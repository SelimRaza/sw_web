<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function searchDataList(Request $request)
    {
        $search_text = $request->search_text;
        $tst = DB::connection('pran_live')->select("SELECT
  t1.id                                         AS site_id,
  concat(t1.Outlet_ID, '-', t1.Outlet_Name)     AS site_name,
  t1.Mobile                                     AS mobile,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', 1)  AS lat,
  SUBSTRING_INDEX(t1.Location_Lat_Lon, ',', -1) AS lon,
  ''                                            AS region_name,
  ''                                            AS zone_name,
  ''                                            AS base_name,
  ''                                            AS emp_name,
  ''                                            AS sv_name,
  0                                             AS dist_dif,
  0                                             AS time_dif,
  1                                             AS type,
  ''                                            AS last_time,
  1                                             AS is_verified,
  t1.Outlet_ID                                  AS user_name,
  t1.id                                         AS emp_id,
  t1.id                                         AS emp_code,
  0                                             AS role_id,
  0                                             AS channel_id,
  ''                                            AS bu,
  ''                                            AS group_name,
  ''                                            AS role_name,
  ''                                            AS type_name
FROM outletmaster_Pran_Data AS t1
WHERE t1.Outlet_ID LIKE '%$search_text%' OR t1.Outlet_Name LIKE '%$search_text%' OR t1.Mobile LIKE '%$search_text%'
");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

  public function searchOutletDataList(Request $request)
   {
       $tst = DB::connection('pran_live')->select("SELECT
  t1.outlet_id   AS site_id,
  t1.outlet_id   AS site_code,
  t1.outlet_name AS site_name,
  ''             AS owner_name,
  ''             AS mobile,
  ''             AS visitCount,
  ''             AS invoiceCount,
  ''             AS skuCount,
  ''             AS routeFrequency,
  ''             AS subCategoryCount,
  0              AS orderAmount,
  0              AS invoiceAmount,
  1              AS lpc
FROM tblh_order_master AS t1
WHERE t1.outlet_id = '$request->site_code' limi 1");
       return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));

   }


}