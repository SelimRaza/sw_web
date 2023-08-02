<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;

class TeleOrderTracking extends Model
{
    protected $table = 'tt_tltr';

    public $fillable = ['order_id'];

    /***
     * *
     * * tltr_ordr = 1 Ordered
     * * tltr_ordr = 0 Non Productive / Not Ordered
     * *
     * */
}
