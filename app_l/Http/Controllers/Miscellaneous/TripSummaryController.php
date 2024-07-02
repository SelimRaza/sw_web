<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use App\MasterData\SKU;
use Maatwebsite\Excel\Facades\Excel;
class TripSummaryController extends Controller
{
    private $access_key = 'trip_summary';
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
        $empId = $this->aemp_id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
        $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
        $dlrm_list = DB::connection($this->db)->select("SELECT dlrm_code id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $dlrm=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC");
        $sv_list=DB::connection($this->db)->select("Select aemp_usnm id,aemp_name FROM tm_aemp WHERE role_id=10 ORDER BY aemp_usnm ASC");
        return view('miscellaneous.TripSummary.trip_summary_report',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list])
                ->with('sv_list',$sv_list);
    }
    
    public function index_test(){
        $empId = $this->aemp_id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, concat(dirg_code,'-',dirg_name)dirg_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $item_cat = DB::connection($this->db)->select("SELECT id,concat(itsg_code,'-',itsg_name)itsg_name FROM tm_itsg WHERE lfcl_id=1 ORDER BY itsg_name ASC limit 150");
        $scnl_list = DB::connection($this->db)->select("SELECT id,concat(scnl_code,'-',scnl_name)scnl_name FROM tm_scnl WHERE lfcl_id=1 ORDER BY scnl_name ASC LIMIT 200");
        $dlrm_list = DB::connection($this->db)->select("SELECT dlrm_code id,concat(dlrm_code,'-',dlrm_name)dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_name ASC limit 150");
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $dlrm=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC");
        $sv_list=DB::connection($this->db)->select("Select aemp_usnm id,aemp_name FROM tm_aemp WHERE role_id=10 ORDER BY aemp_usnm ASC");
        return view('miscellaneous.TripSummary.trip_summary_report_test',['acmp_list'=>$acmp,'region_list'=>$region,'item_cat'=>$item_cat,'scnl_list'=>$scnl_list,'dlrm_list'=>$dlrm_list])
                ->with('sv_list',$sv_list);
    }
    public function getTripDetailsData(Request $request){
        $start_date     =$request->start_date;
        $end_date       =$request->end_date;
        $sr_usnm        =$request->emp_id;
        $site_code      =$request->site_code;
        $ordm_ornm      =$request->ordm_ornm;
        $trip_no        =$request->trip_no;
        $sv_id          =$request->sv_id;
        $dlrm_id        =$request->dlrm_id;
        $query_param='';
        $employee=$this->getEmpAutoId($sr_usnm);
        $aemp_id=$employee?$employee->id:'';
        $rpt_type=$request->rpt_type;
        Switch($rpt_type){
            case '1':
                $query_params='';
                if($sr_usnm){
                    $query_param.=" AND t2.AEMP_USNM='$sr_usnm'";
                }
                if($sv_id){
                    $query_param.=" AND t1.DM_ID='$sv_id'";
                }
                if($site_code){
                    $query_param.=" AND t2.SITE_CODE='$site_code'";
                }
                if($dlrm_id){
                    $query_param.=" AND t1.DEPOT_ID='$dlrm_id'";
                }
                if($trip_no){
                    $query_param.=" AND t1.TRIP_NO='$trip_no'";
                }
                if($ordm_ornm){
                    $query_param.=" AND t2.ORDM_ORNM='$ordm_ornm'";
                }
                if($start_date && $end_date){
                    $query_param.=" AND t1.TRIP_DATE BETWEEN '$start_date' AND '$end_date'";
                }
                // $trip_date=DB::connection($this->db)->select("SELECT 
                //             t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t4.aemp_name DM_NAME,t1.INVOICE_NUM,t1.SR_NUM,t1.ORDD_AMNT,
                //             t1.INV_AMNT,t1.DELV_AMNT,t1.COLLECTION_AMNT,
                //             ROUND(CASE WHEN t1.DM_ACTIVITY=5 THEN t1.INV_AMNT-t1.DELV_AMNT ELSE 0 END,2)RTAN_AMNT,
                //             ROUND(t1.DELV_AMNT-t1.COLLECTION_AMNT) CRED_AMNT,
                //             ROUND(SUM(t5.GRV_AMNT),2)GRV_AMNT,
                //             IFNULL(ROUND(SUM(t5.GD_GRV),2),0) GD_GRV,IFNULL(ROUND(SUM(t5.BAD_GRV),2),0)BAD_GRV,
                //             t2.dlrm_name DEPOT_NAME,t3.lfcl_name TRIP_STAT,
                //             t1.DELI_NUM,t1.Cash,t1.Cheque,t1.Online
                //             FROM 
                //             (Select 
                //             t1.TRIP_NO,
                //             t1.TRIP_DATE,
                //             t1.DM_ID,
                //             t1.DM_ACTIVITY,t1.DEPOT_ID,
                //             COUNT(t2.ORDM_ORNM) INVOICE_NUM,
                //             COUNT(DISTINCT t2.AEMP_ID) SR_NUM,
                //             SUM(CASE WHEN t2.DELIVERY_STATUS=11 THEN 1 ELSE 0 END ) DELI_NUM,
                //             ROUND(SUM(t2.ORDD_AMNT),2) ORDD_AMNT,
                //             ROUND(SUM(t2.INV_AMNT),2) INV_AMNT,
                //             ROUND(SUM(t2.DELV_AMNT),2)DELV_AMNT,
                //             ROUND(SUM(t2.COLLECTION_AMNT),2)COLLECTION_AMNT,
                //             ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Cash' THEN t4.COLLECTION_AMNT ELSE 0 END),2) Cash,
                //             ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Cheque' THEN t4.COLLECTION_AMNT ELSE 0 END),2) Cheque,
                //             ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Online' THEN t4.COLLECTION_AMNT ELSE 0 END),2) 'Online'
                //             FROM dm_trip t1
                //             INNER JOIN dm_trip_master t2 ON t1.TRIP_NO=t2.TRIP_NO
                //             LEFT JOIN dm_invoice_collection_mapp t3 ON t2.ORDM_ORNM=t3.TRANSACTION_ID
                //             LEFT JOIN dm_collection t4 ON COLL_NUMBER=t3.MAP_ID
                //             WHERE t1.TRIP_DATE BETWEEN '$start_date' AND '$end_date' ". $query_param."
                //             GROUP BY t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t1.DM_ACTIVITY,t1.DEPOT_ID)t1
                //             INNER JOIN tm_dlrm t2 ON t1.DEPOT_ID=t2.dlrm_code
                //             INNER JOIN tm_lfcl t3 ON t1.DM_ACTIVITY=t3.ID
                //             INNER JOIN tm_aemp t4 ON t1.DM_ID=t4.AEMP_USNM
                //             LEFT JOIN
                //             ( SELECT dm_trip, rtan_rtnm,rtan_amnt GRV_AMNT,
                //                 ROUND(SUM(CASE WHEN t6.rtdd_ptyp=1 THEN t6.rtdd_oamt ELSE 0 END),2) BAD_GRV,
                //                 ROUND(SUM(CASE WHEN t6.rtdd_ptyp=2 THEN t6.rtdd_oamt ELSE 0 END),2) GD_GRV
                //                 FROM tt_rtan t5
                //             INNER JOIN tt_rtdd t6 ON t5.rtan_rtnm=t6.rtdd_rtan GROUP BY dm_trip,rtan_rtnm)t5
                //             ON t1.TRIP_NO=t5.dm_trip 
                //             GROUP BY t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t4.aemp_name,t1.INVOICE_NUM,t1.SR_NUM,t1.ORDD_AMNT,
                //             t1.INV_AMNT,t1.DELV_AMNT,t1.COLLECTION_AMNT,t2.dlrm_name,t3.lfcl_name ORDER BY t1.TRIP_DATE ASC;");
                $trip_date=DB::connection($this->db)->select("SELECT 
                            t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t4.aemp_name DM_NAME,t1.INVOICE_NUM,t1.SR_NUM,t1.ORDD_AMNT,
                            t1.INV_AMNT,t1.DELV_AMNT,t1.COLLECTION_AMNT,
                            ROUND(t1.RTAN_AMNT,2) RTAN_AMNT,
                            ROUND(t1.DELV_AMNT-t1.COLLECTION_AMNT) CRED_AMNT,
                            ROUND(SUM(t5.GRV_AMNT),2)GRV_AMNT,
                            IFNULL(ROUND(SUM(t5.GD_GRV),2),0) GD_GRV,IFNULL(ROUND(SUM(t5.BAD_GRV),2),0)BAD_GRV,
                            t2.dlrm_name DEPOT_NAME,t3.lfcl_name TRIP_STAT,
                            t1.DELI_NUM,t1.Cash,t1.Cheque,t1.Online
                            FROM 
                            (Select 
                            t1.TRIP_NO,
                            t1.TRIP_DATE,
                            t1.DM_ID,
                            t1.DM_ACTIVITY,t1.DEPOT_ID,
                            COUNT(t2.ORDM_ORNM) INVOICE_NUM,
                            COUNT(DISTINCT t2.AEMP_ID) SR_NUM,
                            SUM(CASE WHEN t2.DELIVERY_STATUS=11 THEN 1 ELSE 0 END ) DELI_NUM,
                            ROUND(SUM(t2.ORDD_AMNT),2) ORDD_AMNT,
                            ROUND(SUM(t2.INV_AMNT),2) INV_AMNT,
                            ROUND(SUM(t2.DELV_AMNT),2)DELV_AMNT,
                            ROUND(SUM(t2.COLLECTION_AMNT),2)COLLECTION_AMNT,
                            ROUND(SUM(t3.Cash),2) Cash,
                            ROUND(SUM(t3.Cheque),2) Cheque,
                            ROUND(SUM(t3.Online),2) 'Online',
                            SUM(CASE WHEN t2.DELIVERY_STATUS=0 THEN 0 ELSE  IF(ROUND(ROUND(t2.INV_AMNT,2)-ROUND(t2.DELV_AMNT,2),2)<0,0,ROUND(ROUND(t2.INV_AMNT,2)-ROUND(t2.DELV_AMNT,2),2)) END) RTAN_AMNT
                            FROM dm_trip t1
                            INNER JOIN dm_trip_master t2 ON t1.TRIP_NO=t2.TRIP_NO
                            LEFT JOIN 
                            (
                            SELECT  t3.TRANSACTION_ID,
                            ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Cash' THEN t4.COLLECTION_AMNT ELSE 0 END),2) Cash,
                            ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Cheque' THEN t4.COLLECTION_AMNT ELSE 0 END),2) Cheque,
                            ROUND(SUM(CASE WHEN t4.COLLECTION_TYPE='Online' THEN t4.COLLECTION_AMNT ELSE 0 END),2) 'Online'
                            FROM  
                            dm_invoice_collection_mapp t3 
                            LEFT JOIN dm_collection t4 ON COLL_NUMBER=t3.MAP_ID
                            GROUP BY TRANSACTION_ID
                            )t3 ON t2.ORDM_ORNM=t3.TRANSACTION_ID
                            WHERE 1 " .$query_param . "
                            GROUP BY t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t1.DM_ACTIVITY,t1.DEPOT_ID)t1
                            INNER JOIN tm_dlrm t2 ON t1.DEPOT_ID=t2.dlrm_code
                            INNER JOIN tm_lfcl t3 ON t1.DM_ACTIVITY=t3.ID
                            INNER JOIN tm_aemp t4 ON t1.DM_ID=t4.AEMP_USNM
                            LEFT JOIN
                            ( SELECT dm_trip, rtan_rtnm,rtan_amnt GRV_AMNT,
                                ROUND(SUM(CASE WHEN t6.rtdd_ptyp=1 THEN t6.rtdd_oamt ELSE 0 END),2) BAD_GRV,
                                ROUND(SUM(CASE WHEN t6.rtdd_ptyp=2 THEN t6.rtdd_oamt ELSE 0 END),2) GD_GRV
                                FROM tt_rtan t5
                            INNER JOIN tt_rtdd t6 ON t5.rtan_rtnm=t6.rtdd_rtan GROUP BY dm_trip,rtan_rtnm)t5
                            ON t1.TRIP_NO=t5.dm_trip 
                            GROUP BY t1.TRIP_NO,t1.TRIP_DATE,t1.DM_ID,t4.aemp_name,t1.INVOICE_NUM,t1.SR_NUM,t1.ORDD_AMNT,
                            t1.INV_AMNT,t1.DELV_AMNT,t1.COLLECTION_AMNT,t2.dlrm_name,t3.lfcl_name ORDER BY t1.TRIP_DATE ASC");
                return $trip_date;    
                break;
            case '2':
                $trip_details=array();
                $query_params='';
                if($sr_usnm){
                    $query_param.=" AND SR_ID='$sr_usnm'";
                }
                if($sv_id){
                    $query_param.=" AND DM_CODE='$sv_id'";
                }
                if($site_code){
                    $query_param.=" AND SITE_CODE='$site_code'";
                }
                if($dlrm_id){
                    $query_param.=" AND DLRM_CODE='$dlrm_id'";
                }
                if($start_date){
                    $query_param.=" AND TRIP_DATE>='$start_date'";
                }
                if($end_date){
                    $query_param.=" AND TRIP_DATE<='$end_date'";
                }
                if($trip_no){
                    $query_param.=" AND TRIP_NO='$trip_no'";
                }
                if($ordm_ornm){
                    $query_param.=" AND ORDM_ORNM='$ordm_ornm'";
                }
                $trip_master=DB::connection($this->db)->select("
                                SELECT `TRIP_DATE`, `TRIP_NO`,`DLRM_NAME`, `DM_CODE`, `DM_NAME`, `TRIP_STAT_ID`, `TRIP_STAT`,ROUND(SUM(ORDD_AMNT),2)ORDD_AMNT,ROUND(SUM(INV_AMNT),2)INV_AMNT,
                                ROUND(SUM(DELV_AMNT),2)DELV_AMNT,ROUND(SUM(COLLECTION_AMNT),2)COLLECTION_AMNT, ROUND(SUM(RTAN_AMNT),2)RTAN_AMNT,ROUND(SUM(CRED_AMNT),2)CRED_AMNT,COUNT(ORDM_ORNM)C_INVOICE
                                FROM `TRIP_DETAILS` 
                                WHERE 1 ". $query_param. " GROUP BY `TRIP_DATE`, `TRIP_NO`,`DLRM_NAME`, `DM_CODE`, `DM_NAME`, `TRIP_STAT_ID`, `TRIP_STAT`");
                $trip_data=array();
                for($i=0;$i<count($trip_master);$i++){
                    $trip_no=$trip_master[$i]->TRIP_NO;
                    $details=DB::connection($this->db)->select("SELECT *  FROM TRIP_DETAILS WHERE TRIP_NO='$trip_no' " . $query_param. "");

                    $data=array(
                        'TRIP_DATE'=>$trip_master[$i]->TRIP_DATE,
                        'TRIP_NO'=>$trip_master[$i]->TRIP_NO,
                        'DLRM_NAME'=>$trip_master[$i]->DLRM_NAME,
                        'DM_CODE'=>$trip_master[$i]->DM_CODE,
                        'DM_NAME'=>$trip_master[$i]->DM_NAME,
                        'ORDD_AMNT'=>$trip_master[$i]->ORDD_AMNT,
                        'INV_AMNT'=>$trip_master[$i]->INV_AMNT,
                        'DELV_AMNT'=>$trip_master[$i]->DELV_AMNT,
                        'COLLECTION_AMNT'=>$trip_master[$i]->COLLECTION_AMNT,
                        'RTAN_AMNT'=>$trip_master[$i]->RTAN_AMNT,
                        'CRED_AMNT'=>$trip_master[$i]->CRED_AMNT,
                        'C_INVOICE'=>$trip_master[$i]->C_INVOICE,
                        'TRIP_STAT_ID'=>$trip_master[$i]->TRIP_STAT_ID,
                        'TRIP_STAT'=>$trip_master[$i]->TRIP_STAT,
                        'INVOICE_DATA'=>$details
                    );
                    array_push($trip_data,$data);
                
                }
                //         $bst_cnt_s_ldr=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,1]);
                return $trip_data;
                break;
            case '3':$trip_details=array();
//                $trip_data=DB::connection($this->db)->select("CALL getFilterLeaders(?,?,?,?)",[$acmp_id_f,$slgp_id_f,$zone_id_f,1]);
//                $trip_data=DB::connection($this->db)->select("CALL getTripDataAdv(?)",[$ordm_ornm]);
                $trip_data=DB::connection($this->db)->select("CALL getTripDataAdv(?)",[$trip_no]);

                if(count($trip_data) > 0){
                    $dm_id = $trip_data[0]->DM_ID;
                }else{
                    break;
                }

                $other_trip_collection_data=DB::connection($this->db)->select("CALL getOtherTripCollectionAdv(?,?,?)",[$trip_no,$start_date,$dm_id]);
//                $trip_data=DB::connection($this->db)->select("CALL getTripDataAdv('T0500-01-2212000254')");
                return [
                    'trip_data' => $trip_data,
                    'other_trip_collection_data' => $other_trip_collection_data
                ];
            default:
                return 0;
                break;
        }
        //$site_id=$this->getSiteAutoId($site_code);
        
    }
    public function getEmpAutoId($aemp_usnm){
        return Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
    }
    public function getSiteAutoId($site_code){
        $site= Site::on($this->db)->where(['site_code'=>$site_code])->first();
        return $site?$site->id:'';
    }

   
}
