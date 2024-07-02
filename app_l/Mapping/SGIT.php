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
Use App\MasterData\SubCategory;
use App\BusinessObject\SalesGroupSku;
class SGIT  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_sgit';
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
        return ['slgp_id', 'item_code','sales_cat_id'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $slgp_id=$request->slgp_id;
        $amim_code=$request->item_code;
        $s_cat=$request->sales_cat_id;
        $sku=$this->getAmimId($amim_code);
        $amim_id=$sku->id;
        //$issc_code=$s_cat?$s_cat:$this->getItsgCode($sku->itsg_id);
        //$s_cat_id=$this->getSalesCatId($issc_code);
        $sgit=SalesGroupSku::on($this->db)->where(['slgp_id'=>$slgp_id,'amim_id'=>$amim_id])->first();
        if(!$sgit){
            $sgit=new SalesGroupSku();
            $sgit->setConnection($this->db);
            $sgit->slgp_id=$slgp_id;
            $sgit->slgp_code='';
            $sgit->amim_id=$amim_id;
            $sgit->item_code=$amim_code;
            $sgit->category_name='';
            $sgit->issc_code='-';
            $sgit->issc_id=$s_cat;
            $sgit->sgit_moqt=1;
            $sgit->cont_id=$this->cont_id;
            $sgit->aemp_iusr=$this->aemp_id;
            $sgit->aemp_eusr=$this->aemp_id;
            $sgit->lfcl_id=1;
            $sgit->attr4=1;
            $sgit->save();
        }
        else{
            $sgit->issc_id=$s_cat;
            $sgit->save();
        }
      }
      
    }
    public function getAmimId($amim_code){
        return SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
          
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