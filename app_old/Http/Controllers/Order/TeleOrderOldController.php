<?php

namespace App\Http\Controllers\Order;

use App\BusinessObject\Attendance;
use App\BusinessObject\NonProductiveOutlet;
use App\BusinessObject\Note;
use App\BusinessObject\OrderLine;
use App\BusinessObject\OrderMaster;
use App\BusinessObject\OrderSequence;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\SiteVisited;
use App\BusinessObject\TeleOrderTracking;
use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Employee;
use App\MasterData\NoOrderReason;
use App\MasterData\NoteType;
use App\MasterData\Site;
use App\MasterData\Zone;
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
use function GuzzleHttp\Psr7\try_fopen;

class TeleOrderController extends Controller
{
    private $access_key = 'tele/order';
    private $currentUser;
    private $userMenu;
    private $db;
    private $aemp_id;
    private $cont_id;
    public $free_item = 0;


    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->aemp_id = Auth::user()->employee()->id;
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


    public function siteInfo($site_id){
        return Site::on($this->db)->find($site_id);
    }


    public function index(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $countries = Country::get(['id', 'cont_name']);

            return view('Order.tele_order_promotion')
                ->with('countries', $countries);
//            return view('Order.tele_order')
//                ->with('countries', $countries);
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
                    }elseif ($promotion->prmr_qfct == 'discount' ){
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
                    }elseif ($promotion->prmr_qfct == 'discount' ){
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

    public function getFocCalculation(Request $request){
        $promotion_id = $request->prom_id;
        $order_qty = $request->order_qty;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $country_id = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){

        }else {
            $foc_info = $this->focCalculation($promotion_id, $order_qty);

            $foc_info['prom_id'] = $promotion_id;

            return $foc_info;
        }

    }

    public function focCalculation($promotion_id, $order_qty){
        $promotion_item = DB::connection($this->db)->select("SELECT t3.id, t3.amim_name, t3.amim_code, t1.prsb_fqty, t1.prsb_qnty
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


                $has_order = TeleOrderTracking::on($this->db)->where([
                    'tltr_date'     => date('y-m-d'),
                    'atten_atyp'    => 1,
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
//    public function promotionStore(Request $request){
//        $info = [];
//        $country = Country::findOrFail($request->country_id);
//
//        $this->db = $country->cont_conn;
//        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;
//
//
//        if($request->country_id == 2 || $request->country_id == 5){
//            $data = [];
//
//
//            if(isset($request->item_sr_ids)) {
//
//                foreach ($request->item_sr_ids as $key => $sr_id) {
//                    $data[$sr_id]['total'] = $request->total;
//                    $data[$sr_id]['outlet_id'] = $request->outlet_id;
//                    $data[$sr_id]['non_p_or_order'] = $request->non_p_or_order;
//                    $data[$sr_id]['country_id'] = $request->country_id;
//                    $data[$sr_id]['acmp_id'] = $request->acmp_id;
//                    $data[$sr_id]['nopr_id'] = $request->nopr_id;
//                    $data[$sr_id]['npro_note'] = $request->npro_note;
//                    $data[$sr_id]['order_note'] = $request->order_note;
//                    $data[$sr_id]['ntpe_id'] = $request->ntpe_id;
//                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
//                    $data[$sr_id]['sr_id'] = $sr_id;
//                    $data[$sr_id]['item_ids'][] = $request->item_ids[$key];
//                    $data[$sr_id]['item_prices'][] = $request->item_prices[$key];
//                    $data[$sr_id]['slgp_id'] = $request->item_slgp_ids[$key];
//                    $data[$sr_id]['rout_id'] = $request->item_rout_ids[$key];
//                    $data[$sr_id]['plmt_id'] = $request->item_plmt_ids[$key];
//                    $data[$sr_id]['total_qtys'][] = $request->total_qtys[$key];
//                    $data[$sr_id]['item_dufts'][] = $request->item_dufts[$key];
//                    $data[$sr_id]['item_unit_prices'][] = $request->item_unit_prices[$key];
//                    $data[$sr_id]['dlrm_id'] = $this->getDistributorId($sr_id);
//                }
//
//                $total_srs = array_unique($request->item_sr_ids);
//
//                foreach ($total_srs as $key => $sr_id) {
//                    $info = $this->storeOrder($data[$sr_id]);
//                }
//            }else{
//                $info = $this->storeOrder($request->all());
//            }
//
//        }else{
//            $data = $request->all();
//            $data['sr_id'] = $employee_id;
//            $info = $this->storePromotionOrder($data);
//        }
//
//        if($info['status'] == 200) {
//            DB::connection($this->db)->beginTransaction();
//
//            try {
//                $has_order = TeleOrderTracking::on($this->db)->where([
//                    'tltr_date' => date('y-m-d'),
//                    'rout_id'   => $request->rout_id,
//                    'aemp_iusr' => $employee_id
//                ])->first();
//
//                if(!$has_order){
//                    $site_info = $this->siteInfo($request->outlet_id);
//
//                    $attendance_info = [
//                        'slgp_id'   => $request->slgp_id,
//                        'aemp_id'   => $employee_id,
//                        'site_id'   => $request->outlet_id,
//                        'site_name' => $site_info->site_name ?? '',
//                        'geo_lat'   => $site_info->geo_lat ?? '',
//                        'geo_lon'   => $site_info->geo_lon ?? '',
//                        'attn_time' => date('Y-m-d H:i:s'),
//                        'attn_date' => date('Y-m-d'),
//                        'attn_mont' => 1,
//                        'atten_type' => 1,
//                        'atten_atyp' => 1,
//                        'rout_id'   => $request->rout_id,
//                        'attn_fdat'   =>  date('Y-m-d'),
//                        'attn_tdat'   =>  date('Y-m-d'),
//                        'cont_id'   => $request->country_id,
//                        'lfcl_id'   => 1,
//                        'aemp_iusr' => $employee_id,
//                        'aemp_eusr' => $employee_id,
//                        'attn_imge' => '',
//                        'attn_rmak' => '',
//                        'var' => 1,
//                        'attr1' => '',
//                        'attr2' => '',
//                        'attr3' => 0,
//                        'attr4' => 0,
//                    ];
//
//                    Attendance::on($this->db)->insert($attendance_info);
//                }
//
//                SiteVisited::on($this->db)->insert([
//                    'ssvh_date' => date('y-m-d'),
//                    'aemp_id'   => $employee_id,
//                    'site_id'   => $request->outlet_id,
//                    'ssvh_ispd' => $request->non_p_or_order,
//                    'cont_id'   => $request->country_id,
//                    'lfcl_id'   => 1,
//                    'aemp_iusr' => $employee_id,
//                    'aemp_eusr' => $employee_id,
//                ]);
//
//                $note_info = $request->only(['ntpe_id','order_note','outlet_id','country_id']);
//                $note_info['employee_id'] = $employee_id;
//
//                $this->storeNote($note_info);
//
//                $data = $request->only(['non_p_or_order','outlet_id','rout_id','total','acmp_id', 'sr_id', 'country_id']);
//
//                $teleOrderTrackingId = $this->teleOrderTracking($data);
//
//                if(isset($info['order_id'])){
//                    DB::connection($this->db)->table('tt_ordm')->where('id', $info['order_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
//                }elseif (isset($info['non_p_id'])){
//                    DB::connection($this->db)->table('tt_npro')->where('id', $info['non_p_id'])->update(['attr4' => $teleOrderTrackingId ?? 0]);
//                }
//
//                DB::connection($this->db)->commit();
//            }catch(\Exception $e)
//            {
//                DB::connection($this->db)->rollBack();
//
//                return response([
//                'error' => 'Order Storing Failed',
//                'outlet_id' => $data['outlet_id']], 401);
//            }
//
//        }
//
//
//        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
//            'tltr_date' => date('y-m-d'),
//            'rout_id' => $request->rout_id,
//        ])->count();
//
//        $info['visited_outlet_in_rout'] = $visited_outlet_in_rout+1;
//
//        return response($info, $info['status']);
//    }

    public function promotionNew(Request $request){
        $info = [];
        $country = Country::findOrFail($request->country_id);

        $this->db = $country->cont_conn;
        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

//        return response(['info' => $request->all()],400);

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


                $has_order = TeleOrderTracking::on($this->db)->where([
                    'tltr_date'     => date('y-m-d'),
                    'atten_atyp'    => 1,
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

                $note_info = $request->only(['ntpe_id','order_note','outlet_id','country_id']);
                $note_info['employee_id'] = $employee_id;

                $this->storeNote($note_info);

                $data = $request->only(['non_p_or_order','outlet_id','rout_id','total','acmp_id', 'sr_id', 'country_id']);
//            $data['sr_id'] = $employee_id;
                $this->teleOrderTracking($data);

                DB::connection($this->db)->commit();
            }catch(\Exception $e)
            {
                DB::connection($this->db)->rollBack();

                return response([
                'error' => $e->getMessage(),
//                'error' => 'Order Storing Failed',
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

    public function checkFocPromotion(Request $request){
        $site_id    = $request->site_id;
        $amim_id    = $request->amim_id;
        $country = Country::findOrFail($request->country_id);
        $this->db = $country->cont_conn;
        $country_id = $country->id ?? $request->country_id;

        if($country_id == 2 || $country_id == 5){
            return response('no foc promotion', 400);
        }else {
            try {
                $getAllPromotions = DB::connection($this->db)->select("SELECT t2.id, t2.prmr_qfct, t2.prmr_ditp from tl_prsm t1
                    INNER JOIN tm_prmr t2 ON t1.prmr_id = t2.id
                    INNER JOIN tm_prmd t3 ON t2.id = t3.prmr_id
                    where t1.site_id={$site_id} and t3.amim_id={$amim_id}
                    and t2.prms_edat >= curdate() and t2.lfcl_id = 1");

                if(count($getAllPromotions) > 0){
                    $promotion = $getAllPromotions[0];
                    if($promotion->prmr_qfct == 'foc' ){
                        $promotion_slub = DB::connection($this->db)->select("select t1.id, t1.prsb_fqty, t1.prsb_qnty from tm_prsb t1
	                        where t1.prmr_id = {$promotion->id}");

                        $promotion_items = DB::connection($this->db)->select("select t2.id, t2.amim_code, t2.amim_name from tm_prmd t1
                            inner join tm_amim t2 on t1.amim_id = t2.id
                            where t1.prmr_id = {$promotion->id}");

                        return [
                            'slub' => $promotion_slub,
                            'items' => $promotion_items,
                            'prom_id' => $promotion->id
                        ];
                    }else{
                        return response('no foc promotion', 400);
                    }
                }
            }catch (\Exception $exception)
            {
                return response(['error' => $exception->getMessage()], 401);
            }
        }

        return response('no foc promotion', 400);

    }

//    public function storePromotionOrder($data){
//        $country = Country::findOrFail($data['country_id']);
//
//        $this->db = $country->cont_conn;
//
//        $employee_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;
//
//        $ordered = true;
//
//        $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
//            'tltr_date' => date('y-m-d'),
//            'rout_id' => $data['rout_id'],
//        ])->count();
//
//        try {
//
//            $today_ordered = TeleOrderTracking::on($this->db)->where([
//                'tltr_date' => date('y-m-d'),
//                'site_id' => $data['outlet_id'],
//                'aemp_id' => $data['sr_id']
//            ])->first();
//
//
//            if($today_ordered){
//                return response(['error' => 'This Tele Order Already Exist', 'exist' => 1,
//                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1], 403);
//            }
//            else{
//                DB::connection($this->db)->beginTransaction();
//
//                if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){
//
//
//                    $order_number = $this->getOrderNumber($data['sr_id'], $data['country_id'], $employee_id);
//
//                    $info = [
//                        'ordm_ornm' => $order_number,
//                        'ordm_amnt' => array_sum($data['item_prices']),
//                        'ordm_note' => 'tele order',
//                        'cont_id'   => $data['country_id'],
//                        'acmp_id'   => $data['acmp_id'],
//                        'aemp_id'   => $data['sr_id'],
//                        'slgp_id'   => $data['slgp_id'],
//                        'rout_id'   => $data['rout_id'],
//                        'site_id'   => $data['outlet_id'],
//                        'ordm_date' => date('Y-m-d'),
//                        'ordm_time' => date('Y-m-d H:i:s'),
//                        'ordm_drdt' => date('Y-m-d', strtotime('+1 day')),
//                        'ordm_dltm' => date('Y-m-d H:i:s', strtotime('+1 day')),
//                        'dlrm_id'   => $data['dlrm_id'],
//                        'ordm_icnt' => count($data['item_ids']),
//                        'plmt_id'   => $data['plmt_id'],
//                        'aemp_iusr' => $employee_id,
//                        'aemp_eusr' => $employee_id,
//                        'lfcl_id'   => 1,
//                        'odtp_id'   => 1, // order type
//                        'ocrs_id'   => 0, // order cancel reason id
//                        'mspm_id'   => 0, // must sell product id
//                        'aemp_cusr' => 0, // order cancel user id
//                        'geo_lat'   => 0,
//                        'geo_lon'   => 0,
//                        'ordm_dtne' => 0,
//                        'ordm_pono' => '',
//                        'attr8' => 10
//                    ];
//
//                    $order_store_id = OrderMaster::on($this->db)->insertGetId($info);
//
//                    $order_details = [];
//
//                    foreach ($data['item_ids'] as $key => $id){
//                        $order_details[] = [
//                            'ordm_id' => $order_store_id,
//                            'ordm_ornm' => $order_number,
//                            'amim_id' => $id,
//                            'ordd_inty' => $data['total_qtys'][$key],
//                            'ordd_qnty' => $data['total_qtys'][$key],
//                            'ordd_oamt' => $data['item_prices'][$key],
//                            'ordd_amnt' => $data['item_prices'][$key],
//                            'ordd_duft' => $data['item_dufts'][$key],
//                            'ordd_uprc' => $data['item_unit_prices'][$key],
//                            'cont_id'   => $data['country_id'],
//                            'lfcl_id'   => 1,
//                            'aemp_iusr' => $employee_id,
//                            'aemp_eusr' => $employee_id,
//                            'ordd_cqty' => 0,
//                            'ordd_dqty' => 0,
//                            'ordd_rqty' => 0,
//                            'ordd_opds' => $data['item_discounts'][$key] ?? 0,
//                            'ordd_cpds' => 0,
//                            'ordd_dpds' => 0,
//                            'ordd_spdo' => 0,
//                            'ordd_spdc' => 0,
//                            'ordd_spdd' => 0,
//                            'ordd_spdi' => 0,
//                            'ordd_runt' => 1,
//                            'ordd_dunt' => 1,
//                            'ordd_smpl' => 0,
//                            'prom_id'   => $data['item_prom_ids'][$id] ?? 0,
//                            'ordd_ocat' => 0,
//                            'ordd_odat' => 0,
//                            'ordd_excs' => 0,
//                            'ordd_ovat' => 0,
//                            'ordd_tdis' => 0,
//                            'ordd_texc' => 0,
//                            'ordd_tvat' => 0,
//                            'ordd_dfdo' => 0,
//                            'ordd_dfdc' => 0,
//                            'ordd_dfdd' => 0
//                        ];
//                    }
//
//                    foreach ($data['free_item_ids'] as $key => $id){
//                        $order_details[] = [
//                            'ordm_id'   => $order_store_id,
//                            'ordm_ornm' => $order_number,
//                            'amim_id'   => $id,
//                            'ordd_inty' => $data['free_item_qtys'][$key],
//                            'ordd_qnty' => $data['free_item_qtys'][$key],
//                            'ordd_oamt' => 0,
//                            'ordd_amnt' => 0,
//                            'ordd_duft' => 0,
//                            'ordd_uprc' => 0,
//                            'cont_id'   => $data['country_id'],
//                            'lfcl_id'   => 1,
//                            'aemp_iusr' => $employee_id,
//                            'aemp_eusr' => $employee_id,
//                            'ordd_cqty' => 0,
//                            'ordd_dqty' => 0,
//                            'ordd_rqty' => 0,
//                            'ordd_opds' => 0,
//                            'ordd_cpds' => 0,
//                            'ordd_dpds' => 0,
//                            'ordd_spdo' => 0,
//                            'ordd_spdc' => 0,
//                            'ordd_spdd' => 0,
//                            'ordd_spdi' => 0,
//                            'ordd_runt' => 1,
//                            'ordd_dunt' => 1,
//                            'ordd_smpl' => 1,
//                            'prom_id'   => $data['free_item_prom_ids'][$key],
//                            'ordd_ocat' => 0,
//                            'ordd_odat' => 0,
//                            'ordd_excs' => 0,
//                            'ordd_ovat' => 0,
//                            'ordd_tdis' => 0,
//                            'ordd_texc' => 0,
//                            'ordd_tvat' => 0,
//                            'ordd_dfdo' => 0,
//                            'ordd_dfdc' => 0,
//                            'ordd_dfdd' => 0
//                        ];
//                    }
//
//                    OrderLine::on($this->db)->insert($order_details);
//
//                }
//                else if(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){
//
//                    if(isset($data['nopr_id']) && !empty($data['nopr_id'])) {
//                        $ordered = false;
//                        $non_productive_info = [
//                            'aemp_id' => $data['sr_id'],
//                            'slgp_id' => $data['slgp_id'],
//                            'site_id' => $data['outlet_id'],
//                            'rout_id' => $data['rout_id'],
//                            'nopr_id' => $data['nopr_id'],
//                            'dlrm_id' => $data['dlrm_id'],
//                            'npro_note' => $data['npro_note'] ?? 'Non Productive Tele Order',
//                            'npro_date' => date('Y-m-d'),
//                            'npro_time' => date('Y-m-d H:i:s'),
//                            'npro_dtne' => 0,
//                            'geo_lat' => 0,
//                            'geo_lon' => 0,
//                            'cont_id'   => $data['country_id'],
//                            'lfcl_id' => 1,
//                            'aemp_iusr' => $employee_id,
//                            'aemp_eusr' => $employee_id
//                        ];
//
//                        $non_productive = NonProductiveOutlet::on($this->db)->insertGetId($non_productive_info);
//                    }else{
//                        return ['error' => 'Please Provide Non Productive Reason',
//                            'status' => 402
//                        ];
//                    }
//                }else{
//                    return ['error' => 'Please Provide Order or Non Productive Reason',
//                        'status' => 402];
//                }
//
//                DB::connection($this->db)->commit();
//
//            }
//
//        }catch (\Exception $exception)
//        {
//            DB::connection($this->db)->rollback();
//
//            return [
////                'error' => 'Order Storing Failed',
//                'error' => $exception->getMessage(),
//                'outlet_id' => $data['outlet_id'],
//                'status' => 401
//            ];
//        }
//
//        if (isset($data['non_p_or_order']) && $data['non_p_or_order'] == "1" && isset($data['item_ids'])){
//
//            return [
//                'success'   => 'Order Stored Successfully',
//                'outlet_id' => $data['outlet_id'],
//                'order_id'  => $order_store_id,
//                'status'    => 200
//            ];
//
//        }elseif(isset($data['non_p_or_order']) && $data['non_p_or_order'] == "0"){
//
//            return [
//                'success' => 'Order Stored Successfully',
//                'outlet_id' => $data['outlet_id'],
//                'non_p_id' => $non_productive,
//                'status' => 200
//            ];
//        }
//
//        return [
//            'success' => 'Order Stored Successfully',
//            'outlet_id' => $data['outlet_id'],
//            'status' => 200
//        ];
//    }


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
                            'prom_id'   => $data['free_item_prom_ids'][$key],
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
                'error' => 'Order Storing Failed',
//                'error' => $exception->getMessage(),
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
                            'aemp_id' => $data['sr_id'],
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
                }else{
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
            'site_id' => $data['outlet_id'],
            'aemp_id' => $data['sr_id'],
            'rout_id' => $data['rout_id'],
            'tltr_amnt' => $data['total'] ?? 0,
            'acmp_id' => $data['acmp_id'],
            'lfcl_id' => 1,
            'cont_id' => $data['country_id'],
            'aemp_iusr' => $employee_id,
            'aemp_eusr' => $employee_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

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

            $this->db = $country->cont_conn;

            try {
                $aemp_id = Employee::on($this->db)->where('aemp_usnm', auth()->user()->employee()->aemp_usnm)->first()->id;

                // permitted comapines only
                $companies = DB::connection($this->db)->select("SELECT acmp_id id,acmp_code,acmp_name
FROM user_group_permission WHERE aemp_id={$aemp_id} GROUP BY acmp_id,acmp_code,acmp_name");


                // all company name
//                 $companies = Company::on($this->db)->where('lfcl_id', 1)->get(['id', 'acmp_code', 'acmp_name']);
            }catch (\Exception $exception)
            {
                return response(['error' => 'No Access, Select Another Country', 'exist' => 0], 403);
            }

            return ['companies' => $companies, 'con' => $this->db];
        }else {
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
FROM user_group_permission WHERE aemp_id={$aemp_id} GROUP BY slgp_id,slgp_code,slgp_name");
            return ['groups' => $groups, 'con' => $this->db];
        }else {
            return response(['error' => 'Access Limited'], 403);
        }
    }

    public function getZones(Request $request)
    {
        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;
            $zones = Zone::on($this->db)->where(['cont_id' => $request->country_id, 'lfcl_id' => 1])->get(['id', 'zone_code', 'zone_name']);

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

        if ($this->userMenu->wsmu_read) {
            $this->db = $request->con ?? $this->db;
            $outlets = DB::connection($this->db)->select("SELECT t2.id, t2.site_code, t2.site_name FROM tl_rsmp t1
                                INNER JOIN tm_site t2 ON t2.id = t1.site_id
                                WHERE t1.rout_id = {$route_id} AND t2.lfcl_id = 1
                                AND t1.site_id NOT IN (SELECT site_id FROM tt_tltr t3
                                WHERE t3.tltr_date = CURDATE() AND t3.rout_id= {$route_id} AND aemp_id = {$employee_id})");

            $total_outlet = DB::connection($this->db)->select("SELECT count(*) FROM tl_rsmp t1
                                INNER JOIN tm_site t2 ON t2.id = t1.site_id
                                WHERE t1.rout_id = {$route_id} AND t2.lfcl_id = 1;");



            $visited_outlet_in_rout = TeleOrderTracking::on($this->db)->where([
                'tltr_date' => date('y-m-d'),
                'rout_id'   => $route_id,
                'aemp_iusr' => $employee_id,
            ])->count();

            if(count($outlets) > 0){
                return response([
                    'outlets' => $outlets,
                    'total_outlets' => $total_outlet,
                    'visited_outlet_in_rout' => $visited_outlet_in_rout + 1
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
            $digit=-10;

                if($country_id==14){
                    $digit=-9;
                    $code='60';
                }

            //return $digit;

            return DB::connection($this->db)->select("SELECT t1.id,t1.site_code, t1.site_name,CONCAT({$code},SUBSTRING(REPLACE(t1.site_mob1,'-',''),{$digit})) 'mobile'
                        , t4.than_name 'thana', t5.dsct_name 'district'
                        FROM tm_site t1
                        INNER JOIN tm_mktm t2 on t1.mktm_id = t2.id
                        INNER JOIN tm_ward t3 on t2.ward_id = t3.id
                        INNER JOIN tm_than t4 on t3.than_id = t4.id
                        INNER JOIN tm_dsct t5 on t4.dsct_id = t5.id
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

//        return ["SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt FROM tt_ordm t1
//                    WHERE t1.aemp_id = {$aemp_id} AND t1.site_id= {$site_id} and t1.lfcl_id = 1
//                    ORDER BY t1.ordm_date desc limit 5"];


        return DB::connection($this->db)->select("SELECT t1.id, t1.ordm_ornm, t1.ordm_date, t1.ordm_amnt, t2.lfcl_name 'order_status' FROM tt_ordm t1
                            inner join tm_lfcl t2 on t1.lfcl_id = t2.id
                            WHERE t1.aemp_id = {$aemp_id} AND t1.site_id= {$site_id} and t1.lfcl_id = 1
                            ORDER BY t1.ordm_date desc limit 5");


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

            $this->db = $country->cont_conn;

            if($country_id == 2 || $country_id == 5){
                $categories = DB::connection($this->db)->select("SELECT t5.id, t5.issc_name cat_name, t5.issc_code cat_code
                                    ,max(t3.slgp_id)slgp_id,max(t2.rout_id)rout_id,max(t2.aemp_id)aemp_id
                                    FROM tl_rsmp t1
                                    inner join tl_rpln t2 on t1.rout_id = t2.rout_id
                                    inner join tl_sgsm t3 ON t2.aemp_id=t3.aemp_id
                                    inner join tl_sgit t4 on t4.slgp_id = t3.slgp_id
                                    inner join tm_issc t5 on t4.issc_id = t5.id
                                    where t1.site_id={$site_id}
                                    GROUP BY t5.id,t5.issc_name,t5.issc_code");

                // category for only one route
//                $categories = DB::connection($this->db)->select("SELECT
//                    t4.id,t4.issc_code cat_code,t4.issc_name cat_name,t1.plmt_id
//                    FROM tl_sgsm t1
//                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
//                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
//                    INNER JOIN tm_issc t4 ON t3.issc_id=t4.id
//                    WHERE t1.aemp_id={$sr_id} AND t4.lfcl_id=1 AND t4.slgp_id = {$slgp_id}
//                    GROUP BY t4.id,t4.issc_code,t4.issc_name,t1.plmt_id");


                return ['categories' => $categories, 'country_id' => $country_id];
//                $categories = DB::connection($this->db)->select("SELECT t1.id, t1.issc_name cat_name, t1.issc_code cat_code, t3.amim_code
//                                            FROM tm_issc t1
//                                            INNER JOIN tl_sgit t2 ON t2.issc_id = t1.id
//                                            INNER JOIN tm_pldt t3 ON t3.amim_id = t2.amim_id
//                                            WHERE t1.slgp_id = {$slgp_id} AND t1.lfcl_id = 1
//                                            GROUP BY t1.id, t1.issc_code, t1.issc_name, t3.amim_code");
//                $categories = DB::connection($this->db)->select("select id, issc_name cat_name, issc_code cat_code from tm_issc
//                        where cont_id = {$country_id} and slgp_id = {$slgp_id} and lfcl_id = 1");
            }else{
//                $categories = DB::connection($this->db)->select("SELECT
//                        t4.id,t4.itsg_code cat_code,t4.itsg_name cat_name,t1.plmt_id
//                        FROM tl_stcm t1
//                        INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
//                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
//                        INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
//                        WHERE t4.lfcl_id=1 AND t1.slgp_id = {$slgp_id} AND t1.site_id = {$site_id}
//                        GROUP BY t4.id,t4.itsg_code,t4.itsg_name,t1.plmt_id");
//                $categories = DB::connection($this->db)->select("SELECT
//                        t4.id,t4.itsg_code cat_code,t4.itsg_name cat_name,t1.plmt_id
//                        FROM tl_stcm t1
//                        INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
//                        INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
//                        INNER JOIN tm_itsg t4 ON t3.itsg_id=t4.id
//                        WHERE t4.lfcl_id=1 AND t1.slgp_id = {$slgp_id} AND t1.site_id = {$site_id}
//                        GROUP BY t4.id,t4.itsg_code,t4.itsg_name,t1.plmt_id");
                $categories = DB::connection($this->db)->select("SELECT
                    t4.id ,t4.issc_code cat_code,t4.issc_name cat_name ,t1.plmt_id
                    FROM tl_stcm t1
                    INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                    INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                    INNER JOIN tm_issc t4 ON t3.issc_id=t4.id
                    WHERE t4.lfcl_id=1 AND t3.slgp_id = {$slgp_id} AND t1.site_id = {$site_id}
                    GROUP BY t4.id ,t4.issc_code,t4.issc_name ,t1.plmt_id");

                return ['categories' => $categories, 'country_id' => $country_id];
            }
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

            $this->db = $country->cont_conn;

            if($country_id == 2 || $country_id == 5){
                $items = DB::connection($this->db)->select("SELECT 
                            t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id
                            FROM tl_sgsm t1
                            INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                            INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                            INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                            WHERE t1.aemp_id={$sr_id} and t4.lfcl_id=1 AND t3.issc_id={$category_id}
                            GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft,t1.plmt_id");
            }else{
//                $items = DB::connection($this->db)->select("SELECT
//                            t3.id amim_id,t3.amim_code,t3.amim_name,t2.pldt_tppr,t2.amim_duft
//                            from tl_stcm t1
//                            INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
//                            INNER JOIN tm_amim t3 ON t2.amim_id=t3.id
//                            where t3.lfcl_id=1 and t3.itsg_id={$category_id} and t1.site_id={$site_id}
//                            GROUP BY t3.id,t3.amim_code,t3.amim_name,t2.pldt_tppr,t2.amim_duft");
                $items = DB::connection($this->db)->select("SELECT
                            t4.id amim_id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft
                            FROM tl_stcm t1
                            INNER JOIN tm_pldt t2 ON t1.plmt_id=t2.plmt_id
                            INNER JOIN tl_sgit t3 ON t2.amim_id=t3.amim_id
                            INNER JOIN tm_amim t4 ON t3.amim_id=t4.id
                            WHERE t4.lfcl_id=1 AND t3.issc_id= {$category_id} AND t1.site_id = {$site_id}
                            GROUP BY t4.id,t4.amim_code,t4.amim_name,t2.pldt_tppr,t4.amim_duft");
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
}
