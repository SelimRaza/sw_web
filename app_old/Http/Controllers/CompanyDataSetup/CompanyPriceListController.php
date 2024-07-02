<?php

namespace App\Http\Controllers\CompanyDataSetup;

use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\HRPolicy;
use App\BusinessObject\HRPolicyEmployee;
use App\BusinessObject\PriceList;
use App\BusinessObject\PriceListDetails;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\SKU;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class CompanyPriceListController extends Controller
{
    private $access_key = 'tbld_price_list';
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
            $priceLists = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id,
  t3.name as company_name
FROM tbld_price_list AS t1 INNER JOIN tbld_company_employee AS t2 ON t1.company_id = t2.company_id
INNER JOIN tbld_company as t3 ON t2.company_id=t3.id
WHERE t2.emp_id = $emp_id and t1.country_id=$country_id");
            return view('CompanyPanel.CompanyPriceList.index')->with("priceLists", $priceLists)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $userCompanys = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id
FROM tbld_company AS t1 INNER JOIN tbld_company_employee AS t2 ON t1.id = t2.company_id
WHERE t2.emp_id = $emp_id and t1.country_id=$country_id");
            return view('CompanyPanel.CompanyPriceList.create')->with('userCompanys', $userCompanys);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $priceList = new PriceList();
        $priceList->name = $request->name;
        $priceList->code = $request->code;
        $priceList->company_id = $request->company_id;
        $priceList->country_id = $this->currentUser->employee()->cont_id;
        $priceList->created_by = $this->currentUser->employee()->id;
        $priceList->updated_by = $this->currentUser->employee()->id;
        $priceList->status_id = 1;
        $priceList->updated_count = 0;
        $priceList->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $priceList = PriceList::findorfail($id);
            return view('CompanyPanel.CompanyPriceList.show')->with('priceList', $priceList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceList = PriceList::findorfail($id);
            $companys = Company::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('CompanyPanel.CompanyPriceList.edit')->with('priceList', $priceList)->with('companys', $companys);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $priceList = PriceList::findorfail($id);
        $priceList->name = $request->name;
        $priceList->code = $request->code;
        $priceList->company_id = $request->company_id;
        $priceList->updated_by = $this->currentUser->employee()->id;
        $priceList->updated_count = $priceList->updated_count + 1;
        $priceList->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $priceList = PriceList::findorfail($id);
            $priceList->status_id = $priceList->status_id == 1 ? 2 : 1;
            $priceList->updated_by = $this->currentUser->employee()->id;
            $priceList->save();
            return redirect('/company_price_list');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function skuEdit($id)
    {
        if ($this->userMenu->wsmu_read) {
            $priceList = PriceList::findorfail($id);
            $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $priceListDetails = PriceListDetails::where('price_list_id', '=', $id)->get();
            return view('CompanyPanel.CompanyPriceList.price_list_item')->with("priceList", $priceList)->with("priceListDetails", $priceListDetails)->with("skus", $skus)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuDelete($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceListDetails = PriceListDetails::findorfail($id);
            $priceListDetails->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuAdd(Request $request, $id)
    {
        //  dd($request);
        if ($this->userMenu->wsmu_updt) {
            $priceListDetails = PriceListDetails::where(['price_list_id' => $id, 'sku_id' => $request->sku_id])->first();
            if ($priceListDetails == null) {
                $priceListDetails = new PriceListDetails();
                $priceListDetails->sku_id = $request->sku_id;
                $priceListDetails->price_list_id = $id;
                $priceListDetails->sales_price = $request->sales_price;
                $priceListDetails->grv_price = $request->grv_price;
                $priceListDetails->mrp_price = $request->mrp_price;
                $priceListDetails->country_id = $this->currentUser->employee()->cont_id;
                $priceListDetails->created_by = $this->currentUser->employee()->id;
                $priceListDetails->updated_by = $this->currentUser->employee()->id;
                $priceListDetails->updated_count = 0;
                $priceListDetails->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                $priceListDetails->sales_price = $request->sales_price;
                $priceListDetails->grv_price = $request->grv_price;
                $priceListDetails->mrp_price = $request->mrp_price;
                $priceListDetails->country_id = $this->currentUser->employee()->cont_id;
                $priceListDetails->created_by = $this->currentUser->employee()->id;
                $priceListDetails->updated_by = $this->currentUser->employee()->id;
                $priceListDetails->updated_count = $priceListDetails->updated_count+1;
                $priceListDetails->save();
                return redirect()->back()->with('success', 'successfully Updated');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function skuUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            return Excel::download(new PriceListDetails(), 'price_list_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new PriceListDetails(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

}
