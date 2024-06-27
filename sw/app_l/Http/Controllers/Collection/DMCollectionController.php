<?php

namespace App\Http\Controllers\Collection;

use Excel;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Site;
use App\MasterData\Depot;

use App\MasterData\Employee;
use Illuminate\Http\Request;

use App\MasterData\DmCollection;
use Illuminate\Support\Facades\DB;
use App\MasterData\DmCollectionLog;
use App\MasterData\DmTripMasterLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Mapping\EmployeeManagerUpload;
use App\MasterData\DmCollectionCnUpload;
use App\MasterData\DmTransferUserGroup;

class DMCollectionController extends Controller
{
  private $access_key = 'CollectionController';
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
      return view('collection.Trip.index',['slgpList'=>$slgp_list])->with('permission', $this->userMenu);
    } else {
      return view('theme.access_limit');
    }
  }

  public function dmTripFilter(Request $request)
  {

    $where = '';
    if (isset($request->aemp_code)) {
      $where .= "AEMP_USNM = '$request->aemp_code'";
    }
    if ($request->aemp_code != '') {
      if (isset($request->site_code)) {
		  $where .= " AND SITE_CODE = '$request->site_code'";
		} 
    }else {
		$where = "SITE_CODE = '$request->site_code'";
    }

    if ($request->aemp_code != '' || $request->site_code != '') {
      	if (isset($request->slgp_id)) {
		  	$where .= " AND slgp_id = '$request->slgp_id'";
		} 
    }
    else {
		$where = "slgp_id = $request->slgp_id";
    }

    // $where .= " AND DELV_AMNT - COLLECTION_AMNT > 0 ";
    // $where .= " AND COALESCE(DELV_AMNT, 0) - COALESCE(COLLECTION_AMNT, 0) > 0 AND DELV_AMNT = ROUND(DELV_AMNT, 2) AND COLLECTION_AMNT = ROUND(COLLECTION_AMNT, 2) ";
    $where .= " AND COALESCE(DELV_AMNT, 0) - COALESCE(COLLECTION_AMNT, 0) > 0 ";

    // $query = "SELECT * FROM dm_trip_master   WHERE $where ORDER BY id DESC LIMIT 100";
      $query = "SELECT t1.ID as edit_id, t1.AEMP_USNM, t1.slgp_id, t1.SITE_CODE, t1.SLGP_ID, t1.ORDM_DATE, 
			t1.ORDM_ORNM, t1.DELV_AMNT, t1.COLLECTION_AMNT, t2.id, t2.slgp_name, t2.slgp_code
			FROM dm_trip_master AS t1 INNER JOIN tm_slgp AS t2 ON t1.slgp_id = t2.id   WHERE $where ORDER BY id DESC LIMIT 200";

    DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
    $data = DB::connection($this->db)->select(DB::raw($query));

    return $data;
  }

  public function edit($id)
  {
    $data = DB::connection($this->db)->select("SELECT * FROM `dm_trip_master` WHERE id = '$id'")[0];
    $slgpList = DB::connection($this->db)->select("SELECT `slgp_name`, `slgp_code`, `id` FROM `tm_slgp`");
    // dd($data);
    return view('collection.Trip.edit', ['data' => $data, 'slgpList' => $slgpList]);
  }

  	public function update(Request $request)
  	{
	  	try {
			$dm_id = $request->dm_id;
			$slgp_id = $request->slgp_id;
			$aemp_usnm = $request->aemp_usnm;
			$site_code = $request->site_code;
			
			$dm_data = DB::connection($this->db)->select("SELECT * FROM `dm_trip_master` WHERE ID = '$dm_id'")[0];
			
		  
		  	if ($dm_data) {
			  	if ($dm_data->COLLECTION_AMNT == $dm_data->DELV_AMNT) {
				  return redirect()->back()->with('warning', ' Already Paid');
				}
				$aemp_data = EmployeeManagerUpload::where('aemp_usnm', $aemp_usnm)->first();
				if ($aemp_data) {
					DB::connection($this->db)->beginTransaction();
					$log = new DmTripMasterLog();
					$log->dm_trip_master_id	= $dm_data->ID;
					$log->ACMP_CODE     	= $dm_data->ACMP_CODE;
					$log->ORDM_DATE    		= $dm_data->ORDM_DATE;
					$log->ORDM_ORNM       	= $dm_data->ORDM_ORNM;
					$log->ORDM_DRDT      	= $dm_data->ORDM_DRDT;
					$log->AEMP_ID      		= $dm_data->AEMP_ID;
					$log->AEMP_USNM     	= $dm_data->AEMP_USNM;
					$log->WH_ID       		= $dm_data->WH_ID;
					$log->SITE_ID       	= $dm_data->SITE_ID;
					$log->SITE_CODE       	= $dm_data->SITE_CODE;
					$log->ORDD_AMNT      	= $dm_data->ORDD_AMNT;
					$log->INV_AMNT       	= $dm_data->INV_AMNT;
					$log->DELV_AMNT       	= $dm_data->DELV_AMNT;
					$log->COLLECTION_AMNT 	= $dm_data->COLLECTION_AMNT;
					$log->IBS_INVOICE     	= $dm_data->IBS_INVOICE;
					$log->COLL_STATUS     	= $dm_data->COLL_STATUS;
					$log->DELIVERY_STATUS 	= $dm_data->DELIVERY_STATUS;
					$log->slgp_id       	= $dm_data->slgp_id;
					$log->created_by    	= $this->currentUser->id;
					$log->save();

          			$ordm_ornm = $dm_data->ORDM_ORNM;
					
					DB::connection($this->db)->select("Update dm_trip_master SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm',slgp_id='$request->slgp_id' WHERE ID=$dm_id");
					DB::connection($this->db)->select("Update dm_trip_detail SET AEMP_ID='$aemp_data->id',AEMP_USNM='$aemp_data->aemp_usnm' WHERE ORDM_ORNM='$ordm_ornm'");

					DB::connection($this->db)->commit();
        		} else {
          			return redirect()->back()->with('danger', 'AEMP not found!!!!');
        		}
      		}
      		return redirect()->back()->with('success', ' Successfully Updated');
    	} catch (\Exception $e) {
      		return $e->getMessage();
      		return redirect()->back()->with('danger', ' Something Went wrong !!!!');
   	 	}
  	}

    public function rentalAdjustment()
    {

      if ($this->userMenu->wsmu_vsbl) {
        $slgp_list=DB::connection($this->db)->select("Select id,slgp_code,slgp_name FROM tm_slgp ORDER BY slgp_code ASC");
        $dlrm_list=DB::connection($this->db)->select("Select id,dlrm_name,dlrm_code FROM tm_dlrm ORDER BY dlrm_code ASC");
        $accounts_head=DB::connection($this->db)->select("Select id,ACCOUNT_NAME,ACCOUNT_CODE FROM ACCOUNTS_CODE ORDER BY id");
        return view('rentalAdjust.index',['slgpList'=>$slgp_list, 'dlrm_list' => $dlrm_list, 'accounts_head' => $accounts_head])->with('permission', $this->userMenu);
      } else {
        return view('theme.access_limit');
      }
    }

    public function rentalAdjustmentStore(Request $request)
    {
		$validatedData = $request->validate([
			'aemp_usnm' => 'required|max:255',
			'site_code' => 'required|max:255',
			'collection_amt' => 'required|gt:0',
			'slgp_id' => 'required|max:255',
			'account_code' => 'required',
			'wh_id' => 'required',
			'coll_note' => 'max:250',
		], [
			'aemp_usnm.required' => 'The aemp usnm is required.',
			'site_code.required' => 'The site code is required.',
			'collection_amt.required' => 'The collection amount is required.',
			'slgp_id.required' => 'The sales group is required.',
			'account_code.required' => 'The account code is required.',
			'wh_id.required' => 'The ware house is required.',
			'coll_note.max' => 'The collection note may not be greater than 250 characters.',
			'collection_amt.gt' => 'The collection amount must be greater than 0.',
		]);
		
		try {
			
			$employee = Employee::on($this->db)->where('aemp_usnm', $request->aemp_usnm)->first();
			$site = Site::on($this->db)->where('site_code', $request->site_code)->first();

			// dd($employee, $site);

			$dt = date('Ymd-Hi');
 			$coll_type = '5';
                    
 			$now = now();
			$last_3 = substr($now->timestamp . $now->milli, 10);
			$CN_NUMBER = "CN" . $coll_type . $dt . '-' . $last_3;

			if($employee != null && $site != null){
				DB::connection($this->db)->beginTransaction();
				$data = new DmCollection();
				$data->setConnection($this->db);
				$data->ACMP_CODE = '01';
				$data->COLL_DATE = date("Y-m-d");
				$data->COLL_NUMBER = $CN_NUMBER;
				$data->IBS_INVOICE = "N";
				$data->AEMP_ID = $employee->id;
				$data->AEMP_USNM = $request->aemp_usnm;
				$data->DM_CODE = $request->aemp_usnm;
				$data->WH_ID = $request->wh_id;
				$data->SITE_ID = $site->id;
				$data->SITE_CODE = $request->site_code;
				$data->COLLECTION_AMNT = floatval($request->collection_amt);
				$data->COLLECTION_TYPE = "CN";
				$data->STATUS = 11;
				$data->INVT_ID = 5;
				$data->slgp_id = $request->slgp_id;
				$data->BANK_NAME = $request->account_code;
				$data->COLL_NOTE = $request->coll_note;
				$data->save();
				DB::connection($this->db)->commit();
				
			}else{
				return redirect()->back()->with('danger', ' Employee or site code not found !!!!');
			}
			return redirect()->back()->with('success', ' Successfully Added');
		} catch (\Throwable $th) {
			DB::rollback();
			return redirect()->back()->with('danger', ' Something Went wrong !!!!');
		}
      
      

    }

	public function rentalAdjustmentUpload()
    {
        return view('rentalAdjust.file-upload')->with('permission', $this->userMenu);
    }
	public function rentalAdjustmentUploadStore(Request $request)
    {
		if ($request->hasFile('import_file')) {
			DB::beginTransaction();
            try {
				Excel::import(new DmCollectionCnUpload(), $request->file('import_file'));
                DB::commit();
                return redirect()->back()->with('success', 'Successfully Uploaded');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('danger', ' Wrong data provided '.$e->getMessage());
            }
        }
        return back()->with('danger', ' File Not Found');
    }
	public function rentalAdjustmentUploadDownload()
    {
		return Excel::download(new DmCollectionCnUpload(), 'rental_adjustment' . date("Y-m-d H:i:s") . '.xlsx');
    }

	public function rentalAdjustmentUploadFilter(Request $request)
	{

		$coll_no = $request->coll_no;
		$coll_date = $request->coll_date;
		$slgp_id = $request->slgp_id;
		$dm_code = $request->dm_code;
		$site_code = $request->site_code;

		$where = '';
		
		if (isset($request->sr_code)) {
			$where = "AEMP_USNM = '$request->sr_code'";
		}
		
		if ($request->sr_code == '') {
			if(isset($request->coll_no)){
				$where = "COLL_NUMBER = '$request->coll_no'";
			}		
		}else {
			if(isset($request->coll_no)){
				$where .= " AND COLL_NUMBER = '$request->coll_no'";
			}
		}

		if ($request->coll_no == '' && $request->sr_code == '') {
		if (isset($request->site_code)) {
			$where = " SITE_CODE = '$request->site_code'";
		}
		}else {
			if(isset($request->site_code)){
				$where .= " AND SITE_CODE = '$request->site_code'";
			}
		}

		if ($request->coll_no == '' && $request->sr_code == '' && $request->site_code == '') {
		if (isset($request->slgp_id)) {
			$where = "slgp_id = $request->slgp_id";
		} 
		}else {
			if(isset($request->slgp_id)){
				$where .= " AND slgp_id = $request->slgp_id";
			}
		}
		

		// $where .= "AND COLLECTION_TYPE IN('CN','DN') AND (INVT_ID = 5 OR INVT_ID = 15)";
		$where .= " AND COLLECTION_TYPE IN('CN') AND (INVT_ID = 5)";
		$where .= " AND COLL_REC_HO = 0 ";

		$query = "SELECT * FROM dm_collection  WHERE $where ORDER BY id DESC LIMIT 500";

		DB::connection($this->db)->select(DB::raw("SET sql_mode=''"));
		$data = DB::connection($this->db)->select(DB::raw($query));

		return $data;
	}

	public function rentalAdjustmentUploadEdit($id)
	{
		$data = DB::connection($this->db)->select("SELECT * FROM `dm_collection` WHERE id = '$id'")[0];
		$slgpList = DB::connection($this->db)->select("SELECT `slgp_name`, `slgp_code`, `id` FROM `tm_slgp`");
		return view('rentalAdjust.edit', ['data' => $data, 'slgpList' => $slgpList]);
	}
	public function rentalAdjustmentUploadUpdate(Request $request)
	{
		{
			$validatedData = $request->validate([
				'coll_amt' => 'required|gt:0',
			], [
				'coll_amt.required' => 'The collection amount is required.',
				'coll_amt.gt' => 'The collection amount must be greater than 0.',
			]);

			try {
				DB::beginTransaction();
				$dm_id = $request->dm_id;
				$dm_collection = DB::connection($this->db)->select("SELECT * FROM `dm_collection` WHERE id = '$dm_id'")[0];

				// if($request->coll_amt > $dm_collection->COLLECTION_AMNT){
				// 	return redirect()->back()->with('danger', ' The collection amount must be less or equal than existing amount.');
				// }

				$log = new DmCollectionLog();
				$log->COLL_DATE     = $dm_collection->COLL_DATE;
				$log->COLL_NUMBER   = $dm_collection->COLL_NUMBER;
				$log->AEMP_ID       = $dm_collection->AEMP_ID;
				$log->AEMP_USNM     = $dm_collection->AEMP_USNM;
				$log->COLLECTION_AMNT = $dm_collection->COLLECTION_AMNT;
				$log->COLL_REC_HO   = $dm_collection->COLL_REC_HO;
				$log->STATUS        = $dm_collection->STATUS;
				$log->slgp_id       = $dm_collection->slgp_id;
				$log->COLL_NOTE     = 'Collection amount updated from Rental Adjustment';
				$log->created_by    = $this->currentUser->id;
				$log->save();

				DB::connection($this->db)->select("Update dm_collection SET COLLECTION_AMNT='$request->coll_amt' WHERE ID=$dm_id");
				
				DB::commit();
				return redirect()->back()->with('success', ' Successfully Updated');
			} catch (\Throwable $th) {
				DB::rollback();
				return redirect()->back()->with('danger', ' Something Went wrong !!!!'.$th);
			}
		}
	}


  	public function transferUserGroupFileUpload()
  	{
    	return view('collection.Trip.transfer-user-group-file-upload');
  	}

	public function transferUserGroupFileUploadStore(Request $request)
    {
		if ($request->hasFile('user_group_transfer_file')) {
			DB::connection($this->db)->beginTransaction();
            try {
				Excel::import(new DmTransferUserGroup(), $request->file('user_group_transfer_file'));
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', ' Successfully Uploaded');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return redirect()->back()->with('danger', ' Wrong data provided '.$e->getMessage());
            }
        }
        return back()->with('danger', ' File Not Found');
    }
	public function transferUserGroupFileDownload()
    {
		return Excel::download(new DmTransferUserGroup(), 'transfer-user-group-' . date("Y-m-d H:i:s") . '.xlsx');
    }

  
}
