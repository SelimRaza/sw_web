<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetSiteMappingData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tl_assm';
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

    public function headings(): array
    {
        return ['Asset', 'Group', 'Zone', 'Site Code', 'Site Name'];
    }

    public function collection()
    {
        $dataRow = DB::connection($this->connection)
            ->select("select t2.astm_name, concat(t3.slgp_code, ' - ', t3.slgp_name),
                        concat(t5.zone_code, ' - ', t5.zone_name), t4.site_code, t4.site_name
                        from tl_assm t1
                        inner join tm_astm t2 on t2.id = t1.astm_id
                        inner join tm_slgp t3 on t3.id = t1.slgp_id
                        inner join tm_site t4 on t4.id = t1.site_id
                        inner join tm_zone t5 on t5.id = t1.zone_id
                        where astm_id={$this->mapping_id}");

        return collect($dataRow);
    }


}
