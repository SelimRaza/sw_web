<?php

namespace App\MasterData;

use App\MasterData\Base;
use App\MasterData\Zone;
use App\BusinessObject\SalesGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToArray;
use DB;

class SalesGroupZoneBaseMapping extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tbl_slgp_zone_base_mapping';
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

        return ['slgp_code', 'zone_code', 'base_code', 'rout_code', 'base_mob'];
    }

    public function array(array $array)
    {

         $data=$array;
         $check=1;

         for($i=0; $i<count($data); $i++){

             $check++;

             $route = Route::on($this->connection)->where(['rout_code' => $data[$i]['rout_code'] ])->first();
             $sales_data = SalesGroup::on($this->connection)->where(['slgp_code' => $data[$i]['slgp_code']])->first();

                if ($route != null && $sales_data != null) {
                    $sales_base_data = SalesGroupZoneBaseMapping::on($this->connection)->where(['slgp_id' => $sales_data->id, 'rout_id' => $route->id])->first();
                    $zone = Zone::on($this->connection)->where(['zone_code' => $data[$i]['zone_code']])->first();
                    $base = Base::on($this->connection)->where(['base_code' => $data[$i]['base_code']])->first();

                    if($sales_base_data == null) {
                        $insert[] = [
                            'slgp_id' =>$sales_data->id,
                            'zone_id' =>$zone->id,
                            'base_id' =>$base->id,
                            'rout_id' =>$route->id,
                            'base_mob' => $data[$i]['base_mob']
                        ];
                    } else {
                        $sales_base_data->zone_id  = $zone->id;
                        $sales_base_data->base_id  = $base->id;
                        $sales_base_data->base_mob = $data[$i]['base_mob'];
                        $sales_base_data->save();
                    }
                }

            }

            if (!empty($insert)) {
                foreach (array_chunk($insert,1000) as $t)
                    {
                       DB::connection($this->connection)->table('tbl_slgp_zone_base_mapping')->insertOrIgnore($t);
                    }

            }


        // $route = Route::on($this->connection)->where(['rout_code' => $value->rout_code])->first();
        // $sales_data = SalesGroup::on($this->connection)->where(['slgp_code' => $value->slgp_code])->first();

        // if ($route != null && $sales_data != null) {

        //     $sales_base_data = SalesGroupZoneBaseMapping::on($this->connection)->where(['slgp_id' => $sales_data->id, 'rout_id' => $route->id])->first();
        //     $zone = Zone::on($this->connection)->where(['zone_code' => $value->zone_code])->first();
        //     $base = Base::on($this->connection)->where(['base_code' => $value->base_code])->first();

        //     if($sales_base_data == null){
        //         $data = new SalesGroupZoneBaseMapping();
        //         $data->setConnection($this->connection);
        //         $data->slgp_id  = $sales_data->id;
        //         $data->zone_id  = $zone->id;
        //         $data->base_id  = $base->id;
        //         $data->rout_id  = $route->id;
        //         $data->base_mob = $value->base_mob;
        //         $data->save();

        //     }else{
        //         $sales_base_data->zone_id  = $zone->id;
        //         $sales_base_data->base_id  = $base->id;
        //         $sales_base_data->base_mob = $value->base_mob;
        //         $sales_base_data->save();
        //     }

        // }
    }

}
