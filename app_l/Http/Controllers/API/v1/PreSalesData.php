<?php

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\ReturnLine;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\SiteVisitHistory;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Site;
use Faker\Provider\DateTime;
use App\Http\Controllers\API\v2\OrderModuleDataUAE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreSalesData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function saveOrder(Request $request)
    {
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
                    $orderMaster = new OrderMaster();
                    $orderMaster->setConnection($country->cont_conn);
                    $orderLines = json_decode($request->line_data);
                    $order_id = strtoupper("O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT));
                    $order_amount = array_sum(array_column($orderLines, 'total_order_amount'));
                    $orderMaster->ordm_ornm = $order_id;
                    $orderMaster->aemp_id = $request->emp_id;
                    $orderMaster->acmp_id = $request->ou_id;
                    $orderMaster->slgp_id = $request->group_id;
                    $orderMaster->dlrm_id = $request->depo_id;
                    $orderMaster->site_id = $request->site_id;
                    $orderMaster->rout_id = $request->route_id;
                    $orderMaster->odtp_id = 1;
                    $orderMaster->ordm_date = $request->order_date;
                    $orderMaster->ordm_time = date('Y-m-d H:i:s');
                    $orderMaster->ordm_drdt = $request->order_date;
                    $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                    $orderMaster->geo_lat = $request->lat;
                    $orderMaster->geo_lon = $request->lon;
                    $orderMaster->ordm_dtne = $request->distance;
                    $orderMaster->ordm_amnt = $order_amount;
                    $orderMaster->ordm_icnt = sizeof($orderLines);
                    $orderMaster->plmt_id = $request->price_list_id;
                    $orderMaster->ocrs_id = 0;
                    $orderMaster->ordm_pono = '';
                    $orderMaster->aemp_cusr = 0;
                    $orderMaster->ordm_note = '';
                    $orderMaster->mspm_id = $request->mspm_id;
                    $orderMaster->cont_id = $request->country_id;
                    $orderMaster->lfcl_id = $request->status_id;
                    $orderMaster->aemp_iusr = $request->up_emp_id;
                    $orderMaster->aemp_eusr = $request->up_emp_id;
                    $orderMaster->save();
                    foreach ($orderLines as $orderLineData) {
                        $orderLine = new OrderLine();
                        $orderLine->setConnection($country->cont_conn);
                        $orderLine->ordm_id = $orderMaster->id;
                        $orderLine->ordm_ornm = $order_id;
                        $orderLine->amim_id = $orderLineData->product_id;
                        $orderLine->ordd_inty = $orderLineData->order_qty;
                        $orderLine->ordd_qnty = $orderLineData->order_qty;
                        $orderLine->ordd_cqty = 0;
                        $orderLine->ordd_dqty = 0;
                        $orderLine->ordd_rqty = 0;
                        $orderLine->ordd_opds = $orderLineData->promo_discount_order;
                        $orderLine->ordd_cpds = 0;
                        $orderLine->ordd_dpds = 0;
                        $orderLine->ordd_spdi = $orderLineData->order_discount;
                        $orderLine->ordd_spdo = $orderLineData->order_discount;
                        $orderLine->ordd_spdc = 0;
                        $orderLine->ordd_spdd = 0;
                        $orderLine->ordd_dfdo = $orderLineData->def_discount_order;
                        $orderLine->ordd_dfdc = 0;
                        $orderLine->ordd_dfdd = 0;
                        $orderLine->ordd_duft = $orderLineData->ctn_size;
                        $orderLine->ordd_uprc = $orderLineData->pcs_price;
                        $orderLine->ordd_runt = 1;
                        $orderLine->ordd_dunt = 1;
                        $orderLine->ordd_smpl = $orderLineData->is_order_line == '1' ? 0 : 1;
                        $orderLine->prom_id = $orderLineData->promo_ref == '' ? 0 : $orderLineData->promo_ref;
                        $orderLine->ordd_oamt = $orderLineData->total_order_amount;
                        $orderLine->ordd_ocat = 0;
                        $orderLine->ordd_odat = 0;
                        $orderLine->ordd_excs = $orderLineData->gst;
                        $orderLine->ordd_ovat = $orderLineData->vat;
                        $orderLine->ordd_tdis = 0;
                        $orderLine->ordd_texc = 0;
                        $orderLine->ordd_tvat = 0;
                        $orderLine->ordd_amnt = 0;
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
                    $orderSyncLog->setConnection($country->cont_conn);
                    $orderSyncLog->oslg_moid = $request->order_id;
                    $orderSyncLog->oslg_ornm = $orderMaster->ordm_ornm;
                    $orderSyncLog->oslg_orid = $orderMaster->id;
                    $orderSyncLog->oslg_type = 'order';
                    $orderSyncLog->lfcl_id = 1;
                    $orderSyncLog->cont_id = $request->country_id;
                    $orderSyncLog->aemp_iusr = $request->up_emp_id;
                    $orderSyncLog->aemp_eusr = $request->up_emp_id;
                    $orderSyncLog->save();
                }

                $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->order_date, 'aemp_id' => $request->emp_id])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($country->cont_conn);
                    $siteVisit->ssvh_date = $request->order_date;
                    $siteVisit->aemp_id = $request->emp_id;
                    $siteVisit->site_id = $request->site_id;
                    $siteVisit->ssvh_ispd = 1;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                } else {
                    $siteVisit->ssvh_ispd = 1;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
                //throw $e;
            }
        }
    }

    public function saveReturnOrder(Request $request)
    {
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
                    $returnMaster->lfcl_id = $request->status_id;
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
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public function siteVisited(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $productive = 0;
                if ($request->visit_status_id == 1) {
                    $siteVisited = new SiteVisitHistory();
                    $siteVisited->setConnection($country->cont_conn);
                    $siteVisited->aemp_id = $request->emp_id;
                    $siteVisited->site_id = $request->site_id;
                    $siteVisited->sith_date = $request->date;
                    $siteVisited->sith_stim = $request->start_time;
                    $siteVisited->sith_etim = $request->end_time;
                    $siteVisited->sith_ttim = $request->spend_time;
                    $siteVisited->geo_lat = $request->lat;
                    $siteVisited->geo_lon = $request->lon;
                    $siteVisited->sith_dist = $request->distance;
                    $siteVisited->lfcl_id = 1;
                    $siteVisited->cont_id = $request->country_id;
                    $siteVisited->aemp_iusr = $request->emp_id;
                    $siteVisited->aemp_eusr = $request->emp_id;
                    $siteVisited->save();
                } else {
                    $nonProductive = new NonProductiveOutlet();
                    $nonProductive->setConnection($country->cont_conn);
                    $nonProductive->aemp_id = $request->emp_id;
                    $nonProductive->slgp_id = 1;
                    $nonProductive->dlrm_id = 1;
                    $nonProductive->site_id = $request->site_id;
                    $nonProductive->rout_id = 1;
                    $nonProductive->npro_date = $request->date;
                    $nonProductive->npro_time = $request->start_time;
                    $nonProductive->nopr_id = $request->visit_status_id;
                    $nonProductive->npro_note = '';
                    $nonProductive->geo_lat = $request->lat;
                    $nonProductive->geo_lon = $request->lon;
                    $nonProductive->npro_dtne = $request->distance;
                    $nonProductive->cont_id = $request->country_id;
                    $nonProductive->lfcl_id = 1;
                    $nonProductive->aemp_iusr = $request->up_emp_id;
                    $nonProductive->aemp_eusr = $request->up_emp_id;
                    $nonProductive->save();
                    $productive = 0;
                }
                $siteVisit = SiteVisited::on($country->cont_conn)->where(['site_id' => $request->site_id, 'ssvh_date' => $request->date, 'aemp_id' => $request->emp_id])->first();
                if ($siteVisit == null) {
                    $siteVisit = new SiteVisited();
                    $siteVisit->setConnection($country->cont_conn);
                    $siteVisit->ssvh_date = $request->date;
                    $siteVisit->aemp_id = $request->emp_id;
                    $siteVisit->site_id = $request->site_id;
                    $siteVisit->ssvh_ispd = 0;
                    $siteVisit->cont_id = $request->country_id;
                    $siteVisit->lfcl_id = 1;
                    $siteVisit->aemp_iusr = $request->up_emp_id;
                    $siteVisit->aemp_eusr = $request->up_emp_id;
                    $siteVisit->save();
                }
                DB::connection($country->cont_conn)->commit();
                return array('column_id' => $request->id);
            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

    public function preSalesData(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.id, t1.rpln_day, t2.rout_name, t2.rout_code) AS column_id,
  t2.rout_name                                           AS route_name,
  t2.rout_code                                           AS route_code,
  t2.id                                                  AS route_id,
  t1.rpln_day                                            AS day_name
FROM tl_rpln AS t1
  INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_id = t3.id
WHERE t1.aemp_id = $request->emp_id
GROUP BY t1.id, t2.rout_name, t2.rout_code, t2.id, t1.rpln_day");

            $data2 = DB::connection($country->cont_conn)->select("SELECT
  concat(curdate(), t3.id, t3.dpot_name, t1.id, t1.acmp_id, concat(t4.acmp_name, '(', t1.dlrm_name, ')')) AS column_id,
  t3.id                                                                                                   AS depot_id,
  t3.dpot_name                                                                                            AS depot_name,
  t1.id                                                                                                   AS dsp_id,
  t1.acmp_id                                                                                              AS ou_id,
  concat(t4.acmp_name, '(', t1.dlrm_name, ')')                                                            AS dsp_name,
  0                                                                                                       AS visibility
FROM tm_dlrm AS t1
  INNER JOIN tm_whos AS t2 ON t1.whos_id = t2.id
  INNER JOIN tm_dpot AS t3 ON t2.dpot_id = t3.id
  INNER JOIN tm_acmp AS t4 ON t1.acmp_id = t4.id
  INNER JOIN tl_emcm AS t5 ON t4.id = t5.acmp_id
WHERE t1.dptp_id = 1 and t5.aemp_id=$request->emp_id
ORDER BY t3.id ASC;");


            /*$data4 = DB::connection($country->cont_conn)->select("SELECT
  concat(t7.id, t7.plmt_name, t8.amim_id, t8.pldt_snme, t9.amim_code, t11.id, t11.issc_name, t8.pldt_tppr,
         t8.pldt_tpgp, t8.pldt_mrpp, t9.amim_duft, t9.lfcl_id,t9.amim_pvat,t9.amim_pexc) AS column_id,
  t7.id                                                        AS price_id,
  t7.plmt_name                                                 AS price_name,
  t8.amim_id                                                   AS sku_id,
  t8.pldt_snme                                                 AS sku_name,
  t9.amim_code                                                 AS sku_code,
  t11.id                                                       AS sub_category_id,
  t11.issc_name                                                AS sub_category_name,
  t8.pldt_tppr * t8.amim_duft                                  AS ctn_price,
  t8.pldt_tpgp * t8.amim_duft                                  AS grv_ctn_price,
  t8.pldt_mrpp * t8.amim_duft                                  AS mrp_price,
  t9.amim_duft                                                 AS ctn_size,
  t9.lfcl_id                                                   AS status_id,
  0                                                            AS out_of_stock,
  t9.amim_pvat                                                 AS vat,
  (t9.amim_pexc/(t8.pldt_tppr * t8.amim_duft))*100             AS gst,
  t10.slgp_id                                                  as group_id
FROM tl_rpln AS t1
  INNER JOIN tl_emcm AS t2 ON t1.aemp_id = t2.aemp_id 
  INNER JOIN tl_sgsm AS t3 ON t1.aemp_id = t3.aemp_id
  INNER JOIN tl_rsmp AS t4 ON t1.rout_id = t4.rout_id
  INNER JOIN tm_site AS t5 ON t4.site_id = t5.id
  INNER JOIN tl_stcm AS t6 ON t6.site_id = t4.site_id AND t6.acmp_id = t2.acmp_id
  INNER JOIN tm_plmt AS t7 ON t6.plmt_id = t7.id
  INNER JOIN tm_pldt AS t8 ON t7.id = t8.plmt_id
  INNER JOIN tm_amim AS t9 ON t8.amim_id = t9.id
  INNER JOIN tl_sgit AS t10 ON t3.slgp_id = t10.slgp_id AND t8.amim_id = t10.amim_id
  INNER JOIN tm_issc AS t11 ON t10.issc_id = t11.id
WHERE t1.aemp_id = $request->emp_id AND t1.rpln_day = dayname(curdate()) AND t9.lfcl_id = 1");*/
            $data4 = DB::connection($country->cont_conn)->select("SELECT
  t1.column_id,
  t1.price_id,
  t1.price_id,
  t1.price_name,
  t1.sku_id,
  t1.sku_name,
  t1.sku_code,
  t1.sub_category_id,
  t1.sub_category_name,
  t1.ctn_price,
  t1.grv_ctn_price,
  t1.mrp_price,
  t1.ctn_size,
  t1.status_id,
  t1.out_of_stock,
  t1.vat,
  t1.gst,
  t1.group_id
FROM (
       SELECT
         concat(t7.id, t7.plmt_name, t8.amim_id, t8.pldt_snme, t9.amim_code, t11.id, t11.issc_name, t8.pldt_tppr,
                t8.pldt_tpgp, t8.pldt_mrpp, t9.amim_duft, t9.lfcl_id, t9.amim_pvat, t9.amim_pexc) AS column_id,
         t7.id                                                                                    AS price_id,
         t7.plmt_name                                                                             AS price_name,
         t8.amim_id                                                                               AS sku_id,
         t8.pldt_snme                                                                             AS sku_name,
         t9.amim_code                                                                             AS sku_code,
         t11.id                                                                                   AS sub_category_id,
         t11.issc_name                                                                            AS sub_category_name,
         t8.pldt_tppr * t8.amim_duft                                                              AS ctn_price,
         t8.pldt_tpgp * t8.amim_duft                                                              AS grv_ctn_price,
         t8.pldt_mrpp * t8.amim_duft                                                              AS mrp_price,
         t9.amim_duft                                                                             AS ctn_size,
         t9.lfcl_id                                                                               AS status_id,
         0                                                                                        AS out_of_stock,
         t9.amim_pvat                                                                             AS vat,
         (t9.amim_pexc / (t8.pldt_tppr * t8.amim_duft)) * 100                                     AS gst,
         t10.slgp_id                                                                              AS group_id
       FROM tl_rpln AS t1
         INNER JOIN tl_emcm AS t2 ON t1.aemp_id = t2.aemp_id
         INNER JOIN tl_sgsm AS t3 ON t1.aemp_id = t3.aemp_id
         INNER JOIN tl_rsmp AS t4 ON t1.rout_id = t4.rout_id
         INNER JOIN tm_site AS t5 ON t4.site_id = t5.id
         INNER JOIN tl_stcm AS t6 ON t6.site_id = t4.site_id AND t6.acmp_id = t2.acmp_id
         INNER JOIN tm_plmt AS t7 ON t6.plmt_id = t7.id
         INNER JOIN tm_pldt AS t8 ON t7.id = t8.plmt_id
         INNER JOIN tm_amim AS t9 ON t8.amim_id = t9.id
         INNER JOIN tl_sgit AS t10 ON t3.slgp_id = t10.slgp_id AND t8.amim_id = t10.amim_id
         INNER JOIN tm_issc AS t11 ON t10.issc_id = t11.id
       WHERE t1.aemp_id = $request->emp_id AND t1.rpln_day = dayname(curdate()) AND t9.lfcl_id = 1
       UNION ALL
       SELECT
         concat(t7.id, t7.plmt_name, t8.amim_id, t8.pldt_snme, t9.amim_code, t11.id, t11.issc_name, t8.pldt_tppr,
                t8.pldt_tpgp, t8.pldt_mrpp, t9.amim_duft, t9.lfcl_id, t9.amim_pvat, t9.amim_pexc) AS column_id,
         t7.id                                                                                    AS price_id,
         t7.plmt_name                                                                             AS price_name,
         t8.amim_id                                                                               AS sku_id,
         t8.pldt_snme                                                                             AS sku_name,
         t9.amim_code                                                                             AS sku_code,
         t11.id                                                                                   AS sub_category_id,
         t11.issc_name                                                                            AS sub_category_name,
         t8.pldt_tppr * t8.amim_duft                                                              AS ctn_price,
         t8.pldt_tpgp * t8.amim_duft                                                              AS grv_ctn_price,
         t8.pldt_mrpp * t8.amim_duft                                                              AS mrp_price,
         t9.amim_duft                                                                             AS ctn_size,
         t9.lfcl_id                                                                               AS status_id,
         0                                                                                        AS out_of_stock,
         t9.amim_pvat                                                                             AS vat,
         (t9.amim_pexc / (t8.pldt_tppr * t8.amim_duft)) * 100                                     AS gst,
         t10.slgp_id                                                                              AS group_id
       FROM tl_ovpm AS t1
         INNER JOIN tl_emcm AS t2 ON t1.aemp_id = t2.aemp_id
         INNER JOIN tl_sgsm AS t3 ON t1.aemp_id = t3.aemp_id
         INNER JOIN tl_rsmp AS t4 ON t1.site_id = t4.site_id
         INNER JOIN tm_site AS t5 ON t4.site_id = t5.id
         INNER JOIN tl_stcm AS t6 ON t6.site_id = t4.site_id AND t6.acmp_id = t2.acmp_id
         INNER JOIN tm_plmt AS t7 ON t6.plmt_id = t7.id
         INNER JOIN tm_pldt AS t8 ON t7.id = t8.plmt_id
         INNER JOIN tm_amim AS t9 ON t8.amim_id = t9.id
         INNER JOIN tl_sgit AS t10 ON t3.slgp_id = t10.slgp_id AND t8.amim_id = t10.amim_id
         INNER JOIN tm_issc AS t11 ON t10.issc_id = t11.id
       WHERE t1.aemp_id = $request->emp_id AND t1.ovpm_date = curdate() AND t9.lfcl_id = 1 AND t5.lfcl_id = 1
     ) AS t1
GROUP BY t1.column_id,
  t1.price_id,
  t1.price_id,
  t1.price_name,
  t1.sku_id,
  t1.sku_name,
  t1.sku_code,
  t1.sub_category_id,
  t1.sub_category_name,
  t1.ctn_price,
  t1.grv_ctn_price,
  t1.mrp_price,
  t1.ctn_size,
  t1.status_id,
  t1.out_of_stock,
  t1.vat,
  t1.gst,
  t1.group_id");

            $data5 = DB::connection($country->cont_conn)->select("SELECT
  concat(id, t1.dprt_name, t1.dprt_code, t1.lfcl_id) AS column_id,
  t1.id                                      AS reason_id,
  t1.dprt_name                                    AS reason_name
FROM tm_dprt AS t1");
            return response()->json(Array(
                "tblt_pre_sales_route" => array("data" => $data1, "action" => $request->country_id),
                "tblt_depot_sub_depot" => array("data" => $data2, "action" => $request->country_id),
                "tblt_pre_price_list" => array("data" => $data4, "action" => $request->country_id),
                "tblt_return_reason" => array("data" => $data5, "action" => $request->country_id)
            ), 200);
        } else {
            return "Errrrrrrrrrrror";
        }
    }

    public function preSalesRouteData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $date = date('Y-m-d');
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.site_id,
         t1.route_id,
         t1.outlet_id,
         t1.pay_mode,
         t1.visited,
         t1.credit_limit,
         t1.due_amount,
         t1.pre_order_amount,
         t1.overdue,
         t1.must_sell_id,
         t1.date,
         !isnull(t1.price_list_id),
         t1.payment_type_id,
         t1.is_verified,
         t1.is_productive) AS column_id,
  t1.site_id,
  t1.route_id,
  t1.outlet_id,
  t1.site_name,
  t1.owner_name,
  t1.owner_mobile,
  t1.address,
  t1.pay_mode,
  t1.visited,
  t1.house_no,
  t1.site_trn,
  t1.credit_limit,
  t1.due_amount,
  t1.pre_order_amount,
  t1.overdue,
  t1.must_sell_id,
  t1.date,
  t1.lat,
  t1.lon,
  t1.price_list_id,
  t1.is_verified,
  t1.is_productive
FROM (
       SELECT
         t3.site_id                                   AS site_id,
         t1.id                                        AS route_id,
         t4.outl_id                                   AS outlet_id,
         concat(t4.site_name, '(', t4.site_olnm, ')') AS site_name,
         concat(t4.site_ownm, '(', t4.site_ownm, ')') AS owner_name,
         t4.site_mob1                                 AS owner_mobile,
         concat(t4.site_adrs, '(', t4.site_olad, ')') AS address,
         t7.optp_name                                 AS pay_mode,
         !isnull(t5.site_id)                          AS visited,
         t4.site_hsno                                 AS house_no,
         t4.site_vtrn                                 AS site_trn,
         t6.stcm_limt                                 AS credit_limit,
         t6.stcm_duea                                 AS due_amount,
         t6.stcm_ordm                                 AS pre_order_amount,
         t6.stcm_odue                                 AS overdue,
         if(t9.id > 0, t9.id, 0)                      AS must_sell_id,
         curdate()                                    AS date,
         t4.geo_lat                                   AS lat,
         t4.geo_lon                                   AS lon,
         t6.plmt_id                                   AS price_list_id,
         t6.id                                        AS payment_type_id,
         0                                            AS is_verified,
         if(t5.ssvh_ispd > 0, 1, 0)                   AS is_productive
       FROM tl_rpln AS t1
         INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
         INNER JOIN tl_rsmp AS t3 ON t2.id = t3.rout_id
         INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
         LEFT JOIN th_ssvh AS t5 ON t3.site_id = t5.site_id AND t5.ssvh_date = '$date' and t5.aemp_id= $request->emp_id
         INNER JOIN tl_stcm AS t6 ON t3.site_id = t6.site_id AND t6.acmp_id = $request->company_id
         INNER JOIN tm_optp AS t7 ON t6.optp_id = t7.id
         LEFT JOIN tl_msps AS t8 ON t3.site_id = t8.site_id
         LEFT JOIN tm_mspm AS t9 ON t8.mspm_id = t9.id AND '$date' BETWEEN t9.mspm_sdat AND t9.mspm_edat
       WHERE t1.aemp_id = $request->emp_id AND t1.rpln_day = DAYNAME('$date')
       UNION ALL
       SELECT
         t1.site_id                                   AS site_id,
         t2.id                                        AS route_id,
         t3.outl_id                                   AS outlet_id,
         concat(t3.site_name, '(', t3.site_olnm, ')') AS site_name,
         t3.site_ownm                                 AS owner_name,
         t3.site_mob1                                 AS owner_mobile,
         concat(t3.site_adrs, '(', t3.site_olad, ')') AS address,
         t6.optp_name                                 AS pay_mode,
         !isnull(t4.site_id)                          AS visited,
         t3.site_hsno                                 AS house_no,
         t3.site_vtrn                                 AS site_trn,
         t5.stcm_limt                                 AS credit_limit,
         t5.stcm_duea                                 AS due_amount,
         t5.stcm_ordm                                 AS pre_order_amount,
         t5.stcm_odue                                 AS overdue,
         if(t8.id > 0, t8.id, 0)                      AS must_sell_id,
         curdate()                                    AS date,
         t3.geo_lat                                   AS lat,
         t3.geo_lon                                   AS lon,
         t5.plmt_id                                   AS price_list_id,
         t5.optp_id                                   AS payment_type_id,
         0                                            AS is_verified,
         if(t4.ssvh_ispd > 0, 1, 0)                   AS is_productive
       FROM tl_ovpm AS t1
         INNER JOIN tm_rout AS t2 ON t1.rout_id = t2.id
         INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
         LEFT JOIN th_ssvh AS t4 ON t3.id = t4.site_id AND t4.ssvh_date = '$date' and t4.aemp_id=$request->emp_id
         INNER JOIN tl_stcm AS t5 ON t1.site_id = t5.site_id AND t5.acmp_id = $request->company_id
         INNER JOIN tm_optp AS t6 ON t5.optp_id = t6.id
         LEFT JOIN tl_msps AS t7 ON t1.site_id = t7.site_id
         LEFT JOIN tm_mspm AS t8 ON t7.mspm_id = t8.id AND '$date' BETWEEN t8.mspm_sdat AND t8.mspm_edat
       WHERE t1.aemp_id = $request->emp_id AND t3.lfcl_id = 1 AND t1.ovpm_date = '$date'
     ) AS t1
GROUP BY t1.site_id,
  t1.route_id,
  t1.outlet_id,
  t1.site_name,
  t1.owner_name,
  t1.owner_mobile,
  t1.address,
  t1.pay_mode,
  t1.visited,
  t1.house_no,
  t1.site_trn,
  t1.credit_limit,
  t1.due_amount,
  t1.pre_order_amount,
  t1.overdue,
  t1.must_sell_id,
  t1.date,
  t1.lat,
  t1.lon,
  t1.price_list_id,
  t1.is_verified,
  t1.is_productive, t1.payment_type_id
");
            return Array(
                "tblt_pre_route_site" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function outStockData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t4.id, t6.amim_id) AS column_id,
  t4.id                     AS depot_id,
  t6.amim_id                AS item_id
FROM tl_emcm AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.acmp_id = t2.acmp_id AND t2.dptp_id = 1
  INNER JOIN tm_whos AS t3 ON t2.whos_id = t3.id
  INNER JOIN tm_dpot AS t4 ON t3.dpot_id = t4.id
  INNER JOIN tl_sgsm AS t5 ON t1.aemp_id = t5.aemp_id
  INNER JOIN tl_sgit AS t6 ON t5.slgp_id = t6.slgp_id
  INNER JOIN tt_outs AS t7 ON t7.dpot_id = t4.id AND t7.amim_id = t6.amim_id
  INNER JOIN tm_amim AS t8 ON t7.amim_id = t8.id
WHERE t1.aemp_id = $request->emp_id AND t8.lfcl_id = 1");
            return Array(
                "tbld_out_of_stock" => array("data" => $data1, "action" => $request->country_id),
            );
        }
    }

    public function siteVisitStatus(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $data1 = DB::connection($country->cont_conn)->select("SELECT
  concat(t1.id, t1.nopr_name, t1.nopr_code) AS column_id,
  t1.id AS status_id,
  t1.nopr_name AS status_name
FROM tm_nopr AS t1 WHERE  t1.cont_id=$request->country_id");
            return Array("tbld_site_visit_status" => array("data" => $data1, "action" => $request->input('emp_id')));
        }
    }

    public function defaultDiscountData(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $tst = DB::connection($country->cont_conn)->select("SELECT
  concat(t2.site_id, t2.rout_id, t4.dfdm_id) AS column_id,
  t2.site_id                                 AS site_id,
  t4.dfdm_id                                 AS discount_id
FROM tl_rpln AS t1
  INNER JOIN tl_rsmp AS t2 ON t1.rout_id = t2.rout_id
  INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
  INNER JOIN tl_dfsm AS t4 ON t2.site_id = t4.site_id
WHERE t1.rpln_day = dayname(curdate()) AND t3.lfcl_id = 1 AND t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t2.site_id, t2.rout_id, t4.dfdm_id) AS column_id,
  t2.site_id                                 AS site_id,
  t4.dfdm_id                                 AS discount_id
FROM tm_aemp AS t1
  INNER JOIN tl_ovpm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
  INNER JOIN tl_dfsm AS t4 ON t2.site_id = t4.site_id
WHERE t1.id = $request->emp_id AND t3.lfcl_id = 1 AND t2.ovpm_date = curdate();;");

            $tst1 = DB::connection($country->cont_conn)->select("SELECT *
FROM (
       SELECT
         concat(t5.dfdm_id, t5.amim_id, t5.dfim_disc) AS column_id,
         t5.dfdm_id                                   AS discount_id,
         t5.amim_id                                   AS sku_id,
         t5.dfim_disc                                 AS percentage
       FROM tl_rpln AS t1
         INNER JOIN tl_rsmp AS t2 ON t1.rout_id = t2.rout_id
         INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
         INNER JOIN tl_dfsm AS t4 ON t2.site_id = t4.site_id
         INNER JOIN tm_dfim AS t5 ON t4.dfdm_id = t5.dfdm_id
         INNER JOIN tl_sgsm AS t6 ON t1.aemp_id = t6.aemp_id
         INNER JOIN tl_sgit AS t7 ON t6.slgp_id = t7.slgp_id AND t5.amim_id = t7.amim_id
         INNER JOIN tm_amim AS t8 ON t5.amim_id = t8.id
       WHERE t1.rpln_day = dayname(curdate()) AND t3.lfcl_id = 1 AND t1.aemp_id = $request->emp_id AND t8.lfcl_id = 1 AND
             t5.dfim_disc > 0
       UNION ALL
       SELECT
         concat(t5.dfdm_id, t5.amim_id, t5.dfim_disc) AS column_id,
         t5.dfdm_id                                   AS discount_id,
         t5.amim_id                                   AS sku_id,
         t5.dfim_disc                                 AS percentage
       FROM tm_aemp AS t1
         INNER JOIN tl_ovpm AS t2 ON t1.id = t2.aemp_id
         INNER JOIN tm_site AS t3 ON t2.site_id = t3.id
         INNER JOIN tl_dfsm AS t4 ON t2.site_id = t4.site_id
         INNER JOIN tm_dfim AS t5 ON t4.dfdm_id = t5.dfdm_id
         INNER JOIN tl_sgsm AS t6 ON t1.id = t6.aemp_id
         INNER JOIN tl_sgit AS t7 ON t6.slgp_id = t7.slgp_id AND t5.amim_id = t7.amim_id
         INNER JOIN tm_amim AS t8 ON t5.amim_id = t8.id
       WHERE t1.id = $request->emp_id AND t3.lfcl_id = 1 AND t2.ovpm_date = curdate() AND t8.lfcl_id = 1 AND t5.dfim_disc > 0) AS t1
GROUP BY t1.discount_id, t1.sku_id, t1.percentage, column_id");
            return Array(
                "tblt_pre_discount_site" => array("data" => $tst, "action" => $request->country_id),
                "tblt_pre_default_discount" => array("data" => $tst1, "action" => $request->country_id),
            );
        }
    }

    public function preOrderCancel(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $table = "receive_data";
            $table_data = "";
            $status_id = 18;
            if ($request->role_id == 1) {
                $status_id = 16;
            }
            if ($request->role_id > 2) {
                $status_id = 21;
            }

            if (0 == (new OrderModuleDataUAE)->Check_Invoice_Ok_OR_Not($request->order_id, $request->country_id)) {
                return array(
                    'success' => 0,
                    'message' => "Order Already Processed for Delivery !! Cancel Order Failed",
                );
            }


            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $request->order_id)->first();
            if ($orderMaster != null) {

                if ($orderMaster->lfcl_id == 11) {
                    $table_data = "Order Already Delivered";
                } else if ($orderMaster->lfcl_id == 34) {
                    $table_data = "Order Already Processed for Delivery";
                } else if ($orderMaster->ordm_date != date('Y-m-d')) {
                    $table_data = "Failed!! you can cancel only current date order";
                } else {
                    $orderMaster->lfcl_id = $status_id;
                    $orderMaster->ocrs_id = $request->reason_id;
                    $orderMaster->aemp_eusr = $request->emp_id;
                    $orderMaster->attr1 = '-';
                    $orderMaster->save();

                    DB::connection($country->cont_conn)->table('tl_stcm')->where(['site_id' => $orderMaster->site_id,'slgp_id' => $orderMaster->slgp_id,'optp_id' =>2
                    ])->decrement('stcm_ordm', $orderMaster->ordm_amnt);

                    $table_data = "Order Successfully Canceled";
                }
            }

            return Array($table => $table_data);
        }
    }

    public function preGrvOrderCancel(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($country) {
            $table = "receive_data";
            $table_data = "";
            $status_id = 18;
            if ($request->role_id == 1) {
                $status_id = 16;
            }
            if ($request->role_id > 2) {
                $status_id = 21;
            }

            // $ReturnMaster = ReturnMaster::on($country->cont_conn)->where('rtan_rtnm', '=', $request->order_id)->first();
            $ReturnMaster = ReturnMaster::on($db_conn)->where(['rtan_rtnm' => $request->order_id])->first();
            if ($ReturnMaster != null) {
                $ReturnMaster->lfcl_id = $status_id;
                $ReturnMaster->attr4 = $request->reason_id;
                $ReturnMaster->aemp_eusr = $request->emp_id;
                $ReturnMaster->save();
                $table_data = "GRV Successfully Canceled";
            }

            return Array($table => $table_data);
        }
    }
	
	public function VanOrderCancel(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {           
            $table_data = "";
            $status_id = 18;
            if ($request->role_id == 10) {
                $status_id = 16;
            }
            if ($request->role_id > 2) {
                $status_id = 21;
            }          
		    // $module_type = $country->module_type;
            // if($module_type==2){            
            // }
			
            $orderMaster = OrderMaster::on($country->cont_conn)->where('ordm_ornm', '=', $request->order_id)->first();
            if ($orderMaster != null) {

                if ($orderMaster->lfcl_id == 11) {
                    $table_data = "Order Already Delivered";
                } else if ($orderMaster->ordm_date != date('Y-m-d')) {
                    $table_data = "Failed!! you can cancel only current date order";
                } else {
                    $orderMaster->lfcl_id = $status_id;
                    $orderMaster->ocrs_id = $request->reason_id;
                    $orderMaster->aemp_eusr = $request->emp_id;
                    $orderMaster->attr1 = '-';
                    $orderMaster->save();
                  				
                    DB::connection($country->cont_conn)
                    ->table('dm_trip_master')
                    ->where('ORDM_ORNM', $request->order_id)
                    ->delete();
					DB::connection($country->cont_conn)
                    ->table('dm_trip_detail')
                    ->where('ORDM_ORNM', $request->order_id)
                    ->delete();				   
							      
                    DB::connection($country->cont_conn)->table('tl_stcm')->where(['site_id' => $orderMaster->site_id,'slgp_id' => $orderMaster->slgp_id,'optp_id' =>2
                    ])->decrement('stcm_ordm', $orderMaster->ordm_amnt);

                    $table_data = "Order Successfully Canceled";
                }
            }

            $result_data = array(
                    'success' => 1,                   
                    'message' => $table_data,
                );
				 return $result_data;
        }
    }
	
	public function VanGRVCancel(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {           
            $table_data = "";
            $status_id = 18;
            if ($request->role_id == 10) {
                $status_id = 16;
            }
            if ($request->role_id > 2) {
                $status_id = 21;
            }          
		    // $module_type = $country->module_type;
            // if($module_type==2){            
            // }
			
            $orderMaster = DB::connection($country->cont_conn)->table('tt_rtan')->where('rtan_rtnm', '=', $request->order_id)->first();
            if ($orderMaster != null) {

                if ($orderMaster->lfcl_id == 11) {
                    $orderMaster->lfcl_id = 1;
                    $orderMaster->dm_trip='';                    
                    $orderMaster->aemp_eusr = $request->emp_id;                   
                    $orderMaster->save();
					
					$sql = "SET autocommit=0;";
                    $sql_rtan = "UPDATE `tt_rtan` SET lfcl_id=1,dm_trip=''
                    WHERE `rtan_rtnm`='$request->order_id';";
					$sql_rtdd = "UPDATE `tt_rtdd` SET rtdd_dqty=0,rtdd_damt=0
                    WHERE `rtdd_rtan`='$request->order_id';";

                    DB::connection($db_conn)->unprepared($sql);
                    DB::connection($db_conn)->unprepared($sql_rtan);
                    DB::connection($db_conn)->unprepared($sql_rtdd);														                                                                         				
                    DB::connection($country->cont_conn)
                    ->table('dm_collection')
                    ->where('COLL_NUMBER', $request->order_id)
                    ->delete();
					DB::connection($db_conn)->commit();
                    $table_data = "GRV Successfully Canceled";
					$result_data = array(
                    'success' => 1,                   
                    'message' => $table_data,
                     );
                } else {
					
				    $table_data = "Cancel GRV Failed ";	
					$result_data = array(
                    'success' => 0,                   
                    'message' => $table_data,
                    );
                }
            }            
				 return $result_data;
        }
    }

}