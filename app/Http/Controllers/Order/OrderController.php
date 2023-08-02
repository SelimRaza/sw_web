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
use App\MasterData\WebOrder;
use App\MasterData\WebOrder1;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use DateTime;

class OrderController extends Controller
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
            $acmp=DB::connection($this->db)->select("SELECT acmp_id,acmp_code,acmp_name FROM user_group_permission WHERE aemp_id=$this->aemp_id GROUP BY acmp_id,acmp_code,acmp_name ORDER BY acmp_name asc");
            
            return view('Order.create_order')->with("acmp", $acmp)->with("salesGroups", $acmp);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function getSRList($id){
        $data=DB::connection($this->db)->select("SELECT id,aemp_name,aemp_usnm FROM tm_aemp WHERE lfcl_id=1 AND slgp_id=$id ORDER BY aemp_name");
        return $data;
    }

    public function getRoutOfSelectedSR($id){
        $routs=DB::connection($this->db)->select("SELECT 
                    t2.id,t2.rout_name,t2.rout_code
                    FROM tl_rpln t1 
                    INNER JOIN tm_rout t2 ON t1.rout_id=t2.id
                    WHERE t1.aemp_id=$id AND t1.rpln_day=DAYNAME(curdate()) ORDER BY t2.rout_name ASC;");
        return $routs;
    }
    public function getDealarList($sr_id){
        $data=DB::connection($this->db)->select("SELECT 
                t2.id,t2.dlrm_name,t2.dlrm_code
                FROM `tl_srdi` t1
                INNER JOIN tm_dlrm t2 ON t1.dlrm_id=t2.id
                WHERE t1.aemp_id=$sr_id ORDER BY t2.dlrm_name ASC");
        return $data;
    }
    public function getRoutOutletListOfSelectedSR($rout_id){
        $outlet_list=DB::connection($this->db)->select("SELECT 
                        t2.id,t2.site_code,t2.site_name
                        FROM tl_rsmp t1
                        INNER JOIN tm_site t2 ON t1.site_id=t2.id
                        WHERE t1.rout_id=$rout_id AND t2.lfcl_id=1;");
        return $outlet_list;
    }

    public function getItemListOfSelectedOutlet($outlet_id,$slgp_id){
        $item_list=DB::connection($this->db)->select("SELECT 
                    t4.id,t4.amim_code,t4.amim_name
                    FROM tl_stcm t1
                    INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id
                    INNER JOIN tm_pldt t3 ON t2.id=t3.plmt_id
                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                    WHERE t1.slgp_id=$slgp_id AND t1.site_id=$outlet_id
                    GROUP BY t4.id,t4.amim_code,t4.amim_name");
        return $item_list;
    }
    public function getItemOrderFormat($site_id,$slgp_id){
        return Excel::download(new WebOrder(0,$slgp_id,0,$site_id,0,0),'order_item_list' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function storeOrderTemp(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('amim_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new WebOrder($request->acmp_id,$request->slgp_id,$request->rout_id,$request->site_id,$request->sr_id,$request->dlrm_id), $request->file('amim_file'));
                    DB::commit();
                    $data=DB::connection($this->db)->select("SELECT 
                            t2.id amim_id,t2.amim_name,t2.amim_code,t1.*
                            FROM `tbl_order_temp` t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            WHERE t1.sr_id=$request->sr_id ORDER BY t2.amim_name ASC");
                    return view("Order.order_discount_adjust")->with('data',$data)->with('permission', $this->userMenu);
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
    public function getItemFactor($slgp_id,$site_id,$amim_id){
        $data=DB::connection($this->db)->select("SELECT 
                t4.amim_duft,t3.pldt_tppr                   
                FROM tl_stcm t1
                INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id
                INNER JOIN tm_pldt t3 ON t2.id=t3.plmt_id
                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                WHERE t1.slgp_id=$slgp_id AND t1.site_id=$site_id AND t3.amim_id=$amim_id");
        return $data;
        

    }
    public function removeTempOrder($id){
        DB::connection($this->db)->select("DELETE FROM tbl_order_temp where id=$id");
    }
    public function getAvailableDiscountPromotion($id){
       $data=WebOrder1::on($this->db)->find($id);
       $pics=$data->amim_ctn*$data->amim_duft+$data->amim_pics;
       $ctn=$data->amim_ctn+($data->amim_pics/$data->amim_duft);
       $itm_tamnt=$pics*$data->amim_tppr;
       $default_disc='';
       $promotion='';
       $wb_ord= WebOrder1::on($this->db)->find($id);
        if($data){
            $default_disc=DB::connection($this->db)->select("SELECT 
                        t2.site_id,t4.dfdm_id,t4.dfim_disc
                        FROM tbl_order_temp t1
                        INNER JOIN tl_dfsm t2 ON t1.site_id=t2.site_id
                        INNER JOIN tm_dfdm t3 ON t2.dfdm_id=t3.id
                        INNER JOIN tm_dfim t4 ON t3.id=t4.dfdm_id AND t1.amim_id=t4.amim_id
                        WHERE t3.end_date >=curdate() AND t4.amim_id='$data->amim_id' AND t1.site_id='$data->site_id' AND t1.id=$id
                        GROUP  BY t2.site_id,t4.dfdm_id,t4.dfim_disc;");
            $wb_ord->dfdm_id=$default_disc?$default_disc[0]->dfdm_id:'';
            $wb_ord->dfdm_disc=$default_disc?$default_disc[0]->dfim_disc:'';
            $wb_ord->is_adjust=1;
            $wb_ord->save();
            $promotion=DB::connection($this->db)->select("SELECT * FROM promotion WHERE site_id='$data->site_id' AND sr_id='$data->sr_id' AND slgp_id='$data->slgp_id' ORDER BY prsb_fqty ASC ");

            if($promotion){
                foreach($promotion as $i=>$p){
                    if($p->prmr_qfct=='foc'){
                        if($p->prmr_qfon=='QTY'){
                            if($pics>=$p->prsb_fqty){
                                // $promotion_dtls[0]['type']='foc';
                                // $promotion_dtls[0]['fitm_id']=$p->fitm_id;
                                // $promotion_dtls[0]['fitm_name']=$p->fitm_name;
                                // $promotion_dtls[0]['qty']=$p->prsb_qnty;
                                $wb_ord->is_foc=1;
                                $wb_ord->fitm_id=$p->fitm_id;
                                $wb_ord->fitm_name=$p->fitm_name;
                                $wb_ord->fitm_qty=$p->prsb_qnty;
                                $wb_ord->prmr_id=$p->prmr_id;
                            }
                        }
                        if($p->prmr_qfon=='CTN'){
                            if($ctn>=$p->prsb_fqty){
                                $wb_ord->is_foc=1;
                                $wb_ord->fitm_id=$p->fitm_id;
                                $wb_ord->fitm_name=$p->fitm_name;
                                $wb_ord->fitm_qty=$p->prsb_qnty;
                                $wb_ord->prmr_id=$p->prmr_id;
                            }
                        }
                    }
                    else if($p->prmr_qfct=='discount'){
                        if($p->prmr_qfon=='QTY'){
                            if($pics>=$p->prsb_fqty){
                                $wb_ord->is_foc=0;
                                $wb_ord->prmr_type=$p->prmr_ditp;
                                $wb_ord->amount=$p->prsb_disc;
                                $wb_ord->prmr_id=$p->prmr_id;
                            }
                        }
                        if($p->prmr_qfon=='CTN'){
                            if($ctn>=$p->prsb_fqty){
                                $wb_ord->is_foc=0;
                                $wb_ord->prmr_type=$p->prmr_ditp;
                                $wb_ord->amount=$p->prsb_disc;
                                $wb_ord->prmr_id=$p->prmr_id;
                            }
                        }
                    }
                    else if($p->prmr_qfct=='value'){
                            if($itm_tamnt>=$p->prsb_famn){
                                $wb_ord->is_foc=0;
                                $wb_ord->prmr_type=$p->prmr_ditp;
                                $wb_ord->amount=$p->prsb_disc;
                                $wb_ord->prmr_id=$p->prmr_id;
                            }  
                    }
                }
                $wb_ord->save();
            }
        }
        $this->calculatePayablePrice($id);
        $data1=WebOrder1::on($this->db)->find($id);
        $data2=ItemMaster::on($this->db)->find($data->amim_id);
        return array(
           'data1'=> $data1,
           'data2'=> $data2
        );
       
       
    }
    public function promotionChoiceSetup($id,$text){
        $data=WebOrder1::on($this->db)->find($id);
        $data->choice=$text;
        $data->save();
    }
    function calculatePayablePrice($id){
        $data=WebOrder1::on($this->db)->find($id);
        $total_amnt=(($data->amim_duft*$data->amim_ctn)+$data->amim_pics)*$data->amim_tppr;

        $without_sp_prce=$total_amnt;
       
        if($data->sp_disc>0){
            if($data->is_percent==1){
                $without_sp_prce=$without_sp_prce-(($without_sp_prce*$data->sp_disc)/100);
            }else{
                $without_sp_prce=$without_sp_prce-$data->sp_disc;
            }
        }
        $dfdm_pay_prce=$without_sp_prce;
        $prmr_pay_prce=$without_sp_prce;
        if($data->dfdm_id !=''){
            $dfdm_pay_prce=$without_sp_prce-(($without_sp_prce*$data->dfdm_disc)/100);
        }
        if($data->prmr_id !='' && $data->is_foc==0){
            if($data->prmr_type=='percent'){
                $prmr_pay_prce=$without_sp_prce-(($without_sp_prce*$data->amount)/100);
            }else{
                $prmr_pay_prce=$without_sp_prce-$data->amount;
            }
        }
        $data->dfdm_pay_prce=$dfdm_pay_prce;
        $data->prmr_pay_prce=$prmr_pay_prce;
        $data->save();
        // $data1=WebOrder1::on($this->db)->find($id);
        // return $data1;



    }
    public function getFreeItemDetails($id){
        return ItemMaster::on($this->db)->find($id);
    }

    public function getTotalPayablePrice($order_no){
        $data=DB::connection($this->db)->select("SELECT 
                t2.amim_pexc,t2.amim_pvat,t1.is_adjust,
                IF (t1.choice='DFLT',t1.dfdm_pay_prce,t1.prmr_pay_prce) pay_price
                FROM `tbl_order_temp` t1
                INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                WHERE t1.tmp_ordm_ornm='$order_no';");
        $exec=0;
        $vat=0;
        $total=0;
        $place_order_flag=1;
        foreach($data as $dt){
            $exec1=($dt->pay_price*$dt->amim_pexc)/100;
            $vat+=($exec1+($dt->pay_price*$dt->amim_pvat))/100;
            $total+=$dt->pay_price;
            if($dt->is_adjust==0){
                $place_order_flag=0;
            }
        }
        return array(
            'exec'=>round($exec,4),
            'vat'=>round($vat,4),
            'total'=>round($total,4),
            'flag'=>$place_order_flag
        );
    }
    public function orderSave($order_no){
        
        DB::connection($this->db)->beginTransaction();
        try{
            $data=DB::connection($this->db)->select("SELECT * FROM tbl_order_temp WHERE tmp_ordm_ornm='$order_no' AND is_adjust=1");
            $order_temp_no=$data[0]->tmp_ordm_ornm;
            $order_calc=$this->getTotalPayablePrice($order_temp_no);
            $acmp_id=$data[0]->acmp_id;
            $slgp_id=$data[0]->slgp_id;
            $sr_id=$data[0]->sr_id;
            $site_id=$data[0]->site_id;
            $rout_id=$data[0]->rout_id;
            $ordm_date=$data[0]->tord_date;
            $dlrm_id=$data[0]->dlrm_id;
            $plmt_id=$data[0]->plmt_id;
            $datetime = new DateTime('tomorrow');
            $orderSequence = OrderSequence::on($this->db)->where(['aemp_id' => $sr_id, 'srsc_year' => date('y')])->first();
            if ($orderSequence == null) {
                $orderSequence = new OrderSequence();
                $orderSequence->setConnection($this->db);
                $orderSequence->aemp_id =$sr_id;
                $orderSequence->srsc_year = date('y');
                $orderSequence->srsc_ocnt = 0;
                $orderSequence->srsc_rcnt = 0;
                $orderSequence->srsc_ccnt = 0;
                $orderSequence->cont_id = $this->cont_id;
                $orderSequence->lfcl_id = 1;
                $orderSequence->aemp_iusr =$this->aemp_id;
                $orderSequence->aemp_eusr =$this->aemp_id;
                $orderSequence->save();
            }
            $employee = Employee::on($this->db)->where(['id' => $sr_id])->first();
            $order_id = "O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . date('y') . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);
            $order_calc=(object)$this->getTotalPayablePrice($data[0]->tmp_ordm_ornm);
            $orderMaster = new OrderMaster();
            $orderMaster->setConnection($this->db);
            $order_amount =$order_calc->exec+$order_calc->vat+$order_calc->total;
            $orderMaster->ordm_ornm = $order_id;
            $orderMaster->aemp_id = $sr_id;
            $orderMaster->slgp_id = $slgp_id;
            $orderMaster->dlrm_id = $dlrm_id;
            $orderMaster->acmp_id = $acmp_id;
            $orderMaster->site_id = $site_id;
            $orderMaster->rout_id = $rout_id;
            $orderMaster->odtp_id = 1;
            $orderMaster->mspm_id = 0;
            $orderMaster->ocrs_id = 0;
            $orderMaster->ordm_pono = '';
            $orderMaster->aemp_cusr = 0;
            $orderMaster->ordm_note = '';
            $orderMaster->ordm_date =$ordm_date;
            $orderMaster->ordm_time =date('Y-m-d H:i:s');
            // $orderMaster->ordm_time = date('Y-m-d H:i:s');
            $orderMaster->ordm_drdt =$datetime->format('Y-m-d');
            $orderMaster->ordm_dltm =$datetime->format('Y-m-d H:i:s');
            $orderMaster->geo_lat =0;
            $orderMaster->geo_lon =0;
            $orderMaster->ordm_dtne =0;
            $orderMaster->ordm_amnt = $order_amount;
            $orderMaster->ordm_icnt = sizeof($data);
            $orderMaster->plmt_id =$plmt_id;
            $orderMaster->cont_id =$this->cont_id;
            $orderMaster->lfcl_id =1;
            $orderMaster->aemp_iusr =$this->aemp_id;
            $orderMaster->aemp_eusr =$this->aemp_id;
            $orderMaster->save();
            $flag=1;
            foreach ($data as $orderLineData) {
                $qty=$orderLineData->amim_ctn*$orderLineData->amim_duft+$orderLineData->amim_pics;
                $sp_disc=0;
                if($orderLineData->sp_disc>0){
                    if($orderLineData->is_percent==1){
                        $sp_disc=($orderLineData->sp_disc*$orderLineData->amim_tppr*$qty)/100;
                    }
                    else{
                        $sp_disc=$orderLineData->sp_disc;
                    }
                }
                $orderLine = new OrderLine();
                $orderLine->setConnection($this->db);
                $orderLine->ordm_id = $orderMaster->id;
                $orderLine->ordm_ornm = $order_id;
                $orderLine->amim_id = $orderLineData->amim_id;
                $orderLine->ordd_qnty =$qty;
                $orderLine->ordd_inty =$qty;
                $orderLine->ordd_cqty = 0;
                $orderLine->ordd_dqty = 0;
                if($orderLineData->choice=='PROM'){
                    if($orderLineData->is_foc !=1){
                        if($orderLineData->prmr_type=='amount'){
                            $orderLine->ordd_opds =$orderLineData->amount;
                        }else{
                            $orderLine->ordd_opds =($orderLineData->amim_tppr*$qty*$orderLineData->amount)/100;
                        }
                    }else{
                            $foc = new OrderLine();
                            $foc->setConnection($this->db);
                            $foc->ordm_id = $orderMaster->id;
                            $foc->ordm_ornm = $order_id;
                            $foc->amim_id = $orderLineData->fitm_id;
                            $foc->ordd_qnty = $orderLineData->fitm_qty;
                            $foc->ordd_inty = $orderLineData->fitm_qty;
                            $foc->ordd_cqty = 0;
                            $foc->ordd_dqty = 0;
                            $foc->ordd_opds =0;
                            $foc->ordd_cpds = 0;
                            $foc->ordd_dpds = 0;
                            $fact=(object)$this->getFreeItemPrice($slgp_id,$site_id,$orderLineData->fitm_id);
                            $foc->ordd_duft = $fact->duft;
                            $foc->ordd_uprc = $orderLineData->amim_tppr;
                            $foc->ordd_runt = 1;
                            $foc->ordd_dunt = 1;
                            $foc->prom_id =$orderLineData->prmr_id;
                            $foc->ordd_spdi = 0;
                            $foc->ordd_spdo = 0;
                            $foc->ordd_spdc = 0;
                            $foc->ordd_spdd = 0;
                            $foc->ordd_dfdo =0;
                            $foc->ordd_dfdc = 0;
                            $foc->ordd_dfdd = 0;
                            $foc->ordd_excs =0;
                            $foc->ordd_ovat =0;
                            $foc->ordd_tdis = 0;
                            $foc->ordd_texc =0;
                            $foc->ordd_tvat =0;
                            $foc->ordd_oamt = $orderLineData->fitm_qty*$orderLineData->tppr;
                            $foc->ordd_ocat = 0;
                            $foc->ordd_odat = 0;
                            $foc->ordd_amnt = 0;
                            $foc->ordd_rqty = 0;
                            $foc->ordd_smpl =1;
                            $foc->lfcl_id = 1;
                            $foc->cont_id =$this->cont_id;
                            $foc->aemp_iusr =$this->aemp_id;
                            $foc->aemp_eusr =$this->aemp_id;
                            $foc->save();
                            $orderLine->ordd_opds=0;
                    }                   
                    $orderLine->ordd_dfdo = 0;
                    $orderLine->ordd_oamt = $orderLineData->prmr_pay_prce;
                }else{
                    $orderLine->ordd_dfdo=($orderLineData->amim_tppr*$qty*$orderLineData->dfdm_disc)/100;
                    $orderLine->ordd_opds=0;
                    $orderLine->ordd_oamt = $orderLineData->dfdm_pay_prce;
                }
                
                $orderLine->ordd_cpds = 0;
                $orderLine->ordd_dpds = 0;
                $orderLine->ordd_duft = $orderLineData->amim_duft;
                $orderLine->ordd_uprc = $orderLineData->amim_tppr;
                $orderLine->ordd_runt = 1;
                $orderLine->ordd_dunt = 1;
                $orderLine->prom_id = $orderLineData->prmr_id? $orderLineData->prmr_id : 0;
                $orderLine->ordd_spdi =$sp_disc;
                $orderLine->ordd_spdo =$sp_disc;
                $orderLine->ordd_spdc = 0;
                $orderLine->ordd_spdd = 0;
               
                $orderLine->ordd_dfdc = 0;
                $orderLine->ordd_dfdd = 0;
                $vatexc=$this->getFreeItemDetails($orderLineData->amim_id);
                $orderLine->ordd_excs =$vatexc->amim_pexc;
                $orderLine->ordd_ovat =$vatexc->amim_pvat;
                $orderLine->ordd_tdis = 0;
                $orderLine->ordd_texc = ($vatexc->amim_pexc*$orderLineData->amim_tppr*$qty)/100;
                $orderLine->ordd_tvat =($vatexc->amim_pvat*$orderLineData->amim_tppr*$qty)/100;
                
                $orderLine->ordd_ocat = 0;
                $orderLine->ordd_odat = 0;
                $orderLine->ordd_amnt = 0;
                $orderLine->ordd_rqty = 0;
                $orderLine->ordd_smpl =0;
                $orderLine->lfcl_id = 1;
                $orderLine->cont_id =$this->cont_id;
                $orderLine->aemp_iusr =$this->aemp_id;
                $orderLine->aemp_eusr =$this->aemp_id;
                $orderLine->save();
                if($orderLineData->is_sp_disc==1){
                    $flag=17;
                }

            }
            $orderMaster->lfcl_id =$flag;
            $orderMaster->save();
            $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
            $orderSequence->aemp_eusr =$this->aemp_id;
            $orderSequence->save();
            DB::connection($this->db)->commit();
            DB::connection($this->db)->select("DELETE FROM tbl_order_temp WHERE tmp_ordm_ornm='$order_no'");
            return 1;
        }catch(\Exception $e){
            DB::connection($this->db)->rollback();
            return $e;
        }
        
    }
    public function getFreeItemPrice($slgp_id,$site_id,$fitm_id){
        $data=DB::connection($this->db)->select("SELECT 
                t3.pldt_tppr ,t4.amim_duft                  
                FROM tl_stcm t1
                INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id
                INNER JOIN tm_pldt t3 ON t2.id=t3.plmt_id
                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                WHERE t1.slgp_id=$slgp_id AND t1.site_id=$site_id AND t3.amim_id=$amim_id");
        return array('tppr'=>$data[0]->pldt_tppr,'duft'=>$data[0]->amim_duft);
    }


    public function maintainOrderDepo(){
        $empId = $this->aemp_id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
        $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
        $dlrm_list = DB::connection($this->db)->select("SELECT id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
        return view('Order.Report.order_delivery_report',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list]);
    }
    public function getOrderDepoDetails(Request $request){
        $start_date=$request->start_date?$request->start_date:date("Y-m-d");
        $end_date=$request->end_date?$request->end_date:date("Y-m-d");
        $acmp_id=$request->acmp_id;
        $emp_id=$request->emp_id;
        $sp_id=$request->sp_id;
        $ordr_id=$request->ordr_id;
        $depot_id=$request->depot_id;
        $dirg_id=$request->dirg_id;
        $scnl_id=$request->scnl_id;
        $site_code=$request->site_code;
        $empId=$this->aemp_id;
        $condition='';
        if($acmp_id !=''){
            $condition .=" AND t1.acmp_id=$acmp_id";
        }
        if($emp_id !=''){
            $condition .=" AND t2.aemp_usnm='$emp_id'";
        }
        if($sp_id !=''){
            $condition .=" AND t2.aemp_mngr='$sp_id'";
        }
        if($ordr_id){
            $condition.=" AND t1.ordm_ornm='$ordr_id'";
        }
        if($depot_id){
            $condition.=" AND t4.id=$depot_id";
        }
        if($dirg_id){
            $zone_list=[];
            $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
            for($i=0;$i<count($dirg_zone_list);$i++){
                    $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
            }
            $condition.=" AND t2.zone_id IN (".implode(',',$zone_list).")";
        }
        if($scnl_id){
            $condition.=" AND t8.scnl_id =$scnl_id";
        }
        if($site_code){
            $condition.=" AND t8.site_code='$site_code'";
        }
        
        $data=DB::connection($this->db)->select("SELECT 
        t1.ordm_date,t4.id dlrm_id,t4.dlrm_name,t2.aemp_name sr_name,concat(t8.site_code,'-',t8.site_name)site_name,t1.ordm_ornm,t10.dirg_name,t11.slgp_name,t12.acmp_name,t7.id lfcl_id,t7.lfcl_name   
        FROM tt_ordm t1
        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
        INNER JOIN tm_lfcl t7 ON t1.lfcl_id=t7.id
        INNER JOIN tm_site t8 ON t1.site_id=t8.id
        INNER JOIN tm_zone t9 ON t2.zone_id=t9.id
        INNER JOIN tm_dirg t10 ON t9.dirg_id=t10.id
        INNER JOIN tm_slgp t11 ON t1.slgp_id=t11.id
        INNER JOIN tm_acmp t12 ON t11.acmp_id=t12.id
        WHERE t1.ordm_date between '$start_date' AND '$end_date' ".$condition." ORDER BY t1.ordm_date,t4.dlrm_name");
        return $data;
    }
    public function getOrderDepoPanel(){
        $empId = $this->aemp_id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
        $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
        $dlrm_list = DB::connection($this->db)->select("SELECT id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
        return view('Order.Report.depo_order',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list]);
    }
    public function getOrderDepoManagementDetails(Request $request){
        $start_date=$request->start_date?$request->start_date:date("Y-m-d");
        $end_date=$request->end_date?$request->end_date:date("Y-m-d");
        $acmp_id=$request->acmp_id;
        $emp_id=$request->emp_id;
        $sp_id=$request->sp_id;
        $ordr_id=$request->ordr_id;
        $depot_id=$request->depot_id;
        $dirg_id=$request->dirg_id;
        $scnl_id=$request->scnl_id;
        $site_code=$request->site_code;
        $empId=$this->aemp_id;
        $start_date=$request->start_date?$request->start_date:date("Y-m-d");
        $end_date=$request->end_date?$request->end_date:date("Y-m-d");
        $acmp_id=$request->acmp_id;
        $emp_id=$request->emp_id;
        $sp_id=$request->sp_id;
        $ordr_id=$request->ordr_id;
        $depot_id=$request->depot_id;
        $dirg_id=$request->dirg_id;
        $scnl_id=$request->scnl_id;
        $site_code=$request->site_code;
        $lfcl_id=$request->lfcl_id;
        $empId=$this->aemp_id;
        $condition='';
        if($acmp_id !=''){
            $condition .=" AND t1.acmp_id=$acmp_id";
        }
        if($emp_id !=''){
            $condition .=" AND t2.aemp_usnm='$emp_id'";
        }
        if($sp_id !=''){
            $condition .=" AND t2.aemp_mngr='$sp_id'";
        }
        if($ordr_id){
            $condition.=" AND t1.ordm_ornm='$ordr_id'";
        }
        if($depot_id){
            $condition.=" AND t4.id=$depot_id";
        }
        if($dirg_id){
            $zone_list=[];
            $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
            for($i=0;$i<count($dirg_zone_list);$i++){
                    $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
            }
            $condition.=" AND t2.zone_id IN (".implode(',',$zone_list).")";
        }
        if($scnl_id){
            $condition.=" AND t8.scnl_id =$scnl_id";
        }
        if($site_code){
            $condition.=" AND t8.site_code='$site_code'";
        }
        if($lfcl_id !=''){
            $condition.=" AND t1.lfcl_id=$lfcl_id";
        }
        $data=DB::connection($this->db)->select("SELECT 
        t1.id,
        t1.ordm_date,t4.id dlrm_id,
        t4.dlrm_name,t2.aemp_name sr_name,
        concat(t8.site_code,'-',t8.site_name)site_name,
        t1.ordm_ornm,t10.dirg_name,
        t11.slgp_name,t12.acmp_name,
        ifnull(t13.TRIP_NO,'')trip_no,
        t7.id lfcl_id,t7.lfcl_name   
        FROM tt_ordm t1
        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
        INNER JOIN tm_lfcl t7 ON t1.lfcl_id=t7.id
        INNER JOIN tm_site t8 ON t1.site_id=t8.id
        INNER JOIN tm_zone t9 ON t2.zone_id=t9.id
        INNER JOIN tm_dirg t10 ON t9.dirg_id=t10.id
        INNER JOIN tm_slgp t11 ON t1.slgp_id=t11.id
        INNER JOIN tm_acmp t12 ON t11.acmp_id=t12.id
        LEFT JOIN dm_trip_master t13 ON t1.ordm_ornm=t13.ORDM_ORNM
        WHERE t1.ordm_date between '$start_date' AND '$end_date' ".$condition."
        ORDER BY t1.ordm_date,t4.dlrm_name");
        return $data;
    }
    function getSingleOrderDetails($id){
        $data1=DB::connection($this->db)->select("SELECT 
                t5.dirg_name,
                t1.ordm_date,
                t10.cont_cncy,
                t7.site_name,
                t7.site_code,
                t1.ordm_date,
                t1.ordm_drdt,
                t1.ordm_amnt,
                t6.slgp_name,
                round(sum(t2.ordd_opds)+sum(t2.ordd_spdi)+sum(t2.ordd_dfdo),4) t_disc,
                sum(t2.ordd_texc) t_excise,
                t1.ordm_ornm,
                t3.aemp_name,t3.aemp_usnm,
                t8.aemp_name created_by,
                ifnull(t11.ocrs_name,'N/A')ocrs_name,
                t1.lfcl_id,
                t9.lfcl_name,
                t12.oult_code,
                t12.oult_name
                FROM tt_ordm t1
                INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                INNER JOIN tm_zone t4 ON t3.zone_id=t4.id
                INNER JOIN tm_dirg t5 ON t4.dirg_id=t5.id
                INNER JOIN tm_slgp t6 ON t3.slgp_id=t6.id
                INNER JOIN tm_site t7 ON t1.site_id=t7.id
                INNER JOIN tm_aemp t8 ON t1.aemp_iusr=t8.id
                INNER JOIN tm_lfcl t9 ON t1.lfcl_id=t9.id
                INNER JOIN tm_cont t10 ON t1.cont_id=t10.id
                LEFT JOIN tm_ocrs t11 ON t1.ocrs_id=t11.id
                INNER JOIN tm_oult t12 ON t7.outl_id=t12.id
                Where t1.id=$id
                GROUP BY t5.dirg_name,
                t1.ordm_date,
                t10.cont_cncy,
                t7.site_name,
                t7.site_code,
                t1.ordm_date,
                t1.ordm_drdt,
                t1.ordm_amnt,
                t1.ordm_ornm,
                t3.aemp_name,t3.aemp_usnm,
                t8.aemp_name,
                t1.lfcl_id,
                t9.lfcl_name,t12.oult_code,
                t12.oult_name
                ");
        $ord_details=DB::connection($this->db)->select("SELECT 
                    t3.amim_code,t3.amim_name,t4.amim_duft,round(t4.pldt_tppr*t4.amim_duft,2) ctn_price,
                    t2.ordd_dfdo,t2.ordd_spdi,t2.ordd_opds,t2.ordd_inty,t2.ordd_texc,t2.ordd_oamt ,t2.ordd_tvat
                    FROM tt_ordm t1
                    INNER JOIN tt_ordd t2 ON  t1.id=t2.ordm_id
                    INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                    INNER JOIN tm_pldt t4 ON t1.plmt_id=t4.plmt_id AND t3.id=t4.amim_id
                    Where t1.id=$id ORDER BY t3.amim_code");

        $free_item = DB::connection($this->db)->select("SELECT 
                t4.amim_code,t4.amim_name,t1.ordd_duft,t1.ordd_inty
                FROM tt_ordd t1
                INNER JOIN tm_amim t4 ON t1.amim_id=t4.id
                WHERE t1.ordm_id=$id AND t1.ordd_smpl=1 AND t1.ordd_oamt=0
                GROUP BY t4.amim_code,t4.amim_name,t1.ordd_duft,t1.ordd_inty");
        return view("Order.Report.single_depo_order",['data'=>$data1,'free_item'=>$free_item,'ord_details'=>$ord_details]);
    }
    public function changeOrderLifeCycle($id,$status){
        $new_lfcl_id=$status;
        if($status==1){
            $new_lfcl_id=21;
        }
        else if($status==21){
            $new_lfcl_id=1;
        }
        DB::connection($this->db)->select("Update tt_ordm SET lfcl_id=$new_lfcl_id WHERE id=$id");
        return 1;
    }
}
