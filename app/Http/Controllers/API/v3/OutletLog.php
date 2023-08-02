<?php
/**
 * Created by PhpStorm.
 * User: 329206
 * Date: 19/03/2023
 */

namespace App\Http\Controllers\API\v3;

use App\MasterData\Asset;
use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Http\Request;
use App\BusinessObject\AssetSite;
use App\BusinessObject\OutletLogM;
use App\BusinessObject\SalesGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\MasterData\SalesGroupZoneBaseMapping;

class OutletLog extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function storeOutletLogData(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        DB::connection($db_conn)->beginTransaction();
        try{
            $exist=DB::connection($db_conn)->select("SELECT id FROM tbl_site_log1 WHERE site_code !='$request->site_code' AND site_mobile1='$request->site_mobile1' limit 1");
            if(!$exist){
                $rules = [
                    'site_code' => 'required|max:255',
                    'site_mobile1' => 'required|max:16',
                    'geo_lat' => 'required',
                    'geo_lon' => 'required'
                ];
                
                $messages = [
                    'site_code.required' => 'The site code field is required.',
                    'site_code.max' => 'The site code field should not exceed 255 characters.',
                    'site_mobile1.required' => 'The site mobile number field is required.',
                    'geo_lat.required' => 'Geo Lattitude is required',
                    'geo_lon.required' => 'Geo Longitude is required',
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return array(
                        'success'=>0,
                        'message'=>'Please provide all required field appropriately',
                        'request_time'=>$start_time,
                        'response_time'=>date('Y-m-d h:i:s'),
                        'status'=>http_response_code(),
        
                    ); 
                }
                //$otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $outlet=new OutletLogM();
                $outlet->setConnection($db_conn);
                $data = $request->all();
                $outlet->site_id = $data['site_id'];
                $outlet->site_code = $data['site_code'];
                $outlet->site_name = $data['site_name'];
                $outlet->site_name_bn = $data['site_name_bn'];
                $outlet->site_adrs = $data['site_adrs'];
                $outlet->site_adrs_bn = $data['site_adrs_bn'];
                $outlet->owner_name = $data['owner_name'];
                $outlet->owner_name_bn = $data['owner_name_bn'];
                $outlet->site_mobile1 = $data['site_mobile1'];
                $outlet->site_mobile2 = $data['site_mobile2'];
                $outlet->geo_lat = $data['geo_lat'];
                $outlet->geo_lon = $data['geo_lon'];
                $outlet->is_fridge =0;
                $outlet->is_shop_sign = 0;
                $outlet->otcg_id =0;
                $outlet->scnl_id = 1;
                $outlet->otp_code ='';
                $outlet->site_image = $data['site_image'];
                $outlet->is_vrfy =0;
                $outlet->scont_id =0;
                $outlet->aemp_iusr = $data['emp_id'];
                $outlet->aemp_eusr = $data['emp_id'];
                $outlet->save();
                // $site_mobile=$data['site_mobile1'];
                // $message = "-Code:" . $otp;
                // $cont_name=$country->cont_name;
                // $res=$this->sendCode($message,$otp,$site_mobile,$cont_name);
                DB::connection($db_conn)->commit();
                return array(
                    'success'=>1,
                    'message'=>'Success !',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>http_response_code(),
                    'line_id'=>$outlet->id
    
                );

            }
            else{
                return array(
                    'success'=>0,
                    'message'=>'This mobile number already associated with different outlet !! ',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>422,
                    'errors'=>'This mobile number already associated with different outlet !! '
                );
            }

        }
        catch(\Exception $e){
            DB::connection($db_conn)->rollback();
            return array(
                'success'=>0,
                'message'=>'Something went wrong !! ',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage()
            );
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
    public function resendOTP(Request $request){
        try{
            $start_time=date('Y-m-d h:i:s');
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $country_id=$request->country_id;
            $log_data=OutletLogM::on($db_conn)->find($request->line_id);
            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $time_limit=DB::connection($db_conn)->select("SELECT TIMESTAMPDIFF(SECOND, updated_at,current_timestamp) time_diff,otp_code FROM tbl_site_log1 WHERE id={$request->line_id} limit 1");
            $time_diff=$time_limit[0]->time_diff;
            $otp_code=$time_limit[0]->otp_code;
            if($otp_code){
                if($time_diff>=60){
                    $log_data->otp_code=$otp;
                    //$log_data->updated_at=date('Y-m-d h:i:s');
                    $log_data->save();
                    $message = "-Code:" . $otp;
                    $cont_name=$country->cont_name;
                    $mobile_number=$log_data->site_mobile1;
                    $response=$this->sendCode($message,$otp,$mobile_number,$cont_name);
                    return array(
                        'success'=>1,
                        'message'=>'Success',
                        'request_time'=>$start_time,
                        'response_time'=>date('Y-m-d h:i:s'),
                        'status'=>200,
                        'errors'=>''
                    );
                }
                else {
                    $wait=60-$time_diff;
                    return array(
                        'success'=>0,
                        'message'=>'Please resend otp after :'.$wait.' Seconds',
                        'request_time'=>$start_time,
                        'response_time'=>date('Y-m-d h:i:s'),
                        'status'=>422,
                        'errors'=>'Please resend otp after :'.$wait.' Seconds',
                    );
                }
            }
            else
            {
                $log_data->otp_code=$otp;
                $log_data->save();
                $message = "-Code:" . $otp;
                $cont_name=$country->cont_name;
                $mobile_number=$log_data->site_mobile1;
                $response=$this->sendCode($message,$otp,$mobile_number,$cont_name);
                return array(
                    'success'=>1,
                    'message'=>'Success',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>200,
                    'errors'=>''
                );
            }
            

        }
        catch(Exception $e){
                return array(
                    'success'=>0,
                    'message'=>'Something went wrong !!!',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>422,
                    'errors'=>$e->getMessage(),
                );
        }
    }
    public function verifyOutlet(Request $request){
        try{
            $start_time=date('Y-m-d h:i:s');
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $country_id=$request->country_id;
            $log_data=OutletLogM::on($db_conn)->where(['id'=>$request->line_id,'otp_code'=>$request->otp_code])->first();
            if($log_data){
                $log_data->is_vrfy=1;
                $log_data->save();
                return array(
                    'success'=>1,
                    'message'=>'Outlet verified successfully !',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>http_response_code(),
                    'errors'=>''
                );
                
            }
            else{
                return array(
                    'success'=>0,
                    'message'=>'Invalid OTP ! Please try again ',
                    'request_time'=>$start_time,
                    'response_time'=>date('Y-m-d h:i:s'),
                    'status'=>401,
                    'errors'=>''
                );
            }
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }
    public function empVerifiedOutletList(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        $emp_id=$request->emp_id;
        try{
            $data=DB::connection($db_conn)->select("SELECT site_id,site_code FROM tbl_site_log1 WHERE is_vrfy=1 AND aemp_iusr={$emp_id}");
            return array(
                'success'=>1,
                'verified_list'=>$data,
                'message'=>'Success !',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }
    public function getAssetList(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        $emp_id=$request->emp_id;
        try{
            $data=DB::connection($db_conn)->select("SELECT id asset_id,astm_name asset_name,if(astm_type=1,'Fixed','Hired')asset_type FROM  tm_astm ORDER BY astm_name");
            return array(
                'success'=>1,
                'asset_list'=>$data,
                'message'=>'Success !',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }
    public function assignAssetToOutlet(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        $emp_id=$request->emp_id;
        
        DB::connection($db_conn)->beginTransaction();                   
        try{
            
            $employee = $this->getEmployeeDetails($emp_id,$db_conn);
            $astm_ids=json_decode($request->astm_ids,true);
            if(count($astm_ids) > 0){
                foreach ($astm_ids as $astm) {
                    $exist_data=DB::connection($db_conn)->select("SELECT id FROM  tl_assm WHERE astm_id={$astm} AND slgp_id={$employee->slgp_id} AND site_id={$request->site_id} AND zone_id={$employee->zone_id} ");
                    
                    if(!$exist_data){                        
                        $data = DB::connection($db_conn)->table('tl_assm')->insert([
                            'astm_id' => $astm,
                            'site_id' => $request->site_id,
                            'slgp_id' => $employee->slgp_id,
                            'zone_id' => $employee->zone_id,
                            'aemp_iusr' =>$emp_id,
                            'aemp_eusr' =>$emp_id,
                        ]);
                        DB::connection($db_conn)->commit();                       
                    }
                        
                }
            }

            return array(
                'success'=>1,
                'message'=>'Success !',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }
    public function getEmployeeDetails($emp_id,$db_conn){
        return Employee::on($db_conn)->where(['id'=>$emp_id])->first();
    }

    public function getOwnListedAssetOutlet(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        $emp_id=$request->emp_id;
        try{
            $employee = $this->getEmployeeDetails($emp_id,$db_conn);
            $slgp_id=$employee->slgp_id ?? 0;
            $zone_id=$employee->zone_id ?? 0;
            $data=DB::connection($db_conn)->select("SELECT DISTINCT site_id FROM tl_assm WHERE aemp_iusr={$emp_id} AND slgp_id={$slgp_id} AND zone_id={$zone_id}   ORDER BY site_id");
            return array(
                'success'=>1,
                'message'=>'Success !',
                'own_listed_outlet'=>$data,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );

        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }
    public function getSelectedOutletAsset(Request $request){
        $start_time=date('Y-m-d h:i:s');
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $country_id=$request->country_id;
        $emp_id=$request->emp_id;
        $site_id=$request->site_id; 
        try{
            $employee = $this->getEmployeeDetails($emp_id,$db_conn);
            $slgp_id=$employee->slgp_id ?? 0;
            $zone_id=$employee->zone_id ?? 0;
           // $data=DB::connection($db_conn)->select("SELECT DISTINCT t2.astm_name FROM `tl_assm` t1 INNER JOIN tm_astm t2 ON t1.astm_id=t2.id WHERE t1.site_id={$site_id} AND t1.slgp_id={$slgp_id} AND t1.zone_id={$zone_id} ORDER BY t2.astm_name");
            $data=DB::connection($db_conn)->select("SELECT DISTINCT t2.astm_name FROM `tl_assm` t1 INNER JOIN tm_astm t2 ON t1.astm_id=t2.id WHERE t1.site_id={$site_id}  ORDER BY t2.astm_name");
            return array(
                'success'=>1,
                'message'=>'Success !',
                'asset_list'=>$data,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );

        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong !!!',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
    }

    public function checkLimit(){
        return "Checking API Request Limit";
    }
    function getAroundOutletList(Request $request)
    {
        $start_time=date('Y-m-d h:i:s');
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        try{
            if ($db_conn != '') {

                if ($request->country_id == 2 || $request->country_id == 5) {
                    $data2 = DB::connection($db_conn)->select("SELECT 
                                t1.id
                                FROM tm_mktm t1
                                INNER JOIN tm_ward t2 ON t1.ward_id=t2.id
                                WHERE t2.than_id={$request->than_id}");
                    $d = '';
                    $i = 1;
                    foreach ($data2 as $key => $value) {
                        if ($i == 1) {
                            $d .= $value->id;
                        } else {
                            $d .= ',' . $value->id;
                        }
                        $i++;
                    }
                    $data1 = DB::connection($db_conn)->select("
                            select
                            Outlet_ID                                                AS Outlet_ID,
                            Outlet_Code                                              AS Outlet_Code,
                            Outlet_Name                                              AS Outlet_Name,
                            Outlet_Name_Bn                                           AS Outlet_Name_Bn,
                            distance_in_km                                           AS distance_in_km,
                            Owner_Name                                               AS Owner_Name,
                            Mobile_No                                                AS Mobile_No,
                            Outlet_Address                                           AS Outlet_Address,
                            Outlet_Address_Bn                                        AS Outlet_Address_Bn,
                            Outlet_imge_ln                                           AS Outlet_imge_ln,
                            refrigerator                                             AS refrigerator,
                            shop_sign                                                AS shop_sign,
                            geo_lat                                                  AS geo_lat,
                            geo_lon                                                  AS geo_lon
                    FROM ( select t1.id                  AS Outlet_ID,
                            t1.site_code             AS Outlet_Code,
                            t1.site_name             AS Outlet_Name,
                            TRIM(t1.site_olnm)        AS Outlet_Name_Bn,
                                ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                                    * COS( RADIANS( t1.geo_lat ) )
                                    * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                                    + SIN( RADIANS( $request->geo_lat  ) )
                                    * SIN( RADIANS( t1.geo_lat ) )
                            )
                            * 6371
                            ) AS distance_in_km,
                    t1.site_ownm AS Owner_Name,
                    t1.site_mob1 AS Mobile_No,
                    t1.site_adrs AS Outlet_Address,
                    t1.site_olad AS Outlet_Address_Bn,
                    t1.site_imge AS Outlet_imge_ln,
                    t1.site_isfg AS refrigerator,
                    t1.site_issg AS shop_sign,
                    t1.geo_lat,
                    t1.geo_lon
                    from tm_site_registration as t1
                    where t1.mktm_id in($d) and MBRContains(st_makeEnvelope (
                    point(($request->geo_lon + 1 / 111.1), ($request->geo_lat + 1 / 111.1)),
                    point(($request->geo_lon - 1 / 111.1), ($request->geo_lat - 1 / 111.1)) 
                    ), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 order by distance_in_km asc limit 50 )AS t22
                    UNION ALL 
                    select  t1.id                                                         AS Outlet_ID,
                            t1.locd_code                                                 AS Outlet_Code,
                            t1.locd_name                                                 AS Outlet_Name,
                            TRIM(t1.locd_name)                                            AS Outlet_Name_Bn,
                                ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                                    * COS( RADIANS( t1.geo_lat ) )
                                    * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                                    + SIN( RADIANS( $request->geo_lat ) )
                                    * SIN( RADIANS( t1.geo_lat ) )
                            )
                            * 6371
                            ) AS distance_in_km,
                    '' AS Owner_Name,
                    '' AS Mobile_No,
                    t1.geo_adrs AS Outlet_Address,
                    t1.geo_adrs AS Outlet_Address_Bn,
                    '' AS Outlet_imge_ln,
                    '0' AS refrigerator,
                    '0' AS shop_sign,
                    t1.geo_lat,
                    t1.geo_lon
                    from tm_locd as t1
                    where MBRContains(st_makeEnvelope (
                    point(($request->geo_lon + 0.08 / 111.1), ($request->geo_lat + 0.08 / 111.1)),
                    point(($request->geo_lon / 111.1), ($request->geo_lat - 0.08 / 111.1)) 
                    ), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 order by distance_in_km asc limit 500
    
                    ");
                } else {
                    $data1 = DB::connection($db_conn)->select("
                            SELECT   t1.id                                                   AS Outlet_ID,
                                t1.site_code                                                 AS Outlet_Code,
                                TRIM(t1.site_name)                                           AS Outlet_Name,
                                TRIM(t1.site_olnm)                                           AS Outlet_Name_Bn,
                                ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                                        * COS( RADIANS( t1.geo_lat ) )
                                        * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                                        + SIN( RADIANS( $request->geo_lat  ) )
                                        * SIN( RADIANS( t1.geo_lat ) )
                                )
                                * 6371
                                ) AS distance_in_km,
                        t1.site_ownm AS Owner_Name,
                        t1.site_mob1 AS Mobile_No,
                        t1.site_adrs AS Outlet_Address,
                        t1.site_olad AS Outlet_Address_Bn,
                        t1.site_imge AS Outlet_imge_ln,
                        t1.site_isfg AS refrigerator,
                        t1.site_issg AS shop_sign,
                        t1.geo_lat,
                        t1.geo_lon
                        FROM tm_site AS t1 where lfcl_id=1   HAVING distance_in_km < 1  
                        ORDER BY distance_in_km
                        LIMIT 20
                        ");
    
                }
            }
            return array(
                'success'=>1,
                'message'=>'Success !',
                'outletList'=>$data1,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>''
            );
        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Something went wrong!!',
                'outletList'=>$data1,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'errors'=>$e->getMessage()
            );
        }
        
        

    }

   
}
