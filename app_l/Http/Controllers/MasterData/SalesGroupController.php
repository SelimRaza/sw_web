<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SalesGroupCategory;
use App\MasterData\SalesGroupCategory as SalesGroupCategoryUpload;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SalesGroupSku;
use App\MasterData\Employee;
use App\MasterData\SKU;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class SalesGroupController extends Controller
{
    private $access_key = 'tm_slgp';
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

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");

            $salesGroups = SalesGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.sales_group.index')->with('salesGroups', $salesGroups)->with('permission', $this->userMenu)->with('acmp',$acmp)->with('dsct',$dsct);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $cont_id = $this->currentUser->country()->id;
            $companies = DB::connection($this->db)->select("SELECT
              t1.id AS id,
              t1.acmp_name  AS name
            FROM tm_acmp AS t1 WHERE t1.cont_id=$cont_id");

            $priceList = DB::connection($this->db)->select("SELECT
              t1.id        AS id,
              t1.plmt_name AS name
            FROM tm_plmt AS t1 WHERE t1.cont_id=$cont_id");

            return view('master_data.sales_group.create')->with("priceList", $priceList)->with("companies", $companies)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $salesGroup = new SalesGroup();
        $salesGroup->setConnection($this->db);
        $salesGroup->slgp_name = $request->slgp_name;
        $salesGroup->slgp_code = $request->slgp_code;
        $salesGroup->acmp_id = $request->acmp_id;
        $salesGroup->plmt_id = $request->plmt_id;
        $salesGroup->cont_id = $this->currentUser->country()->id;
        $salesGroup->lfcl_id = 1;
        $salesGroup->aemp_iusr = $this->currentUser->employee()->id;
        $salesGroup->aemp_eusr = $this->currentUser->employee()->id;
        $salesGroup->save();


        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $salesGroup = SalesGroup::on($this->db)->findorfail($id);
            return view('master_data.sales_group.show')->with('salesGroup', $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {

            $cont_id = $this->currentUser->country()->id;
            $companies = DB::connection($this->db)->select("SELECT
              t1.id AS id,
              t1.acmp_name  AS name
            FROM tm_acmp AS t1 WHERE t1.cont_id=$cont_id");

            $priceList = DB::connection($this->db)->select("SELECT
              t1.id        AS id,
              t1.plmt_name AS name
            FROM tm_plmt AS t1 WHERE t1.cont_id=$cont_id");

            $salesGroup = SalesGroup::on($this->db)->findorfail($id);
            return view('master_data.sales_group.edit')->with("priceList", $priceList)->with('salesGroup', $salesGroup)->with("companies", $companies);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $salesGroup = SalesGroup::on($this->db)->findorfail($id);
        $salesGroup->slgp_name = $request->slgp_name;
        $salesGroup->slgp_code = $request->slgp_code;
        $salesGroup->acmp_id = $request->acmp_id;
        $salesGroup->plmt_id = $request->plmt_id;
        $salesGroup->aemp_eusr = $this->currentUser->employee()->id;
        $salesGroup->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroup = SalesGroup::on($this->db)->findorfail($id);
            $salesGroup->lfcl_id = $salesGroup->lfcl_id == 1 ? 2 : 1;
            $salesGroup->aemp_eusr = $this->currentUser->employee()->id;
            $salesGroup->save();
            return redirect('/sales-group');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::on($this->db)->findorfail($id);

            $skus = SKU::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $categorys = SalesGroupCategory::on($this->db)->where(['slgp_id' => $id, 'lfcl_id' => 1])->get();
            $salesGroupSKUs = DB::connection($this->db)->select("SELECT
                                t3.id        AS id,
                                t4.id        AS sku_id,
                                t4.amim_name AS name,
                                t4.amim_code AS code,
                                t3.lfcl_id,
                                t5.issc_name
                                FROM tm_slgp AS t1
                                INNER JOIN tl_sgit  AS t3 ON t1.id = t3.slgp_id
                                INNER JOIN tm_amim AS t4 ON t3.amim_id = t4.id
                                INNER JOIN tm_issc as t5 On  t3.issc_id=t5.id
                                WHERE t1.id=$id");
            return view('master_data.sales_group.sales_group_sku')->with('permission', $this->userMenu)->with("skus", $skus)->with("categorys", $categorys)->with("salesGroupSKUs", $salesGroupSKUs)->with("salesGroup", $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function skuActiveInactive($id)
    {

        if ($this->userMenu->wsmu_delt) {

            $salesGroupSKU1 = SalesGroupSku::on($this->db)->findorfail($id);
            $salesGroupSKU1->lfcl_id = $salesGroupSKU1->lfcl_id == 1 ? 2 : 1;
            $salesGroupSKU1->aemp_eusr = $this->currentUser->employee()->id;
            $salesGroupSKU1->save();
            return redirect()->back()->with('success', 'Done..!');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuDelete($id){

        if ($this->userMenu->wsmu_delt) {

            SalesGroupSku::on($this->db)->where('id', $id)->delete();
            return redirect()->back()->with('success', 'SKU Deleted Successfully..!!');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }

    }

    public function skuAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroupSKU1 = SalesGroupSku::on($this->db)->where(['slgp_id' => $id, 'amim_id' => $request->sku_id])->first();
            if ($salesGroupSKU1 == null) {
                $salesGroupSKU = new SalesGroupSku();
                $salesGroupSKU->setConnection($this->db);
                $salesGroupSKU->amim_id = $request->sku_id;
                $salesGroupSKU->issc_id = $request->category_id;
                $salesGroupSKU->slgp_id = $id;
                $salesGroupSKU->sgit_moqt = 1;
                $salesGroupSKU->cont_id = $this->currentUser->country()->id;
                $salesGroupSKU->lfcl_id = 1;
                $salesGroupSKU->aemp_iusr = $this->currentUser->employee()->id;
                $salesGroupSKU->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupSKU->var = 1;
                $salesGroupSKU->attr1 = '';
                $salesGroupSKU->attr2 = '';
                $salesGroupSKU->attr3 = 0;
                $salesGroupSKU->attr4 =1;
                $salesGroupSKU->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function categoryEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::on($this->db)->findorfail($id);
            $salesGroupCategorys = DB::connection($this->db)->select("SELECT
                                    t2.id        AS id,
                                    t2.issc_name        AS category_name,
                                    t2.issc_code        AS category_code,
                                    t2.lfcl_id
                                    FROM tm_slgp AS t1
                                    INNER JOIN tm_issc  AS t2 ON t1.id = t2.slgp_id
                                    WHERE t1.id=$id AND t2.lfcl_id = 1");
            return view('master_data.sales_group.sales_group_category')->with('permission', $this->userMenu)->with("salesGroupCategorys", $salesGroupCategorys)->with("salesGroup", $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function categoryDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {

            $salesGroup = SalesGroupCategory::on($this->db)->findorfail($id);
            $salesGroup->lfcl_id = $salesGroup->lfcl_id == 1 ? 2 : 1;
            $salesGroup->aemp_eusr = $this->currentUser->employee()->id;
            $salesGroup->save();
            return redirect()->back()->with('success', 'Category Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function categoryAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroupSKU = new SalesGroupCategory();
            $salesGroupSKU->setConnection($this->db);
            $salesGroupSKU->issc_name = $request->category_name;
            $salesGroupSKU->issc_code = $request->category_code;
            $salesGroupSKU->issc_seqn = $request->category_seqc;
            $salesGroupSKU->issc_opst = 0;
            $salesGroupSKU->slgp_id = $id;
            $salesGroupSKU->cont_id = $this->currentUser->country()->id;
            $salesGroupSKU->lfcl_id = 1;
            $salesGroupSKU->aemp_iusr = $this->currentUser->employee()->id;
            $salesGroupSKU->aemp_eusr = $this->currentUser->employee()->id;
            $salesGroupSKU->var = 1;
            $salesGroupSKU->attr1 = '';
            $salesGroupSKU->attr2 = '';
            $salesGroupSKU->attr3 = 0;
            $salesGroupSKU->attr4 = 0;
            $salesGroupSKU->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceList = PriceList::on($this->db)->get();
            $zoneList = DB::connection($this->db)->select("SELECT
                        t1.id as zone_id,
                        concat(t1.zone_name,'(',t1.zone_code,')', ' < ', t2.dirg_name) AS zone_name
                        FROM tm_zone t1
                        INNER JOIN tm_dirg AS t2 ON t1.dirg_id = t2.id");
                                    $salesGroupEmps = DB::connection($this->db)->select("SELECT
                        t1.id                                                             AS sgsm_id,
                        t1.aemp_id                                                        AS aemp_id,
                        t2.aemp_usnm                                                      AS aemp_usnm,
                        t2.aemp_name                                                      AS aemp_name,
                        concat(t3.zone_name, '(', t3.zone_code, ')', ' < ', t4.dirg_name) AS zone_name
                        FROM tl_sgsm t1
                        INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                        INNER JOIN tm_zone AS t3 ON t1.zone_id = t3.id
                        INNER JOIN tm_dirg AS t4 ON t3.dirg_id = t4.id
                        WHERE t1.slgp_id = $id");
            //dd($zoneList);
            $salesGroup = SalesGroup::on($this->db)->findorfail($id);

           // $salesGroupEmps = SalesGroupEmployee::on($this->db)->where('slgp_id', '=', $id)->get();
            //dd($salesGroupEmps);
            return view('master_data.sales_group.sales_group_employee')->with('permission', $this->userMenu)/*->with("employees", $employees)*/
            ->with("priceList", $priceList)->with("salesGroupEmps", $salesGroupEmps)->with("salesGroup", $salesGroup)->with("zoneList", $zoneList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroupEmp = SalesGroupEmployee::on($this->db)->findorfail($id);
            $salesGroupEmp->delete();
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
                $salesGroupEmp = SalesGroupEmployee::on($this->db)->where(['slgp_id' => $id, 'aemp_id' => $employee->id])->first();
                if ($salesGroupEmp == null) {
                    $salesGroupEmp = new SalesGroupEmployee();
                    $salesGroupEmp->setConnection($this->db);
                    $salesGroupEmp->aemp_id = $employee->id;
                    $salesGroupEmp->slgp_id = $id;
                    $salesGroupEmp->plmt_id = $request->plmt_id;
                    $salesGroupEmp->zone_id = $request->zone_id;
                    $salesGroupEmp->cont_id = $this->currentUser->country()->id;
                    $salesGroupEmp->lfcl_id = 1;
                    $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->save();
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

    public function groupEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SalesGroupEmployee(), 'group_employee_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function groupEmployeeMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SalesGroupEmployee(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function groupSKUMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SalesGroupSku(), 'group_sku_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function groupSkuMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SalesGroupSku(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function filterDetails(Request $request){
        $cont = $this->currentUser->country()->id;

        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;
        /*$acmp_id = 1;
        $slgp_id = '';*/


        if ($slgp_id != "") {
            $query = "select t1.id, t1.slgp_code, t1.slgp_name, t2.acmp_name, t1.plmt_id from tm_slgp t1 inner join tm_acmp t2 on t1.acmp_id=t2.id WHERE t1.cont_id='$cont' AND t1.id='$slgp_id'";
        }else{
            $query = "select t1.id, t1.slgp_code, t1.slgp_name, t2.acmp_name, t1.plmt_id from tm_slgp t1 inner join tm_acmp t2 on t1.acmp_id=t2.id WHERE t1.cont_id='$cont' AND t1.acmp_id='$acmp_id'";
        }
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        return $srData;
    }


    public function salesGroupCategoryMapping(){
        return view('master_data.sales_group.sales_group_category_upload',['permission'=>$this->userMenu]);
    }

    public function salesGroupCategoryUploadFormat(){
        return Excel::download(new SalesGroupCategoryUpload(), 'sales_group_category_' . date("Y-m-d H:i:s") . '.xlsx');
    }

    public function salesGroupCategoryUpload(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SalesGroupCategoryUpload(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    dd($e);
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
