<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\Base;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
use App\MasterData\Region;
use App\MasterData\Zone;
use App\MasterData\Thana;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;
class BaseController extends Controller
{
    private $access_key = 'tm_base';
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
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $bases = DB::connection($this->db)->select("SELECT t1.id, t1.`base_name`, t1.`base_code`, t2.zone_name, t2.zone_code, t1.lfcl_id 
                        FROM `tm_base` t1 INNER JOIN tm_zone t2 ON t1.`zone_id`=t2.id WHERE t1.cont_id ='$country_id' ");
            // $bases = Base::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.base.index')->with("bases", $bases)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE `lfcl_id`='1' AND cont_id='$country_id'");
            $thanas = DB::connection($this->db)->select("SELECT `id`,`than_name`,`than_code` FROM `tm_than` WHERE `lfcl_id`='1' AND cont_id='$country_id'");
            //$zones = Zone::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.base.create')->with('zones', $zones)->with('thanas', $thanas);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $base = new Base();
        $base->setConnection($this->db);
        $base->base_name = $request->name;
        $base->base_code = $request->code;
        $base->zone_id = $request->zone_id;
        $base->than_id = $request->thana_id;
        $base->cont_id = $this->currentUser->employee()->cont_id;
        $base->lfcl_id = 1;
        $base->aemp_iusr = $this->currentUser->employee()->id;
        $base->aemp_eusr = $this->currentUser->employee()->id;
        $base->var = 1;
        $base->attr1 = '';
        $base->attr2 = '';
        $base->attr3 = 0;
        $base->attr4 = 0;
        $base->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $base = Base::on($this->db)->findorfail($id);
            return view('master_data.base.show')->with('base', $base);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $country_id = $this->currentUser->employee()->cont_id;
            $base = Base::on($this->db)->findorfail($id);
            $zones = DB::connection($this->db)->select("SELECT `id`,`zone_name`,`zone_code` FROM `tm_zone` WHERE `lfcl_id`='1' AND cont_id='$country_id'");
            $thanas = DB::connection($this->db)->select("SELECT `id`,`than_name`,`than_code` FROM `tm_than` WHERE `lfcl_id`='1' AND cont_id='$country_id'");
            //  $zones = Zone::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.base.edit')->with('base', $base)->with('zones', $zones)->with('thanas', $thanas);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $base = Base::on($this->db)->findorfail($id);
        $base->base_name = $request->name;
        $base->zone_id = $request->zone_id;
        $base->than_id = $request->thana_id;
        $base->base_code = $request->code;
        $base->aemp_eusr = $this->currentUser->employee()->id;
        $base->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $base = Base::on($this->db)->findorfail($id);
            $base->lfcl_id = $base->lfcl_id == 1 ? 2 : 1;
            $base->aemp_eusr = $this->currentUser->employee()->id;
            $base->save();
            return redirect('/base');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function filterBase(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.base_name 
FROM tm_base AS t1 WHERE t1.zone_id=$request->zone_id");
        return Response::json($divisions);
    }


    public function baseUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {

            return Excel::download(new Base(), 'base_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function baseMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Base(), $request->file('import_file'));
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
