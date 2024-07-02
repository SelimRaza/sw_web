<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\CompanyEmployee;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    private $access_key = 'tm_acmp';
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
            $companies = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.company.index')->with("companies", $companies)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.company.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function store(Request $request)
    {
             DB::connection($this->db)->beginTransaction();
              try {
                  $company = new Company();
                  $company->setConnection($this->db);
                  $company->acmp_name = $request->name;
                  $company->acmp_code = $request->code;
                  $company->acmp_note = !isset($request->acmp_note)?'':$request->acmp_note;
                  $company->acmp_snam = !isset($request->acmp_snam)?'':$request->acmp_snam;
                  $company->acmp_nvat = !isset($request->acmp_nvat)?'':$request->acmp_nvat;
                  $company->acmp_nexc = !isset($request->acmp_nexc)?'':$request->acmp_nexc;
                  $company->acmp_creg = !isset($request->acmp_creg)?'':$request->acmp_creg;
                  $company->acmp_addr = $request->acmp_addr;
                  $company->acmp_vats = !isset($request->acmp_vats)?'0':1;
                  $company->acmp_titl = $request->acmp_titl;
                  $company->acmp_rttl = $request->acmp_rttl;
                  $company->acmp_scod = !isset($request->acmp_scod)?'':$request->acmp_scod;
                  $company->acmp_acod = !isset($request->acmp_acod)?'':$request->acmp_acod;
                  $company->acmp_iban = !isset($request->acmp_iban)?'':$request->acmp_iban;
                  $company->acmp_bank = !isset($request->acmp_bank)?'':$request->acmp_bank;
                  $company->acmp_brnc = !isset($request->acmp_brnc)?'':$request->acmp_brnc;
                  $company->acmp_swft = !isset($request->acmp_swft)?'':$request->acmp_swft;
                  $company->acmp_crnc = $request->acmp_crnc;
                  $company->acmp_dgit = $request->acmp_dgit;
                  $company->acmp_rond = $request->acmp_rond;
                  $company->cont_id = $this->currentUser->country()->id;
                  $company->lfcl_id = 1;
                  $company->aemp_iusr = $this->currentUser->employee()->id;
                  $company->aemp_eusr = $this->currentUser->employee()->id;
                 $company->save();
                  DB::connection($this->db)->commit();
                  return redirect()->back()->with('success', 'successfully Created');
              } catch (\Exception $e) {
                  DB::connection($this->db)->rollback();
                  return back()->withInput()->with('danger', $e);// throw $e;
              }
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $company = Company::on($this->db)->findorfail($id);
            return view('master_data.company.show')->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $company = Company::on($this->db)->findorfail($id);
            return view('master_data.company.edit')->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $company = Company::on($this->db)->findorfail($id);
        $company->acmp_name = $request->name;
        $company->acmp_code = $request->code;
        $company->acmp_note = !isset($request->acmp_note)?'':$request->acmp_note;
        $company->acmp_snam = !isset($request->acmp_snam)?'':$request->acmp_snam;
        $company->acmp_nvat = !isset($request->acmp_nvat)?'':$request->acmp_nvat;
        $company->acmp_nexc = !isset($request->acmp_nexc)?'':$request->acmp_nexc;
        $company->acmp_creg = !isset($request->acmp_creg)?'':$request->acmp_creg;
        $company->acmp_addr = $request->acmp_addr;
        $company->acmp_vats = !isset($request->acmp_vats)?'0':1;
        $company->acmp_titl = $request->acmp_titl;
        $company->acmp_rttl = $request->acmp_rttl;
        $company->acmp_scod = !isset($request->acmp_scod)?'':$request->acmp_scod;
        $company->acmp_acod = !isset($request->acmp_acod)?'':$request->acmp_acod;
        $company->acmp_iban = !isset($request->acmp_iban)?'':$request->acmp_iban;
        $company->acmp_bank = !isset($request->acmp_bank)?'':$request->acmp_bank;
        $company->acmp_brnc = !isset($request->acmp_brnc)?'':$request->acmp_brnc;
        $company->acmp_swft = !isset($request->acmp_swft)?'':$request->acmp_swft;
        $company->acmp_crnc = $request->acmp_crnc;
        $company->acmp_dgit = $request->acmp_dgit;
        $company->acmp_rond = $request->acmp_rond;
        $company->aemp_eusr = $this->currentUser->employee()->id;
        $company->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $company = Company::on($this->db)->findorfail($id);
            $company->lfcl_id = $company->lfcl_id == 1 ? 2 : 1;
            $company->aemp_eusr = $this->currentUser->employee()->id;
            $company->save();
            return redirect('/company');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function empEdit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $company = Company::on($this->db)->findorfail($id);
            // $employees = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            // $companyEmployees = CompanyEmployee::where('acmp_id', '=', $id)->get();

            $companyEmployees = DB::connection($this->db)->select("SELECT
  t1.id        ,
  t2.aemp_name ,
  t2.aemp_usnm
FROM tl_emcm AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
WHERE t1.acmp_id=$id");
            return view('master_data.company.company_employee')->with("companyEmployees", $companyEmployees)->with("company", $company)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $comEmp = CompanyEmployee::on($this->db)->findorfail($id);
            $comEmp->delete();

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
                $comEmp = CompanyEmployee::on($this->db)->where(['acmp_id' => $id, 'aemp_id' => $employee->id])->first();
                if ($comEmp == null) {
                    $comEmp = new CompanyEmployee();
                    $comEmp->setConnection($this->db);
                    $comEmp->aemp_id = $employee->id;
                    $comEmp->acmp_id = $id;
                    $comEmp->cont_id = $this->currentUser->country()->id;
                    $comEmp->lfcl_id = 1;
                    $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $comEmp->var = 1;
                    $comEmp->attr1 = '';
                    $comEmp->attr2 = '';
                    $comEmp->attr3 = 0;
                    $comEmp->attr4 = 0;
                    $comEmp->save();
                    return redirect()->back()->with('success', 'successfully Added');
                } else {
                    return redirect()->back()->with('danger', 'Already exist');
                }
            } else {
                return back()->withInput()->with('danger', 'Wrong User name');
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
}
