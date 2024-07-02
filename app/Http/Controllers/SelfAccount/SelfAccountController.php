<?php

namespace App\Http\Controllers\SelfAccount;

use App\BusinessObject\CashAdjustment;
use App\BusinessObject\CashMovement;
use App\BusinessObject\CashMoveType;
use App\BusinessObject\CashSource;
use App\BusinessObject\CashType;
use App\BusinessObject\SelfAccounts;
use App\BusinessObject\SelfAccountsEmployee;
use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SelfAccountController extends Controller
{
    private $access_key = 'tbld_accounts';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
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
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $accounts = DB::table('tbld_accounts as t1')
                ->join('tbld_employee_account_mapping as t2', 't1.id', '=', 't2.account_id')
                ->select('t1.id as id', 't1.name as name', 't1.status_id as status_id')
                ->where(['t2.emp_id' => $emp_id, 't1.country_id' => $country_id])->get();
            //$accounts = SelfAccounts::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();;
            return view('self.account.index')->with("accounts", $accounts)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('self.account.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $selfAccounts = new SelfAccounts();
        $selfAccounts->name = $request->name;
        $selfAccounts->code = $request->code;
        $selfAccounts->country_id = $this->currentUser->employee()->cont_id;
        $selfAccounts->status_id = 1;
        $selfAccounts->created_by = $this->currentUser->employee()->id;
        $selfAccounts->updated_by = $this->currentUser->employee()->id;
        $selfAccounts->save();
        $salesGroupEmp = new SelfAccountsEmployee();
        $salesGroupEmp->emp_id = $this->currentUser->employee()->id;
        $salesGroupEmp->account_id = $selfAccounts->id;
        $salesGroupEmp->country_id = $this->currentUser->employee()->cont_id;
        $salesGroupEmp->created_by = $this->currentUser->employee()->id;
        $salesGroupEmp->updated_by = $this->currentUser->employee()->id;
        $salesGroupEmp->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            return view('self.account.show')->with('selfAccount', $selfAccount);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $selfAccount = SelfAccounts::findorfail($id);
            return view('self.account.edit')->with('selfAccount', $selfAccount);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $selfAccount = SelfAccounts::findorfail($id);
        $selfAccount->name = $request->name;
        $selfAccount->code = $request->code;
        $selfAccount->updated_by = $this->currentUser->employee()->id;
        $selfAccount->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $selfAccount = SelfAccounts::findorfail($id);
            $selfAccount->status_id = $selfAccount->status_id == 1 ? 2 : 1;
            $selfAccount->updated_by = $this->currentUser->employee()->id;
            $selfAccount->save();
            return redirect('/self_account');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function empEdit($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $employees = Employee::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $selfAccountsEmployees = SelfAccountsEmployee::where('account_id', '=', $id)->get();
            return view('self.account.employee')->with("employees", $employees)->with("selfAccountsEmployees", $selfAccountsEmployees)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function empAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $salesGroupEmp = SelfAccountsEmployee::where(['account_id' => $id, 'emp_id' => $request->emp_id])->first();
            if ($salesGroupEmp == null) {
                $salesGroupEmp = new SelfAccountsEmployee();
                $salesGroupEmp->emp_id = $request->emp_id;
                $salesGroupEmp->account_id = $id;
                $salesGroupEmp->country_id = $this->currentUser->employee()->cont_id;
                $salesGroupEmp->created_by = $this->currentUser->employee()->id;
                $salesGroupEmp->updated_by = $this->currentUser->employee()->id;
                $salesGroupEmp->save();
                return redirect()->back()->with('success', 'successfully Added');
            } else {
                return redirect()->back()->with('danger', 'Already exist');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function empDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $salesGroupEmp = SelfAccountsEmployee::findorfail($id);
            $salesGroupEmp->delete();
            return redirect()->back()->with('success', 'Employee Deleted');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public
    function cashMoveType($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $cashTypes = CashType::all();
            $cashMoveTypes = CashMoveType::where('account_id', '=', $id)->get();
            return view('self.account.cash_move_type')->with("cashTypes", $cashTypes)->with("cashMoveTypes", $cashMoveTypes)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function cashMoveTypeAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $cashMoveType = new CashMoveType();
            $cashMoveType->name = $request->name;
            $cashMoveType->code = $request->code;
            $cashMoveType->cash_type_id = $request->cash_type_id;
            $cashMoveType->account_id = $id;
            $cashMoveType->status_id = 1;
            $cashMoveType->country_id = $this->currentUser->employee()->cont_id;
            $cashMoveType->created_by = $this->currentUser->employee()->id;
            $cashMoveType->updated_by = $this->currentUser->employee()->id;
            $cashMoveType->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashMoveTypeDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $cashMoveTypes = CashMoveType::findorfail($id);
            $cashMoveTypes->status_id = $cashMoveTypes->status_id == 1 ? 2 : 1;
            $cashMoveTypes->updated_by = $this->currentUser->employee()->id;
            $cashMoveTypes->save();
            return redirect()->back()->with('success', 'Success');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public
    function cashSource($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $cashSources = CashSource::where('account_id', '=', $id)->get();
            return view('self.account.cash_source')->with("cashSources", $cashSources)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function cashSourceAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            $cashSource = new CashSource();
            $cashSource->name = $request->name;
            $cashSource->code = $request->code;
            $cashSource->amount = 0;
            $cashSource->account_id = $id;
            $cashSource->status_id = 1;
            $cashSource->country_id = $this->currentUser->employee()->cont_id;
            $cashSource->created_by = $this->currentUser->employee()->id;
            $cashSource->updated_by = $this->currentUser->employee()->id;
            $cashSource->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashSourceDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $cashSource = CashSource::findorfail($id);
            $cashSource->status_id = $cashSource->status_id == 1 ? 2 : 1;
            $cashSource->updated_by = $this->currentUser->employee()->id;
            $cashSource->save();
            return redirect()->back()->with('success', 'Success');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public
    function cashReceive($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $cashMovements = CashMovement::where(['account_id' => $id, 'cash_type_id' => 1, 'status_id' => 1])->get();
            $cashMoveTypes = CashMoveType::where(['account_id' => $id, 'status_id' => 1])->get();
            $cashSources = CashSource::where(['account_id' => $id, 'status_id' => 1])->get();
            return view('self.account.cash_receive')->with("cashSources", $cashSources)->with("cashMovements", $cashMovements)->with("cashMoveTypes", $cashMoveTypes)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function cashReceiveAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            DB::beginTransaction();
            try {
                $cashMovement = new CashMovement();
                $cashMovement->date = $request->date;
                $cashMovement->details = $request->details;
                $cashMovement->amount = $request->amount;
                $cashMovement->cash_move_type_id = $request->cash_move_type_id;
                $cashMovement->cash_source_id = $request->cash_source_id;
                $cashMovement->account_id = $id;
                $cashMovement->cash_type_id = 1;
                $cashMovement->status_id = 1;
                $cashMovement->country_id = $this->currentUser->employee()->cont_id;
                $cashMovement->created_by = $this->currentUser->employee()->id;
                $cashMovement->updated_by = $this->currentUser->employee()->id;
                $cashMovement->save();

                $cashSource = CashSource::findorfail($request->cash_source_id);
                $cashSource->amount = $cashSource->amount + $request->amount;
                $cashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashReceiveDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            DB::beginTransaction();
            try {
                $cashMovement = CashMovement::findorfail($id);
                $cashMovement->status_id = $cashMovement->status_id == 1 ? 2 : 1;
                $cashMovement->updated_by = $this->currentUser->employee()->id;
                $cashMovement->save();
                $cashSource = CashSource::findorfail($cashMovement->cash_source_id);
                $cashSource->amount = $cashSource->amount - $cashMovement->amount;
                $cashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashOut($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $cashMovements = CashMovement::where(['account_id' => $id, 'cash_type_id' => 2, 'status_id' => 1])->get();
            $cashMoveTypes = CashMoveType::where(['account_id' => $id, 'status_id' => 1])->get();
            $cashSources = CashSource::where(['account_id' => $id, 'status_id' => 1])->get();
            return view('self.account.cash_out')->with("cashSources", $cashSources)->with("cashMovements", $cashMovements)->with("cashMoveTypes", $cashMoveTypes)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function cashOutAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            DB::beginTransaction();
            try {
                $cashMovement = new CashMovement();
                $cashMovement->date = $request->date;
                $cashMovement->details = $request->details;
                $cashMovement->amount = $request->amount;
                $cashMovement->cash_move_type_id = $request->cash_move_type_id;
                $cashMovement->cash_source_id = $request->cash_source_id;
                $cashMovement->account_id = $id;
                $cashMovement->cash_type_id = 2;
                $cashMovement->status_id = 1;
                $cashMovement->country_id = $this->currentUser->employee()->cont_id;
                $cashMovement->created_by = $this->currentUser->employee()->id;
                $cashMovement->updated_by = $this->currentUser->employee()->id;
                $cashMovement->save();

                $cashSource = CashSource::findorfail($request->cash_source_id);
                $cashSource->amount = $cashSource->amount - $request->amount;
                $cashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashOutDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            DB::beginTransaction();
            try {
                $cashMovement = CashMovement::findorfail($id);
                $cashMovement->status_id = $cashMovement->status_id == 1 ? 2 : 1;
                $cashMovement->updated_by = $this->currentUser->employee()->id;
                $cashMovement->save();
                $cashSource = CashSource::findorfail($cashMovement->cash_source_id);
                $cashSource->amount = $cashSource->amount + $cashMovement->amount;
                $cashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }


    public
    function cashMove($id)
    {
        if ($this->userMenu->wsmu_read) {
            $selfAccount = SelfAccounts::findorfail($id);
            $cashAdjustments = CashAdjustment::where(['account_id' => $id, 'status_id' => 1])->get();
            $cashMoveTypes = CashMoveType::where(['account_id' => $id, 'status_id' => 1])->get();
            $cashSources = CashSource::where(['account_id' => $id, 'status_id' => 1])->get();
            return view('self.account.cash_move')->with("cashSources", $cashSources)->with("cashMovements", $cashAdjustments)->with("cashMoveTypes", $cashMoveTypes)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function cashMoveAdd(Request $request, $id)
    {
        if ($this->userMenu->wsmu_updt) {
            DB::beginTransaction();
            try {
                $cashAdjustment = new CashAdjustment();
                $cashAdjustment->date = $request->date;
                $cashAdjustment->details = $request->details;
                $cashAdjustment->amount = $request->amount;
                $cashAdjustment->from_cash_source_id = $request->from_cash_source_id;
                $cashAdjustment->to_cash_source_id = $request->to_cash_source_id;
                $cashAdjustment->account_id = $id;
                $cashAdjustment->status_id = 1;
                $cashAdjustment->country_id = $this->currentUser->employee()->cont_id;
                $cashAdjustment->created_by = $this->currentUser->employee()->id;
                $cashAdjustment->updated_by = $this->currentUser->employee()->id;
                $cashAdjustment->save();

                $fromCashSource = CashSource::findorfail($request->from_cash_source_id);
                $fromCashSource->amount = $fromCashSource->amount - $request->amount;
                $fromCashSource->save();
                $toCashSource = CashSource::findorfail($request->to_cash_source_id);
                $toCashSource->amount = $toCashSource->amount + $request->amount;
                $toCashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public
    function cashMoveDelete($id)
    {
        if ($this->userMenu->wsmu_delt) {
            DB::beginTransaction();
            try {
                $cashAdjustment = CashAdjustment::findorfail($id);
                $cashAdjustment->status_id = $cashAdjustment->status_id == 1 ? 2 : 1;
                $cashAdjustment->updated_by = $this->currentUser->employee()->id;
                $cashAdjustment->save();
                $fromCashSource = CashSource::findorfail($cashAdjustment->from_cash_source_id);
                $fromCashSource->amount = $fromCashSource->amount + $cashAdjustment->amount;
                $fromCashSource->save();
                $toCashSource = CashSource::findorfail($cashAdjustment->to_cash_source_id);
                $toCashSource->amount = $toCashSource->amount - $cashAdjustment->amount;
                $toCashSource->save();
                DB::commit();
                return redirect()->back()->with('success', 'Success');
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with('danger', ' ON Line ' . $i);
                return redirect()->back()->with('danger', 'Try Again');
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    function cashHistory(Request $request, $id)
    {
        if ($this->userMenu->wsmu_read) {
            $q = '';
            $selfAccount = SelfAccounts::findorfail($id);
            $cashMoveTypes = CashMoveType::where('account_id', '=', $id)->get();
            $whereCondition = array('account_id' => $id, 'status_id' => 1);
            $cashSources = CashSource::where('account_id', '=', $id)->get();
            $move_type_id = request('move_type_id') == '0' || request('move_type_id') == null ? '0' : request('move_type_id');
            $source_id = request('source_id') == '0' || request('source_id') == null ? '0' : request('source_id');
            $start_date = request('start_date') == '' ? date('Y-m-d') : request('start_date');
            $end_date = request('end_date') == '' ? date('Y-m-d') : request('end_date');
            if (($request->has('start_date') && $request->has('end_date')) || $request->has('move_type_id')) {
                if ($move_type_id != '0') {
                    $whereCondition = array_merge($whereCondition, array("cash_move_type_id" => $move_type_id));
                }
                if ($source_id != '0') {
                    $whereCondition = array_merge($whereCondition, array("cash_source_id" => $source_id));
                }
                $cashMovements = CashMovement::where(
                    $whereCondition
                )->whereBetween('date', [$start_date, $end_date])->paginate(500);
            } else {

                $cashMovements = CashMovement::where(['account_id' => $id, 'status_id' => 1])->paginate(500);

            }
            return view('self.account.cash_history')->with("cashSources", $cashSources)->with("cashMovements", $cashMovements)->with("cashMoveTypes", $cashMoveTypes)->with("selfAccount", $selfAccount)->with('permission', $this->userMenu)->with('start_date', $start_date)->with('end_date', $end_date)->with('move_type_id', $move_type_id)->with('source_id', $source_id);

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
