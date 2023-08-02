<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Base;
use App\MasterData\Channel;
use App\MasterData\District;
use App\MasterData\Division;
use App\MasterData\GovtDivision;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\OutletGrade;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Site;
use App\MasterData\SiteActvInactv;
use App\MasterData\SiteInfo;
use App\MasterData\SubCategory;
use App\MasterData\SubChannel;
use App\MasterData\Thana;
use App\MasterData\Ward;
use App\MasterData\Zone;
use App\MasterData\SiteCountryMapping;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Support\Facades\Input;
use Excel;
use Request as Test;
use Illuminate\Pagination\LengthAwarePaginator;

class SiteController extends Controller
{
    private $access_key = 'tm_site';
    private $currentUser;
    private $userMenu;
    private $db;
    private $module;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
            $this->module = Auth::user()->country()->module_type;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
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
                /*SELECT
                t1.id                                                                                            AS site_id,
                t1.site_code,
                t1.site_name,
                t1.site_adrs,
                t1.site_mob1,
                t1.site_mob2,
                t1.site_olnm,
                t1.site_olad,
                t1.site_olon,
                concat(t7.scnl_name, '<', t8.chnl_name)                                                          AS scnl_name,
                t9.otcg_name,
                concat(t2.mktm_name, '<', t3.ward_name, '<', t4.than_name, '<', t5.dsct_name, '<', t6.disn_name) AS mktm_name
                FROM tm_site AS t1
                INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
                INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
                INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
                INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
                INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
                INNER JOIN tm_scnl AS t7 ON t1.scnl_id = t7.id
                INNER JOIN tm_chnl AS t8 ON t7.chnl_id = t8.id
                INNER JOIN tm_otcg AS t9 ON t1.otcg_id = t9.id;*/
                $sites = DB::connection($this->db)->table('tm_site AS t1')
                    ->join('tm_mktm AS t2', 't1.mktm_id', '=', 't2.id')
                    ->join('tm_ward AS t3', 't2.ward_id', '=', 't3.id')
                    ->join('tm_than AS t4', 't3.than_id', '=', 't4.id')
                    ->join('tm_dsct AS t5', 't4.dsct_id', '=', 't5.id')
                    ->join('tm_disn AS t6', 't5.disn_id', '=', 't6.id')
                    ->join('tm_scnl AS t7', 't1.scnl_id', '=', 't7.id')
                    ->join('tm_chnl AS t8', 't7.chnl_id', '=', 't8.id')
                    ->join('tm_otcg AS t9', 't1.otcg_id', '=', 't9.id')
                    ->select(
                        't1.id AS site_id',
                        't1.site_code',
                        't1.site_name',
                        't1.site_adrs',
                        't1.site_mob1',
                        't1.site_mob2',
                        't1.site_olnm',
                        't1.site_olad',
                        't1.site_olon',
                        DB::connection($this->db)->raw('concat(t7.scnl_name, " < ", t8.chnl_name)                                                          AS scnl_name'),
                        't9.otcg_name',
                        't1.lfcl_id',
                        DB::connection($this->db)->raw('concat(t2.mktm_name, "<", t3.ward_name, "<", t4.than_name, "<", t5.dsct_name, "<", t6.disn_name) AS mktm_name')
                    )
                    ->where(function ($query) use ($q) {
                        $query->where('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_adrs', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_mob1', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_mob2', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_olnm', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_olad', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.site_olon', 'LIKE', '%' . $q . '%')
                            ->orWhere('t7.scnl_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t8.chnl_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.mktm_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.ward_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.than_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t5.dsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t6.disn_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);


            } else {

                $sites = DB::connection($this->db)->table('tm_site AS t1')
                    ->join('tm_mktm AS t2', 't1.mktm_id', '=', 't2.id')
                    ->join('tm_ward AS t3', 't2.ward_id', '=', 't3.id')
                    ->join('tm_than AS t4', 't3.than_id', '=', 't4.id')
                    ->join('tm_dsct AS t5', 't4.dsct_id', '=', 't5.id')
                    ->join('tm_disn AS t6', 't5.disn_id', '=', 't6.id')
                    ->join('tm_scnl AS t7', 't1.scnl_id', '=', 't7.id')
                    ->join('tm_chnl AS t8', 't7.chnl_id', '=', 't8.id')
                    ->join('tm_otcg AS t9', 't1.otcg_id', '=', 't9.id')
                    ->select(
                        't1.id AS site_id',
                        't1.site_code',
                        't1.site_name',
                        't1.site_adrs',
                        't1.site_mob1',
                        't1.site_mob2',
                        't1.site_olnm',
                        't1.site_olad',
                        't1.site_olon',
                        DB::connection($this->db)->raw('concat(t7.scnl_name, " < ", t8.chnl_name) AS scnl_name'),
                        't9.otcg_name',
                        't1.lfcl_id',
                        DB::connection($this->db)->raw('concat(t2.mktm_name, "<", t3.ward_name, "<", t4.than_name, "<", t5.dsct_name, "<", t6.disn_name) AS mktm_name')
                    )->paginate(100);
              //  $sites = Site::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(500);
            }
            return view('master_data.site.index')->with('sites', $sites)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

    public function getOutletMaster()
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            $otcg=DB::connection($this->db)->select("Select id,otcg_code,otcg_name FROM tm_otcg ORDER BY otcg_code ASC");
            $chnl=DB::connection($this->db)->select("Select id,chnl_code,chnl_name FROM tm_chnl ORDER BY chnl_code ASC");
            $dist=DB::connection($this->db)->select("Select id,dsct_code,dsct_name FROM tm_dsct ORDER BY dsct_code ASC");
            return view('master_data.site.outlet.index',['otcg'=>$otcg,'chnl'=>$chnl,'dist'=>$dist,'search_text'=>$q,'permission'=>$this->userMenu]);
        } else {
            return view('theme.access_limit');
        }
    }

    public function getOutletMasterData(Request $request){
        $dsct_id=$request->dsct_id;
        $than_id=$request->than_id;
        $otcg_id=$request->otcg_id;
        $chnl_id=$request->chnl_id;
        $scnl_id=$request->scnl_id;
        $lfcl_id=$request->lfcl_id;
        $site_vrfy=$request->site_vrfy;
        $site_vtrn=$request->site_vtrn;
        $site_code=$request->site_code;
        $query="";
       // return $request->all();
        if($dsct_id !=""){
            $query.=" AND t5.id=$dsct_id";
        }
        if($than_id !=""){
            $query.=" AND t4.id=$than_id";
        }
        if($otcg_id !=""){
            $query.=" AND t9.id=$otcg_id ";
        }
        if($chnl_id !=""){
            $query.=" AND t8.id=$chnl_id";
        }
        if($scnl_id !=""){
            $query.=" AND t7.id=$scnl_id";
        }
        if($lfcl_id !=""){
            $query.=" AND t1.lfcl_id=$lfcl_id";
        }
        if($site_vrfy !=""){
            $query.=" AND t1.site_vrfy=$site_vrfy";
        }
        if($site_vtrn !=""){
            $query.=" AND t1.site_vtrn='$site_vtrn'";
        }if($site_code !=""){
            $query.=" AND t1.site_code=$site_code";
        }
        
        $data=DB::connection($this->db)->select("SELECT
                t1.id                                                                  AS site_id,
                t1.site_code,
                t1.site_name,
                t1.site_adrs,
                t1.site_mob1,
                t1.site_mob2,
                t1.site_olnm,
                t1.site_olad,
                t1.site_olon,
                concat(t7.scnl_name, '<', t8.chnl_name)                               AS scnl_name,
                t9.otcg_name,
                concat(t2.mktm_name, '<', t3.ward_name, '<', t4.than_name, '<', t5.dsct_name, '<', t6.disn_name) 
                                                                                        AS mktm_name,
                t10.lfcl_name,
                t11.licn_no,
                t11.expr_date,
                t1.site_vtrn
                FROM tm_site AS t1
                INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
                INNER JOIN tm_ward AS t3 ON t2.ward_id = t3.id
                INNER JOIN tm_than AS t4 ON t3.than_id = t4.id
                INNER JOIN tm_dsct AS t5 ON t4.dsct_id = t5.id
                INNER JOIN tm_disn AS t6 ON t5.disn_id = t6.id
                INNER JOIN tm_scnl AS t7 ON t1.scnl_id = t7.id
                INNER JOIN tm_chnl AS t8 ON t7.chnl_id = t8.id
                INNER JOIN tm_otcg AS t9 ON t1.otcg_id = t9.id
                INNER JOIN tm_lfcl AS t10 ON t1.lfcl_id=t10.id
                LEFT JOIN  tl_scmp AS t11 ON t1.id=t11.site_id
                WHERE t1.cont_id='$this->cont_id' ". $query."
                ORDER BY t1.site_code ASC limit 10000;
                ");
        return array(
            'sites'=>$data,
            'permission'=>$this->userMenu
            );
    }

    public function lfclChange($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $site = Site::on($this->db)->findorfail($id);
            $site->lfcl_id = $site->lfcl_id == 1 ? 2 : 1;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->save();
            return redirect()->back();
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    // public function arrayPaginator($array, $request){   
    //     $page = Test::get('page', 1);
    //     $perPage = 100;
    //     $offset = ($page * $perPage) - $perPage;
    //     return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
    //         ['path' => $request->url(), 'query' => $request->query()]);
    // }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $govMarket = Market::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $subChannels = SubChannel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $outletCategorys = OutletCategory::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $country=DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 order by cont_name ASC");
            return view('master_data.site.create')->with('subChannels', $subChannels)->with('outletCategorys', $outletCategorys)
                   ->with("govMarket", $govMarket)->with('permission', $this->userMenu)->with('country',$country);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            if(strlen($request->outlet_code)>8 ||strlen($request->site_code)>8){
                return redirect()->back()->with('danger', 'Site Code || Outlet Code Can not more than 8 Digit');
            }
            $outlet = new Outlet();
            $outlet->setConnection($this->db);
            $outlet->oult_name = $request->site_name;
            $outlet->oult_code = $request->outlet_code?$request->outlet_code:$request->site_code;
            $outlet->oult_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $outlet->oult_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $outlet->oult_olad = isset($request->site_olad) ? $request->site_olad : '';
            $outlet->oult_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $outlet->oult_olon = isset($request->site_olon) ? $request->site_olon : '';
            $outlet->oult_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $outlet->oult_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $outlet->oult_emal = isset($request->site_emal) ? $request->site_emal : '';
            $outlet->cont_id = $this->currentUser->country()->id;
            $outlet->lfcl_id = 1;
            $outlet->aemp_iusr = $this->currentUser->employee()->id;
            $outlet->aemp_eusr = $this->currentUser->employee()->id;
            $outlet->save();
            $site = new Site();
            $site->setConnection($this->db);
            $site->site_name = $request->site_name;
            $site->site_code = $request->site_code;
            $site->outl_id = $outlet->id;
            $site->site_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $site->site_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $site->site_olad = isset($request->site_olad) ? $request->site_olad : '';
            $site->mktm_id = $request->mktm_id;
            $site->site_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $site->site_olon = isset($request->site_olon) ? $request->site_olon : '';
            $site->site_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $site->site_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $site->site_emal = isset($request->site_emal) ? $request->site_emal : '';
            $site->scnl_id = $request->scnl_id;
            $site->otcg_id = $request->otcg_id;
            $site->site_imge = '';
            $site->site_omge = '';
            $site->geo_lat = 0;
            $site->geo_lon = 0;
            $site->site_reg = isset($request->site_reg) ? $request->site_reg : '';
            $site->site_vrfy = 0;
            $site->site_hsno = isset($request->site_hsno) ? $request->site_hsno : '';
            $site->site_vtrn = isset($request->site_vtrn) ? $request->site_vtrn : '';
            $site->site_vsts = $request->site_vsts == 'on' ? 1 : 0;;
            $site->cont_id = $this->currentUser->country()->id;
            $site->lfcl_id = 1;
            $site->aemp_iusr = $this->currentUser->employee()->id;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->save();

            $cont_id=$request->ow_cont_id;
            $licn=$request->licn_no;
            $expr_date=$request->expr_date;
            if($cont_id !='' || $licn !=''){
                $scmp=new SiteCountryMapping();
                $scmp->setConnection($this->db);
                $scmp->site_id=$site->id;
                $scmp->cont_id=$cont_id;
                $scmp->licn_no=$licn;
                $scmp->expr_date=$expr_date;
                $scmp->aemp_iusr=$this->aemp_id;
                $scmp->aemp_eusr=$this->aemp_id;
                $scmp->save();
            }
               
            
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
            // return redirect()->back()->withInput()->with('danger', $e);
        }
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $site = Site::on($this->db)->findorfail($id);
            $scmp=SiteCountryMapping::on($this->db)->where('site_id','=',$id)->first();
            $cnt_name=DB::connection($this->db)->select("Select t2.cont_name FROM tl_scmp t1 INNER JOIN myprg_comm.tl_cont t2 ON t1.cont_id=t2.id WHERE t1.site_id=$id ");
            $cnt_name=$cnt_name?$cnt_name[0]->cont_name:'N/A';
            return view('master_data.site.show')->with('site', $site)->with('scmp',$scmp)->with('cnt_name',$cnt_name);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $site = Site::on($this->db)->findorfail($id);
            $outlet=Outlet::on($this->db)->where(['id'=>$site->outl_id])->first();
            $govMarket = Market::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $subChannels = SubChannel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $outletCategorys = OutletCategory::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $country=DB::select("SELECT id,cont_code,cont_name FROM tl_cont WHERE lfcl_id=1 order by cont_name ASC");
            $scmp=DB::connection($this->db)->select("SELECT cont_id,licn_no,expr_date FROM tl_scmp WHERE site_id=$id limit 1");
            //$scmp=(object)$scmp;
            //return $outlet;
            return view('master_data.site.edit')->with('subChannels', $subChannels)->with('site', $site)->with('outletCategorys', $outletCategorys)->with('govMarket', $govMarket)
                    ->with('country',$country)->with('scmp',$scmp)->with('outlet',$outlet);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function getOutletId($outlet_code){
        $outlet=Outlet::on($this->db)->where(['oult_code'=>$outlet_code])->first();
        return $outlet->id;
    }

    public function update(Request $request, $id)
    {
        $outlet_id=$this->getOutletId($request->outlet_code);
        if($outlet_id ==null){
            return redirect()->back()->with('danger', 'Outlet not found');
        }
        try{
            $site = Site::on($this->db)->findorfail($id);
            $site->site_name = $request->site_name;
            // $site->site_code =$request->outlet_code?$request->outlet_code: $request->site_code;
            $site->site_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $site->site_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $site->site_olad = isset($request->site_olad) ? $request->site_olad : '';
            $site->mktm_id = $request->mktm_id;
            $site->site_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $site->site_olon = isset($request->site_olon) ? $request->site_olon : '';
            $site->site_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $site->site_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $site->site_emal = isset($request->site_emal) ? $request->site_emal : '';
            $site->scnl_id = $request->scnl_id;
            $site->otcg_id = $request->otcg_id;
            $site->site_reg = isset($request->site_reg) ? $request->site_reg : '';
            $site->site_hsno = isset($request->site_hsno) ? $request->site_hsno : '';
            $site->site_vtrn = isset($request->site_vtrn) ? $request->site_vtrn : '';
            $site->site_vsts = $request->site_vsts == 'on' ? 1 : 0;;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->save();
            $outlet = Outlet::on($this->db)->findorfail($site->outl_id);
            $outlet->oult_name = $request->site_name;
            // $outlet->oult_code = $request->site_code;
            $outlet->oult_olnm = isset($request->site_olnm) ? $request->site_olnm : '';
            $outlet->oult_adrs = isset($request->site_adrs) ? $request->site_adrs : '';
            $outlet->oult_olad = isset($request->site_olad) ? $request->site_olad : '';
            $outlet->oult_ownm = isset($request->site_ownm) ? $request->site_ownm : '';
            $outlet->oult_olon = isset($request->site_olon) ? $request->site_olon : '';
            $outlet->oult_mob1 = isset($request->site_mob1) ? $request->site_mob1 : '';
            $outlet->oult_mob2 = isset($request->site_mob2) ? $request->site_mob2 : '';
            $outlet->oult_emal = isset($request->site_emal) ? $request->site_emal : '';
            $outlet->aemp_eusr = $this->currentUser->employee()->id;
            $outlet->save();
            $cont_id=$request->ow_cont_id;
            $licn=$request->licn_no;
            $expr_date=$request->expr_date;
            if($cont_id !='' || $licn !=''){
                $scmp=SiteCountryMapping::on($this->db)->where('site_id','=',$id)->first();
                //  return $scmp;
                if($scmp !=null){              
                    $scmp->cont_id=$cont_id;
                    $scmp->licn_no=$licn;
                    $scmp->expr_date=$expr_date;
                    $scmp->aemp_eusr=$this->aemp_id;
                    $scmp->save();
                }else{
                    $scmp=new SiteCountryMapping();
                    $scmp->setConnection($this->db);
                    $scmp->site_id=$site->id;
                    $scmp->cont_id=$cont_id;
                    $scmp->licn_no=$licn;
                    $scmp->expr_date=$expr_date;
                    $scmp->aemp_iusr=$this->aemp_id;
                    $scmp->aemp_eusr=$this->aemp_id;
                    $scmp->save();
                }
                
            }
            return redirect()->back()->with('success', 'successfully Updated');
        }
        catch(\Exception $e){
            return redirect()->back()->with('danger',$e->getMessage());
        }
        
        
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $site = Site::on($this->db)->findorfail($id);
            $site->lfcl_id = $site->lfcl_id == 1 ? 2 : 1;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->save();
            return redirect('/site');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Site(), 'outlet_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.site.site_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Site(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function filterDistrict(Request $request)
    {
        $data = District::on($this->db)->where('disn_id', '=', $request->fk_id)
            ->get(['id AS id', 'dsct_name AS name']);
        return Response::json($data);

    }

    public function filterThana(Request $request)
    {
        $data = Thana::on($this->db)->where('dsct_id', '=', $request->fk_id)
            ->get(['id AS id', 'than_name AS name']);
        return Response::json($data);

    }

    public function filterWard(Request $request)
    {
        $data = Ward::on($this->db)->where('than_id', '=', $request->fk_id)
            ->get(['id AS id', 'ward_name AS name']);
        return Response::json($data);

    }

    public function filterMarket(Request $request)
    {
        $data = Market::on($this->db)->where('ward_id', '=', $request->fk_id)
            ->get(['id AS id', 'mktm_name AS name']);
        return Response::json($data);

    }

    public function filterSubChannel(Request $request)
    {
        $data = SubChannel::on($this->db)->where('chnl_id', '=', $request->fk_id)
            ->get(['id AS id', 'scnl_name AS name']);
        return Response::json($data);

    }

    public function siteActiveInactive(Request $request){
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file1')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SiteActvInactv(), $request->file('import_file1'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Updated');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function unverifiedSite(){
        $sites=DB::connection($this->db)->select("SELECT 
                t1.id,t1.site_name,t1.site_code,t1.site_olnm,t1.site_adrs,t1.site_olad,t1.site_olon,t1.site_ownm,t1.site_mob1,t1.site_mob2,
                t2.mktm_name,t4.otcg_name,t3.scnl_name,t1.site_imge
                FROM `tm_site` t1
                INNER JOIN tm_mktm t2 On t1.mktm_id=t2.id
                INNER JOIN tm_scnl t3 ON t1.scnl_id=t3.id
                INNER JOIN tm_otcg t4 ON t1.otcg_id=t4.id
                WHERE t1.site_vrfy=0 AND t1.lfcl_id=1 ORDER BY t1.updated_at DESC");
        $erpzn=array();
        if($this->cont_id==14 || $this->cont_id==3){
            $erpzn=DB::connection($this->db)->select("SELECT `id`, `epzn_name`, `epzn_code`  FROM `tbl_erp_zone` WHERE `lfcl_id`= 1 ORDER BY epzn_name;");
        }
        return view('master_data.site.unverified',['erpzn'=>$erpzn])->with('sites',$sites);
    }

    public function unverifiedSiteInactive(Request $request){
        $unv_sites=$request->sites;
        $site_code=$request->site_code;
        $flag=$request->flag;
        $prom=$request->prom;
        $erp_zone=$request->erp_zone;
        if($erp_zone==''){
           $erp_zone=0; 
        }
        if($flag==1){
            DB::connection($this->db)->select("Update tm_site SET lfcl_id=2 WHERE id IN (".implode(',',$unv_sites).")");
        }else{
            $slgp_list=DB::connection($this->db)->select("Select id,plmt_id,acmp_id FROM tm_slgp Where lfcl_id=1 Order by id asc");
           // DB::connection($this->db)->beginTransaction();
            try{
                for($i=0;$i<count($unv_sites);$i++){
                    $code=$site_code[$i];
                    $id=$unv_sites[$i];
                    $site=Site::on($this->db)->find($id);
                    $site->site_vrfy=1;
                    $site->erp_zone=$erp_zone;
                    if($site->site_code !=$code){
                        $site->site_code=$code;
                    }
                    $site->save();
                    //DB::connection($this->db)->select("Update IGNORE  tm_site SET site_vrfy=1,site_code='$code' AND attr4={$erp_zone} WHERE id=$id");
                    foreach($slgp_list as $slgp){
                        $slgp_id=$slgp->id;
                        $plmt_id=$slgp->plmt_id;
                        $acmp_id=$slgp->acmp_id;
                        DB::connection($this->db)->select("INSERT IGNORE INTO `tl_stcm`( `site_id`, `site_code`, `acmp_id`, `slgp_id`, `plmt_id`, `plmt_code`, `optp_id`, `stcm_isfx`, 
                        `stcm_limt`, `stcm_days`, `stcm_ordm`, `stcm_duea`, `stcm_odue`, `stcm_pnda`, `stcm_cpnd`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`)
                         VALUES ('$id','$code','$acmp_id','$slgp_id','$plmt_id','','1','1','0','0','0',
                         '0','0','0','0','$this->cont_id','1','$this->aemp_id','$this->aemp_id')");
                    }
                    if($prom==1){
                        $all_prom=DB::connection($this->db)->select("SELECT id FROM `tm_prmr` WHERE lfcl_id=1 AND prms_edat>=curdate()");
                        for($j=0;$j<count($all_prom);$j++){
                            $sql5[] = ['prmr_id' => $all_prom[$j]->id, 'site_id' => $unv_sites[$i], 'cont_id' => $this->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->aemp_id, 'aemp_eusr' => $this->aemp_id];
                        }
                        if (!empty($sql5)) {
                            DB::connection($this->db)->table('tl_prsm')->insertOrIgnore($sql5);
                        }
                    }
                    DB::connection($this->db)->select("CALL tempOrderProcess(?)",[$id]);
                }
                
            }
            catch(\Exception $e){
               // DB::connection($this->db)->rollback();
                return $e->getMessage();
            }
            
            
           // DB::connection($this->db)->select("Update tm_site SET site_vrfy=1,site_code=id WHERE id IN (".implode(',',$unv_sites).")");
        }
        
        $sites=DB::connection($this->db)->select("SELECT 
        t1.id,t1.site_name,t1.site_code,t1.site_olnm,t1.site_adrs,t1.site_olad,t1.site_olon,t1.site_ownm,t1.site_mob1,t1.site_mob2,
        t2.mktm_name,t4.otcg_name,t3.scnl_name
        FROM `tm_site` t1
        INNER JOIN tm_mktm t2 On t1.mktm_id=t2.id
        INNER JOIN tm_scnl t3 ON t1.scnl_id=t3.id
        INNER JOIN tm_otcg t4 ON t1.otcg_id=t4.id
        WHERE t1.site_vrfy=0 AND t1.lfcl_id=1 ORDER BY t1.updated_at DESC");
        
        return $sites;
        
    }

    public function bulkEdit(){
        if ($this->userMenu->wsmu_updt) {
            return view('master_data.site.bulk_edit')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function editFormat()
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SiteInfo(), 'outlet_edit_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function bulkInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SiteInfo(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
