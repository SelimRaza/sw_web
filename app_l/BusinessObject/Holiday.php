<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'tblt_holiday';
    public function holidayType()
    {
        return HolidayType::find($this->type_id);
    }
}
