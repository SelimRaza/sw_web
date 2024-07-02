<?php

namespace App\MasterData;

use App\MasterData\Site;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\DmTripMasterLog;
use App\Mapping\EmployeeManagerUpload;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DmTransferUserGroup extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'dm_trip_master_log';
    private $currentUser;
    private $db;
    private $cont_id;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
        }
    }

    public function headings(): array
    {
        return [
            'COLL_NUMBER',
            'SITE_CODE',
            'AEMP_USNM',
            'WH_ID',
            'SLGP_ID',
            'SG_CODE',
        ];
    }

    public function array(array $array)
    {
        
        $data = $array;
        $insert = [];
        // dd($data);
        foreach ($data as $item) {

            $ordm_ornm  = $item['coll_number'];
            $site_code  = $item['site_code'];
            $aemp_usnm  = $item['aemp_usnm'];
            $wh_id      = $item['wh_id'];
            $slgp_id    = $item['slgp_id'];
            $sg_code    = $item['sg_code'];
			
			$dm_data    = DB::connection($this->db)->select("SELECT * FROM `dm_trip_master` WHERE ORDM_ORNM = '$ordm_ornm'")[0];
                        
            if ($dm_data) {
			  	if ($dm_data->COLLECTION_AMNT == $dm_data->DELV_AMNT) {
				//   return redirect()->back()->with('warning', ' Already Paid');
				}else{
                    $aemp_data = EmployeeManagerUpload::where('aemp_usnm', $aemp_usnm)->first();
                    if ($aemp_data) {
                        $insert[] = [ 
                            "dm_trip_master_id"	=> $dm_data->ID,
                            "ACMP_CODE"     	=> $dm_data->ACMP_CODE,
                            "ORDM_DATE"    		=> $dm_data->ORDM_DATE,
                            "ORDM_ORNM"       	=> $dm_data->ORDM_ORNM,
                            "ORDM_DRDT"      	=> $dm_data->ORDM_DRDT,
                            "AEMP_ID"      		=> $dm_data->AEMP_ID,
                            "AEMP_USNM"     	=> $dm_data->AEMP_USNM,
                            "WH_ID"       		=> $dm_data->WH_ID,
                            "SITE_ID"       	=> $dm_data->SITE_ID,
                            "SITE_CODE"       	=> $dm_data->SITE_CODE,
                            "ORDD_AMNT"      	=> $dm_data->ORDD_AMNT,
                            "INV_AMNT"       	=> $dm_data->INV_AMNT,
                            "DELV_AMNT"       	=> $dm_data->DELV_AMNT,
                            "COLLECTION_AMNT" 	=> $dm_data->COLLECTION_AMNT,
                            "IBS_INVOICE"     	=> $dm_data->IBS_INVOICE,
                            "COLL_STATUS"     	=> $dm_data->COLL_STATUS,
                            "DELIVERY_STATUS" 	=> $dm_data->DELIVERY_STATUS,
                            "slgp_id"       	=> $dm_data->slgp_id,
                            "created_by"    	=> $this->currentUser->id
                        ];

                        $ordm_ornm = $dm_data->ORDM_ORNM;                        
                        DB::connection($this->db)->select("Update dm_trip_master SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm',slgp_id='$slgp_id' WHERE ID=$dm_id");
                        DB::connection($this->db)->select("Update dm_trip_detail SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm' WHERE ORDM_ORNM='$ordm_ornm'");

                    }

                    if (count($insert) >= 20) {
                        DB::connection($this->db)->table('dm_trip_master_log')->insertOrIgnore($insert);
                        $insert = [];
                    }
                }
				
      		}
        }

        if (!empty($insert)) {
            DB::connection($this->db)->table('dm_trip_master_log')->insertOrIgnore($insert);
        }

        // if (!empty($insert)) {
        //     foreach (array_chunk($insert,50) as $t)  
        //         {
        //             DB::connection($this->db)->table('dm_trip_master_log')->insert($t);
        //         }
            
        // }
                    
    }








    // public function oldarray(array $array)
    // {
        
    //     $data = $array;
    //     $insert = [];
    //     // dd($data);
    //     foreach ($data as $item) {

    //         $ordm_ornm = $item['coll_number'];
    //         $site_code = $item['site_code'];
    //         $aemp_id = $item['aemp_id'];
    //         $aemp_usnm = $item['aemp_usnm'];
    //         $wh_id = $item['wh_id'];
    //         $slgp_id = $item['slgp_id'];
    //         $sg_code = $item['sg_code'];
			
	// 		$dm_data = DB::connection($this->db)->select("SELECT * FROM `dm_trip_master` WHERE ORDM_ORNM = '$ordm_ornm'")[0];
	// 		$dm_data2 = DB::connection($this->db)->select("SELECT * FROM `dm_trip_detail` WHERE ORDM_ORNM = '$ordm_ornm'")[0];
    //         //  dd($dm_data2);
                        
    //         if ($dm_data) {
	// 		  	if ($dm_data->COLLECTION_AMNT == $dm_data->DELV_AMNT) {
	// 			//   return redirect()->back()->with('warning', ' Already Paid');
	// 			}else{
    //                 $aemp_data = EmployeeManagerUpload::where('aemp_usnm', $aemp_usnm)->first();
    //                 if ($aemp_data) {
    //                     // DB::connection($this->db)->beginTransaction();
    //                     // $log = new DmTripMasterLog();
    //                     // $log->setConnection($this->db);
    //                     // $log->dm_trip_master_id	= $dm_data->ID;
    //                     // $log->ACMP_CODE     	= $dm_data->ACMP_CODE;
    //                     // $log->ORDM_DATE    		= $dm_data->ORDM_DATE;
    //                     // $log->ORDM_ORNM       	= $dm_data->ORDM_ORNM;
    //                     // $log->ORDM_DRDT      	= $dm_data->ORDM_DRDT;
    //                     // $log->AEMP_ID      		= $dm_data2->AEMP_ID;
    //                     // $log->AEMP_USNM     	= $dm_data2->AEMP_USNM;
    //                     // $log->WH_ID       		= $dm_data->WH_ID;
    //                     // $log->SITE_ID       	= $dm_data->SITE_ID;
    //                     // $log->SITE_CODE       	= $dm_data->SITE_CODE;
    //                     // $log->ORDD_AMNT      	= $dm_data->ORDD_AMNT;
    //                     // $log->INV_AMNT       	= $dm_data->INV_AMNT;
    //                     // $log->DELV_AMNT       	= $dm_data->DELV_AMNT;
    //                     // $log->COLLECTION_AMNT 	= $dm_data->COLLECTION_AMNT;
    //                     // $log->IBS_INVOICE     	= $dm_data->IBS_INVOICE;
    //                     // $log->COLL_STATUS     	= $dm_data->COLL_STATUS;
    //                     // $log->DELIVERY_STATUS 	= $dm_data->DELIVERY_STATUS;
    //                     // $log->slgp_id       	= $dm_data->slgp_id;
    //                     // $log->created_by    	= $this->currentUser->id;
    //                     // $log->save();

    //                     $ordm_ornm = $dm_data->ORDM_ORNM;
                        
    //                     // DB::connection($this->db)->select("Update dm_trip_master SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm',slgp_id='$slgp_id' WHERE ID=$dm_id");
    //                     DB::connection($this->db)->select("Update dm_trip_detail SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm' WHERE ORDM_ORNM='$ordm_ornm'");

    //                     DB::connection($this->db)->commit();
    //                 }
    //             }
				
    //   		}
    //     }
                    
    // }

}
