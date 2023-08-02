<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
//WithHeadingRow,,WithChunkReading,WithBatchInserts
class RequestReportData extends Model implements FromCollection,WithHeadings
{
    protected $table = 'GroupWiseData';
    private $currentUser;
    protected $connection = '';

    public function __construct($query,$cont_conn,$heading)
    {
        // if (Auth::user() != null) {
        //     $this->currentUser = Auth::user();
        //     $this->connection = Auth::user()->country()->cont_conn;
        // }
        $this->query=$query;
        $this->db=$cont_conn;
        $this->heading=$heading;
    }
    // public function batchSize(): int
    // {
    //     return 1000;
    // }
    // public function chunkSize(): int
    // {
    //     return 1000;
    // }

    public static function create($query)
    {
        $instance = new self();
        $instance->query = $query;
        return $instance;
    }

   

    public function collection()
    {


      //  $dataRow = DB::connection($this->connection)->select("");
       $dataRow= DB::connection($this->db)->select(DB::raw($this->query));
        return collect($dataRow);
      //  return collect(null);
    }
    public function headings(): array
    {
     //return $this->collection()->first()->keys()->toArray();
     //return array_keys($this->query()->first()->toArray());
     return $this->heading;
    }
    // public function headingRow(): int
    // {
    //     return 1;
    // }


}
