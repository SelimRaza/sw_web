<?php

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\ReturnLine;
use App\BusinessObject\SequenceMappingReturn;
use App\BusinessObject\SequenceNumber;
use App\BusinessObject\SequenceMappingInvoice;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\Trip;
use App\BusinessObject\TripGRV;
use App\BusinessObject\TripGrvSku;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use App\MasterData\Employee;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrinterData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function salesInvoicePrint(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $order_id=$request->order_id;
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
            $data =    (array)collect($data1 = DB::connection($country->cont_conn)->select("SELECT
  t2.acmp_titl                                                                         AS title,
  t2.acmp_name                                                                         AS ou_name,
  t2.acmp_addr                                                                         AS ou_address,
  t2.acmp_nvat                                                                         AS vat_number,
  t2.acmp_nexc                                                                         AS exciese_number,
  t1.acmp_id                                                                           AS ou_id,
  t1.dlrm_id                                                                           AS depo_id,
  t3.dlrm_name                                                                         AS depo_name,
  t1.aemp_id                                                                           AS sr_id,
  t4.aemp_name                                                                         AS sr_name,
  t5.id                                                                                AS site_id,
  t5.site_code                                                                         AS site_code,
  t5.outl_id                                                                           AS outlet_id,
  t5.site_name                                                                         AS site_name,
  t5.site_adrs                                                                         AS site_address,
  t5.site_mob1                                                                         AS site_mobile,
  t1.ordm_ornm                                                                         AS order_id,
  t1.ordm_date                                                                         AS order_date,
  t1.ordm_drdt                                                                         AS delivery_date,
  t5.site_vtrn                                                                         AS site_trn,
  IFNULL(t6.IBS_INVOICE,'-')                                                           AS vat_invoice,
  IFNULL(round(t6.DELV_AMNT,3),0)                                                      AS invoice_amount
FROM tt_ordm AS t1
  INNER JOIN tm_acmp AS t2 ON t1.acmp_id = t2.id
  INNER JOIN tm_dlrm AS t3 ON t1.dlrm_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
  INNER JOIN tm_site AS t5 ON t1.site_id = t5.id
  LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
WHERE t1.ordm_ornm = '$order_id';"))->first();

            $data2 = DB::connection($country->cont_conn)->select("SELECT
  t1.ordm_ornm AS order_id,
  t2.amim_code AS product_id,
  t2.amim_name AS product_name,
  t1.ordd_duft AS ctn_size,
  t1.ordd_uprc AS unit_price,
  t1.ordd_dqty AS delivery_qty,
  t1.ordd_dfdd AS def_discount_delivery,
  t1.ordd_spdd AS delivery_discount,
  t1.ordd_dpds AS promo_discount_delivery,
  t1.ordd_odat AS total_delivered_amount,
  t1.prom_id   AS promo_ref,
  t1.ordd_excs AS gst,
  t1.ordd_ovat AS vat,
  t1.ordd_tvat AS total_vat,
  t1.ordd_texc AS total_excise,
  t1.ordd_amnt AS net_amount
FROM tt_ordd AS t1
  INNER JOIN tm_amim AS t2 ON t1.amim_id = t2.id
WHERE t1.ordm_ornm = '$order_id'");

            $data['orderLine']=$data2;
            return Array("data" => $data, "action" => $request->country_id);
        }
    }
}