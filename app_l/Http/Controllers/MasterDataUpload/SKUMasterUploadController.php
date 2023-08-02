<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\MasterDataUpload;


use App\MasterData\SKU;
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

class SKUMasterUploadController extends Controller
{
    private $access_key = 'tbld_SKUMasterUploadController_format';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function skuUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SKU(), 'sku_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
            //$routeArray[] = ['name', 'code', 'bangla', 'sub_category_id', 'ctn_size', 'sku_size'];
            /* Excel::create('sku_upload_format_' . date("Y-m-d H:i:s"), function ($excel) use ($routeArray) {
                 $excel->setTitle('SKU Upload Format');
                 $excel->setCreator('FMS MDR')->setCompany('PRAN RFL Group');
                 $excel->setDescription('PRAN RFL Group File System');
                 $excel->sheet('SKU Upload Format', function ($sheet) use ($routeArray) {
                     $sheet->fromArray($routeArray, null, 'A1', false, false);
                 });
             })->download('xlsx');*/
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuMasterUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.upload.sku_master_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {


                DB::beginTransaction();
                try {
                    Excel::import(new SKU(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong '.$e);
                    //throw $e;
                }
                /*         $data = Excel::load($path, function ($reader) {
                         })->get();
                         if (!empty($data) && $data->count()) {
                             DB::beginTransaction();
                             try {
                                 $i = 0;
                                 foreach ($data as $key => $value) {
                                     $i++;
                                     $sku = new SKU();
                                     $sku->name = $value->name;
                                     $sku->code = $value->code;
                                     $sku->ln_name = $value->bangla;
                                     $sku->sub_category_id = $value->sub_category_id;
                                     $sku->ctn_size = $value->ctn_size;
                                     $sku->sku_size = $value->sku_size;
                                     $sku->status_id = 1;
                                     $sku->country_id = $this->currentUser->employee()->cont_id;
                                     $sku->created_by = $this->currentUser->employee()->id;
                                     $sku->updated_by = $this->currentUser->employee()->id;
                                     $sku->image = '';
                                     $sku->image_icon = '';
                                     $sku->save();
                                 }

                                 DB::commit();
                                 return redirect()->back()->with('success', 'Successfully Uploaded');
                             } catch (\Exception $e) {
                                 DB::rollback();
                                 return redirect()->back()->with('danger', ' ON Line ' . $i);
                                 //throw $e;
                             }
                         }*/
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }




}