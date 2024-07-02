<?php

namespace App\Http\Controllers\Report\TeleSales;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 * 
 */
use App\BusinessObject\Department;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SalesGroup;
use App\MasterData\NoOrderReason;
use App\MasterData\NoteType;
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
class TeleSalesReportController extends Controller
{
    private $access_key = 'report/tele_sales';
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
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");     
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        $np_reasons = NoOrderReason::on($this->db)->get(['id', 'nopr_code', 'nopr_name']);

        $asset='';
        if(Auth::user()->country()->id==2 || Auth::user()->country()->id==5){
            $asset=DB::connection($this->db)->select("SELECT id,astm_name FROM tm_astm ORDER BY astm_name asc");
        }
        
        return view('report.TeleSales.teleSales_report',
            ['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone,
                'asset'=>$asset, 'np_reasons' => $np_reasons]);
    }

    public function testReport(){
        return view('report.TeleSales.test_report');
    }
    public function testReport2(){
        return view('report.TeleSales.test_report_single');
    }

    public function index_test()
    {
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $slgps = DB::connection($this->db)->select("SELECT DISTINCT slgp_id as id, slgp_name, slgp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        $asset='';


        if(Auth::user()->country()->id==2 || Auth::user()->country()->id==5){
            $asset=DB::connection($this->db)->select("SELECT id,astm_name FROM tm_astm ORDER BY astm_name asc");
        }

        return view('report.TeleSales.teleSales_report_test',['acmp'=>$acmp,'region'=>$region,'dsct'=>$dsct,'dsct1'=>$dsct,'role_id'=>$user_role_id,'emid'=>$empId,'zone'=>$zone,'asset'=>$asset]);
    }
    public function getReportData(Request $request){
        $empId = $this->currentUser->employee()->id;
        $time_period=$request->time_period;
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $reportType = $request->reportType;
        $nop_reason_id = $request->nop_reason_id;
        $zone_id = $request->zone_id;
        $slgp_id=$request->slgp_id;
        $dirg_id=$request->dirg_id;
        $acmp_id=$request->acmp_id;
        if($time_period !=''){
            $date=$this->getStartAndEndDate($time_period);
            $start_date=$date['start_date'];
            $end_date=$date['end_date'];
        }
        $dirg_zone_list=DB::connection($this->db)->select("SELECT DISTINCT zone_id FROM `user_area_permission` WHERE `aemp_id`='$empId' and dirg_id='$dirg_id'");
        $zone_list=[];
        for($i=0;$i<count($dirg_zone_list);$i++){
            $zone_list[$i]=$dirg_zone_list[$i]->zone_id;
        }
        $data='';
        $nopr_list='';
        $details=$request->is_details;
       // return $request->all();
        switch ($request->reportType){
            case "Tele_NonProductive_Reason_Summary":
                $zone_q='';
                $select_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t2.id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t2.id = '$zone_id' ";
                }
                if ($slgp_id != "") {
                    $zone_q .= " AND t3.id = '$slgp_id' ";
                }

                $nopr_list=DB::connection($this->db)->select("SELECT id,nopr_name FROM tm_nopr WHERE lfcl_id=1 ORDER BY id asc");
                for($i=0;$i<count($nopr_list);$i++){
                    $temp_v_id=$nopr_list[$i]->id;
                    $temp_v_cl=$nopr_list[$i]->nopr_name;
                    $select_q.=",sum(if(t1.nopr_id='$temp_v_id',1,0))`$temp_v_cl`";
                }

                if($details==1){
                    $data=DB::connection($this->db)->select("SELECT
                            t1.npro_date,t2.id aemp_id,t2.aemp_usnm,t2.aemp_name,t3.slgp_name,count(*)npro_olt,count(DISTINCT t1.site_id) olt_cov " .$select_q . "
                            FROM (Select * from tt_npro WHERE npro_date between '$start_date' AND '$end_date' AND  aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)) t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                            INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                            INNER JOIN tm_nopr t4 ON t1.nopr_id=t4.id
                            WHERE t3.acmp_id={$acmp_id} ". $zone_q."
                            GROUP BY t1.npro_date,t2.id,t2.aemp_usnm,t2.aemp_name,t3.slgp_name ORDER BY  t1.npro_date,t2.aemp_usnm;");

                }
                else{
                    $data=DB::connection($this->db)->select("SELECT t2.id aemp_id,
                            t2.aemp_usnm,t2.aemp_name,t3.slgp_name,count(*)npro_olt,count(DISTINCT t1.site_id) olt_cov " .$select_q . "
                            FROM (Select * from tt_npro WHERE npro_date between '$start_date' AND '$end_date' AND  aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)) t1
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                            INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                            INNER JOIN tm_nopr t4 ON t1.nopr_id=t4.id
                            WHERE t3.acmp_id={$acmp_id} ". $zone_q."
                            GROUP BY t2.id,t2.aemp_usnm,t2.aemp_name,t3.slgp_name ORDER BY t2.aemp_usnm;");
                }
                break;
            case "np_summary":
                $zone_q='';
                $select_q='';
                if($dirg_id !=""){
                    $zone_q=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q= " AND t3.zone_id = '$zone_id' ";
                }
                if ($slgp_id != "") {
                    $zone_q .= " AND t4.id = '$slgp_id' ";
                }
                if($details==1){

                    $data = [];

                    $zone_q='';

                    if($dirg_id !=""){
                        $zone_q=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }
                    if ($zone_id != "") {
                        $zone_q= " AND t1.zone_id = '$zone_id' ";
                    }
                    if ($slgp_id != "") {
                        $zone_q .= " AND t1.slgp_id = '$slgp_id' ";
                    }

                    $users = DB::connection($this->db)->select("SELECT t1.id, t1.aemp_name, t1.aemp_usnm, t3.aemp_name manager FROM tm_aemp t1
                        INNER JOIN tbl_tele_users t2 ON t1.id = t2.aemp_id
                        LEFT JOIN tm_aemp t3 on t3.id = t1.aemp_mngr
                        where 1 $zone_q");

                    foreach ($users as $user){
                        $data[$user->id]=DB::connection($this->db)->select("SELECT * FROM 
                        (SELECT
                        t1.nopr_name VISIT_STATUS,count(t2.id) QUANTITY,'' ORDER_AMNT
                        FROM tm_nopr t1
                        LEFT JOIN 
                        (
                        Select t1.id,t1.nopr_id from tt_npro t1
                        INNER JOIN tbl_tele_users t2 ON t1.aemp_id=t2.aemp_id
                        INNER JOIN tm_aemp t3 ON t2.aemp_id=t3.id
                        INNER JOIN tm_slgp t4 ON t3.slgp_id=t4.id
                        WHERE t1.npro_date between '$start_date' AND '$end_date' AND t4.acmp_id=$acmp_id AND
                        t2.aemp_id = {$user->id}) t2 ON t1.id=t2.nopr_id
                        GROUP BY t1.nopr_name
                        ORDER BY nopr_name ASC)t
                        UNION ALL
                        SELECT 
                        'Confirm Order' nopr_name ,COUNT(t1.id) QUANTITY,ROUND(SUM(t1.ordm_amnt),2) ORDER_AMNT
                        FROM tt_ordm t1
                        INNER JOIN tbl_tele_users t2 ON t1.aemp_id=t2.aemp_id
                        WHERE t1.ordm_date between '$start_date' AND '$end_date' AND t1.lfcl_id !=16 AND t2.aemp_id = {$user->id}");
                    }

                    return array(
                        'data'=>$data,
                        'users'=>$users,
                        'managers'=>($users) ? array_unique(array_column($users, 'manager')) : [],
                        'nopr_list'=>$nopr_list,
                        'start_date'=>$start_date,
                        'end_date'=>$end_date
                    );
                }
                else{
                    $data=DB::connection($this->db)->select("SELECT * FROM 
                    (SELECT
                    t1.nopr_name VISIT_STATUS,count(t2.id) QUANTITY
                    FROM tm_nopr t1
                    LEFT JOIN 
                    (
                    Select t1.id,t1.nopr_id from tt_npro t1
                    INNER JOIN tbl_tele_users t2 ON t1.aemp_id=t2.aemp_id
                    INNER JOIN tm_aemp t3 ON t2.aemp_id=t3.id
                    INNER JOIN tm_slgp t4 ON t3.slgp_id=t4.id
                    WHERE t1.npro_date between '$start_date' AND '$end_date' AND t4.acmp_id=$acmp_id ".$zone_q. " ) t2 ON t1.id=t2.nopr_id
                    GROUP BY t1.nopr_name
                    ORDER BY nopr_name ASC)t
                    UNION ALL
                    SELECT 
                    'Confirm Order' nopr_name ,count(t1.id) t_npv
                    FROM tt_ordm t1
                    INNER JOIN tbl_tele_users t2 ON t1.aemp_id=t2.aemp_id
                    WHERE t1.ordm_date between '$start_date' AND '$end_date' AND t1.lfcl_id !=16");
                }


                break;
            case "np_reason_chart":
                $data=DB::connection($this->db)->select("SELECT t4.nopr_name,
                        SUM(CASE WHEN HOUR(t1.npro_time)<9 THEN 1 ELSE 0 END)`9am`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=9 THEN 1 ELSE 0 END)`10am`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=10 THEN 1 ELSE 0 END)`11am`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=11 THEN 1 ELSE 0 END)`12pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=12 THEN 1 ELSE 0 END)`1pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=13 THEN 1 ELSE 0 END)`2pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=14 THEN 1 ELSE 0 END)`3pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=15 THEN 1 ELSE 0 END)`4pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=16 THEN 1 ELSE 0 END)`5pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=17 THEN 1 ELSE 0 END)`6pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=18 THEN 1 ELSE 0 END)`7pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)=19 THEN 1 ELSE 0 END)`8pm`,
                        SUM(CASE WHEN HOUR(t1.npro_time)>=20 THEN 1 ELSE 0 END)`9pm`
                        FROM (Select * from tt_npro WHERE npro_date between '$start_date' AND '$end_date' AND aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)) t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                        INNER JOIN tm_nopr t4 ON t1.nopr_id=t4.id 
                        WHERE t3.acmp_id={$request->acmp_id}  
                        GROUP BY t4.nopr_name;");
                break;
            case "sr_hourly_activity":
                $zone_q='';
                if($dirg_id !=""){
                    $zone_q=" AND zone_id IN (".implode(',',$zone_list).")";
                }
                if ($zone_id != "") {
                    $zone_q = " AND zone_id = $zone_id";
                }
                if($slgp_id){
                    $zone_q .= " AND slgp_id = $slgp_id";
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
                        WHERE  act_date between '$start_date' AND '$end_date' AND acmp_id={$acmp_id} AND  aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)
                        " .$zone_q. "
                          ");
                return $data;
                break;
            case "sr_productivity":
                $q1='';
                $q2='';
                if($details==1){
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = '$zone_id' ";
                    }
                    if ($slgp_id != "") {
                        $q2 = " AND t1.slgp_id = '$slgp_id' ";
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
                            WHERE t1.date >= '$start_date' AND t1.date <= '$end_date'  AND t1.aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)
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
                else{
                    if($dirg_id !=""){
                        $q1=" AND t1.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t1.zone_id = '$zone_id' ";
                    }
                    if ($slgp_id != "") {
                        $q2 = " AND t1.slgp_id = '$slgp_id' ";
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
                            WHERE t1.date >= '$start_date' AND t1.date <= '$end_date' AND t1.aemp_id in (select aemp_id from tbl_tele_users WHERE lfcl_id=1)  AND t2.aemp_id = $empId AND
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
                return array(
                    'data'=>$data,
                    'summary'=>$summary,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date
                );
                break;
            case "weekly_outlet_summary":
                $period=$request->year_mnth;
                $sr_id=$request->sr_id;
                $data='';
                $all_week=DB::connection($this->db)->select("Select WEEK(ordm_date) week_no FROM tbl_olt_cov_details 
                            WHERE DATE_FORMAT(ordm_date, '%Y-%m') ='$period'
                            GROUP BY week_no ORDER BY week_no asc;");
                           // return $start_date ."|". $end_date;
                $select_q='';
                $filter='';
                if($slgp_id){
                    $filter=" AND slgp_id=$slgp_id";
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
            case "item_summary":
                $q1='';
                $q2='';
                if($dirg_id !=""){
                    $q1=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                }          
                if ($zone_id != "") {
                    $q1 = " AND t3.zone_id = '$zone_id' ";
                }
                if ($slgp_id != "") {
                    $q2 = " AND t1.slgp_id = '$slgp_id' ";
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
            case "order_report":
                if($details==0){
                    $q1='';
                    $q2='';
                    if($dirg_id !=""){
                        $q1=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t3.zone_id = '$zone_id' ";
                    }
                    if ($slgp_id != "") {
                        $q2 = " AND t2.slgp_id = '$slgp_id' ";
                    }
                    $data=DB::connection($this->db)->select("SELECT 
                            t2.ordm_date,t8.slgp_name,t3.aemp_usnm,t3.aemp_name,t2.ordm_ornm,t2.ordm_icnt,t2.ordm_amnt,t4.lfcl_name,
                            IF(t2.lfcl_id=11,t2.ordm_drdt,'') delivery_date,IFNULL(t7.TRIP_DATE,'')TRIP_DATE,t5.ORDM_ORNM,COUNT(t6.AMIM_ID) challan_sku,
                            SUM( IF(t6.TRIP_STATUS='D',1,0)) delv_sku,IFNULL(t5.DELV_AMNT,0)DELV_AMNT,IFNULL(t5.INV_AMNT,0)INV_AMNT
                            FROM tbl_tele_users t1
                            INNER JOIN tt_ordm t2 ON t1.aemp_id=t2.aemp_id
                            INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            LEFT JOIN dm_trip_master t5 ON t2.ordm_ornm=t5.ORDM_ORNM
                            LEFT JOIN dm_trip_detail t6 ON t5.ORDM_ORNM=t6.ORDM_ORNM
                            LEFT JOIN dm_trip t7 ON t5.TRIP_NO=t7.TRIP_NO
                            INNER JOIN tm_slgp t8 ON t2.slgp_id=t8.id
                            WHERE t2.ordm_date BETWEEN '$start_date' AND '$end_date' " .$q1.$q2."
                            GROUP BY 
                            t3.aemp_usnm,t3.aemp_name,t2.ordm_date,t2.ordm_ornm,t2.ordm_icnt,t2.ordm_amnt,t4.lfcl_name,
                            t7.TRIP_DATE,t5.ORDM_ORNM,t5.DELV_AMNT,t5.INV_AMNT
                            ORDER BY t2.ordm_date,t3.aemp_usnm;");
                    return $data;
                }else{
                    $q1='';
                    $q2='';
                    if($dirg_id !=""){
                        $q1=" AND t3.zone_id IN (".implode(',',$zone_list).")";
                    }          
                    if ($zone_id != "") {
                        $q1 = " AND t3.zone_id = '$zone_id' ";
                    }
                    if ($slgp_id != "") {
                        $q2 = " AND t2.slgp_id = '$slgp_id' ";
                    }
                    $data=DB::connection($this->db)->select("SELECT 
                            t2.ordm_date,t8.slgp_name,t3.aemp_usnm,t3.aemp_name,t2.ordm_ornm,
                            t7.amim_code,t7.amim_name,max(t9.ordd_inty)ordd_inty,max(t9.ordd_oamt)ordd_oamt,IFNULL(max(t6.INV_QNTY),0)INV_QNTY,IFNULL(max(t6.ORDD_OAMT),0) INV_AMNT,
                            IFNULL(max(t6.DELV_QNTY),0)DELV_QNTY,max(t9.ordd_dqty)ordd_dqty,max(t9.ordd_odat)ordd_odat
                            FROM tbl_tele_users t1
                            INNER JOIN tt_ordm t2 ON t1.aemp_id=t2.aemp_id
                            INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_slgp t8 ON t2.slgp_id=t8.id
                            INNER JOIN tt_ordd t9 ON t2.id=t9.ordm_id
                            LEFT JOIN dm_trip_detail t6 ON t2.ordm_ornm=t6.ORDM_ORNM AND t6.AMIM_ID=t9.amim_id
                            INNER JOIN tm_amim  t7 ON t9.amim_id=t7.id
                            WHERE  t9.ordd_inty>0 AND t2.ordm_date BETWEEN '$start_date' AND '$end_date' " .$q1.$q2."
                            GROUP BY t2.ordm_date,t8.slgp_name,t3.aemp_usnm,t3.aemp_name,t2.ordm_ornm,
                            t7.amim_code,t7.amim_name
                            ORDER BY t2.ordm_date,t3.aemp_usnm;");
                    return $data;
                }
                break;
            case "non_productive_reason_outlet_summary":
                $country_id = $this->currentUser->country()->id;

                $code='880';
                $digit=-10;

                if($country_id==14){
                    $digit=-9;
                    $code='60';
                }
                elseif ($country_id==15){
                    $digit=-9;
                    $code='966';
                }
                elseif ($country_id==9){
                    $digit=-10;
                    $code='91';
                }
                elseif ($country_id==3){
                    $digit=-9;
                    $code='971';
                }
                elseif ($country_id==24){
                    $digit=-10;
                    $code='1';
                }

                $q1='';

                $q2='';

                if($start_date != "" && $end_date != ""){
                    $q1 = "t2.npro_date between '{$start_date}' AND '{$end_date}'";
                }elseif($start_date != ""){
                    $end_date = date('Y-m-d');
                    $q1 = "t2.npro_date between '{$start_date}' AND '{$end_date}'";
                }elseif ($end_date != ""){
                    $q1 = "t2.npro_date = '{$end_date}'";
                }

                if ($nop_reason_id != "") {
                    $q2 = " AND t2.nopr_id = '{$nop_reason_id}'";
                }

                $data=DB::connection($this->db)->select("SELECT t2.npro_date,t9.dsct_name,t8.than_name,t7.ward_name,t6.mktm_name,
                    t5.site_code,t5.site_name,t5.site_adrs,t4.nopr_name,
                    CONCAT({$code},SUBSTRING(REPLACE(t5.site_mob1,'-',''),{$digit})) 'site_mob1'
                    FROM tbl_tele_users t1
                    INNER JOIN tt_npro t2 ON t1.aemp_id=t2.aemp_id
                    INNER JOIN tm_nopr t4 ON t2.nopr_id=t4.id
                    INNER JOIN tm_site t5 ON t2.site_id=t5.id
                    INNER JOIN tm_mktm t6 ON t5.mktm_id=t6.id
                    INNER JOIN tm_ward t7 ON t6.ward_id=t7.id
                    INNER JOIN tm_than t8 ON t7.than_id=t8.id
                    INNER JOIN tm_dsct t9 ON t8.dsct_id=t9.id
                    WHERE {$q1} {$q2}
                    GROUP BY t2.npro_date,t9.dsct_name,t8.than_name,t7.ward_name,t6.mktm_name,t5.site_code,
                    t5.site_name,t5.site_adrs,t5.site_mob1,t4.nopr_name ORDER BY t5.site_code,t2.npro_date");
                return $data;

                break;
            default:
                $data="";
                break;

        }
        return array(
            'data'=>$data,
            'nopr_list'=>$nopr_list,
            'start_date'=>$start_date,
            'end_date'=>$end_date
        );
    }

    public function getNPDetails(Request $request){
        $aemp_id=$request->aemp_id;
        $nopr_id=$request->nopr_id;
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        // $data=DB::connection($this->db)->select("SELECT
        //         t1.npro_date,t2.site_code,t2.site_name,t4.aemp_usnm,t4.aemp_name,t5.zone_code,t5.zone_name,t2.site_mob1,
        //         IF(t3.optp_id=2,'CR','CS') site_type
        //         FROM tt_npro t1
        //         INNER JOIN tm_site t2 ON t1.site_id=t2.id
        //         INNER JOIN tl_stcm t3 ON t1.slgp_id=t3.slgp_id AND t1.site_id=t3.site_id
        //         INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
        //         INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
        //         WHERE t1.npro_date between '$start_date' AND '$end_date' AND t1.aemp_id=$aemp_id and NOPR_ID=$nopr_id;");
        $data=DB::connection($this->db)->select("SELECT
                t1.npro_date,t2.site_code,t2.site_name,t4.aemp_usnm,t4.aemp_name,t5.zone_code,t5.zone_name,t2.site_mob1,
                IF(t3.optp_id=2,'CR','CS') site_type,t6.rcds_file
                FROM tt_npro t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                LEFT JOIN tl_stcm t3 ON t1.slgp_id=t3.slgp_id AND t1.site_id=t3.site_id
                INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
                INNER JOIN tm_zone t5 ON t4.zone_id=t5.id
                LEFT JOIN tm_rcds t6 ON t1.attr4=t6.tltr_id
                WHERE t1.npro_date between '$start_date' AND '$end_date' AND t1.aemp_id=$aemp_id and nopr_id=$nopr_id;");
        return $data;

    }

public function getSRListModuleTwo(Request $request){
    $slgp_id=$request->slgp_id;
    $dirg_id=$request->dirg_id;
    $zone_id=$request->zone_id;
    $filter_query='';
    if($dirg_id){
        $filter_query.=" AND t2.dirg_id=$dirg_id ";
    }
    if($zone_id){
        $filter_query.=" AND t2.id=$zone_id";
    }
    $data=DB::connection($this->db)->select("
            SELECT 
            t1.id,t1.aemp_usnm,t1.aemp_name
            FROM tm_aemp t1
            INNER JOIN tm_zone t2  ON t1.zone_id=t2.id
            WHERE t1.slgp_id={$slgp_id} AND t1.lfcl_id=1 AND t1.role_id=1 " .$filter_query. "  
            ORDER BY aemp_usnm             
        ");
    return $data;
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

    public function getAllGroup(Request $request)
    {
        $acmp_id = implode(',',$request->slgp_ids);
        $empId = $this->currentUser->employee()->id;

        return $companies = DB::connection($this->db)
            ->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`={$empId} and acmp_id in ({$acmp_id})");
    }

    public function salesInvoice($cont_id, $order_id,$ou_id)
    {

        $country = (new Country())->country($cont_id);

        if ($country != false) {
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
            if ($cont_id == 14) {
                $orderMasterData = collect(\DB::connection($country->cont_conn)->select("SELECT
                        t1.ordm_ornm                                          AS Order_ID,
                        DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
                        DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
                        IFNULL(t10.mother_site_name,t2.site_name)             AS Outlet_Name,
                        t2.site_name                                          AS Site_Name,
                        t6.DELV_AMNT                                          AS total_price,
                        '0'                                                   AS discount,
                        t2.site_code                                          AS customer_number,
                        IFNULL(t10.mother_site_code,t2.outl_id)               AS outlet_id,
                        t9.optp_name                                          AS Payment_Type,
                        ''                                                    AS Region_Name,
                        ''                                                    AS Zone_Name,
                        t6.SHIPINGADD                                         AS site_address,
                      IFNULL(t10.billing_address,t2.site_adrs)               AS outlet_address,
                        t3.aemp_name                                          AS preseller_name,
                        t3.aemp_mob1                                          AS preseller_mob,
                        t4.id                                                 AS ou_id,
                        t4.acmp_name                                          AS ou_name,
                        t4.acmp_note                                          AS acmp_note,
                        t4.acmp_nexc                                          AS tax_number,
                        t4.acmp_nvat                                          AS vat_number,
                        ''                                                    AS year,
                        ''                                                    AS serial_number,
                        IFNULL(t6.IBS_INVOICE,'-')                            AS vat_sl_number,
                        t4.acmp_addr                                          AS address,
                        t2.site_vtrn                                          AS VAT_TRN,
                        t4.acmp_titl                                          AS invoice_title,
                        t4.acmp_vats                                          AS vat_status,
                        t6.DELV_AMNT                                          AS invoice_amount,
                        t6.DM_CODE                                            AS DM_CODE,
                        t6.V_NAME                                             AS V_NAME,
                        IFNULL(t11.pocm_pono,'-')                             AS po_no,
                        IFNULL(t11.date,'-')                                  AS po_date,
                        t4.acmp_creg                                          AS currency,
                        t4.acmp_dgit                                          AS round_digit,
                        t4.acmp_rond                                          AS round,
                        t4.acmp_note                                          AS note
                      FROM tt_ordm AS t1
                        INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                        INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                        INNER JOIN tl_pcmp AS t4 ON t4.id=$ou_id
                        LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
                        INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
                        left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id
                        left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
                        left JOIN tl_site_party_mapping AS t10 ON t10.site_code = t6.SITE_CODE
                        left JOIN tl_pocm AS t11 ON t6.ORDM_ORNM = t11.ordm_ornm
                      WHERE t1.ordm_ornm = '$order_id' GROUP BY  t1.ordm_ornm"))->first();

                if ($ou_id == 3) {
                    $orderLineData = DB::connection($country->cont_conn)->select("SELECT
                          t3.amim_code                                                      AS Product_id,
                          t3.amim_name                                                      AS Product_Name,
                          t3.amim_olin                                                      AS sku_print_name,
                          floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                          t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                          round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
                          round(t2.ordd_uprc * t2.ordd_duft,2)                              AS Rate,
                          round((t4.DISCOUNT) * 100 /(t4.ORDD_UPRC*t4.DELV_QNTY),2)         AS ratio,
                          t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                          t2.ordd_duft                                                      AS ctn_size,
                          t2.prom_id                                                        AS promo_ref,
                          t2.ordd_excs                                                      AS gst,
                          t2.ordd_ovat                                                      AS vat,
                          round(t4.DISCOUNT,2)                                              AS total_discount,
                          round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                          round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                          round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
                          round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                          round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                        FROM tt_ordm AS t1
                          INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                          INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                          INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.id=t4.OID
                        WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0 AND t3.attr4 IN(2,3);");

                    if ($orderLineData) {

                    } else {
                        return dd("No item found under this company . Print Not Available");
                    }
                } else {
                    $orderLineData = DB::connection($country->cont_conn)->select("SELECT
                              t3.amim_code                                                      AS Product_id,
                              t3.amim_name                                                      AS Product_Name,
                              t3.amim_olin                                                      AS sku_print_name,
                              floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                              t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                              round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
                              round(t2.ordd_uprc * t2.ordd_duft,2)                              AS Rate,
                              round((t4.DISCOUNT) * 100 /(t4.ORDD_UPRC*t4.DELV_QNTY),2)         AS ratio,
                              t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                              t2.ordd_duft                                                      AS ctn_size,
                              t2.prom_id                                                        AS promo_ref,
                              t2.ordd_excs                                                      AS gst,
                              t2.ordd_ovat                                                      AS vat,
                            round(t4.DISCOUNT,2)                                               AS total_discount,
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                              round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                            FROM tt_ordm AS t1
                              INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                              INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                              INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.id=t4.OID
                            WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0 AND t3.attr4=1;");
                }

                return view('PrintData.sales_invoice_print_test')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);
            }
        }
    }

}