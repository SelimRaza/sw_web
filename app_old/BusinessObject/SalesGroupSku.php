<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/25/2018
 * Time: 7:22 PM
 */

namespace App\BusinessObject;


use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesGroupSku extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_sgit';
    private $currentUser;
    protected $connection= '';
    public function __construct()
    {
        if (Auth::user()){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['group_id', 'group_name','sales_category_id', 'sales_category_name', 'sku_code', 'sku_name', 'min_qty'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $sku = SKU::on($this->connection)->where(['amim_code' => $value->sku_code])->first();
        if ($sku!=null){
            $salesGroupSKU1 = SalesGroupSku::on($this->connection)->where(['slgp_id' => $value->group_id, 'amim_id' => $sku->id])->first();
            if ($salesGroupSKU1 == null) {
                $salesGroupSKU = new SalesGroupSku();
                $salesGroupSKU->setConnection($this->connection);
                $salesGroupSKU->amim_id = $sku->id;
                $salesGroupSKU->issc_id = $value->sales_category_id;
                $salesGroupSKU->slgp_id = $value->group_id;
                $salesGroupSKU->sgit_moqt = $value->min_qty;
                $salesGroupSKU->cont_id = $this->currentUser->employee()->cont_id;
                $salesGroupSKU->lfcl_id = 1;
                $salesGroupSKU->aemp_iusr = $this->currentUser->employee()->id;
                $salesGroupSKU->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupSKU->var = 1;
                $salesGroupSKU->attr1 = '';
                $salesGroupSKU->attr2 = '';
                $salesGroupSKU->attr3 = 0;
                $salesGroupSKU->attr4 = 0;
                $salesGroupSKU->save();
            }else{
                $salesGroupSKU1->issc_id = $value->sales_category_id;
                $salesGroupSKU1->sgit_moqt = $value->min_qty;
                $salesGroupSKU1->cont_id = $this->currentUser->cont_id;
                $salesGroupSKU1->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupSKU1->save();
            }
        }
    }
    public function sku()
    {
        return SKU::on($this->connection)->find($this->amim_id);
    }
}