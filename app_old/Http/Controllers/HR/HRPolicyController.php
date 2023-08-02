<?php

namespace App\Http\Controllers\HR;

use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\HRPolicy;
use App\BusinessObject\HRPolicyEmployee;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class HRPolicyController extends Controller
{
    private $access_key = 'tbld_policy';
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
            $hrPolicy = HRPolicy::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();;
            return view('hr.policy.index')->with("hrPolicy", $hrPolicy)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('hr.policy.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $hrPolicy = new HRPolicy();
        $hrPolicy->name = $request->name;
        $hrPolicy->code = $request->code;
        $hrPolicy->off_day = $request->off_day;
        $hrPolicy->start_time = $request->start_time;
        $hrPolicy->half_time = $request->half_time;
        $hrPolicy->half_end = $request->half_end;
        $hrPolicy->end_time = $request->end_time;
        $hrPolicy->country_id = $this->currentUser->employee()->cont_id;
        $hrPolicy->status_id = 1;
        $hrPolicy->created_by = $this->currentUser->employee()->id;
        $hrPolicy->updated_by = $this->currentUser->employee()->id;
        $hrPolicy->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $policy = HRPolicy::findorfail($id);
            return view('hr.policy.show')->with('policy', $policy);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $policy = HRPolicy::findorfail($id);
            return view('hr.policy.edit')->with('policy', $policy);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $hrPolicy = HRPolicy::findorfail($id);
        $hrPolicy->name = $request->name;
        $hrPolicy->name = $request->code;
        $hrPolicy->off_day = $request->off_day;
        $hrPolicy->start_time = $request->start_time;
        $hrPolicy->half_time = $request->half_time;
        $hrPolicy->half_end = $request->half_end;
        $hrPolicy->end_time = $request->end_time;
        $hrPolicy->country_id = $this->currentUser->employee()->cont_id;
        $hrPolicy->code = $request->code;
        $hrPolicy->updated_by = $this->currentUser->employee()->id;
        $hrPolicy->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $hrPolicy = HRPolicy::findorfail($id);
            $hrPolicy->status_id = $hrPolicy->status_id == 1 ? 2 : 1;
            $hrPolicy->updated_by = $this->currentUser->employee()->id;
            $hrPolicy->save();
            return redirect('/hr-policy');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function empEdit($id)
    {
        if ($this->userMenu->wsmu_read) {
            $policy = HRPolicy::findorfail($id);
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $hrPolicyEmployees = HRPolicyEmployee::where('policy_id', '=', $id)->get();
            return view('hr.policy.policy_employee')->with("employees", $employees)->with("hrPolicyEmployees", $hrPolicyEmployees)->with("policy", $policy)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empDelete($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $hrPolicyEmployee = HRPolicyEmployee::findorfail($id);
            $hrPolicyEmployee->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empAdd(Request $request, $id)
    {
        //  dd($request);
        if ($this->userMenu->wsmu_updt) {
            $hrPolicyEmployee = HRPolicyEmployee::where(['emp_id' => $request->emp_id])->first();
            if ($hrPolicyEmployee == null) {
                $hrPolicyEmployee = new HRPolicyEmployee();
                $hrPolicyEmployee->emp_id = $request->emp_id;
                $hrPolicyEmployee->policy_id = $id;
                $hrPolicyEmployee->country_id = $this->currentUser->employee()->cont_id;
                $hrPolicyEmployee->created_by = $this->currentUser->employee()->id;
                $hrPolicyEmployee->updated_by = $this->currentUser->employee()->id;
                $hrPolicyEmployee->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist Policy name:'.$hrPolicyEmployee->hrPolicy()->name);
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function hrEmployeeUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            return Excel::download(new HRPolicyEmployee(), 'hr_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function hrPolicyEmpUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new HRPolicyEmployee(), $request->file('import_file'));
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
