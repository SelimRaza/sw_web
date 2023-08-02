<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\NoteType;
use App\BusinessObject\OutOfStock;
use App\MasterData\Channel;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OutOfStockController extends Controller
{
    private $access_key = 'OutOfStockController';
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
           // $noteTypes = NoteType::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $outofstock = OutOfStock::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.outofstock.index')->with("outofstock", $outofstock)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.outofstock.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $outofstock = new OutOfStock();
        $outofstock->setConnection($this->db);
        $outofstock->dpot_id = $request->dpot_id;
        $outofstock->amim_id = $request->amim_id;
        $outofstock->lfcl_id = 1;
        $outofstock->cont_id = $this->currentUser->employee()->cont_id;
        $outofstock->aemp_iusr = $this->currentUser->employee()->id;
        $outofstock->aemp_eusr = $this->currentUser->employee()->id;

        $outofstock->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $outofstock = OutOfStock::on($this->db)->findorfail($id);
           // $noteType = NoteType::findorfail($id);
            return view('master_data.outofstock.show')->with('outofstock', $outofstock);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
          //  $noteType = NoteType::findorfail($id);
            $outofstock = OutOfStock::on($this->db)->findorfail($id);
            return view('master_data.outofstock.edit')->with('outofstock', $outofstock);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        //$noteType = NoteType::findorfail($id);
        $outofstock = OutOfStock::on($this->db)->findorfail($id);
        $outofstock->dpot_id = $request->dpot_id;
        $outofstock->amim_id = $request->amim_id;
        $outofstock->aemp_eusr = $this->currentUser->employee()->id;
        $outofstock->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            //$noteType = NoteType::findorfail($id);
            $outofstock = OutOfStock::on($this->db)->findorfail($id);
            $outofstock->lfcl_id = $outofstock->lfcl_id == 1 ? 2 : 1;
            $outofstock->aemp_eusr = $this->currentUser->employee()->id;
            $outofstock->save();
            return redirect('/outofstock');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
