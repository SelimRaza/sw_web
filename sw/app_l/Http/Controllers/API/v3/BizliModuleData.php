<?php

/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v3;

use App\BusinessObject\Attendance;
use App\BusinessObject\DlearProfileAdd;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
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
use Illuminate\Support\Facades\DB;
use AWS;

class BizliModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    /*
    *   API MODEL V3 [START]
    */

    public function Get_AllElectricianProfileUserList(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $emp_id = $request->emp_id;
        $emp_role = $request->emp_role_id;
        if ($db_conn != '') {

            $data1 = DB::connection($db_conn)->select("
                                SELECT
                                t1.id,
                                t1.`dlrp_frno`,
                                t1.`dlrp_edob`,
                                t1.`dlrp_name`,
                                t1.`dlrp_fnam`,
                                t1.`dlrp_prad`,
                                t1.`dlrp_pmad`,
                                t1.`dlrp_mobn`,
                                t1.`dlrp_nidn`,
                                t1.`dlrp_expr`,
                                t1.`dlrp_educ`,
                                t4.than_name AS dlrp_prmt,
                                t2.than_name AS than_name,
                                t5.dsct_name AS Prs_dst,
                                t6.dsct_name AS Par_dst,
                                t1.date_issue AS created_at,
                                t1.`dlrp_eimg`,
                                t1.`dlrp_simg`,
                                t1.`dlrp_nfmg`,
                                t1.`dlrp_nbmg`,
                                t1.dlrp_bldg,
                                t1.dlrp_aprv,
                                t7.mtyp_name AS mistiri_type
                                FROM `tm_bizli` t1
                                JOIN tm_than t2 ON (t1.`dlrp_prmt` = t2.id)
                                JOIN tm_than t4 ON (t1.`dlrp_prst` = t4.id)
                                JOIN tm_dsct t5 ON (t1.`dlrp_prsd` = t5.id)
                                JOIN tm_dsct t6 ON (t1.`dlrp_prmd` = t6.id)
                                JOIN tm_aemp t3 ON (t1.dlrp_cusr = t3.id)
                                LEFT JOIN tm_mtyp t7 ON (t1.dlrp_mtyp=t7.id)
                                WHERE (t1.dlrp_cusr='$emp_id' OR t3.aemp_mngr='$emp_id')
                                ORDER BY t1.`id` DESC LIMIT 200
                            ");
        }
        return $data1;
    }

    public function AllElectricianSearchUserList(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $emp_id = $request->emp_id;
        $emp_role = $request->emp_role_id;
        $searchText = $request->searchText;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT t1.id,t1.`dlrp_frno`,t1.`dlrp_edob`, t1.`dlrp_name`,t1.`dlrp_fnam`,t1.`dlrp_prad`,t1.`dlrp_pmad`,
                            t1.`dlrp_mobn`,t1.`dlrp_nidn`,t1.`dlrp_expr`,t1.`dlrp_educ`,t1.`dlrp_prmt`,t2.than_name,
                            t1.date_issue AS created_at,t1.`dlrp_eimg`,t1.`dlrp_simg`,t1.`dlrp_nfmg`,t1.`dlrp_nbmg`,t1.dlrp_bldg,t1.dlrp_aprv,
                            t4.mtyp_name AS mistiri_type
                            FROM `tm_bizli`t1 JOIN tm_than t2 ON(t1.`dlrp_prmt`=t2.id)
                            JOIN tm_aemp t3 ON(t1.dlrp_cusr=t3.id)
                                LEFT JOIN tm_mtyp t4 ON (t1.dlrp_mtyp=t4.id)
                            WHERE (t1.dlrp_cusr='$emp_id' OR t3.aemp_mngr='$emp_id')
                            AND (t1.dlrp_frno LIKE'%$searchText%' OR t1.dlrp_name LIKE'%$searchText%' OR t1.dlrp_mobn LIKE'%$searchText%')
                            ORDER BY t1.`id` DESC 
                        ");
        }
        return $data1;
    }

    public function Submit_Approved(Request $request){
        
        $country = (new Country())->country($request->country_id);

        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {

                $mistiri = DlearProfileAdd::on($db_conn)->where(['dlrp_frno' => $request->form_no])->first();
                $mistiri->dlrp_frno = $request->form_no;
                $mistiri->dlrp_aprv = 1;
                $mistiri->dlrp_eusr = $request->emp_id;

                $mistiri->save();
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Approved Successfully",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public function Get_Last_UserSerial(Request $request){

        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $emp_id = $request->emp_id;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT `dlrp_frno`AS last_serial_no
                            FROM `tm_bizli`
                            WHERE dlrp_cusr='$emp_id'
                            ORDER BY id DESC LIMIT 1
                        ");
        }
        return $data1;
    }

    public function govDistrict(Request $request)
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
                            ORDER BY dsct_name
                        ");

        }
        return $data1;

    }

    public function govThana(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        $dsct_Name = $request->send_district_Name;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("SELECT
                            `id` as Thana_Code,
                            than_name AS Thana_Name
                            FROM `tm_than`
                            WHERE `lfcl_id` = 1 AND
                            `dsct_id` = '$dsct_id'
                            ORDER BY than_name
                        ");
        }
        return $data1;

    }

    public function mistiri_type(Request $request){
        $data1 = array();
        $country = (new Country())->country($request->country_id);
        $db_conn = $country->cont_conn;
        $dsct_id = $request->send_district_code;
        $dsct_Name = $request->send_district_Name;
        if ($db_conn != '') {
            $data1 = DB::connection($db_conn)->select("
                            SELECT id AS m_id,mtyp_name AS m_type FROM tm_mtyp
                            WHERE `lfcl_id` = 1
                        ");
        }
        return $data1;
    }

    public function ElectricianOpenSaveData(Request $request){
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {

                $DlearProfile = new DlearProfileAdd();
                $DlearProfile->setConnection($db_conn);

                $DlearProfile->dlrp_prsd = $request->district_prsd_code;
                $DlearProfile->dlrp_prmd = $request->district_prmd_code;
                $DlearProfile->dlrp_dlrd = $request->district_dlrd_code;
                $DlearProfile->dlrp_prst = $request->thana_prst_code;
                $DlearProfile->dlrp_prmt = $request->thana_prmt_code;
                $DlearProfile->dlrp_dlrt = $request->thana_dlrt_code;

                $DlearProfile->dlrp_frno = '0';

                $DlearProfile->dlrp_mtyp = $request->M_Type;

                $DlearProfile->dlrp_name = $request->em_name;
                $DlearProfile->lfcl_id = 1;
                $DlearProfile->dlrp_aprv = 0;
                $DlearProfile->date_issue = date('Y-m-d');
                $DlearProfile->dlrp_mobn = $request->em_mob;
                $DlearProfile->dlrp_ocup = $request->em_oucp;
                $DlearProfile->dlrp_bldg = $request->em_bldg;
                $DlearProfile->dlrp_educ = $request->em_educ;
                $DlearProfile->dlrp_fnam = $request->em_fname;
                $DlearProfile->dlrp_prad = $request->prs_addres;
                $DlearProfile->dlrp_pmad = $request->prm_addres;
                $DlearProfile->dlrp_edob = $request->em_dob;
                $DlearProfile->dlrp_nidn = $request->em_nid;
                $DlearProfile->dlrp_expr = $request->em_expr;
                $DlearProfile->dlrp_refn = $request->dlr_ref;
                $DlearProfile->dlrp_dlcd = $request->dlr_code;
                $DlearProfile->dlrp_opnm = $request->dlr_opnm;
                $DlearProfile->dlrp_opid = $request->dlr_opcd;
                $DlearProfile->dlrp_orgn = $request->dlr_org;
                $DlearProfile->dlrp_eimg = $request->user_image;
                $DlearProfile->dlrp_simg = $request->user_signature;
                $DlearProfile->dlrp_nfmg = $request->user_nid_front;
                $DlearProfile->dlrp_nbmg = $request->user_nid_back;
                $DlearProfile->dlrp_cusr = $request->up_emp_id;
                $DlearProfile->dlrp_eusr = $request->up_emp_id;
                $DlearProfile->cont_id = $request->country_id;

                $DlearProfile->dlrp_var1 = '-';
                $DlearProfile->dlrp_var2 = 1;

                $DlearProfile->save();

                $From_No = "U" . date('ymd') . str_pad($DlearProfile->id, 8, '0', STR_PAD_LEFT);

                $DlearProfile->dlrp_frno = $From_No;
                $DlearProfile->save();

                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Registration Successful",

                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    public function Submit_Update_Data(Request $request){

        $country = (new Country())->country($request->country_id);

        $db_conn = $country->cont_conn;
        if ($db_conn != '') {
            DB::connection($db_conn)->beginTransaction();
            try {
                DB::connection($db_conn)->table('tm_bizli')->where(['id' => $request->form_id
                ])->update([
                    'dlrp_prsd' => $request->dlrp_prsd,
                    'dlrp_prmd' => $request->dlrp_prmd,
                    'dlrp_dlrd' => $request->dlrp_dlrd,
                    'dlrp_prst' => $request->dlrp_prst,
                    'dlrp_prmt' => $request->dlrp_prmt,
                    'dlrp_dlrt' => $request->dlrp_dlrt,
                    'dlrp_name' => $request->dlrp_name,
                    'dlrp_mobn' => $request->dlrp_mobn,
                    'dlrp_ocup' => $request->dlrp_ocup,
                    'dlrp_bldg' => $request->dlrp_bldg,
                    'dlrp_educ' => $request->dlrp_educ,
                    'dlrp_fnam' => $request->dlrp_fnam,
                    'dlrp_prad' => $request->dlrp_prad,
                    'dlrp_pmad' => $request->dlrp_pmad,
                    'dlrp_edob' => $request->dlrp_edob,
                    'dlrp_nidn' => $request->dlrp_nidn,
                    'dlrp_expr' => $request->dlrp_expr,
                    'dlrp_refn' => $request->dlrp_refn,
                    'dlrp_dlcd' => $request->dlrp_dlcd,
                    'dlrp_opnm' => $request->dlrp_opnm,
                    'dlrp_opid' => $request->dlrp_opid,
                    'dlrp_orgn' => $request->dlrp_orgn,
                    'dlrp_eusr' => $request->emp_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Update Successful",
                );
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }

    /*
    *   API MODEL V3 [END]
    */

     

}