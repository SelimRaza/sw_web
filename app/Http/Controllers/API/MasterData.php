<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\Note;
use App\BusinessObject\NoteComment;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\SalesGroupEmployee;
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function upImage(Request $request)
    {
        $dataDB = array();
        $path_name = $request['path_name'];
        $s3 = AWS::createClient('s3');
        if ($request->hasFile('fileUpload')) {
            $fileUpload = $request->file('fileUpload');
            $s3->putObject(array(
                'Bucket' => 'prgfms',
                'Key' => $path_name,
                'SourceFile' => $fileUpload,
                'ACL' => 'public-read',
            ));
        }

        if ($request->hasFile('filUpload')) {
            $image = $request->file('filUpload');
            $folder_name = $request['folder_name'];
            $upload_path = base_path() . '/../uploads/' . $folder_name . '/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            if ($image) {
                $image_name = $image->getClientOriginalName();
                $success = $image->move($upload_path, $image_name);
                if ($success) {
                    $dataDB["image_name"] = $image_name;
                } else {
                    $dataDB["StatusID"] = "0";
                    $dataDB["Error"] = "Cannot upload file.";
                }
            }
        }
        return $dataDB;
    }

    public function departmentList(Request $request)
    {
        $country_id = $request->input('country_id');
        $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS department_id,
  t1.name AS department_name
FROM tbld_department AS t1 WHERE t1.status_id=1 and t1.country_id=$country_id and t1.id not in(38,40,41,42,43,44,45)");
        return Array("tbld_department" => array("data" => $tst, "action" => $request->country_id));
    }

    public function departmentListHigh(Request $request)
    {
        $country_id = $request->input('country_id');
        $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS department_id,
  t1.name AS department_name
FROM tbld_department AS t1 WHERE t1.status_id=1 and t1.id in(38,40,41,42,43,44,45)");
        return Array("tbld_department" => array("data" => $tst, "action" => $request->country_id));
    }

    public function salesGroupList(Request $request)
    {

        $country_id = $request->input('country_id');
        $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS group_id,
  t1.name AS group_name
FROM tbld_sales_group AS t1 WHERE t1.status_id=1 and t1.country_id=$country_id");
        return Array("tbld_sales_group" => array("data" => $tst, "action" => $request->country_id));
    }

    public function employeeGroupList(Request $request)
    {

        $country_id = $request->input('country_id');
        $emp_id = $request->emp_id;
        $tst = DB::select("SELECT
concat(t1.id, t1.name,t1.status_id) AS column_id,
  concat(t1.id, t1.name,t1.status_id) AS token,
  t1.id   AS group_id,
  t1.name AS group_name
FROM tbld_sales_group AS t1
  INNER JOIN tbld_sales_group_employee as t2 ON t1.id=t2.sales_group_id WHERE t1.status_id=1 and t1.country_id=$country_id and t2.emp_id=$emp_id");
        return Array("tbld_sales_group" => array("data" => $tst, "action" => $request->country_id));
    }


}
