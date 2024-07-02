<?php
/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v3;

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

    /*
    *   API MODEL V3 [START]
    */

    public function GlobalDashBoardData5(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
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
            return Array("receive_data" => $employee, "action" => $emp_id);
        }


    }

    public function dashBoardSrDate(Request $request){

        $aemp_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
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

    public function dashBoard1ProdSrDate5(Request $request){
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("
                  SELECT
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
                  WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND
                  (t1.dhbd_prnt > 0 OR t1.dhbd_pact > 0 OR t1.dhbd_prdt > 0)
              ");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    public function dashBoard1SiteSrDate(Request $request){
      
        $user_id = $request->emp_id;
        $date = $request->date;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $employee = DB::connection($country->cont_conn)->select("
                  SELECT
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
                  round(IFNULL((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strike_rate,
                  t1.dhbd_line                                             AS numberItemLine,
                  round(IFNULL(t1.dhbd_line / t1.dhbd_memo, 0), 2)         AS lpc,
                  round(t1.dhbd_tamt / 1000, 2)                            AS pe,
                  t1.cont_id                                               AS country_id
                  FROM th_dhbd_5 AS t1
                  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                  WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date';
              ");
            return Array("receive_data" => $employee, "action" => $user_id);
        }
    }

    

    public function dashBoard1NonSrDate5(Request $request){
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
                                  WHERE t1.aemp_mngr = '$user_id' AND t1.dhbd_date = '$date' AND
                                    (t1.dhbd_ucnt - t1.dhbd_prnt > 0 OR t1.dhbd_prnt - t1.dhbd_pact > 0 OR t1.dhbd_pact - t1.dhbd_prdt > 0)
                                ");
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
WHERE t1.aemp_mngr = '$emp_id' AND t1.dhbd_date = '$date';");
            return Array("receive_data" => $employee, "action" => $emp_id);
        }
    }

    /*
    *   API MODEL V3 [END]
    */
     
}