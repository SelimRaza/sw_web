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
use App\MasterData\District;
use App\MasterData\DistrictSRMapping;
use App\MasterData\DistrictSRMappingDataExport;
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

class SRDistrictMappingController extends Controller
{
    private $access_key = 'SRDistrictMappingController';
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

    // public function depotFormatGen(Request $request)
    // {
    //     if ($this->userMenu->wsmu_crat) {
    //         return Excel::download(new Depot(), 'SRThanaMapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
    //     } else {
    //         return redirect()->back()->with('danger', 'Access Limited');
    //     }
    // }

    public function empThanAdd()
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
            return view('Depot.Depot.districtsr_mapping')->with('permission', $this->userMenu)
                ->with('slgp_data', $slgp_data)
                ->with('zone_data', $zone_data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empDistrictMapping(Request $request){

        if ($this->userMenu->wsmu_crat) {

            $employee=Employee::on($this->db)->where('aemp_usnm',$request->aemp_code)->first(['id']);
            $dsct=District::on($this->db)->where('dsct_code',$request->than_code)->first(['id']);
            $salesGroupSKU1 = DistrictSRMapping::on($this->db)->where(['aemp_id' =>$employee->id, 'dsct_id' =>$dsct->id])->first();
            if ($salesGroupSKU1 == null) {

                $thanaSRMapping = new DistrictSRMapping();
                $thanaSRMapping->setConnection($this->db);
                $thanaSRMapping->aemp_id  = $employee->id;
                $thanaSRMapping->dsct_id  = $dsct->id;
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

    public function districtSRMappingFormat(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new DistrictSRMapping(), 'district_employee_mapping_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function districtSRMappingInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {

                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new DistrictSRMapping(), $request->file('import_file'));
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

    public function srDistrictMappingDataExport(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(DistrictSRMappingDataExport::create($request->slgp_id, $request->zone_id), 'DistrictSRMappingData' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function srWiseDistrictList(Request $request,$staff_code){
        return $results=DB::connection($this->db)->table('tl_srds as t1')
            ->join('tm_dsct as t3', 't3.id', '=', 't1.dsct_id')
            ->join('tm_aemp as t2', 't2.id', '=', 't1.aemp_id') 
            ->select('t1.id AS id','t3.dsct_code AS thana_code','t3.dsct_name AS thana_name','t2.aemp_name AS user_name','t2.aemp_usnm AS user_code')
            ->where(['t2.aemp_usnm'=>$staff_code])->get();
    }
    public function deleteSrWiseDistrictList(Request $request,$id){
        DistrictSRMapping::where('id', $id)->delete();
        return response()->json(['Success']);
    }

}
