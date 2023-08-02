<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SrBaseMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_srgb';
    private $currentUser;
    protected $connection= '';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['group_id', 'zone_id', 'base_name', 'base_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $base = new Base();
        $base->setConnection($this->connection);
        $base->name = $value->base_name;
        $base->code = $value->base_code;
        $base->zone_id = $value->zone_id;
        $base->sales_group_id = $value->group_id;
        $base->status_id = 1;
        $base->country_id = $this->currentUser->employee()->country_id;
        $base->created_by = $this->currentUser->employee()->id;
        $base->updated_by = $this->currentUser->employee()->id;
        $base->save();
    }

    public function zone()
    {
        return Zone::on($this->connection)->find($this->zone_id);
    }
    public function thana()
    {
        return Thana::on($this->connection)->find($this->than_id);
    }
    public function base()
    {
        return Base::on($this->connection)->find($this->base_id);
    }
}
