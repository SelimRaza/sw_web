<?php

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetSite extends Model implements WithHeadings, ToModel, WithHeadingRow
{


    protected $table = 'tl_assm';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['asset_id', 'group_id', 'zone_code', 'site_code'];
    }


    public function model(array $row)
    {
        $value = (object)$row;


        try {
            $site_id = $this->getSiteId($value->site_code);
            $zone_id = $this->getZoneId($value->zone_code);
            if($site_id){
                $exist = AssetSite::where([
                    'astm_id'=>$value->asset_id,
                    'slgp_id'=>$value->group_id,
                    'zone_id'=>$zone_id,
                    'site_id'=>$site_id,
                ])->first();

                if(!$exist){
                    $assetMapping = new AssetSite();
                    $assetMapping->setConnection($this->connection);
                    $assetMapping->astm_id = $value->asset_id;
                    $assetMapping->slgp_id = $value->group_id;
                    $assetMapping->zone_id = $zone_id;
                    $assetMapping->site_id = $site_id;
                    $assetMapping->aemp_iusr = $this->currentUser->employee()->id;
                    $assetMapping->aemp_eusr = $this->currentUser->employee()->id;
                    $assetMapping->save();
                }
            }
        }catch(\Exception $e)
        {
            return;
        }


//        $exist = AssetSite::where([
//            'astm_id'=>$value->asset_id,
//            'slgp_id'=>$value->group_id,
//            'zone_id'=>$zone_id,
//            'site_id'=>$site_id,
//        ])->first();


//        if($exist)
//        {
//            return;
//            dd($site_id, $exist, 'if block');
//        }else{
//            dd($site_id, $exist, $value, 'else block');
//            $assetMapping = new AssetSite();
//            $assetMapping->setConnection($this->connection);
//            $assetMapping->astm_id = $value->asset_id;
//            $assetMapping->slgp_id = $value->group_id;
//            $assetMapping->zone_id = $zone_id;
//            $assetMapping->site_id = $site_id;
//            $assetMapping->aemp_iusr = $this->currentUser->employee()->id;
//            $assetMapping->aemp_eusr = $this->currentUser->employee()->id;
//            $assetMapping->save();
    }

    public function getZoneId($zone_code)
    {
        return DB::connection($this->connection)->table('tm_zone')->where('zone_code', $zone_code)->value('id');
    }

    public function getSiteId($site_code)
    {
        return DB::connection($this->connection)->table('tm_site')->where('site_code', $site_code)->value('id');
    }
}
