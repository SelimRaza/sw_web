<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Employee;
use App\MasterData\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SalesGroupEmployee extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_sgsm';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user()) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['group_code', 'staff_id', 'price_list_code', 'zone_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->staff_id])->first();
        $slgp = SalesGroup::on($this->connection)->where(['slgp_code' => $value->group_code])->first();
        $plmt = PriceList::on($this->connection)->where(['plmt_code' => $value->price_list_code])->first();
        $zone = Zone::on($this->connection)->where(['zone_code' => $value->zone_code])->first();

        if ($employee != null && $slgp != null && $plmt != null && $zone != null) {
           // $salesGroupEmp = SalesGroupEmployee::on($this->connection)->where(['slgp_id' => $slgp->id, 'aemp_id' => $employee->id,'plmt_id'=>$plmt->id])->first();
           $salesGroupEmp = SalesGroupEmployee::on($this->connection)->where(['aemp_id' => $employee->id])->first();
            if ($salesGroupEmp == null) {
                $salesGroupEmp = new SalesGroupEmployee();
                $salesGroupEmp->setConnection($this->connection);
                $salesGroupEmp->aemp_id = $employee->id;
                $salesGroupEmp->slgp_id = $slgp->id;
                $salesGroupEmp->plmt_id = $plmt->id;
                $salesGroupEmp->zone_id = $zone->id;
                $salesGroupEmp->cont_id = $this->currentUser->employee()->cont_id;
                $salesGroupEmp->lfcl_id = 1;
                $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupEmp->save();
            }else{
                $salesGroupEmp->setConnection($this->connection);
                $salesGroupEmp->aemp_id = $employee->id;
                $salesGroupEmp->slgp_id = $slgp->id;
                $salesGroupEmp->plmt_id = $plmt->id;
                $salesGroupEmp->zone_id = $zone->id;
                $salesGroupEmp->cont_id = $this->currentUser->employee()->cont_id;
                $salesGroupEmp->lfcl_id = 1;
                $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupEmp->save();
            }
            //else{
                // DB::connection($this->connection)->table('tl_sgsm')->upsert([
                //     'aemp_id'=> $employee->id, 
                //     'slgp_id'=>$slgp->id, 
                //     'plmt_id'=> $plmt->id, 
                //     'zone_id'=> $zone->id, 
                //     'cont_id'=>$this->currentUser->employee()->cont_id, 
                //     'lfcl_id'=> 1, 
                //     'aemp_iusr'=>$this->currentUser->employee()->id, 
                //     'aemp_eusr'=>$this->currentUser->employee()->id
                // ]);
                // SalesGroupEmployee::on($this->connection)->updateOrCreate([
                //     'aemp_id'=> $employee->id, 
                //     'slgp_id'=>$slgp->id, 
                //     'plmt_id'=> $plmt->id, 
                //     'zone_id'=> $zone->id, 
                //     'cont_id'=>$this->currentUser->employee()->cont_id, 
                //     'lfcl_id'=> 1, 
                //     'aemp_iusr'=>$this->currentUser->employee()->id, 
                //     'aemp_eusr'=>$this->currentUser->employee()->id

                // ]);
           // }
        }

    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_id);
    }

    public function salesGroup()
    {
        return SalesGroup::on($this->connection)->find($this->slgp_id);
    }

}