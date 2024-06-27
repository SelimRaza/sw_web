<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocationMaster extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_locm';
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
        return ['location_name', 'location_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $locationData = new LocationMaster();
        $locationData->setConnection($this->connection);
        $locationData->locm_name = $value->location_name;
        $locationData->locm_code = $value->location_code;
        $locationData->lfcl_id = 1;
        $locationData->cont_id = $this->currentUser->country()->id;
        $locationData->aemp_iusr = $this->currentUser->employee()->id;
        $locationData->aemp_eusr = $this->currentUser->employee()->id;
        $locationData->save();

    }

}
