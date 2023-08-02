<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Route extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_rout';
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

        return ['route_name', 'route_code', 'base_code', 'company_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $base = Base::on($this->connection)->where(['base_code' => $value->base_code])->first();
        $company = Company::on($this->connection)->where(['acmp_code' => $value->company_code])->first();
        if ($base != null && $company != null) {
            $route = Route::on($this->connection)->where(['rout_code' => $value->route_code])->first();

            if ($route == null) {
                $route = new Route();
                $route->setConnection($this->connection);
                $route->rout_name = $value->route_name;
                $route->rout_code = $value->route_code;
                $route->base_id = $base->id;
                $route->acmp_id = $company->id;
                $route->cont_id = $this->currentUser->employee()->cont_id;
                $route->lfcl_id = 1;
                $route->aemp_iusr = $this->currentUser->employee()->id;
                $route->aemp_eusr = $this->currentUser->employee()->id;
                $route->var = 1;
                $route->attr1 = '';
                $route->attr2 = '';
                $route->attr3 = 0;
                $route->attr4 = 0;
                $route->save();
            }else{

                $route = Route::on($this->connection)->findorfail($route->id);
                $route->rout_name = $value->route_name;
                $route->aemp_eusr = $this->currentUser->employee()->id;
                $route->save();
            }

        }
    }

    public function base()
    {
        return Base::on($this->connection)->find($this->base_id);
    }

    public function company()
    {
        return Company::on($this->connection)->find($this->acmp_id);
    }
}
