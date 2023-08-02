<?php

namespace App\BusinessObject;

use App\MasterData\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanySiteBalance extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_stcm';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['site_code', 'company_id','group_id', 'price_list_id', 'credit_limit', 'credit_days', 'credit_fixed', 'payment_type'];
    }

    public function array(array $array)
    {
       // dd($array);
        foreach ($array as $row) {
         //   dd($row);

            $site = Site::on($this->connection)->where('cont_id', '=', $this->currentUser->country()->id)->where('site_code', '=', $row['site_code'])->first();
            if ($site!=null){
                $companySiteBalance = CompanySiteBalance::on($this->connection)->where([
                    'site_id' => $site->id,
                    'acmp_id' => $row['group_id'],])->first();
                if ($companySiteBalance == null) {
                    $insert[] = [
                        'site_id' => $site->id,
                        'acmp_id' => $row['company_id'],
                        'slgp_id' => $row['group_id'],
                        'plmt_id' => $row['price_list_id'],
                        'stcm_isfx' => $row['credit_fixed'],
                        'optp_id' => $row['payment_type'],
                        'stcm_limt' => $row['credit_limit'],
                        'stcm_days' => $row['credit_days'],
                        'stcm_ordm' => 0,
                        'stcm_duea' => 0,
                        'stcm_odue' => 0,
                        'stcm_pnda' => 0,
                        'stcm_cpnd' => 0,
                        'lfcl_id' => 1,
                        'cont_id' => $this->currentUser->employee()->cont_id,
                        'aemp_iusr' => $this->currentUser->employee()->id,
                        'aemp_eusr' => $this->currentUser->employee()->id,
                    ];
                } else {
                    $companySiteBalance->plmt_id = $row['price_list_id'];
                    $companySiteBalance->optp_id = $row['payment_type'];
                    $companySiteBalance->stcm_limt = $row['credit_limit'];
                    $companySiteBalance->stcm_days = $row['credit_days'];
                    $companySiteBalance->stcm_isfx = $row['credit_fixed'] == 1 ? '1' : 0;
                    $companySiteBalance->aemp_eusr = $this->currentUser->employee()->id;
                    $companySiteBalance->save();
                }
            }


            // dd($companySiteBalance);

          //  dd($companySiteBalance);
        }
        //dd($insert);
        // Insert or ignore the data in chunks of 400
        if (!empty($insert)) {
            $chunks = array_chunk($insert, 500);
            foreach ($chunks as $chunk) {
                DB::connection($this->connection)->table('company_site_balances')->insertOrIgnore($chunk);
            }
        }

    }
}
