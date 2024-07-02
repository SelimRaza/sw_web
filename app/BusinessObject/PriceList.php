<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PriceList extends Model
{
    protected $table = 'tm_plmt';
    protected $connection= '';
    public function __construct()
    {
        $this->connection=Auth::user()->country()->cont_conn;
    }

    public function group()
    {
        return SalesGroup::on($this->connection)->find($this->slgp_id);
    }
}
