<?php

namespace App\MasterData;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Asset extends Model implements WithHeadings, ToModel, WithHeadingRow
{


    protected $table = 'tm_astm';

    protected $fillable = ['astm_name', 'astm_type'];

    public $timestamps = false;

    private $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }

    public function headings(): array
    {
        return ['id', 'name', 'type'];
    }


    public function model(array $row)
    {
        $value = (object)$row;

        $asset = new Asset();
        $asset->setConnection($this->connection);
        $asset->astm_name = $value->name;
        $asset->astm_type = $value->type;
        $asset->save();

    }
}
