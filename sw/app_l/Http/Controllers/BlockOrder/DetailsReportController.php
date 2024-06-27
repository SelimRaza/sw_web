<?php

namespace App\Http\Controllers\BlockOrder;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\MasterData\Depot;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\TableRows;

class DetailsReportController extends Controller
{
    private $access_key = 'cpcr_spbm_details/report';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;
    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
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

    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $dlrm=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC");
            $chnl=DB::connection($this->db)->select("Select id,chnl_code,chnl_name FROM tm_chnl WHERE lfcl_id=1 ORDER BY chnl_code ASC");
            $sv_list=DB::connection($this->db)->select("Select id,aemp_usnm,aemp_name FROM tm_aemp WHERE role_id=2 ORDER BY aemp_usnm ASC");
            return view('blockOrder.report',['dlrm_list'=>$dlrm,'chnl_list'=>$chnl,'sv_list'=>$sv_list])->with('permission', $this->userMenu)->with('acmp_list',$acmp);
        } else {
            return view('theme.access_limit');
        }
        
    }

    public function getReportData(Request $request){
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $ordm_ornm=$request->ordm_ornm;
            $site_code=$request->site_code;
            $sr_usnm=$request->sr_usnm;
            $sv_id=$request->sv_id;
            $slgp_id=$request->slgp_id;
            $rpt_type=$request->rpt_type;
            if($rpt_type==2){
                $query='';
                if($ordm_ornm){
                    $query.=" AND t1.ordm_ornm='$ordm_ornm' ";
                }
                if($site_code){
                    $query.=" AND t2.site_code='$site_code' ";
                }
                if($sr_usnm){
                    $query.=" AND t3.aemp_usnm='$sr_usnm' ";
                }
                if($sv_id){
                    $query.=" AND t3.aemp_mngr='$sv_id' ";
                }
                if($slgp_id){
                    $query.=" AND t5.slgp_id='$slgp_id' ";
                }
                // $data=DB::connection($this->db)->select("SELECT 
                //         t5.ORDM_DATE ordm_date,t6.slgp_name,t1.ordm_ornm,t2.site_code,t2.site_name,t1.ordm_amnt,
                //         t1.sreq_amnt,t1.sapr_amnt,t1.spbd_type, t3.aemp_usnm sr_id,t3.aemp_name sr_name,t1.cpcr_cdat,
                //         If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_usnm,'') as sv_id,
                //         If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_name,'') as sv_name,
                //         IF(DELV_AMNT !=COLLECTION_AMNT,'DUE','COLL') c_status,
                //         t7.zone_name,
                //         round(DELV_AMNT-COLLECTION_AMNT,2) due_amnt
                //         FROM `tl_cpcr` t1
                //         INNER JOIN tm_site t2 ON t1.site_id=t2.id
                //         INNER JOIN tm_aemp t3 ON t1.aemp_iusr=t3.id
                //         INNER JOIN tm_aemp t4 ON t1.aemp_eusr=t4.id
                //         INNER JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                //         INNER JOIN tm_slgp t6 ON t5.slgp_id=t6.id
                //         INNER JOIN tm_zone t7 ON t3.zone_id=t7.id
                //         WHERE t5.ORDM_DATE BETWEEN '$start_date' AND '$end_date' ".$query. " ");
                $data=DB::connection($this->db)->select("SELECT 
                        t5.TRIP_NO,t5.ORDM_DATE ordm_date,t6.slgp_name,t1.ordm_ornm,t2.site_code,t2.site_name,t1.ordm_amnt,
                        t1.sreq_amnt,t1.sapr_amnt,t1.scol_amnt,t1.spbd_type, t3.aemp_usnm sr_id,t3.aemp_name sr_name,t1.cpcr_cdat,
                        If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_usnm,'') as apr_id,
                        If(t1.aemp_iusr !=t1.aemp_eusr,t4.aemp_name,'') as apr_name,
                        CASE WHEN SUBSTRING_INDEX(t5.`DELV_AMNT`,'.',1)=SUBSTRING_INDEX(t5.`COLLECTION_AMNT`,'.',1) AND t1.sapr_amnt>0 THEN 'Done' 
                        
                       -- ELSE IF(t1.sapr_amnt>0,'Due','') 
                         ELSE IF(t5.DELV_AMNT-t5.COLLECTION_AMNT>=1,'Due','Done')END c_status,
                        t7.zone_name,
                        round(ROUND(t5.DELV_AMNT,2)-ROUND(t5.COLLECTION_AMNT,2),2) due_amnt,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE='Cash' THEN t9.COLLECTION_AMNT ELSE 0 END),2) Cash,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE='Cheque' THEN t9.COLLECTION_AMNT ELSE 0 END),2) Cheque,
                        ROUND(SUM(CASE WHEN t9.COLLECTION_TYPE='Online' THEN t9.COLLECTION_AMNT ELSE 0 END),2) 'Online',
                        DATE(IF(t9.COLLECTION_TYPE='Cheque', MAX(t9.CHECK_DATE) , '')) check_date,
                        t13.dirg_code,t13.dirg_name,t11.aemp_usnm dm_id,t11.aemp_name dm_name,
                        ROUND(t5.COLLECTION_AMNT,2)COLLECTION_AMNT
                        FROM `tl_cpcr` t1
                        INNER JOIN tm_site t2 ON t1.site_id=t2.id
                        INNER JOIN tm_aemp t3 ON t1.aemp_iusr=t3.id
                        INNER JOIN tm_aemp t4 ON t1.aemp_eusr=t4.id
                        INNER JOIN tm_zone t7 ON t3.zone_id=t7.id
                        INNER JOIN dm_trip_master t5 ON t1.ordm_ornm=t5.ORDM_ORNM
                        INNER JOIN dm_trip t10 ON t5.TRIP_NO=t10.TRIP_NO
                        INNER JOIN tm_aemp t11 ON t10.DM_ID=t11.aemp_usnm
                        INNER JOIN tm_dirg t13 ON t7.dirg_id=t13.id
                        LEFT JOIN  dm_invoice_collection_mapp t8 ON t1.ordm_ornm=t8.TRANSACTION_ID
                        LEFT JOIN dm_collection t9 ON t9.COLL_NUMBER=t8.MAP_ID
                        INNER JOIN tm_slgp t6 ON t5.slgp_id=t6.id
                        WHERE date(t1.created_at) BETWEEN '$start_date' AND '$end_date' ".$query. "
                        GROUP BY t5.ORDM_DATE,t6.slgp_name,t1.ordm_ornm,t2.site_code,t2.site_name,t1.ordm_amnt,
                        t13.dirg_code,t13.dirg_name,t11.aemp_usnm,t11.aemp_name,
                        t5.TRIP_NO,t1.sreq_amnt,t1.scol_amnt,t1.sapr_amnt,t1.spbd_type,t3.aemp_usnm ,t3.aemp_name,t1.cpcr_cdat,t7.zone_name,t5.COLLECTION_AMNT;");
                return $data;
            }
            else if($rpt_type==1){
                $query='';
                if($ordm_ornm){
                    $query.=" AND t3.ordm_ornm='$ordm_ornm' ";
                }
                if($site_code){
                    $query.=" AND t7.site_code='$site_code' ";
                }
                if($sr_usnm){
                    $query.=" AND t4.aemp_usnm='$sr_usnm' ";
                }
                if($sv_id){
                    $query.=" AND t4.aemp_mngr='$sv_id' ";
                }
                if($slgp_id){
                    $query.=" AND t8.id='$slgp_id' ";
                }
                $data=DB::connection($this->db)->select("SELECT t3.ordm_date,t8.slgp_name,t3.ordm_ornm,t7.site_code,t7.site_name,t3.ordm_amnt,
                        t9.spdi sreq_amnt,round(t1.spbd_amnt,2) sapr_amnt,t1.spbd_type,t4.aemp_usnm sr_id,t4.aemp_name sr_name,'' cpcr_cdat,
                        t5.aemp_usnm sv_id,t5.aemp_name sv_name,'' due_amnt,'' c_status,t10.zone_name FROM `tt_spbd` t1
                        INNER JOIN tt_spbm t2 ON t2.id=t1.spbm_id
                        INNER JOIN tt_ordm t3 ON t1.ordm_ornm=t3.ordm_ornm
                        INNER JOIN tm_aemp t4 ON t3.aemp_id=t4.id
                        INNER JOIN tm_aemp t5 ON t2.aemp_id=t5.id
                        INNER JOIN tm_dlrm t6 ON t3.dlrm_id=t6.id
                        INNER JOIN tm_site t7 ON t3.site_id=t7.id
                        INNER JOIN tm_slgp t8 ON t3.slgp_id=t8.id
                        INNER JOIN tm_zone t10 ON t4.zone_id=t10.id
                        INNER JOIN (Select ordm_id,round(sum(ordd_spdi),2)spdi FROM tt_ordd WHERE ordd_spdi>0 GROUP BY ordm_id) t9 ON t3.id=t9.ordm_id
                        WHERE t3.ordm_date between '$start_date' AND '$end_date' ".$query. " ");
                return $data;
            }
            
    }
   

}