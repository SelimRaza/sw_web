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

class OrderSummaryController extends Controller
{
    private $access_key = 'OrderSummaryController';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {   set_time_limit(8000000);
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


    public function orderSummaryReport()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");

        $results = [];
        return view('report.summary_report.order_summary')
            ->with('acmp', $acmp)
            ->with('region', $region)
            ->with('dsct', $dsct);
    }

    public function orderSummaryReportFilter(Request $request)
    {

        //dd($request);

        /* "reportType" => "b_sr" d_outlet
       "outletType" => "olt"
       "outletAType" => "isAll"
       "srType" => "wsr"
       "classType" => "w_class"
       "acmp_id" => "8"
       "sales_group_id" => "7"
       "dirg_id" => "1"
       "zone_id" => "29"
       "sr_id" => "69"
       "start_date" => "2021-10-30"
       "end_date" => "2021-10-30"*/
        /*
                "reportType" => "d_outlet"
              "outletTypev" => "olt"
              "outletAType" => "isAll"
              "srTypev" => "wsr"
              "classType" => "w_class"
              "acmp_id" => null
              "sales_group_id" => null
              "dirg_id" => null
              "zone_id" => null
              "sr_id" => null
              "start_date" => "2021-10-31"
              "end_date" => "2021-10-31"
              "dist_id" => "29"
              "than_id" => "153"
              "ward_id" => "3066"
              "market_id" => "3066"
              "start_date_d" => "2021-10-30"
              "end_date_d" => "2021-10-30"*/

        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;
        $outletType = $request->outletType;
        $outletAType = $request->outletAType;
        $srTypewithsr = $request->srType;
        $classType = $request->classType;
//dd($srType);
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
//outlet and sr
        $sr_id=$request->sr_id;
        $site_id=$request->outlet_id;
        $q1 = "";
        $q2 = "";
        $q3 = "";

        $q5 = "";
        $q6 = "";
        $q7 = "";
        $q8 = "";
        $q9="";
        $q10="";

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
        if($sr_id !=""){
            $q9=" AND t1.aemp_id='$sr_id'";
        }
        if($site_id !=""){
            $q10=" AND t1.site_id='$site_id'";
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
  t1.`aemp_mob1`, t1.`site_code`,  replace(t1.site_name, ',' , '-' ) AS site_name, t1.`site_mob1`, t1.`itcl_name`, sum(t1.`ordd_oamt`) as ordd_oamt,
  sum(t1.`ordd_qnty`) as ordd_qnty FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . $q9 . " group by t1.aemp_id, t1.site_id, t1.itcl_id";
                } else {
                    $query = "SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`, t1.`site_code`, replace(t1.site_name, ',' , '-' ) AS site_name, t1.`site_mob1`, t1.`itcl_name`, t1.amim_id, t1.amim_code, t1.amim_name,
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
                      replace(t1.mktm_name, ',' , '-' ) AS mktm_name,
                      t1.`site_code` as site_code,
                      replace(t1.site_name, ',' , '-' ) AS site_name,
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
                }else{
                    $query="SELECT
                          t1.`dsct_name` as dsct_name,
                          t1.`than_name` as than_name,
                          t1.`ward_name` as ward_name,
                          replace(t1.mktm_name, ',' , '-' ) AS mktm_name,
                          t1.`site_code` as site_code,
                          replace(t1.site_name, ',' , '-' ) AS site_name,
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
            }else{
                if ($classType == "w_class") {
                    $query = "SELECT
                      t1.`dsct_name` as dsct_name,
                      t1.`than_name` as than_name,
                      t1.`ward_name` as ward_name,
                      replace(t1.mktm_name, ',' , '-' ) AS mktm_name,
                      t1.`site_code` as site_code,
                      replace(t1.site_name, ',' , '-' ) AS site_name,
                      t1.`site_mob1` as site_mob1,
                      
                      t1.`itcl_name` as itcl_name,
                      t1.`itcl_code` as itcl_code,
                      SUM(t1.ordd_qnty) AS ordd_qnty,
                      SUM(t1.ordd_oamt) AS ordd_oamt
                    FROM `order_summary` t1
                      INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                      INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t1.ordm_date >= '$start_date_d' AND t1.ordm_date <= '$end_date_d' AND t2.aemp_id = '$empId' AND
                          t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . $q10 ." GROUP BY t1.site_id, t1.itcl_id";
                }else{
                    $query="SELECT
                          t1.`dsct_name` as dsct_name,
                          t1.`than_name` as than_name,
                          t1.`ward_name` as ward_name,
                          replace(t1.mktm_name, ',' , '-' ) AS mktm_name,
                          t1.`site_code` as site_code,
                          replace(t1.site_name, ',' , '-' ) AS site_name,
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
        /*else {
            echo $srTyped;
            if ($srTyped == "wsr") {
                if ($classType == "w_class") {

                    //dd($query);
                }
            }
        }*/


        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        $emp_usnm = $this->currentUser->employee()->aemp_usnm;
        $emp_name = $this->currentUser->employee()->aemp_name;
        DB::connection($this->db)->select("INSERT INTO `tl_report_log`( `user_id`, `user_name`, `report_name`, `status`) VALUES('$emp_usnm','$emp_name','OrdSummary','report')");
        return $srData;
    }
    public function loadMarketWiseOutlet(Request $request){
        $mkt_id=$request->market_id;
        $outlets= DB::connection($this->db)->select("SELECT id, site_code,site_name  from tm_site  where tm_site.mktm_id='$mkt_id'");
        return $outlets;

    }


}