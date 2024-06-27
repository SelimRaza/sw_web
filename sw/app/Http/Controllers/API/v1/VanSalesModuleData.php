<?php

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\ReturnLine;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SequenceMappingReturn;
use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\LoadLine;
use App\BusinessObject\LoadMaster;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGRV;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Employee;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VanSalesModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function vanOrderSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

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
                    $order_amount = array_sum(array_column($orderLines, 'total_delivered_amount'));
                    $orderMaster->ordm_ornm = $order_id;
                    $orderMaster->aemp_id = $request->emp_id;
                    $orderMaster->acmp_id = $request->ou_id;
                    $orderMaster->slgp_id = $request->group_id;
                    $orderMaster->dlrm_id = $request->depo_id;
                    $orderMaster->site_id = $request->site_id;
                    $orderMaster->rout_id = $request->route_id;
                    $orderMaster->odtp_id = 2;
                    $orderMaster->ordm_date = $request->order_date;
                    $orderMaster->ordm_time = date('Y-m-d H:i:s');
                    $orderMaster->ordm_drdt = $request->order_date;
                    $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                    $orderMaster->geo_lat = $request->lat;
                    $orderMaster->geo_lon = $request->lon;
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

                    $deliverySequence = SequenceNumber::on($country->cont_conn)->where(['acmp_id' => $request->ou_id, 'vatn_year' => date('Y')])->first();
                    if ($deliverySequence == null) {
                        $deliverySequence = new SequenceNumber();
                        $deliverySequence->setConnection($country->cont_conn);
                        $deliverySequence->acmp_id = $request->ou_id;
                        $deliverySequence->vatn_year = date('Y');
                        $deliverySequence->vatn_icnt = 0;
                        $deliverySequence->vatn_rcnt = 0;
                        $deliverySequence->cont_id = $request->country_id;
                        $deliverySequence->lfcl_id = 1;
                        $deliverySequence->aemp_iusr = $request->up_emp_id;
                        $deliverySequence->aemp_eusr = $request->up_emp_id;
                        $deliverySequence->save();
                    }
                    $deliverySequence->vatn_icnt = $deliverySequence->vatn_icnt + 1;
                    $deliverySequence->aemp_eusr = $request->up_emp_id;
                    $deliverySequence->save();
                    $deliverySequenceMapping = new SequenceMappingInvoice();
                    $deliverySequenceMapping->setConnection($country->cont_conn);
                    $deliverySequenceMapping->acmp_id = $request->ou_id;
                    $deliverySequenceMapping->vatn_year = date('Y');
                    $deliverySequenceMapping->ordm_id = $orderMaster->id;
                    $deliverySequenceMapping->vatn_icnt = $deliverySequence->vatn_icnt;
                    $deliverySequenceMapping->lfcl_id = 1;
                    $deliverySequenceMapping->cont_id = $request->country_id;
                    $deliverySequenceMapping->aemp_iusr = $request->up_emp_id;
                    $deliverySequenceMapping->aemp_eusr = $request->up_emp_id;
                    $deliverySequenceMapping->save();
                    foreach ($orderLines as $orderLineData) {
                        $orderLine = new OrderLine();
                        $orderLine->setConnection($country->cont_conn);
                        $orderLine->ordm_id = $orderMaster->id;
                        $orderLine->ordm_ornm = $order_id;
                        $orderLine->amim_id = $orderLineData->product_id;
                        $orderLine->ordd_inty = $orderLineData->delivary_qty;
                        $orderLine->ordd_qnty = $orderLineData->delivary_qty;
                        $orderLine->ordd_cqty = $orderLineData->delivary_qty;
                        $orderLine->ordd_dqty = $orderLineData->delivary_qty;
                        $orderLine->ordd_rqty = 0;
                        $orderLine->ordd_opds = $orderLineData->promo_discount_order;
                        $orderLine->ordd_cpds = $orderLineData->promo_discount_confirm;
                        $orderLine->ordd_dpds = $orderLineData->promo_discount_delivery;
                        $orderLine->ordd_spdi = $orderLineData->order_discount;
                        $orderLine->ordd_spdo = $orderLineData->order_discount;
                        $orderLine->ordd_spdc = $orderLineData->confirm_discount;
                        $orderLine->ordd_spdd = $orderLineData->delivery_discount;
                        $orderLine->ordd_dfdo = $orderLineData->def_discount_order;
                        $orderLine->ordd_dfdc = $orderLineData->def_discount_confirm;
                        $orderLine->ordd_dfdd = $orderLineData->def_discount_delivery;
                        $orderLine->ordd_duft = $orderLineData->ctn_size;
                        $orderLine->ordd_uprc = $orderLineData->pcs_price;
                        $orderLine->ordd_runt = 1;
                        $orderLine->ordd_dunt = 1;
                        $orderLine->ordd_smpl = $orderLineData->is_order_line == '1' ? 0 : 1;
                        $orderLine->prom_id = $orderLineData->promo_ref == '' ? 0 : $orderLineData->promo_ref;
                        $orderLine->ordd_oamt = $orderLineData->total_delivered_amount;
                        $orderLine->ordd_ocat = $orderLineData->total_confirmed_amount;
                        $orderLine->ordd_odat = $orderLineData->total_delivered_amount;
                        $orderLine->ordd_excs = $orderLineData->gst;
                        $orderLine->ordd_ovat = $orderLineData->vat;
                        $orderLine->ordd_tdis = $orderLineData->total_discount;
                        $orderLine->ordd_texc = $orderLineData->total_gst;
                        $orderLine->ordd_tvat = $orderLineData->total_vat;
                        $orderLine->ordd_amnt = $orderLineData->net_amount;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $request->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;
                        $orderLine->save();
                        $tripSku = TripSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $orderLineData->product_id])->first();
                        $tripSku->troc_dqty = $tripSku->troc_dqty + $orderLineData->delivary_qty;
                        $tripSku->aemp_eusr = $request->up_emp_id;
                        $tripSku->save();
                    }
                    $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $tripOrder = new TripOrder();
                    $tripOrder->setConnection($country->cont_conn);
                    $tripOrder->trip_id = $request->trip_id;
                    $tripOrder->ordm_id = $orderMaster->id;
                    $tripOrder->lfcl_id = 11;
                    $tripOrder->ondr_id = 0;
                    $tripOrder->cont_id = $request->country_id;
                    $tripOrder->aemp_iusr = $request->up_emp_id;
                    $tripOrder->aemp_eusr = $request->up_emp_id;
                    $tripOrder->save();
                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->order_id;
                    $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                    $orderSyncLog->oslg_orid = $orderMaster->id;
                    $orderSyncLog->oslg_type = 'van';
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
                    DB::connection($country->cont_conn)->table('tt_invc')->where('invc_lcod', $request->order_id)->update(['invc_code' => $orderMaster->ordm_ornm]);
                    DB::connection($country->cont_conn)->table('tt_clim')->where('invc_lcod', $request->order_id)->update(['invc_code' => $orderMaster->ordm_ornm]);
                    DB::connection($country->cont_conn)->commit();
                    return array('column_id' => $request->id);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                    //throw $e;
                }
            }

        }
    }

    public function vanGRVSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {

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
                    $order_amount = array_sum(array_column($orderLines, 'total_amount_delivered'));
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
                    $returnMaster->rtan_pono = '';
                    $returnMaster->rtan_podt = $request->date;;;
                    $returnMaster->rtan_note = '';
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = $request->status_id;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    $deliverySequence = SequenceNumber::on($country->cont_conn)->where(['acmp_id' => $request->ou_id, 'vatn_year' => date('Y')])->first();
                    if ($deliverySequence == null) {
                        $deliverySequence = new SequenceNumber();
                        $deliverySequence->setConnection($country->cont_conn);
                        $deliverySequence->acmp_id = $request->ou_id;
                        $deliverySequence->vatn_year = date('Y');
                        $deliverySequence->vatn_icnt = 0;
                        $deliverySequence->vatn_rcnt = 0;
                        $deliverySequence->cont_id = $request->country_id;
                        $deliverySequence->lfcl_id = 1;
                        $deliverySequence->aemp_iusr = $request->up_emp_id;
                        $deliverySequence->aemp_eusr = $request->up_emp_id;
                        $deliverySequence->save();
                    }
                    $deliverySequence->vatn_rcnt = $deliverySequence->vatn_rcnt + 1;
                    $deliverySequence->aemp_eusr = $request->up_emp_id;
                    $deliverySequence->save();
                    $deliverySequenceMapping = new SequenceMappingReturn();
                    $deliverySequenceMapping->setConnection($country->cont_conn);
                    $deliverySequenceMapping->acmp_id = $request->ou_id;
                    $deliverySequenceMapping->vatn_year = date('Y');
                    $deliverySequenceMapping->rtan_id = $returnMaster->id;
                    $deliverySequenceMapping->vatn_rcnt = $deliverySequence->vatn_rcnt;
                    $deliverySequenceMapping->lfcl_id = 1;
                    $deliverySequenceMapping->cont_id = $request->country_id;
                    $deliverySequenceMapping->aemp_iusr = $request->up_emp_id;
                    $deliverySequenceMapping->aemp_eusr = $request->up_emp_id;
                    $deliverySequenceMapping->save();
                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_delivered;
                        $returnLine->rtdd_dqty = 0;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount_delivered;
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
                        $tripGRVSku = TripGrvSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $orderLineData->product_id])->first();
                        if ($tripGRVSku == null) {
                            $tripGRVSku = new TripGrvSku();
                            $tripGRVSku->setConnection($country->cont_conn);
                            $tripGRVSku->trip_id = $request->trip_id;
                            $tripGRVSku->amim_id = $orderLineData->product_id;
                            $tripGRVSku->trgc_iqty = 0;
                            $tripGRVSku->trgc_cqty = $orderLineData->quantity_delivered;
                            $tripGRVSku->trgc_dqty = 0;
                            $tripGRVSku->trgc_bqty = 0;
                            $tripGRVSku->trgc_gqty = 0;
                            $tripGRVSku->lfcl_id = 1;
                            $tripGRVSku->cont_id = $request->country_id;
                            $tripGRVSku->aemp_iusr = $request->up_emp_id;
                            $tripGRVSku->aemp_eusr = $request->up_emp_id;
                            $tripGRVSku->save();

                        } else {
                            $tripGRVSku->trgc_cqty = $tripGRVSku->trgc_cqty + $orderLineData->quantity_delivered;
                            $tripGRVSku->aemp_eusr = $request->up_emp_id;
                            $tripGRVSku->save();
                        }


                    }
                    $tripGrvOrder = new TripGRV();
                    $tripGrvOrder->setConnection($country->cont_conn);
                    $tripGrvOrder->trip_id = $request->trip_id;
                    $tripGrvOrder->rtan_id = $returnMaster->id;
                    $tripGrvOrder->lfcl_id = 11;
                    $tripGrvOrder->ondr_id = 0;
                    $tripGrvOrder->cont_id = $request->country_id;
                    $tripGrvOrder->aemp_iusr = $request->up_emp_id;
                    $tripGrvOrder->aemp_eusr = $request->up_emp_id;
                    $tripGrvOrder->save();
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
                    DB::connection($country->cont_conn)->table('tt_invc')->where('invc_lcod', $request->return_id)->update(['invc_code' => $returnMaster->rtan_rtnm]);
                    DB::connection($country->cont_conn)->table('tt_clim')->where('invc_lcod', $request->return_id)->update(['invc_code' => $returnMaster->rtan_rtnm]);
                    DB::connection($country->cont_conn)->commit();
                    return array('column_id' => $request->id);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                    //throw $e;
                }
            }


        }
    }

    public function vanLoadSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $dataLines = json_decode($request->line_data);
                if ($request->status_id == 1) {
                    $loadMaster = new LoadMaster();
                    $loadMaster->setConnection($country->cont_conn);
                    $loadMaster->lodm_code = $request->order_id;
                    $loadMaster->lodm_date = $request->order_date;
                    $loadMaster->lodm_vdat = $request->order_date;
                    $loadMaster->aemp_vusr = $request->emp_id;
                    $loadMaster->aemp_vusr = 0;
                    $loadMaster->trip_id = $request->trip_id;
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
                    }
                } elseif ($request->status_id == 11) {
                    $loadMaster = LoadMaster::on($country->cont_conn)->findorfail($request->oid);
                    $loadMaster->lfcl_id = 11;
                    $loadMaster->aemp_eusr = $request->up_emp_id;
                    $loadMaster->save();
                    foreach ($dataLines as $lineData) {
                        $tripSku = TripSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $lineData->product_id])->first();
                        if ($tripSku == null) {
                            $tripSku = new TripSku();
                            $tripSku->setConnection($country->cont_conn);
                            $tripSku->trip_id = $request->trip_id;
                            $tripSku->amim_id = $lineData->product_id;
                            $tripSku->troc_iqty = 0;
                            $tripSku->troc_cqty = $lineData->delivary_qty;
                            $tripSku->troc_dqty = 0;
                            $tripSku->troc_lqty = 0;
                            $tripSku->lfcl_id = 1;
                            $tripSku->cont_id = $request->country_id;
                            $tripSku->aemp_iusr = $request->up_emp_id;
                            $tripSku->aemp_eusr = $request->up_emp_id;
                            $tripSku->save();
                        } else {
                            $tripSku->troc_cqty = $tripSku->troc_cqty + $lineData->delivary_qty;
                            $tripSku->aemp_eusr = $request->up_emp_id;
                            $tripSku->save();
                        }
                    }
                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }


    public function vanStockMoveSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $dataLines = json_decode($request->line_data);
                $lodt_id = 0;
                $lods_id = 0;
                if ($request->status_id == 23) {
                    $loadMaster = new LoadMaster();
                    $loadMaster->setConnection($country->cont_conn);
                    $loadMaster->lodm_code = $request->return_id;
                    $loadMaster->lodm_date = $request->date;
                    $loadMaster->lodm_vdat = $request->date;
                    $loadMaster->aemp_vusr = $request->emp_id;
                    $loadMaster->aemp_vusr = 0;
                    $loadMaster->trip_id = $request->trip_id;
                    $loadMaster->dlrm_id = $request->depo_id;
                    $loadMaster->lodt_id = $request->return_type;
                    $loadMaster->lods_id = 1;
                    $loadMaster->lfcl_id = 23;
                    $loadMaster->cont_id = $request->country_id;
                    $loadMaster->aemp_iusr = $request->up_emp_id;
                    $loadMaster->aemp_eusr = $request->up_emp_id;
                    $loadMaster->save();
                    foreach ($dataLines as $lineData) {
                        $loadLine = new LoadLine();
                        $loadLine->setConnection($country->cont_conn);
                        $loadLine->lodm_id = $loadMaster->id;
                        $loadLine->amim_id = $lineData->product_id;
                        $loadLine->lodl_qnty = $lineData->quantity_delivered;
                        $loadLine->lodl_cqty = 0;
                        $loadLine->lodl_dqty = 0;
                        $loadLine->lodl_uprc = $lineData->unit_price;
                        $loadLine->lfcl_id = 1;
                        $loadLine->cont_id = $request->country_id;
                        $loadLine->aemp_iusr = $request->up_emp_id;
                        $loadLine->aemp_eusr = $request->up_emp_id;
                        $loadLine->save();
                    }
                } elseif ($request->status_id == 11) {
                    $loadMaster = new LoadMaster();
                    $loadMaster->setConnection($country->cont_conn);
                    $loadMaster->lodm_code = $request->return_id;
                    $loadMaster->lodm_date = $request->date;
                    $loadMaster->lodm_vdat = $request->date;
                    $loadMaster->aemp_vusr = $request->emp_id;
                    $loadMaster->aemp_vusr = 0;
                    $loadMaster->trip_id = $request->trip_id;
                    $loadMaster->dlrm_id = $request->depo_id;
                    $loadMaster->lodt_id = 4;
                    $loadMaster->lods_id = 1;
                    $loadMaster->lfcl_id = 11;
                    $loadMaster->cont_id = $request->country_id;
                    $loadMaster->aemp_iusr = $request->up_emp_id;
                    $loadMaster->aemp_eusr = $request->up_emp_id;
                    $loadMaster->save();
                    foreach ($dataLines as $lineData) {
                        $loadLine = new LoadLine();
                        $loadLine->setConnection($country->cont_conn);
                        $loadLine->lodm_id = $loadMaster->id;
                        $loadLine->amim_id = $lineData->product_id;
                        $loadLine->lodl_qnty = $lineData->quantity_delivered;
                        $loadLine->lodl_cqty = $lineData->quantity_delivered;
                        $loadLine->lodl_dqty = $lineData->quantity_delivered;
                        $loadLine->lodl_uprc = $lineData->unit_price;
                        $loadLine->lfcl_id = 1;
                        $loadLine->cont_id = $request->country_id;
                        $loadLine->aemp_iusr = $request->up_emp_id;
                        $loadLine->aemp_eusr = $request->up_emp_id;
                        $loadLine->save();

                        $tripGRVSku = TripGrvSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $lineData->product_id])->first();
                        if ($tripGRVSku == null) {
                            $tripGRVSku = new TripGrvSku();
                            $tripGRVSku->setConnection($country->cont_conn);
                            $tripGRVSku->trip_id = $request->trip_id;
                            $tripGRVSku->amim_id = $lineData->product_id;
                            $tripGRVSku->trgc_iqty = 0;
                            $tripGRVSku->trgc_cqty = 0;
                            $tripGRVSku->trgc_dqty = $lineData->quantity_delivered;
                            $tripGRVSku->trgc_bqty = 0;
                            $tripGRVSku->trgc_gqty = 0;
                            $tripGRVSku->lfcl_id = 1;
                            $tripGRVSku->cont_id = $request->country_id;
                            $tripGRVSku->aemp_iusr = $request->up_emp_id;
                            $tripGRVSku->aemp_eusr = $request->up_emp_id;
                            $tripGRVSku->save();
                        } else {
                            $tripGRVSku->trgc_dqty = $tripGRVSku->trgc_dqty + $lineData->quantity_delivered;
                            $tripGRVSku->aemp_eusr = $request->up_emp_id;
                            $tripGRVSku->save();
                        }
                        $tripSku = TripSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $lineData->product_id])->first();
                        if ($tripSku == null) {
                            $tripSku = new TripSku();
                            $tripSku->setConnection($country->cont_conn);
                            $tripSku->trip_id = $request->trip_id;
                            $tripSku->amim_id = $lineData->product_id;
                            $tripSku->troc_iqty = 0;
                            $tripSku->troc_cqty = $lineData->quantity_delivered;
                            $tripSku->troc_dqty = 0;
                            $tripSku->troc_lqty = 0;
                            $tripSku->lfcl_id = 1;
                            $tripSku->cont_id = $request->country_id;
                            $tripSku->aemp_iusr = $request->up_emp_id;
                            $tripSku->aemp_eusr = $request->up_emp_id;
                            $tripSku->save();
                        } else {
                            $tripSku->troc_cqty = $tripSku->troc_cqty + $lineData->quantity_delivered;
                            $tripSku->aemp_eusr = $request->up_emp_id;
                            $tripSku->save();
                        }
                    }


                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function dmTripStatusChange(Request $request)
    {

        $trip = Trip::where(['id' => $request->trip_id])->whereIn('status_id', [6, 7, 8])->whereNotIn('status_id', [1, 2, 11])->first();
        if ($trip != null) {
            $trip->status_id = $request->status_id;
            $trip->save();
        }
        return array('column_id' => $request->id);
    }

    public function vanSalesData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.lfcl_id, t1.dlrm_id,t8.vhcl_name) AS column_id,
  t1.id                                 AS trip_id,
  t2.aemp_name                          AS driver_name,
  t1.trip_date                          AS date,
  t1.id                                 AS trip_code,
  t8.vhcl_name                          AS vehicle_name,
  t1.lfcl_id                            AS status_id,
  t1.dlrm_id                            AS depot_id,
  t7.acmp_id                            AS company_id
FROM tt_trip AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_tusr = t2.id
  INNER JOIN tm_lfcl AS t6 ON t1.lfcl_id = t6.id
  INNER JOIN tm_dlrm AS t7 ON t1.dlrm_id = t7.id
  INNER JOIN tm_vhcl AS t8 ON t1.vhcl_id = t8.id
WHERE t1.lfcl_id = 20 AND t1.aemp_tusr = $request->emp_id AND t1.ttyp_id = 2");

            $data2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t3.acmp_id, t1.dlrm_id, t8.amim_pexc, t8.amim_pvat, t5.amim_id, t8.amim_name, t5.pldt_tpgp, t9.issc_name,
         t5.plmt_id, t5.amim_duft) AS column_id,
  t3.acmp_id                       AS ou_id,
  t1.dlrm_id                       AS depot_id,
  t8.amim_pexc                     AS gst,
  t8.amim_pvat                     AS vat,
  t5.amim_id                       AS sku_id,
  t8.amim_name                     AS sku_name,
  t5.pldt_tppr * t5.amim_duft      AS ctn_price,
  t5.pldt_tppr                     AS unit_price,
  t5.pldt_tpgp * t5.amim_duft      AS grv_price,
  t5.pldt_tpgp                     AS unit_grv_price,
  t5.amim_duft                     AS ctn_size,
  t9.id                            AS sub_category_id,
  t9.issc_name                     AS sub_category,
  t5.plmt_id                       AS price_id,
  t5.amim_duft                     AS default_qty,
  t5.amim_duft * 5                 AS max_qty,
  t5.amim_duft                     AS min_qty,
  1                                AS loadable,
  t6.slgp_id                       AS group_id
FROM tt_trip AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_tusr = t2.id
  LEFT JOIN tm_dlrm AS t3 ON t1.dlrm_id = t3.id
  LEFT JOIN tl_stcm AS t4 ON t2.site_id = t4.site_id AND t3.acmp_id = t4.acmp_id
  LEFT JOIN tm_pldt AS t5 ON t4.plmt_id = t5.plmt_id
  LEFT JOIN tl_sgsm AS t6 ON t6.aemp_id = t2.id
  LEFT JOIN tl_sgit AS t7 ON t6.slgp_id = t7.slgp_id AND t5.amim_id = t7.amim_id
  LEFT JOIN tm_amim AS t8 ON t8.id = t5.amim_id
  LEFT JOIN tm_issc AS t9 ON t6.slgp_id = t9.slgp_id AND t9.id = t7.issc_id
WHERE t8.lfcl_id = 1 AND t1.aemp_tusr = $request->emp_id AND t1.lfcl_id = 20");

            $data3 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.aemp_tusr, t2.site_id, t2.aemp_crdt, t4.stcm_limt, t4.stcm_ordm, t4.stcm_duea) AS column_id,
  t1.aemp_tusr                                                                             AS emp_id,
  t2.site_id                                                                               AS site_id,
  t4.stcm_limt                                                                             AS credit_limit,
  t4.stcm_ordm                                                                             AS order_amount,
  t2.aemp_crdt                                                                             AS van_credit_limit,
  t4.stcm_duea                                                                             AS due_amount
FROM tt_trip AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_tusr = t2.id
  LEFT JOIN tm_dlrm AS t3 ON t1.dlrm_id = t3.id
  LEFT JOIN tl_stcm AS t4 ON t2.site_id = t4.site_id AND t3.acmp_id = t4.acmp_id
WHERE t1.ttyp_id = 2 AND t1.aemp_tusr = $request->emp_id AND t1.lfcl_id = 20");

            $data4 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.rout_name, t2.id, t1.rpln_day) AS column_id,
  t2.rout_name                             AS route_name,
  t2.rout_code                             AS route_code,
  t2.id                                    AS route_id,
  t1.rpln_day                              AS day_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
WHERE t1.aemp_id = $request->emp_id;");


            $data6 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.trip_id, t2.amim_id, t2.troc_cqty, t2.troc_dqty,t7.amim_duft) AS column_id,
  t2.trip_id,
  t2.amim_id                                                 AS sku_id,
  t7.amim_duft                                               AS ctn_size,
  t3.amin_snme                                               AS sku_name,
  t10.id                                                     AS sub_category_id,
  t10.issc_name                                              AS sub_category_name,
  t2.troc_cqty                                               AS confirm_qty,
  t2.troc_dqty                                               AS delivered_qty,
  t7.pldt_tppr*t7.amim_duft                                   AS ctn_price,
  t6.plmt_id                                                  as price_list_id
FROM tt_trip AS t1
  INNER JOIN tt_troc AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_tusr = t4.id
  INNER JOIN tm_dlrm AS t5 ON t1.dlrm_id = t5.id
  INNER JOIN tl_stcm AS t6 ON t4.site_id = t6.site_id AND t5.acmp_id = t6.acmp_id
  INNER JOIN tm_pldt AS t7 ON t7.plmt_id = t6.plmt_id AND t2.amim_id = t7.amim_id
  INNER JOIN tl_sgsm AS t8 ON t1.aemp_tusr = t8.aemp_id
  INNER JOIN tl_sgit AS t9 ON t3.id = t9.amim_id
  INNER JOIN tm_issc AS t10 ON t9.issc_id = t10.id
WHERE t1.aemp_tusr = $request->emp_id AND t3.lfcl_id = 1 AND t1.lfcl_id = 20");

            $data7 = DB::connection($country->cont_conn)->select("SELECT
  concat(id, t1.dprt_name, t1.dprt_code, t1.lfcl_id) AS column_id,
  t1.id                                      AS reason_id,
  t1.dprt_name                                    AS reason_name
FROM tm_dprt AS t1");


            $data8 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.trip_id, t1.id, t2.amim_id, t2.trgc_cqty, t2.trgc_dqty, t7.amim_duft) AS column_id,
  t1.id                                                                    AS trip_id,
  t2.amim_id                                                               AS sku_id,
  t7.amim_duft                                                             AS ctn_size,
  t3.amim_name                                                             AS sku_name,
  t10.id                                                                   AS sub_category_id,
  t10.issc_name                                                            AS sub_category_name,
  t2.trgc_cqty                                                             AS confirm_qty,
  t2.trgc_dqty                                                             AS delivered_qty,
  t7.pldt_tppr * t7.amim_duft                                              AS ctn_price
FROM tt_trip AS t1
  INNER JOIN tt_trgc AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_tusr = t4.id
  INNER JOIN tm_dlrm AS t5 ON t1.dlrm_id = t5.id
  INNER JOIN tl_stcm AS t6 ON t4.site_id = t6.site_id AND t5.acmp_id = t6.acmp_id
  INNER JOIN tm_pldt AS t7 ON t7.plmt_id = t6.plmt_id AND t2.amim_id = t7.amim_id
  INNER JOIN tl_sgsm AS t8 ON t1.aemp_tusr = t8.aemp_id
  INNER JOIN tl_sgit AS t9 ON t3.id = t9.amim_id
  INNER JOIN tm_issc AS t10 ON t9.issc_id = t10.id
WHERE t1.aemp_tusr = $request->emp_id AND t3.lfcl_id = 1 AND t1.lfcl_id = 20");
            return Array(
                "tblt_van_trip" => array("data" => $data1, "action" => $request->country_id),
                "tblt_van_product_details" => array("data" => $data2, "action" => $request->country_id),
                "tbld_van_credit_limit" => array("data" => $data3, "action" => $request->country_id),
                "tblt_van_route" => array("data" => $data4, "action" => $request->country_id),
                "tblt_van_stock" => array("data" => $data6, "action" => $request->country_id),
                "tblt_return_reason" => array("data" => $data7, "action" => $request->country_id),
                "tblt_van_grv_stock" => array("data" => $data8, "action" => $request->country_id)
            );

        }
    }

    public function vanSalesDataDown(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.lodm_code, t2.lfcl_id) AS column_id,
  0                                AS ou_id,
  t1.id                            AS trip_id,
  t1.dlrm_id                       AS depo_id,
  t1.aemp_tusr                     AS sr_id,
  ''                               AS sr_name,
  ''                               AS site_id,
  ''                               AS outlet_id,
  ''                               AS site_name,
  t2.lfcl_id                       AS status_id,
  t4.lfcl_name                     AS status,
  0                                AS lat,
  0                                AS lon,
  t2.lodm_code                     AS order_id,
  t2.id                            AS oid,
  ''                               AS collection_type,
  t2.lodm_date                     AS order_date,
  1                                AS is_synced
FROM tt_trip AS t1
  INNER JOIN tt_lodm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_lfcl AS t4 ON t2.lfcl_id = t4.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id = 20 and t2.lfcl_id in(1,23) and t2.lodt_id=1");

            $data2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.lodm_code, t2.lfcl_id, t3.amim_id, t5.amim_code, t3.lodl_qnty, t3.lodl_cqty,t3.lodl_dqty) AS column_id,
  t2.trip_id                                                                             AS trip_id,
  t2.lodm_code                                                                           AS order_id,
  t3.id                                                                                  AS oid,
  t3.amim_id                                                                             AS product_id,
  t5.amim_name                                                                           AS product_name,
  t5.amim_duft                                                                           AS ctn_size,
  t3.lodl_uprc                                                                           AS unit_price,
  t3.lodl_cqty                                                                           as confirm_qty,
  if(t3.lodl_dqty > 0, t3.lodl_dqty, t3.lodl_cqty)                                       AS delivary_qty,
  t3.lodl_cqty * t3.lodl_uprc                                                            AS total_confirmed_amount,
  if(t3.lodl_dqty > 0, t3.lodl_dqty * t3.lodl_uprc, t3.lodl_qnty * t3.lodl_uprc)         AS total_delivered_amount,
  ''                                                                                     AS promo_ref,
  0                                                                                      AS gst,
  1                                                                                      AS is_synced
FROM tt_trip AS t1
  INNER JOIN tt_lodm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_lodl AS t3 ON t2.id = t3.lodm_id
  INNER JOIN tm_lfcl AS t4 ON t2.lfcl_id = t4.id
  INNER JOIN tm_amim AS t5 ON t3.amim_id = t5.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id = 20 and t2.lfcl_id in(1,23) and t2.lodt_id=1");
            return Array(
                "tblt_van_load_sales_order" => array("data" => $data1, "action" => $request->country_id),
                "tblt_van_load_sales_order_line" => array("data" => $data2, "action" => $request->country_id),
            );
        }
    }

    public function vanRouteSiteData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.site_id,
         t1.route_id,
         t1.outlet_id,
         t1.site_name,
         t1.owner_name,
         t1.owner_mobile,
         t1.address,
         t1.pay_mode,
         t1.visited,
         t1.order_amount,
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
         t1.is_verified,
         t1.is_productive) AS column_id,
  t1.site_id,
  t1.route_id,
  t1.outlet_id,
  t1.site_name,
  t1.owner_name,
  t1.owner_mobile,
  t1.address,
  t1.pay_mode,
  t1.visited,
  t1.order_amount,
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
  t1.is_verified,
  t1.is_productive,
  t1.price_list_id
FROM (
       SELECT
         t3.site_id                 AS site_id,
         t1.rout_id                 AS route_id,
         t4.outl_id                 AS outlet_id,
         t4.site_name               AS site_name,
         t4.site_ownm               AS owner_name,
         t4.site_mob1               AS owner_mobile,
         t4.site_adrs               AS address,
         lower(t9.optp_name)        AS pay_mode,
         !isnull(t8.site_id)        AS visited,
         0                          AS order_amount,
         t4.site_hsno               AS house_no,
         t4.site_vtrn               AS site_trn,
         t7.stcm_limt               AS credit_limit,
         t7.stcm_duea               AS due_amount,
         t7.stcm_ordm               AS pre_order_amount,
         t7.stcm_odue               AS overdue,
         if(t11.id > 0, t11.id, 0)  AS must_sell_id,
         curdate()                  AS date,
         t4.geo_lat                 AS lat,
         t4.geo_lon                 AS lon,
         t4.site_vrfy               AS is_verified,
         if(t8.ssvh_ispd > 0, 1, 0) AS is_productive,
         t7.plmt_id                 AS price_list_id
       FROM tl_rpln AS t1
         INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
         INNER JOIN tl_rsmp AS t3 ON t2.id = t3.rout_id
         INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
         INNER JOIN tt_trip AS t5 ON t1.aemp_id = t5.aemp_tusr AND t5.lfcl_id = 20
         INNER JOIN tm_dlrm AS t6 ON t5.dlrm_id = t6.id
         INNER JOIN tl_stcm AS t7 ON t3.site_id = t7.site_id AND t6.acmp_id = t7.acmp_id
         LEFT JOIN th_ssvh AS t8 ON t3.site_id = t8.site_id AND t8.ssvh_date = curdate()  and t8.aemp_id=$request->emp_id
         INNER JOIN tm_optp AS t9 ON t7.optp_id = t9.id
         LEFT JOIN tl_msps AS t10 ON t3.site_id = t10.site_id
         LEFT JOIN tm_mspm AS t11 ON t10.mspm_id = t11.id AND curdate() BETWEEN t11.mspm_sdat AND t11.mspm_edat
       WHERE t1.aemp_id = $request->emp_id AND t1.rpln_day = DAYNAME(curdate()) AND t4.lfcl_id = 1
       UNION ALL
       SELECT

         t1.site_id                 AS site_id,
         t1.rout_id                 AS route_id,
         t4.outl_id                 AS outlet_id,
         t4.site_name               AS site_name,
         t4.site_ownm               AS owner_name,
         t4.site_mob1               AS owner_mobile,
         t4.site_adrs               AS address,
         lower(t9.optp_name)        AS pay_mode,
         !isnull(t8.site_id)        AS visited,
         0                          AS order_amount,
         t4.site_hsno               AS house_no,
         t4.site_vtrn               AS site_trn,
         t7.stcm_limt               AS credit_limit,
         t7.stcm_duea               AS due_amount,
         t7.stcm_ordm               AS pre_order_amount,
         t7.stcm_odue               AS overdue,
         if(t11.id > 0, t11.id, 0)  AS must_sell_id,
         curdate()                  AS date,
         t4.geo_lat                 AS lat,
         t4.geo_lon                 AS lon,
         t4.site_vrfy               AS is_verified,
         if(t8.ssvh_ispd > 0, 1, 0) AS is_productive,
         t7.plmt_id                 AS price_list_id
       FROM tl_ovpm AS t1
         INNER JOIN tm_site AS t4 ON t1.site_id = t4.id
         INNER JOIN tt_trip AS t5 ON t1.aemp_id = t5.aemp_tusr AND t5.lfcl_id = 20
         INNER JOIN tm_dlrm AS t6 ON t5.dlrm_id = t6.id
         INNER JOIN tl_stcm AS t7 ON t1.site_id = t7.site_id AND t6.acmp_id = t7.acmp_id
         LEFT JOIN th_ssvh AS t8 ON t1.site_id = t8.site_id AND t8.ssvh_date = curdate() and t8.aemp_id=$request->emp_id
         INNER JOIN tm_optp AS t9 ON t7.optp_id = t9.id
         LEFT JOIN tl_msps AS t10 ON t1.site_id = t10.site_id
         LEFT JOIN tm_mspm AS t11 ON t10.mspm_id = t11.id AND curdate() BETWEEN t11.mspm_sdat AND t11.mspm_edat
       WHERE t1.aemp_id = $request->emp_id AND t1.ovpm_date = curdate() AND t4.lfcl_id = 1
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
  t1.order_amount,
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
  t1.is_verified,
  t1.is_productive,
  t1.price_list_id;");


            return Array(
                "tblt_van_route_site" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function vanDeliveryData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.ordm_ornm, t1.lfcl_id) AS column_id,
  t1.acmp_id                       AS ou_id,
  t1.id                            AS line_id,
  0                                AS trip_id,
  t1.dlrm_id                       AS depo_id,
  t1.aemp_id                       AS sr_id,
  t4.lfcl_name                     AS sr_name,
  t1.site_id                       AS site_id,
  t3.outl_id                       AS outlet_id,
  t3.Site_Name                     AS site_name,
  t1.lfcl_id                       AS status_id,
  t4.lfcl_name                     AS status,
  t3.geo_lat                       AS lat,
  t3.geo_lon                       AS lon,
  t1.ordm_ornm                     AS order_id,
  ''                               AS collection_type,
  t1.ordm_date                     AS order_date,
  t3.site_vtrn                     AS site_trn
FROM tt_ordm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.ID
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
WHERE t1.aemp_id = $request->emp_id AND t1.lfcl_id = 1");

            $data2 = DB::connection($country->cont_conn)->select("
SELECT
  concat(t2.id, t1.id, t1.lfcl_id) AS column_id,
  0                                AS trip_id,
  t2.id                            AS order_line_id,
  t1.ordm_ornm                     AS order_id,
  t2.amim_id                       AS product_id,
  t3.amim_name                     AS product_name,
  t2.ordd_duft                     AS ctn_size,
  t2.ordd_uprc * t2.ordd_duft      AS unit_price,
  t2.ordd_qnty                     AS confirm_qty,
  t2.ordd_qnty                     AS delivary_qty,
  t2.ordd_opds                     AS def_discount_confirm,
  t2.ordd_opds                     AS def_discount_delivery,
  t2.ordd_spdo                     AS confirm_discount,
  t2.ordd_spdo                     AS delivery_discount,
  t2.ordd_opds                     AS promo_discount_confirm,
  t2.ordd_opds                     AS promo_discount_delivery,
  t2.ordd_oamt                     AS total_confirmed_amount,
  t2.ordd_oamt                     AS total_delivered_amount,
  t2.prom_id                       AS promo_ref,
  t2.ordd_excs                     AS gst,
  t2.ordd_ovat                     AS vat
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.aemp_id = $request->emp_id AND t1.lfcl_id = 1");


            return Array(
                "tblt_van_pre_order" => array("data" => $data1, "action" => $request->country_id),
                "tblt_van_pre_order_line" => array("data" => $data2, "action" => $request->country_id),
            );

        }
    }

    public function vanOrderDeliverySave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $orderMaster = OrderMaster::on($country->cont_conn)->findorfail($request->line_id);
            $dataOrderLines = json_decode($request->line_data);
            if ($orderMaster->lfcl_id != 11) {
                DB::connection($country->cont_conn)->beginTransaction();
                try {

                    $orderMaster->lfcl_id = $request->status_id;
                    $orderMaster->ordm_date = date('Y-m-d');
                    $orderMaster->ordm_time = date('Y-m-d H:i:s');
                    $orderMaster->geo_lon = $request->lat;
                    $orderMaster->geo_lon = $request->lon;
                    $orderMaster->ordm_dtne = $request->distance;
                    $orderMaster->aemp_eusr = $request->up_emp_id;
                    $orderMaster->save();
                    $tripOrder = new TripOrder();
                    $tripOrder->setConnection($country->cont_conn);
                    $tripOrder->trip_id = $request->trip_id;
                    $tripOrder->ordm_id = $orderMaster->id;
                    $tripOrder->lfcl_id = 11;
                    $tripOrder->ondr_id = 0;
                    $tripOrder->cont_id = $request->country_id;
                    $tripOrder->aemp_iusr = $request->up_emp_id;
                    $tripOrder->aemp_eusr = $request->up_emp_id;
                    $tripOrder->save();
                    $deliverySequence = SequenceNumber::on($country->cont_conn)->where(['acmp_id' => $request->ou_id, 'vatn_year' => date('Y')])->first();
                    if ($deliverySequence == null) {
                        $deliverySequence = new SequenceNumber();
                        $deliverySequence->setConnection($country->cont_conn);
                        $deliverySequence->acmp_id = $request->ou_id;
                        $deliverySequence->vatn_year = date('Y');
                        $deliverySequence->vatn_icnt = 0;
                        $deliverySequence->vatn_rcnt = 0;
                        $deliverySequence->cont_id = $request->country_id;
                        $deliverySequence->lfcl_id = 1;
                        $deliverySequence->aemp_iusr = $request->up_emp_id;
                        $deliverySequence->aemp_eusr = $request->up_emp_id;
                        $deliverySequence->save();
                    }
                    $deliverySequence->vatn_icnt = $deliverySequence->vatn_icnt + 1;
                    $deliverySequence->aemp_eusr = $request->up_emp_id;
                    $deliverySequence->save();
                    $deliverySequenceMapping = new SequenceMappingInvoice();
                    $deliverySequenceMapping->setConnection($country->cont_conn);
                    $deliverySequenceMapping->acmp_id = $request->ou_id;
                    $deliverySequenceMapping->vatn_year = date('Y');
                    $deliverySequenceMapping->ordm_id = $request->line_id;
                    $deliverySequenceMapping->vatn_icnt = $deliverySequence->vatn_icnt;
                    $deliverySequenceMapping->lfcl_id = 1;
                    $deliverySequenceMapping->cont_id = $request->country_id;
                    $deliverySequenceMapping->aemp_iusr = $request->up_emp_id;
                    $deliverySequenceMapping->aemp_eusr = $request->up_emp_id;
                    $deliverySequenceMapping->save();
                    foreach ($dataOrderLines as $dataOrderLine) {
                        $orderLine = OrderLine::on($country->cont_conn)->findorfail($dataOrderLine->order_line_id);
                        $orderLine->ordd_dqty = $dataOrderLine->delivary_qty;
                        $orderLine->ordd_dfdd = $dataOrderLine->def_discount_delivery;
                        $orderLine->ordd_spdd = $dataOrderLine->delivery_discount;
                        $orderLine->ordd_dpds = $dataOrderLine->promo_discount_delivery;
                        $orderLine->ordd_odat = $dataOrderLine->total_delivered_amount;
                        $orderLine->ordd_tdis = $dataOrderLine->total_discount;
                        $orderLine->ordd_texc = $dataOrderLine->total_gst;
                        $orderLine->ordd_tvat = $dataOrderLine->total_vat;
                        $orderLine->ordd_amnt = $dataOrderLine->net_amount;
                        $orderLine->save();
                        $tripSku = TripSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $dataOrderLine->product_id])->first();
                        $tripSku->troc_dqty = $tripSku->troc_dqty + $dataOrderLine->delivary_qty;
                        $tripSku->aemp_eusr = $request->up_emp_id;
                        $tripSku->save();
                    }
                    DB::connection($country->cont_conn)->commit();
                    return array('column_id' => $request->id);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                }
            } else {
                return array('column_id' => $request->id);
            }
        }
    }

}