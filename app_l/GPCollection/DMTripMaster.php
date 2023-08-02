<?php
namespace App\GPCollection;

use Illuminate\Database\Eloquent\Model;

class DMTripMaster extends Model
{
    protected $table = 'dm_trip_master';
    public $timestamps = false;
    protected $fillable = [
        'ACMP_CODE', 'ORDM_DATE', 'ORDM_ORNM', 'ORDM_DRDT', 'AEMP_ID', 'AEMP_USNM', 'WH_ID', 'SITE_ID', 'SITE_CODE',
        'GEO_LAT', 'GEO_LON', 'ORDD_AMNT', 'INV_AMNT', 'DELV_AMNT', 'COLLECTION_AMNT', 'SHIPINGADD', 'DM_CODE',
        'IBS_INVOICE', 'V_NAME', 'TRIP_NO', 'COLL_STATUS', 'update_at', 'DELIVERY_STATUS', 'slgp_id', 'SALES_TYPE'
    ];
    
}


