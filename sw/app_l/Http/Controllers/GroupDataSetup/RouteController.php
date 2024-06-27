<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\Base;
use App\MasterData\Division;
use App\MasterData\PJP;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\RouteSite;
use App\MasterData\RSMP;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\MasterData\SalesGroupZoneBaseMapping;
use Response;
use Excel;
class RouteController extends Controller
{

    private $access_key = 'tm_rout';
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

    public function index(Request $request){
        if ($this->userMenu->wsmu_vsbl) {

            $country_id = $this->currentUser->employee()->cont_id;

            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zone = DB::connection($this->db)->select("SELECT `zone_id`,CONCAT(`zone_code`, ' - ' , `zone_name`) as zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            return view('master_data.route.index')->with('permission', $this->userMenu)->with('acmp',$acmp)->with('zone',$zone);
        }else {
            return view('theme.access_limit');
        }

    }
    public function routeFilter(Request $request){
        $country_id = $this->currentUser->employee()->cont_id;
        $acmp = $request->acmp_id;
        $zone = $request->zone_id;
        $q1="";
        $q2="";
        if ($q1!=""){
            $q1 = "and t1.acmp_id='$acmp'";
        }
        if ($q2!=""){
            $q2 = "and t2.zone_id='$zone'";
        }

        $query = "SELECT t1.id, t2.base_name, t2.base_code, t1.`rout_name`,
t1.`rout_code` FROM `tm_rout` t1 inner join tm_base t2 on t1.base_id = t2.id inner 
JOIN tm_zone t3 on t2.zone_id WHERE t1.cont_id='$country_id' ". $q1 . $q2. " limit 20" ;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        return $srData;

    }

    public function index2(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $q = '';
            if ($request->has('search_text')) {
                $q = request('search_text');
                $routes = DB::connection($this->db)->table('tm_rout as t1')
                    ->join('tm_base as t2', 't2.id', '=', 't1.base_id')
                    ->join('tm_zone as t3', 't3.id', '=', 't2.zone_id')
                    ->join('tm_dirg as t4', 't4.id', '=', 't3.dirg_id')
                    ->select('t1.id  AS id', 't1.rout_name AS name', 't1.rout_code AS code',  't1.lfcl_id   AS status_id',DB::connection($this->db)->raw('concat(t2.base_name,"(",t2.base_code,")", " < ", t3.zone_name,"(",t3.zone_code,")", " < ", t4.dirg_name) AS base_name '))
                    ->where(function ($query) use ($q) {
                        $query->where('t1.rout_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.rout_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.base_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.base_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.zone_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.zone_code', 'LIKE', '%' . $q . '%');
                    })
                    ->where(['t1.cont_id' => $country_id])->paginate(500)->setPath('');//'t3.aemp_id' => $emp_id,
            } else {
                $routes = DB::connection($this->db)->table('tm_rout as t1')
                    ->join('tm_base as t2', 't2.id', '=', 't1.base_id')
                    ->join('tm_zone as t3', 't3.id', '=', 't2.zone_id')
                    ->join('tm_dirg as t4', 't4.id', '=', 't3.dirg_id')
                    ->select('t1.id  AS id', 't1.rout_name AS name', 't1.rout_code AS code',  't1.lfcl_id   AS status_id',DB::connection($this->db)->raw('concat(t2.base_name,"(",t2.base_code,")", " < ", t3.zone_name,"(",t3.zone_code,")", " < ", t4.dirg_name) AS base_name '))

                    ->where(['t1.cont_id' => $country_id])->paginate(500)->setPath('');//'t3.aemp_id' => $emp_id,
            }
            return view('master_data.route.index')->with('routes', $routes)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $companies = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.acmp_name 
FROM tm_acmp AS t1 where t1.cont_id = $country_id");

            $salesGeos = DB::connection($this->db)->select("SELECT
  t1.id,
  concat(t1.base_name, '(', t1.base_code, ')', ' < ', t2.zone_name, '(', t2.zone_code, ')', ' < ',
         t3.dirg_name) AS base_name
FROM tm_base AS t1
  INNER JOIN tm_zone AS t2 ON t1.zone_id = t2.id
  INNER JOIN tm_dirg AS t3 ON t2.dirg_id = t3.id
WHERE t1.cont_id = $country_id");
            return view('master_data.route.create')->with("companies", $companies)->with("salesGeos", $salesGeos);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $route = Route::on($this->db)->where(['rout_code' => $request->code])->first();
            if ($route == null) {
                $route = new Route();
                $route->setConnection($this->db);
                $route->rout_name = $request->name;
                $route->rout_code = $request->code;
                $route->base_id = $request->base_id;
                $route->acmp_id = $request->company_id;
                $route->cont_id = $this->currentUser->employee()->cont_id;
                $route->lfcl_id = 1;
                $route->aemp_iusr = $this->currentUser->employee()->id;
                $route->aemp_eusr = $this->currentUser->employee()->id;
                $route->var = 1;
                $route->attr1 = '';
                $route->attr2 = '';
                $route->attr3 = 0;
                $route->attr4 = 0;
                $route->save();
                DB::commit();
                return redirect()->back()->withInput()->with('success', 'successfully Created');
            }else{
                return back()->withInput()->with('danger', 'Already Exit Code');
            }

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $route = Route::on($this->db)->findorfail($id);
            //$pjps = PJP::on($this->db)->where(['rout_id' => $id])->get();
            $pjps = DB::connection($this->db)->select("SELECT
  t1.id AS rpln_id,
  t1.rpln_day,
  t2.aemp_name,
  t2.aemp_usnm,
  t4.slgp_name,
  t5.zone_name
FROM tl_rpln AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tl_sgsm AS t3 ON t1.aemp_id = t3.aemp_id
  INNER JOIN tm_slgp AS t4 ON t3.slgp_id = t4.id
  INNER JOIN tm_zone AS t5 ON t3.zone_id = t5.id
WHERE t1.rout_id = $id");
            return view('master_data.route.show')->with('route', $route)->with('pjps', $pjps)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $route = Route::on($this->db)->findorfail($id);
            $country_id = $this->currentUser->employee()->cont_id;
            $bases = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.base_name as name
FROM tm_base AS t1");
            $companies = DB::connection($this->db)->select("SELECT
  t1.id        AS acmp_id,
  t1.acmp_name 
FROM tm_acmp AS t1 where t1.cont_id = $country_id");
            return view('master_data.route.edit')->with('route', $route)->with('bases', $bases)->with('companies', $companies);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $route = Route::on($this->db)->findorfail($id);
        $route->rout_name = $request->name;
        $route->rout_code = $request->code;
        $route->base_id = $request->base_id;
        $route->acmp_id = $request->acmp_id;
        $route->aemp_eusr = $this->currentUser->employee()->id;
        $route->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $route = Route::on($this->db)->findorfail($id);
            $route->status_id = $route->status_id == 1 ? 2 : 1;
            $route->updated_by = $this->currentUser->employee()->id;
            $route->save();
            return redirect('/route');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function showSite($id)
    {
        if ($this->userMenu->wsmu_read) {
            $route = Route::on($this->db)->findorfail($id);
            $routeSites = RouteSite::on($this->db)->where('rout_id', '=', $id)->get();
            return view('master_data.route.site_assign')->with("routeSites", $routeSites)->with("route", $route)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $site = Site::on($this->db)->where(['site_code' => $request->site_id])->first();
            if ($site != null) {
                $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $id, 'site_id' => $site->id])->first();
                if ($routeSite1 == null) {
                    $routeSite = new RouteSite();
                    $routeSite->setConnection($this->db);
                    $routeSite->site_id = $site->id;
                    $routeSite->rout_id = $id;
                    $routeSite->rspm_serl = 1;
                    $routeSite->cont_id = $this->currentUser->employee()->cont_id;
                    $routeSite->lfcl_id = 1;
                    $routeSite->aemp_iusr = $this->currentUser->employee()->id;
                    $routeSite->aemp_eusr = $this->currentUser->employee()->id;
                    $routeSite->var = 1;
                    $routeSite->attr1 = '';
                    $routeSite->attr2 = '';
                    $routeSite->attr3 = 0;
                    $routeSite->attr4 = 0;
                    $routeSite->save();
                    return redirect()->back()->with('success', 'successfully Added');
                } else {
                    return redirect()->back()->with('danger', 'Already exist');
                }
            } else {
                return redirect()->back()->with('danger', 'Outlet Not Found');
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function siteRouteDelete($id)
    {
        $routeSite = RouteSite::on($this->db)->findorfail($id);
        $routeSite->delete();
        return redirect()->back()->with('success', 'successfully Deleted');
    }

    public function routeSiteMappingUploadFormatGen(Request $request,$id)
    {
        if ($this->userMenu->wsmu_crat) {
           // dd($id);
           // return redirect()->back()->with('danger', 'Access Limited');
           // PJP::create($request->sales_group_id, $request->emp_id)
            return Excel::download(RouteSite::create($id), 'route_site_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function routeSiteMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                // try {
                    Excel::import(new RSMP(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                // } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' .$e->getMessage());
                // }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
       // Excel::import(new RSMP(), $request->file('import_file'));

    }

    public function routeUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Route(), 'route_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function routeMasterUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.route.route_master_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function routeMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Route(), $request->file('import_file'));
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

public function routeBaseMapping()
    {
        if ($this->userMenu->wsmu_crat) {

            $slgpList = DB::connection($this->db)->select("SELECT `slgp_name`, `slgp_code`, `id` FROM `tm_slgp`");
            $zoneList = DB::connection($this->db)->select("SELECT `zone_name`, `zone_code`, `id` FROM `tm_zone`");
            $baseList = DB::connection($this->db)->select("SELECT `base_name`, `base_code`, `id` FROM `tm_base`");

            return view('master_data.route.route_base_mapping')->with('permission', $this->userMenu)->with('zoneList', $zoneList)->with('slgpList', $slgpList)->with('baseList', $baseList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function routeBaseMappingStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'rout_code' => 'required',
            'base_mob' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $message = '';
            $route = Route::on($this->db)->where(['rout_code' => $request->rout_code])->first();
            if ($route) {
                $base_data = SalesGroupZoneBaseMapping::on($this->db)->where(['slgp_id' => $request->slgp_id,'rout_id' => $route->id])->first();
                if($base_data){
                    $base_data->zone_id  = $request->zone_id;
                    $base_data->base_id  = $request->base_id;
                    $base_data->base_mob = $request->base_mob;
                    $base_data->save();
                    $message = ' Successfully updated';
                }else{
                    $data = new SalesGroupZoneBaseMapping();
                    $data->setConnection($this->db);
                    $data->slgp_id  = $request->slgp_id;
                    $data->zone_id  = $request->zone_id;
                    $data->base_id  = $request->base_id;
                    $data->rout_id  = $route->id;
                    $data->base_mob = $request->base_mob;
                    $data->save();
                    $message = ' Successfully Added';
                }

                DB::commit();
                return redirect()->back()->withInput()->with('success', $message);

            } else {
                return back()->withInput()->with('danger', 'Code not found');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function routeBaseMapFileStore(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('base_map_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new SalesGroupZoneBaseMapping(), $request->file('base_map_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e->getMessage());
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
  
    }


    public function routeBaseMapUploadFormat(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SalesGroupZoneBaseMapping(), 'route_base_map_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    public function filterRoute(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.name as name
FROM tbld_route AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.sales_group_id = t2.sales_group_id
WHERE t2.sales_group_id=$request->sales_group_id
GROUP BY t1.id,t1.name");
        return Response::json($divisions);
    }
}
