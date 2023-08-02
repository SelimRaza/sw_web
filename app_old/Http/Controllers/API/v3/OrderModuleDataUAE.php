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
                    $orderSyncLog = OrderSyncLog::on($db_conn)->where(['oslg_moid' => $request->Order_Unique_ID])->first();
                    if ($orderSyncLog == null) {
                        $orderSequence = OrderSequence::on($db_conn)->where(['aemp_id' => $request->SR_ID, 'srsc_year' => date('y')])->first();
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
                        $order_id = "O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);
                        $order_amount = array_sum(array_column($orderLines, 'P_T_Price'));
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
                            $orderLine->ordd_oamt = $orderLineData->P_T_Price;
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
                    } else {
                        $msg = "Duplicate";
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
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                } else if ($productive == 1) {
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->attr1 = $request->Order_time;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($db_conn)->commit();
                // return array('column_id' => $line_id);
                return response()->json(array('column_id' => $line_id, 'message' => $msg));
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                // return $e;
                return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
                //throw $e;
            }

        }

    }

    public function saveReturnOrder(Request $request)
    {
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
                    $order_id = strtoupper("R" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
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

    public function SubmitSiteWiseInvoiceGrvDelivery(Request $request)
    {
        //  $sql .= "INSERT INTO MyGuests (firstname, lastname, email)
        //  VALUES ('Julie', 'Dooley', 'julie@example.com')";

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

        $outletData = json_decode($request->invoice_wise_delivery)[0];
        $outletData_child = json_decode($request->invoice_wise_delivery);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
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
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');
                    if ($request->type_id == 1) {

                        $sql1 = " Update tt_ordm set lfcl_id=11,ordm_drdt='$DRDT',ordm_dltm='$DRTM' WHERE ordm_ornm='$request->ORDM_ORNM'; ";

                        foreach ($outletData_child as $orderLineData) {
                            $amount = $orderLineData->Dev_QNTY * $orderLineData->Rate;

                            $sql2 .= " Update tt_ordd set ordd_dqty=$orderLineData->Dev_QNTY,ordd_odat=$amount  WHERE id=$orderLineData->trp_line; ";
                            $sql3 .= " Update dm_trip_detail set DELV_QNTY=$orderLineData->Dev_QNTY,DISCOUNT=$orderLineData->DISCOUNT,TRIP_STATUS='D',ORDM_DRDT='$DRDT' WHERE OID=$orderLineData->trp_line;";
                        }
                        $sql4 = " Update dm_trip_master set ORDM_DRDT='$DRDT',DELV_AMNT=$request->DELV_AMNT,DELIVERY_STATUS=11 WHERE ORDM_ORNM='$request->ORDM_ORNM';";
                    } else {
                        $now = now();
                        //  "SELECT `ACMP_CODE`,`WH_ID`,`DM_CODE` FROM `dm_trip_master` WHERE TRIP_NO='TRIP-0500-01-2111000005' GROUP BY TRIP_NO";
                        $dm_ACMP_WH_ID_DM_CODE = DB::connection($db_conn)->table('dm_trip_master')->where(['TRIP_NO' => $request->tpm_code])->first();

                        /* echo $dm_ACMP_WH_ID_DM_CODE;
                         die();*/

                        $last_3 = substr($now->timestamp . $now->milli, 10);
                        $COLL_NUMBER = "RC" . $dt . '-' . $last_3;


                        $sql5 = " Update tt_rtan set lfcl_id=11,dm_trip='$request->tpm_code',rtan_drdt='$DRDT',rtan_dltm='$DRTM' WHERE id=$request->tpm_id; ";
                        $sql6 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,slgp_id)
                                 VALUES (NULL,  '$dm_ACMP_WH_ID_DM_CODE->ACMP_CODE', '$DRDT', '$COLL_NUMBER', 'N', $request->emp_id,
                                  '$request->emp_code', '$dm_ACMP_WH_ID_DM_CODE->DM_CODE', '$dm_ACMP_WH_ID_DM_CODE->WH_ID',
                                  '$request->site_id', '$request->site_code',  $request->DELV_AMNT,0, 'GRV', 11, 'N', 5,$request->slgp_id); ";

                        foreach ($outletData_child as $orderLineData) {
                            $amountd = $orderLineData->Dev_QNTY * $orderLineData->Rate;
                            $sql7 .= " Update tt_rtdd set rtdd_dqty='$orderLineData->Dev_QNTY',rtdd_damt='$amountd' WHERE id=$orderLineData->trp_line; ";
                        }
                    }

                    /* echo $sql1;
                     echo $sql2;
                     echo $sql3;
                     echo $sql4;
                     die();*/
                    DB::connection($db_conn)->unprepared($sql);

                    if ($request->type_id == 1) {
                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->unprepared($sql2);//multiple row
                        DB::connection($db_conn)->unprepared($sql3);//multiple row
                        DB::connection($db_conn)->insert($sql4);
                    } else {
                        DB::connection($db_conn)->insert($sql5);
                        DB::connection($db_conn)->insert($sql6);
                        DB::connection($db_conn)->unprepared($sql7);//multiple row
                    }
                    DB::connection($db_conn)->commit();
                    // echo $retn;
                    // die();
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

    public function SubmitSiteWiseInvoiceCollection(Request $request)
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

        $outletData = json_decode($request->Collection_Data)[0];
        $outletData_child = json_decode($request->Collection_Data);


        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
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
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');

                    $now = now();
                    $last_3 = substr($now->timestamp . $now->milli, 10);
                    $COLL_NUMBER = "CL" . $dt . '-' . $last_3;
                    $now1 = now();
                    $last_31 = substr($now1->timestamp . $now1->milli, 10);
                    $COLL_NUMBER1 = "OC" . $dt . '-' . $last_31;

                    $sql1 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE,slgp_id)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$COLL_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code',  $request->COLL_AMNT,0, '$request->Coll_type',
                                   26, '$request->Ck_No', 1,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE','$request->slgp_id'); ";
                    $sql2 = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                               `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                                VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$COLL_NUMBER1','$request->site_id','$request->site_code',
                                $request->COLL_AMNT,0,0); ";

                    foreach ($outletData_child as $orderLineData) {

                        if ($orderLineData->Type == 1) {
                            $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,$orderLineData->Coll_Amt,0 ); ";

                            $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=(COLLECTION_AMNT+$orderLineData->Coll_Amt) 
                            WHERE ORDM_ORNM='$orderLineData->ORDM_ORNM' AND DELIVERY_STATUS=11 ;";
                        } else {
                            $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                $orderLineData->Coll_Amt,0,0 ); ";
                            $sql6 .= " Update dm_collection set COLL_REC_HO=$orderLineData->Coll_Amt,STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=5; ";
                        }
                    }

                    /* echo $sql1;
                     echo $sql2;
                     echo $sql3;
                     echo $sql4;
                     die();*/
                    DB::connection($db_conn)->unprepared($sql);

                    // if ($request->type_id == 1) {
                    DB::connection($db_conn)->insert($sql1);
                    DB::connection($db_conn)->insert($sql2);
                    DB::connection($db_conn)->unprepared($sql3);//multiple row
                    DB::connection($db_conn)->unprepared($sql4);//multiple row


                    // } else {
                    if (!empty($sql5)) {
                        DB::connection($db_conn)->unprepared($sql5);
                        DB::connection($db_conn)->unprepared($sql6);
                    }
                    DB::connection($db_conn)->commit();
                    // echo $retn;
                    // die();
                    return array(
                        'success' => 1,
                        'message' => "Collection Successfully",
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }
            }
        }
    }

    public function SubmitInvoiceSMS(Request $request)
    {
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

    public function getIsCashPartyCreditRequestExist(Request $request){
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
 

            

            if($isExistRequest){
              if($isExistRequest->trnt_id == 3){
                
                $success = 2;
                $message = "Already Requested !!!";
                $lastTimeInputCollection = $isExistRequest->attr3;
              }else if($isExistRequest->trnt_id == 4){
                $success = 3;
                $message = "Already Approved !!!";
                $lastTimeInputCollection = $isExistRequest->attr3;
              }else if($isExistRequest->trnt_id == 5){
               
                $success = 5;
                $message = "Request Cancel ";
                $lastTimeInputCollection = $isExistRequest->attr3;
              }
            }else{
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

    public function SubmitCashPartyCreditRequest(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $message = "";
            $success = 0;
            
            try {
                $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,t1.`aemp_id`AS mngr_id
             FROM `tm_scbm`t1 JOIN tm_aemp t2 ON(t1.`aemp_id`=t2.aemp_mngr) AND t2.id=$request->emp_id
             ");


                if ($sql_mngr) {
                  $Balance = $sql_mngr[0]->Balance;
                  $spbm_id = $sql_mngr[0]->spbm_id;
                  $mngr_id = $sql_mngr[0]->mngr_id;

                  $sql_cpcr = "INSERT INTO `tl_cpcr`(`spbm_id`, `site_id`, `ordm_ornm`, `ordm_amnt`, `sreq_amnt`,scol_amnt, 
                    `sapr_amnt`, `trnt_id`, `spbd_type`, `cont_id`, `lfcl_id`,
                    `aemp_iusr`, `aemp_eusr`,`attr3`)
                    VALUES ($spbm_id,'$request->site_id','$request->ordm_ornm',$request->ordm_amnt,$request->sreq_amnt,
                    0,0,3,'Credit Req',$request->country_id,'1','$request->up_emp_id','$request->up_emp_id',$request->coll_amnt);";

                  $sql_cpcr_approved = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm, 'trnt_id' => 4])->first();

                  $sql_cpcr_cancel = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm, 'trnt_id' => 5])->first();

                  $sql_cpcr_ck = DB::connection($db_conn)->table('tl_cpcr')->where(['site_id' => $request->site_id, 'ordm_ornm' => $request->ordm_ornm])->first();

                  if ($sql_cpcr_approved) {
                      $success = 3;
                      $message = "Already Approved !!!";
                  }else if($sql_cpcr_cancel){
                    $success = 5;
                    $message = "Request Cancel ";
                  }
                  
                  else if ($sql_cpcr_ck) {
                      $success = 2;
                      $message = "Already Requested !!!";
                  } else {
                      DB::connection($db_conn)->insert($sql);
                      DB::connection($db_conn)->insert($sql_cpcr);
                      $success = 1;
                      $message = "Request Successful";
                  }
                  DB::connection($db_conn)->commit();

              } else {
                  $success = 0;
                  $message = "request failed";
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

    public function GetCashPartyCreditRequestList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $manager_balance = DB::connection($db_conn)->select("
                          SELECT
                          round(t2.spbm_amnt,4)  AS mngr_balance               
                          FROM tt_spbm t2 
                          WHERE t2.aemp_id=$request->emp_id;
                        ");

            $req_list_item = DB::connection($db_conn)->select("
                          SELECT t1.`id`AS cpcr_id,t1.`site_id`,t2.site_name,t2.site_mob1 AS site_mob,t1.`ordm_ornm`,
                          t1.ordm_amnt,(t1.`sreq_amnt`-t1.`sapr_amnt`)AS req_amt
                          FROM `tl_cpcr`t1 
                          JOIN tm_site t2 ON(t1.`site_id`=t2.id)
                          JOIN tt_spbm t3 ON(t1.spbm_id=t3.id)
                          WHERE t3.aemp_id=$request->emp_id AND (t1.`sreq_amnt`-t1.`sapr_amnt`)>0 AND t1.trnt_id=3;
                        ");

            return array(
                'balance' => $manager_balance[0]->mngr_balance,
                'items' => $req_list_item,
            );


        }
    }

    public function SubmitCashPartyCreditApproved(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $message = "";
            $success = 0;
            try {
                $sql_mngr = DB::connection($db_conn)->select("SELECT  t1.spbm_amnt AS Balance, t1.`id` AS spbm_id ,t1.`aemp_id`AS mngr_id
                                  FROM `tt_spbm`t1 
                                  WHERE  t1.aemp_id=$request->emp_id;");

                $_msg_id = $sql_mngr[0]->mngr_id;
                if($request->status ==1){
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
                }else{
                  $sql_cancel = "UPDATE `tl_cpcr` SET trnt_id='5',spbd_type='Credit Rej', aemp_eusr=$_msg_id WHERE `id`=$request->cpcr_id";

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

    public function GetCashPartyCreditCollectionPendingList(Request $request)
    {
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

    public function SubmitCashPartyCollection(Request $request)
    {
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
                        '$request->coll_amnt', '3', '27', '1', '1')";//27=lfcl_id cash collection
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

    public function SubmitTripEOT(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $mgs = "";
            $cod = 0;
            try {

                $ret = DB::connection($db_conn)->table('dm_trip')->where(['TRIP_NO' => $request->TRIP_NO,
                    'STATUS' => 'N', 'DM_ID' => $request->DM_ID])->update(['STATUS' => 'R', 'DM_ACTIVITY' => '5']);

                if ($ret == 1) {
                    $cod = 1;
                    $mgs = "EOT Successful ";
                } else {
                    $cod = 0;
                    $mgs = "EOT Already complete ";
                }
                DB::connection($db_conn)->commit();
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

    public function Test(Request $request)
    {
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

    public function outletSave(Request $request)
    {


        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;

            //  if ($db_conn != '') {
            // if (!$db_conn == 2) {
            $country = Country::on($db_conn)->findorfail($outletData->country_id);
            //$site_code ='';
            // if ($outletData->country_id == 7) {
            //  $site_code = 'CO' . str_pad($country->attr4 + 1, 8, '0', STR_PAD_LEFT);
            // } else {
            $site_code = 'N' . str_pad($country->attr4 + 1, 9, '0', STR_PAD_LEFT);
            //  }

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
                $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                $site->site_reg = '';
                $site->site_hsno = '';
                $site->site_vtrn = '';
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
                $tempSite->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
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
            // }
            // }
        }
    }

    public function updateOutletSerial(Request $request)
    {
        $outletData = json_decode($request->Outlet_Serialize_Data)[0];
        $outletData_child = json_decode($request->Outlet_Serialize_Data);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    foreach ($outletData_child as $outletData_1) {
                        DB::connection($db_conn)->table('tl_rsmp')->where(['rout_id' => $outletData_1->Route_ID,
                            'site_id' => $outletData_1->Outlet_ID])->update(['rspm_serl' => $outletData_1->Outlet_Serial]);

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

    public function SubmitChallanWiseDelivery(Request $request)
    {
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


                        DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $orderLineData->SR_ID,
                            'ordm_date' => $orderLineData->Order_Date])->update(['lfcl_id' => 3]);


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

    public function updateOutletSave(Request $request)
    {
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
                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
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

    public function censusOutletImport(Request $request)
    {
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


    public function attendanceSave_new(Request $request)
    {

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
                $attendance->slgp_id = $request->Group_Id;;//$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID;// $request->SR_ID;
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

    public function attendanceSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                $path_name = $country->cont_imgf . '/' . date('Y-m-d') . '/attendance/' . uniqid() . '.jpg';
                if (isset($request['OutLet_Picture'])) {
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $path_name,
                        'Body' => base64_decode($request->OutLet_Picture),
                        'ContentType' => 'base64',
                        'ContentEncoding' => 'image/jpeg',
                        'ACL' => 'public-read',
                    ));
                }
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = $request->Group_Id;;//$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID;// $request->SR_ID;
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

    public function MasterData(Request $request)
    {
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


        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public function MasterDataNew(Request $request)
    {
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


        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public function MasterDataNew_Three(Request $request)
    {
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
        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
        );
    }

    public function GetPromoSlabDetails(Request $request)
    {
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

        return Array(
            "Buy" => array("data" => $data2, "action" => $request->country_id),
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public function GetPromoSingleFOCSlabDetails(Request $request)
    {
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

        return Array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public function GetItemOrderHistory(Request $request)
    {
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
WHERE t1.`site_id`='$site_id'AND t1.`lfcl_id`='11'
AND t1.acmp_id='$ou_id'AND t2.amim_id='$item_id' 
AND (t2.ordd_dqty-t2.ordd_rqty)>0
ORDER BY t1.`id` DESC
  ");
        }
        return Array(
            "GRV_Refer_Data" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public function GetPromoFOCSlabDetails(Request $request)
    {
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

        return Array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
            "Free" => array("data" => $data2, "action" => $request->country_id),
            "Buy" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public function CheckINSyncAllData(Request $request)
    {
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
        return Array(
            "Sync_t_prices_detail" => array("data" => $data1, "action" => $request->country_id),
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "Sync_T_Promotion_Table" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public function CheckINSyncAllData_Image(Request $request)
    {
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
INNER JOIN tl_stcm t4 ON t3.plmt_id = t4.plmt_id
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
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
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
        return Array(
            "Sync_t_prices_detail" => array("data" => $data1, "action" => $request->country_id),
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "Sync_T_Promotion_Table" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public function CheckINSyncAllData_Merge(Request $request)
    {

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
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id  AND t5.site_id = $request->site_id AND t5.acmp_id = $request->ou_id 
");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.amim_code, $request->site_id)               AS column_id,
  t3.id                                                 AS Item_Id,
  t3.amim_code                                          AS Item_Code,
  if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
  LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
  LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
WHERE t3.lfcl_id = 1
      AND t1.aemp_id = $request->emp_id
      AND t6.site_id = $request->site_id 
      AND t6.acmp_id = $request->ou_id
GROUP BY t3.id, t3.amim_code, t8.dfim_disc
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
WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
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
WHERE t3.site_id='$request->site_id' 
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
WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
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
        return Array(
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

    public function CheckINSyncAllDataMergeVanSales(Request $request)
    {

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
  t6.amim_runt                                         AS R_Unit,
  t9.INV_QNTY-t9.DELV_QNTY                             AS Stock_Qty
FROM `tm_amim`t1 
INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
INNER JOIN tm_aemp AS t7 ON t3.aemp_id=t7.id
INNER JOIN dm_trip AS t8 ON t7.aemp_usnm=t8.DM_ID
INNER JOIN dm_van_trip_detail t9 ON t8.TRIP_NO=t9.TRIP_NO and t9.AMIM_ID=t6.amim_id
WHERE  t1.lfcl_id=1 AND
 t3.aemp_id = $request->emp_id 
 AND t5.site_id = $request->site_id 
 AND t5.acmp_id = $request->ou_id AND 
 t8.TRIP_NO='$request->TRIP_NO' AND
  t8.STATUS='N' AND (t9.INV_QNTY-t9.DELV_QNTY)>0
");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.amim_code, $request->site_id)               AS column_id,
  t3.id                                                 AS Item_Id,
  t3.amim_code                                          AS Item_Code,
  if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
  LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
  LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
WHERE t3.lfcl_id = 1
      AND t1.aemp_id = $request->emp_id
      AND t6.site_id = $request->site_id 
      AND t6.acmp_id = $request->ou_id
GROUP BY t3.id, t3.amim_code, t8.dfim_disc
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
WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
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
WHERE t3.site_id='$request->site_id' 
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
WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
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
        return Array(
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

    public function MasterDataNew_Product_Info(Request $request)
    {
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
        return Array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public function MasterDataNew_RouteWise_Outlet(Request $request)
    {

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

        return Array(
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public function getVanItemList(Request $request)
    {

        $data2 = array();
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

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
WHERE  t1.lfcl_id=1
AND t3.aemp_id = $request->emp_id  
AND t5.site_id = $request->site_id 
AND t5.slgp_id=$request->slgp_id");

        }

        $result_data = array(
            'success' => 1,
            'van_items' => $data2,

        );
        return $result_data;

    }

    public function getVanTripDetails(Request $request)
    {


        $data1 = array();
        $data2 = array();
        $data3 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                    SELECT t1.`id` AS trip_id,t1.`TRIP_NO` AS trip_code,t1.V_ID AS vehicle,t1.`TRIP_DATE`AS trip_date,
                    ifnull(SUM(round((t5.lodl_qnty*t5.lodl_uprc),4)),0)AS load_re_amt,
                    ifnull(SUM(round((t5.lodl_cqty*t5.lodl_uprc),4)),0)AS load_conf_amt,
                    t1.`DM_ACTIVITY` AS trip_status_id,t2.lfcl_name AS trip_status 
                    FROM `dm_trip`t1 JOIN tm_lfcl t2 ON(t1.`DM_ACTIVITY`=t2.id) 
                    LEFT JOIN tt_trip t3 ON(t1.id=t3.trip_otid)
                    LEFT JOIN tt_lodm t4 ON(t3.id=t4.trip_id)
                    LEFT JOIN tt_lodl t5 ON(t4.id=t5.lodm_id)
                    WHERE t1.`DM_ID`='$request->emp_code' AND t1.`STATUS`='N'
                    GROUP BY t1.`id`,t1.`TRIP_NO`,t1.V_ID,t1.`TRIP_DATE`,t1.`DM_ACTIVITY`,t2.lfcl_name;");

            $data2 = DB::connection($db_conn)->select("
SELECT t1.`dlrm_id`,t2.dlrm_name AS dlrm_name,t1.`acmp_id`AS ou_id 
FROM `tl_srdi`t1 JOIN tm_dlrm t2 ON(t1.`dlrm_id`=t2.id)
WHERE t1.`aemp_id`=$request->emp_id;");

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
            $data11 = (array)$data1[0];
        } else {
            $data11 = 0;
        }
        if ($data3 != null) {
            $data33 = (array)$data3[0];
        } else {
            $data33 = 0;
        }

        $result_data = array(
            'success' => 1,
            'van_trip_details' => $data11,
            'van_dpo_ou' => $data2,
            'van_site_details' => $data33,

        );
        return $result_data;

    }



    public function vanLoadDataSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            DB::connection($country->cont_conn)->beginTransaction();

            $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
            try {
                $VanLoadTrip = new VanLoadTrip();
                $VanLoadTrip->setConnection($country->cont_conn);
                $VanLoadTrip->trip_otid = $request->trip_id;
                $VanLoadTrip->aemp_tusr = $request->emp_id;
                $VanLoadTrip->trip_code = $request->emp_id.'TL'.$request->order_date;
                $VanLoadTrip->trip_date = $request->order_date;
                $VanLoadTrip->trip_vdat = $request->order_date;
                $VanLoadTrip->trip_ldat = $request->order_date;
                $VanLoadTrip->trip_cdat = $request->order_date;
                $VanLoadTrip->aemp_vusr = 0;
                $VanLoadTrip->aemp_lusr = 0;
                $VanLoadTrip->aemp_cusr = 0;
                $VanLoadTrip->dpot_id = $request->ou_id;
                $VanLoadTrip->dlrm_id = $request->depo_id;
                $VanLoadTrip->vhcl_id = 1;
                $VanLoadTrip->ttyp_id = 2;
                $VanLoadTrip->cont_id =  $request->country_id;
                $VanLoadTrip->lfcl_id = 20;
                $VanLoadTrip->attr3 = $employee->slgp_id;
                $VanLoadTrip->attr4 = $employee->site_id;
                $VanLoadTrip->aemp_iusr = $request->up_emp_id;
                $VanLoadTrip->aemp_eusr = $request->up_emp_id;
                $VanLoadTrip->save();

                $dataLines = json_decode($request->line_data);

                    $loadMaster = new LoadMaster();
                    $loadMaster->setConnection($country->cont_conn);
                    $loadMaster->lodm_code = $request->order_id;
                    $loadMaster->lodm_date = $request->order_date;
                    $loadMaster->lodm_vdat = $request->order_date;
                    $loadMaster->aemp_vusr = $request->emp_id;
                    $loadMaster->aemp_vusr = 0;
                    $loadMaster->trip_id = $VanLoadTrip->id;
                    $loadMaster->dlrm_id = $request->depo_id;
                    $loadMaster->lodt_id = 1;
                    $loadMaster->lods_id = 1;
                    $loadMaster->lfcl_id = 1;
                    $loadMaster->cont_id = $request->country_id;
                    $loadMaster->aemp_iusr = $request->up_emp_id;
                    $loadMaster->aemp_eusr = $request->up_emp_id;
                    $loadMaster->save();
                    foreach ($dataLines as $lineData) {
                        $loadLine = new LoadLine();
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

                       /* $tripSku = TripSku::on($country->cont_conn)->where(['trip_id' => $VanLoadTrip->id, 'amim_id' => $lineData->product_id])->first();
                        if ($tripSku == null) {
                            $tripSku = new TripSku();
                            $tripSku->setConnection($country->cont_conn);
                            $tripSku->trip_id = $VanLoadTrip->id;
                            $tripSku->amim_id = $lineData->product_id;
                            $tripSku->troc_iqty = $lineData->order_qty;
                            $tripSku->troc_cqty = 0;
                            $tripSku->troc_dqty = 0;
                            $tripSku->troc_lqty = 0;
                            $tripSku->lfcl_id = 1;
                            $tripSku->cont_id = $request->country_id;
                            $tripSku->aemp_iusr = $request->up_emp_id;
                            $tripSku->aemp_eusr = $request->up_emp_id;
                           // $tripSku->save();
                        }*/
                    }

                DB::connection($country->cont_conn)->commit();
              //  return array('column_id' => $request->id);
                return response()->json(array('column_id' => $request->id, 'message' =>'success'));
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
               // return response()->json(array('column_id' => 0, 'message' =>'failed'));
                return $e;
            }
        }
    }

    public function MasterDataNew_FOC(Request $request)
    {

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

        return Array(

            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public function aroundOutlet(Request $request)
    {
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

    public function GetManagers_SR(Request $request)
    {
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

    public function GetVanSalesTripSite(Request $request)
    {
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

    public function GetSRRoute(Request $request)
    {
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


    public function GetOutletSerialData(Request $request)
    {
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

    public function GetSRDPO(Request $request)
    {
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
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
WHERE t1.dptp_id = 1 AND t5.aemp_id = '$emp_id'
ORDER BY t3.id ASC
  ");

        }

        return $data1;

    }

    public function GetSRSUBDPO(Request $request)
    {
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
  INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
  WHERE t3.id = '$DPO' AND t5.aemp_id = '$emp_id' AND  t1.dptp_id = 1
  ORDER BY t3.id ASC
  ");

        }

        return $data1;

    }

    public function GetSrSalesGroup(Request $request)
    {
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

    public function GetSRTodayOutletList(Request $request)
    {
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $Ou_id = $request->ou_id;

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
Site_Balance                                              AS Site_Balance,
avail                                                     AS avail,
Site_Discount_ID                                          AS Site_Discount_ID,
VAT_TRN                                                   AS VAT_TRN,
Sub_Channel                                               AS Channel,
Sub_Channel                                               AS Sub_Channel
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
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.plmt_id=t2.plmt_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
  WHERE t1.aemp_id = $emp_id AND t1.rout_id = $route_id AND t5.acmp_id = $Ou_id
  GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
  t5.stcm_ordm,
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
   LEFT JOIN tl_ovpm t9 ON (t4.id = t9.site_id)AND t1.aemp_id=t9.aemp_id
  INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.plmt_id=t2.plmt_id
  LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
  LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
  LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id) 
  WHERE t9.aemp_id = $emp_id  AND t5.acmp_id = $Ou_id AND t9.ovpm_date=CURDATE()
   GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
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
 p.Sub_Channel
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

    public function GetSRTodayOutletListSearch(Request $request)
    {
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

    public function GetSRTodayOutletListSearchQRCode(Request $request)
    {
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

    public function updateOutlet(Request $request)
    {
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

    public function outletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("select t1.id as Category_Code,t1.otcg_name as Category_Name from tm_otcg as t1");

        }
        return $data1;

    }

    public function govDistrict(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  id        AS district_code,
  dsct_name AS district_name
FROM tm_dsct
WHERE `lfcl_id` = 1
ORDER BY dsct_name");

        }
        return $data1;

    }

    public function govThana(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  `id` as Thana_Code,
  than_name AS Thana_Name
FROM `tm_than`
WHERE `lfcl_id` = 1 AND
      `dsct_id` = '$dsct_id'
ORDER BY than_name");

        }
        return $data1;

    }

    public function govWard(Request $request)
    {
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

    public function market(Request $request)
    {
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

    public function GetOutletNameAndID(Request $request)
    {
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

    public function GetOutletWiseOrderID(Request $request)
    {
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

    public function GetOutletWiseOrderDetails(Request $request)
    {
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

    public function GetChallanWiseOrderData(Request $request)
    {
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