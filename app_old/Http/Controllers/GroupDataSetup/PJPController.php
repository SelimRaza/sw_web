<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\BusinessObject\SalesGroup;
use App\BusinessObject\SalesGroupEmployee;
use App\MasterData\Employee;
use App\MasterData\PJP;
use App\MasterData\RoutePlan;
use App\MasterData\Route;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;

class PJPController extends Controller
{
    private $access_key = 'tl_rpln';
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
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;
            if ($request->has('search_text')) {
                $q = request('search_text');
                $pjps = DB::connection($this->db)->table('tl_rpln as t1')
                    ->join('tl_sgsm as t2', 't1.aemp_id', '=', 't2.aemp_id')
                    ->join('tm_aemp as t3', 't1.aemp_id', '=', 't3.id')
                    ->join('tm_slgp as t4', 't2.slgp_id', '=', 't4.id')
                    ->select('t3.aemp_usnm as aemp_usnm', 't1.aemp_id as emp_id', "t3.aemp_name AS name", 't4.slgp_name                                 AS group_name')
                    ->where(['t1.cont_id' => $country_id])
                    ->where(function ($query) use ($q) {
                        $query->where('t3.aemp_usnm', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.aemp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.slgp_name', 'LIKE', '%' . $q . '%');
                    })
                    ->groupBy("t1.aemp_id", "t3.aemp_name", "t3.aemp_usnm", "t4.slgp_name")->paginate(100)->setPath('');
            } else {
                $pjps = DB::connection($this->db)->table('tl_rpln as t1')
                    ->join('tl_sgsm as t2', 't1.aemp_id', '=', 't2.aemp_id')
                    ->join('tm_aemp as t3', 't1.aemp_id', '=', 't3.id')
                    ->join('tm_slgp as t4', 't2.slgp_id', '=', 't4.id')
                    ->select('t3.aemp_usnm as aemp_usnm', 't1.aemp_id as emp_id', "t3.aemp_name AS name", 't4.slgp_name                                 AS group_name')
                    ->where(['t1.cont_id' => $country_id])
                    ->groupBy("t1.aemp_id", "t3.aemp_name", "t3.aemp_usnm", "t4.slgp_name")->paginate(100)->setPath('');
            }
            /*          $pjps = DB::connection($this->db)->select("SELECT
            t1.aemp_id as emp_id,
            concat(t3.aemp_name, '(', t3.aemp_usnm, ')') AS name,
            t4.slgp_name                                 AS group_name
          FROM tl_rpln AS t1
            INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
            INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
            INNER JOIN tm_slgp AS t4 ON t2.slgp_id = t4.id
          WHERE t1.cont_id= $country_id
           group by t1.aemp_id,t3.aemp_name,t3.aemp_usnm,t4.slgp_name");*/

            return view('master_data.pjp.index')->with('pjps', $pjps)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }

    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $userSaleGroups = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.slgp_name AS name,
  t1.slgp_code AS code,
  t1.lfcl_id   AS status_id
FROM tm_slgp AS t1 
WHERE  t1.cont_id = $country_id");
            $routes = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.rout_name AS name,
  t1.rout_code AS code,
  t1.lfcl_id   AS status_id
FROM tm_rout AS t1
  INNER JOIN tl_emcm AS t2 ON t1.acmp_id = t2.acmp_id
WHERE t1.cont_id = $country_id AND t2.aemp_id = $emp_id");
            return view('master_data.pjp.create')->with("salesGroups", $userSaleGroups)->with("routes", $routes);
        } else {
            return view('theme.access_limit');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            for ($i = 0; $i < sizeof($request->day); $i++) {
                if ($request->route_id[$i] != null) {

                    $pjp = PJP::on($this->db)->where(['aemp_id' => $request->emp_id, 'rpln_day' => $request->day[$i]])->first();
                    if ($pjp != null) {
                        $pjp->rout_id = $request->route_id[$i];
                        $pjp->aemp_eusr = $this->currentUser->employee()->id;
                        $pjp->save();
                    } else {
                        $pjp = new PJP();
                        $pjp->setConnection($this->db);
                        $pjp->aemp_id = $request->emp_id;
                        $pjp->rpln_day = $request->day[$i];
                        $pjp->rout_id = $request->route_id[$i];
                        $pjp->cont_id = $this->currentUser->employee()->cont_id;
                        $pjp->lfcl_id = 1;
                        $pjp->aemp_iusr = $this->currentUser->employee()->id;
                        $pjp->aemp_eusr = $this->currentUser->employee()->id;
                        $pjp->var = 1;
                        $pjp->attr1 = '';
                        $pjp->attr2 = '';
                        $pjp->attr3 = 0;
                        $pjp->attr4 = 0;
                        $pjp->save();
                    }
                }

            }
            DB::commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function show($id)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id = $this->currentUser->employee()->id;
        $sales_group = SalesGroupEmployee::on($this->db)->where(['aemp_id' => $id])->first();
        $pjps = PJP::on($this->db)->where(['aemp_id' => $id])->get();
        $emp = Employee::on($this->db)->findorfail($id);
        $routes = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.rout_name AS name,
  t1.rout_code AS code,
  t1.lfcl_id   AS status_id
FROM tm_rout AS t1
  where t1.cont_id = $country_id");
        //$routes = Route::where(['cont_id' => $this->currentUser->employee()->cont_id, 'slgp_id' => $sales_group->slgp_id])->get();
        return view('master_data.pjp.show')->with('emp', $emp)->with('pjps', $pjps)->with('routes', $routes)->with('sales_group', $sales_group)->with('permission', $this->userMenu);
    }


    /*  public function edit($id)
      {
          $pjps = PJP::where(['emp_id' => $id])->get();
          $emp = Employee::findorfail($id);
          $currentUser = auth()->user();
          $routes = Route::where('country_id', '=', $currentUser->employee()->country_id)->get();
          return view('master_data.pjp.edit')->with('emp', $emp)->with('routes', $routes)->with('pjps', $pjps);
      }

      public function update(Request $request, $id)
      {
          $currentUser = auth()->user();
          DB::beginTransaction();
          try {
              for ($i = 0; $i < sizeof($request->day); $i++) {
                  if ($request->route_id[$i] != null) {
                      $saturday_pjp = PJP::where(['emp_id' => $id, 'day' => $request->day[$i]])->first();
                      $saturday_pjp->route_id = $request->route_id[$i];
                      $saturday_pjp->updated_by = $currentUser->employee()->id;
                      $saturday_pjp->save();
                  }
              }
              DB::commit();
              return redirect()->back()->with('success', 'successfully Updated');
          } catch (\Exception $e) {
              DB::rollback();
              throw $e;
          }

      }*/

    public function destroy($id)
    {
        $pjp = PJP::on($this->db)->findorfail($id);
        $pjp->delete();
        return redirect()->back()->with('success', 'successfully Deleted');

    }

    public function routeAdd(Request $request, $id)
    {
        $pjp = PJP::on($this->db)->where(['aemp_id' => $id, 'rpln_day' => $request->day_name])->first();
        if ($pjp == null) {
            $pjp = new PJP();
            $pjp->setConnection($this->db);
            $pjp->aemp_id = $id;
            $pjp->rpln_day = $request->day_name;
            $pjp->rout_id = $request->route_id;
            $pjp->cont_id = $this->currentUser->employee()->cont_id;
            $pjp->lfcl_id = 1;
            $pjp->aemp_iusr = $this->currentUser->employee()->id;
            $pjp->aemp_eusr = $this->currentUser->employee()->id;
            $pjp->var = 1;
            $pjp->attr1 = '';
            $pjp->attr2 = '';
            $pjp->attr3 = 0;
            $pjp->attr4 = 0;
            $pjp->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }
    }


    public function showSite($id, $emp_id)
    {
        if ($this->userMenu->wsmu_read) {
            $emp = Employee::on($this->db)->findorfail($emp_id);
            $route = Route::on($this->db)->findorfail($id);
            $routeSites = RouteSite::on($this->db)->where('rout_id', '=', $id)->get();
            return view('master_data.pjp.show_site')->with("routeSites", $routeSites)->with("route", $route)->with("emp", $emp)->with('permission', $this->userMenu);
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
                return redirect()->back()->with('danger', 'Outlet NotFound exist');
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

    public function empRouteFormat()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $userSaleGroups = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.slgp_name AS name,
  t1.slgp_code AS code,
  t1.lfcl_id   AS status_id
FROM tm_slgp AS t1 
WHERE  t1.cont_id = $country_id");
            return view('master_data.pjp.emp_route_format')->with('salesGroups', $userSaleGroups);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empRouteFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(PJP::create($request->emp_id), 'employee_route_plan_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function routePlanUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new RoutePlan(), 'employee_route_plan_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empRouteUpload()
    {
        if ($this->userMenu->wsmu_crat) {

            return view('master_data.pjp.emp_route_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empRouteInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new PJP(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . "Please check Route or Employee code. One of them not found in master data. After correction please try again!!!");
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
