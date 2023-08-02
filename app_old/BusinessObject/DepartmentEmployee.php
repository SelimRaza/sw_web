<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 10:35 AM
 */

namespace App\BusinessObject;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;


class DepartmentEmployee extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tbld_department_emp_mapping';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }


    public function headings(): array
    {
        return ['department_id', 'department_name', 'emp_id', 'emp_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $departmentEmployee = DepartmentEmployee::where(['department_id' => $value->department_id, 'emp_id' => $value->emp_id])->first();
        if ($departmentEmployee == null) {
            $departmentEmployee = new DepartmentEmployee();
            $departmentEmployee->emp_id = $value->emp_id;
            $departmentEmployee->department_id = $value->department_id;
            $departmentEmployee->country_id = $this->currentUser->employee()->country_id;
            $departmentEmployee->created_by = $this->currentUser->employee()->id;
            $departmentEmployee->updated_by = $this->currentUser->employee()->id;
            $departmentEmployee->save();
        }
    }

    public function department()
    {
        return Department::find($this->department_id);
    }

    public function employee()
    {
        return Employee::find($this->emp_id);
    }

}