<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\BusinessObject\SalesGroup;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SalesGroupSku;
use App\MasterData\Employee;
use App\MasterData\SKU;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesGroupDataController extends Controller
{
    private $access_key = 'tbld_SalesGroupDataController';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $userSaleGroups = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id
FROM tbld_sales_group AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
WHERE t2.emp_id = $emp_id and t1.country_id=$country_id");
            return view('master_data.sales_group_data.index')->with('salesGroups', $userSaleGroups)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $salesGroup = SalesGroup::findorfail($id);
            return view('master_data.sales_group_data.show')->with('salesGroup', $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::findorfail($id);
            return view('master_data.sales_group_data.edit')->with('salesGroup', $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $salesGroup = SalesGroup::findorfail($id);
        $salesGroup->name = $request->name;
        $salesGroup->code = $request->code;
        $salesGroup->updated_by = $this->currentUser->employee()->id;
        $salesGroup->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroup = SalesGroup::findorfail($id);
            $salesGroup->status_id = $salesGroup->status_id == 1 ? 2 : 1;
            $salesGroup->updated_by = $this->currentUser->employee()->id;
            $salesGroup->save();
            return redirect('/sales-group-data');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::findorfail($id);
            $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $salesGroupSKUs = SalesGroupSku::where('sales_group_id', '=', $id)->get();
            return view('master_data.sales_group_data.sales_group_sku')->with("skus", $skus)->with("salesGroupSKUs", $salesGroupSKUs)->with("salesGroup", $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function skuDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroupSKU1 = SalesGroupSku::findorfail($id);
            $salesGroupSKU1->delete();
            return redirect()->back()->with('success', 'SKU Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function skuAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroupSKU1 = SalesGroupSku::where(['sales_group_id' => $id, 'sku_id' => $request->sku_id])->first();
            if ($salesGroupSKU1 == null) {
                $salesGroupSKU = new SalesGroupSku();
                $salesGroupSKU->sku_id = $request->sku_id;
                $salesGroupSKU->sales_group_id = $id;
                $salesGroupSKU->country_id = $this->currentUser->employee()->cont_id;
                $salesGroupSKU->created_by = $this->currentUser->employee()->id;
                $salesGroupSKU->updated_by = $this->currentUser->employee()->id;
                $salesGroupSKU->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::findorfail($id);
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $salesGroupEmps = SalesGroupEmployee::where('sales_group_id', '=', $id)->get();
            return view('master_data.sales_group_data.sales_group_employee')->with("employees", $employees)->with("salesGroupEmps", $salesGroupEmps)->with("salesGroup", $salesGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroupEmp = SalesGroupEmployee::findorfail($id);
            $salesGroupEmp->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroupEmp = SalesGroupEmployee::where(['sales_group_id' => $id, 'emp_id' => $request->emp_id])->first();
            if ($salesGroupEmp == null) {
                $salesGroupEmp = new SalesGroupEmployee();
                $salesGroupEmp->emp_id = $request->emp_id;
                $salesGroupEmp->sales_group_id = $id;
                $salesGroupEmp->country_id = $this->currentUser->employee()->cont_id;
                $salesGroupEmp->created_by = $this->currentUser->employee()->id;
                $salesGroupEmp->updated_by = $this->currentUser->employee()->id;
                $salesGroupEmp->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

}
