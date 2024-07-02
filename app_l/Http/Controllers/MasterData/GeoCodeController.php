<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\GeoCode;
use App\Mail\Test;
use App\MasterData\Auto;
use App\MasterData\Employee;
use App\MasterData\Role;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class GeoCodeController extends Controller
{
    private $access_key = 'tbld_geo_code';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $geoCode = GeoCode::where(function ($query) use ($q) {
                    $query->where('division_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('district_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('police_station', 'LIKE', '%' . $q . '%')
                        ->orWhere('sub_office', 'LIKE', '%' . $q . '%')
                        ->orWhere('zip_code', 'LIKE', '%' . $q . '%');
                })->where('country_id', '=', $this->currentUser->employee()->cont_id)->paginate(100);
            } else {
                $geoCode = GeoCode::where('country_id', '=', $this->currentUser->employee()->cont_id)->paginate(100);
            }
            return view('master_data.geo_code.index')->with("geoCodes", $geoCode)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


}
