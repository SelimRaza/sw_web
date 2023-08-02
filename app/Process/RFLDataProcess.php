<?php

namespace App\Process;

use App\MasterData\MasterRole;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class RFLDataProcess
{

    public function rgDashboardSVData($date)
    {
        $plog = array();
        $plog[] = array(
            'name' => 'pgsv1',
            'type_name' => 'dash',
            'date_time' => date('Y-m-d H:i:s'));
        DB::table('tblh_dashboard_data')
            ->where(['country_id' => 5, 'date' => $date]) ->whereIn('master_role_id',[2,3,4,5,6,7,8])->where('service_no','=','5')
            ->delete();
        $result = DB::connection('rfl_live')->select("SELECT
  '$date'       AS date,
  t1.Name as name,
  IF(LENGTH(t1.SR_ID)<5,CONCAT('0',t1.SR_ID), t1.SR_ID) as user_id,
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

}