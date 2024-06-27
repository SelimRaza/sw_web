<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/3/2018
 * Time: 5:11 PM
 */

namespace App\BusinessObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DlearProfileAdd extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tm_bizli';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($start_date, $end_date)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        return $instance;
    }

    public function headings(): array
    {

        return ['Issue_Date', 'Form_number', 'Name', 'Mobile','Father_Name',
            'Present_district','Permanent_district',
            'Blood_Group', 'Education','Present_Address',
            'Permanent_Address','Date_Of_Birth','Organization',
            'Experience', 'Reference', 'Dealer_number',
            'NID_Number','Mistiri_Type','Status','NID_Front','NID_Back','Photo','Signature'];
    }

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("
SELECT t1.`date_issue`AS Issue_Date,
t1.`dlrp_frno`AS Form_number,
t1.`dlrp_name`AS Name,
t1.`dlrp_mobn`AS Mobile,
t1.`dlrp_fnam`AS Father_Name,
t5.dsct_name AS Present_district,
t2.dsct_name AS Permanent_district,
t1.dlrp_bldg AS Blood_Group,
t1.dlrp_educ AS Education,
t1.dlrp_prad AS Present_Address,
t1.dlrp_pmad AS Permanent_Address,
t1.dlrp_edob AS Date_Of_Birth,
t1.dlrp_orgn AS Organization,
t1.dlrp_expr AS Experience,
t1.dlrp_refn AS Reference,
t1.dlrp_dlcd AS Dealer_number, 
t1.`dlrp_nidn`AS NID_Number,
t3.mtyp_name AS Mistiri_Type,
IF(t1.`dlrp_aprv`=0, 'Pending', 'Approved') AS Status,
concat(('https://images.sihirbox.com/'),t1.dlrp_nfmg) AS NID_Front,
concat(('https://images.sihirbox.com/'),t1.dlrp_nbmg) AS NID_Back,
concat(('https://images.sihirbox.com/'),t1.dlrp_eimg) AS Photo,
concat(('https://images.sihirbox.com/'),t1.dlrp_simg) AS Signature
FROM `tm_bizli` t1 JOIN tm_dsct t2 ON(t1.`dlrp_prmd`=t2.id) 
JOIN tm_dsct t5 ON (t1.dlrp_prsd=t5.id)
LEFT JOIN tm_mtyp t3 ON(t1.`dlrp_mtyp`=t3.id)
WHERE (t1.date_issue BETWEEN '$this->start_date' and '$this->end_date')
");

        return collect($dataRow);
    }
}