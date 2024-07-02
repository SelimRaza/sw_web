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
class MQTY  extends Model implements WithHeadings, ToArray, WithHeadingRow
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
        return ['item_code','min_qty'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $amim_code=$request->item_code;
        $sku=$this->getAmimId($amim_code);
        $amim_id=$sku?$sku->id:0;
        $min_qty=$request->min_qty;
        $sku_exist=SalesGroupSku::on($this->db)->where(['amim_id'=>$amim_id])->get();
        if($sku_exist){
            foreach($sku_exist as $result){
                $result->attr4 = $min_qty;
                $result->aemp_eusr=Auth::user()->employee()->id;
                $result->save();
            }
        }
      }
      
    }
    public function getAmimId($amim_code){
        return SKU::on($this->db)->where(['amim_code'=>$amim_code])->first();
          
    }

}