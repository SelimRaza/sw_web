<?php

namespace App\Http\Controllers\CompanyDataSetup;

use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\CompanySiteMapping;
use App\BusinessObject\OutletPaymentType;
use App\BusinessObject\PriceList;
use App\MasterData\Company;
use App\MasterData\Employee;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class CompanyDataController extends Controller
{
    private $access_key = 'tbld_CompanyDataController';
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
            $companys = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id
FROM tbld_company AS t1 INNER JOIN tbld_company_employee AS t2 ON t1.id = t2.company_id
WHERE t2.emp_id = $emp_id and t1.country_id=$country_id");
            return view('CompanyPanel.company_data.index')->with('companys', $companys)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $company = Company::findorfail($id);
            return view('CompanyPanel.company_data.show')->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $company = Company::findorfail($id);
            return view('CompanyPanel.company_data.edit')->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $company = Company::findorfail($id);
        $company->name = $request->name;
        $company->code = $request->code;
        $company->updated_by = $this->currentUser->employee()->id;
        $company->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $company = Company::findorfail($id);
            $company->status_id = $company->status_id == 1 ? 2 : 1;
            $company->updated_by = $this->currentUser->employee()->id;
            $company->save();
            return redirect('/company-data');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $company = Company::findorfail($id);
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $companyEmployees = CompanyEmployee::where('company_id', '=', $id)->get();
            return view('CompanyPanel.company_data.company_employee')->with("employees", $employees)->with("companyEmployees", $companyEmployees)->with("company", $company)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $departmentEmployee = CompanyEmployee::findorfail($id);
            $departmentEmployee->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $departmentEmployee = CompanyEmployee::where(['company_id' => $id, 'emp_id' => $request->emp_id])->first();
            if ($departmentEmployee == null) {
                $departmentEmployee = new CompanyEmployee();
                $departmentEmployee->emp_id = $request->emp_id;
                $departmentEmployee->company_id = $id;
                $departmentEmployee->country_id = $this->currentUser->employee()->cont_id;
                $departmentEmployee->created_by = $this->currentUser->employee()->id;
                $departmentEmployee->updated_by = $this->currentUser->employee()->id;
                $departmentEmployee->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function companyEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new CompanyEmployee(), 'company_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function companyEmployeeMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::beginTransaction();
                try {
                    Excel::import(new CompanyEmployee(), $request->file('import_file'));
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

    public function siteEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $company = Company::findorfail($id);
            $outletPaymentTypes = OutletPaymentType::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $priceLists = PriceList::where('company_id', '=', $id)->get();
            $companySiteMappings = CompanySiteMapping::where('company_id', '=', $id)->get();
            return view('CompanyPanel.company_data.company_site')->with("companySiteMappings", $companySiteMappings)->with("outletPaymentTypes", $outletPaymentTypes)->with("priceLists", $priceLists)->with("company", $company)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $companySiteMapping = CompanySiteMapping::findorfail($id);
            $companySiteMapping->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $companySiteMapping = CompanySiteMapping::where(['company_id' => $id, 'site_id' => $request->site_id])->first();
            if ($companySiteMapping == null) {
                $companySiteMapping = new CompanySiteMapping();
                $companySiteMapping->site_id = $request->site_id;
                $companySiteMapping->company_id = $id;
                $companySiteMapping->payment_type_id = $request->payment_type_id;
                $companySiteMapping->price_list_id = $request->price_list_id;
                $companySiteMapping->credit_limit = $request->credit_limit;
                $companySiteMapping->limit_days = $request->limit_days;
                $companySiteMapping->country_id = $this->currentUser->employee()->cont_id;
                $companySiteMapping->created_by = $this->currentUser->employee()->id;
                $companySiteMapping->updated_by = $this->currentUser->employee()->id;
                $companySiteMapping->updated_count = 0;
                $companySiteMapping->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                $companySiteMapping->payment_type_id = $request->payment_type_id;
                $companySiteMapping->price_list_id = $request->price_list_id;
                $companySiteMapping->credit_limit = $request->credit_limit;
                $companySiteMapping->limit_days = $request->limit_days;
                $companySiteMapping->updated_by = $this->currentUser->employee()->id;
                $companySiteMapping->updated_count = $companySiteMapping->updated_count + 1;
                $companySiteMapping->save();
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function companySiteMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new CompanySiteMapping(), 'company_site_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function companySiteMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::beginTransaction();
                try {
                    Excel::import(new CompanySiteMapping(), $request->file('import_file'));
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

}
