<?php

namespace App\Http\Controllers\Order;

use App\BusinessObject\AppMenuGroup;
use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\TsmGroupZoneMapping;
use App\DataExport\EmployeeMenuGroup;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Depot;
use App\MasterData\DepotEmployee;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\MasterData\PJP;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Zone;
use App\MasterData\RoutePlan;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\MasterData\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\MasterData\Site;
use Image;
use AWS;
use Excel;
use Response;
use App\MasterData\WebOrder;
use App\MasterData\WebOrder1;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use DateTime;

class InvoiceDeliveryController extends Controller
{
    private $access_key = 'report/invoice-delivery';
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
            $this->aemp_id = Auth::user()->employee()->id;
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
        $empId = $this->aemp_id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
        $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
        $dlrm_list = DB::connection($this->db)->select("SELECT id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $dlrm=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC");
        $sv_list=DB::connection($this->db)->select("Select id,aemp_usnm,aemp_name FROM tm_aemp WHERE role_id=2 ORDER BY aemp_usnm ASC");
        return view('Order.Report.invoice_delivery_report',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list])
                ->with('sv_list',$sv_list);
    }
    public function getInvoiceDeliveryGrvData(Request $request){
        $start_date     =$request-> start_date;
        $end_date       =$request-> end_date;
        $slgp_id        =$request-> slgp_id;
        $acmp_id        =$request->acmp_id;
        $sr_usnm        =$request-> emp_id;
        $site_code      =$request-> site_code;
        $ordm_ornm      =$request-> ordm_ornm;
        $rpt_type       =$request-> rpt_type;
        $sv_id          =$request-> sv_id;
        $dlrm_id        =$request-> dlrm_id;
        $query_param='';
        $employee=$this->getEmpAutoId($sr_usnm);
        $aemp_id=$employee?$employee->id:'';
        $site_id=$this->getSiteAutoId($site_code);
        if($rpt_type==3){
            $report_name='INVOICE_DETAILS('.$start_date.'-TO-'.$end_date.')';
            if($acmp_id){
                $query_param.=" AND t1.acmp_id=$acmp_id";
            }
            if($slgp_id){
                $query_param.=" AND t1.slgp_id=$slgp_id";
            }
            if($aemp_id){
                $query_param.=" AND t1.aemp_id=$aemp_id";
            }
            if($site_id){
                $query_param.=" AND t1.site_id=$site_id";
            }
            if($ordm_ornm){
                $query_param.=" AND t1.ordm_ornm='$ordm_ornm' ";
            }
            if($sv_id){
                $query_param.=" AND t4.aemp_mngr=$sv_id";
            }
            if($dlrm_id){
                $query_param.=" AND t1.dlrm_id=$dlrm_id";
            }
            $data_query="SELECT t1.ordm_date ORDER_DATE,t1.ordm_ornm ORDER_NO,
                        t5.slgp_name GROUP_NAME,
                        t7.dlrm_name DEPO_NAME,
                        t6.aemp_usnm MANAGER_ID,
                        t6.aemp_name MANAGER_NAME,
                        t4.aemp_usnm SR_ID,
                        t4.aemp_name SR_NAME,
                        t9.site_code OUTLET_CODE,
                        t9.site_name OUTLET_NAME,
                        t3.amim_code ITEM_CODE,
                        t3.amim_name ITEM_NAME,
                        t3.amim_duft ITEM_FACTOR,
                        round(sum(t2.ordd_opds +t2.ordd_dfdo),2) as DISCOUNT,
                        round(sum(t2.ordd_spdi),2) SP_DISC_REQ,
                        round(sum(t2.ordd_spdc),2) SP_DISC_APPR,
                        round(sum(t2.ordd_inty),2) ORDER_QTY,
                        round(sum(t2.ordd_dqty),2) DELI_QTY,
                        round(SUM(t2.ordd_oamt),2) as ORDER_AMNT,
                        round(SUM(t2.ordd_odat),2) as DELI_AMNT,
                        t8.lfcl_name ORDER_STATUS
                        FROM `tt_ordm` t1 
                        INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id 
                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id 
                        INNER JOIN tm_slgp t5 ON t1.slgp_id=t5.id
                        INNER JOIN tm_aemp t6 ON t6.id=t4.aemp_mngr
                        INNER JOIN tm_dlrm t7 ON t1.dlrm_id=t7.id
                        INNER JOIN tm_lfcl t8 ON t1.lfcl_id=t8.id
                        INNER JOIN tm_site t9 ON t1.site_id=t9.id
                        WHERE t1.ordm_date BETWEEN ''$start_date'' AND ''$end_date'' " .$query_param ."
                        GROUP BY t1.ordm_date,t1.ordm_ornm,
                        t5.slgp_name,
                        t7.dlrm_name,
                        t6.aemp_usnm,
                        t6.aemp_name,
                        t4.aemp_usnm,
                        t4.aemp_name,
                        t9.site_code,
                        t9.site_name,
                        t3.amim_code,
                        t3.amim_name,
                        t3.amim_duft,t8.lfcl_name
                        ORDER BY t1.ordm_date ASC;";
            $aemp_name=Auth::user()->employee()->aemp_name;
            $aemp_usnm=Auth::user()->employee()->aemp_usnm;
            $aemp_emal=Auth::user()->employee()->aemp_emal;
            $aemp_id=Auth::user()->employee()->id;
            $cont_conn=$this->db;
            DB::connection($this->db)->select("INSERT INTO `tbl_report_request`(`report_name`, `report_heading_query`, `report_data_query`,
                                                `cont_conn`, `aemp_id`, `aemp_usnm`, `aemp_name`, `aemp_email`, `report_link`, `report_status`) 
                                                VALUES ('$report_name',2,'$data_query','$cont_conn','$aemp_id','$aemp_usnm','$aemp_name','$aemp_emal','',1)");
            return array('data'=>0,'flag'=>0);
        }
        if($slgp_id || $aemp_id ||$site_id || $ordm_ornm || $sv_id || $dlrm_id){
            if($rpt_type==1){
                if($acmp_id){
                    $query_param.=" AND t1.acmp_id=$acmp_id";
                }
                if($slgp_id){
                    $query_param.=" AND t1.slgp_id=$slgp_id";
                }
                if($aemp_id){
                    $query_param.=" AND t1.aemp_id=$aemp_id";
                }
                if($site_id){
                    $query_param.=" AND t1.site_id=$site_id";
                }
                if($ordm_ornm){
                    $query_param.=" AND t1.ordm_ornm='$ordm_ornm' ";
                }
                if($sv_id){
                    $query_param.=" AND t2.aemp_mngr=$sv_id";
                }
                if($dlrm_id){
                    $query_param.=" AND t1.dlrm_id=$dlrm_id";
                }
                $data=DB::connection($this->db)->select("SELECT t1.ordm_date ORDER_DATE,t1.ordm_ornm ORDER_NO,t8.slgp_name GROUP_NAME,
                    t6.site_code OUTLET_CODE,t6.site_name OUTLET_NAME,t2.aemp_usnm SR_ID,
                    t2.aemp_name SR_NAME, t3.aemp_usnm MANAGER_ID,t3.aemp_name MANAGER_NAME,
                    t4.dlrm_name DEPO_NAME,round(sum(t1.ordm_amnt),2)ORDER_AMNT,
                    round(SUM(t7.deli_amnt),2) DELI_AMNT,t5.lfcl_name ORDER_STATUS 
                    FROM tt_ordm t1 
                    INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id 
                    INNER JOIN tm_aemp t3 ON t2.aemp_mngr=t3.id INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id 
                    INNER JOIN tm_lfcl t5 ON t1.lfcl_id=t5.id INNER JOIN tm_site t6 ON t1.site_id=t6.id 
                    INNER JOIN 
                        ( SELECT ordm_id,sum(ordd_oamt)ord_amnt,sum(ordd_odat)deli_amnt 
                                FROM tt_ordd GROUP BY ordm_id 
                        )t7 ON t1.id=t7.ordm_id 
                    INNER JOIN tm_slgp t8 ON t1.slgp_id=t8.id 
                    WHERE t1.ordm_date between '$start_date' AND '$end_date' " .$query_param ."
                    GROUP BY t1.ordm_date,t6.site_code,t6.site_name,t2.aemp_usnm,
                    t2.aemp_name,t3.aemp_usnm,t3.aemp_name, t4.dlrm_name,t5.lfcl_name,t8.slgp_name,t1.ordm_ornm ORDER BY t1.ordm_date ASC");
            }
            else{
                if($acmp_id){
                    $query_param.=" AND t1.acmp_id=$acmp_id";
                }
                if($slgp_id){
                    $query_param.=" AND t1.slgp_id=$slgp_id";
                }
                if($aemp_id){
                    $query_param.=" AND t1.aemp_id=$aemp_id";
                }
                if($site_id){
                    $query_param.=" AND t1.site_id=$site_id";
                }
                if($sv_id){
                    $query_param.=" AND t2.aemp_mngr=$sv_id";
                }
                if($dlrm_id){
                    $query_param.=" AND t1.dlrm_id=$dlrm_id";
                }
                $data=DB::connection($this->db)->select("SELECT t1.rtan_date GRV_DATE,t1.rtan_rtnm GRV_NO,t6.slgp_name GROUP_NAME,
                        t2.aemp_usnm SR_ID,t2.aemp_name SR_NAME, t3.aemp_usnm MANAGER_ID,t3.aemp_name MANAGER_NAME,
                        t4.dlrm_name DEPO_NAME, t5.site_code OUTLET_CODE,t5.site_name OUTLET_NAME, 
                        round(sum(t1.rtan_amnt),2) GRV_AMNT, t7.rttp_name GRV_STATUS 
                        FROM `tt_rtan` t1 
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id 
                        INNER JOIN tm_aemp t3 ON t2.aemp_mngr=t3.id 
                        INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
                        INNER JOIN tm_site t5 ON t1.site_id=t5.id 
                        INNER JOIN tm_slgp t6 ON t1.slgp_id=t6.id 
                        INNER JOIN tm_rttp t7 ON t1.rttp_id=t7.id 
                        WHERE t1.rtan_date between '$start_date' AND '$end_date' " .$query_param ."
                        GROUP BY t1.rtan_date,t6.slgp_name,t2.aemp_usnm,t2.aemp_name,t3.aemp_usnm,
                        t3.aemp_name,t4.dlrm_name, t5.site_code,t7.rttp_name,t1.rtan_rtnm ORDER BY t1.rtan_date ASC;");
            }
            
            return array('data'=>$data,'flag'=>1);

        }
        else{
            $report_name='';
            $data_query='';
            if($rpt_type==1){
                $report_name='INVOICE_DELIVERY_DATA('.$start_date.'-TO-'.$end_date.')';
                $data_query="SELECT t1.ordm_date ORDER_DATE,t1.ordm_ornm ORDER_NO,t8.slgp_name GROUP_NAME,
                            t6.site_code OUTLET_CODE,t6.site_name OUTLET_NAME,t2.aemp_usnm SR_ID,
                            t2.aemp_name SR_NAME, t3.aemp_usnm MANAGER_ID,t3.aemp_name MANAGER_NAME,
                            t4.dlrm_name DEPO_NAME,round(sum(t1.ordm_amnt),2)ORDER_AMNT,
                            round(SUM(t7.deli_amnt),2) DELI_AMNT,t5.lfcl_name ORDER_STATUS 
                            FROM tt_ordm t1 
                            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id 
                            INNER JOIN tm_aemp t3 ON t2.aemp_mngr=t3.id INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id 
                            INNER JOIN tm_lfcl t5 ON t1.lfcl_id=t5.id INNER JOIN tm_site t6 ON t1.site_id=t6.id 
                            INNER JOIN 
                                ( SELECT ordm_id,sum(ordd_oamt)ord_amnt,sum(ordd_odat)deli_amnt 
                                        FROM tt_ordd GROUP BY ordm_id 
                                )t7 ON t1.id=t7.ordm_id 
                            INNER JOIN tm_slgp t8 ON t1.slgp_id=t8.id 
                            WHERE t1.ordm_date between ''$start_date'' AND ''$end_date''
                            GROUP BY t1.ordm_date,t6.site_code,t6.site_name,t2.aemp_usnm,
                            t2.aemp_name,t3.aemp_usnm,t3.aemp_name, t4.dlrm_name,t5.lfcl_name,t8.slgp_name,t1.ordm_ornm ORDER BY t1.ordm_date ASC";
            }
            else{
                $report_name='GRV_DATA('.$start_date.'-TO-'.$end_date.')';
                $data_query=" SELECT t1.rtan_date GRV_DATE,t1.rtan_rtnm GRV_NO,t6.slgp_name GROUP_NAME,
                                t2.aemp_usnm SR_ID,t2.aemp_name SR_NAME, t3.aemp_usnm MANAGER_ID,t3.aemp_name MANAGER_NAME,
                                t4.dlrm_name DEPO_NAME, t5.site_code OUTLET_CODE,t5.site_name OUTLET_NAME, 
                                round(sum(t1.rtan_amnt),2) GRV_AMNT, t7.rttp_name GRV_STATUS 
                                FROM `tt_rtan` t1 
                                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id 
                                INNER JOIN tm_aemp t3 ON t2.aemp_mngr=t3.id 
                                INNER JOIN tm_dlrm t4 ON t1.dlrm_id=t4.id
                                INNER JOIN tm_site t5 ON t1.site_id=t5.id 
                                INNER JOIN tm_slgp t6 ON t1.slgp_id=t6.id 
                                INNER JOIN tm_rttp t7 ON t1.rttp_id=t7.id 
                                WHERE t1.rtan_date between ''$start_date'' AND ''$end_date''
                                GROUP BY t1.rtan_date,t6.slgp_name,t2.aemp_usnm,t2.aemp_name,t3.aemp_usnm,
                                t3.aemp_name,t4.dlrm_name, t5.site_code,t7.rttp_name,t1.rtan_rtnm ORDER BY t1.rtan_date ASC;";
            }
            
            $aemp_name=Auth::user()->employee()->aemp_name;
            $aemp_usnm=Auth::user()->employee()->aemp_usnm;
            $aemp_emal=Auth::user()->employee()->aemp_emal;
            $aemp_id=Auth::user()->employee()->id;
            $cont_conn=$this->db;
            DB::connection($this->db)->select("INSERT INTO `tbl_report_request`(`report_name`, `report_heading_query`, `report_data_query`,
                                                `cont_conn`, `aemp_id`, `aemp_usnm`, `aemp_name`, `aemp_email`, `report_link`, `report_status`) 
                                                VALUES ('$report_name',2,'$data_query','$cont_conn','$aemp_id','$aemp_usnm','$aemp_name','$aemp_emal','',1)");
            return array('data'=>0,'flag'=>0);
            
        }   
    }
    public function getEmpAutoId($aemp_usnm){
        return Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
    }
    public function getSiteAutoId($site_code){
        $site= Site::on($this->db)->where(['site_code'=>$site_code])->first();
        return $site?$site->id:'';
    }
    public function getRequestedReport(){
        $aemp_id=Auth::user()->employee()->id;
        $data=DB::connection($this->db)->select("Select * from tbl_report_request WHERE date(created_at)>curdate()-Interval 3 DAY  AND aemp_id={$aemp_id} ORDER BY created_at DESC");
        return $data;
    }


}
