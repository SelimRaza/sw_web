<?php

namespace App\MasterData;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Category extends Model implements WithHeadings, ToModel, WithHeadingRow
{


    protected $table = 'tm_itcg';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['category_name', 'category_code'];
    }


    public function model(array $row)
    {
        $value = (object)$row;

        $category = new Category();
        $category->setConnection($this->connection);
        $category->itcg_name = $value->category_name;
        $category->itcg_code = $value->category_code;
        $category->lfcl_id = 1;

        $category->cont_id = $this->currentUser->employee()->cont_id;

        $category->aemp_iusr = $this->currentUser->employee()->id;
        $category->aemp_eusr = $this->currentUser->employee()->id;
        $category->save();

    }
}
