<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NoteType extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_ntpe';
    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }



    public function headings(): array
    {
        return ['note_type_name', 'note_type_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $noteType = new NoteType();
        $noteType->setConnection($this->connection);
        $noteType->ntpe_name = $value->note_type_name;
        $noteType->ntpe_code = $value->note_type_code;
        $noteType->lfcl_id = 1;
        $noteType->cont_id = $this->currentUser->employee()->cont_id;
        $noteType->aemp_iusr = $this->currentUser->employee()->id;
        $noteType->aemp_eusr = $this->currentUser->employee()->id;
        $noteType->save();

    }


}
