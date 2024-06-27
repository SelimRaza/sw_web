<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu\Menu;
use App\Menu\SubMenu;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon;

class TestController extends Controller
{	
    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
		
    }
    public function index() {
        
        
    }
    public function home() {
    
    	$this->db = Auth::user()->country()->cont_conn;
    	//$country= Auth::user()->country()->id;
    	$date =Carbon\Carbon::now()->format('Y-m-d');

    	$u=Auth::user()->email;
    	$emp_id=DB::connection($this->db)->select("SELECT id,role_id FROM tm_aemp WHERE aemp_usnm ='$u'");
   		$emid=$emp_id?$emp_id[0]->id:'';
   		$emp_role=$emp_id?$emp_id[0]->role_id:'';
		$pt_show=0;
		$gp_wise_class='';
		$pillar_data='';
		$out_type_count='';
		$today_open_out='';
		$visited=0;
    	  if ($this->db) {
    	            $employee = DB::connection($this->db)->select("SELECT
    	  t1.dhbd_ucnt                                              AS totalSr,
    	  t1.dhbd_prnt                                              AS onSr,
    	  t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
    	  t1.dhbd_pact                                              AS actSr,
    	  t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
    	  t1.dhbd_prdt                                              AS pvSr,
    	  t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
    	  t1.`dhbd_lvsr`                                            AS levSr,
    	  format(ifnull((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
    	  t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
    	  t1.dhbd_memo                                              AS productiveMemo,
    	  t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
    	  t1.dhbd_tsit                                              AS totalScheduleCall,
    	  t1.dhbd_tvit                                              AS total_visited,
    	  t1.dhbd_line                                              AS number_item_line,
    	  round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
    	  round(t1.dhbd_tsit/ t1.dhbd_ucnt,2)                     	AS avgOutSR,
    	  round(t1.dhbd_memo/ t1.dhbd_pact,2)                     	AS AMSR,
    	  round(t1.dhbd_tvit/ t1.dhbd_pact,2)                     	AS AVSR,
    	  round(t1.dhbd_tamt/ t1.dhbd_pact,2)                     	AS APESR,
    	  round(t1.dhbd_tamt/ t1.dhbd_memo,2)                     	AS APEOIT,
    	  t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
    	  0                                                         AS totalMspTargetCtn,
    	  t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
    	  0                                                         AS totalMspOrderCtn,
    	  t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
    	  t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
    	  t1.dhbd_spbg                                              AS supervisorBudgetAmount,
    	  t1.dhbd_spav                                              AS supervisorBudgetAvail,
    	  t1.dhbd_cblc                                              AS creditBlockOrder,
    	  t1.dhbd_cbla                                              AS creditBlockAmount,
    	  t1.dhbd_oblc                                              AS overDueBlockOrder,
    	  t1.dhbd_obla                                              AS overDueBlockAmount,
    	  t1.dhbd_sblc                                              AS specialBlockOrder,
    	  t1.dhbd_sbla                                              AS specialBlockAmount,
    	  t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
    	  t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
    	 
    	  t1.dhbd_time                                              AS updated_at
    	FROM th_dhbd_5 AS t1
    	WHERE t1.aemp_id = '$emid' AND t1.dhbd_date = '$date'");
        if($this->db){
            $data=DB::connection($this->db)->select( "SELECT
    	  t1.dhbd_ucnt                                              AS totalSr,
    	  t1.dhbd_prnt                                              AS onSr,
    	  t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
    	  t1.dhbd_pact                                              AS actSr,
    	  t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
    	  t1.dhbd_prdt                                              AS pvSr,
    	  t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
    	  t1.`dhbd_lvsr`                                            AS levSr,
    	  format(ifnull((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
    	  t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
    	  t1.dhbd_memo                                              AS productiveMemo,
    	  t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
    	  t1.dhbd_tsit                                              AS totalScheduleCall,
    	  t1.dhbd_tvit                                              AS total_visited,
    	  t1.dhbd_line                                              AS number_item_line,
    	  format(ifnull((t1.dhbd_line / t1.dhbd_memo),0), 2)        AS lineParCall,
    	  round(t1.dhbd_tsit/ t1.dhbd_ucnt,2)                     	AS avgOutSR,
    	  round(t1.dhbd_memo/ t1.dhbd_pact,2)                     	AS AMSR,
    	  round(t1.dhbd_tvit/ t1.dhbd_pact,2)                     	AS AVSR,
    	  round(t1.dhbd_tamt/ t1.dhbd_pact,2)                     	AS APESR,
    	  round(t1.dhbd_tamt/ t1.dhbd_memo,2)                     	AS APEOIT,
    	  t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
    	  0                                                         AS totalMspTargetCtn,
    	  t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
    	  t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
    	  t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
          t1.dhbd_date                                              AS date_day,
    	  t1.dhbd_time                                              AS updated_at
    	FROM th_dhbd_5 AS t1
    	WHERE t1.aemp_id = '$emid' AND (t1.dhbd_date <=curdate() AND t1.dhbd_date>=(DATE_SUB(CURDATE(),INTERVAL 6 DAY)))
            ");
        }
		if($emp_role==2){
			$zone_id=Auth::user()->employee()->zone_id;
			$slgp_id=Auth::user()->employee()->slgp_id;
			$current_date=date('Y-m-d');
			$month_id=date('Ym');
			$emid=Auth::user()->employee()->id;
			$parent_emp=DB::connection($this->db)->select("SELECT t1.aemp_name,t1.aemp_usnm,t2.role_name,t1.role_id,t1.zone_id,t1.slgp_id from tm_aemp t1
						 INNER JOIN tm_role t2 ON t1.role_id=t2.id where t1.id='$emid'");
			$out_type_count=DB::connection($this->db)->select("SELECT SUM(if(t4.otcg_id IN(1,10),1,0))AS Whole_Sale_site, 
							SUM(if(t4.otcg_id IN(4),1,0))AS Tong_site, 
							SUM(if(t4.otcg_id NOT IN(1,4,10),1,0))AS Trade_M_site
							FROM tm_aemp t1 JOIN tl_rpln t2 ON(t1.id=t2.aemp_id)
							inner JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
							inner JOIN tm_site t4 ON t3.site_id=t4.id
							WHERE t1.aemp_mngr ='$emid' and  t2.rpln_day = dayname('$current_date')
							AND t1.lfcl_id=1 
							AND t4.lfcl_id=1");
			$today_open_out=DB::connection($this->db)->select("select t1.attr3, count(*)opened_outlet_today from tm_ward t1,tm_mktm t5,tm_site_registration t4
							where t1.id=t5.ward_id and t4.mktm_id=t5.id and t4.lfcl_id=1 and t1.attr3='$zone_id'
							group by t1.attr3");
			$visited=DB::connection($this->db)->select("select t1.visited onemonth,t2.visited twomonth,t3.visited threemonth from zone_visited_onemonth as t1,
							zone_visited_twomonth as t2,zone_visited_Threemonth as t3
							where t1.zone_id=t2.zone_id and t2.zone_id=t3.zone_id 
							and t1.slgp_id=t2.slgp_id and t2.slgp_id=t3.slgp_id
							and t1.zone_id=$zone_id and t1.slgp_id=$slgp_id");
			$slgp_id=$parent_emp[0]->slgp_id;
			$slgp_code=DB::connection($this->db)->select("SELECT slgp_code FROM tm_slgp WHERE id='$slgp_id'");
			$slgp_code=$slgp_code?$slgp_code[0]->slgp_code:'';
			$zone_id=$parent_emp[0]->zone_id;
			$zone_code=DB::connection($this->db)->select("SELECT zone_code FROM tm_zone WHERE id='$zone_id'");
			$zone_code=$zone_code?$zone_code[0]->zone_code:'';

			$pillar_data=DB::connection($this->db)->select("SELECT sg.digr_name,z.zone_name,z.zone_code,sg.company_id,round(SUM(tg.tgt_amount),0)total_tgt,
			round(SUM(tg.achv_amount),0)total_achv,
			sg.digr_text,
			round(sum(case when pl.PRIORITY =1 then tg.tgt_amount else 0 end),0) plv1,
			round(sum(case when pl.PRIORITY =2 then tg.tgt_amount else 0 end),0) plv2,
			round(sum(case when pl.PRIORITY =3 then tg.tgt_amount else 0 end),0) plv3,
			round(sum(case when pl.PRIORITY =4 then tg.tgt_amount else 0 end),0) plv4,
			round(sum(case when pl.PRIORITY =5 then tg.tgt_amount else 0 end),0) plv5,
			round(sum(case when pl.PRIORITY =6 then tg.tgt_amount else 0 end),0) plv6,
			round(sum(case when pl.PRIORITY =7 then tg.tgt_amount else 0 end),0) plv7,
			round(sum(case when pl.PRIORITY =8 then tg.tgt_amount else 0 end),0) plv8,
			round(sum(case when pl.PRIORITY =9 then tg.tgt_amount else 0 end),0) plv9,
			round(sum(case when pl.PRIORITY IS NULL then tg.tgt_amount end),0)   other,
			round(sum(case when pl.PRIORITY =1 then tg.achv_amount else 0 end),0) aplv1,
			round(sum(case when pl.PRIORITY =2 then tg.achv_amount else 0 end),0) aplv2,
			round(sum(case when pl.PRIORITY =3 then tg.achv_amount else 0 end),0) aplv3,
			round(sum(case when pl.PRIORITY =4 then tg.achv_amount else 0 end),0) aplv4,
			round(sum(case when pl.PRIORITY =5 then tg.achv_amount else 0 end),0) aplv5,
			round(sum(case when pl.PRIORITY =6 then tg.achv_amount else 0 end),0) aplv6,
			round(sum(case when pl.PRIORITY =7 then tg.achv_amount else 0 end),0) aplv7,
			round(sum(case when pl.PRIORITY =8 then tg.achv_amount else 0 end),0) aplv8,
			round(sum(case when pl.PRIORITY =9 then tg.achv_amount else 0 end),0) aplv9,
			round(sum(case when pl.PRIORITY IS NULL then tg.achv_amount end),0)aother
							FROM tbld_new_monthly_target tg
							INNER JOIN tbld_new_sales_group sg on sg.digr_text=tg.digr_text
							INNER JOIN tbld_new_distributor db ON db.dist_code=tg.dist_id
							INNER JOIN tm_zone z on z.zone_code=db.sales_zone_id
							LEFT JOIN tbld_new_pillar pl on pl.DIGR_TEXT=tg.digr_text 
							and pl.ITEM_CLASS_ID=tg.item_class_id and pl.S_Y_M=tg.month_id
							WHERE tg.month_id='$month_id' And z.zone_code='$zone_code' AND sg.digr_text LIKE '%$slgp_code'
							GROUP BY sg.company_id,sg.digr_name,sg.digr_text,z.zone_name,z.zone_code ORDER BY sg.company_id");
			if($pillar_data){
				$gp=array_unique( array_column( $pillar_data , 'digr_text' ) );
				$gp_wise_class=DB::connection($this->db)->select("SELECT pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name FROM `tbld_new_pillar` as pl
				INNER JOIN tm_itcl cl on cl.itcl_code=pl.ITEM_CLASS_ID
				where pl.DIGR_TEXT in (".implode(',',$gp).") AND pl.S_Y_M='$month_id' GROUP BY pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name");
			 }
			
			
		}
    	
    } 
	//return array('today_open_out'=>$today_open_out,'out_type_count'=>$out_type_count,'gp_wise_class'=>$gp_wise_class);
	//return $employee;
    return view('project_board')->with('data',$data)->with('employee',$employee)->with('data',$data)->with('emid',$emid)->with('pt_show',$pt_show)->with('emp_role',$emp_role)
		->with('pillar_data',$pillar_data)->with('gp_wise_class',$gp_wise_class)->with('today_open_out',$today_open_out)->with('out_type_count',$out_type_count)->with('visited',$visited);
    }

    public function menu() {
       // $menu = Menu::all()->load('submenu');
        return view('project_menu');
    }
    public function getTopData(Request $request){
        $this->db = Auth::user()->country()->cont_conn;
		$type=$request->type;
		$id=$request->emid;
    	$em_d=DB::connection($this->db)->select("SELECT role_id,slgp_id FROM tm_aemp where id='$id'");
    	$emp_role=$em_d?$em_d[0]->role_id:'';
    	$emp_slgp_id=$em_d?$em_d[0]->slgp_id:'';
		// $emp_role =Auth::user()->employee()->role_id;
    	// $emp_slgp_id =Auth::user()->employee()->slgp_id;
		
		//$query="SELECT id from tm_aemp where aemp_usnm='dsgfgfdgfdgfdgf'";
		if ($emp_role>6) {
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="CLASS") {
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  itcl_code,itcl_name order by sum(ordd_oamt) desc limit 10";
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  amim_code,amim_name order by sum(ordd_oamt) desc limit 10";
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  site_code,site_name order by sum(ordd_oamt) desc limit 10";
			}
		}
		//If Role= Business Unit Head |
		else if($emp_role==6){
			$acmp_id=DB::connection($this->db)->select("SELECT acmp_id FROM tm_slgp WHERE id='$emp_slgp_id'");
			$acmp_id=$acmp_id[0]->acmp_id;
			$slgp_list=DB::connection($this->db)->select("SELECT id FROM tm_slgp WHERE acmp_id='$acmp_id'");
			foreach ($slgp_list as $slgp) {
				$slgps[]=$slgp->id;
			}
			$slgp=implode(",",$slgps);
			
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="CLASS") {
				
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  itcl_code,itcl_name order by sum(ordd_oamt) desc limit 10";
						
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  amim_code,amim_name order by sum(ordd_oamt) desc limit 10";
				
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  site_code,site_name order by sum(ordd_oamt) desc limit 10";
			}
			
		}
		else{
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) desc limit 10";
			}
			else if ($type=="CLASS") {
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  itcl_code,itcl_name order by sum(ordd_oamt) desc limit 10";
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  amim_code,amim_name order by sum(ordd_oamt) desc limit 10";
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  site_code,site_name order by sum(ordd_oamt) desc limit 10";
			}
			
		}
		
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $data= DB::connection($this->db)->select(DB::raw($query));
        return $data;

    }
    public function getBottomData(Request $request){
		$this->db = Auth::user()->country()->cont_conn;
		$type=$request->type;
		$id=$request->emid;
    	$em_d=DB::connection($this->db)->select("SELECT role_id,slgp_id FROM tm_aemp where id='$id'");
    	$emp_role=$em_d?$em_d[0]->role_id:'';
    	$emp_slgp_id=$em_d?$em_d[0]->slgp_id:'';
		$query='';
		if ($emp_role>6) {
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="CLASS") {
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  itcl_code,itcl_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  amim_code,amim_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
				group by  site_code,site_name order by sum(ordd_oamt) asc limit 10";
			}
		}
		//If Role= Business Unit Head |
		else if($emp_role==6){
			$acmp_id=DB::connection($this->db)->select("SELECT acmp_id FROM tm_slgp WHERE id='$emp_slgp_id'");
			$acmp_id=$acmp_id[0]->acmp_id;
			$slgp_list=DB::connection($this->db)->select("SELECT id FROM tm_slgp WHERE acmp_id='$acmp_id'");
			foreach ($slgp_list as $slgp) {
				$slgps[]=$slgp->id;
			}
			$slgp=implode(",",$slgps);
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id in ($slgp)
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="CLASS") {
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  itcl_code,itcl_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  amim_code,amim_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id in ($slgp)
						group by  site_code,site_name order by sum(ordd_oamt) asc limit 10";
			}
			
		}
		else{
			if ($type=="SR") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=1 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="TSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,t3.zone_name,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						INNER JOIN tm_zone as t3 ON t2.zone_id=t3.id
						where t1.role_id=2 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="DSM") {
				$query="SELECT t2.aemp_usnm,t2.aemp_name,t2.aemp_mob1,round(sum(t1.dhbd_tamt),0)/1000 amnt  FROM th_dhbd_5 as t1 INNER JOIN tm_aemp as t2 ON t1.aemp_id=t2.id
						where t1.role_id=3 and t1.dhbd_date=curdate() - Interval 1 day and t2.id= t1.aemp_id and t1.lfcl_id=1 AND t2.slgp_id='$emp_slgp_id'
						group by t2.aemp_usnm,t2.aemp_name
						order by sum(t1.dhbd_tamt) asc limit 10";
			}
			else if ($type=="CLASS") {
				$query="SELECT itcl_code,itcl_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  itcl_code,itcl_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="ITEM") {
				$query="SELECT amim_code,amim_name,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  amim_code,amim_name order by sum(ordd_oamt) asc limit 10";
			}
			else if ($type=="OUTLET") {
				$query="SELECT site_code,site_name,site_mob1,zone_name ,sum(ordd_oamt)/1000 amnt FROM order_summary where ordm_date= curdate()- interval 1 day
						AND slgp_id='$emp_slgp_id'
						group by  site_code,site_name order by sum(ordd_oamt) asc limit 10";
			}
			
		}
        DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
        $data= DB::connection($this->db)->select(DB::raw($query));
        return $data;

    }
    public function loadUnderEmployee(Request $request){
		//$date =Carbon\Carbon::now()->format('Y-m-d');
        $this->db = Auth::user()->country()->cont_conn;
		$un_emp=DB::connection($this->db)->select("SELECT
		t4.id 									   AS id,
	  t4.aemp_usnm,                              
	  t4.aemp_name,                              
	  t1.role_id,                                
	  t2.role_name ,
	  t1.dhbd_tvit,
	  t1.dhbd_memo,
	  t1.dhbd_tamt,
	  t1.dhbd_tsit                                
	FROM th_dhbd_5 AS t1
	  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
	  LEFT JOIN th_dhbd_5 AS t3
		ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
	  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
	WHERE t1.aemp_mngr = '$request->emid' AND t1.dhbd_date =curdate()
	UNION ALL
	SELECT
		t4.id 									   AS id,
	  t4.aemp_usnm,                                
	  t4.aemp_name,                             
	  t1.role_id,                                 
	  t2.role_name ,
	  t1.dhbd_tvit,
	  t1.dhbd_memo,
	  t1.dhbd_tamt ,
	  t1.dhbd_tsit                              
	FROM th_dhbd_5 AS t1
	  INNER JOIN tm_role AS t2 ON t1.role_id = t2.id
	  LEFT JOIN th_dhbd_5 AS t3
		ON t1.aemp_id = t3.aemp_id AND t3.dhbd_date = DATE_SUB(curdate(), INTERVAL 1 MONTH)
	  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
	  INNER JOIN th_dhem AS t8 ON t1.aemp_id = t8.dhem_peid
	WHERE t8.dhem_emid = '$request->emid' AND t1.dhbd_date =curdate()");
        //return $un_emp;
		return view('user_basis_dashboard')->with('un_emp',$un_emp);
    
    }
    public function dashboardLoadAccordingToUser($emid) {
    	$this->db = Auth::user()->country()->cont_conn;
		$pt_show=1;
		$pillar_data='';
		$gp_wise_class='';
		$out_type_count='';
		$today_open_out='';
		$visited='';
		$parent_emp=DB::connection($this->db)->select("SELECT t1.aemp_name,t1.aemp_usnm,t2.role_name,t1.role_id,t1.zone_id,t1.slgp_id,t3.slgp_code from tm_aemp t1
		INNER JOIN tm_role t2 ON t1.role_id=t2.id
		INNER JOIN tm_slgp t3 ON t1.slgp_id=t3.id
		where t1.id='$emid'");
    	$date =Carbon\Carbon::now()->format('Y-m-d');
    	  if ($this->db) {
    	            $employee = DB::connection($this->db)->select("SELECT
    	  t1.dhbd_ucnt                                              AS totalSr,
    	  t1.dhbd_prnt                                              AS onSr,
    	  t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
    	  t1.dhbd_pact                                              AS actSr,
    	  t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
    	  t1.dhbd_prdt                                              AS pvSr,
    	  t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
    	  t1.`dhbd_lvsr`                                            AS levSr,
    	  format(ifnull((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
    	  t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
    	  t1.dhbd_memo                                              AS productiveMemo,
    	  t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
    	  t1.dhbd_tsit                                              AS totalScheduleCall,
    	  t1.dhbd_tvit                                              AS total_visited,
    	  t1.dhbd_line                                              AS number_item_line,
    	  round(t1.dhbd_line / t1.dhbd_memo, 2)                     AS lineParCall,
    	  round(t1.dhbd_tsit/ t1.dhbd_ucnt,2)                     	AS avgOutSR,
    	  round(t1.dhbd_memo/ t1.dhbd_pact,2)                     	AS AMSR,
    	  round(t1.dhbd_tvit/ t1.dhbd_pact,2)                     	AS AVSR,
    	  round(t1.dhbd_tamt/ t1.dhbd_pact,2)                     	AS APESR,
    	  round(t1.dhbd_tamt/ t1.dhbd_memo,2)                     	AS APEOIT,
    	  t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
    	  0                                                         AS totalMspTargetCtn,
    	  t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
    	  0                                                         AS totalMspOrderCtn,
    	  t1.dhbd_sblc + t1.dhbd_cblc + t1.dhbd_oblc                AS blockOrder,
    	  t1.dhbd_sbla + t1.dhbd_cbla + t1.dhbd_obla                AS blockOrderAmount,
    	  t1.dhbd_spbg                                              AS supervisorBudgetAmount,
    	  t1.dhbd_spav                                              AS supervisorBudgetAvail,
    	  t1.dhbd_cblc                                              AS creditBlockOrder,
    	  t1.dhbd_cbla                                              AS creditBlockAmount,
    	  t1.dhbd_oblc                                              AS overDueBlockOrder,
    	  t1.dhbd_obla                                              AS overDueBlockAmount,
    	  t1.dhbd_sblc                                              AS specialBlockOrder,
    	  t1.dhbd_sbla                                              AS specialBlockAmount,
    	  t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
    	  t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
    	 
    	  t1.dhbd_time                                              AS updated_at
    	FROM th_dhbd_5 AS t1
    	WHERE t1.aemp_id = '$emid' AND t1.dhbd_date = '$date'");
          }
        if($this->db){
            $data=DB::connection($this->db)->select( "SELECT
    	  t1.dhbd_ucnt                                              AS totalSr,
    	  t1.dhbd_prnt                                              AS onSr,
    	  t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)              AS offSr,
    	  t1.dhbd_pact                                              AS actSr,
    	  t1.dhbd_prnt - t1.dhbd_pact                               AS inactSr,
    	  t1.dhbd_prdt                                              AS pvSr,
    	  t1.dhbd_pact - t1.dhbd_prdt                               AS nveSr,
    	  t1.`dhbd_lvsr`                                            AS levSr,
    	  format(ifnull((t1.dhbd_memo / t1.dhbd_tsit) * 100, 0), 2) AS strikeRate,
    	  t1.dhbd_prnt - t1.dhbd_prdt                               AS nonProductiveSr,
    	  t1.dhbd_memo                                              AS productiveMemo,
    	  t1.dhbd_tvit - t1.dhbd_memo                               AS nonProductiveMemo,
    	  t1.dhbd_tsit                                              AS totalScheduleCall,
    	  t1.dhbd_tvit                                              AS total_visited,
    	  t1.dhbd_line                                              AS number_item_line,
    	  format(ifnull((t1.dhbd_line / t1.dhbd_memo),0), 2)        AS lineParCall,
    	  round(t1.dhbd_tsit/ t1.dhbd_ucnt,2)                     	AS avgOutSR,
    	  round(t1.dhbd_memo/ t1.dhbd_pact,2)                     	AS AMSR,
    	  round(t1.dhbd_tvit/ t1.dhbd_pact,2)                     	AS AVSR,
    	  round(t1.dhbd_tamt/ t1.dhbd_pact,2)                     	AS APESR,
    	  round(t1.dhbd_tamt/ t1.dhbd_memo,2)                     	AS APEOIT,
    	  t1.dhbd_ttar / 1000                                       AS totalTargetAmount,
    	  0                                                         AS totalMspTargetCtn,
    	  t1.dhbd_tamt / 1000                                       AS totalOrderAmount,
    	  t1.dhbd_mtdo / 1000                                       AS mtd_total_sales,
    	  t1.dhbd_mtdd / 1000                                       AS mtd_total_delivery,
          t1.dhbd_date                                              AS date_day,
    	  t1.dhbd_time                                              AS updated_at
    	FROM th_dhbd_5 AS t1
    	WHERE t1.aemp_id = '$emid' AND (t1.dhbd_date <=curdate() AND t1.dhbd_date>=(DATE_SUB(CURDATE(),INTERVAL 6 DAY)))
            ");
        }
		$emp_role=$parent_emp[0]->role_id;
		$month_id=date('Ym');
		if($emp_role==2){
			$slgp_id=$parent_emp?$parent_emp[0]->slgp_id:'';
			$slgp_code=DB::connection($this->db)->select("SELECT slgp_code FROM tm_slgp WHERE id=$slgp_id");
			$slgp_code=$slgp_code[0]->slgp_code;
			$zone_id=$parent_emp[0]->zone_id;
			$zone_code=DB::connection($this->db)->select("SELECT zone_code FROM tm_zone WHERE id='$zone_id'");
			$zone_code=$zone_code[0]->zone_code;
			$pillar_data=DB::connection($this->db)->select("SELECT sg.digr_name,z.zone_name,z.zone_code,sg.company_id,SUM(tg.tgt_amount)total_tgt,
			SUM(tg.achv_amount)total_achv,
			sg.digr_text,
			sum(case when pl.PRIORITY =1 then tg.tgt_amount else 0 end) plv1,
			sum(case when pl.PRIORITY =2 then tg.tgt_amount else 0 end) plv2,
			sum(case when pl.PRIORITY =3 then tg.tgt_amount else 0 end) plv3,
			sum(case when pl.PRIORITY =4 then tg.tgt_amount else 0 end) plv4,
			sum(case when pl.PRIORITY =5 then tg.tgt_amount else 0 end) plv5,
			sum(case when pl.PRIORITY =6 then tg.tgt_amount else 0 end) plv6,
			sum(case when pl.PRIORITY =7 then tg.tgt_amount else 0 end) plv7,
			sum(case when pl.PRIORITY =8 then tg.tgt_amount else 0 end) plv8,
			sum(case when pl.PRIORITY =9 then tg.tgt_amount else 0 end) plv9,
			sum(case when pl.PRIORITY IS NULL then tg.tgt_amount end)other,
			sum(case when pl.PRIORITY =1 then tg.achv_amount else 0 end) aplv1,
			sum(case when pl.PRIORITY =2 then tg.achv_amount else 0 end) aplv2,
			sum(case when pl.PRIORITY =3 then tg.achv_amount else 0 end) aplv3,
			sum(case when pl.PRIORITY =4 then tg.achv_amount else 0 end) aplv4,
			sum(case when pl.PRIORITY =5 then tg.achv_amount else 0 end) aplv5,
			sum(case when pl.PRIORITY =6 then tg.achv_amount else 0 end) aplv6,
			sum(case when pl.PRIORITY =7 then tg.achv_amount else 0 end) aplv7,
			sum(case when pl.PRIORITY =8 then tg.achv_amount else 0 end) aplv8,
			sum(case when pl.PRIORITY =9 then tg.achv_amount else 0 end) aplv9,
			sum(case when pl.PRIORITY IS NULL then tg.achv_amount end)aother
			FROM tbld_new_monthly_target tg
			INNER JOIN tbld_new_sales_group sg on sg.digr_text=tg.digr_text
			INNER JOIN tbld_new_distributor db ON db.dist_code=tg.dist_id
			INNER JOIN tm_zone z on z.zone_code=db.sales_zone_id
			LEFT JOIN tbld_new_pillar pl on pl.DIGR_TEXT=tg.digr_text 
			and pl.ITEM_CLASS_ID=tg.item_class_id and pl.S_Y_M=tg.month_id
			WHERE tg.month_id='$month_id' And z.zone_code='$zone_code' AND sg.digr_text LIKE '%$slgp_code'
			GROUP BY sg.company_id,sg.digr_name,sg.digr_text,z.zone_name,z.zone_code ORDER BY sg.company_id");
	
			$current_date=date('Y-m-d');
			$out_type_count=DB::connection($this->db)->select("SELECT SUM(if(t4.otcg_id IN(1,10),1,0))AS Whole_Sale_site, 
							SUM(if(t4.otcg_id IN(4),1,0))AS Tong_site, 
							SUM(if(t4.otcg_id NOT IN(1,4,10),1,0))AS Trade_M_site
							FROM tm_aemp t1 JOIN tl_rpln t2 ON(t1.id=t2.aemp_id)
							inner JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
							inner JOIN tm_site t4 ON t3.site_id=t4.id
							WHERE t1.aemp_mngr ='$emid' and  t2.rpln_day = dayname('$current_date')
							AND t1.lfcl_id=1 
							AND t4.lfcl_id=1");
			$today_open_out=DB::connection($this->db)->select("select t1.attr3, count(*)opened_outlet_today from tm_ward t1,tm_mktm t5,tm_site_registration t4
			where t1.id=t5.ward_id and t4.mktm_id=t5.id and t4.lfcl_id=1 and t1.attr3='$zone_id'
			group by t1.attr3");
			$visited=DB::connection($this->db)->select("select t1.visited onemonth,t2.visited twomonth,t3.visited threemonth from zone_visited_onemonth as t1,
							zone_visited_twomonth as t2,zone_visited_Threemonth as t3
							where t1.zone_id=t2.zone_id and t2.zone_id=t3.zone_id 
							and t1.slgp_id=t2.slgp_id and t2.slgp_id=t3.slgp_id
							and t1.zone_id=$zone_id and t1.slgp_id=$slgp_id");
			if($pillar_data){
				$gp=array_unique( array_column( $pillar_data , 'digr_text' ) );
				
				$gp_wise_class=DB::connection($this->db)->select("SELECT pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name FROM `tbld_new_pillar` as pl
				INNER JOIN tm_itcl cl on cl.itcl_code=pl.ITEM_CLASS_ID
				where pl.DIGR_TEXT in (".implode(',',$gp).") AND pl.S_Y_M='$month_id' GROUP BY pl.COMPANY_ID,pl.DIGR_TEXT,cl.itcl_name");
				return view('project_board',['data'=>$data,'employee'=>$employee,'emid'=>$emid,'parent_emp'=>$parent_emp,'emp_role'=>$emp_role,'pt_show'=>$pt_show,'pillar_data'=>$pillar_data,
				'gp_wise_class'=>$gp_wise_class,'out_type_count'=>$out_type_count,'today_open_out'=>$today_open_out,'visited'=>$visited]);
			 }
			 else{
				 
				// return view('project_board')->with('data',$data)->with('employee',$employee)->with('emid',$emid)->with('pt_show',$pt_show)
				// ->with('parent_emp',$parent_emp)->with('emp_role',$emp_role)->with('pillar_data',$pillar_data)->with('gp_wise_class',$gp_wise_class);
				return view('project_board',['data'=>$data,'employee'=>$employee,'emid'=>$emid,'parent_emp'=>$parent_emp,'emp_role'=>$emp_role,'pt_show'=>$pt_show,'pillar_data'=>$pillar_data,
				'gp_wise_class'=>$gp_wise_class,'out_type_count'=>$out_type_count,'today_open_out'=>$today_open_out,'visited'=>$visited]);
			 }
			
		}
		else{
			// return view('project_board')->with('data',$data)->with('employee',$employee)->with('emid',$emid)->with('pt_show',$pt_show)->with('parent_emp',$parent_emp)->with('emp_role',$emp_role)
			// 	->with('pillar_data',$pillar_data)->with('gp_wise_class',$gp_wise_class);
			return view('project_board',['data'=>$data,'employee'=>$employee,'emid'=>$emid,'parent_emp'=>$parent_emp,'emp_role'=>$emp_role,'pt_show'=>$pt_show,'pillar_data'=>$pillar_data,
				'gp_wise_class'=>$gp_wise_class,'out_type_count'=>$out_type_count,'today_open_out'=>$today_open_out,'visited'=>$visited]);
		}
    
}

//Dashboard Bottom Off Sr list
public function getOffSRList($id){
	$date=date('Y-m-d');
	$this->db = Auth::user()->country()->cont_conn;
	$role_id=DB::connection($this->db)->select("SELECT role_id from tm_aemp where id=$id");
	$role_id=$role_id[0]->role_id;
	$mngr_list='';
	$list=$id;
	if($role_id<=5 && $role_id>2){
		while($list){
			$list_to_check=DB::connection($this->db)->select("Select aemp_id,role_id from th_dhbd_5 where aemp_mngr in ($list)");
			$list='';
			$cnt=0;
			$cnt1=0;
			foreach($list_to_check as $s){
				if($s->role_id <$role_id){
					if($s->role_id==2){
						if($cnt==0){
							$mngr_list.=$s->aemp_id;
						}
						else{
							$mngr_list.=','.$s->aemp_id;
						}
						$cnt++;
					}else if($s->role_id>2){
						if($cnt1==0){
							$list.=$s->aemp_id;
							
						}else{
							$list.=','.$s->aemp_id;
						}
						$cnt1++;
						
					}
				}
			}
		}
		
	}else{
		$mngr_list=$id;
	}
	//return $mngr_list;
	//return "SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 FROM `th_dhbd_5` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list) AND   t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)>0 AND t1.role_id=1";
	$sr_list=DB::connection($this->db)->select("SELECT t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1 FROM `th_dhbd_5` t1
			INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
			WHERE t1.dhbd_date='$date' AND t1.aemp_mngr in ($mngr_list)
			AND   t1.dhbd_ucnt - (t1.dhbd_prnt+t1.`dhbd_lvsr`)>0 AND t1.role_id=1 group by t2.aemp_name,t2.aemp_usnm,t2.aemp_mob1  ORDER BY t2.aemp_name");
	return $sr_list;
}
//Get category wise visit history
public function getCatWiseOutVisitDashboard($id){
	$this->db = Auth::user()->country()->cont_conn;
	$out_list=DB::connection($this->db)->select("SELECT  
		t4.id,t4.otcg_name,
		sum(CASE WHEN t3.otcg_id=t4.id THEN 1 ELSE 0 END) num
		FROM  th_ssvh t1
		INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
		INNER JOIN tm_site t3 ON t1.site_id=t3.id
		INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
		WHERE t2.aemp_mngr=$id  AND t1.ssvh_date=curdate() AND t1.ssvh_ispd in (1,0)
		GROUP BY t4.id");
	return $out_list;
}
public function getVisitedOutletDetailsDashboard($id,$cat_id){
	$this->db = Auth::user()->country()->cont_conn;
	 $out_list=DB::connection($this->db)->select("SELECT  t3.site_name,t3.site_code,t3.site_mob1,t3.site_adrs
			 FROM  th_ssvh t1
			 INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
			 INNER JOIN tm_site t3 ON t1.site_id=t3.id
			 INNER JOIN tm_otcg t4 ON t3.otcg_id=t4.id
			 WHERE t2.aemp_mngr=$id  AND t1.ssvh_date=curdate() AND t1.ssvh_ispd in (1,0) AND t4.id=$cat_id
			 ORDER BY t3.site_name ASC");
	 return $out_list;
 }
}
