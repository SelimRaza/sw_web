<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Bank extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_bank';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }
    public function headings(): array
    {
        return ['bank_name', 'bank_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $data = new Bank();
        $data->setConnection($this->connection);
        $data->bank_name = $value->bank_name;
        $data->bank_code = $value->bank_code;
        $data->lfcl_id = 1;
        $data->cont_id = $this->currentUser->employee()->cont_id;
        $data->aemp_iusr = $this->currentUser->employee()->id;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();
    }



}
