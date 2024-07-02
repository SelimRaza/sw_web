<?php

namespace App\Http\Controllers\Campaign;
use App\Http\Controllers\Controller;
use App\MasterData\District;
use App\MasterData\Employee;
use App\MasterData\Thana;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
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

    public function index(){

       $acmp = DB::connection($this->db)->select("select * from tm_acmp");
       $region = DB::connection($this->db)->select("select * from tm_dirg ORDER BY dirg_code ASC");
       $zone = DB::connection($this->db)->select("select * from tm_zone ORDER BY zone_code ASC");
       $category = DB::connection($this->db)->select("select * from tm_otcg ORDER BY id ASC");
       $districts=District::all();
       //$wards=DB::connection($this->db)->select("select * from tm_ward");
       //$markets=Market::all();
        $results=[];
       return view('Campaign.campaign')
              ->with('acmp',$acmp)
              ->with('region',$region)
              ->with('zoneList',$zone)
              ->with('category',$category);

   }

   public function jsonGetEmployeeList(Request $request){

       return $salesGroups = DB::connection($this->db)
           ->select("select  tm_aemp.id as  id,tm_aemp.aemp_name as  Name,tl_sgsm.aemp_code as code
            from  tl_sgsm
            join tm_aemp on  tm_aemp.id=tl_sgsm.aemp_id
            where tl_sgsm.slgp_id='$request->sales_group_id'");

   }
   public function jsonGetAllEmployeeList(Request $request){

       if ($this->userMenu->wsmu_read) {

           $this->currentUser = Auth::user();
           $country_id=$this->currentUser->country()->id;
           $employies = Employee::where('cont_id',$country_id)->get();
           return response()->json($employies);

       }else {

          return redirect()->back()->with('danger', 'Access Limited');

       }

   }

   public function getMarketReport(Request $request){

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

               $salesGroups = DB::connection($this->db)->select("select * from tm_slgp");
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
                   ->where(['tm_dsct.id' => $request->district_id, 'tm_than.id' => $request->thana_id,'tm_ward.id'=>$request->ward_id,'tm_mktm.id'=>$request->market_id])
                   ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                   ->paginate(500);


               $salesGroups = DB::connection($this->db)->select("select * from tm_slgp");
               $districts = District::all();
               return view('market_open.market_report_home')
                   ->with('salesGroups', $salesGroups)
                   ->with('districts', $districts)
                   ->with('results', $results);





           }else if ($request->thana_id && $request->district_id && $request->ward_id){


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
                   ->where(['tm_dsct.id' => $request->district_id, 'tm_than.id' => $request->thana_id,'tm_ward.id'=>$request->ward_id])
                   ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                   ->paginate(500);


               $salesGroups = DB::connection($this->db)->select("select * from tm_slgp");
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
                   ->where(['tm_dsct.id' =>$request->district_id,'tm_than.id' =>$request->thana_id])
                   ->groupBy('tm_mktm.mktm_name', 'tm_mktm.id')
                   ->paginate(500);


               $salesGroups = DB::connection($this->db)->select("select * from tm_slgp");
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

   public  function wardMarketDetails(Request $request, $id){

       if ($this->userMenu->wsmu_read) {

           $results = DB::connection($this->db)->table('tm_mktm AS t1')
                   ->join('tm_ward AS t2', 't1.ward_id', '=', 't2.id')
                   ->join('tm_than AS t3', 't2.than_id', '=', 't3.id')
                   ->join('tm_dsct AS t4', 't3.dsct_id', '=', 't4.id')
                   ->join('tm_aemp AS t5', 't1.aemp_iusr', '=', 't5.id')
                   ->join('tm_msit AS t6','t1.id','=','t6.mktm_id')
                   ->select(
                       't1.mktm_name',
                       't2.ward_name',
                       't3.than_name',
                       't4.dsct_name',
                       't5.aemp_name',
                       't6.site_name'
                   )
                   ->where(['t1.id'=>$id])
                   ->paginate(1000);

           return view('market_open.market_report_details')
               ->with('results',$results);


       }


   }


}
