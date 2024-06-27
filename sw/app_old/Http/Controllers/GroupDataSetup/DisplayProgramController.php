<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\BusinessObject\DisplayProgram;
use App\BusinessObject\DisplayProgramCondition;
use App\BusinessObject\DisplayProgramEmployee;
use App\BusinessObject\DisplayProgramSite;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Site;
use App\MasterData\SKU;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DisplayProgramController extends Controller
{
    private $access_key = 'tbld_display_program';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }
    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $displayPrograms = DisplayProgram::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.display_program.index')->with("displayPrograms", $displayPrograms)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.display_program.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $displayProgram = new DisplayProgram();
        $displayProgram->name = $request->name;
        $displayProgram->code = $request->code;
        $displayProgram->max_width = $request->max_width;
        $displayProgram->max_height = $request->max_height;
        $displayProgram->min_width = $request->min_width;
        $displayProgram->min_height = $request->min_height;
        $displayProgram->start_date = $request->start_date;
        $displayProgram->end_date = $request->end_date;
        $displayProgram->display_string = '';
        $displayProgram->condition_string = '';
        $displayProgram->offer_string = '';
        $displayProgram->status_id = 1;
        $displayProgram->country_id = $this->currentUser->employee()->cont_id;
        $displayProgram->created_by = $this->currentUser->employee()->id;
        $displayProgram->updated_by = $this->currentUser->employee()->id;
        $displayProgram->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $displayProgram = DisplayProgram::findorfail($id);
            $displayProgramConditions = DisplayProgramCondition::where('program_id', '=', $id)->get();
            return view('master_data.display_program.show')->with('displayProgram', $displayProgram)->with('displayProgramConditions', $displayProgramConditions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $displayProgram = DisplayProgram::findorfail($id);
            return view('master_data.display_program.edit')->with('displayProgram', $displayProgram);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $displayProgram = DisplayProgram::findorfail($id);
        $displayProgram->name = $request->name;
        $displayProgram->code = $request->code;
        $displayProgram->max_width = $request->max_width;
        $displayProgram->max_height = $request->max_height;
        $displayProgram->min_width = $request->min_width;
        $displayProgram->min_height = $request->min_height;
        $displayProgram->start_date = $request->start_date;
        $displayProgram->end_date = $request->end_date;
        $displayProgram->updated_by = $this->currentUser->employee()->id;
        $displayProgram->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $displayProgram = DisplayProgram::findorfail($id);
            $displayProgram->status_id = $displayProgram->status_id == 1 ? 2 : 1;
            $displayProgram->updated_by = $this->currentUser->employee()->id;
            $displayProgram->save();
            return redirect('/display-program');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function condition($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $displayProgram = DisplayProgram::findorfail($id);
            $displayProgramConditions = DisplayProgramCondition::where('program_id', '=', $id)->get();
            $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.display_program.display_program_sku')->with("skus", $skus)->with("displayProgramConditions", $displayProgramConditions)->with("displayProgram", $displayProgram);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function skuDelete($id)
    {
        $displayProgramCondition = DisplayProgramCondition::findorfail($id);
        $displayProgramCondition->delete();
        return redirect()->back()->with('success', 'SKU Deleted');

    }


    public function displaySku(Request $request, $id)
    {
        $displayProgramConditionSku = DisplayProgramCondition::where(['program_id' => $id, 'sku_id' => $request->display_sku_id, 'type' => 1])->first();
        if ($displayProgramConditionSku == null) {
            $displayProgramConditionSku = new DisplayProgramCondition();
            $displayProgramConditionSku->sku_id = $request->display_sku_id;
            $displayProgramConditionSku->program_id = $id;
            $displayProgramConditionSku->qty = $request->display_sku_qty == null ? 0 : $request->display_sku_qty;
            $displayProgramConditionSku->amount = $request->display_sku_amount == null ? 0 : $request->display_sku_amount;
            $displayProgramConditionSku->type = 1;
            $displayProgramConditionSku->country_id = $this->currentUser->employee()->cont_id;
            $displayProgramConditionSku->created_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->updated_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }

    }

    public function conditionSku(Request $request, $id)
    {
        $displayProgramConditionSku = DisplayProgramCondition::where(['program_id' => $id, 'sku_id' => $request->condition_sku_id, 'type' => 2])->first();
        if ($displayProgramConditionSku == null) {
            $displayProgramConditionSku = new DisplayProgramCondition();
            $displayProgramConditionSku->sku_id = $request->condition_sku_id;
            $displayProgramConditionSku->program_id = $id;
            $displayProgramConditionSku->qty = $request->condition_sku_qty == null ? 0 : $request->condition_sku_qty;
            $displayProgramConditionSku->amount = $request->condition_sku_amount == null ? 0 : $request->condition_sku_amount;
            $displayProgramConditionSku->type = 2;
            $displayProgramConditionSku->country_id = $this->currentUser->employee()->cont_id;
            $displayProgramConditionSku->created_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->updated_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }

    }

    public function offerSku(Request $request, $id)
    {
        $displayProgramConditionSku = DisplayProgramCondition::where(['program_id' => $id, 'sku_id' => $request->offer_sku_id, 'type' => 3])->first();
        if ($displayProgramConditionSku == null) {
            $displayProgramConditionSku = new DisplayProgramCondition();
            $displayProgramConditionSku->sku_id = $request->offer_sku_id;
            $displayProgramConditionSku->program_id = $id;
            $displayProgramConditionSku->qty = $request->offer_sku_qty == null ? 0 : $request->offer_sku_qty;
            $displayProgramConditionSku->amount = $request->offer_sku_amount == null ? 0 : $request->offer_sku_amount;
            $displayProgramConditionSku->type = 3;
            $displayProgramConditionSku->country_id = $this->currentUser->employee()->cont_id;
            $displayProgramConditionSku->created_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->updated_by = $this->currentUser->employee()->id;
            $displayProgramConditionSku->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }

    }

    public function empAssign($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $displayProgram = DisplayProgram::findorfail($id);
            $displayProgramEmployees = DisplayProgramEmployee::where('program_id', '=', $id)->get();
            $employees = Employee::where(['cont_id' => $this->currentUser->employee()->cont_id, 'role_id' => 3])->get();
            return view('master_data.display_program.display_program_employee')->with("employees", $employees)->with("displayProgramEmployees", $displayProgramEmployees)->with("displayProgram", $displayProgram);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function empAssignAdd(Request $request, $id)
    {
        $displayProgramEmployee = DisplayProgramEmployee::where(['program_id' => $id, 'emp_id' => $request->emp_id])->first();
        if ($displayProgramEmployee == null) {
            $displayProgramEmployee = new DisplayProgramEmployee();
            $displayProgramEmployee->emp_id = $request->emp_id;
            $displayProgramEmployee->program_id = $id;
            $displayProgramEmployee->qty = $request->qty;
            $displayProgramEmployee->country_id = $this->currentUser->employee()->cont_id;
            $displayProgramEmployee->created_by = $this->currentUser->employee()->id;
            $displayProgramEmployee->updated_by = $this->currentUser->employee()->id;
            $displayProgramEmployee->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }
    }

    public function empDelete($id)
    {
        $displayProgramEmployee = DisplayProgramEmployee::findorfail($id);
        $displayProgramEmployee->delete();
        return redirect()->back()->with('success', 'Employee Deleted');

    }

    public function siteAssign($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $displayProgram = DisplayProgram::findorfail($id);
            $displayProgramSites = DisplayProgramSite::where('program_id', '=', $id)->get();
            $sites = Site::where(['cont_id' => $this->currentUser->employee()->cont_id])->get();
            return view('master_data.display_program.display_program_site')->with("sites", $sites)->with("displayProgramSites", $displayProgramSites)->with("displayProgram", $displayProgram);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function siteAssignAdd(Request $request, $id)
    {
        $displayProgramSite = DisplayProgramSite::where(['program_id' => $id, 'site_id' => $request->site_id])->first();
        if ($displayProgramSite == null) {
            $displayProgramSite = new DisplayProgramSite();
            $displayProgramSite->program_id = $id;
            $displayProgramSite->site_id = $request->site_id;
            $displayProgramSite->width = $request->width;
            $displayProgramSite->height = $request->height;
            $displayProgramSite->qty = $request->block_count;
            $displayProgramSite->start_date = date('Y-m-d');
            $displayProgramSite->status_id = 1;
            $displayProgramSite->country_id = $this->currentUser->employee()->cont_id;
            $displayProgramSite->created_by = $this->currentUser->employee()->id;
            $displayProgramSite->updated_by = $this->currentUser->employee()->id;
            $displayProgramSite->save();
            return redirect()->back()->with('success', 'successfully Added');
        } else {
            return redirect()->back()->with('danger', 'Already exist');
        }
    }

    public function siteInactive($id)
    {
        $displayProgramSite = DisplayProgramSite::findorfail($id);
        $displayProgramSite->status_id = $displayProgramSite->status_id == 1 ? 2 : 1;
        $displayProgramSite->updated_by = $this->currentUser->employee()->id;
        $displayProgramSite->save();
        return redirect()->back()->with('success', 'successfully Updated');

    }

}
