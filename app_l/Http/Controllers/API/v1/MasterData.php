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

class MasterData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function masterData1(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.acmp_nvat, t1.acmp_nexc, t1.acmp_name) AS column_id,
  t1.id                                                   AS ou_id,
  t1.acmp_name                                            AS ou_name,
  t1.acmp_nvat                                            AS vat_number,
  t1.acmp_nexc                                            AS exice_number
FROM tm_acmp AS t1
WHERE t1.cont_id =$request->country_id");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
  concat(id, bank_name) AS column_id,
  id                    AS bank_id,
  bank_name             AS bank_name
FROM tm_bank;");
            return Array(
                "tbld_organization_unit" => array("data" => $tst, "action" => $request->country_id),
                "tbld_bank" => array("data" => $tst1, "action" => $request->country_id)
            );
        }
    }

    public function masterData2(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(id, ocrs_name) AS column_id,
  id                    AS reason_id,
  ocrs_name             AS reason_name
FROM tm_ocrs AS t1
WHERE t1.cont_id = $request->country_id");


            return Array(
                "tbld_order_cancel_reason" => array("data" => $tst, "action" => $request->country_id),
            );
        }
    }

    public function orderCancelReason(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(id, ocrs_name) AS column_id,
  id                    AS reason_id,
  ocrs_name             AS reason_name
FROM tm_ocrs AS t1
WHERE t1.cont_id = $request->country_id");


            return Array(
                "tbld_order_cancel_reason" => array("data" => $tst, "action" => $request->country_id),
            );
        }
    }


}