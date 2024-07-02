<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetMappingData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tl_astd';
    private $currentUser;
    protected $connection = '';
    protected $mapping_id;

    public function __construct($id)
    {
        $this->mapping_id = $id;
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

//    public static function create($mstp_name, $mstp_date, $start_time, $end_time)
//    {
//        $instance = new self();
//        $instance->mstp_name = $mstp_name;
//        $instance->mstp_date = $mstp_date;
//        $instance->start_time = date('H:i',strtotime($start_time));
//        $instance->end_time = date('H:i',strtotime($end_time));
//        return $instance;
//    }

    public function headings(): array
    {
        return ['Asset',	'Group',	'Product Code',	'Product Name'];
    }

    public function collection()
    {
//        ->select("select t2.astm_name, t3.slgp_name, t3.slgp_code, t4.amim_name, t4.amim_code

        $dataRow = DB::connection($this->connection)
            ->select("select t2.astm_name, CONCAT(t3.slgp_code, ' - ', t3.slgp_name), t4.amim_code, t4.amim_name
                        from tl_astd t1
                        inner join tm_astm t2 on t2.id = t1.astm_id
                        inner join tm_slgp t3 on t3.id = t1.slgp_id
                        inner join tm_amim t4 on t4.id = t1.amim_id
                        where astm_id={$this->mapping_id}");

        return collect($dataRow);
    }


}
