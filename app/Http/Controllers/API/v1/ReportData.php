<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Employee;
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
        if ($country) {
            $aemp_id = $request->emp_id;
            $date = $request->date;
//t1.dhbd_oexc                                             AS not_order,
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  t2.aemp_name                                             AS route_name,
  t1.dhbd_tsit                                             AS site_count,
  t1.dhbd_memo                                             AS memo_count,
 t1.dhbd_tvit - t1.dhbd_memo                               AS not_order,
  t1.dhbd_tsit - t1.dhbd_memo - t1.dhbd_oexc               AS pending,
  round(IFNULL((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
  t1.dhbd_line                                             AS totalLine,
  round(t1.dhbd_line / t1.dhbd_memo, 2)                    AS lineParCall,
  t1.dhbd_ttar / 26                                        AS todaysTarget,
  round(t1.dhbd_tamt, 2)                                   AS todaysOrder,
  t1.dhbd_ttar                                             AS totalTarget,
  t1.dhbd_mtdo                                             AS totalAchive,
  t1.dhbd_mtdd                                             AS totalDelivery,
  DATE_FORMAT(t1.dhbd_time, '%h:%i %p')                    AS time_gen
FROM th_dhbd_5 AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
WHERE t1.aemp_id = $aemp_id AND t1.dhbd_date = '$date'");

            return Array("receive_data" => $data1, "action" => $request->emp_id);
        }
    }

    public function onReferenceItem(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $site_id = $request->site_id;
            $amim_id = $request->amim_id;
            $acmp_id = $request->acmp_id;
            $data = DB::connection($country->cont_conn)->select("SELECT
  t1.ordm_drdt                                                AS delivery_date,
  t2.ordm_ornm                                                AS order_id,
  t2.ordd_excs                                                AS gst,
  t2.ordd_ovat                                                AS vat,
  t2.ordd_dqty - t2.ordd_rqty                                 AS qty,
  t2.ordd_uprc*t2.ordd_duft                                   AS ctn_price,
  (t2.ordd_spdd + t2.ordd_dpds + t2.ordd_dfdd) / t2.ordd_dqty AS discount_ratio
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
WHERE t1.site_id = '$site_id' AND t2.amim_id = '$amim_id' AND t2.ordd_smpl = 0 AND
      t1.acmp_id = '$acmp_id' AND
      t2.ordd_dqty - t2.ordd_rqty > 0
ORDER BY t1.id DESC");
            return Array("receive_data" => array("data" => $data, "action" => $request->site_id));
        }
    }

    public function outOfStockData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.dpot_id,
  t2.dpot_name,
  t1.amim_id,
  t4.amim_name
FROM tt_outs AS t1
  INNER JOIN tm_dpot AS t2 ON t1.dpot_id = t2.id
  INNER JOIN tm_acmp AS t3 ON t2.acmp_id = t3.id
  INNER JOIN tm_amim AS t4 ON t1.amim_id = t4.id;");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function priceListData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS price_id,
  concat( t1.plmt_name,'(',t1.plmt_code,')' ) AS price_name
FROM tm_plmt AS t1;");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function myTeamData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id as emp_id,
  concat(t1.aemp_name, '(', t1.aemp_usnm, ')') as emp_name
  FROM tm_aemp AS t1
  WHERE t1.id = $request->emp_id OR t1.aemp_mngr = $request->emp_id AND t1.lfcl_id=1");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function priceListLineData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t2.amim_id                                           AS sku_id,
  t3.amim_name                                         AS sku_name,
  t3.amim_code                                         AS sku_code,
  t4.id                                                AS sub_category_id,
  t4.itcg_name                                         AS sub_category_name,
  t2.pldt_tppr * t2.amim_duft                          AS ctn_price,
  t2.pldt_tpgp * t2.amim_duft                          AS grv_ctn_price,
  t2.pldt_mrpp * t2.amim_duft                          AS mrp_price,
  t2.amim_duft                                         AS ctn_size,
  t2.lfcl_id                                           AS status_id,
  0                                                    AS out_of_stock,
  t3.amim_pvat                                         AS vat,
  (t3.amim_pexc / (t2.pldt_tppr * t2.amim_duft)) * 100 AS gst
FROM tm_plmt AS t1
  INNER JOIN tm_pldt AS t2 ON t1.id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_itcg AS t4 ON t3.itcl_id = t4.id
  where t1.id=$request->price_list_id");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function priceListSearchData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  concat( t1.plmt_name,'(',t1.plmt_code,')' )                                              AS price_name,
  t2.amim_id                                           AS sku_id,
  t3.amim_name                                         AS sku_name,
  t3.amim_code                                         AS sku_code,
  t4.id                                                AS sub_category_id,
  t4.itcg_name                                         AS sub_category_name,
  t2.pldt_tppr * t2.amim_duft                          AS ctn_price,
  t2.pldt_tpgp * t2.amim_duft                          AS grv_ctn_price,
  t2.pldt_mrpp * t2.amim_duft                          AS mrp_price,
  t2.amim_duft                                         AS ctn_size,
  t2.lfcl_id                                           AS status_id,
  0                                                    AS out_of_stock,
  t3.amim_pvat                                         AS vat,
  (t3.amim_pexc / (t2.pldt_tppr * t2.amim_duft)) * 100 AS gst
FROM tm_plmt AS t1
  INNER JOIN tm_pldt AS t2 ON t1.id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_itcg AS t4 ON t3.itcl_id = t4.id
WHERE t3.amim_code like '%$request->search_text%' or t3.amim_name like '%$request->search_text%' ");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function outOfStockSearchData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.amim_id,
  t3.amim_code,
  t3.amim_name,
  t2.dpot_code,
  t2.dpot_name,
  t3.itsg_id,
  t4.itsg_name
FROM tt_outs AS t1
  INNER JOIN tm_dpot AS t2 ON t1.dpot_id = t2.id
  INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
  INNER JOIN tm_itsg AS t4 ON t3.itsg_id = t4.id
WHERE t3.amim_name LIKE '%$request->search_text%' OR t3.amim_code LIKE '%$request->search_text%';");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function outOfStockDepotItemData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.amim_id,
  t3.amim_code,
  t3.amim_name,
  t2.dpot_code,
  t2.dpot_name,
  t3.itsg_id,
  t4.itsg_name
FROM tt_outs AS t1
  INNER JOIN tm_dpot AS t2 ON t1.dpot_id = t2.id
  INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
  INNER JOIN tm_itsg AS t4 ON t3.itsg_id = t4.id
WHERE t2.id = $request->depot_id;");

            return array("receive_data" => $tst, "action" => $request->country_id);
        }
    }

    public function outOfStockDepotData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.id as dpot_id,
  t1.dpot_name
FROM tm_dpot AS t1 INNER JOIN tt_outs AS t2 ON t1.id = t2.dpot_id
GROUP BY t1.id, t1.dpot_name;");

            return array("receive_data" => $tst, "action" => $request->country_id);
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
  t2.rout_name AS route_name,
  t2.base_id,
  t3.base_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tm_base AS t3 ON t2.base_id = t3.id
WHERE t1.aemp_id = $request->emp_id");
            return Array(
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
  t4.site_vrfy        AS is_verified,
  !isnull(t5.site_id) AS visited
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN th_ssvh AS t5 ON t4.id = t5.site_id AND t5.ssvh_date = curdate()
WHERE t1.rout_id = $request->route_id AND t4.lfcl_id = 1 AND t1.aemp_id=$request->emp_id
GROUP BY t4.id, t4.site_name, t4.site_ownm, t4.site_vrfy, t5.site_id");
            return array("receive_data" => $data1, "action" => $request->route_id);
        }
    }


    public function employeeOrderPrint(Request $request)
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
         t3.id        AS site_id,
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
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
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
                $data3 = DB::connection($db_conn)->select("SELECT
  t3.amim_name,
  t2.rtdd_qnty,
   t2.rtdd_uprc *t2.rtdd_qnty as total_amount,
  t2.rtdd_uprc  AS ctn_rate
FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_dprt AS t4 ON t2.dprt_id = t4.id
WHERE t1.site_id = $site_id AND t1.rtan_date = '$order_date'");
                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->returnLine = $data3;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            return Array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

    public function employeeOrderPrintMemo(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $order_master = DB::connection($db_conn)->select("SELECT
         t1.id,
         'Order' as title,
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.id        AS site_id,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_olnm AS print_outlet_name,
         t3.site_olad    print_outlet_address,
         t3.site_mob1 AS outlet_mobile,
         t3.site_adrs AS outlet_address,
         t3.site_ownm AS outlet_owner,
         t4.dlrm_code AS dealer_id,
         t4.dlrm_name AS dealer_name,
         t4.dlrm_mob1 AS dealer_mobile,
         t1.ordm_date AS order_date,
         t1.ordm_drdt AS delivery_date,
         0 as totla_amount,
         0 as totla_discount,
         t6.rout_name,
         t7.base_name,
         t8.zone_code
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
         INNER JOIN tm_rout t6 on t1.rout_id = t6.id
         INNER JOIN tm_base t7 on t6.base_id = t7.id
         INNER JOIN tm_zone t8 on t7.zone_id = t8.id
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");


            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  t2.amim_olin as sku_print_name,
  round(t1.ordd_uprc,2) as rate,
  t1.ordd_qnty   as quantity,
  round((t1.ordd_opds+t1.ordd_spdo+t1.ordd_dfdo),2) as discont,
  round(t1.ordd_oamt,2) as price
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
                $data3 = DB::connection($db_conn)->select("SELECT
  t5.aemp_usnm AS sr_id,
  t1.rtan_date AS order_date,
  t6.site_code AS outlet_id,
  t3.amim_code AS product_code,
  t3.amim_name AS product_name,
  t2.rtdd_qnty AS quantity,
  t2.rtdd_uprc  AS rate,
  t2.rtdd_uprc *t2.rtdd_qnty as reasion,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS column_id,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS token
FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_dprt AS t4 ON t2.dprt_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
  INNER JOIN tm_site AS t6 ON t1.site_id = t6.id
  INNER JOIN tl_srdi t7 ON(t1.dlrm_id=t7.dlrm_id)
WHERE t1.site_id = $site_id AND t1.rtan_date = '$order_date' AND t7.aemp_id=$aemp_id");

                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->returnLine = $data3;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            return Array(
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
            return Array(
                "receive_data" => $order_data,
            );
        }
    }
}