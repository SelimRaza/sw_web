<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Employee extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_aemp';
    protected $connection = '';
    private $currentUser;

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['designation_code', 'role_code', 'manager_code', 'line_manager_code', 'user_name', 'full_name', 'short_name', 'language_name', 'sales_person', 'hris_sync', 'mobile'];
    }

    public function user()
    {
        return User::find($this->aemp_lued);
    }


    public function role()
    {
        return Role::on($this->connection)->find($this->edsg_id);
    }

    public function masterRole()
    {
        return MasterRole::on($this->connection)->find($this->role_id);
    }

    public function manager()
    {
        return Employee::on($this->connection)->find($this->aemp_mngr);
    }

    public function lineManager()
    {
        return Employee::on($this->connection)->find($this->aemp_lmid);
    }

    public function country()
    {
        return Country::find($this->cont_id);
    }

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        //$len = sizeof($array);
        $count = 0;
        //dd($len);
        foreach ($array as $row) {
            //dd($row);
            $value = (object)$row;
            //dd($value);

            $designation = Role::on($this->connection)->where(['edsg_code' => $value->designation_code])->first();
            $masterRole = MasterRole::on($this->connection)->where(['role_code' => $value->role_code])->first();
            $ests = Employee::on($this->connection)->where(['aemp_usnm' => $value->user_name])->first();
            $manager = Employee::on($this->connection)->where(['aemp_usnm' => $value->manager_code])->first();
            $lManager = Employee::on($this->connection)->where(['aemp_usnm' => $value->line_manager_code])->first();
           // dd($masterRole);

            
            if ($ests != null) {
                //dd($ests);
                $emId = $ests->id;
                
            } else {
                $emId = 0;

            }


            if ($manager != null && $lManager != null) {
                $manag = $manager->id;
                $lmanag = $lManager->id;
            } else {
                $manag = '1';
                $lmanag = '1';

            }
            if ($emId > 0) {
                //dd($ests);
                $count++;
                $id = $ests->id;
                $emp = Employee::on($this->connection)->findorfail($id);
                $emp->aemp_name = $value->full_name == "" ? '' : $value->full_name;
                $emp->aemp_stnm = $value->short_name == "" ? '' : $value->short_name;
                $emp->aemp_onme = $value->language_name == "" ? '' : $value->language_name;
                $emp->aemp_mob1 = $value->mobile == "" ? '' : $value->mobile;

                $emp->edsg_id = $designation->id;
                $emp->role_id = $masterRole->id;

                $emp->aemp_mngr = $manag;
                $emp->aemp_lmid = $lmanag;

                $emp->aemp_issl = $value->sales_person == "1" ? 1 : 0;
                $emp->aemp_asyn = $value->hris_sync == "1" ? 'Y' : 'N';
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                //dd($emp);
                $emp->save();


            } else {
                $user = User::create([
                    'name' => $value->full_name == "" ? '.' : $value->full_name,
                    'email' => trim($value->user_name),
                    'password' => bcrypt(trim($value->user_name)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $this->currentUser->cont_id,
                ]);
                $emp = new Employee();
                $emp->setConnection($this->connection);
                $emp->aemp_name = $value->full_name == "" ? '' : $value->full_name;
                $emp->aemp_stnm = $value->short_name == "" ? '' : $value->short_name;
                $emp->aemp_onme = $value->language_name == "" ? '' : $value->language_name;
                $emp->aemp_mob1 = $value->mobile == "" ? '' : $value->mobile;
                $emp->aemp_dtsm = $value->mobile == "" ? '' : $value->mobile;
                $emp->aemp_asyn = $value->hris_sync == "1" ? 'Y' : 'N';
                $emp->aemp_emal = '';
                $emp->aemp_lued = $user->id;
                $emp->edsg_id = $designation->id;
                $emp->role_id = $masterRole->id;
                $emp->aemp_utkn = md5(uniqid(rand(), true));
                $emp->cont_id = $this->currentUser->cont_id;
                $emp->aemp_iusr = $this->currentUser->employee()->id;
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->aemp_mngr = $manag;
                $emp->aemp_lmid = $lmanag;
                $emp->aemp_aldt = 0;
                $emp->aemp_lcin = '99';
                $emp->aemp_otml = 2;
                $emp->aemp_lonl = 2;
                $emp->aemp_usnm = trim($value->user_name);
                $emp->aemp_pimg = '';
                $emp->aemp_picn = '';
                $emp->aemp_emcc = '';
                $emp->site_id = 0;
                $emp->aemp_crdt = 0;
                $emp->aemp_issl = 1;
                $emp->lfcl_id = 1;
                $emp->save();

            }


            /*$newArray = array_keys($row);
            for ($i = 10; $i < count($newArray); $i++) {
                $target = Target::on($this->connection)->where([
                    'trgt_year' => $row['year'],
                    'trgt_mnth' => $row['month'],
                    'aemp_vusr' => $row['supervisor_id'],
                    'aemp_susr' => $newArray[$i],
                    'amim_id' => $row['sku_id'
                    ],])->first();

                if ($target == null) {
                    $insert[] = [
                        'trgt_year' => $row['year'],
                        'trgt_mnth' => $row['month'],
                        'aemp_vusr' => $row['supervisor_id'],
                        'aemp_susr' => $newArray[$i],
                        'amim_id' => $row['sku_id'],
                        'trgt_tqty' => $row[$newArray[$i]] * $row['ctn_size'],
                        'trgt_tamt' => $row[$newArray[$i]] * $row['ctn_price'],
                        'trgt_rqty' => $row[$newArray[$i]] * $row['ctn_size'],
                        'trgt_ramt' => $row[$newArray[$i]] * $row['ctn_price'],
                        'cont_id' => $this->currentUser->employee()->cont_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => $this->currentUser->employee()->id,
                        'aemp_eusr' => $this->currentUser->employee()->id,
                    ];
                } else {
                    $target->trgt_rqty = $row[$newArray[$i]] * $row['ctn_size'];
                    $target->trgt_ramt = $row[$newArray[$i]] * $row['ctn_price'];
                    $target->aemp_eusr = $this->currentUser->employee()->id;
                    $target->save();
                }
            }*/
        }
        /*if (!empty($insert)) {
            DB::connection($this->connection)->table('tt_trgt')->insert($insert);
        }*/
    }
}
