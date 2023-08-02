<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;

class NoteEmployee extends Model
{
    protected $table = 'tl_enmp';
    public function employee()
    {
        return Employee::find($this->aemp_id);
    }
}