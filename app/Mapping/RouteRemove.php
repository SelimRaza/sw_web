<?php

namespace App\Mapping;


use App\MasterData\Employee;
use App\MasterData\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RouteRemove extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_rsmp';
    private $currentUser;
    protected $connection = '';
    protected $guarded = [];

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'route_code'
        ];
    }

    public function model(array $row)
    {
        $value = (object)$row;


        try {
            $days = array(
                1 => 'Saturday',
                2 => 'Sunday',
                3 => 'Monday',
                4 => 'Tuesday',
                5 => 'Wednesday',
                6 => 'Thursday',
                7 => 'Friday'
            );

            $day = $days[$value->day];

            $employee_id = $this->getEmployeeId($value->staff_id);
            $route_id = $this->getRouteId($value->rout_code);

            $exist = RoutePlan::on($this->connection)->where(['aemp_id' => $employee_id,'rpln_day' => $day])->first();

            if (!$exist && $employee_id != null && $route_id != null && $value->status != 2) {
                $routePlan = new RoutePlan();
                $routePlan->setConnection($this->connection);
                $routePlan->aemp_id = $employee_id;
                $routePlan->rpln_day = $day;
                $routePlan->rout_id = $route_id;
                $routePlan->cont_id = $this->currentUser->employee()->cont_id;
                $routePlan->lfcl_id = $value->status;
                $routePlan->aemp_iusr = $this->currentUser->employee()->id;
                $routePlan->aemp_eusr = $this->currentUser->employee()->id;
                $routePlan->var = 1;
                $routePlan->attr1 = '';
                $routePlan->attr2 = '';
                $routePlan->attr3 = 0;
                $routePlan->attr4 = 0;
                $routePlan->save();
            }
            else if($exist && $route_id != null && $value->status != 2){
                $exist->update([
                    'rout_id' => $route_id,
                    'aemp_eusr' => $this->currentUser->employee()->id
                ]);
            }else if($exist && $value->status == 2){
                $exist->delete();
            }

        }catch(\Exception $e)
        {
            return;
        }

    }

    public function getEmployeeId($staff_id)
    {
        return Employee::on($this->connection)->where('aemp_usnm', $staff_id)->first()->id;
    }

    public function getRouteId($rout_code)
    {
        return Route::on($this->connection)->where('rout_code', $rout_code)->first()->id;
    }
}
