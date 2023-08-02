<?php

namespace App\Http\Controllers\Report\EReport;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 * 
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
use App\MasterData\Country;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\DataExport\RequestReportData;
use App\DataExport\GvtDetailReport;
use Illuminate\Support\Facades\Hash;
//use Maatwebsite\Excel\Facades\Excel;
Use Excel;
class CommonReportController extends Controller
{
    private $access_key = 'e_report';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $single_date;
    public function __construct()
    {
        set_time_limit(8000000);
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

   
    public function index()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId' ORDER BY dirg_code,dirg_name");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");     
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        $asset='';
        if(Auth::user()->country()->id==2 || Auth::user()->country()->id==5){
            $asset=DB::connection($this->db)->select("SELECT id,astm_name FROM tm_astm ORDER BY astm_name asc");
        }
       // return view('report.EReport.common_report',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone,'asset'=>$asset]);
        return view('report.summary_report.common_report',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone,'asset'=>$asset]);
        
        
    }
    public function getTrackingRecord(){
        $period=date('Y-m-d');
        $u=Auth::user()->email;
    	$emp_id=DB::connection($this->db)->select("SELECT id FROM tm_aemp WHERE aemp_usnm ='$u'");
   		$emid=$emp_id[0]->id;
        $this->db = Auth::user()->country()->cont_conn;
        $un_emp=DB::connection($this->db)->select("SELECT
        t1.dhbd_date,
        t4.aemp_usnm ,                                
        t4.aemp_mob1 ,                               
        concat(t2.role_name,'-',t4.aemp_name)		 AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit  OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.dhbd_prdt                                 AS is_productive,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
        t1.cont_id                                   as country_id,
        t1.dhbd_line                                 AS dhbd_line,
        ifnull(round(t1.dhbd_line/ifnull(t1.dhbd_memo,1),2),0.00) AS lpc,
        format(ifnull((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
        round(t1.dhbd_tvit/t1.dhbd_ucnt,0)           AS vpsr,
        round(t1.dhbd_memo/t1.dhbd_ucnt,0)           AS mpsr,
        ifnull(round(if(t1.dhbd_tamt > 0, t1.dhbd_tamt / (t1.dhbd_ucnt*1000), 0),2),0) AS expsr
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date ='$period'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date ='$period'
        UNION ALL
        SELECT
        t1.dhbd_date,
        t4.aemp_usnm, 
        t4.aemp_mob1,
        concat(t2.role_name,'-',t4.aemp_name)		  AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd /26000, 0) AS delivery,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_mtdo > 0, t1.dhbd_mtdo / 1000, 0) AS mtd_total_sales,
        if(t1.dhbd_mtdd > 0, t1.dhbd_mtdd / 1000, 0) AS mtd_total_delivery,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.dhbd_prdt                                 AS is_productive,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        if(t3.dhbd_mtdd > 0, t3.dhbd_mtdd / 1000, 0) AS last_mtd_total_delivery,
        t1.cont_id                                   as country_id,
        t1.dhbd_line                                 AS dhbd_line,
        round(t1.dhbd_line/ifnull(t1.dhbd_memo,1),2) AS lpc,
        format(ifnull((t1.dhbd_memo / t1.dhbd_tvit) * 100, 0), 2) AS strikeRate,
        round(t1.dhbd_tvit/t1.dhbd_ucnt,0)           AS vpsr,
        round(t1.dhbd_memo/t1.dhbd_ucnt,0)           AS mpsr,
        ifnull(round(if(t1.dhbd_tamt > 0, t1.dhbd_tamt / (t1.dhbd_ucnt*1000), 0),2),0) AS expsr
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date='$period'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
        WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date ='$period'");
        return $un_emp;
    }
    public function getUserWiseReport(Request $request){
        $time_period=$request->period;
        $emid=$request->emid;
        //$dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        $this->single_date=$request->start_date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $toDate = Carbon::parse($start_date);
        $fromDate = Carbon::parse($end_date);
        $days = $toDate->diffInDays($fromDate)+1;
        $holiday=round($days/7);
        $days=$days-$holiday;
        $days=$days<=0?1:$days;
        $query="SELECT max(dhbd_date)dhbd_date,oid,aemp_usnm,aemp_mob1,aemp_name,country_id,
        round(sum(p.total_visited),0)total_visited,round(sum(p.t_outlet),0)t_outlet, round(AVG(p.totalSr),0)totalSr,
        sum(p.memo)memo,sum(p.order_amount)order_amount,sum(p.total_target)total_target,
        MAX(visit_color)visit_color,MAX(outlet_color)outlet_color,max(role_id)role_id,
        MAX(role_name)role_name,sum(dhbd_line)dhbd_line,
        ifnull(round(sum(dhbd_line)/ifnull(sum(memo),1),2),0.00) AS lpc,
        format(ifnull((sum(memo) / sum(total_visited)) * 100, 0), 2) AS strikeRate,
        ifnull(round(sum(total_visited)/round(AVG(p.totalSr)*{$days}),0),0)           AS vpsr,
        ifnull(round(sum(memo)/((round(AVG(p.totalSr))*{$days})),0),0)           AS mpsr,
        ifnull(round(sum(p.order_amount)/(round(AVG(p.totalSr),0)*{$days}),2),0)  AS expsr
        
        FROM 
        (SELECT
        t1.dhbd_date,
        t4.aemp_usnm ,                                
        t4.aemp_mob1 ,                               
        concat(t2.role_name,'-',t4.aemp_name)		 AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        t1.cont_id                                   as country_id,
        t1.dhbd_line                                 AS dhbd_line
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date between '$start_date' AND '$end_date'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date between '$start_date' AND '$end_date'
        GROUP BY t1.dhbd_date, t4.aemp_usnm , t4.aemp_mob1,t1.dhbd_tvit,t1.dhbd_tsit,t1.dhbd_ucnt,t1.dhbd_memo,
        t1.dhbd_prdt,t1.role_id ,t2.role_name, t1.aemp_id,t1.cont_id
        UNION ALL
        SELECT
        t1.dhbd_date,
        t4.aemp_usnm, 
        t4.aemp_mob1,
        concat(t2.role_name,'-',t4.aemp_name)		  AS aemp_name,
        t1.dhbd_tvit                                 AS total_visited,
        t1.dhbd_tsit                                 AS t_outlet,
        t1.dhbd_ucnt                                 AS totalSr,
        t1.dhbd_memo                                 AS memo,
        if(t1.dhbd_tamt > 0, t1.dhbd_tamt / 1000, 0) AS order_amount,
        if(t1.dhbd_ttar > 0, t1.dhbd_ttar / 1000, 0) AS total_target,
        if((t1.dhbd_tvit/t1.dhbd_ucnt)<t6.min_visit OR (t1.dhbd_tvit/t1.dhbd_ucnt)>=t6.max_visit,t6.failure_color,t6.success_color) as visit_color,
        if((t1.dhbd_tsit/t1.dhbd_ucnt)<t6.min_outlet OR (t1.dhbd_tsit/t1.dhbd_ucnt)>=t6.max_outlet,t6.failure_color,t6.success_color) as outlet_color,
        t1.role_id                                   AS role_id,
        t2.role_name                                 AS role_name,
        t1.aemp_id                                   AS oid,
        t1.cont_id                                   as country_id,
        t1.dhbd_line                                 AS dhbd_line
        FROM th_dhbd_5 AS t1
        INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
        LEFT JOIN th_dhbd_5 AS t3
        ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date between '$start_date' AND '$end_date'
        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
        LEFT JOIN tbld_emp_tracking_policies t6 ON t4.slgp_id=t6.slgp_id
        INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
        WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date between '$start_date' AND '$end_date'
        GROUP BY t1.dhbd_date, t4.aemp_usnm , t4.aemp_mob1,t1.dhbd_tvit,t1.dhbd_tsit,t1.dhbd_ucnt,t1.dhbd_memo,
        t1.dhbd_prdt,t1.role_id ,t2.role_name, t1.aemp_id,t1.cont_id)p 
        GROUP BY oid,aemp_usnm,aemp_mob1,aemp_name,country_id ORDER BY aemp_usnm,aemp_name ASC";
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $un_emp = DB::connection($this->db)->select(DB::raw($query));
      return array('un_emp'=>$un_emp,'days'=>$days);

    }

    public function commonReportFilter99(Request $request){
        $empId = $this->currentUser->employee()->id;
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $time_period = $request->time_period;
        $acmp_id = $request->acmp_id;
        $dirg_id = $request->dirg_id;
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        $q1 = "";
        $q2 = "";
        $q5 = "";
        $q6 = "";
        $zone_list=[];
        $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        $this->single_date=$request->start_date;
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $zone_q ="";
            if($dirg_id !=""){
                $zone_q=" AND t4.zone_id IN (".implode(',',$zone_list).")";
            }
            if($zone_id !=""){
                $zone_q= " AND t4.zone_id = '$zone_id' ";
            }
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $module=Auth::user()->country()->module_type;
        if($module==2){
            $data=DB::connection($this->db)->select("SELECT t1.ordm_date, t5.slgp_name,
                concat(t7.dlrm_code,'-',t7.dlrm_name)dpot_name,
                concat(t6.aemp_usnm,'-',t6.aemp_name)sv_name,
                concat(t4.aemp_usnm, ' - ', t4.aemp_name) as user_name,
                concat(t3.amim_code, ' - ',t3.amim_name) as item_name, t3.amim_duft,
                (sum(t2.ordd_qnty)%(t3.amim_duft)) 'pics',
                round(sum(t2.ordd_qnty)/(t3.amim_duft)) as order_qnty,
                round(sum(t2.ordd_opds + t2.ordd_spdi + t2.ordd_dfdo),2) as discount,
                round(SUM(t2.ordd_oamt),2) as order_amnt FROM `tt_ordm` t1 
                INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id 
                INNER JOIN tm_amim t3 ON t2.amim_id=t3.id 
                INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id 
                INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                INNER JOIN tm_aemp t6 ON t6.id=t4.aemp_mngr
                INNER JOIN tm_dlrm t7 ON t1.dlrm_id=t7.id
                WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' 
                and t1.slgp_id='$sales_group_id' ".$zone_q."
                GROUP BY t2.amim_id, t1.aemp_id,t1.ordm_date, t5.slgp_name,t7.id,t6.id               
                ");
        }else{
            $data=DB::connection($this->db)->select("SELECT t1.ordm_date, t5.slgp_name,
                concat(t4.aemp_usnm, ' - ', t4.aemp_name) as user_name,
                concat(t3.amim_code, ' - ',t3.amim_name) as item_name, t3.amim_duft,
                round(sum(t2.ordd_qnty)/(t3.amim_duft)) as order_qnty,
                (sum(t2.ordd_qnty)%(t3.amim_duft)) 'pics',
                round(sum(t2.ordd_opds + t2.ordd_spdi + t2.ordd_dfdo),2) as discount,
                round(SUM(t2.ordd_oamt),2) as order_amnt FROM `tt_ordm` t1 
                INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id 
                INNER JOIN tm_amim t3 ON t2.amim_id=t3.id 
                INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id 
                INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' 
                and t1.slgp_id='$sales_group_id' ".$zone_q."
                GROUP BY t2.amim_id, t1.aemp_id,t1.ordm_date              
                ");
        }
        
        return array(
            'rp_data'=>$data,
            'module'=>$module,
        );
    }

    public function commonReportFilterOrderDetails(Request $request){
        $empId = $this->currentUser->employee()->id;

        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $time_period = $request->time_period;
        $acmp_id = $request->acmp_id;
        $dirg_id = $request->dirg_id;
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        $q1 = "";
        $q2 = "";
        $q5 = "";
        $q6 = "";
        $zone_list=[];
        //$dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        $this->single_date=$request->start_date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $zone_q ="";
        if($zone_id !=""){
            $zone_q= " AND t5.zone_id = '$zone_id' ";
        }
        if($dirg_id !="" and $zone_id == ""){
            $zone_q=" AND t5.zone_id IN (".implode(',',$zone_list).")";
        }

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));

        $quer = "SELECT t2.ordm_date, t5.aemp_name, t5.aemp_usnm, t4.site_code, t4.site_name,
                t3.amim_code,t3.amim_name, t1.ordd_qnty, t1.ordd_oamt,t2.ordm_time  FROM `tt_ordd` t1 INNER JOIN tt_ordm t2 ON t1.ordm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id = t3.id
                INNER JOIN tm_site t4 ON t2.site_id=t4.id
                INNER JOIN tm_aemp t5 on t2.aemp_id=t5.id WHERE t2.ordm_date BETWEEN  '$start_date' and '$end_date' AND t2.slgp_id='$sales_group_id'". $zone_q;
        $data=DB::connection($this->db)->select($quer);
        return $data;
    }


//All report section start
    public function commonReportFilter(Request $request)
    {
        $empId = $this->currentUser->employee()->id;
        $reportType = $request->reportType;
        $zone_id = $request->zone_id;
        $sales_group_id = $request->sales_group_id;
        $time_period = $request->time_period;
        $acmp_id = $request->acmp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;
        $dirg_id = $request->dirg_id;
        $slgp=DB::connection($this->db)->select("Select acmp_name,slgp_name from user_group_permission where acmp_id=$acmp_id and slgp_id=$sales_group_id limit 1");
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        $slgp_name=$slgp[0]->slgp_name;
        $acmp_name=$slgp[0]->acmp_name;
        $q1 = "";
        $q2 = "";
        $q5 = "";
        $q6 = "";
        $zone_list=[];
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        if ($zone_id != "") {
            $q1 = " AND t1.zone_id = '$zone_id' ";
        }
        if ($sales_group_id != "") {
            $q2 = " AND t1.slgp_id = '$sales_group_id' ";
        }
        if ($dist_id != "") {
            $q5 = " AND t1.dsct_id = '$dist_id'";
        }
        if ($than_id != "") {
            $q6 = " AND t1.than_id = '$than_id'";
        }
        $this->single_date=$request->start_date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $oid_min_max=DB::connection($this->db)->select("Select min(id) as min_id,max(id) as max_id FROM tt_ordm where ordm_date BETWEEN '$start_date' AND '$end_date'");
        $min_id=$oid_min_max[0]->min_id;
        $max_id=$oid_min_max[0]->max_id;
        $cls_cat_switch=$request->rtype.$request->sr_zone;
        //return $cls_cat_switch;
        switch($reportType){
            case "class_wise_order_report_amt":
                    $rtype=$request->rtype;                   
                    if($request->ord_flag==1){
                        $select_q='';
                        $zone_q='';
                        $condition_q='';
                        $class_id=[];
                        $temp_v_id='';
                        $min_q='';
                        if($dirg_id !=""){
                            $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
                        }
                        if ($zone_id != "") {
                            $zone_q= " AND t6.id = '$zone_id' ";
                        }
                        if($min_id !='' && $max_id !=''){
                            $min_q="t1.id between $min_id and $max_id AND";
                        }
                        $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                                        INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                                        where t1.slgp_id='$sales_group_id' AND t3.lfcl_id=1 AND t2.lfcl_id=1
                                        GROUP BY t3.id,t3.itcl_name");
                        for($i=0;$i<count($class_list);$i++){
                            $temp_v_id=$class_list[$i]->id;
                            $temp_v_cl=$class_list[$i]->itcl_name;
                            $select_q.=",sum(if(t3.id='$temp_v_id',t2.ordd_oamt,0))/1000 `$temp_v_cl`";
                        }
                        switch($cls_cat_switch){
                            case 11:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,
                                            t5.aemp_mob1,t6.zone_code,t6.zone_name
                                            ".$select_q."
                                            FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                            tm_aemp as t5 ,tm_zone as t6
                                            where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                        and t1.id=t2.ordm_id 
                                            ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                            and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                            group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                            t6.zone_name ORDER BY t1.ordm_date ASC");
                                break;
                            case 12:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t1.ordm_date,t6.zone_code,
                                                        t6.zone_name ORDER BY t1.ordm_date,t6.zone_code ASC");
                                break;
                            case 21:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t5.aemp_usnm,t5.aemp_name,
                                                        t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                                        t6.zone_name ORDER BY t5.aemp_usnm,t5.aemp_name ASC");
                                break;
                            case 22:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t6.zone_code,
                                                        t6.zone_name ORDER BY t6.zone_code ASC");
                                break;
                            default:
                                $class_wise_ord_amnt="";
                                break;
                        }
                        
                        
                        return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt,'guten'=>$cls_cat_switch);
                    }
                    else{
                        $select_q='';
                        $zone_q='';
                        $condition_q='';
                        $class_id=[];
                        $temp_v_id='';
                        $min_q='';
                        if($dirg_id !=""){
                            $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
                        }
                        if ($zone_id != "") {
                            $zone_q= " AND t6.id = '$zone_id' ";
                        }
                        if($min_id !='' && $max_id !=''){
                            $min_q="t1.id between $min_id and $max_id AND";
                        }
                        $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                                        INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                                        where t1.slgp_id='$sales_group_id' AND t3.lfcl_id=1 AND t2.lfcl_id=1
                                        GROUP BY t3.id,t3.itcl_name");
                        for($i=0;$i<count($class_list);$i++){
                            $temp_v_id=$class_list[$i]->id;
                            $temp_v_cl=$class_list[$i]->itcl_name;
                            $select_q.=",sum(if(t3.id='$temp_v_id',1,0))/1000 `$temp_v_cl`";
                        }
                        switch($cls_cat_switch){
                            case 11:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,
                                            t5.aemp_mob1,t6.zone_code,t6.zone_name
                                            ".$select_q."
                                            FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                            tm_aemp as t5 ,tm_zone as t6
                                            where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                        and t1.id=t2.ordm_id 
                                            ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                            and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                            group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                            t6.zone_name ORDER BY t1.ordm_date ASC");
                                break;
                            case 12:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t1.ordm_date,t6.zone_code,
                                                        t6.zone_name ORDER BY t1.ordm_date,t6.zone_code ASC");
                                break;
                            case 21:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t5.aemp_usnm,t5.aemp_name,
                                                        t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,
                                                        t6.zone_name ORDER BY t5.aemp_usnm,t5.aemp_name ASC");
                                break;
                            case 22:
                                $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t6.zone_code,t6.zone_name
                                                        ".$select_q."
                                                        FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                                                        tm_aemp as t5 ,tm_zone as t6
                                                        where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                                    and t1.id=t2.ordm_id 
                                                        ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                                                        and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                        group by t6.zone_code,
                                                        t6.zone_name ORDER BY t6.zone_code ASC");
                                break;
                            default:
                                $class_wise_ord_amnt="";
                                break;
                        }
                        return array('class_list'=>$class_list,'class_wise_ord_memo'=>$class_wise_ord_amnt);
                    }
                break;
            case "cat_wise_order_report_amt":
                $select_q='';
                $zone_q='';
                $condition_q='';
                $class_id=[];
                $temp_v_id='';
                $min_q='';
                $rtype=$request->rtype;
                if($dirg_id !=""){
                    $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t6.id = '$zone_id' ";
                }
                if($min_id !='' && $max_id !=''){
                    $min_q="t1.id between $min_id and $max_id AND";
                }
                $class_list=DB::connection($this->db)->select("SELECT t4.id,t4.itcg_name FROM `tl_sgit` t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
                            INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
                            where t1.slgp_id=$sales_group_id AND t3.lfcl_id=1 AND t2.lfcl_id=1  AND t4.lfcl_id=1
                            GROUP BY t4.id,t4.itcg_name");
                if($request->ord_flag==1){
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcg_name;
                        $select_q.=",sum(if(t3.itcg_id='$temp_v_id',t2.ordd_oamt,0))/1000 `$temp_v_cl`";
                    }
                }else{
                    for($i=0;$i<count($class_list);$i++){
                        $temp_v_id=$class_list[$i]->id;
                        $temp_v_cl=$class_list[$i]->itcg_name;
                        $select_q.=",sum(if(t3.itcg_id='$temp_v_id',1,0))`$temp_v_cl`";
                    }
                }
                switch($cls_cat_switch){
                    case 11:
                        $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,
                                                t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ORDER BY t1.ordm_date ASC");
                        break;
                    case 12:
                        $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t6.zone_code,t6.zone_name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t1.ordm_date,t6.zone_code,t6.zone_name
                                                ORDER BY t1.ordm_date,t6.zone_code ASC");
                        break;
                    case 21:
                        $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t5.aemp_usnm,t5.aemp_name,
                                                t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                                                ORDER BY t5.aemp_usnm,t5.aemp_name ASC");
                        break;
                    case 22:
                        $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t6.zone_code,t6.zone_name
                                                ".$select_q."
                                                FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                                                tm_aemp as t5 ,tm_zone as t6
                                                where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                                            and t1.id=t2.ordm_id 
                                                ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                                                and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                                                group by t6.zone_code,t6.zone_name
                                                ORDER BY t6.zone_code ASC");
                        break;
                    default:
                        $class_wise_ord_amnt="";
                        break;
                }
                // if($rtype==1){
                //     $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,
                //                         t5.aemp_mob1,t6.zone_code,t6.zone_name
                //                         ".$select_q."
                //                         FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                //                         tm_aemp as t5 ,tm_zone as t6
                //                         where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                //                                     and t1.id=t2.ordm_id 
                //                         ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                //                         and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                //                         group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                //                         ORDER BY t1.ordm_date ASC");
                // }
                // else{
                //     $class_wise_ord_amnt=DB::connection($this->db)->select("SELECT t5.aemp_usnm,t5.aemp_name,
                //                         t5.aemp_mob1,t6.zone_code,t6.zone_name
                //                         ".$select_q."
                //                         FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itsg` as t3,tm_amim as t4,
                //                         tm_aemp as t5 ,tm_zone as t6
                //                         where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                //                                     and t1.id=t2.ordm_id 
                //                         ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itsg_id = t3.id
                //                         and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                //                         group by t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                //                         ORDER BY t6.zone_code ASC");
                // }
                
                return array('class_list'=>$class_list,'class_wise_ord_amnt'=>$class_wise_ord_amnt);
                break;
            case "sr_route_outlet":
                $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name,COUNT(t3.site_id) as t_site
                            FROM tm_aemp t1
                            INNER JOIN tl_rpln t2 on t1.id=t2.aemp_id
                            INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                            INNER JOIN tm_zone t4 ON t1.zone_id=t4.id
                            INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                            WHERE t1.role_id=1 AND t2.rpln_day=DAYNAME('$start_date')
                            ".$q2.$q1."
                            AND t1.lfcl_id=1 AND t2.lfcl_id=1 AND t3.lfcl_id=1
                            GROUP BY t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t4.zone_name");
                return $data;
                break;
            case "asset_order":
                $astm_id=$request->astm_id;
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t2.zone_id = '$zone_id' ";
                }
                $amim_list=DB::connection($this->db)->select("SELECT 
                            t2.id,t2.amim_name
                            FROM tl_astd t1
                            INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                            WHERE t1.astm_id=$astm_id AND t1.slgp_id=$sales_group_id GROUP BY t2.id,t2.amim_name ORDER BY t2.amim_name ASC");
                $amim_query='';
                for($i=0;$i<count($amim_list);$i++){
                    $amim_name=$amim_list[$i]->amim_name;
                    $id=$amim_list[$i]->id;
                    $amim_query.=",SUM(CASE WHEN t9.amim_id='$id' THEN t9.ordd_oamt ELSE 0 END)`$amim_name`";
                }

                $data=DB::connection($this->db)->select("SELECT
                        t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,replace(concat(t7.site_code,'-',t7.site_name),',','-')site_name,sum(t9.ordd_oamt)ast_itm_ordr,sum(t1.ordm_amnt) site_order_amount  ".$amim_query."
                        FROM tt_ordm t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                        INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                        INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
                        INNER JOIN tm_astm t6 ON t5.astm_id=t6.id
                        INNER JOIN tm_site t7 ON t1.site_id=t7.id
                        INNER JOIN tl_astd t8 ON t6.id=t8.astm_id
                        INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
                        WHERE t1.ordm_date between '$start_date' AND '$end_date' AND t6.id='$astm_id' AND t1.slgp_id=$sales_group_id ".$zone_q."
                        GROUP BY  t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,t7.site_code,t7.site_name
                        ORDER BY t2.aemp_name,t4.zone_code;");
                return array('t_data'=>$data,'amim_list'=>$amim_list);
                break;
            case "asset_summary":
                $astm_id=$request->astm_id;
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t1.zone_id = '$zone_id' ";
                }
                if($time_period==0){
                    $qr='';
                    if($dirg_id !=""){
                        $qr=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }
                    if ($zone_id != "") {
                        $qr= " AND t2.zone_id = '$zone_id' ";
                    }
                    $data=DB::connection($this->db)->select("    SELECT t1.slgp_id,t1.slgp_name,t1.zone_id,t1.zone_name,t1.ast_olt,count(t2.ordm_ornm)t_memo,count(distinct t2.site_id)ast_ord_olt,
                    ifnull(round(sum(site_ordr)/1000,2),0.00)site_ordr,ifnull(round(sum(ast_itm_ordr)/1000,2),0.00)ast_itm_ordr
                    FROM 
                    (SELECT 
                    t3.id slgp_id,t3.slgp_name,t2.id zone_id,t2.zone_name,count(distinct t1.site_id)ast_olt FROM 
                    tl_assm t1
                    INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                    INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                    WHERE t1.slgp_id='$sales_group_id' AND t1.astm_id='$astm_id' ".$zone_q."
                    GROUP BY t2.zone_name ORDER BY t2.zone_code asc)t1
                    LEFT JOIN 
                    (SELECT 
                    t4.id zone_id,t1.ordm_ornm,t1.site_id,sum(t1.ordm_amnt)site_ordr,t5.astm_id,sum(t9.ordd_oamt)ast_itm_ordr
                    FROM tt_ordm t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                    INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
                    INNER JOIN tm_site t7 ON t1.site_id=t7.id
                    INNER JOIN tl_astd t8 ON t5.astm_id=t8.astm_id
                    INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
                    WHERE t1.slgp_id='$sales_group_id' AND t1.ordm_date=curdate() AND t5.astm_id='$astm_id' ".$qr."
                    GROUP BY t4.id ,t1.ordm_ornm,t1.site_id,t1.ordm_amnt)t2 ON t2.zone_id=t1.zone_id
                    GROUP BY t1.zone_id,t1.zone_name,t1.ast_olt;");
                }else{
                    $data=DB::connection($this->db)->select("SELECT t1.slgp_id,t1.slgp_name,t1.zone_id,t1.zone_name,t1.ast_olt,count(t2.ordm_ornm)t_memo,count(distinct t2.site_id)ast_ord_olt,
                        ifnull(round(sum(site_ordr)/1000,2),0.00)site_ordr,ifnull(round(sum(ast_itm_ordr)/1000,2),0.00)ast_itm_ordr
                        FROM 
                        (SELECT 
                        t3.id slgp_id,t3.slgp_name,t2.id zone_id,t2.zone_name,count(distinct t1.site_id)ast_olt FROM 
                        tl_assm t1
                        INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                        INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                        WHERE t1.slgp_id='$sales_group_id' AND t1.astm_id='$astm_id' ".$zone_q."
                        GROUP BY t2.zone_name ORDER BY t2.zone_code asc)t1
                        LEFT JOIN 
                        (SELECT t1.ordm_ornm,t1.site_id,t1.site_ordr,t1.ast_itm_ordr,t1.zone_id,t1.astm_id FROM  tbl_asset_summary t1 WHERE t1.slgp_id='$sales_group_id' AND t1.astm_id='$astm_id' 
                        AND ordm_date between '$start_date' AND '$end_date' ".$zone_q.") t2 ON t2.zone_id=t1.zone_id
                        GROUP BY t1.zone_id,t1.zone_name,t1.ast_olt;");
                }
                $result=collect($data);
                $grand_total=$result->pipe(function($result){
                    return collect([
                        'ast_olt'=>$result->sum('ast_olt'),
                        't_memo'=>$result->sum('t_memo'),
                        'ast_ord_olt'=>$result->sum('ast_ord_olt'),
                        'site_ordr'=>round($result->sum('site_ordr'),2),
                        'ast_itm_ordr'=>round($result->sum('ast_itm_ordr'),2),
                    ]);                       
                });
                return array('data'=>$data,'start_date'=>$start_date,'end_date'=>$end_date,'gt'=>$grand_total);
            break;
            case "asset_details":
                $astm_id=$request->astm_id;
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t2.zone_id = '$zone_id' ";
                }
                if($time_period==0){
                    $data=DB::connection($this->db)->select("SELECT
                            t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,replace(concat(t7.site_code,'-',t7.site_name),',','-')site_name,replace(concat(t10.amim_code,'-',t10.amim_name),',','-') item_name,
                            round(sum(t9.ordd_oamt)/1000,2)ast_itm_ordr
                            FROM tt_ordm t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                            INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                            INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                            INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
                            INNER JOIN tm_astm t6 ON t5.astm_id=t6.id
                            INNER JOIN tm_site t7 ON t1.site_id=t7.id
                            INNER JOIN tl_astd t8 ON t6.id=t8.astm_id
                            INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
                            INNER JOIN tm_amim t10 ON t8.amim_id=t10.id
                            WHERE t1.ordm_date=curdate() AND t6.id='$astm_id' AND t1.slgp_id=$sales_group_id ".$zone_q."
                            GROUP BY  t3.slgp_name,t4.zone_name,t2.aemp_usnm,t2.aemp_name,t7.site_code,t7.site_name,t10.amim_code,t10.amim_name
                            ORDER BY t2.aemp_name,t4.zone_code;");
                }
                else{
                    $data=DB::connection($this->db)->select("SELECT
                        t1.slgp_name,t1.zone_name,t2.aemp_usnm,t2.aemp_name,replace(concat(t5.site_code,'-',t5.site_name),',','-')site_name,
                        concat(t6.amim_code,'-',t6.amim_name)item_name,round(round(sum(t3.ordd_oamt)/1000,2),2)ast_itm_ordr
                        FROM 
                        tbl_asset_summary t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_usnm=t2.aemp_usnm
                        INNER JOIN tt_ordd t3 ON t1.ordm_ornm=t3.ordm_ornm
                        INNER JOIN tm_site t5 ON t1.site_id=t5.id
                        INNER JOIN tm_amim t6 ON t3.amim_id=t6.id
                        WHERE t1.slgp_id=$sales_group_id AND t1.astm_id=$astm_id AND t1.ordm_date between '$start_date' AND '$end_date' ".$zone_q."
                        GROUP  BY t1.slgp_name,t1.zone_name,t2.aemp_usnm,t2.aemp_name,t5.site_code,t5.site_name,t6.amim_code,t6.amim_name Order By t1.zone_name,t2.aemp_name ASC");
                }
                
                return $data;
                break;
            case "group_wise_route_outlet":
                    $data=DB::connection($this->db)->select("select slgp_id, count(rout_id)t_route,sum(SR)t_sr,
                                SUM(CASE WHEN  site<60 THEN 1 ELSE 0 END)`b_60` ,
                                SUM(CASE WHEN site>=60 AND site<=120 THEN 1 ELSE 0 END) `b_60_120`,
                                SUM(CASE WHEN  site>120 THEN 1 ELSE 0 END)`b_120` 
                                FROM(
                                SELECT t1.slgp_id,t2.rout_id , COUNT(t3.site_id)site,count(DISTINCT(t1.id))SR
                                FROM `tm_aemp` t1
                                LEFT JOIN  tl_rpln t2 ON t1.id=t2.aemp_id
                                INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                                WHERE  t2.rpln_day=DAYNAME('$start_date')".$q2.$q1." AND  t1.lfcl_id=1 AND t3.lfcl_id=1
                                GROUP BY t1.slgp_id,t2.rout_id)pp GROUP BY slgp_id");
                    return $data;
                break;
            case "class_wise_order_report_memo":
                $select_q='';
                $zone_q='';
                $condition_q='';
                $class_id=[];
                $temp_v_id='';
                $min_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t6.id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t6.id = '$zone_id' ";
                }
                if($min_id !='' && $max_id !=''){
                    $min_q="t1.id between $min_id and $max_id AND";
                }
                $class_list=DB::connection($this->db)->select("SELECT t3.id,t3.itcl_name FROM `tl_sgit` t1
                                INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                                INNER JOIN tm_itcl t3 ON t2.itcl_id=t3.id
                                where t1.slgp_id='$sales_group_id' AND t3.lfcl_id=1 AND t2.lfcl_id=1
                                GROUP BY t3.id,t3.itcl_name");
                for($i=0;$i<count($class_list);$i++){
                    $temp_v_id=$class_list[$i]->id;
                    $temp_v_cl=$class_list[$i]->itcl_name;
                    $select_q.=",sum(if(t3.id='$temp_v_id',1,0))`$temp_v_cl`";
                }
                $class_wise_ord_memo=DB::connection($this->db)->select("SELECT t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name
                            ".$select_q."
                            FROM `tt_ordm` as t1,`tt_ordd` as t2,`tm_itcl` as t3,tm_amim as t4,
                            tm_aemp as t5 ,tm_zone as t6
                            where ".$min_q." t1.ordm_date between  '$start_date'  and '$end_date'
                                        and t1.id=t2.ordm_id 
                            ".$q2.$zone_q." and t2.amim_id=t4.id and t4.itcl_id = t3.id
                            and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                            group by t1.ordm_date,t5.aemp_usnm,t5.aemp_name,t5.aemp_mob1,t6.zone_code,t6.zone_name ORDER BY t1.ordm_date asc");
                return array('class_list'=>$class_list,'class_wise_ord_memo'=>$class_wise_ord_memo);
                break;
            case "sr_activity_hourly_order":
                        if($time_period==0){
                            $q_z="";
                            $q2_g="";
                            $q3_r="";
                            $q9_s="";
                            if ($zone_id != "") {

                                $q_z = " AND t2.zone_id =$zone_id";
                            }
                            if ($sales_group_id != "") {

                                $q2_g = " AND t1.slgp_id = '$sales_group_id'";
                            }
                            
                            // DB::connection($this->db)->select(DB::raw("SET sql_require_primary_key=0"));
                            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                            $sql = "CREATE TEMPORARY TABLE temp1(id int auto_increment primary key)   SELECT
                            t4.acmp_name, t4.slgp_name, t3.dirg_name,concat(t3.zone_code,'-',t3.zone_name)zone_name, t1.ordm_date,t1.aemp_id,replace(t2.aemp_name,',','.')aemp_name, t2.aemp_usnm, t2.aemp_mob1,
                            HOUR(ordm_time)'hr', COUNT(ordm_ornm) AS order_number,t5.rout_name AS rout_name
                            FROM `tt_ordm` t1
                            INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                            INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                            INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                            INNER JOIN tm_rout t5 ON t1.rout_id=t5.id
                            WHERE t1.ordm_date = '$start_date' AND
                                t3.aemp_id = '$empId' and t4.aemp_id='$empId' ".$q_z . $q2_g."
                            GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";
                            $srData = DB::connection($this->db)->select(DB::raw($sql));
                            $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
                            acmp_name, zone_name, dirg_name,rout_name from temp1 GROUP BY aemp_id"));

                                $row_no = 1;
                                $icol = 8;
                            $final[0][0] = "Order Date";
                            $final[0][1] = "Group";
                            $final[0][2] = "Zone";
                            $final[0][3] = "SR ID";
                            $final[0][4] = "SR Name";
                            $final[0][5] = "SR Mobile";
                            $final[0][6] = "09AM";
                            $final[0][7] = "10AM";
                            $final[0][8] = "11AM";
                            $final[0][9] = "12PM";
                            $final[0][10] = "01PM";
                            $final[0][11] = "02PM";
                            $final[0][12] = "03PM";
                            $final[0][13] = "04PM";
                            $final[0][14] = "05PM";
                            $final[0][15] = "06PM";
                            $final[0][16] = "07PM";
                            $final[0][17] = "08PM";
                            $final[0][18] = "09PM";
                            
                            foreach ($srDataw as $rows) {
                                $colu = 6;
                                $sr_id = $rows->aemp_id;
                                $final[$row_no][0] = $rows->ordm_date;
                                $final[$row_no][1] = $rows->slgp_name;
                                $final[$row_no][2] = $rows->zone_name;
                                $final[$row_no][3] = $rows->aemp_usnm;
                                $final[$row_no][4] = str_replace(",","-",$rows->aemp_name);
                                $final[$row_no][5] = $rows->aemp_mob1;
                               
                                if ($colu==6){
                                }
                                $h=8;
                                for ($i=6; $i<21; $i++) {
                                    $k = $i;
                                    if ($k==6){
                                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                                    }else if ($i>18){
                                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                                    }else{
                                        $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$h' and aemp_id='$sr_id'"));
                                    }

                                    $tam=0;
                                    foreach ($srDatas as $result)
                                        if($result->order_number > 0){$tam = $result->order_number;}else{$tam = 0;}
                                    //echo "{" . $k . " - ". $tam. "}";
                                    $final[$row_no][$colu] = $tam;
                                    $colu++;
                                    $h++;
                                }
                                $row_no = $row_no +1;
                            }

                        return $final;
                        }
                        else{
                            $zone_filter="";
                            $slgp_filter="";
                            if($dirg_id !=""){
                                $zone_filter=" AND zone_id IN (".implode(',',$zone_list).")";
                            }
                            if ($zone_id != "") {
                                $zone_filter = " AND zone_id = $zone_id";
                            }
                            if ($sales_group_id != "") {
                                $slgp_filter = " AND slgp_id = '$sales_group_id'";
                            }
                            
                            $data=DB::connection($this->db)->select("SELECT act_date,slgp_name,concat(t2.zone_code,'-',t2.zone_name)zone_name,aemp_usnm,replace(aemp_name,',','.')aemp_name,aemp_mob1,9am,
                                    10am,11am,12pm,1pm,2pm,3pm,4pm,5pm,6pm,7pm,8pm,9pm
                                FROM tbl_sr_activity_summary t1
                                INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                                WHERE act_date BETWEEN '$start_date' AND '$end_date'".$slgp_filter.$zone_filter."
                                ORDER BY act_date asc");
                            return $data;
                        }
                break;
            case "sr_activity_hourly_visit":
                if($time_period==0){
                    $q_z="";
                    $q2_g="";
                    if ($zone_id != "") {
                        $q_z = " AND t2.zone_id = $zone_id";
                    }
                    if ($sales_group_id != "") {
                        $q2_g = " AND t1.slgp_id = '$sales_group_id'";
                    }
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $sql = "CREATE TEMPORARY TABLE temp1 (id int auto_increment primary key) SELECT
                    t4.acmp_name, t4.slgp_name, t3.dirg_name,concat(t3.zone_code,'-',t3.zone_name)zone_name, t1.ordm_date,t1.aemp_id,replace(t2.aemp_name,',','.')aemp_name, t2.aemp_usnm, t2.aemp_mob1,
                    HOUR(ordm_time)'hr', COUNT(ordm_ornm) AS order_number
                    FROM `tt_ordm` t1
                    INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                    INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                    INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                    WHERE  t1.ordm_date = '$start_date' AND
                        t3.aemp_id = '$empId' and t4.aemp_id='$empId' ".$q_z . $q2_g."
                    GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";

                                $sql2 = "CREATE TEMPORARY TABLE temp2 (id int auto_increment primary key) SELECT t1.aemp_id, HOUR(npro_time) as hr, COUNT(t1.id) as note FROM `tt_npro` t1
                    INNER JOIN tm_aemp t2 ON  t1.aemp_id=t2.id
                    INNER JOIN user_area_permission t3 on t2.zone_id=t3.zone_id
                    INNER JOIN user_group_permission t4 ON t2.slgp_id=t4.slgp_id
                    WHERE t1.npro_date = '$start_date' AND
                        t3.aemp_id = '$empId' and t4.aemp_id='$empId' ". $q_z . $q2_g."
                    GROUP BY hr, t1.aemp_id ORDER BY t1.aemp_id, hr";


                    $srData = DB::connection($this->db)->select(DB::raw($sql));
                    $srData2 = DB::connection($this->db)->select(DB::raw($sql2));
                    $srDataw = DB::connection($this->db)->select(DB::raw("select ordm_date, aemp_id, aemp_usnm, aemp_name, slgp_name, aemp_mob1,
                                                                        acmp_name, zone_name, dirg_name from temp1 GROUP BY aemp_id"));
                    $row_no = 1;
                    $icol = 6;
                    $final[0][0] = "Order Date";
                    $final[0][1] = "Group";
                    $final[0][2] = "Zone";
                    $final[0][3] = "SR ID";
                    $final[0][4] = "SR Name";
                    $final[0][5] = "SR Mobile";
                    $final[0][6] = "09AM";
                    $final[0][7] = "10AM";
                    $final[0][8] = "11AM";
                    $final[0][9] = "12PM";
                    $final[0][10] = "01PM";
                    $final[0][11] = "02PM";
                    $final[0][12] = "03PM";
                    $final[0][13] = "04PM";
                    $final[0][14] = "05PM";
                    $final[0][15] = "06PM";
                    $final[0][16] = "07PM";
                    $final[0][17] = "08PM";
                    $final[0][18] = "09PM";
                
                    foreach ($srDataw as $rows) {
                        $colu = 6;
                        $sr_id = $rows->aemp_id;
                        $final[$row_no][0] = $rows->ordm_date;
                        $final[$row_no][1] = $rows->slgp_name;
                        $final[$row_no][2] = $rows->zone_name;
                        $final[$row_no][3] = $rows->aemp_usnm;
                        $final[$row_no][4] = str_replace(",","-",$rows->aemp_name);
                        $final[$row_no][5] = $rows->aemp_mob1;
                        if ($colu==6){

                        }
                        $h=8;
                        for ($i=6; $i<21; $i++) {
                            $k = $i;
                            if ($k==6){
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                                $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '0' AND hr<='8' and aemp_id='$sr_id'"));
                            }else if ($i>18){
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                                $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr >= '21' AND hr<='23' and aemp_id='$sr_id'"));
                            }else{
                                $srDatas = DB::connection($this->db)->select(DB::raw("select order_number from temp1 where hr ='$h' and aemp_id='$sr_id'"));
                                $srDatas2 = DB::connection($this->db)->select(DB::raw("select note from temp2 where hr ='$h' and aemp_id='$sr_id'"));
                            }

                            $tam2=0;
                            $tam=0;
                            foreach ($srDatas2 as $result2)
                                if($result2->note > 0){$tam2 = $result2->note ;}else{$tam2 = 0;}
                            foreach ($srDatas as $result)
                                if($result->order_number > 0){$tam = $result->order_number ;}else{$tam = 0;}
                            //echo "{" . $k . " - ". $tam. "}";
                            $final[$row_no][$colu] = $tam+$tam2;
                            $colu++;
                            $h++;
                        }

                        $row_no = $row_no +1;
                    }
                    return $final;
                }
                else{

                    $q_z="";
                    $q2_g="";
                    if($dirg_id !=""){
                        $q_z=" AND zone_id IN (".implode(',',$zone_list).")";
                    }
                    if ($zone_id != "") {
                        $q_z = " AND zone_id = $zone_id";
                    }
                    if ($sales_group_id != "") {
                        $q2_g = " AND slgp_id = '$sales_group_id'";
                    }
                    $data=DB::connection($this->db)->select("SELECT act_date,slgp_name,concat(t2.zone_code,'-',t2.zone_name)zone_name,aemp_usnm,replace(aemp_name,',','.')aemp_name,
                    aemp_mob1,SUM(9am+9amn)9am,SUM(10am+10amn)10am,SUM(11am+11amn)11am,SUM(12pm+12pmn)12pm,SUM(1pm+1pmn)1pm,
                        SUM(2pm+2pmn)2pm,SUM(3pm+3pmn)3pm,SUM(4pm+4pmn)4pm,SUM(5pm+5pmn)5pm,SUM(6pm+6pmn)6pm,SUM(7pm+7pmn)7pm,SUM(8pm+8pmn)8pm,SUM(9pm+9pmn)9pm
                        FROM tbl_sr_activity_summary t1
                        INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                        WHERE act_date BETWEEN '$start_date' AND '$end_date'".$q2_g.$q_z."
                        GROUP BY act_date,slgp_name,zone_name,aemp_usnm,aemp_name,aemp_mob1 ORDER BY act_date asc");
                    return $data;

                    //return $final;
                }
                break;
            case "sr_hourly_activity":
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q = " AND zone_id = $zone_id";
                }
                $data=DB::connection($this->db)->select("SELECT  `act_date`, `acmp_id`, `slgp_id`, `zone_id`, `acmp_name`, `slgp_name`,
                        `zone_code`, `zone_name`, `aemp_id`, `aemp_usnm`, `aemp_name`, `aemp_mob1`, `t_outlet`, `visit_olt`, `success_olt`,
                        `t_sku`, `9am`, `10am`, `11am`, `12pm`, `1pm`, `2pm`, `3pm`, `4pm`, `5pm`, `6pm`, `7pm`, (`8pm`+ `9pm`)8pm, `9amv`,
                        `10amv`, `11amv`, `12pmv`, `1pmv`, `2pmv`, `3pmv`, `4pmv`, `5pmv`, `6pmv`, `7pmv`, (`8pmv`+ `9pmv`)8pmv,
                        ROUND(`9exp_a`/1000,2)9exp_a,
                        ROUND(`10exp_a`/1000,2)10exp_a, 
                        ROUND(`11exp_a`/1000,2)11exp_a, 
                        ROUND(`12exp_p`/1000,2)12exp_p, 
                        ROUND(`1exp_p`/1000,2)1exp_p,
                        ROUND(`2exp_p`/1000,2)2exp_p,
                        ROUND(`3exp_p`/1000,2)3exp_p, 
                        ROUND(`4exp_p`/1000,2)4exp_p, 
                        ROUND(`5exp_p`/1000,2)5exp_p,
                        ROUND(`6exp_p`/1000,2)6exp_p, 
                        ROUND(`7exp_p`/1000,2)7exp_p, 
                        ROUND((`8exp_p`+`9exp_p`)/1000,2)8exp_p, 
                        ROUND(`t_exp`/1000,2)t_exp,
                        (`9am`+ `10am`+ `11am`+ `12pm`+ `1pm`+ `2pm`+ `3pm`+ `4pm`+ `5pm`+ `6pm`+ `7pm`+ `8pm`+ `9pm`)t_order,
                        than_name,than_olt FROM `tbl_sr_hourly_summary` 
                        WHERE slgp_id=$sales_group_id AND act_date between '$start_date' AND '$end_date'
                        " .$zone_q. "
                          ");
                return $data;
                break;
            case "attendance_report":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q2=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q2 = " AND t2.zone_id = $zone_id";
                }
                if ($sales_group_id != "") {
                    $q1= " AND t2.slgp_id = '$sales_group_id'";
                }
                $data=DB::connection($this->db)->select("SELECT t1.attn_date,t1.slgp_name,t1.dirg_name,t1.zone_name,t1.aemp_id,t1.aemp_usnm,replace(t1.aemp_name,',','-')aemp_name,t1.aemp_mob1,t1.edsg_name,
                    ifnull(t1.start_time,'0000-00-00 00:00:00') start_time,ifnull(t1.end_time,'0000-00-00 00:00:00') end_time,ifnull(t2.atyp_name,'Absent')status FROM
                    (SELECT t2.id aemp_id,
                    t1.dhbd_date attn_date,t4.slgp_name,t6.dirg_name,t5.zone_name,t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t7.edsg_name,
                    min(t3.attn_time) start_time,max(t3.attn_time) end_time,
                    max(t3.atten_atyp) type
                    FROM th_dhbd_5 t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    LEFT JOIN tt_attn t3 ON t2.id=t3.aemp_id AND t3.attn_date=t1.dhbd_date
                    INNER JOIN tm_slgp t4 ON t2.slgp_id=t4.id
                    INNER JOIN tm_zone t5 ON t2.zone_id=t5.id
                    INNER JOIN tm_dirg t6 ON t5.dirg_id=t6.id
                    INNER JOIN tm_edsg t7 ON t2.edsg_id=t7.id
                    WHERE t1.dhbd_date between '$start_date' AND '$end_date'
                    AND t2.lfcl_id=1 ".$q1.$q2."
                    GROUP BY t1.dhbd_date,t4.slgp_name,t6.dirg_name,t5.zone_name,t2.id,t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1
                    ORDER BY t5.zone_code,t2.aemp_name,t1.dhbd_date ASC)t1
                    LEFT JOIN tm_atyp t2 ON t1.type=t2.id");
                return $data;
                break;
            case "note_report":
                $q1 = "";
                $q2 = "";
                $q3 = "";
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = " AND t4.zone_id = '$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t3.slgp_id = '$sales_group_id'";
                }
                $data = DB::connection($this->db)->select("SELECT
                        t1.id,t1.note_date,replace(IF(t7.lfcl_id=2,concat(t7.ntpe_name,'/','InActv'),t7.ntpe_name),',','-') as note_type, t2.aemp_usnm, t2.aemp_name, t3.slgp_name, t3.slgp_code,
                        t4.zone_name, t4.zone_code,
                        replace(t1.note_body,',','-') note_body,
                        t5.edsg_name,replace(concat(t6.site_code, ' - ', t6.site_name),',','.')   AS site_name,
                        replace(t1.geo_addr, ',' , '.' ) as geo_addr, TIME(t1.note_dtim) as n_time
                        FROM `tt_note` t1 
                        INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                        INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                        INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                        INNER JOIN tm_edsg t5 ON t2.edsg_id = t5.id
                        LEFT JOIN tm_site t6 on t1.site_code=t6.site_code
                        LEFT JOIN tm_ntpe t7 on t1.ntpe_id=t7.id
                        WHERE t1.note_date >= '$start_date' AND t1.note_date <= '$end_date' AND t3.aemp_id = '$empId' AND
                            t4.aemp_id = '$empId' " . $q1 . $q2 . $q3 . " AND t3.acmp_id = '$acmp_id'
                        ORDER BY t1.note_date, t3.slgp_code, t4.zone_code, t1.aemp_id desc");
                return $data;
                break;
            case "note_summary":
                $q1 = "";
                $q2 = "";
                $q3 = "";
                if($dirg_id !=""){
                    $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = " AND t2.zone_id = '$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t3.id = '$sales_group_id'";
                }
                $data=DB::connection($this->db)->select("SELECT 
                        t2.id aemp_id,t2.aemp_usnm staff_id,t2.aemp_name staff_name,t4.edsg_name,
                        t3.slgp_name,COUNT(t1.id)total_note
                        FROM `tt_note`  t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                        INNER JOIN tm_edsg t4 ON t2.edsg_id=t4.id
                        WHERE t1.note_date between '$start_date' AND '$end_date'". $q1.$q2.$q3."
                        GROUP BY t2.aemp_usnm,t2.aemp_name,t3.slgp_name,t4.edsg_name  
                        ORDER BY t2.aemp_name,t3.slgp_name");
                return array('data'=>$data,'start_date'=>$start_date,'end_date'=>$end_date);
                break;
            case "zone_summary":
                $rtype=$request->rtype;
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $q1 = "AND t4.zone_id = '$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = "AND t2.slgp_id = '$sales_group_id'";
                }
                if($rtype==1){
                    $data=DB::connection($this->db)->select("SELECT 
                        t1.dhbd_date date,
                        '' dirg_name,
                        t4.zone_name zone_name,
                        t3.slgp_name slgp_name,
                        SUM(t1.dhbd_ucnt)t_sr,
                        sum(t1.dhbd_prnt)p_sr,
                        SUM( t1.`dhbd_lvsr`)l_sr,
                        SUM(t1.dhbd_pact)  pro_sr,
                        SUM(t1.dhbd_tsit)t_outlet,
                        SUM(t1.dhbd_tvit) c_outlet,
                        SUM(t1.dhbd_memo)s_outet,
                        round(ifnull(SUM(t1.dhbd_line) / SUM(t1.dhbd_memo),0), 2) lpc,
                        SUM(t1.dhbd_tamt)t_amnt
                        FROM th_dhbd_5 t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                        INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                        WHERE  t1.dhbd_date BETWEEN '$start_date' AND '$end_date' AND t3.aemp_id='$empId'  AND t4.aemp_id='$empId' AND t1.role_id=1 ". $q1. $q2 ."
                        GROUP BY t2.zone_id,t4.zone_name,t1.dhbd_date,t3.slgp_name order by t4.zone_code asc");
                }else{
                    $data=DB::connection($this->db)->select("SELECT 
                        t4.zone_name zone_name,
                        t3.slgp_name slgp_name,
                        SUM(t1.dhbd_ucnt)t_sr,
                        sum(t1.dhbd_prnt)p_sr,
                        SUM( t1.`dhbd_lvsr`)l_sr,
                        SUM(t1.dhbd_pact)  pro_sr,
                        SUM(t1.dhbd_tsit)t_outlet,
                        SUM(t1.dhbd_tvit) c_outlet,
                        SUM(t1.dhbd_memo)s_outet,
                        round(ifnull(SUM(t1.dhbd_line) / SUM(t1.dhbd_memo),0), 2) lpc,
                        SUM(t1.dhbd_tamt)t_amnt
                        FROM th_dhbd_5 t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN user_group_permission t3 ON t2.slgp_id = t3.slgp_id
                        INNER JOIN user_area_permission t4 ON t2.zone_id = t4.zone_id
                        WHERE  t1.dhbd_date BETWEEN '$start_date' AND '$end_date' AND t3.aemp_id='$empId'  AND t4.aemp_id='$empId' AND t1.role_id=1 ". $q1. $q2 ."
                        GROUP BY t2.zone_id,t4.zone_name,t3.slgp_name order by t4.zone_code asc");
                }
                
                return $data;
                break;
            case "sr_productivity":
                $rtype=$request->rtype;
                $utype=$request->utype;
                $q1 = "";
                $q2 = ""; 
                if($rtype==1 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = '$zone_id' ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                    }
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $data=DB::connection($this->db)->select("SELECT
                            t1.date,
                            t1.zone_id,
                            t2.slgp_name   														AS slgp_name,
                            t3.zone_name   														AS zone_name,
                            t1.slgp_id                                  						AS slgp_id,
                            t1.aemp_code                                						AS aemp_id,
                            t1.aemp_name                                						AS aemp_name,
                            t1.aemp_mobile                              						AS aemp_mobile,
                            replace(concat(t7.aemp_usnm,'-',t7.aemp_name),',','-')       		AS aemp_mngr,
                            replace(t1.base_name,',','-')										AS base_name,
                            replace(ifnull(t1.rout_name,'N/A'),',','-') 						AS rout_name,
                            ifnull(t1.t_outlet,0)                       						AS t_outlet,
                            (t1.v_outlet+t1.s_outet)											AS c_outlet,
                            t1.s_outet                        									AS s_outet,
                            ifnull(round((t1.v_outlet+t1.s_outet)*100/t1.t_outlet,2),0.00)		AS c_percentage,
                            round(s_outet*100/(t1.s_outet+t1.v_outlet),2) 						AS strikeRate,
                            round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) 						AS lpc,
                            SUM(t1.t_sku)                                                       AS totl_sku,
                            round(SUM(t1.t_amnt), 2)                    						AS t_amnt,
                            t1.inTime                                   						AS inTime,
                            CASE WHEN t1.npv_min_time ='00:00:00' THEN t1.firstOrTime
                            ELSE IF (t1.firstOrTime='00:00:00',t1.npv_min_time,LEAST(t1.npv_min_time,t1.firstOrTime))
                            END                                                                 AS firstOrTime,
                            GREATEST(t1.lastOrTime,t1.npv_max_time) 							AS lastOrTime,
                            TIMEDIFF(t1.lastOrTime, t1.firstOrTime)     						AS workTime,
                            t1.ro_visit 														AS ro_visit
                            FROM `tt_aemp_summary1` t1
                            INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                            INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                            INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                            INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                            WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND (t1.v_outlet>0 OR t1.s_outet>0) 
                            AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId'" . $q1 . $q2. " and t2.acmp_id='$acmp_id'
                            GROUP BY t1.zone_id,t1.date,t1.slgp_id,t2.slgp_name,t3.zone_name,t1.aemp_code,t1.aemp_name,t1.aemp_mobile,
                            t7.aemp_usnm,t1.base_name
                            ORDER BY t3.zone_code,t1.date,t2.slgp_code,t1.aemp_code ASC");
                    $result=collect($data);
                    $summary = $result->pipe(function ($result) {
                        $t_olt=$result->sum('t_outlet')>0?$result->sum('t_outlet'):1;
                        return collect([
                            'total_outlet' => $result->sum('t_outlet'),
                            'total_visit' => $result->sum('c_outlet'),
                            'total_s_outlet' => $result->sum('s_outet'),
                            'total_visit_percentage' =>round($result->sum('c_outlet')*100/$t_olt,2),
                            'total_strikeRate' =>round($result->avg('strikeRate'),2),
                            'total_lpc' => round($result->avg('lpc'),2),
                            'total_amount' => round($result->sum('t_amnt')/1000,2),
                            'total_ro_visit' => $result->sum('ro_visit'),
                            'total_sku'=>$result->sum('totl_sku'),
                        ]);
                    });
                }
                else if($rtype==2 && $utype==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = '$zone_id' ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                    }
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $data =DB::connection($this->db)->select("SELECT
                            t1.zone_id,t3.zone_name,
                            t2.slgp_name   										AS slgp_name,
                            t3.zone_name   										AS zone_name,
                            t1.slgp_id                                  		AS slgp_id,
                            t1.aemp_code                                		AS aemp_id,
                            t1.aemp_name                                		AS aemp_name,
                            t1.aemp_mobile                              		AS aemp_mobile,
                            concat(t7.aemp_usnm,'-',t7.aemp_name)       		AS aemp_mngr,
                            sum(t1.t_outlet)                       	            AS t_outlet,
                            sum(ifnull(t1.v_outlet,0)) + sum(t1.s_outet)     	AS c_outlet,
                            sum(ifnull(t1.s_outet,0))                        	AS s_outet,
                            round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) 		AS lpc,
                            SUM(t1.t_sku)                                       AS totl_sku,
                            round(SUM(t1.t_amnt), 2)                    		AS t_amnt,
                            sum(t1.ro_visit)                                    AS ro_visit
                            FROM `tt_aemp_summary1` t1
                            INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                            INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                            INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                            INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                            WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet!='0' AND t2.aemp_id = $empId AND
                                t3.aemp_id =$empId  and t2.acmp_id=$acmp_id " .$q1.$q2.  "
                            GROUP BY  t1.slgp_id,  t1.aemp_id,t1.aemp_code,t1.aemp_name,t1.aemp_mobile
                            ORDER BY t3.zone_code,t1.date,t2.slgp_code,t1.aemp_code ASC;");
                    $result=collect($data);
                    $summary = $result->pipe(function ($result) {
                        $t_olt=$result->sum('t_outlet')>0?$result->sum('t_outlet'):1;
                        return collect([
                            'total_outlet' => $result->sum('t_outlet'),
                            'total_visit' => $result->sum('c_outlet'),
                            'total_s_outlet' => $result->sum('s_outet'),
                            'total_visit_percentage' =>round($result->sum('c_outlet')*100/$t_olt,2),
                            'total_strikeRate' =>round($result->avg('strikeRate'),2),
                            'total_lpc' => round($result->avg('lpc'),2),
                            'total_amount' => round($result->sum('t_amnt')/1000,2),
                            'total_ro_visit' => $result->sum('ro_visit'),
                            'total_sku'=>$result->sum('totl_sku'),
                        ]);
                    });

                }
                else if($rtype==1 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id = '$zone_id' ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                    }
                    $data=DB::connection($this->db)->select("SELECT t1.ordm_date,t1.aemp_id,t1.aemp_usnm,t1.aemp_name,COUNT(DISTINCT t1.site_id) olt_cov,
                            COUNT(t1.site_id) olt_visit,sum(t1.memo)memo,
                            sum(t1.item_count) item_count,round(sum(t1.ordm_amnt),2)ordm_amnt,t2.edsg_name,t3.zone_name,SUM(CASE WHEN t1.flag=1 THEN 1 ELSE 0 END)s_olt
                            FROM
                            (SELECT
                            t1.ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,count(t1.id)memo,round(sum(t1.ordm_amnt),2) ordm_amnt,sum(t1.ordm_icnt) item_count,1 flag
                            FROM tt_ordm t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                            WHERE  t1.ordm_date between '$start_date' AND '$end_date' AND t2.role_id >1 " .$q1 .$q2. "
                            GROUP BY t1.ordm_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name
                            UNION ALL
                            SELECT
                            t1.npro_date ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,0 memo,0 ordm_amnt,0 item_count,0 flag
                            FROM tt_npro t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                            WHERE t1.npro_date between '$start_date' AND '$end_date' AND t2.role_id >1  " .$q1 .$q2. "
                            GROUP BY t1.npro_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name)t1
                            INNER JOIN tm_edsg t2 ON t1.edsg_id=t2.id
                            INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
                            GROUP BY t1.ordm_date,t1.aemp_id,t1.aemp_usnm,t1.aemp_name,t2.edsg_name,t3.zone_name ORDER BY  t1.ordm_date,t1.aemp_usnm;");
                    $result=collect($data);
                    $summary = $result->pipe(function ($result) {
                        $rs=$result->sum('memo');
                        if($rs==0){
                            $rs=1;
                        }
                        return collect([
                            'total_site' => $result->sum('olt_cov'),
                            'total_amount' =>round($result->sum('ordm_amnt')/1000,2),
                            'total_item' => $result->sum('item_count'),
                            'avg_olt_order'=>round($result->sum('ordm_amnt')/(1000*$rs),2),
                            'total_lpc'=>round($result->sum('item_count')/$rs,2),
                            'total_memo'=>$result->sum('memo'),
                            'total_visit'=>$result->sum('olt_visit'),
                            'total_solt'=>$result->sum('s_olt'),
                        ]);
                    });
                }
                if($rtype==2 && $utype==2){
                    if($dirg_id !=""){
                        $q1=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t2.zone_id = '$zone_id' ";
                    }
                    if ($sales_group_id != "") {
                        $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                    }
                    // $data=DB::connection($this->db)->select("SELECT  
                    //         t2.aemp_usnm,t2.aemp_name,count(distinct t1.site_id)ord_site,count(t1.site_id)ord_site_v, count(distinct t1.id)memo,
                    //         round(sum(t1.ordm_amnt),2) ordm_amnt,sum(t1.ordm_icnt) item_count,t5.edsg_name,
                    //         t4.zone_name
                    //         FROM tt_ordm t1
                    //         INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                    //         INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
                    //         INNER JOIN tm_edsg t5 ON t2.edsg_id=t5.id
                    //         WHERE t2.role_id>1
                    //         AND ordm_date between  '$start_date' AND '$end_date' " .$q1.$q2."
                    //         GROUP BY zone_name,aemp_usnm,aemp_name,edsg_name
                    //         ORDER BY t4.zone_code,t2.aemp_name ASC                           
                    //         ");
                    // $result=collect($data);
                    // $summary = $result->pipe(function ($result) {
                    //     $rs=$result->sum('ord_site');
                    //     if($rs==0){
                    //         $rs=1;
                    //     }
                    //     return collect([
                    //         'total_site' => $result->sum('ord_site'),
                    //         'total_amount' =>round($result->sum('ordm_amnt')/1000,2),
                    //         'total_item' => $result->sum('item_count'),
                    //         'avg_olt_order'=>round($result->sum('ordm_amnt')/(1000*$rs),2),
                    //         'total_lpc'=>round($result->sum('item_count')/$rs,2),
                    //         'total_memo'=>$result->sum('memo'),
                    //         'total_visit'=>$result->sum('ord_site_v'),
                    //     ]);
                    // });
                    $data=DB::connection($this->db)->select("SELECT t1.aemp_id,t1.aemp_usnm,t1.aemp_name,COUNT(DISTINCT t1.site_id) olt_cov,
                            COUNT(t1.site_id) olt_visit,sum(t1.memo)memo,
                            sum(t1.item_count) item_count,round(sum(t1.ordm_amnt),2)ordm_amnt,t2.edsg_name,t3.zone_name,SUM(CASE WHEN t1.flag=1 THEN 1 ELSE 0 END)s_olt
                            FROM
                            (SELECT
                            t1.ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,count(t1.id)memo,round(sum(t1.ordm_amnt),2) ordm_amnt,sum(t1.ordm_icnt) item_count,1 flag
                            FROM tt_ordm t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                            WHERE  t1.ordm_date between '$start_date' AND '$end_date' AND t2.role_id >1 " .$q1 .$q2. "
                            GROUP BY t1.ordm_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name
                            UNION ALL
                            SELECT
                            t1.npro_date ordm_date,t2.id aemp_id,t2.zone_id,t2.aemp_usnm,t2.aemp_name,t2.edsg_id,t1.site_id,0 memo,0 ordm_amnt,0 item_count,0 flag
                            FROM tt_npro t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                            WHERE t1.npro_date between '$start_date' AND '$end_date' AND t2.role_id >1  " .$q1 .$q2. "
                            GROUP BY t1.npro_date,t2.id,t1.site_id,t2.aemp_usnm,t2.aemp_name)t1
                            INNER JOIN tm_edsg t2 ON t1.edsg_id=t2.id
                            INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
                            GROUP BY t1.aemp_id,t1.aemp_usnm,t1.aemp_name,t2.edsg_name,t3.zone_name ORDER BY t1.aemp_usnm;");
                    $result=collect($data);
                    $summary = $result->pipe(function ($result) {
                        $rs=$result->sum('memo');
                        if($rs==0){
                            $rs=1;
                        }
                        return collect([
                            'total_site' => $result->sum('olt_cov'),
                            'total_amount' =>round($result->sum('ordm_amnt')/1000,2),
                            'total_item' => $result->sum('item_count'),
                            'avg_olt_order'=>round($result->sum('ordm_amnt')/(1000*$rs),2),
                            'total_lpc'=>round($result->sum('item_count')/$rs,2),
                            'total_memo'=>$result->sum('memo'),
                            'total_visit'=>$result->sum('olt_visit'),
                            'total_solt'=>$result->sum('s_olt'),
                        ]);
                    });
                }
                
                return array('data'=>$data,'summary'=>$summary);
                break;
            case "sr_non_productivity":
                $non_pr1 = "";
                $non_pr2 = "";
                if ($sales_group_id!=""){
                    $non_pr1 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                if($dirg_id !=""){
                    $non_pr2=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $non_pr2 = " and t1.zone_id='$zone_id' ";
                }
                $data =DB::connection($this->db)->select("SELECT t1.date, t1.zone_id, concat(t2.slgp_code, ' - ', t2.slgp_name) AS slgp_name, concat(t3.zone_code, ' - ', t3.zone_name) AS zone_name,
                        concat(t3.dirg_code, ' - ', t3.dirg_name) AS dirg_name, t1.slgp_id AS slgp_id, t1.aemp_code AS aemp_id, t1.aemp_name AS aemp_name, t1.aemp_mobile AS aemp_mobile,
                        concat(t7.aemp_usnm,'-',t7.aemp_name)       AS aemp_mngr,
                        t1.inTime AS inTime FROM `tt_aemp_summary1` t1 INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id 
                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id 
                        INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                        INNER JOIN tm_aemp t7 ON t6.aemp_mngr=t7.id
                        WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId'  
                        AND t3.aemp_id = '$empId' AND t1.atten_atyp='1' " . $non_pr1 . $non_pr2 . " AND t2.acmp_id = '$acmp_id' AND (t1.v_outlet=0 AND t1.s_outet=0)
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id,t1.aemp_code,t3.zone_code,t3.zone_name,t3.dirg_name,t3.dirg_code,t1.aemp_name,t1.aemp_mobile,t7.aemp_usnm,t7.aemp_name,t1.inTime
                        ORDER BY t3.zone_code,t1.date, t2.slgp_code");
                return $data;
                break;
            case "sr_summary_by_group":
                $q='';
                if($dirg_id !=""){
                    $q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id!=""){
                    $q = " AND t3.zone_id='$zone_id' ";
                }
                $data =DB::connection($this->db)->select("SELECT
                t1.date,
                t1.slgp_name,
                t4.t_sr,
                t2.p_sr,
                t3.l_sr,
                t1.pro_sr,
                t1.t_outlet,
                t1.c_outlet,
                t1.s_outet,
                t1.lpc,
                t1.t_amnt
                FROM (SELECT
                        t1.date,
                    
                        concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
                    
                        t1.slgp_id,
                        count(DISTINCT t1.aemp_id) as pro_sr,
                        SUM(t1.t_outlet)                            AS t_outlet,
                        (SUM(t1.v_outlet) + SUM(t1.s_outet))        AS c_outlet,
                        SUM(t1.s_outet)                             AS s_outet,
                        round((SUM(t1.t_sku) / SUM(t1.s_outet)), 2) AS lpc,
                        round(SUM(t1.t_amnt), 2)                    AS t_amnt
                    FROM `tt_aemp_summary1` t1
                        INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                        INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                    WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.s_outet > '0' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                            t2.slgp_id =$sales_group_id ". $q." AND t2.acmp_id='$acmp_id'
                    GROUP BY t1.slgp_id, t1.date ORDER BY t2.slgp_code) t1 INNER JOIN
                
                (SELECT
                    t1.date,
                    t1.slgp_id,
                    count(DISTINCT t1.aemp_id) AS p_sr
                FROM tt_aemp_summary1 t1
                    INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                    INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                        t2.slgp_id=$sales_group_id ". $q." AND t1.atten_atyp = '1' AND t2.acmp_id='$acmp_id'
                GROUP BY t1.slgp_id, t1.date) t2 ON t1.slgp_id = t2.slgp_id AND t1.date = t2.date
                LEFT JOIN
                
                (SELECT
                    t1.date,
                    t1.slgp_id,
                    count(DISTINCT t1.aemp_id) AS l_sr
                FROM tt_aemp_summary1 t1
                    INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                    INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                        t2.slgp_id =$sales_group_id ". $q." AND t1.atten_atyp != '1' AND t2.acmp_id='$acmp_id'
                GROUP BY t1.slgp_id, t1.date) t3 ON t1.slgp_id = t3.slgp_id AND t1.date = t3.date
                INNER JOIN
                
                
                (SELECT
                    t1.slgp_id,
                    count(DISTINCT t1.id) AS t_sr
                FROM tm_aemp t1
                    INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                    INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                WHERE t2.aemp_id = '$empId' AND t3.aemp_id = '$empId' AND
                        t2.slgp_id =$sales_group_id ". $q."
                        AND t1.lfcl_id = '1' AND t1.role_id = '1' AND t2.acmp_id='$acmp_id'
                GROUP BY  t1.slgp_id) t4 ON t1.slgp_id = t4.slgp_id");
                return $data;
                break;
            case "market_outlet_sr_outlet":
                $data=DB::connection($this->db)->select("SELECT t1.dsct_name,t1.than_name,t1.ward_name,
                        replace(t1.mktm_name,',','-')mktm_name,t1.total_outlet m_outlet_quantity,ifnull(t2.slgp_out,0)sr_outlet_quantity
                        FROM tbl_mktm_wise_total_outlet t1 
                        LEFT JOIN(SELECT mktm_id,slgp_out FROM tbl_mktm_slgp_wise_outlet WHERE slgp_id=$sales_group_id)  t2 
                        ON t1.mktm_id=t2.mktm_id
                        WHERE t1.than_id=$than_id
                        GROUP BY t1.dsct_name,t1.than_name,t1.mktm_id,t1.mktm_name,t1.ward_name,t1.total_outlet");
                return $data;
                break;
            case 'outlet_coverage':
                $const=' WHERE ';
                $query='';
                if($dist_id){
                    $query=$const." t1.dsct_id=$dist_id";
                }
                if($than_id){
                    $query.=" AND t1.than_id=$than_id";
                }
                $data=DB::connection($this->db)->select("SELECT '$slgp_name' SALES_GROUP,t1.dsct_name DISTRICT_NAME,t1.than_name THANA_NAME,
                        sum(t1.total_outlet)AVAILABLE_OUTLET,ifnull(sum(t2.slgp_out),0)ROUTED_OUTLET,ifnull(sum(t2.Visited_Sites),0) VISITED_SITES,round(sum(t2.ordm_amnt)/1000,2)ordm_amnt
                        FROM tbl_mktm_wise_total_outlet t1 
                        LEFT JOIN
                        (
                            SELECT p2.id slgp_id,p2.slgp_name,p1.mktm_id,slgp_out,Visited_Sites,ordm_amnt FROM tbl_mktm_slgp_wise_outlet p1 
                            inner join tm_slgp p2 ON p1.slgp_id=p2.id
                            LEFT JOIN (Select 
                                        slgp_id,mktm_id,count(distinct site_id) Visited_Sites,sum(ordm_amnt)ordm_amnt
                                        FROM tbl_olt_cov_details WHERE ordm_date between '$start_date' AND '$end_date' AND slgp_id=$sales_group_id
                                        GROUP BY slgp_id,mktm_id
                                        ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                            WHERE p1.slgp_id=$sales_group_id
                        )  t2 
                        ON t1.mktm_id=t2.mktm_id " .$query. "                                              
                        GROUP BY t1.dsct_name,t1.than_name");
                $result=collect($data);
                $summary=$result->pipe(function($result){
                    return collect([
                        'a_olt'=>$result->sum('AVAILABLE_OUTLET'),
                        'r_olt'=>$result->sum('ROUTED_OUTLET'),
                        'v_olt'=>$result->sum('VISITED_SITES'),
                        't_amnt'=>round($result->sum('ordm_amnt'),2),
                    ]);
                });
                return array(
                    'data'=>$data,
                    'summary'=>$summary
                );
                break;
            case "item_coverage":
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t2.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t2.zone_id =$zone_id ";
                }
                $data=DB::connection($this->db)->select("SELECT 
                        '$slgp_name' slgp_name,t2.aemp_usnm,t2.aemp_name,COUNT(DISTINCT amim_id) cov_item,round(sum(t1.order_amnt)/1000,2)order_amnt
                        FROM `tbl_itm_cov_details` t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        WHERE t1.slgp_id=$sales_group_id AND ordm_date between '$start_date' AND '$end_date' " .$zone_q."
                        GROUP BY t2.aemp_usnm,t2.aemp_name");
                $result=collect($data);
                $summary=$result->pipe(function($result){
                    return collect([
                        't_ordr'=>round($result->sum('order_amnt'),2),                       
                    ]);
                });
                return array(
                    'data'=>$data,
                    'summary'=>$summary
                );
                break;
            case "item_summary":
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t3.zone_id =$zone_id ";
                }
                $data=DB::connection($this->db)->select("Select 
                        t1.aemp_id,t3.aemp_usnm,t3.aemp_name,t3.slgp_name,t3.zone_code,t3.zone_name,t3.than_name,
                        t2.amim_name,t2.amim_code,t1.slgp_id,t4.itsg_name,
                        max(t3.than_olt)than_olt,
                        sum(t_outlet) t_outlet,sum(visit_olt) visit_olt,sum(success_olt)success_olt,
                        ROUND(sum(order_amnt)/1000,2)exp,sum(order_qnty)qnty,sum(t1.attr1) count_site
                        FROM
                        tbl_itm_cov_details t1
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tbl_sr_hourly_summary t3 ON t1.ordm_date=t3.act_date AND t1.aemp_id=t3.aemp_id
                        INNER JOIN tm_itsg t4 ON t2.itsg_id=t4.id
                        WHERE t1.ordm_date between '$start_date' AND '$end_date' AND t1.slgp_id=$sales_group_id " .$zone_q. "
                        GROUP BY t1.aemp_id,t3.aemp_usnm,t3.aemp_name,t3.slgp_name,t3.zone_code,t3.zone_name,t3.than_name,
                        t2.amim_name,t1.slgp_id,t2.amim_code,t4.itsg_name
                        ORDER BY t3.aemp_usnm,t4.itsg_code,t2.amim_code limit 25000;");
                $result=collect($data);
                $summary=$result->pipe(function($result){
                    return collect([
                        'than_olt'=>$result->avg('than_olt'),                       
                        't_outlet'=>$result->sum('t_outlet'),                       
                        'visit_olt'=>$result->sum('visit_olt'),                       
                        'success_olt'=>$result->sum('success_olt'),                       
                        'exp'=>$result->sum('exp'),                       
                        'qnty'=>$result->sum('qnty'),                       
                        'count_site'=>$result->sum('count_site'),                       
                    ]);
                });
                return array(
                    'data'=>$data,
                    'summary'=>$summary
                );
                break;
            case "item_summary_depo_wise":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t3.zone_id = '$zone_id' ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                $data=DB::connection($this->db)->select("SELECT 
                        t5.id,t5.amim_code,t5.amim_name,t4.dlrm_code,t4.dlrm_name,t6.itsg_name,t6.itsg_code,t7.itcl_name,t7.itcl_code,ROUND(SUM(t2.ordd_oamt)/1000,2) order_amnt,
                        (SUM(t2.ordd_inty)%t5.amim_duft) order_pics,SUBSTRING_INDEX(SUM(t2.ordd_inty)/t5.amim_duft,'.',1) order_ctn,
                        ROUND(SUM(t2.ordd_odat)/1000,2)deli_amnt
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                        INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
                        INNER JOIN tm_amim t5 ON t2.amim_id=t5.id
                        INNER JOIN tm_itsg t6 ON t5.itsg_id=t6.id
                        INNER JOIN tm_itcl t7 ON t5.itcl_id=t7.id
                        INNER JOIN tm_zone t8 ON t3.zone_id=t8.id
                        WHERE t1.ordm_date between '$start_date' AND '$end_date' ". $q1. $q2."
                        GROUP BY t5.id,t5.amim_code,t5.amim_name,t4.dlrm_code,t4.dlrm_name,t6.itsg_name,t6.itsg_code,t7.itcl_name,t7.itcl_code
                        ORDER BY t5.amim_code,t4.dlrm_code;");
                return $data;
                break;
            case "weekly_outlet_summary":
                $period=$request->year_mnth;
                $sr_id=$request->weekly_olt_sr;
                $data='';
                // $all_week=DB::connection($this->db)->select("Select WEEK(ordm_date) week_no FROM tbl_olt_cov_details 
                //             WHERE DATE_FORMAT(ordm_date, '%Y-%m') ='$period'
                //             GROUP BY week_no ORDER BY week_no asc;");
                            
                $all_week=DB::connection($this->db)->select("Select WEEK(c_date) week_no FROM tm_clndr 
                            WHERE DATE_FORMAT(c_date, '%Y-%m') ='$period'
                            GROUP BY week_no ORDER BY week_no asc;");
                            // return $start_date ."|". $end_date;
                $select_q='';
                $filter='';
                if($sales_group_id){
                    $filter=" AND slgp_id=$sales_group_id";
                }
                for($i=0;$i<count($all_week);$i++){
                    $week=$all_week[$i]->week_no;
                    // $select_q.=",if(weeks=$week,order_amnt,0) `amnt$week`,if(weeks=$week,visit,0) `v$week`,if(weeks=$week,memo,0) `m$week`";
                    $select_q.=",sum(if(weeks=$week,order_amnt,0)) `amnt$week`,sum(if(weeks=$week,visit,0)) `v$week`,sum(if(weeks=$week,memo,0)) `m$week`";
                }
                if($sr_id==0){
                    $data=DB::connection($this->db)->select("               
                        SELECT t3.aemp_usnm,t3.aemp_name,t2.site_code,t2.site_name,t1.site_id " . $select_q . ",IFNULL(ROUND(sum(order_amnt),2),0)t_amnt,IFNULL(SUM(memo),0)t_memo,IFNULL(SUM(visit),0)t_visit
                        FROM (
                        SELECT 
                        t1.aemp_id,site_id,
                        WEEK(ordm_date) weeks,
                        ROUND(SUM(ordm_amnt)/1000,2) order_amnt,
                        count(site_id) visit,
                        SUM(IF(ordm_amnt>0,1,0)) memo
                        FROM tbl_olt_cov_details t1
                        WHERE  DATE_FORMAT(ordm_date, '%Y-%m') ='$period' " .$filter.  "
                        GROUP BY aemp_id,site_id,weeks
                        ORDER BY site_id)t1
                        INNER JOIN tm_site t2 ON t1.site_id=t2.id 
                        INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                        GROUP BY  t2.site_code,t2.site_name,t1.site_id;                       
                        ");
                }
                else{
                    $data=DB::connection($this->db)->select("               
                            SELECT t4.aemp_usnm,t4.aemp_name,t3.site_code,t3.site_name,t1.site_id " . $select_q ." ,IFNULL(ROUND(sum(order_amnt),2),0)t_amnt,IFNULL(SUM(memo),0)t_memo,IFNULL(SUM(visit),0)t_visit
                            FROM 
                            (
                            SELECT 
                            aemp_id,site_id
                            FROM tl_rpln t1
                            INNER JOIN tl_rsmp t2 ON t1.rout_id=t2.rout_id
                            WHERE t1.aemp_id=$sr_id)t1
                            LEFT JOIN                            
                            (SELECT 
                            t1.aemp_id,site_id,
                            WEEK(ordm_date) weeks,
                            ROUND(SUM(ordm_amnt)/1000,2) order_amnt,
                            count(site_id) visit,
                            SUM(IF(ordm_amnt>0,1,0)) memo
                            FROM tbl_olt_cov_details t1
                            WHERE  DATE_FORMAT(ordm_date, '%Y-%m') ='$period' AND t1.aemp_id=$sr_id
                            GROUP BY aemp_id,site_id,weeks
                            ORDER BY site_id) t2 ON t1.aemp_id=t2.aemp_id AND t1.site_id=t2.site_id
                            INNER JOIN tm_site t3 ON t1.site_id=t3.id 
                            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
                            GROUP BY t4.aemp_usnm,t4.aemp_name,t3.site_code,t3.site_name,t1.site_id;                      
                        ");
                }
                
                return array('data'=>$data,'weeks'=>$all_week);
                break;
            case "order_vs_delivery_adv":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t4.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t4.zone_id = '$zone_id' ";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                $data=DB::connection($this->db)->select("SELECT 
                        t8.dirg_name,t7.zone_name,t4.aemp_usnm,t4.aemp_name,
                        t6.site_code,t6.site_name,t1.ordm_date,t1.ordm_ornm,t1.ordm_amnt,
                        IFNULL(t5.INV_AMNT,0)challan_amnt,IFNULL(t5.DELV_AMNT,0)deli_amnt,t1.ordm_drdt, DAYNAME(t1.ordm_drdt) day_name,t9.lfcl_name
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_dlrm t3 ON t1.dlrm_id=t3.id
                        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
                        INNER JOIN tm_site t6 ON t1.site_id=t6.id
                        INNER JOIN tm_zone t7 ON t4.zone_id=t7.id
                        INNER JOIN tm_dirg t8 ON t7.dirg_id=t8.id
                        INNER JOIN tm_lfcl t9 ON t1.lfcl_id=t9.id
                        LEFT JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                        WHERE t1.ordm_date BETWEEN '$start_date' AND '$end_date' " .$q1.$q2. "
                        GROUP BY t8.dirg_name,t7.zone_name,t4.aemp_usnm,t4.aemp_name,
                        t6.site_code,t6.site_name,t1.ordm_date,t1.ordm_ornm,t1.ordm_amnt,t1.ordm_drdt,t9.lfcl_name
                        ;");
                return $data;
                break;
            case "sr_wise_order_delivery":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id='$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                $data=DB::connection($this->db)->select("SELECT '$acmp_name' acmp_name,'$slgp_name' slgp_name,
                        t5.`aemp_name`,
                        t5.`aemp_usnm`,
                        t5.`aemp_mob1`,
                        t1.ordm_date,
                        t6.zone_name,
                        ROUND(SUM(t2.ordd_oamt)/1000,2) AS ordd_amt,
                        ROUND(SUM(t2.ordd_odat)/1000,2) AS deli_amt
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,
                        tm_aemp as t5 ,tm_zone as t6
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between '$start_date' AND '$end_date')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t5.`aemp_name`,
                            t5.`aemp_usnm`,
                            t5.`aemp_mob1`,
                            t1.ordm_date,
                            t6.zone_name");
                $result=collect($data);
                $grand_total=$result->pipe(function($result){
                    return collect([
                        't_order'=>round($result->sum('ordd_amt'),2),
                        't_deli'=>round($result->sum('deli_amt'),2),
                    ]);                       
                });
                return array(
                    'data'=>$data,
                    'gt'=>$grand_total,
                );
                break;
            case "zone_wise_order_delivery_summary":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t5.zone_id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t5.zone_id='$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }
                // $data=DB::connection($this->db)->select("SELECT t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`order_amount`) as ordd_oamt,
                //         sum(t1.`Delivery_Amount`) as ordd_Amnt FROM `s_mktm_zone_group_wise_order_vs_delivery` t1
                //         WHERE t1.ordm_date  BETWEEN '$start_date' AND '$end_date'". $q1. $zone_query . "AND t1.slgp_id=$sales_group_id
                //         group by t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`");
                $data=DB::connection($this->db)->select("SELECT '$acmp_name' acmp_name,'$slgp_name' slgp_name,
                        t1.ordm_date,
                        t6.zone_name,
                        t6.zone_code,
                        ROUND(SUM(t2.ordd_oamt)/1000,2) AS ordd_oamt,
                        ROUND(SUM(t2.ordd_odat)/1000,2) AS deli_amt
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,
                        tm_aemp as t5 ,tm_zone as t6
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between '$start_date' AND '$end_date')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query."  and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by   t1.ordm_date,t6.zone_code,
                            t6.zone_name");
                $result=collect($data);
                $grand_total=$result->pipe(function($result){
                    return collect([
                        't_order'=>round($result->sum('ordd_oamt'),2),
                        't_deli'=>round($result->sum('deli_amt'),2),
                    ]);                       
                });
                return array(
                    'data'=>$data,
                    'gt'=>$grand_total,
                );
                break;
            case "sku_wise_order_delivery":
                $zone_query='';
                if($dirg_id !=""){
                    $zone_query=" AND t6.id IN (".implode(',',$zone_list).")";
                }
                if($zone_id !=''){
                    $zone_query="AND t6.id='$zone_id'";
                }
                if ($sales_group_id != "") {
                    $q2 = " AND t1.slgp_id = '$sales_group_id' ";
                }  
                $data=DB::connection($this->db)->select("SELECT '$acmp_name' acmp_name,'$slgp_name' slgp_name,
                        t5.`aemp_name`,
                        t5.`aemp_usnm`,
                        t5.`aemp_mob1`,
                        t4.`amim_code`,
                        t4.`amim_name`,
                        t1.ordm_date,
                        t6.zone_name,
                        ROUND(SUM(t2.ordd_oamt)/1000,2) AS ordd_amt,
                        ROUND(SUM(t2.ordd_odat)/1000,2) AS deli_amt
                        FROM `tt_ordm` as t1,`tt_ordd`as t2,tm_amim as t4,
                        tm_aemp as t5 ,tm_zone as t6
                        where t1.id between $min_id and $max_id
                        and (t1.ordm_date between '$start_date' AND '$end_date')
                                    and t1.id=t2.ordm_id 
                        ".$q2.$zone_query." and t2.amim_id=t4.id and t1.aemp_id=t5.id  and t5.zone_id=t6.id 
                        group by t5.`aemp_name`,
                        t5.`aemp_usnm`,
                        t5.`aemp_mob1`,
                        t4.`amim_code`,
                        t4.`amim_name`,
                        t1.ordm_date,
                        t6.zone_name");
                $result=collect($data);
                $grand_total=$result->pipe(function($result){
                    return collect([
                        't_order'=>round($result->sum('ordd_amt'),2),
                        't_deli'=>round($result->sum('deli_amt'),2),
                    ]);                       
                });
                return array(
                    'data'=>$data,
                    'gt'=>$grand_total,
                );
                break;
            default:
            return 1;
            break;
        }

    }

// All report section end here

    public function classWiseReport(Request $request)
    {
        $empId='72024';
        $market_id='8400';
        $than_id='';
        $sales_group_id='33';
        $start_date= '2021-11-25';
        $end_date= '2021-11-25';
        $zone_id='';
        $sales_group_id='33';
        $q1='';
        $q2='';
        if ($zone_id != "") {

            $q1 = " AND t1.zone_id = '$zone_id' ";
        }
        if ($sales_group_id != "") {

            $q2 = " AND t1.slgp_id = '$sales_group_id' ";
        }
        $acmp_id='7';


        $temp_s_list = "SELECT
                          t1.date,
                          t1.zone_id,
                          concat(t2.slgp_code, ' - ', t2.slgp_name)   AS slgp_name,
                          concat(t3.zone_code, ' - ', t3.zone_name)   AS zone_name,
                          concat(t3.dirg_code, ' - ', t3.dirg_name)   AS dirg_name,
                          t1.slgp_id                                  AS slgp_id,
                          t1.aemp_code                                AS aemp_id,
                          t1.aemp_name                                AS aemp_name,
                          t1.aemp_mobile                              AS aemp_mobile,
                          t1.inTime                                   AS inTime
                        FROM `tt_aemp_summary1` t1
                          INNER JOIN user_group_permission t2 ON t1.slgp_id = t2.slgp_id
                          INNER JOIN user_area_permission t3 ON t1.zone_id = t3.zone_id
                        WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t2.aemp_id = '$empId' AND (t1.t_amnt='0' OR t1.t_amnt is NULL) AND 
                              t3.aemp_id = '$empId' AND t1.atten_atyp='1' " . $q2 . $q1 . " and t2.acmp_id = '$acmp_id'
                        GROUP BY t1.zone_id, t1.slgp_id, t1.date, t1.aemp_id
                        ORDER BY t3.zone_code, t2.slgp_code";
        echo $temp_s_list;


    }
    // public function getTrackingRecordGvt(){
    //     $date=date('Y-m-d');
    //     $empId = $this->currentUser->employee()->id;
    //     $slgp = DB::connection($this->db)->select("SELECT DISTINCT slgp_id,slgp_name FROM `user_group_permission` WHERE `aemp_id`='$empId'  order by slgp_id");
    //     $slgp_id=[];
    //     $temp_v='';
    //     $q2='';
    //     $q1='';
    //     for($i=0;$i<count($slgp);$i++){
    //         $slgp_id[$i]=$slgp[$i]->slgp_id;
    //         $temp_v=$slgp_id[$i];
    //         $slgp_name=$slgp[$i]->slgp_name;
    //         $q2.=",sum(if(t1.slgp='$temp_v',t_visited,0))`slgpv$i`,sum(if ( t1.slgp='$temp_v',t_memo,0))`slgpm$i`";         
    //         $q1.=",sum(slgpv$i) as `slgpv$i`,sum(slgpm$i) as`slgpm$i`";
    //     }

    //     $data=DB::connection($this->db)->select("select tt.dsct_id,dsct_name,tl.TOTL".$q1." from(
    //         SELECT dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name".$q2."
    //         FROM s_mktm_zone_group_wise_vist_memo as t1 where t1.slgp IN (".implode(',',$slgp_id).")
    //         group by dsct_id,dsct_name,t1.than_id,t1.than_name,t1.mktm_id,mktm_name
    //         ) as tt,
    //         (select dsct_id, sum(TOTL) totl from(
    //         select dsct_id, mktm_id,  MAX(t_outlet) TOTL FROM s_mktm_zone_group_wise_vist_memo GROUP BY mktm_id,dsct_id) as tl
    //         group by dsct_id) AS tl
    //         WHERE tt.dsct_id=tl.dsct_id
    //         group by tt.dsct_id,dsct_name,tl.TOTL ORDER BY dsct_name ASC
    //     "); 
    //     return array('data'=>$data,'slgp'=>$slgp);

    // }

    public function getTrackingRecordGvt(Request $request){
        $slgp_id=$request->slgp_id;
        $acmp_id=$request->acmp_id;
        $time_period=$request->time_period;
        $stage=$request->stage;
        $q1='';
        $q2='';
        $start_date='';
        $end_date='';
        $emp_id=Auth::user()->employee()->id;
        $this->single_date=$request->start_date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $slgp='';
        if($slgp_id==0){
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND  acmp_id=$acmp_id  ORDER BY slgp_name ASC");
        }
        else{
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND slgp_id=$slgp_id  ORDER BY slgp_name ASC");
        }
        $slgp_ids=[];
        $q3='';
        for($i=0;$i<count($slgp);$i++){
            $slgp_id=$slgp[$i]->id;
            $slgp_ids[$i]=$slgp[$i]->id;
            $slgp_name=$slgp[$i]->slgp_name;
            $q2.=",sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.visit ELSE 0 END)`v$slgp_id`,sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.ro_visit ELSE 0 END)`rv$slgp_id`,
                    sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.memo ELSE 0 END)`m$slgp_id`,sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.ord_amount ELSE 0 END) `o$slgp_id`,
                    sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.delivery_amount ELSE 0 END)`d$slgp_id`
                    ";
           // $q1.=",sum(solt$slgp_id) as `solt$slgp_id`,sum(v$slgp_id) as `v$slgp_id`,sum(rv$slgp_id) as `rv$slgp_id`,sum(m$slgp_id) as `m$slgp_id`,sum(o$slgp_id) as `o$slgp_id`,sum(d$slgp_id) as `d$slgp_id`";
           $q1.=", max(`solt$slgp_id`) as `solt$slgp_id`,`v$slgp_id`,`rv$slgp_id`,`m$slgp_id`,`o$slgp_id`/1000 as `o$slgp_id`,`d$slgp_id`/1000 as `d$slgp_id`";
           $q3.=",SUM(CASE WHEN t1.slgp_id=$slgp_id THEN slgp_out ELSE 0 END)`solt$slgp_id`";
            

        }
        $data=DB::connection($this->db)->select("SELECT p2.disn_id,p.disn_name,p2.total_outlet ".$q1." FROM 
            (
                SELECT t2.disn_id,t2.disn_name ".$q2."
                FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).")
                    GROUP BY t2.disn_id,t2.disn_name ORDER BY t2.disn_name asc

            )p,
            (
            SELECT t4.id disn_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
            INNER JOIN tm_dsct t3 On t2.dsct_id=t3.id INNER JOIN tm_disn t4 ON t3.disn_id=t4.id where t1.slgp_id IN (".implode(',',$slgp_ids).") group by t4.id,t1.slgp_id
            )stt,
            (SELECT t2.disn_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1 
            INNER JOIN tm_dsct t2 ON t1.dsct_id=t2.id  group by t2.disn_id)p2
            WHERE p.disn_id=p2.disn_id AND stt.disn_id=p.disn_id
            GROUP BY p2.disn_id,p.disn_name,p2.total_outlet
                "); 


        return array('data'=>$data,'slgp'=>$slgp);

    }
  

    public function getGvtDeeperSalesData(Request $request){
        $stage=$request->stage;
        $id=$request->id;
        $slgp_id=$request->slgp_id;
        $acmp_id=$request->acmp_id;
        $time_period=$request->time_period;
        $emp_id=Auth::user()->employee()->id;
        $q2='';
        $q1='';
        $start_date='';
        $end_date='';
        // $slgp=DB::connection($this->db)->select("Select id,slgp_name from tm_slgp where id=$slgp_id");
        // $q2.=",sum(if(t2.slgp_id=$slgp_id,t2.visit,0))slgpv,sum(if(t2.slgp_id=$slgp_id,t2.memo,0))slgpm";
        $this->single_date=$request->start_date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $slgp='';
        if($slgp_id==0){
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND  acmp_id=$acmp_id  ORDER BY slgp_name ASC");
        }
        else{
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND slgp_id=$slgp_id  ORDER BY slgp_name ASC");
        }
        $slgp_ids=[];
        $q3='';
        for($i=0;$i<count($slgp);$i++){
            $slgp_id=$slgp[$i]->id;
            $slgp_ids[$i]=$slgp[$i]->id;
            $slgp_name=$slgp[$i]->slgp_name;
            $q2.=",sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.visit ELSE 0 END)`v$slgp_id`,sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.ro_visit ELSE 0 END)`rv$slgp_id`,
                    sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.memo ELSE 0 END)`m$slgp_id`,sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.ord_amount ELSE 0 END)`o$slgp_id`,
                    sum(CASE WHEN t2.slgp_id=$slgp_id THEN t2.delivery_amount ELSE 0 END)`d$slgp_id`";
            $q1.=", max(`solt$slgp_id`) as `solt$slgp_id`,`v$slgp_id`,`rv$slgp_id`,`m$slgp_id`,`o$slgp_id`/1000 as `o$slgp_id`,`d$slgp_id`/1000 as `d$slgp_id`";
            $q3.=",SUM(CASE WHEN t1.slgp_id=$slgp_id THEN slgp_out ELSE 0 END)`solt$slgp_id`";
            

        }
        switch($stage){
            case -1:
                $data=DB::connection($this->db)->select("SELECT p2.disn_id,p.disn_name,p2.total_outlet ".$q1." FROM 
                        (
                            SELECT t2.disn_id,t2.disn_name ".$q2."
                            FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).")
                                GROUP BY t2.disn_id,t2.disn_name ORDER BY t2.disn_name asc
            
                        )p,
                        (
                        SELECT t4.id disn_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
                        INNER JOIN tm_dsct t3 On t2.dsct_id=t3.id INNER JOIN tm_disn t4 ON t3.disn_id=t4.id where t1.slgp_id IN (".implode(',',$slgp_ids).") group by t4.id,t1.slgp_id
                        )stt,
                        (SELECT t2.disn_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1 
                        INNER JOIN tm_dsct t2 ON t1.dsct_id=t2.id  group by t2.disn_id)p2
                        WHERE p.disn_id=p2.disn_id AND stt.disn_id=p.disn_id
                        GROUP BY p2.disn_id,p.disn_name,p2.total_outlet ");
                    break;
            case 0:
                
                $data=DB::connection($this->db)->select("SELECT p2.dsct_id,p.dsct_name,p2.total_outlet ".$q1." FROM 
                (
                    SELECT t2.dsct_id,t2.dsct_name ".$q2."
                    FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).") AND t2.disn_id=$id
                        GROUP BY t2.dsct_id,t2.dsct_name ORDER BY t2.dsct_name asc
    
                )p,
                (
                SELECT t1.slgp_id,t3.id dsct_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
                INNER JOIN tm_dsct t3 On t2.dsct_id=t3.id INNER JOIN tm_disn t4 ON t3.disn_id=t4.id where t1.slgp_id IN (".implode(',',$slgp_ids).") AND t4.id=$id group by t3.id,t1.slgp_id
                )stt,
                (SELECT t2.id dsct_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1 
                INNER JOIN tm_dsct t2 ON t1.dsct_id=t2.id  group by t2.id)p2
                WHERE p.dsct_id=p2.dsct_id AND stt.dsct_id=p.dsct_id
                GROUP BY p2.dsct_id,p.dsct_name,p2.total_outlet
                    ");
                break;
            case 1:
                $data=DB::connection($this->db)->select("SELECT p2.than_id,p.than_name,p2.total_outlet ".$q1." FROM 
                (
                    SELECT t2.than_id,t2.than_name ".$q2."
                    FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).") AND t2.dsct_id=$id
                        GROUP BY t2.than_id,t2.than_name ORDER BY t2.than_name asc
    
                )p,
                (
                SELECT t2.than_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
                 where t1.slgp_id IN (".implode(',',$slgp_ids).") AND t2.dsct_id=$id group by t2.than_id,t1.slgp_id
                )stt,
                (SELECT t1.than_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1  group by t1.than_id)p2
                WHERE p.than_id=p2.than_id AND stt.than_id=p.than_id
                GROUP BY p2.than_id,p.than_name,p2.total_outlet");
                    break;
            case 2:
                $data=DB::connection($this->db)->select("SELECT p2.ward_id,p.ward_name,p2.total_outlet ".$q1." FROM 
                (
                    SELECT t2.ward_id,t2.ward_name ".$q2."
                    FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).") AND t2.than_id=$id
                        GROUP BY t2.ward_id,t2.ward_name ORDER BY t2.ward_name asc
    
                )p,
                (
                SELECT t2.ward_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
                 where t1.slgp_id IN (".implode(',',$slgp_ids).") AND t2.than_id=$id group by t2.ward_id,t1.slgp_id
                )stt,
                (SELECT t1.ward_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1  group by t1.ward_id)p2
                WHERE p.ward_id=p2.ward_id AND stt.ward_id=p.ward_id
                GROUP BY p2.ward_id,p.ward_name,p2.total_outlet
                        ");
                    break;
            case 3:
                $data=DB::connection($this->db)->select("SELECT p2.mktm_id,p.mktm_name,p2.total_outlet ".$q1." FROM 
                (
                    SELECT t2.mktm_id,t2.mktm_name ".$q2."
                    FROM tbl_slgp_vsmo1 t2 WHERE ssvh_date between '$start_date' AND '$end_date' AND t2.slgp_id in (".implode(',',$slgp_ids).") AND t2.ward_id=$id
                        GROUP BY t2.mktm_id,t2.mktm_name ORDER BY t2.mktm_name asc
    
                )p,
                (
                SELECT t2.mktm_id ".$q3." FROM tbl_mktm_slgp_wise_outlet t1 INNER JOIN tbl_mktm_wise_total_outlet t2 ON t1.mktm_id=t2.mktm_id
                 where t1.slgp_id IN (".implode(',',$slgp_ids).") AND t2.ward_id=$id group by t2.mktm_id,t1.slgp_id
                )stt,
                (SELECT t1.mktm_id,sum(total_outlet)total_outlet FROM tbl_mktm_wise_total_outlet t1  group by t1.mktm_id)p2
                WHERE p.mktm_id=p2.mktm_id AND stt.mktm_id=p.mktm_id
                GROUP BY p2.mktm_id,p.mktm_name,p2.total_outlet
                    ");
                break;
            case 4:
                $data="";
                break;
        }   
                return array('data'=>$data,
                            'slgp'=>$slgp);
        

    }
    public function getDeviationData(){
        $date=date('Y-m-d');
        $emp_id=Auth::user()->employee()->id;
        $sql="SELECT t1.acmp_id,t1.acmp_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t1.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t1.min_outlet AND t1.site<=t1.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t1.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM(
            SELECT t1.rout_id,t1.aemp_id,t1.acmp_id,t1.acmp_name,t1.site,t2.min_outlet,t2.max_outlet
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.acmp_id=t3.acmp_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE t3.aemp_id=$emp_id AND t4.lfcl_id=1
            GROUP BY t1.rout_id,t1.aemp_id,t1.acmp_id,t1.acmp_name,t1.site,t2.min_outlet,t2.max_outlet)t1 GROUP BY t1.acmp_id,t1.acmp_name ORDER BY t1.acmp_name ASC";
       // return $sql;
        $data = DB::connection($this->db)->select(DB::raw($sql));
        return $data;
    }
    public function getDeviationDeeperData(Request $request){
        $date=date('Y-m-d');
        $id=$request->id;
        $emp_id=Auth::user()->employee()->id;
        $stage=$request->stage;
        if($stage==0){

        }
        else if($stage==1){
            $date=$request->date;
            $sql="SELECT t1.slgp_id,t1.slgp_name,COUNT(t1.rout_id) t_rout,count( DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t1.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t1.min_outlet AND t1.site<=t1.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t1.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM(
            SELECT t1.rout_id,t1.aemp_id,t1.slgp_id,t1.slgp_name,t1.site,t2.min_outlet,t2.max_outlet
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.slgp_id=t3.slgp_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE t3.acmp_id=$id AND t3.aemp_id=$emp_id AND t4.lfcl_id=1
            GROUP BY t1.rout_id,t1.aemp_id,t1.slgp_id,t1.slgp_name,t1.site,t2.min_outlet,t2.max_outlet)t1 GROUP BY t1.slgp_id,t1.slgp_name ORDER BY t1.slgp_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==2){
            $date=$request->date;
            $sql="SELECT t1.zone_id,t1.zone_name,t1.slgp_id,COUNT(t1.rout_id) t_rout,count( DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t1.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t1.min_outlet AND t1.site<=t1.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t1.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM(
            SELECT t1.rout_id,t1.aemp_id,t1.zone_id,t1.zone_name,t1.site,t2.min_outlet,t2.max_outlet,t1.slgp_id
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE t1.slgp_id='$id' AND t3.aemp_id=$emp_id AND t4.lfcl_id=1
            GROUP BY t1.rout_id,t1.aemp_id,t1.zone_id,t1.zone_name,t1.site,t2.min_outlet,t2.max_outlet,t1.slgp_id)t1 GROUP BY t1.zone_id,t1.zone_name,t1.slgp_id ORDER BY t1.zone_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==3){
            $date=$request->date;
            $slgp_id=$request->slgp_id;
            $sql="SELECT t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t1.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t1.min_outlet AND t1.site<=t1.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t1.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM (
            SELECT t1.rout_id,t1.aemp_id,t3.aemp_usnm,t3.aemp_name,t1.site,t2.min_outlet,t2.max_outlet,t3.aemp_mob1
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN tm_aemp t3 ON t3.id=t1.aemp_id 
            WHERE  t1.zone_id='$id' AND t1.slgp_id='$slgp_id' AND t3.lfcl_id=1
            GROUP BY t1.rout_id,t1.aemp_id,t3.aemp_usnm,t3.aemp_name,t1.site,t2.min_outlet,t2.max_outlet,t3.aemp_mob1)t1 GROUP BY  t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1 ORDER BY t1.aemp_name ASC";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
    }
    public function getDateWiseDeviationData(Request $request,$stage=0){
        $date=$request->date;
        $id=$request->id;
        $emp_id=Auth::user()->employee()->id;
        if($stage==0){
            $sql="SELECT t1.acmp_id,t1.acmp_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_group_permission t3 ON t1.slgp_id=t3.slgp_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE  t3.aemp_id=$emp_id AND t4.lfcl_id=1
            GROUP BY t1.acmp_id,t1.acmp_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==1){
            $date=$request->date;
            $sql="SELECT t1.slgp_id,t1.slgp_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE  t1.acmp_id='$id' AND t4.lfcl_id=1
            GROUP BY t1.slgp_id,t1.slgp_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
        else if($stage==2){
            $date=$request->date;
            $sql="SELECT t1.zone_id,t1.zone_name,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
            INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
            WHERE t1.slgp_id='$id' AND t3.aemp_id=$emp_id AND t4.lfcl_id=1
            GROUP BY t1.zone_id,t1.zone_name";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;

        }
        else if($stage==3){
            $date=$request->date;
            $sql="SELECT t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1,COUNT(t1.rout_id) t_rout,count(DISTINCT t1.aemp_id)t_sr,
            SUM(CASE WHEN t1.site<t2.min_outlet THEN 1 ELSE 0 END)'underflow',
            SUM(CASE WHEN t1.site >=t2.min_outlet AND t1.site<=t2.max_outlet THEN 1 ELSE 0 END)'between_limit',
            SUM(CASE WHEN t1.site>t2.max_outlet THEN 1 ELSE 0 END)'overflow'
            FROM `tbl_rout_site_count` t1
            INNER JOIN tbld_emp_tracking_policies t2 ON t1.slgp_id=t2.slgp_id
            INNER JOIN tm_aemp t3 ON t3.id=t1.aemp_id 
            WHERE  t1.zone_id='$id' AND t3.lfcl_id=1
            GROUP BY t3.aemp_name,t3.aemp_usnm,t3.aemp_mob1";
            $data = DB::connection($this->db)->select(DB::raw($sql));
            return $data;
        }
    }
    public function getCompany(Request $request){
        $emid=Auth::user()->employee()->id;
        $acmp_list=DB::connection($this->db)->select("Select acmp_id,acmp_name FROM user_group_permission where aemp_id='$emid' GROUP BY acmp_id order by acmp_name");
        return $acmp_list;
    }
    public function getDeviationOutletVisit(Request $request,$stage=0){
        $emp_id=Auth::user()->employee()->id;
        $date=$request->date;
        if($date==''){
            $date=date('Y-m-d');
        }
        if($stage==0){
            $data=DB::connection($this->db)->select("SELECT t3.acmp_id as slgp_id,t3.acmp_name as slgp_name,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t3.aemp_id=$emp_id
            GROUP BY t3.acmp_id,t3.acmp_name order by t3.acmp_name asc");
        }
        else if($stage==1){
            $id=$request->id;
            $data=DB::connection($this->db)->select("SELECT t3.slgp_id as slgp_id,t3.slgp_name as slgp_name,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t3.aemp_id=$emp_id AND t3.acmp_id=$id
            GROUP BY t3.slgp_id,t3.slgp_name order by t3.acmp_name asc");
        }
        else if($stage==2){
            $id=$request->id;
            $data=DB::connection($this->db)->select("SELECT t4.zone_id zone_id,t4.zone_name zone_name,t3.slgp_id,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN user_group_permission t3 ON t2.slgp_id=t3.slgp_id
            INNER JOIN user_area_permission t4 ON t2.zone_id=t4.zone_id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t2.slgp_id='$id' AND t3.aemp_id=$emp_id AND t4.aemp_id=$emp_id
            GROUP BY t4.zone_id,t4.zone_name,t3.slgp_id ORDER BY t4.zone_name asc");
        }
        else if($stage==3){
            $id=$request->id;
            $slgp_id=$request->slgp_id;
            $data=DB::connection($this->db)->select("SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,SUM(t1.dhbd_ucnt)t_sr,
            sum(t1.dhbd_tsit)t_outlet,sum(t1.dhbd_tvit) t_visit
            FROM th_dhbd_5 t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_slgp t3 on t3.id=t2.slgp_id
            INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
            WHERE  t1.role_id=1  AND  t1.dhbd_date='$date' AND t4.id='$id' AND t2.slgp_id='$slgp_id'
            GROUP BY t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 ORDER BY t2.aemp_name ASC");
        }
        return $data;
        //return $stage;
    }
    public function getNoteTaskReport(){
        $date=date('Y-m-d');
        $emid=Auth::user()->employee()->id;
        $data=DB::connection($this->db)->select("SELECT 
            t1.id,t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1,ifnull(t2.t_note,0)t_note,
            (select ifnull(count(t1.id),0) FROM tl_nasn t1 WHERE t1.aemp_id=t1.id and enmp_date='$date')assign_task,
            (select ifnull(sum(u_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')tm_task
            FROM tm_aemp t1
            LEFT JOIN (SELECT t_note,aemp_id FROM tbl_note_count WHERE note_date='$date')t2 ON t1.id=t2.aemp_id
            WHERE t1.lfcl_id=1 AND t1.id in(  SELECT
                t1.id from tm_aemp t1 WHERE t1.aemp_mngr=$emid
                UNION ALL
                SELECT
                t2.dhem_peid FROM th_dhem t2
                WHERE t2.dhem_emid =$emid) ORDER BY t1.aemp_name ASC");
        return $data;
    }
    public function getUserAndDateWiseTaskNote(Request $request){
        $emid=$request->emid;
        if($emid==''){
            $emid=Auth::user()->employee()->id;
        }
        //(select ifnull(sum(t_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')assign_task,
        $date=$request->date;
        $data=DB::connection($this->db)->select("SELECT t1.id,t1.aemp_name,t1.aemp_usnm,t1.aemp_mob1,ifnull(t2.t_note,0)t_note,
        (select ifnull(count(t1.id),0) FROM tl_nasn t1 WHERE t1.aemp_id=t1.id and enmp_date='$date')assign_task,
        (select ifnull(sum(u_note),0) FROM tbl_note_count WHERE aemp_mngr=t1.id and note_date='$date')tm_task
        FROM tm_aemp t1
        LEFT JOIN (SELECT t_note,aemp_id FROM tbl_note_count WHERE note_date='$date')t2 ON t1.id=t2.aemp_id
        WHERE t1.lfcl_id=1 AND t1.id in(SELECT
            t1.id from tm_aemp t1 WHERE t1.aemp_mngr=$emid
            UNION ALL
            SELECT
            t2.dhem_peid FROM th_dhem t2 WHERE t2.dhem_emid =$emid) ORDER BY t1.aemp_name ASC
        ");
        return $data;
    }
    public function getTaskDetails($id,$date){
        $data=DB::connection($this->db)->select("SELECT 
            t1.note_titl,t1.note_body,t1.note_dtim,t1.note_date,t2.nimg_imag,t3.site_name
            FROM tt_note t1
            LEFT JOIN tl_nimg t2 ON t1.id=t2.note_id
            LEFT JOIN tm_site t3 ON t1.site_code=t3.site_code
            WHERE t1.aemp_id=$id AND t1.note_date='$date' ORDER BY t1.note_dtim DESC");
        return view('report.summary_report.note_task_details')->with('data',$data);
    }
    public function getLineNoteDetails($id){
        $data=DB::connection($this->db)->select("Select id,nimg_imag FROM tl_nimg Where note_id=$id ORDER BY id desc");
        return $data;
       // return view('report.summary_report.note_task_details')->with('data',$data);
    }
    public function getSrMovementSummary(){
        $emp_id=Auth::user()->employee()->id;
        $data=DB::connection($this->db)->select("SELECT t1.acmp_id,t1.acmp_name,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
        SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
        FROM tbl_sr_activity_summary t1
        INNER JOIN tm_aemp t3 On t1.id=t3.id
        INNER JOIN user_group_permission t2 ON t3.slgp_id=t2.slgp_id
        WHERE t1.act_date=curdate()-INTERVAL 1 DAY AND t2.aemp_id='$emp_id' and t3.role_id=1
        GROUP BY t1.acmp_id,t1.acmp_name ORDER BY t1.acmp_name ASC");
        return $data;
    }
    public function getSrMovementSummaryDeeparData(Request $request){
        $id=$request->id;
        $stage=$request->stage;
       // $date=Carbon::yesterday()->format('Y-m-d');
       $emp_id=Auth::user()->employee()->id;
        $date=Carbon::now()->subDays(1)->format('Y-m-d');
       $data='';
        if($stage==1){
            $data=DB::connection($this->db)->select("SELECT t1.slgp_id,t1.slgp_name,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
            SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
                FROM tbl_sr_activity_summary t1
                INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                WHERE t1.act_date='$date' AND t1.acmp_id=$id AND t2.aemp_id=$emp_id
                GROUP BY t1.slgp_id,t1.slgp_name ORDER BY t1.slgp_name ASC;");
        }
        //start from here next day
        else if($stage==2){
            $data=DB::connection($this->db)->select("SELECT t1.zone_id,t1.zone_name,t1.slgp_id,SUM(t1.9am+t1.10am+t1.11am+t1.12pm+t1.9amn+t1.10amn+t1.11amn+t1.12pmn)1st_slot,SUM(t1.1pm+t1.1pmn)2nd_slot,
            SUM(t1.2pm+t1.3pm+t1.4pm+t1.5pm+t1.6pm+t1.2pmn+t1.3pmn+t1.4pmn+t1.5pmn+t1.6pmn)3rd_slot,SUM(t1.7pm+t1.8pm+t1.9pm+t1.7pmn+t1.8pmn+t1.9pmn)4th_slot
                FROM tbl_sr_activity_summary t1
                INNER JOIN user_group_permission t2 ON t1.slgp_id=t2.slgp_id
                INNER JOIN user_area_permission t3 ON t1.zone_id=t3.zone_id
                WHERE act_date='$date' AND t1.slgp_id=$id AND t2.aemp_id=$emp_id AND t3.aemp_id=$emp_id
                GROUP BY t1.zone_id,t1.zone_name ORDER BY t1.zone_name ASC;");
        }
        else if($stage==3){
            $slgp_id=$request->slgp_id;
            $data=DB::connection($this->db)->select("SELECT aemp_usnm,aemp_name,SUM(9am+10am+11am+12pm+9amn+10amn+11amn+12pmn)1st_slot,SUM(1pm+1pmn)2nd_slot,
                SUM(2pm+3pm+4pm+5pm+6pm+2pmn+3pmn+4pmn+5pmn+6pmn)3rd_slot,SUM(7pm+8pm+9pm+7pmn+8pmn+9pmn)4th_slot
                FROM tbl_sr_activity_summary
                WHERE act_date='$date' AND zone_id=$id AND slgp_id='$slgp_id'
                GROUP BY aemp_usnm,aemp_name ORDER BY aemp_name ASC;");
        }
        else{
            $data='Bad';
        }
        return $data;

    }
    public function getCatWiseOutVisit($id,$date){
        $time_period=$date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $this->db = Auth::user()->country()->cont_conn;
        $sr_list=DB::connection($this->db)->select("SELECT  
            t4.id,t4.otcg_name,
            sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
            FROM  th_ssvh t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_site t3 ON t1.site_id=t3.id
            INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
            WHERE t2.aemp_mngr=$id  AND t1.ssvh_date between '$start_date' AND '$end_date' AND t1.ssvh_ispd in (1,0)
            GROUP BY t4.id");
        return $sr_list;
    }
    public function getCatWiseOutNonVisit($id,$date){
        //return $id;
        $time_period=$date;
        $date=$this->getStartAndEndDate($time_period);
        $start_date=$date['start_date'];
        $end_date=$date['end_date'];
        $this->db = Auth::user()->country()->cont_conn;
        $sr_list=DB::connection($this->db)->select("SELECT  
            t4.id,t4.otcg_name,
            sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
            FROM  th_ssvh t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_site t3 ON t1.site_id=t3.id
            INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
            WHERE t2.aemp_mngr=$id  AND t1.ssvh_date between '$start_date' AND '$end_date' AND t1.ssvh_ispd in (1,0)
            GROUP BY t4.id");
        return $sr_list;
    }
    public function getVisitedOutletDetails($emid,$date,$cat_id){
        $time_period=$date;
        $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        if($time_period){ 
            if($time_period==0){
                $start_date= Carbon::now()->format('Y-m-d');
                $end_date= Carbon::now()->format('Y-m-d');
            }
            else if($time_period==1){
                $start_date= Carbon::yesterday()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==10){
                $start_date = Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
                $end_date = Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
            }
            else if($time_period==11){      
                $start_date=$dt->copy()->subDays(7)->format('Y-m-d');
                $end=$dt->copy()->subDays(7);
                $end_date=$end->addDays(6)->format('Y-m-d');
            }
            else if($time_period==12){
                $start_date=$dt->copy()->subDays(14)->format('Y-m-d');
                $end=$dt->copy()->subDays(14);
                $end_date=$end->addDays(6)->format('Y-m-d');
            }
            else if($time_period==5){
                $start_date= Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else{
                $start_date=$time_period;
                $end_date=$time_period;
            }   
        }else{
            $start_date= Carbon::now()->format('Y-m-d');
            $end_date= Carbon::now()->format('Y-m-d');
        }
        $out_list=DB::connection($this->db)->select("SELECT  t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t2.aemp_mngr=$emid  AND t1.ssvh_date  between '$start_date' AND '$end_date' AND t1.ssvh_ispd in (1,0) AND t4.id=$cat_id
                ORDER BY t3.site_name ASC");
        return $out_list;
    }
    public function getSRList(Request $request){
        // $id=$request->id;
        // if($request->type==1){
        //     $data = DB::connection($this->db)->select("SELECT `aemp_usnm`,`aemp_name`,`id` FROM `tm_aemp` WHERE  `slgp_id`='$id' AND lfcl_id='1' AND role_id='1' AND aemp_issl=1");
        // }else{
        //     $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.aemp_name,t1.id
        //     FROM tm_aemp t1
        //     INNER JOIN tl_srth t2 ON t1.id=t2.id
        //     WHERE t1.lfcl_id=1 AND t1.aemp_issl=1 AND t2.than_id=$id");
        // }
        $q1="";
        $q2="";
        $q3="";
        $q4="";
        $slgp_id=$request->slgp_id;
        $dist_id=$request->dist_id;
        $than_id=$request->than_id;
        $usr_type=$request->usr_type;
        if($slgp_id !=''){
            $q3=" AND t1.slgp_id=$slgp_id";
        }
        // if($usr_type !=''){
        //     if($usr_type==1){
        //         $q4=" AND t1.role_id=1";
        //     }else{
        //         $q4=" AND t1.role_id !=1";
        //         $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.id,t1.aemp_name FROM tm_aemp t1 Where t1.slgp_id=$slgp_id AND t1.lfcl_id=1 ORDER BY t1.aemp_name ASC ");
        //         return $data;
        //     }
        // }
        if($dist_id !=''){
            $q1=" AND t4.id=$dist_id";
        }
        if($than_id !=''){
            $q2=" AND t3.id=$than_id";
        }
        if($request->rpt_type=='activity_summary'){
            $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.id,t1.aemp_name from tm_aemp t1
                where t1.lfcl_id=1 AND t1.role_id>1 AND t1.role_id<=6 ". $q3. "
                GROUP BY t1.id,aemp_name,t1.aemp_usnm ORDER BY t1.role_id DESC");
            return $data;
        }
        $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.id,t1.aemp_name from tm_aemp t1
                LEFT JOIN tl_srth t2 ON t1.id=t2.aemp_id
                LEFT JOIN tm_than t3 ON t2.than_id=t3.id
                LEFT JOIN tm_dsct t4 ON t3.dsct_id=t4.id
                where t1.lfcl_id=1 ".$q3.$q4.$q2.$q1. "
                GROUP BY t1.id,aemp_name,t1.aemp_usnm");
        return $data;
         
    }
    public function getHistoryReportData(Request $request){
        $id=$request->sr_id;
        $sr_id_m=$request->sr_id_m;
        if($id=='' && $sr_id_m !=''){
            $sr_id_m=DB::connection($this->db)->select("Select id from tm_aemp where aemp_usnm='$sr_id_m'");
            $id=$sr_id_m?$sr_id_m[0]->id:'';
        }
        $time_period=$request->time_period;
        $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        $time_period=$request->time_period;
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        if($time_period !=''){
            $date=$this->getStartAndEndDate($time_period);
            $start_date=$date['start_date'];
            $end_date=$date['end_date'];
        }
        if($request->slgp_id=='' && $id ==''){
            return false;
        }
        $slgp_id=$request->slgp_id;
        switch ($request->reportType){
            case "sr_activity_sales_hierarchy":
                // if($request->usr_type==1){
                    $sr_query='';
                    $slgp_id=$request->slgp_id;
                    if($id){
                        $sr_query=" AND t1.aemp_id=$id";
                    }
                    if(!$id && $slgp_id){
                        $sr_query.=" AND t1.slgp_id=$slgp_id";
                    }
                    // $data=DB::connection($this->db)->select("SELECT 
                    //         t2.dhbd_date,t1.id,t1.aemp_usnm,t1.aemp_name,
                    //         ifnull(t4.rout_name,'N/A') rout_id,t2.dhbd_tsit rout_olt,
                    //         t2.dhbd_tvit t_visit,t2.dhbd_memo t_memo,count(t6.site_id) rout_visit,
                    //         format(ifnull((t2.dhbd_memo / t2.dhbd_tvit) * 100, 0), 2) AS strikeRate,
                    //         round(ifnull(t2.dhbd_line / t2.dhbd_memo,0), 2)                     AS lpc,
                    //         round(t2.dhbd_tamt/1000	,2)									 AS t_amnt
                    //         FROM tm_aemp  t1
                    //         INNER JOIN th_dhbd_5 t2 ON t2.aemp_id=t1.id
                    //         INNER JOIN tl_rpln t3 ON t1.id=t3.aemp_id AND t3.rpln_day=DAYNAME(t2.dhbd_date)
                    //         LEFT join tm_rout t4 ON t3.rout_id=t4.id
                    //         LEFT JOIN tl_rsmp t5 ON t4.id=t5.rout_id
                    //         LEFT JOIN (SELECT ssvh_date,site_id,aemp_id from th_ssvh where ssvh_date between'$start_date' 
                    //         AND '$end_date' and ssvh_ispd in (1,0) and aemp_id=$id group by ssvh_date,site_id,aemp_id)t6
                    //         ON t5.site_id=t6.site_id AND t2.dhbd_date=t6.ssvh_date
                    //         WHERE t1.id=$id AND t2.dhbd_date between'$start_date' AND '$end_date'
                    //         GROUP BY t2.dhbd_date,t1.id,t1.aemp_usnm,t1.aemp_name, t2.dhbd_tvit,t2.dhbd_memo,t4.rout_name,
                    //         t2.dhbd_tsit,t2.dhbd_line,t2.dhbd_tamt order by t2.dhbd_date ASC");
                    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                    $data=DB::connection($this->db)->select("SELECT 
                                t1.date dhbd_date,t1.aemp_id id,t1.aemp_code aemp_usnm,
                                t1.aemp_name,
                                ifnull(t1.rout_name,'N/A')rout_id,
                                t1.t_outlet rout_olt,
                                (t1.`v_outlet`+t1.`s_outet`) t_visit,
                                t1.`t_memo`,t1.ro_visit rout_visit,
                                ifnull(round(t1.t_memo*100/(t1.`v_outlet`+t1.`s_outet`),2),0) strikeRate,
                                ifnull(round(t1.t_sku/t1.t_memo,2),0) lpc,
                                round(t_amnt/1000,2) t_amnt,
                                t2.zone_name
                                FROM 
                                tt_aemp_summary1 t1
                                INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                                WHERE   t1.date between '$start_date' AND '$end_date' ". $sr_query.  "
                                GROUP BY t1.date,t1.aemp_id,t1.aemp_code,t1.rout_id,t1.t_outlet, t1.`t_memo`,t1.ro_visit,
                                t1.aemp_name,t1.rout_name 
                                ");
                // }else{
                //     $data=DB::connection($this->db)->select("select distinct hloc_date,id,aemp_usnm,aemp_name,than_name,dsct_name,  Total_note from(
                //         select distinct hloc_date,id,aemp_usnm,aemp_name,SUBSTRING(`geo_lat1`, 1, 5) geo_lat ,
                //         SUBSTRING(`geo_lon1`, 1, 3)  geo_lon,than_name,dsct_name, ifnull(total_note,0) Total_note,hloc_time  from 
                //         (
                //         SELECT distinct t1.hloc_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.hloc_time,
                //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                //         SUBSTRING(t1.`geo_lon`, 1, 3)  geo_lon1
                //         FROM `th_hloc` as t1 
                //         , tm_aemp as t2, tm_edsg as t3
                //         where t1.aemp_id=t2.id and t1.hloc_date between '$start_date' AND '$end_date' and t2.id=$id
                //         and t2.edsg_id=t3.id 
                //         ) as t77 inner join  (select distinct t6.dsct_name,t6.than_name,SUBSTRING(t6.`geo_lat`, 1, 5) geo_lat , SUBSTRING(t6.`geo_lon`, 1, 3) geo_lon from tm_ward_zone_lat_lon t6) as t78 
                //         on SUBSTRING(t77.geo_lat1, 1, 5)  =SUBSTRING(t78.geo_lat, 1, 5)  and SUBSTRING(t77.geo_lon1, 1, 3)  =SUBSTRING(t78.geo_lon, 1, 3) 
                //         left join (select note_date, aemp_id, count(*) total_note from tt_note,tm_aemp where tm_aemp.id= tt_note.aemp_id and tm_aemp.id =$id group by aemp_id,note_date) 
                //         t79 on t77.id= t79.aemp_id AND t79.note_date = t77.hloc_date
                //         order by  hloc_time ASC) df");
                        
                //     $data=DB::connection($this->db)->select("SELECT hloc_date,aemp_name,hloc_addr,round(avg(geo_lat),7)geo_lat,round(avg(geo_lon),7) geo_lon FROM
                //     (SELECT
                //     t1.hloc_date,t2.aemp_name,t1.hloc_addr,t1.geo_lat,t1.geo_lon,t1.hloc_time
                //     FROM th_hloc t1
                //     INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                //     WHERE t1.hloc_date between '$start_date' AND '$end_date' AND t1.aemp_id=$id
                //     ORDER BY t1.hloc_time ASC)p
                //     GROUP BY p.hloc_date,p.aemp_name,p.hloc_addr");
                // }
                break;
            case "sr_activity_gvt_hierarchy":
                $sr_query='';
                $slgp_id=$request->slgp_id;
                if($id){
                    $sr_query=" AND t1.aemp_id=$id";
                }
                if(!$id && $slgp_id){
                    $sr_query.=" AND t2.slgp_id=$slgp_id";
                }
                $data=DB::connection($this->db)->select("SELECT t2.aemp_name,t2.aemp_usnm,
                        t4.mktm_name,t5.ward_name,t6.than_name,t7.dsct_name,count(distinct t1.site_id)t_outlet
                        FROM `th_ssvh` t1 
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_site t3 ON t1.site_id=t3.id
                        INNER JOIN tm_mktm t4 ON t3.mktm_id=t4.id
                        INNER JOIN tm_ward t5 ON t4.ward_id=t5.id
                        INNER JOIN tm_than t6 ON t5.than_id=t6.id
                        INNER JOIN tm_dsct t7 ON t6.dsct_id=t7.id
                        where t1.ssvh_date BETWEEN '$start_date' AND '$end_date' AND t1.ssvh_ispd in (1,0)" . $sr_query.  "
                        GROUP BY t2.aemp_name,t2.aemp_usnm,t4.mktm_name,t5.ward_name,t6.than_name,t7.dsct_name");
                break;
            case "activity_summary":
                $weekend='Friday';
                if(Auth::user()->country()->id==14 || Auth::user()->country()->id==3){
                    $weekend='Sunday';
                }
                if($id==0){
                    $sv='';
                    $slgp=DB::connection($this->db)->select("SELECT t1.id from tm_aemp t1
                            where t1.lfcl_id=1 AND t1.role_id>1 AND t1.role_id<=6 AND t1.slgp_id=$slgp_id
                            GROUP BY t1.id,aemp_name,t1.aemp_usnm ORDER BY t1.role_id DESC");
                    for($i=0;$i<count($slgp);$i++){
                        if($i ==count($slgp)-1){
                            $sv .=$slgp[$i]->id;
                        }
                        else{
                            $sv .=$slgp[$i]->id .',';
                        }
                    }
                    
                    // $data=DB::connection($this->db)->select("select mnth,id ,aemp_usnm,aemp_name,max(attn_date) end_date,min(attn_date) start_date,minus_day,(wd_mnt.t_days-wd_mnt.minus_day)t_days,wd_mnt.ss_date,wd_mnt.ee_date, count(distinct attn_date) WorkindDay, 
                    //         count(distinct dsct_name) No_of_District ,count(distinct than_name) No_of_thana,
                    //         count(distinct ward_name) No_of_ward, ifnull(T_note,0)T_note,ifnull(Rvisit,0)Rvisit, ifnull(Otvisit,0)Otvisit from(
                    //         select distinct month(attn_date) mnth,id,attn_date,aemp_usnm,aemp_name,ward_name,than_name,dsct_name from(
                            
                    //         select distinct attn_date,t66.aemp_usnm,t66.aemp_name,SUBSTRING(t66.`geo_lat1`, 1, 5) geo_lat ,
                    //         SUBSTRING(t66.`geo_lon1`, 1, 3)  geo_lon,t66.id  ,t66.than_name,t66.dsct_name,t66.ward_name from 
                    //         (
                    //             SELECT distinct t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.attn_time,
                    //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                    //         SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,t6.ward_name
                    //         FROM `tt_attn` as t1 
                    //         left join tm_ward_ward_lat_lon t6  on
                    //         MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1), (t1.geo_lat - 9 / 111.1)) 
                    //         ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
                    //         , tm_aemp as t2, tm_edsg as t3
                    //         where t1.aemp_id=t2.id and t1.attn_date between '$start_date' AND '$end_date' and t2.id in (".$sv.")
                    //         and t2.edsg_id=t3.id ) as t66 
                    //         left join (SELECT distinct t1.note_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.note_dtim,
                    //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                    //         SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,t6.ward_name
                    //         FROM `tt_note` as t1 
                    //         left join tm_ward_ward_lat_lon t6  on
                    //         MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1), (t1.geo_lat - 9 / 111.1)) 
                    //         ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
                    //         , tm_aemp as t2, tm_edsg as t3
                    //         where t1.aemp_id=t2.id and t1.note_date between '$start_date' AND '$end_date' and t2.id in (".$sv.")
                    //         and t2.edsg_id=t3.id 
                    //         )as t77 on t66.id= t77.id AND t66.attn_date = t77.note_date
                    //         ) df
                    //         ) gr   
                    //         left join ( select month(dt) dt,aemp_id,sum(Total_note) T_note,sum( retailVisit) Rvisit,sum( OtherlVisit) Otvisit from(
                    //             select  note_date dt, aemp_id, count(*) Total_note, 
                    //             sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,
                    //             sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherlVisit
                    //             from tt_note,
                    //             tm_aemp where note_date between '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id in (".$sv.")
                    //             group by aemp_id,note_date
                    //         ) ntc
                    //         group by month(dt),aemp_id ) dg on dg.dt = gr.mnth  AND dg.aemp_id=gr.id
                    //         INNER JOIN 
                    //         (select  
                    //         MONTH(selected_date)mnth_t,count(selected_date) t_days,min(selected_date)ss_date,max(selected_date)ee_date,SUM(CASE WHEN DAYNAME(selected_date)='$weekend' THEN 1 ELSE 0 END)minus_day
                    //         from 
                    //         (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                    //         (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                    //         (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                    //         (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                    //         (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                    //         (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
                    //         where selected_date between '$start_date' AND '$end_date'
                    //         GROUP BY MONTH(selected_date)
                    //         ) wd_mnt ON mnth=wd_mnt.mnth_t
                    //         group by id,mnth,aemp_usnm,aemp_name ORDER BY aemp_usnm,mnth");
                    $data=DB::connection($this->db)->select("select mnth,id ,aemp_usnm,aemp_name,max(attn_date) end_date,min(attn_date) start_date,minus_day,(wd_mnt.t_days-wd_mnt.minus_day)t_days,wd_mnt.ss_date,wd_mnt.ee_date, count(distinct attn_date) WorkindDay, 
                    count(distinct dsct_name) No_of_District ,count(distinct than_name) No_of_thana,
                    count(distinct ward_name) No_of_ward, ifnull(T_note,0)T_note,ifnull(Rvisit,0)Rvisit, ifnull(Otvisit,0)Otvisit 
                    from(                               
                          select distinct month(attn_date) mnth,id,attn_date,aemp_usnm,aemp_name,ward_name,than_name,dsct_name 
                            from(        
                                    SELECT distinct t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.attn_time,
                                    SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,
                                    t6.than_name,t6.dsct_name,t6.ward_name
                                    FROM `tt_attn` as t1 
                                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                    INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                                    INNER join tm_ward_ward_lat_lon t6  on
                                    MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  point((t1.geo_lon - 1 / 111.1),
                                                                                                                                    (t1.geo_lat - 1 / 111.1)) 
                                    ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                                        and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                                      
                                        where t1.aemp_id  in (".$sv.") and t1.attn_date between '$start_date' AND '$end_date' 
                                    UNION ALL
                                    SELECT distinct t1.note_date attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.note_dtim attn_time,
                                        SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                                        SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,t6.ward_name
                                        FROM `tt_note` as t1
                                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                        INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                                        left join tm_ward_ward_lat_lon t6  on
                                        MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  
                                        point((t1.geo_lon - 1 / 111.1), 		(t1.geo_lat - 1 / 111.1)) 
                                        ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                                                   and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
                                        where t1.aemp_id  in (".$sv.") and t1.note_date between '$start_date' AND '$end_date' AND t1.site_code=''
                                    UNION ALL
                                    SELECT DISTINCT
                                        t1.note_date attn_date,
                                        t7.id,
                                        t7.aemp_usnm,
                                        t7.aemp_name,
                                        t8.edsg_name,
                                        t1.note_dtim attn_time,
                                        SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1,
                                        SUBSTRING(t1.`geo_lon`, 1, 4) geo_lon1,
                                        t9.than_name,
                                        t10.dsct_name,
                                        t4.ward_name
                                        FROM tt_note t1 
                                        INNER JOIN tm_site t2 ON t1.site_code=t2.site_code
                                        INNER JOIN tm_mktm t3 on t2.mktm_id=t3.id
                                        INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                                        INNER JOIN tm_than t9 ON t4.than_id=t9.id
                                        INNER JOIN tm_dsct t10 ON t9.dsct_id=t10.id
                                        INNER JOIN tm_aemp AS t7 ON t1.aemp_id=t7.id
                                        INNER JOIN tm_edsg AS t8 ON t7.edsg_id=t8.id
                                        WHERE t1.note_date between '$start_date' AND '$end_date' AND t1.aemp_id   in (".$sv.") AND t1.site_code !=''
                              ) gr )gr  
                              left join ( 
                                  select month(dt) dt,aemp_id,sum(Total_note) T_note,sum( retailVisit) Rvisit,sum( OtherlVisit) Otvisit from(
                                                    select  note_date dt, aemp_id, count(*) Total_note, 
                                                    sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,
                                                    sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherlVisit
                                                    from tt_note,
                                                    tm_aemp where note_date between '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id   in (".$sv.")
                                                    group by aemp_id,note_date
                                                ) ntc
                                                group by month(dt),aemp_id 
                              ) dg on dg.dt = gr.mnth  AND dg.aemp_id=gr.id
                               INNER JOIN 
                                    (select  
                                    MONTH(selected_date)mnth_t,count(selected_date) t_days,min(selected_date)ss_date,max(selected_date)ee_date,
                                     SUM(CASE WHEN DAYNAME(selected_date)='Friday' THEN 1 ELSE 0 END)minus_day
                                    from 
                                    (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                     select 7 union select 8 union select 9) t0,
                                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                     select 7 union select 8 union select 9) t1,
                                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                     select 7 union select 8 union select 9) t2,
                                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                     select 7 union select 8 union select 9) t3,
                                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                     select 7 union select 8 union select 9) t4) v
                                    where selected_date between '$start_date' AND '$end_date'
                                    GROUP BY MONTH(selected_date)
                                ) wd_mnt ON mnth=wd_mnt.mnth_t
                        group by id,mnth,aemp_usnm,aemp_name ORDER BY aemp_usnm,mnth;");
                    return array(
                        'data'=> $data,
                        'start_date'=>$start_date,
                        'end_date'=>$end_date
                        );
                        
                }

                $data=DB::connection($this->db)->select("select mnth,id ,aemp_usnm,aemp_name,max(attn_date) end_date,min(attn_date) start_date,minus_day,(wd_mnt.t_days-wd_mnt.minus_day)t_days,wd_mnt.ss_date,wd_mnt.ee_date, count(distinct attn_date) WorkindDay, 
                    count(distinct dsct_name) No_of_District ,count(distinct than_name) No_of_thana,
                    count(distinct ward_name) No_of_ward, ifnull(T_note,0)T_note,ifnull(Rvisit,0)Rvisit, ifnull(Otvisit,0)Otvisit 
                    from(                               
                      select distinct month(attn_date) mnth,id,attn_date,aemp_usnm,aemp_name,ward_name,than_name,dsct_name 
                        from(        
                                SELECT distinct t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.attn_time,
                                SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,
                                t6.than_name,t6.dsct_name,t6.ward_name
                                FROM `tt_attn` as t1 
                                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                                INNER join tm_ward_ward_lat_lon t6  on
                                    MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  point((t1.geo_lon - 1 / 111.1),
                                                                                                                                    (t1.geo_lat - 1 / 111.1)) 
                                    ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                                        and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                  
                
                                    where t1.aemp_id=$id and t1.attn_date between '$start_date' AND '$end_date' 
                                UNION ALL
                                SELECT distinct t1.note_date attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.note_dtim attn_time,
                                    SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                                    SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,t6.ward_name
                                    FROM `tt_note` as t1
                                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                    INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                                    left join tm_ward_ward_lat_lon t6  on
                                    MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  
                                    point((t1.geo_lon - 1 / 111.1), 		(t1.geo_lat - 1 / 111.1)) 
                                    ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                                               and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
                                    where t1.aemp_id=$id and t1.note_date between '$start_date' AND '$end_date' AND t1.site_code=''
                                UNION ALL
                                SELECT DISTINCT
                                    t1.note_date attn_date,
                                    t7.id,
                                    t7.aemp_usnm,
                                    t7.aemp_name,
                                    t8.edsg_name,
                                    t1.note_dtim attn_time,
                                    SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1,
                                    SUBSTRING(t1.`geo_lon`, 1, 4) geo_lon1,
                                    t9.than_name,
                                    t10.dsct_name,
                                    t4.ward_name
                                    FROM tt_note t1 
                                    INNER JOIN tm_site t2 ON t1.site_code=t2.site_code
                                    INNER JOIN tm_mktm t3 on t2.mktm_id=t3.id
                                    INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                                    INNER JOIN tm_than t9 ON t4.than_id=t9.id
                                    INNER JOIN tm_dsct t10 ON t9.dsct_id=t10.id
                                    INNER JOIN tm_aemp AS t7 ON t1.aemp_id=t7.id
                                    INNER JOIN tm_edsg AS t8 ON t7.edsg_id=t8.id
                                    WHERE t1.note_date between '$start_date' AND '$end_date' AND t1.aemp_id =$id AND t1.site_code !=''
                          ) gr )gr  
                          left join ( 
                              select month(dt) dt,aemp_id,sum(Total_note) T_note,sum( retailVisit) Rvisit,sum( OtherlVisit) Otvisit from(
                                                select  note_date dt, aemp_id, count(*) Total_note, 
                                                sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,
                                                sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherlVisit
                                                from tt_note,
                                                tm_aemp where note_date between '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id =$id
                                                group by aemp_id,note_date
                                            ) ntc
                                            group by month(dt),aemp_id 
                          ) dg on dg.dt = gr.mnth  AND dg.aemp_id=gr.id
                           INNER JOIN 
                                (select  
                                MONTH(selected_date)mnth_t,count(selected_date) t_days,min(selected_date)ss_date,max(selected_date)ee_date,
                                 SUM(CASE WHEN DAYNAME(selected_date)='Friday' THEN 1 ELSE 0 END)minus_day
                                from 
                                (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                 select 7 union select 8 union select 9) t0,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                 select 7 union select 8 union select 9) t1,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                 select 7 union select 8 union select 9) t2,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                 select 7 union select 8 union select 9) t3,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union 
                                 select 7 union select 8 union select 9) t4) v
                                where selected_date between '$start_date' AND '$end_date'
                                GROUP BY MONTH(selected_date)
                            ) wd_mnt ON mnth=wd_mnt.mnth_t
                    group by id,mnth,aemp_usnm,aemp_name ORDER BY aemp_usnm,mnth; ");
                break;

            default:
                $data="";
            
        }
        return array(
        'data'=> $data,
        'start_date'=>$start_date,
        'end_date'=>$end_date
        );
    }
    public function getWardWiseVisitDetails($id,$date){
        $data1=DB::connection($this->db)->select("SELECT 
                t3.id,t1.aemp_id,t1.ssvh_date,t3.mktm_name,count(DISTINCT t1.site_id) t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t3.id,t1.aemp_id,t3.mktm_name ORDER BY t3.mktm_name ASC;");
        $data2=DB::connection($this->db)->select("SELECT 
                t1.ssvh_date,t5.id,t1.aemp_id,t5.than_name,count(DISTINCT t1.site_id)t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t1.ssvh_date,t5.id,t1.aemp_id,t5.than_name
                ORDER BY t5.than_name ASC");
        $data3=DB::connection($this->db)->select("SELECT 
                t4.id,t1.aemp_id,t1.ssvh_date,t4.ward_name,count(DISTINCT t1.site_id) t_visit
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date'
                GROUP BY t4.id,t1.aemp_id,t1.ssvh_date,t4.ward_name ORDER BY t4.ward_name ASC;");
        $data4=DB::connection($this->db)->select("SELECT  
                t4.id,t4.otcg_name,t1.aemp_id,t1.ssvh_date,
                sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0)
                GROUP BY t4.id,t4.otcg_name");
        return array('data1'=>$data1,'data2'=>$data2,'data3'=>$data3,'data4'=>$data4);

    }
    public function showWardWiseVisitOutletDetails(Request $request){
        $data='';
        $id=$request->id;
        $date=$request->date;
        $type=$request->type;
        $stage=$request->stage;
        if($type==1){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t3.id=$stage
                ORDER BY t2.site_name ASC");
        }
        else if($type==2){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t4.id=$stage
                ORDER BY t2.site_name ASC");
        }
        else if($type==3){
            $data=DB::connection($this->db)->select("SELECT 
                t2.site_name,t2.site_code,t2.site_mob1,t2.site_adrs
                FROM `th_ssvh` t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                INNER JOIN tm_than t5 ON t4.than_id=t5.id
                INNER JOIN tm_aemp t6 ON t1.aemp_id=t6.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t5.id=$stage
                ORDER BY t2.site_name ASC");
        }
        return $data;
    }
    public function showCatWiseOutlet(Request $request){
        $id=$request->id;
        $date=$request->date;
        $cat_id=$request->cat_id;
        $data=DB::connection($this->db)->select("SELECT  
                t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
                WHERE t1.aemp_id=$id AND t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0) AND t4.id=$cat_id
               Order By t3.site_name ASC ");
        return $data;
    }
    public function getMapData(Request $request){
        $sr_id=$request->sr_id;
        $date=$request->date;
        $place=$request->place;
      
        $data=DB::connection($this->db)->select("SELECT site_code,site_name,site_adrs,geo_lat,geo_lon,log_time,v_type FROM 
        (
        (SELECT t2.site_code,t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon,t1.attr1 log_time,t1.ssvh_ispd v_type FROM th_ssvh t1
         INNER JOIN tm_site t2 ON t1.site_id=t2.id
         WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.attr1 asc)
        UNION ALL
        (SELECT '' site_code,'0' site_name,t1.hloc_addr site_adrs,t1.geo_lat,t1.geo_lon,t1.hloc_time log_time,'loc' v_type FROM th_hloc t1
        WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date'  group by t1.hloc_addr,site_name,t1.geo_lat,t1.geo_lon,t1.hloc_time ORDER BY t1.hloc_time asc))p 
        ORDER BY p.log_time ASC") ;
        $rout_site=DB::connection($this->db)->select("SELECT 
                    t3.site_code,t3.site_name,t3.site_adrs,t3.geo_lat,t3.geo_lon
                    FROM tl_rpln t1
                    INNER JOIN tl_rsmp t2  ON t1.rout_id=t2.rout_id
                    INNER JOIN tm_site t3 ON t2.site_id=t3.id
                    WHERE t1.aemp_id=$sr_id AND t1.rpln_day=DAYNAME('$date')");
        return array('data'=>$data,'rout_site'=>$rout_site);
        // $hloc_data=DB::connection($this->db)->select("SELECT '' site_name,t1.hloc_addr 'site_adrs',max(t1.geo_lat),max(t1.geo_lon),min(t1.hloc_time) log_time FROM th_hloc t1
        //            WHERE t1.aemp_id=$sr_id and t1.hloc_date='$date'  group by t1.hloc_addr,site_name,t1.hloc_time ORDER BY t1.hloc_time asc");
        // $ssvh_data=DB::connection($this->db)->select("  SELECT t2.site_name,t2.site_adrs,t2.geo_lat,t2.geo_lon,t1.attr1 log_time FROM th_ssvh t1
        //             INNER JOIN tm_site t2 ON t1.site_id=t2.id
        //             WHERE t1.aemp_id=$sr_id and t1.ssvh_date='$date' ORDER BY t1.attr1 asc;");
       // return array('hloc_data'=>$hloc_data,'ssvh_data'=>$ssvh_data);

    }
    public function showAllVisitedOutletList(Request $request){
        $id=$request->id;
        $date=$request->date;
        $out_list=DB::connection($this->db)->select("SELECT  t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
                FROM  th_ssvh t1
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                WHERE  t1.ssvh_date='$date' AND t1.ssvh_ispd in (1,0) AND t1.aemp_id=$id
                ORDER BY t3.site_name ASC");
        return $out_list;
    }



    // Employee Attendance Report
    public function getEmpAttendanceReport(Request $request){
        $emid=$request->emid;
        $role_id=Auth::user()->employee()->role_id;
        $date=$request->date;
        if($date==''){
            $date=date('Y-m-d');
        }
        $un_emp=DB::connection($this->db)->select("SELECT
                t4.id 									   AS id,
                t4.aemp_usnm,                              
                t4.aemp_name,
                t4.aemp_mob1,                              
                t1.role_id,
                t5.zone_name,                                
                t2.role_name ,
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
                t1.dhbd_lvsr                                              AS lvSr
                FROM th_dhbd_5 AS t1
                INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                LEFT JOIN th_dhbd_5 AS t3
                    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
                INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
                INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
                WHERE t1.aemp_mngr = '$emid' AND t1.dhbd_date ='$date'
                UNION ALL
                SELECT
                t4.id 									   AS id,
                t4.aemp_usnm,                                
                t4.aemp_name,
                t4.aemp_mob1,                             
                t1.role_id, 
                t5.zone_name,                                
                t2.role_name ,
                t1.dhbd_ucnt                                              AS totalSr,
                t1.dhbd_prnt                                              AS onSr,
                t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
                t1.dhbd_lvsr                                              AS lvSr   
                FROM th_dhbd_5 AS t1
                INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
                LEFT JOIN th_dhbd_5 AS t3
                    ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
                INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
                INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
                INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
                WHERE t8.dhem_emid = '$emid' AND t1.dhbd_date ='$date'");
        return array('un_emp'=>$un_emp,'role_id'=>$role_id);
    }

    public function expEmpAttendanceData(Request $request){
        $date=$request->date;
        $id=$request->emid;
        $type=$request->type;
        if($date==''){
            $date=date('Y-m-d');
        }    
        $this->db = Auth::user()->country()->cont_conn;
        $role_id=DB::connection($this->db)->select("SELECT role_id from tm_aemp where id=$id");
        $role_id=$role_id[0]->role_id;
        $mngr_list='';
        $list=$id;
        if($role_id<=5 && $role_id>2){
            while($list){
                $list_to_check=DB::connection($this->db)->select("Select aemp_id,role_id from th_dhbd_5 where aemp_mngr in ($list)");
                $list='';
                $cnt=0;
                $cnt1=0;
                foreach($list_to_check as $s){
                    if($s->role_id <$role_id){
                        if($s->role_id==2){
                            if($cnt==0){
                                $mngr_list.=$s->aemp_id;
                            }
                            else{
                                $mngr_list.=','.$s->aemp_id;
                            }
                            $cnt++;
                        }else if($s->role_id>2){
                            if($cnt1==0){
                                $list.=$s->aemp_id;
                                
                            }else{
                                $list.=','.$s->aemp_id;
                            }
                            $cnt1++;
                            
                        }
                    }
                }
            }
            
        }else{
            $mngr_list=$id;
        }
        $heading=['Emp_Name','Staff_Id','Mobile_Num','Zone_Name','Role_ID'];
        $query='';
        $file_name='';
        if($type==1){
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  FROM `th_dhbd_5` t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
             AND t1.role_id=1 AND t2.lfcl_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
            $file_name='total_sr_list.xlsx';
        }
        else if($type==2){
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  FROM `th_dhbd_5` t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
            AND  t1.dhbd_prnt=1 AND t1.role_id=1  AND t2.lfcl_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
             $file_name='present_sr_list.xlsx';
        }
        else if($type==3){
            
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name ,t2.role_id FROM `th_dhbd_5` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
                WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
                AND   (t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`))>0 AND t1.role_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
             $file_name='off_sr_list.xlsx';
        }
        else{
            $query="SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name ,t2.role_id FROM `th_dhbd_5` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
                WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
                AND   t1.dhbd_lvsr>0 AND t1.role_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1,t3.zone_name,t2.role_id  ORDER BY t2.aemp_name";
                $file_name='leave_sr_list.xlsx';
        }
        return Excel::download(new RequestReportData($query,$this->db,$heading), $file_name);
        
    }
    public function insertAddress(){
        $data=DB::connection($this->db)->select("SELECT id,geo_lat,geo_lon FROM track_location_attendance limit 1");
       // $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=25.0625815,91.3898202&sensor=false&key=AIzaSyDudLkmuyLG2cU6G-TT5fD_g2rzMI0tK94';
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=AIzaSyBBT24LpOLk1qOrZrGwARNM_Jnkxwwdu20';
        $json = @file_get_contents($url);
        return $json;
        $data=json_decode($json);
        $status = $data->status;
        if($status=="OK")
        {
           return  $data->results[0]->formatted_address;
        }
        else
        {
        return false;
        }
        
    }
    public function userLocationReport(){
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
       
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        return view('report.summary_report.user_location',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone]);
    }
    public function getLocAddress(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $staff_id=$request->staff_id;
        // $data=DB::connection($this->db)->select("SELECT 
        // t1.hloc_date,t2.aemp_usnm ,t2.aemp_name,max(t1.geo_lat) geo_lat,max(t1.geo_lon)geo_lon
        // FROM `th_hloc` t1
        // INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        // WHERE   t1.hloc_date BETWEEN '$start_date' AND '$end_date' AND t2.aemp_usnm=$staff_id
        // GROUP BY t1.hloc_date,t2.aemp_usnm,t2.aemp_name,t1.hloc_addr ORDER BY t1.hloc_date,t2.aemp_usnm,t2.aemp_name,t1.hloc_addr ASC");

        $data=DB::connection($this->db)->select("select distinct hloc_date,id,aemp_usnm,aemp_name,SUBSTRING(`geo_lat1`, 1, 5) geo_lat ,
            SUBSTRING(`geo_lon1`, 1, 5)  geo_lon,than_name,dsct_name, ifnull(total_note,0) Total_note  from 
            (
            SELECT distinct t1.hloc_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,
            SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
            SUBSTRING(t1.`geo_lon`, 1, 5)  geo_lon1
            FROM `th_hloc` as t1 
            , tm_aemp as t2, tm_edsg as t3
            where t1.aemp_id=t2.id and t1.hloc_date between '$start_date' AND '$end_date' and t2.aemp_usnm='$staff_id'
            and t2.edsg_id=t3.id 
            ) as t77 inner join  (select distinct t6.dsct_name,t6.than_name,SUBSTRING(t6.`geo_lat`, 1, 5) geo_lat , SUBSTRING(t6.`geo_lon`, 1, 5) geo_lon from tm_ward_zone_lat_lon t6) as t78 
            on SUBSTRING(t77.geo_lat1, 1, 5)  =SUBSTRING(t78.geo_lat, 1, 5)  and SUBSTRING(t77.geo_lon1, 1, 5)  =SUBSTRING(t78.geo_lon, 1, 5) 
            left join (select note_date, aemp_id, count(*) total_note from tt_note,tm_aemp where tm_aemp.id= tt_note.aemp_id and aemp_usnm ='$staff_id' group by aemp_id,note_date) 
            t79 on t77.id= t79.aemp_id AND t79.note_date = t77.hloc_date ORDER BY hloc_date,aemp_name asc");
        return $data;
    }

    public function getActivitySummaryDetails($aemp_usnm,$start_date,$end_date){
          $sr_id=DB::connection($this->db)->select("SELECT id FROM tm_aemp WHERE aemp_usnm='$aemp_usnm' limit 1");
          $id=$sr_id?$sr_id[0]->id:'';
        //Level 2
        // $data=DB::connection($this->db)->select("select '$id' aemp_id,max(aemp_usnm)aemp_usnm,monthname(attn_date) mnth,attn_date,GROUP_CONCAT( DISTINCT than_name )than_name,
        //         GROUP_CONCAT( DISTINCT dsct_name)dsct_name,max(Total_note)Total_note,max(ifnull(retailVisit,0))retailVisit,max(ifnull(OtherVisit,0))OtherVisit from(
        //         select distinct attn_date,t66.aemp_usnm,t66.aemp_name,SUBSTRING(t66.`geo_lat1`, 1, 5) geo_lat,
        //         SUBSTRING(t66.`geo_lon1`, 1, 3)  geo_lon,t66.id  ,t66.than_name,t66.dsct_name, ifnull(total_note,0) Total_note,note_dtim,retailVisit,OtherVisit  from 
        //         (SELECT distinct t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.attn_time,
        //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
        //         SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name
        //         FROM `tt_attn` as t1 
        //         left join tm_ward_ward_lat_lon t6  on
        //         MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1), (t1.geo_lat - 9 / 111.1)) 
        //         ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
        //         , tm_aemp as t2, tm_edsg as t3
        //         where t1.aemp_id=t2.id and t1.attn_date between '$start_date' AND '$end_date' and t2.id=$id
        //         and t2.edsg_id=t3.id ) as t66 
        //         left join (SELECT distinct t1.note_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.note_dtim,
        //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
        //         SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,ntpe_id
        //         FROM `tt_note` as t1 
        //         left join tm_ward_ward_lat_lon t6  on
        //         MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1), (t1.geo_lat - 9 / 111.1)) 
        //         ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
        //         , tm_aemp as t2, tm_edsg as t3
        //         where t1.aemp_id=t2.id and t1.note_date between '$start_date' AND '$end_date' and t2.id=$id
        //         and t2.edsg_id=t3.id 
        //         )as t77 on t66.id= t77.id AND t66.attn_date = t77.note_date
        //         left join (select  note_date dt, aemp_id, count(*) Total_note,sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherVisit
        //         from tt_note,tm_aemp where note_date between '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id =$id group by aemp_id,note_date) 
        //         t79 on t77.id= t79.aemp_id AND t79.dt = t77.note_date
        //         order by  attn_date,note_dtim ASC
        //         ) df GROUP BY attn_date;");
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        // $data=DB::connection($this->db)->select("select '$id' aemp_id,max(aemp_usnm)aemp_usnm,monthname(attn_date) mnth,attn_date,GROUP_CONCAT( DISTINCT than_name )than_name,DATE_FORMAT(s_time, '%r')s_time,
        //         DATE_FORMAT(e_time,'%r')e_time,
        //         GROUP_CONCAT( DISTINCT dsct_name)dsct_name,max(Total_note)Total_note,max(ifnull(retailVisit,0))retailVisit,max(ifnull(OtherVisit,0))OtherVisit from(
         
        //         select distinct attn_date,t66.aemp_usnm,t66.aemp_name,SUBSTRING(t66.`geo_lat1`, 1, 5) geo_lat,
        //         SUBSTRING(t66.`geo_lon1`, 1, 3)  geo_lon,t66.id  ,t66.than_name,t66.dsct_name,s_time,e_time,ifnull(total_note,0) Total_note,note_dtim,retailVisit,OtherVisit  from 
        //         (
        //             SELECT  t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,min(t1.attn_time)s_time,max(t1.attn_time)e_time,
        //             SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
        //             SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,
        //             GROUP_CONCAT(DISTINCT t6.than_name)than_name,GROUP_CONCAT(DISTINCT t6.dsct_name)dsct_name
        //             FROM `tt_attn` as t1 
        //             left join tm_ward_ward_lat_lon t6  on 
        //             MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1),(t1.geo_lat - 9 / 111.1))), POINT(t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
        //         , tm_aemp as t2, tm_edsg as t3
        //         where t1.aemp_id=t2.id and t1.attn_date between '$start_date' AND '$end_date' and t2.id=$id
        //         and t2.edsg_id=t3.id GROUP BY attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name) as t66 
        //         left join (SELECT distinct t1.note_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.note_dtim,
        //         SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
        //         SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,ntpe_id
        //         FROM `tt_note` as t1 
        //         left join tm_ward_ward_lat_lon t6  on
        //         MBRContains(st_makeEnvelope (point((t1.geo_lon + .9 / 111.1), (t1.geo_lat + .9 / 111.1)),  point((t1.geo_lon - 9 / 111.1), (t1.geo_lat - 9 / 111.1)) 
        //         ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
        //         , tm_aemp as t2, tm_edsg as t3
        //         where t1.aemp_id=t2.id and t1.note_date between '$start_date' AND '$end_date' and t2.id=$id
        //         and t2.edsg_id=t3.id 
        //         )as t77 on t66.id= t77.id AND t66.attn_date = t77.note_date
        //         left join (select  note_date dt, aemp_id, count(*) Total_note,sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherVisit
        //         from tt_note,tm_aemp where note_date between  '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id =$id group by aemp_id,note_date) 
        //         t79 on t77.id= t79.aemp_id AND t79.dt = t77.note_date
        //         order by  attn_date,note_dtim ASC      
        //     ) df       
        //     GROUP BY attn_date ORDER BY s_time ASC;");
        $data=DB::connection($this->db)->select("select '$id' aemp_id,max(aemp_usnm)aemp_usnm,monthname(attn_date) mnth,attn_date,GROUP_CONCAT( DISTINCT than_name )than_name,DATE_FORMAT(s_time, '%r')s_time,
                DATE_FORMAT(e_time,'%r')e_time,
                GROUP_CONCAT( DISTINCT dsct_name)dsct_name,max(Total_note)Total_note,max(ifnull(retailVisit,0))retailVisit,max(ifnull(OtherVisit,0))OtherVisit from(
        
                select distinct attn_date,aemp_usnm,aemp_name,SUBSTRING(`geo_lat1`, 1, 5) geo_lat,
                SUBSTRING(`geo_lon1`, 1, 3)  geo_lon,id  ,than_name,dsct_name,s_time,e_time,ifnull(total_note,0) Total_note,retailVisit,OtherVisit  from 
                (
                    SELECT  t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,min(t1.attn_time)s_time,max(t1.attn_time)e_time,
                SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,
                t6.than_name,t6.dsct_name,t6.ward_name
                FROM `tt_attn` as t1 
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                LEFT join tm_ward_ward_lat_lon t6  on
                    MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  point((t1.geo_lon - 1 / 111.1),
                                                                                                                    (t1.geo_lat - 1 / 111.1)) 
                    ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                        and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    

                    where t1.aemp_id  =$id and t1.attn_date   between '$start_date' AND '$end_date' 
                GROUP BY t1.attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name, t6.than_name,t6.dsct_name,t6.ward_name
                UNION ALL
                SELECT  t1.note_date attn_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name, min(t1.note_dtim)s_time,max(t1.note_dtim)e_time,
                    SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1 ,
                    SUBSTRING(t1.`geo_lon`, 1, 4)  geo_lon1,t6.than_name,t6.dsct_name,t6.ward_name
                    FROM `tt_note` as t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN tm_edsg t3 ON t2.edsg_id=t3.id
                    left join tm_ward_ward_lat_lon t6  on
                    MBRContains(st_makeEnvelope (point((t1.geo_lon + .1 / 111.1), (t1.geo_lat + .1 / 111.1)),  
                    point((t1.geo_lon - 1 / 111.1), 		(t1.geo_lat - 1 / 111.1)) 
                    ), POINT( t6.geo_lon, t6.geo_lat ))and SUBSTRING(t1.`geo_lat`, 1, 5) =SUBSTRING(t6.`geo_lat`, 1, 5) 
                            and SUBSTRING(t1.`geo_lon`, 1, 3) = SUBSTRING(t6.`geo_lon`, 1, 3)                    
                    where t1.aemp_id  =$id and t1.note_date   between '$start_date' AND '$end_date' AND t1.site_code=''
                GROUP BY t1.note_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t6.than_name,t6.dsct_name,t6.ward_name
                UNION ALL
                SELECT DISTINCT
                    t1.note_date attn_date,
                    t7.id,
                    t7.aemp_usnm,
                    t7.aemp_name,
                    t8.edsg_name,
                    min(t1.note_dtim)s_time,max(t1.note_dtim)e_time,
                    SUBSTRING(t1.`geo_lat`, 1, 5) geo_lat1,
                    SUBSTRING(t1.`geo_lon`, 1, 4) geo_lon1,
                    t9.than_name,
                    t10.dsct_name,
                    t4.ward_name
                    FROM tt_note t1 
                    INNER JOIN tm_site t2 ON t1.site_code=t2.site_code
                    INNER JOIN tm_mktm t3 on t2.mktm_id=t3.id
                    INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                    INNER JOIN tm_than t9 ON t4.than_id=t9.id
                    INNER JOIN tm_dsct t10 ON t9.dsct_id=t10.id
                    INNER JOIN tm_aemp AS t7 ON t1.aemp_id=t7.id
                    INNER JOIN tm_edsg AS t8 ON t7.edsg_id=t8.id
                    WHERE t1.note_date   between '$start_date' AND '$end_date' AND t1.aemp_id  =$id AND t1.site_code !=''
                    GROUP BY t1.note_date,
                    t7.id,
                    t7.aemp_usnm,
                    t7.aemp_name,
                    t8.edsg_name,t9.than_name,
                    t10.dsct_name,
                    t4.ward_name
                    
                )gr
                left join (select  note_date dt, aemp_id, count(*) Total_note,sum(case when ntpe_id=1 then 1 Else 0 END)  retailVisit,sum(case when ntpe_id<>1 then 1 ELSE 0 END) OtherVisit
                from tt_note,tm_aemp where note_date   between '$start_date' AND '$end_date' and  tm_aemp.id= tt_note.aemp_id and tm_aemp.id  =$id group by aemp_id,note_date) 
                t79 on gr.id= t79.aemp_id AND t79.dt = gr.attn_date
                order by  attn_date ASC      
            ) df       
            GROUP BY attn_date ORDER BY attn_date,s_time ASC;");
        return $data;
           
            
    }
    public function getEmpAutoId($aemp_usnm){
        $emp= Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
        return $emp->id;
    }
    public function getHeatMap($aemp_usnm,$start_date,$end_date){
        try{
            // $data=DB::connection($this->db)->select("select distinct  hloc_date,id,aemp_usnm,aemp_name,geo_lat ,geo_lon from(
            //     select distinct hloc_date,id,aemp_usnm,aemp_name,`geo_lat1` geo_lat ,
            //     `geo_lon1`  geo_lon, hloc_time  from 
            //     (
            //     SELECT distinct t1.hloc_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.hloc_time,
            //     t1.`geo_lat` geo_lat1 ,
            //     t1.`geo_lon`  geo_lon1
            //     FROM `th_hloc` as t1 , tm_aemp as t2, tm_edsg as t3
            //     where t1.aemp_id=t2.id and t1.hloc_date between '$start_date' AND '$end_date' and t2.aemp_usnm='$aemp_usnm'
            //     and t2.edsg_id=t3.id 
            //     ) as t77 
            //     order by  hloc_time ASC) df;");
            $emp_id=$this->getEmpAutoId($aemp_usnm);
            $data=DB::connection($this->db)->select("select distinct  hloc_date,id,aemp_usnm,aemp_name,geo_lat ,geo_lon from(
                    select distinct hloc_date,id,aemp_usnm,aemp_name,`geo_lat1` geo_lat ,
                    `geo_lon1`  geo_lon, hloc_time  from 
                    (
                    SELECT distinct t1.hloc_date,t2.id, t2.aemp_usnm,t2.aemp_name,t3.edsg_name,t1.hloc_time,
                    t1.`geo_lat` geo_lat1 ,
                    t1.`geo_lon`  geo_lon1
                    FROM `th_hloc` as t1 , tm_aemp as t2, tm_edsg as t3
                    where t1.aemp_id=t2.id and t1.hloc_date between '$start_date' AND '$end_date' and t2.aemp_usnm='$$aemp_usnm'
                    and t2.edsg_id=t3.id 
                    ) as t77 
                order by  hloc_time ASC) df              
                    UNION ALL
                    SELECT attn_date hloc_date,t2.id,t2.aemp_usnm,t2.aemp_name,t1.geo_lat,t1.geo_lon from tt_attn t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    WHERE t1.aemp_id='$emp_id' AND t1.attn_date between '$start_date' AND '$end_date'
                    UNION ALL
                    SELECT note_date hloc_date,t2.id,t2.aemp_usnm,t2.aemp_name,t1.geo_lat,t1.geo_lon FROM tt_note t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    WHERE t1.aemp_id='$emp_id' AND t1.note_date between '$start_date' AND '$end_date'");
               // return $data;
               $lats = $lons = array();
               foreach ($data as $key => $value) {
                   array_push($lats, $value->geo_lat);
                   array_push($lons, $value->geo_lon);
               }
               $minlat = min($lats);
               $maxlat = max($lats);
               $minlon = min($lons);
               $maxlon = max($lons);
               $lat = $maxlat - (($maxlat - $minlat) / 2);
               $lng = $maxlon - (($maxlon - $minlon) / 2);
            return view("report.summary_report.activity_summary_heatmap")->with('data',$data)->with('lat',$lat)->with('lng',$lng);
        }
        catch(\Exception $e){
            return "Something wrong in your data";
        }
        
    }
    public function getAttendanceLocationMap($aemp_id,$attn_date){
        $data=DB::connection($this->db)->select("SELECT attn_date,aemp_id, max(start_loc) start_loc,max(end_loc) end_loc,max(start_time) start_time,max(end_time) end_time
        FROM 
        (SELECT 
        t3.attn_date,t3.aemp_id,
         CASE WHEN t3.atten_type=1 AND t3.atten_atyp=1 THEN CONCAT(t3.geo_lat,',',t3.geo_lon) END as start_loc,
         CASE WHEN t3.atten_type=2 AND t3.atten_atyp=1 THEN CONCAT(t3.geo_lat,',',t3.geo_lon) END as end_loc,
         CASE WHEN t3.atten_type=1 AND t3.atten_atyp=1 THEN t3.attn_time ELSE '0000-00-00 00:00:00' END as start_time,
         CASE WHEN t3.atten_type=2 AND t3.atten_atyp=1  THEN t3.attn_time ELSE '0000-00-00 00:00:00' END as end_time
        FROM tt_attn t3 WHERE t3.attn_date='$attn_date' AND t3.aemp_id=$aemp_id)t1
        GROUP  BY t1.attn_date,t1.aemp_id");
        return $data;
    }
    public function exportDetailExecutiveGVTReport(Request $request){
        $slgp_id=$request->slgp_id;
        $acmp_id=$request->acmp_id;
        $time_period=$request->time_period;
        $emp_id=Auth::user()->employee()->id;
        $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        if($time_period){ 
            if($time_period==0){
                $start_date= Carbon::now()->format('Y-m-d');
                $end_date= Carbon::now()->format('Y-m-d');
            }
            else if($time_period==1){
                $start_date= Carbon::yesterday()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==10){
                $start_date = Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
                $end_date = Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
            }
            else if($time_period==11){      
                $start_date=$dt->copy()->subDays(7)->format('Y-m-d');
                $end=$dt->copy()->subDays(7);
                $end_date=$end->addDays(6)->format('Y-m-d');
            }
            else if($time_period==12){
                $start_date=$dt->copy()->subDays(14)->format('Y-m-d');
                $end=$dt->copy()->subDays(14);
                $end_date=$end->addDays(6)->format('Y-m-d');
            }
            else if($time_period==5){
                $start_date= Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date= Carbon::yesterday()->format('Y-m-d');
            }
            else if($time_period==30){
                $start_date= Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $end_date= Carbon::now()->endOfMonth()->subMonth()->format('Y-m-d');
            }
            else{
                $start_date=$time_period;
                $end_date=$time_period;
            }   
        }else{
            $start_date= $request->start_date;
            $end_date=$request->end_date;
        }
        $slgp='';
        if($slgp_id==0){
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND  acmp_id=$acmp_id  ORDER BY slgp_name ASC");
        }
        else{
            $slgp=DB::connection($this->db)->select("Select slgp_id as id,slgp_name from user_group_permission where aemp_id=$emp_id AND slgp_id=$slgp_id  ORDER BY slgp_name ASC");
        }
        $slgp_ids=[];
        $q3='';
        for($i=0;$i<count($slgp);$i++){
            $slgp_ids[$i]=$slgp[$i]->id;
        }
        $heading=['Market_Name','Ward_Name','Thana_Name','District_Name','Division_Name','Group_Name','Market_Outlet','Group_Outlet','Total_Visit','Ro_Visit','Order_Amount','Delivery_Amount'];
        $query="Select 
                t1.mktm_name,t1.ward_name,t1.than_name,t1.dsct_name,t1.disn_name,t1.slgp_name,
                t1.mktm_olt,t2.slgp_out slgp_olt,sum(t3.visit) total_visit,
                sum(t3.ro_visit)ro_visit,round(sum(t3.ord_amount),4)ord_amount,
                round(sum(t3.delivery_amount),4)delivery_amount
                FROM 
                (SELECT 
                t1.mktm_id,t1.mktm_name,t1.ward_name,t1.than_name,t1.dsct_name,t3.disn_name,t4.id slgp_id,t4.slgp_name,sum(t1.total_outlet)mktm_olt
                FROM tbl_mktm_wise_total_outlet t1
                INNER JOIN tm_dsct t2 ON t1.dsct_id=t2.id
                INNER JOIN tm_disn t3 ON t2.disn_id=t3.id,
                (SELECT id,slgp_name FROM tm_slgp WHERE id IN (".implode(',',$slgp_ids)."))t4
                GROUP BY t1.mktm_id,t1.mktm_name,t1.ward_name,t1.than_name,t1.dsct_name,t3.disn_name,t4.id,t4.slgp_name
                ORDER BY t1.mktm_name) t1
                LEFT JOIN tbl_mktm_slgp_wise_outlet t2 ON t1.mktm_id=t2.mktm_id AND t1.slgp_id=t2.slgp_id
                LEFT JOIN (select * from tbl_slgp_vsmo1 where ssvh_date between '$start_date' AND '$end_date' AND slgp_id IN (".implode(',',$slgp_ids).") ) t3 
                ON t2.mktm_id=t3.mktm_id AND t2.slgp_id=t3.slgp_id
                GROUP BY t1.mktm_id,t1.mktm_name,t1.ward_name,t1.than_name,t1.dsct_name,t3.disn_name,t1.slgp_name";
        $file_name="Executive_Summary_GVT_Details_Report.xlsx";
        return Excel::download(new GvtDetailReport($query,$this->db,$heading), $file_name);  
}
public function getAssetDeeperData(Request $request){
    $slgp_id=$request->slgp_id;
    $stage=$request->stage;
    $start_date=$request->start_date;
    $end_date=$request->end_date;
    $zone_id=$request->zone_id;
    
}
public function getAssetOutletDetails(Request $request){
    $slgp_id=$request->slgp_id;
    $astm_id=$request->astm_id;
    $start_date=$request->start_date;
    $end_date=$request->end_date;
    $zone_id=$request->zone_id;
    $today_date=date('Y-m-d');
    if($start_date==$today_date){
        $data=DB::connection($this->db)->select("SELECT
        t1.site_id,replace(concat(t2.site_code,'-',t2.site_name),',','-')site_name,t2.site_mob1,replace(t3.mktm_name,',','-')mktm_name,replace(t4.ward_name,',','-')ward_name,t5.than_name,t6.dsct_name,
        ifnull(round(sum(t7.site_ordr),2),'N/A')site_ordr,ifnull(round(sum(t7.ast_itm_ordr),2),'N/A')ast_itm_ordr 
        FROM tl_assm t1
        INNER JOIN tm_site t2 ON t1.site_id=t2.id
        INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
        INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
        INNER JOIN tm_than t5 ON t4.than_id=t5.id
        INNER JOIN tm_dsct t6 ON t5.dsct_id=t6.id
        LEFT JOIN 
        (
        SELECT 
        t1.slgp_id,t4.id zone_id,t1.ordm_ornm,t1.site_id,sum(t1.ordm_amnt) site_ordr,t5.astm_id,sum(t9.ordd_oamt)ast_itm_ordr
        FROM tt_ordm t1
        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
        INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
        INNER JOIN tm_site t7 ON t1.site_id=t7.id
        INNER JOIN tl_astd t8 ON t5.astm_id=t8.astm_id
        INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
        WHERE t1.slgp_id=$slgp_id AND t1.ordm_date=curdate() AND t5.astm_id=$astm_id AND t4.id=$zone_id
        GROUP BY t4.id ,t1.ordm_ornm,t1.site_id,t1.ordm_amnt
        )t7 
        ON t1.site_id=t7.site_id AND t1.slgp_id=t7.slgp_id
        WHERE t1.slgp_id=$slgp_id AND t1.zone_id=$zone_id AND t1.astm_id=$astm_id
        GROUP BY t1.site_id,t2.site_name,t2.site_mob1,t3.mktm_name,t4.ward_name,t5.than_name,t6.dsct_name 
        ORDER BY t6.dsct_name,t5.than_name,t4.ward_name,t3.mktm_name,t2.site_name ASC");
    }else{
        $data=DB::connection($this->db)->select("SELECT
        t1.site_id,replace(concat(t2.site_code,'-',t2.site_name),',','-')site_name,t2.site_mob1,replace(t3.mktm_name,',','-')mktm_name,replace(t4.ward_name,',','-')ward_name,t5.than_name,t6.dsct_name,
        ifnull(round(sum(site_ordr),2),'N/A')site_ordr,ifnull(round(sum(ast_itm_ordr),2),'N/A')ast_itm_ordr 
        FROM tl_assm t1
        INNER JOIN tm_site t2 ON t1.site_id=t2.id
        INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
        INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
        INNER JOIN tm_than t5 ON t4.than_id=t5.id
        INNER JOIN tm_dsct t6 ON t5.dsct_id=t6.id
        LEFT JOIN (SELECT * FROM tbl_asset_summary WHERE ordm_date between '$start_date' AND '$end_date' AND slgp_id=$slgp_id AND zone_id=$zone_id) t7 ON t1.site_id=t7.site_id  AND t1.slgp_id=t7.slgp_id 
        WHERE t1.slgp_id=$slgp_id AND t1.astm_id=$astm_id AND t1.zone_id=$zone_id 
        GROUP BY t1.site_id,t2.site_name,t2.site_mob1,t3.mktm_name,t4.ward_name,t5.than_name,t6.dsct_name 
        ORDER BY t6.dsct_name,t5.than_name,t4.ward_name,t3.mktm_name,t2.site_name ASC");
    }
    return $data;
}

public function getAssetOutletCurrentYearSummary(Request $request){
    $slgp_id=$request->slgp_id;
    $astm_id=$request->astm_id;
    $site_id=$request->site_id;
    $data=DB::connection($this->db)->select("SELECT
    MONTHNAME(t1.ordm_date)mnth,replace(concat(t7.site_code,'-',t7.site_name),',','-')site_name,round(sum(site_ordr),2) site_ordr,round(sum(ast_itm_ordr),2)ast_itm_ordr
    FROM tbl_asset_summary t1
    INNER JOIN tm_site t7 ON t1.site_id=t7.id
    WHERE t7.id=$site_id AND year(t1.ordm_date)=Year(curdate())  AND t1.astm_id=$astm_id AND t1.slgp_id=$slgp_id 
    GROUP BY  mnth,t7.site_name");
    return $data;
}
public function getAssetOutletDetailsThanaWise(Request $request){
    $slgp_id=$request->slgp_id;
    $astm_id=$request->astm_id;
    $start_date=$request->start_date;
    $end_date=$request->end_date;
    $zone_id=$request->zone_id;
    $today_date=date('Y-m-d');
    if($start_date==$today_date){
        $data=DB::connection($this->db)->select("SELECT
        t5.than_name,t6.dsct_name,
        ifnull(round(sum(t7.site_ordr),2),'N/A')site_ordr,ifnull(round(sum(t7.ast_itm_ordr),2),'N/A')ast_itm_ordr 
        FROM tl_assm t1
        INNER JOIN tm_site t2 ON t1.site_id=t2.id
        INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
        INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
        INNER JOIN tm_than t5 ON t4.than_id=t5.id
        INNER JOIN tm_dsct t6 ON t5.dsct_id=t6.id
        LEFT JOIN 
        (
        SELECT 
        t1.slgp_id,t4.id zone_id,t1.ordm_ornm,t1.site_id,sum(t1.ordm_amnt) site_ordr,t5.astm_id,sum(t9.ordd_oamt)ast_itm_ordr
        FROM tt_ordm t1
        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        INNER JOIN tm_zone t4 ON t2.zone_id=t4.id
        INNER JOIN tl_assm t5 ON t1.site_id=t5.site_id AND t1.slgp_id=t5.slgp_id
        INNER JOIN tm_site t7 ON t1.site_id=t7.id
        INNER JOIN tl_astd t8 ON t5.astm_id=t8.astm_id
        INNER JOIN tt_ordd t9 ON t1.ordm_ornm=t9.ordm_ornm and t8.amim_id=t9.amim_id
        WHERE t1.slgp_id=$slgp_id AND t1.ordm_date=curdate() AND t5.astm_id=$astm_id AND t4.id=$zone_id
        GROUP BY t4.id ,t1.ordm_ornm,t1.site_id,t1.ordm_amnt
        )t7 
        ON t1.site_id=t7.site_id AND t1.slgp_id=t7.slgp_id
        WHERE t1.slgp_id=$slgp_id AND t1.zone_id=$zone_id AND t1.astm_id=$astm_id
        GROUP BY t5.than_name,t6.dsct_name 
        ORDER BY t5.than_name,t6.dsct_name ASC");
    }else{
        $data=DB::connection($this->db)->select("SELECT
        t5.than_name,t6.dsct_name,
        ifnull(round(sum(site_ordr),2),'N/A')site_ordr,ifnull(round(sum(ast_itm_ordr),2),'N/A')ast_itm_ordr 
        FROM tl_assm t1
        INNER JOIN tm_site t2 ON t1.site_id=t2.id
        INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
        INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
        INNER JOIN tm_than t5 ON t4.than_id=t5.id
        INNER JOIN tm_dsct t6 ON t5.dsct_id=t6.id
        LEFT JOIN (SELECT * FROM tbl_asset_summary WHERE ordm_date between '$start_date' AND '$end_date' AND slgp_id=$slgp_id AND zone_id=$zone_id) t7 ON t1.site_id=t7.site_id  AND t1.slgp_id=t7.slgp_id 
        WHERE t1.slgp_id=$slgp_id AND t1.astm_id=$astm_id AND t1.zone_id=$zone_id
        GROUP BY t5.than_name,t6.dsct_name 
        ORDER BY t5.than_name,t6.dsct_name ASC");
    }
    return $data;
}
public function orderStatusReportFms(){
    $empId = $this->aemp_id;
    $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
    $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
    $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
    $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
    $dlrm_list = DB::connection($this->db)->select("SELECT id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
    return view('report.summary_report.order_delivery_report',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list]);
}

public function getFullOrderDetails(Request $request){
    $start_date=$request->start_date?$request->start_date:date("Y-m-d");
    $end_date=$request->end_date?$request->end_date:date("Y-m-d");
    $acmp_id=$request->acmp_id;
    $emp_id=$request->emp_id;
    $sp_id=$request->sp_id;
    $ordr_id=$request->ordr_id;
    $depot_id=$request->depot_id;
    $sb_cat=$request->sb_cat;
    $amim_code=$request->amim_code;
    $dirg_id=$request->dirg_id;
    $scnl_id=$request->scnl_id;
    $site_code=$request->site_code;
    $empId=$this->aemp_id;
    $condition='';
    if($acmp_id !=''){
        $condition .=" AND t1.acmp_id=$acmp_id";
    }
    if($emp_id !=''){
        $condition .=" AND t2.aemp_usnm='$emp_id'";
    }
    if($sp_id !=''){
        $condition .=" AND t3.aemp_usnm='$sp_id'";
    }
    if($ordr_id){
        $condition.=" AND t1.ordm_ornm='$ordr_id'";
    }
    if($depot_id){
        $condition.=" AND t4.id=$depot_id";
    }
    if($sb_cat){
        $condition.=" AND t6.itsg_id=$sb_cat";
    }
    if($amim_code){
        $condition.=" AND t6.amim_code='$amim_code'";
    }
    if($dirg_id){
        $zone_list=[];
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        for($i=0;$i<count($dirg_zone_list);$i++){
                $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        $condition.=" AND t2.zone_id IN (".implode(',',$zone_list).")";
    }
    if($scnl_id){
        $condition.=" AND t8.scnl_id =$scnl_id";
    }
    if($site_code){
        $condition.=" AND t8.site_code='$site_code'";
    }
    
    $data=DB::connection($this->db)->select("SELECT 
    t1.ordm_date,t4.dlrm_name,t2.aemp_name sr_name,t3.aemp_name sv_name,t8.site_code,t8.site_name,t1.ordm_ornm,t6.amim_code,
    t6.amim_name,t5.ordd_qnty,round(t5.ordd_qnty/t6.amim_duft,2)ctn,t5.ordd_oamt,t7.id lfcl_id,t7.lfcl_name   
    FROM tt_ordm t1
    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
    INNER JOIN tm_aemp t3 ON t2.aemp_mngr=t3.id
    INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
    INNER JOIN tt_ordd t5 ON t1.ordm_ornm=t5.ordm_ornm
    INNER JOIN tm_amim t6 ON t5.amim_id=t6.id
    INNER JOIN tm_lfcl t7 ON t1.lfcl_id=t7.id
    INNER JOIN tm_site t8 ON t1.site_id=t8.id
    WHERE t1.ordm_date between '$start_date' AND '$end_date' ".$condition." ORDER BY t1.ordm_date,dlrm_name");
    return $data;

}
public function checkdateTime(){

}
public function getStartAndEndDate($time_period){
    $start_date='';
    $end_date='';
    $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
    switch($time_period){
        case -1:
            $start_date=$this->single_date;
            $end_date= $this->single_date;;
            break;
        case 0:
            $start_date= Carbon::now()->format('Y-m-d');
            $end_date= Carbon::now()->format('Y-m-d');
            break;
        case 1:
            $start_date= Carbon::yesterday()->format('Y-m-d');
            $end_date= Carbon::yesterday()->format('Y-m-d');
            break;
        case 10:
            $start_date = Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
            $end_date = Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
            break;
        case 11:
            $start_date=$dt->copy()->subDays(7)->format('Y-m-d');
            $end=$dt->copy()->subDays(7);
            $end_date=$end->addDays(6)->format('Y-m-d');
            break;
        case 12:
            $start_date=$dt->copy()->subDays(14)->format('Y-m-d');
            $end=$dt->copy()->subDays(14);
            $end_date=$end->addDays(6)->format('Y-m-d');
            break;
        case 5:
            $start_date= Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date= Carbon::yesterday()->format('Y-m-d');
            break;
        case 30:
            $start_date= Carbon::now()->startOfMonth()->subMonth(1)->format('Y-m-d');
            $end_date= Carbon::now()->endOfMonth()->subMonth(1)->format('Y-m-d');
            break;
        default:
            $start_date= Carbon::now()->format('Y-m-d');
            $end_date= Carbon::now()->format('Y-m-d');
            break;
    }
    return array(
        'start_date'=>$start_date,
        'end_date'=>$end_date
    );

}

}