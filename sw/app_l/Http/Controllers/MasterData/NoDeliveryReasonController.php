<?php

namespace App\Http\Controllers\MasterData;

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

class NoDeliveryReasonController extends Controller
{
    private $access_key = 'NoDeliveryReasonController';
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
            $data = NoDeliveryReason::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.no_delivery_reason.index')->with('data', $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.no_delivery_reason.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $data = new NoDeliveryReason();
        $data->setConnection($this->db);
        $data->ondr_name = $request->ondr_name;
        $data->ondr_code = $request->ondr_code;
        $data->lfcl_id = 1;
        $data->cont_id = $this->currentUser->country()->id;
        $data->aemp_iusr = $this->currentUser->employee()->id;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

/*ondr_name
ondr_code
*/

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $data = NoDeliveryReason::on($this->db)->findorfail($id);
            return view('master_data.no_delivery_reason.show')->with('data', $data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $data = NoDeliveryReason::on($this->db)->findorfail($id);
            return view('master_data.no_delivery_reason.edit')->with('data', $data);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $data = NoDeliveryReason::on($this->db)->findorfail($id);
        $data->ondr_name = $request->ondr_name;
        $data->ondr_code = $request->ondr_code;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $data = NoDeliveryReason::on($this->db)->findorfail($id);
            $data->lfcl_id = $data->lfcl_id == 1 ? 2 : 1;
            $data->aemp_eusr = $this->currentUser->employee()->id;
            $data->save();
            return redirect('/no_delivery_reason');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function ndrFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new NoDeliveryReason(), 'No_delivery_reason_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function ndrFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new NoDeliveryReason(), $request->file('import_file'));
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
