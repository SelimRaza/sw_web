<?php

namespace App\Http\Controllers\DataExport;

use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\PriceList;
use App\MasterData\Company;
use App\MasterData\Employee;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class NewReportController extends Controller
{
    private $access_key = 'NewReportController';
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
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function start_index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $companies = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $employee = Employee::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $otcg_list = DB::connection($this->db)->select("Select id,otcg_name FROM tm_otcg WHERE lfcl_id=1 ORDER BY otcg_name ASC");
            $channel_list = DB::connection($this->db)->select("Select id,chnl_name,chnl_code FROM tm_chnl WHERE lfcl_id=1 ORDER BY chnl_name");
            return view('DataExport.index', ['otcg_list' => $otcg_list, 'channel_list' => $channel_list])->with('permission', $this->userMenu)->with('companies', $companies)->with('employeies', $employee)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }
    public function showData(Request $request)
    {
        $aemp_id = $request->input('aemp_id');
        $year = $request->input('year');
        $currentYear = date('Y');
        $currentMonth = ($year == $currentYear) ? date('m') : 12;

        // Prepare select columns
        $selectColumns = [
            't2.aemp_usnm as sr_id',
            't2.aemp_name as sr_name',
            't3.site_code as outlet_code',
            't3.site_name as outlet_name',
            't4.amim_code as item_code',
            't4.amim_name as item_name'
        ];

        // Add dynamic month columns
        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthYear = sprintf('%s-%02d', $year, $month);
            $selectColumns[] = DB::raw("SUM(CASE WHEN DATE_FORMAT(t1.ordm_date, '%Y-%m') = '$monthYear' THEN t1.ordd_inty ELSE 0 END) AS `Qty_$monthYear`");
            $selectColumns[] = DB::raw("SUM(CASE WHEN DATE_FORMAT(t1.ordm_date, '%Y-%m') = '$monthYear' THEN t1.ordd_oamt ELSE 0 END) AS `Amt_$monthYear`");
        }

        // Execute the query
        $results = DB::connection($this->db)->table('tl_summary_item_site as t1')
            ->join('tm_aemp as t2', 't1.aemp_id', '=', 't2.id')
            ->join('tm_site as t3', 't1.site_id', '=', 't3.id')
            ->join('tm_amim as t4', 't1.amim_id', '=', 't4.id')
            ->select($selectColumns)
            ->where('t1.aemp_id', $aemp_id)
            ->whereYear('t1.ordm_date', $year)
            ->groupBy('t2.aemp_usnm', 't2.aemp_name', 't3.site_code', 't3.site_name', 't4.amim_code', 't4.amim_name')
            ->orderBy('t1.ordm_date', 'asc')
            ->get();

        // Log the raw results for debugging
        \Log::info('Raw Results: ', $results->toArray());

        // Generate headers for the response
        $headers = [
            'SR ID',
            'SR Name',
            'Outlet Code',
            'Outlet Name',
            'Item Code',
            'Item Name'
        ];

        // Add dynamic month headers
        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthYear = sprintf('%s-%02d', $year, $month);
            $headers[] = "Qty $monthYear";
            $headers[] = "Amt $monthYear";
        }

        // Process results to replace null with 0
        $processedResults = $results->map(function($item) {
            $item = (array) $item; // Convert object to array
            foreach ($item as $key => $value) {
                if ($value === null) {
                    $item[$key] = 0; // Replace null with 0
                }
            }
            return $item;
        });

        // Log the processed results for debugging
        \Log::info('Processed Results: ', $processedResults->toArray());

        // Return JSON response with headers and data
        return response()->json([
            'headers' => $headers,
            'data' => $processedResults
        ]);
    }
    public function usersOrderReportFilter(Request $request)
    {
        $country_id = $this->currentUser->cont_id;
        $where = "1 and t1.cont_id=$country_id";

        if (isset($request->site_code)) {
            $where .= " AND t2.site_code = '$request->site_code'";
        }
        if (isset($request->acmp_id)) {
            $where .= " AND t1.acmp_id= '$request->acmp_id'";
        }
        if (isset($request->slgp_id)) {
            $where .= " AND t1.slgp_id= '$request->slgp_id'";
        }
        if (isset($request->otcg_id)) {
            $where .= " AND t2.otcg_id= '$request->otcg_id'";
        }
        if (isset($request->scnl_id)) {
            $where .= " AND t2.scnl_id= '$request->scnl_id'";
        }
        if (isset($request->chnl_id)) {
            $where .= " AND t6.chnl_id= '$request->chnl_id'";
        }
        if (isset($request->optp_id)) {
            $where .= " AND t1.optp_id= '$request->optp_id'";
        }
        $data = DB::connection($this->db)->select("SELECT
                t1.site_id,
                t3.acmp_name,
                t4.plmt_name,
                t2.site_code,
                t2.site_name,
                t5.optp_name                      ,
                t1.stcm_limt,
                t1.stcm_duea,
                t1.stcm_ordm,
                t6.scnl_name,
                t7.slgp_name,
                round(t1.stcm_pnda - t1.stcm_cpnd) AS pdc_amount,
                round(t1.stcm_cpnd)                AS non_verified,
                t1.stcm_days,
                t1.stcm_isfx,
                t1.stcm_odue                       
                FROM tl_stcm AS t1
                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                INNER JOIN tm_acmp AS t3 ON t1.acmp_id = t3.id
                INNER JOIN tm_plmt AS t4 ON t1.plmt_id = t4.id
                INNER JOIN tm_optp AS t5 ON t1.optp_id = t5.id
                INNER JOIN tm_scnl AS t6 ON t2.scnl_id=t6.id
                INNER JOIN tm_slgp AS t7 ON t1.slgp_id=t7.id
                where $where limit 25000");

        return $data;

    }

}