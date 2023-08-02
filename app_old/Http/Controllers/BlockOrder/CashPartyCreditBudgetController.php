<?php

namespace App\Http\Controllers\BlockOrder;

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
use App\BusinessObject\CashPartyCreditBudget;
use App\BusinessObject\CashPartyCreditBudgetLine;
use Excel;
class CashPartyCreditBudgetController extends Controller
{
    private $access_key = 'cash_party_credit_budget';
    private $currentUser;
    private $userMenu;
    private $db;
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
                $spdm = CashPartyCreditBudget::on($this->db)->join('tm_aemp', 'tm_scbm.aemp_id', '=', 'tm_aemp.id')->where(function ($query) use ($q) {
                    $query->where('tm_aemp.aemp_usnm', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_aemp.aemp_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_scbm.spbm_mnth', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_scbm.spbm_year', 'LIKE', '%' . $q . '%');
                })->where(['tm_aemp.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('tm_aemp.*', 'tm_scbm.*'))->setPath('');
            } else {
                $mnth=date("m");
                $year=date("Y");
                $spdm = CashPartyCreditBudget::on($this->db)->join('tm_aemp', 'tm_scbm.aemp_id', '=', 'tm_aemp.id')
                        ->where(['tm_aemp.cont_id' => $this->currentUser->employee()->cont_id,'tm_scbm.spbm_mnth'=>$mnth,'tm_scbm.spbm_year'=>$year])
                        ->paginate(500, array('tm_aemp.*', 'tm_scbm.*'));
            }
            return view('blockOrder.CashCredit.index')->with('spdm', $spdm)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            return view('blockOrder.CashCredit.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        if (isset($request->aemp_usnm)) {
            $yearOnly = date('Y', strtotime($request->scbm_date));
            $monthOnly = date('m', strtotime($request->scbm_date));
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->aemp_usnm, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($employee) {
                $cashPartyCreditBudget = CashPartyCreditBudget::on($this->db)->where(['aemp_id' => $employee->id, 'spbm_mnth' => $monthOnly, 'spbm_year' => $yearOnly])->first();
                if ($cashPartyCreditBudget== null) {
                    if($request->type==1){
                        $cashPartyCreditBudget = new CashPartyCreditBudget();
                        $cashPartyCreditBudget->setConnection($this->db);
                        $cashPartyCreditBudget->aemp_id = $employee->id;
                        $cashPartyCreditBudget->spbm_mnth = $monthOnly;
                        $cashPartyCreditBudget->spbm_year = $yearOnly;
                        $cashPartyCreditBudget->spbm_limt =0;
                        $cashPartyCreditBudget->spbm_avil = 0;
                        $cashPartyCreditBudget->spbm_amnt =0;
                        $cashPartyCreditBudget->lfcl_id = 1;
                        $cashPartyCreditBudget->cont_id = $this->cont_id;
                        $cashPartyCreditBudget->aemp_iusr = $this->aemp_id;
                        $cashPartyCreditBudget->aemp_eusr = $this->aemp_id;
                        $cashPartyCreditBudget->save();
                        $line=new CashPartyCreditBudgetLine();
                        $line->setConnection($this->db);
                        $line->spbm_id=$cashPartyCreditBudget->id;
                        $line->ordm_ornm=0;
                        $line->trnt_id=1;
                        $cashPartyCreditBudget->spbm_limt=$request->limit;
                        $cashPartyCreditBudget->spbm_amnt=$request->limit;
                        $line->scbd_type="In";
                        $line->scbd_amnt=$request->limit;
                        $line->cont_id=$this->cont_id;
                        $line->aemp_iusr=$this->aemp_id;
                        $line->aemp_eusr=$this->aemp_id;
                        $line->lfcl_id=1;
                        $cashPartyCreditBudget->save();
                        $line->save();
                        
                       return 1;
                    }
                    else{
                        return 2;
                    }
                        
                } 
                else {
                    $value=1;
                    $line=new CashPartyCreditBudgetLine();
                    $line->setConnection($this->db);
                    $line->spbm_id=$cashPartyCreditBudget->id;
                    $line->ordm_ornm=0;
                    if($request->type==1){
                        $line->trnt_id=1;
                        $cashPartyCreditBudget->spbm_limt=$cashPartyCreditBudget->spbm_limt+$request->limit;
                        $cashPartyCreditBudget->spbm_amnt=$cashPartyCreditBudget->spbm_amnt+$request->limit;
                        $line->scbd_type="In";
                    }else{
                        $line->trnt_id=2;
                        if($cashPartyCreditBudget->spbm_amnt>=$request->limit){
                            $cashPartyCreditBudget->spbm_limt=$cashPartyCreditBudget->spbm_limt-$request->limit;
                            $cashPartyCreditBudget->spbm_amnt=$cashPartyCreditBudget->spbm_amnt-$request->limit;
                            $line->scbd_type="Out";
                        }
                        else{
                            $value=3;
                            return $value;
                        }
                        
                    }
                    $line->scbd_amnt=$request->limit;
                    $line->cont_id=$this->cont_id;
                    $line->aemp_iusr=$this->aemp_id;
                    $line->aemp_eusr=$this->aemp_id;
                    $line->lfcl_id=1;
                    $line->save();
                    $cashPartyCreditBudget->save();
                   return $value;
                }
            }
        } else {
            return redirect()->back()->with('danger', "Please Enter Supervisor");
        }


    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $specialBudgetMaster = CashPartyCreditBudget::on($this->db)->findorfail($id);
            $specialBudgetLine = CashPartyCreditBudgetLine::on($this->db)->where(['spbm_id' => $id])->get();
            return view('blockOrder.CashCredit.show')->with('specialBudgetMaster', $specialBudgetMaster)->with('specialBudgetLine', $specialBudgetLine)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function checkCreditBudget($aemp_usnm,$date){
        $yearOnly = date('Y', strtotime($date));
        $monthOnly = date('m', strtotime($date));
        $employee = Employee::on($this->db)->where(['aemp_usnm' => $aemp_usnm])->first();
        $aemp_id=$employee->id;
        $data= CashPartyCreditBudget::on($this->db)->join('tt_scbd', 'tm_scbm.id', '=', 'tt_scbd.spbm_id')
                ->select("tt_scbd.*")
                ->where(['tm_scbm.aemp_id' =>$aemp_id,'tm_scbm.spbm_mnth'=>$monthOnly,'tm_scbm.spbm_year'=>$yearOnly])
                ->get();
        return $data;
    }


    public function edit($id)
    {

        if ($this->userMenu->wsmu_updt) {
            $cashPartyCreditBudget = CashPartyCreditBudget::on($this->db)->findorfail($id);
            $line = CashPartyCreditBudgetLine::on($this->db)->where(['spbm_id' => $id])->where('scbd_type', '!=', 'Approved')->get();
            return view('blockOrder.CashCredit.edit')->with('cashPartyCreditBudget', $cashPartyCreditBudget)->with('line', $line)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $cashPartyCreditBudget = CashPartyCreditBudget::on($this->db)->findorfail($id);
            $line = new CashPartyCreditBudgetLine();
            $line->setConnection($this->db);
            $line->spbm_id = $id;
            $line->scbd_type = $request->trnt_id == '1' ? "In" : "Out";
            $line->ordm_ornm = 0;
            $line->scbd_amnt = $request->amount;
            $line->trnt_id = $request->trnt_id;
            $line->lfcl_id = 1;
            $line->cont_id = $this->currentUser->employee()->cont_id;
            $line->aemp_iusr = $this->currentUser->employee()->id;
            $line->aemp_eusr = $this->currentUser->employee()->id;
            $line->save();
            if ($request->trnt_id == 1) {
                $cashPartyCreditBudget->spbm_limt = $cashPartyCreditBudget->spbm_limt + $request->amount;
                $cashPartyCreditBudget->spbm_amnt = $cashPartyCreditBudget->spbm_amnt + $request->amount;
            } else {
                $cashPartyCreditBudget->spbm_limt = $cashPartyCreditBudget->spbm_limt - $request->amount;
                $cashPartyCreditBudget->spbm_amnt = $cashPartyCreditBudget->spbm_amnt - $request->amount;
            }
            $cashPartyCreditBudget->save();
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', "successfully Created");
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }
    }
    public function bulkUploadCreate(){
        return view('blockOrder.CashCredit.bulk_upload'); 
    }
    public function bulkUploadFormatDownload(){
        return Excel::download(new CashPartyCreditBudget(),'cash_party_credit_budget' . date("Y-m-d H:i:s") . '.xlsx' );
    }

    public function cashPartyCreditBulkUpload(Request $request){
        if ($request->hasFile('credit_file')) {
            DB::beginTransaction();
            try {
                Excel::import(new CashPartyCreditBudget(), $request->file('credit_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Data wrong ' . $e->getMessage());
            }
        }
        return back()->with('danger', ' File Not Found');
    }
    public function cashPartyCreditReport(){
        $sv_list=DB::connection($this->db)->select("Select id,aemp_usnm,aemp_name FROM tm_aemp WHERE role_id>=2 Order By role_id asc");
        $sales_p=DB::connection($this->db)->select("Select id,aemp_usnm,aemp_name FROM tm_aemp WHERE role_id=1 Order By aemp_name asc");
        $chnl=DB::connection($this->db)->select("Select id,chnl_name FROM tm_chnl order by chnl_name");
        return view('blockOrder.CashCredit.Report.cash_credit_report',['sr_list'=>$sv_list,'chnl'=>$chnl,'sales_p'=>$sales_p]);
    }
    public function getCashPartyCreditReport(Request $request){
        $mnth=$request->start_date;
        $aemp_id=$request->aemp_id;
        $site_code=$request->site_code;
        $chnl_id=$request->chnl_id;
        $sr_id=$request->sr_id;
        $yearOnly = date('Y', strtotime($mnth));
        $monthOnly = date('m', strtotime($mnth));
        $q1="";
        $q2="";
        if($aemp_id){
            $q1.=" AND t1.aemp_id=$aemp_id";
        }
        if($mnth){
            $q1.=" AND spbm_mnth='$monthOnly' AND spbm_year='$yearOnly' ";
        }
        if($site_code){
            $q1.=" AND t3.site_code='$site_code'";
        }
        if($chnl_id){
            $q1.=" AND t3.chnl_id=$chnl_id";
        }
        if($sr_id){
            $q1.=" AND t3.sr_id='$sr_id'";
        }
        
        $data=DB::connection($this->db)->select("SELECT 
        t2.aemp_usnm,t2.aemp_name,
        round(ifnull(ordm_amnt,0),4)ordm_amnt,
        round(ifnull(sreq_amnt,0),4)sreq_amnt,
        round(ifnull(sapr_amnt,0),4)sapr_amnt,
        ifnull(t3.site_code,'')site_code,
        ifnull(t3.site_name,'')site_name,
        t1.spbm_limt,
        concat(t1.spbm_mnth,'-',t1.spbm_year)mnth_year,
        round(t1.spbm_avil,4)spbm_avil,
        round(t1.spbm_amnt,4)spbm_amnt,
        ifnull(t3.chnl_name,'')chnl_name,
        ifnull(t3.sr_id,'')sr_id,ifnull(t3.sr_name,'')sr_name,
        t4.aemp_usnm sv_id,
        t4.aemp_name sv_name
        FROM `tm_scbm` t1               
        INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
        LEFT JOIN 
            (
                SELECT 
                sum(t1.ordm_amnt)ordm_amnt,
                sum(t1.sreq_amnt)sreq_amnt,
                sum(t1.sapr_amnt)sapr_amnt,
                t1.spbm_id,
                t2.site_code,
                t2.site_name,
                t4.id as chnl_id,
                t6.aemp_name sr_name,t6.aemp_usnm sr_id,
                t4.chnl_name
                FROM tl_cpcr t1 
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                INNER JOIN tm_scnl t3 ON t2.scnl_id=t3.id
                INNER JOIN tm_chnl t4 ON t3.chnl_id=t4.id
                INNER JOIN tt_ordm t5 ON t1.ordm_ornm=t5.ordm_ornm
                INNER JOIN tm_aemp t6 ON t5.aemp_id=t6.id
                WHERE t1.lfcl_id=1
                GROUP BY t1.spbm_id,
                t2.id,t2.site_name,t4.id,t4.chnl_name,
                t2.site_code,
                t6.aemp_name,t6.aemp_usnm
            )t3
        ON t1.id=t3.spbm_id
        INNER JOIN tm_aemp t4 ON t1.aemp_id=t4.id
        Where t1.lfcl_id=1 ".$q1."
                ");
        return $data;
    }

}