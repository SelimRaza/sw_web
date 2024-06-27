<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\GroupDataUpload;


use App\BusinessObject\SalesGroupEmployee;
use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\MasterData\Route;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Excel;

class GroupEmployeeMappingUploadController extends Controller
{
    private $access_key = 'tbld_GroupEmployeeMappingUploadController_format';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }


    public function groupEmployeeMappingUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SalesGroupEmployee(), 'group_employee_mapping_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function groupEmployeeMappingUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.upload.group_employee_mapping_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function groupEmployeeMappingUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SalesGroupEmployee(), $request->file('import_file'));
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

    /* Employee upload End*/


}