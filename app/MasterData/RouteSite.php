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
use App\MasterData\Site;
use App\MasterData\Route;

class RouteSite extends Model implements FromCollection, WithHeadings, WithHeadingRow
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
    public function model(array $row)
    {
        $value = (object)$row;
        $site = Site::on($this->db)->where(['site_code' => $value->site_code])->first();
        $rout = Route::on($this->db)->where(['rout_code' => $value->rout_code])->first();
        if ($site != null) {
            $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $rout->id, 'site_id' => $site->id])->first();
          //  dd($routeSite1);
            if ($routeSite1 == null) {
                $routeSite = new RouteSite();
                $routeSite->setConnection($this->connection);
                $routeSite->site_id = $site->id;
                $routeSite->rout_id =$rout->id;
                $routeSite->rspm_serl = 1;
                $routeSite->cont_id = $this->currentUser->employee()->cont_id;
                $routeSite->lfcl_id = 1;
                $routeSite->aemp_iusr = $this->currentUser->employee()->id;
                $routeSite->aemp_eusr = $this->currentUser->employee()->id;
                $routeSite->var = 1;
                $routeSite->attr1 = '';
                $routeSite->attr2 = '';
                $routeSite->attr3 = 0;
                $routeSite->attr4 = 0;
                $routeSite->save();
            }
        }
    }

    public function collection()
    {

        $routeArray = array();
        // $route = Route::on($this->connection)->findorfail($this->route_id1);
        // $routeSites = RouteSite::on($this->connection)->where(['rout_id' => $this->route_id1])->get();
        // if (sizeof($routeSites) > 0) {
        //     foreach ($routeSites as $routeSite) {
        //         $routeArray[] = [$route->rout_code,$routeSite->site()->site_code];
        //     }
        // } else {
        //     $routeArray[] = [$route->rout_code, $route->rout_name, '', ''];
        // }
        $routeArray[] = ['', ''];

        return collect([
            $routeArray
        ]);
    }

    public function headings(): array
    {
        return ['rout_code', 'site_code'];
    }

    // public function array(array $array)
    // {
    //     $data = $array;
    //     foreach ($data as $key => $row) {
    //         $value = (object)$row;
    //         $site = Site::on($this->connection)->where(['site_code' => $value->outlet_id])->first();
    //         $insert[] = ['rout_id' => $value->route_id, 'site_id' => $site->id, 'rspm_serl' => 1, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
    //     }
    //     $route_ids = array_column($insert, 'rout_id');
    //     RouteSite::on($this->connection)->whereIn('rout_id', $route_ids)->delete();
    //     if (!empty($insert)) {
    //         DB::connection($this->connection)->table('tl_rsmp')->insert($insert);
    //     }
    // }
    //  public function array(array $array)
    // {
    //     $data = $array;
    //     foreach ($data as $key => $row) {
    //         $value = (object)$row;
    //         $site = Site::on($this->connection)->where(['site_code' => $value->outlet_id])->first();
    //         $insert[] = ['rout_id' => $value->route_id, 'site_id' => $site->id, 'rspm_serl' => 1, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
    //     }
    //     $route_ids = array_column($insert, 'rout_id');
    //     RouteSite::on($this->connection)->whereIn('rout_id', $route_ids)->delete();
    //     if (!empty($insert)) {
    //         DB::connection($this->connection)->table('tl_rsmp')->insert($insert);
    //     }
    // }

    public function route()
    {
        return Route::on($this->connection)->find($this->rout_id);
    }

    public function site()
    {
        return Site::on($this->connection)->find($this->site_id);
    }
}
