<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\DataExport\RequestReportData;
//use Maatwebsite\Excel\Facades\Excel;
Use Excel;
class CommonReportController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(8000000);
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function getUserAPIVersion(){
        $user_data=DB::connection('myprg_comm')->select("SELECT user_name email,max(api_int) api_int from tblg_login_log group by user_name");
        $emp_data=DB::connection($this->db)->select("SELECT t1.aemp_name,t1.aemp_usnm email,'' api_int,t1.aemp_mob1,t2.slgp_name from tm_aemp t1
        INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id where t1.lfcl_id=1");
        $usr_emp = array_merge($user_data,$emp_data);
        return $usr_emp;


    }
    public function weeklyorderSummaryReport()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
       
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        // return view('report.summary_report.common_report')
        //     ->with('acmp', $acmp)
        //     ->with('region', $region)
        //     ->with('dsct', $dsct)
        //     ->with('dsct1', $dsct)
        //     ->with('role_id', $user_role_id)
        //     ->with('emid',$empId);
        return view('report.summary_report.common_report',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone]);
    }
    public function getTrackingRecord(){
        $period=date('Y-m-d');
        $u=Auth::user()->email;
    	$emp_id=DB::connection($this->db)->select("SELECT id FROM tm_aemp WHERE aemp_usnm ='$u'");
		//dd(Auth::user()->id);
   		$emid=$emp_id[0]->id;
        //return $emid;
        $this->db = Auth::user()->country()->cont_conn;
    $un_emp=DB::connection($this->db)->select("SELECT
    t4.aemp_usnm ,                                
    t4.aemp_mob1 ,                               
    concat(t2.role_name,'-',t4.aemp_name)		 AS aemp_name,
    t1.dhbd_tvit                                 AS total_visited,
    t1.dhbd_tsit                                 AS t_outlet,
    t1.dhbd_ucnt                                 AS totalSr,
    t1.dhbd_memo                                 AS memo,
    if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
    if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
    if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
    if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
    if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
    if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
    if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
    t1.dhbd_prdt                                 AS is_productive,
    t1.role_id                                   AS role_id,
    t2.role_name                                 AS role_name,
    t1.aemp_id                                   AS oid,
    if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
    t1.cont_id                                   as country_id
    FROM th_dhbd_5 AS t1
    INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
    LEFT JOIN th_dhbd_5 AS t3
    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date ='$period'
    INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
    LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
    WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date ='$period'
    UNION ALL
    SELECT
    t4.aemp_usnm, 
    t4.aemp_mob1,
    concat(t2.role_name,'-',t4.aemp_name)		  AS aemp_name,
    t1.dhbd_tvit                                 AS total_visited,
    t1.dhbd_tsit                                 AS t_outlet,
    t1.dhbd_ucnt                                 AS totalSr,
    t1.dhbd_memo                                 AS memo,
    if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
    if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
    if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
    if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
    if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
    if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
    if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
    t1.dhbd_prdt                                 AS is_productive,
    t1.role_id                                   AS role_id,
    t2.role_name                                 AS role_name,
    t1.aemp_id                                   AS oid,
    if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
    t1.cont_id                                   as country_id
    FROM th_dhbd_5 AS t1
    INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
    LEFT JOIN th_dhbd_5 AS t3
    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date='$period'
    INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
    LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
    INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
    WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date ='$period'");
    return $un_emp;
    }
    public function getUserWiseReport(Request $request){
        $period=$request->period;
        $emid=$request->emid;
        $query="SELECT
        t4.aemp_usnm ,                                
        t4.aemp_mob1 ,                               
        concat(t2.role_name,'-',t4.aemp_name)		 AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.dhbd_prdt                                 AS is_productive,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
        t1.cont_id                                   as country_id
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date ='$period'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date ='$period'
        UNION ALL
        SELECT
        t4.aemp_usnm, 
        t4.aemp_mob1,
        concat(t2.role_name,'-',t4.aemp_name)		  AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.dhbd_prdt                                 AS is_productive,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
        t1.cont_id                                   as country_id
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date='$period'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
        WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date ='$period'";
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $un_emp = DB::connection($this->db)->select(DB::raw($query));
      return $un_emp;

    }

//All report section start
    public function commonReportFilter(Request $request)
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
            $q2 = " AND t1.slgp_id = '$sales_group_id' ";
        }
        if ($dist_id != "") {
            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {
            $q6 = " AND t1.than_id = '$than_id'";
        }
        if($time_period){ 
            if($time_period==0){
                $start_date= Carbon::now()->format('Y-m-d');
                $end_date= Carbon::now()->format('Y-m-d');
            }
            else if($time_period==1){
                $start_date= Carbon::yesterday()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==2){
                $start_date= Carbon::now()->subDays(3)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==3){
                $start_date= Carbon::now()->subDays(7)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==4){
                $start_date= Carbon::now()->subDays(30)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==5){
                $start_date= Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else{
                $start_date=$time_period;
                $end_date=$time_period;
            }   
        }else{
            $start_date= Carbon::now()->format('Y-m-d');
            $end_date= Carbon::now()->format('Y-m-d');
        }
        $oid_min_max=DB::connection($this->db)->select("Select min(id) as min_id,max(id) as max_id FROM tt_ordm where ordm_date BETWEEN '$start_date' AND '$end_date'");
        $min_id=$oid_min_max[0]->min_id;
        $max_id=$oid_min_max[0]->max_id;
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
                $zone_q= " AND t6.id = '$zone_id' ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT distinct t3.id, t3.itcl_name
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4
                        where ".$min_q." t1.ordm_date between  '$start_date' and '$end_date'
                        and t1.id=t2.ordm_id 
                        and t1.slgp_id=$sales_group_id and t2.amim_id=t4.id and t4.itcl_id = t3.id and t3.lfcl_id=1");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcl_name;
                $select_q.=",sum(if(t3.id='$temp_v_id',t2.ordd_oamt,0))`$temp_v_cl`";
            }
            $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC");
            return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt);
            

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
                $zone_q= " AND t6.id = '$zone_id' ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT DISTINCT
                        t5.id,t5.itcg_name
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                        INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
                        INNER JOIN tm_itcg t5 ON t4.itcg_id=t5.id
                        WHERE ".$min_q."  t1.ordm_date between  '$start_date' and '$end_date'
                        AND t1.slgp_id=$sales_group_id AND t5.lfcl_id=1");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcg_name;
                $select_q.=",sum(if(t3.itcg_id='$temp_v_id',t2.ordd_oamt,0))`$temp_v_cl`";
            }
            $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC");
            return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt);
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
                $zone_q= " AND t6.id = '$zone_id' ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT DISTINCT
                        t5.id,t5.itcg_name
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                        INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
                        INNER JOIN tm_itcg t5 ON t4.itcg_id=t5.id
                        WHERE ".$min_q."  t1.ordm_date between  '$start_date' and '$end_date'
                        AND t1.slgp_id=$sales_group_id AND t5.lfcl_id=1");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcg_name;
                $select_q.=",sum(if(t3.itcg_id='$temp_v_id',1,0))`$temp_v_cl`";
            }
            $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date ASC");
            return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt);
        }
        else if($reportType == "sr_route_outlet"){
            $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name,COUNT(t3.site_id) as t_site
                        FROM tm_aemp t1
                        INNER JOIN tl_rpln t2 on t1.id=t2.aemp_id
                        INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                        INNER JOIN tm_zone t4 ON t1.zone_id=t4.id
                        INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                        WHERE t1.role_id=1 AND t2.rpln_day=DAYNAME('$start_date')
                        ".$q2.$q1."
                        AND t1.lfcl_id=1 AND t2.lfcl_id=1 AND t3.lfcl_id=1
                        GROUP BY t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name");
            return $data;
        }
        else if($reportType == "group_wise_route_outlet"){
            $data=DB::connection($this->db)->select("select slgp_id, count(rout_id)t_route,sum(SR)t_sr,
                        SUM(CASE WHEN  site<60 THEN 1 ELSE 0 END)`b_60` ,
                        SUM(CASE WHEN site>=60 AND site<=120 THEN 1 ELSE 0 END) `b_60_120`,
                        SUM(CASE WHEN  site>120 THEN 1 ELSE 0 END)`b_120` 
                        FROM(
                        SELECT t1.slgp_id,t2.rout_id , COUNT(t3.site_id)site,count(DISTINCT(t1.id))SR
                        FROM `tm_aemp` t1
                        LEFT JOIN  tl_rpln t2 ON t1.id=t2.aemp_id
                        INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                        WHERE  t2.rpln_day=DAYNAME('$start_date')".$q2.$q1." AND  t1.lfcl_id=1 AND t3.lfcl_id=1
                        GROUP BY t1.slgp_id,t2.rout_id)pp GROUP BY slgp_id");
            return $data;
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
                $zone_q= " AND t6.id = '$zone_id' ";
            }
            if($min_id !='' && $max_id !=''){
                $min_q="t1.id between $min_id and $max_id AND";
            }
            $class_list=DB::connection($this->db)->select("SELECT distinct t3.id, t3.itcl_name
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4
                        where ".$min_q."
                         t1.ordm_date between  '$start_date'  and '$end_date'
                        and t1.id=t2.ordm_id 
                        and t1.slgp_id=$sales_group_id and t2.amim_id=t4.id and t4.itcl_id = t3.id and t3.lfcl_id=1");
            for($i=0;$i<count($class_list);$i++){
                $temp_v_id=$class_list[$i]->id;
                $temp_v_cl=$class_list[$i]->itcl_name;
                $select_q.=",sum(if(t3.id='$temp_v_id',1,0))`$temp_v_cl`";
            }
            $class_wise_ord_memo=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                        ".$select_q."
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date asc");
            return array('class_list'=>$class_list,'class_wise_ord_memo'=>$class_wise_ord_memo);

            }else if ($reportType == "sr_activity_hourly_order"){
                if($time_period==0){
                    $q_z="";
                    $q2_g="";
                    $q3_r="";
                    $q9_s="";
                    if ($zone_id != "") {

                        $q_z = " AND t2.zone_id =$zone_id";
                    }
                    if ($sales_group_id != "") {

                        $q2_g = " AND t1.slgp_id = '$sales_group_id'";
                    }
                    
                   // DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key=0"));
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $sql = "CREATE TEMPORARY TABLE temp1 AS SELECT
                    t4.acmp_name, t4.slgp_name, t3.dirg_name,t3.zone_name, t1.ordm_date,t1.aemp_id, t2.aemp_name, t2.aemp_usnm, t2.aemp_mob1,
                    HOUR(ordm_time)'hr', COUNT(ordm_ornm) AS order_number,t5.rout_name AS rout_name
                    FROM `tt_ordm` t1
                    INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                    INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                    INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                    INNER JOIN tm_rout t5 ON t1.rout_id=t5.id
                    WHERE t1.ordm_date = '$start_date' AND
                        t3.aemp_id = '$empId' and t4.aemp_id='$empId' ".$q_z . $q2_g."
                    GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";
                    $srData = DB::connection($this->db)->select(DB::raw($sql));
                    $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
                    acmp_name, zone_name, dirg_name,rout_name from temp1 GROUP BY aemp_id"));

                        $row_no = 1;
                        $icol = 8;
                    $final[0][0] = "Order Date";
                    $final[0][1] = "Group";
                    $final[0][2] = "Zone";
                    $final[0][3] = "SR ID";
                    $final[0][4] = "SR Name";
                    $final[0][5] = "SR Mobile";
                    $final[0][6] = "09AM";
                    $final[0][7] = "10AM";
                    $final[0][8] = "11AM";
                    $final[0][9] = "12PM";
                    $final[0][10] = "01PM";
                    $final[0][11] = "02PM";
                    $final[0][12] = "03PM";
                    $final[0][13] = "04PM";
                    $final[0][14] = "05PM";
                    $final[0][15] = "06PM";
                    $final[0][16] = "07PM";
                    $final[0][17] = "08PM";
                    $final[0][18] = "09PM";
                    $final[0][19] = "Rout Name";
                    foreach ($srDataw as $rows) {
                        $colu = 6;
                        $sr_id = $rows->aemp_id;
                        $final[$row_no][0] = $rows->ordm_date;
                        $final[$row_no][1] = $rows->slgp_name;
                        $final[$row_no][2] = $rows->zone_name;
                        $final[$row_no][3] = $rows->aemp_usnm;
                        $final[$row_no][4] = str_replace(",","-",$rows->aemp_name);
                        $final[$row_no][5] = $rows->aemp_mob1;
                        $final[$row_no][19] = $rows->rout_name;
                        if ($colu==6){
                        }
                        $h=8;
                        for ($i=6; $i<21; $i++) {
                            $k = $i;
                            if ($k==6){
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                            }else if ($i>18){
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                            }else{
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$h' and aemp_id='$sr_id'"));
                            }

                            $tam=0;
                            foreach ($srDatas as $result)
                                if($result->order_number > 0){$tam = $result->order_number;}else{$tam = 0;}
                            //echo "{" . $k . " - ". $tam. "}";
                            $final[$row_no][$colu] = $tam;
                            $colu++;
                            $h++;
                        }
                        $row_no = $row_no +1;
                    }

                 return $final;
                }
                else{
                    $q_z="";
                    $q2_g="";
                    if($dirg_id !=""){
                        $q_z=" AND zone_id IN (".implode(',',$zone_list).")";
                    }
                    if ($zone_id != "") {
                        $q_z = " AND zone_id = $zone_id";
                    }
                    if ($sales_group_id != "") {
                        $q2_g = " AND slgp_id = '$sales_group_id'";
                    }
                    
                    $data=DB::connection($this->db)->select("SELECT act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1,9am,10am,11am,12pm,1pm,2pm,3pm,4pm,5pm,6pm,7pm,8pm,9pm
                        FROM tbl_sr_activity_summary
                        WHERE act_date BETWEEN '$start_date' AND '$end_date'".$q2_g.$q_z."
                        ORDER BY act_date asc");
                    return $data;
                }

        } else if ($reportType == "sr_activity_hourly_visit"){
            if($time_period==0){
                $q_z="";
                $q2_g="";
                if ($zone_id != "") {
                    $q_z = " AND t2.zone_id = $zone_id";
                }
                if ($sales_group_id != "") {
                    $q2_g = " AND t1.slgp_id = '$sales_group_id'";
                }
              //  DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key=0"));
                DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                $sql = "CREATE TEMPORARY TABLE temp1 AS SELECT
                t4.acmp_name, t4.slgp_name, t3.dirg_name,t3.zone_name, t1.ordm_date,t1.aemp_id, t2.aemp_name, t2.aemp_usnm, t2.aemp_mob1,
                HOUR(ordm_time)'hr', COUNT(ordm_ornm) AS order_number
                FROM `tt_ordm` t1
                INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                WHERE  t1.ordm_date = '$start_date' AND
                    t3.aemp_id = '$empId' and t4.aemp_id='$empId' ".$q_z . $q2_g."
                GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";

                            $sql2 = "CREATE TEMPORARY TABLE temp2 AS SELECT t1.aemp_id, HOUR(npro_time) as hr, COUNT(t1.id) as note FROM `tt_npro` t1
                INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                WHERE t1.npro_date = '$start_date' AND
                    t3.aemp_id = '$empId' and t4.aemp_id='$empId' ". $q_z . $q2_g."
                GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";


                $srData = DB::connection($this->db)->select(DB::raw($sql));
                $srData2 = DB::connection($this->db)->select(DB::raw($sql2));
                $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
                                                                    acmp_name, zone_name, dirg_name from temp1 GROUP BY aemp_id"));
                $row_no = 1;
                $icol = 6;
                $final[0][0] = "Order Date";
                $final[0][1] = "Group";
                $final[0][2] = "Zone";
                $final[0][3] = "SR ID";
                $final[0][4] = "SR Name";
                $final[0][5] = "SR Mobile";
                $final[0][6] = "09AM";
                $final[0][7] = "10AM";
                $final[0][8] = "11AM";
                $final[0][9] = "12PM";
                $final[0][10] = "01PM";
                $final[0][11] = "02PM";
                $final[0][12] = "03PM";
                $final[0][13] = "04PM";
                $final[0][14] = "05PM";
                $final[0][15] = "06PM";
                $final[0][16] = "07PM";
                $final[0][17] = "08PM";
                $final[0][18] = "09PM";
            
                foreach ($srDataw as $rows) {
                    $colu = 6;
                    $sr_id = $rows->aemp_id;
                    $final[$row_no][0] = $rows->ordm_date;
                    $final[$row_no][1] = $rows->slgp_name;
                    $final[$row_no][2] = $rows->zone_name;
                    $final[$row_no][3] = $rows->aemp_usnm;
                    $final[$row_no][4] = str_replace(",","-",$rows->aemp_name);
                    $final[$row_no][5] = $rows->aemp_mob1;
                    if ($colu==6){

                    }
                    $h=8;
                    for ($i=6; $i<21; $i++) {
                        $k = $i;
                        if ($k==6){
                            $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                            $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                        }else if ($i>18){
                            $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                            $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                        }else{
                            $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$h' and aemp_id='$sr_id'"));
                            $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr ='$h' and aemp_id='$sr_id'"));
                        }

                        $tam2=0;
                        $tam=0;
                        foreach ($srDatas2 as $result2)
                            if($result2->note > 0){$tam2 = $result2->note ;}else{$tam2 = 0;}
                        foreach ($srDatas as $result)
                            if($result->order_number > 0){$tam = $result->order_number ;}else{$tam = 0;}
                        //echo "{" . $k . " - ". $tam. "}";
                        $final[$row_no][$colu] = $tam+$tam2;
                        $colu++;
                        $h++;
                    }

                    $row_no = $row_no +1;
                }
                return $final;
            }
            else{

                $q_z="";
                $q2_g="";
                if($dirg_id !=""){
                    $q_z=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q_z = " AND zone_id = $zone_id";
                }
                if ($sales_group_id != "") {
                    $q2_g = " AND slgp_id = '$sales_group_id'";
                }
                $data=DB::connection($this->db)->select("SELECT act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1,SUM(9am+9amn)9am,SUM(10am+10amn)10am,SUM(11am+11amn)11am,SUM(12pm+12pmn)12pm,SUM(1pm+1pmn)1pm,
                    SUM(2pm+2pmn)2pm,SUM(3pm+3pmn)3pm,SUM(4pm+4pmn)4pm,SUM(5pm+5pmn)5pm,SUM(6pm+6pmn)6pm,SUM(7pm+7pmn)7pm,SUM(8pm+8pmn)8pm,SUM(9pm+9pmn)9pm
                    FROM tbl_sr_activity_summary
                    WHERE act_date BETWEEN '$start_date' AND '$end_date'".$q2_g.$q_z."
                    GROUP BY act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1 ORDER BY act_date asc");
                return $data;

                //return $final;
            }
        }
        else if($reportType == "note_report"){
            $q1 = "";
            $q2 = "";
            $q3 = "";
            if($dirg_id !=""){
                $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
            }
            if ($zone_id != "") {
                $q1 = " AND t4.zone_id = '$zone_id'";
            }
            if ($sales_group_id != "") {
                $q2 = "AND t3.slgp_id = '$sales_group_id'";
            }
            $data = DB::connection($this->db)->select("SELECT
                    t1.note_date,IF(t7.lfcl_id=2,concat(t7.ntpe_name,'/','InActv'),t7.ntpe_name) as note_type, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body,
                    t5.edsg_name,concat(t6.site_code, ' - ', t6.site_name)   AS site_name,
                    replace(t1.geo_addr, ',' , '.' ) as geo_addr, TIME(t1.note_dtim) as n_time
                    FROM `tt_note` t1 
                    INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                    INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                    INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                    INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
                    LEFT JOIN tm_site t6 on t1.site_code=t6.site_code
                    LEFT JOIN tm_ntpe t7 on t1.ntpe_id=t7.id
                    WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
                        t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
                    ORDER BY t1.note_date, t3.slgp_code, t4.zone_code, t1.aemp_id desc");
            return $data;
        }
        
        else {

            $temp_s_list = "";
            $q1='';
            $q2='';
            if ($reportType == "sr_activity") {
                if($dirg_id !=""){
                    $q1=" AND t4.id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = "AND t4.id = '$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t3.id = '$sales_group_id'";
                }
                $temp_s_list="SELECT 
                t1.dhbd_date date,
                '' dirg_name,
                t4.zone_name zone_name,
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
                INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                WHERE  t1.dhbd_date BETWEEN '$start_date' AND '$end_date' AND t1.role_id=1 ". $q1. $q2 ."
                GROUP BY t2.zone_id,t4.zone_name,t1.dhbd_date,t3.slgp_name order by t4.zone_name asc";
                // $temp_s_list = "SELECT
                //   t1.date,
                //   t1.dirg_name,
                //   t1.zone_name,
                //   t1.slgp_name,
                //   t4.t_sr,
                //   t2.p_sr,
                //   if(isnull(t3.l_sr),0,t3.l_sr) As l_sr,
                //   if(isnull(t1.pro_sr),0,t1.pro_sr)AS pro_sr,
                //   if(isnull(t1.t_outlet),0,t1.t_outlet)AS t_outlet,
                //   if(isnull(t1.c_outlet),0,t1.c_outlet)AS c_outlet,
                //   if(isnull(t1.s_outet),0,t1.s_outet)AS s_outet,
                //   if(isnull(t1.lpc),0,t1.lpc)AS lpc,
                //   t1.t_amnt
                // FROM (SELECT
                //         t1.date,
                //         t1.zone_id,
                //         t2.slgp_name   AS slgp_name,
                //         t3.zone_name   AS zone_name,
                //         t3.dirg_name   AS dirg_name,
                //         t1.slgp_id,
                //         count(DISTINCT t1.aemp_id) as pro_sr,
                //         SUM(t1.t_outlet)                            AS t_outlet,
                //         (SUM(if(isnull(t1.s_outet),0,t1.s_outet))+SUM(if(isnull(t1.v_outlet),0,t1.v_outlet)))        AS c_outlet,
                //         SUM(t1.s_outet)                             AS s_outet,
                //         round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                //         round(SUM(t1.t_amnt), 2)                    AS t_amnt
                //       FROM `tt_aemp_summary` t1
                //         INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                //         INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                //       WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                //             t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
                //       GROUP BY t1.zone_id, t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                //   (SELECT
                //      t1.date,
                //      t1.zone_id,
                //      t1.slgp_id,
                //      count(DISTINCT t1.aemp_id) AS p_sr
                //    FROM tt_aemp_summary t1
                //      INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                //      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                //    WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                //          t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
                //    GROUP BY t1.zone_id, t1.slgp_id, t1.date) t2 ON t1.zone_id = t2.zone_id AND t1.slgp_id = t2.slgp_id AND t1.date = t2.date
                //   LEFT JOIN
                
                //   (SELECT
                //      t1.date,
                //      t1.zone_id,
                //      t1.slgp_id,
                //      count(DISTINCT t1.aemp_id) AS l_sr
                //    FROM tt_aemp_summary t1
                //      INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                //      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                //    WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                //          t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
                //    GROUP BY t1.zone_id, t1.slgp_id, t1.date) t3 ON t1.zone_id = t3.zone_id AND t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                //   INNER JOIN
                
                
                //   (SELECT
                //      t1.zone_id,
                //      t1.slgp_id,
                //      count(DISTINCT t1.id) AS t_sr
                //    FROM tm_aemp t1
                //      INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                //      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                //    WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                //          t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%'
                //           AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
                //    GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
            } else if ($reportType == "sr_productivity") {

                $q1 = "";
                $q2 = ""; 
                if($dirg_id !=""){
                    $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t1.zone_id = '$zone_id' ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                $temp_s_list = "SELECT
                          t1.date,
                          t1.zone_id,
                          t2.slgp_name   AS slgp_name,
                          t3.zone_name   AS zone_name,
                          t1.slgp_id                                  AS slgp_id,
                          t1.aemp_code                                AS aemp_id,
                          t1.aemp_name                                AS aemp_name,
                          t1.aemp_mobile                              AS aemp_mobile,
                          ifnull(t1.rout_name,'N/A')     AS rout_name,
                          ifnull(t1.t_outlet,0)                                 AS t_outlet,
                          ifnull((SUM(t1.v_outlet) + SUM(t1.s_outet)),0)        AS c_outlet,
                          ifnull(SUM(t1.s_outet),0)                             AS s_outet,
                          round(ifnull((SUM(t1.v_outlet) + SUM(t1.s_outet))*100/t1.t_outlet,0),2) AS c_percentage,
                          round(ifnull(SUM(t1.s_outet)*100/(SUM(t1.v_outlet) + SUM(t1.s_outet)),0),2) AS strikeRate,
                          round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                          round(SUM(t1.t_amnt), 2)                    AS t_amnt,
                          t1.inTime                                   AS inTime,
                          t1.firstOrTime                              AS firstOrTime,
                          t1.lastOrTime                               AS lastOrTime,
                          TIMEDIFF(t1.lastOrTime, t1.firstOrTime)     AS workTime
                        FROM `tt_aemp_summary` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                        WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet!='0' AND t2.aemp_id = '$empId' AND
                              t3.aemp_id = '$empId' " . $q1 . $q2. " and t2.acmp_id='$acmp_id'
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                        ORDER BY t2.slgp_code, t3.zone_code";
               
            } else if ($reportType == "sr_non_productivity") {

                $non_pr1 = "";
                $non_pr2 = "";
                if ($sales_group_id!=""){
                    $non_pr1 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                if($dirg_id !=""){
                    $non_pr2=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $non_pr2 = " and t1.zone_id='$zone_id' ";
                }
                $temp_s_list = "SELECT t1.date, t1.zone_id, concat(t2.slgp_code, ' - ', t2.slgp_name) AS slgp_name, concat(t3.zone_code, ' - ', t3.zone_name) AS zone_name,
                concat(t3.dirg_code, ' - ', t3.dirg_name) AS dirg_name, t1.slgp_id AS slgp_id, t1.aemp_code AS aemp_id, t1.aemp_name AS aemp_name, t1.aemp_mobile AS aemp_mobile,
                t1.inTime AS inTime FROM `tt_aemp_summary` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id 
                INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
                WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND (t1.t_amnt='0' OR t1.t_amnt IS NULL) 
                AND t3.aemp_id = '$empId' AND t1.atten_atyp='1' " . $non_pr1 . $non_pr2 . " AND t2.acmp_id = '$acmp_id' GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id 
                ORDER BY t3.zone_code, t2.slgp_code";

            } else if ($reportType == "sr_summary_by_group") {
                if($dirg_id !=""){
                    $q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $q = " AND t3.zone_id='$zone_id' ";
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
                      
                        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
                      
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
                      WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                            t2.slgp_id LIKE '%$sales_group_id%' ". $q." AND t2.acmp_id='$acmp_id'
                      GROUP BY t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                  (SELECT
                     t1.date,
                    
                     t1.slgp_id,
                     count(DISTINCT t1.aemp_id) AS p_sr
                   FROM tt_aemp_summary t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' ". $q." AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
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
                   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' ". $q." AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY t1.slgp_id, t1.date) t3 ON t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                  INNER JOIN
                
                
                  (SELECT
                     t1.zone_id,
                     t1.slgp_id,
                     count(DISTINCT t1.id) AS t_sr
                   FROM tm_aemp t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' ". $q."
                          AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY  t1.slgp_id) t4 ON t1.slgp_id = t4.slgp_id";

            }else if ($reportType == "market_outlet_sr_outlet") {
                /*$temp_s_list = "SELECT dsct_name, than_name, ward_name, mktm_name, mktm_id, sum(site) AS m_outlet_quantity, sum(de) AS sr_outlet_quantity FROM (SELECT t2.id AS mktm_id, t5.dsct_name, t4.than_name, t3.ward_name, t2.mktm_name, if(isnull(t1.site_code),0,1) AS site, if(isnull(t6.id),0,1) AS de FROM `tm_site` t1
  INNER JOIN tm_mktm t2 ON t1.mktm_id=t2.id
  INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
  INNER JOIN tm_than t4 ON t3.than_id=t4.id
  INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
  LEFT JOIN tl_rsmp t6 ON t1.id= t6.id
WHERE t4.id='165') AS ddd GROUP BY mktm_id";*/
                /*market outlet and sr outlet start */

                /*market outlet and sr outlet end */
                // if ($market_id!=""){
                //     $temp_s_list = "SELECT dsct_name, than_name, ward_name, concat(mktm_id,' - ',Replace(mktm_name,',','-')) as mktm_name, mktm_id, 
                //         sum(site) AS m_outlet_quantity, sum(de) AS sr_outlet_quantity
                //         FROM (SELECT t2.id AS mktm_id, t5.dsct_name, t4.than_name, t3.ward_name, t2.mktm_name, t1.site_code, t6.id, if(isnull(t1.site_code),0,1) AS site,
                //          if(isnull(t6.id),0,1) AS de
                //           FROM `tm_site` t1
                //           INNER JOIN tm_mktm t2 ON t1.mktm_id=t2.id
                //           INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
                //           INNER JOIN tm_than t4 ON t3.than_id=t4.id
                //           INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
                //           INNER JOIN tl_srth t7 ON t7.than_id= t4.id
                //           INNER JOIN tm_aemp t8 ON t8.id = t7.aemp_id
                //           INNER JOIN user_area_permission t9 ON t8.zone_id= t9.zone_id
                //           INNER JOIN user_group_permission t10 ON t8.slgp_id= t10.slgp_id
                //           LEFT JOIN tl_rsmp t6 ON t1.id= t6.id
                //         WHERE t8.slgp_id='$sales_group_id' and t9.aemp_id='$empId' and t10.aemp_id='$empId' and t2.id='$market_id' GROUP BY t1.site_code) AS ddd GROUP BY mktm_id";
                // }else{
                    $temp_s_list = "SELECT dsct_name, than_name, ward_name, concat(mktm_id,' - ',Replace(mktm_name,',','-')) as mktm_name, mktm_id, 
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
                        WHERE t8.slgp_id='$sales_group_id' and t9.aemp_id='$empId' and t10.aemp_id='$empId' AND t4.id='$than_id' GROUP BY t1.site_code) AS ddd GROUP BY mktm_id";
                //}


            } else if ($reportType == "sr_wise_order_delivery") {
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id='$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                } 
            //     $temp_s_list = "SELECT
            //   t2.acmp_name,
            //   t2.slgp_name,
            //   t3.zone_name,
            //   t3.dirg_name, 
            //   t1.`aemp_name`,
            //   t1.`aemp_usnm`,
            //   t1.`aemp_mob1`,
            //   t1.ordm_date,
            //   ROUND(SUM(t1.ordd_oamt),2) AS ordd_amt,
            //   ROUND(SUM(t1.ordd_odat),2) AS deli_amt
            // FROM `delivery_summary` t1
            //   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
            //   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
            // WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' AND t2.aemp_id = '$empId' AND
            //       t3.aemp_id = '$empId' " . $q1 . $q2 . $q3 . $q5 .
            //         "GROUP BY t1.aemp_id, t1.ordm_date";
                $temp_s_list="SELECT '$acmp_name' acmp_name,'$slgp_name' slgp_name,
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
                and (t1.ordm_date between '$start_date' AND '$end_date')
                            and t1.id=t2.ordm_id 
                ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                group by t5.`aemp_name`,
                    t5.`aemp_usnm`,
                    t5.`aemp_mob1`,
                    t1.ordm_date,
                    t6.zone_name";
               // return $temp_s_list;
            }
            else if($reportType=='zone_wise_order_delivery_summary'){
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t1.zone_id='$zone_id'";
                }
                $temp_s_list="SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`order_amount`) as ordd_oamt,
                sum(t1.`Delivery_Amount`) as ordd_Amnt FROM `s_mktm_zone_group_wise_order_vs_delivery` t1
               WHERE t1.ordm_date  BETWEEN '$start_date' AND '$end_date'". $q1. $zone_query . "AND t1.slgp_id=$sales_group_id
                     group by t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`";
            }
            else if ($reportType == "sku_wise_order_delivery") {  
            $zone_query='';
            if($dirg_id !=""){
                $zone_query=" AND t6.id IN (".implode(',',$zone_list).")";
            }
            if($zone_id !=''){
                $zone_query="AND t6.id='$zone_id'";
            }
            if ($sales_group_id != "") {
                $q2 = " AND t1.slgp_id = '$sales_group_id' ";
            }  
            $temp_s_list="SELECT '$acmp_name' acmp_name,'$slgp_name' slgp_name,
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
            and (t1.ordm_date between '$start_date' AND '$end_date')
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
            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            $srData = DB::connection($this->db)->select(DB::raw($temp_s_list));
            $emp_usnm = $this->currentUser->employee()->aemp_usnm;
            $emp_name = $this->currentUser->employee()->aemp_name;
            DB::connection($this->db)->select("INSERT INTO `tl_report_log`( `user_id`, `user_name`, `report_name`, `status`) VALUES('$emp_usnm','$emp_name','Common Report SR Activity','report')");
            return $srData;

        }

    }

// All report section end here

    public function classWiseReport(Request $request)
    {
        $empId='72024';
        $market_id='8400';
        $than_id='';
        $sales_group_id='33';
        $start_date= '2021-11-25';
        $end_date= '2021-11-25';
        $zone_id='';
        $sales_group_id='33';
        $q1='';
        $q2='';
        if ($zone_id != "") {

            $q1 = " AND t1.zone_id = '$zone_id' ";
        }
        if ($sales_group_id != "") {

            $q2 = " AND t1.slgp_id = '$sales_group_id' ";
        }
        $acmp_id='7';


        $temp_s_list = "SELECT
                          t1.date,
                          t1.zone_id,
                          concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
                          concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
                          concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
                          t1.slgp_id                                  AS slgp_id,
                          t1.aemp_code                                AS aemp_id,
                          t1.aemp_name                                AS aemp_name,
                          t1.aemp_mobile                              AS aemp_mobile,
                          t1.inTime                                   AS inTime
                        FROM `tt_aemp_summary` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                        WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND (t1.t_amnt='0' OR t1.t_amnt is NULL) AND 
                              t3.aemp_id = '$empId' AND t1.atten_atyp='1' " . $q2 . $q1 . " and t2.acmp_id = '$acmp_id'
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                        ORDER BY t3.zone_code, t2.slgp_code";
        echo $temp_s_list;


    }
    public function getTrackingRecordGvt(){
        $date=date('Y-m-d');
        $empId = $this->currentUser->employee()->id;
        $slgp = DB::connection($this->db)->select("SELECT DISTINCT slgp_id,slgp_name FROM `user_group_permission` WHERE `aemp_id`='$empId'  order by slgp_id");
        $slgp_id=[];
        $temp_v='';
        $q2='';
        $q1='';
        for($i=0;$i<count($slgp);$i++){
            $slgp_id[$i]=$slgp[$i]->slgp_id;
            $temp_v=$slgp_id[$i];
            $slgp_name=$slgp[$i]->slgp_name;
            $q2.=",sum(if(t1.slgp='$temp_v',t_visited,0))`slgpv$i`,sum(if ( t1.slgp='$temp_v',t_memo,0))`slgpm$i`";
            
            $q1.=",sum(slgpv$i) as `slgpv$i`,sum(slgpm$i) as`slgpm$i`";
        }
        $data=DB::connection($this->db)->select("select tt.dsct_id,dsct_name,tl.TOTL".$q1." from(
            SELECT dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name".$q2."
            FROM s_mktm_zone_group_wise_vist_memo as t1 where t1.slgp IN (".implode(',',$slgp_id).")
            group by dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name
            ) as tt,
            (select dsct_id, sum(TOTL) totl from(
            select dsct_id, mktm_id,  MAX(t_outlet) TOTL FROM s_mktm_zone_group_wise_vist_memo GROUP BY mktm_id,dsct_id) as tl
            group by dsct_id) AS tl
            WHERE tt.dsct_id=tl.dsct_id
            group by tt.dsct_id,dsct_name,tl.TOTL ORDER BY dsct_name ASC
        ");  
        return array('data'=>$data,'slgp'=>$slgp);


    }
  

    public function getGvtDeeperSalesData(Request $request){
        $stage=$request->stage;
        $id=$request->id;
        $empId = $this->currentUser->employee()->id;
        $slgp = DB::connection($this->db)->select("SELECT DISTINCT slgp_id,slgp_name FROM `user_group_permission` WHERE `aemp_id`='$empId'   order by slgp_id");
        $slgp_id=[];
        $temp_v='';
        $q2='';
        $q1='';
        for($i=0;$i<count($slgp);$i++){
            $slgp_id[$i]=$slgp[$i]->slgp_id;
            $temp_v=$slgp_id[$i];
            $q2.=",sum(if(t1.slgp='$temp_v',t_outlet,0))`slgpo$i`,sum(if(t1.slgp='$temp_v',t_visited,0))`slgpv$i`,sum(if ( t1.slgp='$temp_v',t_memo,0))`slgpm$i`";
            
            $q1.=",sum(slgpo$i) as `slgpo$i`,sum(slgpv$i) as `slgpv$i`,sum(slgpm$i) as`slgpm$i`";
        }
        if($stage==1){
            $data=DB::connection($this->db)->select("select tt.than_id,tt.than_name,tl.TOTL".$q1."from(
                SELECT dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name".$q2."
                 FROM s_mktm_zone_group_wise_vist_memo as t1  where t1.slgp IN(".implode(',',$slgp_id).") and t1.dsct_id='$id'
                group by dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name
                ) as tt,
                (select than_id, sum(TOTL) totl from(
                select dsct_id,than_id, mktm_id,  MAX(t_outlet) TOTL FROM s_mktm_zone_group_wise_vist_memo GROUP BY mktm_id,dsct_id,than_id) as tl
                group by than_id) AS tl
                WHERE tt.than_id=tl.than_id
                group by tt.dsct_id,tt.dsct_name,tl.TOTL,tt.than_id,tt.than_name ORDER BY tt.than_name ASC");
        }
        if($stage==2){
            $data=DB::connection($this->db)->select("select tt.ward_id,tt.ward_name,tl.TOTL".$q1."from(
                SELECT dsct_id,dsct_name,t1.ward_id ,t1.ward_name ,t1.mktm_id,mktm_name".$q2."
                 FROM s_mktm_zone_group_wise_vist_memo as t1  where t1.slgp IN(".implode(',',$slgp_id).") and t1.than_id ='$id'
                group by dsct_id,dsct_name,t1.ward_id,t1.ward_name,t1.mktm_id,mktm_name
                ) as tt,
                (select ward_id, sum(TOTL) totl from(
                select dsct_id,ward_id, mktm_id,  MAX(t_outlet) TOTL FROM s_mktm_zone_group_wise_vist_memo GROUP BY mktm_id,dsct_id,ward_id) as tl
                group by ward_id) AS tl
                WHERE tt.ward_id=tl.ward_id
                group by tt.dsct_id,tt.dsct_name,tl.TOTL,tt.ward_id,tt.ward_name ORDER BY tt.ward_name ASC
                ");
        }
        if($stage==3){
            $data=DB::connection($this->db)->select("select tt.mktm_id,tt.mktm_name,tl.TOTL".$q1."  from(
                SELECT dsct_id,dsct_name,t1.ward_id ,t1.ward_name ,t1.mktm_id,mktm_name".$q2."
                 FROM s_mktm_zone_group_wise_vist_memo as t1  where t1.slgp IN(".implode(',',$slgp_id).") and t1.ward_id='$id'
                group by dsct_id,dsct_name,t1.ward_id,t1.ward_name,t1.mktm_id,mktm_name
                ) as tt,
                (select mktm_id,  MAX(t_outlet) TOTL FROM s_mktm_zone_group_wise_vist_memo GROUP BY mktm_id) AS tl
                WHERE tt.mktm_id=tl.mktm_id
                group by mktm_id,mktm_name,tl.TOTL ORDER BY tt.mktm_name
                ");
        }
        if($stage==4){
            $data="";
        }
        
        return array('data'=>$data,
                     'slgp'=>$slgp);

    }
    public function getDeviationData(){
        $date=date('Y-m-d');
        $emp_id=Auth::user()->employee()->id;
        $sql="SELECT t1.acmp_id,t1.acmp_name,COUNT(DISTINCT t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.acmp_id=t3.acmp_id
            WHERE t3.aemp_id=$emp_id
            GROUP BY t1.acmp_id,t1.acmp_name ORDER BY t1.acmp_name ASC";
       // return $sql;
        $data = DB::connection($this->db)->select(DB::raw($sql));
        return $data;
    }
    public function getDeviationDeeperData(Request $request){
        $date=date('Y-m-d');
        $id=$request->id;
        $emp_id=Auth::user()->employee()->id;
        $stage=$request->stage;
        if($stage==0){

        }
        else if($stage==1){
            $date=$request->date;
            $sql="SELECT t1.slgp_id,t1.slgp_name,COUNT(DISTINCT t1.rout_id) t_rout,count( DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.slgp_id=t3.slgp_id
            WHERE t3.acmp_id=$id AND t3.aemp_id=$emp_id
            GROUP BY t1.slgp_id,t1.slgp_name ORDER BY t1.slgp_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==2){
            $date=$request->date;
            $sql="SELECT t1.zone_id,t1.zone_name,t1.slgp_id,COUNT(DISTINCT t1.rout_id) t_rout,count( DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
            WHERE t1.slgp_id='$id' AND t3.aemp_id=$emp_id
            GROUP BY t1.zone_id,t1.zone_name,t1.slgp_id ORDER BY t1.zone_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==3){
            $date=$request->date;
            $slgp_id=$request->slgp_id;
            $sql="SELECT t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN tm_aemp t3 ON t3.id=t1.aemp_id 
            WHERE  t1.zone_id='$id' AND t1.slgp_id='$slgp_id'
            GROUP BY t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1 ORDER BY t3.aemp_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
    }
    public function getDateWiseDeviationData(Request $request,$stage=0){
        $date=$request->date;
        $id=$request->id;
        $emp_id=Auth::user()->employee()->id;
        if($stage==0){
            $sql="SELECT t1.acmp_id,t1.acmp_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.slgp_id=t3.slgp_id
            WHERE  t3.aemp_id=$emp_id
            GROUP BY t1.acmp_id,t1.acmp_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==1){
            $date=$request->date;
            $sql="SELECT t1.slgp_id,t1.slgp_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            WHERE  t1.acmp_id='$id'
            GROUP BY t1.slgp_id,t1.slgp_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==2){
            $date=$request->date;
            $sql="SELECT t1.zone_id,t1.zone_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
            WHERE t1.slgp_id='$id' AND t3.aemp_id=$emp_id
            GROUP BY t1.zone_id,t1.zone_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;

        }
        else if($stage==3){
            $date=$request->date;
            $sql="SELECT t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN tm_aemp t3 ON t3.id=t1.aemp_id 
            WHERE  t1.zone_id='$id'
            GROUP BY t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
    }
    public function getCompany(Request $request){
        $emid=Auth::user()->employee()->id;
        $acmp_list=DB::connection($this->db)->select("Select acmp_id,acmp_name FROM user_group_permission where aemp_id='$emid' GROUP BY acmp_id order by acmp_name");
        return $acmp_list;
    }
    public function getDeviationOutletVisit(Request $request,$stage=0){
        $emp_id=Auth::user()->employee()->id;
        $date=$request->date;
        if($date==''){
            $date=date('Y-m-d');
        }
        if($stage==0){
            $data=DB::connection($this->db)->select("SELECT t3.acmp_id as slgp_id,t3.acmp_name as slgp_name,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t3.aemp_id=$emp_id
            GROUP BY t3.acmp_id,t3.acmp_name order by t3.acmp_name asc");
        }
        else if($stage==1){
            $id=$request->id;
            $data=DB::connection($this->db)->select("SELECT t3.slgp_id as slgp_id,t3.slgp_name as slgp_name,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t3.aemp_id=$emp_id AND t3.acmp_id=$id
            GROUP BY t3.slgp_id,t3.slgp_name order by t3.acmp_name asc");
        }
        else if($stage==2){
            $id=$request->id;
            $data=DB::connection($this->db)->select("SELECT t4.zone_id zone_id,t4.zone_name zone_name,t3.slgp_id,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            INNER JOIN user_area_permission t4 ON t2.zone_id=t4.zone_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t2.slgp_id='$id' AND t3.aemp_id=$emp_id AND t4.aemp_id=$emp_id
            GROUP BY t4.zone_id,t4.zone_name,t3.slgp_id ORDER BY t4.zone_name asc");
        }
        else if($stage==3){
            $id=$request->id;
            $slgp_id=$request->slgp_id;
            $data=DB::connection($this->db)->select("SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_slgp t3 on t3.id=t2.slgp_id
            INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t4.id='$id' AND t2.slgp_id='$slgp_id'
            GROUP BY t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 ORDER BY t2.aemp_name ASC");
        }
        return $data;
        //return $stage;
    }
    public function getNoteTaskReport(){
        $date=date('Y-m-d');
        $emid=Auth::user()->employee()->id;
        $data=DB::connection($this->db)->select("SELECT 
            t1.id,t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1,ifnull(t2.t_note,0)t_note,
            (select ifnull(sum(t_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')assign_task,
            (select ifnull(sum(u_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')tm_task
            FROM tm_aemp t1
            LEFT JOIN (SELECT t_note,aemp_id FROM tbl_note_count WHERE note_date='$date')t2 ON t1.id=t2.aemp_id
            WHERE  t1.id in(  SELECT
                t1.id from tm_aemp t1 WHERE t1.aemp_mngr=$emid
                UNION ALL
                SELECT
                t2.dhem_peid FROM th_dhem t2
                WHERE t2.dhem_emid =$emid) ORDER BY t1.aemp_name ASC");
        return $data;
    }
    public function getUserAndDateWiseTaskNote(Request $request){
        $emid=$request->emid;
        if($emid==''){
            $emid=Auth::user()->employee()->id;
        }
        $date=$request->date;
        $data=DB::connection($this->db)->select("SELECT t1.id,t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1,ifnull(t2.t_note,0)t_note,
        (select ifnull(sum(t_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')assign_task,
        (select ifnull(sum(u_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')tm_task
        FROM tm_aemp t1
        LEFT JOIN (SELECT t_note,aemp_id FROM tbl_note_count WHERE note_date='$date')t2 ON t1.id=t2.aemp_id
        WHERE  t1.id in(SELECT
            t1.id from tm_aemp t1 WHERE t1.aemp_mngr=$emid
            UNION ALL
            SELECT
            t2.dhem_peid FROM th_dhem t2 WHERE t2.dhem_emid =$emid) ORDER BY t1.aemp_name ASC
        ");
        return $data;
    }
    public function getTaskDetails($id,$date){
        $data=DB::connection($this->db)->select("SELECT 
            t1.note_titl,t1.note_body,t1.note_dtim,t1.note_date,t2.nimg_imag,t3.site_name
            FROM tt_note t1
            LEFT JOIN tl_nimg t2 ON t1.id=t2.note_id
            LEFT JOIN tm_site t3 ON t1.site_code=t3.site_code
            WHERE t1.aemp_id=$id AND t1.note_date='$date' ORDER BY t1.note_dtim DESC");
        return view('report.summary_report.note_task_details')->with('data',$data);
    }
    public function getSrMovementSummary(){
        $emp_id=Auth::user()->employee()->id;
        $data=DB::connection($this->db)->select("SELECT t1.acmp_id,t1.acmp_name,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
        SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
        FROM tbl_sr_activity_summary t1
        INNER JOIN tm_aemp t3 On t1.id=t3.id
        INNER JOIN user_group_permission t2 ON t3.slgp_id=t2.slgp_id
        WHERE t1.act_date=curdate()-INTERVAL 1 DAY AND t2.aemp_id='$emp_id' and t3.role_id=1
        GROUP BY t1.acmp_id,t1.acmp_name ORDER BY t1.acmp_name ASC");
        return $data;
    }
    public function getSrMovementSummaryDeeparData(Request $request){
        $id=$request->id;
        $stage=$request->stage;
       // $date=Carbon::yesterday()->format('Y-m-d');
       $emp_id=Auth::user()->employee()->id;
        $date=Carbon::now()->subDays(1)->format('Y-m-d');
       $data='';
        if($stage==1){
            $data=DB::connection($this->db)->select("SELECT t1.slgp_id,t1.slgp_name,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
            SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
                FROM tbl_sr_activity_summary t1
                INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                WHERE t1.act_date='$date' AND t1.acmp_id=$id AND t2.aemp_id=$emp_id
                GROUP BY t1.slgp_id,t1.slgp_name ORDER BY t1.slgp_name ASC;");
        }
        //start from here next day
        else if($stage==2){
            $data=DB::connection($this->db)->select("SELECT t1.zone_id,t1.zone_name,t1.slgp_id,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
            SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
                FROM tbl_sr_activity_summary t1
                INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
                WHERE act_date='$date' AND t1.slgp_id=$id AND t2.aemp_id=$emp_id AND t3.aemp_id=$emp_id
                GROUP BY t1.zone_id,t1.zone_name ORDER BY t1.zone_name ASC;");
        }
        else if($stage==3){
            $slgp_id=$request->slgp_id;
            $data=DB::connection($this->db)->select("SELECT aemp_usnm,aemp_name,SUM(9am+10am+11am+12pm+9amn+10amn+11amn+12pmn)1st_slot,SUM(1pm+1pmn)2nd_slot,
                SUM(2pm+3pm+4pm+5pm+6pm+2pmn+3pmn+4pmn+5pmn+6pmn)3rd_slot,SUM(7pm+8pm+9pm+7pmn+8pmn+9pmn)4th_slot
                FROM tbl_sr_activity_summary
                WHERE act_date='$date' AND zone_id=$id AND slgp_id='$slgp_id'
                GROUP BY aemp_usnm,aemp_name ORDER BY aemp_name ASC;");
        }
        else{
            $data='Bad';
        }
        return $data;

    }
    public function getCatWiseOutVisit($id,$date){
        if($date==''){
            $date=date('Y-m-d');
        }
        $this->db = Auth::user()->country()->cont_conn;
        $sr_list=DB::connection($this->db)->select("SELECT  
            t4.id,t4.otcg_name,
            sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
            FROM  th_ssvh t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_site t3 ON t1.site_id=t3.id
            INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
            WHERE t2.aemp_mngr=$id  AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0)
            GROUP BY t4.id");
        return $sr_list;
    }
    public function getVisitedOutletDetails($emid,$date,$cat_id){
       // return $emid."--".$date."--cat".$cat_id;
        $out_list=DB::connection($this->db)->select("SELECT  t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t2.aemp_mngr=$emid  AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0) AND t4.id=$cat_id
                ORDER BY t3.site_name ASC");
        return $out_list;
    }
    public function getSRList(Request $request){
        $id=$request->id;
        if($request->type==1){
            $data = DB::connection($this->db)->select("SELECT `aemp_usnm`,`aemp_name`,`id` FROM `tm_aemp` WHERE  `slgp_id`='$id' AND lfcl_id='1' AND role_id='1' AND aemp_issl=1");
        }else{
            $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.aemp_name,t1.id
            FROM tm_aemp t1
            INNER JOIN tl_srth t2 ON t1.id=t2.id
            WHERE t1.lfcl_id=1 AND t1.aemp_issl=1 AND t2.than_id=$id");
        }
        return $data;
         
    }
    public function getHistoryReportData(Request $request){
        $id=$request->sr_id;
        $time_period=$request->time_period;
        if($time_period){ 
            if($time_period==0){
                $start_date= Carbon::now()->format('Y-m-d');
                $end_date= Carbon::now()->format('Y-m-d');
            }
            else if($time_period==1){
                $start_date= Carbon::yesterday()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==2){
                $start_date= Carbon::now()->subDays(3)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==3){
                $start_date= Carbon::now()->subDays(7)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==4){
                $start_date= Carbon::now()->subDays(30)->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==5){
                $start_date= Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else{
                $start_date=$time_period;
                $end_date=$time_period;
            }   
        }else{
            $start_date= Carbon::now()->format('Y-m-d');
            $end_date= Carbon::now()->format('Y-m-d');
        }
        $data=DB::connection($this->db)->select("SELECT 
        t2.dhbd_date,t1.id,t1.aemp_usnm,t1.aemp_name,
        t4.rout_name rout_id,t3.site rout_olt,
        t2.dhbd_tvit t_visit,t2.dhbd_memo t_memo,
        format(ifnull((t2.dhbd_memo / t2.dhbd_tsit) * 100, 0), 2) AS strikeRate,
        round(ifnull(t2.dhbd_line / t2.dhbd_memo,0), 2)                     AS lpc,
        round(t2.dhbd_tamt/1000	,2)									 AS t_amnt
        FROM tm_aemp  t1
        INNER JOIN th_dhbd_5 t2 ON t2.aemp_id=t1.id
        INNER JOIN tbl_rout_site_count t3 ON t3.rpln_day=DAYNAME(t2.dhbd_date) AND t3.aemp_id=t2.aemp_id
        inner join tm_rout t4 ON t3.rout_id=t4.id
        WHERE t1.id=$id AND t2.dhbd_date between '$start_date' AND '$end_date'
        ");
        return $data;
    }
    public function getWardWiseVisitDetails($id,$date){
        $data1=DB::connection($this->db)->select("SELECT 
                t3.id,t1.aemp_id,t1.ssvh_date,t3.mktm_name,count(DISTINCT t1.site_id) t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t3.id,t1.aemp_id,t3.mktm_name ORDER BY t3.mktm_name ASC;");
        $data2=DB::connection($this->db)->select("SELECT 
                t1.ssvh_date,t5.id,t1.aemp_id,t5.than_name,count(DISTINCT t1.site_id)t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t1.ssvh_date,t5.id,t1.aemp_id,t5.than_name
                ORDER BY t5.than_name ASC");
        $data3=DB::connection($this->db)->select("SELECT 
                t4.id,t1.aemp_id,t1.ssvh_date,t4.ward_name,count(DISTINCT t1.site_id) t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t4.id,t1.aemp_id,t1.ssvh_date,t4.ward_name ORDER BY t4.ward_name ASC;");
        $data4=DB::connection($this->db)->select("SELECT  
                t4.id,t4.otcg_name,t1.aemp_id,t1.ssvh_date,
                sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0)
                GROUP BY t4.id,t4.otcg_name");
        return array('data1'=>$data1,'data2'=>$data2,'data3'=>$data3,'data4'=>$data4);

    }
    public function showWardWiseVisitOutletDetails(Request $request){
        $data='';
        $id=$request->id;
        $date=$request->date;
        $type=$request->type;
        $stage=$request->stage;
        if($type==1){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t3.id=$stage
                ORDER BY t2.site_name ASC");
        }
        else if($type==2){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t4.id=$stage
                ORDER BY t2.site_name ASC");
        }
        else if($type==3){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t5.id=$stage
                ORDER BY t2.site_name ASC");
        }
        return $data;
    }
    public function showCatWiseOutlet(Request $request){
        $id=$request->id;
        $date=$request->date;
        $cat_id=$request->cat_id;
        $data=DB::connection($this->db)->select("SELECT  
                t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0) AND t4.id=$cat_id
               Order By t3.site_name ASC ");
        return $data;
    }
    public function getMapData(Request $request){
        $sr_id=$request->sr_id;
        $date=$request->date;
        $place=$request->place;
        // $data=DB::connection($this->db)->select("SELECT t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon FROM th_ssvh t1
        // INNER JOIN tm_site t2 ON t1.site_id=t2.id
        // WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.created_at asc;");
      //  return $data;
        // $data1=DB::connection("SELECT '' site_name,t1.hloc_addr 'site_adrs',t1.geo_lat,t1.geo_lon FROM th_hloc t1
        //  WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date' ORDER BY t1.created_at asc");
        //  $q1="SELECT t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon FROM th_ssvh t1
        //  INNER JOIN tm_site t2 ON t1.site_id=t2.id
        //  WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.created_at asc";
        //  $q2="SELECT '' site_name,t1.hloc_addr 'site_adrs',t1.geo_lat,t1.geo_lon FROM th_hloc t1
        //  WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date' ORDER BY t1.created_at asc";....
        // return "SELECT site_name,site_adrs,geo_lat,geo_lon,log_time FROM 
        // (
        // (SELECT t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon,t1.created_at,t1.attr1 log_time FROM th_ssvh t1
        //  INNER JOIN tm_site t2 ON t1.site_id=t2.id
        //  WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.created_at asc)
        // UNION ALL
        // (SELECT '' site_name,t1.hloc_addr 'site_adrs',max(t1.geo_lat),max(t1.geo_lon),min(t1.created_at),max(t1.hloc_time) log_time FROM th_hloc t1
        // WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date'  group by t1.hloc_addr,site_name ORDER BY t1.created_at asc))p 
        // ORDER BY p.created_at ASC";
        $data=DB::connection($this->db)->select("SELECT site_name,site_adrs,geo_lat,geo_lon,log_time FROM 
        (
        (SELECT t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon,t1.created_at,t1.attr1 log_time FROM th_ssvh t1
         INNER JOIN tm_site t2 ON t1.site_id=t2.id
         WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.created_at asc)
        UNION ALL
        (SELECT '' site_name,t1.hloc_addr 'site_adrs',max(t1.geo_lat),max(t1.geo_lon),min(t1.created_at),min(t1.hloc_time) log_time FROM th_hloc t1
        WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date'  group by t1.hloc_addr,site_name ORDER BY t1.created_at asc))p 
        ORDER BY p.created_at ASC");
        return $data;

    }
    public function showAllVisitedOutletList(Request $request){
        $id=$request->id;
        $date=$request->date;
        $out_list=DB::connection($this->db)->select("SELECT  t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                WHERE  t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0) AND t1.aemp_id=$id
                ORDER BY t3.site_name ASC");
        return $out_list;
    }



    // Employee Attendance Report
    public function getEmpAttendanceReport(Request $request){
        $emid=$request->emid;
        $date=$request->date;
        if($date==''){
            $date=date('Y-m-d');
        }
        $un_emp=DB::connection($this->db)->select("SELECT
                t4.id 									   AS id,
                t4.aemp_usnm,                              
                t4.aemp_name,
                t4.aemp_mob1,                              
                t1.role_id,
                t5.zone_name,                                
                t2.role_name ,
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr                               
                FROM th_dhbd_5 AS t1
                INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                LEFT JOIN th_dhbd_5 AS t3
                    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
                INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
                INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
                WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date ='$date'
                UNION ALL
                SELECT
                t4.id 									   AS id,
                t4.aemp_usnm,                                
                t4.aemp_name,
                t4.aemp_mob1,                             
                t1.role_id, 
                t5.zone_name,                                
                t2.role_name ,
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr   
                FROM th_dhbd_5 AS t1
                INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                LEFT JOIN th_dhbd_5 AS t3
                    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
                INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
                INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
                INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
                WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date ='$date'");
        return $un_emp;
    }

    public function expEmpAttendanceData(Request $request){
        $date=$request->date;
        $id=$request->emid;
        $type=$request->type;
        if($date==''){
            $date=date('Y-m-d');
        }    
        $this->db = Auth::user()->country()->cont_conn;
        $role_id=DB::connection($this->db)->select("SELECT role_id from tm_aemp where id=$id");
        $role_id=$role_id[0]->role_id;
        $mngr_list='';
        $list=$id;
        if($role_id<=5 && $role_id>2){
            while($list){
                $list_to_check=DB::connection($this->db)->select("Select aemp_id,role_id from th_dhbd_5 where aemp_mngr in ($list)");
                $list='';
                $cnt=0;
                $cnt1=0;
                foreach($list_to_check as $s){
                    if($s->role_id <$role_id){
                        if($s->role_id==2){
                            if($cnt==0){
                                $mngr_list.=$s->aemp_id;
                            }
                            else{
                                $mngr_list.=','.$s->aemp_id;
                            }
                            $cnt++;
                        }else if($s->role_id>2){
                            if($cnt1==0){
                                $list.=$s->aemp_id;
                                
                            }else{
                                $list.=','.$s->aemp_id;
                            }
                            $cnt1++;
                            
                        }
                    }
                }
            }
            
        }else{
            $mngr_list=$id;
        }
        $heading=['Emp_Name','Staff_Id','Mobile_Num','Zone_Name','Role_ID'];
        $query='';
        $file_name='';
        if($type==1){
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  FROM `th_dhbd_5` t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
             AND t1.role_id=1 AND t2.lfcl_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
            $file_name='total_sr_list.xlsx';
        }
        else if($type==2){
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  FROM `th_dhbd_5` t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
            AND  t1.dhbd_prnt=1 AND t1.role_id=1  AND t2.lfcl_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
             $file_name='present_sr_list.xlsx';
        }
        else{
            
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name ,t2.role_id FROM `th_dhbd_5` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
                WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
                AND   (t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`))>0 AND t1.role_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
             $file_name='off_sr_list.xlsx';
        }
        return Excel::download(new RequestReportData($query,$this->db,$heading), $file_name);
        
    }
}