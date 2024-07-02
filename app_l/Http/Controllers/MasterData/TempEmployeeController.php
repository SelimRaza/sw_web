<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Category;
use App\MasterData\DepotMain;
use App\MasterData\ReturnReason;
use App\MasterData\Vehicle;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TempEmployeeController extends Controller
{
    private $access_key = 'TempEmployeeController';
    private $currentUser;
    private $userMenu;

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

    public function newEmployee()
    {
        if ($this->userMenu->wsmu_vsbl) {

            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");

            //$vehicles = Vehicle::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.employee.new_employee')->with('acmp', $acmp)->with('dsct', $dsct)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function newEmployeeFilterDetails(Request $request){
        $q1 = "";
        $q2 = "";

        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;
        $sr_id = $request->sr_id;

        if ($acmp_id != "") {
            $q1 = " AND t2.acmp_id = '$acmp_id'";
        }
        if ($slgp_id != "") {
            $q1 = " AND t1.slgp_id = '$slgp_id'";
        }
        if ($sr_id!=""){
            $query = "SELECT t1.id, t1.aemp_name, t1.`aemp_mob1`, t1.`aemp_usnm`, t2.slgp_name, t3.zone_name,
 t4.role_name,concat(t5.aemp_usnm,' - ',t5.aemp_name) as manager_name  FROM `tm_aemp` t1 
INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
INNER JOIN tm_role t4 ON t1.role_id=t4.id
INNER JOIN tm_aemp t5 ON t1.aemp_mngr=t5.id WHERE t1.role_id='1' and t1.lfcl_id='1' AND t1.aemp_mngr!='1'
            AND t1.id NOT IN (SELECT DISTINCT aemp_id from tl_rpln) and t1.aemp_usnm='$sr_id'";
        }else{
            $query = "SELECT t1.id, t1.aemp_name, t1.`aemp_mob1`, t1.`aemp_usnm`, t2.slgp_name, t3.zone_name,
 t4.role_name,concat(t5.aemp_usnm,' - ',t5.aemp_name) as manager_name  FROM `tm_aemp` t1 
INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
INNER JOIN tm_role t4 ON t1.role_id=t4.id
INNER JOIN tm_aemp t5 ON t1.aemp_mngr=t5.id WHERE t1.role_id='1' and t1.lfcl_id='1' AND t1.aemp_mngr!='1' 
AND t1.id NOT IN (SELECT DISTINCT aemp_id from tl_rpln) " . $q1 . $q2 ;
        }



        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        return $srData;
    }
}
