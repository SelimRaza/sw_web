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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function preData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.prms_edat, t1.prms_sdat, curdate()) AS column_id,
  t1.id                                                AS promo_id,
  t1.prms_name                                         AS promo_name,
  t1.prms_sdat                                         AS start_date,
  t1.prms_edat                                         AS end_date,
  t1.prmr_qfct                                         AS qualifier_category,
  t1.prmr_ditp                                         AS discount_type,
  t1.prmr_ctgp                                         AS category_group,
  t1.prmr_qfon                                         AS qualifier_on,
  t1.prmr_qfln                                         AS qualifier_line
FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
  INNER JOIN tl_rsmp AS t3 ON t2.site_id = t3.site_id
  INNER JOIN tl_rpln AS t4 ON t3.rout_id = t4.rout_id
WHERE t4.aemp_id = $request->emp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1 AND t1.cont_id = $request->country_id
GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,
  t1.prmr_qfln;");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, curdate()) AS column_id,
  t1.prmr_id                                                            AS promo_id,
  t1.amim_id                                                            AS sku_id,
  t1.prmd_modr                                                          AS pro_modifier_id,
  t1.prmd_mosl                                                          AS pro_modifier_id_sl,
  0                                                                     AS order_slab_status
FROM tm_prmd AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
  INNER JOIN tl_rsmp AS t4 ON t3.site_id = t4.site_id
  INNER JOIN tl_rpln AS t5 ON t4.rout_id = t5.rout_id
WHERE t5.aemp_id = $request->emp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1 AND t2.cont_id = $request->country_id
GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmr_id, t1.amim_id, t1.prmd_modr, t1.prmd_mosl");
            $tst2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty, curdate()) AS column_id,
  t1.prmr_id                                                                            AS promo_id,
  t1.prsb_text                                                                          AS promo_slab_text,
  t1.prsb_fqty                                                                          AS from_qty,
  t1.prsb_tqty                                                                          AS to_qty,
  0                                                                                     AS unit,
  0                                                                                     AS unit_factor,
  t1.prsb_famn                                                                          AS from_amnt,
  t1.prsb_tamn                                                                          AS to_amnt,
  t1.prsb_qnty                                                                          AS qty,
  0                                                                                     AS given_unit,
  0                                                                                     AS given_unit_factor,
  t1.prsb_disc                                                                          AS discount,
  t1.prsb_modr                                                                          AS pro_modifier_id,
  t1.prsb_mosl                                                                          AS pro_modifier_id_sl
FROM tm_prsb AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
  INNER JOIN tl_rsmp AS t4 ON t3.site_id = t4.site_id
  INNER JOIN tl_rpln AS t5 ON t4.rout_id = t5.rout_id
WHERE t5.aemp_id = $request->emp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1 AND t2.cont_id = $request->country_id
GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
  t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl");
            $tst3 = DB::connection($country->cont_conn)->select("SELECT *
FROM (
       SELECT
         concat(t1.id, t2.site_id, t1.prms_edat, t1.prms_sdat, curdate()) AS column_id,
         t1.id                                                            AS promotion_id,
         t2.site_id                                                       AS site_id
       FROM tm_prmr AS t1
         INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
         INNER JOIN tl_rsmp AS t3 ON t2.site_id = t3.site_id
         INNER JOIN tl_rpln AS t4 ON t3.rout_id = t4.rout_id
         INNER JOIN tm_site AS t5 ON t3.site_id = t5.id
       WHERE t4.aemp_id = $request->emp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1 AND
             t5.lfcl_id = 1 AND t4.rpln_day = dayname(curdate()) AND t1.cont_id = $request->country_id
       GROUP BY t1.id, t2.site_id, t1.prms_edat, t1.prms_sdat
       UNION ALL
       SELECT
         Concat(t2.prmr_id, t2.site_id, curdate()) AS column_id,
         t2.prmr_id                                AS promotion_id,
         t2.site_id                                AS site_id
       FROM tl_ovpm AS t1
         INNER JOIN tl_prsm AS t2 ON t1.site_id = t2.site_id
         INNER JOIN tm_prmr AS t3 ON t2.prmr_id = t3.id
         INNER JOIN tm_site AS t4 ON t1.site_id = t4.id
       WHERE t1.aemp_id = $request->emp_id AND t1.ovpm_date = curdate() AND curdate() BETWEEN t3.prms_sdat AND t3.prms_edat AND
             t3.lfcl_id = 1 AND t4.lfcl_id = 1  AND t1.cont_id = $request->country_id
     ) AS t1
GROUP BY t1.site_id, t1.promotion_id,t1.column_id");
            return Array(
                "tblt_promotion" => array("data" => $tst, "action" => $request->country_id),
                "tblt_promotion_modifier" => array("data" => $tst1, "action" => $request->country_id),
                "tblt_promotion_slab" => array("data" => $tst2, "action" => $request->country_id),
                "tblt_promotion_spcs" => array("data" => $tst3, "action" => $request->country_id),
            );
        }
    }

    public function dmData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, curdate(), t4.id) AS column_id,
  t1.id                           AS promo_id,
  t1.prms_name                    AS promo_name,
  curdate()                       AS start_date,
  curdate()                       AS end_date,
  t1.prmr_qfct                    AS qualifier_category,
  t1.prmr_ditp                    AS discount_type,
  t1.prmr_ctgp                    AS category_group,
  t1.prmr_qfon                    AS qualifier_on,
  t1.prmr_qfln                    AS qualifier_line
FROM tm_prmr AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.prom_id
  INNER JOIN tt_trom AS t3 ON t2.ordm_id = t3.ordm_id
  INNER JOIN tt_trip AS t4 ON t3.trip_id = t4.id AND t4.lfcl_id IN (31, 20)
WHERE t4.aemp_tusr = $request->emp_id
GROUP BY t1.id, t1.prms_name, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,
  t1.prmr_qfln, t4.id;");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.amim_id, curdate(), t4.id) AS column_id,
  t1.prmr_id                                       AS promo_id,
  t1.amim_id                                       AS sku_id,
  t1.prmd_modr                                     AS pro_modifier_id,
  t1.prmd_mosl                                     AS pro_modifier_id_sl
FROM tm_prmd AS t1
  INNER JOIN tt_ordd AS t2 ON t1.prmr_id = t2.prom_id
  INNER JOIN tt_trom AS t3 ON t2.ordm_id = t3.ordm_id
  INNER JOIN tt_trip AS t4 ON t3.trip_id = t4.id AND t4.lfcl_id IN (31, 20)
WHERE t4.aemp_tusr = $request->emp_id
GROUP BY t1.prmr_id, t1.amim_id, t1.amim_id, t1.prmd_modr, t1.prmd_mosl, t4.id");

            $tst2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.prsb_fqty, curdate(), t4.id) AS column_id,
  t1.prmr_id                                         AS promo_id,
  t1.prsb_text                                       AS promo_slab_text,
  t1.prsb_fqty                                       AS from_qty,
  t1.prsb_tqty                                       AS to_qty,
  0                                                  AS unit,
  0                                                  AS unit_factor,
  t1.prsb_famn                                       AS from_amnt,
  t1.prsb_tamn                                       AS to_amnt,
  t1.prsb_qnty                                       AS qty,
  0                                                  AS given_unit,
  0                                                  AS given_unit_factor,
  t1.prsb_disc                                       AS discount,
  t1.prsb_modr                                       AS pro_modifier_id,
  t1.prsb_mosl                                       AS pro_modifier_id_sl
FROM tm_prsb AS t1
  INNER JOIN tt_ordd AS t2 ON t1.prmr_id = t2.prom_id
  INNER JOIN tt_trom AS t3 ON t2.ordm_id = t3.ordm_id
  INNER JOIN tt_trip AS t4 ON t3.trip_id = t4.id AND t4.lfcl_id IN (31, 20)
WHERE t4.aemp_tusr = $request->emp_id
GROUP BY t1.prmr_id, t1.prsb_fqty, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
  t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl, t4.id");
            $tst3 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t3.site_id, t3.ordm_ornm, curdate()) AS column_id,
  t1.id                                              AS promotion_id,
  t3.site_id                                         AS site_id,
  t3.ordm_ornm                                       AS order_id
FROM tm_prmr AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.prom_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
  INNER JOIN tt_trom AS t4 ON t2.ordm_id = t4.ordm_id
  INNER JOIN tt_trip AS t5 ON t4.trip_id = t5.id AND t5.lfcl_id IN (31, 20)
WHERE t5.aemp_tusr = $request->emp_id
GROUP BY t1.id, t3.site_id, t3.ordm_ornm");
            return Array(
                "tblt_dm_promotion" => array("data" => $tst, "action" => $request->country_id),
                "tblt_dm_promotion_modifier" => array("data" => $tst1, "action" => $request->country_id),
                "tblt_dm_promotion_slab" => array("data" => $tst2, "action" => $request->country_id),
                "tblt_dm_promotion_spcs" => array("data" => $tst3, "action" => $request->country_id),
            );
        }
    }

    public function vanData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, curdate(), t3.id) AS column_id,
  t1.id                           AS promo_id,
  t1.prms_name                    AS promo_name,
  curdate()                       AS start_date,
  curdate()                       AS end_date,
  t1.prmr_qfct                    AS qualifier_category,
  t1.prmr_ditp                    AS discount_type,
  t1.prmr_ctgp                    AS category_group,
  t1.prmr_qfon                    AS qualifier_on,
  t1.prmr_qfln                    AS qualifier_line
FROM tm_prmr AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.prom_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
WHERE t3.aemp_id = $request->emp_id AND t3.lfcl_id = 1
GROUP BY t1.id, t1.prms_name, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,
  t1.prmr_qfln, t3.id;");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.amim_id, curdate(), t3.id) AS column_id,
  t1.prmr_id                                       AS promo_id,
  t1.amim_id                                       AS sku_id,
  t1.prmd_modr                                     AS pro_modifier_id,
  t1.prmd_mosl                                     AS pro_modifier_id_sl
FROM tm_prmd AS t1
  INNER JOIN tt_ordd AS t2 ON t1.prmr_id = t2.prom_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
WHERE t3.aemp_id = 3 AND t3.lfcl_id = 1
GROUP BY t1.prmr_id, t1.amim_id, t1.amim_id, t1.prmd_modr, t1.prmd_mosl, t3.id");

            $tst2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.prmr_id, t1.prsb_fqty, curdate(), t3.id) AS column_id,
  t1.prmr_id                                         AS promo_id,
  t1.prsb_text                                       AS promo_slab_text,
  t1.prsb_fqty                                       AS from_qty,
  t1.prsb_tqty                                       AS to_qty,
  0                                                  AS unit,
  0                                                  AS unit_factor,
  t1.prsb_famn                                       AS from_amnt,
  t1.prsb_tamn                                       AS to_amnt,
  t1.prsb_qnty                                       AS qty,
  0                                                  AS given_unit,
  0                                                  AS given_unit_factor,
  t1.prsb_disc                                       AS discount,
  t1.prsb_modr                                       AS pro_modifier_id,
  t1.prsb_mosl                                       AS pro_modifier_id_sl
FROM tm_prsb AS t1
  INNER JOIN tt_ordd AS t2 ON t1.prmr_id = t2.prom_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
WHERE t3.aemp_id = $request->emp_id AND t3.lfcl_id = 1
GROUP BY t1.prmr_id, t1.prsb_fqty, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
  t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl, t3.id");
            $tst3 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t3.site_id, t3.ordm_ornm, curdate()) AS column_id,
  t1.id                                              AS promotion_id,
  t3.site_id                                         AS site_id,
  t3.ordm_ornm                                       AS order_id
FROM tm_prmr AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.prom_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
WHERE t3.aemp_id = $request->emp_id AND t3.lfcl_id = 1
GROUP BY t1.id, t3.site_id, t3.ordm_ornm");
            return Array(
                "tblt_dm_promotion" => array("data" => $tst, "action" => $request->country_id),
                "tblt_dm_promotion_modifier" => array("data" => $tst1, "action" => $request->country_id),
                "tblt_dm_promotion_slab" => array("data" => $tst2, "action" => $request->country_id),
                "tblt_dm_promotion_spcs" => array("data" => $tst3, "action" => $request->country_id),
            );
        }
    }




}