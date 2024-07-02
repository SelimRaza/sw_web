<?php
/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\BusinessObject\SpaceMaintainShowcase;
use App\BusinessObject\SpaceMaintain;
use App\BusinessObject\SpaceZone;
use App\BusinessObject\SpaceSite;
use App\DataExport\SiteMappingWithSpace;
use App\MasterData\SKU;
use App\MasterData\Site;
use App\MasterData\Employee;

class SpaceManagement extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
 
    public function getAllSpaceProgram(Request $request)
    {
        //return $request->country_id;
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            
            // Auto increment id
            $emp_id=$request->emp_id;
            $employee_obj=Employee::on($db_conn)->find($emp_id);
            $sp_program=array();
            $all_sp=DB::connection($db_conn)->select("Select 
                    t1.id spcm_id,t1.spcm_name,t1.spcm_code,t1.spcm_sdat,t1.spcm_exdt spcm_edat,t1.spft_id,t1.gift_is_national,t1.amnt_is_national,
                    IF(t1.spcm_qyfr=2,1,0) is_fitm,attr4 is_national,t1.spft_amnt,t1.spcm_imge
                    FROM tm_spcm t1 WHERE t1.spcm_exdt>=curdate() AND lfcl_id=1");
            for($i=0;$i<count($all_sp);$i++){
                // Is National
                $spcm_id=$all_sp[$i]->spcm_id;
                if($all_sp[$i]->is_national==1){
                    $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                        ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                        ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                        ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                        ->get();
                    $gift_item='';
                    $gift_amnt=0;
                    if($all_sp[$i]->gift_is_national==1){
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$spcm_id)
                                ->get();
                    }else{
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                ->where('t3.aemp_id',$emp_id)
                                ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                ->get();
                    }
                    if($all_sp[$i]->amnt_is_national==1){
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }else{
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }
                    $single_space=array(
                        'spcm_id'=>$all_sp[$i]->spcm_id,
                        'spcm_name'=>$all_sp[$i]->spcm_name,
                        'spcm_code'=>$all_sp[$i]->spcm_code,
                        'spcm_image'=>$all_sp[$i]->spcm_imge,
                        'start_date'=>$all_sp[$i]->spcm_sdat,
                        'end_date'=>$all_sp[$i]->spcm_edat,
                        'spcm_item'=>$sp_itm,
                        'gift_item'=>$gift_item,
                        'gift_amnt'=>$gift_amnt
                    );
                    array_push($sp_program,$single_space);
                }
                // Particular Zone Wise
                else{
                    $id=$all_sp[$i]->spcm_id;
                    $check=DB::connection($db_conn)->select("Select t1.zone_id
                            FROM tl_sgsm t1
                            INNER JOIN tl_spaz t2 ON t1.zone_id=t2.zone_id
                            WHERE t2.spcm_id={$id} AND t1.aemp_id={$request->emp_id}  ");
                    
                    if($check){
                        $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                                ->where('t1.spcm_id',$id)
                                ->get();
                                $gift_item='';
                                $gift_amnt=0;
                        if($all_sp[$i]->gift_is_national==1){
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$id)
                                    ->get();
                        }else{
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$id)
                                    ->where('t3.aemp_id',$emp_id)
                                    ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                    ->get();
                        }
                        if($all_sp[$i]->amnt_is_national==1){
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }else{
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }
                        $single_space=array(
                            'spcm_id'=>$all_sp[$i]->spcm_id,
                            'spcm_name'=>$all_sp[$i]->spcm_name,
                            'spcm_code'=>$all_sp[$i]->spcm_code,
                            'spcm_image'=>$all_sp[$i]->spcm_imge,
                            'start_date'=>$all_sp[$i]->spcm_sdat,
                            'end_date'=>$all_sp[$i]->spcm_edat,
                            'spcm_item'=>$sp_itm,
                            'gift_item'=>$gift_item,
                            'gift_amnt'=>$gift_amnt
                        );
                        array_push($sp_program,$single_space);
                    }
                }
                         
            }
            //sleep(5);
            return array(
                'sp_program'=>$sp_program,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'message'=>'Success'           
            ); 
        }
        catch(\Exception $e){         
            return array(
                'sp_program'=>'',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>501,
                'message'=>'Error'. $e->getMessage()     
            );
        }
        
    }
    // Assign Outlet To Space
    public function assignOutletToSpace(Request $request){
        $request_time=date('Y-m-d h:i:s');    
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $site =  Site::on($db_conn)->where('site_code', $request->site_code)->first();
        $site_id=$site?$site->id:'';
        $message='';
        $status='';
        try{
            if($db_conn && $site_id){
                $exist=SpaceSite::on($db_conn)->where(['spcm_id'=>$request->spcm_id,'site_id'=>$site_id])->first();
                if(!$exist){
                    $spst=new SpaceSite();
                    $spst->setConnection($db_conn);
                    $spst->spcm_id=$request->spcm_id;
                    $spst->site_id=$site_id;
                    $spst->cont_id=$request->country_id;
                    $spst->lfcl_id = 12;
                    $spst->aemp_iusr = $request->emp_id;
                    $spst->aemp_eusr = $request->emp_id;
                    $spst->save();
                    $status=200;
                    $message='Outlet assigned successfully';
                }
                else{
                    $status=200;
                    $message='Outlet already assigned';
                }
                
            }
            else{
                
                $message='Country Or Outlet not found';
                $status=401;
    
                
            }
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>$status,
                'message'=>$message,
            );
        }
        catch(\Exception $e){
            $status=500;
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>$status,
                'message'=>'Emp_id Can not be null',
            );
        }
        
        
        
    }
    
    public function getRequestedOutletList1(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $role=Employee::on($db_conn)->where(['id'=>$emp_id])->first();
        $role_id=$role->role_id;
        $tmzn=DB::connection($db_conn)->select("Select id FROM tl_tmzn WHERE lfcl_id=1 AND aemp_id={$emp_id} limit 1");
        $data='';
        try{
            if($tmzn){
                if($role_id==2){
                    $data=DB::connection($db_conn)->select("Select t2.id,
                            t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,
                            IF(t4.id=12,1,0)cancelActv,
                            IF(t4.id=12,1,0)verifyActv,
                            IF(t4.id=22,1,0)acceptActv,
                            IF(t4.id=22,1,0)rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            INNER JOIN tl_tmzn t6 ON t5.zone_id=t6.zone_id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t6.lfcl_id=1 AND t6.aemp_id={$emp_id} AND t2.lfcl_id in (12,22)
                            UNION ALL
                            Select t2.id,
                            t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,
                            IF(t4.id=12,1,0)cancelActv,
                            IF(t4.id=12,1,0)verifyActv,
                            IF(t4.id=22,1,0)acceptActv,
                            IF(t4.id=22,1,0)rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t5.aemp_mngr={$emp_id} AND t2.lfcl_id in (12,22)");
                }
                else{
                    $data=DB::connection($db_conn)->select("Select t2.id,
                            t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t3.otcg_id category_id,
                            t3.site_olnm site_name_bn,t3.site_adrs,t3.site_ownm site_owner,t3.site_olad site_adrs_bn,t3.site_imge,t3.geo_lat,t3.geo_lon,
                            t4.id lfcl_id,t4.lfcl_name,t5.role_id,
                            '1' cancelActv,'1' verifyActv,'0' acceptActv,'1' rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            INNER JOIN tl_tmzn t6 ON t5.zone_id=t6.zone_id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t6.lfcl_id=1 AND t6.aemp_id={$emp_id} AND t2.lfcl_id=12
                            ORDER BY t2.id DESC");
                }
            }
            else if($role_id==1){
                $data=DB::connection($db_conn)->select("Select t2.id,
                        t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,t5.role_id,
                        IF(t4.id=12,1,0)cancelActv,
                        0 verifyActv,
                        0 acceptActv,
                        0 rejectActv
                        FROM tm_spcm t1
                        INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                        INNER JOIN tm_site t3 ON t2.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                        WHERE t1.spcm_exdt>=curdate() AND t2.aemp_iusr={$request->emp_id} 
                        ORDER BY t2.id DESC");
            }else{
                $data=DB::connection($db_conn)->select("Select t2.id,
                        t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,t5.role_id,
                        0 cancelActv,
                        0 verifyActv,
                        IF(t4.id=22,1,0)acceptActv,
                        IF(t4.id=22,1,0)rejectActv
                        FROM tm_spcm t1
                        INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                        INNER JOIN tm_site t3 ON t2.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                        WHERE t1.spcm_exdt>=curdate() AND  t5.aemp_mngr={$request->emp_id} AND t2.lfcl_id=22
                        ORDER BY t2.id DESC");
            }
            
            return array(
                'requested_outlet'=>$data,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>200,
                'message'=>'Success',
            );
        }
        catch(\Exception $e){
            return array(
                'requested_outlet'=>'',
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>500,
                'message'=>'Error'.$e->getMessage(),
            );
        }    
    }
    // Get Requested Site Status
    public function getRequestedOutletList(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $role=Employee::on($db_conn)->where(['id'=>$emp_id])->first();
        $role_id=$role->role_id;
        $tmzn=DB::connection($db_conn)->select("Select id FROM tl_tmzn WHERE lfcl_id=1 AND aemp_id={$emp_id} limit 1");
        $data='';
        try{
            $sp_program=array();
            if($tmzn){
                if($role_id==2){
                    $all_sp=DB::connection($db_conn)->select("Select t2.id,
                            t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,
                            IF(t4.id=12,1,0)cancelActv,
                            IF(t4.id=12,1,0)verifyActv,
                            IF(t4.id=22,1,0)acceptActv,
                            IF(t4.id=22,1,0)rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            INNER JOIN tl_tmzn t6 ON t5.zone_id=t6.zone_id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t6.lfcl_id=1 AND t6.aemp_id={$emp_id} AND t2.lfcl_id in (12,22)
                            UNION ALL
                            Select t2.id,
                            t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,
                            IF(t4.id=12,1,0)cancelActv,
                            IF(t4.id=12,1,0)verifyActv,
                            IF(t4.id=22,1,0)acceptActv,
                            IF(t4.id=22,1,0)rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t5.aemp_mngr={$emp_id} AND t2.lfcl_id in (12,22)");
                    for($i=0;$i<count($all_sp);$i++){
                        $spcm_id=$all_sp[$i]->spcm_id;
                        $aemp_iusr=$all_sp[$i]->aemp_iusr;
                        $employee_obj=Employee::on($db_conn)->find($aemp_iusr);
                        $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                                ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                ->get();
                        $gift_item='';
                        $gift_amnt=0;
                        if($all_sp[$i]->gift_is_national==1){
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$spcm_id)
                                    ->get();
                        }else{
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                    ->where('t3.aemp_id',$aemp_iusr)
                                    ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                    ->get();
                        }
                        if($all_sp[$i]->amnt_is_national==1){
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }else{
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }
                        $single_space=array(
                            'id'                                        =>$all_sp[$i]->id,
                            'spcm_id'                                   =>$all_sp[$i]->spcm_id,
                            'spcm_name'                                 =>$all_sp[$i]->spcm_name,
                            'spcm_code'                                 =>$all_sp[$i]->spcm_code,
                            'start_date'                                =>$all_sp[$i]->spcm_sdat,
                            'end_date'                                  =>$all_sp[$i]->end_date,
                            'site_name'                                 =>$all_sp[$i]->site_name,
                            'site_name_bn'                              =>$all_sp[$i]->site_name_bn,
                            'site_code'                                 =>$all_sp[$i]->site_code,
                            'site_mob1'                                 =>$all_sp[$i]->site_mob1,
                            'category_id'                               =>$all_sp[$i]->category_id,
                            'site_adrs'                                 =>$all_sp[$i]->site_adrs,
                            'site_adrs_bn'                              =>$all_sp[$i]->site_adrs_bn,
                            'site_owner'                                =>$all_sp[$i]->site_owner,
                            'site_imge'                                 =>$all_sp[$i]->site_imge,
                            'geo_lat'                                   =>$all_sp[$i]->geo_lat,
                            'geo_lon'                                   =>$all_sp[$i]->geo_lon,
                            'lfcl_id'                                   =>$all_sp[$i]->lfcl_id,
                            'lfcl_name'                                 =>$all_sp[$i]->lfcl_name,
                            'role_id'                                   =>$role_id,
                            'cancelActv'                                =>$all_sp[$i]->cancelActv,
                            'verifyActv'                                =>$all_sp[$i]->verifyActv,
                            'acceptActv'                                =>$all_sp[$i]->acceptActv,
                            'rejectActv'                                =>$all_sp[$i]->rejectActv,
                            'spcm_item'                                 =>$sp_itm,
                            'gift_item'                                 =>$gift_item,
                            'gift_amnt'                                 =>$gift_amnt,
                        );
                       // array_push($sp_program,$single_space);

                    }
                }
                else{
                    $all_sp=DB::connection($db_conn)->select("Select t2.id,t1.id spcm_id,t1.gift_is_national,t1.amnt_is_national,
                            t1.spcm_name,t1.spcm_code,t1.spcm_sdat,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t3.otcg_id category_id,
                            t3.site_olnm site_name_bn,t3.site_adrs,t3.site_ownm site_owner,t3.site_olad site_adrs_bn,t3.site_imge,t3.geo_lat,t3.geo_lon,
                            t4.id lfcl_id,t4.lfcl_name,'$role_id',t2.aemp_iusr,
                            '0' cancelActv,'1' verifyActv,'0' acceptActv,'1' rejectActv
                            FROM tm_spcm t1
                            INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                            INNER JOIN tm_site t3 ON t2.site_id=t3.id
                            INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                            INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                            INNER JOIN tl_tmzn t6 ON t5.zone_id=t6.zone_id
                            WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t6.lfcl_id=1 AND t6.aemp_id={$emp_id} AND t2.lfcl_id=12
                            ORDER BY t2.id DESC");
                    for($i=0;$i<count($all_sp);$i++){
                        $spcm_id=$all_sp[$i]->spcm_id;
                        $aemp_iusr=$all_sp[$i]->aemp_iusr;
                        $employee_obj=Employee::on($db_conn)->find($aemp_iusr);
                        $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                                ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                ->get();
                        $gift_item='';
                        $gift_amnt=0;
                        if($all_sp[$i]->gift_is_national==1){
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$spcm_id)
                                    ->get();
                        }else{
                            $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                    ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                    ->where('t3.aemp_id',$aemp_iusr)
                                    ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                    ->get();
                        }
                        if($all_sp[$i]->amnt_is_national==1){
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }else{
                            $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                            $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                        }
                        $single_space=array(
                            'id'                                        =>$all_sp[$i]->id,
                            'spcm_id'                                   =>$all_sp[$i]->spcm_id,
                            'spcm_name'                                 =>$all_sp[$i]->spcm_name,
                            'spcm_code'                                 =>$all_sp[$i]->spcm_code,
                            'start_date'                                =>$all_sp[$i]->spcm_sdat,
                            'end_date'                                  =>$all_sp[$i]->end_date,
                            'site_name'                                 =>$all_sp[$i]->site_name,
                            'site_name_bn'                              =>$all_sp[$i]->site_name_bn,
                            'site_code'                                 =>$all_sp[$i]->site_code,
                            'site_mob1'                                 =>$all_sp[$i]->site_mob1,
                            'category_id'                               =>$all_sp[$i]->category_id,
                            'site_adrs'                                 =>$all_sp[$i]->site_adrs,
                            'site_adrs_bn'                              =>$all_sp[$i]->site_adrs_bn,
                            'site_owner'                                =>$all_sp[$i]->site_owner,
                            'site_imge'                                 =>$all_sp[$i]->site_imge,
                            'geo_lat'                                   =>$all_sp[$i]->geo_lat,
                            'geo_lon'                                   =>$all_sp[$i]->geo_lon,
                            'lfcl_id'                                   =>$all_sp[$i]->lfcl_id,
                            'lfcl_name'                                 =>$all_sp[$i]->lfcl_name,
                            'role_id'                                   =>$role_id,
                            'cancelActv'                                =>$all_sp[$i]->cancelActv,
                            'verifyActv'                                =>$all_sp[$i]->verifyActv,
                            'acceptActv'                                =>$all_sp[$i]->acceptActv,
                            'rejectActv'                                =>$all_sp[$i]->rejectActv,
                            'spcm_item'                                 =>$sp_itm,
                            'gift_item'                                 =>$gift_item,
                            'gift_amnt'                                 =>$gift_amnt,
                        );
                        array_push($sp_program,$single_space);

                    }
                }
            }
            else if($role_id==1){
                $data=DB::connection($db_conn)->select("Select t2.id,
                        t1.spcm_name,t1.spcm_code,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t4.id lfcl_id,t4.lfcl_name,t5.role_id,
                        IF(t4.id=12,1,0)cancelActv,
                        0 verifyActv,
                        0 acceptActv,
                        0 rejectActv
                        FROM tm_spcm t1
                        INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                        INNER JOIN tm_site t3 ON t2.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                        WHERE t1.spcm_exdt>=curdate() AND t2.aemp_iusr={$request->emp_id} 
                        ORDER BY t2.id DESC");
                return array(
                    'requested_outlet'=>$data,
                    'request_time'=>$request_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>200,
                    'message'=>'Success',
                );
            }else{
                $all_sp=DB::connection($db_conn)->select("Select t2.id,t1.id spcm_id,t1.gift_is_national,t1.amnt_is_national,
                        t1.spcm_name,t1.spcm_code,t1.spcm_sdat,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t3.otcg_id category_id,
                        t3.site_olnm site_name_bn,t3.site_adrs,t3.site_ownm site_owner,t3.site_olad site_adrs_bn,t3.site_imge,t3.geo_lat,t3.geo_lon,
                        t4.id lfcl_id,t4.lfcl_name,t5.role_id,t2.aemp_iusr,t6.otcg_name,
                        0 cancelActv,
                        0 verifyActv,
                        IF(t4.id=22,1,0)acceptActv,
                        IF(t4.id=22,1,0)rejectActv,
                        (CASE WHEN t2.acc_type=1 THEN 'bKash' ELSE 'N/A' END) acc_type,
                        (CASE WHEN t2.acc_type=2 THEN 'Rocket' ELSE 'N/A' END) acc_type,
                        (CASE WHEN t2.acc_type=3 THEN 'Nagad' ELSE 'N/A' END) acc_type
                        FROM tm_spcm t1
                        INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                        INNER JOIN tm_site t3 ON t2.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                        INNER JOIN tm_otcg t6 ON t3.otcg_id=t6.id
                        WHERE t1.spcm_exdt>=curdate() AND  t5.aemp_mngr={$request->emp_id} AND t2.lfcl_id=22
                        ORDER BY t2.id DESC");
                for($i=0;$i<count($all_sp);$i++){
                    $spcm_id=$all_sp[$i]->spcm_id;
                    $spst_id=$all_sp[$i]->id;
                    $aemp_iusr=$all_sp[$i]->aemp_iusr;
                    $employee_obj=Employee::on($db_conn)->find($emp_id);
                    $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                            ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                            ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                            ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                            ->get();
                    $gift_item='';
                    $gift_amnt=0;
                    if($all_sp[$i]->gift_is_national==1){
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$spcm_id)
                                ->get();
                    }else{
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                ->where('t3.aemp_id',$aemp_iusr)
                                ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                ->get();
                    }
                    if($all_sp[$i]->amnt_is_national==1){
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }else{
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }
                    $approve_item=DB::connection($db_conn)->table('tl_spgt AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as apr_quantity')
                                    ->where('t1.spst_id',$spst_id)
                                    ->first();
                    $ap_id=$approve_item?$approve_item->item_id:'';
                    $ap_name=$approve_item?$approve_item->item_name:'';
                    $ap_code=$approve_item?$approve_item->item_code:'';
                    $apr_qty=$approve_item?$approve_item->apr_quantity:'';
                    $approve_amnt=DB::connection($db_conn)->table('tl_spgt AS t1')->select('t1.gift_amnt')->where('t1.spst_id',$spst_id)->first();
                    $amnt=$approve_amnt?$approve_amnt->gift_amnt:0;
                    $single_space=array(
                        'id'                                        =>$all_sp[$i]->id,
                        'spcm_id'                                   =>$all_sp[$i]->spcm_id,
                        'spcm_name'                                 =>$all_sp[$i]->spcm_name,
                        'spcm_code'                                 =>$all_sp[$i]->spcm_code,
                        'start_date'                                =>$all_sp[$i]->spcm_sdat,
                        'end_date'                                  =>$all_sp[$i]->end_date,
                        'site_name'                                 =>$all_sp[$i]->site_name,
                        'site_name_bn'                              =>$all_sp[$i]->site_name_bn,
                        'site_code'                                 =>$all_sp[$i]->site_code,
                        'site_mob1'                                 =>$all_sp[$i]->site_mob1,
                        'category_id'                               =>$all_sp[$i]->category_id,
                        'site_adrs'                                 =>$all_sp[$i]->site_adrs,
                        'site_adrs_bn'                              =>$all_sp[$i]->site_adrs_bn,
                        'site_owner'                                =>$all_sp[$i]->site_owner,
                        'site_imge'                                 =>$all_sp[$i]->site_imge,
                        'geo_lat'                                   =>$all_sp[$i]->geo_lat,
                        'geo_lon'                                   =>$all_sp[$i]->geo_lon,
                        'lfcl_id'                                   =>$all_sp[$i]->lfcl_id,
                        'lfcl_name'                                 =>$all_sp[$i]->lfcl_name,
                        'role_id'                                   =>$role_id,
                        'cancelActv'                                =>$all_sp[$i]->cancelActv,
                        'verifyActv'                                =>$all_sp[$i]->verifyActv,
                        'acceptActv'                                =>$all_sp[$i]->acceptActv,
                        'rejectActv'                                =>$all_sp[$i]->rejectActv,
                        'spcm_item'                                 =>$sp_itm,
                        'gift_item'                                 =>$gift_item,
                        'gift_amnt'                                 =>$gift_amnt,
                        'request_item'                              =>$approve_item,
                        'request_amnt'                              =>$amnt,
                        'apr_item_id'                               =>$ap_id,
                        'apr_item_code'                             =>$ap_code,
                        'apr_item_name'                             =>$ap_name,
                        'apr_item_qty'                              =>$apr_qty,
                        'account_type'                              =>$all_sp[$i]->acc_type,
                        'category_name'                             =>$all_sp[$i]->otcg_name
                    );
                    array_push($sp_program,$single_space);
                }
            }
            
            return array(
                'requested_outlet'=>$sp_program,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>200,
                'message'=>'Success',
            );
        }
        catch(\Exception $e){
            return array(
                'requested_outlet'=>'',
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>501,
                'message'=>'Error'.$e->getMessage(),
            );
        }    
    }

    // Remove Outlet From Space
    public function removeOutletFromSpace(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $site =  Site::on($db_conn)->where('site_code', $request->site_code)->first();
        $site_id=$site?$site->id:'';
        $message='';
        $status='';
        try{
            if($db_conn && $site_id){
               // $exist=SpaceSite::on($db_conn)->where(['spcm_id'=>$request->spcm_id,'site_id'=>$site_id,'aemp_iusr'=>$request->emp_id,'lfcl_id'=>12])->first();
                $exist=DB::connection($db_conn)->select("Select 
                        t1.id
                        FROM tl_spst t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_iusr=t2.id
                        WHERE t1.spcm_id={$request->spcm_id} AND t1.site_id={$site_id} AND (t1.aemp_iusr={$request->emp_id} OR t2.aemp_mngr={$request->emp_id});");
                if($exist){
                    $id=$exist[0]->id;
                    $sp_site=SpaceSite::on($db_conn)->find($id)->first();
                    $sp_site->lfcl_id=24;
                    $sp_site->aemp_eusr=$request->emp_id;
                    $sp_site->save();
                    $status=200;
                    $message='Outlet removed from Space Program';
                }
                else{
                
                    $message='Outlet not found Or permission denied';
                    $status=200;   
                }
                
            }
            else{
                
                $message='Outlet not found Or permission denied';
                $status=500;
    
                
            }
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>$status,
                'message'=>$message,
            );
        }
        catch(\Exception $e){
            $status=500;
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>$status,
                'message'=>'Error',
            );
        }
          
    }

    public function outletAcceptRejectCancel(Request $request){
        // country_id,$emp_id,$id,$lfcl_id
        // Cancel OR Reject 24, Accept 1
        $request_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country?$country->cont_conn:'';
            $emp_id=$request->emp_id;
            $lfcl_id=$request->lfcl_id;
            $id=$request->id;
            $sp_site=SpaceSite::on($db_conn)->where(['id'=>$id])->first();
            if($lfcl_id==0){
                $sp_site->delete();
            }
            if($lfcl_id==1){
                $employee_obj=Employee::on($db_conn)->find($emp_id);
                $emp_zone_id=$employee_obj->zone_id;
                $zone_assign_total_site=DB::connection($db_conn)->select("SELECT count(site_id) total_site  FROM tl_spst WHERE attr3={$emp_zone_id} AND spcm_id={$sp_site->spcm_id} AND lfcl_id=1");
                $zone_limit=DB::connection($db_conn)->select("SELECT max_approve FROM `tl_spaz` WHERE spcm_id={$sp_site->spcm_id} AND zone_id={$emp_zone_id};");
                $zone_limit=$zone_limit->max_approve??0;
                $zone_approved=$zone_assign_total_site->total_site??0;
                if($zone_approved<$zone_limit){
                    $sp_site->lfcl_id=$lfcl_id;
                    $sp_site->aemp_eusr=$emp_id;
                    $sp_site->attr3=$emp_zone_id;
                    $sp_site->save();
                }
                else{
                    return array(
                        'request_time'=>$request_time,
                        'response_time'=>date('Y-m-d h:i:s'),
                        'success'=>0,
                        'status'=>422,
                        'message'=>'Your limit exceed!!!',
                    );
                }



            }
            else{
                $sp_site->lfcl_id=$lfcl_id;
                $sp_site->aemp_eusr=$emp_id;
                $sp_site->save();
            }
            
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'success'=>1,
                'status'=>200,
                'message'=>'success',
            );
        }
        catch(\Exception $e){
            return array(
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>500,
                'success'=>0,
                'message'=>'success',
            );
        }
        



    }


    public function updateOutlet(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $id=$request->id;
        $site_mobile=$request->outlet_mob1;
        $site_name=$request->outlet_name;
        $site_olnm=$request->outlet_name1;
        $site_ownm=$request->outlet_owner;
        $site_adrs=$request->outlet_adrs;
        $site_olad=$request->outlet_adrs1;
        $otcg_id=$request->outlet_cat;
        $geo_lat=$request->geo_lat;
        $geo_lon=$request->geo_lon;
        $site_imge=$request->outlet_img;
        $gift_item=$request->gift_item;
        $gift_amnt=$request->gift_amnt;
        $is_item=$request->is_item;
        if($is_item==1){
            $gift_amnt=0;
        }
        $account_type=$request->account_type;
        $gift_qty=$request->gift_qty;
        $spst=SpaceSite::on($db_conn)->where(['id'=>$id])->first();
        if(!$spst){
          return   array(
                'success' => 0,
                'status'=>403,
                'message' => "Space Request Not Found",
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
            );
        }
        $spcm_id=$spst->spcm_id;
        DB::beginTransaction();
        try{
            $spst_check=DB::connection($db_conn)->select("SELECT * FROM  tl_spst WHERE spcm_id={$spcm_id} AND site_mob1={$site_mobile}");
            if(!$spst_check){
                DB::connection($db_conn)->select("
                Update tm_site t1 INNER JOIN tl_spst t2 ON t1.id=t2.site_id 
                SET t1.site_mob1={$site_mobile},
                t1.site_name='$site_name',
                t1. site_olnm='$site_olnm',
                t1. site_ownm='$site_ownm',
                t1. site_adrs='$site_adrs',
                t1. geo_lat='$geo_lat',
                t1. geo_lon='$geo_lon',
                t1. site_imge='$site_imge',
                t1. otcg_id='$otcg_id',
                t1.site_olad='$site_olad',
                t1.aemp_eusr='$emp_id'
                WHERE t2.id=$id");
                DB::connection($db_conn)->select("Update tl_spst SET site_mob1={$site_mobile},acc_type={$account_type} WHERE id={$id}");
                DB::connection($db_conn)->select("INSERT IGNORE  INTO `tl_spgt`(`spcm_id`, `site_id`, `spst_id`, `amim_id`,`min_qty`, `gift_amnt`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
                VALUES({$spcm_id},{$spst->site_id},{$id},{$gift_item},{$gift_qty},{$gift_amnt},{$request->country_id},1,{$emp_id},{$emp_id})");
                $result_data = array(
                    'success' => 1,
                    'message' => "Outlet Updated Successfully",
                    'status'=>200,
                    'request_time'=>$request_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                );
                return $result_data;
            }
            else{
                $result_data = array(
                    'success' => 0,
                    'message' => "This number is already exist",
                    'status'=>401,
                    'request_time'=>$request_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                );
                return $result_data;
            }
            
        }
        catch (\Exception $e) {
            DB::rollback();
            $result_data = array(
                'success' => 0,
                'status'=>501,
                'message' => "Something Went Wrong".$e->getMessage(),
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
            );
            return $result_data;
        }
        
    }
    public function sendVerificationCode(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $id=$request->id;
        $site_mobile=$request->site_mobile;
        DB::beginTransaction();
        try{
            DB::connection($db_conn)->select("Update tm_site t1 INNER JOIN tl_spst t2 ON t1.id=t2.site_id SET t1.site_mob1={$site_mobile} WHERE t2.id={$id}");
            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            DB::connection($db_conn)->select("Update tl_spst SET attr4={$otp},aemp_eusr={$emp_id} WHERE id={$id}");
            $message = "-Code:" . $otp;
            $cont_name=$country->cont_name;
            $res=$this->sendCode($message,$otp,$site_mobile,$cont_name);
            if ($res == '1') {
                $result_data = array(
                    'success' => 1,
                    'site_mobile' => $site_mobile,
                    'message' => "Please check your registered Mobile for OTP Code ",
                    'status'=>200,
                    'request_time'=>$request_time,
                    'request_time'=>date('Y-m-d h:i:s'),
                );
            }
            else {
                $result_data = array(
                    'success' => 0,
                    'site_mobile' => 0,
                    'status'=>401,
                    'message' => "Failed",
                    'request_time'=>$request_time,
                    'request_time'=>date('Y-m-d h:i:s'),
                );
            }
            return $result_data;
        }
        catch (\Exception $e) {
            DB::rollback();
            $result_data = array(
                'success' => 0,
                'site_mobile' =>0,
                'status'=>500,
                'message' => "Something Went Wrong".$e->getMessage(),
                'request_time'=>$request_time,
                'request_time'=>date('Y-m-d h:i:s'),
            );
            return $result_data;
        }
        
    }
    public function sendCode($message,$otp,$mobile,$cont_name){
        $CURLOPT_URL = 'http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn=' . $mobile . '&masking=' . $cont_name . '&message=OTP' . $message;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $CURLOPT_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: JSESSIONID=5E85DAE3CED0D54107A3A6FB66EA1F77'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $manage = json_decode($response, true);
        $res = $manage['success'];
        return $res;     
    }
    public function verifyRequestedOutlet(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $otp=$request->otp_code;
        $id=$request->id;
        $message='';
        $success=1;
        $exist=SpaceSite::on($db_conn)->where(['id'=>$id,'attr4'=>$otp])->first();
        $status=200;
        if($exist){
            $exist->lfcl_id=22;
            $exist->aemp_eusr=$emp_id;
            $exist->save();
            $message='Outlet Verified Successfully';
        }else{
            $message='Invalid OTP';
            $success=0;
            $status=401;
        }
        $result_data = array(
            'success' =>1,
            'status'=>$status,
            'message' =>$message,
            'request_time'=>$request_time,
            'request_time'=>date('Y-m-d h:i:s'),
        );
        return $result_data;

    }
    public function sendMessage(Request $request){
       // $message="Thanks:--".'Hello-http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn';
        $message="Thanks!".'Your Outlet is Registered';
        $mobile=$request->mobile;
        $cont_name='PRAN';
        $CURLOPT_URL = 'http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn=' . $mobile . '&masking=' . $cont_name . '&message=OTP' . $message;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $CURLOPT_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: JSESSIONID=5E85DAE3CED0D54107A3A6FB66EA1F77'
            ),
             
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $manage = json_decode($response, true);
        //$res = $manage['success'];
        return $manage;     
    }

    public function getOutletListToLoadGoods(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $role=Employee::on($db_conn)->where(['id'=>$emp_id])->first();
        $role_id=$role->role_id;
        try{
            $tmzn=DB::connection($db_conn)->select("Select id FROM tl_tmzn WHERE lfcl_id=1 AND aemp_id={$emp_id} limit 1");
            $sp_program=array();
            if($tmzn){
                $all_sp=DB::connection($db_conn)->select("Select t2.id,t1.id spcm_id,t1.gift_is_national,t1.amnt_is_national,
                        t1.spcm_name,t1.spcm_code,t1.spcm_sdat,t1.spcm_exdt end_date,t3.site_name,t3.site_code,t3.site_mob1,t3.otcg_id category_id,
                        t3.site_olnm site_name_bn,t3.site_adrs,t3.site_ownm site_owner,t3.site_olad site_adrs_bn,t3.site_imge,t3.geo_lat,t3.geo_lon,
                        t4.id lfcl_id,t4.lfcl_name,t5.role_id,t2.aemp_iusr,t6.otcg_name,
                        (CASE WHEN t2.acc_type=1 THEN 'bKash' ELSE 'N/A' END) acc_type,
                        (CASE WHEN t2.acc_type=2 THEN 'Rocket' ELSE 'N/A' END) acc_type,
                        (CASE WHEN t2.acc_type=3 THEN 'Nagad' ELSE 'N/A' END) acc_type
                        FROM tm_spcm t1
                        INNER JOIN tl_spst t2 ON t1.id=t2.spcm_id
                        INNER JOIN tm_site t3 ON t2.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t2.lfcl_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_iusr=t5.id
                        INNER JOIN tm_otcg t6 ON t3.otcg_id=t6.id
                        INNER JOIN tl_tmzn t7 ON t5.zone_id=t7.zone_id
                        WHERE t1.spcm_exdt>=curdate() AND t1.lfcl_id=1 AND t7.lfcl_id=1 AND t7.aemp_id={$emp_id} AND t2.lfcl_id=1 AND t2.status=0
                        ORDER BY t2.id DESC");
                for($i=0;$i<count($all_sp);$i++){
                    $spcm_id=$all_sp[$i]->spcm_id;
                    $spst_id=$all_sp[$i]->id;
                    $aemp_iusr=$all_sp[$i]->aemp_iusr;
                    $employee_obj=Employee::on($db_conn)->find($emp_id);
                    $sp_itm=DB::connection($db_conn)->table('tl_spsb AS t1')
                            ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                            ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as display_quantity')
                            ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                            ->get();
                    $gift_item='';
                    $gift_amnt=0;
                    if($all_sp[$i]->gift_is_national==1){
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$spcm_id)
                                ->get();
                    }else{
                        $gift_item=DB::connection($db_conn)->table('tl_spft AS t1')
                                ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                ->join('tl_sgsm as t3','t1.zone_id','=','t3.zone_id')
                                ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as gift_quantity')
                                ->where('t1.spcm_id',$all_sp[$i]->spcm_id)
                                ->where('t3.aemp_id',$aemp_iusr)
                                ->groupBy('t2.id','t2.amim_name','t2.amim_code','t1.min_qty')
                                ->get();
                    }
                    if($all_sp[$i]->amnt_is_national==1){
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }else{
                        $amnt= DB::connection($db_conn)->select("Select max_amnt from tl_spam WHERE spcm_id={$spcm_id} AND zone_id={$employee_obj->zone_id} limit 1");
                        $gift_amnt=$amnt?$amnt[0]->max_amnt:0;
                    }
                    $approve_item=DB::connection($db_conn)->table('tl_spgt AS t1')
                                    ->join('tm_amim as t2','t1.amim_id','=','t2.id')
                                    ->select('t2.id as item_id','t2.amim_name as item_name','t2.amim_code as item_code','t1.min_qty as apr_quantity')
                                    ->where('t1.spst_id',$spst_id)
                                    ->first();
                    $ap_id=$approve_item?$approve_item->item_id:'';
                    $ap_name=$approve_item?$approve_item->item_name:'';
                    $ap_code=$approve_item?$approve_item->item_code:'';
                    $apr_qty=$approve_item?$approve_item->apr_quantity:'';
                    $approve_amnt=DB::connection($db_conn)->table('tl_spgt AS t1')->select('t1.gift_amnt')->where('t1.spst_id',$spst_id)->first();
                    $amnt=$approve_amnt?$approve_amnt->gift_amnt:0;
                    $single_space=array(
                        'id'                                        =>$all_sp[$i]->id,
                        'spcm_id'                                   =>$all_sp[$i]->spcm_id,
                        'spcm_name'                                 =>$all_sp[$i]->spcm_name,
                        'spcm_code'                                 =>$all_sp[$i]->spcm_code,
                        'start_date'                                =>$all_sp[$i]->spcm_sdat,
                        'end_date'                                  =>$all_sp[$i]->end_date,
                        'site_name'                                 =>$all_sp[$i]->site_name,
                        'site_name_bn'                              =>$all_sp[$i]->site_name_bn,
                        'site_code'                                 =>$all_sp[$i]->site_code,
                        'site_mob1'                                 =>$all_sp[$i]->site_mob1,
                        'category_id'                               =>$all_sp[$i]->category_id,
                        'site_adrs'                                 =>$all_sp[$i]->site_adrs,
                        'site_adrs_bn'                              =>$all_sp[$i]->site_adrs_bn,
                        'site_owner'                                =>$all_sp[$i]->site_owner,
                        'site_imge'                                 =>$all_sp[$i]->site_imge,
                        'geo_lat'                                   =>$all_sp[$i]->geo_lat,
                        'geo_lon'                                   =>$all_sp[$i]->geo_lon,
                        'lfcl_id'                                   =>$all_sp[$i]->lfcl_id,
                        'lfcl_name'                                 =>$all_sp[$i]->lfcl_name,
                        'role_id'                                   =>$role_id,
                        'spcm_item'                                 =>$sp_itm,
                        'gift_item'                                 =>$gift_item,
                        'gift_amnt'                                 =>$gift_amnt,
                        'request_item'                              =>$approve_item,
                        'request_amnt'                              =>$amnt,
                        'apr_item_id'                               =>$ap_id,
                        'apr_item_code'                             =>$ap_code,
                        'apr_item_name'                             =>$ap_name,
                        'apr_item_qty'                              =>$apr_qty,
                        'account_type'                              =>$all_sp[$i]->acc_type,
                        'category_name'                             =>$all_sp[$i]->otcg_name
                    );
                    array_push($sp_program,$single_space);
                }          
            }
            return array(
                'requested_outlet'=>$sp_program,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>200,
                'message'=>'Success',
            );
        }
        catch(\Exception $e){
            return array(
                'requested_outlet'=>0,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>501,
                'message'=>'failed'.$e->getMessage(),
            );
        }
        
        

    }

    public function startProgramByUploadingImage(Request $request){
        $request_time=date('Y-m-d h:i:s');       
        $country = (new Country())->country($request->country_id);
        $db_conn = $country?$country->cont_conn:'';
        $emp_id=$request->emp_id;
        $id=$request->id;
        $date=date('Y-m-d');
        $apply_image=$request->apply_image;
        try{
            $spst=SpaceSite::on($db_conn)->where(['id'=>$id])->first();
            if($spst){
                $spst->apply_image=$apply_image;
                $spst->apply_date=$date;
                $spst->status=1;
                $spst->aemp_eusr=$emp_id;
                $spst->save();
                return array(
                    'success'=>1,
                    'request_time'=>$request_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>200,
                    'message'=>'success',
                );
            }
            else{
                return array(
                    'success'=>0,
                    'request_time'=>$request_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>403,
                    'message'=>'Not Found',
                );
            }
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>501,
                'message'=>'Failed'.$e->getMessage(),
            );
        }
        
        
    }
    // Helper function 



    


}