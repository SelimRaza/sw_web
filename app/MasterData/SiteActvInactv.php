<?php

namespace App\MasterData;

use DB;
use App\MasterData\RouteSite;
use App\MasterData\RouteSiteLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiteActvInactv extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'site_code',
            'lfcl_id',
        ];
    }

    public function model(array $value)
    {
        
        $value = (object)$value;
        $site=Site::on($this->connection)->where(['site_code' => $value->site_code])->first();
        if($site !=null){
            $site->lfcl_id=$value->lfcl_id;
            $site->aemp_eusr=Auth::user()->employee()->id;
            $site->save();
        }
       
       
    }

    public function array(array $values)
    {
        $row=1;
        try {
            $infos = $values;
            $upload_data = [];

            for($i=0;$i<count($infos);$i++) {
                $site = Site::on($this->connection)->where(['site_code' => $infos[$i]['site_code']])->first();
                $row++;
                if ($site != null) {
                    $site->lfcl_id = $infos[$i]['lfcl_id'];
                    $site->aemp_eusr = $this->currentUser->employee()->id;
                    $site->save();
                    $route_site=RouteSite::on($this->connection)->where(['site_id' => $site->id])->first();
                    if($route_site != null){
                        $route_log = new RouteSiteLog();
                        $route_log->rout_id = $route_site->rout_id;
                        $route_log->site_id = $route_site->site_id;
                        $route_log->rout_code = $route_site->rout_code;
                        $route_log->site_code = $route_site->site_code;
                        $route_log->rspm_serl = $route_site->rspm_serl;
                        $route_log->lfcl_id = $route_site->lfcl_id;
                        $route_log->aemp_eusr = $route_site->aemp_eusr;
                        $route_log->cont_id = $route_site->cont_id;
                        $route_log->aemp_iusr = $route_site->aemp_iusr;
                        $route_log->var = $route_site->var;
                        $route_log->created_by = $this->currentUser->employee()->id;
                        $route_log->save();
                        
                        $route_site->delete();
                    }
                }
            }
        }catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function subChannel()
    {
        return SubChannel::on($this->connection)->find($this->scnl_id);
    }

    public function outletCategory()
    {
        return OutletCategory::on($this->connection)->find($this->otcg_id);
    }

    public function market()
    {
        return Market::on($this->connection)->find($this->mktm_id);
    }

    public function outlet()
    {
        return Outlet::on($this->connection)->find($this->outl_id);
    }

}
