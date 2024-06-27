<?php
namespace App\GPCollection;

use Illuminate\Database\Eloquent\Model;

class DMCollection extends Model
{
    protected $table = 'dm_collection';
    public $timestamps = false;
    protected $fillable = [
        'ACMP_CODE', 'COLL_DATE', 'COLL_NUMBER', 'IBS_INVOICE', 'AEMP_ID', 'AEMP_USNM', 'DM_CODE', 'WH_ID', 'SITE_ID',
        'SITE_CODE', 'COLLECTION_AMNT', 'COLL_REC_HO', 'COLLECTION_TYPE', 'STATUS', 'CHECK_NUMBER', 'INVT_ID',
        'CHECK_IMAGE', 'BANK_NAME', 'update_at', 'CHECK_DATE', 'slgp_id', 'sync_status', 'stcm_sync', 'COLL_NOTE',
    ];
}


