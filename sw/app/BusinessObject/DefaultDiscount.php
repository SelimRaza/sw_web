<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DefaultDiscount extends Model
{
    protected $table = 'tm_dfdm';
    protected $connection= '';
}
