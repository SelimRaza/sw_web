<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Category;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{

    private $access_key = 'tm_itcg';

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
        if ($this->userMenu->wsmu_vsbl) {
            $categorys = Category::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.category.index')->with('categorys', $categorys)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
         //   $productGroups = ProductGroup::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.category.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $category = new Category();
        $category->setConnection($this->db);
        $category->itcg_name = $request->name;
        $category->itcg_code = $request->code;
        $category->lfcl_id = 1;
        $category->cont_id = $this->currentUser->country()->id;
        $category->aemp_iusr = $this->currentUser->employee()->id;
        $category->aemp_eusr = $this->currentUser->employee()->id;
        $category->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $category = Category::on($this->db)->findorfail($id);
            return view('master_data.category.show')->with('company', $category);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $category = Category::on($this->db)->findorfail($id);
          //  $productGroups = ProductGroup::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.category.edit')->with('category', $category);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::on($this->db)->findorfail($id);
        $category->itcg_name = $request->name;
        $category->itcg_code = $request->code;
        $category->aemp_eusr = $this->currentUser->employee()->id;
        $category->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $category = Category::on($this->db)->findorfail($id);
            $category->lfcl_id = $category->lfcl_id == 1 ? 2 : 1;
            $category->aemp_eusr = $this->currentUser->employee()->id;
            $category->save();
            return redirect('/category');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function passReset()
    {
        $userAll = User::where('password', '=', '')->get();
        foreach ($userAll as $user) {
            $user->password = bcrypt($user->email);
            $user->save();
        }
    }


    public function categoryFormatGen(Request $request)
    {
        //echo "afdsaf";

        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Category(), 'category_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function categoryMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Category(), $request->file('import_file'));
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
