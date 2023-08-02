<?php

namespace App\Http\Controllers\Order;

use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Image;
use Excel;
use Response;

class OrderLIveDashboardController extends Controller
{
    private $access_key = 'telesales/dashboard';
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

    public function index(){
        // if ($this->userMenu->wsmu_vsbl) {
            return view('Order.dashboard')->with('permission', $this->userMenu);
        // } else {
        //     return view('theme.access_limit');
        // }
    }
    public function getDashboardData(){

        $query = "";

        // $data=DB::select("SELECT t2.id,t2.cont_name,t2.cont_conn,t2.cont_code,ROUND(sum(tltr_amnt),2)t_amnt, SUM(IF(tltr_ordr=1,1,0)) t_memo, SUM(IF(tltr_ordr=0,1,0)) t_npcl,
        //             COUNT(t1.id)t_call,count(distinct aemp_id)active_usr FROM `tt_tltr` t1 INNER JOIN tm_cont t2 ON t1.cont_id=t2.id WHERE t1.tltr_date=curdate() AND t1.cont_id !=2 GROUP BY t1.cont_id ORDER BY t1.cont_id ASC; ");
        $data=DB::select("SELECT 
                        t1.*,t2.total_usr
                        FROM (
                        SELECT t2.id,t2.cont_name,t2.cont_conn,t2.cont_code,ROUND(sum(tltr_amnt),2)t_amnt,
                        SUM(IF(tltr_ordr=1,1,0)) t_memo,
                        SUM(IF(tltr_ordr=0,1,0)) t_npcl,
                        COUNT(t1.id)t_call,count(distinct aemp_id)active_usr 
                        FROM `tt_tltr` t1
                        INNER JOIN tm_cont t2 ON t1.cont_id=t2.id
                        WHERE t1.tltr_date=curdate() AND t1.cont_id !=2 GROUP BY t1.cont_id ORDER BY t1.cont_id ASC)t1
                        LEFT JOIN 
                        (SELECT cont_id,count(distinct aemp_id)total_usr FROM  tbl_tele_users WHERE lfcl_id=1 GROUP BY cont_id) t2 ON t1.id=t2.cont_id");

        $bd_data=DB::select("SELECT t1.*,t2.acmp_snam,count(aemp_id)total_usr FROM (SELECT t2.id cont_id,t2.cont_name,t2.cont_conn,t2.cont_code,ROUND(sum(tltr_amnt),2)t_amnt, 
                SUM(IF(tltr_ordr=1,1,0)) t_memo, SUM(IF(tltr_ordr=0,1,0)) t_npcl, COUNT(t1.id)t_call,count(distinct aemp_id)active_usr ,t1.acmp_id FROM `tt_tltr` t1
                INNER JOIN tm_cont t2 ON t1.cont_id=t2.id WHERE t1.tltr_date=curdate() AND t2.id=2 GROUP BY t1.acmp_id,t1.cont_id ORDER BY t1.cont_id ASC)t1 
                INNER JOIN tm_acmp t2 ON t1.acmp_id=t2.id LEFT JOIN tbl_tele_users t3 ON t1.cont_id=t3.cont_id AND t1.acmp_id=t3.acmp_id WHERE t3.lfcl_id=1 GROUP BY t2.id ORDER BY t2.id ASC;");
        $rout_outlet=array();
        foreach($bd_data as $bd){
            $s_data=DB::connection($bd->cont_conn)->select("SELECT 
                    COUNT(t3.site_id)t_site
                    FROM tbl_tele_users t1
                    INNER JOIN tl_rpln t2 ON t1.aemp_id=t2.aemp_id
                    INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                    INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
                    INNER JOIN tm_slgp t5 ON t4.slgp_id=t5.id
                    WHERE t2.rpln_day=DAYNAME(curdate()) AND t1.lfcl_id=1  AND t5.acmp_id={$bd->acmp_id}");
            array_push($rout_outlet,$s_data[0]->t_site);
        }
        
        $t_outlet=array();
        foreach($data as $dt){
            $s_data=DB::connection($dt->cont_conn)->select("SELECT 
                    COUNT(t3.site_id)t_site
                    FROM tbl_tele_users t1
                    INNER JOIN tl_rpln t2 ON t1.aemp_id=t2.aemp_id
                    INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
                    WHERE t2.rpln_day=DAYNAME(curdate()) AND t1.lfcl_id=1");
            array_push($t_outlet,$s_data[0]->t_site);
        }
        $mergedData = collect($bd_data)
                        ->map(function ($item, $index) use ($rout_outlet) {
                            $item->rout_outlet = $rout_outlet[$index] ?? null;
                            return $item;
                        })
                        ->all();
        $overseas = collect($data)
                        ->map(function ($item, $index) use ($t_outlet) {
                            $item->rout_outlet = $t_outlet[$index] ?? null;
                            return $item;
                        })
                        ->all();
        return array('tele_data'=>$data,'t_outlet'=>$t_outlet, 'bd_data' => $mergedData,'mergedData'=>$mergedData,'rout_outlet'=>$rout_outlet,'overseas'=>$overseas);
    }
    public function getDashboardData1(){

        $date=date('Y-m-d');

      //  $loop_data=DB::connection($this->db)->select(" SELECT t2.acmp_name FROM tt_tltr t1 INNER JOIN tm_acmp t2 ON t1.aemp_id=t2.id WHERE t1.tltr_date={$date} AND t1.cont_id=2  GROUP BY t2.id ORDER BY acmp_id ASC ");
        $results=DB::select("SELECT t1.*,t2.acmp_snam,count(aemp_id)total_usr FROM (SELECT t2.id cont_id,t2.cont_name,t2.cont_conn,t2.cont_code,ROUND(sum(tltr_amnt),2)t_amnt, 
                            SUM(IF(tltr_ordr=1,1,0)) t_memo, SUM(IF(tltr_ordr=0,1,0)) t_npcl, COUNT(t1.id)t_call,count(distinct aemp_id)active_usr ,t1.acmp_id FROM `tt_tltr` t1
                            INNER JOIN tm_cont t2 ON t1.cont_id=t2.id WHERE t1.tltr_date=curdate() AND t2.id=2 GROUP BY t1.acmp_id,t1.cont_id ORDER BY t1.cont_id ASC)t1 
                            INNER JOIN tm_acmp t2 ON t1.acmp_id=t2.id LEFT JOIN tbl_tele_users t3 ON t1.cont_id=t3.cont_id AND t1.acmp_id=t3.acmp_id WHERE t3.lfcl_id=1 GROUP BY t2.id;");
        
        $rows = [];
        foreach ($results as $row) {
            $arrayData = explode(',', $row->acmp_snam);
            foreach ($arrayData as $value) {
                $rows[] = ['acmp' => $value];
            }
            $arrayData = explode(',', $row->total_usr);
            foreach ($arrayData as $value) {
                $rows[] = ['total_usr' => $value];
            }
        }

        return $rows;
        

        return array('tele_data'=>$data,'t_outlet'=>$t_outlet, 'bd_data' => $bd_data);
    }

}
