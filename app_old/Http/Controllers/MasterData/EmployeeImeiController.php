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

class EmployeeImeiController extends Controller
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
        if ($this->userMenu->wsmu_updt) {
            return view('master_data.employee.reset_imei');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function resetImeiStore(Request $request){
        if ($this->userMenu->wsmu_updt) {
            $sraff_id = $request->aemp_usnm;

//            return $sraff_id;
            try {
                if(isset($request->view) && $request->view == 1){
                    $old_id = DB::table('users')->where('email',$sraff_id)->first();

                    return view('master_data.employee.reset_imei', ['staff' => $old_id]);
                }else{
                    $ret = DB::table('users')->where(['email' => $sraff_id])->update(['device_imei' => 'N']);
                    return redirect()->back()->with('success', 'Device IMEI Reset Successfully');
                }

            }catch(\Exception $exception){
                return $exception->getMessage();
                return redirect()->back()->with('danger', 'Invalid Staff ID');
            }



        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
