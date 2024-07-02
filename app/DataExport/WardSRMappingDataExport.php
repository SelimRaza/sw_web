<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\DataExport;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class WardSRMappingDataExport extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tl_srwd';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($slgp_id, $zone_id)
    {
        $instance = new self();
        $instance->slgp_id = $slgp_id;
        $instance->zone_id = $zone_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['ward_code', 'ward_name', 'staff_id', 'staff_name', 'zone_code', 'zone_name'];
    }

    public function collection()
    {

        $where = "t2.slgp_id = $this->slgp_id";
        if ($this->zone_id != 0) {
            $where .= " AND t2.zone_id = $this->zone_id";
        }

        $dataRow = DB::connection($this->connection)->select("
                SELECT t4.`ward_code`AS ward_code,
                t4.ward_name AS ward_name,
                t3.`aemp_usnm`AS staff_id,
                t3.aemp_name AS staff_name,
                t5.zone_code,t5.zone_name
                FROM `tl_srwd` t1 JOIN tl_sgsm t2 ON(t1.`aemp_id`=t2.aemp_id)
                LEFT JOIN tm_aemp t3 ON(t1.`aemp_id`=t3.id)
                LEFT JOIN tm_ward t4 ON(t1.`ward_id`=t4.id)
                LEFT JOIN tm_zone t5 ON(t2.zone_id=t5.id)
                WHERE $where
                GROUP BY
                  t4.`ward_code`,
                  t4.ward_name,
                  t3.`aemp_usnm`,
                  t3.aemp_name,
                  t5.zone_code,
                  t5.zone_name
                ");
        return collect($dataRow);
    }

}