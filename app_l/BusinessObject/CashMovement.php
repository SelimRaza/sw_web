<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    protected $table = 'tblt_cash_movement';

    public function cashMoveType()
    {
        return CashMoveType::find($this->cash_move_type_id);
    }
    public function cashMoveSource()
    {
        return CashSource::find($this->cash_source_id);
    }
}
