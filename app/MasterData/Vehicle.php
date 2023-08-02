<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Vehicle extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_vhcl';
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
        return ['veihcle_name', 'vehicle_code', 'vehicle_type', 'depo_id', 'vehicle_registration_date', 'owner_name', 'engine_number','chassis_number',
            'license_number', 'cubic_capacity', 'fuel_type', 'last_meter_reading','capacity_weight', 'capacity_height', 'capacity_width', 'capacity_length'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $vehicle = new Vehicle();
        $vehicle->setConnection($this->connection);
        $vehicle->vhcl_name = $value->veihcle_name;
        $vehicle->vhcl_code = $value->vehicle_code;
        $vehicle->vhcl_type = $value->vehicle_type;
        $vehicle->dpot_id = $value->depo_id;
        $vehicle->vhcl_rdat = isset($value->vehicle_registration_date) ? $value->vehicle_registration_date : "";
        $vehicle->vhcl_ownr = isset($value->owner_name) ? $value->owner_name : "";
        $vehicle->vhcl_engn = isset($value->engine_number) ? $value->engine_number : "";
        $vehicle->vhcl_csis = isset($value->chassis_number) ? $value->chassis_number : "";
        $vehicle->vhcl_licn = isset($value->license_number) ? $value->license_number : "";
        $vehicle->vhcl_cpct = isset($value->cubic_capacity) ? $value->cubic_capacity : "";
        $vehicle->vhcl_fuel = isset($value->fuel_type) ? $value->fuel_type : "";
        $vehicle->vhcl_lmrd = isset($value->last_meter_reading) ? $value->last_meter_reading : "";
        $vehicle->vhcl_cpwt = isset($value->capacity_weight) ? $value->capacity_weight : "";
        $vehicle->vhcl_cpht = isset($value->capacity_height) ? $value->capacity_height : "";
        $vehicle->vhcl_cpwd = isset($value->capacity_width) ? $value->capacity_width : "";
        $vehicle->vhcl_cplg = isset($value->capacity_length) ? $value->capacity_length : "";
        $vehicle->lfcl_id = 1;
        $vehicle->cont_id = $this->currentUser->country()->id;
        $vehicle->aemp_iusr = $this->currentUser->employee()->id;
        $vehicle->aemp_eusr = $this->currentUser->employee()->id;
        $vehicle->save();
    }

}
