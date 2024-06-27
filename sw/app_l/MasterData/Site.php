<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Site extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;

    protected $fillable = ['site_name', 'site_olnm', 'site_mob1', 'site_vtrn', 'aemp_eusr'];

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'market_id',
            'market_name',
            'sub_channel_id',
            'sub_channel_name',
            'category_id',
            'category_name',
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
            'reg_no',
            'house_no',
            'vat_trn',
        ];
    }

    public function model(array $value)
    {
       try{
        $request = (object)$value;
        $site_exist=Site::on($this->connection)->where(['site_code'=>$request->code])->first();
        if($site_exist){
            $site_exist->site_name = $request->name;
            // $site_exist->site_code = $request->code;
            $site_exist->outl_id = $outlet->id;
            $site_exist->site_olnm = $request->ln_name != "" ? $request->ln_name : '';
            $site_exist->site_adrs = $request->address != "" ? $request->address : '';
            $site_exist->site_olad = $request->ln_address != "" ? $request->ln_address : '';
            $site_exist->mktm_id = $request->market_id;
            $site_exist->site_ownm = $request->owner_name != "" ? $request->owner_name : '';
            $site_exist->site_olon = $request->ln_owner_name != "" ? $request->ln_owner_name : '';
            $site_exist->site_mob1 = $request->mobile_1 != "" ? $request->mobile_1 : '';
            $site_exist->site_mob2 = $request->mobile_2 == '' ? "" : $request->mobile_2;
            $site_exist->site_emal = $request->email == '' ? "" : $request->email;
            $site_exist->scnl_id = $request->sub_channel_id;
            $site_exist->otcg_id = $request->category_id;
            $site_exist->site_hsno = $request->house_no == '' ? "" : $request->house_no;;
            $site_exist->site_vtrn = $request->vat_trn == '' ? "" : $request->vat_trn;;
            $site_exist->site_vsts = 0;
            $site_exist->site_imge = '';
            $site_exist->site_omge = '';
            $site_exist->geo_lat = 0;
            $site_exist->geo_lon = 0;
            $site_exist->site_reg = $request->reg_no == '' ? "" : $request->reg_no;
            $site_exist->site_vrfy = 0;
            $site_exist->cont_id = $this->currentUser->employee()->cont_id;
            $site_exist->lfcl_id = 1;
            $site_exist->aemp_iusr = $this->currentUser->employee()->id;
            $site_exist->aemp_eusr = $this->currentUser->employee()->id;
            $site_exist->var = 1;
            $site_exist->attr1 = '';
            $site_exist->attr2 = '';
            $site_exist->attr3 = 0;
            $site_exist->attr4 = 0;
            $site_exist->save();
        }
        else{
            $outlet = new Outlet();
            $outlet->setConnection($this->connection);
            $outlet->oult_name = $request->name;
            $outlet->oult_code = $request->code;
            $outlet->oult_olnm = $request->ln_name != "" ? $request->ln_name : '';
            $outlet->oult_adrs = $request->address != "" ? $request->address : '';
            $outlet->oult_olad = $request->ln_address != "" ? $request->ln_address : '';
            $outlet->oult_ownm = $request->owner_name != "" ? $request->owner_name : '';
            $outlet->oult_olon = $request->ln_owner_name != "" ? $request->ln_owner_name : '';
            $outlet->oult_mob1 = $request->mobile_1 != "" ? $request->mobile_1 : '';
            $outlet->oult_mob2 = $request->mobile_2 == '' ? "" : $request->mobile_2;
            $outlet->oult_emal = $request->email == '' ? "" : $request->email;
            $outlet->cont_id = $this->currentUser->employee()->cont_id;
            $outlet->lfcl_id = 1;
            $outlet->aemp_iusr = $this->currentUser->employee()->id;
            $outlet->aemp_eusr = $this->currentUser->employee()->id;
            $outlet->var = 0;
            $outlet->attr1 = '';
            $outlet->attr2 = '';
            $outlet->attr3 = 0;
            $outlet->attr4 = 0;
            $outlet->save();
            $site = new Site();
            $site->setConnection($this->connection);
            $site->site_name = $request->name;
            $site->site_code = $request->code;
            $site->outl_id = $outlet->id;
            $site->site_olnm = $request->ln_name != "" ? $request->ln_name : '';
            $site->site_adrs = $request->address != "" ? $request->address : '';
            $site->site_olad = $request->ln_address != "" ? $request->ln_address : '';
            $site->mktm_id = $request->market_id;
            $site->site_ownm = $request->owner_name != "" ? $request->owner_name : '';
            $site->site_olon = $request->ln_owner_name != "" ? $request->ln_owner_name : '';
            $site->site_mob1 = $request->mobile_1 != "" ? $request->mobile_1 : '';
            $site->site_mob2 = $request->mobile_2 == '' ? "" : $request->mobile_2;
            $site->site_emal = $request->email == '' ? "" : $request->email;
            $site->scnl_id = $request->sub_channel_id;
            $site->otcg_id = $request->category_id;
            $site->site_hsno = $request->house_no == '' ? "" : $request->house_no;;
            $site->site_vtrn = $request->vat_trn == '' ? "" : $request->vat_trn;;
            $site->site_vsts = 0;
            $site->site_imge = '';
            $site->site_omge = '';
            $site->geo_lat = 0;
            $site->geo_lon = 0;
            $site->site_reg = $request->reg_no == '' ? "" : $request->reg_no;
            $site->site_vrfy = 0;
            $site->cont_id = $this->currentUser->employee()->cont_id;
            $site->lfcl_id = 1;
            $site->aemp_iusr = $this->currentUser->employee()->id;
            $site->aemp_eusr = $this->currentUser->employee()->id;
            $site->var = 1;
            $site->attr1 = '';
            $site->attr2 = '';
            $site->attr3 = 0;
            $site->attr4 = 0;
            $site->save();
        }
        
       }
       catch(\Exception $e){
         dd($e->getMessage());
       }
        
       
    }

    public function subChannel()
    {
        return SubChannel::on($this->connection)->find($this->scnl_id);
    }

    public function outletCategory()
    {
        return OutletCategory::on($this->connection)->find($this->otcg_id);
    }

    public function market()
    {
        return Market::on($this->connection)->find($this->mktm_id);
    }

    public function outlet()
    {
        return Outlet::on($this->connection)->find($this->outl_id);
    }

}
