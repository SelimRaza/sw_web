<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\AppMenuGroup;
use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\TsmGroupZoneMapping;
use App\DataExport\EmployeeMenuGroup;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Depot;
use App\MasterData\DepotEmployee;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\MasterData\PJP;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\MasterData\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use AWS;
use Excel;
use Response;

class EmployeeController extends Controller
{
    private $access_key = 'tbld_employee';
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

                $employees = DB::connection($this->db)->table('tm_aemp AS t1')
                    ->join('tm_aemp AS t2', 't1.aemp_mngr', '=', 't2.id')
                    ->join('tm_aemp AS t3', 't1.aemp_lmid', '=', 't3.id')
                    ->join('tm_role AS t4', 't1.role_id', '=', 't4.id')
                    ->join('tm_edsg AS t5', 't1.edsg_id', '=', 't5.id')
                    ->leftJoin('tm_amng AS t6', 't1.amng_id', '=', 't6.id')
                    ->select('t1.id AS id', 't1.aemp_usnm', 't1.aemp_name', 't1.aemp_mob1', 't1.aemp_onme', 't1.aemp_stnm', 't2.aemp_usnm AS mnrg_usnm', 't2.aemp_name AS mnrg_name',
                        't3.aemp_usnm AS lmid_usnm', 't3.aemp_name AS lmid_name', 't4.role_name', 't5.edsg_name', 't1.aemp_emal', 't1.aemp_picn', 't1.lfcl_id', 't6.amng_name')->orderByDesc('t1.id')
                    ->where(function ($query) use ($q) {
                        $query->Where('t1.aemp_usnm', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.aemp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.aemp_mob1', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.aemp_usnm', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.aemp_usnm', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.role_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t5.edsg_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.aemp_emal', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $employees = DB::connection($this->db)->table('tm_aemp AS t1')
                    ->join('tm_aemp AS t2', 't1.aemp_mngr', '=', 't2.id')
                    ->join('tm_aemp AS t3', 't1.aemp_lmid', '=', 't3.id')
                    ->join('tm_role AS t4', 't1.role_id', '=', 't4.id')
                    ->join('tm_edsg AS t5', 't1.edsg_id', '=', 't5.id')
                    ->leftJoin('tm_amng AS t6', 't1.amng_id', '=', 't6.id')
                    ->select('t1.id AS id', 't1.aemp_usnm', 't1.aemp_name', 't1.aemp_mob1', 't1.aemp_onme', 't1.aemp_stnm', 't2.aemp_usnm AS mnrg_usnm', 't2.aemp_name AS mnrg_name',
                        't3.aemp_usnm AS lmid_usnm', 't3.aemp_name AS lmid_name', 't4.role_name', 't5.edsg_name', 't1.aemp_emal', 't1.aemp_picn', 't1.lfcl_id', 't6.amng_name')->orderByDesc('t1.id')
                    ->paginate(100);
            }
            return view('master_data.employee.index')->with("employees", $employees)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit')->with('search_text', $q);
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $masterRoles = MasterRole::on($this->db)->get();
            return view('master_data.employee.create')->with("masterRoles", $masterRoles)->with("roles", $userRole)->with("appMenuGroup", $appMenuGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $manager = Employee::on($this->db)->where(['aemp_usnm' => $request->manager_id])->first();
        $lineManager = Employee::on($this->db)->where(['aemp_usnm' => $request->line_manager_id])->first();
        if ($manager != null && $lineManager != null) {
            DB::connection($this->db)->beginTransaction();
            try {
                $user = User::create([
                    'name' => $request['name'],
                    'email' => trim($request['email']),
                    'password' => bcrypt(trim($request['email'])),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $this->currentUser->country()->id,
                ]);

                $emp = new Employee();
                $emp->setConnection($this->db);
                $emp->aemp_name = $request['name'];
                $emp->aemp_onme = $request['ln_name'] == "" ? '' : $request['ln_name'];
                $emp->aemp_stnm = $request['name'];
                $emp->aemp_mob1 = $request['mobile'] == "" ? '' : $request['mobile'];
                $emp->aemp_dtsm = $request['mobile'] == "" ? '' : $request['mobile'];
                $emp->aemp_emal = $request['address'] == "" ? '' : $request['address'];
                $emp->aemp_lued = $user->id;
                $emp->edsg_id = $request['role_id'];
                $emp->role_id = $request['master_role_id'];
                $emp->aemp_utkn = md5(uniqid(rand(), true));
                $emp->cont_id = $this->currentUser->country()->id;
                $emp->aemp_iusr = $this->currentUser->employee()->id;
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->aemp_mngr = $manager->id;
                $emp->aemp_lmid = $lineManager->id;
                $emp->aemp_aldt = $request['allowed_distance'];
                $emp->site_id = $request['site_id'];
                $emp->aemp_crdt = $request['aemp_crdt'];
                $emp->aemp_issl = $request['aemp_issl'] != null ? 1 : 0;
                $emp->aemp_lcin = '50';
                if ($request['address'] != '') {
                    $emp->aemp_otml = $request['auto_email'] != null ? 1 : 0;
                } else {
                    $emp->aemp_otml = 0;
                }
                $emp->aemp_lonl = $request['location_on'] != null ? 1 : 0;
                $emp->aemp_usnm = trim($request['email']);
                $emp->aemp_pimg = '';
                $emp->aemp_picn = '';
                if (isset($request->input_img)) {
                    $imageIcon = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $file = $request->file('input_img');
                    $imageName = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $s3 = AWS::createClient('s3');
                    $s3->putObject(array(
                        'Bucket' => 'sw-bucket',
                        'Key' => $imageName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read-write',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $s3->putObject(array(
                        'Bucket' => 'sw-bucket',
                        'Key' => $imageIcon,
                        'SourceFile' => $file,
                        'ACL' => 'public-read-write',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $emp->aemp_pimg = $imageName;
                    $emp->aemp_picn = $imageIcon;
                }
                $emp->aemp_emcc = $request['email_cc'] == "" ? '' : $request['email_cc'];
                $emp->lfcl_id = 1;
                $emp->amng_id = $request->amng_id;
                $emp->save();
                DB::connection($this->db)->commit();
                return redirect()->back()->withInput()->with('success', 'successfully Created');
            } catch (Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
                // return redirect()->back()->with('danger', 'Not Created');
            }
        } else {
            return back()->withInput()->with('danger', 'Wrong User name');
        }

    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {

            // $sales_groups = SalesGroupEmployee::where(['emp_id' => $id])->get();
            // $pjps = PJP::where(['emp_id' => $id])->get();
            $employee = collect(DB::connection($this->db)->select("SELECT
  t1.id,
  t1.aemp_name AS name,
  t1.aemp_usnm AS email,
  t1.aemp_onme AS ln_name,
  t1.aemp_mob1 AS mobile,
  t1.aemp_emal AS address,
  t1.aemp_aldt AS allowed_distance,
  t9.edsg_name AS role_name,
  t3.aemp_name AS manager_name,
  t4.aemp_name AS line_manager_name,
  t1.aemp_emcc    email_cc,
  t1.aemp_otml    auto_email,
  t1.aemp_lonl    location_on
FROM tm_aemp AS t1
  INNER JOIN tm_aemp AS t3 ON t1.aemp_mngr = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_lmid = t4.id
  INNER JOIN tm_edsg AS t9 ON t1.edsg_id = t9.id
WHERE t1.id =$id"))->first();

            $companyMapping = DB::connection($this->db)->select("SELECT
  t1.id as emcm_id,
  t2.id AS acmp_id,
  t2.acmp_name,
  t2.acmp_code
FROM tl_emcm AS t1
  INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
WHERE t1.aemp_id = $id");

            $depotMapping = DB::connection($this->db)->select("SELECT
  t1.id                                                                                  AS id,
  t2.id                                                                                  AS dlrm_id,
  t2.dlrm_name,
  t2.dlrm_code,
  t3.acmp_name,
  t3.acmp_code,
  concat(t4.base_name, '(', t4.base_code, ')', ' < ', t5.zone_name,'(', t5.zone_code, ')', ' < ', t6.dirg_name) AS base_name
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_acmp AS t3 ON t2.acmp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
  INNER JOIN tm_zone AS t5 ON t4.zone_id = t5.id
  INNER JOIN tm_dirg AS t6 ON t5.dirg_id = t6.id
WHERE t1.aemp_id = $id");

            $salesGroupMapping = DB::connection($this->db)->select("SELECT
  t1.id as sgsm_id,
  t2.id AS slgp_id,
  t2.slgp_name,
  t2.slgp_code,
  t1.plmt_id,
  t3.plmt_name,
  t3.plmt_code,
  t1.zone_id,
  t4.zone_name,
  t4.zone_code
FROM tl_sgsm AS t1
  INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
  INNER JOIN tm_plmt AS t3 ON t1.plmt_id = t3.id
  INNER JOIN tm_zone AS t4 ON t1.zone_id = t4.id
WHERE t1.aemp_id = $id;");

            $routePlanMapping = DB::connection($this->db)->select("SELECT
  t1.id                                                                                  AS rpln_id,
  t1.rpln_day,
  t2.rout_name,
  t2.rout_code,
  concat(t3.base_name, '(', t3.base_code, ')', ' < ', t4.zone_name,'(', t4.zone_code, ')', ' < ', t5.dirg_name) AS base_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tm_base AS t3 ON t2.base_id = t3.id
  INNER JOIN tm_zone AS t4 ON t3.zone_id = t4.id
  INNER JOIN tm_dirg AS t5 ON t4.dirg_id = t5.id
WHERE t1.aemp_id = $id;");
            $zoneGroupMapping = DB::connection($this->db)->select("SELECT
  t1.id,
  t2.slgp_name,
  t2.slgp_code,
  t3.zone_code,
  t3.zone_name
FROM tl_zmzg AS t1
  INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
  INNER JOIN tm_zone AS t3 ON t1.zone_id = t3.id
WHERE t1.aemp_id = $id;");

            return view('master_data.employee.show')->with('employee', $employee)->with('companyMapping', $companyMapping)->with('depotMapping', $depotMapping)->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            //   $employee_all = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $masterRoles = MasterRole::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $employee = Employee::on($this->db)->findorfail($id);
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $companyMapping = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t2.id AS acmp_id,
  t2.acmp_name,
  t2.acmp_code
FROM tl_emcm AS t1
  INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
WHERE t1.aemp_id = $id");

            $depotMapping = DB::connection($this->db)->select("SELECT
  t1.id                                                                                  AS id,
  t2.id                                                                                  AS dlrm_id,
  t2.dlrm_name,
  t2.dlrm_code,
  t3.acmp_name,
  t3.acmp_code,
  concat(t4.base_name, '(', t4.base_code, ')', ' < ', t5.zone_name,'(', t5.zone_code, ')', ' < ', t6.dirg_name) AS base_name
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_acmp AS t3 ON t2.acmp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
  INNER JOIN tm_zone AS t5 ON t4.zone_id = t5.id
  INNER JOIN tm_dirg AS t6 ON t5.dirg_id = t6.id
WHERE t1.aemp_id = $id");

            $salesGroupMapping = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t2.id AS slgp_id,
  t2.slgp_name,
  t2.slgp_code,
  t1.plmt_id,
  t3.plmt_name,
  t3.plmt_code,
  t1.zone_id,
  t4.zone_name,
  t4.zone_code
FROM tl_sgsm AS t1
  INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
  INNER JOIN tm_plmt AS t3 ON t1.plmt_id = t3.id
  INNER JOIN tm_zone AS t4 ON t1.zone_id = t4.id
WHERE t1.aemp_id = $id;");

            $routePlanMapping = DB::connection($this->db)->select("SELECT
  t1.id                                                                                  AS rpln_id,
  t1.rpln_day,
  t2.rout_name,
  t2.rout_code,
  concat(t3.base_name, '(', t3.base_code, ')', ' < ', t4.zone_name,'(', t4.zone_code, ')', ' < ', t5.dirg_name) AS base_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tm_base AS t3 ON t2.base_id = t3.id
  INNER JOIN tm_zone AS t4 ON t3.zone_id = t4.id
  INNER JOIN tm_dirg AS t5 ON t4.dirg_id = t5.id
WHERE t1.aemp_id = $id;");

            $zoneGroupMapping = DB::connection($this->db)->select("SELECT
  t1.id,
  t2.slgp_name,
  t2.slgp_code,
  t3.zone_code,
  t3.zone_name
FROM tl_zmzg AS t1
  INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
  INNER JOIN tm_zone AS t3 ON t1.zone_id = t3.id
WHERE t1.aemp_id = $id;");


            return view('master_data.employee.edit')->with('employee', $employee)->with('appMenuGroup', $appMenuGroup)->with('companyMapping', $companyMapping)->with('depotMapping', $depotMapping)->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)->with('btn_position', 'btn_employee1')
                ->with('userRoles', $userRole)->with('masterRoles', $masterRoles);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function profileEdit()
    {
        $empId = $this->currentUser->employee()->id;
        $employee = Employee::on($this->db)->findorfail($empId);
        $country = Country::all();
        return view('master_data.employee.profile_edit')->with('employee', $employee)->with('country', $country);
    }

    public function profileUpdate(Request $request, $id)
    {

        DB::connection($this->db)->beginTransaction();
        try {
            $emp = Employee::on($this->db)->findorfail($id);
            $emp->aemp_name = $request['name'];
            $emp->aemp_onme = $request['ln_name'] == "" ? '' : $request['ln_name'];
            $emp->aemp_mob1 = $request['mobile'] == "" ? '' : $request['mobile'];
            $emp->aemp_emal = $request['address'] == "" ? '' : $request['address'];
            if (isset($request->input_img)) {
                $imageIcon = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                $file = $request->file('input_img');
                $imageName = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                $s3 = AWS::createClient('s3');
                $s3->putObject(array(
                    'Bucket' => 'sw-bucket',
                    'Key' => $imageName,
                    'SourceFile' => $file,
                    'ACL' => 'public-read-write',
                    'ContentType' => $file->getMimeType(),
                ));
                $s3->putObject(array(
                    'Bucket' => 'sw-bucket',
                    'Key' => $imageIcon,
                    'SourceFile' => $file,
                     'ACL' => 'public-read-write',
                    'ContentType' => $file->getMimeType(),
                ));
                $emp->aemp_pimg = $imageName;
                $emp->aemp_picn = $imageIcon;
            }
            $emp->save();
            // dd($request->country_id)
            // if ($request->country_id != '') {
            // $country = Country::findorfail($request->country_id);

            $country = (new Country())->country($request->country_id);
            if ($country) {
                $employee = Employee::on($country->cont_conn)->where('aemp_lued', '=', $emp->aemp_lued)->first();
                if ($employee == null) {
                    $employee = new Employee();
                    $employee->setConnection($country->cont_conn);
                    $employee->aemp_name = $emp->aemp_name;
                    $employee->aemp_onme = $emp->aemp_onme;
                    $employee->aemp_stnm = $emp->aemp_stnm;
                    $employee->aemp_mob1 = $emp->aemp_mob1;
                    $employee->aemp_dtsm = $emp->aemp_dtsm;
                    $employee->aemp_emal = $emp->aemp_emal;
                    $employee->aemp_lued = $emp->aemp_lued;
                    $employee->edsg_id = 1;
                    $employee->role_id = $emp->role_id;
                    $employee->aemp_utkn = md5(uniqid(rand(), true));
                    $employee->cont_id = $country->id;
                    $employee->aemp_iusr = 1;
                    $employee->aemp_eusr = 1;
                    $employee->aemp_mngr = 1;
                    $employee->aemp_lmid = 1;
                    $employee->aemp_aldt = 0;
                    $employee->aemp_lcin = '50';
                    $employee->aemp_otml = 2;
                    $employee->aemp_lonl = $emp->aemp_lonl;
                    $employee->aemp_usnm = $emp->aemp_usnm;
                    $employee->aemp_pimg = '';
                    $employee->aemp_picn = '';
                    $employee->aemp_pimg = $emp->aemp_pimg;
                    $employee->aemp_picn = $emp->aemp_picn;
                    $employee->aemp_emcc = $emp->aemp_emcc;
                    $employee->site_id = 0;
                    $employee->aemp_crdt = 0;
                    $employee->aemp_issl = isset($emp->aemp_issl) ? $emp->aemp_issl : 0;
                    $employee->lfcl_id = 1;
                    $employee->save();
                }
                $user = User::findorfail($emp->aemp_lued);
                $user->cont_id = $request->country_id;
                $user->remember_token = '';
                $user->save();
            }

            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Updated');
            //     }
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }


    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        $manager = Employee::on($this->db)->where(['aemp_usnm' => $request->manager_id])->first();
        $lineManager = Employee::on($this->db)->where(['aemp_usnm' => $request->line_manager_id])->first();
        if ($manager != null && $lineManager != null) {
            DB::connection($this->db)->beginTransaction();
            try {
                $emp = Employee::on($this->db)->findorfail($id);
                $emp->aemp_name = $request['name'];
                $emp->aemp_onme = $request['ln_name'] == "" ? '' : $request['ln_name'];
                $emp->aemp_mob1 = $request['mobile'] == "" ? '' : $request['mobile'];
                $emp->aemp_emal = $request['address'] == "" ? '' : $request['address'];
                if ($request['address'] != '') {
                    $emp->aemp_otml = $request['auto_email'] != null ? 1 : 0;
                } else {
                    $emp->aemp_otml = 0;
                }
                $emp->aemp_lonl = $request['location_on'] != null ? 1 : 0;
                $emp->aemp_usnm = trim($request['email']);
                if (isset($request->input_img)) {
                    $imageIcon = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $file = $request->file('input_img');
                    $imageName = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $s3 = AWS::createClient('s3');
                    $s3->putObject(array(
                        'Bucket' => 'sw-bucket',
                        'Key' => $imageName,
                        'SourceFile' => $file,
                         'ACL' => 'public-read-write',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $s3->putObject(array(
                        'Bucket' => 'sw-bucket',
                        'Key' => $imageIcon,
                        'SourceFile' => $file,
                         'ACL' => 'public-read-write',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $emp->aemp_pimg = $imageName;
                    $emp->aemp_picn = $imageIcon;

                }
                $emp->aemp_emcc = $request['email_cc'] == "" ? '' : $request['email_cc'];
                $emp->edsg_id = $request['role_id'];
                $emp->role_id = $request['master_role_id'];
                $emp->aemp_utkn = md5(uniqid(rand(), true));
                $emp->aemp_mngr = $manager->id;
                $emp->aemp_lmid = $lineManager->id;
                $emp->aemp_aldt = $request['allowed_distance'];
                $emp->site_id = $request['site_id'];
                $emp->aemp_crdt = $request['aemp_crdt'];
                $emp->aemp_issl = $request['aemp_issl'] != null ? 1 : 0;
                $emp->aemp_asyn = $request['aemp_asyn'] != null ? 'Y' : 'N';
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->amng_id = $request->amng_id;
                $emp->save();
                $empUser = User::find($emp->aemp_lued);
                $empUser->remember_token = '';
                $empUser->email = trim($request['email']);
                $empUser->name = $request['name'];
                $empUser->save();
                DB::connection($this->db)->commit();
                // dd('sdfsd');
                return redirect()->back()->with('success', 'successfully Updated')->with('btn_position', 'btn_employee1');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
            }
        } else {
            return redirect()->back()->with('danger', 'Wrong User name')->with('btn_position', 'btn_employee1');
        }

    }


    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $employee = Employee::on($this->db)->findorfail($id);
            $employee->lfcl_id = $employee->lfcl_id == 1 ? 2 : 1;
            $employee->aemp_iusr = $this->currentUser->employee()->id;
            $employee->save();
            $user = User::find($employee->aemp_lued);
            $user->lfcl_id = $employee->lfcl_id;
            $user->remember_token = '';
            $user->save();
            return redirect()->back()->with('success', 'Successfully Updated');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function aempRplnDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $pjp = PJP::on($this->db)->findorfail($id);
            $pjp->delete();
            return redirect()->back()->with('success', 'successfully Deleted')->with('btn_position', 'btn_route');
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_route');
        }
    }

    public function aempRplnAdd(Request $request, $id)
    {
        $pjp = PJP::on($this->db)->where(['aemp_id' => $id, 'rpln_day' => $request->day_name])->first();
        $route = Route::on($this->db)->where(['rout_code' => $request->rout_code])->first();
        if ($pjp == null && $route != null) {
            $pjp = new PJP();
            $pjp->setConnection($this->db);
            $pjp->aemp_id = $id;
            $pjp->rpln_day = $request->day_name;
            $pjp->rout_id = $route->id;
            $pjp->cont_id = $this->currentUser->employee()->cont_id;
            $pjp->lfcl_id = 1;
            $pjp->aemp_iusr = $this->currentUser->employee()->id;
            $pjp->aemp_eusr = $this->currentUser->employee()->id;
            $pjp->save();
            return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_route');
        } else {
            return redirect()->back()->with('danger', 'Already exist')->with('btn_position', 'btn_route');
        }
    }

    public function aempDlrmDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $depotEmployee = DepotEmployee::on($this->db)->findorfail($id);
            $depotEmployee->delete();
            return redirect()->back()->with('success', 'Employee Deleted')->with('btn_position', 'btn_dealer');
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_dealer');
        }
    }

    public function aempDlrmAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $depot = Depot::on($this->db)->where(['dlrm_code' => $request->dlrm_code])->first();
            $company = Company::on($this->db)->where(['acmp_code' => $request->acmp_code])->first();
            if ($depot != null) {
                $depotEmployee = DepotEmployee::on($this->db)->where(['dlrm_id' => $depot->id, 'aemp_id' => $id])->first();
                if ($depotEmployee == null && $depot != null && $company != null) {
                    $depotEmployee = new DepotEmployee();
                    $depotEmployee->setConnection($this->db);
                    $depotEmployee->aemp_id = $id;
                    $depotEmployee->acmp_id = $company->id;
                    $depotEmployee->dlrm_id = $depot->id;
                    $depotEmployee->cont_id = $this->currentUser->country()->id;
                    $depotEmployee->lfcl_id = 1;
                    $depotEmployee->aemp_iusr = $this->currentUser->employee()->id;
                    $depotEmployee->aemp_eusr = $this->currentUser->employee()->id;
                    $depotEmployee->save();
                    return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_dealer');
                } else {
                    return back()->withInput()->with('danger', 'Already exist Or Code not Match')->with('btn_position', 'btn_dealer');
                }
            } else {
                return back()->withInput()->with('danger', 'Dealer Code not Match')->with('btn_position', 'btn_dealer');
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_dealer');
        }

    }

    public function aempSlgpDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroupEmp = SalesGroupEmployee::on($this->db)->findorfail($id);
            $salesGroupEmp->delete();
            return redirect()->back()->with('success', 'Employee Deleted')->with('btn_position', 'btn_group');
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_group');
        }
    }

    public function aempSlgpAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroup = SalesGroup::on($this->db)->where(['slgp_code' => $request->slgp_code])->first();
            if ($salesGroup != null) {
                $salesGroupEmp = SalesGroupEmployee::on($this->db)->where(['slgp_id' => $salesGroup->id, 'aemp_id' => $id])->first();
                if ($salesGroupEmp == null) {
                    $priceList = PriceList::on($this->db)->where(['plmt_code' => $request->plmt_code])->first();
                    $zone = Zone::on($this->db)->where(['zone_code' => $request->zone_code])->first();
                    if ($priceList != null && $zone != null) {
                        $salesGroupEmp = new SalesGroupEmployee();
                        $salesGroupEmp->setConnection($this->db);
                        $salesGroupEmp->aemp_id = $id;
                        $salesGroupEmp->slgp_id = $salesGroup->id;
                        $salesGroupEmp->plmt_id = $priceList->id;
                        $salesGroupEmp->zone_id = $zone->id;
                        $salesGroupEmp->cont_id = $this->currentUser->country()->id;
                        $salesGroupEmp->lfcl_id = 1;
                        $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                        $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                        $salesGroupEmp->save();
                        return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_group');
                    } else {
                        return back()->withInput()->with('danger', 'Wrong Price or Zone Code')->with('btn_position', 'btn_group');
                    }
                } else {
                    return back()->withInput()->with('danger', 'Already exist')->with('btn_position', 'btn_group');
                }
            } else {
                return back()->withInput()->with('danger', 'Wrong Group Code')->with('btn_position', 'btn_group');
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_group');
        }

    }

    public function aempAcmpDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $comEmp = CompanyEmployee::on($this->db)->findorfail($id);
            $comEmp->delete();

            return redirect()->back()->with('success', 'Employee Deleted')->with('btn_position', 'btn_company');
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_company');
        }
    }

    public function aempAcmpAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {

            $company = Company::on($this->db)->where(['acmp_code' => $request->acmp_code])->first();
            if ($company != null) {
                $comEmp = CompanyEmployee::on($this->db)->where(['acmp_id' => $company->id, 'aemp_id' => $id])->first();
                if ($comEmp == null) {
                    $comEmp = new CompanyEmployee();
                    $comEmp->setConnection($this->db);
                    $comEmp->aemp_id = $id;
                    $comEmp->acmp_id = $company->id;
                    $comEmp->cont_id = $this->currentUser->country()->id;
                    $comEmp->lfcl_id = 1;
                    $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $comEmp->save();
                    return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_company');
                } else {
                    return redirect()->back()->with('danger', 'Already exist')->with('btn_position', 'btn_company');
                }
            } else {
                return back()->withInput()->with('danger', 'Wrong Company Code')->with('btn_position', 'btn_company');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_company');
        }

    }

    public function aempSlgpZoneDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $comEmp = TsmGroupZoneMapping::on($this->db)->findorfail($id);
            $comEmp->delete();

            return redirect()->back()->with('success', 'Employee Deleted')->with('btn_position', 'btn_permission');
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_permission');
        }
    }

    public function aempSlgpZoneAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {

            $zone = Zone::on($this->db)->where(['zone_code' => $request->zone_code])->first();
            $group = SalesGroup::on($this->db)->where(['slgp_code' => $request->slgp_code])->first();
            // dd($group);
            if ($zone != null && $group != null) {
                $comEmp = TsmGroupZoneMapping::on($this->db)->where(['zone_id' => $zone->id, 'slgp_id' => $group->id])->first();
                if ($comEmp == null) {
                    $comEmp = new TsmGroupZoneMapping();
                    $comEmp->setConnection($this->db);
                    $comEmp->aemp_id = $id;
                    $comEmp->zone_id = $zone->id;
                    $comEmp->slgp_id = $group->id;
                    $comEmp->cont_id = $this->currentUser->country()->id;
                    $comEmp->lfcl_id = 1;
                    $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $comEmp->save();
                    return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_permission');
                } else {
                    return back()->withInput()->with('danger', 'Someone Already exist')->with('btn_position', 'btn_permission');
                }
            } else {
                return back()->withInput()->with('danger', 'Wrong Zone or Group Code')->with('btn_position', 'btn_permission');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_permission');
        }

    }

    public function reset(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $employee = Employee::on($this->db)->findorfail($id);
            $employee->aemp_utkn = time() . $id . substr(md5(mt_rand()), 5, 22);
            $employee->aemp_eusr = $this->currentUser->employee()->id;
            $user = User::find($employee->aemp_lued);
            $user->password = bcrypt($user->email);
            $user->remember_token = '';
            $employee->save();
            $user->save();
            return redirect('/employee');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function passChange($id)
    {
        $user = Auth::user();
        return view('master_data.employee.pass_change')->with('user', $user);
    }

    public function change(Request $request, $id)
    {
        //  $employee = Employee::on($this->db)->findorfail($id);
        // $employee->aemp_utkn = time() . $id . substr(md5(mt_rand()), 5, 22);
        //  $employee->aemp_eusr = $this->currentUser->employee()->id;
        /* if (Auth::attempt(['email' => $request->email, 'password' => $request->old_password])) {
             $user = User::find($id);
             $user->password = bcrypt($request['password']);
             $user->remember_token = '';
             //$employee->save();
             $user->save();
             return redirect('/employee');
         }*/


        if (Auth::attempt(['email' => $request->email, 'password' => $request->old_password])) {
            $user = Auth::user();
            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('tt_pswd')->insert(
                array(
                    'pswd_time' => date('Y-m-d H:i:s'),
                    'pswd_user' => $request->email,
                    'pswd_opwd' => $request->old_password,
                    'pswd_npwd' => $request->password,
                    'cont_id' => $user->cont_id,
                    'lfcl_id' => 1,
                )
            );
            return redirect()->back()->with('success', 'Successfully changed');
        } else {
            return redirect()->back()->with('danger', 'Password Not Matched');
        }

    }


    public function employeeUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Employee(), 'employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function employeeUpload()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('master_data.employee.employee_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function employeeUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Employee(), $request->file('import_file'));
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

    public function filterEmployeeByGroup(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id                                        AS id,
  concat(t1.aemp_name, '(', t1.aemp_usnm, ')') AS name
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
WHERE t2.slgp_id = $request->sales_group_id");
        return Response::json($divisions);
    }

    public function employeeHrisUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new EmployeeMenuGroup(), 'hris_user_bulk_create_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function employeeHrisUpload()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('master_data.employee.employee_hris');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function employeeHrisUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Employee(), $request->file('import_file'));
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

    public function employeehrisCreate(Request $request)
    {
        $emp = Employee::on($this->db)->where(['aemp_usnm' => $request->aemp_usnm])->first();
        if ($emp == null) {
            $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$request->aemp_usnm";
            $country_data = json_decode(file_get_contents($staff_detiails));
            $staffResult = json_decode($country_data->StaffResult);
            if ($staffResult != null) {
                DB::connection($this->db)->beginTransaction();
                try {
                    $user = User::where('email', '=', $request->aemp_usnm)->first();
                    if ($user == null) {
                        $user = User::create([
                            'name' => $staffResult[0]->NAME,
                            'email' => trim($request->aemp_usnm),
                            'password' => bcrypt(trim($request->aemp_usnm)),
                            'remember_token' => md5(uniqid(rand(), true)),
                            'lfcl_id' => 1,
                            'cont_id' => $this->currentUser->country()->id,
                        ]);
                    }
                    $emp = new Employee();
                    $emp->setConnection($this->db);
                    $emp->aemp_name = $staffResult[0]->NAME;
                    $emp->aemp_onme = $staffResult[0]->NAME;
                    $emp->aemp_stnm = $staffResult[0]->NAME;
                    $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                    $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                    $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                    $emp->aemp_lued = $user->id;
                    $emp->edsg_id = 1;
                    $emp->role_id = 1;
                    $emp->aemp_utkn = md5(uniqid(rand(), true));
                    $emp->cont_id = $this->currentUser->country()->id;;
                    $emp->aemp_iusr = $this->currentUser->employee()->id;
                    $emp->aemp_eusr = $this->currentUser->employee()->id;
                    $emp->aemp_mngr = 1;
                    $emp->aemp_lmid = 1;
                    $emp->aemp_aldt = 0;
                    $emp->aemp_lcin = '50';
                    $emp->aemp_otml = 2;
                    $emp->aemp_lonl = 0;
                    $emp->aemp_usnm = trim($staffResult[0]->ID);
                    $emp->aemp_pimg = '';
                    $emp->aemp_picn = '';
                    $emp->aemp_emcc = '';
                    $emp->aemp_crdt = 0;
                    $emp->aemp_issl = 0;
                    $emp->site_id = 0;
                    $emp->lfcl_id = 1;
                    $emp->amng_id = 3;
                    $emp->save();
                    $user->cont_id = $this->currentUser->country()->id;
                    $user->save();
                    DB::connection($this->db)->commit();
                    return redirect('employee/' . $emp->id . "/edit")->with('success', 'successfully Created');
                } catch (Exception $e) {
                    DB::connection($this->db)->rollback();
                    throw $e;
                    // return redirect()->back()->with('danger', 'Not Created');
                }
            } else {
                return back()->withInput()->with('danger', 'User Not found on HRIS');
            }
        } else {
            $user = User::where('email', '=', $request->aemp_usnm)->first();
            if ($user == null) {
                $user = User::create([
                    'name' => $request->aemp_usnm,
                    'email' => trim($request->aemp_usnm),
                    'password' => bcrypt(trim($request->aemp_usnm)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $this->currentUser->country()->id,
                ]);
            }
            $user->cont_id = $this->currentUser->country()->id;
            $user->save();
            return back()->withInput()->with('danger', 'Already Exist ' . $this->currentUser->country()->cont_name);
        }

    }
}
