<?php

namespace App\BusinessObject;

use App\MasterData\Employee;
use App\MasterData\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TradeMarketing extends Model
{
    protected $table = 'tl_tmzn';
    protected $connection= '';
    protected $currentUser= '';

    protected $guarded = ['id'];

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }


    public function zone()
    {
        return $this->setConnection($this->connection)->belongsTo(Zone::class, 'zone_id', 'id');
    }


    public function employee()
    {
        return $this->setConnection($this->connection)->belongsTo(Employee::class, 'aemp_id', 'id');
    }

}
