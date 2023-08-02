<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Channel;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\District;
use App\MasterData\Division;
use App\MasterData\Region;
use App\MasterData\SubChannel;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller
{
    private $access_key = 'tm_dsct';
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
            $channel = District::where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.district.index')->with("channels", $channel)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }



    /*public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.channel.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $channel = new Channel();
        $channel->name = $request->name;
        $channel->code = $request->code;
        $channel->status_id = 1;
        $channel->country_id = $this->currentUser->employee()->cont_id;
        $channel->created_by = $this->currentUser->employee()->id;
        $channel->updated_by = $this->currentUser->employee()->id;
        $channel->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $channel = Channel::findorfail($id);
            return view('master_data.channel.show')->with('channel', $channel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $channel = Channel::findorfail($id);
            return view('master_data.channel.edit')->with('channel', $channel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $channel = Channel::findorfail($id);
        $channel->name = $request->name;
        $channel->code = $request->code;
        $channel->updated_by = $this->currentUser->employee()->id;
        $channel->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $channel = Channel::findorfail($id);
            $channel->status_id = $channel->status_id == 1 ? 2 : 1;
            $channel->updated_by = $this->currentUser->employee()->id;
            $channel->save();
            return redirect('/channel');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }*/
}
