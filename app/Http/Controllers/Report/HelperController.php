<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */

use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\BusinessObject\CashPartyCreditBudget;
use App\BusinessObject\SpecialBudgetMaster;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\DataExport\RequestReportData;
use DateTime;
use DatePeriod;
use DateInterval;
use App\MasterData\Country;
use App\User;
//use Maatwebsite\Excel\Facades\Excel;
use Excel;
use Illuminate\Support\Facades\Hash;
use stdClass;
use Mail;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(8000000);
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

    public function pscDataInsertForZoneWiseSpecificClassOutletCoverage()
    {

        // Data Insertion 
        // INSERT IGNORE INTO tbl_psc_data_tmp
        // SELECT
        // null,t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_id,'7'
        // FROM  tt_ordm t1
        // INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        // INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
        // INNER JOIN tt_ordd t4 ON t1.ordm_ornm=t4.ordm_ornm
        // INNER JOIN tm_amim t5 ON t4.amim_id=t5.id
        // INNER JOIN tm_itcl t6 ON t5.itcl_id=t6.id
        // WHERE t1.ordm_date between '2022-07-29' AND '2022-07-30' AND t1.slgp_id in (28,29)
        // GROUP BY t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_code;

        //Data Select Function

        // SELECT slgp_name,zone_code,zone_name,
        // SUM(CASE WHEN itcl_code='CL02925' THEN 1 ELSE 0 END)'CL02925',
        // SUM(CASE WHEN itcl_code='CL02919' THEN 1 ELSE 0 END)'CL02919',
        // SUM(CASE WHEN itcl_code='CL01917' THEN 1 ELSE 0 END)'CL01917',

        // SUM(CASE WHEN itcl_code='CL02904' THEN 1 ELSE 0 END)'CL02904',
        // SUM(CASE WHEN itcl_code='CL054' THEN 1 ELSE 0 END)'CL054',
        // SUM(CASE WHEN itcl_code='CL00318' THEN 1 ELSE 0 END)'CL00318',
        // SUM(CASE WHEN itcl_code='CL03393' THEN 1 ELSE 0 END)'CL03393',
        // SUM(CASE WHEN itcl_code='CL03391' THEN 1 ELSE 0 END)'CL03391',
        // SUM(CASE WHEN itcl_code='CL01925' THEN 1 ELSE 0 END)'CL01925',
        // SUM(CASE WHEN itcl_code='CL03432' THEN 1 ELSE 0 END)'CL03432'
        // FROM 
        // (Select 
        // t1.slgp_id,t3.slgp_name,t1.zone_name,t1.zone_code,t1.site_id,t2.itcl_code,t2.itcl_name,t2.id
        // FROM tbl_psc_data_tmp t1
        // INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
        // INNER JOIN tm_itcl t2 ON t1.itcl_id=t2.id
        // GROUP BY t1.slgp_id,t1.zone_name,t1.zone_code,t1.site_id,t2.id)tt
        // GROUP BY slgp_name,zone_code,zone_name;

    }
    public function getUserAPIVersion()
    {
        // set_time_limit(8000000);
        // $cont_id=Auth::user()->country()->id;
        // $zone_list=DB::connection($this->db)->select("SELECT id from tm_zone where lfcl_id=1");
        // $slgp_list=DB::connection($this->db)->select("SELECT id from tm_slgp where lfcl_id=1");
        // for($j=0;$j<count($slgp_list);$j++){
        //     $slgp_id=$slgp_list[$j]->id;
        //     for($i=0;$i<count($zone_list);$i++){
        //         $zone_id=$zone_list[$i]->id;
        //         DB::connection($this->db)->select("INSERT IGNORE INTO `tm_spmp`(`slgp_id`,`zone_id`,`spmp_sdpr`,`spmp_ldpr`,`cont_id`,`lfcl_id`)VALUES('$slgp_id','$zone_id',7,7,'$cont_id',1)");
        //     }
        // }

        //SLGP WISE ROUTED OUTLET AND CATWISE OUTLET QTY
        // $select_q='';
        // $cats=DB::connection($this->db)->select("SELECT id,otcg_name FROM tm_otcg");
        // for($i=0;$i<count($cats);$i++){
        //     $cat_id=$cats[$i]->id;
        //     $cat_name=$cats[$i]->otcg_name;
        //     $select_q.=",sum(if(otcg_id='$cat_id',1,0))`$cat_name`";
        // }
        // $query="SELECT 
        // t2.slgp_name,count(distinct t4.site_id) routed_outlet ".$select_q."
        // FROM tm_aemp t1
        // INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
        // INNER JOIN tl_rpln t3 ON t1.id=t3.aemp_id
        // INNER JOIN tl_rsmp t4 ON t3.rout_id=t4.rout_id
        // INNER JOIN tm_site t5 ON t4.site_id=t5.id
        // INNER JOIN tm_otcg t6 ON t5.otcg_id=t6.id
        // GROUP  BY t2.slgp_name";
        // return $query;

        // INSERT QUERY
        //  $slgp=DB::connection($this->db)->select("SELECT id from tm_slgp where lfcl_id=1");
        //  for($i=0;$i<count($slgp);$i++){
        //     $id=$slgp[$i]->id;
        //     DB::connection($this->db)->select(" INSERT IGNORE INTO tbl_slgp_ro_olt_cat SELECT null,slgp_name,count(site_id) routed_outlet ,
        //     sum(if(otcg_id='1',1,0))`General Store`,sum(if(otcg_id='2',1,0))`Grocery`,
        //     sum(if(otcg_id='3',1,0))`Confectionery`,sum(if(otcg_id='4',1,0))`Tong`,
        //     sum(if(otcg_id='5',1,0))`HORECA`,sum(if(otcg_id='6',1,0))`Others`,
        //     sum(if(otcg_id='7',1,0))`GT`,sum(if(otcg_id='8',1,0))`GT Small`,
        //     sum(if(otcg_id='9',1,0))`Prime`,sum(if(otcg_id='10',1,0))`Super Store`,
        //     sum(if(otcg_id='11',1,0))`Tea Stall`,sum(if(otcg_id='12',1,0))`Pharmacy`,
        //     sum(if(otcg_id='13',1,0))`Garments`,sum(if(otcg_id='15',1,0))`Farmer`
        //     FROM 
        //     (SELECT t2.slgp_name,t4.site_id,t6.id otcg_id
        //      FROM tm_aemp t1 
        //     INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id 
        //     INNER JOIN tl_rpln t3 ON t1.id=t3.aemp_id 
        //     INNER JOIN tl_rsmp t4 ON t3.rout_id=t4.rout_id 
        //     INNER JOIN tm_site t5 ON t4.site_id=t5.id
        //     INNER JOIN tm_otcg t6 ON t5.otcg_id=t6.id
        //     WHERE t1.lfcl_id=1 AND t5.lfcl_id=1 and t2.id='$id'
        //     GROUP BY t2.slgp_name,t4.site_id,t6.id)t1
        //     GROUP BY slgp_name");

        //}

        //  return "Done";
        //   $category=DB::connection($this->db)->select("SELECT  
        //   t4.id,t4.itcg_name
        //   FROM `tl_sgit`t1
        //   INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
        //   INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
        //   INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
        //   where t1.slgp_id in (1,2,3,4,5)
        //   GROUP BY t4.id,t4.itcg_name;");
        $category = DB::connection($this->db)->select("SELECT t4.id,t4.itcg_name FROM `tl_sgit` t1
        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
        INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
        INNER JOIN tm_itcg t4 ON t3.itcg_id=t4.id
        where t1.slgp_id=66 AND t3.lfcl_id=1 AND t2.lfcl_id=1  AND t4.lfcl_id=1
        GROUP BY t4.id,t4.itcg_name");
        //return count($category);
        $q = '';
        for ($i = 0; $i < count($category); $i++) {
            $id = $category[$i]->id;
            $name = $category[$i]->itcg_name;
            $q .= ",SUM(CASE WHEN pt.itcg_id=$id THEN 1 ELSE 0 END)`$name`";
        }
        return "
      SELECT slgp_name " . $q . " FROM 
      (SELECT
      t6.slgp_name,t5.id itcg_id,t1.site_id,t3.id amim_id
      FROM tt_ordm t1
      INNER JOIN tt_ordd t2 ON t1.ordm_ornm=t2.ordm_ornm
      INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
      INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
      INNER JOIN tm_itcg t5 ON t4.itcg_id=t5.id
      INNER JOIN tm_slgp t6 ON t1.slgp_id=t6.id
       WHERE t1.ordm_date BETWEEN '2022-07-01' AND '2022-07-22' AND t1.slgp_id=66
      GROUP BY t6.id,t5.id,t1.site_id,t3.id)pt";
    }
    public function seTPasswordFromBackUP()
    {
        return Hash::make('UAE17900');
    }
    public function seTPasswordFromBackUP1()
    {
    }
    public function insertSubcategory()
    {
        $cont_id = Auth::user()->country()->id;
        // KSA
        try{
            if ($cont_id == 15) {
                $slgp_ids = DB::connection($this->db)->select("Select id from tm_slgp order by id");
                for ($i = 0; $i < count($slgp_ids); $i++) {
                    $id = $slgp_ids[$i]->id;
                    if($id !=46){
                        // DB::connection($this->db)->select("INSERT INTO `tm_issc`( `issc_name`, `issc_code`, `issc_seqn`, `issc_opst`,  `slgp_code`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`,`slgp_id`)
                        //                 VALUES('Stool','PSC200090','91','0','-','15',1,1365,1365,'$id')");
                    }
                    // DB::connection($this->db)->select("INSERT INTO `tm_issc`( `issc_name`, `issc_code`, `issc_seqn`, `issc_opst`,  `slgp_code`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`, `updated_at`,`slgp_id`)
                    // SELECT   
                    // itsg_name,itsg_code,'1','1','',15,1,1,1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$id'
                    // FROM tm_itsg");
                }
                return "Done";
            }
            else{
                return "Not Permitted";
            }
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
        

        //UAE
        // if($id==3){
        //     $slgp_ids=DB::connection($this->db)->select("Select id from tm_slgp order by id");
        //     for($i=0;$i<count($slgp_ids);$i++){
        //         $id=$slgp_ids[$i]->id;
        //         DB::connection($this->db)->select("INSERT INTO `tm_issc`( `issc_name`, `issc_code`, `issc_seqn`, `issc_opst`,  `slgp_code`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`, `updated_at`,`slgp_id`)
        //         SELECT   
        //         itsg_name,itsg_code,'1','1','',3,1,1,1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$id'
        //         FROM tm_itsg");

        //     }
        // }


        return "All subcategory Uploaded";
    }
    //Outlet Coverage Data Production
    //Outlet Coverage Data Production
    //Outlet Coverage Data Production
    //Outlet Coverage Data Production
    //Outlet Coverage Data Production

    public function outletCoverageDataProduction()
    {
        $start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
        $end_date = Carbon::now()->endOfMonth()->subMonth()->format('Y-m-d');

        for ($i = $start_date; $i <= $end_date; $i->addDays(1)) {
            //PAL -Chutney
            // DB::connection($this->db)->select("INSERT IGNORE INTO tbl_pal_olt_coverage
            // SELECT
            // null,t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_id,7
            // FROM  tt_ordm t1
            // INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            // INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            // INNER JOIN tt_ordd t4 ON t1.ordm_ornm=t4.ordm_ornm
            // INNER JOIN tm_amim t5 ON t4.amim_id=t5.id
            // INNER JOIN tm_itcl t6 ON t5.itcl_id=t6.id
            // WHERE t1.ordm_date='$i' AND  t1.slgp_id =36 AND t5.itcl_id in (208,858)
            // GROUP BY t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_id");

            //Pal -Gems
            DB::connection($this->db)->select("INSERT IGNORE INTO tbl_pal_olt_coverage
            SELECT
            null,t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_id,7
            FROM  tt_ordm t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_zone t3 ON t2.zone_id=t3.id
            INNER JOIN tt_ordd t4 ON t1.ordm_ornm=t4.ordm_ornm
            INNER JOIN tm_amim t5 ON t4.amim_id=t5.id
            INNER JOIN tm_itcl t6 ON t5.itcl_id=t6.id
            WHERE t1.ordm_date='$i' AND  t1.slgp_id =38 AND t5.itcl_id in (864,854)
            GROUP BY t1.slgp_id,t3.zone_name,t3.zone_code,t1.site_id,t5.itcl_id");
        }
        return "Done! Lets Chill...";
    }

    public function mistyBoroiOutletCoverage()
    {
        // $start_date= Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
        // $end_date= Carbon::now()->endOfMonth()->subMonth()->format('Y-m-d');

        // for($i = $start_date; $i <= $end_date; $i->addDays(1)){
        //     DB::connection($this->db)->select("INSERT IGNORE INTO tbl_boroi Select null,
        //     t4.zone_code,t1.site_id
        //     FROM tt_ordm t1
        //     INNER JOIN tt_ordd t2 ON t1.ordm_ornm=t2.ordm_ornm
        //     INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
        //     INNER JOIN tm_zone t4 ON t3.zone_id=t4.id
        //     WHERE t1.ordm_date='$i' AND t2.amim_id=3335
        //     Group By t4.zone_code,t1.site_id");
        // }
        $begin = new DateTime('2022-09-22');
        $end = new DateTime('2022-09-23');
        $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $str = '';
        foreach ($period as $dt) {
            $str .= " ||" . $dt->format('Y-m-d');
        }
        return $str;
        // foreach ($period as $dt) {
        //     $date=$dt->format('Y-m-d');
        //     $day=$dt->format('d');
        //     DB::connection($this->db)->select("INSERT IGNORE INTO tbl_boroi Select null,
        //     t4.zone_code,t1.site_id
        //     FROM tt_ordm t1
        //     INNER JOIN tt_ordd t2 ON t1.ordm_ornm=t2.ordm_ornm
        //     INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
        //     INNER JOIN tm_zone t4 ON t3.zone_id=t4.id
        //     WHERE t1.ordm_date='$date' AND t2.amim_id=3335
        //     Group By t4.zone_code,t1.site_id");
        // }
        return "<h1>Chill Broh!!!</h1>";
    }

    public function getClientIp()
    {
        return request()->ip() . '<br> User Agent :' . request()->userAgent() . "<br>Mac Address:" . exec('getmac');
    }
    public function palOrderDetailsReport()
    {
        $begin = new DateTime('2022-07-25');
        $end = new DateTime('2022-08-26');
        //$slgp_id=35;
        $data = DB::connection($this->db)->select("Select max(ordm_date) ordm_date from tbl_order_details WHERE slgp_id=37");
        if ($data != null) {
            $date = $data[0]->ordm_date ? $data[0]->ordm_date : '2022-07-25';
            $begin = new DateTime($date);
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        //return array($period);
        foreach ($period as $dt) {
            $date = $dt->format('Y-m-d');
            DB::Connection($this->db)->select("Insert ignore into  tbl_order_details 
                    SELECT null,t2.ordm_date,
                    t2.slgp_id,
                    t5.aemp_name, 
                    t5.aemp_usnm, 
                    t4.site_code, 
                    t4.site_name,
                    t3.amim_code,
                    t3.amim_name,
                    t1.ordd_qnty,
                    t1.ordd_oamt 
                    FROM `tt_ordd` t1 
                INNER JOIN tt_ordm t2 ON t1.ordm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id = t3.id
                INNER JOIN tm_site t4 ON t2.site_id=t4.id
                INNER JOIN tm_aemp t5 on t2.aemp_id=t5.id
                    WHERE t2.ordm_date ='$date' and t2.slgp_id=37");
        }
        return "Done this group->36";
    }

    public function stcmDataInsert()
    {
    }
    public function bblFruitFun()
    {
        $begin = new DateTime('2022-06-01');
        $end = new DateTime('2022-08-31');
        $data = DB::connection($this->db)->select("Select max(ordm_date) ordm_date from tbl_order_details WHERE slgp_id=37");
        if ($data != null) {
            $date = $data[0]->ordm_date ? $data[0]->ordm_date : '2022-06-01';
            $begin = new DateTime($date);
        }
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        foreach ($period as $dt) {
            $date = $dt->format('Y-m-d');
            DB::connection($this->db)->select("Insert ignore into tbl_fruit_fun
            SELECT null,t1.ordm_date,
            t4.zone_code,t1.site_id
            FROM tt_ordm t1
            INNER JOIN tt_ordd t2 ON t1.ordm_ornm=t2.ordm_ornm
            INNER JOIN tm_aemp t3 ON t1.aemp_id=t3.id
            INNER JOIN tm_zone t4 ON t3.zone_id=t4.id
            WHERE t1.slgp_id=17 AND t1.ordm_date='$date'
            AND t2.amim_id in (659,
            661,
            1790,
            12199,
            12200,
            12427,
            12428,
            12429,
            12569,
            12607,
            12608,
            12610,
            12616,
            13197,
            13435,
            13438,
            13439)");
        }
        return "<h3 style='color:darkred;'>All Data Uploaded";
    }
    public function ksaStcmDataInsert()
    {
        $cont_id = Auth::user()->country()->id;
        $slgp = DB::connection($this->db)->select("Select max(id) slgp_id_max from tm_slgp");
        $slgp_id_max = $slgp[0]->slgp_id_max;
        $flag = 1;
        while ($flag < 72) {
            for ($i = $flag + 1; $i <= $flag + 8; $i++) {
                DB::connection($this->db)->select("INSERT IGNORE INTO `tl_stcm`( `site_id`, `site_code`, `acmp_id`, `slgp_id`, `plmt_id`, `plmt_code`, `optp_id`,
                `stcm_isfx`, `stcm_limt`, `stcm_days`, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`)
                Select  `site_id`, `site_code`, `acmp_id`, '$i', `plmt_id`, `plmt_code`, `optp_id`,
                `stcm_isfx`, `stcm_limt`, `stcm_days`, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`,1364,1364 From tl_stcm Where slgp_id=$flag");
            }
            $flag = $flag + 9;
        }
    }
    public function uaeStcmDataInsert()
    {
        $cont_id = Auth::user()->country()->id;
        $slgp = DB::connection($this->db)->select("Select id,plmt_id from tm_slgp Order by id");
        $flag = 1;
        for ($i = 0; $i < count($slgp); $i++) {
            $slgp_id = $slgp[$i]->id;
            $plmt_id = $slgp[$i]->plmt_id;
            DB::connection($this->db)->select("INSERT INTO `process_stcm`(`site_id`, `site_code`, `acmp_id`, `slgp_id`, `plmt_id`, 
            `plmt_code`, `optp_id`, `stcm_isfx`, `stcm_limt`, `stcm_days`, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
            SELECT 
            t1.id,
            t1.site_code,
            '1','$slgp_id','$plmt_id','-','1','1','10','1',0,0,0,0,0,3,1,1352,1352
            FROM tm_site t1");
        }
        return "Done !!!";
    }

    public function itemCoverage()
    {
        try {
            // for($i=31;$i<=33;$i++){
            //     DB::connection($this->db)->select("INSERT IGNORE INTO `tbl_item_cov`(`slgp_id`, `slgp_name`, `aemp_name`, `aemp_usnm`, `cov_item`, `order_amount`, `mnth`) 
            //     SELECT  t4.id,t4.slgp_name,t3.aemp_name, t3.aemp_usnm, count(distinct t1.amim_id)cov_item,sum(t1.ordd_oamt),10
            //     FROM `tt_ordd` t1 
            //     INNER JOIN tt_ordm t2 ON t1.ordm_id=t2.id
            //     INNER JOIN tm_aemp t3 on t2.aemp_id=t3.id
            //     INNER JOIN tm_slgp t4 ON t2.slgp_id=t4.id
            //     WHERE t2.ordm_date BETWEEN  '2022-10-01' and '2022-10-31' AND t2.slgp_id=$i
            //     GROUP BY t4.id,t3.aemp_name, t3.aemp_usnm");
            //}
            return "Done->" . $i;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCashCreditLimit()
    {
        $id = 'UAE0245';
        $under_user = [];
        $message = '';
        $success = 0;
        $request_time = date('Y-m-d H:i:s');
        try {
            $emp = Employee::on($this->db)->where(['aemp_usnm' => $id])->first();
            $cash_party_credit_budget = $this->calculateBudget($emp->id, $emp->role_id, $request_time);
            return $cash_party_credit_budget;
        } catch (\Exception $e) {
            $message = "Invalid staff id";
            $response_time = date('Y-m-d H:i:s');
            return array(
                'own_budget' => 0,
                'own_available_budget' => 0,
                'team_budget' => 0,
                'team_available_budget' => 0,
                'under_user' => $under_user,
                'success' => $success,
                'message' => $message,
                'request_time' => $request_time,
                'response_time' => date('Y-m-d H:i:s'),
            );
        }
    }
    public function calculateBudget($auto_id, $role_id, $request_time)
    {
        $t_bdgt = 0;
        $t_available = 0;
        $own_bdgt = 0;
        $own_available = 0;
        $month = date('m');
        $year = date('Y');
        $under_user = [];
        $j = 0;
        $success = 0;
        $message = '';
        try {
            $own_bgt = SpecialBudgetMaster::on($this->db)->where(['aemp_id' => $auto_id, 'spbm_mnth' => $month, 'spbm_year' => $year])->first();
            if ($own_bgt != '') {
                $own_bdgt += $own_bgt->spbm_limt;
                $own_available += $own_bgt->spbm_amnt;
            }
            if ($role_id > 2) {
                $mngr = $auto_id;
                $u_usr = 0;
                while ($mngr) {
                    // $cash_bdgt_user=DB::connection($this->db)->select("SELECT t1.spbm_limt,
                    //                     t1.spbm_avil,
                    //                     t1.spbm_amnt,
                    //                     t1.aemp_id,
                    //                     t2.role_id,
                    //                     t2.aemp_usnm,
                    //                     t2.aemp_name
                    //                     FROM `tt_spbm` t1
                    //                     INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                    //                     WHERE t2.aemp_mngr in ($mngr) AND t1.spbm_mnth=$month AND t1.spbm_year=$year");

                    // $mngr='';
                    //dd($cash_bdgt_user);
                    $cash_bdgt_user = DB::connection($this->db)->select("SELECT 
                    ifnull(t2.spbm_limt,0)spbm_limt,
                    ifnull(t2.spbm_avil,0)spbm_avil,
                    ifnull(t2.spbm_amnt,0)spbm_amnt,
                    t1.id aemp_id,
                    t1.role_id,
                    t1.aemp_usnm,
                    t1.aemp_name
                    FROM `tm_aemp` t1
                    LEFT JOIN( Select * FROM  tt_spbm where spbm_mnth=$month AND spbm_year=$year )t2  ON t1.id=t2.aemp_id
                    WHERE t1.aemp_mngr in ($mngr)");
                    $count = 0;
                    foreach ($cash_bdgt_user as $i => $user) {
                        $t_bdgt += $user->spbm_limt;
                        $t_available += $user->spbm_amnt;
                        if ($user->role_id > 2) {
                            if ($count == 0) {
                                $mngr .= $user->aemp_id;
                                $count++;
                            } else {
                                $mngr .= ',' . $user->aemp_id;
                            }
                        }
                        if ($u_usr == 0) {
                            $under_user[$j]['aemp_name'] = $user->aemp_name;
                            $under_user[$j]['aemp_usnm'] = $user->aemp_id;
                            $under_user[$j]['own_budget'] = $user->spbm_limt;
                            $under_user[$j]['own_available_budget'] = $user->spbm_amnt;
                            $under_user[$j]['role_id'] = $user->role_id;
                            $j++;
                        }
                        $u_user = 1;
                    }
                }
            }
            $success = 1;
            return array(
                'own_budget' => round($own_bdgt, 4),
                'own_available_budget' => round($own_available, 4),
                'team_budget' => round($t_bdgt, 4),
                'team_available_budget' => round($t_available, 4),
                'under_user' => $under_user,
                'success' => $success,
                'message' => $message,
                'request_time' => $request_time,
                'response_time' => date('Y-m-d H:i:s'),

            );
        } catch (\Exception $e) {
            $success = 0;
            $message = "Budget not available";
            return array(
                'own_budget' => round($own_bdgt, 4),
                'own_available_budget' => round($own_available, 4),
                'team_budget' => round($t_bdgt, 4),
                'team_available_budget' => round($t_available, 4),
                'under_user' => $under_user,
                'success' => $success,
                'message' => $message,
                'request_time' => $request_time,
                'response_time' => date('Y-m-d H:i:s'),
            );
        }
    }

    public function hashPassword()
    {
        //$db=(new Country())->country(2);
        // return $db->cont_conn;
   // return Hash::make('IN03489');
        //$this->insertReport($db);
        //$this->getSRList(2581);
        // $this->malSales();
        //$this->malStcmDataInsert();
        // $this->sms();
       // $this->teleCancel();
       $this->insertSubcategory();
    }
    public function getSRList($id)
    {
        $date = date('Y-m-d');
        $this->db = Auth::user()->country()->cont_conn;
        $mngr_list = '';
        $list = $id;
        while ($list) {
            $list_to_check = DB::connection($this->db)->select("Select id aemp_id,role_id from tm_aemp where aemp_mngr in ($list) AND lfcl_id=1");
            $list = '';
            $cnt = 0;
            $cnt1 = 0;
            foreach ($list_to_check as $s) {
                if ($s->role_id == 2) {
                    if ($cnt == 0) {
                        $mngr_list .= $s->aemp_id;
                    } else {
                        $mngr_list .= ',' . $s->aemp_id;
                    }
                    $cnt++;
                } else if ($s->role_id > 2) {
                    if ($cnt1 == 0) {
                        $list .= $s->aemp_id;
                    } else {
                        $list .= ',' . $s->aemp_id;
                    }
                    $cnt1++;
                }
            }
        }
        dd($mngr_list);
    }
    public function insertReport($db_name)
    {
        DB::connection($db_name->cont_conn)->select("INSERT INTO `tbl_report_request`( `report_name`, `report_heading_query`, `report_data_query`, `cont_conn`, `aemp_id`, `aemp_usnm`, 
        `aemp_name`, `aemp_email`, `report_link`, `report_status`, `created_at`, `updated_at`)
        SELECT 
        concat(report_name,'(',start_date,' to ',end_date,')')report_name,'0',report_data_query,cont_conn,aemp_id,aemp_usnm,
        aemp_name,aemp_email,'',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP
        FROM `tbl_report_request_temp` WHERE report_status=0");
        $report_list = DB::connection($db_name->cont_conn)->select("SELECT id,concat(report_name,'(',start_date,' to ',end_date,')')report_name,
                    report_heading_query,report_data_query,cont_conn,aemp_id,aemp_usnm,
                    aemp_name,aemp_email FROM `tbl_report_request_temp` WHERE report_status=0");
        foreach ($report_list as $rpt) {
            try {
                DB::connection($db_name->cont_conn)->select("INSERT INTO `tbl_report_request`( `report_name`, `report_heading_query`, `report_data_query`, `cont_conn`, `aemp_id`, `aemp_usnm`, 
                `aemp_name`, `aemp_email`, `report_link`, `report_status`, `created_at`, `updated_at`)
                VALUES('$rpt->report_name','0','$rpt->report_data_query','$rpt->cont_conn','$rpt->aemp_id','$rpt->aemp_usnm','$rpt->aemp_name','$rpt->aemp_email','','1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)
                ");
                DB::connection($db_name->cont_conn)->select("Update tbl_report_request_temp SET report_status=1 WHERE id='$rpt->id'");
            } catch (\Exception $e) {
                Storage::append('hello.txt', $e->getMessage());
            }
        }
    }


    public function sendMailWithLink($db_name)
    {
        $recipient_list = DB::connection($db_name->cont_conn)->select("SELECT aemp_id,aemp_usnm,aemp_name,aemp_email FROM tbl_report_request 
                        WHERE report_status=2  and aemp_email !='' AND date(created_at)>=curdate()-INTERVAL 2 Day
                        GROUP BY aemp_id,aemp_usnm,aemp_name,aemp_email");

        for ($i = 0; $i < count($recipient_list); $i++) {
            $id = $recipient_list[$i]->aemp_id;
            $data = DB::connection($db_name->cont_conn)->select("SELECT report_name,report_link,aemp_email,aemp_name,aemp_usnm FROM tbl_report_request 
                where aemp_id=$id AND report_status=2 ORDER BY created_at DESC ");
            $recipient_info = array('email' => $recipient_list[$i]->aemp_email, 'aemp_name' => $recipient_list[$i]->aemp_name);
            Mail::send('mail', ['data' => $data, 'info' => $recipient_info], function ($message) use ($data, $recipient_info) {
                $message->to($recipient_info['email']);
                $message->subject('SPRO Requested Report');
                $message->cc(['sa18@prangroup.com', 'mis42@mis.prangroup.com']);
                $message->from('reportbi@prangroup.com');
            });
            if (Mail::failures()) {
                // return response showing failed emails
                Mail::send('failure_mail', function ($message) {
                    $message->to('mis42@mis.prangroup.com');
                    $message->subject('SPRO Requested Report');
                    $message->cc(['hussainmahamud.swe@gmail.com']);
                    $message->from('mis42@mis.prangroup.com');
                });
            } else {
                DB::connection($db_name->cont_conn)->select("UPDATE tbl_report_request SET report_status=3 WHERE aemp_id=$id AND report_status=2");
                // return "Mail Sent";
            }
        }
    }

    public function writeLog()
    {
        Storage::put('hello.txt', 'Hi mr');
    }

    public function malSales1()
    {
        $slgp_list = DB::connection($this->db)->select("select id,slgp_code from tm_slgp");
        for ($i = 0; $i < count($slgp_list); $i++) {
            $slgp_id = $slgp_list[$i]->id;
            $slgp_code = $slgp_list[$i]->slgp_code;
            DB::connection($this->db)->select("INSERT IGNORE INTO `tm_issc`( `issc_name`, `issc_code`, `issc_seqn`, `issc_opst`, `slgp_id`, `slgp_code`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
        SELECT  `itsg_name`, `itsg_code`, 0, 05,{$slgp_id},{$slgp_code},
            14, 1, 4, 4 FROM `tm_itsg` WHERE 1");
        }
        return "Done";
    }
    public function malStcmDataInsert()
    {
        $cont_id = Auth::user()->country()->id;
        $slgp = DB::connection($this->db)->select("Select id,plmt_id from tm_slgp Order by id");
        $flag = 1;
        for ($i = 0; $i < count($slgp); $i++) {
            if ($slgp[$i]->id == 2) {
                $slgp_id = $slgp[$i]->id;
                $plmt_id = $slgp[$i]->plmt_id;
                DB::connection($this->db)->select("INSERT INTO `process_stcm`(`site_id`, `site_code`, `acmp_id`, `slgp_id`, `plmt_id`, 
            `plmt_code`, `optp_id`, `stcm_isfx`, `stcm_limt`, `stcm_days`, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
            SELECT 
            t1.id,
            t1.site_code,
            '1','$slgp_id','$plmt_id','-','1','1','10','1',0,0,0,0,0,14,1,2039,2039
            FROM tm_site t1 where lfcl_id=1");
            }
        }
        return "Done !!!";
    }
    public function juiceStcm()
    {
        DB::connection($this->db)->select("INSERT INTO `process_stcm`(`site_id`, `site_code`, `acmp_id`, `slgp_id`, `plmt_id`, 
            `plmt_code`, `optp_id`, `stcm_isfx`, `stcm_limt`,stcm_tlmt, `stcm_days`,stcm_xday, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
            SELECT 
            t1.id,
            t1.site_code,
            '1',2,3,'-','1','1','10',0,'1',0,0,0,0,0,0,14,1,2039,2039
            FROM tm_site t1 where lfcl_id=1");
    }

    public function addAllPromotions()
    {
        $cont_id = Auth::user()->country()->id;
        $slgp = DB::connection($this->db)->select("Select id,plmt_id from tm_slgp Order by id");
        $flag = 1;
        for ($i = 0; $i < count($slgp); $i++) {
            if ($slgp[$i]->id != 2) {
                $slgp_id = $slgp[$i]->id;
            }
        }
        return "Done !!!";
    }
    public function sms()
    {
        $message = "-Code:171742";
        $mobile = '01991083994';
        $cont_name = 'PRAN';
        $CURLOPT_URL = 'http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn=' . $mobile . '&masking=' . $cont_name . '&message=OTP' . $message;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $CURLOPT_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: JSESSIONID=5E85DAE3CED0D54107A3A6FB66EA1F77'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $manage = json_decode($response, true);
        return array($response);
        $res = $manage['success'];
        return $res;
    }
    public function insertUser()
    {
    }
    public function insertUser1()
    {
        $array = DB::connection($this->db)->select("SELECT * FROM tl_aemp_factory WHERE exist=0");
        $manag = 1;
        $lmanag = 1;
        DB::connection($this->db)->beginTransaction();
        try {
            foreach ($array as $value) {
                $ests = Employee::on($this->db)->where(['aemp_usnm' => $value->aemp_usnm])->first();
                $emId = $ests->id ?? 0;
                $user_exist = User::where('email', $value->aemp_usnm)->first();
                if ($user_exist) {
                    if ($emId > 0) {
                        $ests->aemp_name = $value->aemp_name ?? '-';
                        $ests->aemp_stnm = '-';
                        $ests->aemp_onme = '-';
                        $ests->aemp_mob1 = '';
                        $ests->aemp_dtsm = '';
                        $ests->aemp_asyn = 'N';
                        $ests->aemp_emal = '';
                        $ests->slgp_id = 31;
                        $ests->zone_id = 1;
                        $ests->aemp_lued = $user_exist->id;
                        $ests->edsg_id = 15;
                        $ests->role_id = 1;
                        $ests->aemp_utkn = md5(uniqid(rand(), true));
                        $ests->cont_id = 2;
                        $ests->aemp_iusr = 1;
                        $ests->aemp_emal = 'fc';
                        $ests->aemp_eusr = 1;
                        $ests->aemp_mngr = $manag;
                        $ests->aemp_lmid = $lmanag;
                        $ests->aemp_aldt = 0;
                        $ests->aemp_lcin = '99';
                        $ests->aemp_otml = 2;
                        $ests->aemp_lonl = 2;
                        $ests->aemp_usnm = $value->aemp_usnm;
                        $ests->aemp_pimg = '';
                        $ests->aemp_picn = '';
                        $ests->aemp_emcc = '';
                        $ests->site_id = 0;
                        $ests->aemp_crdt = 0;
                        $ests->aemp_issl = 0;
                        $ests->lfcl_id = 1;
                        $ests->save();
                    } else {
                        $emp = new Employee();
                        $emp->setConnection($this->db);
                        $emp->aemp_name = $value->aemp_name ?? '-';
                        $emp->aemp_stnm = '-';
                        $emp->aemp_onme = '-';
                        $emp->aemp_mob1 = '';
                        $emp->aemp_dtsm = '';
                        $emp->aemp_asyn = 'N';
                        $emp->aemp_emal = '';
                        $emp->slgp_id = 31;
                        $emp->zone_id = 1;
                        $emp->aemp_lued = $user_exist->id;
                        $emp->edsg_id = 15;
                        $emp->role_id = 1;
                        $emp->aemp_utkn = md5(uniqid(rand(), true));
                        $emp->cont_id = 2;
                        $emp->aemp_iusr = 1;
                        $emp->aemp_emal = 'fc';
                        $emp->aemp_eusr = 1;
                        $emp->aemp_mngr = $manag;
                        $emp->aemp_lmid = $lmanag;
                        $emp->aemp_aldt = 0;
                        $emp->aemp_lcin = '99';
                        $emp->aemp_otml = 2;
                        $emp->aemp_lonl = 2;
                        $emp->aemp_usnm = $value->aemp_usnm;
                        $emp->aemp_pimg = '';
                        $emp->aemp_picn = '';
                        $emp->aemp_emcc = '';
                        $emp->site_id = 0;
                        $emp->aemp_crdt = 0;
                        $emp->aemp_issl = 0;
                        $emp->lfcl_id = 1;
                        $emp->save();
                    }
                } else {
                    $user = User::create([
                        'name' => $value->aemp_name == "" ?? '-',
                        'email' => $value->aemp_usnm,
                        'password' => bcrypt($value->aemp_usnm),
                        'remember_token' => md5(uniqid(rand(), true)),
                        'lfcl_id' => 1,
                        'cont_id' => 2,
                    ]);
                    if ($emId > 0) {
                        $ests->aemp_name = $value->aemp_name ?? '-';
                        $ests->aemp_stnm = '-';
                        $ests->aemp_onme = '-';
                        $ests->aemp_mob1 = '';
                        $ests->aemp_dtsm = '';
                        $ests->aemp_asyn = 'N';
                        $ests->aemp_emal = '';
                        $ests->slgp_id = 31;
                        $ests->zone_id = 1;
                        $ests->aemp_lued = $user->id;
                        $ests->edsg_id = 15;
                        $ests->role_id = 1;
                        $ests->aemp_utkn = md5(uniqid(rand(), true));
                        $ests->cont_id = 2;
                        $ests->aemp_iusr = 1;
                        $ests->aemp_emal = 'fc';
                        $ests->aemp_eusr = 1;
                        $ests->aemp_mngr = $manag;
                        $ests->aemp_lmid = $lmanag;
                        $ests->aemp_aldt = 0;
                        $ests->aemp_lcin = '99';
                        $ests->aemp_otml = 2;
                        $ests->aemp_lonl = 2;
                        $ests->aemp_usnm = $value->aemp_usnm;
                        $ests->aemp_pimg = '';
                        $ests->aemp_picn = '';
                        $ests->aemp_emcc = '';
                        $ests->site_id = 0;
                        $ests->aemp_crdt = 0;
                        $ests->aemp_issl = 0;
                        $ests->lfcl_id = 1;
                        $ests->save();
                    } else {
                        $emp = new Employee();
                        $emp->setConnection($this->db);
                        $emp->aemp_name = $value->aemp_name ?? '-';
                        $emp->aemp_stnm = '-';
                        $emp->aemp_onme = '-';
                        $emp->aemp_mob1 = '';
                        $emp->aemp_dtsm = '';
                        $emp->aemp_asyn = 'N';
                        $emp->aemp_emal = '';
                        $emp->slgp_id = 31;
                        $emp->zone_id = 1;
                        $emp->aemp_lued = $user->id;
                        $emp->edsg_id = 15;
                        $emp->role_id = 1;
                        $emp->aemp_utkn = md5(uniqid(rand(), true));
                        $emp->cont_id = 2;
                        $emp->aemp_iusr = 1;
                        $emp->aemp_emal = 'fc';
                        $emp->aemp_eusr = 1;
                        $emp->aemp_mngr = $manag;
                        $emp->aemp_lmid = $lmanag;
                        $emp->aemp_aldt = 0;
                        $emp->aemp_lcin = '99';
                        $emp->aemp_otml = 2;
                        $emp->aemp_lonl = 2;
                        $emp->aemp_usnm = $value->aemp_usnm;
                        $emp->aemp_pimg = '';
                        $emp->aemp_picn = '';
                        $emp->aemp_emcc = '';
                        $emp->site_id = 0;
                        $emp->aemp_crdt = 0;
                        $emp->aemp_issl = 0;
                        $emp->lfcl_id = 1;
                        $emp->save();
                    }
                }
            }
            DB::connection($this->db)->commit();
            return "SUCCESS !!";
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return $e->getMessage();
        }
    }
    public function teleC()
    {

        try {
            $data = DB::connection($this->db)->select("SELECT * FROM data_tele_cancel");
            foreach ($data as $dt) {
                $start_date = $dt->ordm_date;
                $end_date = $dt->ordm_date = date('Y-m-d', strtotime($data[0]->ordm_date . ' +2 day'));
                $site_code = $dt->site_code;
                $ndata = DB::connection($this->db)->select("SELECT 
                        t1.ordm_date,t1.ordm_time,t1.id ordm_id,t1.ordm_ornm,t2.aemp_name,t2.aemp_usnm,t3.site_code,t3.site_name,t1.ordm_amnt,t4.lfcl_name
                        FROM `tt_ordm` t1
                        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                        INNER JOIN tm_site t3 ON t1.site_id=t3.id
                        INNER JOIN tm_lfcl t4 ON t1.lfcl_id=t4.id
                        WHERE t3.site_code='$site_code'  AND t1.ordm_date between '$start_date' AND '$end_date' AND t1.aemp_id not in (Select aemp_id from tbl_tele_users) LIMIT 1");
                if ($ndata) {
                    $new_date_time1 = $ndata[0]->ordm_time;
                    $ordm_date_n = $ndata[0]->ordm_date;
                    $new_ordm_id1 = $ndata[0]->ordm_id;
                    $new_ordm_ornm1 = $ndata[0]->ordm_ornm;
                    $new_aemp_name1 = $ndata[0]->aemp_name;
                    $new_aemp_usnm1 = $ndata[0]->aemp_usnm;
                    $new_site_code1 = $ndata[0]->site_code;
                    $new_site_name1 = $ndata[0]->site_name;
                    $new_ordm_amnt1 = $ndata[0]->ordm_amnt;
                    $new_lfcl_name1 = $ndata[0]->lfcl_name;

                    // Update multiple columns in the database
                    DB::connection($this->db)->table('data_tele_cancel')
                        ->where('id', $dt->id)
                        ->update([
                            'date1' => $ordm_date_n,
                            'date_time1' => $new_date_time1,
                            'ordm_id1' => $new_ordm_id1,
                            'ordm_ornm1' => $new_ordm_ornm1,
                            'aemp_name1' => $new_aemp_name1,
                            'aemp_usnm1' => $new_aemp_usnm1,
                            'site_code1' => $new_site_code1,
                            'site_name1' => $new_site_name1,
                            'ordm_amnt1' => $new_ordm_amnt1,
                            'lfcl_name1' => $new_lfcl_name1,
                            // Add more column-value pairs as needed
                        ]);
                }
            }
           // return $data[0]->ordm_date = date('Y-m-d', strtotime($data[0]->ordm_date . ' +1 day'));

           // return $data[0]->ordm_date + 1;
           return "Done!dfggfghgfg!";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function testPranSmsOtp()
    {
        // return 'ok';
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $message = '-Code:' . $otp;
        $cont_name = 'PRAN';
        $mobile = '01704133302';

        $CURLOPT_URL = 'http://sms.prangroup.com/postman/api/sendsms?userid=spro_api&password=35e31cdbcd68f1fcdc6ca988c5e1f698&msisdn=' . $mobile . '&masking=' . $cont_name . '&message=OTP' . $message;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $CURLOPT_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => ['Cookie: JSESSIONID=5E85DAE3CED0D54107A3A6FB66EA1F77'],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $data = json_decode($response, true);
        $res = $data['success'];
        return $res;
    }
    public function digDown(){
        $date=date('Y-m-d');
        $this->db = Auth::user()->country()->cont_conn;
        $role_id=DB::connection($this->db)->select("SELECT role_id from tm_aemp where id=$id");
        $role_id=$role_id[0]->role_id;
        $mngr_list='';
        $list=$id;
        if($role_id==8){

        }
        else if($role_id==7){
            
        }
        if($role_id<=5 && $role_id>2){
            while($list){
                $list_to_check=DB::connection($this->db)->select("Select aemp_id,role_id from th_dhbd_5 where aemp_mngr in ($list)");
                $list='';
                $cnt=0;
                $cnt1=0;
                foreach($list_to_check as $s){
                    if($s->role_id <$role_id){
                        if($s->role_id==2){
                            if($cnt==0){
                                $mngr_list.=$s->aemp_id;
                            }
                            else{
                                $mngr_list.=','.$s->aemp_id;
                            }
                            $cnt++;
                        }else if($s->role_id>2){
                            if($cnt1==0){
                                $list.=$s->aemp_id;
                                
                            }else{
                                $list.=','.$s->aemp_id;
                            }
                            $cnt1++;
                            
                        }
                    }
                }
            }
            
        }else{
            $mngr_list=$id;
        }
        //return $mngr_list;
        //return "SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 FROM `th_dhbd_5` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list) AND   t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)>0 AND t1.role_id=1";
        $sr_list=DB::connection($this->db)->select("SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 FROM `th_dhbd_5` t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
                AND   t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)>0 AND t1.role_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1  ORDER BY t2.aemp_name");
        return $sr_list;
    }
}
