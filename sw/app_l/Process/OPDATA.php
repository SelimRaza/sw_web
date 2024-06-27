<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Illuminate\Support\Facades\Storage;
class OPDATA
{
    public function insertToItemCov($db_name){
        $text="******Item Coverage Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('item_coverage.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("INSERT IGNORE  INTO `tbl_itm_cov_details`( `ordm_date`, `aemp_id`, `slgp_id`, `amim_id`, `order_qnty`, `order_amnt`, `created_at`, `updated_at`, `attr1`)
            Select 
            t1.ordm_date,t1.aemp_id,t1.slgp_id,t2.amim_id,sum(t2.ordd_qnty)order_qnty,round(sum(t2.ordd_oamt),4)order_amnt,current_timestamp created_at,current_timestamp updated_at,count(DISTINCT t1.site_id) attr
            FROM tt_ordm t1
            INNER JOIN tt_ordd t2 ON t1.id=t2.ordm_id
            WHERE t1.ordm_date=curdate()
            GROUP BY t1.ordm_date,t1.aemp_id,t1.slgp_id,t2.amim_id");
            $text='Item data inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('item_coverage.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('item_coverage.txt',$text);
        }
        
    }
    public function insertMasterOpData($db_name){
        $text="******OPDATA MASTER Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('opdata.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("CALL insertMasterOpData()");
            $text='opdata master inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('opdata.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('opdata.txt',$text);
        }
    }
    public function insertTransactionalOpData($db_name){
        $text="******OPDATA insertTransactionalOpData Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('opdata.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("CALL insertTransactionalOpData()");
            $text='opdata insertTransactionalOpData inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('opdata.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('opdata.txt',$text);
        }
    }
    public function insertTransactionalOpDataYesterday($db_name){
        $text="******OPDATA insertTransactionalOpDataYesterday Starts at:".date('Y-m-d h:i:s').'****************';
        Storage::append('opdata.txt',$text);
        try{
            DB::connection($db_name->cont_conn)->select("CALL insertTransactionalOpDataYesterday()");
            $text='opdata insertTransactionalOpDataYesterday inserted successfully for:-'.$db_name->cont_conn;
            Storage::append('opdata.txt',$text);
            
        }
        catch(\Exception $e){
            $text="Error for:-(".$db_name->cont_conn.')'.$e->getMessage();
            Storage::append('opdata.txt',$text);
        }
    }
    
}

