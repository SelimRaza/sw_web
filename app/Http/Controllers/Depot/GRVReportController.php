<?php

namespace App\Http\Controllers\Depot;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class GRVReportController extends Controller
{
    private $access_key = 'GRVReportController';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function grvSummary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Depot.GRVReport.grv_summary')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function orderPrint($order_id)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $orderMasterData = collect(\DB::select("SELECT
  t1.order_id                                 AS Order_ID,
  DATE_FORMAT(t1.order_date, '%b %d, %Y')     AS order_date,
  DATE_FORMAT(t1.delivery_date, '%b %d, %Y')  AS delivery_date,
  t2.name                                     AS Outlet_Name,
  t2.name                                     AS Site_Name,
  t1.invoice_amount                      AS total_price,
  '0'                                         AS discount,
  t1.site_id                                  AS customer_number,
  t2.outlet_id                                AS outlet_id,
  t9.name                                     AS Payment_Type,
  'Test Region'                               AS Region_Name,
  'Test Zone'                                 AS Zone_Name,
  t2.address                                  AS site_address,
  t2.address                                  AS outlet_address,
  t3.Name                                     AS preseller_name,
  t4.id                                       AS ou_id,
  t4.name                                     AS ou_name,
  '123456'                                    AS tax_number,
  '123456'                                    AS vat_number,
  t5.year                                     AS year,
  t5.s_no                                     AS serial_number,
  concat(t5.year, '-', LPAD(t5.s_no, 8, '0')) AS vat_sl_number,
  'Company Address'                           AS address,
  '464'                                       AS post_box_no,
  'test@mail.com'                             AS email,
  '6541'                                      AS phone,
  '8799'                                      AS fax,
  t2.vat_trn                                  AS VAT_TRN,
  'Tax Invoice'                               AS invoice_title,
  '1'                                         AS vat_status,
  t6.amount                                   AS invoice_amount,
  'AED'                                       AS currency,
  2                                           AS round_digit,
  3                                          AS round,
  ''                                          AS note
FROM tblt_order_master AS t1
  INNER JOIN tbld_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t1.emp_id = t3.id
  INNER JOIN tbld_company AS t4 ON t1.company_id = t4.id
  LEFT JOIN tblt_delivery_sequence_mapping AS t5 ON t1.id = t5.o_id
  INNER JOIN tblt_site_balance AS t6 ON t1.order_id = t6.balance_code
  INNER JOIN tbld_country AS t7 ON t4.country_id = t7.id
  INNER JOIN tbld_site_company_mapping AS t8 ON t1.company_id = t8.company_id AND t1.site_id = t8.site_id
  INNER JOIN tbld_outlet_payment_type AS t9 ON t8.payment_type_id = t9.id
WHERE t1.order_id = '$order_id' "))->first();


            $orderLineData = DB::select("SELECT
  t2.sku_id                     AS Product_id,
  t3.name                       AS Product_Name,
  ''                            AS sku_print_name,
   floor(t2.order_qty / t3.ctn_size)        AS ctn,
   t2.qty_delivery % t3.ctn_size AS pcs,
  t2.             AS Total_Item_Price,
  t2.unit_price * t3.ctn_size   AS Rate,
  ''                            AS ratio,
  ''                            AS Discount,
  t3.ctn_size                   AS ctn_size,
  ''                            AS promo_ref,
  0                             AS gst,
  0                             AS vat,
  0                             AS total_discount,
  0                             AS total_vat,
  0                             AS total_gst,
  t2.invoice_amount             AS net_amount
FROM tblt_order_master AS t1
  INNER JOIN tblt_order_line AS t2 ON t1.id = t2.so_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
WHERE t1.order_id = '$order_id' AND t2.qty_delivery != 0");
            return view('Depot.OrderReport.order_print')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function invoicePrint($order_id)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $orderMasterData = collect(\DB::select("SELECT
  t1.order_id                                 AS Order_ID,
  DATE_FORMAT(t1.order_date, '%b %d, %Y')     AS order_date,
  DATE_FORMAT(t1.delivery_date, '%b %d, %Y')  AS delivery_date,
  t2.name                                     AS Outlet_Name,
  t2.name                                     AS Site_Name,
  t1.invoice_amount                      AS total_price,
  '0'                                         AS discount,
  t1.site_id                                  AS customer_number,
  t2.outlet_id                                AS outlet_id,
  t9.name                                     AS Payment_Type,
  'Test Region'                               AS Region_Name,
  'Test Zone'                                 AS Zone_Name,
  t2.address                                  AS site_address,
  t2.address                                  AS outlet_address,
  t3.Name                                     AS preseller_name,
  t4.id                                       AS ou_id,
  t4.name                                     AS ou_name,
  '123456'                                    AS tax_number,
  '123456'                                    AS vat_number,
  t5.year                                     AS year,
  t5.s_no                                     AS serial_number,
  concat(t5.year, '-', LPAD(t5.s_no, 8, '0')) AS vat_sl_number,
  'Company Address'                           AS address,
  '464'                                       AS post_box_no,
  'test@mail.com'                             AS email,
  '6541'                                      AS phone,
  '8799'                                      AS fax,
  t2.vat_trn                                  AS VAT_TRN,
  'Tax Invoice'                               AS invoice_title,
  '1'                                         AS vat_status,
  t6.amount                                   AS invoice_amount,
  'AED'                                       AS currency,
  2                                           AS round_digit,
  3                                          AS round,
  ''                                          AS note
FROM tblt_order_master AS t1
  INNER JOIN tbld_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t1.emp_id = t3.id
  INNER JOIN tbld_company AS t4 ON t1.company_id = t4.id
  LEFT JOIN tblt_delivery_sequence_mapping AS t5 ON t1.id = t5.o_id
  INNER JOIN tblt_site_balance AS t6 ON t1.order_id = t6.balance_code
  INNER JOIN tbld_country AS t7 ON t4.country_id = t7.id
  INNER JOIN tbld_site_company_mapping AS t8 ON t1.company_id = t8.company_id AND t1.site_id = t8.site_id
  INNER JOIN tbld_outlet_payment_type AS t9 ON t8.payment_type_id = t9.id
WHERE t1.order_id = '$order_id' "))->first();


            $orderLineData = DB::select("SELECT
  t2.sku_id                     AS Product_id,
  t3.name                       AS Product_Name,
  ''                            AS sku_print_name,
   floor(t2.qty_delivery / t3.ctn_size)        AS ctn,
   t2.qty_delivery % t3.ctn_size AS pcs,
  t2.total_delivery             AS Total_Item_Price,
  t2.unit_price * t3.ctn_size   AS Rate,
  ''                            AS ratio,
  ''                            AS Discount,
  t3.ctn_size                   AS ctn_size,
  ''                            AS promo_ref,
  0                             AS gst,
  0                             AS vat,
  0                             AS total_discount,
  0                             AS total_vat,
  0                             AS total_gst,
  t2.invoice_amount             AS net_amount
FROM tblt_order_master AS t1
  INNER JOIN tblt_order_line AS t2 ON t1.id = t2.so_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
WHERE t1.order_id = '$order_id' AND t2.qty_delivery != 0");
            return view('Depot.OrderReport.invoice_print_ksa')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function pushToRoutePlan(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if (isset($request->so_id)) {
                foreach ($request->so_id as $index => $lineId) {
                    DB::connection($this->db)->table('tt_rtan')->where(['id' => $lineId])->update(['lfcl_id' => 8]);
                }
            }
            return redirect()->back()->with('success', 'Successfully Uploaded');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }


    }

    public function filterGRVSummary(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id = $this->currentUser->employee()->id;
        $where = "1 and t1.cont_id=$country_id and t1.aemp_id=$emp_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t2.rtan_date between '$request->start_date' and '$request->end_date'";
        }
        $data = DB::connection($this->db)->select("SELECT
                t2.id        AS so_id,
                t2.rtan_rtnm    order_id,
                t2.rtan_amnt AS order_amount,
                t3.aemp_name AS emp_name,
                t3.aemp_usnm AS user_name,
                t4.site_name AS site_name,
                t2.site_id,
                t4.site_code AS site_code,
                t2.rtan_date    order_date,
                t2.rtan_time AS order_date_time,
                t5.lfcl_name AS status_name,
                t2.lfcl_id   AS status_id,
                'GRV'      AS order_type,
                t1.cont_id      AS cont_id
                FROM tl_srdi AS t1
                INNER JOIN tt_rtan AS t2 ON t1.dlrm_id = t2.dlrm_id
                INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
                INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
                INNER JOIN tm_lfcl AS t5 ON t2.lfcl_id = t5.id
                WHERE $where;");

        return $data;

    }


}