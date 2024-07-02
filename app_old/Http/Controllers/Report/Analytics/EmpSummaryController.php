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
class EmpSummaryController extends Controller
{
    private $access_key = 'analytics/emp/summary';
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
    
    public function index(){
       $this->db = Auth::user()->country()->cont_conn;
       $date =Carbon::now()->format('Y-m-d');
       $u=Auth::user()->email;
       $emp_id=DB::connection($this->db)->select("SELECT id,role_id FROM tm_aemp WHERE aemp_usnm ='$u'");
       $emid=$emp_id[0]->id;
       $emp_role=2;

       $data = [];
       $employee =DB::connection($this->db)->select("Select * from th_dhbd_5 Where dhbd_date = '2022-11-03' AND aemp_id=8");
       $pillar_data='';
       $gp_wise_class = '';


       if($this->userMenu->wsmu_vsbl==1){
            return view('report.Analytics.EmpSummary.index', ['data' => $data, 'employee' => $employee,
                'pillar_data' => $pillar_data, 'gp_wise_class' => $gp_wise_class]);
        }
        else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

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

       return DB::connection($this->db)->select(" Select  
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
                DAYNAME(t1.date) day_name, t1.date, (t1.v_outlet+t1.s_outet) t_visit,
                t1.ro_visit,(t1.v_outlet+t1.s_outet-t1.ro_visit) wr_visit
                FROM tt_aemp_summary1 t1
                WHERE t1.aemp_id={$aemp_id} AND t1.date between '{$start_date}' AND '{$end_date}'");
    }

    public function getEmpAutoId($aemp_usnm){

        return
        $aemp_id=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->firstOrFail();
    }

    public function getSrEmpSummaryData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;


        return
            $emp_summery = DB::connection($this->db)->select("Select 
                t1.aemp_code aemp_usnm,
                t1.aemp_name,
                SUM(CASE WHEN t1.atten_atyp=1 THEN 1 ELSE 0 END)'present',
                SUM(CASE WHEN t1.atten_atyp=2 THEN 1 ELSE 0 END)'iom',
                SUM(CASE WHEN t1.atten_atyp=3 THEN 1 ELSE 0 END)'leave',
                SUM(CASE WHEN t1.atten_atyp=4 THEN 1 ELSE 0 END)'fc_leave',
                SUM(t1.t_outlet) 't_outlet',
                SUM(t1.v_outlet) 'np_visit',
                SUM(t1.s_outet) 'p_visit',
                SUM(t1.ro_visit) 'ro_visit',
                SUM(t1.t_memo) 't_memo',
                SUM(t1.t_sku) 't_sku',
                round(SUM(t1.t_amnt)/1000,2) 't_amnt',
                round(SUM(t1.t_sku)/if(SUM(t1.t_memo)=0,1,SUM(t1.t_memo)),2) 'lpc',
                round((SUM(t1.v_outlet)+SUM(t1.s_outet))*100/if(SUM(t1.t_outlet)>0,SUM(t1.t_outlet),1),2) 'visit_percent'
                FROM tt_aemp_summary1 t1
                WHERE t1.aemp_code={$request->sr_id} and t1.date between '{$start_date}' AND '{$end_date}'
                GROUP BY t1.aemp_code,
                t1.aemp_name");
    }

    public function getSvEmpSummaryData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;

        return DB::connection($this->db)->select("Select 
                    t3.aemp_usnm aemp_usnm,
                    t3.aemp_name,
                    SUM(t1.t_outlet) 't_outlet',
                    SUM(t1.v_outlet) 'np_visit',
                    SUM(t1.s_outet) 'p_visit',
                    SUM(t1.ro_visit) 'ro_visit',
                    SUM(t1.t_memo) 't_memo',
                    SUM(t1.t_sku) 't_sku',
                    round(SUM(t1.t_amnt)/1000,2) 't_amnt',
                    round(SUM(t1.t_sku)/if(SUM(t1.t_memo)=0,1,SUM(t1.t_memo)),2) 'lpc',
                    round((SUM(t1.v_outlet)+SUM(t1.s_outet))*100/if(SUM(t1.t_outlet)>0,SUM(t1.t_outlet),1),2) 'visit_percent'
                    FROM tt_aemp_summary1 t1
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    INNER JOIN tm_aemp t3 ON t3.id=t2.aemp_mngr
                    WHERE t3.id={$aemp_id} and t1.date between '{$start_date}' AND '{$end_date}'
                    GROUP BY t3.aemp_name,t3.aemp_usnm");
    }

    public function getSrAttendanceData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;

        return
            DB::connection($this->db)->select("Select
                        t1.aemp_code aemp_usnm,
                        t1.aemp_name,
                        SUM(CASE WHEN t1.atten_atyp=1 THEN 1 ELSE 0 END)'present',
                        SUM(CASE WHEN t1.atten_atyp=2 THEN 1 ELSE 0 END)'iom',
                        SUM(CASE WHEN t1.atten_atyp=3 THEN 1 ELSE 0 END)'leave',
                        SUM(CASE WHEN t1.atten_atyp=4 THEN 1 ELSE 0 END)'fc_leave',
                        SUM(t1.t_outlet) 't_outlet',
                        SUM(t1.v_outlet) 'np_visit',
                        SUM(t1.s_outet) 'p_visit',
                        SUM(t1.ro_visit) 'ro_visit',
                        SUM(t1.t_memo) 't_memo',
                        SUM(t1.t_sku) 't_sku',
                        round(SUM(t1.t_amnt)/1000,2) 't_amnt',
                        round(SUM(t1.t_sku)/if(SUM(t1.t_memo)=0,1,SUM(t1.t_memo)),2) 'lpc',
                        round((SUM(t1.v_outlet)+SUM(t1.s_outet))*100/if(SUM(t1.t_outlet)>0,SUM(t1.t_outlet),1),2) 'visit_percent'
                        FROM tt_aemp_summary1 t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        WHERE t2.aemp_mngr={$aemp_id} and t1.date between '{$start_date}' AND '{$end_date}'
                        GROUP BY t1.aemp_code, t1.aemp_name");
    }

    public function getGovtHierarchyCoverage(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return
        $date_wise_details = DB::connection($this->db)->select("SELECT t5.ward_name,t6.than_name,t7.dsct_name,count(distinct t1.site_id)t_outlet
                        FROM `th_ssvh` t1 
                        INNER JOIN tm_site t3 ON t1.site_id=t3.id
                        INNER JOIN tm_mktm t4 ON t3.mktm_id=t4.id
                        INNER JOIN tm_ward t5 ON t4.ward_id=t5.id
                        INNER JOIN tm_than t6 ON t5.than_id=t6.id
                        INNER JOIN tm_dsct t7 ON t6.dsct_id=t7.id
                        where t1.ssvh_date between '{$start_date}' AND '{$end_date}' AND t1.aemp_id={$aemp_id} AND t1.ssvh_ispd in (1,0)
                        GROUP BY t5.ward_name,t6.than_name,t7.dsct_name");
   }

    public function getItemSummary(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;


        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return
        $date_wise_details = DB::connection($this->db)->select("Select amim_code,amim_name,amim_qty,amim_duft,round(sum(amim_amnt)/1000,2) as total_amnt,round(sum(amim_deli)/1000,2) as deli_amnt from (
                        SELECT
                        t3.amim_code,t3.amim_name,sum(t2.ordd_inty)amim_qty,t3.amim_duft,sum(t2.ordd_oamt)amim_amnt,sum(t2.ordd_odat)amim_deli
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
                        WHERE t1.ordm_date between '{$start_date}' AND '{$end_date}' AND t1.aemp_id={$aemp_id}
                        GROUP BY t3.amim_code,t3.amim_name)p
                        GROUP BY amim_code,amim_name");
   }

    public function getDateWiseDetails(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;



        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return
        $date_wise_details = DB::connection($this->db)->select("Select 
                    t1.date,
                    t1.aemp_code aemp_usnm,
                    t1.aemp_name,
                    t1.atten_atyp,
                    t1.t_outlet,
                    t1.v_outlet,
                    t1.s_outet,
                    t1.t_sku,
                    round(t1.t_amnt/1000,2)t_amnt,
                    t1.t_memo,
                    t1.inTime,
                    CASE WHEN t1.lastOrTime>t1.npv_max_time THEN t1.lastOrTime ELSE t1.npv_max_time END AS outTime,
                    TIMEDIFF((CASE WHEN t1.lastOrTime>t1.npv_max_time THEN t1.lastOrTime ELSE t1.npv_max_time END),t1.inTime) working_duration
                    FROM tt_aemp_summary1 t1
                    WHERE t1.aemp_id={$aemp_id} and t1.date between '{$start_date}' AND '{$end_date}'");
   }

    public function getOrderDelivery(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;


        $aemp_id = $this->getEmpAutoId($request->sr_id)->id;

        return
        $odr_del_amount = DB::connection($this->db)->select("Select
                    round(sum(order_amnt)/1000,2) order_amnt,round(sum(deli_amnt)/1000,2)deli_amnt
                    FROM 
                    (
                    Select max(t1.ordm_amnt)order_amnt,sum(t2.ordd_odat)deli_amnt
                    FROM tt_ordm t1
                    INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                    WHERE t1.ordm_date between '{$start_date}' AND '{$end_date}' AND t1.aemp_id={$aemp_id}
                    GROUP BY t1.id
                    )t");
   }

    public function getSrActivityData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;



        $sr_activity = DB::connection($this->db)->select("SELECT 
                 sum(`9am`)9am, sum(`10am`)10am, sum(`11am`)11am, sum(`12pm`)12pm,
                 sum(`1pm`)1pm,sum(`2pm`)2pm,sum(`3pm`)3pm,sum(`4pm`)4pm,sum(`5pm`)5pm,sum(`6pm`)6pm,sum(`7pm`)7pm,
                 sum(`8pm`)8pm,sum(`9pm`)9pm,
                 sum(`9amn`)9amn, sum(`10amn`)10amn, sum(`11amn`)11amn, sum(`12pmn`)12pmn,
                 sum(`1pmn`)1pmn,sum(`2pmn`)2pmn,sum(`3pmn`)3pmn,sum(`4pmn`)4pmn,sum(`5pmn`)5pmn,sum(`6pmn`)6pmn,sum(`7pmn`)7pmn,
                 sum(`8pmn`)8pmn,sum(`9pmn`)9pmn
                FROM tbl_sr_activity_summary t1
                WHERE aemp_usnm={$request->sr_id} AND t1.act_date between '{$start_date}' AND '{$end_date}'");



        if($sr_activity[0]) {

            $info = $sr_activity[0];

            $data = [];


            foreach ($info as $time => $visit) {
                if ($time == '9amn') break;
                $p_visit = $visit;
                $np_index = $time.'n';
                $np_visit = $info->$np_index;
                $t_visit = $p_visit + $np_visit;

                $data[$time] = [$t_visit, $p_visit, $np_visit];
            }

            return $data;
        }


   }

    public function getSvActivityData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;


        $sv_activity = DB::connection($this->db)->select("SELECT 
                     sum(`9am`)9am, sum(`10am`)10am, sum(`11am`)11am, sum(`12pm`)12pm,sum(`1pm`)1pm,sum(`2pm`)2pm,sum(`3pm`)3pm,sum(`4pm`)4pm,sum(`5pm`)5pm,sum(`6pm`)6pm,sum(`7pm`)7pm,
                     sum(`8pm`)8pm,sum(`9pm`)9pm,
                     sum(`9amn`)9amn, sum(`10amn`)10amn, sum(`11amn`)11amn, sum(`12pmn`)12pmn,sum(`1pmn`)1pmn,sum(`2pmn`)2pmn,sum(`3pmn`)3pmn,sum(`4pmn`)4pmn,sum(`5pmn`)5pmn,sum(`6pmn`)6pmn,sum(`7pmn`)7pmn,
                     sum(`8pmn`)8pmn,sum(`9pmn`)9pmn
                    FROM tbl_sr_activity_summary t1
                    INNER JOIN tm_aemp t2 ON t1.id=t2.id
                    WHERE t2.aemp_mngr={$aemp_id} AND t1.act_date between '{$start_date}' AND '{$end_date}'");



        if($sv_activity[0]) {

            $info = $sv_activity[0];

            $data = [];


            foreach ($info as $time => $visit) {
                if ($time == '9amn') break;
                $p_visit = $visit;
                $np_index = $time.'n';
                $np_visit = $info->$np_index;
                $t_visit = $p_visit + $np_visit;

                $data[$time] = [$t_visit, $p_visit, $np_visit];
            }

            return $data;
        }


   }

    public function getSvWorkNoteData(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;

        return
        $sv_note_summary = DB::connection($this->db)->select("Select count(id) 'notes'
                    from tt_note Where aemp_id={$aemp_id} and note_date between '{$start_date}' AND '{$end_date}'");
   }

    public function getSvWorkNoteSummary(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;

        return
        $sv_note_summary = DB::connection($this->db)->select("Select note_date,
                    SUM(CASE WHEN HOUR(note_dtim)<='9' THEN 1 ELSE 0 END) '9amnt',
                    SUM(CASE WHEN HOUR(note_dtim)='10' THEN 1 ELSE 0 END) '10amnt',
                    SUM(CASE WHEN HOUR(note_dtim)='11' THEN 1 ELSE 0 END) '11amnt',
                    SUM(CASE WHEN HOUR(note_dtim)='12' THEN 1 ELSE 0 END) '12pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)='13' THEN 1 ELSE 0 END) '1pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)='14' THEN 1 ELSE 0 END) '14pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)='15' THEN 1 ELSE 0 END) '15pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)='16' THEN 1 ELSE 0 END) '16pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)='17' THEN 1 ELSE 0 END) '17pmnt',
                    SUM(CASE WHEN HOUR(note_dtim)>='18' THEN 1 ELSE 0 END) '18pmnt'
                    from tt_note Where aemp_id={$aemp_id} and note_date between '{$start_date}' AND '{$end_date}'
                    GROUP BY note_date");
   }

    public function getSvThanaCoverage(Request $request){
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $aemp_id = $this->getEmpAutoId($request->sv_id)->id;

        return
        $sv_note_summary = DB::connection($this->db)->select("SELECT t6.than_name,t7.dsct_name,count(distinct t1.site_id)t_outlet
                    FROM `th_ssvh` t1 
                    INNER JOIN tm_site t3 ON t1.site_id=t3.id
                    INNER JOIN tm_mktm t4 ON t3.mktm_id=t4.id
                    INNER JOIN tm_ward t5 ON t4.ward_id=t5.id
                    INNER JOIN tm_than t6 ON t5.than_id=t6.id
                    INNER JOIN tm_dsct t7 ON t6.dsct_id=t7.id
                    INNER JOIN tm_aemp t8 ON t1.aemp_id=t8.id
                    where t1.ssvh_date between '{$start_date}' AND '{$end_date}' AND t8.aemp_mngr={$aemp_id} AND t1.ssvh_ispd in (1,0)
                    GROUP BY t6.than_name,t7.dsct_name;");
   }
   
}
