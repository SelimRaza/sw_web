<?php
/**
 * Created by PhpStorm.
 * User: 329206
 * Date: 19/03/2023
 */

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MasterData\Employee;
use App\MasterData\RplnH;
use App\MasterData\RSMP;
use App\MasterData\Route;
use App\MasterData\SalesGroupZoneBaseMapping;
use App\BusinessObject\SalesGroup;

class SRSetup extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function getPendingSRList(Request $request){
//        dd($request->all());
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $emp_id=$request->emp_id;
            $emp_details=$this->getEmployeeDetails($emp_id,$db_conn);
            $zone_id=$emp_details->zone_id;
            $slgp_id=$emp_details->slgp_id;
            $role_id=$emp_details->role_id;
            //return $emp_details;
            if($role_id==2){
                $data=DB::connection($db_conn)->select("SELECT
                        t1.id sr_id,t1.aemp_usnm,t1.aemp_name,t4.id role_id,t4.role_name,t2.id zone_id,t2.zone_code,t2.zone_name,t3.id slgp_id,t3.slgp_name,t3.slgp_code,'1' is_pending
                        FROM tm_aemp t1
                        INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                        INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                        INNER JOIN tm_role t4 ON t1.role_id=t4.id
                        WHERE  t1.role_id=1 AND t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id}
                        AND t1.id not in (SELECT 
                        DISTINCT t1.id
                        FROM tm_aemp t1
                        INNER JOIN tl_rpln t2 ON t1.id=t2.aemp_id
                        WHERE t1.zone_id={$zone_id} AND t1.slgp_id={$slgp_id}) AND t1.lfcl_id=1
                        UNION ALL
                        SELECT
                        t1.id,t1.aemp_usnm,t1.aemp_name,t4.id role_id,t4.role_name,t2.id zone_id,t2.zone_code,t2.zone_name,t3.id slgp_id,t3.slgp_name,t3.slgp_code,'0' is_pending
                        FROM tm_aemp t1
                        INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                        INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                        INNER JOIN tm_role t4 ON t1.role_id=t4.id
                        WHERE  t1.role_id=1 AND t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id}
                        AND t1.id  in (SELECT 
                        DISTINCT t1.id
                        FROM tm_aemp t1
                        INNER JOIN tl_rpln t2 ON t1.id=t2.aemp_id
                        WHERE t1.zone_id={$zone_id} AND t1.slgp_id={$slgp_id}) AND t1.lfcl_id=1");
                $base_list=DB::connection($db_conn)->select("
                            SELECT 
                            t2.id base_id,t2.base_code,t2.base_name
                            FROM tbl_slgp_zone_base_mapping t1
                            INNER JOIN tm_base t2 ON t1.base_id=t2.id
                            WHERE t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id}
                            GROUP BY t2.id,t2.base_code,t2.base_name
                            ORDER BY t2.base_code ASC;");
                return array(
                    'success'=>1,
                    'message'=>'Success!',
                    'sr_list'=>$data,
                    'base_list'=>$base_list,
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>http_response_code(),

                );
            }
            else{
                return array(
                    'success'=>0,
                    'message'=>'You are not allowed!',
                    'sr_list'=>array(),
                    'base_list'=>array(),
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>422,

                );
            }
            

        }
        catch(\Exception $e){
            $error_messages = [];
            return array(
                'success'=>0,
                'message'=>'Failed to fetch data !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
    }
    public function getDealarList(Request $request){
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $emp_id=$request->emp_id;
            $emp_details=$this->getEmployeeDetails($emp_id,$db_conn);
            $zone_id=$emp_details->zone_id;
            $slgp_id=$emp_details->slgp_id;
            $role_id=$emp_details->role_id;
            $base_id=$request->base_id;
            $role_id=$emp_details->role_id;
            if($role_id==2){
                $data=DB::connection($db_conn)->select("SELECT id dlrm_id,dlrm_name,dlrm_code FROM `tm_dlrm` WHERE slgp_id={$slgp_id} AND base_id={$base_id} ORDER BY dlrm_code ASC");
                return array(
                    'success'=>1,
                    'message'=>'Success!',
                    'dealar_list'=>$data,
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>http_response_code(),

                );
            }
            else {
                return array(
                    'success'=>0,
                    'message'=>'You are not allowed!',
                    'dealar_list'=>array(),
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>422,

                );
            }
                        

        }
        catch(\Exception $e){
            $error_messages = [];
            return array(
                'success'=>0,
                'message'=>'Failed to fetch data !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
    }
    public function getThanaList(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $data=array();
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            //$district_code=$request->district_code;
            $district_code=json_decode($request->district_code,true);
            if($district_code){
                $data=DB::connection($db_conn)->select(" SELECT id,than_code,than_name FROM tm_than WHERE dsct_id IN (".implode(',',$district_code).") ORDER BY than_code ASC");
            }          
            return array(
                'success'=>1,
                'thana_list'=>$data,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),

            ); 

        }
        catch(\Exception $e){
            $error_messages = [];
            return array(
                'success'=>0,
                'message'=>'Failed to fetch data !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
    }
    public function getRouteList(Request $request){
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $emp_id=$request->emp_id;
            $emp_details=$this->getEmployeeDetails($emp_id,$db_conn);
            $zone_id=$emp_details->zone_id;
            $slgp_id=$emp_details->slgp_id;
            $role_id=$emp_details->role_id;
            $base_id=$request->base_id;
            $role_id=$emp_details->role_id;
            $sr_id=$request->sr_id;
            if($role_id==2){
                $data=DB::connection($db_conn)->select("SELECT t2.id rout_id,t2.rout_code,concat(t2.rout_code,'-',t2.rout_name)rout_name,IFNULL(t3.is_assigned,0)is_assigned,
                        t3.assigned_user_id,t3.assigned_user_staff_id,t3.assigned_user_name, IFNULL(t4.is_own,0)is_own,
                        t4.journey_plan cusr_day
                        FROM `tbl_slgp_zone_base_mapping` t1
                            INNER JOIN tm_rout t2 ON t1.rout_id=t2.id
                            LEFT JOIN 
                            (  
                                SELECT  
                                t1.id assigned_user_id,t1.aemp_usnm assigned_user_staff_id,t1.aemp_name assigned_user_name,t2.rout_id, 1 is_assigned
                                FROM tm_aemp t1
                                INNER JOIN tl_rpln t2 ON t1.id=t2.aemp_id
                                WHERE t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id}
                            ) t3 ON t2.id=t3.rout_id
                            LEFT JOIN 
                                (SELECT rout_id,group_concat(rpln_day)journey_plan, 0 is_own
                                    FROM tl_rpln WHERE aemp_id={$sr_id} group by rout_id
                                ) t4 ON t1.rout_id=t4.rout_id
                            WHERE t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id} AND t1.base_id={$base_id}");
                return array(
                    'success'=>1,
                    'message'=>'Success!',
                    'route_list'=>$data,
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>http_response_code(),

                );
            }
            else {
                return array(
                    'success'=>0,
                    'message'=>'You are not allowed!',
                    'route_list'=>array(),
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>422,

                );
            }
                        

        }
        catch(\Exception $e){
            $error_messages = [];
            return array(
                'success'=>0,
                'message'=>'Failed to fetch data !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
    }

    public function getSRData(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        try{
            $sr_id=$request->sr_id;
            $sr_data=DB::connection($db_conn)->select("SELECT 
                    t1.id sr_id,t1.aemp_usnm,t1.aemp_name,t1.aemp_name,t2.zone_code,t2.zone_name,t3.slgp_code,t3.slgp_name
                    FROM tm_aemp t1
                    INNER JOIN tm_zone t2 ON t1.zone_id=t2.id
                    INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                    WHERE t1.id={$sr_id};");
            $base_list=DB::connection($db_conn)->select("SELECT 
                        DISTINCT t3.id base_id,t3.base_code,t3.base_name
                        FROM tl_rpln t1
                        INNER JOIN tbl_slgp_zone_base_mapping t2 ON t1.rout_id=t2.rout_id
                        INNER JOIN tm_base t3 ON t2.base_id=t3.id
                        WHERE t1.aemp_id={$sr_id}");
            $thana_list=DB::connection($db_conn)->select("SELECT 
                        DISTINCT t3.id dsct_id,t3.dsct_code,t3.dsct_name, t2.id than_id,t2.than_code,t2.than_name
                        FROM tl_srth t1
                        INNER JOIN tm_than t2 ON t1.than_id=t2.id
                        INNER JOIN tm_dsct t3 ON t2.dsct_id=t3.id
                        WHERE t1.aemp_id={$sr_id}
                        ORDER BY than_name ASC;");
            $dealar_list=DB::connection($db_conn)->select("SELECT 
                        DISTINCT t2.id dlrm_id,t2.dlrm_code,t2.dlrm_name
                        FROM tl_srdi t1
                        INNER JOIN tm_dlrm t2 ON t1.dlrm_id=t2.id
                        WHERE t1.aemp_id={$sr_id}
                        ORDER BY t2.dlrm_name ASC;");
            $route_list=DB::connection($db_conn)->select("SELECT 
                        DISTINCT t2.id rout_id,t2.rout_code,t2.rout_name,t1.rpln_day
                        FROM tl_rpln t1
                        INNER JOIN tm_rout t2 ON t1.rout_id=t2.id
                        WHERE t1.aemp_id={$sr_id}
                        ORDER BY FIELD(t1.rpln_day , '7','1', '2', '3', '4', '5', '6')");
            return array(
                'success'=>1,
                'message'=>'Success!',
                'sr_id'=>$sr_data[0]->sr_id,
                'aemp_usnm'=>$sr_data[0]->aemp_usnm,
                'aemp_name'=>$sr_data[0]->aemp_name,
                'zone_code'=>$sr_data[0]->zone_code,
                'zone_name'=>$sr_data[0]->zone_name,
                'slgp_code'=>$sr_data[0]->slgp_code,
                'slgp_name'=>$sr_data[0]->slgp_name,
                'base_list'=>$base_list,
                'thana_list'=>$thana_list,
                'dealar_list'=>$dealar_list,
                'route_list'=>$route_list,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),

            );
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Failed to fetch data !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
        

    }
    public function storeSRData(Request $request){
        // return 'ok';
        return $request->all();
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        // if($country_id==5){
        //     return array(
        //         'success'=>0,
        //         'message'=>'Please wait we are working on it!! ',
        //         'request_time'=>$start_time,
        //         'response_time'=>date('Y-m-d h:i:s'),
        //         'status'=>422,
        //         'errors'=>''
        //     );
        // }
        DB::connection($db_conn)->beginTransaction();
        try{
            $emp_id=$request->emp_id;
            $sr_id=$request->sr_id;
            $thana_list=json_decode($request->thana_list,true);
            $dlrm_list=json_decode($request->dlrm_list,true);
            $rpln_list=json_decode($request->rpln_list,true);
            // 0 for fresh route, 1 for replace route, 2 for copy route
            //New Update 1 =>Copy, 2=> Replace
            //Extracted Data
            $emp_details=$this->getEmployeeDetails($sr_id,$db_conn);
            $zone_id=$emp_details->zone_id;
            $slgp_id=$emp_details->slgp_id;
            $role_id=$emp_details->role_id;
            $aemp_usnm=$emp_details->aemp_usnm;
            $acmp_id=$this->getAcmpId($slgp_id,$db_conn);
            // SR DEALAR MAPPING
            DB::connection($db_conn)->select("DELETE FROM tl_srdi WHERE aemp_id={$sr_id}");
            DB::connection($db_conn)->select("INSERT IGNORE INTO `tl_srdi`(`aemp_code`, `dlrm_code`, `aemp_id`, `dlrm_id`, `acmp_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`)
                                            SELECT {$aemp_usnm},'-',{$sr_id},id,{$acmp_id},{$country_id},1,{$emp_id},{$emp_id} FROM tm_dlrm WHERE id IN (".implode(',',$dlrm_list).")");
            // SR Thana Mapping
            DB::connection($db_conn)->select("DELETE FROM tl_srth WHERE aemp_id={$sr_id}");
            DB::connection($db_conn)->select("INSERT IGNORE INTO `tl_srth`(`aemp_id`, `than_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`)
                                            SELECT {$sr_id},id,{$country_id},1,{$emp_id},{$emp_id} FROM tm_than WHERE id IN (".implode(',',$thana_list).")");
            
            ## Route Plan Setup
            // Route Setup
            foreach($rpln_list as $rpln){
                if($rpln['status']==2){
                    $rplu_rpln=RplnH::on($db_conn)->where(['rout_id'=>$rpln['rout_id'],'rpln_day'=>$rpln['day_name'],'aemp_id'=>$rpln['rplu_id']])->first();
                    if($rplu_rpln){
                        $rplu_rpln->aemp_id=$sr_id;
                        $rplu_rpln->save();
                    }
                }
                if($rpln['status']==1){
                    $base_mapping=$this->getBaseId($rpln['rout_id'],$db_conn);
                    $zone_base_group=$this->getSlgpZone($sr_id,$db_conn); 

                    $slgp_name=$zone_base_group->slgp_name;                  
                    $zone_name=$zone_base_group->zone_name;                  
                    $zone_code=$zone_base_group->zone_code;                  
                    $slgp_code=$zone_base_group->slgp_code;                  
                    $dt = date('YmdHi');
                    $now = now();
                    $unique_number    = substr($now->timestamp . $now->milli, 10);
                    //$rout_name      = $slgp_name."-" . $zone_name."-".$base_mapping->base_name;
                    $rout_info=$this->getRouteCode($rpln['rout_id'],$db_conn);
                    $rout_name=$rout_info->rout_name;
                    $rout_code      ="RT-".$dt . '-' . $unique_number;
                    // New Route Create
                    $route = new Route();
                    $route->setConnection($db_conn);
                    $route->rout_name =$rout_name;
                    $route->rout_code = $rout_code;
                    if($country_id==5){
                        $route->acmp_code ='';
                        $route->base_code =$base_mapping->base_code;
                    }
                    
                    $route->base_id = $base_mapping->base_id;
                    $route->acmp_id = $acmp_id;
                    $route->cont_id =$country_id;
                    $route->lfcl_id = 1;
                    $route->aemp_iusr =$emp_id;
                    $route->aemp_eusr =$emp_id;
                    $route->var = 1;
                    $route->attr1 = 'auto';
                    $route->attr2 = '';
                    $route->attr3 = 0;
                    $route->attr4 =100;
                    $route->save();

                    // Add Rpln 
                    $rpln_exist=RplnH::on($db_conn)->where(['aemp_id'=>$sr_id,'rpln_day'=>$rpln['day_name']])->first();
                    if($rpln_exist){
                        $rout_code=$route->rout_code;
                        if($country_id==5){
                            $rpln_exist->rout_code=$rout_code;
                        }
                        $rpln_exist->rout_id=$route->id;
                        $rpln_exist->save();
                    }
                    else{
                        $rout_code=$route->rout_code;
                        $rpln_data=new RplnH();
                        $rpln_data->setConnection($db_conn);
                        
                        if($country_id==5){
                            $rpln_data->rout_code=$rout_code;
                            $rpln_data->aemp_code=$aemp_usnm;
                        }                      
                        $rpln_data->aemp_id=$sr_id;
                        $rpln_data->rpln_day=$rpln['day_name'];
                        $rpln_data->rout_id=$route->id;
                        $rpln_data->cont_id=$country_id;
                        $rpln_data->lfcl_id=1;
                        $rpln_data->aemp_iusr=$emp_id;
                        $rpln_data->aemp_eusr=$emp_id;
                        $rpln_data->save();
                    }

                    // Assign Site into this Route
                    $site_from_route=$rpln['rout_id'];
                    DB::connection($db_conn)->select("INSERT INTO `tl_rsmp`(`id`, `rout_id`, `site_id`, `rspm_serl`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`, `updated_at`, `var`, `attr1`, `attr2`, `attr3`, `attr4`) 
                    SELECT null, {$route->id}, `site_id`, `rspm_serl`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`, `updated_at`, `var`, `attr1`, `attr2`, `attr3`, `attr4`
                    FROM `tl_rsmp` WHERE rout_id={$site_from_route}");

                    // SLGP ZONE BASE MAPPING
                    $slgp_zone_base= new SalesGroupZoneBaseMapping();
                    $slgp_zone_base->setConnection($db_conn);
                    $slgp_zone_base->slgp_id = $slgp_id;
                    $slgp_zone_base->zone_id = $zone_id;
                    $slgp_zone_base->base_id = $base_mapping->base_id;
                    $slgp_zone_base->rout_id = $route->id;
                    $slgp_zone_base->created_at = now();
                    $slgp_zone_base->updated_at = now();
                    $slgp_zone_base->base_mob = $base_mapping->base_mob;
                    $slgp_zone_base->save();
                }
                else{
                    $rpln_exist=RplnH::on($db_conn)->where(['aemp_id'=>$sr_id,'rpln_day'=>$rpln['day_name']])->first();
                    $rout_code=$this->getRouteCode($rpln['rout_id'],$db_conn);
                    if($rpln_exist && $rout_code){
                        $rout_code=$rout_code->rout_code;
                        $rpln_exist->rout_code=$rout_code;
                        $rpln_exist->rout_id=$rpln['rout_id'];
                        $rpln_exist->save();
                    }
                    else 
                    {
                        $rout_code=$rout_code->rout_code;
                        $rpln_data=new RplnH();
                        $rpln_data->setConnection($db_conn);
                        $rpln_data->aemp_code=$aemp_usnm;
                        $rpln_data->rout_code=$rout_code;
                        $rpln_data->aemp_id=$sr_id;
                        $rpln_data->rpln_day=$rpln['day_name'];
                        $rpln_data->rout_id=$rpln['rout_id'];
                        $rpln_data->rout_id=$rpln['rout_id'];
                        $rpln_data->cont_id=$country_id;
                        $rpln_data->lfcl_id=1;
                        $rpln_data->aemp_iusr=$emp_id;
                        $rpln_data->aemp_eusr=$emp_id;
                        $rpln_data->save();
                    }
                }
            }
            DB::connection($db_conn)->commit();
            return array(
                'success'=>1,
                'message'=>'Success!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),

            );

        }
        catch(\Exception $e){
            DB::connection($db_conn)->rollback();
            return array(
                'success'=>0,
                'message'=>'Failed!! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
        }
    }
    
    // Helper Function
    public function getEmpAutoId($aemp_usnm,$db_conn){
        return Employee::on($db_conn)->where(['aemp_usnm'=>$aemp_usnm])->first();
    }

    public function getRouteAutoId($route_code,$db_conn){
       return Route::on($db_conn)->where(['rout_code' => $route_code])->first();
    }
    public function getEmployeeDetails($emp_id,$db_conn){
        return Employee::on($db_conn)->where(['id'=>$emp_id])->first();
    }
    public function getAcmpId($slgp_id,$db_conn){
        $data=DB::connection($db_conn)->select("SELECT acmp_id FROM tm_slgp WHERE id={$slgp_id}");
        return $data[0]->acmp_id;
    }


    public function getRouteCode($id,$db_conn){
       return Route::on($db_conn)->where(['id' => $id])->first();
    }
    public function getGroup($id,$db_conn){
        return SalesGroup::on($db_conn)->where(['id' => $id])->first();
    }
    public function getBaseId($id,$db_conn){
        $slgp_zone=DB::connection($db_conn)->select("SELECT t2.id base_id,t2.base_code,t2.base_name,t1.base_mob FROM `tbl_slgp_zone_base_mapping` t1
                    INNER JOIN tm_base t2 ON t1.base_id=t2.id
                    WHERE t1.rout_id={$id}");
        return $slgp_zone[0];
    }
    public function getSlgpZone($id,$db_conn){
        $slgp_zone=DB::connection($db_conn)->select("SELECT 
                    t2.slgp_name,t2.slgp_code,t3.zone_name,t3.zone_code
                    FROM tm_aemp t1
                    INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
                    INNER JOIN tm_zone t3 ON t1.zone_id=t3.id
                    WHERE t1.id={$id}");
        return $slgp_zone[0];
    }

    // Api Rate Limiting
    public function checkLimit(){
        try{
            return "Hi";
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
    }

}
