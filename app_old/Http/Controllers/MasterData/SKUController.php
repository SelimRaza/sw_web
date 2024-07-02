<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Company;
use App\MasterData\ItemClass;
use App\MasterData\SKU;
use App\MasterData\NewSku;
use App\MasterData\SKUActiveInactive;
use App\MasterData\UploadSKU;
use App\MasterData\SubCategory;
use App\MasterData\Unit;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use AWS;
use Response;
use Excel;
use App\MasterData\ItemCountryMapping;

class SKUController extends Controller
{
    private $access_key = 'tm_amim';
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

    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $q = "";
            if ($request->has('search_text')) {
                $q = request('search_text');
                $skus = SKU::on($this->db)->where(function ($query) use ($q) {
                    $query->where('amim_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('amim_olin', 'LIKE', '%' . $q . '%')
                        ->orWhere('amim_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('amin_snme', 'LIKE', '%' . $q . '%')
                        ->orWhere('id', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');
            } else {
                $skus = SKU::on($this->db)->where('cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');
            }


            /*->where('t1.cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');*/

            return view('master_data.sku.index')->with('skus', $skus)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function newSkuList(Request $request)
    {
        echo "sdf";
        /*if ($this->userMenu->wsmu_vsbl) {
            $q = "";
            if ($request->has('search_text')) {
                $q = request('search_text');
                $skus = SKU::on($this->db)->where(function ($query) use ($q) {
                    $query->where('amim_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('amim_olin', 'LIKE', '%' . $q . '%')
                        ->orWhere('amim_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('amin_snme', 'LIKE', '%' . $q . '%')
                        ->orWhere('id', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');
            } else {
                $skus = SKU::on($this->db)->where('cont_id', $this->currentUser->country()->id)->paginate(500)->setPath('');
            }
            return view('master_data.new_sku.index')->with('skus', $skus)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }*/
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $subCategorys = SubCategory::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemClass = ItemClass::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemUnit = Unit::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $company = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $country = DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 ORDER BY cont_name ASC");
            return view('master_data.sku.create')->with('itemClass', $itemClass)->with('subCategorys', $subCategorys)->with('itemUnit', $itemUnit)->with('company', $company)->with('country',$country);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        //return $request->all();
        DB::connection($this->db)->beginTransaction();
        try {
            $imageName = "";
            $imageIcon = "";
            $unique = uniqid();
            $s3 = AWS::createClient('s3');
            if ($request->amim_imgl != "") {
                $file = $request->file('amim_imgl');
                $imageName = 'bdp/Master/item/' . $unique . '1.' . $request->amim_imgl->getClientOriginalExtension();

                $s3->putObject(array(
                    'Bucket' => 'prgfms',
                    'Key' => $imageName,
                    'SourceFile' => $file,
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ));

            }

            if ($request->amim_icon != "") {
                $imageIcon = 'bdp/Master/item/' . $unique . '.' . $request->amim_icon->getClientOriginalExtension();
                $file_icon = $request->file('amim_icon');
                $s3 = AWS::createClient('s3');
                $s3->putObject(array(
                    'Bucket' => 'prgfms',
                    'Key' => $imageIcon,
                    'SourceFile' => $file_icon,
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ));
            }
            $sku = new SKU();
            $sku->amim_name = $request->amim_name;
            $sku->amim_code = $request->amim_code;
            $sku->amim_olin = isset($request->amim_olin) ? $request->amim_olin : "";
            $sku->amim_bcod = isset($request->amim_bcod) ? $request->amim_bcod : "";
            $sku->amin_snme = isset($request->amin_snme) ? $request->amin_snme : "";
            $sku->amim_duft = $request->amim_duft;
            $sku->amim_dunt = $request->amim_dunt;
            $sku->amim_runt = $request->amim_runt;
            $sku->amim_dppr = isset($request->amim_dppr) ? $request->amim_dppr : "0";
            $sku->amim_tppr = isset($request->amim_tppr) ? $request->amim_tppr : "0";
            $sku->amim_mrpp = isset($request->amim_mrpp) ? $request->amim_mrpp : "0";
            $sku->amim_acmp = $request->amim_acmp;
            $sku->amim_imgl = $imageName;
            $sku->amim_imic = $imageIcon;
            $sku->itsg_id = $request->itsg_id;
            $sku->itcl_code ='N';
            $sku->itcl_name ='N';
            $sku->itcl_id = $request->itcl_id;
            $sku->amim_tkns = isset($request->amim_tkns) ? $request->amim_tkns : "";
            $sku->amim_colr = isset($request->amim_colr) ? $request->amim_colr : "";
            $sku->amim_pexc = isset($request->amim_pexc) ? $request->amim_pexc : "0";
            $sku->amim_pvat = isset($request->amim_pvat) ? $request->amim_pvat : "0";
            $sku->amim_cbm = isset($request->amim_cbm) ? $request->amim_cbm : "0";
            $sku->amim_issl = $request->amim_issl == 'on' ? 1 : 0;
            $sku->cont_id = $this->currentUser->country()->id;
            $sku->lfcl_id = 1;
            $sku->aemp_iusr = $this->currentUser->employee()->id;
            $sku->aemp_eusr = $this->currentUser->employee()->id;
            $sku->save();
            if(Auth::user()->country()->module_type==2){
                $cont_id=$request->cont_id;
                if($cont_id!=''){
                    $icmp=new ItemCountryMapping();
                    $icmp->setconnection($this->db);
                    $icmp->amim_id=$sku->id;
                    $icmp->cont_id=$cont_id;
                    $icmp->aemp_iusr=Auth::user()->employee()->id;
                    $icmp->aemp_eusr=Auth::user()->employee()->id;
                    $icmp->save();
                }
            }
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Added');
        } catch (Exception $e) {
            DB::connection($this->db)->rollback();
            //   throw $e;
            return redirect()->back()->withInput()->with('danger', 'Not Created');
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $sku = SKU::on($this->db)->findorfail($id);
            $subCategorys = SubCategory::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemClass = ItemClass::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemUnit = Unit::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $company = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $icmp=DB::connection($this->db)->select("SELECT t2.cont_name FROM tl_icmp t1 INNER JOIN myprg_comm.tl_cont t2 ON t1.cont_id=t2.id WHERE t1.amim_id=$id");
            return view('master_data.sku.show')->with('sku', $sku)->with('itemClass', $itemClass)->with('subCategorys', $subCategorys)
            ->with('itemUnit', $itemUnit)->with('company', $company)->with('icmp',$icmp);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $sku = SKU::on($this->db)->findorfail($id);
            $subCategorys = SubCategory::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemClass = ItemClass::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $itemUnit = Unit::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $company = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $country = DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 ORDER BY cont_name ASC");
            $icmp=DB::connection($this->db)->select("SELECT t1.cont_id FROM tl_icmp t1 where t1.amim_id=$id");
            return view('master_data.sku.edit')->with('subCategorys', $subCategorys)->with('itemClass', $itemClass)->with('sku', $sku)->with('itemUnit', $itemUnit)
            ->with('company', $company)->with('icmp',$icmp)->with('country',$country);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function update(Request $request, $id)
    {

        // dd($request);
        //return $request->file('amim_imgl').'-----'.$request->file('amim_icon');
        $sku = SKU::on($this->db)->findorfail($id);
        $imageName = "";
        $imageIcon = "";
        $unique = uniqid();
        $s3 = AWS::createClient('s3');
        if ($request->amim_imgl != "") {
            $file = $request->file('amim_imgl');
            $imageName = 'bdp/Master/item/' . $unique . '1.' . $request->amim_imgl->getClientOriginalExtension();

            $s3->putObject(array(
                'Bucket' => 'prgfms',
                'Key' => $imageName,
                'SourceFile' => $file,
                'ACL' => 'public-read',
                'ContentType' => $file->getMimeType(),
            ));

        }

        if ($request->amim_icon != "") {
            $imageIcon = 'bdp/Master/item/' . $unique . '.' . $request->amim_icon->getClientOriginalExtension();
            $file_icon = $request->file('amim_icon');
            $s3 = AWS::createClient('s3');
            $s3->putObject(array(
                'Bucket' => 'prgfms',
                'Key' => $imageIcon,
                'SourceFile' => $file_icon,
                'ACL' => 'public-read',
                'ContentType' => $file_icon->getMimeType(),
            ));
        }
        $sku->amim_name = $request->amim_name;
        $sku->amim_code = $request->amim_code;
        $sku->amim_olin = isset($request->amim_olin) ? $request->amim_olin : "";
        $sku->amim_bcod = isset($request->amim_bcod) ? $request->amim_bcod : "";
        $sku->amin_snme = isset($request->amin_snme) ? $request->amin_snme : "";
        $sku->amim_duft = $request->amim_duft;
        $sku->amim_dunt = $request->amim_dunt;
        $sku->amim_runt = $request->amim_runt;
        $sku->amim_dppr = isset($request->amim_dppr) ? $request->amim_dppr : "0";
        $sku->amim_tppr = isset($request->amim_tppr) ? $request->amim_tppr : "0";
        $sku->amim_mrpp = isset($request->amim_mrpp) ? $request->amim_mrpp : "0";
        $sku->amim_acmp = $request->amim_acmp;
        if($imageName !=''){
            $sku->amim_imgl = $imageName;
        }
        if($imageIcon !=''){
            $sku->amim_imic = $imageIcon;
        }
        
        
        $sku->itsg_id = $request->itsg_id;
        $sku->itcl_id = $request->itcl_id;
        $sku->amim_tkns = isset($request->amim_tkns) ? $request->amim_tkns : "";
        $sku->amim_colr = isset($request->amim_colr) ? $request->amim_colr : "";
        $sku->amim_pexc = isset($request->amim_pexc) ? $request->amim_pexc : "0";
        $sku->amim_pvat = isset($request->amim_pvat) ? $request->amim_pvat : "0";
        $sku->amim_cbm = isset($request->amim_cbm) ? $request->amim_cbm : "0";
        $sku->amim_issl = isset($request->amim_issl) ? 1 : 0;
        $sku->save();
        if(Auth::user()->country()->module_type==2){
            $cont_id=$request->cont_id;
            if($cont_id!=''){
                $icmp=ItemCountryMapping::on($this->db)->where('amim_id',$id)->first();
                if($icmp){
                    $icmp->cont_id=$cont_id;
                    $icmp->aemp_eusr=Auth::user()->employee()->id;
                    $icmp->save();
                }
                else{
                    $icmp=new ItemCountryMapping();
                    $icmp->setconnection($this->db);
                    $icmp->amim_id=$sku->id;
                    $icmp->cont_id=$cont_id;
                    $icmp->aemp_iusr=Auth::user()->employee()->id;
                    $icmp->aemp_eusr=Auth::user()->employee()->id;
                    $icmp->save();
                }
                
                
            }
        }
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $sku = SKU::on($this->db)->findorfail($id);
            $sku->lfcl_id = $sku->lfcl_id == 1 ? 2 : 1;
            $sku->aemp_eusr = $this->currentUser->employee()->id;
            $sku->save();
            return redirect('/sku');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function filterSKU(Request $request)
    {
        $skus = SKU::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
        return Response::json($skus);

    }

    public function filterSKU1(Request $request)
    {
        $skus = SKU::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
        $sku_dropdown = '<option></option>';
        foreach ($skus as $sku) {
            $sku_dropdown .= '<option value="' . $sku->id . '" data-item_code="' . $sku->id
                . '" data-price="' . $sku->ctn_size . '">' . $sku->name . ' ('
                . $sku->name . ')' . '</option>';
        }
        $new_line = '';
        $new_line .= '<tr>';
        $new_line .= '<td><select name="sku_id[]" onchange="get_unit(id)" id="sku_id' . $request->count
            . '" class="form-control" required>' . $sku_dropdown . '</select></td>';
        $new_line .= '<td><input type="text" name="item_code[]" id="item_code' . $request->count
            . '" class="form-control" readonly required/>
                         <input type="hidden" name="ctn_size[]" id="ctn_size' . $request->count . '" value = ""></td>';
        $new_line .= '<td><input type="text" name="unit_price[]" id="unit_price' . $request->count
            . '"  class="form-control"  required/></td>';
        $new_line .= '<td><input type="text" name="quantity[]" id="quantity' . $request->count
            . '" class="form-control integer-input" onkeyup="get_sub_total_qty(id);" required/></td>';
        $new_line .= '<td><input type="text" name="value[]" id="value' . $request->count
            . '" class="form-control"  required/></td>';
        $new_line .= '<td><input type="text" name="value[]" id="value' . $request->count
            . '" class="form-control"  required/></td>';
        $new_line .= '<td><span class="btn btn-xs red removeLine"><i class="fa fa-times"></i>delete</span></td>';

        $new_line .= '</tr>';
        // echo $new_line;
        return $new_line;

    }

    public function newSku(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $q = "";

            $skus = DB::statement("SELECT t1.`name`, t1.item_code, t1.factor,
 t1.`r_unit`, t1.`item_class` FROM `tbld_new_item_group` t1 WHERE `item_code` NOT IN 
 (SELECT DISTINCT amim_code FROM `tm_pldt` WHERE 1)");


            return view('master_data.sku.new_sku')->with('skus', $skus)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

    public function skuUpload(){
        return view('master_data.sku.sku_upload')->with('permission', $this->userMenu);
    }

    public function skuActiveInactiveFormat(Request $request){
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SKUActiveInactive(), 'sku_active_inactive_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuUploadFormat(Request $request){
        try {
            if ($this->userMenu->wsmu_crat) {
                return Excel::download(new UploadSKU(), 'sku_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
            } else {
                return redirect()->back()->with('danger', 'Access Limited');
            }
        }catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

//    public function skuUploadFormat(Request $request){
//        if ($this->userMenu->wsmu_crat) {
//            return Excel::download(new UploadSKU(), 'sku_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
//        } else {
//            return redirect()->back()->with('danger', 'Access Limited');
//        }
//    }

    public function skuActiveInactive(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SKUActiveInactive(), $request->file('import_file'));
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

    public function skuBulkUpload(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new UploadSKU(), $request->file('import_file'));
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



}
