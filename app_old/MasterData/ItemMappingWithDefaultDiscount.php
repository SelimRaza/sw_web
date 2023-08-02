<?php

namespace App\MasterData;

use App\MasterData\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemMappingWithDefaultDiscount extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_dfim';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';


    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return ['item_code', 'default_disc_code','default_disc'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        DB::connection($this->db)->select("INSERT IGNORE INTO tm_dfim SELECT null
                ,t2.id dfdm_id,
                t1.id,'$request->default_disc',
                '$this->cont_id',1,'$this->aemp_id','$this->aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0
                FROM 
                `tm_amim` t1,tm_dfdm t2
                WHERE t1.amim_code='$request->item_code' AND t2.dfdm_code='$request->default_disc_code'
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',dfim_disc='$request->default_disc',updated_at=CURRENT_TIMESTAMP");
      }
      
    }
}
