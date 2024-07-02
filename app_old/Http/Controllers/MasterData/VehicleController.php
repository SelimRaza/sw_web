<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Category;
use App\MasterData\DepotMain;
use App\MasterData\ReturnReason;
use App\MasterData\Vehicle;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    private $access_key = 'VehicleController';
    private $currentUser;
    private $userMenu;

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
            $vehicles = Vehicle::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.vehicle.index')->with('vehicles', $vehicles)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $depots = DepotMain::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.vehicle.create')->with('depots', $depots)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    /*vhcl_name
    vhcl_code
    vhcl_type
    dpot_id
    vhcl_rdat
    vhcl_ownr
    vhcl_engn
    vhcl_csis
    vhcl_licn
    vhcl_cpct
    vhcl_fuel
    vhcl_lmrd
    vhcl_cpwt
    vhcl_cpht
    vhcl_cpwd
    vhcl_cplg
    */

    public function store(Request $request)
    {
        $vehicle = new Vehicle();
        $vehicle->setConnection($this->db);
        $vehicle->vhcl_name = $request->vhcl_name;
        $vehicle->vhcl_code = $request->vhcl_code;
        $vehicle->vhcl_type = $request->vhcl_type;
        $vehicle->dpot_id = $request->dpot_id;
        $vehicle->vhcl_rdat = isset($request->vhcl_rdat) ? $request->vhcl_rdat : "";
        $vehicle->vhcl_ownr = isset($request->vhcl_ownr) ? $request->vhcl_ownr : "";
        $vehicle->vhcl_engn = isset($request->vhcl_engn) ? $request->vhcl_engn : "";
        $vehicle->vhcl_csis = isset($request->vhcl_csis) ? $request->vhcl_csis : "";
        $vehicle->vhcl_licn = isset($request->vhcl_licn) ? $request->vhcl_licn : "";
        $vehicle->vhcl_cpct = isset($request->vhcl_cpct) ? $request->vhcl_cpct : "";
        $vehicle->vhcl_fuel = isset($request->vhcl_fuel) ? $request->vhcl_fuel : "";
        $vehicle->vhcl_lmrd = isset($request->vhcl_lmrd) ? $request->vhcl_lmrd : "";
        $vehicle->vhcl_cpwt = isset($request->vhcl_cpwt) ? $request->vhcl_cpwt : "";
        $vehicle->vhcl_cpht = isset($request->vhcl_cpht) ? $request->vhcl_cpht : "";
        $vehicle->vhcl_cpwd = isset($request->vhcl_cpwd) ? $request->vhcl_cpwd : "";
        $vehicle->vhcl_cplg = isset($request->vhcl_cplg) ? $request->vhcl_cplg : "";
        $vehicle->lfcl_id = 1;
        $vehicle->cont_id = $this->currentUser->country()->id;
        $vehicle->aemp_iusr = $this->currentUser->employee()->id;
        $vehicle->aemp_eusr = $this->currentUser->employee()->id;
        $vehicle->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $vehicle = Vehicle::on($this->db)->findorfail($id);
            return view('master_data.vehicle.show')->with('vehicle', $vehicle);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $vehicle = Vehicle::on($this->db)->findorfail($id);
            $depots = DepotMain::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.vehicle.edit')->with('vehicle', $vehicle)->with('depots', $depots)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::on($this->db)->findorfail($id);
        $vehicle->vhcl_name = $request->vhcl_name;
        $vehicle->vhcl_code = $request->vhcl_code;
        $vehicle->vhcl_type = $request->vhcl_type;
        $vehicle->dpot_id = $request->dpot_id;
        $vehicle->vhcl_rdat = isset($request->vhcl_rdat) ? $request->vhcl_rdat : "";
        $vehicle->vhcl_ownr = isset($request->vhcl_ownr) ? $request->vhcl_ownr : "";
        $vehicle->vhcl_engn = isset($request->vhcl_engn) ? $request->vhcl_engn : "";
        $vehicle->vhcl_csis = isset($request->vhcl_csis) ? $request->vhcl_csis : "";
        $vehicle->vhcl_licn = isset($request->vhcl_licn) ? $request->vhcl_licn : "";
        $vehicle->vhcl_cpct = isset($request->vhcl_cpct) ? $request->vhcl_cpct : "";
        $vehicle->vhcl_fuel = isset($request->vhcl_fuel) ? $request->vhcl_fuel : "";
        $vehicle->vhcl_lmrd = isset($request->vhcl_lmrd) ? $request->vhcl_lmrd : "";
        $vehicle->vhcl_cpwt = isset($request->vhcl_cpwt) ? $request->vhcl_cpwt : "";
        $vehicle->vhcl_cpht = isset($request->vhcl_cpht) ? $request->vhcl_cpht : "";
        $vehicle->vhcl_cpwd = isset($request->vhcl_cpwd) ? $request->vhcl_cpwd : "";
        $vehicle->vhcl_cplg = isset($request->vhcl_cplg) ? $request->vhcl_cplg : "";
        $vehicle->aemp_eusr = $this->currentUser->employee()->id;
        $vehicle->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $vehicle = Vehicle::on($this->db)->findorfail($id);
            $vehicle->lfcl_id = $vehicle->lfcl_id == 1 ? 2 : 1;
            $vehicle->aemp_eusr = $this->currentUser->employee()->id;
            $vehicle->save();
            return redirect('/vehicle');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function vehicleFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Vehicle(), 'vehicle_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function vehicleFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Vehicle(), $request->file('import_file'));
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
