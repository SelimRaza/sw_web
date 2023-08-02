<?php
/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v3;

use App\BusinessObject\Attendance;
use App\BusinessObject\Note;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\PhoneBookGroup;
use App\BusinessObject\SiteVisited;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

class AttNoteData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    /*
    *   API MODEL V3 [START]
    */

    public function noteDash(Request $request){
        
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $date = date('Y-m-d');
            $emp_id = $request->emp_id;
            $dataRow = DB::connection($db_conn)->select("
                SELECT
                t1.date,
                count(id)        AS note_count,
                left(dayname(t1.date), 3) day_name,
                day(t1.date) AS day_sl
                FROM
                (SELECT adddate('2020-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
                FROM
                (SELECT 0 i
                UNION SELECT 1
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9) t0,
                (SELECT 0 i
                UNION SELECT 1
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9) t1,
                (SELECT 0 i
                UNION SELECT 1
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9) t2,
                (SELECT 0 i
                UNION SELECT 1
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9) t3,
                (SELECT 0 i
                UNION SELECT 1
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9) t4) AS t1
                LEFT JOIN tt_note AS t2 ON t1.date = t2.note_date AND t2.aemp_id = $request->emp_id
                WHERE t1.date BETWEEN subdate('$date', 6) AND '$date'
                GROUP BY t1.date
            ");


            $visit = collect(DB::connection($country->cont_conn)->select("SELECT count(t1.id) as count_note
                        FROM tt_note AS t1
                        WHERE aemp_id = $emp_id AND t1.note_date = '$date'"))->first();

            $visitMonth = collect(DB::connection($country->cont_conn)->select("SELECT count(t1.id) as count_note
                            FROM tt_note AS t1
                            WHERE aemp_id = $emp_id AND t1.note_date BETWEEN DATE_FORMAT('$date', '%Y-%m-01') AND  '$date'"))->first();

            return response()->json(
                Array(
                    "receive_data" => array(
                        "data" => $dataRow,
                        "dashboard" => array(
                            "note_today" => $visit->count_note,
                            "note_month" => $visitMonth->count_note,
                            "note_assigned" => 0,
                        ),
                        "action" => $request->emp_id
                    ),
                ), 200);
        }

    }

    public function phoneBookRemove(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $emp_id = $request->emp_id;
            $emp_assign = $request->emp_assign;
            $is_group = $request->is_group;
            if ($is_group == '0') {
                DB::connection($country->cont_conn)->table('tl_phbk')
                    ->where(['aemp_id' => $emp_id, 'aemp_asgn' => $emp_assign,])
                    ->delete();
            } else {
                DB::connection($country->cont_conn)->table('tl_grem')
                    ->where(['grop_id' => $emp_assign, 'aemp_id' => $emp_id,])
                    ->delete();
            }

            return response()->json(
                Array(
                    "tm_phbk" => true,
                ), 200);
        }
    }

    public function phoneBookSearch(Request $request){
        $country = (new Country())->country($request->country_id);
        //  $where = "";
        if ($country) {
            if (isset($request->search_text)) {
                $dataRow = DB::connection($country->cont_conn)->select("
                    SELECT
                    concat(t1.id, t1.aemp_usnm, t1.aemp_name) AS column_id,
                    t1.id                                     AS phbk_id,
                    concat(t1.aemp_usnm, '-', t1.aemp_name)   AS phbk_name,
                    t1.aemp_usnm                              AS phbk_code,
                    t1.aemp_emal                              AS phbk_email,
                    0                                         AS is_grop
                    FROM tm_aemp AS t1
                    WHERE t1.lfcl_id = 1 AND (t1.aemp_name LIKE '%$request->search_text%' OR t1.aemp_usnm LIKE '%$request->search_text%' OR t1.aemp_emal LIKE '%$request->search_text%')
                ");
                return response()->json(
                    Array(
                        "tm_phbk" => array(
                            "data" => $dataRow,
                            "action" => $request->emp_id
                        ),
                    ), 200);
            }
        }

    }

    public function barCodeDetails(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("
                SELECT
                concat(t2.locm_name, '<', t3.lcmp_name, '<', t4.ldpt_name, '<', t5.lsct_name) AS location_master,
                t6.ltyp_name                                                                  AS type_name,
                t1.locd_name                                                                  AS site_name,
                t1.locd_code                                                                  AS site_code,
                t1.geo_adrs                                                                   AS site_adrs,
                ''                                                                            AS site_mob1,
                ''                                                                            AS site_mob2,
                t1.geo_lat                                                                    AS lat,
                t1.geo_lon                                                                    AS lon
                FROM tm_locd AS t1
                INNER JOIN tm_locm AS t2 ON t1.locm_id = t2.id
                INNER JOIN tm_lcmp AS t3 ON t1.lcmp_id = t3.id
                INNER JOIN tm_ldpt AS t4 ON t1.ldpt_id = t4.id
                INNER JOIN tm_lsct AS t5 ON t1.lsct_id = t5.id
                INNER JOIN tm_ltyp AS t6 ON t1.ltyp_id = t6.id
                INNER JOIN tm_aemp AS t7 ON t1.aemp_iusr = t7.id
                INNER JOIN tm_aemp AS t8 ON t1.aemp_eusr = t8.id
                WHERE t1.locd_code = '$request->bar_code'
                UNION ALL
                SELECT
                concat(t1.site_name, '<', t2.mktm_name, '<', t3.ward_name, '<', t5.dsct_name, '<', t6.disn_name) AS location_master,
                concat(t7.scnl_name, '-', t8.otcg_name)                                                          AS type_name,
                t1.site_name                                                                                     AS site_name,
                t1.site_code                                                                                     AS site_code,
                t1.site_adrs                                                                                     AS site_adrs,
                t1.site_mob1                                                                                     AS site_mob1,
                t1.site_mob2                                                                                     AS site_mob2,
                t1.geo_lat                                                                                       AS lat,
                t1.geo_lon                                                                                       AS lon
                FROM tm_site AS t1
                INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
                INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
                INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
                INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
                INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
                INNER JOIN tm_scnl AS t7 ON t1.scnl_id = t7.id
                INNER JOIN tm_otcg AS t8 ON t1.otcg_id = t8.id
                WHERE t1.site_code = '$request->bar_code'"
            ))->first();
            return response()->json(
                Array(
                    "receive_data" => array("data" => $tst, "action" => $request->input('country_id')),
                ), 200);
        }


    }

    public function employeeAttendance(Request $request)

    {
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            $dataRow = DB::connection($db_conn)->select("SELECT
  t1.date,
  t2.aemp_id as emp_id,
  min(t2.attn_time) AS start_time,
  max(t2.attn_time) AS end_time,
  ''                AS off_day,
  0                 AS prosess_status_id,
  ''                AS processStatus,
  ''                AS leave_reason,
  ''                AS iom_reason,
  ''                AS holiday_reason
FROM
  (SELECT adddate('2020-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS date
   FROM
     (SELECT 0 i
      UNION SELECT 1
      UNION SELECT 2
      UNION SELECT 3
      UNION SELECT 4
      UNION SELECT 5
      UNION SELECT 6
      UNION SELECT 7
      UNION SELECT 8
      UNION SELECT 9) t0,
     (SELECT 0 i
      UNION SELECT 1
      UNION SELECT 2
      UNION SELECT 3
      UNION SELECT 4
      UNION SELECT 5
      UNION SELECT 6
      UNION SELECT 7
      UNION SELECT 8
      UNION SELECT 9) t1,
     (SELECT 0 i
      UNION SELECT 1
      UNION SELECT 2
      UNION SELECT 3
      UNION SELECT 4
      UNION SELECT 5
      UNION SELECT 6
      UNION SELECT 7
      UNION SELECT 8
      UNION SELECT 9) t2,
     (SELECT 0 i
      UNION SELECT 1
      UNION SELECT 2
      UNION SELECT 3
      UNION SELECT 4
      UNION SELECT 5
      UNION SELECT 6
      UNION SELECT 7
      UNION SELECT 8
      UNION SELECT 9) t3,
     (SELECT 0 i
      UNION SELECT 1
      UNION SELECT 2
      UNION SELECT 3
      UNION SELECT 4
      UNION SELECT 5
      UNION SELECT 6
      UNION SELECT 7
      UNION SELECT 8
      UNION SELECT 9) t4) AS t1
  LEFT JOIN tt_attn AS t2 ON t1.date = t2.attn_date AND t2.aemp_id = $request->emp_id

WHERE t1.date BETWEEN '$request->start_date'and '$request->end_date'

GROUP BY t1.date, t2.aemp_id
ORDER BY t1.date DESC");
            return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
        }

    }

    public function employeeAttendanceReport(Request $request){
        
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            $dataRow = DB::connection($db_conn)->select("
                SELECT
                t1.aemp_id      AS aemp_id,
                t1.aemp_usnm,
                t1.aemp_name,
                t1.atten_atyp,
                t1.attn_imge,
                t1.mxattn_time  AS mxattn_time,
                t1.minattn_time AS minattn_time,
                t1.`attn_date`  AS attn_date,
                '' AS rout_id,
                '' AS rout_name,
                t2.atyp_name
                FROM (SELECT
                t1.`aemp_id`,
                t2.aemp_usnm,
                t2.aemp_name,
                MAX(t1.`atten_atyp`) AS atten_atyp,
                MAX(t1.attn_time)    AS mxattn_time,
                Max(t1.`attn_imge`)  AS attn_imge,
                MIN(t1.attn_time)    AS minattn_time,
                t1.`attn_date`
                FROM `tt_attn` t1
                JOIN tm_aemp t2 ON (t1.`aemp_id` = t2.id)
                WHERE (t2.aemp_mngr = $request->emp_id OR t2.id=$request->emp_id) AND (t1.`attn_date` BETWEEN '$request->start_date'and '$request->end_date')
                GROUP BY t1.`id`,t2.`id`, t2.aemp_mngr, t1.`aemp_id`, t2.aemp_usnm, t2.aemp_name, t1.`attn_date` ORDER BY t1.`id` DESC) AS t1
                JOIN tm_atyp AS t2 ON (t2.id = t1.`atten_atyp`)
            ");
            return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
        }
    }

    public function siteCodeDetails(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("SELECT
                                concat(t1.site_name, '(', t1.site_code, ')') AS site_name,
                                t2.mktm_name,
                                t3.ward_name,
                                t4.than_name,
                                t5.dsct_name,
                                t6.disn_name,
                                t7.scnl_name,
                                t8.otcg_name,
                                t1.site_adrs,
                                t1.site_mob1,
                                t1.site_mob2,
                                t1.geo_lat,
                                t1.geo_lon,
                                t1.site_ownm,
                                t1.site_imge
                                FROM tm_site AS t1
                                INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
                                INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
                                INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
                                INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
                                INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
                                INNER JOIN tm_scnl AS t7 ON t1.scnl_id = t7.id
                                INNER JOIN tm_otcg AS t8 ON t1.otcg_id = t8.id
                                WHERE t1.site_code = '$request->bar_code'"))->first();
            return response()->json(
                Array(
                    "receive_data" => array("data" => $tst, "action" => $request->input('country_id')),
                ), 200);
        }


    }

    public function saveNote(Request $request)
    {
        $site_code = '';
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            if (isset($request->site_id)) {
                if (strlen($request->site_id) <= 10) {
                    $site_code = $request->site_id;
                }
            }
            DB::beginTransaction();
            try {
                $title = substr($request->title, 0, 255);
                $geo_addr = substr($request->geo_addr, 0, 255);
                $note = new Note();
                $note->setConnection($db_conn);
                $note->aemp_id = $request->up_emp_id;
                $note->note_tokn = $request->note_id;
                $note->site_code = $site_code;
                $note->ntpe_id = $request->note_type_id;
                $note->note_dtim = $request->date_time;
                $note->note_date = date("Y-m-d", strtotime($request->date_time));
                $note->note_titl = "$title";
                $note->note_body = "$request->note";
                $note->geo_lat = $request->lat;
                $note->geo_lon = $request->lon;
                $note->geo_addr = isset($geo_addr) ? "$geo_addr" : '';
                $note->note_type = $request->note_type;
                $share_employee = json_decode($request->share_employee);
                $share_group = json_decode($request->share_department);
                $note->lfcl_id = 1;
                $note->cont_id = $request->country_id;
                $note->aemp_iusr = $request->up_emp_id;
                $note->aemp_eusr = $request->up_emp_id;
                $note->save();
                $noteEmp = new NoteEmployee();
                $noteEmp->setConnection($db_conn);
                $noteEmp->aemp_id = $request->up_emp_id;
                $noteEmp->enmp_date = date('Y-m-d');
                $noteEmp->note_id = $note->id;
                $noteEmp->aemp_iusr = $request->up_emp_id;
                $noteEmp->aemp_eusr = $request->up_emp_id;
                $noteEmp->lfcl_id = 1;
                $noteEmp->cont_id = $request->country_id;
                $noteEmp->enmp_scnt = 0;
                $noteEmp->save();
                if (isset($request->site_id)) {
                    $site = Site::on($db_conn)->where(['site_code' => $request->site_id])->first();
                    if ($site != null) {
                        $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $site->id, 'ssvh_date' => date("Y-m-d", strtotime($request->date_time)), 'aemp_id' => $request->up_emp_id])->first();
                        if ($siteVisit == null) {
                            $siteVisit = new SiteVisited();
                            $siteVisit->setConnection($db_conn);
                            $siteVisit->ssvh_date = date("Y-m-d", strtotime($request->date_time));
                            $siteVisit->aemp_id = $request->up_emp_id;
                            $siteVisit->site_id = $site->id;
                            $siteVisit->ssvh_ispd = 0;
                            $siteVisit->cont_id = $request->country_id;
                            $siteVisit->lfcl_id = 1;
                            $siteVisit->aemp_iusr = $request->up_emp_id;
                            $siteVisit->aemp_eusr = $request->up_emp_id;
                            $siteVisit->save();
                        }
                    }
                }
                $imageLines = json_decode($request->line_data);
                foreach ($imageLines as $imageLine) {
                    $noteImage = new NoteImage();
                    $noteImage->setConnection($db_conn);
                    $noteImage->note_id = $note->id;
                    $noteImage->nimg_imag = $imageLine->image_name;
                    $noteImage->aemp_iusr = $request->up_emp_id;
                    $noteImage->aemp_eusr = $request->up_emp_id;
                    $noteImage->lfcl_id = 1;
                    $noteImage->cont_id = $request->country_id;
                    $noteImage->save();
                }


                $groupEmployee = DB::connection($db_conn)->table('tl_grem')->whereIn('id', $share_group)->select('aemp_id');
                foreach ($groupEmployee as $groupEmployee1) {
                    $insert[] = [
                        'aemp_id' => $groupEmployee1->aemp_id,
                        'note_id' => $note->id,
                        'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                        'enmp_scnt' => 1,
                        'cont_id' => $request->up_emp_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ];
                }

                foreach ($share_employee as $share_employee1) {
                    $insert[] = [
                        'aemp_id' => $share_employee1,
                        'note_id' => $note->id,
                        'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                        'enmp_scnt' => 1,
                        'cont_id' => $request->up_emp_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ];
                }

                if (!empty($insert)) {
                    DB::connection($db_conn)->table('tl_enmp')->insertOrIgnore($insert);
                }
                DB::commit();
              //  return $request;
                return response()->json(array('column_id' => $request->id), 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }

    }

    public function saveNote1(Request $request)
    {
        $site_code = '';
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            if (isset($request->site_id)) {
                if (strlen($request->site_id) <= 10) {
                    $site_code = $request->site_id;
                }
            }
            DB::beginTransaction();
            try {
                $title = substr($request->title, 0, 255);
                $geo_addr = substr($request->geo_addr, 0, 255);
                $note = new Note();
                $note->setConnection($db_conn);
                $note->aemp_id = $request->up_emp_id;
                $note->note_tokn = $request->note_id;
                $note->site_code = $site_code;
                $note->ntpe_id = $request->note_type_id;
                $note->note_dtim = $request->date_time;
                $note->note_rtim = $request->remind == "null" ? date('Y-m-d H:m:s') : $request->remind;
                $note->note_date = date("Y-m-d", strtotime($request->date_time));
                $share_employee = json_decode($request->share_employee);
                $share_group = json_decode($request->share_department);
                $note->note_titl = "$title";
                $note->note_body = "$request->note";
                $note->geo_lat = $request->lat;
                $note->geo_lon = $request->lon;
                $note->geo_addr = isset($geo_addr) ? "$geo_addr" : '';
                $note->note_type = $request->note_type;
                $note->lfcl_id = 1;
                $note->cont_id = $request->country_id;
                $note->aemp_iusr = $request->up_emp_id;
                $note->aemp_eusr = $request->up_emp_id;
                $note->save();
                $noteEmp = new NoteEmployee();
                $noteEmp->setConnection($db_conn);
                $noteEmp->aemp_id = $request->up_emp_id;
                $noteEmp->enmp_date = date('Y-m-d');
                $noteEmp->note_id = $note->id;
                $noteEmp->aemp_iusr = $request->up_emp_id;
                $noteEmp->aemp_eusr = $request->up_emp_id;
                $noteEmp->lfcl_id = 1;
                $noteEmp->cont_id = $request->country_id;
                $noteEmp->enmp_scnt = 0;
                $noteEmp->save();
                if (isset($request->site_id)) {
                    $site = Site::on($db_conn)->where(['site_code' => $request->site_id])->first();
                    if ($site != null) {
                        $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $site->id, 'ssvh_date' => date("Y-m-d", strtotime($request->date_time)), 'aemp_id' => $request->up_emp_id])->first();
                        if ($siteVisit == null) {
                            $siteVisit = new SiteVisited();
                            $siteVisit->setConnection($db_conn);
                            $siteVisit->ssvh_date = date("Y-m-d", strtotime($request->date_time));
                            $siteVisit->aemp_id = $request->up_emp_id;
                            $siteVisit->site_id = $site->id;
                            $siteVisit->ssvh_ispd = 0;
                            $siteVisit->cont_id = $request->country_id;
                            $siteVisit->lfcl_id = 1;
                            $siteVisit->aemp_iusr = $request->up_emp_id;
                            $siteVisit->aemp_eusr = $request->up_emp_id;
                            $siteVisit->save();
                        }
                    }
                }
                $imageLines = json_decode($request->line_data);
                foreach ($imageLines as $imageLine) {
                    $noteImage = new NoteImage();
                    $noteImage->setConnection($db_conn);
                    $noteImage->note_id = $note->id;
                    $noteImage->nimg_imag = $imageLine->image_name;
                    $noteImage->aemp_iusr = $request->up_emp_id;
                    $noteImage->aemp_eusr = $request->up_emp_id;
                    $noteImage->lfcl_id = 1;
                    $noteImage->cont_id = $request->country_id;
                    $noteImage->save();
                }

                /*$groupEmployee = DB::connection($db_conn)->table('tl_grem')->whereIn('id', $share_group)->select('aemp_id')->get();
                foreach ($groupEmployee as $groupEmployee1) {
                    $insert[] = [
                        'aemp_id' => $groupEmployee1->aemp_id,
                        'note_id' => $note->id,
                        'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                        'enmp_scnt' => 1,
                        'cont_id' => $request->country_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ];
                }

                foreach ($share_employee as $share_employee1) {
                    $insert[] = [
                        'aemp_id' => $share_employee1->aemp_id,
                        'note_id' => $note->id,
                        'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                        'enmp_scnt' => 1,
                        'cont_id' => $request->country_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $request->up_emp_id,
                        'aemp_eusr' => $request->up_emp_id,
                    ];
                }*/

                if (!empty($insert)) {
                    DB::connection($db_conn)->table('tl_enmp')->insertOrIgnore($insert);
                }
                DB::commit();
                return response()->json(array('column_id' => $request->id), 200);
                // return $insert;
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

    }


    /*
    *   API MODEL V3 [END]
    */
 

}