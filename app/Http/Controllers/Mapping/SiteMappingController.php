<?php

namespace App\Http\Controllers\Mapping;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\MasterData\RoutePlan;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use App\MasterData\Site;
use App\BusinessObject\PriceList;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\SalesGroupSku;
use App\Mapping\PLDT;
use App\Mapping\SGIT;
use App\Mapping\SitePartyMapping;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
Use App\MasterData\SKU;
Use App\MasterData\RouteSite;
Use App\MasterData\RSMP;
Use App\MasterData\Route;
Use App\MasterData\SubCategory;
use Excel;
class SiteMappingController extends Controller
{
    private $access_key = 'site-mapping';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        set_time_limit(8000000);
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    //SKU Master Load Function
    public function index(){
        // $rout_list=Route::on($this->db)->where(['lfcl_id'=>1])->orderBy('rout_code','asc')->get();
        
        $field = false;
        if($this->cont_id  == 2 || $this->cont_id == 5){
            $field = true;
            $rout_list=[];
        }else{
            $rout_list=Route::on($this->db)->where(['lfcl_id'=>1])->orderBy('rout_code','asc')->get();
        }
        return view('Mapping.Site.index',['rout_list'=>$rout_list,'permission'=>$this->userMenu, 'field' => $field]);
    }
    public function getFilterSite(Request $request){
        $rout_id = '';
        if($request->route_code != ''){
            $rout_code=$request->route_code;
            $rout=Route::on($this->db)->where(['rout_code'=>$rout_code])->firstOrFail();
             $rout_id=$rout->id;
        }else{
            $rout_id=$request->rout_id;
        }
        //  return $rout_id;
        $site_code=$request->site_code;
        $query="";
        if($rout_id !=""){
            $query.=" AND t1.id=$rout_id";
        }
        if($site_code !=""){
            $query.=" AND t5.site_code=$site_code";
        }
        if($rout_id !="" || $site_code !=""){
            // $data=DB::connection($this->db)->select("SELECT
            //     t1.rout_code,t1.rout_name,
            //     t3.id rsmp_id,
            //     ifnull(t5.aemp_name,'')aemp_name,
            //     ifnull(t5.aemp_usnm,'')aemp_usnm,
            //     ifnull(t4.site_code,'')site_code
            //     FROM tm_rout t1
            //     LEFT JOIN tl_rpln t2 ON t1.id=t2.id
            //     LEFT JOIN tl_rsmp t3 ON t1.id=t3.rout_id
            //     LEFT JOIN tm_site t4 ON  t3.site_id=t4.id
            //     LEFT JOIN tm_aemp t5 ON t2.aemp_id=t5.id
            //     Where t1.cont_id='$this->cont_id' ".$query. " ");
            
            $data=DB::connection($this->db)->select("Select
                    t1.rout_code,
                    t1.rout_name,
                    ifnull(t3.aemp_usnm,'')aemp_usnm,
                    ifnull(t3.aemp_name,'')aemp_name,
                    ifnull(t6.aemp_usnm,'')sv_usnm,
                    ifnull(t6.aemp_name,'')sv_name,
                    t4.id rsmp_id,
                    ifnull(t5.site_code,'')site_code,
                    ifnull(t5.site_name,'')site_name,
                    ifnull(t5.site_mob1,'')site_mob1
                    FROM tm_rout t1
                    LEFT JOIN tl_rpln t2 ON t1.id=t2.rout_id
                    LEFT JOIN tm_aemp t3 ON t2.aemp_id=t3.id
                    LEFT JOIN tm_aemp t6 ON t3.aemp_mngr=t6.id
                    LEFT JOIN tl_rsmp t4 ON t1.id=t4.rout_id
                    LEFT JOIN tm_site t5 ON t4.site_id=t5.id
                    Where t1.cont_id='$this->cont_id' ".$query. "
                    GROUP BY t1.rout_code,
                    t1.rout_name,
                    t3.aemp_usnm,
                    t3.aemp_name,
                    t4.id,t5.site_code, t5.site_name, t5.site_mob1
                    ");
            return $data;
        }
        return "";

    }

    public function removeSiteFromRoute(Request $request){
        $rmsite=$request->rmsite;
        DB::connection($this->db)->select("Delete FROM tl_rsmp WHERE id IN (".implode(',',$rmsite).") ");
        return 1;
    }
    public function addSiteToRoute($rout_id, $site_code, $route_code){
        
        if (isset($route_code)) {
            $rout_data=Route::on($this->db)->where(['rout_code'=>$route_code])->first();
            if($rout_data){
                $rout_id = $rout_data->id;
            }else{
            }
            
            
        }

        $msg=0;
        $site=Site::on($this->db)->where(['site_code'=>$site_code])->first();
        if($site !=''){
            $rsmp=RouteSite::on($this->db)->where(['rout_id'=>$rout_id,'site_id'=>$site->id])->first();
            if($rsmp==""){
                
                $rsmp=new RouteSite();
                $rsmp->setConnection($this->db);
                $rsmp->rout_id=$rout_id;
                $rsmp->site_id=$site->id;
                if($this->cont_id == 2 || $this->cont_id == 5){                    
                }else{
                    $rsmp->rout_code='-';
                    $rsmp->site_code='-';
                }              
                // return $this->cont_id;
                $rsmp->rspm_serl='0';
                $rsmp->cont_id=$this->cont_id;
                $rsmp->lfcl_id=1;
                $rsmp->aemp_iusr=$this->aemp_id;
                $rsmp->aemp_eusr=$this->aemp_id;
                $rsmp->save();
                $msg=1;

            }else{
                $msg=2;
            }
        }else{
            $msg=3;
        }
        return $msg;

    }

    public function bulkRouteSiteMapping(){
        return view('Mapping.Site.bulk_route_site',['permission'=>$this->userMenu]);
    }
    public function getRouteSiteUploadFormat(){
        return Excel::download(new RSMP(), 'route_site_mapping' . date("Y-m-d H:i:s") . '.xlsx');
    }

    public function mappingRemove(){
        $rout_list=Route::on($this->db)->where(['lfcl_id'=>1])->orderBy('rout_code','asc')->get();

        return view('Mapping.Site.route_remove',['rout_list'=>$rout_list,'permission'=>$this->userMenu]);
    }

    public function routRemove(Request $request){
        $rout_id = $request->rout_id;

        try {
            RouteSite::on($this->db)->where('rout_id', $rout_id)->delete();

            return response(["Route Removed from Mapping"], 200);
        }catch (\Exception $e)
        {
            return response(["No Access"], 400);
        }
    }

    public function econsaveMapping(){
        return view('Mapping.Site.econsave_mapping',['permission'=>$this->userMenu]);
    }
    public function econsaveMappingDownload(){
        return Excel::download(new SitePartyMapping(), 'site_party_mapping' . date("Y-m-d H:i:s") . '.xlsx');
    }
    public function econsaveMappingStore(Request $request){
        if ($request->hasFile('import_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new SitePartyMapping(), $request->file('import_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e);
            }
        }
        return back()->with('danger', ' File Not Found');
    }

}
