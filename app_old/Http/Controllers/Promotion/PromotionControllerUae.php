<?php

namespace App\Http\Controllers\Promotion;

use App\BusinessObject\DepotStock;
use App\BusinessObject\Promotion;
use App\BusinessObject\PromotionDetails;
use App\BusinessObject\PromotionMapping;
use App\BusinessObject\Promotion\SitePromotion;
use App\BusinessObject\SalesGroup;
use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Depot;
use App\MasterData\DepotEmployee;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\SPDS;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Site;
use App\MasterData\SKU;
use App\MasterData\SubCategory;
use App\MasterData\SubChannel;
use App\MasterData\WareHouse;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;

class PromotionControllerUae extends Controller
{
    private $access_key = 'tm_prmr_uae';
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
            $this->aemp_id = Auth::user()->employee()->id;
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




    public function promotionSP(Request $request)
    {

        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");


            return view('Promotion.Promotion_Uae.index_sp')->with('permission', $this->userMenu)->with('acmp', $acmp);
        } else {
            return view('theme.access_limit');
        }
    }

    public function assignPromotionView(){
        $prom_list=DB::connection($this->db)->select("SELECT id,prms_code,prms_name FROM tm_prmr WHERE prms_edat>=curdate() AND lfcl_id=1 ORDER BY prms_name ASC ");
        $category = DB::connection($this->db)->select("SELECT `id`, `otcg_name`,`otcg_code` FROM `tm_otcg` WHERE lfcl_id='1'");
        $channel = DB::connection($this->db)->select("SELECT `id`, `chnl_name`, `chnl_code` FROM `tm_chnl` WHERE `lfcl_id`='1'");
        $slgp = DB::connection($this->db)->select("SELECT DISTINCT slgp_id as id, slgp_name, slgp_code FROM `user_group_permission` WHERE `aemp_id`='$this->aemp_id'");
        //$subchannel = DB::connection($this->db)->select("SELECT `id`, `scnl_name` FROM `tm_scnl` WHERE lfcl_id='1'");
        return view('Promotion.Promotion_Uae.assign_promotion',['prom_list'=>$prom_list,'category'=>$category,'channel'=>$channel,'slgp'=>$slgp]);
    }
    public function getSubChannel($id){
        $subchannel = DB::connection($this->db)->select("SELECT `id`, `scnl_name`,scnl_code FROM `tm_scnl` WHERE lfcl_id='1' AND chnl_id=$id ORDER BY scnl_name ASC");
        return $subchannel;
    }


    public function mappingSiteWithPromotion(Request $request){
        $prmr_id=$request->prmr_id;
        $channel_id=$request->channel_id;
        $sub_channel_id=$request->sub_channel_id;
        $site_cat=$request->site_cat;
        $site_code=$request->site_code;
        $q1='';
        $q2='';
        $q3='';
        if($site_cat !=''){
            $q1=" AND t2.id=$site_cat";
        }
        if($channel_id !=''){
            $q2=" AND t4.id=$channel_id";
        }
        if($sub_channel_id !=''){
            $q3=" AND t3.id=$sub_channel_id";
        }
        if($site_cat !=''){
            DB::connection($this->db)->select("INSERT IGNORE INTO tl_prsm(prmr_id,site_id,cont_id,lfcl_id,aemp_iusr,aemp_eusr)
                SELECT '$prmr_id',t1.id,'$this->cont_id',1,'$this->aemp_id','$this->aemp_id'
                FROM `tm_site` t1
                INNER JOIN tm_otcg t2 ON t1.otcg_id=t2.id
                INNER JOIN tm_scnl t3 ON t1.scnl_id=t3.id
                INNER JOIN tm_chnl t4 ON t3.chnl_id=t4.id
                WHERE t1.lfcl_id=1 " .$q1.$q2.$q3. "
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
        }
        if($site_code){
            DB::connection($this->db)->select("INSERT IGNORE INTO tl_prsm(prmr_id,site_id,cont_id,lfcl_id,aemp_iusr,aemp_eusr)
                SELECT '$prmr_id',t1.id,'$this->cont_id',1,'$this->aemp_id','$this->aemp_id'
                FROM `tm_site` t1 WHERE t1.lfcl_id=1 AND t1.site_code='$site_code'  ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
        }
        return 1;
    }



    public function promotionSPFilter(Request $request)
    {
        $cont = $this->currentUser->country()->id;
        $empId = $this->currentUser->employee()->id;
        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;

        //$acmp_id = '1';
        //$slgp_id = '1';

        if ($slgp_id != "") {
            $query = "SELECT t1.id, t1.`prms_name`, t1.`prms_code`, t1.`prms_sdat`, t1.`prms_edat`, t1.`prmr_qftp` as area_type, t1.lfcl_id,
                      t1.`prmr_qfct` as prom_type, t1.`prmr_ditp` as discount_type, t1.`prmr_ctgp` as prom_qualifier, t1.`prmr_qfgp` as prom_group,
                      t2.slgp_name, t1.`prmr_qfln` as prom_on FROM `tm_prmr` t1 INNER JOIN tm_slgp t2 on t2.id=t1.prmr_qfgp
                      WHERE t1.prmr_qfgp='$slgp_id'";
        } else {
            $query = "SELECT t1.id, t1.`prms_name`, t1.`prms_code`, t1.`prms_sdat`, t1.`prms_edat`, t1.`prmr_qftp` as area_type, t1.lfcl_id,
                      t1.`prmr_qfct` as prom_type, t1.`prmr_ditp` as discount_type, t1.`prmr_ctgp` as prom_qualifier, t1.`prmr_qfgp` as prom_group,
                      t2.slgp_name, t1.`prmr_qfln` as prom_on FROM `tm_prmr` t1 INNER JOIN tm_slgp t2 on t2.id=t1.prmr_qfgp
                      INNER JOIN user_group_permission t3 ON t2.id=t3.slgp_id WHERE t2.acmp_id='$acmp_id' and t3.aemp_id='$empId'";
        }
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $prData = DB::connection($this->db)->select(DB::raw($query));
        return $prData;
    }

    public function promotionSpShow(Request $request, $id)
    {
        //dd($id);
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $slab_sql = DB::connection($this->db)->select("SELECT `prsb_text`, `prsb_fqty` as slab_min_qnty, `prsb_famn` as slab_min_amnt, `prsb_qnty` as offer_qnty,
                `prsb_disc` as offer_amnt, `attr1` as slab_type FROM `tm_prsb` WHERE `prmr_id`='$id'");

        $buy_item = DB::connection($this->db)->select("SELECT t2.amim_name, t2.amim_code FROM `tm_prmd` t1 
                    INNER JOIN tm_amim t2 on t1.amim_id=t2.id WHERE t1.prmr_id='$id'");

        $free_item = DB::connection($this->db)->select("SELECT t2.amim_name, t2.amim_code FROM `tm_prmf` t1 
                        INNER JOIN tm_amim t2 on t1.amim_id=t2.id WHERE t1.prmr_id='$id'");
        //$site_code = DB::connection($this->db)->select("SELECT t2.site_code, t2.site_name FROM `tl_prsm` t1 INNER JOIN tm_site t2 ON t1.site_id=t2.id WHERE t1.prmr_id='$id'");
        $site_code = DB::connection($this->db)->table('tl_prsm AS t1')
                    ->join('tm_site AS t2', 't1.site_id', '=', 't2.id')
                    ->select('t2.site_code','t2.site_name')
                    ->where('t1.prmr_id','=',$id)
                    ->paginate(100);
        return view('Promotion.Promotion_Uae.show_sp')->with('permission', $this->userMenu)->with('acmp', $acmp)->with('slab_sql', $slab_sql)
            ->with('buy_item',$buy_item)->with('free_item',$free_item)->with('site_code',$site_code)->with('prmr_id',$id);
    }
    public function promotionExtendDate(Request $request, $id)
    {
        //dd($request);
        $pr_det = DB::connection($this->db)->select("SELECT id, `prms_name`,`prms_sdat`,`prms_edat`,`lfcl_id` FROM `tm_prmr` WHERE `id`='$id'");

        return view('Promotion.Promotion_Uae.extend_date')->with('permission', $this->userMenu)->with('pr_det', $pr_det);
    }
    public function promotionExtendDateSave(Request $request)
    {
       // dd($request);

        $prom_id = $request->promotion_id;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $lfcl_id = $request->lfcl_id;
        $sql = "UPDATE `tm_prmr` SET prms_sdat='$startDate', prms_edat='$endDate', lfcl_id='$lfcl_id' WHERE `id`='$prom_id'";
        //echo $sql;
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $prData = DB::connection($this->db)->select(DB::raw($sql));

        $pr_det = DB::connection($this->db)->select("SELECT id, `prms_name`,`prms_sdat`,`prms_edat`,`lfcl_id` 
FROM `tm_prmr` WHERE `id`='$prom_id'");
      //  return $pr_det;
        return view('Promotion.Promotion_Uae.extend_date')->with('permission', $this->userMenu)->with('pr_det', $pr_det);

    }

    public function promotionCopyAs(Request $request, $id){

        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $promotion = collect(DB::connection($this->db)->select("SELECT id as prom_id, prms_name, prms_code, prms_sdat, prms_edat,
                          prmr_qftp, prmr_qfct, prmr_ditp, prmr_ctgp, prmr_qfgp, prmr_qfon, prmr_qfln 
                          FROM `tm_prmr` WHERE id='$id'"))->first();

            $salesGroups = DB::connection($this->db)->select("SELECT
                      t2.id        AS slgp_id,
                      t2.slgp_name AS slgp_name,
                      t2.slgp_code AS slgp_code
                    FROM tm_slgp AS t2
                    WHERE t2.cont_id = '$country_id'
                    ORDER BY t2.id, t2.slgp_name");

            $slab_sql = DB::connection($this->db)->select("SELECT `prsb_text`, `prsb_fqty` as slab_min_qnty, `prsb_famn` as slab_min_amnt, `prsb_qnty` as offer_qnty,
                `prsb_disc` as offer_amnt, attr1 as pro_slab_qua FROM `tm_prsb` WHERE `prmr_id`='$id'");
            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            $buy_item = DB::connection($this->db)->select("SELECT t2.amim_name, t2.amim_code, t2.id as amim_id, t4.issc_name FROM `tm_prmd` t1 
INNER JOIN tm_amim t2 on t1.amim_id=t2.id
inner JOIN tl_sgit t3 on t2.id=t3.amim_id
INNER JOIN tm_issc t4 on t3.issc_id=t4.id
inner join tm_prmr t5 on t3.slgp_id=t5.prmr_qfgp WHERE t1.prmr_id='$id' GROUP BY t2.amim_code");


            $free_item = DB::connection($this->db)->select("SELECT t2.amim_name, t2.amim_code, t4.issc_name FROM `tm_prmf` t1 
INNER JOIN tm_amim t2 on t1.amim_id=t2.id
inner JOIN tl_sgit t3 on t2.id=t3.amim_id
INNER JOIN tm_issc t4 on t3.issc_id=t4.id
INNER JOIN tm_prmr t5 on t3.slgp_id=t5.prmr_qfgp WHERE t1.prmr_id='$id' GROUP BY t2.amim_code");



            $category = DB::connection($this->db)->select("SELECT `id`, `otcg_name`,`otcg_code` FROM `tm_otcg` WHERE lfcl_id='1'");
            $channel = DB::connection($this->db)->select("SELECT `id`, `chnl_name`, `chnl_code` FROM `tm_chnl` WHERE `lfcl_id`='1'");
            $subchannel = DB::connection($this->db)->select("SELECT `id`, `scnl_name` FROM `tm_scnl` WHERE lfcl_id='1'");
            $district = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
            $thana = DB::connection($this->db)->select("SELECT `id`, `than_name`, `than_code` FROM `tm_than` WHERE lfcl_id='1'");

            return view('Promotion.Promotion_Uae.promotion_copy_as')->with("salesGroups", $salesGroups)->with("category", $category)
                ->with("add_salesGroups", $salesGroups)->with('permission', $this->userMenu)->with('promotion',$promotion)->with('slab_sql', $slab_sql)
                ->with('buy_item',$buy_item)->with('free_item',$free_item)->with('channel',$channel)->with('subchannel',$subchannel)->with('district', $district)->with('thana',$thana);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function promotionInactive(Request $request, $id){
        $sql = "UPDATE `tm_prmr` SET lfcl_id='2' WHERE id='$id'";
        $sr = DB::connection($this->db)->select("$sql");
        $sql2 =  DB::connection($this->db)->select("select lfcl_id from tm_prmr where id='$id'");
        return $sql2;

    }
    public function showItemPriceAndDisc(Request $request)
    {
        $item_id = $request->item_id;
        $slgp_id = $request->slgp_id;
        $price = DB::connection($this->db)->select("SELECT pldt_tppr from tm_pldt where plmt_id='$slgp_id' AND amim_id='$item_id'");
        return $price;
    }

    public function filterItem(Request $request)

    {
        $slgp_id = $request->slgp_id;
        $items = DB::connection($this->db)->select("SELECT t2.id as id, t2.amim_name as item_name, t2.amim_code as item_code FROM `tl_sgit` t1 
INNER JOIN tm_amim t2 ON t1.`amim_id`=t2.id WHERE t1.slgp_id='$slgp_id'");

        return Response::json($items);

    }

    public function getItemCategory(Request $request)
    {
        $slgp_id = $request->slgp_id;
        $itemCategory = DB::connection($this->db)->select("SELECT DISTINCT t2.id, t2.issc_name, t2.issc_code FROM `tl_sgit` t1 
INNER JOIN tm_issc t2 ON t1.issc_id=t2.id WHERE t1.slgp_id='$slgp_id' ORDER BY `t2`.`issc_name` ASC");

        return Response::json($itemCategory);
    }

    public function getCategoryItem(Request $request)
    {
        $slgp_id = $request->slgp_id;
        $issc_id = $request->category_id;
        $items = DB::connection($this->db)->select("SELECT DISTINCT t2.id, t2.amim_name as item_name, t2.amim_code as item_code, t3.issc_name FROM `tl_sgit` t1 
INNER JOIN tm_amim t2 ON t1.`amim_id`=t2.id inner join tm_issc t3 on t1.issc_id=t3.id WHERE t1.slgp_id='$slgp_id' AND t1.`issc_id`='$issc_id' ORDER BY `t2`.`amim_code` ASC");

        return Response::json($items);
    }

    public function promotionNewCreate()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $salesGroups = DB::connection($this->db)->select("SELECT
                      t2.id        AS slgp_id,
                      t2.slgp_name AS slgp_name,
                      t2.slgp_code AS slgp_code
                    FROM tm_slgp AS t2
                    WHERE t2.cont_id = '$country_id'
                    ORDER BY t2.id, t2.slgp_name");
            $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE 1");
            $category = DB::connection($this->db)->select("SELECT `id`, `otcg_name`,`otcg_code` FROM `tm_otcg` WHERE lfcl_id='1'");
            $channel = DB::connection($this->db)->select("SELECT `id`, `chnl_name`, `chnl_code` FROM `tm_chnl` WHERE `lfcl_id`='1'");
            $subchannel = DB::connection($this->db)->select("SELECT `id`, `scnl_name` FROM `tm_scnl` WHERE lfcl_id='1'");
            $district = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
            $thana = DB::connection($this->db)->select("SELECT `id`, `than_name`, `than_code` FROM `tm_than` WHERE lfcl_id='1'");
            //$ward = DB::connection($this->db)->select("SELECT `id`, `ward_name`, `ward_code` FROM `tm_ward` WHERE lfcl_id='1'");
            //$mktm = DB::connection($this->db)->select("SELECT `id`, `mktm_name`, `mktm_code` FROM `tm_mktm` WHERE lfcl_id='1'");
            return view('Promotion.Promotion_Uae.create2')->with("salesGroups", $salesGroups)
                ->with("category",$category)->with("channel", $channel)->with("zones", $zones)->with("thana",$thana)
                ->with("subchannel",$subchannel)->with("district",$district)->with("add_salesGroups", $salesGroups)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }


        /*if ($this->userMenu->wsmu_vsbl) {

            return view('Promotion.employee.routeSearch');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');
        }*/
    }

    public function promotionNewStore(Request $request)
    {
        //dd($request);
            try{
            $category="";
            $sub_channel="";
            $channel="";
            $than="";
            $distr="";

            //name Code promotion_label discount_type promotion_type  offer_type offer_category  qualifier_type endDate startDate slgp_idds
            $p_name = $request['promotion_name'];
            $Code = $request['promotion_code'];
            $order_qualifier = 0;

            if (isset($request['promotion_label'])) {
                $promotion_label = $request['promotion_label'];
            } else {
                $promotion_label = 0;
            }

            if (isset($request['discount_type'])) {
                $discount_type = $request['discount_type'];
            } else {
                $discount_type = 0;
            }

            if (isset($request['promotion_type'])) {
                $promotion_type = $request['promotion_type'];
            } else {
                $promotion_type = "0";
            }

            if (isset($request['offer_category'])) {
                $offer_category = $request['offer_category'];
            } else {
                $offer_category = 0;
            }
            /*if (isset($request['order_qualifier'])) {
                $order_qualifier = $request['order_qualifier'];
            }*/

            if (isset($request['offer_type'])) {
                $order_qualifier = $request['offer_type'];
            }
            if (isset($request['offer_type'])) {
                $order_qualifier = $request['offer_type'];
            }

            if (isset($request['category'])) {
                $cat = $request['category'];
                $category = " and t1.otcg_id IN (".implode(',',$cat).")";
            }
            if (isset($request['sub_channel'])) {
                $sub_cha = $request['sub_channel'];
                $sub_channel = " and t1.scnl_id IN (".implode(',',$sub_cha).")";
            }
            if (isset($request['channel'])) {
                $cha = $request['channel'];
                $channel = " AND t3.id IN (".implode(',',$cha).")";
            }
            if (isset($request['thana_id'])) {
                $thana = $request['thana_id'];
                $than = " AND t7.id IN (".implode(',',$thana).")";
            }
            if (isset($request['district'])) {
                $dist = $request['district'];
                $distr = " AND t8.id IN (".implode(',',$dist).")";
            }




            $endDate = $request['endDate'];
            $startDate = $request['startDate'];
            //$slgp_idds = $request['slgp_idds'];
            $slgp_idds = $request->slgp_idds;
            //return implode(',',$slgp_idds);
            $cuser = $this->currentUser->employee()->id;
            $uuser = $this->currentUser->employee()->id;

            $promotion_slab_qua = $request['promotion_slab_qua'];
            $min_item_qty = $request['min_item_qty'];
            $offer_item_qty = $request['offer_item_qty'];
            $slab_text = $request['slab_text'];
            $slab_type = $request['slab_type'];
            $no_of_slab = sizeof($min_item_qty);

            //$item_list = $request['order_item_id'];
            $item_type = $request['order_type'];


            if (isset($request['order_item_id'])) {
                $item_list = $request['order_item_id'];
                $no_of_item = sizeof($request['order_item_id']);
            } else {
                $no_of_item = 0;
            }

            $pro_type = $request['pro_type'];
            if ($request['area_list'] != '') {
                $area_list = $request['area_list'];
                $no_of_area = sizeof($area_list);
            }

            $cont_id = $this->currentUser->country()->id;


            if ($pro_type == '1') {
                $qualifier_type = 'Zonal';
            } else {
                $qualifier_type = 'National';
            }

            //min_item_qty  offer_item_qty slab_text slab_type
            //$check='';
            $iterator=[];
            for($i=0;$i<count($slgp_idds);$i++){
                $prms_code=$Code.'-'.$i;
                $insert = ['prms_name' => $p_name, 'prms_code' => $prms_code, 'prms_sdat' => $startDate, 'prms_edat' => $endDate,
                        'prmr_qftp' => $qualifier_type, 'prmr_qfct' => $promotion_label, 'prmr_ditp' => $discount_type,
                        'prmr_ctgp' => $promotion_type, 'prmr_qfgp' => $slgp_idds[$i], 'prmr_qfon' => $order_qualifier,
                        'prmr_qfln' => $offer_category, 'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];
                if (!empty($insert)) {
                    $p_id = DB::connection($this->db)->table('tm_prmr')->insertGetId($insert);
                    $iterator[$i]=$p_id;
                }
                

            }
            
                // $insert = ['prms_name' => $p_name, 'prms_code' => $Code, 'prms_sdat' => $startDate, 'prms_edat' => $endDate,
                //     'prmr_qftp' => $qualifier_type, 'prmr_qfct' => $promotion_label, 'prmr_ditp' => $discount_type,
                //     'prmr_ctgp' => $promotion_type, 'prmr_qfgp' => $slgp_idds, 'prmr_qfon' => $order_qualifier,
                //     'prmr_qfln' => $offer_category, 'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];

                // if (!empty($insert)) {
                //     $p_id = DB::connection($this->db)->table('tm_prmr')->insertGetId($insert);
                // }

                /*$sql = "INSERT INTO `tm_prmr`(`prms_name`, `prms_code`, `prms_sdat`, `prms_edat`, `prmr_qftp`, `prmr_qfct`, `prmr_ditp`,
                `prmr_ctgp`, `prmr_qfgp`, `prmr_qfon`, `prmr_qfln`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `var`)
                VALUES ('$p_name', '$Code','$startDate', '$endDate', '$qualifier_type','$promotion_label','$discount_type', '$promotion_type',
                '$slgp_idds','$order_qualifier','$offer_category', '2', '1','$cuser', '$uuser','1')";*/

                /*if (!empty($insert)) {
                    DB::connection($this->db)->table('tt_pznt')->insert($insert);
                }*/

            if ($promotion_slab_qua == 'Quantity') {
                for($j=0;$j<count($iterator);$j++){
                    $p_id=$iterator[$j];
                    for ($i = 0; $i < $no_of_slab; $i++) {
                        $sl = $i;
                        $sl++;
                        $slab = $slab_text[$i];
                        $offer = $offer_item_qty[$i];
                        $minItem = $min_item_qty[$i];
                        $sql2[] = ['prmr_id' => $p_id, 'prsb_text' => $slab, 'prsb_fqty' => $minItem, 'prsb_tqty' => 0,
                            'prsb_famn' => 0, 'prsb_tamn' => 0, 'prsb_qnty' => $offer,
                            'prsb_disc' => 0, 'prsb_modr' => $p_id, 'prsb_mosl' => $sl,
                            'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1, 'attr1' =>$promotion_slab_qua];
                    }
                }
            } else {
                for($j=0;$j<count($iterator);$j++){
                    $p_id=$iterator[$j];
                    for ($i = 0; $i < $no_of_slab; $i++) {
                        $sl = $i;
                        $sl++;
                        $slab = $slab_text[$i];
                        $offer = $offer_item_qty[$i];
                        $minItem = $min_item_qty[$i];

                        $sql2[] = ['prmr_id' => $p_id, 'prsb_text' => $slab, 'prsb_fqty' => 0, 'prsb_tqty' => 0,
                            'prsb_famn' => $minItem, 'prsb_tamn' => 0, 'prsb_qnty' => 0,
                            'prsb_disc' => $offer, 'prsb_modr' => $p_id, 'prsb_mosl' => $sl,
                            'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1, 'attr1' =>$promotion_slab_qua];
                    }
                }
            }

            if (!empty($sql2)) {
                DB::connection($this->db)->table('tm_prsb')->insert($sql2);
            }


            for ($j = 0; $j < $no_of_item; $j++) {
                if ($item_type[$j] == 'order') {
                    $item = $item_list[$j];
                    for($i=0;$i<count($iterator);$i++){
                        $p_id=$iterator[$i];
                        $sql3[] = ['prmr_id' => $p_id, 'amim_id' => $item, 'prmd_modr' => $p_id, 'prmd_mosl' => 0,
                            'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];
                    }

                    /*$sql3[] = "INSERT INTO `tm_prmd`(`prmr_id`, `amim_id`, `prmd_modr`, `prmd_mosl`, `cont_id`, `lfcl_id`, `aemp_iusr`,
                    `aemp_eusr`,`var`) VALUES ('3','$item_list[$j]','3','0','2','1','$cuser','$uuser','1')";*/
                } else {
                    $item = $item_list[$j];
                    for($i=0;$i<count($iterator);$i++){
                        $p_id=$iterator[$i];
                        $sql5[] = ['prmr_id' => $p_id, 'amim_id' => $item, 'prmd_modr' => $p_id, 'prmf_qnty' => 0, 'prmf_amnt' => 0, 'prmf_damt' => 0,
                            'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];
                    }

                    /*$sql4[] ="INSERT INTO `tm_prmf`(`prmr_id`, `amim_id`, `prmd_modr`, `prmf_qnty`, `prmf_amnt`, `prmf_damt`, `cont_id`,
                    `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `var`) VALUES ('3','$item_list[$j]','3','0','0','0','2','1','$cuser','$uuser','1')";*/
                }
            }
            if (!empty($sql3)) {
                DB::connection($this->db)->table('tm_prmd')->insert($sql3);
            }
            if (!empty($sql5)) {
                DB::connection($this->db)->table('tm_prmf')->insert($sql5);
            }
            /*for ($k = 0; $k < $no_of_area; $k++) {
                if ($pro_type=='1'){
                    $area =$area_list[$k];
                    $sql4[] = ['prmr_id' => $p_id, 'site_id' => $area,
                        'cont_id' => $cont_id,'lfcl_id'=> 1,'aemp_iusr'=> $cuser,'aemp_eusr'=> $uuser,'var'=> 1];
                }
                $sql4[] = "INSERT INTO `tl_prsm`(`prmr_id`, `site_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`,`var`)
    VALUES ('3','$area','2','1','$cuser','$uuser','1')";
            }*/
            for($i=0;$i<count($iterator);$i++){
                $p_id=$iterator[$i];
                $site = "INSERT INTO `tl_prsm`(`prmr_id`, `site_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`)
                        SELECT $p_id as p_id, t1.id as site_id, t1.cont_id, '1' as lfcl_id,'$this->aemp_id',
                        '$this->aemp_id' FROM `tm_site` t1 INNER JOIN tm_scnl t2 ON t1.scnl_id=t2.id 
                        INNER JOIN tm_chnl t3 ON t2.chnl_id=t3.id 
                        INNER JOIN tm_otcg t4 ON t1.otcg_id=t4.id INNER JOIN tm_mktm t5 ON t1.mktm_id=t5.id 
                        INNER JOIN tm_ward t6 ON t5.ward_id=t6.id INNER JOIN tm_than t7 ON t6.than_id=t7.id 
                        INNER JOIN tm_dsct t8 ON t8.id=t7.dsct_id WHERE t1.lfcl_id='1' ". $category. $sub_channel. $channel. $than. $distr."
                        GROUP BY t1.id ";
                DB::connection($this->db)->select($site);
            }
            //dd($site);
        

            /*if ($pro_type == '1') {
                for ($k = 0; $k < $no_of_area; $k++) {

                    $area = $area_list[$k];
                    $sql4[] = ['prmr_id' => $p_id, 'site_id' => $area,
                        'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];

                }

            } else {
                $sql9 = "INSERT INTO `tl_prsm`(`prmr_id`, `site_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `var`)
                SELECT $p_id as prmr_id, `id` as site_id, $cont_id as cont_id, '1' as `lfcl_id`, $cuser as `aemp_iusr`, $uuser as `aemp_eusr`,'1' as  `var` 
                FROM `tm_zone` WHERE `lfcl_id`='1'";

                DB::connection($this->db)->select($sql9);
            }


            if (!empty($sql4)) {
                DB::connection($this->db)->table('tl_prsm')->insert($sql4);
            }*/



            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");


            return redirect()->back()->with('success', 'Promotion Created Successfully')->with('permission', $this->userMenu)->with('acmp', $acmp);
        }catch(\Exception $e){
            $error_message="Something Went Wrong !!!! Please provide all essential Info";
            return redirect()->back()->with('danger',$error_message);
        }

    }

    public function getSpmiView(Request $request)
    {

        if ($this->userMenu->wsmu_vsbl) {

            $results = DB::connection($this->db)->select("SELECT
                                tm_spmp.id,
                                tm_slgp.slgp_code,
                                tm_slgp.slgp_name,
                                tm_zone.zone_code,
                                tm_zone.zone_name,
                                tm_spmp.spmp_sdpr
                            FROM tm_spmp    
                            JOIN tm_slgp ON tm_slgp.id = tm_spmp.slgp_id
                            JOIN tm_zone ON tm_zone.id = tm_spmp.zone_id");
            return view('Promotion.special_discount')
                ->with('results', $results);

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }


    }

    public function getSpmiCreate(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {

            $salesGroups = DB::connection($this->db)->select("SELECT * FROM tm_slgp");
            $zones = DB::connection($this->db)->select("SELECT * FROM tm_zone");

            return view('Promotion.special_discount_create')
                ->with('salesGroups', $salesGroups)
                ->with('zones', $zones);


        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }

    }

    public function spmpStoreDetails(Request $request)
    {

        if (count($request->zone_ids) > 0) {

            for ($i = 0; $i < count($request->zone_ids); $i++) {

                $existOrNot = SPDS::where('slgp_id', $request->slgp_id)->where('zone_id', $request->zone_ids[$i])->first();
                if ($existOrNot == null) {

//                    $spds = new SPDS();
//                    $spds->setConnection($this->db);
//                    $spds->slgp_id  = $request->slgp_id;
//                    $spds->zone_id  = $request->zone_ids[$i];
//                    $spds->spmp_sdpr = $request->discount_percent;
//                    $spds->cont_id = $this->currentUser->country()->id;
//                    $spds->lfcl_id = 1;
//                    $spds->save();

                    DB::connection($this->db)->table('tm_spmp')->insert(
                        array(
                            'slgp_id' => $request->slgp_id,
                            'zone_id' => $request->zone_ids[$i],
                            'spmp_sdpr' => $request->discount_percent,
                            'pswd_npwd' => $request->newPassword,
                            'cont_id' => $this->currentUser->country()->id,
                            'lfcl_id' => 1,
                        )
                    );

                }

            }

            return back()->with('success', 'Added Successfully..!!');

        } else {

            return redirect()->back()->with('danger', 'Please Select Your Zone First');

        }


    }

    public function addingPromotionFromExistingPromotion(Request $value)
    {

        //dd($value->all());
        DB::connection($this->db)->beginTransaction();
        try {
            $promotion = new Promotion();
            $promotionDetails = new PromotionDetails();
            $promotion->setConnection($this->db);
            $promotion->prom_name = $value['name'];
            $promotion->prom_code = $value['code'];
            $promotion->prom_sdat = $value['startDate'];
            $promotion->prom_edat = $value['endDate'];
            $promotion->prom_type = '0';
            $promotion->slgp_id = $value['slgp_id'];
            $promotion->prom_nztp = $value['pro_type'];
            $promotion->cont_id = $this->currentUser->country()->id;
            $promotion->lfcl_id = 1;
            $promotion->aemp_iusr = $this->currentUser->employee()->id;
            $promotion->aemp_eusr = $this->currentUser->employee()->id;
            $promotion->var = 0;
            $promotion->attr1 = '';
            $promotion->attr2 = '';
            $promotion->attr3 = 0;
            $promotion->attr4 = 0;
            $promotion->save();
            $lid = $promotion->id;
            $promotionDetails->prom_id = $lid;
            $promotionDetails->prdt_sitm = $value['buy_item'];
            $promotionDetails->prdt_sitm = $value['buy_item'];
            $promotionDetails->prdt_mbqt = $value['max_qty'];
            $promotionDetails->prdt_mnbt = $value['mi_qty'];
            $promotionDetails->prdt_fitm = $value['free_item'];
            $promotionDetails->prdt_fiqt = $value['f_item_qty'];
            $promotionDetails->prdt_fipr = $value['f_item_price'];
            $promotionDetails->prdt_disc = $value['dis_percen'];
            $promotionDetails->prdt_disa = '0';
            $promotionDetails->cont_id = $this->currentUser->country()->id;
            $promotionDetails->lfcl_id = 1;
            $promotionDetails->aemp_iusr = $this->currentUser->employee()->id;
            $promotionDetails->aemp_eusr = $this->currentUser->employee()->id;
            $promotionDetails->var = 0;
            $promotionDetails->attr1 = '';
            $promotionDetails->attr2 = '';
            $promotionDetails->attr3 = 0;
            $promotionDetails->attr4 = 0;
            $promotionDetails->save();
            if ($value['pro_type'] == 1) {
                $id = $value['promp_id'];
                $zones = DB::connection($this->db)->select("SELECT
                           t2.id as id
                           FROM `tt_pznt` t1 INNER JOIN tm_zone t2 ON t1.`zone_id` = t2.id
                           WHERE t1.`prom_id` = '$id'");
                foreach ($zones as $key => $row) {
                    $insert[] = ['prom_id' => $lid, 'zone_id' => $row->id, 'cont_id' => $this->currentUser->country()->id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
                }
                if (!empty($insert)) {
                    DB::connection($this->db)->table('tt_pznt')->insert($insert);
                }
            }
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }

    }
    public function getCategoryItemUAE(Request $request){
         //$slgp_id = $request->slgp_id;
        // $slgp_id=implode(',',$slgp_id);
        $issc_id = $request->category_id;
        // $items = DB::connection($this->db)->select("SELECT DISTINCT t2.id, t2.amim_name as item_name, t2.amim_code as item_code, t3.issc_name FROM `tl_sgit` t1 
        //           INNER JOIN tm_amim t2 ON t1.`amim_id`=t2.id inner join tm_issc t3 on t1.issc_id=t3.id WHERE t1.slgp_id in ($slgp_id) AND t1.`issc_id`='$issc_id' ORDER BY `t2`.`amim_code` ASC");
        if($issc_id==''){
            return 1;
        }
        $items=DB::connection($this->db)->select("Select t1.id,amim_name as item_name,t1.amim_code as item_code,t2.itsg_name issc_name FROM tm_amim t1
                INNER JOIN tm_itsg t2 ON t1.itsg_id=t2.id
                WHERE t1.lfcl_id=1 AND t1.itsg_id={$issc_id}
                AND t1.id not in (Select 
                                    t1.amim_id
                                    FROM tm_prmd t1
                                    INNER JOIN tm_prmr t2 ON t1.prmr_id=t2.id
                                    WHERE t2.lfcl_id=1 AND t2.prms_edat>=curdate()
                                )
                ORDER BY t1.amim_code ASC");

        return Response::json($items);
    }

    public function getSpecificGroupPromotion($slgp_id){
        $prom_list=DB::connection($this->db)->select("SELECT id,prms_code,prms_name FROM tm_prmr WHERE prms_edat>=curdate() AND lfcl_id=1 AND prmr_qfgp=$slgp_id ORDER BY prms_name ASC ");
        return $prom_list;
    }
    public function getSubChannel1($id){
        $subchannel = DB::connection($this->db)->select("SELECT `id`, `scnl_name` FROM `tm_scnl` WHERE lfcl_id='1' AND chnl_id=$id ORDER BY scnl_name ASC");
        return $subchannel;
    }

    public function specificGroupsiteMapping(Request $request){
        $prmr_id=$request->prmr_id;
        $channel_id=$request->channel_id;
        $sub_channel_id=$request->sub_channel_id;
        $site_cat=$request->site_cat;
        $site_code=$request->site_code;
        $q1='';
        $q2='';
        $q3='';
        if($site_cat !=''){
            $q1=" AND t2.id IN (".implode(',',$site_cat).")";
        }
        if($channel_id !=''){
            $q2=" AND t4.id in (".implode(',',$channel_id).")";
        }
        if($sub_channel_id !=''){
            $q3=" AND t3.id in (".implode(',',$sub_channel_id).")";
        }
        if($site_cat !=''){
            DB::connection($this->db)->select("INSERT IGNORE INTO tl_prsm(prmr_id,site_id,cont_id,lfcl_id,aemp_iusr,aemp_eusr)
                SELECT '$prmr_id',t1.id,'$this->cont_id',1,'$this->aemp_id','$this->aemp_id'
                FROM `tm_site` t1
                INNER JOIN tm_otcg t2 ON t1.otcg_id=t2.id
                INNER JOIN tm_scnl t3 ON t1.scnl_id=t3.id
                INNER JOIN tm_chnl t4 ON t3.chnl_id=t4.id
                WHERE t1.lfcl_id=1 " .$q1.$q2.$q3. "
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
        }
        if($site_code){
            DB::connection($this->db)->select("INSERT IGNORE INTO tl_prsm(prmr_id,site_id,cont_id,lfcl_id,aemp_iusr,aemp_eusr)
                SELECT '$prmr_id',t1.id,'$this->cont_id',1,'$this->aemp_id','$this->aemp_id'
                FROM `tm_site` t1 WHERE t1.lfcl_id=1 AND t1.site_code='$site_code'  ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
        }
        return 1;
    }
    public function exportPromotionAssignSite($id){
        return Excel::download(new SitePromotion($id), 'outlet_list' . date("Y-m-d") . '.xlsx'); 
    }

}
