<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Category;
use App\MasterData\SubCategory;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Image;

class SubCategoryController extends Controller
{
    private $access_key = 'tm_itsg';
    private $currentUser;
    private $userMenu;

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
        // if ($this->userMenu->wsmu_vsbl) {
        //     $subCategorys = SubCategory::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
        //     return view('master_data.sub_category.index')->with('subCategorys', $subCategorys)->with('permission', $this->userMenu);
        // } else {
        //     return view('theme.access_limit');
        // }
        if ($this->userMenu->wsmu_vsbl) {
			$country_id = $this->currentUser->country()->id;
			$subCategorys =DB::connection($this->db)->select("SELECT t1.`id`, t1.`itsg_name`, t1.`itsg_code`, t1.`itcg_id`, t1.`cont_id`,
			t1.`lfcl_id`, t2.itcg_name FROM `tm_itsg` t1 INNER JOIN tm_itcg t2 ON t1.itcg_id = t2.id WHERE t1.cont_id='$country_id' Order by t1.`id`");
            
            return view('master_data.sub_category.index')->with('subCategorys', $subCategorys)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $categorys = Category::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.sub_category.create')->with('categorys', $categorys);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {

        $subCategory = new SubCategory();
        $subCategory->setConnection($this->db);
        $subCategory->itsg_name = $request->name;
        $subCategory->itsg_code = $request->code;
        $subCategory->itcg_id = $request->category_id;
        $subCategory->lfcl_id = 1;
        $subCategory->cont_id = $this->currentUser->employee()->cont_id;
        $subCategory->aemp_iusr = $this->currentUser->employee()->id;
        $subCategory->aemp_eusr = $this->currentUser->employee()->id;
        $subCategory->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $subCategory = SubCategory::on($this->db)->findorfail($id);
            return view('master_data.sub_category.show')->with('subCategory', $subCategory);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $subCategory = SubCategory::on($this->db)->findorfail($id);
            $categorys = Category::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.sub_category.edit')->with('subCategory', $subCategory)->with('categorys', $categorys);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::on($this->db)->findorfail($id);
        $subCategory->itsg_name = $request->name;
        $subCategory->itsg_code = $request->code;
        $subCategory->itcg_id = $request->category_id;

        $subCategory->aemp_eusr = $this->currentUser->employee()->id;

        $subCategory->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $subCategory = SubCategory::on($this->db)->findorfail($id);
            $subCategory->lfcl_id = $subCategory->lfcl_id == 1 ? 2 : 1;
            $subCategory->aemp_eusr = $this->currentUser->employee()->id;
            $subCategory->save();
            return redirect('/sub-category');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
