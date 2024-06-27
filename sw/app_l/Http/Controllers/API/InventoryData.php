<?php

namespace App\Http\Controllers\API;


use App\BusinessObject\InventoryCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function inventoryCount(Request $request)
    {
        $count = InventoryCount::where(['token' => $request->token])->first();
        if ($count == null) {
            $count = new InventoryCount();
            $count->emp_id = $request->emp_id;
            $count->bar_code = $request->barcode;
            $count->good_qty = $request->good_qty;
            $count->date = date('Y-m-d');
            $count->bad_qty = $request->bad_qty;
            $count->token = $request->token;
            $count->updated_by = $request->emp_id;
            $count->created_by = $request->emp_id;
            $count->country_id = $request->country_id;
            $count->status_id = 1;
            $count->token = $request->token;
            $count->save();
        }
        return array('column_id' => $count->id . ' Saved');
    }

    public function inventoryCountList(Request $request)
    {
        $tst = DB::select("SELECT
  t1.id,
   DATE_FORMAT(t1.updated_at, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.bar_code AS bar_code,
  '' AS sku_name,
  t1.good_qty,
  t1.bad_qty
FROM tblt_inventory_count AS t1
WHERE t1.emp_id = $request->emp_id AND t1.date BETWEEN '$request->start_date' AND '$request->end_date' AND t1.country_id = $request->country_id ORDER BY t1.updated_at DESC");
        return Array("receive_data" => array("data" => $tst, "action" => $request->emp_id));
    }

}
