<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductGroup extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_itgp';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['product_group_name', 'product_group_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;


        $productGroup = new ProductGroup();
        $productGroup->setConnection($this->connection);
        $productGroup->itgp_name = $value->product_group_name;
        $productGroup->itgp_code = $value->product_group_code;
        $productGroup->lfcl_id = 1;
        $productGroup->cont_id = $this->currentUser->employee()->cont_id;
        $productGroup->aemp_iusr = $this->currentUser->employee()->id;
        $productGroup->aemp_eusr = $this->currentUser->employee()->id;
        $productGroup->var = 1;
        $productGroup->attr1 = '';
        $productGroup->attr2 = '';
        $productGroup->attr3 = 0;
        $productGroup->attr4 = 0;
        $productGroup->save();

    }


}
