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

class AttendanceData extends Model implements FromCollection, WithHeadings, WithHeadingRow
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

    public static function create($start_date, $end_date, $acmp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->acmp_id = $acmp_id;
        return $instance;
    }

    public function headings(): array
    {

        return ['Date', 'Group', 'Region', 'Zone', 'User_ID',
            'User_Name', 'Mobile', 'Designation',  'Task_Qty.', 'In_Time',
            'Status', 'Out_Time', 'Status'];
    }

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("
SELECT
  t2.attn_date,
  t4.slgp_name,
  t10.dirg_name,
  t5.zone_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t6.edsg_name,
  count( DISTINCT t7.id)      AS note_cont,
  min(t2.attn_time) AS start_time,
   if(t11.id=1, 'Start Work', t11.atyp_name) AS start_status,
  max(t2.attn_time)                               AS end_time,
   if(t11.id=1, 'End Work', t11.atyp_name)   AS end_status
FROM tm_aemp AS t1
  LEFT JOIN tt_attn AS t2 ON t1.id = t2.aemp_id AND t2.attn_date BETWEEN '$this->start_date' and '$this->end_date'
  INNER JOIN tl_sgsm AS t3 ON t1.id = t3.aemp_id
  INNER JOIN tm_slgp AS t4 ON t3.slgp_id = t4.id
  INNER JOIN tm_zone AS t5 ON t3.zone_id = t5.id
  INNER JOIN tm_edsg AS t6 ON t1.edsg_id = t6.id
  LEFT JOIN tt_note AS t7 ON t1.id = t7.aemp_id and t7.note_date BETWEEN '$this->start_date' and '$this->end_date'
  LEFT JOIN tl_rpln AS t8 ON t1.id = t8.aemp_id AND t8.rpln_day = dayname(t2.attn_date)
  LEFT JOIN tm_rout AS t9 ON t8.rout_id = t9.id
  LEFT JOIN tm_dirg AS t10 ON t5.dirg_id = t10.id
   LEFT JOIN tm_atyp t11 ON t2.atten_atyp=t11.id
WHERE t4.acmp_id = $this->acmp_id and t1.lfcl_id=1
GROUP BY t2.attn_date, t4.slgp_name, t5.zone_name, t1.aemp_usnm,
  t1.aemp_name, t1.aemp_mob1, t6.edsg_name,  t10.dirg_name,t11.id
");

        return collect($dataRow);
    }


}
