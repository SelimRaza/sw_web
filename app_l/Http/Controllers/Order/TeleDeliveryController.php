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

class TeleDeliveryController extends Controller
{
    private $access_key = 'tele/order_delv';
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

    

    public function orderVsDelv()
    {
        // if ($this->userMenu->wsmu_vsbl) {
            
            $countries = DB::select("SELECT id, cont_name, cont_code, cont_conn FROM `tm_cont`");
            return view('Order.order_vs_delv')->with('permission', $this->userMenu)->with('countries', $countries);
            
        // } else {
        //     return view('theme.access_limit');
        // }
    }

    public function orderVsDelvFilter(Request $request)
    {
        // return $request->all();
        $cont_list = $request->cont_id;
        $report_type=$request->report_type;
        $query = '';
        if($cont_list != ''){
            $query .= " WHERE  t1.cont_id IN (" . implode(',', $cont_list) . ") ";
        }


        $date = '';
        if($request->start_date != ''){
            $end_date   =  $request->end_date ? $request->end_date : date("Y-m-d");
            $date.= " t2.ordm_date BETWEEN '". $request->start_date. "' AND '". $end_date. "' ";
        }

        $data=DB::select("SELECT t1.cont_id, t2.cont_conn from tbl_tele_users t1
                        INNER JOIN tm_cont t2 ON t1.cont_id = t2.id
                        " .  $query ."
                        GROUP BY t1.cont_id, t2.cont_conn                        
                        ");
        
        $ord_data=array();
        if($report_type==2){
            foreach($data as $dt){
                $d_data=DB::connection($dt->cont_conn)->select("SELECT t2.ordm_date,
                        t1.id, t2.ordm_ornm, t2.ordm_amnt, t2.ordm_date,t7.site_name,t7.site_code,
                        t3.aemp_name, t3.aemp_usnm,
                        t4.slgp_name, t4.slgp_code,
                        t5.cont_name,IFNULL(t6.INV_AMNT,0)INV_AMNT,IFNULL(t6.DELV_AMNT,0)DELV_AMNT
                        FROM tbl_tele_users t1
                        INNER JOIN tt_ordm t2 ON t1.aemp_id = t2.aemp_id
                        INNER JOIN tm_aemp t3 ON t1.aemp_id = t3.id
                        INNER JOIN tm_slgp t4 ON t2.slgp_id = t4.id
                        INNER JOIN tm_cont t5 ON t3.cont_id = t5.id
                        LEFT JOIN dm_trip_master t6 ON t2.ordm_ornm=t6.ORDM_ORNM
                        INNER JOIN tm_site t7 ON t2.site_id=t7.id
                        WHERE $date");
                    if($d_data){
                        array_push($ord_data,$d_data);
                    }
    
                            
            }
        }
        else{
            foreach($data as $dt){
                
                $d_data=DB::connection($dt->cont_conn)->select("SELECT 
                        MONTHNAME(t2.ordm_date)month_name, 
                        t3.aemp_name, t3.aemp_usnm,
                        t4.slgp_name, t4.slgp_code,
                        t5.cont_name,IFNULL(ROUND(SUM(t2.ordm_amnt),2),0)ordm_amnt,IFNULL(ROUND(SUM(t6.INV_AMNT),2),0)INV_AMNT,IFNULL(ROUND(SUM(t6.DELV_AMNT),2),0)DELV_AMNT
                        FROM tbl_tele_users t1
                        INNER JOIN tt_ordm t2 ON t1.aemp_id = t2.aemp_id
                        INNER JOIN tm_aemp t3 ON t1.aemp_id = t3.id
                        INNER JOIN tm_slgp t4 ON t2.slgp_id = t4.id
                        INNER JOIN tm_cont t5 ON t3.cont_id = t5.id
                        LEFT JOIN dm_trip_master t6 ON t2.ordm_ornm=t6.ORDM_ORNM
                        INNER JOIN tm_site t7 ON t2.site_id=t7.id
                        WHERE $date
                        GROUP BY MONTHNAME(t2.ordm_date), t3.aemp_name, t3.aemp_usnm,t4.slgp_name, t4.slgp_code,t5.cont_name;");
                    //return $d_data;
                    if($d_data){
                        array_push($ord_data,$d_data);
                    }
    
                            
            }
        }
        
                    
        return $ord_data;
        
    }

}
