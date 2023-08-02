<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 3/16/2019
 * Time: 5:37 PM
 */

namespace App\Http\Controllers\API;

use App\BusinessObject\MerchandiseWork;
use App\BusinessObject\MerchandiseWorkImage;
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MdrWork  extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
    public function saveMdrWorkSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $note = new MerchandiseWork();
            $note->emp_id = $request->emp_id;
            $note->site_id = $request->site_id;
            $note->merch_work_type_id = $request->note_type_id;
            $note->date_time = $request->date_time;
            $note->date = date("Y-m-d", strtotime($request->date_time));
            $note->note = $request->note;
            $note->lat = $request->lat;
            $note->lon = $request->lon;
            $note->status_id = 1;
            $note->program_id = 1;
            $note->created_by = $request->emp_id;
            $note->updated_by = $request->emp_id;
            $note->country_id = $request->country_id;
            $note->save();
            $imageLines = json_decode($request->line_data);
            foreach ($imageLines as $imageLine) {
                $noteImage = new MerchandiseWorkImage();
                $noteImage->merch_work_id = $note->id;
                $noteImage->image = $imageLine->image_name;
                $noteImage->country_id = $request->country_id;
                $noteImage->created_by = $request->emp_id;
                $noteImage->updated_by = $request->emp_id;
                $noteImage->save();
            }
            DB::commit();
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function merchWorkType(Request $request)
    {
        $tst = DB::select("SELECT
  concat(id, name, code) AS column_id,
  concat(id, name, code) AS token,
  id AS type_id,
  name,
  code
FROM tbld_merchandise_work_type WHERE  status_id=1 and  country_id=$request->country_id");
        return Array("tbld_merchandise_work_type" => array("data" => $tst, "action" => $request->country_id));
    }
}