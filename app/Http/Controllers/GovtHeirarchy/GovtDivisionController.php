<?php

namespace App\Http\Controllers\GovtHeirarchy;

use App\BusinessObject\CompanyEmployee;
use App\MasterData\Company;
use App\MasterData\GovtDivision;
use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class GovtDivisionController extends Controller
{
    private $access_key = 'tm_disn';
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
            $gDivision = GovtDivision::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.govt_division.index')->with("gDivision", $gDivision)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.govt_division.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

//`disn_name`, `disn_code`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`, `created_at`, `updated_at`, `var`,
// `attr1`, `attr2`, `attr3`, `attr4`
    public function store(Request $request)
    {
        $GovtDivision = new GovtDivision();
        $GovtDivision->setConnection($this->db);
        $GovtDivision->disn_name = $request->name;
        $GovtDivision->disn_code = $request->code;
        $GovtDivision->cont_id = $this->currentUser->employee()->cont_id;
        $GovtDivision->lfcl_id = 1;
        $GovtDivision->aemp_iusr = $this->currentUser->employee()->id;
        $GovtDivision->aemp_eusr = $this->currentUser->employee()->id;
        $GovtDivision->var = 1;
        $GovtDivision->attr1 = '';
        $GovtDivision->attr2 = '';
        $GovtDivision->attr3 = 0;
        $GovtDivision->attr4 = 0;
        $GovtDivision->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $gDivision = GovtDivision::on($this->db)->findorfail($id);
            return view('master_data.govt_division.show')->with('gDivision', $gDivision);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $gDivision = GovtDivision::on($this->db)->findorfail($id);
            return view('master_data.govt_division.edit')->with('gDivision', $gDivision);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $gDivision = GovtDivision::on($this->db)->findorfail($id);
        $gDivision->disn_name = $request->name;
        $gDivision->disn_code = $request->code;
        $gDivision->aemp_eusr = $this->currentUser->employee()->id;
        $gDivision->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $gDivision = GovtDivision::on($this->db)->findorfail($id);
            $gDivision->lfcl_id = $gDivision->lfcl_id == 1 ? 2 : 1;
            $gDivision->aemp_eusr = $this->currentUser->employee()->id;
            $gDivision->save();
            return redirect('/divisions');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }
}
