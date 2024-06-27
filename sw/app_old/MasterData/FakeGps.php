<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class FakeGps extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_fgps';
    private $currentUser;
    private $db;

    protected $fillable = ['name', 'url', 'aemp_eusr', 'lfcl_id'];
    public $timestamps = false;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['name', 'url'];
    }

    public function model(array $value)
    {

        $fakeGps = FakeGps::on($this->db)->where(['name' => $value['name'], 'url' => $value['url']])->first();

        if ($fakeGps == null) {
            $insert[] = [
                'name'      => $value['name'] ?? '',
                'url'       => $value['url'],
                'aemp_iusr' => $this->currentUser->employee()->id,
                'aemp_eusr' => $this->currentUser->employee()->id,
                'lfcl_id'   => 1
            ];
        }

        if (!empty($insert)) {
            foreach (array_chunk($insert,1000) as $t)
            {
                DB::connection($this->db)->table('tm_fgps')->insert($t);
            }
        }
    }
}
