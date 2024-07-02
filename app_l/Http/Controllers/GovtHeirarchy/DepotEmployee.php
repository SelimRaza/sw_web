<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\MasterData;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepotEmployee extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_srdi';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }


    public function headings(): array
    {
        return ['thana_code', 'thana_name', 'staff_id', 'staff_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
        $depot = Depot::on($this->connection)->where(['dlrm_code' => $value->dealer_code])->first();
        if ($employee != null && $depot != null) {
            $depotEmployee = DepotEmployee::on($this->connection)->where(['dlrm_id' => $depot->id, 'aemp_id' => $employee->id])->first();
            if ($depotEmployee == null) {
                $depotEmployee = new DepotEmployee();
                $depotEmployee->setConnection($this->connection);
                $depotEmployee->aemp_id = $employee->id;
                $depotEmployee->acmp_id = 1;
                $depotEmployee->dlrm_id = $depot->id;
                $depotEmployee->cont_id = $this->currentUser->country()->id;
                $depotEmployee->lfcl_id = 1;
                $depotEmployee->aemp_iusr = $this->currentUser->employee()->id;
                $depotEmployee->aemp_eusr = $this->currentUser->employee()->id;
                $depotEmployee->var = 1;
                $depotEmployee->attr1 = '';
                $depotEmployee->attr2 = '';
                $depotEmployee->attr3 = 0;
                $depotEmployee->attr4 = 0;
                $depotEmployee->save();
            }
        }

    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_id);
    }

    public function depot()
    {
        return Depot::on($this->connection)->find($this->dlrm_id);
    }

}