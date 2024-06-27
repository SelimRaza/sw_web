<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturnReason extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dprt';
    private $currentUser;
    protected $connection = '';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['return_reason_name', 'return_reason_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;


        $returnReason = new ReturnReason();
        $returnReason->setConnection($this->connection);
        $returnReason->dprt_name = $value->return_reason_name;
        $returnReason->dprt_code = $value->return_reason_code;
        $returnReason->lfcl_id = 1;
        $returnReason->cont_id = $this->currentUser->country()->id;
        $returnReason->aemp_iusr = $this->currentUser->employee()->id;
        $returnReason->aemp_eusr = $this->currentUser->employee()->id;
        $returnReason->save();
    }


}
