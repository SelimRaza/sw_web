<?php

namespace App\Http\Controllers\Order;

use Excel;
use Image;
use App\User;
use Response;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Employee;
use App\MasterData\SiteInfo;
use Illuminate\Http\Request;
use App\BusinessObject\TeleUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeleRemoveController extends Controller
{
    private $access_key = 'tele/setup';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $employee;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->employee = Auth::user()->employee();
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object) ['wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0];
            }
            return $next($request);
        });
    }

    public function removeNonProductive()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Order.remove_non_productive');
        } else {
            return view('theme.access_limit');
        }
    }

    public function removeNonProductiveData(Request $request)
    {
        try {
            DB::connection($this->db)->beginTransaction();

            $site = SiteInfo::on($this->db)
                ->where('site_code', $request->np_outle_code)
                ->first();
            $employee = Employee::on($this->db)
                ->where('aemp_usnm', $request->np_emp_code)
                ->first();

            if (!$site || !$employee) {
                return 'Site or Employee not found';
            }

            $tltr_data = DB::connection($this->db)
                ->table('tt_tltr')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('tltr_ordr', 0)
                ->where('tltr_date', date('Y-m-d'))
                ->delete();

            $npro_data = DB::connection($this->db)
                ->table('tt_npro')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('npro_date', date('Y-m-d'))
                ->delete();

            $ssvh_data = DB::connection($this->db)
                ->table('th_ssvh')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('ssvh_date', date('Y-m-d'))
                ->delete();

            DB::connection($this->db)->commit();
            return [$site, $employee, $tltr_data, $npro_data, $ssvh_data];
            
        } catch (\Exception $e) {
            DB::connection($this->db)->rollBack();
            return 'Error: Something went wrong';
            // return $e->getMessage();
        }
    }

    public function removeOrder(Request $request)
    {
        try {
            DB::connection($this->db)->beginTransaction();
            $site = SiteInfo::on($this->db)->where('site_code', $request->ord_outle_code)->first();
            $employee = Employee::on($this->db)->where('aemp_usnm', $request->ord_emp_code)->first();

            if (!$site || !$employee) {
                return 'Site or Employee not found';
            }

            $ordmIds = DB::connection($this->db)
                ->table('tt_ordm')
                ->select('id')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('attr8', 10)
                ->where('lfcl_id', 1)
                ->where('ordm_date', date('Y-m-d'))
                ->pluck('id')
                ->toArray();

            if ($this->cont_id == 2 || $this->cont_id == 5) {
                $ordd_data = DB::connection($this->db)
                    ->table('tt_ordd')
                    ->whereIn('ordm_id', $ordmIds)
                    ->delete();

                $deletedOrdm = DB::connection($this->db)
                    ->table('tt_ordm')
                    ->whereIn('id', $ordmIds)
                    ->delete();
            } else {
                $updatedOrdm = DB::connection($this->db)
                    ->table('tt_ordm')
                    ->whereIn('id', $ordmIds)
                    ->update(['ordm_amnt' => 0.00001]);

                $updatedOrdd = DB::connection($this->db)
                    ->table('tt_ordd')
                    ->whereIn('ordm_id', $ordmIds)
                    ->update(['ordd_inty' => 0, 'ordd_qnty' => 0, 'ordd_cqty' => 0, 'ordd_dqty' => 0, 'ordd_rqty' => 0, 'ordd_inty' => 0, 'ordd_inty' => 0, 'ordd_oamt' => 0.000001, 'ordd_odat' => 0.0000001, 'ordd_amnt' => 0.00000001]);
            }

            $tltr_data = DB::connection($this->db)
                ->table('tt_tltr')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('tltr_ordr', 1)
                ->where('tltr_date', date('Y-m-d'))
                ->delete();

            $ssvh_data = DB::connection($this->db)
                ->table('th_ssvh')
                ->where('aemp_id', $employee->id)
                ->where('site_id', $site->id)
                ->where('ssvh_date', date('Y-m-d'))
                ->delete();

            DB::connection($this->db)->commit();
            return [$site, $employee];

        } catch (\Exception $e) {
            DB::connection($this->db)->rollBack();
            return 'Error: Something went wrong';
            // return $e->getMessage();
        }
    }
}
