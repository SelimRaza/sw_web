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
use App\DataExport\SVClassOrder;
use App\DataExport\UserInfoReportData;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Excel;

class MobileDataExportController extends Controller
{

    public function __construct()
    {

    }


    public function svClassData($cont_id, $emp_id, $start_date, $end_date)
    {
        return Excel::download(SVClassOrder::create($cont_id, $emp_id, $start_date, $end_date), 'data_' . date("Y-m-d H:i:s") . '.xlsx');
    }


}