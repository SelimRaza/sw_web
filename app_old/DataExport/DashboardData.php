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

class DashboardData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'DashboardData';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    public static function create($company_id)
    {
        $instance = new self();
        $instance->company_id = $company_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['company_name', 'group_name', 'manager_id', 'manager_name','manager_role', 'user_id', 'user_name', 'user_role'];
    }

    public function collection()
    {


        $dataRow = DB::select("SELECT
  t4.company_name,
  t3.group_name,
  t2.user_id AS manager_id,
  t2.name    AS manager_name,
  t6.name    AS manager_role,
  t1.user_id,
  t1.name       user_name,
  t5.name    AS user_role
FROM tblh_dashboard_data AS t1
  INNER JOIN tblh_dashboard_data AS t2 ON t2.user_id = t1.manager_id AND t2.date = curdate()
  INNER JOIN tbld_group_master AS t3 ON t1.group_id = t3.group_id
  INNER JOIN tbld_company_master AS t4 ON t3.company_id = t4.id
  LEFT JOIN tbld_master_role AS t5 ON t1.master_role_id = t5.id
  LEFT JOIN tbld_master_role AS t6 ON t2.master_role_id = t6.id
WHERE t1.date = curdate() and t4.id=$this->company_id");
        return collect($dataRow);
    }


}
