<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;

class CashMoveType extends Model
{
    protected $table = 'tbld_cash_move_type';
    public function cashType()
    {
        return CashType::find($this->cash_type_id);
    }
}
