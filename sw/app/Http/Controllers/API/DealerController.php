<?php

namespace App\Http\Controllers\API;
use App\User;
use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Http\Request;
use App\MasterData\DepotEmployee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DealerController extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function createDealerUser(Request $request)
    {
        try {
            
            $country = Country::findorfail($request->country_id);
            $db_conn = $country->cont_conn;

            DB::connection($db_conn)->beginTransaction();

            $up_user_id = $request->up_user_id;
            $dealer_emp = Employee::on($db_conn)->where('id',$up_user_id)->first();
            
            if(!$dealer_emp){
                return response()->json([
                    "code" => 404,
                    "status" => "error",
                    "message" => "Employee not found"

                ]);
            }
                    
            $aemp_usnm  = $dealer_emp->aemp_usnm;
            $dealer_code = str_replace('DMS', '', $aemp_usnm);
            $dealer = DB::connection($db_conn)->select("SELECT `acmp_id`, `dlrm_name`, `dlrm_code`, `dlrm_adrs`,`dlrm_mob1`, `dlrm_emal`, `id`, slgp_id, cont_id FROM `tm_dlrm` WHERE lfcl_id='1' and dlrm_code='$dealer_code'");
            if(!$dealer){
                return response()->json([
                    "code" => 404,
                    "status" => "error",
                    "message" => "Dealer not found"

                ]);
            }
            $cur_second = date('s');
            $code1 = $dealer_code.$cur_second;
            $code = 'DD'.$code1;			
           // $pass_code = $request->dm_code;
            $pass_code = $code;           			

            $dlrm_adrs = $dealer[0]->dlrm_adrs;
            $id = $dealer[0]->id;
            $group_id = $dealer[0]->slgp_id;
            $cont_id = $dealer[0]->cont_id;
            $dlrm_mob1 = $dealer[0]->dlrm_mob1;
            $dlrm_emal = $dealer[0]->dlrm_emal;
            
            if ($cont_id=='2' || $cont_id=='5'){
                $user=User::where('email',$code)->first();
                if(!$user){
                    $user = User::create([
                        'name' => $request->dm_name,
                        'email' => trim($code),
                        'password' => bcrypt(trim($pass_code)),
                        'remember_token' => md5(uniqid(rand(), true)),
                        'lfcl_id' => 1,
                        'cont_id' => $cont_id,
                    ]);
                }
                
                $employee = Employee::on($db_conn)->where('aemp_usnm',$code)->first();
                if($employee){
                    return response()->json([
                        "code" => 200,
                        "status" => "success",
                        "message" => "User already created"

                    ]);
                }

                $emp = new Employee();
                $emp->setConnection($db_conn);
                $emp->aemp_name = $request->dm_name;
                $emp->aemp_onme = $request->dm_name;
                $emp->aemp_stnm = $request->dm_name;
                $emp->aemp_mob1 = $request->dm_mobile;
                $emp->aemp_dtsm = $request->dm_mobile;
                $emp->aemp_emal = $dlrm_emal;
                $emp->aemp_lued = $user->id;

                $emp->zone_id = $dealer_emp->zone_id;
                $emp->slgp_id  = $dealer_emp->slgp_id;

                if ($cont_id=='2'){
                    $emp->role_id = 56;
                    $emp->edsg_id = 73;
                }else{
                    $emp->role_id = 56;
                    $emp->edsg_id = 8;
                }

                $emp->aemp_utkn = md5(uniqid(rand(), true));
                
                $emp->cont_id = $cont_id;
                $emp->aemp_iusr = $dealer_emp->id;
                $emp->aemp_eusr = $dealer_emp->id;

                $emp->aemp_mngr = 1;
                $emp->aemp_lmid = 1;
                $emp->aemp_aldt = 0;
                $emp->site_id = 0;

                $emp->aemp_otml = 0;
                $emp->aemp_crdt = 0;
                $emp->aemp_issl = 0;
                $emp->aemp_lcin = 0;
                
                $emp->aemp_lonl = 0;
                $emp->aemp_usnm = $code;
                $emp->aemp_pimg = '';
                $emp->aemp_picn = '';
                
                $emp->aemp_emcc = '';
                $emp->lfcl_id = 1;
                $emp->amng_id = 8;
                $emp->save();

                $de_emp = new DepotEmployee;
                $de_emp->setConnection($db_conn);
                $de_emp->aemp_code = $code;
                $de_emp->dlrm_code = $dealer[0]->dlrm_code;
                $de_emp->aemp_id  = $emp->id;
                $de_emp->dlrm_id = $dealer[0]->id;
                $de_emp->acmp_id= $dealer[0]->acmp_id;
                $de_emp->cont_id= $dealer[0]->cont_id;
                $de_emp->lfcl_id  = 1;
                $de_emp->aemp_iusr   = $dealer_emp->id;
                $de_emp->aemp_eusr   = $dealer_emp->id;
                $de_emp->save();

            }            

            DB::connection($db_conn)->commit();
            
            return response()->json([
                "code" => 200,
                "status" => "success",
                "message" => "Successfully created"

            ]);

        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return response()->json([
                "code" => 500,
                "status" => "error",
                "message" => "Something went wrong"

            ]);
        }

    }
}