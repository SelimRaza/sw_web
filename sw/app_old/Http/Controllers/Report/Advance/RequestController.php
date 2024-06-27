<?php

namespace App\Http\Controllers\Report\Advance;
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
class RequestController extends Controller
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

    public function commonReportRequestQueryGenerateAdvance(Request $request){   
        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $time_period = $request->time_period;
        $acmp_id = $request->acmp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;
        $dirg_id = $request->dirg_id;
       // $slgp=DB::connection($this->db)->select("Select acmp_name,slgp_name from user_group_permission where acmp_id=$acmp_id and slgp_id IN (".implode(',',$sales_group_id).") limit 1");
        // $slgp_name=$slgp[0]->slgp_name;
        // $acmp_name=$slgp[0]->acmp_name;
       $dirg_zone_list=array();
       if($dirg_id){
           $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id IN (".implode(',',$dirg_id).")");
       }       
        $email_address=$request->email_address;
        $q1 = "";
        $q2 = "";
        $q5 = "";
        $q6 = "";
        $zone_list=[];
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        if ($zone_id != "") {
            $q1 = " AND t1.zone_id IN (".implode(',',$zone_id).")";
        }
        if ($sales_group_id != "") {
            $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
        }
        if ($dist_id != "") {
            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {
            $q6 = " AND t1.than_id = '$than_id'";
        }
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        // $oid_min_max=DB::connection($this->db)->select("Select min(id) as min_id,max(id) as max_id FROM tt_ordm where ordm_date BETWEEN '$start_date' AND '$end_date'");
        // $min_id=$oid_min_max[0]->min_id;
        // $max_id=$oid_min_max[0]->max_id;
        $min_oid=DB::connection($this->db)->select("Select min(id) as min_id FROM tt_ordm where ordm_date = '$start_date'");
        $max_oid=DB::connection($this->db)->select("Select max(id) as max_id FROM tt_ordm where ordm_date = '$end_date'");
        $min_id=$min_oid[0]->min_id;
        $max_id=$max_oid[0]->max_id;
        $report_heading_query='';
        $report_data_query='';
        $raw_query='';
        $cls_cat_switch=$request->rtype.$request->sr_zone;
        switch($reportType){
            case "class_wise_order_report_amt":
                $rtype=$request->rtype;                   
                if($request->ord_flag==1){
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
                        $zone_q= " AND t6.id IN (".implode(',',$zone_id).")";
                    }
                    if($min_id !='' && $max_id !=''){
                        $min_q="t1.id between $min_id and $max_id AND ";
                    }
                    if ($sales_group_id != "") {
                        $q2= " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    } 
                    $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                                    INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                                    INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                                    where  t3.lfcl_id=1 AND t2.lfcl_id=1 ".$q2.   "
                                    GROUP BY t3.id,t3.itcl_name");
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcl_name;
                        $select_q.=",sum(if(t3.id=$temp_v_id,t2.ordd_oamt,0))`$temp_v_cl`";
                    }
                    switch($cls_cat_switch){
                        case 11:
                            $report_data_query="SELECT t1.ordm_date as Date,t5.aemp_usnm Staff_Id,t5.aemp_name as Name,
                                        t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                        ".$select_q."
                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                        tm_aemp as t5 ,tm_zone as t6
                                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                    and t1.id=t2.ordm_id 
                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                        t6.zone_name ORDER BY t1.ordm_date ASC";
                            break;
                        case 12:
                            $report_data_query="SELECT t1.ordm_date as Date,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t1.ordm_date,t6.zone_code,
                                                    t6.zone_name ORDER BY t1.ordm_date,t6.zone_code ASC";
                            break;
                        case 21:
                            $report_data_query="SELECT t5.aemp_usnm Staff_Id,t5.aemp_name as Name,
                                                    t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                                    t6.zone_name ORDER BY t5.aemp_usnm,t5.aemp_name ASC";
                            break;
                        case 22:
                            $report_data_query="SELECT t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t6.zone_code,
                                                    t6.zone_name ORDER BY t6.zone_code ASC";
                            break;
                        default:
                            $report_data_query="";
                            break;
                    }
                }
                else{
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
                        $zone_q= " AND t6.id IN (".implode(',',$zone_id).")";
                    }
                    if($min_id !='' && $max_id !=''){
                        $min_q="t1.id between $min_id and $max_id AND";
                    }
                    if ($sales_group_id != "") {
                        $q2= " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    } 
                    $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                                    INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                                    INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                                    where  t3.lfcl_id=1 AND t2.lfcl_id=1 ".$q2. "
                                    GROUP BY t3.id,t3.itcl_name");
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcl_name;
                        $select_q.=",sum(if(t3.id=$temp_v_id,1,0))`$temp_v_cl`";
                    }
                    switch($cls_cat_switch){
                        case 11:
                            $report_data_query="SELECT t1.ordm_date as Date,t5.aemp_usnm Staff_Id,t5.aemp_name as Name,
                                                    t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                        ".$select_q."
                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                        tm_aemp as t5 ,tm_zone as t6
                                        where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                    and t1.id=t2.ordm_id 
                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                        t6.zone_name ORDER BY t1.ordm_date ASC";
                            break;
                        case 12:
                            $report_data_query="SELECT t1.ordm_date Date,t6.zone_code Zone_code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t1.ordm_date,t6.zone_code,
                                                    t6.zone_name ORDER BY t1.ordm_date,t6.zone_code ASC";
                            break;
                        case 21:
                            $report_data_query="SELECT t5.aemp_usnm Staff_Id,t5.aemp_name AS Name,
                                                    t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                                    t6.zone_name ORDER BY t5.aemp_usnm,t5.aemp_name ASC";
                            break;
                        case 22:
                            $report_data_query="SELECT t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                    ".$select_q."
                                                    FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                    tm_aemp as t5 ,tm_zone as t6
                                                    where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                                and t1.id=t2.ordm_id 
                                                    ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                    and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                    group by t6.zone_code,
                                                    t6.zone_name ORDER BY t6.zone_code ASC";
                            break;
                        default:
                            $report_data_query="";
                            break;
                    }
                }
            break;
            case "cat_wise_order_report_amt":
                $select_q='';
                $zone_q='';
                $condition_q='';
                $class_id=[];
                $temp_v_id='';
                $min_q='';
                $rtype=$request->rtype;
                if($dirg_id !=""){
                    $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t6.id IN (".implode(',',$zone_id).")";
                }
                if($min_id !='' && $max_id !=''){
                    $min_q="t1.id between $min_id and $max_id AND";
                }
                if ($sales_group_id != "") {
                    $q2= " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                } 
                $class_list=DB::connection($this->db)->select("SELECT t4.id,t4.itcg_name FROM `tl_sgit` t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
                            INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
                            where t3.lfcl_id=1 AND t2.lfcl_id=1  AND t4.lfcl_id=1 ".$q2.   "
                            GROUP BY t4.id,t4.itcg_name");
                if($request->ord_flag==1){
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcg_name;
                        $select_q.=",sum(if(t3.itcg_id=$temp_v_id,t2.ordd_oamt,0))`$temp_v_cl`";
                    }
                }else{
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcg_name;
                        $select_q.=",sum(if(t3.itcg_id=$temp_v_id,1,0))`$temp_v_cl`";
                    }
                }
                switch($cls_cat_switch){
                    case 11:
                        $report_data_query="SELECT t1.ordm_date as Date,t5.aemp_usnm Staff_Id,t5.aemp_name Name,
                                                t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ORDER BY t1.ordm_date ASC";
                        break;
                    case 12:
                        $report_data_query="SELECT t1.ordm_date as Date,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t1.ordm_date,t6.zone_code,t6.zone_name
                                                ORDER BY t1.ordm_date,t6.zone_code ASC";
                        break;
                    case 21:
                        $report_data_query="SELECT t5.aemp_usnm Staff_Id,t5.aemp_name as Name,
                                                t5.aemp_mob1 Mobile,t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ORDER BY t5.aemp_usnm,t5.aemp_name ASC";
                        break;
                    case 22:
                        $report_data_query="SELECT t6.zone_code Zone_Code,t6.zone_name Zone_Name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  ''$start_date''  and ''$end_date''
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t6.zone_code,t6.zone_name
                                                ORDER BY t6.zone_code ASC";
                        break;
                    default:
                        $report_data_query="";
                        break;
                }
            break;
            case "sr_activity_hourly_order":
                $zone_filter="";
                $slgp_filter="";
                if($dirg_id !=""){
                    $zone_filter=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_filter = " AND zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id != "") {
                    $slgp_filter = " AND slgp_id IN (".implode(',',$sales_group_id).")";
                }                
                $report_data_query="SELECT act_date as Date,slgp_name ''Group'',concat(t2.zone_code,''-'',t2.zone_name) as Zone,aemp_usnm SR_ID,
                                    replace(aemp_name,'','',''.'') SR_Name,aemp_mob1 SR_Mobile,9am,10am,11am,12pm,1pm,2pm,3pm,4pm,5pm,6pm,7pm,8pm,9pm
                    FROM tbl_sr_activity_summary t1
                    INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                    WHERE act_date BETWEEN ''$start_date'' AND ''$end_date''".$slgp_filter.$zone_filter."
                    ORDER BY act_date asc";
                
            break;
            case "sr_activity_hourly_visit":
                    $zone_filter="";
                    $slgp_filter="";
                    if($dirg_id !=""){
                        $zone_filter=" AND zone_id IN (".implode(',',$zone_list).")";
                    }
                    if ($zone_id != "") {
                        $zone_filter = " AND zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $slgp_filter = " AND slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT act_date as Date,slgp_name as ''Group'',concat(t2.zone_code,''-'',t2.zone_name)as Zone,aemp_usnm SR_ID,replace(aemp_name,'','',''.'') SR_Name,aemp_mob1 Mobile,
                                        SUM(9am+9amn)9am,SUM(10am+10amn)10am,SUM(11am+11amn)11am,SUM(12pm+12pmn)12pm,SUM(1pm+1pmn)1pm,
                                        SUM(2pm+2pmn)2pm,SUM(3pm+3pmn)3pm,SUM(4pm+4pmn)4pm,SUM(5pm+5pmn)5pm,SUM(6pm+6pmn)6pm,SUM(7pm+7pmn)7pm,SUM(8pm+8pmn)8pm,SUM(9pm+9pmn)9pm
                                        FROM tbl_sr_activity_summary t1
                                        INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                                        WHERE act_date BETWEEN ''$start_date'' AND ''$end_date''".$slgp_filter.$zone_filter."
                                        GROUP BY act_date,slgp_name,t2.zone_name,aemp_usnm,aemp_name,aemp_mob1 ORDER BY act_date asc";
            break;
            case "sr_hourly_activity":
                $zone_q='';
                $q2='';
                if($dirg_id !=""){
                    $zone_q=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q = " AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT  `act_date` ACT_DATE, `acmp_name` CMP_NAME, `slgp_name` GROUP_NAME,
                        `zone_code` ZONE_CODE, `zone_name` ZONE_NAME, `aemp_usnm` SR_ID, `aemp_name` SR_NAME, `aemp_mob1` SR_MOBILE,than_olt THANA_OLT, `t_outlet` T_OLT,
                         (`visit_olt` +`success_olt`) T_VISIT,
                         (`9am`+ `10am`+ `11am`+ `12pm`+ `1pm`+ `2pm`+ `3pm`+ `4pm`+ `5pm`+ `6pm`+ `7pm`+ `8pm`+ `9pm`)T_ORDER,
                         success_olt SUCCESS_OLT,
                         ROUND(`t_exp`,2)T_EXP,
                        `t_sku` T_SKU,`9amv` 9AM_VISIT,`9am` 9AM_ORDER,ROUND(`9exp_a`,2)9AM_EXP,
                        `10amv` 10AM_VISIT,`10am` 10AM_ORDER,ROUND(`10exp_a`,2)10AM_EXP,
                        `11amv` 11AM_VISIT,`11am` 11AM_ORDER,ROUND(`11exp_a`,2)11AM_EXP,
                        `12pmv` 12PM_VISIT,`12pm` 12PM_ORDER,ROUND(`12exp_p`,2)12PM_EXP,
                        `1pmv` 1PM_VISIT,`1pm` 1PM_ORDER,ROUND(`1exp_p`,2)1PM_EXP,
                        `2pmv` 2PM_VISIT,`2pm` 2PM_ORDER,ROUND(`2exp_p`,2)2PM_EXP,
                        `3pmv` 3PM_VISIT,`3pm` 3PM_ORDER,ROUND(`3exp_p`,2)3PM_EXP,
                        `4pmv` 4PM_VISIT,`4pm` 4PM_ORDER,ROUND(`4exp_p`,2)4PM_EXP,
                        `5pmv` 5PM_VISIT,`5pm` 5PM_ORDER,ROUND(`5exp_p`,2)5PM_EXP,
                        `6pmv` 6PM_VISIT,`6pm` 6PM_ORDER,ROUND(`6exp_p`,2)6PM_EXP,
                        `7pmv` 7PM_VISIT,`7pm` 7PM_ORDER,ROUND(`7exp_p`,2)7PM_EXP,
                        (`8pmv`+ `9pmv`) 8PM_VISIT,(`8pm`+ `9pm`) 8PM_ORDER,ROUND((`8exp_p`+`9exp_p`),2)8PM_EXP
                        FROM `tbl_sr_hourly_summary` 
                        WHERE  act_date between ''$start_date'' AND ''$end_date''
                        " .$zone_q.$q2. "
                          ";
                break;
            case "sr_routed_outlet":
                $report_data_query="Select 
                t9.slgp_name GROUP_NAME,t3.aemp_usnm STAFF_ID,t3.aemp_name EMP_NAME,t8.aemp_usnm MANAGER_ID,t8.aemp_name MANAGER_NAME, t7.zone_code ZONE_CODE,t7.zone_name ZONE_NAME,
                t2.rout_code ROUT_CODE,t2.rout_name ROUT_NAME,
                t6.site_code OUTLET_CODE,t6.site_name OUTLET_NAME,t6.site_mob1 OUTLET_MOBILE, t1.rpln_day RPLN_DAY
                FROM tl_rpln t1
                INNER JOIN tm_rout t2 ON t1.rout_id=t2.id
                INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                INNER JOIN tm_aemp t8 ON t3.aemp_mngr=t8.id
                INNER JOIN tl_rsmp t5 ON t1.rout_id=t5.rout_id
                INNER JOIN tm_site t6 ON t5.site_id=t6.id
                INNER JOIN tm_zone t7 ON t3.zone_id=t7.id
                INNER JOIN tm_slgp t9 ON t3.slgp_id=t9.id";
                break;
            case "sr_wise_order_details":
                $q2='';
                $zone_q ='';
                if($zone_id !=""){
                    $zone_q= " AND t5.zone_id IN (".implode(',',$zone_id).")";
                }
                if($dirg_id !="" and $zone_id == ""){
                    $zone_q=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($min_id !=0 AND $max_id !=0){
                    $zone_q .=" AND t2.id BETWEEN ".$min_id."  AND ".$max_id;
                }
                if ($sales_group_id!=""){
                    $q2 = " AND t2.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                // $report_data_query="SELECT t2.ordm_date Date, t5.aemp_usnm SR_ID,t5.aemp_name SR_Name, t4.site_code Outlet_Code, t4.site_name Outlet_Name,
                //                     t3.amim_code Item_Code,t3.amim_name Item_Name, t1.ordd_qnty Quantity, t1.ordd_oamt Amount,t2.ordm_time Order_Time
                //                     FROM `tt_ordd` t1 
                //                     INNER JOIN tt_ordm t2 ON t1.ordm_id=t2.id
                //                     INNER JOIN tm_amim t3 ON t1.amim_id = t3.id
                //                     INNER JOIN tm_site t4 ON t2.site_id=t4.id
                //                     INNER JOIN tm_aemp t5 on t2.aemp_id=t5.id 
                //                     WHERE t2.ordm_date BETWEEN ''$start_date'' and ''$end_date'' ".$zone_q." AND t2.slgp_id=$sales_group_id";
                $report_data_query="SELECT t2.ordm_date ORD_DATE,
                                    t7.zone_name ZONE_NAME,t6.aemp_usnm SV_ID,t6.aemp_name SV_NAME, t5.aemp_usnm SR_ID,t5.aemp_name SR_NAME, t4.site_code OUTLET_CODE, t4.site_name OUTLET_NAME,
                                    t3.amim_code ITEM_CODE,t3.amim_name ITEM_NAME,t8.itsg_name ITEM_SUBCAT,t3.amim_duft ITEM_FACTOR, t1.ordd_qnty ORDER_QNNTY, t1.ordd_oamt ORDER_AMNT,t2.ordm_time ORDER_TIME
                                    FROM tt_ordm t2
                                    INNER JOIN tt_ordd t1 ON t2.id=t1.ordm_id
                                    INNER JOIN tm_amim t3 ON t1.amim_id = t3.id
                                    INNER JOIN tm_site t4 ON t2.site_id=t4.id
                                    INNER JOIN tm_aemp t5 on t2.aemp_id=t5.id 
                                    INNER JOIN tm_aemp t6 ON t5.aemp_mngr=t6.id
                                    INNER JOIN tm_zone t7 ON t5.zone_id=t7.id
                                    INNER JOIN tm_itsg t8 ON t3.itsg_id=t8.id 
                                WHERE t2.ordm_date BETWEEN ''$start_date'' and ''$end_date'' ".$zone_q. $q2. " AND t1.lfcl_id not in (15,16,18) ";
            break;
            case "attendance_report":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q2=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q2 = " AND t2.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id != "") {
                    $q1= " AND t2.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT t1.attn_date as Date,t1.slgp_name as ''Group'',t1.dirg_name Region,t1.zone_name Zone,t1.aemp_usnm Staff_Id,
                                    replace(t1.aemp_name,'','',''-'')Emp_Name,t1.aemp_mob1 Mobile,t1.edsg_name Designation,
                                    ifnull(t1.start_time,''0000-00-00 00:00:00'') Start_Time,ifnull(t1.end_time,''0000-00-00 00:00:00'') End_Time,ifnull(t2.atyp_name,''Absent'')as ''Status'' FROM
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
                break;
            case "note_report":
                $q1 = "";
                $q2 = "";
                $q3 = "";
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = " AND t4.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t3.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query ="SELECT t1.note_date as Date,t2.aemp_usnm Staff_Id, t2.aemp_name Emp_Name,t5.edsg_name Designation,
                                    concat(t3.slgp_code,''-'',t3.slgp_name) as ''Group'',concat(t4.zone_code,''-'', t4.zone_name) Zone,
                                    replace(t1.note_body,'','',''-'') Note_Task_Details,
                                    IF(t7.lfcl_id=2,concat(t7.ntpe_name,''/'',''InActv''),t7.ntpe_name) as Note_Type,                                    
                                    Replace(concat(t6.site_code, '' - '', t6.site_name),'','',''-'') AS Outlet,
                                    TIME(t1.note_dtim) as Note_Time,replace(t1.geo_addr, '','' , ''-'' ) as Area
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
                break;
            case "note_summary":
                $q1 = "";
                $q2 = "";
                $q3 = "";
                if($dirg_id !=""){
                    $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = " AND t2.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t3.id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT 
                        t2.aemp_usnm Staff_Id,t2.aemp_name Emp_Name,t4.edsg_name Designation,
                        t3.slgp_name as ''Group'',COUNT(t1.id)as Total_Note
                        FROM `tt_note`  t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                        INNER JOIN tm_edsg t4 ON t2.edsg_id=t4.id
                        WHERE t1.note_date between ''$start_date'' AND ''$end_date''". $q1.$q2.$q3."
                        GROUP BY t2.aemp_usnm,t2.aemp_name,t3.slgp_name,t4.edsg_name  
                        ORDER BY t2.aemp_name,t3.slgp_name";
            break;
            case "zone_summary":
                $rtype=$request->rtype;
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = "AND t4.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t2.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                if($rtype==1){
                    $report_data_query="SELECT dhbd_date Date,Zone_Name,t_sr T_SR,p_sr P_SR, round(p_sr*100/t_sr,2)as ''Attn_%'',pro_sr P_SR,(p_sr-pro_sr) Np_SR,t_outlet T_Olt,
                    c_outlet Visit,round(c_outlet*100/t_outlet,2) as ''Visit_%'',s_outet S_Olt,round(s_outet*100/c_outlet,2) S_Rate,lpc LPC,round(t_amnt/(p_sr*1000),2) Avg_SR_K,
                    round(t_outlet/t_sr,2) Olt_SR,round(c_outlet/p_sr,2) V_Olt_SR,round(t_amnt,2) Amount
                    FROM 
                    (SELECT 
                    t1.dhbd_date,
                    t4.zone_name Zone_Name,
                    t3.slgp_name slgp_name,
                    SUM(t1.dhbd_ucnt)t_sr,
                    sum(t1.dhbd_prnt)p_sr,
                    SUM( t1.`dhbd_lvsr`)l_sr,
                    SUM(t1.dhbd_pact)  pro_sr,
                    SUM(t1.dhbd_tsit)t_outlet,
                    SUM(t1.dhbd_tvit) c_outlet,
                    SUM(t1.dhbd_memo)s_outet,
                    round(ifnull(SUM(t1.dhbd_line) / SUM(t1.dhbd_memo),0), 2) lpc,
                    SUM(t1.dhbd_tamt)t_amnt
                    FROM th_dhbd_5 t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                    INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                    WHERE  t1.dhbd_date BETWEEN ''$start_date'' AND ''$end_date'' AND t3.aemp_id=''$empId''  AND t4.aemp_id=''$empId'' AND t1.role_id=1  ". $q1. $q2 ."
                    GROUP BY t2.zone_id,t4.zone_name,t1.dhbd_date,t3.slgp_name order by t4.zone_code asc)pt
                    GROUP BY  dhbd_date,Zone_Name";
                }else{
                    $report_data_query="SELECT Zone_Name,t_sr T_SR,p_sr P_SR, round(p_sr*100/t_sr,2)as ''Attn_%'',pro_sr P_SR,(p_sr-pro_sr) Np_SR,t_outlet T_Olt,
                    c_outlet Visit,round(c_outlet*100/t_outlet,2) as ''Visit_%'',s_outet S_Olt,round(s_outet*100/c_outlet,2) S_Rate,lpc LPC,round(t_amnt/(p_sr*1000),2) Avg_SR_K,
                    round(t_outlet/t_sr,2) Olt_SR,round(c_outlet/p_sr,2) V_Olt_SR,round(t_amnt,2) Amount
                    FROM 
                    (SELECT 
                    t4.zone_name Zone_Name,
                    t3.slgp_name slgp_name,
                    SUM(t1.dhbd_ucnt)t_sr,
                    sum(t1.dhbd_prnt)p_sr,
                    SUM( t1.`dhbd_lvsr`)l_sr,
                    SUM(t1.dhbd_pact)  pro_sr,
                    SUM(t1.dhbd_tsit)t_outlet,
                    SUM(t1.dhbd_tvit) c_outlet,
                    SUM(t1.dhbd_memo)s_outet,
                    round(ifnull(SUM(t1.dhbd_line) / SUM(t1.dhbd_memo),0), 2) lpc,
                    SUM(t1.dhbd_tamt)t_amnt
                    FROM th_dhbd_5 t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                    INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                    WHERE  t1.dhbd_date BETWEEN ''$start_date'' AND ''$end_date'' AND t3.aemp_id=''$empId''  AND t4.aemp_id=''$empId'' AND t1.role_id=1  ". $q1. $q2 ."
                    GROUP BY t2.zone_id,t4.zone_name,t3.slgp_name order by t4.zone_code asc)pt
                    GROUP BY Zone_Name";
                }
                break;
            case "sr_productivity":
                $rtype=$request->rtype;
                $utype=$request->utype;
                $q1 = "";
                $q2 = ""; 
                if($rtype==3){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT
                                        t3.zone_name ZONE_NAME,
                                        concat(t7.aemp_usnm,''-'',t7.aemp_name)SV_NAME,
                                        t8.t_sr T_SR,
                                        COUNT(DISTINCT t1.aemp_code)PRD_SR,
                                        ifnull(sum(t1.t_outlet),0)T_OLT,
                                        ifnull(sum(t1.v_outlet) + sum(t1.s_outet),0)VISIT,
                                        ifnull(round((sum(t1.v_outlet)+sum(t1.s_outet))*100/ifnull(sum(t1.t_outlet),1),2),0.00)''VISIT_%'',
                                        sum(t1.ro_visit)RO_VISIT,
                                        (sum(t1.v_outlet)+sum(t1.s_outet)-sum(t1.ro_visit)) WR_VISIT,
                                        sum(t1.s_outet) S_Olt,
                                        round(sum(s_outet)*100/ifnull(sum(t1.s_outet)+sum(t1.v_outlet),1),2) ''S_RATE%'',
                                        sum(t1.v_outlet)NP_OLT,
                                        round(SUM(t1.t_amnt)/1000, 2)ORDER_K,
                                        round(SUM(t1.t_amnt)/(sum(t1.s_outet)*1000), 2)AVG_OLT_K,
                                        round((SUM(t1.t_sku) / ifnull(SUM(t1.s_outet),1)), 2) LPC
                                        FROM `tt_aemp_summary1` t1
                                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                                        INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                                        INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                                        LEFT JOIN (select t1.aemp_mngr,count(DISTINCT t1.id)t_sr FROM tm_aemp t1 WHERE lfcl_id=1 AND t1.role_id=1 GROUP BY t1.aemp_mngr)t8 ON t8.aemp_mngr=t7.id 
                                        WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t1.s_outet!=0
                                        AND t2.aemp_id =$empId AND t3.aemp_id =$empId" . $q1 . $q2. " and t2.acmp_id=$acmp_id
                                        GROUP BY  t1.slgp_id,t7.aemp_usnm,t7.aemp_name
                                        ORDER BY t3.zone_code,t2.slgp_code,t7.aemp_usnm,t7.aemp_name ASC";
                }
                else if($rtype==1 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT
                            t1.date Date,
                            t3.zone_name Zone_Name,
                            t1.base_name Base_Name,
                            concat(t7.aemp_usnm,''-'',t7.aemp_name)SO_Name,
                            t1.aemp_code SR_ID,
                            t1.aemp_name SR_Name,
                            t1.aemp_mobile SR_Mobile,
                            replace(ifnull(t1.rout_name,''N/A''),'','',''-'') Route_Name,
                            ifnull(t1.t_outlet,0) T_Olt,
                            (t1.v_outlet+t1.s_outet) Visit,
                            ifnull(round((t1.v_outlet+t1.s_outet)*100/t1.t_outlet,2),0.00) ''Visit_%'',
                            t1.ro_visit RO_Visit,
                            (t1.v_outlet+t1.s_outet-t1.ro_visit) WR_Visit,
                            t1.s_outet S_Olt,
                            round(s_outet*100/(t1.s_outet+t1.v_outlet),2) ''S_Rate_%'',
                            t1.v_outlet NP_Olt,
                            round(SUM(t1.t_amnt)/1000, 2) Order_K,
                            round(SUM(t1.t_amnt)/(t1.s_outet*1000), 2)Avg_Olt_K,
                            round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) LPC,
                            t1.inTime In_Time,
                            (CASE WHEN t1.npv_min_time !=''00:00:00'' AND t1.firstOrTime>t1.npv_min_time THEN t1.npv_min_time ELSE t1.firstOrTime END) First_Visit,
                            GREATEST(t1.lastOrTime,t1.npv_max_time) Last_Visit,
                            TIMEDIFF(t1.lastOrTime, t1.firstOrTime) Work_Time
                            FROM `tt_aemp_summary1` t1
                            INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                            INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                            INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                            INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                            WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND (t1.v_outlet>0 OR t1.s_outet>0) 
                            AND t2.aemp_id =$empId AND t3.aemp_id =$empId" . $q1 . $q2. " and t2.acmp_id=$acmp_id
                            GROUP BY t1.date,t1.slgp_id,t2.slgp_name,t3.zone_name,t1.aemp_code,t1.aemp_name,t1.aemp_mobile,
                            t7.aemp_usnm,t1.base_name
                            ORDER BY t3.zone_code,t1.date,t2.slgp_code ASC";
                }
                else if($rtype==2 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT
                                        t3.zone_name Zone_Name,
                                        t1.base_name Base_Name,
                                        concat(t7.aemp_usnm,''-'',t7.aemp_name) SO_Name,
                                        t1.aemp_code SR_ID,
                                        t1.aemp_name SR_Name,
                                        t1.aemp_mobile SR_Mobile,
                                        replace(ifnull(t1.rout_name,''N/A''),'','',''-'') Route_Name,
                                        ifnull(sum(t1.t_outlet),0)T_Olt,
                                        ifnull(sum(t1.v_outlet) + sum(t1.s_outet),0)Visit,
                                        ifnull(round((sum(t1.v_outlet)+sum(t1.s_outet))*100/ifnull(sum(t1.t_outlet),1),2),0.00)''Visit_%'',
                                        sum(t1.ro_visit) RO_Visit,
                                        (sum(t1.v_outlet)+sum(t1.s_outet)-sum(t1.ro_visit)) WR_Visit,
                                        sum(t1.s_outet) S_Olt,
                                        round(sum(s_outet)*100/ifnull(sum(t1.s_outet)+sum(t1.v_outlet),1),2) ''S_Rate_%'',
                                        sum(t1.v_outlet) NP_Olt,
                                        round(SUM(t1.t_amnt)/1000, 2) Order_K,
                                        round(SUM(t1.t_amnt)/(sum(t1.s_outet)*1000), 2) Avg_Olt_K,
                                        round((SUM(t1.t_sku) / ifnull(SUM(t1.s_outet),1)), 2) LPC
                                        FROM `tt_aemp_summary1` t1
                                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                                        INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                                        INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                                        WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND (t1.v_outlet>0 OR t1.s_outet>0) 
                                        AND t2.aemp_id =$empId AND t3.aemp_id =$empId" . $q1 . $q2. " and t2.acmp_id=$acmp_id
                                        GROUP BY t1.zone_id,t1.slgp_id,t2.slgp_name,t3.zone_name,t1.aemp_code,t1.aemp_name,t1.aemp_mobile,
                                        t7.aemp_usnm,t1.base_name
                                        ORDER BY t3.zone_code,t2.slgp_code ASC";
                }
                else if($rtype==1 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT t1.ordm_date DATE,t3.zone_name ZONE_NAME,t1.aemp_usnm MANAGER_ID,t1.aemp_name MANAGER_NAME,t2.edsg_name DESIGNATION,
                                        COUNT(t1.site_id) VISIT,COUNT(DISTINCT t1.site_id) C_OLT,sum(t1.memo) T_ORDER,round(sum(t1.ordm_amnt)/1000,2)EXP,ROUND((sum(t1.ordm_amnt)/1000)/sum(t1.memo),2) EXP_OLT,
                                        ROUND(sum(t1.item_count)/sum(t1.memo),2) LPC
                                        FROM
                                        (SELECT
                                        t1.ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,count(t1.id)memo,round(sum(t1.ordm_amnt),2) ordm_amnt,sum(t1.ordm_icnt) item_count,1 flag
                                        FROM tt_ordm t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                                        WHERE  t1.ordm_date between ''$start_date'' AND ''$end_date'' AND t2.role_id >1 " .$q1 .$q2. "
                                        GROUP BY t1.ordm_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name
                                        UNION ALL
                                        SELECT
                                        t1.npro_date ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,0 memo,0 ordm_amnt,0 item_count,0 flag
                                        FROM tt_npro t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                        WHERE t1.npro_date between ''$start_date'' AND ''$end_date'' AND t2.role_id >1  " .$q1 .$q2. "
                                        GROUP BY t1.npro_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name)t1
                                        INNER JOIN tm_edsg t2 ON t1.edsg_id=t2.id
                                        INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
                                        GROUP BY t1.ordm_date,t1.aemp_id,t1.aemp_usnm,t1.aemp_name,t2.edsg_name,t3.zone_name ORDER BY  t1.ordm_date,t1.aemp_usnm;";
                }
                if($rtype==2 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id IN (".implode(',',$zone_id).")";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    $report_data_query="SELECT t3.zone_name ZONE_NAME,t1.aemp_usnm MANAGER_ID,t1.aemp_name MANAGER_NAME,t2.edsg_name DESIGNATION,
                                        COUNT(t1.site_id) VISIT,COUNT(DISTINCT t1.site_id) C_OLT,sum(t1.memo) T_ORDER,round(sum(t1.ordm_amnt)/1000,2)EXP,ROUND((sum(t1.ordm_amnt)/1000)/sum(t1.memo),2) EXP_OLT,
                                        ROUND(sum(t1.item_count)/sum(t1.memo),2) LPC
                                        FROM
                                        (SELECT
                                        t1.ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,count(t1.id)memo,round(sum(t1.ordm_amnt),2) ordm_amnt,sum(t1.ordm_icnt) item_count,1 flag
                                        FROM tt_ordm t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                                        WHERE  t1.ordm_date between ''$start_date'' AND ''$end_date'' AND t2.role_id >1 " .$q1 .$q2. "
                                        GROUP BY t1.ordm_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name
                                        UNION ALL
                                        SELECT
                                        t1.npro_date ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,0 memo,0 ordm_amnt,0 item_count,0 flag
                                        FROM tt_npro t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                        WHERE t1.npro_date between ''$start_date'' AND ''$end_date'' AND t2.role_id >1  " .$q1 .$q2. "
                                        GROUP BY t1.npro_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name)t1
                                        INNER JOIN tm_edsg t2 ON t1.edsg_id=t2.id
                                        INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
                                        GROUP BY t1.aemp_usnm,t1.aemp_name,t2.edsg_name,t3.zone_name ORDER BY t1.aemp_usnm;";
                }
                
            break;
            case "sr_non_productivity":
                $non_pr1 = "";
                $non_pr2 = "";
                if ($sales_group_id!=""){
                    $non_pr1 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                if($dirg_id !=""){
                    $non_pr2=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $non_pr2 = " and t1.zone_id IN (".implode(',',$zone_id).")";
                }
                $report_data_query="SELECT t1.date Date, 
                                    concat(t2.slgp_code, '' - '', t2.slgp_name) AS Group_Name,
                                    concat(t3.dirg_code, '' - '', t3.dirg_name) AS Region_Name,
                                    concat(t3.zone_code, '' - '', t3.zone_name) AS Zone_Name,
                                    concat(t7.aemp_usnm,''-'',t7.aemp_name)     AS SO_Name,
                                    t1.aemp_code AS SR_ID,
                                    t1.aemp_name AS SR_Name, 
                                    t1.aemp_mobile AS SR_Mobile,                                       
                                    t1.inTime AS inTime 
                                    FROM `tt_aemp_summary1` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id 
                                    INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
                                    INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                                    INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                                    WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id =$empId 
                                    AND t3.aemp_id =$empId AND t1.atten_atyp=1 " . $non_pr1 . $non_pr2 . " AND t2.acmp_id =$acmp_id AND (t1.v_outlet=0 AND t1.s_outet=0)
                                    GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id,t1.aemp_code,t3.zone_code,t3.zone_name,t3.dirg_name,t3.dirg_code,t1.aemp_name,t1.aemp_mobile,
                                    t7.aemp_usnm,t7.aemp_name,t1.inTime
                                    ORDER BY t3.zone_code,t1.date, t2.slgp_code";
            break;
            case "sr_summary_by_group":
                $q='';
                if($dirg_id !=""){
                    $q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $q = " AND t3.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id!=""){
                    $q .= " AND t2.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                    $report_data_query = "SELECT
                    t1.date Date,
                    t1.slgp_name Group_Name,
                    t4.t_sr T_SR,
                    t2.p_sr PR_SR,
                    round(t2.p_sr*100/t4.t_sr,2) ''Attendance_%''
                    t3.l_sr LV_IOM,
                    t1.pro_sr P_SR,
                    (t2.p_sr-t1.pro_sr) NP_SR
                    t1.t_outlet T_Olt,
                    t1.c_outlet Visit,
                    round(t1.c_outlet*100/t1.t_outlet,2) ''Visit_%''
                    t1.s_outet S_Olt,
                    round(t1.s_outet*100/t1.c_outlet,2) S_Rate
                    t1.lpc LPC,
                    round((t1.t_amnt/1000)/t2.p_sr,2) AVG_SR_K,
                    round(t1.t_outlet/t4.t_sr,2)    Olt_SR
                    round(t1.c_outlet/t2.p_sr)      V_Olt_SR
                    t1.t_amnt                       Amount
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
                        WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t1.s_outet > ''0'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' ". $q." AND t2.acmp_id=''$acmp_id''
                        GROUP BY t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                    
                    (SELECT
                        t1.date,
                        
                        t1.slgp_id,
                        count(DISTINCT t1.aemp_id) AS p_sr
                    FROM tt_aemp_summary t1
                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' ". $q." AND t1.atten_atyp = ''1'' AND t2.acmp_id=''$acmp_id''
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
                    WHERE t1.date >= ''$start_date'' AND t1.date <= ''$end_date'' AND t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId'' ". $q." AND t1.atten_atyp != ''1'' AND t2.acmp_id=''$acmp_id''
                    GROUP BY t1.slgp_id, t1.date) t3 ON t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                    INNER JOIN
                                            
                    (SELECT
                        t1.zone_id,
                        t1.slgp_id,
                        count(DISTINCT t1.id) AS t_sr
                    FROM tm_aemp t1
                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t2.aemp_id = ''$empId'' AND t3.aemp_id = ''$empId''  ". $q."
                            AND t1.lfcl_id = ''1'' AND t1.role_id = ''1'' AND t2.acmp_id=''$acmp_id''
                    GROUP BY  t1.slgp_id) t4 ON t1.slgp_id = t4.slgp_id";
            break;
            case "sr_wise_order_delivery":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id IN (".implode(',',$zone_id).") ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT t1.ordm_date Date,t7.slgp_name Group_Name,t6.zone_name Zone_Name,t5.aemp_usnm SR_ID,
                        t5.aemp_name SR_Name, t5.aemp_mob1 SR_Mobile, ROUND(SUM(t2.ordd_oamt),2) AS Order_Amount,
                        ROUND(SUM(t2.ordd_odat),2) AS Delivery_Amount
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,
                        tm_aemp as t5 ,tm_zone as t6,tm_slgp t7
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between ''$start_date'' AND ''$end_date'')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id  AND t7.id=t1.slgp_id
                        group by t5.`aemp_name`,
                            t5.`aemp_usnm`,
                            t5.`aemp_mob1`,
                            t1.ordm_date,
                            t6.zone_name";
            break;
            case "zone_wise_order_delivery_summary":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id IN (".implode(',',$zone_id).") ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT  t1.ordm_date Date,t7.slgp_name Group_name,                      
                        t6.zone_name Zone_Name,
                        t6.zone_code Zone_Code,
                        ROUND(SUM(t2.ordd_oamt),2) AS Order_Amount,
                        ROUND(SUM(t2.ordd_odat),2) AS Delivery_Amount
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,
                        tm_aemp as t5 ,tm_zone as t6,tm_slgp t7
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between ''$start_date'' AND ''$end_date'')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id   AND t7.id=t1.slgp_id
                        group by   t1.ordm_date,t6.zone_code,
                            t6.zone_name";
            break;
            case "sku_wise_order_delivery":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id IN (".implode(',',$zone_id).") ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }   
                $report_data_query="SELECT  t1.ordm_date Date,t7.slgp_name Group_Name,t6.zone_name Zone_Name,t5.`aemp_usnm` SR_ID,
                        t5.`aemp_name` SR_Name,                       
                        t5.`aemp_mob1` SR_Mobile,
                        t4.`amim_code` Item_Code,
                        t4.`amim_name` Item_Name,                       
                        ROUND(SUM(t2.ordd_oamt),2) AS Order_Amount,
                        ROUND(SUM(t2.ordd_odat),2) AS Delivery_Amount
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6,tm_slgp t7
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between ''$start_date'' AND ''$end_date'')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query." and t2.amim_id=t4.id and t1.aemp_id=t5.id  and t5.zone_id=t6.id   AND t7.id=t1.slgp_id
                        group by t5.`aemp_name`,
                        t5.`aemp_usnm`,
                        t5.`aemp_mob1`,
                        t4.`amim_code`,
                        t4.`amim_name`,
                        t1.ordm_date,
                        t6.zone_name";
            break;
            case "asset_details":
                $astm_id=$request->astm_id;
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t1.zone_id =$zone_id ";
                }
                $report_data_query="SELECT
                        t1.slgp_name Group_Name,t1.zone_name Zone_Name,t2.aemp_usnm SR_ID,t2.aemp_name SR_Name,replace(concat(t5.site_code,''-'',t5.site_name),'','',''-'')Site_Name,
                        concat(t6.amim_code,''-'',t6.amim_name)Item_Name,round(round(sum(t3.ordd_oamt),2),2)Olt_Asset_Exp
                        FROM 
                        tbl_asset_summary t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_usnm=t2.aemp_usnm
                        INNER JOIN tt_ordd t3 ON t1.ordm_ornm=t3.ordm_ornm
                        INNER JOIN tm_site t5 ON t1.site_id=t5.id
                        INNER JOIN tm_amim t6 ON t3.amim_id=t6.id
                        WHERE t1.slgp_id=$sales_group_id AND t1.astm_id=$astm_id AND t1.ordm_date between ''$start_date'' AND ''$end_date'' ".$zone_q."
                        GROUP  BY t1.slgp_name,t1.zone_name,t2.aemp_usnm,t2.aemp_name,t5.site_code,t5.site_name,t6.amim_code,t6.amim_name Order By t1.zone_name,t2.aemp_name ASC";
            break;
            case "asset_summary":
                $astm_id=$request->astm_id;
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t1.zone_id =$zone_id ";
                }
                $report_data_query="SELECT t1.slgp_name Group_Name,t1.zone_name Zone_Name,t1.ast_olt Asset_Olt,count(distinct t2.site_id)C_Olt,count(t2.ordm_ornm)Memo,
                        ifnull(round(sum(site_ordr),2),0.00)Olt_Exp,ifnull(round(sum(ast_itm_ordr),2),0.00)Olt_Asset_EXP
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
            break;  
            case "sr_wise_item_summary_quatar":
                $zone_q ="";
                if($dirg_id !=""){
                    $zone_q=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=""){
                    $zone_q= " AND t4.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id!=""){
                    $zone_q .= " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $module=Auth::user()->country()->module_type;
                if($module==2){
                    $report_data_query="SELECT t1.ordm_date Date, t5.slgp_name Group_Name,
                        concat(t6.aemp_usnm,''-'',t6.aemp_name)SV_Name,
                        concat(t4.aemp_usnm, '' - '', t4.aemp_name) as SR_Name,
                        concat(t3.amim_code, '' - '',t3.amim_name) as Item_Name,
                        round(sum(t2.ordd_qnty)/(t3.amim_duft)) as Ctn,
                        (sum(t2.ordd_qnty)%(t3.amim_duft)) ''Pics'',
                        round(sum(t2.ordd_opds + t2.ordd_spdi + t2.ordd_dfdo),2) as Discount,
                        round(SUM(t2.ordd_oamt),2) as Amount FROM `tt_ordm` t1 
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id 
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id 
                        INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                        INNER JOIN tm_aemp t6 ON t6.id=t4.aemp_mngr
                        WHERE t1.ordm_date BETWEEN ''$start_date'' AND ''$end_date'' 
                         ".$zone_q."
                        GROUP BY t2.amim_id, t1.aemp_id,t1.ordm_date, t5.slgp_name,t7.id,t6.id               
                        ";
                }else{
                    $report_data_query="SELECT t1.ordm_date Date, t5.slgp_name Group_Name,
                        concat(t4.aemp_usnm, '' - '', t4.aemp_name) as SR_Name,
                        concat(t3.amim_code, '' - '',t3.amim_name) as Item_Name,
                        round(sum(t2.ordd_qnty)/(t3.amim_duft)) as Ctn,
                        (sum(t2.ordd_qnty)%(t3.amim_duft)) ''Pics'',
                        round(sum(t2.ordd_opds + t2.ordd_spdi + t2.ordd_dfdo),2) as Discount,
                        round(SUM(t2.ordd_oamt),2) as Amount FROM `tt_ordm` t1 
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id 
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id 
                        INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                        WHERE t1.ordm_date BETWEEN ''$start_date'' AND ''$end_date'' 
                        ".$zone_q."
                        GROUP BY t2.amim_id, t1.aemp_id,t1.ordm_date              
                        ";
                }
            break;
            case "outlet_coverage":
                $const=' WHERE ';
                $query='';
                if($dist_id){
                    $query=$const." t1.dsct_id=$dist_id";
                }
                if($than_id){
                    $query.=" AND t1.than_id=$than_id";
                }
                $report_data_query="SELECT t2.slgp_name SALES_GROUP,t1.dsct_name DISTRICT_NAME,t1.than_name THANA_NAME,
                sum(t1.total_outlet)AVAILABLE_OUTLET,ifnull(sum(t2.slgp_out),0)ROUTED_OUTLET,ifnull(sum(t2.Visited_Sites),0) VISITED_SITES,round(sum(t2.ordm_amnt)/1000,2)ORDM_AMNT
                FROM tbl_mktm_wise_total_outlet t1 
                LEFT JOIN
                (
                    SELECT p2.id slgp_id,p2.slgp_name,p1.mktm_id,slgp_out,Visited_Sites,ordm_amnt FROM tbl_mktm_slgp_wise_outlet p1 
                    inner join tm_slgp p2 ON p1.slgp_id=p2.id
                    LEFT JOIN (Select 
                                slgp_id,mktm_id,count(distinct site_id) Visited_Sites,sum(ordm_amnt)ordm_amnt
                                FROM tbl_olt_cov_details WHERE ordm_date between ''$start_date'' AND ''$end_date'' AND slgp_id IN (".implode(',',$sales_group_id).")
                                GROUP BY slgp_id,mktm_id
                                ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                    WHERE p1.slgp_id IN (".implode(',',$sales_group_id).")
                )  t2 
                ON t1.mktm_id=t2.mktm_id 
                " .$query. "                                              
                GROUP BY t1.dsct_name,t1.than_name,t2.slgp_name";
            break;
            case "item_coverage":
                $zone_q ="";
                if($dirg_id !=""){
                    $zone_q=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=""){
                    $zone_q= " AND t2.zone_id IN (".implode(',',$zone_id).")";
                }
                if ($sales_group_id!=""){
                    $zone_q .= " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT 
                        t3.slgp_name Group_Name,t2.aemp_usnm Staff_Id,t2.aemp_name Emp_Name,COUNT(DISTINCT amim_id) C_Item,round(sum(t1.order_amnt),2)Exp
                        FROM `tbl_itm_cov_details` t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                        WHERE  ordm_date between ''$start_date'' AND ''$end_date'' " .$zone_q."
                        GROUP BY t2.aemp_usnm,t2.aemp_name,t3.slgp_name";
            break;
            case "item_summary":
                $zone_q='';
                $q2='';
                if ($sales_group_id!=""){
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                if($dirg_id !=""){
                    $zone_q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $zone_q = " and t3.zone_id IN (".implode(',',$zone_id).")";
                }
                $report_data_query="Select 
                        t3.aemp_usnm SR_ID,t3.aemp_name SR_NAME,t3.slgp_name GROUP_NAME,t3.zone_code ZONE_CODE,t3.zone_name ZONE_NAME,
                        t2.amim_name ITEM_NAME,t2.amim_code ITEM_CODE,t4.itsg_name ITEM_SUBCAT,
                        max(t3.than_olt) THANA_OLT,
                        sum(t_outlet) ROUT_OLT,sum(visit_olt) T_VISIT,sum(success_olt)T_SUCCESS,
                        ROUND(sum(order_amnt),2)EXP,sum(order_qnty)ORDER_QNTY,sum(t1.attr1) TUCH_OLT
                        FROM
                        tbl_itm_cov_details t1
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tbl_sr_hourly_summary t3 ON t1.ordm_date=t3.act_date AND t1.aemp_id=t3.aemp_id
                        INNER JOIN tm_itsg t4 ON t2.itsg_id=t4.id
                        WHERE t1.ordm_date between ''$start_date'' AND ''$end_date''  ".$q2 .$zone_q. "
                        GROUP BY t1.aemp_id,t3.aemp_usnm,t3.aemp_name,t3.slgp_name,t3.zone_code,t3.zone_name,t3.than_name,
                        t2.amim_name,t1.slgp_id,t2.amim_code,t4.itsg_name
                        ORDER BY t3.aemp_usnm,t4.itsg_code,t2.amim_code";
            break;
            case "item_summary_depo_wise":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t3.zone_id IN (".implode(',',$zone_id).") ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT 
                        t5.amim_code ITEM_CODE,t5.amim_name ITEM_NAME,t4.dlrm_code DEPO_CODE,t4.dlrm_name DEPO_NAME,t6.itsg_name ITEM_SUBCAT_NAME,t6.itsg_code ITEM_SUBCAT_CODE,t7.itcl_name ITEM_CLASS_NAME,t7.itcl_code ITEM_CLASS_CODE,
                        ROUND(SUM(t2.ordd_oamt),2) ORDER_AMNT,ROUND(SUM(t2.ordd_odat),2)DELV_AMNT,
                        (SUM(t2.ordd_inty)%t5.amim_duft) QTY_PICS,SUBSTRING_INDEX(SUM(t2.ordd_inty)/t5.amim_duft,''.'',1) QTY_CTN,
                        t5.amim_duft CTN_SIZE,SUM(t2.ordd_inty)T_QTY_PICS
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                        INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
                        INNER JOIN tm_amim t5 ON t2.amim_id=t5.id
                        INNER JOIN tm_itsg t6 ON t5.itsg_id=t6.id
                        INNER JOIN tm_itcl t7 ON t5.itcl_id=t7.id
                        INNER JOIN tm_zone t8 ON t3.zone_id=t8.id
                        WHERE t1.ordm_date between ''$start_date'' AND ''$end_date'' ". $q1. $q2."
                        GROUP BY t5.amim_code,t5.amim_name,t4.dlrm_code,t4.dlrm_name,t6.itsg_name,t6.itsg_code,t7.itcl_name,t7.itcl_code,t5.amim_duft
                        ORDER BY t5.amim_code,t4.dlrm_code;";
            break;
            case "order_vs_delivery_adv":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t4.zone_id IN (".implode(',',$zone_id).") ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                $report_data_query="SELECT 
                        t8.dirg_name REGION_NAME,t7.zone_name ZONE_NAME,t4.aemp_usnm SR_ID,t4.aemp_name SR_NAME,
                        t6.site_code OUTLET_CODE,t6.site_name OUTLET_NAME,t1.ordm_date ORDER_DATE,t1.ordm_ornm ORDER_NO,ROUND(t1.ordm_amnt,2)ORDER_AMNT,
                        IFNULL(t5.INV_AMNT,0)CHALLN_AMNT,IFNULL(t5.DELV_AMNT,0)DELV_AMNT,t1.ordm_drdt DELV_DATE, DAYNAME(t1.ordm_drdt) DAY_NAME,t9.lfcl_name ORD_STATUS
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_dlrm t3 ON t1.dlrm_id=t3.id
                        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
                        INNER JOIN tm_site t6 ON t1.site_id=t6.id
                        INNER JOIN tm_zone t7 ON t4.zone_id=t7.id
                        INNER JOIN tm_dirg t8 ON t7.dirg_id=t8.id
                        INNER JOIN tm_lfcl t9 ON t1.lfcl_id=t9.id
                        LEFT JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                        WHERE t1.ordm_date BETWEEN ''$start_date'' AND ''$end_date'' " .$q1.$q2. "
                        GROUP BY t8.dirg_name,t7.zone_name,t4.aemp_usnm,t4.aemp_name,
                        t6.site_code,t6.site_name,t1.ordm_date,t1.ordm_ornm,t1.ordm_amnt,t1.ordm_drdt,t9.lfcl_name
                        ;";
            break;
            case "outlet_dues_report_adv":
                $query='';
                $q='';
                $aemp_id=Auth::user()->employee()->id;
                if($sales_group_id){
                    $query=" AND t5.slgp_id IN (".implode(',',$sales_group_id).")";
                }
                if($dirg_id !=""){
                    $q=" AND t7.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q = " AND t7.zone_id IN (".implode(',',$zone_id).") ";
                }
                $report_data_query="SELECT 
                        t5.TRIP_NO,t5.ORDM_DATE ORDER_DATE,t6.slgp_name GROUP_NAME,t1.ordm_ornm ORDER_NO,t2.site_code OUTLET_CODE,t2.site_name OUTLET_NAME,t1.ordm_amnt ORDM_AMNT,
                        t1.sreq_amnt CREQ_AMNT,t1.sapr_amnt CAPR_AMNT,t1.scol_amnt CCOLL_AMNT,t1.spbd_type CREDIT_TYPE, t3.aemp_usnm SR_ID,t3.aemp_name SR_NAME,t1.cpcr_cdat CREDIT_COLL_DATE,
                        If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_usnm,'''') as APPROVER_ID,
                        If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_name,'''') as APPROVER_NAME,
                        CASE WHEN t5.`DELV_AMNT`-t5.`COLLECTION_AMNT`<1 AND t1.sapr_amnt>0 THEN ''Done'' 
                        
                        ELSE IF(t5.DELV_AMNT-t5.COLLECTION_AMNT>=1,''Due'',''Done'')END C_STATUS,
                        t7.zone_name ZONE_NAME,
                        round(ROUND(t5.DELV_AMNT,2)-ROUND(t5.COLLECTION_AMNT,2),2)  DUES_AMNT,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Cash'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) CASH,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Cheque'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) CHEQUEUE,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Online'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) ''ONLINE'',
                        DATE(IF(t9.COLLECTION_TYPE=''Cheque'', MAX(t9.CHECK_DATE) , '''')) CHQ_DATE,
                        t13.dirg_name REGION_NAME,t11.aemp_usnm DM_ID,t11.aemp_name DM_NAME,
                        ROUND(t5.COLLECTION_AMNT,2)COLLECTION_AMNT,
                        ROUND(t5.INV_AMNT,2) INV_AMNT,
                        ROUND(t5.DELV_AMNT,2) DELV_AMNT
                        FROM `tl_cpcr` t1
                        INNER JOIN tm_site t2 ON t1.site_id=t2.id
                        INNER JOIN tm_aemp t4 ON t1.aemp_eusr=t4.id
                        INNER JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                        INNER JOIN tm_aemp t3 ON t5.AEMP_ID=t3.id
                        INNER JOIN user_area_permission t7 ON t3.zone_id=t7.zone_id
                        INNER JOIN dm_trip t10 ON t5.TRIP_NO=t10.TRIP_NO
                        INNER JOIN tm_aemp t11 ON t10.DM_ID=t11.aemp_usnm
                        INNER JOIN tm_dirg t13 ON t7.dirg_id=t13.id
                        LEFT JOIN  dm_invoice_collection_mapp t8 ON t1.ordm_ornm=t8.TRANSACTION_ID
                        LEFT JOIN dm_collection t9 ON t9.COLL_NUMBER=t8.MAP_ID
                        INNER JOIN user_group_permission t6 ON t5.slgp_id=t6.slgp_id
                        WHERE t6.aemp_id=$aemp_id AND t7.aemp_id=$aemp_id  AND  t5.`DELV_AMNT`-t5.`COLLECTION_AMNT`>.9 ".$query. $q ."
                        GROUP BY t5.ORDM_DATE,t6.slgp_name,t1.ordm_ornm,t2.site_code,t2.site_name,t1.ordm_amnt,
                        t13.dirg_code,t13.dirg_name,t11.aemp_usnm,t11.aemp_name,
                        t5.TRIP_NO,t1.sreq_amnt,t1.scol_amnt,t1.sapr_amnt,t1.spbd_type,t3.aemp_usnm ,t3.aemp_name,t1.cpcr_cdat,t7.zone_name,t5.COLLECTION_AMNT,t5.INV_AMNT,t5.DELV_AMNT;";
                break;
                case "outlet_dues_report_credit":

                    $query='';
                    $q='';
                    $aemp_id=Auth::user()->employee()->id;
                    if($sales_group_id){
                        $query=" AND t1.slgp_id IN (".implode(',',$sales_group_id).")";
                        $query .=" AND t2.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    if($dirg_id !=""){
                        $q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q = " AND t3.zone_id IN (".implode(',',$zone_id).") ";
                    }
                    $data=DB::connection($this->db)->select("SELECT t5.zone_code ZONE_CODE,t5.zone_name ZONE_NAME,t4.aemp_usnm SV_ID,t4.aemp_name SV_NAME,
                            t3.aemp_usnm SR_ID,t3.aemp_name SR_NAME,t7.site_code OUTLET_CODE,t7.site_name OUTLET_NAME,
                            t6.mother_site_code MOTHER_CODE,t6.mother_site_name MOTHER_NAME,
                            t2.ORDM_DRDT DELV_DATE,
                            t2.ORDM_ORNM ORDER_NO,ROUND(t2.ORDD_AMNT,2)ORDD_AMNT,ROUND(t2.INV_AMNT,2)INV_AMNT,ROUND(t2.DELV_AMNT,2)DELV_AMNT,ROUND(t2.COLLECTION_AMNT,2)COLLECTION_AMNT,ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2)DUES_AMNT,
                            t1.stcm_days DAY_LIMIT,
                            CASE WHEN DATEDIFF(CURDATE(),t2.ORDM_DRDT)BETWEEN 37 AND 51 THEN ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2) ELSE 0 END 30_DAYS,
                            CASE WHEN DATEDIFF(CURDATE(),t2.ORDM_DRDT) BETWEEN 52 AND 66 THEN ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2) ELSE 0 END 45_DAYS,
                            CASE WHEN DATEDIFF(CURDATE(),t2.ORDM_DRDT) BETWEEN 67 AND 96 THEN ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2) ELSE 0 END 60_DAYS,
                            CASE WHEN DATEDIFF(CURDATE(),t2.ORDM_DRDT) BETWEEN 97 AND 126 THEN ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2) ELSE 0 END 90_DAYS,
                            CASE WHEN DATEDIFF(CURDATE(),t2.ORDM_DRDT)>126 THEN ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2) ELSE 0 END 120_DAYS
                            FROM `tl_stcm` t1
                            INNER JOIN dm_trip_master t2 ON t1.slgp_id=t2.slgp_id AND t1.site_id=t2.SITE_ID
                            INNER JOIN tm_aemp t3 ON t2.AEMP_ID=t3.id
                            INNER JOIN tm_aemp t4 ON t3.aemp_mngr=t4.id
                            INNER JOIN tm_zone t5 ON t3.zone_id=t5.id
                            LEFT JOIN tl_site_party_mapping t6 ON t6.site_code=t2.SITE_CODE
                            INNER JOIN tm_site t7 ON t2.SITE_ID=t7.id
                            where t1.optp_id=2 AND t2.DELV_AMNT-t2.COLLECTION_AMNT>.9 ". $query. $q ."
                            ORDER BY t7.site_code,t2.ORDM_ORNM;");
                    break;
                case "outlet_crq_history":
                    $query='';
                    $q='';
                    $aemp_id=Auth::user()->employee()->id;
                    if($sales_group_id){
                        $query=" AND t5.slgp_id IN (".implode(',',$sales_group_id).")";
                    }
                    if($dirg_id !=""){
                        $q=" AND t7.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q = " AND t7.zone_id IN (".implode(',',$zone_id).") ";
                    }
                    $report_data_query="SELECT 
                            t5.TRIP_NO,t5.ORDM_DATE ORDER_DATE,t6.slgp_name GROUP_NAME,t1.ordm_ornm ORDER_NO,t2.site_code OUTLET_CODE,t2.site_name OUTLET_NAME,t1.ordm_amnt ORDM_AMNT,
                            t1.sreq_amnt CREQ_AMNT,t1.sapr_amnt CAPR_AMNT,t1.scol_amnt CCOLL_AMNT,t1.spbd_type CREDIT_TYPE, t3.aemp_usnm SR_ID,t3.aemp_name SR_NAME,t1.cpcr_cdat CREDIT_COLL_DATE,
                            If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_usnm,'''') as APPROVER_ID,
                            If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_name,'''') as APPROVER_NAME,
                            CASE WHEN t5.`DELV_AMNT`-t5.`COLLECTION_AMNT`<1 AND t1.sapr_amnt>0 THEN ''Done'' 
                            
                            ELSE IF(t5.DELV_AMNT-t5.COLLECTION_AMNT>=1,''Due'',''Done'')END C_STATUS,
                            t7.zone_name ZONE_NAME,
                            round(ROUND(t5.DELV_AMNT,2)-ROUND(t5.COLLECTION_AMNT,2),2)  DUES_AMNT,
                            ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Cash'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) CASH,
                            ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Cheque'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) CHEQUEUE,
                            ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE=''Online'' THEN t9.COLLECTION_AMNT ELSE 0 END),2) ''ONLINE'',
                            DATE(IF(t9.COLLECTION_TYPE=''Cheque'', MAX(t9.CHECK_DATE) , '''')) CHQ_DATE,
                            t13.dirg_name REGION_NAME,t11.aemp_usnm DM_ID,t11.aemp_name DM_NAME,
                            ROUND(t5.COLLECTION_AMNT,2)COLLECTION_AMNT,
                            ROUND(t5.INV_AMNT,2) INV_AMNT,
                            ROUND(t5.DELV_AMNT,2) DELV_AMNT
                            FROM `tl_cpcr` t1
                            INNER JOIN tm_site t2 ON t1.site_id=t2.id
                            INNER JOIN tm_aemp t4 ON t1.aemp_eusr=t4.id
                            INNER JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                            INNER JOIN tm_aemp t3 ON t5.AEMP_ID=t3.id
                            INNER JOIN user_area_permission t7 ON t3.zone_id=t7.zone_id
                            INNER JOIN dm_trip t10 ON t5.TRIP_NO=t10.TRIP_NO
                            INNER JOIN tm_aemp t11 ON t10.DM_ID=t11.aemp_usnm
                            INNER JOIN tm_dirg t13 ON t7.dirg_id=t13.id
                            LEFT JOIN  dm_invoice_collection_mapp t8 ON t1.ordm_ornm=t8.TRANSACTION_ID
                            LEFT JOIN dm_collection t9 ON t9.COLL_NUMBER=t8.MAP_ID
                            INNER JOIN user_group_permission t6 ON t5.slgp_id=t6.slgp_id
                            WHERE t6.aemp_id=$aemp_id AND t7.aemp_id=$aemp_id  AND date(t1.created_at) BETWEEN ''$start_date'' AND ''$end_date''
                            GROUP BY t5.ORDM_DATE,t6.slgp_name,t1.ordm_ornm,t2.site_code,t2.site_name,t1.ordm_amnt,
                            t13.dirg_code,t13.dirg_name,t11.aemp_usnm,t11.aemp_name,
                            t5.TRIP_NO,t1.sreq_amnt,t1.scol_amnt,t1.sapr_amnt,t1.spbd_type,t3.aemp_usnm ,t3.aemp_name,t1.cpcr_cdat,t7.zone_name,t5.COLLECTION_AMNT,t5.INV_AMNT,t5.DELV_AMNT;";
                    break;
                case "market_outlet_sr_outlet":
                    $report_data_query="SELECT t3.slgp_name GROUP_NAME,t1.dsct_name DIST_NAME,t1.than_name THAN_NAME,t1.ward_name WARD_NAME,
                            replace(t1.mktm_name,'','',''-'')MARKT_NAME,t1.total_outlet MKT_OUTLET,ifnull(t2.slgp_out,0)ROUTED_OUTLET
                            FROM tbl_mktm_wise_total_outlet t1 
                            LEFT JOIN(SELECT slgp_id,mktm_id,slgp_out FROM tbl_mktm_slgp_wise_outlet WHERE slgp_id IN (".implode(',',$sales_group_id).") )  t2 
                            ON t1.mktm_id=t2.mktm_id
                            LEFT JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                            WHERE t1.than_id=$than_id
                            GROUP BY t1.dsct_name,t1.than_name,t1.mktm_id,t1.mktm_name,t1.ward_name,t1.total_outlet";
                    break;
        }

        $emp_usnm = $this->currentUser->employee()->aemp_usnm;
        $emp_name = $this->currentUser->employee()->aemp_name;
        $aemp_emal = $email_address;
        $cont_conn = Auth::user()->countryDB()->db_name;
        $report_name=$reportType.'('.$start_date.' To '. $end_date .' )';
        // DB::connection($this->db)->select("INSERT INTO `tbl_report_request_temp`( `report_name`, `report_heading_query`, `report_data_query`, `start_date`, `end_date`, 
        // `cont_conn`, `aemp_id`, `aemp_usnm`, `aemp_name`, `aemp_email`, `report_link`, `report_status`, `created_at`, `updated_at`) 
        // VALUES('$reportType','0','$report_data_query','$start_date','$end_date','$cont_conn','$empId','$emp_usnm','$emp_name','$aemp_emal','','0',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)");
        // return 1;

        DB::connection($this->db)->select("INSERT INTO tbl_report_request( report_name,report_heading_query,report_data_query,cont_conn,aemp_id,aemp_usnm, aemp_name,aemp_email,report_status)
         VALUES('$report_name','0','$report_data_query','$cont_conn','$empId','$emp_usnm','$emp_name','$aemp_emal','1')");
         return 1;

    }
    public function getGeneratedReportList(){
        $emid=Auth::user()->employee()->id;
        $generated_report_list=DB::connection($this->db)->select("SELECT `id`, `report_name`, `report_link`,
        report_status,aemp_email, `created_at` 
        FROM `tbl_report_request` WHERE aemp_id='$emid'
        AND date(created_at) <=(curdate()) AND  date(created_at)>= (curdate()-INTERVAL 2 DAY)
        Order by created_at desc");
        return $generated_report_list;
    }

}