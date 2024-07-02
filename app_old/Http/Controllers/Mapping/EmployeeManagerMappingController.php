<?php

namespace App\Http\Controllers\Mapping;

use App\Mapping\EmployeeManagerUpload;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use Excel;
use Response;
use AWS;
use function GuzzleHttp\Psr7\try_fopen;
use PDF;

class EmployeeManagerMappingController extends Controller
{
    private $access_key = 'manager-mapping';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
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
        if ($this->userMenu->wsmu_vsbl) {
            return view('Mapping.EmployeeManager.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request){
        $info = $request->validate([
            'staff_id' => 'required|string',
            'manager_id' => 'required|string'
        ]);

        $staff = EmployeeManagerUpload::on($this->db)->where('aemp_usnm', $info['staff_id'])->first();
        $manager = EmployeeManagerUpload::on($this->db)->where('aemp_usnm', $info['manager_id'])->first();

//        dd($staff!= null && $manager != null );
        if($staff != null && $manager != null ){
            try {
                $staff->update([
                    'aemp_mngr' => $manager->id,
                    'aemp_lmid' => $manager->id,
                    'lfcl_id'   => 1,
                    'aemp_eusr' => $this->aemp_id
                ]);

                return redirect()->back()->with('success', 'Employee Manager Mapping Updated Successfully');
            }catch (\Exception $e)
            {
                return $e->getMessage();
                return redirect()->back()->with('danger', 'Please Provide Valid Information');
            }
        }
        else{
            return redirect()->back()->with('danger', 'Please Provide Valid Information');
        }
    }

//
//
//    public function edit($id)
//    {
//
//        if ($this->userMenu->wsmu_updt) {
//            $app = FakeGps::on($this->db)->findOrFail($id);
//            return view('Gps.edit', compact('app'));
//        } else {
//            return redirect()->back()->with('danger', 'Access Limited');
//        }
//    }
//
//
//    public function update(Request $request, $id){
//        $info = $request->validate([
//            'name' => 'nullable|string',
//            'url' => 'required|string'
//        ]);
//
//        $info['aemp_eusr'] = $this->aemp_id;
//
//        try {
//            $app = FakeGps::on($this->db)->findOrFail($id);
//
//            $app->update($info);
//
//            return redirect()->route('gps-apps.index')->with('success', 'Fake GPS App Information Updated Successfully');
//        }catch (\Exception $e)
//        {
//            return redirect()->back()->with('danger', 'Please Provide Valid Information');
//        }
//    }
//
    public function managerMappingFormat(){
        return Excel::download(new EmployeeManagerUpload(), 'employee_manager_mapping_' . date("Y-m-d H:i:s") . '.xlsx');
    }
//
//
    public function managerMappingUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new EmployeeManagerUpload(), $request->file('import_file'));
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
//
//
//
//    public function destroy ($id){
//
//        try {
//            $app = FakeGps::on($this->db)->findOrFail($id);
//
//            $app->update(['lfcl_id' => ($app->lfcl_id == 1) ? 2 : 1]);
//
//            return redirect()->route('gps-apps.index')->with('success', 'Fake GPS App Status Changed Successfully');
//        }catch (\Exception $e)
//        {
//            return redirect()->back()->with('danger', 'Please Provide Valid Information');
//        }
//    }
}
