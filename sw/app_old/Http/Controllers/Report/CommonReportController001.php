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


    public function weeklyorderSummaryReport()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
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
        return view('report.summary_report.common_report',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId]);
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

public function orderSummaryReportFilter(Request $request)
    {
        //return $request->all();
        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;
        $outletType = $request->outletType;
        $outletAType = $request->outletAType;
        $srTypewithsr = $request->srType;
        $classType = $request->classType;
        $zone_id = $request->zone_id;
        $dirg_id = $request->region_id;
        $sales_group_id = $request->sales_group_id;
    
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->comp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;
        $ward_id = $request->ward_id;
        $market_id = $request->market_id;
        $start_date_d = $request->start_date_d;
        $end_date_d = $request->end_date_d;
        $sr_id = $request->sr_id;
        $site_id = $request->outlet_id;
        $q1 = "";
        $q2 = "";
        $q3 = "";
        $q5 = "";
        $q6 = "";
        $q7 = "";
        $q8 = "";
        $q9 = "";
        $q10 = "";

        if ($zone_id != "") {

            $q1 = " AND t1.zone_id = '$zone_id'";
        }
        if ($sales_group_id != "") {

            $q2 = " AND t1.slgp_id = '$sales_group_id'";
        }

        if ($dirg_id != "") {

            $q3 = " AND t1.dirg_id = '$dirg_id'";
        }

        if ($dist_id != "") {

            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {

            $q6 = " AND t1.than_id = '$than_id'";
        }

        if ($ward_id != "") {

            $q7 = " AND t1.ward_id = '$ward_id'";
        }
        if ($market_id != "") {

            $q8 = " AND t1.mktm_id = '$market_id'";
        }
        if ($sr_id != "") {
            $q9 = " AND t1.aemp_id='$sr_id'";
        }
        if ($site_id != "") {
            $q10 = " AND t1.site_id='$site_id'";
        }


        if ($outletAType == "issg") {
            $q4 = " AND t1.site_sign='1' ";
        } else if ($outletAType == "isfg") {
            $q4 = " AND t1.site_refi='1' AND t1.site_code like '66%' ";
        } else {
            $q4 = " ";
        }
        //w_item

        if ($reportType == "b_sr") {
            if ($outletType == "olt") {
                if ($classType == "w_class") {
                    $query = "SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`, t1.`site_code`, t1.`site_name`, t1.`site_mob1`, t1.`itcl_name`, sum(t1.`ordd_oamt`) as ordd_oamt,
  sum(t1.`ordd_qnty`) as ordd_qnty FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . $q9 . " group by t1.aemp_id, t1.site_id, t1.itcl_id";
                } else {
                    $query = "SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`, t1.`site_code`, t1.`site_name`, t1.`site_mob1`, t1.`itcl_name`, t1.amim_id, t1.amim_code, t1.amim_name,
   sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
 FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . $q9 . " group by t1.aemp_id,t1.site_id, t1.amim_id";
                }

            } else {
                if ($classType == "w_class") {
                    $query = "SELECT t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`,  t1.`itcl_name`, t1.amim_code, t1.amim_name, sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . $q9 . " group by t1.aemp_id, t1.itcl_id";
                } else {
                    $query = "SELECT t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`,  t1.`itcl_name`,t1.amim_id, t1.amim_code, t1.amim_name, sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q1 . $q9 . " group by t1.aemp_id, t1.amim_id";
                }

            }

        }
        if ($reportType == "d_outlet") {
            if ($srTypewithsr == "wsr") {
                if ($classType == "w_class") {
                    $query = "SELECT
                      t1.`dsct_name` as dsct_name,
                      t1.`than_name` as than_name,
                      t1.`ward_name` as ward_name,
                      t1.`mktm_name` as mktm_name,
                      t1.`site_code` as site_code,
                      t1.`site_name` as site_name,
                      t1.`site_mob1` as site_mob1,
                      t1.`aemp_name` as aemp_name,
                      t1.`aemp_usnm` as aemp_usnm,
                      t1.`aemp_mob1` as aemp_mob1,
                      t1.`itcl_name` as itcl_name,
                      t1.`itcl_code` as itcl_code,
                      SUM(t1.ordd_qnty) AS ordd_qnty,
                      SUM(t1.ordd_oamt) AS ordd_oamt
                    FROM `order_summary` t1
                      INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t1.ordm_date >= '$start_date_d' AND t1.ordm_date <= '$end_date_d' AND t2.aemp_id = '$empId' AND
                          t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . $q10 . " GROUP BY t1.aemp_id, t1.site_id, t1.itcl_id";
                } else {
                    $query = "SELECT
                          t1.`dsct_name` as dsct_name,
                          t1.`than_name` as than_name,
                          t1.`ward_name` as ward_name,
                          t1.`mktm_name` as mktm_name,
                          t1.`site_code` as site_code,
                          t1.`site_name` as site_name,
                          t1.`site_mob1` as site_mob1,
                          t1.`aemp_name` as aemp_name,
                          t1.`aemp_usnm` as aemp_usnm,
                          t1.`aemp_mob1` as aemp_mob1,
                          t1.`itcl_name` as itcl_name,
                          t1.`itcl_code` as itcl_code,
                          t1.`amim_code` as amim_code,
                          t1.`amim_name` as amim_name,
                          SUM(t1.ordd_qnty) AS ordd_qnty,
                          SUM(t1.ordd_oamt) AS ordd_oamt
                        FROM `order_summary` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                        WHERE t1.ordm_date >= '$start_date_d' AND t1.ordm_date <= '$end_date_d' AND t2.aemp_id = '$empId' AND
                              t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . $q10 . " GROUP BY t1.aemp_id, t1.site_id, t1.amim_id";

                }
            } else {
                if ($classType == "w_class") {
                    $query = "SELECT
                      t1.`dsct_name` as dsct_name,
                      t1.`than_name` as than_name,
                      t1.`ward_name` as ward_name,
                      t1.`mktm_name` as mktm_name,
                      t1.`site_code` as site_code,
                      t1.`site_name` as site_name,
                      t1.`site_mob1` as site_mob1,
                      
                      t1.`itcl_name` as itcl_name,
                      t1.`itcl_code` as itcl_code,
                      SUM(t1.ordd_qnty) AS ordd_qnty,
                      SUM(t1.ordd_oamt) AS ordd_oamt
                    FROM `order_summary` t1
                      INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t1.ordm_date >= '$start_date_d' AND t1.ordm_date <= '$end_date_d' AND t2.aemp_id = '$empId' AND
                          t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . $q10 . " GROUP BY t1.site_id, t1.itcl_id";
                } else {
                    $query = "SELECT
                          t1.`dsct_name` as dsct_name,
                          t1.`than_name` as than_name,
                          t1.`ward_name` as ward_name,
                          t1.`mktm_name` as mktm_name,
                          t1.`site_code` as site_code,
                          t1.`site_name` as site_name,
                          t1.`site_mob1` as site_mob1,
                        
                          t1.`itcl_name` as itcl_name,
                          t1.`itcl_code` as itcl_code,
                          t1.`amim_code` as amim_code,
                          t1.`amim_name` as amim_name,
                          SUM(t1.ordd_qnty) AS ordd_qnty,
                          SUM(t1.ordd_oamt) AS ordd_oamt
                        FROM `order_summary` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                        WHERE t1.ordm_date >= '$start_date_d' AND t1.ordm_date <= '$end_date_d' AND t2.aemp_id = '$empId' AND
                              t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . $q10 . " GROUP BY t1.site_id, t1.amim_id";

                }
            }
        }

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        $emp_usnm = $this->currentUser->employee()->aemp_usnm;
        $emp_name = $this->currentUser->employee()->aemp_name;
        DB::connection($this->db)->select("INSERT INTO `tl_report_log`( `user_id`, `user_name`, `report_name`, `status`) VALUES('$emp_usnm','$emp_name','OrdSummary','report')");
        return $srData;
    }

    public function loadMarketWiseOutlet(Request $request)
    {
        $mkt_id = $request->market_id;
        $outlets = DB::connection($this->db)->select("SELECT id, site_code,site_name  from tm_site  where tm_site.mktm_id='$mkt_id'");
        return $outlets;

    }

    public function commonReportFilter(Request $request)
    {
        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;

        $zone_id = $request->zone_id;
        $dirg_id = $request->region_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;
        $ward_id = $request->ward_id;
        $market_id = $request->market_id;
        $start_date_d = $request->start_date_d;
        $end_date_d = $request->end_date_d;
        $sr_id = $request->sr_id;
        $site_id = $request->outlet_id;
        $oid_min_max=DB::connection($this->db)->select("Select min(id) as min_id,max(id) as max_id FROM tt_ordm where ordm_date BETWEEN '$start_date' AND '$end_date'");
        $slgp=DB::connection($this->db)->select("Select acmp_name,slgp_name from user_group_permission where acmp_id=$acmp_id and slgp_id=$sales_group_id limit 1");
        $slgp_name=$slgp[0]->slgp_name;
        $acmp_name=$slgp[0]->acmp_name;
        $min_id=$oid_min_max[0]->min_id;
        $max_id=$oid_min_max[0]->max_id;
        $q1 = "";
        $q2 = "";
        $q3 = "";

        $q5 = "";
        $q6 = "";
        $q7 = "";
        $q8 = "";
        $q9 = "";
        $q10 = "";

        if ($zone_id != "") {

            $q1 = " AND t1.zone_id = '$zone_id' ";
        }
        if ($sales_group_id != "") {

            $q2 = " AND t1.slgp_id = '$sales_group_id' ";
        }

        if ($dirg_id != "") {

            $q3 = " AND t3.dirg_id = '$dirg_id' ";
        }

        if ($dist_id != "") {

            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {

            $q6 = " AND t1.than_id = '$than_id'";
        }

        if ($ward_id != "") {

            $q7 = " AND t1.ward_id = '$ward_id'";
        }
        if ($market_id != "") {

            $q8 = " AND t1.mktm_id = '$market_id'";
        }


        if ($sr_id != "") {
            $q9 = " AND t1.aemp_id='$sr_id'";
        }
        if ($site_id != "") {
            $q10 = " AND t1.site_id='$site_id'";
        }

        if ($reportType == "class_wise_order_report_amt"){
            // DB::connection($this->db)->select(DB::raw("SET  sql_require_primary_key=0"));
            // DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            // $sql = "CREATE TEMPORARY TABLE temp1 AS SELECT t1.ordm_date, t1.aemp_id, t1.aemp_usnm, t1.aemp_name, t1.aemp_mob1,	t2.slgp_name,
            // t2.acmp_name, t3.zone_name, t3.dirg_name, t1.itcl_id, round(SUM(t1.ordd_oamt),2) AS t_amnt FROM `delivery_summary` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
            // INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id WHERE t2.aemp_id = '$empId' AND
            // t3.aemp_id = '$empId' AND t1.`ordm_date`= '$start_date'". $q1 . $q2. $q3 . $q9 . " GROUP BY t1.ordm_date,t1.itcl_id, t1.aemp_id , t1.aemp_usnm, t1.aemp_name, t1.aemp_mob1,t2.slgp_name,
            // t2.acmp_name, t3.zone_name, t3.dirg_name";
            // $srData = DB::connection($this->db)->select(DB::raw($sql));
            // $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,	
            // acmp_name, zone_name, dirg_name from temp1 GROUP BY aemp_id"));
            //             $iClass = DB::connection($this->db)->select(DB::raw("SELECT DISTINCT t1.itcl_id as itcl_id, t3.itcl_name FROM `tm_amim` t1 INNER JOIN tl_sgit t2 ON t1.id = t2.amim_id 
            // INNER JOIN tm_itcl t3 ON t1.itcl_id = t3.id
            // WHERE t1.lfcl_id='1' AND t2.slgp_id='$sales_group_id'"));
            // $row_no = 1;
            // $icol = 8;

            // $final[0][0] = "Order Date";
            // $final[0][1] = "Company";
            // $final[0][2] = "Group";
            // $final[0][3] = "Region";
            // $final[0][4] = "Zone";
            // $final[0][5] = "SR ID";
            // $final[0][6] = "SR Name";
            // $final[0][7] = "SR Mobile";
            // foreach($iClass as $rrr){
            //     $final[0][$icol] = $rrr->itcl_name;
            //     $icol++;
            // }
            // foreach ($srDataw as $rows) {
            //     $colu = 8;
            //     $sr_id = $rows->aemp_id;
            //     $final[$row_no][0] = $rows->ordm_date;
            //     $final[$row_no][1] = $rows->acmp_name;
            //     $final[$row_no][2] = $rows->slgp_name;
            //     $final[$row_no][3] = $rows->dirg_name;
            //     $final[$row_no][4] = $rows->zone_name;
            //     $final[$row_no][5] = $rows->aemp_usnm;
            //     $final[$row_no][6] = str_replace(",","-",$rows->aemp_name);
            //     $final[$row_no][7] = $rows->aemp_mob1;
            //     foreach ($iClass as $row) {
            //         $classID = $row->itcl_id;
            //         $srDatas = DB::connection($this->db)->select(DB::raw("select t_amnt from temp1 where itcl_id='$classID' and aemp_id='$sr_id'"));
            //         $tam=0;
            //         foreach ($srDatas as $result)
            //             if($result->t_amnt > 0){$tam = $result->t_amnt;}else{$tam = 0;}
            //         $final[$row_no][$colu] = $tam;
            //         $colu++;
            //     }

            //     $row_no = $row_no +1;
            // }
            //dd($final);
            $select_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $class_list=DB::connection($this->db)->select("SELECT distinct t3.id, t3.itcl_name
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4
                        where t1.id between $min_id and $max_id
                        and t1.ordm_date between  '$start_date'  and '$end_date'
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
                        where t1.id between $min_id and $max_id
                        and t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name");
            return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt);
            

        }
        else if ($reportType == "class_wise_order_report_memo"){
            // DB::connection($this->db)->select(DB::raw("SET  sql_require_primary_key=0"));
            // DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            // $sql = " CREATE TEMPORARY TABLE temp1 AS SELECT t1.ordm_date, t1.aemp_id, t1.aemp_usnm, t1.aemp_name, t1.aemp_mob1, t2.slgp_name,
            // t2.acmp_name, t3.zone_name, t3.dirg_name, t1.itcl_id, COUNT(DISTINCT t1.order_no) AS memo FROM `delivery_summary` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
            // INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id WHERE t2.aemp_id = '$empId' AND
            // t3.aemp_id = '$empId' AND t1.`ordm_date`= '$start_date'". $q1 . $q2. $q3 . $q9 . " GROUP BY t1.itcl_id, t1.ordm_date, t1.aemp_id, t1.aemp_usnm, t1.aemp_name, t1.aemp_mob1, t2.slgp_name,
            // t2.acmp_name, t3.zone_name, t3.dirg_name";
            // $srData = DB::connection($this->db)->select(DB::raw($sql));
            // $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
            // acmp_name, zone_name, dirg_name from temp1 GROUP BY aemp_id"));
            //             $iClass = DB::connection($this->db)->select(DB::raw("SELECT DISTINCT t1.itcl_id as itcl_id, t3.itcl_name FROM `tm_amim` t1 INNER JOIN tl_sgit t2 ON t1.id = t2.amim_id 
            // INNER JOIN tm_itcl t3 ON t1.itcl_id = t3.id
            // WHERE t1.lfcl_id='1' AND t2.slgp_id='$sales_group_id'"));
            //             $row_no = 1;
            //             $icol = 8;

            //             $final[0][0] = "Order Date";
            //             $final[0][1] = "Company";
            //             $final[0][2] = "Group";
            //             $final[0][3] = "Region";
            //             $final[0][4] = "Zone";
            //             $final[0][5] = "SR ID";
            //             $final[0][6] = "SR Name";
            //             $final[0][7] = "SR Mobile";
            //             foreach($iClass as $rrr){
            //                 $final[0][$icol] = $rrr->itcl_name;
            //                 $icol++;
            //             }
            //             foreach ($srDataw as $rows) {
            //                 $colu = 8;
            //                 $sr_id = $rows->aemp_id;
            //                 $final[$row_no][0] = $rows->ordm_date;
            //                 $final[$row_no][1] = $rows->acmp_name;
            //                 $final[$row_no][2] = $rows->slgp_name;
            //                 $final[$row_no][3] = $rows->dirg_name;
            //                 $final[$row_no][4] = $rows->zone_name;
            //                 $final[$row_no][5] = $rows->aemp_usnm;
            //                 $final[$row_no][6] = str_replace(",","-",$rows->aemp_name);
            //                 $final[$row_no][7] = $rows->aemp_mob1;
            //                 foreach ($iClass as $row) {
            //                     $classID = $row->itcl_id;
            //                     $srDatas = DB::connection($this->db)->select(DB::raw("select memo from temp1 where itcl_id='$classID' and aemp_id='$sr_id'"));
            //                     $tam=0;
            //                     foreach ($srDatas as $result)
            //                         if($result->memo > 0){$tam = $result->memo;}else{$tam = 0;}
            //                     $final[$row_no][$colu] = $tam;
            //                     $colu++;
            //                 }

            //                 $row_no = $row_no +1;
            //             }
            //             //dd($final);
            //             return $final;
            $select_q='';
            $condition_q='';
            $class_id=[];
            $temp_v_id='';
            $class_list=DB::connection($this->db)->select("SELECT distinct t3.id, t3.itcl_name
                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4
                        where t1.id between $min_id and $max_id
                        and t1.ordm_date between  '$start_date'  and '$end_date'
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
                        where t1.id between $min_id and $max_id
                        and t1.ordm_date between  '$start_date'  and '$end_date'
                                    and t1.id=t2.ordm_id 
                        ".$q2." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name");
            return array('class_list'=>$class_list,'class_wise_ord_memo'=>$class_wise_ord_memo);

                    }else if ($reportType == "sr_activity_hourly_order"){

                        $q_z="";
                        $q2_g="";
                        $q3_r="";
                        $q9_s="";
                        $start_date = $start_date;
                        $zone_id = $zone_id;
                        $sales_group_id = $sales_group_id;
                        $dirg_id = $dirg_id;
                        $sr_id=$sr_id;

                        if ($zone_id != "") {

                            $q_z = " AND t2.zone_id = '$zone_id'";
                        }
                        if ($sales_group_id != "") {

                            $q2_g = " AND t1.slgp_id = '$sales_group_id'";
                        }

                        if ($dirg_id != "") {

                            $q3_r = " AND t3.dirg_id = '$dirg_id'";
                        }
                        if ($sr_id != "") {
                            $q9_s = " AND t1.aemp_id='$sr_id'";
                        }


                        DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key=0"));
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
                t3.aemp_id = '$empId' and t4.aemp_id='$empId' ". $q_z . $q2_g. $q3_r . $q9_s ."
            GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";
            //echo $sql;
            $srData = DB::connection($this->db)->select(DB::raw($sql));
            $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
            acmp_name, zone_name, dirg_name,rout_name from temp1 GROUP BY aemp_id"));

            $row_no = 1;
            $icol = 8;

            $final[0][0] = "Order Date";
            $final[0][1] = "Company";
            $final[0][2] = "Group";
            $final[0][3] = "Region";
            $final[0][4] = "Zone";
            $final[0][5] = "SR ID";
            $final[0][6] = "SR Name";
            $final[0][7] = "SR Mobile";
            $final[0][8] = "Memo(0-9)";
            $final[0][9] = "M(9-10)";
            $final[0][10] = "M(10-11)";
            $final[0][11] = "M(11-12)";
            $final[0][12] = "M(12-13)";
            $final[0][13] = "M(13-14)";
            $final[0][14] = "M(14-15)";
            $final[0][15] = "M(15-16)";
            $final[0][16] = "M(16-17)";
            $final[0][17] = "M(17-18)";
            $final[0][18] = "M(18-19)";
            $final[0][19] = "M(19-20)";
            $final[0][20] = "M(20-24)";
            $final[0][21] = "Rout Name";


            foreach ($srDataw as $rows) {
                $colu = 8;
                $sr_id = $rows->aemp_id;
                $final[$row_no][0] = $rows->ordm_date;
                $final[$row_no][1] = $rows->acmp_name;
                $final[$row_no][2] = $rows->slgp_name;
                $final[$row_no][3] = $rows->dirg_name;
                $final[$row_no][4] = $rows->zone_name;
                $final[$row_no][5] = $rows->aemp_usnm;
                $final[$row_no][6] = str_replace(",","-",$rows->aemp_name);
                $final[$row_no][7] = $rows->aemp_mob1;
                $final[$row_no][21] = $rows->rout_name;
                if ($colu==8){

                }
                for ($i=8; $i<21; $i++) {
                    $k = $i;
                    if ($k==8){
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                    }else if ($i>19){
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                    }else{
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$i' and aemp_id='$sr_id'"));
                    }

                    $tam=0;
                    foreach ($srDatas as $result)
                        if($result->order_number > 0){$tam = $result->order_number;}else{$tam = 0;}
                    //echo "{" . $k . " - ". $tam. "}";
                    $final[$row_no][$colu] = $tam;
                    $colu++;
                }

                $row_no = $row_no +1;
            }

            return $final;

        } else if ($reportType == "sr_activity_hourly_visit"){

            $q_z="";
            $q2_g="";
            $q3_r="";
            $q9_s="";

            /*$start_date="2021-11-25";
            $zone_id='29';
            $sales_group_id='10';
            $dirg_id='';
            $sr_id='297';
            $empId = '72024';*/

            $start_date = $start_date;
            $zone_id = $zone_id;
            $sales_group_id = $sales_group_id;
            $dirg_id = $dirg_id;
            $sr_id=$sr_id;

            if ($zone_id != "") {

                $q_z = " AND t2.zone_id = '$zone_id'";
            }
            if ($sales_group_id != "") {

                $q2_g = " AND t1.slgp_id = '$sales_group_id'";
            }

            if ($dirg_id != "") {

                $q3_r = " AND t3.dirg_id = '$dirg_id'";
            }
            if ($sr_id != "") {
                $q9_s = " AND t1.aemp_id='$sr_id'";
            }

            DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key=0"));
            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            $sql = "CREATE TEMPORARY TABLE temp1 AS SELECT
  t4.acmp_name, t4.slgp_name, t3.dirg_name,t3.zone_name, t1.ordm_date,t1.aemp_id, t2.aemp_name, t2.aemp_usnm, t2.aemp_mob1,
  HOUR(ordm_time)'hr', COUNT(ordm_ornm) AS order_number
FROM `tt_ordm` t1
 INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
 INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
  INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
WHERE t1.ordm_date = '$start_date' AND
      t3.aemp_id = '$empId' and t4.aemp_id='$empId' ". $q_z . $q2_g. $q3_r . $q9_s ."
GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";

            $sql2 = "CREATE TEMPORARY TABLE temp2 AS SELECT t1.aemp_id, HOUR(npro_time) as hr, COUNT(t1.id) as note FROM `tt_npro` t1
INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
 INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
  INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
WHERE t1.npro_date = '$start_date' AND
      t3.aemp_id = '$empId' and t4.aemp_id='$empId' ". $q_z . $q2_g. $q3_r . $q9_s ."
GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";

            $srData = DB::connection($this->db)->select(DB::raw($sql));
            $srData2 = DB::connection($this->db)->select(DB::raw($sql2));
            $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
 acmp_name, zone_name, dirg_name from temp1 GROUP BY aemp_id"));

            $row_no = 1;
            $icol = 8;

            $final[0][0] = "Order Date";
            $final[0][1] = "Company";
            $final[0][2] = "Group";
            $final[0][3] = "Region";
            $final[0][4] = "Zone";
            $final[0][5] = "SR ID";
            $final[0][6] = "SR Name";
            $final[0][7] = "SR Mobile";
            $final[0][8] = "Visit(0-9)";
            $final[0][9] = "V(9-10)";
            $final[0][10] = "V(10-11)";
            $final[0][11] = "V(11-12)";
            $final[0][12] = "V(12-13)";
            $final[0][13] = "V(13-14)";
            $final[0][14] = "V(14-15)";
            $final[0][15] = "V(15-16)";
            $final[0][16] = "V(16-17)";
            $final[0][17] = "V(17-18)";
            $final[0][18] = "V(18-19)";
            $final[0][19] = "V(19-20)";
            $final[0][20] = "V(20-24)";


            foreach ($srDataw as $rows) {
                $colu = 8;
                $sr_id = $rows->aemp_id;
                $final[$row_no][0] = $rows->ordm_date;
                $final[$row_no][1] = $rows->acmp_name;
                $final[$row_no][2] = $rows->slgp_name;
                $final[$row_no][3] = $rows->dirg_name;
                $final[$row_no][4] = $rows->zone_name;
                $final[$row_no][5] = $rows->aemp_usnm;
                $final[$row_no][6] = str_replace(",","-",$rows->aemp_name);
                $final[$row_no][7] = $rows->aemp_mob1;
                if ($colu==8){

                }
                for ($i=8; $i<21; $i++) {
                    $k = $i;
                    if ($k==8){
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                        $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                    }else if ($i>19){
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                        $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                    }else{
                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$i' and aemp_id='$sr_id'"));
                        $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr ='$i' and aemp_id='$sr_id'"));
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
                }

                $row_no = $row_no +1;
            }

            return $final;

        } else {

            $temp_s_list = "";

            if ($reportType == "sr_activity") {
                $temp_s_list = "SELECT
                  t1.date,
                  t1.dirg_name,
                  t1.zone_name,
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
                        t1.zone_id,
                        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
                        concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
                        concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
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
                            t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
                      GROUP BY t1.zone_id, t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                  (SELECT
                     t1.date,
                     t1.zone_id,
                     t1.slgp_id,
                     count(DISTINCT t1.aemp_id) AS p_sr
                   FROM tt_aemp_summary t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t2 ON t1.zone_id = t2.zone_id AND t1.slgp_id = t2.slgp_id AND t1.date = t2.date
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
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t3 ON t1.zone_id = t3.zone_id AND t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                  INNER JOIN
                
                
                  (SELECT
                     t1.zone_id,
                     t1.slgp_id,
                     count(DISTINCT t1.id) AS t_sr
                   FROM tm_aemp t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%'
                          AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
            } else if ($reportType == "sr_productivity") {

                $q1 = "";
                $q2 = "";
                $q3 = "";
                $q5 = "";
                if ($zone_id != "") {
                    // $q1 = "AND t3.zone_id = '$zone_id'";
                    $q1 = " AND t1.zone_id = '$zone_id' ";
                }
                if ($sales_group_id != "") {

                    //$q2 = "AND t2.slgp_id = '$sales_group_id'";
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }

                if ($dirg_id != "") {
                    $q3 = " AND t3.dirg_id = '$dirg_id' ";
                }
                if ($sr_id != "") {
                    $q5 = " AND t1.aemp_id='$sr_id'";
                }

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
                          concat(t1.rout_id, ' - ', t1.rout_name)     AS rout_name,
                          t1.t_outlet                                 AS t_outlet,
                          (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
                          SUM(t1.s_outet)                             AS s_outet,
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
                              t3.aemp_id = '$empId' " . $q1 . $q2 . $q3 . $q5 . " and t2.acmp_id='$acmp_id'
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                        ORDER BY t2.slgp_code, t3.zone_code";
            } else if ($reportType == "sr_non_productivity") {

                $non_pr1 = "";
                $non_pr2 = "";
                if ($sales_group_id!=""){
                    $non_pr1 = " AND t1.slgp_id = '$sales_group_id' ";
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
                            t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
                      GROUP BY t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                  (SELECT
                     t1.date,
                    
                     t1.slgp_id,
                     count(DISTINCT t1.aemp_id) AS p_sr
                   FROM tt_aemp_summary t1
                     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
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
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
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
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%'
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
                if ($market_id!=""){
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
                        WHERE t8.slgp_id='$sales_group_id' and t9.aemp_id='$empId' and t10.aemp_id='$empId' and t2.id='$market_id' GROUP BY t1.site_code) AS ddd GROUP BY mktm_id";
                }else{
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
                }


            } else if ($reportType == "sr_wise_order_delivery") {
                $zone_query='';
                if($zone_id !=''){
                    $zone_query="AND t3.zone_id='$zone_id'";
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
            }
            else if($reportType=='zone_wise_order_delivery_summary'){
                $temp_s_list="SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`order_amount`) as ordd_oamt,
                sum(t1.`Delivery_Amount`) as ordd_Amnt FROM `s_mktm_zone_group_wise_order_vs_delivery` t1
               WHERE t1.ordm_date  BETWEEN '$start_date' AND '$end_date'". $q1.  "AND t1.slgp_id=$sales_group_id
                     group by t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`";
            }
            else if ($reportType == "sku_wise_order_delivery") {  
            $zone_query='';
            if($zone_id !=''){
                $zone_query="AND t3.zone_id='$zone_id'";
            } 
            // $slgp=DB::connection($this->db)->select("Select acmp_name,slgp_name from user_group_permission where acmp_id=$acmp_id and slgp_id=$sales_group_id limit 1");
            // $slgp_name=$slgp[0]->slgp_name;
            // $acmp_name=$slgp[0]->acmp_name;
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
            group by tt.dsct_id,dsct_name,tl.TOTL
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
            $q2.=",sum(if(t1.slgp='$temp_v',t_visited,0))`slgpv$i`,sum(if ( t1.slgp='$temp_v',t_memo,0))`slgpm$i`";
            
            $q1.=",sum(slgpv$i) as `slgpv$i`,sum(slgpm$i) as`slgpm$i`";
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
                group by tt.dsct_id,tt.dsct_name,tl.TOTL,tt.than_id,tt.than_name");
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
                group by tt.dsct_id,tt.dsct_name,tl.TOTL,tt.ward_id,tt.ward_name
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
                group by mktm_id,mktm_name,tl.TOTL
                ");
        }
        if($stage==4){
            $data="";
        }
        
        return array('data'=>$data,
                     'slgp'=>$slgp);

    }


}