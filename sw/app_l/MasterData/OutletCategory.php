<?php

namespace App\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class OutletCategory extends Model
{
    protected $table = 'tm_otcg';

    private $currentUser;
    protected $connection = '';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

}
