<?php

namespace App\Http\Controllers\PrintData;

use App\Blog\Post;
use App\Blog\PostType;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\ReturnMaster;
use App\MasterData\Country;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function __construct()
    {

    }

    public function orderPrint($cont_id, $order_id)
    {
        $country = (new Country())->country($cont_id);
       // if (strpos( '3,4',"$country->id") !== false) {
        if ($country != false) {
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }

            $orderMasterData = collect(DB::connection($country->cont_conn)->select("SELECT
  t1.ordm_ornm                           AS Order_ID,
  DATE_FORMAT(t1.ordm_date, '%b %d, %Y') AS order_date,
  DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y') AS delivery_date,
  t3.site_name                           AS Outlet_Name,
  t3.site_name                           AS Site_Name,
  t1.ordm_amnt                           AS total_price,
  '0'                                    AS discount,
  t1.site_id                             AS customer_number,
  t3.outl_id                             AS outlet_id,
  t10.optp_name                          AS Payment_Type,
  ''                                     AS Region_Name,
  ''                                     AS Zone_Name,
  t3.site_adrs                           AS site_address,
  t3.site_adrs                           AS outlet_address,
  t4.aemp_name                           AS preseller_name,
  t5.id                                  AS ou_id,
  t5.acmp_name                           AS ou_name,
  t5.acmp_nexc                           AS tax_number,
  t5.acmp_nvat                           AS vat_number,
  t5.acmp_addr                           AS address,
  t3.site_vtrn                           AS VAT_TRN,
  t5.acmp_titl                           AS invoice_title,
  t5.acmp_vats                           AS vat_status,
  t5.acmp_crnc                           AS currency,
  t5.acmp_dgit                           AS round_digit,
  t5.acmp_rond                           AS round,
  t5.acmp_note                           AS note
FROM tt_ordm AS t1
  INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
  INNER JOIN tm_acmp AS t5 ON t1.acmp_id = t5.id
  left JOIN tl_stcm AS t9 ON t1.acmp_id = t9.acmp_id AND t1.site_id = t9.site_id
  left JOIN tm_optp AS t10 ON t9.optp_id = t10.id
WHERE t1.ordm_ornm = '$order_id'"))->first();


            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
  t2.amim_id                                                        AS Product_id,
  t3.amim_name                                                      AS Product_Name,
  t3.amim_olin                                                      AS sku_print_name,
  floor(t2.ordd_qnty / t2.ordd_duft)                                AS ctn,
  t2.ordd_qnty % t2.ordd_duft                                       AS pcs,
  round((t2.ordd_uprc*t2.ordd_qnty),3)                              as Total_Item_Price,
  t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
  (t2.ordd_opds + t2.ordd_spdo + t2.ordd_dfdo) * 100 / t2.ordd_oamt AS ratio,
  t2.ordd_opds + t2.ordd_spdo + t2.ordd_dfdo                        AS Discount,
  t2.ordd_duft                                                      AS ctn_size,
  t2.prom_id                                                        AS promo_ref,
  round(t2.ordd_texc,3)                                                      AS gst,
  t2.ordd_ovat                                                      AS vat,
  round(t2.ordd_tvat,3)                                                      AS tvat
  
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.ordm_ornm = '$order_id'");

            //dd($orderMasterData);
            return view('PrintData.order_print')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);
        }
        else{
            dd("Print Not Available");
        }

    }

    public function salesInvoicePrint($cont_id, $order_id)
    {
        $country = (new Country())->country($cont_id);
       // if (strpos( '3,4',"$country->id") !== false) {
        if ($country != false) {
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
            $orderMasterData = collect(\DB::connection($country->cont_conn)->select("SELECT
  t1.ordm_ornm                                          AS Order_ID,
  DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
  DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
  t2.site_name                                          AS Outlet_Name,
  t2.site_name                                          AS Site_Name,
  t6.DELV_AMNT                                          AS total_price,
  '0'                                                   AS discount,
  t1.site_id                                            AS customer_number,
  t2.outl_id                                            AS outlet_id,
  t9.optp_name                                          AS Payment_Type,
  ''                                                    AS Region_Name,
  ''                                                    AS Zone_Name,
  t2.site_adrs                                          AS site_address,
  t2.site_adrs                                          AS outlet_address,
  t3.aemp_name                                          AS preseller_name,
  t4.id                                                 AS ou_id,
  t4.acmp_name                                          AS ou_name,
  t4.acmp_note                                          AS acmp_note,
  t4.acmp_nexc                                          AS tax_number,
  t4.acmp_nvat                                          AS vat_number,
  ''                                                    AS year,
  ''                                                    AS serial_number,
  IFNULL(t6.IBS_INVOICE,'-')                            AS vat_sl_number,
  t4.acmp_addr                                          AS address,
  t2.site_vtrn                                          AS VAT_TRN,
  t4.acmp_titl                                          AS invoice_title,
  t4.acmp_vats                                          AS vat_status,
  t6.DELV_AMNT                                          AS invoice_amount,
  t4.acmp_creg                                          AS currency,
  t4.acmp_dgit                                          AS round_digit,
  t4.acmp_rond                                          AS round,
  t4.acmp_note                                          AS note
FROM tt_ordm AS t1
  INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
  INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
  left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id
  left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
WHERE t1.ordm_ornm = '$order_id'"))->first();


            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
  t2.amim_id                                                        AS Product_id,
  t3.amim_name                                                      AS Product_Name,
  t3.amim_olin                                                      AS sku_print_name,
  floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
  t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
  round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
  t2.ordd_uprc * t2.ordd_duft                                       AS Rate,
  (t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd) * 100 / t2.ordd_amnt AS ratio,
  t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
  t2.ordd_duft                                                      AS ctn_size,
  t2.prom_id                                                        AS promo_ref,
  t2.ordd_excs                                                      AS gst,
  t2.ordd_ovat                                                      AS vat,
  t4.DISCOUNT                                                       AS total_discount,
  round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
  round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
  round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-t4.DISCOUNT+
  round((((t4.ORDD_UPRC*t4.DELV_QNTY)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
  round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.amim_id=t4.AMIM_ID
WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0;");
            return view('PrintData.sales_invoice_print')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);
        }
		
        else{
            dd("Print Not Available");
        }
    }
	public function salesInvoicePrint_ou($cont_id, $order_id,$ou_id)
    {
        $country = (new Country())->country($cont_id);

        if ($country != false) {
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
		if($cont_id==14){
			  $orderMasterData = collect(\DB::connection($country->cont_conn)->select("SELECT
                        t1.ordm_ornm                                          AS Order_ID,
                        DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
                        DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
                        IFNULL(t10.mother_site_name,t2.site_name)             AS Outlet_Name,
                        t2.site_name                                          AS Site_Name,
                        t6.DELV_AMNT                                          AS total_price,
                        '0'                                                   AS discount,
                        t2.site_code                                          AS customer_number,
                        IFNULL(t10.mother_site_code,t2.outl_id)               AS outlet_id,
                        t9.optp_name                                          AS Payment_Type,
                        ''                                                    AS Region_Name,
                        ''                                                    AS Zone_Name,
                        t6.SHIPINGADD                                         AS site_address,
                      IFNULL(t10.billing_address,t2.site_adrs)               AS outlet_address,
                        t3.aemp_name                                          AS preseller_name,
                        t3.aemp_mob1                                          AS preseller_mob,
                        t4.id                                                 AS ou_id,
                        t4.acmp_name                                          AS ou_name,
                        t4.acmp_note                                          AS acmp_note,
                        t4.acmp_nexc                                          AS tax_number,
                        t4.acmp_nvat                                          AS vat_number,
                        ''                                                    AS year,
                        ''                                                    AS serial_number,
                        IFNULL(t6.IBS_INVOICE,'-')                            AS vat_sl_number,
                        t4.acmp_addr                                          AS address,
                        t2.site_vtrn                                          AS VAT_TRN,
                        t4.acmp_titl                                          AS invoice_title,
                        t4.acmp_vats                                          AS vat_status,
                        t6.DELV_AMNT                                          AS invoice_amount,
                        t6.DM_CODE                                            AS DM_CODE,
                        t6.V_NAME                                             AS V_NAME,
                        IFNULL(t11.pocm_pono,'-')                             AS po_no,
                        IFNULL(t11.date,'-')                                  AS po_date,
                        t4.acmp_creg                                          AS currency,
                        t4.acmp_dgit                                          AS round_digit,
                        t4.acmp_rond                                          AS round,
                        t4.acmp_note                                          AS note
                      FROM tt_ordm AS t1
                        INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                        INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                        INNER JOIN tl_pcmp AS t4 ON t4.id=$ou_id
                        LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
                        INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
                        left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id
                        left JOIN tm_optp AS t9 ON t8.optp_id = t9.id
                        left JOIN tl_site_party_mapping AS t10 ON t10.site_code = t6.SITE_CODE
                        left JOIN tl_pocm AS t11 ON t6.ORDM_ORNM = t11.ordm_ornm
                      WHERE t1.ordm_ornm = '$order_id' GROUP BY  t1.ordm_ornm"))->first();

              if($ou_id==3){
				  $orderLineData = DB::connection($country->cont_conn)->select("SELECT
                          t3.amim_code                                                      AS Product_id,
                          t3.amim_name                                                      AS Product_Name,
                          t3.amim_olin                                                      AS sku_print_name,
                          floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                          t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                          round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
                          round(t2.ordd_uprc * t2.ordd_duft,2)                              AS Rate,
                          round((t4.DISCOUNT) * 100 /(t4.ORDD_UPRC*t4.DELV_QNTY),2)         AS ratio,
                          t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                          t2.ordd_duft                                                      AS ctn_size,
                          t2.prom_id                                                        AS promo_ref,
                          t2.ordd_excs                                                      AS gst,
                          t2.ordd_ovat                                                      AS vat,
                          round(t4.DISCOUNT,2)                                              AS total_discount,
                          round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                          round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                          round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-(t4.DISCOUNT)+
                          round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                          round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                        FROM tt_ordm AS t1
                          INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                          INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                          INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.id=t4.OID
                        WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0 AND t3.attr4 IN(2,3);");

          if($orderLineData){
			   
		      } else{
              return  dd("No item found under this company . Print Not Available");            
                  }
			  }else{				  			  
            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
                              t3.amim_code                                                      AS Product_id,
                              t3.amim_name                                                      AS Product_Name,
                              t3.amim_olin                                                      AS sku_print_name,
                              floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                              t2.ordd_dqty % t2.ordd_duft                                       AS pcs,
                              round((t4.ORDD_UPRC*t4.DELV_QNTY),3)                              AS Total_Item_Price,
                              round(t2.ordd_uprc * t2.ordd_duft,2)                              AS Rate,
                              round((t4.DISCOUNT) * 100 /(t4.ORDD_UPRC*t4.DELV_QNTY),2)         AS ratio,
                              t2.ordd_dpds + t2.ordd_spdd + t2.ordd_dfdd                        AS Discount,
                              t2.ordd_duft                                                      AS ctn_size,
                              t2.prom_id                                                        AS promo_ref,
                              t2.ordd_excs                                                      AS gst,
                              t2.ordd_ovat                                                      AS vat,
                            round(t4.DISCOUNT,2)                                               AS total_discount,
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                              round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-(t4.DISCOUNT)+
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                            FROM tt_ordm AS t1
                              INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                              INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                              INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.id=t4.OID
                            WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0 AND t3.attr4=1;");
		        } 
			
		    return view('PrintData.sales_invoice_print_ou')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);				
		}else{
		$orderMasterData = collect(\DB::connection($country->cont_conn)->select("SELECT
                        t1.ordm_ornm                                          AS Order_ID,
                        DATE_FORMAT(t1.ordm_date, '%b %d, %Y')                AS order_date,
                        DATE_FORMAT(t1.ordm_drdt, '%b %d, %Y')                AS delivery_date,
                        t2.site_name                                          AS Outlet_Name,
                        t2.site_name                                          AS Site_Name,
                        t6.DELV_AMNT                                          AS total_price,
                        '0'                                                   AS discount,
                        t2.site_code                                          AS customer_number,
                        IFNULL(t10.mother_site_code,t2.outl_id)               AS outlet_id,
                        t9.optp_name                                          AS Payment_Type,
                        ''                                                    AS Region_Name,
                        ''                                                    AS Zone_Name,
                        t6.SHIPINGADD                                         AS site_address,
                        t2.site_adrs                                          AS outlet_address,
                        t3.aemp_name                                          AS preseller_name,
                        t3.aemp_mob1                                          AS preseller_mob,
                        t4.id                                                 AS ou_id,
                        t4.acmp_name                                          AS ou_name,
                        t4.acmp_note                                          AS acmp_note,
                        t4.acmp_nexc                                          AS tax_number,
                        t4.acmp_nvat                                          AS vat_number,
                        ''                                                    AS year,
                        ''                                                    AS serial_number,
                        IFNULL(t6.IBS_INVOICE,'-')                            AS vat_sl_number,
                        t4.acmp_addr                                          AS address,
                        t2.site_vtrn                                          AS VAT_TRN,
                        t4.acmp_titl                                          AS invoice_title,
                        t4.acmp_vats                                          AS vat_status,
                        t6.DELV_AMNT                                          AS invoice_amount,
						t6.DM_CODE                                            AS DM_CODE,
                        t6.V_NAME                                             AS V_NAME,
						IFNULL(t11.pocm_pono,'-')                             AS po_no,
                        IFNULL(t11.date,'-')                                  AS po_date,
                        t4.acmp_creg                                          AS currency,
                        t4.acmp_dgit                                          AS round_digit,
                        t4.acmp_rond                                          AS round,
                        t4.acmp_note                                          AS note
                      FROM tt_ordm AS t1
                        INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                        INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
                        INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
                        LEFT JOIN dm_trip_master t6 ON t1.ordm_ornm=t6.ORDM_ORNM
                        INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
                        left JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id AND t6.slgp_id=t8.slgp_id
                        left JOIN tm_optp AS t9 ON t8.optp_id = t9.id 
						left JOIN tl_site_party_mapping AS t10 ON t10.site_code = t6.SITE_CODE
						left JOIN tl_pocm AS t11 ON t6.ORDM_ORNM = t11.ordm_ornm
                      WHERE t1.ordm_ornm = '$order_id'"))->first();


            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
                              t3.amim_code                                                      AS Product_id,
                              t3.amim_name                                                      AS Product_Name,
                              t3.amim_olin                                                      AS sku_print_name,
                              floor(t2.ordd_dqty / t2.ordd_duft)                                AS ctn,
                              t2.ordd_dqty % t2.ordd_duft                                       AS pcs,                              
							  if(t2.ordd_oamt=0,0,round((t4.ORDD_UPRC*t4.DELV_QNTY),3))         AS Total_Item_Price,
                              round((t2.ordd_uprc * t2.ordd_duft),2)                            AS Rate,
                              round((t4.DISCOUNT) * 100 / (t4.DELV_QNTY*t4.ORDD_UPRC),2)        AS ratio,
                              t4.DISCOUNT                                                       AS Discount,
                              t2.ordd_duft                                                      AS ctn_size,
                              t2.prom_id                                                        AS promo_ref,
                              t2.ordd_excs                                                      AS gst,
                              t2.ordd_ovat                                                      AS vat,
                              t4.DISCOUNT                                                       AS total_discount,
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)AS total_vat,
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)           AS total_gst,
                              round((t4.ORDD_UPRC*t4.DELV_QNTY),3)-(t4.DISCOUNT)+
                              round((((t4.ORDD_UPRC*t4.DELV_QNTY)-(t4.DISCOUNT)+(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100))*t4.ORDD_OVAT)/100,3)+
                              round(((t4.ORDD_UPRC*t4.DELV_QNTY)*t4.ORDD_EXCS)/100,3)AS net_amount
                            FROM tt_ordm AS t1
                              INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                              INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                              INNER JOIN dm_trip_detail t4 ON t1.ordm_ornm=t4.ORDM_ORNM AND t2.id=t4.OID
                            WHERE t1.ordm_ornm = '$order_id' AND t2.ordd_dqty != 0;");
           
         if($cont_id==15){
			  return view('PrintData.sales_invoice_print_qr_ksa')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);		   	  
		    }else{			 		 
		      return view('PrintData.sales_invoice_print_uae')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);			   		   
	  	         } 
		 }			   
        }else{
            dd("Print Not Available");
        }
    }
    

    public function returnPrint($cont_id, $order_id)
    {
        $country = (new Country())->country($cont_id);
      //  if (strpos( '3,4',"$country->id") !== false) {
			 if ($country != false) {
            $orderMaster = ReturnMaster::on($country->cont_conn)->where('rtan_rtnm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
            $orderMasterData = collect(DB::connection($country->cont_conn)->select("SELECT
  t1.rtan_rtnm                           AS Order_ID,
  DATE_FORMAT(t1.rtan_date, '%b %d, %Y') AS order_date,
  DATE_FORMAT(t1.rtan_drdt, '%b %d, %Y') AS delivery_date,
  t3.site_name                           AS Outlet_Name,
  t3.site_name                           AS Site_Name,
  t1.rtan_amnt                           AS total_price,
  '0'                                    AS discount,
  t1.site_id                             AS customer_number,
  t3.outl_id                             AS outlet_id,
  t10.optp_name                          AS Payment_Type,
  ''                                     AS Region_Name,
  ''                                     AS Zone_Name,
  t3.site_adrs                           AS site_address,
  t3.site_adrs                           AS outlet_address,
  t4.aemp_name                           AS preseller_name,
  t5.id                                  AS ou_id,
  t5.acmp_name                           AS ou_name,
  t5.acmp_nexc                           AS tax_number,
  t5.acmp_nvat                           AS vat_number,
  t5.acmp_addr                           AS address,
  t3.site_vtrn                           AS VAT_TRN,
  t5.acmp_titl                           AS invoice_title,
  t5.acmp_vats                           AS vat_status,
  t5.acmp_crnc                           AS currency,
  t5.acmp_dgit                           AS round_digit,
  t5.acmp_rond                           AS round,
  t5.acmp_note                           AS note
FROM tt_rtan AS t1
  LEFT JOIN tm_site AS t3 ON t1.site_id = t3.id
  LEFT JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
  LEFT JOIN tm_acmp AS t5 ON t1.acmp_id = t5.id
  LEFT JOIN tl_stcm AS t9 ON t1.acmp_id = t9.acmp_id AND t1.site_id = t9.site_id
  LEFT JOIN tm_optp AS t10 ON t9.optp_id = t10.id
WHERE t1.rtan_rtnm = '$order_id'"))->first();

            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
  t2.amim_id                         AS Product_id,
  t3.amim_name                       AS Product_Name,
  t3.amim_olin                       AS sku_print_name,
  floor(t2.rtdd_qnty / t2.rtdd_duft) AS ctn,
  t2.rtdd_qnty % t2.rtdd_duft        AS pcs,
  t2.rtdd_oamt                       AS Total_Item_Price,
  t2.rtdd_uprc * t2.rtdd_duft        AS Rate,
  0                                  AS ratio,
  0                                  AS Discount,
  t2.rtdd_duft                       AS ctn_size,
  0                                  AS promo_ref,
  t2.rtdd_excs                       AS gst,
  t2.rtdd_ovat                       AS vat

FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.rtan_rtnm = '$order_id'");
            return view('PrintData.return_print')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);
        }
        else{
            dd("Print Not Available");
        }
    }

    public function returnInvoicePrint($cont_id, $order_id)
    {
        $country = (new Country())->country($cont_id);
        if ($country != false) {
            $orderMaster = ReturnMaster::on($country->cont_conn)->where('rtan_rtnm', '=', $order_id)->first();
            if ($orderMaster == null) {
                $orderLog = OrderSyncLog::on($country->cont_conn)->where('oslg_moid', '=', $order_id)->first();
                if ($orderLog != null) {
                    $order_id = $orderLog->oslg_ornm;
                }
            }
            $orderMasterData = collect(\DB::connection($country->cont_conn)->select("SELECT
  t1.rtan_rtnm                                          AS Order_ID,
  DATE_FORMAT(t1.rtan_date, '%b %d, %Y')                AS order_date,
  DATE_FORMAT(t1.rtan_drdt, '%b %d, %Y')                AS delivery_date,
  t2.site_name                                          AS Outlet_Name,
  t2.site_name                                          AS Site_Name,
  t1.rtan_amnt                                          AS total_price,
  '0'                                                   AS discount,
  t1.site_id                                            AS customer_number,
  t2.outl_id                                            AS outlet_id,
  t9.optp_name                                          AS Payment_Type,
  ''                                                    AS Region_Name,
  ''                                                    AS Zone_Name,
  t2.site_adrs                                          AS site_address,
  t2.site_adrs                                          AS outlet_address,
  t3.aemp_name                                          AS preseller_name,
  t4.id                                                 AS ou_id,
  t4.acmp_name                                          AS ou_name,
  t4.acmp_nexc                                          AS tax_number,
  t4.acmp_nvat                                          AS vat_number,
  t5.vatn_year                                          AS year,
  t5.vatn_rcnt                                          AS serial_number,
  concat(t5.vatn_year, '-', LPAD(t5.vatn_rcnt, 8, '0')) AS vat_sl_number,
  t4.acmp_addr                                          AS address,
  t2.site_vtrn                                          AS VAT_TRN,
  t4.acmp_rttl                                          AS invoice_title,
  t4.acmp_vats                                          AS vat_status,
  t6.invc_amnt                                          AS invoice_amount,
  t4.acmp_creg                                          AS currency,
  t4.acmp_dgit                                          AS round_digit,
  t4.acmp_rond                                          AS round,
  t4.acmp_note                                          AS note
FROM tt_rtan AS t1
  INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  LEFT JOIN tm_vatr AS t5 ON t1.id = t5.rtan_id
  LEFT JOIN tt_invc AS t6 ON t1.rtan_rtnm = t6.invc_code
  INNER JOIN tm_cont AS t7 ON t4.cont_id = t7.id
  INNER JOIN tl_stcm AS t8 ON t1.acmp_id = t8.acmp_id AND t1.site_id = t8.site_id
  INNER JOIN tm_optp AS t9 ON t8.optp_id = t9.id
WHERE t1.rtan_rtnm = '$order_id'"))->first();


            $orderLineData = DB::connection($country->cont_conn)->select("SELECT
  t2.amim_id                         AS Product_id,
  t3.amin_snme                       AS Product_Name,
  t3.amim_olin                       AS sku_print_name,
  floor(t2.rtdd_dqty / t2.rtdd_duft) AS ctn,
  t2.rtdd_dqty % t2.rtdd_duft        AS pcs,
  t2.rtdd_damt                       AS Total_Item_Price,
  t2.rtdd_uprc * t2.rtdd_duft        AS Rate,
  0                                  AS ratio,
  0                                  AS Discount,
  t2.rtdd_duft                       AS ctn_size,
  0                                  AS promo_ref,
  t2.rtdd_excs                       AS gst,
  t2.rtdd_ovat                       AS vat,
  t2.rtdd_tdis                       AS total_discount,
  t2.rtdd_tvat                       AS total_vat,
  t2.rtdd_texc                       AS total_gst,
  t2.rtdd_amnt                       AS net_amount
FROM tt_rtan AS t1
  INNER JOIN tt_rtdd AS t2 ON t1.id = t2.rtan_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
WHERE t1.rtan_rtnm = '$order_id' AND t2.rtdd_dqty != 0");
            return view('PrintData.return_invoice_print')->with('salesOrder', $orderMasterData)->with('salesOrderLine', $orderLineData);
        }
        else{
            dd("Print Not Available");
        }

    }

    public function collectionPrint($cont_id, $collection_id)
    {
        $country = (new Country())->country($cont_id);
        if (strpos( '3,4',"$country->id") !== false) {
            $collectionData = collect(\DB::connection($country->cont_conn)->select("SELECT
  t1.id        AS payment_id,
  t1.cltn_code AS collection_code,
  t1.cltn_date AS date,
  t1.oult_id   AS outlet_id,
  t2.oult_name AS outlet_name,
  t1.cltn_amnt AS payment_amount,
  t3.acmp_name AS ou_name,
  t4.clpt_name AS payment_type,
  t1.cltn_chqn AS cheque_no,
  t1.cltn_cdat AS cheque_date,
  t5.bank_name AS bank_name,
  t1.cltn_amnt AS amount
FROM tt_cltn AS t1
  INNER JOIN tm_oult AS t2 ON t1.oult_id = t2.id
  INNER JOIN tm_acmp AS t3 ON t1.acmp_id = t3.id
  INNER JOIN tm_clpt AS t4 ON t1.clpt_id = t4.id
  LEFT JOIN tm_bank AS t5 ON t1.bank_id = t5.id
WHERE t1.cltn_code = '$collection_id'"))->first();


            $collectionMatchingData = DB::connection($country->cont_conn)->select("SELECT
  t3.id                                                AS site_id,
  t4.site_code                                         AS site_code,
  t4.site_name                                         AS site_name,
  t2.invc_date                                         AS date,
  t1.invc_code                                         AS invoice_code,
  t3.invt_name                                         AS invoice_type,
  if(t2.trnt_id = 1, t2.invc_amnt, 0)                  AS invoice_amount,
  if(t2.trnt_id = 1, t2.invc_mamt, 0)                  AS collection_amount,
  if(t2.trnt_id = 1, (t2.invc_amnt - t2.invc_mamt), 0) AS balance,
  if(t2.trnt_id = 2, t1.clim_amnt, 0)                  AS deduct_amount,
  if(t2.trnt_id = 2, -1 * t1.clim_amnt, t1.clim_amnt)  AS net_amount,
  if(t2.trnt_id = 1, t1.clim_amnt, 0)                  AS invice_payment_amount,
  t2.invc_taxc                                         AS tax_invoice
FROM tt_clim AS t1
  INNER JOIN tt_invc AS t2 ON t1.invc_code = t2.invc_code
  INNER JOIN tm_invt AS t3 ON t2.invt_id = t3.id
  INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
  INNER JOIN tt_cltn AS t5 ON t1.cltn_id = t5.id
WHERE t5.cltn_code = '$collection_id'");

            $collectionTypeData = DB::connection($country->cont_conn)->select("SELECT
  t3.invt_name                                                  AS invoice_type,
  if(t2.trnt_id = 2, -1 * sum(t1.clim_amnt), sum(t1.clim_amnt)) AS amount
FROM tt_clim AS t1
  INNER JOIN tt_invc AS t2 ON t1.invc_code = t2.invc_code
  INNER JOIN tm_invt AS t3 ON t2.invt_id = t3.id
  INNER JOIN tt_cltn AS t4 ON t1.cltn_id = t4.id
WHERE t4.cltn_code = '$collection_id'
GROUP BY t2.invt_id, t2.trnt_id, invt_name");

            //  dd($collectionMatchingData);
            return view('PrintData.collection_print')->with('collectionData', $collectionData)->with('collectionMatchingData', $collectionMatchingData)->with('collectionTypeData', $collectionTypeData);
        }
        else{
            dd("Print Not Available");
        }
    }

    public function balancePrint($cont_id, $order_id)
    {

    }

    public function statementPrint($cont_id, $order_id)
    {

    }

    public function dmSheetPrint($cont_id, $order_id)
    {

    }

    public function vanLoadSheetPrint($cont_id, $order_id)
    {

    }
}
