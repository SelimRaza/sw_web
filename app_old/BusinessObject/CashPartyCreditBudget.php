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
class CashPartyCreditBudget  extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_scbm';
    private $currentUser;
    protected $db = '';
    protected $con_id='';
    protected $empId='';
    protected $fillable =[
        'aemp_id','spbm_mnth','spbm_year','spbm_limt','spbm_avil','spbm_amnt','cont_id','lfcl_id','aemp_iusr','aemp_eusr'
    ];


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
        return ['staff_id', 'month','year','limit','type'];
    }

    public function array(array $array)
    {
      foreach($array as $row){
        $request=(object)$row;
        $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->staff_id])->first();
        $mnth=$request->month;
        $year=$request->year;
        $type=$request->type;
        //dd($employee->id);
        if ($employee) {
            $cashPartyCreditBudget = CashPartyCreditBudget::on($this->db)->where(['aemp_id' => $employee->id, 'spbm_mnth' => $mnth, 'spbm_year' => $year])->first();
            if ($cashPartyCreditBudget== null) {
                if($request->type==1){
                    $cashPartyCreditBudget = new CashPartyCreditBudget();
                    $cashPartyCreditBudget->setConnection($this->db);                  
                    $cashPartyCreditBudget->aemp_id=$employee->id;
                    $cashPartyCreditBudget->spbm_mnth = $mnth;
                    $cashPartyCreditBudget->spbm_year = $year;
                    $cashPartyCreditBudget->spbm_limt =0;
                    $cashPartyCreditBudget->spbm_avil = 0;
                    $cashPartyCreditBudget->spbm_amnt =0;
                    $cashPartyCreditBudget->lfcl_id = 1;
                    $cashPartyCreditBudget->cont_id = $this->cont_id;
                    $cashPartyCreditBudget->aemp_iusr = $this->empId;
                    $cashPartyCreditBudget->aemp_eusr = $this->empId;
                    //dd($cashPartyCreditBudget) ;               
                    $cashPartyCreditBudget->save();
                    $line=new CashPartyCreditBudgetLine();
                    $line->setConnection($this->db);
                    $line->spbm_id=$cashPartyCreditBudget->id;
                    $line->ordm_ornm=0;
                    $line->trnt_id=1;
                    $cashPartyCreditBudget->spbm_limt=$request->limit;
                    $cashPartyCreditBudget->spbm_amnt=$request->limit;
                    $line->scbd_type="In";
                    $line->scbd_amnt=$request->limit;
                    $line->cont_id=$this->con_id;
                    $line->aemp_iusr=$this->empId;
                    $line->aemp_eusr=$this->empId;
                    $line->lfcl_id=1;
                    $cashPartyCreditBudget->save();
                    $line->save();
                }

            } 
            else {
                $line=new CashPartyCreditBudgetLine();
                $line->setConnection($this->db);
                $line->spbm_id=$cashPartyCreditBudget->id;
                $line->ordm_ornm=0;
                if($request->type==1){
                    $line->trnt_id=1;
                    $cashPartyCreditBudget->spbm_limt=$cashPartyCreditBudget->spbm_limt+$request->limit;
                    $cashPartyCreditBudget->spbm_amnt=$cashPartyCreditBudget->spbm_amnt+$request->limit;
                    $line->scbd_type="In";
                }else{
                    $line->trnt_id=2;
                    if($cashPartyCreditBudget->spbm_amnt>=$request->limit){
                        $cashPartyCreditBudget->spbm_limt=$cashPartyCreditBudget->spbm_limt-$request->limit;
                        $cashPartyCreditBudget->spbm_amnt=$cashPartyCreditBudget->spbm_amnt-$request->limit;
                        $line->scbd_type="Out";
                    }
                }
                $line->scbd_amnt=$request->limit;
                $line->cont_id=$this->cont_id;
                $line->aemp_iusr=$this->empId;
                $line->aemp_eusr=$this->empId;
                $line->lfcl_id=1;
                $line->save();
                $cashPartyCreditBudget->save();
            }
        }
      }
      
    }
    

}