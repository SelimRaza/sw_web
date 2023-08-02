<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v3;

use App\BusinessObject\BaseSiteMapping;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Outlet;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
 
 

    public function district(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.dsct_name AS name,
  t1.dsct_code AS code
FROM tm_dsct AS t1;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function thana(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.than_name AS name,
  t1.than_code AS code
FROM tm_than AS t1
WHERE t1.dsct_id = $request->district_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function ward(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.ward_name AS name,
  t1.ward_code AS code
FROM tm_ward AS t1
WHERE t1.than_id = $request->thana_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function market(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.mktm_name AS name,
  t1.mktm_code AS code
FROM tm_mktm AS t1
WHERE t1.ward_id = $request->ward_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function routeSite(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  t3.id AS site_id,
  t3.site_code,
  t3.site_name,
  t3.site_olnm,
  t3.site_adrs,
  t3.site_olad,
  t3.site_ownm,
  t3.site_olon,
  t3.site_mob1,
  t3.site_mob2,
  t3.site_emal,
  t3.site_reg,
  t3.site_hsno,
  t3.site_vtrn,
  t3.site_vrfy
FROM tm_rout AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.rout_id
  INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
WHERE t1.id = $request->route_id AND t3.lfcl_id IN (1, 7)");
            return array("receive_data" => $data1, "action" => $request->route_id);
        }
    }

     

    
}