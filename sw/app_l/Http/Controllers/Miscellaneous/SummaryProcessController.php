<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Maatwebsite\Excel\Facades\Excel;
use App\DataExport\ExportPieChart;
use App\DataExport\ExportBarChart;

class SummaryProcessController extends Controller
{
    private $access_key = 'rpt_sm_process';
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
    
    public function index(){
       $this->db = Auth::user()->country()->cont_conn;
       $date =Carbon::now()->format('Y-m-d');
       $u=Auth::user()->email;
       $emp_id=DB::connection($this->db)->select("SELECT id,role_id FROM tm_aemp WHERE aemp_usnm ='$u'");
       $emid=$emp_id[0]->id;
       $emp_role=2;

       $data = [
            "t_outlet" => 1175,
            "total_visit" => 888
       ];

       $employee = DB::connection($this->db)->select("Select * from th_dhbd_5 Where dhbd_date = '2022-11-03' AND aemp_id=8");
       $pillar_data='';
       $gp_wise_class = '';


       if($this->userMenu->wsmu_vsbl==1){
            return view('miscellaneous.index', ['data' => $data]);
        }
        else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

   }

    public function exportPieChart(){
        return Excel::download(new ExportPieChart, 'chart-excel.xlsx');
   }

    public function exportBarChart(){
        return Excel::download(new ExportBarChart, 'chart-excel.xlsx');
   }

   
}
