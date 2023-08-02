<?php

namespace App\Http\Controllers\GovtHeirarchy;

use App\BusinessObject\DepotStock;
use App\BusinessObject\SalesGroup;
use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Depot;
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
use App\MasterData\ThanaSRMapping;
use App\MasterData\ThanaSRMappingDataExport;
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

class SRThanaMappingController extends Controller
{
    private $access_key = 'SRThanaMappingController';
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

    public function depotFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Depot(), 'SRThanaMapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empThanAdd()
    {
        if ($this->userMenu->wsmu_updt) {
            //   $depot = Depot::on($this->db)->findorfail();
            //  $employees = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            // $depotEmployees = ThanaSRMapping::on($this->db)->where('dlrm_id', '=', $id)->get();
            //  $companys = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();

            $slgp_data = DB::connection($this->db)->select("
            SELECT
            id as slgp_id,
            slgp_name,
            slgp_code
            FROM tm_slgp ");

            $zone_data = DB::connection($this->db)->select("SELECT
            id as zone_id,
            zone_name,
            zone_code
            FROM tm_zone ");

            return view('Depot.Depot.thansr_mapping')->with('permission', $this->userMenu)
                ->with('slgp_data', $slgp_data)
                ->with('zone_data', $zone_data);

           // return view('Depot.Depot.thansr_mapping')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empThanaMapping(Request $request){

        if ($this->userMenu->wsmu_crat) {

            $employee=Employee::on($this->db)->where('aemp_usnm',$request->aemp_code)->first(['id']);
            $thana=Thana::on($this->db)->where('than_code',$request->than_code)->first(['id']);
            $salesGroupSKU1 = ThanaSRMapping::on($this->db)->where(['aemp_id' =>$employee->id, 'than_id' =>$thana->id])->first();
            if ($salesGroupSKU1 == null) {

                $thanaSRMapping = new ThanaSRMapping();
                $thanaSRMapping->setConnection($this->db);
                $thanaSRMapping->aemp_id  = $employee->id;
                $thanaSRMapping->than_id  = $thana->id;
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
            return Excel::download(new ThanaSRMapping(), 'Thana_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
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
                    Excel::import(new ThanaSRMapping(), $request->file('import_file'));
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

    public function dataExportSRThanaMappingData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(ThanaSRMappingDataExport::create($request->slgp_id, $request->zone_id), 'SRThanaMappingReport_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
