<?php

namespace App\Http\Controllers\Gps;

use App\MasterData\FakeGps;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use Excel;
use Response;
use AWS;
use function GuzzleHttp\Psr7\try_fopen;
use PDF;

class GpsController extends Controller
{
    private $access_key = 'gps-apps';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;
    private $free_amount = 0;
    private $free_qty    = 0;
    private $free_item   = 0;
    private $order_no   = 0;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
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
            $appes = FakeGps::on($this->db)->paginate(20);
            return view('Gps.index', compact('appes'))->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function create(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return view('Gps.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request){
        $info = $request->validate([
            'name' => 'nullable|string',
            'url' => 'required|string'
        ]);

        $info['aemp_iusr'] = $info['aemp_eusr'] = $this->aemp_id;
        $info['lfcl_id'] = 1;

        try {
            FakeGps::insert($info);

            return redirect()->route('gps-apps.index')->with('success', 'Fake GPS App Information Stored Successfully');
        }catch (\Exception $e)
        {
            return redirect()->back()->with('danger', 'Please Provide Valid Information');
        }
    }



    public function edit($id)
    {

        if ($this->userMenu->wsmu_updt) {
            $app = FakeGps::on($this->db)->findOrFail($id);
            return view('Gps.edit', compact('app'));
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id){
        $info = $request->validate([
            'name' => 'nullable|string',
            'url' => 'required|string'
        ]);

        $info['aemp_eusr'] = $this->aemp_id;

        try {
            $app = FakeGps::on($this->db)->findOrFail($id);

            $app->update($info);

            return redirect()->route('gps-apps.index')->with('success', 'Fake GPS App Information Updated Successfully');
        }catch (\Exception $e)
        {
            return redirect()->back()->with('danger', 'Please Provide Valid Information');
        }
    }

    public function show($path){
        if($path = 'getFakeGpsFormat'){
            return Excel::download(new FakeGps(), 'fake_gps_apps_' . date("Y-m-d H:i:s") . '.xlsx');
        }
    }


    public function fakeGpsUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new FakeGps(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    public function destroy ($id){

        try {
            $app = FakeGps::on($this->db)->findOrFail($id);

            $app->update(['lfcl_id' => ($app->lfcl_id == 1) ? 2 : 1]);

            return redirect()->route('gps-apps.index')->with('success', 'Fake GPS App Status Changed Successfully');
        }catch (\Exception $e)
        {
            return redirect()->back()->with('danger', 'Please Provide Valid Information');
        }
    }
}
