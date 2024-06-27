<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 11/16/2019
 * Time: 2:27 PM
 */

namespace App\BusinessObject;

use App\MasterData\Depot;
use App\MasterData\Employee;
use App\MasterData\LifeCycleStatus;
use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MRRMaster extends Model implements WithHeadings, WithHeadingRow, ToArray
{
    protected $table = 'tt_mrrm';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = $this->currentUser->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['mrr_code', 'depot_id', 'sku_code', 'sku_name', 'qty'];
    }

    public function array(array $array)
    {
        $mrrMaster = new MRRMaster();
        $mrrMaster->setConnection($this->connection);
        $mrrMaster->mrrm_code = $array[0]['mrr_code'];
        $mrrMaster->dlrm_id = $array[0]['depot_id'];
        $mrrMaster->mrrm_date = date('Y-m-d');
        $mrrMaster->lfcl_id = 1;
        $mrrMaster->cont_id = $this->currentUser->employee()->cont_id;
        $mrrMaster->aemp_vusr = $this->currentUser->employee()->id;
        $mrrMaster->aemp_iusr = $this->currentUser->employee()->id;
        $mrrMaster->aemp_eusr = $this->currentUser->employee()->id;
        $mrrMaster->save();
        foreach ($array as $key => $row) {
            $value = (object)$row;
            $sku1 = SKU::on($this->connection)->where(['amim_code' => $value->sku_code])->first();
            $insert[] = ['mrrm_id' => $mrrMaster->id, 'amim_id' => $sku1->id, 'mrrl_qnty' => $value->qty, 'cont_id' => $this->currentUser->employee()->cont_id, 'lfcl_id' => 1, 'aemp_iusr' => $this->currentUser->employee()->id, 'aemp_eusr' => $this->currentUser->employee()->id];
        }
        if (!empty($insert)) {
            DB::connection($this->connection)->table('tt_mrrl')->insert($insert);
        }

    }

    public function depot()
    {
        return Depot::on($this->connection)->find($this->dlrm_id);
    }

    public function employee()
    {
        return Employee::on($this->connection)->find($this->aemp_vusr);
    }

    public function employeeCreated()
    {
        return Employee::on($this->connection)->find($this->aemp_iusr);
    }

    public function status()
    {
        return LifeCycleStatus::on($this->connection)->find($this->lfcl_id);
    }


}