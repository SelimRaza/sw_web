<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RSMP extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;
    private $db;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'rout_code',
            'site_code',
        ];
    }

    public function model(array $value)
    {
       
        $value = (object)$value;
        $site = Site::on($this->db)->where(['site_code' => $value->site_code])->first();
        $rout = Route::on($this->db)->where(['rout_code' => $value->rout_code])->first();
        if ($site != null) {
            $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $rout->id, 'site_id' => $site->id])->first();
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
}
