<?php

namespace App\MasterData;

use App\MasterData\Site;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DmCollectionCnUpload extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'dm_collection';
    private $currentUser;
    private $db;
    private $cont_id;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
        }
    }

    public function headings(): array
    {
        return [
            'aemp_usnm',
            'site_code',
            'coll_amt',
            'slgp_id',
            'wh_code',
            'account_code',
            'coll_note',
            'coll_date',
        ];
    }

    public function array(array $array)
    {
        
        $data = $array;
        $insert = [];
        // dd($data);
        foreach ($data as $item) {

            $employee = Employee::on($this->db)->where(['aemp_usnm' => $item['aemp_usnm']])->first();
			$site = Site::on($this->db)->where(['site_code' => $item['site_code']])->first();

            $dt = date('Ymd-Hi');
 			$coll_type = '5';
                    
 			$now = now();
			$last_3 = substr($now->timestamp . $now->milli, 10);
			$CN_NUMBER = "CN" . $coll_type . $dt . '-' . $last_3;

            
                        
            if($employee != null && $site != null && $item['coll_amt'] != null && $item['coll_date'] != null){
                    $insert[] = [                        
                        'ACMP_CODE' => '01',
                        'COLL_DATE' => $item['coll_date'],
                        'COLL_NUMBER' => $CN_NUMBER,
                        'IBS_INVOICE' => "N",
                        'AEMP_ID' => $employee->id,
                        'AEMP_USNM' => $employee->aemp_usnm,
                        'DM_CODE' => $employee->aemp_usnm,
                        'WH_ID' => $item['wh_code'],
                        'SITE_ID' => $site->id,
                        'SITE_CODE' => $site->site_code,
                        'COLLECTION_AMNT' => floatval($item['coll_amt']),
                        'COLLECTION_TYPE' => "CN",
                        'STATUS' => 11,
                        'INVT_ID' => 5,
                        'slgp_id' => $item['slgp_id'],
                        'COLL_NOTE' => $item['coll_note'],
                        'BANK_NAME' => $item['account_code'],
                        
                    ];
                
            }
            if (count($insert) >= 50) {
                DB::connection($this->db)->table('dm_collection')->insertOrIgnore($insert);
                $insert = [];
            }
        }
        if (!empty($insert)) {
            DB::connection($this->db)->table('dm_collection')->insertOrIgnore($insert);
        }
                    
    }

}
