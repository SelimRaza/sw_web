<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\ProductGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class ProductGroupController extends Controller
{
    private $access_key = 'tbld_product_group';
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

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $productGroups = ProductGroup::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.product_group.index')->with('productGroups', $productGroups)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.product_group.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $productGroup = new ProductGroup();
        $productGroup->setConnection($this->db);
        $productGroup->itgp_name = $request->itgp_name;
        $productGroup->itgp_code = $request->itgp_code;
        $productGroup->lfcl_id = 1;
        $productGroup->cont_id = $this->currentUser->employee()->cont_id;
        $productGroup->aemp_iusr = $this->currentUser->employee()->id;
        $productGroup->aemp_eusr = $this->currentUser->employee()->id;
        $productGroup->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $productGroup = ProductGroup::on($this->db)->findorfail($id);
            return view('master_data.product_group.show')->with('productGroup', $productGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $productGroup = ProductGroup::on($this->db)->findorfail($id);
            return view('master_data.product_group.edit')->with('productGroup', $productGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $productGroup = ProductGroup::on($this->db)->findorfail($id);
        $productGroup->itgp_name = $request->itgp_name;
        $productGroup->itgp_code = $request->itgp_code;
        $productGroup->aemp_eusr = $this->currentUser->employee()->id;
        $productGroup->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $productGroup = ProductGroup::on($this->db)->findorfail($id);
            $productGroup->lfcl_id = $productGroup->lfcl_id == 1 ? 2 : 1;
            $productGroup->aemp_eusr = $this->currentUser->employee()->id;
            $productGroup->save();
            return redirect('/product-group');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function productGroupFormatGen(Request $request)
    {
        //echo "afdsaf";

        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new ProductGroup(), 'product_group_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function productGroupUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new ProductGroup(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
