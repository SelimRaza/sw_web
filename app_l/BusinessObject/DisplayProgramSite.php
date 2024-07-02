<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Employee;
use App\MasterData\Site;
use Illuminate\Database\Eloquent\Model;

class DisplayProgramSite extends Model
{
    protected $table = 'tblt_program_site_mapping';

    public function site()
    {
        return Site::find($this->site_id);
    }
}