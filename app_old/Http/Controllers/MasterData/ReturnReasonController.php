<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Category;
use App\MasterData\ReturnReason;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class ReturnReasonController extends Controller
{
    private $access_key = 'tm_rson';
    private $currentUser;
    private $userMenu;

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
            $returnReasons = ReturnReason::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.return_reason.index')->with('returnReasons', $returnReasons)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            //   $productGroups = ProductGroup::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.return_reason.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $returnReason = new ReturnReason();
        $returnReason->setConnection($this->db);
        $returnReason->dprt_name = $request->name;
        $returnReason->dprt_code = $request->code;
        $returnReason->lfcl_id = 1;
        $returnReason->cont_id = $this->currentUser->country()->id;
        $returnReason->aemp_iusr = $this->currentUser->employee()->id;
        $returnReason->aemp_eusr = $this->currentUser->employee()->id;
        $returnReason->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $returnReason = ReturnReason::on($this->db)->findorfail($id);
            return view('master_data.return_reason.show')->with('returnReason', $returnReason);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $returnReason = ReturnReason::on($this->db)->findorfail($id);
            //  $productGroups = ProductGroup::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.return_reason.edit')->with('category', $returnReason);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $returnReason = ReturnReason::on($this->db)->findorfail($id);
        $returnReason->dprt_name = $request->name;
        $returnReason->dprt_code = $request->code;
        $returnReason->aemp_eusr = $this->currentUser->employee()->id;
        $returnReason->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $returnReason = ReturnReason::on($this->db)->findorfail($id);
            $returnReason->lfcl_id = $returnReason->lfcl_id == 1 ? 2 : 1;
            $returnReason->aemp_eusr = $this->currentUser->employee()->id;
            $returnReason->save();
            return redirect('/return_reason');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function returnReasonFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new ReturnReason(), 'return_reason_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function returnReasonFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new ReturnReason(), $request->file('import_file'));
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
