<?php

namespace App\Http\Controllers\Location;


use App\BusinessObject\LocationCompany;
use App\BusinessObject\LocationDetails;
use App\BusinessObject\LocationMaster;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Excel;


class LocationCompanyController extends Controller
{
    private $access_key = 'LocationCompanyController';
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


    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $q = '';
            if ($request->has('search_text')) {
                $q = request('search_text');
                /*$locationData = LocationCompany::on($this->db)->where(function ($query) use ($q) {
                    $query->where('lcmp_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('id', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(100)->setPath('');*/
                $locationData = DB::connection($this->db)->table('tm_locm')
                    ->join('tm_lcmp', 'tm_locm.id', '=', 'tm_lcmp.locm_id')
                    ->select('tm_lcmp.id', DB::connection($this->db)->raw('concat( tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS lcmp_name '), 'tm_lcmp.lcmp_code')
                    ->where(function ($query) use ($q) {
                        $query->Where('tm_lcmp.lcmp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_locm.locm_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                //$locationData = LocationCompany::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
                $locationData = DB::connection($this->db)->table('tm_locm')
                    ->join('tm_lcmp', 'tm_locm.id', '=', 'tm_lcmp.locm_id')
                    ->select('tm_lcmp.id', DB::connection($this->db)->raw('concat( tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS lcmp_name '), 'tm_lcmp.lcmp_code')
                    ->paginate(100);
            }
            return view('Location.location_company.index')->with("locationData", $locationData)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $locationMaster = LocationMaster::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('Location.location_company.create')->with("locationMaster", $locationMaster);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        //   dd($this->currentuser);
        $locationData = new LocationCompany();
        $locationData->setConnection($this->db);
        $locationData->lcmp_name = $request->lcmp_name;
        $locationData->lcmp_code = $request->lcmp_code;
        $locationData->locm_id = $request->locm_id;
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
            $locationData = LocationCompany::on($this->db)->findorfail($id);
            return view('Location.location_company.show')->with('locationData', $locationData);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $locationMaster = LocationMaster::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            $locationData = LocationCompany::on($this->db)->findorfail($id);
            return view('Location.location_company.edit')->with('locationData', $locationData)->with("locationMaster", $locationMaster);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            $locationData = LocationCompany::on($this->db)->findorfail($id);
            $locationData->lcmp_name = $request->lcmp_name;
            $locationData->lcmp_code = $request->lcmp_code;
            $locationData->locm_id = $request->locm_id;
            $locationData->save();
            return redirect()->back()->with('success', 'successfully Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function locationCompanyFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new LocationCompany(), 'Location_company_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function locationCompanyFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new LocationCompany(), $request->file('import_file'));
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
