<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\MasterData\Country;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class District extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dsct';
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
        return ['district_name', 'district_code', 'division_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $division_id = GovtDivision::on($this->connection)->where(['disn_code' => $value->division_code])->first();
        $district = new District();
        $district->setConnection($this->connection);
        $district->dsct_name = $value->district_name;
        $district->dsct_code = $value->district_code;
        $district->disn_id = $division_id->id;
        $district->cont_id = $this->currentUser->employee()->cont_id;
        $district->lfcl_id = 1;
        $district->aemp_iusr = $this->currentUser->employee()->id;
        $district->aemp_eusr = $this->currentUser->employee()->id;
        $district->save();

    }

    public function division()
    {
        return GovtDivision::on($this->connection)->find($this->disn_id);
    }
}
