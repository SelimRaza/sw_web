<?php

namespace App\Mapping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SitePartyMapping extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_site_party_mapping_spro';
    private $currentUser;
    private $db;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'site_code',
            'mother_site_code',
            'mother_site_name',
            'billing_address',
        ];
    }

    public function array(array $array)
    {
        
        $data = $array;
        $insert = [];

        foreach ($data as $item) {
            $partyMapping = SitePartyMapping::on($this->db)->where(['site_code' => $item['site_code'], 'mother_site_code' => $item['mother_site_code']])->first();
            if ($partyMapping == null) {
                $insert[] = [
                    'site_code' => $item['site_code'],
                    'mother_site_code' => $item['mother_site_code'],
                    'mother_site_name' => $item['mother_site_name'],
                    // 'billing_address ' => NULL,
                ];
            }            
            
            if (count($insert) >= 100) {
                DB::connection($this->db)->table('tl_site_party_mapping_spro')->insert($insert);
                $insert = [];
            }
        }
        if (!empty($insert)) {
            DB::connection($this->db)->table('tl_site_party_mapping_spro')->insert($insert);
        }


            
    }
}
