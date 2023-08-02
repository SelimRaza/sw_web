<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/24/2018
 * Time: 4:28 PM
 */

namespace App\BusinessObject;

use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;

class DisplayProgramCondition extends Model
{
    protected $table = 'tbld_program_display_condition';
    public function sku()
    {
        return SKU::find($this->sku_id);
    }
}