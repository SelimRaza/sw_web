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

class Target extends Model implements FromCollection, WithHeadings, ToArray, WithHeadingRow
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

    public static function create($manager_id, $trgt_date,$priceList)
    {
        $instance = new self();
        $instance->manager_id1 = $manager_id;
        $instance->year = date('Y', strtotime($trgt_date));
        $instance->month = date('m', strtotime($trgt_date));
        $instance->priceList = $priceList;
        return $instance;
    }

    public function collection()
    {
        $target_body_all = array();
        $targetItem = DB::connection($this->connection)->select("SELECT
                        t4.itcg_name                AS category,
                        t3.itsg_name                AS sub_category,
                        t2.plmt_id                  AS price_list,
                        t2.pldt_tppr * t2.amim_duft AS ctn_price,
                        t1.id                       AS item_id,
                        t1.amim_name                AS item_name,
                        t2.amim_duft                AS ctn_size,
                        1                           AS ctn_qty
                        FROM tm_amim AS t1
                        INNER JOIN tm_pldt AS t2 ON t1.id = t2.amim_id
                        INNER JOIN tm_itsg AS t3 ON t1.itsg_id = t3.id
                        INNER JOIN tm_itcg AS t4 ON t3.itcg_id = t4.id
                        WHERE t2.plmt_id = $this->priceList AND t1.lfcl_id = 1 and t2.pldt_tppr>0");
        $mngr_id = $this->manager_id1->id;
        foreach ($targetItem as $targetItem1) {
            $target_body = array($targetItem1->category, $targetItem1->sub_category, $targetItem1->price_list, $targetItem1->ctn_price, $targetItem1->item_id,
             $targetItem1->item_name, $targetItem1->ctn_size, $this->year, $this->month, $this->manager_id1->id, $targetItem1->ctn_qty);


            array_push($target_body_all, $target_body);

        }
        return collect([
            $target_body_all

        ]);
    }

    public function headings(): array
    {
        $target_header = array('category', 'sub_category', 'price_list', 'ctn_price', 'sku_id', 'sku_name', 'ctn_size', 'year', 'month', 'supervisor_id','ctn_qty');
         return $target_header;
    }

    public function array(array $array)
    {
        $data=array();
        $data=$array;
        $employee='';
        for($i=0;$i<count($data);$i++){
            if($i==0){
                $employee=Employee::on($this->connection)->where('aemp_mngr', '=',$data[$i]['supervisor_id'])->orderBy('id')->get();
            }

            foreach($employee as $emp){
                $target = Target::on($this->connection)->where([
                    'trgt_year' =>$data[$i]['year'],
                    'trgt_mnth' => $data[$i]['month'],
                    'aemp_vusr' => $data[$i]['supervisor_id'],
                    'aemp_susr' => $emp->id,
                    'amim_id' => $data[$i]['sku_id'],])->first();
                if($target==null){
                    $insert[] = [
                        'trgt_year' =>$data[$i]['year'],
                        'trgt_mnth' => $data[$i]['month'],
                        'aemp_vusr' => $data[$i]['supervisor_id'],
                        'aemp_susr' => $emp->id,
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

        }
        if (!empty($insert)) {
            DB::connection($this->connection)->table('tt_trgt')->insert($insert);
        }

       
  

    }
}
