<?php

namespace App\Http\Controllers\Setting;

use App\BusinessObject\DashboardPermission;
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

class DashboardPermissionController extends Controller
{
    private $access_key = 'DashboardPermissionController';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });

    }


    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $dataDashboardPermissions = DB::connection($this->db)->select("SELECT
  t1.id,
  concat(t2.aemp_name, '(', t2.aemp_usnm, ')') AS assign_user,
  concat(t3.aemp_name, '(', t3.aemp_usnm, ')') AS dashboard_user
FROM th_dhem AS t1
  INNER JOIN tm_aemp AS t2 ON t1.dhem_emid = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.dhem_peid = t3.id");

            return view('Setting.DashboardPermission.index')->with("dataDashboardPermissions", $dataDashboardPermissions)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('Setting.DashboardPermission.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->user_name])->first();
        $employeeSV = Employee::on($this->db)->where(['aemp_usnm' => $request->sv_user_name])->first();
        $dataDashboardPermission = new DashboardPermission();
        $dataDashboardPermission->setConnection($this->db);
        $dataDashboardPermission->dhem_emid = $employee->id;
        $dataDashboardPermission->dhem_peid = $employeeSV->id;
        $dataDashboardPermission->lfcl_id = 1;
        $dataDashboardPermission->cont_id = $this->currentUser->employee()->cont_id;
        $dataDashboardPermission->aemp_iusr = $this->currentUser->employee()->id;
        $dataDashboardPermission->aemp_eusr = $this->currentUser->employee()->id;
        $dataDashboardPermission->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            DashboardPermission::on($this->db)->findorfail($id)->delete();
            return redirect()->back()->with('success', 'successfully Removed');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
