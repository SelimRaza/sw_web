<?php
namespace App\BusinessObject\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Database\Eloquent\Model;
class SitePromotion extends Model implements  FromCollection,WithHeadings, WithHeadingRow
{
    protected $table = 'tl_prsm';
    protected $connection = '';
    protected $prmr_id;
    public function __construct($id)
    {
        $this->prmr_id=$id;
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }
    public function headings(): array
    {
        return ['Outlet_Code',	'Outlet_Name',	'Promotion_Code'];
    }

    public function collection()
    {
        $dataRow = DB::connection($this->connection)
            ->select("Select 
            t2.site_code,t2.site_name,t3.prms_code 
            FROM tl_prsm t1
            INNER JOIN tm_site t2 ON t1.site_id=t2.id
            INNER JOIN tm_prmr t3 ON t1.prmr_id=t3.id WHERE t1.prmr_id='$this->prmr_id'");

        return collect($dataRow);
    }
}
