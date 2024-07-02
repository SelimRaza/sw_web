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

class UserInfoReportData extends Model implements FromCollection, WithHeadings, WithHeadingRow
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

    public static function create($start_date,$end_date,$acmp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->acmp_id = $acmp_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['Group Name','Region','Zone','SR ID','SR Name','Mobile','Is_SP','Is_HR_S','Thana','Designation','Status','Managers ID','Managers Name'];
        }

    public function collection()
    {

       // dd($this->acmp_id);

        $dataRow = DB::connection($this->connection)->select("
SELECT
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
   if(t1.aemp_issl=1,'Y','N') AS Is_SP,
  if(t1.aemp_asyn='Y','Y','N') AS Is_HR_S,
  if(t9.id IS null,'N','Y')  AS Thana,
  t6.edsg_name,
  t8.lfcl_name,
  t7.aemp_usnm AS manager_id,
  t7.aemp_name AS manager_name
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_zone AS t4 ON t2.zone_id = t4.id
  INNER JOIN tm_dirg AS t5 ON t4.dirg_id = t5.id
  INNER JOIN tm_edsg AS t6 ON t1.edsg_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_mngr = t7.id
  INNER JOIN tm_lfcl AS t8 ON t1.lfcl_id = t8.id
  LEFT JOIN tl_srth As t9 ON t1.id=t9.aemp_id
WHERE t3.acmp_id = $this->acmp_id
GROUP BY
  t3.slgp_name,
  t5.dirg_name,
  t4.zone_name,
  t1.aemp_usnm,
  t1.aemp_name,
  t1.aemp_mob1,
  t1.aemp_dtsm,
  t6.edsg_name,
  t8.lfcl_name,
  t7.aemp_usnm,
  t7.aemp_name,
  t1.aemp_issl,
  t1.aemp_asyn,
  t1.id,t9.id

");
        return collect($dataRow);
    }


}
