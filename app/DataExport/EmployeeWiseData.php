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

class EmployeeWiseData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'EmployeeWiseData';
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
        return ['empId', 'user_name', 'name', 'mobile', 'role', 'group_name', 'attendance', 'start_day', 'end_day', 'total_outlet', 'note', 'mech_work', 'visited'];
    }

    public function collection()
    {


        $dataRow = DB::select("SELECT
  t1.*,
  t2.emp_count  AS attendance,
  t2.start_day,
  t2.end_day,
  t3.site_cout  AS total_outlet,
  t4.note_count AS note,
  t5.mech_count AS mech_work,
  t6.site_count AS visited
FROM (
       SELECT
         t1.id,
         t1.user_name,
         t1.name,
         t1.mobile,
         t4.name AS role,
         group_concat(t3.name) AS group_name
       FROM tbld_employee AS t1
         INNER JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.emp_id
         INNER JOIN tbld_sales_group AS t3 ON t2.sales_group_id = t3.id
         INNER JOIN tbld_role AS t4 ON t1.role_id = t4.id
       WHERE t1.country_id = 1
       GROUP BY  t1.id,t1.user_name,t1.name,t1.mobile,t4.name
     ) AS t1
  LEFT JOIN (
              SELECT
                t1.id,
                count(t4.emp_id) AS emp_count,
                t4.start_day,
                t4.end_day
              FROM tbld_employee AS t1
                LEFT JOIN (SELECT
                             t1.id             AS emp_id,
                             min(t2.date_time) AS start_day,
                             max(t2.date_time) AS end_day
                           FROM tbld_employee AS t1
                             INNER JOIN tblt_attendance AS t2 ON t1.id = t2.emp_id AND t2.date ='$this->date'
                           WHERE t1.country_id = 1
                           GROUP BY t1.id) AS t4 ON t1.id = t4.emp_id
              WHERE t1.country_id = 1 AND t1.status_id = 1
              GROUP BY t1.id,t4.start_day,t4.end_day
            ) AS t2 ON t1.id = t2.id
  LEFT JOIN (SELECT
               t1.id             AS emp_id,
               count(t2.site_id) AS site_cout
             FROM tbld_employee AS t1 
             LEFT JOIN (SELECT
                                                   t3.site_id AS site_id,
                                                   t1.id      AS emp_id
                                                 FROM tbld_employee AS t1
                                                   INNER JOIN tbld_pjp AS t2
                                                     ON t1.id = t2.emp_id AND t2.day = dayname('$this->date')
                                                   INNER JOIN tbld_route_site_mapping AS t3 ON t2.route_id = t3.route_id
                                                   INNER JOIN tbld_site AS t4 ON t3.site_id = t4.id
                                                 WHERE t1.country_id = 1 AND t4.status_id = 1
                                                 GROUP BY t3.id,t1.id,t3.site_id
                                                ) AS t2 ON t1.id = t2.emp_id
             GROUP BY t1.id) AS t3 ON t1.id = t3.emp_id
  LEFT JOIN (SELECT
               t1.id,
               count(t4.note) AS note_count
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.id AS emp_id,
                            t2.id AS note
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_note AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t2.id, t1.id) AS t4 ON t1.id = t4.emp_id
             WHERE t1.country_id = 1 AND t1.status_id = 1
             GROUP BY t1.id) AS t4 ON t1.id = t4.id
  LEFT JOIN (SELECT
               t1.id,
               count(t4.mech) AS mech_count
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.id AS emp_id,
                            t2.id AS mech
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_merchandise_work AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t2.id, t1.id) AS t4 ON t1.id = t4.emp_id
             WHERE t1.country_id = 1 AND t1.status_id = 1
             GROUP BY t1.id) AS t5 ON t1.id = t5.id
  LEFT JOIN (SELECT
               t1.id,
               count(t4.site_id) AS site_count
             FROM tbld_employee AS t1
               LEFT JOIN (SELECT
                            t1.id      AS emp_id,
                            t2.site_id AS site_id
                          FROM tbld_employee AS t1
                            INNER JOIN tblt_site_visited AS t2 ON t1.id = t2.emp_id AND t2.date = '$this->date'
                          WHERE t1.country_id = 1
                          GROUP BY t2.site_id, t1.id) AS t4 ON t1.id = t4.emp_id
             WHERE t1.country_id = 1 AND t1.status_id = 1
             GROUP BY t1.id) AS t6 ON t1.id = t6.id;");
        return collect($dataRow);
    }


}
