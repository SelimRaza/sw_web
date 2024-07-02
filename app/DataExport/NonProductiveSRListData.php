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

class NonProductiveSRListData extends Model implements FromCollection, WithHeadings, WithHeadingRow
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

    public static function create($start_date,$end_date,$slgp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->slgp_id = $slgp_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['Date','group_name','Region','zone_name', 'sr_id','sr_name', 'Mobile','Designation',/*'rout_name',*/'Start_time','Start_status'];
    }

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("
            SELECT
              t1.attn_date,
              t4.slgp_name,
              t10.dirg_name,
              t5.zone_name,
              t2.aemp_usnm,
              t2.aemp_name,
              t2.aemp_mob1,
              t6.edsg_name,
              min(t1.attn_time) AS start_time,
              if(t11.id=1, 'Start Work', t11.atyp_name) AS start_status
            FROM tt_attn AS t1
              INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
              LEFT JOIN tl_sgsm AS t3 ON t1.aemp_id = t3.aemp_id
              LEFT JOIN tm_slgp AS t4 ON t3.slgp_id = t4.id
              LEFT JOIN tm_zone AS t5 ON t3.zone_id = t5.id
              INNER JOIN tm_edsg AS t6 ON t2.edsg_id = t6.id
              LEFT JOIN tm_dirg AS t10 ON t5.dirg_id = t10.id
              LEFT JOIN tm_atyp t11 ON t1.atten_atyp=t11.id
            WHERE date(t1.attn_date)='$this->start_date'
            AND t2.lfcl_id=1 and t4.id='$this->slgp_id' AND t6.id=1
            and t2.id not in(SELECT aemp_id FROM th_ssvh where ssvh_date='$this->start_date' and ssvh_ispd = 1 group by aemp_id)
            GROUP BY t1.attn_date, t4.slgp_name, t5.zone_name, t2.aemp_usnm, t2.aemp_name, t2.aemp_mob1, t6.edsg_name,t10.dirg_name,t11.id");
        return collect($dataRow);
    }


}
