<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;

class TeleOrderDetails extends Model
{
    protected $table = 'tt_tlod';

    protected $guarded = ['id'];
}