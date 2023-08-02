<?php

namespace App\Http\Controllers\HR;

use App\BusinessObject\AttendanceProcess;
use App\BusinessObject\Holiday;
use App\BusinessObject\HolidayPolicyMapping;
use App\BusinessObject\HolidayType;
use App\BusinessObject\HRPolicy;
use App\MasterData\Category;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    private $access_key = 'tbld_holiday';
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
            $holidays = Holiday::where('country_id', '=', $this->currentUser->employee()->cont_id)->orderBy('from_date', 'DESC')->get();
            return view('master_data.holiday.index')->with('holidays', $holidays)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $holidayTypes = HolidayType::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.holiday.create')->with('holidayTypes', $holidayTypes);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $holiday = new Holiday();
        $holiday->from_date = $request->from_date;
        $holiday->to_date = $request->to_date;
        $holiday->holiday_reason = $request->reason;
        $holiday->type_id = $request->type_id;
        $holiday->status_id = 1;
        $holiday->country_id = $this->currentUser->employee()->cont_id;
        $holiday->created_by = $this->currentUser->employee()->id;
        $holiday->updated_by = $this->currentUser->employee()->id;
        $holiday->updated_count = 0;
        $holiday->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $holiday = Holiday::findorfail($id);
            //$leave = Leave::findorfail($id);
            // if ($leave->status_id == 1) {
            DB::beginTransaction();
            try {
                $holiday->status_id = 5;
                $holiday->updated_by = $this->currentUser->employee()->id;
                $holiday->updated_count = $holiday->updated_count + 1;
                $holiday->save();
                DB::table('tblt_attendance_process')
                    ->where('holiday_id', $id)
                    ->update(['prosess_status_id' => 1, 'leave_id' => 0, 'leave_reason' => '', 'holiday_id' => 0, 'holiday_reason' => '', 'iom_id' => 0, 'iom_reason' => '', 'late_min' => 0, 'updated_by' => $this->currentUser->employee()->id]);
                $data1 = DB::select("SELECT
  t1.date,
  t2.holiday_reason,
  t2.id AS holiday_id,
  t4.emp_id,
  t5.id AS policy_id,
  6     AS process_id,
  0     AS late_min,
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
  INNER JOIN tblt_holiday AS t2 ON t1.date BETWEEN t2.from_date AND t2.to_date
  INNER JOIN tblt_holiday_policy_mapping AS t3 ON t2.id = t3.holiday_id
  INNER JOIN tbld_policy_employee_mapping as t4 ON t3.policy_id =t4.policy_id
  INNER JOIN tbld_policy AS t5 ON t4.policy_id = t5.id
WHERE  t2.status_id = 5 AND t2.id = $id");
                foreach ($data1 as $request) {
                    $attendanceProcess = AttendanceProcess::where(['emp_id' => $request->emp_id, 'date' => $request->date])->first();
                    if ($attendanceProcess == null) {
                        $attendanceProcess = new AttendanceProcess();
                        $attendanceProcess->date = $request->date;
                        $attendanceProcess->emp_id = $request->emp_id;
                        $attendanceProcess->policy_id = $request->policy_id;
                        $attendanceProcess->prosess_status_id = $request->process_id;
                        $attendanceProcess->leave_id = 0;
                        $attendanceProcess->leave_reason = '';
                        $attendanceProcess->holiday_id = $request->holiday_id;
                        $attendanceProcess->holiday_reason = $request->holiday_reason;
                        $attendanceProcess->iom_id = 0;
                        $attendanceProcess->iom_reason = '';
                        $attendanceProcess->late_min = 0;
                        $attendanceProcess->country_id = $request->country_id;
                        $attendanceProcess->created_by = $this->currentUser->employee()->id;
                        $attendanceProcess->updated_by = $this->currentUser->employee()->id;
                        $attendanceProcess->updated_count = 0;
                        $attendanceProcess->save();
                    } else {
                        $attendanceProcess->policy_id = $request->policy_id;
                        $attendanceProcess->prosess_status_id = $request->process_id;
                        $attendanceProcess->leave_id = 0;
                        $attendanceProcess->leave_reason = '';
                        $attendanceProcess->holiday_id = $request->holiday_id;
                        $attendanceProcess->holiday_reason = $request->holiday_reason;
                        $attendanceProcess->iom_id = 0;
                        $attendanceProcess->iom_reason = '';
                        $attendanceProcess->late_min = 0;
                        $attendanceProcess->updated_by = $this->currentUser->employee()->id;
                        $attendanceProcess->updated_count = $attendanceProcess->updated_count + 1;
                        $attendanceProcess->save();
                    }

                }
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Approved');
            } catch
            (\Exception $e) {
                DB::rollback();
                return $e;
            }


            // }


            return redirect('/holiday');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public
    function policyMapping($id)
    {
        if ($this->userMenu->wsmu_read) {
            $holiday = Holiday::findorfail($id);
            $policys = HRPolicy::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $holidayPolicyMapping = HolidayPolicyMapping::where('holiday_id', '=', $id)->get();
            return view('master_data.holiday.holiday_policy_mapping')->with("holiday", $holiday)->with("holidayPolicyMapping", $holidayPolicyMapping)->with("policys", $policys)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function policyDelete($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $holidayPolicyMapping = HolidayPolicyMapping::findorfail($id);
            $holidayPolicyMapping->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function policyAdd(Request $request, $id)
    {
        //  dd($request);
        if ($this->userMenu->wsmu_updt) {
            $hrPolicyEmployee = HolidayPolicyMapping::where(['holiday_id' => $id, 'policy_id' => $request->policy_id])->first();
            if ($hrPolicyEmployee == null) {
                $hrPolicyEmployee = new HolidayPolicyMapping();
                $hrPolicyEmployee->policy_id = $request->policy_id;
                $hrPolicyEmployee->holiday_id = $id;
                $hrPolicyEmployee->country_id = $this->currentUser->employee()->cont_id;
                $hrPolicyEmployee->created_by = $this->currentUser->employee()->id;
                $hrPolicyEmployee->updated_by = $this->currentUser->employee()->id;
                $hrPolicyEmployee->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function policyUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            return Excel::download(new HolidayPolicyMapping(), 'holiday_policy_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function policyUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new HolidayPolicyMapping(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

}
