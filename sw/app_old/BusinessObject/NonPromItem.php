<?php

namespace App\BusinessObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Employee;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\CashPartyCreditBudgetLine;
class NonPromItem  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_npit';
    private $currentUser;
    protected $db = '';
    protected $con_id='';
    protected $empId='';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->empId = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return ['item_code', 'group_id','type'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $amim_code=$request->item_code;
        $amim_id=$this->getAmimId($amim_code);
        $slgp_id=$request->group_id;
        $type=$request->type;
        $npit=NonPromItem::on($this->db)->where(['amim_id'=>$amim_id,'slgp_id'=>$slgp_id])->first();
        if(!$npit && $type==1){
          $npit=new NonPromItem();
          $npit->setConnection($this->db);
          $npit->amim_id=$amim_id;
          $npit->slgp_id=$slgp_id;
          $npit->cont_id=$this->cont_id;
          $npit->lfcl_id=1;
          $npit->aemp_iusr=$this->empId;
          $npit->aemp_eusr=$this->empId;
          $npit->save();
        }
        else if($npit !='' && $type==2){
          $npit->delete();
        }
      }
      
    }
    public function getAmimId($amim_code){
      $data=ItemMaster::on($this->db)->where(['amim_code'=>$amim_code])->first();
      return $data->id;
    }

    

}