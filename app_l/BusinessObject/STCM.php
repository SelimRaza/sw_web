<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\Company;
use App\MasterData\Employee;
use App\MasterData\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanySiteMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tbld_site_company_mapping';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }


    public function headings(): array
    {
        return ['company_id', 'group_id','price_list_id', 'site_code',  'credit_fixed','party_type', 'credit_limit', 'limit_days'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $site=Site::($this->db)
        $companySiteMapping = CompanySiteMapping::where(['slgp_id' => $value->group_id, 'site_id' => $value->site_id])->first();
        if ($companySiteMapping == null) {
            $companySiteMapping = new CompanySiteMapping();
            $companySiteMapping->site_id = $value->site_id;
            $companySiteMapping->company_id = $value->company_id;
            $companySiteMapping->payment_type_id = $value->payment_type_id;
            $companySiteMapping->price_list_id = $value->price_list_id;
            $companySiteMapping->credit_limit = $value->credit_limit;
            $companySiteMapping->limit_days = $value->limit_days;
            $companySiteMapping->country_id = $this->currentUser->employee()->country_id;
            $companySiteMapping->created_by = $this->currentUser->employee()->id;
            $companySiteMapping->updated_by = $this->currentUser->employee()->id;
            $companySiteMapping->updated_count = 0;
            $companySiteMapping->save();
        } else {
            $companySiteMapping->payment_type_id = $value->payment_type_id;
            $companySiteMapping->price_list_id = $value->price_list_id;
            $companySiteMapping->credit_limit = $value->credit_limit;
            $companySiteMapping->limit_days = $value->limit_days;
            $companySiteMapping->updated_by = $this->currentUser->employee()->id;
            $companySiteMapping->updated_count = $companySiteMapping->updated_count + 1;
            $companySiteMapping->save();
        }
    }

}