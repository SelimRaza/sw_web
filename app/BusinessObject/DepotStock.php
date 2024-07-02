<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 11/16/2019
 * Time: 2:27 PM
 */

namespace App\BusinessObject;

use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DepotStock extends Model
{
    protected $table = 'tt_dlst';
    protected $connection= '';
    public function __construct()
    {
        $this->connection=Auth::user()->country()->cont_conn;
    }
    public function sku()
    {
        return SKU::on($this->connection)->find($this->amim_id);
    }

}