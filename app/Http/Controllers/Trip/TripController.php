<?php

namespace App\Http\Controllers\Trip;

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

//newly added
use App\MasterData\Company;
use App\MasterData\Depot;
class TripController extends Controller
{
    private $access_key = 'maintain_trip';
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
    // Start Trip Code
    public function index(Request $request){
        $sq='';
        $dlrm_list=DB::connection($this->db)->select("SELECT id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC limit 500");
        $msps=Msp::on($this->db)->orderBy('id','DESC')->paginate(50);
        return view('Trip.index',['msps'=>$msps,'permission'=>$this->userMenu,'search_text'=>$sq,'dlrm_list'=>$dlrm_list]);
    }
    public function createTrip(){
        $empId = $this->currentUser->employee()->id;
        $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
        $msps = DB::connection($this->db)->select("SELECT id,mspm_name,mspm_code FROM tm_mspm WHERE lfcl_id=1 AND mspm_edat>=curdate()");
        $cats=DB::connection($this->db)->select("SELECT id,otcg_name,otcg_code FROM tm_otcg WHERE lfcl_id=1 Order By otcg_name ASC");
        $zones=DB::connection($this->db)->select("SELECT zone_id,zone_name FROM user_area_permission WHERE aemp_id=$empId ORDER BY zone_name ASC");

        $dlrm_list=DB::connection($this->db)->select("SELECT id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 ORDER BY dlrm_code ASC limit 500");
        $dm_list=DB::connection($this->db)->select("SELECT id,aemp_usnm,aemp_name FROM tm_aemp WHERE lfcl_id=1 AND role_id=10 ORDER BY aemp_name,aemp_usnm ASC limit 500");
        $vhcl_list=DB::connection($this->db)->select("SELECT id,vhcl_code,vhcl_name,concat(vhcl_code,'-',vhcl_name)vhcl_id FROM `tm_vhcl` WHERE lfcl_id=1 ORDER BY vhcl_name,vhcl_code ASC;");
        $acmp_list=DB::connection($this->db)->select("SELECT acmp_id id,acmp_code,acmp_name FROM `user_group_permission` WHERE aemp_id=$empId GROUP BY acmp_id ORDER BY acmp_code,acmp_name ASC;");
        return view('Trip.create',['dlrm_list'=>$dlrm_list,'dm_list'=>$dm_list,'vhcl_list'=>$vhcl_list,'acmp_list'=>$acmp_list])->with('acmp',$acmp)->with('msps',$msps)->with('cats',$cats)->with('zones',$zones);
    }
    public function storeTrip(Request $request){
        $request->validate([
            'tp_type' => 'required',
            'dlrm_id' => 'required',
            'tp_date' => 'required',
            'acmp_id' => 'required',
            'dm_id' => 'required',
        ]);
        $dm_id=$request->dm_id;
        $trip_exist=DB::connection($this->db)->select("SELECT TRIP_NO FROM dm_trip WHERE DM_ID='$dm_id' AND DM_ACTIVITY in (31,20)");
        if($trip_exist){
            return array(
                'trip_no'=>$trip_exist[0]->TRIP_NO,
                'flag' =>'Failed !',
                'icon'=>'error',
                'message'=>$dm_id.'- has an open Trip "'.$trip_exist[0]->TRIP_NO,
            );
        }
        $trip_no='T'.$request->dlrm_id.'-'.date('ymd').'-'.date('hisv');
        $trip_no=substr($trip_no,0,25);

        DB::connection($this->db)->table('dm_trip')->insert([
            'TRIP_NO' => $trip_no,
            'TRIP_DATE' => $request->tp_date,
            'DM_ID' => $request->dm_id,
            'STATUS' =>'N',
            'V_ID' => $request->vhcl_id,
            'SALES_TYPE' =>$request->tp_type,
            'DEPOT_ID' => $request->dlrm_id,
            'DM_ACTIVITY' =>31,
            'company_id' => $request->acmp_id
        ]);
        return array(
            'trip_no'=>$trip_no,
            'flag' =>'Trip Created !',
            'icon'=>'success',
            'message'=>$trip_no
        );

    }
    public function getDelarList($acmp_id){
        $acmp_info=DB::connection($this->db)->select("SELECT id FROM tm_acmp WHERE acmp_code='$acmp_id'");
        $acmp_auto_id=$acmp_info[0]->id??0;
        $dlrm_list=DB::connection($this->db)->select("SELECT id,dlrm_code,dlrm_name FROM tm_dlrm WHERE lfcl_id=1 AND acmp_id=$acmp_auto_id ORDER BY dlrm_code ASC limit 500");
        return $dlrm_list;
    }
    // Get Trip No
    public function getTripList(Request $request){
        $dm_id=$request->dm_id;
        $tp_date=$request->tp_date;
        $dlrm_id=$request->dlrm_id;
        $filter='';
        if($dm_id){
            $filter .="  AND t1.DM_ID='$dm_id'  ";
        }
        if($dlrm_id){
            $filter .="  AND t1.DEPOT_ID='$dlrm_id'  ";
        }
        if($tp_date){
            $filter .="  AND t1.TRIP_DATE='$tp_date'  ";
        }
        $trip_list=DB::connection($this->db)->select("SELECT t1.id,TRIP_NO,CONCAT(TRIP_NO,'(',DM_ID,'-',t2.aemp_name,')') TRIP_INFO FROM `dm_trip` t1 INNER JOIN tm_aemp t2 ON t1.DM_ID=t2.aemp_usnm WHERE DM_ACTIVITY=31 " .$filter .  " 
                    ORDER BY TRIP_NO");
        $first_trip=$trip_list[0]->TRIP_NO??0;
        $trip_details=DB::connection($this->db)->select("SELECT t3.aemp_usnm,t3.aemp_name,count(DISTINCT t2.ORDM_ORNM)total_invoice,ROUND(SUM(t2.ORDD_AMNT),2)ordm_amnt,ROUND(SUM(t2.INV_AMNT),2)invoice_amnt 
                        FROM `dm_trip` t1
                        INNER JOIN dm_trip_master t2 ON t1.TRIP_NO=t2.TRIP_NO
                        INNER JOIN tm_aemp t3 ON t2.AEMP_ID=t3.id
                        WHERE t1.TRIP_NO='$first_trip'
                        GROUP BY t3.aemp_usnm,t3.aemp_name
                        ;");

        return array(
            'trip_list'=>$trip_list,
            'trip_details'=>$trip_details,
        );
    }
    // Get DMList
    public function getDmList($acmp_code,$dlrm_code){
            $acmp=$this->getAcmp($acmp_code);
            $acmp_id=$acmp->id ??0;
            $dlrm=$this->getDlrm($dlrm_code);
            $dlrm_id=$dlrm->id ??0;
            $dm_list=DB::connection($this->db)->select("SELECT 
            t1.id aemp_id,t1.aemp_usnm,t1.aemp_name
            FROM tm_aemp t1
            INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id
            INNER JOIN tl_srdi t3 ON t1.id=t3.aemp_id
            WHERE t1.lfcl_id=1 AND t1.role_id=10 AND t2.acmp_id={$acmp_id} AND t3.dlrm_id={$dlrm_id} ORDER BY t1.aemp_usnm,t1.aemp_name ASC");
            return $dm_list;
    }
    // Get SRList
    public function getSRList(){
        $sr_list=DB::connection($this->db)->select("SELECT 
                t1.id aemp_id,t1.aemp_usnm,t1.aemp_name
                FROM tm_aemp t1
                INNER JOIN tl_srdi t2 ON t1.id=t2.aemp_id
                INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
                WHERE t1.lfcl_id=1 AND t1.role_id=1 AND t3.acmp_id=1
                AND t2.dlrm_id=53828");
        return $sr_list;
    }
    public function getInvoiceList(Request $request){
        $dlrm_code=$request->dlrm_id;
        $acmp_code=$request->acmp_id;
        $load_type=$request->load_type;
        $invoice_details_data=array();
        $filter='';
        $dlrm=$this->getDlrm($dlrm_code);
        $acmp=$this->getAcmp($acmp_code);
        $dlrm_id=$dlrm->id??0;
        $acmp_id=$acmp->id??0;
        $sr_list=$request->sr_list;
        if($load_type==1){
            $invoice_details_data=DB::connection($this->db)->select("SELECT 
                                    t1.id ordm_id,t1.ordm_ornm,t1.ordm_date,
                                    t3.id site_id,t3.site_code,t3.site_name,t1.aemp_id,t5.aemp_usnm,t5.aemp_name,
                                    t2.amim_id,t4.amim_code,t4.amim_name,t2.ordd_inty,t2.ordd_uprc,t2.ordd_opds,t2.ordd_spdc,t2.ordd_dfdc,
                                    t2.ordd_excs,t2.ordd_ovat,
                                    if(t2.ordd_smpl=1 AND t2.ordd_oamt=0,1,0) is_free
                                    FROM `tt_ordm` t1
                                    INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
                                    INNER JOIN tm_site t3 ON t1.site_id=t3.id
                                    INNER JOIN tm_amim t4 ON t2.amim_id=t4.id
                                    INNER JOIN tm_aemp t5 ON t1.aemp_id=t5.id
                                    WHERE t1.aemp_id in (". implode(',',$sr_list)  .") and t1.lfcl_id=1 AND t1.dlrm_id=53828 
                                    AND t1.ordm_date between curdate()-INTERVAL 10 day AND curdate()
                                    ORDER BY t3.id ASC");
        }
        else{

        }
        return $invoice_details_data;
    }
    private function getDlrm($dlrm_code){
        return Depot::on($this->db)->where('dlrm_code',$dlrm_code)->first();
    }
    private function getAcmp($acmp_code){
        return Company::on($this->db)->where('acmp_code',$acmp_code)->first();
    }

    // End Trip Code

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

    // All Msp Report
    public function getMspData(Request $request){
        
    }

    //Menu
    public function getMenu(){
        return view('Trip.Menu.index');
    }

    

}
