<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Region extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_dirg';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }



    public function headings(): array
    {
        return ['region_name', 'region_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $region = new Region();
        $region->setConnection($this->connection);

        $region->dirg_name = $value->region_name;
        $region->dirg_code = $value->region_code;
        $region->cont_id = $this->currentUser->employee()->cont_id;
        $region->lfcl_id = 1;
        $region->aemp_iusr = $this->currentUser->employee()->id;
        $region->aemp_eusr = $this->currentUser->employee()->id;
        $region->var = 1;
        $region->attr1 = '';
        $region->attr2 = '';
        $region->attr3 = 0;
        $region->attr4 = 0;

        $region->save();


        /*$region = new Region();
        $region->setConnection($this->db);
        $region->dirg_name = $request->name;
        $region->dirg_code = $request->code;
        $region->cont_id = $this->currentUser->employee()->cont_id;
        $region->lfcl_id = 1;
        $region->aemp_iusr = $this->currentUser->employee()->id;
        $region->aemp_eusr = $this->currentUser->employee()->id;
        $region->var = 1;
        $region->attr1 = '';
        $region->attr2 = '';
        $region->attr3 = 0;
        $region->attr4 = 0;

        $region->save();*/

    }
    public function division()
    {
        return Division::find($this->division_id);
    }
}
