<?php

namespace App\Http\Controllers\Setting;

use App\BusinessObject\AttendanceProcess;
use App\BusinessObject\HRPolicy;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class AttendanceProcessController extends Controller
{
    private $access_key = 'AttendanceProcessController';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu != null) {
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
            $policys = HRPolicy::where(['status_id' => 1, 'cont_id' => $this->currentUser->employee()->cont_id])->get();
            return view('Setting.AttendanceProcess.attendance_process')->with('permission', $this->userMenu)->with('policys', $policys);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create(Request $request1)
    {
        if ($this->userMenu->wsmu_crat) {

            /*       DB::beginTransaction();
                   try {*/

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
  INNER JOIN tbld_policy AS t2 ON t1.date BETWEEN '$request1->start_date' AND '$request1->end_date'
  INNER JOIN tbld_policy_employee_mapping AS t3 ON t2.id = t3.policy_id
  LEFT JOIN (SELECT
               t1.date,
               t1.emp_id,
               min(t1.date_time) AS start_time,
               max(t1.date_time) AS end_time
             FROM tblt_attendance AS t1
               INNER JOIN tbld_policy_employee_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_policy AS t3 ON t2.policy_id = t3.id
             WHERE t3.id = $request1->policy_id
             GROUP BY t1.date, t1.emp_id) AS t4 ON t1.date = t4.date AND t3.emp_id = t4.emp_id
WHERE t2.id = $request1->policy_id");
            foreach ($data1 as $request) {
                $attendanceProcess = AttendanceProcess::where(['emp_id' => $request->emp_id, 'date' => $request->date])->first();
                $prosess_status_id = $attendanceProcess->processCheck($request->start_time, $request->emp_id, $request->policy_id);
                if ($attendanceProcess == null) {
                    $attendanceProcess = new AttendanceProcess();
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
                    $attendanceProcess->country_id = $this->currentUser->employee()->cont_id;
                    $attendanceProcess->created_by = $this->currentUser->employee()->id;
                    $attendanceProcess->updated_by = $this->currentUser->employee()->id;
                    $attendanceProcess->updated_count = 0;
                    $attendanceProcess->save();
                } else {
                    if ($request->start_time != null) {
                        $prosess_status_id = $attendanceProcess->processCheck($request->start_time, $request->emp_id, $request->policy_id);
                    }
                    if ($prosess_status_id == 2 || $prosess_status_id == 1) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                    }
                    $attendanceProcess->late_min = 0;
                    if ($prosess_status_id == 3) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                        $attendanceProcess->late_min = $attendanceProcess->getLateMin($request->start_time, $request->emp_id, $request1->policy_id);
                    }
                }
            }
            /* DB::commit();
             return redirect()->back()->with('success', 'Successfully Approved');
         } catch
         (\Exception $e) {
             DB::rollback();
             return $e;
         }*/
            return redirect()->back()->with('success', 'Successfully Genareted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function attendanceProcess(Request $request1)
    {
        if ($this->userMenu->wsmu_crat) {

            /*       DB::beginTransaction();
                   try {*/

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
  INNER JOIN tbld_policy AS t2 ON t1.date BETWEEN '$request1->start_date' AND '$request1->end_date'
  INNER JOIN tbld_policy_employee_mapping AS t3 ON t2.id = t3.policy_id
  LEFT JOIN (SELECT
               t1.date,
               t1.emp_id,
               min(t1.date_time) AS start_time,
               max(t1.date_time) AS end_time
             FROM tblt_attendance AS t1
               INNER JOIN tbld_policy_employee_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_policy AS t3 ON t2.policy_id = t3.id
             WHERE t3.id = $request1->policy_id
             GROUP BY t1.date, t1.emp_id) AS t4 ON t1.date = t4.date AND t3.emp_id = t4.emp_id
WHERE t2.id = $request1->policy_id");
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
                    $attendanceProcess->country_id = $this->currentUser->employee()->cont_id;
                    $attendanceProcess->created_by = $this->currentUser->employee()->id;
                    $attendanceProcess->updated_by = $this->currentUser->employee()->id;
                    $attendanceProcess->updated_count = 0;
                    $attendanceProcess->save();
                } else {
                    if ($request->start_time != null) {
                        $prosess_status_id = $attendanceProcess->processCheck($request->start_time, $request->emp_id, $request->policy_id);
                    }
                    if ($prosess_status_id == 2 || $prosess_status_id == 1) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                    }
                    $attendanceProcess->late_min = 0;
                    if ($prosess_status_id == 3) {
                        $attendanceProcess->prosess_status_id = $prosess_status_id;
                        $attendanceProcess->late_min = $attendanceProcess->getLateMin($request->start_time, $request->emp_id, $request1->policy_id);
                    }
                    $attendanceProcess->updated_by = $this->currentUser->employee()->id;
                    $attendanceProcess->updated_count = $attendanceProcess->updated_count + 1;
                    $attendanceProcess->save();
                }
            }
            /* DB::commit();
             return redirect()->back()->with('success', 'Successfully Approved');
         } catch
         (\Exception $e) {
             DB::rollback();
             return $e;
         }*/
            return redirect()->back()->with('success', 'Successfully Genareted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {

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

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {

    }

}
