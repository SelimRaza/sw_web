<?php

namespace App\Http\Controllers\API\v4;

use App\BusinessObject\DepartmentEmployee;
use App\BusinessObject\DMCashCustomerCredit;
use App\BusinessObject\Note;
use App\BusinessObject\NoteComment;
use App\BusinessObject\NoteEmployee;
use App\BusinessObject\NoteImage;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\OutletCollection;
use App\BusinessObject\OutletCollectionBalanceMapping;
use App\BusinessObject\OutletCollectionImageMapping;
use App\BusinessObject\OutletCollectionMapping;
use App\BusinessObject\OutletCollectionTracking;
use App\BusinessObject\SalesGroupEmployee;
use App\BusinessObject\SiteBalance;
use App\BusinessObject\Attendance;

use App\Http\Controllers\Controller;

use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Outlet;
use App\MasterData\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function trackingNoteList(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
                            t1.cltn_id                                     AS collection_id,
                            t1.cqtr_note                                   AS note,
                            DATE_FORMAT(t1.cqtr_time, '%Y-%m-%d %h:%i %p') AS date_time,
                            concat(t2.aemp_usnm, '-', t2.aemp_name)        AS note_by
                            FROM tt_cqtr AS t1
                            INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                            WHERE t1.cltn_id = $request->collection_id
                            ORDER BY t1.cqtr_time DESC
                        ");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function chequeReceive(Request $request){

        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $collectionTracking = new OutletCollectionTracking();
                $collectionTracking->setConnection($country->cont_conn);
                $collectionTracking->cltn_id = $request->collection_id;
                $collectionTracking->aemp_id = $request->emp_id;
                $collectionTracking->cqtr_time = date('Y-m-d H:i:s');
                $collectionTracking->geo_lat = 0;
                $collectionTracking->geo_lon = 0;
                $collectionTracking->cqtr_note = $request->comment;
                $collectionTracking->cont_id = $request->country_id;
                $collectionTracking->lfcl_id = 1;
                $collectionTracking->aemp_iusr = $request->emp_id;
                $collectionTracking->aemp_eusr = $request->emp_id;
                $collectionTracking->save();
                if ($request->type_id==2) {
                    DB::connection($country->cont_conn)->table('tt_cltn')->where('id', $request->collection_id)->update(['aemp_yusr' => $request->emp_id, 'cltn_ytme' => date('Y-m-d H:i:s')]);
                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->collection_id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function trackingOther(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
                                t1.id                                   AS collection_id,
                                t1.cltn_code                            AS collection_code,
                                t1.cltn_amnt                            AS payment_amount,
                                t1.cltn_chqn                            AS cheque_no,
                                t1.cltn_cdat                            AS cheque_date,
                                t2.bank_name                            AS bank_name,
                                group_concat(t3.clig_qimg)              AS image_name,
                                TIMESTAMPDIFF(DAY, t1.cltn_ytme, now()) AS carrying_time,
                                t1.aemp_yusr                            AS carrying_emp_id,
                                concat(t4.aemp_usnm, '-', t4.aemp_name) AS carrying_emp_name
                                FROM tt_cltn AS t1
                                INNER JOIN tm_bank AS t2 ON t1.bank_id = t2.id
                                LEFT JOIN tm_clig AS t3 ON t1.id = t3.cltn_id
                                LEFT JOIN tm_aemp AS t4 ON t1.aemp_yusr = t4.id
                                WHERE t1.lfcl_id NOT IN (24, 26) AND t1.clpt_id = 2
                                GROUP BY t1.id
                                ORDER BY t1.id DESC
                            ");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function tracking(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
                                t1.id                                   AS collection_id,
                                t1.cltn_code                            AS collection_code,
                                t1.cltn_amnt                            AS payment_amount,
                                t1.cltn_chqn                            AS cheque_no,
                                t1.cltn_cdat                            AS cheque_date,
                                t2.bank_name                            AS bank_name,
                                group_concat(t3.clig_qimg)              AS image_name,
                                TIMESTAMPDIFF(DAY, t1.cltn_ytme, now()) AS carrying_time,
                                t1.aemp_yusr                            AS carrying_emp_id,
                                concat(t4.aemp_usnm, '-', t4.aemp_name) AS carrying_emp_name
                                FROM tt_cltn AS t1
                                INNER JOIN tm_bank AS t2 ON t1.bank_id = t2.id
                                LEFT JOIN tm_clig AS t3 ON t1.id = t3.cltn_id
                                LEFT JOIN tm_aemp AS t4 ON t1.aemp_yusr = t4.id
                                WHERE t1.aemp_yusr = $request->emp_id AND t1.lfcl_id NOT IN (24, 26) AND t1.clpt_id = 2
                                GROUP BY t1.id
                                ORDER BY t1.id DESC
                            ");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function collectionModuleData(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
                                CONCAT(t5.optp_id, t1.id, t1.site_name, t6.optp_name, t4.aemp_name) AS column_id,
                                t1.site_name                                                        AS site_name,
                                t1.id                                                               AS site_id,
                                t6.optp_name                                                        AS cash_type,
                                t4.id                                                               AS sr_id,
                                t4.aemp_name                                                        AS sr_name,
                                ''                                                                  AS route_id
                                FROM tm_site AS t1
                                INNER JOIN tl_rsmp AS t2 ON t1.id = t2.site_id
                                INNER JOIN tl_rpln AS t3 ON t2.rout_id = t3.rout_id
                                INNER JOIN tm_aemp AS t4 ON t3.aemp_id = t4.id
                                INNER JOIN tl_stcm AS t5 ON t1.id = t5.site_id
                                INNER JOIN tm_optp AS t6 ON t5.optp_id = t6.id
                                WHERE (t4.aemp_mngr = $request->emp_id OR t4.id = $request->emp_id) AND t5.optp_id = 2 AND t1.lfcl_id = 1
                                GROUP BY t1.id, t1.site_name, t6.optp_name,t4.id, t4.aemp_name, t5.optp_id
                            ");
            return Array(
                "tbld_sr_site_collection" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }


    public function siteInvoiceListData(Request $request){
        $country = (new Country())->country($request->country_id);
        if ($country) {

            $outlet_id = 0;
            $outlet_name = '';
            $site_id = 0;
            if ($request->site_id != '0' && $request->site_id != '') {
                $site_id = $request->site_id;
                $outlet_id = Site::on($country->cont_conn)->findorfail($request->site_id)->outl_id;
            }

            if ($request->outlet_id > 0) {
                $outlet_id = $request->outlet_id;
                $outlet_name = Outlet::on($country->cont_conn)->find($request->outlet_id)->oult_name;
            }

            $data1 = DB::connection($country->cont_conn)->select("SELECT
                                t1.invc_date                            AS date,
                                t1.site_id,
                                t1.invc_code                            AS invoice_code,
                                t1.invc_amnt                            AS invoice_amount,
                                t1.invc_mamt                            AS collection_amount,
                                t2.outl_id                              AS outlet_id,
                                t2.site_name                            AS outlet_name,
                                concat(t3.invt_name, ' ', t1.invc_note) AS invoice_type,
                                t2.site_name                            AS site_name,
                                t1.trnt_id                              AS transaction_type,
                                if(t4.stcm_isfx = 0, 2, 1)              AS limit_type,
                                t4.stcm_days                            AS days,
                                t1.invc_taxc                            AS vat_order_number
                                FROM tt_invc AS t1
                                INNER JOIN tm_site AS t2 ON t1.site_id = t2.id
                                INNER JOIN tm_invt AS t3 ON t1.invt_id = t3.id
                                INNER JOIN tl_stcm AS t4 ON t1.site_id = t4.site_id  AND t1.acmp_id = t4.acmp_id
                                WHERE
                                1 AND t1.lfcl_id IN (7,30) AND t1.invt_id != 14 AND t1.invc_lock = 0
                                AND t1.invc_date <= curdate() AND t1.invc_amnt > 0 and t1.acmp_id=$request->ou_id and (t1.site_id = $site_id OR t2.outl_id = $outlet_id)
                            ");
                        
                        return Array("receive_data" => $data1, "action" => $request->ou_id, "outlet_id" => $outlet_id, "outlet_name" => $outlet_name);
        }
    }

    public function personalCreditData(Request $request) {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
                                t1.id        AS sr_id,
                                t1.aemp_name AS sr_name,
                                t4.outl_id   AS outlet_id,
                                t2.site_id   AS site_id,
                                t4.Site_Name AS site_name,
                                t2.acmp_id   AS ou_id,
                                t2.invc_code AS invoice_code,
                                t2.invc_taxc AS tax_invoice,
                                t2.invc_date AS invoice_date,
                                t2.invc_amnt AS invoice_amount,
                                t2.invc_mamt AS collection_amount,
                                t2.lfcl_id   AS status_id,
                                t2.trnt_id   AS trn_type_id
                                FROM tm_aemp AS t1
                                INNER JOIN tt_invc AS t2 ON t1.id = t2.aemp_susr
                                INNER JOIN tl_stcm AS t3 ON t2.site_id = t3.site_id AND t2.acmp_id = t3.acmp_id
                                INNER JOIN tm_site AS t4 ON t2.site_id = t4.id
                                INNER JOIN tm_aemp AS t5 ON t1.aemp_mngr = t5.id
                                WHERE
                                (t1.id = $request->emp_id OR t1.aemp_mngr = $request->emp_id) AND t2.lfcl_id IN (7, 30) AND t3.optp_id = 1
                                AND
                                t2.invc_lock = 0
                            ");
            return Array(
                "tblt_sr_credit_invoice_mapping" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    

     


}