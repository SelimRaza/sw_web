<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\BaseSiteMapping;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Outlet;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\SiteVisit;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v2\OrderModuleDataUAE;
use Illuminate\Support\Facades\DB;

class OutletData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function createNewSite(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $country = Country::on($country->cont_conn)->findorfail($request->country_id);
                $site_code = 'N' . str_pad($country->attr4 + 1, 9, '0', STR_PAD_LEFT);
                $country->attr4 = $country->attr4 + 1;
                $country->save();
                $outlet = new Outlet();
                $outlet->setConnection($country->cont_conn);
                $outlet->oult_name = $request->site_name;
                $outlet->oult_code = $site_code;
                $outlet->oult_olnm = isset($request->site_ln_name) ? $request->site_ln_name : '';
                $outlet->oult_adrs = isset($request->address) ? $request->address : '';
                $outlet->oult_olad = isset($request->ln_address) ? $request->ln_address : '';
                $outlet->oult_ownm = isset($request->owner_name) ? $request->owner_name : '';
                $outlet->oult_olon = isset($request->owner_ln_name) ? $request->owner_ln_name : '';
                $outlet->oult_mob1 = isset($request->mobile_1) ? $request->mobile_1 : '';
                $outlet->oult_mob2 = isset($request->mobile_2) ? $request->mobile_2 : '';
                $outlet->oult_emal = isset($request->site_emal) ? $request->site_emal : '';
                $outlet->cont_id = $request->country_id;
                $outlet->lfcl_id = 7;
                $outlet->aemp_iusr = $request->up_emp_id;
                $outlet->aemp_eusr = $request->up_emp_id;
                $outlet->save();
                $site = new Site();
                $site->setConnection($country->cont_conn);
                $site->site_name = $request->site_name;
                $site->site_code = $site_code;
                $site->outl_id = $outlet->id;
                $site->site_olnm = isset($request->site_ln_name) ? $request->site_ln_name : '';
                $site->site_adrs = isset($request->address) ? $request->address : '';
                $site->site_olad = isset($request->ln_address) ? $request->ln_address : '';
                $site->mktm_id = $request->mktm_id;
                $site->site_ownm = isset($request->owner_name) ? $request->owner_name : '';
                $site->site_olon = isset($request->owner_ln_name) ? $request->owner_ln_name : '';
                $site->site_mob1 = isset($request->mobile_1) ? $request->mobile_1 : '';
                $site->site_mob2 = isset($request->mobile_2) ? $request->mobile_2 : '';
                $site->site_emal = isset($request->site_emal) ? $request->site_emal : '';
                $site->scnl_id = $request->grade_id;
                $site->otcg_id = $request->category_id;
                $site->site_imge = $request->site_image;
                $site->site_omge = '';
                $site->geo_lat = $request->lat;
                $site->geo_lon = $request->lon;
                $site->site_reg = isset($request->site_reg) ? $request->site_reg : '';
                $site->site_vrfy = 0;
                $site->site_hsno = isset($request->site_hsno) ? $request->site_hsno : '';
                $site->site_vtrn = isset($request->site_vtrn) ? $request->site_vtrn : '';
                $site->site_vsts = 0;
                $site->cont_id = $request->country_id;
                $site->lfcl_id = 7;
                $site->aemp_iusr = $request->up_emp_id;
                $site->aemp_eusr = $request->up_emp_id;
                $site->save();
                $routeSite = new RouteSite();
                $routeSite->setConnection($country->cont_conn);
                $routeSite->site_id = $site->id;
                $routeSite->rout_id = $request->route_id;
                $routeSite->rspm_serl = 0;
                $routeSite->cont_id = $request->country_id;
                $routeSite->lfcl_id = 1;
                $routeSite->aemp_iusr = $request->up_emp_id;
                $routeSite->aemp_eusr = $request->up_emp_id;
                $routeSite->save();

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                throw $e;
            }
        }

    }

    public function siteVerify(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                if ($request->verify_type_id == 0) {
                    $site = Site::on($country->cont_conn)->findorfail($request->site_id);
                    $site->site_olnm = isset($request->site_ln_name) ? $request->site_ln_name : '';
                    $site->site_adrs = isset($request->address) ? $request->address : '';
                    $site->site_olad = isset($request->ln_address) ? $request->ln_address : '';
                    $site->site_ownm = isset($request->owner_name) ? $request->owner_name : '';
                    $site->site_olon = isset($request->owner_ln_name) ? $request->owner_ln_name : '';
                    $site->site_mob1 = isset($request->mobile_1) ? $request->mobile_1 : '';
                    $site->site_mob2 = isset($request->mobile_2) ? $request->mobile_2 : '';
                    $site->site_emal = isset($request->site_emal) ? $request->site_emal : '';
                    $site->site_imge = $request->site_image;
                    $site->geo_lat = $request->lat;
                    $site->geo_lon = $request->lon;
                    $site->site_reg = isset($request->site_reg) ? $request->site_reg : '';
                    $site->site_vrfy = 1;
                    $site->site_hsno = isset($request->site_hsno) ? $request->site_hsno : '';
                    $site->site_vtrn = isset($request->site_vtrn) ? $request->site_vtrn : '';
                    $site->aemp_eusr = $request->up_emp_id;
                    $site->save();
                    $outlet = Outlet::on($country->cont_conn)->findorfail($site->outlet()->id);
                    $outlet->oult_olnm = isset($request->site_ln_name) ? $request->site_ln_name : '';
                    $outlet->oult_adrs = isset($request->address) ? $request->address : '';
                    $outlet->oult_olad = isset($request->ln_address) ? $request->ln_address : '';
                    $outlet->oult_ownm = isset($request->owner_name) ? $request->owner_name : '';
                    $outlet->oult_olon = isset($request->owner_ln_name) ? $request->owner_ln_name : '';
                    $outlet->oult_mob1 = isset($request->mobile_1) ? $request->mobile_1 : '';
                    $outlet->oult_mob2 = isset($request->mobile_2) ? $request->mobile_2 : '';
                    $outlet->oult_emal = isset($request->site_emal) ? $request->site_emal : '';
                    $outlet->aemp_eusr = $request->up_emp_id;
                    $outlet->save();
                } else {
                    $site = Site::on($country->cont_conn)->findorfail($request->site_id);
                    $site->lat = $request->lat;
                    $site->lon = $request->lon;
                    $site->site_vrfy = 1;
                    $site->aemp_eusr = $request->up_emp_id;
                    $site->save();
                }

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return array('column_id' => $request->id);
            }

        }
    }


    public function without_Today_All_Route_SiteList(Request $request)
    {
        $aemp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
               SELECT t2.rout_id,t4.rout_code,t1.`rpln_day`,t4.rout_name,t2.site_id,t3.site_code,t3.site_name
               FROM `tl_rpln`t1 JOIN tl_rsmp t2 ON(t1.`rout_id`=t2.rout_id)
               JOIN tm_site t3 ON t2.site_id=t3.id
               JOIN tm_rout t4 ON t2.rout_id=t4.id
               WHERE t1.`aemp_id`=$aemp_id
               AND t1.`rout_id`NOT IN(SELECT `rout_id` FROM tl_rpln 
               WHERE `rpln_day`=DAYNAME(curdate()) AND `aemp_id`=$aemp_id) AND t3.site_vrfy=1;
            ");


            return $data1;
        }

    }

    public function sitePermissionRequestList(Request $request)
    {
        $aemp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
               SELECT t1.`site_id`,t2.site_code,t2.site_name,t1.`rout_id`,
               t4.rout_code,t4.rout_name,t3.id AS emp_id,t3.aemp_usnm,t3.aemp_name,t1.`ovpm_date` 
               FROM `tl_ovpr`t1 JOIN tm_site t2 ON t1.`site_id`=t2.id 
               JOIN tm_aemp t3 ON t1.`aemp_id`=t3.id
               JOIN tm_rout t4 ON t1.`rout_id`=t4.id
               WHERE t1.`aemp_mngr`=$aemp_id AND t1.`ovpm_date`=curdate();
            ");


            return $data1;
        }

    }

    public function sitePermission(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->table('tl_ovpm')->insertOrIgnore([
                [
                    'aemp_id' => $request->emp_id,
                    'site_id' => $request->site_id,
                    'rout_id' => $request->route_id,
                    'ovpm_date' => $request->date,
                    'lfcl_id' => 1,
                    'cont_id' => $request->country_id,
                    'aemp_iusr' => $request->up_emp_id,
                    'aemp_eusr' => $request->up_emp_id,
                ]
            ]);
            return array('column_id' => $request->id);
        }

    }

    public function sitePermissionRequestAproved(Request $request)
    {
        $country = (new Country())->country($request->country_id);


        if ($country) {

            $site_line_data = json_decode($request->site_line_data);

            foreach ($site_line_data as $orderLineData) {
                $site_id = $orderLineData->site_id;
                $route_id = $orderLineData->route_id;
                // $emp_id = $orderLineData->emp_id;

                $insert = DB::connection($country->cont_conn)->table('tl_ovpm')->insertOrIgnore([
                    [
                        'aemp_id' => $request->emp_id,
                        'site_id' => $site_id,
                        'rout_id' => $route_id,
                        'ovpm_date' => $request->date,
                        'lfcl_id' => 1,
                        'cont_id' => $request->country_id,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ]
                ]);

                $whereArray = ['aemp_mngr' => $request->up_emp_id, 'site_id' => $site_id, 'rout_id' => $route_id, 'ovpm_date' => $request->date];

                DB::connection($country->cont_conn)->table('tl_ovpr')->where($whereArray)->delete();

            }
            if ($insert) {
                $title = "Site Visit Notification";
                $body = "Your Site Visit Permission Approved !!!";

                (new OrderModuleDataUAE)->give_notification($request->emp_id, $title, $body, $request->country_id);

                $result_data = array(
                    'success' => 1,
                    'message' => "submit request successful !!",
                );
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "submit request Failed !!",
                );
            }

            return $result_data;
        }

    }

    public function sitePermissionRequest(Request $request)
    {
        $country = (new Country())->country($request->country_id);


        if ($country) {

            $aemp_mngr = collect(DB::connection($country->cont_conn)->select("SELECT `aemp_mngr` FROM `tm_aemp` WHERE `id`='$request->emp_id'"))->first();

            if ($aemp_mngr) {
                $aemp_mngr1 = $aemp_mngr->aemp_mngr;

                $site_line_data = json_decode($request->site_line_data);

                foreach ($site_line_data as $orderLineData) {
                    $site_id = $orderLineData->site_id;
                    $route_id = $orderLineData->route_id;

                    $insert = DB::connection($country->cont_conn)->table('tl_ovpr')->insertOrIgnore([
                        [
                            'aemp_id' => $request->emp_id,
                            'aemp_mngr' => $aemp_mngr1,
                            'site_id' => $site_id,
                            'rout_id' => $route_id,
                            'ovpm_date' => $request->date,
                            'lfcl_id' => 1,
                            'cont_id' => $request->country_id,
                            'aemp_iusr' => $request->up_emp_id,
                            'aemp_eusr' => $request->up_emp_id,
                        ]
                    ]);
                }
                if ($insert) {
                    $title = "Site Visit Notification";
                    $body = "Need Site Visit Permission !!\nPlease approve it!!!";

                    (new OrderModuleDataUAE)->give_notification($aemp_mngr1, $title, $body, $request->country_id);

                    $result_data = array(
                        'success' => 1,
                        'message' => "submit request successful !!",
                    );
                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "submit request Failed !!",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "You have no Manager !!",
                );
            }

            return $result_data;
        }

    }
    public function siteAddInRoutePermissionRequestList(Request $request)
    {
        $aemp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
              SELECT t1.`site_id`,t2.site_code,t2.site_name,t1.`rout_id`,
               t4.rout_code,t4.rout_name,t3.id AS emp_id,t3.aemp_usnm,t3.aemp_name,t1.`rspr_date` 
               FROM `tl_rspr`t1 JOIN tm_site t2 ON t1.`site_id`=t2.id 
               JOIN tm_aemp t3 ON t1.`aemp_id`=t3.id
               JOIN tm_rout t4 ON t1.`rout_id`=t4.id
               WHERE t1.`aemp_mngr`=$aemp_id AND t1.`rspr_date`=curdate();
            ");


            return $data1;
        }

    }

    public function siteAddInRoutePermissionRequest(Request $request)
    {
        $country = (new Country())->country($request->country_id);


        if ($country) {

            $aemp_mngr = collect(DB::connection($country->cont_conn)->select("SELECT `aemp_mngr` FROM `tm_aemp` WHERE `id`='$request->emp_id'"))->first();

            if ($aemp_mngr) {
                $aemp_mngr1 = $aemp_mngr->aemp_mngr;

                $site_id = $request->site_id;
                $route_id = $request->route_id;

                $insert = DB::connection($country->cont_conn)->table('tl_rspr')->insertOrIgnore([
                    [
                        'aemp_id' => $request->emp_id,
                        'aemp_mngr' => $aemp_mngr1,
                        'site_id' => $site_id,
                        'rout_id' => $route_id,
                        'rspr_date' => $request->date,
                        'lfcl_id' => 1,
                        'cont_id' => $request->country_id,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ]
                ]);
                if ($insert) {
                    $title = "Site Add to Route Notification";
                    $body = "Need Site Add to Route Permission !!\nPlease approve it!!!";

                    (new OrderModuleDataUAE)->give_notification($aemp_mngr1, $title, $body, $request->country_id);

                    $result_data = array(
                        'success' => 1,
                        'message' => "submit request successful !!",
                    );
                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "submit request Failed !!",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "You have no Manager !!",
                );
            }

            return $result_data;
        }

    }

    public function siteUnverified(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->table('tm_site')->where('id', $request->site_id)->update(['site_vrfy' => 0]);
            return array('column_id' => $request->id);
        }
    }

    public function outletCategory(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.otcg_name, t1.otcg_code) AS column_id,
  t1.id                                     AS category_id,
  t1.otcg_name                              AS category_name
FROM tm_otcg AS t1
WHERE t1.cont_id = $request->country_id");
            return Array("tbld_outlet_category" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function outletGrade(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.scnl_name, t1.scnl_code) AS column_id,
  t1.id                                     AS grade_id,
  t1.scnl_name                              AS grade_name
FROM tm_scnl AS t1
WHERE t1.cont_id = $request->country_id");
            return Array("tbld_outlet_grade" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function district(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.dsct_name AS name,
  t1.dsct_code AS code
FROM tm_dsct AS t1;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function thana(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.than_name AS name,
  t1.than_code AS code
FROM tm_than AS t1
WHERE t1.dsct_id = $request->district_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function ward(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.ward_name AS name,
  t1.ward_code AS code
FROM tm_ward AS t1
WHERE t1.than_id = $request->thana_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function market(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS id,
  t1.mktm_name AS name,
  t1.mktm_code AS code
FROM tm_mktm AS t1
WHERE t1.ward_id = $request->ward_id;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function routeSite(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  t3.id AS site_id,
  t3.site_code,
  t3.site_name,
  t3.site_olnm,
  t3.site_adrs,
  t3.site_olad,
  t3.site_ownm,
  t3.site_olon,
  t3.site_mob1,
  t3.site_mob2,
  t3.site_emal,
  t3.site_reg,
  t3.site_hsno,
  t3.site_vtrn,
  t3.site_vrfy
FROM tm_rout AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.rout_id
  INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
WHERE t1.id = $request->route_id AND t3.lfcl_id IN (1, 7)");
            return array("receive_data" => $data1, "action" => $request->route_id);
        }
    }

    public function siteRoutePlan(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            /*$data1 = DB::connection($db_conn)->select("SELECT
  t4.aemp_id,
  t5.aemp_usnm,
  concat(t5.aemp_name,'(',t5.aemp_usnm,')') as aemp_name,
  t5.aemp_mob1,
  group_concat(LEFT(t4.rpln_day, 3)) AS day_name,
  t7.slgp_name,
  t1.cont_id
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
  INNER JOIN tm_rout AS t3 ON t2.rout_id = t3.id
  INNER JOIN tl_rpln AS t4 ON t3.id = t4.rout_id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_id = t5.id
  LEFT JOIN tl_sgsm AS t6 ON t4.aemp_id = t6.aemp_id
  LEFT JOIN tm_slgp AS t7 ON t6.slgp_id = t7.id
WHERE t1.site_code = '$request->site_code'
GROUP BY t4.aemp_id, t7.slgp_name");*/

            $data1 = DB::connection($db_conn)->select("
  SELECT t4.aemp_id,
  t5.aemp_usnm,
  concat(t5.aemp_name,'(',t5.aemp_usnm,')') as aemp_name,
  t5.aemp_mob1,
  group_concat(LEFT(t4.rpln_day, 3)) AS day_name,
  t7.slgp_name,
  t1.cont_id,
 max(t17.created_at) last_visited
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id and t1.site_code='$request->site_code'
  INNER JOIN tm_rout AS t3 ON t2.rout_id = t3.id
  INNER JOIN tl_rpln AS t4 ON t3.id = t4.rout_id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_id = t5.id
  inner JOIN tl_sgsm AS t6 ON t4.aemp_id = t6.aemp_id
  inner JOIN tm_slgp AS t7 ON t6.slgp_id = t7.id
  LEFT JOIN th_ssvh AS t17 ON t1.id = t17.site_id and t17.ssvh_date > current_date()- interval 30 day and t17.aemp_id=t5.id
WHERE  t1.lfcl_id = 1 
GROUP BY t4.aemp_id,
  t5.aemp_usnm,
  t5.aemp_name,t5.aemp_usnm,
  t5.aemp_mob1,
  t7.slgp_name,
  t1.cont_id
            ");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->site_code),
            );
        }
    }
}