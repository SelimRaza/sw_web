<?php
/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v4;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
 
    public function searchDataList_New(Request $request)
    {
        $search_text = $request->search_text;
        $scan_code = $request->scan_code;
        $search_type = $request->search_type;
        $where1 = '';
        $where2 = '';
        $where3 = '';
        $country = (new Country())->country($request->country_id);
        if ($country) {
            if (isset($scan_code)) {
                $where1 = "and t1.aemp_usnm='$scan_code'";
                $where2 = "and t1.site_code='$scan_code'";
                $where3 = "and t1.locd_code='$scan_code'";
            }
            /*
             * 1 sR
             * 2 Manager
             * 3 Outlet
             * 4 Location
             * 5 Depot
             */
            if ($search_type == 'User') {
                $data = " SELECT t1.id                                   AS trn_id,
                          t1.aemp_name                            AS trn_name,
                          t1.aemp_usnm                            AS trn_code,
                          ''                                 AS owner,
                          t1.aemp_mob1                            AS mobile,
                          ifnull(t3.geo_lat, 0)                   AS lat,
                          ifnull(t3.geo_lon, 0)                   AS lon,
                          if(t1.role_id = 1, 1, 2)                AS type_id,
                          t2.role_name                            AS type_name
                          FROM tm_aemp AS t1
                          INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                          LEFT JOIN tt_lloc AS t3 ON t1.id = t3.aemp_id
                          WHERE (t1.aemp_usnm LIKE '%$search_text%' OR t1.aemp_name LIKE '%$search_text%' OR t1.aemp_mob1 LIKE '%$search_text%') $where1
                          LIMIT 200";
            } else {
                $data = "SELECT  t1.id                                   AS trn_id,
                          concat(t1.site_code, '-', t1.site_name) AS trn_name,
                          t1.site_code                            AS trn_code,
                          t1.site_ownm                            AS owner,
                          t1.site_mob1                            AS mobile,
                          t1.geo_lat                              AS lat,
                          t1.geo_lon                              AS lon,
                          3                                       AS type_id,
                          'Outlet'                                AS type_name
                          FROM tm_site AS t1
                          WHERE (t1.site_code LIKE '%$search_text%' OR t1.site_name LIKE '%$search_text%' OR t1.site_mob1 LIKE '%$search_text%')AND t1.lfcl_id=1 $where2 
                          UNION ALL
                          SELECT
                          t1.id                                   AS trn_id,
                          concat(t1.locd_code, '-', t1.locd_name) AS trn_name,
                          t1.locd_code                            AS trn_code,
                          ''                                      AS owner,
                          ''                                      AS mobile,
                          t1.geo_lat                              AS lat,
                          t1.geo_lon                              AS lon,
                          4                                       AS type_id,
                          'Location'                                AS type_name
                          FROM tm_locd AS t1
                          WHERE (t1.locd_code LIKE '%$search_text%' OR t1.locd_name LIKE '%$search_text%') $where3 LIMIT 200";
            }

            $tst = DB::connection($country->cont_conn)->select($data);
            return Array("receive_data" => array("data" => $tst, "action" => $request->country_id));
        }
    }


}