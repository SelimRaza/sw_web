<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Zone extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_zone';
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
        return ['group_id','region_id', 'zone_name', 'zone_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $zone = new Zone();
        $zone->name = $value->zone_name;
        $zone->code = $value->zone_code;
        $zone->region_id = $value->region_id;
        $zone->sales_group_id = $value->group_id;
        $zone->status_id = 1;
        $zone->country_id = $this->currentUser->employee()->country_id;
        $zone->created_by = $this->currentUser->employee()->id;
        $zone->updated_by = $this->currentUser->employee()->id;
        $zone->save();
    }
    public function region()
    {
        return Region::on($this->connection)->find($this->dirg_id);
    }
}
