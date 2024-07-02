<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SpaceZone extends Model
{
    protected $table = 'tl_spaz';
    protected $connection= '';
    protected $currentUser= '';

    protected $fillable = ['cont_id', 'spcm_id', 'is_national', 'lfcl_id', 'cont_id', 'aemp_iusr', 'aemp_eusr', 'zone_id'];

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }
}
