<?php

namespace App\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DmCollectionLog extends Model
{
    protected $table = 'dm_collection_log';
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
