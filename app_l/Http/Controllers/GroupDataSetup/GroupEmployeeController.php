<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\MasterData\Region;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\MasterData\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use Response;
class GroupEmployeeController extends Controller
{
    private $access_key = 'tbld_group_employee';
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
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $employees = DB::select("SELECT
  t2.id,
  t2.name,
  t2.mobile,
  t2.address,
  t5.name  AS manager_name,
  t6.name  AS line_manager_name,
  t7.name  AS role,
  t8.email AS user_name,
  t1.sales_group_id,
  t4.name  AS group_name,
  t2.status_id,
  t2.profile_icon
FROM tbld_sales_group_employee AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_sales_group_employee AS t3 ON t1.sales_group_id = t3.sales_group_id
  INNER JOIN tbld_sales_group AS t4 ON t1.sales_group_id = t4.id
  INNER JOIN tbld_employee AS t5 ON t2.manager_id = t5.id
  INNER JOIN tbld_employee AS t6 ON t2.line_manager_id = t6.id
  INNER JOIN tbld_role AS t7 ON t2.role_id = t7.id
  INNER JOIN users AS t8 ON t2.user_id = t8.id
WHERE t2.country_id = $country_id AND t3.emp_id = $emp_id and t2.country_id=t4.country_id
GROUP BY t2.id, t2.name, t1.sales_group_id, t2.mobile, t2.address, t5.name, t6.name, t7.name, t8.email, t4.name,t2.status_id,t2.profile_icon");
           // $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_employee.index')->with("employees", $employees)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $userRole = Role::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $divisions = Division::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $masterRoles = MasterRole::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_employee.create')->with("roles", $userRole)->with("divisions", $divisions)->with("employees", $employees)->with('masterRoles', $masterRoles);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['email']),
                'remember_token' => md5(uniqid(rand(), true)),
                'status_id' => 1,
                'cont_id' => $this->currentUser->employee()->cont_id,
            ]);
            $emp = new Employee();
            $emp->name = $request['name'];
            $emp->ln_name = $request['ln_name'] == "" ? '' : $request['ln_name'];
            $emp->mobile = $request['mobile'] == "" ? '' : $request['mobile'];
            //  $emp->ln_name = $request['ln_name'];
            //$emp->mobile = $request['mobile'];
            $emp->address = $request['address'] == "" ? '' : $request['address'];
            //   $emp->address = $request['address'];
            $emp->user_id = $user->id;
            $emp->user_name = $request['email'];
            $emp->role_id = $request['role_id'];
            $emp->master_role_id = $request['master_role_id'];
            $emp->user_token = md5(uniqid(rand(), true));
            $emp->country_id = $this->currentUser->employee()->cont_id;
            $emp->created_by = $this->currentUser->employee()->id;
            $emp->updated_by = $this->currentUser->employee()->id;
            $emp->manager_id = $request['manager_id'];
            $emp->line_manager_id = $request['line_manager_id'];
            $emp->allowed_distance = $request['allowed_distance'];
            $emp->version_code = '';
            if ($request['address'] != '') {
                $emp->auto_email = $request['auto_email'] == null ? 3 : 4;
            } else {
                $emp->auto_email = 3;
            }
            $emp->profile_image = '';
            $emp->profile_icon = '';
            if (isset($request->input_img)) {
                $imageIcon = time() . '.' . $request->input_img->getClientOriginalExtension();
                $file = Input::file('input_img');
                Image::make($file->getRealPath())->fit(128, 128)->save('uploads/image_icon/' . $imageIcon);
                $imageName = time() . '.' . $request->input_img->getClientOriginalExtension();
                $request->input_img->move('uploads/profile_image/', $imageName);
                $emp->profile_image = $imageName;
                $emp->profile_icon = $imageIcon;
            }
            $emp->location_on = $request['location_on'] == null ? 3 : 4;
            $emp->email_cc = $request['email_cc'] == "" ? '' : $request['email_cc'];
            $emp->status_id = 1;
            $emp->save();
            DB::commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            // return redirect()->back()->with('danger', 'Not Created');
        }

    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $employee = collect(DB::select("SELECT
  t1.id,
  t1.name,
  t8.email,
  t1.ln_name,
  t1.mobile,
  t1.address,
  t1.allowed_distance,
  t9.name AS role_name,
  t3.name AS manager_name,
  t4.name AS line_manager_name,
  t1.email_cc,
  t1.auto_email,
  t1.location_on
FROM tbld_employee AS t1
  INNER JOIN tbld_employee AS t3 ON t1.manager_id = t3.id
  INNER JOIN tbld_employee AS t4 ON t1.line_manager_id = t4.id
  INNER JOIN users AS t8 ON t1.user_id = t8.id
  INNER JOIN tbld_role AS t9 ON t1.role_id = t9.id
WHERE t1.id = $id"))->first();
            return view('master_data.group_employee.show')->with('employee', $employee);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $divisions = Division::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $employee_all = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $masterRoles = MasterRole::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $employee = Employee::findorfail($id);
            $userRole = Role::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_employee.edit')->with('employee', $employee)->with("divisions", $divisions)->with('employee_all', $employee_all)->with('userRoles', $userRole)->with('masterRoles', $masterRoles);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $emp = Employee::findorfail($id);
            $emp->name = $request['name'];
            $emp->ln_name = $request['ln_name'] == "" ? '' : $request['ln_name'];
            $emp->mobile = $request['mobile'] == "" ? '' : $request['mobile'];
            $emp->address = $request['address'] == "" ? '' : $request['address'];
            $emp->user_name = $request['email'];
            if ($request['address'] != '') {
                $emp->auto_email = $request['auto_email'] == null ? 3 : 4;

            } else {
                $emp->auto_email = 3;
            }
            if (isset($request->input_img)) {
                $imageIcon = time() . '.' . $request->input_img->getClientOriginalExtension();
                $file = Input::file('input_img');
                Image::make($file->getRealPath())->fit(128, 128)->save('uploads/image_icon/' . $imageIcon);
                $imageName = time() . '.' . $request->input_img->getClientOriginalExtension();
                $request->input_img->move('uploads/profile_image/', $imageName);
                $emp->profile_image = $imageName;
                $emp->profile_icon = $imageIcon;
            }
            $emp->location_on = $request['location_on'] == null ? 3 : 4;
            $emp->email_cc = $request['email_cc'] == "" ? '' : $request['email_cc'];
            $emp->role_id = $request['role_id'];
            $emp->master_role_id = $request['master_role_id'];
            $emp->user_token = md5(uniqid(rand(), true));
            $emp->manager_id = $request['manager_id'];
            $emp->line_manager_id = $request['line_manager_id'];
            $emp->allowed_distance = $request['allowed_distance'];
            $emp->updated_by = $this->currentUser->employee()->id;
            $emp->save();
            $empUser = $emp->user();
            $empUser->email = $request['email'];
            $empUser->name = $request['name'];
            $empUser->save();
            DB::commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            // return redirect()->back()->with('danger', 'Not Created');
        }

        return redirect()->back()->with('success', 'successfully Updated');


    }


    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $employee = Employee::findorfail($id);
            $user = $employee->user();
            $user->status_id = $employee->user()->status_id == 1 ? 2 : 1;
            $employee->updated_by = $this->currentUser->employee()->id;
            $user->save();
            return redirect('/groupEmployee');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function reset(Request $request, $id)
    {

        if ($this->userMenu->wsmu_updt) {
            $employee = Employee::findorfail($id);
            $employee->user_token = time() . $id . substr(md5(mt_rand()), 5, 22);
            $employee->updated_by = $this->currentUser->employee()->id;
            $user = $employee->user();
            $user->password = bcrypt($user->email);
            $employee->save();
            $user->save();
            return redirect('/groupEmployee');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function passChange($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $employee = Employee::findorfail($id);
            return view('master_data.group_employee.pass_change')->with('employee', $employee);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function change(Request $request, $id)
    {
        $employee = Employee::findorfail($id);
        $employee->user_token = time() . $id . substr(md5(mt_rand()), 5, 22);
        $employee->updated_by = $this->currentUser->employee()->id;
        $user = $employee->user();
        $user->password = bcrypt($request['password']);
        $employee->save();
        $user->save();
        return redirect('/groupEmployee');
    }

    public function filterEmployee(Request $request)
    {
        $divisions = DB::select("SELECT
  t1.id as id,
  concat(t1.name,'(',t3.email,')') as name
FROM tbld_employee AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.emp_id
INNER JOIN users as t3 ON t1.user_id=t3.id
WHERE t2.sales_group_id=$request->sales_group_id
GROUP BY t1.id,t1.name,t3.email");
        return Response::json($divisions);
    }

}
