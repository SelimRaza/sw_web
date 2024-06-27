<?php

namespace App\Http\Controllers\API;

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


    public function mapSiteList(Request $request)
    {
        $tst = DB::select("SELECT
  t1.id                                AS site_id,
  t1.name                              AS site_name,
  t1.mobile_1                          AS mobile,
  t1.lat                               AS lat,
  t1.lon                               AS lon,
  ''                                   AS region_name,
  ''                                   AS zone_name,
  ''                                   AS base_name,
  concat(t4.name, ' - ', t4.user_name) AS emp_name,
  concat(t5.name, ' - ', t5.user_name) AS sv_name,
  0                                    AS dist_dif,
  0                                    AS time_dif,
  0                                    AS type,
  max(t7.date)                         AS last_time,
  t1.is_verified                       AS is_verified,
  ''                                   AS user_name,
  0                                    AS emp_id,
  0                                    AS emp_code,
  0                                    AS role_id,
  t6.id                                AS channel_id,
  datediff(curdate(), max(t7.Date))    AS visited
FROM tbld_site AS t1
  INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
  INNER JOIN tbld_pjp AS t3 ON t2.route_id = t3.route_id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
  INNER JOIN tbld_employee AS t5 ON t4.line_manager_id = t5.id
  INNER JOIN tbld_sub_channel AS t6 ON t1.sub_channel_id = t6.id
  LEFT JOIN tblt_site_visited AS t7 ON t1.id = t7.site_id
WHERE 1 AND t1.status_id = 1 AND t3.emp_id = $request->emp_id
GROUP BY t1.id, t1.name, t1.mobile_1, t1.lat, t1.lon, t4.name, t4.user_name, t5.name, t5.user_name, t1.is_verified,
  t6.id");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapAllEmpList(Request $request)
    {
        $tst = DB::select("SELECT
  t1.id                                      AS site_id,
  ''         AS site_name,
  t1.mobile_no                               AS mobile,
  t1.lat                                     AS lat,
  t1.lon                                     AS lon,
  ''                                         AS region_name,
  ''                                         AS zone_name,
  ''                                         AS base_name,
  concat(t1.name, '-', t1.user_name)         AS emp_name,
  ''         AS sv_name,
  0                                          AS dist_dif,
  TIMESTAMPDIFF(MINUTE,  now(),t1.date_time) AS time_dif,
  1                                          AS type,
  t1.date_time                               AS last_time,
  1                                          AS is_verified,
  t1.user_name                               AS user_name,
  t1.id                                      AS emp_id,
  t1.id                                      AS emp_code,
  1                                          AS role_id,
  0                                          AS channel_id,
  
         t1.bu,
         t1.group_name,
         t1.role_name,
         t1.type_name
FROM tblt_user_tracking AS t1 WHERE t1.lat>0  AND TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) <380");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapEmpList(Request $request)
    {
        $tst = DB::select("SELECT
  t2.id                               AS site_id,
  concat(t2.name, '-', t2.user_name)        AS site_name,
  t2.mobile                                 AS mobile,
  t1.lat                                    AS lat,
  t1.lon                                    AS lon,
  ''                                        AS region_name,
  ''                                        AS zone_name,
  ''                                        AS base_name,
  concat(t2.name, '-', t2.user_name)        AS emp_name,
  concat(t3.name, '-', t3.user_name)        AS sv_name,
  0                                         AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.times_stamp, now()) AS time_dif,
  t2.master_role_id                         AS type,
  t1.times_stamp                            AS last_time,
  1                                         AS is_verified,
  t2.user_name                              AS user_name,
  t2.id                                     AS emp_id,
  t2.id                                     AS emp_code,
  t2.role_id                                AS role_id,
  0                                         AS channel_id
FROM tblt_current_location AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t2.line_manager_id = t3.id
WHERE t1.lat != 0 AND TIMESTAMPDIFF(MINUTE, t1.times_stamp, now()) < 120 AND t2.status_id = 1");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapEmpHistoryList(Request $request)
    {
        $tst = DB::select("SELECT
  t2.id                              AS site_id,
  concat(t2.name, '-', t2.user_name)        AS site_name,
  t2.Mobile                                 AS mobile,
  t1.lat                                    AS lat,
  t1.lon                                    AS lon,
  ''                                        AS region_name,
  ''                                        AS zone_name,
  ''                                        AS base_name,
  concat(t2.name, '-', t2.user_name)        AS emp_name,
  concat(t3.name, '-', t3.user_name)        AS sv_name,
  0                                         AS dist_dif,
  TIMESTAMPDIFF(MINUTE, times_stamp, now()) AS time_dif,
  t2.role_id                                AS type,
  t1.times_stamp                            AS last_time,
  1                                         AS is_verified,
  t2.user_name                              AS user_name,
  t2.id                                     AS emp_id,
  t2.id                                     AS emp_code,
  t2.role_id                                AS role_id,
  0                                         AS channel_id
FROM tblt_location_history AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t2.line_manager_id = t3.id
WHERE t1.date = '$request->date' AND t1.emp_id = $request->emp_id AND t2.status_id = 1
ORDER BY t1.id DESC");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }
  public function mapUserHistoryList(Request $request)
    {
        $tst = DB::select("SELECT
  0                                                                          AS site_id,
  concat(t1.name, '-', t1.user_name)                                         AS site_name,
  t1.mobile_no                                                               AS mobile,
  t1.lat                                                                     AS lat,
  t1.lon                                                                     AS lon,
  ''                                                                         AS region_name,
  ''                                                                         AS zone_name,
  ''                                                                         AS base_name,
  ''                                                                         AS emp_name,
  ''                                                                         AS sv_name,
  0                                                                          AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
  1                                                                          AS type,
  t1.date_time                                                               AS last_time,
  1                                                                          AS is_verified,
  t1.user_name                                                               AS user_name,
  0                                                                          AS emp_id,
  0                                                                          AS emp_code,
  0                                                                          AS role_id,
  0                                                                          AS channel_id,
  t1.type_name
FROM tblt_user_tracking_details AS t1
WHERE date(t1.date_time) = curdate() AND t1.user_name = '$request->user_name'
ORDER BY t1.id DESC");
        return Array("receive_data" => array("data" => $tst, "action" => $request->user_name));
    }

    public function mapSearchList(Request $request)
    {
        $search_text = $request->search_text;
        $emp_id = $request->emp_id;
        $tst = DB::select("SELECT
   t1.id                                                        AS site_id,
         t1.name COLLATE utf8_general_ci                              AS site_name,
         t1.mobile_1 COLLATE utf8_general_ci                          AS mobile,
         t1.lat                                                       AS lat,
         t1.lon                                                       AS lon,
         ''                                                           AS region_name,
         ''                                                           AS zone_name,
         ''                                                           AS base_name,
         concat(t4.name, ' - ', t4.user_name) COLLATE utf8_general_ci AS emp_name,
         concat(t5.name, ' - ', t5.user_name) COLLATE utf8_general_ci AS sv_name,
         0                                                            AS dist_dif,
         0                                                            AS time_dif,
         0                                                            AS type,
         ''                                                           AS last_time,
         t1.is_verified                                               AS is_verified,
         ''                                                           AS user_name,
         0                                                            AS emp_id,
         0                                                            AS emp_code,
         0                                                            AS role_id,
         1                                                            AS channel_id
FROM tbld_site AS t1
  INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
  INNER JOIN tbld_pjp AS t3 ON t2.Route_ID = t3.Route_ID
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
  INNER JOIN tbld_employee AS t5 ON t4.line_manager_id = t5.id
WHERE (t1.id LIKE '%111%' OR t1.name LIKE '%$search_text%' OR t1.code LIKE '%$search_text%' OR
       t1.mobile_1 LIKE '%$search_text%') AND t1.status_id = 1
GROUP BY t1.id, t1.name,t1.mobile_1,lat,t1.lon,t4.name,t4.user_name,t5.name,t5.user_name,t1.is_verified
UNION ALL
SELECT
          t2.id                                               AS site_id,
         concat(t2.name, '-', t2.user_name) COLLATE utf8_general_ci AS site_name,
         t2.mobile COLLATE utf8_general_ci                          AS mobile,
         t1.lat                                                     AS lat,
         t1.lon                                                     AS lon,
         ''                                                         AS region_name,
         ''                                                         AS zone_name,
         ''                                                         AS base_name,
         concat(t2.name, '-', t2.user_name) COLLATE utf8_general_ci AS emp_name,
         concat(t3.name, '-', t3.user_name) COLLATE utf8_general_ci AS sv_name,
         0                                                          AS dist_dif,
         TIMESTAMPDIFF(MINUTE, times_stamp, now())                  AS time_dif,
          t2.master_role_id                                                AS type,
         t1.times_stamp                                             AS last_time,
         1                                                          AS is_verified,
         t2.user_name COLLATE utf8_general_ci                       AS user_name,
         t2.id                                                      AS emp_id,
         t2.id                                                      AS emp_code,
         t2.role_id                                                 AS role_id,
         2                                                          AS channel_id
FROM tblt_current_location AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t2.line_manager_id = t3.id
WHERE (t2.user_name LIKE '%$search_text%' OR t2.name LIKE '%$search_text%') AND t2.status_id = 1");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapAroundList(Request $request)
    {
        $distance = 0.70;
        $emp_id = $request->emp_id;
        $lat = $request->lat;
        $lon = $request->lon;
        $tst = DB::select("SELECT *
FROM (
       SELECT
         t1.id                                                        AS site_id,
         t1.name COLLATE utf8_general_ci                              AS site_name,
         t1.mobile_1 COLLATE utf8_general_ci                          AS mobile,
         t1.lat                                                       AS lat,
         t1.lon                                                       AS lon,
         ''                                                           AS region_name,
         ''                                                           AS zone_name,
         ''                                                           AS base_name,
         concat(t4.name, ' - ', t4.user_name) COLLATE utf8_general_ci AS emp_name,
         concat(t5.name, ' - ', t5.user_name) COLLATE utf8_general_ci AS sv_name,
         getDistance(t1.lat, t1.lon, $lat, $lon)                      AS dist_dif,
         0                                                            AS time_dif,
         0                                                            AS type,
         ''                                                           AS last_time,
         t1.is_verified                                               AS is_verified,
         ''                                                           AS user_name,
         0                                                            AS emp_id,
         0                                                            AS emp_code,
         0                                                            AS role_id,
         1                                                            AS channel_id
       FROM tbld_site AS t1
         INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
         INNER JOIN tbld_pjp AS t3 ON t2.route_id = t3.route_id
         INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
         INNER JOIN tbld_employee AS t5 ON t4.line_manager_id = t5.id
       WHERE t1.lat != 0 AND t1.status_id = 1
       HAVING dist_dif < $distance
       UNION ALL
       SELECT
         t2.id                                                      AS site_id,
         concat(t2.name, '-', t2.user_name) COLLATE utf8_general_ci AS site_name,
         t2.mobile COLLATE utf8_general_ci                          AS mobile,
         t1.lat                                                     AS lat,
         t1.lon                                                     AS lon,
         ''                                                         AS region_name,
         ''                                                         AS zone_name,
         ''                                                         AS base_name,
         concat(t2.name, '-', t2.user_name) COLLATE utf8_general_ci AS emp_name,
         concat(t3.name, '-', t3.user_name) COLLATE utf8_general_ci AS sv_name,
         getDistance(t1.lat, t1.lon, $lat, $lon)                    AS dist_dif,
         TIMESTAMPDIFF(MINUTE, times_stamp, now())                  AS time_dif,
          t2.master_role_id                                                AS type,
         t1.times_stamp                                             AS last_time,
         1                                                          AS is_verified,
         t2.user_name COLLATE utf8_general_ci                       AS user_name,
         t2.id                                                      AS emp_id,
         t2.id                                                      AS emp_code,
         t2.role_id                                                 AS role_id,
         2                                                          AS channel_id
       FROM tblt_current_location AS t1
         INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
         INNER JOIN tbld_employee AS t3 ON t2.line_manager_id = t3.id
       WHERE t1.lat != 0 and t1.emp_id!=$emp_id AND TIMESTAMPDIFF(MINUTE, t1.times_stamp, now()) < 120 AND t2.status_id = 1
       HAVING dist_dif < $distance
     ) AS t1 ");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapTrackAroundList(Request $request)
    {
        $distance = $request->distance;
        $emp_id = $request->emp_id;
        $lat = $request->lat;
        $lon = $request->lon;

        /*$tst = DB::select("SELECT *
FROM (
       SELECT
         t1.id                                                                      AS site_id,
         concat(t1.name, '-', t1.user_name)                                         AS site_name,
         t1.mobile_no                                                               AS mobile,
         t1.lat                                                                     AS lat,
         t1.lon                                                                     AS lon,
         ''                                                                         AS region_name,
         ''                                                                         AS zone_name,
         ''                                                                         AS base_name,
         ''                                        AS emp_name,
         ''                                         AS sv_name,
         getDistance(t1.lat, t1.lon, $lat, $lon)                        AS dist_dif,
         TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
         1                                                                          AS type,
         t1.date_time                                                               AS last_time,
         1                                                                          AS is_verified,
         t1.user_name                                                               AS user_name,
         t1.id                                                                      AS emp_id,
         t1.id                                                                      AS emp_code,
         1                                                                          AS role_id,
         0                                                                          AS channel_id,
         t1.bu,
         t1.group_name,
         t1.role_name,
         t1.type_name

       FROM tblt_user_tracking AS t1
       WHERE t1.lat > 0 AND TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) < 680
       HAVING dist_dif < $distance
     ) AS t1");*/
        $tst = DB::select("SELECT *
FROM (
       SELECT
         t1.id                                                                      AS site_id,
         concat(t1.name, '-', t1.user_name)                                         AS site_name,
         t1.mobile_no                                                               AS mobile,
         t1.lat                                                                     AS lat,
         t1.lon                                                                     AS lon,
         ''                                                                         AS region_name,
         ''                                                                         AS zone_name,
         ''                                                                         AS base_name,
         ''                                        AS emp_name,
         ''                                         AS sv_name,
         getDistance(t1.lat, t1.lon, $lat, $lon)                        AS dist_dif,
         TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
         1                                                                          AS type,
         t1.date_time                                                               AS last_time,
         1                                                                          AS is_verified,
         t1.user_name                                                               AS user_name,
         t1.id                                                                      AS emp_id,
         t1.id                                                                      AS emp_code,
         1                                                                          AS role_id,
         0                                                                          AS channel_id,
         t1.bu,
         t1.group_name,
         t1.role_name,
         t1.type_name
         
       FROM tblt_user_tracking AS t1
       WHERE t1.lat > 0 AND DATE_SUB(CONVERT_TZ(now(), '+00:00', '+06:00'), INTERVAL 240 MINUTE) < t1.`date_time`
       HAVING dist_dif < $distance
     ) AS t1");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function mapSiteDetails(Request $request)
    {
        $site_id = $request->site_id;
        $tst = DB::select("SELECT
  t1.id         AS site_id,
  t1.name       AS site_name,
  t1.mobile_1   AS mobile,
  t1.lat        AS lat,
  t1.lon        AS lon,
  t1.address    AS address,
  t1.owner_name AS owner_name,
  t1.house_no   AS house_no,
  t1.vat_trn    AS vat_trn
FROM tbld_site AS t1
WHERE t1.id = '$site_id' AND t1.status_id = 1 ");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }


    public function mapTrackAroundListNew(Request $request)
    {
        $whereCondition="";
        $distance = $request->distance;
        $soft_name = $request->soft_name;
        $company_name = $request->company_name;
        $group_name = $request->group_name;
        $role_name = $request->role_name;
        $emp_id = $request->emp_id;
        $lat = $request->lat;
        $lon = $request->lon;
        if ($soft_name!=""){
            $whereCondition.="and  t1.bu='$soft_name' ";
        }
        if ($company_name!=""){
            $whereCondition.="and t1.com_name='$company_name'";
        }
        if ($group_name!=""){
            $whereCondition.=" and t1.group_name ='$group_name' ";
        }
        if ($role_name!=""){
            $whereCondition.=" and t1.role_name ='$role_name' ";
        }
        $query="SELECT *
FROM (
       SELECT
         t1.id                                                                      AS site_id,
         concat(t1.name, '-', t1.user_name)                                         AS site_name,
         t1.mobile_no                                                               AS mobile,
         t1.lat                                                                     AS lat,
         t1.lon                                                                     AS lon,
         ''                                                                         AS region_name,
         ''                                                                         AS zone_name,
         ''                                                                         AS base_name,
         ''                                        AS emp_name,
         ''                                         AS sv_name,
         getDistance(t1.lat, t1.lon, $lat, $lon)                        AS dist_dif,
         TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
         1                                                                          AS type,
         t1.date_time                                                               AS last_time,
         1                                                                          AS is_verified,
         t1.user_name                                                               AS user_name,
         t1.id                                                                      AS emp_id,
         t1.id                                                                      AS emp_code,
         1                                                                          AS role_id,
         0                                                                          AS channel_id,
         t1.bu,
         t1.group_name,
         t1.role_name,
         t1.type_name
         
       FROM tblt_user_tracking AS t1
       WHERE t1.lat > 0 AND DATE_SUB(CONVERT_TZ(now(), '+00:00', '+06:00'), INTERVAL 440 MINUTE) < t1.`date_time` $whereCondition
       HAVING dist_dif < $distance
     ) AS t1";
        $tst = DB::select($query);
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }
    public function filterMapData(Request $request)
    {


        $data1 = DB::select("SELECT t1.com_name
FROM tblt_user_tracking AS t1
WHERE t1.com_name NOT IN ('0', '')
GROUP BY t1.com_name");
        $data2 = DB::select("SELECT t1.group_name
FROM tblt_user_tracking AS t1
WHERE t1.group_name NOT IN ('0', '')
GROUP BY t1.group_name");
        $data3 = DB::select("SELECT t1.name as role_name
FROM tbld_master_role AS t1");
        $data4 = DB::select("SELECT t1.short_name as type_name,
t1.name as dist_name
FROM tbld_distributor_type AS t1");

        return Array(
            "company_list" => array("data" => $data1, "action" => $request->country_id),
            "group_list" => array("data" => $data2, "action" => $request->country_id),
            "role_list" => array("data" => $data3, "action" => $request->country_id),
            "dist_type_list" => array("data" => $data4, "action" => $request->country_id),
        );
    }


    public function mapSearchUserList(Request $request)
    {
        $search_text = $request->search_text;
        $tst = DB::select("SELECT
  t1.id                                                                      AS site_id,
  concat(t1.name, '-', t1.user_name)                                         AS site_name,
  t1.mobile_no                                                               AS mobile,
  t1.lat                                                                     AS lat,
  t1.lon                                                                     AS lon,
  ''                                                                         AS region_name,
  ''                                                                         AS zone_name,
  ''                                                                         AS base_name,
  ''                                                                         AS emp_name,
  ''                                                                         AS sv_name,
  0                                                                          AS dist_dif,
  TIMESTAMPDIFF(MINUTE, t1.date_time, CONVERT_TZ(now(), '+00:00', '+06:00')) AS time_dif,
  1                                                                          AS type,
  t1.date_time                                                               AS last_time,
  1                                                                          AS is_verified,
  t1.user_name                                                               AS user_name,
  t1.id                                                                      AS emp_id,
  t1.id                                                                      AS emp_code,
  t1.master_role_id                                                          AS role_id,
  0                                                                          AS channel_id,
  t1.bu,
  t1.group_name,
  t1.role_name,
  t1.type_name
FROM tblt_user_tracking AS t1
WHERE t1.user_name LIKE '%$search_text%' OR t1.name LIKE '%$search_text%' OR t1.mobile_no LIKE '%$search_text%'");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

}