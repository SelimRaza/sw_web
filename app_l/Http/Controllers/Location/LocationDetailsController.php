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

class LocationDetailsController extends Controller
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
                    ->join('tm_locd', 'tm_lsct.id', '=', 'tm_locd.lsct_id')
                    ->join('tm_ltyp', 'tm_locd.ltyp_id', '=', 'tm_ltyp.id')
                    ->select('tm_locd.id',
                        DB::connection($this->db)->raw('concat(tm_locd.locd_name, " < ",tm_lsct.lsct_name, " < ", tm_ldpt.ldpt_name, " < ", tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS locd_name '),
                        'tm_ltyp.ltyp_name',
                        'tm_locd.locd_code',
                        'tm_locd.geo_adrs'
                    )
                    ->where(function ($query) use ($q) {
                        $query->where('tm_lsct.lsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_ldpt.ldpt_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_lcmp.lcmp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_locm.locm_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_locd.locd_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tm_locd.locd_code', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $locationData = DB::connection($this->db)->table('tm_locm')
                    ->join('tm_lcmp', 'tm_locm.id', '=', 'tm_lcmp.locm_id')
                    ->join('tm_ldpt', 'tm_lcmp.id', '=', 'tm_ldpt.lcmp_id')
                    ->join('tm_lsct', 'tm_ldpt.id', '=', 'tm_lsct.ldpt_id')
                    ->join('tm_locd', 'tm_lsct.id', '=', 'tm_locd.lsct_id')
                    ->join('tm_ltyp', 'tm_locd.ltyp_id', '=', 'tm_ltyp.id')
                    ->select('tm_locd.id',
                        DB::connection($this->db)->raw('concat(tm_locd.locd_name, " < ",tm_lsct.lsct_name, " < ", tm_ldpt.ldpt_name, " < ", tm_lcmp.lcmp_name, " < ", tm_locm.locm_name) AS locd_name '),
                        'tm_ltyp.ltyp_name',
                        'tm_locd.locd_code',
                        'tm_locd.geo_adrs'
                    )
                    ->paginate(100);
                //  $locationData = LocationSection::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
            }
            return view('Location.location_details.index')->with("locationData", $locationData)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $locationData = collect(DB::connection($this->db)->select("SELECT
  concat(t5.lsct_name, ' < ', t4.ldpt_name, ' < ', t3.lcmp_name, ' < ', t2.locm_name) AS locm_name,
  t6.ltyp_name                                                                  AS ltyp_name,
  t1.locd_name,
  t1.locd_code,
  t1.geo_adrs                                                                   AS location,
  t7.aemp_name                                                                  AS created_by,
  t8.aemp_name                                                                  AS updated_by,
  t1.created_at,
  t1.updated_at
FROM tm_locd AS t1
  INNER JOIN tm_locm AS t2 ON t1.locm_id = t2.id
  INNER JOIN tm_lcmp AS t3 ON t1.lcmp_id = t3.id
  INNER JOIN tm_ldpt AS t4 ON t1.ldpt_id = t4.id
  INNER JOIN tm_lsct AS t5 ON t1.lsct_id = t5.id
  INNER JOIN tm_ltyp AS t6 ON t1.ltyp_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_iusr = t7.id
  INNER JOIN tm_aemp AS t8 ON t1.aemp_eusr = t8.id
WHERE t1.id = $id"))->first();
            return view('Location.location_details.show')->with('locationData', $locationData);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


}
