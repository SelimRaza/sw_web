<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API\v1;

use App\BusinessObject\BlockHistory;
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SpecialBudgetLine;
use App\BusinessObject\SpecialBudgetMaster;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockOrderData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function pendingSpecialBlockOrder(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            $selectSpecialBlockOrder = DB::connection($country->cont_conn)->select("
                        SELECT
                        t1.ordm_ornm AS column_id,
                        t1.acmp_id   AS ou_id,
                        t1.ordm_ornm AS order_id,
                        t1.ordm_date AS date,
                        t1.ordm_amnt AS order_amount,
                        t1.site_id   AS site_id,
                        t3.site_name AS site_name,
                        t1.lfcl_id   AS status_id,
                        t2.id        AS sr_id,
                        t1.slgp_id   AS slgp_id,
                        t2.aemp_name AS sr_name
                        FROM tt_ordm AS t1
                        INNER JOIN tm_aemp AS t2 ON t1.aemp_id = t2.id
                        INNER JOIN tm_site AS t3 ON t1.site_id = t3.id
                        WHERE t2.aemp_mngr = $request->emp_id AND t1.lfcl_id = 17
                    ");


            $selectSpecialBlockOrderLine = DB::connection($country->cont_conn)->select("
                        SELECT
                        t2.id                       AS column_id,
                        t1.ordm_ornm                AS order_id,
                        t2.amim_id                  AS product_id,
                        t3.amim_name                AS product_name,
                        t2.ordd_duft                AS ctn_size,
                        t2.ordd_uprc                AS unit_price,
                        t2.ordd_uprc * t2.ordd_duft AS ctn_price,
                        t2.ordd_qnty                AS order_qty,
                        t2.ordd_qnty                AS Confirm_qty,
                        t2.ordd_dfdo                   default_discount,
                        t2.ordd_spdo                AS discount,
                        t2.ordd_spdo                AS confirm_discount,
                        t2.ordd_opds                AS promo_discount,
                        t2.ordd_oamt                AS total_amount,
                        t2.prom_id                  AS promo_ref
                        FROM tt_ordm AS t1
                        INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
                        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                        INNER JOIN tm_aemp AS t4 ON t1.aemp_id = t4.id
                        WHERE t1.lfcl_id = 17 AND t4.aemp_mngr = $request->emp_id
            ");

            $balance = collect(DB::connection($country->cont_conn)->select("
                    SELECT t1.spbm_amnt AS pre_balance,spbm_limt as total_buget,spbm_avil AS avil
                    FROM tt_spbm AS t1
                    WHERE t1.aemp_id = $request->emp_id AND t1.spbm_mnth = MONTH(curdate()) 
                    AND t1.spbm_year = year(curdate()) AND t1.cont_id = $request->country_id")
            )->first();

            if ($balance) {
                $Balance = $balance->pre_balance;
                $total_buget = $balance->total_buget;
                $used_amt = $balance->avil;
            } else {
                $Balance = 0;
                $total_buget = 0;
                $used_amt = 0;
            }

            return Array(
                "tblt_block_order" => array("data" => $selectSpecialBlockOrder, "action" => $request->country_id),
                "tblt_block_order_line" => array("data" => $selectSpecialBlockOrderLine, "action" => $request->country_id),
                "balance" => $Balance,
                "total_budget" => $total_buget,
                "budget_spend" => $used_amt
            );
        }
    }

    public function approvedSpecialBlockOrder(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {

            $selectApprovedSpecialBlockOrder = DB::connection($country->cont_conn)->select("
                        SELECT
                        t5.ordm_ornm AS column_id,
                        t5.acmp_id   AS ou_id,
                        t1.ordm_ornm AS order_id,
                        t5.ordm_date AS date,
                        t5.ordm_amnt AS order_amount,
                        t5.site_id   AS site_id,
                        t3.site_name AS site_name,
                        t5.lfcl_id   AS status_id,
                        t2.id        AS sr_id,
                        t5.slgp_id   AS slgp_id,
                        t2.aemp_name AS sr_name
                        FROM tt_spbd AS t1 JOIN tt_spbm t4 ON(t1.spbm_id=t4.id) AND t1.aemp_iusr=t4.aemp_id
                       JOIN tt_ordm t5 ON(t1.ordm_ornm=t5.ordm_ornm)
                        INNER JOIN tm_aemp AS t2 ON t5.aemp_id = t2.id
                        INNER JOIN tm_site AS t3 ON t5.site_id = t3.id
                        WHERE t2.aemp_mngr = $request->emp_id AND
                        date(t1.created_at) between date('$request->selected_date')-INTERVAL 7 day and date('$request->selected_date')"
            );


            $selectApprovedSpecialBlockOrderLine = DB::connection($country->cont_conn)->select("
                        SELECT
                        t2.id                       AS column_id,
                        t5.ordm_ornm                AS order_id,
                        t2.amim_id                  AS product_id,
                        t3.amim_name                AS product_name,
                        t2.ordd_duft                AS ctn_size,
                        t2.ordd_uprc                AS unit_price,
                        t2.ordd_uprc * t2.ordd_duft AS ctn_price,
                        t2.ordd_qnty                AS order_qty,
                        t2.ordd_qnty                AS Confirm_qty,
                        t2.ordd_dfdo                   default_discount,
                        t2.ordd_spdo                AS discount,
                        t2.ordd_spdo                AS confirm_discount,
                        t2.ordd_opds                AS promo_discount,
                        t2.ordd_oamt                AS total_amount,
                        t2.prom_id                  AS promo_ref
                        FROM tt_spbd AS t1 JOIN tt_spbm t6 ON(t1.spbm_id=t6.id) AND t1.aemp_iusr=t6.aemp_id
                        JOIN tt_ordm AS t5 ON(t1.ordm_ornm=t5.ordm_ornm)
                        INNER JOIN tt_ordd AS t2 ON t5.id = t2.ordm_id
                        INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
                        INNER JOIN tm_aemp AS t4 ON t5.aemp_id = t4.id
                        WHERE t4.aemp_mngr = $request->emp_id AND date(t1.created_at) between date('$request->selected_date')-INTERVAL 7 day and date('$request->selected_date')
            ");


            return Array(
                "tblt_block_order" => array("data" => $selectApprovedSpecialBlockOrder, "action" => $request->country_id),
                "tblt_block_order_line" => array("data" => $selectApprovedSpecialBlockOrderLine, "action" => $request->country_id),
                "balance" => 0,
                "total_budget" => 0,
                "budget_spend" => 0
            );
        }
    }

    public function specialBlockBalance(Request $request)
    {

        $country = (new Country())->country($request->countryId);
        if ($country) {
            $tst = collect(DB::connection($country->cont_conn)->select("SELECT t1.spbm_amnt AS balance
           FROM tt_spbm AS t1
           WHERE t1.aemp_id = $request->empId AND t1.spbm_mnth = MONTH(curdate()) AND t1.spbm_year = year(curdate())
           AND t1.cont_id = $request->countryId"))->first();
            return Array(
                "receive_data" => array("data" => $tst, "action" => $request->countryId),
            );
        }
    }

    public function blockReleaseSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        if ($country) {
            DB::connection($country->cont_conn)->beginTransaction();
            try {
                $msg = "";
                $success = 0;

                $orderMaster = OrderMaster::on($country->cont_conn)->where(['ordm_ornm' => $request->order_id])->first();
                if ($request->status_id == 1) {
                    $ouSiteMapping = CompanySiteBalance::on($country->cont_conn)->where(['site_id' => $request->site_id, 'acmp_id' => $request->ou_id, 'slgp_id' => $request->slgp_id])->first();
                    $specialBudgetMaster = SpecialBudgetMaster::on($country->cont_conn)->where(['aemp_id' => $request->sv_id, 'spbm_mnth' => date('m'), 'spbm_year' => date('Y')])->first();
                    if ($specialBudgetMaster != null && $ouSiteMapping != null) {
                        if ($ouSiteMapping->optp_id == 1) {
                            // if ($specialBudgetMaster->spbm_amnt >= $request->special_amount) {
                            $orderMaster->lfcl_id = 1;
                            $dm_trip = DB::connection($country->cont_conn)->select("Select DELV_AMNT FROM dm_trip_master WHERE ORDM_ORNM='$request->order_id'");
                            $del_amount = $dm_trip ? $dm_trip[0]->DELV_AMNT : 0;
                            if ($del_amount > 0) {
                                $orderMaster->lfcl_id = 11;
								DB::connection($country->cont_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->order_id])->decrement('DELV_AMNT' ,$request->special_amount);
                            }
                            $orderLines = json_decode($request->line_data);
                            foreach ($orderLines as $orderLineData) {

                                if ($del_amount > 0) {
                                    $orderMaster->lfcl_id = 11;
                                    DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdo' => $orderLineData->discount,
                                        'ordd_spdc' => $orderLineData->discount, 'ordd_spdd' => $orderLineData->discount]);
                             
							       DB::connection($country->cont_conn)->table('dm_trip_detail')->where(['OID' => $orderLineData->column_id])->update(['DISCOUNT' => $orderLineData->discount]);
							      

							 } else {
                                   /* DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdo' => $orderLineData->discount,
                                        'ordd_spdc' => $orderLineData->discount]);*/
                                    DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdc' => $orderLineData->discount,
                                        'aemp_eusr' => $request->up_emp_id]);
                                }

                            }
                            $specialBudgetLine = new SpecialBudgetLine();
                            $specialBudgetLine->setConnection($country->cont_conn);
                            $specialBudgetLine->spbm_id = $specialBudgetMaster->id;
                            $specialBudgetLine->spbd_type = 'Approved';
                            $specialBudgetLine->ordm_ornm = $request->order_id;
                            $specialBudgetLine->spbd_amnt = $request->special_amount;
                            $specialBudgetLine->trnt_id = 2;
                            $specialBudgetLine->cont_id = $request->country_id;
                            $specialBudgetLine->lfcl_id = 17;
                            $specialBudgetLine->aemp_iusr = $request->up_emp_id;
                            $specialBudgetLine->aemp_eusr = $request->up_emp_id;
                            $specialBudgetLine->save();
                            $blockHistory = new BlockHistory();
                            $blockHistory->setConnection($country->cont_conn);
                            $blockHistory->ordm_id = $orderMaster->id;
                            $blockHistory->blck_rtme = date("Y-m-d H:i:s");
                            $blockHistory->cont_id = $request->country_id;
                            $blockHistory->lfcl_id = 17;
                            $blockHistory->aemp_iusr = $request->up_emp_id;
                            $blockHistory->aemp_eusr = $request->up_emp_id;
                            $blockHistory->save();
                            $specialBudgetMaster->spbm_amnt = $specialBudgetMaster->spbm_amnt - $request->special_amount;
                            $specialBudgetMaster->spbm_avil = $specialBudgetMaster->spbm_avil + $request->special_amount;
                            $specialBudgetMaster->save();
                            $msg = "Success";
                            $success = 1;
                            /*  } else {
                                  $success = 0;
                                  $msg = "Insufficient Balance";
                              }*/

                        } else {
                            // if ($ouSiteMapping->stcm_limt - $ouSiteMapping->stcm_ordm - $ouSiteMapping->stcm_duea < $request->net_amount) {
                                // $orderMaster->lfcl_id = 9;
                            // }
                            // if ($ouSiteMapping->stcm_odue > 0) {
                                // $orderMaster->lfcl_id = 14;
                            // } else {
                                //success
                                //   if ($specialBudgetMaster->spbm_amnt >= $request->special_amount) {
                                $orderMaster->lfcl_id = 1;
                                $dm_trip = DB::connection($country->cont_conn)->select("Select DELV_AMNT FROM dm_trip_master WHERE ORDM_ORNM='$request->order_id'");
                                $del_amount = $dm_trip ? $dm_trip[0]->DELV_AMNT : 0;

                                if ($del_amount > 0) {
                                    $orderMaster->lfcl_id = 11;
									DB::connection($country->cont_conn)->table('dm_trip_master')->where(['ORDM_ORNM' => $request->order_id])->decrement('DELV_AMNT' ,$request->special_amount);
                          
                                }

                                $orderLines = json_decode($request->line_data);
                                foreach ($orderLines as $orderLineData) {

                                    if ($del_amount > 0) {
                                        $orderMaster->lfcl_id = 11;
                                        DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdo' => $orderLineData->discount,
                                            'ordd_spdc' => $orderLineData->discount, 'ordd_spdd' => $orderLineData->discount]);
									    DB::connection($country->cont_conn)->table('dm_trip_detail')->where(['OID' => $orderLineData->column_id])->update(['DISCOUNT' => $orderLineData->discount]);
							    
                                    } else {
                                        /*DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdo' => $orderLineData->discount,
                                            'ordd_spdc' => $orderLineData->discount]);*/
                                        DB::connection($country->cont_conn)->table('tt_ordd')->where(['id' => $orderLineData->column_id])->update(['ordd_spdc' => $orderLineData->discount,
                                            'aemp_eusr' => $request->up_emp_id]);
                                    }
                                }
                                $specialBudgetLine = new SpecialBudgetLine();
                                $specialBudgetLine->setConnection($country->cont_conn);
                                $specialBudgetLine->spbm_id = $specialBudgetMaster->id;
                                $specialBudgetLine->spbd_type = 'Approved';
                                $specialBudgetLine->ordm_ornm = $request->order_id;
                                $specialBudgetLine->spbd_amnt = $request->special_amount;
                                $specialBudgetLine->trnt_id = 2;
                                $specialBudgetLine->cont_id = $request->country_id;
                                $specialBudgetLine->lfcl_id = 17;
                                $specialBudgetLine->aemp_iusr = $request->up_emp_id;
                                $specialBudgetLine->aemp_eusr = $request->up_emp_id;
                                $specialBudgetLine->save();
                                $blockHistory = new BlockHistory();
                                $blockHistory->setConnection($country->cont_conn);
                                $blockHistory->ordm_id = $orderMaster->id;
                                $blockHistory->blck_rtme = date("Y-m-d H:i:s");
                                $blockHistory->cont_id = $request->country_id;
                                $blockHistory->lfcl_id = 17;
                                $blockHistory->aemp_iusr = $request->up_emp_id;
                                $blockHistory->aemp_eusr = $request->up_emp_id;
                                $blockHistory->save();
                                $specialBudgetMaster->spbm_amnt = $specialBudgetMaster->spbm_amnt - $request->special_amount;
                                $specialBudgetMaster->spbm_avil = $specialBudgetMaster->spbm_avil + $request->special_amount;
                                $specialBudgetMaster->save();
                                $msg = "Success";
                                $success = 1;
                                /* } else {
                                     $success = 0;
                                     $msg = "Insufficient Balance";

                                 }*/
                           // }
						   
						    if ($ouSiteMapping->stcm_limt - $ouSiteMapping->stcm_ordm - $ouSiteMapping->stcm_duea < $request->net_amount) {
                                $orderMaster->lfcl_id = 9;
                            }else if ($ouSiteMapping->stcm_odue > 0) {
                                $orderMaster->lfcl_id = 14;
                            }
                        }
                    }
                } elseif ($request->status_id == 18) {
                    $orderMaster->lfcl_id = 21;
                    $orderMaster->ocrs_id = $request->cancel_reason;
                    $msg = "Order Cancel Successful";
                    $success = 1;
                }
                $orderMaster->save();

                DB::connection($country->cont_conn)->commit();

                return response()->json(array('success' => $success, 'message' => $msg, 'column_id' => 1), 200);

            } catch (\Exception $e) {
                DB::connection($country->cont_conn)->rollback();
                return $e;
            }
        }
    }

}