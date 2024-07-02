<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/28/2018
 * Time: 12:38 PM
 */

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;

class TutorialQuestion extends Model
{

    protected $table = 'tt_ttqs';

    protected $guarded = ['id'];
}