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
use App\BusinessObject\OrderLineSV;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderMasterSV;
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
                WHERE t1.attr8=99 AND t1.ordm_date >curdate()-INTERVAL 30 DAY
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
                    $cusr_id=Auth::user()->employee()->id;
                    DB::beginTransaction();
                    try {                   
                        $site_id=$site[0]->id;
                        $plmt_id=$site[0]->plmt_id;
                        Excel::import(new FmsOrder($request->aemp_id,$request->slgp_id,$request->depot_id,$site_id,$plmt_id), $request->file('order_file'));
                        DB::commit();
                        $sr_id=$request->aemp_id;
                        $ordm=OrderMasterSV::on($this->db)->where(['site_id'=>$site_id,'aemp_id'=>$sr_id,'aemp_iusr'=>$cusr_id])->first();
                        return redirect()->route('getPreview', ['id' => $ordm->ordm_ornm]);
                       // return redirect()->back()->with('success', 'Order Placed Successfully');
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
        if(Auth::user()->country()->id==2){
            return DB::connection($this->db)->select("SELECT * FROM tm_site WHERE site_code='8800296494'");
        }
        $site=DB::connection($this->db)->select("SELECT
                t1.site_id id,t1.plmt_id
                FROM tl_stcm t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_scnl t3 ON t2.scnl_id=t3.id
                WHERE t1.slgp_id=$slgp_id AND t2.site_code='$site_code'");
                //AND t3.chnl_id in (5,7)
        return $site;
    }
    public function getPreview($ordm_id){

        $date = date('Y-m-d');
        $orderMaster = collect(DB::connection($this->db)->select("SELECT
        t1.id               AS id,
        t1.ordm_ornm        AS Order_ID,
        t1.acmp_id          AS ou_id,
        t3.site_adrs        AS Address,
        t1.ordm_date        AS order_date,
        t3.site_name        AS Outlet_Name,
        t1.ordm_amnt        AS total_price,
        t3.site_code        AS customer_number,
        t1.site_id          AS Site_ID,
        t4.aemp_usnm        AS SR_ID,
        concat(t4.aemp_name,'-',t4.aemp_usnm)        AS sr_name,
        t1.lfcl_id          AS order_status,
        concat(t5.aemp_name,'-',t5.aemp_usnm)               AS manager_name,
        t5.id               AS manager_code,
        LOWER(t7.optp_name) AS payMode,
        t8.spbm_amnt        AS budget
        FROM tt_ordm_sv AS t1
        INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
        INNER JOIN tl_stcm AS t6 ON t1.acmp_id = t6.acmp_id AND t1.site_id = t6.site_id
        INNER JOIN tm_optp AS t7 ON t6.optp_id = t7.id
        LEFT JOIN tt_spbm AS t8 ON t5.id = t8.aemp_id 
        WHERE t1.ordm_ornm='$ordm_id' "))->first();
                    $orderLine = DB::connection($this->db)->select("SELECT
        t2.id                  AS id,
        t2.prom_id                  AS promo_ref,
        t2.ordd_uprc * t2.ordd_duft AS Rate,
        t3.amim_code                  AS Product_id,
        t3.amim_name                AS Product_Name,
        t2.ordd_qnty                AS Product_Quantity,  
        t2.ordd_spdi                AS spDis,
        t2.ordd_spdo                AS Discount,
        t2.ordd_oamt                AS Total_Item_Price,
        t2.ordd_duft                AS ctn_size,
        t2.ordd_dfdo                AS default_discount,
        t2.ordd_opds                AS promo_discount
        FROM tt_ordm_sv AS t1
        INNER JOIN tt_ordd_sv AS t2 ON t1.id = t2.ordm_id
        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
        WHERE t1.ordm_ornm='$ordm_id'");

        $cancelReason = DB::connection($this->db)->select("SELECT id,ocrs_name
                        FROM tm_ocrs;;");

        return view('Order.FmsOrder.preview')->with('orderMaster', $orderMaster)->with('orderLine', $orderLine)->with('cancelReason', $cancelReason)->with('permission', $this->userMenu)->with('ordm_id',$ordm_id);
    }
    public function getOrderPrint($ordm_id){

        $date = date('Y-m-d');
        $orderMaster = collect(DB::connection($this->db)->select("SELECT
        t1.id               AS id,
        t1.ordm_ornm        AS Order_ID,
        t1.acmp_id          AS ou_id,
        t3.site_adrs        AS Address,
        t1.ordm_date        AS order_date,
        t3.site_name        AS Outlet_Name,
        t1.ordm_amnt        AS total_price,
        t3.site_code        AS customer_number,
        t1.site_id          AS Site_ID,
        t4.aemp_usnm        AS SR_ID,
        concat(t4.aemp_name,'-',t4.aemp_usnm)        AS sr_name,
        t1.lfcl_id          AS order_status,
        concat(t5.aemp_name,'-',t5.aemp_usnm)               AS manager_name,
        t5.id               AS manager_code,
        LOWER(t7.optp_name) AS payMode,
        t8.spbm_amnt        AS budget
        FROM tt_ordm AS t1
        INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
        INNER JOIN tl_stcm AS t6 ON t1.acmp_id = t6.acmp_id AND t1.site_id = t6.site_id
        INNER JOIN tm_optp AS t7 ON t6.optp_id = t7.id
        LEFT JOIN tt_spbm AS t8 ON t5.id = t8.aemp_id 
        WHERE t1.ordm_ornm='$ordm_id' "))->first();
                    $orderLine = DB::connection($this->db)->select("SELECT
        t2.id                  AS id,
        t2.prom_id                  AS promo_ref,
        t2.ordd_uprc * t2.ordd_duft AS Rate,
        t3.amim_code                  AS Product_id,
        t3.amim_name                AS Product_Name,
        t2.ordd_qnty                AS Product_Quantity,  
        t2.ordd_spdi                AS spDis,
        t2.ordd_spdo                AS Discount,
        t2.ordd_oamt                AS Total_Item_Price,
        t2.ordd_duft                AS ctn_size,
        t2.ordd_dfdo                AS default_discount,
        t2.ordd_opds                AS promo_discount
        FROM tt_ordm AS t1
        INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
        WHERE t1.ordm_ornm='$ordm_id'");

        $cancelReason = DB::connection($this->db)->select("SELECT id,ocrs_name
                        FROM tm_ocrs;;");

        return view('Order.FmsOrder.print')->with('orderMaster', $orderMaster)->with('orderLine', $orderLine)->with('cancelReason', $cancelReason)->with('permission', $this->userMenu)->with('ordm_id',$ordm_id);
    }
    
   public  function confirmOrder($ordm_id){
        DB::beginTransaction();
        try{
            $orderMasterSvData = OrderMasterSV::on($this->db)->where('ordm_ornm', $ordm_id)->first();
            $orderMaster = new OrderMaster;
            $orderMaster->setConnection($this->db);
            $orderMaster->ordm_ornm = $orderMasterSvData->ordm_ornm;
            $orderMaster->aemp_id = $orderMasterSvData->aemp_id;
            $orderMaster->acmp_id = $orderMasterSvData->acmp_id;
            $orderMaster->slgp_id = $orderMasterSvData->slgp_id;
            $orderMaster->dlrm_id = $orderMasterSvData->dlrm_id;
            $orderMaster->site_id = $orderMasterSvData->site_id;
            $orderMaster->rout_id = $orderMasterSvData->rout_id;
            $orderMaster->odtp_id = $orderMasterSvData->odtp_id;
            $orderMaster->ordm_date = $orderMasterSvData->ordm_date;
            $orderMaster->ordm_time = $orderMasterSvData->ordm_time;
            $orderMaster->ordm_drdt = $orderMasterSvData->ordm_drdt;
            $orderMaster->ordm_dltm = $orderMasterSvData->ordm_dltm;
            $orderMaster->geo_lat = $orderMasterSvData->geo_lat;
            $orderMaster->geo_lon = $orderMasterSvData->geo_lon;
            $orderMaster->ordm_dtne = $orderMasterSvData->ordm_dtne;
            $orderMaster->ordm_amnt = $orderMasterSvData->ordm_amnt;
            $orderMaster->ordm_icnt = $orderMasterSvData->ordm_icnt;
            $orderMaster->plmt_id = $orderMasterSvData->plmt_id;
            $orderMaster->ocrs_id = $orderMasterSvData->ocrs_id;
            $orderMaster->ordm_pono = $orderMasterSvData->ordm_pono;
            $orderMaster->aemp_cusr = $orderMasterSvData->aemp_cusr;
            $orderMaster->ordm_note = $orderMasterSvData->ordm_note;
            $orderMaster->mspm_id = $orderMasterSvData->mspm_id;
            $orderMaster->cont_id = $orderMasterSvData->cont_id;
            $orderMaster->lfcl_id = $orderMasterSvData->lfcl_id;
            $orderMaster->aemp_iusr = $orderMasterSvData->aemp_iusr;
            $orderMaster->aemp_eusr = $orderMasterSvData->aemp_eusr;
            $orderMaster->var = $orderMasterSvData->var;
            $orderMaster->attr1 = $orderMasterSvData->attr1;
            $orderMaster->attr2 = $orderMasterSvData->attr2;
            $orderMaster->attr3 = $orderMasterSvData->attr3;
            $orderMaster->attr4 = $orderMasterSvData->attr4;
            $orderMaster->attr5 = $orderMasterSvData->attr5;
            $orderMaster->attr6 = $orderMasterSvData->attr6;
            $orderMaster->attr7 = $orderMasterSvData->attr7;
            $orderMaster->attr8 = $orderMasterSvData->attr8;
            $orderMaster->attr9 = $orderMasterSvData->attr9;
            $orderMaster->attr10 = 20;
            $orderMaster->attr11 = date('Y-m-d h:i:s');
            $orderMaster->attr12 = date('Y-m-d h:i:s');;
            $orderMaster->save();
            // DB::connection($this->db)->select("INSERT INTO `tt_ordd`(`ordm_id`, `ordm_ornm`, `amim_id`, `ordd_inty`, `ordd_qnty`, `ordd_cqty`, `ordd_dqty`, `ordd_rqty`, `ordd_opds`,
            // `ordd_cpds`, `ordd_dpds`, `ordd_spdi`, `ordd_spdo`, `ordd_spdc`, `ordd_spdd`, `ordd_dfdo`, `ordd_dfdc`, `ordd_dfdd`, `ordd_duft`, `ordd_uprc`, `ordd_runt`, `ordd_dunt`,
            // `ordd_smpl`, `prom_id`, `ordd_oamt`, `ordd_ocat`, `ordd_odat`, `ordd_excs`, `ordd_ovat`, `ordd_tdis`, `ordd_texc`, `ordd_tvat`, `ordd_amnt`, `cont_id`, `lfcl_id`, `aemp_iusr`,
            // `aemp_eusr`, `created_at`, `updated_at`, `var`, `attr1`, `attr2`, `attr3`, `attr4`, `attr5`, `attr6`, `attr7`, `attr8`, `attr9`, `attr10`, `attr11`, `attr12`) 
            //         SELECT '$orderMaster->id', `ordm_ornm`, `amim_id`, `ordd_inty`, `ordd_qnty`, `ordd_cqty`, `ordd_dqty`, `ordd_rqty`, `ordd_opds`, 
            //         `ordd_cpds`, `ordd_dpds`, `ordd_spdi`, `ordd_spdo`, `ordd_spdc`, `ordd_spdd`, `ordd_dfdo`, `ordd_dfdc`, `ordd_dfdd`, `ordd_duft`, `ordd_uprc`, `ordd_runt`, `ordd_dunt`, 
            //         `ordd_smpl`, `prom_id`, `ordd_oamt`, `ordd_ocat`, `ordd_odat`, `ordd_excs`, `ordd_ovat`, `ordd_tdis`, `ordd_texc`, `ordd_tvat`, `ordd_amnt`, `cont_id`, `lfcl_id`, `aemp_iusr`, 
            //         `aemp_eusr`, `created_at`, `updated_at`, `var`, `attr1`, `attr2`, `attr3`, `attr4`, `attr5`, `attr6`, `attr7`, `attr8`, `attr9`, `attr10`, `attr11`, `attr12` FROM tt_ordd_sv WHERE ordm_ornm='$ordm_id' ");
            $orderLineSVs =OrderLineSV::on($this->db)->where('ordm_ornm', $ordm_id)->get();
            foreach ($orderLineSVs as $orderLineSV) {
                // Create a new instance of OrderLine
                $orderLine = new OrderLine;
                $orderLine->setConnection($this->db);
                // Set the values for the columns from OrderLineSV
                $orderLine->ordm_id = $orderMaster->id;
                $orderLine->ordm_ornm = $orderLineSV->ordm_ornm;
                $orderLine->amim_id = $orderLineSV->amim_id;
                $orderLine->ordd_inty = $orderLineSV->ordd_inty;
                $orderLine->ordd_qnty = $orderLineSV->ordd_qnty;
                $orderLine->ordd_cqty = $orderLineSV->ordd_cqty;
                $orderLine->ordd_dqty = $orderLineSV->ordd_dqty;
                $orderLine->ordd_rqty = $orderLineSV->ordd_rqty;
                $orderLine->ordd_opds = $orderLineSV->ordd_opds;
                $orderLine->ordd_cpds = $orderLineSV->ordd_cpds;
                $orderLine->ordd_dpds = $orderLineSV->ordd_dpds;
                $orderLine->ordd_spdi = $orderLineSV->ordd_spdi;
                $orderLine->ordd_spdo = $orderLineSV->ordd_spdo;
                $orderLine->ordd_spdc = $orderLineSV->ordd_spdc;
                $orderLine->ordd_spdd = $orderLineSV->ordd_spdd;
                $orderLine->ordd_dfdo = $orderLineSV->ordd_dfdo;
                $orderLine->ordd_dfdc = $orderLineSV->ordd_dfdc;
                $orderLine->ordd_dfdd = $orderLineSV->ordd_dfdd;
                $orderLine->ordd_duft = $orderLineSV->ordd_duft;
                $orderLine->ordd_uprc = $orderLineSV->ordd_uprc;
                $orderLine->ordd_runt = $orderLineSV->ordd_runt;
                $orderLine->ordd_dunt = $orderLineSV->ordd_dunt;
                $orderLine->ordd_smpl = $orderLineSV->ordd_smpl;
                $orderLine->prom_id = $orderLineSV->prom_id;
                $orderLine->ordd_oamt = $orderLineSV->ordd_oamt;
                $orderLine->ordd_ocat = $orderLineSV->ordd_ocat;
                $orderLine->ordd_odat = $orderLineSV->ordd_odat;
                $orderLine->ordd_excs = $orderLineSV->ordd_excs;
                $orderLine->ordd_ovat = $orderLineSV->ordd_ovat;
                $orderLine->ordd_tdis = $orderLineSV->ordd_tdis;
                $orderLine->ordd_texc = $orderLineSV->ordd_texc;
                $orderLine->ordd_tvat = $orderLineSV->ordd_tvat;
                $orderLine->ordd_amnt = $orderLineSV->ordd_amnt;
                $orderLine->cont_id  = $orderLineSV->cont_id;
                $orderLine->lfcl_id   = $orderLineSV->lfcl_id ;
                $orderLine->aemp_iusr    = $orderLineSV->aemp_iusr;
                $orderLine->aemp_eusr     = $orderLineSV->aemp_eusr ;
                $orderLine->save();
            }
            
            DB::connection($this->db)->select("DELETE FROM tt_ordd_sv WHERE ordm_ornm='$ordm_id'");
            $orderMasterSvData->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Order Placed Successfully');
        }
        catch(\Exception $e){
            DB::rollback();
            return $e->getMessage();
            return redirect()->back()->with('danger', 'Something went wrong!!!');
        }
        
        
    }
   public  function cancelOrder($ordm_id){
        DB::connection($this->db)->select("DELETE FROM tt_ordd_sv WHERE ordm_ornm='$ordm_id'");
        DB::connection($this->db)->select("DELETE FROM tt_ordm_sv WHERE ordm_ornm='$ordm_id'");
        return redirect()->back()->with('danger', 'Order cancelled');
    }

 
}
