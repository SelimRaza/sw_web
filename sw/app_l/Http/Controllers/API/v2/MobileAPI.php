<?php

namespace App\Http\Controllers\API\v3;

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
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MobileAPI extends Controller
{


    public function __construct()
    {
        $this->middleware('timezone');
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
        $nenVersion = "13";
        $user = User::where('email', '=', $name)->first();

        if ($hris_status == 0) {
            $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
            if ($loginAttempt) {
                if ($current_apk == '13' || $current_apk == '14' ||$current_apk == '15' || $current_apk == '12') {
                    $result_data = array(
                        'success' => 1,
                        'force_status' => 0,
                        'message' => "123 ",
                        'Login_token' => $user->remember_token,
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
                    'force_status' => 1,
                    'message' => "Username or password might be wrong !!!",
                );
            }
        } else {
            if ($current_apk == '13' || $current_apk == '14') {
                $result_data = array(
                    'success' => 1,
                    'force_status' => 0,
                    'message' => "44 ",
                    'Login_token' => $user->remember_token,
                );
            } else {
                $result_data = array(
                    'success' => 0,
                    'force_status' => 1,
                    'message' => "This '" . $current_apk . "' version is not allowed !!!",
                );
            }
        }
        /*else if ($hris_status == 1) {
           $loginAttempt = true;
           $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$name";
           $country_data = json_decode(file_get_contents($staff_detiails));
           $staffResult = json_decode($country_data->StaffResult);
           $hris_id = trim($staffResult[0]->ID);
           if ($hris_id == $name) {
               if ($current_apk == '13' || $current_apk == '14') {
                   $result_data = array(
                       'success' => 1,
                       'force_status' => 0,
                       'message' => "This is Right Version and Password Correct!!!  ",
                   );
               } else {
                   $result_data = array(
                       'success' => 2,
                       'force_status' => 1,
                       'message' => "This '" . $current_apk . "' Version Not Allowed !!!",
                   );
               }
           } else {
               $result_data = array(
                   'success' => 3,
                   'force_status' => 1,
                   'message' => "This Password Wrong !!! !!!",
               );
           }
       }*/


        return $result_data;
    }

    public function login(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $version_code = $request->version_code;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';

        if ($version_code == '13' || $version_code == '12' || $version_code == '14' || $version_code == '15') {

            if ($hris_status == 0) {
                $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
                if ($loginAttempt) {
                    if (Auth::user()->country() != null) {
                        $db = Auth::user()->country()->cont_conn;
                        $cont_id = Auth::user()->country()->id;
                        $user_id = Auth::user()->id;
                    }
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
                            'remember_token' => md5(uniqid(rand(), true)),
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
                        $emp->aemp_utkn = md5(uniqid(rand(), true));
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

                $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();
                //  $emp->spmp_sdpr = $spmp_sdpr->spmp_sdpr ? $spmp_sdpr->spmp_sdpr : '0';
                if ($spmp_sdpr == null) {
                    $spmp_sdpr1 = 0;
                } else {
                    $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
                }
                $emp->spmp_sdpr = $spmp_sdpr1;

                $country1 = (new Country())->country($cont_id);

                $emp->country_name = $emp->country()->cont_name;
                // $emp->module_type = $emp->country()->module_type;
                $emp->module_type = $country1->module_type;
                $emp->cont_tzon = $country1->cont_tzon;
                $emp->currency_symb = $country1->cncy_sym;
                $emp->memo_title = $country1->memo_title;
                $emp->memo_sub_title = $country1->memo_sub_title;
                $emp->image_folder = $emp->country()->cont_imgf;
                $emp->round_digit = $country1->cont_dgit;
                $emp->cont_ogdt = $emp->country()->cont_ogdt;
                $emp->currency = $emp->country()->cont_cncy;
                $emp->round_number = $emp->country()->cont_rund;
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

    public function login2(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $hris_status = $request->hris_status;
        $loginAttempt = false;
        $db = '';
        $user_id = '';
        $cont_id = '';
        if ($hris_status == 0) {
            $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
            if ($loginAttempt) {
                if (Auth::user()->country() != null) {
                    $db = Auth::user()->country()->cont_conn;
                    $cont_id = Auth::user()->country()->id;
                    $user_id = Auth::user()->id;
                }

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
                        'remember_token' => md5(uniqid(rand(), true)),
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
                    $emp->aemp_utkn = md5(uniqid(rand(), true));
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
        $log->api_int = $request->api_int;
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
            $spmp_sdpr = SPDS::on($db)->where(['slgp_id' => $emp->slgp_id, 'zone_id' => $emp->zone_id])->first();
            $emp->spmp_sdpr = $spmp_sdpr->spmp_sdpr ? $spmp_sdpr->spmp_sdpr : '0';
            $emp->country_name = $emp->country()->cont_name;
            $emp->image_folder = $emp->country()->cont_imgf;
            $emp->round_digit = $emp->country()->cont_dgit;
            $emp->cont_ogdt = $emp->country()->cont_ogdt;
            $emp->currency = $emp->country()->cont_cncy;
            $emp->round_number = $emp->country()->cont_rund;

            return $emp;
        } else {
            return '[]';
        }
    }

    public function countryChange(Request $request)
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

    public function setEmpToken($userId)
    {
        $user = User::find($userId);
        $user->remember_token = time() . $userId . substr(md5(mt_rand()), 5, 22);
        $user->save();
        return $user->remember_token;
    }

    public function employeeMemu(Request $request)
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

    public function employeeMemuNew(Request $request)
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
         $emp_id                                        AS emp_id,
         t3.amnu_code                             AS menu_id,
          t3.amnu_name                             AS menu_name,
          t3.amnu_iurl                                        AS icon_url,
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

    public function validToken(Request $request)
    {
        return $users = User::where(['id' => $request->user_id, 'remember_token' => $request->user_token])->first() ? '1' : '0';
    }

    public function spd_per(Request $request)
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

    public function userRewordPoint(Request $request)
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

    public function appUrl(Request $request)
    {
        return array(
            'Test' => "http://sprobo.sihirfms.com/",
            'Manik' => "http://172.17.107.136:8080/spro/spro_web/",
            'Raju' => "http://172.17.107.136:80/",

        );
    }

    public function update(Request $request)
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

    public function current_Version_Check(Request $request)
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


    public function time(Request $request)
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

    public function trackError(Request $request)
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
            return array('column_id' => $request->id);
        } catch (\Exception $e) {
            DB::commit();
            Log::error($e);
            Log::error($request);
            // return array('column_id' => $request->id);
        }
    }

    public function trackGPSSave(Request $request)
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

    public function trackGPS(Request $request)
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

    public function country(Request $request)
    {
        $country = Country::all();
        return Array("receive_data" => array("data" => $country, "action" => $request->input('user_id')));
    }

    public function fetchSuperHeroData(Request $request)
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

    public function fetchSuperManagerData(Request $request)
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

    public function getUserGroupList(Request $request)
    {

        $country = (new Country())->country($request->countryId);
        $empId = $request->empId;
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data = DB::connection($db_conn)->select("
            select t2.id,t2.slgp_code as code,t2.slgp_name as name  from tm_aemp as t1
            inner join tm_slgp as t2 on t1.slgp_id = t2.id
            where t1.id = 32786
            UNION
            select t3.id,t3.slgp_code,t3.slgp_name  from tl_egpr as t1 
            inner join tm_slgp as t3 on t3.id = t1.group_id
            where t1.aemp_id = $empId ");

            return Array(
                "salesGroups" => $data,
            );
        }
    }

    public function noteList(Request $request)
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

    public function siteRoutePlan(Request $request)
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

    public function employeeOrderPrintMemo(Request $request)
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
         t5.aemp_usnm AS sr_id,
         t5.aemp_name AS sr_name,
         t5.aemp_mob1 AS sr_mobile,
         t1.ordm_ornm AS order_id,
         t2.slgp_name AS group_name,
         t3.id        AS site_id,
         t3.site_code AS outlet_id,
         t3.site_name AS outlet_name,
         t3.site_olnm AS print_outlet_name,
         t3.site_olad    print_outlet_address,
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
         t8.zone_code
       FROM tt_ordm AS t1
         INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         INNER JOIN tm_dlrm AS t4 ON t1.dlrm_id = t4.id
         INNER JOIN tm_aemp AS t5 ON t1.aemp_id = t5.id
         INNER JOIN tm_rout t6 on t1.rout_id = t6.id
         INNER JOIN tm_base t7 on t6.base_id = t7.id
         INNER JOIN tm_zone t8 on t7.zone_id = t8.id
       WHERE t1.aemp_id = '$aemp_id' AND t1.ordm_date BETWEEN '$start_date' and '$end_date'");


            foreach ($order_master as $index => $data1) {
                $order_id = $data1->id;
                $site_id = $data1->site_id;
                $order_date = $data1->order_date;
                $data2 = DB::connection($db_conn)->select("SELECT
  t1.amim_id as product_code,
  t2.amim_name as product_name,
  t2.amim_olin as sku_print_name,
  round(t1.ordd_uprc,2) as rate,
  t1.ordd_qnty   as quantity,
  round((t1.ordd_opds+t1.ordd_spdo+t1.ordd_dfdo),2) as discont,
  round(t1.ordd_oamt,2) as price,
  t2.amim_duft AS product_factor
FROM tt_ordd as t1
  INNER JOIN tm_amim as t2 on t1.amim_id=t2.id
WHERE t1.ordm_id=$order_id");
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

                $order_master[$index]->orderLine = $data2;
                $order_master[$index]->returnLine = $data3;
                $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            return Array(
                "receive_data" => array("data" => $order_master, "action" => $request->emp_id),
            );
        }
    }

}
