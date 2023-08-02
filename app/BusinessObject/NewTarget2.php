<?php

namespace App\BusinessObject;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\MasterData\SKU;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
//,WithChunkReading,ShouldQueue,WithBatchInserts,WithEvents
class NewTarget2 extends Model implements  WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tt_trgt';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    // public static function create($trgt_date,$priceList)
    // {
    //     $instance = new self();
    //     $instance->year = date('Y', strtotime($trgt_date));
    //     $instance->month = date('m', strtotime($trgt_date));
    //     $instance->priceList = $priceList;
    //     return $instance;
    // }

    // public function collection()
    // {
    //     $target_body_all = array('1001','4','1','12','2023','01','319');
    //     $targetItem = DB::connection($this->connection)->select("SELECT
    //                     t4.itcg_name                AS category,
    //                     t3.itsg_name                AS sub_category,
    //                     t2.plmt_id                  AS price_list,
    //                     t2.pldt_tppr * t2.amim_duft AS ctn_price,
    //                     t1.id                       AS item_id,
    //                     t1.amim_code,
    //                     t1.amim_name                AS item_name,
    //                     t2.amim_duft                AS ctn_size,
    //                     1                           AS ctn_qty
    //                     FROM tm_amim AS t1
    //                     INNER JOIN tm_pldt AS t2 ON t1.id = t2.amim_id
    //                     INNER JOIN tm_itsg AS t3 ON t1.itsg_id = t3.id
    //                     INNER JOIN tm_itcg AS t4 ON t3.itcg_id = t4.id
    //                     WHERE t2.plmt_id = $this->priceList AND t1.lfcl_id = 1 and t2.pldt_tppr>0");
    //     foreach ($targetItem as $targetItem1) {
    //         $target_body = array($targetItem1->ctn_price, $targetItem1->item_id,$targetItem1->amim_code,
    //         $targetItem1->ctn_size, $this->year, $this->month);
    //         array_push($target_body_all, $target_body);

    //     }
    //     return collect([
    //         $target_body_all

    //     ]);
    // }

    public function headings(): array
    {
        return ['sr_auto_id','supervisor_auto_id','sku_id','ctn_size','ctn_price', 'year', 'month','ctn_qty'];
        
    }
    public function array(array $array)
    {
        $error_log='';
        $check=1;
        try{
            $data=$array;
            
            for($i=0;$i<count($data);$i++){
                //$error_log=$data[i];
                $check++;
                $target = NewTarget2::on($this->connection)->where([
                            'trgt_year' =>$data[$i]['year'],
                            'trgt_mnth' => $data[$i]['month'],
                            'aemp_vusr' => $data[$i]['supervisor_auto_id'],
                            'aemp_susr' => $data[$i]['sr_auto_id'],
                            'amim_id' => $data[$i]['sku_id']
                            ])->first();
                if($target==null){
                    $insert[] = [
                        'trgt_year' =>$data[$i]['year'],
                        'trgt_mnth' => $data[$i]['month'],
                        'aemp_vusr' => $data[$i]['supervisor_auto_id'],
                        'aemp_susr' => $data[$i]['sr_auto_id'],
                        'amim_id' => $data[$i]['sku_id'],
                        'trgt_tqty' => $data[$i]['ctn_qty'] * $data[$i]['ctn_size'],
                        'trgt_tamt' => $data[$i]['ctn_qty'] * $data[$i]['ctn_price'],
                        'trgt_rqty' => $data[$i]['ctn_qty']* $data[$i]['ctn_size'],
                        'trgt_ramt' => $data[$i]['ctn_qty'] * $data[$i]['ctn_price'],
                        'cont_id' => $this->currentUser->employee()->cont_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $this->currentUser->employee()->id,
                        'aemp_eusr' => $this->currentUser->employee()->id,
                    ];
                }else {
                    $target->trgt_rqty =$data[$i]['ctn_qty'] * $data[$i]['ctn_size'];
                    $target->trgt_ramt = $data[$i]['ctn_qty'] * $data[$i]['ctn_price'];
                    $target->aemp_eusr = $this->currentUser->employee()->id;
                    $target->save();
                }
                
            }
            if (!empty($insert)) {
                //dd($insert);
                foreach (array_chunk($insert,1000) as $t)  
                    {
                       // DB::connection($this->connection)->table('tt_trgt')->insert($t);
                       DB::connection($this->connection)->table('tt_trgt')->insertOrIgnore($t);

                    }
                
            }
        }
        catch(\Exception $e){

            dd($e->getMessage());
        }
        
       
  

    }
    // public function array(array $array)
    // {
    //     try{
    //         foreach ($array as $row) {
    //             $newArray = array_keys($row);
    //             for ($i =6; $i < count($newArray); $i++) {
    //                 $employee=$this->getEmpAutoId($newArray[$i]);
    //                 $manager=$employee?$employee->aemp_mngr:'';
    //                 $sr_id=$employee?$employee->id:'';
    //                 $amim=$this->getAmimId($row['sku_code']);
    //                 if($employee && $amim){
    //                     $amim_id=$amim->id;
    //                     $amim_duft=$amim->amim_duft;
    //                     $target = NewTarget::on($this->connection)->where([
    //                         'trgt_year' => $row['year'],
    //                         'trgt_mnth' => $row['month'],
    //                         'aemp_vusr' => $manager,
    //                         'aemp_susr' => $sr_id,
    //                         'amim_id' => $amim_id])->first();
    //                    // dd($this->currentUser->employee()->id);
    //                     if ($target == null) {
    //                         $insert[] = [
    //                             'trgt_year' => $row['year'],
    //                             'trgt_mnth' => $row['month'],
    //                             'aemp_vusr' => $manager,
    //                             'aemp_susr' => $sr_id,
    //                             'amim_id'   => $amim_id,
    //                             'trgt_tqty' => $row[$newArray[$i]] * $amim_duft,
    //                             'trgt_tamt' => $row[$newArray[$i]] * $row['ctn_price'],
    //                             'trgt_rqty' => $row[$newArray[$i]] * $amim_duft,
    //                             'trgt_ramt' => $row[$newArray[$i]] * $row['ctn_price'],
    //                             'cont_id' => $this->currentUser->employee()->cont_id,
    //                             'lfcl_id' => 1,
    //                             'aemp_iusr' => $this->currentUser->employee()->id,
    //                             'aemp_eusr' => $this->currentUser->employee()->id,
    //                         ];
    //                     } else {
    //                         $target->trgt_rqty = $row[$newArray[$i]] * $amim_duft;
    //                         $target->trgt_ramt = $row[$newArray[$i]] * $row['ctn_price'];
    //                         $target->aemp_eusr = $this->currentUser->employee()->id;
    //                         $target->save();
    //                     }
    //                 }
                    
    //             }
    //         }
    //         if (!empty($insert)) {
    //             //dd($insert);
    //             DB::connection($this->connection)->table('tt_trgt5')->insert($insert);
    //         }
    //     }
    //     catch(\Exception $e){
    //         dd($e->getMessage());
    //     }

    // }
    // public function chunkSize(): int
    // {
    //     return 1000;
    // }
    // public function batchSize(): int
    // {
    //     return 1000;
    // }
    public function getEmpAutoId($aemp_usnm){
        return Employee::on($this->connection)->where(['aemp_usnm'=>$aemp_usnm])->first();
    }
    public function getAmimId($amim_code){
        $amim= SKU::on($this->connection)->where(['amim_code'=>$amim_code])->first();
        return $amim;
          
    }
    // public function registerEvents(): array
    // {
    //     return [
    //         ImportFailed::class => function(ImportFailed $event) {
    //             $this->importedBy->notify(new ImportHasFailedNotification);
    //         },
    //     ];
    // }
    
}
