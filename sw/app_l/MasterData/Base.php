<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Base extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_base';
    private $currentUser;
    protected $connection= '';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['zone_code', 'thana_code', 'base_name', 'base_code'];
    }

    public function model(array $row)
    {

        $value = (object)$row;
        //$this->zone_code = $value->zone_code;
        $zone_id = Zone::on($this->connection)->where(['zone_code' => $value->zone_code])->first();
        $than_id = Thana::on($this->connection)->where(['than_code' => $value->thana_code])->first();
        $base = new Base();


        $base->setConnection($this->connection);
        $base->base_name = $value->base_name;
        $base->base_code = $value->base_code;
        $base->zone_id = $zone_id->id;
        $base->than_id = $than_id->id;
        $base->cont_id = $this->currentUser->employee()->cont_id;
        $base->lfcl_id = 1;
        $base->aemp_iusr = $this->currentUser->employee()->id;
        $base->aemp_eusr = $this->currentUser->employee()->id;
        $base->var = 1;
        $base->attr1 = '';
        $base->attr2 = '';
        $base->attr3 = 0;
        $base->attr4 = 0;
        $base->save();
    }

    public function zone()
    {
        return Zone::on($this->connection)->find($this->zone_code);
    }
    public function thana()
    {
        return Thana::on($this->connection)->find($this->than_id);
    }
}
