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
use App\Mapping\MQTY;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
Use App\MasterData\SKU;
Use App\MasterData\SubCategory;
use Excel;
class SkuMappingController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
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
        $category_list=DB::connection($this->db)->select("Select id, itcg_name FROM tm_itcg WHERE lfcl_id=1 order by itcg_name");
        return view('Mapping.sku',['category_list'=>$category_list]);
    }
    public function getSubCategory($id){
        $category_list=DB::connection($this->db)->select("Select id, itsg_name FROM tm_itsg WHERE lfcl_id=1 AND itcg_id=$id order by itsg_name");
        return $category_list;
    }
    public function getSku(Request $request){
        $cat=$request->itcg_id;
        $s_cat=$request->itsg_id;
        $item_code=$request->item_code;
        $q='';
        if($cat){
            $q.=" AND t3.id=$cat";
        }
        if($s_cat){
            $q.=" AND t2.id=$s_cat";
        }
        if($item_code){
            $q.=" AND t1.amim_code='$item_code'";
        }
        $data=DB::connection($this->db)->select("SELECT 
            t1.id,
            t4.id lfcl_id,
            t1.amim_code,
            t1.amim_name,
            t1.amim_duft,
            t1.amim_pexc,
            t1.amim_pvat,
            t3.itcg_name,
            t2.itsg_name,
            t4.lfcl_name
            FROM tm_amim t1
            INNER JOIN tm_itsg t2 on t1.itsg_id=t2.id
            INNER JOIN tm_itcg t3 ON t2.itcg_id=t3.id
            INNER JOIN tm_lfcl t4 ON t1.lfcl_id=t4.id
            WHERE 1 ".$q ."
            ORDER BY t1.amim_code,t1.amim_name");
        return $data;
           
    }
    public function itemLfclChange($id,$lfcl_id){
        $sku=SKU::find($id);
        $p=0;
        if($lfcl_id==1){
            $p=2;
        }else{
            $p=1;
        }
        $sku->lfcl_id=$p;
        $sku->save();
        return 1;
    }
    public function skuMappingList(){
        $category_list='';
        return view('Mapping.sku_mapping',['category_list'=>$category_list]);
    }
    public function skuMappingData(){
        $data1=DB::connection($this->db)->select("SELECT
                t1.amim_code,t1.amim_name,t3.slgp_code,t3.slgp_name
                FROM tm_amim t1
                INNER JOIN tl_sgit t2 ON t2.amim_id=t1.id
                INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                limit 1
                ");
        $data2=DB::connection($this->db)->select("SELECT 
                t2.amim_code,t2.amim_name,t3.plmt_code,t3.plmt_name
                FROM tm_pldt t1
                INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                INNER JOIN tm_plmt t3 ON t1.plmt_id=t3.id               
                limit 1");
        return view('Mapping.sku_mapping',['data1'=>$data1,'data2'=>$data2]);
        
    }
    public function skuMappingListSearch(Request $request){
        $amim_code=$request->item_code;
        $data1=DB::connection($this->db)->select("SELECT t2.id,
                t1.amim_code,t1.amim_name,t3.slgp_code,t3.slgp_name
                FROM tm_amim t1
                INNER JOIN tl_sgit t2 ON t2.amim_id=t1.id
                INNER JOIN tm_slgp t3 ON t2.slgp_id=t3.id
                
                WHERE t1.amim_code='$amim_code'
                GROUP BY  t2.id,t1.amim_code,t1.amim_name,t3.slgp_code,t3.slgp_name
                ORDER BY t3.slgp_code
                ");
        $data2=DB::connection($this->db)->select("SELECT t1.id,t2.id amim_id,t3.id plmt_id,
                t2.amim_code,t2.amim_name,t3.plmt_code,t3.plmt_name,round(t1.pldt_tppr*t1.amim_duft,4)pldt_tppr,round(t1.pldt_tpgp*t1.amim_duft,4)pldt_tpgp
                FROM tm_pldt t1
                INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                INNER JOIN tm_plmt t3 ON t1.plmt_id=t3.id
                WHERE t2.amim_code='$amim_code'
                GROUP BY t1.id,t2.amim_code,t2.amim_name,t3.plmt_code,t3.plmt_name
                ORDER BY t3.plmt_code");
        return array(
            'data1'=>$data1,
            'data2'=>$data2
        );
        
    }
    public function removeItemFromPriceList($id){
        DB::connection($this->db)->select("Delete FROM tm_pldt WHERE id=$id");
        return 1;
    }
    public function removeItemFromSlgp($id){
        DB::connection($this->db)->select("Delete FROM tl_sgit WHERE id=$id");
        return 1;
    }

    public function mappingItemWithPriceList(){
        $plmt_list= DB::connection($this->db)->select("Select id,plmt_code,plmt_name FROM tm_plmt Order By plmt_code ASC");
        return view('Mapping.sku_plmt',['plmt_list'=>$plmt_list]);
    }
    public function addItemIntoPriceList(Request $request){
        $amim_code=$request->amim_code;
        $plmt_code=$request->plmt_code;
        $amim_tppr=$request->amim_tppr;
        $amim_gppr=$request->amim_gppr;
        $amim_dppr=$request->amim_dppr;
        $amim_dgpr=$request->amim_dgpr;
        try{
            for($i=0;$i<count($amim_code);$i++){
                $amim=ItemMaster::on($this->db)->where(['amim_code'=>$amim_code[$i]])->first();
                $plmt=PriceList::on($this->db)->where(['plmt_code'=>$plmt_code[$i]])->first();
                $amim_id=$amim?$amim->id:'';
                $factor=$amim?$amim->amim_duft:'';
                $plmt_id=$plmt?$plmt->id:'';
                if($amim_id){
                    $pldt=PLDT::on($this->db)->where(['amim_id'=>$amim_id,'plmt_id'=>$plmt_id])->first();
                    if($pldt){
                        $pldt->pldt_dppr=$amim_dppr[$i]/$factor;
                        $pldt->pldt_dgpr=$amim_dgpr[$i]/$factor;
                        $pldt->pldt_tppr=$amim_tppr[$i]/$factor;
                        $pldt->pldt_tpgp=$amim_gppr[$i]/$factor;
                        $pldt->pldt_mrpp=$amim_tppr[$i]/$factor;
                        $pldt->aemp_eusr=$this->aemp_id;
                        $pldt->save();
                    }
                    else{
                        $pldt=new PLDT();
                        $pldt->setConnection($this->db);
                        $pldt->plmt_id=$plmt_id;
                        $pldt->amim_id=$amim_id;
                        $pldt->plmt_code=$plmt->plmt_code;
                        $pldt->amim_code=$amim->amim_code;
                        $pldt->pldt_dppr=$amim_dppr[$i]/$factor;
                        $pldt->pldt_dgpr=$amim_dgpr[$i]/$factor;
                        $pldt->pldt_tppr=$amim_tppr[$i]/$factor;
                        $pldt->pldt_tpgp=$amim_gppr[$i]/$factor;
                        $pldt->pldt_mrpp=$amim_tppr[$i]/$factor;
                        $pldt->pldt_snme=$amim->amim_name;
                        $pldt->amim_duft=$amim->amim_duft;
                        $pldt->amim_dunt=$amim->amim_dunt;
                        $pldt->amim_runt=$amim->amim_runt;
                        $pldt->cont_id=$this->cont_id;
                        $pldt->aemp_eusr=$this->aemp_id;
                        $pldt->aemp_iusr=$this->aemp_id;
                        $pldt->lfcl_id=1;
                        $pldt->save();
    
                    }
                }
                else{
                    return redirect()->back()->with('danger', 'Please provide valid item code !!!!');
                }
                
            }
            return redirect()->back()->with('success', 'Item added successfully');

        }
        catch(\Exception $e){
            return $e->getMessage();
            //return redirect()->back()->with('danger', 'Something went wrong!!!');
        }
        
    }
    public function priceListBulkFormat(){
        return Excel::download(new PLDT(),'pricelist_item' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function sgitBulkFormat(){
        return Excel::download(new SGIT(),'sales_grout_item' . date("Y-m-d H:i:s") . '.xlsx' );
    }
    public function addBulkItemIntoPriceList(Request $request){
        if ($request->hasFile('plmt_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new PLDT(), $request->file('plmt_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e);
            }
        }
        return back()->with('danger', ' File Not Found');
    }
    public function addBulkItemIntoGroup(Request $request){
        if ($request->hasFile('plmt_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new SGIT(), $request->file('plmt_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e);
            }
        }
        return back()->with('danger', ' File Not Found');
    }
    public function mappingItemwithSlgp(){
        $empId=$this->aemp_id;
        $slgp_list=DB::connection($this->db)->select("Select slgp_id,slgp_name FROM user_group_permission Where aemp_id=$empId Order By slgp_id ASC");
        return view('Mapping.sku_slgp',['slgp_list'=>$slgp_list]);     
    }
    public function addItemIntoSlgp(Request $request){
        try{
            $slgp_id=$request->slgp_id;
            $amim_code=$request->amim_code;
            $s_cat=$request->s_cat;
            for($i=0;$i<count($slgp_id);$i++){
                $sku=$this->getAmimId($amim_code[$i]);
                $amim_id=$sku->id;
                $group_id=$slgp_id[$i];
                $issc_code=$s_cat[$i]?$s_cat[$i]:$this->getItsgCode($sku->itsg_id);
                $s_cat_id=$this->getSalesCatId($issc_code,$group_id);
                $sgit=SalesGroupSku::on($this->db)->where(['slgp_id'=>$group_id,'amim_id'=>$amim_id])->first();
                if(!$sgit){
                    $sgit=new SalesGroupSku();
                    $sgit->setConnection($this->db);
                    $sgit->slgp_id=$group_id;
                    $sgit->slgp_code='';
                    $sgit->amim_id=$amim_id;
                    $sgit->item_code=$amim_code[$i];
                    $sgit->category_name='';
                    $sgit->issc_code=$issc_code;
                    $sgit->issc_id=$s_cat_id;
                    $sgit->sgit_moqt=1;
                    $sgit->cont_id=$this->cont_id;
                    $sgit->aemp_iusr=$this->aemp_id;
                    $sgit->aemp_eusr=$this->aemp_id;
                    $sgit->lfcl_id=1;
                    $sgit->attr4=1;
                    $sgit->save();
                }
                else{
                    $sgit->issc_code=$issc_code;
                    $sgit->issc_id=$s_cat_id;
                    $sgit->save();
                }
            }
            return redirect()->back()->with('success', 'Item added successfully');
        }
        catch(\Exception $e){
           return redirect()->back()->with('danger', 'Something went wrong!!!');
        }
        
    }
    public function getAmimId($amim_code){
        return SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
          
    }
    public function getItsgCode($id){
        $sku=SubCategory::on($this->db)->where(['id'=>$id])->first();
        return $sku->itsg_code;     
    }
    public function getSalesCatId($code,$slgp_id){
        $cat=DB::connection($this->db)->select("Select id from tm_issc where issc_code='$code' AND slgp_id={$slgp_id} limit 1");
        return $cat?$cat[0]->id:'';     
    }
    public function singlePlmtItemView($amim_id,$plmt_id){
        $pldt=PLDT::on($this->db)->where(['amim_id'=>$amim_id,'plmt_id'=>$plmt_id])->first();
        $plmt=PriceList::on($this->db)->find($pldt->plmt_id)->first();
        $plmt_name=$plmt->plmt_name;
        return view('Mapping.single_sku_view',['pldt'=>$pldt,'plmt_name'=>$plmt_name]);  
    }
    public function updatePldtItemPrice(Request $request){
        $pldt=PLDT::on($this->db)->where(['amim_id'=>$request->amim_id,'plmt_id'=>$request->plmt_id])->first();
        $factor=$pldt->amim_duft;
        $price=$request->tppr;
        $grv=$request->gppr;
        $pldt->pldt_dppr=$price/$factor;
        $pldt->pldt_dgpr=$grv/$factor;
        $pldt->pldt_tppr=$price/$factor;
        $pldt->pldt_tpgp=$grv/$factor;
        $pldt->pldt_mrpp=$price/$factor;
        $pldt->aemp_eusr=$this->aemp_id;
        $pldt->save();
        return 1;
    }
    public function skuMinQty(){
        $sku_list=DB::connection($this->db)->select("Select id,amim_name,amim_code FROM tm_amim WHERE lfcl_id=1 ORDER BY amim_code ASC");
        return view('Mapping.sku_min_qty',['sku_list'=>$sku_list]);  
    }
    public function minQtyBulkFormat(){
        return Excel::download(new MQTY(),'min_qty' . date("Y-m-d") . '.xlsx' );
    }
    public function minQtyBulkUpload(Request $request){
        if ($request->hasFile('plmt_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new MQTY(), $request->file('plmt_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e);
            }
        }
        return back()->with('danger', ' File Not Found');
    }
    public function addItemMinQty(Request $request){
        try{
            $sku_id=$request->sku_id;
            $min_qty=$request->min_qty;
            for($i=0;$i<count($sku_id);$i++){
                $amim_id=$sku_id[$i];
                $qty=$min_qty[$i];
                $sku_exist=SalesGroupSku::on($this->db)->where(['amim_id'=>$amim_id])->get();
                if($sku_exist){
                    foreach($sku_exist as $result){
                        $result->attr4 = $qty;
                        $result->aemp_eusr=Auth::user()->employee()->id;
                        $result->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'Item added successfully');
        }
        catch(\Exception $e){
            return $e->getMessage();
           return redirect()->back()->with('danger', 'Something went wrong!!!');
        }
        
    }
    
}