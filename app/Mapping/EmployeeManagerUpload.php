<?php

namespace App\Mapping;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroupEmployee;
use App\MasterData\Role;

use Illuminate\Support\Facades\DB;

class EmployeeManagerUpload extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_aemp';
    protected $connection = '';
    private $currentUser;

    protected $fillable = ['aemp_mngr', 'aemp_lmid', 'lfcl_id', 'aemp_eusr'];

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['staff_id', 'manager_id', 'status'];
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
        foreach ($array as $row) {
            $value = (object)$row;

            $employee   = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
            $manager    = Employee::on($this->connection)->where(['aemp_usnm' => $value->manager_id])->first();

            if ($employee != null && $manager != null && $value->status == 1) {

                $employee->aemp_mngr = $manager->id;
                $employee->aemp_lmid = $manager->id;
                $employee->lfcl_id = 1;
                $employee->aemp_eusr = $this->currentUser->employee()->id;

                try {
                    $employee->save();
                } catch (\Exception $e) {
                    return;
                }
            } else if ($employee != null && $value->status == 2) {
                $employee->lfcl_id = 2;
                $employee->aemp_eusr = $this->currentUser->employee()->id;
                try {
                    DB::transaction(function () use($employee, $value) {
                        $employee->save();


                       $user= User::where(['email' => $value->staff_id])->first();
					  $user->lfcl_id = 2;
					  $user->save();

					   // DB::table('users')->where(['email' => $value->staff_id])->update(['device_imei' => 'M']);
                    });
                } catch (\Exception $e) {
                    return;
                }
            }
        }
    }
}
