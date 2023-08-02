<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;


class OutletGrade extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_otcg';
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
        return ['category_name', 'category_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $outletGrade = new OutletGrade();
        $outletGrade->setConnection($this->connection);
        $outletGrade->otcg_name = $value->category_name;
        $outletGrade->otcg_code = $value->category_code;
        $outletGrade->lfcl_id = 1;
        $outletGrade->cont_id = $this->currentUser->country()->id;
        $outletGrade->aemp_iusr = $this->currentUser->employee()->id;
        $outletGrade->aemp_eusr = $this->currentUser->employee()->id;
        $outletGrade->save();

    }



}
