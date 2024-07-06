<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v1;

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
use DateTime;
use DatePeriod;
use DateInterval;

class AttNoteData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function saveLeave(Request $request)
    {
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            // $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                /*[id=1, column_id, token, leave_id=2962699, line_data=[{"image_name":"leave_file\/img_1594378969421.jpg","id":"1",
                "leave_id":"2962699"}], emp_id=32783, from_date=2020-07-10, to_date=2020-07-10, day_count=1.0, leave_type=2,
                 is_half_day=0, start_time=17:2:00, end_time=17:2:00, reason=hdhf, address=hx, is_synced=0,
                country_id=1, up_emp_id=32783]
                */
                $from_date=$request->from_date == "" ? date('Y-m-d') : $request->from_date;
                $to_date=$request->to_date == "" ? date('Y-m-d') : $request->to_date;
                $begin = new DateTime($from_date);
                $end = new DateTime($to_date);
                $end->modify('+1 day');
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $emp_id=$request->emp_id;
                foreach ($period as $dt) {
                    $date=$dt->format('Y-m-d');
                    $exist=Attendance::on($db_conn)->where(['aemp_id'=>$emp_id,'attn_date'=>$date])->first();
                    if(!$exist){
                        $attendance = new Attendance();
                        $attendance->setConnection($db_conn);
                        $attendance->slgp_id = 1;
                        $attendance->aemp_id = $request->emp_id;
                        $attendance->site_id = 2;
                        $attendance->site_name = '';
                        $attendance->attn_imge = '';
                        $attendance->geo_lat = 0;
                        $attendance->geo_lon = 0;
                        $attendance->attn_time = date('Y-m-d H:m:s');
                        $attendance->attn_mont = 1;
                        $attendance->atten_type = 'L';
                        $attendance->atten_atyp = 3;
                        $attendance->rout_id = 1;
                        $attendance->attn_rmak = $request->reason == "" ? '' : $request->reason;
                        $attendance->cont_id = $request->country_id;
                        $attendance->lfcl_id = 1;
                        $attendance->aemp_iusr = $request->up_emp_id;
                        $attendance->aemp_eusr = $request->up_emp_id;
                        $attendance->var = 1;
                        $attendance->attr1 = '';
                        $attendance->attr2 = '';
                        $attendance->attr3 = 0;
                        $attendance->attr4 = 0;
                        $attendance->attn_date =$date;
                        $attendance->attn_fdat =$date;
                        $attendance->attn_tdat =$date;
                        $attendance->save();
                    }
                    
                }
                

                DB::commit();
                return response()->json(array('column_id' => $request->id), 200);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
                //throw $e;
            }
        }

    }

    public function saveIOM(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);

                $attendance->slgp_id = 1;;//$request->Group_Id;
                $attendance->aemp_id = $request->emp_id;// $request->SR_ID;
                $attendance->site_id = 1;
                $attendance->site_name = '';
                $attendance->attn_imge = '';
                $attendance->geo_lat = 0;
                $attendance->geo_lon = 0;
                $attendance->attn_time = date('Y-m-d H:m:s');
                $attendance->attn_date = date('Y-m-d', strtotime($request->start_time));
                $attendance->attn_mont = $request->day_count;
                $attendance->atten_type = 'IOM';
                $attendance->atten_atyp = 2;
                $attendance->rout_id = 1;
                $attendance->attn_fdat = date('Y-m-d', strtotime($request->from_date));
                $attendance->attn_tdat = date('Y-m-d', strtotime($request->to_date));
                $attendance->attn_rmak = $request->iom_id;
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::connection($db_conn)->commit();
                return response()->json(array('column_id' => $request->id), 200);

            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        } else {
            return response()->json(array('column_id' => ""), 200);
        }
    }

    public function saveFL(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);

                $attendance->slgp_id = 1;;//$request->Group_Id;
                $attendance->aemp_id = $request->emp_id;// $request->SR_ID; start_time
                $attendance->site_id = 1;
                $attendance->site_name = '';
                $attendance->attn_imge = '';
                $attendance->geo_lat = 0;
                $attendance->geo_lon = 0;
                $attendance->attn_time = date('Y-m-d H:m:s');
                $attendance->attn_date = date('Y-m-d', strtotime($request->from_date));
                $attendance->attn_mont = $request->day_count;
                $attendance->atten_type = 'FL';
                $attendance->atten_atyp = 4;
                $attendance->rout_id = 1;
                $attendance->attn_fdat = date('Y-m-d', strtotime($request->from_date));
                $attendance->attn_tdat = date('Y-m-d', strtotime($request->to_date));
                $attendance->attn_rmak = '';
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::connection($db_conn)->commit();
                return response()->json(array('column_id' => $request->id), 200);

            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        } else {
            return response()->json(array('column_id' => ""), 200);
        }
    }

    public function attendanceSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = 1;;//$request->Group_Id;
                $attendance->aemp_id = $request->emp_id;// $request->SR_ID;
                $attendance->site_id = 1;
               // $attendance->site_name = $request->location;//
                $attendance->site_name = $request->location == "" ? '0' : $request->location;
                $attendance->attn_imge = $request->image;
                $attendance->geo_lat = $request->lat;
                $attendance->geo_lon = $request->lon;
                $attendance->attn_time = $request->date_time;
                $attendance->attn_date = date('Y-m-d', strtotime($request->date_time));
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->attendance_type;
                $attendance->atten_atyp = 1;
                $attendance->rout_id = 1;
                $attendance->attn_fdat = date('Y-m-d', strtotime($request->date_time));
                $attendance->attn_tdat = date('Y-m-d', strtotime($request->date_time));
                $attendance->attn_rmak = '';
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::connection($db_conn)->commit();
                return response()->json(array('column_id' => $request->id), 200);

            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return response()->json(array('column_id' => 0,'message' => $e->errorInfo[2]), 200);
            }
        } else {
            return response()->json(array('column_id' => ""), 200);
        }
    }

    public function employeeAttendance(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            $dataRow = DB::connection($db_conn)->select("SELECT
  t1.attn_date      AS date,
  t1.aemp_id        AS emp_id,
  min(t1.attn_time) AS start_time,
  max(t1.attn_time) AS end_time,
  ''                AS off_day,
  0                 AS prosess_status_id,
  ''                AS processStatus,
  ''                AS leave_reason,
  ''                AS iom_reason,
  ''                AS holiday_reason
FROM tt_attn AS t1
WHERE t1.aemp_id = $request->emp_id AND t1.attn_date = '$request->start_date'
GROUP BY t1.attn_date,t1.aemp_id");
            return response()->json(Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id)), 200);
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
                            $siteVisit->ssvh_ispd = 2;
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


                /* $groupEmployee = DB::connection($db_conn)->table('tl_grem')->whereIn('id', $share_group)->select('aemp_id');
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
                  }*/

                /* foreach ($share_employee as $share_employee1) {
                     $insert[] = [
                         'aemp_id' => $request->share_employee,
                         'note_id' => $note->id,
                         'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                         'enmp_scnt' => 1,
                         'cont_id' => $request->up_emp_id,
                         'lfcl_id' => 1,
                         'aemp_iusr' => $request->up_emp_id,
                         'aemp_eusr' => $request->up_emp_id,
                     ];
                 }*/
                /*if (!empty($insert)) {
                    DB::connection($db_conn)->table('tl_enmp')->insertOrIgnore($insert);
                }*/

                DB::commit();
                //  return $request;
                return response()->json(array('column_id' => $request->id), 200);
                // return response()->json(array('id' => $request->id), 200);
            } catch (\Exception $e) {
                // return response()->json(array('ex' => $e), 200);
                DB::rollback();
                return response()->json(array('column_id' => 0,'message' => $e->errorInfo[2]), 200);;
            }
            return response()->json(array('column_id' => $request->id), 200);
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
                            $siteVisit->ssvh_ispd = 2;
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
                   // DB::connection($db_conn)->table('tl_enmp')->insertOrIgnore($insert);
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

    public function noteType(Request $request)
    {
        $country_id = $request->country_id;
        $country = (new Country())->country($country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(id, ntpe_name, ntpe_code) AS column_id,
  id AS type_id,
  ntpe_name as  name,
  ntpe_code as code
FROM tm_ntpe WHERE lfcl_id=1 and  cont_id=$request->country_id");
            return response()->json(Array("tbld_note_type" => array("data" => $tst, "action" => $request->input('country_id'))), 200);
        }


    }

    public function siteCodeDetails(Request $request)
    {
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

    public function barCodeDetails(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("SELECT
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
WHERE t1.site_code = '$request->bar_code'"))->first();
            return response()->json(
                Array(
                    "receive_data" => array("data" => $tst, "action" => $request->input('country_id')),
                ), 200);
        }


    }


    public function noteDash(Request $request)

    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $date = date('Y-m-d');
            $emp_id = $request->emp_id;
            $dataRow = DB::connection($db_conn)->select("SELECT
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
GROUP BY t1.date");


            $visit = collect(DB::connection($country->cont_conn)->select("SELECT count(t1.id) as count_note
FROM tt_note AS t1
WHERE aemp_id = $emp_id AND t1.note_date = '$date'"))->first();

            $visitMonth = collect(DB::connection($country->cont_conn)->select("SELECT count(t1.id) as count_note
FROM tt_note AS t1
WHERE aemp_id = $emp_id AND t1.note_date BETWEEN DATE_FORMAT('$date', '%Y-%m-01') AND  '$date'"))->first();

            $tst = DB::connection($country->cont_conn)->select("
SELECT 
SUM(emp_id)AS own_task,sum(assign_from)AS assign_from,sum(assign_to)AS assign_to FROM 
  (SELECT
   count(t1.aemp_id)                                 AS emp_id,
   ''                                                AS assign_from,
   ''                                                AS assign_to
  FROM tt_note AS t1
WHERE t1.aemp_id=$emp_id and  t1.note_date = curdate()
GROUP BY t1.aemp_id
UNION ALL
SELECT
   ''                                                AS emp_id,
   count(t2.asin_user)                               AS assign_from,
   ''                                                AS assign_to
  FROM tt_note AS t1
   LEFT JOIN tl_nasn t2 ON(t1.id=t2.note_id) AND t2.enmp_date = curdate()
WHERE t2.asin_user=$emp_id and  t2.enmp_date = curdate()
GROUP BY t2.asin_user
UNION ALL
SELECT
   ''                                                AS emp_id,
  ''                                                 AS assign_from,
   count(t3.aemp_id)                                 AS assign_to
  FROM tt_note AS t1
   LEFT JOIN tl_nasn t3 ON(t1.id=t3.note_id) AND t3.enmp_date = curdate()
WHERE t3.aemp_id=$emp_id and  t3.enmp_date = curdate()
GROUP BY t3.aemp_id
  )tt;");

            return response()->json(
                Array(
                    "receive_data" => array(
                        "data" => $dataRow,
                        "dashboard" => array(
                            "note_today" => $visit->count_note,
                            "note_month" => $visitMonth->count_note,
                            "note_assigned" => $tst,
                        ),
                        "action" => $request->emp_id
                    ),
                ), 200);
        }
    }

    public function notePhoneBook(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $db_conn = $country->cont_conn;
            $emp_id = $request->emp_id;
            $dataRow = DB::connection($db_conn)->select("SELECT
  concat(t1.id, t1.aemp_asgn, t2.aemp_usnm, t2.aemp_name) AS column_id,
  t1.aemp_asgn                                            AS phbk_id,
  concat(t2.aemp_usnm, '-', t2.aemp_name)                 AS phbk_name,
  t2.aemp_usnm                                            AS phbk_code,
  0                                                       AS is_grop
FROM tl_phbk AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_asgn = t2.id
WHERE t1.aemp_id = $emp_id
UNION ALL
SELECT
  concat(t1.id, t1.grop_code, t1.grop_name) AS column_id,
  t2.id                                     AS phbk_id,
  concat(t1.grop_code, '-', t1.grop_name)   AS phbk_name,
  t1.grop_code                              AS phbk_code,
  1                                         AS is_grop
FROM tm_grop AS t1
  INNER JOIN tl_grem AS t2 ON t1.id = t2.grop_id
WHERE t2.aemp_id = $emp_id");

            return response()->json(
                Array(
                    "tm_phbk" => array(
                        "data" => $dataRow,
                        "action" => $request->emp_id
                    ),
                ), 200);
        }

    }

    public function phoneBookRemove(Request $request)
    {
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

    public function phoneBookGroup(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            $phoneBookGroup = new PhoneBookGroup();
            $phoneBookGroup->setConnection($country->cont_conn);
            $phoneBookGroup->grop_name = $request->up_emp_id;
            $phoneBookGroup->grop_code = date('Y-m-d');
            $phoneBookGroup->aemp_iusr = $request->up_emp_id;
            $phoneBookGroup->aemp_eusr = $request->up_emp_id;
            $phoneBookGroup->lfcl_id = 1;
            $phoneBookGroup->cont_id = $request->country_id;
            $phoneBookGroup->save();
            DB::connection($country->cont_conn)->table('tm_grop')->insert($insert);
        }

    }

    public function phoneBookSearch(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        //  $where = "";
        if ($country) {
            if (isset($request->search_text)) {
                $dataRow = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.aemp_usnm, t1.aemp_name) AS column_id,
  t1.id                                     AS phbk_id,
  concat(t1.aemp_usnm, '-', t1.aemp_name)   AS phbk_name,
  t1.aemp_usnm                              AS phbk_code,
  t1.aemp_emal                              AS phbk_email,
  0                                         AS is_grop
FROM tm_aemp AS t1
WHERE t1.lfcl_id = 1 AND (t1.aemp_name LIKE '%$request->search_text%' OR t1.aemp_usnm LIKE '%$request->search_text%' OR t1.aemp_emal LIKE '%$request->search_text%')");
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

}