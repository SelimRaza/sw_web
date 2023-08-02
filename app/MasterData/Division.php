<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\MasterData\Country;

class Division extends Model
{
    protected $table = 'tm_sdvm';
    public function country()
    {
        return Country::find($this->country_id);
    }
}
