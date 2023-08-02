<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\CancelOrderReason;
use App\MasterData\Category;
use App\MasterData\NoDeliveryReason;
use App\MasterData\NoOrderReason;
use App\MasterData\ReturnReason;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class CancelOrderReasonController extends Controller
{
    private $access_key = 'CancelOrderReasonController';
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
            $data = CancelOrderReason::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.cancel_order_reason.index')->with('data', $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.cancel_order_reason.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $data = new CancelOrderReason();
        $data->setConnection($this->db);
        $data->ocrs_name = $request->ocrs_name;
        $data->ocrs_code = $request->ocrs_code;
        $data->lfcl_id = 1;
        $data->cont_id = $this->currentUser->country()->id;
        $data->aemp_iusr = $this->currentUser->employee()->id;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    /*ocrs_name
ocrs_code

    */

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $data = CancelOrderReason::on($this->db)->findorfail($id);
            return view('master_data.cancel_order_reason.show')->with('data', $data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $data = CancelOrderReason::on($this->db)->findorfail($id);
            return view('master_data.cancel_order_reason.edit')->with('data', $data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $data = CancelOrderReason::on($this->db)->findorfail($id);
        $data->ocrs_name = $request->ocrs_name;
        $data->ocrs_code = $request->ocrs_code;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $data = CancelOrderReason::on($this->db)->findorfail($id);
            $data->lfcl_id = $data->lfcl_id == 1 ? 2 : 1;
            $data->aemp_eusr = $this->currentUser->employee()->id;
            $data->save();
            return redirect('/cancel_order_reason');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function ocrFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new CancelOrderReason(), 'Cancel_order_reason_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function ocrFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new CancelOrderReason(), $request->file('import_file'));
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
