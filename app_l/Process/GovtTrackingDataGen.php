<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
class GovtTrackingDataGen{

    public function slgpVisitMemo($db_name){
        DB::connection($db_name->cont_conn)->select("TRUNCATE TABLE tbl_mktm_slgp_wise_visit_memo");
        $slgp_list=DB::connection($db_name->cont_conn)->select("SELECT id FROM tm_slgp where lfcl_id=1");
        foreach($slgp_list as $slgp){
            $id=$slgp->id;
            DB::connection($db_name->cont_conn)->select("INSERT INTO tbl_mktm_slgp_wise_visit_memo SELECT null, t1.ssvh_date,t2.slgp_id,t4.id mktm_id,
                SUM(CASE WHEN t1.ssvh_ispd=1 THEN 1 ELSE 0 END) memo,
                count(t1.id) visit
                FROM th_ssvh t1
                INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
                INNER JOIN tm_site t3 ON t1.site_id=t3.id
                INNER JOIN tm_mktm t4 On t3.mktm_id=t4.id
                WHERE t1.ssvh_ispd in (1,0) AND t1.ssvh_date=curdate()-INTERVAL 1 DAY AND t2.slgp_id=$id
                GROUP BY t2.slgp_id,t4.id,t1.ssvh_date");
        }
       
    }
    public function mktTotalOlt($db_name){
        DB::connection($db_name->cont_conn)->select("TRUNCATE TABLE tbl_mktm_wise_total_outlet");
        DB::connection($db_name->cont_conn)->select("INSERT IGNORE INTO tbl_mktm_wise_total_outlet SELECT null,(curdate()-Interval 1 Day)mkot_date,
        t2.id as mktm_id,t2.mktm_name,t3.id as ward_id,t3.ward_name,t4.id as than_id,t4.than_name,
            t5.id as dsct_id,t5.dsct_name, count(DISTINCT t1.site_code) total_outlet
            from tm_site t1
            INNER JOIN tm_mktm t2 On t1.mktm_id=t2.id
            INNER JOIN tm_ward t3 ON t2.ward_id=t3.id
            INNER JOIN tm_than t4 ON t3.than_id=t4.id
            INNER JOIN tm_dsct t5 ON t4.dsct_id=t5.id
            WHERE t1.lfcl_id=1
            GROUP BY t2.id,t2.mktm_name,t3.id,t3.ward_name,t4.id,t4.than_name,t5.id,t5.dsct_name");
    }
    public function insertSlgpWiseOlt($db_name){
        DB::connection($db_name->cont_conn)->select("TRUNCATE TABLE tbl_mktm_slgp_wise_outlet");
        $slgp_list=DB::connection($db_name->cont_conn)->select("SELECT id FROM tm_slgp where lfcl_id=1");
        foreach($slgp_list as $slgp){
            $id=$slgp->id;
            DB::connection($db_name->cont_conn)->select("INSERT IGNORE INTO tbl_mktm_slgp_wise_outlet select null, t1.slgp_id,t5.mktm_id, count(DISTINCT t4.site_id) slgp_out
            from tm_aemp t1
            INNER JOIN tl_rpln t3 ON t1.id=t3.aemp_id
            INNER JOIN tl_rsmp t4 ON t3.rout_id=t4.rout_id
            inner join tm_site  t5 on t4.site_id= t5.id
            where t1.slgp_id=$id
            GROUP BY t1.slgp_id, t5.mktm_id");
           
        }
    }
    public function insertProcessedGovtData($db_name){
        $slgp_list=DB::connection($db_name->cont_conn)->select("SELECT id FROM tm_slgp where lfcl_id=1");
        foreach($slgp_list as $slgp){
            $id=$slgp->id;
            DB::connection($db_name->cont_conn)->select("INSERT IGNORE INTO tbl_slgp_vsmo
            SELECT null,t1.ssvh_date,t4.id mktm_id,t4.mktm_name,t5.id ward_id,t5.ward_name,
            t6.id than_id,t6.than_name,t7.id dsct_id,t7.dsct_name,
            t8.id disn_id,t8.disn_name,t2.slgp_id,
            count(t1.id) visit,
            SUM(CASE WHEN t1.ssvh_ispd=1 THEN 1 ELSE 0 END) memo
            FROM th_ssvh t1
            INNER JOIN tm_aemp t2 ON t1.aemp_id=t2.id
            INNER JOIN tm_site t3 ON t1.site_id=t3.id
            INNER JOIN tm_mktm t4 On t3.mktm_id=t4.id
            INNER JOIN tm_ward t5 ON t4.ward_id=t5.id
            INNER JOIn tm_than t6 ON t5.than_id=t6.id
            INNER JOIN tm_dsct t7 ON t6.dsct_id=t7.id
            INNER JOIN tm_disn t8 ON t7.disn_id=t8.id
            WHERE t1.ssvh_ispd in (1,0) AND t1.ssvh_date=curdate()-INTERVAL 1 DAY AND t2.slgp_id=$id
            GROUP BY t2.slgp_id,t4.id,t1.ssvh_date");
        }
    }
}