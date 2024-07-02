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
use App\MasterData\PJP;
use App\MasterData\RoutePlan;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class SRGroupController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
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

    //SKU Master Load Function
    public function index(){
        $users=DB::connection($this->db)->select("Select id, aemp_usnm,aemp_name FROM tm_aemp WHERE lfcl_id=1 order by aemp_usnm");
        return view('Mapping.sr_group',['users'=>$users, 'datas' => collect([])]);
    }

    //SKU Master Load Function
    public function routSite(Request $request){
        $aemp_id = 6449;
//        $aemp_id = $request->aemp_id;

        $routeSites = DB::connection($this->db)->select("SELECT
                t4.rout_name,t4.rout_code,t5.site_code,t5.site_name,t1.rpln_day
                FROM tl_rpln t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tl_rsmp t3 ON t1.rout_id=t3.rout_id
                INNER JOIN tm_rout t4 ON t3.rout_id=t4.id
                INNER JOIN tm_site t5 ON t3.site_id=t5.id
                WHERE t1.aemp_id={$aemp_id}
                ORDER BY t1.rpln_day ASC;");

        $info['datas'] = $this->paginateArray($routeSites);

        $info['rout_count'] = collect($routeSites)->groupBy('rout_code')->count();
        $info['total_day'] = collect($routeSites)->groupBy('rpln_day')->count();

        return view('Mapping.sr_group_route_site_table', $info);
    }
    

    public function paginateArray($data, $perPage = 20)
    {
        $page = Paginator::resolveCurrentPage();
        $total = count($data);
        $results = array_slice($data, ($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }


    public function getEmployeeSalesGroupMapping(Request $request){
        $aemp_id=$request->aemp_id;
        $data=DB::connection($this->db)->select("SELECT 
                t2.aemp_usnm,t2.aemp_name,t3.slgp_name,t3.slgp_code
                FROM `tl_sgsm` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                WHERE t1.aemp_id=$aemp_id
                GROUP BY t2.aemp_usnm,t2.aemp_name,t3.slgp_name,t3.slgp_code
                ORDER BY t3.slgp_name ASC");
        return $data;
    }

    public function getEmployeeSalesGroupMappingInfo(Request $request){
        $aemp_id = $request->aemp_id;

        $routeSites = DB::connection($this->db)->select("SELECT
                t4.rout_name,t4.rout_code,t5.site_code,t5.site_name,t1.rpln_day
                FROM tl_rpln t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tl_rsmp t3 ON t1.rout_id=t3.rout_id
                INNER JOIN tm_rout t4 ON t3.rout_id=t4.id
                INNER JOIN tm_site t5 ON t3.site_id=t5.id
                WHERE t1.aemp_id={$aemp_id}
                ORDER BY t1.rpln_day ASC;");

        $info['datas'] = $this->paginateArray($routeSites);

        $info['rout_count'] = collect($routeSites)->groupBy('rout_code')->count();
        $info['total_day'] = collect($routeSites)->groupBy('rpln_day')->count();

        return view('Mapping.sr_group_route_site_table', $info)->render();
    }

    public function getEmployeeSalesGroupMappingInfoCsv($aemp_id){
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export.csv"',
        ];

        $routeSites = DB::connection($this->db)->select("SELECT
                t4.rout_name,t4.rout_code,t5.site_code,t5.site_name,t1.rpln_day
                FROM tl_rpln t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tl_rsmp t3 ON t1.rout_id=t3.rout_id
                INNER JOIN tm_rout t4 ON t3.rout_id=t4.id
                INNER JOIN tm_site t5 ON t3.site_id=t5.id
                WHERE t1.aemp_id={$aemp_id}
                ORDER BY t1.rpln_day ASC;");


        $callback = function () use ($routeSites) {
            $handle = fopen('php://output', 'w');
            foreach ($routeSites as $row) {
                fputcsv($handle, collect($row)->toArray(), ',');
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, 'export.csv', $headers);
    }


    public function getEmployeeSalesGroupMappingRoutes(Request $request){
        $aemp_id=$request->aemp_id;
        $data=DB::connection($this->db)->select("SELECT distinct 
                t4.rout_name,t4.rout_code, t1.rout_id
                FROM tl_rpln t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_rout t4 ON t1.rout_id=t4.id
                WHERE t1.aemp_id={$aemp_id}");

        return ['info' => $data, 'aemp_id' => $aemp_id];
    }

    public function employeeRoutesDelete(Request $request){

        try {
            return DB::connection($this->db)->select("DELETE from tl_rpln where aemp_id={$request->aemp_id} and rout_id={$request->rout_id}");
        }catch(\Exception $e)
        {
            return response(['error' => $e->getMessage()], 403);
        }
    }
    
    
}