<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SKUActiveInactive extends Model implements WithHeadings, ToModel, WithHeadingRow
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
            'item_code',
            'lfcl_id',
        ];
    }

    public function model(array $value)
    {
       
        $value = (object)$value;
        $site=SKU::on($this->connection)->where(['amim_code' => $value->item_code])->first();
        if($site !=null){
            $site->lfcl_id=$value->lfcl_id;
            $site->aemp_eusr=Auth::user()->employee()->id;
            $site->save();
        }
       
       
    }
}
