<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Employee;
use App\MasterData\Site;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreSalesData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function saveOrder(Request $request)
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
            $orderMaster->order_amount =$order_amount ;
            $orderMaster->invoice_amount = 0;
            $orderMaster->created_by = $request->up_emp_id;
            $orderMaster->updated_by = $request->up_emp_id;
            $orderMaster->country_id = $request->country_id;
            $orderMaster->updated_count = 0;
            $orderMaster->save();
            if (($request->status_id == 14 || $request->status_id == 15 || $request->status_id==16) && $employee->address != '') {
                $orderSite = Site::findorfail($request->site_id);
                $status = LifeCycleStatus::findorfail($request->status_id);
                $email = new Auto();
                $email->emp_id = $request->emp_id;
                $email->to_mail = $employee->address;
                if ($employee->email_cc!=''){
                    $email->cc_mail = $employee->email_cc.',c7f4b922.prangroup.com@apac.teams.ms';
                }else{
                    $email->cc_mail = 'c7f4b922.prangroup.com@apac.teams.ms';
                }

                $email->title = $orderSite->id.' - '.$orderSite->name . " LPO:" . $order_id .' amount:'.$order_amount. " ".$status->name;
                $email->text = ' Blocked : '.$orderSite->id.' - '.$orderSite->name.' Ord# ' . $order_id  .' amount:'. $order_amount . ' Reason: '.$status->name;
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


    public function preSalesData(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t2.id, t1.rpln_day, t2.rout_name, t2.rout_code) AS column_id,
  t2.rout_name                                           AS route_name,
  t2.rout_code                                           AS route_code,
  t2.id                                                  AS route_id,
  t1.rpln_day                                            AS day_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
WHERE t1.aemp_id = $request->emp_id
GROUP BY t1.id, t2.rout_name, t2.rout_code, t2.id, t1.rpln_day");

        $data2 = DB::select("SELECT
  concat(t1.id, t3.id, t3.name, t1.company_id, t2.name) AS column_id,
  concat(t1.id, t3.id, t3.name, t1.company_id, t2.name) AS token,
  t3.id                                                 AS depot_id,
  t3.name                                               AS depot_name,
  t3.id                                                 AS dsp_id,
  t1.company_id                                         AS ou_id,
  concat(t3.name, '(', t2.name, ')')                    AS dsp_name,
  1                                                     AS visibility
FROM tbld_company_employee AS t1 
INNER JOIN tbld_company AS t2 ON t1.company_id = t2.id
INNER JOIN tbld_distribution_point AS t3 ON t2.id = t3.company_id
WHERE t1.emp_id =  $request->emp_id AND t1.country_id = $request->country_id");


        $data4 = DB::select("SELECT
    concat(t6.price_list_id, t7.sku_id, t8.name, t8.sub_category_id, t9.name, t7.sales_price, t7.grv_price, t7.mrp_price,
         t8.ctn_size, t8.status_id) AS column_id,
  concat(t6.price_list_id, t7.sku_id, t8.name, t8.sub_category_id, t9.name, t7.sales_price, t7.grv_price, t7.mrp_price,
         t8.ctn_size, t8.status_id) AS token,
  t6.price_list_id                  AS price_id,
  t12.name                          AS price_name,
  t7.sku_id,
  t8.name                           AS sku_name,
  t8.code                           AS sku_code,
  t8.sub_category_id,
  t9.name                           AS sub_category_name,
  t7.sales_price* t8.ctn_size                    AS ctn_price,
  t7.grv_price* t8.ctn_size                       AS grv_ctn_price,
  t7.mrp_price* t8.ctn_size as mrp_price ,
  t8.ctn_size,
  t8.status_id,
  0                                 AS out_of_stock,
  0                                 AS vat,
  0                                 AS gst
FROM tbld_pjp AS t1
  INNER JOIN tbld_company_employee AS t2 ON t1.emp_id = t2.emp_id
  INNER JOIN tbld_route_site_mapping AS t3 ON t1.route_id = t3.route_id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tblt_site_visited AS t5 ON t3.site_id = t5.site_id AND t5.date = curdate()
  INNER JOIN tbld_site_company_mapping AS t6 ON t3.site_id = t6.site_id AND t2.company_id = t6.company_id
  INNER JOIN tbld_price_list_item_mapping AS t7 ON t6.price_list_id = t7.price_list_id
  INNER JOIN tbld_sku AS t8 ON t7.sku_id = t8.id
  INNER JOIN tbld_sub_category AS t9 ON t8.sub_category_id = t9.id
  INNER JOIN tbld_sales_group_employee as t10 ON t1.emp_id=t10.emp_id
  INNER JOIN tbld_sales_gorup_sku as t11 ON t10.sales_group_id=t11.sales_group_id and t7.sku_id=t11.sku_id
  INNER JOIN tbld_price_list as t12 ON t6.price_list_id=t12.id
WHERE t1.emp_id = $request->emp_id AND t1.day = DAYNAME(curdate()) AND t4.status_id = 1 AND t4.is_verified = 1 and t1.country_id=$request->country_id
GROUP BY t6.price_list_id, t7.sku_id, t8.name, t8.sub_category_id, t9.name, t7.sales_price, t7.grv_price, t7.mrp_price,
  t8.ctn_size, t8.status_id,t12.name,t8.code");

        $data5 = DB::select("SELECT
  concat(id, t1.name, t1.code, t1.status_id) AS column_id,
  concat(id, t1.name, t1.code, t1.status_id) AS token,
  t1.id                                      AS reason_id,
  t1.name                                    AS reason_name
FROM tbld_return_reason AS t1
WHERE t1.country_id =$request->country_id");

        return Array(
            "tblt_pre_sales_route" => array("data" => $data1, "action" => $request->country_id),
            "tblt_depot_sub_depot" => array("data" => $data2, "action" => $request->country_id),
            "tblt_pre_price_list" => array("data" => $data4, "action" => $request->country_id),
            "tblt_return_reason" => array("data" => $data5, "action" => $request->country_id)
        );
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