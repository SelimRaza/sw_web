<?php

namespace App\Http\Controllers\API\v2;

use App\BusinessObject\Attendance;
use App\BusinessObject\DlearProfileAdd;
use App\BusinessObject\LifeCycleStatus;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\ChallanWiseDelivery;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\OrderSyncLog;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\Trip;
use App\BusinessObject\TripOrder;
use App\BusinessObject\TripSku;
use App\Http\Controllers\Controller;
use App\MasterData\Auto;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\RouteSite;
use App\MasterData\Site;
use App\MasterData\TempSite;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

class DMSModuleData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }


    public function CreateVehicleSaveData(Request $request)
    {
        $country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
			$dlrm_id = DB::connection($db_conn)
            ->table('tl_srdi')
            ->where(['aemp_id' => $request->aemp_id])
            ->select('dlrm_id')
            ->first();
			
			if ($dlrm_id && $dlrm_id->dlrm_id > 0) {			 				

            $cur_second = date('mdHi');
           // $code1 = $request->aemp_id.$cur_second;
            $code = 'vh'.$cur_second;


    $insert= DB::connection($db_conn)->table('tm_vhcl')->insert([
    'vhcl_name'=> $request->vhcl_name,
    'vhcl_code' => $code,
    'vhcl_type' => $request->vhcl_type,
    'dpot_id' => $dlrm_id->dlrm_id,
    'vhcl_rdat' => '-',
    'vhcl_ownr' => $request->vhcl_ownr,
    'vhcl_engn' => '-',
    'vhcl_csis' => '-',
    'vhcl_licn' => '-',
    'vhcl_cpct' => '-',
    'vhcl_fuel' => '-',
    'vhcl_lmrd' => '-',
    'vhcl_cpwt' => '-',
    'vhcl_cpht' => '-',
    'vhcl_cpwd' => '-',
    'vhcl_cplg' => '-',
    'cont_id' => $request->country_id,
    'lfcl_id' => 1,
    'aemp_iusr' => $request->aemp_id,
    'aemp_eusr' => $request->aemp_id
    
                 ]);

             if($insert){
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Create Vehicle Successful",
			        );
			 }else{
				return array(
                    'success' => 0,
                    'message' => "Create Vehicle Failed ",
			        ); 
			  }
			}else{
				return array(
                    'success' => 0,
                    'message' => "No Dealer Assaign ,Create Vehicle Failed",
			        );
			 }			 
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
    }
	
	public function AssignVehicletoDM(Request $request){
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
			$ck_id = DB::connection($db_conn)
            ->table('tl_vhcm')
            ->where(['aemp_id' => $request->dm_id])
            ->select('dlrm_id')
            ->first();
			
			$ck_vhcl_id = DB::connection($db_conn)
            ->table('tl_vhcm')
            ->where(['vhcl_id' => $request->vhcl_id])
            ->select('vhcl_id', 'aemp_id')
            ->first();
			
			if ($ck_vhcl_id && $ck_vhcl_id->vhcl_id > 0) {
				return array(
                    'success' => 0,
                    'message' => "This Vehicle Already Assigned to DM: ".$ck_vhcl_id->aemp_id,
			        ); 
			}else{	
				
			if ($ck_id && $ck_id->dlrm_id > 0) {
				
				 $updateResult = DB::connection($db_conn)->table('tl_vhcm')
                ->where('aemp_id', $request->dm_id)
                ->where('dlrm_id', $ck_id->dlrm_id)
                ->update(['vhcl_id' => $request->vhcl_id]);
								
				if($updateResult){
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Assigned Vehicle Updated Successful",
			        );
			 }else{
				return array(
                    'success' => 0,
                    'message' => "Assign Vehicle Failed ",
			        ); 
			   }
			}else{
				
			$dlrm_id = DB::connection($db_conn)
            ->table('tl_srdi')
            ->where(['aemp_id' => $request->aemp_id])
            ->select('dlrm_id')
            ->first();		
	 
			$insert = DB::connection($db_conn)->table('tl_vhcm')->insert([
            'vhcl_id' => $request->vhcl_id,
            'aemp_id' => $request->dm_id,      
            'dlrm_id' => $dlrm_id->dlrm_id
            ]);

             if($insert){
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Assign Vehicle Successful",
			        );
			 }else{
				return array(
                    'success' => 0,
                    'message' => "Assign Vehicle Failed ",
			        ); 
			    }
		 	  } 
			 } 			 
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
	}
	
	public function CreateTripForDM(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
				
			$ck_id = DB::connection($db_conn)
            ->table('dm_trip')
            ->where(['DM_ID' => $request->DM_ID, 'STATUS' => 'N'])
            ->select('DEPOT_ID')
            ->first();
			$ck_in = DB::connection($db_conn)
            ->table('dm_trip')
            ->where(['DM_ID' => $request->DM_ID, 'STATUS' => 'C'])
            ->select('DEPOT_ID')
            ->first();
				
			if ($ck_in && $ck_in->DEPOT_ID > 0) {
            return array(
                'success' => 0,
                'message' => "One trip Already Created, please Go Next Step",
                       );
            }if ($ck_id && $ck_id->DEPOT_ID > 0) {
            return array(
                'success' => 0,
                'message' => "One trip open, please close it, then try",
                       );
            }else{	
				
			$dlrmData = DB::connection($db_conn)
           ->table('tl_srdi')
           ->where(['aemp_id' => $request->aemp_id])
           ->select('dlrm_id', 'acmp_id') // Corrected select method
           ->first();
		                   					   
            $TRIP_NO = "T" .date('ymd'). $request->DM_ID .'-'. str_pad(date('hms'), 8, '0', STR_PAD_LEFT);			
			
    $insert= DB::connection($db_conn)->table('dm_trip')->insert([
    'TRIP_NO'=>$TRIP_NO,
    'TRIP_DATE' => date('Y-m-d'),
    'DM_ID' => $request->DM_ID,
    'STATUS' => 'C',
    'V_ID' => $request->V_ID,
    'SALES_TYPE' => 'PS',
    'DEPOT_ID' => $dlrmData->dlrm_id,
    'company_id' => $dlrmData->acmp_id

                 ]);

             if($insert){
                DB::connection($db_conn)->commit();
                return array(
                    'success' => 1,
                    'message' => "Create Trip For DM Successful",
			        );
			 }else{
				return array(
                    'success' => 0,
                    'message' => "Create Trip For DM Failed ",
			        ); 
			  }
			 }
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
        }
	}
	public function SaveDMSItemStock(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
				
				$dlrm_id = DB::connection($db_conn)
                ->table('tl_srdi')
                ->where(['aemp_id' => $request->aemp_id])
                ->select('dlrm_id')
                ->first();

           if ($dlrm_id && $dlrm_id->dlrm_id > 0) {
           $orderLines = json_decode($request->line_data);
           $success = true;

           foreach ($orderLines as $orderLineData) {
           $existingRecord = DB::connection($db_conn)->table('tt_dlrm_stock')
            ->where('amim_id', $orderLineData->amim_id)
            ->where('dlrm_id', $dlrm_id->dlrm_id)
            ->first();

          if ($existingRecord) {
            $updateResult = DB::connection($db_conn)->table('tt_dlrm_stock')
                ->where('amim_id', $orderLineData->amim_id)
                ->where('dlrm_id', $dlrm_id->dlrm_id)
                ->update(['qnty' => $orderLineData->qnty]);

            if (!$updateResult) {
                $success = false;
                break;
            }
        } else {
            $insertResult = DB::connection($db_conn)->table('tt_dlrm_stock')->insert([
                'amim_id' => $orderLineData->amim_id,
                'dlrm_id' => $dlrm_id->dlrm_id,
                'qnty' => $orderLineData->qnty
            ]);

            if (!$insertResult) {
                $success = false;
                break;
            }
        }
    }

    if ($success) {
        DB::connection($db_conn)->commit();
        return [
            'success' => 1,
            'message' => "Upload Stock Successful",
        ];
    } else {
        return [
            'success' => 0,
            'message' => "Upload Stock Failed",
        ];
    }
} else {
    return [
        'success' => 0,
        'message' => "Invalid dlrm_id",
    ];
}
			
		      
            } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
          }
        }
	}
	
	public function GetVehicleListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {				    
			$dlrm_id = DB::connection($db_conn)->select("
            SELECT t1.id as vhcl_id,t1.vhcl_name,t1.vhcl_code, t1.vhcl_type, t1.dpot_id 
            FROM `tm_vhcl`t1 JOIN tl_srdi t2 ON t1.`dpot_id`=t2.dlrm_id and t2.aemp_id=$request->aemp_id
            WHERE 1; ");
			
			return $dlrm_id;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     }
	 public function GetTransitTripListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {	
               $dlrm_id = DB::connection($db_conn)
                ->table('tl_srdi')
                ->where(['aemp_id' => $request->aemp_id])
                ->select('dlrm_id')
                ->first();

           if ($dlrm_id && $dlrm_id->dlrm_id > 0) {
			
			$TripList = DB::connection($db_conn)->select("
            SELECT t1.`id`trip_id,t1.`TRIP_NO` trip_no,
            t1.`TRIP_DATE`,t1.`STATUS`,t1.`DM_ID`,
            t2.aemp_usnm md_code,t2.aemp_name dm_name,
            t1.`V_ID`,t3.vhcl_name,t3.vhcl_code,t3.vhcl_type, t1.`SALES_TYPE` 
            FROM `dm_trip`t1 
            JOIN tm_aemp t2 ON t1.`DM_ID`=t2.id 
            JOIN tm_vhcl t3 ON t1.`V_ID`=t3.id
            WHERE t1.`STATUS`='N'AND t1.DEPOT_ID=$dlrm_id->dlrm_id;");
			
			return $TripList;
			}else{
				return [];
			   }
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     } 
	 
	 public function GetTripWiseOrderListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {				    
			$dlrm_id = DB::connection($db_conn)->select("
            SELECT t1.`ORDM_DATE`, t1.`ORDM_ORNM`,t1.`ORDM_DRDT`,t1.`AEMP_ID`,t1.`AEMP_USNM`,
            t1.`SITE_ID`,t1.`SITE_CODE`,t2.site_name,t1.`ORDD_AMNT`,t1.`INV_AMNT`,t1.`DELV_AMNT`,
            t1.`COLLECTION_AMNT`,t1.`V_NAME`,t1.`TRIP_NO`,t1.`DELIVERY_STATUS`,
            t1.`slgp_id`,t1.`SALES_TYPE`,t1.`DM_CODE`dm_id,t3.aemp_usnm,t3.aemp_name
            FROM `dm_trip_master`t1
            JOIN tm_site t2 ON t1.`SITE_ID`=t2.id 
            JOIN tm_aemp t3 ON t1.`DM_CODE`=t3.id
            WHERE t1.`TRIP_NO`='$request->TRIP_NO';");
			
			return $dlrm_id;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     }
	 
	 public function GetDMOpenTripListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
		
		    		
			$dlrm_id = DB::connection($db_conn)->select("
            SELECT t1.`id`trip_id,t1.`TRIP_NO`,t1.`TRIP_DATE`,t1.`DM_ID`,t3.vhcl_name,t3.vhcl_code,
			t2.aemp_usnm AS dm_code,t1.`STATUS`,t1.`DEPOT_ID` AS dealer 
            FROM `dm_trip`t1 
            JOIN tm_aemp t2 ON t1.`DM_ID`=t2.id
            JOIN tm_vhcl t3 ON t1.`V_ID`=t3.id
            WHERE t1.`DEPOT_ID`IN(SELECT `dlrm_id` FROM `tl_srdi` WHERE `aemp_id` = $request->aemp_id)
            AND t1.`STATUS`='C'
            AND t1.`DM_ACTIVITY`=31;");
			
			return $dlrm_id;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     } public function GetDMListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
		
		    		
			$dlrm_id = DB::connection($db_conn)->select("
            SELECT
            t1.`aemp_id`,
            t2.aemp_usnm,
            t2.aemp_name,
            t2.aemp_mob1,
			t1.`dlrm_id`,
			IFNULL( t3.vhcl_id, 0) AS vhcl_id,
            t4.vhcl_code,
            t4.vhcl_name,
            t4.vhcl_type
			FROM `tl_srdi` t1
			JOIN tm_aemp t2 ON
			t1.`aemp_id` = t2.id
            LEFT JOIN tl_vhcm t3 ON t1.aemp_id=t3.aemp_id
            LEFT JOIN tm_vhcl t4 ON t3.vhcl_id=t4.id
			WHERE t1.`dlrm_id` =(
                  SELECT
		         `dlrm_id`
			      FROM
				 `tl_srdi`
				  WHERE`aemp_id` = $request->aemp_id)
			AND t1.`aemp_id` != $request->aemp_id and t2.role_id=56;");
			
			return $dlrm_id;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     }
	 
	 public function GetSRListByDMS(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
		
		    		
			$dlrm_id = DB::connection($db_conn)->select("
            SELECT
            t1.`aemp_id`,
            t2.aemp_usnm,
            t2.aemp_name,
            t2.aemp_mob1,
			t1.`dlrm_id`
			FROM `tl_srdi` t1
			JOIN tm_aemp t2 ON
			t1.`aemp_id` = t2.id
			WHERE t1.`dlrm_id` =(
                  SELECT
		         `dlrm_id`
			      FROM
				 `tl_srdi`
				  WHERE`aemp_id` = $request->aemp_id)
			AND t1.`aemp_id` != $request->aemp_id and t2.role_id=1;");
			
			return $dlrm_id;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     }
	 	 public function GetSRPendingOrderByDMS(Request $request){
         $country = (new Country())->country($request->country_id);

        if ($country != false) {
        $db_conn = $country->cont_conn;
        DB::connection($db_conn)->beginTransaction();

        try {
            $dlrmId = $request->dlrm_id;
            $srIds = $request->sr_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $ordmOm = DB::connection($db_conn)->table('tt_ordm AS t1')
                ->join('tm_site AS t2', 't1.site_id', '=', 't2.id')
                ->where('t1.aemp_id', $srIds)
                ->where('t1.dlrm_id', $dlrmId)
                ->where('t1.lfcl_id', 1)
                ->whereBetween('t1.ordm_date', [$startDate, $endDate])
                ->select(
                    't1.id AS ordm_id',
                    't1.ordm_ornm',
                    't1.site_id',
                    't1.slgp_id',
                    't1.ordm_date',
                    't1.ordm_amnt',
                    't2.site_code',
                    't2.site_name',
                    't2.site_mob1'
                )
                ->get();
             $ordmIds = $ordmOm->pluck('ordm_id')->toArray();
            
            if (count($ordmOm) > 0) {
                $orddData = DB::connection($db_conn)->table('tt_ordd AS t2')
                    ->join('tm_amim AS t3', 't2.amim_id', '=', 't3.id')
                    ->whereIn('t2.ordm_id', $ordmIds)
                    ->select(
                        't2.ordm_id AS ordm_id',
                        't2.ordm_ornm AS ORDM_ORNM',
                        't2.id AS trp_line',                       
                        't2.amim_id AS AMIM_ID',
                        't3.amim_code AS AMIM_CODE',
                        't3.amim_name AS Item_Name',
                        't2.ordd_inty AS ORDD_QNTY',
                        DB::raw('0 AS DELV_QNTY'),
                        't2.ordd_opds',
                        't2.ordd_spdi',
                        't2.ordd_dfdo',
                        DB::raw('(t2.ordd_dfdo + t2.ordd_opds + t2.ordd_spdi) AS DISCOUNT'),
                        't2.ordd_duft AS Item_Factor',
                        't2.ordd_uprc AS Rate',
                        't2.ordd_smpl',
                        't2.prom_id',
                        't2.ordd_ovat AS VAT_Percent',
                        't2.ordd_excs AS EXCS_Percent',
                        't2.ordd_oamt'
                    )
                    ->get();              				
				
				$ordmOm->each(function ($ordm) use ($orddData) {
                $ordm->orderIdLists = $orddData->where('ordm_id', $ordm->ordm_id)->values()->all();
                });
            }
			 

            DB::connection($db_conn)->commit();
            return $ordmOm;
        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return $e;
        }
    } else {
        return "Country not found";
    }
 } 
 public function GetDMPendingTripOrderByTrip(Request $request){
         $country = (new Country())->country($request->country_id);

        if ($country != false) {
        $db_conn = $country->cont_conn;
        DB::connection($db_conn)->beginTransaction();

        try {
           $tripNo = $request->trip_No;

  $ordmOm = DB::connection($db_conn)->table('dm_trip_master')
    ->select(
        'ORDM_ORNM',
        'ORDM_DATE',
        'ORDM_DRDT',
        'AEMP_ID',
        'AEMP_USNM',
        'WH_ID',
        'SITE_ID',
        'SITE_CODE',
        'ORDD_AMNT',
        'INV_AMNT',
        'DELV_AMNT',
        'COLLECTION_AMNT',
        'SHIPINGADD',
        'DM_CODE',
        'V_NAME',
        'TRIP_NO',
        'slgp_id'
    )
    ->where('TRIP_NO', '=', $tripNo)
    ->get();

    $ordmIds = $ordmOm->pluck('ORDM_ORNM')->toArray();

    if (count($ordmOm) > 0) {
    $orddData = DB::connection($db_conn)->table('dm_trip_detail AS t1')
        ->join('tm_amim AS t2', 't1.AMIM_ID', '=', 't2.id')
        ->select(
            't1.ORDM_ORNM',
            't1.AMIM_ID',
            't1.AMIM_CODE',
            't2.amim_name',
            't2.amim_duft AS factor',
            't1.ORDD_QNTY',
            't1.INV_QNTY',
            't1.DELV_QNTY',
            't1.ORDD_UPRC',
            't1.ORDD_EXCS',
            't1.ORDD_OVAT',
            't1.ORDD_OAMT',
            't1.prom_id',
            't1.DISCOUNT AS DISCOUNT',
            't1.ordd_opds',
            't1.ordd_spdc',
            't1.ordd_dfdo'
        )
        ->where('TRIP_NO', '=', $tripNo)
        ->whereIn('ORDM_ORNM', $ordmIds) // Corrected 'whereIn'
        ->get();

    $ordmOm->each(function ($ordm) use ($orddData) {
        $ordm->orderIdLists = $orddData->where('ORDM_ORNM', $ordm->ORDM_ORNM)->values()->all();
     });
   }			
            DB::connection($db_conn)->commit();
            return $ordmOm;
        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return $e;
        }
    } else {
        return "Country not found";
    }
 }
	 
	 public function GetDMSItemListForStock(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
            try {
		$dlrm_id = DB::connection($db_conn)
                ->table('tl_srdi')
                ->where(['aemp_id' => $request->aemp_id])
                ->select('dlrm_id')
                ->first();           
		    		
	$dlrm_idd = DB::connection($db_conn)->select("
    SELECT t3.id                                         AS item_id,
    t3.amim_code                                         AS item_code,
    t3.amim_imgl                                         AS amim_imgl,
    t3.amim_imic                                         AS amim_imic,
    t4.issc_name                                         AS category_name,
    t3.amim_name                                         AS item_mame, 
    t3.amim_duft                                         AS item_factor,
    IFNULL(t5.qnty, 0)                                   AS item_stock  
    FROM `tl_sgit`t1 
    JOIN tm_dlrm t2 ON t1.`slgp_id`=t2.slgp_id 
    JOIN tm_amim t3 ON t1.`amim_id`=t3.id
    INNER JOIN tm_issc t4 ON t1.issc_id = t4.id
	LEFT JOIN tt_dlrm_stock t5 ON t1.amim_id=t5.amim_id AND t5.dlrm_id=$dlrm_id->dlrm_id
    WHERE t3.lfcl_id=1 AND t2.id=$dlrm_id->dlrm_id
    GROUP BY t3.id,
    t3.amim_code,
    t3.amim_imgl,
    t3.amim_imic,
    t4.issc_name,
    t3.amim_name, 
    t3.amim_duft;");
			
			return $dlrm_idd;
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     } 
	 
	 public function PushOrderToDM(Request $request)
    {

    $country = (new Country())->country($request->country_id);

    if ($country !== false) {
        $db_conn = $country->cont_conn;
        DB::connection($db_conn)->beginTransaction();

        try {
            $date = date('Y-m-d');
            $sql_tpm = "";
            $sql_tpd = "";
            $sql8 = "";

            $dlrm_id = DB::connection($db_conn)
                ->table('tl_srdi as t1')
                ->join('tm_acmp as t2', 't1.acmp_id', '=', 't2.id')
                ->join('tm_dlrm as t3', 't1.dlrm_id', '=', 't3.id')
                ->select('t1.dlrm_id', 't3.dlrm_code', 't2.acmp_code')
                ->where('t1.aemp_id', '=', $request->up_emp_id)
                ->first();

            if ($dlrm_id && $dlrm_id->dlrm_id > 0) {
                $WH_ID = $dlrm_id->dlrm_code;
                $ACMP_CODE = $dlrm_id->acmp_code;
            } else {
                return [
                    'success' => 0,
                    'message' => "Dealer Mapping data Missing",
                ];
            }

            $orderLines = json_decode($request->line_data);

            if ($orderLines) {
                foreach ($orderLines as $orderLineData) {
                    $address = $orderLineData->site_name . '-' . $orderLineData->site_mob1;
                    $geo_lat = 0;
                    $geo_lon = 0;

                     $sql_tpm .= "INSERT INTO `dm_trip_master`(`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`,
                    `AEMP_USNM`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `GEO_LAT`, `GEO_LON`, `ORDD_AMNT`, `INV_AMNT`, `DELV_AMNT`, `COLLECTION_AMNT`,
                    `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`, `V_NAME`, `TRIP_NO`, `COLL_STATUS`,`DELIVERY_STATUS`, `slgp_id`,SALES_TYPE) VALUES (
                    '$ACMP_CODE','$orderLineData->ordm_date','$orderLineData->ordm_ornm','$date',$orderLineData->emp_id,
                    '$orderLineData->emp_code','$WH_ID',$orderLineData->site_id,'$orderLineData->site_code',
                     $geo_lat,
                     $geo_lon,
                     $orderLineData->ordm_amnt,$orderLineData->ordm_amnt,0,0,
                    '$address','$request->DM_ID','N','$request->V_NAME','$request->trip_code',7,0,$orderLineData->slgp_id,'PS');";
                    
					 $sql8 .= " UPDATE `tt_ordm` SET `lfcl_id`=34 WHERE id='$orderLineData->ordm_id'; ";
					 
					  $sitewisederLines = $orderLineData->orderIdLists;
					  
					  
					   foreach ($sitewisederLines as $sitewisederLineData) {
						  
                           $sitewisederLineData->trp_line;
						   $sitewisederLineData->AMIM_ID;
						   $sitewisederLineData->AMIM_CODE;
						   $sitewisederLineData->ORDD_QNTY;
						   $sitewisederLineData->Rate;
						   $sitewisederLineData->EXCS_Percent;
						   $sitewisederLineData->EXCS_Percent;
                           $sitewisederLineData->totalOrderAmount = $sitewisederLineData->ORDD_QNTY * $sitewisederLineData->Rate;
						   $sitewisederLineData->prom_id;
						   $sitewisederLineData->DISCOUNT;

						   
						  $sql_tpd .= "INSERT INTO `dm_trip_detail`(`OID`,`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`, 
                        `AEMP_USNM`, `DLRM_ID`, `SITE_ID`, `SITE_CODE`, `AMIM_ID`, `AMIM_CODE`, 
                        `GEO_LAT`, `GEO_LON`, `ORDD_QNTY`, `INV_QNTY`, `DELV_QNTY`,
                        `RETURN_QNTY`, `ORDD_UPRC`, `ORDD_EXCS`, `ORDD_OVAT`, `ORDD_OAMT`, `prom_id`,
                         `DISCOUNT`, `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`,
                         `V_NAME`, `TRIP_NO`, `TRIP_STATUS`) 
                         VALUES ($sitewisederLineData->trp_line,'$ACMP_CODE','$orderLineData->ordm_date','$orderLineData->ordm_ornm','$date',$orderLineData->emp_id,
                        '$orderLineData->emp_code','$WH_ID',$orderLineData->site_id,'$orderLineData->site_code',$sitewisederLineData->AMIM_ID,'$sitewisederLineData->AMIM_CODE',
                         $geo_lat,$geo_lon,$sitewisederLineData->ORDD_QNTY,$sitewisederLineData->ORDD_QNTY,0,
                         0,$sitewisederLineData->Rate,$sitewisederLineData->EXCS_Percent,$sitewisederLineData->EXCS_Percent,
						 $sitewisederLineData->totalOrderAmount,$sitewisederLineData->prom_id,
                         $sitewisederLineData->DISCOUNT,'$address','$request->DM_ID','N',
                        '$request->V_NAME','$request->trip_code','N');";
                      				   						   						   
					   }
                }

                if (!empty($sql_tpm)) {
                    DB::connection($country->cont_conn)->unprepared($sql_tpm); // Execute multiple row insert
                    DB::connection($db_conn)->unprepared($sql_tpd); // Execute multiple row insert
                    DB::connection($db_conn)->unprepared($sql8); // Execute multiple row insert
						 					 
                }

                DB::connection($country->cont_conn)->commit();

                return [
                    'success' => 1,                    
                    'message' => "Successfully Save Order" ,
                ];
            } else {
                return [
                    'success' => 0,
                    'message' => "Invalid JSON format in line_data",
                ];
            }
        } catch (\Exception $e) {
            DB::connection($db_conn)->rollback();
            return [
                'success' => 0,
                'message' => "An error occurred: " . $e->getMessage(),
            ];
        }
    } else {
        return [
            'success' => 0,
            'message' => "Invalid country_id",
        ];
    }
}

 public function DMSSendTripToDM(Request $request){
		
		
		$country = (new Country())->country($request->country_id);
// return $country;
        if ($country != false) {
            $db_conn = $country->cont_conn;
            
            try {
		                        				   
                   $tripNo = $request->trip_code;				 
                   $tripNo_Update= DB::connection($db_conn)
                   ->table('dm_trip')
                   ->where('TRIP_NO', $tripNo)
                   ->update(['STATUS' => 'N']);
				
			 if($tripNo_Update){				 
				 return [
                    'success' => $tripNo_Update,                    
                    'message' => "Successfully Send Trip to DM" ,
                ]; 
			 }else{				  
				 return [
                    'success' => 0,                    
                    'message' => "Send Trip to DM Failed" ,
                ]; 
			 }
			
	         } catch (\Exception $e) {
				 
                //DB::connection($db_conn)->rollback();
                return $e->getMessage();
            }
	     }
     } 
	 
	 public function PushOrderToDM1(Request $request){
		
		
		$country = (new Country())->country($request->country_id);

        if ($country != false) {
            $db_conn = $country->cont_conn;
            DB::connection($db_conn)->beginTransaction();
			
		$request->country_id;                                          
        $request->V_NAME;       
        $request->trip_code;       
        $request->DM_CODE;
        $request->up_emp_id;
		
	 $dlrm_id = DB::connection($db_conn)
    ->table('tl_srdi as t1')
    ->join('tm_acmp as t2', 't1.acmp_id', '=', 't2.id')
    ->join('tm_dlrm as t3', 't1.dlrm_id', '=', 't3.id')
    ->select('t1.dlrm_id', 't3.dlrm_code', 't2.acmp_code')
    ->where('t1.aemp_id', '=', $request->up_emp_id)
    ->first();
    $WH_ID = 0;
    $ACMP_CODE = 0;

    if ($dlrm_id && $dlrm_id->dlrm_id > 0) {
    $WH_ID = $dlrm_id->dlrm_code;
    $ACMP_CODE = $dlrm_id->acmp_code;
    }else{
	return array(
        'success' => 0,                        
        'message' => "Dealer Mapping data Missing",
        );
   }
            try {
		            $date = date('Y-m-d');
                    $sql_tpm = "";
                    $sql_tpd = "";
                    $sql = "SET autocommit=0;";
                   
                   
				   
					$orderLines = json_decode($request->line_data);
					
					
 
                    foreach ($orderLines as $orderLineData) {	
                  				
				      $orderLineData->ordm_amnt;
				      $orderLineData->ordm_date;
				      $orderLineData->ordm_ornm;
				      $orderLineData->slgp_id;
				      $orderLineData->site_code;
				      $orderLineData->site_id;					  
				      $orderLineData->site_mob1;
				      $orderLineData->site_name;					  
				      $orderLineData->emp_code;
				      $orderLineData->emp_id;

                    $address = $orderLineData->site_name . '-' . $orderLineData->site_mob1;
                    $geo_lat = 0;
                    $geo_lon = 0;

                     $sql_tpm .= "INSERT INTO `dm_trip_master`(`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`,
                    `AEMP_USNM`, `WH_ID`, `SITE_ID`, `SITE_CODE`, `GEO_LAT`, `GEO_LON`, `ORDD_AMNT`, `INV_AMNT`, `DELV_AMNT`, `COLLECTION_AMNT`,
                    `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`, `V_NAME`, `TRIP_NO`, `COLL_STATUS`,`DELIVERY_STATUS`, `slgp_id`,SALES_TYPE) VALUES (
                    '$ACMP_CODE','$orderLineData->ordm_date','$orderLineData->ordm_ornm','$date',$orderLineData->emp_id,
                    '$orderLineData->emp_code','$WH_ID',$orderLineData->site_id,'$orderLineData->site_code',
                     $geo_lat,
                     $geo_lon,
                     $orderLineData->ordm_amnt,$orderLineData->ordm_amnt,0,0,
                    '$address','$request->DM_CODE','N','$request->V_NAME','$request->trip_code',7,0,$orderLineData->slgp_id,'PS');";
                    
					  $sitewisederLines = $orderLineData->orderIdLists;
					  
					  
					   foreach ($sitewisederLines as $sitewisederLineData) {
						  
                           $sitewisederLineData->line_id;
						   $sitewisederLineData->amim_id;						  
						   $sitewisederLineData->amim_code;						  
						   $sitewisederLineData->ordd_inty;
						   $sitewisederLineData->ordd_uprc;
						   $sitewisederLineData->excise_percent;
						   $sitewisederLineData->vat_percent;
						   $sitewisederLineData->ordd_oamt;						   
						   $sitewisederLineData->promo_ref;
						   $sitewisederLineData->total_discount;						  
						   
						   
						  $sql_tpd .= "INSERT INTO `dm_trip_detail`(`OID`,`ACMP_CODE`, `ORDM_DATE`, `ORDM_ORNM`, `ORDM_DRDT`, `AEMP_ID`, 
                        `AEMP_USNM`, `DLRM_ID`, `SITE_ID`, `SITE_CODE`, `AMIM_ID`, `AMIM_CODE`, 
                        `GEO_LAT`, `GEO_LON`, `ORDD_QNTY`, `INV_QNTY`, `DELV_QNTY`,
                        `RETURN_QNTY`, `ORDD_UPRC`, `ORDD_EXCS`, `ORDD_OVAT`, `ORDD_OAMT`, `prom_id`,
                         `DISCOUNT`, `SHIPINGADD`, `DM_CODE`, `IBS_INVOICE`,
                         `V_NAME`, `TRIP_NO`, `TRIP_STATUS`) 
                         VALUES ($sitewisederLineData->line_id,'$ACMP_CODE','$orderLineData->ordm_date','$orderLineData->ordm_ornm','$date',$orderLineData->emp_id,
                        '$orderLineData->emp_code','$WH_ID',$orderLineData->site_id,'$orderLineData->site_code',$sitewisederLineData->amim_id,'$sitewisederLineData->amim_code',
                         $geo_lat,$geo_lon,$sitewisederLineData->ordd_inty,$sitewisederLineData->ordd_inty,0,
                         0,$sitewisederLineData->ordd_uprc,$sitewisederLineData->excise_percent,$sitewisederLineData->vat_percent,
						 $sitewisederLineData->ordd_oamt,$sitewisederLineData->promo_ref,
                         $sitewisederLineData->total_discount,'$address','$request->DM_CODE','N',
                        '$request->V_NAME','$request->trip_code','N');";
                      				   						   						   
					   }	  
                    }
                         if (!empty($sql_tpm)) {
							DB::connection($country->cont_conn)->insert($sql);
                            DB::connection($db_conn)->unprepared($sql_tpm);//multiple row
                            DB::connection($db_conn)->unprepared($sql_tpd);//multiple row                           
                        }
					DB::connection($country->cont_conn)->commit();
					 return array(
                        'success' => 1,
                        'column_id' => $request->id,
                        'message' => "Successfully Save Order w" . $request->id,
                    );
			
	         } catch (\Exception $e) {
                DB::connection($db_conn)->rollback();
                return $e;
            }
	     }
     }

}