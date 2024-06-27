<?php

namespace App\Process;

use App\MasterData\Country;
use App\MasterData\MasterRole;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class DashboardDataProcess
{


    public function pgDashboardSVData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'pgsv1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblh_dashboard_data')
            ->where(['country_id' => 1, 'date' => $date])->whereIn('master_role_id', [2, 3, 4, 5, 6, 7, 8])->where('service_no', '=', '1')
            ->delete();
        $result = DB::connection('pran_live')->select("SELECT
  '$date'       AS date,
  t1.Name as name,
  IF(LENGTH(t1.SR_ID)<5,CONCAT('0',t1.SR_ID), t1.SR_ID) as user_id,
  t1.short_name         as short_name,
  t1.group_id           as group_id,
  t1.manager_code AS manager_id,
  t1.desig2 as master_role_id,
  0               AS user_count,
  0 AS is_present,
  0 AS is_active,
  0 AS is_productive,
  0 AS total_site,
  0 AS total_visited,
  0 AS total_memo,
  0 AS total_memo,
  0 AS total_order_exception,
  0 AS number_item_line,
  0 AS total_order,
  0 AS total_target,
  0 AS msp_target_ctn,
  0 AS msp_target_order,
  0 AS credit_block_count,
  0 AS credit_block_amount,
  0 AS over_due_count,
  0 AS over_due_amount,
  0 AS special_count,
  0 AS special_amount,
  0                             AS budget_amount,
  0                              AS avail_amount,
  0 AS total_budget,
  0 AS total_avail,
  0 AS mtd_total_sales,
  0 AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') as local_date_time,
  '1' as country_id,
  '1' as service_no
FROM srinfo AS t1
WHERE t1.Status = 'Y' and t1.desig2>1
GROUP BY t1.SR_ID;");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblh_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'pgsv2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    public function prgDashboardSVData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'prgsv1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblh_dashboard_data')
            ->where(['country_id' => 1, 'date' => $date])->whereIn('master_role_id', [5, 6, 7, 8])->where('service_no', '=', '2')
            ->delete();
        $result = DB::connection('myprg_p')->select("SELECT
  '$date'                               AS DATE,
  t1.aemp_name                          AS NAME,
  t1.aemp_stnm                          AS short_name,
  1                                     AS group_id,
  t1.aemp_usnm                          AS user_id,
  t2.aemp_usnm                          AS manager_id,
  t1.role_id                            AS master_role_id,
  0                                     AS user_count,
  0                                     AS is_present,
  0                                     AS is_active,
  0                                     AS is_productive,
  0                                     AS total_site,
  0                                     AS total_visited,
  0                                     AS total_memo,
  0                                     AS total_order_count,
  0                                     AS total_order_exception,
  0                                     AS number_item_line,
  0                                     AS total_order,
  0                                     AS total_target,
  0                                     AS msp_target_ctn,
  0                                     AS msp_target_order,
  0                                     AS credit_block_count,
  0                                     AS credit_block_amount,
  0                                     AS over_due_count,
  0                                     AS over_due_amount,
  0                                     AS special_count,
  0                                     AS special_amount,   
  0                             AS budget_amount,
  0                              AS avail_amount,
  0                                     AS total_budget,
  0                                     AS total_avail,
  0                                     AS mtd_total_sales,
  0                                     AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') AS local_date_time,
  1                                     AS country_id,
  '2'                                   as service_no
FROM tm_aemp AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_mngr = t2.id
WHERE t1.lfcl_id = 1 AND t1.role_id > 5;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblh_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'prgsv2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);
    }

    public function pgDashboardSRData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'pgsr1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblt_dashboard_data')
            ->where(['service_no' => 1, 'date' => $date])
            ->delete();
        $result = DB::connection('pran_live')->select("SELECT
  '$date'       AS date,
  t1.Name as name, 
  IF(LENGTH(t1.SR_ID)<5,CONCAT('0',t1.SR_ID), t1.SR_ID) as user_id,
  t1.short_name         as short_name,
  t1.group_id           as group_id,
  t1.manager_code AS manager_id,
  t1.desig2 as master_role_id,
  1               AS user_count,
  !isnull(t2.Date) AS is_present,
  !isnull(t3.memo)                AS is_active,
  if(t3.memo > 9, 1, 0)                                    AS is_productive,
  t8.t_outlet AS total_site,
  sum(t3.visited) AS total_visited,
  sum(t3.memo) AS total_memo,
  sum(t3.memo) AS total_memo,
  sum(t3.visited) - sum(t3.memo) AS total_order_exception,
  sum(t3.item_count) AS number_item_line,
  t5.total_amount AS total_order,
  t7.target_amount AS total_target,
  0 AS msp_target_ctn,
  0 AS msp_target_order,
  0 AS credit_block_count,
  0 AS credit_block_amount,
  0 AS over_due_count,
  0 AS over_due_amount,
  0 AS special_count,
  0 AS special_amount,
  0                             AS budget_amount,
  0                              AS avail_amount,
  0 AS total_budget,
  0 AS total_avail,
  t6.mtd_total_amount AS mtd_total_sales,
  0                   AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') as local_date_time,
  '1' as country_id,
  '1' as service_no
FROM srinfo AS t1
  LEFT JOIN ( SELECT
                t1.SR_ID,
                t1.Date
              FROM attendence_table AS t1
              WHERE t1.Date ='$date' and lower(t1.Status)='start work'
              GROUP BY t1.SR_ID) AS t2 ON t1.SR_ID = t2.SR_ID
  LEFT JOIN (
              SELECT t1.`SR_ID`, COUNT(t2.Outlet_ID) as t_outlet FROM `groupwiseroute` t1 LEFT JOIN
                routewiseoutlet t2 ON t1.`Route_ID`=t2.Route_ID AND t1.`Group_ID`=t2.Group_Id WHERE
                t1.Day=DAYNAME('$date') GROUP BY t1.SR_ID) as t8 on t1.SR_ID=t8.SR_ID
  LEFT JOIN (
              SELECT
                t2.SR_ID,
                t2.visited,
                t2.t_amount as total_amount,
                t3.t_memo as memo,
                t3.sku as item_count
              FROM (
                     SELECT `SR_ID`, COUNT(DISTINCT OutLet_ID) as visited, SUM(Total_Item_Price) as t_amount FROM `order_table`
                     WHERE Date='$date' AND Order_ID!='' GROUP BY `SR_ID`
                   ) as t2
                LEFT JOIN (
                            SELECT SR_ID, COUNT(OutLet_ID) as t_memo, SUM(skus) as sku from(SELECT `SR_ID`,`OutLet_ID`,
                                                                                              COUNT(DISTINCT Product_id) as skus FROM `order_table` WHERE Date='$date' AND Order_ID!='0' GROUP BY SR_ID,OutLet_ID) as sdd
                            GROUP BY SR_ID
                          ) as t3 ON t2.SR_ID=t3.SR_ID GROUP BY t2.SR_ID
            ) AS t3 ON t1.SR_ID = t3.SR_ID
  LEFT JOIN ( SELECT
                t1.SR_ID,
                t1.Date AS DATE
              FROM order_table AS t1
              WHERE t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND t1.Route_ID != ''
              GROUP BY t1.Date, t1.SR_ID) AS t4 ON t1.SR_ID = t4.SR_ID
  LEFT JOIN ( SELECT
                t1.SR_ID,
                sum(t1.Total_Item_Price) AS total_amount
              FROM order_table AS t1
              WHERE t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND t1.Route_ID != ''
              GROUP BY t1.SR_ID) AS t5 ON t1.SR_ID = t5.SR_ID
  LEFT JOIN (
              SELECT
                t1.SR_ID,
                sum(t1.Total_Item_Price) AS mtd_total_amount
              FROM order_table AS t1
              WHERE
                t1.Date = '$date' AND
                t1.Order_ID NOT IN ('', '0')
                AND t1.Route_ID != ''
              GROUP BY t1.SR_ID
            ) AS t6 ON t1.SR_ID = t6.SR_ID
  LEFT JOIN (
              SELECT
                t1.SR_ID,
                t1.Amount as target_amount
              FROM targetsystem AS t1
              WHERE t1.Month = monthname('$date') AND t1.Year = year('$date') AND t1.SR_ID != 0
              GROUP BY t1.SR_ID
            ) AS t7 ON t1.SR_ID = t7.SR_ID
WHERE t1.desig2 = 1 AND t1.Status = 'Y' 
GROUP BY t1.SR_ID;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblt_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'pgsr2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    public function rgDashboardSVData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'pgsv1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblh_dashboard_data')
            ->where(['country_id' => 5, 'date' => $date])->whereIn('master_role_id', [2, 3, 4, 5, 6, 7, 8])->where('service_no', '=', '5')
            ->delete();
        $result = DB::connection('rfl_live')->select("SELECT
  '$date'       AS date,
  t1.Name as name,
  t1.h_id as user_id,
  t1.short_name         as short_name,
  t1.group_id           as group_id,
  t1.manager_code AS manager_id,
  t1.role as master_role_id,
  0               AS user_count,
  0 AS is_present,
  0 AS is_active,
  0 AS is_productive,
  0 AS total_site,
  0 AS total_visited,
  0 AS total_memo,
  0 AS total_memo,
  0 AS total_order_exception,
  0 AS number_item_line,
  0 AS total_order,
  0 AS total_target,
  0 AS msp_target_ctn,
  0 AS msp_target_order,
  0 AS credit_block_count,
  0 AS credit_block_amount,
  0 AS over_due_count,
  0 AS over_due_amount,
  0 AS special_count,
  0 AS special_amount,
  0                             AS budget_amount,
  0                              AS avail_amount,
  0 AS total_budget,
  0 AS total_avail,
  0 AS mtd_total_sales,
  0 AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') as local_date_time,
  '5' as country_id,
  '5' as service_no
FROM srinfo AS t1
WHERE t1.Status = 'Y' and t1.role>1
GROUP BY t1.SR_ID;");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblh_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'pgsv2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    public function rgDashboardSRData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'pgsr1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblt_dashboard_data')
            ->where(['service_no' => 5, 'date' => $date])
            ->delete();
        $result = DB::connection('rfl_live')->select("SELECT
  '$date'       AS date,
  t1.Name as name, 
  IF(LENGTH(t1.SR_ID)<5,CONCAT('0',t1.SR_ID), t1.SR_ID) as user_id,
  t1.short_name         as short_name,
  t1.group_id           as group_id,
  t1.manager_code AS manager_id,
  t1.role as master_role_id,
  1               AS user_count,
  !isnull(t2.Date) AS is_present,
  !isnull(t3.memo)                AS is_active,
  if(t3.memo > 9, 1, 0)                                    AS is_productive,
  t8.t_outlet AS total_site,
  sum(t3.visited) AS total_visited,
  sum(t3.memo) AS total_memo,
  sum(t3.memo) AS total_memo,
  sum(t3.visited) - sum(t3.memo) AS total_order_exception,
  sum(t3.item_count) AS number_item_line,
  t5.total_amount AS total_order,
  t7.target_amount AS total_target,
  0 AS msp_target_ctn,
  0 AS msp_target_order,
  0 AS credit_block_count,
  0 AS credit_block_amount,
  0 AS over_due_count,
  0 AS over_due_amount,
  0 AS special_count,
  0 AS special_amount,
  0                             AS budget_amount,
  0                              AS avail_amount,
  0 AS total_budget,
  0 AS total_avail,
  t6.mtd_total_amount AS mtd_total_sales,
  0                   AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') as local_date_time,
  '5' as country_id,
  '5' as service_no
FROM srinfo AS t1
  LEFT JOIN ( SELECT
                t1.SR_ID,
                t1.Date
              FROM attendence_table AS t1
              WHERE t1.Date ='$date' and lower(t1.Status)='start work'
              GROUP BY t1.SR_ID) AS t2 ON t1.SR_ID = t2.SR_ID
  LEFT JOIN (
              SELECT t1.`SR_ID`, COUNT(t2.Outlet_ID) as t_outlet FROM `groupwiseroute` t1 LEFT JOIN
                routewiseoutlet t2 ON t1.`Route_ID`=t2.Route_ID AND t1.`Group_ID`=t2.Group_Id WHERE
                t1.Day=DAYNAME('$date') GROUP BY t1.SR_ID) as t8 on t1.SR_ID=t8.SR_ID
  LEFT JOIN (
              SELECT
                t2.SR_ID,
                t2.visited,
                t2.t_amount as total_amount,
                t3.t_memo as memo,
                t3.sku as item_count
              FROM (
                     SELECT `SR_ID`, COUNT(DISTINCT OutLet_ID) as visited, SUM(Total_Item_Price) as t_amount FROM `order_table`
                     WHERE Date='$date' AND Order_ID!='' GROUP BY `SR_ID`
                   ) as t2
                LEFT JOIN (
                            SELECT SR_ID, COUNT(OutLet_ID) as t_memo, SUM(skus) as sku from(SELECT `SR_ID`,`OutLet_ID`,
                                                                                              COUNT(DISTINCT Product_id) as skus FROM `order_table` WHERE Date='$date' AND Order_ID!='0' GROUP BY SR_ID,OutLet_ID) as sdd
                            GROUP BY SR_ID
                          ) as t3 ON t2.SR_ID=t3.SR_ID GROUP BY t2.SR_ID
            ) AS t3 ON t1.SR_ID = t3.SR_ID
  LEFT JOIN ( SELECT
                t1.SR_ID,
                t1.Date AS DATE
              FROM order_table AS t1
              WHERE t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND t1.Route_ID != ''
              GROUP BY t1.Date, t1.SR_ID) AS t4 ON t1.SR_ID = t4.SR_ID
  LEFT JOIN ( SELECT
                t1.SR_ID,
                sum(t1.Total_Item_Price) AS total_amount
              FROM order_table AS t1
              WHERE t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND t1.Route_ID != ''
              GROUP BY t1.SR_ID) AS t5 ON t1.SR_ID = t5.SR_ID
  LEFT JOIN (
              SELECT
                t1.SR_ID,
                sum(t1.Total_Item_Price) AS mtd_total_amount
              FROM order_table AS t1
              WHERE
                t1.Date = '$date' AND
                t1.Order_ID NOT IN ('', '0')
                AND t1.Route_ID != ''
              GROUP BY t1.SR_ID
            ) AS t6 ON t1.SR_ID = t6.SR_ID
  LEFT JOIN (
              SELECT
                t1.SR_ID,
                t1.Amount as target_amount
              FROM targetsystem AS t1
              WHERE t1.Month = monthname('$date') AND t1.Year = year('$date') AND t1.SR_ID != 0
              GROUP BY t1.SR_ID
            ) AS t7 ON t1.SR_ID = t7.SR_ID
WHERE t1.role = 1 AND t1.Status = 'Y' 
GROUP BY t1.SR_ID;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblt_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'pgsr2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    public function uaeDashboardSVData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'usesr1',
            'type_name' => 'uaedash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblh_dashboard_data')
            ->where(['country_id' => 3, 'date' => $date])->whereIn('master_role_id', [2, 3, 4, 5, 6, 7, 8])->where('service_no', '=', '3')
            ->delete();
        $result = DB::connection('uae_live')->select("SELECT
  '$date'                             AS date,
  t1.name                               AS name,
  concat('UAE', REPLACE(t1.user_name, 'S', '0'))           AS user_id,
  ''                                    AS short_name,
  ''                                    AS group_id,
  concat('UAE', REPLACE(t12.user_name, 'S', '0'))         AS manager_id,
  t1.master_role_id                     AS master_role_id,
  0                                     AS user_count,
  0                                     AS is_present,
  0                                     AS is_active,
  0                                     AS is_productive,
  0                                     AS total_site,
  0                                     AS total_visited,
  0                                     AS total_memo,
  0                                     AS total_memo,
  0                                     AS total_order_exception,
  0                                     AS number_item_line,
  0                                     AS total_order,
  0                                     AS total_target,
  0                                     AS msp_target_ctn,
  0                                     AS msp_target_order,
  0                                     AS credit_block_count,
  0                                     AS credit_block_amount,
  0                                     AS over_due_count,
  0                                     AS over_due_amount,
  0                                     AS special_count,
  0                                     AS special_amount,
  t5.budget                             AS budget_amount,
  t5.avail                              AS avail_amount,
  0                                     AS total_budget,
  0                                     AS total_avail,
  0                                     AS total_budget,
  0                                     AS total_avail,
  0                                     AS mtd_total_sales,
  0                                     AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') AS local_date_time,
  '3'                                   AS country_id,
  '3'                                   as service_no
  
FROM tbld_employee AS t1
LEFT JOIN (
              SELECT
                t1.emp_code,
                t1.budget + if(sum(t3.budget), sum(t3.budget), 0) AS budget,
                t1.avail
              FROM tblt_credit_budget AS t1
                LEFT JOIN tblt_supervisor_budget_log AS t3
                  ON t1.emp_code = t3.emp_code AND t1.month = t3.month AND t1.year = t3.year
              WHERE t1.month = MONTH('$date') AND t1.year = YEAR('$date')
              GROUP BY t1.emp_code
            ) AS t5 ON t1.emp_code = t5.emp_code
  INNER JOIN tbld_employee AS t12 ON t1.manager_code = t12.emp_code
WHERE t1.master_role_id > 1 AND t1.status_id = 33
GROUP BY t1.emp_code;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblh_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'uaesr2',
            'type_name' => 'uaedash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    public function uaeDashboardSRData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'usesr1',
            'type_name' => 'uaedash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblt_dashboard_data')
            ->where(['service_no' => 3, 'date' => $date])
            ->delete();
        $result = DB::connection('uae_live')->select("SELECT
  '$date'                                           AS date,
  t1.name                                             AS name,
  concat('UAE', REPLACE(t1.user_name, 'S', '0'))      AS user_id,
  ''                                                  AS short_name,
  ''                                                  AS group_id,
  concat('UAE', REPLACE(t12.user_name, 'S', '0'))     AS manager_id,
  t1.master_role_id                                   AS master_role_id,
  1                                                   AS user_count,
  !isnull(t2.Date)                                    AS is_present,
  !isnull(t9.number_of_memo)                          AS is_active,
  if(t9.number_of_memo > 9, 1, 0)                    AS is_productive,
  t3.total_site                                       AS total_site,
  t3.visited                                          AS total_visited,
  t9.number_of_memo                                   AS total_memo,
  t3.visited - t3.memo                                AS total_order_exception,
  t11.item_count                                      AS number_item_line,
  t9.total_amount + t9.vat_amount + t9.excise_duty    AS total_order,
  t4.target_amount                                    AS total_target,
  0                                                   AS msp_target_ctn,
  0                                                   AS msp_target_order,
  t8.order_count                                      AS credit_block_count,
  t8.order_amount                                     AS credit_block_amount,
  t7.order_count                                      AS over_due_count,
  t7.order_amount                                     AS over_due_amount,
  t6.order_count                                      AS special_count,
  t6.order_amount                                     AS special_amount,
  t5.budget                                           AS budget_amount,
  0                                                   AS total_budget,
  0                                                   AS total_avail,
  t5.avail                                            AS avail_amount,
  t10.total_amount + t10.vat_amount + t10.excise_duty AS mtd_total_sales,
  t10.delivery_amount                                 AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+04:00')               AS local_date_time,
  '3'                                                 AS country_id,
  '3'                                                 AS service_no
FROM tbld_employee AS t1
  LEFT JOIN (SELECT
               t1.SR_ID,
               t1.Date
             FROM attendence_table AS t1
             WHERE t1.Date = '$date'
             GROUP BY t1.SR_ID) AS t2 ON t1.user_name = t2.SR_ID
  LEFT JOIN (
              SELECT
                t1.SR_ID,
                count(t2.Sites_ID)   AS total_site,
                t1.Route_ID          AS route_id,
                count(t3.OutLet_ID)  AS visited,
                count(t4.OutLet_ID)  AS memo,
                sum(t4.item_count)   AS item_count,
                sum(t4.total_amount) AS total_amount

              FROM groupwiseroute AS t1
                INNER JOIN routesites AS t2 ON t1.Route_ID = t2.Route_ID
                LEFT JOIN (SELECT
                             t1.SR_ID,
                             t1.OutLet_ID
                           FROM order_table AS t1
                           WHERE t1.Date = '$date' AND t1.Route_ID != ''
                           GROUP BY t1.SR_ID, t1.OutLet_ID) AS t3
                  ON t1.SR_ID = t3.SR_ID AND t2.Sites_ID = t3.OutLet_ID
                LEFT JOIN (SELECT
                             DISTINCT
                             count(t1.Product_id)                                AS item_count,
                             t1.SR_ID,
                             t1.OutLet_ID,
                             t1.Order_ID,
                             sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                                            t1.default_discount) AS total_amount
                           FROM order_table AS t1
                           WHERE t1.Date = '$date' AND t1.order_status NOT IN (15, 16, 18, 21) AND
                                 t1.Order_ID NOT IN ('', '0') AND t1.Route_ID != ''
                           GROUP BY t1.SR_ID, t1.OutLet_ID) AS t4
                  ON t1.SR_ID = t4.SR_ID AND t2.Sites_ID = t4.OutLet_ID
                INNER JOIN outletmaster AS t5 ON t2.Sites_ID = t5.Site_ID
              WHERE
                t1.Day = dayname('$date') AND t5.Outlet_Status = 'Y' AND t5.Region_Code NOT IN ('R0990', 'R0980')
                AND t5.subchannel_id != 'SCH051' AND t1.Route_ID != 'EMP0001'
              GROUP BY t1.SR_ID
            ) AS t3 ON t1.user_name = t3.SR_ID
  LEFT JOIN (
              SELECT
                t1.sr_emp_id,
                sum(t1.revised_target_value) AS target_amount
              FROM tblt_sr_product_wise_target AS t1
              WHERE target_month = month('$date') AND target_year = year('$date')
              GROUP BY t1.sr_emp_id
            ) AS t4 ON t1.emp_code = t4.sr_emp_id
  LEFT JOIN (
              SELECT
                t1.emp_code,
                t1.budget + if(sum(t3.budget), sum(t3.budget), 0) AS budget,
                t1.avail
              FROM tblt_credit_budget AS t1
                LEFT JOIN tblt_supervisor_budget_log AS t3
                  ON t1.emp_code = t3.emp_code AND t1.month = t3.month AND t1.year = t3.year
              WHERE t1.month = MONTH('$date') AND t1.year = YEAR('$date')
              GROUP BY t1.emp_code
            ) AS t5 ON t1.emp_code = t5.emp_code
  LEFT JOIN (SELECT
               t1.emp_code,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.SR_ID,
                            t1.Order_ID                                         AS order_count,
                            sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                                           t1.default_discount) AS order_amount

                          FROM order_table AS t1
                          WHERE t1.order_status = 17 AND t1.Date = '$date'
                          GROUP BY t1.SR_ID, t1.Order_ID) AS t2 ON t1.user_name = t2.SR_ID
             GROUP BY t1.emp_code) AS t6 ON t1.emp_code = t6.emp_code
  LEFT JOIN (SELECT
               t1.emp_code,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.SR_ID,
                            t1.Order_ID                                         AS order_count,
                            sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                                           t1.default_discount) AS order_amount

                          FROM order_table AS t1
                          WHERE t1.order_status = 14 AND t1.Date = '$date'
                          GROUP BY t1.SR_ID, t1.Order_ID) AS t2 ON t1.user_name = t2.SR_ID
             GROUP BY t1.emp_code) AS t7 ON t1.emp_code = t7.emp_code
  LEFT JOIN (SELECT
               t1.emp_code,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.SR_ID,
                            t1.Order_ID                                         AS order_count,
                            sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                                           t1.default_discount) AS order_amount

                          FROM order_table AS t1
                          WHERE t1.order_status = 9 AND t1.Date = '$date'
                          GROUP BY t1.SR_ID, t1.Order_ID) AS t2 ON t1.user_name = t2.SR_ID
             GROUP BY t1.emp_code) AS t8 ON t1.emp_code = t8.emp_code
  LEFT JOIN (SELECT
               count(DISTINCT t1.Order_ID)                         AS number_of_order,
               count(DISTINCT t1.OutLet_ID)                        AS number_of_memo,
               t1.SR_ID,
               t1.Date                                             AS Date,
               sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                              t1.default_discount) AS total_amount,
               sum((t1.Total_Item_Price + ((t1.Total_Item_Price * t1.gst) / 100) - t1.Discount -
                    t1.default_discount - t1.promo_discount) * t1.vat /
                   100)                                            AS vat_amount,
               sum((t1.Total_Item_Price) * t1.gst /
                   100)                                            AS excise_duty
             FROM order_table AS t1
             WHERE
               t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND order_status NOT IN (15, 16, 18, 21, 22)
               AND t1.Route_ID != ''
             GROUP BY t1.Date, t1.SR_ID) AS t9 ON t1.user_name = t9.SR_ID
  LEFT JOIN (SELECT
               t1.SR_ID,
               sum(t1.Total_Item_Price) - sum(t1.Discount + t1.promo_discount +
                                              t1.default_discount) AS total_amount,
               sum((t1.Total_Item_Price + ((t1.Total_Item_Price * t1.gst) / 100) - t1.Discount -
                    t1.default_discount - t1.promo_discount) * t1.vat /
                   100)                                            AS vat_amount,
               sum((t1.Total_Item_Price) * t1.gst /
                   100)                                            AS excise_duty,
               sum(t1.net_amount)                                  AS delivery_amount
             FROM order_table AS t1
             WHERE
               t1.Date BETWEEN DATE_FORMAT('$date', '%Y-%m-01') AND '$date' AND t1.Order_ID NOT IN ('', '0')
               AND t1.Route_ID != ''
             GROUP BY t1.SR_ID) AS t10 ON t1.user_name = t10.SR_ID
  LEFT JOIN (SELECT
               t1.emp_code,
               sum(t2.item_count) AS item_count
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.SR_ID,
                            count(DISTINCT t1.Product_id) AS item_count
                          FROM order_table AS t1
                          WHERE t1.Date = '$date' AND t1.Order_ID NOT IN ('', '0') AND
                                order_status NOT IN (15, 16, 18, 21, 22)
                                AND t1.Route_ID != ''
                          GROUP BY t1.SR_ID, t1.OutLet_ID) AS t2 ON t1.user_name = t2.SR_ID
             GROUP BY t1.emp_code) AS t11 ON t1.emp_code = t11.emp_code
  INNER JOIN tbld_employee AS t12 ON t1.manager_code = t12.emp_code
WHERE t1.master_role_id =1 AND (t1.status_id = 33 OR t10.delivery_amount > 0)
GROUP BY t1.emp_code;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblt_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'uaesr2',
            'type_name' => 'uaedash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);

    }

    /*public function prgDashboardSRData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'prgsr1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblt_dashboard_data')
            ->where(['service_no' => 2, 'date' => $date])->where('master_role_id', '=', '1')
            ->delete();
        $result = DB::connection('myprg_p')->select("SELECT
  '$date'                               AS date,
  t1.aemp_name                          AS name,
  t1.aemp_usnm                          AS user_id,
  t1.aemp_stnm                          AS short_name,
  0                                     AS group_id,
  t2.aemp_usnm                          AS manager_id,
  t1.role_id                            AS master_role_id,
  1                                     AS user_count,
  !isnull(t3.attn_date)                 AS is_present,
  !isnull(t3.attn_date)                 AS is_active,
  if(t6.memo_count>9,1,0)              AS is_productive,
  t4.site_count                         AS total_site,
  t5.visited_count                      AS total_visited,
  t6.memo_count                         AS total_memo,
  t5.visited_count - t6.memo_count      AS total_order_exception,
  t7.line_count                         AS number_item_line,
  t7.total_amount                       AS total_order,
  0                                     AS total_target,
  0                                     AS msp_target_ctn,
  0                                     AS msp_target_order,
  0                                     AS credit_block_count,
  0                                     AS credit_block_amount,
  0                                     AS over_due_count,
  0                                     AS over_due_amount,
  0                                     AS special_count,
  0                                     AS special_amount,
  0                                     AS total_budget,
  0                                     AS total_avail,
  t7.total_amount                       AS mtd_total_sales,
  0                                     AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') AS local_date_time,
  t1.cont_id                            AS country_id,
  '2'                                   as service_no
FROM tm_aemp AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_mngr = t2.id
  LEFT JOIN (SELECT
               t1.aemp_id,
               t1.attn_date
             FROM tt_attn AS t1
             WHERE t1.attn_date = '$date'
             GROUP BY t1.aemp_id, t1.attn_date) AS t3 ON t1.id = t3.aemp_id
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t2.site_id) AS site_count
             FROM tl_rpln AS t1 LEFT JOIN tl_rsmp AS t2 ON t1.rout_id = t2.rout_id
             WHERE t1.rpln_day = dayname('$date')
             GROUP BY t1.aemp_id) AS t4 ON t4.aemp_id = t1.id
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.site_id) AS visited_count
             FROM th_ssvh AS t1
             WHERE t1.ssvh_date = '$date'
             GROUP BY t1.aemp_id) AS t5 ON t1.id = t5.aemp_id
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.site_id) AS memo_count
             FROM th_ssvh AS t1
             WHERE t1.ssvh_date = '$date' AND t1.ssvh_ispd = 1
             GROUP BY t1.aemp_id) AS t6 ON t1.id = t6.aemp_id
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t2.amim_id) AS line_count,
               sum(t2.ordd_oamt) AS total_amount
             FROM tt_ordm AS t1 INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
             WHERE t1.ordm_date = '$date'
             GROUP BY t1.aemp_id) AS t7 ON t1.id = t7.aemp_id
             where t1.role_id=1 and t1.lfcl_id=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::table('tblt_dashboard_data')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'name' => 'prgsr2',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);
    }*/

    public function uniqueDashboardDataSR($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'prgsru',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        $countryAll = Country::all();
        foreach ($countryAll as $country) {
            DB::table('tblh_dashboard_data')
                ->where(['country_id' => $country->id, 'date' => $date])
                ->where('master_role_id', '=', '1')
                ->delete();
            $result = DB::connection('myprg_comm1')->select("SELECT
  '$date'                               AS date,
  t1.name                               AS name,
  t1.user_id                            AS user_id,
  t1.short_name                         AS short_name,
  t1.group_id                           AS group_id,
  t1.manager_id                         AS manager_id,
  t1.master_role_id                     AS master_role_id,
  1                                     AS user_count,
  if(sum(t1.is_present), 1, 0)          AS is_present,
  if(sum(t1.is_active), 1, 0)           AS is_active,
  if(sum(t1.is_productive), 1, 0)       AS is_productive,
  sum(t1.total_site)                    AS total_site,
  sum(t1.total_visited)                 AS total_visited,
  sum(t1.total_memo)                    AS total_memo,
  sum(t1.total_order_exception)         AS total_order_exception,
  sum(t1.number_item_line)              AS number_item_line,
  sum(t1.total_order)                   AS total_order,
  t1.total_target                                  AS total_target,
  t1.msp_target_ctn                                     AS msp_target_ctn,
  t1.msp_target_order                                     AS msp_target_order,
  t1.credit_block_count                                     AS credit_block_count,
  t1.credit_block_amount                                     AS credit_block_amount,
  t1.over_due_count                      AS over_due_count,
  t1.over_due_amount                                     AS over_due_amount,
  t1.special_count                                     AS special_count,
  t1.special_amount                                     AS special_amount,  
  t1.budget_amount                              AS budget_amount,
 t1.avail_amount                              AS avail_amount,
  t1.total_budget                                     AS total_budget,
  t1.total_avail                                     AS total_avail,
  sum(t1.mtd_total_sales)               AS mtd_total_sales,
  t1.mtd_total_delivery            AS mtd_total_delivery,
  CONVERT_TZ(now(), '+00:00', '+06:00') AS local_date_time,
  t1.country_id                         AS country_id,
  t1.service_no                         AS service_no
FROM tblt_dashboard_data AS t1
WHERE t1.country_id = $country->id and t1.date='$date' and t1.master_role_id =1
GROUP BY t1.user_id");
            $result = array_map(function ($value) {
                return (array)$value;
            }, $result);
            foreach (array_chunk($result, 500) as $t) {
                DB::table('tblh_dashboard_data')->insertOrIgnore(
                    $t
                );
            }
        }
        $plog[] = array(
            'name' => 'prgsru',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);
    }

    public function dashboardDataUpdate($date)
    {

        DB::update("UPDATE tblh_dashboard_sr_mtd_sales AS t1 INNER JOIN tblh_dashboard_data AS t2
    ON t1.user_id = t2.user_id AND t2.date = curdate() AND t2.master_role_id = 1
SET t2.mtd_total_sales = t1.mtd_sales");

        $plog = array();
        $plog[] = array(
            'name' => 'prgsrup',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        $masterRoles = MasterRole::all();
        foreach ($masterRoles as $role) {
            DB::update("UPDATE tblh_dashboard_data AS t1 INNER JOIN (
                                              SELECT
                                                t1.user_id,
                                                sum(t2.user_count)            AS user_count,
                                                sum(t2.is_present)            AS is_present,
                                                sum(t2.is_active)             AS is_active,
                                                sum(t2.is_productive)         AS is_productive,
                                                sum(t2.total_site)            AS total_site,
                                                sum(t2.total_visited)         AS total_visited,
                                                sum(t2.total_memo)            AS total_memo,
                                                sum(t2.total_order_count)     AS total_order_count,
                                                sum(
                                                    t2.total_order_exception) AS total_order_exception,
                                                sum(
                                                    t2.number_item_line)      AS number_item_line,
                                                sum(t2.total_order)           AS total_order,
                                                sum(t2.total_target)          AS total_target,
                                                sum(
                                                    t2.credit_block_count)    AS credit_block_count,
                                                sum(
                                                    t2.credit_block_amount)   AS credit_block_amount,
                                                sum(
                                                    t2.over_due_count)        AS over_due_count,
                                                sum(
                                                    t2.over_due_amount)       AS over_due_amount,
                                                sum(t2.special_count)         AS special_count,
                                                sum(
                                                    t2.special_amount)        AS special_amount,
                                                sum(t2.total_budget)          AS total_budget,
                                                sum(t2.total_avail)           AS total_avail,
                                                sum(t2.mtd_total_sales)       AS mtd_total_sales,
                                                sum(t2.mtd_total_delivery)    AS mtd_total_delivery
                                              FROM tblh_dashboard_data AS t1
                                                INNER JOIN tblh_dashboard_data AS t2
                                                  ON t1.user_id = t2.manager_id AND t2.date = '$date'
                                              WHERE t1.master_role_id = $role->id AND $role->id > t2.master_role_id AND
                                                    t2.master_role_id != 0 AND t1.date = '$date'
                                              GROUP BY t1.user_id
                                            ) AS t2 ON t1.user_id = t2.user_id
SET t1.user_count          = t2.user_count,
  t1.is_present            = t2.is_present,
  t1.is_active             = t2.is_active,
  t1.is_productive         = t2.is_productive,
  t1.total_site            = t2.total_site,
  t1.total_visited         = t2.total_visited,
  t1.total_memo            = t2.total_memo,
  t1.total_order_count     = t2.total_order_count,
  t1.total_order_exception = t2.total_order_exception,
  t1.number_item_line      = t2.number_item_line,
  t1.total_order           = t2.total_order,
  t1.total_target          = t2.total_target,
  t1.credit_block_count    = t2.credit_block_count,
  t1.credit_block_amount   = t2.credit_block_amount,
  t1.over_due_count        = t2.over_due_count,
  t1.over_due_amount       = t2.over_due_amount,
  t1.special_count         = t2.special_count,
  t1.special_amount        = t2.special_amount,
  t1.total_budget          = t1.budget_amount+t2.total_budget,
  t1.total_avail           = t1.avail_amount+t2.total_avail,
  t1.mtd_total_sales       = t2.mtd_total_sales,
  t1.mtd_total_delivery    = t2.mtd_total_delivery,
  t1.local_date_time       = CONVERT_TZ(now(), '+00:00', '+06:00')
WHERE t1.master_role_id = $role->id AND t1.date = '$date';");
        }
        $plog[] = array(
            'name' => 'prgsrup',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('th_plog')->insert($plog);
    }

    public function dashboardRoleManagerUpdate()
    {
        DB::connection('pran_live')->update("UPDATE srinfo AS t1
  INNER JOIN designation_table AS t2 ON t1.designation = t2.designation_id
SET t1.desig2 = t2.ids,t1.user_token=''");

        DB::connection('pran_live')->update("UPDATE srinfo AS t1
  INNER JOIN zone_master AS t2 ON t1.Zone_Id = t2.Zone_ID AND t2.Company_id = 'PS001'
  INNER JOIN tblt_user_area_permission AS t3 ON t2.ID = t3.zone_id
  INNER JOIN tblt_user_group_permission AS t4 ON t3.user_id = t4.user_id
  INNER JOIN srinfo AS t5 ON t4.user_id = t5.SR_ID AND t5.desig2 = 2 AND t5.Status = 'Y'
SET t1.manager_code = IF(LENGTH(t4.user_id)<5,CONCAT('0',t4.user_id), t4.user_id),t1.user_token='auto'
WHERE t1.desig2 = 1 AND t1.Status = 'Y' AND t1.Group_Id = t4.group_id");


    }

    public function rgManagerUpdate()
    {
        DB::connection('rfl_live')->update("UPDATE srinfo AS t1
  INNER JOIN designation_master AS t2 ON t1.designation = t2.id
SET t1.role = t2.role, t1.user_token = ''");

        DB::connection('rfl_live')->update("UPDATE `tblm_group_zone_sv_mapping` AS t1 INNER JOIN srinfo AS t2 ON t1.manager_code = t2.SR_ID
  INNER JOIN srinfo AS t3 ON t1.zone_code = t3.Zone_Id AND t1.group_code = t3.Group_Id AND t3.role = 1 and t3.Status=\"Y\"
SET t3.manager_code = t2.SR_ID,t3.user_token = 'Auto'");
    }

    public function pgDashboardSRMTDOrderData($date)
    {
        DB::table('tblh_dashboard_sr_mtd_sales')
            ->where(['country_id' => 1])
            ->delete();
        $result = DB::connection('pran_live')->select("
        SELECT
                t1.SR_ID                 as user_id,
                sum(t1.Total_Item_Price) AS mtd_sales,
                 '1'                     as country_id
              FROM order_table AS t1
              WHERE
                t1.Date BETWEEN DATE_FORMAT('$date', '%Y-%m-01') AND date(subdate('$date', 1)) AND
                t1.Order_ID NOT IN ('', '0')
                AND t1.Route_ID != ''
              GROUP BY t1.SR_ID
        ");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblh_dashboard_sr_mtd_sales')->insertOrIgnore(
                $t
            );
        }

    }

    public function outletDataImport()
    {
        $maxId = DB::connection('myprg_fpran')->table('tm_site')->max('attr3');
        $result = DB::connection('pran_live')->select("SELECT
  t1.Outlet_Name                                AS site_name,
  t1.Outlet_ID                                  AS site_code,
  1                                             AS outl_id,
  t1.print_outlet_name                          AS site_olnm,
  t1.Area                                       AS site_adrs,
  t1.print_outlet_address                       AS site_olad,
  if(t1.Market_ID = 0, 1, t1.Market_ID)         AS mktm_id,
  t1.Owner_Name                                 AS site_ownm,
  ''                                            AS site_olon,
  t1.Mobile                                     AS site_mob1,
  t1.Mobile_No_2                                AS site_mob2,
  ''                                            AS site_emal,
  1                                             AS scnl_id,
  if(t1.Category_Code = 0, 6, t1.Category_Code) AS otcg_id,
  ''                                            AS site_imge,
  ''                                            AS site_omge,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', 1)  AS geo_lat,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', -1) AS geo_lon,
  ''                                            AS site_reg,
  0                                             AS site_vrfy,
  ''                                            AS site_hsno,
  ''                                            AS site_vtrn,
  0                                             AS site_vsts,
  if(t1.refrigerator = 'yes', 1, 0)             AS site_isfg,
  if(t1.shopsigning = 'yes', 1, 0)              AS site_issg,
  2                                             AS cont_id,
  1                                             AS lfcl_id,
  1                                             AS aemp_iusr,
  1                                             AS aemp_eusr,
  t1.id                                         as attr3
FROM
  outletmaster_Pran_Data AS t1
  INNER JOIN tbld_random AS t2 ON t1.Outlet_ID = t2.number
  where t1.id>$maxId
GROUP BY t1.Outlet_ID;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {

            DB::connection('myprg_pran')->table('tm_site')->insertOrIgnore(
                $t
            );
        }

    }


    public function outletDataImportRFL()
    {
        $maxId = DB::connection('myprg_rfl')->table('tm_site')->max('attr3');
        $result = DB::connection('rfl_live')->select("SELECT
  t1.Outlet_Name                                 AS site_name,
  concat('885', lpad(t1.Outlet_ID_2, 7, '0'))    AS site_code,
  1                                              AS outl_id,
  t1.print_outlet_name                           AS site_olnm,
  t1.Area                                        AS site_adrs,
  t1.print_outlet_address                        AS site_olad,
  if(t1.Market_ID = 0, 1, t1.Market_ID)          AS mktm_id,
  t1.Owner_Name                                  AS site_ownm,
  ''                                             AS site_olon,
  t1.Mobile                                      AS site_mob1,
  t1.Mobile_No_2                                 AS site_mob2,
  ''                                             AS site_emal,
  1                                              AS scnl_id,
  if(t1.Category_Code = 0, 11, t1.Category_Code) AS otcg_id,
  ''                                             AS site_imge,
  ''                                             AS site_omge,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', 1)   AS geo_lat,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', -1)  AS geo_lon,
  ''                                             AS site_reg,
  0                                              AS site_vrfy,
  ''                                             AS site_hsno,
  ''                                             AS site_vtrn,
  0                                              AS site_vsts,
  if(t1.refrigerator = 'yes', 1, 0)              AS site_isfg,
  if(t1.shopsigning = 'yes', 1, 0)               AS site_issg,
  5                                              AS cont_id,
  1                                              AS lfcl_id,
  1                                              AS aemp_iusr,
  1                                              AS aemp_eusr,
  t1.id                                          AS attr3
FROM
  outletmaster_Pran_Data AS t1
  where t1.id>$maxId
GROUP BY t1.Outlet_ID_2;
");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::connection('myprg_rfl')->table('tm_site')->insertOrIgnore(
                $t
            );
        }

    }

}