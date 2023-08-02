<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\ProductClass;
use App\MasterData\ProductGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\Company;
use Illuminate\Support\Facades\Auth;

class ProductClassController extends Controller
{
    private $access_key = 'ProductClassController';
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
            $productClasses = ProductClass::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.product_class.index')->with('productClasses', $productClasses)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $productGroup = ProductGroup::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.product_class.create')->with('productGroup', $productGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    public function store(Request $request)
    {
        $productGroup = new ProductClass();
        $productGroup->setConnection($this->db);
        $productGroup->itgp_id = $request->itgp_id;
        $productGroup->itcl_name = $request->itcl_name;
        $productGroup->itcl_code = $request->itcl_code;
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
            $productClass = ProductClass::on($this->db)->findorfail($id);
            return view('master_data.product_class.show')->with('productClass', $productClass);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $productClass = ProductClass::on($this->db)->findorfail($id);
            $productGroup = ProductGroup::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.product_class.edit')->with('productClass', $productClass)->with('productGroup', $productGroup);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $productGroup = ProductClass::on($this->db)->findorfail($id);
        $productGroup->itcl_name = $request->itcl_name;
        $productGroup->itcl_code = $request->itcl_code;
        $productGroup->aemp_eusr = $this->currentUser->employee()->id;
        $productGroup->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $productGroup = ProductClass::on($this->db)->findorfail($id);
            $productGroup->lfcl_id = $productGroup->lfcl_id == 1 ? 2 : 1;
            $productGroup->aemp_eusr = $this->currentUser->employee()->id;
            $productGroup->save();
            return redirect('/product-class');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
