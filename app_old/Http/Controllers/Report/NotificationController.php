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
use App\MasterData\District;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    private $access_key = 'tt_noti';
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

    public function notificationList()
    {

        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $country_id = $this->currentUser->employee()->cont_id;
            $notification = DB::connection($this->db)->select("SELECT `id`, `noti_head`, `noti_body`, `noti_date`, `noti_imge`, `lfcl_id` FROM `tt_noti` WHERE 1");

            return view('Notification.notification')->with('permission', $this->userMenu)
                ->with('notification', $notification);
        } else {
            return view('theme.access_limit');
        }

    }

    public function notificationListCreate()
    {

        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $country_id = $this->currentUser->employee()->cont_id;

            $acmp = DB::connection($this->db)->select("select * from tm_acmp");
            $region = DB::connection($this->db)->select("select * from tm_dirg ORDER BY dirg_code ASC");
            $zone = DB::connection($this->db)->select("select * from tm_zone ORDER BY zone_code ASC");
            $category = DB::connection($this->db)->select("select * from tm_otcg ORDER BY id ASC");
            $district = DB::connection($this->db)->select("SELECT `id`, `dsct_name`  FROM `tm_dsct` WHERE `lfcl_id`='1'");

            $role = DB::connection($this->db)->select("SELECT `id`, `role_name`, `role_code`, `cont_id`, `lfcl_id`  FROM `tm_role` WHERE lfcl_id='1'");



            $notification = DB::connection($this->db)->select("SELECT `id`, `noti_head`, `noti_body`, `noti_date`, `noti_imge`, `lfcl_id` FROM `tt_noti` WHERE 1");

            return view('Notification.create-notification')->with('permission', $this->userMenu)
                ->with('acmp', $acmp)->with('region', $region)->with('zoneList', $zone)->with('district', $district)->with('category', $category)->with('role',$role);
        } else {
            return view('theme.access_limit');
        }

    }

    public function getGroup(Request $request){
        $acmp_id = $request->slgp_id;
        $empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId' and acmp_id='$acmp_id'");
    }

    public function summaryDetails(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $emp = Employee::on($this->db)->findorfail($id);
            $data = DB::connection($this->db)->select("SELECT
  t1.aemp_id                           AS emp_id,
  t2.aemp_name                         AS name,
  t1.note_date                         AS date,
  t1.note_titl                         AS title,
  t1.note_body                         AS note,
  DATE_FORMAT(t1.note_dtim, '%l.%i%p') AS time,
  t1.geo_lat                           AS lat,
  t1.geo_lon                           AS lon,
  group_concat(t3.nimg_imag)           AS image_name,
  t4.ntpe_name                         AS note_type,
  t1.geo_addr
FROM tt_note AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  LEFT JOIN tl_nimg AS t3 ON t1.id = t3.note_id
  INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
WHERE t1.aemp_id = $id AND t1.note_date = '$date'
GROUP BY t1.id, t1.note_titl, t1.note_body, t1.aemp_id, t2.aemp_name, t1.note_date, t1.geo_lat, t1.geo_lon,
  t1.note_dtim, t4.ntpe_name
ORDER BY t1.note_dtim ASC");
            return view('report.note.summary_details')->with("notes", $data)->with("emp", $emp)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function summaryLocation(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $data = DB::connection($this->db)->select("SELECT
  t1.aemp_id                           AS emp_id,
  t2.aemp_name                         AS name,
  t1.note_date                         AS date,
  t1.note_titl                         AS title,
  t1.note_body                         AS note,
  DATE_FORMAT(t1.note_dtim, '%l.%i%p') AS time,
  t1.geo_lat                           AS lat,
  t1.geo_lon                           AS lon
FROM tt_note AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  LEFT JOIN tl_nimg AS t3 ON t1.id = t3.note_id
WHERE t1.aemp_id = $id AND t1.note_date = '$date'
GROUP BY t1.id, t1.note_titl, t1.note_body, t1.aemp_id, t2.aemp_name, t1.note_date, t1.geo_lat, t1.geo_lon, t1.note_dtim
ORDER BY t1.note_date ASC");

            return view('report.note.summary_map')->with("notes", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function filterNoteSummary(Request $request)
    {

        $country_id = $this->currentUser->employee()->cont_id;


        $empId = $this->currentUser->employee()->id;

        $zone_id = $request->zone_id;
        $dirg_id = $request->dirg_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;


        $q1 = "";
        $q2 = "";
        $q3 = "";
        if ($zone_id != "") {
            $q1 = "AND t4.zone_id = '$zone_id'";
        }
        if ($sales_group_id != "") {
            $q2 = "AND t3.slgp_id = '$sales_group_id'";
        }

        if ($dirg_id != "") {
            $q3 = "AND t4.dirg_id = '$dirg_id'";
        }



        $data = DB::connection($this->db)->select("SELECT
  t1.note_date, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body, t1.geo_addr, TIME(t1.note_dtim) as n_time
FROM `tt_note` t1 
  INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
  INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
  INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
      t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
ORDER BY t2.slgp_code, t3.zone_code");
        return $data;

    }

    public function filterNoteDetails(Request $request)
    {
       // dd($request);
        $country_id = $this->currentUser->employee()->cont_id;

        $empId = $this->currentUser->employee()->id;

        /*$zone_id = '';
        $sales_group_id = '1';
        $dirg_id = '';
        $start_date = '2021-07-23';
        $end_date = '2021-08-04';
        $acmp_id = '3';*/

        /*$start_date = '2021-07-23';
        $end_date = '2021-08-04';*/

        $zone_id = $request->zone_id;
        $dirg_id = $request->dirg_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $acmp_id = $request->acmp_id;


        $q1 = "";
        $q2 = "";
        $q3 = "";
        if ($zone_id != "") {
            $q1 = "AND t4.zone_id = '$zone_id'";
        }
        if ($sales_group_id != "") {
            $q2 = "AND t3.slgp_id = '$sales_group_id'";
        }

        if ($dirg_id != "") {
            $q3 = "AND t4.dirg_id = '$dirg_id'";
        }

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
//         $data = DB::connection($this->db)->select("SELECT
//   t1.note_date, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body,
//     t5.edsg_name, replace(t1.geo_addr, ',' , '.' ) as geo_addr, TIME(t1.note_dtim) as n_time
// FROM `tt_note` t1 
//   INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
//   INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
//   INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
//   INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
// WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
//       t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
// ORDER BY t3.slgp_code, t4.zone_code, t1.aemp_id");
// $data = DB::connection($this->db)->select("SELECT
// t1.note_date,t1.note_type, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body,
//   t5.edsg_name,concat(t6.id, ' - ', t6.site_name)   AS site_name, replace(t1.geo_addr, ',' , '.' ) as geo_addr, TIME(t1.note_dtim) as n_time
// FROM `tt_note` t1 
// INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
// INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
// INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
// INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
// LEFT JOIN tm_site t6 on t1.site_code=t6.site_code
// WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
//     t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
// ORDER BY t3.slgp_code, t4.zone_code, t1.aemp_id");
$data = DB::connection($this->db)->select("SELECT
t1.note_date,IF(t7.lfcl_id=2,concat(t7.ntpe_name,'/','InActv'),t7.ntpe_name) as note_type, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code, t4.zone_name, t4.zone_code, t1.note_body,
  t5.edsg_name,concat(t6.site_code, ' - ', t6.site_name)   AS site_name,
   replace(t1.geo_addr, ',' , '.' ) as geo_addr, TIME(t1.note_dtim) as n_time
FROM `tt_note` t1 
INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
LEFT JOIN tm_site t6 on t1.site_code=t6.site_code
LEFT JOIN tm_ntpe t7 on t1.ntpe_id=t7.id
WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
    t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
ORDER BY t3.slgp_code, t4.zone_code, t1.aemp_id");
        return $data;
    }

}