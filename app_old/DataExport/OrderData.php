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

class OrderData extends Model implements FromCollection, WithHeadings, WithHeadingRow
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

    public static function create($start_date, $end_date,  $slgp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
      //  $instance->acmp_id = $acmp_id;
        $instance->slgp_id = $slgp_id;
        return $instance;
    }

    public function headings(): array
    {
        return [
            'Order Date',
            'Group Name',
            'Region',
            'Zone Name',
            'Dealer Name',
            'SR Name',
            'Route Name',
            'Order Number',
            'Outlet Name',
            'Item Category',
            'Item Class',
            'Item Code',
            'Item Name',
            'Order Qty',
            'Order Amount',
            'Delivery Qty',
            'Delivery Amount',
            'Order Time'];
    }

    public function collection()
    {


        $dataRow = DB::connection($this->connection)->select("SELECT
              t1.ordm_date,
              t3.slgp_name, 
              concat(t6.dirg_code, '-', t6.dirg_name) as dirg_name,
              concat(t5.zone_code, '-', t5.zone_name) as zone_name, 
              concat(t11.dlrm_code, '-', t11.dlrm_name)                  AS dlrm_name,  
              concat(t7.aemp_usnm, '-', t7.aemp_name, '-', t7.aemp_mob1) AS aemp_name,
              t9.rout_name,
              t1.ordm_ornm,
              concat(t10.site_code, '-', t10.site_name)                  AS site_name,
              concat(t12.itsg_code, '-', t12.itsg_name)                  AS itsg_name,
              concat(t13.itcl_code, '-', t13.itcl_name)                  AS itcl_name,
              t8.amim_code,
              t8.amim_name,  
              t2.ordd_qnty,
              t2.ordd_oamt,
              t2.ordd_dqty,
              t2.ordd_odat,
              TIME(t1.ordm_time) as Order_Time
            FROM tt_ordm_d_cur_date AS t1
              INNER JOIN tt_ordd_d_cur_date AS t2 ON t1.id = t2.ordm_id
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
            WHERE t1.ordm_date BETWEEN '$this->start_date' and '$this->end_date' and t1.slgp_id=$this->slgp_id");
        return collect($dataRow);
      //  return collect(null);
    }


}
