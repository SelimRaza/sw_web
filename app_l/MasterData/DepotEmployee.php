<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\MasterData;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class DepotEmployee extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_srdi';
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
        return ['dealer_code', 'dealer_name', 'staff_id', 'staff_name'];
    }

    public function array(array $row)
    {
        // foreach ($row as $sdsf) {
        //     $values = (object)$sdsf;

        //     $employee = Employee::on($this->connection)->where(['aemp_usnm' => $values->staff_id])->first();
        //     $srList[] = $employee->id;
        //     $depot = Depot::on($this->connection)->where(['dlrm_code' => $values->dealer_code])->first();
        //     $depotList[] = $depot->id;
        // }
        // $qu = "DELETE FROM `tl_srdi` WHERE `aemp_id` IN (" . implode(',', $srList) . ")";
        // DB::connection($this->connection)->select(DB::raw($qu));
        // $len = sizeof($srList);
        
        // for ($i = 0; $i < $len; $i++) {
        //     $depotEmployee = new DepotEmployee();
        //     $depotEmployee->setConnection($this->connection);
        //     $depotEmployee->aemp_id = $srList[$i];
        //     $depotEmployee->acmp_id = 1;
        //     $depotEmployee->dlrm_id = $depotList[$i];
        //     $depotEmployee->cont_id = $this->currentUser->country()->id;
        //     $depotEmployee->lfcl_id = 1;
        //     $depotEmployee->aemp_iusr = $this->currentUser->employee()->id;
        //     $depotEmployee->aemp_eusr = $this->currentUser->employee()->id;
        //     $depotEmployee->var = 1;
        //     $depotEmployee->attr1 = '';
        //     $depotEmployee->attr2 = '';
        //     $depotEmployee->attr3 = 0;
        //     $depotEmployee->attr4 = 0;
        //     $depotEmployee->save();
        // }
        try{
            $values=(object)$row;  

            foreach($values as $val){
                $employee = Employee::on($this->connection)->where(['aemp_usnm' => $val['staff_id']])->first();
                $depot = Depot::on($this->connection)->where(['dlrm_code' => $val['dealer_code']])->first();
                $depotEmp=DepotEmployee::on($this->connection)->where(['dlrm_id'=>$depot['id'],'aemp_id'=>$employee['id']])->first();
                if($depotEmp==null && $employee && $depot){
                    $insert[] = [
                        'aemp_id' =>$employee['id'],
                        'acmp_id' =>1,
                        'dlrm_id' =>$depot['id'],
                        'cont_id' =>$this->currentUser->country()->id,
                        'lfcl_id' =>1,
                        'aemp_iusr' => $this->currentUser->employee()->id,
                        'aemp_eusr' => $this->currentUser->employee()->id,
                        'var'=>1,
                        'attr1'=>'',
                        'attr2'=>'',
                        'attr3'=>0,
                        'attr4'=>0,
                    ];
                }
                
            }
            if (!empty($insert)) {
                foreach (array_chunk($insert,200) as $t)  
                    {
                       DB::connection($this->connection)->table('tl_srdi')->insertOrIgnore($t);
                    }
                
            }
        }
        catch(\Exception $e){
            dd($e->getMessage());
        }

    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_id);
    }

    public function depot()
    {
        return Depot::on($this->connection)->find($this->dlrm_id);
    }

}