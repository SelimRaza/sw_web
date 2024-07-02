<?php

namespace App\BusinessObject;

use App\MasterData\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DefaultDiscountSiteMapping extends Model
{
    protected $table = 'tl_dfsm';
    protected $connection= '';
}
