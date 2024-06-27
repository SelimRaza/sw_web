<?php

namespace App\Http\Controllers\MasterData;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\PriceList;
use App\MasterData\Company;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class CompanySiteMappingController extends Controller
{
    private $access_key = 'CompanySiteMappingController';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
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

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $companies = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $priceList = PriceList::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.CompanySiteMapping.create')->with('permission', $this->userMenu)->with('companies', $companies)->with('priceList', $priceList)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }
    public function uploadFormat(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new CompanySiteBalance(), 'company_site_upload_format' . $request->trgt_date . "_" . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function uploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new CompanySiteBalance(), $request->file('import_file'));
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

    public function store(Request $request)
    {
        $site = Site::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->where('site_code', '=', $request->site_code)->first();
        if ($site != null) {
            $companySiteBalance = CompanySiteBalance::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->where(['site_id' => $site->id, 'acmp_id' => $request->acmp_id,'slgp_id'=>$request->slgp_id])->first();
            //return $companySiteBalance;
            DB::connection($this->db)->beginTransaction();
            try {
                if ($companySiteBalance == null) {
                    $companySiteBalance = new CompanySiteBalance();
                    $companySiteBalance->setConnection($this->db);
                    $companySiteBalance->site_id = $site->id;
                    $companySiteBalance->plmt_id = $request->plmt_id;
                    $companySiteBalance->acmp_id = $request->acmp_id;
                    $companySiteBalance->slgp_id = $request->slgp_id;
                    $companySiteBalance->optp_id = $request->optp_id;
                    $companySiteBalance->stcm_limt = $request->stcm_limt;
                    $companySiteBalance->stcm_days = $request->stcm_days;
                    $companySiteBalance->stcm_isfx = isset($request->stcm_isfx) ? '0' : 1;
                    $companySiteBalance->stcm_ordm = 0;
                    $companySiteBalance->stcm_duea = 0;
                    $companySiteBalance->stcm_odue = 0;
                    $companySiteBalance->stcm_pnda = 0;
                    $companySiteBalance->stcm_cpnd = 0;
                    $companySiteBalance->cont_id = $this->currentUser->country()->id;
                    $companySiteBalance->lfcl_id = 1;
                    $companySiteBalance->aemp_iusr = $this->currentUser->employee()->id;
                    $companySiteBalance->aemp_eusr = $this->currentUser->employee()->id;
                    $companySiteBalance->save();
                } else {
                    $companySiteBalance->plmt_id = $request->plmt_id;
                    $companySiteBalance->optp_id = $request->optp_id;
                    $companySiteBalance->stcm_limt = $request->stcm_limt;
                    $companySiteBalance->stcm_days = $request->stcm_days;
                    $companySiteBalance->stcm_isfx = isset($request->stcm_isfx) ? '0' : 1;
                    $companySiteBalance->aemp_eusr = $this->currentUser->employee()->id;
                    $companySiteBalance->save();
                }

                DB::connection($this->db)->commit();
                return redirect()->back()->withInput()->with('success', 'successfully Created or updated');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                // throw $e;

                return back()->withInput()->with('danger', $e);// throw $e;
            }
        } else {
            return back()->withInput()->with('danger', "Wrong Site Code");// throw $e;
        }

    }

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $companies = Company::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $otcg_list=DB::connection($this->db)->select("Select id,otcg_name FROM tm_otcg WHERE lfcl_id=1 ORDER BY otcg_name ASC");
            $channel_list=DB::connection($this->db)->select("Select id,chnl_name,chnl_code FROM tm_chnl WHERE lfcl_id=1 ORDER BY chnl_name");
            return view('master_data.CompanySiteMapping.index',['otcg_list'=>$otcg_list,'channel_list'=>$channel_list])->with('permission', $this->userMenu)->with('companies', $companies)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function companySiteMappingFilter(Request $request)
    {
        $country_id = $this->currentUser->cont_id;
        $where = "1 and t1.cont_id=$country_id";

        if (isset($request->site_code)) {
            $where .= " AND t2.site_code = '$request->site_code'";
        }
        if (isset($request->acmp_id)) {
            $where .= " AND t1.acmp_id= '$request->acmp_id'";
        }
        if (isset($request->slgp_id)) {
            $where .= " AND t1.slgp_id= '$request->slgp_id'";
        }
        if (isset($request->otcg_id)) {
            $where .= " AND t2.otcg_id= '$request->otcg_id'";
        }
        if (isset($request->scnl_id)) {
            $where .= " AND t2.scnl_id= '$request->scnl_id'";
        }
        if (isset($request->chnl_id)) {
            $where .= " AND t6.chnl_id= '$request->chnl_id'";
        }
        if (isset($request->optp_id)) {
            $where .= " AND t1.optp_id= '$request->optp_id'";
        }
        $data = DB::connection($this->db)->select("SELECT
                t1.site_id,
                t3.acmp_name,
                t4.plmt_name,
                t2.site_code,
                t2.site_name,
                t5.optp_name                      ,
                t1.stcm_limt,
                t1.stcm_duea,
                t1.stcm_ordm,
                t6.scnl_name,
                t7.slgp_name,
                round(t1.stcm_pnda - t1.stcm_cpnd) AS pdc_amount,
                round(t1.stcm_cpnd)                AS non_verified,
                t1.stcm_days,
                t1.stcm_isfx,
                t1.stcm_odue                       
                FROM tl_stcm AS t1
                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                INNER JOIN tm_acmp AS t3 ON t1.acmp_id = t3.id
                INNER JOIN tm_plmt AS t4 ON t1.plmt_id = t4.id
                INNER JOIN tm_optp AS t5 ON t1.optp_id = t5.id
                INNER JOIN tm_scnl AS t6 ON t2.scnl_id=t6.id
                INNER JOIN tm_slgp AS t7 ON t1.slgp_id=t7.id
                where $where limit 25000");
        return $data;

    }
    public function getSubchannel($id){
        $data=DB::connection($this->db)->select("Select id,scnl_code,scnl_name FROM tm_scnl WHERE lfcl_id=1 Order By scnl_code");
        return $data;
    }
    public function editSiteCredit($id){
        $data=DB::connection($this->db)->select("
            SELECT
            t1.id stcm_id,
            t1.site_id,
            t3.acmp_name,
            t4.plmt_name ,
            t4.id plmt_id,
            t2.site_code,
            t5.optp_name,
            t5.id optp_id,
            t1.stcm_limt,
            t7.slgp_name,
            t1.stcm_days,
            t1.stcm_isfx                       
            FROM tl_stcm AS t1
            INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
            INNER JOIN tm_acmp AS t3 ON t1.acmp_id = t3.id
            INNER JOIN tm_plmt AS t4 ON t1.plmt_id = t4.id
            INNER JOIN tm_optp AS t5 ON t1.optp_id = t5.id
            INNER JOIN tm_slgp AS t7 ON t1.slgp_id=t7.id WHERE t1.site_id=$id");
        return view('master_data.CompanySiteMapping.credit_edit',['data'=>$data]);
    }
    public function updateSiteCredit(Request $request){
        $empId=$this->aemp_id;
        try{
            $stcm_id=$request->stcm_id;
            $stcm_days=$request->stcm_days;
            $stcm_limt=$request->stcm_limt;
            $stcm_isfx=$request->stcm_isfx;
            $optp_id=$request->optp_id;
            $update_query="";
            for($i=0;$i<count($stcm_id);$i++){
                $stcm=$stcm_id[$i];
                $fix=$stcm_isfx[$i];
                $limit=$stcm_limt[$i];
                $day=$stcm_days[$i];
                $p_type=$optp_id[$i];
                DB::connection($this->db)->select("Update tl_stcm SET stcm_isfx='$fix',stcm_days='$day',stcm_limt='$limit',optp_id='$p_type', aemp_eusr='$empId' WHERE id=$stcm");
            }
            // if($update_query){
            //     //DB::connection($this->db)->select($update_query);
            //     DB::connection($this->db)->select(DB::raw($update_query));
            // }
            return redirect()->back()->with('success', 'Successfully Updated');
        }catch(\Exception $e){
            return $e->getMessage();
            return redirect()->back()->with('danger', 'Something Went wrong !!!!');
        }
        
    }

}