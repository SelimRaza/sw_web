<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ProductClass extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_itcl';
    private $currentUser;
    protected $connection = '';
    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['class_name', 'class_code', 'product_group_id'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $productClass = new ProductClass();
        $productClass->setConnection($this->connection);
        $productClass->itgp_id = $value->product_group_id;
        $productClass->itcl_name = $value->class_name;
        $productClass->itcl_code = $value->class_code;
        $productClass->lfcl_id = 1;
        $productClass->cont_id = $this->currentUser->employee()->cont_id;
        $productClass->aemp_iusr = $this->currentUser->employee()->id;
        $productClass->aemp_eusr = $this->currentUser->employee()->id;
        $productClass->save();

    }

    public function itemGroup()
    {
        return ProductGroup::on($this->connection)->find($this->itgp_id);
    }



}
