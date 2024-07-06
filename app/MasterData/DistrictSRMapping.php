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

class DistrictSRMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_srds';
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
        return ['district_code', 'staff_id'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
        $dsct = District::on($this->connection)->where(['dsct_code' => $value->district_code])->first();
        if ($employee != null && $dsct != null) {
            $DistrictSRMapping = DistrictSRMapping::on($this->connection)->where(['dsct_id' => $dsct->id, 'aemp_id' => $employee->id])->first();
            if ($DistrictSRMapping == null) {
                $DistrictSRMapping = new DistrictSRMapping();
                $DistrictSRMapping->setConnection($this->connection);
                $DistrictSRMapping->aemp_id = $employee->id;
                $DistrictSRMapping->dsct_id = $dsct->id;
                $DistrictSRMapping->cont_id = $this->currentUser->country()->id;
                $DistrictSRMapping->lfcl_id = 1;
                $DistrictSRMapping->aemp_iusr = $this->currentUser->employee()->id;
                $DistrictSRMapping->aemp_eusr = $this->currentUser->employee()->id;
                $DistrictSRMapping->save();
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