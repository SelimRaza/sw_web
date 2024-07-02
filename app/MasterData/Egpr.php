<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Egpr extends Model
{
    protected $table = 'tl_egpr';
    private $currentUser;
    public function __construct()
    {
        $this->currentUser = Auth::user();
    }
}
