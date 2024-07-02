<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Hash;

use DB;

class DealerLoginUpload extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dlrm';
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
        return ['dealer_name', 'dealer_code'];
    }

    public function model(array $row)
    {
        $this->db = Auth::user()->country()->cont_conn;
        $value = (object)$row;
        $dlrid = $value->dealer_code;
        //$dlr = DB::connection($this->connection)->select("SELECT `id` FROM `tm_dlrm` WHERE `dlrm_code`='$dlrid'");
        //dd($dlr);
        $dlr = Depot:: on($this->db)->where(['dlrm_code' => $dlrid])->first();
        //$slgp = SalesGroup::on($this->connection)->where(['slgp_code' => $value->sales_group_code])->first();
        //$thana = Thana::on($this->connection)->where(['than_code' => $value->than_id])->first();
        //$Base = Base::on($this->connection)->where(['base_code' => $value->base_code])->first();

        if ($dlr != null) {

            $name = $dlr->dlrm_name;

            $code = $dlr->dlrm_code;
            $dlrm_adrs = $dlr->dlrm_adrs;
            $idddfdfd = $dlr->id;
            $group_id = $dlr->slgp_id;
            $cont_id = $dlr->cont_id;
            $hashed = Hash::make($code);

            if ($cont_id == '2') {
                $sql = "INSERT INTO dist.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', 'myprg_pran', '1','1', '$cont_id')";
            }
            if ($cont_id == '5') {
                $sql = "INSERT INTO dist_rfl.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', 'myprg_rfl', '1','1', '$cont_id')";
            }


            DB::connection($this->db)->select($sql);

            $depot = Depot::on($this->db)->findorfail($idddfdfd);
            $depot->dlrm_akey = 'Y';

            $depot->save();

        }
    }

    public function region()
    {
        return Region::on($this->connection)->find($this->dirg_id);
    }
}
