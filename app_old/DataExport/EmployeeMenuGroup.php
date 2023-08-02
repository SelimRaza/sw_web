<?php

namespace App\DataExport;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeMenuGroup extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_amng';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($id)
    {
        $instance = new self();
        $instance->amng_id = $id;
        return $instance;
    }

    public function headings(): array
    {
        return ['user_name'];
    }

    public function array(array $array)
    {
        $userId=array();

        foreach ($array as $row) {
            $value = (object)$row;
            array_push($userId,"$value->user_name");
        }
        DB::table('users')->whereIn('email',$userId)->update(['remember_token' => '']);
        DB::connection($this->connection)->table('tm_aemp')->whereIn('aemp_usnm',$userId)->update(['amng_id' => $this->amng_id, 'aemp_eusr' => $this->currentUser->employee()->id]);


    }
}
