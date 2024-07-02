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
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        return view('Mapping.sr_group',['users'=>$users]);
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
    
    
}