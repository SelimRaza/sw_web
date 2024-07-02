<?php

namespace App\BusinessObject;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HolidayPolicyMapping extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tblt_holiday_policy_mapping';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }


    public function headings(): array
    {
        return ['holiday_id','policy_id', 'policy_name'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $holidayPolicyMapping = HolidayPolicyMapping::where(['holiday_id' => $value->holiday_id, 'policy_id' => $value->policy_id])->first();
        if ($holidayPolicyMapping == null) {
            $holidayPolicyMapping = new HolidayPolicyMapping();
            $holidayPolicyMapping->holiday_id = $value->holiday_id;
            $holidayPolicyMapping->policy_id = $value->policy_id;
            $holidayPolicyMapping->country_id = $this->currentUser->employee()->country_id;
            $holidayPolicyMapping->created_by = $this->currentUser->employee()->id;
            $holidayPolicyMapping->updated_by = $this->currentUser->employee()->id;
            $holidayPolicyMapping->save();
        }
    }

    public function holiday()
    {
        return Holiday::find($this->holiday_id);
    }
    public function hrPolicy()
    {
        return HRPolicy::find($this->policy_id);
    }
}
