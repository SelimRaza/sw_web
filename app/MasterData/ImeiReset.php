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

            foreach ($staff_ids as $staff_id) {
                
                $user = User::where('email', $staff_id)->first();
                $user->device_imei = 'K';
                $user->save();
            }

//            DB::select("UPDATE users SET device_imei = 'N', updated_at = '{$date}' WHERE email IN ($search_string)");
//            DB::select("UPDATE users SET device_imei = N WHERE email IN ($search_string)");

        }catch(\Exception $e)
        {
            return;
        }
    }
}
