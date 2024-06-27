<?php

namespace App\BusinessObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Employee;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\Mapping\PLDT;
use DateTime;
use Carbon\Carbon;
class FmsOrder  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tt_ordm';
    private $currentUser;
    protected $db = '';
    protected $con_id='';
    protected $empId='';
    protected $site_id='';
    protected $slgp_id='';
    protected $depot_id='';
    protected $sr_id='';
    protected $plmt_id='';
    public function __construct($sr_id,$slgp_id,$depot_id,$site_id,$plmt_id)
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
        $this->sr_id=$sr_id;
        $this->site_id=$site_id;
        $this->slgp_id=$slgp_id;
        $this->depot_id=$depot_id;
        $this->plmt_id=$plmt_id;
    }

    public function headings(): array
    {
        return ['item_code','pcs'];
    }

    public function array(array $array)
    {
       DB::connection($this->db)->beginTransaction();
        try{
            $orderSequence = OrderSequence::on($this->db)->where(['aemp_id' => $this->sr_id, 'srsc_year' => date('y')])->first();
            if ($orderSequence == null) {
                $orderSequence = new OrderSequence();
                $orderSequence->setConnection($this->db);
                $orderSequence->aemp_id = $this->sr_id;
                $orderSequence->srsc_year = date('y');
                $orderSequence->srsc_ocnt = 0;
                $orderSequence->srsc_rcnt = 0;
                $orderSequence->srsc_ccnt = 0;
                $orderSequence->cont_id = Auth::user()->country()->id;
                $orderSequence->lfcl_id = 1;
                $orderSequence->aemp_iusr = $this->aemp_id;
                $orderSequence->aemp_eusr = $this->aemp_id;
                $orderSequence->save();
            }
            $employee = Employee::on($this->db)->where(['id' => $this->sr_id])->first();
            $orderMaster = new OrderMaster();
            $orderMaster->setConnection($this->db);
            $order_id = "O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);
            $order_amount =0;
            $orderMaster->ordm_ornm = $order_id;
            $orderMaster->aemp_id = $this->sr_id;
            $orderMaster->slgp_id = $this->slgp_id;
            $orderMaster->dlrm_id = $this->depot_id;
            $orderMaster->acmp_id = 1;
            $orderMaster->site_id = $this->site_id;
            $orderMaster->rout_id =0;
            $orderMaster->odtp_id = 1;
            $orderMaster->mspm_id =0;
            $orderMaster->ocrs_id = 0;
            $orderMaster->ordm_pono = '';
            $orderMaster->aemp_cusr = 0;
            $orderMaster->ordm_note = '';
            $orderMaster->ordm_date = date('Y-m-d');
            $orderMaster->ordm_time =date('Y-m-d H:i:s');
            $orderMaster->ordm_drdt =Carbon::now()->addDays(1)->format('Y-m-d');
            $orderMaster->ordm_dltm = date('Y-m-d H:i:s');
            $orderMaster->geo_lat =0;
            $orderMaster->geo_lon =0;
            $orderMaster->ordm_dtne =0;
            $orderMaster->ordm_amnt =0;
            $orderMaster->ordm_icnt =1;
            $orderMaster->plmt_id =$this->plmt_id;
            $orderMaster->cont_id =Auth::user()->country()->id;
            $orderMaster->lfcl_id =1;
            $orderMaster->aemp_iusr =$this->aemp_id;
            $orderMaster->aemp_eusr =$this->aemp_id;
            $orderMaster->save();
            //dd($orderMaster);
            foreach($array as $row){
                $request=(object)$row;
                $amim=$this->getAmimId($request->item_code);
                $pldt=$this->getPrice($amim->id);
                $t_exc=($pldt->pldt_tppr*$request->pcs*$amim->amim_pexc)/100;
                $t_vat=($pldt->pldt_tppr*$request->pcs*$amim->amim_pvat)/100;
                $orderLine = new OrderLine();
                $orderLine->setConnection($this->db);
                $orderLine->ordm_id = $orderMaster->id;
                $orderLine->ordm_ornm = $order_id;
                $orderLine->amim_id =$amim->id;
                $orderLine->ordd_qnty = $request->pcs;
                $orderLine->ordd_inty = $request->pcs;
                $orderLine->ordd_cqty = 0;
                $orderLine->ordd_dqty = 0;
                $orderLine->ordd_opds = 0;
                $orderLine->ordd_cpds = 0;
                $orderLine->ordd_dpds = 0;
                $orderLine->ordd_duft =$pldt->amim_duft;
                $orderLine->ordd_uprc =$pldt->pldt_tppr;
                $orderLine->ordd_runt = 1;
                $orderLine->ordd_dunt = 1;
                $orderLine->prom_id =0;
                $orderLine->ordd_spdi = 0;
                $orderLine->ordd_spdo = 0;
                $orderLine->ordd_spdc = 0;
                $orderLine->ordd_spdd = 0;
                $orderLine->ordd_dfdo =0;
                $orderLine->ordd_dfdc = 0;
                $orderLine->ordd_dfdd = 0;
                $orderLine->ordd_excs =$amim->amim_pexc;
                $orderLine->ordd_ovat =$amim->amim_pvat;
                $orderLine->ordd_tdis = 0;
                $orderLine->ordd_texc =$t_exc;
                $orderLine->ordd_tvat =$t_vat;
                $orderLine->ordd_oamt =$pldt->pldt_tppr*$request->pcs+$t_exc+$t_vat;
                $orderLine->ordd_ocat = 0;
                $orderLine->ordd_odat = 0;
                $orderLine->ordd_amnt =$pldt->pldt_tppr*$request->pcs+$t_exc+$t_vat;
                $orderLine->ordd_rqty = 0;
                $orderLine->ordd_smpl =0;
                $orderLine->lfcl_id = 11;
                $orderLine->cont_id =Auth::user()->country()->id;
                $orderLine->aemp_iusr =$this->aemp_id;
                $orderLine->aemp_eusr =$this->aemp_id;
                $orderLine->save();
            }
            $orderSequence->srsc_ocnt = $orderSequence->srsc_ocnt + 1;
            $orderSequence->aemp_eusr =$this->aemp_id;
            $orderSequence->save();
            $master_data=$this->getTotalOrderAmount($orderMaster->id);
            $orderMaster->ordm_icnt=$master_data[0]->icnt;
            $orderMaster->ordm_amnt=$master_data[0]->net_amount;
            $orderMaster->lfcl_id=11;
            $orderMaster->save();
            DB::connection($this->db)->commit();

        }
        catch(\Exception $e){
           DB::connection($this->db)->rollback();
           dd($e->getMessage());
        }  
    }
    public function getAmimId($amim_code){
        $data=ItemMaster::on($this->db)->where(['amim_code'=>$amim_code])->first();
        return $data;
    }
    public function getPrice($amim_id){
        $data=PLDT::on($this->db)->where(['plmt_id'=>$this->plmt_id,'amim_id'=>$amim_id])->first();
        return $data;
    }
    public function getTotalOrderAmount($ordm_id){
        $data=DB::connection($this->db)->select("SELECT 
                sum(ordd_oamt)net_amount,count(amim_id) icnt
                FROM `tt_ordd` where ordm_id=$ordm_id");
        return $data;

    }

    

}