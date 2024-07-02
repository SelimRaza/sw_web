<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\Note;
use App\BusinessObject\NoteComment;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SiteVisited;
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
use App\MasterData\Country;
use App\MasterData\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function noteComment(Request $request)
    {
        $comment = NoteComment::where(['token' => $request->token])->first();
        if ($comment == null) {
            $comment = new NoteComment();
            $comment->emp_id = $request->emp_id;
            $comment->note_id = $request->note_id;
            $comment->comment = $request->comment;
            $comment->date = date('Y-m-d');
            $comment->date_time = $request->date_time;
            $comment->updated_by = $request->share_by_id;
            $comment->created_by = $request->share_by_id;
            $comment->country_id = $request->country_id;
            $comment->token = $request->token;
            $comment->save();
        }
        return array('column_id' => $comment->id);
    }


    public function saveNote(Request $request)
    {

        $country = Country::findorfail($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::beginTransaction();
            try {
                $title = substr($request->title, 0, 255);
                $note = new Note();
                $note->setConnection($db_conn);
                $note->aemp_id = $request->emp_id;
                $note->note_tokn = $request->note_id;
                $note->site_code = isset($request->site_id) ? "$request->site_id" : '';
                $note->ntpe_id = $request->note_type_id;
                $note->note_dtim = $request->date_time;
                $note->note_date = date("Y-m-d", strtotime($request->date_time));
                $note->note_titl = "$title";
                $note->note_body = "$request->note";
                $note->geo_lat = $request->lat;
                $note->geo_lon = $request->lon;
                $note->lfcl_id = 1;
                $note->cont_id = $request->country_id;
                $note->aemp_iusr = $request->up_emp_id;
                $note->aemp_eusr = $request->up_emp_id;
                $note->save();
                $noteEmp = new NoteEmployee();
                $noteEmp->setConnection($db_conn);
                $noteEmp->aemp_id = $request->emp_id;
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
                        $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $site->id, 'ssvh_date' => date("Y-m-d", strtotime($request->date_time)), 'aemp_id' => $request->emp_id])->first();
                        if ($siteVisit == null) {
                            $siteVisit = new SiteVisited();
                            $siteVisit->setConnection($db_conn);
                            $siteVisit->ssvh_date = date("Y-m-d", strtotime($request->date_time));
                            $siteVisit->aemp_id = $request->emp_id;
                            $siteVisit->site_id = $site->id;
                            $siteVisit->SSVH_ISPD = 0;
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
                DB::commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }

    }

    public function shareNote(Request $request)
    {
        DB::beginTransaction();
        try {
            $departmentEmployees = DepartmentEmployee::where('department_id', '=', $request->department_id)->get();
            foreach ($departmentEmployees as $departmentEmployee) {
                $noteEmp = NoteEmployee::where(['emp_id' => $departmentEmployee->emp_id, 'note_id' => $request->note_id])->first();
                if ($noteEmp == null) {
                    $noteEmp = new NoteEmployee();
                    $noteEmp->emp_id = $departmentEmployee->emp_id;
                    $noteEmp->date = date('Y-m-d');
                    $noteEmp->note_id = $request->note_id;
                    $noteEmp->updated_by = $request->share_by_id;
                    $noteEmp->created_by = $request->share_by_id;
                    $noteEmp->status_id = 1;
                    $noteEmp->country_id = $request->country_id;
                    $noteEmp->share_count = 0;
                    $noteEmp->save();
                } else {
                    $noteEmp->share_count = 1 + $noteEmp->share_count;
                    $noteEmp->save();

                }
            }

            $salesGroupEmployees = SalesGroupEmployee::where('sales_group_id', '=', $request->sales_group_id)->get();
            foreach ($salesGroupEmployees as $salesGroupEmployee) {
                $noteEmp = NoteEmployee::where(['emp_id' => $salesGroupEmployee->emp_id, 'note_id' => $request->note_id])->first();
                if ($noteEmp == null) {
                    $noteEmp = new NoteEmployee();
                    $noteEmp->emp_id = $salesGroupEmployee->emp_id;
                    $noteEmp->date = date('Y-m-d');
                    $noteEmp->note_id = $request->note_id;
                    $noteEmp->updated_by = $request->share_by_id;
                    $noteEmp->created_by = $request->share_by_id;
                    $noteEmp->status_id = 1;
                    $noteEmp->country_id = $request->country_id;
                    $noteEmp->share_count = 0;
                    $noteEmp->save();
                } else {
                    $noteEmp->share_count = 1 + $noteEmp->share_count;
                    $noteEmp->save();

                }
            }
            if ($request->emp_id != 0) {
                $noteEmp = NoteEmployee::where(['emp_id' => $request->emp_id, 'note_id' => $request->note_id])->first();
                if ($noteEmp == null) {
                    $noteEmp = new NoteEmployee();
                    $noteEmp->emp_id = $request->emp_id;
                    $noteEmp->date = date('Y-m-d');
                    $noteEmp->note_id = $request->note_id;
                    $noteEmp->updated_by = $request->share_by_id;
                    $noteEmp->created_by = $request->share_by_id;
                    $noteEmp->status_id = 1;
                    $noteEmp->country_id = $request->country_id;
                    $noteEmp->share_count = 0;
                    $noteEmp->save();
                } else {
                    $noteEmp->share_count = 1 + $noteEmp->share_count;
                    $noteEmp->save();
                }
            }
            DB::commit();
            return array('column_id' => $request->note_id);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    public function note(Request $request)
    {
        $tst = DB::select("SELECT
  t1.id as note_id,
  t1.site_id      AS site_id,
  t2.name         AS site_name,
  DATE_FORMAT(t1.date_time, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.title,
  t1.note,
  t1.status_id,
  t3.name         AS status_name,
  t1.note_type_id AS type_id,
  t4.name         AS type_name,
  group_concat(t5.image_name)        AS image_name,
  t1.emp_id as emp_id,
  concat(t7.email,' - ',t6.name) as emp_name
FROM tblt_note AS t1
  LEFT JOIN tbld_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tbld_lifecycle_status AS t3 ON t1.status_id = t3.id
  INNER JOIN tbld_note_type AS t4 ON t1.note_type_id = t4.id
  left JOIN tblt_note_image_mapping as t5 ON t1.id=t5.note_id
  INNER JOIN tbld_employee as t6 ON t1.emp_id=t6.id 
  INNER JOIN users as t7 ON t6.user_id=t7.id
WHERE t1.emp_id = $request->emp_id and date(t1.date_time)  BETWEEN '$request->start_date 'and '$request->end_date'
 GROUP BY t1.id,t1.site_id,t2.name,t1.date_time,t1.title,t1.note,t1.status_id,t3.name,t1.note_type_id,t4.name,t1.emp_id,t6.name,t7.email
 ORDER BY t1.updated_at DESC");
        return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function noteList(Request $request)
    {
        $where = "1 and ";
        if ($request->site_id != "null") {
            $where .= " t1.site_code='$request->site_id' and ";
        }
        if ($request->emp_id != 0) {
            $where .= " t8.aemp_id=$request->emp_id and ";
        }
        if ($request->site_id == 0 && $request->emp_id == 0) {
            $where .= " t1.site_code='0' and ";
            $where .= " t8.aemp_id=0 and ";
        }
        $country_id = $request->country_id;
        $country = Country::findorfail($country_id);
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
WHERE $where  t8.enmp_date BETWEEN '$request->start_date 'and '$request->end_date'
GROUP BY t1.id, t1.site_code, t2.site_name, t1.note_dtim, t1.note_titl, t1.note_body, t1.lfcl_id, t3.lfcl_name,
  t1.ntpe_id, t4.ntpe_name,
  t1.aemp_id, t6.aemp_usnm, t6.aemp_name
ORDER BY t1.note_dtim DESC");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }

    public function noteCommentList(Request $request)
    {
        $noteId = $request->note_id;
        $tst = DB::select("SELECT
  concat(t2.name,'(',t4.email,')') as emp_name,
  t2.id as emp_id,
  t1.comment,
  t1.date_time,
  t1.note_id
FROM tblt_note_comment AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN tblt_note AS t3 ON t1.note_id = t3.id
  INNER JOIN users AS t4 ON t2.user_id = t4.id
WHERE t1.note_id = $noteId 
ORDER BY t1.date_time DESC ");
        return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function noteType(Request $request)
    {
        $country_id = $request->country_id;
        $country = Country::findorfail($country_id);
        if ($country != null) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
                    concat(id, ntpe_name, ntpe_code) AS column_id,
                    id AS type_id,
                    ntpe_name as  name,
                    ntpe_code as code
                    FROM tm_ntpe WHERE lfcl_id=1 and  cont_id=$request->country_id");

            return Array("tbld_note_type" => array("data" => $tst, "action" => $request->input('country_id')));
        }


    }
    public function getSubNote(Request $request){
        $country = (new Country())->country($request->country_id);
        $sub_notes=[];
        $request_time=date('Y-m-d h:i:s');
        if ($country != false) {
            $db_conn = $country->cont_conn;
            if($request->ntpe_id==-1){
                $sub_notes=DB::connection($db_conn)->select("Select
                            sntp_id,ntpe_id,sntp_name
                            FROM 
                            (SELECT
                            t1.id sntp_id,4 ntpe_id,concat(t1.mstp_name,'(',TIME_FORMAT(t1.start_time, '%h:%i %p'),'-',TIME_FORMAT(t1.start_time, '%h:%i %p'),')') sntp_name
                            FROM tt_mstp t1 WHERE mstp_date>=curdate()
                            ORDER BY mstp_date DESC)t1
                            UNION ALL
                            (Select id sntp_id,ntpe_id,sntp_name from tm_sntp WHERE lfcl_id=1)");
            }
            else if($request->ntpe_id==4){
                $sub_notes=DB::connection($db_conn)->select("SELECT
                            t1.id sntp_id,concat(t1.mstp_name,'(',TIME_FORMAT(t1.start_time, '%h:%i %p'),'-',TIME_FORMAT(t1.start_time, '%h:%i %p'),')') sntp_name
                            FROM tt_mstp t1 WHERE mstp_date>=curdate()
                            ORDER BY mstp_date DESC");
            }else{
                $sub_notes=DB::connection($db_conn)->select("Select id sntp_id,sntp_name from tm_sntp WHERE ntpe_id=$request->ntpe_id");
            }

        }
        return array(
            "sub_notes"=>$sub_notes,
            "request_time"=>$request_time,
            "response_time"=>date('Y-m-d h:i:s')
        );
        
    }
}
