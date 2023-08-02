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

class ThanaSRMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_srth';
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
        $than = Thana::on($this->connection)->where(['than_code' => $value->thana_code])->first();
        if ($employee != null && $than != null) {
            $ThanaSRMapping = ThanaSRMapping::on($this->connection)->where(['than_id' => $than->id, 'aemp_id' => $employee->id])->first();
            if ($ThanaSRMapping == null) {
                $ThanaSRMapping = new ThanaSRMapping();
                $ThanaSRMapping->setConnection($this->connection);
                $ThanaSRMapping->aemp_id = $employee->id;
                $ThanaSRMapping->than_id = $than->id;
                $ThanaSRMapping->cont_id = $this->currentUser->country()->id;
                $ThanaSRMapping->lfcl_id = 1;
                $ThanaSRMapping->aemp_iusr = $this->currentUser->employee()->id;
                $ThanaSRMapping->aemp_eusr = $this->currentUser->employee()->id;
                $ThanaSRMapping->save();
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