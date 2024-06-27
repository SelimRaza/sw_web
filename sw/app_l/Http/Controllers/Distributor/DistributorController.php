<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\MasterData\District;
use App\MasterData\Employee;
use App\MasterData\Thana;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DistributorController extends Controller
{

    private $access_key = 'MarketReportController';
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
        return view('Distributor.distributor');

    }
    public function authenticationCheck(Request $request){

        $password = $request->password;
        $user_id = $request->email;
        $dfsdf = DB::connection($this->db)
            ->select("SELECT `dlrm_pass` FROM `tm_dlrm` WHERE `dlrm_code`='3601620' AND dlrm_akey='Y'");
        $dsfs = $dfsdf[0];
        if ($dsfs->dlrm_pass==$password){
            Session::put('variableName', $user_id);
            return redirect('/dis_order_list');
        }else{
            return redirect()->back()->with('danger', 'User ID Or Password Wrong. Please try again!!!');
        }
        
        /*$pass = bcrypt($dsfs);
        dd($pass);*/
    }
    public function distributorOrderList(){

        $sr = DB::connection($this->db)->select("SELECT t2.id as sr_id, t2.aemp_usnm, t2.aemp_name,t3.dlrm_name, t3.dlrm_code FROM `tl_srdi` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id`=t2.id INNER JOIN tm_dlrm t3 ON t1.`dlrm_id`=t3.id WHERE t3.dlrm_code='3603022' and t2.lfcl_id='1'");

        return view('Distributor.d_order')->with('sr', $sr);
    }

    public function distributorOrderListFilter(Request $request){
        $from_date = $request->start_date;
        $srid = $request->_date;
        $dlr = '14053';

        if ($srid!=''){
            $sql = "SELECT t1.ordm_date, t4.slgp_name, t2.aemp_name, t2.aemp_usnm, t5.site_code, t5.site_name,
 t1.ordm_ornm, t8.amim_name, t8.amim_code, t7.ordd_uprc, t7.ordd_qnty, t7.ordd_oamt FROM `tt_ordm` t1 
 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id 
 INNER JOIN tm_dlrm t3 ON t1.dlrm_id = t3.id 
 INNER JOIN tm_slgp t4 ON t1.slgp_id = t4.id 
 INNER JOIN tm_site t5 ON t1.site_id = t5.id 
 INNER JOIN tt_ordd t7 ON t1.id = t7.ordm_id 
 INNER JOIN tm_amim t8 ON t7.amim_id = t8.id 
 WHERE t1.ordm_date='$from_date' AND t1.dlrm_id='$dlr' AND t1.aemp_id='$srid' ORDER BY t1.aemp_id, t1.site_id";
        }else{
            $sql = "SELECT t1.ordm_date, t4.slgp_name, t2.aemp_name, t2.aemp_usnm, t5.site_code, t5.site_name,
 t1.ordm_ornm, t8.amim_name, t8.amim_code, t7.ordd_uprc, t7.ordd_qnty, t7.ordd_oamt FROM `tt_ordm` t1 
 INNER JOIN tm_aemp t2 ON t1.`aemp_id` = t2.id 
 INNER JOIN tm_dlrm t3 ON t1.dlrm_id = t3.id 
 INNER JOIN tm_slgp t4 ON t1.slgp_id = t4.id 
 INNER JOIN tm_site t5 ON t1.site_id = t5.id 
 INNER JOIN tt_ordd t7 ON t1.id = t7.ordm_id 
 INNER JOIN tm_amim t8 ON t7.amim_id = t8.id 
 WHERE t1.ordm_date='$from_date' and t1.dlrm_id='$dlr' ORDER BY t1.aemp_id, t1.site_id";
        }

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($sql));

        return $srData;

    }

    public function distributorOrderItemSummary(){

        $sr = DB::connection($this->db)->select("SELECT t2.id as sr_id, t2.aemp_usnm, t2.aemp_name,t3.dlrm_name, t3.dlrm_code FROM `tl_srdi` t1 INNER JOIN tm_aemp t2 ON t1.`aemp_id`=t2.id INNER JOIN tm_dlrm t3 ON t1.`dlrm_id`=t3.id WHERE t3.dlrm_code='3603022' and t2.lfcl_id='1'");

        return view('Distributor.d_item_summary')->with('sr', $sr);
    }

    public function distributorOrderItemSummaryFilter(Request $request){
        $from_date = $request->from_date;
        $srid = $request->sr_id;
        $dlr = '14053';

        if ($srid!=''){
            $sql = "SELECT
  t1.ordm_date,
  t4.slgp_name,
  t8.amim_name,
  t8.amim_code,
  t7.ordd_uprc,
  sum(t7.ordd_qnty) as t_qnty,
  sum(t7.ordd_oamt) as t_amnt
FROM `tt_ordm` t1
  INNER JOIN tm_slgp t4 ON t1.slgp_id = t4.id
  INNER JOIN tt_ordd t7 ON t1.id = t7.ordm_id
  INNER JOIN tm_amim t8 ON t7.amim_id = t8.id
WHERE t1.ordm_date='$from_date' and t1.dlrm_id='$dlr' AND t1.aemp_id='$srid' GROUP BY t8.amim_code, t1.ordm_date";
        }else{
            $sql = "SELECT
  t1.ordm_date,
  t4.slgp_name,
  t8.amim_name,
  t8.amim_code,
  t7.ordd_uprc,
  sum(t7.ordd_qnty) as t_qnty,
  sum(t7.ordd_oamt) as t_amnt
FROM `tt_ordm` t1
  INNER JOIN tm_slgp t4 ON t1.slgp_id = t4.id
  INNER JOIN tt_ordd t7 ON t1.id = t7.ordm_id
  INNER JOIN tm_amim t8 ON t7.amim_id = t8.id
WHERE t1.ordm_date='$from_date' and t1.dlrm_id='$dlr' GROUP BY t8.amim_code, t1.ordm_date";
        }

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($sql));

        return $srData;

    }

    public function jsonGetEmployeeList(Request $request)
    {

        return $salesGroups = DB::connection($this->db)
            ->select("select  tm_aemp.id as  id,tm_aemp.aemp_name as  Name,tl_sgsm.aemp_code as code
            from  tl_sgsm
            join tm_aemp on  tm_aemp.id=tl_sgsm.aemp_id
            where tl_sgsm.slgp_id='$request->sales_group_id'");

    }

    public function jsonGetAllEmployeeList(Request $request)
    {

        if ($this->userMenu->wsmu_read) {

            $this->currentUser = Auth::user();
            $country_id = $this->currentUser->country()->id;
            $employies = Employee::where('cont_id', $country_id)->get();
            return response()->json($employies);

        } else {

            return redirect()->back()->with('danger', 'Access Limited');

        }

    }

    public function getMarketReport(Request $request)
    {

        // dd($request->all());
        if ($this->userMenu->wsmu_read) {

            if ($request->employee_id) {

                $results = DB::connection($this->db)->table('tm_mktm AS t1')
                    ->join('tm_msit AS t3', 't1.id', '=', 't3.mktm_id')
                    ->select(
                        't1.mktm_name as market_name',
                        't1.id as market_id',
                        DB::connection($this->db)->raw('count(t3.site_name) AS market_count')
                    )
                    ->where(['t1.aemp_iusr' => $request->employee_id])
                    ->groupBy('t1.mktm_name', 't1.id')
                    ->paginate(500);

                $salesGroups = DB::connection($this->db)->select("SELECT * FROM tm_slgp");
                $districts = District::all();
                return view('market_open.market_report_home')
                    ->with('salesGroups', $salesGroups)
                    ->with('districts', $districts)
                    ->with('results', $results);

            } else if ($request->thana_id && $request->district_id && $request->ward_id && $request->market_id) {

                $results = DB::connection($this->db)->table('tm_mktm')
                    ->join('tm_ward', 'tm_mktm.ward_id', '=', 'tm_ward.id')
                    ->join('tm_than', 'tm_ward.than_id', '=', 'tm_than.id')
                    ->join('tm_dsct', 'tm_than.dsct_id', '=', 'tm_dsct.id')
                    ->join('tm_msit', 'tm_msit.mktm_id', '=', 'tm_mktm.id')
                    ->select(
                        'tm_mktm.mktm_name as market_name',
                        'tm_mktm.id as market_id',
                        DB::connection($this->db)->raw('count(tm_msit.site_name) AS market_count')
                    )
                    ->where(['tm_dsct.id' => $request->district_id, 'tm_than.id' => $request->thana_id, 'tm_ward.id' => $request->ward_id, 'tm_mktm.id' => $request->market_id])
                    ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                    ->paginate(500);


                $salesGroups = DB::connection($this->db)->select("SELECT * FROM tm_slgp");
                $districts = District::all();
                return view('market_open.market_report_home')
                    ->with('salesGroups', $salesGroups)
                    ->with('districts', $districts)
                    ->with('results', $results);


            } else if ($request->thana_id && $request->district_id && $request->ward_id) {


                $results = DB::connection($this->db)->table('tm_mktm')
                    ->join('tm_ward', 'tm_mktm.ward_id', '=', 'tm_ward.id')
                    ->join('tm_than', 'tm_ward.than_id', '=', 'tm_than.id')
                    ->join('tm_dsct', 'tm_than.dsct_id', '=', 'tm_dsct.id')
                    ->join('tm_msit', 'tm_msit.mktm_id', '=', 'tm_mktm.id')
                    ->select(
                        'tm_mktm.mktm_name as market_name',
                        'tm_mktm.id as market_id',
                        DB::connection($this->db)->raw('count(tm_msit.site_name) AS market_count')
                    )
                    ->where(['tm_dsct.id' => $request->district_id, 'tm_than.id' => $request->thana_id, 'tm_ward.id' => $request->ward_id])
                    ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                    ->paginate(500);


                $salesGroups = DB::connection($this->db)->select("SELECT * FROM tm_slgp");
                $districts = District::all();
                return view('market_open.market_report_home')
                    ->with('salesGroups', $salesGroups)
                    ->with('districts', $districts)
                    ->with('results', $results);


            } else if ($request->thana_id && $request->district_id) {

                $results = DB::connection($this->db)->table('tm_mktm')
                    ->join('tm_ward', 'tm_mktm.ward_id', '=', 'tm_ward.id')
                    ->join('tm_than', 'tm_ward.than_id', '=', 'tm_than.id')
                    ->join('tm_dsct', 'tm_than.dsct_id', '=', 'tm_dsct.id')
                    ->join('tm_msit', 'tm_msit.mktm_id', '=', 'tm_mktm.id')
                    ->select(
                        'tm_mktm.mktm_name as market_name',
                        'tm_mktm.id as market_id',
                        DB::connection($this->db)->raw('count(tm_msit.site_name) AS market_count')
                    )
                    ->where(['tm_dsct.id' => $request->district_id, 'tm_than.id' => $request->thana_id])
                    ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                    ->paginate(500);


                $salesGroups = DB::connection($this->db)->select("SELECT * FROM tm_slgp");
                $districts = District::all();
                return view('market_open.market_report_home')
                    ->with('salesGroups', $salesGroups)
                    ->with('districts', $districts)
                    ->with('results', $results);


            } else {

                return redirect()->back()->with('danger', 'Access Limited');

            }
        }

    }

    public function wardMarketDetails(Request $request, $id)
    {

        if ($this->userMenu->wsmu_read) {

            $results = DB::connection($this->db)->table('tm_mktm AS t1')
                ->join('tm_ward AS t2', 't1.ward_id', '=', 't2.id')
                ->join('tm_than AS t3', 't2.than_id', '=', 't3.id')
                ->join('tm_dsct AS t4', 't3.dsct_id', '=', 't4.id')
                ->join('tm_aemp AS t5', 't1.aemp_iusr', '=', 't5.id')
                ->join('tm_msit AS t6', 't1.id', '=', 't6.mktm_id')
                ->select(
                    't1.mktm_name',
                    't2.ward_name',
                    't3.than_name',
                    't4.dsct_name',
                    't5.aemp_name',
                    't6.site_name'
                )
                ->where(['t1.id' => $id])
                ->paginate(1000);

            return view('market_open.market_report_details')
                ->with('results', $results);


        }


    }


}
