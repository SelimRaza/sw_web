<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CancelOrderReason extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_ocrs';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }
    public function headings(): array
    {
        return ['order_cancel_reason_name', 'order_cancel_reason_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $data = new CancelOrderReason();
        $data->setConnection($this->connection);
        $data->ocrs_name = $value->order_cancel_reason_name;
        $data->ocrs_code = $value->order_cancel_reason_code;
        $data->lfcl_id = 1;
        $data->cont_id = $this->currentUser->employee()->cont_id;
        $data->aemp_iusr = $this->currentUser->employee()->id;
        $data->aemp_eusr = $this->currentUser->employee()->id;
        $data->save();

    }


}
