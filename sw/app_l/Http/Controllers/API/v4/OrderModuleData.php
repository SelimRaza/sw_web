<?php

/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v4;

use App\BusinessObject\Attendance;
use App\BusinessObject\DlearProfileAdd;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\RewardMaster;
use App\BusinessObject\RewardsDetails;
use App\BusinessObject\SiteLogRfl;
use App\BusinessObject\SiteMqr;
use App\BusinessObject\SiteRandom;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\Trip;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use AWS;

class OrderModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    /*
    *   API MODEL V3 [START]
    */

    public function masterData_Common(Request $request){
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();

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
                t5.acmp_id                                                                                 AS OU_ID,
                TRIM(t1.rpln_day)                                                                                AS Day
                FROM tl_rpln AS t1
                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
                INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
                INNER JOIN tm_slgp AS t5 ON t2.slgp_id = t5.id

                WHERE t1.aemp_id = $request->emp_id ");

                            $data2 = DB::connection($db_conn)->select("
                SELECT
                concat(id, ntpe_name, ntpe_code) AS column_id,
                id AS type_id,
                ntpe_name as  name,
                ntpe_code as code
                FROM tm_ntpe WHERE lfcl_id=1 and  cont_id=$request->country_id
            ");


            $data4 = DB::connection($db_conn)->select("
                SELECT
                concat(t2.id, t2.dlrm_name,t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
                t2.id                                                                               AS Dealer_ID,
                t2.dlrm_name                                                                        AS Dealer_Name,
                t4.id                                                                               AS Base_Code,
                t4.base_name                                                                        AS Base_Name,
                t3.id                                                                               AS Group_ID,
                t2.dlrm_adrs                                                                        AS Address,
                t2.dlrm_mob1                                                                        AS Mobile_No
                FROM tl_srdi AS t1
                INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
                LEFT JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
                LEFT JOIN tm_base AS t4 ON t2.base_id = t4.id
                WHERE t1.aemp_id =  $request->emp_id");
                            $data5 = DB::connection($db_conn)->select("
                SELECT
                concat(t1.id, t1.nopr_name) AS column_id,
                concat(t1.id, t1.nopr_name) AS token,
                t1.id                       AS Reason_id,
                t1.nopr_name                AS Reason_Name
                FROM tm_nopr AS t1");

                            $data7 = DB::connection($db_conn)->select("
                SELECT
                    concat(t1.id, t1.dprt_name) AS column_id,
                    concat(t1.id, t1.dprt_name) AS token,
                    t1.id                       AS Reason_id,
                    t1.dprt_name                AS Reason_Name
                FROM tm_dprt AS t1
            ");


            $data8 = DB::connection($db_conn)->select("
                SELECT concat(t1.id, t1.`lfcl_id`,t1.`mspm_sdat`,t1.`mspm_edat`,t2.slgp_id,t2.zone_id,t3.amim_id) AS column_id,
                t3.amim_id                  AS MSP_Item_ID,
                t3.mspd_qnty                AS MSP_Item_Qty,
                t1.id                       AS MSP_ID,
                '2'                         AS Status_ID
                FROM `tm_mspm`t1 JOIN tl_mspg t2 ON(t1.`id`=t2.mspm_id)
                JOIN tm_mspd t3 ON(t1.id=t3.mspm_id)
                JOIN tm_aemp t4 ON t2.slgp_id=t4.slgp_id AND t2.zone_id=t4.zone_id
                WHERE t4.id=$request->emp_id AND t1.`lfcl_id`=1 AND t1.`mspm_sdat` <= CURDATE()
                    AND t1.`mspm_edat` >= CURDATE()
            ");

            $data3 = DB::connection($db_conn)->select("
                SELECT concat(t1.id,t1.slgp_id,t1.zone_id,t1.`pmgl_imgl`) AS column_id,
                t1.`pmgl_imgl` AS image 
                FROM `tl_pmgl`t1 JOIN tl_sgsm t2 ON(t1.`slgp_id`=t2.slgp_id) AND t1.`zone_id`=t2.zone_id
                WHERE t2.aemp_id=$request->emp_id
            ");

            $data6 = DB::connection($db_conn)->select("
                SELECT
                concat(t1.id, t1.`wurl_link`,t1.wurl_muid,t1.wurl_stus) AS column_id,
                concat(t1.id, t1.`wurl_link`,t1.wurl_muid,t1.wurl_stus) AS token,
                t1.`wurl_muid`                                          AS menu_id,
                t1.`wurl_link`                                          AS menu_link
                FROM tm_wurl AS t1
                WHERE t1.wurl_stus=1
            ");

        }
        return Array(
            "sr_route_data" => $data1,
            "note_type" =>  $data2,
            "promotion_gallery" =>  $data3,
            "distribution_info" =>  $data4,
            "non_productive_reason" =>  $data5,
            "web_url" =>  $data6,
            "grv_reason" =>  $data7,
            "msp_item_data" =>  $data8,
        );
    }
    
    public function MasterDataNew_FOC(Request $request){

        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data6 = DB::connection($db_conn)->select("
                SELECT
                concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt, round(t5.prdt_fipr, 2),
                        t5.prdt_disc, t4.prom_edat) AS column_id,
                t5.prdt_sitm                       AS buy_item_code,
                t5.prdt_mbqt                       AS max_buy_qty,
                t5.prdt_mnbt                       AS min_buy_qyt,
                t5.prdt_fitm                       AS free_item_code,
                t5.prdt_fiqt                       AS free_item_qty,
                round(t5.prdt_fipr, 2)             AS free_item_price,
                t5.prdt_disc                       AS discount_percente,
                t4.prom_edat                       AS end_date
                FROM tm_aemp AS t1
                INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
                INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
                INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t2.plmt_id = t4.slgp_id
                INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
                WHERE t1.id = $request->emp_id AND t4.lfcl_id='1'
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
                FROM tl_sgsm AS t1
                INNER JOIN tm_prom AS t2 ON t1.plmt_id = t2.slgp_id
                INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
                WHERE t1.aemp_id = $request->emp_id AND t2.lfcl_id='1'
                AND t2.prom_nztp = 0
                AND t2.prom_sdat <= CURDATE()
                AND t2.prom_edat >= CURDATE()
            ");
        }
        return Array(
        // 0= NT  1= Zn
            "foc_data" => $data6,
        );

    }

    public function MasterDataNew_RouteWise_Outlet(Request $request){

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
                SELECT
                concat(t3.site_id, t1.rout_id, t3.rspm_serl,t4.lfcl_id) AS column_id,
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
                WHERE t1.aemp_id = $request->emp_id AND t4.lfcl_id = 1
                GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
                t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;
            ");
        }

        return Array(
            "outlet_data" => $data2,
        );
    }

    public function MasterDataNew_Product_Info_With_Image_Icon_Large(Request $request){
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data3 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t3.id,t3.amim_imgl,t3.amim_imic,t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
                                t1.plmt_id                                                                          AS Item_Price_List,
                                t1.plmt_id                                                                          AS Grv_Item_Price_List,
                                t3.id                                                                               AS Item_Id,
                                t3.amim_code                                                                        AS Item_Code,
                                t3.amim_imgl                                                                        AS amim_imgl,
                                t3.amim_imic                                                                        AS amim_imic,
                                t5.issc_name                                                                        AS Category_Name,
                                t3.amim_name                                                                        AS Item_Name,
                                round(t2.pldt_tppr,2)                                                               AS Item_Rate,
                                round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
                                t2.amim_duft                                                                        AS Item_Factor,
                                t5.issc_seqn                                                                        AS Sorting_Index,
                                t2.amim_dunt                                                                        AS D_Unit,
                                t2.amim_runt                                                                        AS R_Unit
                                FROM tl_sgsm AS t1
                                INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                INNER JOIN tl_sgit AS t4 ON t1.plmt_id = t4.slgp_id AND t2.amim_id = t4.amim_id
                                INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                                WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
                                GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code 
                            ");
        }
        return Array(
            "product_info_data" => $data3,
        );/*return Array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );*/

    }


    public function govThana(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
                        `id` as Thana_Code,
                        than_name AS Thana_Name
                        FROM `tm_than`
                        WHERE `lfcl_id` = 1 AND
                            `dsct_id` = '$dsct_id'
                        ORDER BY than_name
                    ");
        }
        return $data1;

    }

    public function govThana1(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        $district_Name = $request->send_district_Name;
        $district_Name1 = $request->send_district_getLocality;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT t2.id as Thana_Code,t2.than_name AS Thana_Name
                                FROM tm_dsct t1 JOIN tm_than t2 ON (t1.id=t2.dsct_id)
                                WHERE t1.dsct_name='$district_Name' OR t1.dsct_name='$district_Name1'
                            ");

        }
        return $data1;

    }

    public function govWard(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $than_id = $request->send_thana_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                    SELECT `id` as ward_code,`ward_name` as ward_name
                    FROM `tm_ward`
                    WHERE `LFCL_ID`='1' AND
                    `THAN_ID`='$than_id' Order by ward_name
                ");

        }
        return $data1;

    }

    public function aroundOutlet(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
                select 0 id from dual union all SELECT t4.id
                FROM tm_mktm t4
                LEFT JOIN tm_ward_lt t6 ON(t4.ward_id=t6.ward_id)
                WHERE  MBRContains(st_makeEnvelope (
                point(($request->geo_lon + 5 / 111.1), ($request->geo_lat + 5 / 111.1)),
                point(($request->geo_lon - 5 / 111.1), ($request->geo_lat - 5 / 111.1)) 
                ), POINT( geo_lon, geo_lat ))            
            ");

            $d='';
            $i=1;
            foreach ($data2 as $key => $value) {
                if($i==1){
                    $d.=$value->id;
                }else{
                    $d.=','.$value->id;
                }
                $i++;
            }
            $data1 = DB::connection($db_conn)->select("
                select t1.id                                                   AS Outlet_ID,
                t1.site_code                                                 AS Outlet_Code,
                t1.site_name                                                 AS Outlet_Name,
                TRIM(t1.site_olnm)                                           AS Outlet_Name_Bn,
                ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                * COS( RADIANS( t1.geo_lat ) )
                * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                + SIN( RADIANS( $request->geo_lat  ) )
                * SIN( RADIANS( t1.geo_lat ) )
                )
                * 6371
                ) AS distance_in_km,
                t1.site_ownm AS Owner_Name,
                t1.site_mob1 AS Mobile_No,
                t1.site_adrs AS Outlet_Address,
                t1.site_olad AS Outlet_Address_Bn,
                t1.site_imge AS Outlet_imge_ln,
                t1.site_isfg AS refrigerator,
                t1.site_issg AS shop_sign,
                t1.geo_lat,
                t1.geo_lon
                from tm_site_registration as t1
                where t1.mktm_id in($d) and MBRContains(st_makeEnvelope (
                point(($request->geo_lon + 1 / 111.1), ($request->geo_lat + 1 / 111.1)),
                point(($request->geo_lon - 1 / 111.1), ($request->geo_lat - 1 / 111.1)) 
                ), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 order by distance_in_km asc limit 50;
            ");         
        }

        return Array(
            "OutletBank_Table" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public function RemoveOutletFromRoute(Request $request){

        $country = (new Country())->country($request->country_id);
        $result_data = array(
            'success' => 10,
            'message' => "Fail to Remove From Route",
        );
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $routeSite = RouteSite::on($db_conn)->where(['site_id' => $request->site_id, 'rout_id' => $request->route_id])->first();
            if ($routeSite != null) {

                DB::connection($db_conn)->table('tl_rsmp')->where(['rout_id' => $request->route_id,
                    'site_id' => $request->site_id])->delete();
                $result_data = array(
                    'success' => 1,
                    'message' => "Successfully Remove From Route !!",
                );
            } else {
                $result_data = array(
                    'success' => 20,
                    'message' => "Fail to Remove From Route",
                );
            }
        }
        return $result_data;
    }

    public function outletsByThana(Request $request){

        $data2 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data2 = DB::connection($db_conn)->select("
                    SELECT
                    concat(t1.id, t1.site_code, t3.than_id) AS column_id,
                    t1.id AS Outlet_ID,
                    t1.site_code AS Outlet_Code,
                    TRIM(t1.site_name) AS Outlet_Name,
                    TRIM(t1.site_olnm) AS Outlet_Name_Bn,
                    t1.site_ownm AS Owner_Name,
                    t1.site_mob1 AS Mobile_No,
                    t1.site_adrs AS Outlet_Address,
                    t1.site_olad AS Outlet_Address_Bn,
                    t1.site_imge AS Outlet_imge_ln,
                    t1.site_isfg AS refrigerator,
                    t1.site_issg AS shop_sign,
                    t1.geo_lat AS geo_lat,
                    t1.geo_lon AS geo_lon
                    FROM tm_site t1 JOIN tm_mktm t2 ON t1.mktm_id=t2.id
                    LEFT JOIN tm_ward t3 ON t2.ward_id=t3.id
                    LEFT JOIN tl_srth t4 ON t3.than_id=t4.than_id
                    WHERE t4.aemp_id='$request->emp_id' AND t4.than_id='$request->than_id' AND t1.lfcl_id=1;
                ");
        }
        return Array(
            "OutletBank_Table" => array("data" => $data2, "action" => $request->country_id),
        );
    }

    public function SrThanalinklist(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                    SELECT t1.id,t1.than_name 
                    from tm_than t1 JOIN tl_srth t2 ON (t1.id=t2.than_id)
                    WHERE t2.aemp_id=$request->emp_id");
        }
        return $data1;

    }

    public function updateOutletSave(Request $request){
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = Site::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name != "" ? $outletData->Outlet_Name : '';
                    $site->site_olnm = $outletData->Outlet_Name_BN != "" ? $outletData->Outlet_Name_BN : '';
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    $site->site_imge = $outletData->Outlet_Image != "" ? $outletData->Outlet_Image : '';
                    $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                    $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                    $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    $site->aemp_eusr = $outletData->up_emp_id;
                    $site->save();
                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Updated Successfully",
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
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }

    public function market(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $ward_id = $request->Ward_Code_Send;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                        SELECT `id` as market_id,
                        mktm_name AS `market_name`
                        FROM `tm_mktm`
                        WHERE `lfcl_id` = '1' AND `ward_id` = '$ward_id'
                        ORDER BY mktm_name
                    ");
        }
        return $data1;
    }


    public function RFLoutletCategory(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            select t1.id as Category_Code,
                            t1.otcg_name as Category_Name 
                            from tm_rtcg as t1 ORDER BY t1.otcg_name
                        ");
        }
        return $data1;
    }

    public function RFLOutletSave(Request $request){
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet akk",
        );
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    
                    $site_code = '7' . str_pad($outletData->Outlet_Code, 9, '7', STR_PAD_LEFT);
                    $site1 = Site::on($db_conn)->where(['site_mob1' => $outletData->Mobile_No_2])->first();
                    $site2 = Site::on($db_conn)->where(['site_code' => $site_code])->first();
                    if ($site1 == null) {
                        if ($site2 == null) {

                            $site = new Site();
                            $site->setConnection($db_conn);

                            $site->site_name = $outletData->Outlet_Name;
                            $site->site_code = $site_code;
                            $site->outl_id = 1;
                            $site->site_olnm = $outletData->Outlet_Name_BN;
                            $site->site_adrs = $outletData->Address;
                            $site->site_olad = $outletData->Address_BN;
                            $site->mktm_id = $outletData->Market_ID;
                            $site->site_ownm = $outletData->owner_name;
                            $site->site_olon = '';
                            $site->site_mob2 = '';
                            $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                            $site->site_emal = '';
                            $site->scnl_id = 1;
                            $site->otcg_id = $outletData->Shop_Category_id;
                            $site->site_imge = $outletData->Outlet_Image;
                            $site->site_omge = '';
                            $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                            $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                            $site->site_reg = '';
                            $site->site_vrfy = 0;
                            $site->site_hsno = '';
                            $site->site_vtrn = '';
                            $site->site_vsts = 0;
                            $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                            $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                            $site->cont_id = $outletData->country_id;
                            $site->lfcl_id = 1;
                            $site->aemp_iusr = $outletData->up_emp_id;
                            $site->aemp_eusr = $outletData->up_emp_id;
                            $site->save();

                            $SiteLog = new SiteLogRfl();
                            $SiteLog->setConnection($db_conn);
                            $SiteLog->site_id = $site->id;
                            $SiteLog->aemp_iusr = $outletData->up_emp_id;
                            $SiteLog->save();

                            DB::connection($db_conn)->commit();
                            $result_data = array(
                                'success' => 1,
                                'message' => "  Outlet Open Successful  ",
                                'code' => $site->site_code,
                                'id' => $site->id,
                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'message' => " Outlet Already Opened \n Try Again !!!  ",
                            );
                        }
                    } else {

                        $existId = Site::on($db_conn)->where('site_mob1', $outletData->Mobile_No_2)->first(['id']);                       
                        DB::connection($db_conn)->select("UPDATE tm_site SET lfcl_id=2 WHERE site_mob1='$outletData->Mobile_No_2'");

                        $site5 = DB::connection($db_conn)->table('tm_site')->where(['id' => $existId->id
                        ])->update([
                            'site_name' => $outletData->Outlet_Name,
                            'site_code' => $site_code,
                            'site_olnm' => $outletData->Outlet_Name_BN,
                            'site_adrs' => $outletData->Address,
                            'site_olad' => $outletData->Address_BN,
                            'mktm_id' => $outletData->Market_ID,
                            'site_ownm' => $outletData->owner_name,
                            'otcg_id' => $outletData->Shop_Category_id,
                            'site_imge' => $outletData->Outlet_Image,
                            'geo_lat' => isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0,
                            'geo_lon' => isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0,
                            'site_isfg' => $outletData->Refrigerator == "Yes" ? 1 : 0,
                            'site_issg' => $outletData->ShopSining == "Yes" ? 1 : 0,
                            'lfcl_id' => 1,
                            'aemp_eusr' => $outletData->up_emp_id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        $SiteLog = new SiteLogRfl();
                        $SiteLog->setConnection($db_conn);
                        $SiteLog->site_id = $existId->id;
                        $SiteLog->aemp_iusr = $outletData->up_emp_id;
                        $SiteLog->save();
                        DB::connection($db_conn)->commit();

                        $result_data = array(
                            'success' => 1,
                            'message' => " Outlet Open Successful  ",
                            'code' => $site_code,
                            'id' => $existId->id,
                        );
                    }
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public function RFLOpenOutletCount(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                        SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_site`
                        WHERE (aemp_iusr='$SR_ID' OR aemp_eusr='$SR_ID') AND (Date(`created_at`)=CURDATE() OR Date(`updated_at`)=CURDATE())");
        }
        return $data1;

    }

    public function RFLOpenOutletInfo(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                        SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
                        t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
                        FROM `tm_site`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
                        WHERE (t1.aemp_iusr='$SR_ID'OR t1.aemp_eusr='$SR_ID') AND (Date(t1.`created_at`)='$Date' OR Date(t1.`updated_at`)='$Date')
                    ");

        }
        return $data1;

    }

    public function GetChallanWiseOrderData(Request $request){

        $data1 = array();
        $From_Date = $request->From_Date;
        $To_Date = $request->To_Date;
        $SR_ID = $request->SR_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
                t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity, SUM( t1.ordd_qnty ) AS totalDeliveryItemQuantity,
                ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
                FROM tt_ordd AS t1
                INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
                INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
                WHERE t2.ordm_date
                BETWEEN '$From_Date'
                AND '$To_Date'
                AND t2.aemp_id = '$SR_ID'
                AND t1.ordd_oamt!=0
                AND t3.lfcl_id=1 
                AND t2.lfcl_id!=11
                GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date
                UNION ALL
                SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
                t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity, SUM( t1.ordd_qnty ) AS totalDeliveryItemQuantity,
                ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
                FROM tt_ordd AS t1
                INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
                INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
                WHERE t2.ordm_date
                BETWEEN '$From_Date'
                AND '$To_Date'
                AND t2.aemp_id = '$SR_ID'
                AND t1.ordd_oamt=0
                AND t3.lfcl_id=1
                AND t2.lfcl_id !=11 
                GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date 
            ");
        }
        return $data1;
    }

    public function SubmitChallanWiseDelivery(Request $request){

        $outletData = json_decode($request->delivery_data_challan_wise)[0];
        $outletData_child = json_decode($request->delivery_data_challan_wise);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();

                try {

                    $Trip_master1 = new Trip();
                    $Trip_master1->setConnection($db_conn);

                    $Trip_master1->trip_otid = 0;
                    $Trip_master1->aemp_tusr = $outletData_child[0]->SR_ID;
                    $Trip_master1->trip_code = '';
                    $Trip_master1->trip_date = date('Y-m-d');
                    $Trip_master1->trip_vdat = date('Y-m-d');
                    $Trip_master1->trip_ldat = date('Y-m-d');
                    $Trip_master1->trip_cdat = date('Y-m-d');
                    $Trip_master1->aemp_vusr = 0;
                    $Trip_master1->aemp_lusr = 0;
                    $Trip_master1->aemp_cusr = 0;
                    $Trip_master1->dpot_id = 1;
                    $Trip_master1->dlrm_id = $outletData_child[0]->Dealer_ID;
                    $Trip_master1->vhcl_id = 1;
                    $Trip_master1->ttyp_id = 1;
                    $Trip_master1->lfcl_id = 25;
                    $Trip_master1->cont_id = $outletData_child[0]->country_id;
                    $Trip_master1->aemp_iusr = $request->up_emp_id;
                    $Trip_master1->aemp_eusr = $request->up_emp_id;
                    $Trip_master1->save();

                    foreach ($outletData_child as $orderLineData) {

                        $orderLine = new TripSku();
                        $orderLine->setConnection($db_conn);
                        $orderLine->trip_id = $Trip_master1->id;
                        $orderLine->amim_id = $orderLineData->P_ID;
                        $orderLine->troc_iqty = $orderLineData->Order_P_Qty;
                        $orderLine->troc_cqty = $orderLineData->Order_P_Qty;
                        $orderLine->troc_dqty = $orderLineData->Delivery_P_Qty;
                        $orderLine->troc_lqty = 0;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $orderLineData->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;
                        $orderLine->save();

                        $isFree_item1 = 0;
                        if ($orderLineData->P_Total_Price == 0) {
                            $isFree_item1 = 1;
                        }
                        $order_details = DB::connection($db_conn)->table('tt_ordm')->join('tt_ordd',
                            'tt_ordm.id', '=', 'tt_ordd.ordm_id')->where(['tt_ordm.aemp_id' => $orderLineData->SR_ID,
                            'tt_ordm.ordm_date' => $orderLineData->Order_Date, 'tt_ordm.lfcl_id' => 1, 'tt_ordd.ordd_smpl' => $isFree_item1, 'tt_ordd.amim_id' => $orderLineData->P_ID])
                            ->select('tt_ordd.id AS id', 'tt_ordd.ordd_qnty AS ordd_qnty',
                                'tt_ordd.ordd_opds AS ordd_opds', 'tt_ordd.ordd_uprc AS ordd_uprc',
                                'tt_ordd.ordd_smpl AS ordd_smpl')->orderBy('tt_ordd.id')->get();

                        $del_qty = 0;

                        foreach ($order_details as $order_details1) {


                            if ($order_details1->ordd_qnty < $orderLineData->Delivery_P_Qty - $del_qty) {
                                $dd_amt = 0;
                                $isFree_item = $order_details1->ordd_smpl;
                                if ($isFree_item == 1) {
                                    $dd_amt = 0;
                                } else {
                                    $dd_amt = ($order_details1->ordd_qnty * $order_details1->ordd_uprc) - $order_details1->ordd_opds;
                                }


                                DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                                ])->update(['ordd_dqty' => $order_details1->ordd_qnty, 'ordd_amnt' => $dd_amt, 'ordd_odat' => $order_details1->ordd_qnty * $order_details1->ordd_uprc]);
                                $del_qty = $del_qty + $order_details1->ordd_qnty;

                            } else {
                                $dd_amt = 0;
                                $isFree_item = $order_details1->ordd_smpl;
                                if ($isFree_item == 1) {
                                    $dd_amt = 0;
                                } else {
                                    $dd_amt = (($orderLineData->Delivery_P_Qty - $del_qty) * $order_details1->ordd_uprc) - $order_details1->ordd_opds;
                                }


                                DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                                ])->update(['ordd_dqty' => $orderLineData->Delivery_P_Qty - $del_qty, 'ordd_amnt' => $dd_amt, 'ordd_odat' => ($orderLineData->Delivery_P_Qty - $del_qty) * $order_details1->ordd_uprc]);

                                $del_qty = $del_qty + $orderLineData->Delivery_P_Qty - $del_qty;
                            }


                            DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                            ])->update(['ordd_amnt' => $dd_amt]);

                        }

                    }


                    $order_master = DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $outletData_child[0]->SR_ID,
                        'ordm_date' => $outletData_child[0]->Order_Date, 'lfcl_id' => 1])->get();

                    foreach ($order_master as $TripLineData) {
                        $Trip_master = new TripOrder();
                        $Trip_master->setConnection($db_conn);

                        $Trip_master->trip_id = $Trip_master1->id;
                        $Trip_master->ordm_id = $TripLineData->id;
                        $Trip_master->ondr_id = 0;
                        $Trip_master->lfcl_id = 11;
                        $Trip_master->cont_id = $outletData_child[0]->country_id;
                        $Trip_master->aemp_iusr = $request->up_emp_id;
                        $Trip_master->aemp_eusr = $request->up_emp_id;
                        $Trip_master->save();

                    }

                    DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $outletData_child[0]->SR_ID,
                        'ordm_date' => $outletData_child[0]->Order_Date, 'lfcl_id' => 1])->update(['lfcl_id' => 11]);


                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Delivery Successfully",
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }

    public function employeeOutletOrderInfo(Request $request){

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $order_master = DB::connection($db_conn)->select("
                                SELECT t1.site_name AS OutLet_Name,t2.site_id AS OutLet_ID,t2.ordm_date
                                FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
                                where t2.ordm_date = '$start_date'
                                AND t2.aemp_id='$aemp_id'
                                AND t2.lfcl_id=1
                                GROUP BY t2.ordm_date,t2.site_id
                            ");

            foreach ($order_master as $index => $data1) {
                $order_OutLet_Name = $data1->OutLet_Name;
                $site_id = $data1->OutLet_ID;
                $order_date = $data1->ordm_date;
                $data2 = DB::connection($db_conn)->select("
                                SELECT t2.id AS Order_ID,t2.ordm_ornm AS Order_Code,t2.ordm_date,t1.id as parentId
                                FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
                                where t2.ordm_date = '$start_date'
                                AND t2.aemp_id='$aemp_id'
                                AND t2.lfcl_id=1
                                AND t2.site_id=$site_id
                                GROUP BY t2.ordm_date,t2.ordm_ornm
                            ");

                $order_master[$index]->orderIdLists = $data2;
            }
            return $order_master;
        }
    }

    public function GetOutletWiseOrderDetails(Request $request){

        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;
        $OutLet_ID = $request->OutLet_ID;
        $Order_ID = $request->Order_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT 
                            t2.slgp_id AS Group_Id,
                            t2.dlrm_id AS Dealer_Id,
                            t2.rout_id AS Route_Id,
                            t1.`amim_id`AS Item_Id,
                            t3.amim_code AS Item_Code,
                            t3.amim_name As Item_Name,
                            t1.`ordd_qnty`As Order_Qty,
                            t1.`ordd_qnty`As deliveryQty,
                            t1.`ordd_duft`As Item_Factor,
                            t1.`ordd_uprc`As Unit_Price,
                            t1.ordd_opds As Promotional_Discount,
                            t1.ordd_spdo As Special_Discount,
                            t1.prom_id As prom_id,
                            t1.`ordd_oamt`AS Order_Amt,
                            t1.`ordd_oamt` AS deliveryAmount,
                            t2.geo_lat,
                            t2.geo_lon,
                            '0'AS Item_Stock_B_Qty
                                FROM `tt_ordd`t1 JOIN tt_ordm t2 ON(t2.id=t1.`ordm_id`)
                                LEFT JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
                                WHERE t2.id='$Order_ID' AND t2.ordm_date = '$Order_Date' 
                                AND t2.aemp_id='$SR_ID'
                                AND t2.lfcl_id=1
                                AND t3.lfcl_id=1
                                GROUP BY t2.slgp_id ,t1.ordd_spdo,
                            t2.dlrm_id ,
                            t2.rout_id ,
                            t1.`amim_id`,
                            t3.amim_code ,
                            t3.amim_name ,
                            t1.`ordd_qnty`,
                            t1.`ordd_duft`,
                            t1.`ordd_uprc`,
                            t1.ordd_opds ,
                            t1.`ordd_oamt`,
                            t2.geo_lat,
                            t2.geo_lon,t1.prom_id
                        ");
        }
        return $data1;

    }

    public function SubmitInvoiceWiseDelivery(Request $request){

        $outletData = json_decode($request->invoice_wise_delivery)[0];
        $outletData_child = json_decode($request->invoice_wise_delivery);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();

                try {

                    $Trip_master1 = new Trip();
                    $Trip_master1->setConnection($db_conn);

                    $Trip_master1->trip_otid = 0;
                    $Trip_master1->aemp_tusr = $request->SR_ID;
                    $Trip_master1->trip_code = '';
                    $Trip_master1->trip_date = date('Y-m-d');
                    $Trip_master1->trip_vdat = date('Y-m-d');
                    $Trip_master1->trip_ldat = date('Y-m-d');
                    $Trip_master1->trip_cdat = date('Y-m-d');
                    $Trip_master1->aemp_vusr = 0;
                    $Trip_master1->aemp_lusr = 0;
                    $Trip_master1->aemp_cusr = 0;
                    $Trip_master1->dpot_id = 1;
                    $Trip_master1->dlrm_id = $outletData_child[0]->dealerId;
                    $Trip_master1->vhcl_id = 1;
                    $Trip_master1->ttyp_id = 1;
                    $Trip_master1->lfcl_id = 25;
                    $Trip_master1->cont_id = $request->country_id;
                    $Trip_master1->aemp_iusr = $request->SR_ID;
                    $Trip_master1->aemp_eusr = $request->SR_ID;
                    $Trip_master1->save();

                    foreach ($outletData_child as $orderLineData) {

                        $orderLine = new TripSku();
                        $orderLine->setConnection($db_conn);
                        $orderLine->trip_id = $Trip_master1->id;
                        $orderLine->amim_id = $orderLineData->itemId;
                        $orderLine->troc_iqty = $orderLineData->orderQty;
                        $orderLine->troc_cqty = $orderLineData->orderQty;
                        $orderLine->troc_dqty = $orderLineData->deliveryQty;
                        $orderLine->troc_lqty = 0;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $request->country_id;
                        $orderLine->aemp_iusr = $request->SR_ID;
                        $orderLine->aemp_eusr = $request->SR_ID;
                        $orderLine->save();

                        $order_details = DB::connection($db_conn)->table('tt_ordm')->join('tt_ordd',
                            'tt_ordm.id', '=', 'tt_ordd.ordm_id')->where(['tt_ordm.ordm_date' => $request->order_date,
                            'tt_ordm.lfcl_id' => 1, 'tt_ordm.id' => $orderLineData->invoiceId])
                            ->select('tt_ordd.id AS id', 'tt_ordd.ordd_qnty AS ordd_qnty',
                                'tt_ordd.ordd_opds AS ordd_opds', 'tt_ordd.ordd_uprc AS ordd_uprc',
                                'tt_ordd.ordd_smpl AS ordd_smpl')->orderBy('tt_ordd.id')->get();

                                
                        $dd_amt = 0;
                        
                        if ($orderLineData->unitPrice < 1) {
                            $dd_amt = 0;
                        } else {
                            $dd_amt = ($orderLineData->deliveryQty * $orderLineData->unitPrice) - $orderLineData->promotionalDiscount;
                        }
                        DB::connection($db_conn)->table('tt_ordd')->where(['ordm_id' => $orderLineData->invoiceId, 'amim_id' => $orderLineData->itemId, 'ordd_uprc' => $orderLineData->unitPrice, 'ordd_qnty' => $orderLineData->orderQty
                        ])->update(['ordd_dqty' => $orderLineData->deliveryQty, 'ordd_amnt' => $dd_amt, 'ordd_opds' => $orderLineData->promotionalDiscount, 'ordd_odat' => $orderLineData->deliveryQty * $orderLineData->unitPrice]);
                    }

                    $Trip_master = new TripOrder();
                    $Trip_master->setConnection($db_conn);

                    $Trip_master->trip_id = $Trip_master1->id;
                    $Trip_master->ordm_id = $outletData_child[0]->invoiceId;
                    $Trip_master->ondr_id = 0;
                    $Trip_master->lfcl_id = 11;
                    $Trip_master->cont_id = $request->country_id;
                    $Trip_master->aemp_iusr = $request->SR_ID;
                    $Trip_master->aemp_eusr = $request->SR_ID;
                    $Trip_master->save();

                    DB::connection($db_conn)->table('tt_ordm')->where(['id' => $outletData_child[0]->invoiceId,
                        'ordm_date' => $request->order_date, 'lfcl_id' => 1])->update(['lfcl_id' => 11]);

                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Delivery Successfully",
                    );
                }catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
    }

    public function dmTripWiseSROutletInfo(Request $request){

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_code = $request->emp_code;           
            $trip_m = DB::connection($db_conn)->select("
                            SELECT TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                            emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered 
                            from(
                            SELECT t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t2.AEMP_USNM,
                            t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t2.ORDM_ORNM) totalINv,if(t2.TRIP_STATUS='D',1,'0') Delin
                            FROM `dm_trip`t1 JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
                            JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
                            JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
                            WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N' 
                            GROUP by t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,t3.geo_lon,t2.TRIP_STATUS) f
                            group by TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                            emp_id,aemp_name,geo_lat,geo_lon
                        ");
            return $trip_m;
        }

    }

    public function dmItemWiseTripDetails(Request $request){

        $trip_m = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {            
            $trip_code = $request->trip_code;
            $trip_m = DB::connection($db_conn)->select("
                            SELECT t2.amim_name,t1.`AMIM_ID`,t1.`AMIM_CODE`,SUM(t1.`INV_QNTY`)AS INV_QNTY,
                            SUM(t1.`DELV_QNTY`)AS DELV_QNTY,(SUM(t1.`INV_QNTY`)-SUM(t1.`DELV_QNTY`))AS Avail_Qty,
                            1 AS Type 
                            FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id) 
                            WHERE t1.`TRIP_NO`='$trip_code'
                            GROUP BY t1.`AMIM_ID`,t1.`AMIM_CODE` 
                            UNION ALL
                            SELECT t3.amim_name,t2.amim_id,t3.amim_code,0 AS INV_QNTY ,
                            0 AS DELV_QNTY,SUM(t2.rtdd_dqty) AS Avail_Qty,
                            2 AS Type 
                            FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
                            JOIN tm_amim t3 ON(t2.amim_id=t3.id) 
                            WHERE t1.`dm_trip`='$trip_code'
                            AND t2.rtdd_dqty!=0 
                            GROUP BY t3.amim_name,t2.amim_id,t3.amim_code      
                        ");
            return $trip_m;
        }

    }


    public function dmSiteWiseOrderDetails(Request $request){

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $trip_code = $request->trip_code;

            $trip_m = DB::connection($db_conn)->select("
                            SELECT t3.id AS tpm_id,1 AS Type,t1.`TRIP_STATUS`AS Status,t1.`ORDM_ORNM`,t2.site_code,t2.site_name
                            FROM `dm_trip_detail`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
                            JOIN dm_trip t3 ON (t1.TRIP_NO=t3.TRIP_NO)
                            WHERE t1.`TRIP_NO`='$trip_code'      
                            AND t1.`SITE_ID`= $site_id      
                            GROUP BY t1.`ORDM_ORNM`,t1.`TRIP_STATUS`
                            UNION ALL
                            SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name 
                            FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
                            JOIN tm_site t3 ON(t1.`site_id`=t3.id)
                            WHERE t1.`site_id`=$site_id AND t1.`lfcl_id`=1 AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
                            GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`
                            UNION ALL
                            SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name 
                            FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
                            JOIN tm_site t3 ON(t1.`site_id`=t3.id)
                            WHERE t1.`site_id`=$site_id AND t1.dm_trip='$trip_code' AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
                            GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`
                        ");

            foreach ($trip_m as $index => $data1) {
                $order_code = $data1->ORDM_ORNM;
                $data2 = DB::connection($db_conn)->select("
                            SELECT t1.`OID`AS trp_line,
                            t1.`ORDM_ORNM`,
                            t1.`ORDM_DRDT`,
                            t1.`AMIM_ID`,
                            t1.`AMIM_CODE`,
                            t2.amim_name AS Item_Name,
                            t1.ORDD_UPRC AS Rate,
                            t1.ORDD_EXCS AS EXCS_Percent,
                            t1.ORDD_OVAT AS VAT_Percent,
                            t1.`prom_id`,
                            t1.`ORDD_QNTY`,
                            t1.`INV_QNTY`,
                            t1.`DELV_QNTY`,
                            t1.DISCOUNT
                            FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id)
                            WHERE t1.`ORDM_ORNM`='$order_code'
                            GROUP BY t1.`OID`,t1.`AMIM_ID`
                            UNION ALL 
                            SELECT t2.id AS trp_line,
                            t1.rtan_rtnm AS ORDM_ORNM,
                            t2.`rtdd_edat` AS ORDM_DRDT,
                            t2.amim_id AS AMIM_ID,
                            t3.amim_code AS AMIM_CODE,
                            t3.amim_name AS Item_Name,
                            t2.rtdd_uprc AS Rate,
                            0 AS EXCS_Percent,
                            0 AS VAT_Percent,
                            0 AS prom_id,
                            t2.rtdd_qnty AS ORDD_QNTY,
                            t2.rtdd_qnty AS INV_QNTY,
                            t2.rtdd_dqty AS DELV_QNTY,
                            0 AS DISCOUNT  
                            FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
                            JOIN tm_amim t3 ON(t2.amim_id=t3.id)
                            WHERE t1.`rtan_rtnm`='$order_code'
                            GROUP BY t2.id,t2.amim_id
                        ");
                $trip_m[$index]->orderIdLists = $data2;
            }
            return $trip_m;
        }
    }

    public function dmSiteWiseCollectionDetailsData(Request $request){

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            // $trip_code = $request->trip_code;

            $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
            round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,0 AS Coll_Amt,1 AS Type
            FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.`SITE_ID`=$site_id 
            AND t1.`DELIVERY_STATUS`=11 
            AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.COLLECTION_AMNT AS Due_Amt,t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.SITE_ID=$site_id AND
            t1.STATUS=11 AND 
            t1.STATUS!=26 AND
            t1.INVT_ID=5      
           ");
            return $Coll_Data;
        }
    }

    public function SRSiteWiseCollectionListData(Request $request){
        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $emp_id = $request->emp_id;
            $Coll_Data = DB::connection($db_conn)->select("
                                SELECT t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,COUNT(`ORDM_ORNM`)AS InVoice_Qty
                                FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
                                JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
                                WHERE t1.`AEMP_ID`=$emp_id
                                AND t1.`DELIVERY_STATUS`=11 
                                AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
                                GROUP BY t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name  
                            ");
            return $Coll_Data;
        }
    }

    public function SRSiteWiseCollectionDetailsData(Request $request){

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $Coll_Data = DB::connection($db_conn)->select("
                                SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
                                round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,0 AS Coll_Amt,1 AS Type
                                FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
                                JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
                                WHERE t1.`SITE_ID`=$site_id 
                                AND t1.`DELIVERY_STATUS`=11 
                                AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
                                UNION ALL
                                SELECT t1.`ACMP_CODE`,
                                t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
                                t1.`IBS_INVOICE`, round((t1.COLLECTION_AMNT-t1.COLL_REC_HO),4)AS Due_Amt,
                                t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type
                                FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
                                JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
                                WHERE t1.SITE_ID=$site_id 
                                AND t1.STATUS=11 
                                AND t1.COLL_REC_HO!=t1.COLLECTION_AMNT 
                                AND t1.INVT_ID=5  
                            ");
            return $Coll_Data;
        }
    }

    public function reWardsOfferList(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT t2.kpim_id,t1.`kpim_code`,t1.`kpim_name`,t2.kpid_maxp,t2.kpid_minp,t2.kpid_fqty,t2.kpid_disp,
                            t2.kpid_disa,t3.id AS ktyp_id,t3.ktyp_name,t2.kfit_id,t4.kfit_code,t4.kfit_name,t4.kfit_pont
                            FROM `tm_kpim`t1 JOIN tm_kpid t2 ON(t1.`id`=t2.kpim_id)
                            JOIN tm_ktyp t3 ON(t1.`ktyp_id`=t3.id)
                            JOIN tm_kfit t4 ON(t2.kfit_id=t4.id)
                            WHERE t1.`slgp_id`=$request->slgp_id AND t1.`zone_id`=$request->zone_id AND t1.`kpim_sdat` <= CURDATE()
                            AND t1.`kpim_edat` >= CURDATE() AND t1.`lfcl_id`=1
                        ");
        }
        return $data1;
    }

    public function reWardsGiftList(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT `id`,`kfit_code`,`kfit_name`,`kfit_iqty`,`kfit_pont` 
                            FROM tm_kfit t1
                            WHERE t1.`slgp_id`=$request->slgp_id 
                            AND t1.`zone_id`=$request->zone_id 
                            AND t1.`lfcl_id`=1
                        ");
        }
        return $data1;
    }
 
    public function reWardsStatementsDataList(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT t2.id AS earn_pid,t2.kpim_name AS earn_point_name,
                            t1.rwdd_epnt AS earn_point,t3.id AS use_pid,t3.kfit_name AS use_point_name,
                            t1.rwdd_upnt AS use_point,
                            t1.rwdd_ptyp AS point_type,t1.`created_at`AS date_time
                            FROM `tt_rwdd`t1 left JOIN tm_kpim t2 ON(t1.kpim_id=t2.id) 
                            left JOIN tm_kfit t3 ON(t1.kfit_id=t3.id) 
                            WHERE t1.`aemp_id`=$request->emp_id AND 
                            date(t1.created_at)>= SUBDATE(DATE(NOW()), 7)
                        ");
        }
        return $data1;
    }

    public function tutorialDataList(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tutorial_master = DB::connection($db_conn)->select("
                                        SELECT `id`,`ttop_name`,`ttop_code`,`ttop_vdid`,`ttop_vurl`
                                        FROM `tm_ttop` 
                                        WHERE `slgp_id`=$request->slgp_id AND 
                                        `zone_id`=$request->zone_id AND 
                                        `lfcl_id`=1
                                    ");

            foreach ($tutorial_master as $index => $data1) {
                $tt_id = $data1->id;
                $data2 = DB::connection($db_conn)->select("
                                    SELECT `id`, `ttop_id`,`qutn_name`,`qutn_opta`,`qutn_optb`,`qutn_optc`,`qutn_optd`,`qutn_crta`
                                    FROM `tm_qutn` 
                                    WHERE `ttop_id`=$tt_id
                                ");
                $tutorial_master[$index]->questionLists = $data2;
            }
            return $tutorial_master;
        }
    }


    public function preOrderCancel(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $table = "receive_data";
            $table_data = "";
            $status_id = 18;
            if ($request->role_id == 1) {
                $status_id = 16;
            }
            if ($request->role_id > 2) {
                $status_id = 21;
            }

            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $request->order_id)->first();
            if ($orderMaster != null) {
                $orderMaster->lfcl_id = $status_id;
                $orderMaster->ocrs_id = $request->reason_id;
                $orderMaster->aemp_cusr = $request->emp_id;
                $orderMaster->save();
                $table_data = "Order Successfully Canceled";
            }

            return Array($table => $table_data);
        }
    }

    public function orderCancelReason(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
                        SELECT
                        concat(id, ocrs_name) AS column_id,
                        id                    AS reason_id,
                        ocrs_name             AS reason_name
                        FROM tm_ocrs AS t1
                        WHERE t1.cont_id = $request->country_id
                    ");
            return Array(
                "tbld_order_cancel_reason" => array("data" => $tst, "action" => $request->country_id),
            );
        }
    }


    public function siteHistoryList(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country->cont_conn != '') {

            $site = collect(DB::connection($country->cont_conn)->select("
                                SELECT
                                t1.id                              AS site_id,
                                t1.site_code                       AS site_code,
                                t1.site_name                       AS site_name,
                                t1.site_ownm                       AS site_ownm,
                                t1.site_mob1                       AS site_mob1,
                                concat(t2.mktm_name, ' < ', t3.ward_name, ' < ', t4.than_name, ' < ', t5.dsct_name, ' < ', t6.disn_name) AS mktm_name
                                FROM tm_site AS t1
                                INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
                                INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
                                INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
                                INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
                                INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
                                WHERE t1.id = '$request->site_id'
                            "))->first();

            $visit = collect(DB::connection($country->cont_conn)->select("
                                SELECT
                                count(t2.ssvh_ispd) AS visit,
                                sum(t2.ssvh_ispd)   AS productive
                                FROM tm_site AS t1
                                INNER JOIN th_ssvh AS t2 ON t1.id = t2.site_id
                                WHERE t1.id = '$request->site_id' AND t2.ssvh_date BETWEEN '$request->start_date' AND '$request->end_date'
                            "))->first();

            $order = collect(DB::connection($country->cont_conn)->select("
                                SELECT
                                sum(t2.ordd_oamt)          AS order_amount,
                                sum(t2.ordd_odat)          AS invoice_amouont,
                                count(DISTINCT t2.amim_id) AS amim_count,
                                count(DISTINCT t3.itsg_id) AS itsg_count,
                                count(t2.id)               AS line_count
                                FROM tt_ordm AS t1
                                INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                WHERE t1.site_id = '$request->site_id' AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'
                            "))->first();

            $visitRoute = collect(DB::connection($country->cont_conn)->select("
                                SELECT count(t1.site_id) AS vsit_cout
                                FROM tl_rsmp AS t1
                                INNER JOIN tl_rpln AS t2 ON t1.rout_id = t2.rout_id
                                WHERE t1.site_id = '$request->site_id'
                            "))->first();
            
            $data_responce = array(
                "site_id" => $site->site_id,
                "site_code" => $site->site_code,
                "site_name" => $site->site_name,
                "owner_name" => $site->site_ownm,
                "mobile" => $site->site_mob1,
                "area_name" => $site->mktm_name,
                "visitCount" => $visit->visit,
                "invoiceCount" => $visit->productive,
                "skuCount" => $order->amim_count,
                "routeFrequency" => $visitRoute->vsit_cout,
                "subCategoryCount" => $order->itsg_count,
                "orderAmount" => $order->order_amount,
                "invoiceAmount" => $order->invoice_amouont,
                "lpc" => number_format($order->line_count / $visit->productive, 2),
            );
            return Array("receive_data" => [$data_responce], "action" => $request->site_id);
        }
    }



    public function siteOrderList(Request $request){
        
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tst = DB::connection($db_conn)->select("
                        SELECT
                        t1.ordm_ornm                            AS order_id,
                        t1.ordm_amnt                            AS order_amount,
                        concat(t2.aemp_usnm, '-', t2.aemp_name) AS sr_name,
                        t1.ordm_date                            AS order_date,
                        t1.lfcl_id                              AS status_id,
                        t4.lfcl_name                            AS status,
                        sum(t5.ordd_odat)                       AS invoice_amount,
                        1                                       AS type_id
                        FROM tt_ordm AS t1
                        INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                        INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
                        INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
                        INNER JOIN tt_ordd AS t5 ON t1.id = t5.ordm_id
                        WHERE
                        t1.site_id = '$request->site_id' AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'
                        GROUP BY
                        t1.ordm_ornm,
                        t1.ordm_amnt,
                        t2.aemp_usnm,
                        t2.aemp_name,
                        t1.ordm_date,
                        t1.lfcl_id,
                        t4.lfcl_name  
                    ");
            return Array("receive_data" => array("data" => $tst, "action" => $request->site_id));
        }
    }

    public function censusOutletImport(Request $request){
        
        $country = Country::findorfail($request->country_id);
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Add on Route",
        );
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site = Site::on($db_conn)->where(['site_code' => $request->site_code])->first();
            if ($site != null) {
                $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $request->route_id])->first();
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
                    $routeSite->save();
                    $result_data = array(
                        'success' => 1,
                        'message' => "Adding Successful",
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
                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "Already Exits",
                    );
                }
            }
        }
        return $result_data;
    }


    public function mapAroundOutlet(Request $request){

        $whereCondition="";
        $distance = $request->distance;
        if(isset($request->dist_id)){
            $whereCondition.="and  t1.code!='$request->dist_id' ";
        }
        if(isset($request->soft_name)){
            $whereCondition.="and  t1.business_group='$request->soft_name' ";
        }
        if(isset($request->company_name)){
            $whereCondition.="and  t1.company='$request->company_name'";
        }
        if(isset($request->group_name)){
            $whereCondition.=" and t1.sales_group ='$request->group_name' ";
        }
        if(isset($request->dist_type)){
            $whereCondition.=" and t2.short_name ='$request->dist_type' ";
        }
        $lat = $request->lat;
        $lon = $request->lon;
        $query="SELECT *
                    FROM (
                        SELECT
                            t1.id                                               AS site_id,
                            concat(t1.name, '-', t1.code)                       AS site_name,
                            t1.mobile                                           AS mobile,
                            t1.lat                                              AS lat,
                            t1.lon                                              AS lon,
                            ''                                                  AS region_name,
                            ''                                                  AS zone_name,
                            ''                                                  AS base_name,
                            ''                                                  AS emp_name,
                            ''                                                  AS sv_name,
                            getDistance(t1.lat, t1.lon, $lat, $lon) AS dist_dif,
                            0                                                  AS time_dif,
                            1                                                   AS type,
                            ''                                                  AS last_time,
                            1                                                   AS is_verified,
                            t1.code                                             AS user_name,
                            t1.id                                               AS emp_id,
                            t1.id                                               AS emp_code,
                            1                                                   AS role_id,
                            0                                                   AS channel_id,
                            t1.business_group                                   AS bu,
                            t1.company                                          AS group_name,
                            t1.sales_group                                      AS sales_group,
                            ''                                                  AS role_name,
                            t2.name                                             AS type_name,
                            t2.short_name as type_code
                        FROM tbld_distributor AS t1
                            INNER JOIN tbld_distributor_type AS t2 ON t1.distributor_type_id = t2.id
                        WHERE t1.lat > 0 $whereCondition
                        HAVING dist_dif < $distance
                    ) AS t1";
        $tst = DB::select($query);
        return Array("receive_data" => array("data" => $tst, "action" => 1));
    }

    public
    function MQRUpdateOutletLocationFromMap(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                // $site = SiteMqr::on($db_conn)->findorfail($request->Outlet_Code);
                $site = SiteMqr::on($db_conn)->where(['site_code' => $request->Outlet_Code])->first();

                $site->geo_lat = isset($request->latitude) ? $request->latitude : $site->geo_lat;
                $site->geo_lon = isset($request->longitude) ? $request->longitude : $site->geo_lon;

                $site->aemp_eusr = $request->up_emp_id;
                $site->save();
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Outlet Location Updated Successfully",
                    'Outlet_ID' => $site->id,
                    'Outlet_Code' => $site->site_code,
                    'geo_lat' => $site->geo_lat,
                    'geo_lon' => $site->geo_lon,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }

        }
    }


    public
    function MQRoutletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select(
                "select t1.id as Category_Code,
t1.otcg_name as Category_Name 
from tm_mtcg as t1");

        }
        return $data1;

    }

    public function MQROutletSave(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet",
        );
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    // $ran = SiteRandom::on($db_conn)->where(['number' => $outletData->Outlet_Code])->first();

                    $ran = DB::connection($db_conn)->table('tbld_mqr_random')->where('number', $outletData->Outlet_Code)->first();

                    if ($ran != null) {

                        $site1 = SiteMqr::on($db_conn)->where(['site_code' => $outletData->Outlet_Code])->first();
                        if ($site1 == null) {

                            $site = new SiteMqr();
                            $site->setConnection($db_conn);

                            $site->site_name = $outletData->Outlet_Name;
                            $site->site_code = $outletData->Outlet_Code;
                            $site->site_olcd = $outletData->Outlet_Old_Code;
                            $site->outl_id = 1;
                            $site->site_olnm = '';
                            $site->site_adrs = '';
                            $site->site_olad = '';
                            $site->mktm_id = $outletData->Market_ID;
                            $site->site_ownm = '';
                            $site->site_olon = '';
                            $site->site_mob2 = '';
                            $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                            $site->site_emal = '';
                            $site->scnl_id = 1;
                            $site->otcg_id = $outletData->Shop_Category_id;
                            $site->site_imge = $outletData->Outlet_Image;;
                            $site->site_omge = '';
                            $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                            $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                            $site->site_reg = '';
                            $site->site_vrfy = 0;
                            $site->site_hsno = '';
                            $site->site_vtrn = '';
                            $site->site_vsts = 0;
                            $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                            $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;

                            $site->site_isib = $outletData->IceBox == "Yes" ? 1 : 0;
                            $site->site_isdf = $outletData->DeepFredge == "Yes" ? 1 : 0;
                            $site->site_isvc = $outletData->VC == "Yes" ? 1 : 0;

                            $site->cont_id = $outletData->country_id;
                            $site->lfcl_id = 1;
                            $site->aemp_iusr = $outletData->up_emp_id;
                            $site->aemp_eusr = $outletData->up_emp_id;
                            $site->save();
                            DB::connection($db_conn)->commit();
                            $result_data = array(
                                'success' => 1,
                                'message' => "  Outlet Open Successful  ",
                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'message' => " Outlet Already Opened \n Using this QR Code!!!  ",
                            );
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => " Wrong QR Code\n Fail to Open Outlet !!! ",
                        );
                    }
                } catch
                (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public
    function MQROpenOutletCount(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_msit`
WHERE aemp_iusr='$SR_ID' AND Date(`created_at`)=CURDATE()");

        }
        return $data1;

    }

    public
    function QROpenOutletCheck(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Scanned_outlet_id = $request->Scanned_outlet_id;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
          SELECT `id`AS Outlet_ID,site_code AS Outlet_Code,`site_name`AS Outlet_Name,`site_olnm`AS Outlet_Name_Bangla,
          `site_adrs`AS Outlet_Address,`site_olad`AS outlet_address_Bangla,
         `site_ownm`AS Outlet_Owner_Name,`site_mob1`AS Outlet_Mobile_No,`site_isfg`AS refrigerator,`site_issg`AS shop_sign
          FROM `tm_site`
          WHERE `site_code`='$Scanned_outlet_id'
          ");

        }
        return $data1;

    }

    public
    function MQROpenOutletInfo(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
FROM `tm_msit`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
WHERE  t1.aemp_iusr='$SR_ID' AND Date(t1.`created_at`)='$Date' 
");

        }
        return $data1;

    }

    public
    function MQRUpdateOutletInfo(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
FROM `tm_msit`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
WHERE  t1.aemp_eusr='$SR_ID' AND Date(t1.`updated_at`)='$Date' 
");

        }
        return $data1;

    }

    public
    function MQRupdateOutletSave(Request $request)
    {
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = SiteMqr::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name != "" ? $outletData->Outlet_Name : '';
                    $site->site_olnm = $outletData->Outlet_Name_BN != "" ? $outletData->Outlet_Name_BN : '';
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    $site->site_imge = $outletData->Outlet_Image != "" ? $outletData->Outlet_Image : '';
                    $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                    $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;

                    $site->site_isib = $outletData->IceBox == "Yes" ? 1 : 0;
                    $site->site_isdf = $outletData->DeepFredge == "Yes" ? 1 : 0;
                    $site->site_isvc = $outletData->VC == "Yes" ? 1 : 0;

                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                    $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    $site->aemp_eusr = $outletData->up_emp_id;
                    $site->save();
                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Updated Successfully",
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
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }

    public
    function MQROpenOutletCheck(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Scanned_outlet_id = $request->Scanned_outlet_id;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
          SELECT `id`AS Outlet_ID,site_code AS Outlet_Code,`site_name`AS Outlet_Name,`site_olnm`AS Outlet_Name_Bangla,
          `site_adrs`AS Outlet_Address,`site_olad`AS outlet_address_Bangla,
         `site_ownm`AS Outlet_Owner_Name,`site_mob1`AS Outlet_Mobile_No,
         `site_isfg`AS refrigerator,`site_issg`AS shop_sign,
         `site_isib`AS ice_box,`site_isdf`AS deep_fridge,`site_isvc`AS vc
          FROM `tm_msit`
          WHERE `site_code`='$Scanned_outlet_id'
          ");

        }
        return $data1;

    }
    

    public
    function UsingThana_GetMarket(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $send_district_code = $request->send_district_code;
        $send_thana_code = $request->send_thana_code;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT t3.id AS mktm_code,t3.mktm_name
FROM tm_than t1 INNER JOIN tm_ward t2 ON (t1.id=t2.than_id)
INNER JOIN tm_mktm t3 ON (t2.id=t3.ward_id)
 WHERE t1.id='$send_thana_code'
 GROUP BY t3.id ,t3.mktm_name
");

        }
        return $data1;

    }

    

    public
    function User_SetUpMarket(Request $request)
    { 

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $user_day_name = $request->user_day_name;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
 SELECT
  t2.`id` as market_id,
  t2.mktm_name AS `market_name`
 FROM `tm_mktm`t2 JOIN tl_srmd t1 ON(t2.id=t1.mktm_id)
 WHERE t1.`lfcl_id` = '1' AND
 t1.`srmd_day` = '$user_day_name'AND t1.aemp_id='$request->EmpId'
 GROUP BY t2.`id` ,t2.mktm_name
");

        }
        return $data1;

    }

    public
    function aroundOutlet_UsingMarket(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
   SELECT
       t1.id                                                        AS Outlet_ID,
       t1.site_code                                                 AS Outlet_Code,
       TRIM(t1.site_name)                                                AS Outlet_Name,
       t1.site_mob1                                                 AS Mobile,
       t2.mktm_name                                                 AS Market_Name,
      TRIM(t1.site_olad)                                          AS Address,
       t1.geo_lat                                                   AS Lat,
       t1.geo_lon                                                   AS Lon,
       t1.site_ownm                                                 AS OwnerName
       FROM `tm_site` AS t1
       INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
       WHERE t1.`mktm_id`='$request->user_market_id'
       AND t1.site_code NOT LIKE '%U%'
       AND t1.site_code NOT LIKE '%N%'
       AND t1.`lfcl_id` = '1'
GROUP BY t1.id,
t1.site_code,
t1.site_name,
t1.site_mob1,
t2.mktm_name,
t1.site_olad,
t1.geo_lat,
t1.geo_lon,
t1.site_ownm ORDER BY t1.site_name
     ");

        }
        return $data1;

    }

    public
    function aroundOutlet_UsingSearch(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  t1.id                                                        AS Outlet_ID,
  t1.site_code                                                 AS Outlet_Code,
 TRIM( t1.site_name)                                           AS Outlet_Name,
  t1.site_mob1                                                 AS Mobile,
  t2.mktm_name                                                 AS Market_Name,
  TRIM(t1.site_olad)                                           AS Address,
  t1.geo_lat                                                   AS Lat,
  t1.geo_lon                                                   AS Lon,
  t1.site_ownm                                                 AS OwnerName
FROM `tm_site` AS t1
  INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
WHERE  t1.site_name LIKE'%$request->user_search_text%' 
AND t1.`mktm_id`='$request->user_market_id'
AND t1.site_code NOT LIKE '%U%'
AND t1.site_code NOT LIKE '%N%'
AND t1.lfcl_id='1'
GROUP BY t1.id,
t1.site_code,
t1.site_name,
t1.site_mob1,
t2.mktm_name,
t1.site_olad,
t1.geo_lat,
t1.geo_lon,
t1.site_ownm
     ");

        }
        return $data1;

    }

    public
    function outletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select(
                "select t1.id as Category_Code,
t1.otcg_name as Category_Name 
from tm_otcg as t1");

        }
        return $data1;

    }

    public function QROutletSave(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet",
        );
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    $site1 = Site::on($db_conn)->where(['site_code' => $outletData->Outlet_Code])->first();
                    if ($site1 == null) {

                        $site = new Site();
                        $site->setConnection($db_conn);

                        $site->site_name = $outletData->Outlet_Name;
                        $site->site_code = $outletData->Outlet_Code;
                        $site->outl_id = 1;
                        $site->site_olnm = '';
                        $site->site_adrs = '';
                        $site->site_olad = '';
                        $site->mktm_id = $outletData->Market_ID;
                        $site->site_ownm = '';
                        $site->site_olon = '';
                        $site->site_mob2 = '';
                        $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                        $site->site_emal = '';
                        $site->scnl_id = 1;
                        $site->otcg_id = $outletData->Shop_Category_id;
                        $site->site_imge = $outletData->Outlet_Image;;
                        $site->site_omge = '';
                        $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                        $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                        $site->site_reg = '';
                        $site->site_vrfy = 0;
                        $site->site_hsno = '';
                        $site->site_vtrn = '';
                        $site->site_vsts = 0;
                        $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                        $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                        $site->cont_id = $outletData->country_id;
                        $site->lfcl_id = 1;
                        $site->aemp_iusr = $outletData->up_emp_id;
                        $site->aemp_eusr = $outletData->up_emp_id;
                        $site->save();
                        DB::connection($db_conn)->commit();
                        $result_data = array(
                            'success' => 1,
                            'message' => "  Outlet Open Successful  ",
                        );
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => " Outlet Already Opened \n Using this QR Code!!!  ",
                        );
                    }
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public
    function QROpenOutletCount(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_site`
WHERE aemp_iusr='$SR_ID' AND Date(`created_at`)=CURDATE()");

        }
        return $data1;

    }

    public function SubmitUseRewardPoint(Request $request){
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();

            try {
                $redM = RewardMaster::on($db_conn)->where(['aemp_id' => $request->emp_id])->first();

                $Red_details = new RewardsDetails();
                $Red_details->setConnection($db_conn);

                $Red_details->rwdm_id = $redM->id;
                $Red_details->aemp_id = $redM->aemp_id;
                $Red_details->rwdd_epnt = 0;
                $Red_details->rwdd_upnt = $request->use_point;
                $Red_details->kpim_id = 0;
                $Red_details->kfit_id = $request->kfit_id;
                $Red_details->rwdd_ptyp = 2;
                $Red_details->cont_id = $request->country_id;
                $Red_details->save();

                $epoint = $redM->rwdm_rpnt - $request->use_point;
                $upoint = $redM->rwdm_upnt + $request->use_point;

                DB::connection($db_conn)->table('tm_rwdm')->where(['id' => $redM->id
                ])->update(['rwdm_rpnt' => $epoint, 'rwdm_upnt' => $upoint]);

                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Successfully Use Point ",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }
 
    /*
    *   API MODEL V3 [END]
    */

     

}