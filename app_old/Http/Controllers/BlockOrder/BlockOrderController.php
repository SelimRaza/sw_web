<?php

namespace App\Http\Controllers\BlockOrder;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */

use App\BusinessObject\BlockHistory;
use App\BusinessObject\BlockReleaseLog;
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockOrderController extends Controller
{
    private $access_key = 'BlockOrderController';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function maintainBlock()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $dlrm=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC");
            $chnl=DB::connection($this->db)->select("Select id,chnl_code,chnl_name FROM tm_chnl WHERE lfcl_id=1 ORDER BY chnl_code ASC");
            $sv_list=DB::connection($this->db)->select("Select id,aemp_usnm,aemp_name FROM tm_aemp WHERE role_id>=2 ORDER BY aemp_usnm ASC");
            return view('blockOrder.maintain_order',['dlrm_list'=>$dlrm,'chnl_list'=>$chnl,'sv_list'=>$sv_list])->with('permission', $this->userMenu)->with('acmp_list',$acmp);
        } else {
            return view('theme.access_limit');
        }

    }
    public function filterMaintainBlock(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id=$request->emp_id;
        $slgp_id=$request->slgp_id;
        $block_type=$request->block_type;
        $site_code=$request->site_code;
        $acmp_id=$request->acmp_id;
        $start_date=$request->start_date?$request->start_date:date('Y-m-d');
        $end_date=$request->end_date?$request->end_date:date('Y-m-d');
        $chnl_id=$request->chnl_id;
        $sv_id=$request->sv_id;
        $ordm_ornm=$request->ordm_ornm;
        $query='';

        if($emp_id !=''){
            $query.=" AND t2.aemp_usnm='$emp_id'";
        }
        if($slgp_id !=''){
            $query.=" AND t1.slgp_id='$slgp_id'";
        }
        if($site_code !=''){
            $query.=" AND t4.site_code='$site_code'";
        }
        if($acmp_id !=''){
            $query.=" AND t1.acmp_id=$acmp_id";
        }
        if($block_type !=''){
            $query.=" AND t1.lfcl_id=$block_type";
        }
        if($ordm_ornm !=''){
            $query.=" AND t1.ordm_ornm='$ordm_ornm'";
        }if($chnl_id !=''){
            $query.=" AND t8.chnl_id=$chnl_id";
        }
        if($sv_id !=''){
            $query.=" AND t2.aemp_mngr=$sv_id";
        }
        
        // $data = DB::connection($this->db)->select("SELECT
        //         t1.id        AS so_id,
        //         t1.ordm_ornm    order_id,
        //         t1.ordm_amnt AS order_amount,
        //         t2.aemp_name AS emp_name,
        //         t2.aemp_usnm AS user_name,
        //         t3.slgp_name As slgp_name,
        //         t4.site_name AS site_name,
        //         t1.site_id,
        //         t4.site_code AS site_code,
        //         t1.ordm_date    order_date,
        //         t1.ordm_time AS order_date_time,
        //         t5.lfcl_name AS status_name,
        //         t1.lfcl_id   AS status_id,
        //         'Order'      AS order_type,
        //         t1.cont_id      AS cont_id
        //         FROM tt_ordm t1
        //         INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        //         INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
        //         INNER JOIN tm_site t4 ON t1.site_id=t4.id
        //         INNER JOIN tm_lfcl t5 ON t1.lfcl_id=t5.id
        //         INNER JOIN user_group_permission t6 ON t3.id=t6.slgp_id
        //         WHERE  t6.aemp_id='$this->aemp_id' AND t1.ordm_date between '$start_date' AND '$end_date' ".$query." Order by t1.id desc  ");
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $data=DB::connection($this->db)->select("SELECT
                t1.id        AS so_id,
                t1.ordm_ornm    order_id,
                t1.ordm_amnt AS order_amount,
                t2.aemp_name AS emp_name,
                t2.aemp_usnm AS user_name,
                t3.slgp_name As slgp_name,
                t7.site_name AS site_name,
                t1.site_id,
                t7.site_code AS site_code,
                t1.ordm_date    order_date,
                t1.ordm_time AS order_date_time,
                t5.lfcl_name AS status_name,
                t1.lfcl_id   AS status_id,
                'Order'      AS order_type,
                t1.cont_id      AS cont_id,
                t4.stcm_limt,
                t4.stcm_days,
                (CASE WHEN t10.prev_lfcl_id=17 THEN t10.created_at ELSE '' END)'special',
                (CASE WHEN t10.prev_lfcl_id=9 THEN t10.created_at ELSE '' END)'cdb',
                (CASE WHEN t10.prev_lfcl_id=14 THEN t10.created_at ELSE '' END)'odb',
                concat(t9.dlrm_code,'-',t9.dlrm_name)depo_name,
                concat(t11.aemp_usnm,'-',t11.aemp_name)sv_name
                FROM tt_ordm t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                LEFT JOIN tl_stcm t4 ON t1.site_id=t4.site_id AND t1.slgp_id=t4.slgp_id
                INNER JOIN tm_lfcl t5 ON t1.lfcl_id=t5.id
                INNER JOIN user_group_permission t6 ON t3.id=t6.slgp_id
                INNER JOIN tm_dlrm t9 ON t1.dlrm_id=t9.id
                INNER JOIN tm_site t7 ON t1.site_id=t7.id  
                INNER JOIN tm_scnl t8 ON t7.scnl_id=t8.id
                LEFT JOIN tl_block_release_log t10 ON t1.ordm_ornm=t10.ordm_ornm
                INNER JOIN tm_aemp t11 ON t2.aemp_mngr=t11.id
                WHERE  t6.aemp_id='$this->aemp_id' AND t1.ordm_date between '$start_date' AND '$end_date' ".$query."
                GROUP BY 
                t1.id,
                t1.ordm_ornm,
                t1.ordm_amnt,
                t2.aemp_name,
                t2.aemp_usnm,
                t3.slgp_name,
                t7.site_name,
                t1.site_id,
                t7.site_code,
                t1.ordm_date,
                t1.ordm_time,
                t5.lfcl_name,
                t1.lfcl_id,
                t1.cont_id,  
                t4.stcm_limt,
                t4.stcm_days,t9.dlrm_code,t9.dlrm_name,t11.aemp_usnm,t11.aemp_name
                Order by t1.id DESC;");
        return $data;

    }
    public function specialRelease($id)
    {
        if ($this->userMenu->wsmu_delt) {
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
            LEFT JOIN tt_spbm AS t8 ON t5.id = t8.aemp_id AND t8.spbm_year = year('$date') AND t8.spbm_mnth = month('$date')
            WHERE t1.id = $id"))->first();
                        $orderLine = DB::connection($this->db)->select("SELECT
            t2.id                  AS id,
            t2.prom_id                  AS promo_ref,
            t2.ordd_uprc * t2.ordd_duft AS Rate,
            t2.amim_id                  AS Product_id,
            t3.amim_name                AS Product_Name,
            t2.ordd_qnty                AS Product_Quantity,  
            t2.ordd_spdi                AS spDis,
            t2.ordd_spdo                AS Discount,
            t1.ordm_amnt                AS Total_Item_Price,
            t2.ordd_duft                AS ctn_size,
            t2.ordd_dfdo                AS default_discount,
            t2.ordd_opds                AS promo_discount
            FROM tt_ordm AS t1
            INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
            INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
            WHERE t1.id = $id;");

            $cancelReason = DB::connection($this->db)->select("SELECT id,ocrs_name
                            FROM tm_ocrs;;");

            return view('blockOrder.special_release')->with('orderMaster', $orderMaster)->with('orderLine', $orderLine)->with('cancelReason', $cancelReason)->with('permission', $this->userMenu);

        } else {
            return view('theme.access_limit');
        }

    }
    public function specialReleaseAction(Request $request, $id)
    {
        // dd($request);
        if ($this->userMenu->wsmu_delt) {
            DB::connection($this->db)->beginTransaction();
            $msg = "";
            try {
                $block_log=new BlockReleaseLog();
                $block_log->setConnection($this->db);
                $block_log->release_date=date('Y-m-d');
                $block_log->prev_lfcl_id=17;
                $block_log->release_amnt =array_sum($request->discountAmount);
                $block_log->source ='W';
                $block_log->aemp_iusr = $this->currentUser->employee()->id;
                if ($request->submit=='release') {
                    $ouSiteMapping = CompanySiteBalance::on($this->db)->where(['site_id' => $request->site_id, 'acmp_id' => $request->ou_id])->first();
                    $specialBudgetMaster = SpecialBudgetMaster::on($this->db)->where(['aemp_id' =>$this->aemp_id,'spbm_mnth' => date('m'), 'spbm_year' => date('Y')])->first();
                    if ($specialBudgetMaster != null && $ouSiteMapping != null && $specialBudgetMaster->spbm_amnt >= array_sum($request->discountAmount)) {
                        $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                        $orderMaster->lfcl_id = 1;
                        $block_log->lfcl_id = 1;
                        if ($ouSiteMapping->optp_id == 2) {
                            if ($ouSiteMapping->stcm_limt - $ouSiteMapping->stcm_ordm - $ouSiteMapping->stcm_duea < $request->net_amount) {
                                $orderMaster->lfcl_id = 9;
                                $block_log->lfcl_id = 9;
                            }
                            if ($ouSiteMapping->stcm_odue > 0) {
                                $orderMaster->lfcl_id = 14;
                                $block_log->lfcl_id = 14;
                            }
                            else{
                                
                            }
                        }
                        if (isset($request->discountLineId)) {
                            foreach ($request->discountLineId as $index => $lineId) {
                                DB::connection($this->db)->table('tt_ordd')->where(['id' => $lineId])->update(['ordd_spdc' => $request->discountAmount[$index],'attr1' =>'-']);
                            }
                        }
                        if($orderMaster->lfcl_id==1){
                            $ornm=$orderMaster->ordm_ornm;
                            $dm_trip=DB::connection($this->db)->select("Select DELV_AMNT FROM dm_trip_master WHERE ORDM_ORNM='$ornm'");
                            $del_amount=$dm_trip?$dm_trip[0]->DELV_AMNT:0;
                            if($del_amount>0){
                                $orderMaster->lfcl_id=11;
                            }
                        }
                        $specialBudgetLine = new SpecialBudgetLine();
                        $specialBudgetLine->setConnection($this->db);
                        $specialBudgetLine->spbm_id = $specialBudgetMaster->id;
                        $specialBudgetLine->spbd_type = 'Approved';
                        $specialBudgetLine->ordm_ornm = $orderMaster->ordm_ornm;
                        $specialBudgetLine->spbd_amnt = array_sum($request->discountAmount);
                        $specialBudgetLine->trnt_id = 2;
                        $specialBudgetLine->lfcl_id = 17;
                        $specialBudgetLine->cont_id = $this->currentUser->country()->id;
                        $specialBudgetLine->aemp_iusr = $this->currentUser->employee()->id;
                        $specialBudgetLine->aemp_eusr = $this->currentUser->employee()->id;
                        $specialBudgetLine->save();
                        $blockHistory = new BlockHistory();
                        $blockHistory->setConnection($this->db);
                        $blockHistory->ordm_id = $orderMaster->id;
                        $blockHistory->blck_rtme = date("Y-m-d H:i:s");
                        $blockHistory->lfcl_id = 17;
                        $blockHistory->cont_id = $this->currentUser->country()->id;
                        $blockHistory->aemp_iusr = $this->currentUser->employee()->id;
                        $blockHistory->aemp_eusr = $this->currentUser->employee()->id;
                        $blockHistory->save();
                        $specialBudgetMaster->spbm_amnt = $specialBudgetMaster->spbm_amnt - array_sum($request->discountAmount);
                        $specialBudgetMaster->spbm_avil = $specialBudgetMaster->spbm_avil + array_sum($request->discountAmount);
                        $specialBudgetMaster->save();
                        $orderMaster->save();

                        $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                        $block_log->save();
                        $msg = "Release Successful";
                    } else {
                        return redirect()->back()->with('danger', 'Special Budget not Avaialble');
                    }
                }
                if ($request->submit=='cancel') {
                    $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                    $orderMaster->ocrs_id = $request->reject_id;
                    $orderMaster->lfcl_id = 18;
                    $orderMaster->save();

                    $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                    $block_log->lfcl_id =18;
                    $block_log->save();
                    $msg = "Cancel Successful";
                }

                DB::connection($this->db)->commit();

                return redirect()->back()->with('success', $msg);
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', 'Something went wrong!!!');
            }
        } else {
            return view('theme.access_limit');
        }

    }
    public function overDueRelease($id)
    {
        if ($this->userMenu->wsmu_delt) {
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
            LOWER(t7.optp_name) AS payMode
            FROM tt_ordm AS t1
            INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
            INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
            INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
            INNER JOIN tl_stcm AS t6 ON t1.acmp_id = t6.acmp_id AND t1.site_id = t6.site_id
            INNER JOIN tm_optp AS t7 ON t6.optp_id = t7.id
            WHERE t1.id = $id"))->first();

                        $orderLine = DB::connection($this->db)->select("SELECT
            t2.id                  AS id,
            t2.prom_id                  AS promo_ref,
            t2.ordd_uprc * t2.ordd_duft AS Rate,
            t2.amim_id                  AS Product_id,
            t3.amim_name                AS Product_Name,
            t2.ordd_qnty                AS Product_Quantity,
            t2.ordd_spdo                AS Discount,
            t1.ordm_amnt                AS Total_Item_Price,
            t2.ordd_duft                AS ctn_size,
            t2.ordd_dfdo                AS default_discount,
            t2.ordd_opds                AS promo_discount
            FROM tt_ordm AS t1
            INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
            INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
            WHERE t1.id = $id;");

            $cancelReason = DB::connection($this->db)->select("SELECT id,ocrs_name
            FROM tm_ocrs;");

            return view('blockOrder.over_due_release')->with('orderMaster', $orderMaster)->with('orderLine', $orderLine)->with('cancelReason', $cancelReason)->with('permission', $this->userMenu);

        } else {
            return view('theme.access_limit');
        }

    }
    public function overDueReleaseAction(Request $request, $id)
    {
        // dd($request);
        if ($this->userMenu->wsmu_delt) {
            DB::connection($this->db)->beginTransaction();
            $msg = "";
            try {
                $block_log=new BlockReleaseLog();
                $block_log->setConnection($this->db);
                $block_log->release_date=date('Y-m-d');
                $block_log->prev_lfcl_id=14;
                $block_log->release_amnt =0;
                $block_log->source ='W';
                $block_log->aemp_iusr = $this->currentUser->employee()->id;
                if ($request->submit=='release') {
                    $ouSiteMapping = CompanySiteBalance::on($this->db)->where(['site_id' => $request->site_id, 'acmp_id' => $request->ou_id])->first();
                    $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                    $orderMaster->lfcl_id = 1;
                    $block_log->lfcl_id=1;
                    if ($ouSiteMapping->optp_id == 2) {
                        if ($ouSiteMapping->stcm_limt - $ouSiteMapping->stcm_ordm - $ouSiteMapping->stcm_duea < $request->net_amount) {
                            $orderMaster->lfcl_id = 9;
                            $block_log->lfcl_id=9;
                        }

                    }
                    if($orderMaster->lfcl_id==1){
                        $ornm=$orderMaster->ordm_ornm;
                        $dm_trip=DB::connection($this->db)->select("Select DELV_AMNT FROM dm_trip_master WHERE ORDM_ORNM='$ornm'");
                        $del_amount=$dm_trip?$dm_trip[0]->DELV_AMNT:0;
                        if($del_amount>0){
                            $orderMaster->lfcl_id=11;
                        }
                    }
                    $blockHistory = new BlockHistory();
                    $blockHistory->setConnection($this->db);
                    $blockHistory->ordm_id = $orderMaster->id;
                    $blockHistory->blck_rtme = date("Y-m-d H:i:s");
                    $blockHistory->lfcl_id = 14;
                    $blockHistory->cont_id = $this->currentUser->country()->id;
                    $blockHistory->aemp_iusr = $this->currentUser->employee()->id;
                    $blockHistory->aemp_eusr = $this->currentUser->employee()->id;
                    $blockHistory->save();
                    $orderMaster->save();

                    $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                    $block_log->save();
                    $msg = "Release Successful";

                }
                if ($request->submit=='cancel') {
                    $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                    $orderMaster->ocrs_id = $request->reject_id;
                    $orderMaster->lfcl_id = 18;
                    $orderMaster->save();

                    $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                    $block_log->lfcl_id =18;
                    $block_log->save();
                    $msg = "Cancel Successful";
                }

                DB::connection($this->db)->commit();

                return redirect()->back()->with('success', $msg);
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return $e;
            }
        } else {
            return view('theme.access_limit');
        }

    }
    public function creditRelease($id)
    {
        if ($this->userMenu->wsmu_delt) {
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
            LOWER(t7.optp_name) AS payMode
            FROM tt_ordm AS t1
            INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
            INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
            INNER JOIN tm_aemp AS t5 ON t4.aemp_mngr = t5.id
            INNER JOIN tl_stcm AS t6 ON t1.acmp_id = t6.acmp_id AND t1.site_id = t6.site_id
            INNER JOIN tm_optp AS t7 ON t6.optp_id = t7.id
            WHERE t1.id = $id"))->first();

                        $orderLine = DB::connection($this->db)->select("SELECT
            t2.id                  AS id,
            t2.prom_id                  AS promo_ref,
            t2.ordd_uprc * t2.ordd_duft AS Rate,
            t2.amim_id                  AS Product_id,
            t3.amim_name                AS Product_Name,
            t2.ordd_qnty                AS Product_Quantity,
            t2.ordd_spdo                AS Discount,
            t1.ordm_amnt                AS Total_Item_Price,
            t2.ordd_duft                AS ctn_size,
            t2.ordd_dfdo                AS default_discount,
            t2.ordd_opds                AS promo_discount
            FROM tt_ordm AS t1
            INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
            INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
            WHERE t1.id = $id;");
            $cancelReason = DB::connection($this->db)->select("SELECT id,ocrs_name
                                FROM tm_ocrs;");

            return view('blockOrder.credit_release')->with('orderMaster', $orderMaster)->with('orderLine', $orderLine)->with('cancelReason', $cancelReason)->with('permission', $this->userMenu);

        } else {
            return view('theme.access_limit');
        }

    }
    public function creditReleaseAction(Request $request, $id)
    {
        // dd($request);
        if ($this->userMenu->wsmu_delt) {
            DB::connection($this->db)->beginTransaction();
            $msg = "";
            try {
                $block_log=new BlockReleaseLog();
                $block_log->setConnection($this->db);
                $block_log->release_date=date('Y-m-d');
                $block_log->prev_lfcl_id=9;
                $block_log->release_amnt =0;
                $block_log->source ='W';
                $block_log->aemp_iusr = $this->currentUser->employee()->id;
                if ($request->submit=='release') {
                    $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                    $orderMaster->lfcl_id = 1;
                    $block_log->lfcl_id=1;
                    $blockHistory = new BlockHistory();
                    $blockHistory->setConnection($this->db);
                    $blockHistory->ordm_id = $orderMaster->id;
                    $blockHistory->blck_rtme = date("Y-m-d H:i:s");
                    $blockHistory->lfcl_id = 9;
                    $blockHistory->cont_id = $this->currentUser->country()->id;
                    $blockHistory->aemp_iusr = $this->currentUser->employee()->id;
                    $blockHistory->aemp_eusr = $this->currentUser->employee()->id;
                    if($orderMaster->lfcl_id==1){
                        $ornm=$orderMaster->ordm_ornm;
                        $dm_trip=DB::connection($this->db)->select("Select DELV_AMNT FROM dm_trip_master WHERE ORDM_ORNM='$ornm'");
                        $del_amount=$dm_trip?$dm_trip[0]->DELV_AMNT:0;
                        if($del_amount>0){
                            $orderMaster->lfcl_id=11;
                        }
                    }
                    $blockHistory->save();
                    $orderMaster->save();

                    $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                    $block_log->save();
                    $msg = "Release Successful";

                }
                if ($request->submit=='cancel') {
                    $orderMaster = OrderMaster::on($this->db)->where(['id' => $request->so_id])->first();
                    $orderMaster->ocrs_id = $request->reject_id;
                    $orderMaster->lfcl_id = 18;
                    $orderMaster->save();

                    $block_log->ordm_ornm =$orderMaster->ordm_ornm;
                    $block_log->lfcl_id =18;
                    $block_log->save();

                    $msg = "Cancel Successful";
                }

                DB::connection($this->db)->commit();

                return redirect()->back()->with('success', $msg);
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return $e;
            }
        } else {
            return view('theme.access_limit');
        }

    }

}