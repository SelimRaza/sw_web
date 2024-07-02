<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/3/2018
 * Time: 5:11 PM
 */

namespace App\BusinessObject;
use Illuminate\Database\Eloquent\Model;

class SiteVisited extends Model
{
    protected $table = 'th_ssvh';

    protected $guarded = ['id'];
}