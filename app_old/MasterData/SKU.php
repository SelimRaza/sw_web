<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;

class SKU extends Model implements FromCollection, WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_amim';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function collection()
    {
        return collect([
            [
                'name' => 'Test1',
                'code' => 's12345',
                'bangla' => '',
                'sub_category_id' => '1',
                'ctn_size' => '1',
                'sku_size' => '1',
            ],

        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
            'bangla',
            'sub_category_id',
            'ctn_size',
            'sku_size',
        ];
    }

    public function model(array $row)
    {
        $sku = new SKU();
        $sku->setConnection($this->connection);
        $sku->name = $row['name'];
        $sku->code = $row['code'];
        $sku->ln_name = $row['bangla'];
        $sku->sub_category_id = $row['sub_category_id'];
        $sku->ctn_size = $row['ctn_size'];
        $sku->sku_size = $row['sku_size'];
        $sku->status_id = 1;
        $sku->country_id = $this->currentUser->employee()->country_id;
        $sku->created_by = $this->currentUser->employee()->id;
        $sku->updated_by = $this->currentUser->employee()->id;
        $sku->image = '';
        $sku->image_icon = '';
        $sku->save();
    }
    public function itemClasss()
    {
        return ItemClass::on($this->connection)->find($this->itcl_id);
    }

    public function subCategory()
    {
        return SubCategory::on($this->connection)->find($this->itsg_id);
    }

}
