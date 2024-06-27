<?php

namespace App\Http\Controllers\Report\Advance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
class CommonReportAdvHelperController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    public function __construct()
    {
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
    public function getEmpId($aemp_usnm){
        $emp=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
        return $emp->id;
    }
    public function getCategoryWiseNoteDetails($aemp_usnm,$start_date,$end_date){
            $aemp_id=$this->getEmpId($aemp_usnm);
            // $data=DB::connection($this->db)->select("SELECT 
            //         t1.note_date,t1.note_dtim,concat(t4.site_code,'-',t4.site_name)site_name,t1.note_titl,t1.note_body,t2.ntpe_name,t3.nimg_imag note_img
            //         FROM tt_note t1
            //         INNER JOIN tm_ntpe t2 ON t1.ntpe_id=t2.id
            //         LEFT JOIN tl_nimg t3 ON t1.id=t3.note_id
            //         LEFT JOIN tm_site t4 ON t1.site_code=t4.site_code
            //         WHERE t1.note_date between '$start_date' AND '$end_date' AND t1.aemp_id=$aemp_id
            //         ");
            $data=DB::connection($this->db)->select("SELECT 
                    t1.id,t1.note_date,t1.note_dtim,concat(t4.site_code,'-',t4.site_name)site_name,t1.note_titl,t1.note_body,t2.ntpe_name,max(t3.nimg_imag) note_img
                    FROM tt_note t1
                    INNER JOIN tm_ntpe t2 ON t1.ntpe_id=t2.id
                    LEFT JOIN tl_nimg t3 ON t1.id=t3.note_id
                    LEFT JOIN tm_site t4 ON t1.site_code=t4.site_code
                    WHERE t1.note_date between '$start_date' AND '$end_date' AND t1.aemp_id=$aemp_id
                    GROUP BY t1.id,t1.note_date,t1.note_dtim,t4.site_code,t4.site_name,t1.note_titl,t1.note_body,t2.ntpe_name ORDER BY t1.note_dtim ASC;
                    ");
            return $data;
    }
    public function getUserEmailAddress(){
        $user_email=Employee::on($this->db)->find($this->aemp_id);
        return array('email'=>$user_email->aemp_emal);
    }

    public function getZone(Request $request){
        try{
            $aemp_id=$this->aemp_id;
            $dirg_list=$request->dirg_id;
            return DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission` WHERE `aemp_id`='$aemp_id' and dirg_id IN (".implode(',',$dirg_list).") ORDER BY zone_code,zone_name");
        }
        catch(\Exception $e){
            return $data=array();
        }
        
    }
    public function getSRList(Request $request){
        $slgp_id=$request->slgp_id;
        $dirg_id=$request->dirg_id;
        $zone_id=$request->zone_id;
        $filter_query='';
        if($dirg_id){
            $filter_query.=" AND t2.dirg_id IN (".implode(',',$dirg_id).") ";
        }
        if($zone_id){
            $filter_query.=" AND t2.id IN (".implode(',',$zone_id).")";
        }
        if($slgp_id){
            $filter_query.=" AND t1.slgp_id IN (".implode(',',$slgp_id).") ";
        }
        $data=DB::connection($this->db)->select("
                SELECT 
                t1.id,t1.aemp_usnm,t1.aemp_name
                FROM tm_aemp t1
                INNER JOIN tm_zone t2  ON t1.zone_id=t2.id
                WHERE  t1.lfcl_id=1 AND t1.role_id=1 " .$filter_query. "  
                ORDER BY aemp_usnm,aemp_name           
            ");
        return $data;
    }
    public function getSRListofActivityReport(Request $request){
        $q1="";
        $q2="";
        $q3="";
        $q4="";
        $slgp_id=$request->slgp_id;
        $dist_id=$request->dist_id;
        $than_id=$request->than_id;
        $usr_type=$request->usr_type;
        if($slgp_id !=''){
            $q3=" AND t1.slgp_id IN (".implode(',',$slgp_id).")";
        }
        if($dist_id !=''){
            $q1=" AND t4.id =$dist_id";
        }
        if($than_id !=''){
            $q2=" AND t3.id =$than_id";
        }
        if($request->rpt_type=='activity_summary'){
            if($dist_id !='' || $than_id !=''){
                $data=DB::connection($this->db)->select("
                SELECT 
                t2.aemp_usnm,t2.id,t2.aemp_name
                FROM 
                (SELECT t1.id,t1.aemp_mngr from tm_aemp t1
                LEFT JOIN tl_srth t2 ON t1.id=t2.aemp_id
                LEFT JOIN tm_than t3 ON t2.than_id=t3.id
                LEFT JOIN tm_dsct t4 ON t3.dsct_id=t4.id
                where t1.lfcl_id=1 AND t1.role_id=1 ".$q3.$q4.$q2.$q1. "
                GROUP BY t1.id,t1.aemp_mngr)t1
                INNER JOIN tm_aemp t2 ON t1.aemp_mngr=t2.id
                WHERE t2.lfcl_id=1
                GROUP BY t2.aemp_usnm,t2.id,t2.aemp_name           
                ");

            }
            else{
                $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.id,t1.aemp_name from tm_aemp t1
                where t1.lfcl_id=1 AND t1.role_id>1 AND t1.role_id<=6 ". $q3. "
                GROUP BY t1.id,aemp_name,t1.aemp_usnm ORDER BY t1.role_id DESC");
            }
            
            return $data;
        }
        $data=DB::connection($this->db)->select("SELECT t1.aemp_usnm,t1.id,t1.aemp_name from tm_aemp t1
                LEFT JOIN tl_srth t2 ON t1.id=t2.aemp_id
                LEFT JOIN tm_than t3 ON t2.than_id=t3.id
                LEFT JOIN tm_dsct t4 ON t3.dsct_id=t4.id
                where t1.lfcl_id=1 AND t1.role_id=1 ".$q3.$q4.$q2.$q1. "
                GROUP BY t1.id,aemp_name,t1.aemp_usnm");
        return $data;
         
    }
    public function getAllTrip(Request $request){
        $filter='';
        $date=$request->trip_date;
        $depo_id=$request->depo_id;
        if($date){
            $filter.=" AND TRIP_DATE='$date' ";
        }
        if($depo_id){
            $filter.=" AND DEPOT_ID='$depo_id' ";
        }
        $trip_list=DB::connection($this->db)->select("SELECT TRIP_NO,DM_ID FROM `dm_trip` WHERE 1 ". $filter ." ORDER BY id;"); 
        return $trip_list;
    }
   
}
