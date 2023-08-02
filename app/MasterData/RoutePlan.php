<?php

namespace App\MasterData;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoutePlan extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_rpln';
    private $currentUser;
    protected $connection = '';

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
            'user_name', 'name', 'day', 'route_code', 'route_name',
        ];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $routePlan = new RoutePlan();

        $employee = Employee::on($this->connection)->where(['aemp_usnm' => $value->sr_id])->first();
        $route = Route::on($this->connection)->where(['rout_code' => $value->route_code])->first();

        if ($employee != null && $route != null) {
            $routePlan->setConnection($this->connection);
            $routePlan->aemp_id = $employee->id;
            $routePlan->rpln_day = $value->day;
            $routePlan->rout_id = $route->id;
            $routePlan->cont_id = $this->currentUser->employee()->cont_id;
            $routePlan->lfcl_id = 1;
            $routePlan->aemp_iusr = $this->currentUser->employee()->id;
            $routePlan->aemp_eusr = $this->currentUser->employee()->id;
            $routePlan->var = 1;
            $routePlan->attr1 = '';
            $routePlan->attr2 = '';
            $routePlan->attr3 = 0;
            $routePlan->attr4 = 0;
            $routePlan->save();
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
