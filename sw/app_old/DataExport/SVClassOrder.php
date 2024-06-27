<?php

namespace App\DataExport;

use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SVClassOrder extends Model implements FromCollection, WithHeadings, ToArray, WithHeadingRow
{

    public function __construct()
    {

    }

    public static function create($country_id, $manager_id, $start_date, $end_date)
    {
        $instance = new self();
        $instance->manager_id = $manager_id;
        $instance->cont_id = $country_id;
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        return $instance;
    }

    public function collection()
    {
        $country = (new Country())->country($this->cont_id);
        $target_body_all = array();
        $employee = Employee::on($country->cont_conn)->where(['aemp_mngr' => $this->manager_id, 'lfcl_id' => 1])->orderBy('id')->get();
        foreach ($employee as $employee1) {
            $target_body = array($employee1->aemp_usnm, $employee1->aemp_name);
            $targetPreQty = DB::connection($country->cont_conn)->select("SELECT
  t6.id,
  t6.itcl_code,
  t7.ordd_oamt,
  t7.memo_count
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_plmt AS t3 ON t2.plmt_id = t3.id
  INNER JOIN tm_pldt AS t4 ON t3.id = t4.plmt_id
  INNER JOIN tm_amim AS t5 ON t4.amim_id = t5.id
  INNER JOIN tm_itcl AS t6 ON t5.itcl_id = t6.id
  LEFT JOIN (
              SELECT
                t4.itcl_code,
                sum(t2.ordd_oamt)     AS ordd_oamt,
                count(DISTINCT t1.id) AS memo_count
              FROM tt_ordm AS t1 INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                INNER JOIN tm_itcl AS t4 ON t3.itcl_id = t4.id
              WHERE t1.aemp_id = $employee1->id AND t1.ordm_date BETWEEN '$this->start_date' AND '$this->end_date'
              GROUP BY t4.itcl_code
            ) AS t7 ON t6.itcl_code = t7.itcl_code
WHERE t1.aemp_mngr = $this->manager_id AND t5.lfcl_id = 1 and t1.lfcl_id=1
GROUP BY t6.itcl_code
ORDER BY t6.id DESC");

            foreach ($targetPreQty as $targetPreQty1) {
                array_push($target_body, $targetPreQty1->ordd_oamt);
                array_push($target_body, $targetPreQty1->memo_count);
            }
            array_push($target_body_all, $target_body);

        }
        return collect([
            $target_body_all

        ]);
    }


    public function array(array $array)
    {

    }

    public function headings(): array
    {
        $country = (new Country())->country($this->cont_id);
        $target_header = array('User Name', 'Name');
        $itemClass = DB::connection($country->cont_conn)->select("SELECT
  t6.itcl_code,
  t6.id
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_plmt AS t3 ON t2.plmt_id = t3.id
  INNER JOIN tm_pldt AS t4 ON t3.id = t4.plmt_id
  INNER JOIN tm_amim AS t5 ON t4.amim_id = t5.id
  INNER JOIN tm_itcl AS t6 ON t5.itcl_id = t6.id
WHERE t1.aemp_mngr = $this->manager_id AND t5.lfcl_id = 1
GROUP BY t6.itcl_code, t6.id
ORDER BY t6.id DESC");
        foreach ($itemClass as $index => $itemClass1) {
            array_push($target_header, $itemClass1->itcl_code);
            array_push($target_header, "Memo");
        }

        return $target_header;
    }
}
