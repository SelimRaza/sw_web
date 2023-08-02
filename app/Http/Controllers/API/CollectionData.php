<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\Note;
use App\BusinessObject\NoteComment;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\OutletCollection;
use App\BusinessObject\OutletCollectionBalanceMapping;
use App\BusinessObject\OutletCollectionMapping;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SiteBalance;
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
use App\MasterData\Employee;
use App\MasterData\Outlet;
use App\MasterData\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function invoiceSave(Request $request)
    {
        // DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $dataSiteBalance = new SiteBalance();
            $dataSiteBalance->company_id = $request->ou_id;
            $dataSiteBalance->balance_code = $request->invoice_code;
            $dataSiteBalance->local_code = $request->invoice_code;
            $dataSiteBalance->balance_date = $request->date;
            $dataSiteBalance->emp_by = $request->emp_code;
            $dataSiteBalance->note = '';
            $dataSiteBalance->site_id = $request->site_id;
            $dataSiteBalance->trip_id = $request->trip_id;
            $dataSiteBalance->amount = $request->invoice_amount;
            $dataSiteBalance->collection_amount = $request->collection_amount;
            $dataSiteBalance->balance_head_id = $request->invoice_type;
            $dataSiteBalance->cash_type_id = $request->transaction_type;
            $dataSiteBalance->round_amount = $request->round_amount;
            $dataSiteBalance->status_id = $request->status_id;
            $dataSiteBalance->country_id = $request->country_id;
            $dataSiteBalance->created_by = $request->up_emp_id;
            $dataSiteBalance->updated_by = $request->up_emp_id;
            $dataSiteBalance->updated_count = 0;
            $dataSiteBalance->save();
            $orderSyncLog = OrderSyncLog::where(['local_id' => $request->invoice_code])->first();
            if ($orderSyncLog != null) {
                DB::table('tblt_site_balance')->where('balance_code', $request->invoice_code)->update(['balance_code' => $orderSyncLog->order_id]);
                DB::table('tblt_collection_invoice_mapping')->where('invoice_code', $request->invoice_code)->update(['invoice_code' => $orderSyncLog->order_id]);
            }

            DB::commit();
            return array('column_id' => $request->id);

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function dmCollectionSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderSequence = OrderSequence::where(['emp_id' => $request->emp_id, 'year' => date('Y')])->first();
            $dataCollectionLines = json_decode($request->line_data);
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
            $dataOutletCollection = new OutletCollection();
            $dataOutletCollection->collection_code = "C" . str_pad($employee->user_name, 8, '0', STR_PAD_LEFT) . $orderSequence->year . str_pad($orderSequence->collection_count + 1, 6, '0', STR_PAD_LEFT);;
            $dataOutletCollection->column_id = $request->column_id;
            $dataOutletCollection->emp_id = $request->emp_id;
            $dataOutletCollection->company_id = $request->ou_id;
            $dataOutletCollection->trip_id = $request->trip_id;
            $dataOutletCollection->payment_date = $request->date;
            $dataOutletCollection->created_date = $request->date;
            $dataOutletCollection->note = '';
            $dataOutletCollection->outlet_id = $request->outlet_id;
            $dataOutletCollection->amount = $request->amount;
            $dataOutletCollection->allocated_amount = $request->amount;
            $dataOutletCollection->payment_type_id = $request->payment_type;
            $dataOutletCollection->collection_type_id = 1;
            $dataOutletCollection->cheque_no = '';
            $dataOutletCollection->cheque_date = date('Y-m-d');
            $dataOutletCollection->bank_id = 0;
            $dataOutletCollection->note = '';
            $dataOutletCollection->verified_by = 0;
            $dataOutletCollection->status_id = $request->status_id;
            $dataOutletCollection->country_id = $request->country_id;
            $dataOutletCollection->created_by = $request->up_emp_id;
            $dataOutletCollection->updated_by = $request->up_emp_id;
            $dataOutletCollection->updated_count = 0;
            $dataOutletCollection->save();
            foreach ($dataCollectionLines as $dataCollectionLine) {
                $outletCollectionMapping = new OutletCollectionMapping();
                $outletCollectionMapping->collection_id = $dataOutletCollection->id;
                $outletCollectionMapping->site_id = $dataCollectionLine->site_id;
                $outletCollectionMapping->invoice_code = $dataCollectionLine->invoice_code;
                $outletCollectionMapping->local_invoice_code = $dataCollectionLine->invoice_code;
                $outletCollectionMapping->amount = $dataCollectionLine->payment_amount;
                $outletCollectionMapping->country_id = $request->country_id;
                $outletCollectionMapping->created_by = $request->up_emp_id;
                $outletCollectionMapping->updated_by = $request->up_emp_id;
                $outletCollectionMapping->updated_count = 0;
                $outletCollectionMapping->save();
                DB::table('tblt_site_balance')->where(['local_code' => $dataCollectionLine->invoice_code])->update(['collection_amount' => $dataCollectionLine->payment_amount, 'status_id' => 17]);
            }
            $outletCollectionBalanceMapping = new OutletCollectionBalanceMapping();
            $outletCollectionBalanceMapping->site_id = $request->site_id;
            $outletCollectionBalanceMapping->balance_code = $dataOutletCollection->collection_code;
            $outletCollectionBalanceMapping->collection_id = $dataOutletCollection->id;
            $outletCollectionBalanceMapping->amount = $request->amount;
            $outletCollectionBalanceMapping->status_id = $request->status_id;
            $outletCollectionBalanceMapping->country_id = $request->country_id;
            $outletCollectionBalanceMapping->created_by = $request->up_emp_id;
            $outletCollectionBalanceMapping->updated_by = $request->up_emp_id;
            $outletCollectionBalanceMapping->updated_count = 0;
            $outletCollectionBalanceMapping->save();
            $dataSiteBalance = new SiteBalance();
            $dataSiteBalance->company_id = $request->ou_id;
            $dataSiteBalance->balance_code = $dataOutletCollection->collection_code;
            $dataSiteBalance->local_code = $request->column_id;
            $dataSiteBalance->balance_date = $request->date;
            $dataSiteBalance->emp_by = $request->emp_id;
            $dataSiteBalance->note = '';
            $dataSiteBalance->site_id = $request->site_id;
            $dataSiteBalance->trip_id = $request->trip_id;
            $dataSiteBalance->amount = $request->amount;
            $dataSiteBalance->collection_amount = $request->amount;
            $dataSiteBalance->balance_head_id = 1;
            $dataSiteBalance->cash_type_id = 2;
            $dataSiteBalance->round_amount = 0;
            $dataSiteBalance->status_id = $request->status_id;
            $dataSiteBalance->country_id = $request->country_id;
            $dataSiteBalance->created_by = $request->up_emp_id;
            $dataSiteBalance->updated_by = $request->up_emp_id;
            $dataSiteBalance->updated_count = 0;
            $dataSiteBalance->save();
            $orderSequence->collection_count = $orderSequence->collection_count + 1;
            $orderSequence->updated_by = $request->up_emp_id;
            $orderSequence->updated_count = $orderSequence->updated_count + 1;
            $orderSequence->save();
            DB::commit();
            return array('column_id' => $request->id);

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }


    public function collectionSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderSequence = OrderSequence::where(['emp_id' => $request->emp_id, 'year' => date('Y')])->first();
            $dataCollectionLines = json_decode($request->line_data);
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
            $dataOutletCollection = new OutletCollection();
            $dataOutletCollection->collection_code = "C" . str_pad($employee->user_name, 8, '0', STR_PAD_LEFT) . $orderSequence->year . str_pad($orderSequence->collection_count + 1, 6, '0', STR_PAD_LEFT);;
            $dataOutletCollection->column_id = $request->column_id;
            $dataOutletCollection->emp_id = $request->emp_id;
            $dataOutletCollection->company_id = $request->ou_id;
            $dataOutletCollection->trip_id = $request->trip_id;
            $dataOutletCollection->payment_date = $request->date;
            $dataOutletCollection->created_date = $request->date;
            $dataOutletCollection->note = '';
            $dataOutletCollection->outlet_id = $request->outlet_id;
            $dataOutletCollection->amount = $request->amount;
            $dataOutletCollection->allocated_amount = $request->amount;
            $dataOutletCollection->payment_type_id = $request->payment_type;
            $dataOutletCollection->collection_type_id = 1;
            $dataOutletCollection->cheque_no = '';
            $dataOutletCollection->cheque_date = date('Y-m-d');
            $dataOutletCollection->bank_id = 0;
            $dataOutletCollection->note = '';
            $dataOutletCollection->verified_by = 0;
            $dataOutletCollection->status_id = $request->status_id;
            $dataOutletCollection->country_id = $request->country_id;
            $dataOutletCollection->created_by = $request->up_emp_id;
            $dataOutletCollection->updated_by = $request->up_emp_id;
            $dataOutletCollection->updated_count = 0;
            $dataOutletCollection->save();
            foreach ($dataCollectionLines as $dataCollectionLine) {
                $outletCollectionMapping = new OutletCollectionMapping();
                $outletCollectionMapping->collection_id = $dataOutletCollection->id;
                $outletCollectionMapping->site_id = $dataCollectionLine->site_id;
                $outletCollectionMapping->invoice_code = $dataCollectionLine->invoice_code;
                $outletCollectionMapping->local_invoice_code = $dataCollectionLine->invoice_code;
                $outletCollectionMapping->amount = $dataCollectionLine->payment_amount;
                $outletCollectionMapping->country_id = $request->country_id;
                $outletCollectionMapping->created_by = $request->up_emp_id;
                $outletCollectionMapping->updated_by = $request->up_emp_id;
                $outletCollectionMapping->updated_count = 0;
                $outletCollectionMapping->save();
                $siteBalance = SiteBalance::where(['balance_code' => $dataCollectionLine->invoice_code])->first();
                if ($siteBalance != null) {
                    if ($siteBalance->collection_amount + $dataCollectionLine->payment_amount <= $siteBalance->amount) {
                        $siteBalance->collection_amount = $siteBalance->collection_amount + $dataCollectionLine->payment_amount;
                    }
                    if ($siteBalance->amount == $siteBalance->collection_amount) {
                        $siteBalance->status_id = 17;
                    }
                    $siteBalance->save();
                }

                //  DB::table('tblt_site_balance')->where(['local_code' => $dataCollectionLine->invoice_code])->update(['collection_amount' => $dataCollectionLine->payment_amount, 'status_id' => 17]);
            }
            $orderSequence->collection_count = $orderSequence->collection_count + 1;
            $orderSequence->updated_by = $request->up_emp_id;
            $orderSequence->updated_count = $orderSequence->updated_count + 1;
            $orderSequence->save();
            DB::commit();
            return array('column_id' => $request->id);

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function collectionModuleData(Request $request)
    {
        $data1 = DB::select("SELECT
  CONCAT(t5.payment_type_id, t1.id,t1.name,t6.name,t4.user_name,t4.name) AS column_id,
  t1.name                           AS site_name,
  t1.id                             AS site_id,
  t6.name                           AS cash_type,
  t4.user_name                      AS sr_id,
  t4.name                           AS sr_name,
  ''                                AS route_id
FROM tbld_site AS t1
  INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
  INNER JOIN tbld_pjp AS t3 ON t2.route_id = t3.route_id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
  INNER JOIN tbld_site_company_mapping AS t5 ON t1.id = t5.site_id
  INNER JOIN tbld_outlet_payment_type AS t6 ON t5.payment_type_id = t6.id
WHERE (t4.line_manager_id = $request->emp_id OR t4.id = $request->emp_id) AND t5.payment_type_id = 2 AND t1.status_id = 1
GROUP BY t1.id, t1.name, t6.name, t4.user_name, t4.name, t5.payment_type_id");
        $data2 = DB::select("SELECT
  concat(t1.id, t1.name, t1.code) AS column_id,
  t1.id                           AS ou_id,
  t1.Name                         AS ou_name,
  t1.code                         AS vat_number,
  t1.code                         AS exice_number
FROM tbld_company AS t1 INNER JOIN tbld_company_employee AS t2 ON t1.id = t2.company_id
WHERE t2.emp_id = $request->emp_id");

        $data3 = DB::select("SELECT
  id as column_id,id as token,id as bank_id,name as bank_name
FROM tbld_bank AS t1 ");
        return Array(
            "tbld_sr_site_collection" => array("data" => $data1, "action" => $request->country_id),
            "tbld_organization_unit" => array("data" => $data2, "action" => $request->country_id),
            "tbld_bank" => array("data" => $data3, "action" => $request->country_id),
        );
    }

    public function collectionData(Request $request)
    {
        $outlet_id = 0;
        $outlet_name = '';
        $site_id = 0;
        if ($request->site_id != '0' && $request->site_id != '') {
            $site_id = $request->site_id;
            $outlet_id = Site::find($request->site_id)->outlet_id;
        }
        if ($request->outlet_id != '0' && $request->outlet_id != '') {
            $outlet_id = $request->outlet_id;
            $outlet_name = Outlet::find($request->outlet_id)->name;
        }


        $data1 = DB::select("SELECT
  t1.balance_date               AS date,
  t1.site_id,
  t1.balance_code               AS invoice_code,
  t1.amount                     AS invoice_amount,
  t1.collection_amount          AS collection_amount,
  t2.outlet_id                  AS outlet_id,
  t2.name                       AS outlet_name,
  concat(t3.name, ' ', t1.note) AS invoice_type,
  t2.name                       AS site_name,
  t1.cash_type_id               AS transaction_type,
  2                             AS limit_type,
  t4.limit_days                 AS days,
   ''                           as vat_order_number
FROM tblt_site_balance AS t1
  INNER JOIN tbld_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tbld_balance_head AS t3 ON t1.balance_head_id = t3.id
  LEFT JOIN tbld_site_company_mapping AS t4 ON t1.site_id = t4.site_id and t1.company_id=t4.company_id
WHERE t1.status_id NOT IN (17, 18) and t1.company_id=$request->ou_id AND (t1.site_id = $site_id OR t2.outlet_id = $outlet_id)");
        return Array("receive_data" => $data1, "action" => $request->ou_id, "outlet_id" => $outlet_id, "outlet_name" => $outlet_name);
    }

}
