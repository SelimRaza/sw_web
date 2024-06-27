<?php

namespace App\MasterData;
use Illuminate\Database\Eloquent\Model;
class Company extends Model
{
    protected $table = 'tm_acmp';

    public function country()
    {
        return Country::find($this->cont_id);
    }
}
