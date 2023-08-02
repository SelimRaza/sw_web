<?php

namespace App\Http\Controllers\LeaderBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderBoardController extends Controller
{
	private $access_key = 'national_leaders';
    private $currentUser;
    private $userMenu;
    private $db;
    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }
    public function index($selector=0){
        $date=date('m');
        $q='';
        $year='';
        if($date=='01'){
           $q='AND S_YEAR=YEAR(CURDATE()-Interval 1 year)';
        }else{
            $q='AND S_YEAR=YEAR(CURDATE())';
        }
        if($selector==0){
            $super_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,
                `SPRO_IMG`,
                `SEC_AMOUNT`
                FROM `tbld_new_leaderboard`
                WHERE (`LEADER_TYPE`=29 OR `LEADER_TYPE`=30) AND `ACHV_PERCENTAGE`>=98 ".$q."AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
                ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,
                `SPRO_IMG`,
                `SEC_AMOUNT`
                FROM `tbld_new_leaderboard`
                WHERE (`LEADER_TYPE`=39 OR `LEADER_TYPE`=40) AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
                ORDER BY (`ACHV_AMOUNT`)DESC LIMIT 10");
            $bst_cnt_s_ldr=DB::connection($this->db)->select("CALL getBestConSupLeader");
            $bst_cnt_sf_ldr=DB::connection($this->db)->select("CALL getBestConSupFutureLeader");
            $hatrick=DB::connection($this->db)->select("CALL getHatrick");
            $s_mngr=DB::connection($this->db)->select("	SELECT 
                `oid`,`STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,
                `SPRO_IMG`,
                `SEC_AMOUNT`
                FROM `tbld_new_leaderboard`
                WHERE (`LEADER_TYPE`=49 OR `LEADER_TYPE`=50)AND TGT_AMOUNT>=5000 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
                ORDER BY (`ACHV_AMOUNT`)DESC LIMIT 10");
            $s_hero=DB::connection($this->db)->select("SELECT 
                `oid`,`STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,
                `SPRO_IMG`,
                `SEC_AMOUNT`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=99 AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
                ORDER BY (`ACHV_AMOUNT`)DESC LIMIT 100");
            $hero=DB::connection($this->db)->select("SELECT 
                `oid`,`STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,
                `SPRO_IMG`,
                `SEC_AMOUNT`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=100 AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
                ORDER BY (`ACHV_AMOUNT`)DESC LIMIT 75");
        return view('LeaderBoard.index',
                ['super_ldr'=>$super_ldr,'bst_cnt_s_ldr'=>$bst_cnt_s_ldr,'sf_ldr'=>$sf_ldr,
                'bst_cnt_sf_ldr'=>$bst_cnt_sf_ldr,'hatrick'=>$hatrick,'s_mngr'=>$s_mngr,
                's_hero'=>$s_hero,'hero'=>$hero
                ]);
        }else if($selector==1){
            $super_ldr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=1 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND ACHV_PERCENTAGE>=98 ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $bst_cnt_s_ldr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=1 ".$q." AND S_MONTH=month(curdate()-interval 1 month)  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $sf_ldr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=2 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND ACHV_PERCENTAGE>=98  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $bst_cnt_sf_ldr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=2 ".$q." AND S_MONTH=month(curdate()-interval 1 month)  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $hatrick=DB::connection($this->db)->select("SELECT		 
                    `STAFFID`,`EMPLOYEENAME`,
                    `MOBILE`,`PHOTO`,`COMPANY_NAME`,
                    `ZONE_NAME`,
                    SUM(`TGT_AMOUNT`) AS TGT_AMOUNT,
                    SUM(ACHV_AMOUNT) AS ACHV_AMOUNT,
                    `SPRO_IMG`, max(ACHV_PERCENTAGE)ACHV_PERCENTAGE
                    FROM `tbld_new_leaderboard_ss`
                    WHERE `LEADER_TYPE`=3 AND `TGT_AMOUNT`>=5000 AND `ACHV_PERCENTAGE`>=98 
                    ".$q."
                    AND S_MONTH<=MONTH(CURDATE()-INTERVAL 1 MONTH)
                    AND S_MONTH>=MONTH(CURDATE()-INTERVAL 3 MONTH)
                    GROUP BY `STAFFID`,`EMPLOYEENAME`,
                    `MOBILE`,`PHOTO`,`COMPANY_NAME`,
                    `ZONE_NAME`,SPRO_IMG
                    HAVING COUNT(STAFFID) = 3
                    ORDER BY(ACHV_AMOUNT)DESC");
            $s_mngr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=3 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=5000 AND `ACHV_PERCENTAGE`>=98  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
            $s_hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 100");
            $hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
                    ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
                    WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98  ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 100,500");
            return view('LeaderBoard.index1',
                ['super_ldr'=>$super_ldr,'bst_cnt_s_ldr'=>$bst_cnt_s_ldr,'sf_ldr'=>$sf_ldr,
                'bst_cnt_sf_ldr'=>$bst_cnt_sf_ldr,'hatrick'=>$hatrick,'s_mngr'=>$s_mngr,
                's_hero'=>$s_hero,'hero'=>$hero
                ]);

        }
        
    	
    }
public function filterLeaderBoard(){
    $empId = $this->currentUser->employee()->id;
    $zone = DB::connection($this->db)->select("SELECT DISTINCT zone_id as id, zone_name FROM `user_area_permission`
            WHERE `aemp_id`='$empId'");
    $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name FROM `user_group_permission` 
            WHERE `aemp_id`='$empId'");
    return view('LeaderBoard.filter_leaders',['acmp'=>$acmp,'zone'=>$zone]);
}
public function progressiveLeaderBoard(){
    
        $ps_ldr=DB::connection($this->db)->select("SELECT 
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `SEC_AMOUNT`,ACHV_PERCENTAGE
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=130 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_AMOUNT`)DESC
                LIMIT 10");
        $ps_ldr_percnt=DB::connection($this->db)->select("SELECT 
                `oid`,
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `SEC_AMOUNT`,
                `ACHV_PERCENTAGE`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=135 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_PERCENTAGE`)DESC
                LIMIT 10");
        $psf_ldr=DB::connection($this->db)->select("SELECT 
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `SEC_AMOUNT`,
                `ACHV_PERCENTAGE`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=140 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_AMOUNT`)DESC
                LIMIT 10");
        $psf_ldr_percnt=DB::connection($this->db)->select("SELECT 
                `oid`,
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `ACHV_PERCENTAGE`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=145  AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_PERCENTAGE`)DESC
                LIMIT 10");
        $ps_mngr=DB::connection($this->db)->select("SELECT 
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `ACHV_PERCENTAGE`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=150 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_AMOUNT`)DESC
                LIMIT 10");
        $ps_mngr_percnt=DB::connection($this->db)->select("SELECT 
                `oid`,
                `STAFFID`,`EMPLOYEENAME`,
                `MOBILE`,`PHOTO`,`COMPANY_ID`,
                `SALES_ZONE_ID`,`TGT_AMOUNT`,
                `ACHV_AMOUNT`,`SPRO_IMG`,
                `ACHV_PERCENTAGE`
                FROM `tbld_new_leaderboard`
                WHERE `LEADER_TYPE`=155 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE()) and STR_TO_DATE( IDATE, '%d/%m/%Y')= current_date()- interval 1 day
                ORDER BY (`ACHV_AMOUNT`)DESC
                LIMIT 10");
   
    
    return view('LeaderBoard.progress',[
        'ps_ldr'=>$ps_ldr,'ps_ldr_percnt'=>$ps_ldr_percnt,
        'psf_ldr'=>$psf_ldr,'psf_ldr_percnt'=>$psf_ldr_percnt,
        'ps_mngr'=>$ps_mngr,'ps_mngr_percnt'=>$ps_mngr_percnt
    ]);
}
public function progressiveLeaderBoardSecondary(){
    $ps_ldr=DB::connection($this->db)->select("SELECT 
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE`=5 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_AMOUNT`)DESC
            LIMIT 10");
    $ps_ldr_percnt=DB::connection($this->db)->select("SELECT 
            `oid`,
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            `ACHV_PERCENTAGE`
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE`=5 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_PERCENTAGE`)DESC
            LIMIT 10");
    $psf_ldr=DB::connection($this->db)->select("SELECT 
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            `ACHV_PERCENTAGE`
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE` in (4,3) AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_AMOUNT`)DESC
            LIMIT 10");
    $psf_ldr_percnt=DB::connection($this->db)->select("SELECT 
            `oid`,
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            `ACHV_PERCENTAGE`
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE` in (4,3)  AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_PERCENTAGE`)DESC
            LIMIT 10");
    $ps_mngr=DB::connection($this->db)->select("SELECT 
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            `ACHV_PERCENTAGE`
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE`=2 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_AMOUNT`)DESC
            LIMIT 10");
    $ps_mngr_percnt=DB::connection($this->db)->select("SELECT 
            `oid`,
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,COMPANY_NAME COMPANY_ID,
            ZONE_ID SALES_ZONE_ID,`TGT_AMOUNT`,
            `ACHV_AMOUNT`,`SPRO_IMG`,
            `ACHV_PERCENTAGE`
            FROM `tbld_new_leaderboard_ss_ps`
            WHERE `LEADER_TYPE`=2 AND S_YEAR=YEAR(CURDATE()) AND S_MONTH=MONTH(CURDATE())
            ORDER BY (`ACHV_PERCENTAGE`)DESC
            LIMIT 10");
     return view('LeaderBoard.progress_secondary',[
        'ps_ldr'=>$ps_ldr,'ps_ldr_percnt'=>$ps_ldr_percnt,
        'psf_ldr'=>$psf_ldr,'psf_ldr_percnt'=>$psf_ldr_percnt,
        'ps_mngr'=>$ps_mngr,'ps_mngr_percnt'=>$ps_mngr_percnt
    ]);

}
public function getGroup(Request $request){
        $acmp_id = $request->acmp_id;
        $empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_name` FROM `user_group_permission`
                 WHERE `aemp_id`='$empId' and acmp_id  IN (".implode(',',$acmp_id).")");
}
public function getLeaders(Request $request){
    $acmp_id=$request->acmp_id;
    $slgp_id=$request->slgp_id;
    $zone_id=$request->zone_id;
    $selector=$request->selector;
    $date=date('m');
    $q='';
    $year='';
    if($date=='01'){
       $q='AND S_YEAR=YEAR(CURDATE()-Interval 1 year)';
    }else{
        $q='AND S_YEAR=YEAR(CURDATE())';
    }
    if($acmp_id !=""){
        $acmp_id_f=implode(",",$acmp_id);
    }
    else{
        $acmp_id_f=$acmp_id;
    }
    if($slgp_id !=""){
        $slgp_id_f=implode(",",$slgp_id);
    }
    else{
        $slgp_id_f=$slgp_id;
    }
    if($zone_id !=""){
        $zone_id_f=implode(",",$zone_id);
    }
    else{
        $zone_id_f=$zone_id;
    }
    $q1='';
    $q2='';
    $q3='';
    
    if($selector==0){
        if($acmp_id !=''){
            $q1="AND acmp_id IN (".implode(',',$acmp_id).")";
        }
        if($slgp_id !=''){
            $q2="AND slgp_id IN (".implode(',',$slgp_id).")";
        }
        if($zone_id !=''){
            $q3="AND zone_id IN (".implode(',',$zone_id).")";
        }
        $super_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name`,`acmp_name`,`SPRO_IMG`
                    FROM `filter_leaderboard`
                    WHERE (`LEADER_TYPE`=29 OR `LEADER_TYPE`=30) AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
                    "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                    `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name`,`acmp_name`,`SPRO_IMG`
                    FROM `filter_leaderboard`
                    WHERE (`LEADER_TYPE`=39 OR `LEADER_TYPE`=40) AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
                    "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $s_mngr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                    `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name`,`acmp_name`,`SPRO_IMG`,zone_name ZONE_NAME
                    FROM `filter_leaderboard`
                    WHERE (`LEADER_TYPE`=49 OR `LEADER_TYPE`=50)AND TGT_AMOUNT>=5000 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
                    "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $s_hero=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`,
                    `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name`,`acmp_name`,`SPRO_IMG`,zone_name ZONE_NAME
                    FROM `filter_leaderboard`
                    WHERE `LEADER_TYPE`=99 AND TGT_AMOUNT>=5000 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
                    "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 100");
        $hero=DB::connection($this->db)->select("SELECT 
                        `oid`,`STAFFID`,`EMPLOYEENAME`,`TGT_AMOUNT`,`ACHV_AMOUNT`,`SPRO_IMG`,
                        `slgp_name`,`acmp_name`,zone_name ZONE_NAME
                        FROM `filter_leaderboard`
                        WHERE `LEADER_TYPE`=100 AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
                        "ORDER BY (`ACHV_AMOUNT`)DESC LIMIT 75");
        $bst_cnt_s_ldr=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,1]);
        $bst_cnt_sf_ldr=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,2]);
        $hatrick=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,3]);
        return view('LeaderBoard.primary',
            ['super_ldr'=>$super_ldr,'sf_ldr'=>$sf_ldr,'s_mngr'=>$s_mngr,'s_hero'=>$s_hero,'hero'=>$hero,'bst_cnt_s_ldr'=>$bst_cnt_s_ldr,'bst_cnt_sf_ldr'=>$bst_cnt_sf_ldr,'hatrick'=>$hatrick]);
    }else{
        if($acmp_id !=''){
            $q1="AND COMPANY_ID IN (".implode(',',$acmp_id).")";
        }
        if($slgp_id !=''){
            $q2="AND DIGR_TEXT IN (".implode(',',$slgp_id).")";
        }
        if($zone_id !=''){
            $q3="AND ZONE_ID IN (".implode(',',$zone_id).")";
        }
       
        $super_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss`
            WHERE `LEADER_TYPE`=1 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
            "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $bst_cnt_s_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss`
            WHERE `LEADER_TYPE`=1  ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
            "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss`
            WHERE `LEADER_TYPE`=2 AND `ACHV_PERCENTAGE`>=98 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
            "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $bst_cnt_sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss`
            WHERE `LEADER_TYPE`=2 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
            "ORDER BY (`ACHV_AMOUNT`) DESC LIMIT 10");
        $hatrick=DB::connection($this->db)->select("SELECT		 
            `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,`COMPANY_NAME`,
            `ZONE_NAME`,GROUP_NAME,
            SUM(`TGT_AMOUNT`) AS TGT_AMOUNT,
            SUM(ACHV_AMOUNT) AS ACHV_AMOUNT,
            `SPRO_IMG`, max(ACHV_PERCENTAGE)ACHV_PERCENTAGE
            FROM `tbld_new_leaderboard_ss`
            WHERE `LEADER_TYPE`=3 AND `TGT_AMOUNT`>=5000 AND `ACHV_PERCENTAGE`>=98 
            ".$q."
            AND S_MONTH<=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND S_MONTH>=MONTH(CURDATE()-INTERVAL 3 MONTH)". $q1 . $q2 . $q3."
            GROUP BY `STAFFID`,`EMPLOYEENAME`,
            `MOBILE`,`PHOTO`,`COMPANY_NAME`,GROUP_NAME,
            `ZONE_NAME`,SPRO_IMG
            HAVING COUNT(STAFFID) = 3
            ORDER BY ACHV_AMOUNT DESC");
        $s_mngr=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=3 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=5000 AND `ACHV_PERCENTAGE`>=98 ". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC LIMIT 10");
        $s_hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98 ". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC LIMIT 100");
        $hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month) AND TGT_AMOUNT>=500 AND `ACHV_PERCENTAGE`>=98 ". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC LIMIT 100,500");
        
            
        return view('LeaderBoard.secondary',
            ['super_ldr'=>$super_ldr,'sf_ldr'=>$sf_ldr,'s_mngr'=>$s_mngr,'s_hero'=>$s_hero,'hero'=>$hero,'bst_cnt_s_ldr'=>$bst_cnt_s_ldr,'bst_cnt_sf_ldr'=>$bst_cnt_sf_ldr,'hatrick'=>$hatrick]);


    }
    // return response()->json([
    //     'super_ldr'=>$super_ldr,
    //     'sf_ldr'=>$sf_ldr,
    //     's_mngr'=>$s_mngr,
    //     's_hero'=>$s_hero,
    //     'hero'=>$hero,
    //     'bst_cnt_s_ldr'=>$bst_cnt_s_ldr,
    //     'bst_cnt_sf_ldr'=>$bst_cnt_sf_ldr,
    //     'hatrick'=>$hatrick
    // ]);
   

}
public function filterOwnUnitLeaderBoard(){
    $empId = $this->currentUser->employee()->id;
    $zone = DB::connection($this->db)->select("SELECT DISTINCT zone_id as id, zone_name FROM `user_area_permission`
            WHERE `aemp_id`='$empId'");
    $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name FROM `user_group_permission` 
            WHERE `aemp_id`='$empId'");
    return view('LeaderBoard.own_unit_filter_leaders',['acmp'=>$acmp,'zone'=>$zone]);
}
public function getOwnUnitLeaders(Request $request){
    $acmp_id=$request->acmp_id;
    $slgp_id=$request->slgp_id;
    $zone_id=$request->zone_id;
    $selector=$request->selector;
    $date=date('m');
    $q='';
    $year='';
    if($date=='01'){
       $q='AND S_YEAR=YEAR(CURDATE()-Interval 1 year)';
    }else{
        $q='AND S_YEAR=YEAR(CURDATE())';
    }
    // if($acmp_id !=""){
    //     $acmp_id_f=implode(",",$acmp_id);
    // }
    // else{
    //     $acmp_id_f=$acmp_id;
    // }
    // if($slgp_id !=""){
    //     $slgp_id_f=implode(",",$slgp_id);
    // }
    // else{
    //     $slgp_id_f=$slgp_id;
    // }
    // if($zone_id !=""){
    //     $zone_id_f=implode(",",$zone_id);
    // }
    // else{
    //     $zone_id_f=$zone_id;
    // }
    $q1='';
    $q2='';
    $q3='';
    
    // if($selector==0){
    //     if($acmp_id !=''){
    //         $q1="AND acmp_id IN (".implode(',',$acmp_id).")";
    //     }
    //     if($slgp_id !=''){
    //         $q2="AND slgp_id IN (".implode(',',$slgp_id).")";
    //     }
    //     if($zone_id !=''){
    //         $q3="AND zone_id IN (".implode(',',$zone_id).")";
    //     }
        // $super_ldr=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`,
        //     `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name` SLGP_NAME,`acmp_name` ACMP_NAME,`SPRO_IMG`
        //     FROM `filter_leaderboard`
        //     WHERE (`LEADER_TYPE`=29 OR `LEADER_TYPE`=30)  ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC ");
        // $sf_ldr=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`,
        //     `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name` SLGP_NAME,`acmp_name` ACMP_NAME,`SPRO_IMG`
        //     FROM `filter_leaderboard`
        //     WHERE (`LEADER_TYPE`=39 OR `LEADER_TYPE`=40)  ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC ");
        // $s_mngr=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`,
        //     `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name` SLGP_NAME,`acmp_name`,`SPRO_IMG`,zone_name ZONE_NAME
        //     FROM `filter_leaderboard`
        //     WHERE (`LEADER_TYPE`=49 OR `LEADER_TYPE`=50) ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC ");
        // $s_hero=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`,
        //     `TGT_AMOUNT`,`ACHV_AMOUNT`,`slgp_name` SLGP_NAME,`acmp_name` ACMP_NAME,`SPRO_IMG`,zone_name ZONE_NAME
        //     FROM `filter_leaderboard`
        //     WHERE `LEADER_TYPE`=99  ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC ");
        // $hero=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`,`TGT_AMOUNT`,`ACHV_AMOUNT`,`SPRO_IMG`,
        //         `slgp_name` SLGP_NAME,`acmp_name` ACMP_NAME,zone_name ZONE_NAME
        //         FROM `filter_leaderboard`
        //         WHERE `LEADER_TYPE`=100  ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //         "ORDER BY (`ACHV_AMOUNT`)DESC ");
        // $bst_cnt_s_ldr=DB::connection($this->db)->select("SELECT `STAFFID`,`SPRO_IMG`,`EMPLOYEENAME`,acmp_name ACMP_NAME,slgp_name SLGP_NAME,ACHV_AMOUNT,ACHV_PERCENTAGE
        //         FROM `filter_leaderboard` WHERE `LEADER_TYPE`=15 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 . "ORDER BY ACHV_AMOUNT DESC");
        // $bst_cnt_sf_ldr=DB::connection($this->db)->select("SELECT `STAFFID`,`SPRO_IMG`,`EMPLOYEENAME`,acmp_name ACMP_NAME,slgp_name SLGP_NAME,ACHV_AMOUNT,ACHV_PERCENTAGE
        //         FROM `filter_leaderboard` WHERE `LEADER_TYPE`=18 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 . "ORDER BY ACHV_AMOUNT DESC");
        //$bst_cnt_s_ldr=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,1]);
        //$bst_cnt_sf_ldr=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,2]);
        //$hatrick=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,3]);
    //     return view('LeaderBoard.own_unit_filter_leaders_primary',
    //         ['s_mngr'=>$s_mngr,'s_hero'=>$s_hero,'hero'=>$hero]);
    // }else{
        if($acmp_id !=''){
            $q1="AND COMPANY_ID IN (".implode(',',$acmp_id).")";
        }
        if($slgp_id !=''){
            $q2="AND DIGR_TEXT IN (".implode(',',$slgp_id).")";
        }
        if($zone_id !=''){
            $q3="AND ZONE_ID IN (".implode(',',$zone_id).")";
        }
        // $super_ldr=DB::connection($this->db)->select("SELECT `STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME` ACMP_NAME,`GROUP_NAME` SLGP_NAME,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
        //     FROM `tbld_new_leaderboard_ss`
        //     WHERE `LEADER_TYPE`=1 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC");
        // $bst_cnt_s_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
        //     FROM `tbld_new_leaderboard_ss`
        //     WHERE `LEADER_TYPE`=1 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC");
        // $bst_cnt_s_ldr='';
        // $bst_cnt_sf_ldr='';
        // $sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME` SLGP_NAME,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
        //     FROM `tbld_new_leaderboard_ss`
        //     WHERE `LEADER_TYPE`=2 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        //     "ORDER BY (`ACHV_AMOUNT`) DESC");
        // $bst_cnt_sf_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
        //     FROM `tbld_new_leaderboard_ss`
        //     WHERE `LEADER_TYPE`=2 ".$q." AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)". $q1 . $q2 . $q3 .
        // "ORDER BY (`ACHV_AMOUNT`) DESC ");
        // $hatrick=DB::connection($this->db)->select("SELECT		 
        //     `STAFFID`,`EMPLOYEENAME`,
        //     `MOBILE`,`PHOTO`,`COMPANY_NAME`,
        //     `ZONE_NAME`,GROUP_NAME,
        //     SUM(`TGT_AMOUNT`) AS TGT_AMOUNT,
        //     SUM(ACHV_AMOUNT) AS ACHV_AMOUNT,
        //     `SPRO_IMG`, max(ACHV_PERCENTAGE)ACHV_PERCENTAGE
        //     FROM `tbld_new_leaderboard_ss`
        //     WHERE `LEADER_TYPE`=3 AND `TGT_AMOUNT`>=5000 AND `ACHV_PERCENTAGE`>=98 
        //     ".$q."
        //     AND S_MONTH<=MONTH(CURDATE()-INTERVAL 1 MONTH)
        //     AND S_MONTH>=MONTH(CURDATE()-INTERVAL 3 MONTH)". $q1 . $q2 . $q3."
        //     GROUP BY `STAFFID`,`EMPLOYEENAME`,
        //     `MOBILE`,`PHOTO`,`COMPANY_NAME`,GROUP_NAME,
        //     `ZONE_NAME`,SPRO_IMG
        //     HAVING COUNT(STAFFID) = 3
        //     ORDER BY ACHV_AMOUNT DESC");
        $s_mngr=DB::connection($this->db)->select("SELECT ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME SLGP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=3 ".$q." AND S_MONTH=month(curdate()-interval 1 month) ". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC");
        $s_hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME SLGP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month)". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC LIMIT 100");
        $hero=DB::connection($this->db)->select("SELECT oid,ID,STAFFID ,EMPLOYEENAME,MOBILE,EMAIL,DESIGNATION,PHOTO,COMPANY_ID,COMPANY_NAME,GROUP_NAME SLGP_NAME,ZONE_ID,ZONE_NAME,TGT_AMOUNT,ACHV_AMOUNT
            ,ACHV_PERCENTAGE,P_SALES_AMOUNT,SPRO_IMG FROM  tbld_new_leaderboard_ss 
            WHERE  LEADER_TYPE=4 ".$q." AND S_MONTH=month(curdate()-interval 1 month)". $q1 . $q2 . $q3." ORDER BY `ACHV_AMOUNT` DESC LIMIT 100,30000");
        
            
        return view('LeaderBoard.own_unit_filter_leaders_primary', ['s_mngr'=>$s_mngr,'s_hero'=>$s_hero,'hero'=>$hero]);


    //}
}
// public function getOwnUnitLeaders(){
     
//     $super_ldr=DB::connection($this->db)->select("SELECT `oid`,`STAFFID`,`EMPLOYEENAME`, `TGT_AMOUNT`,`ACHV_AMOUNT`,`COMPANY_NAME`,`GROUP_NAME`,`SPRO_IMG`,ZONE_NAME,ACHV_PERCENTAGE
//     FROM `tbld_new_leaderboard_ss`
//     WHERE `LEADER_TYPE`=1 AND S_YEAR='2022' AND S_MONTH=MONTH(CURDATE()-INTERVAL 1 MONTH)
//     ORDER BY (`ACHV_AMOUNT`) DESC");
//     return view('LeaderBoard.own_unit_filter_leaders_primary',['super_ldr'=>$super_ldr]);
// }
}
