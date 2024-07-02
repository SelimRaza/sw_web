<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Role extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_edsg';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }



    public function headings(): array
    {
        return ['role_name', 'role_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $role = new Role();
        $role->setConnection($this->connection);
        $role->edsg_name = $value->role_name;
        $role->edsg_code = $value->role_code;
        $role->lfcl_id = 1;
        $role->cont_id = $this->currentUser->employee()->cont_id;
        $role->aemp_iusr = $this->currentUser->employee()->id;
        $role->aemp_eusr = $this->currentUser->employee()->id;
        $role->var = 1;
        $role->attr1 = '';
        $role->attr2 = '';
        $role->attr3 = 0;
        $role->attr4 = 0;
        $role->save();

    }
}
