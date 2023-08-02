<?php

namespace App\BusinessObject;

use App\BusinessObject\SalesGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Promotion extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_prom';
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
        return [
            'sales_gorup_id',
            'than_id',
            'base_id',
            'name',
            'code',
            'ln_name',
            'address',
            'ln_address',
            'owner_name',
            'ln_owner_name',
            'mobile_1',
            'mobile_2',
            'email',
            'zip_code',
        ];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $depot = new Depot();
        $depot->setConnection($this->db);
        $depot->dlrm_code = $value->code;
        $depot->dlrm_name = $value->name;
        $depot->dlrm_olnm = $value->ln_name != "" ? $value->ln_name : "";
        $depot->dlrm_adrs = $value->address != "" ? $value->address : "";
        $depot->dlrm_olad = $value->ln_address != "" ? $value->ln_address : "";
        $depot->dlrm_ownm = $value->owner_name != "" ? $value->owner_name : "";
        $depot->dlrm_olon = $value->ln_owner_name != "" ? $value->ln_owner_name : "";
        $depot->dlrm_mob1 = $value->mobile_1 != "" ? $value->mobile_1 : "";
        $depot->dlrm_mob2 = $value->mobile_2 != "" ? $value->mobile_2 : "";
        $depot->dlrm_emal = $value->email != "" ? $value->email : "";
        $depot->dlrm_zpcd = $value->zip_code != "" ? $value->zip_code : "";
        $depot->slgp_id = $value->sales_gorup_id;
        $depot->than_id = $value->than_id;
        $depot->base_id = $value->base_id;
        $depot->cont_id = $this->currentUser->country()->id;
        $depot->lfcl_id = 1;
        $depot->aemp_iusr = $this->currentUser->employee()->id;
        $depot->aemp_eusr = $this->currentUser->employee()->id;
        $depot->var = 1;
        $depot->attr1 = '';
        $depot->attr2 = '';
        $depot->attr3 = 0;
        $depot->attr4 = 0;
        $depot->save();
    }


    public function salesGroup()
    {
        return SalesGroup::on($this->connection)->find($this->slgp_id);
    }
    /*
        public function WareHouse()
        {
            return WareHouse::on($this->connection)->find($this->wh_id);
        }*/
}
