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
class AttendanceDataProcess
{

    public function attendanceProcess($date)
    {

        $hrPolicy = HRPolicy::all();
        foreach ($hrPolicy as $hrPolicyRow) {
            $data1 = DB::select("SELECT
  t1.date,
  t3.emp_id,
  t2.id AS policy_id,
  t4.start_time,
  t4.end_time,
  t2.country_id
FROM (
       SELECT adddate('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
       FROM
         (SELECT 0 i
          UNION SELECT 1
          UNION SELECT 2
          UNION SELECT 3
          UNION SELECT 4
          UNION SELECT 5
          UNION SELECT 6
          UNION SELECT 7
          UNION SELECT 8
          UNION SELECT 9) t0,
         (SELECT 0 i
          UNION SELECT 1
          UNION SELECT 2
          UNION SELECT 3
          UNION SELECT 4
          UNION SELECT 5
          UNION SELECT 6
          UNION SELECT 7
          UNION SELECT 8
          UNION SELECT 9) t1,
         (SELECT 0 i
          UNION SELECT 1
          UNION SELECT 2
          UNION SELECT 3
          UNION SELECT 4
          UNION SELECT 5
          UNION SELECT 6
          UNION SELECT 7
          UNION SELECT 8
          UNION SELECT 9) t2,
         (SELECT 0 i
          UNION SELECT 1
          UNION SELECT 2
          UNION SELECT 3
          UNION SELECT 4
          UNION SELECT 5
          UNION SELECT 6
          UNION SELECT 7
          UNION SELECT 8
          UNION SELECT 9) t3,
         (SELECT 0 i
          UNION SELECT 1
          UNION SELECT 2
          UNION SELECT 3
          UNION SELECT 4
          UNION SELECT 5
          UNION SELECT 6
          UNION SELECT 7
          UNION SELECT 8
          UNION SELECT 9) t4) AS t1
  INNER JOIN tbld_policy AS t2 ON t1.date ='$date'
  INNER JOIN tbld_policy_employee_mapping AS t3 ON t2.id = t3.policy_id
  LEFT JOIN (SELECT
               t1.date,
               t1.emp_id,
               min(t1.date_time) AS start_time,
               max(t1.date_time) AS end_time
             FROM tblt_attendance AS t1
               INNER JOIN tbld_policy_employee_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_policy AS t3 ON t2.policy_id = t3.id
             WHERE t3.id = $hrPolicyRow->id
             GROUP BY t1.date, t1.emp_id) AS t4 ON t1.date = t4.date AND t3.emp_id = t4.emp_id
WHERE t2.id = $hrPolicyRow->id");
            foreach ($data1 as $request) {
                $attendanceProcess = AttendanceProcess::where(['emp_id' => $request->emp_id, 'date' => $request->date])->first();
                $prosess_status_id = 1;
                if ($attendanceProcess == null) {
                    $attendanceProcess = new AttendanceProcess();
                    if ($request->start_time != null) {
                        $prosess_status_id = $attendanceProcess->processCheck($request->start_time, $request->emp_id, $request->policy_id);
                    }
                    $attendanceProcess->date = $request->date;
                    $attendanceProcess->emp_id = $request->emp_id;
                    $attendanceProcess->policy_id = $request->policy_id;
                    $attendanceProcess->prosess_status_id = $prosess_status_id;
                    $attendanceProcess->leave_id = 0;
                    $attendanceProcess->leave_reason = '';
                    $attendanceProcess->iom_id = 0;
                    $attendanceProcess->iom_reason = '';
                    $attendanceProcess->holiday_id = 0;
                    $attendanceProcess->holiday_reason = '';
                    $attendanceProcess->late_min = 0;
                    $attendanceProcess->country_id = $hrPolicyRow->country_id;
                    $attendanceProcess->created_by = 1;
                    $attendanceProcess->updated_by = 1;
                    $attendanceProcess->updated_count = 0;
                    $attendanceProcess->save();
                } else {
                    if ($request->start_time != null) {
                        $prosess_status_id = $attendanceProcess->processCheck($request->start_time, $request->emp_id, $request->policy_id);
                    }
                    if ($prosess_status_id == 2|| $prosess_status_id == 1) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                    }
                    $attendanceProcess->late_min = 0;
                    if ($prosess_status_id == 3) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                        $attendanceProcess->late_min = $attendanceProcess->getLateMin($request->start_time, $request->emp_id, $hrPolicyRow->id);
                    }
                    $attendanceProcess->updated_by = 1;
                    $attendanceProcess->updated_count = $attendanceProcess->updated_count + 1;
                    $attendanceProcess->save();
                }
            }
        }



    }


    public function attendanceDataPranDB($db_conn,$date)
    {

        $plog = array();
        $plog[] = array(
            'dhlg_name' => 'atta',
            'dhlg_code' => 'atta');
        $result = DB::connection($db_conn)->select("SELECT *
FROM (SELECT
        t2.aemp_usnm              AS sr_id,
        t1.attn_date              AS date,
        TIME(
            t1.attn_time)         AS time,
        concat(t1.geo_lat, ',',
               t1.geo_lon)        AS location,
        t1.geo_lat                AS actv_lat,
        t1.geo_lon                AS actv_lon,
        if(t1.atten_type = 1 OR t1.atten_type = 2, if(t1.atten_type = 1, 'Start Work', 'End Work'),
           t1.atten_type)         AS status
      FROM tt_attn t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
      WHERE DATE(t1.created_at) = '$date' AND t1.atten_type IN (1, 2)
      UNION ALL
      SELECT
        t2.aemp_usnm       AS sr_id,
        t1.note_date       AS date,
        TIME(t1.note_dtim) AS time,
        concat(t1.geo_lat, ',',
               t1.geo_lon) AS location,
        t1.geo_lat         AS actv_lat,
        t1.geo_lon         AS actv_lon,
        'Note'             AS status
      FROM tt_note t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
      WHERE DATE(t1.created_at) = '$date'
      UNION ALL
      SELECT
        t2.aemp_usnm       AS sr_id,
        t1.ordm_date       AS date,
        TIME(t1.ordm_time) AS time,
        concat(t1.geo_lat, ',',
               t1.geo_lon) AS location,
        t1.geo_lat         AS actv_lat,
        t1.geo_lon         AS actv_lon,
        'Order'            AS status
      FROM tt_ordm t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
      WHERE DATE(t1.created_at) = '$date') AS dddf;");
        $result = array_map(function ($value) {
            return (array)$value;
        }, $result);
        foreach (array_chunk($result, 500) as $t) {
            DB::connection('pran_live')->table('t_actv')->insertOrIgnore(
                $t
            );
        }
    }



}