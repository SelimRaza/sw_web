<?php

namespace App\Http\Controllers\Collection;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */

use Excel;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use Illuminate\Support\Facades\DB;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\SiteBalance;
use App\MasterData\DmCollectionLog;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use App\Mapping\EmployeeManagerUpload;
use App\BusinessObject\OutletCollection;
use App\BusinessObject\OutletCollectionTracking;
use Illuminate\Support\Facades\Validator;
use App\GPCollection\DMTripMaster;
use App\GPCollection\InvoiceMap;
use App\GPCollection\DMCollection;

class GPCollectionController extends Controller
{
  private $access_key = 'gp_collection';
  private $currentUser;
  private $userMenu;
  private $db;

  public function __construct()
  {
    $this->middleware('timezone');
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->currentUser = Auth::user();
      $this->db = Auth::user()->country()->cont_conn;
      $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
      if ($subMenu != null) {
        $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
      } else {
        $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
      }
      return $next($request);
    });
  }
  public function index(){
    if ($this->userMenu->wsmu_vsbl) {
      $slgp_list=DB::connection($this->db)->select("Select id,slgp_code,slgp_name FROM tm_slgp ORDER BY slgp_code ASC");
      $dlrm_list=DB::connection($this->db)->select("Select id,dlrm_code,dlrm_name FROM tm_dlrm ORDER BY dlrm_code ASC");
      return view('collection.MY.index',['slgp_list'=>$slgp_list,'dlrm_list'=>$dlrm_list])->with('permission', $this->userMenu);
    } else {
      return view('theme.access_limit');
    }
  }
  public function getGroupPartiesDuesInvoice(Request $request){
    if ($this->userMenu->wsmu_vsbl) {
        $ms_code=$request->ms_code;
        $collection_data=array();
        $data=DB::connection($this->db)->select("SELECT t2.ID,t2.ORDM_DATE,t2.SITE_ID,
              t2.SITE_CODE,t3.site_name SITE_NAME,t2.ORDM_ORNM,t2.ORDD_AMNT,t2.INV_AMNT,t2.DELV_AMNT,t2.COLLECTION_AMNT,t2.DM_CODE,t2.IBS_INVOICE,t2.TRIP_NO,t2.V_NAME,ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2)DUES
              FROM tl_site_party_mapping t1
              INNER JOIN dm_trip_master t2 ON t1.site_code=t2.SITE_CODE
              INNER JOIN tm_site t3 ON t2.SITE_ID=t3.id
              WHERE t2.DELIVERY_STATUS=11 AND  t2.DELV_AMNT-t2.COLLECTION_AMNT>0.9 AND t1.mother_site_code='$ms_code'     
              ORDER BY  t2.SITE_CODE ASC;      
              ");
                            // UNION ALL
                            // SELECT t2.ID,t2.ORDM_DATE,t2.SITE_ID,
                            // t2.SITE_CODE,t3.site_name SITE_NAME,t2.ORDM_ORNM,t2.ORDD_AMNT,t2.INV_AMNT,t2.DELV_AMNT,t2.COLLECTION_AMNT,t2.DM_CODE,t2.IBS_INVOICE,t2.TRIP_NO,t2.V_NAME,ROUND(t2.DELV_AMNT-t2.COLLECTION_AMNT,2)DUES
                            // FROM tl_site_party_mapping_spro t1
                            // INNER JOIN dm_trip_master t2 ON t1.site_code=t2.SITE_CODE
                            // INNER JOIN tm_site t3 ON t2.SITE_ID=t3.id
                            // WHERE t2.DELIVERY_STATUS=11 AND  t2.DELV_AMNT-t2.COLLECTION_AMNT>0.9 AND t1.mother_site_code='$ms_code'  
        // $siteIds = collect($data)->pluck('SITE_ID')->unique()->toArray();
        // for($i=0;$i<count($data);$i++){
        //         $grv_data=DB::connection($this->db)->select("SELECT * FROM dm_collection_test WHERE  COLLECTION_TYPE='GRV' AND STATUS=11 AND SITE_ID={$data[$i]->SITE_ID} AND INVT_ID=5");
        //         $cn_data=DB::connection($this->db)->select("SELECT * FROM dm_collection WHERE  COLLECTION_TYPE='CN' AND STATUS=11 AND SITE_ID={$data[$i]->SITE_ID} AND INVT_ID=5");
        //         $single_invoice=array(
        //           'ID'=>$data[$i]->ID,
        //           'ORDM_DATE'=>$data[$i]->ORDM_DATE,
        //           'SITE_ID'=>$data[$i]->SITE_ID,
        //           'SITE_ID'=>$data[$i]->SITE_ID,
        //           'SITE_CODE'=>$data[$i]->SITE_CODE,
        //           'SITE_NAME'=>$data[$i]->SITE_NAME,
        //           'ORDM_ORNM'=>$data[$i]->ORDM_ORNM,
        //           'ORDD_AMNT'=>$data[$i]->ORDD_AMNT,
        //           'INV_AMNT'=>$data[$i]->INV_AMNT,
        //           'DELV_AMNT'=>$data[$i]->DELV_AMNT,
        //           'COLLECTION_AMNT'=>$data[$i]->COLLECTION_AMNT,
        //           'DM_CODE'=>$data[$i]->DM_CODE,
        //           'IBS_INVOICE'=>$data[$i]->IBS_INVOICE,
        //           'TRIP_NO'=>$data[$i]->TRIP_NO,
        //           'V_NAME'=>$data[$i]->V_NAME,
        //           'DUES'=>$data[$i]->DUES,
        //           'cn_list'=>$cn_data,
        //           'grv_list'=>$grv_data
        //         );
        //         array_push($collection_data,$single_invoice);
        // }
        //return $query;
        $result = collect($data);
        $site_list = $result->pluck('SITE_ID')->unique()->toArray();
        
        // Handle the case when $site_list is empty
        if (!empty($site_list)) {
            $grv_data = DB::connection($this->db)->select("SELECT ID,COLL_NUMBER,COLLECTION_AMNT,COLL_REC_HO,COLL_DATE,SITE_ID,SITE_CODE FROM dm_collection WHERE COLLECTION_TYPE='GRV' AND STATUS=11 AND INVT_ID=5 AND SITE_ID IN (" . implode(',', $site_list) . ")");
            $cn_data = DB::connection($this->db)->select("SELECT ID,COLL_NUMBER,COLLECTION_AMNT,COLL_REC_HO,COLL_DATE,SITE_ID,SITE_CODE FROM dm_collection WHERE COLLECTION_TYPE='CN' AND STATUS=11 AND INVT_ID=5 AND SITE_ID IN (" . implode(',', $site_list) . ")");
        } else {
            // Handle the scenario when $site_list is empty
            $grv_data = [];
            $cn_data = [];
        }
        $cn=collect($cn_data);
        $grv=collect($grv_data);
        $total_dues=$result->pipe(function($result){
            return collect([
                'total_dues'=>round($result->sum('DUES'),2),
            ]);                       
        });
        $total_cn=$cn->pipe(function($result){
          return collect([
              'total_cn'=>round($result->sum('COLLECTION_AMNT'),2),
          ]);                       
        });
        $total_grv=$grv->pipe(function($result){
          return collect([
              'total_grv'=>round($result->sum('COLLECTION_AMNT'),2),
          ]);                       
        });
      $cn_grv=round($total_cn['total_cn']+$total_grv['total_grv'],2);
      $net_payable=round($total_dues['total_dues']-$cn_grv,2);
      //$net_payable=$net_payable<0?0:$net_payable;
      return array(
        'data'=>$data,
        'total_dues'=>$total_dues,
        'collection_data'=>$site_list,
        'grv_data'=>$grv_data,
        'cn_data'=>$cn_data,
        'total_cn'=>$total_cn,
        'total_grv'=>$total_grv,
        'cn_grv'=>$cn_grv,
        'net_payable'=>$net_payable
      );
    } else {
      return view('theme.access_limit');
    }
  }

  public function storeMatchedCollection(Request $request){
      //return $request->all();
      $dt = date('Ymd-Hi');
      $now = now();
      $unique_number    = substr($now->timestamp . $now->milli, 10);
      $coll_number      = "CL" . $request->coll_type . $dt . '-' . $unique_number;
      $cn_number        = "CN" . $request->coll_type . $dt . '-' . $unique_number;
      $dn_number        = "DN" . $request->coll_type . $dt .'-'.$unique_number;
      // Parameters
      $amnt=$request->amnt;
      $chq_no=$request->chq_no;
      $cn=$request->cn;
      $coll_type=$request->coll_type;
      $dlrm_code=$request->dlrm_code;
      $ms_code=$request->ms_code;
      $slgp_id=$request->slgp_id;
      $data=$request->data;
      $validator = Validator::make($request->all(), [
        'amnt' => 'required|numeric',
        'coll_type' => 'required|numeric',
        'dlrm_code' => 'required|string',
        'ms_code' => 'required|string',
        'slgp_id' => 'required|numeric',
        'data' => 'required|array'
      ]);
      if ($validator->fails()) {
          return response()->json([
              'success' => false,
              'errors' => $validator->errors()->all()
          ]);
      }
      $coll_type_name='';
      $chq_number='N';
      $bank_name='';
      $chq_date='';
      $aemp_id=Auth::user()->employee()->id;
      $aemp_usnm=Auth::user()->employee()->aemp_usnm;
      if($coll_type==2){
        $coll_type_name='Cash';
      }
      else if($coll_type==3){
        $coll_type_name='Cheque';
        $chq_number=$request->chq_no;
        $bank_name=$request->bank_name;
        $chq_date=$request->chq_date;
      }
      else if($coll_type==4){
        $coll_type_name='Online';
      }
      $acmp_code=$this->getAcmpCode($slgp_id);
      //return $request->all();
      DB::connection($this->db)->beginTransaction();
      try{
          // DM COLLECTION
          $dm_collection=new DMCollection();
          $dm_collection->setConnection($this->db);
          $dm_collection->create([
            'ACMP_CODE' =>$acmp_code,
            'COLL_DATE' => date('Y-m-d'),
            'COLL_NUMBER' =>$coll_number,
            'IBS_INVOICE' => '',
            'AEMP_ID' =>$aemp_id,
            'AEMP_USNM' => $aemp_usnm,
            'DM_CODE' =>$aemp_usnm,
            'WH_ID' =>$dlrm_code,
            'SITE_ID' =>$ms_code,
            'SITE_CODE' => $ms_code,
            'COLLECTION_AMNT' =>$amnt,
            'COLL_REC_HO' => 0,
            'COLLECTION_TYPE' => $coll_type_name,
            'STATUS' =>26,
            'CHECK_NUMBER' =>$chq_number,
            'INVT_ID' => 1,
            'CHECK_IMAGE' => '',
            'BANK_NAME' =>$bank_name,
            'CHECK_DATE' =>$chq_date,
            'slgp_id' =>$slgp_id,
            'sync_status' =>0,
            'stcm_sync' => 0,
            'COLL_NOTE' => 'N'
        ]);
        if($cn>0){
          $cn_obj=new DMCollection();
          $cn_obj->setConnection($this->db);
          $cn_obj->create([
            'ACMP_CODE' =>$acmp_code,
            'COLL_DATE' => date('Y-m-d'),
            'COLL_NUMBER' =>$cn_number,
            'IBS_INVOICE' => '',
            'AEMP_ID' =>$aemp_id,
            'AEMP_USNM' => $aemp_usnm,
            'DM_CODE' =>$aemp_usnm,
            'WH_ID' =>$dlrm_code,
            'SITE_ID' =>$ms_code,
            'SITE_CODE' => $ms_code,
            'COLLECTION_AMNT' =>$cn,
            'COLL_REC_HO' => 0,
            'COLLECTION_TYPE' =>'CN',
            'STATUS' =>40,
            'CHECK_NUMBER' =>$chq_number,
            'INVT_ID' => 19,
            'CHECK_IMAGE' => '',
            'BANK_NAME' =>$bank_name,
            'CHECK_DATE' =>$chq_date,
            'slgp_id' =>$slgp_id,
            'sync_status' =>0,
            'stcm_sync' => 0,
            'COLL_NOTE' => 'N'
          ]);
          $dn=new DMCollection();
          $dn->setConnection($this->db);
          $dn->create([
            'ACMP_CODE' =>$acmp_code,
            'COLL_DATE' => date('Y-m-d'),
            'COLL_NUMBER' =>$dn_number,
            'IBS_INVOICE' => '',
            'AEMP_ID' =>$aemp_id,
            'AEMP_USNM' => $aemp_usnm,
            'DM_CODE' =>$aemp_usnm,
            'WH_ID' =>$dlrm_code,
            'SITE_ID' => 'N',
            'SITE_CODE' => $ms_code,
            'COLLECTION_AMNT' =>-$cn,
            'COLL_REC_HO' => 0,
            'COLLECTION_TYPE' =>'DN',
            'STATUS' =>41,
            'CHECK_NUMBER' =>$chq_number,
            'INVT_ID' => 5,
            'CHECK_IMAGE' => '',
            'BANK_NAME' =>$bank_name,
            'CHECK_DATE' =>$chq_date,
            'slgp_id' =>$slgp_id,
            'sync_staus' =>0,
            'stcm_sync' => 0,
            'COLL_NOTE' => 'N'
          ]);
          $invoiceMap_dn = new InvoiceMap();
          $invoiceMap_dn->setConnection($this->db);
          $invoiceMap_dn->create([
              'ACMP_CODE' =>$acmp_code,
              'TRN_DATE' => date('Y-m-d'),
              'MAP_ID' =>$coll_number,
              'TRANSACTION_ID' =>$dn_number,
              'SITE_ID' => $ms_code,
              'SITE_CODE' =>$ms_code,
              'DEBIT_AMNT' =>0,
              'CRECIT_AMNT' =>$cn,
              'DELV_AMNT' => 0,
              'sync_staus' =>0,
              'stcm_sync' =>0
          ]);
        }
        $invoiceMap = new InvoiceMap();
        $invoiceMap->setConnection($this->db);
        $invoiceMap->create([
            'ACMP_CODE' =>$acmp_code,
            'TRN_DATE' => date('Y-m-d'),
            'MAP_ID' =>$coll_number,
            'TRANSACTION_ID' =>$coll_number,
            'SITE_ID' => $ms_code,
            'SITE_CODE' =>$ms_code,
            'DEBIT_AMNT' =>$amnt,
            'CRECIT_AMNT' => 0,
            'DELV_AMNT' => 0,
            'sync_staus' =>0,
            'stcm_sync' =>0
        ]);


        foreach ($data as $item) {
          // DM TRIP MASTER
          // $dm_trip_master=DMTripMaster::on($this->db)->find($item['id']);
          // $dm_trip_master->COLLECTION_AMNT=$dm_trip_master->COLLECTION_AMNT+$item['coll_amnt'];
          // $dm_trip_master->save();
          DB::connection($this->db)->table('dm_trip_master')
          ->where('id', $item['id'])
          ->update(['COLLECTION_AMNT' => DB::raw('COLLECTION_AMNT + ' . $item['coll_amnt'])]);


          // INVOICE COLLECTION MAP
          $invoiceMap_dtls = new InvoiceMap();
          $invoiceMap_dtls->setConnection($this->db);
          $invoiceMap_dtls->create([
              'ACMP_CODE' =>$acmp_code,
              'TRN_DATE' => date('Y-m-d'),
              'MAP_ID' =>$coll_number,
              'TRANSACTION_ID' => $item['ordm_ornm'],
              'SITE_ID' =>$ms_code,
              'SITE_CODE' => $ms_code,
              'DEBIT_AMNT' =>0,
              'CRECIT_AMNT' =>$item['coll_amnt'],
              'DELV_AMNT' => 0,
              'sync_staus' =>0,
              'stcm_sync' =>0
          ]);
        }
        // CN  MAPPING INTO INVOICE MAP TABLE
        if($request->c1>0){
          $cn_list=$request->cn_list;
          foreach ($cn_list as $item) {
            $invoiceMap_dtls = new InvoiceMap();
            $invoiceMap_dtls->setConnection($this->db);
            $invoiceMap_dtls->create([
                'ACMP_CODE' =>$acmp_code,
                'TRN_DATE' => date('Y-m-d'),
                'MAP_ID' =>$coll_number,
                'TRANSACTION_ID' => $item['cn_number'],
                'SITE_ID' =>$ms_code,
                'SITE_CODE' => $ms_code,
                'DEBIT_AMNT' =>$item['coll_cn'],
                'CRECIT_AMNT' =>0,
                'DELV_AMNT' => 0,
                'sync_staus' =>0,
                'stcm_sync' =>0
            ]);
            // $cl_cn=DMCollection::on($this->db)->where(['ID'=>$item['id'] ])->first();
            // $cl_cn->STATUS=26;
            // $cl_cn->COLL_REC_HO=$item['coll_cn'];
            // $cl_cn->save();
            DB::connection($this->db)->table('dm_collection')
            ->where('id', $item['id'])
            ->update(['STATUS' =>26,'COLL_REC_HO'=>$item['coll_cn']]);
          }
        }
        // GRV  MAPPING INTO INVOICE MAP TABLE
        if($request->g1>0){
          $grv_list=$request->grv_list;
          foreach ($grv_list as $item) {
            $invoiceMap_dtls = new InvoiceMap();
            $invoiceMap_dtls->setConnection($this->db);
            $invoiceMap_dtls->create([
                'ACMP_CODE' =>$acmp_code,
                'TRN_DATE' => date('Y-m-d'),
                'MAP_ID' =>$coll_number,
                'TRANSACTION_ID' => $item['grv_number'],
                'SITE_ID' =>$ms_code,
                'SITE_CODE' => $ms_code,
                'DEBIT_AMNT' =>$item['coll_grv'],
                'CRECIT_AMNT' =>0,
                'DELV_AMNT' => 0,
                'sync_staus' =>0,
                'stcm_sync' =>0
            ]);
            // $cl_grv=DMCollection::on($this->db)->where(['ID'=>$item['id'] ])->first();
            // $cl_grv->STATUS=26;
            // $cl_grv->COLL_REC_HO=$item['coll_grv'];
            // $cl_grv->save();
            DB::connection($this->db)->table('dm_collection')
            ->where('id', $item['id'])
            ->update(['STATUS' =>26,'COLL_REC_HO'=>$item['coll_grv']]);
          }
        }

        DB::connection($this->db)->commit();
        return response()->json([
          'success' => true,
          'message' => 'DONE'
      ]);
      }
      catch(\Exception $e){
        DB::connection($this->db)->rollback();
        return response()->json([
          'success' => false,
          'message' =>$e->getMessage()
      ]);
      }


  }

  public function getAcmpCode($slgp_id){
     $data=DB::connection($this->db)->select("SELECT 
            t2.acmp_code
            FROM tm_slgp t1
            INNER JOIN tm_acmp t2 ON t1.acmp_id=t2.id
            WHERE t1.id={$slgp_id}");
    return $data[0]->acmp_code;
  }

}
