<?php

namespace App\BusinessObject;


use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Recordings extends Model
{
    protected $table = 'tm_rcds';
    protected $connection= '';
    protected $currentUser= '';

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

}
