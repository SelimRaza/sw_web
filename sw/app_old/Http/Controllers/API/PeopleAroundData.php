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

class PeopleAroundData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function company(Request $request)
    {
        $tst = DB::select("SELECT `company_id`,`company_name` 
        FROM `tblh_people_around` 
        GROUP BY `company_id`,`company_name`");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('emp_id'))
        );
    }
	public function region(Request $request)
    {
        $tst = DB::select("SELECT `region_id`AS Region_Code,`region_name`AS Region_Name 
		FROM `tblh_people_around` 
		GROUP BY `region_id`,`region_name`");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('emp_id'))
        );
    }
	public function zone(Request $request)
    {
        $tst = DB::select("SELECT `zone_id`AS Zone_ID,`zone_name`AS Zone_Name
        FROM `tblh_people_around`
        WHERE `region_id`LIKE'%$request->Region_ID%'
		GROUP BY `zone_id`,`zone_name`");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('emp_id'))
        );
    }
	public function base(Request $request)
    {
        $tst = DB::select("SELECT `base_id`AS Base_Code,`base_name`AS Base_Name
        FROM `tblh_people_around`
        WHERE `region_id`LIKE'%$request->Region_ID%' 
		AND `zone_id`LIKE'%$request->Zone_ID%'
		AND `company_id`LIKE'%$request->Company_ID%'
		AND `group_id`LIKE'%$request->Group_ID%'
		GROUP BY `base_id`,`base_name`");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('emp_id'))
        );
    }
	public function group(Request $request)
    {
        $tst = DB::select("SELECT `group_id`AS Group_ID,`group_name`AS Group_Name
        FROM `tblh_people_around` 
        WHERE `company_id`LIKE'%$request->Company_ID%'
		GROUP BY `group_id`,`group_name`");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('emp_id'))
        );
    }
	
	public function details_data(Request $request)
    {
        $tst = DB::select("SELECT `name`AS Name,`user_id`AS ID,`mobile_no`AS Mobile,
        zone_name,group_name,base_name		
        FROM `tblh_people_around`
        WHERE `user_type`LIKE'%$request->People_Type%'AND 
       `region_id`LIKE'%$request->Region_ID%'
	    AND `zone_id`LIKE'%$request->Zone_ID%'
        AND `company_id`LIKE'%$request->Company_ID%'AND 
       `group_id`LIKE'%$request->Group_ID%'AND 
       `base_id`LIKE'%$request->Base_ID%'
	   LIMIT $request->LIMIT_First_Value,$request->LIMIT_Last_Value");
        return Array(
            "receive_data" => array("data" => $tst, "action" => $request->input('SR_ID'))
        );
    }
}
