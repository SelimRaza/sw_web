<?php

namespace App\Http\Controllers\BlockOrder;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\DepotStock;
use App\BusinessObject\LoadLine;
use App\BusinessObject\LoadMaster;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGRV;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\BusinessObject\TripType;
use App\MasterData\Depot;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\TableRows;

class SpecialBudgetController extends Controller
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
                $spdm = SpecialBudgetMaster::on($this->db)->join('tm_aemp', 'tt_spbm.aemp_id', '=', 'tm_aemp.id')->where(function ($query) use ($q) {
                    $query->where('tm_aemp.aemp_usnm', 'LIKE', '%' . $q . '%')
                        ->orWhere('tm_aemp.aemp_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('tt_spbm.spbm_mnth', 'LIKE', '%' . $q . '%')
                        ->orWhere('tt_spbm.spbm_year', 'LIKE', '%' . $q . '%');
                })->where(['tm_aemp.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('tm_aemp.*', 'tt_spbm.*'))->setPath('');
            } else {
                $spdm = SpecialBudgetMaster::on($this->db)->join('tm_aemp', 'tt_spbm.aemp_id', '=', 'tm_aemp.id')->where(['tm_aemp.cont_id' => $this->currentUser->employee()->cont_id])->paginate(500, array('tm_aemp.*', 'tt_spbm.*'));
            }
            return view('blockOrder.budget.index')->with('spdm', $spdm)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            return view('blockOrder.budget.create')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        if (isset($request->user_name)) {
            $yearOnly = date('Y', strtotime($request->spbm_date));
            $monthOnly = date('m', strtotime($request->spbm_date));
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->user_name, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($employee) {
                $specialBudgetMaster = SpecialBudgetMaster::on($this->db)->where(['aemp_id' => $employee->id, 'spbm_mnth' => $monthOnly, 'spbm_year' => $yearOnly])->first();
                if ($specialBudgetMaster == null) {
                    DB::connection($this->db)->beginTransaction();
                    try {
                        $specialBudgetMaster = new SpecialBudgetMaster();
                        $specialBudgetMaster->setConnection($this->db);
                        $specialBudgetMaster->aemp_id = $employee->id;
                        $specialBudgetMaster->spbm_mnth = $monthOnly;
                        $specialBudgetMaster->spbm_year = $yearOnly;
                        $specialBudgetMaster->spbm_limt = 0;
                        $specialBudgetMaster->spbm_avil = 0;
                        $specialBudgetMaster->spbm_amnt = 0;
                        $specialBudgetMaster->lfcl_id = 1;
                        $specialBudgetMaster->cont_id = $this->currentUser->employee()->cont_id;
                        $specialBudgetMaster->aemp_iusr = $this->currentUser->employee()->id;
                        $specialBudgetMaster->aemp_eusr = $this->currentUser->employee()->id;
                        $specialBudgetMaster->save();
                        $specialBudgetMaster->save();
                        DB::connection($this->db)->commit();
                        return redirect('/specialBudget/' . $specialBudgetMaster->id . '/edit')->with('success', "successfully Created");
                       // return redirect()->back()->with('success', "successfully Created");
                    } catch
                    (\Exception $e) {
                        DB::connection($this->db)->rollback();
                        return redirect()->back()->with('danger', 'problem' . $e);
                        //throw $e;
                    }
                } else {
                    return redirect('/specialBudget/' . $specialBudgetMaster->id . '/edit')->with('danger', "Already Exists");
                   // return redirect()->back()->with('danger', "Already Exists");
                }
            }
        } else {
            return redirect()->back()->with('danger', "Please Enter Supervisor");
        }


    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $specialBudgetMaster = SpecialBudgetMaster::on($this->db)->findorfail($id);
            $specialBudgetLine = SpecialBudgetLine::on($this->db)->where(['spbm_id' => $id, 'cont_id' => $this->currentUser->employee()->cont_id])->get();
            return view('blockOrder.budget.show')->with('specialBudgetMaster', $specialBudgetMaster)->with('specialBudgetLine', $specialBudgetLine)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {

        if ($this->userMenu->wsmu_updt) {
            $specialBudgetMaster = SpecialBudgetMaster::on($this->db)->findorfail($id);
            $specialBudgetLine = SpecialBudgetLine::on($this->db)->where(['spbm_id' => $id, 'cont_id' => $this->currentUser->employee()->cont_id])->where('spbd_type', '!=', 'Approved')->get();
            return view('blockOrder.budget.edit')->with('specialBudgetMaster', $specialBudgetMaster)->with('specialBudgetLine', $specialBudgetLine)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $specialBudgetMaster = SpecialBudgetMaster::on($this->db)->findorfail($id);
            $specialBudgetLine = new SpecialBudgetLine();
            $specialBudgetLine->setConnection($this->db);
            $specialBudgetLine->spbm_id = $id;
            $specialBudgetLine->spbd_type = $request->trnt_id == '1' ? "In" : "Out";
            $specialBudgetLine->ordm_ornm = 0;
            $specialBudgetLine->spbd_amnt = $request->amount;
            $specialBudgetLine->trnt_id = $request->trnt_id;
            $specialBudgetLine->lfcl_id = 1;
            $specialBudgetLine->cont_id = $this->currentUser->employee()->cont_id;
            $specialBudgetLine->aemp_iusr = $this->currentUser->employee()->id;
            $specialBudgetLine->aemp_eusr = $this->currentUser->employee()->id;
            $specialBudgetLine->save();
            if ($request->trnt_id == 1) {
                $specialBudgetMaster->spbm_limt = $specialBudgetMaster->spbm_limt + $request->amount;
                $specialBudgetMaster->spbm_amnt = $specialBudgetMaster->spbm_amnt + $request->amount;
            } else {
                $specialBudgetMaster->spbm_limt = $specialBudgetMaster->spbm_limt - $request->amount;
                $specialBudgetMaster->spbm_amnt = $specialBudgetMaster->spbm_amnt - $request->amount;
            }
            $specialBudgetMaster->save();
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', "successfully Created");
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            return redirect()->back()->with('danger', 'problem' . $e);
            //throw $e;
        }
    }


}