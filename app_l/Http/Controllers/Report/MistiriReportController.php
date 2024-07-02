<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\DlearProfileAdd;
use App\BusinessObject\SalesGroup;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Excel;

class MistiriReportController extends Controller
{
    private $access_key = 'MistiriReportController';
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
    }

    public function summary(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                // $mistiridata = DlearProfileAdd::on($this->db)
                //  ->where('cont_id', '=', $this->currentUser->country()->id)->get()->paginate(5);
                $mistiridata = DB::connection($this->db)->table('tm_bizli AS t1')
                    ->join('tm_than AS t2', 't1.dlrp_prmt', '=', 't2.id')
                    ->join('tm_than AS t5', 't1.dlrp_prst', '=', 't5.id')
                    ->join('tm_dsct AS t6', 't1.dlrp_prsd', '=', 't6.id')
                    ->join('tm_dsct AS t7', 't1.dlrp_prmd', '=', 't7.id')
                    ->join('tm_mtyp AS t4', 't1.dlrp_mtyp', '=', 't4.id')
                    ->select('t1.id', 't1.dlrp_frno AS dlrp_frno', 't1.dlrp_name', 't1.dlrp_bldg',
                        't1.dlrp_edob', 't1.dlrp_nidn', 't1.dlrp_mobn','t1.dlrp_prad', 't1.dlrp_pmad',
                        't6.dsct_name AS Present_Dist_Name','t7.dsct_name AS Permanent_Dist_Name',
                        't5.than_name AS Present_Thana_Name','t2.than_name AS Permanent_Thana_Name','t1.date_issue','t1.lfcl_id', 't1.dlrp_aprv')->orderByDesc('t1.id')
                    ->where(function ($query) use ($q) {
                        $query->Where('t1.dlrp_frno', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.dlrp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.dlrp_mobn', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.dlrp_nidn', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.dlrp_bldg', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.dlrp_edob', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.date_issue', 'LIKE', '%' . $q . '%')
                            ->orWhere('t6.dsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t7.dsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.than_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t5.than_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.mtyp_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(50);

                //return view('report.mistiri.summary')->with('permission', $this->userMenu);
            } else {
                $mistiridata = DB::connection($this->db)->table('tm_bizli AS t1')
                    ->join('tm_than AS t2', 't1.dlrp_prmt', '=', 't2.id')
                    ->join('tm_than AS t5', 't1.dlrp_prst', '=', 't5.id')
                    ->join('tm_dsct AS t6', 't1.dlrp_prsd', '=', 't6.id')
                    ->join('tm_dsct AS t7', 't1.dlrp_prmd', '=', 't7.id')
                    ->join('tm_mtyp AS t4', 't1.dlrp_mtyp', '=', 't4.id')
                    ->select('t1.id', 't1.dlrp_frno AS dlrp_frno', 't1.dlrp_name', 't1.dlrp_bldg',
                        't1.dlrp_edob', 't1.dlrp_nidn', 't1.dlrp_mobn','t1.dlrp_prad', 't1.dlrp_pmad',
                        't6.dsct_name AS Present_Dist_Name','t7.dsct_name AS Permanent_Dist_Name',
                        't5.than_name AS Present_Thana_Name','t2.than_name AS Permanent_Thana_Name','t1.date_issue','t1.lfcl_id', 't1.dlrp_aprv')->orderByDesc('t1.id')
                    ->paginate(50);
            }
            return view('report.mistiri.summary')->with("mistiridata", $mistiridata)
                ->with('permission', $this->userMenu)->with('search_text', $q);

        } else {
            return view('theme.access_limit');
        }

    }

    public function dataExportMistiriData(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(DlearProfileAdd::create($request->start_date, $request->end_date), 'Mistiri_data_' . date("Y-m-d H:i:s") . '.xlsx');

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            //$noteType = NoteType::findorfail($id);
            $noteType = DlearProfileAdd::on($this->db)->findorfail($id);
            $noteType->dlrp_aprv = $noteType->dlrp_aprv == 1 ? 0 : 1;
            $noteType->dlrp_eusr = $this->currentUser->employee()->id;
            $noteType->save();
            return redirect('mistiri/report');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $noteType = DlearProfileAdd::on($this->db)->findorfail($id);
            // $noteType = NoteType::findorfail($id);
            return view('master_data.NoteType.show')->with('noteType', $noteType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            //  $noteType = NoteType::findorfail($id);
            $noteType = DlearProfileAdd::on($this->db)->findorfail($id);
            return view('master_data.NoteType.edit')->with('noteType', $noteType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}