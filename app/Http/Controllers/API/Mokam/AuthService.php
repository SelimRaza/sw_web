<?php

namespace App\Http\Controllers\API\Mokam;

use App\BusinessObject\ErrorTrack;
use App\BusinessObject\Location;
use App\BusinessObject\LocationHistory;
use App\BusinessObject\LoginLog;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\Menu\MobileEmpMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\MasterData\Site;
use App\BusinessObject\Mokam;
use Illuminate\Support\Facades\Validator;

class AuthService extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function createAccount(Request $request)
    {
        $country = (new Country())->country(2);
        $conn=$country->cont_conn;
        $request_time=date('Y-m-d h:i:s');
        $response_flag=0;
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => ['required', 'regex:/^01\d{9}$/'],
            ], [
                'mobile_no.required' => 'The phone number field is required.',
                'mobile_no.regex' => 'The phone number must start with "01" and be 11 digits long.',
            ]); 
            if ($validator->fails()) {
                return response()->json([
                    'success'=>0,'message'=>'Invalid Number !','status'=>422, 'errors' => $validator->errors(),
                    'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
                ], 422);
            }           
            $mobile_no=$request->mobile_no;
            $outlet_info=DB::connection($conn)->select("SELECT t5.id dsct_id,t5.dsct_name,
                        t4.id than_id,t4.than_name,t3.id ward_id ,t3.ward_name,t2.id mktm_id,t2.mktm_name
                        site_name,site_code,site_mob1 site_mobile,
                        geo_lat,geo_lon, t6.id cat_id,t6.otcg_name cat_name
                        FROM `tm_site` t1
                        INNER JOIN tm_mktm t2 ON t1.mktm_id=t2.id
                        INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
                        INNER JOIN tm_than t4 ON t3.than_id=t4.id
                        INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
                        INNER JOIN tm_otcg t6 ON t1.otcg_id=t6.id
                        WHERE t1.site_mob1=RIGHT(REPLACE({$mobile_no},'-',''),10) AND t1.lfcl_id=1;");
            $district=DB::connection($conn)->select(" SELECT id dsct_id,dsct_name FROM tm_dsct WHERE lfcl_id=1 ORDER BY dsct_name ASC");
            return response()->json([
                'success'=>1,'message'=>'Success',
                'site_info'=>$outlet_info,
                'dsct_list'=>$district,
                'status'=>200,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s')
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'success'=>0,'message'=>'Failed!','status'=>422, 'errors' => $e->getMessage(),
                'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
            ], 422);
        }
        
        
    }
    // Mokam Start  APIS
    public function getOTP(Request $request){
        $country = (new Country())->country(2);
        $conn=$country->cont_conn;
        $request_time=date('Y-m-d h:i:s');
        $has_account=0;
        $response_flag=0;
        $rtn_msg="Failed !";
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => ['required', 'regex:/^01\d{9}$/'],
            ], [
                'mobile_no.required' => 'The phone number field is required.',
                'mobile_no.regex' => 'The phone number must start with "01" and be 11 digits long.',
            ]); 
            if ($validator->fails()) {
                return response()->json([
                    'success'=>0,'message'=>'Invalid Number !','status'=>422, 'errors' => $validator->errors(),
                    'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
                ], 422);
            } 
            $mobile_no=$request->mobile_no; 
            //return $this->sendOTP('123456','01991083994','PRAN');         
            $mokam_exist=Mokam::on($conn)->where(['site_mobile'=>$mobile_no])->first();
            if($mokam_exist && $mokam_exist->is_verified==1){
                    $has_account=1;
            }
            else{
                $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $message = "-OTP:" . $otp;
                $cont_name=$country->cont_name;
                if($mokam_exist){
                    $mokam_exist->otp=$otp;
                    $mokam_exist->otp_time=date('Y-m-d h:i:s');
                    $mokam_exist->save();
                    $response_flag=$this->sendOTP($message,$mobile_no,$cont_name);
                }else{
                    $new_mokam=new Mokam();
                    $new_mokam->setConnection($conn);
                    $new_mokam->site_mobile=$mobile_no;
                    $new_mokam->otp=$otp;
                    $new_mokam->otp_time=date('Y-m-d h:i:s');
                    $new_mokam->is_verified=0;
                    $new_mokam->save();
                    $response_flag=$this->sendOTP($message,$mobile_no,$cont_name);
                }
            }
            if($response_flag==1){
                $rtn_msg="OTP is sent to the number:-".$mobile_no;
            }
            return response()->json([
                'success'=>1,
                'has_account'=>$has_account,
                'message'=>$rtn_msg,
                'status'=>200,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s')
            ], 200);
        }
        catch(\Exception $e){
            return $e->getMessage();
            return response()->json([
                'success'=>0,'message'=>'Failed!','status'=>422, 'errors' => $e->getMessage(),
                'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
            ], 422);
        }

    }
    public function verifyOTP(Request $request){
        $country = (new Country())->country(2);
        $conn=$country->cont_conn;
        $mobile_no=$request->mobile_no;
        $otp=$request->otp;
        $request_time=date('Y-m-d h:i:s');
        $validator = Validator::make($request->all(), [
            'mobile_no' => ['required', 'regex:/^01\d{9}$/'],
            'otp' => 'required|numeric|digits:6'
        ], [
            'mobile_no.required' => 'The phone number field is required.',
            'mobile_no.regex' => 'The phone number must start with "01" and be 11 digits long.',
            'otp.required' => 'Please enter the OTP code.',
            'otp.numeric' => 'The OTP code should be numeric.',
            'otp.digits' => 'The OTP code should be exactly 6 digits.'
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'success'=>0,'message'=>'Invalid Number or OTP !','status'=>422, 'errors' => $validator->errors(),
                'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
            ], 422);
        } 
        $is_valid=DB::connection($conn)->select("
                    SELECT id,
                    CASE WHEN CURRENT_TIMESTAMP>otp_time + Interval 55 Minute THEN 0 ELSE 1 END FLAG
                    FROM tm_mokm WHERE site_mobile='{$mobile_no}' AND otp={$otp}
        ");
        if($is_valid && $is_valid[0]->FLAG==1){
            $id=$is_valid[0]->id;
            $mokam=Mokam::on($conn)->find($id);
            $mokam->is_verified=1;
            $mokam->save();
            $outlet_info=DB::connection($conn)->select("SELECT t5.id dsct_id,t5.dsct_name,
                        t4.id than_id,t4.than_name,t3.id ward_id ,t3.ward_name,t2.id mktm_id,t2.mktm_name
                        site_name,site_code,site_mob1 site_mobile,
                        geo_lat,geo_lon, t6.id cat_id,t6.otcg_name cat_name
                        FROM `tm_site` t1
                        INNER JOIN tm_mktm t2 ON t1.mktm_id=t2.id
                        INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
                        INNER JOIN tm_than t4 ON t3.than_id=t4.id
                        INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
                        INNER JOIN tm_otcg t6 ON t1.otcg_id=t6.id
                        WHERE t1.site_mob1=RIGHT(REPLACE({$mobile_no},'-',''),10) AND t1.lfcl_id=1 limit 1;");
            $district=DB::connection($conn)->select(" SELECT id dsct_id,dsct_name FROM tm_dsct WHERE lfcl_id=1 ORDER BY dsct_name ASC");
            $cat_list=DB::connection($conn)->select(" SELECT id cat_id,otcg_name cat_name FROM tm_otcg WHERE lfcl_id=1 ORDER BY otcg_name ASC");
            return response()->json([
                'success'=>1,'message'=>'Success ! Account Verified',
                'account_id'=>$id,
                'site_info'=>$outlet_info,
                'dsct_list'=>$district,
                'cat_list'=>$cat_list,
                'status'=>200,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s')
            ], 200);
        }
        else{
            return response()->json([
                'success'=>0,'message'=>'OTP Wrong or Expired !','status'=>422, 'errors' => $validator->errors(),
                'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
            ], 422);
        }
        return $is_valid;

    }

    public function upSertOutletInfo(Request $request){
        $country = (new Country())->country(2);
        $conn=$country->cont_conn;
        $otp=$request->otp;
        $request_time=date('Y-m-d h:i:s');
        $mobile_no=$request->mobile_no;
        $account_id=$request->account_id;
        $cat_id=$request->cat_id;
        $mktm_id=$request->mktm_id;
        $scnl_id=1;
        $site_name=$request->site_name;
        $site_code=$request->site_code;


    }
    // Helper Function
    public function sendOTP($message,$mobile,$cont_name){
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
        $data = json_decode($response, true);
        $n = $data['success'];
        return $data['success'];     
    }
     // Mokam END  APIS
    public function getMarketDetails(Request $request){
        $country = (new Country())->country(2);
        $conn=$country->cont_conn;
        $request_time=date('Y-m-d h:i:s');
        $dsct_id=$request->dsct_id;
        try{
            $market_list=DB::connection($conn)->select("SELECT 
                t4.id dsct_id,t4.dsct_name,t3.id thana_id,t3.than_name,t2.id ward_id,t2.ward_name,t1.id mktm_id,t1.mktm_name
                FROM tm_mktm t1
                INNER JOIN tm_ward t2 ON t1.ward_id=t2.id
                INNER JOIN tm_than t3 ON t2.than_id=t3.id
                INNER JOIN tm_dsct t4 ON t3.dsct_id=t4.id
                WHERE t4.id={$dsct_id}
                ORDER BY t4.dsct_name,t3.than_name,t2.ward_name,t1.mktm_name;");
            return response()->json([
                'success'=>1,'message'=>'Success',
                'market_list'=>$market_list,
                'status'=>200,
                'request_time'=>$request_time,
                'response_time'=>date('Y-m-d h:i:s')
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'success'=>0,'message'=>'Failed!','status'=>422, 'errors' => $e->getMessage(),
                'request_time'=>$request_time,'response_time'=>date('Y-m-d h:i:s')
            ], 422);
        }
        
    }

    public function upSertOutletInfo(Request $request){
        $site_exist=Site::on($conn)->where(['lfcl_id'=>1,'site_mob1'=>$mobile_no])->first();
        $message='';
        if($site_exist){
            $mokam_exist=Mokam::on($conn)->where(['is_verified'=>1,'site_mobile'=>$mobile_no])->first();
            if($mokam_exist){
                $message='Account already exist with this number';
            }
            else{
                $mokam=Mokam::on($conn)->where(['site_mobile'=>$mobile_no,'site_id'=>$site_exist->id])->first();
                if($mokam){
                  //  $mokam->
                    $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $mokam->otp=$otp;
                    $mokam->otp_time=date('Y-m-d h:i:s');
                    $mokam->save();
                    $message = "-OTP:" . $otp;
                    $cont_name=$country->cont_name;
                    $response_flag=$this->sendOTP($message,$otp,$site_mobile,$cont_name);
                }
                else{
                    $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $mokam=new Mokam();
                    $mokam->setConnection($conn);
                    $mokam->site_id=$site_exist->id;
                    $mokam->site_code=$site_exist->site_code;
                    $mokam->site_mobile=$site_exist->site_mob1;
                    $mokam->site_mobile=$site_exist->site_mob1;
                    $mokam->otp=$otp;
                    $mokam->otp_time=date('Y-m-d h:i:s');
                    $mokam->save();
                    $message = "-OTP:" . $otp;
                    $cont_name=$country->cont_name;
                    $response_flag=$this->sendOTP($message,$otp,$site_mobile,$cont_name);
                }
            }
            return $site_exist;
        }else{
            $site=new Site();
            $site->setConnection($conn);
            if(strlen($request->outlet_code)>8 ||strlen($request->site_code)>8){
                return redirect()->back()->with('danger', 'Site Code || Outlet Code Can not more than 8 Digit');
            }
            $outlet = new Outlet();
            $outlet->setConnection($this->db);
            $outlet->oult_name = $request->site_name;
            $outlet->oult_code = $request->outlet_code?$request->outlet_code:$request->site_code;
            $outlet->oult_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $outlet->oult_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $outlet->oult_olad = isset($request->site_olad) ? $request->site_olad : '';
            $outlet->oult_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $outlet->oult_olon = isset($request->site_olon) ? $request->site_olon : '';
            $outlet->oult_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $outlet->oult_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $outlet->oult_emal = isset($request->site_emal) ? $request->site_emal : '';
            $outlet->cont_id = $this->currentUser->country()->id;
            $outlet->lfcl_id = 1;
            $outlet->aemp_iusr = $this->currentUser->employee()->id;
            $outlet->aemp_eusr = $this->currentUser->employee()->id;
            $outlet->save();
            $site = new Site();
            $site->setConnection($this->db);
            $site->site_name = $request->site_name;
            $site->site_code = $request->site_code;
            $site->outl_id = $outlet->id;
            $site->site_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $site->site_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $site->site_olad = isset($request->site_olad) ? $request->site_olad : '';
            $site->mktm_id = $request->mktm_id;
            $site->site_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $site->site_olon = isset($request->site_olon) ? $request->site_olon : '';
            $site->site_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $site->site_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $site->site_emal = isset($request->site_emal) ? $request->site_emal : '';
            $site->scnl_id = $request->scnl_id;
            $site->otcg_id = $request->otcg_id;
            $site->site_imge = '';
            $site->site_omge = '';
            $site->geo_lat = 0;
            $site->geo_lon = 0;
            $site->site_reg = isset($request->site_reg) ? $request->site_reg : '';
            $site->site_vrfy = 0;
            $site->site_hsno = isset($request->site_hsno) ? $request->site_hsno : '';
            $site->site_vtrn = isset($request->site_vtrn) ? $request->site_vtrn : '';
            $site->site_vsts = $request->site_vsts == 'on' ? 1 : 0;;
            $site->cont_id = $this->currentUser->country()->id;
            $site->lfcl_id = 1;
            $site->aemp_iusr = $this->currentUser->employee()->id;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->save();

            $cont_id=$request->ow_cont_id;
            $licn=$request->licn_no;
            $expr_date=$request->expr_date;
            if($cont_id !='' || $licn !=''){
                $scmp=new SiteCountryMapping();
                $scmp->setConnection($this->db);
                $scmp->site_id=$site->id;
                $scmp->cont_id=$cont_id;
                $scmp->licn_no=$licn;
                $scmp->expr_date=$expr_date;
                $scmp->aemp_iusr=$this->aemp_id;
                $scmp->aemp_eusr=$this->aemp_id;
                $scmp->save();
            }
        }
        return "Hello";
    }

    

}
