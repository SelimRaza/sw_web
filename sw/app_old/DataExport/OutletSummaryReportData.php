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

class OutletSummaryReportData extends Model implements FromCollection, WithHeadings, WithHeadingRow
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

    public static function create($start_date, $end_date, $slgp_id, $zone_id)
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
        return ['Group Name', 'Region', 'Zone', 'Dist.ID', 'Dist.Name', 'SR ID', 'SR Name', 'Mobile', 'Designation', 'Day','Route Code','Route Name', 'Total Outlet'];
    }

    public function collection()
    {

        /*$dataRow = DB::connection($this->connection)->select("
SELECT
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t10.dlrm_code,
  t10.dlrm_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t6.edsg_name,
  t7.rpln_day,
  t8.rout_name,
  count(t11.site_id) AS site_count
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_zone AS t4 ON t2.zone_id = t4.id
  INNER JOIN tm_dirg AS t5 ON t4.dirg_id = t5.id
  INNER JOIN tm_edsg AS t6 ON t1.edsg_id = t6.id
  INNER JOIN tl_rpln AS t7 ON t1.id = t7.aemp_id
  INNER JOIN tm_rout AS t8 ON t7.rout_id = t8.id
  INNER JOIN tl_srdi AS t9 ON t1.id = t9.aemp_id
  INNER JOIN tm_dlrm AS t10 ON t9.dlrm_id = t10.id
  INNER JOIN tl_rsmp AS t11 ON t7.rout_id = t11.rout_id
WHERE t3.id = $this->slgp_id AND t4.id = $this->zone_id
GROUP BY
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t10.dlrm_code,
  t10.dlrm_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t6.edsg_name,
  t7.rpln_day,
  t8.rout_name

"); */

        $where = "t3.id = $this->slgp_id AND t1.lfcl_id='1'";
        if ($this->zone_id != 0) {
            $where .= " AND t4.id = $this->zone_id AND t1.lfcl_id='1'";
        }

        $dataRow = DB::connection($this->connection)->select("
SELECT
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t10.dlrm_code,
  t10.dlrm_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t6.edsg_name,
  t7.rpln_day,
  t8.rout_code,
  t8.rout_name,
  count(DISTINCT t11.site_id) AS site_count
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_zone AS t4 ON t2.zone_id = t4.id
  INNER JOIN tm_dirg AS t5 ON t4.dirg_id = t5.id
  INNER JOIN tm_edsg AS t6 ON t1.edsg_id = t6.id
  INNER JOIN tl_rpln AS t7 ON t1.id = t7.aemp_id
  INNER JOIN tm_rout AS t8 ON t7.rout_id = t8.id
  INNER JOIN tl_srdi AS t9 ON t1.id = t9.aemp_id
  INNER JOIN tm_dlrm AS t10 ON t9.dlrm_id = t10.id
  LEFT JOIN tl_rsmp AS t11 ON t7.rout_id = t11.rout_id
WHERE $where
GROUP BY
t2.slgp_id,
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t10.dlrm_code,
  t10.dlrm_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t6.edsg_name,
  t7.rpln_day,
  t8.rout_code,
  t8.rout_name

");
        return collect($dataRow);
        //return collect(null);
    }


}
