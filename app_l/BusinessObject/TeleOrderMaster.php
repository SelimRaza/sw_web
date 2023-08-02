<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;

class TeleOrderMaster extends Model
{
    protected $table = 'tt_tlom';

    protected $guarded = ['id'];

    public function tele_order_details()
    {
        return $this->hasMany(TeleOrderDetails::class, 'id');
    }
}
