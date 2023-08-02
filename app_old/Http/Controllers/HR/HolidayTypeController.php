<?php

namespace App\Http\Controllers\HR;

use App\BusinessObject\HolidayType;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HolidayTypeController extends Controller
{
    private $access_key = 'tbld_holiday_type';
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
            $holidayTypes = HolidayType::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.holiday_type.index')->with("holidayTypes", $holidayTypes)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.holiday_type.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $holidayType = new HolidayType();
        $holidayType->name = $request->name;
        $holidayType->code = $request->code;
        $holidayType->status_id = 1;
        $holidayType->country_id = $this->currentUser->employee()->cont_id;
        $holidayType->created_by = $this->currentUser->employee()->id;
        $holidayType->updated_by = $this->currentUser->employee()->id;
        $holidayType->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $holidayType = HolidayType::findorfail($id);
            return view('master_data.holiday_type.show')->with('holidayType', $holidayType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $holidayType = HolidayType::findorfail($id);
            return view('master_data.holiday_type.edit')->with('holidayType', $holidayType);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $holidayType = HolidayType::findorfail($id);
        $holidayType->name = $request->name;
        $holidayType->code = $request->code;
        $holidayType->updated_by = $this->currentUser->employee()->id;
        $holidayType->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $holidayType = HolidayType::findorfail($id);
            $holidayType->status_id = $holidayType->status_id == 1 ? 2 : 1;
            $holidayType->updated_by = $this->currentUser->employee()->id;
            $holidayType->save();
            return redirect('/holiday_type');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
