<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocationCompany extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_lcmp';
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
        return ['location_company_name', 'location_company_code', 'location_master_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $locationM = LocationMaster::on($this->connection)->where(['locm_code' => $value->location_master_code])->first();

        $locationData = new LocationCompany();
        $locationData->setConnection($this->connection);
        $locationData->lcmp_name = $value->location_company_name;
        $locationData->lcmp_code = $value->location_company_code;
        $locationData->locm_id = $locationM->id;
        $locationData->lfcl_id = 1;
        $locationData->cont_id = $this->currentUser->country()->id;
        $locationData->aemp_iusr = $this->currentUser->employee()->id;
        $locationData->aemp_eusr = $this->currentUser->employee()->id;
        $locationData->save();

    }
}
