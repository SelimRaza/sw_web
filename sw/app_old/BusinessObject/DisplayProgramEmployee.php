<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;

class DisplayProgramEmployee extends Model
{
    protected $table = 'tblt_program_employee_mapping';

    public function employee()
    {
        return Employee::find($this->emp_id);
    }
}