<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
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
        $orderMaster = OrderMaster::findorfail($request->oid);
        $dataOrderLines = json_decode($request->line_data);
        if ($orderMaster->status_id != 13) {
            DB::beginTransaction();
            try {

                $orderMaster->status_id = $request->status_id;
                $orderMaster->delivery_date = date('Y-m-d');
                $orderMaster->delivery_date_time = date('Y-m-d H:i:s');
                $orderMaster->d_lat = $request->status_id == 13 ? $request->lat : 0;
                $orderMaster->d_lon = $request->status_id == 13 ? $request->lon : 0;
                $orderMaster->d_distance = $request->status_id == 13 ? $request->distance : 0;
                $orderMaster->invoice_amount = $request->status_id == 13 ? array_sum(array_column($dataOrderLines, 'net_amount')) : 0;
                $orderMaster->updated_by = $request->up_emp_id;
                $orderMaster->save();
                $tripOrder = TripOrder::where(['trip_id' => $request->trip_id, 'order_id' => $request->oid])->first();
                $tripOrder->status_id = $request->status_id;
                $tripOrder->save();
                if ($request->status_id == 13) {
                    $deliverySequence = SequenceNumber::where(['company_id' => $request->ou_id, 'year' => date('Y')])->first();
                    if ($deliverySequence == null) {
                        $deliverySequence = new SequenceNumber();
                        $deliverySequence->company_id = $request->ou_id;
                        $deliverySequence->year = date('Y');
                        $deliverySequence->order_count = 0;
                        $deliverySequence->return_count = 0;
                        $deliverySequence->country_id = $request->country_id;
                        $deliverySequence->status_id = 1;
                        $deliverySequence->created_by = $request->up_emp_id;
                        $deliverySequence->updated_by = $request->up_emp_id;
                        $deliverySequence->updated_count = 0;
                        $deliverySequence->save();
                    }
                    $deliverySequence->order_count = $deliverySequence->order_count + 1;
                    $deliverySequence->updated_by = $request->up_emp_id;
                    $deliverySequence->updated_count = $deliverySequence->updated_count + 1;
                    $deliverySequence->save();
                    $deliverySequenceMapping = new SequenceMappingInvoice();
                    $deliverySequenceMapping->company_id = $request->ou_id;
                    $deliverySequenceMapping->year = date('Y');
                    $deliverySequenceMapping->o_id = $request->oid;
                    $deliverySequenceMapping->s_no = $deliverySequence->order_count;
                    $deliverySequenceMapping->order_type_id = 1;
                    $deliverySequenceMapping->country_id = $request->country_id;
                    $deliverySequenceMapping->created_by = $request->up_emp_id;
                    $deliverySequenceMapping->updated_by = $request->up_emp_id;
                    $deliverySequenceMapping->updated_count = 0;
                    $deliverySequenceMapping->save();
                    foreach ($dataOrderLines as $dataOrderLine) {
                        $orderLine = OrderLine::findorfail($dataOrderLine->oid);
                        $orderLine->qty_delivery = $dataOrderLine->delivary_qty;
                        $orderLine->total_delivery = $dataOrderLine->total_delivered_amount;
                        $orderLine->invoice_amount = $dataOrderLine->net_amount;
                        $orderLine->updated_count = $orderLine->updated_count + 1;
                        $orderLine->save();
                        $tripSku = TripSku::where(['trip_id' => $request->trip_id, 'sku_id' => $dataOrderLine->product_id])->first();
                        $tripSku->delivery_qty = $tripSku->delivery_qty + $dataOrderLine->delivary_qty;
                        $tripSku->updated_by = $request->up_emp_id;
                        $tripSku->updated_count = $tripSku->updated_count + 1;
                        $tripSku->save();
                    }

                }

                DB::commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        } else {
            return array('column_id' => $request->id);
        }
    }

    public function dmReturnDeliverySave(Request $request)
    {
        $orderMaster = OrderMaster::findorfail($request->oid);
        $dataOrderLines = json_decode($request->line_data);
        if ($orderMaster->status_id != 13) {
            DB::beginTransaction();
            try {

                $orderMaster->status_id = $request->status_id;
                $orderMaster->delivery_date = date('Y-m-d');
                $orderMaster->delivery_date_time = date('Y-m-d H:i:s');
                //$orderMaster->d_lat = $request->lat;
                //$orderMaster->d_lon = $request->lon;
                //$orderMaster->d_distance = $request->distance;
                $orderMaster->invoice_amount = $request->status_id == 13 ? array_sum(array_column($dataOrderLines, 'net_amount')) : 0;
                $orderMaster->updated_by = $request->up_emp_id;
                $orderMaster->save();
                $tripOrder = TripOrder::where(['trip_id' => $request->trip_id, 'order_id' => $request->oid])->first();
                $tripOrder->status_id = $request->status_id;
                $tripOrder->save();
                if ($orderMaster->status_id == 13) {
                    $deliverySequence = DeliverySequence::where(['company_id' => $request->ou_id, 'year' => date('Y')])->first();
                    if ($deliverySequence == null) {
                        $deliverySequence = new SequenceNumber();
                        $deliverySequence->company_id = $request->ou_id;
                        $deliverySequence->year = date('Y');
                        $deliverySequence->order_count = 0;
                        $deliverySequence->return_count = 0;
                        $deliverySequence->country_id = $request->country_id;
                        $deliverySequence->status_id = 1;
                        $deliverySequence->created_by = $request->up_emp_id;
                        $deliverySequence->updated_by = $request->up_emp_id;
                        $deliverySequence->updated_count = 0;
                        $deliverySequence->save();
                    }
                    $deliverySequence->return_count = $deliverySequence->return_count + 1;
                    $deliverySequence->updated_by = $request->up_emp_id;
                    $deliverySequence->updated_count = $deliverySequence->updated_count + 1;
                    $deliverySequence->save();
                    $deliverySequenceMapping = new SequenceMappingInvoice();
                    $deliverySequenceMapping->company_id = $request->ou_id;
                    $deliverySequenceMapping->year = date('Y');
                    $deliverySequenceMapping->o_id = $request->oid;
                    $deliverySequenceMapping->s_no = $deliverySequence->return_count;
                    $deliverySequenceMapping->order_type_id = 2;
                    $deliverySequenceMapping->country_id = $request->country_id;
                    $deliverySequenceMapping->created_by = $request->up_emp_id;
                    $deliverySequenceMapping->updated_by = $request->up_emp_id;
                    $deliverySequenceMapping->updated_count = 0;
                    $deliverySequenceMapping->save();
                    foreach ($dataOrderLines as $dataOrderLine) {
                        $orderLine = OrderLine::findorfail($dataOrderLine->oid);
                        $orderLine->qty_delivery = $dataOrderLine->quantity_delivered;
                        $orderLine->total_delivery = $dataOrderLine->total_amount_delivered;
                        $orderLine->invoice_amount = $dataOrderLine->net_amount;
                        $orderLine->updated_count = $orderLine->updated_count + 1;
                        $orderLine->save();
                        $tripGrvSku = TripGrvSku::where(['trip_id' => $request->trip_id, 'sku_id' => $dataOrderLine->product_id])->first();
                        $tripGrvSku->delivery_qty = $tripGrvSku->delivery_qty + $dataOrderLine->quantity_delivered;
                        $tripGrvSku->updated_by = $request->up_emp_id;
                        $tripGrvSku->updated_count = $tripGrvSku->updated_count + 1;
                        $tripGrvSku->save();
                    }
                }

                DB::commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        } else {
            return array('column_id' => $request->id);
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

    public function deliveryData(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t1.id,t1.status_id)        AS column_id,
  concat(t1.id,t1.status_id)        AS token,
  t1.id        AS trip_id,
  t2.name      AS driver_name,
  t1.trip_date AS date,
  t1.id        AS trip_code,
  t2.name      AS vehicle_name,
  t1.status_id
FROM `tblt_trip` AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_lifecycle_status AS t6 ON t1.status_id = t6.id
WHERE t1.status_id IN (7, 8, 9) AND t1.emp_id = $request->emp_id and t1.trip_type_id=1");

        $data2 = DB::select("SELECT
  concat(t2.sku_id, t2.id, t1.status_id, t2.delivery_qty, t2.confirm_qty, t2.issued_qty) AS column_id,
  concat(t2.sku_id, t2.id, t1.status_id, t2.delivery_qty, t2.confirm_qty, t2.issued_qty) AS token,
  t1.id                                                                                  AS trip_id,
  t2.sku_id                                                                              AS product_code,
  t3.name                                                                                AS product_name,
  t3.ctn_size                                                                            AS pack_size,
  t2.issued_qty                                                                          AS issued_pcs,
  t2.confirm_qty                                                                         AS confirm_qty,
  t2.delivery_qty                                                                        AS delivered_qty,
  t1.status_id,
  t4.name                                                                                AS status
FROM `tblt_trip` AS t1
  INNER JOIN tblt_trip_sku_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (7, 8, 9)");


        $data3 = DB::select("SELECT
  concat(t2.sku_id, t2.id, t1.status_id, t2.delivery_qty, t2.confirm_qty, t2.issued_qty) AS column_id,
  concat(t2.sku_id, t2.id, t1.status_id, t2.delivery_qty, t2.confirm_qty, t2.issued_qty) AS token,
  t1.id                                                                                  AS trip_id,
  1                                                                                      AS return_type,
  t2.sku_id                                                                              AS product_code,
  t3.name                                                                                AS product_name,
  t3.ctn_size                                                                            AS pack_size,
  t2.issued_qty                                                                          AS issued_pcs,
  t2.delivery_qty                                                                        AS collected_qty,
  t1.status_id,
  t4.name                                                                                AS status,
  ''                                                                                     AS return_type_name
FROM `tblt_trip` AS t1
  INNER JOIN tblt_trip_grv_sku_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
  INNER JOIN tbld_lifecycle_status AS t4 ON t1.status_id = t4.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (7, 8, 9)");

        $data4 = DB::select("SELECT
  concat(t1.id, t1.name) AS column_id,
  concat(t1.id, t1.name) AS token,
  t1.id                  AS reason_id,
  t1.name                AS reason_name
FROM tbld_undelivered_reason AS t1
WHERE t1.country_id = $request->country_id");


        $data5 = DB::select("SELECT
  concat(t3.order_id, t3.status_id, t2.status_id, t1.id, t7.payment_type_id) AS column_id,
  concat(t3.order_id, t3.status_id, t2.status_id, t1.id, t7.payment_type_id) AS token,
  t3.company_id                                                              AS ou_id,
  t1.id                                                                      AS trip_id,
  t3.depot_id                                                                AS depo_id,
  t4.user_name                                                               AS sr_id,
  t4.Name                                                                    AS sr_name,
  t3.site_id                                                                 AS site_id,
  t5.outlet_id                                                               AS outlet_id,
  t5.name                                                                    AS site_name,
  t2.status_id                                                               AS status_id,
  t6.name                                                                    AS status,
  t5.lat                                                                     AS lat,
  t5.lon                                                                     AS lon,
  t3.order_id                                                                AS order_id,
  t3.id                                                                      AS oid,   
  lower(t8.name)                                                             AS collection_type,
  t3.order_date                                                              AS order_date,
  t5.VAT_TRN                                                                 AS site_trn
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_order_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_order_master AS t3 ON t2.order_id = t3.id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
  INNER JOIN tbld_site AS t5 ON t3.site_id = t5.id
  INNER JOIN tbld_lifecycle_status AS t6 ON t3.status_id = t6.id
  LEFT JOIN tbld_site_company_mapping AS t7 ON t3.company_id = t7.company_id AND t3.site_id = t7.site_id
  INNER JOIN tbld_outlet_payment_type AS t8 ON t7.payment_type_id = t8.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (6, 7, 8) and t3.order_type_id=1
UNION ALL 
SELECT
  concat(t3.order_id, t3.status_id, t2.status_id, t1.id, t7.payment_type_id) AS column_id,
  concat(t3.order_id, t3.status_id, t2.status_id, t1.id, t7.payment_type_id) AS token,
  t3.company_id                                                              AS ou_id,
  t1.id                                                                      AS trip_id,
  t3.depot_id                                                                AS depo_id,
  t4.user_name                                                               AS sr_id,
  t4.Name                                                                    AS sr_name,
  t3.site_id                                                                 AS site_id,
  t5.outlet_id                                                               AS outlet_id,
  t5.name                                                                    AS site_name,
  13                                                              AS status_id,
  t6.name                                                                    AS status,
  t5.lat                                                                     AS lat,
  t5.lon                                                                     AS lon,
  t3.order_id                                                                AS order_id,
  t3.id                                                                      AS oid,   
  lower(t8.name)                                                             AS collection_type,
  t3.order_date                                                              AS order_date,
  t5.VAT_TRN                                                                 AS site_trn
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_order_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_order_master AS t3 ON t2.order_id = t3.id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
  INNER JOIN tbld_site AS t5 ON t3.site_id = t5.id
  INNER JOIN tbld_lifecycle_status AS t6 ON t3.status_id = t6.id
  INNER JOIN tbld_site_company_mapping AS t7 ON t3.company_id = t7.company_id AND t3.site_id = t7.site_id 
  INNER JOIN tbld_outlet_payment_type AS t8 ON t7.payment_type_id = t8.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (6, 7, 8) and t3.order_type_id=2 and t7.payment_type_id=2");

        $data6 = DB::select("SELECT
  concat(t4.id, t1.id)                                        AS column_id,
  concat(t4.id, t1.id)                                        AS token,
  t4.id                                                       AS order_line_id,
  t2.trip_id                                                  AS trip_id,
  t3.Order_ID                                                 AS order_id,
  t4.id                                                       AS oid, 
  t4.sku_id                                                   AS product_id,
  t5.name                                                     AS product_name,
  t5.ctn_size                                                 AS ctn_size,
  t4.unit_price * t5.ctn_size                                 AS unit_price,
  t4.qty_order                                                AS confirm_qty,
  if(t3.status_id = 13, t4.qty_delivery, t4.qty_order)        AS delivary_qty,
  0                                                           AS def_discount_confirm,
  0                                                           AS def_discount_delivery,
  0                                                           AS confirm_discount,
  0                                                           AS delivery_discount,
  0                                                           AS promo_discount_confirm,
  0                                                           AS promo_discount_delivery,
  t4.total_order                                              AS total_confirmed_amount,
  if(t3.status_id = 13, t4.total_delivery, t4.total_order) AS total_delivered_amount,
  t4.promo_ref_id                                             AS promo_ref,
  0                                                           AS gst,
  0                                                           AS vat
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_order_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_order_master AS t3 ON t2.order_id = t3.id
  INNER JOIN tblt_order_line AS t4 ON t3.id = t4.so_id
  INNER JOIN tbld_sku AS t5 ON t5.id = t4.sku_id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (6, 7, 8) and t3.order_type_id=1");

        $data7 = DB::select("SELECT
  concat(t3.id, t3.order_id, t3.status_id, t1.id) AS column_id,
  concat(t3.id, t3.order_id, t3.status_id, t1.id) AS token,
  t3.company_id                                   AS ou_id,
  t1.id                                           AS trip_id,
  t3.depot_id                                     AS depo_id,
  t5.user_name                                    AS sr_id,
  t3.order_date                                   AS date,
  t5.name                                         AS sr_name,
  t3.site_id                                      AS site_id,
  t4.name                                         AS site_name,
  t3.status_id                                    AS status_id,
  t6.name                                         AS status,
  t3.order_id                                     AS return_id,
  t3.id                                           AS oid,
  t3.id                                           AS return_order_id,
  t4.VAT_TRN                                      AS vat_trn
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_order_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_order_master AS t3 ON t2.order_id = t3.id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tbld_employee AS t5 ON t3.emp_id = t5.id
  INNER JOIN tbld_lifecycle_status AS t6 ON t3.status_id = t6.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (6, 7, 8) AND t3.order_type_id = 2");

        $data8 = DB::select("SELECT
  concat(t4.id, t1.id)        AS column_id,
  concat(t4.id, t1.id)        AS token,
  t4.id                       AS order_line_id,
  t2.trip_id                  AS trip_id,
  t3.order_id                 AS return_id,
  t4.id                       AS oid,
  t4.sku_id                   AS product_id,
  t5.name                     AS product_name,
  t5.ctn_size                 AS ctn_size,
  t4.unit_price * t5.ctn_size AS unit_price,
  0                           AS retrun_type,
  t4.qty_order                AS quantity_returned,
  if(t3.status_id = 13, t4.qty_delivery,
     t4.qty_order)         AS quantity_delivered,
  t4.total_order              AS total_amount,
  if(t3.status_id = 13, t4.total_delivery,
     t4.total_order)          AS total_amount_delivered,
  0                           AS gst,
  0                           AS vat,
  ''                          AS order_ref_no,
  0                           AS discount_ratio
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_order_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_order_master AS t3 ON t2.order_id = t3.id
  INNER JOIN tblt_order_line AS t4 ON t3.id = t4.so_id
  INNER JOIN tbld_sku AS t5 ON t4.sku_id = t5.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id IN (6, 7, 8) AND t3.order_type_id = 2");


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

    public function deliveredData(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t1.balance_code, t1.status_id) AS column_id,
  concat(t1.balance_code, t1.status_id) AS token,
  t1.company_id                         AS ou_id,
  t3.user_name                          AS sr_id,
  t1.emp_by                             AS emp_code,
  t1.balance_date                       AS date,
  t1.note,
  t1.site_id,
  t1.trip_id,
  t1.balance_code                       AS invoice_code,
  t1.amount                             AS invoice_amount,
  t1.collection_amount,
  t1.balance_head_id                    AS invoice_type,
  t1.cash_type_id                       AS transaction_type,
  t1.status_id,
  1                                     AS is_synced
FROM tblt_site_balance AS t1
  INNER JOIN tblt_trip AS t2 ON t1.trip_id = t2.id
  INNER JOIN tbld_employee as t3 ON t1.emp_by=t3.id
WHERE t2.emp_id = $request->emp_id AND t2.status_id = 8;");
        $data2 = DB::select("SELECT
  t1.column_id,
  t1.column_id       AS token,
  t4.site_id,
  t5.user_name       AS sr_id,
  t1.emp_id,
  t1.company_id      AS ou_id,
  t1.trip_id,
  t1.payment_date    AS date,
  t1.outlet_id,
  t1.amount,
  t1.payment_type_id AS payment_type,
  t1.status_id,
  1                  AS is_synced
FROM `tblt_outlet_collection` AS t1
  INNER JOIN tblt_trip AS t2 ON t1.trip_id = t2.id
  INNER JOIN tblt_collection_invoice_mapping AS t4 ON t1.id = t4.collection_id
  INNER JOIN tbld_employee AS t5 ON t1.emp_id = t5.id
WHERE t2.emp_id = $request->emp_id AND t2.status_id = 8
GROUP BY t1.column_id,t1.emp_id,t1.company_id,t1.trip_id,t1.payment_date, t1.outlet_id,t1.amount,t1.payment_type_id,t1.status_id,t4.site_id,t5.user_name");


        return Array(
            "tblt_driver_collection" => array("data" => $data2, "action" => $request->country_id),
            "tblt_site_invoice" => array("data" => $data1, "action" => $request->country_id),
        );
    }

}