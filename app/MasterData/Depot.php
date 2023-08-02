<?php

namespace App\MasterData;

use App\BusinessObject\SalesGroup;
use App\MasterData\Thana;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class Depot extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dlrm';
    protected $connection = '';
    private $currentUser;

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
            'sales_group_code',
            'than_code',
            'base_code',
            'dealer_name',
            'dealer_code',
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
        $dlrid = $value->dealer_code;
        //$dlr = DB::connection($this->connection)->select("SELECT `id` FROM `tm_dlrm` WHERE `dlrm_code`='$dlrid'");
        //dd($dlr);
        $dlr = Depot:: on($this->connection)->where(['dlrm_code' => $value->dealer_code])->first();
        $slgp = SalesGroup::on($this->connection)->where(['slgp_code' => $value->sales_group_code])->first();
        $thana = Thana::on($this->connection)->where(['than_code' => $value->than_id])->first();
        $Base = Base::on($this->connection)->where(['base_code' => $value->base_code])->first();

        if ($dlr != null) {
            $id = $dlr->id;
            $depot = Depot::on($this->connection)->findorfail($id);

            $depot->dlrm_name = str_replace("'", '', $value->dealer_name);
            $depot->dlrm_olnm = str_replace("'", '', $value->ln_name) != "" ? str_replace("'", '', $value->ln_name) : "";
            $depot->dlrm_adrs = str_replace("'", '', $value->address) != "" ? str_replace("'", '', $value->address) : "";
            $depot->dlrm_olad = str_replace("'", '', $value->ln_address) != "" ? str_replace("'", '', $value->ln_address) : "";
            $depot->dlrm_ownm = str_replace("'", '', $value->owner_name) != "" ? str_replace("'", '', $value->owner_name) : "";
            $depot->dlrm_olon = str_replace("'", '', $value->ln_owner_name) != "" ? str_replace("'", '', $value->ln_owner_name) : "";
            $depot->dlrm_mob1 = $value->mobile_1 != "" ? $value->mobile_1 : "";
            $depot->dlrm_mob2 = $value->mobile_2 != "" ? $value->mobile_2 : "";
            $depot->dlrm_emal = $value->email != "" ? $value->email : "";
            $depot->dlrm_zpcd = $value->zip_code != "" ? $value->zip_code : "";
            $depot->slgp_id = $slgp->id;
            $depot->than_id = $thana->id;
            $depot->base_id = $Base->id;
            $depot->cont_id = $this->currentUser->country()->id;
            $depot->lfcl_id = 1;
            $depot->aemp_iusr = $this->currentUser->employee()->id;
            $depot->aemp_eusr = $this->currentUser->employee()->id;
            $depot->save();
        } else {
            $depot = new Depot();
            $depot->setConnection($this->connection);

            $depot->acmp_id = $slgp->acmp_id;
            $depot->dptp_id = 1;
            $depot->whos_id = 1;
            $depot->dlrm_code = $value->dealer_code;
            $depot->dlrm_name = str_replace("'", '', $value->dealer_name);
            $depot->dlrm_olnm = str_replace("'", '', $value->ln_name) != "" ? str_replace("'", '', $value->ln_name) : "";
            $depot->dlrm_adrs = str_replace("'", '', $value->address) != "" ? str_replace("'", '', $value->address) : "";
            $depot->dlrm_olad = str_replace("'", '', $value->ln_address) != "" ? str_replace("'", '', $value->ln_address) : "";
            $depot->dlrm_ownm = str_replace("'", '', $value->owner_name) != "" ? str_replace("'", '', $value->owner_name) : "";
            $depot->dlrm_olon = str_replace("'", '', $value->ln_owner_name) != "" ? str_replace("'", '', $value->ln_owner_name) : "";
            $depot->dlrm_mob1 = $value->mobile_1 != "" ? $value->mobile_1 : "";
            $depot->dlrm_mob2 = $value->mobile_2 != "" ? $value->mobile_2 : "";
            $depot->dlrm_emal = $value->email != "" ? $value->email : "";
            $depot->dlrm_zpcd = $value->zip_code != "" ? $value->zip_code : "";
            $depot->slgp_id = $slgp->id;
            $depot->than_id = $thana->id;
            $depot->base_id = $Base->id;
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
        

    }


    public function salesGroup()
    {
        return SalesGroup::on($this->connection)->find($this->slgp_id);
    }

    public function thana()
    {
        return Thana::on($this->connection)->find($this->than_id);
    }

    public function base()
    {
        return Base::on($this->connection)->find($this->base_id);
    }

    public function WareHouse()
    {
        return WareHouse::on($this->connection)->find($this->whos_id);
    }
}
