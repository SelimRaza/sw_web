<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\GroupDataUpload;


use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\MasterData\Route;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Excel;

class RouteSiteUploadController extends Controller
{
    private $access_key = 'tbld_RouteSiteUploadController_format';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function routeSiteFormat()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $userSaleGroups = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id
FROM tbld_sales_group AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
WHERE t2.emp_id = $emp_id and t1.country_id=$country_id");
            return view('master_data.upload.route_site_format')->with("salesGroups", $userSaleGroups);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function routeSiteFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(RouteSite::create($request->base_id, $request->route_id), 'route_outlet_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function routeSiteUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.upload.route_site_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function routeSiteInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::beginTransaction();
                try {

                    Excel::import(new RouteSite(), $request->file('import_file'));
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


}