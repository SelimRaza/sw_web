<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocationSection extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_lsct';
    private $currentUser;
    protected $connection= '';
    public function __construct()
    {
        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }
    public function headings(): array
    {
        return ['detartment_id', 'section_name', 'section_code'];
    }

    public function array(array $array)
    {
        $data = $array;
        foreach ($data as $key => $row) {
            $value = (object)$row;
            $insert[] = ['lsct_name' => $value->section_name, 'lsct_code' => $value->section_code, 'ldpt_id' => $value->detartment_id, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id];
        }
        if (!empty($insert)) {
            DB::connection($this->connection)->table('tm_lsct')->insert($insert);
        }
    }

}
