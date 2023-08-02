<?php

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{


    protected $table = 'tl_astd';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['asset_id', 'group_id', 'item_code'];
    }


    public function model(array $row)
    {
        $value = (object)$row;


        $assetMapping = new AssetMapping();
        $assetMapping->setConnection($this->connection);
        $assetMapping->astm_id = $value->asset_id;
        $assetMapping->slgp_id = $value->group_id;
        $assetMapping->amim_id = $this->getItemId($value->item_code);

        $assetMapping->aemp_iusr = $this->currentUser->employee()->id;
        $assetMapping->aemp_eusr = $this->currentUser->employee()->id;

        $assetMapping->save();

    }

    public function getItemId($item_code)
    {
        return DB::connection($this->connection)->table('tm_amim')->where('amim_code', $item_code)->value('id');
    }
}
