<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Illuminate\Support\Facades\Storage;
class Productivity
{
    public function insertProductivityData($db_name){
        $text="******Productivity Starts at:".date('Y-m-d h:i:s').'****************';
        DB::connection($db_name->cont_conn)->select("DELETE FROM tt_aemp_summary1 WHERE date='2022-11-29';");
        Storage::append('outlet_coverage.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select(" INSERT INTO `tt_aemp_summary1`(`date`, `zone_id`, `slgp_id`, `aemp_id`,`aemp_code`, `aemp_name`, `aemp_mobile`, `atten_atyp`, `t_outlet`, `v_outlet`,
            `s_outet`, `t_sku`, `t_amnt`, `t_memo`, `rout_id`, `rout_name`, `rpln_day`, `inTime`, `outTime`, `firstOrTime`, `lastOrTime`,ro_visit,npv_min_time,npv_max_time,base_name,role_id)
            SELECT
              t1.attn_date,t1.zone_id,t1.slgp_id,t1.aemp_id,t1.aemp_usnm,t1.aemp_name,t1.aemp_mob1,t1.atten_atyp,
              ifnull(t4.t_out,0)t_out,
              ifnull(t3.visi_out,0)visi_out,
              ifnull(t2.s_out,0)s_out,
              ifnull(t2.t_sku,0)t_sku,
              ifnull(t2.t_amnt,0)t_amnt,
              ifnull(t2.t_memo,0)t_memo,
              t4.rout_id,
              t4.rout_name,
              t4.rpln_day,
              t1.inTime,0 AS outTime,
              ifnull(t2.min_o_time,'00:00:00')min_o_time,
              ifnull(t2.max_o_time,'00:00:00')max_o_time,
              ifnull(t4.ro_visit,0)ro_visit,
              ifnull(t3.npv_min_time,'00:00:00')npv_min_time,ifnull(t3.npv_max_time,'00:00:00')npv_max_time,t4.base_name,
              role_id
            FROM (
                (SELECT
                   t1.`attn_date`,
                   t2.`slgp_id`,
                   t2.zone_id,
                   t1.`aemp_id`,
                   t2.aemp_usnm,
                   t2.aemp_name,
                   t2.aemp_mob1,
                   t1.`atten_atyp`,
                   t2.role_id,
                   MIN(TIME(t1.attn_time)) AS inTime
                 FROM `tt_attn` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id
                 WHERE t1.`attn_date` = '2022-11-29' AND t2.lfcl_id = '1' AND t2.role_id = '1'
                 GROUP BY t1.atten_atyp, t1.aemp_id) AS t1 LEFT JOIN
                (SELECT
                   ordm_date                  AS date,
                   aemp_id,
                   COUNT(`ordm_ornm`)         AS t_memo,
                   COUNT(DISTINCT `site_id`)  AS s_out,
                   sum(`ordm_icnt`)           AS t_sku,
                   round(sum(`ordm_amnt`), 2) AS t_amnt,
                   MIN(TIME(ordm_time))       AS min_o_time,
                   MAX(TIME(ordm_time))       AS max_o_time
                 FROM `tt_ordm`
                 WHERE `ordm_date` = '2022-11-29' and ordm_icnt>0
                 GROUP BY aemp_id) AS t2 ON t1.aemp_id = t2.aemp_id
                LEFT JOIN
                (SELECT
                   `npro_date`               AS date,
                   `aemp_id`,
                   COUNT(DISTINCT `site_id`) AS visi_out,
                   min(TIME(npro_time))      AS npv_min_time,
                   max(TIME(npro_time))      AS npv_max_time
                 FROM `tt_npro` 
                 WHERE npro_date = '2022-11-29' 
                 GROUP BY aemp_id) AS t3 ON t1.aemp_id = t3.aemp_id
                LEFT JOIN
                (SELECT
                   t1.`aemp_id`,
                   t1.rpln_day,
                   t1.rout_id,
                   t3.rout_name,
                   t4.base_name,
                   COUNT(DISTINCT t2.site_id) AS t_out,
                   COUNT(DISTINCT t5.site_id) ro_visit
                    FROM `tl_rpln` t1 
                    INNER JOIN tl_rsmp t2 ON t1.rout_id = t2.rout_id
                   INNER JOIN tm_rout t3 ON t1.rout_id = t3.id
                   INNER JOIN tm_base t4 ON t3.base_id=t4.id
                   LEFT JOIN (SELECT aemp_id,site_id FROM th_ssvh WHERE ssvh_date='2022-11-29' AND ssvh_ispd in (1,0)   GROUP BY aemp_id,site_id ) t5 ON t2.site_id=t5.site_id AND t1.aemp_id=t5.aemp_id
                 WHERE t1.`rpln_day` = DAYNAME('2022-11-29') 
                 GROUP BY t1.aemp_id) t4 ON t1.aemp_id = t4.aemp_id  
            
            )
            GROUP BY t1.aemp_id");
            $text='Productivity data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('outlet_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error productivity for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('outlet_coverage.txt',$text);
        }
        
    }
    
    
}

