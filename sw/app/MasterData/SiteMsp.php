<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SiteMsp extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_msps';
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
        return ['msp_code', 'slgp_id','site_code'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        DB::connection($this->db)->select("INSERT IGNORE INTO tl_msps SELECT null
                ,t2.id,
                t1.id,'$request->slgp_id',
                '$this->cont_id',1,'$this->aemp_id','$this->aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0
                FROM `tm_site` t1,tm_mspm t2
                WHERE t1.site_code='$request->site_code' AND t2.mspm_code='$request->msp_code'
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
      }
      
    }
}
