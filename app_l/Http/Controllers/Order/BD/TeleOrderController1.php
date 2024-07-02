<?php

namespace App\Http\Controllers\Order\BD;

use App\BusinessObject\Attendance;
use App\BusinessObject\CompanySiteBalance;
use App\BusinessObject\ItemMaster;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\Note;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\Recordings;
use App\BusinessObject\ReturnLine;
use App\BusinessObject\ReturnMaster;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\TeleOrderTracking;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\NoOrderReason;
use App\MasterData\NoteType;
use App\MasterData\Site;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use Excel;
use Response;
use AWS;
use function GuzzleHttp\Psr7\try_fopen;
use PDF;

class TeleOrderController extends Controller
{
    private $access_key = 'tele/order';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $employee;
    private $cont_id;
    private $free_amount = 0;
    private $free_qty    = 0;
    private $free_item   = 0;
    private $order_no   = 0;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
            $this->employee = Auth::user()->employee();
            $this->aemp_usnm = Auth::user()->employee()->aemp_usnm;
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => $this->currentUser->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            $hasAccess = true;

            if($this->employee->role_id === 1){
                $hasAttendance = Attendance::on($this->db)->where([
                    'aemp_id'       => $this->aemp_id,
                    'attn_date'     => date('Y-m-d'),
                    'atten_atyp'    => 1
                ])->first();

                if(!$hasAttendance){
                    $hasAccess = false;
                }
            }

            return view('Order.BD.tele_order_promotion_final')
                ->with('countries', $countries)
                ->with('has_access', $hasAccess);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function index_new(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            return view('Order.tele_multi_order')
                ->with('countries', $countries);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function index_promotion(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            return view('Order.tele_order_promotion')
                ->with('countries', $countries);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function calculatePromotion(Request $request){
        $site_id    = $request->site_id;
        $amim_id    = $request->amim_id;
        $order_qty  = $request->order_qty;
        $order_amnt = $request->order_amnt;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $country_id = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){

        }else {
            try {
                $hasValidPromotion = DB::connection($this->db)->select("SELECT t2.id, t2.prmr_qfct, t2.prmr_ditp from tl_prsm t1
                INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                INNER JOIN tm_prmd t3 ON t2.id = t3.prmr_id
                where t1.site_id={$site_id} and t3.amim_id={$amim_id}
                and t2.prms_edat >= curdate() and t2.lfcl_id = 1");

                if(count($hasValidPromotion) > 0){
                    $promotion = $hasValidPromotion[0];

                    if($promotion->prmr_qfct == 'foc' ){
                        $foc_info = $this->focCalculation($promotion->id, $order_qty);

                        $foc_info['prom_id'] = $promotion->id;

                        return $foc_info;
                    }
                    elseif ($promotion->prmr_qfct == 'discount' ){
                        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_qnty
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_qnty/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion->id} and {$order_qty} >= t1.prsb_fqty
                            order by t1.prsb_fqty desc limit 1");

                        if(count($promotion_values) > 0) {
                            return response(['amount' => $promotion_values[0]->amount, 'prom_id' => $promotion->id], 200);
                        }else{
                            return response(['amount' => 0], 200);
                        }
                    }
                    elseif ($promotion->prmr_qfct == 'value' ){
                        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_disc
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_disc/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion->id} and {$order_amnt} >= t1.prsb_famn
                            order by t1.prsb_famn desc limit 1");

                        if(count($promotion_values) > 0) {
                            return response(['amount' => $promotion_values[0]->amount, 'prom_id' => $promotion->id], 200);
                        }else{
                            return response(['amount' => 0], 200);
                        }
                    }
                }

            }catch (\Exception $exception)
            {
                return response(['error' => $exception->getMessage()], 401);
            }
        }
    }

    public function calculateFinalPromotion(Request $request){
        $site_id    = $request->site_id;
        $amim_id    = $request->amim_id;
        $order_qty  = $request->order_qty;
        $order_amnt = $request->order_amnt;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $country_id = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){

        }else {
            try {
                $hasValidPromotion = DB::connection($this->db)->select("SELECT t2.id, t2.prmr_qfct, t2.prmr_ditp from tl_prsm t1
                INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                INNER JOIN tm_prmd t3 ON t2.id = t3.prmr_id
                where t1.site_id={$site_id} and t3.amim_id={$amim_id}
                and t2.prms_edat >= curdate() and t2.lfcl_id = 1");

                if(count($hasValidPromotion) > 0){
                    $promotion = $hasValidPromotion[0];

                    if($promotion->prmr_qfct == 'foc' ){
                        $foc_info = $this->focCalculation($promotion->id, $order_qty);

                        $foc_info['prom_id'] = $promotion->id;

                        return $foc_info;

                        if(count($promotion_item) > 0) {
                            return response(['item' => $promotion_item[0], 'prom_id' => $promotion->id], 200);
                        }else{
                            return response(['item' => 0, 'prom_id' => $promotion->id], 200);
                        }
                    }
                    elseif ($promotion->prmr_qfct == 'discount' ){
                        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_qnty
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_qnty/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion->id} and {$order_qty} >= t1.prsb_fqty
                            order by t1.prsb_fqty desc limit 1");

                        if(count($promotion_values) > 0) {
                            return response(['amount' => $promotion_values[0]->amount, 'prom_id' => $promotion->id], 200);
                        }else{
                            return response(['amount' => 0], 200);
                        }
                    }
                    elseif ($promotion->prmr_qfct == 'value' ){
                        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_disc
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_disc/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion->id} and {$order_amnt} >= t1.prsb_famn
                            order by t1.prsb_famn desc limit 1");

                        if(count($promotion_values) > 0) {
                            return response(['amount' => $promotion_values[0]->amount, 'prom_id' => $promotion->id], 200);
                        }else{
                            return response(['amount' => 0], 200);
                        }
                    }
                }

            }catch (\Exception $exception)
            {
                return response(['error' => $exception->getMessage()], 401);
            }
        }
    }

    public function focCalculation($promotion_id, $order_qty){
        $promotion_item = DB::connection($this->db)->select(
            "SELECT t3.id, t3.amim_name, t3.amim_code, t1.prsb_fqty, t1.prsb_qnty
                            FROM tm_prsb t1
                            inner join tm_prmf t2 on t1.prmr_id = t2.prmr_id
                            inner join tm_amim t3 on t2.amim_id = t3.id
                            where t1.prmr_id={$promotion_id} and {$order_qty} >= t1.prsb_fqty
                            order by t1.prsb_fqty desc limit 1");


        if(count($promotion_item) > 0){
            $rest_qty = intval($order_qty) - $promotion_item[0]->prsb_fqty;
            if($rest_qty > 0){
                $this->free_item = $this->free_item + $promotion_item[0]->prsb_qnty;
                $this->focCalculation($promotion_id, $rest_qty);
            }elseif ($rest_qty == 0){
                $this->free_item = $this->free_item + $promotion_item[0]->prsb_qnty;
            }
        }else{
            return ['free_qty' => $this->free_item];
        }

        return ['promotion_item' => $promotion_item[0],'free_qty' => $this->free_item];
    }

    public function promCalculation($promotion_id, $order_qty){

        if($order_qty > 0) {
            $promotion_item = DB::connection($this->db)->select("SELECT
                t1.prdt_mbqt,
                    CASE
                        WHEN {$order_qty} >= t1.prdt_mbqt THEN t1.prdt_fiqt
                        ELSE 0
                    END AS qty,
                    CASE
                        WHEN t1.prdt_mnbt <= {$order_qty} AND {$order_qty} < t1.prdt_mbqt THEN t1.prdt_fipr * (t1.prdt_disc / 100)
                        ELSE 0
                    END AS amount
                FROM tt_prdt t1
            WHERE prom_id = {$promotion_id}");

            $promotion = $promotion_item[0];

            if ($promotion->qty > 0) {
                $rest_qty = $order_qty - $promotion->prdt_mbqt;
                if ($rest_qty > 0) {
                    $this->free_qty = $this->free_qty + $promotion->qty;
                    $this->promCalculation($promotion_id, $rest_qty);
                } elseif ($rest_qty == 0) {
                    $this->free_qty = $this->free_qty + $promotion->qty;
                }
            }elseif($promotion->amount){
                $this->free_amount = $this->free_amount + $promotion->amount;

            }
        }
        else{
            return [
                'free_qty'      => $this->free_qty,
                'free_amount'   => $this->free_amount
            ];
        }

        return ['free_qty' => $this->free_qty,'free_amount' => $this->free_amount];
    }

    public function discountCalculation($promotion_id, $order_fqty, $order_amnt){
        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_qnty
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_qnty/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion_id} and {$order_fqty} >= t1.prsb_fqty
                            order by t1.prsb_fqty desc limit 1");

        if(count($promotion_values) > 0) {
            return ['discount' => $promotion_values[0]->amount, 'prom_id' => $promotion_id];
        }else{
            return ['discount' => 0];
        }
    }

    public function valueCalculation($promotion_id, $order_amnt){
        $promotion_values = DB::connection($this->db)->select("SELECT 
                            case 
                                when (t2.prmr_ditp = 'amount') then t1.prsb_disc
                                when (t2.prmr_ditp = 'percent') then round({$order_amnt}*(t1.prsb_disc/100), 2)
                            end as amount
                            FROM tm_prsb t1
                            inner join tm_prmr t2 on t1.prmr_id = t2.id
                            where t1.prmr_id={$promotion_id} and {$order_amnt} >= t1.prsb_famn
                            order by t1.prsb_famn desc limit 1");

        if(count($promotion_values) > 0) {
            return ['value' => $promotion_values[0]->amount, 'prom_id' => $promotion_id];
        }else{
            return ['value' => 0];
        }
    }

    public function store(Request $request){
        $info = [];
        $country = Country::findOrFail($request->country_id);

        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

//        return $request->all();

        if($request->country_id == 2 || $request->country_id == 5){
            $data = [];


            if(isset($request->item_sr_ids)) {

                foreach ($request->item_sr_ids as $key => $sr_id) {
                    $data[$sr_id]['total'] = $request->total;
                    $data[$sr_id]['outlet_id'] = $request->outlet_id;
                    $data[$sr_id]['non_p_or_order'] = $request->non_p_or_order;
                    $data[$sr_id]['country_id'] = $request->country_id;
                    $data[$sr_id]['acmp_id'] = $request->acmp_id;
                    $data[$sr_id]['nopr_id'] = $request->nopr_id;
                    $data[$sr_id]['npro_note'] = $request->npro_note;
                    $data[$sr_id]['order_note'] = $request->order_note;
                    $data[$sr_id]['ntpe_id'] = $request->ntpe_id;
                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                    $data[$sr_id]['sr_id'] = $sr_id;
                    $data[$sr_id]['item_ids'][] = $request->item_ids[$key];
                    $data[$sr_id]['item_prices'][] = $request->item_prices[$key];
                    $data[$sr_id]['slgp_id'] = $request->item_slgp_ids[$key];
                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                    $data[$sr_id]['plmt_id'] = $request->item_plmt_ids[$key];
                    $data[$sr_id]['total_qtys'][] = $request->total_qtys[$key];
                    $data[$sr_id]['item_dufts'][] = $request->item_dufts[$key];
                    $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
                    $data[$sr_id]['dlrm_id'] = $this->getDistributorId($sr_id);
                }

                $total_srs = array_unique($request->item_sr_ids);

                foreach ($total_srs as $key => $sr_id) {
                    $info = $this->storeOrder($data[$sr_id]);
                }
            }else{
                $info = $this->storeOrder($request->all());
            }

        }else{
            $data = $request->all();
            $data['sr_id'] = $employee_id;
            $info = $this->storeOrder($data);
        }

        if($info['status'] == 200) {
            DB::connection($this->db)->beginTransaction();

            try {

                $note_info = $request->only(['ntpe_id','order_note','outlet_id','country_id']);
                $note_info['employee_id'] = $employee_id;

                $this->storeNote($note_info);

                $data = $request->only(['non_p_or_order','outlet_id','rout_id','total','acmp_id', 'sr_id', 'country_id']);
//            $data['sr_id'] = $employee_id;
                $teleOrderTrackingId = $this->teleOrderTracking($data);

                if(isset($info['order_id'])){
                    DB::connection($this->db)->table('tt_ordm')->where('id', $info['order_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
                }elseif (isset($info['non_p_id'])){
                    DB::connection($this->db)->table('tt_npro')->where('id', $info['non_p_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
                }

                DB::connection($this->db)->commit();
            }catch(\Exception $e)
            {
                DB::connection($this->db)->rollBack();

                return response([
                'error' => 'Order Storing Failed',
                'outlet_id' => $data['outlet_id']], 401);
            }

        }


        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
            'tltr_date' => date('y-m-d'),
            'rout_id' => $request->rout_id,
        ])->count();

        $info['visited_outlet_in_rout'] = $visited_outlet_in_rout+1;

        return response($info, $info['status']);
    }

    public function promotionStore(Request $request){
        $info = [];
        $country = Country::findOrFail($request->country_id);

        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;


        if($request->country_id == 2 || $request->country_id == 5){
            $data = [];


            if(isset($request->item_sr_ids)) {

                foreach ($request->item_sr_ids as $key => $sr_id) {
                    $data[$sr_id]['total'] = $request->total;
                    $data[$sr_id]['outlet_id'] = $request->outlet_id;
                    $data[$sr_id]['non_p_or_order'] = $request->non_p_or_order;
                    $data[$sr_id]['country_id'] = $request->country_id;
                    $data[$sr_id]['acmp_id'] = $request->acmp_id;
                    $data[$sr_id]['nopr_id'] = $request->nopr_id;
                    $data[$sr_id]['npro_note'] = $request->npro_note;
                    $data[$sr_id]['order_note'] = $request->order_note;
                    $data[$sr_id]['ntpe_id'] = $request->ntpe_id;
                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                    $data[$sr_id]['sr_id'] = $sr_id;
                    $data[$sr_id]['item_ids'][] = $request->item_ids[$key];
                    $data[$sr_id]['item_prices'][] = $request->item_prices[$key];
                    $data[$sr_id]['slgp_id'] = $request->item_slgp_ids[$key];
                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                    $data[$sr_id]['plmt_id'] = $request->item_plmt_ids[$key];
                    $data[$sr_id]['total_qtys'][] = $request->total_qtys[$key];
                    $data[$sr_id]['item_dufts'][] = $request->item_dufts[$key];
                    $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
                    $data[$sr_id]['dlrm_id'] = $this->getDistributorId($sr_id);
                }

                $total_srs = array_unique($request->item_sr_ids);

                foreach ($total_srs as $key => $sr_id) {
                    $info = $this->storeOrder($data[$sr_id]);
                }
            }else{
                $info = $this->storeOrder($request->all());
            }

        }else{
            $data = $request->all();
            $data['sr_id'] = $employee_id;
            $info = $this->storePromotionOrder($data);
        }

        if($info['status'] == 200) {
            DB::connection($this->db)->beginTransaction();

            try {

                $note_info = $request->only(['ntpe_id','order_note','outlet_id','country_id']);
                $note_info['employee_id'] = $employee_id;

                $this->storeNote($note_info);

                $has_order = TeleOrderTracking::on($this->db)->where([
                    'tltr_date'     => date('y-m-d'),
                    'aemp_iusr'     => $employee_id
                ])->first();

                if(!$has_order){
                    $site_info = $this->siteInfo($request->outlet_id);

                    $attendance_info = [
                        'slgp_id'   => $request->slgp_id,
                        'aemp_id'   => $employee_id,
                        'site_id'   => $request->outlet_id,
                        'site_name' => $site_info->site_name ?? '',
                        'geo_lat'   => $site_info->geo_lat ?? '',
                        'geo_lon'   => $site_info->geo_lon ?? '',
                        'attn_time' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                        'attn_date' => date('Y-m-d'),
                        'attn_mont' => 1,
                        'atten_type' => 1,
                        'atten_atyp' => 1,
                        'rout_id'   => $request->rout_id,
                        'attn_fdat'   =>  date('Y-m-d'),
                        'attn_tdat'   =>  date('Y-m-d'),
                        'cont_id'   => $request->country_id,
                        'lfcl_id'   => 1,
                        'aemp_iusr' => $employee_id,
                        'aemp_eusr' => $employee_id,
                        'attn_imge' => '',
                        'attn_rmak' => '',
                        'var' => 1,
                        'attr1' => '',
                        'attr2' => '',
                        'attr3' => 0,
                        'attr4' => 0,
                    ];

                    Attendance::on($this->db)->insert($attendance_info);
                }

                SiteVisited::on($this->db)->insert([
                    'ssvh_date' => date('y-m-d'),
                    'aemp_id'   => $employee_id,
                    'site_id'   => $request->outlet_id,
                    'ssvh_ispd' => $request->non_p_or_order,
                    'cont_id'   => $request->country_id,
                    'lfcl_id'   => 1,
                    'aemp_iusr' => $employee_id,
                    'aemp_eusr' => $employee_id,
                ]);


                $data = $request->only(['non_p_or_order','outlet_id','rout_id','total','acmp_id', 'sr_id', 'country_id']);

                $teleOrderTrackingId = $this->teleOrderTracking($data);

                if(isset($info['order_id'])){
                    DB::connection($this->db)->table('tt_ordm')->where('id', $info['order_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
                }elseif (isset($info['non_p_id'])){
                    DB::connection($this->db)->table('tt_npro')->where('id', $info['non_p_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
                }

                DB::connection($this->db)->commit();
            }catch(\Exception $e)
            {
                return $e->getMessage();
                DB::connection($this->db)->rollBack();
                return response([
//                'error' => $e->getMessage(),
                'error' => 'Order Storing Failed',
                'outlet_id' => $data['outlet_id']], 401);
            }

        }


        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
            'tltr_date' => date('y-m-d'),
            'rout_id' => $request->rout_id,
        ])->count();

        $info['visited_outlet_in_rout'] = $visited_outlet_in_rout+1;

        return response($info, $info['status']);
    }

    public function siteInfo($site_id){
        return Site::on($this->db)->find($site_id);
    }

    // check has any promotion
    public function checkPromotion(Request $request){
        $site_id    = $request->site_id;
        $amim_id    = $request->amim_id;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $country_id = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){
            return response('no promotion', 400);
        }else {
            try {
                $getAllPromotions = DB::connection($this->db)->select("SELECT t2.id, t2.prmr_qfct, t2.prmr_ditp, t2.prmr_qfon from tl_prsm t1
                    INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                    INNER JOIN tm_prmd t3 ON t2.id = t3.prmr_id
                    where t1.site_id={$site_id} and t3.amim_id={$amim_id}
                    and t2.prms_edat >= curdate() and t2.lfcl_id = 1");

                if(count($getAllPromotions) > 0){
                    $promotion = $getAllPromotions[0];
                    if($promotion->prmr_qfct == 'foc'){
                        $promotion_slub = DB::connection($this->db)->select("select t1.id, t1.prsb_fqty, t1.prsb_qnty from tm_prsb t1
	                        where t1.prmr_id = {$promotion->id}");

                        $promotion_items = DB::connection($this->db)->select("SELECT
                                t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft
                                FROM tl_stcm t1
                                INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                inner join tm_amim t2 on t1.amim_id = t2.id
                                where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

//                        $promotion_items = DB::connection($this->db)->select("select t2.id, t2.amim_code, t2.amim_name from tm_prmd t1
//                            inner join tm_amim t2 on t1.amim_id = t2.id
//                            where t1.prmr_id = {$promotion->id}");

                        return [
                            'prom_type' => strtoupper($promotion->prmr_qfct),
                            'prom_on'   => $promotion->prmr_qfon,
                            'slub'      => $promotion_slub,
                            'items'     => $promotion_items,
                            'prom_id'   => $promotion->id
                        ];
                    }
                    elseif ($promotion->prmr_qfct == 'discount'){
                        $promotion_slub = DB::connection($this->db)->select("select t1.id, t2.prmr_ditp, t1.prsb_fqty, t1.prsb_qnty, t2.prmr_qfon
                                from tm_prsb t1
                                inner join tm_prmr t2 on t1.prmr_id = t2.id
                                where t1.prmr_id = {$promotion->id}");

                        $promotion_items = DB::connection($this->db)->select("SELECT
                                t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr amim_price,t4.amim_duft amim_ctn_size
                                FROM tl_stcm t1
                                INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                inner join tm_amim t2 on t1.amim_id = t2.id
                                where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

//                        $promotion_items = DB::connection($this->db)->select("select t2.id, t2.amim_code, t2.amim_name from tm_prmd t1
//                            inner join tm_amim t2 on t1.amim_id = t2.id
//                            where t1.prmr_id = {$promotion->id}");

                        return [
                            'prom_type' => ucfirst($promotion->prmr_qfct),
                            'prom_on'   => $promotion->prmr_qfon,
                            'gift_on'   => $promotion->prmr_ditp,
                            'slub'      => $promotion_slub,
                            'items'     => $promotion_items,
                            'prom_id'   => $promotion->id
                        ];
                    }
                    elseif ($promotion->prmr_qfct == 'value'){
                        $promotion_slub = DB::connection($this->db)->select("select t1.id, t1.prsb_famn, t1.prsb_disc
                                from tm_prsb t1
                                inner join tm_prmr t2 on t1.prmr_id = t2.id
                                where t1.prmr_id = {$promotion->id}");

                        $promotion_items = DB::connection($this->db)->select("SELECT
                                t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr amim_price,t4.amim_duft amim_ctn_size
                                FROM tl_stcm t1
                                INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                inner join tm_amim t2 on t1.amim_id = t2.id
                                where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

                        return [
                            'prom_type' => ucfirst($promotion->prmr_qfct),
                            'gift_on'   => $promotion->prmr_ditp,
                            'slub'      => $promotion_slub,
                            'items'     => $promotion_items,
                            'prom_id'   => $promotion->id
                        ];
                    }
                    else{
                        return response('no promotion', 400);
                    }
                }
                else{
                    return response('no promotion', 400);
                }
            }
            catch (\Exception $exception)
            {
                return response(['error' => $exception->getMessage()], 401);
            }
        }

        return response('no promotion', 400);

    }

    // store order for foc pop up promotion
    public function storePromotionOrder($data){
        $country = Country::findOrFail($data['country_id']);

        $this->db = $country->cont_conn;

        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

        $ordered = true;

        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
            'tltr_date' => date('y-m-d'),
            'rout_id' => $data['rout_id'],
        ])->count();


        try {

            $today_ordered = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'site_id' => $data['outlet_id'],
                'aemp_id' => $data['sr_id']
            ])->first();

            if($today_ordered){
                return response(['error' => 'This Tele Order Already Exist', 'exist' => 1,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1], 403);
            }
            else{
                DB::connection($this->db)->beginTransaction();

                if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){


                    $order_number = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                    $info = [
                        'ordm_ornm' => $order_number,
                        'ordm_amnt' => array_sum($data['item_prices']),
                        'ordm_note' => 'tele order',
                        'cont_id'   => $data['country_id'],
                        'acmp_id'   => $data['acmp_id'],
                        'aemp_id'   => $data['sr_id'],
                        'slgp_id'   => $data['slgp_id'],
                        'rout_id'   => $data['rout_id'],
                        'site_id'   => $data['outlet_id'],
                        'ordm_date' => date('Y-m-d'),
                        'ordm_time' => date('Y-m-d H:i:s'),
                        'ordm_drdt' => date('Y-m-d', strtotime('+1 day')),
                        'ordm_dltm' => date('Y-m-d H:i:s', strtotime('+1 day')),
                        'dlrm_id'   => $data['dlrm_id'],
                        'ordm_icnt' => count($data['item_ids']),
                        'plmt_id'   => $data['plmt_id'],
                        'aemp_iusr' => $employee_id,
                        'aemp_eusr' => $employee_id,
                        'lfcl_id'   => 1,
                        'odtp_id'   => 1, // order type
                        'ocrs_id'   => 0, // order cancel reason id
                        'mspm_id'   => 0, // must sell product id
                        'aemp_cusr' => 0, // order cancel user id
                        'geo_lat'   => 0,
                        'geo_lon'   => 0,
                        'ordm_dtne' => 0,
                        'ordm_pono' => '',
                        'attr8' => 10
                    ];

                    $order_store_id = OrderMaster::on($this->db)->insertGetId($info);

                    $order_details = [];

                    foreach ($data['item_ids'] as $key => $id){
                        $order_details[] = [
                            'ordm_id' => $order_store_id,
                            'ordm_ornm' => $order_number,
                            'amim_id' => $id,
                            'ordd_inty' => $data['total_qtys'][$key],
                            'ordd_qnty' => $data['total_qtys'][$key],
                            'ordd_oamt' => $data['item_prices'][$key],
                            'ordd_amnt' => $data['item_prices'][$key],
                            'ordd_duft' => $data['item_dufts'][$key],
                            'ordd_uprc' => $data['item_unit_prices'][$key],
                            'cont_id'   => $data['country_id'],
                            'lfcl_id'   => 1,
                            'aemp_iusr' => $employee_id,
                            'aemp_eusr' => $employee_id,
                            'ordd_cqty' => 0,
                            'ordd_dqty' => 0,
                            'ordd_rqty' => 0,
                            'ordd_opds' => $data['item_discounts'][$key] ?? 0,
                            'ordd_cpds' => 0,
                            'ordd_dpds' => 0,
                            'ordd_spdo' => 0,
                            'ordd_spdc' => 0,
                            'ordd_spdd' => 0,
                            'ordd_spdi' => 0,
                            'ordd_runt' => 1,
                            'ordd_dunt' => 1,
                            'ordd_smpl' => 0,
                            'prom_id'   => $data['item_prom_ids'][$id] ?? 0,
                            'ordd_ocat' => 0,
                            'ordd_odat' => 0,
                            'ordd_excs' => 0,
                            'ordd_ovat' => 0,
                            'ordd_tdis' => 0,
                            'ordd_texc' => 0,
                            'ordd_tvat' => 0,
                            'ordd_dfdo' => 0,
                            'ordd_dfdc' => 0,
                            'ordd_dfdd' => 0
                        ];
                    }

                    foreach ($data['free_item_ids'] as $key => $id){
                        $order_details[] = [
                            'ordm_id'   => $order_store_id,
                            'ordm_ornm' => $order_number,
                            'amim_id'   => $id,
                            'ordd_inty' => $data['free_item_qtys'][$key],
                            'ordd_qnty' => $data['free_item_qtys'][$key],
                            'ordd_oamt' => 0,
                            'ordd_amnt' => 0,
                            'ordd_duft' => $data['free_item_dufts'][$key],
                            'ordd_uprc' => $data['free_item_unit_prices'][$key],
                            'cont_id'   => $data['country_id'],
                            'lfcl_id'   => 1,
                            'aemp_iusr' => $employee_id,
                            'aemp_eusr' => $employee_id,
                            'ordd_cqty' => 0,
                            'ordd_dqty' => 0,
                            'ordd_rqty' => 0,
                            'ordd_opds' => 0,
                            'ordd_cpds' => 0,
                            'ordd_dpds' => 0,
                            'ordd_spdo' => 0,
                            'ordd_spdc' => 0,
                            'ordd_spdd' => 0,
                            'ordd_spdi' => 0,
                            'ordd_runt' => 1,
                            'ordd_dunt' => 1,
                            'ordd_smpl' => 1,
                            'prom_id'   => $data['free_item_prom_ids'][$key] ?? 0,
                            'ordd_ocat' => 0,
                            'ordd_odat' => 0,
                            'ordd_excs' => 0,
                            'ordd_ovat' => 0,
                            'ordd_tdis' => 0,
                            'ordd_texc' => 0,
                            'ordd_tvat' => 0,
                            'ordd_dfdo' => 0,
                            'ordd_dfdc' => 0,
                            'ordd_dfdd' => 0
                        ];
                    }

                    OrderLine::on($this->db)->insert($order_details);

                }
                else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

                    if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                        $ordered = false;
                        $non_productive_info = [
                            'aemp_id'   => $employee_id,
                            'slgp_id'   => $data['slgp_id'],
                            'site_id'   => $data['outlet_id'],
                            'rout_id'   => $data['rout_id'],
                            'nopr_id'   => $data['nopr_id'],
                            'dlrm_id'   => $data['dlrm_id'],
                            'npro_note' => $data['npro_note'] ?? 'Non Productive Tele Order',
                            'npro_date' => date('Y-m-d'),
                            'npro_time' => date('Y-m-d H:i:s'),
                            'npro_dtne' => 0,
                            'geo_lat'   => 0,
                            'geo_lon'   => 0,
                            'cont_id'   => $data['country_id'],
                            'lfcl_id'   => 1,
                            'aemp_iusr' => $employee_id,
                            'aemp_eusr' => $employee_id
                        ];

                        $non_productive = NonProductiveOutlet::on($this->db)->insertGetId($non_productive_info);
                    }else{
                        return ['error' => 'Please Provide Non Productive Reason',
                            'status' => 402
                        ];
                    }
                }
                else{
                    return ['error' => 'Please Provide Order or Non Productive Reason',
                        'status' => 402];
                }

                DB::connection($this->db)->commit();
            }

        }catch (\Exception $exception)
        {
            DB::connection($this->db)->rollback();

            return [
                //                'error' => 'Order Storing Failed',
                'error' => $exception->getMessage(),
                'outlet_id' => $data['outlet_id'],
                'status' => 401
            ];
        }

        if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

            return [
                'success'   => 'Order Stored Successfully',
                'outlet_id' => $data['outlet_id'],
                'order_id'  => $order_store_id,
                'status'    => 200
            ];

        }
        elseif(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

            return [
                'success' => 'Order Stored Successfully',
                'outlet_id' => $data['outlet_id'],
                'non_p_id' => $non_productive,
                'status' => 200
            ];
        }

        return [
            'success' => 'Order Stored Successfully',
            'outlet_id' => $data['outlet_id'],
            'status' => 200
        ];
    }

    // final promotion with foc pop up
    public function promotion(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            return view('Order.tele_order_promotion_final')
                ->with('countries', $countries);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    // all promotion calculation in pop up
    public function getPromotionCalculation(Request $request){
        $promotion_id   = $request->prom_id;
        $order_qty      = $request->order_qty;
        $order_ctn      = $request->order_ctn;
        $order_amnt      = $request->order_amnt;
        $site_id        = $request->site_id;
        $country        = Country::findOrFail($request->country_id);
        $this->db       = $country->cont_conn;
        $country_id     = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){

        }else {
            $promotion = DB::connection($this->db)->table('tm_prmr')->find($promotion_id);

            if($promotion->prmr_qfct == 'foc' && $promotion->prmr_qfon != 'CRT') {
                $foc_info = $this->focCalculation($promotion_id, $order_qty);
            }
            elseif ($promotion->prmr_qfct == 'foc' && $promotion->prmr_qfon == 'CRT'){
                $foc_info = $this->focCalculation($promotion_id, $order_ctn);
            }
            elseif ($promotion->prmr_qfct == 'discount' && $promotion->prmr_qfon == 'QTY'){
                $discount = $this->discountCalculation($promotion_id, $order_qty, $order_amnt);
            }
            elseif ($promotion->prmr_qfct == 'discount' && $promotion->prmr_qfon == 'CRT'){
                $discount = $this->discountCalculation($promotion_id, $order_ctn, $order_amnt);
            }
            elseif ($promotion->prmr_qfct == 'value'){
                $value = $this->valueCalculation($promotion_id, $order_amnt);
            }

            if($promotion->prmr_qfct == 'foc') {
                if ($foc_info['free_qty'] > 0) {
                    $free_item_info = DB::connection($this->db)->select("SELECT
                    t4.id id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                    WHERE t4.lfcl_id=1 AND t3.amim_id in ({$foc_info['promotion_item']->id}) AND t1.site_id = {$site_id}
                    GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");


                    $foc_info['type']       = 'foc';
                    $foc_info['prom_id']    = $promotion_id;
                    $foc_info['item_info']  = $free_item_info[0];
                    $foc_info['qfon']       = $promotion->prmr_qfon;
                    unset($foc_info['promotion_item']);
                }else{
                    $foc_info['type']       = 'foc';
                }

                return $foc_info;
            }
            elseif($promotion->prmr_qfct == 'discount'){
                $discount['type']       = 'discount';
                $discount['qfon']       = $promotion->prmr_qfon;
                $discount['prom_id']    = $promotion_id;
                return $discount;
            }
            elseif($promotion->prmr_qfct == 'value'){
                $value['type']       = 'value';
                $value['qfon']       = $promotion->prmr_qfon;
                $value['prom_id']    = $promotion_id;
                return $value;
            }
            else{
                return response(['No Promotion'], 404);
            }
        }

    }

    public function audioUpload(Request $request){
        return $request->all();
        if (isset($request->recording_file)) {
            $recording_file = $this->currentUser->country()->cont_imgf .'/trn/tel_ord/'.date('Y-m-d').'/'.uniqid() . '.' . $request->recording_file->getClientOriginalExtension();
            $file = $request->file('recording_file');
            $size =  $file->getSize();

            try {

//            if ($size < 5*1024*1024) {
                $s3 = AWS::createClient('s3');
                $s3->putObject(array(
                    'Bucket' => 'prgfms',
                    'Key' => $recording_file,
                    'SourceFile' => $file,
                    'ACL' => 'public-read',
                    'ContentType' => $file->getMimeType(),
                ));

                return response('uploaded successfully', 200);
            }catch (\Exception $exception){
                return response($exception, 400);
            }

//                $has_audio = true;
//            }


//            if($has_audio){
//                Recordings::on($this->db)->insert([
//                    'tltr_id' => $teleOrderTrackingId,
//                    'rcds_file' => $recording_file,
//                    'aemp_iusr' => $employee_id,
//                ]);
//            }
        }
    }

    // store foc promotion pop up
    public function promotionNew(Request $request){

        $has_audio = false;

        $order_store_id = 0;

        $recording_file = '';


        if(isset($request->non_p_or_order) && $request->non_p_or_order == '0'){
            if($request->nopr_id == null){
                return response(['error' => 'Please Provide Non Productive Reason'], 400);
            }
        }

        if(isset($request->non_p_or_order) && $request->non_p_or_order == '1'){
            if($request->total == null || $request->total == 0.00){
                return response(['error' => 'Please Select an Item'], 400);
            }
        }

        if(isset($request->note) && $request->ntpe_id == null){
//        if(isset($request->note) && $request->ntpe_id == null && !isset($request->recording_file)){
            return response(['error' => 'Please Provide Note Type'], 400);
        }

//        if (isset($request->recording_file)) {
//            $recording_file = $this->currentUser->country()->cont_imgf .'/trn/tel_ord/'.date('Y-m-d').'/'.uniqid() . '.' . $request->recording_file->getClientOriginalExtension();
//            $file = $request->file('recording_file');
//            $size =  $file->getSize();
//
//            if ($size < 5*1024*1024) {
//                $s3 = AWS::createClient('s3');
//                $s3->putObject(array(
//                    'Bucket' => 'prgfms',
//                    'Key' => $recording_file,
//                    'SourceFile' => $file,
//                    'ACL' => 'public-read',
//                    'ContentType' => $file->getMimeType(),
//                ));
//
//                $has_audio = true;
//            }
//
//        }

        DB::connection($this->db)->beginTransaction();

        try {
            $country = Country::findOrFail($request->country_id);

            $this->db = $country->cont_conn;

            $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id' => $request->rout_id,
            ])->count();

//            $has_order = TeleOrderTracking::on($this->db)->where([
//                'tltr_date' => date('y-m-d'),
//                'aemp_iusr' => $employee_id
//            ])->first();
//
//            if (!$has_order) {
//                $this->storeAttendance($request->all(), $employee_id);
//            }


//            if(isset($request->tracking_id)){
//                $today_ordered = TeleOrderTracking::on($this->db)->where([
//                    'tltr_date' => date('y-m-d'),
//                    'site_id' => $request->outlet_id,
//                    'tltr_ordr' => 1
//                ])->first();
//            }else {
            $today_ordered = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'site_id' => $request->outlet_id,
//                'tltr_ordr' => 1
            ])->first();
//            }

            if(!is_null($today_ordered)){
                return response(['error' => 'Tele Order for this Site Already Exist', 'exist' => 1,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1], 403);
            }

            $data = $request->only(['non_p_or_order', 'outlet_id', 'rout_id', 'total', 'acmp_id', 'sr_id', 'country_id', 'zone_id']);
            $teleOrderTrackingId = $this->teleOrderTracking($data);

//
//            if($has_audio){
//                Recordings::on($this->db)->insert([
//                   'tltr_id' => $teleOrderTrackingId,
//                   'rcds_file' => $recording_file,
//                   'aemp_iusr' => $employee_id,
//                ]);
//            }


            if ($request->country_id == 2 || $request->country_id == 5) {
                $data = [];


                if (isset($request->item_sr_ids)) {

                    foreach ($request->item_sr_ids as $key => $sr_id) {

                        $data[$sr_id]['total']              = $request->total;
                        $data[$sr_id]['outlet_id']          = $request->outlet_id;
                        $data[$sr_id]['non_p_or_order']     = $request->non_p_or_order;
                        $data[$sr_id]['country_id']         = $request->country_id;
                        $data[$sr_id]['acmp_id']            = $request->acmp_id;
                        $data[$sr_id]['nopr_id']            = $request->nopr_id;
                        $data[$sr_id]['npro_note']          = $request->npro_note;
                        $data[$sr_id]['order_note']         = $request->order_note;
                        $data[$sr_id]['ntpe_id']            = $request->ntpe_id;
                        $data[$sr_id]['rout_id']            = $request->item_rout_ids[$key];
                        $data[$sr_id]['sr_id']              = $sr_id;
                        $data[$sr_id]['item_ids'][]         = $request->item_ids[$key];
                        $data[$sr_id]['item_prices'][]      = $request->item_prices[$key];
                        $data[$sr_id]['slgp_id']            = $request->item_slgp_ids[$key];
                        $data[$sr_id]['rout_id']            = $request->item_rout_ids[$key];
                        $data[$sr_id]['plmt_id']            = $request->item_plmt_ids[$key];
                        $data[$sr_id]['total_qtys'][]       = $request->total_qtys[$key];
                        $data[$sr_id]['item_dufts'][]       = $request->item_dufts[$key];
                        $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
                        $data[$sr_id]['item_discounts'][]   = $request->item_discounts[$key];
                        $data[$sr_id]['item_special_discounts'][]   = $request->item_special_discounts[$key];
                        $data[$sr_id]['item_prom_ids'][$request->item_ids[$key]]    = $request->item_prom_ids[$request->item_ids[$key]] ?? 0;
                        $data[$sr_id]['dlrm_id']            = $this->getDistributorId($sr_id);

                    }

                    if (isset($request->free_item_ids)) {
                        foreach ($request->free_item_ids as $key => $free_item_id) {
                            $data[$key]['free_item_ids'][]           = $free_item_id;
                            $data[$key]['free_item_qtys'][]          = $request->free_item_qtys[$key];
                            $data[$key]['free_item_dufts'][]         = $request->free_item_dufts[$key];
                            $data[$key]['free_item_unit_prices'][]   = $request->free_item_unit_prices[$key];
                            $data[$key]['free_item_prom_ids'][]      = $request->free_item_prom_ids[$key];
                        }
                    }

                                    //                    return response($data, 400);
                                //                    return $data;

                    $total_srs = array_unique($request->item_sr_ids);
                    
                    foreach ($total_srs as $key => $sr_id) {

                        if (isset($data[$sr_id]['non_p_or_order']) && $data[$sr_id]['non_p_or_order'] == "1" && isset($data[$sr_id]['item_ids'])){

                            $data[$sr_id]['order_number'] = $this->getOrderNumber($data[$sr_id]['sr_id'], $data[$sr_id]['country_id'], $employee_id);

                            $order_store_id = $this->storeOrderMaster($data[$sr_id], $employee_id, $teleOrderTrackingId);

                            $this->storeOrderLine($data[$sr_id], $order_store_id, $employee_id);

                        }
                        else if(isset($data[$sr_id]['non_p_or_order']) && $data[$sr_id]['non_p_or_order'] == "0"){

                            if(isset($data[$sr_id]['nopr_id']) && !empty($data[$sr_id]['nopr_id'])) {
                                $non_productive = $this->storeNonProductiveOutlet($data[$sr_id], $employee_id, $teleOrderTrackingId);
                            }else{
                                return response(['error' => 'Please Provide Non Productive Reason'],402);
                            }
                        }
                        else{
                            return ['error' => 'Please Provide Order or Non Productive Reason',
                                'status' => 402];
                        }
                    }
                }
                else {
                    $data = $request->all();

                    if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                        $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                        $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                        $this->storeOrderLine($data, $order_store_id, $employee_id);
                    }
                    else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == 0){

                        if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                            $non_productive = $this->storeNonProductiveOutlet($data, $employee_id, $teleOrderTrackingId);
                        }else{
                            return response(['error' => 'Please Provide Non Productive Reason'],402);
                        }
                    }
                    else{
                        return ['error' => 'Please Provide Order or Non Productive Reason',
                            'status' => 402];
                    }
                }

            }
            else {
                $data = $request->all();
                $data['sr_id'] = $employee_id;

                if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                    $this->order_no = $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                    if(isset($data['grv_item_ids']) && $request->country_id != 9){
                        $grv_master_id = $this->storeGrvMaster($data, $employee_id, $teleOrderTrackingId);
                        $this->storeGrvLine($data, $grv_master_id, $employee_id);
                    }

                    $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                    $this->storeOrderLine($data, $order_store_id, $employee_id);

                }
                else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

                    if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                        $non_productive = $this->storeNonProductiveOutlet($data, $employee_id, $teleOrderTrackingId);
                    }else{
                        return response(['error' => 'Please Provide Non Productive Reason'],402);
                    }
                }
                else{
                    return response(['error' => 'Please Order or Non Productive Reason'], 401);
                }
            }

            $data = $request->only(['non_p_or_order', 'outlet_id', 'country_id']);

            $this->storeSiteVisitStory($data, $employee_id);

            if(!is_null($request->ntpe_id)) {
                $note_info = $request->only(['ntpe_id', 'order_note', 'outlet_id', 'country_id']);
                $note_info['employee_id'] = $employee_id;
                $this->storeNote($note_info);
            }

            DB::connection($this->db)->commit();

            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id' => $request->rout_id,
            ])->count();

            $outlets = DB::connection($this->db)->select("SELECT t2.id, t2.site_code, t2.site_name FROM tl_rsmp t1
                            INNER JOIN tm_site t2 ON t2.id = t1.site_id
                            WHERE t1.rout_id = {$request->rout_id} AND t2.lfcl_id = 1
                            AND t1.site_id NOT IN (SELECT site_id FROM tt_tltr t3
                            WHERE t3.tltr_date = CURDATE() AND t3.rout_id= {$request->rout_id})");

            if($request->country_id != 9) {
                return response([
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1,
                    'outlets' => $outlets
                ], 200);
            }else{
                if ($request->country_id==9){
                    $digit=-10;
                    $code='91';
                }

                $manager_info = DB::connection($this->db)->select("SELECT t1.id, t1.aemp_name, CONCAT({$code},SUBSTRING(REPLACE(t1.aemp_mob1,'-',''),{$digit})) 'mobile'
                    FROM tm_aemp t1
                    INNER JOIN tm_aemp t2 ON t1.id = t2.aemp_mngr AND t2.lfcl_id = 1
                    INNER JOIN tm_aemp t3 ON t2.id = t3.aemp_mngr AND t3.lfcl_id = 1
                    INNER JOIN tl_rpln t4 ON t3.id = t4.aemp_id
                    INNER JOIN tl_rsmp t5 ON t4.rout_id = t5.rout_id AND t5.site_id = {$request->outlet_id} AND t4.aemp_id <> {$request->sr_id}
                    where t1.lfcl_id = 1 limit 1");

                $order_no = DB::connection($this->db)->select("select ordm_ornm from tt_ordm where id = {$order_store_id}");

                return response([
                    'visited_outlet_in_rout'    => $visited_outlet_in_rout + 1,
                    'outlets'                   => $outlets,
                    'manager_info'              => count($manager_info) > 0 ? $manager_info : [],
                    'order_id'                  => $order_store_id > 0 ? $order_store_id : null,
                    'order_no'                  => count($order_no) > 0 ? $order_no[0] : null
                ], 200);
            }
        }
        catch(\Exception $e)
        {
            DB::connection($this->db)->rollBack();

            return response([
//                'error' => $e,
                'error' => $e->getMessage(),
//                'error' => 'Order Storing Failed',
                'outlet_id' => $request->outlet_id], 401);
        }

    }

    public function storeGrvMaster($data, $employee_id, $teleOrderTrackingId){
        $info = [
            'rtan_rtnm' => substr_replace($data['order_number'], 'R', 0, 1),
            'rtan_amnt' => array_sum($data['grv_item_prices']),
            'rtan_note' => 'tele order GRV',
            'cont_id'   => $data['country_id'],
            'acmp_id'   => $data['acmp_id'],
            'aemp_id'   => $data['sr_id'],
            'slgp_id'   => $data['slgp_id'],
            'rout_id'   => $data['rout_id'],
            'site_id'   => $data['outlet_id'],
            'rtan_date' => date('Y-m-d'),
            'rtan_podt' => date('Y-m-d'),
            'rtan_time' => date('Y-m-d H:i:s'),
            'rtan_drdt' => date('Y-m-d'),
            'rtan_dltm' => date('Y-m-d'),
            'dlrm_id'   => $data['dlrm_id'],
            'rtan_icnt' => count($data['grv_item_ids']),
            'rtan_pono' => '',
            'rttp_id'   => 1,
            'plmt_id'   => $data['plmt_id'],
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
            'lfcl_id'   => 1,
            'geo_lat'   => 0,
            'geo_lon'   => 0,
            'attr4'     => $teleOrderTrackingId
        ];

        return ReturnMaster::on($this->db)->insertGetId($info);
    }

    public function storeGrvLine($data, $grv_master_id, $employee_id){

        $return_details = [];

        foreach ($data['grv_item_ids'] as $key => $id){
            $return_details[] = [
                'rtan_id' => $grv_master_id,
                'rtdd_rtan' => substr_replace($data['order_number'], 'R', 0, 1),
                'amim_id'   => $id,
                'dprt_id'   => 1,
                'rtdd_qnty' => $data['grv_total_qtys'][$key],
                'rtdd_dqty' => 0,
                'rtdd_duft' => $data['grv_item_dufts'][$key],
                'rtdd_uprc' => $data['grv_item_unit_prices'][$key],
                'rtdd_runt' => 1,
                'rtdd_dunt' => 1,
                'rtdd_oamt' => $data['grv_item_prices'][$key],
                'rtdd_amnt' => $data['grv_item_prices'][$key],
                'rtdd_damt' => 0,
                'rtdd_edat' => date('Y-m-d'),
                'rtdd_note' => 0,
                'ordm_ornm' => 0,
                'rtdd_rato' => 0,
                'rtdd_excs' => 0,
                'rtdd_ovat' => $this->getItemVat($id),
                'rtdd_tdis' => 0,
                'rtdd_texc' => 0,
                'rtdd_tvat' => $data['grv_item_prices'][$key]*($this->getItemVat($id)/100),
                'rtdd_ptyp' => 1,
                'cont_id'   => $data['country_id'],
                'lfcl_id'   => 1,
                'aemp_iusr' => $employee_id,
                'aemp_eusr' => $employee_id,
            ];
        }

        return ReturnLine::on($this->db)->insert($return_details);
    }

    public function getItemVat($amim_id){
        return ItemMaster::on($this->db)->find($amim_id)->amim_pvat;
    }

    public function storeOrderLine($data, $order_store_id, $employee_id){

        $order_details = [];
       // return $data;

        foreach ($data['item_ids'] as $key => $id){
            $order_details[] = [
                'ordm_id' => $order_store_id,
                'ordm_ornm' => $data['order_number'],
                'amim_id'   => $id,
                'ordd_inty' => $data['total_qtys'][$key],
                'ordd_qnty' => $data['total_qtys'][$key],
                'ordd_oamt' => $data['item_prices'][$key],
                'ordd_amnt' => $data['item_prices'][$key],
                'ordd_duft' => $data['item_dufts'][$key],
                'ordd_uprc' => $data['item_unit_prices'][$key],
                'cont_id'   => $data['country_id'],
                'lfcl_id'   => 1,
                'aemp_iusr' => $employee_id,
                'aemp_eusr' => $employee_id,
                'ordd_cqty' => 0,
                'ordd_dqty' => 0,
                'ordd_rqty' => 0,
                'ordd_opds' => $data['item_discounts'][$key] ?? 0,
                'ordd_cpds' => 0,
                'ordd_dpds' => 0,
                'ordd_spdc' => 0,
                'ordd_spdd' => 0,
                'ordd_spdi' => $this->getSpecialDiscount($data['country_id'], $data['slgp_id'], $data['outlet_id'], $data['item_special_discounts'][$key], $data['item_prices'][$key]),
                'ordd_spdo' => $this->getSpecialDiscount($data['country_id'], $data['slgp_id'], $data['outlet_id'], $data['item_special_discounts'][$key], $data['item_prices'][$key]),
                'ordd_runt' => 1,
                'ordd_dunt' => 1,
                'ordd_smpl' => 0,
                'prom_id'   => $data['item_prom_ids'][$id] ?? 0,
                'ordd_ocat' => 0,
                'ordd_odat' => 0,
                'ordd_excs' => 0,
                'ordd_ovat' => 0,
                'ordd_tdis' => 0,
                'ordd_texc' => 0,
                'ordd_tvat' => 0,
                'ordd_dfdo' => 0,
                'ordd_dfdc' => 0,
                'ordd_dfdd' => 0
            ];
        }

        if(isset($data['free_item_ids'])) {
            foreach ($data['free_item_ids'] ?? [] as $key => $id) {
                $order_details[] = [
                    'ordm_id' => $order_store_id,
                    'ordm_ornm' => $data['order_number'],
                    'amim_id' => $id,
                    'ordd_inty' => $data['free_item_qtys'][$key],
                    'ordd_qnty' => $data['free_item_qtys'][$key],
                    'ordd_oamt' => 0,
                    'ordd_amnt' => 0,
                    'ordd_duft' => $data['free_item_dufts'][$key],
                    'ordd_uprc' => $data['free_item_unit_prices'][$key],
                    'cont_id' => $data['country_id'],
                    'lfcl_id' => 1,
                    'aemp_iusr' => $employee_id,
                    'aemp_eusr' => $employee_id,
                    'ordd_cqty' => 0,
                    'ordd_dqty' => 0,
                    'ordd_rqty' => 0,
                    'ordd_opds' => 0,
                    'ordd_cpds' => 0,
                    'ordd_dpds' => 0,
                    'ordd_spdo' => 0,
                    'ordd_spdc' => 0,
                    'ordd_spdd' => 0,
                    'ordd_spdi' => 0,
                    'ordd_runt' => 1,
                    'ordd_dunt' => 1,
                    'ordd_smpl' => 1,
                    'prom_id' => $data['free_item_prom_ids'][$key] ?? 0,
                    'ordd_ocat' => 0,
                    'ordd_odat' => 0,
                    'ordd_excs' => 0,
                    'ordd_ovat' => 0,
                    'ordd_tdis' => 0,
                    'ordd_texc' => 0,
                    'ordd_tvat' => 0,
                    'ordd_dfdo' => 0,
                    'ordd_dfdc' => 0,
                    'ordd_dfdd' => 0
                ];
            }
        }

        return OrderLine::on($this->db)->insert($order_details);
    }

    public function getSpecialDiscount($country_id, $slgp_id, $outlet_id, $item_special_discount, $item_subtotal){
        $country = Country::findOrFail(request()->country_id);

        $this->db = $country->cont_conn;

        if($item_special_discount == 0){
            return 0;
        }elseif($this->is_bangladesh($country_id)){
             return 0;
        }elseif($country_id == 9){
             return $item_special_discount;
        }else{
            $payment_type = CompanySiteBalance::on($this->db)->where([
                'slgp_id' => $slgp_id,
                'site_id' => $outlet_id
            ])->first();

            if($payment_type->optp_id == 1){
                return $item_special_discount;
            }elseif($payment_type->optp_id == 2){
                return 0;
            }else{
                return 0;
            }
        }
    }

    public function getSpecialDiscountInfo($country_id, $slgp_id, $outlet_id){
        $country = Country::findOrFail(request()->country_id);

        $this->db = $country->cont_conn;

        if($this->is_bangladesh($country_id)){
             return 0;
        }else if($country_id == 9){
             return 1;
        }
        else{
            $payment_type = CompanySiteBalance::on($this->db)->where([
                'slgp_id' => $slgp_id,
                'site_id' => $outlet_id
            ])->first();

            return $payment_type->optp_id;
        }
    }

    public function is_bangladesh($country_id){
        if($country_id == 2 && $country_id == 5){
            return true;
        }else{
            return false;
        }
    }

    public function storeOrderMaster($data, $employee_id, $teleOrderTrackingId){
        $info = [
            'ordm_ornm' => $data['order_number'],
            'ordm_amnt' => array_sum($data['item_prices']),
            'ordm_note' => 'tele order',
            'cont_id'   => $data['country_id'],
            'acmp_id'   => $data['acmp_id'],
            'aemp_id'   => $data['sr_id'],
            'slgp_id'   => $data['slgp_id'],
            'rout_id'   => $data['rout_id'],
            'site_id'   => $data['outlet_id'],
            'ordm_date' => date('Y-m-d'),
            'ordm_time' => date('Y-m-d H:i:s'),
            'ordm_drdt' => date('Y-m-d', strtotime('+1 day')),
            'ordm_dltm' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'dlrm_id'   => $data['dlrm_id'],
            'ordm_icnt' => count($data['item_ids']),
            'plmt_id'   => $data['plmt_id'],
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
            'lfcl_id'   => $this->getOrderLifeCycle(
                isset($data['has_special_discount']) && $data['has_special_discount'] == '1' ? 1 : 0, $data['country_id'], $data['slgp_id'], $data['outlet_id']),
            'odtp_id'   => 1, // order type
            'ocrs_id'   => 0, // order cancel reason id
            'mspm_id'   => 0, // must sell product id
            'aemp_cusr' => 0, // order cancel user id
            'geo_lat'   => 0,
            'geo_lon'   => 0,
            'ordm_dtne' => 0,
            'ordm_pono' => '',
            'attr4'     => $teleOrderTrackingId,
            'attr8'     => 10
        ];

        return OrderMaster::on($this->db)->insertGetId($info);
    }

    public function getOrderLifeCycle($hasSpecialDiscount, $country_id, $slgp_id, $outlet_id){
        if($hasSpecialDiscount == 1){
            $info = $this->getSpecialDiscountInfo($country_id, $slgp_id, $outlet_id);

            if($info == 1){
                return 17;
            }
            else{
                return 1;
            }
        }else{
            return 1;
        }
    }

    public function storeNonProductiveOutlet($data, $employee_id, $teleOrderTrackingId){
        $non_productive_info = [
            'aemp_id'   => $employee_id,
            'slgp_id'   => $data['slgp_id'],
            'site_id'   => $data['outlet_id'],
            'rout_id'   => $data['rout_id'],
            'nopr_id'   => $data['nopr_id'],
            'dlrm_id'   => $data['dlrm_id'],
            'npro_note' => $data['npro_note'] ?? 'Non Productive Tele Order',
            'npro_date' => date('Y-m-d'),
            'npro_time' => date('Y-m-d H:i:s'),
            'npro_dtne' => 0,
            'geo_lat'   => 0,
            'geo_lon'   => 0,
            'cont_id'   => $data['country_id'],
            'lfcl_id'   => 1,
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
            'attr4'     => $teleOrderTrackingId
        ];

        return NonProductiveOutlet::on($this->db)->insertGetId($non_productive_info);
    }

    public function storeAttendance($data, $employee_id){
        $site_info = $this->siteInfo($data['outlet_id']);

        $exist = Attendance::on($this->db)->where([
            'aemp_id'       => $employee_id,
            'attn_date'     => date('Y-m-d'),
            'atten_atyp'    => 1
        ])->first();

        if(!$exist) {

            $attendance_info = [
                'slgp_id' => $data['slgp_id'],
                'aemp_id' => $employee_id,
                'site_id' => $data['outlet_id'],
                'site_name' => $site_info->site_name ?? '',
                'geo_lat' => $site_info->geo_lat ?? '',
                'geo_lon' => $site_info->geo_lon ?? '',
                'attn_time' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                'attn_date' => date('Y-m-d'),
                'attn_mont' => 1,
                'atten_type' => 1,
                'atten_atyp' => 1,
                'rout_id' => $data['rout_id'],
                'attn_fdat' => date('Y-m-d'),
                'attn_tdat' => date('Y-m-d'),
                'cont_id' => $data['country_id'],
                'lfcl_id' => 1,
                'aemp_iusr' => $employee_id,
                'aemp_eusr' => $employee_id,
                'attn_imge' => '',
                'attn_rmak' => '',
                'var' => 1,
                'attr1' => '',
                'attr2' => '',
                'attr3' => 0,
                'attr4' => 0,
            ];


            return Attendance::on($this->db)->insert($attendance_info);
        }

    }

    public function storeSiteVisitStory($data, $employee_id){
        SiteVisited::on($this->db)->insert([
            'ssvh_date' => date('y-m-d'),
            'aemp_id'   => $employee_id,
            'site_id'   => $data['outlet_id'],
            'ssvh_ispd' => $data['non_p_or_order'],
            'cont_id'   => $data['country_id'],
            'lfcl_id'   => 1,
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
        ]);
    }

    public function storeOrder($data){
        $country = Country::findOrFail($data['country_id']);

        $this->db = $country->cont_conn;

        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

        $ordered = true;

        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
            'tltr_date' => date('y-m-d'),
            'rout_id' => $data['rout_id'],
        ])->count();

        try {

            $today_ordered = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'site_id' => $data['outlet_id'],
                'aemp_id' => $data['sr_id']
            ])->first();


            if($today_ordered){
                return response(['error' => 'This Tele Order Already Exist', 'exist' => 1,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1], 403);
            }
            else{
                DB::connection($this->db)->beginTransaction();


                if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){


                    $order_number = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                    $info = [
                        'ordm_ornm' => $order_number,
                        'ordm_amnt' => array_sum($data['item_prices']),
                        'ordm_note' => 'tele order',
                        'cont_id'   => $data['country_id'],
                        'acmp_id'   => $data['acmp_id'],
                        'aemp_id'   => $data['sr_id'],
                        'slgp_id'   => $data['slgp_id'],
                        'rout_id'   => $data['rout_id'],
                        'site_id'   => $data['outlet_id'],
                        'ordm_date' => date('Y-m-d'),
                        'ordm_time' => date('Y-m-d H:i:s'),
                        'ordm_drdt' => date('Y-m-d', strtotime('+1 day')),
                        'ordm_dltm' => date('Y-m-d H:i:s', strtotime('+1 day')),
                        'dlrm_id'   => $data['dlrm_id'],
                        'ordm_icnt' => count($data['item_ids']),
                        'plmt_id'   => $data['plmt_id'],
                        'aemp_iusr' => $employee_id,
                        'aemp_eusr' => $employee_id,
                        'lfcl_id'   => 1,
                        'odtp_id'   => 1, // order type
                        'ocrs_id'   => 0, // order cancel reason id
                        'mspm_id'   => 0, // must sell product id
                        'aemp_cusr' => 0, // order cancel user id
                        'geo_lat'   => 0,
                        'geo_lon'   => 0,
                        'ordm_dtne' => 0,
                        'ordm_pono' => '',
                        'attr8' => 10
                    ];

                    $order_store_id = OrderMaster::on($this->db)->insertGetId($info);

                    $order_details = [];

                    foreach ($data['item_ids'] as $key => $id){
                        $order_details[] = [
                            'ordm_id' => $order_store_id,
                            'ordm_ornm' => $order_number,
                            'amim_id' => $id,
                            'ordd_inty' => $data['total_qtys'][$key],
                            'ordd_qnty' => $data['total_qtys'][$key],
                            'ordd_oamt' => $data['item_prices'][$key],
                            'ordd_amnt' => $data['item_prices'][$key],
                            'ordd_duft' => $data['item_dufts'][$key],
                            'ordd_uprc' => $data['item_unit_prices'][$key],
                            'cont_id'   => $data['country_id'],
                            'lfcl_id'   => 1,
                            'aemp_iusr' => $employee_id,
                            'aemp_eusr' => $employee_id,
                            'ordd_cqty' => 0,
                            'ordd_dqty' => 0,
                            'ordd_rqty' => 0,
                            'ordd_opds' => 0,
                            'ordd_cpds' => 0,
                            'ordd_dpds' => 0,
                            'ordd_spdo' => 0,
                            'ordd_spdc' => 0,
                            'ordd_spdd' => 0,
                            'ordd_spdi' => 0,
                            'ordd_runt' => 1,
                            'ordd_dunt' => 1,
                            'ordd_smpl' => 0,
                            'prom_id' => 0,
                            'ordd_ocat' => 0,
                            'ordd_odat' => 0,
                            'ordd_excs' => 0,
                            'ordd_ovat' => 0,
                            'ordd_tdis' => 0,
                            'ordd_texc' => 0,
                            'ordd_tvat' => 0,
                            'ordd_dfdo' => 0,
                            'ordd_dfdc' => 0,
                            'ordd_dfdd' => 0
                        ];
                    }

                    OrderLine::on($this->db)->insert($order_details);

                }
                else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

                    if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                        $ordered = false;
                        $non_productive_info = [
                            'aemp_id' => $employee_id,
                            'slgp_id' => $data['slgp_id'],
                            'site_id' => $data['outlet_id'],
                            'rout_id' => $data['rout_id'],
                            'nopr_id' => $data['nopr_id'],
                            'dlrm_id' => $data['dlrm_id'],
                            'npro_note' => $data['npro_note'] ?? 'Non Productive Tele Order',
                            'npro_date' => date('Y-m-d'),
                            'npro_time' => date('Y-m-d H:i:s'),
                            'npro_dtne' => 0,
                            'geo_lat' => 0,
                            'geo_lon' => 0,
                            'cont_id'   => $data['country_id'],
                            'lfcl_id' => 1,
                            'aemp_iusr' => $employee_id,
                            'aemp_eusr' => $employee_id
                        ];

                        $non_productive = NonProductiveOutlet::on($this->db)->insertGetId($non_productive_info);
                    }else{
                        return ['error' => 'Please Provide Non Productive Reason',
                            'status' => 402
                        ];
                    }
                }
                else{
                    return ['error' => 'Please Provide Order or Non Productive Reason',
                        'status' => 402];
                }

                DB::connection($this->db)->commit();

            }

        }catch (\Exception $exception)
        {
            DB::connection($this->db)->rollback();

            return [
                'error' => 'Order Storing Failed',
                'outlet_id' => $data['outlet_id'],
                'status' => 401
            ];
        }

        if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

            return [
                'success'   => 'Order Stored Successfully',
                'outlet_id' => $data['outlet_id'],
                'order_id'  => $order_store_id,
                'status'    => 200
            ];

        }elseif(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

            return [
                'success' => 'Order Stored Successfully',
                'outlet_id' => $data['outlet_id'],
                'non_p_id' => $non_productive,
                'status' => 200
            ];
        }

        return [
            'success' => 'Order Stored Successfully',
            'outlet_id' => $data['outlet_id'],
            'status' => 200
        ];
    }

    public function storeNote($data){
        if(isset($data['order_note']) && !empty($data['order_note'])) {
            $site = Site::on($this->db)->find($data['outlet_id']);

            $note = [
                'aemp_id' => $data['employee_id'],
                'ntpe_id' => $data['ntpe_id'],
                'note_tokn' => str_pad(rand(1, (int)999999999999999999), 19, '0', STR_PAD_LEFT),
                'site_code' => $site->site_code,
                'note_body' => $data['order_note'],
                'note_date' => date('Y-m-d'),
                'note_dtim' => date('Y-m-d H:i:s'),
                'note_rtim' => date('Y-m-d H:i:s'),
                'note_titl'   => '',
                'geo_addr'   => '',
                'geo_lat'   => 0,
                'geo_lon'   => 0,
                'note_type'   => 'tele order',
                'cont_id'   => $data['country_id'],
                'lfcl_id' => 1,
                'aemp_iusr' => $data['employee_id'],
                'aemp_eusr' => $data['employee_id'],
            ];

            Note::on($this->db)->insert($note);
        }
    }

    public function teleOrderTracking($data){
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

        $order_tracking = [
            'tltr_ordr' => $data['non_p_or_order'],
            'tltr_date' => date('Y-m-d'),
            'tltr_dtim' => date('Y-m-d H:i:s'),
            'site_id'   => $data['outlet_id'],
            'aemp_id'   => $data['sr_id'],
            'rout_id'   => $data['rout_id'],
            'tltr_amnt' => $data['total'] ?? 0,
            'acmp_id'   => $data['acmp_id'],
            'zone_id'   => $data['zone_id'] ?? 0,
            'lfcl_id'   => 1,
            'cont_id'   => $data['country_id'],
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ];

        if($data['country_id'] != 2 && $data['country_id'] != 5) {
            unset($order_tracking['zone_id']);
        }


        return TeleOrderTracking::on($this->db)->insertGetId($order_tracking);
    }

    public function getOrderNumber($sr_id, $country_id, $employee_id)
    {
        $orderSequence = OrderSequence::on($this->db)->where(['aemp_id' => $sr_id, 'srsc_year' => date('y')])->first();
        if ($orderSequence == null) {
            $orderSequence = new OrderSequence();
            $orderSequence->setConnection($this->db);
            $orderSequence->aemp_id = $sr_id;
            $orderSequence->srsc_year = date('y');
            $orderSequence->srsc_ocnt = 0;
            $orderSequence->srsc_rcnt = 0;
            $orderSequence->srsc_ccnt = 0;
            $orderSequence->cont_id = $country_id;
            $orderSequence->lfcl_id = 1;
            $orderSequence->aemp_iusr = $employee_id;
            $orderSequence->aemp_eusr = $employee_id;
            $orderSequence->save();
        }else{
            $orderSequence->increment('srsc_ocnt',1);
        }
        $employee = Employee::on($this->db)->where(['id' => $sr_id])->first();
        $order_id = "O" . str_pad($employee->aemp_usnm, 10, '0', STR_PAD_LEFT) . '-' . $orderSequence->srsc_year . '-' . str_pad($orderSequence->srsc_ocnt + 1, 5, '0', STR_PAD_LEFT);

        return $order_id;
    }

    public function getCompanies(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $country = Country::findOrFail($request->country_id);

            $minOrderAmount = (new Country)->country($request->country_id)->min_oamt;

            $minOrderAmount = ($minOrderAmount == 0) ? 100 : $minOrderAmount;

            $this->db = $country->cont_conn;

            try {
                $aemp_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

                $companies = DB::connection($this->db)->select("SELECT acmp_id id,acmp_code,acmp_name
                    FROM user_group_permission WHERE aemp_id={$aemp_id} GROUP BY acmp_id,acmp_code,acmp_name");
                   // return $minOrderAmount;

                return response(['companies' => $companies, 'minOrderAmount' => $minOrderAmount], 200);

                // all company name
//                 $companies = Company::on($this->db)->where('lfcl_id', 1)->get(['id', 'acmp_code', 'acmp_name']);
            }catch (\Exception $exception)
            {
                return response(['error' => 'No Access, Select Another Country', 'exist' => 0], 403);
            }

            return ['companies' => $companies, 'con' => $this->db];
        }
        else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getNoteNonProductiveTypes(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $country = Country::findOrFail($request->country_id);

            $this->db = $country->cont_conn;

            $np_reasons = NoOrderReason::on($this->db)->get(['id', 'nopr_code', 'nopr_name']);

            $note_types = NoteType::on($this->db)->get(['id', 'ntpe_code', 'ntpe_name']);

            return [
                'np_reasons' => $np_reasons,
                'note_types' => $note_types,
                'con' => $this->db
            ];

        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getGroups(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;

            $aemp_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;
//            $groups = SalesGroup::on($this->db)->where(['acmp_id' => $request->company_id, 'lfcl_id' => 1])->get(['id', 'slgp_code', 'slgp_name']);
            $groups = DB::connection($this->db)->select("SELECT slgp_id id,slgp_code,slgp_name
                            FROM user_group_permission WHERE aemp_id={$aemp_id} and acmp_id={$request->company_id}
                            GROUP BY slgp_id,slgp_code,slgp_name");
            return ['groups' => $groups, 'con' => $this->db];
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getZones(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;

            $aemp_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            $zones = DB::connection($this->db)->select("SELECT zone_id id, zone_code, zone_name FROM user_area_permission
                                        WHERE aemp_id={$aemp_id} GROUP BY zone_id, zone_code, zone_name");

            return ['zones' => $zones, 'con' => $this->db];
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getSrs(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $slgp_id = $request->slgp_id;
        $zone_id = $request->zone_id;
        $country_id = $request->country_id;

        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;
            $srs = DB::connection($this->db)
                ->select("select t2.id, t2.aemp_usnm, t2.aemp_name from tl_sgsm t1
                        INNER JOIN tm_aemp t2 on t2.id = t1.aemp_id
                        where t1.slgp_id={$slgp_id} and t1.zone_id={$zone_id} and t1.cont_id={$country_id} and t2.role_id=1 and t2.lfcl_id = 1");


            return ['srs' => $srs, 'con' => $this->db];
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getDistributors(Request $request){
        $this->db = $request->con ?? $this->db;
        $employee_id = $request->aemp_id;

        if ($this->userMenu->wsmu_read) {
            return DB::connection($this->db)->select("SELECT t2.id, t2.dlrm_code, t2.dlrm_name
                    FROM tl_srdi t1
                    INNER JOIN tm_dlrm t2 ON t1.dlrm_id = t2.id
                    WHERE t1.aemp_id = {$employee_id} AND t2.lfcl_id = 1");
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getDistributorId($employee_id){
        $this->db = $request->con ?? $this->db;

        $dlrm_info = DB::connection($this->db)->select("SELECT t2.id
                FROM tl_srdi t1
                INNER JOIN tm_dlrm t2 ON t1.dlrm_id = t2.id
                WHERE t1.aemp_id = {$employee_id} AND t2.lfcl_id = 1 limit 1");

        if($dlrm_info){
            return $dlrm_info[0]->id;
        }
    }

    public function getRoutes(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $country_id = $request->country_id;
        $aemp_id = $request->aemp_id;

        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;
            $routes = DB::connection($this->db)->select("select t2.id, t2.rout_code, t2.rout_name from tl_rpln t1
                    INNER JOIN tm_rout t2 on t2.id = t1.rout_id
                    where t1.aemp_id={$aemp_id} and t1.cont_id={$country_id} and t2.lfcl_id = 1");

            return ['routs' => $routes, 'con' => $this->db];
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getOutlets(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $route_id = $request->route_id;
        $employee_id = $request->employee_id;

//        $staff_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;


        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;

            $outlets = DB::connection($this->db)->select("SELECT t2.id, t2.site_code, t2.site_name FROM tl_rsmp t1
                            INNER JOIN tm_site t2 ON t2.id = t1.site_id
                            WHERE t1.rout_id = {$route_id} AND t2.lfcl_id = 1
                            AND t1.site_id NOT IN (SELECT site_id FROM tt_tltr t3
                            WHERE t3.tltr_date = CURDATE() AND t3.rout_id= {$route_id})");

            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id' => $route_id,
            ])->count();

            if(count($outlets) > 0){
                return response([
                    'outlets' => $outlets,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1,
//                    'staff_id' => $staff_id
                ]);
            }else{
                return response(['error' => 'Access Limited'], 403);
            }
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getOutletInfo(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $country_id = $request->country_id;
        $outlet_id = $request->outlet_id;



        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;
            $code='880';
            $digit=10;

            if($country_id==14){
                $digit=9;
                $code='60';
            }
            elseif ($country_id==15){
                $digit=9;
                $code='966';
            }
            elseif ($country_id==9){
                $digit=10;
                $code='91';
            }
            elseif ($country_id==3){
                $digit=9;
                $code='971';
            }
            elseif ($country_id==24){
                $digit=10;
                $code='1';
            }

            return DB::connection($this->db)->select("SELECT t1.id,t1.site_code, t1.site_name, t4.than_name 'thana', t1.site_adrs,
                        CONCAT({$code},RIGHT(REPLACE(t1.site_mob1,'-',''),{$digit})) 'mobile',  t5.dsct_name 'district',t1.site_olnm,t6.dm_name,
                        t6.rout_day,t6.rout_name
                        FROM tm_site t1
                        INNER JOIN tm_mktm t2 on t1.mktm_id = t2.id
                        INNER JOIN tm_ward t3 on t2.ward_id = t3.id
                        INNER JOIN tm_than t4 on t3.than_id = t4.id
                        INNER JOIN tm_dsct t5 on t4.dsct_id = t5.id
                        LEFT JOIN tl_dmst_rout t6 ON t1.id=t6.site_id
                        WHERE t1.id = {$outlet_id} and t1.lfcl_id = 1");
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getOrderInfo(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $site_id = $request->outlet_id;
        $aemp_id = $request->employee_id;
        $order_info=array();
        // return DB::connection($this->db)->select("SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt, t2.lfcl_name 'order_status'
        //                     FROM tt_ordm t1
        //                     inner join tm_lfcl t2 on t1.lfcl_id = t2.id
        //                     WHERE  t1.site_id= {$site_id} and t1.lfcl_id = 1 and t1.ordm_date >= curdate() - interval 60 day
        //                     ORDER BY t1.ordm_date desc");
        $order_master= DB::connection($this->db)->select("SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt, t2.lfcl_name 'order_status'
                        FROM tt_ordm t1
                        inner join tm_lfcl t2 on t1.lfcl_id = t2.id
                        WHERE  t1.site_id= {$site_id} and t1.lfcl_id = 1 and t1.ordm_date >= curdate() - interval 60 day
                        ORDER BY t1.ordm_date desc");
         for($i=0;$i<count($order_master);$i++){
                $order_id=$order_master[$i]->id;
                $order_details=DB::connection($this->db)->select("select t1.id, t1.ordd_inty, t1.ordd_oamt, t2.amim_code, t2.amim_name from tt_ordd t1
                                inner join tm_amim t2 on t1.amim_id = t2.id
                                where t1.ordm_id={$order_id} and t1.lfcl_id");
                $order_all_info=array(
                    'id'=>$order_master[$i]->id,
                    'ordm_ornm'=>$order_master[$i]->ordm_ornm,
                    'ordm_date'=>$order_master[$i]->ordm_date,
                    'ordm_amnt'=>$order_master[$i]->ordm_amnt,
                    'order_status'=>$order_master[$i]->order_status,
                    'order_details'=>$order_details
                );
                array_push($order_info,$order_all_info);
        
         }
        return $order_info;

    }

    public function getOrderInfo1(){
        $this->db = $request->con ?? $this->db;
        $site_id = $request->outlet_id;
        $aemp_id = $request->employee_id;
        $order_info=array();
        $order_master= DB::connection($this->db)->select("SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt, t2.lfcl_name 'order_status'
                        FROM tt_ordm t1
                        inner join tm_lfcl t2 on t1.lfcl_id = t2.id
                        WHERE  t1.site_id= {$site_id} and t1.lfcl_id = 1 and t1.ordm_date >= curdate() - interval 60 day
                        ORDER BY t1.ordm_date desc");
         for($i=0;$i<count($order_master);$i++){
                $order_id=$order_master[$i]->id;
                $order_details=DB::connection($this->db)->select("select t1.id, t1.ordd_inty, t1.ordd_oamt, t2.amim_code, t2.amim_name from tt_ordd t1
                                inner join tm_amim t2 on t1.amim_id = t2.id
                                where t1.ordm_id={$order_id} and t1.lfcl_id");
                $order_all_info=array(
                    'id'=>$order_master[$i]->id,
                    'ordm_ornm'=>$order_master[$i]->ordm_ornm,
                    'ordm_date'=>$order_master[$i]->ordm_date,
                    'ordm_amnt'=>$order_master[$i]->ordm_amnt,
                    'order_status'=>$order_master[$i]->order_status,
                    'order_details'=>$order_details
                );
                array_push($order_info,$order_all_info);
        
         }
        return $order_info;
    }

    public function getOrderDetails(Request $request)
    {
        $this->db = $request->con ?? $this->db;
        $order_id = $request->order_id;


        return DB::connection($this->db)->select("select t1.id, t1.ordd_inty, t1.ordd_oamt, t2.amim_code, t2.amim_name from tt_ordd t1
                    inner join tm_amim t2 on t1.amim_id = t2.id
                    where t1.ordm_id={$order_id} and t1.lfcl_id");


    }

    public function getSubCategories(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $country = Country::findOrFail($request->country_id);

            $country_id = $country->id ?? $request->country_id;
            $slgp_id = $request->slgp_id;
            $site_id = $request->site_id;
            $sr_id = $request->sr_id;
            $dpot_id = $request->dlrm_id;

            $this->db = $country->cont_conn;

            if($country_id == 2 || $country_id == 5){
                $categories = DB::connection($this->db)->select("SELECT t5.id, t5.issc_name cat_name, t5.issc_code cat_code
                                    ,max(t3.slgp_id)slgp_id,max(t2.rout_id)rout_id,max(t2.aemp_id)aemp_id
                                    FROM tl_rsmp t1
                                    inner join tl_rpln t2 on t1.rout_id = t2.rout_id
                                    inner join tl_sgsm t3 ON t2.aemp_id=t3.aemp_id
                                    inner join tl_sgit t4 on t4.slgp_id = t3.slgp_id
                                    inner join tm_issc t5 on t4.issc_id = t5.id
                                    inner join tm_aemp t6 on t2.aemp_id = t6.id
                                    where t1.site_id={$site_id} and t6.lfcl_id = 1
                                    GROUP BY t5.id,t5.issc_name,t5.issc_code");

            }elseif ($country_id == 9){
                $categories = DB::connection($this->db)->select("select t4.id,  t4.issc_code cat_code, t4.issc_name cat_name, t1.plmt_id
                    FROM tl_sgsm AS t1
                      INNER JOIN tl_sgit AS t3 ON t1.slgp_id = t3.slgp_id 
                      INNER JOIN tm_issc AS t4 ON t3.issc_id = t4.id and t1.slgp_id = t4.slgp_id
                      where t1.slgp_id = {$slgp_id} and t4.lfcl_id = 1 and t1.aemp_id = {$sr_id}
                      group by t4.id, t4.issc_code, t4.issc_name, t1.plmt_id");
            }
            else{
                $categories = DB::connection($this->db)->select("SELECT
                    t4.id ,t4.issc_code cat_code,t4.issc_name cat_name ,t1.plmt_id
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_issc t4 ON t3.issc_id=t4.id
                    INNER JOIN tm_amim t5 ON t2.amim_id=t5.id
                    WHERE t4.lfcl_id=1 AND t3.slgp_id = {$slgp_id} AND t1.site_id = {$site_id} AND t1.slgp_id = {$slgp_id}
                    AND t5.lfcl_id = 1
                    GROUP BY t4.id ,t4.issc_code,t4.issc_name ,t1.plmt_id");

            }
            return ['categories' => $categories, 'country_id' => $country_id];
        }else{
            return response(['error' => 'Permission Denied'], 403);
        }
    }

    public function getItems(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $country = Country::findOrFail($request->country_id);

            $country_id = $country->id ?? $request->country_id;
            $category_id = $request->category_id;
            $sr_id = $request->sr_id;
            $site_id = $request->outlet_id;
            $dpot_id = $request->dlrm_id;
            $category_name=$request->category_name;
            $cat_name = explode('-', $category_name);
            $name=$cat_name[1];

            $this->db = $country->cont_conn;

            if($country_id == 2 || $country_id == 5){
                $items = DB::connection($this->db)->select("SELECT 
                            t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id,t2.pldt_tpgp
                            FROM tl_sgsm t1
                            INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                            INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                            INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                            WHERE t1.aemp_id={$sr_id} and t4.lfcl_id=1 AND t3.issc_id={$category_id}
                            GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id,t2.pldt_tpgp");
            }
            elseif ($country_id == 9){
                $items = DB::connection($this->db)->select("SELECT
                    t3.id amim_id,t3.amim_code,t3.amim_name,t2.pldt_tppr,t3.amim_duft,t2.pldt_tpgp
                    FROM tl_sgsm AS t1
                    INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id 
                    INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                    INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
                    INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
                    where t5.issc_name = '{$name}' and t1.aemp_id = {$sr_id}
                    group by t3.id ,t3.amim_code,t3.amim_name,t2.pldt_tppr,t3.amim_duft,t2.pldt_tpgp");
            }
            else{
                $items = DB::connection($this->db)->select("SELECT
                    t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t2.pldt_tpgp
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                    WHERE t4.lfcl_id=1 AND t3.issc_id= {$category_id} AND t1.site_id = {$site_id}
                    AND t4.id NOT IN (SELECT amim_id FROM tt_outs WHERE dpot_id = {$dpot_id})
                    group by t4.id ,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t2.pldt_tpgp");
            }

            return ['items' => $items, 'country_id' => $country_id];
        }else{
            return response(['error' => 'Permission Denied'], 403);
        }

    }

    public function getGrvItems(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $country = Country::findOrFail($request->country_id);

            $country_id = $country->id ?? $request->country_id;
            $category_id = $request->category_id;
            $sr_id = $request->sr_id;
            $site_id = $request->outlet_id;
            $dpot_id = $request->dlrm_id;

            $this->db = $country->cont_conn;

            if($country_id == 2 || $country_id == 5){
                $items = DB::connection($this->db)->select("SELECT 
                            t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id,t2.pldt_tpgp
                            FROM tl_sgsm t1
                            INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                            INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                            INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                            WHERE t1.aemp_id={$sr_id} and t4.lfcl_id=1 AND t3.issc_id={$category_id}
                            GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id,t2.pldt_tpgp");
            }
            else if($country_id == 9){
                $items = DB::connection($this->db)->select("SELECT
                    t3.id amim_id,t3.amim_code,t3.amim_name,t2.pldt_tppr,t3.amim_duft,t2.pldt_tpgp
                    FROM tl_sgsm AS t1
                    INNER JOIN tl_sgit AS t4 ON t1.slgp_id = t4.slgp_id 
                    INNER JOIN tm_issc AS t5 ON t5.id = t4.issc_id
                    INNER JOIN tm_pldt AS t2 ON t1.plmt_id = t2.plmt_id AND t2.amim_id = t4.amim_id
                    INNER JOIN tm_amim AS t3 ON t2.amim_id = t3.id 
                    where t4.issc_id = {$category_id} and t1.aemp_id = {$sr_id}
                    group by t3.id ,t3.amim_code,t3.amim_name,t2.pldt_tppr,t3.amim_duft,t2.pldt_tpgp");
            }else{
                $items = DB::connection($this->db)->select("SELECT
                    t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t2.pldt_tpgp
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                    WHERE t4.lfcl_id=1 AND t3.issc_id= {$category_id} AND t1.site_id = {$site_id}
                    group by t4.id ,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t2.pldt_tpgp");
            }

            return ['items' => $items, 'country_id' => $country_id];
        }else{
            return response(['error' => 'Permission Denied'], 403);
        }

    }

    public function loadItem(Request $request){
        $country = Country::findOrFail($request->country_id);
        $country_id = $country->id ?? $request->country_id;
        $this->db = $country->cont_conn;
        $search_text = $request->search_text;


        $items=DB::connection($this->db)->select("SELECT t2.id, t2.amim_code,t2.amim_name FROM
				tl_sgit t1
                inner join tm_amim t2 on t1.amim_id = t2.id
                WHERE t2.lfcl_id=1 AND t2.amim_code LIKE '%{$search_text}%' OR t2.amim_name LIKE '%{$search_text}%'
                group by t2.id,t2.amim_code,t2.amim_name");
        return $items;
    }

    public function itemPrice(Request $request){

        $country = Country::findOrFail($request->country_id);
        $country_id = $country->id ?? $request->country_id;
        $this->db = $country->cont_conn;
        $amim_id = $request->amim_id;

        if($country_id == 2 || $country_id == 5){
            $items=DB::connection($this->db)->select("select t3.amim_id id,t3.pldt_tppr amim_price, t3.amim_duft amim_ctn_size 
                from tl_sgsm t1
                inner join tm_plmt t2 on t1.plmt_id = t2.id
                inner join tm_pldt t3 on t3.plmt_id = t2.id
                where t3.amim_id = {$amim_id} and t3.lfcl_id = 1
                group by t3.amim_id, t3.pldt_tppr, t3.amim_duft;");
            return $items;
        }else{
            $items=DB::connection($this->db)->select("select t2.amim_id id,t2.pldt_tppr unit_price, t2.amim_duft ctn_size 
                from tl_stcm t1
                inner join tm_pldt t2 on t2.plmt_id = t1.plmt_id
                where t2.amim_id = {$amim_id} and t2.lfcl_id = 1
                group by t2.amim_id, t2.pldt_tppr, t2.amim_duft");
            return $items;
        }
    }

    // BD and Advanced Promotion Page
    public function globalPromotion(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            return view('Order.tele_order_global')
                ->with('countries', $countries);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    // Check BD and Advanced Promotion
    public function checkGlobalPromotion(Request $request){
        $data['site_id']    = $request->site_id;
        $data['amim_id']    = $request->amim_id;
        $data['slgp_id']    = $request->slgp_id;
        $data['zone_id']    = $request->zone_id;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $data['country_id'] = $country->id ?? $request->country_id;

        if($request->country_id == 2 || $request->country_id == 5 || $request->country_id == 9)
        {
            try {
                $getAllPromotions = DB::connection($this->db)->select("select t1.id, t2.prdt_sitm, t2.prdt_mbqt, t2.prdt_mnbt 
                    , t2.prdt_fitm , t2.prdt_fiqt , t2.prdt_fipr , t2.prdt_disc  
                    from tm_prom t1
                    inner join tt_prdt t2 on t1.id = t2.prom_id
                    inner join tt_pznt t3 on t1.id = t2.prom_id
                    where prom_edat >= curdate() and t1.lfcl_id = 1
                    and t1.slgp_id = {$data['slgp_id']} and t3.zone_id= {$data['zone_id']}
                    and t2.prdt_sitm = {$data['amim_id']}
                    limit 1");


                if (count($getAllPromotions) > 0) {
                    $promotion = $getAllPromotions[0];

                    $different_free_item    = false;
                    $free_item              = [];
                    if($promotion->prdt_sitm != $promotion->prdt_fitm){
                        $free_item = DB::connection($this->db)->select("select t3.amim_id id, t4.amim_code, t4.amim_name,t3.pldt_tppr amim_price, t3.amim_duft amim_ctn_size 
                            from tl_sgsm t1
                            inner join tm_plmt t2 on t1.plmt_id = t2.id
                            inner join tm_pldt t3 on t3.plmt_id = t2.id
                            inner join tm_amim t4 on t3.amim_id = t4.id
                            where t3.amim_id = {$promotion->prdt_fitm} and t3.lfcl_id = 1
                            group by t3.amim_id, t4.amim_code, t4.amim_name,t3.pldt_tppr, t3.amim_duft");

                        $different_free_item = true;
                    }

                    return response([
                        'prom_type'             => 'Promotion',
                        'prom_id'               => $promotion->id,
                        'slub'                  => $promotion,
                        'free_item'             => $free_item[0] ?? [],
                        'different_free_item'   => $different_free_item
                    ], 200);
                }
            }
            catch (\Exception $exception)
            {
                return response(['error' => $exception->getMessage()], 401);
            }
        }
        else {
            try {
                $response = $this->advancedPromotionCalculation($data);

                if (isset($response['status']) && $response['status'] == 200) {
                    unset($response['status']);
                    return response($response, 200);
                } else {
                    unset($response['status']);
                    return response($response, 400);
                }
            } catch (\Exception $exception) {
                return response(['error' => $exception->getMessage()], 401);
            }
        }
        return response('no promotion', 400);
    }

    // BD and Advanced Promotion Calculation
    public function calculateGlobalPromotion(Request $request){
        $promotion_id   = $request->prom_id;
        $order_qty      = $request->order_qty;
        $order_ctn      = $request->order_ctn;
        $order_amnt     = $request->order_amnt;
        $site_id        = $request->site_id;
        $slgp_id        = $request->slgp_id;
        $country        = Country::findOrFail($request->country_id);
        $this->db       = $country->cont_conn;
        $country_id     = $country->id ?? $request->country_id;


        try {
            if ($country_id == 2 || $country_id == 5) {
                $promotion = DB::connection($this->db)->select("SELECT
                    prdt_sitm, prdt_mbqt, prdt_mnbt, prdt_fitm, prdt_fiqt, prdt_fipr, prdt_disc
                    FROM tt_prdt
                WHERE prom_id = {$promotion_id}");

                $promotion = $promotion[0];

                $free_item_info = $this->promCalculation($promotion_id, $order_qty);

                if($free_item_info['free_qty'] > 0){
                    $free_item = DB::connection($this->db)->select("select t4.id, t4.amim_code, t4.amim_name,t3.pldt_tppr, t3.amim_duft 
                        from tl_sgsm t1
                        inner join tm_plmt t2 on t1.plmt_id = t2.id
                        inner join tm_pldt t3 on t3.plmt_id = t2.id
                        inner join tm_amim t4 on t3.amim_id = t4.id
                        where t4.id = {$promotion->prdt_fitm} and t3.lfcl_id = 1 and t1.slgp_id = {$slgp_id}
                        group by t3.amim_id, t4.amim_code, t4.amim_name,t3.pldt_tppr, t3.amim_duft");
                }

                $free_item_info['prom_id']          = $promotion_id;
                $free_item_info['item']             = $free_item[0] ?? [];
                $free_item_info['promotion_item']   = $promotion->prdt_sitm;
                $free_item_info['type']             = 'promotion';
//                $free_item_info['item_info'] = $free_item_info[0];
//                $free_item_info['qfon'] = $promotion->prmr_qfon;
//                unset($free_item_info['promotion_item']);
                return $free_item_info;

            }
            else {
                $promotion = DB::connection($this->db)->table('tm_prmr')->find($promotion_id);

                if ($promotion->prmr_qfct == 'foc' && $promotion->prmr_qfon != 'CRT') {
                    $foc_info = $this->focCalculation($promotion_id, $order_qty);
                } elseif ($promotion->prmr_qfct == 'foc' && $promotion->prmr_qfon == 'CRT') {
                    $foc_info = $this->focCalculation($promotion_id, $order_ctn);
                } elseif ($promotion->prmr_qfct == 'discount' && $promotion->prmr_qfon == 'QTY') {
                    $discount = $this->discountCalculation($promotion_id, $order_qty, $order_amnt);
                } elseif ($promotion->prmr_qfct == 'discount' && $promotion->prmr_qfon == 'CRT') {
                    $discount = $this->discountCalculation($promotion_id, $order_ctn, $order_amnt);
                } elseif ($promotion->prmr_qfct == 'value') {
                    $value = $this->valueCalculation($promotion_id, $order_amnt);
                }

                if ($promotion->prmr_qfct == 'foc') {
                    if ($foc_info['free_qty'] > 0) {
                        $free_item_info = DB::connection($this->db)->select("SELECT
                        t4.id id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft
                        FROM tl_stcm t1
                        INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                        INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                        INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                        WHERE t4.lfcl_id=1 AND t3.amim_id in ({$foc_info['promotion_item']->id}) AND t1.site_id = {$site_id}
                        GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

                        $foc_info['type'] = 'foc';
                        $foc_info['prom_id'] = $promotion_id;
                        $foc_info['item_info'] = $free_item_info[0];
                        $foc_info['qfon'] = $promotion->prmr_qfon;
                        unset($foc_info['promotion_item']);
                    } else {
                        $foc_info['type'] = 'foc';
                    }

                    return $foc_info;
                }
                elseif ($promotion->prmr_qfct == 'discount') {
                    $discount['type'] = 'discount';
                    $discount['qfon'] = $promotion->prmr_qfon;
                    $discount['prom_id'] = $promotion_id;
                    return $discount;
                }
                elseif ($promotion->prmr_qfct == 'value') {
                    $value['type'] = 'value';
                    $value['qfon'] = $promotion->prmr_qfon;
                    $value['prom_id'] = $promotion_id;
                    return $value;
                } else {
                    return response(['No Promotion'], 404);
                }
            }
        }
        catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 401);
        }

    }

    // Advanced Promotion Calculation
    public function advancedPromotionCalculation($data){
        $site_id    = $data['site_id'];
        $amim_id    = $data['amim_id'];
        $country_id = $data['country_id'];

        try {
            $getAllPromotions = DB::connection($this->db)->select("SELECT t2.id, t2.prmr_qfct, t2.prmr_ditp, t2.prmr_qfon from tl_prsm t1
                        INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                        INNER JOIN tm_prmd t3 ON t2.id = t3.prmr_id
                        where t1.site_id={$site_id} and t3.amim_id={$amim_id}
                        and t2.prms_edat >= curdate() and t2.lfcl_id = 1");

            if(count($getAllPromotions) > 0){
                $promotion = $getAllPromotions[0];
                if($promotion->prmr_qfct == 'foc'){
                    $promotion_slub = DB::connection($this->db)->select("select t1.id, t1.prsb_fqty, t1.prsb_qnty from tm_prsb t1
                                where t1.prmr_id = {$promotion->id}");

                    $promotion_items = DB::connection($this->db)->select("SELECT
                                    t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft
                                    FROM tl_stcm t1
                                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                    WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                    inner join tm_amim t2 on t1.amim_id = t2.id
                                    where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                    GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");


                    return [
                        'prom_type' => strtoupper($promotion->prmr_qfct),
                        'prom_on'   => $promotion->prmr_qfon,
                        'slub'      => $promotion_slub,
                        'items'     => $promotion_items,
                        'prom_id'   => $promotion->id,
                        'status'    => 200
                    ];
                }
                elseif ($promotion->prmr_qfct == 'discount'){
                    $promotion_slub = DB::connection($this->db)->select("select t1.id, t2.prmr_ditp, t1.prsb_fqty, t1.prsb_qnty, t2.prmr_qfon
                                    from tm_prsb t1
                                    inner join tm_prmr t2 on t1.prmr_id = t2.id
                                    where t1.prmr_id = {$promotion->id}");

                    $promotion_items = DB::connection($this->db)->select("SELECT
                                    t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr amim_price,t4.amim_duft amim_ctn_size
                                    FROM tl_stcm t1
                                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                    WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                    inner join tm_amim t2 on t1.amim_id = t2.id
                                    where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                    GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

                    return [
                        'prom_type' => ucfirst($promotion->prmr_qfct),
                        'prom_on'   => $promotion->prmr_qfon,
                        'gift_on'   => $promotion->prmr_ditp,
                        'slub'      => $promotion_slub,
                        'items'     => $promotion_items,
                        'prom_id'   => $promotion->id,
                        'status'    => 200
                    ];
                }
                elseif ($promotion->prmr_qfct == 'value'){
                    $promotion_slub = DB::connection($this->db)->select("select t1.id, t1.prsb_famn, t1.prsb_disc
                                    from tm_prsb t1
                                    inner join tm_prmr t2 on t1.prmr_id = t2.id
                                    where t1.prmr_id = {$promotion->id}");

                    $promotion_items = DB::connection($this->db)->select("SELECT
                                    t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr amim_price,t4.amim_duft amim_ctn_size
                                    FROM tl_stcm t1
                                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                                    INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                                    WHERE t4.lfcl_id=1 AND t3.amim_id in (select t2.id from tm_prmd t1
                                    inner join tm_amim t2 on t1.amim_id = t2.id
                                    where t1.prmr_id = {$promotion->id}) AND t1.site_id = {$site_id}
                                    GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");

                    return [
                        'prom_type' => ucfirst($promotion->prmr_qfct),
                        'gift_on'   => $promotion->prmr_ditp,
                        'slub'      => $promotion_slub,
                        'items'     => $promotion_items,
                        'prom_id'   => $promotion->id,
                        'status'    => 200
                    ];
                }
                else{
                    return ['no promotion', 'status' => 400];
                }
            }
            else{
                return ['no promotion', 'status' => 400];
            }
        }
        catch (\Exception $exception)
        {
            return ['error' => $exception->getMessage(), 'status' => 401];
        }
    }

    public function loadNonProductive(Request $request){

        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;

        try {
            $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            if($request->country_id == 2 || $request->country_id == 5) {
                $nop_info = DB::connection($this->db)->select("select t1.id, t2.slgp_id,  t1.zone_id, t2.site_id, t1.aemp_id sr_id, t3.site_code, t3.site_name
                from tt_tltr t1
                inner join (select * from tt_npro WHERE npro_date=curdate() AND aemp_id={$employee_id}) t2 on t1.id = t2.attr4
                inner join tm_site t3 on t2.site_id = t3.id
                where t1.aemp_iusr = {$employee_id} and t1.tltr_date = curdate()");
            }else{
                $nop_info = DB::connection($this->db)->select("select t1.id, t2.slgp_id , t2.site_id, t2.dlrm_id, t1.aemp_id sr_id, t3.site_code, t3.site_name
                from tt_tltr t1
                inner join (select * from tt_npro WHERE npro_date=curdate() AND aemp_id={$employee_id}) t2 on t1.id = t2.attr4
                inner join tm_site t3 on t2.site_id = t3.id
                where t1.aemp_iusr = {$employee_id} and t1.tltr_date = curdate()");
            }
            return response($nop_info, 200);
        }catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 401);
        }

    }

    // store foc promotion pop up
    public function promotionNP(Request $request){
//        return response($request->all(), 400);

        DB::connection($this->db)->beginTransaction();

        try {
            $country = Country::findOrFail($request->country_id);

            $this->db = $country->cont_conn;

            $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            // counting total outlet visited in this route
            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id' => $request->rout_id,
            ])->count();

            // checking the Tele Marketing user's first visit or not
            $has_order = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'aemp_iusr' => $employee_id
            ])->first();



            // if first visit in running day then execute attendance store
            if (!$has_order) {
                $this->storeAttendance($request->all(), $employee_id);
            }

            // checking any tele-order exist in this site today
            $today_ordered = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'site_id'   => $request->outlet_id,
//                'aemp_iusr' => $employee_id
            ])->first();


            // if tele order exist then return back the req
            if(!is_null($today_ordered)){
                return response(['error' => 'Tele Order for this Site Already Exist', 'exist' => 1,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1], 403);
            }



            // Store data in Tracking table
            $data = $request->only(['non_p_or_order', 'outlet_id', 'rout_id', 'total', 'acmp_id', 'sr_id', 'country_id']);
            $teleOrderTrackingId = $this->teleOrderTracking($data);


//            return response([$request->all()],400);

            // checking country PRAN & RFL or other country
            if ($request->country_id == 2 || $request->country_id == 5) {
                $data = [];



                // checking multiple SR order exist or not
                if (isset($request->item_sr_ids)) {


                    // Order of multiple SR being filtered $data[SR] in these arrays
                    foreach ($request->item_sr_ids as $key => $sr_id) {
                        $data[$sr_id]['total']      = $request->total;
                        $data[$sr_id]['outlet_id']  = $request->outlet_id;
                        $data[$sr_id]['non_p_or_order'] = $request->non_p_or_order;
                        $data[$sr_id]['country_id'] = $request->country_id;
                        $data[$sr_id]['acmp_id'] = $request->acmp_id;
                        $data[$sr_id]['nopr_id'] = $request->nopr_id;
                        $data[$sr_id]['npro_note'] = $request->npro_note;
                        $data[$sr_id]['order_note'] = $request->order_note;
                        $data[$sr_id]['ntpe_id'] = $request->ntpe_id;
                        $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                        $data[$sr_id]['sr_id'] = $sr_id;
                        $data[$sr_id]['item_ids'][] = $request->item_ids[$key];
                        $data[$sr_id]['item_prices'][] = $request->item_prices[$key];
                        $data[$sr_id]['slgp_id'] = $request->item_slgp_ids[$key];
                        $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                        $data[$sr_id]['plmt_id'] = $request->item_plmt_ids[$key];
                        $data[$sr_id]['total_qtys'][] = $request->total_qtys[$key];
                        $data[$sr_id]['item_dufts'][] = $request->item_dufts[$key];
                        $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
                        $data[$sr_id]['dlrm_id'] = $this->getDistributorId($sr_id);
                    }



                    // Figuring out unique SR exist
                    $total_srs = array_unique($request->item_sr_ids);


                    // Storing information for each SR
                    foreach ($total_srs as $key => $sr_id) {

                        if (isset($data[$sr_id]['non_p_or_order']) && $data[$sr_id]['non_p_or_order'] == "1" && isset($data[$sr_id]['item_ids'])){

                            $data[$sr_id]['order_number'] = $this->getOrderNumber($data[$sr_id]['sr_id'], $data[$sr_id]['country_id'], $employee_id);

                            $order_store_id = $this->storeOrderMaster($data[$sr_id], $employee_id, $teleOrderTrackingId);

                            $this->storeOrderLine($data[$sr_id], $order_store_id, $employee_id);

                        }
                        else if(isset($data[$sr_id]['non_p_or_order']) && $data[$sr_id]['non_p_or_order'] == "0"){

                            if(isset($data[$sr_id]['nopr_id']) && !empty($data[$sr_id]['nopr_id'])) {
                                $non_productive = $this->storeNonProductiveOutlet($data[$sr_id], $employee_id, $teleOrderTrackingId);
                            }else{
                                return response(['error' => 'Please Provide Non Productive Reason'],402);
                            }
                        }
                        else{
                            return ['error' => 'Please Provide Order or Non Productive Reason',
                                'status' => 402];
                        }
                    }
                }
                else {

                    // when only one SR Order Exist then store Info
                    $data = $request->all();

                    if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                        $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                        $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                        $this->storeOrderLine($data, $order_store_id, $employee_id);
                    }
                    else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == 0){

                        if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                            $non_productive = $this->storeNonProductiveOutlet($data, $employee_id, $teleOrderTrackingId);
                        }else{
                            return response(['error' => 'Please Provide Non Productive Reason'],402);
                        }
                    }
                    else{
                        return ['error' => 'Please Provide Order or Non Productive Reason',
                            'status' => 402];
                    }
                }

            }
            else {

                // Storing information for oversea countries
                $data = $request->all();
                $data['sr_id'] = $employee_id;

                if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                    $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                    $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                    $this->storeOrderLine($data, $order_store_id, $employee_id);

                }
                else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){

                    if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
                        $non_productive = $this->storeNonProductiveOutlet($data, $employee_id, $teleOrderTrackingId);
                    }else{
                        return response(['error' => 'Please Provide Non Productive Reason'],402);
                    }
                }
                else{
                    return response(['error' => 'Please Order or Non Productive Reason'], 401);
                }
            }

            // Store information in ssvh table
            $data = $request->only(['non_p_or_order', 'outlet_id', 'country_id']);
            $this->storeSiteVisitStory($data, $employee_id);

            // Store information if any note exist
            if(!is_null($request->ntpe_id)) {
                $note_info = $request->only(['ntpe_id', 'order_note', 'outlet_id', 'country_id']);
                $note_info['employee_id'] = $employee_id;
                $this->storeNote($note_info);
            }

            DB::connection($this->db)->commit();

            // counting the outlet in current route after information stored
            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id' => $request->rout_id,
            ])->count();

            return response(['visited_outlet_in_rout' => $visited_outlet_in_rout+1], 200);
        }
        catch(\Exception $e)
        {
            DB::connection($this->db)->rollBack();

            return response([
                'error' => $e->getMessage(),
                'outlet_id' => $request->outlet_id], 401);
        }

    }

    public function nonProductiveStore(Request $request){
        $data = $request->all();

        if(isset($request->non_p_or_order) && $request->non_p_or_order == '1'){
            if($request->total == null || $request->total == 0.00){
                return response(['error' => 'Please Select an Item'], 400);
            }
        }

        DB::connection($this->db)->beginTransaction();

        try{

            $country = Country::findOrFail($request->country_id);
            $this->db = $country->cont_conn;
            $tracking_id = $request->tracking_id;
            $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            $nop_info = DB::connection($this->db)->select("select t2.id, t1.acmp_id, t2.slgp_id, t2.dlrm_id, t2.site_id, t2.rout_id
                    , t1.aemp_id sr_id
                    from tt_tltr t1
                    inner join (select * from tt_npro where npro_date=curdate() and aemp_id = {$employee_id}) t2 on t1.id = t2.attr4
                    where t1.id = {$tracking_id}");


            if(!is_null($nop_info)) {
                $info = $nop_info[0];

                $data['acmp_id']    = $request->acmp_id     = $info->acmp_id;
                $data['slgp_id']    = $request->slgp_id     = $info->slgp_id;
                $data['dlrm_id']    = $request->dlrm_id     = $info->dlrm_id;
                $data['outlet_id']  = $request->outlet_id   = $info->site_id;
                $data['sr_id']      = $request->sr_id       = $info->sr_id;
                $data['rout_id']    = $request->rout_id     = $info->rout_id;

                $teleOrderTrackingId = $tracking_id;

//        return response($data, 400);


                DB::connection($this->db)->table('tt_tltr')->where('id', $tracking_id)->update([
                    'tltr_ordr' => 1,
                    'tltr_amnt' => $request->total,
                    'tltr_dtim' => date('Y-m-d H:i:s')
                ]);

                DB::connection($this->db)->table('th_ssvh')->where([
                        'ssvh_date' => date('Y-m-d'),
                        'aemp_id'   => $employee_id,
                        'site_id'   => $request->outlet_id
                    ])->update(['ssvh_ispd' => 1]);

                $nonProductive = NonProductiveOutlet::on($this->db)->find($info->id);

                $nonProductive->delete();

                if ($request->country_id == 2 || $request->country_id == 5) {

                    if (isset($request->item_sr_ids)) {

                        $data = [];

                        foreach ($request->item_sr_ids as $key => $sr_id) {
                            $data[$sr_id]['total']      = $request->total;
                            $data[$sr_id]['outlet_id']  = $request->outlet_id;
                            $data[$sr_id]['non_p_or_order'] = $request->non_p_or_order;
                            $data[$sr_id]['country_id'] = $request->country_id;
                            $data[$sr_id]['acmp_id'] = $request->acmp_id;
                            $data[$sr_id]['nopr_id'] = $request->nopr_id;
                            $data[$sr_id]['npro_note'] = $request->npro_note;
                            $data[$sr_id]['order_note'] = $request->order_note;
                            $data[$sr_id]['ntpe_id'] = $request->ntpe_id;
                            $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                            $data[$sr_id]['sr_id'] = $sr_id;
                            $data[$sr_id]['item_ids'][] = $request->item_ids[$key];
                            $data[$sr_id]['item_prices'][] = $request->item_prices[$key];
                            $data[$sr_id]['slgp_id'] = $request->item_slgp_ids[$key];
                            $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
                            $data[$sr_id]['plmt_id'] = $request->item_plmt_ids[$key];
                            $data[$sr_id]['total_qtys'][] = $request->total_qtys[$key];
                            $data[$sr_id]['item_dufts'][] = $request->item_dufts[$key];
                            $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
                            $data[$sr_id]['item_discounts'][]   = $request->item_discounts[$key];
                            $data[$sr_id]['item_prom_ids'][$request->item_ids[$key]]    = $request->item_prom_ids[$request->item_ids[$key]] ?? 0;
                            $data[$sr_id]['dlrm_id'] = $this->getDistributorId($sr_id);
                        }


                        if (isset($request->free_item_ids)) {
                            foreach ($request->free_item_ids as $key => $free_item_id) {
                                $data[$key]['free_item_ids'][]           = $free_item_id;
                                $data[$key]['free_item_qtys'][]          = $request->free_item_qtys[$key];
                                $data[$key]['free_item_dufts'][]         = $request->free_item_dufts[$key];
                                $data[$key]['free_item_unit_prices'][]   = $request->free_item_unit_prices[$key];
                                $data[$key]['free_item_prom_ids'][]      = $request->free_item_prom_ids[$key];
                            }
                        }

                        $total_srs = array_unique($request->item_sr_ids);

                        foreach ($total_srs as $key => $sr_id) {

                            if (isset($data[$sr_id]['non_p_or_order']) && $data[$sr_id]['non_p_or_order'] == "1" && isset($data[$sr_id]['item_ids'])){

                                $data[$sr_id]['order_number'] = $this->getOrderNumber($data[$sr_id]['sr_id'], $data[$sr_id]['country_id'], $employee_id);

                                $order_store_id = $this->storeOrderMaster($data[$sr_id], $employee_id, $teleOrderTrackingId);

                                $this->storeOrderLine($data[$sr_id], $order_store_id, $employee_id);

                            }
                        }
                    }
                    else {

                        if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                            $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                            $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                            $this->storeOrderLine($data, $order_store_id, $employee_id);
                        }
                    }

                }
                else {
                    $data['sr_id'] = $employee_id;

                    if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){

                        $data['order_number'] = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);

                        $order_store_id = $this->storeOrderMaster($data, $employee_id, $teleOrderTrackingId);

                        $this->storeOrderLine($data, $order_store_id, $employee_id);

                    }
                }

                if(!is_null($request->ntpe_id)) {
                    $note_info['ntpe_id']       = $data['ntpe_id'];
                    $note_info['order_note']    = $data['order_note'] ?? '';
                    $note_info['outlet_id']     = $data['outlet_id'];
                    $note_info['country_id']    = $data['country_id'];
                    $note_info['employee_id']   = $employee_id;
                    $this->storeNote($note_info);
                }
            }

            DB::connection($this->db)->commit();

            return response('Order Stored Successfully', 200);
        }
        catch(\Exception $e)
        {
            DB::connection($this->db)->rollBack();

            return response([
                'error' => $e->getMessage(),
//                    'error' => 'Order Storing Failed',
                'outlet_id' => $request->outlet_id], 401);
        }
    }

    public function loadOrderedOutlets(Request $request){
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

        $outlets = DB::connection($this->db)->select("select t2.id, t2.site_name, t2.site_code from tt_tltr t1
            inner join tm_site t2 on t1.site_id = t2.id
            where t1.tltr_date = curdate() and t1.aemp_iusr = {$employee_id} and t1.tltr_ordr = 1");

        return response([
            'outlets'   => $outlets
        ], 200);
    }

    public function loadOrderHistory(Request $request){
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;


        $site_id = $request->outlet_id;
        $code = '880';
        $digit = -10;

        if ($request->country_id == 14) {
            $digit = -9;
            $code = '60';
        }
        elseif ($request->country_id == 15){
            $digit=-9;
            $code='996';
        }

        $outlet_info = DB::connection($this->db)->select("
                SELECT t3.id,t3.site_code, t3.site_name,
	                t6.than_name 'thana', t7.dsct_name 'district', 
	                CONCAT({$code},SUBSTRING(REPLACE(t3.site_mob1,'-',''),{$digit})) 'mobile'
                FROM tl_rpln t1
	            INNER JOIN tl_rsmp t2 on t1.rout_id = t2.rout_id
                INNER JOIN tm_site t3 on t2.site_id = t3.id
                INNER JOIN tm_mktm t4 on t3.mktm_id = t4.id
                INNER JOIN tm_ward t5 on t4.ward_id = t5.id
                INNER JOIN tm_than t6 on t5.than_id = t6.id
                INNER JOIN tm_dsct t7 on t6.dsct_id = t7.id
                and t3.id = {$site_id}
                group by t3.id,t3.site_code, t3.site_name,
	                t6.than_name, t7.dsct_name");

//                where t1.aemp_id = {$employee_id}

        if(count($outlet_info) == 0){
            return response(['error' => 'Invalid Site Code'], 403);
        }

        $orders = DB::connection($this->db)->select("SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt, t1.attr4 tltr_id,
            t2.lfcl_name 'order_status', t1.slgp_id, t1.dlrm_id, t1.aemp_id sr_id
            FROM tt_ordm t1
            inner join tm_lfcl t2 on t1.lfcl_id = t2.id
            WHERE t1.aemp_id = {$employee_id} AND t1.site_id= {$site_id} and t1.lfcl_id in (1, 2, 17) AND t1.attr8 = 10 and t1.ordm_date = curdate()");

        $order_info = [];
        if(count($orders) == 1) {
            $order_info = DB::connection($this->db)->select("SELECT t1.id ordd_id,t1.amim_id, t1.ordd_inty, t1.ordd_opds, t1.ordd_spdi,
                t1.ordd_duft, t1.prom_id, t1.ordd_uprc, t1.ordd_oamt, t2.amim_code, t2.amim_name, t1.ordd_smpl
                FROM tt_ordd t1
                INNER JOIN tm_amim t2 on t1.amim_id = t2.id
                WHERE t1.ordm_id = {$orders[0]->id}");
        }

        $grv_info = [];
        if(count($orders) == 1) {
            $grv_info = DB::connection($this->db)->select("SELECT t1.id rtan_id,t3.amim_code, t3.amim_name, t2.amim_id, t2.rtdd_qnty, t2.rtdd_duft,
                t2.rtdd_uprc, t2.rtdd_oamt
                FROM tt_rtan t1
                inner join tt_rtdd t2 on t1.id = t2.rtan_id
                inner join tm_amim t3 on t2.amim_id = t3.id
                WHERE t1.aemp_id = {$employee_id} AND t1.site_id= {$site_id} and t1.lfcl_id = 1
                AND t1.attr4 = {$orders[0]->tltr_id} and t1.rtan_drdt = curdate();");
        }

        return response([
            'outlet_info'   => $outlet_info,
            'order_info'    => $order_info,
            'orders'        => $orders,
            'grv_info'      => (count($grv_info) > 0) ? $grv_info : [],
        ], 200);
    }

    public function loadTeleOrderDetails(Request $request){
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

        $data = [];
        $order_details = DB::connection($this->db)->select("select t2.slgp_id, t2.site_id, t2.dlrm_id,t1.* from tt_ordd t1
            inner join tt_ordm t2 on t1.ordm_id = t2.id
            where t1.ordm_id = {$request->ordm_id}");

        if($request->country_id != 2 && $request->country_id != 5 && count($order_details) > 0){
            $order_info = $order_details[0];
            $categories = DB::connection($this->db)->select("SELECT
                    t4.id ,t4.issc_code cat_code,t4.issc_name cat_name ,t1.plmt_id
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_issc t4 ON t3.issc_id=t4.id
                    INNER JOIN tm_amim t5 ON t2.amim_id=t5.id
                    WHERE t4.lfcl_id=1 AND t3.slgp_id = {$order_info->slgp_id} AND t1.site_id = {$order_info->site_id}
                    AND t5.lfcl_id = 1
                    GROUP BY t4.id ,t4.issc_code,t4.issc_name ,t1.plmt_id");

            $data = ['categories' => $categories, 'country_id' => $request->country_id];
        }
        $data['order_details'] = $order_details;

        return $data;
    }

    public function getMspData(Request $request){
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $site_id = $request->outlet_id;

        return DB::connection($this->db)->select('CALL getMSPDATA(?)', [$site_id]);

    }

    public function updateOrder(Request $request){

//        return response([$request->all()], 400);
        $country = Country::findOrFail($request->country_id);

        $this->db = $country->cont_conn;

        $data = $request->all();

        DB::connection($this->db)->beginTransaction();

        try {
            $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

            $order_master = OrderMaster::on($this->db)->find($request->ordm_id);

            $data['order_number'] = $order_master->ordm_ornm;

            $order_master->update([
                'ordm_amnt' => array_sum($request->item_prices),
                'lfcl_id' => $this->getOrderLifeCycle(
                    isset($data['has_special_discount']) && $data['has_special_discount'] == '1' ? 1 : 0,
                    $data['country_id'], $data['slgp_id'], $data['outlet_id']),
                'ordm_icnt' => count($data['item_ids'])
            ]);

            OrderLine::on($this->db)->where('ordm_id', $request->ordm_id)->delete();

            $this->storeOrderLine($data, $order_master->id, $employee_id);

            if(isset($data['grv_exist']) && $data['grv_exist'] == '1' && isset($data['grv_id'])){
                $grv_return = ReturnMaster::on($this->db)->findOrFail($data['grv_id']);

                if($grv_return && isset($data['grv_item_ids'])){
                    $grv_return->update([
                        'rtan_amnt' => array_sum($data['grv_item_prices']),
                        'rtan_icnt' => count($data['grv_item_ids'])
                    ]);
                }elseif($grv_return){
                    ReturnLine::on($this->db)->where('rtan_id', $request->grv_id)->delete();
                    $grv_return->delete();
                }

                ReturnLine::on($this->db)->where('rtan_id', $request->grv_id)->delete();


                if(isset($data['grv_item_ids']) && count($data['grv_item_ids']) > 0) {
                    $this->storeGrvLine($data, $request->grv_id, $employee_id);
                }
            }

            if(isset($data['grv_item_ids']) && count($data['grv_item_ids']) > 0  && !isset($data['grv_id'])) {
                $data['acmp_id'] = $order_master->acmp_id;
                $data['sr_id'] = $order_master->aemp_id;
                $data['rout_id'] = $order_master->rout_id;
                $data['dlrm_id'] = $order_master->dlrm_id;
                $grv_id = $this->storeGrvMaster($data, $employee_id, $order_master->attr4);

                $this->storeGrvLine($data, $grv_id, $employee_id);
            }

            DB::connection($this->db)->commit();

            return response(['Order Updated Successfully'], 200);
        }
        catch(\Exception $e)
        {
            DB::connection($this->db)->rollBack();
            return response($e->getMessage(), 400);
            return response(['Invalid Information'], 400);
        }

    }

    public function getOrderAsPdf(Request $request){
        $digit=-10;
        $code='91';
        $country = Country::findOrFail($request->country_id);

        $this->db = $country->cont_conn;

        $order_items = DB::connection($this->db)->select("select t1.ordm_ornm, t1.ordm_date, t1.ordm_icnt, t1.ordm_amnt, t3.amim_name,
            t4.acmp_name, t4.acmp_code, t2.ordd_inty, t2.ordd_oamt, t2.ordd_spdo, t2.ordd_uprc, t5.aemp_name, t5.aemp_usnm, t6.site_name,
            t7.dlrm_code, t7.dlrm_name, t8.slgp_code, t8.slgp_name, t2.ordd_duft, t6.site_code, t3.amim_code, t1.ordm_drdt,
            CONCAT({$code},SUBSTRING(REPLACE(t6.site_mob1,'-',''),{$digit})) 'mobile'
            from tt_ordm t1
            inner join tt_ordd t2 on t2.ordm_id = t1.id
            inner join tm_amim t3 on t2.amim_id = t3.id
            inner join tm_acmp t4 on t1.acmp_id = t4.id
            inner join tm_aemp t5 on t1.aemp_id = t5.id
            inner join tm_site t6 on t1.site_id = t6.id
            inner join tm_dlrm t7 on t1.dlrm_id = t7.id
            inner join tm_slgp t8 on t1.slgp_id = t8.id
            where t1.id = $request->order_id");


        $order_pdf = PDF::loadView('Order.tele_order_pdf', compact('order_items'))->setPaper('a4', 'landscape');

        return $order_pdf->download('invoice.pdf');
    }

    public function getTeleOrderPdf($order_id){

        $digit=-10;
        $code='91';

        $country = Country::findOrFail(9);

        $this->db = $country->cont_conn;

        $order_items = DB::connection($this->db)->select("select t1.ordm_ornm, t1.ordm_date, t1.ordm_icnt, t1.ordm_amnt, t3.amim_name,
            t4.acmp_name, t4.acmp_code, t2.ordd_inty, t2.ordd_oamt, t2.ordd_spdo, t2.ordd_uprc, t5.aemp_name, t5.aemp_usnm, t6.site_name,
            t7.dlrm_code, t7.dlrm_name, t8.slgp_code, t8.slgp_name, t2.ordd_duft, t6.site_code, t3.amim_code, t1.ordm_drdt,
            CONCAT({$code},SUBSTRING(REPLACE(t6.site_mob1,'-',''),{$digit})) 'mobile'
            from tt_ordm t1
            inner join tt_ordd t2 on t2.ordm_id = t1.id
            inner join tm_amim t3 on t2.amim_id = t3.id
            inner join tm_acmp t4 on t1.acmp_id = t4.id
            inner join tm_aemp t5 on t1.aemp_id = t5.id
            inner join tm_site t6 on t1.site_id = t6.id
            inner join tm_dlrm t7 on t1.dlrm_id = t7.id
            inner join tm_slgp t8 on t1.slgp_id = t8.id
            where t1.id = {$order_id}");

        $order_pdf = PDF::loadView('Order.tele_order_pdf', compact('order_items'))->setPaper('a4', 'landscape');

        return $order_pdf->download('invoice.pdf');
    }

    public function giveAttendance()
    {
        try {
            $exist = Attendance::on($this->db)->where([
                'aemp_id'       => $this->aemp_id,
                'attn_date'     => date('Y-m-d'),
                'atten_atyp'    => 1
            ])->first();

            if(!$exist) {

                $attendance_info = [
                    'slgp_id' => '',
                    'aemp_id' => $this->aemp_id,
                    'site_id' => '',
                    'site_name' => $site_info->site_name ?? '',
                    'geo_lat' => $site_info->geo_lat ?? '',
                    'geo_lon' => $site_info->geo_lon ?? '',
                    'attn_time' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                    'attn_date' => date('Y-m-d'),
                    'attn_mont' => 1,
                    'atten_type' => 1,
                    'atten_atyp' => 1,
                    'rout_id' => '',
                    'attn_fdat' => date('Y-m-d'),
                    'attn_tdat' => date('Y-m-d'),
                    'cont_id' => $this->cont_id,
                    'lfcl_id' => 1,
                    'aemp_iusr' => $this->aemp_id,
                    'aemp_eusr' => $this->aemp_id,
                    'attn_imge' => '',
                    'attn_rmak' => '',
                    'var' => 1,
                    'attr1' => '',
                    'attr2' => '',
                    'attr3' => 0,
                    'attr4' => 0,
                ];

                Attendance::on($this->db)->insert($attendance_info);
            }
            return response('Success', 200);

        }catch(\Exception $exception){
            return response('Invalid Informtion', 400);
        }
    }
}
