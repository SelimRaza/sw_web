<?php

namespace App\Http\Controllers\Promotion;

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
use App\MasterData\ItemMappingWithDefaultDiscount;
use App\MasterData\SiteMappingWithDefaultDiscount;
class DefaultDiscountController extends Controller
{
    private $access_key = 'tm_dfdm';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(80000000);
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

    public function index(Request $request){
        $sq='';
        if($request->has('search_text')){
            $sq=$request->has('search_text');
            $dfdsc= DB::connection($this->db)->table('tm_dfdm AS t1')
                        ->select('t1.id AS id', 't1.dfdm_name', 't1.dfdm_code', 't1.start_date', 't1.end_date','t1.lfcl_id')->orderByDesc('t1.id')
                        ->where(function ($query) use ($sq) {
                            $query->Where('t1.dfdm_code', 'LIKE', '%' . $sq . '%')
                                ->orWhere('t1.dfdm_name', 'LIKE', '%' . $sq . '%');
                        })->paginate(100);
        }
        else{
            $dfdsc=DefaultDiscount::on($this->db)->orderBy('id','DESC')->get();
        }
        
        return view('DefaultDiscount.index',['dfdsc'=>$dfdsc,'permission'=>$this->userMenu,'search_text'=>$sq]);
    }

    public function actvInactv($id){
        $dfdsc = DefaultDiscount::on($this->db)->findorfail($id);
        $dfdsc->lfcl_id = $dfdsc->lfcl_id == 1 ? 2 : 1;
        $dfdsc->aemp_eusr = $this->currentUser->employee()->id;
        $dfdsc->save();
        return redirect()->back()->with('success', 'Successfully Updated');
    }

    public function createDefaultDiscount(){
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $dfdm=DB::connection($this->db)->select("SELECT id,dfdm_name,dfdm_code FROM tm_dfdm WHERE lfcl_id=1 AND end_date>=curdate() order by dfdm_name ASC");
        $cats=DB::connection($this->db)->select("SELECT id,otcg_name,otcg_code FROM tm_otcg WHERE lfcl_id=1 Order By otcg_name ASC");
        return view('DefaultDiscount.create')->with('dfdm',$dfdm)->with('cats',$cats)->with('acmp',$acmp);
    }

    public function storeDefaultDiscount(Request $request){
        $db=$this->db.'.'.'tm_dfdm';
        $validatedData = $request->validate([
            'dfdm_name' => 'required',
            'dfdm_code' => 'required|max:50|unique:'.$db,
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $dfdsc=new DefaultDiscount();
        $dfdsc->setConnection($this->db);
        $dfdsc->dfdm_name=$request->dfdm_name;
        $dfdsc->dfdm_code=$request->dfdm_code;
        $dfdsc->start_date=$request->start_date;
        $dfdsc->end_date=$request->end_date;
        $dfdsc->cont_id=Auth::user()->employee()->cont_id;
        $dfdsc->lfcl_id=1;
        $dfdsc->aemp_iusr=Auth::user()->employee()->id;
        $dfdsc->aemp_eusr=Auth::user()->employee()->id;
        $dfdsc->save();

        return redirect()->back()->with('success', 'Default Discount Added Successfully');
    }
    public function loadItem($search_text){
        $items=DB::connection($this->db)->select("SELECT t1.id,t1.amim_code,t1.amim_name FROM tm_amim t1
                WHERE t1.amim_code LIKE '%$search_text%' OR t1.amim_name LIKE '%$search_text%'
                AND t1.lfcl_id=1");
        return $items;

    }

    public function itemMappingWithDefaultDiscount(Request $request){
        $validatedData = $request->validate([
            'dfdm_id' => 'required',
            'amim_id' => 'required',
            'dfim_disc' => 'required| max:4',
        ]);
        $disc=$request->dfim_disc;
        if($disc>=80){
            $disc=80;
        }
        $message=0;
        $dfim=new DefaultDiscountItemMapping();
        $dfim->setConnection($this->db);
        $dfim->dfdm_id=$request->dfdm_id;
        $dfim->amim_id=$request->amim_id;
        $dfim->dfim_disc=$disc;
        $dfim->cont_id=Auth::user()->employee()->cont_id;
        $dfim->lfcl_id=1;
        $dfim->aemp_iusr=Auth::user()->employee()->id;
        $dfim->aemp_eusr=Auth::user()->employee()->id;
        $dfim->save();
        $message=1;
        return $message;


    }
    public function siteMappingWithDefaultDiscount(Request $request){
        
        set_time_limit(80000000);
        $site_cat=$request->site_cat;
        $site_code=$request->site_code;
        $dfdm_id=$request->dfdm_id;
        $slgp_id=$request->slgp_id;
        $cont_id=Auth::user()->employee()->cont_id;
        $aemp_id=Auth::user()->employee()->id;
        for($i=0;$i<count($slgp_id);$i++){
            $temp_slgp=$slgp_id[$i];
            if($site_cat){
                DB::connection($this->db)->select("INSERT IGNORE INTO tl_dfsm SELECT null,'$dfdm_id',t1.id,'$temp_slgp','$cont_id','1','$aemp_id','$aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0   FROM tm_site t1 
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
                    DB::connection($this->db)->select("INSERT IGNORE INTO tl_dfsm SELECT null,'$dfdm_id','$site_id','$temp_slgp','$cont_id','1','$aemp_id','$aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0   FROM tm_site t1 
                        ON DUPLICATE KEY UPDATE aemp_eusr=$aemp_id,
                                    updated_at=CURRENT_TIMESTAMP
                        ");
                }
            }
        }
        
        
    }
    public function showDefaultDiscountItem ($id){
        $data=DB::connection($this->db)->select("SELECT t1.id, t3.id amim_id,t2.id dfdm_id,t3.amim_code,t3.amim_name,t1.dfim_disc,t2.dfdm_code,t2.dfdm_name
                FROM `tm_dfim` t1
                INNER JOIN tm_dfdm t2 ON t1.dfdm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id=t3.id
                WHERE t2.id='$id' Order By t3.amim_name ASC");
        return view('DefaultDiscount.itemShow',['data'=>$data,'permission'=>$this->userMenu]);

    }
    public function removeItem($id,$dfdm_id){
        $dfim=DefaultDiscountItemMapping::on($this->db)->find($id);
        $dfim->delete();
        $data=DB::connection($this->db)->select("SELECT t1.id,t2.id dfdm_id, t3.id amim_id,t3.amim_code,t3.amim_name,t1.dfim_disc,t2.dfdm_code,t2.dfdm_name
                FROM `tm_dfim` t1
                INNER JOIN tm_dfdm t2 ON t1.dfdm_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id=t3.id
                WHERE t2.id='$dfdm_id' Order By t3.amim_name ASC");
        return $data;
    }

    public function showDefaultDiscountSite($id){
        $data = DB::connection($this->db)->table('tl_dfsm AS t1')
                    ->join('tm_site AS t2', 't1.site_id', '=', 't2.id')
                    ->join('tm_dfdm AS t3', 't1.dfdm_id', '=', 't3.id')
                    ->select('t1.id AS id', 't3.id AS dfdm_id', 't2.site_name', 't2.site_code', 't2.site_adrs', 't2.site_mob1', 't3.dfdm_name', 't3.dfdm_code')->orderBy('t2.site_name', 'ASC')
                    ->where('t3.id',$id)
                    ->paginate(50);
        return view('DefaultDiscount.siteShow',['data'=>$data,'permission'=>$this->userMenu]);
    }

    public function removeSite($id,$dfdm_id){
        $dfsm=DefaultDiscountSiteMapping::on($this->db)->find($id);
        $dfsm->delete();
        return redirect()->back()->with('success', 'Site Removed Successfully');
    }
    public function editDefaultDiscount($dfdm_id){
        $dfdm=DefaultDiscount::on($this->db)->find($dfdm_id);
        return view('DefaultDiscount.editDefaultDiscount',['dfdm'=>$dfdm,'permission'=>$this->userMenu]);
    }

    public function updateDefaultDiscount(Request $request,$id){
        $dfdm=DefaultDiscount::on($this->db)->find($id);
        if($this->db=='mydb_uae'){
            $validatedData = $request->validate([
                'dfdm_name' => 'required',
                'end_date' => 'required',
            ]);
        }        
        $dfdm->dfdm_name=$request->dfdm_name;
        $dfdm->end_date=$request->end_date;
        $dfdm->save();
        return redirect()->back()->with('success', 'Default Discount Updated Successfully');
    }

    public function getItemMappingFormat(){
        return Excel::download(new itemMappingWithDefaultDiscount(),'item_mapping_with_default_discount' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function uploadItem(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('item_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new ItemMappingWithDefaultDiscount(), $request->file('item_file'));
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
        return Excel::download(new SiteMappingWithDefaultDiscount(),'site_mapping_with_default_discount' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function uploadSite(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('site_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SiteMappingWithDefaultDiscount(), $request->file('site_file'));
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
