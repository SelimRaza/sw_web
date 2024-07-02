<?php

namespace App\Http\Controllers\API\v4;

use App\BusinessObject\Attendance;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\TempSite;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

class ManagersOrderModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function GetManagers_SR(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t1.`id`AS Sr_id,t1.`aemp_usnm`AS Sr_Code,t1.`aemp_name`AS Sr_Name,t1.aemp_mob1 AS Sr_Mobile
     FROM `tm_aemp` t1 WHERE t1.`role_id`='1' AND t1.`aemp_mngr`=$request->MG_EmpId");

        }

        return $data1;

    }

    public function MGCensusOutletScan(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Add on Route",
        );
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site = Site::on($db_conn)->where(['site_code' => $request->site_code])->first();
            if ($site != null) {
                /* $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $request->route_id])->first();
                 if ($routeSite == null) {
                     $routeSite = new RouteSite();
                     $routeSite->setConnection($db_conn);
                     $routeSite->site_id = $site->id;
                     $routeSite->rout_id = $request->route_id;
                     $routeSite->rspm_serl = 0;
                     $routeSite->cont_id = $request->country_id;
                     $routeSite->lfcl_id = 1;
                     $routeSite->aemp_iusr = $request->up_emp_id;
                     $routeSite->aemp_eusr = $request->up_emp_id;
                     $routeSite->var = 1;
                     $routeSite->attr1 = '';
                     $routeSite->attr2 = '';
                     $routeSite->attr3 = 0;
                     $routeSite->attr4 = 0;
                     $routeSite->save();*/
                $result_data = array(
                    'success' => 1,
                    'message' => " Get Outlet ",
                    'Outlet_ID' => $site->id,
                    'Outlet_Code' => $site->site_code,
                    'Outlet_Name' => $site->site_name,
                    'Owner_Name' => $site->site_ownm,
                    'outlet_serial' => 0,
                    'Mobile_No' => $site->site_mob1,
                    'Outlet_Address' => $site->site_adrs,
                    'geo_lat' => $site->geo_lat,
                    'geo_lon' => $site->geo_lon,
                );
                /* } else {
                     $result_data = array(
                         'success' => 0,
                         'message' => "Already Exits",
                     );
                 }*/
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "Outlet Not Exits",
                );
            }
        }
        return $result_data;
    }

    public function MGMasterData(Request $request){
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
                                t1.aemp_id                                                                                 AS SR_ID,
                                t2.slgp_id                                                                                 AS Group_ID,
                                t1.rout_id                                                                                 AS Route_ID,
                                t3.rout_name                                                                               AS Route_Name,
                                t4.id                                                                                      AS Base_Code,
                                t4.base_name                                                                               AS Base_Name,
                                t1.rpln_day                                                                                AS Day
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
                                INNER JOIN tm_base AS t4 ON t3.base_id = t4.id

                                WHERE t1.aemp_id = $request->emp_id 
                            ");
 

            $data3 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
                                t1.plmt_id                                                                          AS Item_Price_List,
                                t1.plmt_id                                                                          AS Grv_Item_Price_List,
                                t3.id                                                                               AS Item_Code,
                                t3.amim_code                                                                        AS sku_code,
                                t5.issc_name                                                                        AS Item_Category,
                                t3.amim_name                                                                        AS Item_Name,
                                round(t2.pldt_tppr,2)                                                               AS Item_Price,
                                round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
                                t2.amim_duft                                                                        AS Item_Factor,
                                t5.issc_seqn                                                                        AS Item_Showing_flg,
                                t2.amim_dunt                                                                        AS D_Unit,
                                t2.amim_runt                                                                        AS R_Unit
                                FROM tl_sgsm AS t1
                                INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
                                INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                                WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
                                GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code 
                            ");

            $data4 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
                                t2.id                                                                               AS Dealer_ID,
                                t2.dlrm_name                                                                        AS Dealer_Name,
                                t4.id                                                                               AS Base_Code,
                                t4.base_name                                                                        AS Base_Name,
                                t3.id                                                                               AS Group_ID,
                                t2.dlrm_adrs                                                                        AS Address,
                                t2.dlrm_mob1                                                                        AS Mobile_No
                                FROM tl_srdi AS t1
                                INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
                                INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
                                INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
                                WHERE t1.aemp_id =  $request->emp_id
                            ");

            $data5 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.nopr_name) AS column_id,
                                concat(t1.id, t1.nopr_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.nopr_name                AS Reason_Name
                                FROM tm_nopr AS t1
                            ");

            $data6 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
                                t5.prdt_disc, t4.prom_edat) AS column_id,
                                t5.prdt_sitm AS buy_item_code,
                                t5.prdt_mbqt as max_buy_qty,
                                t5.prdt_mnbt as min_buy_qyt,
                                t5.prdt_fitm as free_item_code,
                                t5.prdt_fiqt as free_item_qty,
                                round(t5.prdt_fipr,2) as free_item_price,
                                t5.prdt_disc as discount_percente,
                                t4.prom_edat as end_date
                                from tl_srgb as t1
                                INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
                                INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
                                INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
                                INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
                                WHERE t1.aemp_id = $request->emp_id
                                UNION ALL
                                SELECT
                                concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
                                    t2.prom_edat)   AS column_id,
                                t3.prdt_sitm           AS buy_item_code,
                                t3.prdt_mbqt           AS max_buy_qty,
                                t3.prdt_mnbt           AS min_buy_qyt,
                                t3.prdt_fitm           AS free_item_code,
                                t3.prdt_fiqt           AS free_item_qty,
                                round(t3.prdt_fipr, 2) AS free_item_price,
                                t3.prdt_disc           AS discount_percente,
                                t2.prom_edat           AS end_date
                                FROM tl_srgb AS t1
                                INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
                                INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
                                WHERE t1.aemp_id = $request->emp_id 
                                and t2.prom_nztp=0 
                                and t2.prom_sdat <=CURDATE()
                                AND t2.prom_edat >=CURDATE()
                            ");
        }

        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            //"RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }


    public function GeSrTodayOutlet(Request $request){ 

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t1.rout_id                                   AS Route_ID,
                                t3.site_id                                   AS Outlet_ID,
                                t4.site_code                                 AS Outlet_Code,
                                t4.site_name                                 AS Outlet_Name,
                                t4.site_ownm                                 AS Owner_Name,
                                t2.slgp_id                                   AS Group_ID,
                                t3.rspm_serl                                 AS outlet_serial,
                                t4.site_mob1                                 AS Mobile_No,
                                t4.site_adrs                                 AS Outlet_Address,
                                t4.geo_lat                                   AS geo_lat,
                                t4.geo_lon                                   AS geo_lon
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
                                INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
                                WHERE t1.aemp_id = $request->SR_EmpId AND t1.rpln_day=dayname(curdate())
                                GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
                                t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon
                            ");
        }
        return $data1;

    }
}