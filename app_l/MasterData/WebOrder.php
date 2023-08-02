<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class WebOrder extends Model implements FromCollection,WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tbl_order_temp';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';
    protected $acmp_id='';
    protected $slgp_id='';
    protected $rout_id='';
    protected $site_id='';
    protected $sr_id='';
    protected $ordm='';
    protected $dlrm_id='';
    public function __construct($acmp_id,$slgp_id,$rout_id,$site_id,$sr_id,$dlrm_id)
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
            $this->acmp_id=$acmp_id;
            $this->slgp_id=$slgp_id;
            $this->rout_id=$rout_id;
            $this->site_id=$site_id;
            $this->sr_id=$sr_id;
            $this->dlrm_id=$dlrm_id;
            $this->ordm=uniqid();
        }
    }
    public function collection(){
        $items=array();
        $data=DB::connection($this->db)->select("SELECT 
                t4.amim_name,t4.amim_code,'0' ctn,'0' pics,'0' sp_disc,'0' is_prcnt      
                FROM tl_stcm t1
                INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id
                INNER JOIN tm_pldt t3 ON t2.id=t3.plmt_id
                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                WHERE t1.site_id=$this->site_id AND t1.slgp_id=$this->slgp_id ORDER BY t4.amim_name ASC;");
        foreach($data as $d){
            $dt=array($d->amim_name,$d->amim_code,$d->ctn,$d->pics,$d->sp_disc,$d->is_prcnt);
            array_push($items,$dt);
        }
        return collect([
            $items

        ]);
    }

    public function headings(): array
    {
        return [
            'amim_name',
            'amim_code',
            'ctn_qty',
            'pics_qty',
            'sp_disc',
            'is_percent'
        ];
    }

    public function model(array $value)
    {
        
            $sp_disc=0;
            $request=(object)$value;
                if($request->sp_disc>0){
                    $sp_disc=1;
                }
            if($request->ctn_qty !=0 || $request->pics_qty !=0){
                DB::connection($this->db)->select("INSERT IGNORE INTO tbl_order_temp SELECT null,'$this->ordm','$this->acmp_id','$this->slgp_id','$this->sr_id','$this->rout_id','$this->site_id','$this->dlrm_id',t1.plmt_id,
                        t4.id,'$request->ctn_qty','$request->pics_qty',t4.amim_duft,t3.pldt_tppr,'$request->sp_disc','$request->is_percent',$this->aemp_id,curdate(),'$sp_disc',current_timestamp,current_timestamp,'','','','','','','','','','DFLT','','',''                   
                        FROM tl_stcm t1
                        INNER JOIN tm_plmt t2 ON t1.plmt_id=t2.id
                        INNER JOIN tm_pldt t3 ON t2.id=t3.plmt_id
                        INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                        WHERE t1.slgp_id='$this->slgp_id' AND t1.site_id='$this->site_id' AND t4.amim_code='$request->amim_code'
                        ON DUPLICATE KEY UPDATE sp_disc='$request->sp_disc',is_percent='$request->is_percent',
                        amim_ctn=$request->ctn_qty,amim_pics=$request->pics_qty
                        
                        ");
            }
        
           
        
    }
    

}
