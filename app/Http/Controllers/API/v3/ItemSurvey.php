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
use App\BusinessObject\ItemSurveyM;
use App\BusinessObject\ItemSurveyImageM;
use App\MasterData\RplnH;
use App\MasterData\Route;
use App\MasterData\Employee;

class ItemSurvey extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
 
    public function marketItemSurveyStore(Request $request)
    {
        
        $start_time=date('Y-m-d h:i:s');
        DB::beginTransaction();
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $request->validate([
                'emp_id' => 'required',
                'site_id' => 'required',
                'site_code' => 'required',
                'item_name' => 'required',
                'acmp_name' => 'required',
                'item_weit' => 'required',
                'item_tppr' => 'required',
                'item_mrpp' => 'required',
                'mnfc_date' => 'required|date',
                'expr_date' => 'required|date|after:mnfc_date',
                'prom_desc' => 'required',
                'item_imge' =>'required',
                'country_name' =>'required',
            ], [
                'required' => 'The :attribute field is required.',
                'date' => 'The :attribute field must be a valid date.',
                'after' => 'The :attribute field must be after :date.',
            ]); 
           // return $request->all();           
            $aemp_id = $request->emp_id;
            $site_id = $request->site_id;
            $site_code = $request->site_code;
            $item_name = $request->item_name;
            $acmp_name = $request->acmp_name;
            $item_weit = $request->item_weit;
            $item_tppr = $request->item_tppr;
            $item_mrpp = $request->item_mrpp;
            $mnfc_date = $request->mnfc_date;
            $expr_date = $request->expr_date;
            $prom_desc = $request->prom_desc;
            $item_imge = $request->item_imge;
            $country_name = $request->country_name;
            $itsv = new ItemSurveyM();
            $itsv->setConnection($db_conn);
           // return gettype($item_imge);
            $itsv->aemp_iusr = $aemp_id;
            $itsv->aemp_eusr = $aemp_id;
            $itsv->site_id = $site_id;
            $itsv->site_code = $site_code;
            $itsv->item_name = $item_name;
            $itsv->acmp_name = $acmp_name;
            $itsv->item_weit = $item_weit;
            $itsv->item_tppr = $item_tppr;
            $itsv->item_mrpp = $item_mrpp;
            $itsv->mnfc_date = $mnfc_date;
            $itsv->expr_date = $expr_date;
            $itsv->prom_desc = $prom_desc;            
            $itsv->country_name = $country_name;            
            $itsv->svit_id = $request->survey_id;            
            $itsv->class_id =$request->class_id;          
            $itsv->save();   
            $all_img=json_decode($item_imge, true);
            foreach ($all_img as $img) {
                $image=new ItemSurveyImageM();
                $image->setConnection($db_conn);
                $image->itsv_id=$itsv->id;
                $image->svmg_imge=$img['path'];
                $image->save();
            }
            DB::commit();         
            return array(
                'success'=>1,
                'message'=>'Success ! Thanks for your cooperation',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>http_response_code(),
                        
            ); 
        }
        catch(\Exception $e){   
            DB::rollback();
            $errors = $e->validator->errors()->toArray();
            $error_messages = [];
            // foreach ($errors as $field => $message) {
            //     $error_messages[] = [
            //         'field' => $field,
            //         'message' => $message[0]
            //     ];
            // }
            return array(
                'success'=>0,
                'message'=>'Validation failed',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
                'hhh'=>$errors
            );
        }
        
    }
    public function getSurveyItem(Request $request){
        $start_time=date('Y-m-d h:i:s');
        try{
            $country = (new Country())->country($request->country_id);
            $db_conn = $country->cont_conn;
            $item_list=DB::connection($db_conn)->select("SELECT id survey_id,class_id,class_name,amim_name item_name FROM `tm_svit` WHERE curdate() between sv_sdat AND sv_edat AND lfcl_id=1 ORDER BY amim_name ASC");
            return array(
                'success'=>1,
                'message'=>'Success',
                'item_list'=>$item_list,
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>200
            );

        }
        catch(\Exception $e){
            return array(
                'success'=>0,
                'message'=>'Failed',
                'request_time'=>$start_time,
                'response_time'=>date('Y-m-d h:i:s'),
                'status'=>422,
                'errors'=>$e->getMessage(),
            );
        }
        
    }
}