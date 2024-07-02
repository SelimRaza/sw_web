<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\MasterData\Country;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
class Market extends Model implements WithHeadings, ToModel, WithHeadingRow,FromCollection
{
    protected $table = 'tm_mktm';
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
        return ['id','market_code', 'market_name', 'ward_code', 'ward_id'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $ward_id = Ward::on($this->connection)->where(['ward_code' => $value->ward_code])->first();

        $market = new Market();
        $market->setConnection($this->connection);
        $market->mktm_name = $value->market_name;
        $market->mktm_code = $value->market_code;
        $market->ward_id = $ward_id->id;
        $market->cont_id = $this->currentUser->country()->id;
        $market->lfcl_id = 1;
        $market->aemp_iusr = $this->currentUser->employee()->id;
        $market->aemp_eusr = $this->currentUser->employee()->id;
        $market->save();

    }
    public function collection()
    {
        $dataRow = DB::connection($this->connection)->select("SELECT 
        t1.id,t1.mktm_code,t1.mktm_name,t2.ward_code, t2.id ward_id
        FROM tm_mktm t1
        INNER JOIN tm_ward t2 ON t1.ward_id=t2.id ORDER BY t1.id ASC");
        return collect($dataRow);
    }

    public function ward()
    {
        return Ward::on($this->connection)->find($this->ward_id);
    }
}
