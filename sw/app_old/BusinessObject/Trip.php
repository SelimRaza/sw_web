<?php

namespace App\BusinessObject;

use App\MasterData\Depot;
use App\MasterData\Employee;
use App\MasterData\LifeCycleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Trip extends Model
{
    protected $table = 'tt_trip';
    protected $connection= '';
    public function __construct()
    {
        if (Auth::user()!=null){
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_tusr);
    }
    public function depot()
    {
        return Depot::on($this->connection)->find($this->dlrm_id);
    }
    public function status()
    {
        return LifeCycleStatus::on($this->connection)->find($this->lfcl_id);
    }
    public function tripType()
    {
        return TripType::on($this->connection)->find($this->ttyp_id);
    }

}
