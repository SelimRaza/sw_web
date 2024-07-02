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
use App\MasterData\ThanaSRMappingDataExport;
use App\MasterData\Ward;
use App\MasterData\WardSRMapping;
use App\DataExport\WardSRMappingDataExport;
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

class SRWardMappingController extends Controller
{
    private $access_key = 'ward/sr-mapping';
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
            return Excel::download(new Depot(), 'SRWardMapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empWardAdd()
    {
        if ($this->userMenu->wsmu_updt) {

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

            return view('Depot.Depot.wardsr_mapping')->with('permission', $this->userMenu)
                ->with('slgp_data', $slgp_data)
                ->with('zone_data', $zone_data);

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empWardMapping(Request $request){

        if ($this->userMenu->wsmu_crat) {
            $employee=Employee::on($this->db)->where('aemp_usnm',$request->aemp_code)->first(['id']);
            $ward=Ward::on($this->db)->where('ward_code',$request->ward_code)->first(['id']);
            $salesGroupSKU1 = WardSRMapping::on($this->db)->where(['aemp_id' =>$employee->id, 'ward_id' =>$ward->id])->first();
            if ($salesGroupSKU1 == null) {

                $wardSRMapping = new WardSRMapping();
                $wardSRMapping->setConnection($this->db);
                $wardSRMapping->aemp_id  = $employee->id;
                $wardSRMapping->ward_id  = $ward->id;
                $wardSRMapping->cont_id = $this->currentUser->country()->id;
                $wardSRMapping->lfcl_id = 1;
                $wardSRMapping->aemp_iusr = $this->currentUser->employee()->id;
                $wardSRMapping->aemp_eusr = $this->currentUser->employee()->id;
                $wardSRMapping->var = 1;
                $wardSRMapping->attr1 = '';
                $wardSRMapping->attr2 = '';
                $wardSRMapping->attr3 = 0;
                $wardSRMapping->attr4 = 0;
                $wardSRMapping->save();
                return redirect()->back()->with('success', 'successfully Added');

            } else {

                return redirect()->back()->with('danger', 'Already exist');
            }
        }
        else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function depotEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new WardSRMapping(), 'ward_employee_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
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
                    Excel::import(new WardSRMapping(), $request->file('import_file'));
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

    public function dataExportSRWardMappingData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(WardSRMappingDataExport::create($request->slgp_id, $request->zone_id), 'SRWardMappingReport_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
