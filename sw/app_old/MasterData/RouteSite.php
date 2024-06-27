<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RouteSite extends Model implements FromCollection, WithHeadings, WithHeadingRow, ToArray
{
    protected $table = 'tl_rsmp';
    private $currentUser;
    private $route_id1 = 0;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($id)
    {
        $instance = new self();
        // dd($route_id);
        $instance->route_id1 = $id;
        return $instance;
    }

    public function collection()
    {

        $routeArray = array();
        $route = Route::on($this->connection)->findorfail($this->route_id1);
        $routeSites = RouteSite::on($this->connection)->where(['rout_id' => $this->route_id1])->get();
        if (sizeof($routeSites) > 0) {
            foreach ($routeSites as $routeSite) {
                $routeArray[] = [$route->id, $route->rout_name, $routeSite->site()->site_code, $routeSite->site()->site_name];
            }
        } else {
            $routeArray[] = [$route->id, $route->rout_name, '', ''];
        }

        return collect([
            $routeArray
        ]);
    }

    public function headings(): array
    {
        return ['route_id', 'route_name', 'outlet_id', 'outlet_name'];
    }

    public function array(array $array)
    {
        $data = $array;
        foreach ($data as $key => $row) {
            $value = (object)$row;
            $site = Site::on($this->connection)->where(['site_code' => $value->outlet_id])->first();
            $insert[] = ['rout_id' => $value->route_id, 'site_id' => $site->id, 'rspm_serl' => 1, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
        }
        $route_ids = array_column($insert, 'rout_id');
        RouteSite::on($this->connection)->whereIn('rout_id', $route_ids)->delete();
        if (!empty($insert)) {
            DB::connection($this->connection)->table('tl_rsmp')->insert($insert);
        }
    }

    public function route()
    {
        return Route::on($this->connection)->find($this->rout_id);
    }

    public function site()
    {
        return Site::on($this->connection)->find($this->site_id);
    }
}
