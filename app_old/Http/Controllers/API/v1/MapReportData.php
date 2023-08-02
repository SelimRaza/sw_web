<?php

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Employee;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapReportData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function searchList(Request $request)
    {

        $search_text = $request->search_text;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id                                                             AS site_id,
  t1.site_name COLLATE utf8_general_ci                              AS site_name,
  t1.site_mob1 COLLATE utf8_general_ci                              AS mobile,
  t1.geo_lat                                                        AS lat,
  t1.geo_lon                                                        AS lon,
  ''                                                                AS region_name,
  ''                                                                AS zone_name,
  ''                                                                AS base_name,
  concat(t4.aemp_name, ' - ', t4.aemp_usnm) COLLATE utf8_general_ci AS emp_name,
  concat(t5.aemp_name, ' - ', t5.aemp_usnm) COLLATE utf8_general_ci AS sv_name,
  0                                                                 AS dist_dif,
  0                                                                 AS time_dif,
  0                                                                 AS type,
  ''                                                                AS last_time,
  t1.site_vrfy                                                      AS is_verified,
  ''                                                                AS user_name,
  0                                                                 AS emp_id,
  0                                                                 AS emp_code,
  0                                                                 AS role_id,
  1                                                                 AS channel_id
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
  INNER JOIN tl_rpln AS t3 ON t2.rout_id = t3.rout_id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
WHERE (t1.site_name LIKE '%$search_text%' OR t1.site_code LIKE '%$search_text%' OR
       t1.site_mob1 LIKE '%$search_text%') AND t1.lfcl_id = 1
GROUP BY t1.id, t1.site_name, t1.site_mob1, t1.geo_lat, t1.geo_lon, t4.aemp_name, t4.aemp_usnm, t5.aemp_name, t5.aemp_usnm,
  t1.site_vrfy
UNION ALL
SELECT
  t2.id                                      AS site_id,
  concat(t2.aemp_name, '-', t2.aemp_usnm)    AS site_name,
  t2.aemp_mob1                               AS mobile,
  t1.geo_lat                                 AS lat,
  t1.geo_lon                                 AS lon,
  ''                                         AS region_name,
  ''                                         AS zone_name,
  ''                                         AS base_name,
  concat(t2.aemp_name, '-', t2.aemp_usnm)    AS emp_name,
  concat(t3.aemp_name, '-', t3.aemp_usnm)    AS sv_name,
  0                                          AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.lloc_time, now()) AS time_dif,
  t2.role_id                                 AS type,
  t1.lloc_time                               AS last_time,
  1                                          AS is_verified,
  t2.aemp_usnm                               AS user_name,
  t2.id                                      AS emp_id,
  t2.id                                      AS emp_code,
  t2.role_id                                 AS role_id,
  0                                          AS channel_id
FROM tt_lloc AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_mngr = t3.id
WHERE (t2.aemp_usnm LIKE '%$search_text%' OR t2.aemp_name LIKE '%$search_text%') AND t2.lfcl_id = 1");
            return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }

    }

    public function aroundList(Request $request)
    {
        $distance = 0.70;
        $emp_id = $request->emp_id;
        $lat = $request->lat;
        $lon = $request->lon;
        $time = date('Y-m-d H:i:s');
        $country = (new Country())->country($request->country_id);
        if ($country) {


            $tst = DB::connection($country->cont_conn)->select("SELECT *
FROM (
       SELECT
         t1.id                                                             AS site_id,
         t1.site_name COLLATE utf8_general_ci                              AS site_name,
         t1.site_mob1 COLLATE utf8_general_ci                              AS mobile,
         t1.geo_lat                                                        AS lat,
         t1.geo_lon                                                        AS lon,
         ''                                                                AS region_name,
         ''                                                                AS zone_name,
         ''                                                                AS base_name,
         concat(t4.aemp_name, ' - ', t4.aemp_usnm) COLLATE utf8_general_ci AS emp_name,
         concat(t5.aemp_name, ' - ', t5.aemp_usnm) COLLATE utf8_general_ci AS sv_name,
         0                                                                 AS dist_dif,
         0                                                                 AS time_dif,
         0                                                                 AS type,
         ''                                                                AS last_time,
         t1.site_vrfy                                                      AS is_verified,
         ''                                                                AS user_name,
         0                                                                 AS emp_id,
         0                                                                 AS emp_code,
         0                                                                 AS role_id,
         1                                                                 AS channel_id
       FROM tm_site AS t1
         INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
         INNER JOIN tl_rpln AS t3 ON t2.rout_id = t3.rout_id
         INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
       WHERE t1.geo_lon != 0 AND t1.lfcl_id = 1
       GROUP BY t1.id, t1.site_name, t1.site_mob1, geo_lat, t1.geo_lon, t4.aemp_name, t4.aemp_usnm, t5.aemp_name, t5.aemp_usnm,
         t1.site_vrfy
       HAVING dist_dif < $distance
       UNION ALL
       SELECT
         t2.id                                      AS site_id,
         concat(t2.aemp_name, '-', t2.aemp_usnm)    AS site_name,
         t2.aemp_mob1                               AS mobile,
         t1.geo_lat                                 AS lat,
         t1.geo_lon                                 AS lon,
         ''                                         AS region_name,
         ''                                         AS zone_name,
         ''                                         AS base_name,
         concat(t2.aemp_name, '-', t2.aemp_usnm)    AS emp_name,
         concat(t3.aemp_name, '-', t3.aemp_usnm)    AS sv_name,
         0                                          AS dist_dif,
         TIMESTAMPDIFF(MINUTE, t1.lloc_time, now()) AS time_dif,
         t2.role_id                                 AS type,
         t1.lloc_time                               AS last_time,
         1                                          AS is_verified,
         t2.aemp_usnm                               AS user_name,
         t2.id                                      AS emp_id,
         t2.id                                      AS emp_code,
         t2.role_id                                 AS role_id,
         0                                          AS channel_id
       FROM tt_lloc AS t1
         INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
         INNER JOIN tm_aemp AS t3 ON t2.aemp_mngr = t3.id
       WHERE t1.geo_lon != 0 AND TIMESTAMPDIFF(MINUTE, t1.lloc_time, '$time') < 120 AND t2.lfcl_id = 1
       HAVING dist_dif < $distance
     ) AS t1");
            return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }
    }

    public function empList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            $time = date('Y-m-d H:i:s');
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t2.id                                      AS site_id,
  concat(t2.aemp_name, '-', t2.aemp_usnm)    AS site_name,
  t2.aemp_mob1                               AS mobile,
  t1.geo_lat                                 AS lat,
  t1.geo_lon                                 AS lon,
  ''                                         AS region_name,
  ''                                         AS zone_name,
  ''                                         AS base_name,
  concat(t2.aemp_name, '-', t2.aemp_usnm)    AS emp_name,
  concat(t3.aemp_name, '-', t3.aemp_usnm)    AS sv_name,
  0                                          AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.lloc_time, now()) AS time_dif,
  t2.role_id                                 AS type,
  t1.lloc_time                               AS last_time,
  1                                          AS is_verified,
  t2.aemp_usnm                               AS user_name,
  t2.id                                      AS emp_id,
  t2.id                                      AS emp_code,
  t2.role_id                                 AS role_id,
  0                                          AS channel_id
FROM tt_lloc AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_mngr = t3.id
WHERE t1.geo_lon != 0 AND TIMESTAMPDIFF(MINUTE, t1.lloc_time,'$time') < 120 AND t2.lfcl_id = 1");
            return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }
    }

    public function historyList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $time = date('Y-m-d H:i:s');
            $tst = DB::connection($country->cont_conn)->select("SELECT
                  t1.id        AS site_id,
                  ''           AS site_name,
                  ''           AS mobile,
                  t1.geo_lat   AS lat,
                  t1.geo_lon   AS lon,
                  ''           AS region_name,
                  ''           AS zone_name,
                  ''           AS base_name,
                  ''           AS emp_name,
                  ''           AS sv_name,
                  0            AS dist_dif,
                  0           AS time_dif,
                  1            AS type,
                  t1.hloc_time AS last_time,
                  1            AS is_verified,
                  ''           AS user_name,
                  0           AS emp_id,
                  0           AS emp_code,
                  0            AS role_id,
                  0            AS channel_id
                FROM th_hloc AS t1
                WHERE t1.hloc_date = '$request->date' AND t1.aemp_id = $request->emp_id
                ORDER BY t1.id DESC");
            return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }
    }

    public function aroundMe(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            $whereCondition = "";
            $distance = $request->distance;
            $lat = $request->lat;
            $lon = $request->lon;
            if (isset($request->company_name)) {
                $whereCondition .= "and t6.acmp_name='$request->company_name'";
            }
            if (isset($request->group_name)) {
                $whereCondition .= " and t5.slgp_name ='$request->group_name' ";
            }
            if (isset($request->role_name)) {
                $whereCondition .= " and t7.role_name ='$request->role_name' ";
            }
            $query = "SELECT *
FROM (
   SELECT
  t1.id                                                                      AS site_id,
  concat(t2.aemp_name, '-', t2.aemp_usnm)                                    AS site_name,
  t2.aemp_mob1                                                               AS mobile,
  t1.geo_lat                                                                 AS lat,
  t1.geo_lon                                                                 AS lon,
  ''                                                                         AS region_name,
  ''                                                                         AS zone_name,
  ''                                                                         AS base_name,
  t2.aemp_name                                                               AS emp_name,
  ''                                                                         AS sv_name,
  getDistance(t1.geo_lat, t1.geo_lon, $lat, $lon)                            AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.lloc_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
  1                                                                          AS type,
  t1.lloc_time                                                               AS last_time,
  1                                                                          AS is_verified,
  t2.aemp_usnm                                                               AS user_name,
  t2.id                                                                      AS emp_id,
  t1.id                                                                      AS emp_code,
  t2.role_id                                                                 AS role_id,
  0                                                                          AS channel_id,
  t5.slgp_name                                                               AS group_name,
  t7.role_name                                                               AS role_name
FROM tt_lloc AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_cont AS t3 ON t2.cont_id = t3.id
  LEFT JOIN tl_sgsm AS t4 ON t1.aemp_id = t4.aemp_id
  LEFT JOIN tm_slgp AS t5 ON t4.slgp_id = t5.id
  LEFT JOIN tm_acmp AS t6 ON t5.acmp_id = t6.id
  INNER JOIN tm_role AS t7 ON t2.role_id = t7.id
WHERE t1.geo_lat != 0  AND t2.aemp_issl=1 
       $whereCondition
HAVING dist_dif < $distance
     ) AS t1";
            $tst = DB::connection($country->cont_conn)->select($query);
            return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
        }

    }

    public function filterRoleData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data3 = DB::connection($country->cont_conn)->select("SELECT t1.id,t1.role_name AS role_name,t1.role_name as role_code
FROM tm_role AS t1 where t1.id <7");

            return Array(
                "role_list" => array("data" => $data3, "action" => $request->country_id),
            );
        }
    }

    public function filterAcmpData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $whereCondition2 = '1';

            if (isset($request->company_name)) {
                $whereCondition2 .= " and  t1.acmp_name ='$request->company_name' ";
            }


            $data1 = DB::connection($country->cont_conn)->select("SELECT
  t1.id,
  t1.acmp_name,
  t1.acmp_name as acmp_code
FROM tm_acmp AS t1");

            return Array(
                "company_list" => array("data" => $data1, "action" => $request->country_id),
            );
        }

    }
    public function filterSlgpData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $whereCondition2 = '1';

            if (isset($request->company_name)) {
                $whereCondition2 .= " and  t2.acmp_name ='$request->company_name' ";
            }


            $data2 = DB::connection($country->cont_conn)->select("SELECT
  t1.id,
  t1.slgp_name,
  t1.slgp_name as slgp_code
FROM tm_slgp AS t1
  INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
WHERE $whereCondition2");

            return Array(
                "group_list" => array("data" => $data2, "action" => $request->country_id),
            );
        }

    }

}