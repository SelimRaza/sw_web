<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 11/15/2020
 * Time: 5:21 PM
 */

namespace App\Http\Controllers\API\v1;


use App\BusinessObject\FireBaseToken;
use App\Http\Controllers\Controller;
use App\MasterData\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FireBaseData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function saveToken(Request $request)
    {
        $country = (new Country())->country($request->country_id);
        if ($country) {
            $fireBaseToken = FireBaseToken::on($country->cont_conn)->where(['aemp_id' => $request->aemp_id])->first();
            if ($fireBaseToken == null && $request->aemp_id != 0) {
                $fireBaseToken = new FireBaseToken();
                $fireBaseToken->setConnection($country->cont_conn);
                $fireBaseToken->aemp_id = $request->aemp_id;
                $fireBaseToken->eftm_tokn = $request->eftm_tokn;
                $fireBaseToken->cont_id = $request->country_id;
                $fireBaseToken->lfcl_id = 1;
                $fireBaseToken->aemp_iusr = $request->up_emp_id;
                $fireBaseToken->aemp_eusr = $request->up_emp_id;
                $fireBaseToken->save();
                return response()->json(array('column_id' => $request->id), 200);
            } else if ($request->aemp_id != 0) {
                $fireBaseToken->eftm_tokn = $request->eftm_tokn;
                $fireBaseToken->aemp_eusr = $request->up_emp_id;
                $fireBaseToken->save();
                return response()->json(array('column_id' => $request->id), 200);
            }
        }
        return response()->json(array('column_id' => 0), 200);
    }


}