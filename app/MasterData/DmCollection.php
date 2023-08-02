<?php

namespace App\MasterData;

use App\MasterData\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToArray;
use App\BusinessObject\SalesGroup;
use App\Mapping\EmployeeManagerUpload;

use DB;

class DmCollection extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'dm_collection';
    private $currentUser;
    protected $connection = '';

    const CREATED_AT = 'update_at';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {

        return ['COLL_NUMBER', 'AEMP_USNM', 'SLGP_ID'];
    }

    public function array(array $array)
    {

        $data = $array;
        $check = 1;
        for ($i = 0; $i < count($data); $i++) {

            $check++;

            //  $slgp_data = SalesGroup::on($this->connection)->where(['slgp_code' => $data[$i]['slgp_code']])->first();
            $dm_data = DmCollection::on($this->connection)->where(['COLL_NUMBER' => $data[$i]['coll_number']])->first();
            $aemp_data = EmployeeManagerUpload::where('aemp_usnm', $data[$i]['aemp_usnm'])->first();
            $dm_id = $dm_data->ID;
            $slgp_id = $data[$i]['slgp_id'];

            //  dd($data, $slgp_data->id, $dm_data->ID, $aemp_data->id);

            if ($dm_data != null) {
                DB::connection($this->connection)->select("Update dm_collection SET AEMP_ID='$aemp_data->id', AEMP_USNM='$aemp_data->aemp_usnm', slgp_id=$slgp_id WHERE ID=$dm_id");
            }
        }
    }
}
