<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesGroupCategory extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_issc';
    private $currentUser;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'category_name', 'category_code', 'category_sequence', 'slgp_id'
        ];
    }

    public function model(array $row)
    {
        try {

            $request = (object)$row;

            $site = SalesGroupCategory::on($this->connection)->where(['slgp_id' => $request->slgp_id,
                'issc_code' => $request->category_code
            ])->first();

            if ($site == null) {
                $salesGroupSKU = new SalesGroupCategory();
                $salesGroupSKU->setConnection($this->connection);
                $salesGroupSKU->issc_name = $request->category_name;
                $salesGroupSKU->issc_code = $request->category_code;
                $salesGroupSKU->issc_seqn = $request->category_sequence;
                $salesGroupSKU->issc_opst = 0;
                $salesGroupSKU->slgp_id = $request->slgp_id;
                $salesGroupSKU->cont_id = $this->currentUser->country()->id;
                $salesGroupSKU->lfcl_id = 1;
                $salesGroupSKU->aemp_iusr = $this->currentUser->employee()->id;
                $salesGroupSKU->aemp_eusr = $this->currentUser->employee()->id;
                $salesGroupSKU->var = 1;
                $salesGroupSKU->attr1 = '';
                $salesGroupSKU->attr2 = '';
                $salesGroupSKU->attr3 = 0;
                $salesGroupSKU->attr4 = 0;
                $salesGroupSKU->save();
            }

        }catch(\Exception $e)
        {
            dd($e);
            return;
        }
    }

}
