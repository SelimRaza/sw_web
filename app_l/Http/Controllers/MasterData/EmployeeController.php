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
use App\MasterData\RoutePlan;

use App\MasterData\Thana;
use App\MasterData\ThanaSRMapping;
use App\MasterData\ThanaSRMappingDataExport;
use App\MasterData\EmployeeGroupZonePricelistUpload;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\MasterData\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\MasterData\EmployeeCountryMapping;
use Image;
use AWS;
use Excel;
use Response;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    private $access_key = 'tbld_employee';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function resetImei(){

    }

    public function index(Request $request)
    {
        //return Hash::make(1111);
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zone = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` as id, zone_name, zone_code FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            if ($request->has('search_text')) {
                $q = request('search_text');
                //$dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");


                $employees = DB::connection($this->db)->table('tm_aemp AS t1')
                    ->join('tm_aemp AS t2', 't1.aemp_mngr', '=', 't2.id')
                    ->join('tm_aemp AS t3', 't1.aemp_lmid', '=', 't3.id')
                    ->join('tm_role AS t4', 't1.role_id', '=', 't4.id')
                    ->join('tm_edsg AS t5', 't1.edsg_id', '=', 't5.id')
                    ->leftJoin('tm_site as t7','t1.site_id','=','t7.id')
                    ->leftJoin('tm_amng AS t6', 't1.amng_id', '=', 't6.id')
                    ->select('t1.id AS id','t7.site_code', 't1.aemp_usnm', 't1.aemp_name', 't1.aemp_mob1', 't1.aemp_onme', 't1.aemp_stnm', 't1.aemp_mngr', 't2.aemp_usnm AS mnrg_usnm', 't2.aemp_name AS mnrg_name',
                        't3.aemp_usnm AS lmid_usnm', 't3.aemp_name AS lmid_name', 't4.role_name', 't5.edsg_name', 't1.aemp_emal', 't1.aemp_picn', 't1.lfcl_id', 't6.amng_name')->orderByDesc('t1.id')
                    ->where(function ($query) use ($q) {
                        $query->Where('t1.aemp_usnm', 'LIKE', '%' . $q . '%')
                            // ->orWhere('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.aemp_name', 'LIKE', '%' . $q . '%');
                        // ->orWhere('t1.aemp_mob1', 'LIKE', '%' . $q . '%')
                        // ->orWhere('t2.aemp_usnm', 'LIKE', '%' . $q . '%')
                        // ->orWhere('t3.aemp_usnm', 'LIKE', '%' . $q . '%')
                        // ->orWhere('t4.role_name', 'LIKE', '%' . $q . '%')
                        // ->orWhere('t5.edsg_name', 'LIKE', '%' . $q . '%')
                        // ->orWhere('t1.aemp_emal', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $employees = DB::connection($this->db)->table('tm_aemp AS t1')
                    ->join('tm_aemp AS t2', 't1.aemp_mngr', '=', 't2.id')
                    ->join('tm_aemp AS t3', 't1.aemp_lmid', '=', 't3.id')
                    ->join('tm_role AS t4', 't1.role_id', '=', 't4.id')
                    ->join('tm_edsg AS t5', 't1.edsg_id', '=', 't5.id')
                    ->leftJoin('tm_amng AS t6', 't1.amng_id', '=', 't6.id')
                    ->leftJoin('tm_site as t7','t1.site_id','=','t7.id')
                    ->select('t1.id AS id','t7.site_code', 't1.aemp_usnm', 't1.aemp_name', 't1.aemp_mob1', 't1.aemp_onme', 't1.aemp_stnm', 't1.aemp_mngr', 't2.aemp_usnm AS mnrg_usnm', 't2.aemp_name AS mnrg_name',
                        't3.aemp_usnm AS lmid_usnm', 't3.aemp_name AS lmid_name', 't4.role_name', 't5.edsg_name', 't1.aemp_emal', 't1.aemp_picn', 't1.lfcl_id', 't6.amng_name')->orderByDesc('t1.id')
                    ->paginate(100);
            }
            return view('master_data.employee.index')->with("employees", $employees)->with('permission', $this->userMenu)->with('search_text', $q)->with('acmp', $acmp)->with('zone', $zone);
        } else {
            return view('theme.access_limit')->with('search_text', $q);
        }
    }
    public function getEmployeeMaster(){
        $smnu = SubMenu::where(['wsmn_ukey' =>'employee-master', 'cont_id' => $this->currentUser->country()->id])->first();
        $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' =>$smnu->id])->first();
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code 
                    FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $zone = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` as id, zone_name, zone_code 
                    FROM `user_area_permission` WHERE `aemp_id`='$empId'");
        $sv_list = DB::connection($this->db)->select("SELECT  aemp_usnm,aemp_name,id 
                    FROM `tm_aemp` WHERE lfcl_id=1 AND role_id>=2 ORDER BY aemp_usnm");
        $sr_list = DB::connection($this->db)->select("SELECT  aemp_usnm,aemp_name,id 
                    FROM `tm_aemp` WHERE lfcl_id=1 AND role_id=1 ORDER BY aemp_usnm");
        return view('master_data.employee.emp_filter.index',['acmp'=>$acmp,'zone'=>$zone,'sv_list'=>$sv_list,'sr_list'=>$sr_list,'permission'=>$permission]);
    }
    public function getEmpAutoId($aemp_usnm){
        $emp=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
        return $emp?$emp->id:' ';
    }
    public function employeeFilter(Request $request){
        $cont = $this->currentUser->country()->id;
        $acmp_id = $request->acmp_id;
        $slgp_id = $request->slgp_id;
        $zone_id = $request->zone_id;
        $sr_id = $request->sr_id;
        $sp_id=$request->sv_id;
        $sv_id =$this->getEmpAutoId($request->sv_id);
        if ($sr_id != "") {
            $query = "SELECT t1.id, t1.aemp_name, t1.aemp_mob1, t1.aemp_usnm, t2.role_name, t3.edsg_name,t8.site_code,
                         t4.aemp_usnm as m_code, t4.aemp_name as m_name, t5.amng_name,t6.lfcl_name,t7.slgp_name,date(t1.created_at) join_date,date(t1.updated_at) edat
                        FROM `tm_aemp` t1 INNER JOIN tm_role t2 ON t1.role_id=t2.id 
                        INNER JOIN tm_edsg t3 ON t1.edsg_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.`aemp_mngr`=t4.id 
                        LEFT JOIN tm_amng t5 ON t1.amng_id=t5.id 
                        INNER JOIN tm_lfcl t6 ON t1.lfcl_id=t6.id
                        INNER JOIN tm_slgp t7 ON t1.slgp_id=t7.id
                        LEFT JOIN tm_site t8 ON t1.site_id=t8.id
                        WHERE t1.cont_id = '$cont' and t1.`aemp_usnm`='$sr_id'
                        
                        ";

        }else if($sp_id !=""){
            $query = "SELECT t1.id, t1.aemp_name, t1.aemp_mob1, t1.aemp_usnm, t2.role_name, t3.edsg_name, 
                        t4.aemp_usnm as m_code, t4.aemp_name as m_name, t5.amng_name ,t6.lfcl_name,t7.slgp_name,date(t1.created_at) join_date,date(t1.updated_at) edat
                        FROM `tm_aemp` t1 INNER JOIN tm_role t2 ON t1.role_id=t2.id 
                        INNER JOIN tm_edsg t3 ON t1.edsg_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.`aemp_mngr`=t4.id 
                        LEFT JOIN tm_amng t5 ON t1.amng_id=t5.id 
                        INNER JOIN tm_lfcl t6 ON t1.lfcl_id=t6.id
                        INNER JOIN tm_slgp t7 ON t1.slgp_id=t7.id
                        WHERE t1.cont_id = '$cont' and t1.aemp_mngr=$sv_id";
        }
         else if ($zone_id != "") {
            $query = "SELECT t1.id, t1.aemp_name, t1.aemp_mob1, t1.aemp_usnm, t2.role_name, t3.edsg_name,
                        t4.aemp_usnm as m_code, t4.aemp_name as m_name, t5.amng_name,lfcl_name,t7.slgp_name,date(t1.created_at) join_date,date(t1.updated_at) edat
                        FROM `tm_aemp` t1 INNER JOIN tm_role t2 ON t1.role_id=t2.id 
                        INNER JOIN tm_edsg t3 ON t1.edsg_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.`aemp_mngr`=t4.id 
                        LEFT JOIN tm_amng t5 ON t1.amng_id=t5.id
                        INNER JOIN tm_lfcl t6 ON t1.lfcl_id=t6.id
                        INNER JOIN tm_slgp t7 ON t1.slgp_id=t7.id
                         WHERE t1.cont_id = '$cont' and t1.`slgp_id`='$slgp_id' AND t1.zone_id='$zone_id'";
        } else {
            $query = "SELECT t1.id, t1.aemp_name, t1.aemp_mob1, t1.aemp_usnm, t2.role_name, t3.edsg_name,
                        t4.aemp_usnm as m_code, t4.aemp_name as m_name, t5.amng_name,t6.lfcl_name,t7.slgp_name,date(t1.created_at) join_date,date(t1.updated_at) edat
                        FROM `tm_aemp` t1 INNER JOIN tm_role t2 ON t1.role_id=t2.id 
                        INNER JOIN tm_edsg t3 ON t1.edsg_id=t3.id 
                        INNER JOIN tm_aemp t4 ON t1.`aemp_mngr`=t4.id 
                        LEFT JOIN tm_amng t5 ON t1.amng_id=t5.id 
                        INNER JOIN tm_lfcl t6 ON t1.lfcl_id=t6.id
                        INNER JOIN tm_slgp t7 ON t1.slgp_id=t7.id
                        WHERE t1.cont_id = '$cont' and  t1.`slgp_id`='$slgp_id'";
        }
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));
        return array(
            'permission'=>$this->userMenu,
            'srData'=>$srData,
        );

    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $masterRoles = MasterRole::on($this->db)->get();
            $country = DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 order by cont_name ASC");
            return view('master_data.employee.create')->with("masterRoles", $masterRoles)->with("roles", $userRole)->with("appMenuGroup", $appMenuGroup)->with('country', $country);
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

                $user=User::where('email',$request['email'])->first();
                if(!$user){
                    $user = User::create([
                        'name' => $request['name'],
                        'email' => trim($request['email']),
                        'password' => bcrypt(trim($request['email'])),
                        'remember_token' => md5(uniqid(rand(), true)),
                        'lfcl_id' => 1,
                        'cont_id' => $this->currentUser->country()->id,
                    ]);
                }
                $site_id=$this->getSiteId($request->site_code);
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
                $emp->site_id = $site_id;
                //$request['site_id'];
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
                        'Bucket' => 'prgfms',
                        'Key' => $imageName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $imageIcon,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $emp->aemp_pimg = $imageName;
                    $emp->aemp_picn = $imageIcon;
                }
                $emp->aemp_emcc = $request['email_cc'] == "" ? '' : $request['email_cc'];
                $emp->lfcl_id = 1;
                $emp->amng_id = $request->amng_id;
                $emp->save();

                $cont_id = $request->cont_id;
                $visa_no = $request->visa_no;
                $expr_date = $request->expr_date;
                
                if ($cont_id != '' || $visa_no != '') {
                    $ecmp = new EmployeeCountryMapping();
                    $ecmp->setConnection($this->db);
                    $ecmp->aemp_id = $emp->id;
                    $ecmp->cont_id = $cont_id;
                    $ecmp->visa_no = $visa_no;
                    $ecmp->expr_date = $expr_date;
                    $ecmp->aemp_iusr = $this->aemp_id;
                    $ecmp->aemp_eusr = $this->aemp_id;
                    $ecmp->save();
                }
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

    /*public function show($id)
    {
        if ($this->userMenu->wsmu_read) {

            $empId = $this->currentUser->employee()->id;

            //   $employee_all = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $masterRoles = MasterRole::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $employee = Employee::on($this->db)->findorfail($id);
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $site_code=$this->getSiteCode($employee->site_id);
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
                        WHERE t1.aemp_id = $id AND t1.lfcl_id=1");

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
            $salesGroup = DB::connection($this->db)->select("SELECT
                            id,
                            slgp_name,
                            slgp_code
                            FROM tm_slgp;");
            $zoneGroup = DB::connection($this->db)->select("SELECT
                            id,
                            zone_name,
                            zone_code
                            FROM tm_zone;");
            $country_id = $this->currentUser->country()->id;
            $acmp_list = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            $slgp_list = DB::connection($this->db)->select("SELECT id,slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $zone_list = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` as id, zone_name, zone_code FROM `user_area_permission` WHERE `aemp_id`=$empId");
            $than_list = DB::connection($this->db)->select("SELECT `id`, `than_code`, `than_name` FROM `tm_than`");
            $emp_than_list = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_name, t2.aemp_usnm, t3.than_name, t3.than_code,
             t4.dsct_name FROM `tl_srth` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id INNER JOIN tm_than t3 ON t1.than_id = t3.id 
            INNER JOIN tm_dsct t4 ON t3.dsct_id = t4.id WHERE t1.`aemp_id`='$id';");

            $depot_acmp = DB::connection($this->db)->select("select acmp_name,acmp_code from tm_acmp  where id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $countries = DB::select("SELECT id,cont_code,cont_name FROM tl_cont Order by cont_name ASC");
            $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
            return view('master_data.employee.show')->with('employee', $employee)->with('appMenuGroup', $appMenuGroup)->with('companyMapping', $companyMapping)
                ->with('depotMapping', $depotMapping)->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)
                ->with('btn_position', 'btn_employee1')->with('userRoles', $userRole)->with('masterRoles', $masterRoles)->with('salesGroup', $salesGroup)
                ->with('zoneGroup', $zoneGroup)->with('permission', $this->userMenu)->with('acmp_list', $acmp_list)->with('slgp_list', $slgp_list)
                ->with('zone_list', $zone_list)->with('depot_acmp', $depot_acmp)->with('ecmp', $ecmp)
                ->with('countries', $countries)->with('than_list', $than_list)->with('emp_than_list',$emp_than_list)
                ->with('site_code',$site_code);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }*/

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
            $ecmp = DB::connection($this->db)->select("SELECT t2.cont_name,t1.visa_no,t1.expr_date  FROM tl_ecmp t1 INNER JOIN myprg_comm.tl_cont t2 ON t1.cont_id=t2.id WHERE t1.aemp_id=$id");


            return view('master_data.employee.show')->with('employee', $employee)->with('companyMapping', $companyMapping)->with('depotMapping', $depotMapping)
                ->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)
                ->with('ecmp', $ecmp);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function getSiteCode($id){
        $site=Site::on($this->db)->find($id);
        return $site?$site->site_code:'';
    }
    public function getSiteId($code){
        $site=Site::on($this->db)->where(['site_code'=>$code])->first();
        return $site?$site->id:0;
    }
    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $empId = $this->currentUser->employee()->id;

            $masterRoles = MasterRole::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $employee = Employee::on($this->db)->findorfail($id);
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $site_code=$this->getSiteCode($employee->site_id);
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
                            WHERE t1.aemp_id = $id AND t1.lfcl_id=1");
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
            $salesGroup = DB::connection($this->db)->select("SELECT
                            id,
                            slgp_name,
                            slgp_code
                            FROM tm_slgp;");
            $zoneGroup = DB::connection($this->db)->select("SELECT
                        id,
                        zone_name,
                        zone_code
                        FROM tm_zone;");
            /*$countries = DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 Order by cont_name ASC");
            $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
            return view('master_data.employee.edit')->with('employee', $employee)->with('appMenuGroup', $appMenuGroup)->with('companyMapping', $companyMapping)->with('depotMapping', $depotMapping)
                ->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)->with('btn_position', 'btn_employee1')
                ->with('userRoles', $userRole)->with('masterRoles', $masterRoles)->with('salesGroup', $salesGroup)->with('zoneGroup', $zoneGroup)->with('countries', $countries)
                ->with('ecmp', $ecmp)
                ->with('site_code',$site_code);*/


            $acmp_list = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            $slgp_list = DB::connection($this->db)->select("SELECT id,slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $zone_list = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` as id, zone_name, zone_code FROM `user_area_permission` WHERE `aemp_id`=$empId");
            $than_list = DB::connection($this->db)->select("SELECT `id`, `than_code`, `than_name` FROM `tm_than`");
            $dsct = DB::connection($this->db)->select("SELECT `id`, `dsct_name`, `dsct_code` FROM `tm_dsct` WHERE `lfcl_id`='1'");
            $emp_than_list = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_name, t2.aemp_usnm, t3.than_name, t3.than_code,
             t4.dsct_name FROM `tl_srth` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id INNER JOIN tm_than t3 ON t1.than_id = t3.id 
            INNER JOIN tm_dsct t4 ON t3.dsct_id = t4.id WHERE t1.`aemp_id`='$id';");

            $depot_acmp = DB::connection($this->db)->select("select acmp_name,acmp_code from tm_acmp  where id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $countries = DB::select("SELECT id,cont_code,cont_name FROM tl_cont Order by cont_name ASC");
            $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
            return view('master_data.employee.edit')->with('employee', $employee)->with('appMenuGroup', $appMenuGroup)->with('companyMapping', $companyMapping)
                ->with('depotMapping', $depotMapping)->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)
                ->with('btn_position', 'btn_employee1')->with('userRoles', $userRole)->with('masterRoles', $masterRoles)->with('salesGroup', $salesGroup)
                ->with('zoneGroup', $zoneGroup)->with('permission', $this->userMenu)->with('acmp_list', $acmp_list)->with('slgp_list', $slgp_list)
                ->with('zone_list', $zone_list)->with('depot_acmp', $depot_acmp)->with('ecmp', $ecmp)
                ->with('countries', $countries)->with('than_list', $than_list)->with('emp_than_list',$emp_than_list)
                ->with('site_code',$site_code)->with('dsct',$dsct);

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
                    'Bucket' => 'prgfms',
                    'Key' => $imageName,
                    'SourceFile' => $file,
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ));
                $s3->putObject(array(
                    'Bucket' => 'prgfms',
                    'Key' => $imageIcon,
                    'SourceFile' => $file,
                    'ACL' => 'public-read',
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
                        'Bucket' => 'prgfms',
                        'Key' => $imageName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $imageIcon,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
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
                $emp->site_id = $this->getSiteId($request['site_id']);
                $emp->aemp_crdt = $request['aemp_crdt'];
                $emp->aemp_issl = $request['aemp_issl'] != null ? 1 : 0;
                $emp->aemp_asyn = $request['aemp_asyn'] != null ? 'Y' : 'N';
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->amng_id = $request->amng_id;
                $emp->save();

                $empUser = User::find($emp->aemp_lued);
                // dd($emp, $empUser);
                $empUser->remember_token = '';
                $empUser->email = trim($request['email']);
                $empUser->name = $request['name'];
                $empUser->save();

                $cont_id = $request->cont_id;
                $visa_no = $request->visa_no;
                $expr_date = $request->expr_date;
                if ($cont_id != '' || $visa_no != '') {
                    $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
                    if ($ecmp) {
                        $ecmp->cont_id = $cont_id;
                        $ecmp->visa_no = $visa_no;
                        $ecmp->expr_date = $expr_date;
                        $ecmp->aemp_eusr = $this->aemp_id;
                        $ecmp->save();
                    } else {
                        $ecmp = new EmployeeCountryMapping();
                        $ecmp->setConnection($this->db);
                        $ecmp->aemp_id = $emp->id;
                        $ecmp->cont_id = $cont_id;
                        $ecmp->visa_no = $visa_no;
                        $ecmp->expr_date = $expr_date;
                        $ecmp->aemp_iusr = $this->aemp_id;
                        $ecmp->aemp_eusr = $this->aemp_id;
                        $ecmp->save();
                    }

                }
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'successfully Updated')->with('btn_position', 'btn_employee1');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return $e->getMessage();
                throw $e->getMessage();
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
        //dd($request);
        // $pjp = PJP::on($this->db)->where(['aemp_id' => $id, 'rpln_day' => $request->day_name])->first();
        $route = Route::on($this->db)->where(['rout_code' => $request->rout_code])->first();
        if ($route != null) {
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
                $priceList = PriceList::on($this->db)->where(['plmt_code' => $request->plmt_code])->first();
                $salesGroupEmp = SalesGroupEmployee::on($this->db)->where(['slgp_id' => $salesGroup->id, 'aemp_id' => $id, 'plmt_id' => $priceList->id])->first();
                if ($salesGroupEmp == null) {

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
            //$zone = Zone::on($this->db)->where(['id' => $request->zone_code])->first();
            // $group = SalesGroup::on($this->db)->where(['id' => $request->slgp_code])->first();
            $zone = $request->zone_code;
            $slgp = $request->slgp_code;

            $comEmp = new TsmGroupZoneMapping();
            $comEmp->setConnection($this->db);
            $comEmp->aemp_id = $id;
            $comEmp->zone_id = $zone;
            $comEmp->slgp_id = $slgp;
            $comEmp->cont_id = $this->currentUser->country()->id;
            $comEmp->lfcl_id = 1;
            $comEmp->aemp_iusr = $this->currentUser->employee()->id;
            $comEmp->aemp_eusr = $this->currentUser->employee()->id;
            $comEmp->save();
            DB::connection($this->db)->select("UPDATE `tm_aemp` SET `aemp_mngr` = '$id', `aemp_lmid` = '$id' WHERE `zone_id` = '$zone' AND `slgp_id` = '$slgp' AND lfcl_id='1' AND `edsg_id`='1' and role_id='1'");
            return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_permission');


        } else {
            return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_permission');
        }


    }

    public function reset(Request $request, $id)
    {
        $newPassword = '';
        if ($this->userMenu->wsmu_updt) {
            $employee = Employee::on($this->db)->findorfail($id);
            $employee->aemp_utkn = time() . $id . substr(md5(mt_rand()), 5, 22);
            $employee->aemp_eusr = $this->currentUser->employee()->id;
            $user = User::find($employee->aemp_lued);
            $user->password = bcrypt($user->email);
            $user->remember_token = '';
            $newPassword = $user->email;
            $employee->save();
            $user->save();
            $oldPassword = DB::table('tt_pswd')->where('pswd_user', $user->email)->orderBy('id', 'DESC')->select('pswd_opwd')->pluck('pswd_opwd')->first();
            if ($oldPassword) {

                $oldPassword = $oldPassword;

            } else {

                $oldPassword = $user->email;
            }

            DB::table('tt_pswd')->insert(
                array(
                    'pswd_time' => date('Y-m-d H:i:s'),
                    'pswd_user' => $user->email,
                    'pswd_opwd' => $oldPassword,
                    'pswd_npwd' => $newPassword,
                    'cont_id' => $user->cont_id,
                    'lfcl_id' => $user->lfcl_id,
                )
            );
            //return redirect('/employee');
            return redirect()->back()->with('success', 'successfully Reset');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }

    }

    public
    function passChange($id)
    {
        $user = Auth::user();
        return view('master_data.employee.pass_change')->with('user', $user);
    }

    public
    function change(Request $request, $id)
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


    public
    function employeeUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Employee(), 'employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function employeeUpload()
    {
        if ($this->userMenu->wsmu_vsbl) {

            return view('master_data.employee.employee_upload');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }
    }

    public
    function masterdataUpload()
    {
        if ($this->userMenu->wsmu_vsbl) {

            return view('master_data.employee.master_data_upload');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }
    }

    public
    function employeeRouteRearchView()
    {

        if ($this->userMenu->wsmu_vsbl) {

            return view('master_data.employee.routeSearch');

        } else {

            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function employeeRouteLikeCreate(Request $request)
    {
        dd($request);
    }

    public
    function jsonGetEmployeeRoute(Request $request)
    {

        if ($this->userMenu->wsmu_read) {

            $results = DB::connection($this->db)->select("SELECT
                    tl_rpln.id,
                    tm_aemp.aemp_name,
                    tm_aemp.aemp_usnm,
                    tm_rout.rout_name,
                    tm_rout.rout_code
                  FROM
                    tl_rpln
                    JOIN tm_aemp ON tm_aemp.id = tl_rpln.aemp_id
                    JOIN tm_rout ON tm_rout.id = tl_rpln.rout_id
                  WHERE tm_rout.rout_code = '$request->route_id'");

            return $queryResult = response()->json($results);

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function jsonDeteteEmployeeRoute(Request $request)
    {

        if ($this->userMenu->wsmu_delt) {

//            $routePlan = RoutePlan::on($this->db)->findorfail($request->delete_id);
//            $routePlan->delete();
            return 1;

        } else {

            return 0;
        }

    }

    public
    function routeLikeView()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $companies = DB::connection($this->db)->select("SELECT  * FROM  tm_acmp");
            $regions = DB::connection($this->db)->select("SELECT  * FROM tm_dirg");
            return view('master_data.employee.route_like')
                ->with('companies', $companies)
                ->with('regions', $regions);

        } else {
            return redirect()->back()->with('danger', 'Access Limited');

        }
    }

    public
    function jsonLoadCompanyGroup(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            return $groups = DB::connection($this->db)->select("select  * from tm_slgp where acmp_id='$request->company_id'");
        }
    }

    public
    function jsonLoadZoonName(Request $request)
    {
        if ($this->userMenu->wsmu_read) {

            return $zones = DB::connection($this->db)->select("select  * from tm_zone where dirg_id ='$request->region_id'");
        }
    }

    public
    function jsonLoadGroupZoonWiseUser(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            return $users = DB::connection($this->db)
                ->select("select  tm_aemp.id as  id,tm_aemp.aemp_name as  Name,tm_aemp.aemp_usnm as code
                  from  tl_sgsm
                  join tm_aemp on  tm_aemp.id=tl_sgsm.aemp_id
                  where tl_sgsm.zone_id='$request->zoon_id' and tl_sgsm.slgp_id='$request->group_id'");

        }


    }

    public
    function jsonSubmitRouteLike(Request $request)
    {
        //dd($request);
        $from_user = $request->from_user_id;
        $to_user = $request->to_user_id;
        /*$to_user = '72024';
        $from_user = '1';*/
        /*$to_user='1';
        $from_user = '146';*/

        $a_type = $request->a_type;

        if ($a_type == "replace") {
            DB::beginTransaction();
            try {
                DB::connection($this->db)->delete("DELETE FROM `tl_srdi` WHERE `aemp_id`='$to_user'");
                DB::connection($this->db)->delete("DELETE FROM `tl_rpln` WHERE `aemp_id`='$to_user'");
                DB::connection($this->db)->delete("DELETE FROM `tl_srth` WHERE `aemp_id`='$to_user'");
                DB::connection($this->db)->delete("DELETE FROM `tl_sgsm` WHERE `aemp_id`='$to_user'");
                DB::connection($this->db)->update("UPDATE `tl_rpln` SET `aemp_id`='$to_user' WHERE `aemp_id`='$from_user'");
                DB::connection($this->db)->update("UPDATE `tl_srdi` SET `aemp_id`='$to_user' WHERE `aemp_id`='$from_user'");
                DB::connection($this->db)->update("UPDATE `tl_srth` SET `aemp_id`='$to_user' WHERE `aemp_id`='$from_user'");
                DB::connection($this->db)->update("UPDATE `tl_sgsm` SET `aemp_id`='$to_user' WHERE `aemp_id`='$from_user'");


                DB::commit();
                return redirect()->back()->with('success', 'Successfully Replaced!!!');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            try {
                $routeList = DB::connection($this->db)->select("SELECT t2.rout_name, t2.rout_code, t2.base_id, t1.rpln_day,
 t2.acmp_id, t2.cont_id, t2.lfcl_id, '1' AS var  FROM `tl_rpln` t1 
 INNER JOIN tm_rout t2 ON t1.`rout_id`=t2.id WHERE t1.`aemp_id`='$from_user'");
                $len = sizeof($routeList);
                $count = 0;

                for ($i = 0; $i < $len; $i++) {
                    $randId = round(microtime(true) * 1000);
                    $count++;
                    $route = new Route();
                    $route->rout_name = $routeList[$i]->rout_name;
                    $route->rout_code = "ST" . $randId . $i;
                    $route->base_id = $routeList[$i]->base_id;
                    $route->acmp_id = $routeList[$i]->acmp_id;
                    $route->cont_id = $this->currentUser->employee()->cont_id;
                    $route->lfcl_id = 1;
                    $route->aemp_iusr = $this->currentUser->employee()->id;
                    $route->aemp_eusr = $this->currentUser->employee()->id;
                    $route->var = 1;
                    $route->attr1 = '';
                    $route->attr2 = '';
                    $route->attr3 = 0;
                    $route->attr4 = 0;
                    $route->save();
                    $rout_id = $route->id;


                    $pjp = new PJP();
                    $pjp->setConnection($this->db);
                    $pjp->aemp_id = $to_user;
                    $pjp->rpln_day = $routeList[$i]->rpln_day;
                    $pjp->rout_id = $rout_id;
                    $pjp->cont_id = $this->currentUser->employee()->cont_id;
                    $pjp->lfcl_id = 1;
                    $pjp->aemp_iusr = $this->currentUser->employee()->id;
                    $pjp->aemp_eusr = $this->currentUser->employee()->id;
                    $pjp->var = 1;
                    $pjp->attr1 = '';
                    $pjp->attr2 = '';
                    $pjp->attr3 = 0;
                    $pjp->attr4 = 0;
                    $pjp->save();

                }

                if (isset($request->dealer)) {
                    DB::connection($this->db)->delete("DELETE FROM `tl_srdi` WHERE `aemp_id`='$to_user'");
                    $dealer = "INSERT IGNORE INTO `tl_srdi`(`aemp_id`, `dlrm_id`, `acmp_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
SELECT " . $to_user . " as `aemp_id`, `dlrm_id`, `acmp_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr` FROM `tl_srdi` WHERE `aemp_id`='$from_user'";
                    DB::connection($this->db)->select($dealer);

                }
                if (isset($request->thana)) {
                    DB::connection($this->db)->delete("DELETE FROM `tl_srth` WHERE `aemp_id`='$to_user'");
                    $thana = "INSERT IGNORE INTO `tl_srth`(`aemp_id`, `than_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
SELECT " . $to_user . " as `aemp_id`, `than_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr` FROM `tl_srth` WHERE `aemp_id`='$from_user'";
                    DB::connection($this->db)->select($thana);
                }
                if (isset($request->priceList)) {
                    DB::connection($this->db)->delete("DELETE FROM `tl_sgsm` WHERE `aemp_id`='$to_user'");
                    $priceList = "INSERT IGNORE INTO `tl_sgsm`(`slgp_id`, `aemp_id`, `aemp_code`, `zone_id`, `plmt_id`,
 `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) SELECT `slgp_id`, " . $to_user . " AS `aemp_id`, '' AS `aemp_code`, `zone_id`,
  `plmt_id`, `cont_id`,
 `lfcl_id`, `aemp_iusr`, `aemp_eusr`
 FROM `tl_sgsm` WHERE `aemp_id`='$from_user'";
                    DB::connection($this->db)->select($priceList);

                }


                DB::commit();
                return redirect()->back()->with('success', 'Successfully Created!!!');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }


            /*$query1 = "INSERT INTO `tm_rout`(`rout_name`, `rout_code`, `base_id`, `acmp_id`, `cont_id`, `lfcl_id`, `aemp_iusr`,
`aemp_eusr`, `var`, `attr1`, `attr2`, `attr3`, `attr4`) VALUES ('$route_rout_name', '$route_rout_code', '$route_base_id',
'$route_acmp_id', '$route_cont_id', '$route_lfcl_id', '$route_aemp_iusr', '$route_aemp_eusr', '$route_var', '$route_attr1',
'$route_attr2', '$route_attr3', '$route_attr4')";

            $user = DB::connection($this->db)->insert($query1);

            DB::connection($this->db)->insert($query1);
        }
        return $count;
      //  return $len;
        /*$route = new Route();
        for ($i=0; $i<$len;$i++){
            $route->rout_name = $routeList[$i]->rout_name;
            $route->rout_code = "ST".$routeList[$i]->rout_code.'7803';
            $route->base_id = $routeList[$i]->base_id;
            $route->acmp_id =$routeList[$i]->acmp_id;
            $route->cont_id = $this->currentUser->employee()->cont_id;
            $route->lfcl_id = 1;
            $route->aemp_iusr = $this->currentUser->employee()->id;
            $route->aemp_eusr = $this->currentUser->employee()->id;
            $route->var = 1;
            $route->attr1 = '';
            $route->attr2 = '';
            $route->attr3 = 0;
            $route->attr4 = 0;
           // $routeInsert[] = $route;
            $route->save();
            dd($route);
        }*/
            // return uniqid();
            /*return $users = DB::connection($this->db)->select("SELECT t2.rout_name, t2.rout_code, t2.base_id,
 t2.acmp_id  FROM `tl_rpln` t1
 INNER JOIN tm_rout t2 ON t1.`rout_id`=t2.id WHERE t1.`aemp_id`='$from_user'");*/

            /* DB::beginTransaction();
             try {*/
            /*$routeInsert = array();
            $route = new Route();
            $route->setConnection($this->db);
            foreach ($routeList as $routel){

                $route->rout_name = $routel->rout_name;
                $route->rout_code = $routel->rout_code.'7803';
                $route->base_id = $routel->base_id;
                $route->acmp_id = $routel->acmp_id;
                $route->cont_id = $this->currentUser->employee()->cont_id;
                $route->lfcl_id = 1;
                $route->aemp_iusr = $this->currentUser->employee()->id;
                $route->aemp_eusr = $this->currentUser->employee()->id;
                $route->var = 1;
                $route->attr1 = '';
                $route->attr2 = '';
                $route->attr3 = 0;
                $route->attr4 = 0;
                $routeInsert[] = $route;
                //$route->save();
            }*/
            /*$route->save($routeInsert);
                dd($routeInsert);*/

            /*  DB::commit();
              return $route;
            //  return redirect()->back()->withInput()->with('success', 'successfully Created');


          } catch (\Exception $e) {
              DB::rollback();
              throw $e;
          }*/


            /*SELECT
              t1.id                                        AS id,
              concat(t1.aemp_name, '(', t1.aemp_usnm, ')') AS name
            FROM tm_aemp AS t1
              INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
            WHERE t2.slgp_id = $request->sales_group_id");*/

            //$this->currentUser->employee()->id, $this->currentUser->employee()->id

        }


        /* DB::connection($this->db)->select("SELECT
   t1.id                                        AS id,
   concat(t1.aemp_name, '(', t1.aemp_usnm, ')') AS name
 FROM tm_aemp AS t1
   INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
 WHERE t2.slgp_id = $request->sales_group_id");*/

        /*if ($this->userMenu->wsmu_read) {

            return $users=DB::connection($this->db)
                ->select("select  tm_aemp.id as  id,tm_aemp.aemp_name as  Name,tl_sgsm.aemp_code as code
                  from  tl_sgsm
                  join tm_aemp on  tm_aemp.id=tl_sgsm.aemp_id
                  where tl_sgsm.zone_id='$request->zoon_id' and tl_sgsm.slgp_id='$request->group_id'");

        }*/


    }

    public
    function jsonLoadGroup(Request $request)
    {

        if ($this->userMenu->wsmu_read) {

            return $users = DB::connection($this->db)
                ->select("select  tm_aemp.id as  id,tm_aemp.aemp_name as  Name,tl_sgsm.aemp_code as code
                  from  tl_sgsm
                  join tm_aemp on  tm_aemp.id=tl_sgsm.aemp_id
                  where tl_sgsm.zone_id='$request->zoon_id' and tl_sgsm.slgp_id='$request->group_id'");

        }

    }


    public
    function employeeUploadInsert(Request $request)
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

    public
    function employeeGroupZonePriceListUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new EmployeeGroupZonePricelistUpload(), 'user_bluk_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function employeeHrisUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new EmployeeMenuGroup(), 'hris_user_bulk_create_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function employeeHrisUpload()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('master_data.employee.employee_hris');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function employeeHrisUploadInsert(Request $request)
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

    public
    function employeeGroupZonePriceListInsert(Request $request)
    {
        //dd($request);
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new EmployeeGroupZonePricelistUpload(), $request->file('import_file'));
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


    public
    function employeehrisCreate(Request $request)
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

    public function showPassword(Request $request)
    {

        return view('master_data.employee.employee_psw_show');

    }

    public function jsonShowPassword(Request $request)
    {

        $this->currentUser = Auth::user();
        $country_id = $this->currentUser->country()->id;
        $results = DB::select("SELECT users.name,users.email as staff_id,tt_pswd.pswd_opwd,tt_pswd.pswd_npwd
                    FROM tt_pswd
                    JOIN users ON users.email = tt_pswd.pswd_user 
                    WHERE pswd_user = '$request->staff_id' and tt_pswd.cont_id='$country_id'
                    ORDER BY tt_pswd.id DESC LIMIT 1");
        if (count($results) > 0) {

            return response()->json($results);

        } else {

            $results = DB::select("SELECT users.name,users.email as staff_id,users.email as pswd_opwd,users.email AS pswd_npwd
                    FROM users
                    WHERE email='$request->staff_id' and users.cont_id='$country_id' LIMIT 1");

            if (count($results) > 0) {

                return response()->json($results);

            } else {

                $results = [];
                return response()->json($results);

            }

        }

    }
    //New formation of Employee Edit
    //Feed Employee Selection dropdown with employee data
    public function getEmployeeUsnm(Request $request)
    {
        $crnt_aemp_oid = Auth::user()->employee()->id;
        $empId = $request->empId;
        // $emp_list=DB::connection($this->db)->select("SELECT id,aemp_name,aemp_usnm FROM tm_aemp where aemp_usnm LIKE '$empId%'  
        //             AND  slgp_id in(select id from tm_slgp where acmp_id 
        //             in(SELECT acmp_id from tl_emcm where aemp_id=$crnt_aemp_oid)) ORDER BY id");
        $emp_list = DB::connection($this->db)->select("SELECT id,aemp_name,aemp_usnm FROM tm_aemp where aemp_usnm LIKE '$empId%'  
         ORDER BY id");

        return array('emp_list' => $emp_list);
    }

    public function getNewEditIndexPage(Request $request)
    {
        $smnu = SubMenu::where(['wsmn_ukey' =>'employee_new', 'cont_id' => $this->currentUser->country()->id])->first();
        $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' =>$smnu->id])->first();
        if (isset($request->aemp_id_temp)) {
            $aemp_temp = $request->aemp_id_temp;
            $q = '';
            if ($permission->wsmu_vsbl) {
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
                    $employees = DB::connection($this->db)->select("SELECT id,aemp_usnm,aemp_name FROM tm_aemp ORDER BY id DESC");
                }

                return view('master_data.employee.new_index')->with('aemp_temp', $aemp_temp)->with("employees", $employees)->with('permission', $permission)->with('search_text', $q);

            } else {
                return view('theme.access_limit')->with('search_text', $q);
            }

        } else {
            $q = '';
            if ($permission->wsmu_vsbl) {
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
                    $employees = DB::connection($this->db)->select("SELECT id,aemp_usnm,aemp_name FROM tm_aemp ORDER BY id DESC");
                }

                return view('master_data.employee.new_index')->with("employees", $employees)->with('permission', $permission)->with('search_text', $q);

            } else {
                return view('theme.access_limit')->with('search_text', $q);
            }
        }

        //var_dump($aemp_temp);
        //var_dump($request);

    }


    //Showw Employee Info According to Selection
    public function editEmployeeOnChangeDropdown($id)
    {
        $smnu = SubMenu::where(['wsmn_ukey' =>'employee_new', 'cont_id' => $this->currentUser->country()->id])->first();
        $permission = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' =>$smnu->id])->first();
        if ($permission->wsmu_updt) {
            $empId = $this->currentUser->employee()->id;

            //   $employee_all = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $masterRoles = MasterRole::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $appMenuGroup = AppMenuGroup::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $employee = Employee::on($this->db)->findorfail($id);
            $userRole = Role::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $site_code=$this->getSiteCode($employee->site_id);
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
                        WHERE t1.aemp_id = $id AND t1.lfcl_id=1");

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
            $salesGroup = DB::connection($this->db)->select("SELECT
                            id,
                            slgp_name,
                            slgp_code
                            FROM tm_slgp;");
            $zoneGroup = DB::connection($this->db)->select("SELECT
                            id,
                            zone_name,
                            zone_code
                            FROM tm_zone;");
            $country_id = $this->currentUser->country()->id;
            $acmp_list = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            $slgp_list = DB::connection($this->db)->select("SELECT id,slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $zone_list = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` as id, zone_name, zone_code FROM `user_area_permission` WHERE `aemp_id`=$empId");
            $than_list = DB::connection($this->db)->select("SELECT `id`, `than_code`, `than_name` FROM `tm_than`");
            $emp_than_list = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_name, t2.aemp_usnm, t3.than_name, t3.than_code,
             t4.dsct_name FROM `tl_srth` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id INNER JOIN tm_than t3 ON t1.than_id = t3.id 
            INNER JOIN tm_dsct t4 ON t3.dsct_id = t4.id WHERE t1.`aemp_id`='$id';");

            $depot_acmp = DB::connection($this->db)->select("select acmp_name,acmp_code from tm_acmp  where id in(select acmp_id from tl_emcm where aemp_id=$id)");
            $countries = DB::select("SELECT id,cont_code,cont_name FROM tl_cont Order by cont_name ASC");
            $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
            return view('master_data.employee.new_edit')->with('employee', $employee)->with('appMenuGroup', $appMenuGroup)->with('companyMapping', $companyMapping)
                ->with('depotMapping', $depotMapping)->with('salesGroupMapping', $salesGroupMapping)->with('routePlanMapping', $routePlanMapping)->with('zoneGroupMapping', $zoneGroupMapping)
                ->with('btn_position', 'btn_employee1')->with('userRoles', $userRole)->with('masterRoles', $masterRoles)->with('salesGroup', $salesGroup)
                ->with('zoneGroup', $zoneGroup)->with('permission', $permission)->with('acmp_list', $acmp_list)->with('slgp_list', $slgp_list)
                ->with('zone_list', $zone_list)->with('depot_acmp', $depot_acmp)->with('ecmp', $ecmp)
                ->with('countries', $countries)->with('than_list', $than_list)->with('emp_than_list',$emp_than_list)
                ->with('site_code',$site_code);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function addEmpThanaMapping(Request $request)
    {
        //dd($request);
        $than_id = $request->than_id_mapping;
        $emp_id = $request->id;
        $thana = json_decode(stripslashes($than_id));
        $message = 0;
        //$thana = explode(",", $than_id);
        //dd($thana);

        try{
            for ($i=0; $i < sizeof($thana); $i++){
                $thanSR = ThanaSRMapping::on($this->db)->where(['aemp_id' => $emp_id, 'than_id' => $thana[$i]])->first();
                if ($thanSR == null){
                    $thanaSRMapping = new ThanaSRMapping();
                    $thanaSRMapping->setConnection($this->db);
                    $thanaSRMapping->aemp_id  =$emp_id;
                    $thanaSRMapping->than_id  = $thana[$i];
                    $thanaSRMapping->cont_id = $this->currentUser->country()->id;
                    $thanaSRMapping->lfcl_id = 1;
                    $thanaSRMapping->aemp_iusr = $this->currentUser->employee()->id;
                    $thanaSRMapping->aemp_eusr = $this->currentUser->employee()->id;
                    $thanaSRMapping->var = 1;
                    $thanaSRMapping->attr1 = '';
                    $thanaSRMapping->attr2 = '';
                    $thanaSRMapping->attr3 = 0;
                    $thanaSRMapping->attr4 = 0;
                    $thanaSRMapping->save();
                }
            }
        }catch (\Throwable $th) {
            $message = 1;
        }
        $emp_than_list = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_name, t2.aemp_usnm, t3.than_name, t3.than_code,
 t4.dsct_name FROM `tl_srth` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id INNER JOIN tm_than t3 ON t1.than_id = t3.id 
 INNER JOIN tm_dsct t4 ON t3.dsct_id = t4.id WHERE t1.`aemp_id`='$emp_id';");
        return array('message' => $message, 'empThanaList' => $emp_than_list);
    }

    public function deleteEmpThanaMapping(Request $request){
        $than_id = $request->than_id_mapping;
        $emp_id = $request->id;
        $thana = json_decode($than_id);

        $message = 0;
        //$thana = explode(",", $than_id);
        //dd($thana);
        try{
            for ($i=0; $i < sizeof($thana); $i++){
                $thanaSRMapping = ThanaSRMapping::on($this->db)->findorfail($thana[$i]);
                $thanaSRMapping->delete();

            }

        }catch (\Throwable $th) {
            $message = 1;

        }


        $emp_than_list = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_name, t2.aemp_usnm, t3.than_name, t3.than_code,
 t4.dsct_name FROM `tl_srth` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id INNER JOIN tm_than t3 ON t1.than_id = t3.id 
 INNER JOIN tm_dsct t4 ON t3.dsct_id = t4.id WHERE t1.`aemp_id`='$emp_id';");


        return array('message' => $message, 'empThanaList' => $emp_than_list);
    }


    public function addEmpCompany(Request $request)
    {
        $emp_id = Employee::on($this->db)->where(['aemp_usnm' => $request->emp_usnm])->first();
        $message = 0;
        if ($this->userMenu->wsmu_updt) {
            $company = Company::on($this->db)->where(['acmp_code' => $request->acmp_code])->first();
            if ($company != null) {
                $comEmp = CompanyEmployee::on($this->db)->where(['acmp_id' => $company->id, 'aemp_id' => $emp_id->id])->first();
                if ($comEmp == null) {
                    $comEmp = new CompanyEmployee();
                    $comEmp->setConnection($this->db);
                    $comEmp->aemp_id = $emp_id->id;
                    $comEmp->acmp_id = $company->id;
                    $comEmp->cont_id = $this->currentUser->country()->id;
                    $comEmp->lfcl_id = 1;
                    $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $comEmp->save();
                } else {
                    //$message= "This company already exis";
                    $message = 1;
                }
            } else {
                //return "wrong company code";
                $message = 2;
            }
        } else {
            // return "no access";
            $message = 3;
        }
        $companyMapping = DB::connection($this->db)->select("SELECT t1.id as id,t2.id AS acmp_id,t2.acmp_name,
                        t2.acmp_code FROM tl_emcm AS t1
                        INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id WHERE t1.aemp_id = $emp_id->id");
        $slgp_list = DB::connection($this->db)->select("SELECT slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$emp_id->id)");
        return array(
            'companyMapping' => $companyMapping,
            'message' => $message,
            'slgp_list' => $slgp_list
        );
    }

    function deleteEmpCompany(Request $request)
    {
        $emp_id = Employee::on($this->db)->where(['aemp_usnm' => $request->emp_usnm])->first();
        if ($this->userMenu->wsmu_delt) {
            $comEmp = CompanyEmployee::on($this->db)->findorfail($request->id);
            $comEmp->delete();
            $companyMapping = DB::connection($this->db)->select("SELECT
        t1.id as id,
        t2.id AS acmp_id,
        t2.acmp_name,
        t2.acmp_code
      FROM tl_emcm AS t1
        INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
      WHERE t1.aemp_id = $emp_id->id");
            $slgp_list = DB::connection($this->db)->select("SELECT slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$emp_id->id)");
            return array(
                'companyMapping' => $companyMapping,
                'slgp_list' => $slgp_list
            );
        } else {
            $companyMapping = DB::connection($this->db)->select("SELECT
        t1.id as id,
        t2.id AS acmp_id,
        t2.acmp_name,
        t2.acmp_code
      FROM tl_emcm AS t1
        INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
      WHERE t1.aemp_id = '$emp_id->id'");
            $slgp_list = DB::connection($this->db)->select("SELECT slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$emp_id->id)");
            return array(
                'companyMapping' => $companyMapping,
                'slgp_list' => $slgp_list
            );
        }
    }


    public function deleteEmpCompanyNew(Request $request){
        $id = $request->id;
        $emp_id = $request->emp_id;

        $comEmp = CompanyEmployee::on($this->db)->findorfail($request->id);
        $comEmp->delete();

        $companyMapping = DB::connection($this->db)->select("SELECT
        t1.id as id,
        t2.id AS acmp_id,
        t2.acmp_name,
        t2.acmp_code
      FROM tl_emcm AS t1
        INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
      WHERE t1.aemp_id = $emp_id");
        $slgp_list = DB::connection($this->db)->select("SELECT slgp_name,slgp_code FROM tm_slgp where acmp_id in(select acmp_id from tl_emcm where aemp_id=$emp_id)");

        return array(
            'companyMapping' => $companyMapping,
            'slgp_list' => $slgp_list
        );

    }

//Add group
    public function addEmpSlgp(Request $request)
    {
        //dd($request);
        $date = date("Y-m-d H:i:s");
        $emp_id = $request->emp_id;
        $emp_usnm = $request->emp_usnm;
        $acmp_code = $request->acmp_code;
        $slgp_code = $request->slgp_code;
        $plmt_code = $request->plmt_code;
        $zone_code = $request->zone_code;
        $extra_price_list = $request->extra_price_list;
        $dlrm_id = $request->dlrm_id;

        if ($plmt_code == "extra_price_list"){
            $priceList = PriceList::on($this->db)->where(['plmt_code' => $extra_price_list])->first();
            $plmt_code = $priceList->id;
        }else{
            $plmt_code = $request->plmt_code;
        }
        $message = 0;
        if ($this->userMenu->wsmu_updt) {

            if ($acmp_code!=''){
                $comEmp = CompanyEmployee::on($this->db)->where(['acmp_id' => $acmp_code, 'aemp_id' => $emp_id])->first();
                if ($comEmp == null) {
                    $comEmp = new CompanyEmployee();
                    $comEmp->setConnection($this->db);
                    $comEmp->aemp_id = $emp_id;
                    $comEmp->acmp_id = $acmp_code;
                    $comEmp->cont_id = $this->currentUser->country()->id;
                    $comEmp->lfcl_id = 1;
                    $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $comEmp->save();
                } else {
                    //$message= "This company already exis";
                    $message = 1;
                }
            }

            if ($slgp_code!=''){
                DB::connection($this->db)->select("INSERT IGNORE INTO tl_egpr (aemp_id,group_id,created_at,updated_at) values('$emp_id','$slgp_code','$date','$date')");
            }
            if($zone_code!=''){
                DB::connection($this->db)->select("INSERT IGNORE INTO tl_ezpr (aemp_id,zone_id,created_at,updated_at) values('$emp_id','$zone_code','$date','$date')");
            }


            if ($slgp_code!=''){
                $salesGroupEmp = SalesGroupEmployee::on($this->db)->where(['slgp_id' => $slgp_code, 'aemp_id' => $emp_id, 'plmt_id' => $plmt_code])->first();
                if ($salesGroupEmp == null) {
                    $salesGroupEmp = new SalesGroupEmployee();
                    $salesGroupEmp->setConnection($this->db);
                    $salesGroupEmp->aemp_id = $emp_id;
                    $salesGroupEmp->slgp_id = $slgp_code;
                    $salesGroupEmp->plmt_id = $plmt_code;
                    $salesGroupEmp->zone_id = $zone_code;
                    $salesGroupEmp->cont_id = $this->currentUser->country()->id;
                    $salesGroupEmp->lfcl_id = 1;
                    $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->save();

                } else {
                    $message = 1;
                }
            }


            if ($dlrm_id != ""){
                $depot = Depot::on($this->db)->where(['dlrm_code' => $request->dlrm_id])->first();
                $depotEmployee = DepotEmployee::on($this->db)->where(['dlrm_id' => $depot->id, 'aemp_id' => $emp_id])->first();
                if ($depotEmployee == null){
                    $depotEmployee = new DepotEmployee();
                    $depotEmployee->setConnection($this->db);
                    $depotEmployee->aemp_id = $emp_id;
                    $depotEmployee->acmp_id = $acmp_code;
                    $depotEmployee->dlrm_id = $depot->id;
                    $depotEmployee->cont_id = $this->currentUser->country()->id;
                    $depotEmployee->lfcl_id = 1;
                    $depotEmployee->aemp_iusr = $this->currentUser->employee()->id;
                    $depotEmployee->aemp_eusr = $this->currentUser->employee()->id;
                    $depotEmployee->save();
                }
            }else{
                $message = 1;
            }

        } else {
            $message = 2;
        }
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
                        WHERE t1.aemp_id = $emp_id AND t1.lfcl_id=1");
        $salesGroupMapping = DB::connection($this->db)->select("SELECT
                      t1.id as id, t2.id AS slgp_id, t2.slgp_name, t2.slgp_code, t1.plmt_id, t3.plmt_name, t3.plmt_code,
                      t1.zone_id, t4.zone_name, t4.zone_code
                      FROM tl_sgsm AS t1
                      INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
                      INNER JOIN tm_plmt AS t3 ON t1.plmt_id = t3.id
                      INNER JOIN tm_zone AS t4 ON t1.zone_id = t4.id
                      WHERE t1.aemp_id = '$emp_id'");
        $companyMapping = DB::connection($this->db)->select("SELECT t1.id as id,t2.id AS acmp_id,t2.acmp_name,
                        t2.acmp_code FROM tl_emcm AS t1
                        INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id WHERE t1.aemp_id =$emp_id");
        return array('depotMapping' => $depotMapping, 'salesGroupMapping' => $salesGroupMapping, 'companyMapping' => $companyMapping, 'message' => $message);
    }
    public function deleteEmpSlgp(Request $request)
    {
        $id = $request->id;
        $emp_id = $request->emp_usnm;
        $message = 0;
        if ($this->userMenu->wsmu_delt) {
            $salesGroupEmp = SalesGroupEmployee::on($this->db)->findorfail($id);
            $salesGroupEmp->delete();

        } else {
            $message = 3;
        }
        $salesGroupMapping = DB::connection($this->db)->select("SELECT
                      t1.id as id, t2.id AS slgp_id, t2.slgp_name, t2.slgp_code, t1.plmt_id, t3.plmt_name, t3.plmt_code,
                      t1.zone_id, t4.zone_name, t4.zone_code
                      FROM tl_sgsm AS t1
                      INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
                      INNER JOIN tm_plmt AS t3 ON t1.plmt_id = t3.id
                      INNER JOIN tm_zone AS t4 ON t1.zone_id = t4.id
                      WHERE t1.aemp_id = $emp_id");
        return array(
            'salesGroupMapping' => $salesGroupMapping,
            'message' => $message);

    }


//get Emp Group Price List
    public function getEmpSlgp($acmp_id)
    {
        $empId = $this->currentUser->employee()->id;
        $country_id = $this->currentUser->country()->id;
        $slgp_list = DB::connection($this->db)->select("SELECT `slgp_id` as id, `slgp_code`, `slgp_name` FROM `user_group_permission` WHERE `acmp_id`='$acmp_id' AND aemp_id='$empId'");

        return $slgp_list;
    }
    public function getEmpSlgpPriceList($slgp_code)
    {
        $country_id = $this->currentUser->country()->id;
        $plmt = SalesGroup::on($this->db)->where(['id' => $slgp_code])->first();
        $plmt_id = $plmt->plmt_id;
        $price_list = DB::connection($this->db)->select("SELECT
                t1.id AS id, t1.plmt_name AS name, t1.plmt_code AS code,t1.lfcl_id   AS status_id
                FROM tm_plmt AS t1 WHERE  t1.cont_id = $country_id AND t1.id=$plmt_id");
        return $price_list;
    }
    public function getDealerList(Request $request){
        $country_id = $this->currentUser->country()->id;
        $zone_id = $request->zone_code;
        $slgp_code = $request->slgp_code;

        $sql = "SELECT t1.id, t1.dlrm_code, t1.dlrm_name FROM `tm_dlrm` t1 INNER JOIN tm_base t2 ON t1.base_id = t2.id 
INNER JOIN tm_zone t3 ON t2.zone_id=t3.id WHERE t1.cont_id='$country_id' and t3.id='$zone_id' and t1.slgp_id='$slgp_code' and t1.lfcl_id='1'";

        $dealer_list = DB::connection($this->db)->select($sql);
        return $dealer_list;
    }

//Update Employee Info
    public function updateEmpInfo(Request $request, $id)
    {
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
                    $emp->aemp_otml = $request['auto_email'];
                } else {
                    $emp->aemp_otml = 0;
                }
                $emp->aemp_lonl = $request['location_on'];
                $emp->aemp_usnm = trim($request['email']);
                if (isset($request->input_img)) {
                    $imageIcon = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $file = $request->file('input_img');
                    $imageName = $this->currentUser->country()->cont_imgf . "/master/profile/" . uniqid() . '.' . $request->input_img->getClientOriginalExtension();
                    $s3 = AWS::createClient('s3');
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $imageName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $imageIcon,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
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
                $emp->site_id =$this->getSiteId($request['site_id']);
                $emp->aemp_crdt = $request['aemp_crdt'];
                $emp->aemp_issl = $request['aemp_issl'];
                $emp->aemp_asyn = $request['aemp_asyn'];
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->amng_id = $request->amng_id;
                $emp->save();
                $empUser = User::find($emp->aemp_lued);
                $empUser->remember_token = '';
                $empUser->email = trim($request['email']);
                $empUser->name = $request['name'];
                $empUser->save();
                $cont_id = $request->cont_id;
                $visa_no = $request->visa_no;
                $expr_date = $request->expr_date;
                $ecmp = EmployeeCountryMapping::on($this->db)->where('aemp_id', $id)->first();
                if($cont_id !='' || $visa_no !=''){
                    if ($ecmp) {
                        $ecmp->cont_id = $cont_id;
                        $ecmp->visa_no = $visa_no;
                        $ecmp->expr_date = $expr_date;
                        $ecmp->aemp_eusr = $this->aemp_id;
                        $ecmp->save();
                    } else {
                        $ecmp = new EmployeeCountryMapping();
                        $ecmp->setConnection($this->db);
                        $ecmp->aemp_id = $emp->id;
                        $ecmp->cont_id = $cont_id;
                        $ecmp->visa_no = $visa_no;
                        $ecmp->expr_date = $expr_date;
                        $ecmp->aemp_iusr = $this->aemp_id;
                        $ecmp->aemp_eusr = $this->aemp_id;
                        $ecmp->save();
                    }
                }

                DB::connection($this->db)->commit();
                // dd('sdfsd');
                return $message = "Updated Successfully";
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
            }
        } else {
            return $message = "Wrong user name";
        }

    }

    public function addEmpDlrm(Request $request, $id)
    {
        $message = 0;
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
                    //return redirect()->back()->with('success', 'successfully Added')->with('btn_position', 'btn_dealer');
                } else {
                    // return back()->withInput()->with('danger', 'Already exist Or Code not Match')->with('btn_position', 'btn_dealer');
                    $message = 1;
                }
            } else {
                //return back()->withInput()->with('danger', 'Dealer Code not Match')->with('btn_position', 'btn_dealer');
                $message = 10;
            }

        } else {
            // return redirect()->back()->with('danger', 'Access Limited')->with('btn_position', 'btn_dealer');
            $message = 2;
        }
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
                        WHERE t1.aemp_id = $id AND t1.lfcl_id=1");
        return array('depotMapping' => $depotMapping, 'message' => $message);
    }

//Delete Employee Dlrm
    public function deleteEmpDlrm($id, $eid)
    {
        if ($this->userMenu->wsmu_delt) {

            $depotEmployee = DepotEmployee::on($this->db)->findorfail($id);
            $depotEmployee->delete();
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
        WHERE t1.aemp_id = $eid AND t1.lfcl_id=1");

            return array(
                'depotMapping' => $depotMapping);

        }
    }


//Add Employee Route Plan
    public function addEmpRoutePlan(Request $request)
    {
        $rout_codes = $request->rout_code;
        $rout_code = json_decode($rout_codes);

        $day_names = $request->day_name;
        $id = $request->id;
        $day_name = json_decode($day_names);

        $message = 0;
        $sql = "DELETE FROM `tl_rpln` WHERE `aemp_id`='$id'";
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        DB::connection($this->db)->select(DB::raw($sql));
        for($i=0; $i<sizeof($rout_code);$i++){
            if ($rout_code[$i]!='' && $day_name[$i]!=''){
                $route = Route::on($this->db)->where(['rout_code' => $rout_code[$i]])->first();
                $message = 0;
                if ($route != null) {
                    $pjp = new PJP();
                    $pjp->setConnection($this->db);
                    $pjp->aemp_id = $id;
                    $pjp->rpln_day = $day_name[$i];
                    $pjp->rout_id = $route->id;
                    $pjp->cont_id = $this->currentUser->employee()->cont_id;
                    $pjp->lfcl_id = 1;
                    $pjp->aemp_iusr = $this->currentUser->employee()->id;
                    $pjp->aemp_eusr = $this->currentUser->employee()->id;
                    $pjp->save();

                } else {
                    $message = 1;
                }
            }
        }

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
        return array('message' => $message, 'routePlanMapping' => $routePlanMapping);
    }

// Delete Employee Route Plan
    public function deleteEmpRoutePlan(Request $request, $id)
    {
        $rpln_ids = $request->rpln_id;
        $rpln = json_decode($rpln_ids);

        $message = 0;


            /*for ($j=0; $j<sizeof($rpln); $j++){
                $dd[] = $rpln[$j];
                /*$pjp = PJP::on($this->db)->findorfail($rpln[$j]);
                $pjp->delete();*/
            //}


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
        return array('message' => $message, 'routePlanMapping' => $routePlanMapping, 'rpln'=>$rpln);

    }
    public function deleteEmpThanaMappingEMP(Request $request){
        $rpln_ids = $request->rpln_id;
        $id = $request->id;
        $rpln = json_decode($rpln_ids);

        $message = 0;


        for ($j=0; $j<sizeof($rpln); $j++){
           // $dd[] = $rpln[$j];
            $pjp = PJP::on($this->db)->findorfail($rpln[$j]);
            $pjp->delete();
        }


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
        return array('message' => $message, 'routePlanMapping' => $routePlanMapping, 'rpln'=>$rpln);
    }

    public function addEmpZoneGroupMapping(Request $request, $id)
    {
        try {
            $message = 0;
            if ($this->userMenu->wsmu_updt) {
                //$zone = Zone::on($this->db)->where(['id' => $request->zone_code])->first();
                // $group = SalesGroup::on($this->db)->where(['id' => $request->slgp_code])->first();
                $zone = $request->zone_code;
                $slgp = $request->slgp_code;

                $comEmp = new TsmGroupZoneMapping();
                $comEmp->setConnection($this->db);
                $comEmp->aemp_id = $id;
                $comEmp->zone_id = $zone;
                $comEmp->slgp_id = $slgp;
                $comEmp->cont_id = $this->currentUser->country()->id;
                $comEmp->lfcl_id = 1;
                $comEmp->aemp_iusr = $this->currentUser->employee()->id;
                $comEmp->aemp_eusr = $this->currentUser->employee()->id;
                $comEmp->save();
                DB::connection($this->db)->select("UPDATE `tm_aemp` SET `aemp_mngr` = '$id', `aemp_lmid` = '$id' 
                WHERE `zone_id` = '$zone' AND `slgp_id` = '$slgp' AND lfcl_id='1' AND `edsg_id`='1' and role_id='1'");
            } else {
                $message = 1;
            }
            $zoneGroupMapping = DB::connection($this->db)->select("SELECT
            t1.id,
            t2.slgp_name,
            t2.slgp_code,
            t3.zone_code,
            t3.zone_name
            FROM tl_zmzg AS t1
            INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
            INNER JOIN tm_zone AS t3 ON t1.zone_id = t3.id
            WHERE t1.aemp_id = $id");
            return array('message' => $message, 'zoneGroupMapping' => $zoneGroupMapping);
        } catch (\Throwable $th) {
            $message = -1;
            $exists = DB::connection($this->db)->select("Select t1.aemp_usnm from tm_aemp t1 WHERE id=(select aemp_id from tl_zmzg WHERE zone_id= '$request->zone_code' AND slgp_id='$request->slgp_code')");
            return array('exists' => $exists, 'message' => $message);
        }
    }

    public function deleteEmpZoneGroupMapping($id, $eid)
    {
        $message = 0;
        if ($this->userMenu->wsmu_delt) {
            $comEmp = TsmGroupZoneMapping::on($this->db)->findorfail($id);
            $comEmp->delete();
        } else {
            $message = 1;
        }
        $zoneGroupMapping = DB::connection($this->db)->select("SELECT
        t1.id,
        t2.slgp_name,
        t2.slgp_code,
        t3.zone_code,
        t3.zone_name
        FROM tl_zmzg AS t1
        INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id
        INNER JOIN tm_zone AS t3 ON t1.zone_id = t3.id
        WHERE t1.aemp_id = $eid;");
        return array('message' => $message, 'zoneGroupMapping' => $zoneGroupMapping);
    }

// Employee Active Inactive
    public function empActvInactv($id)
    {
        $message = 0;
        if ($this->userMenu->wsmu_delt) {
            $employee = Employee::on($this->db)->findorfail($id);
            $employee->lfcl_id = $employee->lfcl_id == 1 ? 2 : 1;
            $employee->aemp_iusr = $this->currentUser->employee()->id;
            $employee->save();
            $user = User::find($employee->aemp_lued);
            $user->lfcl_id = $employee->lfcl_id;
            $user->remember_token = '';
            $user->save();
        } else {
            $message = 1;
        }
        return array('emp' => $employee->lfcl_id, 'message' => $message);
    }

    public function empPassReset($id)
    {
        $newPassword = '';
        $message = 0;
        if ($this->userMenu->wsmu_updt) {
            $employee = Employee::on($this->db)->findorfail($id);
            $employee->aemp_utkn = time() . $id . substr(md5(mt_rand()), 5, 22);
            $employee->aemp_eusr = $this->currentUser->employee()->id;
            $user = User::find($employee->aemp_lued);
            $user->password = bcrypt($user->email);
            $user->remember_token = '';
            $newPassword = $user->email;
            $employee->save();
            $user->save();
            $oldPassword = DB::table('tt_pswd')->where('pswd_user', $user->email)->orderBy('id', 'DESC')->select('pswd_opwd')->pluck('pswd_opwd')->first();
            if ($oldPassword) {

                $oldPassword = $oldPassword;

            } else {

                $oldPassword = $user->email;
            }

            DB::table('tt_pswd')->insert(
                array(
                    'pswd_time' => date('Y-m-d H:i:s'),
                    'pswd_user' => $user->email,
                    'pswd_opwd' => $oldPassword,
                    'pswd_npwd' => $newPassword,
                    'cont_id' => $user->cont_id,
                    'lfcl_id' => $user->lfcl_id,
                )
            );

        } else {
            $message = 1;
        }
        return $message;
    }


    public function existingEmployee(Request $request)
    {
        $slgp_id = $request->slgp_id;
        $zone_id = $request->zone_id;

        /*$slgp_id = 37;
        $zone_id = 35;*/


        $depotMapping = DB::connection($this->db)->select("SELECT t1.id, t2.aemp_usnm, t2.aemp_name FROM `tl_zmzg` t1 
INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id WHERE t1.`zone_id`='$zone_id' AND t1.`slgp_id`='$slgp_id';");
        return $depotMapping;


    }
}
