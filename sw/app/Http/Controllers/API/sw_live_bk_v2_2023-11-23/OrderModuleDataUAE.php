<?php

namespace App\Http\Controllers\API\v2;

use App\BusinessObject\Attendance;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\TripOrder;
use App\BusinessObject\LoadLine;
use App\BusinessObject\LoadMaster;
use App\BusinessObject\VanLoadTrip;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\TempSite;
use Faker\Provider\DateTime;
use App\BusinessObject\SpecialBudgetMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\ReturnLine;
use Carbon\Carbon;


class OrderModuleDataUAE extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function orderSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
                $productive = 0;
                $line_id = 0;
                $msg = "";
                if ($request->Order_Type == 'Productive') {

                    $otyp_id = $request->otyp_id ?? 1;

                    $orderSyncLog = OrderSyncLog::on($db_conn)->where(['oslg_moid' => $request->Order_Unique_ID])->first(); //tl_oslg
                    if ($orderSyncLog == null) {
                        $orderSequence = OrderSequence::on($db_conn)->where(['aemp_id' => $request->SR_ID, 'srsc_year' => date('y')])->first(); //tm_srsc
                        if ($orderSequence == null) {
                            $orderSequence = new OrderSequence();
                            $orderSequence->setConnection($db_conn);
                            $orderSequence->aemp_id = $request->SR_ID;
                            $orderSequence->srsc_year = date('y');
                            $orderSequence->srsc_ocnt = 0;
                            $orderSequence->srsc_rcnt = 0;
                            $orderSequence->srsc_ccnt = 0;
                            $orderSequence->cont_id = $request->country_id;
                            $orderSequence->lfcl_id = 1;
                            $orderSequence->aemp_iusr = $request->up_emp_id;
                            $orderSequence->aemp_eusr = $request->up_emp_id;
                            $orderSequence->save();
                        }
                        $employee = Employee::on($db_conn)->where(['id' => $request->SR_ID])->first();
                        $orderMaster = new OrderMaster();
                        $orderMaster->setConnection($db_conn);
                        $orderLines = json_decode($request->line_data);
                        $order_id = "I" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '_' . $orderSequence->srsc_year . '_' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);
                        //$order_id = $employee->aemp_usnm . str_pad("I", 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);                   
                        $order_amount = array_sum(array_column($orderLines, 'total_price'));
                        $orderMaster->ordm_ornm = $order_id;
                        $orderMaster->aemp_id = $request->SR_ID;
                        $orderMaster->slgp_id = $request->Group_ID;
                        $orderMaster->dlrm_id = $request->D_ID;
                        $orderMaster->acmp_id = $request->ou_id;
                        $orderMaster->site_id = $request->Outlet_ID;
                        $orderMaster->rout_id = $request->Route_ID;
                        $orderMaster->odtp_id = 1;
                        $orderMaster->mspm_id = $request->Site_MSP_ID;
                        $orderMaster->ocrs_id = 0;
                        $orderMaster->ordm_pono = '';
                        $orderMaster->aemp_cusr = 0;
                        $orderMaster->ordm_note = '';
                        $orderMaster->ordm_date = $request->Date;
                        $orderMaster->ordm_time = $request->Order_time;
                        // $orderMaster->ordm_time = date('Y-m-d H:i:s');
                        $orderMaster->ordm_drdt = $request->Delivery_Date;
                        $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                        $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                        $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                        $orderMaster->ordm_dtne = $request->Outlet_Distance;
                        $orderMaster->ordm_amnt = $order_amount;
                        $orderMaster->ordm_icnt = sizeof($orderLines);
                        $orderMaster->plmt_id = $request->Item_Price_List;
                        $orderMaster->cont_id = $request->country_id;
                        $orderMaster->lfcl_id = $request->Order_Status;
                        $orderMaster->aemp_iusr = $request->up_emp_id;
                        $orderMaster->aemp_eusr = $request->up_emp_id;
                        $orderMaster->attr8 = $otyp_id;
                        $orderMaster->save();
                        foreach ($orderLines as $orderLineData) {

                            $ifFree = 0;
                            if ($orderLineData->P_T_Price == 0) {
                                $ifFree = 1;
                            }
                            $orderLine = new OrderLine();
                            $orderLine->setConnection($db_conn);
                            $orderLine->ordm_id = $orderMaster->id;
                            $orderLine->ordm_ornm = $order_id;
                            $orderLine->amim_id = $orderLineData->P_ID;
                            $orderLine->ordd_qnty = $orderLineData->P_Qty;
                            $orderLine->ordd_inty = $orderLineData->P_Qty;
                            $orderLine->ordd_cqty = 0;
                            $orderLine->ordd_dqty = 0;
                            $orderLine->ordd_opds = $orderLineData->Promo_Discount == "" ? 0 : $orderLineData->Promo_Discount;
                            $orderLine->ordd_cpds = 0;
                            $orderLine->ordd_dpds = 0;
                            $orderLine->ordd_duft = $orderLineData->Item_Factor;
                            $orderLine->ordd_uprc = $orderLineData->Rate;
                            $orderLine->ordd_runt = 1;
                            $orderLine->ordd_dunt = 1;
                            $orderLine->prom_id = isset($orderLineData->promo_ref) ? $orderLineData->promo_ref : 0;
                            $orderLine->ordd_spdi = isset($orderLineData->sp_Discount) ? $orderLineData->sp_Discount : 0;
                            $orderLine->ordd_spdo = isset($orderLineData->sp_Discount) ? $orderLineData->sp_Discount : 0;
                            $orderLine->ordd_spdc = 0;
                            $orderLine->ordd_spdd = 0;
                            $orderLine->ordd_dfdo = isset($orderLineData->df_Discount) ? $orderLineData->df_Discount : 0;
                            $orderLine->ordd_dfdc = 0;
                            $orderLine->ordd_dfdd = 0;
                            $orderLine->ordd_excs = isset($orderLineData->excise_percent) ? $orderLineData->excise_percent : 0;
                            $orderLine->ordd_ovat = isset($orderLineData->vat_percent) ? $orderLineData->vat_percent : 0;
                            $orderLine->ordd_tdis = 0;
                            $orderLine->ordd_texc = isset($orderLineData->excise) ? $orderLineData->excise : 0;
                            $orderLine->ordd_tvat = isset($orderLineData->vat) ? $orderLineData->vat : 0;
                            $orderLine->ordd_oamt = $orderLineData->total_price;
                            $orderLine->ordd_ocat = 0;
                            $orderLine->ordd_odat = 0;
                            $orderLine->ordd_amnt = 0;
                            $orderLine->ordd_rqty = 0;
                            $orderLine->ordd_smpl = $ifFree;
                            $orderLine->lfcl_id = 1;
                            $orderLine->cont_id = $request->country_id;
                            $orderLine->aemp_iusr = $request->up_emp_id;
                            $orderLine->aemp_eusr = $request->up_emp_id;
                            $orderLine->save();
                        }
                        $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();

                        $orderSyncLog = new OrderSyncLog();
                        $orderSyncLog->setConnection($db_conn);
                        $orderSyncLog->oslg_moid = $request->Order_Unique_ID;
                        $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                        $orderSyncLog->oslg_orid = $orderMaster->id;
                        $orderSyncLog->oslg_type = $request->Order_Type;

                        $orderSyncLog->lfcl_id = 1;
                        $orderSyncLog->cont_id = $request->country_id;
                        $orderSyncLog->aemp_iusr = $request->up_emp_id;
                        $orderSyncLog->aemp_eusr = $request->up_emp_id;
                        $orderSyncLog->save();
                        $productive = 1;
                        $line_id = $request->ID;
                        $msg = "Success";

                        if ($request->country_id == 2 || $request->country_id == 5) {
                        } else {
                            $tp = round($order_amount, 3);
                            $sql = "SET autocommit=0;";
                            $sql_order = "update `tl_stcm`t1 SET t1.`stcm_ordm`=t1.`stcm_ordm`+$tp
                                WHERE t1.acmp_id= $request->ou_id AND t1.`site_id`=$request->Outlet_ID AND t1.`slgp_id`=$request->Group_ID AND t1.`optp_id`=2 ;";

                            DB::connection($db_conn)->unprepared($sql);
                            DB::connection($db_conn)->unprepared($sql_order);

                        }
                    } else {
                        $msg = "Duplicate";
                        // return response()->json(array('column_id' => $request->ID, 'message' => $msg));
                    }

                } elseif ($request->Order_Type == 'Non_Productive') {
                    $nonProductive = new NonProductiveOutlet();
                    $nonProductive->setConnection($db_conn);
                    $nonProductive->aemp_id = $request->SR_ID;
                    $nonProductive->slgp_id = $request->Group_ID;
                    $nonProductive->dlrm_id = $request->D_ID;
                    $nonProductive->site_id = $request->Outlet_ID;
                    $nonProductive->rout_id = $request->Route_ID;
                    $nonProductive->npro_date = $request->Date;
                    $nonProductive->npro_time = $request->Order_time;
                    $nonProductive->nopr_id = $request->Exception_id;
                    $nonProductive->npro_note = $request->Exception_note;
                    $nonProductive->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $nonProductive->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                    $nonProductive->npro_dtne = $request->Outlet_Distance;
                    $nonProductive->cont_id = $request->country_id;
                    $nonProductive->lfcl_id = 1;
                    $nonProductive->aemp_iusr = $request->up_emp_id;
                    $nonProductive->aemp_eusr = $request->up_emp_id;
                    $nonProductive->save();
                    $productive = 0;
                    $line_id = $request->ID;
                    $msg = "Visited";
                }

                $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $request->Outlet_ID, 'ssvh_date' => $request->Date, 'aemp_id' => $request->SR_ID])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($db_conn);
                    $siteVisit->ssvh_date = $request->Date;
                    // $siteVisit->ssvh_date = date('Y-m-d');
                    $siteVisit->aemp_id = $request->SR_ID;
                    $siteVisit->site_id = $request->Outlet_ID;
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->attr1 = $request->Order_time;
                    // $siteVisit->attr2 =$request->Route_ID;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                } else if ($productive == 1) {
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->attr1 = $request->Order_time;
                    // $siteVisit->attr2 =$request->Route_ID;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($db_conn)->commit();
                // return array('column_id' => $line_id);
                return response()->json(array('column_id' => $line_id, 'message' => $msg));
            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                // return $e;
                return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
                //throw $e;
            }

        }

    }

    public
        function saveReturnOrder_dev(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $msg = "";
                $ret_id = 0;
                $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->return_id])->first();
                if ($orderSyncLog == null) {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $returnMaster = new ReturnMaster();
                    $returnMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("G" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '_' . $orderSequence->srsc_year . '_' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    $order_amount = array_sum(array_column($orderLines, 'total_amount'));
                    $returnMaster->rtan_rtnm = $order_id;
                    $returnMaster->aemp_id = $request->emp_id;
                    $returnMaster->acmp_id = $request->ou_id;

                    // $returnMaster->slgp_id = $request->group_id;
                    $returnMaster->slgp_id = $request->group_id == "" ? 1 : $request->group_id;

                    $returnMaster->dlrm_id = $request->depo_id;
                    $returnMaster->site_id = $request->site_id;
                    $returnMaster->rout_id = $request->route_id;
                    $returnMaster->rttp_id = 1;
                    //  $returnMaster->plmt_id = 1;
                    // $returnMaster->plmt_id = $request->price_list;
                    $returnMaster->plmt_id = $request->price_list == "" ? 3 : $request->price_list;
                    $returnMaster->rtan_date = $request->date;
                    $returnMaster->rtan_time = date('Y-m-d H:i:s');
                    $returnMaster->rtan_drdt = $request->date;
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    $returnMaster->geo_lat = $request->lat;
                    $returnMaster->geo_lon = $request->lon;
                    $returnMaster->rtan_amnt = $order_amount;
                    $returnMaster->rtan_icnt = sizeof($orderLines);
                    $returnMaster->rtan_pono = $request->party_grv_number;
                    $returnMaster->rtan_podt = $request->party_grv_date;
                    $returnMaster->rtan_note = '';
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = 1;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        // $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no == "" ? 0 : $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_dqty = 0;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount;
                        $returnLine->rtdd_damt = 0;
                        $returnLine->rtdd_edat = date('Y-m-d');
                        $returnLine->rtdd_note = 0;
                        $returnLine->rtdd_rato = $orderLineData->discount_ratio;
                        $returnLine->rtdd_excs = $orderLineData->gst_percent;
                        $returnLine->rtdd_ovat = $orderLineData->vat_percent;
                        $returnLine->rtdd_tdis = 0;
                        $returnLine->rtdd_texc = $orderLineData->gst;
                        $returnLine->rtdd_tvat = $orderLineData->vat;
                        $returnLine->rtdd_amnt = 0;
                        $returnLine->dprt_id = $orderLineData->reason_id;
                        // $returnLine->rtdd_ptyp = $orderLineData->retrun_type;//1 non-saleb=saleablele, 2
                        $returnLine->rtdd_ptyp = $orderLineData->retrun_type == "retrun_type" ? 1 : $orderLineData->retrun_type;
                        $returnLine->lfcl_id = 1;
                        $returnLine->cont_id = $request->country_id;
                        $returnLine->aemp_iusr = $request->up_emp_id;
                        $returnLine->aemp_eusr = $request->up_emp_id;
                        $returnLine->save();
                    }
                    $orderSequence->srsc_rcnt = $orderSequence->srsc_rcnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->return_id;
                    $orderSyncLog->oslg_ornm = $returnMaster->rtan_rtnm;
                    $orderSyncLog->oslg_orid = $returnMaster->id;
                    $orderSyncLog->oslg_type = 'grv';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->date, 'aemp_id' => $request->emp_id])->first();
                    if ($siteVisit == null) {
                        $siteVisit = new SiteVisited();
                        $siteVisit->setConnection($country->cont_conn);
                        $siteVisit->ssvh_date = $request->date;
                        $siteVisit->aemp_id = $request->emp_id;
                        $siteVisit->site_id = $request->site_id;
                        $siteVisit->SSVH_ISPD = 0;
                        $siteVisit->cont_id = $request->country_id;
                        $siteVisit->lfcl_id = 1;
                        $siteVisit->aemp_iusr = $request->up_emp_id;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    }

                    $msg = "Success";
                    $ret_id = $request->ID;
                    // return array('column_id' => $request->ID,'message'=>$msg);
                } else {
                    $msg = "Duplicate";
                    $ret_id = 0;
                }

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $ret_id, 'message' => $msg);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
                // return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
            }
        }
    }

    public
        function saveReturnOrder(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $msg = "";
                $ret_id = 0;
                $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->return_id])->first();
                if ($orderSyncLog == null) {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $returnMaster = new ReturnMaster();
                    $returnMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("G" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '_' . $orderSequence->srsc_year . '_' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    //$order_id = strtoupper($employee->aemp_usnm . str_pad("G", 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    $order_amount = array_sum(array_column($orderLines, 'total_amount'));
                    $returnMaster->rtan_rtnm = $order_id;
                    $returnMaster->aemp_id = $request->emp_id;
                    $returnMaster->acmp_id = $request->ou_id;
                    $returnMaster->slgp_id = 1;
                    $returnMaster->dlrm_id = $request->depo_id;
                    $returnMaster->site_id = $request->site_id;
                    $returnMaster->rout_id = $request->route_id;
                    $returnMaster->rttp_id = 1;
                    $returnMaster->plmt_id = 1;
                    $returnMaster->rtan_date = $request->date;
                    $returnMaster->rtan_time = date('Y-m-d H:i:s');
                    $returnMaster->rtan_drdt = $request->date;
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    $returnMaster->geo_lat = $request->lat;
                    $returnMaster->geo_lon = $request->lon;
                    $returnMaster->rtan_amnt = $order_amount;
                    $returnMaster->rtan_icnt = sizeof($orderLines);
                    $returnMaster->rtan_pono = $request->party_grv_number;
                    $returnMaster->rtan_podt = $request->party_grv_date;
                    $returnMaster->rtan_note = '';
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = 1;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_dqty = 0;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount;
                        $returnLine->rtdd_damt = 0;
                        $returnLine->rtdd_edat = date('Y-m-d');
                        $returnLine->rtdd_note = 0;
                        $returnLine->rtdd_rato = $orderLineData->discount_ratio;
                        $returnLine->rtdd_excs = $orderLineData->gst;
                        $returnLine->rtdd_ovat = $orderLineData->vat;
                        $returnLine->rtdd_tdis = 0;
                        $returnLine->rtdd_texc = 0;
                        $returnLine->rtdd_tvat = 0;
                        $returnLine->rtdd_amnt = 0;
                        $returnLine->dprt_id = $orderLineData->reason_id;
                        $returnLine->lfcl_id = 1;
                        $returnLine->cont_id = $request->country_id;
                        $returnLine->aemp_iusr = $request->up_emp_id;
                        $returnLine->aemp_eusr = $request->up_emp_id;
                        $returnLine->save();
                    }
                    $orderSequence->srsc_rcnt = $orderSequence->srsc_rcnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->return_id;
                    $orderSyncLog->oslg_ornm = $returnMaster->rtan_rtnm;
                    $orderSyncLog->oslg_orid = $returnMaster->id;
                    $orderSyncLog->oslg_type = 'grv';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->date, 'aemp_id' => $request->emp_id])->first();
                    if ($siteVisit == null) {
                        $siteVisit = new SiteVisited();
                        $siteVisit->setConnection($country->cont_conn);
                        $siteVisit->ssvh_date = $request->date;
                        $siteVisit->aemp_id = $request->emp_id;
                        $siteVisit->site_id = $request->site_id;
                        $siteVisit->SSVH_ISPD = 0;
                        $siteVisit->cont_id = $request->country_id;
                        $siteVisit->lfcl_id = 1;
                        $siteVisit->aemp_iusr = $request->up_emp_id;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    }

                    $msg = "Success";
                    $ret_id = $request->ID;
                    // return array('column_id' => $request->ID,'message'=>$msg);
                } else {
                    $msg = "Duplicate";
                    $ret_id = 0;
                }

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $ret_id, 'message' => $msg);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                // return $e;
                return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
            }
        }
    }

    public
        function saveReturnOrderWithReturnType(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $msg = "";
                $ret_id = 0;
                $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->return_id])->first();
                if ($orderSyncLog == null) {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $returnMaster = new ReturnMaster();
                    $returnMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("G" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '_' . $orderSequence->srsc_year . '_' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    //$order_id = strtoupper($employee->aemp_usnm . str_pad("G", 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    $order_amount = array_sum(array_column($orderLines, 'total_amount'));
                    $returnMaster->rtan_rtnm = $order_id;
                    $returnMaster->aemp_id = $request->emp_id;
                    $returnMaster->acmp_id = $request->ou_id;

                    // $returnMaster->slgp_id = $request->group_id;
                    $returnMaster->slgp_id = $request->group_id == "" ? 1 : $request->group_id;

                    $returnMaster->dlrm_id = $request->depo_id;
                    $returnMaster->site_id = $request->site_id;
                    $returnMaster->rout_id = $request->route_id;
                    $returnMaster->rttp_id = 1;
                    //  $returnMaster->plmt_id = 1;
                    // $returnMaster->plmt_id = $request->price_list;
                    $returnMaster->plmt_id = $request->price_list == "" ? 3 : $request->price_list;
                    $returnMaster->rtan_date = $request->date;
                    $returnMaster->rtan_time = date('Y-m-d H:i:s');
                    $returnMaster->rtan_drdt = $request->date;
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    $returnMaster->geo_lat = $request->lat;
                    $returnMaster->geo_lon = $request->lon;
                    $returnMaster->rtan_amnt = $order_amount;
                    $returnMaster->rtan_icnt = sizeof($orderLines);
                    $returnMaster->rtan_pono = $request->party_grv_number;
                    $returnMaster->rtan_podt = $request->party_grv_date;
                    $returnMaster->rtan_note = '';
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = 1;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        // $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no == "" ? 0 : $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_dqty = 0;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount;
                        $returnLine->rtdd_damt = 0;
                        $returnLine->rtdd_edat = date('Y-m-d');
                        $returnLine->rtdd_note = 0;
                        $returnLine->rtdd_rato = $orderLineData->discount_ratio;
                        $returnLine->rtdd_excs = $orderLineData->gst_percent;
                        $returnLine->rtdd_ovat = $orderLineData->vat_percent;
                        $returnLine->rtdd_tdis = 0;
                        $returnLine->rtdd_texc = $orderLineData->gst;
                        $returnLine->rtdd_tvat = $orderLineData->vat;
                        $returnLine->rtdd_amnt = 0;
                        $returnLine->dprt_id = $orderLineData->reason_id;
                        // $returnLine->rtdd_ptyp = $orderLineData->retrun_type;//1 non-saleb=saleablele, 2
                        $returnLine->rtdd_ptyp = $orderLineData->retrun_type == "retrun_type" ? 1 : $orderLineData->retrun_type;
                        $returnLine->lfcl_id = 1;
                        $returnLine->cont_id = $request->country_id;
                        $returnLine->rtdd_batc = $orderLineData->rtdd_batc ?? '-';
                        $returnLine->rtdd_mfdt = $orderLineData->rtdd_mfdt ?? null;
                        $returnLine->rtdd_exdt = $orderLineData->rtdd_exdt ?? null;
                        $returnLine->rtdd_imag = $orderLineData->rtdd_imag ?? '-';
                        $returnLine->aemp_iusr = $request->up_emp_id;
                        $returnLine->aemp_eusr = $request->up_emp_id;
                        $returnLine->save();
                    }
                    $orderSequence->srsc_rcnt = $orderSequence->srsc_rcnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->return_id;
                    $orderSyncLog->oslg_ornm = $returnMaster->rtan_rtnm;
                    $orderSyncLog->oslg_orid = $returnMaster->id;
                    $orderSyncLog->oslg_type = 'grv';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->date, 'aemp_id' => $request->emp_id])->first();
                    if ($siteVisit == null) {
                        $siteVisit = new SiteVisited();
                        $siteVisit->setConnection($country->cont_conn);
                        $siteVisit->ssvh_date = $request->date;
                        $siteVisit->aemp_id = $request->emp_id;
                        $siteVisit->site_id = $request->site_id;
                        $siteVisit->SSVH_ISPD = 0;
                        $siteVisit->cont_id = $request->country_id;
                        $siteVisit->lfcl_id = 1;
                        $siteVisit->aemp_iusr = $request->up_emp_id;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    }

                    $msg = "Success";
                    $ret_id = $request->ID;
                    // return array('column_id' => $request->ID,'message'=>$msg);
                } else {
                    $msg = "Duplicate";
                    $ret_id = 0;
                }

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $ret_id, 'message' => $msg);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
                // return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
            }
        }
    }

    public function get_com_wh($trip_code, $db_conn)
    {
        $trip_m = DB::connection($db_conn)->select("
       SELECT t2.DEPOT_ID AS WH_ID,t2.company_id AS company_code 
       FROM dm_trip t2 
       WHERE t2.TRIP_NO='$trip_code'      
       ");

        return $trip_m;
    }

    public function get_com_wh_invoice($invoice_code, $db_conn)
    {
        $invoice_m = DB::connection($db_conn)->select("
        SELECT `WH_ID`AS WH_ID,`ACMP_CODE`AS company_code
        FROM `dm_trip_master` WHERE `ORDM_ORNM`='$invoice_code';      
       ");

        return $invoice_m;
    }

    public function Re_Generated_Invoice_TripWise(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $trip_code = $request->trip_code;

        if ($db_conn != '') {
            try {
                DB::connection($db_conn)->beginTransaction();
                $com_wh = $this->get_com_wh($trip_code, $db_conn);
                $comp = $com_wh ? $com_wh[0]->company_code : '';
                $wh = $com_wh ? $com_wh[0]->WH_ID : '';

                $trip_wise_invoice = DB::connection($db_conn)->select("
            SELECT `ORDM_ORNM` FROM `dm_trip_master` 
            WHERE `TRIP_NO`='$trip_code'
            AND `DELIVERY_STATUS`=11 
            AND `IBS_INVOICE`='N';    
            ");

                foreach ($trip_wise_invoice as $orderLineData) {
                    $ORDM_ORNM = $orderLineData->ORDM_ORNM;

                    if ($IBS_INVOICE1 = $this->get_IBS_INVOICE($comp, $wh, $ORDM_ORNM, $request->country_id)) {
                        $IBS_INVOICE = str_replace('"', '', $IBS_INVOICE1);
                    }
                    $length = strlen($IBS_INVOICE);
                    if ($length > 17) {
                        return array(
                            'success' => 0,
                            //  'column_id' => $request->id,
                            'message' => 'Vat Server Problem !!!',
                        );
                    }
                    $ret = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $ORDM_ORNM])
                        ->update(['IBS_INVOICE' => $IBS_INVOICE]);
                    $ret2 = DB::connection($db_conn)->table('dm_trip_detail')->where(['ORDM_ORNM' => $ORDM_ORNM])
                        ->update(['IBS_INVOICE' => $IBS_INVOICE]);
                }
                DB::connection($db_conn)->commit();
                return "Updated Successful";
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public function Re_Generated_Invoice_OrderWise(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $invoice_code = $request->invoice_code;

        if ($db_conn != '') {
            try {
                DB::connection($db_conn)->beginTransaction();
                $com_wh = $this->get_com_wh_invoice($invoice_code, $db_conn);
                $comp = $com_wh ? $com_wh[0]->company_code : '';
                $wh = $com_wh ? $com_wh[0]->WH_ID : '';

                if ($IBS_INVOICE1 = $this->get_IBS_INVOICE_new($comp, $wh, $invoice_code, $request->country_id)) {

                    $array = json_decode($IBS_INVOICE1, true);
                    $data = json_decode($array, true);

                    // Access "Status" value
                    $status = $data[0]['Status'];
                    $vatNumber = $data[0]['VatNumber'];

                }

                if ($status == 'success') {
                    $ret = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $invoice_code])
                        ->update(['IBS_INVOICE' => $vatNumber]);
                    $ret2 = DB::connection($db_conn)->table('dm_trip_detail')->where(['ORDM_ORNM' => $invoice_code])
                        ->update(['IBS_INVOICE' => $vatNumber]);

                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'vatNumber' => $vatNumber,
                        'message' => 'Vat Generated Successfully !!!',
                    );
                } else {
                    return array(
                        'success' => 0,
                        'vatNumber' => 'N',
                        'message' => 'Vat Server Problem !!!',
                    );
                }

            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public
        function SubmitSiteWiseInvoiceGrvDelivery(
        Request $request
    ) {


        $request->tpm_id;
        $request->tpm_code;
        $request->site_code;
        $request->site_id;
        $request->slgp_id;
        $request->country_id;
        $request->type_id;
        $request->ORDM_ORNM;
        $request->DELV_AMNT;
        $request->emp_id;
        $request->emp_code;

        $country = new Country();
        $country1 = $country->country($request->country_id);
        $round_digit = $country1->cont_dgit;
        $module_type = $country1->module_type;
        //return $round_digit;
        $outletData = json_decode($request->invoice_wise_delivery)[0];
        $outletData_child = json_decode($request->invoice_wise_delivery);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            // return $country;
            $db_conn = $country->cont_conn;
            //$slq_array=[]; 
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $sql1 = "";
                    $sql2 = "";
                    $sql3 = "";
                    $sql4 = "";
                    $sql5 = "";
                    $sql6 = "";
                    $sql7 = "";
                    $sql9 = "";
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');

                    if ($request->type_id == 1) {

                        $com_wh = $this->get_com_wh($request->tpm_code, $db_conn);
                        $comp = $com_wh ? $com_wh[0]->company_code : '';
                        $wh = $com_wh ? $com_wh[0]->WH_ID : '';
                        $IBS_INVOICE = 'N';
                        $site_type = $request->site_type == "" ? 2 : $request->site_type;
                        if ($site_type == 1 || $module_type == 1) {

                            $IBS_INVOICE = 'N';

                        } else {

                            if ($IBS_INVOICE1 = $this->get_IBS_INVOICE($comp, $wh, $request->ORDM_ORNM, $request->country_id)) {
                                $IBS_INVOICE = str_replace('"', '', $IBS_INVOICE1);
                                // $IBS_INVOICE = $this->get_IBS_INVOICE($request->ACMP_CODE, $request->WH_ID, $order_id, $request->country_id); //O0000328585-22-00039  01/2022-00449972
                            }
                            $length = strlen($IBS_INVOICE);
                            if ($length > 17) {
                                return array(
                                    'success' => 0,
                                    //  'column_id' => $request->id,
                                    'message' => 'Vat Server Problem !!!',
                                );
                            }
                        }
                        $sql1 = " Update tt_ordm set lfcl_id=11,ordm_drdt='$DRDT',ordm_dltm='$DRTM' WHERE ordm_ornm='$request->ORDM_ORNM' ;";
                        // array_push($slq_array,$sql1);

                        //return $slq_array;

                        foreach ($outletData_child as $orderLineData) {
                            $amount = $orderLineData->Dev_QNTY * $orderLineData->Rate;

                            $sql2 .= "update tt_ordd set ordd_dqty=$orderLineData->Dev_QNTY,ordd_odat=$amount  WHERE id=$orderLineData->trp_line ;";
                            //array_push($slq_array,$sql2);
                            $sql3 .= " Update dm_trip_detail set DELV_QNTY=$orderLineData->Dev_QNTY,TRIP_STATUS='D',IBS_INVOICE='$IBS_INVOICE',DISCOUNT=$orderLineData->DISCOUNT,ORDM_DRDT='$DRDT' WHERE OID=$orderLineData->trp_line;";
                            // array_push($slq_array,$sql3);
                        }

                        // $sql4 = " Update dm_trip_master set ORDM_DRDT='$DRDT',DELV_AMNT=round($request->DELV_AMNT,4),DELIVERY_STATUS=11 WHERE ORDM_ORNM='$request->ORDM_ORNM';";
                        $sql4 = " Update dm_trip_master set ORDM_DRDT='$DRDT',IBS_INVOICE='$IBS_INVOICE',DELV_AMNT=round($request->DELV_AMNT,$round_digit),DELIVERY_STATUS=11 WHERE ORDM_ORNM='$request->ORDM_ORNM';";
                        //array_push($slq_array,$sql4);
                        // $sql8 = " Update dm_trip_detail set TRIP_STATUS='D',IBS_INVOICE='$IBS_INVOICE' WHERE ORDM_ORNM='$request->ORDM_ORNM';";
                    } else {
                        $now = now();
                        //  "SELECT `ACMP_CODE`,`WH_ID`,`DM_CODE` FROM `dm_trip_master` WHERE TRIP_NO='TRIP-0500-01-2111000005' GROUP BY TRIP_NO";
                        $dm_ACMP_WH_ID_DM_CODE = DB::connection($db_conn)->table('dm_trip_master')->where(['TRIP_NO' => $request->tpm_code])->first();

                        /* echo $dm_ACMP_WH_ID_DM_CODE;
                         die();*/

                        $last_3 = substr($now->timestamp . $now->milli, 10);
                        $COLL_NUMBER1 = "RC" . $dt . '-' . $last_3;
                        $COLL_NUMBER = $request->ORDM_ORNM;


                        $sql5 = " Update tt_rtan set lfcl_id=11,dm_trip='$request->tpm_code',rtan_drdt='$DRDT',rtan_dltm='$DRTM' WHERE id=$request->tpm_id; ";
                        $sql6 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,slgp_id)
                                 VALUES (NULL,  '$dm_ACMP_WH_ID_DM_CODE->ACMP_CODE', '$DRDT', '$COLL_NUMBER', 'N', $request->emp_id,
                                  '$request->emp_code', '$dm_ACMP_WH_ID_DM_CODE->DM_CODE', '$dm_ACMP_WH_ID_DM_CODE->WH_ID',
                                  '$request->site_id', '$request->site_code',  round($request->DELV_AMNT,$round_digit),0, 'GRV', 11, 'N', 5,$request->slgp_id); ";

                        foreach ($outletData_child as $orderLineData) {
                            $amountd = $orderLineData->Dev_QNTY * $orderLineData->Rate;

                            // if(isset($orderLineData->delv_gqty)){									 

                            // $sql9 .= "INSERT IGNORE INTO `tt_rtdd_new`(`rtdd_rtan`, `amim_id`, `rtdd_qnty`,
                            // `rtdd_dqty`, `rtdd_ptyp`, `lfcl_id`)
                            // VALUES ('$orderLineData->ORDM_ORNM',$orderLineData->AMIM_ID,$orderLineData->ORDD_QNTY,
                            // $orderLineData->delv_gqty,2, 11);";
                            // $sql9 .= "INSERT IGNORE INTO `tt_rtdd_new`(`rtdd_rtan`, `amim_id`, `rtdd_qnty`,
                            // `rtdd_dqty`, `rtdd_ptyp`, `lfcl_id`)
                            // VALUES ('$orderLineData->ORDM_ORNM',$orderLineData->AMIM_ID,$orderLineData->ORDD_QNTY,
                            // $orderLineData->delv_bqty,1, 11);";	
                            // }

                            $sql7 .= " Update tt_rtdd set rtdd_dqty='$orderLineData->Dev_QNTY',rtdd_damt='$amountd' WHERE id=$orderLineData->trp_line; ";
                        }
                    }
                    DB::connection($db_conn)->unprepared($sql);

                    if ($request->type_id == 1) {
                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->unprepared($sql2); //multiple row
                        DB::connection($db_conn)->unprepared($sql3); //multiple row
                        DB::connection($db_conn)->insert($sql4);
                        // DB::connection($db_conn)->insert($sql8);


                    } else {
                        DB::connection($db_conn)->insert($sql5);
                        DB::connection($db_conn)->insert($sql6);
                        DB::connection($db_conn)->unprepared($sql7); //multiple row
                        // if($sql9){
                        // DB::connection($db_conn)->unprepared($sql9);//multiple row
                        // }
                    }
                    DB::connection($db_conn)->commit();

                    return array(
                        'success' => 1,
                        'message' => "Delivery Successfully",
                    );
                } catch
                (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    // $sq33 = "SET autocommit=1;";
                    // DB::connection($db_conn)->unprepared($sq33);
                    return $e;
                    //throw $e;
                }
            }
        }
    }

    public
        function ReverseDelivery_postman(
        Request $request
    ) {

        $request->country_id;
        $request->ORDM_ORNM;
        $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                $sql1 = "";
                $sql2 = "";
                $sql3 = "";
                $sql4 = "";
                $sql = "SET autocommit=0;";
                $DRDT = date('Y-m-d');
                $Year = date('Y');
                $DRTM = date('Y-m-d H:i:s');
                $dt = date('Ymd-Hi');
                $zero = 0;
                $is_EOT_completed = DB::connection($db_conn)->table('dm_trip_master as t1')
                    ->join('dm_trip as t2', 't1.TRIP_NO', '=', 't2.TRIP_NO')
                    ->select('t2.TRIP_NO')
                    ->where('t1.ORDM_ORNM', '=', $request->ORDM_ORNM)
                    ->where('t2.STATUS', '=', 'N')
                    ->where('t2.DM_ACTIVITY', '=', 20)
                    ->get();

                // if (count($is_EOT_completed) > 0) {

                // $is_challan_completed = DB::connection($db_conn)->table('tt_ordm')->where(['ordm_ornm' => $request->ORDM_ORNM])->first();
                // if ($is_challan_completed->lfcl_id == 39) {
                // return array(
                // 'success' => 2,
                // 'message' => "Already Challan Completed !!\nUn-do Delivery Failed",
                // );
                // }
                $order_cpcr = DB::connection($db_conn)->table('tl_cpcr')->where(['ordm_ornm' => $request->ORDM_ORNM, 'trnt_id' => 4])->where('sapr_amnt', '!=', $zero)->get();

                // ->where('ordd_dqty', '!=', $zero)->get();
                if (count($order_cpcr) > 0) {
                    return array(
                        'success' => 0,
                        'message' => "Already Credit Approved by SV !!\nUn-do Delivery Failed",
                    );
                } else {
                    $order_master = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 11, 'COLLECTION_AMNT' => 0])->get();
                    $order_details = DB::connection($db_conn)->table('tt_ordd')->where(['ordm_ornm' => $request->ORDM_ORNM])->get();
                    if (count($order_master) > 0) {
                        $sql1 = " Update tt_ordm set lfcl_id=34,ordm_drdt=ordm_date,ordm_dltm=ordm_time,aemp_eusr=$request->emp_id WHERE ordm_ornm='$request->ORDM_ORNM'; ";
                        foreach ($order_details as $orderLineData) {

                            $sql2 .= " Update tt_ordd set ordd_dqty=0,ordd_odat=0  WHERE ordm_ornm='$request->ORDM_ORNM' AND id=$orderLineData->id; ";
                            $sql3 .= " Update dm_trip_detail set DELV_QNTY=0,
                             DISCOUNT=round(($orderLineData->ordd_opds+$orderLineData->ordd_spdc+$orderLineData->ordd_dfdo),4),
                             TRIP_STATUS='N',ORDM_DRDT=ORDM_DATE
                             WHERE OID=$orderLineData->id AND ORDM_ORNM='$request->ORDM_ORNM';";

                        }
                        $sql4 = " Update dm_trip_master set ORDM_DRDT=ORDM_DATE,DELV_AMNT=0,DELIVERY_STATUS=0 WHERE ORDM_ORNM='$request->ORDM_ORNM';";

                        $sql_cancel = "DELETE FROM `tl_cpcr`  WHERE `ordm_ornm`='$request->ORDM_ORNM'";

                        DB::connection($db_conn)->unprepared($sql);

                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->unprepared($sql2); //multiple row
                        DB::connection($db_conn)->unprepared($sql3); //multiple row
                        DB::connection($db_conn)->insert($sql4);
                        DB::connection($db_conn)->unprepared($sql_cancel);
                        DB::connection($db_conn)->commit();
                        return array(
                            'success' => 1,
                            'message' => "Un-do Delivery Successfully",
                        );
                    } else {
                        $order_master_collection = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 11])->where('COLLECTION_AMNT', '!=', $zero)->get();
                        if (count($order_master_collection) > 0) {
                            return array(
                                'success' => 0,
                                'message' => "Already Collection Done!!\nUn-do Delivery Failed",
                            );
                        } else {
                            return array(
                                'success' => 02,
                                'message' => "Un-do Delivery Failed",
                            );
                        }
                    }
                }
                // } else {
                // return array(
                // 'success' => 0,
                // 'message' => "Un-do Delivery Failed !!! Already EOT Done",
                // );
                // }
            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }

    public
        function ReverseDelivery(
        Request $request
    ) {

        $request->country_id;
        $request->ORDM_ORNM;
        $request->emp_id;

        /* return array(
             'success' => 0,
             'message' => "Un-do Delivery Failed\nThis Option is temporary Blocked !!!",
         );*/


        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                $sql1 = "";
                $sql2 = "";
                $sql3 = "";
                $sql4 = "";
                $sql = "SET autocommit=0;";
                $DRDT = date('Y-m-d');
                $Year = date('Y');
                $DRTM = date('Y-m-d H:i:s');
                $dt = date('Ymd-Hi');
                $zero = 0;
                $is_challan_completed = DB::connection($db_conn)->table('tt_ordm')->where(['ordm_ornm' => $request->ORDM_ORNM])->first();
                if ($is_challan_completed->lfcl_id == 39) {
                    return array(
                        'success' => 0,
                        'message' => "Already Challan Completed !!\nReverse Delivery Failed",
                    );
                }
                $order_cpcr = DB::connection($db_conn)->table('tl_cpcr')->where(['ordm_ornm' => $request->ORDM_ORNM, 'trnt_id' => 4])->where('sapr_amnt', '!=', $zero)->get();

                // ->where('ordd_dqty', '!=', $zero)->get();
                if (count($order_cpcr) > 0) {
                    return array(
                        'success' => 0,
                        'message' => "Already Credit Approved by SV !!\nReverse Delivery Failed",
                    );
                } else {
                    $order_master = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 11, 'COLLECTION_AMNT' => 0, 'IBS_INVOICE' => 'N'])->get();
                    $order_details = DB::connection($db_conn)->table('tt_ordd')->where(['ordm_ornm' => $request->ORDM_ORNM])->get();
                    if (count($order_master) > 0) {
                        $sql1 = " Update tt_ordm set lfcl_id=34,ordm_drdt=ordm_date,ordm_dltm=ordm_time,aemp_eusr=$request->emp_id WHERE ordm_ornm='$request->ORDM_ORNM'; ";
                        foreach ($order_details as $orderLineData) {

                            $sql2 .= " Update tt_ordd set ordd_dqty=0,ordd_odat=0  WHERE ordm_ornm='$request->ORDM_ORNM' AND id=$orderLineData->id; ";
                            $sql3 .= " Update dm_trip_detail set DELV_QNTY=0,
                             DISCOUNT=round(($orderLineData->ordd_opds+$orderLineData->ordd_spdc+$orderLineData->ordd_dfdo),4),
                             TRIP_STATUS='N',ORDM_DRDT=ORDM_DATE
                             WHERE OID=$orderLineData->id AND ORDM_ORNM='$request->ORDM_ORNM';";

                        }
                        $sql4 = " Update dm_trip_master set ORDM_DRDT=ORDM_DATE,DELV_AMNT=0,DELIVERY_STATUS=0 WHERE ORDM_ORNM='$request->ORDM_ORNM';";

                        $sql_cancel = "DELETE FROM `tl_cpcr`  WHERE `ordm_ornm`='$request->ORDM_ORNM'";

                        DB::connection($db_conn)->unprepared($sql);

                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->unprepared($sql2); //multiple row
                        DB::connection($db_conn)->unprepared($sql3); //multiple row
                        DB::connection($db_conn)->insert($sql4);
                        DB::connection($db_conn)->unprepared($sql_cancel);
                        DB::connection($db_conn)->commit();
                        return array(
                            'success' => 1,
                            'message' => "Reverse Delivery Successfully",
                        );
                    } else {
                        $order_master_collection = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 11])->where('COLLECTION_AMNT', '!=', $zero)->get();
                        if (count($order_master_collection) > 0) {
                            return array(
                                'success' => 0,
                                'message' => "Already Collection Done!!\nReverse Delivery Failed",
                            );
                        } else {
                            return array(
                                'success' => 0,
                                'message' => "Reverse Delivery Failed, Invoice Generated !!!",
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }


    public
        function CancelDelivery(
        Request $request
    ) {

        $request->country_id;
        $request->ORDM_ORNM;
        $request->emp_id;
        $request->reason_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                $sql1 = "";
                $sql2 = "";
                $sql3 = "";
                $sql4 = "";
                $sql = "SET autocommit=0;";
                $DRDT = date('Y-m-d');
                $Year = date('Y');
                $DRTM = date('Y-m-d H:i:s');
                $dt = date('Ymd-Hi');
                $zero = 0;

                $is_challan_completed = DB::connection($db_conn)->table('tt_ordm')->where(['ordm_ornm' => $request->ORDM_ORNM])->first();

                if ($is_challan_completed->lfcl_id == 39) {
                    return array(
                        'success' => 0,
                        'message' => "Already Challan Completed !!\nUn-do Delivery Failed",
                    );
                } elseif ($is_challan_completed->lfcl_id == 38) {
                    return array(
                        'success' => 0,
                        'message' => "Already Delivery Canceled !!",
                    );
                }
                if ($request->country_id == 14) {
                    $order_master = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 0, 'COLLECTION_AMNT' => 0])->get();
                } else {
                    $order_master = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 0, 'COLLECTION_AMNT' => 0, 'IBS_INVOICE' => 'N'])->get();
                }

                // return $order_master;

                if (count($order_master) > 0) {

                    $sql1 = " Update tt_ordm set lfcl_id=38,ocrs_id=$request->reason_id,ordm_drdt='$DRDT',ordm_dltm='$DRTM',aemp_eusr=$request->emp_id WHERE ordm_ornm='$request->ORDM_ORNM'; ";

                    // return $sql1;

                    $sql4 = " Update dm_trip_master set ORDM_DRDT='$DRDT',DELV_AMNT=0,DELIVERY_STATUS=38 WHERE ORDM_ORNM='$request->ORDM_ORNM';";

                    DB::connection($db_conn)->unprepared($sql);

                    DB::connection($db_conn)->insert($sql1);
                    DB::connection($db_conn)->insert($sql4);

                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Cancel Delivery Successfully",
                    );
                } else {
                    $order_master_collection = DB::connection($db_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->ORDM_ORNM, 'DELIVERY_STATUS' => 11])->where('COLLECTION_AMNT', '!=', $zero)->get();
                    if (count($order_master_collection) > 0) {
                        return array(
                            'success' => 0,
                            'message' => "Already Collection Done!!\nUn-do Delivery Failed",
                        );
                    } else {
                        return array(
                            'success' => 0,
                            'message' => "Cancel Delivery Failed",
                        );
                    }

                }
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }

    public
        function SubmitSiteWiseInvoiceCollection(
        Request $request
    ) {

        $request->site_code;
        $request->site_id;
        $request->country_id;
        $request->Coll_type;
        $request->Ck_Imgl;
        $request->Ck_No;
        $request->BANK_NAME;
        $request->CHECK_DATE;
        $request->COLL_AMNT;
        $request->emp_id;
        $request->emp_code;
        $request->DM_CODE;
        $comp = $request->ACMP_CODE;
        $wh = $request->WH_ID;
        $request->collectedBy;

        $outletData = json_decode($request->Collection_Data)[0];
        $outletData_child = json_decode($request->Collection_Data);

        if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {
            $last_order_number = $outletData_child[count($outletData_child) - 1]->ORDM_ORNM;
            // $last_order_coll_amt = $outletData_child[count($outletData_child) - 1]->Coll_Amt + $request->ex_st_amt;
            $last_order_coll_amt = $outletData_child[count($outletData_child) - 1]->Coll_Amt;
        } else {
            $last_order_number = '';
            $last_order_coll_amt = 0;
        }
        $COLL_NOTE = $request->note == "" ? 'N' : $request->note;

        // Credit_EX=40
        // Cash_EX=43
        // Cash_ST=42
        //ex_st_amt
        // ex_st_type
        //
        if (($request->ex_st_type == 43 or $request->ex_st_type == 40) && $request->ex_st_amt > 300) {
            return array(
                'success' => 0,
                'message' => 'Collection Failed! Excess amt more than 50 !',
            );
        }
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $sql1 = "";
                    $ordm_list = "";
                    $sql_credit = "";
                    $sql2 = "";
                    $sql3 = "";
                    $sql4 = "";
                    $sql5 = "";
                    $sql8 = "";
                    $sql6 = "";
                    $sql_CLMP_Credit_EX_CN = "";
                    $sql_DMCL_Credit_EX_CN = "";
                    $sql_DMCL_Credit_EX_DN = "";
                    $sql_DMCL_Cash_EX_DN = "";
                    $sql_CLMP_Cash_EX_DN = "";
                    $sql_DMCL_Cash_ST_DN = "";
                    $sql_CLMP_Cash_ST_DN = "";
                    $sql_tl_cpcr = "";

                    $total_Credit_amt = 0;
                    $total_Debit_amt = 0;
                    $sql20 = false;
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');

                    //  $Coll_type = [1 => 'GRV', 2 => 'Cash', 3 => 'Cheque', 4 => 'Online'];
                    //  $coll_type = $Coll_type[$request->Coll_type];
                    /*
                    $Coll_type = 'GRV';//1
                    $Coll_type = 'Cash';//2
                    $Coll_type = 'Cheque';//3
                    $Coll_type = 'Online';//4*/

                    $coll_type = 0;
                    if ($request->Coll_type == 'GRV') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Cash') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Cheque') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Online') {
                        $coll_type = $request->Coll_no;
                    }


                    $now = now();
                    $last_3 = substr($now->timestamp . $now->milli, 10);
                    $COLL_NUMBER = "CL" . $coll_type . $dt . '-' . $last_3;
                    $CN_NUMBER = "CN" . $coll_type . $dt . '-' . $last_3;
                    $DN_NUMBER = "DN" . $coll_type . $dt . '-' . $last_3;

                    $sql1 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id,COLL_NOTE)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$COLL_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', round($request->COLL_AMNT,5),0, '$request->Coll_type',
                                   26, '$request->Ck_No', 1,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id','$COLL_NOTE'); ";

                    $sql2 = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                               `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                                VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$COLL_NUMBER','$request->site_id','$request->site_code',
                                round($request->COLL_AMNT,5),0,0); ";

                    if ($request->ex_st_type != 42 && $request->ex_st_amt > 0) { // cash or credit excess
                        $total_Debit_amt = round($request->COLL_AMNT - $request->ex_st_amt, 5);
                    } else {
                        // $total_Debit_amt = round($request->COLL_AMNT, 5);
                        $total_Debit_amt = round($request->COLL_AMNT + $request->ex_st_amt, 5);
                    }

                    // credit party Excess
                    if ($request->ex_st_type == 40 && $request->ex_st_amt > 0) {

                        $sql_CLMP_Credit_EX_CN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$request->site_id','$request->site_code',
                                0,round($request->ex_st_amt,5),0 ); ";

                        $sql_DMCL_Credit_EX_CN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', round($request->ex_st_amt,5),0, 'CN',
                                   40, '$request->Ck_No', 19,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_DMCL_Credit_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   41, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";
                    }

                    //  return '$sql1='.$sql1.'/n/'.$sql2.'/n/'.$sql_CLMP_Credit_EX_CN.'/n/'.$sql_DMCL_Credit_EX_CN.'/n/'.$sql_DMCL_Credit_EX_DN;

                    // Cash Party Excess
                    if ($request->ex_st_type == 43 && $request->ex_st_amt > 0) {

                        /*$sql_DMCL_Cash_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`,
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   43, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_CLMP_Cash_EX_DN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$request->site_id','$request->site_code',
                                0,round($request->ex_st_amt,5),0 ); ";*/

                        $sql_CLMP_Credit_EX_CN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$request->site_id','$request->site_code',
                                0,round($request->ex_st_amt,5),0 ); ";

                        $sql_DMCL_Credit_EX_CN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', round($request->ex_st_amt,5),0, 'CN',
                                   40, '$request->Ck_No', 19,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_DMCL_Credit_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   41, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                    }
                    // Cash Party Sort
                    if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {

                        $sql_DMCL_Cash_ST_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code', round($request->ex_st_amt,5),0, 'CN',
                                   42, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_CLMP_Cash_ST_DN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$CN_NUMBER','$request->site_id','$request->site_code',
                                round($request->ex_st_amt,5),0,0 ); ";
                    }
                    foreach ($outletData_child as $orderLineData) {

                        if ($orderLineData->Type == 1) {

                            if ($IBS_INVOICE1 = $this->get_IBS_INVOICE($comp, $wh, $orderLineData->ORDM_ORNM, $request->country_id)) {
                                $IBS_INVOICE = str_replace('"', '', $IBS_INVOICE1);
                                // $IBS_INVOICE = $this->get_IBS_INVOICE($request->ACMP_CODE, $request->WH_ID, $order_id, $request->country_id); //O0000328585-22-00039  01/2022-00449972
                            }
                            $length = strlen($IBS_INVOICE);
                            if ($length > 17) {
                                return array(
                                    'success' => 0,
                                    //  'column_id' => $request->id,
                                    'message' => 'Vat Server Problem !!!',
                                );
                            }

                            if ($orderLineData->ORDM_ORNM == $last_order_number && $last_order_coll_amt > 0) {
                                $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$last_order_number','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,round($last_order_coll_amt,5),0 ); ";
                                $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=(COLLECTION_AMNT+round($last_order_coll_amt,5)),IBS_INVOICE='$IBS_INVOICE'
                            WHERE ORDM_ORNM='$last_order_number' AND DELIVERY_STATUS=11 ;";
                            } else {
                                $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,round($orderLineData->Coll_Amt,5),0 ); ";
                                $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=round(COLLECTION_AMNT+$orderLineData->Coll_Amt,5),IBS_INVOICE='$IBS_INVOICE' 
                            WHERE ORDM_ORNM='$orderLineData->ORDM_ORNM' AND DELIVERY_STATUS=11 ;";
                            }
                            $sql8 .= " Update dm_trip_detail set IBS_INVOICE='$IBS_INVOICE' WHERE ORDM_ORNM='$orderLineData->ORDM_ORNM'; ";

                            /* $sql_credit .= "update `tl_stcm`t1 SET t1.`stcm_ordm`=t1.`stcm_ordm` - $total_Credit_amt
                                 WHERE  t1.`site_id`=$request->site_id AND t1.`slgp_id`=$request->Group_ID AND t1.`optp_id`=2 ;";*/
                            $total_Credit_amt += round($orderLineData->Coll_Amt, 5);

                            /*$sql_tl_cpcr_ck = DB::connection($db_conn)->table('tl_cpcr')->where(['ordm_ornm' => $orderLineData->ORDM_ORNM, 'trnt_id' => 3])->first();
                            if (count($sql_tl_cpcr_ck) > 0) {
                                $sql_tl_cpcr .= "DELETE FROM `tl_cpcr` WHERE `ordm_ornm`='$orderLineData->ORDM_ORNM' AND trnt_id=3";
                            }*/


                        } else {

                            if ($orderLineData->Type == 19) {
                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                round($orderLineData->Coll_Amt,5),0,0 ); ";

                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5)
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=19; ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else if ($orderLineData->Type == 15) {

                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,round($orderLineData->Coll_Amt,5),0 ); ";

                                $total_Credit_amt += round($orderLineData->Coll_Amt, 5);

                                $sql6 .= " Update dm_collection set COLL_REC_HO=COLL_REC_HO+round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID =15; ";

                                // $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else {

                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                round($orderLineData->Coll_Amt,5),0,0 ); ";
                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=5; ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            }
                        }
                    }
                    DB::connection($db_conn)->unprepared($sql);
                    $total_Debit_amt = round($total_Debit_amt, 4);
                    $total_Credit_amt = round($total_Credit_amt, 4);
                    // return $total_Debit_amt.'-'.$total_Credit_amt.'----'.$total_Debit_amt.'-'.$total_Credit_amt;

                    if ($total_Debit_amt === $total_Credit_amt) {
                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->insert($sql2);
                        if (!empty($sql3)) {
                            DB::connection($db_conn)->unprepared($sql3); //multiple row
                            DB::connection($db_conn)->unprepared($sql4); //multiple row
                            DB::connection($db_conn)->unprepared($sql8); //multiple row

                            /*if (!empty($sql_tl_cpcr)) {
                                DB::connection($db_conn)->unprepared($sql_tl_cpcr);//multiple row
                            }*/

                            // DB::connection($db_conn)->unprepared($sql_credit_block_release);//multiple row
                        }
                        if (!empty($sql5)) {
                            DB::connection($db_conn)->unprepared($sql5);
                            DB::connection($db_conn)->unprepared($sql6);
                        }
                        if ($request->ex_st_type == 40 && $request->ex_st_amt > 0) {
                            DB::connection($db_conn)->unprepared($sql_CLMP_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_DN);
                        }
                        if ($request->ex_st_type == 43 && $request->ex_st_amt > 0) {
                            // DB::connection($db_conn)->unprepared($sql_DMCL_Cash_EX_DN);
                            // DB::connection($db_conn)->unprepared($sql_CLMP_Cash_EX_DN);
                            DB::connection($db_conn)->unprepared($sql_CLMP_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_DN);
                        }
                        if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {
                            DB::connection($db_conn)->unprepared($sql_DMCL_Cash_ST_DN);
                            DB::connection($db_conn)->unprepared($sql_CLMP_Cash_ST_DN);
                        }

                        DB::connection($db_conn)->commit();
                        return array(

                            'success' => 1,
                            'message' => "Collection Successfully",
                        );

                    } else {
                        // DB::connection($db_conn)->commit();
                        return array(
                            'success' => 0,
                            'message' => 'Collection Failed! Debit and Credit not same!',
                        );
                    }
                } catch
                (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return response()->json(array('success' => 0, 'message' => $e->errorInfo[2]));
                    // return array(
                    // 'success' => 0,
                    // 'message' => $e);
                }
            }
        }
    }

    function SubmitGroupSiteWiseInvoiceCollection(Request $request)
    {

        $request->site_code;
        $request->site_id;
        $request->country_id;
        $request->Coll_type;
        $request->Ck_Imgl;
        $request->Ck_No;
        $request->BANK_NAME;
        $request->CHECK_DATE;
        $request->COLL_AMNT;
        $request->emp_id;
        $request->emp_code;
        $request->DM_CODE;
        $request->ACMP_CODE;
        $request->WH_ID;
        $request->collectedBy;

        $outletData = json_decode($request->Collection_Data)[0];
        $outletData_child = json_decode($request->Collection_Data);

        if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {
            $last_order_number = $outletData_child[count($outletData_child) - 1]->ORDM_ORNM;
            //$last_order_coll_amt = $outletData_child[count($outletData_child) - 1]->Coll_Amt + $request->ex_st_amt;
            $last_order_coll_amt = $outletData_child[count($outletData_child) - 1]->Coll_Amt;
        } else {
            $last_order_number = '';
            $last_order_coll_amt = 0;
        }
        $COLL_NOTE = $request->note == "" ? 'N' : $request->note;

        // Credit_EX=40
        // Cash_EX=43
        // Cash_ST=42
        //ex_st_amt
        // ex_st_type
        //
        if (($request->ex_st_type == 43 or $request->ex_st_type == 40) && $request->ex_st_amt > 300) {
            return array(
                'success' => 0,
                'message' => 'Collection Failed! Excess amt more than 50 !',
            );
        }
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $module_type = $country->module_type;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $sql1 = "";
                    $ordm_list = "";
                    $sql_credit = "";
                    $sql2 = "";
                    $sql3 = "";
                    $sql4 = "";
                    $sql5 = "";
                    $sql6 = "";
                    $sql_CLMP_Credit_EX_CN = "";
                    $sql_DMCL_Credit_EX_CN = "";
                    $sql_DMCL_Credit_EX_DN = "";
                    $sql_DMCL_Cash_EX_DN = "";
                    $sql_CLMP_Cash_EX_DN = "";
                    $sql_DMCL_Cash_ST_DN = "";
                    $sql_CLMP_Cash_ST_DN = "";
                    $sql_tl_cpcr = "";

                    $total_Credit_amt = 0;
                    $total_Debit_amt = 0;
                    $sql20 = false;
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');

                    //  $Coll_type = [1 => 'GRV', 2 => 'Cash', 3 => 'Cheque', 4 => 'Online'];
                    //  $coll_type = $Coll_type[$request->Coll_type];
                    /*
                    $Coll_type = 'GRV';//1
                    $Coll_type = 'Cash';//2
                    $Coll_type = 'Cheque';//3
                    $Coll_type = 'Online';//4*/

                    $coll_type = 0;
                    if ($request->Coll_type == 'GRV') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Cash') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Cheque') {
                        $coll_type = $request->Coll_no;
                    } elseif ($request->Coll_type == 'Online') {
                        $coll_type = $request->Coll_no;
                    }

                    if ($module_type == 2) {
                        $sql_get_mother = "SELECT `mother_site_code`AS m_site_code FROM `tl_site_party_mapping` WHERE `site_code`=$request->site_code";
                        $pending_collection = DB::connection($db_conn)->select($sql_get_mother);
                        if ($pending_collection) {
                            $m_site_code = $pending_collection[0]->m_site_code;
                            $m_site_id = $pending_collection[0]->m_site_code;
                        } else {
                            $m_site_id = $request->site_id;
                            $m_site_code = $request->site_code;
                        }
                    } else {
                        $m_site_id = $request->site_id;
                        $m_site_code = $request->site_code;
                    }

                    $now = now();
                    $last_3 = substr($now->timestamp . $now->milli, 10);
                    $COLL_NUMBER = "CL" . $coll_type . $dt . '-' . $last_3;
                    $CN_NUMBER = "CN" . $coll_type . $dt . '-' . $last_3;
                    $DN_NUMBER = "DN" . $coll_type . $dt . '-' . $last_3;

                    $sql1 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id,COLL_NOTE)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$COLL_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', round($request->COLL_AMNT,5),0, '$request->Coll_type',
                                   26, '$request->Ck_No', 1,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id','$COLL_NOTE'); ";

                    $sql2 = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                               `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                                VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$COLL_NUMBER','$m_site_id','$m_site_code',
                                round($request->COLL_AMNT,5),0,0); ";

                    if ($request->ex_st_type != 42 && $request->ex_st_amt > 0) { // cash or credit excess
                        $total_Debit_amt = round($request->COLL_AMNT - $request->ex_st_amt, 5);
                    } else {
                        // $total_Debit_amt = round($request->COLL_AMNT, 5);
                        $total_Debit_amt = round($request->COLL_AMNT + $request->ex_st_amt, 5);
                    }

                    // credit party Excess
                    if ($request->ex_st_type == 40 && $request->ex_st_amt > 0) {

                        $sql_CLMP_Credit_EX_CN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$m_site_id','$m_site_code',
                                0,round($request->ex_st_amt,5),0 ); ";

                        $sql_DMCL_Credit_EX_CN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', round($request->ex_st_amt,5),0, 'CN',
                                   40, '$request->Ck_No', 19,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_DMCL_Credit_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   41, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";
                    }

                    //  return '$sql1='.$sql1.'/n/'.$sql2.'/n/'.$sql_CLMP_Credit_EX_CN.'/n/'.$sql_DMCL_Credit_EX_CN.'/n/'.$sql_DMCL_Credit_EX_DN;

                    // Cash Party Excess
                    if ($request->ex_st_type == 43 && $request->ex_st_amt > 0) {

                        /*$sql_DMCL_Cash_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`,
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   43, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_CLMP_Cash_EX_DN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$m_site_id','$m_site_code',
                                0,round($request->ex_st_amt,5),0 ); ";*/

                        $sql_CLMP_Credit_EX_CN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$DN_NUMBER','$m_site_id','$m_site_code',
                                0,round($request->ex_st_amt,5),0 ); ";

                        $sql_DMCL_Credit_EX_CN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', round($request->ex_st_amt,5),0, 'CN',
                                   40, '$request->Ck_No', 19,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_DMCL_Credit_EX_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$DN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', -round($request->ex_st_amt,5),-round($request->ex_st_amt,5), 'DN',
                                   41, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                    }
                    // Cash Party Sort
                    if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {

                        $sql_DMCL_Cash_ST_DN = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$CN_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$m_site_id', '$m_site_code', round($request->ex_st_amt,5),0, 'CN',
                                   42, '$request->Ck_No', 5,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";

                        $sql_CLMP_Cash_ST_DN = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$CN_NUMBER','$m_site_id','$m_site_code',
                                round($request->ex_st_amt,5),0,0 ); ";
                    }
                    foreach ($outletData_child as $orderLineData) {

                        if ($orderLineData->Type == 1) {

                            if ($orderLineData->ORDM_ORNM == $last_order_number && $last_order_coll_amt > 0) {
                                $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$last_order_number','$m_site_id','$m_site_code',
                                0,round($last_order_coll_amt,5),0 ); ";
                                $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=(COLLECTION_AMNT+round($last_order_coll_amt,5)) 
                            WHERE ORDM_ORNM='$last_order_number' AND DELIVERY_STATUS=11 ;";
                            } else {
                                $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$m_site_id','$m_site_code',
                                0,round($orderLineData->Coll_Amt,5),0 ); ";
                                $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=round(COLLECTION_AMNT+$orderLineData->Coll_Amt,5) 
                            WHERE ORDM_ORNM='$orderLineData->ORDM_ORNM' AND DELIVERY_STATUS=11 ;";
                            }
                            /* $sql_credit .= "update `tl_stcm`t1 SET t1.`stcm_ordm`=t1.`stcm_ordm` - $total_Credit_amt
                                 WHERE  t1.`site_id`=$request->site_id AND t1.`slgp_id`=$request->Group_ID AND t1.`optp_id`=2 ;";*/
                            $total_Credit_amt += round($orderLineData->Coll_Amt, 5);

                            /*$sql_tl_cpcr_ck = DB::connection($db_conn)->table('tl_cpcr')->where(['ordm_ornm' => $orderLineData->ORDM_ORNM, 'trnt_id' => 3])->first();
                            if (count($sql_tl_cpcr_ck) > 0) {
                                $sql_tl_cpcr .= "DELETE FROM `tl_cpcr` WHERE `ordm_ornm`='$orderLineData->ORDM_ORNM' AND trnt_id=3";
                            }*/


                        } else {

                            if ($orderLineData->Type == 19) {
                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                round($orderLineData->Coll_Amt,5),0,0 ); ";

                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5)
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=19; ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else if ($orderLineData->Type == 15) {

                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,round($orderLineData->Coll_Amt,5),0 ); ";

                                $total_Credit_amt += round($orderLineData->Coll_Amt, 5);

                                $sql6 .= " Update dm_collection set COLL_REC_HO=COLL_REC_HO+round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID =15; ";

                                // $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else {

                                $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                round($orderLineData->Coll_Amt,5),0,0 ); ";
                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=5; ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            }

                            /*$sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$m_site_id','$m_site_code',
                                round($orderLineData->Coll_Amt,5),0,0 ); ";

                            if ($orderLineData->Type == 19) {
                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5)
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=19; ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else if ($orderLineData->Type == 15) {

                                $total_Credit_amt += round($orderLineData->Coll_Amt, 5);

                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID IN(15); ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            } else {
                                $sql6 .= " Update dm_collection set COLL_REC_HO=round($orderLineData->Coll_Amt,5),STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID IN(5); ";

                                $total_Debit_amt += round($orderLineData->Coll_Amt, 5);

                            }*/
                        }
                    }
                    DB::connection($db_conn)->unprepared($sql);
                    $total_Debit_amt = round($total_Debit_amt, 4);
                    $total_Credit_amt = round($total_Credit_amt, 4);
                    //return $total_Debit_amt.'-'.$total_Credit_amt.'----'.$total_Debit_amt.'-'.$total_Credit_amt;

                    if ($total_Debit_amt === $total_Credit_amt) {
                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->insert($sql2);
                        if (!empty($sql3)) {
                            DB::connection($db_conn)->unprepared($sql3); //multiple row
                            DB::connection($db_conn)->unprepared($sql4); //multiple row

                            /*if (!empty($sql_tl_cpcr)) {
                                DB::connection($db_conn)->unprepared($sql_tl_cpcr);//multiple row
                            }*/

                            // DB::connection($db_conn)->unprepared($sql_credit_block_release);//multiple row
                        }
                        if (!empty($sql5)) {
                            DB::connection($db_conn)->unprepared($sql5);
                            DB::connection($db_conn)->unprepared($sql6);
                        }
                        if ($request->ex_st_type == 40 && $request->ex_st_amt > 0) {
                            DB::connection($db_conn)->unprepared($sql_CLMP_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_DN);
                        }
                        if ($request->ex_st_type == 43 && $request->ex_st_amt > 0) {
                            // DB::connection($db_conn)->unprepared($sql_DMCL_Cash_EX_DN);
                            // DB::connection($db_conn)->unprepared($sql_CLMP_Cash_EX_DN);
                            DB::connection($db_conn)->unprepared($sql_CLMP_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_CN);
                            DB::connection($db_conn)->unprepared($sql_DMCL_Credit_EX_DN);
                        }
                        if ($request->ex_st_type == 42 && $request->ex_st_amt > 0) {
                            DB::connection($db_conn)->unprepared($sql_DMCL_Cash_ST_DN);
                            DB::connection($db_conn)->unprepared($sql_CLMP_Cash_ST_DN);
                        }

                        DB::connection($db_conn)->commit();
                        return array(

                            'success' => 1,
                            'message' => "Collection Successfully",
                        );

                    } else {
                        // DB::connection($db_conn)->commit();
                        return array(
                            'success' => 0,
                            'message' => 'Collection Failed! Debit and Credit not same!',
                        );
                    }
                } catch
                (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return response()->json(array('success' => 0, 'message' => $e->errorInfo[2]));
                    // return array(
                    // 'success' => 0,
                    // 'message' => $e);
                }
            }
        }
    }

    public
        function SubmitInvoiceSMS(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $sql1 = "";
            try {

                $sql1 = "INSERT INTO `tm_smst` (`id`, `aemp_id`, `site_id`, `site_mob1`, `smst_body`, `cont_id`, `lfcl_id`)
                         VALUES (NULL, '$request->emp_id', '$request->site_id', '$request->site_mobile', '$request->sms_body', $request->country_id, 1)";

                DB::connection($db_conn)->unprepared($sql);
                // if ($request->type_id == 1) {
                DB::connection($db_conn)->insert($sql1);

                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Successfully Save SMS ",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function getIsCashPartyCreditRequestExist(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $success = 0;
            $message = "";
            $lastTimeInputCollection = 0;

            try {

                $isExistRequest = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm])->first();


                if ($isExistRequest) {
                    if ($isExistRequest->trnt_id == 3) {

                        $success = 2;
                        $message = "Already Requested !!!";
                        $lastTimeInputCollection = $isExistRequest->attr3;
                    } else if ($isExistRequest->trnt_id == 4) {
                        $success = 3;
                        $message = "Already Approved !!!";
                        $lastTimeInputCollection = $isExistRequest->attr3;
                    } else if ($isExistRequest->trnt_id == 5) {

                        $success = 5;
                        $message = "Request Cancel ";
                        $lastTimeInputCollection = $isExistRequest->attr3;
                    }
                } else {
                    $success = 999;
                }

                return array(
                    'success' => $success,
                    'message' => $message,
                    'collectionAmount' => $lastTimeInputCollection,
                );

            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function give_notification(
        $usnm,
        $title,
        $body,
        $cond_id
    ) {
        $country = (new Country())->country($cond_id);
        $db_conn = $country->cont_conn;
        $result = DB::connection($db_conn)->select("SELECT
             t2.eftm_tokn
             FROM fm_eftm t2
             WHERE t2.aemp_id=$usnm;
             ");
        $image = 'https://images.sihirbox.com/bdp/menu/credit_reqest.png';
        //   $top = "eXmv9lLjScieqYKNa6OG23:APA91bFn14NRPbH0dtRogHmvY2Jl_C7le_g2tp_t4mnxhWMdLpX_R-Qrvmb14uTxDNnBFPAfstEsI9aNFX4hUk-yWvGXBL13HIxKmLsDUJCInjJ9Zaee3e-J4NyQQl4sF6HdIpIIzxZT";
        foreach ($result as $data) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
            "to":"' . $data->eftm_tokn . '",
            "collapse_key": "type_a",
            "notification": {
            "body":"' . $body . '",
            "title":"' . $title . '",
            "image":"' . $image . '"
               }}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:  application/json ',
                    'Authorization:  key=AAAAzsoU3ew:APA91bH8T-JQP9unlLTWFsoHHIRaMpXyGnTQ52bUkKB_kBuaLI75WGo4BPIfyawFrzn0Tsj_o9Deg4Qu-cW4ZBIJjC3wcCRqKroGV-BS576LbvE9x1rI1vNuM-8wRNpE9IVv-rx6lLH7'
                ),
            )
            );
            $response = curl_exec($curl);
            curl_close($curl);

            // return $response;
        }
    }

    public
        function ggg(
    ) {
        $curl = curl_init();

        $top = "eXmv9lLjScieqYKNa6OG23:APA91bFn14NRPbH0dtRogHmvY2Jl_C7le_g2tp_t4mnxhWMdLpX_R-Qrvmb14uTxDNnBFPAfstEsI9aNFX4hUk-yWvGXBL13HIxKmLsDUJCInjJ9Zaee3e-J4NyQQl4sF6HdIpIIzxZT";
        $body = "Mahmud vai";
        $title = "Valo kisu";
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "to":"eXmv9lLjScieqYKNa6OG23:APA91bFn14NRPbH0dtRogHmvY2Jl_C7le_g2tp_t4mnxhWMdLpX_R-Qrvmb14uTxDNnBFPAfstEsI9aNFX4hUk-yWvGXBL13HIxKmLsDUJCInjJ9Zaee3e-J4NyQQl4sF6HdIpIIzxZT",
            "collapse_key": "type_a",
            "notification": {
            "body":"fffff",
            "title":"fffff"
               }}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type:  application/json ',
                'Authorization:  key=AAAAzsoU3ew:APA91bH8T-JQP9unlLTWFsoHHIRaMpXyGnTQ52bUkKB_kBuaLI75WGo4BPIfyawFrzn0Tsj_o9Deg4Qu-cW4ZBIJjC3wcCRqKroGV-BS576LbvE9x1rI1vNuM-8wRNpE9IVv-rx6lLH7'
            ),
        )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public
        function SubmitCashPartyCreditRequest(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $message = "";
            $success = 0;
            //$month = date('m');
            //$year = date('Y');
            try {
                $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,
             t1.`aemp_id`AS mngr_id,t3.aemp_usnm AS manager_usname,t2.aemp_usnm
             FROM `tm_scbm`t1 JOIN tm_aemp t2 ON(t1.`aemp_id`=t2.aemp_mngr) AND t2.id=$request->emp_id
             JOIN tm_aemp t3 ON(t2.aemp_mngr=t3.id);
             "); /* $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,
           t1.`aemp_id`AS mngr_id,t3.aemp_usnm AS manager_usname,t2.aemp_usnm
           FROM `tm_scbm`t1 JOIN tm_aemp t2 ON(t1.`aemp_id`=t2.aemp_mngr) AND t2.id=$request->emp_id
           JOIN tm_aemp t3 ON(t2.aemp_mngr=t3.id)
           WHERE t1.spbm_mnth = $month AND t1.spbm_year = $year;
           ");*/

                if ($sql_mngr) {
                    $Balance = $sql_mngr[0]->Balance;
                    $spbm_id = $sql_mngr[0]->spbm_id;
                    $mngr_id = $sql_mngr[0]->mngr_id;
                    $manager_usname = $sql_mngr[0]->manager_usname;
                    $SR_usnm = $sql_mngr[0]->aemp_usnm;

                    /*$title = 'You Have 1 Credit Request please approve it';
                    $body = 'SR ID:' . $SR_usnm . '\nOrder No:' . $request->ordm_ornm . '\nRequest Amt:' . $request->sreq_amnt;
                    $notification = $this->give_notification($mngr_id, $title, $body, $request->country_id);
                    return $notification;*/

                    $sql_cpcr = "INSERT INTO `tl_cpcr`(`spbm_id`, `site_id`, `ordm_ornm`, `ordm_amnt`, `sreq_amnt`,scol_amnt, 
                    `sapr_amnt`, `trnt_id`, `spbd_type`, `cont_id`, `lfcl_id`,
                    `aemp_iusr`, `aemp_eusr`,`attr3`,cpcr_cdat)
                    VALUES ($spbm_id,'$request->site_id','$request->ordm_ornm', round($request->ordm_amnt,4), round($request->sreq_amnt,4),
                    0,0,3,'Credit Req',$request->country_id,'1','$request->up_emp_id','$request->up_emp_id',round($request->coll_amnt,4),'$request->credit_date');";

                    $sql_cpcr_approved = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm, 'trnt_id' => 4])->first();

                    $sql_cpcr_cancel = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm, 'trnt_id' => 5])->first();

                    $sql_cpcr_ck = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm])->first();

                    if ($sql_cpcr_approved) {
                        $success = 3;
                        $message = "Already Approved !!!";
                        // $title = 'You Have 1 Credit Request please approve it';
                        // $body = 'SR ID:' . $SR_usnm . '\nOrder No:' . $request->ordm_ornm . '\nRequest Amt:' . $request->sreq_amnt;
                        // $message = $this->give_notification(4, $title, $body, $request->country_id);
                        // $message = $this->ggg();

                    } else if ($sql_cpcr_cancel) {
                        $success = 5;
                        $message = "Request Cancel ";
                    } else if ($sql_cpcr_ck) {
                        $success = 2;
                        $message = "Already Requested !!!";
                    } else {
                        DB::connection($db_conn)->insert($sql);
                        DB::connection($db_conn)->insert($sql_cpcr);

                        $title = 'You Have 1 Credit Request please approve it';
                        $body = 'SR ID:' . $SR_usnm . '\nOrder No:' . $request->ordm_ornm . '\nColection Date:' . $request->credit_date . '\nRequest Amt:' . round($request->sreq_amnt, 4);
                        $this->give_notification($mngr_id, $title, $body, $request->country_id);

                        $success = 1;
                        $message = "Request Successful";

                    }
                    DB::connection($db_conn)->commit();

                } else {
                    $success = 0;
                    $message = "request failed !  SV Have not any Buget";
                }

                return array(
                    'success' => $success,
                    'message' => $message
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function GetCashPartyCreditRequestList(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $month = date('m');
            $year = date('Y');

            $manager_balance = DB::connection($db_conn)->select("
                          SELECT
                          round(t2.spbm_amnt,4)  AS mngr_balance               
                          FROM tm_scbm t2 
                          WHERE t2.aemp_id=$request->emp_id 
                        ");

            /*$manager_balance = DB::connection($db_conn)->select("
                          SELECT
                          round(t2.spbm_amnt,4)  AS mngr_balance
                          FROM tm_scbm t2
                          WHERE t2.aemp_id=$request->emp_id AND t2.spbm_mnth = $month AND t2.spbm_year = $year
                        ");*/

            $req_list_item = DB::connection($db_conn)->select("
                          SELECT t1.`id`AS cpcr_id,t1.`site_id`,t2.site_name,t2.site_mob1 AS site_mob,t1.`ordm_ornm`,
                          t1.ordm_amnt,(t1.`sreq_amnt`-t1.`sapr_amnt`)AS req_amt,t1.`cpcr_cdat`
                          FROM `tl_cpcr`t1 
                          JOIN tm_site t2 ON(t1.`site_id`=t2.id)
                          JOIN tm_scbm t3 ON(t1.spbm_id=t3.id)
                          WHERE t3.aemp_id=$request->emp_id AND (t1.`sreq_amnt`-t1.`sapr_amnt`)>0 AND t1.trnt_id=3;
                        ");

            return array(
                'balance' => $manager_balance[0]->mngr_balance,
                'items' => $req_list_item,
            );


        }
    }

    public
        function GetCashPartyCreditApprovedList(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $month = date('m');
            $year = date('Y');

            $manager_balance = DB::connection($db_conn)->select("
                          SELECT
                          round(t2.spbm_amnt,4)  AS mngr_balance               
                          FROM tm_scbm t2 
                          WHERE t2.aemp_id=$request->emp_id 
                        "); /* $manager_balance = DB::connection($db_conn)->select("
                        SELECT
                        round(t2.spbm_amnt,4)  AS mngr_balance
                        FROM tm_scbm t2
                        WHERE t2.aemp_id=$request->emp_id AND t2.spbm_mnth = $month AND t2.spbm_year = $year
                      ");*/

            $req_list_item = DB::connection($db_conn)->select("
                          SELECT t1.`id`AS cpcr_id,t1.`site_id`,t2.site_name,t2.site_mob1 AS site_mob,t1.`ordm_ornm`,
                          t1.ordm_amnt, t1.`sreq_amnt`,
                          t1.`sapr_amnt`AS aprv_amt,t1.`scol_amnt`,t1.`cpcr_cdat`
                          FROM `tl_cpcr`t1 
                          JOIN tm_site t2 ON(t1.`site_id`=t2.id)
                          JOIN tm_scbm t3 ON(t1.spbm_id=t3.id)
                          JOIN tm_scbm t4 ON t1.`spbm_id`=t4.id
                          WHERE t3.aemp_id=$request->emp_id AND t1.`sapr_amnt`>0 AND t1.trnt_id=4;
                         
                        "); /*$req_list_item = DB::connection($db_conn)->select("
                        SELECT t1.`id`AS cpcr_id,t1.`site_id`,t2.site_name,t2.site_mob1 AS site_mob,t1.`ordm_ornm`,
                        t1.ordm_amnt, t1.`sreq_amnt`,
                        t1.`sapr_amnt`AS aprv_amt,t1.`scol_amnt`,t1.`cpcr_cdat`
                        FROM `tl_cpcr`t1
                        JOIN tm_site t2 ON(t1.`site_id`=t2.id)
                        JOIN tm_scbm t3 ON(t1.spbm_id=t3.id)
                        JOIN tm_scbm t4 ON t1.`spbm_id`=t4.id
                        WHERE t3.aemp_id=$request->emp_id AND t1.`sapr_amnt`>0 AND t1.trnt_id=4
                        AND t4.spbm_mnth=$month AND t4.spbm_year=$year;
                      ");*/

            return array(
                'balance' => $manager_balance[0]->mngr_balance,
                'items' => $req_list_item,
            );
        }
    }

    public
        function SpecialBudgetDetailsUserWise(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $id = $request->emp_id;
            $role_id = $request->role_id;
            $under_user = [];
            $message = '';
            $success = 0;
            $request_time = date('Y-m-d H:i:s');
            try {
                // $emp = Employee::on($db_conn)->where(['id' => $id])->first();
                $cash_party_credit_budget = $this->calculateBudget($id, $role_id, $request_time, $db_conn);
                return $cash_party_credit_budget;
            } catch (\Exception $e) {
                $message = "Invalid staff id";
                $response_time = date('Y-m-d H:i:s');
                return array(
                    'own_budget' => 0,
                    'own_available_budget' => 0,
                    'team_budget' => 0,
                    'team_available_budget' => 0,
                    'under_user' => $under_user,
                    'success' => $success,
                    'message' => $message,
                    'request_time' => $request_time,
                    'response_time' => date('Y-m-d H:i:s'),
                );
            }
            return array(
                'balance' => $manager_balance[0]->mngr_balance,
                'items' => $req_list_item,
            );
        }
    }

    public
        function calculateBudget(
        $auto_id,
        $role_id,
        $request_time,
        $db_conn
    ) {
        $t_bdgt = 0;
        $t_available = 0;
        $own_bdgt = 0;
        $own_available = 0;
        $month = date('m');
        $year = date('Y');
        $under_user = [];
        $j = 0;
        $success = 0;
        $message = '';
        try {
            $own_bgt = SpecialBudgetMaster::on($db_conn)->where(['aemp_id' => $auto_id, 'spbm_mnth' => $month, 'spbm_year' => $year])->first();
            if ($own_bgt != '') {
                $own_bdgt += $own_bgt->spbm_limt;
                $own_available += $own_bgt->spbm_amnt;
            }
            if ($role_id > 2) {
                $mngr = $auto_id;
                $u_usr = 0;
                while ($mngr) {
                    $cash_bdgt_user = DB::connection($db_conn)->select("SELECT t1.spbm_limt,
                                        t1.spbm_avil,
                                        t1.spbm_amnt,
                                        t1.aemp_id,
                                        t2.role_id,
                                        t2.aemp_usnm,
                                        t2.aemp_name
                                        FROM `tt_spbm` t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                        WHERE t2.aemp_mngr in ($mngr) AND t1.spbm_mnth=$month AND t1.spbm_year=$year");
                    $mngr = '';
                    //dd($cash_bdgt_user);
                    $count = 0;
                    foreach ($cash_bdgt_user as $i => $user) {
                        $t_bdgt += $user->spbm_limt;
                        $t_available += $user->spbm_amnt;
                        if ($user->role_id > 2) {
                            if ($count == 0) {
                                $mngr .= $user->aemp_id;
                                $count++;
                            } else {
                                $mngr .= ',' . $user->aemp_id;
                            }
                        }
                        if ($u_usr == 0) {
                            $under_user[$j]['aemp_name'] = $user->aemp_name;
                            $under_user[$j]['aemp_id'] = $user->aemp_id;
                            $under_user[$j]['aemp_usnm'] = $user->aemp_usnm;
                            $under_user[$j]['own_budget'] = $user->spbm_limt;
                            $under_user[$j]['own_available_budget'] = $user->spbm_amnt;
                            $j++;
                        }
                        $u_user = 1;

                    }
                }
            }
            $success = 1;
            return array(
                'own_budget' => round($own_bdgt, 4),
                'own_available_budget' => round($own_available, 4),
                'team_budget' => round($t_bdgt, 4),
                'team_available_budget' => round($t_available, 4),
                'under_user' => $under_user,
                'success' => $success,
                'message' => $message,
                'request_time' => $request_time,
                'response_time' => date('Y-m-d H:i:s'),

            );
        } catch (\Exception $e) {
            $success = 0;
            $message = "Budget not available";
            return array(
                'own_budget' => round($own_bdgt, 4),
                'own_available_budget' => round($own_available, 4),
                'team_budget' => round($t_bdgt, 4),
                'team_available_budget' => round($t_available, 4),
                'under_user' => $under_user,
                'success' => $success,
                'message' => $message,
                'request_time' => $request_time,
                'response_time' => date('Y-m-d H:i:s'),
            );
        }
    }

    public
        function SubmitCashPartyCreditApproved(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $message = "";
            $success = 0;
            try {
                $month = date('m');
                $year = date('Y');
                /* $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,t1.`aemp_id`AS mngr_id
                                   FROM `tm_scbm`t1
                                   WHERE  t1.aemp_id=$request->emp_id AND t1.spbm_mnth = $month AND t1.spbm_year = $year;");*/
                $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,t1.`aemp_id`AS mngr_id
                                  FROM `tm_scbm`t1 
                                  WHERE  t1.aemp_id=$request->emp_id ;");

                $_msg_id = $sql_mngr[0]->mngr_id;
                if ($request->status == 1) {
                    if ($sql_mngr) {
                        $Balance = $sql_mngr[0]->Balance;
                        $spbm_id = $sql_mngr[0]->spbm_id;
                        $mngr_id = $sql_mngr[0]->mngr_id;
                        if ($Balance > $request->req_amt) {

                            $sql_cpcr_approved = DB::connection($db_conn)->table('tl_cpcr')->where(['id' => $request->cpcr_id, 'trnt_id' => 4])->first();
                            if ($sql_cpcr_approved) {
                                $success = 3;
                                $message = "Already Approved !!!";
                            } else {
                                $sql_approved = "UPDATE `tl_cpcr` SET `sapr_amnt`=$request->req_amt,trnt_id='4',spbd_type='Credit Apr', aemp_eusr=$mngr_id
                        WHERE `id`=$request->cpcr_id;";

                                $sql_adjust_balance = "UPDATE `tm_scbm` SET `spbm_avil`=`spbm_avil`+$request->req_amt ,
                        spbm_amnt=spbm_amnt-$request->req_amt , aemp_eusr=$mngr_id
                        WHERE `id`=$spbm_id";

                                DB::connection($db_conn)->insert($sql);
                                DB::connection($db_conn)->unprepared($sql_approved);
                                DB::connection($db_conn)->unprepared($sql_adjust_balance);
                                DB::connection($db_conn)->commit();

                                $success = 1;
                                $message = "Successful Approved !!!";
                            }

                        } else {
                            $success = 2;
                            $message = "Have Not Enough Balance !!!";
                        }
                    } else {
                        $success = 0;
                        $message = "Request Failed !!!";
                    }
                } else {
                    // update query $sql_cancle = "SET trnt_id='5',spbd_type='Credit Rej', aemp_eusr=$_msg_id"
                    /*
                     * it`s requirement for iqram bhai.
                     */

                    $sql_cancel = "DELETE FROM `tl_cpcr`  WHERE `id`=$request->cpcr_id";

                    DB::connection($db_conn)->insert($sql);
                    DB::connection($db_conn)->unprepared($sql_cancel);
                    DB::connection($db_conn)->commit();

                    $success = 1;
                    $message = "Successfully cancel !!!";
                }


                return array(
                    'success' => $success,
                    'message' => $message,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function GetCashPartyCreditCollectionPendingList(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $sql_req_list = DB::connection($db_conn)->select("
                
                SELECT t1.`id`AS crcp_id,t2.id AS spbm_id,t1.`site_id`,t3.site_name,t3.site_mob1 AS site_mob,t1.`ordm_ornm`,
                t1.ordm_amnt,(t1.`sapr_amnt`-t1.`scol_amnt`)AS due_amt 
                FROM `tl_cpcr`t1 JOIN tm_scbm t2 ON(t1.`spbm_id`=t2.id)
                JOIN tm_site t3 ON(t1.`site_id`=t3.id)
                WHERE t2.aemp_id=$request->emp_id AND t1.`aemp_eusr`=$request->emp_id AND (t1.`sapr_amnt`-t1.`scol_amnt`)>0;
                ");

            return $sql_req_list;

        }
    }

    public
        function GetTrip_Summery_Details_Data(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $sql_req_list = DB::connection($db_conn)->select("
                
                SELECT t2.`site_code`,t2.site_name,t1.`ORDM_ORNM`,t1.`INV_AMNT`as challan_amt,t1.`DELV_AMNT`AS delv_amt,
                t1.`COLLECTION_AMNT`AS coll_amt,
                (t1.DELV_AMNT-t1.`COLLECTION_AMNT`) AS crdt_amt,
                (t1.`INV_AMNT`-t1.DELV_AMNT) as retn_amt,t1.`DELIVERY_STATUS`,
                t3.aemp_usnm As sr_code,t3.aemp_name as sr_name
                FROM `dm_trip_master`t1 JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
                JOIN tm_aemp t3 ON t1.`AEMP_ID`=t3.id
                WHERE t1.`TRIP_NO`='$request->trip_code';
                ");
            return $sql_req_list;
        }
    }

    public
        function GetTrip_Report_Details_Data(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $emp_id = $request->emp_id;
            $emp_code = $request->emp_code;
            $role_id = $request->role_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            if ($role_id == '10') {
                //DM
                $Coll_Data = DB::connection($db_conn)->select("
           SELECT 
t1.TRIP_NO,
t2.`site_code`,
t2.site_name,
t1.`ORDM_ORNM`,
t1.IBS_INVOICE AS Trn_No,
t1.`INV_AMNT`as challan_amt,
t1.`DELV_AMNT`AS delv_amt,
t1.`COLLECTION_AMNT`AS coll_amt,
if((t5.COLLECTION_TYPE='Cash'), t4.CRECIT_AMNT,0) AS Cash_amt,
if((t5.COLLECTION_TYPE='Cheque'), t4.CRECIT_AMNT,0) AS Cheque_amt,  
if((t5.COLLECTION_TYPE='Online'), t4.CRECIT_AMNT,0) AS Online_amt,  
round((t1.DELV_AMNT-t1.`COLLECTION_AMNT`),3) AS crdt_amt,
round((t1.`INV_AMNT`-t1.DELV_AMNT),3) as retn_amt,
t1.`DELIVERY_STATUS`,
t3.aemp_usnm As sr_code,
t3.aemp_name as sr_name
FROM `dm_trip_master`t1 JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
JOIN tm_aemp t3 ON t1.`AEMP_ID`=t3.id
LEFT JOIN dm_invoice_collection_mapp t4 ON t1.ORDM_ORNM=t4.TRANSACTION_ID
LEFT JOIN dm_collection t5 ON t4.MAP_ID=t5.COLL_NUMBER
WHERE t1.`DM_CODE`='$emp_code'
AND t1.ORDM_DRDT BETWEEN '$start_date' AND '$end_date';
            ");
            } elseif ($role_id == '1') {
                //SR
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT 
t1.TRIP_NO,
t2.`site_code`,
t2.site_name,
t6.ordm_ornm AS`ORDM_ORNM`,
t1.IBS_INVOICE AS Trn_No,
t1.`INV_AMNT`as challan_amt,
t1.`DELV_AMNT`AS delv_amt,
t1.`COLLECTION_AMNT`AS coll_amt,
if((t5.COLLECTION_TYPE='Cash'), t4.CRECIT_AMNT,0) AS Cash_amt,
if((t5.COLLECTION_TYPE='Cheque'), t4.CRECIT_AMNT,0) AS Cheque_amt,  
if((t5.COLLECTION_TYPE='Online'), t4.CRECIT_AMNT,0) AS Online_amt,  
round((t1.DELV_AMNT-t1.`COLLECTION_AMNT`),3) AS crdt_amt,
round((t1.`INV_AMNT`-t1.DELV_AMNT),3) as retn_amt,
t9.lfcl_name AS`DELIVERY_STATUS`,
t3.aemp_usnm As sr_code,
t3.aemp_name as sr_name
FROM tt_ordm t6 JOIN tm_site t2 ON t6.site_id=t2.id
JOIN tm_aemp t3 ON t6.aemp_id=t3.id
JOIN tm_lfcl t9 ON t6.lfcl_id=t9.id
LEFT JOIN `dm_trip_master`t1 ON t1.`ORDM_ORNM`=t6.ordm_ornm
LEFT JOIN dm_invoice_collection_mapp t4 ON t1.ORDM_ORNM=t4.TRANSACTION_ID
LEFT JOIN dm_collection t5 ON t4.MAP_ID=t5.COLL_NUMBER
WHERE t6.aemp_id=$emp_id 
AND t6.ordm_date BETWEEN '$start_date' AND '$end_date';
            ");
            }
            return $Coll_Data;
        }
    }

    public
        function SubmitCashPartyCollection(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";

            try {
                $sql_cpcr = DB::connection($db_conn)->select("SELECT `id` AS cpcr_id,(`sapr_amnt`-`scol_amnt`)AS due_Amt 
                FROM `tl_cpcr` 
                WHERE `spbm_id`=$request->spbm_id AND `site_id`=$request->site_id 
                AND `ordm_ornm`='$request->ordm_ornm' 
                AND (`sapr_amnt`-`scol_amnt`)>0");

                if ($sql_cpcr) {
                    $cpcr_id = $sql_cpcr[0]->cpcr_id;
                    $sql1 = "UPDATE `tl_cpcr` SET `scol_amnt`=`scol_amnt`+$request->coll_amnt WHERE `id`=$cpcr_id;";

                    $sql_cpcd = "INSERT INTO `tl_cpcd` (cpcr_id,`spbm_id`, `site_id`, `ordm_ornm`, `scol_amnt`, 
                        `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
                        VALUES ($cpcr_id,'$request->spbm_id', '$request->site_id', '$request->ordm_ornm',
                        '$request->coll_amnt', '3', '27', '1', '1')"; //27=lfcl_id cash collection
                }
                DB::connection($db_conn)->insert($sql);
                DB::connection($db_conn)->unprepared($sql1);
                DB::connection($db_conn)->insert($sql_cpcd);
                DB::connection($db_conn)->commit();

                return array(
                    'success' => 1,
                    'message' => "Successfully Save SMS ",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function SubmitTripEOT(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $mgs = "";
            $cod = 0;
            try {
                $sql_cl_ck1 = "SELECT
                (COUNT(t1.`ORDM_ORNM`))AS pending_collection1
                FROM `dm_trip_master`t1 JOIN tl_stcm t2 ON t1.`SITE_ID`=t2.site_id AND t1.`slgp_id`=t2.slgp_id
                WHERE t1.`TRIP_NO`='$request->TRIP_NO'and t2.optp_id=1 AND t1.`DELV_AMNT`>0 AND t1.`COLLECTION_AMNT`=0;";
                $pending_collection = DB::connection($db_conn)->select($sql_cl_ck1);

                $pp = $pending_collection[0]->pending_collection1;

                if ($pp > 0) {
                    $cod = 0;
                    $mgs = $pp . ' Invoice Collection Pending Please collection then Try to EOT';
                } else {
                    $ret = DB::connection($db_conn)->table('dm_trip')->where([
                        'TRIP_NO' => $request->TRIP_NO,
                        'STATUS' => 'N'
                    ])->update(['STATUS' => 'R', 'DM_ACTIVITY' => '5']);

                    if ($ret == 1) {
                        $cod = 1;
                        $mgs = "EOT Successful ";
                    } else {
                        $cod = 0;
                        $mgs = "EOT Already complete ";
                    }
                    DB::connection($db_conn)->commit();

                }
                return array(
                    'success' => $cod,
                    'message' => $mgs,
                );

            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
        function Test(
        Request $request
    ) {
        // $country = (new Country())->country($request->country_id);
        // $db_conn = $country->cont_conn;
        $DRTM = date('YmdHi');
        $DRT = date('Y-m-d');
        $now = now();
        $last_3 = substr($now->timestamp . $now->milli, 10);
        echo $DRTM . '</br>';
        echo $now->timestamp . $now->milli . '</br>';
        echo $last_3 . '</br>';
        $COLL_NUMBER = "RC" . $DRTM . $last_3;
        $day = Carbon::parse($DRT)->format('l');
        echo $day;
        /* $sql = "SET autocommit=0;";
         $sql6 = "INSERT INTO `t1`(`name`) VALUES('ab')";
         $sql1 = "update `t1`set `name`= 'raju' Where id=4";
         $sql2 = "INSERT INTO `t2`(`name`) VALUES('bb')";*/
        //  DB::connection($db_conn)->beginTransaction();

        /*try {
            DB::connection($db_conn)->unprepared($sql);
            DB::connection($db_conn)->insert($sql1);
            DB::connection($db_conn)->insert($sql2);


            DB::connection($db_conn)->commit();
            // all good
        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return $e;
        }*/

    }

    public function outletSave(Request $request) {


        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;

            $country = Country::on($db_conn)->findorfail($outletData->country_id);

           // $site_code = '5' . str_pad($country->attr4 + 1, 9, '0', STR_PAD_LEFT);
           $shop_credit_code = [
                    8 => 30,
                    9 => 70,
                    10 => 20,
                    11 => 50,
            ];
		    // $site_code = $outletData->p_type . $outletData->dsct_code . str_pad($country->attr4 + 1, 6, '0', STR_PAD_LEFT);
		    $site_code = $shop_credit_code[$outletData->Shop_Category_id] . $outletData->dsct_code . str_pad($country->attr4 + 1, 6, '0', STR_PAD_LEFT);

            DB::connection($db_conn)->beginTransaction();
            try {
                $outlet = new Outlet();
                $outlet->setConnection($db_conn);
                $outlet->oult_name = $outletData->Outlet_Name;
                $outlet->oult_code = $site_code;
                $outlet->oult_olnm = $outletData->Outlet_Name_BN;
                $outlet->oult_adrs = $outletData->Address != "" ? $outletData->Address : '';
                $outlet->oult_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                $outlet->oult_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                $outlet->oult_olon = '';
                $outlet->oult_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                $outlet->oult_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                $outlet->oult_emal = '';
                $outlet->cont_id = $outletData->country_id;
                $outlet->lfcl_id = 1;
                $outlet->aemp_iusr = $outletData->up_emp_id;
                $outlet->aemp_eusr = $outletData->up_emp_id;
                $outlet->var = 1;
                $outlet->attr1 = '-';
                $outlet->attr2 = '-';
                $outlet->attr3 = 0;
                $outlet->attr4 = 0;
                $outlet->save();
                $site = new Site();
                $site->setConnection($db_conn);
                $site->site_name = $outletData->Outlet_Name;
                $site->site_code = $site_code;
                $site->outl_id = $outlet->id;
                $site->site_olnm = $outletData->Outlet_Name_BN;
                $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                $site->mktm_id = $outletData->Market_ID;
                $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                $site->site_olon = '';
                $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                $site->site_emal = '';
                $site->scnl_id = 1;
                $site->otcg_id = $outletData->Shop_Category_id;
                $site->site_imge = $outletData->Outlet_Image;
                $site->site_omge = '';
                $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;
                ;
                $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                $site->site_reg = '';
                $site->site_hsno = '';   
                $site->site_vtrn =  $outletData->vat_trn ?? 0;
                $site->site_vsts = 1;
                $site->site_vrfy = 0;
                $site->cont_id = $outletData->country_id;
                $site->lfcl_id = 1;
                $site->aemp_iusr = $outletData->up_emp_id;
                $site->aemp_eusr = $outletData->up_emp_id;
                $site->var = 1;
                $site->attr1 = '';
                $site->attr2 = '';
                $site->attr3 = 0;
                $site->attr4 = 0;
                $site->save();

                if ($outletData->national_id != "") {
                    $site_wdov = $outletData->site_wdov ?? 0;
                    $insert[] = [
                        'site_id' => $site->id,
                        'cont_id' => $outletData->national_id,
                        'licn_no' => $outletData->trade_license,
                        'expr_date' => $outletData->tlc_expiry_date,
                        'aemp_iusr' => $outletData->up_emp_id,
                        'aemp_eusr' => $outletData->up_emp_id,
                        'site_wdov' => $site_wdov,
                    ];
                    if (!empty($insert)) {
                        DB::connection($db_conn)->table('tl_scmp')->insertOrIgnore($insert);
                    }
                }

                $tempSite = new TempSite();
                $tempSite->setConnection($db_conn);
                $tempSite->nsit_name = $outletData->Outlet_Name;
                $tempSite->nsit_code = $site_code;
                $tempSite->nsit_olnm = $outletData->Outlet_Name_BN;
                $tempSite->nsit_adrs = $outletData->Address != "" ? $outletData->Address : '';
                $tempSite->nsit_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                $tempSite->mktm_id = $outletData->Market_ID;
                $tempSite->nsit_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                $tempSite->nsit_olon = '';
                $tempSite->nsit_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                $tempSite->nsit_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                $tempSite->nsit_emal = '';
                $tempSite->scnl_id = 1;
                $tempSite->otcg_id = $outletData->Shop_Category_id;
                $tempSite->nsit_imge = $outletData->Outlet_Image;
                $tempSite->nsit_omge = '';
                $tempSite->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;
                ;
                $tempSite->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                $tempSite->nsit_reg = '';
                $tempSite->nsit_vrfy = 0;
                $tempSite->site_id = $site->id;
                $tempSite->cont_id = $outletData->country_id;
                $tempSite->lfcl_id = 1;
                $tempSite->aemp_iusr = $outletData->up_emp_id;
                $tempSite->aemp_eusr = $outletData->up_emp_id;
                $tempSite->var = 1;
                $tempSite->attr1 = '-';
                $tempSite->attr2 = '-';
                $tempSite->attr3 = 0;
                $tempSite->attr4 = 0;
                $tempSite->save();

                $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $outletData->Route_ID])->first();
                if ($routeSite == null) {
                    $routeSite = new RouteSite();
                    $routeSite->setConnection($db_conn);
                    $routeSite->site_id = $site->id;
                    $routeSite->rout_id = $outletData->Route_ID;
                    $routeSite->rspm_serl = 0;
                    $routeSite->cont_id = $outletData->country_id;
                    $routeSite->lfcl_id = 1;
                    $routeSite->aemp_iusr = $outletData->up_emp_id;
                    $routeSite->aemp_eusr = $outletData->up_emp_id;
                    $routeSite->var = 1;
                    $routeSite->attr1 = '-';
                    $routeSite->attr2 = '-';
                    $routeSite->attr3 = 0;
                    $routeSite->attr4 = 0;
                    $routeSite->save();
                }
                $country->attr4 = $country->attr4 + 1;
                $country->save();
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Outlet Created Successfully",
                    'Outlet_ID' => $site->id,
                    'Outlet_Code' => $site->site_code,
                    'Outlet_Name' => $site->site_name,
                    'Owner_Name' => $site->site_ownm,
                    'outlet_serial' => 0,
                    'Mobile_No' => $site->site_mob1,
                    'Outlet_Address' => $site->site_adrs,
                    'geo_lat' => $site->geo_lat,
                    'geo_lon' => $site->geo_lon,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }

    public
        function SavePoCopyMapping(
        Request $request
    ) {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::beginTransaction();
            try {

                if ($request->order_ornm != "") {

                    $insert[] = [
                        'site_id' => $request->site_id,
                        'ordm_ornm' => $request->order_ornm,
                        'pocm_pono' => $request->pocm_pono,
                        'pocm_link' => $request->pocm_link,
                        'date' => date('Y-m-d'),
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ];
                    $routeSite = DB::connection($db_conn)->table('tl_pocm')->where(['ordm_ornm' => $request->order_ornm])->first();
                    if ($routeSite) {
                        return array(
                            'success' => 0,
                            'message' => "PO Copy Already Uploaded !!!\nSelect Different Invoice ",
                        );
                    } else {
                        if (!empty($insert)) {
                            $ss = DB::connection($db_conn)->table('tl_pocm')->insertOrIgnore($insert);
                        }
                    }
                }

                DB::commit();
                if ($ss) {
                    return array(
                        'success' => 1,
                        'message' => "PO Copy Upload Successful",
                    );
                } else {
                    return array(
                        'success' => 0,
                        'message' => "PO Upload Failed",
                    );
                }

            } catch (Exception $e) {
                DB::rollback();
                // return $e;
                //throw $e;
                return array(
                    'success' => 0,
                    'message' => $e,
                );
            }
        }

    }

    public
        function updateOutletSerial(
        Request $request
    ) {
        $outletData = json_decode($request->Outlet_Serialize_Data)[0];
        $outletData_child = json_decode($request->Outlet_Serialize_Data);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    foreach ($outletData_child as $outletData_1) {
                        DB::connection($db_conn)->table('tl_rsmp')->where([
                            'rout_id' => $outletData_1->Route_ID,
                            'site_id' => $outletData_1->Outlet_ID
                        ])->update(['rspm_serl' => $outletData_1->Outlet_Serial]);

                    }

                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Serialized Successfully",

                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }

    public
        function SubmitChallanWiseDelivery(
        Request $request
    ) {
        $outletData = json_decode($request->delivery_data_challan_wise)[0];
        $outletData_child = json_decode($request->delivery_data_challan_wise);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    foreach ($outletData_child as $orderLineData) {

                        $orderLine = new ChallanWiseDelivery();
                        $orderLine->setConnection($db_conn);

                        $orderLine->aemp_id = $orderLineData->SR_ID;
                        $orderLine->slgp_id = $orderLineData->Group_ID;
                        $orderLine->dlrm_id = $orderLineData->Dealer_ID;
                        $orderLine->amim_id = $orderLineData->P_ID;
                        $orderLine->ordd_uprc = $orderLineData->Item_Rate;
                        $orderLine->ordd_qnty = $orderLineData->Order_P_Qty;
                        $orderLine->delv_qnty = $orderLineData->Delivery_P_Qty;
                        $orderLine->delv_amnt = $orderLineData->P_Total_Price;
                        $orderLine->ordm_amnt = $orderLineData->Order_P_Qty * $orderLineData->Item_Rate;
                        $orderLine->ordm_date = $orderLineData->Order_Date;
                        $orderLine->ordm_drdt = date('Y-m-d');

                        $orderLine->lfcl_id = 3;
                        $orderLine->cont_id = $orderLineData->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;
                        $orderLine->save();


                        DB::connection($db_conn)->table('tt_ordm')->where([
                            'aemp_id' => $orderLineData->SR_ID,
                            'ordm_date' => $orderLineData->Order_Date
                        ])->update(['lfcl_id' => 3]);


                    }
                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Delivery Successfully",
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }

    public
        function updateOutletSave(
        Request $request
    ) {
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = Site::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name;
                    $site->site_olnm = $outletData->Outlet_Name_BN;
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    /* $site->site_imge = '';
                     $site->site_omge = '';*/
                    $site->site_imge = $outletData->Outlet_Image;
                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;
                    ;
                    $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    $site->aemp_eusr = $outletData->up_emp_id;
                    $site->save();
                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Updated Successfully",
                        'Outlet_ID' => $site->id,
                        'Outlet_Code' => $site->site_code,
                        'Outlet_Name' => $site->site_name,
                        'Owner_Name' => $site->site_ownm,
                        'outlet_serial' => 0,
                        'Mobile_No' => $site->site_mob1,
                        'Outlet_Address' => $site->site_adrs,
                        'geo_lat' => $site->geo_lat,
                        'geo_lon' => $site->geo_lon,
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }


    }

    public
        function censusOutletImport(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Add on Route",
        );
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site = Site::on($db_conn)->where(['site_code' => $request->site_code])->first();
            if ($site != null) {
                $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $request->route_id])->first();
                if ($routeSite == null) {
                    $routeSite = new RouteSite();
                    $routeSite->setConnection($db_conn);
                    $routeSite->site_id = $site->id;
                    $routeSite->rout_id = $request->route_id;
                    $routeSite->rspm_serl = 0;
                    $routeSite->cont_id = $request->country_id;
                    $routeSite->lfcl_id = 1;
                    $routeSite->aemp_iusr = $request->up_emp_id;
                    $routeSite->aemp_eusr = $request->up_emp_id;
                    $routeSite->var = 1;
                    $routeSite->attr1 = '';
                    $routeSite->attr2 = '';
                    $routeSite->attr3 = 0;
                    $routeSite->attr4 = 0;
                    $routeSite->save();

                    $result_data = array(
                        'success' => 1,
                        'message' => "Adding Successful",
                        'Outlet_ID' => $site->id,
                        'Outlet_Code' => $site->site_code,
                        'Outlet_Name' => $site->site_name,
                        'Owner_Name' => $site->site_ownm,
                        'outlet_serial' => 0,
                        'Mobile_No' => $site->site_mob1,
                        'Outlet_Address' => $site->site_adrs,
                        'geo_lat' => $site->geo_lat,
                        'geo_lon' => $site->geo_lon,
                    );
                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "Already Exits",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "Site Not Exits In System",
                );
            }
        }
        return $result_data;
    }


    public
        function attendanceSave_new(
        Request $request
    ) {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                /* $path_name = $country->cont_imgf . '/' . date('Y-m-d') . '/attendance/' . uniqid() . '.jpg';
                 if (isset($request['OutLet_Picture'])) {
                     $s3->putObject(array(
                         'Bucket' => 'prgfms',
                         'Key' => $path_name,
                         'Body' => base64_decode($request->OutLet_Picture),
                         'ContentType' => 'base64',
                         'ContentEncoding' => 'image/jpeg',
                         'ACL' => 'public-read',
                     ));
                 }*/
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = $request->Group_Id;
                ; //$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID; // $request->SR_ID;
                $attendance->site_id = 2;
                $attendance->site_name = '';
                $attendance->attn_imge = $request->OutLet_Picture;
                $attendance->geo_lat = explode(',', $request->Location)[0];
                $attendance->geo_lon = explode(',', $request->Location)[1];
                $attendance->attn_time = $request->Date_Time;
                $attendance->attn_date = $request->Date_Time;
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->Status;
                $attendance->rout_id = $request->Route_ID;
                $attendance->attn_fdat = $request->From_Date == "" ? date('Y-m-d') : $request->From_Date;
                $attendance->attn_tdat = $request->To_Date == "" ? date('Y-m-d') : $request->To_Date;
                $attendance->attn_rmak = $request->Lv_IOM_Remarks == "" ? '' : $request->Lv_IOM_Remarks;
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::commit();
                return array('column_id' => $request->ID);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public
        function attendanceSave(
        Request $request
    ) {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                $path_name = $country->cont_imgf . '/' . date('Y-m-d') . '/attendance/' . uniqid() . '.jpg';
                if (isset($request['OutLet_Picture'])) {
                    $s3->putObject(
                        array(
                            'Bucket' => 'prgfms',
                            'Key' => $path_name,
                            'Body' => base64_decode($request->OutLet_Picture),
                            'ContentType' => 'base64',
                            'ContentEncoding' => 'image/jpeg',
                            'ACL' => 'public-read',
                        )
                    );
                }
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = $request->Group_Id;
                ; //$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID; // $request->SR_ID;
                $attendance->site_id = 2;
                $attendance->site_name = '';
                $attendance->attn_imge = $path_name;
                $attendance->geo_lat = explode(',', $request->Location)[0];
                $attendance->geo_lon = explode(',', $request->Location)[1];
                $attendance->attn_time = $request->Date_Time;
                $attendance->attn_date = $request->Date_Time;
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->Status;
                $attendance->rout_id = $request->Route_ID;
                $attendance->attn_fdat = $request->From_Date == "" ? date('Y-m-d') : $request->From_Date;
                $attendance->attn_tdat = $request->To_Date == "" ? date('Y-m-d') : $request->To_Date;
                $attendance->attn_rmak = $request->Lv_IOM_Remarks == "" ? '' : $request->Lv_IOM_Remarks;
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::commit();
                return array('column_id' => $request->ID);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public
        function MasterData(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t1.rpln_day                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
WHERE t1.aemp_id = $request->emp_id and t1.rpln_day=dayname(curdate()) ");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id  and t1.rpln_day=dayname(curdate())
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt ");

            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");

            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.rson_name) AS column_id,
  concat(t1.id, t1.rson_name) AS token,
  t1.id                       AS Reason_id,
  t1.rson_name                AS Reason_Name
FROM tm_rson AS t1");

            $data6 = DB::connection($db_conn)->select("
SELECT
concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
  t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm AS buy_item_code,
  t5.prdt_mbqt as max_buy_qty,
  t5.prdt_mnbt as min_buy_qyt,
  t5.prdt_fitm as free_item_code,
  t5.prdt_fiqt as free_item_qty,
  round(t5.prdt_fipr,2) as free_item_price,
  t5.prdt_disc as discount_percente,
  t4.prom_edat as end_date
from tl_srgb as t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id 
and t2.prom_nztp=0 
and t2.prom_sdat <=CURDATE()
AND t2.prom_edat >=CURDATE()
");
        }


        return array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
        function MasterDataNew(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t1.rpln_day                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id

WHERE t1.aemp_id = $request->emp_id ");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id 
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");

            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");

            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data6 = DB::connection($db_conn)->select("
SELECT
concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
  t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm AS buy_item_code,
  t5.prdt_mbqt as max_buy_qty,
  t5.prdt_mnbt as min_buy_qyt,
  t5.prdt_fitm as free_item_code,
  t5.prdt_fiqt as free_item_qty,
  round(t5.prdt_fipr,2) as free_item_price,
  t5.prdt_disc as discount_percente,
  t4.prom_edat as end_date
from tl_srgb as t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id 
and t2.prom_nztp=0 
and t2.prom_sdat <=CURDATE()
AND t2.prom_edat >=CURDATE()
");
        }


        return array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
        function MasterDataNew_Three(
        Request $request
    ) {
        $data1 = array();
        $data4 = array();
        $data5 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            /* $data1 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
    t1.aemp_id                                                                                 AS SR_ID,
    t2.slgp_id                                                                                 AS Group_ID,
    t1.rout_id                                                                                 AS Route_ID,
    t3.rout_name                                                                               AS Route_Name,
    t4.id                                                                                      AS Base_Code,
    t4.base_name                                                                               AS Base_Name,
    t1.rpln_day                                                                                AS Day,
    t5.acmp_id                                                                                AS Ou
    FROM tl_rpln AS t1
    INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
    INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
    INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
    INNER JOIN tm_slgp AS t5 ON t2.slgp_id = t5.id
    WHERE t1.aemp_id = $request->emp_id
    ");*/

            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t1.rpln_day                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
WHERE t1.aemp_id = $request->emp_id 
");
            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  LEFT JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  LEFT JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");
            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");
        }
        return array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
        );
    }

    public
        function GetPromoSlabDetails(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT DISTINCT t1.`prsb_fqty`AS From_QTY,
t1.`prsb_tqty`AS To_QTY,
t1.`prsb_disc`AS Given_QTY,
t1.`prsb_mosl`AS Pro_Modifier_id_sl,
'0'AS Unit_Factor,'0'AS Given_Unit_Factor
FROM `tm_prsb` t1 JOIN tm_prmd t2 ON(t1.`prmr_id`=t2.prmr_id)
LEFT JOIN tm_prmr t3 ON(t3.id=t2.prmr_id)
WHERE t1.`prmr_id`='$promo_id' AND t3.prms_edat>=CURDATE()
 AND t3.prms_sdat<=CURDATE()
 GROUP BY t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_disc`,t1.`prsb_mosl`
  ");
            $data2 = DB::connection($db_conn)->select("
SELECT DISTINCT t2.amim_id AS Buy_Item_id,
t3.amim_code AS Buy_Item_ID,
t3.amim_name AS Buy_Item_Name
FROM `tm_prmr` t1 JOIN tm_prmd t2 ON(t1.`id`=t2.prmr_id)
LEFT JOIN tm_amim t3 ON(t3.id=t2.`amim_id`)
WHERE t2.`prmr_id`='$promo_id' AND t1.prms_edat>=CURDATE()
AND t1.prms_sdat<=CURDATE()
GROUP BY t2.amim_id,t3.amim_code,t3.amim_name
  ");

        }

        return array(
            "Buy" => array("data" => $data2, "action" => $request->country_id),
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public
        function GetPromoSingleFOCSlabDetails(
        Request $request
    ) {
        $data1 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT t3.amim_id AS Buy_Item_ID,t2.amim_id AS Free_Item_ID,t1.`prsb_fqty`AS From_QTY,
t1.`prsb_tqty`AS To_QTY,t1.`prsb_qnty`AS Given_QTY,t1.`prsb_mosl`AS Pro_Modifier_id_sl 
FROM `tm_prsb`t1 JOIN tm_prmf t2 ON(t1.`prmr_id`=t2.prmr_id) 
LEFT JOIN tm_prmd t3 ON(t2.prmr_id=t3.prmr_id)
WHERE t3.`prmr_id`='$promo_id'
GROUP BY t3.amim_id,t2.amim_id,t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_qnty`,t1.`prsb_mosl`
  ");


        }

        return array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public
        function GetItemOrderHistory(
        Request $request
    ) {
        $data1 = array();

        $site_id = $request->site_id;
        $item_id = $request->item_id;
        $ou_id = $request->ou_id;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT t1.`id` AS ORDER_Id ,
 t1.`ordm_ornm` AS ORDER_Code,
 (t2.ordd_dqty-t2.ordd_rqty)AS Product_order_qty,
((t2.ordd_dpds+t2.ordd_spdd+t2.ordd_dfdd)/t2.ordd_dqty)AS discount_ratio,
t2.ordd_uprc AS Rate,
t2.ordd_excs AS Gst_percent,
t2.ordd_ovat AS vat_percent
FROM `tt_ordm` t1 JOIN tt_ordd t2 ON(t1.`id`=t2.ordm_id)
WHERE t1.`site_id`='$site_id'AND t1.`lfcl_id` IN(11,39)
AND t1.acmp_id='$ou_id'AND t2.amim_id='$item_id' 
AND (t2.ordd_dqty-t2.ordd_rqty)>0
ORDER BY t1.`id` DESC
  ");
        }
        return array(
            "GRV_Refer_Data" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public
        function GetPromoFOCSlabDetails(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT DISTINCT t1.`prsb_fqty`AS From_QTY,
t1.`prsb_tqty`AS To_QTY,
t1.`prsb_qnty`AS Given_QTY,
t1.`prsb_mosl`AS Pro_Modifier_id_sl,
'0'AS Unit_Factor,'0'AS Given_Unit_Factor
FROM `tm_prsb` t1 JOIN tm_prmd t2 ON(t1.`prmr_id`=t2.prmr_id)
LEFT JOIN tm_prmr t3 ON(t3.id=t2.prmr_id)
WHERE t1.`prmr_id`='$promo_id' AND t3.prms_edat>=CURDATE()
 AND t3.prms_sdat<=CURDATE()
 GROUP BY t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_qnty`,t1.`prsb_mosl`
  ");
            $data2 = DB::connection($db_conn)->select("
SELECT t2.amim_code as Free_Item_ID,t2.amim_name as Free_Item_Name
FROM `tm_prmf` t1 JOIN tm_amim t2 ON(t1.`amim_id`=t2.id)
WHERE t1.`prmr_id`='$promo_id'
GROUP BY t2.amim_code,t2.amim_name
  ");
            $data3 = DB::connection($db_conn)->select("
SELECT DISTINCT t2.amim_id AS Buy_Item_id,
t3.amim_code AS Buy_Item_ID,
t3.amim_name AS Buy_Item_Name
FROM `tm_prmr` t1 JOIN tm_prmd t2 ON(t1.`id`=t2.prmr_id)
LEFT JOIN tm_amim t3 ON(t3.id=t2.`amim_id`)
WHERE t2.`prmr_id`='$promo_id' AND t1.prms_edat>=CURDATE()
AND t1.prms_sdat<=CURDATE()
GROUP BY t2.amim_id,t3.amim_code,t3.amim_name
  ");

        }

        return array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
            "Free" => array("data" => $data2, "action" => $request->country_id),
            "Buy" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public
        function CheckINSyncAllData(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT 
concat(t3.amim_code, $request->site_id) AS column_id,
t3.amim_code             AS Item_Code,
t2.amim_id               AS Item_ID,
t2.pldt_tppr             AS Item_Price,
t2.amim_duft             AS Item_Factor
FROM `tl_stcm` t1 JOIN tm_pldt t2 ON(t1.`plmt_id`=t2.plmt_id)
LEFT JOIN tm_amim t3 ON(t2.amim_id=t3.id)
WHERE t1.acmp_id='$request->ou_id'
AND t1.site_id='$request->site_id'
AND t3.lfcl_id='1'
GROUP BY t3.amim_code,t2.amim_id,t2.pldt_tppr,t2.amim_duft
");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.amim_code, $request->site_id)                                             AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Id,
  t3.amim_code                                                                        AS Item_Code,
  t5.issc_name                                                                        AS Category_Name,
  t3.amim_name                                                                        AS Item_Name,
  t2.pldt_tppr                                                                        AS Item_Rate,
  t2.pldt_tpgp                                                                        AS Grv_Item_Price,
  (t3.amim_pexc*100)/(t2.pldt_tppr*t2.amim_duft)                                      as Item_gst,
  t3.amim_pvat                                                                        as Item_vat,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS category_Showing_seqn,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  LEFT JOIN tl_stcm t6 ON(t2.plmt_id=t6.plmt_id)
WHERE t3.lfcl_id = '1' 
AND t1.aemp_id = '$request->emp_id'
 AND t6.site_id='$request->site_id' 
 AND t6.acmp_id='$request->ou_id'
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr,pldt_tpgp, 
t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code,t3.amim_pvat  
");
            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.amim_code, $request->site_id)               AS column_id,
  t3.id                                                 AS Item_Id,
  t3.amim_code                                          AS Item_Code,
  if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis,
  if(t9.prmr_id > 0, t9.prmr_id, 'null')                AS Promotion_ID
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
  LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
  LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
  LEFT JOIN (
              SELECT
                t3.amim_id,
                t1.prmr_id
              FROM tl_prsm t1
                INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                INNER JOIN tm_prmd t3 ON (t3.`prmr_id` = t2.id)
              WHERE t1.site_id = '$request->site_id'  AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat
            ) AS t9 ON t2.amim_id = t9.amim_id
WHERE t3.lfcl_id = '1'
      AND t1.aemp_id = '$request->emp_id'
      AND t6.site_id = '$request->site_id' 
      AND t6.acmp_id = '$request->ou_id'
GROUP BY t3.id, t3.amim_code, t8.dfim_disc, t9.prmr_id
");


            $data4 = DB::connection($db_conn)->select("
SELECT 
concat(t1.`id` , $request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`)    AS column_id,
t1.`id`                                    AS Promotion_ID,
t1.`prmr_ctgp`                             AS FOC_Type_Single_Multiple,
t1.`prmr_qfct`                             AS promo_type,
t1.`prmr_ditp`                             AS discount_type, 
t1.`prmr_qfon`                             AS FOC_CRT_PCS_Type 
FROM `tm_prmr` t1 JOIN tl_prsm t2 ON(t1.id=t2.prmr_id)
WHERE t2.site_id='$request->site_id' and t1.prms_edat>=CURDATE() AND t1.prms_sdat<=CURDATE()
");
            $data5 = DB::connection($db_conn)->select("
 SELECT 
 concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
 t1.`mspm_id`AS MSP_ID,
 t4.amim_code AS MSP_Item_ID,
 t1.`mspd_qnty`AS MSP_Item_Qty,
 '2' AS Status_ID
FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
WHERE t3.site_id='$request->site_id' 
AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`
 ");
            $data6 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
  SELECT
    concat(t1.id, t1.dprt_name) AS column_id,
    concat(t1.id, t1.dprt_name) AS token,
    t1.id                       AS Reason_id,
    t1.dprt_name                AS Reason_Name
  FROM tm_dprt AS t1
  ");

        }
        return array(
            "Sync_t_prices_detail" => array("data" => $data1, "action" => $request->country_id),
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "Sync_T_Promotion_Table" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public
        function CheckINSyncAllData_Image(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            /* $data1 = DB::connection($db_conn)->select("
    SELECT
    concat(t3.amim_code, $request->site_id) AS column_id,
    t3.amim_code             AS Item_Code,
    t2.amim_id               AS Item_ID,
    t2.pldt_tppr             AS Item_Price,
    t2.amim_duft             AS Item_Factor
    FROM `tl_stcm` t1 JOIN tm_pldt t2 ON(t1.`plmt_id`=t2.plmt_id)
    LEFT JOIN tm_amim t3 ON(t2.amim_id=t3.id)
    WHERE t1.acmp_id='$request->ou_id'
    AND t1.site_id='$request->site_id'
    AND t3.lfcl_id='1'
    GROUP BY t3.amim_code,t2.amim_id,t2.pldt_tppr,t2.amim_duft
    ");*/
            $data1 = DB::connection($db_conn)->select("
SELECT concat(t1.amim_code, $request->site_id) AS column_id,
t1.amim_code             AS Item_Code,
t5.amim_id               AS Item_ID,
t5.pldt_tppr             AS Item_Price,
t5.amim_duft             AS Item_Factor 

From `tm_amim` t1
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tl_stcm t4 ON t3.plmt_id = t4.plmt_id AND t4.slgp_id=$request->slgp_id
INNER JOIN tm_pldt AS t5 ON t4.plmt_id = t5.plmt_id  AND t5.amim_id = t2.amim_id
WHERE t1.lfcl_id = 1 AND t3.aemp_id = $request->emp_id AND t4.acmp_id=$request->ou_id AND t4.site_id = $request->site_id  
GROUP BY t1.amim_code,t5.amim_id,t5.pldt_tppr,t5.amim_duft
");

            /* $data2 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.amim_code, $request->site_id)              AS column_id,
    t3.plmt_id                                           AS Item_Price_List,
    t3.plmt_id                                           AS Grv_Item_Price_List,
    t1.id                                                AS Item_Id,
    t1.amim_code                                         AS Item_Code,
    t1.amim_imgl                                         AS amim_imgl,
    t6.issc_name                                         AS Category_Name,
    t1.amim_name                                         AS Item_Name,
    t2.pldt_tppr                                         AS Item_Rate,
    t2.pldt_tpgp                                         AS Grv_Item_Price,
    (t1.amim_pexc * 100) / (t2.pldt_tppr * t2.amim_duft) AS Item_gst,
    t1.amim_pvat                                         AS Item_vat,
    t2.amim_duft                                         AS Item_Factor,
    t6.issc_seqn                                         AS category_Showing_seqn,
    t2.amim_dunt                                         AS D_Unit,
    t2.amim_runt                                         AS R_Unit
    FROM tm_amim AS t1
    INNER JOIN tm_pldt AS t2 ON t1.id = t2.amim_id
    INNER JOIN tl_stcm AS t3 ON t2.plmt_id = t3.plmt_id
    INNER JOIN tl_sgit AS t4 ON t1.id = t4.amim_id
    INNER JOIN tl_sgsm AS t5 ON t4.slgp_id = t5.slgp_id
    INNER JOIN tm_issc AS t6 ON t4.issc_id = t6.id
    WHERE t3.site_id = '$request->site_id' AND t3.acmp_id = '$request->ou_id' AND t5.aemp_id = '$request->emp_id'
    "); */

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t1.amim_code, $request->site_id)              AS column_id,
  t5.plmt_id                                           AS Item_Price_List,
  t5.plmt_id                                           AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  t4.issc_name                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t6.pldt_tppr                                         AS Item_Rate,
  t6.pldt_tpgp                                         AS Grv_Item_Price,
  (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t6.amim_duft                                         AS Item_Factor,
  t4.issc_seqn                                         AS category_Showing_seqn,
  t6.amim_dunt                                         AS D_Unit,
  t6.amim_runt                                         AS R_Unit

FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id AND t5.slgp_id=$request->slgp_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id  AND t5.site_id = $request->site_id AND t5.acmp_id = $request->ou_id 
");


            /* $data3 = DB::connection($db_conn)->select("
    SELECT
    concat(t3.amim_code, $request->site_id)               AS column_id,
    t3.id                                                 AS Item_Id,
    t3.amim_code                                          AS Item_Code,
    if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis,
    if(t9.prmr_id > 0, t9.prmr_id, 'null')                AS Promotion_ID
    FROM tl_sgsm AS t1
    INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
    INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
    INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
    INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
    INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
    LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
    LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
    LEFT JOIN (
               SELECT
                 t3.amim_id,
                 t1.prmr_id
               FROM tl_prsm t1
                 INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                 INNER JOIN tm_prmd t3 ON (t3.`prmr_id` = t2.id)
               WHERE t1.site_id = '$request->site_id'  AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat
             ) AS t9 ON t2.amim_id = t9.amim_id
    WHERE t3.lfcl_id = '1'
       AND t1.aemp_id = '$request->emp_id'
       AND t6.site_id = '$request->site_id'
       AND t6.acmp_id = '$request->ou_id'
    GROUP BY t3.id, t3.amim_code, t8.dfim_disc, t9.prmr_id
    ");*/

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.amim_code, $request->site_id)               AS column_id,
  t3.id                                                 AS Item_Id,
  t3.amim_code                                          AS Item_Code,
  if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis,
  if(t9.prmr_id > 0, t9.prmr_id, 'null')                AS Promotion_ID
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id) AND t6.slgp_id=$request->slgp_id
  LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
  LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
  LEFT JOIN (
              SELECT
                t3.amim_id,
                t1.prmr_id
              FROM tl_prsm t1
                INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                INNER JOIN tm_prmd t3 ON (t3.`prmr_id` = t2.id)
              WHERE t1.site_id = $request->site_id  AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat
            ) AS t9 ON t2.amim_id = t9.amim_id
  
WHERE t3.lfcl_id = 1
      AND t1.aemp_id = $request->emp_id
      AND t6.site_id = $request->site_id 
      AND t6.acmp_id = $request->ou_id
GROUP BY t3.id, t3.amim_code, t8.dfim_disc, t9.prmr_id
");


            $data4 = DB::connection($db_conn)->select("
SELECT 
concat(t1.`id` , $request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`)    AS column_id,
t1.`id`                                    AS Promotion_ID,
t1.`prmr_ctgp`                             AS FOC_Type_Single_Multiple,
t1.`prmr_qfct`                             AS promo_type,
t1.`prmr_ditp`                             AS discount_type, 
t1.`prmr_qfon`                             AS FOC_CRT_PCS_Type 
FROM `tm_prmr` t1 JOIN tl_prsm t2 ON(t1.id=t2.prmr_id)
WHERE t2.site_id='$request->site_id' and t1.prms_edat>=CURDATE() AND t1.prms_sdat<=CURDATE()
");
            $data5 = DB::connection($db_conn)->select("
 SELECT 
 concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
 t1.`mspm_id`AS MSP_ID,
 t4.amim_code AS MSP_Item_ID,
 t1.`mspd_qnty`AS MSP_Item_Qty,
 '2' AS Status_ID
FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
WHERE t3.site_id='$request->site_id' 
AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`
 ");
            $data6 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
  SELECT
    concat(t1.id, t1.dprt_name) AS column_id,
    concat(t1.id, t1.dprt_name) AS token,
    t1.id                       AS Reason_id,
    t1.dprt_name                AS Reason_Name
  FROM tm_dprt AS t1
  ");

        }
        return array(
            "Sync_t_prices_detail" => array("data" => $data1, "action" => $request->country_id),
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "Sync_T_Promotion_Table" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public
        function fetchMarketOutProduct(
        Request $request
    ) {

        $sql = array();

        $Search_key = $request->search_item;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {


            /*$sql = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.amim_code, $request->site_id)              AS column_id,
                            t5.plmt_id                                           AS Item_Price_List,
                            t5.plmt_id                                           AS Grv_Item_Price_List,
                            t1.id                                                AS Item_Id,
                            t1.amim_code                                         AS Item_Code,
                            t1.amim_imgl                                         AS amim_imgl,
                            t4.issc_name                                         AS Category_Name,
                            t1.amim_name                                         AS Item_Name,
                            t6.pldt_tppr                                         AS Item_Rate,
                            t6.pldt_tpgp                                         AS Grv_Item_Price,
                            (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                            t1.amim_pvat                                         AS Item_vat,
                            t6.amim_duft                                         AS Item_Factor,
                            t4.issc_seqn                                         AS category_Showing_seqn,
                            t6.amim_dunt                                         AS D_Unit,
                            t6.amim_runt                                         AS R_Unit
                            FROM `tm_amim`t1
                            INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                            INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
                            INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                            INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id AND t5.slgp_id=$request->slgp_id
                            INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                            WHERE  t1.lfcl_id=2 AND t3.aemp_id = $request->emp_id
                            AND t5.site_id = $request->site_id
                            AND t1.amim_name LIKE'%$Search_key%'
                    "); */

            $sql = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.amim_code, 1)                              AS column_id,
                            1                                                    AS Item_Price_List,
                            1                                                    AS Grv_Item_Price_List,
                            t1.id                                                AS Item_Id,
                            t1.amim_code                                         AS Item_Code,
                            t1.amim_imgl                                         AS amim_imgl,
                            t1.amim_name                                         AS Category_Name,
                            t1.amim_name                                         AS Item_Name,
                            t1.amim_tppr                                         AS Item_Rate,
                            t1.amim_tppr                                         AS Grv_Item_Price,
                            (t1.amim_pexc * 100) / (t1.amim_tppr * t1.amim_duft) AS Item_gst,
                            t1.amim_pvat                                         AS Item_vat,
                            t1.amim_duft                                         AS Item_Factor,
                            1                                                    AS category_Showing_seqn,
                            t1.amim_dunt                                         AS D_Unit,
                            t1.amim_runt                                         AS R_Unit
                            FROM `tm_amim`t1                                            
                            WHERE  t1.amim_name LIKE'%$Search_key%'                           
                    ");

            return array(
                "productList" => $sql
            );
        }
    }

    public function CheckINSyncAllData_Merge(Request $request) {

        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();
        $data9 = array();
        $data10 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            if ($request->country_id == 15) {
                $data2 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.amim_code, $request->site_id)              AS column_id,
                            t5.plmt_id                                           AS Item_Price_List,
                            t5.plmt_id                                           AS Grv_Item_Price_List,
                            t1.id                                                AS Item_Id,
                            t1.amim_code                                         AS Item_Code,
                            t1.amim_imgl                                         AS amim_imgl,
                            t1.amim_imic                                         AS amim_imic,
                            t4.issc_name                                         AS Category_Name,
                            t1.amim_name                                         AS Item_Name,
                            t6.pldt_tppr                                         AS Item_Rate,
                            t6.pldt_tpgp                                         AS Grv_Item_Price,
                            (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                            t1.amim_pvat                                         AS Item_vat,
                            t6.amim_duft                                         AS Item_Factor,
                            t4.issc_seqn                                         AS category_Showing_seqn,
                            t6.amim_dunt                                         AS D_Unit,
                            t6.amim_runt                                         AS R_Unit,
                            t2.attr4                                             AS min_oqty,
                            if(t9.amim_id IS NULL,true,false)                    AS is_special_dis

                            FROM `tm_amim`t1 
                            INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                            INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id AND t3.slgp_id=$request->slgp_id
                            INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                            INNER JOIN tl_stcm t5 ON t3.slgp_id = t5.slgp_id AND t5.slgp_id=$request->slgp_id
                            INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                            LEFT JOIN tt_outs t8 ON(t6.amim_id=t8.amim_id) AND t8.dpot_id=$request->dpot_id
                            LEFT JOIN tl_npit t9 ON(t2.amim_id=t9.amim_id) AND t2.slgp_id=t9.slgp_id

                            JOIN tl_srdi t77 ON(t3.aemp_id=t77.aemp_id) AND t77.dlrm_id=$request->dpot_id
                            JOIN tm_dlrm t99 ON t77.dlrm_id=t99.id


                            WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id  AND t5.site_id = $request->site_id 
                            AND t5.acmp_id = $request->ou_id AND t8.id IS NULL
                            ");
            } 
            else {
                $data2 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.amim_code, $request->site_id)              AS column_id,
                            t5.plmt_id                                           AS Item_Price_List,
                            t5.plmt_id                                           AS Grv_Item_Price_List,
                            t1.id                                                AS Item_Id,
                            t1.amim_code                                         AS Item_Code,
                            t1.amim_imgl                                         AS amim_imgl,
                            t1.amim_imic                                         AS amim_imic,
                            t4.issc_name                                         AS Category_Name,
                            t1.amim_name                                         AS Item_Name,
                            t6.pldt_tppr                                         AS Item_Rate,
                            t6.pldt_tpgp                                         AS Grv_Item_Price,
                            (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                            t1.amim_pvat                                         AS Item_vat,
                            t6.amim_duft                                         AS Item_Factor,
                            t4.issc_seqn                                         AS category_Showing_seqn,
                            t6.amim_dunt                                         AS D_Unit,
                            t6.amim_runt                                         AS R_Unit,
                            t2.attr4                                             AS min_oqty,
                            if(t9.amim_id IS NULL,true,false)                    AS is_special_dis

                            FROM `tm_amim`t1 
                            INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                            INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id AND t3.slgp_id=$request->slgp_id
                            INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                            INNER JOIN tl_stcm t5 ON t3.slgp_id = t5.slgp_id AND t5.slgp_id=$request->slgp_id
                            INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                            LEFT JOIN tt_outs t8 ON(t6.amim_id=t8.amim_id) AND t8.dpot_id=$request->dpot_id
                            LEFT JOIN tl_npit t9 ON(t2.amim_id=t9.amim_id) AND t2.slgp_id=t9.slgp_id

                            JOIN tl_srdi t77 ON(t3.aemp_id=t77.aemp_id) AND t77.dlrm_id=$request->dpot_id
                            JOIN tm_dlrm t99 ON t77.dlrm_id=t99.id
                            JOIN DEPOT_STOCK t10 ON t99.dlrm_code=t10.DEPOT_ID AND t6.amim_code = t10.DEPOT_ITEM

                            WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id AND t10.DEPOT_B>0 AND t5.site_id = $request->site_id 
                            AND t5.acmp_id = $request->ou_id AND t8.id IS NULL
                            ");
            }

            if ($request->country_id == 14) {
                $data4 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                            t1.id                                                AS promo_id,
                            t1.prms_name                                         AS promo_name,
                            t1.prms_sdat                                         AS start_date,
                            t1.prms_edat                                         AS end_date,
                            t1.prmr_qfct                                         AS qualifier_category,
                            t1.prmr_ditp                                         AS discount_type,
                            t1.prmr_ctgp                                         AS category_group,
                            t1.prmr_qfon                                         AS qualifier_on,
                            t1.prmr_qfln                                         AS qualifier_line
                            FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                            WHERE t2.site_id = $request->site_id  AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                            GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                            ");

                $data8 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                            t1.prmr_id                                                            AS promo_id,
                            t1.amim_id                                                            AS product_id,
                            t1.prmd_modr                                                          AS pro_modifier_id
                            FROM tm_prmd AS t1
                            INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                            INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                            WHERE t3.site_id =$request->site_id  AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                            and t1.amim_id IN(SELECT t11.amim_id FROM `tl_sgit` t11 WHERE `slgp_id` = $request->slgp_id)
                            GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");
            } 
            else {
                $data4 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                            t1.id                                                AS promo_id,
                            t1.prms_name                                         AS promo_name,
                            t1.prms_sdat                                         AS start_date,
                            t1.prms_edat                                         AS end_date,
                            t1.prmr_qfct                                         AS qualifier_category,
                            t1.prmr_ditp                                         AS discount_type,
                            t1.prmr_ctgp                                         AS category_group,
                            t1.prmr_qfon                                         AS qualifier_on,
                            t1.prmr_qfln                                         AS qualifier_line
                            FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                            WHERE t2.site_id = $request->site_id AND t1.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                            GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                            ");

                $data8 = DB::connection($db_conn)->select("
                            SELECT
                            concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                            t1.prmr_id                                                            AS promo_id,
                            t1.amim_id                                                            AS product_id,
                            t1.prmd_modr                                                          AS pro_modifier_id
                            FROM tm_prmd AS t1
                            INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                            INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                            WHERE t3.site_id =$request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                            and t1.amim_id IN(SELECT t11.amim_id FROM `tl_sgit` t11 WHERE `slgp_id` = $request->slgp_id)
                            GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");
            }


            $data3 = DB::connection($db_conn)->select("
                        SELECT
                        concat(t1.amim_code, $request->site_id )              AS column_id,
                        t1.id                                                 AS Item_Id,
                        t1.amim_code                                          AS Item_Code,
                        if(t2.dfim_disc > 0, t2.dfim_disc, 0)                 AS Item_Default_Dis
                        FROM tm_amim t1 JOIN tm_dfim AS t2 ON (t1.id = t2.amim_id) 
                        JOIN tl_dfsm AS t3 ON t3.dfdm_id = t2.dfdm_id 
                        JOIN tm_dfdm AS t4 ON t4.id = t3.dfdm_id 
                        WHERE t1.lfcl_id = 1 AND t3.slgp_id=$request->slgp_id
                            AND t3.site_id = $request->site_id 
                            AND (curdate() BETWEEN t4.start_date AND t4.end_date) AND t4.lfcl_id = 1
                        GROUP BY t1.id, t1.amim_code, t2.dfim_disc;
                        ");


            // $data4 = DB::connection($db_conn)->select("
                        // SELECT
                                    // concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                                    // t1.id                                                AS promo_id,
                                    // t1.prms_name                                         AS promo_name,
                                    // t1.prms_sdat                                         AS start_date,
                                    // t1.prms_edat                                         AS end_date,
                                    // t1.prmr_qfct                                         AS qualifier_category,
                                    // t1.prmr_ditp                                         AS discount_type,
                                    // t1.prmr_ctgp                                         AS category_group,
                                    // t1.prmr_qfon                                         AS qualifier_on,
                                    // t1.prmr_qfln                                         AS qualifier_line
                        // FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                        // WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                        // GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                        // ");
            $data5 = DB::connection($db_conn)->select("
                        SELECT 
                        concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
                        t1.`mspm_id`AS MSP_ID,
                        t1.amim_id AS MSP_Item_ID,
                        t1.`mspd_qnty`AS MSP_Item_Qty,
                        '2' AS Status_ID
                        FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
                        LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id) 
                        LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
                        WHERE t3.site_id='$request->site_id' AND t3.slgp_id=$request->slgp_id
                        AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
                        GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`,t1.amim_id
                        ");
            $data6 = DB::connection($db_conn)->select("
                        SELECT
                        concat(t1.id, t1.nopr_name) AS column_id,
                        concat(t1.id, t1.nopr_name) AS token,
                        t1.id                       AS Reason_id,
                        t1.nopr_name                AS Reason_Name
                        FROM tm_nopr AS t1");

                                    $data7 = DB::connection($db_conn)->select("
                        SELECT
                            concat(t1.id, t1.dprt_name) AS column_id,
                            concat(t1.id, t1.dprt_name) AS token,
                            t1.id                       AS Reason_id,
                            t1.dprt_name                AS Reason_Name
                        FROM tm_dprt AS t1
                        ");
            // $data8 = DB::connection($db_conn)->select("
                                    // SELECT
                                    // concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                                    // t1.prmr_id                                                            AS promo_id,
                                    // t1.amim_id                                                            AS product_id,
                                    // t1.prmd_modr                                                          AS pro_modifier_id
                                    // FROM tm_prmd AS t1
                                    // INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                    // INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        // WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        // and t1.amim_id IN(SELECT t11.amim_id FROM `tl_sgit` t11 WHERE `slgp_id` = $request->slgp_id)
                        // GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                                    // ");


            $data9 = DB::connection($db_conn)->select("
                        SELECT
                        concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
                        t1.prmr_id                                                                            AS promo_id,
                        t1.prsb_text                                                                          AS promo_slab_text,
                        t1.prsb_fqty                                                                          AS from_qty,
                        t1.prsb_tqty                                                                          AS to_qty,
                        0                                                                                     AS unit,
                        0                                                                                     AS unit_factor,
                        t1.prsb_famn                                                                          AS from_amnt,
                        t1.prsb_tamn                                                                          AS to_amnt,
                        t1.prsb_qnty                                                                          AS qty,
                        0                                                                                     AS given_unit,
                        0                                                                                     AS given_unit_factor,
                        t1.prsb_disc                                                                          AS discount,
                        t1.prsb_modr                                                                          AS pro_modifier_id,
                        t1.prsb_mosl                                                                          AS pro_modifier_id_sl
                        FROM tm_prsb AS t1
                        INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                        INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        WHERE t3.site_id = $request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
                        t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
                        ");

            $data10 = DB::connection($db_conn)->select("
                        SELECT
                        concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                        t1.prmr_id                                                            AS promo_id,
                        t1.amim_id                                                            AS product_id,
                        t1.prmd_modr                                                          AS pro_modifier_id
                        FROM tm_prmf AS t1
                        INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                        INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                        ");

        }
        return array(
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "promotion" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data8, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data9, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data10, "action" => $request->country_id),
        );
    }

    public
        function CheckINSyncAllDataMergeVanSales(
        Request $request
    ) {

        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();
        $data9 = array();
        $data10 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
 SELECT
  column_id,
  Item_Price_List,
  Grv_Item_Price_List,
  Item_Id,
  Item_Code,
  amim_imgl,
  Category_Name,
  Item_Name,
  Item_Rate,
  Grv_Item_Price,
  Item_gst,
  Item_vat,
  Item_Factor,
  category_Showing_seqn,
  D_Unit,
  R_Unit,
  sum(Rec_Qty)-ifnull(sum(tn.delQnty),0) as Stock_Qty ,
  is_special_dis from(   
 (SELECT
  concat(t1.amim_code,$request->own_site_id)           AS column_id,
  t5.plmt_id                                           AS Item_Price_List,
  t5.plmt_id                                           AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  t1.amim_imic                                         AS amim_imic,
  t4.issc_name                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t6.pldt_tppr                                         AS Item_Rate,
  t6.pldt_tpgp                                         AS Grv_Item_Price,
  (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t6.amim_duft                                         AS Item_Factor,
  t4.issc_seqn                                         AS category_Showing_seqn,
  t6.amim_dunt                                         AS D_Unit,
  t6.amim_runt                                         AS R_Unit,
  sum(t9.INV_QNTY)                                     AS Rec_Qty,
  if(t11.amim_id IS NULL,true,false)                   AS is_special_dis
FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id and t5.slgp_id=t3.slgp_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
INNER JOIN tm_aemp AS t7 ON t3.aemp_id=t7.id
INNER JOIN dm_trip AS t8 ON t7.aemp_usnm=t8.DM_ID
INNER JOIN dm_van_trip_detail t9 ON t8.TRIP_NO=t9.TRIP_NO and t9.AMIM_ID=t6.amim_id
LEFT JOIN tl_npit t11 ON(t2.amim_id=t11.amim_id) AND t2.slgp_id=t11.slgp_id
WHERE  t1.lfcl_id=1 AND
 t3.aemp_id = $request->emp_id
 AND t5.site_id = $request->own_site_id 
 AND t5.acmp_id = $request->ou_id AND 
 t8.TRIP_NO='$request->TRIP_NO' AND   t8.STATUS='N'
 GROUP BY t9.AMIM_ID,t1.amim_code,t11.amim_id,
  t5.plmt_id ,
  t1.id,                                               
  t1.amim_code  ,                                       
  t1.amim_imgl ,                                       
  t4.issc_name ,                                        
  t1.amim_name ,                                       
  t6.pldt_tppr,                                        
  t6.pldt_tpgp ,                                       
  t1.amim_pvat,                                        
  t6.amim_duft ,                                        
  t4.issc_seqn,                                        
  t6.amim_dunt,                                        
  t6.amim_runt
 ) as tm left join (select t10.Amim_id,sum(t10.DELV_QNTY) delQnty from  dm_trip_detail t10 where  t10.TRIP_NO='$request->TRIP_NO' group by t10.Amim_id) tn on tm.Item_Id = tn.Amim_id
 ) 
  group by 
   column_id,is_special_dis,
  Item_Price_List,
  Grv_Item_Price_List,
  Item_Id,
  Item_Code,
  amim_imgl,
  Category_Name,
  Item_Name,
  Item_Rate,
  Grv_Item_Price,
  Item_gst,
  Item_vat,
  Item_Factor,
  category_Showing_seqn,
  D_Unit,
  R_Unit  having sum(Rec_Qty)-ifnull(sum(tn.delQnty),0)>0
 ");


            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t2.AMIM_CODE,$request->site_id)                AS column_id,
  t2.AMIM_ID                                            AS Item_Id,
  t2.AMIM_CODE                                          AS Item_Code,
  if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis
 FROM dm_trip AS t1 INNER JOIN dm_van_trip_detail t2 ON t1.TRIP_NO=t2.TRIP_NO 
 LEFT JOIN tl_dfsm AS t3 ON t2.SITE_ID = t3.site_id
 LEFT JOIN tm_dfdm t4 ON t3.dfdm_id=t4.id 
 LEFT JOIN tm_dfim AS t8 ON t3.dfdm_id = t8.dfdm_id AND t2.AMIM_ID = t8.amim_id
WHERE 
 t3.site_id = $request->site_id
 and  t4.start_date <=curdate() AND t4.end_date >=curdate()
 AND t3.slgp_id = $request->slgp_id
 AND t1.TRIP_NO='$request->TRIP_NO' 
 AND t1.STATUS='N' GROUP BY t2.AMIM_ID,t2.AMIM_CODE, t8.dfim_disc
");


            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
  t1.id                                                AS promo_id,
  t1.prms_name                                         AS promo_name,
  t1.prms_sdat                                         AS start_date,
  t1.prms_edat                                         AS end_date,
  t1.prmr_qfct                                         AS qualifier_category,
  t1.prmr_ditp                                         AS discount_type,
  t1.prmr_ctgp                                         AS category_group,
  t1.prmr_qfon                                         AS qualifier_on,
  t1.prmr_qfln                                         AS qualifier_line
FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
WHERE t2.site_id = $request->site_id AND t1.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
");
            $data5 = DB::connection($db_conn)->select("
 SELECT 
 concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
 t1.`mspm_id`AS MSP_ID,
 t1.amim_id AS MSP_Item_ID,
 t1.`mspd_qnty`AS MSP_Item_Qty,
 '2' AS Status_ID
FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
WHERE t3.site_id='$request->site_id' AND t3.slgp_id=$request->slgp_id
AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`,t1.amim_id
 ");
            $data6 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
  SELECT
    concat(t1.id, t1.dprt_name) AS column_id,
    concat(t1.id, t1.dprt_name) AS token,
    t1.id                       AS Reason_id,
    t1.dprt_name                AS Reason_Name
  FROM tm_dprt AS t1
  ");
            $data8 = DB::connection($db_conn)->select("
  SELECT
  concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
  t1.prmr_id                                                            AS promo_id,
  t1.amim_id                                                            AS product_id,
  t1.prmd_modr                                                          AS pro_modifier_id
  FROM tm_prmd AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
WHERE t3.site_id =$request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
  ");

            $data9 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
  t1.prmr_id                                                                            AS promo_id,
  t1.prsb_text                                                                          AS promo_slab_text,
  t1.prsb_fqty                                                                          AS from_qty,
  t1.prsb_tqty                                                                          AS to_qty,
  0                                                                                     AS unit,
  0                                                                                     AS unit_factor,
  t1.prsb_famn                                                                          AS from_amnt,
  t1.prsb_tamn                                                                          AS to_amnt,
  t1.prsb_qnty                                                                          AS qty,
  0                                                                                     AS given_unit,
  0                                                                                     AS given_unit_factor,
  t1.prsb_disc                                                                          AS discount,
  t1.prsb_modr                                                                          AS pro_modifier_id,
  t1.prsb_mosl                                                                          AS pro_modifier_id_sl
FROM tm_prsb AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
WHERE t3.site_id = $request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
  t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
  ");

            $data10 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
  t1.prmr_id                                                            AS promo_id,
  t1.amim_id                                                            AS product_id,
  t1.prmd_modr                                                          AS pro_modifier_id
FROM tm_prmf AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
WHERE t3.site_id =$request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
");

        }

        return array(
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            // "Sync_Product_Info_Table" => array("data" => 'Raju', "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "promotion" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data8, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data9, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data10, "action" => $request->country_id),
        );
    }

    public
        function MasterDataNew_Product_Info(
        Request $request
    ) {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");
        }
        return array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public
        function MasterDataNew_RouteWise_Outlet(
        Request $request
    ) {

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl,t4.site_name,t4.site_adrs) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id 
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");
        }

        return array(
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public
        function getVanItemList(
        Request $request
    ) {

        $data2 = array();
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            if ($request->country_id == 15) {
                $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t1.amim_code, $request->site_id )              AS column_id,
  t5.plmt_id                                           AS Item_Price_List,
  t5.plmt_id                                           AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  t4.issc_name                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t6.pldt_tppr                                         AS Item_Rate,
  t6.pldt_tpgp                                         AS Grv_Item_Price,
  (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t6.amim_duft                                         AS Item_Factor,
  t4.issc_seqn                                         AS category_Showing_seqn,
  t6.amim_dunt                                         AS D_Unit,
  t6.amim_runt                                         AS R_Unit

FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
JOIN tl_srdi t7 ON(t3.aemp_id=t7.aemp_id)
LEFT JOIN tt_outs t8 ON(t6.amim_id=t8.amim_id) AND t8.dpot_id=t7.dlrm_id
JOIN tm_dlrm t9 ON t7.dlrm_id=t9.id

WHERE  t1.lfcl_id=1 
AND t3.aemp_id = $request->emp_id  
AND t5.site_id = $request->site_id 
AND t5.slgp_id=$request->slgp_id  AND t8.id IS NULL");
            } else {
                $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t1.amim_code, $request->site_id )              AS column_id,
  t5.plmt_id                                           AS Item_Price_List,
  t5.plmt_id                                           AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  t4.issc_name                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t6.pldt_tppr                                         AS Item_Rate,
  t6.pldt_tpgp                                         AS Grv_Item_Price,
  (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t6.amim_duft                                         AS Item_Factor,
  t4.issc_seqn                                         AS category_Showing_seqn,
  t6.amim_dunt                                         AS D_Unit,
  t6.amim_runt                                         AS R_Unit

FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
JOIN tl_srdi t7 ON(t3.aemp_id=t7.aemp_id)
LEFT JOIN tt_outs t8 ON(t6.amim_id=t8.amim_id) AND t8.dpot_id=t7.dlrm_id
JOIN tm_dlrm t9 ON t7.dlrm_id=t9.id
JOIN DEPOT_STOCK t10 ON t9.dlrm_code=t10.DEPOT_ID AND t6.amim_code = t10.DEPOT_ITEM
WHERE  t1.lfcl_id=1 
AND t3.aemp_id = $request->emp_id  
AND t5.site_id = $request->site_id 
AND t5.slgp_id=$request->slgp_id AND t10.DEPOT_B>0 AND t8.id IS NULL");
            }

        }

        $result_data = array(
            'success' => 1,
            'van_items' => $data2,

        );
        return $result_data;

    }
    public
        function getVanpending_collection(
        Request $request
    ) {
        $pending_collection = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $pending_collection = DB::connection($db_conn)->select("
                        SELECT TRIP_NO,SITE_CODE,SITE_ID ,slgp_id, optp_id as p_type_id  from(
                            SELECT t1.`TRIP_NO`,t3.SITE_CODE,t3.SITE_ID ,t3.slgp_id,t5.optp_id,sum(t3.DELV_AMNT) amnt, sum(t6.COLLECTION_AMNT) camnt
                            FROM 
                            dm_trip t1
                            JOIN dm_trip_master t3 ON(t3.SITE_ID= t3.SITE_ID )
                            JOIN tl_stcm t5 ON(t3.site_id=t5.site_id) AND t5.slgp_id=t3.slgp_id
                            left JOIN dm_collection t6 ON(t6.site_id = t3.site_id) and t6.STATUS=11 and t6.INVT_ID=5 AND t3.slgp_id=t6.slgp_id
                            WHERE t1.`DM_ID` = '$request->emp_code' AND t1.`STATUS`='N' AND t3.DELIVERY_STATUS = 11 AND t3.COLLECTION_AMNT = 0 AND t5.optp_id = 1
                            and t3.DELV_AMNT>0 and t3.COLLECTION_AMNT=0 AND t1.`TRIP_NO`=t3.TRIP_NO
                            group by t3.SITE_CODE,t3.site_id ,t3.slgp_id) d
                            group by SITE_CODE,SITE_ID ,slgp_id
                            having sum(ifnull(d.amnt,0))>sum( ifnull(d.camnt,0))
                        LIMIT 1;
            ");
        }
        if (count($pending_collection) > 0) {
            $collection = $pending_collection[0];

        } else {

            $collection = [];
        }

        $result_data = array(

            'pending_collection' => $collection,
        );
        return $result_data;
    }
    public
        function getVanTripDetails(
        Request $request
    ) {


        $data1 = array();
        $data2 = array();
        $data3 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                   SELECT 
t1.`id` AS trip_id,
t1.`TRIP_NO` AS trip_code,t1.V_ID AS vehicle,
t1.`TRIP_DATE`AS trip_date,t1.DEPOT_ID AS WH_ID,
t1.company_id AS ibs_com_code,
ifnull(round(SUM((t5.lodl_qnty*t5.lodl_uprc)),4),0)AS load_re_amt,
ifnull(round(SUM((t5.lodl_cqty*t5.lodl_uprc)),4),0)AS load_conf_amt,
t6.aemp_crdt AS personal_credit_limit,
ifnull(personal_credit_amount,0) personal_credit_amount,
t1.`DM_ACTIVITY` AS trip_status_id,t2.lfcl_name AS trip_status 
FROM `dm_trip`t1
JOIN tm_lfcl t2 ON(t1.`DM_ACTIVITY`=t2.id) 
LEFT JOIN tt_trip t3 ON(t1.id=t3.trip_otid)
LEFT JOIN tt_lodm t4 ON(t3.id=t4.trip_id)
LEFT JOIN tt_lodl t5 ON(t4.id=t5.lodm_id)
JOIN tm_aemp t6 ON(t1.`DM_ID`=t6.aemp_usnm)
LEFT JOIN 
( SELECT `aemp_iusr`,round(sum(`sapr_amnt`-`scol_amnt`),2)AS personal_credit_amount 
   FROM `tl_cpcr` WHERE `aemp_iusr`=$request->emp_id and (`sapr_amnt`-`scol_amnt`)>0
) t7 ON t6.id=t7.aemp_iusr
WHERE t1.`DM_ID`='$request->emp_code' AND t1.`STATUS`='N' 
GROUP BY t1.`id`,t1.`TRIP_NO`,t1.V_ID,t1.DEPOT_ID,t1.company_id,
t1.`TRIP_DATE`,t1.`DM_ACTIVITY`,t2.lfcl_name,t6.aemp_crdt,
t7.personal_credit_amount");

            $data2 = DB::connection($db_conn)->select("
SELECT t1.`dlrm_id`,t2.dlrm_name AS dlrm_name,t1.`acmp_id`AS ou_id 
FROM `tl_srdi`t1 JOIN tm_dlrm t2 ON(t1.`dlrm_id`=t2.id)
WHERE t1.`aemp_id`=$request->emp_id;");

            /* $data10 = DB::connection($db_conn)->select("
SELECT t1.`TRIP_NO`,
round(sum(t1.`DELV_AMNT`),2)AS DELV_AMNT,
round(sum(t1.`COLLECTION_AMNT`),2)AS COLLECTION_AMNT,
round(Sum(if(t4.sync_status!=2,t3.CRECIT_AMNT,0)),2)Hand_COLLECTION_AMNT,
round(Sum(if(t4.sync_status=2,t3.CRECIT_AMNT,0)),2)HO_COLLECTION_AMNT
FROM `dm_trip_master`t1 JOIN dm_trip t2 ON t1.`TRIP_NO`=t2.TRIP_NO
LEFT JOIN dm_invoice_collection_mapp t3 ON t1.`ORDM_ORNM`=t3.TRANSACTION_ID 
LEFT JOIN dm_collection t4 ON t3.MAP_ID=t4.COLL_NUMBER
WHERE t2.`DM_ID`='$request->emp_code'  and t2.STATUS='N'
group by t1.`TRIP_NO`;");*/
            $data10 = DB::connection($db_conn)->select("
SELECT 
t1.TRIP_NO,
t1.DM_ID,
ROUND(t1.DELV_AMNT,2)DELV_AMNT,
ROUND(t1.COLLECTION_AMNT,2)COLLECTION_AMNT,
ROUND(t2.CASH_IN_HAND,2)CASH_IN_HAND,
ROUND(t2.CHEQUE_IN_HAND,2)CHEQUE_IN_HAND,
ROUND(t2.ONLINE_IN_HAND,2)ONLINE_IN_HAND
FROM 
(SELECT t1.`TRIP_NO`,t2.DM_ID,
sum(t1.`DELV_AMNT`)AS DELV_AMNT,
sum(t1.`COLLECTION_AMNT`) AS COLLECTION_AMNT
FROM `dm_trip_master`t1 
JOIN dm_trip t2 ON t1.`TRIP_NO`=t2.TRIP_NO
WHERE t2.`DM_ID`='$request->emp_code'  and t2.STATUS='N'
GROUP BY t1.TRIP_NO,t2.DM_ID)t1
LEFT JOIN (
SELECT 
AEMP_USNM,
SUM(CASE WHEN COLLECTION_TYPE='Cash' THEN COLLECTION_AMNT-COLL_REC_HO ELSE 0 END) CASH_IN_HAND,
SUM(CASE WHEN COLLECTION_TYPE='Cheque' THEN COLLECTION_AMNT-COLL_REC_HO ELSE 0 END) CHEQUE_IN_HAND,
SUM(CASE WHEN COLLECTION_TYPE='Online' THEN COLLECTION_AMNT-COLL_REC_HO ELSE 0 END) ONLINE_IN_HAND
FROM dm_collection 
WHERE `DM_CODE`='$request->emp_code' AND AEMP_USNM='$request->emp_code' AND STATUS=26 AND COLLECTION_AMNT-COLL_REC_HO>0
GROUP BY AEMP_USNM
)t2 ON t1.DM_ID=t2.AEMP_USNM;");

            $data3 = DB::connection($db_conn)->select("
SELECT
  t4.id                                                      AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_mob1                                               AS Mobile_No,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days                                               AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN
  FROM tm_site AS t4 JOIN tm_aemp t1 ON(t4.id=t1.site_id)
  INNER JOIN tl_stcm t5 ON (t4.id = t5.site_id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  WHERE t1.id = $request->emp_id AND t5.site_id = $request->site_id AND t5.slgp_id = $request->slgp_id 
  GROUP BY t4.id, t4.site_code, t4.site_name, 
  t4.site_mob1, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
  t5.stcm_ordm,t4.site_vtrn, t5.stcm_odue, t8.dfdm_id;");


        }

        if ($data1 != null) {
            $data11 = (array) $data1[0];
        } else {
            $data11 = null;
        }
        if ($data10 != null) {
            $data12 = (array) $data10[0];
        } else {
            $data12 = null;
        }
        if ($data3 != null) {
            $data33 = (array) $data3[0];
        } else {
            $data33 = null;
        }

        $result_data = array(
            'success' => 1,
            'van_trip_details' => $data11,
            'van_dpo_ou' => $data2,
            'van_HO_collection' => $data12,
            'van_site_details' => $data33,

        );
        return $result_data;

    }


    public
        function vanLoadDataSave(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            DB::connection($country->cont_conn)->beginTransaction();

            $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
            try {
                $VanLoadTrip = new VanLoadTrip(); //table tt_trip
                $VanLoadTrip->setConnection($country->cont_conn);
                $VanLoadTrip->trip_otid = $request->trip_id;
                $VanLoadTrip->aemp_tusr = $request->emp_id;
                $VanLoadTrip->trip_code = $request->emp_id . 'TL' . $request->order_date;
                $VanLoadTrip->trip_date = $request->order_date;
                $VanLoadTrip->trip_vdat = $request->order_date;
                $VanLoadTrip->trip_ldat = $request->order_date;
                $VanLoadTrip->trip_cdat = $request->order_date;
                $VanLoadTrip->aemp_vusr = 0;
                $VanLoadTrip->aemp_lusr = 0;
                $VanLoadTrip->aemp_cusr = 0;
                $VanLoadTrip->dpot_id = $request->depo_id;
                $VanLoadTrip->dlrm_id = $request->depo_id;
                $VanLoadTrip->vhcl_id = 1; //table vhcl
                $VanLoadTrip->ttyp_id = 2; //table ttyp
                $VanLoadTrip->cont_id = $request->country_id;
                $VanLoadTrip->lfcl_id = 20;
                $VanLoadTrip->attr3 = $employee->slgp_id;
                $VanLoadTrip->attr4 = $employee->site_id;
                $VanLoadTrip->aemp_iusr = $request->up_emp_id;
                $VanLoadTrip->aemp_eusr = $request->up_emp_id;
                $VanLoadTrip->save();

                $dataLines = json_decode($request->line_data);

                $loadMaster = new LoadMaster(); //table lodm
                $loadMaster->setConnection($country->cont_conn);
                $loadMaster->lodm_code = $request->order_id;
                $loadMaster->lodm_date = $request->order_date;
                $loadMaster->lodm_vdat = $request->order_date;
                $loadMaster->aemp_vusr = $request->emp_id;
                $loadMaster->aemp_vusr = 0;
                $loadMaster->trip_id = $VanLoadTrip->id;
                $loadMaster->dlrm_id = $request->depo_id;
                $loadMaster->lodt_id = 1; //table lodt
                $loadMaster->lods_id = 1; //table lods
                $loadMaster->lfcl_id = 1;
                $loadMaster->cont_id = $request->country_id;
                $loadMaster->aemp_iusr = $request->up_emp_id;
                $loadMaster->aemp_eusr = $request->up_emp_id;
                $loadMaster->save();
                foreach ($dataLines as $lineData) {
                    $loadLine = new LoadLine(); //table lodl
                    $loadLine->setConnection($country->cont_conn);
                    $loadLine->lodm_id = $loadMaster->id;
                    $loadLine->amim_id = $lineData->product_id;
                    $loadLine->lodl_qnty = $lineData->order_qty;
                    $loadLine->lodl_cqty = 0;
                    $loadLine->lodl_dqty = 0;
                    $loadLine->lodl_uprc = $lineData->unit_price;
                    $loadLine->lfcl_id = 1;
                    $loadLine->cont_id = $request->country_id;
                    $loadLine->aemp_iusr = $request->up_emp_id;
                    $loadLine->aemp_eusr = $request->up_emp_id;
                    $loadLine->save();

                }

                DB::connection($country->cont_conn)->commit();
                //  return array('column_id' => $request->id);
                return response()->json(array('column_id' => $request->id, 'message' => 'success'));
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                // return response()->json(array('column_id' => 0, 'message' =>'failed'));
                return $e;
            }
        }
    }

    public
        function get_IBS_INVOICE(
        $com_id,
        $wh_id,
        $order_id,
        $cont_id
    ) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => '159.223.60.117/api/InvoicesaveVan',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ApiKey_van' => 'API2345fsdvh3675gsvbxdgeg425435hdgfsfg33',
                'company_id' => $com_id,
                'wh_id' => $wh_id,
                'MobileOrder_nummber' => $order_id,
                'trn' => 'OC',
                'cont_code_van' => $cont_id
            ),
        )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    public
        function get_IBS_INVOICE111(
        Request $request
    ) {
        $com_id = $request->com_id;
        $wh_id = $request->wh_id;
        $order_id = $request->order_id;
        $cont_id = $request->cont_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => '159.223.60.117/api/Invoicesavevann',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ApiKey_van' => 'API2345fsdvh3675gsvbxdgeg425435hdgfsfg33',
                'company_id' => $com_id,
                'wh_id' => $wh_id,
                'MobileOrder_nummber' => $order_id,
                'trn' => 'OC',
                'cont_code_van' => $cont_id
            ),
        )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        try {
            $array = json_decode($response, true);
            $data = json_decode($array, true);
            // Access "Status" value
            $status = $data[0]['Status'];
            $vatNumber = $data[0]['VatNumber'];

            if ($status == 'success') {
                return $vatNumber;
            } else {
                return '11111111111111111111';
            }

        } catch (\Exception $e) {
            return $e;
        }

    }

    public
        function get_IBS_INVOICE_new(
        $com_id,
        $wh_id,
        $order_id,
        $cont_id
    ) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => '159.223.60.117/api/Invoicesavevann',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ApiKey_van' => 'API2345fsdvh3675gsvbxdgeg425435hdgfsfg33',
                'company_id' => $com_id,
                'wh_id' => $wh_id,
                'MobileOrder_nummber' => $order_id,
                'trn' => 'OC',
                'cont_code_van' => $cont_id
            ),
        )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }
    public function submit_leave_HRIS(Request $request)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://hris.prangroup.com:8696/Leave/LeaveApply',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                     "staffId": "' . $request->staffId . '",
                     "startDate": "' . $request->startDate . '",
                     "endDate": "' . $request->endDate . '",
                     "leaveTypeId": "' . $request->leaveTypeId . '",
                     "isHalfDay": ' . $request->isHalfDay . ',
                     "reason": "' . $request->reason . '",
                     "leaveLocation": "' . $request->leaveLocation . '",
                     "inTime": "' . $request->inTime . '",
                     "outTime": "' . $request->outTime . '",
                     "doc": "' . $request->doc . '",
                     "sC_KEY": "StrONGKAutHENTICATIONKEy"
                      }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        $manage = json_decode($response, true);
        $res = $manage['status'];
        $result_data = array(
            'status' => '',
            'message' => '',
            'oid' => 0,
        );
        if ($res == 'success') {
            $result_data = array(
                'status' => $manage['status'],
                'message' => $manage['message'],
                'oid' => $manage['oid'],
            );
        } else {
            $result_data = array(
                'status' => $manage['status'],
                'message' => $manage['message'],
                'oid' => 0,
            );
        }
        return $result_data;
    }


    public
        function Check_Invoice_Ok_OR_Not(
        $order_id,
        $cont_id
    ) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => '159.223.60.117/api/checkInv',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ApiKey_van' => 'API2345fsdvh3675gsvbxdgeg425435hdgfsfg33',
                'trip_nummber' => $order_id,
                'cont_code_trip' => $cont_id
            ),
        )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        $manage = json_decode($response, true);
        $res = $manage['order_number'];
        return $res;

    }

    public
        function vanOrderSaveNew(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);

        $request->country_id;
        $request->site_code;
        $request->site_name;
        $request->site_mob;
        $request->site_id;
        $request->country_id;
        $request->emp_id;
        $request->emp_code;
        $request->ACMP_CODE;
        $request->WH_ID;
        $request->depo_id;
        $request->route_id;
        $request->V_NAME;
        $request->trip_id;
        $request->trip_code;
        $request->slgp_id;
        $request->up_emp_id;
        $request->lat;
        $request->lon;
        $request->distance;
        $request->price_list_id;
        $request->mspm_id;
        $request->status_id;
        $request->order_date;


        if ($country != false) {

            $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->order_id])->first();

            if ($orderSyncLog == null) {

                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();

                    //  return $orderSequence;

                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $orderMaster = new OrderMaster();
                    $orderMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT));
                    //$IBS_INVOICE = '01/2022-00449972';
                    // $IBS_INVOICE = '';
                    if ($IBS_INVOICE1 = $this->get_IBS_INVOICE($request->ACMP_CODE, $request->WH_ID, $order_id, $request->country_id)) {
                        $IBS_INVOICE = str_replace('"', '', $IBS_INVOICE1);
                        // $IBS_INVOICE = $this->get_IBS_INVOICE($request->ACMP_CODE, $request->WH_ID, $order_id, $request->country_id); //O0000328585-22-00039  01/2022-00449972
                    }
                    $length = strlen($IBS_INVOICE);
                    if ($length > 17) {
                        return array(
                            'success' => 0,
                            //  'column_id' => $request->id,
                            'message' => 'Vat Server Problem !!!',
                        );
                    }
                    $order_amount = array_sum(array_column($orderLines, 'total_price'));
                    $orderMaster->ordm_ornm = $order_id;
                    $orderMaster->aemp_id = $request->emp_id;
                    $orderMaster->acmp_id = $request->ou_id;
                    $orderMaster->slgp_id = $request->slgp_id;
                    $orderMaster->dlrm_id = $request->depo_id;
                    $orderMaster->site_id = $request->site_id;
                    $orderMaster->rout_id = $request->route_id;
                    $orderMaster->odtp_id = 2;
                    $orderMaster->ordm_date = $request->order_date;
                    $orderMaster->ordm_time = date('Y-m-d H:i:s');
                    $orderMaster->ordm_drdt = $request->order_date;
                    $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                    $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                    $orderMaster->ordm_dtne = $request->distance;
                    $orderMaster->ordm_amnt = $order_amount;
                    $orderMaster->ordm_icnt = sizeof($orderLines);
                    $orderMaster->plmt_id = $request->price_list_id;
                    $orderMaster->ocrs_id = 0;
                    $orderMaster->ordm_pono = '';
                    $orderMaster->aemp_cusr = 0;
                    $orderMaster->ordm_note = '';
                    $orderMaster->mspm_id = $request->mspm_id;
                    $orderMaster->cont_id = $request->country_id;
                    $orderMaster->lfcl_id = $request->status_id;
                    $orderMaster->aemp_iusr = $request->up_emp_id;
                    $orderMaster->aemp_eusr = $request->up_emp_id;
                    $orderMaster->save();

                    $date = date('Y-m-d');
                    $sql_tpm = "";
                    $sql_tpd = "";
                    $sql = "SET autocommit=0;";
                    DB::connection($country->cont_conn)->insert($sql);

                    $address = $request->site_name . '-' . $request->site_mob;
                    $geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;

                    $sql_tpm = "INSERT INTO `dm_trip_master`(`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`,
                    `AEMP_USNM`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `GEO_LAT`, `GEO_LON`, `ORDD_AMNT`, `INV_AMNT`, `DELV_AMNT`, `COLLECTION_AMNT`,
                    `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`, `V_NAME`, `TRIP_NO`, `COLL_STATUS`,`DELIVERY_STATUS`, `slgp_id`,SALES_TYPE) VALUES (
                    '$request->ACMP_CODE','$date','$order_id','$date',$request->emp_id,
                    '$request->emp_code','$request->WH_ID',$request->site_id,'$request->site_code',
                     $geo_lat,
                     $geo_lon,
                    $order_amount,$order_amount,$order_amount,0,
                   '$address','$request->emp_code','$IBS_INVOICE','$request->V_NAME','$request->trip_code',7,11,$request->slgp_id,'VS');";

                    //  return $sql_tpm;
                    DB::connection($country->cont_conn)->insert($sql_tpm);

                    foreach ($orderLines as $orderLineData) {

                        // return $orderLines;

                        $orderLine = new OrderLine();
                        $orderLine->setConnection($country->cont_conn);
                        $orderLine->ordm_id = $orderMaster->id;
                        $orderLine->ordm_ornm = $order_id;
                        $orderLine->amim_id = $orderLineData->P_ID;
                        $orderLine->ordd_inty = $orderLineData->P_Qty;
                        $orderLine->ordd_qnty = $orderLineData->P_Qty;
                        $orderLine->ordd_cqty = $orderLineData->P_Qty;
                        $orderLine->ordd_dqty = $orderLineData->P_Qty;
                        $orderLine->ordd_rqty = 0;
                        $orderLine->ordd_opds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_cpds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_dpds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_spdi = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdo = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdc = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdd = $orderLineData->sp_Discount;
                        $orderLine->ordd_dfdo = $orderLineData->df_Discount;
                        $orderLine->ordd_dfdc = $orderLineData->df_Discount;
                        $orderLine->ordd_dfdd = $orderLineData->df_Discount;
                        $orderLine->ordd_duft = $orderLineData->Item_Factor;
                        $orderLine->ordd_uprc = $orderLineData->Rate;
                        $orderLine->ordd_runt = 1;
                        $orderLine->ordd_dunt = 1;
                        //  $orderLine->ordd_smpl = $orderLineData->is_order_line == '1' ? 0 : 1;
                        $orderLine->ordd_smpl = $orderLineData->is_free;
                        $orderLine->prom_id = $orderLineData->promo_ref;
                        // $orderLine->prom_id = $orderLineData->promo_ref == '' ? 0 : $orderLineData->promo_ref;
                        $orderLine->ordd_oamt = $orderLineData->total_price;
                        $orderLine->ordd_ocat = $orderLineData->total_price;
                        $orderLine->ordd_odat = $orderLineData->total_price;
                        $orderLine->ordd_excs = $orderLineData->excise_percent;
                        $orderLine->ordd_ovat = $orderLineData->vat_percent;
                        $orderLine->ordd_tdis = $orderLineData->total_discount;
                        $orderLine->ordd_texc = $orderLineData->excise;
                        $orderLine->ordd_tvat = $orderLineData->vat;
                        // $orderLine->ordd_amnt = $orderLineData->net_amount;
                        $orderLine->ordd_amnt = $orderLineData->total_price;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $request->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;

                        $orderLine->save();

                        $sql_tpd = "INSERT INTO `dm_trip_detail`(`OID`,`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`, 
                        `AEMP_USNM`, `DLRM_ID`, `SITE_ID`, `SITE_CODE`, `AMIM_ID`, `AMIM_CODE`, 
                        `GEO_LAT`, `GEO_LON`, `ORDD_QNTY`, `INV_QNTY`, `DELV_QNTY`,
                        `RETURN_QNTY`, `ORDD_UPRC`, `ORDD_EXCS`, `ORDD_OVAT`, `ORDD_OAMT`, `prom_id`,
                         `DISCOUNT`, `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`,
                         `V_NAME`, `TRIP_NO`, `TRIP_STATUS`) 
                         VALUES ($orderLine->id,'$request->ACMP_CODE','$orderLineData->Delivery_Date','$order_id','$orderLineData->Delivery_Date',$request->emp_id,
                        '$request->emp_code','$request->WH_ID','$request->site_id','$request->site_code',$orderLineData->P_ID,'$orderLineData->product_code',
                         $geo_lat,$geo_lon,$orderLineData->P_Qty,$orderLineData->P_Qty,$orderLineData->P_Qty,
                         0,$orderLineData->Rate,$orderLineData->excise_percent,$orderLineData->vat_percent,$orderLineData->total_price,$orderLineData->promo_ref,
                         $orderLineData->total_discount,'$address','$request->emp_code','$IBS_INVOICE',
                        '$request->V_NAME','$request->trip_code','D');";

                        DB::connection($country->cont_conn)->insert($sql_tpd);
                        //  return $sql_tpd;
                    }


                    $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();

                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->order_id;
                    $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                    $orderSyncLog->oslg_orid = $orderMaster->id;
                    $orderSyncLog->oslg_type = 1;

                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->order_date, 'aemp_id' => $request->emp_id])->first();
                    if ($siteVisit == null) {
                        $siteVisit = new SiteVisited();
                        $siteVisit->setConnection($country->cont_conn);
                        $siteVisit->ssvh_date = $request->order_date;
                        $siteVisit->aemp_id = $request->emp_id;
                        $siteVisit->site_id = $request->site_id;
                        $siteVisit->SSVH_ISPD = 1;
                        $siteVisit->cont_id = $request->country_id;
                        $siteVisit->lfcl_id = 1;
                        $siteVisit->aemp_iusr = $request->up_emp_id;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    } else {
                        $siteVisit->ssvh_ispd = 1;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    }
                    if ($request->country_id == 2 || $request->country_id == 5) {
                    } else {

                        $tp = round($order_amount, 3);
                        $sql = "SET autocommit=0;";
                        $sql_order = "update `tl_stcm`t1 SET t1.`stcm_ordm`=t1.`stcm_ordm`+$tp
                                WHERE t1.acmp_id= $request->ou_id AND t1.`site_id`=$request->site_id AND t1.`slgp_id`=$request->slgp_id AND t1.`optp_id`=2 ;";

                        DB::connection($country->cont_conn)->unprepared($sql);
                        DB::connection($country->cont_conn)->unprepared($sql_order);

                    }
                    DB::connection($country->cont_conn)->commit();

                    return array(
                        'success' => 1,
                        'column_id' => $request->id,
                        'message' => "Successfully Save Order w" . $request->id,
                    );
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    // return $e;
                    return array(
                        'success' => 0,
                        //  'column_id' => $request->id,
                        'message' => $e,
                    );
                }
            } else {
                return array(
                    'success' => 0,
                    // 'column_id' => $request->id,
                    'message' => 'Duplicate Order ',
                );
            }
        }
    }

    public
        function SaveVanOrderNew(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);

        $request->country_id;
        $request->site_code;
        $request->site_name;
        $request->site_mob;
        $request->site_id;
        $request->country_id;
        $request->emp_id;
        $request->emp_code;
        $request->ACMP_CODE;
        $request->WH_ID;
        $request->depo_id;
        $request->route_id;
        $request->V_NAME;
        $request->trip_id;
        $request->trip_code;
        $request->slgp_id;
        $request->up_emp_id;
        $request->lat;
        $request->lon;
        $request->distance;
        $request->price_list_id;
        $request->mspm_id;
        $request->status_id;
        $request->order_date;

        if ($country != false) {

            $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->order_id])->first();

            if ($orderSyncLog == null) {

                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();

                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $orderMaster = new OrderMaster();
                    $orderMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT));

                    /*$order_amount = array_sum(array_map(function($orderLines) {
                        return $orderLines['P_Qty'] * $orderLines['Rate']+$orderLines['excise'] * $orderLines['vat'];
                    }, $orderLines));*/
                    $order_amount = array_sum(array_column($orderLines, 'total_price'));
                    $orderMaster->ordm_ornm = $order_id;
                    $orderMaster->aemp_id = $request->emp_id;
                    $orderMaster->acmp_id = $request->ou_id;
                    $orderMaster->slgp_id = $request->slgp_id;
                    $orderMaster->dlrm_id = $request->depo_id;
                    $orderMaster->site_id = $request->site_id;
                    $orderMaster->rout_id = $request->route_id;
                    $orderMaster->odtp_id = 2;
                    $orderMaster->ordm_date = $request->order_date;
                    $orderMaster->ordm_time = date('Y-m-d H:i:s');
                    $orderMaster->ordm_drdt = $request->order_date;
                    $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                    $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                    $orderMaster->ordm_dtne = $request->distance;
                    $orderMaster->ordm_amnt = $order_amount;
                    $orderMaster->ordm_icnt = sizeof($orderLines);
                    $orderMaster->plmt_id = $request->price_list_id;
                    $orderMaster->ocrs_id = 0;
                    $orderMaster->ordm_pono = '';
                    $orderMaster->aemp_cusr = 0;
                    $orderMaster->ordm_note = '';
                    $orderMaster->mspm_id = $request->mspm_id;
                    $orderMaster->cont_id = $request->country_id;
                    $orderMaster->lfcl_id = $request->status_id;
                    $orderMaster->aemp_iusr = $request->up_emp_id;
                    $orderMaster->aemp_eusr = $request->up_emp_id;
                    $orderMaster->save();

                    $date = date('Y-m-d');
                    $sql_tpm = "";
                    $sql_tpd = "";
                    $sql = "SET autocommit=0;";
                    DB::connection($country->cont_conn)->insert($sql);

                    $address = $request->site_name . '-' . $request->site_mob;
                    $geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;

                    $sql_tpm = "INSERT INTO `dm_trip_master`(`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`,
                    `AEMP_USNM`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `GEO_LAT`, `GEO_LON`, `ORDD_AMNT`, `INV_AMNT`, `DELV_AMNT`, `COLLECTION_AMNT`,
                    `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`, `V_NAME`, `TRIP_NO`, `COLL_STATUS`,`DELIVERY_STATUS`, `slgp_id`,SALES_TYPE) VALUES (
                    '$request->ACMP_CODE','$date','$order_id','$date',$request->emp_id,
                    '$request->emp_code','$request->WH_ID',$request->site_id,'$request->site_code',
                     $geo_lat,
                     $geo_lon,
                    $order_amount,$order_amount,0,0,
                   '$address','$request->emp_code','N','$request->V_NAME','$request->trip_code',7,0,$request->slgp_id,'VS');";

                    //  return $sql_tpm;
                    DB::connection($country->cont_conn)->insert($sql_tpm);

                    foreach ($orderLines as $orderLineData) {

                        // $order_amount_new = ($orderLineData->P_Qty * $orderLineData->Rate)
                        //   + $orderLineData->excise + $orderLineData->vat - $orderLineData->total_discount;

                        $orderLine = new OrderLine();
                        $orderLine->setConnection($country->cont_conn);
                        $orderLine->ordm_id = $orderMaster->id;
                        $orderLine->ordm_ornm = $order_id;
                        $orderLine->amim_id = $orderLineData->P_ID;
                        $orderLine->ordd_inty = $orderLineData->P_Qty;
                        $orderLine->ordd_qnty = $orderLineData->P_Qty;
                        $orderLine->ordd_cqty = $orderLineData->P_Qty;
                        $orderLine->ordd_dqty = 0;
                        $orderLine->ordd_rqty = 0;
                        $orderLine->ordd_opds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_cpds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_dpds = $orderLineData->Promo_Discount;
                        $orderLine->ordd_spdi = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdo = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdc = $orderLineData->sp_Discount;
                        $orderLine->ordd_spdd = $orderLineData->sp_Discount;
                        $orderLine->ordd_dfdo = $orderLineData->df_Discount;
                        $orderLine->ordd_dfdc = $orderLineData->df_Discount;
                        $orderLine->ordd_dfdd = $orderLineData->df_Discount;
                        $orderLine->ordd_duft = $orderLineData->Item_Factor;
                        $orderLine->ordd_uprc = $orderLineData->Rate;
                        $orderLine->ordd_runt = 1;
                        $orderLine->ordd_dunt = 1;
                        //  $orderLine->ordd_smpl = $orderLineData->is_order_line == '1' ? 0 : 1;
                        $orderLine->ordd_smpl = $orderLineData->is_free;
                        $orderLine->prom_id = $orderLineData->promo_ref;
                        // $orderLine->prom_id = $orderLineData->promo_ref == '' ? 0 : $orderLineData->promo_ref;

                        $orderLine->ordd_oamt = $orderLineData->total_price;
                        $orderLine->ordd_ocat = $orderLineData->total_price;
                        $orderLine->ordd_odat = 0;

                        // $orderLine->ordd_oamt = $order_amount_new;
                        // $orderLine->ordd_ocat = $order_amount_new;
                        // $orderLine->ordd_odat = $order_amount_new;

                        $orderLine->ordd_excs = $orderLineData->excise_percent;
                        $orderLine->ordd_ovat = $orderLineData->vat_percent;
                        $orderLine->ordd_tdis = $orderLineData->total_discount;
                        $orderLine->ordd_texc = $orderLineData->excise;
                        $orderLine->ordd_tvat = $orderLineData->vat;

                        $orderLine->ordd_amnt = 0;
                        // $orderLine->ordd_amnt = $order_amount_new;

                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $request->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;

                        $orderLine->save();

                        $sql_tpd = "INSERT INTO `dm_trip_detail`(`OID`,`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`, 
                        `AEMP_USNM`, `DLRM_ID`, `SITE_ID`, `SITE_CODE`, `AMIM_ID`, `AMIM_CODE`, 
                        `GEO_LAT`, `GEO_LON`, `ORDD_QNTY`, `INV_QNTY`, `DELV_QNTY`,
                        `RETURN_QNTY`, `ORDD_UPRC`, `ORDD_EXCS`, `ORDD_OVAT`, `ORDD_OAMT`, `prom_id`,
                         `DISCOUNT`, `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`,
                         `V_NAME`, `TRIP_NO`, `TRIP_STATUS`) 
                         VALUES ($orderLine->id,'$request->ACMP_CODE','$orderLineData->Delivery_Date','$order_id','$orderLineData->Delivery_Date',$request->emp_id,
                        '$request->emp_code','$request->depo_id','$request->site_id','$request->site_code',$orderLineData->P_ID,'$orderLineData->product_code',
                         $geo_lat,$geo_lon,$orderLineData->P_Qty,$orderLineData->P_Qty,0,
                         0,$orderLineData->Rate,$orderLineData->excise_percent,$orderLineData->vat_percent,$orderLineData->total_price,$orderLineData->promo_ref,
                         $orderLineData->total_discount,'$address','$request->emp_code','N',
                        '$request->V_NAME','$request->trip_code','N');";

                        DB::connection($country->cont_conn)->insert($sql_tpd);
                        //  return $sql_tpd;
                    }


                    $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();

                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->order_id;
                    $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                    $orderSyncLog->oslg_orid = $orderMaster->id;
                    $orderSyncLog->oslg_type = 1;

                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->order_date, 'aemp_id' => $request->emp_id])->first();
                    if ($siteVisit == null) {
                        $siteVisit = new SiteVisited();
                        $siteVisit->setConnection($country->cont_conn);
                        $siteVisit->ssvh_date = $request->order_date;
                        $siteVisit->aemp_id = $request->emp_id;
                        $siteVisit->site_id = $request->site_id;
                        $siteVisit->SSVH_ISPD = 1;
                        $siteVisit->cont_id = $request->country_id;
                        $siteVisit->lfcl_id = 1;
                        $siteVisit->aemp_iusr = $request->up_emp_id;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    } else {
                        $siteVisit->ssvh_ispd = 1;
                        $siteVisit->aemp_eusr = $request->up_emp_id;
                        $siteVisit->save();
                    }
                    if ($request->country_id == 2 || $request->country_id == 5) {
                    } else {

                        $tp = round($order_amount, 3);
                        $sql = "SET autocommit=0;";
                        $sql_order = "update `tl_stcm`t1 SET t1.`stcm_ordm`=t1.`stcm_ordm`+$tp
                                WHERE t1.acmp_id= $request->ou_id AND t1.`site_id`=$request->site_id AND t1.`slgp_id`=$request->slgp_id AND t1.`optp_id`=2 ;";

                        DB::connection($country->cont_conn)->unprepared($sql);
                        DB::connection($country->cont_conn)->unprepared($sql_order);

                    }
                    DB::connection($country->cont_conn)->commit();

                    return array(
                        'success' => 1,
                        'column_id' => $request->id,
                        'message' => "Successfully Save Order w" . $request->id,
                    );
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    // return $e;
                    return array(
                        'success' => 0,
                        //  'column_id' => $request->id,
                        'message' => $e,
                    );
                }
            } else {
                return array(
                    'success' => 0,
                    // 'column_id' => $request->id,
                    'message' => 'Duplicate Order ',
                );
            }
        }
    }

    public
        function vanGRVSave(
        Request $request
    ) {

        $country = (new Country())->country($request->country_id);
        if ($country != false) {

            $DRDT = date('Y-m-d');
            $dt = date('Ymd-Hi');

            $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->return_id])->first();
            if ($orderSyncLog == null) {
                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $order_id = strtoupper("R" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    $orderSequence->srsc_rcnt = $orderSequence->srsc_rcnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $returnMaster = new ReturnMaster();
                    $returnMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_amount = array_sum(array_column($orderLines, 'total_amount'));
                    $vat_amount = array_sum(array_column($orderLines, 'vat'));
                    $exc_amount = array_sum(array_column($orderLines, 'gst'));
                    $delivery_amt = round(($order_amount + $vat_amount + $exc_amount), 4);
                    $returnMaster->rtan_rtnm = $order_id;
                    $returnMaster->aemp_id = $request->emp_id;
                    $returnMaster->acmp_id = $request->ou_id;
                    $returnMaster->slgp_id = $request->slgp_id;
                    $returnMaster->dlrm_id = $request->depo_id;
                    $returnMaster->site_id = $request->site_id;
                    $returnMaster->rout_id = $request->route_id;
                    $returnMaster->rttp_id = 1;
                    $returnMaster->plmt_id = $request->price_list;
                    $returnMaster->rtan_date = $request->date;
                    $returnMaster->rtan_time = date('Y-m-d H:i:s');
                    $returnMaster->rtan_drdt = $DRDT;
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    $returnMaster->geo_lat = $request->lat;
                    $returnMaster->geo_lon = $request->lon;
                    $returnMaster->rtan_amnt = $order_amount;
                    $returnMaster->rtan_icnt = sizeof($orderLines);
                    $returnMaster->rtan_pono = '';
                    $returnMaster->rtan_podt = $request->date;
                    $returnMaster->rtan_note = '';
                    $returnMaster->dm_trip = $request->trip_code;
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = $request->status_id;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();

                    $dm_ACMP_WH_ID_DM_CODE = DB::connection($country->cont_conn)->table('dm_trip_master')->where(['TRIP_NO' => $request->trip_code])->first();
                    $now = now();
                    $last_3 = substr($now->timestamp . $now->milli, 10);
                    $COLL_NUMBER1 = "RC" . $dt . '-' . $last_3;
                    $COLL_NUMBER = $order_id;

                    $sql = "SET autocommit=0;";
                    DB::connection($country->cont_conn)->insert($sql);

                    $sql6 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,slgp_id)
                                 VALUES (NULL,  '$dm_ACMP_WH_ID_DM_CODE->ACMP_CODE', '$DRDT', '$COLL_NUMBER', 'N', $request->emp_id,
                                  '$request->emp_code', '$dm_ACMP_WH_ID_DM_CODE->DM_CODE', '$dm_ACMP_WH_ID_DM_CODE->WH_ID',
                                  '$request->site_id', '$request->site_code', $delivery_amt,0, 'GRV', 11, 'N', 5,$request->slgp_id); ";

                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_dqty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount;
                        $returnLine->rtdd_damt = $delivery_amt;
                        $returnLine->rtdd_edat = date('Y-m-d');
                        $returnLine->rtdd_note = 0;
                        $returnLine->rtdd_rato = $orderLineData->discount_ratio;
                        $returnLine->rtdd_excs = $orderLineData->gst_percent;
                        $returnLine->rtdd_ovat = $orderLineData->vat_percent;
                        $returnLine->rtdd_tdis = 0;
                        $returnLine->rtdd_texc = $orderLineData->gst;
                        $returnLine->rtdd_tvat = $orderLineData->vat;
                        $returnLine->rtdd_amnt = 0;
                        $returnLine->dprt_id = $orderLineData->reason_id;
                        $returnLine->rtdd_ptyp = $orderLineData->retrun_type; //1 non-saleble, 2=saleable
                        $returnLine->lfcl_id = 11;
                        $returnLine->cont_id = $request->country_id;
                        $returnLine->rtdd_batc = $orderLineData->rtdd_batc ?? '-';
                        $returnLine->rtdd_mfdt = $orderLineData->rtdd_mfdt ?? null;
                        $returnLine->rtdd_exdt = $orderLineData->rtdd_exdt ?? null;
                        $returnLine->rtdd_imag = $orderLineData->rtdd_imag ?? '-';
                        $returnLine->aemp_iusr = $request->up_emp_id;
                        $returnLine->aemp_eusr = $request->up_emp_id;
                        $returnLine->save();

                    }

                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->return_id;
                    $orderSyncLog->oslg_ornm = $returnMaster->rtan_rtnm;
                    $orderSyncLog->oslg_orid = $returnMaster->id;
                    $orderSyncLog->oslg_type = 'van_grv';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();

                    DB::connection($country->cont_conn)->insert($sql6);

                    DB::connection($country->cont_conn)->commit();

                    return array(
                        'success' => 1,
                        'column_id' => $request->ID,
                        'message' => "Successfully Save GRV " . $request->ID,
                    );
                    //  return array('column_id' => $request->id);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return array(
                        'success' => 0,
                        //  'column_id' => $request->id,
                        'message' => $e,
                    );
                }
            } else {
                return array(
                    'success' => 0,
                    // 'column_id' => $request->id,
                    'message' => 'Duplicate Order ',
                );
            }
        }
    }

    public
        function MasterDataNew_FOC(
        Request $request
    ) {

        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data6 = DB::connection($db_conn)->select("
SELECT
concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
  t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm AS buy_item_code,
  t5.prdt_mbqt as max_buy_qty,
  t5.prdt_mnbt as min_buy_qyt,
  t5.prdt_fitm as free_item_code,
  t5.prdt_fiqt as free_item_qty,
  round(t5.prdt_fipr,2) as free_item_price,
  t5.prdt_disc as discount_percente,
  t4.prom_edat as end_date
from tl_srgb as t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id 
and t2.prom_nztp=0 
and t2.prom_sdat <=CURDATE()
AND t2.prom_edat >=CURDATE()
");
        }

        return array(

            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
        function aroundOutlet(
        Request $request
    ) {
        $data1 = array();
        /*     $country = (new Country())->country($request->country_id);
             $db_conn = $country->cont_conn;
             if ($db_conn != '') {
                 $data1 = DB::connection($db_conn)->select("
     SELECT
       t1.id                                                        AS Outlet_ID,
       t1.site_code                                                 AS Outlet_Code,
       t1.site_name                                                 AS Outlet_Name,
       t1.site_mob1                                                 AS Mobile,
       t2.mktm_name                                                 AS Market_Name,
       t1.geo_lat                                                   AS Lat,
       t1.geo_lon                                                   AS Lon,
       getDistance(t1.geo_lat, t1.geo_lon, $request->geo_lat, $request->geo_lon) AS distance
     FROM tm_site AS t1
       INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
     HAVING distance < 0.1
     ORDER BY distance");

             }*/


        return $data1;

    }

    public
        function GetManagers_SR(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t1.`id`AS Sr_id,t1.`aemp_usnm`AS Sr_Code,t1.`aemp_name`AS Sr_Name,t1.aemp_mob1 AS Sr_Mobile
     FROM `tm_aemp` t1 WHERE t1.`role_id`='1' AND t1.`aemp_mngr`=$request->MG_EmpId");

        }

        return $data1;

    }

    public
        function GetVanSalesTripSite(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
    SELECT t1.`TRIP_NO`,t2.site_id
    FROM `dm_trip`t1 JOIN tm_aemp t2 ON t1.`DM_ID`=t2.aemp_usnm
    WHERE t2.`id`='$request->emp_id' 
    AND t1.`STATUS`='N' 
    AND t1.`SALES_TYPE`='VS'
     ");

        }

        return $data1;

    }

    public
        function GetSRRoute(
        Request $request
    ) {
        $data1 = array();

        $Day_Name = $request->Day_Name;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t1.rpln_day                                                                                AS Day
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
  WHERE t1.aemp_id = $emp_id and t1.rpln_day='$Day_Name'");

        }

        return $data1;

    }


    public
        function GetOutletSerialData(
        Request $request
    ) {
        $data1 = array();

        $route_id = $request->route_id;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t3.rspm_serl                                 AS outlet_serial
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON(t3.site_id=t5.site_id)
WHERE t1.aemp_id = '$emp_id'AND t1.rout_id='$route_id'
GROUP BY t3.site_id, t4.site_code, t4.site_name, t3.rspm_serl
  ORDER BY  t3.rspm_serl
  ");

        }

        return $data1;

    }

    public
        function GetSRDPO(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
 SELECT
  t3.id        AS depot_id,
  t3.dpot_name AS depot_name
FROM tm_dlrm AS t1
  INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
  INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
  INNER JOIN tl_srdi AS t5 ON t1.id = t5.dlrm_id
WHERE t1.dptp_id = 1 AND t5.aemp_id ='$emp_id'
ORDER BY t3.id ASC;
  "); /*$data1 = DB::connection($db_conn)->select("
SELECT
t3.id        AS depot_id,
t3.dpot_name AS depot_name
FROM tm_dlrm AS t1
INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
WHERE t1.dptp_id = 1 AND t5.aemp_id = '$emp_id'
ORDER BY t3.id ASC
");*/

        }

        return $data1;

    }

    public
        function GetSRSUBDPO(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $DPO = $request->dpo_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
  SELECT
  t1.id AS depot_id,
  t1.acmp_id AS ou_id,
  concat(t4.acmp_name, '(', t1.dlrm_name, ')') AS dsp_name
  FROM tm_dlrm AS t1
  INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
  INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  INNER JOIN tl_srdi AS t5 ON t1.id = t5.dlrm_id
  WHERE t3.id = '$DPO' AND t5.aemp_id = '$emp_id' AND  t1.dptp_id = 1
  ORDER BY t3.id ASC;
  "); /* $data1 = DB::connection($db_conn)->select("
SELECT
t1.id AS depot_id,
t1.acmp_id AS ou_id,
concat(t4.acmp_name, '(', t1.dlrm_name, ')') AS dsp_name
FROM tm_dlrm AS t1
INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
WHERE t3.id = '$DPO' AND t5.aemp_id = '$emp_id' AND  t1.dptp_id = 1
ORDER BY t3.id ASC
");*/

        }

        return $data1;

    }

    public
        function GetSrSalesGroup(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
  SELECT t1.`slgp_id`,t2.slgp_name AS slgp_name 
  FROM `tl_sgsm`t1 JOIN tm_slgp t2 ON(t1.`slgp_id`=t2.id)
  WHERE t1.`aemp_id`=$emp_id;
  ");

        }

        return $data1;

    }
    public
        function GetSrRouteSiteData(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
  SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_mob2                                               AS Mobile_No_2,
  t4.site_imge                                               AS Outlet_imge_ln,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t4.site_vtrn                                               AS trn,
  t4.otcg_id                                                 AS shop_category_id,
  t5.cont_id                                                 AS nationality   
  FROM tl_rpln AS t1
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tl_scmp t5 ON t4.id=t5.site_id 
  WHERE t1.aemp_id = $request->emp_id AND t1.rout_id = $request->route_id
  GROUP BY t3.site_id,
  t4.site_code,
  t4.site_name,
  t4.site_ownm,
  t3.rspm_serl,
  t4.site_mob1,
  t4.site_adrs,
  t4.geo_lat,
  t4.geo_lon,
  t4.site_vtrn,
  t4.otcg_id,
  t5.cont_id ;
  ");
        }
        return $data1;

    }
    public
        function GetSRTodayOutletList(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $Ou_id = $request->ou_id;
        $slgp_id = $request->slgp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
  SELECT p.* FROM
(SELECT
Outlet_ID                                                 AS Outlet_ID,
Outlet_Code                                               AS Outlet_Code,
Outlet_Name                                               AS Outlet_Name,
Owner_Name                                                AS Owner_Name,
Group_ID                                                  AS Group_ID,
outlet_serial                                             AS outlet_serial,
Mobile_No                                                 AS Mobile_No,
Outlet_Address                                            AS Outlet_Address,
geo_lat                                                   AS geo_lat,
geo_lon                                                   AS geo_lon,
payment_type                                              AS payment_type,
Site_Limit                                                AS Site_Limit,
Site_overdue                                              AS Site_overdue,
Site_Order_Expire_Day                                     AS Site_Order_Expire_Day,
round(Site_Balance)                                              AS Site_Balance,
round(avail)                                                     AS avail,
Site_Discount_ID                                          AS Site_Discount_ID,
VAT_TRN                                                   AS VAT_TRN,
Sub_Channel                                               AS Channel,
Sub_Channel                                               AS Sub_Channel,
delivery_schedule                                         AS delivery_schedule,
is_credit                                                 AS is_credit
FROM
(SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days + t5.stcm_xday                                AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel,
  concat('DM-',t9.dm_name,' # ',t9.rout_day,' # route-',t9.rout_name)AS delivery_schedule,
  IF(COUNT(DISTINCT date(t11.`created_at`))>1,'1','0')      AS is_credit 
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id and t2.slgp_id='$slgp_id'
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.slgp_id=t2.slgp_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  LEFT JOIN tl_dmst_rout t9 ON(t4.id=t9.site_id)
  LEFT JOIN tl_cpcr t11 ON t4.id=t11.site_id AND t11.trnt_id=4 AND t11.scol_amnt=0 
  WHERE t1.aemp_id = $emp_id AND t1.rout_id = $route_id AND t5.acmp_id = $Ou_id and t5.slgp_id=$slgp_id
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, 
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,t5.stcm_xday,
  t5.stcm_ordm,t9.dm_name,t9.rout_day,t9.rout_name,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
  ORDER BY `t3`.`rspm_serl` ASC)g
  UNION ALL 
  (SELECT
  t9.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days + t5.stcm_xday                                AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel,
  ''                                                         AS delivery_schedule,
  '0'                                                        AS is_credit                                                                                                
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id and t2.slgp_id='$slgp_id'
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id     
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tl_ovpm t9 ON (t4.id = t9.site_id)AND t1.aemp_id=t9.aemp_id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.slgp_id=t2.slgp_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id) 
  WHERE t9.aemp_id = $emp_id  AND t5.acmp_id = $Ou_id AND t9.ovpm_date=CURDATE() AND t5.slgp_id=$slgp_id
   GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, 
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,t5.stcm_xday,
  t5.stcm_ordm,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id)) p
  GROUP BY p.Outlet_ID,
 p.Outlet_Code,
 p.Outlet_Name,
 p.Owner_Name,
 p.Group_ID,
 p.outlet_serial,
 p.Mobile_No,
 p.Outlet_Address,
 p.geo_lat,
 p.geo_lon,
 p.payment_type,
 p.Site_Limit,
 p.Site_overdue,
 p.Site_Order_Expire_Day,
 p.Site_Balance,
 p.avail,
 p.Site_Discount_ID,
 p.VAT_TRN,
 p.Channel,
 p.Sub_Channel,
 p.delivery_schedule,is_credit
  ");
            /*$data1 = DB::connection($db_conn)->select("
    SELECT
    t3.site_id                                   AS Outlet_ID,
    t4.site_code                                 AS Outlet_Code,
    t4.site_name                                 AS Outlet_Name,
    t4.site_ownm                                 AS Owner_Name,
    t2.slgp_id                                   AS Group_ID,
    t3.rspm_serl                                 AS outlet_serial,
    t4.site_mob1                                 AS Mobile_No,
    t4.site_adrs                                 AS Outlet_Address,
    t4.geo_lat                                   AS geo_lat,
    t4.geo_lon                                   AS geo_lon,
    t5.optp_id                                   AS payment_type,
    t5.stcm_limt                                 AS Site_Limit,
    t5.stcm_odue                                 AS Site_overdue,
    t5.stcm_days                                 AS Site_Order_Expire_Day,
    round(t5.stcm_limt-(t5.stcm_duea+t5.stcm_ordm),2)AS Site_Balance,
    round((t5.stcm_duea+t5.stcm_ordm),2)         AS avail,
    IF(t8.dfdm_id IS NULL or t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
    t4.site_vtrn                                 AS VAT_TRN,
    t6.chnl_name                                 AS Channel,
    t7.scnl_name                                 AS Sub_Channel
    FROM tl_rpln AS t1
    INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
    INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
    INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
    INNER JOIN tl_stcm t5 ON(t3.site_id=t5.site_id)
    LEFT JOIN tm_chnl t6 ON(t4.scnl_id=t6.id)
    LEFT JOIN tm_scnl t7 ON(t6.id=t7.chnl_id)
    LEFT JOIN tl_dfsm t8 ON(t4.id=t8.site_id)
    WHERE t1.aemp_id = '$emp_id'AND t1.rout_id='$route_id'
    GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
    t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id,t5.stcm_limt,t5.stcm_duea,t5.stcm_days,t5.stcm_ordm,
    t4.site_vtrn,t6.chnl_name , t7.scnl_name,t5.stcm_odue,t8.dfdm_id
    ORDER BY  t3.rspm_serl
    ");*/

        }

        return $data1;

    }

    public
        function GetCumulativeVisited_Site_Details_Data(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $Ou_id = $request->ou_id;
        $slgp_id = $request->slgp_id;
        $country1 = (new Country())->country($request->country_id);
        $cont_tzon = $country1->cont_tzon;
        $date_1 = Carbon::now($cont_tzon)->format('Y-m-1');
        $date_cur = Carbon::now($cont_tzon)->format('Y-m-d');


        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days + t5.stcm_xday                                AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel,
  concat('DM-',t9.dm_name,' # ',t9.rout_day,' # route-',t9.rout_name)AS delivery_schedule,
  IF(t11.site_id IS NULL, '0',  round(sum(t11.ordm_amnt),2))as Order_amt,
  IF(t11.site_id IS NULL, '0', sum(t11.ordm_memo))AS Memo_count,
  IF(t11.site_id IS NULL, 'Not Visit', 'Visited') AS visit_status, 
  IF(t11.site_id IS NULL, 'Not Visit', IF(t11.ordm_amnt > 0, 'Productive', 'Non-Productive'))   AS visit_type
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id and t2.slgp_id=$slgp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.slgp_id=t2.slgp_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  LEFT JOIN tl_dmst_rout t9 ON(t4.id=t9.site_id)
  LEFT JOIN tbl_olt_cov_details t11 ON t1.aemp_id=t11.aemp_id and t4.id=t11.site_id AND (t11.`ordm_date` BETWEEN '$date_1' and '$date_cur')
  WHERE t1.aemp_id =$emp_id AND t5.acmp_id = $Ou_id and t5.slgp_id=$slgp_id 
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, 
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,t5.stcm_xday,
  t5.stcm_ordm,t9.dm_name,t9.rout_day,t9.rout_name,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id,t11.ordm_amnt
  ORDER BY `t3`.`rspm_serl` ASC;
                ");
            return $data1;
        }
    }

    public
        function halfDayFullDayReport(
        Request $request
    ) {
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;

            if ($db_conn != '') {
                $country1 = (new Country())->country($request->country_id);
                $cont_tzon = $country1->cont_tzon;
                $date = Carbon::now($cont_tzon)->format('Y-m-d');
                $da_st = $date . ' ' . '06:00:00';
                $da_set = $date . ' ' . '13:00:59';
                $daf_st = $date . ' ' . '13:01:00';
                $daf_set = $date . ' ' . '23:59:59';

                $sql_1st = DB::connection($db_conn)->select(
                    "SELECT sum(TP_AMT)T_AMT,sum(VISITED_PRO)VISITED_PRO, sum(VISITED_NON) VISITED_NON FROM
(SELECT round(SUM(ordm_amnt),2)AS TP_AMT,(COUNT(`site_id`))AS VISITED_PRO,0 AS VISITED_NON
FROM `tt_ordm` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$da_st'AND'$da_set')
UNION ALL
SELECT 0 T_AMT,0 VISITED_PRO,COUNT(`site_id`)AS VISITED_NON 
FROM `tt_npro` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$da_st'AND'$da_set'))t1"
                );

                $sql_2nd = DB::connection($db_conn)->select(
                    "SELECT sum(TP_AMT)T_AMT,sum(VISITED_PRO)VISITED_PRO, sum(VISITED_NON) VISITED_NON FROM
(SELECT round(SUM(ordm_amnt),2)AS TP_AMT,(COUNT(`site_id`))AS VISITED_PRO,0 AS VISITED_NON
FROM `tt_ordm` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$daf_st'AND'$daf_set')
UNION ALL
SELECT 0 T_AMT,0 VISITED_PRO,COUNT(`site_id`)AS VISITED_NON 
FROM `tt_npro` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$daf_st'AND'$daf_set'))t1"
                );

                $sql_rout_out = DB::connection($db_conn)->select(
                    "SELECT count(t3.site_id)AS t_outlet  
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id 
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  WHERE t1.aemp_id=$request->aemp_id AND t1.rout_id=$request->rout_id;"
                );

                return array(
                    "first_half" => $sql_1st[0],
                    "second_half" => $sql_2nd[0],
                    "rout_outlet" => $sql_rout_out[0],
                    "response_time" => date('Y-m-d h:i:s'),
                );
            }

        }
    }

    public
        function GetXVisited_Site_Details_Data(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $Ou_id = $request->ou_id;
        $slgp_id = $request->slgp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                
                SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days + t5.stcm_xday                                AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id and t2.slgp_id=$slgp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.slgp_id=t2.slgp_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  LEFT JOIN tl_xvot t9 ON t5.site_id=t9.site_id
  LEFT JOIN th_ssvh t10 ON t9.site_id=t10.site_id
  WHERE t1.aemp_id = $emp_id 
  AND t5.acmp_id = $Ou_id 
  and t5.slgp_id=$slgp_id 
  and t10.ssvh_date >=SUBDATE(DATE(NOW()),t9.xvot_vday)AND t10.aemp_id=$emp_id AND t10.ssvh_ispd !=2
  AND t9.lfcl_id=1
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, 
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,t5.stcm_xday,
  t5.stcm_ordm,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
  ORDER BY `t3`.`rspm_serl` ASC;
                ");
            return $data1;
        }
    }

    public
        function CheckINSyncAllData_TempSiteMerge(
        Request $request
    ) {

        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();
        $data9 = array();
        $data10 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t1.amim_code, 2)                              AS column_id,
  t5.plmt_id                                           AS Item_Price_List,
  t5.plmt_id                                           AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  t1.amim_imic                                         AS amim_imic,
  t4.issc_name                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t6.pldt_tppr                                         AS Item_Rate,
  t6.pldt_tpgp                                         AS Grv_Item_Price,
  (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t6.amim_duft                                         AS Item_Factor,
  t4.issc_seqn                                         AS category_Showing_seqn,
  t6.amim_dunt                                         AS D_Unit,
  t6.amim_runt                                         AS R_Unit,
  t2.attr4                                             AS min_oqty,
  if(t9.amim_id IS NULL,true,false)                    AS is_special_dis

FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id AND t3.slgp_id=$request->slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tm_slgp t5 ON t3.slgp_id = t5.id AND t5.id=$request->slgp_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
LEFT JOIN tt_outs t8 ON(t6.amim_id=t8.amim_id) AND t8.dpot_id=$request->dpot_id
LEFT JOIN tl_npit t9 ON(t2.amim_id=t9.amim_id) AND t2.slgp_id=t9.slgp_id
WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id 
AND t5.acmp_id = $request->ou_id AND t8.id IS NULL;
");

            /*
            $data3 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.amim_code, $request->site_id )              AS column_id,
    t1.id                                                 AS Item_Id,
    t1.amim_code                                          AS Item_Code,
    if(t2.dfim_disc > 0, t2.dfim_disc, 0)                 AS Item_Default_Dis
    FROM tm_amim t1 JOIN tm_dfim AS t2 ON (t1.id = t2.amim_id)
    JOIN tl_dfsm AS t3 ON t3.dfdm_id = t2.dfdm_id
    JOIN tm_dfdm AS t4 ON t4.id = t3.dfdm_id
    WHERE t1.lfcl_id = 1 AND t3.slgp_id=$request->slgp_id
      AND t3.site_id = $request->site_id
      AND (curdate() BETWEEN t4.start_date AND t4.end_date) AND t4.lfcl_id = 1
    GROUP BY t1.id, t1.amim_code, t2.dfim_disc;
    ");


            $data4 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
    t1.id                                                AS promo_id,
    t1.prms_name                                         AS promo_name,
    t1.prms_sdat                                         AS start_date,
    t1.prms_edat                                         AS end_date,
    t1.prmr_qfct                                         AS qualifier_category,
    t1.prmr_ditp                                         AS discount_type,
    t1.prmr_ctgp                                         AS category_group,
    t1.prmr_qfon                                         AS qualifier_on,
    t1.prmr_qfln                                         AS qualifier_line
    FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
    WHERE t2.site_id = $request->site_id AND t1.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
    GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
    ");
            $data5 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
    t1.`mspm_id`AS MSP_ID,
    t1.amim_id AS MSP_Item_ID,
    t1.`mspd_qnty`AS MSP_Item_Qty,
    '2' AS Status_ID
    FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
    LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
    LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
    WHERE t3.site_id='$request->site_id' AND t3.slgp_id=$request->slgp_id
    AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
    GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`,t1.amim_id
    ");
            $data6 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.id, t1.nopr_name) AS column_id,
    concat(t1.id, t1.nopr_name) AS token,
    t1.id                       AS Reason_id,
    t1.nopr_name                AS Reason_Name
    FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.id, t1.dprt_name) AS column_id,
    concat(t1.id, t1.dprt_name) AS token,
    t1.id                       AS Reason_id,
    t1.dprt_name                AS Reason_Name
    FROM tm_dprt AS t1
    ");
            $data8 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
    t1.prmr_id                                                            AS promo_id,
    t1.amim_id                                                            AS product_id,
    t1.prmd_modr                                                          AS pro_modifier_id
    FROM tm_prmd AS t1
    INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
    INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
    WHERE t3.site_id =$request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
    GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
    ");

            $data9 = DB::connection($db_conn)->select("
    SELECT
    concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
    t1.prmr_id                                                                            AS promo_id,
    t1.prsb_text                                                                          AS promo_slab_text,
    t1.prsb_fqty                                                                          AS from_qty,
    t1.prsb_tqty                                                                          AS to_qty,
    0                                                                                     AS unit,
    0                                                                                     AS unit_factor,
    t1.prsb_famn                                                                          AS from_amnt,
    t1.prsb_tamn                                                                          AS to_amnt,
    t1.prsb_qnty                                                                          AS qty,
    0                                                                                     AS given_unit,
    0                                                                                     AS given_unit_factor,
    t1.prsb_disc                                                                          AS discount,
    t1.prsb_modr                                                                          AS pro_modifier_id,
    t1.prsb_mosl                                                                          AS pro_modifier_id_sl
    FROM tm_prsb AS t1
    INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
    INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
    WHERE t3.site_id = $request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
    GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
    t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
    ");

            $data10 = DB::connection($db_conn)->select("
    SELECT
    concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
    t1.prmr_id                                                            AS promo_id,
    t1.amim_id                                                            AS product_id,
    t1.prmd_modr                                                          AS pro_modifier_id
    FROM tm_prmf AS t1
    INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
    INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
    WHERE t3.site_id =$request->site_id AND t2.prmr_qfgp=$request->slgp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
    GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
    ");*/

        }
        return array(
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "promotion" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data8, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data9, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data10, "action" => $request->country_id),
        );
    }

    public
        function GetTemp_Site_Details_Data(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        //$Ou_id = $request->ou_id;
        $slgp_id = $request->slgp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                
                SELECT
  t4.id                                                      AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  $slgp_id           AS Group_ID,
  0                  AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  1                  AS payment_type,
  10000              AS Site_Limit,
  0                  AS Site_overdue,
  45                 AS Site_Order_Expire_Day,
  10000              AS Site_Balance,
  0                  AS avail,
  0                  AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  'Temp'             AS Channel,
  'Temp'             AS Sub_Channel
  FROM tm_site AS t4 
  WHERE t4.`aemp_iusr` = $emp_id AND date(t4.`created_at`) =CURDATE()AND t4.lfcl_id=1 AND t4.site_vrfy=0
  GROUP BY t4.id, t4.site_code, t4.site_name, t4.site_ownm,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, 
  t4.site_vtrn
  ORDER BY `t4`.`id` ASC;
                ");
            return $data1;
        }
    }


    public
        function GetSRTodayOutletListSearch(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $search_text = $request->search_text;
        $ou_id = $request->ou_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            /*      $data1 = DB::connection($db_conn)->select("
        SELECT
        t3.site_id                                   AS Outlet_ID,
        t4.site_code                                 AS Outlet_Code,
        t4.site_name                                 AS Outlet_Name,
        t4.site_ownm                                 AS Owner_Name,
        t2.slgp_id                                   AS Group_ID,
        t3.rspm_serl                                 AS outlet_serial,
        t4.site_mob1                                 AS Mobile_No,
        t4.site_adrs                                 AS Outlet_Address,
        t4.geo_lat                                   AS geo_lat,
        t4.geo_lon                                   AS geo_lon,
        t5.optp_id                                   AS payment_type,
        t5.stcm_limt                                 AS Site_Limit,
        t5.stcm_odue                                 AS Site_overdue,
        t5.stcm_days                                 AS Site_Order_Expire_Day,
        round(t5.stcm_limt-(t5.stcm_duea+t5.stcm_ordm),2)AS Site_Balance,
        round((t5.stcm_duea+t5.stcm_ordm),2)         AS avail,
        IF(t8.dfdm_id IS NULL or t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
        t4.site_vtrn                                 AS VAT_TRN,
        t6.chnl_name                                 AS Channel,
        t7.scnl_name                                 AS Sub_Channel
      FROM tl_rpln AS t1
        INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
        INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
        INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
        INNER JOIN tl_stcm t5 ON(t3.site_id=t5.site_id)
        LEFT JOIN tm_chnl t6 ON(t4.scnl_id=t6.id)
        LEFT JOIN tm_scnl t7 ON(t6.id=t7.chnl_id)
        LEFT JOIN tl_dfsm t8 ON(t4.id=t8.site_id)
      WHERE t1.aemp_id = '$emp_id'AND t1.rout_id='$route_id' AND  t4.site_name LIKE'%$search_text%'
      GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
        t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id,t5.stcm_limt,t5.stcm_duea,t5.stcm_days,t5.stcm_ordm,
        t4.site_vtrn,t6.chnl_name , t7.scnl_name,t5.stcm_odue,t8.dfdm_id
         ORDER BY  t3.rspm_serl
        ");*/

            $data1 = DB::connection($db_conn)->select("
  SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
 t5.stcm_days + t5.stcm_xday                                 AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id)
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  WHERE t1.aemp_id = '$emp_id' AND t1.rout_id = '$route_id' AND t5.acmp_id = '$ou_id' AND  t4.site_name ='$search_text'
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
  t5.stcm_ordm,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
  ORDER BY `t3`.`site_id` ASC
  ");

        }

        return $data1;

    }

    public
        function GetSRTodayOutletListSearchQRCode(
        Request $request
    ) {
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $search_qrcode = $request->outlet_code;
        $ou_id = $request->ou_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            /* $data1 = DB::connection($db_conn)->select("
    SELECT
    DISTINCT t3.site_id                                   AS Outlet_ID,
    t4.site_code                                 AS Outlet_Code,
    t4.site_name                                 AS Outlet_Name,
    t4.site_ownm                                 AS Owner_Name,
    t2.slgp_id                                   AS Group_ID,
    t3.rspm_serl                                 AS outlet_serial,
    t4.site_mob1                                 AS Mobile_No,
    t4.site_adrs                                 AS Outlet_Address,
    t4.geo_lat                                   AS geo_lat,
    t4.geo_lon                                   AS geo_lon,
    t5.optp_id                                   AS payment_type,
    t5.stcm_limt                                 AS Site_Limit,
    t5.stcm_odue                                 AS Site_overdue,
    t5.stcm_days                                 AS Site_Order_Expire_Day,
    round(t5.stcm_limt-(t5.stcm_duea+t5.stcm_ordm),2)AS Site_Balance,
    round((t5.stcm_duea+t5.stcm_ordm),2)         AS avail,
    IF(t8.dfdm_id IS NULL or t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
    t4.site_vtrn                                 AS VAT_TRN,
    t6.chnl_name                                 AS Channel,
    t7.scnl_name                                 AS Sub_Channel
    FROM tl_rpln AS t1
    INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
    INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
    INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
    INNER JOIN tl_stcm t5 ON(t3.site_id=t5.site_id)
    LEFT JOIN tm_chnl t6 ON(t4.scnl_id=t6.id)
    LEFT JOIN tm_scnl t7 ON(t6.id=t7.chnl_id)
    LEFT JOIN tl_dfsm t8 ON(t4.id=t8.site_id)
    WHERE t1.aemp_id = '$emp_id'AND t1.rout_id='$route_id' AND  t4.site_code ='$search_qrcode'
    GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
    t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id,t5.stcm_limt,t5.stcm_duea,t5.stcm_days,t5.stcm_ordm,
    t4.site_vtrn,t6.chnl_name , t7.scnl_name,t5.stcm_odue,t8.dfdm_id
    ");*/
            $data1 = DB::connection($db_conn)->select("
  SELECT
  t3.site_id                                                 AS Outlet_ID,
  t4.site_code                                               AS Outlet_Code,
  t4.site_name                                               AS Outlet_Name,
  t4.site_ownm                                               AS Owner_Name,
  t2.slgp_id                                                 AS Group_ID,
  t3.rspm_serl                                               AS outlet_serial,
  t4.site_mob1                                               AS Mobile_No,
  t4.site_adrs                                               AS Outlet_Address,
  t4.geo_lat                                                 AS geo_lat,
  t4.geo_lon                                                 AS geo_lon,
  t5.optp_id                                                 AS payment_type,
  t5.stcm_limt                                               AS Site_Limit,
  t5.stcm_odue                                               AS Site_overdue,
  t5.stcm_days                                               AS Site_Order_Expire_Day,
  round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
  round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
  IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
  t4.site_vtrn                                               AS VAT_TRN,
  t7.chnl_name                                               AS Channel,
  t6.scnl_name                                               AS Sub_Channel
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id)
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  WHERE t1.aemp_id = '$emp_id' AND t1.rout_id = '$route_id' AND t5.acmp_id = '$ou_id' AND  t4.site_code ='$search_qrcode'
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
  t5.stcm_ordm,
  t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
  ORDER BY `t3`.`site_id` ASC
  ");

        }

        return $data1;

    }

    public
        function updateOutlet(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  t1.id        AS Outlet_ID,
  t1.site_code AS Outlet_Code,
  t1.site_name AS Outlet_Name,
  t1.site_olnm AS Outlet_Name_BN,
  t1.site_ownm AS owner_name,
  t1.site_mob1 AS Mobile_No,
  t1.site_mob2 AS Mobile_No_2,
  t1.site_adrs AS Address,
  t1.site_olad AS Address_BN
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
WHERE t2.rout_id=$request->route_id
GROUP BY t1.id,
  t1.site_code,
  t1.site_name,
  t1.site_olnm,
  t1.site_ownm,
  t1.site_mob1,
  t1.site_mob2,
  t1.site_adrs,
  t1.site_olad 
");
        }
        return $data1;

    }

    public
        function outletCategory(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("select t1.id as Category_Code,t1.otcg_name as Category_Name from tm_otcg as t1");

        }
        return $data1;

    }

    public
        function govDistrict(Request $request) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $module_type = $country->module_type;
        if ($db_conn != '') {
			
			 if ($module_type ==2) {
				 $data1 = DB::connection($db_conn)->select("
			SELECT t2.id as dsct_id,t2.dsct_code AS district_code,t2.dsct_name AS district_name
			FROM `tl_srds`t1 
			JOIN tm_dsct t2 ON t1.`dsct_id`=t2.id
			WHERE t1.`aemp_id`=$request->aemp_id"); 
			 }else{
				 $data1 = DB::connection($db_conn)->select("SELECT
		  id        AS district_code,
		  dsct_name AS district_name
		FROM tm_dsct
		WHERE `lfcl_id` = 1
		ORDER BY dsct_name"); 
			 }           
        }
        return $data1;

    }

    public
        function govThana(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_code = $request->send_district_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  t1.`id` as Thana_Code,
  t1.than_name AS Thana_Name
FROM `tm_than`t1 
JOIN tm_dsct t2 ON t1.`dsct_id`=t2.id
WHERE t1.`lfcl_id` = 1 AND
      t2.dsct_code = '$dsct_code'
ORDER BY t1.than_name;");

        }
        return $data1;

    }

    public
        function govWard(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $than_id = $request->send_thana_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT `id` as ward_code,`ward_name` as ward_name
FROM `tm_ward`
WHERE `LFCL_ID`='1' AND
      `THAN_ID`='$than_id' Order by ward_name");

        }
        return $data1;

    }

    public
        function market(
        Request $request
    ) {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $ward_id = $request->Ward_Code_Send;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  `id` as market_id,
  mktm_name AS `market_name`
FROM `tm_mktm`
WHERE `lfcl_id` = '1' AND
      `ward_id` = '$ward_id'
ORDER BY mktm_name");

        }
        return $data1;

    }

    public
        function GetOutletNameAndID(
        Request $request
    ) {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT t1.site_name AS OutLet_Name,t2.site_id AS OutLet_ID
        FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
		where t2.ordm_date = '$Order_Date' 
        AND t2.aemp_id='$SR_ID'
        AND t2.lfcl_id=1
        GROUP BY t1.site_name,t2.site_id
        ");

        }
        return $data1;

    }

    public
        function GetOutletWiseOrderID(
        Request $request
    ) {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;
        $OutLet_ID = $request->OutLet_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT t2.ordm_ornm AS Order_Code,t2.id AS Order_ID
        FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
		where t2.ordm_date = '$Order_Date' 
        AND t2.aemp_id='$SR_ID'
        AND t2.site_id='$OutLet_ID'
        AND t2.lfcl_id=1
        GROUP BY t2.id,t2.ordm_ornm
        ");

        }
        return $data1;

    }

    public
        function GetDMTripItemStockDetails(
        Request $request
    ) {
        $data1 = array();
        $TRIP_Code = $request->TRIP_Code;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT t9.item_id,t9.item_code,t9.amin_snme,t9.INV_QTY,t9.DELV_QNTY,(t9.STK_QTY+t9.GRV_G+t9.GRV_B)AS tSTK_QTY,t9.GRV_G,t9.GRV_B,t9.factor,t9.rate 
         FROM
           (SELECT item_id,item_code,amin_snme,INV_QTY,DELV_QNTY,STK_QTY,GRV_G,GRV_B,factor,rate
           from (SELECT t1.`AMIM_ID`AS item_id,t1.AMIM_CODE AS item_code,t2.amin_snme AS amin_snme,SUM(t1.`INV_QNTY`)AS INV_QTY,SUM(t1.`DELV_QNTY`)AS DELV_QNTY,
           SUM(t1.`INV_QNTY`-t1.`DELV_QNTY`)AS STK_QTY,0 GRV_G,0 GRV_B,t2.amim_duft AS factor,t1.ORDD_UPRC AS rate
           FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id)
           WHERE t1.`TRIP_NO`='$TRIP_Code'
           GROUP BY t1.`AMIM_ID`,t1.AMIM_CODE,t1.ORDD_UPRC)tg
       UNION ALL
           (SELECT t2.`amim_id`AS item_id,t4.amim_code AS item_code,t4.amin_snme AS amin_snme,0 INV_QTY,0 DELV_QNTY,
           0 STK_QTY,if(t2.rtdd_ptyp=2,SUM(t2.`rtdd_dqty`),0) GRV_G,if(t2.rtdd_ptyp=1,SUM(t2.`rtdd_dqty`),0) GRV_B,t4.amim_duft AS factor,t2.rtdd_uprc AS rate
           FROM tt_rtan t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
           JOIN `dm_trip`t3 ON(t1.dm_trip=t3.`TRIP_NO`)
           JOIN tm_amim t4 ON(t2.`amim_id`=t4.id)
           WHERE t1.`dm_trip`='$TRIP_Code'
           GROUP BY t2.`amim_id`,t4.amim_code,t2.rtdd_uprc,t2.rtdd_ptyp))t9
           GROUP BY t9.item_id,t9.item_code,t9.amin_snme,t9.STK_QTY,t9.INV_QTY,t9.DELV_QNTY,t9.GRV_G,t9.GRV_B,t9.factor,t9.rate;
        ");

        }
        return $data1;

    }

    public
        function GetVanTripItemStockDetails(
        Request $request
    ) {
        $data1 = array();
        $TRIP_Code = $request->TRIP_Code;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT
              trp_n TrpNumber,
              tm.Item_Id AS item_id,
              tm.Item_Code AS item_code,
              Item_Name AS amin_snme,
              sum(Rec_Qty) AS INV_QTY,
              ifnull(tn.delQnty,0) DELV_QNTY,
              sum(Rec_Qty)-ifnull(tn.delQnty,0) as tSTK_QTY,
              ifnull(tg.GRV_G,0) GRV_G,
              ifnull(tg.GRV_B,0)GRV_B,
              Item_Factor AS factor,
              avg(Item_Rate) AS rate
              from(              
              SELECT
              t8.TRIP_NO trp_n,
              t1.id                  AS Item_Id,
              t1.amim_code                       AS Item_Code,
              t1.amim_name                       AS Item_Name,
              t9.ORDD_UPRC                       AS Item_Rate,
              t1.amim_duft                       AS Item_Factor,
			  sum(t9.INV_QNTY)                 AS Rec_Qty
             FROM `tm_amim`t1 JOIN dm_van_trip_detail t9 ON(t9.`AMIM_ID`=t1.id)
             INNER JOIN dm_trip AS t8 ON t9.TRIP_NO=t8.TRIP_NO
             WHERE  t1.lfcl_id=1 AND
             t8.TRIP_NO IN('$TRIP_Code')
             GROUP BY t8.TRIP_NO,t1.id,t1.amim_code,t1.amim_name,t9.ORDD_UPRC,t1.amim_duft) as tm 
	         left join (
             select TRIP_NO, Amim_id,sum(delQnty) delQnty from(
             select t10.TRIP_NO, t10.Amim_id,t10.DELV_QNTY delQnty from  dm_trip_detail t10 where  t10.TRIP_NO IN('$TRIP_Code')
             ) as tc   group by TRIP_NO,Amim_id             
             ) tn on tm.Item_Id = tn.Amim_id and tn.TRIP_NO=tm.trp_n  
			left join ( SELECT t1.dm_trip, t2.`amim_id`AS item_id,t4.amim_code AS item_code,
           0 STK_QTY,if(t2.rtdd_ptyp=2,SUM(t2.`rtdd_dqty`),0) GRV_G,if(t2.rtdd_ptyp=1,SUM(t2.`rtdd_dqty`),0) GRV_B
           FROM tt_rtan t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
           JOIN `dm_trip`t3 ON(t1.dm_trip=t3.`TRIP_NO`)
           JOIN tm_amim t4 ON(t2.`amim_id`=t4.id)
           WHERE t1.`dm_trip`IN('$TRIP_Code')
           GROUP BY t2.`amim_id`,t4.amim_code,t2.rtdd_uprc,t2.rtdd_ptyp) tg
           on tm.Item_Id = tg.item_id and tg.dm_trip=tm.trp_n               
          group by  trp_n , tm.Item_Id, Item_Code,Item_Name, Item_Factor;
        ");
        }
        return $data1;

    }

    public
        function GetVanTripItemLoadDetails(
        Request $request
    ) {
        $data1 = array();
        $TRIP_Code = $request->TRIP_Code;
        $load_id = $request->load_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
       SELECT t4.amim_id AS item_id,t5.`amim_code`AS item_code,t5.`amin_snme` AS amin_snme,
        t4.lodl_qnty AS INV_QTY,t4.lodl_cqty AS DELV_QNTY,t4.lodl_cqty AS tSTK_QTY,0 AS GRV_G,
        0 AS GRV_B,t5.`amim_duft`AS factor,t4.lodl_uprc AS rate 
        FROM `dm_trip`t1 JOIN tt_trip t2 ON(t1.`id`=t2.trip_otid)
        JOIN tt_lodm t3 ON(t2.id=t3.trip_id)
        JOIN tt_lodl t4 ON(t3.id=t4.lodm_id)
        JOIN tm_amim t5 ON(t4.amim_id=t5.id)
        WHERE t1.`TRIP_NO`='$TRIP_Code' AND t4.`lodm_id`='$load_id' AND t1.STATUS='N';
        ");
        }
        return $data1;

    }

    public
        function GetVanTripLoadRequestDetails(
        Request $request
    ) {
        $data1 = array();
        $TRIP_Code = $request->TRIP_Code;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            /* $data1 = DB::connection($db_conn)->select("
         SELECT Concat(t2.trip_code,t3.id) AS request_code,
         t3.id AS request_id,
         if(t4.lodl_cqty>0,'Verified','Pending')AS status,
         t2.trip_date AS request_date
         FROM `dm_trip`t1 JOIN tt_trip t2 ON(t1.`id`=t2.trip_otid)
         JOIN tt_lodm t3 ON(t2.id=t3.trip_id)
         JOIN tt_lodl t4 ON(t3.id=t4.lodm_id)
         WHERE t1.`TRIP_NO`='$TRIP_Code' AND t1.STATUS='N';
         "); */

            $data1 = DB::connection($db_conn)->select("
        SELECT Concat(t2.trip_code,t3.id) AS request_code,
        t3.id AS request_id,
        if(t3.`lfcl_id`=31,'Verified','Pending')AS status,
        t2.trip_date AS request_date
        FROM `dm_trip`t1 JOIN tt_trip t2 ON(t1.`id`=t2.trip_otid)
        JOIN tt_lodm t3 ON(t2.id=t3.trip_id)
        WHERE t1.`TRIP_NO`='$TRIP_Code' AND t1.STATUS='N'
        GROUP BY t2.trip_code,t3.id,t2.trip_date  
        ORDER BY `request_id`  DESC
        ");
        }
        return $data1;

    }

    public
        function GetOutletWiseOrderDetails(
        Request $request
    ) {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;
        $OutLet_ID = $request->OutLet_ID;
        $Order_ID = $request->Order_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
       SELECT 
t2.slgp_id AS Group_Id,
t2.dlrm_id AS Dealer_Id,
t2.rout_id AS Route_Id,
t1.`amim_id`AS Item_Id,
t3.amim_code AS Item_Code,
t3.amim_name As Item_Name,
t1.`ordd_qnty`As Order_Qty,
t1.`ordd_duft`As Item_Factor,
t1.`ordd_uprc`As Unit_Price,
t1.ordd_opds As Promotional_Discount,
t1.`ordd_oamt`AS Order_Amt,
t2.geo_lat,
t2.geo_lon,
'0'AS Item_Stock_B_Qty
       FROM `tt_ordd`t1 JOIN tt_ordm t2 ON(t2.id=t1.`ordm_id`)
       LEFT JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
       WHERE t2.id='$Order_ID' AND t2.ordm_date = '$Order_Date' 
       AND t2.aemp_id='$SR_ID'
       AND t2.lfcl_id=1
       AND t3.lfcl_id=1
       GROUP BY t2.slgp_id ,
t2.dlrm_id ,
t2.rout_id ,
t1.`amim_id`,
t3.amim_code ,
t3.amim_name ,
t1.`ordd_qnty`,
t1.`ordd_duft`,
t1.`ordd_uprc`,
t1.ordd_opds ,
t1.`ordd_oamt`,
t2.geo_lat,
t2.geo_lon
        ");

        }
        return $data1;

    }

    public
        function GetChallanWiseOrderData(
        Request $request
    ) {
        $data1 = array();
        $From_Date = $request->From_Date;
        $To_Date = $request->To_Date;
        $SR_ID = $request->SR_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,t2.lfcl_id AS Delivery_Status,t2.ordm_date
      FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
      AND t1.ordd_oamt!=0
      AND t3.lfcl_id=1 AND t2.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date
      UNION ALL
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,t2.lfcl_id AS Delivery_Status,t2.ordm_date
       FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
       AND t1.ordd_oamt=0
      AND t3.lfcl_id=1 AND t2.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date");

        }

        return $data1;

    }

}