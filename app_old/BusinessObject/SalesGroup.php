<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SalesGroup extends Model
{
    protected $table = 'tm_slgp';
    protected $connection= '';
    public function __construct()
    {
        $this->connection=Auth::user()->country()->cont_conn;
    }

    public function company()
    {
        return Company::on($this->connection)->find($this->acmp_id);
    }
    public function priceList()
    {
        return PriceList::on($this->connection)->find($this->plmt_id);
    }
}