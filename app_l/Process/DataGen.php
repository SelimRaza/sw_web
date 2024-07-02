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
class DataGen
{

    public function prgDashboardSRData($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '0000');
        DB::connection($db_conn)->table('th_dhbd')
            ->where(['role_id' => 1, 'dhbd_date' => $date])->delete();
        $result = DB::connection($db_conn)->select("SELECT
  '$date'                        AS dhbd_date,
  t1.id                            AS aemp_id,
  t1.aemp_mngr                     AS aemp_mngr,
  t1.role_id                       AS role_id,
  1                                AS dhbd_ucnt,
  !isnull(t3.attn_date)            AS dhbd_prnt,
  !isnull(t6.memo_count)           AS dhbd_pact,
  if(t6.memo_count > 9, 1, 0)     AS dhbd_prdt,
  t4.site_count                    AS dhbd_tsit,
  t5.visited_count                 AS dhbd_tvit,
  t6.memo_count                    AS dhbd_memo,
  t5.visited_count - t6.memo_count AS dhbd_oexc,
  t12.line_count                   AS dhbd_line,
  t12.today_amount                 AS dhbd_tamt,
  t8.target_amount                 AS dhbd_ttar,
  t9.order_count                   AS dhbd_cblc,
  t9.order_amount                  AS dhbd_cbla,
  t10.order_count                  AS dhbd_oblc,
  t10.order_amount                 AS dhbd_obla,
  t11.order_count                  AS dhbd_sblc,
  t11.order_amount                 AS dhbd_sbla,
  0                                AS dhbd_tmbg,
  0                                AS dhbd_tmav,
  0                                AS dhbd_spbg,
  0                                AS dhbd_spav,
  0                                AS dhbd_mtdo,
  0                               AS dhbd_mtdd,
  '$datetime'                        AS dhbd_time,
  t1.cont_id                       AS cont_id,
  1                                AS lfcl_id,
  1                                AS aemp_iusr,
  1                                AS aemp_eusr
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
               t1.aemp_susr,
               sum(t1.trgt_ramt) AS target_amount
             FROM tt_trgt AS t1
             WHERE t1.trgt_mnth = month('$date') AND t1.trgt_year = year('$date')
             GROUP BY t1.aemp_susr) AS t8 ON t1.id = t8.aemp_susr
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 9 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t9 ON t1.id = t9.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 14 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t10 ON t1.id = t10.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 17 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t11 ON t1.id = t11.id
  LEFT JOIN (SELECT
  sdd.aemp_id,
  sum(line_count)   AS line_count,
  SUM(today_amount) AS today_amount
FROM (
       SELECT
         t1.aemp_id,
         t1.site_id,
         count(DISTINCT t2.amim_id) AS line_count,
         sum(t2.ordd_oamt)          AS today_amount
       FROM tt_ordm AS t1
         INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
       WHERE t1.ordm_date = '$date'
       GROUP BY t1.aemp_id, t1.site_id) AS sdd
GROUP BY sdd.aemp_id) AS t12 ON t1.id = t12.aemp_id
WHERE t1.role_id = 1 AND t1.lfcl_id = 1 and t1.aemp_issl=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection($db_conn)->table('th_dhbd')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '1111');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }

    public function prgDashboardSRData5($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '8888');
        DB::connection($db_conn)->table('th_dhbd_5')
            ->where(['role_id' => 1, 'dhbd_date' => $date])->delete();
        $result = DB::connection($db_conn)->select("SELECT
  '$date'                        AS dhbd_date,
  t1.id                            AS aemp_id,
  t1.aemp_mngr                     AS aemp_mngr,
  t1.role_id                       AS role_id,
  1                                AS dhbd_ucnt,
  !isnull(t3.attn_date)            AS dhbd_prnt,
  !isnull(t6.memo_count)           AS dhbd_pact,
  if(t6.memo_count > 9, 1, 0)     AS dhbd_prdt,
  t4.site_count                    AS dhbd_tsit,
  t5.visited_count                 AS dhbd_tvit,
  t6.memo_count                    AS dhbd_memo,
  t5.visited_count - t6.memo_count AS dhbd_oexc,
  t12.line_count                   AS dhbd_line,
  t12.today_amount                 AS dhbd_tamt,
  t8.target_amount                 AS dhbd_ttar,
  t9.order_count                   AS dhbd_cblc,
  t9.order_amount                  AS dhbd_cbla,
  t10.order_count                  AS dhbd_oblc,
  t10.order_amount                 AS dhbd_obla,
  t11.order_count                  AS dhbd_sblc,
  t11.order_amount                 AS dhbd_sbla,
  0                                AS dhbd_tmbg,
  0                                AS dhbd_tmav,
  0                                AS dhbd_spbg,
  0                                AS dhbd_spav,
  0                                AS dhbd_mtdo,
  0                               AS dhbd_mtdd,
  '$datetime'                        AS dhbd_time,
  t1.cont_id                       AS cont_id,
  1                                AS lfcl_id,
  1                                AS aemp_iusr,
  1                                AS aemp_eusr,
  t13.dhbd_lvsr                    AS dhbd_lvsr
FROM tm_aemp AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_mngr = t2.id
  LEFT JOIN (SELECT
               t1.aemp_id,
               t1.attn_date
             FROM tt_attn AS t1
             WHERE t1.attn_date = '$date'AND t1.atten_atyp!=3
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
               t1.aemp_susr,
               sum(t1.trgt_ramt) AS target_amount
             FROM tt_trgt AS t1
             WHERE t1.trgt_mnth = month('$date') AND t1.trgt_year = year('$date')
             GROUP BY t1.aemp_susr) AS t8 ON t1.id = t8.aemp_susr
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 9 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t9 ON t1.id = t9.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 14 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t10 ON t1.id = t10.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 17 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t11 ON t1.id = t11.id
  LEFT JOIN (SELECT
  sdd.aemp_id,
  sum(line_count)   AS line_count,
  SUM(today_amount) AS today_amount
FROM (
       SELECT
         t1.aemp_id,
         t1.site_id,
         count(DISTINCT t2.amim_id) AS line_count,
         sum(t2.ordd_oamt)          AS today_amount
       FROM tt_ordm AS t1
         INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
       WHERE t1.ordm_date = '$date'
       GROUP BY t1.aemp_id, t1.site_id) AS sdd
GROUP BY sdd.aemp_id) AS t12 ON t1.id = t12.aemp_id
LEFT JOIN (SELECT
               t1.aemp_id,
               t1.attn_date,
                count(t1.atten_atyp) AS dhbd_lvsr
                FROM tt_attn AS t1
                                WHERE t1.attn_date = '$date' AND t1.atten_atyp=3
                                                     GROUP BY t1.aemp_id, t1.attn_date,t1.atten_atyp) AS t13 ON t1.id = t13.aemp_id
WHERE t1.role_id = 1 AND t1.lfcl_id = 1 and t1.aemp_issl=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection($db_conn)->table('th_dhbd_5')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '9999');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }


 public function prgDashboardSRData5_rfl($db_conn, $datetime)
    {
        /*saka;     */
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '8888');
        DB::connection($db_conn)->table('th_dhbd_5')
            ->where(['role_id' => 1, 'dhbd_date' => $date])->delete();
        $result = DB::connection($db_conn)->select("SELECT
  '$date'                        AS dhbd_date,
  t1.id                            AS aemp_id,
  t1.aemp_mngr                     AS aemp_mngr,
  t1.role_id                       AS role_id,
  1                                AS dhbd_ucnt,
  !isnull(t3.attn_date)            AS dhbd_prnt,
  !isnull(t6.memo_count)           AS dhbd_pact,
  if(t6.memo_count > 14, 1, 0)     AS dhbd_prdt,
  t4.site_count                    AS dhbd_tsit,
  t5.visited_count                 AS dhbd_tvit,
  t6.memo_count                    AS dhbd_memo,
  t5.visited_count - t6.memo_count AS dhbd_oexc,
  t12.line_count                   AS dhbd_line,
  t12.today_amount                 AS dhbd_tamt,
  t8.target_amount                 AS dhbd_ttar,
  t9.order_count                   AS dhbd_cblc,
  t9.order_amount                  AS dhbd_cbla,
  t10.order_count                  AS dhbd_oblc,
  t10.order_amount                 AS dhbd_obla,
  t11.order_count                  AS dhbd_sblc,
  t11.order_amount                 AS dhbd_sbla,
  0                                AS dhbd_tmbg,
  0                                AS dhbd_tmav,
  0                                AS dhbd_spbg,
  0                                AS dhbd_spav,
  0                                AS dhbd_mtdo,
  0                               AS dhbd_mtdd,
  '$datetime'                        AS dhbd_time,
  t1.cont_id                       AS cont_id,
  1                                AS lfcl_id,
  1                                AS aemp_iusr,
  1                                AS aemp_eusr,
  t13.dhbd_lvsr                    AS dhbd_lvsr
FROM tm_aemp AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_mngr = t2.id
  LEFT JOIN (SELECT
               t1.aemp_id,
               t1.attn_date
             FROM tt_attn AS t1
             WHERE t1.attn_date = '$date'AND t1.atten_atyp!=3
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
               t1.aemp_susr,
               sum(t1.trgt_ramt) AS target_amount
             FROM tt_trgt AS t1
             WHERE t1.trgt_mnth = month('$date') AND t1.trgt_year = year('$date')
             GROUP BY t1.aemp_susr) AS t8 ON t1.id = t8.aemp_susr
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 9 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t9 ON t1.id = t9.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 14 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t10 ON t1.id = t10.id
  LEFT JOIN (SELECT
               t1.id,
               count(t2.order_count) AS order_count,
               sum(t2.order_amount)  AS order_amount
             FROM tm_aemp AS t1
               LEFT JOIN (SELECT
                            t1.aemp_id,
                            t1.id             AS order_count,
                            sum(t1.ordm_amnt) AS order_amount
                          FROM tt_ordm AS t1
                          WHERE t1.lfcl_id = 17 AND t1.ordm_date = '$date'
                          GROUP BY t1.aemp_id, t1.id) AS t2 ON t1.id = t2.aemp_id
             WHERE t1.role_id = 1
             GROUP BY t1.id) AS t11 ON t1.id = t11.id
  LEFT JOIN (SELECT
  sdd.aemp_id,
  sum(line_count)   AS line_count,
  SUM(today_amount) AS today_amount
FROM (
       SELECT
         t1.aemp_id,
         t1.site_id,
         count(DISTINCT t2.amim_id) AS line_count,
         sum(t2.ordd_oamt)          AS today_amount
       FROM tt_ordm AS t1
         INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
       WHERE t1.ordm_date = '$date'
       GROUP BY t1.aemp_id, t1.site_id) AS sdd
GROUP BY sdd.aemp_id) AS t12 ON t1.id = t12.aemp_id
LEFT JOIN (SELECT
               t1.aemp_id,
               t1.attn_date,
                count(t1.atten_atyp) AS dhbd_lvsr
                FROM tt_attn AS t1
                                WHERE t1.attn_date = '$date' AND t1.atten_atyp=3
                                                     GROUP BY t1.aemp_id, t1.attn_date,t1.atten_atyp) AS t13 ON t1.id = t13.aemp_id
WHERE t1.role_id = 1 AND t1.lfcl_id = 1 and t1.aemp_issl=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection($db_conn)->table('th_dhbd_5')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '9999');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }

    public function prgDashboardSVData($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '2222');
        DB::connection($db_conn)->table('th_dhbd')
            ->where(['dhbd_date' => $date])->where('role_id', '>', 1)->delete();
        $result = DB::connection($db_conn)->select("SELECT
  '$date'      AS dhbd_date,
  t1.id        AS aemp_id,
  t1.aemp_mngr AS aemp_mngr,
  t1.role_id   AS role_id,
  0            AS dhbd_ucnt,
  0            AS dhbd_prnt,
  0            AS dhbd_pact,
  0            AS dhbd_prdt,
  0            AS dhbd_tsit,
  0            AS dhbd_tvit,
  0            AS dhbd_memo,
  0            AS dhbd_oexc,
  0            AS dhbd_line,
  0            AS dhbd_tamt,
  0            AS dhbd_ttar,
  0            AS dhbd_cblc,
  0            AS dhbd_cbla,
  0            AS dhbd_oblc,
  0            AS dhbd_obla,
  0            AS dhbd_sblc,
  0            AS dhbd_sbla,
  t2.budget    AS dhbd_tmbg,
  t2.avail     AS dhbd_tmav,
  0            AS dhbd_spbg,
  0            AS dhbd_spav,
  0            AS dhbd_mtdo,
  0            AS dhbd_mtdd,
  '$datetime'  AS dhbd_time,
  t1.cont_id   AS cont_id,
  1            AS lfcl_id,
  1            AS aemp_iusr,
  1            AS aemp_eusr
FROM tm_aemp AS t1
  LEFT JOIN (SELECT
               t1.aemp_id,
               t1.spbm_limt AS budget,
               t1.spbm_avil AS avail
             FROM tt_spbm AS t1
             WHERE t1.spbm_mnth = MONTH('$date') AND t1.spbm_year = YEAR('$date')
             GROUP BY t1.id) AS t2 ON t1.id = t2.aemp_id
WHERE t1.role_id > 1 AND t1.lfcl_id = 1 and t1.aemp_issl=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection($db_conn)->table('th_dhbd')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '3333');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }

    public function prgDashboardSVData5($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '5511');
        DB::connection($db_conn)->table('th_dhbd_5')
            ->where(['dhbd_date' => $date])->where('role_id', '>', 1)->delete();
        $result = DB::connection($db_conn)->select("SELECT
  '$date'      AS dhbd_date,
  t1.id        AS aemp_id,
  t1.aemp_mngr AS aemp_mngr,
  t1.role_id   AS role_id,
  0            AS dhbd_ucnt,
  0            AS dhbd_prnt,
  0            AS dhbd_pact,
  0            AS dhbd_prdt,
  0            AS dhbd_tsit,
  0            AS dhbd_tvit,
  0            AS dhbd_memo,
  0            AS dhbd_oexc,
  0            AS dhbd_line,
  0            AS dhbd_tamt,
  0            AS dhbd_ttar,
  0            AS dhbd_cblc,
  0            AS dhbd_cbla,
  0            AS dhbd_oblc,
  0            AS dhbd_obla,
  0            AS dhbd_sblc,
  0            AS dhbd_sbla,
  t2.budget    AS dhbd_tmbg,
  t2.avail     AS dhbd_tmav,
  0            AS dhbd_spbg,
  0            AS dhbd_spav,
  0            AS dhbd_mtdo,
  0            AS dhbd_mtdd,
  '$datetime'  AS dhbd_time,
  t1.cont_id   AS cont_id,
  1            AS lfcl_id,
  1            AS aemp_iusr,
  1            AS aemp_eusr,
  0            AS dhbd_lvsr
FROM tm_aemp AS t1
  LEFT JOIN (SELECT
               t1.aemp_id,
               t1.spbm_limt AS budget,
               t1.spbm_avil AS avail
             FROM tt_spbm AS t1
             WHERE t1.spbm_mnth = MONTH('$date') AND t1.spbm_year = YEAR('$date')
             GROUP BY t1.id) AS t2 ON t1.id = t2.aemp_id
WHERE t1.role_id > 1 AND t1.lfcl_id = 1 and t1.aemp_issl=1");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection($db_conn)->table('th_dhbd_5')->insertOrIgnore(
                $t
            );
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '1155');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }

    public function prgDashboardUpdate($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '4444');
        $masterRoles = MasterRole::on($db_conn)->where('id', '>', 1)->get();
        foreach ($masterRoles as $role) {
            DB::connection($db_conn)->update("UPDATE th_dhbd AS t1 INNER JOIN (
                                  SELECT
                                    t1.aemp_id,
                                    sum(t2.dhbd_ucnt) AS dhbd_ucnt,
                                    sum(t2.dhbd_prnt) AS dhbd_prnt,
                                    sum(t2.dhbd_pact) AS dhbd_pact,
                                    sum(t2.dhbd_prdt) AS dhbd_prdt,
                                    sum(t2.dhbd_tsit) AS dhbd_tsit,
                                    sum(t2.dhbd_tvit) AS dhbd_tvit,
                                    sum(t2.dhbd_memo) AS dhbd_memo,
                                    sum(t2.dhbd_oexc) AS dhbd_oexc,
                                    sum(t2.dhbd_line) AS dhbd_line,
                                    sum(t2.dhbd_tamt) AS dhbd_tamt,
                                    sum(t2.dhbd_ttar) AS dhbd_ttar,
                                    sum(t2.dhbd_cblc) AS dhbd_cblc,
                                    sum(t2.dhbd_cbla) AS dhbd_cbla,
                                    sum(t2.dhbd_oblc) AS dhbd_oblc,
                                    sum(t2.dhbd_obla) AS dhbd_obla,
                                    sum(t2.dhbd_sblc) AS dhbd_sblc,
                                    sum(t2.dhbd_sbla) AS dhbd_sbla,
                                    sum(t2.dhbd_tmbg) AS dhbd_tmbg,
                                    sum(t2.dhbd_tmav) AS dhbd_tmav,
                                    sum(t2.dhbd_spbg) AS dhbd_spbg,
                                    sum(t2.dhbd_spav) AS dhbd_spav,
                                    sum(t2.dhbd_mtdo) AS dhbd_mtdo,
                                    sum(t2.dhbd_mtdd) AS dhbd_mtdd
                                  FROM th_dhbd AS t1
                                    INNER JOIN th_dhbd AS t2
                                      ON t1.aemp_id = t2.aemp_mngr AND t2.dhbd_date = '$date'
                                  WHERE t1.role_id = $role->id AND $role->id > t2.role_id AND t1.dhbd_date = '$date'
                                  GROUP BY t1.aemp_id
                                ) AS t2 ON t1.aemp_id = t2.aemp_id
SET t1.dhbd_ucnt = t2.dhbd_ucnt,
  t1.dhbd_prnt   = t2.dhbd_prnt,
  t1.dhbd_pact   = t2.dhbd_pact,
  t1.dhbd_prdt   = t2.dhbd_prdt,
  t1.dhbd_tsit   = t2.dhbd_tsit,
  t1.dhbd_tvit   = t2.dhbd_tvit,
  t1.dhbd_memo   = t2.dhbd_memo,
  t1.dhbd_oexc   = t2.dhbd_oexc,
  t1.dhbd_line   = t2.dhbd_line,
  t1.dhbd_tamt   = t2.dhbd_tamt,
  t1.dhbd_ttar   = t2.dhbd_ttar,
  t1.dhbd_cblc   = t2.dhbd_cblc,
  t1.dhbd_cbla   = t2.dhbd_cbla,
  t1.dhbd_oblc   = t2.dhbd_oblc,
  t1.dhbd_obla   = t2.dhbd_obla,
  t1.dhbd_sblc   = t2.dhbd_sblc,
  t1.dhbd_sbla   = t2.dhbd_sbla,
  t1.dhbd_spbg   = t2.dhbd_spbg+ t2.dhbd_tmbg,
  t1.dhbd_spav   = t2.dhbd_spav+t2.dhbd_tmav,
  t1.dhbd_mtdo   = t2.dhbd_mtdo,
  t1.dhbd_mtdd   = t2.dhbd_mtdd,
  t1.dhbd_time   = '$datetime'
WHERE t1.role_id = $role->id AND t1.dhbd_date = '$date';");
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '5555');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }

    public function prgdashboardUpdate5($db_conn, $datetime)
    {
        $date = date("Y-m-d", strtotime($datetime));
        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'Start',
            'dhlg_code' => '6666');
        $masterRoles = MasterRole::on($db_conn)->where('id', '>', 1)->get();
        foreach ($masterRoles as $role) {
            DB::connection($db_conn)->update("UPDATE th_dhbd_5 AS t1 INNER JOIN (
                                  SELECT
                                    t1.aemp_id,
                                    sum(t2.dhbd_ucnt) AS dhbd_ucnt,
                                    sum(t2.dhbd_prnt) AS dhbd_prnt,
                                    sum(t2.dhbd_pact) AS dhbd_pact,
                                    sum(t2.dhbd_prdt) AS dhbd_prdt,
                                    sum(t2.dhbd_tsit) AS dhbd_tsit,
                                    sum(t2.dhbd_tvit) AS dhbd_tvit,
                                    sum(t2.dhbd_memo) AS dhbd_memo,
                                    sum(t2.dhbd_oexc) AS dhbd_oexc,
                                    sum(t2.dhbd_line) AS dhbd_line,
                                    sum(t2.dhbd_tamt) AS dhbd_tamt,
                                    sum(t2.dhbd_ttar) AS dhbd_ttar,
                                    sum(t2.dhbd_cblc) AS dhbd_cblc,
                                    sum(t2.dhbd_cbla) AS dhbd_cbla,
                                    sum(t2.dhbd_oblc) AS dhbd_oblc,
                                    sum(t2.dhbd_obla) AS dhbd_obla,
                                    sum(t2.dhbd_sblc) AS dhbd_sblc,
                                    sum(t2.dhbd_sbla) AS dhbd_sbla,
                                    sum(t2.dhbd_tmbg) AS dhbd_tmbg,
                                    sum(t2.dhbd_tmav) AS dhbd_tmav,
                                    sum(t2.dhbd_spbg) AS dhbd_spbg,
                                    sum(t2.dhbd_spav) AS dhbd_spav,
                                    sum(t2.dhbd_mtdo) AS dhbd_mtdo,
                                    sum(t2.dhbd_mtdd) AS dhbd_mtdd,
                                    sum(t2.dhbd_lvsr) AS dhbd_lvsr
                                  FROM th_dhbd_5 AS t1
                                    INNER JOIN th_dhbd_5 AS t2
                                      ON t1.aemp_id = t2.aemp_mngr AND t2.dhbd_date = '$date'
                                  WHERE t1.role_id = $role->id AND $role->id > t2.role_id AND t1.dhbd_date = '$date'
                                  GROUP BY t1.aemp_id
                                ) AS t2 ON t1.aemp_id = t2.aemp_id
SET t1.dhbd_ucnt = t2.dhbd_ucnt,
  t1.dhbd_prnt   = t2.dhbd_prnt,
  t1.dhbd_pact   = t2.dhbd_pact,
  t1.dhbd_prdt   = t2.dhbd_prdt,
  t1.dhbd_tsit   = t2.dhbd_tsit,
  t1.dhbd_tvit   = t2.dhbd_tvit,
  t1.dhbd_memo   = t2.dhbd_memo,
  t1.dhbd_oexc   = t2.dhbd_oexc,
  t1.dhbd_line   = t2.dhbd_line,
  t1.dhbd_tamt   = t2.dhbd_tamt,
  t1.dhbd_ttar   = t2.dhbd_ttar,
  t1.dhbd_cblc   = t2.dhbd_cblc,
  t1.dhbd_cbla   = t2.dhbd_cbla,
  t1.dhbd_oblc   = t2.dhbd_oblc,
  t1.dhbd_obla   = t2.dhbd_obla,
  t1.dhbd_sblc   = t2.dhbd_sblc,
  t1.dhbd_sbla   = t2.dhbd_sbla,
  t1.dhbd_spbg   = t2.dhbd_spbg+ t2.dhbd_tmbg,
  t1.dhbd_spav   = t2.dhbd_spav+t2.dhbd_tmav,
  t1.dhbd_mtdo   = t2.dhbd_mtdo,
  t1.dhbd_mtdd   = t2.dhbd_mtdd,
  t1.dhbd_lvsr   = t2.dhbd_lvsr,
  t1.dhbd_time   = '$datetime'
WHERE t1.role_id = $role->id AND t1.dhbd_date = '$date';");
        }
        $plog[] = array(
            'dhlg_name' => 'End',
            'dhlg_code' => '7777');
        DB::connection($db_conn)->table('th_dhlg')->insert($plog);
    }
}