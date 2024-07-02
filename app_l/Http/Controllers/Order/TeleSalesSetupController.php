<?php

namespace App\Http\Controllers\Order;

use Excel;
use Image;
use App\User;
use Response;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Employee;
use App\BusinessObject\TeleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeleSalesSetupController extends Controller
{
    private $access_key = 'tele/setup';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $employee;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->employee = Auth::user()->employee();
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function teleSetup(){
        if ($this->userMenu->wsmu_vsbl) {
            $query = "SELECT t9.id eid, t9.lfcl_id, t1.id,  t1.aemp_name, t1.aemp_mob1, t1.aemp_usnm, t2.role_name, t3.edsg_name,t8.site_code,
                    t4.aemp_usnm as m_code, t4.aemp_name as m_name, t5.amng_name,t7.slgp_name
                    FROM `tbl_tele_users` t9
                    INNER JOIN tm_aemp t1 ON t9.aemp_usnm = t1.aemp_usnm
                    INNER JOIN tm_role t2 ON t1.role_id=t2.id 
                    INNER JOIN tm_edsg t3 ON t1.edsg_id=t3.id 
                    INNER JOIN tm_aemp t4 ON t1.`aemp_mngr`=t4.id 
                    LEFT JOIN tm_amng t5 ON t1.amng_id=t5.id 
                    INNER JOIN tm_slgp t7 ON t1.slgp_id=t7.id
                    LEFT JOIN tm_site t8 ON t1.site_id=t8.id";
            // $empl = DB::connection($this->db)->select("SELECT * FROM `tbl_tele_users` ORDER BY id DESC");
            $empl = DB::connection($this->db)->select(DB::raw($query));
            return view('Order.tele_setup')->with('permission', $this->userMenu)->with('empl', $empl);
            
        } else {
            return view('theme.access_limit');
        }
    }

    public function userActiveInActive($id)
    {
        try {
            if ($this->userMenu->wsmu_delt) {

                DB::connection($this->db)->beginTransaction();

                $emp = TeleUsers::on($this->db)->findorfail(intVal($id));
                $emp->lfcl_id = $emp->lfcl_id == 1 ? 2 : 1;
                $emp->save();

                DB::table('tbl_tele_users')
                    ->where('aemp_usnm', $emp->aemp_usnm)
                    ->update(['lfcl_id' => DB::raw('IF(lfcl_id = 1, 2, 1)')]);

                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', ' Employee Status Updated Success');

            } else {
                return redirect()->back()->with('danger', 'Access Limited');
            }
        } catch (\Throwable $th) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', ' Something Went wrong !!!!'.$th->getMessage());
        }
        
    }


    public function getEmployee($id){
        $employee = DB::connection($this->db)->select("SELECT * FROM `tm_aemp` WHERE aemp_usnm = '$id'")[0];
        return [
            "employee" => $employee
        ];
    }

    public function teleUserStore(Request $request)
    {
        try {
            DB::connection($this->db)->beginTransaction();
            $aemp_usnm = $request->user_id;
            $data = DB::connection($this->db)->select("SELECT t1.id, t1.aemp_usnm, t1.cont_id, t1.slgp_id, t1.lfcl_id, t2.acmp_id
                         FROM `tm_aemp` t1 
                         INNER JOIN tm_slgp t2 ON t2.id=t1.slgp_id
                         WHERE aemp_usnm = '$aemp_usnm'");

            
            if($data){
                $aemp_usnm = $data[0]->aemp_usnm;
                $employee = DB::connection($this->db)->select("SELECT * FROM `tbl_tele_users` WHERE aemp_usnm = '$aemp_usnm'");
                // if($employee){
                //     return redirect()->back()->with('warning', 'Employee Already Exist');
                // }
                if(!$employee){
                    DB::connection($this->db)->table('tbl_tele_users')->insert([
                        'aemp_id' => $data[0]->id,
                        'aemp_usnm' => $data[0]->aemp_usnm,
                        'lfcl_id' => $data[0]->lfcl_id,
                        'var' => 0,
                        'attr1' => 0,
                        'attr2' => 0,
                        'attr3' => 0,
                    ]);
                }
                
                $cont_id = Auth::user()->country()->id;
                $common_employee = DB::select("SELECT * FROM `tbl_tele_users` WHERE aemp_usnm = '$aemp_usnm' AND cont_id = '$cont_id'");
                if($common_employee){
                    return redirect()->back()->with('warning', 'Employee Already Exist');
                }
                
                DB::table('tbl_tele_users')->insert([
                    'aemp_id' => $data[0]->id,
                    'aemp_usnm' => $data[0]->aemp_usnm,
                    'cont_id' => $data[0]->cont_id,
                    'acmp_id' => $data[0]->acmp_id,
                    'slgp_id' => $data[0]->slgp_id,
                    'lfcl_id' => $data[0]->lfcl_id,
                    'attr3'   => 0,
                ]);
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', ' Successfully Added');
            }else{
                return redirect()->back()->with('danger', 'Employee not found!!!!');
            }

        } catch (\Throwable $th) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', ' Something Went wrong !!!!');
        }
        
    }

    public function perUserRoute(Request $request){

        try {
            // Truncate the table
            DB::connection($this->db)->statement('TRUNCATE TABLE tl_tele_setup');

            DB::connection($this->db)->table('tl_tele_setup')->insert([
                'totl_oult' => $request->total_route,
                'tuch_oult' => $request->touch_route,
                'nont_oult' => $request->untouch_route,
                'off_rout' => $request->day_name,
            ]);
            return [
                'status' => true,
                'message' => 'Successfully Added'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => ' Something Went wrong !!!!'.$th
            ];
        }
    }

    public function orderVsDelv()
    {
        if ($this->userMenu->wsmu_vsbl) {
            
            $countries = DB::select("SELECT id, cont_name, cont_code, cont_conn FROM `tm_cont`");
            return view('Order.order_vs_delv')->with('permission', $this->userMenu)->with('countries', $countries);
            
        } else {
            return view('theme.access_limit');
        }
    }

}
