<?php

namespace App\Http\Controllers\SurveyItem;

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
use App\BusinessObject\SurveyItem;
use App\BusinessObject\ItemMaster;

use Excel;
class SurveyItemController extends Controller
{
    private $access_key = 'survey_items';
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
                $data = SurveyItem::on($this->db)->join('tm_aemp as t1', 't1.id', '=', 'tm_svit.aemp_iusr')
                        ->join('tm_aemp as t3','tm_svit.aemp_eusr','=','t3.id')                   
                        ->join('tm_lfcl as t4','tm_svit.lfcl_id','=','t4.id')                   
                    ->where(function ($query) use ($q) {
                    $query->where('tm_svit.amim_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_svit.class_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_svit.sv_sdat', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_svit.sv_edat', 'LIKE', '%' . $q . '%');
                })->paginate(500, array('tm_svit.id','tm_svit.amim_name','tm_svit.class_name','tm_svit.sv_sdat','tm_svit.sv_edat','t1.aemp_usnm AS iusr_id','t1.aemp_name AS iusr_name','t3.aemp_usnm AS eusr_id','t3.aemp_name AS eusr_name',
                    't4.lfcl_name'
                ))->setPath('');
            } else {
                $data = SurveyItem::on($this->db)->join('tm_aemp as t1', 't1.id', '=', 'tm_svit.aemp_iusr')
                        ->join('tm_aemp as t3','tm_svit.aemp_eusr','=','t3.id')                   
                        ->join('tm_lfcl as t4','tm_svit.lfcl_id','=','t4.id') 
                        ->paginate(500, array('tm_svit.id','tm_svit.amim_name','tm_svit.class_name','tm_svit.sv_sdat','tm_svit.sv_edat','t1.aemp_usnm AS iusr_id','t1.aemp_name AS iusr_name','t3.aemp_usnm AS eusr_id','t3.aemp_name AS eusr_name',
                        't4.lfcl_name'
                    ));
            }
            return view('SurveyItem.index')->with('data', $data)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }

    public function editSurveyItem($id){
        if ($this->userMenu->wsmu_updt) {
            $data=SurveyItem::on($this->db)->find($id);
            return view('SurveyItem.edit')->with('data', $data)->with('permission', $this->userMenu);
        }
        else {
            return view('theme.access_limit');
        }
    }
    public function inactiveSurveyItem(Request $request){
        $survey_items=$request->survey_items;
        DB::connection($this->db)->select("Update  tm_svit SET lfcl_id=2 WHERE id in (".implode(',',$survey_items).") ");
        return 1;
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $class_list=DB::connection($this->db)->select("Select id class_id,itcl_name class_name FROM tm_itcl WHERE lfcl_id=1 ORDER BY itcl_code ASC");
            return view('SurveyItem.create',['class_list'=>$class_list])->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function updateSurveyItem(Request $request, $id){
        if ($this->userMenu->wsmu_updt) {
            try{
                $sv_item=SurveyItem::on($this->db)->find($id);
                $sv_item->lfcl_id=$request->lfcl_id;
                $sv_item->sv_edat=$request->endDate;
                $sv_item->save();
                return redirect()->back()->with('success', 'Survey Item Updated Successfully');
            }
            catch(\Exception $e){
                return redirect()->back()->with('danger', 'Something Went Wrong!!!');
            }        
            
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function storeSurveyItem(Request $request)
    {
        $item_name = $request->item_code;
        $class_id = $request->class_id;
        $start_date = $request->sv_sdat;
        $end_date = $request->sv_edat;
        $class_id=$request->class_id;
        $class_data=DB::connection($this->db)->select("Select itcl_name FROM tm_itcl WHERE id ={$class_id}");
        $class_name=$class_data[0]->itcl_name;
        $start_date=$request->sv_sdat;
        $end_date=$request->sv_edat;
        if ($this->userMenu->wsmu_crat) {
            try{
                $npit=new SurveyItem();
                $npit->setConnection($this->db);
                $npit->class_id=$class_id;
                $npit->class_name=$class_name;
                $npit->amim_name=$item_name;
                $npit->sv_sdat=$start_date;
                $npit->sv_edat=$end_date;
                $npit->lfcl_id=1;
                $npit->aemp_iusr=$this->empId;
                $npit->aemp_eusr=$this->empId;
                $npit->save();
                return redirect()->back()->with('success', 'Survey Item Added Successfully');
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