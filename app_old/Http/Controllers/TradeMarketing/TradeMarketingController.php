<?php

namespace App\Http\Controllers\TradeMarketing;

use App\BusinessObject\TradeMarketing;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;

class TradeMarketingController extends Controller
{
    private $access_key = 'maintain/space';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(80000000);
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

    public function index(Request $request){
        if ($this->userMenu->wsmu_vsbl) {

            $trades = TradeMarketing::on($this->db)->with('employee:id,aemp_name,aemp_usnm', 'zone:id,zone_name,zone_code')->paginate(50);

            return view('TradeMarketing.zones_mapping', [
                'permission' => $this->userMenu,
                'trades' => $trades
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function create(Request $request){
        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;

            $zones = DB::connection($this->db)->select("SELECT DISTINCT zone_id, zone_name, zone_code
                    FROM `user_area_permission`
                    WHERE `aemp_id`='$empId'");
            return view('TradeMarketing.zones_create', [
                'zones' => $zones
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    protected function store(Request $request)
    {
        $db_zone = $this->db.'.'.'tm_zone';
        $db_aemp = $this->db.'.'.'tm_aemp';

        $info = $request->validate([
            'aemp_id' => 'required|numeric|exists:'.$db_aemp.',aemp_usnm',
            'zone_id' => 'required|numeric|exists:'.$db_zone.',id',
        ]);

        $info['aemp_id'] = Auth::user()->employee()->id;
        $info['cont_id'] = Auth::user()->country()->id;
        $info['lfcl_id'] = 1;
        $info['aemp_iusr'] = Auth::user()->employee()->id;
        $info['aemp_eusr'] = Auth::user()->employee()->id;

        try {
            TradeMarketing::on($this->db)->insert($info);
            return redirect()->back()->with('success', 'Trade Marketing Zone Added Successfully');
        }catch(\Exception $e)
        {
            return redirect()->back()->with('danger', 'Invalid Information');
        }
    }



    public function show($id){
        if ($this->userMenu->wsmu_vsbl) {

            $trade = TradeMarketing::on($this->db)->with('employee:id,aemp_name,aemp_usnm', 'zone:id,zone_name,zone_code')->findOrFail($id);

            return view('TradeMarketing.zones_view', [
                'trade' => $trade
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
