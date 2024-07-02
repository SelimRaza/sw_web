<?php

namespace App\Http\Controllers\Depot;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\DepotStock;
use App\BusinessObject\MRRLine;
use App\BusinessObject\MRRMaster;
use App\BusinessObject\Trip;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripType;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Excel;

class MRRController extends Controller
{
    private $access_key = 'MRRController';
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

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $mrrLists = MRRMaster::on($this->db)->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%')
                        ->orWhere('code', 'LIKE', '%' . $q . '%')
                        ->orWhere('mrr_date', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->employee()->cont_id)->paginate(500)->setPath('');
            } else {
                $mrrLists = MRRMaster::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->paginate(500);
            }
            return view('Depot.MRR.index')->with('mrrLists', $mrrLists)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {

    }


    public function store(Request $value)
    {



    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $dataMrrLine = DB::connection($this->db)->select("SELECT
  t1.id     AS mrr_id,
  t2.amim_id AS sku_id,
  t3.amim_name   AS sku_name,
  t2.mrrl_qnty
FROM tt_mrrm AS t1
  INNER JOIN tt_mrrl AS t2 ON t1.id = t2.mrrm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.id = $id");
            return view('Depot.MRR.show')->with('dataMrrLine', $dataMrrLine)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {

            $mrrMaster = MRRMaster::on($this->db)->findorfail($id);
            $dataMrrLine = DB::connection($this->db)->select("SELECT
  t1.id     AS mrr_id,
  t2.amim_id AS sku_id,
  t3.amim_name   AS sku_name,
  t2.mrrl_qnty
FROM tt_mrrm AS t1
  INNER JOIN tt_mrrl AS t2 ON t1.id = t2.mrrm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.id = $id");
            return view('Depot.MRR.edit')->with('dataMrrLine', $dataMrrLine)->with('mrrMaster', $mrrMaster)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {

        DB::connection($this->db)->beginTransaction();
        try {
            $mrrMaster = MRRMaster::on($this->db)->findorfail($id);
            $mrrMaster->lfcl_id = 22;
            $mrrMaster->aemp_eusr = $this->currentUser->employee()->id;
            $mrrMaster->aemp_vusr = $this->currentUser->employee()->id;
            $mrrMaster->save();
            $mrrLine = MRRLine::on($this->db)->where('mrrm_id', '=', $id)->get();
            if ($mrrLine != null) {
                foreach ($mrrLine as $mrrLine1) {
                    $depotStock = DepotStock::on($this->db)->where(['dlrm_id' => $mrrMaster->dlrm_id, 'amim_id' => $mrrLine1->amim_id])->first();
                    if ($depotStock == null) {
                        $depotStock = new DepotStock();
                        $depotStock->setConnection($this->db);
                        $depotStock->dlrm_id = $mrrMaster->dlrm_id;
                        $depotStock->amim_id = $mrrLine1->amim_id;
                        $depotStock->dlst_qnty = $mrrLine1->mrrl_qnty;
                        $depotStock->cont_id = $mrrLine1->cont_id;
                        $depotStock->lfcl_id = 1;
                        $depotStock->aemp_iusr = $this->currentUser->employee()->id;
                        $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                        $depotStock->save();
                    } else {
                        $depotStock->dlst_qnty = $depotStock->dlst_qnty + $mrrLine1->mrrl_qnty;
                        $depotStock->aemp_eusr = $this->currentUser->employee()->id;
                        $depotStock->save();
                    }


                }
            }
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'Successfully Uploaded');
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger',  ' Data wrong ' . $e);

        }


    }


    public function destroy($id)
    {

    }


    public function mrrFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new MRRMaster(), 'mrr_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function mrrUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new MRRMaster(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


}