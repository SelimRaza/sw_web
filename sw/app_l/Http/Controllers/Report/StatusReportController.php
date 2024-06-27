<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class StatusReportController extends Controller
{
    private $access_key = 'StatusReportController';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function summary()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $emp_id = $this->currentUser->employee()->id;
            $country_id = $this->currentUser->employee()->cont_id;


            return view('report.activity_status.summary')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function activeSummaryDetails(Request $request, $id, $start_date,$end_date)
    {
        $country_id = $this->currentUser->employee()->cont_id;

      if ($this->userMenu->wsmu_vsbl) {
            $data = DB::select("SELECT
  t3.id,
  t3.name,
  '$start_date' as start_date,
  '$end_date' as end_date,
  t6.email as user_name,
  t1.name AS emp_name,
  t4.att_day,
  t5.note_count,
  DATEDIFF('$end_date','$start_date')+1 as date_count,
  t8.holiday,
  t9.name as role_name
FROM tbld_employee AS t1 INNER JOIN tbld_department_emp_mapping AS t2 ON t1.id = t2.emp_id
  INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
  LEFT JOIN (SELECT
               t3.id                   AS department_id,
               t1.emp_id               AS emp_id,
               count(DISTINCT t1.date) AS att_day
             FROM tblt_attendance AS t1
               INNER JOIN tbld_department_emp_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
               INNER JOIN tbld_employee AS t4 ON t1.emp_id = t4.id
               LEFT JOIN tblt_holiday as t5 ON t1.date=t5.from_date and t1.date BETWEEN '$start_date' AND '$end_date' and t1.country_id=t5.country_id
             WHERE t1.date BETWEEN '$start_date' AND '$end_date' AND t1.country_id = $country_id and t5.from_date is NULL  
             GROUP BY t3.id, t1.emp_id
            ) AS t4 ON t3.id = t4.department_id AND t1.id = t4.emp_id
  LEFT JOIN (SELECT
               t3.id                   AS department_id,
               t1.emp_id               AS emp_id,
               count(DISTINCT t1.id) AS note_count
             FROM tblt_note AS t1
               INNER JOIN tbld_department_emp_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
               INNER JOIN tbld_employee AS t4 ON t1.emp_id = t4.id
             WHERE t1.date BETWEEN '$start_date' AND '$end_date' AND t1.country_id = $country_id
             GROUP BY t3.id, t1.emp_id
            ) AS t5 ON t3.id = t5.department_id AND t1.id = t5.emp_id  
  INNER JOIN users as t6 ON t1.user_id=t6.id

   JOIN (SELECT
              count(t1.from_date)                 AS holiday             
             FROM tblt_holiday AS t1               
             WHERE  t1.from_date BETWEEN '$start_date' AND '$end_date' and t1.country_id = $country_id
            ) AS t8 
            INNER JOIN tbld_role as t9 ON t1.role_id=t9.id
WHERE  t3.id=$id and t4.emp_id is NOT NULL
GROUP BY t1.id,t3.id,t3.name,t6.email,t1.name,t4.att_day,t5.note_count,t8.holiday,t9.name");
/*LEFT JOIN (SELECT
               t3.id                   AS group_id,
               t3.name                   AS group_name,
               t1.id               AS emp_id
             FROM tbld_employee AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.emp_id
               INNER JOIN tbld_sales_group AS t3 ON t2.sales_group_id = t3.id
             WHERE  t1.country_id = $country_id
             GROUP BY t1.id,t3.id,t3.name
            ) AS t7 on t1.id = t7.emp_id*/
            return view('report.activity_status.active_summary_details')->with("att_data", $data)->with('permission', $this->userMenu);
       } else {
         return view('theme.access_limit');
       }

    }
    public function inactiveSummaryDetails(Request $request, $id, $start_date,$end_date)
    {
        $country_id = $this->currentUser->employee()->cont_id;

      if ($this->userMenu->wsmu_vsbl) {
            $data = DB::select("SELECT
  t3.id,
  t3.name,
  t5.email as user_name,
  t1.name AS emp_name,
  t6.name as role_name
FROM tbld_employee AS t1 INNER JOIN tbld_department_emp_mapping AS t2 ON t1.id = t2.emp_id
  INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
  LEFT JOIN (SELECT
               t3.id                   AS department_id,
               t1.emp_id               AS emp_id,
               count(DISTINCT t1.date) AS att_day
             FROM tblt_attendance AS t1
               INNER JOIN tbld_department_emp_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
               INNER JOIN tbld_employee AS t4 ON t1.emp_id = t4.id
             WHERE t1.date BETWEEN '$start_date' AND '$end_date' AND t1.country_id = $country_id
             GROUP BY t3.id, t1.emp_id
            ) AS t4 ON t3.id = t4.department_id AND t1.id = t4.emp_id
  INNER JOIN users as t5 ON t1.user_id=t5.id
  INNER JOIN tbld_role as t6 ON t1.role_id=t6.id
WHERE  t3.id =$id and t4.emp_id is NULL
GROUP BY t1.id,t3.id,t3.name,t5.email,t1.name,t6.name");

            return view('report.activity_status.inactive_summary_details')->with("att_data", $data)->with('permission', $this->userMenu);
       } else {
         return view('theme.access_limit');
       }

    }




    public function filterActivityStatusSymmary(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
       // dd($request);
        $where = "1 and t1.country_id=$country_id";

       $query= "SELECT
  t3.id AS     depertment_id,
  '$request->start_date' as start_date,
  '$request->end_date' as end_date,
  t3.name,
  count(t1.id) user_count,
  if(t4.att_user>0,t4.att_user,0) as att_user
FROM tbld_employee AS t1 INNER JOIN tbld_department_emp_mapping AS t2 ON t1.id = t2.emp_id
  INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
  LEFT JOIN (SELECT
               count(DISTINCT t1.emp_id) AS att_user,
               t3.id                     AS department_id
             FROM tblt_attendance AS t1
               INNER JOIN tbld_department_emp_mapping AS t2 ON t1.emp_id = t2.emp_id
               INNER JOIN tbld_department AS t3 ON t2.department_id = t3.id
             WHERE t1.date BETWEEN '$request->start_date' AND '$request->end_date' and t1.country_id=$country_id
             GROUP BY t3.id
            ) AS t4 ON t3.id = t4.department_id
            WHERE $where
GROUP BY t3.id,t3.name,t4.att_user";
        $data = DB::select($query);

        return $data;

    }

}