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

class Ward extends Model implements WithHeadings, ToModel, WithHeadingRow,FromCollection
{
    protected $table = 'tm_ward';
    private $currentUser;
    protected $connection = '';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }
    public static function create()
    {
        $instance = new self();
        return $instance;
    }

    public function headings(): array
    {
        return ['id','ward_code', 'ward_name', 'thana_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $thana_id = Thana::on($this->connection)->where(['than_code' => $value->thana_code])->first();

        $ward = new Ward();
        $ward->setConnection($this->connection);
        $ward->ward_name = $value->ward_name;
        $ward->ward_code = $value->ward_code;
        $ward->than_id = $thana_id->id;
        $ward->cont_id = $this->currentUser->employee()->cont_id;
        $ward->lfcl_id = 1;
        $ward->aemp_iusr = $this->currentUser->employee()->id;
        $ward->aemp_eusr = $this->currentUser->employee()->id;
        $ward->save();
    }
    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("SELECT t1.id,ward_code,ward_name,t2.than_code FROM `tm_ward` t1
        INNER JOIN tm_than t2 ON t1.than_id=t2.id ORDER BY t1.id ASC");
        return collect($dataRow);
      //  return collect(null);
    }

    public function thana()
    {
        return Thana::on($this->connection)->find($this->than_id);
    }
}
