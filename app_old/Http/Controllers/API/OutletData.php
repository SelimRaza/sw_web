<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/28/2018
 * Time: 9:42 AM
 */

namespace App\Http\Controllers\API;

use App\BusinessObject\BaseSiteMapping;
use App\BusinessObject\SiteStock;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\SiteVisitHistory;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Outlet;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function sitePermission(Request $request)
    {
        $siteVisit = new SiteVisit();
        $siteVisit->emp_id = $request->emp_id;
        $siteVisit->site_id = $request->site_id;
        $siteVisit->route_id = $request->route_id;
        $siteVisit->date = $request->date;
        $siteVisit->country_id = $request->country_id;
        $siteVisit->save();
        return array('column_id' => $request->id);
    }


    public function siteVisited(Request $request)
    {
        DB::beginTransaction();
        try {
            $siteVisited = new SiteVisitHistory();
            $siteVisited->emp_id = $request->emp_id;
            $siteVisited->site_id = $request->site_id;
            $siteVisited->date = $request->date;
            $siteVisited->start_time = $request->start_time;
            $siteVisited->visit_status_id = $request->visit_status_id;
            $siteVisited->end_time = $request->end_time;
            $siteVisited->spend_time = $request->spend_time;
            $siteVisited->lat = $request->lat;
            $siteVisited->lon = $request->lon;
            $siteVisited->distance = $request->distance;
            $siteVisited->country_id = $request->country_id;
            $siteVisited->updated_by = $request->emp_id;
            $siteVisited->created_by = $request->emp_id;
            $siteVisited->save();

            $siteVisit = SiteVisited::where(['site_id' => $request->site_id, 'date' => $request->date])->first();
            if ($siteVisit == null) {
                $siteVisit = new SiteVisited();
                $siteVisit->emp_id = $request->emp_id;
                $siteVisit->site_id = $request->site_id;
                $siteVisit->date = $request->date;
                $siteVisit->spend_time = $request->spend_time;
                $siteVisit->visit_count = 1;
                $siteVisit->is_productive = $request->visit_status_id == 1 ? 1 : 0;
                $siteVisit->country_id = $request->country_id;
                $siteVisit->updated_by = $request->emp_id;
                $siteVisit->created_by = $request->emp_id;
                $siteVisit->save();
            } else {
                $siteVisit->spend_time = $siteVisit->spend_time + $request->spend_time;
                $siteVisit->visit_count = $siteVisit->visit_count + 1;
                $siteVisit->is_productive = $siteVisit->is_productive == 0 ? ($request->visit_status_id == 1 ? 1 : 0) : 1;
                $siteVisit->updated_by = $request->emp_id;
                $siteVisit->save();
            }
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return array('column_id' => $e);
        }
    }

    public function siteStock(Request $request)
    {
        $siteStock = new SiteStock();
        $siteStock->sku_id = $request->sku_id;
        $siteStock->site_id = $request->site_id;
        $siteStock->qty = $request->qty;
        $siteStock->date = $request->date;
        $siteStock->country_id = $request->country_id;
        $siteStock->created_by = $request->up_emp_id;
        $siteStock->updated_by = $request->up_emp_id;
        $siteStock->save();
        return array('column_id' => $request->id);
    }

    public function siteUnverified(Request $request)
    {
        $site = Site::findorfail($request->site_id);
        $site->is_verified = 0;
        $site->save();
        return array('column_id' => $request->id);
    }


    public function createNewSiteNew(Request $request)
    {

        DB::beginTransaction();
        try {
            $outlet = new Outlet();
            $outlet->name = $request->site_name;
            $outlet->code = $request->site_name;
            $outlet->country_id = $request->country_id;
            $outlet->created_by = $request->up_emp_id;
            $outlet->updated_by = $request->up_emp_id;
            $outlet->save();
            $site = new Site();
            $site->name = $request->site_name;
            $site->code = $request->site_name;
            $site->ln_name = $request->site_ln_name;
            $site->address = $request->address;
            $site->ln_address = $request->ln_address;
            $site->map_address = $request->map_address != "" ? $request->map_address : '';
            $site->zip_code = $request->zip_code;
            $site->owner_name = $request->owner_name;
            $site->ln_owner_name = $request->owner_ln_name;
            $site->mobile_1 = $request->mobile_1;
            $site->mobile_2 = $request->mobile_2 != "" ? $request->mobile_2 : '0';
            $site->email = "";
            $site->house_no = "";
            $site->vat_trn = "";
            $site->sub_channel_id = $request->category_id;
            $site->grade_id = $request->grade_id;
            $site->outlet_id = $outlet->id;
            $site->site_image = $request->site_image;
            $site->owner_image = '';
            $site->lat = $request->lat;
            $site->lon = $request->lon;
            $site->is_verified = 1;
            $site->status_id = 1;
            $site->country_id = $request->country_id;
            $site->created_by = $request->up_emp_id;
            $site->updated_by = $request->up_emp_id;
            $site->save();
            $routeSite = new RouteSite();
            $routeSite->site_id = $site->id;
            $routeSite->route_id = $request->route_id;
            $routeSite->country_id = $request->country_id;
            $routeSite->created_by = $request->up_emp_id;
            $routeSite->updated_by = $request->up_emp_id;
            $routeSite->save();
            $baseSiteMapping = new BaseSiteMapping();
            $baseSiteMapping->base_id = $request->base_id;
            $baseSiteMapping->site_id = $site->id;
            $baseSiteMapping->sales_group_id = $request->group_id;
            $baseSiteMapping->country_id = $request->country_id;;
            $baseSiteMapping->created_by = $request->up_emp_id;
            $baseSiteMapping->updated_by = $request->up_emp_id;
            $baseSiteMapping->save();
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }


    }

    public function siteVerify(Request $request)
    {


        DB::beginTransaction();
        try {
            if ($request->verify_type_id == 0) {
                $site = Site::findorfail($request->site_id);
                $site->name = $request->site_name;
                // $site->code = $request->site_name;
                $site->ln_name = $request->site_ln_name;
                $site->address = $request->address;
                $site->ln_address = $request->ln_address;
                $site->owner_name = $request->owner_name;
                $site->ln_owner_name = $request->owner_ln_name;
                $site->mobile_1 = $request->mobile_1;
                $site->mobile_2 = $request->mobile_2 != "" ? $request->mobile_2 : '0';
                $site->sub_channel_id = $request->category_id;
                $site->grade_id = $request->grade_id;
                $site->site_image = $request->site_image;
                $site->map_address = $request->map_address != "" ? $request->map_address : '';
                $site->zip_code = $request->zip_code;
                $site->lat = $request->lat;
                $site->lon = $request->lon;
                $site->is_verified = 1;
                $site->updated_by = $request->up_emp_id;
                $site->save();
                $outlet = Outlet::findorfail($site->outlet()->id);
                $outlet->name = $request->site_name;
                $outlet->code = $request->site_name;
                $outlet->updated_by = $request->up_emp_id;
                $outlet->save();
            } else {
                $site = Site::findorfail($request->site_id);
                $site->lat = $request->lat;
                $site->lon = $request->lon;
                $site->is_verified = 1;
                $site->updated_by = $request->up_emp_id;
                $site->save();
            }

            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return array('column_id' => $request->id);
        }


    }


    public function siteVerifyNew(Request $request)
    {

        DB::beginTransaction();
        try {

            $site = Site::findorfail($request->site_id);
            $site->name = $request->site_name;
            // $site->code = $request->site_name;
            $site->ln_name = $request->site_ln_name;
            $site->address = $request->address;
            $site->ln_address = $request->ln_address;
            $site->owner_name = $request->owner_name;
            $site->ln_owner_name = $request->owner_ln_name;
            $site->mobile_1 = $request->mobile_1;
            $site->mobile_2 = $request->mobile_2 != "" ? $request->mobile_2 : '0';
            $site->sub_channel_id = $request->category_id;
            $site->grade_id = $request->grade_id;
            $site->site_image = $request->site_image;
            $site->map_address = $request->map_address != "" ? $request->map_address : '';
            $site->zip_code = $request->zip_code;
            $site->lat = $request->lat;
            $site->lon = $request->lon;
            $site->is_verified = 1;
            $site->updated_by = $request->up_emp_id;
            $site->save();
            $outlet = Outlet::findorfail($site->outlet()->id);
            $outlet->name = $request->site_name;
            $outlet->code = $request->site_name;
            $outlet->updated_by = $request->up_emp_id;
            $outlet->save();
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            return array('column_id' => $request->id);
        }


    }

    public function preRoute(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id,!isnull(t5.site_id)) AS column_id,
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id,!isnull(t5.site_id))                               AS token,
  t3.site_id                                                                     AS site_id,
  concat(t4.name, '(', t4.ln_name, ')')                                          AS site_name,
  t4.outlet_id                                                                   AS outlet_id,
  t1.id                                                                          AS route_id,
  t2.name                                                                        AS route_name,
  t1.route_id,
  t1.day                                                                         AS day_name,
  concat(t4.owner_name, '(', t4.ln_owner_name, ')')                              AS owner_name,
  t4.mobile_1                                                                    AS owner_mobile,
  concat(t4.address, '(', t4.ln_address, ')')                                    AS address,
 !isnull(t5.site_id)                                     AS visited,
  1                                                                              AS pay_mode,
  0                                                                              AS order_amount,
  t4.house_no,
  t4.vat_trn,
  t4.lat,
  t4.lon
FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_route_site_mapping AS t3 ON t2.id = t3.route_id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tblt_site_visited as t5 ON t3.site_id=t5.site_id and t5.date=curdate()
WHERE t1.emp_id = $request->emp_id AND t1.day = DAYNAME(curdate()) AND t4.status_id = 1 AND t4.is_verified = 1
UNION ALL
SELECT
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id,!isnull(t4.site_id)) AS column_id,
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id,!isnull(t4.site_id)) AS token,
  t1.site_id                                                                      AS site_id,
  concat(t3.name, '(', t3.ln_name, ')')                                           AS site_name,
  t3.outlet_id                                                                    AS outlet_id,
  t2.id                                                                           AS route_id,
  ''                                                                              AS route_name,
  t1.route_id,
  DAYNAME(curdate())                                                              AS day_name,
  concat(t3.owner_name, '(', t3.ln_owner_name, ')')                               AS owner_name,
  t3.mobile_1                                                                     AS owner_mobile,
  concat(t3.address, '(', t3.ln_address, ')')                                     AS address,
 !isnull(t4.site_id)                                                              AS visited,
  1                                                                               AS pay_mode,
  0                                                                               AS order_amount,
  t3.house_no,
  t3.vat_trn,
  t3.lat,
  t3.lon,
  t3.is_verified
FROM tblt_site_visit_permission AS t1
 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
   LEFT JOIN tblt_site_visited as t4 ON t3.id=t4.site_id and t4.date=curdate()
WHERE t1.emp_id = $request->emp_id AND t3.is_verified = 1 AND t3.status_id = 1 and t1.date=curdate()");
        return Array("tblt_route_site" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function preRouteNew(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id,!isnull(t5.site_id),if(t5.is_productive>0,1,0)) AS column_id,
  concat(t4.id, t4.status_id, t1.route_id, t1.day, t4.is_verified, t4.status_id,!isnull(t5.site_id),if(t5.is_productive>0,1,0))                               AS token,
  t3.site_id                                                                     AS site_id,
  concat(t4.name, '(', t4.ln_name, ')')                                          AS site_name,
  t4.outlet_id                                                                   AS outlet_id,
  t1.id                                                                          AS route_id,
  t2.name                                                                        AS route_name,
  t1.route_id,
  t1.day                                                                         AS day_name,
  concat(t4.owner_name, '(', t4.ln_owner_name, ')')                              AS owner_name,
  t4.mobile_1                                                                    AS owner_mobile,
  concat(t4.address, '(', t4.ln_address, ')')                                    AS address,
 !isnull(t5.site_id)                                     AS visited,
 if(t5.is_productive>0,1,0) as is_productive,
  1                                                                              AS pay_mode,
  0                                                                              AS order_amount,
  t4.house_no,
  t4.vat_trn,
  t4.lat,
  t4.lon,
  t4.is_verified
FROM tbld_pjp AS t1 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_route_site_mapping AS t3 ON t2.id = t3.route_id
  INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
  LEFT JOIN tblt_site_visited as t5 ON t3.site_id=t5.site_id and t5.date=curdate()
WHERE t1.emp_id = $request->emp_id AND t1.day = DAYNAME(curdate()) AND t4.status_id = 1 
UNION ALL
SELECT
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id,!isnull(t4.site_id),if(t4.is_productive>0,1,0)) AS column_id,
  concat(t1.date, t2.id, t3.id, DAYNAME(curdate()), t3.is_verified, t3.status_id,!isnull(t4.site_id),if(t4.is_productive>0,1,0)) AS token,
  t1.site_id                                                                      AS site_id,
  concat(t3.name, '(', t3.ln_name, ')')                                           AS site_name,
  t3.outlet_id                                                                    AS outlet_id,
  t2.id                                                                           AS route_id,
  ''                                                                              AS route_name,
  t1.route_id,
  DAYNAME(curdate())                                                              AS day_name,
  concat(t3.owner_name, '(', t3.ln_owner_name, ')')                               AS owner_name,
  t3.mobile_1                                                                     AS owner_mobile,
  concat(t3.address, '(', t3.ln_address, ')')                                     AS address,
 !isnull(t4.site_id)                                                            AS visited,
 if(t4.is_productive>0,1,0) as is_productive,
  1                                                                               AS pay_mode,
  0                                                                               AS order_amount,
  t3.house_no,
  t3.vat_trn,
  t3.lat,
  t3.lon,
  t3.is_verified
FROM tblt_site_visit_permission AS t1
 INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
   LEFT JOIN tblt_site_visited as t4 ON t3.id=t4.site_id and t4.date=curdate()
WHERE t1.emp_id = $request->emp_id  AND t3.status_id = 1 and t1.date=curdate()");
        return Array("tblt_route_site" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empRouteList(Request $request)
    {
        $tst = DB::select("SELECT
 concat(t1.day,t1.emp_id,t2.name,  t2.id) AS column_id,
  concat(t1.day,t1.emp_id,t2.name,  t2.id) AS token,
  t1.emp_id     AS emp_id,
  t2.name       AS route_name,
  t2.id         AS route_id,
  t2.base_id    as base_id,
  t4.name       as base_name,
  t1.day
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t1.emp_id = t3.id
  INNER JOIN tbld_base as t4 ON t2.base_id=t4.id
WHERE t1.emp_id = $request->emp_id OR t3.manager_id = $request->emp_id
GROUP BY t1.id,t1.emp_id,t1.route_id, t2.name, t2.id, t2.base_id,t4.name,t1.day");
        return Array("tblt_route" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empSKUList(Request $request)
    {
        $tst = DB::select("SELECT
 concat(t1.emp_id,t2.name,  t2.id) AS column_id,
  concat(t1.emp_id,t2.name,  t2.id) AS token,
  t1.emp_id     AS emp_id,
  t2.name       AS route_name,
  t2.id         AS route_id,
  t2.base_id    as base_id,
  t4.name       as base_name
FROM tbld_pjp AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_employee AS t3 ON t1.emp_id = t3.id
  INNER JOIN tbld_base as t4 ON t2.base_id=t4.id
WHERE t1.emp_id = $request->emp_id OR t3.manager_id = $request->emp_id
GROUP BY t1.emp_id, t1.route_id, t2.name, t2.id, t2.base_id,t4.name");
        return Array("tblt_route" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empRouteSiteList(Request $request)
    {
        $tst = DB::select("SELECT
  t1.site_id    AS site_id,
  t3.name       AS site_name,
  t3.owner_name AS owner_name,
  t3.mobile_1   AS owner_mobile,
  t3.address    AS address,
  0             AS is_verified,
  1             AS pay_mode,
  ''            AS last_visit,
  t3.house_no
FROM tbld_route_site_mapping AS t1
  INNER JOIN tbld_route AS t2 ON t1.route_id = t2.id
  INNER JOIN tbld_site AS t3 ON t1.site_id = t3.id
WHERE t1.route_id = $request->route_id");
        return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empRouteSiteMapping(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.site_id, t1.route_id) AS column_id,
  concat(t1.site_id, t1.route_id) AS token,
  t1.site_id,
  t1.route_id
FROM tbld_route_site_mapping AS t1
INNER JOIN tbld_pjp as t2 ON t1.route_id=t2.route_id
INNER JOIN tbld_employee as t3 ON t2.emp_id=t3.id
WHERE t3.id=$request->emp_id or t3.manager_id=$request->emp_id");
        return Array("tblt_route_site_mapping" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empSiteList(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.id, t1.sub_channel_id, t1.grade_id, t1.is_verified,t1.status_id) AS column_id,
  concat(t1.id, t1.sub_channel_id, t1.grade_id, t1.is_verified,t1.status_id) AS token,
  t1.id as site_id,
  t1.name as site_name,
  t1.ln_name as site_ln_name,
  t1.address,
  t1.ln_address,
  t1.owner_name,
  t1.ln_owner_name,
  t1.mobile_1,
  t1.mobile_2,
  t1.sub_channel_id as category_id,
  t1.grade_id,
  t1.is_verified
FROM tbld_site AS t1
  INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
  INNER JOIN tbld_pjp AS t3 ON t2.route_id = t3.route_id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
WHERE (t4.id = $request->emp_id OR t4.manager_id = $request->emp_id) and t1.status_id=1
GROUP BY t1.id,t1.sub_channel_id, t1.grade_id,t1.is_verified,t1.name,t1.ln_name,t1.address,t1.ln_address,t1.owner_name,t1.ln_owner_name,t1.mobile_1,t1.mobile_2,t1.status_id");
        return Array("tbld_site" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function empSiteListNew(Request $request)
    {
        $tst = DB::select("SELECT
  concat(curdate(),t1.id, t1.sub_channel_id, t1.grade_id, t1.is_verified,t1.status_id) AS column_id,
  concat(curdate(),t1.id, t1.sub_channel_id, t1.grade_id, t1.is_verified,t1.status_id) AS token,
  t1.id as site_id,
  t1.name as site_name,
  t1.ln_name as site_ln_name,
  t1.address,
  t1.ln_address,
  t1.owner_name,
  t1.ln_owner_name,
  t1.mobile_1,
  t1.mobile_2,
  t1.sub_channel_id as category_id,
  t1.grade_id,
  t1.is_verified,
  t1.zip_code,
  t1.map_address
FROM tbld_site AS t1
  INNER JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id
  INNER JOIN tbld_pjp AS t3 ON t2.route_id = t3.route_id
  INNER JOIN tbld_employee AS t4 ON t3.emp_id = t4.id
WHERE (t4.id = $request->emp_id OR t4.manager_id = $request->emp_id) and t1.status_id=1
GROUP BY t1.id,t1.sub_channel_id, t1.grade_id,t1.is_verified,t1.name,t1.ln_name,t1.address,t1.ln_address,t1.owner_name,t1.ln_owner_name,t1.mobile_1,t1.mobile_2,t1.status_id,t1.zip_code,t1.map_address");
        return Array("tbld_site" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function outletCategory(Request $request)
    {

        $tst = DB::select("SELECT
  concat(t1.id, t1.name, t1.code) AS column_id,
  concat(t1.id, t1.name, t1.code) AS token,
  t1.id AS category_id,
  t1.name AS category_name
FROM tbld_sub_channel  AS t1  WHERE t1.country_id=$request->country_id");
        return Array("tbld_outlet_category" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function outletGrade(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.id, t1.name, t1.code) AS column_id,
  concat(t1.id, t1.name, t1.code) AS token,
  t1.id AS grade_id,
  t1.name AS grade_name
FROM tbld_outlet_grade AS t1 WHERE t1.country_id=$request->country_id");
        return Array("tbld_outlet_grade" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function siteVisitStatus(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.id, t1.name, t1.code) AS column_id,
  concat(t1.id, t1.name, t1.code) AS token,
  t1.id AS status_id,
  t1.name AS status_name
FROM tbld_site_visit_status AS t1 WHERE t1.id!=1 and t1.country_id=$request->country_id");
        return Array("tbld_site_visit_status" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function siteOrderList(Request $request)
    {
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
  t4.lfcl_name");
            return Array("receive_data" => array("data" => $tst, "action" => $request->site_id));
        }
    }

    public function siteProductList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $tst = DB::connection($db_conn)->select("SELECT
  t5.amim_id        AS product_id,
  t6.amim_name      AS product_name,
  t6.amim_dunt      AS ctn_size,
  sum(t5.ordd_qnty) AS order_qty,
  sum(t5.ordd_dqty) AS delivery_qty,
  sum(t5.ordd_opds) AS discount,
  sum(t5.ordd_oamt) AS total_amount,
  sum(t5.ordd_odat) AS net_amount,
  1                 AS type_id,
  t7.itsg_name      AS sub_category
FROM tt_ordm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id
  INNER JOIN tt_ordd AS t5 ON t1.id = t5.ordm_id
  INNER JOIN tm_amim AS t6 ON t5.amim_id = t6.id
  INNER JOIN tm_itsg AS t7 ON t6.itsg_id = t7.id
WHERE
  t1.site_id = '$request->site_id' AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'
GROUP BY t5.amim_id, t6.amim_name, t6.amim_dunt, t7.itsg_name
");
            return Array("receive_data" => array("data" => $tst, "action" => $request->site_id));

        }
    }

    public function siteOrderLineList(Request $request)
    {
        $tst = DB::select("SELECT
  t1.order_id                 AS order_id,
  t2.sku_id                   AS product_id,
  t3.name                     AS product_name,
  t3.ctn_size                 AS ctn_size,
  t2.unit_price               AS unit_price,
  t2.unit_price * t3.ctn_size AS ctn_price,
  t2.qty_order                AS order_qty,
  t2.qty_order                AS Confirm_qty,
  0                           AS default_discount,
  0                           AS discount,
  0                           AS confirm_discount,
  0                           AS promo_discount,
  0                           AS confirm_default_discount,
  0                           AS confirm_promo_discount,
  t2.total_order              AS total_amount,
  t2.total_order              AS total_confirm_amount,
  t2.promo_ref_id             AS promo_ref
FROM tblt_order_master AS t1
  INNER JOIN tblt_order_line AS t2 ON t1.id = t2.so_id
  INNER JOIN tbld_sku AS t3 ON t2.sku_id = t3.id
WHERE
  t1.order_id = '$request->order_id' ");
        return Array("receive_data" => array("data" => $tst, "action" => $request->order_id));
    }

    public function siteHistoryList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country->cont_conn != '') {

            $site = collect(DB::connection($country->cont_conn)->select("SELECT
  t1.id                                                                                            AS site_id,
  t1.site_code                                                                                     AS site_code,
  t1.site_name                                                                                     AS site_name,
  t1.site_ownm                                                                                     AS site_ownm,
  t1.site_mob1                                                                                     AS site_mob1,
  concat(t2.mktm_name, ' < ', t3.ward_name, ' < ', t4.than_name, ' < ', t5.dsct_name, ' < ', t6.disn_name) AS mktm_name
FROM tm_site AS t1
  INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
  INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
  INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
  INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
  INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
WHERE t1.id = '$request->site_id'"))->first();

            $visit = collect(DB::connection($country->cont_conn)->select("SELECT
  count(t2.ssvh_ispd) AS visit,
  sum(t2.ssvh_ispd)   AS productive
FROM tm_site AS t1
  INNER JOIN th_ssvh AS t2 ON t1.id = t2.site_id
WHERE t1.id = '$request->site_id' AND t2.ssvh_date BETWEEN '$request->start_date' AND '$request->end_date'"))->first();

            $order = collect(DB::connection($country->cont_conn)->select("SELECT
  sum(t2.ordd_oamt)          AS order_amount,
  sum(t2.ordd_odat)          AS invoice_amouont,
  count(DISTINCT t2.amim_id) AS amim_count,
  count(DISTINCT t3.itsg_id) AS itsg_count,
  count(t2.id)               AS line_count
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.site_id = '$request->site_id' AND t1.ordm_date BETWEEN '$request->start_date' AND '$request->end_date'"))->first();

            $visitRoute = collect(DB::connection($country->cont_conn)->select("SELECT count(t1.site_id) AS vsit_cout
FROM tl_rsmp AS t1
  INNER JOIN tl_rpln AS t2 ON t1.rout_id = t2.rout_id
WHERE t1.site_id = '$request->site_id'"))->first();
            //  $site = Site::on($db_conn)->find($request->site_id);
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

    public function noteList(Request $request)
    {

        $country_id = $request->country_id;
        $country = (new Country())->country($country_id);
        if ($country != null) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.id                                          AS note_id,
  t1.site_code                                   AS site_id,
  t2.site_name                                   AS site_name,
  DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.note_titl                                   AS title,
  t1.note_body                                   AS note,
  t1.lfcl_id                                     AS status_id,
  t3.lfcl_name                                   AS status_name,
  t1.ntpe_id                                     AS type_id,
  t4.ntpe_name                                   AS type_name,
  group_concat(t5.nimg_imag)                     AS image_name,
  t1.aemp_id                                     AS emp_id,
  concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name
FROM tt_note AS t1
  LEFT JOIN tm_site AS t2 ON t1.site_code = t2.site_code
  INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
  INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
  LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
  INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
  INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id
WHERE t1.site_code='$request->site_id' and t8.enmp_date BETWEEN '$request->start_date 'and '$request->end_date'
GROUP BY t1.id, t1.site_code, t2.site_name, t1.note_dtim, t1.note_titl, t1.note_body, t1.lfcl_id, t3.lfcl_name,
  t1.ntpe_id, t4.ntpe_name,
  t1.aemp_id, t6.aemp_usnm, t6.aemp_name
ORDER BY t1.note_dtim DESC");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }

}