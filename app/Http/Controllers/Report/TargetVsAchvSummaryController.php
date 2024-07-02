<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TargetVsAchvSummaryController extends Controller
{
    private $access_key = 'TargetVsAchvSummaryController';
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
    public function index(){
        $acmp=DB::connection($this->db)->select("SELECT sg.company_id FROM `tbld_new_sales_group` sg
        INNER JOIN tbld_new_monthly_target tg ON sg.digr_text=tg.digr_text
        group BY sg.company_id");
    	return view('report.target.target_vs_achv_summary')->with('acmp',$acmp);
    }
    public function getTgtVsAchvReport(Request $request){
        $month_id=str_replace("/","",$request->start_date);
        $cmp=$request->acmp_id;
        if($cmp=='all'){
        $data=DB::connection($this->db)->select("SELECT sg.digr_name,z.zone_name,z.zone_code,sg.company_id,SUM(tg.tgt_amount)total_tgt,
        SUM(tg.achv_amount)total_achv,
        sg.digr_text,
        sum(case when pl.PRIORITY =1 then tg.tgt_amount end) plv1,
        sum(case when pl.PRIORITY =2 then tg.tgt_amount end) plv2,
        sum(case when pl.PRIORITY =3 then tg.tgt_amount end) plv3,
        sum(case when pl.PRIORITY =4 then tg.tgt_amount end) plv4,
        sum(case when pl.PRIORITY =5 then tg.tgt_amount end) plv5,
        sum(case when pl.PRIORITY =6 then tg.tgt_amount end) plv6,
        sum(case when pl.PRIORITY =7 then tg.tgt_amount end) plv7,
        sum(case when pl.PRIORITY =8 then tg.tgt_amount end) plv8,
        sum(case when pl.PRIORITY =9 then tg.tgt_amount end) plv9,
        sum(case when pl.PRIORITY IS NULL then tg.tgt_amount end)other,
        sum(case when pl.PRIORITY =1 then tg.achv_amount end) aplv1,
        sum(case when pl.PRIORITY =2 then tg.achv_amount end) aplv2,
        sum(case when pl.PRIORITY =3 then tg.achv_amount end) aplv3,
        sum(case when pl.PRIORITY =4 then tg.achv_amount end) aplv4,
        sum(case when pl.PRIORITY =5 then tg.achv_amount end) aplv5,
        sum(case when pl.PRIORITY =6 then tg.achv_amount end) aplv6,
        sum(case when pl.PRIORITY =7 then tg.achv_amount end) aplv7,
        sum(case when pl.PRIORITY =8 then tg.achv_amount end) aplv8,
        sum(case when pl.PRIORITY =9 then tg.achv_amount end) aplv9,
        sum(case when pl.PRIORITY IS NULL then tg.achv_amount end)aother
        FROM tbld_new_monthly_target tg
        INNER JOIN tbld_new_sales_group sg on sg.digr_text=tg.digr_text
        INNER JOIN tbld_new_distributor db ON db.dist_code=tg.dist_id
        INNER JOIN tm_zone z on z.zone_code=db.sales_zone_id
        LEFT JOIN tbld_new_pillar pl on pl.DIGR_TEXT=tg.digr_text 
        and pl.ITEM_CLASS_ID=tg.item_class_id and pl.S_Y_M=tg.month_id
        WHERE tg.month_id='$month_id'
        GROUP BY sg.company_id,sg.digr_name,sg.digr_text,z.zone_name,z.zone_code ORDER BY sg.company_id");
        }
        else {
            $data=DB::connection($this->db)->select("SELECT sg.digr_name,z.zone_name,z.zone_code,sg.company_id,SUM(tg.tgt_amount)total_tgt,
            SUM(tg.achv_amount)total_achv,
            sg.digr_text,
            sum(case when pl.PRIORITY =1 then tg.tgt_amount end) plv1,
            sum(case when pl.PRIORITY =2 then tg.tgt_amount end) plv2,
            sum(case when pl.PRIORITY =3 then tg.tgt_amount end) plv3,
            sum(case when pl.PRIORITY =4 then tg.tgt_amount end) plv4,
            sum(case when pl.PRIORITY =5 then tg.tgt_amount end) plv5,
            sum(case when pl.PRIORITY =6 then tg.tgt_amount end) plv6,
            sum(case when pl.PRIORITY =7 then tg.tgt_amount end) plv7,
            sum(case when pl.PRIORITY =8 then tg.tgt_amount end) plv8,
            sum(case when pl.PRIORITY =9 then tg.tgt_amount end) plv9,
            sum(case when pl.PRIORITY IS NULL then tg.tgt_amount end)other,
            sum(case when pl.PRIORITY =1 then tg.achv_amount end) aplv1,
            sum(case when pl.PRIORITY =2 then tg.achv_amount end) aplv2,
            sum(case when pl.PRIORITY =3 then tg.achv_amount end) aplv3,
            sum(case when pl.PRIORITY =4 then tg.achv_amount end) aplv4,
            sum(case when pl.PRIORITY =5 then tg.achv_amount end) aplv5,
            sum(case when pl.PRIORITY =6 then tg.achv_amount end) aplv6,
            sum(case when pl.PRIORITY =7 then tg.achv_amount end) aplv7,
            sum(case when pl.PRIORITY =8 then tg.achv_amount end) aplv8,
            sum(case when pl.PRIORITY =9 then tg.achv_amount end) aplv9,
            sum(case when pl.PRIORITY IS NULL then tg.achv_amount end)aother
            FROM tbld_new_monthly_target tg
            INNER JOIN tbld_new_sales_group sg on sg.digr_text=tg.digr_text
            INNER JOIN tbld_new_distributor db ON db.dist_code=tg.dist_id
            INNER JOIN tm_zone z on z.zone_code=db.sales_zone_id
            LEFT JOIN tbld_new_pillar pl on pl.DIGR_TEXT=tg.digr_text 
            and pl.ITEM_CLASS_ID=tg.item_class_id and pl.S_Y_M=tg.month_id
            WHERE tg.month_id='$month_id' AND sg.company_id ='$cmp'
            GROUP BY sg.company_id,sg.digr_name,sg.digr_text,z.zone_name,z.zone_code ORDER BY sg.company_id"); 
        }
        if($data){
        $gp=array_unique( array_column( $data , 'digr_text' ) );
        $gp_wise_class=DB::connection($this->db)->select("SELECT pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name FROM `tbld_new_pillar` as pl
        INNER JOIN tm_itcl cl on cl.itcl_code=pl.ITEM_CLASS_ID
        where pl.DIGR_TEXT in (".implode(',',$gp).") AND pl.S_Y_M='$month_id' GROUP BY pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name");

        return response()->json(
            [
                'details'=>$data,
                'acmp_class'=>$gp_wise_class
            ]
            );
        }
        else{
            return response()->json(
                [
                    'details'=>'',
                    'acmp_class'=>''
                ]
                );

        }
        
        //dd($gp);
    }
}
