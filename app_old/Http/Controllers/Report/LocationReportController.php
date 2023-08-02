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

class LocationReportController extends Controller
{
    private $access_key = 'location_report';
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
            $emp_id=$this->currentUser->employee()->id;
            $country_id=$this->currentUser->employee()->cont_id;
            $userDepartment = DB::select("SELECT
  t1.id,
  t1.name
FROM tbld_department AS t1 INNER JOIN tbld_department_emp_mapping AS t2 ON t1.id = t2.department_id
WHERE t2.emp_id =$emp_id and t1.country_id= $country_id ORDER BY t1.id ASC ");
            $userSaleGroups = DB::select("SELECT
  t1.id,
  t1.name
FROM tbld_sales_group AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
WHERE t2.emp_id =$emp_id and t1.country_id= $country_id ORDER BY t1.id ASC ");
            $saleGroups = SalesGroup::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();

            $department = Department::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $emp = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('report.location.summary')->with("emps", $emp)->with("userDepartments", $userDepartment)->with("departments", $department)->with("userSaleGroups", $userSaleGroups)->with("saleGroups", $saleGroups)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function summaryLocation(Request $request, $id, $date)
    {


        if ($this->userMenu->wsmu_vsbl) {
            $data = DB::select("SELECT
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.times_stamp, '%l.%i%p') AS time,
  t1.lat,
  t1.lon
FROM tblt_location_history AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
WHERE t1.emp_id=$id and t1.date='$date'
ORDER BY t1.date ASC");

            return view('report.location.summary_map')->with("attendances", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function filterLocationSummary(Request $request)
    {

        $country_id=$this->currentUser->employee()->cont_id;
        $where = "1 and t1.country_id=$country_id";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.date between '$request->start_date' and '$request->end_date'";
        }
        if ($request->emp_id != "") {
            $where .= " AND t1.emp_id = $request->emp_id";
        }
        if ($request->department_id != "") {
            $where .= " AND t5.department_id=$request->department_id";
        }
        if ($request->sales_group_id != "") {
            $where .= " AND t6.sales_group_id=$request->sales_group_id";
        }
        $querySQL="SELECT
t3.email as user_name,
  t1.emp_id,
  t2.name,
  t1.date,
  count(t1.id) as count,
  DATE_FORMAT(min(t1.times_stamp), '%d/%m/%Y : %l.%i%p') AS start_time,
  DATE_FORMAT(max(t1.times_stamp), '%d/%m/%Y : %l.%i%p') AS end_time,  
  t4.name as role_name
FROM tblt_location_history AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id  
  INNER JOIN users as t3 ON t2.user_id=t3.id
  INNER JOIN tbld_role as t4 ON t2.role_id=t4.id
 LEFT JOIN tbld_department_emp_mapping as t5 ON t1.emp_id=t5.emp_id
 LEFT JOIN tbld_sales_group_employee as t6 ON t1.emp_id=t6.emp_id
  WHERE $where
GROUP BY t1.emp_id, t1.date, t2.name,t3.email,t4.name
ORDER BY min(t1.times_stamp) ASC";
       // echo $querySQL;
        $data = DB::select($querySQL);
        return $data;

    }

}