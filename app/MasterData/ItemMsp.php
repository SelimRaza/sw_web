<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ItemMsp extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_mspd';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->db = Auth::user()->country()->cont_conn;
        $this->cont_id = Auth::user()->country()->id;
        $this->aemp_id = Auth::user()->employee()->id;
    }
    public function headings(): array
    {
        return ['msp_code', 'item_code','quantity'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        DB::connection($this->db)->select("INSERT IGNORE INTO tm_mspd SELECT null
                ,t2.id,
                t1.id,'$request->quantity',
                '$this->cont_id',1,'$this->aemp_id','$this->aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0
                FROM `tm_amim` t1,tm_mspm t2
                WHERE t1.amim_code='$request->item_code' AND t2.mspm_code='$request->msp_code'
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',mspd_qnty='$request->quantity',updated_at=CURRENT_TIMESTAMP");
      }
      
    }
 
}
