<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiteMobileInfo extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_site';
    private $currentUser;

    protected $connection= '';
    public function __construct()
    {
        if (Auth::user()!=null){
            $this->currentUser  = Auth::user();
            $this->connection   = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'outlet_code', 'mobile'
        ];
    }

    public function model(array $value)
    {
        $request = (object)$value;

        try {
            $site = Site::on($this->connection)->where('site_code', $request->outlet_code)->first();

            if(!$site){
                return;
            }

            $site->update([
                'site_mob1' => $request->mobile,
                'aemp_eusr' => $this->currentUser->employee()->id
            ]);

            $outlet = Outlet::on($this->connection)->find($site->outl_id);

            if(!$outlet){
                return;
            }

            $outlet->update([
                'oult_mob1' => $request->mobile,
                'aemp_eusr' => $this->currentUser->employee()->id
            ]);
        }
        catch (\Exception $exception){
            return;
        }
    }
}
