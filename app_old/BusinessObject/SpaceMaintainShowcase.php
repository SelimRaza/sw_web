<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SpaceMaintainShowcase extends Model
{
    protected $table = 'tl_spsb';
    protected $connection= '';
    protected $currentUser= '';



    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    protected $fillable = ['amim_id', 'spcm_id'];

}
