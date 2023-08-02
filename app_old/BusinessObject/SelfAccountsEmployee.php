<?php

namespace App\BusinessObject;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;

class SelfAccountsEmployee extends Model
{
    protected $table = 'tbld_employee_account_mapping';
    public function employee()
    {
        return Employee::find($this->emp_id);
    }
}
