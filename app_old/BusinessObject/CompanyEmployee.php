<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Company;
use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyEmployee extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_emcm';
    private $currentUser;
    protected $connection= '';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }


    public function headings(): array
    {
        return ['company_id', 'company_name', 'staff_id', 'staff_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
        $companyEmployee = CompanyEmployee::on($this->connection)->where(['acmp_id' => $value->company_id, 'aemp_id' => $employee->id])->first();
        if ($companyEmployee == null) {
            $comEmp = new CompanyEmployee();
            $comEmp->setConnection($this->connection);
            $comEmp->aemp_id = $employee->id;
            $comEmp->acmp_id = $value->company_id;
            $comEmp->cont_id = $this->currentUser->employee()->cont_id;
            $comEmp->lfcl_id = 1;
            $comEmp->aemp_iusr = $this->currentUser->employee()->id;
            $comEmp->aemp_eusr = $this->currentUser->employee()->id;
            $comEmp->var = 1;
            $comEmp->attr1 = '';
            $comEmp->attr2 = '';
            $comEmp->attr3 = 0;
            $comEmp->attr4 = 0;
            $comEmp->save();
        }
    }
    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_id);
    }
     public function Company()
    {
        return Company::on($this->connection)->find($this->acmp_id);
    }

}