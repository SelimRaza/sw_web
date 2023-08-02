<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\MasterData\Country;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Thana extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_than';
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
        return ['thana_name', 'thana_code', 'district_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $district_id = District::on($this->connection)->where(['dsct_code' => $value->district_code])->first();

        $thana = new Thana();
        $thana->setConnection($this->connection);
        $thana->than_name = $value->thana_name;
        $thana->than_code = $value->thana_code;
        $thana->dsct_id = $district_id->id;
        $thana->cont_id = $this->currentUser->employee()->cont_id;
        $thana->lfcl_id = 1;
        $thana->aemp_iusr = $this->currentUser->employee()->id;
        $thana->aemp_eusr = $this->currentUser->employee()->id;
        $thana->save();

    }



    public function district()
    {
        return District::on($this->connection)->find($this->dsct_id);
    }
}
