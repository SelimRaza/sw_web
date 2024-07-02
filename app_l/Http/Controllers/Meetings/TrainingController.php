<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\BusinessObject\MeetingsTraining;
use App\DataExport\MeetingTrainingData;
use Maatwebsite\Excel\Facades\Excel;

class TrainingController extends Controller
{
    private $access_key = 'meetings/training';
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
            $training = MeetingsTraining::on($this->db)->get();
            return view('meetings.training.index')->with('trainings', $training)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('meetings.training.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }




    public function store(Request $request)
    {
        $training = new MeetingsTraining();
        $training->setConnection($this->db);
        $training->mstp_name = $request->name;
        $training->mstp_date = $request->date;
        $training->start_time = date('H:i',strtotime($request->start_time));
        $training->end_time = date('H:i',strtotime($request->end_time));
        $training->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $training = MeetingsTraining::on($this->db)->findorfail($id);
            return view('meetings.training.show')->with('training', $training);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $training = MeetingsTraining::on($this->db)->findorfail($id);
            return view('meetings.training.edit')->with('training', $training);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $training = MeetingsTraining::on($this->db)->findorfail($id);
        $training->mstp_name = $request->name;
        $training->mstp_date = $request->date;
        $training->start_time = date('H:i',strtotime($request->start_time));
        $training->end_time = date('H:i',strtotime($request->end_time));
        $training->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function excelExport()
    {
        return Excel::download(new MeetingTrainingData, "meetings_training_".date('Y_m_d').".xlsx");
    }
}
