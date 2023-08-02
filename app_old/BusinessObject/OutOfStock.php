<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/3/2018
 * Time: 5:11 PM
 */

namespace App\BusinessObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutOfStock extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tt_outs';

    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['dlrm_code', 'item_code', 'status'];
    }

    public function model(array $row)
    {
        $date=date('Y-m-d');
        $value = (object)$row;
        try {
            $depo_id = $this->getDepoId($value->depo_code);
            $item_id = $this->getItemId($value->item_code);

            $exist = OutOfStock::on($this->connection)->where(['dpot_id' => $depo_id,'amim_id' => $item_id])->first();

            if (!$exist && isset($value->status) && $value->status == 1 && $item_id !='') {
                $outofstock = new OutOfStock();
                $outofstock->setConnection($this->connection);
                $outofstock->dpot_id = $depo_id;
                $outofstock->amim_id = $item_id;

                $outofstock->lfcl_id = 1;
                $outofstock->cont_id = $this->currentUser->employee()->cont_id;
                $outofstock->aemp_iusr = $this->currentUser->employee()->id;
                $outofstock->aemp_eusr = $this->currentUser->employee()->id;
                $outofstock->save();
                DB::connection($this->db)->select("INSERT INTO tl_stock_out_log (sout_date,depot_id,amim_id,aemp_iusr,sout_type,source)
                                     VALUES('$date','$exist->dpot_id','$exist->amim_id','$aemp_id','ADD','W')");
            }else{
                if(!empty($exist) && isset($value->status) && $value->status == 2) {
                    $exist->delete();
                    DB::connection($this->db)->select("INSERT INTO tl_stock_out_log (sout_date,depot_id,amim_id,aemp_iusr,sout_type,source)
                                     VALUES('$date','$exist->dpot_id','$exist->amim_id','$aemp_id','DEL','W')");
                }
            }
        }catch(\Exception $e)
        {
            return;
        }
    }


    public function getDepoId($dpot_code)
    {
        return DB::connection($this->connection)->table('tm_dlrm')->where('dlrm_code', $dpot_code)->value('id');
    }

    public function getItemId($item_code)
    {
        return DB::connection($this->connection)->table('tm_amim')->where('amim_code', $item_code)->value('id');
    }
}
