<?php

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\ReturnLine;
use App\BusinessObject\SequenceMappingReturn;
use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\ReturnMaster;
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

class DeliveryModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function dmOrderDeliverySave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $orderMaster = OrderMaster::on($country->cont_conn)->findorfail($request->oid);
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
                    $tripOrder = TripOrder::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'ordm_id' => $request->oid])->first();
                    $tripOrder->lfcl_id = $request->status_id;
                    $tripOrder->save();
                    if ($request->status_id == 11) {
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
                        $deliverySequenceMapping->ordm_id = $request->oid;
                        $deliverySequenceMapping->vatn_icnt = $deliverySequence->vatn_icnt;
                        $deliverySequenceMapping->lfcl_id = 1;
                        $deliverySequenceMapping->cont_id = $request->country_id;
                        $deliverySequenceMapping->aemp_iusr = $request->up_emp_id;
                        $deliverySequenceMapping->aemp_eusr = $request->up_emp_id;
                        $deliverySequenceMapping->save();
                        foreach ($dataOrderLines as $dataOrderLine) {
                            $orderLine = OrderLine::on($country->cont_conn)->findorfail($dataOrderLine->oid);
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

    public function dmReturnDeliverySave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $returnMaster = ReturnMaster::on($country->cont_conn)->findorfail($request->oid);
            $dataOrderLines = json_decode($request->line_data);
            if ($returnMaster->status_id != 11) {
                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $returnMaster->lfcl_id = $request->status_id;
                    $returnMaster->rtan_drdt = date('Y-m-d');
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    //$orderMaster->d_lat = $request->lat;
                    //$orderMaster->d_lon = $request->lon;
                    //$orderMaster->d_distance = $request->distance;
                    // $returnMaster->invoice_amount = $request->status_id == 13 ? array_sum(array_column($dataOrderLines, 'net_amount')) : 0;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    $tripOrder = TripGRV::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'rtan_id' => $request->oid])->first();
                    $tripOrder->lfcl_id = $request->status_id;
                    $tripOrder->save();
                    if ($request->status_id == 11) {
                        $slNumber = SequenceNumber::on($country->cont_conn)->where(['acmp_id' => $request->ou_id, 'vatn_year' => date('Y')])->first();
                        if ($slNumber == null) {
                            $slNumber = new SequenceNumber();
                            $slNumber->setConnection($country->cont_conn);
                            $slNumber->acmp_id = $request->ou_id;
                            $slNumber->vatn_year = date('Y');
                            $slNumber->vatn_icnt = 0;
                            $slNumber->vatn_rcnt = 0;
                            $slNumber->cont_id = $request->country_id;
                            $slNumber->lfcl_id = 1;
                            $slNumber->aemp_iusr = $request->up_emp_id;
                            $slNumber->aemp_eusr = $request->up_emp_id;
                            $slNumber->save();
                        }
                        $slNumber->vatn_rcnt = $slNumber->vatn_rcnt + 1;
                        $slNumber->aemp_eusr = $request->up_emp_id;
                        $slNumber->save();
                        $deliverySequenceMapping = new SequenceMappingReturn();
                        $deliverySequenceMapping->setConnection($country->cont_conn);
                        $deliverySequenceMapping->acmp_id = $request->ou_id;
                        $deliverySequenceMapping->vatn_year = date('Y');
                        $deliverySequenceMapping->rtan_id = $request->oid;
                        $deliverySequenceMapping->vatn_rcnt = $slNumber->vatn_rcnt;
                        $deliverySequenceMapping->lfcl_id = 1;
                        $deliverySequenceMapping->cont_id = $request->country_id;
                        $deliverySequenceMapping->aemp_iusr = $request->up_emp_id;
                        $deliverySequenceMapping->aemp_eusr = $request->up_emp_id;
                        $deliverySequenceMapping->save();
                        foreach ($dataOrderLines as $dataOrderLine) {
                            $orderLine = ReturnLine::on($country->cont_conn)->findorfail($dataOrderLine->oid);
                            $orderLine->rtdd_dqty = $dataOrderLine->quantity_delivered;
                            $orderLine->rtdd_damt = $dataOrderLine->total_amount_delivered;
                            $orderLine->rtdd_rato = $dataOrderLine->discount_ratio;
                            $orderLine->rtdd_excs = $dataOrderLine->gst;
                            $orderLine->rtdd_ovat = $dataOrderLine->vat;
                            $orderLine->rtdd_tdis = $dataOrderLine->total_discount;
                            $orderLine->rtdd_texc = $dataOrderLine->total_gst;
                            $orderLine->rtdd_tvat = $dataOrderLine->total_vat;
                            $orderLine->rtdd_amnt = $dataOrderLine->net_amount;
                            $orderLine->save();
                            $tripGrvSku = TripGrvSku::on($country->cont_conn)->where(['trip_id' => $request->trip_id, 'amim_id' => $dataOrderLine->product_id])->first();
                            $tripGrvSku->trgc_cqty = $tripGrvSku->trgc_cqty + $dataOrderLine->quantity_delivered;
                            $tripGrvSku->aemp_eusr = $request->up_emp_id;
                            $tripGrvSku->save();
                        }
                    }

                    DB::connection($country->cont_conn)->commit();
                    return array('column_id' => $request->id);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                }
            } else {
                return array('column_id' =>$request->id);
            }
        }
    }

    public function dmTripStatusChange(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $trip = Trip::on($country->cont_conn)->where(['id' => $request->trip_id])->whereIn('lfcl_id', [31, 20, 5, 6, 32])->whereNotIn('lfcl_id', [1, 2, 25])->first();
            if ($trip != null) {
                $trip->lfcl_id = $request->status_id;
                $trip->save();
            }
            return array('column_id' => $request->id);
        }
    }

    public function deliveryData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.lfcl_id) AS column_id,
  concat(t1.id, t1.lfcl_id) AS token,
  t1.id                     AS trip_id,
  t2.aemp_name              AS driver_name,
  t1.trip_date              AS date,
  t1.id                     AS trip_code,
  t4.vhcl_name              AS vehicle_name,
  t1.lfcl_id                AS status_id
FROM tt_trip AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_tusr = t2.id
  INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
  INNER JOIN tm_vhcl AS t4 ON t1.vhcl_id = t4.id
WHERE t1.lfcl_id IN (31,20,5,6,32) AND t1.aemp_tusr = $request->emp_id");

            $data2 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.amim_id, t2.id, t1.lfcl_id, t2.troc_dqty, t2.troc_cqty, t2.troc_iqty,t2.troc_dqty) AS column_id,
  t1.id                                                                                  AS trip_id,
  t2.amim_id                                                                             AS product_code,
  t3.amim_code                                                                           AS product_name,
  t3.amim_duft                                                                           AS pack_size,
  t2.troc_iqty                                                                           AS issued_pcs,
  t2.troc_cqty                                                                           AS confirm_qty,
  t2.troc_dqty                                                                           AS delivered_qty,
  t1.lfcl_id                                                                             AS status_id,
  t4.lfcl_name                                                                           AS status
FROM tt_trip AS t1
  INNER JOIN tt_troc AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31,20,5,6,32)");


            $data3 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.amim_id, t2.id, t1.lfcl_id, t2.trgc_iqty, t2.trgc_cqty) AS column_id,
  t1.id                                                                           AS trip_id,
  1                                                                               AS return_type,
  t2.amim_id                                                                      AS product_code,
  t3.amim_name                                                                    AS product_name,
  t3.amim_duft                                                                    AS pack_size,
  t2.trgc_iqty                                                                    AS issued_pcs,
  t2.trgc_cqty                                                                    AS collected_qty,
  t1.lfcl_id                                                                      AS status_id,
  t4.lfcl_name                                                                    AS status,
  ''                                                                              AS return_type_name
FROM tt_trip AS t1
  INNER JOIN tt_trgc AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31,20,5,6,32)");

            $data4 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.ondr_name) AS column_id,
  t1.id                       AS reason_id,
  t1.ondr_name                AS reason_name
FROM tm_ondr AS t1
WHERE t1.cont_id = $request->country_id");


            $data5 = DB::connection($country->cont_conn)->select("SELECT
  concat(t3.ordm_ornm, t3.lfcl_id, t2.lfcl_id, t1.id, t7.optp_id) AS column_id,
  t3.acmp_id                                                      AS ou_id,
  t1.id                                                           AS trip_id,
  t3.dlrm_id                                                      AS depo_id,
  t4.id                                                           AS sr_id,
  t4.aemp_name                                                    AS sr_name,
  t3.site_id                                                      AS site_id,
  t5.outl_id                                                      AS outlet_id,
  t5.site_name                                                    AS site_name,
  t2.lfcl_id                                                      AS status_id,
  t6.lfcl_name                                                    AS status,
  t5.geo_lat                                                      AS lat,
  t5.geo_lon                                                      AS lon,
  t3.ordm_ornm                                                    AS order_id,
  t3.id                                                           AS oid,
  lower(t8.optp_name)                                             AS collection_type,
  t3.ordm_date                                                    AS order_date,
  t5.site_vtrn                                                    AS site_trn
FROM tt_trip AS t1
  INNER JOIN tt_trom AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_site AS t5 ON t3.site_id = t5.id
  INNER JOIN tm_lfcl AS t6 ON t3.lfcl_id = t6.id
  INNER JOIN tl_stcm AS t7 ON t3.acmp_id = t7.acmp_id AND t3.site_id = t7.site_id
  INNER JOIN tm_optp AS t8 ON t7.optp_id = t8.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31,20,5,6,32)
UNION ALL
SELECT
  concat(t3.rtan_rtnm, t3.lfcl_id, t2.lfcl_id, t1.id, t7.optp_id) AS column_id,
  t3.acmp_id                                                      AS ou_id,
  t1.id                                                           AS trip_id,
  t3.dlrm_id                                                      AS depo_id,
  t4.id                                                           AS sr_id,
  t4.aemp_name                                                    AS sr_name,
  t3.site_id                                                      AS site_id,
  t5.outl_id                                                      AS outlet_id,
  t5.site_name                                                    AS site_name,
  11                                                              AS status_id,
  t6.lfcl_name                                                    AS status,
  t5.geo_lat                                                      AS lat,
  t5.geo_lon                                                      AS lon,
  t3.rtan_rtnm                                                    AS order_id,
  t3.id                                                           AS oid,
  lower(t8.optp_name)                                             AS collection_type,
  t3.rtan_date                                                    AS order_date,
  t5.site_vtrn                                                    AS site_trn
FROM tt_trip AS t1
  INNER JOIN tt_trgm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_rtan AS t3 ON t2.rtan_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_site AS t5 ON t3.site_id = t5.id
  INNER JOIN tm_lfcl AS t6 ON t3.lfcl_id = t6.id
  INNER JOIN tl_stcm AS t7 ON t3.acmp_id = t7.acmp_id AND t3.site_id = t7.site_id
  INNER JOIN tm_optp AS t8 ON t7.optp_id = t8.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31,20,5,6,32) AND t7.optp_id = 2");

            $data6 = DB::connection($country->cont_conn)->select("SELECT
  concat(t4.id, t1.id)                            AS column_id,
  concat(t4.id, t1.id)                            AS token,
  t4.id                                           AS order_line_id,
  t2.trip_id                                      AS trip_id,
  t3.ordm_ornm                                    AS order_id,
  t4.id                                           AS oid,
  t4.amim_id                                      AS product_id,
  t5.amim_name                                    AS product_name,
  t5.amim_duft                                    AS ctn_size,
  t4.ordd_uprc * t5.amim_duft                     AS unit_price,
  t4.ordd_cqty                                    AS confirm_qty,
  if(t3.lfcl_id = 11, t4.ordd_dqty, t4.ordd_cqty) AS delivary_qty,
  t4.ordd_dfdc                                    AS def_discount_confirm,
  if(t3.lfcl_id=11,t4.ordd_dfdd,t4.ordd_dfdc)                                    AS def_discount_delivery,
  t4.ordd_spdc                                    AS confirm_discount,
  if(t3.lfcl_id=11,t4.ordd_spdd,t4.ordd_spdc)                                    AS delivery_discount,
  t4.ordd_cpds                                    AS promo_discount_confirm,
  if(t3.lfcl_id=11,t4.ordd_dpds,t4.ordd_cpds)                                   AS promo_discount_delivery,
  t4.ordd_oamt                                    AS total_confirmed_amount,
  if(t3.lfcl_id = 11, t4.ordd_odat, t4.ordd_ocat) AS total_delivered_amount,
  t4.prom_id                                      AS promo_ref,
  t4.ordd_excs                                    AS gst,
  t4.ordd_ovat                                    AS vat
FROM tt_trip AS t1
  INNER JOIN tt_trom AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_ordm AS t3 ON t2.ordm_id = t3.id
  INNER JOIN tt_ordd AS t4 ON t3.id = t4.ordm_id
  INNER JOIN tm_amim AS t5 ON t5.id = t4.amim_id
WHERE t1.aemp_tusr = $request->emp_id  AND t1.lfcl_id IN (31,20,5,6,32)");

            $data7 = DB::connection($country->cont_conn)->select("SELECT
  concat(t3.id, t3.rtan_rtnm, t3.lfcl_id, t1.id) AS column_id,
  t3.acmp_id                                     AS ou_id,
  t1.id                                          AS trip_id,
  t3.dlrm_id                                     AS depo_id,
  t5.id                                          AS sr_id,
  t3.rtan_date                                   AS date,
  t5.aemp_name                                   AS sr_name,
  t3.site_id                                     AS site_id,
  t4.site_name                                   AS site_name,
  t3.lfcl_id                                     AS status_id,
  t6.lfcl_name                                   AS status,
  t3.rtan_rtnm                                   AS return_id,
  t3.id                                          AS oid,
  t3.id                                          AS return_order_id,
  t4.site_vtrn                                   AS vat_trn
FROM tt_trip AS t1
  INNER JOIN tt_trgm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_rtan AS t3 ON t2.rtan_id = t3.id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t3.aemp_id = t5.id
  INNER JOIN tm_lfcl AS t6 ON t3.lfcl_id = t6.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31,20,5,6,32)");

            $data8 = DB::connection($country->cont_conn)->select("SELECT
  concat(t4.id, t1.id)        AS column_id,
  concat(t4.id, t1.id)        AS token,
  t4.id                       AS order_line_id,
  t2.trip_id                  AS trip_id,
  t3.rtan_rtnm                AS return_id,
  t4.id                       AS oid,
  t4.amim_id                  AS product_id,
  t5.amim_name                AS product_name,
  t5.amim_duft                AS ctn_size,
  t4.rtdd_uprc * t5.amim_duft AS unit_price,
  1                           AS retrun_type,
  t4.rtdd_qnty                AS quantity_returned,
  if(t3.lfcl_id = 13, t4.rtdd_dqty,
     t4.rtdd_qnty)            AS quantity_delivered,
  t4.rtdd_oamt                AS total_amount,
  if(t3.lfcl_id = 13, t4.rtdd_damt,
     t4.rtdd_oamt)            AS total_amount_delivered,
  t4.rtdd_excs                AS gst,
  t4.rtdd_ovat                AS vat,
  t4.ordm_ornm                AS order_ref_no,
  t4.rtdd_rato                AS discount_ratio
FROM tt_trip AS t1
  INNER JOIN tt_trgm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_rtan AS t3 ON t2.rtan_id = t3.id
  INNER JOIN tt_rtdd AS t4 ON t3.id = t4.rtan_id
  INNER JOIN tm_amim AS t5 ON t4.amim_id = t5.id
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (31, 20, 5, 6, 32)");


            return Array(
                "tblt_trip" => array("data" => $data1, "action" => $request->country_id),
                "tblt_driver_trip_details" => array("data" => $data2, "action" => $request->country_id),
                "tblt_driver_trip_grv_details" => array("data" => $data3, "action" => $request->country_id),
                "tblt_driver_sales_order" => array("data" => $data5, "action" => $request->country_id),
                "tblt_driver_sales_order_line" => array("data" => $data6, "action" => $request->country_id),
                "tblt_driver_return_order" => array("data" => $data7, "action" => $request->country_id),
                "tblt_driver_return_order_line" => array("data" => $data8, "action" => $request->country_id),
                "tbld_status_change_reason" => array("data" => $data4, "action" => $request->country_id),
            );
        }
    }

    public function deliveredData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.invc_code, t1.lfcl_id,t1.aemp_susr, t1.aemp_cusr) AS column_id,
  t1.acmp_id                       AS ou_id,
  t1.aemp_susr                     AS sr_id,
  t1.aemp_cusr                       AS emp_code,
  t1.invc_date                     AS date,
  t1.invc_note                     AS note,
  t1.site_id                       AS site_id,
  t1.trip_id,
  t1.invc_code                     AS invoice_code,
  t1.invc_amnt                     AS invoice_amount,
  t1.invc_mamt                     AS collection_amount,
  t1.invt_id                       AS invoice_type,
  t1.trnt_id                       AS transaction_type,
  t1.lfcl_id                       AS status_id,
  1                                AS is_synced
FROM tt_invc AS t1
  INNER JOIN tt_trip AS t2 ON t1.trip_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_cusr = t3.id
WHERE t2.aemp_tusr = $request->emp_id AND t2.lfcl_id = 20;");
            $data2 = DB::connection($country->cont_conn)->select("SELECT
   concat(t1.cltn_ucod, t1.lfcl_id,t1.aemp_susr) AS column_id,
  t6.site_id   AS site_id,
  t1.aemp_susr        AS sr_id,
  t1.aemp_cusr AS emp_id,
  t1.acmp_id   AS ou_id,
  t1.trip_id,
  t1.cltn_date AS date,
  t1.oult_id   AS outlet_id,
  t1.cltn_amnt AS amount,
  t1.clpt_id   AS payment_type,
  t1.lfcl_id   AS status_id,
  1            AS is_synced
FROM tt_cltn AS t1
  INNER JOIN tt_trip AS t2 ON t1.trip_id = t2.id
  INNER JOIN tt_clim AS t4 ON t1.id = t4.cltn_id
  INNER JOIN tm_aemp AS t5 ON t1.aemp_cusr = t5.id
  INNER JOIN tt_invc AS t6 ON t4.invc_code = t6.invc_code
WHERE t2.aemp_tusr = $request->emp_id AND t2.lfcl_id = 20
GROUP BY t1.cltn_ucod,t1.acmp_id, t1.aemp_cusr, t1.aemp_susr, t1.trip_id, t1.cltn_date, t1.oult_id, t1.cltn_amnt,
  t1.clpt_id, t1.lfcl_id, t6.site_id, t5.aemp_usnm");

            $data3 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t3.invc_date, t3.invc_amnt, t3.invc_mamt, t3.trnt_id, t2.aemp_id, t2.trip_id, t2.site_id, t2.invc_code,
         t2.srcr_amnt, t2.lfcl_id) AS column_id,
  t3.invc_date                     AS date,
  t3.invc_amnt                     AS invoice_amount,
  t3.invc_mamt                     AS collection_amount,
  t3.trnt_id                       AS trn_type,
  t2.aemp_id                       AS sr_id,
  t2.trip_id                       AS trip_id,
  t2.site_id                       AS site_id,
  t2.invc_code                     AS invoice_code,
  t2.srcr_amnt                     AS request_amount,
  2                                AS status_id,
  1                                AS is_synced
FROM tt_trip AS t1
  INNER JOIN tt_srcr AS t2 ON t1.id = t2.trip_id
  INNER JOIN tt_invc AS t3 ON t2.invc_code = t3.invc_code
WHERE t1.aemp_tusr = $request->emp_id AND t1.lfcl_id IN (20, 5)
GROUP BY t1.id, t3.invc_date, t3.invc_amnt, t3.invc_mamt, t3.trnt_id, t2.aemp_id, t2.trip_id, t2.site_id, t2.invc_code,
  t2.srcr_amnt, t2.lfcl_id");
            return Array(
                "tblt_driver_collection" => array("data" => $data2, "action" => $request->country_id),
                "tblt_site_invoice" => array("data" => $data1, "action" => $request->country_id),
                "tblt_sr_credit_invoice_mapping" => array("data" => $data3, "action" => $request->country_id),
            );
        }
    }
}