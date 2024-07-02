<?php


namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalDashBoardData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function GlobalDashBoardData(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.dhbd_ucnt                                              AS totalSr,
  t1.dhbd_prnt                                              AS onSr,
  t1.dhbd_ucnt - t1.dhbd_prnt                               AS offSr,
  t1.dhbd_pact                                              AS actSr,
  t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
  t1.dhbd_prdt                                              AS pvSr,
  t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
  format(ifnull((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
  t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
  t1.dhbd_memo                                              AS productiveMemo,
  t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
  t1.dhbd_tsit                                              AS totalScheduleCall,
  t1.dhbd_tvit                                              AS total_visited,
  t1.dhbd_line                                              AS number_item_line,
  round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
  t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
  0                                                         AS totalMspTargetCtn,
  t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
  0                                                         AS totalMspOrderCtn,
  t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
  t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
  t1.dhbd_spbg                                              AS supervisorBudgetAmount,
  t1.dhbd_spav                                              AS supervisorBudgetAvail,
  t1.dhbd_cblc                                              AS creditBlockOrder,
  t1.dhbd_cbla                                              AS creditBlockAmount,
  t1.dhbd_oblc                                              AS overDueBlockOrder,
  t1.dhbd_obla                                              AS overDueBlockAmount,
  t1.dhbd_sblc                                              AS specialBlockOrder,
  t1.dhbd_sbla                                              AS specialBlockAmount,
  t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
  t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
  IFNULL(t2.dhbd_mtdo / 1000, 0)                            AS last_mtd_sales,
  IFNULL(t2.dhbd_mtdd / 1000, 0)                            AS last_mtd_delivery,
  t1.dhbd_time                                              AS updated_at
FROM th_dhbd AS t1
  LEFT JOIN th_dhbd AS t2
    ON t1.aemp_id = t2.aemp_id AND t2.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
WHERE t1.aemp_id = '$emp_id' AND t1.dhbd_date = '$date'");
            return Array("receive_data" => $employee, "action" => $emp_id);
        }

    }

    public function GlobalDashBoardData5(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $country = (new Country())->country($request->country_id);
        // $module_type = $country->module_type;
        if ($country) {
            if ($request->country_id != 3) {

                if (!empty($request->date)) {
                    //  dd('user_id is not empty.');
                    $employee = DB::connection($country->cont_conn)->select("SELECT
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
                t1.dhbd_pact                                              AS actSr,
                t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
                t1.dhbd_prdt                                              AS pvSr,
                t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
                t1.`dhbd_lvsr`                                            AS levSr,
                format(ifnull((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
                t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
                t1.dhbd_memo                                              AS productiveMemo,
                t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
                t1.dhbd_tsit                                              AS totalScheduleCall,
                t1.dhbd_tvit                                              AS total_visited,
                t1.dhbd_line                                              AS number_item_line,
                round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
                t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
                0                                                         AS totalMspTargetCtn,
                t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
                0                                                         AS totalMspOrderCtn,
                t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
                t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
                t1.dhbd_spbg                                              AS supervisorBudgetAmount,
                t1.dhbd_spav                                              AS supervisorBudgetAvail,
                t1.dhbd_cblc                                              AS creditBlockOrder,
                t1.dhbd_cbla                                              AS creditBlockAmount,
                t1.dhbd_oblc                                              AS overDueBlockOrder,
                t1.dhbd_obla                                              AS overDueBlockAmount,
                t1.dhbd_sblc                                              AS specialBlockOrder,
                t1.dhbd_sbla                                              AS specialBlockAmount,
                t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
                t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
                IFNULL(t2.dhbd_mtdo / 1000, 0)                            AS last_mtd_sales,
                IFNULL(t2.dhbd_mtdd / 1000, 0)                            AS last_mtd_delivery,
                t1.dhbd_time                                              AS updated_at
              FROM th_dhbd_5 AS t1
                LEFT JOIN th_dhbd_5 AS t2
                  ON t1.aemp_id = t2.aemp_id AND t2.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
              WHERE t1.aemp_id = '$emp_id' AND t1.dhbd_date = '$date'");
                } else {
                    $employee = DB::connection($country->cont_conn)->select("SELECT
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
                t1.dhbd_pact                                              AS actSr,
                t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
                t1.dhbd_prdt                                              AS pvSr,
                t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
                t1.`dhbd_lvsr`                                            AS levSr,
                format(ifnull((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
                t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
                t1.dhbd_memo                                              AS productiveMemo,
                t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
                t1.dhbd_tsit                                              AS totalScheduleCall,
                t1.dhbd_tvit                                              AS total_visited,
                t1.dhbd_line                                              AS number_item_line,
                round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
                t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
                0                                                         AS totalMspTargetCtn,
                t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
                0                                                         AS totalMspOrderCtn,
                t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
                t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
                t1.dhbd_spbg                                              AS supervisorBudgetAmount,
                t1.dhbd_spav                                              AS supervisorBudgetAvail,
                t1.dhbd_cblc                                              AS creditBlockOrder,
                t1.dhbd_cbla                                              AS creditBlockAmount,
                t1.dhbd_oblc                                              AS overDueBlockOrder,
                t1.dhbd_obla                                              AS overDueBlockAmount,
                t1.dhbd_sblc                                              AS specialBlockOrder,
                t1.dhbd_sbla                                              AS specialBlockAmount,
                t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
                t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
                IFNULL(t2.dhbd_mtdo / 1000, 0)                            AS last_mtd_sales,
                IFNULL(t2.dhbd_mtdd / 1000, 0)                            AS last_mtd_delivery,
                t1.dhbd_time                                              AS updated_at
              FROM th_dhbd_5 AS t1
                LEFT JOIN th_dhbd_5 AS t2
                  ON t1.aemp_id = t2.aemp_id AND t2.dhbd_date = DATE_SUB('$start_date', INTERVAL 1 MONTH)
              WHERE t1.aemp_id = '$emp_id' AND(t1.dhbd_date BETWEEN '$start_date' AND '$end_date')");
                }
            } else { // UAE Module
                $employee = DB::connection($country->cont_conn)->select("SELECT
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
                t1.dhbd_pact                                              AS actSr,
                t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
                t1.dhbd_prdt                                              AS pvSr,
                t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
                t1.`dhbd_lvsr`                                            AS levSr,
                format(ifnull((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
                t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
                t1.dhbd_memo                                              AS productiveMemo,
                t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
                t1.dhbd_tsit                                              AS totalScheduleCall,
                t1.dhbd_tvit                                              AS total_visited,
                t1.dhbd_line                                              AS number_item_line,
                round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
                t1.dhbd_ttar                                              AS totalTargetAmount,
                0                                                         AS totalMspTargetCtn,
                t1.dhbd_tamt                                              AS totalOrderAmount,
                0                                                         AS totalMspOrderCtn,
                t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
                t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
                t1.dhbd_spbg                                              AS supervisorBudgetAmount,
                t1.dhbd_spav                                              AS supervisorBudgetAvail,
                t1.dhbd_cblc                                              AS creditBlockOrder,
                t1.dhbd_cbla                                              AS creditBlockAmount,
                t1.dhbd_oblc                                              AS overDueBlockOrder,
                t1.dhbd_obla                                              AS overDueBlockAmount,
                t1.dhbd_sblc                                              AS specialBlockOrder,
                t1.dhbd_sbla                                              AS specialBlockAmount,
                t1.dhbd_mtdo                                              AS mtd_total_sales,
                t1.dhbd_mtdd                                              AS mtd_total_delivery,
                IFNULL(t2.dhbd_mtdo / 1000, 0)                            AS last_mtd_sales,
                IFNULL(t2.dhbd_mtdd / 1000, 0)                            AS last_mtd_delivery,
                t1.dhbd_time                                              AS updated_at
              FROM th_dhbd_5 AS t1
                LEFT JOIN th_dhbd_5 AS t2
                  ON t1.aemp_id = t2.aemp_id AND t2.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
              WHERE t1.aemp_id = '$emp_id' AND t1.dhbd_date = '$date'");
            }

            return Array("receive_data" => $employee, "action" => $emp_id);
        }


    }

    public function dashBoardSrDate(Request $request)
    {
        $aemp_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {

            /*$employee = DB::connection($country->cont_conn)->select("
SELECT
  t4.aemp_usnm                                 AS sr_id,
  t4.aemp_mob1                                 AS sr_mobile,
  t4.aemp_name                                 AS sr_name,
  t4.aemp_stnm                                 AS short_sr_name,
  ''                                           AS sr_region,
  if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
  if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
  if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
  if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
  '0'                                          AS attendance,
  t1.dhbd_prdt                                 AS is_productive,
  0                                            AS emp_code,
  t1.role_id                                   AS role_id,
  t2.role_name                                 AS role_name,
  t1.aemp_id                                   AS emp_id,
  if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
  0                                            AS last_year_mtd_total_delivery,
  t1.cont_id                                   as country_id
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN th_dhbd_5 AS t3
    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
WHERE t1.aemp_mngr = '$aemp_id' AND t1.dhbd_date = '$date'
UNION ALL
SELECT
  t4.aemp_usnm                                   AS sr_id,
  t4.aemp_mob1                                 AS sr_mobile,
  t4.aemp_name                                 AS sr_name,
  t4.aemp_stnm                                 AS short_sr_name,
  ''                                           AS sr_region,
  if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
  if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
  if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
  if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
  '0'                                          AS attendance,
  t1.dhbd_prdt                                 AS is_productive,
  0                                            AS emp_code,
  t1.role_id                                   AS role_id,
  t2.role_name                                 AS role_name,
  t1.aemp_id                                   AS emp_id,
  if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
  0                                            AS last_year_mtd_total_delivery,
  t1.cont_id                                   as country_id
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN th_dhbd_5 AS t3
    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
  INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
WHERE t8.dhem_emid = '$aemp_id' AND t1.dhbd_date = '$date'
");*/

            $employee = DB::connection($country->cont_conn)->select("
SELECT  sr_id                                 AS sr_id,
        sr_mobile                             AS sr_mobile,
        sr_name                               AS sr_name,
        short_sr_name                         AS short_sr_name,
        sr_region                             AS sr_region,
        order_amount                          AS order_amount,
        mtd_total_sales                       AS mtd_total_sales,
        mtd_total_delivery                    AS mtd_total_delivery,
        total_target                          AS total_target,
        attendance                            AS attendance,
        is_productive                         AS is_productive,
        emp_code                              AS emp_code,
        role_id                               AS role_id,
        role_name                             AS role_name,
        emp_id                                AS emp_id,
        last_mtd_total_delivery               AS last_mtd_total_delivery,
        last_year_mtd_total_delivery          AS last_year_mtd_total_delivery,
        country_id                            as country_id
FROM (
       SELECT
         t4.aemp_usnm                                 AS sr_id,
         t4.aemp_mob1                                 AS sr_mobile,
         t4.aemp_name                                 AS sr_name,
         t4.aemp_stnm                                 AS short_sr_name,
         ''                                           AS sr_region,
         if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
         if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
         if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
         if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
         '0'                                          AS attendance,
         t1.dhbd_prdt                                 AS is_productive,
         0                                            AS emp_code,
         t1.role_id                                   AS role_id,
         t2.role_name                                 AS role_name,
         t1.aemp_id                                   AS emp_id,
         if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
         0                                            AS last_year_mtd_total_delivery,
         t1.cont_id                                   as country_id,
         t9.zone_code
       FROM th_dhbd_5 AS t1
         INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
         LEFT JOIN th_dhbd_5 AS t3
           ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
         INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
         JOIN tm_zone t9 ON t4.zone_id=t9.id
       WHERE t1.aemp_mngr = '$aemp_id' AND t1.dhbd_date = '$date'
       UNION ALL
       SELECT
         t4.aemp_usnm                                   AS sr_id,
         t4.aemp_mob1                                 AS sr_mobile,
         t4.aemp_name                                 AS sr_name,
         t4.aemp_stnm                                 AS short_sr_name,
         ''                                           AS sr_region,
         if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
         if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
         if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
         if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
         '0'                                          AS attendance,
         t1.dhbd_prdt                                 AS is_productive,
         0                                            AS emp_code,
         t1.role_id                                   AS role_id,
         t2.role_name                                 AS role_name,
         t1.aemp_id                                   AS emp_id,
         if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
         0                                            AS last_year_mtd_total_delivery,
         t1.cont_id                                   as country_id,
         t9.zone_code
       FROM th_dhbd_5 AS t1
         INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
         LEFT JOIN th_dhbd_5 AS t3
           ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB('$date', INTERVAL 1 MONTH)
         INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
         INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
         JOIN tm_zone t9 ON t4.zone_id=t9.id
       WHERE t8.dhem_emid = '$aemp_id' AND t1.dhbd_date = '$date')AS dd
      ORDER BY dd.zone_code
");
            return Array("receive_data" => $employee, "action" => $aemp_id);
        }
    }

    /*public function dashBoard1SrDate(Request $request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("
SELECT
  t1.user_id                                              AS sr_id,
  ''                                                      AS sr_mobile,
  t1.name                                                 AS sr_name,
  t1.short_name                                           AS short_sr_name,
  ''                                                      AS sr_region,
  ifnull( t1.total_order/1000, 0)                         AS order_amount,
  ifnull( t1.mtd_total_sales/1000, 0)                     AS mtd_total_sales,
  0                                                       AS mtd_total_delivery,
  0                                                       AS total_target,
  '0'                                                     AS attendance,
  ifnull( t1.is_productive, 0)                            AS is_productive,
  0                                                       AS emp_code,
  t1.master_role_id                                       AS role_id,
  ''                                                      AS role_name,
  0                                                       AS emp_id,
  ifnull( t6.mtd_total_delivery/1000, 0)                  AS last_mtd_total_delivery,
  0                                                       AS last_year_mtd_total_delivery
FROM tblh_dashboard_data AS t1
inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
WHERE t1.manager_id = '$user_id' AND t1.date = '$date'
UNION ALL
SELECT
  t1.user_id                                              AS sr_id,
  ''                                                      AS sr_mobile,
  t1.name                                                 AS sr_name,  
  t1.short_name                                           AS short_sr_name,
  ''                                                      AS sr_region,
  ifnull( t1.total_order/1000, 0)                         AS order_amount,
  ifnull( t1.mtd_total_sales/1000, 0)                     AS mtd_total_sales,
  0                                                       AS mtd_total_delivery,
  0                                                       AS total_target,
  '0'                                                     AS attendance,
  ifnull( t1.is_productive, 0)                            AS is_productive,
  0                                                       AS emp_code,
  t1.master_role_id                                       AS role_id,
  ''                                                     AS role_name,
  0                                              AS emp_id,
  ifnull( t6.mtd_total_delivery/1000, 0) AS last_mtd_total_delivery,
  0 AS last_year_mtd_total_delivery
FROM tblh_dashboard_data AS t1
  inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  INNER JOIN tblh_dashboard_permission as t8 ON t1.user_id=t8.pr_user_id
WHERE t8.user_id = '$user_id' AND t1.date = '$date'
");
        return Array("receive_data" => $employee, "action" => $user_id);
    }*/


    public function srDashboard(Request $request)
    {
        $data1 = DB::select("SELECT
  t2.aemp_name                                             AS route_name,
  t1.dhbd_tsit                                             AS site_count,
  t1.dhbd_memo                                             AS memo_count,
  t1.dhbd_oexc                                             AS not_order,
  t1.dhbd_tsit - t1.dhbd_memo - t1.dhbd_oexc               AS pending,
  round(IFNULL((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
  t1.dhbd_line                                             AS totalLine,
  round(t1.dhbd_line / t1.dhbd_memo, 2)                    AS lineParCall,
  t1.dhbd_ttar / 26                                        AS todaysTarget,
  round(t1.dhbd_tamt, 2)                                   AS todaysOrder,
  t1.dhbd_ttar                                             AS totalTarget,
  t1.dhbd_mtdo                                             AS totalAchive,
  t1.dhbd_mtdd                                             AS totalDelivery
FROM th_dhbd_5 AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
WHERE t1.aemp_id = 3 AND t1.dhbd_date = curdate()");

        return Array(
            "receive_data_summary" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    /* public function srOrder(Request $request)
     {
         $srId = $request->srId;
         $startDate = $request->start_date;
         $endDate = $request->end_date;
         $data1 = DB::connection('myprg_p')->select("SELECT
   t1.ordm_ornm                            AS order_id,
   sum(t2.ordd_oamt)                       AS order_amount,
   concat(t4.site_code, '-', t4.site_name) AS site_name,
   t1.ordm_date                            AS order_date,
   t1.lfcl_id                              AS status_id,
   t5.lfcl_name                            AS status,
   0                                       AS invoice_amount,
   1                                       AS type_id
 FROM tt_ordm AS t1
   INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
   INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
   INNER JOIN tm_site AS t4 ON t1.site_id = t4.id
   INNER JOIN tm_lfcl AS t5 ON t1.lfcl_id = t5.id
 WHERE t3.aemp_usnm = '$srId' AND t1.ordm_date BETWEEN '$startDate' AND '$endDate'
 GROUP BY t1.ordm_ornm, t4.site_code, t4.site_name, t1.ordm_date, t1.lfcl_id, t5.lfcl_name
 ");

         return Array(
             "receive_data" => array("data" => $data1, "action" => $request->country_id),
         );
     }*/

    /*    public function srProduct(Request $request)
        {
            $srId = $request->srId;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $data1 = DB::connection('myprg_p')->select("SELECT
      t4.amim_code      AS product_id,
      t4.amim_name      AS product_name,
      t1.ordd_duft      AS ctn_size,
      sum(t1.ordd_qnty) AS order_qty,
      0                 AS delivary_qty,
      sum(t1.ordd_opds) AS discount,
      sum(t1.ordd_oamt) AS total_amount,
      0                 AS target_amount,
      0                 AS target_qty,
      0                 AS net_amount,
      1                 AS type_id,
      t5.itsg_name      AS sub_category
    FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
      INNER JOIN tm_amim AS t4 ON t1.amim_id = t4.id
      INNER JOIN tm_itsg AS t5 ON t4.itsg_id = t5.id
    WHERE t3.aemp_usnm = '$srId' AND t2.ordm_date BETWEEN '$startDate' AND '$endDate'
    GROUP BY t4.amim_code, t4.amim_name, t1.ordd_duft, t5.itsg_name
    ");

            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->country_id),
            );
        }*/


    public function dashBoard1NonSrDate(Request $request)
    {
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                  AS emp_id,
  t3.aemp_usnm                AS aemp_usnm,
  t3.aemp_mob1                AS mobile_no,
  t2.role_name                AS role_name,
  t2.id                       AS role_id,
  t3.aemp_name                AS sr_name,
  t3.aemp_stnm                AS short_sr_name,
  t1.dhbd_ucnt                AS tSr,
  t1.dhbd_prnt                AS onSr,
  t1.dhbd_ucnt - t1.dhbd_prnt AS offSr,
  t1.dhbd_pact                AS actSr,
  t1.dhbd_prnt - t1.dhbd_pact AS inactSr,
  t1.dhbd_prdt                AS pveSr,
  t1.dhbd_pact - t1.dhbd_prdt AS nveSr,
  t1.cont_id                  AS country_id
FROM th_dhbd AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND
      (t1.dhbd_ucnt - t1.dhbd_prnt > 0 OR t1.dhbd_prnt - t1.dhbd_pact > 0 OR t1.dhbd_pact - t1.dhbd_prdt > 0)");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1NonSrDate5(Request $request)
    {
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                  AS emp_id,
  t3.aemp_usnm                AS aemp_usnm,
  t3.aemp_mob1                AS mobile_no,
  t2.role_name                AS role_name,
  t2.id                       AS role_id,
  t3.aemp_name                AS sr_name,
  t3.aemp_stnm                AS short_sr_name,
  t1.dhbd_ucnt                AS tSr,
  t1.dhbd_prnt                AS onSr,
  t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)AS offSr,
  t1.dhbd_pact                AS actSr,
  t1.dhbd_prnt - t1.dhbd_pact AS inactSr,
  t1.dhbd_prdt                AS pveSr,
  t1.dhbd_pact - t1.dhbd_prdt AS nveSr,
  t1.dhbd_lvsr                AS levSr,
  t1.cont_id                  AS country_id
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER join tm_zone as t4 on t3.zone_id=t4.id
WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND
      (t1.dhbd_ucnt - t1.dhbd_prnt > 0 OR t1.dhbd_prnt - t1.dhbd_pact > 0 OR t1.dhbd_pact - t1.dhbd_prdt > 0) order by t4.zone_code ");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1ProdSrDate(Request $request)
    {
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                  AS emp_id, 
  t3.aemp_usnm                AS aemp_usnm,
  t3.aemp_mob1                AS mobile_no,
  t2.role_name                AS role_name,
  t2.id                       AS role_id,
  t3.aemp_name                AS sr_name,
  t3.aemp_stnm                AS short_sr_name,
  t1.dhbd_ucnt                AS tSr,
  t1.dhbd_prnt                AS onSr,
  t1.dhbd_ucnt - t1.dhbd_prnt AS offSr,
  t1.dhbd_pact                AS actSr,
  t1.dhbd_prdt - t1.dhbd_pact AS inactSr,
  t1.dhbd_prdt                AS pveSr,
  t1.dhbd_pact - t1.dhbd_prdt AS nveSr,
  t1.cont_id                  AS country_id
FROM th_dhbd AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND
      (t1.dhbd_prnt > 0 OR t1.dhbd_pact > 0 OR t1.dhbd_prdt > 0)");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1ProdSrDate5(Request $request)
    {
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                  AS emp_id, 
  t3.aemp_usnm                AS aemp_usnm,
  t3.aemp_mob1                AS mobile_no,
  t2.role_name                AS role_name,
  t2.id                       AS role_id,
  t3.aemp_name                AS sr_name,
  t3.aemp_stnm                AS short_sr_name,
  t1.dhbd_ucnt                AS tSr,
  t1.dhbd_prnt                AS onSr,
  t1.dhbd_ucnt - t1.dhbd_prnt AS offSr,
  t1.dhbd_pact                AS actSr,
  t1.dhbd_prdt - t1.dhbd_pact AS inactSr,
  t1.dhbd_prdt                AS pveSr,
  t1.dhbd_pact - t1.dhbd_prdt AS nveSr,
  t1.cont_id                  AS country_id
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER join tm_zone as t4 on t3.zone_id=t4.id
WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND

      (t1.dhbd_prnt > 0 OR t1.dhbd_pact > 0 OR t1.dhbd_prdt > 0) order by t4.zone_code");

            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1SiteSrDate(Request $request)
    {
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                                               AS emp_id, 
  t3.aemp_usnm                                             AS aemp_usnm,
  t3.aemp_mob1                                             AS mobile_no,
  t2.role_name                                             AS role_name,
  t2.id                                                    AS role_id,
  t3.aemp_name                                             AS sr_name,
  t3.aemp_stnm                                             AS short_sr_name,
  t1.dhbd_tsit                                             AS tSite,
  t1.dhbd_tvit                                             AS vSite,
  t1.dhbd_memo                                             AS mSite,
  round(IFNULL((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strike_rate,
  t1.dhbd_line                                             AS numberItemLine,
  round(IFNULL(t1.dhbd_line / t1.dhbd_memo, 0), 2)         AS lpc,
  round(t1.dhbd_tamt / 1000, 2)                            AS pe,
  t1.cont_id                                               AS country_id,
  t1.dhbd_ttar                                             AS m_target,
  t1.dhbd_mtdo                                             AS m_order
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER join tm_zone as t4 on t3.zone_id=t4.id
WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' order by t4.zone_code ;");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1SiteSrAvgDate(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("SELECT
  t1.aemp_id                            AS aemp_id,
  t3.aemp_usnm                          AS aemp_usnm,
  t3.aemp_mob1                          AS aemp_mob1,
  t2.role_name                          AS role_name,
  t2.id                                 AS role_id,
  t3.aemp_name                          AS aemp_name,
  t1.dhbd_tsit                          AS dhbd_tsit,
  t1.dhbd_ucnt                          AS dhbd_ucnt,
  round(t1.dhbd_tsit / t1.dhbd_ucnt, 2) AS aSite,
  t1.dhbd_tvit                          AS dhbd_tvit,
  t1.dhbd_pact                          AS dhbd_pact,
  round(t1.dhbd_tvit / t1.dhbd_pact, 2) AS aVisit,
  t1.dhbd_memo                          AS dhbd_memo,
  round(t1.dhbd_memo / t1.dhbd_pact, 2) AS aMemo,
  t1.dhbd_tamt                          AS dhbd_tamt,
  round(t1.dhbd_tamt / t1.dhbd_pact, 2) AS ape,
  round(t1.dhbd_tamt / t1.dhbd_memo, 2) AS ape_per_olt,
  t1.cont_id                            AS country_id
FROM th_dhbd_5 AS t1
  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER join tm_zone as t4 on t3.zone_id=t4.id
WHERE t1.aemp_mngr = '$emp_id' AND t1.dhbd_date = '$date' order by t4.zone_code ;");
            return Array("receive_data" => $employee, "action" => $emp_id);
        }
    }


}