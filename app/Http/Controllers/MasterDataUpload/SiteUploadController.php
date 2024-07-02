<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\MasterDataUpload;


use App\BusinessObject\SalesGroup;
use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Division;
use App\MasterData\Employee;
use App\MasterData\Outlet;
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

class SiteUploadController extends Controller
{
    private $access_key = 'tbld_SiteUploadController_format';
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

    public function siteFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Site(), 'outlet_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
          /*  $routeArray[] = ['sub_channel_id', 'sub_channel_name', 'grade_id', 'grade_name', 'name', 'code', 'ln_name', 'address', 'ln_address', 'owner_name', 'ln_owner_name', 'mobile_1', 'mobile_2', 'email', 'house_no', 'vat_trn'];
            Excel::create('outlet_upload_format_' . date("Y-m-d H:i:s"), function ($excel) use ($routeArray) {
                $excel->setTitle('Outlet Upload Format');
                $excel->setCreator('FMS MDR')->setCompany('PRAN RFL Group');
                $excel->setDescription('PRAN RFL Group File System');
                $excel->sheet('Outlet Upload Format', function ($sheet) use ($routeArray) {
                    $sheet->fromArray($routeArray, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.upload.site_master_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $currentUser = auth()->user();
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Site(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
                /* $path = $request->file('import_file')->getRealPath();
                 $data = Excel::load($path, function ($reader) {
                 })->get();
                 if (!empty($data) && $data->count()) {
                     DB::beginTransaction();
                     try {
                         $i = 0;
                         foreach ($data as $key => $value) {
                             $i++;
                             //  $insert[] = ['name' => $value->name, 'quantity' => $value->quanity];
                             $outlet = new Outlet();
                             $outlet->name = $value->name;
                             $outlet->code = $value->code;
                             $outlet->country_id = $currentUser->employee()->country_id;
                             $outlet->created_by = $currentUser->employee()->id;
                             $outlet->updated_by = $currentUser->employee()->id;
                             $outlet->save();
                             $site = new Site();
                             $site->name = $value->name;
                             $site->code = $value->code != "" ? $value->code : "";
                             $site->ln_name = $value->ln_name != "" ? $value->ln_name : "";
                             $site->address = $value->address != "" ? $value->address : "";
                             $site->ln_address = $value->ln_address != "" ? $value->ln_address : "";
                             $site->owner_name = $value->owner_name != "" ? $value->owner_name : "";
                             $site->ln_owner_name = $value->ln_owner_name != "" ? $value->ln_owner_name : "";
                             $site->mobile_1 = $value->mobile_1 != "" ? $value->mobile_1 : "";
                             $site->mobile_2 = $value->mobile_2 != "" ? $value->mobile_2 : "";
                             $site->email = $value->email != "" ? $value->email : "";
                             $site->house_no = $value->house_no != "" ? $value->house_no : "";
                             $site->vat_trn = $value->vat_trn != "" ? $value->vat_trn : "";
                             $site->sub_channel_id = $value->sub_channel_id;
                             $site->grade_id = $value->grade_id;
                             $site->outlet_id = $outlet->id;
                             $site->map_address = '';
                             $site->zip_code = '';
                             $site->site_image = '';
                             $site->owner_image = '';
                             $site->lat = 0;
                             $site->lon = 0;
                             $site->is_verified = 0;
                             $site->status_id = 1;
                             $site->country_id = $currentUser->employee()->country_id;
                             $site->created_by = $currentUser->employee()->id;
                             $site->updated_by = $currentUser->employee()->id;
                             $site->save();

                         }
                         DB::commit();
                         return redirect()->back()->with('success', 'Successfully Uploaded');
                     } catch (\Exception $e) {
                         DB::rollback();
                         // return redirect()->back()->with('danger', ' ON Line ' . $i);
                         throw $e;
                     }
                }*/


            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


}