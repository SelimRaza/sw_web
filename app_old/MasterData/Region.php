<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Region extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dirg';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    public function headings(): array
    {
        return ['group_id','division_id', 'region_name', 'region_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $region = new Region();
        $region->name = $value->region_name;
        $region->code = $value->region_code;
        $region->division_id = $value->division_id;
        $region->sales_group_id = $value->group_id;
        $region->status_id = 1;
        $region->country_id = $this->currentUser->employee()->country_id;
        $region->created_by = $this->currentUser->employee()->id;
        $region->updated_by = $this->currentUser->employee()->id;
        $region->save();
    }
    public function division()
    {
        return Division::find($this->division_id);
    }
}
