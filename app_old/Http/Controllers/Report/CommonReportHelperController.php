<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
class CommonReportHelperController extends Controller
{
    private $access_key = 'WeeklyOrderSummary';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->aemp_id = Auth::user()->employee()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }
    public function getEmpId($aemp_usnm){
        $emp=Employee::on($this->db)->where(['aemp_usnm'=>$aemp_usnm])->first();
        return $emp->id;
    }
    public function getCategoryWiseNoteDetails($aemp_usnm,$start_date,$end_date){
        $aemp_id=$this->getEmpId($aemp_usnm);
        $data=DB::connection($this->db)->select("SELECT 
                    t1.id,t1.note_date,t1.note_dtim,concat(t4.site_code,'-',t4.site_name)site_name,t1.note_titl,t1.note_body,t2.ntpe_name,max(t3.nimg_imag) note_img
                    FROM tt_note t1
                    INNER JOIN tm_ntpe t2 ON t1.ntpe_id=t2.id
                    LEFT JOIN tl_nimg t3 ON t1.id=t3.note_id
                    LEFT JOIN tm_site t4 ON t1.site_code=t4.site_code
                    WHERE t1.note_date between '$start_date' AND '$end_date' AND t1.aemp_id=$aemp_id
                    GROUP BY t1.id,t1.note_date,t1.note_dtim,t4.site_code,t4.site_name,t1.note_titl,t1.note_body,t2.ntpe_name ORDER BY t1.note_dtim ASC
                    ");
        return $data;
    }
    public function getUserEmailAddress(){
        $user_email=Employee::on($this->db)->find($this->aemp_id);
        return array('email'=>$user_email->aemp_emal);
    }
   
}
