<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Company;
use App\MasterData\ItemClass;
use App\MasterData\SKU;
use App\MasterData\NewSku;
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

class SKUControllerNew extends Controller
{
    private $access_key = 'tm_amim_new';
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
            return view('master_data.sku.create')->with('itemClass', $itemClass)->with('subCategorys', $subCategorys)->with('itemUnit', $itemUnit)->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {

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
            return view('master_data.sku.show')->with('sku', $sku)->with('itemClass', $itemClass)->with('subCategorys', $subCategorys)->with('itemUnit', $itemUnit)->with('company', $company);
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
            return view('master_data.sku.edit')->with('subCategorys', $subCategorys)->with('itemClass', $itemClass)->with('sku', $sku)->with('itemUnit', $itemUnit)->with('company', $company);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function update(Request $request, $id)
    {

        // dd($request);
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
                'ContentType' => $file->getMimeType(),
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
        $sku->amim_imgl = $imageName;
        $sku->amim_imic = $imageIcon;
        $sku->itsg_id = $request->itsg_id;
        $sku->itcl_id = $request->itcl_id;
        $sku->amim_tkns = isset($request->amim_tkns) ? $request->amim_tkns : "";
        $sku->amim_colr = isset($request->amim_colr) ? $request->amim_colr : "";
        $sku->amim_pexc = isset($request->amim_pexc) ? $request->amim_pexc : "0";
        $sku->amim_pvat = isset($request->amim_pvat) ? $request->amim_pvat : "0";
        $sku->amim_cbm = isset($request->amim_cbm) ? $request->amim_cbm : "0";
        $sku->amim_issl = isset($request->amim_issl) ? 1 : 0;
        $sku->save();
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

    public function filterSKUdddddd(Request $request)
    {
        $q="SELECT DISTINCT amim_code FROM `tm_pldt`";
        $employees = DB::connection($this->db)->table('tbld_new_item_group AS t1')
            ->join('tm_aemp AS t2', 't1.aemp_mngr', '=', 't2.id')
            ->join('tm_aemp AS t3', 't1.aemp_lmid', '=', 't3.id')
            ->join('tm_role AS t4', 't1.role_id', '=', 't4.id')
            ->join('tm_edsg AS t5', 't1.edsg_id', '=', 't5.id')
            ->leftJoin('tm_amng AS t6', 't1.amng_id', '=', 't6.id')
            ->select('t1.id AS id', 't1.aemp_usnm', 't1.aemp_name', 't1.aemp_mob1', 't1.aemp_onme', 't1.aemp_stnm',
                't1.aemp_mngr', 't2.aemp_usnm AS mnrg_usnm', 't2.aemp_name AS mnrg_name',
                't3.aemp_usnm AS lmid_usnm', 't3.aemp_name AS lmid_name', 't4.role_name', 't5.edsg_name',
                't1.aemp_emal', 't1.aemp_picn', 't1.lfcl_id', 't6.amng_name')->orderByDesc('t1.id')
            ->paginate(100);

        $employees = DB::connection($this->db)->table('tbld_new_item_group AS t1')
            ->select('t1.name', 't1.item_code', 't1.factor',
                't1.r_unit', 't1.item_class')
            ->where(function ($query) use ($q) {
                $query->Where('t1.item_code', 'not in', $q );
            })
            ->paginate(100);

    }



    public function newSku(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $q="";
            //$qq=DB::connection($this->db)->select("SELECT DISTINCT amim_code FROM `tm_pldt`");

            /*$skus = DB::connection($this->db)->select("SELECT t1.`name`, t1.item_code, t1.factor,
 t1.`r_unit`, t1.`item_class` FROM `tbld_new_item_group` t1 WHERE `item_code` NOT IN 
 (SELECT DISTINCT amim_code FROM `tm_pldt`)");*/

            DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
            $skus =  DB::connection($this->db)->table('tbld_new_item AS t1')
                ->select('t1.name', 't1.item_code', 't1.factor',
                    't1.r_unit', 't1.item_class')
                ->groupBy('t1.item_code')
                ->whereNotIn('t1.item_code', function ($query) {
                    $query->selectRaw('distinct amim_code from tm_pldt');
                })
                ->paginate(100);


            return view('master_data.sku.new_sku')->with('skus', $skus)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

}
