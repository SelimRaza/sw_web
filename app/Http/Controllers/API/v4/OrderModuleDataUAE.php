<?php

/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v4;

use App\BusinessObject\Attendance;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\TempSite;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\ReturnLine;
use Carbon\Carbon;


class OrderModuleDataUAE extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
    
    /*
    *   API MODEL V3 [START]
    */

    public function govDistrict(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
                        id        AS district_code,
                        dsct_name AS district_name
                        FROM tm_dsct
                        WHERE `lfcl_id` = 1
                        ORDER BY dsct_name
                      ");
        }
        return $data1;
    }

    public function SubmitTripEOT(Request $request){

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $mgs = "";
            $cod = 0;
            try {
                $ret = DB::connection($db_conn)->table('dm_trip')->where(['TRIP_NO' => $request->TRIP_NO,
                    'STATUS' => 'N', 'DM_ID' => $request->DM_ID])->update(['STATUS' => 'R']);

                if ($ret == 1) {
                    $cod = 1;
                    $mgs = "EOT Successful ";
                } else {
                    $cod = 0;
                    $mgs = "EOT Already complete ";
                }
                DB::connection($db_conn)->commit();
                return array(
                    'success' => $cod,
                    'message' => $mgs,
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public function SubmitSiteWiseInvoiceCollection(Request $request){

        $request->site_code;
        $request->site_id;
        $request->country_id;
        $request->Coll_type;
        $request->Ck_Imgl;
        $request->Ck_No;
        $request->BANK_NAME;
        $request->CHECK_DATE;
        $request->COLL_AMNT;
        $request->emp_id;
        $request->emp_code;
        $request->DM_CODE;
        $request->ACMP_CODE;
        $request->WH_ID;

        $outletData = json_decode($request->Collection_Data)[0];
        $outletData_child = json_decode($request->Collection_Data);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $sql1 = "";
                    $sql2 = "";
                    $sql3 = "";
                    $sql4 = "";
                    $sql5 = "";
                    $sql6 = "";
                    $sql7 = "";
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');

                    $now = now();
                    $last_3 = substr($now->timestamp . $now->milli, 10);
                    $COLL_NUMBER = "CL" . $dt . '-' . $last_3;
                    $now1 = now();
                    $last_31 = substr($now1->timestamp . $now1->milli, 10);
                    $COLL_NUMBER1 = "OC" . $dt . '-' . $last_31;

                    $sql1 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                 `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                 `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`,CHECK_IMAGE,BANK_NAME,CHECK_DATE)
                                  VALUES (NULL,  '$request->ACMP_CODE', '$DRDT', '$COLL_NUMBER', '$request->IBS_INVOICE', $request->emp_id,
                                  '$request->emp_code', '$request->DM_CODE', '$request->WH_ID',
                                  '$request->site_id', '$request->site_code',  $request->COLL_AMNT,0, '$request->Coll_type',
                                   26, '$request->Ck_No', 1,'$request->Ck_Imgl','$request->BANK_NAME','$request->CHECK_DATE'); ";
                    $sql2 = "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                               `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                                VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$COLL_NUMBER1','$request->site_id','$request->site_code',
                                $request->COLL_AMNT,0,0); ";

                    foreach ($outletData_child as $orderLineData) {

                        if ($orderLineData->Type == 1) {
                            $sql3 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                0,$orderLineData->Coll_Amt,0 ); ";

                            $sql4 .= " Update dm_trip_master set COLLECTION_AMNT=(COLLECTION_AMNT+$orderLineData->Coll_Amt) 
                            WHERE ORDM_ORNM='$orderLineData->ORDM_ORNM' AND DELIVERY_STATUS=11 ;";
                        } else {
                            $sql5 .= "INSERT INTO `dm_invoice_collection_mapp`(`ID`, `ACMP_CODE`, `TRN_DATE`, `MAP_ID`,
                        `TRANSACTION_ID`, `SITE_ID`, `SITE_CODE`, `DEBIT_AMNT`, `CRECIT_AMNT`, `DELV_AMNT`)
                         VALUES (NULL,  '$request->ACMP_CODE','$DRDT','$COLL_NUMBER','$orderLineData->ORDM_ORNM','$orderLineData->SITE_ID','$orderLineData->SITE_CODE',
                                $orderLineData->Coll_Amt,0,0 ); ";
                            $sql6 .= " Update dm_collection set COLL_REC_HO=$orderLineData->Coll_Amt,STATUS=26
                            WHERE COLL_NUMBER='$orderLineData->ORDM_ORNM' AND INVT_ID=5; ";
                        }
                    }
                    DB::connection($db_conn)->unprepared($sql);

                    
                    DB::connection($db_conn)->insert($sql1);
                    DB::connection($db_conn)->insert($sql2);
                    DB::connection($db_conn)->unprepared($sql3);//multiple row
                    DB::connection($db_conn)->unprepared($sql4);//multiple row
                    
                    if (!empty($sql5)) {
                        DB::connection($db_conn)->unprepared($sql5);
                        DB::connection($db_conn)->unprepared($sql6);
                    }
                    DB::connection($db_conn)->commit();
                    
                    return array(
                        'success' => 1,
                        'message' => "Collection Successfully",
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
    }

    public function SubmitSiteWiseInvoiceGrvDelivery(Request $request){ 

        $request->tpm_id;
        $request->tpm_code;
        $request->site_code;
        $request->site_id;
        $request->country_id;
        $request->type_id;
        $request->ORDM_ORNM;
        $request->DELV_AMNT;
        $request->emp_id;
        $request->emp_code;

        $outletData = json_decode($request->invoice_wise_delivery)[0];
        $outletData_child = json_decode($request->invoice_wise_delivery);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $sql1 = "";
                    $sql2 = "";
                    $sql3 = "";
                    $sql4 = "";
                    $sql5 = "";
                    $sql6 = "";
                    $sql7 = "";
                    $sql = "SET autocommit=0;";
                    $DRDT = date('Y-m-d');
                    $Year = date('Y');
                    $DRTM = date('Y-m-d H:i:s');
                    $dt = date('Ymd-Hi');
                    if ($request->type_id == 1) {

                        $sql1 = " Update tt_ordm set lfcl_id=11,ordm_drdt='$DRDT',ordm_dltm='$DRTM' WHERE ordm_ornm='$request->ORDM_ORNM'; ";

                        foreach ($outletData_child as $orderLineData) {
                            $amount = $orderLineData->Dev_QNTY * $orderLineData->Rate;

                            $sql2 .= " Update tt_ordd set ordd_dqty=$orderLineData->Dev_QNTY,ordd_odat=$amount  WHERE id=$orderLineData->trp_line; ";
                            $sql3 .= " Update dm_trip_detail set DELV_QNTY=$orderLineData->Dev_QNTY,DISCOUNT=$orderLineData->DISCOUNT,TRIP_STATUS='D',ORDM_DRDT='$DRDT' WHERE OID=$orderLineData->trp_line;";
                        }
                        $sql4 = " Update dm_trip_master set ORDM_DRDT='$DRDT',DELV_AMNT=$request->DELV_AMNT,DELIVERY_STATUS=11 WHERE ORDM_ORNM='$request->ORDM_ORNM';";
                    } else {
                        $now = now();
                        //  "SELECT `ACMP_CODE`,`WH_ID`,`DM_CODE` FROM `dm_trip_master` WHERE TRIP_NO='TRIP-0500-01-2111000005' GROUP BY TRIP_NO";
                        $dm_ACMP_WH_ID_DM_CODE = DB::connection($db_conn)->table('dm_trip_master')->where(['TRIP_NO' => $request->tpm_code])->first();

                        /* echo $dm_ACMP_WH_ID_DM_CODE;
                         die();*/

                        $last_3 = substr($now->timestamp . $now->milli, 10);
                        $COLL_NUMBER = "RC" . $dt . '-' . $last_3;


                        $sql5 = " Update tt_rtan set lfcl_id=11,dm_trip='$request->tpm_code',rtan_drdt='$DRDT',rtan_dltm='$DRTM' WHERE id=$request->tpm_id; ";
                        $sql6 = "INSERT INTO `dm_collection` (`ID`, `ACMP_CODE`, `COLL_DATE`, `COLL_NUMBER`, 
                                `IBS_INVOICE`, `AEMP_ID`, `AEMP_USNM`, `DM_CODE`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `COLLECTION_AMNT`,
                                `COLL_REC_HO`, `COLLECTION_TYPE`, `STATUS`, `CHECK_NUMBER`, `INVT_ID`)
                                 VALUES (NULL,  '$dm_ACMP_WH_ID_DM_CODE->ACMP_CODE', '$DRDT', '$COLL_NUMBER', 'N', $request->emp_id,
                                  '$request->emp_code', '$dm_ACMP_WH_ID_DM_CODE->DM_CODE', '$dm_ACMP_WH_ID_DM_CODE->WH_ID',
                                  '$request->site_id', '$request->site_code',  $request->DELV_AMNT,0, 'GRV', 11, 'N', 5); ";

                        foreach ($outletData_child as $orderLineData) {
                            $amountd = $orderLineData->Dev_QNTY * $orderLineData->Rate;
                            $sql7 .= " Update tt_rtdd set rtdd_dqty='$orderLineData->Dev_QNTY',rtdd_damt='$amountd' WHERE id=$orderLineData->trp_line; ";
                        }
                    }

                    /* echo $sql1;
                     echo $sql2;
                     echo $sql3;
                     echo $sql4;
                     die();*/
                    DB::connection($db_conn)->unprepared($sql);

                    if ($request->type_id == 1) {
                        DB::connection($db_conn)->insert($sql1);
                        DB::connection($db_conn)->unprepared($sql2);//multiple row
                        DB::connection($db_conn)->unprepared($sql3);//multiple row
                        DB::connection($db_conn)->insert($sql4);
                    } else {
                        DB::connection($db_conn)->insert($sql5);
                        DB::connection($db_conn)->insert($sql6);
                        DB::connection($db_conn)->unprepared($sql7);//multiple row
                    }
                    DB::connection($db_conn)->commit();
                    // echo $retn;
                    // die();
                    return array(
                        'success' => 1,
                        'message' => "Delivery Successfully",
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }
            }
        }
    }

    public function SubmitInvoiceSMS(Request $request){
      
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            $sql = "SET autocommit=0;";
            $sql1 = "";
            try {

                $sql1 = "INSERT INTO `tm_smst` (`id`, `aemp_id`, `site_id`, `site_mob1`, `smst_body`, `cont_id`, `lfcl_id`)
                         VALUES (NULL, '$request->emp_id', '$request->site_id', '$request->site_mobile', '$request->sms_body', $request->country_id, 1)";

                DB::connection($db_conn)->unprepared($sql);
                DB::connection($db_conn)->insert($sql1);
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Successfully Save SMS ",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public function GetVanSalesTripSite(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT t1.`TRIP_NO`,t2.site_id
                            FROM `dm_trip`t1 JOIN tm_aemp t2 ON t1.`DM_ID`=t2.aemp_usnm
                            WHERE t2.`id`='$request->emp_id' 
                            AND t1.`STATUS`='N' 
                            AND t1.`SALES_TYPE`='VS'
                        ");
        }

        return $data1;

    }

    public function CheckINSyncAllDataMergeVanSales(Request $request){

        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();
        $data9 = array();
        $data10 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.amim_code, $request->site_id)              AS column_id,
                                t5.plmt_id                                           AS Item_Price_List,
                                t5.plmt_id                                           AS Grv_Item_Price_List,
                                t1.id                                                AS Item_Id,
                                t1.amim_code                                         AS Item_Code,
                                t1.amim_imgl                                         AS amim_imgl,
                                t4.issc_name                                         AS Category_Name,
                                t1.amim_name                                         AS Item_Name,
                                t6.pldt_tppr                                         AS Item_Rate,
                                t6.pldt_tpgp                                         AS Grv_Item_Price,
                                (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                                t1.amim_pvat                                         AS Item_vat,
                                t6.amim_duft                                         AS Item_Factor,
                                t4.issc_seqn                                         AS category_Showing_seqn,
                                t6.amim_dunt                                         AS D_Unit,
                                t6.amim_runt                                         AS R_Unit,
                                t9.INV_QNTY-t9.DELV_QNTY                             AS Stock_Qty
                                FROM `tm_amim`t1 
                                INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                                INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
                                INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                                INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
                                INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                                INNER JOIN tm_aemp AS t7 ON t3.aemp_id=t7.id
                                INNER JOIN dm_trip AS t8 ON t7.aemp_usnm=t8.DM_ID
                                INNER JOIN dm_trip_detail t9 ON t8.TRIP_NO=t9.TRIP_NO and t9.AMIM_ID=t6.amim_id
                                WHERE  t1.lfcl_id=1 AND
                                t3.aemp_id = $request->emp_id 
                                AND t5.site_id = $request->site_id 
                                AND t5.acmp_id = $request->ou_id AND 
                                t8.TRIP_NO='$request->TRIP_NO' AND
                                t8.STATUS='N' AND (t9.INV_QNTY-t9.DELV_QNTY)>0
                            ");

            $data3 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t3.amim_code, $request->site_id)               AS column_id,
                                t3.id                                                 AS Item_Id,
                                t3.amim_code                                          AS Item_Code,
                                if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis
                                FROM tl_sgsm AS t1
                                INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
                                INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                                INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
                                LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
                                LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
                                WHERE t3.lfcl_id = 1
                                AND t1.aemp_id = $request->emp_id
                                AND t6.site_id = $request->site_id 
                                AND t6.acmp_id = $request->ou_id
                                GROUP BY t3.id, t3.amim_code, t8.dfim_disc
                            ");


            $data4 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                                t1.id                                                AS promo_id,
                                t1.prms_name                                         AS promo_name,
                                t1.prms_sdat                                         AS start_date,
                                t1.prms_edat                                         AS end_date,
                                t1.prmr_qfct                                         AS qualifier_category,
                                t1.prmr_ditp                                         AS discount_type,
                                t1.prmr_ctgp                                         AS category_group,
                                t1.prmr_qfon                                         AS qualifier_on,
                                t1.prmr_qfln                                         AS qualifier_line
                                FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                                WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                                GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                                ");
                                        $data5 = DB::connection($db_conn)->select("
                                SELECT 
                                concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
                                t1.`mspm_id`AS MSP_ID,
                                t1.amim_id AS MSP_Item_ID,
                                t1.`mspd_qnty`AS MSP_Item_Qty,
                                '2' AS Status_ID
                                FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
                                LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
                                LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
                                WHERE t3.site_id='$request->site_id' 
                                AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
                                GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`,t1.amim_id
                            ");
            $data6 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.nopr_name) AS column_id,
                                concat(t1.id, t1.nopr_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.nopr_name                AS Reason_Name
                                FROM tm_nopr AS t1");

                                        $data7 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.dprt_name) AS column_id,
                                concat(t1.id, t1.dprt_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.dprt_name                AS Reason_Name
                                FROM tm_dprt AS t1
                            ");
            $data8 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                                t1.prmr_id                                                            AS promo_id,
                                t1.amim_id                                                            AS product_id,
                                t1.prmd_modr                                                          AS pro_modifier_id
                                FROM tm_prmd AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");

            $data9 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
                                t1.prmr_id                                                                            AS promo_id,
                                t1.prsb_text                                                                          AS promo_slab_text,
                                t1.prsb_fqty                                                                          AS from_qty,
                                t1.prsb_tqty                                                                          AS to_qty,
                                0                                                                                     AS unit,
                                0                                                                                     AS unit_factor,
                                t1.prsb_famn                                                                          AS from_amnt,
                                t1.prsb_tamn                                                                          AS to_amnt,
                                t1.prsb_qnty                                                                          AS qty,
                                0                                                                                     AS given_unit,
                                0                                                                                     AS given_unit_factor,
                                t1.prsb_disc                                                                          AS discount,
                                t1.prsb_modr                                                                          AS pro_modifier_id,
                                t1.prsb_mosl                                                                          AS pro_modifier_id_sl
                                FROM tm_prsb AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id = $request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
                                t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
                            ");

            $data10 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                                t1.prmr_id                                                            AS promo_id,
                                t1.amim_id                                                            AS product_id,
                                t1.prmd_modr                                                          AS pro_modifier_id
                                FROM tm_prmf AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");

        }
        return Array(
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "promotion" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data8, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data9, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data10, "action" => $request->country_id),
        );
    }

    public function GetSRTodayOutletList(Request $request){
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $Ou_id = $request->ou_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t3.site_id                                                 AS Outlet_ID,
                                t4.site_code                                               AS Outlet_Code,
                                t4.site_name                                               AS Outlet_Name,
                                t4.site_ownm                                               AS Owner_Name,
                                t2.slgp_id                                                 AS Group_ID,
                                t3.rspm_serl                                               AS outlet_serial,
                                t4.site_mob1                                               AS Mobile_No,
                                t4.site_adrs                                               AS Outlet_Address,
                                t4.geo_lat                                                 AS geo_lat,
                                t4.geo_lon                                                 AS geo_lon,
                                t5.optp_id                                                 AS payment_type,
                                t5.stcm_limt                                               AS Site_Limit,
                                t5.stcm_odue                                               AS Site_overdue,
                                t5.stcm_days                                               AS Site_Order_Expire_Day,
                                round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
                                round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
                                IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
                                t4.site_vtrn                                               AS VAT_TRN,
                                t7.chnl_name                                               AS Channel,
                                t6.scnl_name                                               AS Sub_Channel
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
                                INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
                                INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.plmt_id=t2.plmt_id
                                LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
                                LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
                                LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
                                WHERE t1.aemp_id = '$emp_id' AND t1.rout_id = '$route_id' AND t5.acmp_id = '$Ou_id'
                                GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
                                t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
                                t5.stcm_ordm,
                                t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
                                ORDER BY `t3`.`rspm_serl` ASC
                            "); 
        }

        return $data1;

    }

    public function GetItemOrderHistory(Request $request){
        $data1 = array();

        $site_id = $request->site_id;
        $item_id = $request->item_id;
        $ou_id = $request->ou_id;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                            SELECT t1.`id` AS ORDER_Id ,
                            t1.`ordm_ornm` AS ORDER_Code,
                            (t2.ordd_dqty-t2.ordd_rqty)AS Product_order_qty,
                            ((t2.ordd_dpds+t2.ordd_spdd+t2.ordd_dfdd)/t2.ordd_dqty)AS discount_ratio,
                            t2.ordd_uprc AS Rate,
                            t2.ordd_excs AS Gst_percent,
                            t2.ordd_ovat AS vat_percent
                            FROM `tt_ordm` t1 JOIN tt_ordd t2 ON(t1.`id`=t2.ordm_id)
                            WHERE t1.`site_id`='$site_id'AND t1.`lfcl_id`='11'
                            AND t1.acmp_id='$ou_id'AND t2.amim_id='$item_id' 
                            AND (t2.ordd_dqty-t2.ordd_rqty)>0
                            ORDER BY t1.`id` DESC
                        ");
        }
        return Array(
            "GRV_Refer_Data" => array("data" => $data1, "action" => $request->country_id),
        );
    }


    public function outletSave(Request $request){
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;

            if ($db_conn != '') {
                if (!$db_conn == 2) {
                    $country = Country::on($db_conn)->findorfail($outletData->country_id);
                    //$site_code ='';
                   // if ($outletData->country_id == 7) {
                      //  $site_code = 'CO' . str_pad($country->attr4 + 1, 8, '0', STR_PAD_LEFT);
                   // } else {
                        $site_code = 'N' . str_pad($country->attr4 + 1, 9, '0', STR_PAD_LEFT);
                  //  }

                    DB::connection($db_conn)->beginTransaction();
                    try {
                        $outlet = new Outlet();
                        $outlet->setConnection($db_conn);
                        $outlet->oult_name = $outletData->Outlet_Name;
                        $outlet->oult_code = $site_code;
                        $outlet->oult_olnm = $outletData->Outlet_Name_BN;
                        $outlet->oult_adrs = $outletData->Address != "" ? $outletData->Address : '';
                        $outlet->oult_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                        $outlet->oult_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                        $outlet->oult_olon = '';
                        $outlet->oult_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                        $outlet->oult_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                        $outlet->oult_emal = '';
                        $outlet->cont_id = $outletData->country_id;
                        $outlet->lfcl_id = 1;
                        $outlet->aemp_iusr = $outletData->up_emp_id;
                        $outlet->aemp_eusr = $outletData->up_emp_id;
                        $outlet->var = 1;
                        $outlet->attr1 = '-';
                        $outlet->attr2 = '-';
                        $outlet->attr3 = 0;
                        $outlet->attr4 = 0;
                        $outlet->save();
                        $site = new Site();
                        $site->setConnection($db_conn);
                        $site->site_name = $outletData->Outlet_Name;
                        $site->site_code = $site_code;
                        $site->outl_id = $outlet->id;
                        $site->site_olnm = $outletData->Outlet_Name_BN;
                        $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                        $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                        $site->mktm_id = $outletData->Market_ID;
                        $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                        $site->site_olon = '';
                        $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                        $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                        $site->site_emal = '';
                        $site->scnl_id = 1;
                        $site->otcg_id = $outletData->Shop_Category_id;
                        $site->site_imge = $outletData->Outlet_Image;
                        $site->site_omge = '';
                        $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                        $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                        $site->site_reg = '';
                        $site->site_hsno = '';
                        $site->site_vtrn = '';
                        $site->site_vsts = 1;
                        $site->site_vrfy = 0;
                        $site->cont_id = $outletData->country_id;
                        $site->lfcl_id = 1;
                        $site->aemp_iusr = $outletData->up_emp_id;
                        $site->aemp_eusr = $outletData->up_emp_id;
                        $site->var = 1;
                        $site->attr1 = '';
                        $site->attr2 = '';
                        $site->attr3 = 0;
                        $site->attr4 = 0;
                        $site->save();

                        $tempSite = new TempSite();
                        $tempSite->setConnection($db_conn);
                        $tempSite->nsit_name = $outletData->Outlet_Name;
                        $tempSite->nsit_code = $site_code;
                        $tempSite->nsit_olnm = $outletData->Outlet_Name_BN;
                        $tempSite->nsit_adrs = $outletData->Address != "" ? $outletData->Address : '';
                        $tempSite->nsit_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                        $tempSite->mktm_id = $outletData->Market_ID;
                        $tempSite->nsit_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                        $tempSite->nsit_olon = '';
                        $tempSite->nsit_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                        $tempSite->nsit_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                        $tempSite->nsit_emal = '';
                        $tempSite->scnl_id = 1;
                        $tempSite->otcg_id = $outletData->Shop_Category_id;
                        $tempSite->nsit_imge = $outletData->Outlet_Image;
                        $tempSite->nsit_omge = '';
                        $tempSite->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                        $tempSite->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                        $tempSite->nsit_reg = '';
                        $tempSite->nsit_vrfy = 0;
                        $tempSite->site_id = $site->id;
                        $tempSite->cont_id = $outletData->country_id;
                        $tempSite->lfcl_id = 1;
                        $tempSite->aemp_iusr = $outletData->up_emp_id;
                        $tempSite->aemp_eusr = $outletData->up_emp_id;
                        $tempSite->var = 1;
                        $tempSite->attr1 = '-';
                        $tempSite->attr2 = '-';
                        $tempSite->attr3 = 0;
                        $tempSite->attr4 = 0;
                        $tempSite->save();

                        $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $outletData->Route_ID])->first();
                        if ($routeSite == null) {
                            $routeSite = new RouteSite();
                            $routeSite->setConnection($db_conn);
                            $routeSite->site_id = $site->id;
                            $routeSite->rout_id = $outletData->Route_ID;
                            $routeSite->rspm_serl = 0;
                            $routeSite->cont_id = $outletData->country_id;
                            $routeSite->lfcl_id = 1;
                            $routeSite->aemp_iusr = $outletData->up_emp_id;
                            $routeSite->aemp_eusr = $outletData->up_emp_id;
                            $routeSite->var = 1;
                            $routeSite->attr1 = '-';
                            $routeSite->attr2 = '-';
                            $routeSite->attr3 = 0;
                            $routeSite->attr4 = 0;
                            $routeSite->save();
                        }
                        $country->attr4 = $country->attr4 + 1;
                        $country->save();
                        DB::connection($db_conn)->commit();
                        return array(
                            'success' => 1,
                            'message' => "Outlet Created Successfully",
                            'Outlet_ID' => $site->id,
                            'Outlet_Code' => $site->site_code,
                            'Outlet_Name' => $site->site_name,
                            'Owner_Name' => $site->site_ownm,
                            'outlet_serial' => 0,
                            'Mobile_No' => $site->site_mob1,
                            'Outlet_Address' => $site->site_adrs,
                            'geo_lat' => $site->geo_lat,
                            'geo_lon' => $site->geo_lon,
                        );
                    } catch (\Exception $e) {
                        DB::connection($db_conn)->rollback();
                        return $e;                        
                    }
                }
            }
        }
    }

    public function updateOutletSerial(Request $request){
        $outletData = json_decode($request->Outlet_Serialize_Data)[0];
        $outletData_child = json_decode($request->Outlet_Serialize_Data);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    foreach ($outletData_child as $outletData_1) {
                        DB::connection($db_conn)->table('tl_rsmp')->where(['rout_id' => $outletData_1->Route_ID,
                            'site_id' => $outletData_1->Outlet_ID])->update(['rspm_serl' => $outletData_1->Outlet_Serial]);

                    }

                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Serialized Successfully",

                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;                    
                }

            }
        }
    }

    public function updateOutlet(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t1.id        AS Outlet_ID,
                                t1.site_code AS Outlet_Code,
                                t1.site_name AS Outlet_Name,
                                t1.site_olnm AS Outlet_Name_BN,
                                t1.site_ownm AS owner_name,
                                t1.site_mob1 AS Mobile_No,
                                t1.site_mob2 AS Mobile_No_2,
                                t1.site_adrs AS Address,
                                t1.site_olad AS Address_BN
                                FROM tm_site AS t1
                                INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
                                WHERE t2.rout_id=$request->route_id
                                GROUP BY t1.id,
                                t1.site_code,
                                t1.site_name,
                                t1.site_olnm,
                                t1.site_ownm,
                                t1.site_mob1,
                                t1.site_mob2,
                                t1.site_adrs,
                                t1.site_olad 
                            ");
        }
        return $data1;

    }


    public function updateOutletSave(Request $request){
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = Site::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name;
                    $site->site_olnm = $outletData->Outlet_Name_BN;
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    /* $site->site_imge = '';
                     $site->site_omge = '';*/
                    $site->site_imge = $outletData->Outlet_Image;
                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                    $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    $site->aemp_eusr = $outletData->up_emp_id;
                    $site->save();
                    DB::connection($db_conn)->commit();
                    return array(
                        'success' => 1,
                        'message' => "Outlet Updated Successfully",
                        'Outlet_ID' => $site->id,
                        'Outlet_Code' => $site->site_code,
                        'Outlet_Name' => $site->site_name,
                        'Owner_Name' => $site->site_ownm,
                        'outlet_serial' => 0,
                        'Mobile_No' => $site->site_mob1,
                        'Outlet_Address' => $site->site_adrs,
                        'geo_lat' => $site->geo_lat,
                        'geo_lon' => $site->geo_lon,
                    );
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                    //throw $e;
                }

            }
        }
    }


    public function CheckINSyncAllData_Image(Request $request){
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
 
            $data1 = DB::connection($db_conn)->select("
                                SELECT concat(t1.amim_code, $request->site_id) AS column_id,
                                t1.amim_code             AS Item_Code,
                                t5.amim_id               AS Item_ID,
                                t5.pldt_tppr             AS Item_Price,
                                t5.amim_duft             AS Item_Factor 

                                From `tm_amim` t1
                                INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                                INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
                                INNER JOIN tl_stcm t4 ON t3.plmt_id = t4.plmt_id
                                INNER JOIN tm_pldt AS t5 ON t4.plmt_id = t5.plmt_id  AND t5.amim_id = t2.amim_id
                                WHERE t1.lfcl_id = 1 AND t3.aemp_id = $request->emp_id AND t4.acmp_id=$request->ou_id AND t4.site_id = $request->site_id  
                                GROUP BY t1.amim_code,t5.amim_id,t5.pldt_tppr,t5.amim_duft
                            ");

            $data2 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.amim_code, $request->site_id)              AS column_id,
                                t5.plmt_id                                           AS Item_Price_List,
                                t5.plmt_id                                           AS Grv_Item_Price_List,
                                t1.id                                                AS Item_Id,
                                t1.amim_code                                         AS Item_Code,
                                t1.amim_imgl                                         AS amim_imgl,
                                t4.issc_name                                         AS Category_Name,
                                t1.amim_name                                         AS Item_Name,
                                t6.pldt_tppr                                         AS Item_Rate,
                                t6.pldt_tpgp                                         AS Grv_Item_Price,
                                (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                                t1.amim_pvat                                         AS Item_vat,
                                t6.amim_duft                                         AS Item_Factor,
                                t4.issc_seqn                                         AS category_Showing_seqn,
                                t6.amim_dunt                                         AS D_Unit,
                                t6.amim_runt                                         AS R_Unit

                                FROM `tm_amim`t1 
                                INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                                INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
                                INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                                INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
                                INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                                WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id  AND t5.site_id = $request->site_id AND t5.acmp_id = $request->ou_id 
                            ");


             $data3 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t3.amim_code, $request->site_id)               AS column_id,
                                t3.id                                                 AS Item_Id,
                                t3.amim_code                                          AS Item_Code,
                                if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis,
                                if(t9.prmr_id > 0, t9.prmr_id, 'null')                AS Promotion_ID
                                FROM tl_sgsm AS t1
                                INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
                                INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                                INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
                                LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
                                LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
                                LEFT JOIN (
                                            SELECT
                                            t3.amim_id,
                                            t1.prmr_id
                                            FROM tl_prsm t1
                                            INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                                            INNER JOIN tm_prmd t3 ON (t3.`prmr_id` = t2.id)
                                            WHERE t1.site_id = $request->site_id  AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat
                                        ) AS t9 ON t2.amim_id = t9.amim_id

                                WHERE t3.lfcl_id = 1
                                    AND t1.aemp_id = $request->emp_id
                                    AND t6.site_id = $request->site_id 
                                    AND t6.acmp_id = $request->ou_id
                                GROUP BY t3.id, t3.amim_code, t8.dfim_disc, t9.prmr_id
                            ");


            $data4 = DB::connection($db_conn)->select("
                                SELECT 
                                concat(t1.`id` , $request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`)    AS column_id,
                                t1.`id`                                    AS Promotion_ID,
                                t1.`prmr_ctgp`                             AS FOC_Type_Single_Multiple,
                                t1.`prmr_qfct`                             AS promo_type,
                                t1.`prmr_ditp`                             AS discount_type, 
                                t1.`prmr_qfon`                             AS FOC_CRT_PCS_Type 
                                FROM `tm_prmr` t1 JOIN tl_prsm t2 ON(t1.id=t2.prmr_id)
                                WHERE t2.site_id='$request->site_id' and t1.prms_edat>=CURDATE() AND t1.prms_sdat<=CURDATE()
                            ");
            $data5 = DB::connection($db_conn)->select("
                                SELECT 
                                concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
                                t1.`mspm_id`AS MSP_ID,
                                t4.amim_code AS MSP_Item_ID,
                                t1.`mspd_qnty`AS MSP_Item_Qty,
                                '2' AS Status_ID
                                FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
                                LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
                                LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
                                WHERE t3.site_id='$request->site_id' 
                                AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
                                GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`
                            ");
            $data6 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.nopr_name) AS column_id,
                                concat(t1.id, t1.nopr_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.nopr_name                AS Reason_Name
                                FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.dprt_name) AS column_id,
                                concat(t1.id, t1.dprt_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.dprt_name                AS Reason_Name
                                FROM tm_dprt AS t1
                            ");

        }
        return Array(
            "Sync_t_prices_detail" => array("data" => $data1, "action" => $request->country_id),
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "Sync_T_Promotion_Table" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public function CheckINSyncAllData_Merge(Request $request){

        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $data7 = array();
        $data8 = array();
        $data9 = array();
        $data10 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.amim_code, $request->site_id)              AS column_id,
                                t5.plmt_id                                           AS Item_Price_List,
                                t5.plmt_id                                           AS Grv_Item_Price_List,
                                t1.id                                                AS Item_Id,
                                t1.amim_code                                         AS Item_Code,
                                t1.amim_imgl                                         AS amim_imgl,
                                t4.issc_name                                         AS Category_Name,
                                t1.amim_name                                         AS Item_Name,
                                t6.pldt_tppr                                         AS Item_Rate,
                                t6.pldt_tpgp                                         AS Grv_Item_Price,
                                (t1.amim_pexc * 100) / (t6.pldt_tppr * t6.amim_duft) AS Item_gst,
                                t1.amim_pvat                                         AS Item_vat,
                                t6.amim_duft                                         AS Item_Factor,
                                t4.issc_seqn                                         AS category_Showing_seqn,
                                t6.amim_dunt                                         AS D_Unit,
                                t6.amim_runt                                         AS R_Unit

                                FROM `tm_amim`t1 
                                INNER JOIN tl_sgit t2 ON t1.id=t2.amim_id
                                INNER JOIN tl_sgsm t3 ON t2.slgp_id = t3.slgp_id
                                INNER JOIN tm_issc t4 ON t2.issc_id = t4.id
                                INNER JOIN tl_stcm t5 ON t3.plmt_id = t5.plmt_id
                                INNER JOIN tm_pldt AS t6 ON t5.plmt_id = t6.plmt_id  AND t6.amim_id = t2.amim_id
                                WHERE  t1.lfcl_id=1 AND t3.aemp_id = $request->emp_id  AND t5.site_id = $request->site_id AND t5.acmp_id = $request->ou_id 
                            ");

            $data3 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t3.amim_code, $request->site_id)               AS column_id,
                                t3.id                                                 AS Item_Id,
                                t3.amim_code                                          AS Item_Code,
                                if(t8.dfim_disc > 0, t8.dfim_disc, 0)                 AS Item_Default_Dis
                                FROM tl_sgsm AS t1
                                INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
                                INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                                INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
                                INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                                INNER JOIN tl_stcm t6 ON (t2.plmt_id = t6.plmt_id)
                                LEFT JOIN tl_dfsm AS t7 ON t6.site_id = t7.site_id
                                LEFT JOIN tm_dfim AS t8 ON t7.dfdm_id = t8.dfdm_id AND t2.amim_id = t8.amim_id
                                WHERE t3.lfcl_id = 1
                                    AND t1.aemp_id = $request->emp_id
                                    AND t6.site_id = $request->site_id 
                                    AND t6.acmp_id = $request->ou_id
                                GROUP BY t3.id, t3.amim_code, t8.dfim_disc
                            ");


            $data4 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,$request->site_id,t1.`prmr_ctgp`,t1.`prmr_qfct`,t1.`prmr_ditp`) AS column_id,
                                t1.id                                                AS promo_id,
                                t1.prms_name                                         AS promo_name,
                                t1.prms_sdat                                         AS start_date,
                                t1.prms_edat                                         AS end_date,
                                t1.prmr_qfct                                         AS qualifier_category,
                                t1.prmr_ditp                                         AS discount_type,
                                t1.prmr_ctgp                                         AS category_group,
                                t1.prmr_qfon                                         AS qualifier_on,
                                t1.prmr_qfln                                         AS qualifier_line
                                FROM tm_prmr AS t1 INNER JOIN tl_prsm AS t2 ON t1.id = t2.prmr_id
                                WHERE t2.site_id = $request->site_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
                                GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,t1.prmr_qfln
                            ");

            $data5 = DB::connection($db_conn)->select("
                                SELECT 
                                concat(t1.`mspm_id` , $request->site_id,t2.mspm_sdat,t2.mspm_edat,t4.amim_code,t1.`mspd_qnty`)AS column_id,
                                t1.`mspm_id`AS MSP_ID,
                                t1.amim_id AS MSP_Item_ID,
                                t1.`mspd_qnty`AS MSP_Item_Qty,
                                '2' AS Status_ID
                                FROM `tm_mspd` t1 JOIN tm_mspm t2 ON(t1.`mspm_id`=t2.id)
                                LEFT JOIN tl_msps t3 ON(t3.mspm_id=t2.id)
                                LEFT JOIN tm_amim t4 ON(t1.`amim_id`=t4.id)
                                WHERE t3.site_id='$request->site_id' 
                                AND (curdate() BETWEEN t2.mspm_sdat AND t2.mspm_edat)
                                GROUP BY t1.`mspm_id`,t4.amim_code,t1.`mspd_qnty`,t1.amim_id
                            ");

            $data6 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.nopr_name) AS column_id,
                                concat(t1.id, t1.nopr_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.nopr_name                AS Reason_Name
                                FROM tm_nopr AS t1");

            $data7 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.id, t1.dprt_name) AS column_id,
                                concat(t1.id, t1.dprt_name) AS token,
                                t1.id                       AS Reason_id,
                                t1.dprt_name                AS Reason_Name
                                FROM tm_dprt AS t1
                                ");
            $data8 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                                t1.prmr_id                                                            AS promo_id,
                                t1.amim_id                                                            AS product_id,
                                t1.prmd_modr                                                          AS pro_modifier_id
                                FROM tm_prmd AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");

            $data9 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,$request->site_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
                                t1.prmr_id                                                                            AS promo_id,
                                t1.prsb_text                                                                          AS promo_slab_text,
                                t1.prsb_fqty                                                                          AS from_qty,
                                t1.prsb_tqty                                                                          AS to_qty,
                                0                                                                                     AS unit,
                                0                                                                                     AS unit_factor,
                                t1.prsb_famn                                                                          AS from_amnt,
                                t1.prsb_tamn                                                                          AS to_amnt,
                                t1.prsb_qnty                                                                          AS qty,
                                0                                                                                     AS given_unit,
                                0                                                                                     AS given_unit_factor,
                                t1.prsb_disc                                                                          AS discount,
                                t1.prsb_modr                                                                          AS pro_modifier_id,
                                t1.prsb_mosl                                                                          AS pro_modifier_id_sl
                                FROM tm_prsb AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id = $request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
                                t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl
                            ");

            $data10 = DB::connection($db_conn)->select("
                                SELECT
                                concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,$request->site_id) AS column_id,
                                t1.prmr_id                                                            AS promo_id,
                                t1.amim_id                                                            AS product_id,
                                t1.prmd_modr                                                          AS pro_modifier_id
                                FROM tm_prmf AS t1
                                INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
                                INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
                                WHERE t3.site_id =$request->site_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1
                                GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmd_modr
                            ");

        }
        return Array(
            "Sync_Product_Info_Table" => array("data" => $data2, "action" => $request->country_id),
            "Sync_Item_Promo_Df_Discount_Locally" => array("data" => $data3, "action" => $request->country_id),
            "promotion" => array("data" => $data4, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data5, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data6, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data8, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data9, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data10, "action" => $request->country_id),
        );
    }

    public function GetSRTodayOutletListSearchQRCode(Request $request){
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $search_qrcode = $request->outlet_code;
        $ou_id = $request->ou_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t3.site_id                                                 AS Outlet_ID,
                                t4.site_code                                               AS Outlet_Code,
                                t4.site_name                                               AS Outlet_Name,
                                t4.site_ownm                                               AS Owner_Name,
                                t2.slgp_id                                                 AS Group_ID,
                                t3.rspm_serl                                               AS outlet_serial,
                                t4.site_mob1                                               AS Mobile_No,
                                t4.site_adrs                                               AS Outlet_Address,
                                t4.geo_lat                                                 AS geo_lat,
                                t4.geo_lon                                                 AS geo_lon,
                                t5.optp_id                                                 AS payment_type,
                                t5.stcm_limt                                               AS Site_Limit,
                                t5.stcm_odue                                               AS Site_overdue,
                                t5.stcm_days                                               AS Site_Order_Expire_Day,
                                round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
                                round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
                                IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
                                t4.site_vtrn                                               AS VAT_TRN,
                                t7.chnl_name                                               AS Channel,
                                t6.scnl_name                                               AS Sub_Channel
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
                                INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
                                INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id)
                                LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
                                LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
                                LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
                                WHERE t1.aemp_id = '$emp_id' AND t1.rout_id = '$route_id' AND t5.acmp_id = '$ou_id' AND  t4.site_code ='$search_qrcode'
                                GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
                                t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
                                t5.stcm_ordm,
                                t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
                                ORDER BY `t3`.`site_id` ASC
                            ");
        }
        return $data1;

    }

    public function censusOutletImport(Request $request){
        
        $country = (new Country())->country($request->country_id);
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Add on Route",
        );
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site = Site::on($db_conn)->where(['site_code' => $request->site_code])->first();
            if ($site != null) {
                $routeSite = RouteSite::on($db_conn)->where(['site_id' => $site->id, 'rout_id' => $request->route_id])->first();
                if ($routeSite == null) {
                    $routeSite = new RouteSite();
                    $routeSite->setConnection($db_conn);
                    $routeSite->site_id = $site->id;
                    $routeSite->rout_id = $request->route_id;
                    $routeSite->rspm_serl = 0;
                    $routeSite->cont_id = $request->country_id;
                    $routeSite->lfcl_id = 1;
                    $routeSite->aemp_iusr = $request->up_emp_id;
                    $routeSite->aemp_eusr = $request->up_emp_id;
                    $routeSite->var = 1;
                    $routeSite->attr1 = '';
                    $routeSite->attr2 = '';
                    $routeSite->attr3 = 0;
                    $routeSite->attr4 = 0;
                    $routeSite->save();

                    $result_data = array(
                        'success' => 1,
                        'message' => "Adding Successful",
                        'Outlet_ID' => $site->id,
                        'Outlet_Code' => $site->site_code,
                        'Outlet_Name' => $site->site_name,
                        'Owner_Name' => $site->site_ownm,
                        'outlet_serial' => 0,
                        'Mobile_No' => $site->site_mob1,
                        'Outlet_Address' => $site->site_adrs,
                        'geo_lat' => $site->geo_lat,
                        'geo_lon' => $site->geo_lon,
                    );
                } else {
                    $result_data = array(
                        'success' => 0,
                        'message' => "Already Exits",
                    );
                }
            } else {
                $result_data = array(
                    'success' => 0,
                    'message' => "Site Not Exits In System",
                );
            }
        }
        return $result_data;
    }
    

    public function GetSRTodayOutletList(Request $request){
        $data1 = array();
        $emp_id = $request->emp_id;
        $route_id = $request->route_id;
        $Ou_id = $request->ou_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t3.site_id                                                 AS Outlet_ID,
                                t4.site_code                                               AS Outlet_Code,
                                t4.site_name                                               AS Outlet_Name,
                                t4.site_ownm                                               AS Owner_Name,
                                t2.slgp_id                                                 AS Group_ID,
                                t3.rspm_serl                                               AS outlet_serial,
                                t4.site_mob1                                               AS Mobile_No,
                                t4.site_adrs                                               AS Outlet_Address,
                                t4.geo_lat                                                 AS geo_lat,
                                t4.geo_lon                                                 AS geo_lon,
                                t5.optp_id                                                 AS payment_type,
                                t5.stcm_limt                                               AS Site_Limit,
                                t5.stcm_odue                                               AS Site_overdue,
                                t5.stcm_days                                               AS Site_Order_Expire_Day,
                                round(t5.stcm_limt - (t5.stcm_duea + t5.stcm_ordm), 2)     AS Site_Balance,
                                round((t5.stcm_duea + t5.stcm_ordm), 2)                    AS avail,
                                IF(t8.dfdm_id IS NULL OR t8.dfdm_id = '', '0', t8.dfdm_id) AS Site_Discount_ID,
                                t4.site_vtrn                                               AS VAT_TRN,
                                t7.chnl_name                                               AS Channel,
                                t6.scnl_name                                               AS Sub_Channel
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
                                INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
                                INNER JOIN tl_stcm t5 ON (t3.site_id = t5.site_id) AND t5.plmt_id=t2.plmt_id
                                LEFT JOIN tm_scnl t6 ON (t4.scnl_id = t6.id)
                                LEFT JOIN tm_chnl t7 ON (t6.chnl_id = t7.id)
                                LEFT JOIN tl_dfsm t8 ON (t4.id = t8.site_id)
                                WHERE t1.aemp_id = '$emp_id' AND t1.rout_id = '$route_id' AND t5.acmp_id = '$Ou_id'
                                GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
                                t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon, t5.optp_id, t5.stcm_limt, t5.stcm_duea, t5.stcm_days,
                                t5.stcm_ordm,
                                t4.site_vtrn, t7.chnl_name, t6.scnl_name, t5.stcm_odue, t8.dfdm_id
                                ORDER BY `t3`.`rspm_serl` ASC
                            ");            

        }

        return $data1;

    }

    public function GetOutletSerialData(Request $request){
        $data1 = array();

        $route_id = $request->route_id;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT
                            t3.site_id                                   AS Outlet_ID,
                            t4.site_code                                 AS Outlet_Code,
                            t4.site_name                                 AS Outlet_Name,
                            t3.rspm_serl                                 AS outlet_serial
                            FROM tl_rpln AS t1
                            INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                            INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
                            INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
                            INNER JOIN tl_stcm t5 ON(t3.site_id=t5.site_id)
                            WHERE t1.aemp_id = '$emp_id'AND t1.rout_id='$route_id'
                            GROUP BY t3.site_id, t4.site_code, t4.site_name, t3.rspm_serl
                            ORDER BY  t3.rspm_serl
                        ");
        }
        return $data1;

    }

    public function GetSRDPO(Request $request){
        
        $data1 = array();
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t3.id        AS depot_id,
                                t3.dpot_name AS depot_name
                                FROM tm_dlrm AS t1
                                INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
                                INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
                                INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
                                INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
                                WHERE t1.dptp_id = 1 AND t5.aemp_id = '$emp_id'
                                ORDER BY t3.id ASC
                            ");
        }

        return $data1;

    }

    public function GetSRRoute(Request $request){
        $data1 = array();
        $Day_Name = $request->Day_Name;
        $emp_id = $request->emp_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t2.slgp_id                                                                                 AS Group_ID,
                                t1.rout_id                                                                                 AS Route_ID,
                                t3.rout_name                                                                               AS Route_Name,
                                t4.id                                                                                      AS Base_Code,
                                t4.base_name                                                                               AS Base_Name,
                                t1.rpln_day                                                                                AS Day
                                FROM tl_rpln AS t1
                                INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
                                INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
                                INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
                                WHERE t1.aemp_id = $emp_id and t1.rpln_day='$Day_Name'
                            ");
        }
        return $data1;

    }

    public function GetSRSUBDPO(Request $request){
        $data1 = array();
        $emp_id = $request->emp_id;
        $DPO = $request->dpo_id;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t1.id AS depot_id,
                                t1.acmp_id AS ou_id,
                                concat(t4.acmp_name, '(', t1.dlrm_name, ')') AS dsp_name
                                FROM tm_dlrm AS t1
                                INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
                                INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
                                INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
                                INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
                                WHERE t3.id = '$DPO' AND t5.aemp_id = '$emp_id' AND  t1.dptp_id = 1
                                ORDER BY t3.id ASC
                            ");
        }
        return $data1;
    }

    public function GetPromoSlabDetails(Request $request){
        $data1 = array();
        $data2 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                                SELECT DISTINCT t1.`prsb_fqty`AS From_QTY,
                                t1.`prsb_tqty`AS To_QTY,
                                t1.`prsb_disc`AS Given_QTY,
                                t1.`prsb_mosl`AS Pro_Modifier_id_sl,
                                '0'AS Unit_Factor,'0'AS Given_Unit_Factor
                                FROM `tm_prsb` t1 JOIN tm_prmd t2 ON(t1.`prmr_id`=t2.prmr_id)
                                LEFT JOIN tm_prmr t3 ON(t3.id=t2.prmr_id)
                                WHERE t1.`prmr_id`='$promo_id' AND t3.prms_edat>=CURDATE()
                                AND t3.prms_sdat<=CURDATE()
                                GROUP BY t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_disc`,t1.`prsb_mosl`
                            ");
            $data2 = DB::connection($db_conn)->select("
                                SELECT DISTINCT t2.amim_id AS Buy_Item_id,
                                t3.amim_code AS Buy_Item_ID,
                                t3.amim_name AS Buy_Item_Name
                                FROM `tm_prmr` t1 JOIN tm_prmd t2 ON(t1.`id`=t2.prmr_id)
                                LEFT JOIN tm_amim t3 ON(t3.id=t2.`amim_id`)
                                WHERE t2.`prmr_id`='$promo_id' AND t1.prms_edat>=CURDATE()
                                AND t1.prms_sdat<=CURDATE()
                                GROUP BY t2.amim_id,t3.amim_code,t3.amim_name
                            ");

        }

        return Array(
            "Buy" => array("data" => $data2, "action" => $request->country_id),
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public function GetPromoSingleFOCSlabDetails(Request $request){
        $data1 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                                SELECT t3.amim_id AS Buy_Item_ID,t2.amim_id AS Free_Item_ID,t1.`prsb_fqty`AS From_QTY,
                                t1.`prsb_tqty`AS To_QTY,t1.`prsb_qnty`AS Given_QTY,t1.`prsb_mosl`AS Pro_Modifier_id_sl 
                                FROM `tm_prsb`t1 JOIN tm_prmf t2 ON(t1.`prmr_id`=t2.prmr_id) 
                                LEFT JOIN tm_prmd t3 ON(t2.prmr_id=t3.prmr_id)
                                WHERE t3.`prmr_id`='$promo_id'
                                GROUP BY t3.amim_id,t2.amim_id,t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_qnty`,t1.`prsb_mosl`
                            ");
        }

        return Array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
        );

    }


    public function GetPromoFOCSlabDetails(Request $request){
        
        $data1 = array();
        $data2 = array();
        $data3 = array();

        $promo_id = $request->Promotion_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                            SELECT DISTINCT t1.`prsb_fqty`AS From_QTY,
                            t1.`prsb_tqty`AS To_QTY,
                            t1.`prsb_qnty`AS Given_QTY,
                            t1.`prsb_mosl`AS Pro_Modifier_id_sl,
                            '0'AS Unit_Factor,'0'AS Given_Unit_Factor
                            FROM `tm_prsb` t1 JOIN tm_prmd t2 ON(t1.`prmr_id`=t2.prmr_id)
                            LEFT JOIN tm_prmr t3 ON(t3.id=t2.prmr_id)
                            WHERE t1.`prmr_id`='$promo_id' AND t3.prms_edat>=CURDATE()
                            AND t3.prms_sdat<=CURDATE()
                            GROUP BY t1.`prsb_fqty`,t1.`prsb_tqty`,t1.`prsb_qnty`,t1.`prsb_mosl`
                        ");
            $data2 = DB::connection($db_conn)->select("
                            SELECT t2.amim_code as Free_Item_ID,t2.amim_name as Free_Item_Name
                            FROM `tm_prmf` t1 JOIN tm_amim t2 ON(t1.`amim_id`=t2.id)
                            WHERE t1.`prmr_id`='$promo_id'
                            GROUP BY t2.amim_code,t2.amim_name
                        ");

            $data3 = DB::connection($db_conn)->select("
                            SELECT DISTINCT t2.amim_id AS Buy_Item_id,
                            t3.amim_code AS Buy_Item_ID,
                            t3.amim_name AS Buy_Item_Name
                            FROM `tm_prmr` t1 JOIN tm_prmd t2 ON(t1.`id`=t2.prmr_id)
                            LEFT JOIN tm_amim t3 ON(t3.id=t2.`amim_id`)
                            WHERE t2.`prmr_id`='$promo_id' AND t1.prms_edat>=CURDATE()
                            AND t1.prms_sdat<=CURDATE()
                            GROUP BY t2.amim_id,t3.amim_code,t3.amim_name
                        ");

        }

        return Array(
            "Slab" => array("data" => $data1, "action" => $request->country_id),
            "Free" => array("data" => $data2, "action" => $request->country_id),
            "Buy" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public function orderSave(Request $request){
        
        $country = (new Country())->country($request->country_id);
        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
                $productive = 0;
                $line_id = "";
                if ($request->Order_Type == 'Productive') {
                    $orderSyncLog = OrderSyncLog::on($db_conn)->where(['oslg_moid' => $request->Order_Unique_ID])->first();
                    if ($orderSyncLog == null) {
                        $orderSequence = OrderSequence::on($db_conn)->where(['aemp_id' => $request->SR_ID, 'srsc_year' => date('y')])->first();
                        if ($orderSequence == null) {
                            $orderSequence = new OrderSequence();
                            $orderSequence->setConnection($db_conn);
                            $orderSequence->aemp_id = $request->SR_ID;
                            $orderSequence->srsc_year = date('y');
                            $orderSequence->srsc_ocnt = 0;
                            $orderSequence->srsc_rcnt = 0;
                            $orderSequence->srsc_ccnt = 0;
                            $orderSequence->cont_id = $request->country_id;
                            $orderSequence->lfcl_id = 1;
                            $orderSequence->aemp_iusr = $request->up_emp_id;
                            $orderSequence->aemp_eusr = $request->up_emp_id;
                            $orderSequence->save();
                        }
                        $employee = Employee::on($db_conn)->where(['id' => $request->SR_ID])->first();
                        $orderMaster = new OrderMaster();
                        $orderMaster->setConnection($db_conn);
                        $orderLines = json_decode($request->line_data);
                        $order_id = "O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);
                        $order_amount = array_sum(array_column($orderLines, 'P_T_Price'));
                        $orderMaster->ordm_ornm = $order_id;
                        $orderMaster->aemp_id = $request->SR_ID;
                        $orderMaster->slgp_id = $request->Group_ID;
                        $orderMaster->dlrm_id = $request->D_ID;
                        $orderMaster->acmp_id = $request->ou_id;
                        $orderMaster->site_id = $request->Outlet_ID;
                        $orderMaster->rout_id = $request->Route_ID;
                        $orderMaster->odtp_id = 1;
                        $orderMaster->mspm_id = $request->Site_MSP_ID;
                        $orderMaster->ocrs_id = 0;
                        $orderMaster->ordm_pono = '';
                        $orderMaster->aemp_cusr = 0;
                        $orderMaster->ordm_note = '';
                        $orderMaster->ordm_date = $request->Date;
                        $orderMaster->ordm_time = $request->Order_time;
                        // $orderMaster->ordm_time = date('Y-m-d H:i:s');
                        $orderMaster->ordm_drdt = $request->Delivery_Date;
                        $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                        $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                        $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                        $orderMaster->ordm_dtne = $request->Outlet_Distance;
                        $orderMaster->ordm_amnt = $order_amount;
                        $orderMaster->ordm_icnt = sizeof($orderLines);
                        $orderMaster->plmt_id = $request->Item_Price_List;
                        $orderMaster->cont_id = $request->country_id;
                        $orderMaster->lfcl_id = $request->Order_Status;
                        $orderMaster->aemp_iusr = $request->up_emp_id;
                        $orderMaster->aemp_eusr = $request->up_emp_id;
                        $orderMaster->save();
                        foreach ($orderLines as $orderLineData) {

                            $ifFree = 0;
                            if ($orderLineData->P_T_Price == 0) {
                                $ifFree = 1;
                            }
                            $orderLine = new OrderLine();
                            $orderLine->setConnection($db_conn);
                            $orderLine->ordm_id = $orderMaster->id;
                            $orderLine->ordm_ornm = $order_id;
                            $orderLine->amim_id = $orderLineData->P_ID;
                            $orderLine->ordd_qnty = $orderLineData->P_Qty;
                            $orderLine->ordd_inty = $orderLineData->P_Qty;
                            $orderLine->ordd_cqty = 0;
                            $orderLine->ordd_dqty = 0;
                            $orderLine->ordd_opds = $orderLineData->Promo_Discount == "" ? 0 : $orderLineData->Promo_Discount;
                            $orderLine->ordd_cpds = 0;
                            $orderLine->ordd_dpds = 0;
                            $orderLine->ordd_duft = $orderLineData->Item_Factor;
                            $orderLine->ordd_uprc = $orderLineData->Rate;
                            $orderLine->ordd_runt = 1;
                            $orderLine->ordd_dunt = 1;
                            $orderLine->prom_id = isset($orderLineData->promo_ref) ? $orderLineData->promo_ref : 0;
                            $orderLine->ordd_spdi = isset($orderLineData->sp_Discount) ? $orderLineData->sp_Discount : 0;
                            $orderLine->ordd_spdo = isset($orderLineData->sp_Discount) ? $orderLineData->sp_Discount : 0;
                            $orderLine->ordd_spdc = 0;
                            $orderLine->ordd_spdd = 0;
                            $orderLine->ordd_dfdo = isset($orderLineData->df_Discount) ? $orderLineData->df_Discount : 0;
                            $orderLine->ordd_dfdc = 0;
                            $orderLine->ordd_dfdd = 0;
                            $orderLine->ordd_excs = isset($orderLineData->excise_percent) ? $orderLineData->excise_percent : 0;
                            $orderLine->ordd_ovat = isset($orderLineData->vat_percent) ? $orderLineData->vat_percent : 0;
                            $orderLine->ordd_tdis = 0;
                            $orderLine->ordd_texc = 0;
                            $orderLine->ordd_tvat = 0;
                            $orderLine->ordd_oamt = $orderLineData->P_T_Price;
                            $orderLine->ordd_ocat = 0;
                            $orderLine->ordd_odat = 0;
                            $orderLine->ordd_amnt = 0;
                            $orderLine->ordd_rqty = 0;
                            $orderLine->ordd_smpl = $ifFree;
                            $orderLine->lfcl_id = 1;
                            $orderLine->cont_id = $request->country_id;
                            $orderLine->aemp_iusr = $request->up_emp_id;
                            $orderLine->aemp_eusr = $request->up_emp_id;
                            $orderLine->save();
                        }
                        $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();

                        $orderSyncLog = new OrderSyncLog();
                        $orderSyncLog->setConnection($db_conn);
                        $orderSyncLog->oslg_moid = $request->Order_Unique_ID;
                        $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                        $orderSyncLog->oslg_orid = $orderMaster->id;
                        $orderSyncLog->oslg_type = $request->Order_Type;

                        $orderSyncLog->lfcl_id = 1;
                        $orderSyncLog->cont_id = $request->country_id;
                        $orderSyncLog->aemp_iusr = $request->up_emp_id;
                        $orderSyncLog->aemp_eusr = $request->up_emp_id;
                        $orderSyncLog->save();
                        $productive = 1;
                        $line_id = $request->ID;

                    }

                } elseif ($request->Order_Type == 'Non_Productive') {
                    $nonProductive = new NonProductiveOutlet();
                    $nonProductive->setConnection($db_conn);
                    $nonProductive->aemp_id = $request->SR_ID;
                    $nonProductive->slgp_id = $request->Group_ID;
                    $nonProductive->dlrm_id = $request->D_ID;
                    $nonProductive->site_id = $request->Outlet_ID;
                    $nonProductive->rout_id = $request->Route_ID;
                    $nonProductive->npro_date = $request->Date;
                    $nonProductive->npro_time = $request->Order_time;
                    $nonProductive->nopr_id = $request->Exception_id;
                    $nonProductive->npro_note = $request->Exception_note;
                    $nonProductive->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                    $nonProductive->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                    $nonProductive->npro_dtne = $request->Outlet_Distance;
                    $nonProductive->cont_id = $request->country_id;
                    $nonProductive->lfcl_id = 1;
                    $nonProductive->aemp_iusr = $request->up_emp_id;
                    $nonProductive->aemp_eusr = $request->up_emp_id;
                    $nonProductive->save();
                    $productive = 0;
                    $line_id = $request->ID;

                }

                $siteVisit = SiteVisited::on($db_conn)->where(['site_id' => $request->Outlet_ID, 'ssvh_date' => $request->Date, 'aemp_id' => $request->SR_ID])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($db_conn);
                    $siteVisit->ssvh_date = $request->Date;
                    // $siteVisit->ssvh_date = date('Y-m-d');
                    $siteVisit->aemp_id = $request->SR_ID;
                    $siteVisit->site_id = $request->Outlet_ID;
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                } else if ($productive == 1) {
                    $siteVisit->SSVH_ISPD = $productive;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($db_conn)->commit();
                return array('column_id' => $line_id);
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
                //throw $e;
            }

        }

    }


    public function saveReturnOrder(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->order_id])->first();
                if ($orderSyncLog == null) {
                    $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                    if ($orderSequence == null) {
                        $orderSequence = new OrderSequence();
                        $orderSequence->setConnection($country->cont_conn);
                        $orderSequence->aemp_id = $request->emp_id;
                        $orderSequence->srsc_year = date('y');
                        $orderSequence->srsc_ocnt = 0;
                        $orderSequence->srsc_rcnt = 0;
                        $orderSequence->srsc_ccnt = 0;
                        $orderSequence->cont_id = $request->country_id;
                        $orderSequence->lfcl_id = 1;
                        $orderSequence->aemp_iusr = $request->up_emp_id;
                        $orderSequence->aemp_eusr = $request->up_emp_id;
                        $orderSequence->save();
                    }
                    $employee = Employee::on($country->cont_conn)->where(['id' => $request->emp_id])->first();
                    $returnMaster = new ReturnMaster();
                    $returnMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("R" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_rcnt + 1, 5, '0', STR_PAD_LEFT));
                    $order_amount = array_sum(array_column($orderLines, 'total_amount'));
                    $returnMaster->rtan_rtnm = $order_id;
                    $returnMaster->aemp_id = $request->emp_id;
                    $returnMaster->acmp_id = $request->ou_id;
                    $returnMaster->slgp_id = 1;
                    $returnMaster->dlrm_id = $request->depo_id;
                    $returnMaster->site_id = $request->site_id;
                    $returnMaster->rout_id = $request->route_id;
                    $returnMaster->rttp_id = 1;
                    $returnMaster->plmt_id = 1;
                    $returnMaster->rtan_date = $request->date;
                    $returnMaster->rtan_time = date('Y-m-d H:i:s');
                    $returnMaster->rtan_drdt = $request->date;
                    $returnMaster->rtan_dltm = date('Y-m-d H:i:s');
                    $returnMaster->geo_lat = $request->lat;
                    $returnMaster->geo_lon = $request->lon;
                    $returnMaster->rtan_amnt = $order_amount;
                    $returnMaster->rtan_icnt = sizeof($orderLines);
                    $returnMaster->rtan_pono = $request->party_grv_number;
                    $returnMaster->rtan_podt = $request->party_grv_date;;
                    $returnMaster->rtan_note = '';
                    $returnMaster->cont_id = $request->country_id;
                    $returnMaster->lfcl_id = 1;
                    $returnMaster->aemp_iusr = $request->up_emp_id;
                    $returnMaster->aemp_eusr = $request->up_emp_id;
                    $returnMaster->save();
                    foreach ($orderLines as $orderLineData) {
                        $returnLine = new ReturnLine();
                        $returnLine->setConnection($country->cont_conn);
                        $returnLine->rtan_id = $returnMaster->id;
                        $returnLine->ordm_ornm = $orderLineData->order_ref_no;
                        $returnLine->rtdd_rtan = $order_id;
                        $returnLine->amim_id = $orderLineData->product_id;
                        $returnLine->rtdd_qnty = $orderLineData->quantity_returned;
                        $returnLine->rtdd_dqty = 0;
                        $returnLine->rtdd_duft = $orderLineData->ctn_size;
                        $returnLine->rtdd_uprc = $orderLineData->pcs_price;
                        $returnLine->rtdd_runt = 1;
                        $returnLine->rtdd_dunt = 1;
                        $returnLine->rtdd_oamt = $orderLineData->total_amount;
                        $returnLine->rtdd_damt = 0;
                        $returnLine->rtdd_edat = date('Y-m-d');
                        $returnLine->rtdd_note = 0;
                        $returnLine->rtdd_rato = $orderLineData->discount_ratio;
                        $returnLine->rtdd_excs = $orderLineData->gst;
                        $returnLine->rtdd_ovat = $orderLineData->vat;
                        $returnLine->rtdd_tdis = 0;
                        $returnLine->rtdd_texc = 0;
                        $returnLine->rtdd_tvat = 0;
                        $returnLine->rtdd_amnt = 0;
                        $returnLine->dprt_id = $orderLineData->reason_id;
                        $returnLine->lfcl_id = 1;
                        $returnLine->cont_id = $request->country_id;
                        $returnLine->aemp_iusr = $request->up_emp_id;
                        $returnLine->aemp_eusr = $request->up_emp_id;
                        $returnLine->save();
                    }
                    $orderSequence->srsc_rcnt = $orderSequence->srsc_rcnt + 1;
                    $orderSequence->aemp_eusr = $request->up_emp_id;
                    $orderSequence->save();
                    $orderSyncLog = new OrderSyncLog();
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->return_id;
                    $orderSyncLog->oslg_ornm = $returnMaster->rtan_rtnm;
                    $orderSyncLog->oslg_orid = $returnMaster->id;
                    $orderSyncLog->oslg_type = 'grv';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();
                }
                $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->date, 'aemp_id' => $request->emp_id])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($country->cont_conn);
                    $siteVisit->ssvh_date = $request->date;
                    $siteVisit->aemp_id = $request->emp_id;
                    $siteVisit->site_id = $request->site_id;
                    $siteVisit->SSVH_ISPD = 0;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->ID);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }

    /*
    *   API MODEL V3 [END]
    */

     
}