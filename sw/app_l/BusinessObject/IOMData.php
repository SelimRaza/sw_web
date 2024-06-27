<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/28/2018
 * Time: 12:38 PM
 */

namespace App\BusinessObject;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;

class IOMData extends Model
{

    protected $table = 'tblt_iom';


    public function employee()
    {
        return Employee::find($this->emp_id);
    }
}