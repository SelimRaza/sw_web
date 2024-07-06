<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'tm_cont';
    private $array = array();
    private $db_array = array();

  public function country($id)
    {
        array_push($this->array, (object)array('id' => 1, 'cont_name' => 'CS', 'memo_title' => 'CS company ltd','memo_sub_title' => 'Thanks for staying with us','cont_code' => 'C0001', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'common','cont_con2' => 'pran_live', 'cont_imgf' => 'bdp', 'cont_dgit' => '4','cont_ogdt' => '5', 'cont_cncy' => 'BDT','cncy_sym' => "\u09F3", 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));//1=bd,2=uae
        array_push($this->array, (object)array('id' => 2, 'cont_name' => 'Module1', 'memo_title' => 'Invoice','memo_sub_title' => 'Thanks for staying with us','cont_code' => 'C0002', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'mdule1','cont_con2' => 'mdule1', 'cont_imgf' => 'bds', 'cont_dgit' => '2','cont_ogdt' => '99999', 'cont_cncy' => 'BDT','cncy_sym' => '৳', 'cont_rund' => '4', 'GCKCD' =>90,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0,'min_oamt' =>0,'vat_exc' =>0,'site_renm' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 3, 'cont_name' => 'Module2', 'memo_title' => 'Order Memo(For Order Purpose Only)','memo_sub_title' => '','cont_code' => 'C0003', 'cont_tzon' => 'asia/dubai', 'cont_conn' => 'mdule2','cont_con2' => 'mdule2', 'cont_imgf' => 'uae','cont_dgit' => '2','cont_ogdt' => '250000', 'cont_cncy' => 'AED','cncy_sym' => '', 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 15,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>1,'vat_exc' =>0,'site_renm' =>0,'module_type' => 2));
        
	   // array_push($this->array, (object)array('id' => 6, 'cont_name' => 'oracle', 'cont_code' => 'C0006', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'oracle','cont_con2' => '', 'cont_imgf' => 'oracle', 'cont_dgit' => '4', 'cont_cncy' => 'BDT', 'cont_rund' => '2'));
        foreach ($this->array as $element) {
            if ($id == $element->id) {
                return $element;
            }
        }
        return false;

    }
   public function country1($id)
    {
        array_push($this->array, (object)array('id' => 1, 'cont_name' => 'PRAN', 'cont_code' => 'C0001', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'myprg_p','cont_con2' => 'pran_live', 'cont_imgf' => 'bdp', 'cont_dgit' => '4', 'cont_cncy' => 'BDT', 'cont_rund' => '2'));
        array_push($this->array, (object)array('id' => 2, 'cont_name' => 'RFL', 'cont_code' => 'C0002', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'myprg_r','cont_con2' => '', 'cont_imgf' => 'bdr',  'cont_dgit' => '4','cont_cncy' => 'BDT', 'cont_rund' => '2'));
        array_push($this->array, (object)array('id' => 3, 'cont_name' => 'UAE', 'cont_code' => 'C0003', 'cont_tzon' => 'asia/dubai', 'cont_conn' => 'mydb_uae','cont_con2' => 'uae_live', 'cont_imgf' => 'uae','cont_dgit' => '4', 'cont_cncy' => 'AED', 'cont_rund' => '2'));
        array_push($this->array, (object)array('id' => 4, 'cont_name' => 'Qatar', 'cont_code' => 'C0004', 'cont_tzon' => 'Asia/Qatar', 'cont_conn' => 'mydb_qat1','cont_con2' => '', 'cont_imgf' => 'qat', 'cont_dgit' => '40','cont_cncy' => 'QAT', 'cont_rund' => '3'));

        foreach ($this->array as $element) {
            if ($id == $element->id) {
                return $element;
            }
        }
        return false;
    }

    public function currentUserDB($id){
        array_push($this->db_array, (object)array('id' => 1,'db_name'=>'myprg_comm'));
        array_push($this->db_array, (object)array('id' => 2,'db_name'=>'mdule1'));
        array_push($this->db_array, (object)array('id' => 3,'db_name'=>'mdule2'));
        foreach ($this->db_array as $element) {
            if ($id == $element->id) {
                return $element;
            }
        }
        return false;
    }

}
