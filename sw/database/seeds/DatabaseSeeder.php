<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run($cont_id)
    {
        $country = (new \App\MasterData\Country())->country($cont_id);
        if ($country!=null){
            DB::connection($country->cont_conn)->table('tm_lfcl')->insertOrIgnore([
                ['id' => '1', 'lfcl_name' => 'Active', 'lfcl_code' => 'Active',],
                ['id' => '2', 'lfcl_name' => 'Inactive', 'lfcl_code' => 'Inactive',],
                ['id' => '3', 'lfcl_name' => 'Picker Assigned', 'lfcl_code' => 'Picker Assigned',],
                ['id' => '4', 'lfcl_name' => 'Logistics Checked Ou', 'lfcl_code' => 'Logistics Checked Ou',],
                ['id' => '5', 'lfcl_name' => 'EOT', 'lfcl_code' => 'EOT',],
                ['id' => '6', 'lfcl_name' => 'Discrepancy Order Cr', 'lfcl_code' => 'Discrepancy Order Cr',],
                ['id' => '7', 'lfcl_name' => 'New', 'lfcl_code' => 'New',],
                ['id' => '8', 'lfcl_name' => 'Available for RouteP', 'lfcl_code' => 'Available for RouteP',],
                ['id' => '9', 'lfcl_name' => 'Credit Limit Blocked', 'lfcl_code' => 'Credit Limit Blocked',],
                ['id' => '10', 'lfcl_name' => 'Auto IR', 'lfcl_code' => 'Auto IR',],
                ['id' => '11', 'lfcl_name' => 'Delivered', 'lfcl_code' => 'Delivered',],
                ['id' => '12', 'lfcl_name' => 'Waiting for Verifica', 'lfcl_code' => 'Waiting for Verifica',],
                ['id' => '13', 'lfcl_name' => 'Planned', 'lfcl_code' => 'Planned',],
                ['id' => '14', 'lfcl_name' => 'Over Due Blocked', 'lfcl_code' => 'Over Due Blocked',],
                ['id' => '15', 'lfcl_name' => 'Order Cancel Auto', 'lfcl_code' => 'Order Cancel Auto',],
                ['id' => '16', 'lfcl_name' => 'Order Cancel SR', 'lfcl_code' => 'Order Cancel SR',],
                ['id' => '17', 'lfcl_name' => 'Special FOC/Discount', 'lfcl_code' => 'Special FOC/Discount',],
                ['id' => '18', 'lfcl_name' => 'Order Cancel Depot', 'lfcl_code' => 'Order Cancel Depot',],
                ['id' => '19', 'lfcl_name' => 'Transferred', 'lfcl_code' => 'Transferred',],
                ['id' => '20', 'lfcl_name' => 'Driver Checked Out', 'lfcl_code' => 'Driver Checked Out',],
                ['id' => '21', 'lfcl_name' => 'Order Cancel SV', 'lfcl_code' => 'Order Cancel SV',],
                ['id' => '22', 'lfcl_name' => 'Verified', 'lfcl_code' => 'Verified',],
                ['id' => '23', 'lfcl_name' => 'On Transit', 'lfcl_code' => 'On Transit',],
                ['id' => '24', 'lfcl_name' => 'Reject', 'lfcl_code' => 'Reject',],
                ['id' => '25', 'lfcl_name' => 'Trip Close', 'lfcl_code' => 'Trip Close',],
                ['id' => '26', 'lfcl_name' => 'Paid', 'lfcl_code' => 'Paid',],
                ['id' => '27', 'lfcl_name' => 'Cash Collected', 'lfcl_code' => 'Cash Collected',],
                ['id' => '28', 'lfcl_name' => 'Prepared', 'lfcl_code' => 'Prepared',],
                ['id' => '29', 'lfcl_name' => 'Ready for Aplication', 'lfcl_code' => 'Ready for Aplication',],
                ['id' => '30', 'lfcl_name' => 'Partially Applied', 'lfcl_code' => 'Partially Applied',],
                ['id' => '31', 'lfcl_name' => 'Picker Verified', 'lfcl_code' => 'Picker Verified',],
                ['id' => '32', 'lfcl_name' => 'Stock Settlement', 'lfcl_code' => 'Stock Settlement',],
                ['id' => '33', 'lfcl_name' => 'Not Used', 'lfcl_code' => 'Not Used',],
                ['id' => '34', 'lfcl_name' => 'Not Used', 'lfcl_code' => 'Not Used',],
                ['id' => '35', 'lfcl_name' => 'New Site Order', 'lfcl_code' => 'New Site Order',],
                ['id' => '36', 'lfcl_name' => 'Full Applied', 'lfcl_code' => 'Full Applied',],
                ['id' => '37', 'lfcl_name' => 'On Account', 'lfcl_code' => 'On Account',],
            ]);
            DB::table('tm_cont')->insertOrIgnore([
                ['id' => "$country->id", 'cont_name' => "$country->cont_name", 'cont_code' => "$country->cont_code", 'cont_conn' => "$country->cont_conn", 'cont_imgf' => "$country->cont_imgf", 'cont_dgit' => "$country->cont_dgit", 'cont_cncy' => "$country->cont_cncy", 'cont_rund' => "$country->cont_rund",],
            ]);
            DB::connection($country->cont_conn)->table('tm_cont')->insertOrIgnore([
                ['id' => "$country->id", 'cont_name' => "$country->cont_name", 'cont_code' => "$country->cont_code", 'cont_conn' => "$country->cont_conn", 'cont_imgf' => "$country->cont_imgf", 'cont_dgit' => "$country->cont_dgit", 'cont_cncy' => "$country->cont_cncy", 'cont_rund' => "$country->cont_rund",],
            ]);
            DB::connection($country->cont_conn)->statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::connection($country->cont_conn)->table('tm_aemp')->insertOrIgnore([
                ['id' => '1', 'aemp_name' => 'admin', 'aemp_onme' => 'admin', 'aemp_stnm' => 'admin', 'aemp_mob1' => '01769696355', 'aemp_dtsm' => '', 'aemp_emal' => 'mis84@mis.prangroup.com', 'aemp_otml' => '', 'aemp_emcc' => '', 'aemp_lued' => '1081',
                    'edsg_id' => '1', 'role_id' => '1', 'aemp_usnm' => 'admin', 'aemp_pimg' => '', 'aemp_picn' => '', 'aemp_mngr' => '1', 'aemp_lmid' => '1', 'aemp_aldt' => '0', 'aemp_lcin' => '127',
                    'aemp_lonl' => '0', 'aemp_utkn' => '', 'site_id' => '0', 'aemp_crdt' => '0', 'aemp_issl' => '0', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'amng_id' => '0', 'zone_id' => '0', 'aemp_iusr' => '1',
                    'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::connection($country->cont_conn)->table('tm_edsg')->insertOrIgnore([
                ['id' => '1', 'edsg_name' => 'SR', 'edsg_code' => '1', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_role')->insertOrIgnore([
                ['id' => '1', 'role_name' => 'SR', 'role_code' => '1', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'role_name' => 'TSM', 'role_code' => '2', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '3', 'role_name' => 'DSM', 'role_code' => '3', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '4', 'role_name' => 'AGM', 'role_code' => '4', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '5', 'role_name' => 'HOS', 'role_code' => '5', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '6', 'role_name' => 'COO', 'role_code' => '6', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '7', 'role_name' => 'MD', 'role_code' => '7', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '8', 'role_name' => 'CEO', 'role_code' => '8', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '9', 'role_name' => 'MIS', 'role_code' => '9', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_trnt')->insertOrIgnore([
                ['id' => '1', 'trnt_name' => 'Debit', 'trnt_code' => 'Debit', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'trnt_name' => 'Credit', 'trnt_code' => 'Credit', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],

            ]);
            DB::connection($country->cont_conn)->table('tm_clmt')->insertOrIgnore([
                ['id' => '1', 'clmt_name' => 'Full Applied', 'clmt_code' => 'Full Appli', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'clmt_name' => 'On Account', 'clmt_code' => 'On Account', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_lods')->insertOrIgnore([
                ['id' => '1', 'lods_name' => 'Sales able', 'lods_code' => 'Sales able', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'lods_name' => 'Damage', 'lods_code' => 'Damage', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_lodt')->insertOrIgnore([
                ['id' => '1', 'lodt_name' => 'Load Request', 'lodt_code' => 'Load Reque', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'lodt_name' => 'Unload Stock', 'lodt_code' => 'Unload Sto', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '3', 'lodt_name' => 'Unload GRV Damage', 'lodt_code' => 'Unload GRV', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '4', 'lodt_name' => 'Move GRV To Van', 'lodt_code' => 'Move GRV T', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '5', 'lodt_name' => 'Unload GRV Good WH', 'lodt_code' => 'Unload GRV', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_odtp')->insertOrIgnore([
                ['id' => '1', 'odtp_name' => 'PreSales', 'odtp_code' => 'PreSales', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'odtp_name' => 'Van Sales', 'odtp_code' => 'Van Sales', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_ntpe')->insertOrIgnore([
                ['id' => '1', 'ntpe_name' => 'Market visit', 'ntpe_code' => 'Market visit', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_optp')->insertOrIgnore([
                ['id' => '1', 'optp_name' => 'Cash', 'optp_code' => 'Cash', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'optp_name' => 'Credit', 'optp_code' => 'Credit', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_unit')->insertOrIgnore([
                ['id' => '1', 'unit_name' => 'AUNC', 'unit_sybl' => 'Anc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'unit_name' => 'Bag', 'unit_sybl' => 'Bag', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '3', 'unit_name' => 'Bottle', 'unit_sybl' => 'Btl', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '4', 'unit_name' => 'Box', 'unit_sybl' => 'Box', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '5', 'unit_name' => 'BUNDLE', 'unit_sybl' => 'Bndl', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '6', 'unit_name' => 'Can', 'unit_sybl' => 'Can', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '7', 'unit_name' => 'Cartoon', 'unit_sybl' => 'Ctn', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '8', 'unit_name' => 'CILN', 'unit_sybl' => 'Cln', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '9', 'unit_name' => 'Coil', 'unit_sybl' => 'Col', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '10', 'unit_name' => 'CONE', 'unit_sybl' => 'Con', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '11', 'unit_name' => 'Crate', 'unit_sybl' => 'Crt', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '12', 'unit_name' => 'Cubic Feet', 'unit_sybl' => 'Cft', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '13', 'unit_name' => 'Cylender', 'unit_sybl' => 'Clndr', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '14', 'unit_name' => 'DAY', 'unit_sybl' => 'DAY', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '15', 'unit_name' => 'DIS', 'unit_sybl' => 'DIS', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '16', 'unit_name' => 'DOSE', 'unit_sybl' => 'Dse', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '17', 'unit_name' => 'Dozzen', 'unit_sybl' => 'Dzn', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '18', 'unit_name' => 'Drum', 'unit_sybl' => 'Drm', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '19', 'unit_name' => 'Feet', 'unit_sybl' => 'Ft', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '20', 'unit_name' => 'Gallon', 'unit_sybl' => 'Gln', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '21', 'unit_name' => 'Gram', 'unit_sybl' => 'Gm', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '22', 'unit_name' => 'Gros', 'unit_sybl' => 'Grs', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '23', 'unit_name' => 'Inchase', 'unit_sybl' => 'Inc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '24', 'unit_name' => 'Jar', 'unit_sybl' => 'Jar', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '25', 'unit_name' => 'Killogram', 'unit_sybl' => 'Kg', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '26', 'unit_name' => 'LINK', 'unit_sybl' => 'lnk', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '27', 'unit_name' => 'Liter', 'unit_sybl' => 'Ltr', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '28', 'unit_name' => 'LOT', 'unit_sybl' => 'LOT', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '29', 'unit_name' => 'Matric Ton', 'unit_sybl' => 'Mct', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '30', 'unit_name' => 'Mili Litre', 'unit_sybl' => 'Ml', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '31', 'unit_name' => 'Mitre', 'unit_sybl' => 'Mtr', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '32', 'unit_name' => 'Ounch', 'unit_sybl' => 'Onch', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '33', 'unit_name' => 'Pack', 'unit_sybl' => 'Pac', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '34', 'unit_name' => 'Pair', 'unit_sybl' => 'Pair', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '35', 'unit_name' => 'Piece', 'unit_sybl' => 'Pcs', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '36', 'unit_name' => 'Pouch', 'unit_sybl' => 'Pch', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '37', 'unit_name' => 'Pound', 'unit_sybl' => 'Pnd', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '38', 'unit_name' => 'Rim', 'unit_sybl' => 'Rim', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '39', 'unit_name' => 'Roll', 'unit_sybl' => 'Roll', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '40', 'unit_name' => 'Set', 'unit_sybl' => 'Set', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '41', 'unit_name' => 'Sft', 'unit_sybl' => 'Sft', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '42', 'unit_name' => 'SIFT', 'unit_sybl' => 'Sift', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '43', 'unit_name' => 'Tin', 'unit_sybl' => 'Tin', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '44', 'unit_name' => 'Un Defined', 'unit_sybl' => 'und', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '45', 'unit_name' => 'Unit', 'unit_sybl' => 'Unit', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '46', 'unit_name' => 'Yard', 'unit_sybl' => 'Yard', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],

            ]);
            DB::connection($country->cont_conn)->table('tm_dptp')->insertOrIgnore([
                ['id' => '1', 'dptp_name' => 'General', 'dptp_code' => 'General', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'dptp_name' => 'GRV', 'dptp_code' => 'GRV', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_rttp')->insertOrIgnore([
                ['id' => '1', 'rttp_name' => 'Damage Item', 'rttp_code' => 'Damage', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'rttp_name' => 'Sales Return', 'rttp_code' => 'Return', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_ttyp')->insertOrIgnore([
                ['id' => '1', 'ttyp_name' => 'DM Trip', 'ttyp_code' => 'DM Trip', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'ttyp_name' => 'Van Trip', 'ttyp_code' => 'Van Trip', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::connection($country->cont_conn)->table('tm_invt')->insertOrIgnore([
                ['id' => '1', 'invt_name' => 'Invoice', 'invt_code' => 'Invoice', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '2', 'invt_name' => 'FOC', 'invt_code' => 'FOC', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '3', 'invt_name' => 'Statement Discount', 'invt_code' => 'Statement ', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '4', 'invt_name' => 'Rental', 'invt_code' => 'Rental', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '5', 'invt_name' => 'GRV', 'invt_code' => 'GRV', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '6', 'invt_name' => 'Opening Support', 'invt_code' => 'Opening Su', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '7', 'invt_name' => 'Other Support', 'invt_code' => 'Other Supp', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '8', 'invt_name' => 'Opening Balance', 'invt_code' => 'Opening Ba', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '9', 'invt_name' => 'Credit Sales', 'invt_code' => 'Party Bala', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '10', 'invt_name' => 'Van Discrepancy', 'invt_code' => 'Discrepanc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '11', 'invt_name' => 'Collection', 'invt_code' => 'Collection', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '12', 'invt_name' => 'Market Damage', 'invt_code' => 'Market Dam', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '13', 'invt_name' => 'Discrepancy', 'invt_code' => 'Discrepanc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '14', 'invt_name' => 'IBS Balance Adjustme', 'invt_code' => 'IBS Balanc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '15', 'invt_name' => 'Trade discount', 'invt_code' => 'Trade disc', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '16', 'invt_name' => 'Advance Against inpu', 'invt_code' => 'Advance Ag', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '17', 'invt_name' => 'Input Vat', 'invt_code' => 'Input Vat', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
                ['id' => '18', 'invt_name' => 'Output Vat', 'invt_code' => 'output_vat', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'aemp_iusr' => '1', 'aemp_eusr' => '1',],
            ]);
            DB::table('tm_wsmn')->insertOrIgnore([
                [ 'wsmn_name'=>'Note Report', 'wsmn_wurl'=>'note/summary', 'wmnu_id'=>'9', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'NoteReportController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'App Menu Group', 'wsmn_wurl'=>'appMenuGroup', 'wmnu_id'=>'11', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'AppMenuGroupController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Details', 'wsmn_wurl'=>'location_details', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationDetailsController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Section', 'wsmn_wurl'=>'location_section', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationSectionController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Department', 'wsmn_wurl'=>'location_department', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationDepartmentController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Company', 'wsmn_wurl'=>'location_company', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationCompanyController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Master', 'wsmn_wurl'=>'location_master', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationMasterController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Location Report', 'wsmn_wurl'=>'location/maintainLocation', 'wmnu_id'=>'15', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'LocationController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Vehicle', 'wsmn_wurl'=>'vehicle', 'wmnu_id'=>'3', 'wsmn_oseq'=>'13', 'wsmn_ukey'=>'VehicleController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Maintain Trip', 'wsmn_wurl'=>'trip', 'wmnu_id'=>'8', 'wsmn_oseq'=>'5', 'wsmn_ukey'=>'TripController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Ward', 'wsmn_wurl'=>'ward', 'wmnu_id'=>'4', 'wsmn_oseq'=>'4', 'wsmn_ukey'=>'tm_ward', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Thana', 'wsmn_wurl'=>'thana', 'wmnu_id'=>'4', 'wsmn_oseq'=>'3', 'wsmn_ukey'=>'tm_than', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Sub Channel', 'wsmn_wurl'=>'sub_channel', 'wmnu_id'=>'3', 'wsmn_oseq'=>'8', 'wsmn_ukey'=>'tm_scnl', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Return Reason', 'wsmn_wurl'=>'return_reason', 'wmnu_id'=>'3', 'wsmn_oseq'=>'12', 'wsmn_ukey'=>'tm_rson', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'promotion', 'wsmn_wurl'=>'promotion', 'wmnu_id'=>'6', 'wsmn_oseq'=>'9', 'wsmn_ukey'=>'tm_prom', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Outlet Category', 'wsmn_wurl'=>'outlet_grade', 'wmnu_id'=>'3', 'wsmn_oseq'=>'9', 'wsmn_ukey'=>'tm_otcg', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Market', 'wsmn_wurl'=>'market', 'wmnu_id'=>'4', 'wsmn_oseq'=>'5', 'wsmn_ukey'=>'tm_mktm', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Sub Category', 'wsmn_wurl'=>'sub-category', 'wmnu_id'=>'7', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'tm_itsg', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Product Category', 'wsmn_wurl'=>'category', 'wmnu_id'=>'7', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tm_itcg', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'District', 'wsmn_wurl'=>'district', 'wmnu_id'=>'4', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'tm_dsct', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Product Master', 'wsmn_wurl'=>'sku', 'wmnu_id'=>'7', 'wsmn_oseq'=>'5', 'wsmn_ukey'=>'tm_amim', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Product Group', 'wsmn_wurl'=>'product-group', 'wmnu_id'=>'7', 'wsmn_oseq'=>'3', 'wsmn_ukey'=>'tbld_product_group', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Channel', 'wsmn_wurl'=>'channel', 'wmnu_id'=>'3', 'wsmn_oseq'=>'7', 'wsmn_ukey'=>'tbld_chnl', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Target', 'wsmn_wurl'=>'target', 'wmnu_id'=>'13', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'TargetUploadController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Special Budget', 'wsmn_wurl'=>'specialBudget', 'wmnu_id'=>'12', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'SpecialBudgetController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Product Class', 'wsmn_wurl'=>'product-class', 'wmnu_id'=>'7', 'wsmn_oseq'=>'4', 'wsmn_ukey'=>'ProductClassController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Process Run', 'wsmn_wurl'=>'setting/process', 'wmnu_id'=>'11', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'ProcessRunController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Maintain Order', 'wsmn_wurl'=>'order_report/orderSummary', 'wmnu_id'=>'8', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'OrderReportController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'No Order Reason', 'wsmn_wurl'=>'no_order_reason', 'wmnu_id'=>'3', 'wsmn_oseq'=>'14', 'wsmn_ukey'=>'NoOrderReasonController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Not Delivery Reason', 'wsmn_wurl'=>'no_delivery_reason', 'wmnu_id'=>'3', 'wsmn_oseq'=>'15', 'wsmn_ukey'=>'NoDeliveryReasonController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Stock Upload', 'wsmn_wurl'=>'mrr', 'wmnu_id'=>'8', 'wsmn_oseq'=>'4', 'wsmn_ukey'=>'MRRController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Maintain GRV', 'wsmn_wurl'=>'grv_report/grvSummary', 'wmnu_id'=>'8', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'GRVReportController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Export', 'wsmn_wurl'=>'data_export/dataExport', 'wmnu_id'=>'10', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'DataExportController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Dashboard Permission', 'wsmn_wurl'=>'dashboardPermission', 'wmnu_id'=>'11', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'DashboardPermissionController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Dashboard1 Permission', 'wsmn_wurl'=>'dashboard1Permission', 'wmnu_id'=>'11', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'Dashboard1PermissionController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Company Site Mapping', 'wsmn_wurl'=>'companySiteMapping', 'wmnu_id'=>'3', 'wsmn_oseq'=>'11', 'wsmn_ukey'=>'CompanySiteMappingController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Maintain Collection', 'wsmn_wurl'=>'collection/maintainCollection', 'wmnu_id'=>'8', 'wsmn_oseq'=>'3', 'wsmn_ukey'=>'CollectionController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Order Cancel Reason', 'wsmn_wurl'=>'cancel_order_reason', 'wmnu_id'=>'3', 'wsmn_oseq'=>'16', 'wsmn_ukey'=>'CancelOrderReasonController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Maintain Block', 'wsmn_wurl'=>'block/maintainBlock', 'wmnu_id'=>'12', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'BlockOrderController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Bank List', 'wsmn_wurl'=>'bank', 'wmnu_id'=>'3', 'wsmn_oseq'=>'17', 'wsmn_ukey'=>'BankController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Activity', 'wsmn_wurl'=>'activity/summary', 'wmnu_id'=>'9', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'ActivityReportController', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Dealer', 'wsmn_wurl'=>'depot', 'wmnu_id'=>'3', 'wsmn_oseq'=>'9', 'wsmn_ukey'=>'tm_dlrm', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Price List', 'wsmn_wurl'=>'price_list', 'wmnu_id'=>'3', 'wsmn_oseq'=>'8', 'wsmn_ukey'=>'tm_plmt', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Outlet', 'wsmn_wurl'=>'site', 'wmnu_id'=>'3', 'wsmn_oseq'=>'7', 'wsmn_ukey'=>'tm_site', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Route plan', 'wsmn_wurl'=>'pjp', 'wmnu_id'=>'3', 'wsmn_oseq'=>'6', 'wsmn_ukey'=>'tl_rpln', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Route', 'wsmn_wurl'=>'route', 'wmnu_id'=>'3', 'wsmn_oseq'=>'5', 'wsmn_ukey'=>'tm_rout', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Division', 'wsmn_wurl'=>'division', 'wmnu_id'=>'5', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tm_sdvm', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Base', 'wsmn_wurl'=>'base', 'wmnu_id'=>'5', 'wsmn_oseq'=>'4', 'wsmn_ukey'=>'tm_base', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Zone', 'wsmn_wurl'=>'zone', 'wmnu_id'=>'5', 'wsmn_oseq'=>'3', 'wsmn_ukey'=>'tm_zone', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Region', 'wsmn_wurl'=>'region', 'wmnu_id'=>'5', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'tm_dirg', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Divisions', 'wsmn_wurl'=>'divisions', 'wmnu_id'=>'4', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tm_disn', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Group', 'wsmn_wurl'=>'sales-group', 'wmnu_id'=>'3', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tm_slgp', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Company', 'wsmn_wurl'=>'company', 'wmnu_id'=>'3', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tm_acmp', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Employee', 'wsmn_wurl'=>'employee', 'wmnu_id'=>'1', 'wsmn_oseq'=>'1', 'wsmn_ukey'=>'tbld_employee', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Designation', 'wsmn_wurl'=>'role', 'wmnu_id'=>'1', 'wsmn_oseq'=>'2', 'wsmn_ukey'=>'tbld_role', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Mobile Access', 'wsmn_wurl'=>'mobile_menu/create', 'wmnu_id'=>'1', 'wsmn_oseq'=>'4', 'wsmn_ukey'=>'tbld_mobile_menu', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],
                [ 'wsmn_name'=>'Web Access', 'wsmn_wurl'=>'menu/create', 'wmnu_id'=>'1', 'wsmn_oseq'=>'3', 'wsmn_ukey'=>'tblt_user_menu', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'users_iusr'=>'1', 'users_eusr'=>'1',],

            ]);
            $data = \App\Menu\SubMenu::where(['wsmn_ukey' => 'tblt_user_menu', 'cont_id' => "$country->id"])->first();
            DB::table('tl_wsmu')->insertOrIgnore([
                ['users_id' => '1081', 'wsmn_id' => "$data->id", 'wsmu_vsbl' => '1', 'wsmu_crat' => '1', 'wsmu_read' => '1', 'wsmu_updt' => '1', 'wsmu_delt' => '1', 'cont_id' => "$country->id", 'lfcl_id' => '1', 'users_iusr' => '1', 'users_eusr' => '1',],

            ]);
            DB::connection($country->cont_conn)->table('tm_amnu')->insertOrIgnore([
                ['id'=>'1', 'amnu_name'=>'Dashboard1', 'amnu_code'=>'10', 'amnu_oseq'=>'9', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'2', 'amnu_name'=>'Report', 'amnu_code'=>'11', 'amnu_oseq'=>'4', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'3', 'amnu_name'=>'Search', 'amnu_code'=>'13', 'amnu_oseq'=>'1', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'4', 'amnu_name'=>'Order', 'amnu_code'=>'14', 'amnu_oseq'=>'3', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'5', 'amnu_name'=>'DM Trip', 'amnu_code'=>'16', 'amnu_oseq'=>'8', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'6', 'amnu_name'=>'Van Sales', 'amnu_code'=>'17', 'amnu_oseq'=>'6', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'7', 'amnu_name'=>'Visit', 'amnu_code'=>'18', 'amnu_oseq'=>'10', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'8', 'amnu_name'=>'Collection', 'amnu_code'=>'19', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'9', 'amnu_name'=>'Attendance', 'amnu_code'=>'2', 'amnu_oseq'=>'2', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'10', 'amnu_name'=>'Outlet', 'amnu_code'=>'20', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'11', 'amnu_name'=>'Personal Credit', 'amnu_code'=>'21', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'12', 'amnu_name'=>'Promotion', 'amnu_code'=>'22', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'13', 'amnu_name'=>'Price List', 'amnu_code'=>'23', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'14', 'amnu_name'=>'Route Permission', 'amnu_code'=>'24', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'15', 'amnu_name'=>'Delivery', 'amnu_code'=>'25', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'16', 'amnu_name'=>'Outlet Info', 'amnu_code'=>'26', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'17', 'amnu_name'=>'Cheque', 'amnu_code'=>'27', 'amnu_oseq'=>'11', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'18', 'amnu_name'=>'Special Block', 'amnu_code'=>'28', 'amnu_oseq'=>'13', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'19', 'amnu_name'=>'Note', 'amnu_code'=>'3', 'amnu_oseq'=>'5', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'20', 'amnu_name'=>'Around Me', 'amnu_code'=>'7', 'amnu_oseq'=>'8', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'22', 'amnu_name'=>'Dashboard', 'amnu_code'=>'9', 'amnu_oseq'=>'7', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'23', 'amnu_name'=>'Out Of Stock', 'amnu_code'=>'29', 'amnu_oseq'=>'21', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],
                ['id'=>'24', 'amnu_name'=>'Order bd', 'amnu_code'=>'1', 'amnu_oseq'=>'3', 'cont_id'=>"$country->id", 'lfcl_id'=>'1', 'aemp_iusr'=>'1', 'aemp_eusr'=>'1',],


            ]);
        }

    }
}
