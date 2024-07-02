<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/28/2018
 * Time: 9:42 AM
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisplayProgram extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function displayProgram(Request $request)
    {
        $tst = DB::select("SELECT
  t2.emp_id,
  t3.name                                    AS emp_name,
  t3.manager_id,
  t1.id                                      AS program_id,
  t1.name                                    AS program_name,
  t2.qty                                     AS program_qty,
  concat(t1.max_width, ' * ', t1.max_height) AS max_dimension,
  concat(t1.min_width, ' * ', t1.min_height) AS min_dimension,
  t1.start_date,
  t1.end_date,
  0                                          AS is_synced
FROM tbld_display_program AS t1
  INNER JOIN tblt_program_employee_mapping AS t2 ON t1.id = t2.program_id
  INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
WHERE (t3.id = $request->emp_id or t3.manager_id=$request->emp_id ) AND curdate() BETWEEN t1.start_date AND t1.end_date ");
        return Array("tblt_employee_program" => array("data" => $tst, "action" => $request->emp_id));
    }

    public function ipAddress(Request $request)
    {

        return Array("tblt_employee_program" => array("data" => request()->server('SERVER_ADDR'), "action" => $request->emp_id));
    }

}