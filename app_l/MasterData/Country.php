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
        array_push($this->array, (object)array('id' => 1, 'cont_name' => 'CS', 'memo_title' => 'CS company ltd','memo_sub_title' => 'Thanks for staying with us','cont_code' => 'C0001', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'myprg_p','cont_con2' => 'pran_live', 'cont_imgf' => 'bdp', 'cont_dgit' => '4','cont_ogdt' => '5', 'cont_cncy' => 'BDT','cncy_sym' => "\u09F3", 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));//1=bd,2=uae
        array_push($this->array, (object)array('id' => 2, 'cont_name' => 'Saleswheel', 'memo_title' => 'Invoice','memo_sub_title' => 'Thanks for staying with us','cont_code' => 'C0002', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'bsolutio_mdule1','cont_con2' => '', 'cont_imgf' => 'bdp', 'cont_dgit' => '2','cont_ogdt' => '99999', 'cont_cncy' => 'BDT','cncy_sym' => '৳', 'cont_rund' => '4', 'GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 3, 'cont_name' => 'UAE', 'memo_title' => 'Order Memo(For Order Purpose Only)','memo_sub_title' => '','cont_code' => 'C0003', 'cont_tzon' => 'asia/dubai', 'cont_conn' => 'mydb_uae','cont_con2' => 'uae_live', 'cont_imgf' => 'uae', 'cont_dgit' => '2','cont_ogdt' => '99999', 'cont_cncy' => 'AED','cncy_sym' => '', 'cont_rund' => '2','GCKCD' =>90,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 1, 'min_oamt' =>30,'module_type' => 2));
        array_push($this->array, (object)array('id' => 4, 'cont_name' => 'Qatar','memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0004', 'cont_tzon' => 'Asia/Qatar', 'cont_conn' => 'myprg_qat','cont_con2' => '', 'cont_imgf' => 'qat', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'QAT', 'cncy_sym' => '','cont_rund' => '4','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));//RTBO=1 block, 0 open
        array_push($this->array, (object)array('id' => 5, 'cont_name' => 'RFL','memo_title' => 'Invoice','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0005', 'cont_tzon' => 'asia/dhaka', 'cont_conn' => 'myprg_rfl','cont_con2' => 'rfl_live', 'cont_imgf' => 'rfl', 'cont_dgit' => '2', 'cont_ogdt' => '9999','cont_cncy' => 'BDT','cncy_sym' => '৳', 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 1,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 6, 'cont_name' => 'Singapore','memo_title' => 'TAX INVOICE(GLOBAL PIONEER PTE LTD)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0006', 'cont_tzon' => 'Asia/Singapore', 'cont_conn' => 'myprg_sgp','cont_con2' => 'sgp_live', 'cont_imgf' => 'sgp', 'cont_dgit' => '2', 'cont_ogdt' => '9999','cont_cncy' => 'SGD','cncy_sym' => 'S$', 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>1, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 7, 'cont_name' => 'Bahrain','memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0007', 'cont_tzon' => 'Asia/Bahrain', 'cont_conn' => 'myprg_bhr','cont_con2' => 'bhr_live', 'cont_imgf' => 'bhr', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'BHD', 'cncy_sym' => '','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 8, 'cont_name' => 'Nepal', 'memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us','cont_code' => 'C0008', 'cont_tzon' => 'Asia/Kathmandu', 'cont_conn' => 'myprg_nep','cont_con2' => 'nep_live', 'cont_imgf' => 'nep', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'NEP','cncy_sym' => '', 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 9, 'cont_name' => 'India', 'memo_title' => 'Memo(PRAN India Ltd)','memo_sub_title' => 'It`s a Secondary Order Form','cont_code' => 'C0009', 'cont_tzon' => 'Asia/Kolkata', 'cont_conn' => 'myprg_ind','cont_con2' => 'ind_live', 'cont_imgf' => 'ind', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'INR','cncy_sym' => '₹', 'cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 10, 'cont_name' => 'India_RFL','memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0010', 'cont_tzon' => 'Asia/Kolkata', 'cont_conn' => 'myprg_ind_rfl','cont_con2' => 'ind_rfl_live', 'cont_imgf' => 'ind_rfl', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'INR', 'cncy_sym' => '₹','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 11, 'cont_name' => 'Philippine','memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0011', 'cont_tzon' => 'Asia/Manila', 'cont_conn' => 'myprg_phi','cont_con2' => 'phi_live', 'cont_imgf' => 'phi', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'PHP', 'cncy_sym' => '₱','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 12, 'cont_name' => 'Somaliland','memo_title' => 'Memo(Somaliland company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0012', 'cont_tzon' => 'Africa/Mogadishu', 'cont_conn' => 'myprg_som','cont_con2' => 'som_live', 'cont_imgf' => 'som', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'SOS', 'cncy_sym' => 'Sh','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 13, 'cont_name' => 'Ghana','memo_title' => 'Memo(PRAN company ltd)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0013', 'cont_tzon' => 'Africa/Accra', 'cont_conn' => 'myprg_gna','cont_con2' => 'gna_live', 'cont_imgf' => 'gna', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'GHS', 'cncy_sym' => 'GH₵','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 14, 'cont_name' => 'Malaysia','memo_title' => 'PINNACLE FOODS (M) SDN BHD','memo_sub_title' => 'PT 9892(2739) OFF JALAN BARU,KG.BARU SG.BULOH,SEKSYEN U4,40160 SHAH ALAM,SELANGOR DARUL EHSAN. D.E MALAYSIA Tel:03-61573561 Fax:03-61572621', 'cont_code' => 'C0014', 'cont_tzon' => 'Asia/Kuala_Lumpur', 'cont_conn' => 'myprg_mal','cont_con2' => 'mal_live', 'cont_imgf' => 'mal', 'cont_dgit' => '2', 'cont_ogdt' => '99999','cont_cncy' => 'RM', 'cncy_sym' => 'RM','cont_rund' => '2','GCKCD' =>30,'GTCCD' =>15,'GTCRD' =>7, 'RTBO' => 1,'min_oamt' =>48,'module_type' => 2));
        array_push($this->array, (object)array('id' => 15, 'cont_name' => 'KSA','memo_title' => 'Memo(TAKHYYR FOODS TRADING COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0015', 'cont_tzon' => 'Asia/Riyadh', 'cont_conn' => 'myprg_ksa','cont_con2' => 'ksa_live', 'cont_imgf' => 'ksa', 'cont_dgit' => '4', 'cont_ogdt' => '99999','cont_cncy' => 'SR', 'cncy_sym' => 'SR','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 1, 'min_oamt' =>0,'module_type' => 2));
        array_push($this->array, (object)array('id' => 16, 'cont_name' => 'Italy','memo_title' => 'Memo(4 S TRADING SRLS)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0016', 'cont_tzon' => 'Europe/Rome', 'cont_conn' => 'mydb_ita','cont_con2' => 'ita_live', 'cont_imgf' => 'ita', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'EUR', 'cncy_sym' => '€','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 17, 'cont_name' => 'Kuwait','memo_title' => 'Memo(Modern foods)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0017', 'cont_tzon' => 'Asia/Kuwait', 'cont_conn' => 'mydb_kuw','cont_con2' => 'kuw_live', 'cont_imgf' => 'kuw', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'KWD', 'cncy_sym' => ' KD','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 1,'min_oamt' =>0,'module_type' => 2));
        array_push($this->array, (object)array('id' => 18, 'cont_name' => 'CANADA','memo_title' => 'Memo(TAKHYYR FOODS TRADING COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0018', 'cont_tzon' => 'America/Toronto', 'cont_conn' => 'mydb_cnd','cont_con2' => 'cnd_live', 'cont_imgf' => 'cnd', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'CAD', 'cncy_sym' => ' C$','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 19, 'cont_name' => 'Palestine','memo_title' => 'Memo(TAKHYYR FOODS TRADING COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0019', 'cont_tzon' => 'Asia/Gaza', 'cont_conn' => 'mydb_pal','cont_con2' => 'pal_live', 'cont_imgf' => 'pal', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'PP', 'cncy_sym' => ' £P','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 20, 'cont_name' => 'Brunei','memo_title' => 'Memo(TAKHYYR FOODS TRADING COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0020', 'cont_tzon' => 'Asia/Brunei', 'cont_conn' => 'mydb_brunei','cont_con2' => 'brunei_live', 'cont_imgf' => 'brunei', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'BND', 'cncy_sym' => ' B$','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 21, 'cont_name' => 'Sierra Leone','memo_title' => 'Memo(TAKHYYR FOODS TRADING COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0021', 'cont_tzon' => 'Africa/Freetown', 'cont_conn' => 'mydb_sierra','cont_con2' => 'sierra_live', 'cont_imgf' => 'sierra', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'SLE', 'cncy_sym' => ' Le','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 22, 'cont_name' => 'Salalah','memo_title' => 'Order Memo(For Order Purpose Only)','memo_sub_title' => '', 'cont_code' => 'C0022', 'cont_tzon' => 'Asia/Muscat', 'cont_conn' => 'mydb_salalah','cont_con2' => 'salalah_live', 'cont_imgf' => 'salalah', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'OMR', 'cncy_sym' => ' OR','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 23, 'cont_name' => 'Muscat','memo_title' => 'Order Memo(For Order Purpose Only)','memo_sub_title' => '', 'cont_code' => 'C0023', 'cont_tzon' => 'Asia/Muscat', 'cont_conn' => 'mydb_muscat','cont_con2' => 'muscat_live', 'cont_imgf' => 'muscat', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'OMR', 'cncy_sym' => ' OR','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 1,'min_oamt' =>0, 'module_type' => 2));
        array_push($this->array, (object)array('id' => 24, 'cont_name' => 'USA','memo_title' => 'Memo(COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0024', 'cont_tzon' => 'America/New_York', 'cont_conn' => 'mydb_usa','cont_con2' => 'usa_live', 'cont_imgf' => 'usa', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'USD', 'cncy_sym' => ' $','cont_rund' => '2','GCKCD' =>30, 'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0,'min_oamt' =>1, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 25, 'cont_name' => 'UK','memo_title' => 'Memo(COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0025', 'cont_tzon' => 'Europe/London', 'cont_conn' => 'mydb_uk','cont_con2' => 'uk_live', 'cont_imgf' => 'uk', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'GBP', 'cncy_sym' => ' £','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2, 'RTBO' => 0,'min_oamt' =>0, 'module_type' => 1));
        array_push($this->array, (object)array('id' => 26, 'cont_name' => 'Test_P','memo_title' => 'Memo(COMPANY)','memo_sub_title' => 'Thanks for staying with us', 'cont_code' => 'C0026', 'cont_tzon' => 'Asia/Dhaka', 'cont_conn' => 'mydb_tp','cont_con2' => 'tp_live', 'cont_imgf' => 'tp', 'cont_dgit' => '4', 'cont_ogdt' => '9999','cont_cncy' => 'BDT', 'cncy_sym' => ' ৳','cont_rund' => '2','GCKCD' =>30,'GTCCD' => 7,'GTCRD' => 2,'RTBO' => 0, 'min_oamt' =>0, 'module_type' => 1));
       
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
        array_push($this->db_array, (object)array('id' => 2,'db_name'=>'myprg_pran'));
        array_push($this->db_array, (object)array('id' => 3,'db_name'=>'mydb_uae'));
        array_push($this->db_array, (object)array('id' => 4,'db_name'=>'mydb_qtr'));
        array_push($this->db_array, (object)array('id' => 5,'db_name'=>'myprg_rfl'));
        array_push($this->db_array, (object)array('id' => 6,'db_name'=>'mydb_sgp'));
        array_push($this->db_array, (object)array('id' => 7,'db_name'=>'mydb_bhr'));
        array_push($this->db_array, (object)array('id' => 8,'db_name'=>'mydb_nep'));
        array_push($this->db_array, (object)array('id' => 9,'db_name'=>'mydb_ind'));
        array_push($this->db_array, (object)array('id' => 10,'db_name'=>'mydb_indrfl'));
        array_push($this->db_array, (object)array('id' => 11,'db_name'=>'mydb_phi'));
        array_push($this->db_array, (object)array('id' => 12,'db_name'=>'mydb_som'));
        array_push($this->db_array, (object)array('id' => 13,'db_name'=>'mydb_gna'));
        array_push($this->db_array, (object)array('id' => 14,'db_name'=>'mydb_mal'));
        array_push($this->db_array, (object)array('id' => 15,'db_name'=>'mydb_ksa'));
		array_push($this->db_array, (object)array('id' => 16,'db_name'=>'mydb_ita'));
        array_push($this->db_array, (object)array('id' => 17,'db_name'=>'mydb_kuw'));
        array_push($this->db_array, (object)array('id' => 18,'db_name'=>'mydb_cnd'));
        array_push($this->db_array, (object)array('id' => 19,'db_name'=>'mydb_pal'));
        array_push($this->db_array, (object)array('id' => 20,'db_name'=>'mydb_brunei'));
        array_push($this->db_array, (object)array('id' => 21,'db_name'=>'mydb_sierra'));
        array_push($this->db_array, (object)array('id' => 22,'db_name'=>'mydb_salalah'));
        array_push($this->db_array, (object)array('id' => 23,'db_name'=>'mydb_muscat'));
        array_push($this->db_array, (object)array('id' => 24,'db_name'=>'mydb_usa'));
        array_push($this->db_array, (object)array('id' => 25,'db_name'=>'mydb_uk'));
        array_push($this->db_array, (object)array('id' => 26,'db_name'=>'mydb_tp'));
        foreach ($this->db_array as $element) {
            if ($id == $element->id) {
                return $element;
            }
        }
        return false;
    }

}
