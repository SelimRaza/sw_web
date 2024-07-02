<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\AttendanceProcess;
use App\BusinessObject\HRPolicy;
use App\Mail\Test;
use App\MasterData\Auto;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\Process\AttendanceDataProcess;
use App\Process\DashboardDataProcess;
use App\Process\DataGen;
use App\Process\NotifyUser;
use App\Process\RFLDataProcess;
use App\Process\TrackingDataProcess;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Mail;
use Excel;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private $access_key = 'tbld_role';
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
            $userAll = User::where('password', '=', '')->get();
            foreach ($userAll as $user) {
                $user->password = bcrypt($user->email);
                $user->save();
            }
           /* $dataGen = new  DataGen();
            $country5=(new Country())->country(5);
            $datetime = new \DateTime(now(), new \DateTimeZone($country5->cont_tzon));
            date_default_timezone_set($country5->cont_tzon);
            $dataGen->prgDashboardSRData($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));
            $dataGen->prgdashboardUpdate($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));*/
          /*  $country = Country::all();
            $notify = new  NotifyUser();
            foreach ($country as $country1) {
                date_default_timezone_set($country1->cont_tzon);
                $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                $time = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                $time->add(new \DateInterval('PT30M'));
                $notify->notify($country1->cont_conn, $datetime->format('Y-m-d H:i:s'),$time->format('Y-m-d H:i:s'));
            }*/
            /*$country = Country::all();
            $dashboardDataProcess = new  DataGen();
            foreach ($country as $country1) {
                $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                $dashboardDataProcess->prgDashboardSVData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                $dashboardDataProcess->prgDashboardSRData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
            }*/


            $roles = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.role.index')->with("roles", $roles)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.role.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        //   dd($this->currentuser);
        $role = new Role();
        $role->setConnection($this->db);
        $role->edsg_name = $request->name;
        $role->edsg_code = $request->code;
        $role->lfcl_id = 1;
        $role->cont_id = $this->currentUser->country()->id;
        $role->aemp_iusr = $this->currentUser->employee()->id;
        $role->aemp_eusr = $this->currentUser->employee()->id;
        $role->var = 0;
        $role->attr1 = '';
        $role->attr2 = '';
        $role->attr3 = 0;
        $role->attr4 = 0;
        $role->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $role = Role::on($this->db)->findorfail($id);
            return view('master_data.role.show')->with('role', $role);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $role = Role::on($this->db)->findorfail($id);
            //dd($role);
            return view('master_data.role.edit')->with('role', $role);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            $role = Role::on($this->db)->findorfail($id);
            $role->edsg_name = $request->name;
            $role->edsg_code = $request->code;
            // $role->AEMP_EUSR = $this->currentUser->employee()->ID;
            $role->save();
            return redirect()->back()->with('success', 'successfully Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function roleFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Role(), 'role_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function roleMasterFileUpoad(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Role(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
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
