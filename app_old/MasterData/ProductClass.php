<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductClass extends Model
{
    protected $table = 'tm_itcl';
    private $currentUser;
    protected $connection = '';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }
    public function itemGroup()
    {
        return ProductGroup::on($this->connection)->find($this->itgp_id);
    }



}
