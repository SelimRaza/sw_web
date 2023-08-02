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

class WardSRMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_srwd';
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
        return ['ward_code', 'staff_id'];
    }

    public function model(array $row)
    {

        try {
            $value = (object)$row;
            $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
            $ward = Ward::on($this->connection)->where(['ward_code' => $value->ward_code])->first();
            if ($employee != null && $ward != null) {
                $WardSRMapping = WardSRMapping::on($this->connection)->where(['ward_id' => $ward->id, 'aemp_id' => $employee->id])->first();
                if ($WardSRMapping == null) {
                    $WardSRMapping = new WardSRMapping();
                    $WardSRMapping->setConnection($this->connection);
                    $WardSRMapping->aemp_id = $employee->id;
                    $WardSRMapping->ward_id = $ward->id;
                    $WardSRMapping->cont_id = $this->currentUser->country()->id;
                    $WardSRMapping->lfcl_id = 1;
                    $WardSRMapping->aemp_iusr = $this->currentUser->employee()->id;
                    $WardSRMapping->aemp_eusr = $this->currentUser->employee()->id;
                    $WardSRMapping->save();
                }
            }else{
                return;
            }

        }catch (\Exception $e) {
            return;
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