<?php

namespace App\DataExport;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductivityData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'GroupWiseData';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($start_date,$end_date,$acmp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->acmp_id = $acmp_id;
        return $instance;
    }
    public function headings(): array
    {
        return ['order_date',  'group_name','zone_name','Region', 'sr_name', 'order_number','rout_name','site_name','dealer_name',
            'item_name', 'item_code', 'Item Category', 'Item Class',
            'Order Qty', 'Order Amount', 'Delivery Qty', 'Delivery Amount'];
    }
   /* public function headings(): array
    {
        return ['order_date',  'group_name','zone_name','Region', 'Base_Name','SR_ID','SR_Name','SR_Mobile', 'rout_name','Total Outlet','OLT Visit',
            'Visit %','Successful Memo', 'Strike Rate %', 'Non Productive Call', 'Avg. OLT Contrib.', 'Item LPC',
            'Todays Tgt. Amnt', 'Total Order Amount', 'In Time','First Order Time','Last Order Time','T. Work Time', 'Show Location'];
    }*/

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("SELECT
  t1.ordm_date,
  t3.slgp_name,
  t5.zone_name,
  t6.dirg_name,
  concat(t7.aemp_usnm, '-', t7.aemp_name, '-', t7.aemp_mob1) AS aemp_name,
  t1.ordm_ornm,
  t9.rout_name,
  concat(t10.site_code, '-', t10.site_name)                  AS site_name,
  concat(t11.dlrm_code, '-', t11.dlrm_name)                  AS dlrm_name,
  t8.amim_name,
  t8.amim_code,
  concat(t12.itsg_code, '-', t12.itsg_name)                  AS itsg_name,
  concat(t13.itcl_code, '-', t13.itcl_name)                  AS itcl_name,
  t2.ordd_qnty,
  t2.ordd_oamt,
  t2.ordd_dqty,
  t2.ordd_odat
FROM tt_ordm AS t1
  INNER JOIN tt_ordd AS t2 ON t1.id = t2.ordm_id
  INNER JOIN tm_slgp AS t3 ON t1.slgp_id = t3.id
  INNER JOIN tl_sgsm AS t4 ON t1.slgp_id = t4.slgp_id AND t1.aemp_id = t4.aemp_id
  INNER JOIN tm_zone AS t5 ON t4.zone_id = t5.id
  INNER JOIN tm_dirg AS t6 ON t5.dirg_id = t6.id
  INNER JOIN tm_aemp AS t7 ON t1.aemp_id = t7.id
  INNER JOIN tm_amim AS t8 ON t2.amim_id = t8.id
  INNER JOIN tm_rout AS t9 ON t1.rout_id = t9.id
  INNER JOIN tm_site AS t10 ON t1.site_id = t10.id
  INNER JOIN tm_dlrm AS t11 ON t1.dlrm_id = t11.id
  INNER JOIN tm_itsg AS t12 ON t8.itsg_id = t12.id
  INNER JOIN tm_itcl AS t13 ON t8.itcl_id = t13.id
WHERE t1.ordm_date BETWEEN '$this->start_date' and '$this->end_date' and t1.acmp_id=$this->acmp_id");
        return collect($dataRow);
    }


}
