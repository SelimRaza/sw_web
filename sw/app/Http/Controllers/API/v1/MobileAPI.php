<?php

namespace App\Http\Controllers\API\v1;

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
use Carbon\Carbon;

class MobileAPI extends Controller
{


    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function login(Request $request)
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
                /* if (strcasecmp("PRAN GROUP", $staffResult[0]->GROUPNAME) == 0) {
                     $country = (new Country())->country(2);

                 }
                */
                /*if (strcasecmp("RFL GROUP", $staffResult[0]->GROUPNAME) == 0) {
                    $country = (new Country())->country(5);
                    $amng_id = 2;
                }*/


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
            //  $emp->spmp_sdpr = $spmp_sdpr->spmp_sdpr ? $spmp_sdpr->spmp_sdpr : '0';
            if ($spmp_sdpr == null) {
                $spmp_sdpr1 = 0;
            } else {
                $spmp_sdpr1 = $spmp_sdpr->spmp_sdpr;
            }
            $emp->spmp_sdpr = $spmp_sdpr1;

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
            $employee->image_folder = $country1->cont_imgf;
            $employee->round_digit = $country1->cont_dgit;
            $employee->currency = $country1->cont_cncy;
            $employee->round_number = $country1->cont_rund;
            $employee->aemp_utkn = $user->remember_token;
            $employee->youtube_api_key = 'AIzaSyDh1jUrg_MsZcq_30834wBIXq4VAuP01SY';
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
  t1.amnu_oseq
FROM (
       SELECT
         concat($emp_id, t2.amnu_code, t1.aumn_vsbl, t2.amnu_oseq) AS column_id,
         t1.aemp_id                                          AS emp_id,
         t2.amnu_code                                        AS menu_id,
         t2.amnu_oseq                                        AS amnu_oseq
       FROM tl_aumn t1
         INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
       WHERE t1.aemp_id = $emp_id AND t1.aumn_vsbl = 1 AND t1.cont_id = $country_id
       UNION ALL
       SELECT
         concat($emp_id, t3.amnu_code, 1, t3.amnu_oseq) AS column_id,
         $emp_id                                        AS emp_id,
         t3.amnu_code                             AS menu_id,
         t3.amnu_oseq                             AS amnu_oseq
       FROM tm_amng AS t1
         INNER JOIN tl_amnd t2 ON t2.amng_id = t1.id
         INNER JOIN tm_amnu AS t3 ON t2.amnu_id = t3.id
       WHERE t1.cont_id = $country_id AND (t1.role_id = $role_id OR t1.id = $amng_id)
     ) AS t1
GROUP BY t1.column_id, t1.emp_id, t1.menu_id, t1.amnu_oseq");
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
            'Test' => "http://sprod.sihirfms.com/",
            'Live' => "http://spro.sihirfms.com/",

        );
    }

    public function update(Request $request)
    {
        $nenVersion = "1.0.13";
        $current_apk = $request->current_apk_version;
        $current_apk1 = $request->current_apk_version;
        if ($current_apk == "1.0.13" ||$current_apk == "1.0.12" || $current_apk == "2.1.2") {
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


    public function time(Request $request)
    {

        $country1 = (new Country())->country($request->country_id);
        $cont_tzon = $country1->cont_tzon;
        $serverDateTime = Carbon::now($cont_tzon)->format('Y-m-d H:i:s');
        $rows = array('status' => 1, 'server_time' => $serverDateTime);
        return $rows;
       // $serverDateTime = date('Y-m-d H:i:s');
      /*  $apkTime = strtotime($request->timestamp);
        $serverTime = strtotime($serverDateTime);
        $rows = array('status' => 0, 'server_time' => $serverDateTime);
        $timeDifference = round(abs($serverTime - $apkTime) / 60, 0);
        if ($timeDifference >= 30) {
            //if ($timeDifference >= 720) {
            $rows = array('status' => round($timeDifference), 'server_time' => $serverDateTime);
            // $rows = array('status' => '720', 'server_time' => $serverDateTime);
        }
        return $rows;*/
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

    public function countryList(Request $request){ 
        
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $sql = DB::select("
                SELECT id,cont_name FROM `tl_cont` where lfcl_id = 1
            
            ");
        }
        return Array("receive_data" => array("data" => $sql, "action" => 1 ));
    }
}