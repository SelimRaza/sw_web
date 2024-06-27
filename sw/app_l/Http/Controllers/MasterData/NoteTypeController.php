<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\NoteType;
use App\MasterData\Channel;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

use Response;

class NoteTypeController extends Controller
{
    private $access_key = 'NoteTypeController';
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
            $noteTypes = NoteType::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.NoteType.index')->with("noteTypes", $noteTypes)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.NoteType.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $noteType = new NoteType();
        $noteType->setConnection($this->db);
        $noteType->ntpe_name = $request->ntpe_name;
        $noteType->ntpe_code = $request->ntpe_code;
        $noteType->lfcl_id = 1;
        $noteType->cont_id = $this->currentUser->employee()->cont_id;
        $noteType->aemp_iusr = $this->currentUser->employee()->id;
        $noteType->aemp_eusr = $this->currentUser->employee()->id;

        $noteType->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $noteType = NoteType::on($this->db)->findorfail($id);
           // $noteType = NoteType::findorfail($id);
            return view('master_data.NoteType.show')->with('noteType', $noteType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
          //  $noteType = NoteType::findorfail($id);
            $noteType = NoteType::on($this->db)->findorfail($id);
            return view('master_data.NoteType.edit')->with('noteType', $noteType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        //$noteType = NoteType::findorfail($id);
        $noteType = NoteType::on($this->db)->findorfail($id);
        $noteType->ntpe_name = $request->ntpe_name;
        $noteType->ntpe_code = $request->ntpe_code;
        $noteType->aemp_eusr = $this->currentUser->employee()->id;
        $noteType->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            //$noteType = NoteType::findorfail($id);
            $noteType = NoteType::on($this->db)->findorfail($id);
            $noteType->lfcl_id = $noteType->lfcl_id == 1 ? 2 : 1;
            $noteType->aemp_eusr = $this->currentUser->employee()->id;
            $noteType->save();
            return redirect('/notetype');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function noteTypeFormatGen(Request $request)
    {
        //echo "afdsaf";

        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new NoteType(), 'note_type_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function regionMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Region(), $request->file('import_file'));
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
