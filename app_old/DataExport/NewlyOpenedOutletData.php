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

class NewlyOpenedOutletData extends Model implements FromCollection, WithHeadings, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($start_date,$end_date,$acmp_id,$slgp_id)
    {
        $instance = new self();
        $instance->start_date = $start_date;
        $instance->end_date = $end_date;
        $instance->acmp_id = $acmp_id;
        $instance->slgp_id=$slgp_id;
        return $instance;
    }

    public function headings(): array
    {
        return ['Date','ID','Staff_ID','User_Name',
        'Outlet qty'];
    }

    public function collection()
    {
        if($this->start_date && $this->end_date){
            $dataRow = DB::connection($this->connection)->select("
            SELECT Date(t2.`created_at`)AS Date,t3.id,t3.aemp_usnm AS Stuff_ID,t3.aemp_name AS User_Name,COUNT(t1.id)AS OUT_QTY
            FROM `tm_site`t1 JOIN tblg_otop t2 ON(t1.`id`=t2.site_id)
            JOIN tm_aemp t3 ON(t2.aemp_iusr=t3.id)
            JOIN tl_sgsm t4 ON(t3.id=t4.aemp_id)
            JOIN tm_slgp t5 ON(t4.`slgp_id`=t5.id)
            where t5.id=$this->slgp_id AND t5.acmp_id=$this->acmp_id
            AND (t2.`created_at` BETWEEN '$this->start_date' AND '$this->end_date')
            GROUP by t3.id,t3.aemp_usnm,t3.aemp_name,Date(t2.`created_at`)
            ");
        }
        else if($this->start_date !="" && $this->end_date==""){
            $dataRow = DB::connection($this->connection)->select("
            SELECT Date(t2.`created_at`)AS Date,t3.id,t3.aemp_usnm AS Stuff_ID,t3.aemp_name AS User_Name,COUNT(t1.id)AS OUT_QTY
            FROM `tm_site`t1 JOIN tblg_otop t2 ON(t1.`id`=t2.site_id)
            JOIN tm_aemp t3 ON(t2.aemp_iusr=t3.id)
            JOIN tl_sgsm t4 ON(t3.id=t4.aemp_id)
            JOIN tm_slgp t5 ON(t4.`slgp_id`=t5.id)
            where t5.id=$this->slgp_id AND t5.acmp_id=$this->acmp_id
            AND (t2.`created_at`>='$this->start_date')
            GROUP by t3.id,t3.aemp_usnm,t3.aemp_name,Date(t2.`created_at`)
            ");
        }
        else{
            $dataRow = DB::connection($this->connection)->select("
            SELECT Date(t2.`created_at`)AS Date,t3.id,t3.aemp_usnm AS Stuff_ID,t3.aemp_name AS User_Name,COUNT(t1.id)AS OUT_QTY
            FROM `tm_site`t1 JOIN tblg_otop t2 ON(t1.`id`=t2.site_id)
            JOIN tm_aemp t3 ON(t2.aemp_iusr=t3.id)
            JOIN tl_sgsm t4 ON(t3.id=t4.aemp_id)
            JOIN tm_slgp t5 ON(t4.`slgp_id`=t5.id)
            where t5.id=$this->slgp_id AND t5.acmp_id=$this->acmp_id
            GROUP by t3.id,t3.aemp_usnm,t3.aemp_name,Date(t2.`created_at`)
            ");
        }

        return collect($dataRow);
       // return collect(null);
    }


}
