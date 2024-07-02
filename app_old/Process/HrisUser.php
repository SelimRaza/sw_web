<?php

namespace App\Process;

use App\MasterData\Employee;
use App\MasterData\MasterRole;
use App\User;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class HrisUser
{
    public function createOrUpdateUser($country)
    {
        /*$lld[]=['sr_id' => "insert"];
        DB::connection($country->cont_conn)->table('test45')->insert($lld);*/

        $query1 = "SELECT
  t1.name                              AS aemp_name,
  ''                                   AS aemp_onme,
  t1.name                              AS aemp_stnm,
  t1.mobile                            AS aemp_mob1,
  t1.mobile                            AS aemp_dtsm,
  t1.email                             AS aemp_emal,
  0                                    AS aemp_otml,
  ''                                   AS aemp_emcc,
  0                                    AS aemp_lued,
  1                                    AS edsg_id,
  if(t1.designation_name = 'SR' OR t1.designation_name = 'Senior Sales Representative' OR t1.designation_name = 'Sales Representative' OR t1.designation_name = 'se' OR t1.designation_name = 'dsr', 1, 2) AS role_id,
  t1.sr_id                             AS aemp_usnm,
  ''                                   AS aemp_pimg,
  ''                                   AS aemp_picn,
  1                                    AS aemp_mngr,
  1                                    AS aemp_lmid,
  0                                    AS aemp_aldt,
  127                                  AS aemp_lcin,
  0                                    AS aemp_lonl,
  ''                                   AS aemp_utkn,
  0                                    AS site_id,
  0                                    AS aemp_crdt,
  1                                    AS aemp_issl,
  2                                    AS cont_id,
  if(t1.status = 'Y', 1, 2)            AS lfcl_id,
  if(t1.designation_name = 'SR' OR t1.designation_name = 'Senior Sales Representative' OR t1.designation_name = 'Sales Representative' OR t1.designation_name = 'se' OR t1.designation_name = 'dsr' , 1, 2) AS amng_id,
  2                                    AS aemp_iusr,
  2                                    AS aemp_eusr,
  t1.manager_code,
  t1.id
FROM tbld_new_employee AS t1
WHERE t1.sync_status = 0";
        $result = DB::connection($country->cont_conn)->select($query1);
        foreach ($result as $t) {
            if ($t->manager_code != '') {
                $manager = Employee::on($country->cont_conn)->where(['aemp_usnm' => $t->manager_code])->first();
                if ($manager != null) {
                    $t->aemp_mngr = $manager->id;
                    $t->aemp_lmid = $manager->id;
                }
            }
            $user = User::where('email', '=', $t->aemp_usnm)->first();
            if ($user == null) {
                $user = User::create([
                    'name' => $t->aemp_name,
                    'email' => trim($t->aemp_usnm),
                    'password' => bcrypt(trim($t->aemp_usnm)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $country->id,
                ]);
            }
            $user->remember_token = '';
            $user->lfcl_id = $t->lfcl_id;
            $user->cont_id = $country->id;
            $user->save();
            //DB::connection($country->cont_conn)->table('test45')->insert($lld);
            $employee = Employee::on($country->cont_conn)->where(['aemp_usnm' => $t->aemp_usnm])->first();
            if ($employee == null) {
                if ($t->lfcl_id==1){
                    DB::connection($country->cont_conn)->table('tm_aemp')->insert(
                        array(
                            'aemp_name' => $t->aemp_name,
                            'aemp_onme' => $t->aemp_onme,
                            'aemp_stnm' => $t->aemp_stnm,
                            'aemp_mob1' => $t->aemp_mob1,
                            'aemp_dtsm' => $t->aemp_dtsm,
                            'aemp_emal' => $t->aemp_emal,
                            'aemp_otml' => $t->aemp_otml,
                            'aemp_emcc' => $t->aemp_emcc,
                            'aemp_lued' => $user->id,
                            'edsg_id' => $t->edsg_id,
                            'role_id' => $t->role_id,
                            'aemp_usnm' => $t->aemp_usnm,
                            'aemp_pimg' => $t->aemp_pimg,
                            'aemp_picn' => $t->aemp_picn,
                            'aemp_mngr' => $t->aemp_mngr,
                            'aemp_lmid' => $t->aemp_lmid,
                            'aemp_aldt' => $t->aemp_aldt,
                            'aemp_lcin' => $t->aemp_lcin,
                            'aemp_lonl' => $t->aemp_lonl,
                            'aemp_utkn' => $t->aemp_utkn,
                            'site_id' => $t->site_id,
                            'aemp_crdt' => $t->aemp_crdt,
                            'aemp_issl' => $t->aemp_issl,
                            'aemp_asyn' => 'Y',
                            'cont_id' => $country->id,
                            'lfcl_id' => $t->lfcl_id,
                            'amng_id' => $t->amng_id,
                            'aemp_iusr' => $t->aemp_iusr,
                            'aemp_eusr' => $t->aemp_eusr,
                        )
                    );
                }
            } else {
                //DB::connection($country->cont_conn)->table('test45')->insert($lld);
                $employee->lfcl_id = $t->lfcl_id;
             //   $employee->amng_id = $t->amng_id;
                $employee->aemp_eusr = $t->aemp_eusr;
                $employee->save();
            }

            DB::connection($country->cont_conn)->table('tbld_new_employee')->where(['id' => $t->id])->update(
                array(
                    'sync_status' => 1,
                )
            );
        }

    }


}