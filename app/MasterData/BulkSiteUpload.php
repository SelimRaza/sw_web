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

class BulkSiteUpload extends Model implements WithHeadings,ToModel,WithHeadingRow{
    protected $table = 'tl_rsmp';
    private $currentUser;
    private $route_id1 = 0;
    protected $connection = '';
    protected $id='';

    public function __construct($id)
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
            $this->id=$id;
        }
    }
    public function headings(): array
    {
        return ['site_code','rout_id'];
    }

    public function model(array $row)
    {
        $value= (object)$row;
        $site = Site::on($this->connection)->where(['site_code'=>$value->site_code])->first();
        if ($site != null) {
            $routeSite1 = RouteSite::on($this->connection)->where(['rout_id' =>$value->rout_id, 'site_id' => $site->id])->first();
            if ($routeSite1 == null) {
                $routeSite = new RouteSite();
                $routeSite->setConnection($this->connection);
                $routeSite->site_id = $site->id;
                $routeSite->rout_id =$value->rout_id;
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
    
}
