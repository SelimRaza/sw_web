<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'tm_oult';

    protected $fillable = ['oult_name', 'oult_olnm', 'oult_mob1', 'aemp_eusr'];

}
