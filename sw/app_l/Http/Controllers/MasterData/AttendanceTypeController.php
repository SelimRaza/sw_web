<?php

namespace App\Http\Controllers\MasterData;

use App\BusinessObject\AttendanceType;
use App\MasterData\Channel;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceTypeController extends Controller
{
    private $access_key = 'tbld_attendance_type';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $attendanceTypes = AttendanceType::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.attendance_type.index')->with("attendanceTypes", $attendanceTypes)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.attendance_type.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $attendanceType = new AttendanceType();
        $attendanceType->name = $request->name;
        $attendanceType->code = $request->code;
        $attendanceType->status_id = 1;
        $attendanceType->country_id = $this->currentUser->employee()->cont_id;
        $attendanceType->created_by = $this->currentUser->employee()->id;
        $attendanceType->updated_by = $this->currentUser->employee()->id;
        $attendanceType->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $attendanceType = AttendanceType::findorfail($id);
            return view('master_data.attendance_type.show')->with('attendanceType', $attendanceType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $attendanceType = AttendanceType::findorfail($id);
            return view('master_data.attendance_type.edit')->with('attendanceType', $attendanceType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $attendanceType = AttendanceType::findorfail($id);
        $attendanceType->name = $request->name;
        $attendanceType->code = $request->code;
        $attendanceType->updated_by = $this->currentUser->employee()->id;
        $attendanceType->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->dlete) {
            $attendanceType = AttendanceType::findorfail($id);
            $attendanceType->status_id = $attendanceType->status_id == 1 ? 2 : 1;
            $attendanceType->updated_by = $this->currentUser->employee()->id;
            $attendanceType->save();
            return redirect('/attendance_type');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
