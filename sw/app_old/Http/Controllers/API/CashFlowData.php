<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }



    public function cashFlowData(Request $request)
    {
        $data1 = DB::select("SELECT
  concat(t1.id, t1.name, t1.code, t1.status_id) AS column_id,
  t1.id                                         AS account_id,
  t1.name                                       AS account_name
FROM tbld_accounts AS t1 INNER JOIN tbld_employee_account_mapping AS t2 ON t1.id = t2.account_id
WHERE t2.emp_id = $request->emp_id AND t1.country_id = $request->country_id AND t1.status_id = 1");
        $data2 = DB::select("SELECT
  concat(t3.id, t3.name, t3.status_id) AS column_id,
  t1.id                                AS account_id,
  t3.id                                AS cash_move_id,
  t3.name                              AS cash_move_name,
  t3.cash_type_id                      AS cash_type_id
FROM tbld_accounts AS t1
  INNER JOIN tbld_employee_account_mapping AS t2 ON t1.id = t2.account_id
  INNER JOIN tbld_cash_move_type AS t3 ON t1.id = t3.account_id
WHERE t2.emp_id = $request->emp_id AND t1.country_id = $request->country_id AND t1.status_id = 1");
        $data3 = DB::select("SELECT
  concat(t3.id, t3.name, t3.status_id, t1.id, t3.amount) AS column_id,
  t1.id                                       AS account_id,
  t3.id                                       AS cash_source_id,
  t3.name                                     AS cash_source_name,
  t3.amount                                   AS balance
FROM tbld_accounts AS t1
  INNER JOIN tbld_employee_account_mapping AS t2 ON t1.id = t2.account_id
  INNER JOIN tbld_cash_source AS t3 ON t1.id = t3.account_id
WHERE t2.emp_id = $request->emp_id AND t1.country_id = $request->country_id AND t1.status_id = 1");

        return Array(
            "tbld_cash_flow_accounts" => array("data" => $data1, "action" => $request->country_id),
            "tbld_cash_move_type" => array("data" => $data2, "action" => $request->country_id),
            "tbld_cash_source" => array("data" => $data3, "action" => $request->country_id),
        );
    }


}