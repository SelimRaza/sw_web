<?php

namespace App\Mapping;

use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\PriceList;
class PLDT  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_pldt';
    private $currentUser;
    protected $db = '';
    protected $conuntry_id='';
    protected $aemp_id='';


    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->conuntry_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return ['item_code', 'price_list_code','ctn_price','ctn_grv_price'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $plmt_id=$this->getPlmtId($request->price_list_code);
        $amim=$this->getAmimId($request->item_code);
        if(!$amim){
            return;
        }
        $amim_id=$amim->id;
        $factor=$amim->amim_duft;
        $pldt=PLDT::on($this->db)->where(['amim_id'=>$amim_id,'plmt_id'=>$plmt_id])->first();
        if($pldt){
            $pldt->pldt_dppr=$request->ctn_price/$factor;
            $pldt->pldt_dgpr=$request->ctn_grv_price/$factor;
            $pldt->pldt_tppr=$request->ctn_price/$factor;
            $pldt->pldt_tpgp=$request->ctn_grv_price/$factor;
            $pldt->pldt_mrpp=$request->ctn_price/$factor;
            $pldt->aemp_eusr=$this->aemp_id;
            $pldt->save();
        }else{
            $pldt=new PLDT();
            $pldt->setConnection($this->db);
            $pldt->plmt_id=$plmt_id;
            $pldt->amim_id=$amim_id;
            $pldt->plmt_code=$request->price_list_code;
            $pldt->amim_code=$request->item_code;
            $pldt->pldt_dppr=$request->ctn_price/$factor;
            $pldt->pldt_dgpr=$request->ctn_grv_price/$factor;
            $pldt->pldt_tppr=$request->ctn_price/$factor;
            $pldt->pldt_tpgp=$request->ctn_grv_price/$factor;
            $pldt->pldt_mrpp=$request->ctn_price/$factor;
            $pldt->pldt_snme=$amim->amim_name;
            $pldt->amim_duft=$amim->amim_duft;
            $pldt->amim_dunt=$amim->amim_dunt;
            $pldt->amim_runt=$amim->amim_runt;
            $pldt->cont_id=$this->conuntry_id;
            $pldt->aemp_eusr=$this->aemp_id;
            $pldt->aemp_iusr=$this->aemp_id;
            $pldt->lfcl_id=1;
            $pldt->save();
        }
      }
      
    }
    public function getPlmtId($plmt_code){
        $plmt= PriceList::on($this->db)->where(['plmt_code'=>$plmt_code])->first();
        $plmt_id=$plmt->id;
        return $plmt_id;
    }
    public function getAmimId($amim_code){
        return  SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
        
    }

}