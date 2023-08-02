<?php

namespace App\Http\Controllers\Attendance;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */


use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\TableRows;
use App\BusinessObject\ItemMaster;

use Excel;
class AttendanceProcessController extends Controller
{
    private $access_key = 'attn_process';
    private $currentUser;
    private $userMenu;
    private $db;
    private $empId;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->empId = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            
            if ($subMenu != null) {
                 $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            
            return view('Attendance.Process.create')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function processAttendance(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $aemp_usnm=$request->aemp_usnm;
        $aemp_id=0;
        if($aemp_usnm !=''){
            $emp=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
            $aemp_id=$emp?$emp->id:0;
        }
        if ($this->userMenu->wsmu_crat && $start_date !='') {
           DB::connection($this->db)->select("CALL attendanceProcess('$start_date','$end_date',$aemp_id)");
           return 1;
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function getHoliday(){
            $mnth=date('m');
            $year=date('Y');
            $subMenu = SubMenu::where(['wsmn_ukey' =>'emp/holiday', 'cont_id' => $this->currentUser->country()->id])->first();           
            if ($subMenu != null) {
                 $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $permission = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
        $data=DB::connection($this->db)->select("SELECT t1.`id`, t1.`h_date`, t1.`h_atyp`, t1.`created_at`, 
                t2.atyp_name
                FROM `tbl_holiday` t1
                INNER JOIN tm_atyp t2 ON t1.h_atyp=t2.atyp_code
                WHERE MONTH(t1.h_date)='$mnth' AND YEAR(t1.h_date)='$year'
                ORDER BY t1.h_date ASC;");
        return view('Attendance.Holiday.create',['data'=>$data,'permission'=>$permission]);
    }
    public function addHoliday(Request $request){
        $mnth=date('m');
        $year=date('Y');
        $subMenu = SubMenu::where(['wsmn_ukey' =>'emp/holiday', 'cont_id' => $this->currentUser->country()->id])->first();           
        if ($subMenu != null) {
                $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
        } else {
            $permission = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
        }
        $date=$request->start_date;
        $h_atyp=$request->h_atyp;
        if ($permission->wsmu_crat) {
            try{
                DB::connection($this->db)->select("INSERT IGNORE INTO `tbl_holiday`( `h_date`, `h_atyp`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr` ) 
                VALUES ('$date','$h_atyp','$this->cont_id',1,'$this->empId','$this->empId')");
            }
            catch(\Exception $e){
                return $e;
            }
               
    
        $data=DB::connection($this->db)->select("SELECT t1.`id`, t1.`h_date`, t1.`h_atyp`, t1.`created_at`, 
                t2.atyp_name
                FROM `tbl_holiday` t1
                INNER JOIN tm_atyp t2 ON t1.h_atyp=t2.atyp_code
                WHERE MONTH(t1.h_date)='$mnth' AND YEAR(t1.h_date)='$year'
                ORDER BY t1.h_date ASC;");
            return $data;
        }
        else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function removeHoliday($id){
        $res=0;
        $subMenu = SubMenu::where(['wsmn_ukey' =>'emp/holiday', 'cont_id' => $this->currentUser->country()->id])->first();           
        if($subMenu != null) {
                $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
        } else {
            $permission = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
        }
        if ($permission->wsmu_crat) {
            $res=1;
            DB::connection($this->db)->select("Delete FROM tbl_holiday WHERE id=$id");
        }
        return $res;
    }

    public function adjustHolidayLeave(){
        $res=0;
        $mnth=date('m');
        $year=date('Y');
        $subMenu = SubMenu::where(['wsmn_ukey' =>'emp/holiday', 'cont_id' => $this->currentUser->country()->id])->first();           
        if($subMenu != null) {
                $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
        } else {
            $permission = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
        }
        if ($permission->wsmu_crat) {
            DB::connection($this->db)->select("UPDATE  tbl_attendance t1
                INNER JOIN tbl_holiday t2 ON t1.attn_date=t2.h_date
                SET t1.atten_atyp=t2.h_atyp,t1.res1=10,t1.aemp_eusr='$this->empId'
                WHERE MONTH(t2.h_date)='$mnth' AND YEAR(t2.h_date)='$year'");
            $res=1;
        }
        return $res;
    }

    public function getAttendanceReportView(){
        $subMenu = SubMenu::where(['wsmn_ukey' =>'attn_report', 'cont_id' => $this->currentUser->country()->id])->first();           
        if($subMenu != null) {
                $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
        } else {
            $permission = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
        }
        if ($permission->wsmu_vsbl) {
            $role=DB::connection($this->db)->select("Select id,role_name from tm_role WHERE lfcl_id=1 Order by id asc");
            return view ('Attendance.Report.index',['permission'=>$permission,'roles'=>$role]);
        }
    }
    public function getAttendanceReportData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $report_type=$request->report_type;
        $role_id=$request->role_id;
        $aemp_usnm=$request->aemp_usnm;
        $aemp_id='';
        $query='';
        if($aemp_usnm !=''){
            $aemp_id=$this->getEmpId($aemp_usnm);
        }
        if($role_id !=''){
            $query.=" AND t1.role_id=$role_id ";
        }
        if($aemp_id !=''){
            $query.=" AND t1.aemp_id=$aemp_id";
        }
        if($report_type==1){
            $report_data_query="SELECT  attn_date Attn_Date,aemp_usnm Staff_Id, aemp_name Emp_Name,aemp_mob1 Emp_Mobile,slgp_name Group_Name,  
                zone_name Zone_name, dirg_name Region_Name,edsg_name Designation, start_time Start_Time, end_time End_Time,
                round(time_to_sec(timediff(end_time, start_time ))/ 3600,1) AS ''Working_Hour'',
                ifnull(t2.atyp_name,''Absent'') Attn_Status
                FROM `tbl_attendance` t1
                LEFT JOIN tm_atyp t2 ON t1.atten_atyp=t2.id
                WHERE t1.attn_date between ''$start_date'' AND ''$end_date'' ".$query."  ORDER BY t1.attn_date,t1.aemp_usnm";
            //return $report_data_query;
            $emp_usnm = $this->currentUser->employee()->aemp_usnm;
            $emp_name = $this->currentUser->employee()->aemp_name;
            $aemp_emal = $this->currentUser->employee()->aemp_emal;
            $cont_conn = Auth::user()->countryDB()->db_name;
            $reportType='Attendance_Details';
            DB::connection($this->db)->select("INSERT INTO tbl_report_request( report_name,report_heading_query,report_data_query,cont_conn,aemp_id,aemp_usnm, aemp_name,aemp_email,report_status)
             VALUES('$reportType','1','$report_data_query','$cont_conn','$this->empId','$emp_usnm','$emp_name','$aemp_emal','1')");
             return 1;
        }else{
            $data=DB::connection($this->db)->select("SELECT 
                    t1.aemp_id,max(aemp_usnm)aemp_usnm, max(aemp_name)aemp_name,max(aemp_mob1)aemp_mob1,
                    max(slgp_name)slgp_name,  
                    max(zone_name)zone_name,max(dirg_name)dirg_name,max(edsg_name)edsg_name,DATEDIFF('$end_date','$start_date')+1 as total_days,
                    SUM(CASE WHEN t1.atten_atyp=1 THEN 1 ELSE 0 END)`Attendance`,
                    SUM(CASE WHEN t1.atten_atyp=2 THEN 1 ELSE 0 END)'IOM',
                    SUM(CASE WHEN t1.atten_atyp=3 THEN 1 ELSE 0 END)'Leave',
                    SUM(CASE WHEN t1.atten_atyp=4 THEN 1 ELSE 0 END)'Force_Leave',
                    SUM(CASE WHEN t1.atten_atyp=5 THEN 1 ELSE 0 END)'Absent',
                    SUM(CASE WHEN t1.atten_atyp=6 THEN 1 ELSE 0 END)'Gvt_Holiday',
                    SUM(CASE WHEN t1.atten_atyp=7 THEN 1 ELSE 0 END)'Off_Day'
                    FROM `tbl_attendance` t1
                    INNER JOIN tm_atyp t2 ON t1.atten_atyp=t2.id
                    WHERE t1.attn_date between '$start_date' AND '$end_date' ". $query. "
                    GROUP BY t1.aemp_id
                   ");
        }
        
        return $data;
    }

    public function getEmpAttnDetailsRequest(){
        $aemp_id=$this->empId;
        $data=DB::connection($this->db)->select("Select report_name,report_link,report_status,aemp_email,created_at FROM tbl_report_request 
        WHERE aemp_id=$aemp_id and report_heading_query='1' AND DATE(created_at)>=curdate()-Interval 2 Day AND report_status<=2 ORDER BY created_at desc");
        return $data;
    }
    public function getEmpId($aemp_usnm){
        $data=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
        return $data?$data->id:0;
    }
}