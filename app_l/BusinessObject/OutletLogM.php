<?php
namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;

class OutletLogM extends Model
{
    protected $table = 'tbl_site_log1';
    public $timestamps = false;
    protected $fillable = [
                'id',
                'site_id',
                'site_code',
                'site_name',
                'site_name_bn',
                'site_adrs',
                'site_adrs_bn',
                'owner_name',
                'owner_name_bn',
                'site_mobile1',
                'site_mobile2',
                'geo_lat',
                'geo_lon',
                'is_fridge',
                'is_shop_sign',
                'otcg_id',
                'scnl_id',
                'site_image',
                'is_vrfy',
                'otp_code',
                'scont_id',
                'aemp_iusr',
                'aemp_eusr',
                'created_at',
                'updated_at'
    ];
}


