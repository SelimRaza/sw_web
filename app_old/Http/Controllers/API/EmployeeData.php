<?php

namespace App\Http\Controllers\API;


use App\BusinessObject\Attendance;
use App\BusinessObject\AttendanceProcess;
use App\BusinessObject\HRPolicyEmployee;
use App\BusinessObject\InventoryCount;

use App\BusinessObject\IOMData;
use App\BusinessObject\Leave;
use App\BusinessObject\LeaveImage;
use App\BusinessObject\OrderMaster;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function saveLeave(Request $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $leave = new Leave();
            $leave->emp_id = $request->emp_id;
            $leave->from_date = $request->from_date;
            $leave->to_date = $request->to_date;
            $leave->day_count = $request->day_count;
            $leave->leave_type_id = $request->leave_type;
            $leave->is_half_day = $request->is_half_day;
            $leave->start_time = $request->start_time;
            $leave->end_time = $request->end_time;
            $leave->reason = $request->reason;
            $leave->address = $request->address;
            $leave->status_id = 1;
            $leave->country_id = $request->country_id;
            $leave->created_by = $request->up_emp_id;
            $leave->updated_by = $request->up_emp_id;
            $leave->save();

            $imageLines = json_decode($request->line_data);
            foreach ($imageLines as $imageLine) {
                $leaveImage = new LeaveImage();
                $leaveImage->leave_id = $leave->id;
                $leaveImage->image_name = $imageLine->image_name;
                $leaveImage->country_id = $request->country_id;
                $leaveImage->save();
            }
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        // return array('column_id' => $leaveImage);
    }

    public function saveIOM(Request $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $iomData = new IOMData();
            $iomData->emp_id = $request->emp_id;
            $iomData->from_date = $request->from_date;
            $iomData->to_date = $request->to_date;
            $iomData->day_count = $request->day_count;
            $iomData->start_time = $request->start_time;
            $iomData->end_time = $request->end_time;
            $iomData->reason = $request->reason;
            $iomData->status_id = 1;
            $iomData->country_id = $request->country_id;
            $iomData->created_by = $request->up_emp_id;
            $iomData->updated_by = $request->up_emp_id;
            $iomData->save();

            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        // return array('column_id' => $leaveImage);
    }

    public function attendance(Request $request)
    {
        if ($request->country_id != 0) {
            DB::beginTransaction();
            try {

                $attendance = new Attendance();
                $attendance->emp_id = $request->emp_id;
                $attendance->attendance_type_id = $request->attendance_type;
                $attendance->date = date('Y-m-d', strtotime($request->date_time));
                $attendance->date_time = $request->date_time;
                $attendance->lat = $request->lat;
                $attendance->lon = $request->lon;
                $attendance->distance = $request->distance;
                $attendance->image = $request->image;
                $attendance->country_id = $request->country_id;
                $attendance->status_id = 1;
                $attendance->created_by = $request->up_emp_id;
                $attendance->updated_by = $request->up_emp_id;
                $attendance->save();
                $hrPolicyEmployee = HRPolicyEmployee::where(['emp_id' => $request->emp_id])->first();
                if ($hrPolicyEmployee != null) {
                    $attendanceProcess = AttendanceProcess::where(['emp_id' => $request->emp_id, 'date' => date('Y-m-d', strtotime($request->date_time))])->first();
                    if ($attendanceProcess == null) {
                        $attendanceProcess = new AttendanceProcess();
                        $attendanceProcess->date = date('Y-m-d', strtotime($request->date_time));
                        $attendanceProcess->emp_id = $request->emp_id;
                        $attendanceProcess->policy_id = $hrPolicyEmployee->policy_id;
                        $attendanceProcess->prosess_status_id = 1;
                        $attendanceProcess->leave_id = 0;
                        $attendanceProcess->leave_reason = '';
                        $attendanceProcess->iom_id = 0;
                        $attendanceProcess->iom_reason = '';
                        $attendanceProcess->holiday_id = 0;
                        $attendanceProcess->holiday_reason = '';
                        $attendanceProcess->late_min = 0;
                        $attendanceProcess->country_id = $request->country_id;
                        $attendanceProcess->created_by = $request->up_emp_id;
                        $attendanceProcess->updated_by = $request->up_emp_id;
                        $attendanceProcess->updated_count = 0;
                        $attendanceProcess->save();
                    } else {
                        if ($attendanceProcess->prosess_status_id == 1) {
                            $prosess_status_id = $attendanceProcess->processCheck($request->date_time, $request->emp_id, $hrPolicyEmployee->policy_id);
                            $attendanceProcess->prosess_status_id = $prosess_status_id;
                            $attendanceProcess->late_min = 0;
                            if ($prosess_status_id == 3) {
                                $attendanceProcess->late_min = $attendanceProcess->getLateMin($request->date_time, $request->emp_id, $hrPolicyEmployee->policy_id);
                            }
                            $attendanceProcess->updated_by = $request->up_emp_id;
                            $attendanceProcess->updated_count = $attendanceProcess->updated_count + 1;
                            $attendanceProcess->save();
                        }

                    }
                }
                DB::commit();
                return array('column_id' => $request->id);
            } catch
            (\Exception $e) {
                DB::rollback();
                return $e;
            }
        } else {
            return array('column_id' => "");
        }
    }

    public function attendanceData(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t1.id, t1.name) AS column_id,
  concat(t1.id, t1.name) AS token,
  t1.id                           AS type_id,
  t1.name                         AS name,
  t1.code                         AS code
FROM tbld_attendance_type  t1
WHERE t1.status_id = 1 AND t1.country_id = $request->country_id");
        $data2 = DB::select("SELECT
  concat(t1.id, t1.name) AS column_id,
  concat(t1.id, t1.name) AS token,
  t1.id                           AS type_id,
  t1.name                         AS name,
  t1.code                         AS code
FROM tbld_leave_type  t1
WHERE t1.status_id = 1 AND t1.country_id = $request->country_id");

        return Array(
            "tbld_attendance_type" => array("data" => $data1, "action" => $request->country_id),
            "tbld_leave_type" => array("data" => $data2, "action" => $request->country_id)
        );
    }

    public function employeeAttendanceold(Request $request)

    {
        $dataRow = DB::select("SELECT
  t1.date,
  t2.emp_id,
  min(t2.date_time) AS start_time,
  max(t2.date_time) AS end_time,
  t4.name           AS status,
  t5.prosess_status_id,
  t6.name as processStatus,
  t5.leave_reason as leave_reason,
  t5.iom_reason as iom_reason
FROM
  (SELECT adddate('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
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
  LEFT JOIN tblt_attendance AS t2 ON t1.date = t2.date AND t2.emp_id = $request->emp_id
  LEFT JOIN tblt_holiday AS t3 ON t1.date = t3.date AND t1.date BETWEEN '$request->start_date' AND '$request->end_date' and t3.country_id=$request->country_id and t3.status_id=1
  LEFT JOIN tbld_holiday_type AS t4 ON t3.type_id = t4.id
  LEFT JOIN tblt_attendance_process as t5 ON t1.date=t5.date and t5.emp_id=$request->emp_id
  LEFT JOIN tbld_process_satus as t6 ON t5.prosess_status_id=t6.id
WHERE t1.date BETWEEN '$request->start_date'and '$request->end_date' 
GROUP BY t1.date, t2.emp_id,t4.name,t5.prosess_status_id,t6.name,t5.leave_reason,t5.iom_reason");
        return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
    }

    public function employeeAttendance(Request $request)

    {
        $dataRow = DB::select("SELECT
  t1.date,
  t2.emp_id,
  min(t2.date_time) AS start_time,
  max(t2.date_time) AS end_time,
  t4.off_day        AS off_day,
  t5.prosess_status_id,
  t6.name as processStatus,
  t5.leave_reason as leave_reason,
  t5.iom_reason as iom_reason,
  t5.holiday_reason
FROM
  (SELECT DateAdd('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
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
  LEFT JOIN tblt_attendance AS t2 ON t1.date = t2.date AND t2.emp_id = $request->emp_id
  LEFT JOIN tbld_policy_employee_mapping as t3 ON t3.emp_id=$request->emp_id
  LEFT JOIN tbld_policy as t4 ON t3.policy_id=t4.id
  LEFT JOIN tblt_attendance_process as t5 ON t1.date=t5.date and t5.emp_id=$request->emp_id
  LEFT JOIN tbld_process_satus as t6 ON t5.prosess_status_id=t6.id
WHERE t1.date BETWEEN '$request->start_date'and '$request->end_date' 
GROUP BY t1.date, t2.emp_id,t5.prosess_status_id,t6.name,t5.leave_reason,t5.iom_reason,t4.off_day,t5.holiday_reason
ORDER BY t1.date DESC");
        return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
    }


    public function attendanceHistory(Request $request)
    {
        $dataRow = DB::select("SELECT
  t1.date,
  t2.emp_id,
  min(t2.date_time) AS start_time,
  max(t2.date_time) AS end_time,
  t4.name           AS status
FROM
  (SELECT adddate('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
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
  LEFT JOIN tblt_attendance AS t2 ON t1.date = t2.date AND t2.emp_id = $request->emp_id
  LEFT JOIN tblt_holiday AS t3 ON t1.date = t3.date AND t1.date BETWEEN '$request->start_date' AND '$request->end_date' and t3.status_id=1
  LEFT JOIN tbld_holiday_type AS t4 ON t3.type_id = t4.id
WHERE t1.date BETWEEN '$request->start_date'and '$request->end_date' 
GROUP BY t1.date, t2.emp_id,t4.name");
        return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
    }

    public function myPolicy(Request $request)
    {
        $dataRow = DB::select("sel");
        return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
    }

    public function employeeList(Request $request)
    {
        $country_id = $request->input('country_id');
        $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS emp_id,
  t1.user_name AS user_name,
  t1.name AS emp_name
FROM tbld_employee AS t1 
INNER JOIN tbld_department_emp_mapping as t2 ON t1.id=t2.emp_id WHERE t1.status_id=1 and t1.country_id=$country_id and t2.department_id not in(38,40,41,42,43,44,45)");
        return Array("tbld_all_employee" => array("data" => $tst, "action" => $request->country_id));
    }

    public function employeeListHigh(Request $request)
    {
        $country_id = $request->input('country_id');
   /*     $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS emp_id,
   t1.user_name AS user_name,
  t1.name AS emp_name
FROM tbld_employee AS t1 
INNER JOIN tbld_department_emp_mapping as t2 ON t1.id=t2.emp_id WHERE t1.status_id=1  and t2.department_id in(38,40,41,42,43,44,45)
GROUP BY t1.id,t1.name,t1.user_name,t1.status_id
");*/
        $tst=array();
        return Array("tbld_all_employee" => array("data" => $tst, "action" => $request->country_id));
    }


    public function employee(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.id, t2.email, t1.role_id, t3.id, t1.mobile, t1.name, t1.profile_image,
         if(group_concat(t6.name) != NULL, group_concat(t6.name), ''),t1.address,t3.user_name,t1.manager_id,t1.ln_name ) AS column_id,
  concat(t1.id, t2.email, t1.role_id, t3.id, t1.mobile, t1.name, t1.profile_image,
         if(group_concat(t6.name) != NULL, group_concat(t6.name), ''),t1.address,t3.user_name,t1.manager_id,t1.ln_name ) AS token,
  t1.id                                                                         AS emp_id,
  concat(t1.name, '(', t1.ln_name, ')')                                         AS emp_name,
  t2.email                                                                      AS user_name,
  t1.role_id,
  ''                                                                            AS role_name,
  t1.manager_id                                                                 AS manager_code,
  t1.id                                                                         AS emp_code,
  t3.name                                                                       AS manager_name,
  t3.id                                                                         AS manager_emp_id,
  t3.user_name                                                                  AS manager_user_name,
  t1.mobile,
  t1.address                                                                    AS email_address,
  t3.mobile                                                                     AS manager_mobile,
  t3.address                                                                    AS manager_email_address,
  0                                                                         AS company_id,
  ''                                                                       AS company_name,
  if(group_concat(t6.name) != NULL, group_concat(t6.name), '')                  AS group_name,
  t1.profile_image
FROM tbld_employee AS t1
  INNER JOIN users AS t2 ON t1.user_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t1.manager_id = t3.id AND t1.country_id = t3.country_id
  LEFT JOIN tbld_sales_group_employee AS t5 ON t1.id = t5.emp_id
  LEFT JOIN tbld_sales_group AS t6 ON t5.sales_group_id = t6.id
WHERE (t3.id = $request->emp_id OR t1.id = $request->emp_id) AND t1.status_id = 1
GROUP BY t1.id, t2.email, t1.role_id, t3.id, t1.name, t1.ln_name, t1.manager_id, t3.name, t1.mobile, t1.address,
  t3.mobile,
  t3.address,  t3.user_name, t1.profile_image");
        return Array("tbld_employee" => array("data" => $tst, "action" => $request->country_id));
    }

    public function employeeLeave(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.id,
  t1.from_date,
  t1.to_date,
  t1.day_count,
  t1.is_half_day,
  t1.start_time,
  t1.end_time,
  t1.leave_type_id,
  t2.name AS leave_type,
  t1.reason,
  t1.address,
  t1.status_id,
  t3.name AS status
FROM tblt_leave AS t1 INNER JOIN tbld_leave_type AS t2 ON t1.leave_type_id = t2.id
  INNER JOIN tbld_lifecycle_status AS t3 ON t1.status_id = t3.id
WHERE t1.emp_id = $request->emp_id");

        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->emp_id),
        );
    }

    public function employeeIOM(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.id,
  t1.from_date,
  t1.to_date,
  t1.day_count,
  t1.start_time,
  t1.end_time,
  t1.reason,
  t1.status_id,
  t2.name AS status
FROM tblt_iom AS t1 
  INNER JOIN tbld_lifecycle_status AS t2 ON t1.status_id = t2.id
WHERE t1.emp_id = $request->emp_id");
        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->emp_id),
        );
    }

    public function employeeRoutePlan(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.route_id AS route_id,
  t2.code     AS route_code,
  t1.Day      AS day_name,
  t2.name     AS route_name
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
WHERE t1.emp_id = $request->emp_id");
        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->emp_id),
        );
    }

    public function employeeRoutePlanSite(Request $request)
    {
        $data1 = DB::select("SELECT
  t4.id               AS site_id,
  t4.name             AS site_name,
  t4.Owner_Name          owner_name,
  ''                  AS payment,
  t4.is_verified      AS is_verified,
  !isnull(t5.site_id) AS visited
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tblt_site_visited AS t5 ON t4.id = t5.site_id AND t5.date = curdate()
WHERE t1.route_id = $request->route_id AND t4.status_id = 1
GROUP BY t4.id,t4.name,t4.Owner_Name,t4.Owner_Name,t4.is_verified,t5.site_id");
        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->emp_id),
        );
    }

    public function employeeOrderList(Request $request)
    {
        $data1 = DB::select("SELECT
  t1.order_id                                    AS order_id,
  t1.order_amount                                AS order_amount,
  concat(t3.id, '-', t3.name, '(', t3.code, ')') AS site_name,
  t1.order_date                                  AS order_date,
  t1.status_id                                   AS status_id,
  t4.name                                        AS status,
  0                                              AS invoice_amount,
  t1.order_type_id                               AS type_id
FROM tblt_order_master AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
WHERE t1.emp_id = $request->emp_id AND t1.order_date BETWEEN '$request->start_date' AND '$request->end_date'
GROUP BY t1.order_id, t1.order_amount, t3.id, t3.name, t3.code, t1.order_date, t1.status_id, t4.name, t1.order_type_id");
        return Array(
            "receive_data" => array("data" => $data1, "action" => $request->emp_id),
        );
    }


    public function employeeProductList(Request $request)
    {
        $tst = DB::select("SELECT
  t5.sku_id              AS product_id,
  t6.name                AS product_name,
  t6.ctn_size            AS ctn_size,
  sum(t5.qty_order)      AS order_qty,
  sum(t5.qty_delivery)   AS delivery_qty,
  0                      AS discount,
  sum(t5.total_order)    AS total_amount,
  0                     AS target_amount,
  0                      AS target_qty,
  sum(t5.invoice_amount) AS net_amount,
  t1.order_type_id       AS type_id,
  t7.name                AS sub_category
FROM tblt_order_master AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
  INNER JOIN tblt_order_line AS t5 ON t1.id = t5.so_id
  INNER JOIN tbld_sku AS t6 ON t5.sku_id = t6.id
  INNER JOIN tbld_sub_category AS t7 ON t6.sub_category_id = t7.id
WHERE
  t1.emp_id = $request->emp_id AND t1.order_date BETWEEN '$request->start_date' AND '$request->end_date' AND
  t1.order_type_id = 1
  GROUP BY t5.sku_id,t6.name,t6.ctn_size,t7.name,t1.order_type_id
UNION ALL
SELECT
  t5.sku_id              AS product_id,
  t6.name                AS product_name,
  t6.ctn_size            AS ctn_size,
  sum(t5.qty_order)      AS order_qty,
  sum(t5.qty_delivery)   AS delivery_qty,
  0                      AS discount,
  sum(t5.total_order)    AS total_amount, 
  0                     AS target_amount,
  0                      AS target_qty,
  sum(t5.invoice_amount) AS net_amount,
  t1.order_type_id       AS type_id,
  t7.name                AS sub_category
FROM tblt_order_master AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
  INNER JOIN tblt_order_line AS t5 ON t1.id = t5.so_id
  INNER JOIN tbld_sku AS t6 ON t5.sku_id = t6.id
  INNER JOIN tbld_sub_category AS t7 ON t6.sub_category_id = t7.id
WHERE
   t1.emp_id = $request->emp_id AND t1.order_date BETWEEN '$request->start_date' AND '$request->end_date' AND
  t1.order_type_id = 2
GROUP BY t5.sku_id,t6.name,t6.ctn_size,t7.name,t1.order_type_id");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function employeeOrderPrint(Request $request)
    {
        $order_master = collect(\DB::select("SELECT
  t1.company_id         AS ou_id,
  0           AS trip_id,
  t1.depot_id       AS depo_id,
  t2.user_name         AS sr_id,
  t2.name          AS sr_name,
  t1.site_id     AS site_id,
  t3.outlet_id     AS outlet_id,
  t3.name     AS site_name,
  t1.status_id  AS status_id,
  t4.name          AS status,
  t3.lat           AS lat,
  t3.lon           AS lon,
  t1.Order_ID      AS order_id,
  ''  AS collection_type,
  t1.order_date          AS order_date,
  t1.delivery_date AS delivery_date,
  t3.VAT_TRN       AS site_trn,
  ''    AS vat_number,
  0 as invoice_amount
FROM tblt_order_master AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
WHERE t1.order_id='$request->order_id'"))->first();

        $data1 = DB::select("SELECT
  t1.order_id                                    AS order_id,
  t1.order_amount                                AS order_amount,
  concat(t3.id, '-', t3.name, '(', t3.code, ')') AS site_name,
  t1.order_date                                  AS order_date,
  t1.status_id                                   AS status_id,
  t4.name                                        AS status,
  0                                              AS invoice_amount,
  t1.order_type_id                               AS type_id
FROM tblt_order_master AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
WHERE t1.emp_id = $request->emp_id AND t1.order_date BETWEEN '$request->start_date' AND '$request->end_date'
GROUP BY t1.order_id, t1.order_amount, t3.id, t3.name, t3.code, t1.order_date, t1.status_id, t4.name, t1.order_type_id");

        $orderLineBluetoothPrint = $this->SalesOrderModel->orderLineBluetoothPrintReport($this->post('order_id'));
        $order_data = array(
            'ou_id' => $order_master->ou_id,
            'trip_id' => $order_master->trip_id,
            'depo_id' => $order_master->depo_id,
            'sr_id' => $order_master->sr_id,
            'sr_name' => $order_master->sr_name,
            'site_id' => $order_master->site_id,
            'outlet_id' => $order_master->outlet_id,
            'site_name' => $order_master->site_name,
            'order_id' => $order_master->order_id,
            'collection_type' => $order_master->collection_type,
            'order_date' => $order_master->order_date,
            'delivery_date' => $order_master->delivery_date,
            'site_trn' => $order_master->site_trn,
            'vat_number' => $order_master->vat_number,
            'invoice_amount' => $order_master->invoice_amount,
            'orderLine' => $orderLineBluetoothPrint,
        );

        return Array(
            "receive_data" => array("data" => $order_data, "action" => $request->emp_id),
        );
    }

    public function employeeReportByDate(Request $request)
    {
        $todaysRoute = collect(\DB::select("SELECT
  count(t1.site_id)  AS site_count,
  max(t1.route_name) AS route_name
FROM (
       SELECT
         t2.route_id AS site_id,
         t3.name     AS route_name
       FROM tbld_pjp AS t1
         INNER JOIN tbld_route_site_mapping AS t2 ON t1.route_id = t2.route_id
         INNER JOIN tbld_route AS t3 ON t1.route_id = t3.id
         INNER JOIN tbld_site as t4 ON t2.site_id=t4.id
       WHERE t1.emp_id =$request->emp_id and t4.is_verified=1 AND t1.Day = dayname(curdate())
       UNION ALL
       SELECT
         t2.site_id AS site_id,
         ''         AS route_name
       FROM tbld_pjp AS t1
         INNER JOIN tblt_site_visit_permission AS t2 ON t1.route_id = t2.route_id         
         INNER JOIN tbld_site as t3 ON t2.site_id=t3.id
       WHERE t2.emp_id = $request->emp_id and t3.is_verified=1 AND t1.Day != dayname(curdate()) AND t2.date = curdate()
       GROUP BY t2.site_id) AS t1"))->first();

        $todaysRouteOrder = DB::select("SELECT t1.site_id AS site_id
FROM tblt_site_visited AS t1
WHERE t1.emp_id = $request->emp_id AND t1.date = curdate() and t1.is_productive=1 ");

        $todaysRouteNotOrder = DB::select("SELECT t1.site_id AS site_id
FROM tblt_site_visited AS t1
WHERE t1.emp_id = $request->emp_id AND t1.date = curdate() and t1.is_productive!=1");
        $totalNumberLine = DB::select("SELECT
  t3.sku_id AS line_count,
  t3.total_order as order_amount
FROM tblt_site_visited AS t1
  INNER JOIN tblt_order_master AS t2 ON t1.site_id = t2.site_id AND t1.date = t2.order_date
  INNER JOIN tblt_order_line AS t3 ON t2.id = t3.so_id
WHERE t1.emp_id = $request->emp_id AND t1.date = curdate() and t2.order_type_id=1");


        $data_responce = array(
            "route_name" => $todaysRoute->route_name,
            "site_count" => $todaysRoute->site_count,
            "memo_count" => count($todaysRouteOrder),
            "not_order" => count($todaysRouteNotOrder),
            "pending" => $todaysRoute->site_count - count($todaysRouteNotOrder) - count($todaysRouteOrder),
            "strikeRate" => number_format((($todaysRoute->site_count > 0) ? count($todaysRouteOrder) / $todaysRoute->site_count : 0) * 100, 2),
            "totalLine" => number_format((count($totalNumberLine) > 0) ? count($totalNumberLine) : 0, 0),
            "lineParCall" => number_format((count($todaysRouteOrder) > 0) ? count($totalNumberLine) / count($todaysRouteOrder) : 0, 2),
            "todaysTarget" => 1,
            "todaysOrder" => array_sum(array_column($totalNumberLine, 'order_amount')),
            "totalTarget" => 1,
            "totalAchive" => array_sum(array_column($totalNumberLine, 'order_amount')),
        );

        return Array("receive_data" => [$data_responce], "action" => $request->emp_id);

    }

    public function srTodayReport_post()
    {
        $sr_id = $this->post('sr_id');
        $todaysRoute = $this->ReportModel->srTodaysRoute($sr_id);
        $todaysRouteOrder = $this->ReportModel->srTodaysRouteOrder($sr_id);
        $todaysRouteNotOrder = $this->ReportModel->srTodaysRouteNotOrder($sr_id);
        $totalNumberLine = $this->ReportModel->totalNumberLine($sr_id);
        $target_amount = $this->ReportModel->srTargetAmountNew($sr_id);
        $totalAmount = $this->ReportModel->totalOrderAmount($sr_id);
        $data_responce = array(
            "route_name" => $todaysRoute->route_name,
            "site_count" => $todaysRoute->site_count,
            "memo_count" => count($todaysRouteOrder),
            "not_order" => count($todaysRouteNotOrder),
            "pending" => $todaysRoute->site_count - count($todaysRouteNotOrder) - count($todaysRouteOrder),
            "strikeRate" => number_format((($todaysRoute->site_count > 0) ? count($todaysRouteOrder) / $todaysRoute->site_count : 0) * 100, 2),
            "totalLine" => number_format((count($totalNumberLine) > 0) ? count($totalNumberLine) : 0, 0),
            "lineParCall" => number_format((count($todaysRouteOrder) > 0) ? count($totalNumberLine) / count($todaysRouteOrder) : 0, 2),

            "todaysTarget" => $target_amount->target_amount > 0 ? $target_amount->target_amount / 26 : 1,
            "todaysOrder" => $totalAmount->totalAmount > 0 ? $totalAmount->totalAmount : 0,
            "totalTarget" => $target_amount->target_amount > 0 ? $target_amount->target_amount : 1,
            "totalAchive" => $target_amount->achive > 0 ? $target_amount->achive : 0,
        );
        //var_dump($target_amount);
        //  var_dump($data_responce);
        $this->set_response(Array("receive_data" => [$data_responce], "action" => $this->post('sr_id')), REST_Controller::HTTP_OK);
    }


}