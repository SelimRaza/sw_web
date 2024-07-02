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

class MyActivityReportController extends Controller
{
    private $access_key = 'MyActivityReportController';
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

    public function mySummary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $emp = Employee::where(['cont_id' => $this->currentUser->employee()->cont_id, 'manager_id' => $this->currentUser->employee()->id])->get();
            return view('report.my_activity.summary')->with("emps", $emp)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function mySummaryBy(Request $request, $id, $date)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;


            $data = DB::select("SELECT
  t1.user_name                                         AS user_name,
  t1.id                                                AS emp_id,
  t1.name                                              AS name,
  t2.name                                              AS role,
  '$date' as date,
  t9.att_count                                         AS att_count,
  t7.note_count                                        AS note_count,
  t8.loc_count                                         AS loc_count,
  t10.outlet_count                                     AS outlet_count,
  t11.visited_count                                    AS visited_count,
  t11.time_spend                                       AS time_spend,
  t11.checkin_count                                    AS checkin_count,
  t11.productive_count                                 AS productive_count,
  DATE_FORMAT(min(t3.date_time), '%d/%m/%Y : %l.%i%p') AS start_time,
  DATE_FORMAT(max(t3.date_time), '%d/%m/%Y : %l.%i%p') AS end_time,
  t2.id                                                AS role_id
FROM tbld_employee AS t1 INNER JOIN tbld_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN tblt_attendance AS t3 ON t1.id = t3.emp_id AND t3.date = '$date'
  LEFT JOIN tbld_department_emp_mapping AS t5 ON t1.id = t5.emp_id
  LEFT JOIN tbld_sales_group_employee AS t6 ON t1.id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) AS t7 ON t1.id = t7.emp_id AND t3.date = t7.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) AS t8 ON t1.id = t8.emp_id AND t3.date = t8.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS att_count,
               t1.date
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) AS t9 ON t1.id = t9.emp_id AND t3.date = t9.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t3.site_id) AS outlet_count
             FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
               INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
             WHERE t1.day = dayname('$date')
             GROUP BY t1.emp_id) AS t10 ON t1.id = t10.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.site_id)       AS visited_count,
               sum(t1.spend_time) / 60 AS time_spend,
               sum(t1.visit_count)        checkin_count,
               sum(t1.is_productive)      productive_count
             FROM tblt_site_visited AS t1
             WHERE t1.date = '$date'
             GROUP BY t1.emp_id, t1.date) AS t11 ON t1.id = t11.emp_id
WHERE t1.manager_id = $id AND t1.country_id = $country_id and t1.status_id=1
GROUP BY t1.id, t1.user_name, t1.name, t2.name, t3.date, t9.att_count, t7.note_count, t8.loc_count, t2.id,t10.outlet_count,t11.visited_count,t11.visited_count,t11.time_spend,t11.checkin_count,t11.productive_count");
            $emp = Employee::where(['cont_id' => $this->currentUser->employee()->cont_id, 'manager_id' => $this->currentUser->employee()->id])->get();
            return view('report.my_activity.summary_by_user_date')->with("emps", $emp)->with("emp_dates", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function filterMyActivitySummary(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id = $this->currentUser->employee()->id;
        if ($request->emp_id!=null){
            $emp_id=$request->emp_id;
        }

        $data = DB::select("SELECT
  t1.user_name                                         AS user_name,
  t1.id                                                AS emp_id,
  t1.name                                              AS name,
  t2.name                                              AS role,
  '$request->date' as date,
  t9.att_count                                         AS att_count,
  t7.note_count                                        AS note_count,
  t8.loc_count                                         AS loc_count,
  t10.outlet_count                                     AS outlet_count,
  t11.visited_count                                    AS visited_count,
  t11.time_spend                                       AS time_spend,
  t11.checkin_count                                    AS checkin_count,
  t11.productive_count                                 AS productive_count,
  DATE_FORMAT(min(t3.date_time), '%d/%m/%Y : %l.%i%p') AS start_time,
  DATE_FORMAT(max(t3.date_time), '%d/%m/%Y : %l.%i%p') AS end_time,
  t2.id                                                AS role_id
FROM tbld_employee AS t1 INNER JOIN tbld_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN tblt_attendance AS t3 ON t1.id = t3.emp_id AND t3.date = '$request->date'
  LEFT JOIN tbld_department_emp_mapping AS t5 ON t1.id = t5.emp_id
  LEFT JOIN tbld_sales_group_employee AS t6 ON t1.id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) AS t7 ON t1.id = t7.emp_id AND t3.date = t7.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) AS t8 ON t1.id = t8.emp_id AND t3.date = t8.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS att_count,
               t1.date
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) AS t9 ON t1.id = t9.emp_id AND t3.date = t9.date
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t3.site_id) AS outlet_count
             FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
               INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
             WHERE t1.day = dayname('$request->date')
             GROUP BY t1.emp_id) AS t10 ON t1.id = t10.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.site_id)       AS visited_count,
               sum(t1.spend_time) / 60 AS time_spend,
               sum(t1.visit_count)        checkin_count,
               sum(t1.is_productive)      productive_count
             FROM tblt_site_visited AS t1
             WHERE t1.date = '$request->date'
             GROUP BY t1.emp_id, t1.date) AS t11 ON t1.id = t11.emp_id
WHERE t1.manager_id = $emp_id AND t1.country_id = $country_id and t1.status_id=1
GROUP BY t1.id, t1.user_name, t1.name, t2.name, t3.date, t9.att_count, t7.note_count, t8.loc_count, t2.id,t10.outlet_count,t11.visited_count,t11.visited_count,t11.time_spend,t11.checkin_count,t11.productive_count");

        return $data;

    }



    public function attendanceSummaryDetails(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $data = DB::select("SELECT
  t2.id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t1.image,
  t3.name as attendance_type,
  t1.lat,
  t1.lon
FROM tblt_attendance AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_attendance_type as t3 ON t1.attendance_type_id=t3.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");
            foreach ($data as $index=>$data1){
                $address="";
                $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($data1->lat) . ',' . trim($data1->lon) . '&sensor=true&key=AIzaSyBGHAvVAeNHTkWUUnGsXpbA6AK3GWbFByg');
                $output = json_decode($geocodeFromLatLong);
                $status = $output->status;
                //  $address .= ($index1+1).". ";
                $address .= ($status == "OK") ? $output->results[0]->formatted_address."" : '';
                $data[$index]->address=$address;
            }
            return view('report.attendance.summary_details')->with("attendances", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function noteSummaryDetails(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $emp = Employee::findorfail($id);
            $data = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  t1.title,
  t1.note,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  group_concat(t3.image_name) as image_name,
  t4.name as note_type
FROM tblt_note AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  LEFT JOIN tblt_note_image_mapping as t3 ON t1.id=t3.note_id
  INNER JOIN tbld_note_type as t4 ON t1.note_type_id=t4.id
WHERE t1.emp_id=$id and t1.date='$date'
GROUP BY t1.id,t1.title,t1.note,t1.emp_id,t2.name,t1.date,t1.lat,t1.lon,t1.date_time,t4.name
ORDER BY t1.date ASC");
            foreach ($data as $index=>$data1){
                $address="";
                $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($data1->lat) . ',' . trim($data1->lon) . '&sensor=true&key=AIzaSyBGHAvVAeNHTkWUUnGsXpbA6AK3GWbFByg');
                $output = json_decode($geocodeFromLatLong);
                $status = $output->status;
                //  $address .= ($index1+1).". ";
                $address .= ($status == "OK") ? $output->results[0]->formatted_address."" : '';
                $data[$index]->address=$address;
            }

            return view('report.note.summary_details')->with("notes", $data)->with("emp", $emp)->with('permission', $this->userMenu);
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

            return view('report.attendance.summary_map_all')->with("attendances", $attendances)->with("notes", $notes)->with("locations", $locations)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }
}