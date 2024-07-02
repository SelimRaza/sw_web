<?php

namespace App\Http\Controllers\NonProm;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */


use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\TableRows;
use App\BusinessObject\NonPromItem;
use App\BusinessObject\ItemMaster;

use Excel;
class NonPromItemController extends Controller
{
    private $access_key = 'non_prom_item';
    private $currentUser;
    private $userMenu;
    private $db;
    private $empId;
    private $cont_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->empId = Auth::user()->employee()->id;
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
        // dd($request);
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $data = NonPromItem::on($this->db)->join('tm_amim as t1', 't1.id', '=', 'tl_npit.amim_id')
                        ->join('tm_slgp as t3','tl_npit.slgp_id','=','t3.id')                   
                        ->join('tm_itsg as t4','t1.itsg_id','=','t4.id')                   
                    ->where(function ($query) use ($q) {
                    $query->where('t1.amim_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('t1.amim_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('t3.slgp_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('t3.slgp_code', 'LIKE', '%' . $q . '%')
                        ->orWhere('t4.itsg_name', 'LIKE', '%' . $q . '%');
                })->where(['t1.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('t1.amim_name','tl_npit.amim_id','t1.amim_code',
                        'tl_npit.slgp_id','t3.slgp_name','t3.slgp_code','t4.itsg_name','tl_npit.id'))->setPath('');
            } else {
                $data=NonPromItem::on($this->db)->join('tm_amim as t1', 't1.id', '=', 'tl_npit.amim_id')
                        ->join('tm_slgp as t3','tl_npit.slgp_id','=','t3.id')
                        ->join('tm_itsg as t4','t1.itsg_id','=','t4.id')                      
                        ->where(['t1.cont_id' => $this->currentUser->employee()->cont_id])
                        ->paginate(500, array('t1.amim_name','tl_npit.amim_id','t1.amim_code',
                        'tl_npit.slgp_id','t3.slgp_name','t3.slgp_code','t4.itsg_name','tl_npit.id'));
            }
            return view('NonPromItem.index')->with('data', $data)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $slgp_list=DB::connection($this->db)->select("Select slgp_id,slgp_name FROM user_group_permission WHERE aemp_id=$this->empId");
            return view('NonPromItem.create',['slgp_list'=>$slgp_list])->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $amim_code=$request->amim_code;
        $slgp_id=$request->sgp;
        if ($this->userMenu->wsmu_crat) {
            try{
                for($i=0;$i<count($amim_code);$i++){
                    $amim_id=$this->getAmimId($amim_code[$i]);
                    $sgp_id=$slgp_id[$i];
                    $npit=NonPromItem::on($this->db)->where(['amim_id'=>$amim_id,'slgp_id'=>$sgp_id])->first();
                    if(!$npit){
                        $npit=new NonPromItem();
                        $npit->setConnection($this->db);
                        $npit->amim_id=$amim_id;
                        $npit->slgp_id=$sgp_id;
                        $npit->cont_id=$this->cont_id;
                        $npit->lfcl_id=1;
                        $npit->aemp_iusr=$this->empId;
                        $npit->aemp_eusr=$this->empId;
                        $npit->save();
                    }

                }
                return redirect()->back()->with('success', 'NonSP Item Added Successfully');
            }
            catch(\Exception $e){
                return $e->getMessage();
                return redirect()->back()->with('danger', 'Something Went Wrong!!!');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }
    public function bulkUploadFormatDownload(){
        return Excel::download(new NonPromItem(),'non_prom_item_bulk' . date("Y-m-d H:i:s") . '.xlsx' );
    }

    public function nonPromItemBulkUpload(Request $request){
        if ($request->hasFile('npit_item')) {
            DB::beginTransaction();
            try {
                Excel::import(new NonPromItem(), $request->file('npit_item'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e->getMessage());
            }
        }
        return back()->with('danger', ' File Not Found');
    }


    public function removeItemFromNonPromlList(Request $request){
        $ids=$request->npit;
        DB::connection($this->db)->select("Delete FROM tl_npit WHERE id in (".implode(',',$ids).") ");
        return 1;
    }
    public function getAmimId($amim_code){
        $data=ItemMaster::on($this->db)->where(['amim_code'=>$amim_code])->first();
        return $data->id;
    }



}