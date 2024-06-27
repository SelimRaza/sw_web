<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\Note;
use App\BusinessObject\NoteComment;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\SalesGroupEmployee;
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function dashBoardData(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $employee = DB::select("SELECT
  t1.emp_id                        AS emp_code,
  t1.user_count                    AS totalSr,
  t1.is_present                    AS presentSr,  
  t1.is_attended                   as attendedSr,
  t1.is_leave_iom                  as leave_iomSr,
  t1.user_count - t1.is_present    AS absentSr,
  format(IFNULL((t1.productive_outlet / t1.total_site) * 100, 0),2) AS strikeRate,
  t1.is_productive                 AS productiveSr,
  t1.is_attended+t1.is_present - t1.is_productive AS nonProductiveSr,
  t1.total_memo                    AS productiveMemo,
  t1.total_visited- t1.productive_outlet       AS nonProductiveMemo,
  t1.total_site                    AS totalScheduleCall,
  t1.total_visited                 AS total_visited,
  round(t1.time_spent/60,2)        AS time_spent,
  t1.note_count                    AS note_count,
  t1.productive_outlet             AS productive_outlet,
  t1.loc_count                     AS loc_count,
  t1.number_item_line              AS number_item_line,
  0                                AS lineParCall,
  t1.total_target                  AS totalTargetAmount,
  0                                AS totalMspTargetCtn,
  t1.total_order                   AS totalOrderAmount,
  0                                AS totalMspOrderCtn,
  0                                AS blockOrder,
  0                                AS blockOrderAmount,
  0                                AS supervisorBudgetAmount,
  0                                AS supervisorBudgetAvail,
  0                                AS creditBlockOrder,
  0                                AS creditBlockAmount,
  0                                AS overDueBlockOrder,
  0                                AS overDueBlockAmount,
  0                                AS specialBlockOrder,
  0                                AS specialBlockAmount,
  t1.mtd_total_sales               AS mtd_total_sales,
  t1.mtd_total_delivery            AS mtd_total_delivery,
  CONVERT_TZ(t1.updated_at,'+00:00','+06:00')                   AS updated_at
FROM tblt_dashboard_data AS t1
WHERE t1.emp_id = $emp_id AND t1.date = '$date'");
        return Array("receive_data" => $employee, "action" => $emp_id);
    }

    public function dashBoardDataUser(Request $request)
    {
        $emp_id = $request->emp_id;
        $date = $request->date;
        $employee = DB::select("
select * FROM (
SELECT
  t2.user_name                                            AS sr_id,
  t2.mobile                                               AS sr_mobile,
  t2.name                                                 AS sr_name,
  ''                                                      AS sr_region,
  if(t1.total_order > 0, t1.total_order, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target, 0)             AS total_target,
  if(t1.start_time > 0, t1.start_time, 0)                 AS start_time,
  if(t1.end_time > 0, t1.end_time, 0)                 AS end_time,
   if(t1.is_productive > 0, t1.is_productive, 0) AS is_productive,
  t1.emp_id                                               AS emp_code,
  t2.role_id,
  t5.name                                                 AS role_name,
  t1.emp_id                                                   AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery, 0) AS last_year_mtd_total_delivery,
  t1.note_count                                           AS note_count,
  t2.master_role_id
FROM tblt_dashboard_data AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_role AS t5 ON t2.role_id = t5.id
  LEFT JOIN tblt_dashboard_data AS t6
    ON t1.emp_id = t6.emp_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblt_dashboard_data AS t7
    ON t1.emp_id = t7.emp_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
WHERE t1.manager_id = $emp_id AND (t2.status_id = 1 OR t1.mtd_total_delivery > 0) AND t1.date = '$date'
UNION ALL 
SELECT
  t2.user_name                                            AS sr_id,
  t2.mobile                                               AS sr_mobile,
  t2.name                                                 AS sr_name,
  ''                                                      AS sr_region,
  if(t1.total_order > 0, t1.total_order, 0)               AS order_amount,
  if(t1.mtd_total_sales > 0, t1.mtd_total_sales, 0)       AS mtd_total_sales,
  if(t1.mtd_total_delivery > 0, t1.mtd_total_delivery, 0) AS mtd_total_delivery,
  if(t1.total_target > 0, t1.total_target, 0)             AS total_target,
  if(t1.start_time > 0, t1.start_time, 0)                 AS start_time,
  if(t1.end_time > 0, t1.end_time, 0)                 AS end_time,
   if(t1.is_productive > 0, t1.is_productive, 0) AS is_productive,
  t1.emp_id                                               AS emp_code,
  t2.role_id,
  t5.name                                                 AS role_name,
  t1.emp_id                                                   AS emp_id,
  if(t6.mtd_total_delivery > 0, t6.mtd_total_delivery, 0) AS last_mtd_total_delivery,
  if(t7.mtd_total_delivery > 0, t7.mtd_total_delivery, 0) AS last_year_mtd_total_delivery,
  t1.note_count                                           AS note_count,
  t2.master_role_id
FROM tblt_dashboard_data AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_role AS t5 ON t2.role_id = t5.id
  LEFT JOIN tblt_dashboard_data AS t6
    ON t1.emp_id = t6.emp_id AND t6.date = DATE_SUB('$date', INTERVAL 1 MONTH)
  LEFT JOIN tblt_dashboard_data AS t7
    ON t1.emp_id = t7.emp_id AND t7.date = DATE_SUB('$date', INTERVAL 12 MONTH)
    INNER JOIN tblt_dashboard_permission as t8 ON t1.emp_id=t8.pr_emp_id
WHERE  t8.emp_id= $emp_id and  (t2.status_id = 1 OR t1.mtd_total_delivery > 0) AND t1.date = '$date'
) as t1 
ORDER BY t1.sr_id
");
        return Array("receive_data" => $employee, "action" => $emp_id);
    }


}
