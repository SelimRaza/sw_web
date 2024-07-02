<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use App\DataExport\RequestReportData;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\MasterData\Country;

use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\User;
use App\BusinessObject\SalesGroupEmployee;
//use Excel;
class CommonReportRequestController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;

    public function __construct()
    {
        set_time_limit(8000000);
        ini_set('memory_limit', '512M');
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

  
//All report section start
    public function commonReportRequestQueryGenerate(Request $request)
     {
        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $time_period = $request->time_period;
        $acmp_id = $request->acmp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;
        $dirg_id = $request->dirg_id;
        $slgp=DB::connection($this->db)->select("Select acmp_name,slgp_name from user_group_permission where acmp_id=$acmp_id and slgp_id=$sales_group_id limit 1");
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        $slgp_name=$slgp[0]->slgp_name;
        $acmp_name=$slgp[0]->acmp_name;
        $q1 = "";
        $q2 = "";
        $q5 = "";
        $q6 = "";
        $zone_list=[];
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        if ($zone_id != "") {
            $q1 = " AND t1.zone_id = '$zone_id' ";
        }
        if ($sales_group_id != "") {
            $q2 = " AND t1.slgp_id = $sales_group_id ";
        }
        if ($dist_id != "") {
            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {
            $q6 = " AND t1.than_id = '$than_id'";
        }
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $oid_min_max=DB::connection($this->db)->select("Select min(id) as min_id,max(id) as max_id FROM tt_ordm where ordm_date BETWEEN '$start_date' AND '$end_date'");
        $min_id=$oid_min_max[0]->min_id;
        $max_id=$oid_min_max[0]->max_id;
        $report_heading_query='';
        $report_data_query='';
        $raw_query='';
        if ($reportType == "class_wise_order_report_amt"){
            $select_q='';
            $zone_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $min_q='';
            if($dirg_id !=""){
                $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t6.id = $zone_id ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            // $class_list=DB::connection($this->db)->select("SELECT distinct t3.id, t3.itcl_name
            //             FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4
            //             where ".$min_q." t1.ordm_date between  '$start_date' and '$end_date'
            //             and t1.id=t2.ordm_id 
            //             and t1.slgp_id=$sales_group_id and t2.amim_id=t4.id and t4.itcl_id = t3.id and t3.lfcl_id=1");
            $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                            where t1.slgp_id='$sales_group_id' AND t3.lfcl_id=1 AND t2.lfcl_id=1
                            GROUP BY t3.id,t3.itcl_name");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcl_name;
                $select_q.=",sum(if(t3.id=$temp_v_id,t2.ordd_oamt,0))`$temp_v_cl`";
            }
            // $report_data_query="SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
            //             ".$select_q."
            //             FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
            //             tm_aemp as t5 ,tm_zone as t6
            //             where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
            //                         and t1.id=t2.ordm_id 
            //             ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
            //             and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
            //             group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC";
            $report_data_query="SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC";
           // return array('h'=>$report_heading_query,'d'=>$report_data_query);

        }
        else if ($reportType == "cat_wise_order_report_amt"){
            $select_q='';
            $zone_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $min_q='';
            if($dirg_id !=""){
                $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t6.id = $zone_id ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT t4.id,t4.itcg_name FROM `tl_sgit` t1
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
                        INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
                        where t1.slgp_id=$sales_group_id AND t3.lfcl_id=1 AND t2.lfcl_id=1  AND t4.lfcl_id=1
                        GROUP BY t4.id,t4.itcg_name");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcg_name;
                $select_q.=",sum(if(t3.itcg_id=$temp_v_id,t2.ordd_oamt,0))`$temp_v_cl`";
            }
            $report_data_query="SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC";
            //return array('h'=>$report_heading_query,'d'=>$class_wise_ord_amnt);
        }
        else if ($reportType == "cat_wise_order_report_memo"){
            $select_q='';
            $zone_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $min_q='';
            if($dirg_id !=""){
                $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t6.id = $zone_id ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT t4.id,t4.itcg_name FROM `tl_sgit` t1
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
                        INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
                        where t1.slgp_id=$sales_group_id AND t3.lfcl_id=1 AND t2.lfcl_id=1  AND t4.lfcl_id=1
                        GROUP BY t4.id,t4.itcg_name");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcg_name;
                $select_q.=",sum(if(t3.itcg_id=$temp_v_id,1,0))`$temp_v_cl`";
            }
            $report_data_query="SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC";
           // return array('h'=>$report_heading_query,'d'=>$report_data_query);
        }
        else if($reportType == "sr_route_outlet"){
            $report_data_query="SELECT t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name,COUNT(t3.site_id) as t_site
                        FROM tm_aemp t1
                        INNER JOIN tl_rpln t2 on t1.id=t2.aemp_id
                        INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                        INNER JOIN tm_zone t4 ON t1.zone_id=t4.id
                        INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                        WHERE t1.role_id=1 AND t2.rpln_day=DAYNAME('$start_date')
                        ".$q2.$q1."
                        AND t1.lfcl_id=1 AND t2.lfcl_id=1 AND t3.lfcl_id=1
                        GROUP BY t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name";
          //  return array('h'=>$report_heading_query,'d'=>$report_data_query);
        }
        else if($reportType == "group_wise_route_outlet"){
            $report_data_query="select slgp_id, count(rout_id)t_route,sum(SR)t_sr,
                        SUM(CASE WHEN  site<60 THEN 1 ELSE 0 END)`b_60` ,
                        SUM(CASE WHEN site>=60 AND site<=120 THEN 1 ELSE 0 END) `b_60_120`,
                        SUM(CASE WHEN  site>120 THEN 1 ELSE 0 END)`b_120` 
                        FROM(
                        SELECT t1.slgp_id,t2.rout_id , COUNT(t3.site_id)site,count(DISTINCT(t1.id))SR
                        FROM `tm_aemp` t1
                        LEFT JOIN  tl_rpln t2 ON t1.id=t2.aemp_id
                        INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                        WHERE  t2.rpln_day=DAYNAME('$start_date')".$q2.$q1." AND  t1.lfcl_id=1 AND t3.lfcl_id=1
                        GROUP BY t1.slgp_id,t2.rout_id)pp GROUP BY slgp_id";
          //  return array('h'=>$report_heading_query,'d'=>$report_data_query);
        }
        else if ($reportType == "class_wise_order_report_memo"){
            $select_q='';
            $zone_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $min_q='';
            if($dirg_id !=""){
                $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t6.id = $zone_id ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                            where t1.slgp_id='$sales_group_id' AND t3.lfcl_id=1 AND t2.lfcl_id=1
                            GROUP BY t3.id,t3.itcl_name");
            
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcl_name;
                $select_q.=",sum(if(t3.id=$temp_v_id,1,0))`$temp_v_cl`";
            }
            $report_data_query="SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date asc";
          //  return $report_data_query;
           // return array('h'=>$report_heading_query,'d'=>$report_data_query);

            }else if ($reportType == "sr_activity_hourly_order"){
            $q_z="";
            $q2_g="";
            if($dirg_id !=""){
                $q_z=" AND zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $q_z = " AND zone_id = $zone_id";
            }
            if ($sales_group_id != "") {
                $q2_g = " AND slgp_id = ''$sales_group_id''";
            }
            
            $report_data_query="SELECT act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1,9am,10am,11am,12pm,1pm,2pm,3pm,4pm,5pm,6pm,7pm,8pm,9pm
                FROM tbl_sr_activity_summary
                WHERE act_date BETWEEN ''$start_date'' AND ''$end_date''".$q2_g.$q_z."
                ORDER BY act_date asc";
           // return array('h'=>$report_heading_query,'d'=>$report_data_query);

        } else if ($reportType == "sr_activity_hourly_visit"){
                $q_z="";
                $q2_g="";
                if($dirg_id !=""){
                    $q_z=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q_z = " AND zone_id = $zone_id";
                }
                if ($sales_group_id != "") {
                    $q2_g = " AND slgp_id = ''$sales_group_id''";
                }
                $report_data_query="SELECT act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1,SUM(9am+9amn)9am,SUM(10am+10amn)10am,SUM(11am+11amn)11am,SUM(12pm+12pmn)12pm,SUM(1pm+1pmn)1pm,
                    SUM(2pm+2pmn)2pm,SUM(3pm+3pmn)3pm,SUM(4pm+4pmn)4pm,SUM(5pm+5pmn)5pm,SUM(6pm+6pmn)6pm,SUM(7pm+7pmn)7pm,SUM(8pm+8pmn)8pm,SUM(9pm+9pmn)9pm
                    FROM tbl_sr_activity_summary
                    WHERE act_date BETWEEN ''$start_date'' AND ''$end_date''".$q2_g.$q_z."
                    GROUP BY act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1 ORDER BY act_date asc";
              //  return array('h'=>$report_heading_query,'d'=>$report_data_query);
            
        }
        else if($reportType== "attendance_report"){
            $q1='';
            $q2='';
            if($dirg_id !=""){
                $q2=" AND t2.zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $q2 = " AND t2.zone_id = $zone_id";
            }
            if ($sales_group_id != "") {
                $q1= " AND t2.slgp_id = ''$sales_group_id''";
            }
            $report_data_query="SELECT t1.attn_date,t1.slgp_name,t1.dirg_name,t1.zone_name,t1.aemp_usnm,replace(t1.aemp_name,'','',''-'')aemp_name,t1.aemp_mob1,t1.edsg_name,
                                ifnull(t1.start_time,''0000-00-00 00:00:00'') start_time,ifnull(t1.end_time,''0000-00-00 00:00:00'') end_time,ifnull(t2.atyp_name,''Absent'')status FROM
                                (SELECT
                                t1.dhbd_date attn_date,t4.slgp_name,t6.dirg_name,t5.zone_name,t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t7.edsg_name,
                                min(t3.attn_time) start_time,max(t3.attn_time) end_time,
                                max(t3.atten_atyp) type
                                FROM th_dhbd_5 t1
                                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                LEFT JOIN tt_attn t3 ON t2.id=t3.aemp_id AND t3.attn_date=t1.dhbd_date
                                INNER JOIN tm_slgp t4 ON t2.slgp_id=t4.id
                                INNER JOIN tm_zone t5 ON t2.zone_id=t5.id
                                INNER JOIN tm_dirg t6 ON t5.dirg_id=t6.id
                                INNER JOIN tm_edsg t7 ON t2.edsg_id=t7.id
                                WHERE t1.dhbd_date between ''$start_date'' AND ''$end_date''
                                AND t2.lfcl_id=1 ".$q1.$q2."
                                GROUP BY t1.dhbd_date,t4.slgp_name,t6.dirg_name,t5.zone_name,t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1
                                ORDER BY t2.aemp_name,t1.dhbd_date ASC)t1
                                LEFT JOIN tm_atyp t2 ON t1.type=t2.id";
          
        }
        else if($reportType == "note_report"){
            $q1 = "";
            $q2 = "";
            $q3 = "";
            if($dirg_id !=""){
                $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $q1 = " AND t4.zone_id = ''$zone_id''";
            }
            if ($sales_group_id != "") {
                $q2 = "AND t3.slgp_id = ''$sales_group_id''";
            }
            $report_data_query ="SELECT
                    t1.note_date,IF(t7.lfcl_id=2,concat(t7.ntpe_name,''/'',''InActv''),t7.ntpe_name) as note_type, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body,
                    t5.edsg_name,concat(t6.site_code, '' - '', t6.site_name)   AS site_name,
                    replace(t1.geo_addr, '','' , ''-'' ) as geo_addr, TIME(t1.note_dtim) as n_time
                    FROM `tt_note` t1 
                    INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                    INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                    INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                    INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
                    LEFT JOIN tm_site t6 on t1.site_code=t6.site_code
                    LEFT JOIN tm_ntpe t7 on t1.ntpe_id=t7.id
                    WHERE t1.note_date >= ''$start_date'' AND t1.note_date <= ''$end_date'' AND t3.aemp_id = ''$empId'' AND
                        t4.aemp_id = ''$empId'' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = ''$acmp_id''
                    ORDER BY t1.note_date, t3.slgp_code, t4.zone_code, t1.aemp_id desc";
        }else if($reportType=="sr_wise_order_details"){
            $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$this->aemp_id' and dirg_id='$dirg_id'");
            for($i=0;$i<count($dirg_zone_list);$i++){
                $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
            }
            $zone_q ="";
            if($zone_id !=""){
                $zone_q= " AND t5.zone_id =$zone_id ";
            }
            if($dirg_id !="" and $zone_id == ""){
                $zone_q=" AND t5.zone_id IN (".implode(',',$zone_list).")";
            }
            $report_data_query="SELECT t2.ordm_date, t5.aemp_name, t5.aemp_usnm, t4.site_code, t4.site_name,
                                t3.amim_code item_code,t3.amim_name item_name, t1.ordd_qnty order_quantity, t1.ordd_oamt order_amount FROM `tt_ordd` t1 INNER JOIN tt_ordm t2 ON t1.ordm_id=t2.id
                                INNER JOIN tm_amim t3 ON t1.amim_id = t3.id
                                INNER JOIN tm_site t4 ON t2.site_id=t4.id
                                INNER JOIN tm_aemp t5 on t2.aemp_id=t5.id WHERE t2.ordm_date BETWEEN  ''$start_date'' and ''$end_date'' AND t2.slgp_id=''$sales_group_id'' ". $zone_q;
        }
        else if($reportType=="asset_order"){
            $astm_id=$request->astm_id;
            $zone_q='';
            if($dirg_id !=""){
                $zone_q=" AND t4.id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t4.id = $zone_id ";
            }
            $amim_list=DB::connection($this->db)->select("SELECT 
                        t2.id,t2.amim_name
                        FROM tl_astd t1
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        WHERE t1.astm_id=$astm_id AND t1.slgp_id=$sales_group_id GROUP BY t2.id,t2.amim_name ORDER BY t2.amim_name ASC");
            $amim_query='';
            for($i=0;$i<count($amim_list);$i++){
                $amim_name=$amim_list[$i]->amim_name;
                $id=$amim_list[$i]->id;
                $amim_query.=",SUM(CASE WHEN t9.amim_id=$id THEN t9.ordd_oamt ELSE 0 END)`$amim_name`";
            }
            // return $amim_query;
            $report_data_query="SELECT
                                t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,replace(concat(t7.site_code,''-'',t7.site_name),'','',''-'')site_name,sum(t9.ordd_oamt)ast_itm_ordr,sum(t1.ordm_amnt) site_order_amount  ".$amim_query."
                                FROM tt_ordm t1
                                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                                INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                                INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
                                INNER JOIN tm_astm t6 ON t5.astm_id=t6.id
                                INNER JOIN tm_site t7 ON t1.site_id=t7.id
                                INNER JOIN tl_astd t8 ON t6.id=t8.astm_id
                                INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
                                WHERE t1.ordm_date between ''$start_date'' AND ''$end_date'' AND t6.id=$astm_id AND t1.slgp_id=$sales_group_id ".$zone_q."
                                GROUP BY  t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,t7.site_code,t7.site_name
                                ORDER BY t2.aemp_name,t4.zone_code";
        }
        else if($reportType=="asset_summary"){
            $astm_id=$request->astm_id;
            $zone_q='';
            if($dirg_id !=""){
                $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t1.zone_id = ''$zone_id'' ";
            }
            // $report_data_query="SELECT 
            //                     t1.slgp_name Sales_Group,t1.zone_name Zone_Name,count(DISTINCT t1.site_id) Outlet,count(distinct t1.ordm_ornm) No_Of_Order,count(t1.amim_id)Line_Qty,round(sum(t1.ordd_oamt),2)Amount
            //                     FROM `order_summary` t1
            //                     INNER JOIN tl_assm t2 ON t1.site_id=t2.site_id AND t1.slgp_id=t2.slgp_id
            //                     INNER JOIN tm_astm t3 ON t2.astm_id=t3.id
            //                     INNER JOIN tl_astd t4 ON t3.id=t4.astm_id AND t1.amim_id=t4.amim_id
            //                     WHERE t1.ordm_date between ''$start_date'' and ''$end_date'' AND t1.slgp_id=''$sales_group_id'' AND t3.id=''$astm_id''
            //                     GROUP BY t1.slgp_name,t1.zone_code,t1.zone_name 
            //                     ORDER BY t1.zone_code,t1.slgp_name";
            $report_data_query="SELECT t1.slgp_name Group_Name,t1.zone_name,t1.ast_olt Asset_Olt,count(distinct t2.site_id)C_Olt,count(t2.ordm_ornm)Memo,
                                ifnull(round(sum(site_ordr),2),0.00)Olt_Exp,ifnull(round(sum(ast_itm_ordr),2),0.00)Olt_Asset_Exp
                                FROM 
                                (SELECT 
                                t3.id slgp_id,t3.slgp_name,t2.id zone_id,t2.zone_name,count(distinct t1.site_id)ast_olt FROM 
                                tl_assm t1
                                INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                                INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                                WHERE t1.slgp_id=''$sales_group_id'' AND t1.astm_id=''$astm_id'' ".$zone_q."
                                GROUP BY t2.zone_name ORDER BY t2.zone_code asc)t1
                                LEFT JOIN 
                                (SELECT t1.ordm_ornm,t1.site_id,t1.site_ordr,t1.ast_itm_ordr,t1.zone_id,t1.astm_id FROM  tbl_asset_summary t1 WHERE t1.slgp_id=''$sales_group_id'' AND t1.astm_id=''$astm_id'' 
                                AND ordm_date between ''$start_date'' AND ''$end_date'' ".$zone_q.") t2 ON t2.zone_id=t1.zone_id
                                GROUP BY t1.zone_id,t1.zone_name,t1.ast_olt";
        }
        else if($reportType=="asset_details"){
            $astm_id=$request->astm_id;
            $zone_q='';
            if($dirg_id !=""){
                $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $zone_q= " AND t1.zone_id = ''$zone_id'' ";
            }
            $report_data_query="SELECT
            t1.slgp_name Group_Name,t1.zone_name Zone_Name,t2.aemp_usnm SR_ID,t2.aemp_name SR_Name,replace(concat(t5.site_code,''-'',t5.site_name),'','',''-'')Site_Name,concat(t6.amim_code,''-'',t6.amim_name)Item_Name,round(round(sum(t3.ordd_oamt),2),2)Olt_Asset_Exp
            FROM 
            tbl_asset_summary t1
            INNER JOIN tm_aemp t2 ON t1.aemp_usnm=t2.aemp_usnm
            INNER JOIN tt_ordd t3 ON t1.ordm_ornm=t3.ordm_ornm
            INNER JOIN tm_site t5 ON t1.site_id=t5.id
            INNER JOIN tm_amim t6 ON t3.amim_id=t6.id
            WHERE t1.slgp_id=$sales_group_id AND t1.astm_id=$astm_id AND t1.ordm_date between ''$start_date'' AND ''$end_date'' ".$zone_q."
            GROUP  BY t1.slgp_name,t1.zone_name,t2.aemp_usnm,t2.aemp_name,t5.site_code,t5.site_name,t6.amim_code,t6.amim_name Order By t1.zone_name,t2.aemp_name ASC";
        }
        
        else {

            $temp_s_list = "";
            $q1='';
            $q2='';
            if ($reportType == "sr_activity") {
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = "AND t4.zone_id =$zone_id";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t2.slgp_id = $sales_group_id ";
                }
                $temp_s_list="SELECT 
                t1.dhbd_date AS date,
                t4.zone_name AS zone_name,
                t3.slgp_name AS slgp_name,
                SUM(t1.dhbd_ucnt) AS total_sr,
                sum(t1.dhbd_prnt) AS present_sr,
                SUM( t1.`dhbd_lvsr`) AS leave_sr,
                SUM(t1.dhbd_pact) AS   productive_sr,
                SUM(t1.dhbd_tsit) AS total_outlet,
                SUM(t1.dhbd_tvit) AS  total_visit,
                SUM(t1.dhbd_memo) AS total_successful_calls,
                round(ifnull(SUM(t1.dhbd_line) / SUM(t1.dhbd_memo),0), 2) AS  lpc,
                SUM(t1.dhbd_tamt) AS t_amnt
                FROM th_dhbd_5 t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                WHERE  t1.dhbd_date BETWEEN ''$start_date'' AND ''$end_date''  AND t3.aemp_id=''$empId''  AND t4.aemp_id=''$empId'' AND t1.role_id=1 ". $q1. $q2 ."
                GROUP BY t2.zone_id,t4.zone_name,t1.dhbd_date,t3.slgp_name order by t4.zone_name asc";
            } else if ($reportType == "sr_productivity") {

                // $q1 = "";
                // $q2 = ""; 
                // if($dirg_id !=""){
                //     $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                // }          
                // if ($zone_id != "") {
                //     $q1 = " AND t1.zone_id = ''$zone_id'' ";
                // }
                // if ($sales_group_id != "") {
                //     $q2 = " AND t1.slgp_id = ''$sales_group_id'' ";
                // }
                // $temp_s_list = "SELECT
                //           t1.date,
                //           t1.zone_id,
                //           t2.slgp_name   AS slgp_name,
                //           t3.zone_name   AS zone_name,
                //           t1.slgp_id                                  AS slgp_id,
                //           t1.aemp_code                                AS aemp_id,
                //           t1.aemp_name                                AS aemp_name,
                //           t1.aemp_mobile                              AS aemp_mobile,
                //           replace(ifnull(t1.rout_name,''N/A''),'','',''-'')                AS rout_name,
                //           ifnull(t1.t_outlet,0)                       AS t_outlet,
                //           (ifnull(t1.v_outlet,0) + t1.s_outet)        AS c_outlet,
                //           ifnull(t1.s_outet,0)                        AS s_outet,
                //           round((ifnull(t1.v_outlet,0)+ifnull(t1.s_outet,0))*100/t1.t_outlet,2) AS c_percentage,
                //           round(ifnull(t1.s_outet*100/(t1.s_outet+t1.v_outlet),0),2) AS strikeRate,
                //           round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                //           round(SUM(t1.t_amnt), 2)                    AS t_amnt,
                //           t1.inTime                                   AS inTime,
                //           t1.firstOrTime                              AS firstOrTime,
                //           t1.lastOrTime                               AS lastOrTime,
                //           TIMEDIFF(t1.lastOrTime, t1.firstOrTime)     AS workTime
                //         FROM `tt_aemp_summary` t1
                //           INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                //           INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                //         WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t1.s_outet!=''0'' AND t2.aemp_id = ''$empId'' AND
                //               t3.aemp_id = ''$empId'' " . $q1 . $q2. " and t2.acmp_id=''$acmp_id''
                //         GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                //         ORDER BY t2.slgp_code, t3.zone_code";
                $rtype=$request->rtype;
                $utype=$request->utype;
                $q1 = "";
                $q2 = ""; 
                $temp_s_list="";
                if($rtype==1 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = $zone_id ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = $sales_group_id ";
                    }
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $temp_s_list="SELECT
                            t1.date,
                            t1.zone_id,
                            t2.slgp_name   AS slgp_name,
                            t3.zone_name   AS zone_name,
                            t1.slgp_id                                  AS slgp_id,
                            t1.aemp_code                                AS aemp_id,
                            t1.aemp_name                                AS aemp_name,
                            t1.aemp_mobile                              AS aemp_mobile,
                            concat(t7.aemp_usnm,''-'',t7.aemp_name)       AS aemp_mngr,
                            t5.base_name,
                            replace(ifnull(t1.rout_name,''N/A''),'','',''-'')                AS rout_name,
                            ifnull(t1.t_outlet,0)                       AS total_outlet,
                            (ifnull(t1.v_outlet,0) + ifnull(t1.s_outet,0))        AS visited_outlet,
                            ifnull(t1.s_outet,0)                        AS sucessful_outet,
                            round((ifnull(t1.v_outlet,0)+ifnull(t1.s_outet,0))*100/t1.t_outlet,2) AS visit_percentage,
                            round(ifnull(t1.s_outet*100/(t1.s_outet+t1.v_outlet),0),2) AS strikeRate,
                            round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                            round(SUM(t1.t_amnt), 2)                    AS total_amnt,
                            t1.inTime                                   AS inTime,
                            t1.firstOrTime                              AS firstOrTime,
                            t1.lastOrTime                               AS lastOrTime,
                            TIMEDIFF(t1.lastOrTime, t1.firstOrTime)     AS workTime
                            FROM `tt_aemp_summary` t1
                            INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                            INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                            INNER JOIN tm_rout t4 ON t1.rout_id=t4.id
                            INNER JOIN tm_base t5 ON t4.base_id=t5.id
                            INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                            INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                            WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND (t1.v_outlet>0 OR t1.s_outet>0) AND t2.aemp_id = $empId AND
                                t3.aemp_id =$empId " . $q1 . $q2. " and t2.acmp_id=$acmp_id
                            GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id,t1.aemp_code,t1.aemp_name,t1.aemp_mobile
                            ORDER BY t3.zone_code,t1.date,t2.slgp_code ASC";
                 
                }
                else if($rtype==2 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = $zone_id ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = $sales_group_id ";
                    }
                    
                    $temp_s_list="SELECT
                            t1.zone_id,t3.zone_name,
                            t2.slgp_name   										AS slgp_name,
                            t3.zone_name   										AS zone_name,
                            t1.slgp_id                                  		AS slgp_id,
                            t1.aemp_code                                		AS aemp_id,
                            t1.aemp_name                                		AS aemp_name,
                            t1.aemp_mobile                              		AS aemp_mobile,
                            concat(t7.aemp_usnm,'-',t7.aemp_name)       		AS aemp_mngr,
                            sum(ifnull(t1.t_outlet,0))                       	AS total_outlet,
                            sum(ifnull(t1.v_outlet,0)) + sum(t1.s_outet)     	AS visited_outlet,
                            sum(ifnull(t1.s_outet,0))                        	AS successful_outet,
                            round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) 		AS lpc,
                            round(SUM(t1.t_amnt), 2)                    		AS total_amnt
                            FROM `tt_aemp_summary` t1
                            INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                            INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                            INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                            INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                            WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date''  AND (t1.v_outlet>0 OR t1.s_outet>0) AND t2.aemp_id = $empId AND
                                t3.aemp_id =$empId  and t2.acmp_id=$acmp_id " .$q1.$q2.  "
                            GROUP BY  t1.slgp_id,  t1.aemp_id,t1.aemp_code,t1.aemp_name,t1.aemp_mobile
                            ORDER BY t3.zone_code,t1.date,t2.slgp_code ASC;";

                }
                else if($rtype==1 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id = $zone_id ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = $sales_group_id ";
                    }
                    $temp_s_list="SELECT  
                            t1.ordm_date,t2.aemp_usnm,t2.aemp_name,count(distinct t1.site_id)total_outlet,
                            round(sum(t1.ordm_amnt),2) order_amnt,count(t3.amim_id) item_count,
                            t4.zone_name
                            FROM tt_ordm t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                            INNER JOIN tt_ordd t3 ON t1.id=t3.ordm_id
                            INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                            WHERE t2.role_id>1
                            AND ordm_date between  ''$start_date'' AND ''$end_date'' " .$q1.$q2."
                            GROUP BY t1.ordm_date,t2.aemp_usnm,t2.aemp_name
                            ORDER BY t1.ordm_date,t4.zone_code,t2.aemp_usnm,t2.aemp_name;";
                }
                else if($rtype==2 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id = $zone_id ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = $sales_group_id ";
                    }
                    $temp_s_list="SELECT 
                            zone_name,aemp_usnm,aemp_name,sum(ord_site)total_outlet,sum(ordm_amnt)order_amnt,sum(item_count)item_count
                            FROM(
                            SELECT  
                            t1.ordm_date,t2.aemp_usnm,t2.aemp_name,count(distinct t1.site_id)ord_site,
                            round(sum(t1.ordm_amnt),2) ordm_amnt,count(t3.amim_id) item_count,
                            t4.zone_name
                            FROM tt_ordm t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                            INNER JOIN tt_ordd t3 ON t1.id=t3.ordm_id
                            INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                            WHERE t2.role_id>1
                            AND ordm_date between  ''$start_date'' AND ''$end_date'' " .$q1.$q2."
                            GROUP BY t1.ordm_date,t2.aemp_usnm,t2.aemp_name
                            ORDER BY t1.ordm_date,t4.zone_code,t2.aemp_usnm,t2.aemp_name)p
                            GROUP BY zone_name,aemp_usnm,aemp_name                            
                            ";
                }
               
            }
            else if($reportType=="sr_productivity_summary"){
                $q1 = "";
                $q2 = ""; 
                if($dirg_id !=""){
                    $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t1.zone_id = ''$zone_id'' ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = ''$sales_group_id'' ";
                }
                $temp_s_list = "SELECT
                          t3.zone_name   AS zone_name,
                          t5.base_name,
                          concat(t7.aemp_usnm,''-'',t7.aemp_name)     AS so_name,
                          t1.aemp_code                                AS sr_id,
                          t1.aemp_name                                AS sr_name,
                          t1.aemp_mobile                              AS sr_mobile,
                          
                          
                          
                          sum(ifnull(t1.t_outlet,0))                  AS t_olt,
                          sum((ifnull(t1.v_outlet,0) + t1.s_outet))   AS visit,
                          round((ifnull(t1.v_outlet,0)+ifnull(t1.s_outet,0))*100/t1.t_outlet,2) AS visit_percentage,
                          sum(ifnull(t1.s_outet,0))                   AS s_outet,
                          round(ifnull(sum(ifnull(t1.s_outet,0))*100/ sum((ifnull(t1.v_outlet,0) + t1.s_outet)) ,0),2) AS strike_rate,
                          sum(t1.t_sku)/sum(ifnull(t1.s_outet,0))     AS lpc,
                          round(SUM(t1.t_amnt), 2)                    AS order_amount,
                          round(SUM(t1.t_amnt)/sum(ifnull(t1.s_outet,0)),2) avg_olt_order
                          
                        FROM `tt_aemp_summary` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                          INNER JOIN tm_rout t4 ON t1.rout_id=t4.id
                          INNER JOIN tm_base t5 ON t4.base_id=t5.id
                          INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                          INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                        WHERE t1.date between ''$start_date'' AND  ''$end_date'' AND t1.s_outet!=''0'' AND t2.aemp_id = ''$empId'' AND
                              t3.aemp_id = ''$empId'' " . $q1 . $q2. " and t2.acmp_id=''$acmp_id''
                        GROUP BY t1.zone_id, t1.slgp_id, t1.aemp_id
                        ORDER BY t3.zone_code,t2.slgp_code ASC";
            }
             else if ($reportType == "sr_non_productivity") {

                $non_pr1 = "";
                $non_pr2 = "";
                if ($sales_group_id!=""){
                    $non_pr1 = " AND t1.slgp_id = ''$sales_group_id'' ";
                }
                if($dirg_id !=""){
                    $non_pr2=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $non_pr2 = " and t1.zone_id=''$zone_id'' ";
                }
                $temp_s_list = "SELECT t1.date, t1.zone_id, concat(t2.slgp_code, '' - '', t2.slgp_name) AS slgp_name, concat(t3.zone_code, '' - '', t3.zone_name) AS zone_name,
                concat(t3.dirg_code, '' - '', t3.dirg_name) AS dirg_name, t1.slgp_id AS slgp_id, t1.aemp_code AS aemp_id, t1.aemp_name AS aemp_name, t1.aemp_mobile AS aemp_mobile,
                t1.inTime AS inTime FROM `tt_aemp_summary` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id 
                INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
                WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id = ''$empId'' AND (t1.t_amnt=''0'' OR t1.t_amnt IS NULL) 
                AND t3.aemp_id = ''$empId'' AND t1.atten_atyp=''1'' " . $non_pr1 . $non_pr2 . " AND t2.acmp_id = ''$acmp_id'' GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id 
                ORDER BY t3.zone_code, t2.slgp_code";

            } else if ($reportType == "sr_summary_by_group") {
                $q='';
                if($dirg_id !=""){
                    $q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $q = " AND t3.zone_id=''$zone_id'' ";
                }
                $temp_s_list = "SELECT
                  t1.date,
                  t1.slgp_name,
                  t4.t_sr,
                  t2.p_sr,
                  t3.l_sr,
                  t1.pro_sr,
                  t1.t_outlet,
                  t1.c_outlet,
                  t1.s_outet,
                  t1.lpc,
                  t1.t_amnt
                FROM (SELECT
                        t1.date,
                      
                        concat(t2.slgp_code, '' - '', t2.slgp_name)   AS slgp_name,
                      
                        t1.slgp_id,
                        count(DISTINCT t1.aemp_id) as pro_sr,
                        SUM(t1.t_outlet)                            AS t_outlet,
                        (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
                        SUM(t1.s_outet)                             AS s_outet,
                        round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                        round(SUM(t1.t_amnt), 2)                    AS t_amnt
                      FROM `tt_aemp_summary` t1
                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                      WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t1.s_outet > ''0'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' AND
                            t2.slgp_id LIKE ''%$sales_group_id%'' ". $q." AND t2.acmp_id=''$acmp_id''
                      GROUP BY t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                  (SELECT
                     t1.date,
                    
                     t1.slgp_id,
                     count(DISTINCT t1.aemp_id) AS p_sr
                   FROM tt_aemp_summary t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' AND
                         t2.slgp_id LIKE ''%$sales_group_id%'' ". $q." AND t1.atten_atyp = ''1'' AND t2.acmp_id=''$acmp_id''
                   GROUP BY t1.slgp_id, t1.date) t2 ON t1.slgp_id = t2.slgp_id AND t1.date = t2.date
                  LEFT JOIN
                
                  (SELECT
                     t1.date,
                     t1.zone_id,
                     t1.slgp_id,
                     count(DISTINCT t1.aemp_id) AS l_sr
                   FROM tt_aemp_summary t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' AND
                         t2.slgp_id LIKE ''%$sales_group_id%'' ". $q." AND t1.atten_atyp != ''1'' AND t2.acmp_id=''$acmp_id''
                   GROUP BY t1.slgp_id, t1.date) t3 ON t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                  INNER JOIN
                
                
                  (SELECT
                     t1.zone_id,
                     t1.slgp_id,
                     count(DISTINCT t1.id) AS t_sr
                   FROM tm_aemp t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' AND
                         t2.slgp_id LIKE ''%$sales_group_id%'' ". $q."
                          AND t1.lfcl_id = ''1'' AND t1.role_id = ''1'' AND t2.acmp_id=''$acmp_id''
                   GROUP BY  t1.slgp_id) t4 ON t1.slgp_id = t4.slgp_id";

            }else if ($reportType == "market_outlet_sr_outlet") {
                    $temp_s_list = "SELECT dsct_name, than_name, ward_name, concat(mktm_id,'' - '',Replace(mktm_name,'','',''-'')) as mktm_name, mktm_id, 
                        sum(site) AS m_outlet_quantity, sum(de) AS sr_outlet_quantity
                        FROM (SELECT t2.id AS mktm_id, t5.dsct_name, t4.than_name, t3.ward_name, t2.mktm_name, t1.site_code, t6.id, if(isnull(t1.site_code),0,1) AS site,
                         if(isnull(t6.id),0,1) AS de
                          FROM `tm_site` t1
                          INNER JOIN tm_mktm t2 ON t1.mktm_id=t2.id
                          INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
                          INNER JOIN tm_than t4 ON t3.than_id=t4.id
                          INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
                          INNER JOIN tl_srth t7 ON t7.than_id= t4.id
                          INNER JOIN tm_aemp t8 ON t8.id = t7.aemp_id
                          INNER JOIN user_area_permission t9 ON t8.zone_id= t9.zone_id
                          INNER JOIN user_group_permission t10 ON t8.slgp_id= t10.slgp_id
                          LEFT JOIN tl_rsmp t6 ON t1.id= t6.id
                        WHERE t8.slgp_id=''$sales_group_id'' and t9.aemp_id=''$empId'' and t10.aemp_id=''$empId'' AND t4.id=''$than_id'' GROUP BY t1.site_code) AS ddd GROUP BY mktm_id";
            } else if ($reportType == "sr_wise_order_delivery") {
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id=''$zone_id''";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = ''$sales_group_id'' ";
                } 
                $temp_s_list="SELECT ''$acmp_name'' acmp_name,''$slgp_name'' slgp_name,
                t5.`aemp_name`,
                t5.`aemp_usnm`,
                t5.`aemp_mob1`,
                t1.ordm_date,
                t6.zone_name,
                ROUND(SUM(t2.ordd_oamt),2) AS ordd_amt,
                ROUND(SUM(t2.ordd_odat),2) AS deli_amt
                FROM `tt_ordm` as t1,`tt_ordd`as t2,
                tm_aemp as t5 ,tm_zone as t6
                where t1.id between $min_id and $max_id
                and (t1.ordm_date between ''$start_date'' AND ''$end_date'')
                            and t1.id=t2.ordm_id 
                ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                group by t5.`aemp_name`,
                    t5.`aemp_usnm`,
                    t5.`aemp_mob1`,
                    t1.ordm_date,
                    t6.zone_name";
            }
            else if($reportType=='zone_wise_order_delivery_summary'){
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t1.zone_id=''$zone_id''";
                }
                $temp_s_list="SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`order_amount`) as ordd_oamt,
                sum(t1.`Delivery_Amount`) as ordd_Amnt FROM `s_mktm_zone_group_wise_order_vs_delivery` t1
               WHERE t1.ordm_date  BETWEEN ''$start_date'' AND ''$end_date''". $q1. $zone_query . "AND t1.slgp_id=$sales_group_id
                     group by t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`";
            }
            else if ($reportType == "sku_wise_order_delivery") {  
            $zone_query='';
            if($dirg_id !=""){
                $zone_query=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if($zone_id !=''){
                $zone_query="AND t6.id=''$zone_id''";
            }
            if ($sales_group_id != "") {
                $q2 = " AND t1.slgp_id = ''$sales_group_id'' ";
            }  
            $temp_s_list="SELECT ''$acmp_name'' acmp_name,''$slgp_name'' slgp_name,
            t5.`aemp_name`,
            t5.`aemp_usnm`,
            t5.`aemp_mob1`,
            t4.`amim_code`,
            t4.`amim_name`,
            t1.ordm_date,
            t6.zone_name,
            ROUND(SUM(t2.ordd_oamt),2) AS ordd_amt,
            ROUND(SUM(t2.ordd_odat),2) AS deli_amt
            FROM `tt_ordm` as t1,`tt_ordd`as t2,tm_amim as t4,
            tm_aemp as t5 ,tm_zone as t6
            where t1.id between $min_id and $max_id
            and (t1.ordm_date between ''$start_date'' AND ''$end_date'')
                        and t1.id=t2.ordm_id 
            ".$q2.$zone_query." and t2.amim_id=t4.id and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
            group by t5.`aemp_name`,
              t5.`aemp_usnm`,
              t5.`aemp_mob1`,
              t4.`amim_code`,
              t4.`amim_name`,
              t1.ordm_date,
              t6.zone_name";
            }
            $report_data_query=$temp_s_list;

        }
        $emp_usnm = $this->currentUser->employee()->aemp_usnm;
        $emp_name = $this->currentUser->employee()->aemp_name;
        $aemp_emal = $this->currentUser->employee()->aemp_emal;
        $cont_conn = Auth::user()->countryDB()->db_name;
        DB::connection($this->db)->select("INSERT INTO tbl_report_request( report_name,report_heading_query,report_data_query,cont_conn,aemp_id,aemp_usnm, aemp_name,aemp_email,report_status)
         VALUES('$reportType','0','$report_data_query','$cont_conn','$empId','$emp_usnm','$emp_name','$aemp_emal','1')");
        // $q=DB::connection($this->db)->select("Select report_data_query from tbl_report_request limit 1");
        //  $q=$q?$q[0]->report_data_query:'';
        //  $stmt= base64_decode($q);
         //$srData = DB::connection($this->db)->select(DB::raw($stmt));
         return 1;

    }
    public function sendMail(){
      // $data_source=public_path('/mail_data/');
     //  return $data_source;
    //     $email_array=array('mis42@mis.prangroup.com');
    //     $q=DB::connection($this->db)->select("Select * from tbl_report_request where report_status=5");
    //     $qt=$q[0]->report_data_query;
    //     $data_query=base64_decode($qt);
    //     $data = array('name'=>"Hussain Mahamud",'report_name'=>$q[0]->report_name,'aemp_name'=>$q[0]->aemp_name,'subject'=>'SPRO Requested Report Delivery Notification');
    //     $result=DB::connection($this->db)->select(DB::raw($data_query));

    //     if(count($result)>0){
    //         $array=array();
    //         for($i=0;$i<count($result);$i++){
    //             $array[$i]=(array)$result[$i];
    //         }
        
    //     Excel::store('aemp_data', function($excel) use ($array){
            
    //         $excel->setTitle('Requested Report');
    //         $excel->setCreator('Hussain Mahamud')->setCompany('PRAN-RFL');
    //         $excel->setDescription('Custom Report Request');
    //         $excel->sheet('Sheet 1', function ($sheet) use ($array) {
    //             $sheet->setOrientation('landscape');
    //             $sheet->setAutoFilter('A1:W1');
    //             $sheet->fromArray($array);
    //         });

    //     })->store('xlsx', $data_source);

    //     Mail::send('mail', $data, function($message) {
    //        $message->to('hussainmahamud.swe@gmail.com');
    //        $message->subject(data['subject']);
    //        $message->from('mis42@mis.prangroup.com');
    //        $message->attach('/home1/sprobo/core/public/mail_data/aemp_data.xlsx');
    //     });
    //     return "Mail Delivered Successfully";

    //     }
    $heading=[];
    $pending_list=DB::connection($this->db)->select("SELECT * FROM tbl_report_request where report_status=1");
    $file_name='';
    $file_path='';
    if($pending_list){
        for($i=0;$i<count($pending_list);$i++){
            $file_name=$pending_list[$i]->report_name.date("Y_m_d__ H:i:s"). '.xlsx';
            $file_path="/home1/sprobo/core/storage/app/".$file_name;

            $query=base64_decode($pending_list[$i]->report_data_query);
            if($pending_list[$i]->report_name=='note_report'){
                $heading=['Date','Staff id','Staff Name','Designation','Group','Zone','Note/Task Details','Note Type','Outlet','Time','Area'];
            }
          Excel::store(new RequestReportData($query,$pending_list[$i]->cont_conn,$heading), $file_name);
            $data = array('name'=>"Hussain Mahamud",'report_name'=>'---',
            'aemp_name'=>'N/A','subject'=>'SPRO Requested Report Delivery Notification(Test)',
            'attach'=>$file_path
        );
            Mail::send('mail', $data, function($message) use ($data) {
                $message->to('mis42@mis.prangroup.com');              
                $message->subject('SPRO Requested Report Delivery Notification Test');
                $message->cc(['sa18@prangroup.com','hussainmahamud.swe@gmail.com']);
                $message->from('mis42@mis.prangroup.com');
                $message->attach($data['attach']);
            });
            
        }
    }
     $message->cc(['sa18@prangroup.com','hussainmahamud.swe@gmail.com']);
    return "Completed";
    


    
 }
 public function getGeneratedReportList(){
    $emid=Auth::user()->employee()->id;
    $generated_report_list=DB::connection($this->db)->select("SELECT `id`, `report_name`, `report_link`,report_status,aemp_email, `created_at` 
    FROM `tbl_report_request` WHERE aemp_id='$emid'
    AND date(created_at) <=(curdate()) AND  date(created_at)>= (curdate()-INTERVAL 2 DAY)
    Order by created_at desc");
    return $generated_report_list;
}
 public function sendMailWithReportLink(){
    set_time_limit(8000000);
    $recipient_list=DB::connection($this->db)->select("SELECT aemp_id,aemp_usnm,aemp_name,aemp_email FROM tbl_report_request WHERE report_status=2 
                     GROUP BY aemp_id,aemp_usnm,aemp_name,aemp_email");
    for($i=0;$i<count($recipient_list);$i++){
        $id=$recipient_list[$i]->aemp_id;
        $data=DB::connection($this->db)->select("SELECT report_name,report_link,aemp_email,aemp_name,aemp_usnm FROM tbl_report_request where aemp_id=$id AND report_status=2 ORDER BY created_at DESC");
        $recipient_info=array('email'=>$recipient_list[$i]->aemp_email,'aemp_name'=>$recipient_list[$i]->aemp_name);
        Mail::send('mail', ['data'=>$data,'info'=>$recipient_info], function($message) use ($data,$recipient_info) {
            $message->to($recipient_info['email']);              
            $message->subject('SPRO Requested Report');
            $message->cc(['sa18@prangroup.com','mis42@mis.prangroup.com']);
            $message->from('reportbi@prangroup.com');    
        });
        if (Mail::failures()) {
            // return response showing failed emails
            Mail::send('failure_mail', function($message) {
                $message->to('mis42@mis.prangroup.com');              
                $message->subject('SPRO Requested Report');
                $message->cc(['hussainmahamud.swe@gmail.com']);
                $message->from('mis42@mis.prangroup.com');    
            });
        }else{
            DB::connection($this->db)->select("UPDATE tbl_report_request SET report_status=3 WHERE aemp_id=$id");
            return "Mail Sent";
        }

   }
    
 }

  
}