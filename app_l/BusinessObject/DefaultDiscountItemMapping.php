<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DefaultDiscountItemMapping extends Model
{
    protected $table = 'tm_dfim';
    protected $connection= '';
}
