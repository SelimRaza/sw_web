<?php

namespace App\Http\Controllers\Setting;

use App\Mail\Test;
use App\MasterData\Auto;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class ManualEmailController extends Controller
{
    private $access_key = 'tbld_setting_panel';
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

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $employees = Employee::where(['auto_email' => 4,'cont_id' => $this->currentUser->employee()->cont_id])->get();
            return view('Setting.ManualEmail.index')->with('permission', $this->userMenu)->with('employees', $employees);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $generateActivityReports = DB::select("SELECT t1.id,t1.user_name,t1.name,t1.address,t1.email_cc FROM tbld_employee AS t1
 WHERE t1.auto_email = 4 and t1.status_id=1");
            foreach ($generateActivityReports as $generateActivityReport) {
                $lineManager_id = $generateActivityReport->id;
                $to_mail = $generateActivityReport->address;
                $cc_mail = $generateActivityReport->email_cc;
                $employee_name = $generateActivityReport->name;
                $user_name = $generateActivityReport->user_name;
                $generateActivityReport1 = DB::select("SELECT
  t1.id,
  t1.name,
  curdate()           AS date,
  t2.name             AS role,
  t3.note_count,
  t4.loc_count,
  t5.att_count,
  t5.in_time,
  t5.out_time,
  t6.email            AS user_name,
  t1.mobile,
  t6.outlet_count     AS outlet_count,
  t7.visited_count    AS visited_count,
  t7.time_spend       AS time_spend,
  t7.checkin_count    AS checkin_count,
  t7.productive_count AS productive_count,
  t8.status_name AS status_name
FROM tbld_employee AS t1
  INNER JOIN tbld_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) AS t3 ON t1.id = t3.emp_id AND t3.date = curdate()
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) AS t4 ON t1.id = t4.emp_id AND t4.date = curdate()
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id)                          AS att_count,
               t1.date,
               DATE_FORMAT(min(t1.date_time), '%l.%i%p') AS in_time,
               DATE_FORMAT(max(t1.date_time), '%l.%i%p') AS out_time
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) AS t5 ON t1.id = t5.emp_id AND t5.date = curdate()
  INNER JOIN users AS t6 ON t1.user_id = t6.id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t3.site_id) AS outlet_count
             FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
               INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
             WHERE t1.day = dayname(curdate())
             GROUP BY t1.emp_id) AS t6 ON t1.id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.site_id)       AS visited_count,
               sum(t1.spend_time) / 60 AS time_spend,
               sum(t1.visit_count)        checkin_count,
               sum(t1.is_productive)      productive_count
             FROM tblt_site_visited AS t1
             WHERE t1.date = curdate()
             GROUP BY t1.emp_id, t1.date) AS t7 ON t1.id = t7.emp_id
LEFT JOIN (SELECT
               t1.emp_id,
               t2.name       AS status_name
             FROM tblt_attendance_process AS t1
             INNER JOIN tbld_process_satus as t2 ON t1.prosess_status_id=t2.id
             WHERE t1.date = curdate()
             GROUP BY t1.emp_id, t2.name) AS t8 ON t1.id = t8.emp_id
WHERE t1.line_manager_id = $lineManager_id AND t1.status_id = 1");

                $emailTextLine = "";
                foreach ($generateActivityReport1 as $generateActivityReport12) {
                    $emailTextLine .= "<tr>
            <td class='service'>$generateActivityReport12->user_name - $generateActivityReport12->name-$generateActivityReport12->mobile</td>
            <td class='desc'>$generateActivityReport12->role</td>
            <td class='desc'>$generateActivityReport12->in_time</td>
            <td class='desc'>$generateActivityReport12->out_time</td>
            <td class='unit'>$generateActivityReport12->att_count</td>     
            <td class='desc'>$generateActivityReport12->status_name</td>
            <td class='qty'>$generateActivityReport12->note_count</td>
            <td class='qty'>$generateActivityReport12->loc_count</td>
            <td class='unit'>$generateActivityReport12->outlet_count</td>
            <td class='unit'>$generateActivityReport12->visited_count</td>
            <td class='unit'>$generateActivityReport12->time_spend</td>

            <td class='total'>
            <a target='_blank' href='http://mdr.sihirfms.com/attendance/summaryDetails/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Attendance</i>     </a>
            <a target='_blank' href='http://mdr.sihirfms.com/note/summaryDetails/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Note</i>     </a>
            <a target='_blank' href='http://mdr.sihirfms.com/attendance/summaryLocationAll/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Map</i>     </a></td>
        </tr>";
                }
                if (count($generateActivityReport1) > 0) {
                    $emailText = "<table border='1' bordercolor='#d3d3d3' cellpadding='0' cellspacing='0'>
        <thead>
        <tr>
            <th class='service'>Employee </th>
            <th class='service'>Role </th>
            <th class='desc'>In Time</th>
            <th class='desc'>Out Time</th>            
            <th>Att</th>            
            <th>Att Status</th> 
            <th>Note</th>
            <th>Location</th>
            <th>Outlet</th>
            <th>Visited Outlet</th>
            <th>Time Spend(min)</th>                     
            <th>Link</th>
        </tr>
        </thead>
        <tbody>
        $emailTextLine
        </tbody>
    </table>";
                    $auto = new Auto();
                    $auto->emp_id = $lineManager_id;
                    $auto->to_mail = $to_mail;
                    if ($cc_mail != '') {
                        $auto->cc_mail = $cc_mail . ',mis84@mis.prangroup.com,c7f4b922.prangroup.com@apac.teams.ms';
                    } else {
                        $auto->cc_mail = 'mis84@mis.prangroup.com,c7f4b922.prangroup.com@apac.teams.ms';
                    }

                    $auto->title = 'Live Activity Report - ' .$employee_name.' ('.$user_name.') (' . date("Y-m-d") . ")";
                    $auto->text = $emailText;
                    $auto->is_send = 3;
                    $auto->save();
                }

            }
            return redirect()->back()->with('success',  'Email Generated successfully');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->name;
        $role->code = $request->code;
        $role->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {

            $role = Role::findorfail($id);


            return view('Setting.ManualEmail.show')->with('role', $role);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $employee_name="";
            $user_name="";
            $generateActivityReports = DB::select("SELECT t1.id,t1.user_name,t1.name,t1.address,t1.email_cc FROM tbld_employee AS t1
 WHERE t1.auto_email = 4 and t1.status_id=1 and t1.id=$id");
            foreach ($generateActivityReports as $generateActivityReport) {
                $lineManager_id = $generateActivityReport->id;
                $to_mail = $generateActivityReport->address;
                $cc_mail = $generateActivityReport->email_cc;
                $employee_name = $generateActivityReport->name;
                $user_name = $generateActivityReport->user_name;
                $generateActivityReport1 = DB::select("SELECT
  t1.id,
  t1.name,
  curdate()           AS date,
  t2.name             AS role,
  t3.note_count,
  t4.loc_count,
  t5.att_count,
  t5.in_time,
  t5.out_time,
  t6.email            AS user_name,
  t1.mobile,
  t6.outlet_count     AS outlet_count,
  t7.visited_count    AS visited_count,
  t7.time_spend       AS time_spend,
  t7.checkin_count    AS checkin_count,
  t7.productive_count AS productive_count,
  t8.status_name AS status_name
FROM tbld_employee AS t1
  INNER JOIN tbld_role AS t2 ON t1.role_id = t2.id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS note_count,
               t1.date
             FROM tblt_note AS t1
             GROUP BY t1.emp_id, t1.date) AS t3 ON t1.id = t3.emp_id AND t3.date = curdate()
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id) AS loc_count,
               t1.date
             FROM tblt_location_history AS t1
             GROUP BY t1.emp_id, t1.date) AS t4 ON t1.id = t4.emp_id AND t4.date = curdate()
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.emp_id)                          AS att_count,
               t1.date,
               DATE_FORMAT(min(t1.date_time), '%l.%i%p') AS in_time,
               DATE_FORMAT(max(t1.date_time), '%l.%i%p') AS out_time
             FROM tblt_attendance AS t1
             GROUP BY t1.emp_id, t1.date) AS t5 ON t1.id = t5.emp_id AND t5.date = curdate()
  INNER JOIN users AS t6 ON t1.user_id = t6.id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t3.site_id) AS outlet_count
             FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
               INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
             WHERE t1.day = dayname(curdate())
             GROUP BY t1.emp_id) AS t6 ON t1.id = t6.emp_id
  LEFT JOIN (SELECT
               t1.emp_id,
               count(t1.site_id)       AS visited_count,
               sum(t1.spend_time) / 60 AS time_spend,
               sum(t1.visit_count)        checkin_count,
               sum(t1.is_productive)      productive_count
             FROM tblt_site_visited AS t1
             WHERE t1.date = curdate()
             GROUP BY t1.emp_id, t1.date) AS t7 ON t1.id = t7.emp_id
 LEFT JOIN (SELECT
               t1.emp_id,
               t2.name       AS status_name
             FROM tblt_attendance_process AS t1
             INNER JOIN tbld_process_satus as t2 ON t1.prosess_status_id=t2.id
             WHERE t1.date = curdate()
             GROUP BY t1.emp_id, t2.name) AS t8 ON t1.id = t8.emp_id
WHERE t1.line_manager_id = $lineManager_id AND t1.status_id = 1");

                $emailTextLine = "";
                foreach ($generateActivityReport1 as $generateActivityReport12) {
                    $emailTextLine .= "<tr>
            <td class='service'>$generateActivityReport12->user_name - $generateActivityReport12->name-$generateActivityReport12->mobile</td>
            <td class='desc'>$generateActivityReport12->role</td>
            <td class='desc'>$generateActivityReport12->in_time</td>
            <td class='desc'>$generateActivityReport12->out_time</td>
            <td class='unit'>$generateActivityReport12->att_count</td>                   
            <td class='desc'>$generateActivityReport12->status_name</td>
            <td class='qty'>$generateActivityReport12->note_count</td>
            <td class='qty'>$generateActivityReport12->loc_count</td>
            <td class='unit'>$generateActivityReport12->outlet_count</td>
            <td class='unit'>$generateActivityReport12->visited_count</td>
            <td class='unit'>$generateActivityReport12->time_spend</td>
        
            <td class='total'>
            <a target='_blank' href='http://mdr.sihirfms.com/attendance/summaryDetails/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Attendance</i>     </a>
            <a target='_blank' href='http://mdr.sihirfms.com/note/summaryDetails/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Note</i>     </a>
            <a target='_blank' href='http://mdr.sihirfms.com/attendance/summaryLocationAll/$generateActivityReport12->id/$generateActivityReport12->date'><i class='fa fa-pencil'>Map</i>     </a></td>
        </tr>";
                }
                if (count($generateActivityReport1) > 0) {
                    $emailText = "<table border='1' bordercolor='#d3d3d3' cellpadding='0' cellspacing='0'>
        <thead>
        <tr>
            <th class='service'>Employee </th>
            <th class='service'>Role </th>
            <th class='desc'>In Time</th>
            <th class='desc'>Out Time</th>            
            <th>Att</th>            
            <th>Att Status</th>          
            <th>Note</th>
            <th>Location</th>
            <th>Outlet</th>
            <th>Visited Outlet</th>
            <th>Time Spend(min)</th>
            <th>Link</th>
        </tr>
        </thead>
        <tbody>
        $emailTextLine
        </tbody>
    </table>";
                    $auto = new Auto();
                    $auto->emp_id = $lineManager_id;
                    $auto->to_mail = $to_mail;
                    if ($cc_mail != '') {
                        $auto->cc_mail = $cc_mail . ',mis84@mis.prangroup.com,c7f4b922.prangroup.com@apac.teams.ms';
                    } else {
                        $auto->cc_mail = 'mis84@mis.prangroup.com,c7f4b922.prangroup.com@apac.teams.ms';
                    }

                    $auto->title = 'Live Activity Report - ' .$employee_name.' ('.$user_name.') (' . date("Y-m-d") . ")";
                    $auto->text = $emailText;
                    $auto->is_send = 3;
                    $auto->save();
                }

            }
            return redirect()->back()->with('success',  $employee_name.' ('.$user_name.') Email Generated successfully');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {

    }

}
