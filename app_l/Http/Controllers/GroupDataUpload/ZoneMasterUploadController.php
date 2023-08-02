<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\GroupDataUpload;

use App\MasterData\Route;
use App\MasterData\Zone;
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

class ZoneMasterUploadController extends Controller
{
    private $access_key = 'tbld_ZoneUploadController_format';
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

    public function zoneUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Zone(), 'zone_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');

            /*$routeArray[] = ['group_id','region_id', 'zone_name', 'zone_code'];
            Excel::create('zone_upload_format_' . date("Y-m-d H:i:s"), function ($excel) use ($routeArray) {
                $excel->setTitle('Zone Upload Format');
                $excel->setCreator('FMS MDR')->setCompany('PRAN RFL Group');
                $excel->setDescription('PRAN RFL Group File System');
                $excel->sheet('Zone Upload Format', function ($sheet) use ($routeArray) {
                    $sheet->fromArray($routeArray, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function zoneMasterUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.upload.zone_master_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function zoneMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Zone(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }

                /*$path = Input::file('import_file')->getRealPath();
                $data = Excel::load($path, function ($reader) {
                })->get();
                if (!empty($data) && $data->count()) {
                    DB::beginTransaction();
                    try {
                        $i = 0;
                        foreach ($data as $key => $value) {
                            $i++;
                            $zone = new Zone();
                            $zone->name = $value->zone_name;
                            $zone->code = $value->zone_code;
                            $zone->region_id = $value->region_id;
                            $zone->sales_group_id = $value->group_id;
                            $zone->status_id = 1;
                            $zone->country_id = $this->currentUser->employee()->cont_id;
                            $zone->created_by = $this->currentUser->employee()->id;
                            $zone->updated_by = $this->currentUser->employee()->id;
                            $zone->save();
                        }

                        DB::commit();
                        return redirect()->back()->with('success', 'Successfully Uploaded');
                    } catch (\Exception $e) {
                        DB::rollback();
                      //  return redirect()->back()->with('danger', ' ON Line ' . $i);
                        throw $e;
                    }
                }*/
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    /* Employee upload End*/


}