<?php

namespace App\Http\Controllers\Location;


use App\BusinessObject\LocationDetails;
use App\BusinessObject\LocationMaster;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Excel;

class LocationMasterController extends Controller
{
    private $access_key = 'LocationMasterController';
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
            $locationData= LocationMaster::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('Location.location_master.index')->with("locationData", $locationData)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('Location.location_master.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        //   dd($this->currentuser);
        $locationData = new LocationMaster();
        $locationData->setConnection($this->db);
        $locationData->locm_name = $request->locm_name;
        $locationData->locm_code = $request->locm_code;
        $locationData->lfcl_id = 1;
        $locationData->cont_id = $this->currentUser->country()->id;
        $locationData->aemp_iusr = $this->currentUser->employee()->id;
        $locationData->aemp_eusr = $this->currentUser->employee()->id;
        $locationData->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $locationData = LocationMaster::on($this->db)->findorfail($id);
            return view('Location.location_master.show')->with('locationData', $locationData);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $locationData = LocationMaster::on($this->db)->findorfail($id);
            return view('Location.location_master.edit')->with('locationData', $locationData);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            $locationData = LocationMaster::on($this->db)->findorfail($id);
            $locationData->locm_name = $request->locm_name;
            $locationData->locm_code = $request->locm_code;
            $locationData->save();
            return redirect()->back()->with('success', 'successfully Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function locationMasterFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new LocationMaster(), 'location_master_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function locationMasterFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new LocationMaster(), $request->file('import_file'));
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
