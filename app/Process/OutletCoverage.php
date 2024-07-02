<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class OutletCoverage
{
    public function insertToOltCov($db_name){
        $text="******Outlet Coverage Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('outlet_coverage.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("Insert ignore into tbl_olt_cov_details
                Select
                null,ordm_date,slgp_id,mktm_id,site_id,current_timestamp() created_at,current_timestamp() updated_at,sum(ordm_amnt) ordm_amnt
                FROM 
                (
                Select
                t1.ordm_date ordm_date,t1.slgp_id,t2.mktm_id,t2.id site_id,sum(t1.ordm_amnt)ordm_amnt
                FROM tt_ordm t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.ordm_date=curdate()
                GROUP BY t1.ordm_date,t1.slgp_id,t2.mktm_id,t2.id
                UNION ALL
                Select 
                t1.npro_date ordm_date,t1.slgp_id,t2.mktm_id,t2.id site_id,0 ordm_amnt
                FROM tt_npro t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.npro_date=curdate()
                GROUP BY t1.npro_date,t1.slgp_id,t2.mktm_id,t2.id
                )t
                GROUP BY ordm_date,slgp_id,mktm_id,site_id");
            $text='Visit data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('outlet_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
        }
        
    }
    public function outletDataFeed($db_name){
        $text="******Outlet Coverage Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('outlet_coverage.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("Insert ignore into tbl_olt_cov_details
                Select
                null,ordm_date,slgp_id,mktm_id,site_id,current_timestamp() created_at,current_timestamp() updated_at,sum(ordm_amnt) ordm_amnt
                FROM 
                (
                Select
                t1.ordm_date ordm_date,t1.slgp_id,t2.mktm_id,t2.id site_id,sum(t1.ordm_amnt)ordm_amnt
                FROM tt_ordm t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.ordm_date=curdate()- Interval 1 DAY
                GROUP BY t1.ordm_date,t1.slgp_id,t2.mktm_id,t2.id
                UNION ALL
                Select 
                t1.npro_date ordm_date,t1.slgp_id,t2.mktm_id,t2.id site_id,0 ordm_amnt
                FROM tt_npro t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.npro_date =curdate()- Interval 1 DAY
                GROUP BY t1.npro_date,t1.slgp_id,t2.mktm_id,t2.id
                )t
                GROUP BY ordm_date,slgp_id,mktm_id,site_id");
            $text='Visit data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('outlet_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
        }
        
    }
    public function insertToOltCovAdvanceModule($db_name){
        $text="******Advance Outlet Coverage Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('outlet_coverage.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("DELETE FROM tbl_olt_cov_details WHERE ordm_date=curdate()- Interval 1 DAY");
            DB::connection($db_name->cont_conn)->select(" Insert ignore into tbl_olt_cov_details
            Select
                null,ordm_date,aemp_id,slgp_id,mktm_id,site_id,current_timestamp() created_at,current_timestamp() updated_at,round(sum(ordm_amnt),2) ordm_amnt,memo,'1'
                FROM 
                (
                Select
                t1.ordm_date ordm_date,t1.aemp_id,t1.slgp_id,t2.mktm_id,t2.id site_id,sum(t1.ordm_amnt)ordm_amnt,count(DISTINCT t1.id)memo
                FROM tt_ordm t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.ordm_date=curdate()- Interval 1 DAY
                GROUP BY t1.aemp_id,t1.ordm_date,t1.slgp_id,t2.mktm_id,t2.id
                UNION ALL
                Select 
                t1.npro_date ordm_date,t1.aemp_id,t1.slgp_id,t2.mktm_id,t2.id site_id,0 ordm_amnt,0 memo
                FROM tt_npro t1
                INNER JOIN tm_site t2 ON t1.site_id=t2.id
                WHERE t1.npro_date=curdate()- Interval 1 DAY
                GROUP BY t1.aemp_id,t1.npro_date,t1.slgp_id,t2.mktm_id,t2.id
                )t
                GROUP BY aemp_id,ordm_date,slgp_id,mktm_id,site_id;");
            $text='Visit data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('outlet_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
        }
        
    }
    public function insertRoutedData($db_name){
        try{
            $txt='start-'.date('Y-m-d h:i:s').',,|';
            DB::connection($db_name->cont_conn)->select("
            INSERT INTO tbl_rout_site_count
            SELECT null,p.rpln_day,p.aemp_id,p.staff_id,p.acmp_id,p.acmp_name,p.slgp_id,p.slgp_name,p.zone_id,p.zone_name,p.rout_id,p.site
            FROM
            (SELECT t2.rpln_day,t1.id as aemp_id,t1.aemp_usnm staff_id,
            t5.id as acmp_id,t5.acmp_name as acmp_name,t4.id as slgp_id,t4.slgp_name as slgp_name,t6.id as zone_id,
            t6.zone_name,t2.rout_id rout_id, COUNT(t3.site_id)site
            FROM `tm_aemp` t1
            LEFT JOIN  tl_rpln t2 ON t1.id=t2.aemp_id
            INNER JOIN tl_rsmp t3 ON t2.rout_id=t3.rout_id
            INNER JOIN tm_slgp t4 ON t1.slgp_id=t4.id
            INNER JOIN tm_acmp t5 ON t4.acmp_id=t5.id
            INNER JOIN tm_zone t6 ON t1.zone_id=t6.id
            WHERE t2.rpln_day='Friday' AND  t1.lfcl_id=1 AND t3.lfcl_id=1
            GROUP BY t1.id,t1.aemp_usnm,t2.rpln_day,t5.id,t6.id,t5.acmp_name,t6.id,t6.zone_name,t2.rout_id)p");
            $txt.='End:-'. date('Y-m-d h:i:s');
            Storage::append('outlet_coverage.txt',$txt);
        }catch(\Exception $e){
            $text="Error for:- route site".$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
        }
        
    }
    public function insertOutStat($db_name){
        $text="START outlet_stat :-". date('Y-m-d h:i:s').'\n'; 
            Storage::append('outlet_coverage.txt',$text);
        $start_date='';
        $end_date='';
        $dt=Carbon::now()->startOfWeek(Carbon::SATURDAY);
        //11 Last week
        $start_date_1=$dt->copy()->subDays(7)->format('Y-m-d');
        $end=$dt->copy()->subDays(7);
        $end_date_1=$end->addDays(6)->format('Y-m-d');
        //12 Previous week
        $start_date_2=$dt->copy()->subDays(14)->format('Y-m-d');
        $end_2=$dt->copy()->subDays(14);
        $end_date_2=$end_2->addDays(6)->format('Y-m-d');

        // Min Max ID
        $min_id_1=DB::connection($db_name->cont_conn)->select("SELECT min(id)id FROM tbl_olt_cov_details WHERE ordm_date between '$start_date_1'  AND '$end_date_1' ");
        $max_id_1=DB::connection($db_name->cont_conn)->select("SELECT max(id)id FROM tbl_olt_cov_details WHERE ordm_date between '$start_date_1' AND '$end_date_1'");
        $min_id_1=$min_id_1[0]->id;
        $max_id_1=$max_id_1[0]->id;

        $min_id_2=DB::connection($db_name->cont_conn)->select("SELECT min(id)id FROM tbl_olt_cov_details WHERE ordm_date between '$start_date_2' AND '$end_date_2'");
        $max_id_2=DB::connection($db_name->cont_conn)->select("SELECT max(id)id FROM tbl_olt_cov_details WHERE ordm_date between '$start_date_2' AND '$end_date_2'");
        $min_id_2=$min_id_2[0]->id;
        $max_id_2=$max_id_2[0]->id;
        
        try{
            DB::connection($db_name->cont_conn)->select(" TRUNCATE tbl_out_stat ");
            // Last week visit Data Insert
            $slgp_list=DB::connection($db_name->cont_conn)->select("SELECT id FROM tm_slgp  ORDER BY id asc");
            for($i=0;$i<count($slgp_list);$i++){
                $slgp_id=$slgp_list[$i]->id;
                    DB::connection($db_name->cont_conn)->select("INSERT INTO tbl_out_stat
                    SELECT null,11 TIME_PERIOD,p2.id SLGP_ID,p2.slgp_name SLGP_NAME,t3.than_id THAN_ID,t3.dsct_id DSCT_ID,SUM(slgp_out)SLGP_OUT,SUM(Visited_Sites)Visited_Sites,
                    IF(SUM(slgp_out)-SUM(Visited_Sites)<1,0,SUM(slgp_out)-SUM(Visited_Sites))ZERO_VISIT,
                    SUM(ONE_VISIT)ONE_VISIT,
                    SUM(TWO_VISIT)TWO_VISIT,SUM(THREE_VISIT)THREE_VISIT,SUM(FOUR_VISIT)FOUR_VISIT,1 VISIT_FALG, current_timestamp() CREATED_AT
                        FROM tbl_mktm_slgp_wise_outlet p1 
                        inner join tm_slgp p2 ON p1.slgp_id=p2.id
                        LEFT JOIN (Select 
                                    slgp_id,t1.mktm_id,count(site_id) Visited_Sites, 
                                    SUM(CASE WHEN t1.visit=1 THEN 1 ELSE 0 END) ONE_VISIT,
                                    SUM(CASE WHEN t1.visit=2 THEN 1 ELSE 0 END) TWO_VISIT,
                                    SUM(CASE WHEN t1.visit=3 THEN 1 ELSE 0 END) THREE_VISIT,
                                    SUM(CASE WHEN t1.visit=4 THEN 1 ELSE 0 END) FOUR_VISIT
                                    FROM 
                                    (SELECT slgp_id,mktm_id,site_id,count(site_id) visit
                                    FROM tbl_olt_cov_details t1
                                    WHERE id between $min_id_1 AND $max_id_1 AND ordm_date between '$start_date_1' AND '$end_date_1' AND slgp_id=$slgp_id
                                    GROUP BY slgp_id,mktm_id,site_id
                                    )t1
                                    WHERE t1.visit<5
                                    GROUP BY slgp_id,t1.mktm_id
                                    ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                        INNER JOIN tbl_gvt_hierarchy t3 ON p1.mktm_id=t3.mktm_id
                        WHERE p1.slgp_id=$slgp_id
                        GROUP BY p2.id ,p2.slgp_name,t3.than_id,t3.dsct_id;");
                    // Previous Week Visit Data Insert
                    DB::connection($db_name->cont_conn)->select("INSERT INTO tbl_out_stat
                    SELECT null,12 TIME_PERIOD,p2.id SLGP_ID,p2.slgp_name SLGP_NAME,t3.than_id THAN_ID,t3.dsct_id DSCT_ID,SUM(slgp_out)SLGP_OUT,SUM(Visited_Sites)Visited_Sites,
                    IF(SUM(slgp_out)-SUM(Visited_Sites)<1,0,SUM(slgp_out)-SUM(Visited_Sites))ZERO_VISIT,
                    SUM(ONE_VISIT)ONE_VISIT,
                    SUM(TWO_VISIT)TWO_VISIT,SUM(THREE_VISIT)THREE_VISIT,SUM(FOUR_VISIT)FOUR_VISIT,1 VISIT_FALG, current_timestamp() CREATED_AT
                        FROM tbl_mktm_slgp_wise_outlet p1 
                        inner join tm_slgp p2 ON p1.slgp_id=p2.id
                        LEFT JOIN (Select 
                                    slgp_id,t1.mktm_id,count(site_id) Visited_Sites, 
                                    SUM(CASE WHEN t1.visit=1 THEN 1 ELSE 0 END) ONE_VISIT,
                                    SUM(CASE WHEN t1.visit=2 THEN 1 ELSE 0 END) TWO_VISIT,
                                    SUM(CASE WHEN t1.visit=3 THEN 1 ELSE 0 END) THREE_VISIT,
                                    SUM(CASE WHEN t1.visit=4 THEN 1 ELSE 0 END) FOUR_VISIT
                                    FROM 
                                    (SELECT slgp_id,mktm_id,site_id,count(site_id) visit
                                    FROM tbl_olt_cov_details t1
                                    WHERE id between $min_id_2 AND $max_id_2 AND ordm_date between '$start_date_2' AND '$end_date_2' AND slgp_id=$slgp_id
                                    GROUP BY slgp_id,mktm_id,site_id
                                    )t1
                                    WHERE t1.visit<5
                                    GROUP BY slgp_id,t1.mktm_id
                                    ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                        INNER JOIN tbl_gvt_hierarchy t3 ON p1.mktm_id=t3.mktm_id
                        WHERE p1.slgp_id=$slgp_id
                        GROUP BY p2.id ,p2.slgp_name,t3.than_id,t3.dsct_id;");
                    // Last week ORDER Data Insert
                    DB::connection($db_name->cont_conn)->select("INSERT INTO tbl_out_stat
                    SELECT null,11 TIME_PERIOD,p2.id SLGP_ID,p2.slgp_name SLGP_NAME,t3.than_id THAN_ID,t3.dsct_id DSCT_ID,SUM(slgp_out)SLGP_OUT,SUM(Visited_Sites)Visited_Sites,
                    IF(SUM(slgp_out)-SUM(Visited_Sites)<1,0,SUM(slgp_out)-SUM(Visited_Sites))ZERO_VISIT,
                    SUM(ONE_VISIT)ONE_VISIT,
                    SUM(TWO_VISIT)TWO_VISIT,SUM(THREE_VISIT)THREE_VISIT,SUM(FOUR_VISIT)FOUR_VISIT,2 VISIT_FALG, current_timestamp() CREATED_AT
                        FROM tbl_mktm_slgp_wise_outlet p1 
                        inner join tm_slgp p2 ON p1.slgp_id=p2.id
                        LEFT JOIN (Select 
                                    slgp_id,t1.mktm_id,count(site_id) Visited_Sites, 
                                    SUM(CASE WHEN t1.visit=1 THEN 1 ELSE 0 END) ONE_VISIT,
                                    SUM(CASE WHEN t1.visit=2 THEN 1 ELSE 0 END) TWO_VISIT,
                                    SUM(CASE WHEN t1.visit=3 THEN 1 ELSE 0 END) THREE_VISIT,
                                    SUM(CASE WHEN t1.visit=4 THEN 1 ELSE 0 END) FOUR_VISIT
                                    FROM 
                                    (SELECT slgp_id,mktm_id,site_id,count(site_id) visit
                                    FROM tbl_olt_cov_details t1
                                    WHERE id between $min_id_1 AND $max_id_1 AND ordm_date between '$start_date_1' AND '$end_date_1' AND slgp_id=$slgp_id AND ordm_amnt>0
                                    GROUP BY slgp_id,mktm_id,site_id
                                    )t1
                                    WHERE t1.visit<5
                                    GROUP BY slgp_id,t1.mktm_id
                                    ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                        INNER JOIN tbl_gvt_hierarchy t3 ON p1.mktm_id=t3.mktm_id
                        WHERE p1.slgp_id=$slgp_id
                        GROUP BY p2.id ,p2.slgp_name,t3.than_id,t3.dsct_id;");
                    // Previous week ORDER Data Insert
                    DB::connection($db_name->cont_conn)->select("INSERT INTO tbl_out_stat
                    SELECT null,12 TIME_PERIOD,p2.id SLGP_ID,p2.slgp_name SLGP_NAME,t3.than_id THAN_ID,t3.dsct_id DSCT_ID,SUM(slgp_out)SLGP_OUT,SUM(Visited_Sites)Visited_Sites,
                    IF(SUM(slgp_out)-SUM(Visited_Sites)<1,0,SUM(slgp_out)-SUM(Visited_Sites))ZERO_VISIT,
                    SUM(ONE_VISIT)ONE_VISIT,
                    SUM(TWO_VISIT)TWO_VISIT,SUM(THREE_VISIT)THREE_VISIT,SUM(FOUR_VISIT)FOUR_VISIT,2 VISIT_FALG, current_timestamp() CREATED_AT
                        FROM tbl_mktm_slgp_wise_outlet p1 
                        inner join tm_slgp p2 ON p1.slgp_id=p2.id
                        LEFT JOIN (Select 
                                    slgp_id,t1.mktm_id,count(site_id) Visited_Sites, 
                                    SUM(CASE WHEN t1.visit=1 THEN 1 ELSE 0 END) ONE_VISIT,
                                    SUM(CASE WHEN t1.visit=2 THEN 1 ELSE 0 END) TWO_VISIT,
                                    SUM(CASE WHEN t1.visit=3 THEN 1 ELSE 0 END) THREE_VISIT,
                                    SUM(CASE WHEN t1.visit=4 THEN 1 ELSE 0 END) FOUR_VISIT
                                    FROM 
                                    (SELECT slgp_id,mktm_id,site_id,count(site_id) visit
                                    FROM tbl_olt_cov_details t1
                                    WHERE id between $min_id_2 AND $max_id_2 AND ordm_date between '$start_date_2' AND '$end_date_2' AND slgp_id=$slgp_id AND ordm_amnt>0
                                    GROUP BY slgp_id,mktm_id,site_id
                                    )t1
                                    WHERE t1.visit<5
                                    GROUP BY slgp_id,t1.mktm_id
                                    ) p3 ON p1.mktm_id=p3.mktm_id AND p1.slgp_id=p3.slgp_id
                        INNER JOIN tbl_gvt_hierarchy t3 ON p1.mktm_id=t3.mktm_id
                        WHERE p1.slgp_id=$slgp_id
                        GROUP BY p2.id ,p2.slgp_name,t3.than_id,t3.dsct_id;");
            }

            $text="END outlet_stat DONE :-". date('Y-m-d h:i:s').'\n'; 
            Storage::append('outlet_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:- outlet_stat".$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
          
        }
        
    }
    public function insertAssetData($db_name){
        $text="******asset Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('asset.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("CALL sp_InsertAssetData");
            $text='asset data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('asset.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('asset.txt',$text);
        }
        
    }

    

    
}

