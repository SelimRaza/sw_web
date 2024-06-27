<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API;

use App\BusinessObject\Attendance;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

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
            //$s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                /*[id=1, column_id, token, leave_id=2962699, line_data=[{"image_name":"leave_file\/img_1594378969421.jpg","id":"1",
                "leave_id":"2962699"}], emp_id=32783, from_date=2020-07-10, to_date=2020-07-10, day_count=1.0, leave_type=2,
                 is_half_day=0, start_time=17:2:00, end_time=17:2:00, reason=hdhf, address=hx, is_synced=0,
                country_id=1, up_emp_id=32783]
                */
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = 1;;//$request->Group_Id;
                $attendance->aemp_id = $request->emp_id;// $request->SR_ID;
                $attendance->site_id = 2;
                $attendance->site_name = '';
                $attendance->attn_imge = '';
                $attendance->geo_lat = 0;
                $attendance->geo_lon = 0;
                $attendance->attn_time = date('Y-m-d H:m:s');
                $attendance->attn_date = $request->from_date;
                $attendance->attn_mont = 1;
                $attendance->atten_type = 'L';
                $attendance->rout_id = 1;
                $attendance->attn_fdat = $request->from_date == "" ? date('Y-m-d') : $request->from_date;
                $attendance->attn_tdat = $request->to_date == "" ? date('Y-m-d') : $request->to_date;
                $attendance->attn_rmak = $request->reason == "" ? '' : $request->leave_id;
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

                DB::commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                //throw $e;
            }
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
                $attendance->site_name = '';
                $attendance->attn_imge = $request->image;
                $attendance->geo_lat = $request->lat;
                $attendance->geo_lon = $request->lon;
                $attendance->attn_time = $request->date_time;
                $attendance->attn_date = date('Y-m-d', strtotime($request->date_time));
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->attendance_type;
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
                return array('column_id' => $request->id);
            } catch
            (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        } else {
            return array('column_id' => "");
        }
    }

    public function employeeAttendanceReport(Request $request)

    {
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
          /*  $dataRow = DB::connection($db_conn)->select("
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
     GROUP BY  t1.aemp_id, t2.atyp_name");*/


            


        //     $dataRow = DB::connection($db_conn)->select("
        //   SELECT
        //   t1.`aemp_id`AS aemp_id,
        //   t2.role_id,
        //   t2.aemp_usnm,
        //   t2.aemp_name,
        //   MAX(t1.`atten_atyp`) AS atten_atyp,
        //   MAX(t1.attn_time)    AS mxattn_time,
        //   Max(t1.`attn_imge`)  AS attn_imge,
        //   MIN(t1.attn_time)    AS minattn_time,
        //   t1.`attn_date`AS attn_date,
        //   '' AS rout_id,
        //   '' AS rout_name,
        //   t3.atyp_name,
        //   t3.atyp_name AS type

        // FROM `tt_attn` t1
        //   JOIN tm_aemp t2 ON (t1.`aemp_id` = t2.id)
        //  JOIN tm_atyp AS t3 ON (t3.id = t1.`atten_atyp`)
        // WHERE (t2.aemp_mngr = $request->emp_id OR t2.id=$request->emp_id) 
        // AND (t1.`attn_date` BETWEEN '$request->start_date'and '$request->end_date')
        // GROUP BY t1.`aemp_id`, t2.aemp_usnm, t2.aemp_name, t1.`attn_date`,t3.atyp_name 
		// ");

        $aempIdList = array();
        $checkIdList = array();
        

        $list = '';


        $sql = DB::connection($db_conn)->select("
            SELECT t1.id, t1.aemp_name, t1.role_id  
            FROM tm_aemp as t1
            WHERE t1.aemp_mngr = $request->emp_id OR t1.id=$request->emp_id
            GROUP BY t1.id;
        ");

        foreach($sql as $x => $val) {
            $aempIdList[$x] = $val->id;

            if($x == 0){
                $list = $val->id;
            }else{
                $list .= ','.$val->id;
            }

            if($val->role_id !=1){

                $sql2 = DB::connection($db_conn)->select("
                    SELECT t1.id, t1.aemp_name, t1.role_id  
                    FROM tm_aemp as t1
                    WHERE t1.aemp_mngr = $val->id OR t1.id=$val->id
                    GROUP BY t1.id;
                ");

                foreach($sql2 as $x => $val2) {
                    $list .= ','.$val2->id;

                    //$checkIdList[$x] = $val2->id;                    
                }


                //$checkIdList[$x] = $val->id;
            }
        }

        $dataRow = DB::connection($db_conn)->select("
            SELECT
            t1.`aemp_id`AS aemp_id,
            t2.role_id,
            t2.aemp_usnm,
            t2.aemp_name,
            MAX(t1.`atten_atyp`) AS atten_atyp,
            MAX(t1.attn_time)    AS mxattn_time,
            Max(t1.`attn_imge`)  AS attn_imge,
            MIN(t1.attn_time)    AS minattn_time,
            t1.`attn_date`AS attn_date,
            '' AS rout_id,
            '' AS rout_name,
            t3.atyp_name,
            t3.atyp_name AS type
            FROM `tt_attn` t1
            JOIN tm_aemp t2 ON (t1.`aemp_id` = t2.id)
            JOIN tm_atyp AS t3 ON (t3.id = t1.`atten_atyp`)
            WHERE (t2.aemp_mngr in($list)) 
            AND (t1.`attn_date` BETWEEN '$request->start_date'and '$request->end_date')
            GROUP BY t1.`aemp_id`, t2.aemp_usnm, t2.aemp_name, t1.`attn_date`,t3.atyp_name 
        ");

        $userRoleList = DB::connection($db_conn)->select("
            SELECT id, role_name, id as role_code FROM `tm_role` WHERE id < $request->role_id
            ORDER BY id DESC;
        ");



        return Array("receive_data" => array("data" => $dataRow,"role_list" => $userRoleList, "action" => $request->country_id));

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
  t9.atyp_name AS type,
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
  LEFT JOIN tm_atyp as t9 ON t2.atten_atyp=t9.id
WHERE t1.date BETWEEN '$request->start_date'and '$request->end_date'

GROUP BY t1.date, t2.aemp_id,t9.atyp_name
ORDER BY t1.date DESC");
            return Array("receive_data" => array("data" => $dataRow, "action" => $request->emp_id));
        }

    }


}