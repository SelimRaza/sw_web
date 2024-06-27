<?php

namespace App\Http\Controllers\Tutorial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Tutorial;
//use App\DataExport\MeetingTrainingData;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TutorialController extends Controller
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
            $empId = $this->currentUser->employee()->id;
            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name`
                            FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zones = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name`
                            FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            return view('tutorials.index', ['groups' => $groups, 'zones' => $zones, 'permission' => $this->userMenu]);
        } else {
            return view('theme.access_limit');
        }
    }

    public function filter (Request $request)
    {
        $slgp_id = $request->sales_group_id;
        $zone_id = $request->zone_id;

        if($slgp_id && $zone_id){
            $tutorials = DB::connection($this->db)->table('tm_ttop')
                ->where('slgp_id', $slgp_id)
                ->where('zone_id', $zone_id)
                ->select('id', 'ttop_code', 'ttop_name', 'ttop_vdid', 'ttop_vurl')
                ->paginate(50);

            return response(['tutorials' => $tutorials, 'permission' => $this->userMenu->wsmu_updt], 200);
        }

        if($slgp_id)
        {
            $tutorials = DB::connection($this->db)
                ->table('tm_ttop')
                ->where('slgp_id', $slgp_id)
                ->select('id', 'ttop_code', 'ttop_name', 'ttop_vdid', 'ttop_vurl')
                ->paginate(50);

            return response(['tutorials' => $tutorials,'permission' => $this->userMenu->wsmu_updt], 200);
        }

        if($zone_id)
        {
            $tutorials = DB::connection($this->db)
                ->table('tm_ttop')
                ->where('zone_id', $zone_id)
                ->select('id', 'ttop_code', 'ttop_name', 'ttop_vdid', 'ttop_vurl')
                ->paginate(50);

            return response(['tutorials' => $tutorials,'permission' => $this->userMenu->wsmu_updt], 200);
        }

        return response(['error' => error], 400);
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) { $empId = $this->currentUser->employee()->id;

            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            $zones = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            return view('tutorials.create', ['groups' => $groups, 'zones' => $zones]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $tutorial = new Tutorial();
        $tutorial->setConnection($this->db);
        $tutorial->ttop_name = $request->name;
        $tutorial->ttop_code = $request->code;
        $tutorial->ttop_vdid = $request->ttop_vdid;
        $tutorial->ttop_desc = $request->ttop_desc;
        $tutorial->ttop_vurl = $request->ttop_vurl;
        $tutorial->ttop_ythm = $request->ttop_ythm ?? '';
        $tutorial->slgp_id = $request->sales_group_id;
        $tutorial->zone_id = $request->zone_id;
        $tutorial->cont_id = Auth::user()->country()->id;
        $tutorial->lfcl_id = 1;
        $tutorial->save();
        return redirect()->route('tutorial_topic')->with('success', 'successfully Added');
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $empId = $this->currentUser->employee()->id;

            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zones = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            $tutorial = Tutorial::on($this->db)->findorfail($id);
            return view('tutorials.edit', ['tutorial' => $tutorial, 'groups' => $groups, 'zones' => $zones]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $tutorial = Tutorial::on($this->db)->findorfail($id);
        $tutorial->ttop_name = $request->name;
        $tutorial->ttop_code = $request->code;
        $tutorial->ttop_vdid = $request->ttop_vdid;
        $tutorial->ttop_desc = $request->ttop_desc;
        $tutorial->ttop_vurl = $request->ttop_vurl;
        $tutorial->slgp_id = $request->sales_group_id;
        $tutorial->zone_id = $request->zone_id;
        $tutorial->save();

        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function tutorialMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Tutorial(), $request->file('import_file'));
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


    public function tutorialFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Tutorial(), 'tutorial_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
