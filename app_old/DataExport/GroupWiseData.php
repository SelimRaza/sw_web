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

class GroupWiseData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'GroupWiseData';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    public static function create($date)
    {
        $instance = new self();
        $instance->date = $date;
        return $instance;
    }

    public function headings(): array
    {
        return ['group_name', 'total_user', 'attendnace', 'total_note_emp', 'total_note_count', 'mech_work', 'outlet_count', 'visited_count', 'active_outlet'];
    }

    public function collection()
    {


        $dataRow = DB::select("SELECT
  t1.name       AS group_name,
  t1.emp_count  AS total_user,
  t2.emp_count  AS attendnace,
  t3.emp_count  AS total_note_emp,
  t3.note_count  AS total_note_count,
  t4.emp_count  AS mech_work,
  t5.site_count AS outlet_count,
  t6.cov_count  AS visited_count,
  t7.ac_count   AS active_outlet
FROM (
       SELECT
         t1.id,
         t1.name,
         count(t2.emp_id) AS emp_count
       FROM tbld_sales_group AS t1
         INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
         INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
       WHERE t1.country_id = 1 AND t3.status_id = 1
       GROUP BY t1.id,t1.name) AS t1
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.emp_id) AS emp_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT t1.id AS emp_id
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_attendance AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t1.id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t2 ON t1.id = t2.id
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.emp_id) AS emp_count,
               sum(t4.note_count) AS note_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT t1.id AS emp_id,
               count(t2.id) as note_count
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_note AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t1.id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t3 ON t1.id = t3.id
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.emp_id) AS emp_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT t1.id AS emp_id
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_merchandise_work AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t1.id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t4 ON t1.id = t4.id
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.site_id) AS site_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT
                            t3.site_id AS site_id,
                            t1.id      AS emp_id
                          FROM tbld_employee AS t1
                            INNER JOIN tbld_pjp AS t2 ON t1.id = t2.emp_id AND t2.day = dayname('$this->date')
                            INNER JOIN tbld_route_site_mapping AS t3 ON t2.route_id = t3.route_id
                          WHERE t1.country_id = 1
                          GROUP BY t3.id,t1.id,t3.site_id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t5 ON t1.id = t5.id
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.site_id) AS cov_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT
                            t2.site_id AS site_id,
                            t1.id      AS emp_id
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_site_visited AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t2.id,t1.id,t2.site_id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t6 ON t1.id = t6.id
  LEFT JOIN (SELECT
               t1.id,
               t1.name,
               count(t4.site_id) AS ac_count
             FROM tbld_sales_group AS t1
               INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
               INNER JOIN tbld_employee AS t3 ON t2.emp_id = t3.id
               LEFT JOIN (SELECT
                            t3.site_id AS site_id,
                            t1.id      AS emp_id
                          FROM tbld_employee AS t1
                            INNER JOIN tbld_pjp AS t2 ON t1.id = t2.emp_id AND t2.day = dayname('$this->date')
                            INNER JOIN tbld_route_site_mapping AS t3 ON t2.route_id = t3.route_id
                            INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
                          WHERE t1.country_id = 1 AND t4.status_id = 1
                          GROUP BY t3.id,t1.id,t3.site_id) AS t4 ON t3.id = t4.emp_id
             WHERE t1.country_id = 1 AND t3.status_id = 1
             GROUP BY t1.id,t1.name) AS t7 ON t1.id = t7.id;");
        return collect($dataRow);
    }


}
