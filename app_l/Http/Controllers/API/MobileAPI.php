<?php

namespace App\Http\Controllers\API;

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
        if ($hris_status == 0) {
            $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
            if ($loginAttempt) {
                $db = Auth::user()->country()->cont_conn;
                $cont_id = Auth::user()->country()->id;
                $user_id = Auth::user()->id;
            }

        } else if ($hris_status == 1) {
            $loginAttempt = true;
            $user = User::where('email', '=', $name)->first();
            if ($user == null) {
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
                        'cont_id' => 1,
                    ]);
                    $emp = new Employee();
                    $emp->setConnection('myprg_p');
                    $emp->aemp_name = $staffResult[0]->NAME;
                    $emp->aemp_onme = $staffResult[0]->NAME;
                    $emp->aemp_stnm = $staffResult[0]->NAME;
                    $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                    $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                    $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                    $emp->aemp_lued = $user->id;
                    $emp->edsg_id = 1;
                    $emp->role_id = 1;
                    $emp->aemp_utkn = md5(uniqid(rand(), true));
                    $emp->cont_id = 1;
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
                    $emp->lfcl_id = 1;
                    $emp->var = 1;
                    $emp->attr1 = '';
                    $emp->attr2 = '';
                    $emp->attr3 = 1;
                    $emp->attr4 = 1;
                    $emp->save();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                }
                $db = $user->country()->cont_conn;
                $cont_id = $user->country()->id;
                $user_id = $user->id;

            } else {
                $db = $user->country()->cont_conn;
                $cont_id = $user->country()->id;
                $user_id = $user->id;
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
        if ($loginAttempt) {
            $emp = Employee::on($db)->where('aemp_lued', '=', $user_id)->first();
            $emp->aemp_utkn = $this->setEmpToken($user_id);
            $emp->region_id = '';
            $emp->country_name = $emp->country()->cont_name;
            $emp->image_folder = $emp->country()->cont_imgf;
            $role_id = $emp->role_id;
            /*$tst = DB::select("SELECT
  t2.amnu_id
FROM tl_grmp AS t1
  INNER JOIN tl_amnd AS t2 ON t1.amng_id = t2.amng_id
WHERE t1.role_id = $role_id and t1.cont_id = $cont_id ");
            foreach ($tst as $sdsd) {
                $user_menu = MobileEmpMenu::where(['users_id' => $user_id, 'amnu_id' => $sdsd->amnu_id, 'cont_id' => $cont_id])->first();
                if ($user_menu == null) {
                    $user_menu = new MobileEmpMenu();
                    $user_menu->amnu_id = $sdsd->amnu_id;
                    $user_menu->users_id = $user_id;
                    $user_menu->aumn_vsbl = 1;
                    $user_menu->lfcl_id = 1;
                    $user_menu->cont_id = $cont_id;
                    $user_menu->users_iusr = $user_id;
                    $user_menu->users_eusr = $user_id;
                    $user_menu->var = 1;
                    $user_menu->attr1 = '';
                    $user_menu->attr2 = '';
                    $user_menu->attr3 = 0;
                    $user_menu->attr4 = 0;
                    $user_menu->save();
                } else {
                    $user_menu->aumn_vsbl = 1;
                    $user_menu->users_eusr = $user_id;
                    $user_menu->save();
                }
            }*/

            return $emp;
        } else {
            return '[]';
        }
    }

    public function loginHris1(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where(['email' => $request->email])->first();
            if ($user != null) {
                $user->password = bcrypt($request->password);
                $user->save();
            }

        }
        $loginAttempt = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $log = new LoginLog();
        $log->user_name = $request->email;
        $log->api_int = $request->api_int;
        $log->device_imei = "$request->device_imei";
        $log->device_name = "$request->device_name";
        $log->mobile_number = '';
        $log->auth_type = 'hris';
        $log->login_attempt = 1;
        $log->save();
        if ($loginAttempt) {
            $emp = Employee::where('user_id', '=', Auth::user()->id)->first();
            $emp->user_token = $this->setEmpToken($emp->id);
            $emp->save();
            $emp->region_id = '';
            $emp->country_name = $emp->country()->name;
            return $emp;
        } else {
            return '[]';
        }
        //return $emp;
    }

    public function loginHris(Request $request)
    {
        $log = new LoginLog();
        $log->user_name = $request->user_name;
        $log->api_int = $request->api_int;
        $log->device_imei = "$request->device_imei";
        $log->device_name = "$request->device_name";
        $log->mobile_number = '';
        $log->auth_type = 'hris';
        $log->login_attempt = 1;
        $log->save();
        $return_data = '[]';
        $emp = Employee::where('user_name', '=', $request->user_name)->first();
        if ($emp) {
            $emp->user_token = $this->setEmpToken($emp->id);
            $emp->save();
            $emp->region_id = '';
            $emp->country_name = $emp->country()->name;
            $user_menu1 = MobileEmpMenu::where(['emp_id' => $emp->id, 'menu_id' => 48])->first();
            if ($user_menu1 == null) {
                $user_menu1 = new MobileEmpMenu();
                $user_menu1->menu_id = 48;
                $user_menu1->emp_id = $emp->id;
                $user_menu1->updated_by = 1;
                $user_menu1->visibility = 1;
                $user_menu1->country_id = 1;
                $user_menu1->created_by = 1;
                $user_menu1->updated_by = 1;
                $user_menu1->save();
            }
            $user_menu1->visibility = 1;
            $user_menu1->save();
            $user_menu2 = MobileEmpMenu::where(['emp_id' => $emp->id, 'menu_id' => 49])->first();
            if ($user_menu2 == null) {
                $user_menu2 = new MobileEmpMenu();
                $user_menu2->menu_id = 49;
                $user_menu2->emp_id = $emp->id;
                $user_menu2->updated_by = 1;
                $user_menu2->visibility = 1;
                $user_menu2->country_id = 1;
                $user_menu2->created_by = 1;
                $user_menu2->updated_by = 1;
                $user_menu2->save();
            }
            $user_menu2->visibility = 1;
            $user_menu2->save();
            $user_menu3 = MobileEmpMenu::where(['emp_id' => $emp->id, 'menu_id' => 45])->first();
            if ($user_menu3 == null) {
                $user_menu3 = new MobileEmpMenu();
                $user_menu3->menu_id = 45;
                $user_menu3->emp_id = $emp->id;
                $user_menu3->updated_by = 1;
                $user_menu3->visibility = 1;
                $user_menu3->country_id = 1;
                $user_menu3->created_by = 1;
                $user_menu3->updated_by = 1;
                $user_menu3->save();
            }
            $user_menu3->visibility = 1;
            $user_menu3->save();
            $user_menu4 = MobileEmpMenu::where(['emp_id' => $emp->id, 'menu_id' => 5])->first();
            if ($user_menu4 == null) {
                $user_menu4 = new MobileEmpMenu();
                $user_menu4->menu_id = 5;
                $user_menu4->emp_id = $emp->id;
                $user_menu4->updated_by = 1;
                $user_menu4->visibility = 1;
                $user_menu4->country_id = 1;
                $user_menu4->created_by = 1;
                $user_menu4->updated_by = 1;
                $user_menu4->save();
            }
            $user_menu4->visibility = 1;
            $user_menu4->save();
            $user_menu5 = MobileEmpMenu::where(['emp_id' => $emp->id, 'menu_id' => 52])->first();
            if ($user_menu5 == null) {
                $user_menu5 = new MobileEmpMenu();
                $user_menu5->menu_id = 52;
                $user_menu5->emp_id = $emp->id;
                $user_menu5->updated_by = 1;
                $user_menu5->visibility = 1;
                $user_menu5->country_id = 1;
                $user_menu5->created_by = 1;
                $user_menu5->updated_by = 1;
                $user_menu5->save();
            }
            $user_menu5->visibility = 1;
            $user_menu5->save();
            $return_data = $emp;
        } else {
            $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$request->user_name";
            $country_data = json_decode(file_get_contents($staff_detiails));
            $staffResult = json_decode($country_data->StaffResult);

            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $staffResult[0]->ID,
                    'email' => trim($staffResult[0]->ID),
                    'password' => bcrypt(trim($staffResult[0]->ID)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'status_id' => 1,
                    'cont_id' => 1,
                ]);
                $emp = new Employee();
                $emp->name = $staffResult[0]->NAME;
                $emp->ln_name = $staffResult[0]->NAME;
                $emp->mobile = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                $emp->address = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                $emp->user_id = $user->id;
                $emp->role_id = 1;
                $emp->master_role_id = 1;
                $emp->user_token = md5(uniqid(rand(), true));
                $emp->country_id = 1;
                $emp->created_by = 1;
                $emp->updated_by = 1;
                $emp->manager_id = 1;
                $emp->line_manager_id = 1;
                $emp->allowed_distance = "0";
                $emp->version_code = '0';
                $emp->location_interval = '50';
                $emp->auto_email = 3;
                $emp->location_on = 4;
                $emp->user_name = trim($staffResult[0]->ID);
                $emp->profile_image = '';
                $emp->profile_icon = '';
                $emp->email_cc = "";
                $emp->status_id = 1;
                $emp->save();
                $user_menu = new MobileEmpMenu();
                $user_menu->menu_id = 45;
                $user_menu->emp_id = $emp->id;
                $user_menu->updated_by = 1;
                $user_menu->visibility = 1;
                $user_menu->country_id = 1;
                $user_menu->created_by = 1;
                $user_menu->updated_by = 1;
                $user_menu->save();
                $user_menu = new MobileEmpMenu();
                $user_menu->menu_id = 48;
                $user_menu->emp_id = $emp->id;
                $user_menu->updated_by = 1;
                $user_menu->visibility = 1;
                $user_menu->country_id = 1;
                $user_menu->created_by = 1;
                $user_menu->updated_by = 1;
                $user_menu->save();
                $user_menu1 = new MobileEmpMenu();
                $user_menu1->menu_id = 48;
                $user_menu1->emp_id = $emp->id;
                $user_menu1->updated_by = 1;
                $user_menu1->visibility = 1;
                $user_menu1->country_id = 1;
                $user_menu1->created_by = 1;
                $user_menu1->updated_by = 1;
                $user_menu1->save();
                $user_menu4 = new MobileEmpMenu();
                $user_menu4->menu_id = 5;
                $user_menu4->emp_id = $emp->id;
                $user_menu4->updated_by = 1;
                $user_menu4->visibility = 1;
                $user_menu4->country_id = 1;
                $user_menu4->created_by = 1;
                $user_menu4->updated_by = 1;
                $user_menu4->save();
                $user_menu5 = new MobileEmpMenu();
                $user_menu5->menu_id = 52;
                $user_menu5->emp_id = $emp->id;
                $user_menu5->updated_by = 1;
                $user_menu5->visibility = 1;
                $user_menu5->country_id = 1;
                $user_menu5->created_by = 1;
                $user_menu5->updated_by = 1;
                $user_menu5->save();
                DB::commit();
                $emp->region_id = '';
                $emp->country_name = $emp->country()->name;
                $return_data = $emp;
            } catch (\Exception $e) {
                DB::rollback();
                $return_data = $e;
            }
        }

        return $return_data;
        //return $emp;
    }

    public function loginNew(Request $request)
    {
        $password = $request->password;
        $name = $request->email;
        $loginAttempt = Auth::attempt(['email' => $name, 'password' => $password]);
        $db = Auth::user()->country()->cont_conn;
        $cont_id = Auth::user()->country()->id;
        $user_id = Auth::user()->id;
        $log = new LoginLog();
        $log->user_name = $request->email;
        $log->api_int = $request->api_int;
        $log->device_imei = "$request->device_imei";
        $log->device_name = "$request->device_name";
        $log->mobile_number = '0';
        $log->auth_type = 'fms';
        $log->login_attempt = $loginAttempt;
        $log->save();
        if ($loginAttempt) {
            $emp = Employee::on($db)->where('aemp_lued', '=', Auth::user()->id)->first();
            $emp->aemp_utkn = $this->setEmpToken(Auth::user()->id);
            $emp->save();
            $emp->region_id = '';
            $emp->country_name = $emp->country()->cont_name;
            $emp->image_folder = $emp->country()->cont_imgf;
            $role_id = $emp->role_id;
            $tst = DB::select("SELECT
  t2.amnu_id
FROM tl_grmp AS t1
  INNER JOIN tl_amnd AS t2 ON t1.amng_id = t2.amng_id
WHERE t1.role_id = $role_id and t1.cont_id = $cont_id ");
            foreach ($tst as $sdsd) {
                $user_menu = MobileEmpMenu::where(['users_id' => $user_id, 'amnu_id' => $sdsd->amnu_id, 'cont_id' => $cont_id])->first();
                if ($user_menu == null) {
                    $user_menu = new MobileEmpMenu();
                    $user_menu->amnu_id = $sdsd->amnu_id;
                    $user_menu->users_id = $user_id;
                    $user_menu->aumn_vsbl = 1;
                    $user_menu->lfcl_id = 1;
                    $user_menu->cont_id = $cont_id;
                    $user_menu->users_iusr = $user_id;
                    $user_menu->users_eusr = $user_id;
                    $user_menu->var = 1;
                    $user_menu->attr1 = '';
                    $user_menu->attr2 = '';
                    $user_menu->attr3 = 0;
                    $user_menu->attr4 = 0;
                    $user_menu->save();
                } else {
                    $user_menu->aumn_vsbl = 1;
                    $user_menu->users_eusr = $user_id;
                    $user_menu->save();
                }
            }

            return $emp;
        } else {
            return '[]';
        }

    }

    public function employeeMemu(Request $request)
    {
        $tst = array();
        $user_id = $request->user_id;
        $role_id = $request->role_id;
        //   $country = Country::findorfail($country_id);
        //  if ($country != null) {
        /*$tst = DB::select("SELECT
  concat(t1.id,t2.amnu_code, t1.aumn_vsbl,t2.amnu_oseq) AS column_id,
  t1.users_id                 AS emp_id,
  t2.amnu_code                AS menu_id,
  t2.amnu_oseq                as amnu_oseq
FROM tl_aumn t1
  INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
WHERE t1.users_id = $request->user_id AND t1.aumn_vsbl =1 and t1.cont_id=$country_id ");*/
        //   }

        array_push($tst, (object)array("column_id" => "2$user_id", "token" => "2$user_id", "emp_id" => $user_id, "menu_id" => "2", "amnu_oseq" => "1"));
        array_push($tst, (object)array("column_id" => "3$user_id", "token" => "3$user_id", "emp_id" => $user_id, "menu_id" => "3", "amnu_oseq" => "4"));
        if ($role_id == 1) {

            array_push($tst, (object)array("column_id" => "1$user_id", "token" => "1$user_id", "emp_id" => $user_id, "menu_id" => "1", "amnu_oseq" => "2"));
            array_push($tst, (object)array("column_id" => "11$user_id", "token" => "11$user_id", "emp_id" => $user_id, "menu_id" => "11", "amnu_oseq" => "3"));
            array_push($tst, (object)array("column_id" => "5$user_id", "token" => "5$user_id", "emp_id" => $user_id, "menu_id" => "5", "amnu_oseq" => "4"));
        } else {
            // array_push($tst, (object)array("column_id" => "7$user_id", "token" => "7$user_id", "emp_id" => $user_id, "menu_id" => "7", "amnu_oseq" => "5"));
            // array_push($tst, (object)array("column_id" => "8$user_id", "token" => "8$user_id", "emp_id" => $user_id, "menu_id" => "8", "amnu_oseq" => "6"));
            // array_push($tst, (object)array("column_id" => "9$user_id", "token" => "9$user_id", "emp_id" => $user_id, "menu_id" => "9", "amnu_oseq" => "7"));
            array_push($tst, (object)array("column_id" => "10$user_id", "token" => "10$user_id", "emp_id" => $user_id, "menu_id" => "10", "amnu_oseq" => "8"));

        }
        array_push($tst, (object)array("column_id" => "0$user_id", "token" => "0$user_id", "emp_id" => $user_id, "menu_id" => "0", "amnu_oseq" => "0"));
        //  array_push($tst, (object)array("column_id" => "200$user_id", "token" => "200$user_id", "emp_id" => $user_id, "menu_id" => "200", "amnu_oseq" => "200"));
        return Array("tblt_emp_menu" => array("data" => $tst, "action" => $request->input('user_id')));
    }

    public function employeeMemuNew(Request $request)
    {
        $tst = array();
        $emp_id = $request->emp_id;
        $country_id = $request->country_id;
        $country = Country::findorfail($country_id);
        if ($country != null) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id,t2.amnu_code, t1.aumn_vsbl,t2.amnu_oseq) AS column_id,
  t1.aemp_id                  AS emp_id,
  t2.amnu_code                AS menu_id,
  t2.amnu_oseq                as amnu_oseq
FROM tl_aumn t1
  INNER JOIN tm_amnu t2 ON t1.amnu_id = t2.id
WHERE t1.aemp_id = $request->emp_id AND t1.aumn_vsbl =1 ");
        }
        array_push($tst, (object)array("column_id" => "0$emp_id", "token" => "0$emp_id", "emp_id" => $emp_id, "menu_id" => "0", "amnu_oseq" => "0"));
        array_push($tst, (object)array("column_id" => "200$emp_id", "token" => "200$emp_id", "emp_id" => $emp_id, "menu_id" => "200", "amnu_oseq" => "200"));
        return Array("tblt_emp_menu" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

    public function change(Request $request)
    {
        $change = false;
        if (Auth::attempt(['email' => $request->userName, 'password' => $request->oldPassword])) {
            $user = Auth::user();
            $user->password = bcrypt($request->newPassword);
            $user->save();
            DB::table('tt_pswd')->insert(
                array(
                    'pswd_time' => date('Y-m-d H:i:s'),
                    'pswd_user' => $request->userName,
                    'pswd_opwd' => $request->oldPassword,
                    'pswd_npwd' => $request->newPassword,
                    'cont_id' =>  $user->cont_id,
                    'lfcl_id' => 0,
                )
            );
            $change = true;
        }
        return ['changed' => $change];
    }

    public function isValidToken($token)
    {
        return $users = User::where('user_token', '=', $token)->first() ? 1 : 0;
    }

    public function validToken(Request $request)
    {
        return $users = User::where(['id' => $request->user_id, 'remember_token' => $request->user_token])->first() ? '1' : '0';
    }

    public function appUrl(Request $request)
    {
        /*$ip = $request->ip();
        $user_details_url = 'http://ip-api.com/json/' . $ip;
        $country_data = json_decode(file_get_contents($user_details_url));
        $login_url = "";
        if ($country_data->countryCode == "IN") {
            $login_url = "http://myprg.prgfms.com/";
        } elseif ($country_data->countryCode == "MY") {
            $login_url = "http://myprg.prgfms.com/";
        } elseif ($country_data->countryCode == "BD") {
            $login_url = "http://myprg.prgfms.com/";
        } else {
            $login_url = "http://myprg.prgfms.com/";
        }*/
        /*'RFL' => "http://myprg.prgfms.com/",
            'UAE' => "http://myprg.prgfms.com/",
            'Link1' => "http://apps.sihirfms.com/my_prg/index.php/",
            'Link2' => "http://178.128.126.21/my_prg/index.php/",
            'Link3' => "http://104.248.96.148/my_prg/index.php/",
            'Link4' => "http://myprg.sihirfms.com/",
            'Link5' => "http://172.17.107.136:8080/mdr/fms_new/",*/
        return array(
            'PRAN' => "http://myprg.prgfms.com/",
            'Nginx' => "http://myprg.sihirfms.com/",
        );
    }

    public function setEmpToken($userId)
    {
        $user = User::find($userId);
        $user->remember_token = time() . $userId . substr(md5(mt_rand()), 5, 22);
        $user->save();
        return $user->remember_token;
    }

    public function update(Request $request)
    {
        $nenVersion = "1.0.1";
        $current_apk = $request->input('current_apk_version');
        $current_apk_version = 'fms-' . $current_apk . '.apk';
        $filename = "fms-$nenVersion.apk";
        $return_obj = null;
        if ($filename == $current_apk_version) {
            $return_obj = array(
                'fileName' => null,
                "newVersion" => $nenVersion
            );
        } else {
            $return_obj = array(
                'fileName' => $filename,
                "newVersion" => $nenVersion);
        }
        if ($request->input('emp_id') != 0 && $request->input('current_apk_version') != 0) {
            $emp = Employee::find($request->input('emp_id'));
            $emp->version_code = $request->input('current_apk_version');
            $emp->save();
        }

        return $return_obj;
    }

    public function updatePrg(Request $request)
    {
        /*$nenVersion = "1.1.0";
        $current_apk = $request->input('current_apk_version');
        $current_apk_version = 'my_prg-' . $current_apk . '.apk';
        $filename = "my_prg-$nenVersion.apk";
        $return_obj = null;
        if ($filename == $current_apk_version) {
            $return_obj = array(
                'fileName' => null,
                "newVersion" => $nenVersion
            );
        } else {
            $return_obj = array(
                'fileName' => $filename,
                "newVersion" => $nenVersion);
        }
        if ($request->input('emp_id') != 0 && $request->input('current_apk_version') != 0) {
            $emp = Employee::find($request->input('emp_id'));
            $emp->version_code = $request->input('current_apk_version');
            $emp->save();
        }

        return $return_obj;*/
        $nenVersion = "1.1.0";
        $current_apk = $request->input('current_apk_version');
        $current_apk1 = $request->input('current_apk_version');
        if ($current_apk == "1.1.1") {
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
        if ($request->input('emp_id') != 0 && $request->input('current_apk_version') != 0) {
            $emp = Employee::find($request->input('emp_id'));
            $emp->version_code = $request->input('current_apk_version');
            $emp->save();
        }

        return $return_obj;
    }

    public function updatePrgTest(Request $request)
    {
        /*$nenVersion = "1.1.0";
        $current_apk = $request->input('current_apk_version');
        $current_apk_version = 'my_prg-' . $current_apk . '.apk';
        $filename = "my_prg-$nenVersion.apk";
        $return_obj = null;
        if ($filename == $current_apk_version) {
            $return_obj = array(
                'fileName' => null,
                "newVersion" => $nenVersion
            );
        } else {
            $return_obj = array(
                'fileName' => $filename,
                "newVersion" => $nenVersion);
        }
        if ($request->input('emp_id') != 0 && $request->input('current_apk_version') != 0) {
            $emp = Employee::find($request->input('emp_id'));
            $emp->version_code = $request->input('current_apk_version');
            $emp->save();
        } */
        $nenVersion = "2.0.4";
        $current_apk = $request->current_apk_version;
        $current_apk1 = $request->current_apk_version;
        if ($current_apk == "2.0.4") {
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
        /*  if ($request->input('emp_id') != 0 && $request->input('current_apk_version') != 0) {
              $emp = Employee::find($request->input('emp_id'));
              $emp->version_code = $request->input('current_apk_version');
              $emp->save();
          }*/

        return $return_obj;
    }

    public function time(Request $request)
    {
        $serverDateTime = date('Y-m-d H:i:s');
        $apkTime = strtotime($request->timestamp);
        $serverTime = strtotime($serverDateTime);
        $rows = array('status' => 0, 'server_time' => $serverDateTime);
        $timeDifference = round(abs($serverTime - $apkTime) / 60, 0);
        if ($timeDifference >= 5) {
            $rows = array('status' => round($timeDifference), 'server_time' => $serverDateTime);
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
        if ($request->line_data != '[]') {
            $dataLines = json_decode($request->line_data);
            if ($dataLines > 0) {
                if ($dataLines > 0) {
                    $locationHistory = [];
                    for ($i = 0; $i < count($dataLines); $i++) {
                        if ($dataLines[$i]->user_id != 0 and $dataLines[$i]->country_id != 0) {
                            $locationHistory[] = [
                                'user_id' => $dataLines[$i]->user_id,
                                'date' => date("Y-m-d", strtotime($dataLines[$i]->times_stamp)),
                                'times_stamp' => $dataLines[$i]->times_stamp,
                                'lat' => $dataLines[$i]->lat,
                                'lon' => $dataLines[$i]->lon,
                            ];
                        }
                    }

                    DB::beginTransaction();
                    try {
                        \DB::table('tblt_location_history')->insert($locationHistory);
                        $emp = User::findorfail($request->user_id);
                        DB::table('tblt_user_tracking')->where('user_name', $emp->email)->update(['lat' => $request->lat, 'lon' => $request->lon, 'date_time' => $request->times_stamp]);
                        DB::commit();
                        return array('column_id' => max(array_column($dataLines, 'id')));
                    } catch (\Exception $e) {
                        //  DB::commit();
                        return $e;
                    }
                } else {
                    return array('column_id' => 0);
                }
            } else {
                return array('column_id' => 0);
            }
        } else {
            return array('column_id' => 0);
        }


    }

    public function trackGPS(Request $request)
    {
        $location = Location::where(['user_id' => $request->user_id])->first();
        if ($location == null && $request->user_id != 0) {
            $location = new Location();
            $location->user_id = $request->user_id;
            $location->lat = $request->lat;
            $location->lon = $request->lon;
            $location->country_id = $request->country_id;
            $location->times_stamp = $request->times_stamp;
            $location->date = date("Y-m-d", strtotime($request->times_stamp));
            $location->save();
        } else if ($request->user_id != 0) {
            $location->lat = $request->lat;
            $location->lon = $request->lon;
            $location->times_stamp = $request->times_stamp;
            $location->country_id = $request->country_id;
            $location->date = date("Y-m-d", strtotime($request->times_stamp));
            $location->save();
        }
        if ($request->user_id == 0 || $request->country_id == 0) {

        } else {
            $locationHistory = new LocationHistory();
            $locationHistory->user_id = $request->user_id;
            $locationHistory->date = date("Y-m-d", strtotime($request->times_stamp));
            $locationHistory->times_stamp = $request->times_stamp;
            $locationHistory->lat = $request->lat;
            $locationHistory->lon = $request->lon;
            $locationHistory->country_id = $request->country_id;
            $locationHistory->save();
        }
        $emp = User::findorfail($request->user_id);
        DB::table('tblt_user_tracking')->where('user_name', $emp->email)->update(['lat' => $request->lat, 'lon' => $request->lon, 'date_time' => $request->times_stamp]);

        return array('column_id' => $request->id);
    }
}
