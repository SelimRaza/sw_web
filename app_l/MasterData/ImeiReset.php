<?php

namespace App\MasterData;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ImeiReset extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'users';
    protected $fillable = ['device_imei'];
    public $timestamps = false;

    public function headings(): array
    {
        return ['staff_id'];
    }

    public function array(array $values)
    {

        try {
            $staff_ids = array_column($values, 'staff_id');
//            $search_string = implode(',', $staff_ids);

//            $date = date('Y-m-d H:i:s');

            foreach ($staff_ids as $staff_id) {
                $user = User::where('email', $staff_id)->first();
                $user->device_imei = 'N';
                $user->save();
            }
//            ImeiReset::whereIn('email', $staff_ids)->update([
//                'device_imei' => 'N'
////                'updated_at'  => date('Y-m-d H:i:s')
//            ]);

//            DB::select("UPDATE users SET device_imei = 'N', updated_at = '{$date}' WHERE email IN ($search_string)");
//            DB::select("UPDATE users SET device_imei = N WHERE email IN ($search_string)");

        }catch(\Exception $e)
        {
            dd($e);
            return;
        }
    }
}
