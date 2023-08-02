<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Tutorial extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_ttop';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['ttop_name',    'ttop_code',	'ttop_desc', 	'ttop_vdid',	'ttop_vurl',	'slgp_id',	'zone_id'];
    }


    public function model(array $row)
    {
        $value = (object)$row;

        $tutorial = new Tutorial();
        $tutorial->setConnection($this->connection);
        $tutorial->ttop_name = $value->ttop_name;
        $tutorial->ttop_code = $value->ttop_code;
        $tutorial->ttop_desc = $value->ttop_desc;
        $tutorial->ttop_vdid = $value->ttop_vdid;
        $tutorial->ttop_vurl = $value->ttop_vurl;
        $tutorial->ttop_ythm = $value->ttop_ythm ?? '';
        $tutorial->slgp_id = $value->slgp_id;
        $tutorial->zone_id = $value->zone_id;
        $tutorial->lfcl_id = 1;
        $tutorial->cont_id = $this->currentUser->employee()->cont_id;
        $tutorial->save();

    }
}
