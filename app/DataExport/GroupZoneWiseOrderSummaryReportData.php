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


class GroupZoneWiseOrderSummaryReportData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'GroupWiseData';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($start_date,$end_date,$slgp_id,$zone_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->slgp_id = $slgp_id;
        $instance->zone_id = $zone_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['Group','Zone','Order_Date','Order_Amt'];
    }

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("
SELECT
  t3.slgp_name,
  t4.zone_name,
  t5.ordm_date,
  Sum(t5.ordm_amnt)
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_zone AS t4 ON t2.zone_id = t4.id
  INNER JOIN tt_ordm AS t5 ON t5.slgp_id = t3.id

WHERE t3.id = $this->slgp_id AND t4.id= $this->zone_id AND (t5.ordm_date BETWEEN '$this->start_date'AND '$this->end_date')
GROUP BY
  t3.slgp_name,
  t4.zone_name,
  t5.ordm_date
Order by t5.ordm_date
");
        return collect($dataRow);
       // return collect(null); tt_ordm_d_cur_date
    }


}
