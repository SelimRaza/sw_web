<?php

namespace App\Http\Controllers\Tutorial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Tutorial;
use App\BusinessObject\TutorialQuestion;
//use App\DataExport\MeetingTrainingData;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TutorialQuestionController extends Controller
{
    private $access_key = 'tutorial_question';
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
            $questions = DB::connection($this->db)->select("select 
                            t1.id 'id',
                            t2.ttop_vdid,
                            t1.ttqs_type,
                            t1.ttqs_ques,
                            t1.ttqs_opt1,
                            t1.ttqs_opt2,
                            t1.ttqs_opt3,
                            t1.ttqs_opt4,
                            t1.ttqs_ansr,
                            t1.lfcl_id
                            from tt_ttqs t1
                            inner join tm_ttop t2 on t1.ttop_id = t2.id
                            where t1.lfcl_id = 1");
            return view('tutorial_question.index', ['questions' => $questions, 'permission' => $this->userMenu]);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) { $empId = $this->currentUser->employee()->id;

            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission`
                                                        WHERE `aemp_id`='$empId'");

            $zones = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission`
                                                        WHERE `aemp_id`='$empId'");

            $tutorials = DB::connection($this->db)->select("select max(ttop_vdid) vdo_id, max(ttop_name) name
                                                from tm_ttop group by ttop_vdid;");

            return view('tutorial_question.create', ['groups' => $groups, 'zones' => $zones, 'tutorials' => $tutorials]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
//
    public function store(Request $request)
    {
        try {
            $tutorial = new TutorialQuestion();
            $tutorial->setConnection($this->db);
            $tutorial->ttop_id = $this->getTutorialId($request->tutorial_code, $request->group_id, $request->zone_id);
            $tutorial->ttqs_type = $request->ttqs_type;
            $tutorial->ttqs_ques = $request->ttqs_ques;
            $tutorial->ttqs_opt1 = $request->ttqs_opt1 ?? '';
            $tutorial->ttqs_opt2 = $request->ttqs_opt2 ?? '';
            $tutorial->ttqs_opt3 = $request->ttqs_opt3 ?? '';
            $tutorial->ttqs_opt4 = $request->ttqs_opt4 ?? '';
            $tutorial->ttqs_ansr = $request->ttqs_ansr;
            $tutorial->cont_id = Auth::user()->country()->id;
            $tutorial->lfcl_id = 1;
            $tutorial->aemp_iusr = $this->currentUser->employee()->id;
            $tutorial->save();
            return redirect()->route('tutorial_question.index')->with('success', 'successfully Added');

        }catch(\Exception $e)
        {
            return redirect()->back()->with('danger', 'Please Create Tutorial First for this group and zone');
        }
    }

    public function getTutorialId($tutorial_code, $group_id, $zone_id)
    {
        return DB::connection($this->db)->table('tm_ttop')->where('ttop_vdid', $tutorial_code)
                                        ->where('slgp_id', $group_id)
                                        ->where('zone_id', $zone_id)
                                        ->value('id') ?? null;
    }
//
    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $empId = $this->currentUser->employee()->id;
            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission`
                                                        WHERE `aemp_id`='$empId'");

            $zones = DB::connection($this->db)->select("SELECT DISTINCT `zone_id` AS id,`zone_code`,`zone_name` FROM `user_area_permission`
                                                        WHERE `aemp_id`='$empId'");

            $tutorials = DB::connection($this->db)->select("select max(ttop_vdid) vdo_id, max(ttop_name) name
                                                from tm_ttop group by ttop_vdid");
            $question = TutorialQuestion::on($this->db)->findorfail($id);
            $tutorial_info = Tutorial::on($this->db)->findorfail($question->ttop_id);
            return view('tutorial_question.edit',[
                'tutorials' => $tutorials,
                'groups' => $groups,
                'zones' => $zones,
                'question' => $question,
                'tutorial_info' => $tutorial_info
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
//
    public function update(Request $request, $id)
    {

        try {
            $tutorial = TutorialQuestion::on($this->db)->findorfail($id);
            $tutorial->ttop_id = $this->getTutorialId($request->tutorial_code, $request->group_id, $request->zone_id);
            $tutorial->ttqs_type = $request->ttqs_type;
            $tutorial->ttqs_ques = $request->ttqs_ques;
            $tutorial->ttqs_opt1 = $request->ttqs_opt1 ?? '';
            $tutorial->ttqs_opt2 = $request->ttqs_opt2 ?? '';
            $tutorial->ttqs_opt3 = $request->ttqs_opt3 ?? '';
            $tutorial->ttqs_opt4 = $request->ttqs_opt4 ?? '';
            $tutorial->ttqs_ansr = $request->ttqs_ansr;
            $tutorial->cont_id = Auth::user()->country()->id;
            $tutorial->lfcl_id = 1;
            $tutorial->aemp_eusr = $this->currentUser->employee()->id;
            $tutorial->save();

            return redirect()->route('tutorial_question.index')->with('success', 'Successfully Updated');
        }catch(\Exception $e)
        {
            return $e->getMessage();
            return redirect()->back()->with('danger', 'Please Create a Tutorial First for this Group and Zone');
        }
    }
//
//    public function tutorialMasterUploadInsert(Request $request)
//    {
//        if ($this->userMenu->wsmu_crat) {
//            if ($request->hasFile('import_file')) {
//                DB::connection($this->db)->beginTransaction();
//                try {
//                    Excel::import(new Tutorial(), $request->file('import_file'));
//                    DB::connection($this->db)->commit();
//                    return redirect()->back()->with('success', 'Successfully Uploaded');
//                } catch (\Exception $e) {
//                    DB::connection($this->db)->rollback();
//                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
//                    //throw $e;
//                }
//            }
//            return back()->with('danger', ' File Not Found');
//        } else {
//            return redirect()->back()->with('danger', 'Access Limited');
//        }
//    }
//
//
//    public function tutorialFormatGen(Request $request)
//    {
//        //echo "afdsaf";
//
//        if ($this->userMenu->wsmu_crat) {
//            return Excel::download(new Tutorial(), 'tutorial_format_' . date("Y-m-d H:i:s") . '.xlsx');
//        } else {
//            return redirect()->back()->with('danger', 'Access Limited');
//        }
//    }

    public function show($id)
    {
        try {
            $tutorial = TutorialQuestion::on($this->db)->findorfail($id);
            $tutorial->lfcl_id = 2;
            $tutorial->save();

            return response(['success', 'Successfully Deleted'], 200);
        }catch(\Exception $e)
        {
            return response(['danger', 'Not Have the right to Delete'], 400);
        }

    }


//    public function excelExport()
//    {
//        return Excel::download(new MeetingTrainingData, "meetings_training_".date('Y_m_d').".xlsx");
//    }
}
