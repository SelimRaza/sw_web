<?php

namespace App\Http\Controllers\Mapping;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Mapping\RoutePlan;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use App\MasterData\Site;
use App\BusinessObject\PriceList;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\SalesGroupSku;
use App\Mapping\PLDT;
use App\Mapping\SGIT;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
Use App\MasterData\RouteSite;
Use App\MasterData\Route;
use Maatwebsite\Excel\Facades\Excel;
class RoutePlanMappingController extends Controller
{
    private $access_key = 'rpln-mapping';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        set_time_limit(8000000);
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    // Rout Plan Mapping
    public function index(){
        $data=DB::connection($this->db)->select("SELECT t2.id emp_id,t2.aemp_usnm,t2.aemp_name name,t3.slgp_name group_name FROM `tl_rpln` t1 
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                    INNER JOIN  tm_rout t4 ON t1.rout_id=t4.id
                    GROUP BY  t2.id,t2.aemp_usnm,t2.aemp_name,t3.slgp_name
                    ORDER BY t1.id DESC");
        return view('Mapping.RoutePlan.rout_plan',[
            'permission' => $this->userMenu,
            'pjps' => $data
        ]);
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            return view('Mapping.RoutePlan.route_plan_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function excelFormat()
    {
        if ($this->userMenu->wsmu_crat) {

            return Excel::download(new RoutePlan(), 'rout_plan_mapping_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new RoutePlan(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    dd($e);
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', 'Data wrong ' . $e->getMessage());
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function show($path){
        if($path) {
            if ($this->userMenu->wsmu_updt) {
                $data['companies'] = DB::connection($this->db)->select("SELECT  * FROM  tm_acmp");
                $data['regions'] = DB::connection($this->db)->select("SELECT  * FROM tm_dirg");
                $data['purpose'] = 'Rout '.$path;
                return view('Mapping.RoutePlan.route_like', $data);
            } else {
                return redirect()->back()->with('danger', 'Access Limited');
            }
        }
    }

    public function replace(Request $request){
        DB::connection($this->db)->update("UPDATE `tl_rpln` SET `aemp_id`='$request->to_user' WHERE `aemp_id`='$request->from_user'");
    }

    public function exchange(Request $request){
        $from_user_route = DB::connection($this->db)->select("select * from `tl_rpln` WHERE `aemp_id`='$request->to_user'");
        foreach($from_user_route as $route){
            $route['aemp_id'] = $request->from_user;
        }
        DB::connection($this->db)->delete("DELETE FROM `tl_rpln` WHERE `aemp_id`='$request->to_user'");
        DB::connection($this->db)->update("UPDATE `tl_rpln` SET `aemp_id`='$request->to_user' WHERE `aemp_id`='$request->from_user'");
        RoutePlan::on($this->db)->create([$from_user_route]);
    }
}
