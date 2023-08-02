<?php

namespace App\Http\Controllers\Setting;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\AppMenuGroup;
use App\BusinessObject\AppMenuGroupLine;
use App\DataExport\EmployeeMenuGroup;
use App\Menu\MobileMenu;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class AppMenuGroupController extends Controller
{
    private $access_key = 'AppMenuGroupController';
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
        // dd($request);
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $tm_amng = AppMenuGroup::on($this->db)->where(function ($query) use ($q) {
                    $query->where('tm_amng.amng_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_amng.amng_name', 'LIKE', '%' . $q . '%');
                })->where(['tm_amng.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('tm_amng.*'))->setPath('');
            } else {
                $tm_amng = AppMenuGroup::on($this->db)->where(['tm_amng.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('tm_amng.*'));
            }
            return view('Setting.AppMenuGroup.index')->with('tm_amng', $tm_amng)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            return view('Setting.AppMenuGroup.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $appMenuGroup = new AppMenuGroup();
            $appMenuGroup->setConnection($this->db);
            $appMenuGroup->amng_name = $request->amng_name;
            $appMenuGroup->amng_code = $request->amng_code;
            $appMenuGroup->role_id = 0;
            $appMenuGroup->lfcl_id = 1;
            $appMenuGroup->cont_id = $this->currentUser->employee()->cont_id;
            $appMenuGroup->aemp_iusr = $this->currentUser->employee()->id;
            $appMenuGroup->aemp_eusr = $this->currentUser->employee()->id;
            $appMenuGroup->save();
            DB::connection($this->db)->commit();
            return redirect('/appMenuGroup/' . $appMenuGroup->id . '/edit')->with('success', "successfully Created");
        } catch
        (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }


    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $appMenuGroup = AppMenuGroup::on($this->db)->findorfail($id);
            $appMenuGroupLine = DB::connection($this->db)->select("SELECT
  t1.id AS amnd_id,
  t1.amnu_id,
  t2.amnu_name
FROM tl_amnd AS t1
  INNER JOIN tm_amnu AS t2 ON t1.amnu_id = t2.id
WHERE t1.amng_id = $id;");
            return view('Setting.AppMenuGroup.show')->with('appMenuGroup', $appMenuGroup)->with('appMenuGroupLine', $appMenuGroupLine)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {


        if ($this->userMenu->wsmu_updt) {
            $appMenuGroup = AppMenuGroup::on($this->db)->findorfail($id);
            $appMenuGroupLine = DB::connection($this->db)->select("SELECT
  t1.id AS amnd_id,
  t1.amnu_id,
  t2.amnu_name
FROM tl_amnd AS t1
  INNER JOIN tm_amnu AS t2 ON t1.amnu_id = t2.id
WHERE t1.amng_id = $id;");
            $mobileMenu = MobileMenu::on($this->db)->where(['cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1])->get();
            return view('Setting.AppMenuGroup.edit')->with('appMenuGroup', $appMenuGroup)->with('appMenuGroupLine', $appMenuGroupLine)->with('mobileMenu', $mobileMenu)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $gAppMenuGroup = AppMenuGroup::on($this->db)->findorfail($id);
        $gAppMenuGroup->amng_name = $request->amng_name;
        $gAppMenuGroup->amng_code = $request->amng_code;
        $gAppMenuGroup->aemp_eusr = $this->currentUser->employee()->id;
        $gAppMenuGroup->save();
        return redirect()->back()->with('success', 'successfully Updated');

    }

    public function destroy($id)
    {

    }


    public function menuDelete($id)
    {
        //dd($id);
        // dd($this->userMenu->wsmu_delt);
        if ($this->userMenu->wsmu_delt) {
            $appMenuGroupLine = AppMenuGroupLine::on($this->db)->findorfail($id);
            //  dd($appMenuGroupLine);
            $appMenuGroupLine->delete();
            return redirect()->back()->with('success', 'App Group Menu Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function assignToUser(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(EmployeeMenuGroup::create($id), $request->file('import_file'));
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

    public function assignMenu(Request $request, $id)
    {
        if (isset($request->amnu_id))
            DB::connection($this->db)->beginTransaction();
        try {
            foreach ($request->amnu_id as $index => $amnu_id1) {
                $appMenuGroupLine = AppMenuGroupLine::on($this->db)->where(['cont_id' => $this->currentUser->employee()->cont_id, 'amng_id' => $id, 'amnu_id' => $amnu_id1])->first();
                if ($appMenuGroupLine == null) {
                    $appMenuGroupLine = new AppMenuGroupLine();
                    $appMenuGroupLine->setConnection($this->db);
                    $appMenuGroupLine->amng_id = $id;
                    $appMenuGroupLine->amnu_id = $amnu_id1;
                    $appMenuGroupLine->lfcl_id = 1;
                    $appMenuGroupLine->cont_id = $this->currentUser->employee()->cont_id;
                    $appMenuGroupLine->aemp_iusr = $this->currentUser->employee()->id;
                    $appMenuGroupLine->aemp_eusr = $this->currentUser->employee()->id;
                    $appMenuGroupLine->save();
                }
            }
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', "successfully Added");
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }
    }

    public function uploadFormat($id)
    {

        if ($this->userMenu->wsmu_updt) {
            return Excel::download(new EmployeeMenuGroup(), 'menu_assign_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}