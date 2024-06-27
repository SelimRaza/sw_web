<?php

namespace App\MasterData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ezpr extends Model
{
    protected $table = 'tl_ezpr';
    private $currentUser;
    public function __construct()
    {
        $this->currentUser = Auth::user();
    }
}
