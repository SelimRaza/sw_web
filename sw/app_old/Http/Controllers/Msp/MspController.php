<?php

namespace App\Http\Controllers\Msp;

use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;
use App\BusinessObject\DefaultDiscount;
use App\BusinessObject\DefaultDiscountItemMapping;
use App\BusinessObject\DefaultDiscountSiteMapping;
use App\MasterData\Msp;
use App\MasterData\ItemMsp;
use App\MasterData\SiteMsp;
use App\MasterData\SiteMappingWithDefaultDiscount;
class MspController extends Controller
{
    private $access_key = 'tm_mspm';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;

    public function __construct()
    {
        set_time_limit(80000000);
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

    public function index(Request $request){
        $sq='';
        if($request->has('search_text')){            
            $sq=$request->search_text;
            $msps= DB::connection($this->db)->table('tm_mspm AS t1')
                    ->select('t1.id AS id', 't1.mspm_name', 't1.mspm_code', 't1.mspm_sdat', 't1.mspm_edat','t1.lfcl_id')->orderByDesc('t1.id')
                    ->Where('t1.mspm_code', 'LIKE', '%' . $sq . '%')
                    ->orWhere('t1.mspm_name', 'LIKE', '%' . $sq . '%')
                    ->paginate(50);
        }
        else{
            $msps=Msp::on($this->db)->orderBy('id','DESC')->paginate(50);
        }
        
        return view('Msp.index',['msps'=>$msps,'permission'=>$this->userMenu,'search_text'=>$sq]);
    }

    public function actvInactv($id){
        $msp = Msp::on($this->db)->findorfail($id);
        $msp->lfcl_id = $msp->lfcl_id == 1 ? 2 : 1;
        $msp->aemp_eusr = $this->currentUser->employee()->id;
        $msp->save();
        return redirect()->back()->with('success', 'Successfully Updated');
    }

    public function createMsp(){
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $msps = DB::connection($this->db)->select("SELECT id,mspm_name,mspm_code FROM tm_mspm WHERE lfcl_id=1 AND mspm_edat>=curdate()");
        $cats=DB::connection($this->db)->select("SELECT id,otcg_name,otcg_code FROM tm_otcg WHERE lfcl_id=1 Order By otcg_name ASC");
        $zones=DB::connection($this->db)->select("SELECT zone_id,zone_name FROM user_area_permission WHERE aemp_id=$empId ORDER BY zone_name ASC");
        return view('Msp.create')->with('acmp',$acmp)->with('msps',$msps)->with('cats',$cats)->with('zones',$zones);
    }

    public function saveMsp(Request $request){
        $db=$this->db.'.'.'tm_mspm';
        $validatedData = $request->validate([
            'mspm_name' => 'required',
            'mspm_code' => 'required|max:50|unique:'.$db,
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $msp=new Msp();
        $msp->setConnection($this->db);
        $msp->mspm_name=$request->mspm_name;
        $msp->mspm_code=$request->mspm_code;
        $msp->mspm_sdat=$request->start_date;
        $msp->mspm_edat=$request->end_date;
        $msp->cont_id=Auth::user()->employee()->cont_id;
        $msp->lfcl_id=1;
        $msp->aemp_iusr=Auth::user()->employee()->id;
        $msp->aemp_eusr=Auth::user()->employee()->id;
        $msp->save();
        return redirect()->back()->with('success', 'MSP Added Successfully');
    }
    public function loadItem($search_text){
        $items=DB::connection($this->db)->select("SELECT t1.id,t1.amim_code,t1.amim_name FROM tm_amim t1
                WHERE t1.amim_code LIKE '%$search_text%' OR t1.amim_name LIKE '%$search_text%'
                AND t1.lfcl_id=1");
        return $items;

    }

    public function itemMappingWithMsp(Request $request){
        $validatedData = $request->validate([
            'mspm_id' => 'required',
            'amim_id' => 'required',
            'mspd_qnty' => 'required|numeric',
        ]);
        DB::connection($this->db)->select("INSERT IGNORE INTO tm_mspd (mspm_id,amim_id,mspd_qnty,cont_id,lfcl_id,aemp_iusr,aemp_eusr) VALUES
         ('$request->mspm_id','$request->amim_id','$request->mspd_qnty','$this->cont_id',1,'$this->aemp_id','$this->aemp_id')
          ON DUPLICATE KEY UPDATE mspd_qnty='$request->mspd_qnty',aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
    }
    public function siteMappingWithMsp(Request $request){
        //return ($request->all());
        $site_cat=$request->site_cat;
        $site_code=$request->site_code;
        $mspm_id=$request->mspm_id;
        $slgp_id=$request->slgp_id;
        $cont_id=Auth::user()->employee()->cont_id;
        $aemp_id=Auth::user()->employee()->id;
        if($site_cat){
            DB::connection($this->db)->select("INSERT IGNORE INTO tl_msps SELECT null,'$mspm_id',t1.id,'$slgp_id','$cont_id','1','$aemp_id','$aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0   FROM tm_site t1 
            INNER JOIN tm_otcg t2 ON t1.otcg_id=t2.id
            WHERE t2.id=$site_cat AND t1.lfcl_id=1
            ON DUPLICATE KEY UPDATE aemp_eusr=$aemp_id,
                        updated_at=CURRENT_TIMESTAMP
            ");
        }
        if($site_code){
            $site=DB::connection($this->db)->select("SELECT t1.id   FROM tm_site t1 
                    WHERE t1.site_code=$site_code AND t1.lfcl_id=1");
            if($site){
                $site_id=$site[0]->id;
                DB::connection($this->db)->select("INSERT IGNORE INTO tl_msps SELECT null,'$mspm_id','$site_id','$slgp_id','$cont_id','1','$aemp_id','$aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0   FROM tm_site t1 
                    ON DUPLICATE KEY UPDATE aemp_eusr=$aemp_id,
                                updated_at=CURRENT_TIMESTAMP
                    ");
            }
        }
        
        
    }
    public function showMspItem($id){
        $data=DB::connection($this->db)->select("SELECT t1.id, t3.id amim_id,t2.id mspm_id,t3.amim_code,t3.amim_name,t1.mspd_qnty,t2.mspm_code,t2.mspm_name
                FROM `tm_mspd` t1
                INNER JOIN tm_mspm t2 ON t1.mspm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id=t3.id
                WHERE t2.id='$id' Order By t3.amim_name ASC");
        return view('Msp.itemShow',['data'=>$data,'permission'=>$this->userMenu]);

    }
    public function removeItemFromMsp($id,$mspm_id){
        $dfim=ItemMsp::on($this->db)->find($id);
        $dfim->delete();
        $data=DB::connection($this->db)->select("SELECT t1.id, t3.id amim_id,t2.id mspm_id,t3.amim_code,t3.amim_name,t1.mspd_qnty,t2.mspm_code,t2.mspm_name
                FROM `tm_mspd` t1
                INNER JOIN tm_mspm t2 ON t1.mspm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id=t3.id
                WHERE t2.id='$mspm_id' Order By t3.amim_name ASC");
        return $data;
    }

    public function showMspSite($id){
        $data = DB::connection($this->db)->table('tl_msps AS t1')
                    ->join('tm_site AS t2', 't1.site_id', '=', 't2.id')
                    ->join('tm_mspm AS t3', 't1.mspm_id', '=', 't3.id')
                    ->select('t1.id AS id', 't3.id AS mspm_id', 't2.site_name', 't2.site_code', 't2.site_adrs', 't2.site_mob1', 't3.mspm_name', 't3.mspm_code')->orderBy('t2.site_name', 'ASC')
                    ->where('t3.id',$id)
                    ->paginate(50);
        return view('Msp.siteShow',['data'=>$data,'permission'=>$this->userMenu]);
    }

    public function removeSite($id,$mspm_id){
        $dfsm=SiteMsp::on($this->db)->find($id);
        $dfsm->delete();
        return redirect()->back()->with('success', 'Site Removed Successfully');
    }
    public function editMsp($id){
        $msp=Msp::on($this->db)->find($id);
        return view('Msp.editMsp',['msp'=>$msp,'permission'=>$this->userMenu]);
    }

    public function updateMsp(Request $request,$id){
        $msp=Msp::on($this->db)->find($id);

            $validatedData = $request->validate([
                'mspm_name' => 'required',
                'end_date' => 'required',
            ]);       
        $msp->mspm_name=$request->mspm_name;
        $msp->mspm_edat=$request->end_date;
        $msp->save();
        return redirect()->back()->with('success', 'MSP Updated Successfully');
    }

    public function getItemMappingFormat(){
        return Excel::download(new ItemMsp(),'item_mapping_with_msp' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function uploadItem(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('item_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new ItemMsp(), $request->file('item_file'));
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

    public function getSiteMappingFormat(){
        return Excel::download(new SiteMsp(),'site_mapping_with_msp' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function uploadSite(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('site_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SiteMsp(), $request->file('site_file'));
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

    public function slgpZoneMspMapping(Request $request){
        DB::connection($this->db)->select("INSERT IGNORE INTO tl_mspg (mspm_id,slgp_id,zone_id,cont_id,lfcl_id,aemp_iusr,aemp_eusr) VALUES
        ('$request->mspm_id','$request->slgp_id','$request->zone_id','$this->cont_id',1,'$this->aemp_id','$this->aemp_id') ON DUPLICATE
        KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP
        ");
    }

    public function showSlgpZoneMsp($id){
        // $data=DB::connection($this->db)->select("SELECT 
        //     t1.id,t2.id mspm_id,t3.slgp_name,t3.slgp_code,t4.zone_name,t4.zone_code,t2.mspm_name,t2.mspm_code
        //     FROM `tl_mspg` t1
        //     INNER JOIN tm_mspm t2 ON t1.mspm_id=t2.id
        //     INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
        //     INNER JOIN tm_zone t4 ON t1.zone_id=t4.id
        //     WHERE t2.id=$id");
        $data=DB::connection($this->db)->table('tl_mspg AS t1')
            ->join('tm_mspm AS t2', 't1.mspm_id', '=', 't2.id')
            ->join('tm_slgp AS t3', 't1.slgp_id', '=', 't3.id')
            ->join('tm_zone AS t4', 't1.zone_id', '=', 't4.id')
            ->select('t1.id AS id', 't2.id AS mspm_id', 't3.slgp_name', 't3.slgp_code', 't4.zone_name', 't4.zone_code', 't2.mspm_name', 't2.mspm_code')->orderBy('t3.slgp_name', 'ASC')
            ->where('t2.id',$id)
            ->paginate(50);
        return view('Msp.slgpZoneShow',['data'=>$data,'permission'=>$this->userMenu]);
        
    }
    public function removeSlgpZoneMsp($id,$mspm_id){
        DB::connection($this->db)->select("DELETE FROM tl_mspg WHERE id=$id");
        $data=DB::connection($this->db)->table('tl_mspg AS t1')
            ->join('tm_mspm AS t2', 't1.mspm_id', '=', 't2.id')
            ->join('tm_slgp AS t3', 't1.slgp_id', '=', 't3.id')
            ->join('tm_zone AS t4', 't1.zone_id', '=', 't4.id')
            ->select('t1.id AS id', 't2.id AS mspm_id', 't3.slgp_name', 't3.slgp_code', 't4.zone_name', 't4.zone_code', 't2.mspm_name', 't2.mspm_code')->orderBy('t3.slgp_name', 'ASC')
            ->where('t2.id',$mspm_id)
            ->paginate(50);
        return view('Msp.slgpZoneShow',['data'=>$data,'permission'=>$this->userMenu]);
        
    }

    

}
