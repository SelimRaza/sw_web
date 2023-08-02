<?php

namespace App\Http\Controllers\API;
use App\User;
use App\MasterData\Depot;
use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DealerController extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function createDealerUser(Request $request)
    {
        return 'ok';
        try {
        
            DB::connection($db_conn)->beginTransaction();
            $country = Country::findorfail($request->country_id);
            $db_conn = $country->cont_conn;

            $id = $request->id;
            $this->currentUser = Auth::user();
            $dealer = DB::connection($db_conn)->select("SELECT `dlrm_name`, `dlrm_code`, `dlrm_adrs`,`dlrm_mob1`, `dlrm_emal`, `id`, slgp_id, cont_id FROM `tm_dlrm` WHERE lfcl_id='1' and id='$id'");
            return $dealer;

            $name = $dealer[0]->dlrm_name;

            $code = 'DMS'.$dealer[0]->dlrm_code;
            $pass_code = $dealer[0]->dlrm_code;
            $dlrm_adrs = $dealer[0]->dlrm_adrs;
            $id = $dealer[0]->id;
            $group_id = $dealer[0]->slgp_id;
            $cont_id = $dealer[0]->cont_id;
            $dlrm_mob1 = $dealer[0]->dlrm_mob1;
            $dlrm_emal = $dealer[0]->dlrm_emal;


            if ($cont_id=='2' || $cont_id=='5'){
                $user=User::where('email',$code)->first();
                return $user;
                if(!$user){
                    $user = User::create([
                        'name' => $name,
                        'email' => trim($code),
                        'password' => bcrypt(trim($pass_code)),
                        'remember_token' => md5(uniqid(rand(), true)),
                        'lfcl_id' => 1,
                        'cont_id' => $cont_id,
                    ]);
                }
                $employee = Employee::on($db_conn)->where('aemp_usnm',$code)->first();
                if($employee){
                    return 'Employee already created';
                }
                $emp = new Employee();
                $emp->setConnection($db_conn);
                $emp->aemp_name = $name;
                $emp->aemp_onme = $name;
                $emp->aemp_stnm = $name;
                $emp->aemp_mob1 = $dlrm_mob1;
                $emp->aemp_dtsm = $dlrm_mob1;
                $emp->aemp_emal = $dlrm_emal;
                $emp->aemp_lued = $user->id;

                $emp->zone_id = $request->zone_id;
                $emp->slgp_id  = $request->slgp_id;

                if ($cont_id=='2'){
                    $emp->role_id = 55;
                    $emp->edsg_id = 72;
                }else{
                    $emp->role_id = 55;
                    $emp->edsg_id = 7;
                }

                $emp->aemp_utkn = md5(uniqid(rand(), true));
                
                $emp->cont_id = $cont_id;
                // $emp->aemp_iusr = $this->currentUser->employee()->id;
                // $emp->aemp_eusr = $this->currentUser->employee()->id;

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
                $emp->amng_id = 0;
                // $emp->save();

                $depot = Depot::on($db_conn)->findorfail($id);
                $depot->dlrm_akey = 'Y';
                // $depot->save();

            }            

            DB::connection($db_conn)->commit();
            
            return $dealer;

        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return $e->getMessage();
        }

    }
}