<?php

namespace App\Http\Controllers\MasterData;

use App\MasterData\Channel;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
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

class SubChannelController extends Controller
{
    private $access_key = 'tm_scnl';
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
            $subChannel = SubChannel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.sub_channel.index')->with("subChannels", $subChannel)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $channel = Channel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.sub_channel.create')->with('channels', $channel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $subChannel = new SubChannel();
        $subChannel->setConnection($this->db);
        $subChannel->scnl_name = $request->name;
        $subChannel->scnl_code = $request->code;
        $subChannel->chnl_id = $request->channel_id;
        $subChannel->lfcl_id = 1;
        $subChannel->cont_id = $this->currentUser->country()->id;
        $subChannel->aemp_iusr = $this->currentUser->employee()->id;
        $subChannel->aemp_eusr = $this->currentUser->employee()->id;
        $subChannel->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $subChannel = SubChannel::on($this->db)->findorfail($id);
            return view('master_data.sub_channel.show')->with('subChannel', $subChannel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $subChannel = SubChannel::findorfail($id);
            $channels = Channel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.sub_channel.edit')->with('subChannel', $subChannel)->with('channels', $channels);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $subChannel = SubChannel::on($this->db)->findorfail($id);
        $subChannel->scnl_name = $request->name;
        $subChannel->chnl_id = $request->channel_id;
        $subChannel->scnl_code = $request->code;
        $subChannel->aemp_eusr= $this->currentUser->employee()->id;
        $subChannel->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $subChannel = SubChannel::on($this->db)->findorfail($id);
            $subChannel->lfcl_id = $subChannel->lfcl_id == 1 ? 2 : 1;
            $subChannel->aemp_eusr = $this->currentUser->employee()->id;
            $subChannel->save();
            return redirect('/sub_channel');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function subChannelFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new SubChannel(), 'sub_channel_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function subChannelFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new SubChannel(), $request->file('import_file'));
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
