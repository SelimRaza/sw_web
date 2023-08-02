<?php

namespace App\Http\Controllers\API\v2;

use App\BusinessObject\Attendance;
use App\BusinessObject\DlearProfileAdd;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\RewardMaster;
use App\BusinessObject\RewardsDetails;
use App\BusinessObject\SiteLogRfl;
use App\BusinessObject\SiteMqr;
use App\BusinessObject\SiteRandom;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\Trip;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use AWS;

class OrderModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function orderSave(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $order_amount = 0;
        $order_id = 0;
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
                        $orderMaster->ocrs_id = 0;
                        $orderMaster->ordm_pono = '';
                        $orderMaster->aemp_cusr = 0;
                        $orderMaster->ordm_note = '';
                        $orderMaster->ordm_date = $request->Date;
                        $orderMaster->ordm_time = date('Y-m-d H:i:s');
                        $orderMaster->ordm_drdt = $request->Delivery_Date;
                        $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
                        $orderMaster->geo_lat = isset($request->User_Location) ? explode(',', $request->User_Location)[0] : 0;
                        $orderMaster->geo_lon = isset($request->User_Location) ? explode(',', $request->User_Location)[1] : 0;
                        $orderMaster->ordm_dtne = $request->Outlet_Distance;
                        $orderMaster->ordm_amnt = $order_amount;
                        $orderMaster->ordm_icnt = sizeof($orderLines);
                        $orderMaster->plmt_id = $request->Item_Price_List;
                        $orderMaster->cont_id = $request->country_id;
                        $orderMaster->lfcl_id = 1;
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
                            $orderLine->ordd_opds = $orderLineData->P_Discount;
                            $orderLine->ordd_cpds = 0;
                            $orderLine->ordd_dpds = 0;
                            $orderLine->ordd_duft = $orderLineData->Item_Factor;
                            $orderLine->ordd_uprc = $orderLineData->Rate;
                            $orderLine->ordd_runt = 1;
                            $orderLine->ordd_dunt = 1;
                            $orderLine->prom_id = 0;
                            $orderLine->ordd_spdi = 0;
                            $orderLine->ordd_spdo = 0;
                            $orderLine->ordd_spdc = 0;
                            $orderLine->ordd_spdd = 0;
                            $orderLine->ordd_dfdo = 0;
                            $orderLine->ordd_dfdc = 0;
                            $orderLine->ordd_dfdd = 0;
                            $orderLine->ordd_excs = 0;
                            $orderLine->ordd_ovat = 0;
                            $orderLine->ordd_tdis = 0;
                            $orderLine->ordd_texc = 0;
                            $orderLine->ordd_tvat = 0;
                            $orderLine->ordd_oamt = $orderLineData->P_T_Price;
                            $orderLine->ordd_ocat = 0;
                            $orderLine->ordd_odat = 0;
                            $orderLine->ordd_amnt = 0;
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

    public function dmTripWiseSROutletInfo(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_code = $request->emp_code;
            $ou_id = $request->ou_id;
            $slgp_id = $request->slgp_id;

            $trip_m = DB::connection($db_conn)->select("
         SELECT TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
        emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered ,optp_id AS p_type_id
        from(
        SELECT t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t2.AEMP_USNM,
        t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t2.ORDM_ORNM) totalINv,if(t2.TRIP_STATUS='D',1,'0') Delin,t5.optp_id
        FROM `dm_trip`t1 JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
        JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
        join tl_stcm t5 ON(t5.site_id=t3.id)
        JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
        WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N'  AND t5.acmp_id='$ou_id' AND t5.slgp_id='$slgp_id'
        GROUP by t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,t3.geo_lon,t2.TRIP_STATUS,t5.optp_id) f
        group by TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
        emp_id,aemp_name,geo_lat,geo_lon,optp_id");

            /* $trip_m = DB::connection($db_conn)->select("
         SELECT TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
         emp_id,aemp_name,geo_lat,geo_lon, sum(totalINv) total,sum(Delin) delivered
         from(
         SELECT t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,t2.AEMP_USNM,
         t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, count(distinct t2.ORDM_ORNM) totalINv,if(t2.TRIP_STATUS='D',1,'0') Delin
         FROM `dm_trip`t1 JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
         JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
         JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
         WHERE t1.`DM_ID`='$aemp_code' AND t1.`STATUS`='N'
         GROUP by t1.`TRIP_NO`,t2.SITE_ID,t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,t3.geo_lon,t2.TRIP_STATUS) f
         group by TRIP_NO,SITE_ID,SITE_CODE,site_name,site_mobile,AEMP_USNM,
         emp_id,aemp_name,geo_lat,geo_lon
        ");*/
            return $trip_m;
        }

    }

    public function dmSiteWiseOrderDetails1(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $trip_code = $request->trip_code;
            // $emp_id = $request->emp_id;

            $trip_m = DB::connection($db_conn)->select("
        SELECT t1.`ORDM_ORNM`,t2.site_code,t2.site_name
        FROM `dm_trip_detail`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
        WHERE t1.`TRIP_NO`='$trip_code'
        AND t1.`TRIP_STATUS`='N' 
        AND t1.`SITE_ID`= $site_id
        AND t1.DELV_QNTY=0
        GROUP BY t1.`ORDM_ORNM`
       ");

            foreach ($trip_m as $index => $data1) {
                $order_code = $data1->ORDM_ORNM;
                $data2 = DB::connection($db_conn)->select("
                SELECT t1.`OID`AS trp_line,t1.`ORDM_ORNM`,t1.`ORDM_DRDT`,t1.`AMIM_ID`,t1.`AMIM_CODE`,
                t2.amim_name AS Item_Name,t1.ORDD_UPRC AS Rate,t1.`prom_id`,t1.`ORDD_QNTY`,t1.`INV_QNTY`,t1.DISCOUNT
                FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id)
                WHERE t1.`ORDM_ORNM`='$order_code'
                AND t1.`TRIP_STATUS`='N' 
                AND t1.DELV_QNTY=0
                GROUP BY t1.`OID`,t1.`AMIM_ID`
               ");

                $trip_m[$index]->orderIdLists = $data2;
            }
            return $trip_m;
        }
    }

    public function SiteWiseOrderDetails(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $emp_id = $request->emp_id;
            $slgp_id = $request->slgp_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $order_m = DB::connection($db_conn)->select("
        SELECT t4.id,t4.site_code,t4.site_name,t1.`ordm_ornm`AS ordm_ornm,t1.`ordm_date`AS ordm_date,t2.id AS status_id,t2.lfcl_name AS status,
round(t1.`ordm_amnt`,3)AS ordm_amt,
round(t3.DELV_AMNT,3)AS sales_amt,1 AS type_order
FROM `tt_ordm`t1 JOIN tm_lfcl t2 ON(t1.`lfcl_id`=t2.id)
LEFT JOIN dm_trip_master t3 ON(t1.`ordm_ornm`=t3.ORDM_ORNM)
JOIN tm_site t4 ON(t1.`site_id`=t4.id)
WHERE t1.`site_id`=$site_id AND t1.`aemp_id`=$emp_id AND t1.slgp_id=$slgp_id
AND t1.`ordm_date`BETWEEN '$from_date' AND '$to_date'
UNION ALL
SELECT t4.id,t4.site_code,t4.site_name,t1.`rtan_rtnm`AS ordm_ornm,t1.`rtan_date`AS ordm_date,t2.id AS status_id,t2.lfcl_name AS status,
round(t1.`rtan_amnt`,3)AS ordm_amt,
round(t3.COLLECTION_AMNT,3)AS sales_amt,2 AS type_order
FROM `tt_rtan`t1 JOIN tm_lfcl t2 ON(t1.`lfcl_id`=t2.id)
LEFT JOIN dm_collection t3 ON(t1.`rtan_rtnm`=t3.COLL_NUMBER)
JOIN tm_site t4 ON(t1.`site_id`=t4.id)
WHERE t1.`site_id`=$site_id AND t1.`aemp_id`=$emp_id AND t1.slgp_id=$slgp_id
AND t1.`rtan_date`BETWEEN '$from_date' AND '$to_date';
       ");

            foreach ($order_m as $index => $data1) {
                $order_code = $data1->ordm_ornm;
                $data2 = DB::connection($db_conn)->select("
                SELECT t2.ordm_ornm AS ordm_ornm,t3.amim_code,t3.amim_name,t1.`amim_id`,
                t1.`ordd_qnty`AS qty,t1.`ordd_opds`,t1.`ordd_spdo`,t1.`ordd_dfdo`,round(t1.`ordd_oamt`,3)AS amt
FROM tt_ordd t1 JOIN tt_ordm t2 ON(t1.ordm_ornm=t2.ordm_ornm)
JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
WHERE t2.ordm_ornm='$order_code'
UNION ALL
SELECT t2.rtan_rtnm AS ordm_ornm,t3.amim_code,t3.amim_name,t1.`amim_id`,t1.`rtdd_qnty`AS qty,
0 AS `ordd_opds`,0 AS `ordd_spdo`,0 AS`ordd_dfdo`,round(t1.`rtdd_oamt`,3)AS amt
FROM tt_rtdd t1 JOIN tt_rtan t2 ON(t1.rtdd_rtan=t2.rtan_rtnm)
JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
WHERE t2.rtan_rtnm='$order_code';
               ");

                $order_m[$index]->orderIdLists = $data2;
            }
            return $order_m;
        }
    }

    public function dmSiteWiseCollectionDetailsData_role(Request $request)
    {

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
			$site_code = $request->site_code;
            $site_id = $request->site_id;
            $slgp_id = $request->slgp_id;
            $role_id = $request->role_id;
            $dm_code = $request->dm_code;
            if ($role_id == 10) { //DM
			
                if ($request->country_id == 3){
					
				$dm_code = $request->dm_code;
					 
				$tm_aemp = DB::connection($db_conn)->table('tm_aemp')->where('aemp_usnm', $dm_code)->first();
				   if ($tm_aemp->edsg_id == 14) { 
				   
				     $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
            where t9.mother_site_code=(SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
            AND t5.optp_id IN(1,2) and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
            UNION ALL
                                SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
                                round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
                                case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
                                FROM dm_trip_master t1
                                JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
                                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
                                JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
                                left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
                                left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
                                LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
                                where t9.mother_site_code not in (SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
                                and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11 and t3.`site_code`=$site_code and t1.slgp_id=$slgp_id
                                and t6.lfcl_id IN(39,11)
								
					UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
           round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0 					
			AND t1.slgp_id = $slgp_id			
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t5.optp_id=2 AND
            t1.slgp_id=$slgp_id AND      
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 and t1.slgp_id=$slgp_id
            
           ");
			
			}else{
				
				 
				  $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11) AND t1.`DM_CODE`='$dm_code'
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)AND t1.COLLECTION_TYPE!='DN' AND t1.`DM_CODE`='$dm_code'
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 AND t1.`DM_CODE`='$dm_code'		            
           ");
				}
			 }else{
			 
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
			UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>1 					
			AND t1.slgp_id = $slgp_id	
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)AND t1.COLLECTION_TYPE!='DN'
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0	
          		
           ");
			  }
            } elseif ($role_id == 1) {
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id AND t5.optp_id IN(1,2) and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
			 UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>1 					
			AND t1.slgp_id = $slgp_id
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND           
            t1.slgp_id=$slgp_id AND      
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 AND t1.slgp_id = $slgp_id
            
           ");
            } else {
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
			UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
           round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>1 						
			AND t1.slgp_id = $slgp_id
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t1.slgp_id=$slgp_id AND 
           t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)AND t1.COLLECTION_TYPE ='DN' 
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 AND t1.slgp_id = $slgp_id
             
           ");
            }
            /*$Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
            round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,if(t1.`COLLECTION_AMNT`>0,t1.`COLLECTION_AMNT`,0) AS Coll_Amt,1 AS Type,IF(t5.sapr_amnt>0, t5.sapr_amnt,0)AS cr_amt
            FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            LEFT JOIN tl_cpcr t5 ON(t1.SITE_ID=t5.site_id) AND t1.`ORDM_ORNM`=t5.ordm_ornm
            WHERE t1.`SITE_ID`=$site_id
            AND t1.`DELIVERY_STATUS`=11
            AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.COLLECTION_AMNT AS Due_Amt,t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type,0 AS cr_amt
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.SITE_ID=$site_id AND
            t1.STATUS=11 AND
            t1.STATUS!=26 AND
            t1.INVT_ID=5;
           ");*/


            return $Coll_Data;
        }

    }

    public function groupPartyCollectionDetailsData_role(Request $request)
    {


        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_code = $request->site_code;
            $site_id = $request->site_id;
            $slgp_id = $request->slgp_id;
            $role_id = $request->role_id;
            if ($role_id == 10) { //DM
                
                if ($request->country_id == 3){
					
				$dm_code = $request->dm_code;
					 
				$tm_aemp = DB::connection($db_conn)->table('tm_aemp')->where('aemp_usnm', $dm_code)->first();
				   if ($tm_aemp->edsg_id == 14) { 
				   
				     $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
            where t9.mother_site_code=(SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
            AND t5.optp_id IN(1,2) and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
            UNION ALL
                                SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
                                round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
                                case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
                                FROM dm_trip_master t1
                                JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
                                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
                                JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
                                left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
                                left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
                                LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
                                where t9.mother_site_code not in (SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
                                and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11 and t3.`site_code`=$site_code and t1.slgp_id=$slgp_id
                                and t6.lfcl_id IN(39,11)
								
					UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
           round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0 					
			AND t1.slgp_id = $slgp_id			
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t5.optp_id=2 AND
            t1.slgp_id=$slgp_id AND      
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 and t1.slgp_id=$slgp_id
            
           ");
			
			}else{
				
				 
				  $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11) AND t1.`DM_CODE`='$dm_code'
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)AND t1.COLLECTION_TYPE!='DN' AND t1.`DM_CODE`='$dm_code'
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 AND t1.`DM_CODE`='$dm_code'		            
           ");
				}
			}else{
			 
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
			UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0 					
			AND t1.slgp_id = $slgp_id	
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)AND t1.COLLECTION_TYPE!='DN'
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0	
            		
           ");
			  }	

            } elseif ($role_id == 1) {
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
            where t9.mother_site_code=(SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
            AND t5.optp_id IN(1,2) and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
            UNION ALL
                                SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
                                round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
                                case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
                                FROM dm_trip_master t1
                                JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
                                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
                                JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
                                left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
                                left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
                                LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
                                where t9.mother_site_code not in (SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
                                and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11 and t3.`site_code`=$site_code and t1.slgp_id=$slgp_id
                                and t6.lfcl_id IN(39,11)
				UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
           round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>1 					
			AND t1.slgp_id = $slgp_id				
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t5.optp_id=2 AND
            t1.slgp_id=$slgp_id AND      
            t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5)
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 and t1.slgp_id=$slgp_id
            
           ");
            } else {
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm
            LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code  
            where t9.mother_site_code=(SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code)  
            and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id IN(39,11)
            UNION ALL
                                SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
                                t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
                                round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
                                case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
                                case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus ,t1.ORDM_DRDT AS del_date
                                FROM dm_trip_master t1
                                JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
                                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
                                JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
                                left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
                                left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
                                LEFT JOIN tl_site_party_mapping t9 ON t1.`SITE_CODE`=t9.site_code 
                                where t9.mother_site_code not in (SELECT `mother_site_code` FROM `tl_site_party_mapping` WHERE `site_code`=$site_code) 
                                and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11 and t3.`site_code`=$site_code
                                and t6.lfcl_id IN(39,11)and t1.slgp_id=$slgp_id
				UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,t1.`INVT_ID` AS t_Type,t5.optp_id as p_type_id,
           round((t1.COLLECTION_AMNT - t1.COLL_REC_HO),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id 
			AND t1.INVT_ID IN(15)
			AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>1 					
			AND t1.slgp_id = $slgp_id												
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t1.slgp_id=$slgp_id AND 
           t1.STATUS IN(11) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(5) AND t1.COLLECTION_TYPE ='DN'
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,19 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus ,t1.COLL_DATE AS del_date
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = t1.slgp_id)
            WHERE t1.SITE_ID=$site_id AND          
            t1.STATUS IN(40) AND 
            t1.STATUS!=26 AND
            t1.INVT_ID IN(19)AND 
            t1.COLL_REC_HO=0 and t1.slgp_id=$slgp_id
            
           ");
            }

            return $Coll_Data;
        }

    }

    public function dmSiteWiseCollectionDetailsData(Request $request)
    {

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $slgp_id = $request->slgp_id;

            $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t4.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t3.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,t1.slgp_id, 1 AS t_Type, t5.optp_id as p_type_id,
            round((t1.DELV_AMNT-t1.COLLECTION_AMNT),4)AS DueAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sreq_amnt,4),0)  else 0 end  AS reqAmnt,
            case when t1.COLLECTION_AMNT=0 then IFNULL(round(t2.sapr_amnt,4),0)  else 0 end AS apprv,
            case when (t1.COLLECTION_AMNT>0 and t2.sapr_amnt>0) then 1  else 0 end CredStatus
            FROM dm_trip_master t1
            JOIN tm_site t3 ON(t1.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
            JOIN tl_stcm t5 ON(t3.id=t5.site_id AND t5.slgp_id = $slgp_id)
            left join tl_cpcr t2 on t1.SITE_ID=t2.site_id and t1.ORDM_ORNM=t2.ordm_ornm
            left join tt_ordm t6 on t1.ORDM_ORNM=t6.ordm_ornm 
            where t1.`SITE_ID`=$site_id and t1.slgp_id=$slgp_id and t1.DELV_AMNT-t1.COLLECTION_AMNT>0.1 and t1.DELIVERY_STATUS=11
            and t6.lfcl_id = 11
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.slgp_id,2 AS t_Type,t5.optp_id as p_type_id,
            round((t1.COLLECTION_AMNT),4) AS DueAmnt,
            0 reqAmnt,0 apprv,8 CredStatus 
            FROM `dm_collection`t1
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            JOIN tl_stcm t5 ON(t2.id=t5.site_id AND t5.slgp_id = $slgp_id)
            WHERE t1.SITE_ID=$site_id AND 
            t1.slgp_id=$slgp_id AND 
            t1.STATUS=11 AND 
            t1.STATUS!=26 AND
            t1.INVT_ID=5
           ");

            /*$Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
            round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,if(t1.`COLLECTION_AMNT`>0,t1.`COLLECTION_AMNT`,0) AS Coll_Amt,1 AS Type,IF(t5.sapr_amnt>0, t5.sapr_amnt,0)AS cr_amt
            FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            LEFT JOIN tl_cpcr t5 ON(t1.SITE_ID=t5.site_id) AND t1.`ORDM_ORNM`=t5.ordm_ornm
            WHERE t1.`SITE_ID`=$site_id
            AND t1.`DELIVERY_STATUS`=11
            AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`,t1.COLLECTION_AMNT AS Due_Amt,t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type,0 AS cr_amt
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.SITE_ID=$site_id AND
            t1.STATUS=11 AND
            t1.STATUS!=26 AND
            t1.INVT_ID=5;
           ");*/


            return $Coll_Data;
        }


    }


    public function CollectionDetailsData(Request $request)
    {

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $emp_id = $request->emp_id;
            $emp_code = $request->emp_code;
            $role_id = $request->role_id;

            if ($role_id == '10') {
                //DM				
				 if ($request->country_id == 3) {
					 $dm_code = $request->emp_code;					 
				$tm_aemp = DB::connection($db_conn)->table('tm_aemp')->where('aemp_usnm', $dm_code)->first();
				   if ($tm_aemp->edsg_id == 14) { 
				    $Coll_Data = DB::connection($db_conn)->select("
                                SELECT t1.`TRIP_NO`,t6.slgp_id,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,
                                t2.AEMP_USNM,t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, 
                                count(distinct t2.ORDM_ORNM) totalINv,
                                if(t2.TRIP_STATUS='D',1,'0') Delin,t5.optp_id as p_type_id                       
                                FROM `dm_trip`t1 
                                JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
                                JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
                                JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
                                JOIN dm_trip_master as t6 ON (t2.SITE_ID= t6.SITE_ID)
                                join tl_stcm t5 ON(t5.site_id=t3.id)AND t5.slgp_id=t6.slgp_id
                                WHERE t1.`DM_ID`='$emp_code' AND (t6.`DELV_AMNT`-t6.`COLLECTION_AMNT`)>0.1
                                and t6.DELIVERY_STATUS IN(39,11)
                                GROUP by t2.SITE_ID,t1.`TRIP_NO`,t6.slgp_id,
                                t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,
                                t3.geo_lon,t2.TRIP_STATUS,t5.optp_id
                                    UNION ALL 
                                    SELECT ''AS `TRIP_NO`,t1.`slgp_id`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
                                    t1.AEMP_ID AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.COLL_NUMBER) totalINv,0 AS Delin,t3.optp_id as p_type_id
                                    FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
                                    JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
                                    JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)AND t4.id=$emp_id
                                    JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
                                    JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
                                    WHERE t4.id=$emp_id AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0 AND t1.INVT_ID=5
                                    GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
                                    t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.COLL_NUMBER,t3.optp_id
                                    UNION ALL 
                                    SELECT '' AS`TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,
                                    t1.`AEMP_USNM`,t1.`AEMP_ID` AS emp_id,'' AS aemp_name,0 AS geo_lat,0 AS geo_lon, 
                                    1 AS totalINv,1 AS Delin,
                                    t5.optp_id as p_type_id 
                                    FROM dm_collection t1
                                    JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
                                    join tl_stcm t5 ON(t5.site_id=t1.`SITE_ID`)AND t5.slgp_id=t1.slgp_id
                                    WHERE t1.INVT_ID=15 AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0.1 AND t1.`AEMP_ID`=$emp_id
                                    GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1,
                                    t1.`AEMP_USNM`,t1.`AEMP_ID`,t5.optp_id;
                                ");
				   
				}else{
				
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`TRIP_NO`,t6.slgp_id,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,
            t2.AEMP_USNM,t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, 
            count(distinct t2.ORDM_ORNM) totalINv,
            if(t2.TRIP_STATUS='D',1,'0') Delin,t5.optp_id as p_type_id                       
             FROM `dm_trip`t1 
            JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
            JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
            JOIN dm_trip_master as t6 ON (t2.SITE_ID= t6.SITE_ID)
            join tl_stcm t5 ON(t5.site_id=t3.id)AND t5.slgp_id=t6.slgp_id
            WHERE t1.`DM_ID`='$emp_code' AND (t6.`DELV_AMNT`-t6.`COLLECTION_AMNT`)>0.1
            and t6.DELIVERY_STATUS IN(39,11)
            GROUP by t2.SITE_ID,t1.`TRIP_NO`,t6.slgp_id,
			t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,
			t3.geo_lon,t2.TRIP_STATUS,t5.optp_id
			 ");
			}					 
				}else{
						 $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`TRIP_NO`,t6.slgp_id,t2.SITE_ID,t2.SITE_CODE,t3.site_name,t3.site_mob1 AS site_mobile,
            t2.AEMP_USNM,t4.id AS emp_id,t4.aemp_name,t3.geo_lat,t3.geo_lon, 
            count(distinct t2.ORDM_ORNM) totalINv,
            if(t2.TRIP_STATUS='D',1,'0') Delin,t5.optp_id as p_type_id                       
             FROM `dm_trip`t1 
            JOIN dm_trip_detail t2 ON(t1.`TRIP_NO`=t2.TRIP_NO)
            JOIN tm_site t3 ON(t2.SITE_ID=t3.id)
            JOIN tm_aemp t4 ON(t2.AEMP_USNM=t4.aemp_usnm)
            JOIN dm_trip_master as t6 ON (t2.SITE_ID= t6.SITE_ID)
            join tl_stcm t5 ON(t5.site_id=t3.id)AND t5.slgp_id=t6.slgp_id
            WHERE t1.`DM_ID`='$emp_code' AND (t6.`DELV_AMNT`-t6.`COLLECTION_AMNT`)>0.1
            and t6.DELIVERY_STATUS IN(39,11)
            GROUP by t2.SITE_ID,t1.`TRIP_NO`,t6.slgp_id,
			t2.SITE_CODE,t2.AEMP_USNM,t4.id,t3.geo_lat,
			t3.geo_lon,t2.TRIP_STATUS,t5.optp_id
			 UNION ALL 
                SELECT ''AS `TRIP_NO`,t1.`slgp_id`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
                t1.AEMP_ID AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.COLL_NUMBER) totalINv,0 AS Delin,t3.optp_id as p_type_id
                FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
                JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)AND t4.id=$emp_id
                JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
                JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
                WHERE t4.id=$emp_id AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0 AND t1.INVT_ID=5
                GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
                t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.COLL_NUMBER,t3.optp_id
                UNION ALL 
                SELECT '' AS`TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,
                t1.`AEMP_USNM`,t1.`AEMP_ID` AS emp_id,'' AS aemp_name,0 AS geo_lat,0 AS geo_lon, 
                1 AS totalINv,1 AS Delin,
                t5.optp_id as p_type_id 
                FROM dm_collection t1
                JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
                join tl_stcm t5 ON(t5.site_id=t1.`SITE_ID`)AND t5.slgp_id=t1.slgp_id
                WHERE t1.INVT_ID=15 AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0.1 AND t1.`AEMP_ID`=$emp_id
				GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1,
                t1.`AEMP_USNM`,t1.`AEMP_ID`,t5.optp_id;
			 "); 
				}	
            } elseif ($role_id == '1') {
                //SR
                $Coll_Data = DB::connection($db_conn)->select("
                SELECT ''AS `TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
                t4.id AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.ORDM_ORNM) totalINv,t3.optp_id as p_type_id,0 AS Delin
                FROM `dm_trip_master`t1 
                JOIN tm_site t2 ON(t1.SITE_ID=t2.id) 
                JOIN tl_stcm t3 ON(t3.site_id=t2.id) AND t3.slgp_id=t1.slgp_id
                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)
                JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
                JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
                WHERE t1.`AEMP_ID`=$emp_id AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0.1
                and t1.DELIVERY_STATUS IN(39,11)
                GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
                t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.ORDM_ORNM,t3.optp_id
                UNION ALL 
                SELECT ''AS `TRIP_NO`,t1.`slgp_id`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
                t1.AEMP_ID AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.COLL_NUMBER) totalINv,t3.optp_id as p_type_id,0 AS Delin
                FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
                JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
                JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)AND t4.id=$emp_id
                JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
                JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
                WHERE t4.id=$emp_id AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0 AND t1.INVT_ID=5
                GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
                t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.COLL_NUMBER,t3.optp_id
                UNION ALL 
                SELECT '' AS`TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,
                t1.`AEMP_USNM`,t1.`AEMP_ID` AS emp_id,'' AS aemp_name,0 AS geo_lat,0 AS geo_lon, 
                1 AS totalINv,
                t5.optp_id as p_type_id ,1 AS Delin
                FROM dm_collection t1
                JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
                join tl_stcm t5 ON(t5.site_id=t1.`SITE_ID`)AND t5.slgp_id=t1.slgp_id
                WHERE t1.INVT_ID=15 AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO)>0.1 AND t1.`AEMP_ID`=$emp_id
				GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1,
                t1.`AEMP_USNM`,t1.`AEMP_ID`,t5.optp_id;
                ");
            } elseif ($role_id == '2') {
                // TSM
                $Coll_Data = DB::connection($db_conn)->select("
            SELECT ''AS `TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
            t4.id AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.ORDM_ORNM) totalINv,t3.optp_id as p_type_id,1 AS Delin
            FROM `dm_trip_master`t1 
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id) 
            JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)AND t4.aemp_mngr=$emp_id
            JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
            JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
            WHERE t4.aemp_mngr=$emp_id AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0.1
            and t1.DELIVERY_STATUS IN(39,11)
            GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
            t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.ORDM_ORNM,t3.optp_id
            UNION ALL 
            SELECT ''AS `TRIP_NO`,t1.`slgp_id`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
            t1.AEMP_ID AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.COLL_NUMBER) totalINv,t3.optp_id as p_type_id,1 AS Delin
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
            JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)AND t4.aemp_mngr=$emp_id
            JOIN tl_rpln t5 ON(t4.id=t5.aemp_id)
            JOIN tl_rsmp t6 ON(t6.rout_id=t5.rout_id) AND t6.site_id=t1.`SITE_ID`
            WHERE t4.aemp_mngr=$emp_id AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0 AND t1.INVT_ID=5
            GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
            t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.COLL_NUMBER,t3.optp_id
            UNION ALL 
            SELECT '' AS`TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,
            t1.`AEMP_USNM`,t1.`AEMP_ID` AS emp_id,'' AS aemp_name,0 AS geo_lat,0 AS geo_lon, 
            1 AS totalINv,
            t5.optp_id as p_type_id ,1 AS Delin
            FROM dm_collection t1
            JOIN tm_site t2 ON t1.`SITE_ID`=t2.id
            join tl_stcm t5 ON(t5.site_id=t1.`SITE_ID`)AND t5.slgp_id=t1.slgp_id
            WHERE t1.INVT_ID=15 AND(t1.COLLECTION_AMNT - t1.COLL_REC_HO) >0.1 AND t1.`AEMP_ID`=$emp_id
			GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1,
            t1.`AEMP_USNM`,t1.`AEMP_ID`,t5.optp_id
			UNION ALL
            SELECT ''AS `TRIP_NO`,t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 AS site_mobile,t1.AEMP_USNM,
            t4.id AS emp_id,t4.aemp_name,t2.geo_lat,t2.geo_lon, count(distinct t1.ORDM_ORNM) totalINv,t3.optp_id as p_type_id,1 AS Delin
            FROM `dm_trip_master`t1 
            JOIN tm_site t2 ON(t1.SITE_ID=t2.id) 
            JOIN tl_stcm t3 ON(t3.site_id=t2.id)AND t3.slgp_id=t1.slgp_id
            JOIN tm_aemp t4 ON(t1.AEMP_ID=t4.id)       
            WHERE t1.AEMP_ID=$emp_id AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            and t1.DELIVERY_STATUS IN(39,11)
            GROUP BY t1.slgp_id,t1.SITE_ID,t1.SITE_CODE,t2.site_name,t2.site_mob1 ,t1.AEMP_USNM,
            t4.id ,t4.aemp_name,t2.geo_lat,t2.geo_lon, t1.ORDM_ORNM,t3.optp_id;			
            ");
            }

            return $Coll_Data;
        }
    }

    public function SRSiteWiseCollectionListData(Request $request)
    {

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $emp_id = $request->emp_id;
            // $trip_code = $request->trip_code;

            $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,COUNT(`ORDM_ORNM`)AS InVoice_Qty
            FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.`AEMP_ID`=$emp_id
            AND t1.`DELIVERY_STATUS`=11 
            AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            GROUP BY t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name  
           ");
            return $Coll_Data;
        }
    }

    public function SRSiteWiseCollectionDetailsData(Request $request)
    {

        $Coll_Data = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;

            $Coll_Data = DB::connection($db_conn)->select("
            SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
            round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,0 AS Coll_Amt,1 AS Type
            FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.`SITE_ID`=$site_id 
            AND t1.`DELIVERY_STATUS`=11 
            AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
            UNION ALL
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`, round((t1.COLLECTION_AMNT-t1.COLL_REC_HO),4)AS Due_Amt,
            t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.SITE_ID=$site_id 
            AND t1.STATUS=11 
            AND t1.COLL_REC_HO!=t1.COLLECTION_AMNT
            AND t1.INVT_ID=5 
            UNION ALL 
            SELECT t1.`ACMP_CODE`,
            t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
            t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
            t1.`IBS_INVOICE`, round((t1.COLLECTION_AMNT-t1.COLL_REC_HO),4)AS Due_Amt,
            t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type
            FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
            JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
            WHERE t1.SITE_ID=$site_id
            AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0 
            AND t1.INVT_ID=5 AND t1.COLLECTION_TYPE='OC';
           ");

            /* $Coll_Data = DB::connection($db_conn)->select("
             SELECT t1.`ACMP_CODE`,t1.`ORDM_ORNM`,t1.`AEMP_ID`,t1.`AEMP_USNM`,t3.aemp_name,
             t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,t1.`IBS_INVOICE`,
             round((t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`),4)AS Due_Amt,0 AS Coll_Amt,1 AS Type
             FROM `dm_trip_master`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
             JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
             WHERE t1.`SITE_ID`=$site_id
             AND t1.`DELIVERY_STATUS`=11
             AND (t1.`DELV_AMNT`-t1.`COLLECTION_AMNT`)>0
             UNION ALL
             SELECT t1.`ACMP_CODE`,
             t1.COLL_NUMBER AS ORDM_ORNM,t1.AEMP_ID,t1.`AEMP_USNM`,t3.aemp_name,
             t1.`WH_ID`,t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`DM_CODE`,
             t1.`IBS_INVOICE`, round((t1.COLLECTION_AMNT-t1.COLL_REC_HO),4)AS Due_Amt,
             t1.COLLECTION_AMNT AS Coll_Amt,2 AS Type
             FROM `dm_collection`t1 JOIN tm_site t2 ON(t1.SITE_ID=t2.id)
             JOIN tm_aemp t3 ON(t1.AEMP_ID=t3.id)
             WHERE t1.SITE_ID=$site_id
             AND (t1.`COLL_REC_HO`-t1.`COLLECTION_AMNT`)>0
             AND t1.INVT_ID=5
            ");*/
            return $Coll_Data;
        }
    }

    public function dmItemWiseTripDetails(Request $request)
    {

        $trip_m = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            // $site_id = $request->site_id;
            $trip_code = $request->trip_code;

            $trip_m = DB::connection($db_conn)->select("
        SELECT t2.amim_name,t1.`AMIM_ID`,t1.`AMIM_CODE`,SUM(t1.`INV_QNTY`)AS INV_QNTY,
        SUM(t1.`DELV_QNTY`)AS DELV_QNTY,(SUM(t1.`INV_QNTY`)-SUM(t1.`DELV_QNTY`))AS Avail_Qty,
        1 AS Type 
        FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id) 
        WHERE t1.`TRIP_NO`='$trip_code'
         GROUP BY t1.`AMIM_ID`,t1.`AMIM_CODE` 
        UNION ALL
        SELECT t3.amim_name,t2.amim_id,t3.amim_code,0 AS INV_QNTY ,
        0 AS DELV_QNTY,SUM(t2.rtdd_dqty) AS Avail_Qty,
        2 AS Type 
        FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
         JOIN tm_amim t3 ON(t2.amim_id=t3.id) 
         WHERE t1.`dm_trip`='$trip_code'
         AND t2.rtdd_dqty!=0 
         GROUP BY t3.amim_name,t2.amim_id,t3.amim_code      
       ");
            return $trip_m;
        }

    }

    public function dmTripSummeryReport(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $trip_code = $request->trip_code;

            $totalInvoiceAmount = 0;
            $totalDeliveryAmount = 0;
            $totalCollectionAmount = 0;

            $sql = DB::connection($db_conn)->select("            
                SELECT t1.SITE_ID AS siteId ,SUM(t1.INV_AMNT) AS invoiceAmount, SUM(t1.DELV_AMNT) AS deliveryAmount , SUM(t1.COLLECTION_AMNT) AS collectionAmount FROM `dm_trip_master` AS t1 
                WHERE t1.TRIP_NO = '$trip_code'
                GROUP BY t1.SITE_ID;         
            ");

            foreach ($sql as $index => $data) {
                $totalInvoiceAmount += $data->invoiceAmount;
                $totalDeliveryAmount += $data->deliveryAmount;
                $totalCollectionAmount += $data->collectionAmount;
            }

            return array(
                'totalInvoiceAmount' => $totalInvoiceAmount,
                'totalDeliveryAmount' => $totalDeliveryAmount,
                'totalCollectionAmount' => $totalCollectionAmount
            );

        }
    }

    public function dmSiteWiseOrderDetails(Request $request)
    {


        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $site_id = $request->site_id;
            $trip_code = $request->trip_code;

            /* $trip_m = DB::connection($db_conn)->select("
         SELECT t3.id AS tpm_id,1 AS Type,t1.`TRIP_STATUS`AS Status,t1.`ORDM_ORNM`,t2.site_code,t2.site_name
         FROM `dm_trip_detail`t1 JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
         JOIN dm_trip t3 ON (t1.TRIP_NO=t3.TRIP_NO)
         WHERE t1.`TRIP_NO`='$trip_code'
         AND t1.`SITE_ID`= $site_id
         GROUP BY t1.`ORDM_ORNM`,t1.`TRIP_STATUS`
         UNION ALL
         SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name
         FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
         JOIN tm_site t3 ON(t1.`site_id`=t3.id)
         WHERE t1.`site_id`=$site_id AND t1.`lfcl_id`=1 AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
         GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`
         UNION ALL
         SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name
         FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
         JOIN tm_site t3 ON(t1.`site_id`=t3.id)
         WHERE t1.`site_id`=$site_id AND t1.dm_trip='$trip_code' AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
         GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`
        ");*/

            $trip_m = DB::connection($db_conn)->select("
            SELECT t3.id AS tpm_id,1 AS Type,t1.`TRIP_STATUS`AS Status,t1.`ORDM_ORNM`,t2.site_code,t2.site_name,t4.slgp_id,
            if(SUM(t5.CRECIT_AMNT)>0,1,0)AS coll_status                                                                        
            FROM `dm_trip_master`t4 JOIN dm_trip_detail t1 ON t1.SITE_ID=t4.SITE_ID and t1.ORDM_ORNM=t4.ORDM_ORNM
            JOIN tm_site t2 ON(t1.`SITE_ID`=t2.id)
            JOIN dm_trip t3 ON (t1.TRIP_NO=t3.TRIP_NO)
            LEFT JOIN dm_invoice_collection_mapp t5 ON t1.ORDM_ORNM=t5.TRANSACTION_ID
            WHERE t1.`TRIP_NO`='$trip_code'
            AND t1.`SITE_ID`= '$site_id' 
            GROUP BY t1.`ORDM_ORNM`,t1.`TRIP_STATUS`,t4.slgp_id
        UNION ALL
        SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name,t1.slgp_id,0 AS coll_status
        FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
        JOIN tm_site t3 ON(t1.`site_id`=t3.id)
        WHERE t1.`site_id`=$site_id AND t1.`lfcl_id`=1 AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
        GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`,t1.slgp_id
        UNION ALL
        SELECT t1.`id` AS tpm_id,2 AS Type,t1.`lfcl_id`AS Status,t1.`rtan_rtnm` AS ORDM_ORNM,t3.site_code,t3.site_name,t1.slgp_id,0 AS coll_status
        FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
        JOIN tm_site t3 ON(t1.`site_id`=t3.id)
        WHERE t1.`site_id`=$site_id AND t1.dm_trip='$trip_code' AND t1.rtan_date>= SUBDATE(DATE(NOW()), 7)
        GROUP BY t1.`rtan_rtnm`,t1.`lfcl_id`,t1.slgp_id;
       ");

            foreach ($trip_m as $index => $data1) {
                $order_code = $data1->ORDM_ORNM;
                $data2 = DB::connection($db_conn)->select("
                SELECT t1.`OID`AS trp_line,
t1.`ORDM_ORNM`,
t1.`ORDM_DRDT`,
t1.`AMIM_ID`,
t1.`AMIM_CODE`,
t2.amim_name AS Item_Name,
t1.ORDD_UPRC AS Rate,
t1.ORDD_EXCS AS EXCS_Percent,
t1.ORDD_OVAT AS VAT_Percent,
t1.ORDD_OAMT AS totalOrderAmount,
t1.`prom_id`,
t1.`ORDD_QNTY`,
t1.`INV_QNTY`,
t1.`DELV_QNTY`,
t1.DISCOUNT,
t2.amim_duft AS Item_Factor
FROM `dm_trip_detail`t1 JOIN tm_amim t2 ON(t1.`AMIM_ID`=t2.id)
WHERE t1.`ORDM_ORNM`='$order_code' AND t1.`INV_QNTY`!=0
GROUP BY t1.`OID`,t1.`AMIM_ID`
UNION ALL 
SELECT t2.id AS trp_line,
t1.rtan_rtnm AS ORDM_ORNM,
t2.`rtdd_edat` AS ORDM_DRDT,
t2.amim_id AS AMIM_ID,
t3.amim_code AS AMIM_CODE,
t3.amim_name AS Item_Name,
t2.rtdd_uprc AS Rate,
t2.rtdd_excs AS EXCS_Percent,
t2.rtdd_ovat AS VAT_Percent,
t2.rtdd_oamt AS totalOrderAmount,
0 AS prom_id,
t2.rtdd_qnty AS ORDD_QNTY,
t2.rtdd_qnty AS INV_QNTY,
t2.rtdd_dqty AS DELV_QNTY,
0 AS DISCOUNT,
t3.amim_duft AS Item_Factor  
FROM `tt_rtan`t1 JOIN tt_rtdd t2 ON(t1.id=t2.rtan_id)
JOIN tm_amim t3 ON(t2.amim_id=t3.id)
WHERE t1.`rtan_rtnm`='$order_code'
GROUP BY t2.id,t2.amim_id
 ");

                $trip_m[$index]->orderIdLists = $data2;
            }
            return $trip_m;
        }
    }

    public function outletSave1(Request $request)
    {
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                $country = Country::on($db_conn)->findorfail($outletData->country_id);
                $site_code = 'N' . str_pad($country->attr4 + 1, 9, '0', STR_PAD_LEFT);
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
                    $site->site_imge = '';
                    $site->site_omge = '';
                    $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                    $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    $site->site_reg = '';
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
                    $tempSite->nsit_imge = '';
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
                    //throw $e;
                }

            }
        }

    }

    public function updateOutletSerial(Request $request)
    {
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
                    //throw $e;
                }

            }
        }
    }

    public function SubmitChallanWiseDelivery(Request $request)
    {
        $outletData = json_decode($request->delivery_data_challan_wise)[0];
        $outletData_child = json_decode($request->delivery_data_challan_wise);
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();

                try {

                    $Trip_master1 = new Trip();
                    $Trip_master1->setConnection($db_conn);

                    $Trip_master1->trip_otid = 0;
                    $Trip_master1->aemp_tusr = $outletData_child[0]->SR_ID;
                    $Trip_master1->trip_code = '';
                    $Trip_master1->trip_date = date('Y-m-d');
                    $Trip_master1->trip_vdat = date('Y-m-d');
                    $Trip_master1->trip_ldat = date('Y-m-d');
                    $Trip_master1->trip_cdat = date('Y-m-d');
                    $Trip_master1->aemp_vusr = 0;
                    $Trip_master1->aemp_lusr = 0;
                    $Trip_master1->aemp_cusr = 0;
                    $Trip_master1->dpot_id = 1;
                    $Trip_master1->dlrm_id = $outletData_child[0]->Dealer_ID;
                    $Trip_master1->vhcl_id = 1;
                    $Trip_master1->ttyp_id = 1;
                    $Trip_master1->lfcl_id = 25;
                    $Trip_master1->cont_id = $outletData_child[0]->country_id;
                    $Trip_master1->aemp_iusr = $request->up_emp_id;
                    $Trip_master1->aemp_eusr = $request->up_emp_id;
                    $Trip_master1->save();

                    foreach ($outletData_child as $orderLineData) {

                        $orderLine = new TripSku();
                        $orderLine->setConnection($db_conn);
                        $orderLine->trip_id = $Trip_master1->id;
                        $orderLine->amim_id = $orderLineData->P_ID;
                        $orderLine->troc_iqty = $orderLineData->Order_P_Qty;
                        $orderLine->troc_cqty = $orderLineData->Order_P_Qty;
                        $orderLine->troc_dqty = $orderLineData->Delivery_P_Qty;
                        $orderLine->troc_lqty = 0;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $orderLineData->country_id;
                        $orderLine->aemp_iusr = $request->up_emp_id;
                        $orderLine->aemp_eusr = $request->up_emp_id;
                        $orderLine->save();

                        $isFree_item1 = 0;
                        if ($orderLineData->P_Total_Price == 0) {
                            $isFree_item1 = 1;
                        }
                        $order_details = DB::connection($db_conn)->table('tt_ordm')->join('tt_ordd',
                            'tt_ordm.id', '=', 'tt_ordd.ordm_id')->where(['tt_ordm.aemp_id' => $orderLineData->SR_ID,
                            'tt_ordm.ordm_date' => $orderLineData->Order_Date, 'tt_ordm.lfcl_id' => 1, 'tt_ordd.ordd_smpl' => $isFree_item1, 'tt_ordd.amim_id' => $orderLineData->P_ID])
                            ->select('tt_ordd.id AS id', 'tt_ordd.ordd_qnty AS ordd_qnty',
                                'tt_ordd.ordd_opds AS ordd_opds', 'tt_ordd.ordd_uprc AS ordd_uprc',
                                'tt_ordd.ordd_smpl AS ordd_smpl')->orderBy('tt_ordd.id')->get();

                        $del_qty = 0;

                        foreach ($order_details as $order_details1) {


                            if ($order_details1->ordd_qnty < $orderLineData->Delivery_P_Qty - $del_qty) {
                                $dd_amt = 0;
                                $isFree_item = $order_details1->ordd_smpl;
                                if ($isFree_item == 1) {
                                    $dd_amt = 0;
                                } else {
                                    $dd_amt = ($order_details1->ordd_qnty * $order_details1->ordd_uprc) - $order_details1->ordd_opds;
                                }


                                DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                                ])->update(['ordd_dqty' => $order_details1->ordd_qnty, 'ordd_amnt' => $dd_amt, 'ordd_odat' => $order_details1->ordd_qnty * $order_details1->ordd_uprc]);
                                $del_qty = $del_qty + $order_details1->ordd_qnty;

                            } else {
                                $dd_amt = 0;
                                $isFree_item = $order_details1->ordd_smpl;
                                if ($isFree_item == 1) {
                                    $dd_amt = 0;
                                } else {
                                    $dd_amt = (($orderLineData->Delivery_P_Qty - $del_qty) * $order_details1->ordd_uprc) - $order_details1->ordd_opds;
                                }


                                DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                                ])->update(['ordd_dqty' => $orderLineData->Delivery_P_Qty - $del_qty, 'ordd_amnt' => $dd_amt, 'ordd_odat' => ($orderLineData->Delivery_P_Qty - $del_qty) * $order_details1->ordd_uprc]);

                                $del_qty = $del_qty + $orderLineData->Delivery_P_Qty - $del_qty;
                            }


                            DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id
                            ])->update(['ordd_amnt' => $dd_amt]);

                        }

                    }


                    $order_master = DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $outletData_child[0]->SR_ID,
                        'ordm_date' => $outletData_child[0]->Order_Date, 'lfcl_id' => 1])->get();

                    foreach ($order_master as $TripLineData) {
                        $Trip_master = new TripOrder();
                        $Trip_master->setConnection($db_conn);

                        $Trip_master->trip_id = $Trip_master1->id;
                        $Trip_master->ordm_id = $TripLineData->id;
                        $Trip_master->ondr_id = 0;
                        $Trip_master->lfcl_id = 11;
                        $Trip_master->cont_id = $outletData_child[0]->country_id;
                        $Trip_master->aemp_iusr = $request->up_emp_id;
                        $Trip_master->aemp_eusr = $request->up_emp_id;
                        $Trip_master->save();

                    }

                    $dl_date = date('Y-m-d');

                    DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $outletData_child[0]->SR_ID,
                        'ordm_date' => $outletData_child[0]->Order_Date, 'lfcl_id' => 1])->update(['lfcl_id' => 11, 'ordm_drdt' => $dl_date]);


                    DB::connection($db_conn)->commit();
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

    public function SubmitInvoiceWiseDelivery(Request $request)
    {
        $outletData = json_decode($request->invoice_wise_delivery)[0];
        $outletData_child = json_decode($request->invoice_wise_delivery);
        if ($outletData) {
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();

                try {

                    $Trip_master1 = new Trip();
                    $Trip_master1->setConnection($db_conn);

                    $Trip_master1->trip_otid = 0;
                    $Trip_master1->aemp_tusr = $request->SR_ID;
                    $Trip_master1->trip_code = '';
                    $Trip_master1->trip_date = date('Y-m-d');
                    $Trip_master1->trip_vdat = date('Y-m-d');
                    $Trip_master1->trip_ldat = date('Y-m-d');
                    $Trip_master1->trip_cdat = date('Y-m-d');
                    $Trip_master1->aemp_vusr = 0;
                    $Trip_master1->aemp_lusr = 0;
                    $Trip_master1->aemp_cusr = 0;
                    $Trip_master1->dpot_id = 1;
                    $Trip_master1->dlrm_id = $outletData_child[0]->dealerId;
                    $Trip_master1->vhcl_id = 1;
                    $Trip_master1->ttyp_id = 1;
                    $Trip_master1->lfcl_id = 25;
                    $Trip_master1->cont_id = $request->country_id;
                    $Trip_master1->aemp_iusr = $request->SR_ID;
                    $Trip_master1->aemp_eusr = $request->SR_ID;
                    $Trip_master1->save();

                    foreach ($outletData_child as $orderLineData) {

                        $orderLine = new TripSku();
                        $orderLine->setConnection($db_conn);
                        $orderLine->trip_id = $Trip_master1->id;
                        $orderLine->amim_id = $orderLineData->itemId;
                        $orderLine->troc_iqty = $orderLineData->orderQty;
                        $orderLine->troc_cqty = $orderLineData->orderQty;
                        $orderLine->troc_dqty = $orderLineData->deliveryQty;
                        $orderLine->troc_lqty = 0;
                        $orderLine->lfcl_id = 1;
                        $orderLine->cont_id = $request->country_id;
                        $orderLine->aemp_iusr = $request->SR_ID;
                        $orderLine->aemp_eusr = $request->SR_ID;
                        $orderLine->save();

                        /*$isFree_item1 = 0;
                        if ($orderLineData->P_Total_Price == 0) {
                            $isFree_item1 = 1;
                        }*/
                        $order_details = DB::connection($db_conn)->table('tt_ordm')->join('tt_ordd',
                            'tt_ordm.id', '=', 'tt_ordd.ordm_id')->where(['tt_ordm.ordm_date' => $request->order_date,
                            'tt_ordm.lfcl_id' => 1, 'tt_ordm.id' => $orderLineData->invoiceId])
                            ->select('tt_ordd.id AS id', 'tt_ordd.ordd_qnty AS ordd_qnty',
                                'tt_ordd.ordd_opds AS ordd_opds', 'tt_ordd.ordd_uprc AS ordd_uprc',
                                'tt_ordd.ordd_smpl AS ordd_smpl')->orderBy('tt_ordd.id')->get();

                        // foreach ($order_details as $order_details1) {
                        //  if ($order_details1->ordd_qnty < $orderLineData->Delivery_P_Qty) {
                        $dd_amt = 0;
                        //  $isFree_item = $order_details1->ordd_smpl;
                        //   if ($isFree_item == 1) {
                        if ($orderLineData->unitPrice < 1) {
                            $dd_amt = 0;
                        } else {
                            $dd_amt = ($orderLineData->deliveryQty * $orderLineData->unitPrice) - $orderLineData->promotionalDiscount;
                        }
                        // DB::connection($db_conn)->table('tt_ordd')->where(['id' => $order_details1->id, 'amim_id' => $orderLineData->itemId, 'ordd_uprc' => $orderLineData->unitPrice
                        DB::connection($db_conn)->table('tt_ordd')->where(['ordm_id' => $orderLineData->invoiceId, 'amim_id' => $orderLineData->itemId, 'ordd_uprc' => $orderLineData->unitPrice, 'ordd_qnty' => $orderLineData->orderQty
                        ])->update(['ordd_dqty' => $orderLineData->deliveryQty, 'ordd_amnt' => $dd_amt, 'ordd_opds' => $orderLineData->promotionalDiscount, 'ordd_odat' => $orderLineData->deliveryQty * $orderLineData->unitPrice]);


                        // }
                    }

                    $Trip_master = new TripOrder();
                    $Trip_master->setConnection($db_conn);

                    $Trip_master->trip_id = $Trip_master1->id;
                    $Trip_master->ordm_id = $outletData_child[0]->invoiceId;
                    $Trip_master->ondr_id = 0;
                    $Trip_master->lfcl_id = 11;
                    $Trip_master->cont_id = $request->country_id;
                    $Trip_master->aemp_iusr = $request->SR_ID;
                    $Trip_master->aemp_eusr = $request->SR_ID;
                    $Trip_master->save();
                    $dl_date = date('Y-m-d');
                    DB::connection($db_conn)->table('tt_ordm')->where(['id' => $outletData_child[0]->invoiceId,
                        'ordm_date' => $request->order_date, 'lfcl_id' => 1])->update(['lfcl_id' => 11, 'ordm_drdt' => $dl_date]);

                    DB::connection($db_conn)->commit();
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

    public function SubmitUseRewardPoint(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();

            try {
                $redM = RewardMaster::on($db_conn)->where(['aemp_id' => $request->emp_id])->first();

                $Red_details = new RewardsDetails();
                $Red_details->setConnection($db_conn);

                $Red_details->rwdm_id = $redM->id;
                $Red_details->aemp_id = $redM->aemp_id;
                $Red_details->rwdd_epnt = 0;
                $Red_details->rwdd_upnt = $request->use_point;
                $Red_details->kpim_id = 0;
                $Red_details->kfit_id = $request->kfit_id;
                $Red_details->rwdd_ptyp = 2;
                $Red_details->cont_id = $request->country_id;
                $Red_details->save();

                $epoint = $redM->rwdm_rpnt - $request->use_point;
                $upoint = $redM->rwdm_upnt + $request->use_point;

                DB::connection($db_conn)->table('tm_rwdm')->where(['id' => $redM->id
                ])->update(['rwdm_rpnt' => $epoint, 'rwdm_upnt' => $upoint]);

                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Successfully Use Point ",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public function RFLOutletSave(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet",
            'code' => 0,
            'id' => 0,
        );


        $outletData = json_decode($request->data_Open_New_Outlet)[0];

        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    // if ($outletData->country_id = 5) {
                    $site_code = '7' . str_pad($outletData->Outlet_Code, 9, '7', STR_PAD_LEFT);
                    /*} else {
                        $site_code = '9' . str_pad($outletData->Outlet_Code, 9, '9', STR_PAD_LEFT);
                    }*/
                    $site1 = Site::on($db_conn)->where(['site_mob1' => $outletData->Mobile_No_2])->first();
                    $site2 = Site::on($db_conn)->where(['site_code' => $site_code])->first();


                    if ($site1 == null) {

                        if ($site2 == null) {

                            $site = new Site();
                            $site->setConnection($db_conn);

                            $site->site_name = $outletData->Outlet_Name;
                            $site->site_code = $site_code;
                            $site->outl_id = 1;
                            $site->site_olnm = $outletData->Outlet_Name_BN;
                            $site->site_adrs = $outletData->Address;
                            $site->site_olad = $outletData->Address_BN;
                            $site->mktm_id = $outletData->Market_ID;
                            $site->site_ownm = $outletData->owner_name;
                            $site->site_olon = '';
                            $site->site_mob2 = '';
                            $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                            $site->site_emal = '';
                            $site->scnl_id = 1;
                            $site->otcg_id = $outletData->Shop_Category_id;
                            $site->site_imge = $outletData->Outlet_Image;
                            $site->site_omge = '';
                            $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                            $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                            $site->site_reg = '';
                            $site->site_vrfy = 0;
                            $site->site_hsno = '';
                            $site->site_vtrn = '';
                            $site->site_vsts = 0;
                            $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                            $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                            $site->cont_id = $outletData->country_id;
                            $site->lfcl_id = 1;
                            $site->aemp_iusr = $outletData->up_emp_id;
                            $site->aemp_eusr = $outletData->up_emp_id;
                            $site->save();

                            $SiteLog = new SiteLogRfl();
                            $SiteLog->setConnection($db_conn);
                            $SiteLog->site_id = $site->id;
                            $SiteLog->aemp_iusr = $outletData->up_emp_id;
                            $SiteLog->save();

                            DB::connection($db_conn)->commit();


                            $result_data = array(
                                'success' => 1,
                                'message' => "  Outlet Open Successful  ",
                                'code' => $site->site_code,
                                'id' => $site->id,
                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'message' => " Outlet Code match another Outlet ",
                                'code' => 0,
                                'id' => 0,
                            );
                        }
                    } else {

                        $existId = Site::on($db_conn)->where('site_mob1', $outletData->Mobile_No_2)->first(['id']);

                        DB::connection($db_conn)->select("UPDATE tm_site SET lfcl_id=2 WHERE site_mob1='$outletData->Mobile_No_2'");

                        $site5 = DB::connection($db_conn)->table('tm_site')->where(['id' => $existId->id
                        ])->update([
                            'site_name' => $outletData->Outlet_Name,
                            'site_code' => $site_code,
                            'site_olnm' => $outletData->Outlet_Name_BN,
                            'site_adrs' => $outletData->Address,
                            'site_olad' => $outletData->Address_BN,
                            'mktm_id' => $outletData->Market_ID,
                            'site_ownm' => $outletData->owner_name,
                            'otcg_id' => $outletData->Shop_Category_id,
                            'site_imge' => $outletData->Outlet_Image,
                            'geo_lat' => isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0,
                            'geo_lon' => isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0,
                            'site_isfg' => $outletData->Refrigerator == "Yes" ? 1 : 0,
                            'site_issg' => $outletData->ShopSining == "Yes" ? 1 : 0,
                            'lfcl_id' => 1,
                            'aemp_eusr' => $outletData->up_emp_id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        DB::connection($db_conn)->table('tblg_otop')->where(['site_id' => $existId->id
                        ])->update([
                            'aemp_iusr' => $outletData->up_emp_id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        /*  $SiteLog = new SiteLogRfl();
                          $SiteLog->setConnection($db_conn);
                          $SiteLog->site_id = $existId->id;
                          $SiteLog->aemp_iusr = $outletData->up_emp_id;
                          $SiteLog->save();*/
                        DB::connection($db_conn)->commit();

                        $result_data = array(
                            'success' => 1,
                            'message' => "Already this Outlet Mobile Number exist . Update Successful  ",
                            'code' => $site_code,
                            'id' => $existId->id,
                        );
                    }

                } catch (Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public function QROutletSave(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet",
        );
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    $site1 = Site::on($db_conn)->where(['site_code' => $outletData->Outlet_Code])->first();
                    if ($site1 == null) {

                        $site = new Site();
                        $site->setConnection($db_conn);

                        $site->site_name = $outletData->Outlet_Name;
                        $site->site_code = $outletData->Outlet_Code;
                        $site->outl_id = 1;
                        $site->site_olnm = '';
                        $site->site_adrs = '';
                        $site->site_olad = '';
                        $site->mktm_id = $outletData->Market_ID;
                        $site->site_ownm = '';
                        $site->site_olon = '';
                        $site->site_mob2 = '';
                        $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                        $site->site_emal = '';
                        $site->scnl_id = 1;
                        $site->otcg_id = $outletData->Shop_Category_id;
                        $site->site_imge = $outletData->Outlet_Image;;
                        $site->site_omge = '';
                        $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                        $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                        $site->site_reg = '';
                        $site->site_vrfy = 0;
                        $site->site_hsno = '';
                        $site->site_vtrn = '';
                        $site->site_vsts = 0;
                        $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                        $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                        $site->cont_id = $outletData->country_id;
                        $site->lfcl_id = 1;
                        $site->aemp_iusr = $outletData->up_emp_id;
                        $site->aemp_eusr = $outletData->up_emp_id;
                        $site->save();
                        DB::connection($db_conn)->commit();
                        $result_data = array(
                            'success' => 1,
                            'message' => "  Outlet Open Successful  ",
                        );
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => " Outlet Already Opened \n Using this QR Code!!!  ",
                        );
                    }
                } catch (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public function MQROutletSave(Request $request)
    {
        $result_data = array(
            'success' => 0,
            'message' => "Fail to Open Outlet",
        );
        $outletData = json_decode($request->data_Open_New_Outlet)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {

                    // $ran = SiteRandom::on($db_conn)->where(['number' => $outletData->Outlet_Code])->first();

                    $ran = DB::connection($db_conn)->table('tbld_mqr_random')->where('number', $outletData->Outlet_Code)->first();

                    if ($ran != null) {

                        $site1 = SiteMqr::on($db_conn)->where(['site_code' => $outletData->Outlet_Code])->first();
                        if ($site1 == null) {

                            $site = new SiteMqr();
                            $site->setConnection($db_conn);

                            $site->site_name = $outletData->Outlet_Name;
                            $site->site_code = $outletData->Outlet_Code;
                            $site->site_olcd = $outletData->Outlet_Old_Code;
                            $site->outl_id = 1;
                            $site->site_olnm = '';
                            $site->site_adrs = '';
                            $site->site_olad = '';
                            $site->mktm_id = $outletData->Market_ID;
                            $site->site_ownm = '';
                            $site->site_olon = '';
                            $site->site_mob2 = '';
                            $site->site_mob1 = isset($outletData->Mobile_No_2) ? $outletData->Mobile_No_2 : '';
                            $site->site_emal = '';
                            $site->scnl_id = 1;
                            $site->otcg_id = $outletData->Shop_Category_id;
                            $site->site_imge = $outletData->Outlet_Image;;
                            $site->site_omge = '';
                            $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                            $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                            $site->site_reg = '';
                            $site->site_vrfy = 0;
                            $site->site_hsno = '';
                            $site->site_vtrn = '';
                            $site->site_vsts = 0;
                            $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                            $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;

                            $site->site_isib = $outletData->IceBox == "Yes" ? 1 : 0;
                            $site->site_isdf = $outletData->DeepFredge == "Yes" ? 1 : 0;
                            $site->site_isvc = $outletData->VC == "Yes" ? 1 : 0;

                            $site->cont_id = $outletData->country_id;
                            $site->lfcl_id = 1;
                            $site->aemp_iusr = $outletData->up_emp_id;
                            $site->aemp_eusr = $outletData->up_emp_id;
                            $site->save();
                            DB::connection($db_conn)->commit();
                            $result_data = array(
                                'success' => 1,
                                'message' => "  Outlet Open Successful  ",
                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'message' => " Outlet Already Opened \n Using this QR Code!!!  ",
                            );
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => " Wrong QR Code\n Fail to Open Outlet !!! ",
                        );
                    }
                } catch
                (\Exception $e) {
                    DB::connection($db_conn)->rollback();
                    return $e;
                }
            }
        }
        return $result_data;
    }

    public
    function QROpenOutletCount(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_site`
WHERE aemp_iusr='$SR_ID' AND Date(`created_at`)=CURDATE()");

        }
        return $data1;

    }

    public
    function getItemPicture(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country(5);
        $db_conn = $country->cont_conn;
        $item_code = $request->item_code;

        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT concat('https://images.sihirbox.com/',`amim_imgl`)AS item_picture FROM `tm_amim` WHERE `amim_code`='$item_code' AND amim_imgl!=''");

        }
        return $data1;

    }

    public
    function RFLOpenOutletCount(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_site`
WHERE (aemp_iusr='$SR_ID' OR aemp_eusr='$SR_ID') AND (Date(`created_at`)=CURDATE() OR Date(`updated_at`)=CURDATE())");

        }
        return $data1;

    }

    public
    function MQROpenOutletCount(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT COUNT(`id`)AS Open_Outlet_Qty FROM `tm_msit`
WHERE aemp_iusr='$SR_ID' AND Date(`created_at`)=CURDATE()");

        }
        return $data1;

    }

    public
    function MQROpenOutletInfo(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
FROM `tm_msit`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
WHERE  t1.aemp_iusr='$SR_ID' AND Date(t1.`created_at`)='$Date' 
");

        }
        return $data1;

    }

    public
    function RFLOpenOutletInfo(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
FROM `tm_site`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
WHERE (t1.aemp_iusr='$SR_ID'OR t1.aemp_eusr='$SR_ID') AND (Date(t1.`created_at`)='$Date' OR Date(t1.`updated_at`)='$Date')
");

        }
        return $data1;

    }

    public
    function MQRUpdateOutletInfo(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Date = $request->Date;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t1.id AS site_id ,t1.`site_code`,t1.`site_name`,t1.`site_adrs`,
t1.`geo_lat`,t1.`geo_lon`,t2.mktm_name,t2.id AS market_id,t1.site_mob1 AS mobile
FROM `tm_msit`t1 JOIN tm_mktm t2 ON(t1.`mktm_id`=t2.id)
WHERE  t1.aemp_eusr='$SR_ID' AND Date(t1.`updated_at`)='$Date' 
");

        }
        return $data1;

    }

    public
    function QROpenOutletCheck(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Scanned_outlet_id = $request->Scanned_outlet_id;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
          SELECT `id`AS Outlet_ID,site_code AS Outlet_Code,`site_name`AS Outlet_Name,`site_olnm`AS Outlet_Name_Bangla,
          `site_adrs`AS Outlet_Address,`site_olad`AS outlet_address_Bangla,
         `site_ownm`AS Outlet_Owner_Name,`site_mob1`AS Outlet_Mobile_No,`site_isfg`AS refrigerator,`site_issg`AS shop_sign
          FROM `tm_site`
          WHERE `site_code`='$Scanned_outlet_id'
          ");

        }
        return $data1;

    }

    public
    function MQROpenOutletCheck(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $SR_ID = $request->SR_ID;
        $Scanned_outlet_id = $request->Scanned_outlet_id;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
          SELECT `id`AS Outlet_ID,site_code AS Outlet_Code,`site_name`AS Outlet_Name,`site_olnm`AS Outlet_Name_Bangla,
          `site_adrs`AS Outlet_Address,`site_olad`AS outlet_address_Bangla,
         `site_ownm`AS Outlet_Owner_Name,`site_mob1`AS Outlet_Mobile_No,
         `site_isfg`AS refrigerator,`site_issg`AS shop_sign,
         `site_isib`AS ice_box,`site_isdf`AS deep_fridge,`site_isvc`AS vc
          FROM `tm_msit`
          WHERE `site_code`='$Scanned_outlet_id'
          ");

        }
        return $data1;

    }

    public
    function updateOutletSave(Request $request)
    {
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = Site::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name != "" ? $outletData->Outlet_Name : '';
                    $site->site_olnm = $outletData->Outlet_Name_BN != "" ? $outletData->Outlet_Name_BN : '';
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';

                    if (isset($outletData->trn)) {
                        if (!is_null($outletData->trn)) {
                            $site->site_vtrn = $outletData->trn;
                        }
                    }
                    if (isset($outletData->shop_category)) {
                        if (!is_null($outletData->shop_category)) {
                            $site->otcg_id = $outletData->shop_category;
                        }
                    }
                    if (isset($outletData->nationality)) {
                        if (!is_null($outletData->nationality)) {
                            $tl_scmp = DB::connection($db_conn)->table('tl_scmp')->where(['site_id' => $site->id])->first();
                            if ($tl_scmp != null) {

                                DB::connection($db_conn)->table('tl_scmp')->where(['site_id' => $site->id
                                ])->update(['cont_id' => $outletData->nationality]);
                            }
                        }
                    }
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    $site->site_imge = $outletData->Outlet_Image != "" ? $outletData->Outlet_Image : '';
                    $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                    $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
                    if ($outletData->country_id == 23) {
                    } else {
                        $site->geo_lat = isset($outletData->Location) ? explode(',', $outletData->Location)[0] : 0;;
                        $site->geo_lon = isset($outletData->Location) ? explode(',', $outletData->Location)[1] : 0;
                    }
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

    public
    function MQRupdateOutletSave(Request $request)
    {
        $outletData = json_decode($request->Outlet_Update_Data)[0];
        if ($outletData) {
            $country = (new Country())->country($outletData->country_id);
            $db_conn = $country->cont_conn;
            if ($db_conn != '') {
                DB::connection($db_conn)->beginTransaction();
                try {
                    $site = SiteMqr::on($db_conn)->findorfail($outletData->Outlet_ID);
                    $site->site_name = $outletData->Outlet_Name != "" ? $outletData->Outlet_Name : '';
                    $site->site_olnm = $outletData->Outlet_Name_BN != "" ? $outletData->Outlet_Name_BN : '';
                    $site->site_adrs = $outletData->Address != "" ? $outletData->Address : '';
                    $site->site_olad = $outletData->Address_BN != "" ? $outletData->Address_BN : '';
                    $site->site_ownm = $outletData->owner_name != "" ? $outletData->owner_name : '';
                    $site->site_mob1 = $outletData->Mobile_No != "" ? $outletData->Mobile_No : '';
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    $site->site_imge = $outletData->Outlet_Image != "" ? $outletData->Outlet_Image : '';
                    $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                    $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;

                    $site->site_isib = $outletData->IceBox == "Yes" ? 1 : 0;
                    $site->site_isdf = $outletData->DeepFredge == "Yes" ? 1 : 0;
                    $site->site_isvc = $outletData->VC == "Yes" ? 1 : 0;

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

    public
    function MQRUpdateOutletLocationFromMap(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                // $site = SiteMqr::on($db_conn)->findorfail($request->Outlet_Code);
                $site = SiteMqr::on($db_conn)->where(['site_code' => $request->Outlet_Code])->first();

                $site->geo_lat = isset($request->latitude) ? $request->latitude : $site->geo_lat;
                $site->geo_lon = isset($request->longitude) ? $request->longitude : $site->geo_lon;

                $site->aemp_eusr = $request->up_emp_id;
                $site->save();
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Outlet Location Updated Successfully",
                    'Outlet_ID' => $site->id,
                    'Outlet_Code' => $site->site_code,
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

    public
    function RemoveOutletFromRoute(Request $request)
    {
        $country = (new Country())->country($request->country_id);

        if ($request->country_id == 14 || $request->country_id == 5) {

            $result_data = array(
                'success' => 10,
                'message' => "Removing outlet From Route has been Blocked !!! ",
            );
            return $result_data;
        }

        $result_data = array(
            'success' => 10,
            'message' => "Blocked !!! Remove outlet From Route",
        );
        $db_conn = $country->cont_conn;

        if ($db_conn != '') {

            $routeSite = RouteSite::on($db_conn)->where(['site_id' => $request->site_id, 'rout_id' => $request->route_id])->first();
            if ($routeSite != null) {

                DB::connection($db_conn)->table('tl_rsmp')->where(['rout_id' => $request->route_id,
                    'site_id' => $request->site_id])->delete();

                $result_data = array(
                    'success' => 1,
                    'message' => "Successfully Remove From Route !!",
                );
            } else {
                $result_data = array(
                    'success' => 20,
                    'message' => "Fail to Remove From Route",
                );
            }
        }
        return $result_data;
    }

    public
    function censusOutletImport(Request $request)
    {
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
            }
        }
        return $result_data;
    }

    public
    function attendanceSave_new(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                /* $path_name = $country->cont_imgf . '/' . date('Y-m-d') . '/attendance/' . uniqid() . '.jpg';
                 if (isset($request['OutLet_Picture'])) {
                     $s3->putObject(array(
                         'Bucket' => 'prgfms',
                         'Key' => $path_name,
                         'Body' => base64_decode($request->OutLet_Picture),
                         'ContentType' => 'base64',
                         'ContentEncoding' => 'image/jpeg',
                         'ACL' => 'public-read',
                     ));
                 }*/
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = $request->Group_Id;;//$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID;// $request->SR_ID;
                $attendance->site_id = 2;
                $attendance->site_name = '';
                $attendance->attn_imge = $request->OutLet_Picture;
                $attendance->geo_lat = explode(',', $request->Location)[0];
                $attendance->geo_lon = explode(',', $request->Location)[1];
                $attendance->attn_time = $request->Date_Time;
                $attendance->attn_date = $request->Date_Time;
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->Status;
                $attendance->rout_id = $request->Route_ID;
                $attendance->attn_fdat = $request->From_Date == "" ? date('Y-m-d') : $request->From_Date;
                $attendance->attn_tdat = $request->To_Date == "" ? date('Y-m-d') : $request->To_Date;
                $attendance->attn_rmak = $request->Lv_IOM_Remarks == "" ? '' : $request->Lv_IOM_Remarks;
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::commit();
                return array('column_id' => $request->ID);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public
    function attendanceSave(Request $request)
    {

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $s3 = AWS::createClient('s3');
            DB::beginTransaction();
            try {
                $path_name = $country->cont_imgf . '/' . date('Y-m-d') . '/attendance/' . uniqid() . '.jpg';
                if (isset($request['OutLet_Picture'])) {
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $path_name,
                        'Body' => base64_decode($request->OutLet_Picture),
                        'ContentType' => 'base64',
                        'ContentEncoding' => 'image/jpeg',
                        'ACL' => 'public-read',
                    ));
                }
                $attendance = new Attendance();
                $attendance->setConnection($db_conn);
                $attendance->slgp_id = $request->Group_Id;;//$request->Group_Id;
                $attendance->aemp_id = $request->SR_ID;// $request->SR_ID;
                $attendance->site_id = 2;
                $attendance->site_name = '';
                $attendance->attn_imge = $path_name;
                $attendance->geo_lat = explode(',', $request->Location)[0];
                $attendance->geo_lon = explode(',', $request->Location)[1];
                $attendance->attn_time = $request->Date_Time;
                $attendance->attn_date = $request->Date_Time;
                $attendance->attn_mont = 1;
                $attendance->atten_type = $request->Status;
                $attendance->rout_id = $request->Route_ID;
                $attendance->attn_fdat = $request->From_Date == "" ? date('Y-m-d') : $request->From_Date;
                $attendance->attn_tdat = $request->To_Date == "" ? date('Y-m-d') : $request->To_Date;
                $attendance->attn_rmak = $request->Lv_IOM_Remarks == "" ? '' : $request->Lv_IOM_Remarks;
                $attendance->cont_id = $request->country_id;
                $attendance->lfcl_id = 1;
                $attendance->aemp_iusr = $request->up_emp_id;
                $attendance->aemp_eusr = $request->up_emp_id;
                $attendance->var = 1;
                $attendance->attr1 = '';
                $attendance->attr2 = '';
                $attendance->attr3 = 0;
                $attendance->attr4 = 0;
                $attendance->save();

                DB::commit();
                return array('column_id' => $request->ID);
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                //throw $e;
            }
        }

    }

    public
    function MasterData(Request $request)
    {
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
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
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
WHERE t1.aemp_id = $request->emp_id and t1.rpln_day=dayname(curdate()) ");

            $data2 = DB::connection($db_conn)->select("
SELECT  
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id  and t1.rpln_day=dayname(curdate())
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt ");

            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");

            $data5 = DB::connection($db_conn)->select("
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
FROM tm_dprt AS t1");

            $data6 = DB::connection($db_conn)->select("
SELECT
concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
  t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm AS buy_item_code,
  t5.prdt_mbqt as max_buy_qty,
  t5.prdt_mnbt as min_buy_qyt,
  t5.prdt_fitm as free_item_code,
  t5.prdt_fiqt as free_item_qty,
  round(t5.prdt_fipr,2) as free_item_price,
  t5.prdt_disc as discount_percente,
  t4.prom_edat as end_date
from tl_srgb as t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id 
and t2.prom_nztp=0 
and t2.prom_sdat <=CURDATE()
AND t2.prom_edat >=CURDATE()
");
        }


        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
    function MasterDataNew(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t5.acmp_id                                                                                 AS OU_ID,
  t1.rpln_day                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
  INNER JOIN tm_slgp AS t5 ON t2.slgp_id = t5.id

WHERE t1.aemp_id = $request->emp_id ");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id 
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");

            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name, t4.id, t4.base_name, t3.id, t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  INNER JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  INNER JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");

            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");

            $data6 = DB::connection($db_conn)->select("
SELECT
concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt,round(t5.prdt_fipr,2),
  t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm AS buy_item_code,
  t5.prdt_mbqt as max_buy_qty,
  t5.prdt_mnbt as min_buy_qyt,
  t5.prdt_fitm as free_item_code,
  t5.prdt_fiqt as free_item_qty,
  round(t5.prdt_fipr,2) as free_item_price,
  t5.prdt_disc as discount_percente,
  t4.prom_edat as end_date
from tl_srgb as t1
  INNER JOIN tm_base AS t2 ON t1.base_id = t2.id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t1.slgp_id=t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.aemp_id = $request->emp_id
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_srgb AS t1
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id 
and t2.prom_nztp=0 
and t2.prom_sdat <=CURDATE()
AND t2.prom_edat >=CURDATE()
");
        }


        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
    function MasterDataNew_Three(Request $request)
    {
        $data1 = array();
        $data4 = array();
        $data5 = array();
        $data7 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
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

WHERE t1.aemp_id = $request->emp_id ");
            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name,t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  LEFT JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  LEFT JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id");
            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");


            /*SELECT
              concat(t1.id, t1.rson_name) AS column_id,
              concat(t1.id, t1.rson_name) AS token,
              t1.id                       AS Reason_id,
              t1.rson_name                AS Reason_Name
            FROM tm_rson AS t1*/
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
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
        );
    }

    public
    function MasterDataNew_WebLink(Request $request)
    {
        $data8 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data8 = DB::connection($db_conn)->select("
    SELECT
    concat(t1.id, t1.`wurl_link`,t1.wurl_muid,t1.wurl_stus) AS column_id,
    concat(t1.id, t1.`wurl_link`,t1.wurl_muid,t1.wurl_stus) AS token,
    t1.`wurl_muid`                                          AS menu_id,
    t1.`wurl_link`                                          AS menu_link
    FROM tm_wurl AS t1
    WHERE t1.wurl_stus=1
    ");

        }
        return Array(
            "Web_URL" => array("data" => $data8, "action" => $request->country_id),
        );
    }

    public
    function MasterDataNew_Three1(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data4 = array();
        $data5 = array();
        $data7 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.aemp_id, t2.slgp_id, t1.rout_id, t3.rout_name, t4.id, t4.base_name, t1.rpln_day) AS column_id,
  t1.aemp_id                                                                                 AS SR_ID,
  t2.slgp_id                                                                                 AS Group_ID,
  t1.rout_id                                                                                 AS Route_ID,
  t3.rout_name                                                                               AS Route_Name,
  t4.id                                                                                      AS Base_Code,
  t4.base_name                                                                               AS Base_Name,
  t5.acmp_id                                                                                 AS OU_ID,
  TRIM(t1.rpln_day)                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tl_srdi AS t6 ON t1.aemp_id=t6.aemp_id
  INNER JOIN tm_dlrm AS t7 ON t6.dlrm_id = t7.id
  LEFT JOIN tm_base AS t4 ON t7.base_id = t4.id
  INNER JOIN tm_slgp AS t5 ON t2.slgp_id = t5.id

WHERE t1.aemp_id = $request->emp_id ");


            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t2.dlrm_name,t2.dlrm_adrs, t2.dlrm_mob1) AS column_id,
  t2.id                                                                               AS Dealer_ID,
  t2.dlrm_name                                                                        AS Dealer_Name,
  t4.id                                                                               AS Base_Code,
  t4.base_name                                                                        AS Base_Name,
  t3.id                                                                               AS Group_ID,
  t2.dlrm_adrs                                                                        AS Address,
  t2.dlrm_mob1                                                                        AS Mobile_No
FROM tl_srdi AS t1
  INNER JOIN tm_dlrm AS t2 ON t1.dlrm_id = t2.id
  LEFT JOIN tm_slgp AS t3 ON t2.slgp_id = t3.id
  LEFT JOIN tm_base AS t4 ON t2.base_id = t4.id
WHERE t1.aemp_id =  $request->emp_id and t2.lfcl_id=1");
            $data5 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.nopr_name) AS column_id,
  concat(t1.id, t1.nopr_name) AS token,
  t1.id                       AS Reason_id,
  t1.nopr_name                AS Reason_Name
FROM tm_nopr AS t1");


            /*SELECT
              concat(t1.id, t1.rson_name) AS column_id,
              concat(t1.id, t1.rson_name) AS token,
              t1.id                       AS Reason_id,
              t1.rson_name                AS Reason_Name
            FROM tm_rson AS t1*/
            $data7 = DB::connection($db_conn)->select("
SELECT
    concat(t1.id, t1.dprt_name) AS column_id,
    concat(t1.id, t1.dprt_name) AS token,
    t1.id                       AS Reason_id,
    t1.dprt_name                AS Reason_Name
  FROM tm_dprt AS t1
");


            $data8 = DB::connection($db_conn)->select("
    SELECT concat(t1.id, t1.`lfcl_id`,t1.`mspm_sdat`,t1.`mspm_edat`,t2.slgp_id,t2.zone_id,t3.amim_id) AS column_id,
    t3.amim_id                  AS MSP_Item_ID,
    t3.mspd_qnty                AS MSP_Item_Qty,
    t1.id                       AS MSP_ID,
    '2'                         AS Status_ID
FROM `tm_mspm`t1 
JOIN tl_mspg t2 ON(t1.`id`=t2.mspm_id)
JOIN tm_mspg t3 ON(t1.id=t3.mspm_id)
JOIN tm_aemp t4 ON t2.slgp_id=t4.slgp_id AND t2.zone_id=t4.zone_id
WHERE t4.id=$request->emp_id AND t1.`lfcl_id`=1 AND t1.`mspm_sdat` <= CURDATE()
      AND t1.`mspm_edat` >= CURDATE()
");

        }
        return Array(
            "SR_Group_Wise_Route_Table" => array("data" => $data1, "action" => $request->country_id),
            "Distribution_Info_Table" => array("data" => $data4, "action" => $request->country_id),
            "Non_Productive_Reason" => array("data" => $data5, "action" => $request->country_id),
            "Grv_Reason" => array("data" => $data7, "action" => $request->country_id),
            "MSP_Item_MSP_Locally_New" => array("data" => $data8, "action" => $request->country_id),
        );
    }

    public
    function MasterDataNew_Product_Info(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.plmt_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");
        }
        return Array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public
    function MasterDataNew_Product_Info_With_Image_Icon_Large(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            /*$data3 = DB::connection($db_conn)->select("
    SELECT
    concat(t3.id,t3.amim_imgl,t3.amim_imic,t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
    t1.plmt_id                                                                          AS Item_Price_List,
    t1.plmt_id                                                                          AS Grv_Item_Price_List,
    t3.id                                                                               AS Item_Code,
    t3.amim_code                                                                        AS sku_code,
    t3.amim_imgl                                                                        AS amim_imgl,
    t3.amim_imic                                                                        AS amim_imic,
    t5.issc_name                                                                        AS Item_Category,
    t3.amim_name                                                                        AS Item_Name,
    round(t2.pldt_tppr,2)                                                               AS Item_Price,
    round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
    t2.amim_duft                                                                        AS Item_Factor,
    t5.issc_seqn                                                                        AS Item_Showing_flg,
    t2.amim_dunt                                                                        AS D_Unit,
    t2.amim_runt                                                                        AS R_Unit
    FROM tl_sgsm AS t1
    INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
    INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
    INNER JOIN tl_sgit AS t4 ON t1.plmt_id = t4.slgp_id AND t2.amim_id = t4.amim_id
    INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
    WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
    GROUP BY t1.plmt_id, t3.id, t5.issc_name,
    t3.amim_name, t2.pldt_tppr, t2.amim_duft,
    t5.issc_seqn,t2.amim_dunt,t2.amim_runt,
    t3.amim_code "); */

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id,t3.amim_imgl,t3.amim_imic,t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2),t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t3.amim_imgl                                                                        AS amim_imgl,
  t3.amim_imic                                                                        AS amim_imic,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,4)                                                               AS Item_Price,
  round(t2.pldt_tpgp,4)                                                               AS Grv_Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id 
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
GROUP BY t1.plmt_id, t3.id, t5.issc_name, 
t3.amim_name, t2.pldt_tppr, t2.amim_duft, 
t5.issc_seqn,t2.amim_dunt,t2.amim_runt,
t3.amim_code ");


        }
        return Array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );



    }

    public
    function MasterDataNew_Product_Info_With_Image(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t3.id, t5.issc_name, t3.amim_name, round(t2.pldt_tppr,2), t2.amim_duft, t5.issc_seqn) AS column_id,
  t1.plmt_id                                                                          AS Item_Price_List,
  t1.plmt_id                                                                          AS Grv_Item_Price_List,
  t3.id                                                                               AS Item_Code,
  t3.amim_code                                                                        AS sku_code,
  t3.amim_imgl                                                                        AS amim_imgl,
  t5.issc_name                                                                        AS Item_Category,
  t3.amim_name                                                                        AS Item_Name,
  round(t2.pldt_tppr,2)                                                               AS Item_Price,
  round(t2.pldt_tppr,2)                                                               AS Grv_Item_Price,
  t2.amim_duft                                                                        AS Item_Factor,
  t5.issc_seqn                                                                        AS Item_Showing_flg,
  t2.amim_dunt                                                                        AS D_Unit,
  t2.amim_runt                                                                        AS R_Unit
FROM tl_sgsm AS t1
  INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id
  INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id
  INNER JOIN tl_sgit AS t4 ON t1.plmt_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");
        }
        return Array(
            "Product_Info_Table" => array("data" => $data3, "action" => $request->country_id),
        );

    }

    public
    function MasterDataNew_RouteWise_Outlet(Request $request)
    {

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t3.site_id, t1.rout_id, t3.rspm_serl,t4.lfcl_id) AS column_id,
  t1.rout_id                                   AS Route_ID,
  t3.site_id                                   AS Outlet_ID,
  t4.site_code                                 AS Outlet_Code,
  t4.site_name                                 AS Outlet_Name,
  t4.site_ownm                                 AS Owner_Name,
  t2.slgp_id                                   AS Group_ID,
  t3.rspm_serl                                 AS outlet_serial,
  t4.site_mob1                                 AS Mobile_No,
  t4.site_adrs                                 AS Outlet_Address,
  t4.geo_lat                                   AS geo_lat,
  t4.geo_lon                                   AS geo_lon
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tl_rsmp AS t3 ON t1.rout_id = t3.rout_id
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
WHERE t1.aemp_id = $request->emp_id AND t4.lfcl_id = 1
GROUP BY t3.site_id, t1.rout_id, t3.rspm_serl, t4.site_code, t4.site_name, t4.site_ownm, t2.slgp_id, t3.rspm_serl,
  t4.site_mob1, t4.site_adrs, t4.geo_lat, t4.geo_lon;");
        }

        return Array(
            "RouteWise_Outlet_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public
    function GetOutletBankFromQROutlet(Request $request)
    {

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
  SELECT
  concat(t1.id, t1.site_code, t3.than_id) AS column_id,
  t1.id AS Outlet_Id,
  t1.site_code AS Outlet_Code,
  t1.site_name AS Outlet_Name,
  t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.geo_lat AS geo_lat,
  t1.geo_lon AS geo_lon
  FROM tm_site t1 JOIN tm_mktm t2 ON t1.mktm_id=t2.id
  LEFT JOIN tm_ward t3 ON t2.ward_id=t3.id
  LEFT JOIN tl_srth t4 ON t3.than_id=t4.than_id
  WHERE t4.aemp_id='$request->emp_id ' AND t4.lfcl_id='1';");
        }
        /*$data2 = DB::connection($db_conn)->select("
    SELECT
    t1.id AS Outlet_Id,
    t1.site_code AS Outlet_Code,
    t1.site_name AS Outlet_Name,
    t1.geo_lat AS geo_lat,
    t1.geo_lon AS geo_lon
    FROM tm_site t1 JOIN tm_mktm t2 ON t1.mktm_id=t2.id
    LEFT JOIN tm_ward t3 ON t2.ward_id=t3.id
    LEFT JOIN tl_srth t4 ON t3.than_id=t4.than_id
    WHERE t4.aemp_id='$request->emp_id ' AND t4.lfcl_id='1';");
        }*/

        return Array(
            "OutletBank_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public
    function GetOutletBankFromQROutletUsingThan(Request $request)
    {

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
  SELECT
  concat(t1.id, t1.site_code, t3.than_id) AS column_id,
  t1.id AS Outlet_Id,
  t1.site_code AS Outlet_Code,
  TRIM(t1.site_name) AS Outlet_Name,
  t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.geo_lat AS geo_lat,
  t1.geo_lon AS geo_lon
  FROM tm_site t1 JOIN tm_mktm t2 ON t1.mktm_id=t2.id
  LEFT JOIN tm_ward t3 ON t2.ward_id=t3.id
  LEFT JOIN tl_srth t4 ON t3.than_id=t4.than_id
  WHERE t4.aemp_id='$request->emp_id' AND t4.than_id='$request->than_id' AND t1.lfcl_id=1;");
        }
        return Array(
            "OutletBank_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public
    function outletsByThana(Request $request)
    {

        $data2 = array();

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data2 = DB::connection($db_conn)->select("
  SELECT
  concat(t1.id, t1.site_code, t3.than_id) AS column_id,
  t1.id AS Outlet_ID,
  t1.site_code AS Outlet_Code,
  TRIM(t1.site_name) AS Outlet_Name,
  TRIM(t1.site_olnm) AS Outlet_Name_Bn,
  t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.site_olad AS Outlet_Address_Bn,
  t1.site_imge AS Outlet_imge_ln,
  t1.site_isfg AS refrigerator,
  t1.site_issg AS shop_sign,
  t1.otcg_id AS shop_category_id,
  t1.site_vtrn AS trn,
  t5.cont_id AS nationality,
  t1.geo_lat AS geo_lat,
  t1.geo_lon AS geo_lon
  FROM tm_site t1 JOIN tm_mktm t2 ON t1.mktm_id=t2.id
  LEFT JOIN tm_ward t3 ON t2.ward_id=t3.id
  LEFT JOIN tl_srth t4 ON t3.than_id=t4.than_id
  LEFT JOIN  tl_scmp t5 ON t1.id=t5.site_id
  WHERE t4.aemp_id='$request->emp_id' AND t4.than_id='$request->than_id' AND t1.lfcl_id=1;");
        }
        return Array(
            "OutletBank_Table" => array("data" => $data2, "action" => $request->country_id),
        );

    }

    public
    function MasterDataNew_FOC(Request $request)
    {

        $data6 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data6 = DB::connection($db_conn)->select("
SELECT
  concat(t5.prdt_sitm, t5.prdt_mbqt, t5.prdt_mnbt, t5.prdt_fitm, t5.prdt_fiqt, round(t5.prdt_fipr, 2),
         t5.prdt_disc, t4.prom_edat) AS column_id,
  t5.prdt_sitm                       AS buy_item_code,
  t5.prdt_mbqt                       AS max_buy_qty,
  t5.prdt_mnbt                       AS min_buy_qyt,
  t5.prdt_fitm                       AS free_item_code,
  t5.prdt_fiqt                       AS free_item_qty,
  round(t5.prdt_fipr, 2)             AS free_item_price,
  t5.prdt_disc                       AS discount_percente,
  t4.prom_edat                       AS end_date
FROM tm_aemp AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.id = t2.aemp_id
  INNER JOIN tt_pznt AS t3 ON t2.zone_id = t3.zone_id
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t2.plmt_id = t4.slgp_id
  INNER JOIN tt_prdt AS t5 ON t4.id = t5.prom_id
WHERE t1.id = $request->emp_id AND t4.lfcl_id='1'
UNION ALL
SELECT
  concat(t3.prdt_sitm, t3.prdt_mbqt, t3.prdt_mnbt, t3.prdt_fitm, t3.prdt_fiqt, round(t3.prdt_fipr, 2), t3.prdt_disc,
         t2.prom_edat)   AS column_id,
  t3.prdt_sitm           AS buy_item_code,
  t3.prdt_mbqt           AS max_buy_qty,
  t3.prdt_mnbt           AS min_buy_qyt,
  t3.prdt_fitm           AS free_item_code,
  t3.prdt_fiqt           AS free_item_qty,
  round(t3.prdt_fipr, 2) AS free_item_price,
  t3.prdt_disc           AS discount_percente,
  t2.prom_edat           AS end_date
FROM tl_sgsm AS t1
  INNER JOIN tm_prom AS t2 ON t1.plmt_id = t2.slgp_id
  INNER JOIN tt_prdt AS t3 ON t2.id = t3.prom_id
WHERE t1.aemp_id = $request->emp_id AND t2.lfcl_id='1'
      AND t2.prom_nztp = 0
      AND t2.prom_sdat <= CURDATE()
      AND t2.prom_edat >= CURDATE()
");
        }
        return Array(
// 0= NT  1= Zn
            "FOC_Program_ALL_Info_Table" => array("data" => $data6, "action" => $request->country_id),
        );

    }

    public
    function PromotionTwoDataSync(Request $request)
    {
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  concat(t1.id, t1.prms_edat, t1.prms_sdat,t1.lfcl_id,t3.slgp_id,t3.zone_id) AS column_id,
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
JOIN tm_aemp t3 ON(t1.prmr_qfgp=t3.slgp_id) AND t2.site_id=t3.zone_id
WHERE t3.id = $request->emp_id AND curdate() BETWEEN t1.prms_sdat AND t1.prms_edat AND t1.lfcl_id = 1
GROUP BY t1.id, t1.prms_edat, t1.prms_name, t1.prms_sdat, t1.prmr_qfct, t1.prmr_ditp, t1.prmr_ctgp, t1.prmr_qfon,
  t1.prmr_qfln
  ");

            $data2 = DB::connection($db_conn)->select("
SELECT
  concat(t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,t4.slgp_id,t4.zone_id) AS column_id,
  t1.prmr_id                                                            AS promo_id,
  t1.amim_id                                                            AS product_id,
  t1.prmd_modr                                                          AS pro_modifier_id
FROM tm_prmd AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
  JOIN tm_aemp t4 ON(t2.prmr_qfgp=t4.slgp_id) AND t3.site_id=t4.zone_id
WHERE t4.id =$request->emp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1 
GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmr_id, t1.amim_id, t1.prmd_modr");

            $data3 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_tqty,t2.lfcl_id,t4.slgp_id,t4.zone_id, t1.prsb_famn,t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl) AS column_id,
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
  JOIN tm_aemp t4 ON(t2.prmr_qfgp=t4.slgp_id) AND t3.site_id=t4.zone_id
WHERE t4.id = $request->emp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1 
GROUP BY t1.prmr_id, t1.prsb_fqty, t2.prms_sdat, t2.prms_edat, t1.prsb_text, t1.prsb_tqty, t1.prsb_famn, t1.prsb_tamn,
  t1.prsb_qnty, t1.prsb_disc, t1.prsb_modr, t1.prsb_mosl");

            $data4 = DB::connection($db_conn)->select("
SELECT
  concat(t2.id, t1.amim_id, t2.prms_sdat, t2.prms_edat,t2.lfcl_id,t4.slgp_id,t4.zone_id) AS column_id,
  t1.prmr_id                                                            AS promo_id,
  t1.amim_id                                                            AS product_id,
  t1.prmd_modr                                                          AS pro_modifier_id
FROM tm_prmf AS t1
  INNER JOIN tm_prmr AS t2 ON t1.prmr_id = t2.id
  INNER JOIN tl_prsm AS t3 ON t2.id = t3.prmr_id
  JOIN tm_aemp t4 ON(t2.prmr_qfgp=t4.slgp_id) AND t3.site_id=t4.zone_id
WHERE t4.id =$request->emp_id AND curdate() BETWEEN t2.prms_sdat AND t2.prms_edat AND t2.lfcl_id = 1 
GROUP BY t1.prmr_id, t1.amim_id, t2.prms_sdat, t2.prms_edat, t1.prmr_id, t1.amim_id, t1.prmd_modr");

        }
        return Array(
            "promotion" => array("data" => $data1, "action" => $request->country_id),
            "promotion_buy_item" => array("data" => $data2, "action" => $request->country_id),
            "promotion_slab" => array("data" => $data3, "action" => $request->country_id),
            "promotion_free_item" => array("data" => $data4, "action" => $request->country_id),
        );

    }

    public
    function aroundOutlet(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            if ($request->country_id == 2 || $request->country_id == 5) {
                $data2 = DB::connection($db_conn)->select("select 0 id from dual union all SELECT distinct t4.id
FROM tm_mktm t4
LEFT JOIN tm_ward_lt t6 ON(t4.ward_id=t6.ward_id)
WHERE  MBRContains(st_makeEnvelope (
point(($request->geo_lon + 5 / 111.1), ($request->geo_lat + 5 / 111.1)),
  point(($request->geo_lon - 5 / 111.1), ($request->geo_lat - 5 / 111.1)) 
), POINT( geo_lon, geo_lat ))");
                $d = '';
                $i = 1;
                foreach ($data2 as $key => $value) {
                    if ($i == 1) {
                        $d .= $value->id;
                    } else {
                        $d .= ',' . $value->id;
                    }
                    $i++;
                }
                $data1 = DB::connection($db_conn)->select("
         select
         Outlet_ID                                                AS Outlet_ID,
         Outlet_Code                                              AS Outlet_Code,
         Outlet_Name                                              AS Outlet_Name,
         Outlet_Name_Bn                                           AS Outlet_Name_Bn,
         distance_in_km                                           AS distance_in_km,
         Owner_Name                                               AS Owner_Name,
         Mobile_No                                                AS Mobile_No,
         Outlet_Address                                           AS Outlet_Address,
         Outlet_Address_Bn                                        AS Outlet_Address_Bn,
         Outlet_imge_ln                                           AS Outlet_imge_ln,
         refrigerator                                             AS refrigerator,
         shop_sign                                                AS shop_sign,
         geo_lat                                                  AS geo_lat,
         geo_lon                                                  AS geo_lon
  FROM ( select t1.id                  AS Outlet_ID,
         t1.site_code             AS Outlet_Code,
         t1.site_name             AS Outlet_Name,
        TRIM(t1.site_olnm)        AS Outlet_Name_Bn,
               ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                 * COS( RADIANS( t1.geo_lat ) )
                 * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                 + SIN( RADIANS( $request->geo_lat  ) )
                   * SIN( RADIANS( t1.geo_lat ) )
           )
           * 6371
         ) AS distance_in_km,
   t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.site_olad AS Outlet_Address_Bn,
  t1.site_imge AS Outlet_imge_ln,
  t1.site_isfg AS refrigerator,
  t1.site_issg AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
  from tm_site_registration as t1
 where t1.mktm_id in($d) and MBRContains(st_makeEnvelope (
  point(($request->geo_lon + 1 / 111.1), ($request->geo_lat + 1 / 111.1)),
  point(($request->geo_lon - 1 / 111.1), ($request->geo_lat - 1 / 111.1)) 
), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 order by distance_in_km asc limit 50 )AS t22
UNION ALL 
select  t1.id                                                         AS Outlet_ID,
         t1.locd_code                                                 AS Outlet_Code,
         t1.locd_name                                                 AS Outlet_Name,
        TRIM(t1.locd_name)                                            AS Outlet_Name_Bn,
               ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                 * COS( RADIANS( t1.geo_lat ) )
                 * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                 + SIN( RADIANS( $request->geo_lat ) )
                   * SIN( RADIANS( t1.geo_lat ) )
           )
           * 6371
         ) AS distance_in_km,
   '' AS Owner_Name,
  '' AS Mobile_No,
  t1.geo_adrs AS Outlet_Address,
  t1.geo_adrs AS Outlet_Address_Bn,
  '' AS Outlet_imge_ln,
  '0' AS refrigerator,
  '0' AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
  from tm_locd as t1
 where MBRContains(st_makeEnvelope (
  point(($request->geo_lon + 0.08 / 111.1), ($request->geo_lat + 0.08 / 111.1)),
  point(($request->geo_lon / 111.1), ($request->geo_lat - 0.08 / 111.1)) 
), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 order by distance_in_km asc limit 50

 ");
            } else {
                $data1 = DB::connection($db_conn)->select("
     SELECT   t1.id                                                   AS Outlet_ID,
         t1.site_code                                                 AS Outlet_Code,
         TRIM(t1.site_name)                                           AS Outlet_Name,
         TRIM(t1.site_olnm)                                           AS Outlet_Name_Bn,
         ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                 * COS( RADIANS( t1.geo_lat ) )
                 * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                 + SIN( RADIANS( $request->geo_lat  ) )
                   * SIN( RADIANS( t1.geo_lat ) )
           )
           * 6371
         ) AS distance_in_km,
  t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.site_olad AS Outlet_Address_Bn,
  t1.site_imge AS Outlet_imge_ln,
  t1.site_isfg AS refrigerator,
  t1.site_issg AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
FROM tm_site AS t1 where lfcl_id=1   HAVING distance_in_km < 1  
ORDER BY distance_in_km
LIMIT 20
");

            }
        }

        return Array(
            "OutletBank_Table" => array("data" => $data1, "action" => $request->country_id),
        );

    }

    public
    function aroundOutletSearch(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $Search_key = $request->search_key;

        if ($request->country_id == 2 || $request->country_id == 5) {
            if ($db_conn != '') {

                $data2 = DB::connection($db_conn)->select("select 0 id from dual union all SELECT distinct t4.id
FROM tm_mktm t4
LEFT JOIN tm_ward_lt t6 ON(t4.ward_id=t6.ward_id)
WHERE  MBRContains(st_makeEnvelope (
point(($request->geo_lon + 15 / 111.1), ($request->geo_lat + 15 / 111.1)),
  point(($request->geo_lon - 15 / 111.1), ($request->geo_lat - 15 / 111.1)) 
), POINT( geo_lon, geo_lat ))");
                $d = '';
                $i = 1;
                foreach ($data2 as $key => $value) {
                    if ($i == 1) {
                        $d .= $value->id;
                    } else {
                        $d .= ',' . $value->id;
                    }
                    $i++;
                }

//AND t1.`site_name`LIKE'%$Search_key%'
                $data1 = DB::connection($db_conn)->select("
        select
         Outlet_ID                                                AS Outlet_ID,
         Outlet_Code                                              AS Outlet_Code,
         Outlet_Name                                              AS Outlet_Name,
         Outlet_Name_Bn                                           AS Outlet_Name_Bn,
         distance_in_km                                           AS distance_in_km,
         Owner_Name                                               AS Owner_Name,
         Mobile_No                                                AS Mobile_No,
         Outlet_Address                                           AS Outlet_Address,
         Outlet_Address_Bn                                        AS Outlet_Address_Bn,
         Outlet_imge_ln                                           AS Outlet_imge_ln,
         refrigerator                                             AS refrigerator,
         shop_sign                                                AS shop_sign,
         geo_lat                                                  AS geo_lat,
         geo_lon                                                  AS geo_lon
  FROM ( select t1.id                  AS Outlet_ID,
         t1.site_code             AS Outlet_Code,
         t1.site_name             AS Outlet_Name,
        TRIM(t1.site_olnm)        AS Outlet_Name_Bn,
               ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                 * COS( RADIANS( t1.geo_lat ) )
                 * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                 + SIN( RADIANS( $request->geo_lat  ) )
                   * SIN( RADIANS( t1.geo_lat ) )
           )
           * 6371
         ) AS distance_in_km,
   t1.site_ownm AS Owner_Name,
  t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.site_olad AS Outlet_Address_Bn,
  t1.site_imge AS Outlet_imge_ln,
  t1.site_isfg AS refrigerator,
  t1.site_issg AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
  from tm_site_registration as t1
 where t1.mktm_id in($d) and MBRContains(st_makeEnvelope (
  point(($request->geo_lon + 1 / 111.1), ($request->geo_lat + 1 / 111.1)),
  point(($request->geo_lon - 1 / 111.1), ($request->geo_lat - 1 / 111.1)) 
), POINT( geo_lon, geo_lat ))AND t1.lfcl_id=1 AND t1.`site_name`LIKE'%$Search_key%'HAVING distance_in_km < .5
order by distance_in_km asc limit 20 )AS t22
UNION ALL 
select  t1.id                                                         AS Outlet_ID,
         t1.locd_code                                                 AS Outlet_Code,
         t1.locd_name                                                 AS Outlet_Name,
        TRIM(t1.locd_name)                                            AS Outlet_Name_Bn,
               ( ACOS( COS( RADIANS( $request->geo_lat  ) )
                 * COS( RADIANS( t1.geo_lat ) )
                 * COS( RADIANS( t1.geo_lon ) - RADIANS( $request->geo_lon ) )
                 + SIN( RADIANS( $request->geo_lat ) )
                   * SIN( RADIANS( t1.geo_lat ) )
           )
           * 6371
         ) AS distance_in_km,
   '' AS Owner_Name,
  '' AS Mobile_No,
  t1.geo_adrs AS Outlet_Address,
  t1.geo_adrs AS Outlet_Address_Bn,
  '' AS Outlet_imge_ln,
  '0' AS refrigerator,
  '0' AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
  from tm_locd as t1
 where t1.locd_code LIKE'%$Search_key%' AND t1.lfcl_id=1 HAVING distance_in_km < .5 order by distance_in_km asc limit 200;
");
            }

        } else {

            $data1 = DB::connection($db_conn)->select("
        select   t1.id            AS Outlet_ID,
        t1.site_code              AS Outlet_Code,
        t1.site_name              AS Outlet_Name,
        TRIM(t1.site_olnm)        AS Outlet_Name_Bn,
        '0'                        AS distance_in_km,
        t1.site_ownm              AS Owner_Name,
        t1.site_mob1 AS Mobile_No,
  t1.site_adrs AS Outlet_Address,
  t1.site_olad AS Outlet_Address_Bn,
  t1.site_imge AS Outlet_imge_ln,
  t1.site_isfg AS refrigerator,
  t1.site_issg AS shop_sign,
  t1.geo_lat,
  t1.geo_lon
  from tm_site as t1
 where t1.site_name LIKE'%$Search_key%'OR t1.site_code LIKE'%$Search_key%' AND t1.lfcl_id=1  order by t1.site_name ;
");

        }
        return Array(
            "OutletBank_Table" => array("data" => $data1, "action" => $request->country_id),
        );

    }


    public
    function User_SetUpMarket(Request $request)
    {
        /*SELECT
    t2.`id` as market_id,
    t2.mktm_name AS `market_name`
    FROM `tm_mktm`t2 JOIN tl_srmd t1 ON(t2.id=t1.mktm_id)
    WHERE t1.`lfcl_id` = '1' AND
    t1.`srmd_day` = '$user_day_name'
    GROUP BY t2.`id` ,t2.mktm_name*/

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $user_day_name = $request->user_day_name;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
 SELECT
  t2.`id` as market_id,
  t2.mktm_name AS `market_name`
 FROM `tm_mktm`t2 JOIN tl_srmd t1 ON(t2.id=t1.mktm_id)
 WHERE t1.`lfcl_id` = '1' AND
 t1.`srmd_day` = '$user_day_name'AND t1.aemp_id='$request->EmpId'
 GROUP BY t2.`id` ,t2.mktm_name
");

        }
        return $data1;

    }

    public
    function UsingThana_GetMarket(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $send_district_code = $request->send_district_code;
        $send_thana_code = $request->send_thana_code;
        if ($db_conn != '') {
            /* $data1 = DB::connection($db_conn)->select("
    SELECT t1.mktm_code,t1.mktm_name
    FROM tm_mktm t1 JOIN tm_ward t2 ON(t1.ward_id=t2.id)
    JOIN tm_than t3 ON(t2.than_id=t3.id)
    WHERE  t3.id='$send_thana_code'
    GROUP BY t1.mktm_code ,t1.mktm_name
    ");*/

            $data1 = DB::connection($db_conn)->select("
SELECT t3.id AS mktm_code,t3.mktm_name
FROM tm_than t1 INNER JOIN tm_ward t2 ON (t1.id=t2.than_id)
INNER JOIN tm_mktm t3 ON (t2.id=t3.ward_id)
 WHERE t1.id='$send_thana_code'
 GROUP BY t3.id ,t3.mktm_name
");

        }
        return $data1;

    }

    public
    function aroundOutlet_UsingMarket(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
   SELECT
       t1.id                                                        AS Outlet_ID,
       t1.site_code                                                 AS Outlet_Code,
       TRIM(t1.site_name)                                                AS Outlet_Name,
       t1.site_mob1                                                 AS Mobile,
       t2.mktm_name                                                 AS Market_Name,
      TRIM(t1.site_olad)                                          AS Address,
       t1.geo_lat                                                   AS Lat,
       t1.geo_lon                                                   AS Lon,
       t1.site_ownm                                                 AS OwnerName
       FROM `tm_site` AS t1
       INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
       WHERE t1.`mktm_id`='$request->user_market_id'
       AND t1.site_code NOT LIKE '%U%'
       AND t1.site_code NOT LIKE '%N%'
       AND t1.`lfcl_id` = '1'
GROUP BY t1.id,
t1.site_code,
t1.site_name,
t1.site_mob1,
t2.mktm_name,
t1.site_olad,
t1.geo_lat,
t1.geo_lon,
t1.site_ownm ORDER BY t1.site_name
     ");

        }
        return $data1;

    }

    public
    function aroundOutlet_UsingSearch(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT
  t1.id                                                        AS Outlet_ID,
  t1.site_code                                                 AS Outlet_Code,
 TRIM( t1.site_name)                                           AS Outlet_Name,
  t1.site_mob1                                                 AS Mobile,
  t2.mktm_name                                                 AS Market_Name,
  TRIM(t1.site_olad)                                           AS Address,
  t1.geo_lat                                                   AS Lat,
  t1.geo_lon                                                   AS Lon,
  t1.site_ownm                                                 AS OwnerName
FROM `tm_site` AS t1
  INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
WHERE  t1.site_name LIKE'%$request->user_search_text%' 
AND t1.`mktm_id`='$request->user_market_id'
AND t1.site_code NOT LIKE '%U%'
AND t1.site_code NOT LIKE '%N%'
AND t1.lfcl_id='1'
GROUP BY t1.id,
t1.site_code,
t1.site_name,
t1.site_mob1,
t2.mktm_name,
t1.site_olad,
t1.geo_lat,
t1.geo_lon,
t1.site_ownm
     ");

        }
        return $data1;

    }

    public
    function GetManagers_SR(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t1.`id`AS Sr_id,t1.`aemp_usnm`AS Sr_Code,t1.`aemp_name`AS Sr_Name,t1.aemp_mob1 AS Sr_Mobile
     FROM `tm_aemp` t1 WHERE t1.`role_id`='1' AND t1.`aemp_mngr`=$request->MG_EmpId");

        }

        return $data1;

    }

    public
    function SrThanalinklist(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t1.id,t1.than_name 
     from tm_than t1 JOIN tl_srth t2 ON (t1.id=t2.than_id)
     WHERE t2.aemp_id=$request->emp_id");

        }
        return $data1;

    }

    public
    function promotionGalleryLinkList(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT concat(t1.id,t1.slgp_id,t1.zone_id,t1.`pmgl_imgl`) AS column_id,
     t1.`pmgl_imgl` AS image 
     FROM `tl_pmgl`t1 JOIN tl_sgsm t2 ON(t1.`slgp_id`=t2.slgp_id) AND t1.`zone_id`=t2.zone_id
     WHERE t2.aemp_id=$request->emp_id");

        }
        return Array(
            "tbld_promotion_gallery" => array("data" => $data1, "action" => $request->country_id),
        );
    }

    public
    function tutorialDataList(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $tutorial_master = DB::connection($db_conn)->select("
                   SELECT `id`,`ttop_name`,`ttop_code`,`ttop_vdid`,`ttop_vurl`, `ttop_desc` as description, `ttop_ythm` as youtubeThumb 
                   FROM `tm_ttop` 
                   WHERE `slgp_id`=$request->slgp_id AND 
                   `zone_id`=$request->zone_id AND 
                   `lfcl_id`=1
                   ");

            foreach ($tutorial_master as $index => $data1) {
                $tt_id = $data1->id;

                $data2 = DB::connection($db_conn)->select("
                       SELECT `id`, `ttop_id`,`qutn_name`,`qutn_opta`,`qutn_optb`,`qutn_optc`,`qutn_optd`,`qutn_crta`
                       FROM `tm_qutn` 
                       WHERE `ttop_id`=$tt_id
                        ");

                $tutorial_master[$index]->questionLists = $data2;

            }

            return $tutorial_master;
        }
    }

    public
    function reWardsOfferList(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t2.kpim_id,t1.`kpim_code`,t1.`kpim_name`,t2.kpid_maxp,t2.kpid_minp,t2.kpid_fqty,t2.kpid_disp,
     t2.kpid_disa,t3.id AS ktyp_id,t3.ktyp_name,t2.kfit_id,t4.kfit_code,t4.kfit_name,t4.kfit_pont
     FROM `tm_kpim`t1 JOIN tm_kpid t2 ON(t1.`id`=t2.kpim_id)
     JOIN tm_ktyp t3 ON(t1.`ktyp_id`=t3.id)
     JOIN tm_kfit t4 ON(t2.kfit_id=t4.id)
     WHERE t1.`slgp_id`=$request->slgp_id AND t1.`zone_id`=$request->zone_id AND t1.`kpim_sdat` <= CURDATE()
     AND t1.`kpim_edat` >= CURDATE() AND t1.`lfcl_id`=1
     ");

        }
        return $data1;
    }

    public
    function reWardsGiftList(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT `id`,`kfit_code`,`kfit_name`,`kfit_iqty`,`kfit_pont` 
     FROM tm_kfit t1
     WHERE t1.`slgp_id`=$request->slgp_id 
     AND t1.`zone_id`=$request->zone_id 
     AND t1.`lfcl_id`=1
     ");

        }
        return $data1;
    }

    public
    function reWardsStatementsDataList(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
     SELECT t2.id AS earn_pid,t2.kpim_name AS earn_point_name,
     t1.rwdd_epnt AS earn_point,t3.id AS use_pid,t3.kfit_name AS use_point_name,
     t1.rwdd_upnt AS use_point,
t1.rwdd_ptyp AS point_type,t1.`created_at`AS date_time
FROM `tt_rwdd`t1 left JOIN tm_kpim t2 ON(t1.kpim_id=t2.id) 
left JOIN tm_kfit t3 ON(t1.kfit_id=t3.id) 
WHERE t1.`aemp_id`=$request->emp_id AND 
date(t1.created_at)>= SUBDATE(DATE(NOW()), 7)
     ");

        }
        return $data1;
    }

    public
    function updateOutlet(Request $request)
    {
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
WHERE t2.rout_id=$request->route_id");
        }
        return $data1;

    }

    public
    function outletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select(
                "select t1.id as Category_Code,
t1.otcg_name as Category_Name 
from tm_otcg as t1");

        }
        return $data1;

    }

    public
    function RFLoutletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select(
                "select t1.id as Category_Code,
              t1.otcg_name as Category_Name 
              from tm_rtcg as t1 WHERE t1.lfcl_id=1
              ORDER BY t1.otcg_name");

        }
        return $data1;

    }

    public
    function MQRoutletCategory(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select(
                "select t1.id as Category_Code,
t1.otcg_name as Category_Name 
from tm_mtcg as t1");

        }
        return $data1;

    }

    public
    function govDistrict(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  id        AS district_code,
  dsct_name AS district_name
FROM tm_dsct
WHERE `lfcl_id` = 1
ORDER BY dsct_name");

        }
        return $data1;

    }

    public
    function govThana(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        // $dsct_id = $request->send_district_code;
        if ($db_conn != '') {
            $data2 = DB::connection($db_conn)->select(" 
     SELECT t1.id as ward_code,t1.`ward_name` as ward_name
     from tm_ward t1 JOIN tl_srwd t2 ON (t1.id=t2.ward_id)
     WHERE t2.aemp_id=$request->emp_id");

            $data1 = DB::connection($db_conn)->select("
     SELECT t1.id as Thana_Code,t1.than_name AS Thana_Name
     from tm_than t1 JOIN tl_srth t2 ON (t1.id=t2.than_id)
     WHERE t2.aemp_id=$request->emp_id");

        }
        return Array(
            "receive_data" => array("ward_data" => $data2, "thana_data" => $data1),
        );

    }

    public
    function govThana1(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        $district_Name = $request->send_district_Name;
        $district_Name1 = $request->send_district_getLocality;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT t2.id as Thana_Code,t2.than_name AS Thana_Name
FROM tm_dsct t1 JOIN tm_than t2 ON (t1.id=t2.dsct_id)
WHERE t1.dsct_name='$district_Name' OR t1.dsct_name='$district_Name1'
");

        }
        return $data1;

    }

    public
    function govWard(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $than_id = $request->send_thana_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
SELECT `id` as ward_code,`ward_name` as ward_name
FROM `tm_ward`
WHERE `LFCL_ID`='1' AND
      `THAN_ID`='$than_id' Order by ward_name");

        }
        return $data1;

    }

    public
    function market(Request $request)
    {
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $ward_id = $request->Ward_Code_Send;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  `id` as market_id,
  mktm_name AS `market_name`
FROM `tm_mktm`
WHERE `lfcl_id` = '1' AND
      `ward_id` = '$ward_id'
ORDER BY mktm_name");

        }
        return $data1;

    }

    public
    function GetOutletNameAndID(Request $request)
    {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT t1.site_name AS OutLet_Name,t2.site_id AS OutLet_ID
        FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
		where t2.ordm_date = '$Order_Date' 
        AND t2.aemp_id='$SR_ID'
        AND t2.lfcl_id=1
        GROUP BY t1.site_name,t2.site_id
        ");

        }
        return $data1;

    }

    public
    function employeeOutletOrderInfo(Request $request)
    {


        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $aemp_id = $request->emp_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $order_master = DB::connection($db_conn)->select("
       SELECT t1.site_name AS OutLet_Name,t2.site_id AS OutLet_ID,t2.ordm_date
FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
where t2.ordm_date = '$start_date'
      AND t2.aemp_id='$aemp_id'
      AND t2.lfcl_id=1
GROUP BY t2.ordm_date,t2.site_id
       ");


            foreach ($order_master as $index => $data1) {
                $order_OutLet_Name = $data1->OutLet_Name;
                $site_id = $data1->OutLet_ID;
                $order_date = $data1->ordm_date;
                $data2 = DB::connection($db_conn)->select("
SELECT t2.id AS Order_ID,t2.ordm_ornm AS Order_Code,t2.ordm_date,t1.id as parentId
FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
where t2.ordm_date = '$start_date'
      AND t2.aemp_id='$aemp_id'
      AND t2.lfcl_id=1
      AND t2.site_id=$site_id
GROUP BY t2.ordm_date,t2.ordm_ornm");

                $order_master[$index]->orderIdLists = $data2;
                // $order_master[$index]->totla_amount = number_format(array_sum(array_column($data2, 'price')), 2, '.', '');
                // $order_master[$index]->totla_discount = number_format(array_sum(array_column($data2, 'discont')), 2, '.', '');

            }

            // return Array("data" => $order_master);
            return $order_master;
        }
    }

    public
    function GetOutletWiseOrderID(Request $request)
    {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;
        $OutLet_ID = $request->OutLet_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
        SELECT t2.ordm_ornm AS Order_Code,t2.id AS Order_ID
        FROM  tm_site t1 JOIN  tt_ordm t2 ON(t1.id=t2.site_id)
		where t2.ordm_date = '$Order_Date' 
        AND t2.aemp_id='$SR_ID'
        AND t2.site_id='$OutLet_ID'
        AND t2.lfcl_id=1
        GROUP BY t2.id,t2.ordm_ornm
        ");

        }
        return $data1;

    }

    public
    function GetOutletWiseOrderDetails(Request $request)
    {
        $data1 = array();
        $Order_Date = $request->S_Date;
        $SR_ID = $request->SR_ID;
        $OutLet_ID = $request->OutLet_ID;
        $Order_ID = $request->Order_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
       SELECT 
t2.slgp_id AS Group_Id,
t2.dlrm_id AS Dealer_Id,
t2.rout_id AS Route_Id,
t1.`amim_id`AS Item_Id,
t3.amim_code AS Item_Code,
t3.amim_name As Item_Name,
t1.`ordd_qnty`As Order_Qty,
t1.`ordd_qnty`As deliveryQty,
t1.`ordd_duft`As Item_Factor,
t1.`ordd_uprc`As Unit_Price,
t1.ordd_opds As Promotional_Discount,
t1.ordd_spdo As Special_Discount,
t1.prom_id As prom_id,
t1.`ordd_oamt`AS Order_Amt,
t1.`ordd_oamt` AS deliveryAmount,
t2.geo_lat,
t2.geo_lon,
'0'AS Item_Stock_B_Qty
       FROM `tt_ordd`t1 JOIN tt_ordm t2 ON(t2.id=t1.`ordm_id`)
       LEFT JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
       WHERE t2.id='$Order_ID' AND t2.ordm_date = '$Order_Date' 
       AND t2.aemp_id='$SR_ID'
       AND t2.lfcl_id=1
       AND t3.lfcl_id=1
       GROUP BY t2.slgp_id ,t1.ordd_spdo,
t2.dlrm_id ,
t2.rout_id ,
t1.`amim_id`,
t3.amim_code ,
t3.amim_name ,
t1.`ordd_qnty`,
t1.`ordd_duft`,
t1.`ordd_uprc`,
t1.ordd_opds ,
t1.`ordd_oamt`,
t2.geo_lat,
t2.geo_lon,t1.prom_id
        ");

        }
        return $data1;

    }

    public
    function GetChallanWiseOrderData(Request $request)
    {
        $data1 = array();
        $From_Date = $request->From_Date;
        $To_Date = $request->To_Date;
        $SR_ID = $request->SR_ID;

        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
     SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity, SUM( t1.ordd_qnty ) AS totalDeliveryItemQuantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
      FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
      AND t1.ordd_oamt!=0
      AND t3.lfcl_id=1 
      AND t2.lfcl_id!=11
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date
      UNION ALL
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity, SUM( t1.ordd_qnty ) AS totalDeliveryItemQuantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
       FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
       AND t1.ordd_oamt=0
      AND t3.lfcl_id=1
      AND t2.lfcl_id !=11 
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date ");
            /* $data1 = DB::connection($db_conn)->select("
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
      FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
      AND t1.ordd_oamt!=0
      AND t3.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date
      UNION ALL
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,if(t2.lfcl_id=11,'2',t2.lfcl_id) AS Delivery_Status,t2.ordm_date
       FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
       AND t1.ordd_oamt=0
      AND t3.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date");*/

        }

        return $data1;

    }

}