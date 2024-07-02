<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Excel;
use App\DataExport\SRClassOrder;

class ItemReportController extends Controller
{
    private $access_key = 'ItemReportController';
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


    public function srActivitySummary()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $results = [];
        return view('report.activity.sr_activity')
            ->with('acmp', $acmp)
            ->with('region', $region);
    }


    public function srProductivitySummary()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $results = [];
        return view('report.activity.sr_productivity')
            ->with('acmp', $acmp)
            ->with('region', $region);
    }

    public function srProductivitySummaryFilter(Request $request)
    {
        $empId = $this->currentUser->employee()->id;


        $zone_id = $request->zone_id;
        $dirg_id = $request->dirg_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;


        /*$zone_id = '';
        $sales_group_id = '8';
        $dirg_id = '';
        $start_date = '2021-06-23';
        $end_date = '2021-06-23';
        $acmp_id = '7';*/

        $q1 = "";
        $q2 = "";
        $q3 = "";
        if ($zone_id != "") {
            $q1 = "AND t3.zone_id = '$zone_id'";
        }
        if ($sales_group_id != "") {
            $q2 = "AND t2.slgp_id = '$sales_group_id'";
        }

        if ($dirg_id != "") {
            $q3 = "AND t3.dirg_id = '$dirg_id'";
        }

        $query = "SELECT
  t1.date,
  t1.zone_id,
  concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
  concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
  concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
  t1.slgp_id                                  AS slgp_id,
  t1.aemp_code                                AS aemp_id,
  t1.aemp_name                                AS aemp_name,
  t1.aemp_mobile                              AS aemp_mobile,
  concat(t1.rout_id, ' - ', t1.rout_name)     AS rout_name,
  t1.t_outlet                                 AS t_outlet,
  (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
  SUM(t1.s_outet)                             AS s_outet,
  round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
  round(SUM(t1.t_amnt), 2)                    AS t_amnt,
  t1.inTime                                   AS inTime,
  t1.firstOrTime                              AS firstOrTime,
  t1.lastOrTime                               AS lastOrTime,
  TIMEDIFF(t1.lastOrTime, t1.firstOrTime)     AS workTime
FROM `tt_aemp_summary` t1
  INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND
      t3.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t2.acmp_id = '$acmp_id'
GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
ORDER BY t2.slgp_code, t3.zone_code";


        //echo $query;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));

        return $srData;
    }

    public function srNonProductivitySummary()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $results = [];
        return view('report.activity.non_productive_report')
            ->with('acmp', $acmp)
            ->with('region', $region);
    }

    public function srNonProductivitySummaryFilter(Request $request)
    {
        //dd($request);
        $empId = $this->currentUser->employee()->id;

        $zone_id = $request->zone_id;
        $dirg_id = $request->dirg_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;


        /*$zone_id = '';
        $sales_group_id = '8';
        $dirg_id = '';
        $start_date = '2021-06-23';
        $end_date = '2021-06-23';
        $acmp_id = '7';*/

        $q1 = "";
        $q2 = "";
        $q3 = "";
        if ($zone_id != "") {
            $q1 = "AND t3.zone_id = '$zone_id'";
        }
        if ($sales_group_id != "") {
            $q2 = "AND t2.slgp_id = '$sales_group_id'";
        }

        if ($dirg_id != "") {
            $q3 = "AND t3.dirg_id = '$dirg_id'";
        }


        $query = "SELECT
  t1.date,
  t1.zone_id,
  concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
  concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
  concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
  t1.slgp_id                                  AS slgp_id,
  t1.aemp_code                                AS aemp_id,
  t1.aemp_name                                AS aemp_name,
  t1.aemp_mobile                              AS aemp_mobile,
  t1.inTime                                   AS inTime
FROM `tt_aemp_summary` t1
  INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t1.t_amnt='0' AND 
      t3.aemp_id = '$empId' AND t1.atten_atyp='1'" . $q1 . $q2 . $q3 . " AND t2.acmp_id = '$acmp_id'
GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
ORDER BY t3.zone_code, t2.slgp_code";


        //echo $query;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));

        return $srData;
    }

    public function srClassWiseOrderSummary()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $results = [];
        return view('report.activity.sr_class_wise_order_summary')
            ->with('acmp', $acmp)
            ->with('region', $region);
    }

    public function dataExportAttendanceData(Request $request)
    {
        /*if ($this->userMenu->wsmu_crat) {*/
        $tt = array();
        $topLine = array("#", "Date", "Zone", "SR ID", "SR Name");
        // $topLineID = array("#", "Date", "Zone", "SR ID", "SR Name");
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key= 0"));
       /* $query = DB::connection($this->db)->select("CREATE TEMPORARY TABLE sOrdrSumm as SELECT t1.`aemp_id`,t1.`aemp_code`,t1.`aemp_name`,t1.`zone_id`,
 t2.zone_name, t1.`itcl_id`, t1.`itcl_name`, SUM(t1.`ordd_oamt`) AS ordr, COUNT(DISTINCT t1.order_id) AS memo
 FROM `tt_aemp_ordd_summ` t1 INNER JOIN tm_zone t2 ON t1.`zone_id`=t2.id WHERE
 `date` BETWEEN '2021-07-02' AND '2021-07-02' AND slgp_id='9' GROUP BY t1.aemp_id, t1.`itcl_id`");*/
        $iClass = DB::connection($this->db)->select("SELECT `id`,concat(`itcl_name`,' (Amount - Memo)') AS itcl_name,
`itcl_code` FROM `tm_itcl` WHERE `itgp_id`='1'");
        foreach ($iClass as $row) {
            $topLine[] = $row->itcl_name;
            $topLineID[] = $row->id;
            $topLi = $row->id;
           /* $iClass = DB::connection($this->db)->select("select ordr, memo from sOrdrSumm WHERE itcl_id='400'");
            dd($iClass);*/

        }
        $topLine[] = "Total";
        //    $topLineID[] = "Total";

        return Excel::download(SRClassOrder::create($topLine, $topLineID, $request->start_date, $request->end_date, $request->acmp_id), 'Attendance_data_' . date("Y-m-d H:i:s") . '.xlsx');

        /*} else {
            return redirect()->back()->with('danger', 'Access Limited');
        }*/
    }

    public function dataExportAttendanceData2(Request $request)
    {
        $tt = array();
        $topLine = array("#", "Date", "Zone", "SR ID", "SR Name");
        // $topLineID = array("#", "Date", "Zone", "SR ID", "SR Name");
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key= 0"));
        /* $query = DB::connection($this->db)->select("CREATE TEMPORARY TABLE sOrdrSumm as SELECT t1.`aemp_id`,t1.`aemp_code`,t1.`aemp_name`,t1.`zone_id`,
  t2.zone_name, t1.`itcl_id`, t1.`itcl_name`, SUM(t1.`ordd_oamt`) AS ordr, COUNT(DISTINCT t1.order_id) AS memo
  FROM `tt_aemp_ordd_summ` t1 INNER JOIN tm_zone t2 ON t1.`zone_id`=t2.id WHERE
  `date` BETWEEN '2021-07-02' AND '2021-07-02' AND slgp_id='9' GROUP BY t1.aemp_id, t1.`itcl_id`");*/
        /*$iClass = DB::connection($this->db)->select("SELECT `id`,concat(`itcl_name`,' (Amount - Memo)') AS itcl_name,
`itcl_code` FROM `tm_itcl` WHERE `itgp_id`='1'");*/
        /*foreach ($iClass as $row) {
            $topLine[] = $row->itcl_name;
            $topLineID[] = $row->id;
            $topLi = $row->id;

        }*/
        $iClass = DB::connection($this->db)->select("SELECT `date`, `order_no`, `site_id`, `aemp_id`, `aemp_code`, 
`aemp_name`, `aemp_mob`, `zone_id`, `slgp_id`, `amim_id`, `amim_code`, `amim_name` FROM `tt_aemp_ordd_summ` LIMIT 10");

        $topLine[] = "Total";
        /*foreach ($iClass as $row) {
            $topLineID[] = $row;
        }*/
           // $topLineID[] = $iClass;

        return Excel::download(SRClassOrder::create($topLine,  $request->start_date, $request->end_date, $request->acmp_id), 'Attendance_data_' . date("Y-m-d H:i:s") . '.xlsx');

    }
	
// public function unsoldItemSummary(){
		// return "This method is under development....";
	// }


}