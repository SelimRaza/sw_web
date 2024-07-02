<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v3;

use App\BusinessObject\BlockHistory;
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockOrderData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function specialBlockOrder(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.ordm_ornm AS column_id,
  t1.acmp_id   AS ou_id,
  t1.ordm_ornm AS order_id,
  t1.ordm_date AS date,
  t1.ordm_amnt AS order_amount,
  t1.site_id   AS site_id,
  t3.site_name AS site_name,
  t1.lfcl_id   AS status_id,
  t1.id        AS sr_id,
  t2.aemp_name AS sr_name
FROM tt_ordm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
WHERE t2.aemp_mngr = $request->emp_id AND t1.lfcl_id = 17");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
  t2.id                       AS column_id,
  t1.ordm_ornm                AS order_id,
  t2.amim_id                  AS product_id,
  t3.amim_name                AS product_name,
  t2.ordd_duft                AS ctn_size,
  t2.ordd_uprc                AS unit_price,
  t2.ordd_uprc * t2.ordd_duft AS ctn_price,
  t2.ordd_qnty                AS order_qty,
  t2.ordd_qnty                AS Confirm_qty,
  t2.ordd_dfdo                   default_discount,
  t2.ordd_spdo                AS discount,
  t2.ordd_spdo                AS confirm_discount,
  t2.ordd_opds                AS promo_discount,
  t2.ordd_oamt                AS total_amount,
  t2.prom_id                  AS promo_ref
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
WHERE t1.lfcl_id = 17 AND t4.aemp_mngr = $request->emp_id");
            return Array(
                "tblt_block_order" => array("data" => $tst, "action" => $request->country_id),
                "tblt_block_order_line" => array("data" => $tst1, "action" => $request->country_id)
            );
        }
    }

    public function specialBlockBalance(Request $request) {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("SELECT t1.spbm_amnt AS balance
FROM tt_spbm AS t1
WHERE t1.aemp_id = $request->emp_id AND t1.spbm_mnth = MONTH(curdate()) AND t1.spbm_year = year(curdate()) AND t1.cont_id = $request->country_id"))->first();
            return Array(
                "receive_data" => array("data" => $tst, "action" => $request->country_id),
            );
        }
    }
 

}