<?php

namespace App\BusinessObject;

use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\PriceList;
Use App\MasterData\SubCategory;
use App\BusinessObject\SalesGroupSku;
use App\BusinessObject\Promotion;
use App\BusinessObject\PromotionDetails;
use App\BusinessObject\SalesGroup;
class BulkPromUpload  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tt_prom';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';


    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return ['prom_name', 'prom_code','slgp_code','start_date','end_date','buy_item_code','free_item_code','max_buy_qty',
        'min_buy_qty','free_item_qty','free_item_price','is_zonal','zone_list','lfcl_id'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $slgp_id=$this->getSalesGroupId($request->slgp_code);
        $buy_item=$this->getAmimId($request->buy_item_code);
        $buy_item_id=$buy_item->id;
        $free_item=$this->getAmimId($request->free_item_code);
        $free_item_id=$free_item->id;
        $free_item_price=$free_item->amim_tppr;
        if($request->free_item_price){
            $free_item_price=$request->free_item_price;
        }
        DB::connection($this->db)->beginTransaction();
        try{
            $exist_prom=Promotion::on($this->db)->where(['prom_code'=>$request->prom_code,'slgp_id'=>$slgp_id])->first();
            if($exist_prom){
                $exist_prom->lfcl_id=$request->lfcl_id;
                $exist_prom->prom_edat=$request->end_date;
                $exist_prom->save();
            }
            else{
                $promotion=new Promotion();
                $promotionDetails=new PromotionDetails();
                $promotion->setConnection($this->db);
                $promotion = new Promotion();
                $promotion->prom_name = $request->prom_name;
                $promotion->prom_code = $request->prom_code;
                $promotion->prom_sdat = $request->start_date;
                $promotion->prom_edat = $request->end_date;
                $promotion->prom_type = '0';
                $promotion->slgp_id =$slgp_id;
                $promotion->prom_nztp =$request->is_zonal;
                $promotion->cont_id = $this->currentUser->country()->id;
                $promotion->lfcl_id =$request->lfcl_id;
                $promotion->aemp_iusr = $this->currentUser->employee()->id;
                $promotion->aemp_eusr = $this->currentUser->employee()->id;
                $promotion->var = 0;
                $promotion->attr1 = '';
                $promotion->attr2 = '';
                $promotion->attr3 = 0;
                $promotion->attr4 = 0;
                $promotion->save();
                $lid = $promotion->id;
                $promotionDetails->setConnection($this->db);
                $promotionDetails->prom_id = $lid;
                $promotionDetails->prdt_sitm =$buy_item_id;
                $promotionDetails->prdt_fitm =$free_item_id;
                $promotionDetails->prdt_mbqt =$request->max_buy_qty;
                $promotionDetails->prdt_mnbt =$request->min_buy_qty;                
                $promotionDetails->prdt_fiqt =$request->free_item_qty;
                $promotionDetails->prdt_fipr = $free_item_price;
                $promotionDetails->prdt_disc =round(($request->min_buy_qty*100/$request->max_buy_qty),2);
                $promotionDetails->prdt_disa = '0';
                $promotionDetails->cont_id = $this->currentUser->country()->id;
                $promotionDetails->lfcl_id = 1;
                $promotionDetails->aemp_iusr = $this->currentUser->employee()->id;
                $promotionDetails->aemp_eusr = $this->currentUser->employee()->id;
                $promotionDetails->var = 0;
                $promotionDetails->attr1 = '';
                $promotionDetails->attr2 = '';
                $promotionDetails->attr3 = 0;
                $promotionDetails->attr4 = 0;
                $promotionDetails->save();
                if($request->is_zonal==1 && $request->zone_list !=''){
                    $zones=DB::connection($this->db)->select("SELECT id FROM tm_zone WHERE zone_code in ('$request->zone_list)");
                    foreach ($zones as $key => $row) {
                        $insert[] = ['prom_id' => $lid, 'zone_id' => $row->id, 'cont_id' => $this->currentUser->country()->id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id, 'var' => 1, 'attr1' => '', 'attr2' => '', 'attr3' => 0, 'attr4' => 0];
                    }
                    if (!empty($insert)) {
                        DB::connection($this->db)->table('tt_pznt')->insert($insert);
                    }
                }
            }
            

        
            DB::connection($this->db)->commit();
        }
        catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }
        
      }
      
    }
    public function getAmimId($amim_code){
        return SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
          
    }
    public function getSalesGroupId($slgp_code){
        $slgp=SalesGroup::on($this->db)->where(['slgp_code'=>$slgp_code])->first();
        return $slgp->id;
    }
    public function getItsgCode($id){
        $sku=SubCategory::on($this->db)->where(['id'=>$id])->first();
        return $sku->itsg_code;     
    }
    public function getSalesCatId($code){
        $cat=DB::connection($this->db)->select("Select id from tm_issc where issc_code='$code' limit 1");
        return $cat?$cat[0]->id:'';     
    }

}