<?php

namespace App\Http\Controllers\Location;


use App\BusinessObject\LocationCompany;
use App\BusinessObject\LocationDepartment;
use App\BusinessObject\LocationDetails;
use App\BusinessObject\LocationMaster;
use App\BusinessObject\LocationSection;
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
class LocationSectionController extends Controller
{
    private $access_key = 'LocationSectionController';
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
                $locationData = DB::connection($this->db)->table('tm_locm')
                    ->join('tm_lcmp', 'tm_locm.id', '=', 'tm_lcmp.locm_id')
                    ->join('tm_ldpt', 'tm_lcmp.id', '=', 'tm_ldpt.lcmp_id')
                    ->join('tm_lsct', 'tm_ldpt.id', '=', 'tm_lsct.ldpt_id')
                    ->select('tm_lsct.id', DB::connection($this->db)->raw('concat(tm_lsct.lsct_name, " < ", tm_ldpt.ldpt_name, " < ", tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS lsct_name '), 'tm_lsct.lsct_code')
                    ->where(function ($query) use ($q) {
                        $query->where('tm_lsct.lsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_ldpt.ldpt_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_lcmp.lcmp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_locm.locm_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $locationData = DB::connection($this->db)->table('tm_locm')
                    ->join('tm_lcmp', 'tm_locm.id', '=', 'tm_lcmp.locm_id')
                    ->join('tm_ldpt', 'tm_lcmp.id', '=', 'tm_ldpt.lcmp_id')
                    ->join('tm_lsct', 'tm_ldpt.id', '=', 'tm_lsct.ldpt_id')
                    ->select('tm_lsct.id', DB::connection($this->db)->raw('concat(tm_lsct.lsct_name, " < ", tm_ldpt.ldpt_name, " < ", tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS lsct_name '), 'tm_lsct.lsct_code')
                    ->paginate(100);
                //  $locationData = LocationSection::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
            }
            return view('Location.location_section.index')->with("locationData", $locationData)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            $locationDepartment = DB::connection($this->db)->select("SELECT
  t3.id,
  concat(t3.ldpt_name, ' < ', t2.lcmp_name, ' < ', t1.locm_name) AS ldpt_name
FROM tm_locm t1
  INNER JOIN tm_lcmp AS t2 ON t1.id = t2.locm_id
  INNER JOIN tm_ldpt AS t3 ON t2.id = t3.lcmp_id");
            return view('Location.location_section.create')->with("locationDepartment", $locationDepartment);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        //   dd($this->currentuser);
        $locationData = new LocationSection();
        $locationData->setConnection($this->db);
        $locationData->lsct_name = $request->lsct_name;
        $locationData->lsct_code = $request->lsct_code;
        $locationData->ldpt_id = $request->ldpt_id;
        $locationData->lfcl_id = 1;
        $locationData->cont_id = $this->currentUser->country()->id;
        $locationData->aemp_iusr = $this->currentUser->employee()->id;
        $locationData->aemp_eusr = $this->currentUser->employee()->id;
        $locationData->save();
        return redirect()->back()->withInput()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $locationData = LocationSection::on($this->db)->findorfail($id);
            return view('Location.location_section.show')->with('locationData', $locationData);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $locationDepartment = DB::connection($this->db)->select("SELECT
  t3.id,
  concat(t3.ldpt_name, ' < ', t2.lcmp_name, ' < ', t1.locm_name) AS ldpt_name
FROM tm_locm t1
  INNER JOIN tm_lcmp AS t2 ON t1.id = t2.locm_id
  INNER JOIN tm_ldpt AS t3 ON t2.id = t3.lcmp_id");
            $locationData = LocationSection::on($this->db)->findorfail($id);
            return view('Location.location_section.edit')->with('locationData', $locationData)->with("locationDepartment", $locationDepartment);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            $locationData = LocationSection::on($this->db)->findorfail($id);
            $locationData->lsct_name = $request->lsct_name;
            $locationData->lsct_code = $request->lsct_code;
            $locationData->ldpt_id = $request->ldpt_id;
            $locationData->save();
            return redirect()->back()->with('success', 'successfully Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function format(Request $request)
    {

        if ($this->userMenu->wsmu_updt) {
            return Excel::download(new LocationSection(), 'section_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function upload(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new LocationSection(), $request->file('import_file'));
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
