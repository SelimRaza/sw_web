<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SpaceMaintainFreeAmount extends Model
{
    protected $table = 'tl_spam';
    protected $connection= '';
    protected $currentUser= '';



    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    protected $guarded = [];

}
