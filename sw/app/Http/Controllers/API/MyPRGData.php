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

class MyPRGData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function userReportData(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.total_site,
  t1.total_visited,
  t1.total_memo,
  t1.total_order_exception,
  t1.number_item_line,
  t1.total_order,
  round(t1.number_item_line/t1.total_memo,2) as line_per_call,
   round(t1.total_site/t1.total_visited,2) as strikeRate
FROM tblt_dashboard_data_my_prg AS t1
  INNER JOIN tblt_user_tracking AS t2 ON t1.emp_code = t2.user_name
WHERE t1.emp_code = '$request->user_name' AND t1.date = curdate()");

        $data2 = DB::select("SELECT
  t1.outlet_name AS site_name,
  t1.amount AS order_amount,
  TIME_FORMAT(t1.date_time, '%h:%i:%s %p') as date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name = 'order' and t1.amount>0");
        $data3 = DB::select("SELECT
  '' AS note,
  t1.date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name = 'note'");
        $data4 = DB::select("SELECT
  '' AS other_note,
  t1.date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name in('Start Work','end Work','other Work')");
        return Array(
            "receive_data_summary" => array("data" => $data1, "action" => $request->country_id),
            "receive_data_order" => array("data" => $data2, "action" => $request->country_id),
            "receive_data_note" => array("data" => $data3, "action" => $request->country_id),
            "receive_data_other" => array("data" => $data4, "action" => $request->country_id),
        );
    }

    public function userReportDataNew(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.total_site,
  t1.total_visited,
  t1.total_memo,
  t1.total_order_exception,
  t1.number_item_line,
  t1.total_order,
  round(t1.number_item_line/t1.total_memo,2) as line_per_call,
  round(IFNULL((t1.total_memo / t1.total_site) * 100, 0),2)  as strikeRate
FROM tblt_dashboard_data_my_prg AS t1
  INNER JOIN tblt_user_tracking AS t2 ON t1.emp_code = t2.user_name
WHERE t1.emp_code = '$request->user_name' AND t1.date = '$request->date'");

        $data2 = DB::select("SELECT
  t1.outlet_name AS site_name,
  round(t1.amount,2) AS order_amount,
  TIME_FORMAT(t1.date_time, '%h:%i:%s %p') as date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name = 'order' and date(t1.date_time)='$request->date' and t1.amount>0");
        $data3 = DB::select("SELECT
  '' AS note,
  t1.date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name = 'note'");
        $data4 = DB::select("SELECT
  '' AS other_note,
  t1.date_time
FROM tblt_user_tracking_details AS t1
WHERE t1.user_name = '$request->user_name' AND t1.type_name in('Start Work','end Work','other Work')");
        return Array(
            "receive_data_summary" => array("data" => $data1, "action" => $request->country_id),
            "receive_data_order" => array("data" => $data2, "action" => $request->country_id),
            "receive_data_note" => array("data" => $data3, "action" => $request->country_id),
            "receive_data_other" => array("data" => $data4, "action" => $request->country_id),
        );
    }



    public function mapTrackAroundList(Request $request)
    {
        $whereCondition="";
        $distance = $request->distance;
        $lat = $request->lat;
        $lon = $request->lon;
        if (isset($request->soft_name)){
            $whereCondition.="and  t1.bu='$request->soft_name' ";
        }
        if (isset($request->company_name)){
            $whereCondition.="and t1.com_name='$request->company_name'";
        }
        if (isset($request->group_name)){
            $whereCondition.=" and t1.group_name ='$request->group_name' ";
        }
        if (isset($request->role_name)){
            $whereCondition.=" and t1.role_name ='$request->role_name' ";
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
         t1.master_role_id                                                          AS role_id,
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

    public function mapAroundOutlet(Request $request)
    {
        $whereCondition="";
        $distance = $request->distance;
        if(isset($request->dist_id)){
            $whereCondition.="and  t1.code!='$request->dist_id' ";
        }
        if(isset($request->soft_name)){
            $whereCondition.="and  t1.business_group='$request->soft_name' ";
        }
        if(isset($request->company_name)){
            $whereCondition.="and  t1.company='$request->company_name'";
        }
        if(isset($request->group_name)){
            $whereCondition.=" and t1.sales_group ='$request->group_name' ";
        }
        if(isset($request->dist_type)){
            $whereCondition.=" and t2.short_name ='$request->dist_type' ";
        }
        $lat = $request->lat;
        $lon = $request->lon;
       $query="SELECT *
FROM (
       SELECT
         t1.id                                               AS site_id,
         concat(t1.name, '-', t1.code)                       AS site_name,
         t1.mobile                                           AS mobile,
         t1.lat                                              AS lat,
         t1.lon                                              AS lon,
         ''                                                  AS region_name,
         ''                                                  AS zone_name,
         ''                                                  AS base_name,
         ''                                                  AS emp_name,
         ''                                                  AS sv_name,
          getDistance(t1.lat, t1.lon, $lat, $lon) AS dist_dif,
         0                                                  AS time_dif,
         1                                                   AS type,
         ''                                                  AS last_time,
         1                                                   AS is_verified,
         t1.code                                             AS user_name,
         t1.id                                               AS emp_id,
         t1.id                                               AS emp_code,
         1                                                   AS role_id,
         0                                                   AS channel_id,
         t1.business_group                                   AS bu,
         t1.company                                          AS group_name,
         t1.sales_group                                      AS sales_group,
         ''                                                  AS role_name,
         t2.name                                             AS type_name,
         t2.short_name as type_code

       FROM tbld_distributor AS t1
         INNER JOIN tbld_distributor_type AS t2 ON t1.distributor_type_id = t2.id
       WHERE t1.lat > 0 $whereCondition
       HAVING dist_dif < $distance
     ) AS t1";
        $tst = DB::select($query);
        return Array("receive_data" => array("data" => $tst, "action" => 1));
    }

    public function filterMapData(Request $request)
    {

$date=date('Y-m-d');
        $data1 = DB::select("SELECT t1.com_name,t1.com_name as com_code
FROM tblt_user_tracking AS t1
WHERE t1.com_name NOT IN ('0', '') and date(t1.date_time)='$date'
GROUP BY t1.com_name");
        $data2 = DB::select("SELECT t1.group_name , t1.group_name as group_code
FROM tblt_user_tracking AS t1
WHERE t1.group_name NOT IN ('0', '') and date(t1.date_time)='$date'
GROUP BY t1.group_name");
        $data3 = DB::select("SELECT t1.name as role_name,t1.CODE as role_code
FROM tbld_master_role AS t1");
        $data4 = DB::select("SELECT t1.short_name as dist_code,
t1.short_name as dist_name,
t1.name as full_name
FROM tbld_distributor_type AS t1");

        return Array(
            "company_list" => array("data" => $data1, "action" => $request->country_id),
            "group_list" => array("data" => $data2, "action" => $request->country_id),
            "role_list" => array("data" => $data3, "action" => $request->country_id),
            "dist_type_list" => array("data" => $data4, "action" => $request->country_id),
        );
    }

    public function filterUserMapData(Request $request)
    {
        $whereCondition1='';
        $whereCondition2='';
        if(isset($request->bu_name)){
            $whereCondition1.="and  t1.bu ='$request->bu_name' ";
            $whereCondition2.="and  t1.bu ='$request->bu_name' ";
        }
        if(isset($request->com_name)){

            $whereCondition2.="and  t1.com_name ='$request->com_name' ";
        }


        $data1 = DB::select("SELECT t1.com_name
FROM tblt_user_tracking AS t1
WHERE t1.com_name NOT IN ('0', '') $whereCondition1
GROUP BY t1.com_name");
        $data2 = DB::select("SELECT t1.group_name
FROM tblt_user_tracking AS t1
WHERE t1.group_name NOT IN ('0', '') $whereCondition2
GROUP BY t1.group_name");

        return Array(
            "company_list" => array("data" => $data1, "action" => $request->country_id),
            "group_list" => array("data" => $data2, "action" => $request->country_id),
        );
    }


    public function filterOutletMapData(Request $request)
    {
        $whereCondition1='';
        $whereCondition2='';
        if(isset($request->bu_name)){
            $whereCondition1.="and  t1.business_group ='$request->bu_name' ";
            $whereCondition2.="and  t1.business_group ='$request->bu_name' ";
        }
        if(isset($request->com_name)){

            $whereCondition2.="and  t1.company ='$request->com_name' ";
        }


        $data1 = DB::select("SELECT t1.company as com_name
FROM tbld_distributor AS t1
WHERE t1.company NOT IN ('0', '') $whereCondition1
GROUP BY t1.company");
        $data2 = DB::select("SELECT t1.sales_group as  group_name
FROM tbld_distributor AS t1
WHERE t1.sales_group NOT IN ('0', '') $whereCondition2
GROUP BY t1.sales_group");

        return Array(
            "company_list" => array("data" => $data1, "action" => $request->country_id),
            "group_list" => array("data" => $data2, "action" => $request->country_id),
        );
    }
    public function filterOutletTypeData(Request $request)
    {
             $data4 = DB::select("SELECT t1.short_name as dist_code,
t1.short_name as dist_name,
t1.name as full_name
FROM tbld_distributor_type AS t1");

        return Array(
            "dist_type_list" => array("data" => $data4, "action" => $request->country_id),
        );
    }
    public function filterRoleTypeData(Request $request)
    {
        $data3 = DB::select("SELECT t1.name as role_name
FROM tbld_master_role AS t1");

        return Array(
            "role_list" => array("data" => $data3, "action" => $request->country_id),
        );
    }


    public function mapSearchOutletList(Request $request)
    {
        $search_text = $request->search_text;

        $tst = DB::select("SELECT
         t1.id                                               AS site_id,
         concat(t1.name, '-', t1.code)                       AS site_name,
         t1.mobile                                           AS mobile,
         t1.lat                                              AS lat,
         t1.lon                                              AS lon,
         ''                                                  AS region_name,
         ''                                                  AS zone_name,
         ''                                                  AS base_name,
         ''                                                  AS emp_name,
         ''                                                  AS sv_name,
          0 AS dist_dif,
         0                                                  AS time_dif,
         1                                                   AS type,
         ''                                                  AS last_time,
         1                                                   AS is_verified,
         t1.code                                             AS user_name,
         t1.id                                               AS emp_id,
         t1.id                                               AS emp_code,
         1                                                   AS role_id,
         0                                                   AS channel_id,
         t1.business_group                                   AS bu,
         t1.company                                          AS group_name,
         t1.sales_group                                      AS sales_group,
         ''                                                  AS role_name,
         t2.name                                             AS type_name,
         t2.short_name as type_code

       FROM tbld_distributor AS t1
         INNER JOIN tbld_distributor_type AS t2 ON t1.distributor_type_id = t2.id
       WHERE  t1.name LIKE '%$search_text%' or t1.mobile LIKE '%$search_text%'or t1.code LIKE '%$search_text%'");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }


}