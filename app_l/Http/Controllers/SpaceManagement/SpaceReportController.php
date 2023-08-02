<?php

namespace App\Http\Controllers\SpaceManagement;

use AWS;
use Image;
use Response;
use Carbon\Carbon;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Site;
use App\MasterData\Ward;
use App\MasterData\Zone;
use App\MasterData\Thana;
use App\MasterData\Market;
use App\MasterData\District;
use App\MasterData\Division;
use Illuminate\Http\Request;
use App\MasterData\GovtDivision;
use App\BusinessObject\SpaceSite;
use App\BusinessObject\SpaceZone;
use App\MasterData\NoOrderReason;
use App\MasterData\OutletCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\BusinessObject\SpaceMaintain;
use App\DataExport\SiteMappingWithSpace;
use App\BusinessObject\SpaceMaintainFreeItem;
use App\BusinessObject\SpaceMaintainShowcase;
use App\BusinessObject\SpaceMaintainFreeAmount;


class SpaceReportController extends Controller
{
    private $access_key = 'maintain/space';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(80000000);
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index(Request $request){
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
        $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");     
        $results = [];
        $role_id=Auth::user()->employee()->role_id;
        $user_role_id=DB::select("SELECT id,role_name from tm_role where id='$role_id'");
        $slgpList=DB::connection($this->db)->select("Select id,slgp_code,slgp_name FROM tm_slgp ORDER BY slgp_code ASC");
        $spaces = DB::connection($this->db)->select("Select id, spcm_name, spcm_code From tm_spcm");
        // dd($spaces);
        
        return view('SpaceManagement.report.search_report',
            [
                'spaces'=>$spaces,
                'acmp'=>$acmp,
                'region'=>$region,
                'dsct'=>$dsct,
                'dsct1'=>$dsct,
                'role_id'=>$user_role_id,
                'emid'=>$empId,
                'zone'=>$zone,
                'slgpList' => $slgpList,
                'permission' => $this->userMenu,
            ]);
    }

    public function reportFilter(Request $request)
    {
        $reportType=$request->reportType;
        $data = array();
        switch($reportType){
            case "summary_space_report":
                $where = '';
                if (isset($request->acmp_id)) {
                    $where .= " t3.id = '$request->acmp_id'";
                }
                if ($request->acmp_id != '') {
                    if (isset($request->slgp_id)) {
                        $where .= " AND  t2.id = '$request->slgp_id'";
                    } 
                }
                if($request->zone_id != ''){
                    // $where .= " AND  t4.id = '$request->zone_id'";
                }

                if($request->space_id != ''){
                    $where .= " AND  t1.id = '$request->space_id'";
                }

                // $where .= ' AND DATE(spcm_edat) >= CURDATE()';

                $query = "SELECT 
                            t1.id, t1.spcm_name, t1.spcm_code, 
                            t1.spcm_sdat, t1.spcm_edat, t1.lfcl_id, 
                            t2.slgp_name, t2.slgp_code,
                            t3.acmp_name, t3.acmp_code,
                            t4.lfcl_name
                            FROM tm_spcm AS t1 
                            INNER JOIN tm_slgp AS t2 ON t1.spcm_slgp = t2.id 
                            INNER JOIN tm_acmp AS t3 ON t2.acmp_id = t3.id 
                            INNER JOIN tm_lfcl AS t4 ON t1.lfcl_id = t4.id 
                            WHERE ".$where." GROUP BY t1.id, t1.spcm_name, t1.spcm_code";

                DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                $data = DB::connection($this->db)->select(DB::raw($query));

                return $data;
                break;
            case "statistics_space_report":

                $site_cond = '';
                $site = '';
                if($request->site_code != ''){
                    $site = DB::connection($this->db)->select("select id, site_name, site_code from tm_site where site_code = {$request->site_code}");
                    if($site){
                        $space_site = DB::connection($this->db)->select("select * from tl_spst where site_id = {$site[0]->id}");
                        $id = $site[0]->id;
                        $site_cond .= " t1.site_id = $id";
                    }
                    
                }
                $where = '';

                if($site_cond != ''){
                    if($request->space_id != ''){
                        $where .= " $site_cond AND t1.spcm_id = $request->space_id";
                    }else{
                        $where .= $site_cond;
                    }
                }else{
                    if($request->space_id != ''){
                        $where .= "t1.spcm_id = $request->space_id";
                    }else{

                        $where .= " status = 1";
                    }
                }
                
                if($request->zone_id != ''){
                    $where .= " AND  t1.attr3 = $request->zone_id";
                }

                // return $where;
                

                // $where .= ' AND DATE(spcm_edat) >= CURDATE()';

                 $query = "SELECT 
                            t1.id, t1.spcm_id, t1.site_mob1, t1.acc_type, t1.status, t1.apply_date, t1.apply_image,
                            t2.spcm_name, t2.spcm_code,
                            t4.site_name, t4.site_code,
                            t5.lfcl_name,
                            t6.zone_name, t6.zone_code
                            FROM tl_spst AS t1 
                            INNER JOIN tm_spcm AS t2 ON t1.spcm_id = t2.id 
                            INNER JOIN tm_site AS t4 ON t4.id = t1.site_id 
                            INNER JOIN tm_lfcl AS t5 ON t1.lfcl_id = t5.id 
                            INNER JOIN tm_zone AS t6 ON t1.attr3 = t6.id 
                            WHERE ".$where." GROUP BY t1.id, t1.spcm_id, t2.spcm_name, t2.spcm_code";

                DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
                $data = DB::connection($this->db)->select(DB::raw($query));
                
                return $data;
                break;
            default:
                return $data;
                break;
        }
        
    }

    public function reportDetails($id)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $space = SpaceMaintain::on($this->db)->with('saleGroup')->findOrFail($id, ['id', 'spft_id',
                'spcm_name', 'spft_amnt', 'spcm_code', 'spcm_sdat', 'spcm_exdt', 'spcm_qyfr', 'spcm_imge']);

            $showcases = [];
            if(count($space->showcases)>0) {
                $showcases = DB::connection($this->db)->select("select amim_name, amim_code, min_qty
                            from tl_spsb t1 
                            inner join tm_amim t2 on t2.id = t1.amim_id
                            where t1.spcm_id={$space->id}");
            }

            $zones = [];
            if(count($space->zones)>0) {
                $zones = DB::connection($this->db)->select("select t1.id, t1.lfcl_id, lfcl_name, zone_name, zone_code, is_national, max_approve,
                            SUM(CASE WHEN t4.status=1 THEN 1 ELSE 0 END ) 'running',
                            SUM(CASE WHEN t4.lfcl_id=1 THEN 1 ELSE 0 END ) 'approved'
                            from tl_spaz t1 
                            inner join tm_zone t2 on t2.id = t1.zone_id
                            inner join tm_lfcl t3 on t3.id = t1.lfcl_id
                            left join tl_spst t4 on t4.attr3 = t1.zone_id
                            where t1.spcm_id={$space->id}  group by t1.id, t1.lfcl_id, lfcl_name, zone_name, zone_code, is_national, max_approve");
            }

            // dd($zones);

            $free_items = [];
            if(count($space->freeItems)>0) {
                $free_items = DB::connection($this->db)->select("select distinct amim_name, amim_code, min_qty
                            from tl_spft t1 
                            inner join tm_amim t2 on t2.id = t1.amim_id
                            where t1.spcm_id={$space->id}");
            }

            $free_amounts = [];
            if(count($space->freeAmounts)>0) {
                $free_amounts = DB::connection($this->db)->select("select zone_name, zone_code, max_amnt, zone_id
                            from tl_spam t1 
                            left join tm_zone t2 on t2.id = t1.zone_id
                            where t1.spcm_id={$space->id}");
            }

            $sites = [];
            if(count($space->sites) > 0) {
                $count_status = 0;
                $count_lfcl = 0;
            }

            return view('SpaceManagement.report.details', [
                'permission'    => $this->userMenu,
                'space'         => $space,
                'showcases'     => count($showcases) > 0 ? collect($showcases) : [],
                'free_items'    => count($free_items) > 0 ? collect($free_items) : [],
                'free_amounts'  => count($free_amounts) > 0 ? collect($free_amounts) : [],
                'zones'         => count($zones) > 0 ? collect($zones) : []
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function reportShow($id)
    {
        if ($this->userMenu->wsmu_vsbl) {

            $outlet = DB::connection($this->db)->select("select t1.id, t1.spcm_id, t1.site_id, 
                    t2.id, t2.mktm_id, t2.otcg_id, t2.site_name, t2.site_code, t2.site_adrs, t2.site_mob1,
                    t3.ward_id, t3.mktm_name, t3.mktm_code,
                    t4.than_id, t4.ward_name, t4.ward_code,
                    t5.dsct_id, t5.than_name, t5.than_code,
                    t6.disn_id, t6.dsct_name, t6.dsct_code,
                    t7.disn_name, t7.disn_code,
                    t8.otcg_name, t8.otcg_code
                    from tl_spst t1 
                    inner join tm_site t2 on t1.site_id = t2.id 
                    inner join tm_mktm t3 on t2.mktm_id = t3.id 
                    inner join tm_ward t4 on t3.ward_id = t4.id 
                    inner join tm_than t5 on t4.than_id = t5.id 
                    inner join tm_dsct t6 on t5.dsct_id = t6.id 
                    inner join tm_disn t7 on t6.disn_id = t7.id 
                    inner join tm_otcg t8 on t2.otcg_id = t8.id 
                    where t1.id={$id}");
            
            $site_id = $outlet[0]->site_id;
            $spcm_id = $outlet[0]->spcm_id;
            
            // $sale_details = DB::connection($this->db)->select("select t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt
            //                     from tt_ordm t1 
            //                     where t1.site_id={$site_id}");

            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            $sale_details = DB::connection($this->db)->select("SELECT 
                                t1.MONTH_NAME, t1.YEAR_NO,t1.MONTH_NO,t1.ORDER_AMNT,t1.DELI_AMNT,t1.TOTAL_LINE,t1.ITEM_COV,t2.TOTAL_VISIT,t2.MEMO,t3.SPCM_ORD_AMNT,t3.SPCM_DELI_AMNT
                                FROM 
                                (SELECT 
                                MONTHNAME(t1.ordm_date) MONTH_NAME,
                                YEAR(t1.ordm_date) YEAR_NO,
                                MONTH(t1.ordm_date) MONTH_NO,
                                ROUND(SUM(t1.ordm_amnt),2) ORDER_AMNT,
                                ROUND(SUM(t2.ordd_odat),2)DELI_AMNT,
                                SUM(t1.ordm_icnt) TOTAL_LINE,
                                COUNT(DISTINCT t2.amim_id) ITEM_COV
                                FROM tt_ordm t1
                                INNER  JOIN tt_ordd t2 on t2.ordm_id = t1.id 
                                WHERE t1.site_id = {$site_id} 
                                AND t1.ordm_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                                GROUP BY MONTHNAME(t1.ordm_date)
                                )t1
                                LEFT JOIN (
                                        SELECT MONTHNAME(ssvh_date) MONTH_NAME,COUNT(id) TOTAL_VISIT, SUM(IF(ssvh_ispd=1,1,0)) MEMO
                                        FROM th_ssvh
                                        WHERE site_id={$site_id} AND ssvh_date>=DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND ssvh_ispd<2
                                        GROUP BY  MONTHNAME(ssvh_date)
                                ) t2 ON t1.MONTH_NAME=t2.MONTH_NAME
                                LEFT JOIN 
                                ( 		SELECT 
                                        MONTHNAME(t3.ordm_date) MONTH_NAME,
                                        ROUND(SUM(t4.ordd_oamt),2) SPCM_ORD_AMNT,
                                        ROUND(SUM(t4.ordd_odat),2) SPCM_DELI_AMNT
                                        FROM tl_spst t1 
                                        INNER JOIN tl_spft t2 ON t1.spcm_id=t2.spcm_id
                                        INNER JOIN tt_ordm t3 ON t1.site_id=t3.site_id
                                        INNER JOIN tt_ordd t4 ON t3.id=t4.ordm_id
                                        WHERE t1.site_id={$site_id} AND t3.ordm_date>=DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND t1.spcm_id={$spcm_id}
                                ) t3 ON t1.MONTH_NAME=t3.MONTH_NAME
                                ORDER BY t1.MONTH_NO");
            
            // dd($sale_details);

            return view('SpaceManagement.report.show', [ 
                'outlet' => $outlet[0],
                'sale_details' => $sale_details,
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function itemCoverage(Request $request)
    {
        $value = $request->data;
        $ex_data = explode(" ", $value); 

        $month = intVal($ex_data[0]);
        $year = intval($ex_data[1]); 
        $site_id = intval($ex_data[2]);
        // return [
        //     "year_no" => $year,
        //     "month_no" => $month,
        //     "site_id" => $site_id,
        // ];
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        // return "SELECT 
        //                 t1.id, t1.ordm_ornm, t1.ordm_date, t3.amim_name, amim_code
        //                 FROM tt_ordm t1
        //                 INNER JOIN tt_ordd t2 on t1.id = t2.ordm_id
        //                 INNER JOIN tm_amim t3 on t2.amim_id = t3.id
        //                 INNER JOIN tl_spst t4 on t4.site_id = t1.site_id
        //                 WHERE t1.site_id={$site_id} AND t1.ordm_date >= t4.apply_date 
        //                 AND  MONTH(t1.ordm_date) = {$month} 
        //                 AND YEAR(t1.ordm_date) = {$year}";

        return $data = DB::connection($this->db)->select("SELECT 
                        t1.id, t1.ordm_ornm, t1.ordm_date, t3.amim_name, amim_code,
                        ROUND(SUM(t2.ordd_oamt),2) ORD_AMNT,
                        ROUND(SUM(t2.ordd_odat),2) DELI_AMNT
                        FROM tt_ordm t1
                        INNER JOIN tt_ordd t2 on t1.id = t2.ordm_id
                        INNER JOIN tm_amim t3 on t2.amim_id = t3.id
                        INNER JOIN tl_spst t4 on t4.site_id = t1.site_id
                        AND t1.ordm_date >= t4.apply_date 
                        WHERE t1.site_id={$site_id} 
                        AND  MONTH(t1.ordm_date) = {$month} 
                        AND YEAR(t1.ordm_date) = {$year}
                        GROUP BY t1.id,t1.ordm_date, t3.amim_name, amim_code
                        ");

                return $data;
    }


    public function index1(Request $request){
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $spaces = SpaceMaintain::on($this->db)->with('saleGroup:id,slgp_name,slgp_code')->paginate(50, ['id', 'spcm_name', 'spcm_slgp', 'spcm_code', 'spcm_sdat', 'spcm_exdt', 'spcm_qyfr']);
            $slgp_list=DB::connection($this->db)->select("Select id,slgp_code,slgp_name FROM tm_slgp ORDER BY slgp_code ASC");
            
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $region = DB::connection($this->db)->select("SELECT DISTINCT `dirg_id` as id, dirg_name, dirg_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");
            $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
            $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");     
            
            return view('SpaceManagement.report.index', [
                'permission' => $this->userMenu,

                'spaces' => $spaces,
                'acmp' => $acmp,
                'region' => $region,
                'dsct' => $dsct,
                'zone' => $zone,
                'slgpList' => $slgp_list
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    

}
