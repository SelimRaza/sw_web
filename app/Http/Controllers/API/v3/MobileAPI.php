<?php

namespace App\Http\Controllers\API\v3;


use App\BusinessObject\Attendance;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\PhoneBookGroup;
use App\BusinessObject\SiteVisited;
use App\MasterData\Site;
//
use App\BusinessObject\Note;
use App\BusinessObject\ErrorTrack;
use App\BusinessObject\Location;
use App\BusinessObject\LocationHistory;
use App\BusinessObject\LoginLog;
use App\BusinessObject\RewardMaster;
use App\DataExport\EmployeeMenuGroup;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\SPDS;
use App\Menu\MobileEmpMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v2\OrderModuleDataUAE;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MobileAPI extends Controller
{


    public function __construct()
    {

        $this->middleware('timezone');
    }

    public function check_apk_version($current_apk)
    {
        return $current_apk >= '29' || $current_apk == '18';
        // return $current_apk >= '29' || $current_apk == '25' || $current_apk == '26' || $current_apk == '18';

    }

    public function ft_version($current_apk)
    {
        // return $current_apk >= '27' || $current_apk == '25' || $current_apk == '26' || $current_apk == '18';
        return $current_apk >= '27' || $current_apk == '25' || $current_apk == '26' || $current_apk == '18';

    }

    public function app_status(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $token = $request->token;
        $hris_status = $request->hris_status;
        $loginAttempt = false;
        $current_apk = $request->version_code;

        $result_data = array(
            'success' => 0,
            'force_status' => 0,
            'message' => "",
        );
        $loginAttempt = DB::select("select * from users where email='$request->email'");
        $cont_id = $loginAttempt[0]->cont_id;
        if (($cont_id == 3 || $cont_id == 14 || $cont_id == 15) && $current_apk < 76) {
            $result_data = array(
                'success' => 0,
                'force_status' => 1,
                'message' => "This '" . $current_apk . "' version is not allowed !!!",
            );
        } else {
            // }
            $user = User::where('email', '=', $name)->first();
            if ($user->remember_token == $token) {
                if ($hris_status == 0) {
                    $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                    if ($loginAttempt) {
                        if ($this->check_apk_version($current_apk)) {
                            $result_data = array(
                                'success' => 1,
                                'force_status' => 0,
                                'message' => "All is Ok",

                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'force_status' => 1,
                                'message' => "This '" . $current_apk . "' version is not allowed !!!",

                            );
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'force_status' => 3, // next version it will be 3
                            'message' => "Inactive user force logout !!!",
                        );
                    }
                } else {
                    if ($this->check_apk_version($current_apk)) {
                        $result_data = array(
                            'success' => 1,
                            'force_status' => 0,
                            'message' => "44 ",
                        );
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'force_status' => 1,
                            'message' => "This '" . $current_apk . "' version is not allowed !!!",
                        );
                    }
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'force_status' => 2,//just logout
                    'message' => "Invalid User Token",
                );
            }
        }
        return $result_data;
    }

    public function login_data_in_sync(Request $request)
    {$country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;

            $emp = Employee::on($db_conn)->where(['id' => $request->emp_id])->first();

            $spmp_sdpr = SPDS::on($db_conn)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
            $ck_attn = DB::connection($db_conn)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();//tl_atnt
            if ($ck_attn == null) {
                $attn_start = '00:00:00';
                $attn_endt = '00:00:00';
            } else {
                $attn_start = $ck_attn->atnt_stat;
                $attn_endt = $ck_attn->atnt_etat;
            }

            if ($spmp_sdpr == null) {
                $spmp_sdpr1 = 0;
                $spmp_ldpr = 0;
            } else {
                $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
            }
            $emp->attn_start = $attn_start;
            $emp->attn_end = $attn_endt;
            $emp->spmp_sdpr = $spmp_sdpr1;
            $emp->spmp_sdpr_line = $spmp_ldpr;

            // $country1 = (new Country())->country($db_conn);
            $country1 = (new Country())->country($request->country_id);

            $emp->country_name = $emp->country()->cont_name;
			$emp->cheque_day = $country1->GCKCD;
            $emp->cheque_xday = $country1->GTCCD;
            $emp->credit_xday = $country1->GTCRD;
            $emp->route_openblock = $country1->RTBO;
            $emp->min_order_amt = $country1->min_oamt;
            $emp->module_type = $country1->module_type;
            $emp->cont_tzon = $country1->cont_tzon;
            $emp->currency_symb = $country1->cncy_sym;
            $emp->memo_title = $country1->memo_title;
            $emp->memo_sub_title = $country1->memo_sub_title;
            $emp->round_digit = $country1->cont_dgit;
            $emp->image_folder = $emp->country()->cont_imgf;
            // $emp->cont_ogdt = $emp->country()->cont_ogdt;
            $emp->cont_ogdt = $country1->cont_ogdt;
            $emp->currency = $emp->country()->cont_cncy;
            $emp->round_number = $emp->country()->cont_rund;
            $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
            $emp->success = 1;
            return $emp;
        } else {
            return '';
        }
    }

    public function changePasswordWithOtp(Request $request)
    {
        $change = false;

        $result_data = array(
            'success' => 0,
            'message' => "failed");

        //$loginAttempt = DB::select('select * from users where email=' . $request->email);
        $loginAttempt = DB::select("select * from users where email='$request->email'");

        if ($loginAttempt) {
            $old_otp = $loginAttempt[0]->aemp_otpn;
            if ($old_otp == $request->otpCode) {

                $password = bcrypt($request->newPassword);

                $ret = DB::table('users')->where(['email' => $request->email])->update(['password' => $password, 'aemp_otpn' => '']);

                DB::table('tt_pswd')->insert(
                    array(
                        'pswd_time' => date('Y-m-d H:i:s'),
                        'pswd_user' => $request->email,
                        'pswd_opwd' => $request->newPassword,
                        'pswd_npwd' => $request->newPassword,
                        'cont_id' => $loginAttempt[0]->cont_id,
                        'lfcl_id' => 0,
                    )
                );
                $change = true;
                if ($ret) {
                    $result_data = array(
                        'success' => 1,
                        'message' => "Password Changed Successful!!!");
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "Wrong OTP Code");
            }
        }
        return $result_data;

    }

    public function verifyOtpCode(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'token' => 0,
            'message' => "failed");

        $loginAttempt = DB::select("select * from users where email='$request->email'");
        if ($loginAttempt) {
            $old_otp = $loginAttempt[0]->aemp_otpn;
            if ($old_otp == $request->otpCode) {
                $token = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

                $ret = DB::table('users')->where(['email' => $request->email])->update(['aemp_otpn' => $token]);
                if ($ret == '1') {
                    $result_data = array(
                        'success' => 1,
                        'token' => $token,
                        'message' => "Your OTP Code Verified Successful !!",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'token' => 0,
                    'message' => "OTP Code InValid !!",
                );
            }
        }
        return $result_data;
    }

    public function getOtpForgetPassword(Request $request)
    {
        $name = $request->email;

        $loginAttempt = DB::select("select * from users where email='$request->email'");

        $result_data = array(
            'success' => 0,
            'message' => "failed");

        if ($loginAttempt) {
            $country = (new Country())->country($loginAttempt[0]->cont_id);

            $db_conn = $country->cont_conn;
            $aemp = DB::connection($db_conn)->table('tm_aemp')->where(['aemp_usnm' => $name])->first();
            $user_mob = $aemp->aemp_mob1;
            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            if ($user_mob) {
                $com = '';
                if ($country == '2') {
                    $com = "PRAN";
                } elseif ($country == '5') {
                    $com = "RFL";
                } else {
                    $com = "PRAN";
                }
                $message = "-Code:" . $otp;
                $CURLOPT_URL = 'http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn=' . $user_mob . '&masking=' . $com . '&message=OTP' . $message;
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
                $ret = DB::table('users')->where(['email' => $request->email])->update(['aemp_otpn' => $otp]);

                if ($res == '1') {
                    $result_data = array(
                        'success' => 1,
                        'mobile' => $user_mob,
                        'message' => "Please check your registered Mobile for OTP Code ",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'mobile' => 0,
                    'message' => "Your Mobile Number is Empty !! Please Add Your Mobile No.",
                );
            }


            return $result_data;
        }
    }

    public function getMobileForOtp(Request $request)
    {
        $name = $request->email;

        $loginAttempt = DB::select("select * from users where email='$request->email'");

        $result_data = array(
            'success' => 0,
            'message' => "failed");

        if ($loginAttempt) {
            $country = (new Country())->country($loginAttempt[0]->cont_id);

            $db_conn = $country->cont_conn;
            $aemp = DB::connection($db_conn)->table('tm_aemp')->where(['aemp_usnm' => $name])->first();
            $user_mob = $aemp->aemp_mob1;
            if ($user_mob) {
                $result_data = array(
                    'success' => 1,
                    'mobile' => $user_mob,
                    'message' => "Your Mobile Number is <b>" . $user_mob . "</b>. If the no. is wrong then contact with your coordinator.",
                );
            } else {
                $result_data = array(
                    'success' => 0,
                    'mobile' => 0,
                    'message' => "Your Mobile Number is Empty !! Please Add Your Mobile No.",
                );
            }

            return $result_data;
        }
    }
    
     public
    function login_hris(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $version_code = $request->version_code;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';

        if ($this->check_apk_version($version_code)) {


            $loginAttempt = DB::select("select * from users where email='$request->email'");


            if ($loginAttempt) {
                $cont_id1 = $loginAttempt[0]->cont_id;
                $country1 = (new Country())->country($cont_id1);
                $module_type = $country1->module_type;
                $device_imei = $loginAttempt[0]->device_imei;
                if ($module_type == 2) {
                    if ($device_imei == $request->device_imei || $device_imei == 'N') {

                        $token = md5(uniqid(rand(), true));

                        if ($hris_status == 0) {
                            $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                            $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                            if ($loginAttempt) {
                                if ($loginAttempt_I) {
                                    if (Auth::user()->country() != null) {
                                        $db = Auth::user()->country()->cont_conn;
                                        $cont_id = Auth::user()->country()->id;
                                        $user_id = Auth::user()->id;
                                        $ret = DB::table('users')->where(['email' => $request->email])->update(['device_imei' => $request->device_imei]);
                                    }
                                } else {
                                    $result_data = array(
                                        'success' => 0,
                                        'message' => "You are Inactive User !!!",
                                    );

                                    return $result_data;
                                }

                            } else {
                                $succ = 0;
                                $mess = "";
                                $response = $this->hris_validation($name, $password);
                                if (isset($response)) {
                                    $result = json_decode($response);
                                    $status = $result->status;
                                    $message = $result->message;
                                    if (strtolower($status) == 'success') {
                                        $succ = 2;
                                        $mess = $message;
                                    } elseif (strtolower($status) == 'failed') {
                                        $succ = 0;
                                        $mess = $message;
                                    } else {
                                        $succ = 0;
                                        $mess = "Username or password might be wrong !!!";
                                    }
                                } else {
                                    $succ = 0;
                                    $mess = "Username or password might be wrong !!!";
                                }
                                $result_data = array(
                                    'success' => $succ,
                                    'message' => $mess,
                                );
                                // $result_data=[];
                                if ($version_code <= 28) {
                                    $result_data = array(
                                        'success' => 0,
                                        'message' => "Please Update Your App !!!",
                                    );
                                }
                                return $result_data;
                            }
                        } else if ($hris_status == 1) {
                            $loginAttempt = true;
                            $user = User::where('email', '=', $name)->first();
                            if ($user == null) {
                                $country = (new Country())->country(2);
                                $role_id = 1;
                                $amng_id = 3;
                                $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                                $country_data = json_decode(file_get_contents($staff_detiails));
                                $staffResult = json_decode($country_data->StaffResult);

                                DB::beginTransaction();
                                try {
                                    $user = User::create([
                                        'name' => $staffResult[0]->ID,
                                        'email' => trim($staffResult[0]->ID),
                                        'password' => bcrypt(trim($staffResult[0]->ID)),
                                        'remember_token' => $token,
                                        'lfcl_id' => 1,
                                        'cont_id' => $country->id,

                                    ]);
                                    $ret = DB::table('users')->where(['email' => $request->email])->update(['device_imei' => $request->device_imei]);
                                    $emp = new Employee();
                                    $emp->setConnection($country->cont_conn);
                                    $emp->aemp_name = $staffResult[0]->NAME;
                                    $emp->aemp_onme = $staffResult[0]->NAME;
                                    $emp->aemp_stnm = $staffResult[0]->NAME;
                                    $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                    $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                    $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                                    $emp->aemp_lued = $user->id;
                                    $emp->edsg_id = 1;
                                    $emp->role_id = $role_id;
                                    $emp->aemp_utkn = $token;
                                    $emp->cont_id = $country->id;
                                    $emp->aemp_iusr = 1;
                                    $emp->aemp_eusr = 1;
                                    $emp->aemp_mngr = 1;
                                    $emp->aemp_lmid = 1;
                                    $emp->aemp_aldt = 0;
                                    $emp->aemp_lcin = '50';
                                    $emp->aemp_otml = 2;
                                    $emp->aemp_lonl = 0;
                                    $emp->aemp_usnm = trim($staffResult[0]->ID);
                                    $emp->aemp_pimg = '';
                                    $emp->aemp_picn = '';
                                    $emp->aemp_emcc = '';
                                    $emp->aemp_crdt = 0;
                                    $emp->aemp_issl = 0;
                                    $emp->site_id = 0;
                                    $emp->lfcl_id = 1;
                                    $emp->amng_id = $amng_id;
                                    $emp->save();
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    return $e;
                                }
                                $db = $user->country()->cont_conn;
                                $cont_id = $user->country()->id;
                                $user_id = $user->id;

                            } else {
                                if ($user->country() != null) {
                                    $db = $user->country()->cont_conn;
                                    $cont_id = $user->country()->id;
                                    $user_id = $user->id;
                                }
                            }
                        }
                        $log = new LoginLog();
                        $log->user_name = $request->email;
                        $log->api_int = $request->version_code;
                        $log->device_imei = "$request->device_imei";
                        $log->device_name = "$request->device_name";
                        $log->mobile_number = '0';
                        $log->auth_type = $hris_status;
                        $log->login_attempt = $loginAttempt;
                        $log->save();
                        if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                            $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                            $emp->aemp_utkn = $this->setEmpToken($user_id);
                            $emp->region_id = '';

                            $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                            $ck_attn = DB::connection($db)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();//tl_atnt
                            if ($ck_attn == null) {
                                $attn_start = '00:00:00';
                                $attn_endt = '00:00:00';
                            } else {
                                $attn_start = $ck_attn->atnt_stat;
                                $attn_endt = $ck_attn->atnt_etat;
                            }

                            if ($spmp_sdpr == null) {
                                $spmp_sdpr1 = 0;
                                $spmp_ldpr = 0;
                            } else {
                                $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                                $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                            }
                            $emp->attn_start = $attn_start;
                            $emp->attn_end = $attn_endt;

                            $emp->spmp_sdpr = $spmp_sdpr1;
                            $emp->spmp_sdpr_line = $spmp_ldpr;

                            $country1 = (new Country())->country($cont_id);

                            $emp->country_name = $emp->country()->cont_name;
                            $emp->cheque_day = $country1->GCKCD;
                            $emp->cheque_xday = $country1->GTCCD;
                            $emp->credit_xday = $country1->GTCRD;
                            $emp->route_openblock = $country1->RTBO;
                            $emp->min_order_amt = $country1->min_oamt;
                            $emp->module_type = $country1->module_type;
                            $emp->cont_tzon = $country1->cont_tzon;
                            $emp->currency_symb = $country1->cncy_sym;
                            $emp->memo_title = $country1->memo_title;
                            $emp->memo_sub_title = $country1->memo_sub_title;
                            $emp->cont_ogdt = $country1->cont_ogdt;
                            $emp->image_folder = $emp->country()->cont_imgf;
                            $emp->round_digit = $country1->cont_dgit;

                            $emp->currency = $emp->country()->cont_cncy;
                            $emp->round_number = $emp->country()->cont_rund;
                            $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                            $emp->success = 1;
                            $emp->message = "Successful login";
                            return $emp;
                        } else {
                            $result_data = array(
                                'success' => 2,
                                'message' => "Username or password might be wrong !!!",
                            );
                            return $result_data;
                        }

                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => "You are trying to login from wrong Device !!!",
                        );
                        return $result_data;
                    }
                } else {
                    $token = md5(uniqid(rand(), true));

                    if ($hris_status == 0) {
                        $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                        $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                        if ($loginAttempt) {
                            if ($loginAttempt_I) {
                                if (Auth::user()->country() != null) {
                                    $db = Auth::user()->country()->cont_conn;
                                    $cont_id = Auth::user()->country()->id;
                                    $user_id = Auth::user()->id;
                                    $ret = DB::table('users')->where(['email' => $request->email])->update(['device_imei' => $request->device_imei]);
                                }
                            } else {
                                $result_data = array(
                                    'success' => 0,
                                    'message' => "You are Inactive User !!!",
                                );

                                return $result_data;
                            }

                        } else {
                            $succ = 0;
                            $mess = "";
                            $response = $this->hris_validation($name, $password);
                            if (isset($response)) {
                                $result = json_decode($response);
                                $status = $result->status;
                                $message = $result->message;
                                if (strtolower($status) == 'success') {
                                    $succ = 2;
                                    $mess = $message;
                                } elseif (strtolower($status) == 'failed') {
                                    $succ = 0;
                                    $mess = $message;
                                } else {
                                    $succ = 0;
                                    $mess = "Username or password might be wrong !!!";
                                }
                            } else {
                                $succ = 0;
                                $mess = "Username or password might be wrong !!!";
                            }
                            $result_data = array(
                                'success' => $succ,
                                'message' => $mess,
                            );
                            // $result_data=[];
                            if ($version_code <= 28) {
                                $result_data = array(
                                    'success' => 0,
                                    'message' => "Please Update Your App !!!",
                                );
                            }
                            return $result_data;
                        }
                    } else if ($hris_status == 1) {
                        $loginAttempt = true;
                        $user = User::where('email', '=', $name)->first();
                        if ($user == null) {
                            $country = (new Country())->country(2);
                            $role_id = 1;
                            $amng_id = 3;
                            $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                            $country_data = json_decode(file_get_contents($staff_detiails));
                            $staffResult = json_decode($country_data->StaffResult);

                            DB::beginTransaction();
                            try {
                                $user = User::create([
                                    'name' => $staffResult[0]->ID,
                                    'email' => trim($staffResult[0]->ID),
                                    'password' => bcrypt(trim($staffResult[0]->ID)),
                                    'remember_token' => $token,
                                    'lfcl_id' => 1,
                                    'cont_id' => $country->id,

                                ]);
                                $ret = DB::table('users')->where(['email' => $request->email])->update(['device_imei' => $request->device_imei]);
                                $emp = new Employee();
                                $emp->setConnection($country->cont_conn);
                                $emp->aemp_name = $staffResult[0]->NAME;
                                $emp->aemp_onme = $staffResult[0]->NAME;
                                $emp->aemp_stnm = $staffResult[0]->NAME;
                                $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                                $emp->aemp_lued = $user->id;
                                $emp->edsg_id = 1;
                                $emp->role_id = $role_id;
                                $emp->aemp_utkn = $token;
                                $emp->cont_id = $country->id;
                                $emp->aemp_iusr = 1;
                                $emp->aemp_eusr = 1;
                                $emp->aemp_mngr = 1;
                                $emp->aemp_lmid = 1;
                                $emp->aemp_aldt = 0;
                                $emp->aemp_lcin = '50';
                                $emp->aemp_otml = 2;
                                $emp->aemp_lonl = 0;
                                $emp->aemp_usnm = trim($staffResult[0]->ID);
                                $emp->aemp_pimg = '';
                                $emp->aemp_picn = '';
                                $emp->aemp_emcc = '';
                                $emp->aemp_crdt = 0;
                                $emp->aemp_issl = 0;
                                $emp->site_id = 0;
                                $emp->lfcl_id = 1;
                                $emp->amng_id = $amng_id;
                                $emp->save();
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                return $e;
                            }
                            $db = $user->country()->cont_conn;
                            $cont_id = $user->country()->id;
                            $user_id = $user->id;

                        } else {
                            if ($user->country() != null) {
                                $db = $user->country()->cont_conn;
                                $cont_id = $user->country()->id;
                                $user_id = $user->id;
                            }
                        }
                    }
                    $log = new LoginLog();
                    $log->user_name = $request->email;
                    $log->api_int = $request->version_code;
                    $log->device_imei = "$request->device_imei";
                    $log->device_name = "$request->device_name";
                    $log->mobile_number = '0';
                    $log->auth_type = $hris_status;
                    $log->login_attempt = $loginAttempt;
                    $log->save();
                    if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                        $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                        $emp->aemp_utkn = $this->setEmpToken($user_id);
                        $emp->region_id = '';

                        $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                        $ck_attn = DB::connection($db)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();//tl_atnt
                        if ($ck_attn == null) {
                            $attn_start = '00:00:00';
                            $attn_endt = '00:00:00';
                        } else {
                            $attn_start = $ck_attn->atnt_stat;
                            $attn_endt = $ck_attn->atnt_etat;
                        }

                        if ($spmp_sdpr == null) {
                            $spmp_sdpr1 = 0;
                            $spmp_ldpr = 0;
                        } else {
                            $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                            $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                        }

                        $emp->attn_start = $attn_start;
                        $emp->attn_end = $attn_endt;

                        $emp->spmp_sdpr = $spmp_sdpr1;
                        $emp->spmp_sdpr_line = $spmp_ldpr;

                        $country1 = (new Country())->country($cont_id);

                        $emp->country_name = $emp->country()->cont_name;
						$emp->cheque_day = $country1->GCKCD;
                        $emp->cheque_xday = $country1->GTCCD;
                        $emp->credit_xday = $country1->GTCRD;
                        $emp->route_openblock = $country1->RTBO;
                        $emp->min_order_amt = $country1->min_oamt;
                        $emp->module_type = $country1->module_type;
                        $emp->cont_tzon = $country1->cont_tzon;
                        $emp->currency_symb = $country1->cncy_sym;
                        $emp->memo_title = $country1->memo_title;
                        $emp->memo_sub_title = $country1->memo_sub_title;
                        $emp->cont_ogdt = $country1->cont_ogdt;
                        $emp->image_folder = $emp->country()->cont_imgf;
                        $emp->round_digit = $country1->cont_dgit;

                        $emp->currency = $emp->country()->cont_cncy;
                        $emp->round_number = $emp->country()->cont_rund;
                        $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                        $emp->success = 1;
                        $emp->message = "Successful login";
                        return $emp;
                    } else {
                        $result_data = array(
                            'success' => 2,
                            'message' => "Username or password might be wrong !!!",
                        );
                        return $result_data;
                    }
                }
            }
        } else {
            $result_data = array(
                'success' => 0,
                'message' => "This '" . $version_code . "' version is not allowed !!!",
            );
            return $result_data;
        }
    }

   public function login_sw(Request $request)
{
    $name = $request->email;
    $version_code = $request->version_code;
    $device_imei = $request->device_imei;
    $hris_status = $request->hris_status;

    // Check APK version
    if (!$this->check_apk_version($version_code)) {
        $result_data = [
            'success' => 0,
            'message' => "This '" . $version_code . "' version is not allowed !!!",
        ];
        return $result_data;
    }

    // Fetch user based on email
    $user = User::where('email', $name)->first();
 //return $user;

    if (!$user) {
        $result_data = [
            'success' => 20,
            'message' => "Username or password not found!!!",
        ];
        return $result_data;
    }
    

 
    $cont_id = $user->cont_id;
    $country1 = (new Country())->country($cont_id);
    $module_type = $country1->module_type;
    

 //return $user->device_imei.'--'.'N';
 
    // Validate device IMEI
   /*if ($device_imei != $user->device_imei) {
        $result_data = [
            'success' => 10,
            'message' => "You are trying to login from the wrong device !!!",
        ];
        return $result_data;
    }else 
    if ($device_imei != $user->device_imei || $user->device_imei != 'N') {
    
        
        $result_data = [
            'success' => 03,
            'message' => "You are trying to login from the wrong device !!!",
        ];
        return $result_data;
    }*/

 //return $name.'--'.$request->password;
    // Perform authentication
    $loginAttempt_n = Auth::attempt(['email' => $name, 'password' => $request->password]);
    $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $request->password, 'lfcl_id' => 1]);

//return $loginAttempt_n;
    if (!$loginAttempt_n) {
        $result_data = [
            'success' => 2,
            'message' => "Username or password might be wrong !!!",
        ];
        return $result_data;
    }

    // Update device IMEI
    $user->device_imei = $device_imei;
    $user->save();

    // Logging login attempt
    $log = new LoginLog();
    $log->user_name = $request->email;
    $log->api_int = $request->version_code;
    $log->device_imei = $request->device_imei;
    $log->device_name = $request->device_name;
    $log->mobile_number = '0';
    $log->auth_type = $hris_status;
    $log->login_attempt = true; // Set to true since login was successful
    $log->save();

    // Fetch employee data
    $db = $user->country()->cont_conn;
    $user_id = $user->id;
    $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
    $emp->aemp_utkn = $this->setEmpToken($user_id);
    $emp->region_id = '';

    // Fetch SPMP and tl_atnt data
    $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();
    $ck_attn = DB::connection($db)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();

    // Handle SPMP and tl_atnt data if available
    $attn_start = $ck_attn ? $ck_attn->atnt_stat : '00:00:00';
    $attn_endt = $ck_attn ? $ck_attn->atnt_etat : '00:00:00';
    $spmp_sdpr1 = $spmp_sdpr ? $spmp_sdpr->spmp_sdpr : 0;
    $spmp_ldpr = $spmp_sdpr ? $spmp_sdpr->spmp_ldpr : 0;

    $emp->attn_start = $attn_start;
    $emp->attn_end = $attn_endt;
    $emp->spmp_sdpr = $spmp_sdpr1;
    $emp->spmp_sdpr_line = $spmp_ldpr;

    // Fetch country data
    $emp->country_name = $country1->cont_name;
    $emp->cheque_day = $country1->GCKCD;
    $emp->cheque_xday = $country1->GTCCD;
    $emp->credit_xday = $country1->GTCRD;
    $emp->route_openblock = $country1->RTBO;
    $emp->min_order_amt = $country1->min_oamt;
    $emp->module_type = $country1->module_type;
    $emp->cont_tzon = $country1->cont_tzon;
    $emp->currency_symb = $country1->cncy_sym;
    $emp->memo_title = $country1->memo_title;
    $emp->memo_sub_title = $country1->memo_sub_title;
    $emp->cont_ogdt = $country1->cont_ogdt;
    $emp->image_folder = $user->country()->cont_imgf;
    $emp->round_digit = $country1->cont_dgit;

    $emp->currency = $user->country()->cont_cncy;
    $emp->round_number = $user->country()->cont_rund;
    $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
    $emp->success = 1;
    $emp->message = "Successful login";
    return $emp;
}
    public function loginMasumSelimIkramHussainSujanAsad(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $version_code = $request->version_code;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';

        if ($this->check_apk_version($version_code)) {


            $loginAttempt = DB::select("select * from users where email='$request->email'");


            if ($loginAttempt) {
                $cont_id1 = $loginAttempt[0]->cont_id;
                $country1 = (new Country())->country($cont_id1);
                $module_type = $country1->module_type;
                $device_imei = $loginAttempt[0]->device_imei;
                if ($module_type == 2) {
                        $token = md5(uniqid(rand(), true));
                        if ($hris_status == 0) {
                            $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                            $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                            if ($loginAttempt) {
                                if (!$loginAttempt_I) {
                                    $result_data = array(
                                        'success' => 0,
                                        'message' => "You are Inactive User !!!",
                                    );

                                    return $result_data;
                                }

                            } else {
                                $succ = 0;
                                $mess = "";
                                $response = $this->hris_validation($name, $password);
                                if (isset($response)) {
                                    $result = json_decode($response);
                                    $status = $result->status;
                                    $message = $result->message;
                                    if (strtolower($status) == 'success') {
                                        $succ = 2;
                                        $mess = $message;
                                    } elseif (strtolower($status) == 'failed') {
                                        $succ = 0;
                                        $mess = $message;
                                    } else {
                                        $succ = 0;
                                        $mess = "Username or password might be wrong !!!";
                                    }
                                } else {
                                    $succ = 0;
                                    $mess = "Username or password might be wrong !!!";
                                }
                                $result_data = array(
                                    'success' => $succ,
                                    'message' => $mess,
                                );
                                // $result_data=[];
                                if ($version_code <= 28) {
                                    $result_data = array(
                                        'success' => 0,
                                        'message' => "Please Update Your App !!!",
                                    );
                                }
                                return $result_data;
                            }
                        } else if ($hris_status == 1) {
                            $loginAttempt = true;
                            $user = User::where('email', '=', $name)->first();
                            if ($user == null) {
                                $country = (new Country())->country(2);
                                $role_id = 1;
                                $amng_id = 3;
                                $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                                $country_data = json_decode(file_get_contents($staff_detiails));
                                $staffResult = json_decode($country_data->StaffResult);

                                DB::beginTransaction();
                                try {
                                    $user = User::create([
                                        'name' => $staffResult[0]->ID,
                                        'email' => trim($staffResult[0]->ID),
                                        'password' => bcrypt(trim($staffResult[0]->ID)),
                                        'remember_token' => $token,
                                        'lfcl_id' => 1,
                                        'cont_id' => $country->id,

                                    ]);
                                    $emp = new Employee();
                                    $emp->setConnection($country->cont_conn);
                                    $emp->aemp_name = $staffResult[0]->NAME;
                                    $emp->aemp_onme = $staffResult[0]->NAME;
                                    $emp->aemp_stnm = $staffResult[0]->NAME;
                                    $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                    $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                    $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                                    $emp->aemp_lued = $user->id;
                                    $emp->edsg_id = 1;
                                    $emp->role_id = $role_id;
                                    $emp->aemp_utkn = $token;
                                    $emp->cont_id = $country->id;
                                    $emp->aemp_iusr = 1;
                                    $emp->aemp_eusr = 1;
                                    $emp->aemp_mngr = 1;
                                    $emp->aemp_lmid = 1;
                                    $emp->aemp_aldt = 0;
                                    $emp->aemp_lcin = '50';
                                    $emp->aemp_otml = 2;
                                    $emp->aemp_lonl = 0;
                                    $emp->aemp_usnm = trim($staffResult[0]->ID);
                                    $emp->aemp_pimg = '';
                                    $emp->aemp_picn = '';
                                    $emp->aemp_emcc = '';
                                    $emp->aemp_crdt = 0;
                                    $emp->aemp_issl = 0;
                                    $emp->site_id = 0;
                                    $emp->lfcl_id = 1;
                                    $emp->amng_id = $amng_id;
                                    $emp->save();
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    return $e;
                                }
                                $db = $user->country()->cont_conn;
                                $cont_id = $user->country()->id;
                                $user_id = $user->id;

                            } else {
                                if ($user->country() != null) {
                                    $db = $user->country()->cont_conn;
                                    $cont_id = $user->country()->id;
                                    $user_id = $user->id;
                                }
                            }
                        }
                        
                        if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                            $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                            $emp->aemp_utkn = $this->setEmpToken($user_id);
                            $emp->region_id = '';

                            $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                            $ck_attn = DB::connection($db)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();//tl_atnt
                            if ($ck_attn == null) {
                                $attn_start = '00:00:00';
                                $attn_endt = '00:00:00';
                            } else {
                                $attn_start = $ck_attn->atnt_stat;
                                $attn_endt = $ck_attn->atnt_etat;
                            }

                            if ($spmp_sdpr == null) {
                                $spmp_sdpr1 = 0;
                                $spmp_ldpr = 0;
                            } else {
                                $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                                $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                            }
                            $emp->attn_start = $attn_start;
                            $emp->attn_end = $attn_endt;

                            $emp->spmp_sdpr = $spmp_sdpr1;
                            $emp->spmp_sdpr_line = $spmp_ldpr;

                            $country1 = (new Country())->country($cont_id);

                            $emp->country_name = $emp->country()->cont_name;
                            $emp->cheque_day = $country1->GCKCD;
                            $emp->cheque_xday = $country1->GTCCD;
                            $emp->credit_xday = $country1->GTCRD;
                            $emp->route_openblock = $country1->RTBO;
                            $emp->min_order_amt = $country1->min_oamt;
                            $emp->module_type = $country1->module_type;
                            $emp->cont_tzon = $country1->cont_tzon;
                            $emp->currency_symb = $country1->cncy_sym;
                            $emp->memo_title = $country1->memo_title;
                            $emp->memo_sub_title = $country1->memo_sub_title;
                            $emp->cont_ogdt = $country1->cont_ogdt;
                            $emp->image_folder = $emp->country()->cont_imgf;
                            $emp->round_digit = $country1->cont_dgit;

                            $emp->currency = $emp->country()->cont_cncy;
                            $emp->round_number = $emp->country()->cont_rund;
                            $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                            $emp->success = 1;
                            $emp->message = "Successful login";
                            return $emp;
                        } 
                        else {
                            $result_data = array(
                                'success' => 2,
                                'message' => "Username or password might be wrong !!!",
                            );
                            return $result_data;
                        }

                    
                } else {
                    $token = md5(uniqid(rand(), true));

                    if ($hris_status == 0) {
                        $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                        $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                        if ($loginAttempt) {
                            if (!$loginAttempt_I) {
                                $result_data = array(
                                    'success' => 0,
                                    'message' => "You are Inactive User !!!",
                                );

                                return $result_data;
                            }

                        } else {
                            $succ = 0;
                            $mess = "";
                            $response = $this->hris_validation($name, $password);
                            if (isset($response)) {
                                $result = json_decode($response);
                                $status = $result->status;
                                $message = $result->message;
                                if (strtolower($status) == 'success') {
                                    $succ = 2;
                                    $mess = $message;
                                } elseif (strtolower($status) == 'failed') {
                                    $succ = 0;
                                    $mess = $message;
                                } else {
                                    $succ = 0;
                                    $mess = "Username or password might be wrong !!!";
                                }
                            } else {
                                $succ = 0;
                                $mess = "Username or password might be wrong !!!";
                            }
                            $result_data = array(
                                'success' => $succ,
                                'message' => $mess,
                            );
                            // $result_data=[];
                            if ($version_code <= 28) {
                                $result_data = array(
                                    'success' => 0,
                                    'message' => "Please Update Your App !!!",
                                );
                            }
                            return $result_data;
                        }
                    } else if ($hris_status == 1) {
                        $loginAttempt = true;
                        $user = User::where('email', '=', $name)->first();
                        if ($user == null) {
                            $country = (new Country())->country(2);
                            $role_id = 1;
                            $amng_id = 3;
                            $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                            $country_data = json_decode(file_get_contents($staff_detiails));
                            $staffResult = json_decode($country_data->StaffResult);

                            DB::beginTransaction();
                            try {
                                $user = User::create([
                                    'name' => $staffResult[0]->ID,
                                    'email' => trim($staffResult[0]->ID),
                                    'password' => bcrypt(trim($staffResult[0]->ID)),
                                    'remember_token' => $token,
                                    'lfcl_id' => 1,
                                    'cont_id' => $country->id,

                                ]);
                                $emp = new Employee();
                                $emp->setConnection($country->cont_conn);
                                $emp->aemp_name = $staffResult[0]->NAME;
                                $emp->aemp_onme = $staffResult[0]->NAME;
                                $emp->aemp_stnm = $staffResult[0]->NAME;
                                $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                                $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                                $emp->aemp_lued = $user->id;
                                $emp->edsg_id = 1;
                                $emp->role_id = $role_id;
                                $emp->aemp_utkn = $token;
                                $emp->cont_id = $country->id;
                                $emp->aemp_iusr = 1;
                                $emp->aemp_eusr = 1;
                                $emp->aemp_mngr = 1;
                                $emp->aemp_lmid = 1;
                                $emp->aemp_aldt = 0;
                                $emp->aemp_lcin = '50';
                                $emp->aemp_otml = 2;
                                $emp->aemp_lonl = 0;
                                $emp->aemp_usnm = trim($staffResult[0]->ID);
                                $emp->aemp_pimg = '';
                                $emp->aemp_picn = '';
                                $emp->aemp_emcc = '';
                                $emp->aemp_crdt = 0;
                                $emp->aemp_issl = 0;
                                $emp->site_id = 0;
                                $emp->lfcl_id = 1;
                                $emp->amng_id = $amng_id;
                                $emp->save();
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                return $e;
                            }
                            $db = $user->country()->cont_conn;
                            $cont_id = $user->country()->id;
                            $user_id = $user->id;

                        } else {
                            if ($user->country() != null) {
                                $db = $user->country()->cont_conn;
                                $cont_id = $user->country()->id;
                                $user_id = $user->id;
                            }
                        }
                    }

                    if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                        $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                        $emp->aemp_utkn = $this->setEmpToken($user_id);
                        $emp->region_id = '';

                        $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                        $ck_attn = DB::connection($db)->table('tl_atnt')->where(['slgp_id' => $emp->slgp_id])->first();//tl_atnt
                        if ($ck_attn == null) {
                            $attn_start = '00:00:00';
                            $attn_endt = '00:00:00';
                        } else {
                            $attn_start = $ck_attn->atnt_stat;
                            $attn_endt = $ck_attn->atnt_etat;
                        }

                        if ($spmp_sdpr == null) {
                            $spmp_sdpr1 = 0;
                            $spmp_ldpr = 0;
                        } else {
                            $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                            $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                        }

                        $emp->attn_start = $attn_start;
                        $emp->attn_end = $attn_endt;

                        $emp->spmp_sdpr = $spmp_sdpr1;
                        $emp->spmp_sdpr_line = $spmp_ldpr;

                        $country1 = (new Country())->country($cont_id);

                        $emp->country_name = $emp->country()->cont_name;
						$emp->cheque_day = $country1->GCKCD;
                        $emp->cheque_xday = $country1->GTCCD;
                        $emp->credit_xday = $country1->GTCRD;
                        $emp->route_openblock = $country1->RTBO;
                        $emp->min_order_amt = $country1->min_oamt;
                        $emp->module_type = $country1->module_type;
                        $emp->cont_tzon = $country1->cont_tzon;
                        $emp->currency_symb = $country1->cncy_sym;
                        $emp->memo_title = $country1->memo_title;
                        $emp->memo_sub_title = $country1->memo_sub_title;
                        $emp->cont_ogdt = $country1->cont_ogdt;
                        $emp->image_folder = $emp->country()->cont_imgf;
                        $emp->round_digit = $country1->cont_dgit;

                        $emp->currency = $emp->country()->cont_cncy;
                        $emp->round_number = $emp->country()->cont_rund;
                        $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                        $emp->success = 1;
                        $emp->message = "Successful login";
                        return $emp;
                    } else {
                        $result_data = array(
                            'success' => 2,
                            'message' => "Username or password might be wrong !!!",
                        );
                        return $result_data;
                    }
                }
            }
        } else {
            $result_data = array(
                'success' => 0,
                'message' => "This '" . $version_code . "' version is not allowed !!!",
            );
            return $result_data;
        }
}

    public
    function hris_validation($user, $pass)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://hris.prangroup.com:8696/Login/GetUserValidation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "username" : "' . $user . '",
            "password" : "' . $pass . '"
             }',
            CURLOPT_HTTPHEADER => array(
                'Key: HR1S019XX78LOGIN',
                'Content-Type: application/json',
                'Cookie: ASP.NET_SessionId=tt1k0ic3iqdopesy5w5k5v4u'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public
    function login(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $version_code = $request->version_code;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';

        if ($this->check_apk_version($version_code)) {

            $token = md5(uniqid(rand(), true));

            if ($hris_status == 0) {
                $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                if ($loginAttempt) {
                    if ($loginAttempt_I) {
                        if (Auth::user()->country() != null) {
                            $db = Auth::user()->country()->cont_conn;
                            $cont_id = Auth::user()->country()->id;
                            $user_id = Auth::user()->id;
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => "You are Inactive User !!!",
                        );

                        return $result_data;
                    }

                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "Username or password might be wrong!!!",
                    );
                    // $result_data=[];
                    return $result_data;
                }

            } else if ($hris_status == 1) {
                $loginAttempt = true;
                $user = User::where('email', '=', $name)->first();
                if ($user == null) {
                    $country = (new Country())->country(2);
                    $role_id = 1;
                    $amng_id = 3;
                    $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                    $country_data = json_decode(file_get_contents($staff_detiails));
                    $staffResult = json_decode($country_data->StaffResult);

                    DB::beginTransaction();
                    try {
                        $user = User::create([
                            'name' => $staffResult[0]->ID,
                            'email' => trim($staffResult[0]->ID),
                            'password' => bcrypt(trim($staffResult[0]->ID)),
                            'remember_token' => $token,
                            'lfcl_id' => 1,
                            'cont_id' => $country->id,
                        ]);
                        $emp = new Employee();
                        $emp->setConnection($country->cont_conn);
                        $emp->aemp_name = $staffResult[0]->NAME;
                        $emp->aemp_onme = $staffResult[0]->NAME;
                        $emp->aemp_stnm = $staffResult[0]->NAME;
                        $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                        $emp->aemp_lued = $user->id;
                        $emp->edsg_id = 1;
                        $emp->role_id = $role_id;
                        $emp->aemp_utkn = $token;
                        $emp->cont_id = $country->id;
                        $emp->aemp_iusr = 1;
                        $emp->aemp_eusr = 1;
                        $emp->aemp_mngr = 1;
                        $emp->aemp_lmid = 1;
                        $emp->aemp_aldt = 0;
                        $emp->aemp_lcin = '50';
                        $emp->aemp_otml = 2;
                        $emp->aemp_lonl = 0;
                        $emp->aemp_usnm = trim($staffResult[0]->ID);
                        $emp->aemp_pimg = '';
                        $emp->aemp_picn = '';
                        $emp->aemp_emcc = '';
                        $emp->aemp_crdt = 0;
                        $emp->aemp_issl = 0;
                        $emp->site_id = 0;
                        $emp->lfcl_id = 1;
                        $emp->amng_id = $amng_id;
                        $emp->save();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                    }
                    $db = $user->country()->cont_conn;
                    $cont_id = $user->country()->id;
                    $user_id = $user->id;

                } else {
                    if ($user->country() != null) {
                        $db = $user->country()->cont_conn;
                        $cont_id = $user->country()->id;
                        $user_id = $user->id;
                    }
                }
            }
            $log = new LoginLog();
            $log->user_name = $request->email;
            $log->api_int = $request->version_code;
            $log->device_imei = "$request->device_imei";
            $log->device_name = "$request->device_name";
            $log->mobile_number = '0';
            $log->auth_type = $hris_status;
            $log->login_attempt = $loginAttempt;
            $log->save();
            if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                $emp->aemp_utkn = $this->setEmpToken($user_id);
                $emp->region_id = '';

                $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                //  $emp->spmp_sdpr = $spmp_sdpr->spmp_sdpr ? $spmp_sdpr->spmp_sdpr : '0';
                if ($spmp_sdpr == null) {
                    $spmp_sdpr1 = 0;
                    $spmp_ldpr = 0;
                } else {
                    $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                    $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                }
                $emp->spmp_sdpr = $spmp_sdpr1;
                $emp->spmp_sdpr_line = $spmp_ldpr;

                $country1 = (new Country())->country($cont_id);

                $emp->country_name = $emp->country()->cont_name;
                // $emp->module_type = $emp->country()->module_type;
                $emp->module_type = $country1->module_type;
                $emp->cont_tzon = $country1->cont_tzon;
                $emp->currency_symb = $country1->cncy_sym;
                $emp->memo_title = $country1->memo_title;
                $emp->memo_sub_title = $country1->memo_sub_title;
                $emp->cont_ogdt = $country1->cont_ogdt;
                $emp->image_folder = $emp->country()->cont_imgf;
                $emp->round_digit = $country1->cont_dgit;

                $emp->currency = $emp->country()->cont_cncy;
                $emp->round_number = $emp->country()->cont_rund;
                $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                $emp->success = 1;
                return $emp;
            } else {
                $result_data = array(
                    'success' => 2,
                    'message' => "Username or password might be wrong !!!",
                );
                return $result_data;
            }
        } else {
            $result_data = array(
                'success' => 0,
                'message' => "This '" . $version_code . "' version is not allowed !!!",
            );
            return $result_data;
        }
    }

    public
    function login_factory(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $version_code = $request->version_code;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';

        if ($this->ft_version($version_code)) {

            $token = md5(uniqid(rand(), true));

            if ($hris_status == 0) {
                $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                $loginAttempt_I = Auth::attempt(['email' => $name, 'password' => $password, 'lfcl_id' => 1]);
                if ($loginAttempt) {
                    if ($loginAttempt_I) {
                        if (Auth::user()->country() != null) {
                            $db = Auth::user()->country()->cont_conn;
                            $cont_id = Auth::user()->country()->id;
                            $user_id = Auth::user()->id;
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => "You are Inactive User !!!",
                        );

                        return $result_data;
                    }

                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "Username or password might be wrong!!!",
                    );
                    // $result_data=[];
                    return $result_data;
                }

            } else if ($hris_status == 1) {
                $loginAttempt = true;
                $user = User::where('email', '=', $name)->first();
                if ($user == null) {
                    $country = (new Country())->country(2);
                    $role_id = 1;
                    $amng_id = 3;
                    $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
                    $country_data = json_decode(file_get_contents($staff_detiails));
                    $staffResult = json_decode($country_data->StaffResult);

                    DB::beginTransaction();
                    try {
                        $user = User::create([
                            'name' => $staffResult[0]->ID,
                            'email' => trim($staffResult[0]->ID),
                            'password' => bcrypt(trim($staffResult[0]->ID)),
                            'remember_token' => $token,
                            'lfcl_id' => 1,
                            'cont_id' => $country->id,
                        ]);
                        $emp = new Employee();
                        $emp->setConnection($country->cont_conn);
                        $emp->aemp_name = $staffResult[0]->NAME;
                        $emp->aemp_onme = $staffResult[0]->NAME;
                        $emp->aemp_stnm = $staffResult[0]->NAME;
                        $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                        $emp->aemp_lued = $user->id;
                        $emp->edsg_id = 1;
                        $emp->role_id = $role_id;
                        $emp->aemp_utkn = $token;
                        $emp->cont_id = $country->id;
                        $emp->aemp_iusr = 1;
                        $emp->aemp_eusr = 1;
                        $emp->aemp_mngr = 1;
                        $emp->aemp_lmid = 1;
                        $emp->aemp_aldt = 0;
                        $emp->aemp_lcin = '50';
                        $emp->aemp_otml = 2;
                        $emp->aemp_lonl = 0;
                        $emp->aemp_usnm = trim($staffResult[0]->ID);
                        $emp->aemp_pimg = '';
                        $emp->aemp_picn = '';
                        $emp->aemp_emcc = '';
                        $emp->aemp_crdt = 0;
                        $emp->aemp_issl = 0;
                        $emp->site_id = 0;
                        $emp->lfcl_id = 1;
                        $emp->amng_id = $amng_id;
                        $emp->save();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                    }
                    $db = $user->country()->cont_conn;
                    $cont_id = $user->country()->id;
                    $user_id = $user->id;

                } else {
                    if ($user->country() != null) {
                        $db = $user->country()->cont_conn;
                        $cont_id = $user->country()->id;
                        $user_id = $user->id;
                    }
                }
            }
            $log = new LoginLog();
            $log->user_name = $request->email;
            $log->api_int = $request->version_code;
            $log->device_imei = "$request->device_imei";
            $log->device_name = "$request->device_name";
            $log->mobile_number = '0';
            $log->auth_type = $hris_status;
            $log->login_attempt = $loginAttempt;
            $log->save();
            if ($loginAttempt && $db != '' && $user_id != '' && $cont_id != '') {
                $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
                $emp->aemp_utkn = $this->setEmpToken($user_id);
                $emp->region_id = '';

                $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();//spmp
                //  $emp->spmp_sdpr = $spmp_sdpr->spmp_sdpr ? $spmp_sdpr->spmp_sdpr : '0';
                if ($spmp_sdpr == null) {
                    $spmp_sdpr1 = 0;
                    $spmp_ldpr = 0;
                } else {
                    $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                    $spmp_ldpr = $spmp_sdpr->spmp_ldpr;
                }
                $emp->spmp_sdpr = $spmp_sdpr1;
                $emp->spmp_sdpr_line = $spmp_ldpr;

                $country1 = (new Country())->country($cont_id);

                $emp->country_name = $emp->country()->cont_name;
                // $emp->module_type = $emp->country()->module_type;
                $emp->module_type = $country1->module_type;
                $emp->cont_tzon = $country1->cont_tzon;
                $emp->currency_symb = $country1->cncy_sym;
                $emp->memo_title = $country1->memo_title;
                $emp->memo_sub_title = $country1->memo_sub_title;
                $emp->cont_ogdt = $country1->cont_ogdt;
                $emp->image_folder = $emp->country()->cont_imgf;
                $emp->round_digit = $country1->cont_dgit;

                $emp->currency = $emp->country()->cont_cncy;
                $emp->round_number = $emp->country()->cont_rund;
                $emp->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
                $emp->success = 1;
                return $emp;
            } else {
                $result_data = array(
                    'success' => 2,
                    'message' => "Username or password might be wrong !!!",
                );
                return $result_data;
            }
        } else {
            $result_data = array(
                'success' => 0,
                'message' => "This '" . $version_code . "' version is not allowed !!!",
            );
            return $result_data;
        }
    }

    public
    function countryChange(Request $request)
    {
        $country0 = (new Country())->country($request->country0_id);
        $country1 = (new Country())->country($request->country1_id);
        if ($country1) {
            $employee = Employee::on($country1->cont_conn)->where(['aemp_lued' => $request->user_id])->first();
            if ($employee == null) {
                $emp = Employee::on($country0->cont_conn)->where(['aemp_lued' => $request->user_id])->first();
                if ($emp != null) {
                    $employee = new Employee();
                    $employee->setConnection($country1->cont_conn);
                    $employee->aemp_name = $emp->aemp_name;
                    $employee->aemp_onme = $emp->aemp_onme;
                    $employee->aemp_stnm = $emp->aemp_stnm;
                    $employee->aemp_mob1 = $emp->aemp_mob1;
                    $employee->aemp_dtsm = $emp->aemp_dtsm;
                    $employee->aemp_emal = $emp->aemp_emal;
                    $employee->aemp_lued = $emp->aemp_lued;
                    $employee->edsg_id = 1;
                    $employee->role_id = $emp->role_id;
                    $employee->aemp_utkn = md5(uniqid(rand(), true));
                    $employee->cont_id = $country1->id;
                    $employee->aemp_iusr = 1;
                    $employee->aemp_eusr = 1;
                    $employee->aemp_mngr = 1;
                    $employee->aemp_lmid = 1;
                    $employee->aemp_aldt = 0;
                    $employee->aemp_lcin = '50';
                    $employee->aemp_otml = 2;
                    $employee->aemp_lonl = $emp->aemp_lonl;
                    $employee->aemp_usnm = $emp->aemp_usnm;
                    $employee->aemp_pimg = '';
                    $employee->aemp_picn = '';
                    $employee->aemp_pimg = $emp->aemp_pimg;
                    $employee->aemp_picn = $emp->aemp_picn;
                    $employee->aemp_emcc = $emp->aemp_emcc;
                    $employee->site_id = 0;
                    $employee->aemp_crdt = 0;
                    $employee->amng_id = 3;
                    $employee->aemp_issl = isset($emp->aemp_issl) ? $emp->aemp_issl : 0;
                    $employee->lfcl_id = 1;
                    $employee->save();
                }

            }

            $user = User::findorfail($request->user_id);
            $user->cont_id = $request->country1_id;
            $user->save();
            $employee->region_id = '';
            $employee->country_name = $country1->cont_name;
            $employee->module_type = $country1->module_type;
            $employee->image_folder = $country1->cont_imgf;
            $employee->round_digit = $country1->cont_dgit;
            $employee->currency = $country1->cont_cncy;
            $employee->round_number = $country1->cont_rund;
            $employee->aemp_utkn = $user->remember_token;
            return $employee;
        }


    }

    public
    function setEmpToken($userId)
    {
        $user = User::find($userId);
        $user->remember_token = time() . $userId . substr(md5(mt_rand()), 5, 22);
        $user->save();
        return $user->remember_token;
    }

    public
    function employeeMemu(Request $request)
    {
        $tst = array();
        $user_id = $request->user_id;
        $country_id = $request->country_id;
        $role_id = $request->role_id;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            /*$tst = DB::select("SELECT
  concat(t1.id,t2.amnu_code, t1.aumn_vsbl,t2.amnu_oseq) AS column_id,
  t1.users_id                 AS emp_id,
  t2.amnu_code                AS menu_id,
  t2.amnu_oseq                as amnu_oseq
FROM tl_aumn t1
  INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
WHERE t1.users_id = $request->user_id AND t1.aumn_vsbl =1 and t1.cont_id=$country_id "); */

            $tst = DB::select("SELECT
  t1.column_id,
  t1.emp_id,
  t1.menu_id,
  t1.amnu_oseq
FROM (
       SELECT
         concat($user_id,t2.amnu_code, t1.aumn_vsbl, t2.amnu_oseq) AS column_id,
         t1.users_id                                      AS emp_id,
         t2.amnu_code                                     AS menu_id,
         t2.amnu_oseq                                     AS amnu_oseq
       FROM tl_aumn t1
         INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
       WHERE t1.users_id = $user_id AND t1.aumn_vsbl = 1 AND t1.cont_id = $country_id
       UNION ALL
       SELECT
         concat($user_id,t3.amnu_code, 1, t3.amnu_oseq) AS column_id,
         $user_id                              AS emp_id,
         t3.amnu_code                          AS menu_id,
         t3.amnu_oseq                          AS amnu_oseq
       FROM tm_amng AS t1
         INNER JOIN tl_amnd t2 ON t2.amng_id = t1.id
         INNER JOIN tm_amnu AS t3 ON t2.amnu_id = t3.id
       WHERE t1.cont_id = $country_id AND t1.role_id = $role_id
     ) AS t1
GROUP BY t1.column_id, t1.emp_id, t1.menu_id, t1.amnu_oseq");
        }

        // array_push($tst, (object)array("column_id" => "0$user_id", "emp_id" => $user_id, "menu_id" => "0", "amnu_oseq" => "0"));
        return Array("tblt_emp_menu" => array("data" => $tst, "action" => $request->input('user_id')));
    }

    public
    function employeeMemuNew(Request $request)
    {
        $tst = array();
        $emp_id = $request->emp_id;
        //$user_id = $request->user_id;
        $country_id = $request->country_id;
        $role_id = $request->role_id;
        $amng_id = $request->amng_id;
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.column_id,
  t1.emp_id,
  t1.menu_id,
  t1.menu_name,
  t1.icon_url,
  t1.amnu_oseq
FROM (
       SELECT
         concat($emp_id, t2.amnu_code, t1.aumn_vsbl, t2.amnu_oseq) AS column_id,
         t1.aemp_id                                          AS emp_id,
         t2.amnu_code                                        AS menu_id,
         t2.amnu_name                                        AS menu_name,
         t2.amnu_iurl                                        AS icon_url,
         t2.amnu_oseq                                        AS amnu_oseq
       FROM tl_aumn t1
         INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
       WHERE t1.aemp_id = $emp_id AND t1.aumn_vsbl = 1 AND t1.cont_id = $country_id
       UNION ALL
       SELECT
         concat($emp_id, t3.amnu_code, 1, t3.amnu_oseq) AS column_id,
         $emp_id                                  AS emp_id,
         t3.amnu_code                             AS menu_id,
         t3.amnu_name                             AS menu_name,
         t3.amnu_iurl                             AS icon_url,
         t3.amnu_oseq                             AS amnu_oseq
       FROM tm_amng AS t1
         INNER JOIN tl_amnd t2 ON t2.amng_id = t1.id
         INNER JOIN tm_amnu AS t3 ON t2.amnu_id = t3.id
       WHERE t1.cont_id = $country_id AND (t1.role_id = $role_id OR t1.id = $amng_id)
     ) AS t1
GROUP BY t1.column_id, t1.emp_id, t1.menu_id,t1.menu_name,t1.icon_url,t1.amnu_oseq");
        }
        /*

SELECT
  t1.column_id,
  t1.emp_id,
  t1.menu_id,
  t1.amnu_oseq
FROM (
       SELECT
         concat($user_id,t2.amnu_code, t1.aumn_vsbl, t2.amnu_oseq) AS column_id,
         t1.users_id                                      AS emp_id,
         t2.amnu_code                                     AS menu_id,
         t2.amnu_oseq                                     AS amnu_oseq
       FROM tl_aumn t1
         INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
       WHERE t1.users_id = $user_id AND t1.aumn_vsbl = 1 AND t1.cont_id = $country_id
       UNION ALL
       SELECT
         concat($user_id,t3.amnu_code, 1, t3.amnu_oseq) AS column_id,
         $user_id                              AS emp_id,
         t3.amnu_code                          AS menu_id,
         t3.amnu_oseq                          AS amnu_oseq
       FROM tm_amng AS t1
         INNER JOIN tl_amnd t2 ON t2.amng_id = t1.id
         INNER JOIN tm_amnu AS t3 ON t2.amnu_id = t3.id
       WHERE t1.cont_id = $country_id AND (t1.role_id = $role_id OR t1.id = $amng_id)
     ) AS t1
GROUP BY t1.column_id, t1.emp_id, t1.menu_id, t1.amnu_oseq*/

        return Array("tblt_emp_menu" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public
    function validToken(Request $request)
    {
        return $users = User::where(['id' => $request->user_id, 'remember_token' => $request->user_token])->first() ? '1' : '0';
    }

    public
    function spd_per(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $spmp_sdpr1 = array('spmp_sdpr' => 0);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $spmp_sdpr = SPDS::on($db_conn)->where(['slgp_id' => $request->user_slgp, 'zone_id' => $request->user_zone])->first();
            if ($spmp_sdpr == null) {
                $spmp_sdpr1 = array('spmp_sdpr' => 0);
            } else {
                $spmp_sdpr1 = array('spmp_sdpr' => $spmp_sdpr->spmp_sdpr);
            }
            // return $spmp_sdpr;
            // $spmp_sdpr1 = array('spmp_sdpr' => $spmp_sdpr->spmp_sdpr == null ? '0' : $spmp_sdpr->spmp_sdpr);

            /*$data1 = DB::connection($db_conn)->select("
            SELECT t1.spmp_sdpr
            from tm_spmp t1 JOIN tm_aemp t2 ON (t1.slgp_id=t2.slgp_id) AND t1.zone_id=t2.zone_id
            WHERE t2.id='$request->emp_id'");
            $spmp_sdpr1 =  array('spmp_sdpr' => $data1->spmp_sdpr ? $data1->spmp_sdpr : '0');*/
        }

        return $spmp_sdpr1;

        // return $rows;
    }

    public
    function userRewordPoint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $userRewordPoint = array('userRewordPoint' => 0);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $redM = RewardMaster::on($db_conn)->where(['aemp_id' => $request->emp_id])->first();
            if ($redM == null) {
                $userRewordPoint = array('userRewordPoint' => 0);
            } else {
                $userRewordPoint = array('userRewordPoint' => $redM->rwdm_rpnt);
            }
        }
        return $userRewordPoint;
    }

    public
    function appUrl(Request $request)
    {
        return array(
            'Test' => "http://sprobo.sihirfms.com/",
            'Manik' => "http://172.17.107.136:8080/spro/spro_web/",
            'Raju' => "http://172.17.107.136:80/",

        );
    }

    public
    function HRISLoginData()
    {
        return array(
            'url' => "http://hris.prangroup.com:8696/Login/GetUserValidation",
            'Key' => "HR1S019XX78LOGIN",
        );

        $user = '156827';
        $pass = '1134750';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://hris.prangroup.com:8696/Login/GetUserValidation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "username" : "' . $user . '",
            "password" : "' . $pass . '"
             }',
            CURLOPT_HTTPHEADER => array(
                'Key: HR1S019XX78LOGIN',
                'Content-Type: application/json',
                'Cookie: ASP.NET_SessionId=tt1k0ic3iqdopesy5w5k5v4u'
            ),
        ));

        $response = curl_exec($curl);
        $status = "";
        curl_close($curl);
        /* if(isset($response)){
            // $result=json_decode($response);
            // return $status=$result->message;
             foreach($response as $val){
                 return  $status=$val->message;
             }
         }*/
        return $response;
    }

    public
    function update(Request $request)
    {
        $nenVersion = "1.0.13";
        $current_apk = $request->current_apk_version;
        $current_apk1 = $request->current_apk_version;
        // if ($current_apk == "1.0.11" || $current_apk == "1.0.8" || $current_apk == "1.0.9" || $current_apk == "1.0.10") {
        if ($current_apk == "1.0.13" || $current_apk == "1.0.12") {
            $current_apk = $nenVersion;
        }
        $current_apk_version = 'my_prg-' . $current_apk . '.apk';
        $filename = "my_prg-$nenVersion.apk";
        $return_obj = null;
        if ($filename == $current_apk_version) {
            $return_obj = array(
                'fileName' => null,
                "newVersion" => $current_apk1
            );
        } else {
            $return_obj = array(
                'fileName' => $filename,
                "newVersion" => $nenVersion);
        }
        return $return_obj;
    }

    public
    function current_Version_Check(Request $request)
    {

        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet akk",
        );
        $nenVersion = "13";

        $current_apk = $request->current_apk_version;

        if ($current_apk == '13' || $current_apk == '14' || $current_apk == '15') {

            $result_data = array(
                'success' => 1,
                'message' => "This is Right Version !!!  ",
            );
        } else {
            $result_data = array(
                'success' => 0,
                'message' => "This '" . $current_apk . "' Version Not Allowed !!!",
            );
        }

        return $result_data;
    }


    public
    function time(Request $request)
    {
        $serverDateTime = date('Y-m-d H:i:s');
        $apkTime = strtotime($request->timestamp);
        $serverTime = strtotime($serverDateTime);
        $rows = array('status' => 0, 'server_time' => $serverDateTime);
        $timeDifference = round(abs($serverTime - $apkTime) / 60, 0);
        if ($timeDifference >= 30) {
            //if ($timeDifference >= 720) {
            $rows = array('status' => round($timeDifference), 'server_time' => $serverDateTime);
            // $rows = array('status' => '720', 'server_time' => $serverDateTime);
        }
        return $rows;
    }

    public
    function Test_Live(Request $request)
    {
        $now = now();
        // return $last_3 = substr($now->timestamp . $now->milli, 10);
        return $now->timestamp . '----' . $now->milli . '---' . microtime();

        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;

            if ($db_conn != '') {
                $country1 = (new Country())->country($request->country_id);
                $cont_tzon = $country1->cont_tzon;
                $date = Carbon::now($cont_tzon)->format('Y-m-d');
                $da_st = $date . ' ' . '06:00:00';
                $da_set = $date . ' ' . '13:00:59';
                $daf_st = $date . ' ' . '13:01:00';
                $daf_set = $date . ' ' . '23:59:59';

                $sql_1st = DB::connection($db_conn)->select(
                    "SELECT sum(TP_AMT)T_AMT,sum(VISITED_PRO)VISITED_PRO, sum(VISITED_NON) VISITED_NON FROM
(SELECT round(SUM(ordm_amnt),2)AS TP_AMT,(COUNT(`site_id`))AS VISITED_PRO,0 AS VISITED_NON
FROM `tt_ordm` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$da_st'AND'$da_set')
UNION ALL
SELECT 0 T_AMT,0 VISITED_PRO,COUNT(`site_id`)AS VISITED_NON 
FROM `tt_npro` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$da_st'AND'$da_set'))t1");

                $sql_2nd = DB::connection($db_conn)->select(
                    "SELECT sum(TP_AMT)T_AMT,sum(VISITED_PRO)VISITED_PRO, sum(VISITED_NON) VISITED_NON FROM
(SELECT round(SUM(ordm_amnt),2)AS TP_AMT,(COUNT(`site_id`))AS VISITED_PRO,0 AS VISITED_NON
FROM `tt_ordm` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$daf_st'AND'$daf_set')
UNION ALL
SELECT 0 T_AMT,0 VISITED_PRO,COUNT(`site_id`)AS VISITED_NON 
FROM `tt_npro` WHERE `aemp_id`=$request->aemp_id
AND (`created_at` BETWEEN '$daf_st'AND'$daf_set'))t1");

                $sql_rout_out = DB::connection($db_conn)->select(
                    "SELECT count(t3.site_id)AS t_outlet  
  FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id 
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  WHERE t1.aemp_id=$request->aemp_id AND t1.rout_id=$request->rout_id;");

                return array(
                    "first_half" => $sql_1st[0],
                    "second_half" => $sql_2nd[0],
                    "rout_outlet" => $sql_rout_out[0],
                    "response_time" => date('Y-m-d h:i:s'),
                );
            }

        }
        // return '';
    }

    public
    function trackError(Request $request)
    {
        DB::beginTransaction();
        try {
            $errorTrack = new ErrorTrack();
            $errorTrack->emp_id = $request->sr_id;
            $errorTrack->error = $request->error;
            $errorTrack->status_id = 1;
            $errorTrack->country_id = 1;
            $errorTrack->save();
            DB::commit();
            return array('column_id' => $request->id, 'message' => 'success');
        } catch (\Exception $e) {
            DB::commit();
            Log::error($e);
            Log::error($request);
            return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]));
            // return array('column_id' => $request->id);
        }
    }

    public
    function trackGPSSave(Request $request)
    {
        $country = (new Country())->country($request->cont_id);
        if ($country) {
            if ($request->line_data != '[]') {
                $dataLines = json_decode($request->line_data);
                if ($dataLines > 0) {
                    $locationHistory = [];
                    for ($i = 0; $i < count($dataLines); $i++) {
                        if ($dataLines[$i]->user_id != 0 and $dataLines[$i]->country_id != 0) {
                            $locationHistory[] = [
                                'aemp_id' => $dataLines[$i]->user_id,
                                'geo_lat' => $dataLines[$i]->lat,
                                'geo_lon' => $dataLines[$i]->lon,
                                'hloc_date' => date("Y-m-d", strtotime($dataLines[$i]->times_stamp)),
                                'hloc_time' => $dataLines[$i]->times_stamp,
                                'hloc_addr' => isset($dataLines[$i]->address) ? $dataLines[$i]->address : '',
                                'cont_id' => $request->cont_id,
                            ];
                        }
                    }

                    DB::connection($country->cont_conn)->beginTransaction();
                    try {
                        DB::connection($country->cont_conn)->table('th_hloc')->insert($locationHistory);
                        $location1 = (object)end($locationHistory);
                        $location = Location::on($country->cont_conn)->where(['aemp_id' => $location1->aemp_id])->first();
                        if ($location == null && $location1->aemp_id != 0) {
                            $employee = Employee::on($country->cont_conn)->findorfail($location1->aemp_id);
                            if ($employee != null) {
                                $location = new Location();
                                $location->setConnection($country->cont_conn);
                                $location->aemp_id = $location1->aemp_id;
                                $location->aemp_usnm = $employee->aemp_usnm;
                                $location->geo_lat = $location1->geo_lat;
                                $location->geo_lon = $location1->geo_lon;
                                $location->lloc_addr = "$location1->hloc_addr";
                                $location->lloc_date = date("Y-m-d", strtotime($location1->hloc_time));
                                $location->lloc_time = $location1->hloc_time;
                                $location->cont_id = $request->cont_id;
                                $location->save();
                            }

                        } else if ($location1->aemp_id != 0) {
                            $location->geo_lat = $location1->geo_lat;
                            $location->geo_lon = $location1->geo_lon;
                            $location->lloc_time = $location1->hloc_time;
                            $location->lloc_addr = "$location1->hloc_addr";
                            $location->lloc_date = date("Y-m-d", strtotime($location1->hloc_time));
                            $location->save();
                        }
                        DB::connection($country->cont_conn)->commit();
                        return array('column_id' => max(array_column($dataLines, 'id')));
                    } catch (\Exception $e) {
                        return $e;
                        // return array('column_id' => 0);
                    }

                } else {
                    return array('column_id' => 0);
                }
            } else {
                return array('column_id' => 0);
            }
        }

    }

    public
    function trackGPS(Request $request)
    {
        $country = (new Country())->country($request->cont_id);
        if ($country) {
            $location = Location::on($country->cont_conn)->where(['aemp_id' => $request->aemp_id])->first();
            if ($location == null && $request->aemp_id != 0) {
                $emp = Employee::on($country->cont_conn)->where('id', '=', $request->aemp_id)->first();
                $location = new Location();
                $location->setConnection($country->cont_conn);
                $location->aemp_id = $request->aemp_id;
                $location->aemp_usnm = $emp->aemp_usnm;
                $location->geo_lat = $request->geo_lat;
                $location->geo_lon = $request->geo_lon;
                $location->lloc_addr = "$request->address";
                $location->lloc_date = date("Y-m-d", strtotime($request->date_time));
                $location->lloc_time = $request->date_time;
                $location->cont_id = $request->cont_id;
                $location->save();
            } else if ($request->aemp_id != 0) {
                $location->geo_lat = $request->geo_lat;
                $location->geo_lon = $request->geo_lon;
                $location->lloc_time = $request->date_time;
                $location->lloc_addr = "$request->address";
                $location->lloc_date = date("Y-m-d", strtotime($request->date_time));
                $location->save();
            }
            if ($request->aemp_id != 0) {
                $locationHistory = new LocationHistory();
                $locationHistory->setConnection($country->cont_conn);
                $locationHistory->aemp_id = $request->aemp_id;
                $locationHistory->geo_lat = $request->geo_lat;
                $locationHistory->geo_lon = $request->geo_lon;
                $locationHistory->hloc_addr = "$request->address";
                $locationHistory->hloc_date = date("Y-m-d", strtotime($request->date_time));
                $locationHistory->hloc_time = $request->date_time;
                $locationHistory->cont_id = $request->cont_id;
                $locationHistory->save();
            }
        }
        return array('column_id' => $request->cont_id);
    }

    public
    function country(Request $request)
    {
        $country = Country::all();
        return Array("receive_data" => array("data" => $country, "action" => $request->input('user_id')));
    }

    public
    function fetchSuperHeroData(Request $request)
    {

        $country = (new Country())->country($request->countryId);

        $slgpId = $request->empGroupId;
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $slgpCode = DB::connection($db_conn)->select("select slgp_code as slgpCode from tm_slgp where id = $slgpId");
            $salesGroupCode = $slgpCode[0]->slgpCode;

            DB::connection($db_conn)->select("SET @row_number = 0");
            $superHero = DB::connection($db_conn)->select("                
                select (@row_number:=@row_number + 1) AS position,  EMPLOYEENAME AS name, STAFFID AS staffId, ZONE_NAME as zone, SPRO_IMG as image
                from tbld_new_leaderboard_ss_ps 
                where LEADER_TYPE = 1 AND  DIGR_TEXT= $salesGroupCode
                order by ACHV_AMOUNT DESC limit 50");

            return Array(
                "superHero" => $superHero,
            );


        }

    }

    public
    function fetchSuperManagerData(Request $request)
    {

        $country = (new Country())->country($request->countryId);
        $slgpCode = $request->empGroupCode;

        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            DB::connection($db_conn)->select("SET @row_number = 0");
            $superManager = DB::connection($db_conn)->select("
            select (@row_number:=@row_number + 1) AS position, EMPLOYEENAME AS name, STAFFID AS staffId, ZONE_NAME as zone, SPRO_IMG as image
            from tbld_new_leaderboard_ss_ps 
            where LEADER_TYPE = 2 AND  DIGR_TEXT= $slgpCode
            order by ACHV_AMOUNT DESC limit 50");

            return Array(
                "superManager" => $superManager
            );

        }
    }

    public
    function getUserGroupList(Request $request)
    {

        $country = (new Country())->country($request->countryId);
        $empId = $request->empId;
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data = DB::connection($db_conn)->select("
            select t2.id,t2.slgp_code as code,t2.slgp_name as name  from tm_aemp as t1
            inner join tm_slgp as t2 on t1.slgp_id = t2.id
            where t1.id = $empId
            UNION
            select t3.id,t3.slgp_code,t3.slgp_name  from tl_egpr as t1 
            inner join tm_slgp as t3 on t3.id = t1.group_id
            where t1.aemp_id = $empId ");

            return Array(
                "salesGroups" => $data,
            );
        }
    }

    public
    function noteList(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.id                                          AS note_id,
  t1.site_code                                   AS site_id,
  IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
  DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.note_body                                   AS title,
  ''                                             AS note,
  t1.lfcl_id                                     AS status_id,
  t3.lfcl_name                                   AS status_name,
  t1.ntpe_id                                     AS type_id,
  t4.ntpe_name                                   AS type_name,
  group_concat(t5.nimg_imag)                     AS image_name,
  t1.aemp_id                                     AS emp_id,
  concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name
FROM tt_note AS t1
  INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
  INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
  LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
  INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
  INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id  
  LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
  LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
WHERE t8.aemp_id=$request->emp_id and  t1.note_date BETWEEN '$request->start_date'and '$request->end_date'
GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,
  t1.ntpe_id, t4.ntpe_name,
  t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }

    public
    function noteSummaryQty(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("
SELECT 
SUM(emp_id)AS own_task,sum(assign_from)AS assign_from,sum(assign_to)AS assign_to FROM 
  (SELECT
  count(t1.aemp_id)                                 AS emp_id,
  count(t12.asin_user)                              AS assign_from,
  count(t11.asin_user)                              AS assign_to
  FROM tt_note AS t1
  LEFT JOIN tl_nasn t12 ON(t1.aemp_id=t12.asin_user) AND t12.enmp_date = curdate()
  LEFT JOIN tl_nasn t11 ON(t1.id=t11.note_id) AND t11.enmp_date = curdate()
WHERE t1.aemp_id=$request->emp_id and  t1.note_date = curdate()
GROUP BY t1.aemp_id,t12.aemp_id,t11.asin_user)tt;");
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }

    public
    function noteSiteWiseUserListReport(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {

            /* $tst = DB::connection($country->cont_conn)->select("
  SELECT
    t1.id                                          AS note_id,
    t1.site_code                                   AS site_id,
    IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
    DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
    t1.note_body                                   AS title,
    ''                                             AS note,
    t1.lfcl_id                                     AS status_id,
    t3.lfcl_name                                   AS status_name,
    t1.ntpe_id                                     AS type_id,
    t4.ntpe_name                                   AS type_name,
    group_concat(t5.nimg_imag)                     AS image_name,
    t1.aemp_id                                     AS emp_id,
    concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name,
    0                                              AS assign_from,
    t11.asin_user                                  AS assign_to,
    if(t13.nasn_life=1,'done','pending')           AS assign_status,
    ''                                             AS assign_from_emp,
    concat(t60.aemp_usnm, ' - ', t60.aemp_name)    AS assign_to_emp
  FROM tt_note AS t1
    INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
    INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
    LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
    INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
    INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id
    LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
    LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
    LEFT JOIN tl_nasn t11 ON(t1.id=t11.note_id) AND t11.enmp_date BETWEEN '$request->start_date'and '$request->end_date'
    LEFT JOIN tl_nasn t13 ON(t1.id=t13.note_id)
    LEFT JOIN tm_aemp AS t60 ON t11.asin_user = t60.id
  WHERE t1.aemp_id=$request->emp_id and  t1.note_date BETWEEN '$request->start_date'and '$request->end_date' AND t1.aemp_id!=t11.asin_user
  GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,t11.asin_user,t13.nasn_life,
    t1.ntpe_id, t4.ntpe_name,t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name;");*/
            $tst = DB::connection($country->cont_conn)->select("
 SELECT
   t1.id                                          AS note_id,
   t1.site_code                                   AS site_id,
   IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
   DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
   t1.note_body                                   AS title,
   ''                                             AS note,
   t1.lfcl_id                                     AS status_id,
   t3.lfcl_name                                   AS status_name,
   t1.ntpe_id                                     AS type_id,
   t4.ntpe_name                                   AS type_name,
   group_concat(t5.nimg_imag)                     AS image_name,
   t1.aemp_id                                     AS emp_id,
   concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name,
   0                                              AS assign_from,
   t11.asin_user                                  AS assign_to,
   if(t13.nasn_life=1,'done','pending')           AS assign_status,
   ''                                             AS assign_from_emp,
   concat(t60.aemp_usnm, ' - ', t60.aemp_name)    AS assign_to_emp
 FROM tt_note AS t1
   INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
   INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
   LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
   INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
   LEFT JOIN tl_enmp AS t8 ON t1.id = t8.note_id
   LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
   LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
   LEFT JOIN tl_nasn t11 ON(t1.id=t11.note_id)
   LEFT JOIN tl_nasn t13 ON(t1.id=t13.note_id)
   LEFT JOIN tm_aemp AS t60 ON t11.asin_user = t60.id
 WHERE t1.note_date>=DATE_SUB(curdate(), INTERVAL 15 DAY) AND t1.`site_code`='$request->site_code'
 GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,t11.asin_user,t13.nasn_life,
   t1.ntpe_id, t4.ntpe_name,t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name;");

            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public
    function noteListReportFromTo(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            if ($request->type == '1') {
                $tst = DB::connection($country->cont_conn)->select("
 SELECT
   t1.id                                          AS note_id,
   t1.site_code                                   AS site_id,
   IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
   DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
   t1.note_body                                   AS title,
   ''                                             AS note,
   t1.lfcl_id                                     AS status_id,
   t3.lfcl_name                                   AS status_name,
   t1.ntpe_id                                     AS type_id,
   t4.ntpe_name                                   AS type_name,
   group_concat(t5.nimg_imag)                     AS image_name,
   t1.aemp_id                                     AS emp_id,
   concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name,
   0                                              AS assign_from,
   t11.asin_user                                  AS assign_to,
   if(t11.nasn_life=1,'done','pending')           AS assign_status,
   ''                                             AS assign_from_emp,
   concat(t60.aemp_usnm, ' - ', t60.aemp_name)    AS assign_to_emp
 FROM tt_note AS t1
   INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
   INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
   LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
   INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
   INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id
   LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
   LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
   LEFT JOIN tl_nasn t11 ON(t1.id=t11.note_id) AND t11.enmp_date BETWEEN '$request->start_date'and '$request->end_date'
   LEFT JOIN tm_aemp AS t60 ON t11.asin_user = t60.id
 WHERE t1.aemp_id=$request->emp_id and  t1.note_date BETWEEN '$request->start_date'and '$request->end_date' 
 GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,t11.asin_user,t11.nasn_life,
   t1.ntpe_id, t4.ntpe_name,t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name;");
            } else {
                $tst = DB::connection($country->cont_conn)->select("
 SELECT
   t1.id                                          AS note_id,
   t1.site_code                                   AS site_id,
   IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
   DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
   t1.note_body                                   AS title,
   ''                                             AS note,
   t1.lfcl_id                                     AS status_id,
   t3.lfcl_name                                   AS status_name,
   t1.ntpe_id                                     AS type_id,
   t4.ntpe_name                                   AS type_name,
   group_concat(t5.nimg_imag)                     AS image_name,
   t1.aemp_id                                     AS emp_id,
   concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name,
   t12.asin_user                                  AS assign_from,
   0                                              AS assign_to,
   if(t12.nasn_life=1,'done','pending')           AS assign_status,
   concat(t60.aemp_usnm, ' - ', t60.aemp_name)    AS assign_from_emp,
   ''                                             AS assign_to_emp
 FROM tt_note AS t1
   INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
   INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
   LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
   INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
   INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id
   LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
   LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
   LEFT JOIN tl_nasn t12 ON(t1.id=t12.note_id) AND t12.enmp_date BETWEEN '$request->start_date'and '$request->end_date'
   LEFT JOIN tm_aemp AS t60 ON t12.asin_user = t60.id
   WHERE t12.asin_user=$request->emp_id and  t12.enmp_date BETWEEN '$request->start_date'and '$request->end_date'
   GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,t12.asin_user,t12.nasn_life,
   t1.ntpe_id, t4.ntpe_name,t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name;");
            }
            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }
    }

    public
    function noteListReport(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {


            $tst = DB::connection($country->cont_conn)->select("
SELECT
  t1.id                                          AS note_id,
  t1.site_code                                   AS site_id,
  IF(t1.site_code = t9.locd_code, concat(t1.site_code, ' - ', t9.locd_name), concat(t1.site_code, ' - ', t10.site_name))AS site_name,
  DATE_FORMAT(t1.note_dtim, '%Y-%m-%d %h:%i %p') AS date_time,
  t1.note_body                                   AS title,
  ''                                             AS note,
  t1.lfcl_id                                     AS status_id,
  t3.lfcl_name                                   AS status_name,
  t1.ntpe_id                                     AS type_id,
  t4.ntpe_name                                   AS type_name,
  group_concat(t5.nimg_imag)                     AS image_name,
  t1.aemp_id                                     AS emp_id,
  concat(t6.aemp_usnm, ' - ', t6.aemp_name)      AS emp_name
FROM tt_note AS t1
  INNER JOIN tm_lfcl AS t3 ON t1.lfcl_id = t3.id
  INNER JOIN tm_ntpe AS t4 ON t1.ntpe_id = t4.id
  LEFT JOIN tl_nimg AS t5 ON t1.id = t5.note_id
  INNER JOIN tm_aemp AS t6 ON t1.aemp_id = t6.id
  INNER JOIN tl_enmp AS t8 ON t1.id = t8.note_id  
  LEFT JOIN tm_locd AS t9 ON t1.site_code = t9.locd_code
  LEFT JOIN tm_site AS t10 ON t1.site_code=t10.site_code
WHERE t8.aemp_id=$request->emp_id and  t1.note_date BETWEEN '$request->start_date'and '$request->end_date'
GROUP BY t1.id, t1.site_code, t1.note_dtim,  t1.note_body, t1.lfcl_id, t3.lfcl_name,
  t1.ntpe_id, t4.ntpe_name,
  t1.aemp_id, t6.aemp_usnm, t6.aemp_name , t9.locd_name");

            return Array("receive_data" => array("data" => $tst, "action" => $request->input('emp_id')));
        }

    }

    public
    function siteRoutePlan(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT t4.aemp_id,
  t5.aemp_usnm,
  concat(t5.aemp_name,'(',t5.aemp_usnm,')') as aemp_name,
  t5.aemp_mob1,
  group_concat(DISTINCT LEFT(t4.rpln_day, 3)) AS day_name,
  t7.slgp_name,
  t1.cont_id,
 max(t17.created_at) last_visited
FROM tm_site AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id and t1.id='$request->site_id'
  INNER JOIN tm_rout AS t3 ON t2.rout_id = t3.id
  INNER JOIN tl_rpln AS t4 ON t3.id = t4.rout_id
  INNER JOIN tm_aemp AS t5 ON t4.aemp_id = t5.id
  inner JOIN tl_sgsm AS t6 ON t4.aemp_id = t6.aemp_id
  inner JOIN tm_slgp AS t7 ON t6.slgp_id = t7.id
  LEFT JOIN th_ssvh AS t17 ON t1.id = t17.site_id and t17.ssvh_date > current_date()- interval 30 day and t17.aemp_id=t5.id
WHERE  t1.lfcl_id = 1 
GROUP BY t4.aemp_id,
  t5.aemp_usnm,
  t5.aemp_name,t5.aemp_usnm,
  t5.aemp_mob1,
  t7.slgp_name,
  t1.cont_id
            ");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->site_code),
            );
        }
    }

    public
    function last_five_history(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT t1.`ordm_ornm`,CONCAT(t1.aemp_usnm, '-',t1.aemp_name)AS sr_info,t1.ordm_date AS Date ,t1.`site_code`,t1.`site_name`,
round(SUM(t1.`ordd_oamt`), 2)AS ORDER_AMT,t3.lfcl_name AS stasus
FROM `order_summary`t1 JOIN tt_ordm t2 ON(t1.`ordm_ornm`=t2.ordm_ornm)
JOIN tm_lfcl t3 ON t2.lfcl_id=t3.id
WHERE t1.`site_id`=$request->site_id  
GROUP BY t1.`ordm_ornm`,t1.ordm_date,t1.aemp_usnm,t1.aemp_name,t1.`site_code`,t1.`site_name`
 ORDER BY t1.`ordm_date` DESC LIMIT 6;
            ");
            return Array(
                "receive_data" => $data1,
            );
        }
    }

    public
    function last_five_history_details(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT t1.`ordm_ornm`,t1.ordm_date AS Date ,t1.`site_code`,t1.`site_name`,
t1.`amim_code`,t1.`amim_name`,t1.`ordd_uprc`,t1.`ordd_qnty`,t2.ordd_opds,t2.ordd_spdo,t2.ordd_dfdo,round((t1.`ordd_oamt`), 2)AS ORDER_AMT 
FROM `order_summary`t1 JOIN tt_ordd t2 ON t1.`ordm_ornm`=t2.ordm_ornm
WHERE t1.`ordm_ornm`='$request->ordm_ornm'
GROUP BY t1.`ordm_ornm`,t1.ordm_date,t1.`site_code`,t1.`site_name`,
t1.`amim_code`,t1.`amim_name`,t1.`ordd_uprc`,t1.`ordd_qnty`,t1.`ordd_oamt`,t2.ordd_opds,t2.ordd_spdo,t2.ordd_dfdo;
            ");
            return Array(
                "receive_data" => $data1,
            );
        }
    }

    public
    function last_five_npro_history(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
  SELECT t4.slgp_name,CONCAT(t3.aemp_usnm, '-',t3.aemp_name)AS sr_info,t1.npro_date AS date ,
t2.`site_code`,t2.`site_name`,t1.npro_note,t1.npro_dtne AS visit_distance
FROM `tt_npro`t1 JOIN tm_site t2 ON t1.site_id=t2.id
JOIN tm_aemp t3 ON t1.aemp_id=t3.id
JOIN tm_slgp t4 ON t1.slgp_id=t4.id
WHERE t1.`site_id`=$request->site_id  
GROUP BY t4.slgp_name,t3.aemp_usnm,t3.aemp_name,t1.npro_date,
t2.`site_code`,t2.`site_name`,t1.npro_note,t1.npro_dtne
ORDER BY t1.`npro_date` DESC LIMIT 6;
            ");
            return Array(
                "receive_data" => $data1,
            );
        }
    }

    public
    function employeeOrderPrintMemo(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
//on t6.base_id = t7.id
            $order_master = DB::connection($db_conn)->select("SELECT
         t1.id,
         'Order' as title,
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.id        AS site_id,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_olnm AS outlet_name_bn,
         t3.site_olad AS outlet_address_bn,
         t3.site_mob1 AS outlet_mobile,
         t3.site_adrs AS outlet_address,
         t3.site_ownm AS outlet_owner,
         t3.site_vtrn AS customer_trn,
         t4.dlrm_code AS dealer_id,
         t4.dlrm_name AS dealer_name,
         t4.dlrm_mob1 AS dealer_mobile,
         t1.ordm_date AS order_date,
         t1.ordm_drdt AS delivery_date,
         0 as totla_amount,
         0 as totla_discount,
         t6.rout_name,
         t7.base_name,
         t8.zone_code,
         t9.acmp_titl AS invoice_title,t9.acmp_rttl AS return_title,t9.acmp_name AS company,t9.acmp_addr AS address,t9.acmp_nvat AS vat_no,t9.`acmp_nexc` AS excise_no
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
         INNER JOIN tm_rout t6 on t1.rout_id = t6.id
         INNER JOIN tm_base t7 ON t4.base_id = t7.id
         INNER JOIN tm_zone t8 on t7.zone_id = t8.id
         JOIN tm_acmp t9 ON (t1.acmp_id=t9.id)
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");

            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  t2.amim_olin as sku_print_name,
  round(t1.ordd_uprc,4) as rate,
  t1.ordd_qnty   as quantity,
  round((t1.ordd_opds+t1.ordd_spdo+t1.ordd_dfdo),4) as discont,
  round(t1.ordd_tvat,4) AS tvat,
  if(ordd_smpl=1,0,round(t1.ordd_uprc*t1.ordd_qnty,4)) as price,
  t1.ordd_duft AS product_factor,  
  round(t1.ordd_texc,4)AS gst
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
                $data3 = DB::connection($db_conn)->select("SELECT
  t5.aemp_usnm AS sr_id,
  t1.rtan_date AS order_date,
  t6.site_code AS outlet_id,
  t3.amim_code AS product_code,
  t3.amim_name AS product_name,
  t2.rtdd_duft AS product_factor,
  t2.rtdd_qnty AS quantity,
  t2.rtdd_uprc  AS rate,
  t2.rtdd_uprc *t2.rtdd_qnty as reasion,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS column_id,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS token
FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_dprt AS t4 ON t2.dprt_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
  INNER JOIN tm_site AS t6 ON t1.site_id = t6.id
  INNER JOIN tl_srdi t7 ON(t1.dlrm_id=t7.dlrm_id)
WHERE t1.site_id = $site_id AND t1.rtan_date = '$order_date' AND t7.aemp_id=$aemp_id");

                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->returnLine = $data3;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 4, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 4, '.', '');

            }

            return Array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

    public
    function UaeOrderPrintMemo(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
//on t6.base_id = t7.id
            $order_master = DB::connection($db_conn)->select("SELECT
         t1.id,
         'Tax Invoice' as title,
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.id        AS site_id,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_vtrn AS site_trn,
         t3.site_olnm AS outlet_name_bn,
         t3.site_olad AS outlet_address_bn,
         t3.site_mob1 AS outlet_mobile,
         t3.site_adrs AS outlet_address,
         t3.site_ownm AS outlet_owner,
         t4.dlrm_code AS dealer_id,
         t4.dlrm_name AS dealer_name,
         t4.dlrm_mob1 AS dealer_mobile,
         t1.ordm_date AS order_date,
         t1.ordm_drdt AS delivery_date,
         0 as totla_amount,
         0 as totla_discount,
         t6.rout_name,
         t7.base_name,
         t8.zone_code,
         IFNULL(t10.IBS_INVOICE,'-')       AS vat_invoice,
         IFNULL(t10.TRIP_NO,'-')           AS TRIP_NO, 
         t9.acmp_titl AS invoice_title,t9.acmp_rttl AS return_title,t9.acmp_name AS company,t9.acmp_addr AS address,t9.acmp_nvat AS vat_no,t9.`acmp_nexc` AS excise_no
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
         INNER JOIN tm_rout t6 on t1.rout_id = t6.id
         INNER JOIN tm_base t7 ON t4.base_id = t7.id
         INNER JOIN tm_zone t8 on t7.zone_id = t8.id
         JOIN tm_acmp t9 ON (t1.acmp_id=t9.id)
         LEFT JOIN dm_trip_master t10 ON t1.ordm_ornm=t10.ORDM_ORNM
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");

            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  t2.amim_olin as sku_print_name,
  round(t1.ordd_uprc,4) as rate,
  t1.ordd_qnty   as quantity,
  round((t1.ordd_opds+t1.ordd_spdo+t1.ordd_dfdo),4) as discont,
  round(t1.ordd_tvat,4) AS tvat,
  round(t1.ordd_oamt,4) as price,
  t1.ordd_duft AS product_factor,  
  round(t1.ordd_texc,4)AS gst
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
                $data3 = DB::connection($db_conn)->select("SELECT
  t5.aemp_usnm AS sr_id,
  t1.rtan_date AS order_date,
  t6.site_code AS outlet_id,
  t3.amim_code AS product_code,
  t3.amim_name AS product_name,
  t2.rtdd_duft AS product_factor,
  t2.rtdd_qnty AS quantity,
  t2.rtdd_uprc  AS rate,
  t2.rtdd_uprc *t2.rtdd_qnty as reasion,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS column_id,
  concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS token
FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tm_dprt AS t4 ON t2.dprt_id = t4.id
  INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
  INNER JOIN tm_site AS t6 ON t1.site_id = t6.id
  INNER JOIN tl_srdi t7 ON(t1.dlrm_id=t7.dlrm_id)
WHERE t1.site_id = $site_id AND t1.rtan_date = '$order_date' AND t7.aemp_id=$aemp_id");

                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->returnLine = $data3;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 4, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 4, '.', '');

            }

            return Array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

    public
    function UaeOrderInvoicePrint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $order_id = $request->order_id;

            $order_master = DB::connection($db_conn)->select("SELECT
  t1.ordm_ornm                           AS Order_ID,
  DATE_FORMAT(t1.ordm_date, '%b %d, %Y') AS order_date,
  DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y') AS delivery_date,
  t3.site_name                           AS Outlet_Name,
  t3.site_name                           AS site_name,
  t3.site_code                           AS site_code,
  t3.site_mob1                           AS site_mobile,
  '0'                                    AS discount,
  t1.site_id                             AS site_id,
  t3.outl_id                             AS outlet_id,
  t10.optp_name                          AS Payment_Type,
  ''                                     AS Region_Name,
  ''                                     AS Zone_Name,
  t3.site_adrs                           AS site_address,
  t3.site_adrs                           AS outlet_address,
  t4.aemp_usnm                           AS sr_id,
  t4.aemp_name                           AS sr_name,
  t4.aemp_mob1                           AS sr_mobile,
  t5.id                                  AS ou_id,
  t5.acmp_name                           AS ou_name,
  t5.acmp_nexc                           AS exciese_number,
  t5.acmp_nvat                           AS vat_number,
  t5.acmp_addr                           AS ou_address,
  t3.site_vtrn                           AS site_trn,
  'Order Invoice'                        AS invoice_title,
 round(t1.ordm_amnt,4)                   AS invoice_amount,
  t5.acmp_vats                           AS vat_status,
  t5.acmp_crnc                           AS currency,
  t5.acmp_dgit                           AS round_digit,
  t5.acmp_rond                           AS round,
  t5.acmp_note                           AS note
FROM tt_ordm AS t1
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
  INNER JOIN tm_acmp AS t5 ON t1.acmp_id = t5.id
  left JOIN tl_stcm AS t9 ON t1.acmp_id = t9.acmp_id AND t1.site_id = t9.site_id AND t1.slgp_id=t9.slgp_id
  left JOIN tm_optp AS t10 ON t9.optp_id = t10.id
WHERE t1.ordm_ornm = '$order_id'");

            $data2 = DB::connection($db_conn)->select("SELECT
  t2.amim_id                                                        AS Product_id,
  t3.amim_name                                                      AS Product_Name,
  t3.amim_olin                                                      AS sku_print_name,
  floor(t2.ordd_qnty / t2.ordd_duft)                                AS ctn,
  t2.ordd_qnty % t2.ordd_duft                                       AS pcs,
  t2.ordd_qnty                                                      AS qty,
  round((t2.ordd_uprc*t2.ordd_qnty),3)                              as Total_Item_Price,
  t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
  (t2.ordd_opds + t2.ordd_spdo + t2.ordd_dfdo) * 100 / t2.ordd_oamt AS ratio,
  t2.ordd_opds + t2.ordd_spdo + t2.ordd_dfdo                        AS Discount,
  t2.ordd_duft                                                      AS factor,
  t2.prom_id                                                        AS promo_ref,
  round(t2.ordd_texc,3)                                             AS gst,
  t2.ordd_ovat                                                      AS vat,
  round(t2.ordd_tvat,3)                                             AS tvat
  
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.ordm_ornm = '$order_id'");

            $order_master[0]->orderLine = $data2;

            /* return Array(
               "data" => $order_master
             );*/
            //  return response()->json($order_master);
            return response()->json(array('data' => $order_master));
        }
    }

    public
    function UaeInvoicePrint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $order_id = $request->order_id;

            if ($request->country_id == 14) { //malaysia

                $order_master = DB::connection($db_conn)->select("SELECT
                            t1.ordm_ornm                                          AS Order_ID,
                            DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
                            DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
                            IFNULL(t10.mother_site_name,t2.site_name)             AS Outlet_Name,
                            t2.site_name                                          AS site_name,
                            t2.site_code                                          AS site_code,
                            t2.id                                                 AS site_id,
                            t2.site_mob1                                          AS site_mobile,
                            t6.DELV_AMNT                                          AS total_price,
                            '0'                                                   AS discount,
                            t1.site_id                                            AS customer_number,
                            IFNULL(t10.mother_site_code,t2.outl_id)               AS outlet_id,
                            t9.optp_name                                          AS Payment_Type,
                            ''                                                    AS Region_Name,
                            ''                                                    AS Zone_Name,
                            t2.site_adrs                                          AS site_address,
                          IFNULL(t10.billing_address,t2.site_adrs)                AS outlet_address,
                            t3.aemp_usnm                                          AS sr_id,
                            t3.aemp_name                                          AS sr_name,
                            t3.aemp_mob1                                          AS sr_mobile,
                            t4.id                                                 AS ou_id,
                            t4.acmp_name                                          AS ou_name,
                            t4.acmp_note                                          AS acmp_note,
                            t4.acmp_nexc                                          AS exciese_number,
                            t4.acmp_nvat                                          AS vat_number,
                            ''                                                    AS year,
                            ''                                                    AS serial_number,
                            IFNULL(t6.IBS_INVOICE,'-')                            AS tax_invoice,
                            t4.acmp_addr                                          AS ou_address,
                            t2.site_vtrn                                          AS site_trn,
                            t4.acmp_titl                                          AS invoice_title,
                            t4.acmp_vats                                          AS vat_status,
                            t6.DELV_AMNT                                          AS invoice_amount,
                            t4.acmp_creg                                          AS currency,
                            t4.acmp_dgit                                          AS round_digit,
                            t4.acmp_rond                                          AS round,
                            t4.acmp_note                                          AS note
                        FROM tt_ordm AS t1
                            INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                            INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                            INNER JOIN tl_pcmp AS t4
                            LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
                            INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
                            left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t1.slgp_id=t8.slgp_id
                            left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
                            left JOIN tl_site_party_mapping AS t10 ON t10.site_code = t6.SITE_CODE
                        WHERE t1.ordm_ornm ='$order_id' AND t1.lfcl_id in(11,39)");

                $data2 = DB::connection($db_conn)->select("SELECT
                                    t2.amim_id                                                        AS Product_id,
                                    t3.amim_name                                                      AS Product_Name,
                                    t3.amim_olin                                                      AS sku_print_name,
                                    floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                                    t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                                    t2.ordd_dqty                                                      AS qty,
                                   if(t2.ordd_oamt=0,0,round((t4.ORDD_UPRC*t4.DELV_QNTY),3))         AS Total_Item_Price,
                                    t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
                                    (t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd) * 100 / t2.ordd_amnt AS ratio,
                                    t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                                    t2.ordd_duft                                                      AS factor,
                                    t2.prom_id                                                        AS promo_ref,
                                    t2.ordd_excs                                                      AS gst,
                                    t2.ordd_ovat                                                      AS vat,
                                    t4.DISCOUNT                                                       AS total_discount,
                                    round((((t4.ORDD_UPRC*t4.DELV_QNTY)-t4.DISCOUNT+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                                    round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                                    round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
                                    round((((t4.ORDD_UPRC*t4.DELV_QNTY)-t4.DISCOUNT+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                                    round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS net_amount,
                                    t3.attr4                                                          AS print_ou_id
                                FROM tt_ordm AS t1
                                    INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                                    INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                    INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.amim_id=t4.AMIM_ID
                                WHERE t1.ordm_ornm = '$order_id' and t4.OID=t2.id AND t2.ordd_dqty != 0 AND t1.lfcl_id in(11,39)");

                if (count($order_master) > 0) {
                    // $order_master[0]->orderLine = $data2;
                    return response()->json(array('company_list' => $order_master, 'order_line' => $data2));
                } else {
                    return response()->json(array('company_list' => [], 'order_line' => []));
                    // return response()->json(array('data' => []));
                }
            } else {

                $order_master = DB::connection($db_conn)->select("SELECT
                                t1.ordm_ornm                                          AS Order_ID,
                                DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
                                DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
                                t2.site_name                                          AS Outlet_Name,
                                t2.site_name                                          AS site_name,
                                t2.site_code                                          AS site_code,
                                t2.id                                                 AS site_id,
                                t2.site_mob1                                          AS site_mobile,
                                t6.DELV_AMNT                                          AS total_price,
                                '0'                                                   AS discount,
                                t1.site_id                                            AS customer_number,
                                t2.outl_id                                            AS outlet_id,
                                t9.optp_name                                          AS Payment_Type,
                                ''                                                    AS Region_Name,
                                ''                                                    AS Zone_Name,
                                t2.site_adrs                                          AS site_address,
                                t2.site_adrs                                          AS outlet_address,
                                t3.aemp_usnm                                          AS sr_id,
                                t3.aemp_name                                          AS sr_name,
                                t3.aemp_mob1                                          AS sr_mobile,
                                t4.id                                                 AS ou_id,
                                t4.acmp_name                                          AS ou_name,
                                t4.acmp_note                                          AS acmp_note,
                                t4.acmp_nexc                                          AS exciese_number,
                                t4.acmp_nvat                                          AS vat_number,
                                ''                                                    AS year,
                                ''                                                    AS serial_number,
                                IFNULL(t6.IBS_INVOICE,'-')                            AS tax_invoice,
                                t4.acmp_addr                                          AS ou_address,
                                t2.site_vtrn                                          AS site_trn,
                                t4.acmp_titl                                          AS invoice_title,
                                t4.acmp_vats                                          AS vat_status,
                                t6.DELV_AMNT                                          AS invoice_amount,
                                t4.acmp_creg                                          AS currency,
                                t4.acmp_dgit                                          AS round_digit,
                                t4.acmp_rond                                          AS round,
                                t4.acmp_note                                          AS note
                                FROM tt_ordm AS t1
                                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                                INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                                INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
                                LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
                                INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
                                left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t1.slgp_id=t8.slgp_id
                                left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
                                WHERE t1.ordm_ornm ='$order_id' AND t1.lfcl_id in(11,39)");

                $data2 = DB::connection($db_conn)->select("SELECT
                        t2.amim_id                                                        AS Product_id,
                        t3.amim_name                                                      AS Product_Name,
                        t3.amim_olin                                                      AS sku_print_name,
                        floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                        t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                        t2.ordd_dqty                                                      AS qty,
                        if(t2.ordd_oamt=0,0,round((t4.ORDD_UPRC*t4.DELV_QNTY),3))         AS Total_Item_Price,
                        t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
                        (t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd) * 100 / t2.ordd_amnt AS ratio,
                        t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                        t2.ordd_duft                                                      AS factor,
                        t2.prom_id                                                        AS promo_ref,
                        t2.ordd_excs                                                      AS gst,
                        t2.ordd_ovat                                                      AS vat,
                        t4.DISCOUNT                                                       AS total_discount,
                        round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                        round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                        round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
                        round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                        round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                        FROM tt_ordm AS t1
                        INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                        INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.amim_id=t4.AMIM_ID
                        WHERE t1.ordm_ornm = '$order_id' and t4.OID=t2.id AND t2.ordd_dqty != 0 AND t1.lfcl_id in(11,39)");
                        return response()->json(array('company_list' => $order_master, 'order_line' => $data2));
                //return $order_master;
                if (count($order_master) > 0) {
                    $order_master[0]->orderLine = $data2;
                    return response()->json(array('company_list' => $order_master, 'order_line' => $data2));
                    // return response()->json(array('data' => $order_master));
                } else {
                    return response()->json(array('company_list' => [], 'order_line' => []));
                    //  return response()->json(array('data' => []));
                }
            }
            /*if (count($order_master) > 0) {
                // $order_master[0]->orderLine = $data2;
                return response()->json(array('company_list' => $order_master, 'order_line' => $data2));
            } else {
                return response()->json(array('company_list' => [], 'order_line' => []));
                // return response()->json(array('data' => []));
            }*/

        }
    }

    public
    function UaeGrvInvoicePrint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $order_id = $request->order_id;
            if ($request->country_id == 14) { //malaysia
                $order_master = DB::connection($db_conn)->select("SELECT
                                t1.rtan_rtnm                                          AS Order_ID,
                                DATE_FORMAT(t1.rtan_date, '%b %d, %Y')                AS order_date,
                                DATE_FORMAT(t1.rtan_drdt, '%b %d, %Y')                AS delivery_date,
                                t2.site_name                                          AS Outlet_Name,
                                t2.site_name                                          AS site_name,
                                t2.site_code                                          AS site_code,
                                t2.id                                                 AS site_id,
                                t2.site_mob1                                          AS site_mobile,
                                0                                                     AS total_price,
                                '0'                                                   AS discount,
                                t1.site_id                                            AS customer_number,
                                t2.outl_id                                            AS outlet_id,
                                ''                                                    AS Payment_Type,
                                ''                                                    AS Region_Name,
                                ''                                                    AS Zone_Name,
                                t2.site_adrs                                          AS site_address,
                                t2.site_adrs                                          AS outlet_address,
                                t3.aemp_usnm                                          AS sr_id,
                                t3.aemp_name                                          AS sr_name,
                                t3.aemp_mob1                                          AS sr_mobile,
                                t4.id                                                 AS ou_id,
                                t4.acmp_name                                          AS ou_name,
                                t4.acmp_note                                          AS acmp_note,
                                t4.acmp_nexc                                          AS exciese_number,
                                t4.acmp_nvat                                          AS vat_number,
                                ''                                                    AS year,
                                ''                                                    AS serial_number,
                                '-'                                                   AS tax_invoice,
                                t4.acmp_addr                                          AS ou_address,
                                t2.site_vtrn                                          AS site_trn,
                                t4.acmp_rttl                                          AS invoice_title,
                                t4.acmp_vats                                          AS vat_status,
                                0                                                      AS invoice_amount,
                                t4.acmp_creg                                          AS currency,
                                t4.acmp_dgit                                          AS round_digit,
                                t4.acmp_rond                                          AS round,
                                t4.acmp_note                                          AS note
                                FROM tt_rtan AS t1
                                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                                INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                                INNER JOIN tl_pcmp AS t4 
                                left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t1.slgp_id=t8.slgp_id
                                WHERE t1.rtan_rtnm ='$order_id' AND t1.lfcl_id in(11,39);");
                $data2 = DB::connection($db_conn)->select("SELECT
                        t2.amim_id                                                        AS Product_id,
                        t3.amim_name                                                      AS Product_Name,
                        t3.amim_olin                                                      AS sku_print_name,
                        floor(t2.rtdd_dqty / t2.rtdd_duft)                                AS ctn,
                        t2.rtdd_dqty % t2.rtdd_duft                                       AS pcs,
                        t2.rtdd_dqty                                                      AS qty,
                        round((t2.rtdd_uprc*t2.rtdd_dqty),3)                              AS Total_Item_Price,
                        t2.rtdd_uprc * t2.rtdd_duft                                       AS Rate,
                        0                                                                 AS ratio,
                        t2.rtdd_tdis                                                      AS Discount,
                        t2.rtdd_duft                                                      AS factor,
                        0                                                                 AS promo_ref,
                        t2.rtdd_texc                                                      AS gst,
                        t2.rtdd_tvat                                                      AS vat,
                        t2.rtdd_tdis                                                      AS total_discount,
                        round((((t2.rtdd_uprc*t2.rtdd_dqty)-t2.rtdd_tdis+(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100))*t2.rtdd_ovat)/100,3)AS total_vat,
                        round(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100,3)           AS total_gst,
                        round((t2.rtdd_uprc*t2.rtdd_dqty),3)-t2.rtdd_tdis+
                        round((((t2.rtdd_uprc*t2.rtdd_dqty)-t2.rtdd_tdis+(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100))*t2.rtdd_ovat)/100,3)+
                        round(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100,3)AS net_amount,
                        t3.attr4                                                          AS print_ou_id
                        FROM tt_rtan AS t1
                        INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
                        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                        WHERE t1.rtan_rtnm = '$order_id' AND t2.rtdd_dqty != 0 AND t1.lfcl_id in(11,39);");

            } else {

                $order_master = DB::connection($db_conn)->select("SELECT
                                t1.rtan_rtnm                                          AS Order_ID,
                                DATE_FORMAT(t1.rtan_date, '%b %d, %Y')                AS order_date,
                                DATE_FORMAT(t1.rtan_drdt, '%b %d, %Y')                AS delivery_date,
                                t2.site_name                                          AS Outlet_Name,
                                t2.site_name                                          AS site_name,
                                t2.site_code                                          AS site_code,
                                t2.id                                                 AS site_id,
                                t2.site_mob1                                          AS site_mobile,
                                0                                                     AS total_price,
                                '0'                                                   AS discount,
                                t1.site_id                                            AS customer_number,
                                t2.outl_id                                            AS outlet_id,
                                ''                                                    AS Payment_Type,
                                ''                                                    AS Region_Name,
                                ''                                                    AS Zone_Name,
                                t2.site_adrs                                          AS site_address,
                                t2.site_adrs                                          AS outlet_address,
                                t3.aemp_usnm                                          AS sr_id,
                                t3.aemp_name                                          AS sr_name,
                                t3.aemp_mob1                                          AS sr_mobile,
                                t4.id                                                 AS ou_id,
                                t4.acmp_name                                          AS ou_name,
                                t4.acmp_note                                          AS acmp_note,
                                t4.acmp_nexc                                          AS exciese_number,
                                t4.acmp_nvat                                          AS vat_number,
                                ''                                                    AS year,
                                ''                                                    AS serial_number,
                                t1.rtan_rtnm                                          AS tax_invoice,
                                t4.acmp_addr                                          AS ou_address,
                                t2.site_vtrn                                          AS site_trn,
                                t4.acmp_rttl                                          AS invoice_title,
                                t4.acmp_vats                                          AS vat_status,
                                0                                                      AS invoice_amount,
                                t4.acmp_creg                                          AS currency,
                                t4.acmp_dgit                                          AS round_digit,
                                t4.acmp_rond                                          AS round,
                                t4.acmp_note                                          AS note
                                FROM tt_rtan AS t1
                                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                                INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                                INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
                                left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t1.slgp_id=t8.slgp_id
                                WHERE t1.rtan_rtnm ='$order_id' AND t1.lfcl_id in(11,39);");
                $data2 = DB::connection($db_conn)->select("SELECT
                        t2.amim_id                                                        AS Product_id,
                        t3.amim_name                                                      AS Product_Name,
                        t3.amim_olin                                                      AS sku_print_name,
                        floor(t2.rtdd_dqty / t2.rtdd_duft)                                AS ctn,
                        t2.rtdd_dqty % t2.rtdd_duft                                       AS pcs,
                        t2.rtdd_dqty                                                      AS qty,
                        round((t2.rtdd_uprc*t2.rtdd_dqty),3)                              AS Total_Item_Price,
                        t2.rtdd_uprc * t2.rtdd_duft                                       AS Rate,
                        0                                                                 AS ratio,
                        t2.rtdd_tdis                                                      AS Discount,
                        t2.rtdd_duft                                                      AS factor,
                        0                                                                 AS promo_ref,
                        t2.rtdd_texc                                                      AS gst,
                        t2.rtdd_tvat                                                      AS vat,
                        t2.rtdd_tdis                                                      AS total_discount,
                        round((((t2.rtdd_uprc*t2.rtdd_dqty)-t2.rtdd_tdis+(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100))*t2.rtdd_ovat)/100,3)AS total_vat,
                        round(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100,3)           AS total_gst,
                        round((t2.rtdd_uprc*t2.rtdd_dqty),3)-t2.rtdd_tdis+
                        round((((t2.rtdd_uprc*t2.rtdd_dqty)-t2.rtdd_tdis+(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100))*t2.rtdd_ovat)/100,3)+
                        round(((t2.rtdd_uprc*t2.rtdd_dqty)*t2.rtdd_excs)/100,3)AS net_amount
                        FROM tt_rtan AS t1
                        INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
                        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                        WHERE t1.rtan_rtnm = '$order_id' AND t2.rtdd_dqty != 0 AND t1.lfcl_id in(11,39);");
            }
            if (count($order_master) > 0) {

                // $order_master[0]->orderLine = $data2;

                return response()->json(array('company_list' => $order_master, 'order_line' => $data2));
            } else {
                return response()->json(array('company_list' => [], 'order_line' => []));
                // return response()->json(array('data' => []));
            }
        }
    }

    public
    function UaeTaxInvoicePrint(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $order_id = $request->order_id;

            $order_master = DB::connection($db_conn)->select("SELECT
  t1.ordm_ornm                                          AS Order_ID,
  DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
  DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
  t2.site_name                                          AS Outlet_Name,
  t2.site_name                                          AS site_name,
  t2.site_code                                          AS site_code,
  t2.id                                                 AS site_id,
  t2.site_mob1                                          AS site_mobile,
  t6.DELV_AMNT                                          AS total_price,
  '0'                                                   AS discount,
  t1.site_id                                            AS customer_number,
  t2.outl_id                                            AS outlet_id,
  t9.optp_name                                          AS Payment_Type,
  ''                                                    AS Region_Name,
  ''                                                    AS Zone_Name,
  t2.site_adrs                                          AS site_address,
  t2.site_adrs                                          AS outlet_address,
  t3.aemp_usnm                                          AS sr_id,
  t3.aemp_name                                          AS sr_name,
  t3.aemp_mob1                                          AS sr_mobile,
  t4.id                                                 AS ou_id,
  t4.acmp_name                                          AS ou_name,
  t4.acmp_note                                          AS acmp_note,
  t4.acmp_nexc                                          AS exciese_number,
  t4.acmp_nvat                                          AS vat_number,
  ''                                                    AS year,
  ''                                                    AS serial_number,
  IFNULL(t6.IBS_INVOICE,'-')                            AS tax_invoice,
  t4.acmp_addr                                          AS ou_address,
  t2.site_vtrn                                          AS site_trn,
  t4.acmp_titl                                          AS invoice_title,
  t4.acmp_vats                                          AS vat_status,
  t6.DELV_AMNT                                          AS invoice_amount,
  t4.acmp_creg                                          AS currency,
  t4.acmp_dgit                                          AS round_digit,
  t4.acmp_rond                                          AS round,
  t4.acmp_note                                          AS note
FROM tt_ordm AS t1
  INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
  INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
  left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t1.slgp_id=t8.slgp_id
  left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
WHERE t1.ordm_ornm ='$order_id'");

            $data2 = DB::connection($db_conn)->select("SELECT
  t2.amim_id                                                        AS Product_id,
  t3.amim_name                                                      AS Product_Name,
  t3.amim_olin                                                      AS sku_print_name,
  floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
  t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
  t2.ordd_dqty                                                      AS qty,
  round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
  t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
  (t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd) * 100 / t2.ordd_amnt AS ratio,
  t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
  t2.ordd_duft                                                      AS factor,
  t2.prom_id                                                        AS promo_ref,
  t2.ordd_excs                                                      AS gst,
  t2.ordd_ovat                                                      AS vat,
  t4.DISCOUNT                                                       AS total_discount,
  round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
  round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
  round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
  round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
  round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.amim_id=t4.AMIM_ID
WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0;");

            $order_master[0]->orderLine = $data2;

            /* return Array(
                 "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
             );*/
            return response()->json(array('data' => $order_master));

        }
    }

    public
    function employeeOrderPrintMemo_new(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $order_master = DB::connection($db_conn)->select("SELECT
                        t1.id,
                        'Order' as title,   
                        t1.ordm_ornm AS order_id,
                        t1.ordm_date AS order_date,
                        t11.amim_id as product_code,
                        t12.amim_name as product_name,
                        t12.amim_olin as sku_print_name,
                        round(t11.ordd_uprc,2) as rate,
                        t11.ordd_qnty   as quantity,
                        round((t11.ordd_opds+t11.ordd_spdo+t11.ordd_dfdo),2) as discont,
                        round(t11.ordd_oamt,2) as price,
                        t12.amim_duft AS product_factor,  
                        round(((t11.ordd_oamt * `ordd_excs`) / 100),2)AS gst 
                        FROM tt_ordm AS t1
                        INNER JOIN tt_ordd as t11 on t1.id = t11.ordm_id
                        INNER JOIN tm_amim as t12 on t11.amim_id=t12.id
                        WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");
            $num_of_order = DB::connection($db_conn)->select(" SELECT
                            t1.id,'Order' as title,t5.aemp_usnm AS sr_id,t5.aemp_name AS sr_name,
                            t5.aemp_mob1 AS sr_mobile,
                            t1.ordm_ornm AS order_id,
                            t2.slgp_name AS group_name,
                            t3.id        AS site_id,
                            t3.site_code AS outlet_id,
                            t3.site_name AS outlet_name,
                            t3.site_olnm AS outlet_name_bn,
                            t3.site_olad AS outlet_address_bn,
                            t3.site_mob1 AS outlet_mobile,
                            t3.site_adrs AS outlet_address,
                            t3.site_ownm AS outlet_owner,
                            t4.dlrm_code AS dealer_id,
                            t4.dlrm_name AS dealer_name,
                            t4.dlrm_mob1 AS dealer_mobile,
                            t1.ordm_date AS order_date,
                            t1.ordm_drdt AS delivery_date,
                            0 as total_amount,
                            0 as total_discount,
                            t6.rout_name,
                            t7.base_name,
                            t8.zone_code
                            FROM tt_ordm AS t1
                            INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
                            INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
                            INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
                            INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
                            INNER JOIN tm_rout t6 on t1.rout_id = t6.id
                            INNER JOIN tm_base t7 ON t4.base_id = t7.id
                            INNER JOIN tm_zone t8 on t7.zone_id = t8.id
                            WHERE t1.aemp_id = $aemp_id AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");
            $p = 0;
            //  $order_data;
            foreach ($num_of_order as $index => $data1) {
                $p = $index;
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
                $data_orddt = array();
                foreach ($order_master as $d) {
                    if ($data1->order_id == $d->order_id) {
                        $dt = (object)['product_code' => $d->product_code,
                            'product_name' => $d->product_name,
                            'sku_print_name' => $d->sku_print_name,
                            'rate' => $d->rate,
                            'quantity' => $d->quantity,
                            'discont' => $d->discont,
                            'price' => $d->price,
                            'product_factor' => $d->product_factor,
                            'gst' => $d->gst,
                        ];
                        array_push($data_orddt, $dt);


                    }

                }


                $data3 = DB::connection($db_conn)->select("SELECT
                            t5.aemp_usnm AS sr_id,
                            t1.rtan_date AS order_date,
                            t6.site_code AS outlet_id,
                            t3.amim_code AS product_code,
                            t3.amim_name AS product_name,
                            t3.amim_duft AS product_factor,
                            t2.rtdd_qnty AS quantity,
                            t2.rtdd_uprc  AS rate,
                            t2.rtdd_uprc *t2.rtdd_qnty as reasion,
                            concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS column_id,
                            concat(t1.rtan_date, t6.site_code, t3.amim_code, t3.amim_name, t2.rtdd_qnty, t2.rtdd_uprc) AS token
                            FROM tt_rtan AS t1
                            INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
                            INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                            INNER JOIN tm_dprt AS t4 ON t2.dprt_id = t4.id
                            INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
                            INNER JOIN tm_site AS t6 ON t1.site_id = t6.id
                            INNER JOIN tl_srdi t7 ON(t1.dlrm_id=t7.dlrm_id)
                            WHERE t1.site_id = $site_id AND t1.rtan_date = '$order_date' AND t7.aemp_id=$aemp_id");
                $num_of_order[$index]->orderLine = $data_orddt;
                $num_of_order[$index]->returnLine = $data3;
                $num_of_order[$index]->total_amount = number_format(array_sum(array_column($data_orddt, 'price')), 2, '.', '');
                $num_of_order[$index]->total_discount = number_format(array_sum(array_column($data_orddt, 'discont')), 2, '.', '');
            }
            //$p=$p+1;
            // array_splice($order_master, ($p));

            return Array(
                "receive_data" => array("data" => $num_of_order, "action" => $request->emp_id),
            );
        }
    }

    public
    function saveNote(Request $request)
    {
        $site_code = '';
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            if (isset($request->site_id)) {
                if (strlen($request->site_id) <= 10) {
                    $site_code = $request->site_id;
                }
            }
            DB::beginTransaction();
            try {
                $Note = Note::on($db_conn)->where(['note_tokn' => $request->note_id])->first();
                if ($Note == null) {
                    $title = substr($request->title, 0, 255);
                    $geo_addr = substr($request->geo_addr, 0, 255);
                    $note = new Note();
                    $note->setConnection($db_conn);
                    $note->aemp_id = $request->up_emp_id;
                    $note->note_tokn = $request->note_id;
                    $note->site_code = $site_code;
                    $note->ntpe_id = $request->note_type_id;
                    $note->note_dtim = $request->date_time;
                    $note->note_date = date("Y-m-d", strtotime($request->date_time));
                    $note->note_titl = "$title";
                    $note->note_body = "$request->note";
                    $note->geo_lat = $request->lat;
                    $note->geo_lon = $request->lon;
                    $note->geo_addr = isset($geo_addr) ? "$geo_addr" : '';
                    $note->note_type = $request->note_type;
                    $share_employee = json_decode($request->share_employee);
                    //  $share_group = json_decode($request->share_department);
                    $note->lfcl_id = 1;
                    $note->cont_id = $request->country_id;
                    $note->aemp_iusr = $request->up_emp_id;
                    $note->aemp_eusr = $request->up_emp_id;
                    $note->save();
                    $noteEmp = new NoteEmployee();
                    $noteEmp->setConnection($db_conn);
                    $noteEmp->aemp_id = $request->up_emp_id;
                    $noteEmp->enmp_date = date('Y-m-d');
                    $noteEmp->note_id = $note->id;
                    $noteEmp->aemp_iusr = $request->up_emp_id;
                    $noteEmp->aemp_eusr = $request->up_emp_id;
                    $noteEmp->lfcl_id = 1;
                    $noteEmp->cont_id = $request->country_id;
                    $noteEmp->enmp_scnt = 0;
                    $noteEmp->save();
                    if (isset($request->site_id)) {
                        $site = Site::on($db_conn)->where(['site_code' => $request->site_id])->first();
                        if ($site != null) {
                            $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $site->id, 'ssvh_date' => date("Y-m-d", strtotime($request->date_time)), 'aemp_id' => $request->up_emp_id])->first();
                            if ($siteVisit == null) {
                                $siteVisit = new SiteVisited();
                                $siteVisit->setConnection($db_conn);
                                $siteVisit->ssvh_date = date("Y-m-d", strtotime($request->date_time));
                                $siteVisit->aemp_id = $request->up_emp_id;
                                $siteVisit->site_id = $site->id;
                                $siteVisit->ssvh_ispd = 2;
                                $siteVisit->cont_id = $request->country_id;
                                $siteVisit->lfcl_id = 1;
                                $siteVisit->aemp_iusr = $request->up_emp_id;
                                $siteVisit->aemp_eusr = $request->up_emp_id;
                                $siteVisit->save();
                            }
                        }
                    }
                    $imageLines = json_decode($request->line_data);
                    foreach ($imageLines as $imageLine) {
                        $noteImage = new NoteImage();
                        $noteImage->setConnection($db_conn);
                        $noteImage->note_id = $note->id;
                        $noteImage->nimg_imag = $imageLine->image_name;
                        $noteImage->aemp_iusr = $request->up_emp_id;
                        $noteImage->aemp_eusr = $request->up_emp_id;
                        $noteImage->lfcl_id = 1;
                        $noteImage->cont_id = $request->country_id;
                        $noteImage->save();
                    }

                    foreach ($share_employee as $share_employee1) {
                        $insert[] = [
                            'attr3' => $share_employee1->aemp_id,
                            'note_id' => $note->id,
                            'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                            'enmp_scnt' => 1,
                            'cont_id' => $request->country_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => $request->up_emp_id,
                            'aemp_eusr' => $request->up_emp_id,
                        ];
                    }
                    if (!empty($insert)) {
                        DB::connection($db_conn)->table('tl_enmp')->insertOrIgnore($insert);
                    }

                    DB::commit();
                    //  return $request;
                    return response()->json(array('column_id' => $request->id), 200);
                    // return response()->json(array('id' => $request->id), 200);
                } else {
                    $msg = 'duplicate';
                    return response()->json(array('column_id' => $request->id, 'message' => $msg), 200);
                }
            } catch (\Exception $e) {
                // return response()->json(array('ex' => $e), 200);
                DB::rollback();
                return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]), 200);
            }
            return response()->json(array('column_id' => $request->id), 200);
        }

    }

    public
    function saveNote_sp(Request $request)
    {
        $site_code = '';
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            if (isset($request->site_id)) {
                if (strlen($request->site_id) <= 10) {
                    $site_code = $request->site_id;
                }
            }
            DB::beginTransaction();
            try {
                $Note = Note::on($db_conn)->where(['note_tokn' => $request->note_id])->first();
                if ($Note == null) {

                    $title = substr($request->title, 0, 255);
                    $geo_addr = substr($request->geo_addr, 0, 255);
                    $note = new Note();
                    $note->setConnection($db_conn);
                    $note->aemp_id = $request->up_emp_id;
                    $note->note_tokn = $request->note_id;
                    $note->site_code = $site_code;
                    $note->ntpe_id = $request->note_type_id;
                    $note->note_dtim = $request->date_time;
                    $note->note_date = date("Y-m-d", strtotime($request->date_time));
                    $note->note_titl = "$title";
                    $note->note_body = "$request->note";
                    $note->geo_lat = $request->lat;
                    $note->geo_lon = $request->lon;
                    $note->geo_addr = isset($geo_addr) ? "$geo_addr" : '';
                    $note->note_type = $request->note_type;
                    $share_employee = json_decode($request->share_employee);
                    //  $share_group = json_decode($request->share_department);
                    $note->lfcl_id = 1;
                    $note->cont_id = $request->country_id;
                    $note->aemp_iusr = $request->up_emp_id;
                    $note->aemp_eusr = $request->up_emp_id;
                    $note->save();
                    $noteEmp = new NoteEmployee();
                    $noteEmp->setConnection($db_conn);
                    $noteEmp->aemp_id = $request->up_emp_id;
                    $noteEmp->enmp_date = date('Y-m-d');
                    $noteEmp->note_id = $note->id;
                    $noteEmp->aemp_iusr = $request->up_emp_id;
                    $noteEmp->aemp_eusr = $request->up_emp_id;
                    $noteEmp->lfcl_id = 1;
                    $noteEmp->cont_id = $request->country_id;
                    $noteEmp->enmp_scnt = 0;
                    $noteEmp->save();
                    if (isset($request->site_id)) {
                        $site = Site::on($db_conn)->where(['site_code' => $request->site_id])->first();
                        if ($site != null) {
                            $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $site->id, 'ssvh_date' => date("Y-m-d", strtotime($request->date_time)), 'aemp_id' => $request->up_emp_id])->first();
                            if ($siteVisit == null) {
                                $siteVisit = new SiteVisited();
                                $siteVisit->setConnection($db_conn);
                                $siteVisit->ssvh_date = date("Y-m-d", strtotime($request->date_time));
                                $siteVisit->aemp_id = $request->up_emp_id;
                                $siteVisit->site_id = $site->id;
                                $siteVisit->ssvh_ispd = 2;
                                $siteVisit->cont_id = $request->country_id;
                                $siteVisit->lfcl_id = 1;
                                $siteVisit->aemp_iusr = $request->up_emp_id;
                                $siteVisit->aemp_eusr = $request->up_emp_id;
                                $siteVisit->save();
                            }
                        }
                    }
                    $imageLines = json_decode($request->line_data);
                    foreach ($imageLines as $imageLine) {
                        $noteImage = new NoteImage();
                        $noteImage->setConnection($db_conn);
                        $noteImage->note_id = $note->id;
                        $noteImage->nimg_imag = $imageLine->image_name;
                        $noteImage->aemp_iusr = $request->up_emp_id;
                        $noteImage->aemp_eusr = $request->up_emp_id;
                        $noteImage->lfcl_id = 1;
                        $noteImage->cont_id = $request->country_id;
                        $noteImage->save();
                    }
                    $title = "New Task Notification";
                    $body = $request->note;

                    if ($share_employee) {
                        foreach ($share_employee as $share_employee1) {
                            $insert[] = [
                                'aemp_id' => $request->up_emp_id,
                                'asin_user' => $share_employee1,
                                'note_id' => $note->id,
                                'enmp_date' => date("Y-m-d", strtotime($request->date_time)),
                            ];

                            (new OrderModuleDataUAE)->give_notification($share_employee1, $title, $body, $request->country_id);
                        }
                        if (!empty($insert)) {
                            DB::connection($db_conn)->table('tl_nasn')->insertOrIgnore($insert);
                        }
                    } else {
                    }

                    DB::commit();
                    //  return $request;
                    return response()->json(array('column_id' => $request->id), 200);
                    // return response()->json(array('id' => $request->id), 200);

                } else {
                    $msg = 'duplicate';
                    return response()->json(array('column_id' => $request->id, 'message' => $msg), 200);
                }
            } catch
            (\Exception $e) {
                // return response()->json(array('ex' => $e), 200);
                DB::rollback();
                // return response()->json(array('column_id' => 0, 'message' => $e->errorInfo[2]), 200);
                //   return response()->json(array('column_id' => 0, 'message' => $e), 200);
                return $e;
            }
            // return response()->json(array('column_id' => $request->id), 200);
        }

    }

    public
    function savePendingNote(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $mgs = "";
            $cod = 0;
            try {

                $ret = DB::connection($db_conn)->table('tl_nasn')->where(['asin_user' => $request->asin_user,
                    'note_id' => $request->note_id, 'nasn_life' => 0])->update(['nasn_life' => 1,
                    'nasn_comn' => $request->nasn_comn, 'nasn_imge' => $request->nasn_imge]);

                if ($ret == 1) {
                    $cod = 1;
                    $mgs = "Task Done Successful ";
                } else {
                    $cod = 0;
                    $mgs = "Task Done Failed ";
                }
                DB::connection($db_conn)->commit();
                return array(
                    'success' => $cod,
                    'message' => $mgs,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
    function SubmitTripReceived(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $mgs = "";
            $cod = 0;

            try {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://coreapi.sihirfms.com/api/tripconfirm',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('trip_nummber' => $request->TRIP_NO,
                        'cont_code_trip' => $request->country_id,
                        'ApiKey_van' => 'API2345fsdvh3675gsvbxdgeg425435hdgfsfg33'),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $IBS_INVOICE = json_decode($response, true);

                if ($IBS_INVOICE['trip_number'] == 'Success') {
                    $ret = DB::connection($db_conn)->table('dm_trip')->where(['TRIP_NO' => $request->TRIP_NO,
                        'STATUS' => 'N', 'DM_ID' => $request->DM_ID])->update(['DM_ACTIVITY' => '20']);
                    if ($ret == 1) {
                        $cod = 1;
                        $mgs = "Trip Received Successful ";
                    } else {
                        $cod = 0;
                        $mgs = "Trip Received Failed ";
                    }
                } else {
                    $cod = 0;
                    $mgs = "Trip Received Failed ";
                }

                DB::connection($db_conn)->commit();
                return array(
                    'success' => $cod,
                    'message' => $mgs,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public
    function dmTripWiseSROutletInfo(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_code = $request->emp_code;


            $delivery_vs_collection_status = DB::connection($db_conn)->select("
                        SELECT sum(t3.ORDD_AMNT) AS total_delivery_amount, sum(t3.COLLECTION_AMNT) AS total_collection_amount 
                        FROM `dm_trip`t1 
                        INNER JOIN `dm_trip_detail` t2 ON t1.`TRIP_NO`=t2.TRIP_NO
                        INNER JOIN `dm_trip_master` AS t3 ON t2.`ORDM_ORNM`=t3.`ORDM_ORNM`
                        WHERE t1.`DM_ID` = '$aemp_code' AND t1.`STATUS`='N';
                        
            ");

            $pending_collection = DB::connection($db_conn)->select("
                        SELECT SITE_CODE,SITE_ID ,slgp_id, optp_id as p_type_id  from(
                            SELECT t3.SITE_CODE,t3.SITE_ID ,t3.slgp_id,t5.optp_id,sum(t3.DELV_AMNT) amnt, sum(t6.COLLECTION_AMNT) camnt
                            FROM 
                            dm_trip t1
                            JOIN dm_trip_master t3 ON(t3.SITE_ID= t3.SITE_ID )
                            JOIN tl_stcm t5 ON(t3.site_id=t5.site_id) AND t5.slgp_id=t3.slgp_id
                            left JOIN dm_collection t6 ON(t6.site_id = t3.site_id) and t6.STATUS=11 and t6.INVT_ID=5 AND t3.slgp_id=t6.slgp_id
                            WHERE t1.`DM_ID` = '$aemp_code' AND t1.`STATUS`='N' AND t3.DELIVERY_STATUS = 11 AND t3.COLLECTION_AMNT = 0 AND t5.optp_id = 1
                            and t3.DELV_AMNT>0 and t3.COLLECTION_AMNT=0 AND t1.`TRIP_NO`=t3.TRIP_NO
                            group by t3.SITE_CODE,t3.site_id ,t3.slgp_id) d
                            group by SITE_CODE,SITE_ID ,slgp_id
                            having sum(ifnull(d.amnt,0))>sum( ifnull(d.camnt,0))
                        LIMIT 1;
            ");


            /* $trip_m = DB::connection($db_conn)->select("
                     SELECT TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                     emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered ,optp_id AS p_type_id,deliveryStatus
                     from(
                         SELECT t1.`TRIP_NO`,t1.DM_ACTIVITY AS trip_status,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t2.AEMP_USNM,
                         t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t2.ORDM_ORNM) totalINv,if(t2.TRIP_STATUS='D',1,'0') Delin,t5.optp_id
                         ,t6.DELIVERY_STATUS as deliveryStatus
                         FROM `dm_trip`t1
                         JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
                         JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
                         JOIN dm_trip_master as t6 ON (t2.SITE_ID= t6.SITE_ID)
                         join tl_stcm t5 ON(t5.site_id=t3.id) AND t5.slgp_id = t6.slgp_id
                         JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
                         WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N'
                         GROUP by t1.`TRIP_NO`,t1.DM_ACTIVITY,t2.SITE_ID,t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,t3.geo_lon,t2.TRIP_STATUS,t5.optp_id,t6.DELIVERY_STATUS
                     ) f
                     group by TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                     emp_id,aemp_name,geo_lat,geo_lon,optp_id,deliveryStatus
         ");*/

            $trip_m = DB::connection($db_conn)->select("
                    SELECT TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                    emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered ,optp_id AS p_type_id,deliveryStatus
                    from(
                        SELECT t1.`TRIP_NO`,t1.DM_ACTIVITY AS trip_status,t6.SITE_ID,t6.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t6.AEMP_USNM,
                        t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t6.ORDM_ORNM) totalINv,if(t6.DELIVERY_STATUS='11',1,'0') Delin,t5.optp_id
                        ,t6.DELIVERY_STATUS as deliveryStatus
                        FROM `dm_trip`t1 
                         JOIN dm_trip_master as t6 ON (t1.`TRIP_NO`= t6.TRIP_NO)
                        JOIN tm_site t3 ON(t6.SITE_ID=t3.id)                      
                        join tl_stcm t5 ON(t5.site_id=t3.id) AND t5.slgp_id = t6.slgp_id 
                        JOIN tm_aemp t4 ON(t6.AEMP_USNM=t4.aemp_usnm) 
                        WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N'
                        GROUP by t1.`TRIP_NO`,t1.DM_ACTIVITY,t6.SITE_ID,t6.SITE_CODE,t6.AEMP_USNM,t4.id,t3.geo_lat,t3.geo_lon,t5.optp_id,t6.DELIVERY_STATUS
                    ) f
                    group by TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                    emp_id,aemp_name,geo_lat,geo_lon,optp_id,deliveryStatus
                    UNION ALL 
                   SELECT t2.`TRIP_NO`,t2.DM_ACTIVITY AS trip_status,t3.id AS SITE_ID,t3.site_code AS SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t8.aemp_usnm AS AEMP_USNM,
                        t8.id AS emp_id,t8.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t6.rtan_rtnm) totalINv,if(t6.lfcl_id='11',1,'0') Delin,t4.optp_id
                        ,t6.lfcl_id as deliveryStatus 
                    FROM `tl_dmst_rout`t1 
                    JOIN dm_trip t2 ON t1.`dm_code`=t2.DM_ID
                    JOIN tm_site t3 ON t1.`site_code`=t3.site_code
                    JOIN tl_stcm t4 ON t3.id=t4.site_id and t4.optp_id=2 
                    JOIN tt_rtan t6 ON t4.site_id=t6.site_id 
                    LEFT JOIN tm_aemp t8 ON(t6.aemp_id=t8.id) 
                    LEFT JOIN dm_trip_master t5 ON t2.TRIP_NO=t5.TRIP_NO and t5.SITE_ID IS NULL
                    WHERE t2.`DM_ID`='$aemp_code' AND t2.`STATUS`='N'AND t6.lfcl_id=1 AND t6.rtan_date >= SUBDATE(DATE(NOW()), 7)
                    AND t1.rout_day LIKE concat('%',DAYNAME(CURDATE()),'%')
                    GROUP by t6.site_id,t2.TRIP_NO,t2.DM_ACTIVITY,t3.id,t3.site_code,t3.site_name,t3.site_mob1,t8.aemp_usnm,
                    t8.id,t8.aemp_name,t3.geo_lat,t3.geo_lon,t4.optp_id,t6.lfcl_id
                    UNION ALL 
                    SELECT t2.`TRIP_NO`,t2.DM_ACTIVITY AS trip_status,t3.id AS SITE_ID,t3.site_code AS SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t8.aemp_usnm AS AEMP_USNM,
                        t8.id AS emp_id,t8.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t6.rtan_rtnm) totalINv,if(t6.lfcl_id='11',1,'0') Delin,t4.optp_id
                        ,t6.lfcl_id as deliveryStatus 
                    FROM `tl_dmst_rout`t1 
                    JOIN dm_trip t2 ON t1.`dm_code`=t2.DM_ID
                    JOIN tm_site t3 ON t1.`site_code`=t3.site_code
                    JOIN tl_stcm t4 ON t3.id=t4.site_id and t4.optp_id=2 
                    JOIN tt_rtan t6 ON t4.site_id=t6.site_id and t6.lfcl_id=11 AND t2.TRIP_NO=t6.dm_trip
                    LEFT JOIN tm_aemp t8 ON(t6.aemp_id=t8.id) 
                    LEFT JOIN dm_trip_master t5 ON t2.TRIP_NO=t5.TRIP_NO and t5.SITE_ID IS NULL
                    WHERE t2.`DM_ID`='$aemp_code' AND t2.`STATUS`='N' AND t1.rout_day LIKE concat('%',DAYNAME(CURDATE()),'%')
                    GROUP by t6.site_id,t2.TRIP_NO,t2.DM_ACTIVITY,t3.id,t3.site_code,t3.site_name,t3.site_mob1,t8.aemp_usnm,
                    t8.id,t8.aemp_name,t3.geo_lat,t3.geo_lon,t4.optp_id,t6.lfcl_id;
        ");

            /*$trip_Details = DB::connection($db_conn)->select("
         SELECT
    t5.plmt_id                                           AS Item_Price_List,
    t5.plmt_id                                           AS Grv_Item_Price_List,
    t1.id                                                AS Item_Id,
    t1.amim_code                                         AS Item_Code,
    t1.amim_imgl                                         AS amim_imgl,
    t4.issc_name                                         AS Category_Name,
    t1.amim_name                                         AS Item_Name,
    t6.pldt_tppr                                         AS Item_Rate,
    t6.pldt_tpgp                                         AS Grv_Item_Price,
    (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
    t1.amim_pvat                                         AS Item_vat,
    t6.amim_duft                                         AS Item_Factor,
    t4.issc_seqn                                         AS category_Showing_seqn,
    t6.amim_dunt                                         AS D_Unit,
    t6.amim_runt                                         AS R_Unit,
    SUM(t9.INV_QNTY-t9.DELV_QNTY)                        AS Stock_Qty,
    round(SUM(t9.ORDD_OAMT),4)                           AS t_amt
    FROM `tm_amim`t1
    INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
    INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
    INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
    INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
    INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
    INNER JOIN tm_aemp AS t7 ON t3.aemp_id=t7.id
    INNER JOIN dm_trip AS t8 ON t7.aemp_usnm=t8.DM_ID
    INNER JOIN dm_trip_detail t9 ON t8.TRIP_NO=t9.TRIP_NO and t9.AMIM_ID=t6.amim_id
    WHERE  t1.lfcl_id=1 AND
    t8.DM_ID='$aemp_code' AND t8.`STATUS`='N' GROUP BY t5.plmt_id,
    t5.plmt_id, t9.INV_QNTY,t9.DELV_QNTY, t9.ORDD_OAMT,
    t1.id ,
    t1.amim_code,
    t1.amim_imgl ,
    t4.issc_name ,
    t1.amim_name ,
    t6.pldt_tppr ,
    t6.pldt_tpgp ,
    t1.amim_pvat  ,
    t6.amim_duft ,
    t4.issc_seqn ,
    t6.amim_dunt ,
    t6.amim_runt;");*/

            $trip_Details = DB::connection($db_conn)->select("
         SELECT
  1                                          AS Item_Price_List,
  1                                          AS Grv_Item_Price_List,
  t1.id                                                AS Item_Id,
  t1.amim_code                                         AS Item_Code,
  t1.amim_imgl                                         AS amim_imgl,
  ''                                         AS Category_Name,
  t1.amim_name                                         AS Item_Name,
  t9.ORDD_UPRC                                         AS Item_Rate,
  t9.ORDD_UPRC                                         AS Grv_Item_Price,
  t9.ORDD_EXCS AS Item_gst,
  t1.amim_pvat                                         AS Item_vat,
  t1.amim_duft                                         AS Item_Factor,
  1                                         AS category_Showing_seqn,
  t1.amim_dunt                                         AS D_Unit,
  t1.amim_runt                                         AS R_Unit,
  SUM(t9.INV_QNTY-t9.DELV_QNTY)                        AS Stock_Qty,
  round(SUM(t9.ORDD_OAMT),4)                           AS t_amt
FROM  dm_trip_detail t9
INNER JOIN dm_trip AS t8 ON t8.TRIP_NO=t9.TRIP_NO 
inner join tm_amim as t1 on t9.AMIM_ID=t1.id
WHERE  t8.DM_ID='$aemp_code' AND t8.`STATUS`='N'
  GROUP BY                                        
  t9.INV_QNTY,t9.DELV_QNTY, t9.ORDD_OAMT,                                      
  t1.id ,                                              
  t1.amim_code,                                         
  t1.amim_imgl ,                                       
  t1.amim_name ,
  t9.ORDD_UPRC,
  t9.ORDD_EXCS,
  t1.amim_pvat ,
  t1.amim_duft,
  t1.amim_dunt,
  t1.amim_runt");


            if ($trip_m != null) {
                $arrayObj = (array)$trip_m[0];
                $TRIP_NO = $arrayObj['TRIP_NO'];
                $trip_status = $arrayObj['trip_status'];

            } else {
                $TRIP_NO = 0;
                $trip_status = 0;
            }

            if ($delivery_vs_collection_status != null) {
                $delivery_vs_collection_status = (array)$delivery_vs_collection_status[0];
                $total_delivery_amount = $delivery_vs_collection_status['total_delivery_amount'];
                $total_collection_amount = $delivery_vs_collection_status['total_collection_amount'];

            } else {
                $total_delivery_amount = 0;
                $total_collection_amount = 0;
            }


            if (count($pending_collection) > 0) {
                $collection = $pending_collection[0];
            } else {
                $collection = array('SITE_ID' => 0, 'slgp_id' => 0);
            }

            return response()->json(array('trip_no' => $TRIP_NO, 'trip_status' => $trip_status, 'delivery_amount' => $total_delivery_amount, 'collection_amount' => $total_collection_amount, 'data' => $trip_m, 'collection' => $collection, 'trip_item_data' => $trip_Details, 'status' => 1), 200);
        }

    }

    public
    function Pending_collection_siteList(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_code = $request->emp_code;

            $catalog_data = DB::connection($db_conn)->select("
            SELECT TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                    emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered ,optp_id AS p_type_id,deliveryStatus,slgp_id,aemp_mobile
                    from(SELECT t1.`TRIP_NO`,t1.DM_ACTIVITY AS trip_status,t6.SITE_ID,t6.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t6.AEMP_USNM,
                        t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t6.ORDM_ORNM) totalINv,if(t6.DELIVERY_STATUS='11',1,'0') Delin,t5.optp_id
                        ,t6.DELIVERY_STATUS as deliveryStatus,t6.slgp_id,t4.aemp_mob1 AS aemp_mobile
                        FROM `dm_trip`t1 
                         JOIN dm_trip_master as t6 ON (t1.`TRIP_NO`= t6.TRIP_NO)
                        JOIN tm_site t3 ON(t6.SITE_ID=t3.id)                      
                        join tl_stcm t5 ON(t5.site_id=t3.id) AND t5.slgp_id = t6.slgp_id 
                        JOIN tm_aemp t4 ON(t6.AEMP_USNM=t4.aemp_usnm) 
                        WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N'AND t6.COLLECTION_AMNT=0 AND t6.DELV_AMNT>0               
                        GROUP BY t1.`TRIP_NO`,t1.DM_ACTIVITY,t6.SITE_ID,t6.SITE_CODE,t6.AEMP_USNM,
                        t4.id,t3.geo_lat,t3.geo_lon,t5.optp_id,t6.DELIVERY_STATUS ,t6.slgp_id,t4.aemp_mob1) f
                    group by TRIP_NO,trip_status,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
                    emp_id,aemp_name,geo_lat,geo_lon,optp_id,deliveryStatus,slgp_id,aemp_mobile;
             ");

        }
        return $catalog_data;

    }

    public
    function catalog_data(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $emp_id = $request->emp_id;
            $slgp_id = $request->slgp_id;
            if ($slgp_id == 0) {
                $catalog_data = DB::connection($db_conn)->select("
        SELECT  p.item_id,
 p.sku_code,
 max(p.offer) offeritem,
 amim_imgl,
 amim_imic,
 item_category,
 item_name,
 item_price,
 item_factor FROM (SELECT 
       Item_Code        AS item_id,
       sku_code         AS sku_code,
       Offer            AS offer,
       amim_imgl        AS amim_imgl,
       amim_imic        AS amim_imic,
       Item_Category    AS item_category,
       Item_Name        AS item_name,
       Item_Price       AS item_price,
       Item_Factor      AS item_factor 
FROM (SELECT
  t9.id                                                                               AS Item_Code,
  t9.amim_code                                                                        AS sku_code,
  concat(t7.prdt_mbqt,'-',t7.prdt_fiqt, ',',t7.prdt_mnbt,'-',t7.prdt_disc,
         '%(',t9.amim_code ,',',round(t7.prdt_fipr, 2),')')                           AS Offer,
        
  t3.amim_imgl                                                                        AS amim_imgl,
  t3.amim_imic                                                                        AS amim_imic,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor 
  
  FROM tl_sgsm AS t1
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id 
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tm_prom AS t6 ON t1.slgp_id = t6.slgp_id
  INNER JOIN tt_prdt AS t7 ON t6.id = t7.prom_id AND t2.amim_id=t7.prdt_sitm
  INNER JOIN tm_amim AS t9 ON t7.prdt_fitm=t9.id 
WHERE t3.lfcl_id = '1' AND t1.aemp_id = $emp_id
      AND t6.lfcl_id='1'
      AND t6.prom_nztp = 0 
      AND t6.prom_sdat <= CURDATE()
      AND t6.prom_edat >= CURDATE())g 
UNION ALL
(
SELECT
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  ''                                                                                  AS Offer,
  t3.amim_imgl                                                                        AS amim_imgl,
  t3.amim_imic                                                                        AS amim_imic,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor 
  FROM tl_sgsm AS t1
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id 
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id = $emp_id AND t2.pldt_tppr>0       
GROUP BY t1.plmt_id, t3.id, t5.issc_name, 
t3.amim_name, t2.pldt_tppr, t2.amim_duft, 
t5.issc_seqn,t2.amim_dunt,t2.amim_runt,
t3.amim_code
))p
 GROUP by 
 p.item_id,
 p.sku_code,
 amim_imgl,
 amim_imic,
 item_category,
 item_name,
 item_price,
 item_factor
       ");
            } else {
                $catalog_data = DB::connection($db_conn)->select("
        SELECT  p.item_id,
 p.sku_code,
 max(p.offer) offeritem,
 amim_imgl,
 amim_imic,
 item_category,
 item_name,
 item_price,
 item_factor FROM (SELECT 
       Item_Code        AS item_id,
       sku_code         AS sku_code,
       Offer            AS offer,
       amim_imgl        AS amim_imgl,
       amim_imic        AS amim_imic,
       Item_Category    AS item_category,
       Item_Name        AS item_name,
       Item_Price       AS item_price,
       Item_Factor      AS item_factor 
FROM (SELECT
  t9.id                                                                               AS Item_Code,
  t9.amim_code                                                                        AS sku_code,
  concat(t7.prdt_mbqt,'-',t7.prdt_fiqt, ',',t7.prdt_mnbt,'-',t7.prdt_disc,
         '%(',t9.amim_code ,',',round(t7.prdt_fipr, 2),')')                           AS Offer,
        
  t3.amim_imgl                                                                        AS amim_imgl,
  t3.amim_imic                                                                        AS amim_imic,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor 
  
  FROM tl_sgsm AS t1
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t4.slgp_id=$slgp_id
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
  INNER JOIN tm_prom AS t6 ON t1.slgp_id = t6.slgp_id
  INNER JOIN tt_prdt AS t7 ON t6.id = t7.prom_id AND t2.amim_id=t7.prdt_sitm
  INNER JOIN tm_amim AS t9 ON t7.prdt_fitm=t9.id 
WHERE t3.lfcl_id = '1' AND t1.aemp_id = $emp_id
      AND t6.lfcl_id='1'
      AND t6.prom_nztp = 0 
      AND t6.prom_sdat <= CURDATE()
      AND t6.prom_edat >= CURDATE())g 
UNION ALL
(
SELECT
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  ''                                                                                  AS Offer,
  t3.amim_imgl                                                                        AS amim_imgl,
  t3.amim_imic                                                                        AS amim_imic,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor 
  FROM tl_sgsm AS t1
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t4.slgp_id=$slgp_id
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id = $emp_id AND t2.pldt_tppr>0       
GROUP BY t1.plmt_id, t3.id, t5.issc_name, 
t3.amim_name, t2.pldt_tppr, t2.amim_duft, 
t5.issc_seqn,t2.amim_dunt,t2.amim_runt,
t3.amim_code
))p
 GROUP by 
 p.item_id,
 p.sku_code,
 amim_imgl,
 amim_imic,
 item_category,
 item_name,
 item_price,
 item_factor
       ");
            }
            $sql_slgp = DB::connection($db_conn)->select("
                    SELECT t1.`slgp_id`,t2.slgp_name  
                    FROM `tl_sgsm`t1 JOIN tm_slgp t2 ON t1.`slgp_id`=t2.id
                    WHERE t1.`aemp_id` = $emp_id;
                ");


            //return $catalog_data;
            //return response()->json(array('data' => $trip_m,'status'=>1), 200);
            return Array(
                "products" => $catalog_data,
                "slgp_list" => $sql_slgp,
                "status" => 1,
            );
        }

    }


    function collectionReportUae(Request $request)
    {


        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;

        if ($db_conn != '') {
            $emp_id = $request->emp_id;
            $slgp_id = $request->slgp_id;

            $totalCollection = 0;
            $totalDepositHo = 0;
            if ($request->country_id == 14) {

                if (empty($request->selectedDate)) {
                    $sql = DB::connection($db_conn)->select("
                    SELECT t1.COLLECTION_AMNT AS collectionAmount, round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount,round(t1.COLL_REC_HO,4) AS hoAmount,
                    t2.site_name AS siteName,t2.site_code AS siteCode
                    FROM dm_collection AS t1 
                    INNER JOIN tm_site AS t2 ON t1.SITE_ID = t2.id
                    WHERE t1.AEMP_ID = $emp_id AND t1.INVT_ID = 1 AND t1.`STATUS`!=24
                    and (t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0
                    UNION  ALL 
                    SELECT (t1.COLLECTION_AMNT) AS collectionAmount, round(((t1.COLLECTION_AMNT) - (t1.COLL_REC_HO)),4) AS dueAmount,round((t1.COLL_REC_HO),4) AS hoAmount,
                    t3.mother_site_name AS siteName,t3.mother_site_code AS siteCode
                    FROM dm_collection AS t1                  
                    JOIN tl_site_party_mapping t3 ON t1.`SITE_CODE`=t3.mother_site_code
                    WHERE t1.AEMP_ID = $emp_id  AND t1.INVT_ID = 1 AND t1.`STATUS`!=24
                    and (t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0 group by t1.COLLECTION_AMNT,t3.mother_site_name,t3.mother_site_code;
                ");
                } else {
                    $sql = DB::connection($db_conn)->select("
                    SELECT t1.COLLECTION_AMNT AS collectionAmount,  round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount, round(t1.COLL_REC_HO,4) AS hoAmount,
                    t2.site_name AS siteName,t2.site_code AS siteCode
                    FROM dm_collection AS t1 
                    INNER JOIN tm_site AS t2 ON t1.SITE_ID = t2.id
                    WHERE t1.AEMP_ID = $emp_id AND t1.INVT_ID = 1 AND t1.`STATUS`!=24
					and date(t1.update_at) = '$request->selectedDate'
                    UNION  ALL 
                    SELECT DISTINCT t1.COLLECTION_AMNT AS collectionAmount,  round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount, round(t1.COLL_REC_HO,4) AS hoAmount,
                    t3.mother_site_name AS siteName,t3.mother_site_code AS siteCode
                    FROM dm_collection AS t1 
                     JOIN tl_site_party_mapping t3 ON t1.`SITE_CODE`=t3.mother_site_code
                    WHERE t1.AEMP_ID = $emp_id AND t1.INVT_ID = 1 AND t1.`STATUS`!=24
                    and date(t1.`COLL_DATE`) = '$request->selectedDate';
                ");
                }
            } else {
                if (empty($request->selectedDate)) {
                    $sql = DB::connection($db_conn)->select("
                    SELECT t1.COLLECTION_AMNT AS collectionAmount, round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount,round(t1.COLL_REC_HO,4) AS hoAmount,
                    t2.site_name AS siteName,t2.site_code AS siteCode
                    FROM dm_collection AS t1 
                    INNER JOIN tm_site AS t2 ON t1.SITE_ID = t2.id
                    WHERE t1.AEMP_ID = $emp_id  AND  INVT_ID = 1 AND t1.`STATUS`!=24
                    and (t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0
                    UNION  ALL 
                    SELECT SUM(t1.COLLECTION_AMNT) AS collectionAmount, round((SUM(t1.COLLECTION_AMNT) - SUM(t1.COLL_REC_HO)),4) AS dueAmount,round(SUM(t1.COLL_REC_HO),4) AS hoAmount,
                    t3.mother_site_name AS siteName,t3.mother_site_code AS siteCode
                    FROM dm_collection AS t1                  
                    JOIN tl_site_party_mapping t3 ON t1.`SITE_CODE`=t3.mother_site_code
                    WHERE t1.AEMP_ID = $emp_id  AND t1.INVT_ID = 1 AND t1.`STATUS`!=24
                    and (t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0 group by t1.COLLECTION_AMNT,t3.mother_site_name,t3.mother_site_code ;
                ");
                } else {
                    $sql = DB::connection($db_conn)->select("
                    SELECT t1.COLLECTION_AMNT AS collectionAmount,  round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount, round(t1.COLL_REC_HO,4) AS hoAmount,
                    t2.site_name AS siteName,t2.site_code AS siteCode
                    FROM dm_collection AS t1 
                    INNER JOIN tm_site AS t2 ON t1.SITE_ID = t2.id
                    WHERE t1.AEMP_ID = $emp_id  AND  INVT_ID = 1 AND t1.`STATUS`!=24
					and date(t1.update_at) = '$request->selectedDate'
                    UNION  ALL 
                    SELECT DISTINCT t1.COLLECTION_AMNT AS collectionAmount,  round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS dueAmount, round(t1.COLL_REC_HO,4) AS hoAmount,
                    t3.mother_site_name AS siteName,t3.mother_site_code AS siteCode
                    FROM dm_collection AS t1 
                     JOIN tl_site_party_mapping t3 ON t1.`SITE_CODE`=t3.mother_site_code
                    WHERE t1.AEMP_ID = $emp_id AND  INVT_ID = 1 AND t1.`STATUS`!=24
                    and date(t1.`COLL_DATE`) = '$request->selectedDate';
                ");
                }
            }
            foreach ($sql as $index => $item) {
                $totalCollection += $item->collectionAmount;
                $totalDepositHo += $item->hoAmount;
            }
            return Array(
                "totalCollection" => round($totalCollection, 4),
                "totalDepositHo" => round($totalDepositHo, 4),
                "collectionLists" => $sql,
            );
        }
    }

    public
    function syncAllDataPromotionalDataForDeliveryModule(Request $request)
    {


        $promotion = array();
        $promotion_buy_item = array();
        $promotion_slab = array();
        $promotion_free_item = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {


            $promotion = DB::connection($db_conn)->select("
                        SELECT
                        concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                        t1.id                                                AS promo_id,
                        t1.prms_name                                         AS promo_name,
                        t1.prms_sdat                                         AS start_date,
                        t1.prms_edat                                         AS end_date,
                        t1.prmr_qfct                                         AS qualifier_category,
                        t1.prmr_ditp                                         AS discount_type,
                        t1.prmr_ctgp                                         AS category_group,
                        t1.prmr_qfon                                         AS qualifier_on,
                        t1.prmr_qfln                                         AS qualifier_line
                        FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                        WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                        GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                ");


            $promotion_buy_item = DB::connection($db_conn)->select("
                        SELECT
                        concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                        t1.prmr_id                                                            AS promo_id,
                        t1.amim_id                                                            AS product_id,
                        t1.prmd_modr                                                          AS pro_modifier_id
                        FROM tm_prmd AS t1
                        INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                        INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                ");

            $promotion_slab = DB::connection($db_conn)->select("
                        SELECT
                        concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
                        t1.prmr_id                                                                            AS promo_id,
                        t1.prsb_text                                                                          AS promo_slab_text,
                        t1.prsb_fqty                                                                          AS from_qty,
                        t1.prsb_tqty                                                                          AS to_qty,
                        0                                                                                     AS unit,
                        0                                                                                     AS unit_factor,
                        t1.prsb_famn                                                                          AS from_amnt,
                        t1.prsb_tamn                                                                          AS to_amnt,
                        t1.prsb_qnty                                                                          AS qty,
                        0                                                                                     AS given_unit,
                        0                                                                                     AS given_unit_factor,
                        t1.prsb_disc                                                                          AS discount,
                        t1.prsb_modr                                                                          AS pro_modifier_id,
                        t1.prsb_mosl                                                                          AS pro_modifier_id_sl
                        FROM tm_prsb AS t1
                        INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                        INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        WHERE t3.site_id = $request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
                        t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
                ");

            $promotion_free_item = DB::connection($db_conn)->select("
                        SELECT
                        concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                        t1.prmr_id                                                            AS promo_id,
                        t1.amim_id                                                            AS product_id,
                        t1.prmd_modr                                                          AS pro_modifier_id
                        FROM tm_prmf AS t1
                        INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                        INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                        WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                        GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                ");

        }
        return Array(
            "promotion" => array("data" => $promotion, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $promotion_buy_item, "action" => $request->country_id),
            "promotion_slab" => array("data" => $promotion_slab, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $promotion_free_item, "action" => $request->country_id),

            "Sync_Product_Info_Table" => array("data" => [], "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => [], "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => [], "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => [], "action" => $request->country_id),
            "Grv_Reason" => array("data" => [], "action" => $request->country_id),


        );
    }

    public
    function getUaeNationality(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        //  $db_conn = $country->cont_conn;
        //  if ($db_conn != '') {
        $data1 = DB::connection()->select("
            SELECT id, cont_code as country_code, cont_name as country_name FROM `tl_cont` WHERE lfcl_id = 1
            ");

        return Array(
            "data" => $data1, "action" => $request->country_id
        );
        // }
    }

    public
    function OrderList_ForPOCopyUpload(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
            SELECT `ordm_ornm`,`ordm_time` FROM `tt_ordm` 
            WHERE `aemp_id`='$request->aemp_id' 
            AND site_id=$request->site_id
            AND slgp_id=$request->slgp_id
            AND date(created_at)=CURDATE()
            GROUP BY `ordm_ornm`;
            ");


            return $data1;
        }
    }

    public
    function getAssetItemList(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;

        $testArray = array('name' => "", 'path' => "");


        if ($db_conn != '') {


            $assetInfo = DB::connection($db_conn)->select("
                SELECT
                t2.astm_name AS assetName,
                t2.id AS assetId,
                (case when t1.astm_id=t2.id THEN 1 ELSE 0 END ) AS isExist
                FROM tl_assm t1,tm_astm t2
                Where t1.site_id=$request->site_id
            ");


            $result = [];
            $finalresult = array();
            foreach ($assetInfo as $item) {

                $assetName = $item->assetName;
                $assetId = $item->assetId;
                $isExist = $item->isExist;
                $result = [
                    'assetName' => $assetName,
                    'assetId' => $assetId,
                    'isExist' => $isExist,
                    'assetImages' => array($testArray),
                ];
                array_push($finalresult, $result);
            }
            return $finalresult;
        }
    }

    public
    function srGrvOrderReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $aemp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data2 = DB::connection($db_conn)->select("
                SELECT t1.`id`,t1.`rtan_rtnm`,t1.`rtan_pono`AS party_grv_no,t1.`rtan_podt`AS grv_date,
                t1.`rtan_date`AS order_date,t1.`site_id`,t4.site_name,round(t1.rtan_amnt,4) AS order_amt,round(SUM(t2.rtdd_damt),4)AS delivery_amt,
                t3.dprt_name AS reason,if(t1.lfcl_id=11,'delivered','pending')AS status
                FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.`id`=t2.rtan_id)
                JOIN tm_dprt t3 ON(t2.dprt_id=t3.id)
                JOIN tm_site t4 ON(t1.`site_id`=t4.id)
                WHERE t1.`aemp_id`=$aemp_id AND `rtan_date` BETWEEN '$start_date' AND '$end_date'
                GROUP BY t1.`id`,t1.`rtan_rtnm`,t1.rtan_pono,t1.rtan_podt,
                 t1.rtan_date,t1.`site_id`,t4.site_name,
                 t1.rtan_amnt,t2.rtdd_damt,t3.dprt_name;
            ");

            foreach ($data2 as $index => $data1) {

                $rtan_id = $data1->id;

                $data3 = DB::connection($db_conn)->select(" SELECT t2.amim_name,t2.amim_code,t1.`amim_id`,t1.`rtdd_qnty`,t1.rtdd_duft AS factor,
                t1.`rtdd_uprc`AS rate,round(t1.`rtdd_oamt`,4)AS order_amt,
                round(t1.rtdd_damt,4) AS delivery_amt,round(t1.rtdd_texc,4) AS excise_amt,
                round(t1.rtdd_tvat,4) AS vat_amt,
                t1.`rtdd_edat`AS expire_date,t3.dprt_name AS reason
                FROM `tt_rtdd`t1 JOIN tm_amim t2 ON(t1.`amim_id`=t2.id)
                JOIN tm_dprt t3 ON(t1.dprt_id=t3.id)
                WHERE t1.`rtan_id`=$rtan_id;");

                $data2[$index]->returnLine = $data3;
            }


            return Array(
                "data" => $data2, "action" => $request->emp_id
            );

            //return $data1;
        }
    }

    public
    function srGrvOrderReportDetails(Request $request)
    {
        $rtan_id = $request->rtan_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                SELECT t2.amim_name,t2.amim_code,t1.`amim_id`,t1.`rtdd_qnty`,
                t1.`rtdd_uprc`AS rate,round(t1.`rtdd_oamt`,4)AS order_amt,
                round(t1.rtdd_damt,4) AS delivery_amt,round(t1.rtdd_texc,4) AS excise_amt,
                round(t1.rtdd_tvat,4) AS vat_amt,
                t1.`rtdd_edat`AS expire_date,t3.dprt_name AS reason
                FROM `tt_rtdd`t1 JOIN tm_amim t2 ON(t1.`amim_id`=t2.id)
                JOIN tm_dprt t3 ON(t1.dprt_id=t3.id)
                WHERE t1.`rtan_id`=$rtan_id;
            ");


            return $data1;
        }
    }

    public
    function DpoWiseStockDetails(Request $request)
    {
        $emp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
            SELECT t2.amim_code,t2.amim_name,t2.amim_duft AS factor,t1.`DEPOT_B`,t3.dlrm_code,t3.dlrm_name 
            FROM `DEPOT_STOCK`t1 JOIN tm_amim t2 ON(t1.`DEPOT_ITEM`=t2.amim_code)
            LEFT JOIN tm_dlrm t3 ON(t1.`DEPOT_ID`=t3.dlrm_code)
            LEFT JOIN tl_srdi t4 ON(t3.id=t4.dlrm_id)
            WHERE t4.aemp_id=$emp_id  AND t2.lfcl_id=1;
            ");


            return $data1;
        }
    }

    public
    function getCancelDeliveryReason(Request $request)
    {
        $emp_id = $request->emp_id;
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
           SELECT `id` reason_id,`ondr_name`AS reason_name
            FROM `tm_ondr` WHERE `lfcl_id`=1;
            ");


            return $data1;
        }
    }

    public
    function PromoReCalculationDetailsData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;

        if ($db_conn != '') {
            $promo_id_list = json_decode($request->promo_id_list);

            $finalresult1 = array();
            $finalresult2 = array();
            foreach ($promo_id_list as $promo_id_list1) {

                $promo_id = $promo_id_list1;

                $data_promo = DB::connection($db_conn)->select("
            SELECT
            concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,
            t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`)        AS column_id,
            t1.id                                                AS promo_id,
            t1.prms_name                                         AS promo_name,
            t1.prms_sdat                                         AS start_date,
            t1.prms_edat                                         AS end_date,
            t1.prmr_qfct                                         AS qualifier_category,
            t1.prmr_ditp                                         AS discount_type,
            t1.prmr_ctgp                                         AS category_group,
            t1.prmr_qfon                                         AS qualifier_on,
            t1.prmr_qfln                                         AS qualifier_line
            FROM tm_prmr AS t1 
            WHERE t1.`id` = $promo_id
            GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat,
            t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln;
            ");
                $promotions = $data_promo ? $data_promo[0] : '';

                $data_slab = DB::connection($db_conn)->select("
            SELECT
            concat(t1.id,t1.prsb_fqty,t1.prsb_tqty,t1.prsb_famn,t1.prsb_disc,
            t1.prsb_mosl,t1.prsb_qnty) AS column_id,
            t1.prmr_id                                            AS promo_id,
            t1.prsb_text                                          AS promo_slab_text,
            t1.prsb_fqty                                          AS from_qty,
            t1.prsb_tqty                                          AS to_qty,
            0                                                     AS unit,
            0                                                     AS unit_factor,
            t1.prsb_famn                                          AS from_amnt,
            t1.prsb_tamn                                          AS to_amnt,
            t1.prsb_qnty                                          AS qty,
            0                                                     AS given_unit,
            0                                                     AS given_unit_factor,
            t1.prsb_disc                                          AS discount,
            t1.prsb_modr                                          AS pro_modifier_id,
            t1.prsb_mosl                                          AS pro_modifier_id_sl
            FROM tm_prsb AS t1
            WHERE t1.`prmr_id` = $promo_id
            GROUP BY t1.prmr_id, t1.prsb_fqty, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,t1.id,
            t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl ORDER BY column_id DESC;
            ");

                array_push($finalresult1, $promotions);
                foreach ($data_slab as $ds) {
                    array_push($finalresult2, $ds);
                }

            }

            return array(
                'data_promo' => $finalresult1,
                'data_slab' => $finalresult2,
            );
        }
    }

    public
    function personal_credit_data(Request $request)
    {
        $aemp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;


        if ($db_conn != '') {
            /*  $data1 = DB::connection($db_conn)->select("
      Select t1.site_id,t1.site_code,t1.site_name,t1.slgp_id,t1.ordm_ornm,t1.sapr_amnt,t1.credit_date
      FROM(
      select t7.`ordm_ornm`,t7.`sapr_amnt` ,t7.ordm_amnt,
      round((t7.ordm_amnt-t8.CRECIT_AMNT),3)AS s,t7.site_id,t2.site_code,t2.site_name,t3.slgp_id,
      date(t7.updated_at)AS credit_date
      FROM tl_cpcr  t7
      LEFT JOIN (Select TRANSACTION_ID,sum(CRECIT_AMNT)CRECIT_AMNT,SITE_ID  FROM dm_invoice_collection_mapp GROUP BY TRANSACTION_ID,SITE_ID)t8
      ON t7.ordm_ornm=t8.TRANSACTION_ID AND t7.site_id=t8.SITE_ID
      LEFT JOIN tm_site t2 ON(t7.site_id=t2.id)
      JOIN dm_trip_master t3 ON(t7.ordm_ornm=t3.ORDM_ORNM)
      WHERE t7.aemp_iusr=$aemp_id OR t7.aemp_eusr=$aemp_id)t1
      WHERE t1.s>0.1
      GROUP BY t1.site_id,t1.site_code,t1.site_name,t1.slgp_id,t1.ordm_ornm,t1.sapr_amnt,t1.credit_date
              ");*/


            $data1 = DB::connection($db_conn)->select("
    Select t1.`ACMP_CODE`,t1.ORDM_ORNM AS ORDM_ORNM,t1.`AEMP_ID`,t1.`AEMP_USNM`,t1.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t1.site_id,t1.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id,1 AS t_Type, 1 as p_type_id,
            t1.sapr_amnt AS DueAmnt,t1.sapr_amnt AS reqAmnt,t1.sapr_amnt AS apprv,1 AS CredStatus,t1.credit_date 
    FROM(
    select t7.`ordm_ornm`AS ORDM_ORNM,t7.`sapr_amnt` ,t7.ordm_amnt,
    round((t7.ordm_amnt-t8.CRECIT_AMNT),3)AS s,t3.`ACMP_CODE`,t3.`AEMP_ID`,t3.`AEMP_USNM`,t4.aemp_name,
            t3.`WH_ID`,t3.`SITE_ID`,t3.`SITE_CODE`,t2.site_name,t3.`DM_CODE`,t3.`IBS_INVOICE`,t3.slgp_id,
    date(t7.updated_at)AS credit_date
    FROM tl_cpcr  t7
    LEFT JOIN (Select TRANSACTION_ID,sum(CRECIT_AMNT)CRECIT_AMNT,SITE_ID  FROM dm_invoice_collection_mapp GROUP BY TRANSACTION_ID,SITE_ID)t8
    ON t7.ordm_ornm=t8.TRANSACTION_ID AND t7.site_id=t8.SITE_ID
    LEFT JOIN tm_site t2 ON(t7.site_id=t2.id) 
    JOIN dm_trip_master t3 ON(t7.ordm_ornm=t3.ORDM_ORNM)
    JOIN tm_aemp t4 ON(t3.AEMP_ID=t4.id)  
    WHERE t7.aemp_iusr=$aemp_id OR t7.aemp_eusr=$aemp_id and t7.attr4!=2)t1
    WHERE t1.s>0.1
    GROUP BY t1.site_id,t1.site_code,t1.site_name,t1.slgp_id,t1.ordm_ornm,t1.sapr_amnt,t1.credit_date;
            ");
            $data2 = DB::connection($db_conn)->select("
    SELECT round(`spbm_limt`,3)AS spbm_limt,round(`spbm_avil`,3)AS spbm_avil,round(`spbm_amnt`,3)AS spbm_amnt
    FROM `tm_scbm` 
    WHERE aemp_id = $aemp_id ;
    
            ");/* $data2 = DB::connection($db_conn)->select("
    SELECT round(`spbm_limt`,3)AS spbm_limt,round(`spbm_avil`,3)AS spbm_avil,round(`spbm_amnt`,3)AS spbm_amnt
    FROM `tm_scbm`
    WHERE aemp_id = $aemp_id
    AND spbm_mnth = MONTH(curdate())
    AND spbm_year = year(curdate());
            ");*/


            return array(
                'balance' => $data2,
                'data' => $data1,
            );
        }
    }

}