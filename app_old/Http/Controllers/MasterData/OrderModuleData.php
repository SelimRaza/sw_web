<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\Attendance;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
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
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

class OrderModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    /* public function saveOrder(Request $request)
     {
         DB::beginTransaction();
         try {
             $orderSequence = OrderSequence::where(['emp_id' => $request->emp_id, 'year' => date('Y')])->first();
             if ($orderSequence == null) {
                 $orderSequence = new OrderSequence();
                 $orderSequence->emp_id = $request->emp_id;
                 $orderSequence->year = date('Y');
                 $orderSequence->order_count = 0;
                 $orderSequence->return_count = 0;
                 $orderSequence->collection_count = 0;
                 $orderSequence->country_id = $request->country_id;
                 $orderSequence->status_id = 1;
                 $orderSequence->created_by = $request->up_emp_id;
                 $orderSequence->updated_by = $request->up_emp_id;
                 $orderSequence->updated_count = 0;
                 $orderSequence->save();
             }
             $employee = Employee::where(['id' => $request->emp_id])->first();
             $orderMaster = new OrderMaster();
             $orderLines = json_decode($request->line_data);
             $order_id = "O" . str_pad($employee->user_name, 8, '0', STR_PAD_LEFT) . $orderSequence->year . str_pad($orderSequence->order_count + 1, 6, '0', STR_PAD_LEFT);
             $order_amount = array_sum(array_column($orderLines, 'total_order_amount'));
             $orderMaster->order_id = $order_id;
             $orderMaster->emp_id = $request->emp_id;
             $orderMaster->company_id = $request->ou_id;
             $orderMaster->site_id = $request->site_id;
             $orderMaster->depot_id = $request->depo_id;
             $orderMaster->route_id = $request->route_id;
             $orderMaster->sales_group_id = 1;
             $orderMaster->order_type_id = 1;
             $orderMaster->order_date = $request->order_date;
             $orderMaster->order_date_time = date('Y-m-d H:i:s');
             $orderMaster->delivery_date = $request->order_date;
             $orderMaster->delivery_date_time = date('Y-m-d H:i:s');
             $orderMaster->o_lat = $request->lat;
             $orderMaster->o_lon = $request->lon;
             $orderMaster->o_distance = $request->distance;
             $orderMaster->d_lat = '';
             $orderMaster->d_lon = '';
             $orderMaster->d_distance = 0;
             $orderMaster->status_id = $request->status_id;
             $orderMaster->order_amount = $order_amount;
             $orderMaster->invoice_amount = 0;
             $orderMaster->created_by = $request->up_emp_id;
             $orderMaster->updated_by = $request->up_emp_id;
             $orderMaster->country_id = $request->country_id;
             $orderMaster->updated_count = 0;
             $orderMaster->save();
             if (($request->status_id == 14 || $request->status_id == 15 || $request->status_id == 16) && $employee->address != '') {
                 $orderSite = Site::findorfail($request->site_id);
                 $status = LifeCycleStatus::findorfail($request->status_id);
                 $email = new Auto();
                 $email->emp_id = $request->emp_id;
                 $email->to_mail = $employee->address;
                 if ($employee->email_cc != '') {
                     $email->cc_mail = $employee->email_cc . ',c7f4b922.prangroup.com@apac.teams.ms';
                 } else {
                     $email->cc_mail = 'c7f4b922.prangroup.com@apac.teams.ms';
                 }

                 $email->title = $orderSite->id . ' - ' . $orderSite->name . " LPO:" . $order_id . ' amount:' . $order_amount . " " . $status->name;
                 $email->text = ' Blocked : ' . $orderSite->id . ' - ' . $orderSite->name . ' Ord# ' . $order_id . ' amount:' . $order_amount . ' Reason: ' . $status->name;
                 $email->is_send = 3;
                 $email->save();
             }

             foreach ($orderLines as $orderLineData) {
                 $orderLine = new OrderLine();
                 $orderLine->so_id = $orderMaster->id;
                 $orderLine->sku_id = $orderLineData->product_id;
                 $orderLine->qty_order = $orderLineData->order_qty;
                 $orderLine->qty_confirm = 0;
                 $orderLine->qty_delivery = 0;
                 $orderLine->ctn_size = $orderLineData->ctn_size;
                 $orderLine->unit_price = $orderLineData->pcs_price;
                 $orderLine->is_order_line = 1;
                 $orderLine->promo_ref_id = 0;
                 $orderLine->total_order = $orderLineData->total_order_amount;
                 $orderLine->total_confirm = 0;
                 $orderLine->total_delivery = 0;
                 $orderLine->invoice_amount = 0;
                 $orderLine->country_id = $request->country_id;
                 $orderLine->created_by = $request->up_emp_id;
                 $orderLine->updated_by = $request->up_emp_id;
                 $orderLine->updated_count = 0;
                 $orderLine->save();
             }
             $orderSequence->order_count = $orderSequence->order_count + 1;
             $orderSequence->updated_by = $request->up_emp_id;
             $orderSequence->updated_count = $orderSequence->updated_count + 1;
             $orderSequence->save();
             $orderSyncLog = new OrderSyncLog();
             $orderSyncLog->local_id = $request->order_id;
             $orderSyncLog->order_id = $orderMaster->order_id;
             $orderSyncLog->oid = $orderMaster->id;
             $orderSyncLog->country_id = $request->country_id;
             $orderSyncLog->created_by = $request->up_emp_id;
             $orderSyncLog->updated_by = $request->up_emp_id;
             $orderSyncLog->updated_count = 0;
             $orderSyncLog->save();
             DB::commit();
             return array('column_id' => $request->id);
         } catch (\Exception $e) {
             DB::rollback();
             return $e;
             //throw $e;
         }
     }*/
    public function orderSave(Request $request)
    {
        $country = Country::findorfail($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                $productive = 0;
                $line_id = "";
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
                        $orderMaster->site_id = $request->Outlet_ID;
                        $orderMaster->rout_id = $request->Route_ID;
                        $orderMaster->odtp_id = 1;
                        $orderMaster->ordm_date = $request->Date;
                        $orderMaster->ordm_time = date('Y-m-d H:i:s');
                        $orderMaster->ordm_drdt = $request->Delivery_Date;
                        $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                        $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                        $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                        $orderMaster->ordm_dtne = $request->Outlet_Distance;
                        $orderMaster->ordm_amnt = $order_amount;
                        $orderMaster->ordm_icnt = sizeof($orderLines);
                        $orderMaster->plmt_id = $request->Item_Price_List;
                        $orderMaster->cont_id = $request->country_id;
                        $orderMaster->lfcl_id = 1;
                        $orderMaster->aemp_iusr = $request->up_emp_id;
                        $orderMaster->aemp_eusr = $request->up_emp_id;
                        $orderMaster->save();
                        foreach ($orderLines as $orderLineData) {
                            $orderLine = new OrderLine();
                            $orderLine->setConnection($db_conn);
                            $orderLine->ordm_id = $orderMaster->id;
                            $orderLine->ordm_ornm = $order_id;
                            $orderLine->amim_id = $orderLineData->P_ID;
                            $orderLine->ordd_qnty = $orderLineData->P_Qty;
                            $orderLine->ordd_cqty = 0;
                            $orderLine->ordd_dqty = 0;
                            $orderLine->ordd_opds = $orderLineData->P_Discount;
                            $orderLine->ordd_cpds = 0;
                            $orderLine->ordd_dpds = 0;
                            $orderLine->ordd_duft = $orderLineData->Item_Factor;
                            $orderLine->ordd_uprc = $orderLineData->Rate;
                            $orderLine->ordd_runt = $orderLineData->R_Unit;
                            $orderLine->ordd_dunt = $orderLineData->D_Unit;
                            $orderLine->prom_id = 0;
                            $orderLine->ordd_oamt = $orderLineData->P_T_Price;
                            $orderLine->ordd_ocat = 0;
                            $orderLine->ordd_odat = 0;
                            $orderLine->ordd_smpl = 0;
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
                        $orderSyncLog->ordm_ornm = $orderMaster->ordm_ornm;
                        $orderSyncLog->ordm_id = $orderMaster->id;
                        $orderSyncLog->lfcl_id = 1;
                        $orderSyncLog->cont_id = $request->country_id;
                        $orderSyncLog->aemp_iusr = $request->up_emp_id;
                        $orderSyncLog->aemp_eusr = $request->up_emp_id;
                        $orderSyncLog->save();
                        $productive = 1;
                        $line_id = $request->ID;

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

                }

                $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $request->Outlet_ID, 'ssvh_date' => $request->Date, 'aemp_id' => $request->SR_ID])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($db_conn);
                    $siteVisit->ssvh_date = $request->Date;
                    $siteVisit->aemp_id = $request->SR_ID;
                    $siteVisit->site_id = $request->Outlet_ID;
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                } else {
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($db_conn)->commit();
                return array('column_id' => $line_id);
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }

        }

    }

    public function censusOutletImport(Request $request)
    {
        $country = Country::findorfail($request->country_id);
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
            }
        }
        return $result_data;
    }

    public function attendanceSave(Request $request)
    {

        $country = Country::findorfail($request->country_id);
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

    public function saveReturnOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderSequence = OrderSequence::where(['emp_id' => $request->emp_id, 'year' => date('Y')])->first();
            if ($orderSequence == null) {
                $orderSequence = new OrderSequence();
                $orderSequence->emp_id = $request->emp_id;
                $orderSequence->year = date('Y');
                $orderSequence->order_count = 0;
                $orderSequence->return_count = 0;
                $orderSequence->collection_count = 0;
                $orderSequence->country_id = $request->country_id;
                $orderSequence->status_id = 1;
                $orderSequence->created_by = $request->up_emp_id;
                $orderSequence->updated_by = $request->up_emp_id;
                $orderSequence->updated_count = 0;
                $orderSequence->save();
            }
            $employee = Employee::where(['id' => $request->emp_id])->first();
            $orderMaster = new OrderMaster();
            $orderLines = json_decode($request->line_data);
            $orderMaster->order_id = "R" . str_pad($employee->user_name, 8, '0', STR_PAD_LEFT) . $orderSequence->year . str_pad($orderSequence->return_count + 1, 6, '0', STR_PAD_LEFT);
            $orderMaster->emp_id = $request->emp_id;
            $orderMaster->company_id = $request->ou_id;
            $orderMaster->site_id = $request->site_id;
            $orderMaster->depot_id = $request->depo_id;
            $orderMaster->route_id = $request->route_id;
            $orderMaster->sales_group_id = 1;
            $orderMaster->order_type_id = 2;
            $orderMaster->order_date = $request->date;
            $orderMaster->order_date_time = date('Y-m-d H:i:s');
            $orderMaster->delivery_date = $request->date;
            $orderMaster->delivery_date_time = date('Y-m-d H:i:s');
            $orderMaster->o_lat = $request->lat;
            $orderMaster->o_lon = $request->lon;
            $orderMaster->o_distance = $request->distance;
            $orderMaster->d_lat = '';
            $orderMaster->d_lon = '';
            $orderMaster->d_distance = 0;
            $orderMaster->order_amount = array_sum(array_column($orderLines, 'total_amount'));
            $orderMaster->invoice_amount = 0;
            $orderMaster->status_id = $request->status_id;
            $orderMaster->created_by = $request->up_emp_id;
            $orderMaster->updated_by = $request->up_emp_id;
            $orderMaster->country_id = $request->country_id;
            $orderMaster->updated_count = 0;
            $orderMaster->save();
            foreach ($orderLines as $orderLineData) {
                $orderLine = new OrderLine();
                $orderLine->so_id = $orderMaster->id;
                $orderLine->sku_id = $orderLineData->product_id;
                $orderLine->qty_order = $orderLineData->quantity_returned;
                $orderLine->qty_confirm = 0;
                $orderLine->qty_delivery = 0;
                $orderLine->ctn_size = $orderLineData->ctn_size;
                $orderLine->unit_price = $orderLineData->pcs_price;
                $orderLine->is_order_line = 1;
                $orderLine->promo_ref_id = 0;
                $orderLine->total_order = $orderLineData->total_amount;
                $orderLine->total_confirm = 0;
                $orderLine->total_delivery = 0;
                $orderLine->invoice_amount = 0;
                $orderLine->return_reason_id = $orderLineData->reason_id;
                $orderLine->country_id = $request->country_id;
                $orderLine->created_by = $request->up_emp_id;
                $orderLine->updated_by = $request->up_emp_id;
                $orderLine->updated_count = 0;
                $orderLine->save();
            }
            $orderSequence->return_count = $orderSequence->return_count + 1;
            $orderSequence->updated_by = $request->up_emp_id;
            $orderSequence->updated_count = $orderSequence->updated_count + 1;
            $orderSequence->save();
            $orderSyncLog = new OrderSyncLog();
            $orderSyncLog->local_id = $request->return_id;
            $orderSyncLog->order_id = $orderMaster->order_id;
            $orderSyncLog->oid = $orderMaster->id;
            $orderSyncLog->country_id = $request->country_id;
            $orderSyncLog->created_by = $request->up_emp_id;
            $orderSyncLog->updated_by = $request->up_emp_id;
            $orderSyncLog->updated_count = 0;
            $orderSyncLog->save();
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return array('column_id1' => $e);
            //throw $e;
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
        $country = Country::findorfail($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS token,
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
WHERE t1.aemp_id = $request->emp_id;");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS column_id,
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS token,
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
  concat(t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn) AS column_id,
  concat(t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn) AS token,
  t1.plmt_id                                                                          AS Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  t2.pldt_tppr                                                                        AS Item_Price,
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
  concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS token,
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
  t5.prdt_sitm AS buy_item_code,
  t5.PRDT_MBQT AS max_buy_qty,
  t5.PRDT_MNBT AS min_buy_qyt,
  t5.PRDT_FITM AS free_item_code,
  t5.PRDT_FIQT AS free_item_qty,
  t5.PRDT_FIPR AS free_item_price,
  t5.PRDT_DISC AS discount_percente,
  t4.prom_edat AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id");
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

    public function aroundOutlet(Request $request)
    {
        $data1 = array();
        $country = Country::findorfail($request->country_id);
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

        }


        return $data1;

    }

    public function preSalesRouteData(Request $request)
    {

        $data3 = DB::select("SELECT
  concat(t1.site_id,
         t1.route_id,
         t1.outlet_id,
         !isnull(t1.pay_mode),
         t1.visited,
         t1.credit_limit,
         t1.due_amount,
         t1.pre_order_amount,
         t1.overdue,
         t1.must_sell_id,
         t1.date,
         !isnull(t1.price_list_id),
         sum(t1.payment_type_id),
         t1.is_verified,
         t1.is_productive) AS column_id,
  concat(t1.site_id,
         t1.route_id,
         t1.outlet_id,
         !isnull(t1.pay_mode),
         t1.visited,
         t1.credit_limit,
         t1.due_amount,
         t1.pre_order_amount,
         t1.overdue,
         t1.must_sell_id,
         t1.date,
         !isnull(t1.price_list_id),
         t1.is_verified,
         t1.is_productive)AS token,
  t1.site_id,
  t1.route_id,
  t1.outlet_id,
  t1.site_name,
  t1.owner_name,
  t1.owner_mobile,
  t1.address,
  t1.pay_mode,
  t1.visited,
  t1.house_no,
  t1.site_trn,
  t1.credit_limit,
  t1.due_amount,
  t1.pre_order_amount,
  t1.overdue,
  t1.must_sell_id,
  t1.date,
  t1.lat,
  t1.lon,
  t1.price_list_id,
  t1.is_verified,
  t1.is_productive
FROM (
       SELECT

         t3.site_id                                        AS site_id,
         t1.id                                             AS route_id,
         t4.outlet_id                                      AS outlet_id,
         concat(t4.name, '(', t4.ln_name, ')')             AS site_name,
         concat(t4.owner_name, '(', t4.ln_owner_name, ')') AS owner_name,
         t4.mobile_1                                       AS owner_mobile,
         concat(t4.address, '(', t4.ln_address, ')')       AS address,
         t7.name                                           AS pay_mode,
         !isnull(t5.site_id)                               AS visited,
         t4.house_no                                       AS house_no,
         t4.vat_trn                                        AS site_trn,
         0                                                 AS credit_limit,
         0                                                 AS due_amount,
         0                                                 AS pre_order_amount,
         0                                                 AS overdue,
         0                                                 AS must_sell_id,
         curdate()                                         AS date,
         t4.lat,
         t4.lon,
         t6.price_list_id                                  AS price_list_id,
         t6.payment_type_id,
         t4.is_verified,
         if(t5.is_productive > 0, 1, 0)                    AS is_productive
       FROM tbld_pjp AS t1
         INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
         INNER JOIN tbld_route_site_mapping AS t3 ON t2.id = t3.route_id
         INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
         LEFT JOIN tblt_site_visited AS t5 ON t3.site_id = t5.site_id AND t5.date = curdate()
         LEFT JOIN tbld_site_company_mapping AS t6 ON t3.site_id = t6.site_id AND t6.company_id = $request->company_id
         LEFT JOIN tbld_outlet_payment_type AS t7 ON t6.payment_type_id = t7.id
       WHERE t1.emp_id = $request->emp_id AND t1.day = DAYNAME(curdate()) AND t4.status_id = 1
       UNION ALL
       SELECT

         t1.site_id                                        AS site_id,
         t2.id                                             AS route_id,
         t3.outlet_id                                      AS outlet_id,
         concat(t3.name, '(', t3.ln_name, ')')             AS site_name,
         concat(t3.owner_name, '(', t3.ln_owner_name, ')') AS owner_name,
         t3.mobile_1                                       AS owner_mobile,
         concat(t3.address, '(', t3.ln_address, ')')       AS address,
         t6.name                                           AS pay_mode,
         !isnull(t4.site_id)                               AS visited,
         t3.house_no                                       AS house_no,
         t3.vat_trn                                        AS site_trn,
         0                                                 AS credit_limit,
         0                                                 AS due_amount,
         0                                                 AS pre_order_amount,
         0                                                 AS overdue,
         0                                                 AS must_sell_id,
         curdate()                                         AS date,
         t3.lat,
         t3.lon,
         t5.price_list_id                                  AS price_list_id,
         t5.payment_type_id,
         t3.is_verified,
         if(t4.is_productive > 0, 1, 0)                    AS is_productive
       FROM tblt_site_visit_permission AS t1
         INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
         INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
         LEFT JOIN tblt_site_visited AS t4 ON t3.id = t4.site_id AND t4.date = curdate()
         LEFT JOIN tbld_site_company_mapping AS t5 ON t1.site_id = t5.site_id AND t5.company_id = $request->company_id
         LEFT JOIN tbld_outlet_payment_type AS t6 ON t5.payment_type_id = t6.id
       WHERE t1.emp_id = $request->emp_id AND t3.status_id = 1 AND t1.date = curdate()
     ) AS t1
GROUP BY t1.site_id,
  t1.route_id,
  t1.outlet_id,
  t1.site_name,
  t1.owner_name,
  t1.owner_mobile,
  t1.address,
  t1.pay_mode,
  t1.visited,
  t1.house_no,
  t1.site_trn,
  t1.credit_limit,
  t1.due_amount,
  t1.pre_order_amount,
  t1.overdue,
  t1.must_sell_id,
  t1.date,
  t1.lat,
  t1.lon,
  t1.price_list_id,
  t1.is_verified,
  t1.is_productive
");
        return Array(
            "tblt_pre_route_site" => array("data" => $data3, "action" => $request->country_id),
        );
    }

}