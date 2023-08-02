<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    private $access_key = 'tm_sdvm';
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
        if ($this->userMenu->wsmu_vsbl) {
            $division = Division::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();;
            return view('master_data.division.index')->with("division", $division)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $userSaleGroups = DB::connection($this->db)->select("SELECT `id`,`acmp_name` FROM `tm_acmp` WHERE `lfcl_id`='1' AND cont_id=$country_id");
            return view('master_data.division.create')->with('salesGroups', $userSaleGroups);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        /*`id`, `sdvm_name`, `sdvm_code`, `acmp_id`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`,
        `updated_at`, `var`, `attr1`, `attr2`, `attr3`, `attr4`*/

        $division = new Division();
        $division->setConnection($this->db);
        $division->sdvm_name = $request->name;
        $division->sdvm_code = $request->code;
        $division->acmp_id = $request->company;
        $division->cont_id = $this->currentUser->country()->id;
        $division->lfcl_id = 1;
        $division->aemp_iusr = $this->currentUser->employee()->id;
        $division->aemp_eusr = $this->currentUser->employee()->id;
        $division->var = 1;
        $division->attr1 = '';
        $division->attr2 = '';
        $division->attr3 = 0;
        $division->attr4 = 0;
        $division->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $division = Division::on($this->db)->findorfail($id);
            return view('master_data.division.show')->with('division', $division);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $division = Division::on($this->db)->findorfail($id);
            return view('master_data.division.edit')->with('division', $division);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $division = Division::on($this->db)->findorfail($id);
        $division->sdvm_name = $request->name;
        $division->sdvm_code = $request->code;
        $division->aemp_eusr = $this->currentUser->employee()->id;
        $division->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $division = Division::on($this->db)->findorfail($id);
            $division->lfcl_id = $division->lfcl_id == 1 ? 2 : 1;
            $division->aemp_eusr = $this->currentUser->employee()->id;
            $division->save();
            return redirect('/division');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function filterDivision(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.name as name
FROM tbld_division AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.sales_group_id = t2.sales_group_id
WHERE t2.sales_group_id=$request->sales_group_id
GROUP BY t1.id,t1.name");
        return Response::json($divisions);
    }
}
