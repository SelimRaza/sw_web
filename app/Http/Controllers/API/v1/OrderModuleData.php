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

    public function outletSave(Request $request)
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

                    DB::connection($db_conn)->table('tt_ordm')->where(['aemp_id' => $outletData_child[0]->SR_ID,
                        'ordm_date' => $outletData_child[0]->Order_Date, 'lfcl_id' => 1])->update(['lfcl_id' => 11]);


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

    public function RFLOutletSave(Request $request)
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

                    $site_code = '7' . str_pad($outletData->Outlet_Code, 9, '7', STR_PAD_LEFT);

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

                            $SiteLog = new SiteLogRfl();
                            $SiteLog->setConnection($db_conn);
                            $SiteLog->site_id = $site->id;
                            $SiteLog->aemp_iusr = $outletData->up_emp_id;
                            $SiteLog->save();

                            DB::connection($db_conn)->commit();
                            $result_data = array(
                                'success' => 1,
                                'message' => "  Outlet Open Successful  ",
                            );
                        } else {
                            $result_data = array(
                                'success' => 0,
                                'message' => " Outlet Already Opened \n Try Again !!!  ",
                            );
                        }
                    } else {
                        $result_data = array(
                            'success' => 0,
                            'message' => " Outlet Already Opened \n Using this Mobile Number !!!  ",
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
    function RFLOpenOutletCount(Request $request)
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
WHERE  t1.aemp_iusr='$SR_ID' AND Date(t1.`created_at`)='$Date' 
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
                    $site->site_mob2 = $outletData->Mobile_No_2 == '' ? "" : $outletData->Mobile_No_2;
                    $site->site_imge = $outletData->Outlet_Image != "" ? $outletData->Outlet_Image : '';
                    $site->site_isfg = $outletData->Refrigerator == "Yes" ? 1 : 0;
                    $site->site_issg = $outletData->ShopSining == "Yes" ? 1 : 0;
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
        $result_data = array(
            'success' => 10,
            'message' => "Fail to Remove From Route",
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
    function MasterDataNew_Three1(Request $request)
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
  t5.acmp_id                                                                                 AS OU_ID,
  t1.rpln_day                                                                                AS Day
FROM tl_rpln AS t1
  INNER JOIN tl_sgsm AS t2 ON t1.aemp_id = t2.aemp_id
  INNER JOIN tm_rout AS t3 ON t1.rout_id = t3.id
  INNER JOIN tm_base AS t4 ON t3.base_id = t4.id
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
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
  INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
WHERE t3.lfcl_id = '1' AND t1.aemp_id =  $request->emp_id AND t2.pldt_tppr>0
GROUP BY t1.plmt_id, t3.id, t5.issc_name, t3.amim_name, t2.pldt_tppr, t2.amim_duft, t5.issc_seqn,t2.amim_dunt,t2.amim_runt,t3.amim_code ");
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
  INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id AND t2.amim_id = t4.amim_id
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
  WHERE t4.aemp_id='$request->emp_id' AND t4.than_id='$request->than_id';");
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
  INNER JOIN tm_prom AS t4 ON t3.prom_id = t4.id AND t2.slgp_id = t4.slgp_id
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
  INNER JOIN tm_prom AS t2 ON t1.slgp_id = t2.slgp_id
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
    function aroundOutlet(Request $request)
    {
        $data1 = array();
        /*     $country = (new Country())->country($request->country_id);
             $db_conn = $country->cont_conn;
             if ($db_conn != '') {
                 $data1 = DB::connection($db_conn)->select("
     SELECT
       t1.id                                                        AS Outlet_ID,
       t1.site_code                                                 AS Outlet_Code,
       t1.site_name                                                 AS Outlet_Name,
       t1.site_mob1                                                 AS Mobile,
       t2.mktm_name                                                 AS Market_Name,
       t2.site_ownm                                                 AS Owner_name,
       t1.geo_lat                                                   AS Lat,
       t1.geo_lon                                                   AS Lon,
       getDistance(t1.geo_lat, t1.geo_lon, $request->geo_lat, $request->geo_lon) AS distance
     FROM tm_site AS t1
       INNER JOIN tm_mktm AS t2 ON t1.mktm_id = t2.id
     HAVING distance < 0.1
     ORDER BY distance");

             }*/


        return $data1;

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
from tm_rtcg as t1 ORDER BY t1.otcg_name");

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
        $dsct_id = $request->send_district_code;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
  `id` as Thana_Code,
  than_name AS Thana_Name
FROM `tm_than`
WHERE `lfcl_id` = 1 AND
      `dsct_id` = '$dsct_id'
ORDER BY than_name");

        }
        return $data1;

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
            $data1 = DB::connection($db_conn)->select("SELECT `id` as ward_code,`ward_name` as ward_name
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
t1.`ordd_duft`As Item_Factor,
t1.`ordd_uprc`As Unit_Price,
t1.ordd_opds As Promotional_Discount,
t1.`ordd_oamt`AS Order_Amt,
t2.geo_lat,
t2.geo_lon,
'0'AS Item_Stock_B_Qty
       FROM `tt_ordd`t1 JOIN tt_ordm t2 ON(t2.id=t1.`ordm_id`)
       LEFT JOIN tm_amim t3 ON(t1.`amim_id`=t3.id)
       WHERE t2.id='$Order_ID' AND t2.ordm_date = '$Order_Date' 
       AND t2.aemp_id='$SR_ID'
       AND t2.lfcl_id=1
       AND t3.lfcl_id=1
       GROUP BY t2.slgp_id ,
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
t2.geo_lon
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
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,t2.lfcl_id AS Delivery_Status,t2.ordm_date
      FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
      AND t1.ordd_oamt!=0
      AND t3.lfcl_id=1 AND t2.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date
      UNION ALL
      SELECT t2.slgp_id AS Group_ID,t2.dlrm_id AS Dealer_ID,t1.amim_id AS Product_Code, t3.amim_name AS Product_Item_Name,t3.amim_name AS Product_Catagory,
      t3.amim_code AS Item_Code,ROUND( t1.ordd_uprc, 2 ) AS Product_Rate, SUM( t1.ordd_qnty ) AS Total_Order_Item_Quantity,
      ROUND( SUM( t1.ordd_oamt ) , 2 ) AS Total_Order_Item_Price ,COUNT(t1.`ordm_ornm`)AS Memo_Count,t2.lfcl_id AS Delivery_Status,t2.ordm_date
       FROM tt_ordd AS t1
      INNER JOIN tt_ordm AS t2 ON t1.ordm_id = t2.id
      INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id 
      WHERE t2.ordm_date
      BETWEEN '$From_Date'
      AND '$To_Date'
      AND t2.aemp_id = '$SR_ID'
       AND t1.ordd_oamt=0
      AND t3.lfcl_id=1 AND t2.lfcl_id=1
      GROUP BY t2.ordm_date, t1.amim_id,t2.slgp_id,t2.dlrm_id,t3.amim_name,t2.lfcl_id,t1.ordd_uprc,t2.ordm_date");

        }

        return $data1;

    }

}