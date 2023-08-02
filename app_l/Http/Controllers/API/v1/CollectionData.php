<?php

namespace App\Http\Controllers\API\v1;

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
use App\Http\Controllers\Controller;
use App\BusinessObject\Attendance;
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


    public function invoiceSave(Request $request)
    {
        /*// DB::enableQueryLog();*/
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $dataSiteBalance = new SiteBalance();
                $dataSiteBalance->setConnection($country->cont_conn);
                $dataSiteBalance->acmp_id = $request->ou_id;
                $dataSiteBalance->invc_code = $request->invoice_code;
                $dataSiteBalance->invc_lcod = $request->invoice_code;
                $dataSiteBalance->invc_date = $request->date;
                $dataSiteBalance->aemp_cusr = $request->emp_code;
                $dataSiteBalance->aemp_susr = $request->sr_id;
                $dataSiteBalance->invc_note = '';
                $dataSiteBalance->site_id = $request->site_id;
                $dataSiteBalance->trip_id = $request->trip_id;
                $dataSiteBalance->invc_taxc = '';
                $dataSiteBalance->invc_amnt = $request->invoice_amount;
                $dataSiteBalance->invc_mamt = $request->collection_amount;
                $dataSiteBalance->invt_id = $request->invoice_type;
                $dataSiteBalance->trnt_id = $request->transaction_type;
                $dataSiteBalance->invc_gamt = 0;
                $dataSiteBalance->invc_exca = 0;
                $dataSiteBalance->invc_vata = 0;
                $dataSiteBalance->invc_tdis = 0;
                $dataSiteBalance->invc_ramt = $request->round_amount;
                $dataSiteBalance->invc_lock = $request->trip_status_id;
                $dataSiteBalance->invc_icod = '';
                $dataSiteBalance->invc_vcod = '';
                $dataSiteBalance->cltn_id = 0;
                $dataSiteBalance->cltn_amnt = 0;
                $dataSiteBalance->lfcl_id = $request->status_id;
                $dataSiteBalance->cont_id = $request->country_id;
                $dataSiteBalance->aemp_iusr = $request->up_emp_id;
                $dataSiteBalance->aemp_eusr = $request->up_emp_id;
                $dataSiteBalance->save();
                $orderSyncLog = OrderSyncLog::on($country->cont_conn)->where(['oslg_moid' => $request->invoice_code])->first();
                if ($orderSyncLog != null) {
                    DB::connection($country->cont_conn)->table('tt_invc')->where('invc_code', $request->invoice_code)->update(['invc_code' => $orderSyncLog->oslg_ornm]);
                    DB::connection($country->cont_conn)->table('tt_clim')->where('invc_code', $request->invoice_code)->update(['invc_code' => $orderSyncLog->oslg_ornm]);
                }

                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);

            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function srCashCustomerCredit(Request $request)
    {
        /*// DB::enableQueryLog();*/
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $dmCredit = new DMCashCustomerCredit();
                $dmCredit->setConnection($country->cont_conn);
                $dmCredit->aemp_id = $request->sr_id;
                $dmCredit->invc_code = $request->invoice_code;
                $dmCredit->site_id = $request->site_id;
                $dmCredit->trip_id = $request->trip_id;
                $dmCredit->srcr_amnt = $request->request_amount;
                $dmCredit->lfcl_id = $request->status_id;
                $dmCredit->cont_id = $request->country_id;
                $dmCredit->aemp_iusr = $request->up_emp_id;
                $dmCredit->aemp_eusr = $request->up_emp_id;
                $dmCredit->save();
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);

            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function dmCollectionSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                $dataCollectionLines = json_decode($request->line_data);
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
                $order_id = strtoupper("C" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ccnt + 1, 5, '0', STR_PAD_LEFT));
                $orderSequence->srsc_ccnt = $orderSequence->srsc_ccnt + 1;
                $orderSequence->aemp_eusr = $request->up_emp_id;
                $orderSequence->save();
                $dataOutletCollection = new OutletCollection();
                $dataOutletCollection->setConnection($country->cont_conn);
                $dataOutletCollection->cltn_code = $order_id;
                $dataOutletCollection->cltn_ucod = $request->column_id;
                $dataOutletCollection->aemp_cusr = $request->emp_id;
                $dataOutletCollection->aemp_susr = $request->sr_id;
                $dataOutletCollection->acmp_id = $request->ou_id;
                $dataOutletCollection->trip_id = $request->trip_id;
                $dataOutletCollection->cltn_date = $request->date;
                $dataOutletCollection->cltn_note = '';
                $dataOutletCollection->oult_id = $request->outlet_id;
                $dataOutletCollection->cltn_amnt = $request->amount;
                $dataOutletCollection->cltn_mamt = $request->amount;
                $dataOutletCollection->clpt_id = $request->payment_type;
                $dataOutletCollection->clmt_id = 1;
                $dataOutletCollection->cltn_chqn = '';
                $dataOutletCollection->cltn_cdat = date('Y-m-d');
                $dataOutletCollection->bank_id = 0;
                $dataOutletCollection->cltn_note = '';
                $dataOutletCollection->aemp_vusr = 0;
                $dataOutletCollection->aemp_musr = 0;
                $dataOutletCollection->aemp_qusr = 0;
                $dataOutletCollection->aemp_pusr = 0;
                $dataOutletCollection->cltn_ctme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_vtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_mtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_qtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_ptme = date('Y-m-d H:m:s');
                $dataOutletCollection->aemp_yusr = 0;
                $dataOutletCollection->cltn_ytme = date('Y-m-d H:m:s');
                $dataOutletCollection->clrj_id = 0;
                $dataOutletCollection->cltn_rnte = '';
                $dataOutletCollection->lfcl_id = $request->status_id;
                $dataOutletCollection->cont_id = $request->country_id;
                $dataOutletCollection->aemp_iusr = $request->up_emp_id;
                $dataOutletCollection->aemp_eusr = $request->up_emp_id;
                $dataOutletCollection->save();
                foreach ($dataCollectionLines as $dataCollectionLine) {
                    $outletCollectionMapping = new OutletCollectionMapping();
                    $outletCollectionMapping->setConnection($country->cont_conn);
                    $outletCollectionMapping->cltn_id = $dataOutletCollection->id;
                    //$outletCollectionMapping->site_id = $dataCollectionLine->site_id;
                    $outletCollectionMapping->invc_code = $dataCollectionLine->invoice_code;
                    $outletCollectionMapping->invc_lcod = $dataCollectionLine->invoice_code;
                    $outletCollectionMapping->clim_amnt = $dataCollectionLine->payment_amount;
                    $outletCollectionMapping->lfcl_id = 1;
                    $outletCollectionMapping->cont_id = $request->country_id;
                    $outletCollectionMapping->aemp_iusr = $request->up_emp_id;
                    $outletCollectionMapping->aemp_eusr = $request->up_emp_id;
                    $outletCollectionMapping->save();
                    $siteBalance = SiteBalance::on($country->cont_conn)->where(['invc_lcod' => $dataCollectionLine->invoice_code])->first();
                    if ($siteBalance != null) {
                        if ($dataCollectionLine->payment_amount <= $siteBalance->invc_amnt - $siteBalance->invc_mamt) {
                            $siteBalance->invc_mamt = $siteBalance->invc_mamt + $dataCollectionLine->payment_amount;
                        }
                        if ($siteBalance->invc_amnt == $siteBalance->invc_mamt) {
                            $siteBalance->lfcl_id = 26;
                        }
                        $siteBalance->save();
                    }
//DB::connection($country->cont_conn)->raw("`invc_mamt`")+
                    // DB::connection($country->cont_conn)->table('tt_invc')->where(['invc_lcod' => $dataCollectionLine->invoice_code])->update(['invc_mamt' => DB::connection($country->cont_conn)->raw('invc_mamt+' . $dataCollectionLine->payment_amount), 'lfcl_id' => $dataCollectionLine->status_id]);
                }

                $dataSiteBalance = new SiteBalance();
                $dataSiteBalance->setConnection($country->cont_conn);
                $dataSiteBalance->acmp_id = $request->ou_id;
                $dataSiteBalance->invc_code = $dataOutletCollection->cltn_code;
                $dataSiteBalance->invc_lcod = $request->column_id;
                $dataSiteBalance->invc_date = $request->date;
                $dataSiteBalance->aemp_cusr = $request->emp_id;
                $dataSiteBalance->aemp_susr = $request->sr_id;
                $dataSiteBalance->invc_note = '';
                $dataSiteBalance->site_id = $request->site_id;
                $dataSiteBalance->trip_id = $request->trip_id;
                $dataSiteBalance->invc_taxc = '';
                $dataSiteBalance->invc_amnt = $request->amount;
                $dataSiteBalance->invc_mamt = $request->amount;
                $dataSiteBalance->invt_id = 1;
                $dataSiteBalance->trnt_id = 2;
                $dataSiteBalance->invc_gamt = 0;
                $dataSiteBalance->invc_exca = 0;
                $dataSiteBalance->invc_vata = 0;
                $dataSiteBalance->invc_tdis = 0;
                $dataSiteBalance->invc_ramt = 0;
                $dataSiteBalance->invc_lock = 0;
                $dataSiteBalance->invc_icod = '';
                $dataSiteBalance->invc_vcod = '';
                $dataSiteBalance->cltn_id = $dataOutletCollection->id;
                $dataSiteBalance->cltn_amnt = 0;
                $dataSiteBalance->lfcl_id = $request->status_id;
                $dataSiteBalance->cont_id = $request->country_id;
                $dataSiteBalance->aemp_iusr = $request->up_emp_id;
                $dataSiteBalance->aemp_eusr = $request->up_emp_id;
                $dataSiteBalance->save();
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);

            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function collectionSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $orderSequence = OrderSequence::on($country->cont_conn)->where(['aemp_id' => $request->emp_id, 'srsc_year' => date('y')])->first();
                $dataCollectionLines = json_decode($request->line_data);
                $dataUploadCollectionImage = json_decode($request->collection_image, true);
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
                $order_id = strtoupper("C" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ccnt + 1, 5, '0', STR_PAD_LEFT));
                $orderSequence->srsc_ccnt = $orderSequence->srsc_ccnt + 1;
                $orderSequence->aemp_eusr = $request->up_emp_id;
                $orderSequence->save();
                $dataOutletCollection = new OutletCollection();
                $dataOutletCollection->setConnection($country->cont_conn);
                $dataOutletCollection->cltn_code = $order_id;
                $dataOutletCollection->cltn_ucod = $request->column_id;
                $dataOutletCollection->aemp_cusr = $request->emp_id;
                $dataOutletCollection->aemp_susr = $request->emp_id;
                $dataOutletCollection->acmp_id = $request->ou_id;
                $dataOutletCollection->trip_id = 0;
                $dataOutletCollection->cltn_date = $request->date;
                $dataOutletCollection->cltn_note = '';
                $dataOutletCollection->oult_id = $request->outlet_id;
                $dataOutletCollection->cltn_amnt = $request->amount;
                $dataOutletCollection->cltn_mamt = $request->allocation_amount;
                $dataOutletCollection->clpt_id = $request->payment_type;
                $dataOutletCollection->clmt_id = $request->collection_type;
                $dataOutletCollection->cltn_chqn = isset($request->cheque_no) ? $request->cheque_no : '';
                $dataOutletCollection->cltn_cdat = isset($request->cheque_date) ? $request->cheque_date : date('Y-m-d');
                $dataOutletCollection->bank_id = $request->bank_id;
                $dataOutletCollection->cltn_note = $request->collection_note;
                $dataOutletCollection->aemp_vusr = 0;
                $dataOutletCollection->aemp_musr = 0;
                $dataOutletCollection->aemp_qusr = 0;
                $dataOutletCollection->aemp_pusr = 0;
                $dataOutletCollection->cltn_ctme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_vtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_mtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_qtme = date('Y-m-d H:m:s');
                $dataOutletCollection->cltn_ptme = date('Y-m-d H:m:s');
                $dataOutletCollection->aemp_yusr = $request->emp_id;
                $dataOutletCollection->cltn_ytme = date('Y-m-d H:m:s');
                $dataOutletCollection->clrj_id = 0;
                $dataOutletCollection->cltn_rnte = '';
                $dataOutletCollection->lfcl_id = $request->status_id;
                $dataOutletCollection->cont_id = $request->country_id;
                $dataOutletCollection->aemp_iusr = $request->up_emp_id;
                $dataOutletCollection->aemp_eusr = $request->up_emp_id;
                $dataOutletCollection->save();
                foreach ($dataCollectionLines as $dataCollectionLine) {
                    $outletCollectionMapping = new OutletCollectionMapping();
                    $outletCollectionMapping->setConnection($country->cont_conn);
                    $outletCollectionMapping->cltn_id = $dataOutletCollection->id;
                    $outletCollectionMapping->invc_code = $dataCollectionLine->invoice_code;
                    $outletCollectionMapping->invc_lcod = $dataCollectionLine->invoice_code;
                    $outletCollectionMapping->clim_amnt = $dataCollectionLine->payment_amount;
                    $outletCollectionMapping->lfcl_id = 1;
                    $outletCollectionMapping->cont_id = $request->country_id;
                    $outletCollectionMapping->aemp_iusr = $request->up_emp_id;
                    $outletCollectionMapping->aemp_eusr = $request->up_emp_id;
                    $outletCollectionMapping->save();
                    $siteBalance = SiteBalance::on($country->cont_conn)->where(['invc_code' => $dataCollectionLine->invoice_code])->first();
                    if ($siteBalance != null) {
                        if ($dataCollectionLine->payment_amount <= $siteBalance->invc_amnt - $siteBalance->invc_mamt) {
                            $siteBalance->invc_mamt = $siteBalance->invc_mamt + $dataCollectionLine->payment_amount;
                        }
                        if ($siteBalance->invc_amnt == $siteBalance->invc_mamt) {
                            $siteBalance->lfcl_id = 26;
                        }
                        $siteBalance->save();
                    }

                }
                foreach ($dataUploadCollectionImage as $dataUploadCollectionImage1) {
                    $outletCollectionImageMapping = new OutletCollectionImageMapping();
                    $outletCollectionImageMapping->setConnection($country->cont_conn);
                    $outletCollectionImageMapping->cltn_id = $dataOutletCollection->id;
                    $outletCollectionImageMapping->clig_qimg = $dataUploadCollectionImage1;
                    $outletCollectionImageMapping->lfcl_id = 1;
                    $outletCollectionImageMapping->cont_id = $request->country_id;
                    $outletCollectionImageMapping->aemp_iusr = $request->up_emp_id;
                    $outletCollectionImageMapping->aemp_eusr = $request->up_emp_id;
                    $outletCollectionImageMapping->save();
                }
                $collectionTracking = new OutletCollectionTracking();
                $collectionTracking->setConnection($country->cont_conn);
                $collectionTracking->cltn_id = $dataOutletCollection->id;
                $collectionTracking->aemp_id = $request->up_emp_id;
                $collectionTracking->cqtr_time = date('Y-m-d H:i:s');
                $collectionTracking->geo_lat = 0;
                $collectionTracking->geo_lon = 0;
                $collectionTracking->cqtr_note = 'collected';
                $collectionTracking->cont_id = $request->country_id;
                $collectionTracking->lfcl_id = 1;
                $collectionTracking->aemp_iusr = $request->emp_id;
                $collectionTracking->aemp_eusr = $request->emp_id;
                $collectionTracking->save();
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);

            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function collectionModuleData(Request $request)
    {
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
GROUP BY t1.id, t1.site_name, t6.optp_name,t4.id, t4.aemp_name, t5.optp_id");

            return Array(
                "tbld_sr_site_collection" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function siteInvoiceListData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {

            $outlet_id = 0;
            $outlet_name = '';
            $site_id = 0;
            if ($request->site_id != '0' && $request->site_id != '') {
                $site_id = $request->site_id;
                $outlet_id = Site::on($country->cont_conn)->findorfail($request->site_id)->outl_id;
                //$outlet_id = Site::on($country->cont_conn)->find($request->site_id)->oult_id;
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
  AND t1.invc_date <= curdate() AND t1.invc_amnt > 0 and t1.acmp_id=$request->ou_id and (t1.site_id = $site_id OR t2.outl_id = $outlet_id)");
            return Array("receive_data" => $data1, "action" => $request->ou_id, "outlet_id" => $outlet_id, "outlet_name" => $outlet_name);
        }
    }

    public function personalCreditData(Request $request)
    {

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
  t2.invc_lock = 0");
            return Array(
                "tblt_sr_credit_invoice_mapping" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function tracking(Request $request)
    {
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

    public function trackingOther(Request $request)
    {
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

    public function trackingNoteList(Request $request)
    {
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

    public function status(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  t1.cltn_date                            AS date,
  t1.oult_id                              AS outlet_id,
  concat(t2.oult_code, '-', t2.oult_name) AS outlet_name,
  t1.id                                   AS collection_id,
  t1.cltn_amnt                            AS payment_amount,
  t1.clpt_id                              AS payment_id,
  t3.clpt_name                            AS payment_type,
  ''                                      AS collection_type,
  t1.lfcl_id                              AS status_id,
  t5.lfcl_name                            AS status
FROM tt_cltn AS t1
  INNER JOIN tm_oult AS t2 ON t1.oult_id = t2.id
  INNER JOIN tm_clpt AS t3 ON t1.clpt_id = t3.id
  INNER JOIN tm_lfcl AS t5 ON t1.lfcl_id = t5.id
WHERE t1.aemp_cusr = $request->emp_id AND t1.cltn_date BETWEEN '2020-01-01' AND curdate()
GROUP BY t1.id
ORDER BY t1.id DESC;
");
            return Array(
                "receive_data" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }


    public function chequeReceive(Request $request)
    {

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

}
