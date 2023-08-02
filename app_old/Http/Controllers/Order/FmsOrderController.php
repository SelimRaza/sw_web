<?php

namespace App\Http\Controllers\Order;

use App\BusinessObject\AppMenuGroup;
use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\TsmGroupZoneMapping;
use App\DataExport\EmployeeMenuGroup;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Depot;
use App\MasterData\DepotEmployee;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\MasterData\PJP;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Zone;
use App\MasterData\RoutePlan;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\MasterData\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use AWS;
use Excel;
use Response;
use App\BusinessObject\FmsOrder;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use DateTime;

class FmsOrderController extends Controller
{
    private $access_key = 'orders';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
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

    public function index(Request $request)
    {
        $data=DB::connection($this->db)->select("SELECT 
                t1.ordm_date,t1.ordm_ornm,t2.aemp_usnm,t2.aemp_name,t3.site_code,t3.site_name,t4.slgp_name,t1.ordm_amnt,t1.ordm_icnt,t5.lfcl_name
                FROM `tt_ordm` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_slgp t4 ON t1.slgp_id=t4.id
                INNER JOIN tm_lfcl t5 ON t1.lfcl_id=t5.id
                WHERE t1.aemp_iusr=$this->aemp_id AND EXTRACT(YEAR_MONTH FROM t1.ordm_date)= EXTRACT(YEAR_MONTH FROM curdate())
                ORDER BY t1.ordm_date,t1.ordm_ornm DESC");
        return view('Order.index')->with("data", $data)->with('permission', $this->userMenu);
        
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $acmp_list=DB::connection($this->db)->select("SELECT acmp_id,acmp_code,acmp_name FROM user_group_permission WHERE aemp_id=$this->aemp_id GROUP BY acmp_id,acmp_code,acmp_name ORDER BY acmp_name asc");
            $slgp_list=DB::connection($this->db)->select("SELECT slgp_id,slgp_name,slgp_code FROM user_group_permission WHERE aemp_id=$this->aemp_id GROUP BY slgp_id,slgp_name,slgp_code ORDER BY slgp_name asc");
            $depot_list=DB::connection($this->db)->select("Select id,dpot_code,dpot_name FROM tm_dpot WHERE lfcl_id=1 ORDER BY dpot_code,dpot_name asc");
            $sr_list=Employee::on($this->db)->where(['lfcl_id'=>1])->get();
            
            return view('Order.FmsOrder.create',['acmp_list'=>$acmp_list,'slgp_list'=>$slgp_list,'depot_list'=>$depot_list,'sr_list'=>$sr_list])
                        ->with('permission',$this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function store(Request $request){
        $request->validate([
            'aemp_id' =>'required',
            'site_code' =>'required',
            'depot_id' =>'required',
            'slgp_id' =>'required',
            'order_file'=>'required',
        ]);
        $site=$this->getSiteId($request->site_code,$request->slgp_id);
        if ($this->userMenu->wsmu_crat) {
            if($site){
                if ($request->hasFile('order_file')) {
                    DB::beginTransaction();
                    try {                   
                        $site_id=$site[0]->id;
                        $plmt_id=$site[0]->plmt_id;
                        Excel::import(new FmsOrder($request->aemp_id,$request->slgp_id,$request->depot_id,$site_id,$plmt_id), $request->file('order_file'));
                        DB::commit();
                        return redirect()->back()->with('success', 'Order Placed Successfully');
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    }
                }
                return back()->with('danger', ' File Not Found');
            }
            else{
                return redirect()->back()->with('danger', 'This site is not permitted to place order from web panel !!!');
            }
            
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function getFormat(){
        return Excel::download(new FmsOrder(0,0,0,0,0),'sv_order' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function getSiteId($site_code,$slgp_id){
        $site=DB::connection($this->db)->select("SELECT
                t1.site_id id,t1.plmt_id
                FROM tl_stcm t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_scnl t3 ON t2.scnl_id=t3.id
                WHERE t1.slgp_id=$slgp_id AND t2.site_code='$site_code' AND t3.chnl_id in (5,7)");
        return $site;
    }

 
}
