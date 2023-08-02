<?php
/**
 * Created by PhpStorm.
 * User: 328253
 * Date: 02/20/2022
 */

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\BusinessObject\SpaceMaintainShowcase;
use App\BusinessObject\SpaceMaintain;
use App\BusinessObject\SpaceZone;
use App\BusinessObject\SpaceSite;
use App\DataExport\SiteMappingWithSpace;
use App\MasterData\SKU;
use App\MasterData\Site;
use App\MasterData\Employee;

class FakeGpsApks extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
 
    public function getAllInfo(Request $request)
    {
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;

            $fake_gps_apks=DB::connection($db_conn)->select("Select name, url from tm_fgps");

            return array(
                'fake_gps_apks'=>$fake_gps_apks,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                'message'=>'Success'           
            ); 
        }
        catch(\Exception $e){         
            return array(
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>501,
                'message'=>'Error'. $e->getMessage()     
            );
        }
    }
}