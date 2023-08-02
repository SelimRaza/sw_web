<?php

namespace App\Http\Controllers\Depot;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\DepotStock;
use App\BusinessObject\LoadLine;
use App\BusinessObject\LoadMaster;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGRV;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\BusinessObject\TripType;
use App\MasterData\Depot;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\TableRows;

class TripController extends Controller
{
    private $access_key = 'TripController';
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

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $trips = Trip::on($this->db)->where(function ($query) use ($q) {
                    $query->where('tt_trip.id', 'LIKE', '%' . $q . '%')
                        ->orWhere('tt_trip.aemp_tusr', 'LIKE', '%' . $q . '%');
                })->join('tl_srdi', 'tl_srdi.dlrm_id', '=', 'tt_trip.dlrm_id')->where(['tt_trip.cont_id' => $this->currentUser->employee()->cont_id, 'tl_srdi.aemp_id' => $this->currentUser->employee()->id])->paginate(500, array('tt_trip.*'))->setPath('');
            } else {
                $trips = Trip::on($this->db)->join('tl_srdi', 'tl_srdi.dlrm_id', '=', 'tt_trip.dlrm_id')->where(['tt_trip.cont_id' => $this->currentUser->employee()->cont_id, 'tl_srdi.aemp_id' => $this->currentUser->employee()->id])->paginate(500, array('tt_trip.*'));
            }
            return view('Depot.Trip.index')->with('trips', $trips)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $emp_id = $this->currentUser->employee()->id;

            $data = DB::connection($this->db)->select("SELECT
  t2.id        AS dlrm_id,
  t2.dlrm_name AS dlrm_name,
  t4.id        AS dpot_id,
  t4.dpot_name as dpot_name
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_whos AS t3 ON t2.whos_id = t3.id
  INNER JOIN tm_dpot AS t4 ON t3.dpot_id = t4.id
WHERE t1.aemp_id = $emp_id");
            $dataVhcl = DB::connection($this->db)->select("SELECT
  t1.id        AS id,
  t1.vhcl_name AS vhcl_name,
  t1.vhcl_code        AS vhcl_code
FROM tm_vhcl AS t1");


            $dataEmp = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            $dataTripType = TripType::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('Depot.Trip.create')->with("depots", $data)->with("dataTripType", $dataTripType)->with("dataEmp", $dataEmp)->with("dataVhcl", $dataVhcl)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $value)
    {
        $depot = unserialize($value->depot_id);
        if ($value->trip_type_id == 2) {
            $trip1 = Trip::on($this->db)->where(['aemp_tusr' => $value->emp_id])->whereIn('lfcl_id', [31, 20, 5, 6, 32])->whereNotIn('lfcl_id', [1, 2, 25])->first();
            if ($trip1 == null) {
                DB::connection($this->db)->beginTransaction();
                try {
                    $trip = new Trip();
                    $trip->setConnection($this->db);
                    $trip->trip_code = '';
                    $trip->trip_otid = 0;
                    $trip->dlrm_id = $depot->dlrm_id;
                    $trip->dpot_id = $depot->dpot_id;
                    $trip->vhcl_id = $value->vhcl_id;
                    $trip->aemp_tusr = $value['emp_id'];
                    $trip->ttyp_id = $value->trip_type_id;
                    $trip->trip_date = $value['trip_date'];
                    $trip->trip_vdat = date('Y-m-d');
                    $trip->aemp_vusr = 0;
                    $trip->trip_ldat = date('Y-m-d');
                    $trip->aemp_lusr = 0;
                    $trip->trip_cdat = date('Y-m-d');
                    $trip->aemp_cusr = 0;
                    $trip->lfcl_id = 20;
                    $trip->cont_id = $this->currentUser->employee()->cont_id;
                    $trip->aemp_iusr = $this->currentUser->employee()->id;
                    $trip->aemp_eusr = $this->currentUser->employee()->id;
                    $trip->save();
                    DB::connection($this->db)->commit();
                    return redirect('/trip');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    throw $e;
                }
            } else {
                return redirect()->back()->with('danger', 'Please Close Previous Trip');
            }

        } else {
            DB::connection($this->db)->beginTransaction();
            try {
                $trip = new Trip();
                $trip->setConnection($this->db);
                $trip->trip_code = '';
                $trip->trip_otid = 0;
                $trip->dlrm_id = $depot->dlrm_id;
                $trip->dpot_id = $depot->dpot_id;
                $trip->vhcl_id = 1;
                $trip->aemp_tusr = $value['emp_id'];
                $trip->ttyp_id = $value->trip_type_id;
                $trip->trip_date = $value['trip_date'];
                $trip->trip_vdat = date('Y-m-d');
                $trip->aemp_vusr = 0;
                $trip->trip_ldat = date('Y-m-d');
                $trip->aemp_lusr = 0;
                $trip->trip_cdat = date('Y-m-d');
                $trip->aemp_cusr = 0;
                $trip->lfcl_id = 1;
                $trip->cont_id = $this->currentUser->employee()->cont_id;
                $trip->aemp_iusr = $this->currentUser->employee()->id;
                $trip->aemp_eusr = $this->currentUser->employee()->id;
                $trip->save();
                DB::connection($this->db)->commit();
                return redirect('trip/' . $trip->id . "/edit");
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
            }
        }

    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $trip = Trip::on($this->db)->findorfail($id);

            $tripCollection = DB::connection($this->db)->select("SELECT
  t1.id        AS collection_id,
  t1.cltn_code AS collection_code,
  t1.cltn_amnt AS amount,
  t1.oult_id   AS outlet_id,
  t2.oult_name AS outlet_name
FROM tt_cltn AS t1
  INNER JOIN tm_oult AS t2 ON t1.oult_id = t2.id
WHERE t1.trip_id = $id");

            $tripOrder = DB::connection($this->db)->select("SELECT
  t2.id        AS so_id,
  t2.ordm_ornm AS order_id,
  t2.ordm_amnt AS order_amount,
  ''           AS invoice_amount,
  t3.aemp_name AS emp_name,
  t3.aemp_usnm AS user_name,
  t4.site_name AS site_name,
  t2.site_id,
  t4.site_code AS site_code,
  t2.ordm_date AS order_date,
  t2.ordm_time    order_date_time,
  t5.lfcl_name AS status_name,
  t2.lfcl_id   AS status_id,
  'order'      AS order_type,
  t1.cont_id
FROM tt_trom AS t1
  INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
  INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
  INNER JOIN tm_lfcl AS t5 ON t2.lfcl_id = t5.id
WHERE t1.trip_id = $id");

            $tripGRv = DB::connection($this->db)->select("SELECT
  t2.id        AS so_id,
  t2.rtan_rtnm AS order_id,
  t2.rtan_amnt AS order_amount,
  ''           AS invoice_amount,
  t3.aemp_name AS emp_name,
  t3.aemp_usnm AS user_name,
  t4.site_name AS site_name,
  t2.site_id,
  t4.site_code AS site_code,
  t2.rtan_date AS order_date,
  t2.rtan_time    order_date_time,
  t5.lfcl_name AS status_name,
  t2.lfcl_id   AS status_id,
  'order'      AS order_type,
  t1.cont_id
FROM tt_trgm AS t1
  INNER JOIN tt_rtan AS t2 ON t1.rtan_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
  INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
  INNER JOIN tm_lfcl AS t5 ON t2.lfcl_id = t5.id
WHERE t1.trip_id = $id");


            $tripOrderSKu = DB::connection($this->db)->select("SELECT
  t2.id        AS sku_id,
  t2.amim_name AS sku_name,
  t2.amim_code AS sku_code,
  t1.troc_iqty AS issued_qty,
  t1.troc_cqty AS confirm_qty,
  t1.troc_dqty AS delivery_qty,
  t1.troc_lqty AS logistic_qty
FROM tt_troc AS t1
  INNER JOIN tm_amim AS t2 ON t1.amim_id = t2.id
WHERE t1.trip_id = $id ");
            $tripGrvSKu = DB::connection($this->db)->select("SELECT
  t2.id        AS sku_id,
  t2.amim_name AS sku_name,
  t2.amim_code AS sku_code,
  t1.trgc_iqty AS issued_qty,
  t1.trgc_cqty AS confirm_qty,
  t1.trgc_dqty AS delivery_qty,
  t1.trgc_gqty AS g_qty,
  t1.trgc_bqty AS b_qty
FROM tt_trgc AS t1
  INNER JOIN tm_amim AS t2 ON t1.amim_id = t2.id
WHERE t1.trip_id = $id");

            $tripStockMove = DB::connection($this->db)->select("SELECT
  t2.id,
  t2.lodm_code AS load_code,
  t2.lodm_date AS request_date,
  t2.lodm_vdat AS load_date,
  t2.lfcl_id   AS status_id,
  t4.lfcl_name AS status,
  t5.lodt_name as type,
  t5.id as type_id
FROM tt_trip AS t1
  INNER JOIN tt_lodm AS t2 ON t1.id = t2.trip_id
  INNER JOIN tm_lfcl AS t4 ON t2.lfcl_id = t4.id
  INNER JOIN tm_lodt as t5 ON t2.lodt_id=t5.id
WHERE t1.id = $id");
            return view('Depot.Trip.show')->with('tripStockMove', $tripStockMove)->with('tripCollection', $tripCollection)->with('trip', $trip)->with('tripOrderSKu', $tripOrderSKu)->with('tripGrvSKu', $tripGrvSKu)->with('tripGRvs', $tripGRv)->with('tripOrders', $tripOrder)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $trip = Trip::on($this->db)->findorfail($id);

            $data = DB::connection($this->db)->select("SELECT
  t4.aemp_id,
  t5.aemp_name,
  t5.aemp_usnm
FROM tm_dpot AS t1 INNER JOIN tm_whos AS t2 ON t1.id = t2.dpot_id
  INNER JOIN tm_dlrm AS t3 ON t2.id = t3.whos_id
  INNER JOIN tl_srdi AS t4 ON t3.id = t4.dlrm_id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_id = t5.id
WHERE t1.id = $trip->dpot_id");
            return view('Depot.Trip.edit')->with('trip', $trip)->with('aemp', $data)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function grv($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $trip = Trip::on($this->db)->findorfail($id);
            $data = DB::connection($this->db)->select("SELECT
  t4.aemp_id,
  t5.aemp_name,
  t5.aemp_usnm
FROM tm_dpot AS t1 INNER JOIN tm_whos AS t2 ON t1.id = t2.dpot_id
  INNER JOIN tm_dlrm AS t3 ON t2.id = t3.whos_id
  INNER JOIN tl_srdi AS t4 ON t3.id = t4.dlrm_id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_id = t5.id
WHERE t1.id = $trip->dpot_id");
            return view('Depot.Trip.grv_assign')->with('trip', $trip)->with('aemp', $data)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $trip = Trip::on($this->db)->findorfail($id);
            $trip->aemp_eusr = $this->currentUser->employee()->id;
            $trip->save();
            if ($request->so_id != null) {
                DB::connection($this->db)->table('tt_trom')
                    ->where(['trip_id' => $id])
                    ->delete();
                foreach ($request->so_id as $order_id) {
                    $tripOrder = new TripOrder();
                    $tripOrder->setConnection($this->db);
                    //  $tripOrder->from_previous_trip = 0;
                    $tripOrder->trip_id = $id;
                    $tripOrder->ordm_id = $order_id;
                    $tripOrder->lfcl_id = 8;
                    $tripOrder->ondr_id = 0;
                    $tripOrder->cont_id = $this->currentUser->employee()->cont_id;
                    $tripOrder->aemp_iusr = $this->currentUser->employee()->id;
                    $tripOrder->aemp_eusr = $this->currentUser->employee()->id;
                    $tripOrder->save();

                }
            }
            DB::connection($this->db)->commit();
            return redirect('trip/order/' . $trip->id);
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }
    }

    public function grvAssign(Request $request, $id)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $trip = Trip::on($this->db)->findorfail($id);
            $trip->aemp_eusr = $this->currentUser->employee()->id;
            $trip->save();
            if ($request->so_id != null) {
                DB::connection($this->db)->table('tt_trgm')
                    ->where(['trip_id' => $id])
                    ->delete();
                foreach ($request->so_id as $order_id) {
                    $tripGrv = new TripGRV();
                    $tripGrv->setConnection($this->db);
                    $tripGrv->trip_id = $id;
                    $tripGrv->rtan_id = $order_id;
                    $tripGrv->lfcl_id = 8;
                    $tripGrv->ondr_id = 0;
                    $tripGrv->cont_id = $this->currentUser->employee()->cont_id;
                    $tripGrv->aemp_iusr = $this->currentUser->employee()->id;
                    $tripGrv->aemp_eusr = $this->currentUser->employee()->id;
                    $tripGrv->save();

                }
            }
            DB::connection($this->db)->commit();
            return redirect('trip/product/' . $trip->id);
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }
    }


    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $trip = Trip::on($this->db)->findorfail($id);
            if ($trip->lfcl_id == 1) {
                $trip->lfcl_id = 2;
                $trip->aemp_eusr = $this->currentUser->employee()->id;
                $trip->save();
            }
            return redirect('/trip')->with('success', 'Trip Inactivated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function product($id)
    {
        $trip = Trip::on($this->db)->findorfail($id);
        $tripOrderSKu = DB::connection($this->db)->select("SELECT
  t4.id                   AS sku_id,
  t4.amim_name            AS sku_name,
  t4.amim_code            AS sku_code,
  sum(t3.ordd_cqty)       AS qty_order,
  IFNULL(t5.dlst_qnty, 0) AS stock_qty
FROM tt_trom AS t1
  INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
  INNER JOIN tt_ordd AS t3 ON t2.id = t3.ordm_id
  INNER JOIN tm_amim AS t4 ON t3.amim_id = t4.id
  LEFT JOIN tt_dlst AS t5 ON t5.dlrm_id = $trip->dlrm_id AND t4.id = t5.amim_id
WHERE t1.trip_id = $id
GROUP BY t4.id, t4.amim_name, t4.amim_code, t5.dlst_qnty");
        $tripGrvSKu = DB::connection($this->db)->select("SELECT
  t4.id             AS sku_id,
  t4.amim_name      AS sku_name,
  t4.amim_code      AS sku_code,
  sum(t3.rtdd_qnty) AS qty_order
FROM tt_trgm AS t1
  INNER JOIN tt_rtan AS t2 ON t1.rtan_id = t2.id
  INNER JOIN tt_rtdd AS t3 ON t2.id = t3.rtan_id
  INNER JOIN tm_amim AS t4 ON t3.amim_id = t4.id
WHERE t1.trip_id = $id
GROUP BY t4.id, t4.amim_name, t4.amim_code");
        return view('Depot.Trip.trip_product')->with('trip', $trip)->with('tripOrderSKu', $tripOrderSKu)->with('tripGrvSKu', $tripGrvSKu)->with('permission', $this->userMenu);
    }

    public function orderEdit(Request $request, $id)
    {
        // dd($request->rtdd_id);
        if (isset($request->rtdd_id)) {
            $p_qty = $request->p_qty;
            $qty_rate = $request->qty_rate;
            $order_qty = $request->order_qty;
            $ctn_size = $request->ctn_size;
            $def_discount = $request->def_discount;
            $discount = $request->discount;
            $promo_discount = $request->promo_discount;
            foreach ($request->rtdd_id as $index => $lineId) {
                $request->p_qty[$index];
                $confirm_qty = (int)round($p_qty[$index] * $ctn_size[$index]);
                $total_amount = $confirm_qty * $qty_rate[$index];
                $def_discount_ratio = $def_discount[$index] / $order_qty[$index];
                $discount_ratio = $discount[$index] / $order_qty[$index];
                $promo_discount_ratio = $promo_discount[$index] / $order_qty[$index];
                DB::connection($this->db)->table('tt_ordd')->where(['id' => $lineId])->update(['ordd_cqty' => $confirm_qty, 'ordd_ocat' => $total_amount, 'ordd_cpds' => $promo_discount_ratio * $confirm_qty, 'ordd_spdc' => $discount_ratio * $confirm_qty, 'ordd_dfdc' => $def_discount_ratio * $confirm_qty,]);
            }
        }
        return redirect('trip/grv/' . $id);
    }

    public function order($id)
    {
        $trip = Trip::on($this->db)->findorfail($id);
        $tripOrder = DB::connection($this->db)->select("SELECT
  t2.ordm_ornm as order_id,
  t8.aemp_usnm as  sr_id,
  t9.optp_name as  payment_type,
  t6.site_code as site_code,
  t6.site_name as  site_name,
  t2.ordm_date as order_date,
  t3.id,
  t4.amim_code as sku_code,
  t3.ordd_uprc as unit_price,
  t4.amim_name as item_name,
  t3.ordd_qnty as order_qty,
  t5.dlst_qnty as stock_qty,
  t3.ordd_duft as ctn_size,
  t3.ordd_qnty/t3.ordd_duft as order_ctn_qty,
  t3.ordd_dfdo as defult_discount,
  t3.ordd_spdo as sp_dis,
  t3.ordd_opds as promo_dis,
  t3.ordd_oamt as order_amouont,
  t3.prom_id as promo_ref,
  t3.ordd_smpl as is_free_item
FROM tt_trom AS t1
  INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
  INNER JOIN tt_ordd AS t3 ON t2.id = t3.ordm_id
  INNER JOIN tm_amim AS t4 ON t3.amim_id = t4.id
  LEFT JOIN tt_dlst AS t5 ON t5.dlrm_id = $trip->dlrm_id AND t4.id = t5.amim_id
  INNER JOIN tm_site AS t6 ON t2.site_id = t6.id
  INNER JOIN tl_stcm AS t7 ON t6.id = t7.site_id AND t2.acmp_id = t7.acmp_id
  INNER JOIN tm_aemp AS t8 ON t2.aemp_id = t8.id
  INNER JOIN tm_optp AS t9 ON t7.optp_id = t9.id
WHERE t1.trip_id = $id
ORDER BY t2.id ASC ");

        return view('Depot.Trip.trip_order')->with('trip', $trip)->with('tripOrder', $tripOrder)->with('permission', $this->userMenu);
    }

    public function loadProduct($id)
    {
        $loadMaster = LoadMaster::on($this->db)->findorfail($id);
        $tripLoadLine = DB::connection($this->db)->select("SELECT
  t3.id                   AS sku_id,
  t3.amim_name            AS sku_name,
  t3.amim_code            AS sku_code,
  t2.lodl_qnty            AS request_qty,
  t2.lodl_cqty            AS load_qty,
  IFNULL(t4.dlst_qnty, 0) AS stock_qty
FROM tt_lodm AS t1
  INNER JOIN tt_lodl AS t2 ON t1.id = t2.lodm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  LEFT JOIN tt_dlst AS t4 ON t1.dlrm_id = t4.dlrm_id AND t2.amim_id = t4.amim_id
WHERE t1.id = $id ");
        return view('Depot.Trip.load_product')->with('loadMaster', $loadMaster)->with('tripLoadLine', $tripLoadLine)->with('permission', $this->userMenu);
    }


    public function loadVerify(Request $request, $id)
    {
        //  dd($request);
        $loadMaster = LoadMaster::on($this->db)->findorfail($id);
        if (array_sum($request->stock_qty) > 0) {
            DB::connection($this->db)->beginTransaction();
            try {
                foreach ($request->load_sku as $index => $load_sku1) {
                    $loadSku = LoadLine::on($this->db)->where(['lodm_id' => $id, 'amim_id' => $load_sku1])->first();
                    $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $loadMaster->dlrm_id, 'amim_id' => $load_sku1])->first();
                    if ($depotStock != null) {
                        if ($depotStock->dlst_qnty >= $request->stock_qty[$index]) {
                            if ($loadSku != null) {
                                $loadSku->lodl_cqty = $request->stock_qty[$index];
                                $loadSku->lodl_dqty = $request->stock_qty[$index];
                                $loadSku->aemp_eusr = $this->currentUser->employee()->id;
                                $loadSku->save();
                                $depotStock->dlst_qnty = $depotStock->dlst_qnty - $request->stock_qty[$index];
                                $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                                $depotStock->save();
                            }
                        }
                    } else {
                        $loadSku->lodl_cqty = 0;
                        $loadSku->lodl_dqty = 0;
                        $loadSku->aemp_eusr = $this->currentUser->employee()->id;
                        $loadSku->save();

                    }


                }
                $loadMaster->lfcl_id = 23;
                $loadMaster->aemp_eusr = $this->currentUser->employee()->id;
                $loadMaster->save();
                DB::connection($this->db)->commit();

                return redirect()->back()->with('success', 'Successfully Verified');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', 'problem' . $e);
            }


        } else {
            return redirect()->back()->with('danger', 'No Item To Verify');
        }

    }

    public function unloadProduct($id)
    {
        $loadMaster = LoadMaster::on($this->db)->findorfail($id);
        $tripLoadLine = DB::connection($this->db)->select("SELECT
  t3.id                            AS sku_id,
  t3.amim_name                     AS sku_name,
  t3.amim_code                     AS sku_code,
  t2.lodl_qnty                     AS request_qty,
  t2.lodl_dqty                     AS load_qty,
  t4.troc_cqty - t4.troc_dqty AS available_qty
FROM tt_lodm AS t1
  INNER JOIN tt_lodl AS t2 ON t1.id = t2.lodm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tt_troc AS t4 ON t1.trip_id = t4.trip_id AND t2.amim_id = t4.amim_id
WHERE t1.id = $id ");
        if ($loadMaster->lodt_id == 3 || $loadMaster->lodt_id == 5) {
            $tripLoadLine = DB::connection($this->db)->select("SELECT
  t3.id                       AS sku_id,
  t3.amim_name                AS sku_name,
  t3.amim_code                AS sku_code,
  t2.lodl_qnty                AS request_qty,
  t2.lodl_dqty                AS load_qty,
  t4.trgc_cqty - t4.trgc_dqty AS available_qty
FROM tt_lodm AS t1
  INNER JOIN tt_lodl AS t2 ON t1.id = t2.lodm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tt_trgc AS t4 ON t1.trip_id = t4.trip_id AND t2.amim_id = t4.amim_id
WHERE t1.id = $id ");
        }

        return view('Depot.Trip.unload_product')->with('loadMaster', $loadMaster)->with('tripLoadLine', $tripLoadLine)->with('permission', $this->userMenu);
    }

    public function unloadVerify(Request $request, $id)
    {
        $loadMaster = LoadMaster::on($this->db)->findorfail($id);
        if ($request->load_sku != null) {
            DB::connection($this->db)->beginTransaction();
            try {
                foreach ($request->load_sku as $index => $load_sku1) {
                    $loadSku = LoadLine::on($this->db)->where(['lodm_id' => $id, 'amim_id' => $load_sku1])->first();
                    if ($loadSku != null) {
                        $loadSku->lodl_cqty = $request->request_qty[$index];
                        $loadSku->lodl_qnty = $request->request_qty[$index];
                        $loadSku->lodl_dqty = $request->request_qty[$index];
                        $loadSku->aemp_eusr = $this->currentUser->employee()->id;
                        $loadSku->save();

                        if ($loadMaster->lodt_id == 2) {
                            $tripSku = TripSku::on($this->db)->where(['trip_id' => $loadMaster->trip_id, 'amim_id' => $load_sku1])->first();
                            if ($tripSku == null) {
                                $tripSku = new TripSku();
                                $tripSku->setConnection($this->db);
                                $tripSku->trip_id = $loadMaster->trip_id;
                                $tripSku->amim_id = $loadSku->amim_id;
                                $tripSku->troc_iqty = 0;
                                $tripSku->troc_cqty = 0;
                                $tripSku->troc_dqty = $loadSku->lodl_qnty;
                                $tripSku->troc_lqty = 0;
                                $tripSku->lfcl_id = 1;
                                $tripSku->cont_id = $this->currentUser->employee()->cont_id;
                                $tripSku->aemp_iusr = $this->currentUser->employee()->id;
                                $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripSku->save();
                            } else {
                                $tripSku->troc_dqty = $tripSku->troc_dqty + $loadSku->lodl_qnty;
                                $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripSku->save();
                            }
                        }
                        if ($loadMaster->lodt_id == 3 || $loadMaster->lodt_id == 5) {
                            $tripGRVSku = TripGrvSku::on($this->db)->where(['trip_id' => $loadMaster->trip_id, 'amim_id' => $load_sku1])->first();
                            if ($tripGRVSku == null) {
                                $tripGrvSku = new TripGrvSku();
                                $tripGrvSku->setConnection($this->db);
                                $tripGrvSku->trip_id = $request->trip_id;
                                $tripGrvSku->amim_id = $loadSku->amim_id;
                                $tripGrvSku->trgc_iqty = 0;
                                $tripGrvSku->trgc_cqty = 0;
                                $tripGrvSku->trgc_dqty = $loadSku->lodl_qnty;
                                $tripGrvSku->trgc_lqty = 0;
                                $tripGrvSku->lfcl_id = 1;
                                $tripGrvSku->cont_id = $this->currentUser->employee()->cont_id;
                                $tripGrvSku->aemp_iusr = $this->currentUser->employee()->id;
                                $tripGrvSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripGrvSku->save();
                            } else {
                                $tripGRVSku->trgc_dqty = $tripGRVSku->trgc_dqty + $loadSku->lodl_qnty;
                                $tripGRVSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripGRVSku->save();
                            }
                        }
                        $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $loadMaster->dlrm_id, 'amim_id' => $load_sku1])->first();
                        if ($depotStock == null) {
                            $depotStock = new DepotStock();
                            $depotStock->setConnection($this->db);
                            $depotStock->dlrm_id = $loadMaster->dlrm_id;
                            $depotStock->amim_id = $load_sku1;
                            $depotStock->dlst_qnty = $loadSku->lodl_qnty;
                            $depotStock->cont_id = $loadSku->cont_id;
                            $depotStock->lfcl_id = 1;
                            $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        } else {
                            $depotStock->dlst_qnty = $depotStock->dlst_qnty + $loadSku->lodl_qnty;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        }
                    }

                }
                $loadMaster->lfcl_id = 11;
                $loadMaster->aemp_eusr = $this->currentUser->employee()->id;
                $loadMaster->save();
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'Successfully Verified');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', 'problem' . $e);
            }
        } else {
            return redirect()->back()->with('danger', 'No Item To Verify');
        }

    }

    public function tripClose(Request $request, $id)
    {
        $trip = Trip::on($this->db)->findorfail($id);
        if ($trip->lfcl_id == 5) {
            DB::connection($this->db)->beginTransaction();
            try {
                if (isset($request->trip_sku)) {
                    foreach ($request->trip_sku as $index => $sku_id) {
                        $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $trip->dlrm_id, 'amim_id' => $sku_id])->first();
                        if ($depotStock == null) {
                            $depotStock = new DepotStock();
                            $depotStock->setConnection($this->db);
                            $depotStock->dlrm_id = $trip->dlrm_id;
                            $depotStock->amim_id = $sku_id;
                            $depotStock->dlst_qnty = $request->logistic_qty[$index];
                            $depotStock->lfcl_id = 1;
                            $depotStock->cont_id = $this->currentUser->employee()->cont_id;
                            $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        } else {
                            $depotStock->dlst_qnty = $depotStock->dlst_qnty + $request->logistic_qty[$index];
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        }
                        $tripSku = TripSku::on($this->db)->where(['trip_id' => $trip->id, 'amim_id' => $sku_id])->first();
                        if ($tripSku == null) {
                            $tripSku = new TripSku();
                            $tripSku->setConnection($this->db);
                            $tripSku->trip_id = $trip->id;
                            $tripSku->amim_id = $sku_id;
                            $tripSku->troc_iqty = 0;
                            $tripSku->troc_cqty = 0;
                            $tripSku->troc_dqty = 0;
                            $tripSku->troc_lqty = $request->logistic_qty[$index];
                            $tripSku->lfcl_id = 1;
                            $tripSku->cont_id = $this->currentUser->employee()->cont_id;
                            $tripSku->aemp_iusr = $this->currentUser->employee()->id;
                            $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                            $tripSku->save();
                        } else {
                            $tripSku->troc_lqty = $request->logistic_qty[$index];
                            $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                            $tripSku->save();
                        }

                        $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $trip->dlrm_id, 'amim_id' => $sku_id])->first();
                        if ($depotStock == null) {
                            $depotStock = new DepotStock();
                            $depotStock->setConnection($this->db);
                            $depotStock->dlrm_id = $trip->dlrm_id;
                            $depotStock->amim_id = $sku_id;
                            $depotStock->dlst_qnty = $request->logistic_qty[$index];
                            $depotStock->cont_id = $trip->cont_id;
                            $depotStock->lfcl_id = 1;
                            $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        } else {
                            $depotStock->dlst_qnty = $depotStock->dlst_qnty + $request->logistic_qty[$index];
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        }
                    }
                }
                if (isset($request->trip_grv_sku)) {
                    foreach ($request->trip_grv_sku as $index => $sku_id) {
                        $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $trip->dlrm_id, 'amim_id' => $sku_id])->first();
                        if ($depotStock == null) {
                            $depotStock = new DepotStock();
                            $depotStock->setConnection($this->db);
                            $depotStock->dlrm_id = $trip->dlrm_id;
                            $depotStock->amim_id = $sku_id;
                            $depotStock->dlst_qnty = $request->logistic_qty[$index];
                            $depotStock->lfcl_id = 1;
                            $depotStock->cont_id = $this->currentUser->employee()->cont_id;
                            $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        } else {
                            $depotStock->dlst_qnty = $depotStock->dlst_qnty + $request->logistic_qty[$index];
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        }

                        $tripSku = TripGrvSku::on($this->db)->where(['trip_id' => $trip->id, 'amim_id' => $sku_id])->first();
                        if ($tripSku == null) {
                            $tripSku = new TripGrvSku();
                            $tripSku->setConnection($this->db);
                            $tripSku->trip_id = $trip->id;
                            $tripSku->amim_id = $sku_id;
                            $tripSku->trgc_iqty = 0;
                            $tripSku->trgc_cqty = 0;
                            $tripSku->trgc_dqty = 0;
                            $tripSku->trgc_bqty = $request->trip_grv_sku_bqty[$index];
                            $tripSku->trgc_gqty = $request->trip_grv_sku_gqty[$index];
                            $tripSku->lfcl_id = 1;
                            $tripSku->cont_id = $this->currentUser->employee()->cont_id;
                            $tripSku->aemp_iusr = $this->currentUser->employee()->id;
                            $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                            $tripSku->save();
                        } else {
                            $tripSku->trgc_bqty = $request->trip_grv_sku_bqty[$index];
                            $tripSku->trgc_gqty = $request->trip_grv_sku_gqty[$index];
                            $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                            $tripSku->save();
                        }

                        $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $trip->dlrm_id, 'amim_id' => $sku_id])->first();
                        if ($depotStock == null) {
                            $depotStock = new DepotStock();
                            $depotStock->setConnection($this->db);
                            $depotStock->dlrm_id = $trip->dlrm_id;
                            $depotStock->amim_id = $sku_id;
                            $depotStock->dlst_qnty = $request->trip_grv_sku_bqty[$index] + $request->trip_grv_sku_gqty[$index];
                            $depotStock->cont_id = $trip->cont_id;
                            $depotStock->lfcl_id = 1;
                            $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        } else {
                            $depotStock->dlst_qnty = $depotStock->dlst_qnty + $request->trip_grv_sku_bqty[$index] + $request->trip_grv_sku_gqty[$index];
                            $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                            $depotStock->save();
                        }

                    }
                }

                $trip->trip_cdat = date('Y-m-d');
                $trip->aemp_cusr = $this->currentUser->employee()->id;
                DB::connection($this->db)->table('tt_invc')->where('trip_id', '=', $id)->update(['invc_lock' => 0,'aemp_eusr'=>$this->currentUser->employee()->id]);
                DB::connection($this->db)->table('tt_invc')->where('trip_id', '=', $id)->where('cltn_id', '!=', 0)->update(['lfcl_id' => 26,'aemp_eusr'=>$this->currentUser->employee()->id]);
                DB::connection($this->db)->table('tt_cltn')->where('trip_id', '=', $id)->update(['lfcl_id' =>26,'aemp_eusr'=>$this->currentUser->employee()->id]);
                $trip->lfcl_id = 25;
                $trip->aemp_eusr = $this->currentUser->employee()->id;
                $trip->save();

                DB::connection($this->db)->commit();
                return redirect('/trip')->with('success', 'Trip Closed');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', 'problem' . $e);
            }

        } else {
            return redirect()->back()->with('danger', 'Please Make EOT');
        }


    }

    public function tripStatusChange(Request $request, $id, $trip_id)
    {
        $dataOrderMaster = OrderMaster::on($this->db)->findorfail($id);
        $dataTripOrders = TripOrder::on($this->db)->where(['trip_id' => $trip_id, 'ordm_id' => $id])->first();
        // dd($dataTripOrders);
        if ($dataTripOrders->lfcl_id == 23) {
            $dataOrderMaster->lfcl_id = 8;
            $dataOrderMaster->aemp_eusr = $this->currentUser->employee()->id;
            $dataOrderMaster->save();
            $dataTripOrders->lfcl_id = 8;
            $dataTripOrders->aemp_eusr = $this->currentUser->employee()->id;
            $dataTripOrders->save();
            return redirect()->back()->with('success', 'Order Status Changed');
        } else {
            return redirect()->back()->with('danger', 'Already Status changed');
        }


    }

    public function tripGStatusChange(Request $request, $id, $trip_id)
    {
        $dataOrderMaster = ReturnMaster::on($this->db)->findorfail($id);
        $dataTripOrders = TripGRV::on($this->db)->where(['trip_id' => $trip_id, 'rtan_id' => $id])->first();
        // dd($dataTripOrders);
        if ($dataTripOrders->lfcl_id == 23) {
            $dataOrderMaster->lfcl_id = 8;
            $dataOrderMaster->aemp_eusr = $this->currentUser->employee()->id;
            $dataOrderMaster->save();
            $dataTripOrders->lfcl_id = 8;
            $dataTripOrders->aemp_eusr = $this->currentUser->employee()->id;
            $dataTripOrders->save();
            return redirect()->back()->with('success', 'Order Status Changed');
        } else {
            return redirect()->back()->with('danger', 'Already Status changed');
        }


    }

    public function productAssign(Request $request, $id)
    {
        $trip = Trip::on($this->db)->findorfail($id);
        $ready = false;
        $msg = "";
        if ($trip->lfcl_id == 1) {
            DB::connection($this->db)->beginTransaction();
            try {

                if ($request->trip_order_sku != null) {
                    if (array_sum($request->trip_sku_qty) > 0) {
                        $ready = true;
                        foreach ($request->trip_order_sku as $index => $trip_order_sku) {
                            $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $trip->dlrm_id, 'amim_id' => $trip_order_sku])->first();
                            if ($depotStock != null) {
                                if ($depotStock->dlst_qnty >= $request->trip_sku_qty[$index]) {
                                    $tripSku = TripSku::on($this->db)->where(['trip_id' => $id, 'amim_id' => $trip_order_sku])->first();
                                    if ($tripSku == null) {
                                        $tripSku = new TripSku();
                                        $tripSku->setConnection($this->db);
                                        $tripSku->trip_id = $id;
                                        $tripSku->amim_id = $trip_order_sku;
                                        $tripSku->troc_iqty = $request->trip_order_sku_qty[$index];
                                        $tripSku->troc_cqty = $request->trip_sku_qty[$index];
                                        $tripSku->troc_dqty = 0;
                                        $tripSku->troc_lqty = 0;
                                        $tripSku->lfcl_id = 1;
                                        $tripSku->cont_id = $this->currentUser->employee()->cont_id;
                                        $tripSku->aemp_iusr = $this->currentUser->employee()->id;
                                        $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                                        $tripSku->save();
                                    } else {
                                        $tripSku->troc_iqty = $request->trip_order_sku_qty[$index];
                                        $tripSku->troc_cqty = $request->trip_sku_qty[$index];
                                        $tripSku->troc_dqty = 0;
                                        $tripSku->troc_lqty = 0;
                                        $tripSku->aemp_eusr = $this->currentUser->employee()->id;
                                        $tripSku->save();
                                    }
                                    $depotStock->dlst_qnty = $depotStock->dlst_qnty - $request->trip_sku_qty[$index];
                                    $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                                    $depotStock->save();
                                }
                            }

                        }

                    }
                }
                if ($request->trip_grv_sku != null) {
                    if (array_sum($request->trip_grv_sku_qty) > 0) {
                        $ready = true;
                        foreach ($request->trip_grv_sku as $index => $trip_grv_sku) {
                            $tripGrvSku = TripGrvSku::on($this->db)->where(['trip_id' => $id, 'amim_id' => $trip_grv_sku])->first();
                            if ($tripGrvSku == null) {
                                $tripGrvSku = new TripGrvSku();
                                $tripGrvSku->setConnection($this->db);
                                $tripGrvSku->trip_id = $id;
                                $tripGrvSku->amim_id = $trip_grv_sku;
                                $tripGrvSku->trgc_iqty = $request->trip_grv_sku_qty[$index];
                                $tripGrvSku->trgc_dqty = 0;
                                $tripGrvSku->trgc_cqty = 0;
                                $tripGrvSku->trgc_bqty = 0;
                                $tripGrvSku->trgc_gqty = 0;
                                $tripGrvSku->lfcl_id = 1;
                                $tripGrvSku->cont_id = $this->currentUser->employee()->cont_id;
                                $tripGrvSku->aemp_iusr = $this->currentUser->employee()->id;
                                $tripGrvSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripGrvSku->save();
                            } else {
                                $tripGrvSku->trgc_iqty = $request->trip_grv_sku_qty[$index];
                                $tripGrvSku->trgc_dqty = 0;
                                $tripGrvSku->trgc_cqty = 0;
                                $tripGrvSku->trgc_bqty = 0;
                                $tripGrvSku->trgc_gqty = 0;
                                $tripGrvSku->aemp_eusr = $this->currentUser->employee()->id;
                                $tripGrvSku->save();
                            }
                        }
                    }
                }
                if ($ready) {
                    $trip1 = Trip::on($this->db)->where(['aemp_tusr' => $trip->aemp_tusr])->whereIn('lfcl_id', [31, 20, 5, 6, 32])->first();
                    if ($trip1 == null) {
                        $dataTripOrders = TripOrder::on($this->db)->where(['trip_id' => $id, 'lfcl_id' => 8])->pluck('ordm_id')->toArray();
                        $dataTripGRVs = TripGRV::on($this->db)->where(['trip_id' => $id, 'lfcl_id' => 8])->pluck('rtan_id')->toArray();
                        DB::connection($this->db)->table('tt_ordm')->whereIn('id', $dataTripOrders)->update(['lfcl_id' => 23]);
                        DB::connection($this->db)->table('tt_trom')->where(['trip_id' => $id, 'lfcl_id' => 8])->whereIn('ordm_id', $dataTripOrders)->update(['lfcl_id' => 23]);
                        DB::connection($this->db)->table('tt_rtan')->whereIn('id', $dataTripGRVs)->update(['lfcl_id' => 23]);
                        DB::connection($this->db)->table('tt_trgm')->where(['trip_id' => $id, 'lfcl_id' => 8])->whereIn('rtan_id', $dataTripGRVs)->update(['lfcl_id' => 23]);
                        $trip->trip_vdat = date('Y-m-d');
                        $trip->aemp_vusr = $this->currentUser->employee()->id;
                        $trip->lfcl_id = 31;
                        $trip->aemp_eusr = $this->currentUser->employee()->id;
                        $trip->save();
                        $msg = 'Trip Ready to Delivery';
                    } else {
                        $msg = 'Close Previous trip';
                    }


                } else {
                    $msg = 'Product not Available to Delivery';
                }
                DB::connection($this->db)->commit();
                return redirect('/trip')->with('success', $msg);
            } catch
            (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', 'rollback' . $e);
            }
        } else {
            $msg = 'Already Verified';
            return redirect('/trip')->with('danger', $msg);
        }
    }

    public function filterOrderDetails(Request $request)
    {
        //dd($request->trip);
        // $trip = unserialize($request->trip);
        // dd($trip);
        $aemp = $request->aemp;
        $country_id = $this->currentUser->employee()->cont_id;
        $where = "1 and t1.cont_id=$country_id and t1.lfcl_id=8 and t7.id=$request->dpot_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.ordm_date between '$request->start_date' and '$request->end_date'";
        }
        if (isset($aemp)) {
            $aemp = implode(', ', $aemp);
            $where .= " AND t1.aemp_id in($aemp)";
        }


        $data = DB::connection($this->db)->select("SELECT
  t1.id        AS so_id,
  t1.ordm_ornm AS order_id,
  t1.ordm_amnt AS order_amount,
  t2.aemp_name AS emp_name,
  t2.aemp_usnm AS user_name,
  t3.site_name AS site_name,
  t1.site_id,
  t3.site_code AS site_code,
  t1.ordm_date AS order_date,
  t1.ordm_time AS order_date_time,
  t4.lfcl_name AS status_name,
  t1.lfcl_id   AS status_id,
  'Order'      AS order_type
FROM tt_ordm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
  INNER JOIN tm_dlrm AS t5 ON t1.dlrm_id = t5.id
  INNER JOIN tm_whos AS t6 ON t5.whos_id = t6.id
  INNER JOIN tm_dpot AS t7 ON t6.dpot_id = t7.id
WHERE $where;");

        return $data;

    }

    public function filterGrvDetails(Request $request)
    {
        $aemp = $request->aemp;
        $trip_id = $request->trip_id;
        $dpot_id = $request->dpot_id;
        $country_id = $this->currentUser->employee()->cont_id;
        $where = "1 and t1.cont_id=$country_id and t1.lfcl_id=8 and t8.optp_id = 2 and t7.id=$request->dpot_id";
        $where1 = "1 and t1.cont_id=$country_id and t3.lfcl_id=8 and t10.optp_id = 1 and t9.id=$request->dpot_id and t1.trip_id = $trip_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.rtan_date between '$request->start_date' and '$request->end_date'";
            $where1 .= " AND t3.rtan_date between '$request->start_date' and '$request->end_date'";
        }
        if (isset($aemp)) {
            $aemp = implode(', ', $aemp);
            $where .= " AND t1.aemp_id in($aemp)";
            $where1 .= " AND t3.aemp_id in($aemp)";
        }


        $data = DB::connection($this->db)->select("SELECT
  t1.id        AS so_id,
  t1.rtan_rtnm AS order_id,
  t1.rtan_amnt AS order_amount,
  t2.aemp_name AS emp_name,
  t2.aemp_usnm AS user_name,
  t3.site_name AS site_name,
  t1.site_id,
  t3.site_code AS site_code,
  t1.rtan_date AS order_date,
  t1.rtan_time AS order_date_time,
  t4.lfcl_name AS status_name,
  t1.lfcl_id   AS status_id,
  'GRV'      AS order_type
FROM tt_rtan AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
  INNER JOIN tm_dlrm AS t5 ON t1.dlrm_id = t5.id
  INNER JOIN tm_whos AS t6 ON t5.whos_id = t6.id
  INNER JOIN tm_dpot AS t7 ON t6.dpot_id = t7.id
INNER JOIN tl_stcm AS t8 ON t1.site_id = t8.site_id AND t1.acmp_id = t8.acmp_id
WHERE $where
UNION ALL 
SELECT
  t3.id        AS so_id,
  t3.rtan_rtnm AS order_id,
  t3.rtan_amnt AS order_amount,
  t4.aemp_name AS emp_name,
  t4.aemp_usnm AS user_name,
  t5.site_name AS site_name,
  t3.site_id,
  t5.site_code AS site_code,
  t3.rtan_date AS order_date,
  t3.rtan_time AS order_date_time,
  t6.lfcl_name AS status_name,
  t3.lfcl_id   AS status_id,
  'GRV'        AS order_type
FROM tt_trom AS t1
  INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
  INNER JOIN tt_rtan AS t3 ON t2.site_id = t3.site_id AND t2.acmp_id = t3.acmp_id AND t2.aemp_id = t3.aemp_id
  INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
  INNER JOIN tm_site AS t5 ON t3.site_id = t5.id
  INNER JOIN tm_lfcl AS t6 ON t3.lfcl_id = t6.id
  INNER JOIN tm_dlrm AS t7 ON t3.dlrm_id = t7.id
  INNER JOIN tm_whos AS t8 ON t7.whos_id = t8.id
  INNER JOIN tm_dpot AS t9 ON t8.dpot_id = t9.id
  INNER JOIN tl_stcm AS t10 ON t2.site_id = t10.site_id AND t2.acmp_id = t10.acmp_id
WHERE  $where1;
");
        return $data;

    }
}