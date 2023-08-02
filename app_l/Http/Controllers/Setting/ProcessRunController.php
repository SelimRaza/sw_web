<?php

namespace App\Http\Controllers\Setting;

use App\BusinessObject\DashboardPermission;
use App\Mail\Test;
use App\MasterData\Auto;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\Process\AttendanceDataProcess;
use App\Process\DashboardDataProcess;
use App\Process\DataGen;
use App\Process\HrisUser;
use App\Process\RFLDataProcess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class ProcessRunController extends Controller
{
    private $access_key = 'ProcessRunController';
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


    public function index()
    {
        $data = array();
        if ($this->userMenu->wsmu_read) {
            array_push($data, (object)array('name' => 'My Process', 'code' => 'my_process',));
            // array_push($data, (object)array('name' => 'PRAN Process', 'code' => 'pran_process',));
            array_push($data, (object)array('name' => 'RFL Process', 'code' => 'rfl_process',));
            //  array_push($data, (object)array('name' => 'PRG Process', 'code' => 'prg_process',));
            array_push($data, (object)array('name' => 'PRAN QR Outlet', 'code' => 'qr_process',));
            array_push($data, (object)array('name' => 'RFL Census Outlet', 'code' => 'rfl_qr_process',));
            array_push($data, (object)array('name' => 'Attendance Process', 'code' => 'att_process_hris',));
            array_push($data, (object)array('name' => 'Hris User', 'code' => 'hris_user',));

            //dd($data);
            //dd($this->transpose($data));
            return view('Setting.ProcessList.index')->with('data', $data)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    function transpose($array_one)
    {
        $array_two = [];
        foreach ($array_one as $key => $item) {
            foreach ($item as $subkey => $subitem) {
                $array_two[$subkey][$key] = $subitem;
            }
        }
        return $array_two;
    }

    public function store(Request $request)
    {
        if (isset($request->process_id)) {
            foreach ($request->process_id as $index => $process_key) {
                if ($process_key == 'pran_process') {
                    date_default_timezone_set("Asia/Dhaka");
                    $date = date('Y-m-d');
                    $dashboardDataProcess = new  DashboardDataProcess();
                    $dashboardDataProcess->dashboardRoleManagerUpdate();
                    $dashboardDataProcess->uaeDashboardSVData($date);
                    $dashboardDataProcess->pgDashboardSVData($date);
                    $dashboardDataProcess->rgDashboardSVData($date);
                    $dashboardDataProcess->prgDashboardSVData($date);
                    $dashboardDataProcess->pgDashboardSRData($date);
                    $dashboardDataProcess->uniqueDashboardDataSR($date);
                    $dashboardDataProcess->dashboardDataUpdate($date);
                    /*date_default_timezone_set("Asia/Dhaka");
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];//date('Y-m-d');
                    $dashboardDataProcess = new  DashboardDataProcess();
                    //$dashboardDataProcess->uaeDashboardSVData($date);
                    $dashboardDataProcess->dashboardRoleManagerUpdate();
                    // $dashboardDataProcess->pgDashboardSVData($date);
                    //$dashboardDataProcess->prgDashboardSVData($date);
                    //$dashboardDataProcess->prgDashboardSRData($date);
                    // $dashboardDataProcess->uaeDashboardSRData($date);
                    $dashboardDataProcess->pgDashboardSRData($date);
                    $dashboardDataProcess->uniqueDashboardDataSR($date);
                    $dashboardDataProcess->dashboardDataUpdate($date);*/
                }
                if ($process_key == 'rfl_process') {
                    date_default_timezone_set("Asia/Dhaka");
                    /* $key = array_search($process_key, $request->process);
                     $date = $request->date[$key];//date('Y-m-d');
                     $dashboardDataProcess = new  DashboardDataProcess();
                     $dashboardDataProcess->rgManagerUpdate();
                     $dashboardDataProcess->prgDashboardSVData($date);
                     $dashboardDataProcess->rgDashboardSVData($date);
                     $dashboardDataProcess->rgDashboardSRData($date);
                     $dashboardDataProcess->uniqueDashboardDataSR($date);
                     $dashboardDataProcess->dashboardDataUpdate($date);*/

                    $country1 = (new Country())->country(5);
                    $dashboardDataProcess = new  DataGen();
                    $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];
                    $dateUnix = strtotime($date);
                    //$datetime->setDate($date('Y'),$date('m'),$date('m'));

                    // $dashboardDataProcess->prgDashboardSVData5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));  // blocked 28-09-2021
                    // $dashboardDataProcess->prgDashboardSRData5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));  // blocked 28-09-2021
                    // $dashboardDataProcess->prgdashboardUpdate5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));  // blocked 28-09-2021

                   // $dashboardDataProcess->prgDashboardSVData($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));
                    //$dashboardDataProcess->prgDashboardSRData($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));
                   // $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));

                    // dd(date('Y-m-d H:i:s',$dateUnix));
                    // $dashboardDataProcess->prgDashboardSVData($country1->cont_conn,  date('Y-m-d H:i:s',$dateUnix));// old dashboard
                    // $dashboardDataProcess->prgDashboardSRData($country1->cont_conn,  date('Y-m-d H:i:s',$dateUnix));
                    // $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn,  date('Y-m-d H:i:s',$dateUnix));
                }
                if ($process_key == 'prg_process') {
                    $country = Country::all();
                    $dashboardDataProcess = new  DataGen();
                    /*foreach ($country as $country1) {
                        $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                        $dashboardDataProcess->prgDashboardSVData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                        $dashboardDataProcess->prgDashboardSRData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                        $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                    }*/
                    $country1 = (new Country())->country(2);
                    $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                    $dashboardDataProcess->prgDashboardSVData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                    $dashboardDataProcess->prgDashboardSRData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                    $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                }
                if ($process_key == 'my_process') {
                    $country1 = (new Country())->country(2);
                    $dashboardDataProcess = new  DataGen();
                    $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];
                    $dateUnix = strtotime($date);
                    //$datetime->setDate($date('Y'),$date('m'),$date('m'));

                    // $dashboardDataProcess->prgDashboardSVData5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix)); //blocked 28-09-2021
                    // $dashboardDataProcess->prgDashboardSRData5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix)); //blocked 28-09-2021
                     //$dashboardDataProcess->prgdashboardUpdate5($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix)); //blocked 28-09-2021

                    // dd(date('Y-m-d H:i:s',$dateUnix));
                   // $dashboardDataProcess->prgDashboardSVData($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix)); // old dashboard
                   // $dashboardDataProcess->prgDashboardSRData($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));
                   // $dashboardDataProcess->prgdashboardUpdate($country1->cont_conn, date('Y-m-d H:i:s', $dateUnix));

                }
                if ($process_key == 'qr_process') {
                    $dashboardDataProcess = new  DashboardDataProcess();
                    $dashboardDataProcess->outletDataImport();

                }
                if ($process_key == 'rfl_qr_process') {
                    $dashboardDataProcess = new  DashboardDataProcess();
                    $dashboardDataProcess->outletDataImportRFL();
                }

                if ($process_key == 'att_process') {
                    $country1 = Auth::user()->country();
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];
                    $attData = new  AttendanceDataProcess();
                    $attData->attendanceDataPranDB($country1->cont_conn, $date);
                }

                if ($process_key == 'att_process_hris') {
                    //dd($request);
                    //$date = $request->date;
                    $country1 = Auth::user()->country();
                    ///dd($country1);
                    //$country1 = (new Country())->country(2);
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];
                    $employee = $request->employee;
                    //dd($employee[$count]);
                    //dd($country1->cont_conn);
                    //dd($query);
                    $query='';
                    $count =0;
                    if ($country1->id == '2') {
                        if ($employee[$count] != '') {
                            $query = "INSERT INTO `t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                      SELECT * FROM (SELECT
                                        t2.aemp_usnm                       AS sr_id,
                                        t1.attn_date                       AS date,
                                        TIME(t1.attn_time)                 AS time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        (CASE WHEN `atten_type` = '1'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = '2'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = 'L'
                                          THEN 'Leave'
                                        WHEN atten_type = 'Select Type'
                                          THEN 'Start Work'
                                        ELSE `atten_type` END)            AS type
                                      FROM `tt_attn` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                                      WHERE t1.`attn_date` = '$date' AND `aemp_asyn`='Y' AND t2.lfcl_id=1 AND t2.aemp_usnm='$employee[$count]'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`ordm_date`,
                                        TIME(t1.`ordm_time`)                    AS Time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Order'                                 AS type
                                      FROM `tt_ordm` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`ordm_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y' AND t2.aemp_usnm='$employee[$count]'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`note_date`                          AS date,
                                        TIME(t1.`note_dtim`)                    AS TIME,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Note'                                  AS type
                                      FROM `tt_note` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`note_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y' AND t2.aemp_usnm='$employee[$count]') AS ddd";
                        } else {
                            $query = "INSERT INTO `t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                      SELECT * FROM (SELECT
                                        t2.aemp_usnm                       AS sr_id,
                                        t1.attn_date                       AS date,
                                        TIME(t1.attn_time)                 AS time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        (CASE WHEN `atten_type` = '1'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = '2'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = 'L'
                                          THEN 'Leave'
                                        WHEN atten_type = 'Select Type'
                                          THEN 'Start Work'
                                        ELSE `atten_type` END)            AS type
                                      FROM `tt_attn` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                                      WHERE t1.`attn_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`ordm_date`,
                                        TIME(t1.`ordm_time`)                    AS Time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Order'                                 AS type
                                      FROM `tt_ordm` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`ordm_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`note_date`                          AS date,
                                        TIME(t1.`note_dtim`)                    AS TIME,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Note'                                  AS type
                                      FROM `tt_note` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`note_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y') AS ddd";
                        }

                    }
                    if ($country1->id == '5') {
                        if ($employee[$count] != '') {
                            $query = "INSERT INTO rgsihirfms_rssa.`t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                      SELECT * FROM (SELECT
                                        t2.aemp_usnm                       AS sr_id,
                                        t1.attn_date                       AS date,
                                        TIME(t1.attn_time)                 AS time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        (CASE WHEN `atten_type` = '1'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = '2'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = 'L'
                                          THEN 'Leave'
                                        WHEN atten_type = 'Select Type'
                                          THEN 'Start Work'
                                        ELSE `atten_type` END)            AS type
                                      FROM myprg_rfl.`tt_attn` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.aemp_id = t2.id
                                      WHERE t1.`attn_date` = '$date' AND t2.aemp_usnm='$employee[$count]'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`ordm_date`,
                                        TIME(t1.`ordm_time`)                    AS Time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Order'                                 AS type
                                      FROM myprg_rfl.`tt_ordm` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`ordm_date` = '$date' AND t2.aemp_usnm='$employee[$count]'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`note_date`                          AS date,
                                        TIME(t1.`note_dtim`)                    AS TIME,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Note'                                  AS type
                                      FROM myprg_rfl.`tt_note` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`note_date` = '$date' AND t2.aemp_usnm='$employee[$count]') AS ddd";
                        } else {
                            $query = "INSERT INTO rgsihirfms_rssa.`t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                      SELECT * FROM (SELECT
                                        t2.aemp_usnm                       AS sr_id,
                                        t1.attn_date                       AS date,
                                        TIME(t1.attn_time)                 AS time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        (CASE WHEN `atten_type` = '1'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = '2'
                                          THEN 'Start Work'
                                        WHEN `atten_type` = 'L'
                                          THEN 'Leave'
                                        WHEN atten_type = 'Select Type'
                                          THEN 'Start Work'
                                        ELSE `atten_type` END)            AS type
                                      FROM myprg_rfl.`tt_attn` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.aemp_id = t2.id
                                      WHERE t1.`attn_date` = '$date'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`ordm_date`,
                                        TIME(t1.`ordm_time`)                    AS Time,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Order'                                 AS type
                                      FROM myprg_rfl.`tt_ordm` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`ordm_date` = '$date'
                                      UNION ALL
                                      SELECT
                                        t2.aemp_usnm                            AS sr_id,
                                        t1.`note_date`                          AS date,
                                        TIME(t1.`note_dtim`)                    AS TIME,
                                        t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                        'Note'                                  AS type
                                      FROM myprg_rfl.`tt_note` t1 INNER JOIN myprg_rfl.tm_aemp t2 ON t1.`aemp_id` = t2.id
                                      WHERE t1.`note_date` = '$date') AS ddd";
                        }

                    }
                    if ($country1->id == '9') {
                      if ($employee[$count] != '') {
                          $query = "INSERT INTO `t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                    SELECT * FROM (SELECT
                                      t2.aemp_usnm                       AS sr_id,
                                      t1.attn_date                       AS date,
                                      TIME(t1.attn_time)                 AS time,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      (CASE WHEN `atten_type` = '1'
                                        THEN 'Start Work'
                                      WHEN `atten_type` = '2'
                                        THEN 'Start Work'
                                      WHEN `atten_type` = 'L'
                                        THEN 'Leave'
                                      WHEN atten_type = 'Select Type'
                                        THEN 'Start Work'
                                      ELSE `atten_type` END)            AS type
                                    FROM `tt_attn` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                                    WHERE t1.`attn_date` = '$date' AND `aemp_asyn`='Y' AND t2.lfcl_id=1 AND t2.aemp_usnm='$employee[$count]'
                                    UNION ALL
                                    SELECT
                                      t2.aemp_usnm                            AS sr_id,
                                      t1.`ordm_date`,
                                      TIME(t1.`ordm_time`)                    AS Time,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      'Order'                                 AS type
                                    FROM `tt_ordm` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                    WHERE t1.`ordm_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y' AND t2.aemp_usnm='$employee[$count]'
                                    UNION ALL
                                    SELECT
                                      t2.aemp_usnm                            AS sr_id,
                                      t1.`note_date`                          AS date,
                                      TIME(t1.`note_dtim`)                    AS TIME,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      'Note'                                  AS type
                                    FROM `tt_note` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                    WHERE t1.`note_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y' AND t2.aemp_usnm='$employee[$count]') AS ddd";
                      } else {
                          $query = "INSERT INTO `t_actv`(`sr_id`, `date`, `time`, `actv_lat`, `actv_lon`, `status`)
                                    SELECT * FROM (SELECT
                                      t2.aemp_usnm                       AS sr_id,
                                      t1.attn_date                       AS date,
                                      TIME(t1.attn_time)                 AS time,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      (CASE WHEN `atten_type` = '1'
                                        THEN 'Start Work'
                                      WHEN `atten_type` = '2'
                                        THEN 'Start Work'
                                      WHEN `atten_type` = 'L'
                                        THEN 'Leave'
                                      WHEN atten_type = 'Select Type'
                                        THEN 'Start Work'
                                      ELSE `atten_type` END)            AS type
                                    FROM `tt_attn` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                                    WHERE t1.`attn_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y'
                                    UNION ALL
                                    SELECT
                                      t2.aemp_usnm                            AS sr_id,
                                      t1.`ordm_date`,
                                      TIME(t1.`ordm_time`)                    AS Time,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      'Order'                                 AS type
                                    FROM `tt_ordm` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                    WHERE t1.`ordm_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y'
                                    UNION ALL
                                    SELECT
                                      t2.aemp_usnm                            AS sr_id,
                                      t1.`note_date`                          AS date,
                                      TIME(t1.`note_dtim`)                    AS TIME,
                                      t1.`geo_lat` AS actv_lat, t1.`geo_lon` AS actv_lon,
                                      'Note'                                  AS type
                                    FROM `tt_note` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id
                                    WHERE t1.`note_date` = '$date' AND t2.lfcl_id=1 AND `aemp_asyn`='Y') AS ddd";
                      }

                  }
                    //dd($query);
                    DB::connection($country1->cont_conn)->select($query);
                    /*$date = $request->date;
                    $employee = $request[0]->employee;
                    //dd($request);
                    echo $process_key .'<br />';
                    echo $employee;*/


                    /*$country1 = Auth::user()->country();
                    $key = array_search($process_key, $request->process);
                    $date = $request->date[$key];
                    $attData = new  AttendanceDataProcess();
                    $attData->attendanceDataPranDB($country1->cont_conn, $date);*/
                }

                if ($process_key == 'hris_user') {
                    $hrdata = new HrisUser();
                    $hrdata->createOrUpdateUser((new Country())->country(2));
                    //$hrdata->createOrUpdateUser((new Country())->country(5));
                }


            }
        }
        /**/

        return redirect()->back()->with('success', 'Refresh Successfully');
    }

    public function destroy($id)
    {

    }

}
