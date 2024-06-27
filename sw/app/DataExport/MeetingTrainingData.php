<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeetingTrainingData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tt_mstp';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($mstp_name, $mstp_date, $start_time, $end_time)
    {
        $instance = new self();
        $instance->mstp_name = $mstp_name;
        $instance->mstp_date = $mstp_date;
        $instance->start_time = date('H:i',strtotime($start_time));
        $instance->end_time = date('H:i',strtotime($end_time));
        return $instance;
    }

    public function headings(): array
    {
        return ['Name', 'Date', 'Start Time', 'End Time'];
    }

    public function collection()
    {
        $dataRow = DB::connection($this->connection)->select("select mstp_name, mstp_date, start_time, end_time from `tt_mstp`");

        return collect($dataRow);
    }


}
