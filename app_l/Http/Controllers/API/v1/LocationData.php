<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 11/15/2020
 * Time: 5:21 PM
 */

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\LocationDetails;
use App\BusinessObject\LocationLog;
use App\BusinessObject\NoteComment;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function comment(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $comment = NoteComment::on($country->cont_conn)->where(['ncom_tokn' => $request->token])->first();
            if ($comment == null) {
                $comment = new NoteComment();
                $comment->setConnection($country->cont_conn);
                $comment->aemp_id = $request->emp_id;
                $comment->note_id = $request->note_id;
                $comment->ncom_note = $request->comment;
                $comment->ncom_date = date('Y-m-d');
                $comment->ncom_time = $request->date_time;
                $comment->aemp_eusr = $request->share_by_id;
                $comment->aemp_iusr = $request->share_by_id;
                $comment->cont_id = $request->country_id;
                $comment->lfcl_id = $request->country_id;
                $comment->ncom_tokn = $request->token;
                $comment->save();
            }
            return array('column_id' => $comment->id);
        }
    }

    public function saveLocation(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $locationLog = new LocationLog();
            $locationLog->setConnection($country->cont_conn);
            $locationLog->ltyp_id = $request->ltyp_id;
            $locationLog->locm_id = $request->locm_id;
            $locationLog->lcmp_id = $request->lcmp_id;
            $locationLog->ldpt_id = $request->ldpt_id;
            $locationLog->lsct_id = $request->lsct_id;
            $locationLog->locl_name = $request->locd_name;
            $locationLog->locl_code = $request->locd_code;
            $locationLog->locl_date = date("Y-m-d", strtotime($request->locd_time));
            $locationLog->locl_time = $request->locd_time;
            $locationLog->geo_lat = $request->geo_lat;
            $locationLog->geo_lon = $request->geo_lon;
            $locationLog->geo_adrs = isset($request->geo_adrs) ? "$request->geo_adrs" : '';
            $locationLog->lfcl_id = 1;
            $locationLog->cont_id = $request->country_id;
            $locationLog->aemp_iusr = $request->up_emp_id;
            $locationLog->aemp_eusr = $request->up_emp_id;
            $locationLog->save();
            $locationDetails = LocationDetails::on($country->cont_conn)->where(['locd_code' => $request->locd_code])->first();
            if ($locationDetails == null) {
                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $locationDetails = new LocationDetails();
                    $locationDetails->setConnection($country->cont_conn);
                    $locationDetails->ltyp_id = $request->ltyp_id;
                    $locationDetails->locm_id = $request->locm_id;
                    $locationDetails->lcmp_id = $request->lcmp_id;
                    $locationDetails->ldpt_id = $request->ldpt_id;
                    $locationDetails->lsct_id = $request->lsct_id;
                    $locationDetails->locd_name = $request->locd_name;
                    $locationDetails->locd_code = $request->locd_code;
                    $locationDetails->locd_date = date("Y-m-d", strtotime($request->locd_time));
                    $locationDetails->locd_time = $request->locd_time;
                    $locationDetails->geo_lat = $request->geo_lat;
                    $locationDetails->geo_lon = $request->geo_lon;
                    $locationDetails->geo_adrs = $request->geo_adrs;
                    $locationDetails->lfcl_id = 1;
                    $locationDetails->cont_id = $request->country_id;
                    $locationDetails->aemp_iusr = $request->up_emp_id;
                    $locationDetails->aemp_eusr = $request->up_emp_id;
                    $locationDetails->save();

                    DB::connection($country->cont_conn)->commit();
                    return response()->json(array('column_id' => $request->id), 200);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                }
            } else {
                DB::connection($country->cont_conn)->beginTransaction();
                try {
                    $locationDetails->ltyp_id = $request->ltyp_id;
                    $locationDetails->locm_id = $request->locm_id;
                    $locationDetails->lcmp_id = $request->lcmp_id;
                    $locationDetails->ldpt_id = $request->ldpt_id;
                    $locationDetails->lsct_id = $request->lsct_id;
                    $locationDetails->locd_name = $request->locd_name;
                    $locationDetails->locd_code = $request->locd_code;
                    $locationDetails->locd_date = date("Y-m-d", strtotime($request->locd_time));
                    $locationDetails->locd_time = $request->locd_time;
                    $locationDetails->geo_lat = $request->geo_lat;
                    $locationDetails->geo_lon = $request->geo_lon;
                    $locationDetails->geo_adrs = $request->geo_adrs;
                    $locationDetails->aemp_eusr = $request->up_emp_id;
                    $locationDetails->save();
                    DB::connection($country->cont_conn)->commit();
                    return response()->json(array('column_id' => $request->id), 200);
                } catch (\Exception $e) {
                    DB::connection($country->cont_conn)->rollback();
                    return $e;
                }
            }

        }

    }

    public function updateLocation(Request $request)
    {
        $outletData = json_decode($request->Location_QR_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                $locationDetails = LocationDetails::on($country->cont_conn)->where(['locd_code' => $outletData->old_locd_code])->first();
                if ($locationDetails == null) {
                    return array(
                        'success' => 0,
                        'message' => "Not Found Existing Location Code",
                    );
                } else {
                    DB::connection($country->cont_conn)->beginTransaction();
                    try {
                       // $locationDetails->ltyp_id = $outletData->ltyp_id;
                        $locationDetails->ltyp_id = $outletData->ltyp_id != "0" ? $outletData->ltyp_id :  $locationDetails->ltyp_id;
                       // $locationDetails->locm_id = $outletData->locm_id;
                        $locationDetails->locm_id = $outletData->locm_id != "0" ? $outletData->locm_id :  $locationDetails->locm_id;
                       // $locationDetails->lcmp_id = $outletData->lcmp_id;
                        $locationDetails->lcmp_id = $outletData->lcmp_id != "0" ? $outletData->lcmp_id :  $locationDetails->lcmp_id;
                       // $locationDetails->ldpt_id = $outletData->ldpt_id;
                        $locationDetails->ldpt_id = $outletData->ldpt_id != "0" ? $outletData->ldpt_id :  $locationDetails->ldpt_id;
                       // $locationDetails->lsct_id = $outletData->lsct_id;
                        $locationDetails->lsct_id = $outletData->lsct_id != "0" ? $outletData->lsct_id :  $locationDetails->lsct_id;
                       // $locationDetails->locd_name = $outletData->locd_name;
                        $locationDetails->locd_name =$outletData->locd_name != "" ? $outletData->locd_name :  $locationDetails->locd_name;

                        $locationDetails->locd_code = $outletData->locd_code;
                        $locationDetails->locd_date = date("Y-m-d", strtotime($outletData->locd_time));
                        $locationDetails->locd_time = $outletData->locd_time;
                        $locationDetails->geo_lat = $outletData->geo_lat;
                        $locationDetails->geo_lon = $outletData->geo_lon;
                        $locationDetails->geo_adrs = $outletData->geo_adrs;
                        $locationDetails->aemp_eusr = $outletData->up_emp_id;
                        $locationDetails->save();
                        DB::connection($db_conn)->commit();
                        return array(
                            'success' => 1,
                            'message' => "Location Code Updated Successfully",
                        );
                    } catch (\Exception $e) {
                        DB::connection($country->cont_conn)->rollback();
                        return $e;
                    }
                }
            }
        }
    }

    public function locationData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
concat(id,locm_name) as column_id,
  id AS locm_id,
  locm_name
FROM tm_locm where cont_id=$request->country_id");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT
concat(id,lcmp_name) as column_id,
  id AS lcmp_id,
  lcmp_name,
  locm_id
FROM tm_lcmp where  cont_id=$request->country_id");

            $tst2 = DB::connection($country->cont_conn)->select("SELECT
concat(id,ldpt_name) as column_id,
  id AS ldpt_id,
  ldpt_name,
  lcmp_id
FROM tm_ldpt where  cont_id=$request->country_id");
            $tst3 = DB::connection($country->cont_conn)->select("SELECT
concat(id,lsct_name) as column_id,
  id AS lsct_id,
  lsct_name,
  ldpt_id
FROM tm_lsct where  cont_id=$request->country_id");

            $tst4 = DB::connection($country->cont_conn)->select("SELECT
concat(id,ltyp_name) as column_id,
  id AS ltyp_id,
  ltyp_name
FROM tm_ltyp where  cont_id=$request->country_id");

            return response()->json(
                Array(
                    "tm_locm" => array("data" => $tst, "action" => $request->country_id),
                    "tm_lcmp" => array("data" => $tst1, "action" => $request->country_id),
                    "tm_ldpt" => array("data" => $tst2, "action" => $request->country_id),
                    "tm_lsct" => array("data" => $tst3, "action" => $request->country_id),
                    "tm_ltyp" => array("data" => $tst4, "action" => $request->country_id),
                ), 200);
        }


    }

    public function barCodeDetails(Request $request)
    {
        $country_id = $request->country_id;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("SELECT
  concat(t2.locm_name, '-', t3.lcmp_name, '-', t4.ldpt_name, '-', t5.lsct_name) AS location_master,
  t6.ltyp_name                                                                  AS type_name,
  t1.locd_name,
  t1.locd_code,
  t1.geo_adrs                                                                   AS location,
  t7.aemp_name                                                                  AS created_by,
  t8.aemp_name                                                                  AS updated_by,
  t1.created_at,
  t1.updated_at
FROM tm_locd AS t1
  INNER JOIN tm_locm AS t2 ON t1.locm_id = t2.id
  INNER JOIN tm_lcmp AS t3 ON t1.lcmp_id = t3.id
  INNER JOIN tm_ldpt AS t4 ON t1.ldpt_id = t4.id
  INNER JOIN tm_lsct AS t5 ON t1.lsct_id = t5.id
  INNER JOIN tm_ltyp AS t6 ON t1.ltyp_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_iusr = t7.id
  INNER JOIN tm_aemp AS t8 ON t1.aemp_eusr = t8.id
WHERE t1.locd_code ='$request->bar_code'"))->first();
            return response()->json(
                Array(
                    "receive_data" => array("data" => $tst, "action" => $request->input('country_id')),
                ), 200);
        }


    }

    public function commentList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $noteId = $request->note_id;
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.aemp_name, '(', t2.aemp_usnm, ')') AS emp_name,
  t2.id                                        AS emp_id,
  t1.ncom_note                                 AS comment,
  t1.ncom_time                                 AS date_time,
  t1.note_id
FROM tt_ncom AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
  INNER JOIN tt_note AS t3 ON t1.note_id = t3.id
WHERE t1.note_id = $noteId
ORDER BY t1.ncom_time DESC ");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public function noteList(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.id                                          AS note_id,
  t1.site_code                                   AS site_id,
  IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
  DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.note_body                                   AS title,
  ''                                             AS note,
  t1.lfcl_id                                     AS status_id,
  t3.lfcl_name                                   AS status_name,
  t1.ntpe_id                                     AS type_id,
  t4.ntpe_name                                   AS type_name,
  group_concat(t5.nimg_imag)                     AS image_name,
  t1.aemp_id                                     AS emp_id,
  concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name
FROM tt_note AS t1
  INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
  INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
  LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
  INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
  INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id  
  LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
  LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
WHERE t8.aemp_id=$request->emp_id and  t1.note_date BETWEEN '$request->start_date'and '$request->end_date'
GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,
  t1.ntpe_id, t4.ntpe_name,
  t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }
}