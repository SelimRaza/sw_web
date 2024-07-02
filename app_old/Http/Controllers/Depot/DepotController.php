<?php

namespace App\Http\Controllers\Depot;

use App\BusinessObject\DepotStock;
use App\BusinessObject\SalesGroup;
use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Depot;
use App\MasterData\DealerLoginUpload;
use App\MasterData\DepotEmployee;
use App\MasterData\DepotMain;
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
use App\MasterData\Thana;
use App\MasterData\WareHouse;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use Excel;
use App\User;
use App\Process\HrisUser;
use App\MasterData\Country;

class DepotController extends Controller
{
    private $access_key = 'tm_dlrm';
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
        $this->currentUser = Auth::user();
        $db_conne = Auth::user()->country()->cont_conn;

        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");


        //$acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $depots = Depot::on($this->db)->where(function ($query) use ($q) {
                    $query->where('dlrm_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('dlrm_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('dlrm_mob1', 'LIKE', '%' . $q . '%')
                        ->orWhere('dlrm_ownm', 'LIKE', '%' . $q . '%')
                        ->orWhere('id', 'LIKE', '%' . $q . '%')
                        ->orWhere('dlrm_adrs', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');
            } else {
                $depots = Depot::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(500);
            }
            // dd($depots);
            return view('Depot.Depot.index')->with('depots', $depots)->with('acmp', $acmp)->with('dsct', $dsct)->with('permission', $this->userMenu)->with('search_text', $q)->with('cont_id', $db_conne);
        } else {
            return view('theme.access_limit');
        }
    }

    public function filterDepotDetails(Request $request)
    {

        $q1 = "";
        $q2 = "";
        $q3 = "";
        $q4 = "";


        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;
        $dist_id = $request->dist_id;
        $than_id = $request->than_id;

        if ($acmp_id != "") {
            $q1 = " AND t1.acmp_id = '$acmp_id'";
        }
        if ($slgp_id != "") {

            $q1 = " AND t1.slgp_id = '$slgp_id'";
        }
        if ($dist_id != "") {

            $q1 = " AND t4.dist_id = '$dist_id'";
        }
        if ($than_id != "") {

            $q1 = " AND t1.than_id = '$than_id'";
        }


        $query = "SELECT t1.id as id, t2.slgp_name, concat(t4.than_code, ' - ',t4.than_name) as than_name, t5.base_name, t1.`dlrm_code`,
                    t1.`dlrm_name`, t1.`dlrm_ownm`, t1.`dlrm_adrs`, t1.`dlrm_mob1`, t1.dlrm_akey, t1.lfcl_id, t1.cont_id, t6.zone_name, t7.dirg_name FROM `tm_dlrm` t1 
                    INNER JOIN tm_slgp t2 ON t1.slgp_id = t2.id 
                    INNER JOIN tm_acmp t3 ON t2.acmp_id=t3.id 
                    INNER JOIN tm_than t4 ON t1.`than_id`=t4.id 
                    INNER JOIN tm_base t5 ON t5.id=t1.base_id 
                    INNER JOIN tm_zone t6 ON t5.zone_id=t6.id
                    INNER JOIN tm_dirg t7 ON t6.dirg_id =t7.id WHERE t1.id !='' " . $q1 . $q2 . $q3 . $q4;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        return $srData;
    }

    public function dealerStatusChange(Request $request){
        $id = $request->id;
        if ($this->userMenu->wsmu_delt) {
            $depot = Depot::on($this->db)->findorfail($id);
            $depot->lfcl_id = $depot->lfcl_id == 1 ? 2 : 1;
            $depot->aemp_eusr = $this->currentUser->employee()->id;
            $depot->save();
            $srData[] = "Dealer status changed successfully!!!";
            return $srData;
        }
    }

    public function dealerLoginCreate(Request $request)
    {

        $id = $request->id;

        $this->currentUser = Auth::user();
        $db_conne = Auth::user()->country()->cont_conn;


        //$id = 6;
        $dealer = DB::connection($this->db)->select("SELECT `dlrm_name`, `dlrm_code`, `dlrm_adrs`, `id`, slgp_id, cont_id FROM `tm_dlrm` WHERE lfcl_id='1' and id='$id'");
        //dd($dealer);
        $name = $dealer[0]->dlrm_name;

        $code = $dealer[0]->dlrm_code;
        $dlrm_adrs = $dealer[0]->dlrm_adrs;
        $idddfdfd = $dealer[0]->id;
        $group_id = $dealer[0]->slgp_id;
        $cont_id = $dealer[0]->cont_id;
        $hashed = Hash::make($code);

        if ($cont_id=='2'){
            $sql = "INSERT INTO dist.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', '$db_conne', '1','1', '$cont_id')";
        }else if ($cont_id=='5'){
            $sql = "INSERT INTO dist_rfl.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', '$db_conne', '1','1', '$cont_id')";
        }else{
            $sql = "INSERT INTO dist.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', '$db_conne', '1','1', '$cont_id')";
        }


        DB::connection($this->db)->select($sql);

        $depot = Depot::on($this->db)->findorfail($id);
        $depot->dlrm_akey = 'Y';

        $depot->save();
        $srData[] = "Dealer user id and password created successfully!!!";
        return $srData;

    }


    public function dealerPassReset(Request $request)
    {

        $id = $request->id;

        $this->currentUser = Auth::user();
        $db_conne = Auth::user()->country()->cont_conn;
        $this->db = Auth::user()->country()->cont_conn;


        //$id = 6;
        $dealer = DB::connection($this->db)->select("SELECT `dlrm_name`, `dlrm_code`, `dlrm_adrs`, `id`, slgp_id, cont_id FROM `tm_dlrm` WHERE lfcl_id='1' and id='$id'");

        $code = $dealer[0]->dlrm_code;

        $cont_id = $dealer[0]->cont_id;
        $hashed = Hash::make($code);

        if ($cont_id=='2'){
            $sql = "update dist.`users` set `password`='$hashed' WHERE `email`='$code'";
        }else if ($cont_id=='5'){
            $sql = "update dist_rfl.`users` set `password`='$hashed' WHERE `email`='$code'";
        }else{
            $sql = "update dist.`users` set `password`='$hashed' WHERE `email`='$code'";
        }


        DB::connection($this->db)->select($sql);

        $depot = Depot::on($this->db)->findorfail($id);
        $depot->dlrm_akey = 'Y';

        $depot->save();
        $srData[] = "Password reset successfully!!! Password same as user id";
        //$srData[] = $hashed;
        return $srData;

    }

    public function dealerLoginUpload(){

        if ($this->userMenu->wsmu_crat) {
            return view('Depot.Depot.login_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function DealerLoginUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new DealerLoginUpload(), 'Dealer_login_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function DealerLoginUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new DealerLoginUpload(), $request->file('import_file'));
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


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $salesGroups = SalesGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $govGeos = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $salesGeos = Base::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('Depot.Depot.create')->with("salesGroups", $salesGroups)->with("salesGeos", $salesGeos)->with("govGeos", $govGeos)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        // dd($request);
        $salesGroup = SalesGroup::on($this->db)->findorfail($request->slgp_id);
        if ($salesGroup != null) {
            DB::connection($this->db)->beginTransaction();
            try {
                $depotMain = new DepotMain();
                $depotMain->setConnection($this->db);
                $depotMain->dpot_name = $request->dlrm_name;
                $depotMain->dpot_code = $request->dlrm_code;
                $depotMain->acmp_id = $salesGroup->acmp_id;
                $depotMain->cont_id = $this->currentUser->country()->id;
                $depotMain->lfcl_id = 1;
                $depotMain->aemp_iusr = $this->currentUser->employee()->id;
                $depotMain->aemp_eusr = $this->currentUser->employee()->id;
                $depotMain->save();

                $depotWare = new WareHouse();
                $depotWare->setConnection($this->db);
                $depotWare->whos_name = $request->dlrm_name;
                $depotWare->whos_code = $request->dlrm_code;
                $depotWare->dpot_id = $depotMain->id;
                $depotWare->cont_id = $this->currentUser->country()->id;
                $depotWare->lfcl_id = 1;
                $depotWare->aemp_iusr = $this->currentUser->employee()->id;
                $depotWare->aemp_eusr = $this->currentUser->employee()->id;
                $depotWare->save();

                $depot = new Depot();
                $depot->setConnection($this->db);
                $depot->dlrm_code = $request->dlrm_code;
                $depot->dlrm_name = $request->dlrm_name;
                $depot->dlrm_olnm = isset($request->dlrm_olnm) ? $request->dlrm_olnm : "";
                $depot->dlrm_adrs = isset($request->dlrm_adrs) ? $request->dlrm_adrs : "";
                $depot->dlrm_olad = isset($request->dlrm_olad) ? $request->dlrm_olad : "";
                $depot->dlrm_ownm = isset($request->dlrm_ownm) ? $request->dlrm_ownm : "";
                $depot->dlrm_olon = isset($request->dlrm_olon) ? $request->dlrm_olon : "";
                $depot->dlrm_mob1 = isset($request->dlrm_mob1) ? $request->dlrm_mob1 : "";
                $depot->dlrm_mob2 = isset($request->dlrm_mob2) ? $request->dlrm_mob2 : "";
                $depot->dlrm_emal = isset($request->dlrm_emal) ? $request->dlrm_emal : "";
                $depot->dlrm_zpcd = isset($request->dlrm_zpcd) ? $request->dlrm_zpcd : "";
                $depot->slgp_id = $request->slgp_id;
                $depot->acmp_id = $salesGroup->acmp_id;
                $depot->whos_id = $depotWare->id;
                $depot->dptp_id = 1;
                $depot->than_id = $request->than_id;
                $depot->base_id = $request->base_id;
                $depot->cont_id = $this->currentUser->country()->id;
                $depot->lfcl_id = 1;
                $depot->aemp_iusr = $this->currentUser->employee()->id;
                $depot->aemp_eusr = $this->currentUser->employee()->id;
                $depot->save();
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'successfully Created');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
                // return redirect()->back()->withInput()->with('danger', 'not Created');
            }
        } else {
            return redirect()->back()->withInput()->with('danger', 'not Created');
        }

    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $depot = collect(DB::connection($this->db)->select("SELECT
  t1.id AS id,
  t2.slgp_name AS slgp_name,
  t3.base_name AS base_name,
  t4.than_name AS than_name,
  t1.dlrm_name AS name,
  t1.dlrm_code AS code,
  t1.dlrm_olnm AS ln_name,
  t1.dlrm_adrs AS address,
  t1.dlrm_olad AS ln_address,
  t1.dlrm_ownm    owner_name,
  t1.dlrm_olon AS ln_owner_name,
  t1.dlrm_mob1 AS mobile_1,
  t1.dlrm_mob2 AS mobile_2,
  t1.dlrm_emal AS email,
  t1.dlrm_zpcd AS zip_code
FROM tm_dlrm AS t1
  INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
  INNER JOIN tm_base AS t3 ON t1.base_id = t3.id
  INNER JOIN tm_than AS t4 ON t1.than_id = t4.id
WHERE t1.id = $id"))->first();
            return view('Depot.Depot.show')->with('depot', $depot);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $depot = Depot::on($this->db)->findorfail($id);
            $salesGroups = SalesGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $govGeos = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $salesGeos = Base::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('Depot.Depot.edit')->with("salesGroups", $salesGroups)->with("salesGeos", $salesGeos)->with("govGeos", $govGeos)->with('depot', $depot);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function dealerLoginCreateForm(){
        if ($this->userMenu->wsmu_updt) {
            $id=2;
            $depot = Depot::on($this->db)->findorfail($id);
            $salesGroups = SalesGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $govGeos = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $salesGeos = Base::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('Depot.Depot.dealer_login')->with("salesGroups", $salesGroups)->with("salesGeos", $salesGeos)->with("govGeos", $govGeos)->with('depot', $depot);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {

        $salesGroup = SalesGroup::on($this->db)->findorfail($request->slgp_id);
        if ($salesGroup != null) {
            DB::connection($this->db)->beginTransaction();
            try {
                $depot = Depot::on($this->db)->findorfail($id);
                $depot->dlrm_code = $request->dlrm_code;
                $depot->dlrm_name = $request->dlrm_name;
                $depot->dlrm_olnm = isset($request->dlrm_olnm) ? $request->dlrm_olnm : "";
                $depot->dlrm_adrs = isset($request->dlrm_adrs) ? $request->dlrm_adrs : "";
                $depot->dlrm_olad = isset($request->dlrm_olad) ? $request->dlrm_olad : "";
                $depot->dlrm_ownm = isset($request->dlrm_ownm) ? $request->dlrm_ownm : "";
                $depot->dlrm_olon = isset($request->dlrm_olon) ? $request->dlrm_olon : "";
                $depot->dlrm_mob1 = isset($request->dlrm_mob1) ? $request->dlrm_mob1 : "";
                $depot->dlrm_mob2 = isset($request->dlrm_mob2) ? $request->dlrm_mob2 : "";
                $depot->dlrm_emal = isset($request->dlrm_emal) ? $request->dlrm_emal : "";
                $depot->dlrm_zpcd = isset($request->dlrm_zpcd) ? $request->dlrm_zpcd : "";
                $depot->slgp_id = $request->slgp_id;
                $depot->acmp_id = $salesGroup->acmp_id;
                $depot->than_id = $request->than_id;
                $depot->base_id = $request->base_id;
                $depot->aemp_eusr = $this->currentUser->employee()->id;
                $depot->save();

                $depotWh = WareHouse::on($this->db)->findorfail($depot->whos_id);
                $depotWh->whos_name = $request->dlrm_name;
                $depotWh->whos_code = $request->dlrm_code;
                $depotWh->save();

                $depotMain = DepotMain::on($this->db)->findorfail($depotWh->dpot_id);
                $depotMain->dpot_name = $request->dlrm_name;
                $depotMain->dpot_code = $request->dlrm_code;
                $depotMain->save();


                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'successfully Updated');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
                // return redirect()->back()->withInput()->with('danger', 'not Created');
            }
        }
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $depot = Depot::on($this->db)->findorfail($id);
            $depot->lfcl_id = $depot->lfcl_id == 1 ? 2 : 1;
            $depot->aemp_eusr = $this->currentUser->employee()->id;
            $depot->save();
            return redirect('/depot');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
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
            // $employees = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $depotEmployees = DepotEmployee::on($this->db)->where('dlrm_id', '=', $id)->get();
            $companys = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('Depot.Depot.depot_employee')->with("companys", $companys)/*->with("employees", $employees)*/
            ->with("depotEmployees", $depotEmployees)->with("depot", $depot)->with('permission', $this->userMenu);
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
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->user_name])->first();
            if ($employee != null) {
                $depotEmployee = DepotEmployee::on($this->db)->where(['dlrm_id' => $id, 'aemp_id' => $employee->id])->first();
                if ($depotEmployee == null) {
                    $depotEmployee = new DepotEmployee();
                    $depotEmployee->setConnection($this->db);
                    $depotEmployee->aemp_id = $employee->id;
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
                    return back()->withInput()->with('danger', 'Already exist');
                }
            } else {
                return back()->withInput()->with('danger', 'Wrong User name');
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

    public function testSgsm()
    {
        /*$lld[]=['sr_id' => "insert"];
        DB::connection($country->cont_conn)->table('test45')->insert($lld);*/

        $query1 = "SELECT
  t1.name                              AS aemp_name,
  ''                                   AS aemp_onme,
  t1.name                              AS aemp_stnm,
  t1.mobile                            AS aemp_mob1,
  t1.mobile                            AS aemp_dtsm,
  t1.email                             AS aemp_emal,
  0                                    AS aemp_otml,
  ''                                   AS aemp_emcc,
  0                                    AS aemp_lued,
  1                                    AS edsg_id,
  if(t1.designation_name = 'SR' OR t1.designation_name = 'Senior Sales Representative' OR t1.designation_name = 'Sales Representative' OR t1.designation_name = 'se' OR t1.designation_name = 'dsr', 1, 2) AS role_id,
  t1.sr_id                             AS aemp_usnm,
  ''                                   AS aemp_pimg,
  ''                                   AS aemp_picn,
  1                                    AS aemp_mngr,
  1                                    AS aemp_lmid,
  0                                    AS aemp_aldt,
  127                                  AS aemp_lcin,
  0                                    AS aemp_lonl,
  ''                                   AS aemp_utkn,
  0                                    AS site_id,
  0                                    AS aemp_crdt,
  1                                    AS aemp_issl,
  2                                    AS cont_id,
  if(t1.status = 'Y', 1, 2)            AS lfcl_id,
  if(t1.designation_name = 'SR' OR t1.designation_name = 'Senior Sales Representative' OR t1.designation_name = 'Sales Representative' OR t1.designation_name = 'se' OR t1.designation_name = 'dsr' , 1, 2) AS amng_id,
  2                                    AS aemp_iusr,
  2                                    AS aemp_eusr,
  t1.manager_code,
  t1.id
FROM tbld_new_employee AS t1
WHERE t1.sr_id = 'D33681'";

        $result = DB::connection($this->db)->select($query1);
        //dd($result);
        if($this->currentUser->country()->id =='2'){
            $d_slgp ='39';
            $d_zone ='1';
        }
        if ($this->currentUser->country()->id =='5'){
            $d_slgp ='90';
            $d_zone ='49';
        }
        foreach ($result as $t) {
            if ($t->manager_code != '') {
                $manager = Employee::on($this->db)->where(['aemp_usnm' => $t->manager_code])->first();
                if ($manager != null) {
                    $t->aemp_mngr = $manager->id !="" ? $manager->id : 1;
                    $t->aemp_lmid = $manager->id !="" ? $manager->id : 1;
                    $t->aemp_slgp = $manager->slgp_id !="" ? $manager->slgp_id : $d_slgp;
                    $t->aemp_zone = $manager->zone_id !="" ? $manager->zone_id : $d_zone;

                }else{
                    $t->aemp_slgp = $d_slgp;
                    $t->aemp_zone = $d_zone;
                }
            }else{
                $t->aemp_slgp = $d_slgp;
                $t->aemp_zone = $d_zone;
            }
            $user = User::where('email', '=', $t->aemp_usnm)->first();
            if ($user == null) {
                $user = User::create([
                    'name' => $t->aemp_name,
                    'email' => trim($t->aemp_usnm),
                    'password' => bcrypt(trim($t->aemp_usnm)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $this->currentUser->country()->id,
                ]);
            }
            $user->remember_token = '';
            $user->lfcl_id = $t->lfcl_id;
            $user->cont_id = $this->currentUser->country()->id;
            $user->save();
//DB::connection($country->cont_conn)->table('test45')->insert($lld);
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $t->aemp_usnm])->first();
            if ($employee == null) {
                if ($t->lfcl_id == 1) {
                    DB::connection($this->db)->table('tm_aemp')->insert(
                        array(
                            'aemp_name' => $t->aemp_name,
                            'aemp_onme' => $t->aemp_onme,
                            'aemp_stnm' => $t->aemp_stnm,
                            'aemp_mob1' => $t->aemp_mob1,
                            'aemp_dtsm' => $t->aemp_dtsm,
                            'aemp_emal' => $t->aemp_emal,
                            'aemp_otml' => $t->aemp_otml,
                            'aemp_emcc' => $t->aemp_emcc,
                            'aemp_lued' => $user->id,
                            'edsg_id' => $t->edsg_id,
                            'role_id' => $t->role_id,
                            'aemp_usnm' => $t->aemp_usnm,
                            'aemp_pimg' => $t->aemp_pimg,
                            'aemp_picn' => $t->aemp_picn,
                            'aemp_mngr' => $t->aemp_mngr,
                            'aemp_lmid' => $t->aemp_lmid,
                            'aemp_aldt' => $t->aemp_aldt,
                            'aemp_lcin' => $t->aemp_lcin,
                            'aemp_lonl' => $t->aemp_lonl,
                            'aemp_utkn' => $t->aemp_utkn,
                            'site_id' => $t->site_id,
                            'aemp_crdt' => $t->aemp_crdt,
                            'aemp_issl' => $t->aemp_issl,
                            'aemp_asyn' => 'Y',
                            'cont_id' => $this->currentUser->country()->id,
                            'lfcl_id' => $t->lfcl_id,
                            'amng_id' => $t->amng_id,
                            'aemp_iusr' => $t->aemp_iusr,
                            'aemp_eusr' => $t->aemp_eusr,
                            'slgp_id' => $t->aemp_slgp,
                            'zone_id' => $t->aemp_zone,
                        )
                    );
                }
            } else {
                //DB::connection($country->cont_conn)->table('test45')->insert($lld);
                $employee->lfcl_id = $t->lfcl_id;
                //   $employee->amng_id = $t->amng_id;
                $employee->aemp_eusr = $t->aemp_eusr;
                $employee->save();
            }


            $employeeID = Employee::on($this->db)->where(['aemp_usnm' => $t->aemp_usnm])->first();
            if ($employeeID != null) {
                DB::connection($this->db)->table('tl_sgsm')->insertOrIgnore(
                    array(
                        'aemp_id' => $employeeID->id,
                        'slgp_id' => $t->aemp_slgp,
                        'plmt_id' => $t->aemp_slgp,
                        'zone_id' => $t->aemp_zone,
                        'cont_id' => $this->currentUser->country()->id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $t->aemp_iusr,
                        'aemp_eusr' => $t->aemp_iusr,
                    )
                );
            }

            DB::connection($this->db)->table('tbld_new_employee')->where(['id' => $t->id])->update(
                array(
                    'sync_status' => 1,
                )
            );
        }

    }

    public function testSgsmHrisUser(Request $request){
        //echo "adfasfsa";

        $hrdata = new HrisUser();
        $hrdata->createOrUpdateUser((new Country())->country(2));

    }

}
