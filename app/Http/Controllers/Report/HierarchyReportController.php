<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class HierarchyReportController extends Controller
{
    private $access_key = 'ActivityReportController';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function summary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $emp_id = $this->currentUser->employee()->id;
            $country_id = $this->currentUser->employee()->cont_id;
            $userSaleGroups = DB::select("SELECT
  t1.id,
  t1.name
FROM tbld_sales_group AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
WHERE t2.emp_id =$emp_id and t1.country_id= $country_id ORDER BY t1.id ASC ");
            $saleGroups = SalesGroup::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $userDepartment = DB::select("SELECT
  t1.id,
  t1.name
FROM tbld_department AS t1 INNER JOIN tbld_department_emp_mapping AS t2 ON t1.id = t2.department_id
WHERE t2.emp_id =$emp_id and t1.country_id= $country_id ORDER BY t1.id ASC ");

            $department = Department::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $emp = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('report.activity.summary')->with("emps", $emp)->with("userDepartments", $userDepartment)->with("departments", $department)->with("userSaleGroups", $userSaleGroups)->with("saleGroups", $saleGroups)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function hierarchyReportEmployee(Request $request, $id)
    {

        $country_id = $this->currentUser->employee()->cont_id;
        $where = "1 and t1.country_id=$country_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.date between '$request->start_date' and '$request->end_date'";
        }
        if ($request->emp_id != "") {
            $where .= " AND t1.emp_id = $request->emp_id";
        }
        if ($request->department_id != "") {
            $where .= " AND t5.department_id=$request->department_id";
        }
        if ($request->sales_group_id != "") {
            $where .= " AND t6.sales_group_id=$request->sales_group_id";
        }

        if ($this->userMenu->wsmu_vsbl) {
            $data = DB::select("SELECT
  t3.email                                             AS user_name,
  t1.emp_id,
  t2.name,
  t1.date,
  t9.att_count                                   AS att_count,
  t7.note_count                               AS note_count,
  t8.loc_count                               AS loc_count,
  DATE_FORMAT(min(t1.date_time), '%d/%m/%Y : %l.%i%p') AS start_time,
  DATE_FORMAT(max(t1.date_time), '%d/%m/%Y : %l.%i%p') AS end_time,
  t4.name                                              AS role_id
FROM tblt_attendance AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN users AS t3 ON t2.user_id = t3.id
  INNER JOIN tbld_role AS t4 ON t2.role_id = t4.id
  LEFT JOIN tbld_department_emp_mapping AS t5 ON t1.emp_id = t5.emp_id
  LEFT JOIN tbld_sales_group_employee AS t6 ON t1.emp_id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) as t7 ON t1.emp_id=t7.emp_id and t1.date=t7.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) as t8 ON t1.emp_id=t8.emp_id and t1.date=t8.date
              LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as att_count,
               t1.date
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) as t9 ON t1.emp_id=t9.emp_id and t1.date=t9.date
             WHERE $where
GROUP BY t1.emp_id, t1.date, t2.id, t2.name, t3.email, t4.name,t7.note_count,t8.loc_count,t9.att_count
ORDER BY t1.date ASC");
            return view('report.attendance.summary_details')->with("attendances", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function summaryLocAttenNote(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $attendances = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t1.image,
  t3.name as role_name,
  t4.name as att_type
FROM tblt_attendance AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_role as t3 ON t2.role_id=t3.id
  INNER JOIN tbld_attendance_type as t4 ON t1.attendance_type_id=t4.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");

            $notes = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  t1.title,
  t1.note,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t4.name as note_type
FROM tblt_note AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  LEFT JOIN tblt_note_image_mapping as t3 ON t1.id=t3.note_id
  INNER JOIN tbld_note_type as t4 ON t1.note_type_id=t4.id
WHERE t1.emp_id=$id and t1.date='$date'
GROUP BY t1.id,t1.title,t1.note,t1.emp_id,t2.name,t1.date,t1.lat,t1.lon,t1.date_time,t4.name
ORDER BY t1.date ASC");
            $locations = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.times_stamp, '%l.%i%p') AS time,
  t1.lat,
  t1.lon
FROM tblt_location_history AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");

            return view('report.activity.summary_map_all')->with("attendances", $attendances)->with("notes", $notes)->with("locations", $locations)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function filterActivitySymmary(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $where = "1 and t1.country_id=$country_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.date between '$request->start_date' and '$request->end_date'";
        }
        if ($request->emp_id != "") {
            $where .= " AND t1.emp_id = $request->emp_id";
        }
        if ($request->department_id != "") {
            $where .= " AND t5.department_id=$request->department_id";
        }
        if ($request->sales_group_id != "") {
            $where .= " AND t6.sales_group_id=$request->sales_group_id";
        }
        $data = DB::select("SELECT
  t3.email                                             AS user_name,
  t1.emp_id,
  t2.name,
  t1.date,
  t9.att_count                                   AS att_count,
  t7.note_count                               AS note_count,
  t8.loc_count                               AS loc_count,
  DATE_FORMAT(min(t1.date_time), '%d/%m/%Y : %l.%i%p') AS start_time,
  DATE_FORMAT(max(t1.date_time), '%d/%m/%Y : %l.%i%p') AS end_time,
  t4.name                                              AS role_id
FROM tblt_attendance AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN users AS t3 ON t2.user_id = t3.id
  INNER JOIN tbld_role AS t4 ON t2.role_id = t4.id
  LEFT JOIN tbld_department_emp_mapping AS t5 ON t1.emp_id = t5.emp_id
  LEFT JOIN tbld_sales_group_employee AS t6 ON t1.emp_id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) as t7 ON t1.emp_id=t7.emp_id and t1.date=t7.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) as t8 ON t1.emp_id=t8.emp_id and t1.date=t8.date
              LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) as att_count,
               t1.date
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) as t9 ON t1.emp_id=t9.emp_id and t1.date=t9.date
             WHERE $where
GROUP BY t1.emp_id, t1.date, t2.id, t2.name, t3.email, t4.name,t7.note_count,t8.loc_count,t9.att_count
ORDER BY min(t1.date_time) ASC");

        return $data;

    }

}