<?php

namespace App\Http\Controllers\Location;


use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;

class NoteDetailsController extends Controller
{
    private $access_key = 'NoteDetailsController';
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


    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_vsbl) {
            $q = '';
            /*if ($request->has('search_text')) {
                $q = request('search_text');
                $noteData = DB::connection($this->db)->table('tt_note as t1')
                    ->leftJoin('tm_locd as t2', 't1.site_code', '=', 't2.locd_code')
                    ->leftJoin('tm_lsct as t3', 't2.lsct_id', '=', 't3.id')
                    ->leftJoin('tm_ldpt as t4', 't3.ldpt_id', '=', 't4.id')
                    ->leftJoin('tm_lcmp as t5', 't4.lcmp_id', '=', 't5.id')
                    ->leftJoin('tm_locm as t6', 't5.locm_id', '=', 't6.id')
                    ->leftJoin('tm_aemp as t7', 't1.aemp_id', '=', 't7.id')
                    ->leftJoin('tl_nimg as t8', 't8.note_id', '=', 't1.id')
                    ->select(
                        't1.id',
                        't1.note_body',
                        't1.note_dtim',
                        DB::connection($this->db)->raw('concat(t2.locd_name, " < ",t3.lsct_name, " < ", t4.ldpt_name, " < ", t5.lcmp_name, " < ", t6.locm_name) AS locd_name '),
                        't1.site_code',
                        't7.aemp_name',
                        't7.aemp_usnm',
                        DB::connection($this->db)->raw('GROUP_CONCAT("https://images.sihirbox.com/",t8.nimg_imag) as nimg_imag')
                    )->groupBy('t1.id','t1.note_body','t1.site_code','t7.aemp_name','t7.aemp_usnm','t2.locd_name','t3.lsct_name','t4.ldpt_name','t5.lcmp_name','t6.locm_name')
                    ->orderByDesc("t1.id")
                    ->where(function ($query) use ($q) {
                        $query->where('t1.site_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.locd_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.lsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.ldpt_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t5.lcmp_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t6.locm_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t7.aemp_usnm', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);
            } else {
                $noteData = DB::connection($this->db)->table('tt_note as t1')
                    ->leftJoin('tm_locd as t2', 't1.site_code', '=', 't2.locd_code')
                    ->leftJoin('tm_lsct as t3', 't2.lsct_id', '=', 't3.id')
                    ->leftJoin('tm_ldpt as t4', 't3.ldpt_id', '=', 't4.id')
                    ->leftJoin('tm_lcmp as t5', 't4.lcmp_id', '=', 't5.id')
                    ->leftJoin('tm_locm as t6', 't5.locm_id', '=', 't6.id')
                    ->leftJoin('tm_aemp as t7', 't1.aemp_id', '=', 't7.id')
                    ->select(
                        't1.id',
                        't1.note_body',
                        't1.note_dtim',
                        DB::connection($this->db)->raw('concat(t2.locd_name, " < ",t3.lsct_name, " < ", t4.ldpt_name, " < ", t5.lcmp_name, " < ", t6.locm_name) AS locd_name '),
                        't1.site_code',
                        't7.aemp_name',
                        't7.aemp_usnm'
                    )->orderByDesc("t1.id")
                    ->paginate(100);
                //  $locationData = LocationSection::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
            }*/
            $locm = DB::connection($this->db)->select("SELECT `id`,`locm_name` FROM `tm_locm` WHERE 1");

            return view('Location.note.index')->with("locm", $locm)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $noteData = collect(DB::connection($this->db)->select("SELECT
  t1.id,
  t1.note_body,
  t1.note_dtim,
  concat(t3.lsct_name, ' < ', t4.ldpt_name, ' < ', t5.lcmp_name, ' < ', t6.locm_name) AS locm_name,
  t2.locd_name,
  t1.site_code,
  t7.aemp_name                                                                        AS aemp_name,
  t7.aemp_usnm                                                                        AS aemp_usnm
FROM tt_note AS t1
  LEFT JOIN tm_locd AS t2 ON t1.site_code = t2.locd_code
  LEFT JOIN tm_lsct AS t3 ON t2.lsct_id = t3.id
  LEFT JOIN tm_ldpt AS t4 ON t3.ldpt_id = t4.id
  LEFT JOIN tm_lcmp AS t5 ON t4.lcmp_id = t5.id
  LEFT JOIN tm_locm AS t6 ON t5.locm_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_id = t7.id
WHERE t1.id = $id limit 10"))->first();

            $noteImage = DB::connection($this->db)->select("SELECT t1.nimg_imag
FROM tl_nimg AS t1
WHERE t1.note_id =$id");
            return view('Location.note.show')->with('noteData', $noteData)->with('noteImage', $noteImage);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function filterCompany(Request $request){
        $id=$request->id;
        $loc_companies=DB::connection($this->db)->select("SELECT id,lcmp_name from tm_lcmp where locm_id='$id'");
        return $loc_companies;
    }

    function filterNoteDetails(Request $request)
    {
        $empId = $this->currentUser->employee()->id;

        /*$zone_id = '';
                $sales_group_id = '8';
                $dirg_id = '';
                $start_date = '2021-06-23';
                $end_date = '2021-06-23';
                $acmp_id = '7';*/
        $q="";
        $locm_id = $request->locm_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $lcmp_id=$request->lcmp_id;
        $user_id=$request->user_id;
       
        if ($locm_id!=''){
          $q.= "and t6.id='".$locm_id."'";
        }
        if($lcmp_id!=''){
          $q.="and t5.id='".$lcmp_id."'";
        }
        if($user_id!=''){
          $q.="and t7.aemp_usnm='".$user_id."'";
        }
        $query = "SELECT
  t1.id,
  t1.note_body,
  t1.note_dtim,
  t2.locd_name,
  t3.lsct_name,
  t4.ldpt_name,
  t5.lcmp_name,
  t6.locm_name,
  t1.site_code,
  t7.aemp_name                                                                        AS aemp_name,
  t7.aemp_usnm                                                                        AS aemp_usnm,
  REPLACE(t1.geo_addr,',','-') As geo_addr, 
  t1.note_date
FROM tt_note AS t1
  LEFT JOIN tm_locd AS t2 ON t1.site_code = t2.locd_code
  LEFT JOIN tm_lsct AS t3 ON t2.lsct_id = t3.id
  LEFT JOIN tm_ldpt AS t4 ON t3.ldpt_id = t4.id
  LEFT JOIN tm_lcmp AS t5 ON t4.lcmp_id = t5.id
  LEFT JOIN tm_locm AS t6 ON t5.locm_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_id = t7.id
  WHERE t1.note_date BETWEEN '$start_date' and '$end_date'".$q;

        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $srData = DB::connection($this->db)->select(DB::raw($query));

        return $srData;
    }


}
