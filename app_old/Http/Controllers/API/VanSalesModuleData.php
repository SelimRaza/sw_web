<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\LoadLine;
use App\BusinessObject\LoadMaster;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
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
            $orderMaster->order_id = "O" . str_pad($employee->user_name, 8, '0', STR_PAD_LEFT) . $orderSequence->year . str_pad($orderSequence->order_count + 1, 6, '0', STR_PAD_LEFT);
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
            $orderMaster->status_id = 13;
            $orderMaster->order_amount = array_sum(array_column($orderLines, 'total_order_amount'));
            $orderMaster->invoice_amount = array_sum(array_column($orderLines, 'net_amount'));
            $orderMaster->created_by = $request->up_emp_id;
            $orderMaster->updated_by = $request->up_emp_id;
            $orderMaster->country_id = $request->country_id;
            $orderMaster->updated_count = 0;
            $orderMaster->save();

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
            $deliverySequenceMapping->o_id = $orderMaster->id;
            $deliverySequenceMapping->s_no = $deliverySequence->order_count;
            $deliverySequenceMapping->order_type_id = 1;
            $deliverySequenceMapping->country_id = $request->country_id;
            $deliverySequenceMapping->created_by = $request->up_emp_id;
            $deliverySequenceMapping->updated_by = $request->up_emp_id;
            $deliverySequenceMapping->updated_count = 0;
            $deliverySequenceMapping->save();
            foreach ($orderLines as $orderLineData) {
                $orderLine = new OrderLine();
                $orderLine->so_id = $orderMaster->id;
                $orderLine->sku_id = $orderLineData->product_id;
                $orderLine->qty_order = $orderLineData->delivary_qty;
                $orderLine->qty_confirm = $orderLineData->delivary_qty;
                $orderLine->qty_delivery = $orderLineData->delivary_qty;
                $orderLine->ctn_size = $orderLineData->ctn_size;
                $orderLine->unit_price = $orderLineData->pcs_price;
                $orderLine->is_order_line = 1;
                $orderLine->promo_ref_id = 0;
                $orderLine->total_order = $orderLineData->total_delivered_amount;
                $orderLine->total_confirm = $orderLineData->total_delivered_amount;
                $orderLine->total_delivery = $orderLineData->total_delivered_amount;
                $orderLine->invoice_amount = $orderLineData->net_amount;
                $orderLine->country_id = $request->country_id;
                $orderLine->created_by = $request->up_emp_id;
                $orderLine->updated_by = $request->up_emp_id;
                $orderLine->updated_count = 0;
                $orderLine->save();
                $tripSku = TripSku::where(['trip_id' => $request->trip_id, 'sku_id' => $orderLineData->product_id])->first();
                $tripSku->delivery_qty = $tripSku->delivery_qty + $orderLineData->delivary_qty;
                $tripSku->updated_by = $request->up_emp_id;
                $tripSku->updated_count = $tripSku->updated_count + 1;
                $tripSku->save();
            }
            $orderSequence->order_count = $orderSequence->order_count + 1;
            $orderSequence->updated_by = $request->up_emp_id;
            $orderSequence->updated_count = $orderSequence->updated_count + 1;
            $orderSequence->save();
            $tripOrder = new TripOrder();
            $tripOrder->from_previous_trip = 0;
            $tripOrder->trip_id = $request->trip_id;
            $tripOrder->order_id = $orderMaster->id;
            $tripOrder->status_id = 13;
            $tripOrder->reason_id = 0;
            $tripOrder->country_id = $request->country_id;
            $tripOrder->created_by = $request->up_emp_id;
            $tripOrder->updated_by = $request->up_emp_id;
            $tripOrder->updated_count = 0;
            $tripOrder->save();
            $orderSyncLog = new OrderSyncLog();
            $orderSyncLog->local_id = $request->order_id;
            $orderSyncLog->order_id = $orderMaster->order_id;
            $orderSyncLog->oid = $orderMaster->id;
            $orderSyncLog->country_id = $request->country_id;
            $orderSyncLog->created_by = $request->up_emp_id;
            $orderSyncLog->updated_by = $request->up_emp_id;
            $orderSyncLog->updated_count = 0;
            $orderSyncLog->save();
            DB::table('tblt_site_balance')->where('balance_code', $request->order_id)->update(['balance_code' => $orderMaster->order_id]);
            DB::table('tblt_collection_invoice_mapping')->where('invoice_code', $request->order_id)->update(['invoice_code' => $orderMaster->order_id]);

            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            //throw $e;
        }
    }

    public function vanGRVSave(Request $request)
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
            $orderMaster->status_id = 13;
            $orderMaster->order_amount = array_sum(array_column($orderLines, 'total_amount_delivered'));
            $orderMaster->invoice_amount = array_sum(array_column($orderLines, 'net_amount'));
            $orderMaster->created_by = $request->up_emp_id;
            $orderMaster->updated_by = $request->up_emp_id;
            $orderMaster->country_id = $request->country_id;
            $orderMaster->updated_count = 0;
            $orderMaster->save();

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
            $deliverySequence->return_count = $deliverySequence->return_count + 1;
            $deliverySequence->updated_by = $request->up_emp_id;
            $deliverySequence->updated_count = $deliverySequence->updated_count + 1;
            $deliverySequence->save();
            $deliverySequenceMapping = new SequenceMappingInvoice();
            $deliverySequenceMapping->company_id = $request->ou_id;
            $deliverySequenceMapping->year = date('Y');
            $deliverySequenceMapping->o_id = $orderMaster->id;
            $deliverySequenceMapping->s_no = $deliverySequence->order_count;
            $deliverySequenceMapping->order_type_id = 1;
            $deliverySequenceMapping->country_id = $request->country_id;
            $deliverySequenceMapping->created_by = $request->up_emp_id;
            $deliverySequenceMapping->updated_by = $request->up_emp_id;
            $deliverySequenceMapping->updated_count = 0;
            $deliverySequenceMapping->save();
            foreach ($orderLines as $orderLineData) {
                $orderLine = new OrderLine();
                $orderLine->so_id = $orderMaster->id;
                $orderLine->sku_id = $orderLineData->product_id;
                $orderLine->qty_order = $orderLineData->quantity_delivered;
                $orderLine->qty_confirm = $orderLineData->quantity_delivered;
                $orderLine->qty_delivery = $orderLineData->quantity_delivered;
                $orderLine->ctn_size = $orderLineData->ctn_size;
                $orderLine->unit_price = $orderLineData->ctn_price / $orderLineData->ctn_size;
                $orderLine->is_order_line = 1;
                $orderLine->promo_ref_id = 0;
                $orderLine->total_order = $orderLineData->total_amount_delivered;
                $orderLine->total_confirm = $orderLineData->total_amount_delivered;
                $orderLine->total_delivery = $orderLineData->total_amount_delivered;
                $orderLine->invoice_amount = $orderLineData->net_amount;
                $orderLine->return_reason_id = $orderLineData->reason_id;
                $orderLine->country_id = $request->country_id;
                $orderLine->created_by = $request->up_emp_id;
                $orderLine->updated_by = $request->up_emp_id;
                $orderLine->updated_count = 0;
                $orderLine->save();
                $tripGRVSku = TripGrvSku::where(['trip_id' => $request->trip_id, 'sku_id' => $orderLineData->product_id])->first();
                if ($tripGRVSku == null) {
                    $tripGrvSku = new TripGrvSku();
                    $tripGrvSku->trip_id = $request->trip_id;
                    $tripGrvSku->sku_id = $orderLineData->product_id;
                    $tripGrvSku->issued_qty = 0;
                    $tripGrvSku->confirm_qty = $orderLineData->quantity_delivered;
                    $tripGrvSku->delivery_qty = 0;
                    $tripGrvSku->logistic_qty = 0;
                    $tripGrvSku->country_id = $request->country_id;
                    $tripGrvSku->created_by = $request->up_emp_id;
                    $tripGrvSku->updated_by = $request->up_emp_id;
                    $tripGrvSku->updated_count = 0;
                    $tripGrvSku->save();
                } else {
                    $tripGRVSku->delivery_qty = $tripGRVSku->delivery_qty + $orderLineData->quantity_delivered;
                    $tripGRVSku->updated_by = $request->up_emp_id;
                    $tripGRVSku->updated_count = $tripGRVSku->updated_count + 1;
                    $tripGRVSku->save();
                }

            }
            $orderSequence->return_count = $orderSequence->return_count + 1;
            $orderSequence->updated_by = $request->up_emp_id;
            $orderSequence->updated_count = $orderSequence->updated_count + 1;
            $orderSequence->save();
            $tripOrder = new TripOrder();
            $tripOrder->from_previous_trip = 0;
            $tripOrder->trip_id = $request->trip_id;
            $tripOrder->order_id = $orderMaster->id;
            $tripOrder->status_id = 13;
            $tripOrder->reason_id = 0;
            $tripOrder->country_id = $request->country_id;
            $tripOrder->created_by = $request->up_emp_id;
            $tripOrder->updated_by = $request->up_emp_id;
            $tripOrder->updated_count = 0;
            $tripOrder->save();
            $orderSyncLog = new OrderSyncLog();
            $orderSyncLog->local_id = $request->return_id;
            $orderSyncLog->order_id = $orderMaster->order_id;
            $orderSyncLog->oid = $orderMaster->id;
            $orderSyncLog->country_id = $request->country_id;
            $orderSyncLog->created_by = $request->up_emp_id;
            $orderSyncLog->updated_by = $request->up_emp_id;
            $orderSyncLog->updated_count = 0;
            $orderSyncLog->save();
            DB::table('tblt_site_balance')->where('balance_code', $request->return_id)->update(['balance_code' => $orderMaster->order_id]);
            DB::table('tblt_collection_invoice_mapping')->where('invoice_code', $request->return_id)->update(['invoice_code' => $orderMaster->order_id]);

            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            //throw $e;
        }
    }

    public function vanLoadSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataLines = json_decode($request->line_data);
            if ($request->status_id == 1) {
                $loadMaster = new LoadMaster();
                $loadMaster->load_code = $request->order_id;
                $loadMaster->request_date = $request->order_date;
                $loadMaster->verify_date = $request->order_date;
                $loadMaster->load_date = $request->order_date;
                $loadMaster->emp_id = $request->emp_id;
                $loadMaster->trip_id = $request->trip_id;
                $loadMaster->depot_id = $request->depo_id;
                $loadMaster->load_type_id = 1;
                $loadMaster->grv_type = 0;
                $loadMaster->status_id = 1;
                $loadMaster->country_id = $request->country_id;
                $loadMaster->created_by = $request->up_emp_id;
                $loadMaster->updated_by = $request->up_emp_id;
                $loadMaster->updated_count = 0;
                $loadMaster->save();
                foreach ($dataLines as $lineData) {
                    $loadLine = new LoadLine();
                    $loadLine->load_id = $loadMaster->id;
                    $loadLine->sku_id = $lineData->product_id;
                    $loadLine->request_qty = $lineData->order_qty;
                    $loadLine->confirm_qty = 0;
                    $loadLine->load_qty = 0;
                    $loadLine->unit_price = $lineData->unit_price;
                    $loadLine->country_id = $request->country_id;
                    $loadLine->created_by = $request->up_emp_id;
                    $loadLine->updated_by = $request->up_emp_id;
                    $loadLine->updated_count = 0;
                    $loadLine->save();
                }
            } elseif ($request->status_id == 13) {
                $loadMaster = LoadMaster::findorfail($request->oid);
                $loadMaster->status_id = 13;
                $loadMaster->updated_by = $request->up_emp_id;
                $loadMaster->updated_count = $loadMaster->updated_count + 1;
                $loadMaster->save();
                foreach ($dataLines as $lineData) {
                    $tripSku = TripSku::where(['trip_id' => $request->trip_id, 'sku_id' => $lineData->product_id])->first();
                    if ($tripSku == null) {
                        $tripSku = new TripSku();
                        $tripSku->trip_id = $request->trip_id;
                        $tripSku->sku_id = $lineData->product_id;
                        $tripSku->issued_qty = 0;
                        $tripSku->confirm_qty = $lineData->delivary_qty;
                        $tripSku->delivery_qty = 0;
                        $tripSku->logistic_qty = 0;
                        $tripSku->country_id = $request->country_id;
                        $tripSku->created_by = $request->up_emp_id;
                        $tripSku->updated_by = $request->up_emp_id;
                        $tripSku->updated_count = 0;
                        $tripSku->save();
                    } else {
                        $tripSku->confirm_qty = $tripSku->confirm_qty + $lineData->delivary_qty;
                        $tripSku->updated_by = $request->up_emp_id;
                        $tripSku->updated_count = $tripSku->updated_count + 1;
                        $tripSku->save();
                    }
                }
            }
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }


    public function vanStockMoveSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataLines = json_decode($request->line_data);
            if ($request->status_id == 18) {
                $loadMaster = new LoadMaster();
                $loadMaster->load_code = $request->return_id;
                $loadMaster->request_date = $request->date;
                $loadMaster->verify_date = $request->date;
                $loadMaster->load_date = $request->date;
                $loadMaster->emp_id = $request->emp_id;
                $loadMaster->trip_id = $request->trip_id;
                $loadMaster->depot_id = $request->depo_id;
                $loadMaster->load_type_id = $request->stock_type == 1 ? 2 : 3;
                $loadMaster->grv_type = $request->stock_type == 1 ? 0 : $request->return_type;
                $loadMaster->status_id = 1;
                $loadMaster->country_id = $request->country_id;
                $loadMaster->created_by = $request->up_emp_id;
                $loadMaster->updated_by = $request->up_emp_id;
                $loadMaster->updated_count = 0;
                $loadMaster->save();
                foreach ($dataLines as $lineData) {
                    $loadLine = new LoadLine();
                    $loadLine->load_id = $loadMaster->id;
                    $loadLine->sku_id = $lineData->product_id;
                    $loadLine->request_qty = $lineData->quantity_delivered;
                    $loadLine->confirm_qty = 0;
                    $loadLine->load_qty = 0;
                    $loadLine->unit_price = $lineData->unit_price;
                    $loadLine->country_id = $request->country_id;
                    $loadLine->created_by = $request->up_emp_id;
                    $loadLine->updated_by = $request->up_emp_id;
                    $loadLine->updated_count = 0;
                    $loadLine->save();
                }
            } elseif ($request->status_id == 13) {
                $loadMaster1 = new LoadMaster();
                $loadMaster1->load_code = $request->return_id;
                $loadMaster1->request_date = $request->date;
                $loadMaster1->verify_date = $request->date;
                $loadMaster1->load_date = $request->date;
                $loadMaster1->emp_id = $request->emp_id;
                $loadMaster1->trip_id = $request->trip_id;
                $loadMaster1->depot_id = $request->depo_id;
                $loadMaster1->load_type_id = 3;
                $loadMaster1->grv_type = $request->return_type;
                $loadMaster1->status_id = 13;
                $loadMaster1->country_id = $request->country_id;
                $loadMaster1->created_by = $request->up_emp_id;
                $loadMaster1->updated_by = $request->up_emp_id;
                $loadMaster1->updated_count = 0;
                $loadMaster1->save();
                foreach ($dataLines as $lineData) {
                    $loadLine = new LoadLine();
                    $loadLine->load_id = $loadMaster1->id;
                    $loadLine->sku_id = $lineData->product_id;
                    $loadLine->request_qty = $lineData->quantity_delivered;
                    $loadLine->confirm_qty = $lineData->quantity_delivered;
                    $loadLine->load_qty = $lineData->quantity_delivered;
                    $loadLine->unit_price = $lineData->unit_price;
                    $loadLine->country_id = $request->country_id;
                    $loadLine->created_by = $request->up_emp_id;
                    $loadLine->updated_by = $request->up_emp_id;
                    $loadLine->updated_count = 0;
                    $loadLine->save();
                    $tripGrvSku = TripGrvSku::where(['trip_id' => $request->trip_id, 'sku_id' => $lineData->product_id])->first();
                    if ($tripGrvSku == null) {
                        $tripGrvSku = new TripSku();
                        $tripGrvSku->trip_id = $request->trip_id;
                        $tripGrvSku->sku_id = $lineData->product_id;
                        $tripGrvSku->issued_qty = 0;
                        $tripGrvSku->confirm_qty = 0;
                        $tripGrvSku->delivery_qty = $lineData->quantity_delivered;
                        $tripGrvSku->logistic_qty = 0;
                        $tripGrvSku->country_id = $request->country_id;
                        $tripGrvSku->created_by = $request->up_emp_id;
                        $tripGrvSku->updated_by = $request->up_emp_id;
                        $tripGrvSku->updated_count = 0;
                        $tripGrvSku->save();
                    } else {
                        $tripGrvSku->delivery_qty = $tripGrvSku->delivery_qty + $lineData->quantity_delivered;
                        $tripGrvSku->updated_by = $request->up_emp_id;
                        $tripGrvSku->updated_count = $tripGrvSku->updated_count + 1;
                        $tripGrvSku->save();
                    }
                }

                $loadMaster2 = new LoadMaster();
                $loadMaster2->load_code = $request->return_id;
                $loadMaster2->request_date = $request->date;
                $loadMaster2->verify_date = $request->date;
                $loadMaster2->load_date = $request->date;
                $loadMaster2->emp_id = $request->emp_id;
                $loadMaster2->trip_id = $request->trip_id;
                $loadMaster2->depot_id = $request->depo_id;
                $loadMaster2->load_type_id = 1;
                $loadMaster2->grv_type = 0;
                $loadMaster2->status_id = 13;
                $loadMaster2->country_id = $request->country_id;
                $loadMaster2->created_by = $request->up_emp_id;
                $loadMaster2->updated_by = $request->up_emp_id;
                $loadMaster2->updated_count = 0;
                $loadMaster2->save();
                foreach ($dataLines as $lineData) {
                    $loadLine = new LoadLine();
                    $loadLine->load_id = $loadMaster2->id;
                    $loadLine->sku_id = $lineData->product_id;
                    $loadLine->request_qty = $lineData->quantity_delivered;
                    $loadLine->confirm_qty = $lineData->quantity_delivered;
                    $loadLine->load_qty = $lineData->quantity_delivered;
                    $loadLine->unit_price = $lineData->unit_price;
                    $loadLine->country_id = $request->country_id;
                    $loadLine->created_by = $request->up_emp_id;
                    $loadLine->updated_by = $request->up_emp_id;
                    $loadLine->updated_count = 0;
                    $loadLine->save();
                    $tripSku = TripSku::where(['trip_id' => $request->trip_id, 'sku_id' => $lineData->product_id])->first();
                    if ($tripSku == null) {
                        $tripSku = new TripSku();
                        $tripSku->trip_id = $request->trip_id;
                        $tripSku->sku_id = $lineData->product_id;
                        $tripSku->issued_qty = 0;
                        $tripSku->confirm_qty = $lineData->quantity_delivered;
                        $tripSku->delivery_qty = 0;
                        $tripSku->logistic_qty = 0;
                        $tripSku->country_id = $request->country_id;
                        $tripSku->created_by = $request->up_emp_id;
                        $tripSku->updated_by = $request->up_emp_id;
                        $tripSku->updated_count = 0;
                        $tripSku->save();
                    } else {
                        $tripSku->confirm_qty = $tripSku->confirm_qty + $lineData->quantity_delivered;
                        $tripSku->updated_by = $request->up_emp_id;
                        $tripSku->updated_count = $tripSku->updated_count + 1;
                        $tripSku->save();
                    }
                }
            }
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
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
        $data1 = DB::select("SELECT
  concat(t1.id,t1.status_id,t1.depot_id)        AS column_id,
  concat(t1.id,t1.status_id,t1.depot_id)        AS token,
  t1.id        AS trip_id,
  t2.name      AS driver_name,
  t1.trip_date AS date,
  t1.id        AS trip_code,
  t2.name      AS vehicle_name,
  t1.status_id,
  t1.depot_id as depot_id,
  t7.company_id
FROM `tblt_trip` AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_lifecycle_status AS t6 ON t1.status_id = t6.id
  INNER JOIN tbld_distribution_point as t7 ON t1.depot_id=t7.id
WHERE t1.status_id =8 AND t1.emp_id = $request->emp_id and t1.trip_type_id=2");

        $data2 = DB::select("SELECT
  concat(t2.company_id, t1.depot_id, t3.sku_id, t3.sales_price, t3.grv_price, t4.ctn_size,
         t2.price_list_id)     AS column_id,
  concat(t2.company_id, t1.depot_id, t3.sku_id, t3.sales_price, t3.grv_price, t4.ctn_size,
         t2.price_list_id)     AS token,
  t2.company_id                AS ou_id,
  t1.depot_id                  AS depot_id,
  0                            AS gst,
  0                            AS vat,
  t3.sku_id                    AS sku_id,
  t4.name                      AS sku_name,
  t3.sales_price * t4.ctn_size AS ctn_price,
  t3.sales_price               AS unit_price,
  t3.grv_price * t4.ctn_size   as grv_price,
  t3.grv_price                 AS unit_grv_price,
  t4.ctn_size                  AS ctn_size,
  t5.id                        AS sub_category_id,
  t5.name                      AS sub_category,
  t2.price_list_id             AS price_id,
  t4.ctn_size                  AS default_qty,
  t4.ctn_size*5                AS max_qty,
  1                            AS min_qty,
  1                            AS loadable
FROM tblt_trip AS t1
  INNER JOIN tblt_employee_own_site_mapping AS t2 ON t1.emp_id = t2.emp_id
  INNER JOIN tbld_price_list_item_mapping AS t3 ON t2.price_list_id = t3.price_list_id
  INNER JOIN tbld_sku AS t4 ON t3.sku_id = t4.id
  INNER JOIN tbld_sub_category AS t5 ON t4.sub_category_id = t5.id
  INNER JOIN tbld_sales_group_employee AS t6 ON t1.emp_id=t6.emp_id
  INNER JOIN tbld_sales_gorup_sku as t7 ON t6.sales_group_id=t7.sales_group_id and t3.sku_id=t7.sku_id
WHERE t4.status_id = 1 AND t1.emp_id = $request->emp_id AND t1.status_id = 8");

        $data3 = DB::select("SELECT
  concat(t1.emp_id, t2.site_id, t2.stock_limit, t2.personal_limit) AS column_id,
  concat(t1.emp_id, t2.site_id, t2.stock_limit, t2.personal_limit) AS token,
  t1.emp_id                                                        AS emp_id,
  t2.site_id                                                       AS site_id,
  t2.stock_limit                                                   AS credit_limit,
  0                                                                AS order_amount,
  t2.personal_limit                                                AS van_credit_limit,
  0                                                                AS due_amount
FROM tblt_trip AS t1
  INNER JOIN tblt_employee_own_site_mapping AS t2 ON t1.emp_id = t2.emp_id
WHERE t1.trip_type_id = 2 AND t1.emp_id =  $request->emp_id and t1.status_id=8");

        $data4 = DB::select("SELECT
  concat(t2.id, t1.Day) AS column_id,
  concat(t2.id, t1.Day) AS token,
  t2.name               AS route_name,
  t2.code               AS route_code,
  t2.id                 AS route_id,
  t1.Day                AS day_name
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
WHERE t1.emp_id =  $request->emp_id ;");

        $data5 = DB::select("SELECT
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id, !isnull(t5.site_id),
         if(t5.is_productive > 0, 1, 0)) AS column_id,
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id, !isnull(t5.site_id),
         if(t5.is_productive > 0, 1, 0)) AS token,
  t3.site_id                             AS site_id,
  t1.route_id                            AS route_id,
  t4.outlet_id                           AS outlet_id,
  t4.name                                AS site_name,
  t4.owner_name                          AS owner_name,
  t4.mobile_1                            AS owner_mobile,
  t4.address                             AS address,
  'cash'                                 AS pay_mode,
   !isnull(t5.site_id)                   AS visited,
  0                                      AS order_amount,
  t4.house_no                            AS house_no,
  t4.vat_trn                             AS site_trn,
  0                                      AS credit_limit,
  0                                      AS due_amount,
  0                                      AS pre_order_amount,
  0                                      AS overdue,
  0                                      AS must_sell_id,
  curdate()                              AS date,
  t4.lat,
  t4.lon,
  t4.is_verified,
  if(t5.is_productive > 0, 1, 0)                    AS is_productive
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_route_site_mapping AS t3 ON t2.id = t3.route_id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tblt_site_visited AS t5 ON t3.site_id = t5.site_id AND t5.date = curdate()
WHERE t1.emp_id =  $request->emp_id AND t1.day = DAYNAME(curdate()) AND t4.status_id = 1
UNION ALL
SELECT
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id, !isnull(t4.site_id),
         if(t4.is_productive > 0, 1, 0)) AS column_id,
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id, !isnull(t4.site_id),
         if(t4.is_productive > 0, 1, 0)) AS token,
  t1.site_id                             AS site_id,
  t1.route_id                            AS route_id,
  t3.outlet_id                           AS outlet_id,
  t3.name                                AS site_name,
  t3.owner_name                          AS owner_name,
  t3.mobile_1                            AS owner_mobile,
  t3.address                             AS address,
  'cash'                                 AS pay_mode,
   !isnull(t4.site_id)                   AS visited,
  0                                      AS order_amount,
  t3.house_no                            AS house_no,
  t3.vat_trn                             AS site_trn,
  0                                      AS credit_limit,
  0                                      AS due_amount,
  0                                      AS pre_order_amount,
  0                                      AS overdue,
  0                                      AS must_sell_id,
  curdate()                              AS date,
  t3.lat,
  t3.lon,
  t3.is_verified,
   if(t4.is_productive > 0, 1, 0)                    AS is_productive
FROM tblt_site_visit_permission AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
  LEFT JOIN tblt_site_visited AS t4 ON t3.id = t4.site_id AND t4.date = curdate()
WHERE t1.emp_id =  $request->emp_id AND t3.status_id = 1 AND t1.date = curdate();");

        $data6 = DB::select("SELECT
  concat(t2.trip_id, t2.sku_id, t2.confirm_qty, t2.delivery_qty) AS column_id,
  concat(t2.trip_id, t2.sku_id, t2.confirm_qty, t2.delivery_qty) AS token,
  t2.trip_id,
  t2.sku_id                                                      AS sku_id,
  t3.ctn_size                                                    AS ctn_size,
  t3.name                                                        AS sku_name,
  t3.sub_category_id                                             AS sub_category_id,
  t7.name                                                        AS sub_category_name,
  t2.confirm_qty,
  t2.delivery_qty                                                AS delivered_qty,
  t6.sales_price                                                 AS ctn_price
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_sku_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
  INNER JOIN tbld_employee AS t4 ON t1.emp_id = t4.id
  INNER JOIN tblt_employee_own_site_mapping AS t5 ON t1.emp_id = t5.emp_id
  INNER JOIN tbld_price_list_item_mapping AS t6 ON t5.price_list_id = t6.price_list_id AND t2.sku_id = t6.sku_id
  INNER JOIN tbld_sub_category AS t7 ON t3.sub_category_id = t7.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id = 8");

        $data7 = DB::select("SELECT
  concat(id, t1.name, t1.code, t1.status_id) AS column_id,
  concat(id, t1.name, t1.code, t1.status_id) AS token,
  t1.id                                      AS reason_id,
  t1.name                                    AS reason_name
FROM tbld_return_reason AS t1
WHERE t1.country_id =$request->country_id");


        $data8 = DB::select("SELECT
  concat(t2.trip_id, t2.sku_id, t2.confirm_qty, t2.delivery_qty) AS column_id,
  concat(t2.trip_id, t2.sku_id, t2.confirm_qty, t2.delivery_qty) AS token,
  t2.trip_id,
  t2.sku_id                                                      AS sku_id,
  t3.ctn_size                                                    AS ctn_size,
  t3.name                                                        AS sku_name,
  t3.sub_category_id                                             AS sub_category_id,
  t7.name                                                        AS sub_category_name,
  t2.confirm_qty,
  t2.delivery_qty                                                AS delivered_qty,
  t6.sales_price                                                 AS ctn_price
FROM tblt_trip AS t1
  INNER JOIN tblt_trip_grv_sku_mapping AS t2 ON t1.id = t2.trip_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
  INNER JOIN tbld_employee AS t4 ON t1.emp_id = t4.id
  INNER JOIN tblt_employee_own_site_mapping AS t5 ON t1.emp_id = t5.emp_id
  INNER JOIN tbld_price_list_item_mapping AS t6 ON t5.price_list_id = t6.price_list_id AND t2.sku_id = t6.sku_id
  INNER JOIN tbld_sub_category AS t7 ON t3.sub_category_id = t7.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id = 8");
        return Array(
            "tblt_van_trip" => array("data" => $data1, "action" => $request->country_id),
            "tblt_van_product_details" => array("data" => $data2, "action" => $request->country_id),
            "tbld_van_credit_limit" => array("data" => $data3, "action" => $request->country_id),
            "tblt_van_route" => array("data" => $data4, "action" => $request->country_id),
            "tblt_van_route_site" => array("data" => $data5, "action" => $request->country_id),
            "tblt_van_stock" => array("data" => $data6, "action" => $request->country_id),
            "tblt_return_reason" => array("data" => $data7, "action" => $request->country_id),
            "tblt_van_grv_stock" => array("data" => $data8, "action" => $request->country_id)
        );
    }

    public function vanSalesDataDown(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t2.load_code, t2.status_id ) AS column_id,
  concat(t2.load_code, t2.status_id ) AS token,
  0                                    AS ou_id,
  t1.id                                AS trip_id,
  t1.depot_id                          AS depo_id,
  t1.emp_id                            AS sr_id,
  ''                                   AS sr_name,
  ''                                   AS site_id,
  ''                                   AS outlet_id,
  ''                                   AS site_name,
  t2.status_id                         AS status_id,
  t4.name                              AS status,
  0                                    AS lat,
  0                                    AS lon,
  t2.load_code                         AS order_id,
 t2.id                                 as oid,
  ''                                   AS collection_type,
  t2.request_date                      AS order_date,
  1 as is_synced
FROM tblt_trip AS t1
  INNER JOIN tblt_load_master AS t2 ON t1.id = t2.trip_id
  INNER JOIN tbld_lifecycle_status AS t4 ON t2.status_id = t4.id
WHERE t1.emp_id = $request->emp_id AND t1.status_id = 8");

        $data2 = DB::select("SELECT
  concat(t2.load_code, t2.status_id,t3.sku_id,t5.code,t3.confirm_qty,t3.load_qty) AS column_id,
   concat(t2.load_code, t2.status_id,t3.sku_id,t5.code,t3.confirm_qty,t3.load_qty) AS token,
  t2.trip_id                                                                             AS trip_id,
  t2.load_code                                                                           AS order_id,
  t3.id                                                                                  as oid,
  t3.sku_id                                                                              AS product_id,
  t5.name                                                                                AS product_name,
  t5.ctn_size                                                                            AS ctn_size,
  t3.unit_price                                                                          AS unit_price,
  t3.confirm_qty,
  if(t3.load_qty > 0, t3.load_qty, t3.confirm_qty)                                 AS delivary_qty,
  t3.confirm_qty * t3.unit_price                                                         AS total_confirmed_amount,
  if(t3.load_qty > 0, t3.load_qty * t3.unit_price, t3.confirm_qty * t3.unit_price) AS total_delivered_amount,
  ''                                                                                     AS promo_ref,
  0                                                                                      AS gst,
  1 as is_synced
FROM tblt_trip AS t1
  INNER JOIN tblt_load_master AS t2 ON t1.id = t2.trip_id
  INNER JOIN tblt_load_line AS t3 ON t2.id = t3.load_id
  INNER JOIN tbld_lifecycle_status AS t4 ON t2.status_id = t4.id
  INNER JOIN tbld_sku AS t5 ON t3.sku_id = t5.id
WHERE t1.emp_id =  $request->emp_id AND t1.status_id = 8");
        return Array(
            "tblt_van_load_sales_order" => array("data" => $data1, "action" => $request->country_id),
            "tblt_van_load_sales_order_line" => array("data" => $data2, "action" => $request->country_id),
        );
    }

}