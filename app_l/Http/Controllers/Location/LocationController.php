<?php

namespace App\Http\Controllers\Location;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */

use App\BusinessObject\BlockHistory;
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    private $access_key = 'LocationController';
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

    public function maintainLocation()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Location.location_report.maintain_location')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }
    public function filterMaintainLocation(Request $request)
    {
      $data = DB::connection($this->db)->select("SELECT
  concat(t5.lsct_name,' < ',  t4.ldpt_name, ' < ',  t3.lcmp_name,' < ',t2.locm_name) AS location_master,
  t6.ltyp_name                                                                AS type_name,
  t1.locd_name,
  t1.locd_code,
  t1.geo_adrs                                                                 AS location,
  t7.aemp_name                                                                AS created_by,
  t8.aemp_name                                                                AS updated_by,
  t1.created_at,
  t1.updated_at
FROM tm_locd AS t1
  INNER JOIN tm_locm AS t2 ON t1.locm_id = t2.id
  INNER JOIN tm_lcmp AS t3 ON t1.lcmp_id = t3.id
  INNER JOIN tm_ldpt AS t4 ON t1.ldpt_id = t4.id
  INNER JOIN tm_lsct AS t5 ON t1.lsct_id = t5.id
  INNER JOIN tm_ltyp AS t6 ON t1.ltyp_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_iusr = t7.id
  INNER JOIN tm_aemp AS t8 ON t1.aemp_eusr = t8.id");
        return $data;

    }

}