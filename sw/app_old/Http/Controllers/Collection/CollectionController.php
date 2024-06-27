<?php

namespace App\Http\Controllers\Collection;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OutletCollection;
use App\BusinessObject\OutletCollectionTracking;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SiteBalance;
use App\MasterData\Country;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
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

    public function maintainCollection()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('collection.collection_maintain')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function verify($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $collection = collect(DB::connection($this->db)->select("SELECT
  t1.id                                                             AS payment_id, 
  t1.cltn_code                                                      AS payment_code,
  concat(t4.aemp_usnm, '-', t4.aemp_name, '(', t1.cltn_ctme, ')')   AS created_by,
  concat(t5.aemp_usnm, '-', t5.aemp_name, '(', t1.cltn_vtme, ')')   AS verified_by,
  concat(t10.aemp_usnm, '-', t10.aemp_name, '(', t1.cltn_mtme, ')') AS match_by,
  concat(t11.aemp_usnm, '-', t11.aemp_name, '(', t1.cltn_qtme, ')') AS cheque_paid_by,
  concat(t12.aemp_usnm, '-', t12.aemp_name, '(', t1.cltn_ytme, ')') AS carrying_by,
  t6.acmp_name                                                      AS ou_name,
  t1.cltn_note                                                      AS note,
  t1.cltn_date                                                      AS date,
  t1.cltn_ctme                                                      AS created_date,
  t1.cltn_amnt                                                      AS amount,
  t1.cltn_mamt                                                      AS allocated_amount,
  t2.lfcl_name                                                      AS status,
  concat(t1.oult_id, '-', t3.oult_name)                             AS outlet_name,
  t7.clpt_name                                                      AS payment_type,
  t1.clpt_id                                                        AS clpt_id,
  t8.clmt_name                                                      AS collection_type,
  t1.cltn_chqn                                                      AS cheque_no,
  t1.cltn_cdat                                                      AS cheque_date,
  t9.bank_name                                                      AS bank_name,
  t13.clrj_name                                                     AS reject_reason,
  t1.cltn_rnte                                                      AS reject_note,
  t1.lfcl_id
FROM tt_cltn AS t1
  INNER JOIN tm_lfcl AS t2 ON t1.lfcl_id = t2.id
  INNER JOIN tm_oult AS t3 ON t1.oult_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_cusr = t4.id
  LEFT JOIN tm_aemp AS t5 ON t1.aemp_vusr = t5.id
  INNER JOIN tm_acmp AS t6 ON t1.acmp_id = t6.id
  INNER JOIN tm_clpt AS t7 ON t1.clpt_id = t7.id
  INNER JOIN tm_clmt AS t8 ON t1.clmt_id = t8.id
  LEFT JOIN tm_bank AS t9 ON t1.bank_id = t9.id
  LEFT JOIN tm_aemp AS t10 ON t1.aemp_musr = t10.id
  LEFT JOIN tm_aemp AS t11 ON t1.aemp_qusr = t11.id
  LEFT JOIN tm_aemp AS t12 ON t1.aemp_yusr = t12.id
  LEFT JOIN tm_clrj AS t13 ON t1.clrj_id = t13.id
WHERE t1.id = $id"))->first();
            $chequeImage = DB::connection($this->db)->select("SELECT t2.clig_qimg as image_name
FROM tt_cltn AS t1
  INNER JOIN tm_clig AS t2 ON t1.id = t2.cltn_id
WHERE t1.id = $id");
            $time = date('Y-m-d h:i:s');
            $chequeHistory = DB::connection($this->db)->select("SELECT
  concat(t3.aemp_usnm, '-', t3.aemp_name) AS user,
  t2.cqtr_note                            AS note,
  t2.cqtr_time                            AS date_time
FROM tt_cltn AS t1
  INNER JOIN tt_cqtr AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
WHERE t1.id = $id
ORDER BY t2.cqtr_time DESC");
            $chequeMatching = DB::connection($this->db)->select("SELECT
  t3.invc_date                                        AS date,
  t3.site_id                                          AS site_id,
  t3.invc_code                                        AS invoice_code,
  t3.invc_taxc                                        AS tax_invoice,
  t3.invc_amnt                                        AS invoice_amount,
  t3.invc_mamt                                        AS collection_amount,
  if(t3.trnt_id = 2, -1 * t2.clim_amnt, t2.clim_amnt) AS matching_amount,
  t3.site_id                                          AS site_id,
  t4.site_name                                        AS site_name,
  t3.trnt_id                                          AS transaction_type,
  t5.invt_name                                        AS invoice_type
FROM tt_cltn AS t1
  INNER JOIN tt_clim AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tt_invc AS t3 ON t2.invc_code = t3.invc_code
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tm_invt AS t5 ON t3.invt_id = t5.id
WHERE t1.id = $id");

            $type = 1;
            if ($collection->clpt_id == 1) {
                $type = 2;
            }
            $rejectReason = DB::connection($this->db)->select("SELECT
  t1.clrj_name,
  t1.id
FROM tm_clrj AS t1
WHERE t1.clrj_type = $type;");

            return view('collection.collection_verify')->with('rejectReason', $rejectReason)->with('collection', $collection)->with('chequeImage', $chequeImage)->with('chequeHistory', $chequeHistory)->with('chequeMatching', $chequeMatching)->with('permission', $this->userMenu);

        } else {
            return view('theme.access_limit');
        }

    }

    public function verifyUpdate(Request $value, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            $collection = OutletCollection::on($this->db)->findorfail($id);
            if ($collection->clmt_id == 2) {
                $collection->lfcl_id = 37;
            }
            if ($collection->clmt_id == 1 && $collection->clpt_id == 2) {
                $collection->lfcl_id = 1;
            }
            if ($collection->clmt_id == 1 && $collection->clpt_id != 2) {
                $collection->lfcl_id = 26;
                $collectionPaid = DB::connection($this->db)->select("SELECT
  t2.site_id,
  t1.acmp_id             AS ou_id,
  t1.aemp_cusr            ,
  t1.aemp_susr            ,
  t1.acmp_id            ,
  sum(t2.payment_amount) AS line_amount,
  t3.id           AS sr_id,
  t3.id                  AS emp_code,
  t1.cltn_date           AS date,
  t1.id                  AS collection_id,
  t1.cltn_note           AS collection_note,
  t1.cltn_amnt           AS amount,
  t1.cltn_code           AS collection_code
FROM tt_cltn AS t1
  INNER JOIN (SELECT
                t2.cltn_id        AS collection_id,
                sum(t2.clim_amnt) AS payment_amount,
                t1.site_id
              FROM tt_invc AS t1
                INNER JOIN tt_clim AS t2
                  ON t1.invc_code = t2.invc_code
              WHERE t1.trnt_id = 1 AND t2.cltn_id = $collection->id
              GROUP BY t1.site_id, t2.cltn_id
              UNION ALL
              SELECT
                t2.cltn_id             AS collection_id,
                -1 * sum(t2.clim_amnt) AS payment_amount,
                t1.site_id
              FROM tt_invc AS t1
                INNER JOIN tt_clim AS t2
                  ON t1.invc_code = t2.invc_code
              WHERE t1.trnt_id = 2 AND t2.cltn_id = $collection->id
              GROUP BY t1.site_id, t2.cltn_id
             ) AS t2 ON t1.id = t2.collection_id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_susr = t3.id
WHERE t1.id = $collection->id
GROUP BY t2.site_id, t1.acmp_id,t1.aemp_cusr,t1.aemp_susr,  t3.id, t1.cltn_date, t1.cltn_note, t1.id, t1.cltn_amnt, t1.cltn_code");
                foreach ($collectionPaid as $index => $collectionPaid1) {
                    $invc_code=$collectionPaid1->collection_code;
                    if ($index>0){
                        $invc_code=str_replace('-'.date('y').'-', date('y'). str_pad($index, 2, '0', STR_PAD_LEFT), $collectionPaid1->collection_code);
                    }
                    $dataSiteBalance = new SiteBalance();
                    $dataSiteBalance->setConnection($this->db);
                    $dataSiteBalance->acmp_id = $collectionPaid1->ou_id;
                    $dataSiteBalance->invc_code = $invc_code;
                    $dataSiteBalance->invc_lcod = $collectionPaid1->collection_code;
                    $dataSiteBalance->invc_date = $collectionPaid1->date;
                    $dataSiteBalance->aemp_cusr = $collectionPaid1->aemp_cusr;
                    $dataSiteBalance->aemp_susr = $collectionPaid1->aemp_susr;
                    $dataSiteBalance->invc_note = '';
                    $dataSiteBalance->site_id = $collectionPaid1->site_id;
                    $dataSiteBalance->trip_id = 0;
                    $dataSiteBalance->invc_taxc = '';
                    $dataSiteBalance->invc_amnt = $collectionPaid1->line_amount;
                    $dataSiteBalance->invc_mamt = $collectionPaid1->line_amount;
                    $dataSiteBalance->invt_id = 11;
                    $dataSiteBalance->trnt_id = 2;
                    $dataSiteBalance->invc_gamt = 0;
                    $dataSiteBalance->invc_exca = 0;
                    $dataSiteBalance->invc_vata = 0;
                    $dataSiteBalance->invc_tdis = 0;
                    $dataSiteBalance->invc_ramt = 0;
                    $dataSiteBalance->invc_lock = 0;
                    $dataSiteBalance->invc_icod = '';
                    $dataSiteBalance->invc_vcod = '';
                    $dataSiteBalance->cltn_id = $collectionPaid1->collection_id;
                    $dataSiteBalance->cltn_amnt = $collectionPaid1->amount;
                    $dataSiteBalance->lfcl_id = 26;
                    $dataSiteBalance->cont_id = $this->currentUser->country()->id;
                    $dataSiteBalance->aemp_iusr = $this->currentUser->employee()->id;
                    $dataSiteBalance->aemp_eusr = $this->currentUser->employee()->id;
                    $dataSiteBalance->save();
                }


            }

            $collection->cltn_date = date('Y-m-d H:i:s');
            $collection->aemp_vusr = $this->currentUser->employee()->id;
            $collection->cltn_vtme = date('Y-m-d H:i:s');
            $collection->aemp_yusr = $this->currentUser->employee()->id;
            $collection->cltn_ytme = date('Y-m-d H:i:s');
            $collection->save();
            $collectionTracking = new OutletCollectionTracking();
            $collectionTracking->setConnection($this->db);
            $collectionTracking->cltn_id = $collection->id;
            $collectionTracking->aemp_id = $this->currentUser->employee()->id;
            $collectionTracking->cqtr_time = date('Y-m-d H:i:s');
            $collectionTracking->geo_lat = 0;
            $collectionTracking->geo_lon = 0;
            $collectionTracking->cqtr_note = 'Collection Verified';
            $collectionTracking->cont_id = $this->currentUser->country()->id;
            $collectionTracking->lfcl_id = 1;
            $collectionTracking->aemp_iusr = $this->currentUser->employee()->id;
            $collectionTracking->aemp_eusr = $this->currentUser->employee()->id;
            $collectionTracking->save();

            return redirect()->back()->with('success', 'Successfully Updated');
        } else {
            return view('theme.access_limit');
        }

    }

    public function chequeVerifyUpdate(Request $value, $id)
    {
        if ($this->userMenu->wsmu_delt) {
            DB::connection($this->db)->beginTransaction();
            try {
                $collection = OutletCollection::on($this->db)->findorfail($id);
                if ($collection->lfcl_id == 1) {
                    $collection->lfcl_id = 26;
                    $collectionPaid = DB::connection($this->db)->select("SELECT
  t2.site_id,
  t1.acmp_id             AS ou_id,
  t1.aemp_cusr            ,
  t1.aemp_susr            ,
  t1.acmp_id            ,
  sum(t2.payment_amount) AS line_amount,
  t3.id           AS sr_id,
  t3.id                  AS emp_code,
  t1.cltn_date           AS date,
  t1.id                  AS collection_id,
  t1.cltn_note           AS collection_note,
  t1.cltn_amnt           AS amount,
  t1.cltn_code           AS collection_code
FROM tt_cltn AS t1
  INNER JOIN (SELECT
                t2.cltn_id        AS collection_id,
                sum(t2.clim_amnt) AS payment_amount,
                t1.site_id
              FROM tt_invc AS t1
                INNER JOIN tt_clim AS t2
                  ON t1.invc_code = t2.invc_code
              WHERE t1.trnt_id = 1 AND t2.cltn_id = $collection->id
              GROUP BY t1.site_id, t2.cltn_id
              UNION ALL
              SELECT
                t2.cltn_id             AS collection_id,
                -1 * sum(t2.clim_amnt) AS payment_amount,
                t1.site_id
              FROM tt_invc AS t1
                INNER JOIN tt_clim AS t2
                  ON t1.invc_code = t2.invc_code
              WHERE t1.trnt_id = 2 AND t2.cltn_id = $collection->id
              GROUP BY t1.site_id, t2.cltn_id
             ) AS t2 ON t1.id = t2.collection_id
  INNER JOIN tm_aemp AS t3 ON t1.aemp_susr = t3.id
WHERE t1.id = $collection->id
GROUP BY t2.site_id, t1.acmp_id,t1.aemp_cusr,t1.aemp_susr,  t3.id, t1.cltn_date, t1.cltn_note, t1.id, t1.cltn_amnt, t1.cltn_code");
                    foreach ($collectionPaid as $index => $collectionPaid1) {
                        $invc_code=$collectionPaid1->collection_code;
                        if ($index>0){
                            $invc_code=str_replace('-'.date('y').'-', date('y'). str_pad($index, 2, '0', STR_PAD_LEFT), $collectionPaid1->collection_code);
                        }
                        $dataSiteBalance = new SiteBalance();
                        $dataSiteBalance->setConnection($this->db);
                        $dataSiteBalance->acmp_id = $collectionPaid1->ou_id;
                        $dataSiteBalance->invc_code = $invc_code;
                        $dataSiteBalance->invc_lcod = $collectionPaid1->collection_code;
                        $dataSiteBalance->invc_date = $collectionPaid1->date;
                        $dataSiteBalance->aemp_cusr = $collectionPaid1->aemp_cusr;
                        $dataSiteBalance->aemp_susr = $collectionPaid1->aemp_susr;
                        $dataSiteBalance->invc_note = '';
                        $dataSiteBalance->site_id = $collectionPaid1->site_id;
                        $dataSiteBalance->trip_id = 0;
                        $dataSiteBalance->invc_taxc = '';
                        $dataSiteBalance->invc_amnt = $collectionPaid1->line_amount;
                        $dataSiteBalance->invc_mamt = $collectionPaid1->line_amount;
                        $dataSiteBalance->invt_id = 11;
                        $dataSiteBalance->trnt_id = 2;
                        $dataSiteBalance->invc_gamt = 0;
                        $dataSiteBalance->invc_exca = 0;
                        $dataSiteBalance->invc_vata = 0;
                        $dataSiteBalance->invc_tdis = 0;
                        $dataSiteBalance->invc_ramt = 0;
                        $dataSiteBalance->invc_lock = 0;
                        $dataSiteBalance->invc_icod = '';
                        $dataSiteBalance->invc_vcod = '';
                        $dataSiteBalance->cltn_id = $collectionPaid1->collection_id;
                        $dataSiteBalance->cltn_amnt = $collectionPaid1->amount;
                        $dataSiteBalance->lfcl_id = 26;
                        $dataSiteBalance->cont_id = $this->currentUser->country()->id;
                        $dataSiteBalance->aemp_iusr = $this->currentUser->employee()->id;
                        $dataSiteBalance->aemp_eusr = $this->currentUser->employee()->id;
                        $dataSiteBalance->save();
                    }
                }

                $collection->cltn_date = date('Y-m-d H:i:s');
                $collection->aemp_vusr = $this->currentUser->employee()->id;
                $collection->cltn_vtme = date('Y-m-d H:i:s');
                $collection->aemp_yusr = $this->currentUser->employee()->id;
                $collection->cltn_ytme = date('Y-m-d H:i:s');
                $collection->save();
                $collectionTracking = new OutletCollectionTracking();
                $collectionTracking->setConnection($this->db);
                $collectionTracking->cltn_id = $collection->id;
                $collectionTracking->aemp_id = $this->currentUser->employee()->id;
                $collectionTracking->cqtr_time = date('Y-m-d H:i:s');
                $collectionTracking->geo_lat = 0;
                $collectionTracking->geo_lon = 0;
                $collectionTracking->cqtr_note = 'Cheque Verified';
                $collectionTracking->cont_id = $this->currentUser->country()->id;
                $collectionTracking->lfcl_id = 1;
                $collectionTracking->aemp_iusr = $this->currentUser->employee()->id;
                $collectionTracking->aemp_eusr = $this->currentUser->employee()->id;
                $collectionTracking->save();
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'Successfully Updated');

            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                return $e;
            }
        } else {
            return view('theme.access_limit');
        }

    }

    public function onAccountVerify()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('collection.collection_maintain')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function chequeVerify($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $collection = collect(DB::connection($this->db)->select("SELECT
  t1.id                                                             AS payment_id, 
  t1.cltn_code                                                      AS payment_code,
  concat(t4.aemp_usnm, '-', t4.aemp_name, '(', t1.cltn_ctme, ')')   AS created_by,
  concat(t5.aemp_usnm, '-', t5.aemp_name, '(', t1.cltn_vtme, ')')   AS verified_by,
  concat(t10.aemp_usnm, '-', t10.aemp_name, '(', t1.cltn_mtme, ')') AS match_by,
  concat(t11.aemp_usnm, '-', t11.aemp_name, '(', t1.cltn_qtme, ')') AS cheque_paid_by,
  concat(t12.aemp_usnm, '-', t12.aemp_name, '(', t1.cltn_ytme, ')') AS carrying_by,
  t6.acmp_name                                                      AS ou_name,
  t1.cltn_note                                                      AS note,
  t1.cltn_date                                                      AS date,
  t1.cltn_ctme                                                      AS created_date,
  t1.cltn_amnt                                                      AS amount,
  t1.cltn_mamt                                                      AS allocated_amount,
  t2.lfcl_name                                                      AS status,
  concat(t1.oult_id, '-', t3.oult_name)                             AS outlet_name,
  t7.clpt_name                                                      AS payment_type,
  t1.clpt_id                                                        AS clpt_id,
  t8.clmt_name                                                      AS collection_type,
  t1.cltn_chqn                                                      AS cheque_no,
  t1.cltn_cdat                                                      AS cheque_date,
  t9.bank_name                                                      AS bank_name,
  t13.clrj_name                                                     AS reject_reason,
  t1.cltn_rnte                                                      AS reject_note,
  t1.lfcl_id
FROM tt_cltn AS t1
  INNER JOIN tm_lfcl AS t2 ON t1.lfcl_id = t2.id
  INNER JOIN tm_oult AS t3 ON t1.oult_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_cusr = t4.id
  LEFT JOIN tm_aemp AS t5 ON t1.aemp_vusr = t5.id
  INNER JOIN tm_acmp AS t6 ON t1.acmp_id = t6.id
  INNER JOIN tm_clpt AS t7 ON t1.clpt_id = t7.id
  INNER JOIN tm_clmt AS t8 ON t1.clmt_id = t8.id
  LEFT JOIN tm_bank AS t9 ON t1.bank_id = t9.id
  LEFT JOIN tm_aemp AS t10 ON t1.aemp_musr = t10.id
  LEFT JOIN tm_aemp AS t11 ON t1.aemp_qusr = t11.id
  LEFT JOIN tm_aemp AS t12 ON t1.aemp_yusr = t12.id
  LEFT JOIN tm_clrj AS t13 ON t1.clrj_id = t13.id
WHERE t1.id = $id"))->first();
            $chequeImage = DB::connection($this->db)->select("SELECT t2.clig_qimg as image_name
FROM tt_cltn AS t1
  INNER JOIN tm_clig AS t2 ON t1.id = t2.cltn_id
WHERE t1.id = $id");
            $time = date('Y-m-d h:i:s');
            $chequeHistory = DB::connection($this->db)->select("SELECT
  concat(t3.aemp_usnm, '-', t3.aemp_name) AS user,
  t2.cqtr_note                            AS note,
  t2.cqtr_time                            AS date_time
FROM tt_cltn AS t1
  INNER JOIN tt_cqtr AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
WHERE t1.id = $id
ORDER BY t2.cqtr_time DESC");
            $chequeMatching = DB::connection($this->db)->select("SELECT
  t3.invc_date                                        AS date,
  t3.site_id                                          AS site_id,
  t3.invc_code                                        AS invoice_code,
  t3.invc_taxc                                        AS tax_invoice,
  t3.invc_amnt                                        AS invoice_amount,
  t3.invc_mamt                                        AS collection_amount,
  if(t3.trnt_id = 2, -1 * t2.clim_amnt, t2.clim_amnt) AS matching_amount,
  t3.site_id                                          AS site_id,
  t4.site_name                                        AS site_name,
  t3.trnt_id                                          AS transaction_type,
  t5.invt_name                                        AS invoice_type
FROM tt_cltn AS t1
  INNER JOIN tt_clim AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tt_invc AS t3 ON t2.invc_code = t3.invc_code
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tm_invt AS t5 ON t3.invt_id = t5.id
WHERE t1.id = $id");

            $type = 1;
            if ($collection->clpt_id == 1) {
                $type = 2;
            }
            $rejectReason = DB::connection($this->db)->select("SELECT
  t1.clrj_name,
  t1.id
FROM tm_clrj AS t1
WHERE t1.clrj_type = $type;");

            return view('collection.collection_cheque_verify')->with('rejectReason', $rejectReason)->with('collection', $collection)->with('chequeImage', $chequeImage)->with('chequeHistory', $chequeHistory)->with('chequeMatching', $chequeMatching)->with('permission', $this->userMenu);

        } else {
            return view('theme.access_limit');
        }

    }

    public function newCollection()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('collection.collection_maintain')->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }

    public function viewCollection($id)
    {
        if ($this->userMenu->wsmu_vsbl) {

            $collection = collect(DB::connection($this->db)->select("SELECT
  t1.id                                                             AS payment_id,
  t1.cltn_code                                                      AS payment_code,
  concat(t4.aemp_usnm, '-', t4.aemp_name, '(', t1.cltn_ctme, ')')   AS created_by,
  concat(t5.aemp_usnm, '-', t5.aemp_name, '(', t1.cltn_vtme, ')')   AS verified_by,
  concat(t10.aemp_usnm, '-', t10.aemp_name, '(', t1.cltn_mtme, ')') AS match_by,
  concat(t11.aemp_usnm, '-', t11.aemp_name, '(', t1.cltn_qtme, ')') AS cheque_paid_by,
  concat(t12.aemp_usnm, '-', t12.aemp_name, '(', t1.cltn_ytme, ')') AS carrying_by,
  t6.acmp_name                                                      AS ou_name,
  t1.cltn_note                                                      AS note,
  t1.cltn_date                                                      AS date,
  t1.cltn_ctme                                                      AS created_date,
  t1.cltn_amnt                                                      AS amount,
  t1.cltn_mamt                                                      AS allocated_amount,
  t2.lfcl_name                                                      AS status,
  concat(t1.oult_id, '-', t3.oult_name)                             AS outlet_name,
  t7.clpt_name                                                      AS payment_type,
  t8.clmt_name                                                      AS collection_type,
  t1.cltn_chqn                                                      AS cheque_no,
  t1.cltn_cdat                                                      AS cheque_date,
  t9.bank_name                                                      AS bank_name,
  t13.clrj_name                                                     AS reject_reason,
  t1.cltn_rnte                                                      AS reject_note
FROM tt_cltn AS t1
  INNER JOIN tm_lfcl AS t2 ON t1.lfcl_id = t2.id
  INNER JOIN tm_oult AS t3 ON t1.oult_id = t3.id
  INNER JOIN tm_aemp AS t4 ON t1.aemp_cusr = t4.id
  LEFT JOIN tm_aemp AS t5 ON t1.aemp_vusr = t5.id
  INNER JOIN tm_acmp AS t6 ON t1.acmp_id = t6.id
  INNER JOIN tm_clpt AS t7 ON t1.clpt_id = t7.id
  INNER JOIN tm_clmt AS t8 ON t1.clmt_id = t8.id
  LEFT JOIN tm_bank AS t9 ON t1.bank_id = t9.id
  LEFT JOIN tm_aemp AS t10 ON t1.aemp_musr = t10.id
  LEFT JOIN tm_aemp AS t11 ON t1.aemp_qusr = t11.id
  LEFT JOIN tm_aemp AS t12 ON t1.aemp_yusr = t12.id
  LEFT JOIN tm_clrj AS t13 ON t1.clrj_id = t13.id
WHERE t1.id = $id"))->first();;
            $chequeImage = DB::connection($this->db)->select("SELECT t2.clig_qimg as image_name
FROM tt_cltn AS t1
  INNER JOIN tm_clig AS t2 ON t1.id = t2.cltn_id
WHERE t1.id = $id");
            $chequeHistory = DB::connection($this->db)->select("SELECT
  concat(t3.aemp_usnm, '-', t3.aemp_name) AS user,
  t2.cqtr_note                            AS note,
  t2.cqtr_time                            AS date_time
FROM tt_cltn AS t1
  INNER JOIN tt_cqtr AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tm_aemp AS t3 ON t2.aemp_id = t3.id
WHERE t1.id = $id
ORDER BY t2.cqtr_time DESC");
            $chequeMatching = DB::connection($this->db)->select("SELECT
  t3.invc_date                                        AS date,
  t3.site_id                                          AS site_id,
  t3.invc_code                                        AS invoice_code,
  t3.invc_taxc                                        AS tax_invoice,
  t3.invc_amnt                                        AS invoice_amount,
  t3.invc_mamt                                        AS collection_amount,
  if(t3.trnt_id = 2, -1 * t2.clim_amnt, t2.clim_amnt) AS matching_amount,
  t3.site_id                                          AS site_id,
  t4.site_name                                        AS site_name,
  t3.trnt_id                                          AS transaction_type,
  t5.invt_name                                        AS invoice_type
FROM tt_cltn AS t1
  INNER JOIN tt_clim AS t2 ON t1.id = t2.cltn_id
  INNER JOIN tt_invc AS t3 ON t2.invc_code = t3.invc_code
  INNER JOIN tm_site AS t4 ON t3.site_id = t4.id
  INNER JOIN tm_invt AS t5 ON t3.invt_id = t5.id
WHERE t1.id = $id");

            return view('collection.collection_view')->with('collection', $collection)->with('chequeImage', $chequeImage)->with('chequeHistory', $chequeHistory)->with('chequeMatching', $chequeMatching)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }


    public function filterCollection(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $where = "1 and t1.cont_id=$country_id and t1.trip_id=0";
        if ($request->start_date != "" && $request->end_date != "") {
            $where .= " AND t1.cltn_date between '$request->start_date' and '$request->end_date'";
        }
        $data = DB::connection($this->db)->select("SELECT

  t1.id                                   AS payment_id,
  t1.cltn_code                            AS collection_code,
  t1.oult_id                              AS outlet_id,
  t3.oult_name                            AS outlet_name,
  concat(t4.aemp_name, '-', t4.aemp_usnm) AS verified_by,
  concat(t5.aemp_name, '-', t5.aemp_usnm) AS collected,
  t6.acmp_name                            AS ou_name,
  t1.cltn_date                            AS payment_date,
  t1.cltn_vtme                            AS verify_date_time,
  t1.trip_id                              AS trip_id,
  t1.cltn_chqn                            AS cheque_no,
  t1.cltn_cdat                            AS cheque_date,
  t1.cltn_amnt                            AS amount,
   t1.cltn_amnt-t1.cltn_mamt              AS on_account,
  t1.lfcl_id                              AS status_id,
  t2.lfcl_name                            AS status,
  t1.cont_id
FROM tt_cltn AS t1
  INNER JOIN tm_lfcl AS t2 ON t1.lfcl_id = t2.id
  INNER JOIN tm_oult AS t3 ON t1.oult_id = t3.id
  LEFT JOIN tm_aemp AS t4 ON t1.aemp_vusr = t4.id
  LEFT JOIN tm_aemp AS t5 ON t1.aemp_iusr = t5.id
  INNER JOIN tm_acmp AS t6 ON t1.acmp_id = t6.id
  LEFT JOIN tt_clim AS t8 ON t1.id = t8.cltn_id
WHERE $where
GROUP BY t1.id,
  t1.cltn_code,
  t1.oult_id,
  t3.oult_name, t4.aemp_name, t4.aemp_usnm, t5.aemp_name, t5.aemp_usnm,
  t6.acmp_name,
  t1.cltn_date,
  t1.cltn_vtme,
  t1.trip_id,
  t1.cltn_chqn,
  t1.cltn_cdat,
  t1.cltn_amnt,
  t1.cltn_amnt - t1.cltn_mamt,
  t1.lfcl_id,
  t2.lfcl_name,t1.cont_id;");

        return $data;

    }


}