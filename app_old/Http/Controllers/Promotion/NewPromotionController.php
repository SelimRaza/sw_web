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

class NewPromotionController extends Controller
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
            if ($request->has('search_text')) {
                $q = request('search_text');
                $promotion = DB::connection($this->db)->table('tm_prom as t1')
                    ->Join('tm_slgp as t2', 't1.slgp_id', '=', 't2.id')
                    ->Join('tt_prdt as t3', 't1.id', '=', 't3.prom_id')
                    ->Join('tm_amim as t4', 't3.prdt_sitm', '=', 't4.id')
                    ->select(
                        't1.id AS prom_id',
                        't1.prom_name',
                        't1.prom_code',
                        't1.prom_sdat',
                        't1.prom_edat',
                        't1.prom_type',
                        't1.prom_nztp',
                        't2.slgp_name',
                        't4.amim_code',
                        't4.amim_name',
                        't1.lfcl_id'
                    )->orderByDesc("t1.id")
                    ->where(function ($query) use ($q) {
                        $query->where('t1.prom_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.prom_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.prom_sdat', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.prom_edat', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.slgp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.amim_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.amim_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $promotion = DB::connection($this->db)->table('tm_prom as t1')
                    ->Join('tm_slgp as t2', 't1.slgp_id', '=', 't2.id')
                    ->Join('tt_prdt as t3', 't1.id', '=', 't3.prom_id')
                    ->Join('tm_amim as t4', 't3.prdt_sitm', '=', 't4.id')
                    ->select(
                        't1.id AS prom_id',
                        't1.prom_name',
                        't1.prom_code',
                        't1.prom_sdat',
                        't1.prom_edat',
                        't1.prom_type',
                        't1.prom_nztp',
                        't2.slgp_name',
                        't4.amim_code',
                        't4.amim_name',
                        't1.lfcl_id'
                    )->orderByDesc("t1.id")
                    ->paginate(100);
            }
            return view('Promotion.index')->with('promotion', $promotion)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
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
        //dd($value);
        DB::connection($this->db)->beginTransaction();
        try {
            $promotion = new Promotion();
            $promotionDetails = new PromotionDetails();


            $promotion->setConnection($this->db);
            $promotion->prom_name = $value['name'];
            $promotion->prom_code = $value['Code'];
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
            $depot = collect(DB::connection($this->db)->select("SELECT
t1.`prom_name`,
t1.`prom_code`,
t1.`prom_sdat`,
t1.`prom_edat`,
IF(t1.`prom_nztp` = '0', 'Nationally', 'Zonal') AS promotionType,
t2.slgp_name,
t2.id                                           AS slgp_id,
t3.prdt_sitm as item_id,
t4.amim_name                                    AS buy_item,
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
            $zones = DB::connection($this->db)->select("SELECT
t2.id as id,
  t2.zone_name as zone_name,
  t2.zone_code as zone_code
FROM `tt_pznt` t1 INNER JOIN tm_zone t2 ON t1.`zone_id` = t2.id
WHERE t1.`prom_id` = '$id'");
            //dd($zones);
            return view('Promotion.show')->with('depot', $depot)->with('zones', $zones);
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

    /*public function zoneMappingDelete(Request $value, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            if ($value['zoneId'] != '') {
                $zone = $value['zoneId'];
                if (!empty($zone)) {
                    DB::table('tt_pznt')->whereIn('id', $zone)->delete();
                }
            }
            return redirect('/promotion')->with('success', 'successfully Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }*/

    /*public function zoneMappingShow($id)
    {
        $Promotion = Promotion::on($this->db)->findorfail($id);
        $eZone = DB::connection($this->db)->select("SELECT
  t1.id                        AS id,
  t1.`zone_name`               AS zone_name,
  t1.`zone_code`               AS zone_code,
  IF(t2.zone_id !='','checked','') AS p_status
FROM `tm_zone` t1 LEFT JOIN tt_pznt t2 ON t1.`id` = t2.zone_id AND t2.prom_id = '$id'");
        return view('Promotion.zone_add')->with("eZone", $eZone)->with("Promotion",$Promotion);
    }*/

    public function zoneMappingAdd(Request $request, $id)
    {
        /*dd($request);
        dd($id);*/
        DB::connection($this->db)->beginTransaction();
        try {
            if ($request['zoneId'] != '') {
                $zone = $request['zoneId'];
                DB::connection($this->db)->table('tt_pznt')->where('prom_id', $id)->delete();
                foreach ($zone as $key => $row) {
                    $insert[] = ['prom_id' => $id, 'zone_id' => $row, 'cont_id' => $this->currentUser->country()->id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
                }
                if (!empty($insert)) {
                    DB::connection($this->db)->table('tt_pznt')->insert($insert);
                }
            }
            DB::connection($this->db)->commit();
            //return redirect('/promotion')->with('success', 'successfully Created');
            return redirect('promotion/' . $id . '/edit')->with('success', 'successfully Created');

        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }
    }

    public function depotFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Depot(), 'depot_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function depotInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Depot(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $depot = Depot::on($this->db)->findorfail($id);
            $employees = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $depotEmployees = DepotEmployee::on($this->db)->where('dlrm_id', '=', $id)->get();
            $companys = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('promotion.Depot.depot_employee')->with("companys", $companys)->with("employees", $employees)->with("depotEmployees", $depotEmployees)->with("depot", $depot)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function depotStock($id, Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_read) {
            $depot = Depot::on($this->db)->findorfail($id);
            if ($request->has('search_text')) {
                $q = request('search_text');
                $depotStock = DB::connection($this->db)->table('tblt_depot_stock as t1')
                    ->join('tbld_sku as t2', 't1.sku_id', '=', 't2.id')
                    ->select('t1.depot_id as depot_id', 't1.sku_id as sku_id', 't1.qty as qty', 't2.name as sku_name', 't2.code as sku_code', 't2.ctn_size as ctn_size')
                    ->where(function ($query) use ($q) {
                        $query->where('t1.sku_id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.code', 'LIKE', '%' . $q . '%');
                    })
                    ->where(['t1.depot_id' => $id, 't1.country_id' => $this->currentUser->country()->id])->paginate(100)->setPath('');
            } else {
                $depotStock = DB::connection($this->db)->table('tblt_depot_stock as t1')
                    ->join('tbld_sku as t2', 't1.sku_id', '=', 't2.id')
                    ->select('t1.depot_id as depot_id', 't1.sku_id as sku_id', 't1.qty as qty', 't2.name as sku_name', 't2.code as sku_code', 't2.ctn_size as ctn_size')
                    ->where(['t1.depot_id' => $id, 't1.country_id' => $this->currentUser->country()->id])->paginate(100)->setPath('');
            }
            return view('Depot.Depot.depot_stock')->with("depotStock", $depotStock)->with("depot", $depot)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $depotEmployee = DepotEmployee::on($this->db)->findorfail($id);
            $depotEmployee->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $depotEmployee = DepotEmployee::on($this->db)->where(['dlrm_id' => $id, 'aemp_id' => $request->emp_id])->first();
            if ($depotEmployee == null) {
                $depotEmployee = new DepotEmployee();
                $depotEmployee->setConnection($this->db);
                $depotEmployee->aemp_id = $request->emp_id;
                $depotEmployee->acmp_id = $request->acmp_id;
                $depotEmployee->dlrm_id = $id;
                $depotEmployee->cont_id = $this->currentUser->country()->id;
                $depotEmployee->lfcl_id = 1;
                $depotEmployee->aemp_iusr = $this->currentUser->employee()->id;
                $depotEmployee->aemp_eusr = $this->currentUser->employee()->id;
                $depotEmployee->var = 1;
                $depotEmployee->attr1 = '';
                $depotEmployee->attr2 = '';
                $depotEmployee->attr3 = 0;
                $depotEmployee->attr4 = 0;
                $depotEmployee->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function depotEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new DepotEmployee(), 'depot_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function depotEmployeeMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new DepotEmployee(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function filterItem(Request $request)

    {
        $slgp_id = $request->slgp_id;
        $items = DB::connection($this->db)->select("SELECT t2.id as id, t2.amim_name as item_name, t2.amim_code as item_code FROM `tl_sgit` t1 
INNER JOIN tm_amim t2 ON t1.`amim_id`=t2.id WHERE t1.slgp_id='$slgp_id'");

        return Response::json($items);

    }

    public function NewPromotion()
    {
        safas;
        /*if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $salesGroups = DB::connection($this->db)->select("SELECT
  t2.id        AS slgp_id,
  t2.slgp_name AS slgp_name,
  t2.slgp_code AS slgp_code
FROM tm_slgp AS t2
WHERE t2.cont_id = '$country_id'
ORDER BY t2.id, t2.slgp_name");
            $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE 1");
            return view('Promotion.create2')->with("salesGroups", $salesGroups)->with("zones", $zones)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }*/
    }

}
