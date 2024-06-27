<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use App\MasterData\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SpaceSite extends Model
{
    protected $table = 'tl_spst';
    protected $connection= '';
    protected $currentUser= '';

    protected $guarded = ['id'];


    public function site()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
        return $this->setConnection($this->connection)->belongsTo(Site::class, 'site_id', 'id');
    }
}
