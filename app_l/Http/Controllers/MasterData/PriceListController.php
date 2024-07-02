<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\CompanyEmployee;
use App\BusinessObject\HRPolicy;
use App\BusinessObject\HRPolicyEmployee;
use App\BusinessObject\PriceList;
use App\BusinessObject\PriceListDetails;
use App\BusinessObject\SalesGroupCategory;
use App\BusinessObject\SalesGroupSku;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\SKU;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
    private $access_key = 'tm_plmt';
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
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->country()->id;
            $cat_id='';
            $sub_cat_id='';
            $item_code='';
            $site_code='';
            $priceLists = DB::connection($this->db)->select("SELECT
                            t1.id        AS id,
                            t1.plmt_name AS name,
                            t1.plmt_code AS code,
                            t1.lfcl_id   AS status_id
                            FROM tm_plmt AS t1
                            WHERE  t1.cont_id = $country_id");
            $cats=DB::connection($this->db)->select("Select id,itcg_name FROM tm_itcg where lfcl_id=1 order by itcg_name asc");
            return view('master_data.PriceList.index',['cat_id'=>$cat_id,'sub_cat_id'=>$sub_cat_id,'item_code'=>$item_code,'site_code'=>$site_code])->with("priceLists", $priceLists)->with('permission', $this->userMenu)->with("cats", $cats);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.PriceList.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $priceList = new PriceList();
        $priceList->setConnection($this->db);
        $priceList->plmt_name = $request->name;
        $priceList->plmt_code = $request->code;
        $priceList->cont_id = $this->currentUser->country()->id;
        $priceList->lfcl_id = 1;
        $priceList->aemp_iusr = $this->currentUser->employee()->id;
        $priceList->aemp_eusr = $this->currentUser->employee()->id;
        $priceList->var = 1;
        $priceList->attr1 = '';
        $priceList->attr2 = '';
        $priceList->attr3 = 0;
        $priceList->attr4 = 0;
        $priceList->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $priceList = PriceList::on($this->db)->findorfail($id);
            return view('master_data.PriceList.show')->with('priceList', $priceList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceList = PriceList::on($this->db)->findorfail($id);
            return view('master_data.PriceList.edit')->with('priceList', $priceList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $priceList = PriceList::on($this->db)->findorfail($id);
        $priceList->plmt_name = $request->name;
        $priceList->plmt_code = $request->code;
        $priceList->aemp_eusr = $this->currentUser->employee()->id;
        $priceList->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $priceList = PriceList::on($this->db)->findorfail($id);
            $priceList->lfcl_id = $priceList->lfcl_id == 1 ? 2 : 1;
            $priceList->aemp_eusr = $this->currentUser->employee()->id;
            $priceList->save();
            return redirect('/price_list');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public function skuEdit($id)
    {
        
        if ($this->userMenu->wsmu_read) {
            
            $priceList = PriceList::on($this->db)->findorfail($id);
            $groupWishCategoryies = SalesGroupCategory::on($this->db)->where('slgp_id',$id)->get();
            
            $skus = SKU::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->where('lfcl_id','=','1')->get();
            $priceListDetails = PriceListDetails::on($this->db)->where('plmt_id', '=', $id)->get();
            return view('master_data.PriceList.price_list_item')->with("priceList", $priceList)
                   ->with("priceListDetails", $priceListDetails)
                   ->with("skus", $skus)
                   ->with("groupWishCategoryies",$groupWishCategoryies)
                   ->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function priceListItemEdit($priceId, $id)
    {
        if ($this->userMenu->wsmu_read) {
            $priceList = PriceList::on($this->db)->findorfail($priceId);
            $priceListDetails = PriceListDetails::on($this->db)->findorfail($id);
            return view('master_data.PriceList.price_list_item_edit')->with("priceList", $priceList)->with("priceListDetails", $priceListDetails)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function priceListItemEditSubmit(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceListDetails = PriceListDetails::on($this->db)->findorfail($id);
            if ($priceListDetails != null) {
                $priceListDetails->pldt_dppr = $request->pldt_dppr / $request->amim_duft;
                $priceListDetails->pldt_dgpr = $request->pldt_dgpr / $request->amim_duft;
                $priceListDetails->pldt_tppr = $request->pldt_tppr / $request->amim_duft;
                $priceListDetails->pldt_tpgp = $request->pldt_tpgp / $request->amim_duft;
                $priceListDetails->pldt_mrpp = $request->pldt_mrpp / $request->amim_duft;
                $priceListDetails->pldt_snme = $request->pldt_snme;
                $priceListDetails->amim_duft = $request->amim_duft;
                $priceListDetails->aemp_eusr = $this->currentUser->employee()->id;
                $priceListDetails->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->withInput()->with('danger', 'Item not Updated');
            }
        } else {
            return redirect()->back()->withInput()->with('danger', 'Access Limited');
        }

    }

    public function skuDelete($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $priceListDetails = PriceListDetails::on($this->db)->findorfail($id);
            $priceListDetails->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuAdd(Request $request, $id)
    {

        if ($this->userMenu->wsmu_updt) {

            $sku = SKU::on($this->db)->where(['id' => $request->amim_id])->first();
            $priceListDetails = PriceListDetails::on($this->db)->where(['plmt_id' => $id, 'amim_id' => $request->amim_id])->first();
            if ($priceListDetails == null) {
                $priceListDetails = new PriceListDetails();
                $priceListDetails->setConnection($this->db);
                $priceListDetails->amim_id = $request->amim_id;
                $priceListDetails->plmt_id = $id;
                $priceListDetails->pldt_dppr = $request->pldt_dppr / $request->amim_duft;
                $priceListDetails->pldt_dgpr = $request->pldt_dgpr / $request->amim_duft;
                $priceListDetails->pldt_tppr = $request->pldt_tppr / $request->amim_duft;
                $priceListDetails->pldt_tpgp = $request->pldt_tpgp / $request->amim_duft;
                $priceListDetails->pldt_mrpp = $request->pldt_mrpp / $request->amim_duft;
                $priceListDetails->pldt_snme = $request->pldt_snme;
                $priceListDetails->amim_duft = $request->amim_duft;
                $priceListDetails->amim_dunt = $sku->amim_dunt;
                $priceListDetails->amim_runt = $sku->amim_runt;
                $priceListDetails->cont_id = $this->currentUser->country()->id;
                $priceListDetails->lfcl_id = 1;
                $priceListDetails->aemp_iusr = $this->currentUser->employee()->id;
                $priceListDetails->aemp_eusr = $this->currentUser->employee()->id;
                $priceListDetails->save();
                $this->addCategorySqu($request,$id);
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                $priceListDetails->pldt_dppr = $request->pldt_dppr / $request->amim_duft;
                $priceListDetails->pldt_dgpr = $request->pldt_dgpr / $request->amim_duft;
                $priceListDetails->pldt_tppr = $request->pldt_tppr / $request->amim_duft;
                $priceListDetails->pldt_tpgp = $request->pldt_tpgp / $request->amim_duft;
                $priceListDetails->pldt_mrpp = $request->pldt_mrpp / $request->amim_duft;
                $priceListDetails->pldt_snme = $request->pldt_snme;
                $priceListDetails->amim_duft = $request->amim_duft;
                $priceListDetails->aemp_eusr = $this->currentUser->employee()->id;
                $priceListDetails->save();
                $this->addCategorySqu($request,$id);
                return redirect()->back()->withInput()->with('success', 'successfully Updated');
            }

        } else {
            return redirect()->back()->withInput()->with('danger', 'Access Limited');
        }

    }

    private function addCategorySqu($request,$id){

        $item_code=SKU::on($this->db)->where('id', $request->amim_id)->first(['amim_code']);
        $category=SalesGroupCategory::on($this->db)->where('id',$request->issc_id)->first(['issc_name']);
        $squExistOrNot=SalesGroupSku::on($this->db)->where(['issc_id' => $request->issc_id, 'item_code' => $item_code->amim_code])->first();
        if($squExistOrNot==null){

            $salesGroupSKU = new SalesGroupSku();
            $salesGroupSKU->setConnection($this->db);
            $salesGroupSKU->amim_id = $request->amim_id;
            $salesGroupSKU->item_code = $item_code->amim_code;
            $salesGroupSKU->category_name = $category->issc_name;
            $salesGroupSKU->issc_id = $request->issc_id;
            $salesGroupSKU->slgp_id = $id;
            $salesGroupSKU->sgit_moqt = 1;
            $salesGroupSKU->cont_id = $this->currentUser->country()->id;
            $salesGroupSKU->lfcl_id = 1;
            $salesGroupSKU->aemp_iusr = $this->currentUser->employee()->id;
            $salesGroupSKU->aemp_eusr = $this->currentUser->employee()->id;
            $salesGroupSKU->var = 1;
            $salesGroupSKU->attr1 = '';
            $salesGroupSKU->attr2 = '';
            $salesGroupSKU->attr3 = 0;
            $salesGroupSKU->attr4 = 0;
            $salesGroupSKU->save();

        }

    }

    public function skuUploadFormatGen(Request $request, $id)
    {

        if ($this->userMenu->wsmu_updt) {
            return Excel::download(PriceListDetails::create($id), 'price_list_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new PriceListDetails(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function getSubCategory($id){
        $sub_cats=DB::connection($this->db)->select("SELECT id,itsg_name FROM tm_itsg where id=$id");
        return $sub_cats;
    }

    public function getPriceList(Request $request){
        $cat_id=$request->cat_id;
        $sub_cat_id=$request->sub_cat_id;
        $item_code=$request->item_code;
        $site_code=$request->site_code;
        $sql='';
        if($site_code !=''){
            $sql="SELECT t3.id,t3.plmt_name as name,t3.plmt_code as code,t3.lfcl_id as status_id FROM `tl_stcm` t1
            INNER JOIN tm_site t2 ON t1.site_id=t2.id
            INNER JOIN tm_plmt t3 ON t1.plmt_id=t3.id
            WHERE t2.site_code='$site_code'
            GROUP BY t3.id,t3.plmt_name,t3.plmt_code";
        }
        else{
            $q1='';
            $q2='';
            $q3='';
            if($cat_id !=''){
                $q1.=" AND t3.itcg_id=$cat_id";
            }
            if($sub_cat_id !=''){
                $q2.=" AND t3.id=$sub_cat_id";
            }
            if($item_code !=''){
                $q3.=" AND t2.amim_code='$item_code'";
            }
            $sql="SELECT t4.id as id,t4.plmt_name as name,t4.plmt_code as code,t4.lfcl_id as status_id FROM `tm_pldt` t1
                    INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                    INNER JOIN tm_itsg t3 ON t2.itsg_id=t3.id
                    INNER JOIN tm_plmt t4 ON t1.plmt_id=t4.id
                    WHERE t4.lfcl_id=1 ". $q1. $q2 . $q3. "
                    GROUP BY t4.id,t4.plmt_name,t4.plmt_code";
            
        }
        $priceLists=DB::connection($this->db)->select(DB::raw($sql));
        $country_id = $this->currentUser->country()->id;
        $cats=DB::connection($this->db)->select("Select id,itcg_name FROM tm_itcg where lfcl_id=1 order by itcg_name asc");
        return view('master_data.PriceList.index',['cat_id'=>$cat_id,'sub_cat_id'=>$sub_cat_id,'item_code'=>$item_code,'site_code'=>$site_code])->with("priceLists", $priceLists)->with('permission', $this->userMenu)->with("cats", $cats);
    }
    public function getPriceListGloballyUpdateForm(){
        $aemp_id=Auth::user()->employee()->id;
        $acmp_list=DB::connection($this->db)->select("Select acmp_id id,acmp_name,acmp_code FROM user_group_permission 
        WHERE aemp_id={$aemp_id} GROUP BY acmp_id,acmp_name,acmp_code ORDER BY acmp_name ASC");
        return view('master_data.PriceList.price_update_globally',['acmp_list'=>$acmp_list]);
    }
    public function getPldtSlgp(Request $request){
        $aemp_id=Auth::user()->employee()->id;
        $acmp_id=$request->acmp_id;
        if($acmp_id>0){
            return DB::connection($this->db)->select("Select slgp_id id,slgp_name,slgp_code FROM user_group_permission 
            WHERE aemp_id={$aemp_id} AND acmp_id={$acmp_id} GROUP BY slgp_id,slgp_name,slgp_code ORDER BY acmp_id,slgp_name ASC");
        }
        else if($acmp_id==0){
            return DB::connection($this->db)->select("Select slgp_id id,slgp_name,slgp_code FROM user_group_permission 
            WHERE aemp_id={$aemp_id}  GROUP BY slgp_id,slgp_name,slgp_code ORDER BY acmp_id,slgp_name ASC");
        }
        else{
            return 0;
        }
        
    }
    public function getSelectedGroupPriceList(Request $request){
        $slgp_ids=$request->slgp_id;
        $cont_id=$this->currentUser->country()->id;
        $q1='';
        if($slgp_ids){
            $q1=" AND t1.id IN (".implode(',',$slgp_ids).")";
            return DB::connection($this->db)->select("SELECT t2.id, t2.plmt_name,t2.plmt_code FROM tm_slgp t1 
            INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id Where t1.cont_id={$cont_id} ".$q1." ");
        }
        return 0;
        
    }
    public function getSingleItem(Request $request){
        $amim_id=$this->getAmimId($request->amim_code);
        $amim_details=DB::connection($this->db)->select("SELECT t2.id,t2.amim_code,t2.amim_name,t1.pldt_tppr,concat(t3.plmt_code,'-',t3.plmt_name)plmt_name FROM `tm_pldt` t1 
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tm_plmt t3 ON t1.plmt_id=t3.id
                        WHERE t1.amim_id={$amim_id} limit 1");
        return $amim_details;
    }
    public function getPlmtAllItem(Request $request){
        $q='';
        $plmt_id=$request->plmt_id;
        if($plmt_id){
            $q1=" WHERE  t1.plmt_id IN (".implode(',',$plmt_id).")";
        }
        $amim_details=DB::connection($this->db)->select("SELECT t2.id,t2.amim_code,t2.amim_name,t1.pldt_tppr,concat(t3.plmt_code,'-',t3.plmt_name)plmt_name FROM `tm_pldt` t1 
                        INNER JOIN tm_amim t2 ON t1.amim_id=t2.id
                        INNER JOIN tm_plmt t3 ON t1.plmt_id=t3.id
                        ". $q1." ");
        return $amim_details;
    }
    public function updateItemPrice(Request $request){
        $plmt_id=$request->plmt_id;
        $item_details=$request->pldt_tppr;
        $check='';
        if($plmt_id){
            $q1=" WHERE  t1.plmt_id IN (".implode(',',$plmt_id).")";
            for($i=0;$i<count($item_details);$i++){
                $amim_id=$item_details[$i]['amim_id'];
                $pldt_tppr=$item_details[$i]['pldt_tppr'];
                //return $pldt_tppr;
                DB::connection($this->db)->select("
                        Update tm_pldt SET pldt_tppr={$pldt_tppr},pldt_tpgp={$pldt_tppr} WHERE  plmt_id IN (".implode(',',$plmt_id).")
                ");
            }
        }
        else{
            return -1;
        }
        return 1;
        
    }
    public function getAmimId($amim_code){
        $amim=SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
        return $amim?$amim->id:0;
    }
    

}
