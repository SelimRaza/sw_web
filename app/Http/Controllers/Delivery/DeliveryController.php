<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\MasterData\District;
use App\MasterData\Employee;
use App\MasterData\Thana;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends Controller
{

    private $access_key = 'DeliveryController';
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


    public function srWiseDelivery(){

        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

        $results = [];
        return view('report.delivery.sr_wise_delivery')
            ->with('acmp', $acmp)
            ->with('region', $region);
    }

    public function srWiseDeliveryFilterReport(Request $request){
        $empId = $this->currentUser->employee()->id;
        //dd($request);
        /*$zone_id = '';
        $sales_group_id = '8';
        $dirg_id = '';
        $start_date = '2021-06-23';
        $end_date = '2021-06-23';
        $acmp_id = '7';*/


        $zone_id = $request->zone_id;
        $dirg_id = $request->dirg_id;
        $sales_group_id = $request->sales_group_id;
        $start_date = $request->start_date;

        $acmp_id = $request->acmp_id;


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
  t2.ordm_date,
  concat(t6.slgp_code, ' - ', t6.slgp_name) AS g_Name,
  concat(t5.dirg_code, ' - ', t5.dirg_name) AS region,
  concat(t4.zone_code, ' - ', t4.zone_name) AS zone,
  t3.aemp_usnm,
  t3.aemp_name,
  t3.aemp_mob1,
  round(SUM(t1.`ordd_oamt`),2)                       AS oamt,
  round(SUM(t1.`ordd_odat`),2)                       AS damt
FROM `tt_ordd` t1 INNER JOIN tt_ordm t2 ON t1.ordm_id = t2.id
  INNER JOIN tm_aemp t3 ON t2.aemp_id = t3.id
  INNER JOIN tm_zone t4 ON t3.zone_id = t4.id
  INNER JOIN tm_dirg t5 ON t4.dirg_id = t5.id
  INNER JOIN tm_slgp t6 ON t2.slgp_id = t6.id
WHERE t2.ordm_date = '$start_date' ". $q1 . $q2 . $q3 . " GROUP BY t2.aemp_id";
        //$query = "SELECT t2.ordm_date, concat(t6.slgp_code, ' - ', t6.slgp_name) AS g_Name, concat(t5.dirg_code, ' - ', t5.dirg_name) AS region, concat(t4.zone_code, ' - ', t4.zone_name) AS zone, t3.aemp_usnm, t3.aemp_name, t3.aemp_mob1, round(SUM(t1.`ordd_oamt`),2) AS oamt, round(SUM(t1.`ordd_odat`),2) AS damt FROM `tt_ordd` t1 INNER JOIN tt_ordm t2 ON t1.ordm_id = t2.id INNER JOIN tm_aemp t3 ON t2.aemp_id = t3.id INNER JOIN tm_zone t4 ON t3.zone_id = t4.id INNER JOIN tm_dirg t5 ON t4.dirg_id = t5.id INNER JOIN tm_slgp t6 ON t2.slgp_id = t6.id WHERE t2.ordm_date = '2021-06-23' AND t2.slgp_id = '8' GROUP BY t2.aemp_id LIMIT 2";


       // echo $query;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));

        return $srData;

    }







}
