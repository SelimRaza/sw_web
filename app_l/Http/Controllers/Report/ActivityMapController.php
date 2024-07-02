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

class ActivityMapController extends Controller
{
    private $access_key = 'ActivityMapController';
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

    public function summary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $groups = DB::connection($this->db)->select("SELECT
   id,
  slgp_name,
  slgp_code
FROM tm_slgp ");
            return view('report.activity.summary')->with('permission', $this->userMenu)->with('groups', $groups);
        } else {
            return view('theme.access_limit');
        }

    }


    public function summaryLocAttenNote(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $attendances = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t1.image,
  t3.name as role_name,
  t4.name as att_type
FROM tblt_attendance AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tbld_role as t3 ON t2.role_id=t3.id
  INNER JOIN tbld_attendance_type as t4 ON t1.attendance_type_id=t4.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");

            $notes = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  t1.title,
  t1.note,
  DATE_FORMAT(t1.date_time, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t4.name as note_type
FROM tblt_note AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  LEFT JOIN tblt_note_image_mapping as t3 ON t1.id=t3.note_id
  INNER JOIN tbld_note_type as t4 ON t1.note_type_id=t4.id
WHERE t1.emp_id=$id and t1.date='$date'
GROUP BY t1.id,t1.title,t1.note,t1.emp_id,t2.name,t1.date,t1.lat,t1.lon,t1.date_time,t4.name
ORDER BY t1.date ASC");
            $locations = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.times_stamp, '%l.%i%p') AS time,
  t1.lat,
  t1.lon
FROM tblt_location_history AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");

            return view('report.activity.summary_map_all')->with("attendances", $attendances)->with("notes", $notes)->with("locations", $locations)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

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

    public function srActivitySummaryFilterdemo_22(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        /*$zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;*/

        $zone_id = '';
        $sales_group_id = '';
        $start_date = '2021-06-15';
        $end_date = '2021-06-17';
        $acmp_id = $request->acmp_id;

        $temp_s_list = "SELECT
  t1.date,
  t1.dirg_name,
  t1.zone_name,
  t1.slgp_name,
  t4.t_sr,
  t2.p_sr,
  t3.l_sr,
  t1.pro_sr,
  t1.t_outlet,
  t1.c_outlet,
  t1.s_outet,
  t1.lpc,
  t1.t_amnt
FROM (SELECT
        t1.date,
        t1.zone_id,
        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
        concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
        concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
        t1.slgp_id,
        count(DISTINCT t1.aemp_id) as pro_sr,
        SUM(t1.t_outlet)                            AS t_outlet,
        (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
        SUM(t1.s_outet)                             AS s_outet,
        round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
        round(SUM(t1.t_amnt), 2)                    AS t_amnt
      FROM `tt_aemp_summary` t1
        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
      WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
            t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
      GROUP BY t1.zone_id, t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS p_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t2 ON t1.zone_id = t2.zone_id AND t1.slgp_id = t2.slgp_id AND t1.date = t2.date
  LEFT JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS l_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t3 ON t1.zone_id = t3.zone_id AND t1.slgp_id = t3.slgp_id AND t1.date = t3.date
  INNER JOIN


  (SELECT
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.id) AS t_sr
   FROM tm_aemp t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
        echo $temp_s_list;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($temp_s_list));

        return $srData;
        //dd($srData);
    }

    public function srActivitySummaryFilter(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query2 = "SELECT t1.id FROM `tm_aemp` t1 
INNER JOIN user_group_permission t2 ON t1.`slgp_id` = t2.slgp_id 
INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
WHERE t1.`slgp_id` LIKE '%$sales_group_id%' AND t1.`zone_id` LIKE '%$zone_id%' AND t1.lfcl_id = '1' 
AND t1.role_id = '1' AND t2.aemp_id='$empId' AND t3.aemp_id='$empId'";

        $query1 = "SELECT
  concat(t2.slgp_code, ' - ', t2.slgp_name) AS slgp,
  concat(t3.zone_code, ' - ', t3.zone_name) AS zone,
  COUNT(t1.id) as total_sr
FROM `tm_aemp` t1 INNER JOIN tm_slgp t2 ON t1.`slgp_id` = t2.id
  INNER JOIN tm_zone t3 ON t1.zone_id = t3.id
WHERE t1.`slgp_id` = '$sales_group_id' AND t1.`zone_id` = '$zone_id' AND t1.lfcl_id = '1' AND t1.role_id = '1'
GROUP BY t1.slgp_id, t1.zone_id";

        $query3 = "SELECT COUNT(id) as ss, attn_date FROM `tt_attn` WHERE `aemp_id` in ( SELECT t1.id
FROM `tm_aemp` t1 INNER JOIN tm_slgp t2 ON t1.`slgp_id` = t2.id
  INNER JOIN tm_zone t3 ON t1.zone_id = t3.id
WHERE t1.`slgp_id` = '$sales_group_id' AND t1.`zone_id` = '$zone_id' AND t1.lfcl_id = '1' AND t1.role_id = '1'
GROUP BY t1.slgp_id, t1.zone_id ) AND attn_date=curdate() GROUP BY attn_date";
        //$data3 = DB::connection($this->db)->select($query3);


        //$empId = $this->currentUser->employee()->id;
        //$acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        //$region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $data = DB::connection($this->db)->select($query3);
        //$data2 = DB::connection($this->db)->select($query2);


        /*$query3 = "SELECT COUNT(id), attn_date FROM `tt_attn` WHERE `aemp_id` in ( SELECT t1.id
FROM `tm_aemp` t1 INNER JOIN tm_slgp t2 ON t1.`slgp_id` = t2.id
  INNER JOIN tm_zone t3 ON t1.zone_id = t3.id
WHERE t1.`slgp_id` = '$sales_group_id' AND t1.`zone_id` = '$zone_id' AND t1.lfcl_id = '1' AND t1.role_id = '1'
GROUP BY t1.slgp_id, t1.zone_id ) AND attn_date=curdate()";
        $data3 = DB::connection($this->db)->select($query3);*/
        $results = [];

        /* return view('report.activity.sr_activity')
             ->with('acmp', $acmp)
             ->with('region', $region);*/
        return $data;
    }

    public function srActivitySummaryFilterdemo_1(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;


        $temp_s_list = "SELECT
  t1.date,
  t1.dirg_name,
  t1.zone_name,
  t1.slgp_name,
  t4.t_sr,
  t2.p_sr,
  t3.l_sr,
  t1.pro_sr,
  t1.t_outlet,
  t1.c_outlet,
  t1.s_outet,
  t1.lpc,
  t1.t_amnt
FROM (SELECT
        t1.date,
        t1.zone_id,
        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
        concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
        concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
        t1.slgp_id,
        count(DISTINCT t1.aemp_id) as pro_sr,
        SUM(t1.t_outlet)                            AS t_outlet,
        (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
        SUM(t1.s_outet)                             AS s_outet,
        round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
        round(SUM(t1.t_amnt), 2)                    AS t_amnt
      FROM `tt_aemp_summary` t1
        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
      WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
            t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
      GROUP BY t1.zone_id, t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS p_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t2 ON t1.zone_id = t2.zone_id AND t1.slgp_id = t2.slgp_id AND t1.date = t2.date
  LEFT JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS l_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t3 ON t1.zone_id = t3.zone_id AND t1.slgp_id = t3.slgp_id AND t1.date = t3.date
  INNER JOIN


  (SELECT
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.id) AS t_sr
   FROM tm_aemp t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
        //echo $temp_s_list;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($temp_s_list));

        return $srData;
        //dd($srData);
    }


    public function srActivitySummaryFilterdemo_2(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        /*$zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;*/

        $zone_id = '';
        $sales_group_id = '';
        $start_date = '2021-06-15';
        $end_date = '2021-06-17';
        $acmp_id = $request->acmp_id;

        $temp_s_list = "SELECT
  t1.date,
  t1.dirg_name,
  t1.zone_name,
  t1.slgp_name,
  t4.t_sr,
  t2.p_sr,
  t3.l_sr,
  t1.pro_sr,
  t1.t_outlet,
  t1.c_outlet,
  t1.s_outet,
  t1.lpc,
  t1.t_amnt
FROM (SELECT
        t1.date,
        t1.zone_id,
        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
        concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
        concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
        t1.slgp_id,
        count(DISTINCT t1.aemp_id) as pro_sr,
        SUM(t1.t_outlet)                            AS t_outlet,
        (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
        SUM(t1.s_outet)                             AS s_outet,
        round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
        round(SUM(t1.t_amnt), 2)                    AS t_amnt
      FROM `tt_aemp_summary` t1
        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
      WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
            t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t2.acmp_id='$acmp_id'
      GROUP BY t1.zone_id, t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS p_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t2 ON t1.zone_id = t2.zone_id AND t1.slgp_id = t2.slgp_id AND t1.date = t2.date
  LEFT JOIN

  (SELECT
     t1.date,
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.aemp_id) AS l_sr
   FROM tt_aemp_summary t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id, t1.date) t3 ON t1.zone_id = t3.zone_id AND t1.slgp_id = t3.slgp_id AND t1.date = t3.date
  INNER JOIN


  (SELECT
     t1.zone_id,
     t1.slgp_id,
     count(DISTINCT t1.id) AS t_sr
   FROM tm_aemp t1
     INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
     INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
   WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
         t2.slgp_id LIKE '%$sales_group_id%' AND t3.zone_id LIKE '%$zone_id%' AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
   GROUP BY t1.zone_id, t1.slgp_id) t4 ON t1.zone_id = t4.zone_id AND t1.slgp_id = t4.slgp_id";
        //echo $temp_s_list;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($temp_s_list));

        return $srData;
        //dd($srData);
    }


    public function srActivitySummaryFilterdemo(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        /*$zone_id = '';
        $sales_group_id = '9';
        $start_date = '2021-06-17';
        $end_date = '2021-06-17';
        $acmp_id = '6';*/

        $acmp_id = $request->acmp_id;
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $result = [];

        /*        $productList = "CREATE TEMPORARY TABLE tempSrList as SELECT t1.id, t4.slgp_name, t4.slgp_code, t5.zone_name, t5.zone_code FROM `tm_aemp` t1
        INNER JOIN tm_slgp t4 ON t1.`slgp_id` = t4.id
        INNER JOIN tm_zone t5 ON t1.zone_id = t5.id
        INNER JOIN user_group_permission t2 ON t1.`slgp_id` = t2.slgp_id
        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
        WHERE t1.`slgp_id` LIKE '%$sales_group_id%' AND t1.`zone_id` LIKE '%$zone_id%' AND t1.lfcl_id = '1'
        AND t1.role_id = '1' AND t2.aemp_id='$empId' AND t3.aemp_id='$empId'";*/

        $temp_s_list = "CREATE TEMPORARY TABLE tempSrList as SELECT t1.id, t2.slgp_name, t2.slgp_code, t3.zone_name, t3.zone_code 
FROM `tm_aemp` t1 
INNER JOIN user_group_permission t2 ON t1.`slgp_id` = t2.slgp_id 
INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
WHERE t1.`slgp_id` LIKE '%$sales_group_id%' AND t1.`zone_id` LIKE '%$zone_id%' AND t1.lfcl_id = '1'
 AND t1.role_id = '1' AND t2.aemp_id='$empId' AND t3.aemp_id='$empId' and t2.acmp_id='$acmp_id'";

        DB::connection($this->db)->select(DB::raw("SET SQL_REQUIRE_PRIMARY_KEY = 0"));
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        DB::connection($this->db)->select(DB::raw($temp_s_list));


        $tt = "SELECT DISTINCT id FROM tempSrList";

        $srData = DB::connection($this->db)->select($tt);
        foreach ($srData as $row) {
            $srList[] = $row->id;
        }

        $tempAttnList = "CREATE TEMPORARY TABLE tempAttnList as SELECT DISTINCT `aemp_id`, `attn_date`, `atten_atyp` FROM `tt_attn` WHERE `aemp_id` IN (" . implode(',', $srList) . " ) AND attn_date >= '$start_date' AND attn_date <= '$end_date' group by aemp_id, attn_date";
        $tempOrderList = "CREATE TEMPORARY TABLE tempOrderList as SELECT `ordm_ornm`,`aemp_id`,`site_id`,`rout_id`,`ordm_amnt`,`ordm_icnt`,`ordm_date` FROM `tt_ordm` WHERE `aemp_id` IN (" . implode(',', $srList) . ") AND ordm_date >= '$start_date' AND `ordm_date` <='$end_date'";

        DB::connection($this->db)->select($tempAttnList);
        DB::connection($this->db)->select($tempOrderList);

        $query1 = DB::connection($this->db)->select("SELECT DISTINCT attn_date FROM tempAttnList");
        $query2 = DB::connection($this->db)->select("SELECT slgp_code, slgp_name, zone_name, zone_code, count(id) AS t_sr, 
concat(slgp_code,' - ', slgp_name) AS s_name, concat(zone_code, ' - ', zone_name) AS z_name FROM tempSrList GROUP BY zone_code, slgp_code");
        $i = 0;

        foreach ($query1 as $t_date) {

            foreach ($query2 as $t_sr) {
                $date = $t_date->attn_date;
                $dayName = date('l', strtotime($date));
                $zone = $t_sr->zone_code;
                $groups = $t_sr->slgp_code;
                $pres_sr = DB::connection($this->db)->select("select count(aemp_id) as p_sxr from tempAttnList WHERE aemp_id IN (select id from tempSrList where slgp_code='$groups' and zone_code='$zone') and attn_date = '$date' and atten_atyp='1'");
                $l_sr = DB::connection($this->db)->select("select count(aemp_id) as l_sxr from tempAttnList WHERE aemp_id IN (select id from tempSrList where slgp_code='$groups' and zone_code='$zone') and attn_date = '$date' and atten_atyp!='1'");
                $pro_sr = DB::connection($this->db)->select("select count(DISTINCT aemp_id) as pro_sr, sum(ordm_amnt) as t_amt, sum(ordm_icnt) as t_sku, count(ordm_ornm) as t_memo from tempOrderList WHERE aemp_id IN (select id from tempSrList where slgp_code='$groups' and zone_code='$zone') and ordm_date = '$date'");
                $n_pro_sr = DB::connection($this->db)->select("SELECT COUNT(DISTINCT site_id) as v_out FROM `tt_npro` WHERE `aemp_id` IN (select id from tempSrList where slgp_code='$groups' and zone_code='$zone') AND `npro_date`='$date'");
                $total_site = DB::connection($this->db)->select("SELECT sum(t_site) as t_site FROM `ss_aemp_site` WHERE `aemp_id` IN (select id from tempSrList where slgp_code='$groups' and zone_code='$zone') AND `rpln_day`='$dayName'");
                $present = $pres_sr[0]->p_sxr;
                $result[$i][0] = $t_date->attn_date;
                $result[$i][1] = $t_sr->s_name;
                $result[$i][2] = $t_sr->z_name;
                $result[$i][3] = $t_sr->t_sr;
                $result[$i][4] = $present;
                $result[$i][5] = $l_sr[0]->l_sxr;
                $result[$i][6] = $pro_sr[0]->pro_sr;
                $result[$i][7] = $pro_sr[0]->t_amt;
                $result[$i][8] = $pro_sr[0]->t_sku;
                $result[$i][9] = $pro_sr[0]->t_memo;
                $result[$i][10] = $n_pro_sr[0]->v_out;
                $result[$i][11] = $total_site[0]->t_site;
                $i++;
                // dd($pres_sr);
            }

        }
        return $result;
    }

    public function getGroup(Request $request)
    {
        $acmp_id = $request->slgp_id;
        $empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId' and acmp_id='$acmp_id'");


    }

    public function getZone(Request $request)
    {
        $dirg_id = $request->dirg_id;
        $empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");


    }

    public function getSr(Request $request)
    {
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        //$empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT `aemp_usnm`,`aemp_name`,`id` FROM `tm_aemp` WHERE `zone_id`='$zone_id' AND `slgp_id`='$sales_group_id' AND lfcl_id='1' AND edsg_id='1'");


    }

    public function getItemClass(Request $request)
    {
        $itgp_id = $request->itgp_id;

        return $companies = DB::connection($this->db)->select("SELECT `id`,`itcl_name`,`itcl_code` FROM `tm_itcl` WHERE `itgp_id`='$itgp_id' AND lfcl_id='1'");


    }

    public function getItem(Request $request)
    {
        /*$sales_group_id = $request->sales_group_id;
        $class_id = $request->class_id;*/
        $sales_group_id = '36';
        $class_id = '820';

        return $companies = DB::connection($this->db)->select("SELECT t1.`id`, t1.`amim_code`, t1.`amim_name` FROM `tm_amim` t1 INNER JOIN tm_itcl t2 ON t1.itcl_id=t2.id WHERE t1.`itcl_id`='$class_id' AND t1.lfcl_id='1'");


    }


    public function filterActivitySymmary(Request $request)
    {
        $country_id = $this->currentUser->cont_id;
        $where = "1 and t1.cont_id=$country_id";
        $where1 = "1 ";

        /*if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.attn_date between '$request->start_date' and '$request->end_date'";
        }*/
        if (isset($request->user_name)) {
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->user_name])->first();
            $where .= " AND t1.aemp_id = $employee->id";
            $where1 .= " AND t1.aemp_id = $employee->id";
        }
        if (isset($request->sales_group_id)) {
            $where .= " AND t11.slgp_id= '$request->sales_group_id'";
        }
        $query1 = "SELECT
  t2.aemp_usnm                              AS user_name,
  t1.aemp_id                                AS emp_id,
  t2.aemp_name                              AS name,
  t1.attn_date                              AS date,
  t9.att_count                              AS att_count,
  t7.note_count                             AS note_count,
  t5.site_count                             AS visit_count,
  t6.site_count                             AS prod_count,
  t10.order_amount                          AS order_amount,
  t10.item_count                            AS item_count,
  DATE_FORMAT(MIN(t1.attn_time), '%l.%i%p') AS start_time,
  DATE_FORMAT(MAX(t1.attn_time), '%l.%i%p') AS end_time,
  t4.edsg_name                              AS role_id
FROM tt_attn AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_edsg AS t4 ON t2.edsg_id = t4.id
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.site_id) AS site_count,
               t1.ssvh_date      AS DATE
             FROM th_ssvh AS t1
              WHERE t1.ssvh_date BETWEEN '$request->start_date' and '$request->end_date' and $where1
             GROUP BY t1.aemp_id, t1.ssvh_date) AS t5 ON t1.aemp_id = t5.aemp_id AND t1.attn_date = t5.date
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.site_id) AS site_count,
               t1.ssvh_date      AS DATE
             FROM th_ssvh AS t1
             WHERE t1.ssvh_ispd = 1 and  t1.ssvh_date BETWEEN '$request->start_date' and '$request->end_date'  and $where1
             GROUP BY t1.aemp_id, t1.ssvh_date) AS t6 ON t1.aemp_id = t6.aemp_id AND t1.attn_date = t6.date
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.aemp_id) AS note_count,
               t1.note_date      AS DATE
             FROM tt_note AS t1
              WHERE t1.note_date BETWEEN '$request->start_date' and '$request->end_date'  and $where1
             GROUP BY t1.aemp_id, t1.note_date) AS t7 ON t1.aemp_id = t7.aemp_id AND t1.attn_date = t7.date
  LEFT JOIN (SELECT
               t1.aemp_id,
               count(t1.aemp_id) AS att_count,
               t1.attn_date
             FROM tt_attn AS t1
              WHERE t1.attn_date BETWEEN '$request->start_date' and '$request->end_date'  and $where1
             GROUP BY t1.aemp_id, t1.attn_date) AS t9 ON t1.aemp_id = t9.aemp_id AND t1.attn_date = t9.attn_date
  LEFT JOIN (SELECT
               t1.aemp_id,
               sum(t1.ordm_amnt) AS order_amount,
               sum(t1.ordm_icnt) AS item_count,
               t1.ordm_date
             FROM tt_ordm AS t1
              WHERE t1.ordm_date BETWEEN '$request->start_date' and '$request->end_date'  and $where1
             GROUP BY t1.aemp_id, t1.ordm_date) AS t10 ON t1.aemp_id = t10.aemp_id AND t1.attn_date = t10.ordm_date
  LEFT JOIN tl_sgsm AS t11 ON t1.aemp_id = t11.aemp_id
WHERE  t1.attn_date between '$request->start_date' and '$request->end_date' and $where
GROUP BY t1.aemp_id, t1.attn_date, t2.id, t2.aemp_name, t2.aemp_usnm, t7.note_count, t9.att_count, t5.site_count,
  t6.site_count, t10.order_amount, t4.edsg_name,t10.item_count
ORDER BY MIN(t1.attn_time) ASC";
        //return $query1;
        $data = DB::connection($this->db)->select($query1);

        return $data;

    }

}