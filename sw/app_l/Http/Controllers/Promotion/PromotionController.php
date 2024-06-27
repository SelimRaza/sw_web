<?php

namespace App\Http\Controllers\Promotion;

use App\BusinessObject\DepotStock;
use App\BusinessObject\Promotion;
use App\BusinessObject\PromotionDetails;
use App\BusinessObject\PromotionMapping;
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
use App\BusinessObject\BulkPromUpload;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;

class PromotionController extends Controller
{
    private $access_key = 'tm_prom';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
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

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            return view('Promotion.index')->with('permission', $this->userMenu)->with('search_text', $q)->with('acmp', $acmp);
        } else {
            return view('theme.access_limit');
        }
    }


    public function promotionSP(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");


            return view('Promotion.index_sp')->with('permission', $this->userMenu)->with('acmp', $acmp);
        } else {
            return view('theme.access_limit');
        }
    }


    public function promotionFilter(Request $request)
    {
        $cont = $this->currentUser->country()->id;
        $empId = $this->currentUser->employee()->id;
        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;

        //$acmp_id = '1';
        //$slgp_id = '1';

        if ($slgp_id != "") {
            $query = "select t1.id AS prom_id, t1.prom_name, t1.prom_code, t1.prom_sdat,
                        t1.prom_edat,
                        t1.prom_type,
                        t1.prom_nztp,
                        t2.slgp_name,
                        t4.amim_code,
                        t4.amim_name,
                        t1.lfcl_id FROM tm_prom as t1 
                        INNER JOIN  tm_slgp as t2 on t1.slgp_id=t2.id 
                        INNER JOIN tt_prdt as t3 on t1.id=t3.prom_id 
                        INNER JOIN tm_amim as t4 on t3.prdt_sitm=t4.id WHERE t1.slgp_id='$slgp_id'";
        } else {
            $query = "select t1.id AS prom_id, t1.prom_name, t1.prom_code, t1.prom_sdat,
                        t1.prom_edat,
                        t1.prom_type,
                        t1.prom_nztp,
                        t2.slgp_name,
                        t4.amim_code,
                        t4.amim_name,
                        t1.lfcl_id FROM tm_prom as t1 INNER JOIN  tm_slgp as t2 on t1.slgp_id=t2.id 
                        INNER JOIN tt_prdt as t3 on t1.id=t3.prom_id INNER JOIN tm_amim as t4 on t3.prdt_sitm=t4.id
                        INNER JOIN user_group_permission t5 ON t2.id=t5.slgp_id WHERE t2.acmp_id='$acmp_id' AND t5.aemp_id='$empId'";
        }
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $prData = DB::connection($this->db)->select(DB::raw($query));
        return $prData;
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
        $zone_code = DB::connection($this->db)->select("SELECT t2.zone_name, t2.zone_code FROM `tl_prsm` t1 INNER JOIN tm_zone t2 ON t1.site_id = t2.id WHERE t1.`prmr_id`='$id'");
        return view('Promotion.show_sp')->with('permission', $this->userMenu)->with('acmp', $acmp)->with('slab_sql', $slab_sql)
            ->with('buy_item',$buy_item)->with('free_item',$free_item)->with('zone_code',$zone_code);
    }
    public function promotionExtendDate(Request $request, $id)
    {
        //dd($request);
        $pr_det = DB::connection($this->db)->select("SELECT id, `prms_name`,`prms_sdat`,`prms_edat`,`lfcl_id` FROM `tm_prmr` WHERE `id`='$id'");

        return view('Promotion.extend_date')->with('permission', $this->userMenu)->with('pr_det', $pr_det);
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
        return view('Promotion.extend_date')->with('permission', $this->userMenu)->with('pr_det', $pr_det);

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



            $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE 1");

            $zone_code = DB::connection($this->db)->select("SELECT t2.zone_name, t2.zone_code, t1.id as id FROM `tl_prsm` t1 INNER JOIN tm_zone t2 ON t1.site_id = t2.id WHERE t1.`prmr_id`='$id'");

            return view('Promotion.promotion_copy_as')->with("salesGroups", $salesGroups)->with("zones", $zones)
                ->with("add_salesGroups", $salesGroups)->with('permission', $this->userMenu)->with('promotion',$promotion)->with('slab_sql', $slab_sql)
                ->with('buy_item',$buy_item)->with('free_item',$free_item)->with('zone_code',$zone_code);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function create()
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
            return view('Promotion.create')->with("salesGroups", $salesGroups)->with("zones", $zones)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $value)
    {
        //dd($value->all());
        $value->validate([
            'name' => 'required',
            'code' => 'required|max:10',
            'startDate' => 'required',
            'endDate' => 'required',
            'slgp_id' => 'required',
            'pro_type' => 'required',
            'buy_item' => 'required',
            'max_qty' => 'required',
            'mi_qty' => 'required',
            'free_item' => 'required',
            'f_item_qty' => 'required',
            'f_item_price' => 'required',
            'dis_percen' => 'required',
        ]);
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

            if ($value['area_item'] != '') {
                $zone = $value['area_item'];
                foreach ($zone as $key => $row) {
                    $insert[] = ['prom_id' => $lid, 'zone_id' => $row, 'cont_id' => $this->currentUser->country()->id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
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


    public function show($id)
    {

        if ($this->userMenu->wsmu_read) {
            $country_id = $this->currentUser->country()->id;
            $depot = collect(DB::connection($this->db)->select("SELECT
            t1.`id`,
            t1.`prom_name`,
            t1.`prom_code`,
            t1.`prom_sdat`,
            t1.`prom_edat`,
            t1.`lfcl_id`,
            IF(t1.`prom_nztp` = '0', 'Nationally', 'Zonal') AS promotionType,
            t2.slgp_name,
            t2.id                                           AS slgp_id,
            t3.prdt_sitm as item_id,
            t4.amim_name                                    AS buy_item,
            t4.id                                           AS buy_item_id,
            t4.amim_code                                    AS buy_item_code,
            t3.prdt_mbqt                                    AS item_m_qty,
            t3.prdt_mnbt                                    AS item_min_qty,
            t5.amim_name                                    AS free_item,
            t5.amim_code                                    AS free_item_code,
            t3.prdt_fiqt                                    AS free_item_qty,
            t3.prdt_fipr                                    AS free_item_price,
            t3.prdt_disc                                    AS discount_percentage
            FROM `tm_prom` t1
            LEFT JOIN tm_slgp t2 ON t1.`slgp_id` = t2.id
            LEFT JOIN tt_prdt t3 ON t1.`id` = t3.prom_id
            LEFT JOIN tm_amim t4 ON t3.prdt_sitm = t4.id
            LEFT JOIN tm_amim t5 ON t3.prdt_fitm = t5.id
            WHERE t1.id = '$id'"))->first();
            $salesGroups = DB::connection($this->db)->select("SELECT
              t2.id        AS slgp_id,
              t2.slgp_name AS slgp_name,
              t2.slgp_code AS slgp_code
            FROM tm_slgp AS t2
            WHERE t2.cont_id = '$country_id'
            ORDER BY t2.id, t2.slgp_name");

            $zones = DB::connection($this->db)->select("SELECT
            t2.id as id,
              t2.zone_name as zone_name,
              t2.zone_code as zone_code
            FROM `tt_pznt` t1 INNER JOIN tm_zone t2 ON t1.`zone_id` = t2.id
            WHERE t1.`prom_id` = '$id'");
            // dd($depot->promotionType);
            //dd($depot);
            return view('Promotion.show')->with('depot', $depot)->with('zones', $zones)->with('salesGroups', $salesGroups);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $country_id = $this->currentUser->country()->id;
            $Promotion = Promotion::on($this->db)->findorfail($id);
            $editPromotion = collect(DB::connection($this->db)->select("SELECT id,`prom_sdat`,`prom_edat`,`prom_nztp`, `prom_name`, `prom_code`  FROM `tm_prom` WHERE `id`='$id'"))->first();

            $eZone = DB::connection($this->db)->select("SELECT
  t1.id                        AS id,
  t1.`zone_name`               AS zone_name,
  t1.`zone_code`               AS zone_code,
  IF(t2.zone_id !='','checked','') AS p_status
FROM `tm_zone` t1 LEFT JOIN tt_pznt t2 ON t1.`id` = t2.zone_id AND t2.prom_id = '$id'");
            // $companys = Company::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('Promotion.edit')->with("editPromotion", $editPromotion)->with("eZone", $eZone)->with("Promotion", $Promotion);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $value, $id)
    {
        $Promotion = Promotion::on($this->db)->findorfail($id);
        $Promotion->prom_sdat = $value['startDate'];
        $Promotion->prom_edat = $value['endDate'];

        $Promotion->aemp_eusr = $this->currentUser->employee()->id;
        $Promotion->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $promotion = Promotion::on($this->db)->findorfail($id);
            $promotion->lfcl_id = $promotion->lfcl_id == 1 ? 2 : 1;
            $promotion->aemp_iusr = $this->currentUser->employee()->id;
            $promotion->aemp_eusr = $promotion->updated_coun + 1;
            $promotion->save();
            return redirect()->back()->with('success', 'successfully Updated');
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
            $cat_list = DB::connection($this->db)->select("SELECT `id`,`issc_name`,`issc_code` FROM `tm_issc` WHERE 1");
            return view('Promotion.create2')->with("salesGroups", $salesGroups)->with("cat_list", $cat_list)->with("zones", $zones)->with("add_salesGroups", $salesGroups)->with('permission', $this->userMenu);
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

        $endDate = $request['endDate'];
        $startDate = $request['startDate'];
        $slgp_idds = $request['slgp_idds'];
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

        $insert = ['prms_name' => $p_name, 'prms_code' => $Code, 'prms_sdat' => $startDate, 'prms_edat' => $endDate,
            'prmr_qftp' => $qualifier_type, 'prmr_qfct' => $promotion_label, 'prmr_ditp' => $discount_type,
            'prmr_ctgp' => $promotion_type, 'prmr_qfgp' => $slgp_idds, 'prmr_qfon' => $order_qualifier,
            'prmr_qfln' => $offer_category, 'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];

        if (!empty($insert)) {
            $p_id = DB::connection($this->db)->table('tm_prmr')->insertGetId($insert);
        }

        /*$sql = "INSERT INTO `tm_prmr`(`prms_name`, `prms_code`, `prms_sdat`, `prms_edat`, `prmr_qftp`, `prmr_qfct`, `prmr_ditp`,
 `prmr_ctgp`, `prmr_qfgp`, `prmr_qfon`, `prmr_qfln`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `var`)
 VALUES ('$p_name', '$Code','$startDate', '$endDate', '$qualifier_type','$promotion_label','$discount_type', '$promotion_type',
 '$slgp_idds','$order_qualifier','$offer_category', '2', '1','$cuser', '$uuser','1')";*/

        /*if (!empty($insert)) {
            DB::connection($this->db)->table('tt_pznt')->insert($insert);
        }*/

        if ($promotion_slab_qua == 'Quantity') {
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
        } else {

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

        if (!empty($sql2)) {
            DB::connection($this->db)->table('tm_prsb')->insert($sql2);
        }


        for ($j = 0; $j < $no_of_item; $j++) {
            if ($item_type[$j] == 'order') {
                $item = $item_list[$j];
                $sql3[] = ['prmr_id' => $p_id, 'amim_id' => $item, 'prmd_modr' => $p_id, 'prmd_mosl' => 0,
                    'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];

                /*$sql3[] = "INSERT INTO `tm_prmd`(`prmr_id`, `amim_id`, `prmd_modr`, `prmd_mosl`, `cont_id`, `lfcl_id`, `aemp_iusr`,
 `aemp_eusr`,`var`) VALUES ('3','$item_list[$j]','3','0','2','1','$cuser','$uuser','1')";*/
            } else {
                $item = $item_list[$j];
                $sql5[] = ['prmr_id' => $p_id, 'amim_id' => $item, 'prmd_modr' => $p_id, 'prmf_qnty' => 0, 'prmf_amnt' => 0, 'prmf_damt' => 0,
                    'cont_id' => $cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $cuser, 'aemp_eusr' => $uuser, 'var' => 1];

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

        if ($pro_type == '1') {
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
        }

        $country_id = $this->currentUser->country()->id;
        $salesGroups = DB::connection($this->db)->select("SELECT
                      t2.id        AS slgp_id,
                      t2.slgp_name AS slgp_name,
                      t2.slgp_code AS slgp_code
                    FROM tm_slgp AS t2
                    WHERE t2.cont_id = '$country_id'
                    ORDER BY t2.id, t2.slgp_name");
        $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE 1");
        return view('Promotion.create2')->with('success', 'Promotion Created Successfully')->with("salesGroups", $salesGroups)->with("zones", $zones)->with("add_salesGroups", $salesGroups)->with('permission', $this->userMenu);

        //return view('Promotion.create2')->with('success', 'Promotion Created Successfully');
        //return redirect()->back()->with('success', 'Promotion Created Successfully');
        //dd($p_id);
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
    public function getBulkPromotionPanel(){
        return view('Promotion.BulkPromotion.bulk_prom')->with('permission',$this->userMenu);
    }
    public function getBulkPromotionFormat(){
        return Excel::download(new BulkPromUpload(),'bulk_promotion' . date("Y-m-d") . '.xlsx' );
    }
    public function addBulkPromotion(Request $request){
        if ($request->hasFile('prom_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new BulkPromUpload(), $request->file('prom_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e);
            }
        }
        return back()->with('danger', ' File Not Found');
    }

}
