<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocationDepartment extends Model  implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_ldpt';
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
        return ['location_department_name', 'location_department_code', 'location_company_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $locationD = LocationCompany::on($this->connection)->where(['lcmp_code' => $value->location_company_code])->first();

        $locationData = new LocationDepartment();
        $locationData->setConnection($this->connection);
        $locationData->ldpt_name = $value->location_department_name;
        $locationData->ldpt_code = $value->location_department_code;
        $locationData->lcmp_id = $locationD->id;
        $locationData->lfcl_id = 1;
        $locationData->cont_id = $this->currentUser->country()->id;
        $locationData->aemp_iusr = $this->currentUser->employee()->id;
        $locationData->aemp_eusr = $this->currentUser->employee()->id;
        $locationData->save();

    }
}
