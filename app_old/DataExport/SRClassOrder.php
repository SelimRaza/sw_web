<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SRClassOrder extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'GroupWiseData';
    protected $connection = '';
    private $currentUser;

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($topLine, $start_date, $end_date, $acmp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->topLine = $topLine;
        //$instance->topLineID = $topLineID;
        $instance->end_date = $end_date;
        $instance->acmp_id = $acmp_id;
        return $instance;
    }

    public function headings(): array
    {

        return [$this->topLine];
    }

    public function collection()
    {
        /*foreach ($this->topLine as $classList) {
            $res[] = $classList;
        }*/
        $res = array ();
        $cid = $this->topLineID;
        //dd($cid);
        $k=1;
        //$len2 = sizeof($this->topLineID);


        $dataRow = DB::connection($this->connection)->select("SELECT `date`, `order_no`, `site_id`, `aemp_id`, `aemp_code`,
`aemp_name`, `aemp_mob`, `zone_id`, `slgp_id`, `amim_id`, `amim_code`, `amim_name` FROM `tt_aemp_ordd_summ` LIMIT 10
");
        /*$len = sizeof($dataRow);
        for ($i = 0; $i < $len; $i++) {
            $res[$i][] = $k++;
            $res[$i][] = $dataRow[$i]->date;
            $res[$i][] = $dataRow[$i]->zone_name;
            $res[$i][] = $dataRow[$i]->aemp_code;
            $res[$i][] = $dataRow[$i]->aemp_name;
            $aemp_id = $dataRow[$i]->aemp_id;

            for ($j = 0; $j < $len2; $j++) {

                $cDAta = DB::connection($this->connection)->select("SELECT concat(round(SUM(`ordd_oamt`),2),
 ' - ', COUNT(DISTINCT order_id)) as ordr 
FROM `tt_aemp_ordd_summ` WHERE `aemp_id`='$aemp_id' AND itcl_id='$cid[$j]' AND date BETWEEN '$this->start_date' AND '$this->end_date'");
               // $res[$i][] = $dds;
                $res[$i][] = $cDAta[0]->ordr;
                //dd($cDAta);
            }*/
            /*$res[$i][] = $dataRow;

        }*/


        // dd($res);
        return collect($dataRow);
    }


}
