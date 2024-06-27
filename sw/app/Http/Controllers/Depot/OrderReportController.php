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
use App\MasterData\Country;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class OrderReportController extends Controller
{
    private $access_key = 'OrderReportController';
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
    public function orderSummary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $emp_id = $this->currentUser->employee()->id;

            $acmps = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$emp_id'");
            $lfcl_list=DB::connection($this->db)->select("SELECT DISTINCT id, lfcl_name, lfcl_code FROM `tm_lfcl` WHERE id in (1,2,11,14,15,16,17,18,21,38,39,34,44) ORDER BY lfcl_name");
            return view('Depot.OrderReport.order_summary', compact( 'acmps'))->with('permission', $this->userMenu)->with('lfcl_list', $lfcl_list);
        } else {
            return view('theme.access_limit');
        }

    }
    public function grvSummary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Depot.OrderReport.grv_summary')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }
    public function pushToRoutePlan(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if (isset($request->so_id)) {
                foreach ($request->so_id as $index => $lineId) {
                    DB::connection($this->db)->table('tt_ordm')->where(['id' => $lineId])->update(['lfcl_id' => 8]);
                }
            }
            return redirect()->back()->with('success', 'Successfully Uploaded');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }


    }
    public function filterOrderSummary(Request $request)
    {

        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id = $this->currentUser->employee()->id;
        // $where = "1 AND t1.cont_id=$country_id and t1.aemp_id=$emp_id";
//        $where = "1 AND t1.cont_id=$country_id";
//        if ($request->start_date != "" && $request->end_date != "") {
//            $where .= " AND t2.ordm_date between '$request->start_date' and '$request->end_date'";
//        }
//        if ($request->start_date != "" && $request->end_date != "") {
//            $where .= " AND t2.ordm_date between '$request->start_date' and '$request->end_date'";
//        }
//
//        $data = DB::connection($this->db)->select("SELECT
//                t2.id        AS so_id,
//                t2.ordm_ornm    order_id,
//                t2.ordm_amnt AS order_amount,
//                t3.aemp_name AS emp_name,
//                t3.aemp_usnm AS user_name,
//                t4.site_name AS site_name,
//                t2.site_id,
//                t4.site_code AS site_code,
//                t2.ordm_date    order_date,
//                t2.ordm_time AS order_date_time,
//                t5.lfcl_name AS status_name,
//                t2.lfcl_id   AS status_id,
//                'Order'      AS order_type,
//                t1.cont_id      AS cont_id
//                FROM tl_srdi AS t1
//                INNER JOIN tt_ordm AS t2 ON t1.dlrm_id = t2.dlrm_id
//                INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
//                INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
//                INNER JOIN tm_lfcl AS t5 ON t2.lfcl_id = t5.id
//                WHERE $where  group by t2.ordm_ornm;");
//
//        return $data;
        $query =  DB::connection($this->db)->table('tl_srdi AS t1')

            ->selectRaw("t2.id AS so_id,
                t2.ordm_ornm    order_id,
                t2.ordm_amnt AS order_amount,
                t3.aemp_name AS emp_name,
                t3.aemp_usnm AS user_name,
                t4.site_name AS site_name,
                t2.site_id,
                t4.site_code AS site_code,
                t2.ordm_date    order_date,
                t2.ordm_time AS order_date_time,
                t5.lfcl_name AS status_name,
                t2.lfcl_id   AS status_id,
                'Order'      AS order_type,
                t6.TRIP_NO,
                t6.DM_CODE     AS DM_ID,
                t1.cont_id      AS cont_id")
            ->join('tt_ordm AS t2' ,'t1.dlrm_id' , 't2.dlrm_id')
            ->join('tm_aemp AS t3' ,'t2.aemp_id' , 't3.id')
            ->join('tm_site AS t4' ,'t2.site_id' , 't4.id')
            ->join('tm_lfcl AS t5' ,'t2.lfcl_id' , 't5.id')
            ->leftJoin('dm_trip_master AS t6','t2.ordm_ornm','t6.ORDM_ORNM')
            ->where('t1.cont_id', $country_id)
            ->orderBy('t2.ordm_ornm', 'ASC')
            ->orderBy('t3.aemp_usnm', 'ASC');
        if($request->start_date != "" && $request->end_date != "") {
            $query->whereBetween('t2.ordm_date', [$request->start_date, $request->end_date]);
        }
        if($request->ordm_ornm) {
            $query->where('t2.ordm_ornm', $request->ordm_ornm);
        }
        if($request->site_code) {
            $query->where('t4.site_code', $request->site_code);
        }
        if($request->slgp_id) {
            $query->where('t2.slgp_id', $request->slgp_id);
        }
        if($request->emp_id) {
            $query->where('t3.aemp_usnm', $request->emp_id);
        }
        if($request->lfcl_id) {
            $query->where('t2.lfcl_id', $request->lfcl_id);
        }
        return $query->groupBy('t2.ordm_ornm')->get();
    }

    public function cancelOrder($id,$start_date,$end_date){
        DB::connection($this->db)->table('tt_ordm')->where(['ordm_ornm' => $id])->update(['lfcl_id' => 18]);
        $date=date('Y-m-d');
        
        $country_id = $this->currentUser->employee()->cont_id;
        $emp_id = $this->currentUser->employee()->id;
        DB::connection($this->db)->select("Insert into tl_ordm_cancel_log(cancel_date,ordm_ornm,source,aemp_iusr)values('$date','$id','W','$emp_id')");
        $where = "1 AND t1.cont_id=$country_id and t1.aemp_id=$emp_id";
        if ($start_date != "" && $end_date != "") {
            $where .= " AND t2.ordm_date between '$start_date' and '$end_date'";
        }
        $data = DB::connection($this->db)->select("SELECT
                t2.id        AS so_id,
                t2.ordm_ornm    order_id,
                t2.ordm_amnt AS order_amount,
                t3.aemp_name AS emp_name,
                t3.aemp_usnm AS user_name,
                t4.site_name AS site_name,
                t2.site_id,
                t4.site_code AS site_code,
                t2.ordm_date    order_date,
                t2.ordm_time AS order_date_time,
                t5.lfcl_name AS status_name,
                t2.lfcl_id   AS status_id,
                'Order'      AS order_type,
                t1.cont_id      AS cont_id
                FROM tl_srdi AS t1
                INNER JOIN tt_ordm AS t2 ON t1.dlrm_id = t2.dlrm_id
                INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
                INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
                INNER JOIN tm_lfcl AS t5 ON t2.lfcl_id = t5.id
                WHERE $where;");
        
        return $data;

    }


}