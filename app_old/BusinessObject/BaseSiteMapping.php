<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Base;
use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BaseSiteMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tbld_base_site_mapping';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    public function headings(): array
    {
        return ['group_id', 'group_name', 'base_id', 'base_name', 'outlet_id', 'outlet_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $baseSiteMapping = BaseSiteMapping::where(['sales_group_id' => $value->group_id, 'site_id' => $value->outlet_id])->first();
        if ($baseSiteMapping != null) {
            $baseSiteMapping->base_id = $value->base_id;
            $baseSiteMapping->updated_by = $this->currentUser->employee()->id;
            $baseSiteMapping->save();
        } else {
            $baseSiteMapping = new BaseSiteMapping();
            $baseSiteMapping->sales_group_id =$value->group_id;
            $baseSiteMapping->base_id = $value->base_id;
            $baseSiteMapping->site_id = $value->outlet_id;
            $baseSiteMapping->country_id = $this->currentUser->employee()->country_id;
            $baseSiteMapping->created_by = $this->currentUser->employee()->id;
            $baseSiteMapping->updated_by = $this->currentUser->employee()->id;
            $baseSiteMapping->save();
        }
    }

    public function base()
    {
        return Base::find($this->base_id);
    }

}