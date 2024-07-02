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

class SRSummaryController extends Controller
{
    private $access_key = 'OrderSummaryController';
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


    public function orderSummaryReport()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");

        $results = [];
        return view('report.summary_report.sr_summary')
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

        $q1 = "";
        $q2 = "";
        $q3 = "";

        $q5 = "";
        $q6 = "";
        $q7 = "";
        $q8 = "";

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


        if ($outletAType == "issg") {
            $q4 = " AND t1.site_sign='1' ";
        } else if ($outletAType == "isfg") {
            $q4 = " AND t1.site_refi='1' ";
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
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . " group by t1.aemp_id, t1.site_id, t1.itcl_id";
                } else {
                    $query = "SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`, t1.`site_code`, t1.`site_name`, t1.`site_mob1`, t1.`itcl_name`, t1.amim_id, t1.amim_code, t1.amim_name,
   sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
 FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . " group by t1.aemp_id,t1.site_id, t1.amim_id";
                }

            } else {
                if ($classType == "w_class") {
                    $query = "SELECT t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`,  t1.`itcl_name`, t1.amim_code, t1.amim_name, sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q4 . $q1 . " group by t1.aemp_id, t1.itcl_id";
                } else {
                    $query = "SELECT t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, t1.`aemp_name`, t1.`aemp_usnm`,
  t1.`aemp_mob1`,  t1.`itcl_name`,t1.amim_id, t1.amim_code, t1.amim_name, sum(t1.`ordd_oamt`) as ordd_oamt, sum(t1.`ordd_qnty`) as ordd_qnty
FROM `order_summary` t1
   INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
   INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
 WHERE t1.ordm_date >= '$start_date' AND t1.ordm_date <= '$end_date' AND t2.aemp_id = '$empId' AND
       t3.aemp_id = '$empId' " . $q2 . $q3 . $q1 . " group by t1.aemp_id, t1.amim_id";
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
                          t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . " GROUP BY t1.aemp_id, t1.site_id, t1.itcl_id";
                }else{
                    $query="SELECT
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
                              t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . " GROUP BY t1.aemp_id, t1.site_id, t1.amim_id";

                }
            }else{
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
                          t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . " GROUP BY t1.site_id, t1.itcl_id";
                }else{
                    $query="SELECT
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
                              t3.aemp_id = '$empId' " . $q5 . $q6 . $q7 . $q8 . $q4 . " GROUP BY t1.site_id, t1.amim_id";

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

        return $srData;
    }

    public function srActivitySummaryFilterdemo_1(Request $request)
    {
        dd($request);
        $empId = $this->currentUser->employee()->id;
        //dd($request->all());
        $dirg_id        = $request->zone_id;
        $zone_id        = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $acmp_id        = $request->acmp_id;
        $reportType     = $request->reportType;
        $outletAType    = $request->outletAType;
        $sr_id          =$request->sr_id;
        if($reportType == "sr_activity"){
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
                         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
                   GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
        }else{
                $q1 = "";
                $q2 = "";
                $q3 = "";
                $q5="";
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
                if($sr_id!=""){
                  $q5=" AND t1.aemp_id='$sr_id'";
                }
                if ($outletAType == "productive") {

                    $q4 = " AND t1.s_outet > '0' ";
                }elseif ($outletAType == "non_productive"){
                    $q4 = " AND t1.s_outet='0' ";
                }else{
                    $q4="";
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
                        WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND
                              t3.aemp_id = '$empId' " . $q1 . $q2 . $q3 . $q5 . $q4 .  " AND t2.acmp_id = '$acmp_id'
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                        ORDER BY t2.slgp_code, t3.zone_code";

        }

      //  echo $temp_s_list;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($temp_s_list));
        $emp_usnm = $this->currentUser->employee()->aemp_usnm;
        $emp_name = $this->currentUser->employee()->aemp_name;
        DB::connection($this->db)->select("INSERT INTO `tl_report_log`( `user_id`, `user_name`, `report_name`, `status`) VALUES('$emp_usnm','$emp_name','SrSummary','report')");
        return $srData;
        //dd($srData);
    }

    public function mapShow($id,$date){
      $map_data = DB::connection($this->db)->select("SELECT  t1.date,t1.aemp_name,t1.v_outlet,t1.s_outet,t1.t_memo,t1.rpln_day,t1.inTime,t1.outTime,
                  t1.firstOrTime,t1.lastOrTime,t1.aemp_mobile,t3.site_name,t3.geo_lat,t3.geo_lon from tt_aemp_summary t1
                  INNER JOIN order_summary t2 ON t1.aemp_code=t2.aemp_usnm
                  INNER JOIN tm_site t3 on t2.site_id=t3.id
                  WHERE t1.aemp_code='$id' AND t1.date='$date' AND t2.ordm_date='$date'");
      return "This feature is under development";

    }


}