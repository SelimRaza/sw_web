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
        return ['region_code', 'zone_name', 'zone_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $region_id = Region::on($this->connection)->where(['dirg_code' => $value->region_code])->first();
        $zone = new Zone();
        $zone->setConnection($this->connection);
        $zone->zone_name = $value->zone_name;
        $zone->zone_code = $value->zone_code;
        $zone->dirg_id = $region_id->id;
        $zone->cont_id = $this->currentUser->country()->id;
        $zone->lfcl_id = 1;
        $zone->aemp_iusr = $this->currentUser->employee()->id;
        $zone->aemp_eusr = $this->currentUser->employee()->id;
        $zone->var = 1;
        $zone->attr1 = '';
        $zone->attr2 = '';
        $zone->attr3 = 0;
        $zone->attr4 = 0;
        $zone->save();
    }
    public function region()
    {
        return Region::on($this->connection)->find($this->dirg_id);
    }
}
