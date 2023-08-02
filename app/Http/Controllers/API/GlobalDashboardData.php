<?php


namespace App\Http\Controllers\API;

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
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.user_count                    AS totalSr,
  t1.is_present                                              as onSr,
  t1.is_active                                              as actSr,    
  t1.is_productive                                              as pvSr,   
  t1.user_count - t1.is_present   AS offSr,  
  t1.is_present - t1.is_active    AS inactSr,
  t1.is_active - t1.is_productive AS nveSr,  
  format(IFNULL((t1.total_memo / t1.total_site) * 100, 0),2) AS strikeRate,
  t1.is_present - t1.is_productive AS nonProductiveSr,
  t1.total_memo                    AS productiveMemo,  
  t1.total_visited- t1.total_memo  AS nonProductiveMemo,
  t1.total_site                    AS totalScheduleCall,
  t1.total_visited                 AS total_visited,
  t1.number_item_line              AS number_item_line,
  round(t1.number_item_line/t1.total_memo,2)     AS lineParCall,
  t1.total_target/1000                  AS totalTargetAmount,  
  0                                AS totalMspTargetCtn,
  t1.total_order/1000              AS totalOrderAmount,
  0                               AS totalMspOrderCtn,
  round(t1.credit_block_count+t1.over_due_count+t1.special_count,2)                              AS blockOrder,
  round(t1.credit_block_amount+t1.over_due_amount+t1.special_amount/1000,2)                                AS blockOrderAmount,
  round(t1.total_budget/1000,2)                                AS supervisorBudgetAmount,  
  round(t1.total_avail/1000,2)                                AS supervisorBudgetAvail,
  round(t1.credit_block_count,2)                                AS creditBlockOrder,
  round(t1.credit_block_amount/1000,2)                                AS creditBlockAmount,
  round(t1.over_due_count,2)                                AS overDueBlockOrder,
  round(t1.over_due_amount/1000,2)                                AS overDueBlockAmount,
  round(t1.special_count,2)                                AS specialBlockOrder,  
  round(t1.special_amount/1000,2)                                AS specialBlockAmount,
  t1.mtd_total_sales/1000               AS mtd_total_sales,
  t1.mtd_total_delivery/1000            AS mtd_total_delivery,
  IFNULL(t2.mtd_total_sales/1000, 0)                               AS last_mtd_sales,
  IFNULL(t2.mtd_total_delivery/1000, 0)                            AS last_mtd_delivery,
  t1.local_date_time                   AS updated_at
FROM tblh_dashboard_data AS t1 
LEFT JOIN tblh_dashboard_data AS t2
    ON t1.user_id = t2.user_id AND t2.date = DATE_SUB('$date', INTERVAL 1 MONTH)
WHERE t1.user_id = '$user_id' AND t1.date = '$date'");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function dashBoardSrDate(Request $request)
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
  if(t1.total_order > 0, t1.total_order/1000, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales/1000, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery/1000, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target/1000, 0)             AS total_target,
  '0'                                                      AS attendance,
  if(t1.is_productive > 0, t1.is_productive, 0)           AS is_productive,
  0                                              AS emp_code,
  t1.master_role_id                                       AS role_id,
  t2.name                                                      AS role_name,
  0                                              AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery/1000, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery/1000, 0) AS last_year_mtd_total_delivery,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblh_dashboard_data AS t7
    ON t1.user_id = t7.user_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
WHERE t1.manager_id = '$user_id' AND t1.date = '$date'
UNION ALL
SELECT
  t1.user_id                                              AS sr_id,
  ''                                                      AS sr_mobile,
  t1.name                                                 AS sr_name,  
  t1.short_name                                           AS short_sr_name,
  ''                                                      AS sr_region,
  if(t1.total_order > 0, t1.total_order/1000, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales/1000, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery/1000, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target/1000, 0)             AS total_target,
  '0'                                                      AS attendance,
  if(t1.is_productive > 0, t1.is_productive, 0)           AS is_productive,
  0                                              AS emp_code,
  t1.master_role_id                                       AS role_id,
  t2.name                                                      AS role_name,
  0                                              AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery/1000, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery/1000, 0) AS last_year_mtd_total_delivery,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
  inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblh_dashboard_data AS t7
    ON t1.user_id = t7.user_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
  INNER JOIN tblh_dashboard_permission as t8 ON t1.user_id=t8.pr_user_id
WHERE t8.user_id = '$user_id' AND t1.date = '$date'
");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function dashBoard1SrDate(Request $request)
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
  if(t1.total_order > 0, t1.total_order/1000, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales/1000, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery/1000, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target/1000, 0)             AS total_target,
  '0'                                                      AS attendance,
  if(t1.is_productive > 0, t1.is_productive, 0)           AS is_productive,
  0                                              AS emp_code,
  t1.master_role_id                                       AS role_id,
  t2.name                                                      AS role_name,
  0                                              AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery/1000, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery/1000, 0) AS last_year_mtd_total_delivery,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblh_dashboard_data AS t7
    ON t1.user_id = t7.user_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
WHERE t1.manager_id = '$user_id' AND t1.date = '$date'
UNION ALL
SELECT
  t1.user_id                                              AS sr_id,
  ''                                                      AS sr_mobile,
  t1.name                                                 AS sr_name,  
  t1.short_name                                           AS short_sr_name,
  ''                                                      AS sr_region,
  if(t1.total_order > 0, t1.total_order/1000, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales/1000, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery/1000, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target/1000, 0)             AS total_target,
  '0'                                                      AS attendance,
  if(t1.is_productive > 0, t1.is_productive, 0)           AS is_productive,
  0                                              AS emp_code,
  t1.master_role_id                                       AS role_id,
  t2.name                                                      AS role_name,
  0                                              AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery/1000, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery/1000, 0) AS last_year_mtd_total_delivery,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
  inner join tbld_master_role as t2 On t1.master_role_id=t2.id
  LEFT JOIN tblh_dashboard_data AS t6
    ON t1.user_id = t6.user_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblh_dashboard_data AS t7
    ON t1.user_id = t7.user_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
  INNER JOIN tblh_dashboard_permission as t8 ON t1.user_id=t8.pr_user_id
WHERE t8.user_id = '$user_id' AND t1.date = '$date'
");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function dashBoard1NonSrDate(Request $request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.user_id                      AS sr_id,
  t5.mobile_no                    AS mobile_no,
  t2.role_name                    AS role_name,
  t2.id                           AS role_id,
  t1.name                         AS sr_name,
  t1.short_name                   AS short_sr_name,
  t1.user_count                   AS tSr,
  t1.is_present                   AS onSr,
  t1.user_count - t1.is_present   AS offSr,
  t1.is_active                    AS actSr,
  t1.is_present - t1.is_active    AS inactSr,
  t1.is_productive                AS pveSr,
  t1.is_active - t1.is_productive AS nveSr,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
  INNER JOIN tm_role AS t2 ON t1.master_role_id = t2.id
  LEFT JOIN tblt_user_tracking AS t5 ON t1.user_id = t5.user_name
WHERE t1.manager_id = '$user_id' AND t1.date = '$date' AND
      (t1.user_count - t1.is_present > 0 OR t1.is_present - t1.is_active > 0 OR t1.is_active - t1.is_productive > 0)");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function dashBoard1ProdSrDate(Request $request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.user_id                      AS sr_id,
  t5.mobile_no                    AS mobile_no,
  t2.role_name                    AS role_name,
  t2.id                           AS role_id,
  t1.name                         AS sr_name,
  t1.short_name                   AS short_sr_name,
  t1.user_count                   AS tSr,
  t1.is_present                   AS onSr,
  t1.user_count - t1.is_present   AS offSr,
  t1.is_active                    AS actSr,
  t1.is_present - t1.is_active    AS inactSr,
  t1.is_productive                AS pveSr,
  t1.is_active - t1.is_productive AS nveSr,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
  INNER JOIN tm_role AS t2 ON t1.master_role_id = t2.id
  LEFT JOIN tblt_user_tracking AS t5 ON t1.user_id = t5.user_name
WHERE t1.manager_id = '$user_id' AND t1.date = '$date' AND
      (t1.is_present > 0 OR t1.is_active > 0 OR t1.is_productive > 0)");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function dashBoard1SiteSrDate(Request $request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.user_id                                                  AS sr_id,
  t5.mobile_no                                                AS mobile_no,
  t2.role_name                                                AS role_name,
  t2.id                                                       AS role_id,
  t1.name                                                     AS sr_name,
  t1.short_name                                               AS short_sr_name,
  t1.total_site                                               AS tSite,
  t1.total_visited                                            AS vSite,
  t1.total_memo                                               AS mSite,
  round(IFNULL((t1.total_memo / t1.total_site) * 100, 0), 2) AS strike_rate,
  t1.number_item_line               AS numberItemLine,
  round(IFNULL(t1.number_item_line / t1.total_memo,0), 2)               AS lpc,
  round(t1.total_order / 1000,2)                             AS pe,
  t1.country_id                                                AS country_id
FROM tblh_dashboard_data AS t1
  INNER JOIN tm_role AS t2 ON t1.master_role_id = t2.id
  LEFT JOIN tblt_user_tracking AS t5 ON t1.user_id = t5.user_name
WHERE t1.manager_id = '$user_id' AND t1.date = '$date';");
        return Array("receive_data" => $employee, "action" => $user_id);
    }
    public function dashBoard1SiteAvgSrDate(Request $request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.user_id                                AS sr_id,
  t5.mobile_no                              AS mobile_no,
  t2.role_name                              AS role_name,
  t2.id                                     AS role_id,
  t1.name                                   AS sr_name,
  t1.total_site                             AS tSite,
  round(t1.total_site / t1.user_count, 2)   AS atSite,
  round(t1.total_visited / t1.is_active, 2) AS avVisit,
  t1.total_visited                          AS total_visited,
  round(t1.total_memo / t1.is_active, 2)    AS avMemo,
  t1.total_memo                             AS total_memo,
  round(t1.total_order / t1.is_active, 2)   AS av_pe,
  round(t1.total_order / t1.total_memo, 2)  AS av_pe_olt,
  t1.total_order                            as total_order,
  t1.user_count                             AS sr_count,
  t1.is_active                              AS active_count,
  t1.country_id                             AS country_id
FROM tblh_dashboard_data AS t1
  INNER JOIN tm_role AS t2 ON t1.master_role_id = t2.id
  LEFT JOIN tblt_user_tracking AS t5 ON t1.user_id = t5.user_name
WHERE t1.manager_id = '$user_id' AND t1.date = '$date';");
        return Array("receive_data" => $employee, "action" => $user_id);
    }

    public function srDashboard(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.name,
  t1.total_site,
  t1.total_visited,
  t1.total_memo,
  t1.total_order_exception,
  t1.number_item_line,
  t1.total_order,
  round(t1.number_item_line/t1.total_memo,2) as line_per_call,
  round(IFNULL((t1.total_memo / t1.total_site) * 100, 0),2)  as strikeRate,
  t1.total_target/26 as  todaysTarget,
  round(t1.total_order,2) as  todaysOrder,
  t1.total_target   as totalTarget,
  t1.mtd_total_sales  as totalAchive,
  t1.mtd_total_delivery                                      AS totalDelivery
FROM tblh_dashboard_data AS t1
WHERE t1.user_id = '$request->user_name' AND t1.date = '$request->date'");

        return Array(
            "receive_data_summary" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public function srOrder(Request $request)
    {
        $srId = $request->srId;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $data1 = DB::connection('pran_live')->select("SELECT
  t1.Order_ID                               AS order_id,
  sum(t1.Total_Item_Price)                  AS order_amount,
  concat(t1.OutLet_ID, '-', t1.OutLet_Name) AS site_name,
  t1.Date                                   AS order_date,
  1                                         AS status_id,
  'Pending'                                 AS status,
  0                                         AS invoice_amount,
  1                                         AS type_id
FROM order_table AS t1
  INNER JOIN outletmaster AS t2 ON t1.OutLet_ID = t2.Outlet_ID
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Order_ID
");

        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public function srOrderNew(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $srId = str_replace($country->cont_name,"",$request->srId);
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $query="SELECT
  t1.Order_ID                               AS order_id,
  sum(t1.Total_Item_Price)                  AS order_amount,
  concat(t1.OutLet_ID, '-', t1.OutLet_Name) AS site_name,
  t1.Date                                   AS order_date,
  1                                         AS status_id,
  'Pending'                                 AS status,
  0                                         AS invoice_amount,
  1                                         AS type_id
FROM order_table AS t1
  INNER JOIN outletmaster AS t2 ON t1.OutLet_ID = t2.Outlet_ID
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Order_ID";
            if ($request->country_id==3){
                $query="SELECT
  t1.Order_ID                               AS order_id,
  sum(t1.Total_Item_Price)                  AS order_amount,
  concat(t1.OutLet_ID, '-', t1.OutLet_Name) AS site_name,
  t1.Date                                   AS order_date,
  1                                         AS status_id,
  'Pending'                                 AS status,
  0                                         AS invoice_amount,
  1                                         AS type_id
FROM order_table AS t1
  INNER JOIN outletmaster AS t2 ON t1.OutLet_ID = t2.Site_ID
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Order_ID";
            }

            $data1 = DB::connection($country->cont_con2)->select($query);
            return Array(
                "receive_data" => array("data" => $data1, "action" => $srId),
            );
        }
    }

    public function srProduct(Request $request)
    {
        $srId = $request->srId;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $data1 = DB::connection('pran_live')->select("SELECT
  t1.Product_id            AS product_id,
  t2.Item_Name             AS product_name,
  t2.Unit                  AS ctn_size,
  sum(t1.Product_Quantity) AS order_qty,
  0                        AS delivary_qty,
  sum(t1.Discount)         AS discount,
  sum(t1.Total_Item_Price) AS total_amount,
  0                        AS target_amount,
  0                        AS target_qty,
  0                        AS net_amount,
  1                        AS type_id,
  t2.Category_Name         AS sub_category
FROM order_table AS t1
  INNER JOIN products_info AS t2 ON t1.Product_id = t2.Item_Code and t1.Group_ID=t2.Group_Code
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Product_id
");

        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public function srProductNew(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $srId = str_replace($country->cont_name,"",$request->srId);
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $query="SELECT
  t1.Product_id            AS product_id,
  t2.Item_Name             AS product_name,
  t2.Unit                  AS ctn_size,
  sum(t1.Product_Quantity) AS order_qty,
  0                        AS delivary_qty,
  sum(t1.Discount)         AS discount,
  sum(t1.Total_Item_Price) AS total_amount,
  0                        AS target_amount,
  0                        AS target_qty,
  0                        AS net_amount,
  1                        AS type_id,
  t2.Category_Name         AS sub_category
FROM order_table AS t1
  INNER JOIN products_info AS t2 ON t1.Product_id = t2.Item_Code and t1.Group_ID=t2.Group_Code
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Product_id";
            if ($request->country_id==3){
                $query="SELECT
  t1.Product_id            AS product_id,
  t2.Item_Name             AS product_name,
  t2.Unit                  AS ctn_size,
  sum(t1.Product_Quantity) AS order_qty,
  0                        AS delivary_qty,
  sum(t1.Discount)         AS discount,
  sum(t1.Total_Item_Price) AS total_amount,
  0                        AS target_amount,
  0                        AS target_qty,
  0                        AS net_amount,
  1                        AS type_id,
  t2.Category_Name         AS sub_category
FROM order_table AS t1
  INNER JOIN products_info AS t2 ON t1.Product_id = t2.Item_Code
WHERE t1.SR_ID = '$srId' AND t1.Date BETWEEN '$startDate' AND '$endDate'
GROUP BY t1.Product_id";
            }

            $data1 = DB::connection($country->cont_con2)->select($query);

            return Array(
                "receive_data" => array("data" => $data1, "action" => $srId),
            );
        }
    }


}
