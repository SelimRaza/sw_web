<?php

namespace App\Http\Controllers\MarketOpen;
use App\Http\Controllers\Controller;
use App\MasterData\WardSRMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\District;
use App\MasterData\Thana;
use App\MasterData\Ward;
use App\MasterData\Market;
use App\MasterData\ThanaSRMapping;
use App\MasterData\Employee; 
class MarketOpenController extends Controller
{    

    private $access_key = 'MarketOpenController';
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $markets=DB::connection($this->db)->table('tm_dsct as t1')
            ->join('tm_than as t2', 't2.dsct_id', '=', 't1.id')
            ->join('tm_ward as t3', 't3.than_id', '=', 't2.id')
            ->join('tm_mktm as t4', 't4.ward_id', '=', 't3.id')
            ->select('t4.id as id','t1.dsct_name  AS district', 't2.than_name as thana', 't3.ward_name as ward','t4.mktm_code as market_code','t4.mktm_name as market_name')->paginate(2000);
        return view('market_open.market_home')->with('markets',$markets);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $districts=District::all();
        $thanas=Thana::all();
        $wards=Ward::all();
        return view('market_open.market_create')
               ->with('districts',$districts)
               ->with('thanas',$thanas)
               ->with('wards',$wards);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $districts=District::all();
        $thanas=Thana::all();
        $wards=Ward::all();
        $markets=Market::all();
        return view('market_open.market_edit')
               ->with('districts',$districts)
               ->with('thanas',$thanas)
               ->with('wards',$wards)
               ->with('markets',$markets);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function jsonGetThanaList(Request $request){
        
        return $results=Thana::where('dsct_id',$request->district_id)->get(); 
        
    }

    public function jsonGetMarketWordList(Request $request){
         
        return $wards=Ward::where('than_id',$request->thana_id)->get();

    }

    public function jsonGetWardWiseMarketList(Request $request){

        return $depots=DB::connection($this->db)->table('tm_dsct as t1')
            ->join('tm_than as t2', 't2.dsct_id', '=', 't1.id')
            ->join('tm_ward as t3', 't3.than_id', '=', 't2.id')
            ->join('tm_mktm as t4', 't4.ward_id', '=', 't3.id')
            ->select('t4.id as id','t1.dsct_name  AS district','t1.dsct_code  AS district_code',
                't2.than_name as thana','t2.than_code  as thana_code', 't3.ward_name as ward','t3.ward_code as ward_code',
                't4.mktm_code as market_code','t4.mktm_name as market_name')
            ->where(['t4.ward_id' =>$request->ward_id])->get();

    }


    public function jsonSaveMarketDetails(Request $request){


       DB::connection($this->db)->beginTransaction();
       try {

            $market = new Market();
            $market->setConnection($this->db);
            $market->mktm_name = $request->market_name;
            $market->mktm_code = '/';
            $market->ward_id = $request->ward_id;
            $market->cont_id = $this->currentUser->country()->id;
            $market->lfcl_id = 1;
            $market->aemp_iusr = $this->currentUser->employee()->id;
            $market->aemp_eusr = $this->currentUser->employee()->id;
            $market->save();
            DB::connection($this->db)->commit();
            if($market->id) {

               return 1;

            }else{

               return 0;
            }
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }


    }

    public function jsonEditMarket(Request $request){

         $market=Market::findorfail($request->edit_id);
         $ward_id=$market->ward_id;
         $market_name=$market->mktm_name;
         $wards=Ward::where('id',$ward_id)->get();
         $array1=array();
         foreach ($wards as $ward){

             $array1[] = array('id' => $ward->id, 'name' =>$ward->ward_name);

         }

         $results=Ward::where('than_id',$request->thana_id)->get();
         foreach ($results as $result){

              if($result->id!=$ward_id){

                  $array1[] = array('id' => $result->id, 'name' =>$result->ward_name);
              }


         }

         return $array=array($market_name,$array1,$request->edit_id);

    }

    public function jsonEditMarketDetails(Request $request){

      DB::connection($this->db)->beginTransaction();
       try {

            $market = Market::findorfail($request->upgrade_id);
            $market->setConnection($this->db);
            $market->mktm_name = $request->edit_market_name;
            // $market->mktm_code = $request->market_name;
            $market->ward_id = $request->edit_ward_id;
            $market->cont_id = $this->currentUser->country()->id;
            $market->lfcl_id = 1;
            $market->aemp_iusr = $this->currentUser->employee()->id;
            $market->aemp_eusr = $this->currentUser->employee()->id;
            $market->save();

            DB::connection($this->db)->commit();
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }

        return $depots=DB::connection($this->db)->table('tm_dsct as t1')
            ->join('tm_than as t2', 't2.dsct_id', '=', 't1.id')
            ->join('tm_ward as t3', 't3.than_id', '=', 't2.id')
            ->join('tm_mktm as t4', 't4.ward_id', '=', 't3.id')
            ->select('t4.id as id','t1.dsct_name  AS district','t1.dsct_code  AS district_code',
                't2.than_name as thana','t2.than_code  as thana_code', 't3.ward_name as ward','t3.ward_code as ward_code',
                't4.mktm_code as market_code','t4.mktm_name as market_name')
            ->where(['t4.ward_id' =>$request->edit_ward_id])->get();

        

    }

    public function mapMarkeReportShow(Request $request){

        $districts=District::all();
        $thanas=Thana::all();
        $wards=DB::connection($this->db)->select("select * from tm_ward");
        $users = DB::connection($this->db)->select("SELECT distinct tm_msit.aemp_iusr,tm_aemp.aemp_name
            from tm_msit
            LEFT JOIN tm_aemp on tm_aemp.id=tm_msit.aemp_iusr");

        return view('market_open.market_map_view')
              ->with('districts',$districts)
              ->with('thanas',$thanas)
              ->with('users', $users)
              ->with('wards',$wards);

         
    }

    public function jsonLoadMarketGoogleMap(Request $request){

        if((!empty($request->thana_id)) && (!empty($request->thana_id))){

            return $results=DB::connection($this->db)->table('tm_msit as t1')
                ->join('tm_mktm as t2', 't2.id', '=', 't1.mktm_id')
                ->join('tm_ward as t3', 't3.id', '=', 't2.ward_id')
                ->join('tm_than as t4', 't4.id', '=', 't3.than_id')
                ->join('tm_dsct as t5', 't5.id', '=', 't4.dsct_id')
                ->select('t4.id as thana_id','t5.id as distirct_id','t1.geo_lat AS lat','t1.geo_lon AS lng','t1.site_name AS name','t1.site_adrs AS address','t1.site_code AS site_code')
                ->where(['t4.id'=>$request->thana_id,'t5.id'=>$request->district_id])
                ->get();


        }else if(!empty($request->user_id)){


            return $results=DB::connection($this->db)->table('tm_msit as t1')
                ->join('tm_mktm as t2', 't2.id', '=', 't1.mktm_id')
                ->join('tm_ward as t3', 't3.id', '=', 't2.ward_id')
                ->join('tm_than as t4', 't4.id', '=', 't3.than_id')
                ->join('tm_dsct as t5', 't5.id', '=', 't4.dsct_id')
                ->select('t4.id as thana_id','t5.id as distirct_id','t1.geo_lat AS lat','t1.geo_lon AS lng','t1.site_name AS name','t1.site_adrs AS address','t1.site_code AS site_code')
                ->where(['t1.aemp_iusr'=>$request->user_id])
                ->get();


        }



    }

    public function srWiseThanaList(Request $request,$staff_code){

        return $results=DB::connection($this->db)->table('tl_srth as t1')
            ->join('tm_than as t3', 't3.id', '=', 't1.than_id')
            ->join('tm_aemp as t2', 't2.id', '=', 't1.aemp_id') 
            ->select('t1.id AS id','t3.than_code AS thana_code','t3.than_name AS thana_name','t2.aemp_name AS user_name','t2.aemp_usnm AS user_code')
            ->where(['t2.aemp_usnm'=>$staff_code])->get();


    }

    public function srWiseWardList(Request $request,$staff_code){

        return $results=DB::connection($this->db)->table('tl_srwd as t1')
            ->join('tm_ward as t3', 't3.id', '=', 't1.ward_id')
            ->join('tm_aemp as t2', 't2.id', '=', 't1.aemp_id')
            ->select('t1.id AS id','t3.ward_code AS ward_code','t3.ward_name AS ward_name','t2.aemp_name AS user_name','t2.aemp_usnm AS user_code')
            ->where(['t2.aemp_usnm'=>$staff_code])->get();


    }

    public function deleteSrWiseThanaList(Request $request,$id){
            

        ThanaSRMapping::where('id', $id)->delete();
        return response()->json(['Success']);


    }

    public function deleteSrWiseWardList(Request $request,$id){


        WardSRMapping::where('id', $id)->delete();
        return response()->json(['Success']);


    }



}
