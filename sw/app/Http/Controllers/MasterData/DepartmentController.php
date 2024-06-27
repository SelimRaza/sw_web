<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\Department;
use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SalesGroupSku;
use App\MasterData\Employee;
use App\MasterData\SKU;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    private $access_key = 'tbld_department';
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
            $departments = Department::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.department.index')->with('departments', $departments)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.department.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $department = new Department();
        $department->name = $request->name;
        $department->code = $request->code;
        $department->country_id = $this->currentUser->employee()->cont_id;
        $department->created_by = $this->currentUser->employee()->id;
        $department->updated_by = $this->currentUser->employee()->id;
        $department->status_id = 1;
        $department->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $department = Department::findorfail($id);
            return view('master_data.department.show')->with('department', $department);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $department = Department::findorfail($id);
            return view('master_data.department.edit')->with('department', $department);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $department = Department::findorfail($id);
        $department->name = $request->name;
        $department->code = $request->code;
        $department->updated_by = $this->currentUser->employee()->id;
        $department->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $department = Department::findorfail($id);
            $department->status_id = $department->status_id == 1 ? 2 : 1;
            $department->updated_by = $this->currentUser->employee()->id;
            $department->save();
            return redirect('/department');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    public
    function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $department = Department::findorfail($id);
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $departmentEmployees= DepartmentEmployee::where('department_id', '=', $id)->get();
            return view('master_data.department.department_employee')->with('permission', $this->userMenu)->with("employees", $employees)->with("departmentEmployees", $departmentEmployees)->with("department", $department);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $departmentEmployee = DepartmentEmployee::findorfail($id);
            $departmentEmployee->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $departmentEmployee = DepartmentEmployee::where(['department_id' => $id, 'emp_id' => $request->emp_id])->first();
            if ($departmentEmployee == null) {
                $departmentEmployee = new DepartmentEmployee();
                $departmentEmployee->emp_id = $request->emp_id;
                $departmentEmployee->department_id = $id;
                $departmentEmployee->country_id = $this->currentUser->employee()->cont_id;
                $departmentEmployee->created_by = $this->currentUser->employee()->id;
                $departmentEmployee->updated_by = $this->currentUser->employee()->id;
                $departmentEmployee->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function deptEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new DepartmentEmployee(), 'dept_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function deptEmployeeMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::beginTransaction();
                try {
                    Excel::import(new DepartmentEmployee(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

}
