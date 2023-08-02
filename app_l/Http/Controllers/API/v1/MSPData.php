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

class MSPData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function mspData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t2.amim_id, t2.mspd_qnty) AS column_id,
  t1.id                                   AS msp_id,
  t1.mspm_name                            AS msp_name,
  t2.amim_id                              AS sku_id,
  t6.amim_name                            AS sku_name,
  t2.mspd_qnty                            AS qty
FROM tm_mspm AS t1
  INNER JOIN tm_mspd AS t2 ON t1.id = t2.mspm_id
  INNER JOIN tl_msps AS t3 ON t1.id = t3.mspm_id
  INNER JOIN tl_rsmp AS t4 ON t3.site_id = t4.site_id
  INNER JOIN tl_rpln AS t5 ON t4.rout_id = t5.rout_id
  INNER JOIN tm_amim AS t6 ON t2.amim_id = t6.id
WHERE t5.aemp_id = $request->emp_id AND curdate() BETWEEN t1.mspm_sdat AND t1.mspm_edat AND t1.lfcl_id = 1
GROUP BY t1.id, t2.amim_id, t2.mspd_qnty, t1.mspm_name, t6.amim_name;");


            return Array(
                "tblt_must_sell_product" => array("data" => $tst, "action" => $request->country_id),
            );
        }
    }





}