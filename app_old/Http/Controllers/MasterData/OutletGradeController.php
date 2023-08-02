<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Channel;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
use App\MasterData\OutletCategory;
use App\MasterData\OutletGrade;
use App\MasterData\Region;
use App\MasterData\SubChannel;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class OutletGradeController extends Controller
{
    private $access_key = 'tm_otcg';
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
            $outletGrades = OutletGrade::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.outlet_grade.index')->with("outletGrades", $outletGrades)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.outlet_grade.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $outletGrade = new OutletGrade();
        $outletGrade->setConnection($this->db);
        $outletGrade->otcg_name = $request->name;
        $outletGrade->otcg_code = $request->code;
        $outletGrade->lfcl_id = 1;
        $outletGrade->cont_id = $this->currentUser->country()->id;
        $outletGrade->aemp_iusr = $this->currentUser->employee()->id;
        $outletGrade->aemp_eusr = $this->currentUser->employee()->id;
        $outletGrade->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $outletGrade = OutletGrade::on($this->db)->findorfail($id);
            return view('master_data.outlet_grade.show')->with('outletGrade', $outletGrade);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $outletGrade = OutletGrade::on($this->db)->findorfail($id);
            return view('master_data.outlet_grade.edit')->with('outletGrade', $outletGrade);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $outletGrade = OutletGrade::on($this->db)->findorfail($id);
        $outletGrade->otcg_name = $request->name;
        $outletGrade->otcg_code = $request->code;
        $outletGrade->aemp_eusr = $this->currentUser->employee()->id;
        $outletGrade->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $outletGrade = OutletGrade::on($this->db)->findorfail($id);
            $outletGrade->lfcl_id = $outletGrade->lfcl_id == 1 ? 2 : 1;
            $outletGrade->aemp_eusr = $this->currentUser->employee()->id;
            $outletGrade->save();
            return redirect('/outlet_grade');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function outletGradeFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new OutletGrade(), 'return_reason_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function outletGradeFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new OutletGrade(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
