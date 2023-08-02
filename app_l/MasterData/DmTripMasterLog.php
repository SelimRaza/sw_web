<?php
namespace App\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DmTripMasterLog extends Model
{
    protected $table = 'dm_trip_master_log';
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

