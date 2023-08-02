<?php

namespace App\BusinessObject;

use App\MasterData\SKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PriceListDetails extends Model implements FromCollection, WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_pldt';
    private $currentUser;
    private $price_list_id = 0;
    protected $connection= '';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }
    public static function create($id)
    {
        $instance = new self();
        // dd($route_id);
        $instance->price_list_id = $id;
        return $instance;
    }
    public function collection()
    {
        $routeArray =  $routeArray[] = [$this->price_list_id];
        return collect([
            $routeArray
        ]);
    }

    public function headings(): array
    {
        return ['price_list_id', 'sku_code','sku_short_name', 'ctn_dealer_price','ctn_dealer_grv_price', 'ctn_sales_price','ctn_sales_grv_price', 'ctn_market_price'];
    }

    public function model(array $row)
    {
        $request = (object)$row;
        $sku = SKU::on($this->connection)->where(['amim_code' => $request->sku_code])->first();
        $duft=$sku?$sku->amim_duft:1;
        if ($sku!=null){
            $priceListDetails = PriceListDetails::on($this->connection)->where(['plmt_id' => $request->price_list_id, 'amim_id' => $sku->id])->first();
            if ($priceListDetails == null) {
                $priceListDetails = new PriceListDetails();
                $priceListDetails->setConnection($this->connection);
                $priceListDetails->amim_id = $sku->id;
                $priceListDetails->amim_code = $sku->amim_code;
                $priceListDetails->plmt_id = $request->price_list_id;
                $priceListDetails->pldt_dppr = $request->ctn_dealer_price/$duft;
                $priceListDetails->pldt_dgpr = $request->ctn_dealer_grv_price/$duft;
                $priceListDetails->pldt_tppr = $request->ctn_sales_price/$duft;
                $priceListDetails->pldt_tpgp = $request->ctn_sales_grv_price/$duft;
                $priceListDetails->pldt_mrpp = $request->ctn_market_price/$duft;
                $priceListDetails->pldt_snme = $request->sku_short_name;
                $priceListDetails->amim_duft = $duft;
                $priceListDetails->amim_dunt = $sku->amim_dunt;
                $priceListDetails->amim_runt = $sku->amim_runt;
                $priceListDetails->cont_id = $this->currentUser->employee()->cont_id;
                $priceListDetails->lfcl_id = 1;
                $priceListDetails->aemp_iusr = $this->currentUser->employee()->id;
                $priceListDetails->aemp_eusr = $this->currentUser->employee()->id;
                $priceListDetails->var = 1;
                $priceListDetails->attr1 = '';
                $priceListDetails->attr2 = '';
                $priceListDetails->attr3 = 0;
                $priceListDetails->attr4 = 0;
                $priceListDetails->save();
            } else {
                $priceListDetails->pldt_dppr = $request->ctn_dealer_price/$duft;
                $priceListDetails->pldt_dgpr = $request->ctn_dealer_grv_price/$duft;
                $priceListDetails->pldt_tppr = $request->ctn_sales_price/$duft;
                $priceListDetails->pldt_tpgp = $request->ctn_sales_grv_price/$duft;
                $priceListDetails->pldt_mrpp = $request->ctn_market_price/$duft;
                $priceListDetails->pldt_snme = $request->sku_short_name;
                $priceListDetails->amim_duft = $duft;
                $priceListDetails->amim_code = $sku->amim_code;
                $priceListDetails->save();
            }
        }


    }

    public function sku()
    {
        return SKU::on($this->connection)->find($this->amim_id);
    }
}
