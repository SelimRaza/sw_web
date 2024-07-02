<?php
namespace App\GPCollection;

use Illuminate\Database\Eloquent\Model;

class InvoiceMap extends Model
{
    public $timestamps = false;
    protected $table = 'dm_invoice_collection_mapp';
    protected $fillable = [
        'ACMP_CODE', 'TRN_DATE', 'MAP_ID', 'TRANSACTION_ID', 'SITE_ID', 'SITE_CODE', 
        'DEBIT_AMNT', 'CRECIT_AMNT', 'DELV_AMNT', 'update_at', 'sync_status', 'stcm_sync'
    ];
    

}

