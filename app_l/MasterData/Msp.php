<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Msp extends Model
{
    protected $table = 'tm_mspm';
    private $currentUser;
    public function __construct()
    {
        $this->currentUser = Auth::user();
    }
 
}
