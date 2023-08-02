<?php

namespace App\Process;

use App\BusinessObject\AttendanceProcess;
use App\BusinessObject\HRPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class TrackingDataProcess
{


    public function OJODataTransfer($db)
    {
        $curl = curl_init();

        $result = DB::select("SELECT `user_name` AS staffid,`lat`,lon as `lng`,date(date_time) as date,`date_time` as tracktime FROM `tblt_user_tracking_details` WHERE DATE(date_time)=curdate() ;");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://103.206.184.53:8081/api/v1/locations",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>json_encode(array("data" => $t)) ,//"{\"data\":[{\"staffid\": 163214,\"lat\":\"23.2343\",\"lng\":\"90.2343\",\"date\":\"2020-05-10\",\"tracktime\":\"2019-12-11 12:24:31\"}]}",
                 CURLOPT_HTTPHEADER => array(
                    "content-type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $dataRes=array("response" => $response);
            DB::table('tblh_sync_log')->insertOrIgnore(
                $dataRes
            );
        }
        curl_close($curl);

    }
    public function rgTrackingData($date)
    {
        $plog = array();
        $plog[]  = array(
            'name'=>'track1',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        $result = DB::connection('rfl_live')->select("SELECT
  t1.`SR_ID`                            AS user_name,
  t2.Name                               AS name,
  t6.Region_Name 						AS region_name,
  t7.Zone_Name 							AS zone_name,
  t2.Base_Name 							AS base_name,
  t4.Group_Name                         AS group_name,
  t5.Company_Short_Name                 AS com_name,
  t2.Mobile                             AS mobile_no,
  t3.designation                        AS role_name,
  t1.`Status`                           AS type_name,
  SUBSTRING_INDEX(t1.Location, ',', 1)  AS lat,
  SUBSTRING_INDEX(t1.Location, ',', -1) AS lon,
  t1.Date_Time                          AS date_time,
  CONCAT('A', t1.ID)                    AS uid,
  'rfl'                                AS bu
FROM `attendence_table` t1 INNER JOIN srinfo t2 ON t1.SR_ID = t2.SR_ID
  INNER JOIN designation_master t3 ON t2.designation = t3.id
  INNER JOIN groupmaster t4 ON t2.Group_Id = t4.Group_ID 
  INNER JOIN company_master t5 ON t4.company_id = t5.Company_ID
  INNER JOIN region_master t6 ON t2.Region_ID = t6.Region_Code
  INNER JOIN zone_master t7 ON t2.Zone_Id = t7.Zone_ID
WHERE t1.`Date` = '$date' AND t1.Location != '' AND t6.Company_id='2222' AND t7.Company_id='2222'
GROUP BY t1.SR_ID");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking_details')->insertOrIgnore(
                $t
            );
        }
        $result = DB::connection('rfl_live')->select("SELECT
  t1.`SR_ID`                            AS user_name,
  t2.Name                               AS name,
  t6.Region_Name 						AS region_name,
  t7.Zone_Name 							AS zone_name,
  t2.Base_Name 							AS base_name,
  t4.Group_Name                         AS group_name,
  t5.Company_Short_Name                 AS com_name,
  t2.Mobile                             AS mobile_no,
  t3.designation                        AS role_name,
  'Order'                               AS type_name,
  SUBSTRING_INDEX(t1.Location, ',', 1)  AS lat,
  SUBSTRING_INDEX(t1.Location, ',', -1) AS lon,
  Order_time                            AS date_time,
  CONCAT('O', t1.ID)                    AS uid,
  'rfl'                                 AS bu
FROM `order_table` t1 INNER JOIN srinfo t2 ON t1.SR_ID = t2.SR_ID
  INNER JOIN designation_master t3 ON t2.designation = t3.id
  INNER JOIN groupmaster t4 ON t2.Group_Id = t4.Group_ID
  INNER JOIN company_master t5 ON t4.company_id = t5.Company_ID
  INNER JOIN region_master t6 ON t2.Region_ID = t6.Region_Code
  INNER JOIN zone_master t7 ON t2.Zone_Id = t7.Zone_ID
WHERE t1.`Date` = '$date' AND t1.Location != '' AND t6.Company_id='2222' AND t7.Company_id='2222'
GROUP BY t1.SR_ID, t1.`Order_ID`, t1.`Location`");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking_details')->insertOrIgnore(
                $t
            );
        }
        $result = DB::connection('rfl_live')->select("SELECT
  t1.`sr_id`         AS user_name,
  t2.Name            AS name,
  t6.Region_Name 	 AS region_name,
  t7.Zone_Name 		 AS zone_name,
  t2.Base_Name 		 AS base_name,
  t4.Group_Name      AS group_name,
  t5.Company_Short_Name AS com_name,
  t2.Mobile          AS mobile_no,
  t3.designation     AS role_name,
  'GPS'              AS type_name,
  t1.lat             AS lat,
  t1.lon             AS lon,
  t1.times_stamp     AS date_time,
  uuid_short() AS uid,
  'rfl'              AS bu
FROM `tbl_sr_cur_location` t1 INNER JOIN srinfo t2 ON t1.sr_id = t2.SR_ID
  INNER JOIN designation_master t3 ON t2.designation = t3.id
  INNER JOIN groupmaster t4 ON t2.Group_Id = t4.Group_ID
  INNER JOIN company_master t5 ON t4.company_id = t5.Company_ID
  INNER JOIN region_master t6 ON t2.Region_ID = t6.Region_Code
  INNER JOIN zone_master t7 ON t2.Zone_Id = t7.Zone_ID
WHERE DATE_SUB(CONVERT_TZ(now(), '+00:00', '+06:00'), INTERVAL 15 MINUTE) < t1.`times_stamp` AND t6.Company_id='2222' AND t7.Company_id='2222'
GROUP BY t1.SR_ID");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking_details')->insertOrIgnore(
                $t
            );
        }
        $plog[]  = array(
            'name'=>'track2',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        DB::table('th_plog')->insert($plog);

    }
    public function pgTrackingData($date)
    {
        $plog = array();
        $plog[]  = array(
            'name'=>'track3',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        $result = DB::connection('pran_live')->select("SELECT
  t1.`SR_ID`                            AS user_name,
  t2.Name                               AS name,
  t6.Region_Name 						AS region_name,
  t7.Zone_Name 							AS zone_name,
  t2.Base_Name 							AS base_name,
  t4.Group_Name                         AS group_name,
  t5.Company_Short_Name                 AS com_name,
  t2.Mobile                             AS mobile_no,
  t3.designation                        AS role_name,
  t1.`Status`                           AS type_name,
  SUBSTRING_INDEX(t1.Location, ',', 1)  AS lat,
  SUBSTRING_INDEX(t1.Location, ',', -1) AS lon,
  t1.Date_Time                          AS date_time,
  CONCAT('A', t1.ID)                    AS uid,
  'pran'                                AS bu
FROM `attendence_table` t1 INNER JOIN srinfo t2 ON t1.SR_ID = t2.SR_ID
  INNER JOIN designation_table t3 ON t2.designation = t3.id
  INNER JOIN groupmaster t4 ON t2.Group_Id = t4.Group_ID 
  INNER JOIN company_master t5 ON t4.company_id = t5.Company_ID
  INNER JOIN region_master t6 ON t2.Region_ID = t6.Region_Code
  INNER JOIN zone_master t7 ON t2.Zone_Id = t7.Zone_ID
WHERE t1.`Date` = '$date' AND t1.Location != '' 
GROUP BY t1.SR_ID");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking_details')->insertOrIgnore(
                $t
            );
        }

        $result = DB::connection('pran_live')->select("SELECT
  t1.`sr_id`         AS user_name,
  t2.Name            AS name,
  t6.Region_Name 	 AS region_name,
  t7.Zone_Name 		 AS zone_name,
  t2.Base_Name 		 AS base_name,
  t4.Group_Name      AS group_name,
  t5.Company_Short_Name AS com_name,
  t2.Mobile          AS mobile_no,
  t3.designation     AS role_name,
  'GPS'              AS type_name,
  t1.lat             AS lat,
  t1.lon             AS lon,
  t1.times_stamp     AS date_time,
  uuid_short() AS uid,
  'pran'              AS bu
FROM `tbl_sr_cur_location` t1 INNER JOIN srinfo t2 ON t1.sr_id = t2.SR_ID
  INNER JOIN designation_table t3 ON t2.designation = t3.id
  INNER JOIN groupmaster t4 ON t2.Group_Id = t4.Group_ID
  INNER JOIN company_master t5 ON t4.company_id = t5.Company_ID
  INNER JOIN region_master t6 ON t2.Region_ID = t6.Region_Code
  INNER JOIN zone_master t7 ON t2.Zone_Id = t7.Zone_ID
WHERE DATE_SUB(CONVERT_TZ(now(), '+00:00', '+06:00'), INTERVAL 15 MINUTE) < t1.`times_stamp` 
GROUP BY t1.SR_ID");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking_details')->insertOrIgnore(
                $t
            );
        }
        $plog[]  = array(
            'name'=>'trackUpdate1',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        DB::table('th_plog')->insert($plog);
    }
    public function prgTrackingDataUpdate()
    {
        $plog = array();
        $plog[]  = array(
            'name'=>'track5',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        DB::connection('myprg_comm1')->update("UPDATE tblt_user_tracking_details AS t1
  INNER JOIN tblt_user_tracking AS t2 ON t1.user_name = t2.user_name
SET t2.lat       = t1.lat, t2.lon = t1.lon, t2.date_time = t1.date_time, t2.group_name = t1.group_name,
  t2.region_name = t1.region_name, t2.zone_name = t1.zone_name, t2.base_name = t1.base_name, t2.bu = t1.bu,t2.com_name=t1.com_name,t2.role_name=t1.role_name
WHERE t1.id IN (SELECT max(id)
                FROM tblt_user_tracking_details AS t1
                GROUP BY t1.user_name);");

        DB::connection('myprg_comm1')->update("UPDATE tblt_user_tracking as t1 inner JOIN tbld_master_role as t2 ON t1.role_name=t2.name SET t1.master_role_id=t2.id;");
        $plog[]  = array(
            'name'=>'trackUpdate2',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        DB::table('th_plog')->insert($plog);
    }

    public function prgTrackingDataMissing()
    {
        $plog = array();
        $plog[]  = array(
            'name'=>'trackMissing1',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );

        $result = DB::connection('myprg_comm1')->select("SELECT
    t1.`user_name`,
    t1.`name`,
    t1.region_name,
    t1.zone_name,
    t1.base_name,
    t1.`group_name`,
    t1.`mobile_no`,
    t1.`role_name`,
    t1.`type_name`,
    t1.`lat`,
    t1.`lon`,
    t1.`date_time`,
    t1.`bu`
  FROM tblt_user_tracking_details AS t1 
    LEFT JOIN tblt_user_tracking AS t2 ON t1.user_name = t2.user_name
  WHERE t2.user_name IS NULL
GROUP BY t1.user_name");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking')->insertOrIgnore(
                $t
            );
        }

        $result = DB::connection('myprg_comm1')->select("SELECT
  t1.aemp_usnm AS `user_name`,
  t1.aemp_name AS `name`,
  t1.role_id   AS master_role_id,
  ''           AS region_name,
  ''           AS zone_name,
  ''           AS base_name,
  ''           AS `group_name`,
  t1.aemp_mob1 AS `mobile_no`,
  t2.role_name AS `role_name`,
  'comm'       AS `type_name`,
  0            AS `lat`,
  0            AS `lon`,
  '' `date_time`,
  'comm'       AS `bu`
FROM myprg_p.tm_aemp AS t1
  INNER JOIN myprg_p.tm_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN myprg_comm.tblt_user_tracking AS t3 ON t1.aemp_usnm = t3.user_name
WHERE t1.lfcl_id = 1 AND t3.user_name IS NULL;");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 200) as $t) {
            DB::table('tblt_user_tracking')->insertOrIgnore(
                $t
            );
        }

        $plog[]  = array(
            'name'=>'trackMissing2',
            'type_name' =>'track',
            'date_time'=> date('Y-m-d H:i:s') );
        DB::table('th_plog')->insert($plog);
    }
}