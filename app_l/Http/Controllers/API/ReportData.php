<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function employeeReportByDate(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $date = $request->date;
            $todaysRoute = collect(\DB::connection($db_conn)->select("SELECT
  count(t1.site_id)  AS site_count,
  max(t1.route_name) AS route_name
FROM (
       SELECT
         t2.site_id   AS site_id,
         t3.rout_name AS route_name
       FROM tl_rpln AS t1
         INNER JOIN tl_rsmp AS t2 ON t1.rout_id = t2.rout_id
         INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
         INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
       WHERE t1.aemp_id = $aemp_id AND t1.rpln_day = dayname('$date')
     ) AS t1"))->first();

            $todaysRouteOrder = DB::connection($db_conn)->select("SELECT t1.site_id AS site_id
FROM th_ssvh AS t1
WHERE t1.aemp_id = $aemp_id AND t1.ssvh_date = '$date' AND t1.ssvh_ispd = 1");

            $todaysRouteNotOrder = DB::connection($db_conn)->select("SELECT t1.site_id AS site_id
FROM th_ssvh AS t1
WHERE t1.aemp_id = $aemp_id AND t1.ssvh_date = '$date' AND t1.ssvh_ispd != 1");
            $totalNumberLine = DB::connection($db_conn)->select("SELECT
  t3.amim_id   AS line_count,
  t3.ordd_oamt AS order_amount
FROM th_ssvh AS t1
  INNER JOIN tt_ordm AS t2 ON t1.site_id = t2.site_id AND t1.ssvh_date = t2.ordm_date and t1.aemp_id=t2.aemp_id
  INNER JOIN tt_ordd AS t3 ON t2.id = t3.ordm_id
WHERE t1.aemp_id =$aemp_id AND t1.ssvh_date = '$date'");

            $data_responce = array(
                "route_name" => $todaysRoute->route_name,
                "site_count" => $todaysRoute->site_count,
                "memo_count" => count($todaysRouteOrder),
                "not_order" => count($todaysRouteNotOrder),
                "pending" => $todaysRoute->site_count - count($todaysRouteNotOrder) - count($todaysRouteOrder),
                "strikeRate" => number_format((($todaysRoute->site_count > 0) ? count($todaysRouteOrder) / $todaysRoute->site_count : 0) * 100, 2),
                "totalLine" => number_format((count($totalNumberLine) > 0) ? count($totalNumberLine) : 0, 0),
                "lineParCall" => number_format((count($todaysRouteOrder) > 0) ? count($totalNumberLine) / count($todaysRouteOrder) : 0, 2),
                "todaysTarget" => 1,
                "todaysOrder" => array_sum(array_column($totalNumberLine, 'order_amount')),
                "totalTarget" => 1,
                "totalAchive" => array_sum(array_column($totalNumberLine, 'order_amount')),
            );

            return array("receive_data" => [$data_responce], "action" => $request->emp_id);
        }
    }

    public function employeeProductList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tst = DB::connection($db_conn)->select("SELECT
  t6.amim_code                                    AS product_id,
  t6.amim_name                                    AS product_name,
  t6.amim_duft                                    AS ctn_size,
  sum(t5.ordd_qnty)                               AS order_qty,
  sum(t5.ordd_dqty)                               AS delivery_qty,
  sum(t5.ordd_dfdo + t5.ordd_spdo + t5.ordd_opds) AS discount,
  sum(t5.ordd_oamt)                               AS total_amount,
  IFNULL(t8.trgt_ramt, 0)                         AS target_amount,
  IFNULL(t8.trgt_rqty, 0)                         AS target_qty,
  sum(t5.ordd_odat)                               AS net_amount,
  1                                               AS type_id,
  t7.itsg_name                                    AS sub_category
FROM tt_ordm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
  INNER JOIN tt_ordd AS t5 ON t1.id = t5.ordm_id
  INNER JOIN tm_amim AS t6 ON t5.amim_id = t6.id
  INNER JOIN tm_itsg AS t7 ON t6.itsg_id = t7.id
  LEFT JOIN tt_trgt AS t8
    ON t2.id = t8.aemp_susr AND t5.amim_id = t8.amim_id AND t8.trgt_mnth = month(curdate()) AND
       t8.trgt_year = year(curdate())
WHERE
  (t1.aemp_id = $request->emp_id or t2.aemp_mngr=$request->emp_id) AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'
GROUP BY t6.amim_code, t6.amim_name, t6.amim_duft, t7.itsg_name,t8.trgt_ramt,t8.trgt_rqty
ORDER BY t6.amim_code, t6.amim_name,t7.itsg_name");
            return array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }
    }

    public function employeeOrderList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $country1 = (new Country())->country($request->country_id);
            $module_type = $country1->module_type;

            $request_time = date('Y-m-d h:i:s');
            if ($module_type == 2) {
                $data1 = DB::connection($db_conn)->select("SELECT
                    t1.ordm_ornm                                             AS order_id,
                    t1.ordm_amnt                                             AS order_amount,
                    concat(t3.id, '-', t3.site_name, '(', t3.site_code, ')') AS site_name,
                    t1.ordm_date                                             AS order_date,
					t1.ordm_drdt                                             AS deliv_date,
                    t1.lfcl_id                                               AS status_id,
                    t4.lfcl_name                                             AS status,
                    ifnull(t7.INV_AMNT,0)                                    AS invoice_amount,
                    ifnull(t7.DELV_AMNT,0)                                   AS delv_amount,
                    1                                                        AS type_id,
                    concat(t6.id,'-',t6.dlrm_name)                           AS depo,
                    (CASE WHEN t1.aemp_id !=t1.aemp_iusr THEN 1 ELSE 0 END)  AS is_manager
                  FROM tt_ordm AS t1
                    INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                    INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
                    INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
                    INNER JOIN tt_ordd AS t5 ON t1.id = t5.ordm_id
                    INNER JOIN tm_dlrm AS t6 ON t1.dlrm_id=t6.id
					LEFT JOIN dm_trip_master t7 ON t1.ordm_ornm=t7.ORDM_ORNM
                  WHERE (t1.aemp_id = $request->emp_id or t2.aemp_mngr=$request->emp_id) AND t1.ordm_date BETWEEN  '$request->start_date' AND '$request->end_date'
                  GROUP BY t1.ordm_ornm,t1.ordm_amnt, t3.id, t3.site_name, t3.site_code, t1.ordm_date, t1.lfcl_id, t4.lfcl_name");
            } else {
                $data1 = DB::connection($db_conn)->select("SELECT
                    t1.ordm_ornm                                             AS order_id,
                    t1.ordm_amnt                                             AS order_amount,
                    concat(t3.id, '-', t3.site_name, '(', t3.site_code, ')') AS site_name,
                    t1.ordm_date                                             AS order_date,
					t1.ordm_drdt                                             AS deliv_date,
                    t1.lfcl_id                                               AS status_id,
                    t4.lfcl_name                                             AS status,
                    ifnull(SUM(t5.ordd_oamt),0)                                   AS invoice_amount,
                    ifnull(SUM(t5.ordd_odat),0)                                   AS delv_amount,
                    1                                                        AS type_id,
                    concat(t6.id,'-',t6.dlrm_name)                           AS depo,
                    (CASE WHEN t1.aemp_id !=t1.aemp_iusr THEN 1 ELSE 0 END)  AS is_manager
                  FROM tt_ordm AS t1
                    INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                    INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
                    INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
                    INNER JOIN tt_ordd AS t5 ON t1.id = t5.ordm_id
                    INNER JOIN tm_dlrm AS t6 ON t1.dlrm_id=t6.id
					
                  WHERE (t1.aemp_id = $request->emp_id or t2.aemp_mngr=$request->emp_id) AND t1.ordm_date BETWEEN  '$request->start_date' AND '$request->end_date'
                  GROUP BY t1.ordm_ornm,t1.ordm_amnt, t3.id, t3.site_name, t3.site_code, t1.ordm_date, t1.lfcl_id, t4.lfcl_name");
            }

            return array(
                "receive_data" => array("data" => $data1, "action" => $request->emp_id),
                "request_time" => $request_time,
                "response_time" => date('Y-m-d h:i:s'),
            );
        }
    }

    public function employeeSummaryList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            /* $data1 = DB::connection($db_conn)->select("SELECT
            sum(order_amount)   AS ord_value,
            order_date          AS ord_date,
            sum(Memo_Qty)       AS ord_memo,
            sum(Visited_Outlet) AS ord_visited_Out,
            sum(lpc)            AS ord_lpc
            FROM (
            SELECT
            0                 AS order_amount,
            t3.ssvh_date      AS order_date,
            0                 AS Memo_Qty,
            COUNT(t3.site_id) AS Visited_Outlet,
            0                 AS lpc
            FROM tm_aemp AS t2
            INNER JOIN th_ssvh AS t3 ON t3.aemp_id = t2.id
            WHERE (t2.id = $request->emp_id OR t2.aemp_mngr = $request->emp_id) AND t3.ssvh_date BETWEEN '$request->start_date' AND '$request->end_date'
            GROUP BY t3.ssvh_date
            UNION ALL
            SELECT
            Sum(t1.ordm_amnt)                     AS order_amount,
            t1.ordm_date                          AS order_date,
            COUNT(t1.id)                          AS Memo_Qty,
            0                     AS Visited_Outlet,
            sum(t1.ordm_icnt) / COUNT(t1.site_id) AS lpc
            FROM tt_ordm AS t1
            INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
            INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
            WHERE (t1.aemp_id = $request->emp_id OR t2.aemp_mngr = $request->emp_id) AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'
            GROUP BY t1.ordm_date) AS t7
            GROUP BY ord_date");//ORDER BY ord_date ASC*/

            /*
             ** Nepal ED 08652 USER ISSUE DASHBOAD NUMBER OF MEMO BUT ORDER WISE MEMO NUMBER DOUBLE
             *OR t2.aemp_mngr = $request->emp_id
             */
            //return $request->all();
            // $data1 = DB::connection($db_conn)->select("SELECT
            //           sum(order_amount)   AS ord_value,
            //           order_date          AS ord_date,
            //           sum(Memo_Qty)       AS ord_memo,
            //           sum(Visited_Outlet) AS ord_visited_Out,
            //           sum(lpc)            AS ord_lpc
            //         FROM (
            //               SELECT
            //                 0                 AS order_amount,
            //                 t3.ssvh_date      AS order_date,
            //                 0                 AS Memo_Qty,
            //                 COUNT(t3.site_id) AS Visited_Outlet,
            //                 0                 AS lpc
            //               FROM tm_aemp AS t2
            //                 INNER JOIN th_ssvh AS t3 ON t3.aemp_id = t2.id
            //               WHERE (t2.id = '$request->emp_id' OR t2.aemp_mngr = '$request->emp_id')
            //               AND t3.ssvh_date BETWEEN '$request->start_date' AND '$request->end_date'
            //               GROUP BY t3.ssvh_date
            //               UNION ALL
            //               SELECT
            //                 (t1.dhbd_tamt)                     AS order_amount,
            //                 t1.dhbd_date                          AS order_date,
            //                 (t1.dhbd_memo)                          AS Memo_Qty,
            //                 0                     AS Visited_Outlet,
            //                 round(t1.dhbd_line / t1.dhbd_memo, 2) AS lpc
            //               FROM th_dhbd_5 AS t1
            //                 INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
            //               WHERE (t1.aemp_id ='$request->emp_id' )AND t1.dhbd_tsit!=0 AND t1.dhbd_line!=0
            //               AND t1.dhbd_date BETWEEN '$request->start_date' AND '$request->end_date'
            //               GROUP BY t1.dhbd_tamt,t1.dhbd_date,t1.dhbd_memo,t1.dhbd_line) AS t7
            //               GROUP BY ord_date ORDER BY ord_date");
            /*
                                  SELECT
                                  0                 AS order_amount,
                                  t3.ssvh_date      AS order_date,
                                  0                 AS Memo_Qty,
                                  COUNT(t3.site_id) AS Visited_Outlet,
                                  0                 AS lpc,
                                  0                 AS manager_memo
                                FROM tm_aemp AS t2
                                INNER JOIN th_ssvh  t3 ON t3.aemp_iusr = t2.id
                                WHERE (t2.id =$emp_id OR t2.aemp_mngr =$emp_id)
                                AND t3.ssvh_date BETWEEN '$request->start_date' AND '$request->end_date'
                                GROUP BY t3.ssvh_date
            */
            $request_time = date('Y-m-d h:i:s');
            $emp_id = $request->emp_id;
            $order_memo_data = DB::connection($db_conn)->select("SELECT 
                              round(sum(order_amount),2)   AS ord_value,
                              order_date          AS ord_date,
                              sum(Memo_Qty)       AS ord_memo,
                              sum(Visited_Outlet) AS ord_visited_Out,
                              sum(lpc)            AS ord_lpc,
                              sum(manager_memo)   AS manager_memo
                              FROM (
                                SELECT
                                  0                 AS order_amount,
                                  t3.dhbd_date      AS order_date,
                                  0                 AS Memo_Qty,
                                  t3.dhbd_tvit AS Visited_Outlet,
                                  0                 AS lpc,
                                  0                 AS manager_memo
                                FROM tm_aemp AS t2
                                INNER JOIN th_dhbd_5 t3 ON t3.aemp_id = t2.id
                                WHERE t3.aemp_id =$emp_id
                                AND t3.dhbd_date BETWEEN '$request->start_date' AND '$request->end_date'
                                GROUP BY t3.dhbd_date,t3.dhbd_tvit
                                UNION ALL
                                SELECT
                                  (t1.dhbd_tamt)        AS order_amount,
                                  t1.dhbd_date          AS order_date,
                                  (t1.dhbd_memo)        AS Memo_Qty,
                                  0                     AS Visited_Outlet,
                                  round(t1.dhbd_line / t1.dhbd_memo, 2) AS lpc,
                                  0                 AS manager_memo
                                FROM th_dhbd_5 AS t1
                                  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                                WHERE (t1.aemp_id =$emp_id )AND t1.dhbd_tsit!=0 AND t1.dhbd_line!=0
                                AND t1.dhbd_date BETWEEN '$request->start_date' AND '$request->end_date'
                                GROUP BY t1.dhbd_tamt,t1.dhbd_date,t1.dhbd_memo,t1.dhbd_line
                              
                              UNION ALL
                              SELECT 
                                  0                 AS order_amount,
                                t1.ordm_date       AS order_date,
                                  0                 AS Memo_Qty,
                                  0                 AS Visited_Outlet,
                                  0                 AS lpc,
                              count(t1.id) manager_memo
                              FROM tt_ordm t1							  
                              WHERE t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date' AND t1.aemp_id=$emp_id AND t1.aemp_iusr !=$emp_id
                              GROUP BY t1.ordm_date) AS t7
                              GROUP BY ord_date ORDER BY ord_date;");

            $order_data = array(
                'green_qty' => '40',
                'gold_qty' => '30',
                'red_qty' => '12',
                'green_color' => '#008000',
                'gold_color' => '#FFD700',
                'red_color' => '#ED1B24'
            );
            // return array(
            //     'ord_value'=>$data1?$data1[0]->ord_value:0,
            //     'ord_date'=>$data1?$data1[0]->ord_date:0,
            //     'sr_memo'=>$data1?$data1[0]->ord_memo-$manager_data[0]->manager_memo:0,
            //     'manager_memo'=>$manager_data[0]->manager_memo,
            //     'ord_visited_Out'=>$data1?$data1[0]->ord_visited_Out:0,
            //     'ord_lpc'=>$data1?$data1[0]->ord_lpc:0,
            //     'request_time'=>$request_time,
            //     'response_time'=>date('Y-m-d h:i:s')

            // );

            return array(
                "receive_data" => array("data" => $order_memo_data, "action" => $order_data),
                "request_time" => $request_time,
                "response_time" => date('Y-m-d h:i:s')
            );

        }
    }

    public function orderLineList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tst = DB::connection($db_conn)->select("
SELECT
  t1.ordm_ornm                AS order_id,
  t2.amim_id                  AS product_id,
  t3.amim_name                AS product_name,
  t3.amim_duft                AS ctn_size,
  t2.ordd_uprc                AS unit_price,
  t2.ordd_uprc * t3.amim_duft AS ctn_price,
  t2.ordd_qnty                AS order_qty,
  t2.ordd_cqty                AS Confirm_qty,
  t2.ordd_dfdo                AS default_discount,
  t2.ordd_spdo                AS discount,
  t2.ordd_spdd                AS confirm_discount,
  t2.ordd_opds                AS promo_discount,
  t2.ordd_dfdd                AS confirm_default_discount,
  t2.ordd_dpds                AS confirm_promo_discount,
  t2.ordd_oamt                AS total_amount,
  t2.ordd_amnt                AS total_confirm_amount,
  t2.prom_id                  AS promo_ref
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE
  t1.ordm_ornm = '$request->order_id'");
            return array("receive_data" => array("data" => $tst, "action" => $request->order_id));
        }
    }

    public function employeeRoutePlan(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  t1.rout_id   AS route_id,
  t2.rout_code AS route_code,
  t1.rpln_day  AS day_name,
  t2.rout_name AS route_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
WHERE t1.aemp_id = $request->emp_id");
            return array(
                "receive_data" => array("data" => $data1, "action" => $request->emp_id),
            );
        }
    }

    public function employeeRoutePlanSite(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  t4.id               AS site_id,
  t4.site_code        AS site_code,
  t4.site_name        AS site_name,
  t4.site_ownm        AS owner_name,
  ''                  AS payment,
  t4.site_adrs        AS address,
  t4.site_mob1        AS mobile,
  t4.site_vrfy        AS is_verified,
  !isnull(t5.site_id) AS visited
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN th_ssvh AS t5 ON t4.id = t5.site_id AND t5.ssvh_date = curdate()
WHERE t1.rout_id = $request->route_id AND t4.lfcl_id = 1
GROUP BY t4.id, t4.site_name, t4.site_ownm, t4.site_vrfy, t5.site_id");
            return array(
                "receive_data" => array("data" => $data1, "action" => $request->route_id),
            );
        }
    }

    public function mapSiteList_DateWise(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $DRT = $request->date;
            $day = Carbon::parse($DRT)->format('l');

            $tst = DB::connection($db_conn)->select("
SELECT
  t1.id                                     AS site_id,
  t1.site_name                              AS site_name,
  t1.site_mob1                              AS mobile,
  t1.geo_lat                                AS lat,
  t1.geo_lon                                AS lon,
  ''                                        AS region_name,
  ''                                        AS zone_name,
  ''                                        AS base_name,
  concat(t4.aemp_name, ' - ', t4.aemp_usnm) AS emp_name,
  concat(t5.aemp_name, ' - ', t5.aemp_usnm) AS sv_name,
  0                                         AS dist_dif,
  0                                         AS time_dif,
  0                                         AS type,
  max(t7.created_at)                         AS last_time,
  t1.site_vrfy                              AS is_verified,
  ''                                        AS user_name,
  0                                         AS emp_id,
  0                                         AS emp_code,
  0                                         AS role_id,
  t6.id                                     AS channel_id,
 datediff('$request->date', max(t7.ssvh_date))    AS visited
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
  INNER JOIN tl_rpln AS t3 ON t2.rout_id = t3.rout_id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
  INNER JOIN tm_scnl AS t6 ON t1.scnl_id = t6.id
  LEFT JOIN th_ssvh AS t7 ON t1.id = t7.site_id and t7.ssvh_date='$request->date' and t7.aemp_id=$request->emp_id
WHERE 1 AND t1.lfcl_id = 1 AND t3.aemp_id = $request->emp_id AND t3.rpln_day= dayname('$request->date')
GROUP BY t1.id, t1.site_name, t1.site_mob1, t1.geo_lat, t1.geo_lon, t4.aemp_name, t4.aemp_usnm, t5.aemp_name, t5.aemp_usnm, t1.site_vrfy,
  t6.id
");
            // return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
            return array("data" => $tst, "action" => $request->emp_id);
        }
    }

    public function mapSiteList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tst = DB::connection($db_conn)->select("
SELECT
  t1.id                                     AS site_id,
  t1.site_name                              AS site_name,
  t1.site_mob1                              AS mobile,
  t1.geo_lat                                AS lat,
  t1.geo_lon                                AS lon,
  ''                                        AS region_name,
  ''                                        AS zone_name,
  ''                                        AS base_name,
  concat(t4.aemp_name, ' - ', t4.aemp_usnm) AS emp_name,
  concat(t5.aemp_name, ' - ', t5.aemp_usnm) AS sv_name,
  0                                         AS dist_dif,
  0                                         AS time_dif,
  0                                         AS type,
  max(t7.created_at)                         AS last_time,
  t1.site_vrfy                              AS is_verified,
  ''                                        AS user_name,
  0                                         AS emp_id,
  0                                         AS emp_code,
  0                                         AS role_id,
  t6.id                                     AS channel_id,
  datediff(curdate(), max(t7.ssvh_date))    AS visited
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
  INNER JOIN tl_rpln AS t3 ON t2.rout_id = t3.rout_id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
  INNER JOIN tm_scnl AS t6 ON t1.scnl_id = t6.id
  LEFT JOIN th_ssvh AS t7 ON t1.id = t7.site_id
WHERE 1 AND t1.lfcl_id = 1 AND t3.aemp_id = $request->emp_id
GROUP BY t1.id, t1.site_name, t1.site_mob1, t1.geo_lat, t1.geo_lon, t4.aemp_name, t4.aemp_usnm, t5.aemp_name, t5.aemp_usnm, t1.site_vrfy,
  t6.id");
            return array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }
    }

    public function employeeOrderPrint(Request $request)
    {
        /*       $order_master = collect(\DB::select("SELECT
        t5.aemp_usnm AS sr_id,
        t5.aemp_name AS sr_name,
        t5.aemp_mob1 AS sr_mobile,
        t1.ordm_ornm AS order_id,
        t2.slgp_name AS group_name,
        t3.site_code AS outlet_id,
        t3.site_name AS outlet_name,
        t3.site_olnm AS print_outlet_name,
        t3.site_olad    print_outlet_address,
        t3.site_mob1 AS outlet_mobile,
        t3.site_adrs AS outlet_address,
        t3.site_ownm AS outlet_owner,
        t4.dlrm_name AS dealer_name,
        t4.dlrm_mob1 AS dealer_mobile,
        t1.ordm_date AS order_date,
        t1.ordm_drdt AS delivery_date
        FROM tt_ordm AS t1
        INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
        INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
        INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
        INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
        WHERE t1.aemp_id = '9' AND t1.ordm_date = curdate()"))->first();*/

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $where = "";
            if ($request->order_id != "") {
                $order_id1 = $request->order_id;
                $where = " and t1.ordm_ornm='$order_id1' ";
            }
            $order_master = DB::connection($db_conn)->select("SELECT
         t1.id,
         'Order' as title,
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_olnm AS print_outlet_name,
         t3.site_olad    print_outlet_address,
         t3.site_mob1 AS outlet_mobile,
         t3.site_adrs AS outlet_address,
         t3.site_ownm AS outlet_owner,
         t4.dlrm_name AS dealer_name,
         t4.dlrm_mob1 AS dealer_mobile,
         t1.ordm_date AS order_date,
         t1.ordm_drdt AS delivery_date,
         0 as totla_amount,
         0 as totla_discount
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date' $where");

            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  round(t1.ordd_uprc,2) as rate,
  t1.ordd_qnty as quantity,
  round(t1.ordd_opds,2) as discont,
  round(t1.ordd_oamt,2) as price
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            return array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

    public function employeeOrderPrintNew(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $where = "";
            if (isset($request->order_id)) {
                if ($request->order_id != "") {
                    $order_id1 = $request->order_id;
                    $where = " and t1.ordm_ornm='$order_id1' ";
                }
            }

            $order_master = DB::connection($db_conn)->select("SELECT
         t1.id,
         'Order' as title,
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_olnm AS print_outlet_name,
         t3.site_olad    print_outlet_address,
         t3.site_mob1 AS outlet_mobile,
         t3.site_adrs AS outlet_address,
         t3.site_ownm AS outlet_owner,
         t4.dlrm_name AS dealer_name,
         t4.dlrm_mob1 AS dealer_mobile,
         t1.ordm_date AS order_date,
         t1.ordm_drdt AS delivery_date,
         0 as totla_amount,
         0 as totla_discount
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date' $where");

            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  t2.amim_olin as sku_print_name,
  round(t1.ordd_uprc,2) as rate,
  t1.ordd_qnty   as quantity,
  round(t1.ordd_opds,2) as discont,
  round(t1.ordd_oamt,2) as price
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            return array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

    public function employeeOrderSummaryPrint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $employee = Employee::on($db_conn)->findorfail($request->emp_id);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $order_amount = 0;
            $data1 = DB::connection($db_conn)->select("SELECT
  t2.amim_id as product_code,
  t4.amim_name as product_name,
  round(t2.ordd_uprc,2) as rate,
  sum(t2.ordd_qnty) as quantity,
  round(sum(t2.ordd_oamt),2) as price
FROM tt_ordm as t1
  INNER JOIN tt_ordd as t2 on t1.id=t2.ordm_id
  INNER JOIN tm_site as t3 on t1.site_id=t3.id
  INNER JOIN tm_amim as t4 on t2.amim_id=t4.id
WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' and t1.aemp_id=$aemp_id
GROUP BY t2.amim_id,t4.amim_name,t2.ordd_uprc");
            if ($data1 != null) {
                $order_amount = number_format(array_sum(array_column($data1, 'price')), 2, '.', '');
            }
            $order_data = array(
                'title' => "Challan Summary",
                'sr_name' => $employee->aemp_name . '(' . $employee->aemp_usnm . ')',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_amount' => $order_amount,
                'orderLine' => $data1,
            );
            return array(
                "receive_data" => $order_data,
            );
        }
    }

    public function employeeOrderSummaryPrintNew(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $employee = Employee::on($db_conn)->findorfail($request->emp_id);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $order_amount = 0;
            $total_discount = 0;
            $data1 = DB::connection($db_conn)->select("SELECT
  t2.amim_id as product_code,
  t4.amim_name as product_name,
  round(t2.ordd_uprc,2) as rate,
  sum(t2.ordd_qnty) as quantity,
  round(sum(t2.ordd_oamt),2) as price,
   round(sum(t2.ordd_opds),2) as discount,
   t4.amim_olin as print_product_name
FROM tt_ordm as t1
  INNER JOIN tt_ordd as t2 on t1.id=t2.ordm_id
  INNER JOIN tm_site as t3 on t1.site_id=t3.id
  INNER JOIN tm_amim as t4 on t2.amim_id=t4.id
WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' and t1.aemp_id=$aemp_id
GROUP BY t2.amim_id,t4.amim_name,t2.ordd_uprc");
            if ($data1 != null) {
                $order_amount = number_format(array_sum(array_column($data1, 'price')), 2, '.', '');
                $total_discount = number_format(array_sum(array_column($data1, 'discount')), 2, '.', '');
            }
            $order_data = array(
                'title' => "Challan Summary",
                'sr_name' => $employee->aemp_name . '(' . $employee->aemp_usnm . ')',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_amount' => $order_amount,
                'total_discount' => $total_discount,
                'orderLine' => $data1,
            );
            return array(
                "receive_data" => $order_data,
            );
        }
    }

    public function employeeOrderSummaryPrintChallan(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $employee = Employee::on($db_conn)->findorfail($request->emp_id);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $order_amount = 0;
            $total_discount = 0;
            $data1 = DB::connection($db_conn)->select("SELECT
  t2.amim_id as product_code,
  t4.amim_name as product_name,
  round(t2.ordd_uprc,2) as rate,
  sum(t2.ordd_qnty) as quantity,
  round(sum(t2.ordd_oamt),2) as price,
   round(sum(t2.ordd_opds),2) as discount,
   t4.amim_olin as print_product_name,
   t1.ordm_date AS order_date,
   t1.ordm_drdt AS delivery_date,
   t5.dlrm_code AS dealer_code,
   t5.dlrm_name as dealer_name,
   t4.amim_duft AS product_factor
FROM tt_ordm as t1
  INNER JOIN tt_ordd as t2 on t1.id=t2.ordm_id
  INNER JOIN tm_site as t3 on t1.site_id=t3.id
  INNER JOIN tm_amim as t4 on t2.amim_id=t4.id
  INNER JOIN tm_dlrm as t5 ON t1.dlrm_id=t5.id
WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' and t1.aemp_id=$aemp_id
GROUP BY t2.amim_id,t4.amim_name,t2.ordd_uprc,t1.ordm_date,t1.ordm_drdt,t5.dlrm_code");
            if ($data1 != null) {
                $order_amount = number_format(array_sum(array_column($data1, 'price')), 2, '.', '');
                $total_discount = number_format(array_sum(array_column($data1, 'discount')), 2, '.', '');
            }
            $order_data = array(
                'title' => "Challan Summary",
                'sr_name' => $employee->aemp_name . '(' . $employee->aemp_usnm . ')',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_amount' => $order_amount,
                'total_discount' => $total_discount,
                'orderLine' => $data1,
            );
            return array(
                "receive_data" => $order_data,
            );
        }
    }
}
