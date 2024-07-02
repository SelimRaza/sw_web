<?php

namespace App\MasterData;

use App\BusinessObject\SalesGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PJP extends Model implements FromCollection, WithHeadings, WithHeadingRow, ToArray
{
    protected $table = 'tl_rpln';
    private $currentUser;
    private $sales_group_id1 = 0;
    private $emp_id1 = 0;
    protected $connection= '';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }

    public static function create($emp_id1)
    {
        $instance = new self();
        $instance->emp_id1 = $emp_id1;
        return $instance;
    }

    public function collection()
    {

        $pjpArray = [];
        // $salesGroup = SalesGroup::findorfail($this->sales_group_id1);
        // dd($request->emp_id);
        //  var_dump($request->emp_id);
        foreach ($this->emp_id1 as $emp_id) {
            $employee = Employee::on($this->connection)->findorfail($emp_id);

            $pjps = PJP::on($this->connection)->where(['aemp_id' => $emp_id])->get();
            foreach ($pjps as $pjp) {
                $pjpArray[] = [$pjp->employee()->aemp_usnm, $pjp->employee()->aemp_name, $pjp->rpln_day, $pjp->route()->rout_code, $pjp->route()->rout_name];
            }
            if (!count($pjps) == 1) {
                $pjpArray[] = [$employee->aemp_usnm, $employee->aemp_name, '', '', ''];
            }
        }

        return collect([
            $pjpArray

        ]);
    }

    public function headings(): array
    {
        return ['user_code', 'user_name', 'day', 'route_code', 'route_name'];
    }

    public function array(array $array)
    {
        $data = $array;
        foreach ($data as $key => $row) {
            $value = (object)$row;

            $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->user_code])->first();
            $route = Route::on($this->connection)->where(['rout_code' => $value->route_code])->first();

            if ($employee != null &&$route!=null) {
                $insert[] = ['aemp_id' => $employee->id, 'rpln_day' => $value->day, 'rout_id' => $route->id, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
            }
        }
        $emp_ids = array_column($insert, 'aemp_id');
        PJP::on($this->connection)->whereIn('aemp_id', $emp_ids)->delete();
        if (!empty($insert)) {
            DB::connection($this->connection)->table('tl_rpln')->insert($insert);
        }
    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_id);
    }

    public function route()
    {
        return Route::on($this->connection)->find($this->rout_id);
    }


}
