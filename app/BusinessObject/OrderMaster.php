<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    protected $table = 'tt_ordm';

    protected $fillable = ['ordm_amnt', 'lfcl_id', 'ordm_icnt'];

    public function details(){
        return $this->hasMany(OrderLine::class, 'ordm_id');
    }
}