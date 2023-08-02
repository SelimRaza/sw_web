<?php

namespace App\BusinessObject;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HRPolicyEmployee extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tbld_policy_employee_mapping';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }


    public function headings(): array
    {
        return ['policy_id', 'policy_name', 'emp_id', 'emp_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $hrEmployee = HRPolicyEmployee::where(['emp_id' => $value->emp_id])->first();
        if ($hrEmployee == null) {
            $hrEmployee = new HRPolicyEmployee();
            $hrEmployee->emp_id = $value->emp_id;
            $hrEmployee->policy_id = $value->policy_id;
            $hrEmployee->country_id = $this->currentUser->employee()->country_id;
            $hrEmployee->created_by = $this->currentUser->employee()->id;
            $hrEmployee->updated_by = $this->currentUser->employee()->id;
            $hrEmployee->save();
        }
    }

    public function employee()
    {
        return Employee::find($this->emp_id);
    }
    public function hrPolicy()
    {
        return HRPolicy::find($this->policy_id);
    }
}
