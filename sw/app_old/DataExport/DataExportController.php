<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\DataExport;


use App\BusinessObject\CompanyEmployee;

use App\DataExport\AttendanceData;
use App\DataExport\DashboardData;
use App\DataExport\EmployeeWiseData;
use App\DataExport\GroupWiseData;
use App\DataExport\NonProductiveSRListData;
use App\DataExport\OrderData;
use App\DataExport\Data;
use App\DataExport\OutletDetailsReportData;
use App\DataExport\OutletSummaryReportData;
use App\DataExport\UserInfoReportData;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Excel;

class DataExportController extends Controller
{
    private $access_key = 'DataExportController';
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


    public function dataExportGroupData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(GroupWiseData::create($request->date), 'Group_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportEmpData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(EmployeeWiseData::create($request->date), 'Employee_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportDashData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(DashboardData::create($request->company_id), 'dashBoard_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportAttendanceData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(AttendanceData::create($request->start_date, $request->end_date, $request->acmp_id), 'Attendance_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportOrderData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
           // return Excel::download(OrderData::create($request->start_date, $request->end_date, $request->acmp_id, $request->slgp_id), 'order_data_' . date("Y-m-d H:i:s") . '.xlsx');
            return Excel::download(OrderData::create($request->start_date, $request->end_date, $request->slgp_id), 'order_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportNonProductiveSRListData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(NonProductiveSRListData::create($request->start_date, $request->end_date, $request->acmp_id), 'NonProductiveSRList_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportOutletSummaryReportData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(OutletSummaryReportData::create($request->start_date, $request->end_date, $request->slgp_id, $request->zone_id), 'OutletSummaryReport_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportOutletDetailsReportData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(OutletDetailsReportData::create($request->start_date, $request->end_date, $request->slgp_id, $request->zone_id), 'OutletDetailsReport_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function dataExportUserInfoReportData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(UserInfoReportData::create($request->start_date, $request->end_date, $request->acmp_id), 'UserInfoReport_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function dataExport()
    {
        if ($this->userMenu->wsmu_crat) {

            $acmp_data = DB::connection($this->db)->select("SELECT
   id as acmp_id,
  acmp_name,
  acmp_code
FROM tm_acmp ");

            $slgp_data = DB::connection($this->db)->select("SELECT
   id as slgp_id,
  slgp_name,
  slgp_code
FROM tm_slgp ");

            $zone_data = DB::connection($this->db)->select("SELECT
   id as zone_id,
  zone_name,
  zone_code
FROM tm_zone ");

            return view('DataExport.date_export')->with('permission', $this->userMenu)
                ->with('acmp_data', $acmp_data)->with('slgp_data', $slgp_data)
                ->with('zone_data', $zone_data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


}