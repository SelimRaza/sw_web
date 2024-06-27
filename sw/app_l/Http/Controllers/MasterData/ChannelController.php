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
use Illuminate\Support\Facades\DB;
use Excel;


class ChannelController extends Controller
{
    private $access_key = 'tbld_chnl';
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
            $channel = Channel::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.channel.index')->with("channels", $channel)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
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
        $channel->setConnection($this->db);
        $channel->chnl_name = $request->name;
        $channel->chnl_code = $request->code;
        $channel->lfcl_id = 1;
        $channel->cont_id = $this->currentUser->country()->id;
        $channel->aemp_iusr = $this->currentUser->employee()->id;
        $channel->aemp_eusr = $this->currentUser->employee()->id;
        $channel->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $channel = Channel::on($this->db)->findorfail($id);
            return view('master_data.channel.show')->with('channel', $channel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $channel = Channel::on($this->db)->findorfail($id);
            return view('master_data.channel.edit')->with('channel', $channel);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $channel = Channel::on($this->db)->findorfail($id);
        $channel->chnl_name = $request->name;
        $channel->chnl_code = $request->code;
        $channel->aemp_eusr = $this->currentUser->employee()->id;
        $channel->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $channel = Channel::on($this->db)->findorfail($id);
            $channel->lfcl_id = $channel->lfcl_id == 1 ? 2 : 1;
            $channel->aemp_eusr = $this->currentUser->employee()->id;
            $channel->save();
            return redirect('/channel');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function channelFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Channel(), 'channel_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function channelFileUpload(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Channel(), $request->file('import_file'));
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
