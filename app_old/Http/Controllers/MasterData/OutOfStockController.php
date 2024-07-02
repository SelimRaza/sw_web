<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\OutOfStock;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class OutOfStockController extends Controller
{
    private $access_key = 'OutOfStockController';
    private $currentUser;
    private $userMenu;

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

    public function index(Request $request)
    {

        // if ($this->userMenu->wsmu_vsbl) {
        //     $q = '';
        //     if ($request->has('search_text')) {
        //         $q = request('search_text');
        //         $outofstockData = DB::connection($this->db)->table('tm_dpot')
        //             ->join('tt_outs', 'tm_dpot.id', '=', 'tt_outs.dpot_id')
        //             ->join('tm_amim', 'tm_amim.id', '=', 'tt_outs.amim_id')
        //             ->join('tm_itsg','tm_itsg.id','=','tm_amim.itsg_id')
        //             ->join('tm_itcg','tm_itcg.id','=','tm_itsg.itcg_id')
        //             ->join('tm_itcl','tm_itcl.id','=','tm_amim.itcl_id')
        //             ->select('tt_outs.id',
        //                     DB::connection($this->db)->raw('concat(tm_dpot.dpot_name, "-",tm_dpot.dpot_code) AS DealerName '),
        //                     'tm_amim.amim_code','tm_amim.amim_name',
        //                     'tm_itsg.itsg_name','tm_itcg.itcg_name','tm_itcl.itcl_name','tm_amim.amim_duft'
        //             )
        //             ->where(function ($query) use ($q) {
        //                 $query->where('tm_dpot.dpot_name', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_dpot.dpot_code', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_amim.amim_code', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_amim.amim_name', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_itsg.itsg_code', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_itsg.itsg_name', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_itcg.itcg_name', 'LIKE', '%' . $q . '%')
        //                     ->orWhere('tm_itcl.itcl_name', 'LIKE', '%' . $q . '%');
        //             })
        //             ->paginate(50);
        //     } else {
        //         $outofstockData = DB::connection($this->db)->table('tm_dpot')
        //             ->join('tt_outs', 'tm_dpot.id', '=', 'tt_outs.dpot_id')
        //             ->join('tm_amim', 'tm_amim.id', '=', 'tt_outs.amim_id')
        //             ->join('tm_itsg','tm_itsg.id','=','tm_amim.itsg_id')
        //             ->join('tm_itcg','tm_itcg.id','=','tm_itsg.itcg_id')
        //             ->join('tm_itcl','tm_itcl.id','=','tm_amim.itcl_id')
        //             ->select('tt_outs.id',
        //                 DB::connection($this->db)->raw('concat(tm_dpot.dpot_name, "-",tm_dpot.dpot_code) AS DealerName '),
        //                 'tm_amim.amim_code','tm_amim.amim_name',
        //                 'tm_itsg.itsg_name','tm_itcg.itcg_name','tm_itcl.itcl_name','tm_amim.amim_duft'

        //             )
        //             ->paginate(50);
        //         //  $locationData = LocationSection::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
        //     }
        //     return view('master_data.outofstock.index')->with("outofstockData", $outofstockData)->with('permission', $this->userMenu)->with('search_text', $q);
        // } else {
        //     return view('theme.access_limit');
        // }
        if ($this->userMenu->wsmu_vsbl){
            $dpot=DB::connection($this->db)->select("Select id,dpot_code,dpot_name FROM tm_dpot WHERE lfcl_id=1  ORDER BY dpot_code ASC");
            $itcg=DB::connection($this->db)->select("Select id,itcg_code,itcg_name FROM tm_itcg WHERE lfcl_id=1  ORDER BY itcg_code ASC");
            $itcl=DB::connection($this->db)->select("Select id,itcl_code,itcl_name FROM tm_itcl WHERE lfcl_id=1  ORDER BY itcl_code ASC");
            return view("master_data.outofstock.index",['dpot'=>$dpot,'itcg'=>$itcg,'itcl'=>$itcl,'permission'=>$this->userMenu]);
        }
        
        else {
            return view('theme.access_limit');
        }
    }
    public function getSubCategory($id){
        $data=DB::connection($this->db)->select("Select id,itsg_name,itsg_code FROM tm_itsg WHERE itcg_id=$id AND lfcl_id=1 
                ORDER BY itsg_code ASC");
        return $data;
    }
    public function getFilterOutOfStockDetails(Request $request){
        $dpot_id=$request->dpot_id;
        $itcg_id=$request->itcg_id;
        $itsg_id=$request->itsg_id;
        $itcl_id=$request->itcl_id;
        $amim_code=$request->item_code;
        $cont_id=Auth::user()->country()->id;
        $query="";
        if($dpot_id !=""){
            $query.=" AND t2.id=$dpot_id";
        }
        if($itcg_id !=""){
            $query.=" AND t5.id=$itcg_id";
        }
        if($itsg_id!=""){
            $query.=" AND t4.id=$itsg_id";
        }
        if($itcl_id !=""){
            $query.=" AND t6.id=$itcl_id";
        }
        if($amim_code !=""){
            $query.=" AND t3.amim_code=$amim_code";
        }
        $data=DB::connection($this->db)->select("SELECT 
                t1.id,
                t3.amim_code,
                t3.amim_name,
                concat(t2.dpot_code,'-',t2.dpot_name)dpot_name,
                concat(t5.itcg_code,'-',t5.itcg_name)itcg_name,
                concat(t4.itsg_code,'-',t4.itsg_name)itsg_name,
                concat(t6.itcl_code,'-',t6.itcl_name)itcl_name
                FROM `tt_outs` t1
                INNER JOIN tm_dpot t2 ON t1.dpot_id=t2.id
                INNER JOIN tm_amim t3 ON t1.amim_id=t3.id
                INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
                INNER JOIN tm_itcg t5 ON t4.itcg_id=t5.id
                INNER JOIN tm_itcl t6 ON t3.itcl_id=t6.id
                WHERE t1.cont_id=$cont_id " .$query . "
                ORDER BY t3.amim_code;");
        
        return array(
            'item'=>$data,
            'permission'=>$this->userMenu
        );
    }
    public function removeItemFromOutOfStock($id){
        $date=date('Y-m-d');
        $data=OutOfStock::on($this->db)->find($id);
        $data->delete();
        $aemp_id=$this->currentUser->employee()->id;
        DB::connection($this->db)->select("INSERT INTO tl_stock_out_log (sout_date,depot_id,amim_id,aemp_iusr,sout_type,source)
                                     VALUES('$date','$data->dpot_id','$data->amim_id','$aemp_id','DEL','W')");
        return 1;
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $DPOList = DB::connection($this->db)->select("SELECT `id`,CONCAT(`dpot_name`,'-',`dpot_code`)AS DPO_Name
            FROM `tm_dpot` WHERE `lfcl_id`='1' AND cont_id=$country_id");

            // $ItemMaster = ItemMaster::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $ItemList = DB::connection($this->db)->select("SELECT `id`,CONCAT(`amim_name`,'-',`amim_code`)AS Item_Name
            FROM `tm_amim` WHERE `lfcl_id`='1' AND cont_id=$country_id");


            return view('master_data.outofstock.create')->with('DPOList', $DPOList)->with('ItemList', $ItemList);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {

        /*$country_id = $this->currentUser->country()->id;
        $ItemList = DB::connection($this->db)->select("SELECT `id`
             FROM `tm_amim`
             WHERE `lfcl_id`='1' AND cont_id=$country_id AND amim_code=$request->amim_id");*/

        $DPOLIST = $request->dpot_id;

      //  dd($DPOLIST);

        if (isset($DPOLIST)) {
            $date=date('Y-m-d');
            $aemp_id=$this->currentUser->employee()->id;
            foreach ($DPOLIST as $dd) {

                $outofstock = new OutOfStock();
                $outofstock->setConnection($this->db);

                $outofstock->dpot_id = $dd;
                $outofstock->amim_id = $request->amim_id;

                $outofstock->lfcl_id = 1;
                $outofstock->cont_id = $this->currentUser->employee()->cont_id;
                $outofstock->aemp_iusr = $this->currentUser->employee()->id;
                $outofstock->aemp_eusr = $this->currentUser->employee()->id;

                $outofstock->save();
                if($this->currentUser->employee()->cont_id==3){
                    DB::connection($this->db)->select("INSERT INTO tl_stock_out_log (sout_date,depot_id,amim_id,aemp_iusr,sout_type,source)
                                     VALUES('$date','$dd','$request->amim_id','$aemp_id','ADD','W')");
                }
                

            }
        }
        return redirect()->back()->with('success', 'successfully Added');
        // foreach ($ItemList as $ItemList1) {

        // $outofstock->dpot_id = $request->dpot_id;  //single
        // $outofstock->amim_id = $request->amim_id;

        // $outofstock->amim_id = $ItemList1->id;
        // }
       // $outofstock->dpot_id = $request['dpot_id'];
        //$outofstock->amim_id = $request['amim_id'];


    }

    public function destroy($id)
    {
        $date=date('Y-m-d');
        $aemp_id=$this->currentUser->employee()->id;
        if ($this->userMenu->wsmu_delt) {
            $outofstock = OutOfStock::on($this->db)->findorfail($id);
            if($this->currentUser->employee()->cont_id==3 || $this->currentUser->employee()->cont_id==14){
                DB::connection($this->db)->select("INSERT INTO tl_stock_out_log (sout_date,depot_id,amim_id,aemp_iusr,sout_type,source)
                                    VALUES('$date','$outofstock->dpot_id','$outofstock->amim_id','$aemp_id','DEL','W')");
           }
            $outofstock->delete();
            return redirect()->back()->with('success', 'Deleted successful');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }



    public function bulkUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.outofstock.bulk',['permission'=>$this->userMenu]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function outOfStockFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new OutOfStock(), 'out_of_stock_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function outOfStockFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new OutOfStock(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function bulkDelete(Request $request){

        if ($this->userMenu->wsmu_delt) {

            $outofstocks = $request->out_stocks;
            DB::connection($this->db)->beginTransaction();

            try {

               $outofstock = OutOfStock::on($this->db)->findorfail($outofstocks);
               // $outofstock = DB::connection($this->db)->select("SELECT * FROM tt_outs WHERE id IN (".implode(',',$outofstocks)." ");
                $date=date('Y-m-d');
                $aemp_id=$this->currentUser->employee()->id;

                 $logs = [];
                if($this->currentUser->employee()->cont_id==3 || $this->currentUser->employee()->cont_id==14){
                    if($outofstocks){
                        foreach ($outofstock as $stock){
                            $logs[] = [
                                'sout_date' => $date,
                                'depot_id' => $stock->dpot_id,
                                'amim_id' => $stock->amim_id,
                                'aemp_iusr' => $aemp_id,
                                'sout_type' => 'DEL',
                                'source' => 'W'
                            ];

                        };

                        DB::connection($this->db)->table('tl_stock_out_log')->insert($logs);
                    }
                }

                OutOfStock::on($this->db)->whereIn('id', $outofstocks)->delete();

                DB::connection($this->db)->commit();

                return response(['success', 'Deleted successful']);

            }catch (\Exception $e)
            {
                DB::connection($this->db)->rollback();
                return $e->getMessage();
                return response('Deleted Failed', 401);
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

}
