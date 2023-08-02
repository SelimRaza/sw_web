<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SiteMappingWithDefaultDiscount extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_dfsm';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return [
            'site_code',
            'default_disc_code',
            'slgp_id'
        ];
    }

    public function model(array $value)
    {
        $request=(object)$value;
        DB::connection($this->db)->select("INSERT IGNORE INTO tl_dfsm SELECT null
                ,t2.id dfdm_id,
                t1.id,'$request->slgp_id',
                '$this->cont_id',1,'$this->aemp_id','$this->aemp_id',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,1,'-','-',0,0
                FROM 
                `tm_site` t1,tm_dfdm t2
                WHERE t1.site_code='$request->site_code' AND t2.dfdm_code='$request->default_disc_code'
                ON DUPLICATE KEY UPDATE aemp_eusr='$this->aemp_id',updated_at=CURRENT_TIMESTAMP");
    }

}
