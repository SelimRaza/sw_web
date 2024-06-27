<?php

namespace App\Http\Controllers\Setting;

use App\BusinessObject\Dashboard1Permission;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class Dashboard1PermissionController extends Controller
{
    private $access_key = 'Dashboard1PermissionController';
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
            $dataDashboardPermissions = DB::select("SELECT
  t1.id,
  t1.user_id AS assign_user,
  t1.pr_user_id    AS dashboard_user
FROM tblh_dashboard_permission AS t1");

            return view('Setting.Dashboard1Permission.index')->with("dataDashboardPermissions", $dataDashboardPermissions)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('Setting.Dashboard1Permission.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $dataDashboardPermission = new Dashboard1Permission();
        $dataDashboardPermission->user_id = $request->user_name;
        $dataDashboardPermission->pr_user_id = $request->sv_user_name;
        $dataDashboardPermission->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            Dashboard1Permission::findorfail($id)->delete();
            return redirect()->back()->with('success', 'successfully Removed');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
