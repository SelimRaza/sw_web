<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiteActvInactv extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;

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
            'site_code',
            'lfcl_id',
        ];
    }

    public function model(array $value)
    {
       
        $value = (object)$value;
        $site=Site::on($this->connection)->where(['site_code' => $value->site_code])->first();
        if($site !=null){
            $site->lfcl_id=$value->lfcl_id;
            $site->aemp_eusr=Auth::user()->employee()->id;
            $site->save();
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
