<?php

namespace App\Http\Controllers\Report\Analytics;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
class SRAmolnamaController extends Controller
{
    private $access_key = 'analytics/sr/amolnama';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function srAmolnama()
   {
       if($this->userMenu->wsmu_vsbl==1){
           return view('report.Analytics.EmpSummary.sr_amolnama');
       }
       else {
           return redirect()->back()->with('danger', 'Access Limited');
       }
   }

    public function getSrAmolnamaData(Request $request)
   {
       return DB::connection($this->db)->select("Select
                 t4.id,t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t2.slgp_name,t3.acmp_name,concat(t4.zone_code,'-',t4.zone_name)zone_name
                 FROM tm_aemp t1
                 INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
                 INNER JOIN tm_acmp t3 ON t2.acmp_id=t3.id
                 INNER JOIN tm_zone t4 ON t1.zone_id=t4.id
                 WHERE t1.aemp_usnm={$request->sr_id}");
   }

    public function getSrInformation(Request $request)
   {
       $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

       return DB::connection($this->db)->select("Select
                     t1.rpln_day,t3.rout_code,t3.rout_name,count(DISTINCT t2.site_id)t_site
                     FROM tl_rpln t1
                     INNER JOIN tl_rsmp t2 ON t1.rout_id=t2.rout_id
                     INNER JOIN tm_rout t3 ON t1.rout_id=t3.id
                     WHERE t1.aemp_id={$aemp_id}
                     GROUP BY  t1.rpln_day,t3.rout_code,t3.rout_name
                     ORDER BY FIELD(t1.rpln_day,'SATURDAY', 'SUNDAY','MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY')");
   }

    public function getSrSku(Request $request)
    {
       $slgp_id = $this->getEmpAutoId($request->sr_id)->slgp_id;

       return DB::connection($this->db)->select("Select count(distinct amim_id) as t_sku from tl_sgit WHERE slgp_id={$slgp_id}");
    }

    public function getSrTotalCategory(Request $request)
    {
        $slgp_id = $this->getEmpAutoId($request->sr_id)->slgp_id;

        return DB::connection($this->db)->select("Select
                COUNT(DISTINCT t4.itcg_id) total_category
                FROM tl_sgit t2
                INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
                WHERE t2.slgp_id={$slgp_id}");
    }

    public function getSrOrderCategory(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return DB::connection($this->db)->select("Select
                count(distinct t4.itcg_id) order_category
                FROM tt_ordm t1
                INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
                WHERE t1.aemp_id={$aemp_id} and t1.ordm_date between '{$start_date}' AND '{$end_date}'");

    }

    public function getSrOrderSku(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp = $this->getEmpAutoId($request->sr_id);

        $aemp_id = $aemp->id;
        $cont_id = $aemp->cont_id;

        if($cont_id == 2 || $cont_id==5) {
            return DB::connection($this->db)->select("select count(distinct amim_id) sku_cov FROM tbl_itm_cov_details
                                       WHERE aemp_id={$aemp_id} AND ordm_date between '{$start_date}' AND '{$end_date}'");
        }else{
            return DB::connection($this->db)->select("SELECT
                count(distinct t2.amim_id) sku_cov
                FROM (Select t1.id,t1.aemp_id from tt_ordm t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                                              WHERE t1.ordm_date between '{$start_date}' AND '{$end_date}' and aemp_id={$aemp_id})t1
                INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id");
        }
    }

    public function getSrCategoryWiseVisit(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        $data = DB::connection($this->db)->select("Select
                    t3.id, t3.otcg_name,count(t1.site_id) t_site
                    FROM th_ssvh t1
                    INNER JOIN tm_site t2 ON t1.site_id=t2.id
                    INNER JOIN tm_otcg t3 ON t2.otcg_id=t3.id
                    WHERE t1.ssvh_date between '{$start_date}' AND '{$end_date}' and aemp_id={$aemp_id} AND t1.ssvh_ispd in (1,0)
                    GROUP BY t3.otcg_name, t3.id");

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'employee_id' => $aemp_id,
            'info' => $data,
        ];
    }


    public function getSrCategoryWiseVisitDetails(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $aemp_id    = $request->employee_id;
        $otcg_id    = $request->outlet_category_id;

//                     ,t2.site_adrs
        return DB::connection($this->db)->select("Select
                    t2.site_code,t2.site_name,t2.site_mob1
                     ,count(t1.id) frequency
                    FROM th_ssvh t1
                    INNER JOIN tm_site t2 ON t1.site_id=t2.id
                    WHERE t1.ssvh_date between '{$start_date}' AND '{$end_date}' and aemp_id={$aemp_id} AND t2.otcg_id={$otcg_id} AND t1.ssvh_ispd in (1,0)
                    GROUP BY t2.site_code,t2.site_name,t2.site_mob1,t2.site_adrs");
    }

    public function getSrAssignedThanaUnion(Request $request)
    {
        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return DB::connection($this->db)->select("Select
                    COUNT(DISTINCT t5.than_id)t_thana, COUNT(DISTINCT t5.id) t_ward, COUNT(DISTINCT t2.site_id) t_site
                    FROM tl_rpln t1
                    INNER JOIN tl_rsmp t2 ON t1.rout_id=t2.rout_id
                    INNER JOIN tm_site t3 ON t2.site_id=t3.id
                    INNER JOIN tm_mktm t4 ON t3.mktm_id=t4.id
                    INNER JOIN tm_ward t5 ON t4.ward_id=t5.id
                    WHERE t1.aemp_id={$aemp_id}");
    }

    public function getSrVisitedThanaUnion(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return DB::connection($this->db)->select("Select
                COUNT(DISTINCT t4.than_id)t_thana, COUNT(DISTINCT t4.id) t_ward, COUNT(DISTINCT t1.site_id) t_site
                FROM th_ssvh t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_mktm t3 ON t2.mktm_id=t3.id
                INNER JOIN tm_ward t4 ON t3.ward_id=t4.id
                WHERE t1.aemp_id={$aemp_id} AND t1.ssvh_date between '{$start_date}' AND '{$end_date}'");
    }

    public function getSrDayWiseVisitOutlet(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return DB::connection($this->db)->select("Select
                t2.c_day day_name, t2.c_date 'date',ifnull(t1.t_outlet,0)t_outlet, ifnull((t1.v_outlet+t1.s_outet),0) t_visit,
                ifnull(t1.ro_visit,0)ro_visit,ifnull((t1.v_outlet+t1.s_outet-t1.ro_visit),0) wr_visit
                FROM tm_clndr t2
                LEFT JOIN (Select * from tt_aemp_summary1 where aemp_id={$aemp_id} and date between '{$start_date}' AND '{$end_date}') t1 ON t2.c_date=t1.date
                WHERE  t2.c_date between '{$start_date}' AND '{$end_date}'
                GROUP BY t2.c_date,t1.v_outlet,t1.s_outet, t1.ro_visit, t2.c_day,t1.t_outlet
               ORDER BY t2.c_date ASC");
    }

    public function getEmpAutoId($aemp_usnm){

        return
        $aemp_id=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->firstOrFail();
    }
    public function getSRAmolnamaVisitMapData(Request $request){
        $date=$request->date;
        $sr_id=$request->sr_id;
        $sr_id=$this->getEmpAutoId($sr_id);
        $sr_id=$sr_id->id;
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $data=DB::connection($this->db)->select("Select 
                t2.site_code,t2.site_name,t2.site_mob1,t2.site_adrs,t2.geo_lat lat,t2.geo_lon lon,t1.ssvh_ispd
                FROM th_ssvh t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.ssvh_date = '$date' and aemp_id=$sr_id  AND t1.ssvh_ispd in (1,0)
                GROUP BY t2.site_code,t2.site_name,t2.site_mob1,t2.site_adrs,t2.geo_lat,t2.geo_lon,t1.ssvh_ispd
                ORDER BY t1.ssvh_date,t1.created_at ASC;");
        return $data;
    }

}
