<?php

namespace App\Http\Controllers\SpaceManagement;

use App\BusinessObject\SpaceMaintainFreeAmount;
use App\BusinessObject\SpaceMaintainFreeItem;
use App\BusinessObject\SpaceMaintainShowcase;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use App\BusinessObject\SpaceMaintain;
use App\BusinessObject\SpaceZone;
use App\BusinessObject\SpaceSite;
use App\DataExport\SiteMappingWithSpace;
use App\MasterData\Site;
use Maatwebsite\Excel\Facades\Excel;
use AWS;
use Image;


class MaintainSpaceController extends Controller
{
    private $access_key = 'maintain/space';
    private $currentUser;
    private $userMenu;
    private $db;

    public function __construct()
    {
        set_time_limit(80000000);
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

    public function index(Request $request){
        if ($this->userMenu->wsmu_vsbl) {
            $spaces = SpaceMaintain::on($this->db)->with('saleGroup:id,slgp_name,slgp_code')->paginate(50, ['id', 'spcm_name', 'spcm_slgp', 'spcm_code', 'spcm_sdat', 'spcm_exdt', 'spcm_qyfr']);

            return view('SpaceManagement.maintain_space.index', [
                'permission' => $this->userMenu,

                'spaces' => $spaces
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code, slgp_name, slgp_code, slgp_id
                    FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zones = DB::connection($this->db)->select("SELECT id zone_id, zone_name, zone_code
                    FROM `tm_zone`");
            return view('SpaceManagement.maintain_space.create_new', [
                'zones' => $zones,
                'acmp' => $acmp
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function program()
    {
        if ($this->userMenu->wsmu_crat) {
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code, slgp_name, slgp_code, slgp_id
                    FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zones = DB::connection($this->db)->select("SELECT DISTINCT zone_id, zone_name, zone_code
                    FROM `user_area_permission`
                    WHERE `aemp_id`='$empId'");
            return view('SpaceManagement.maintain_space.create', [
                'zones' => $zones,
                'acmp' => $acmp
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    protected function store(Request $request)
    {
        try {
            $data = $request->all();

            $spaceInfo = [];
            $spaceInfo['spcm_name'] = $request->spcm_name;
            $spaceInfo['spcm_code'] = 'SP'.str_pad(SpaceMaintain::on($this->db)->max('id')+1, 6, '0', STR_PAD_LEFT);
            $spaceInfo['spcm_slgp'] = $request->slgp_id;
            $spaceInfo['spcm_sdat'] = $request->spcm_sdat;
            $spaceInfo['spcm_edat'] = $request->spcm_edat;
            $spaceInfo['spcm_exdt'] = $request->spcm_edat;
            $spaceInfo['spcm_qyfr'] = $request->spcm_qyfr ?? 0;
            $spaceInfo['cont_id'] = Auth::user()->employee()->cont_id;
            $spaceInfo['lfcl_id'] = 1;
            $spaceInfo['spcm_type'] = 1;
            $spaceInfo['gift_is_national'] = $request->gift_is_national;
            $spaceInfo['amnt_is_national'] = $request->amnt_is_national;
            $spaceInfo['aemp_iusr'] = Auth::user()->employee()->id;
            $spaceInfo['aemp_eusr'] = Auth::user()->employee()->id;

            if (isset($request->spcm_imge)) {
                $regulation = $this->currentUser->country()->cont_imgf .'/master/space/'.uniqid() . '.' . $request->spcm_imge->getClientOriginalExtension();
                $file = $request->file('spcm_imge');
                list($width, $height) = getimagesize($file);

                if ($width == 512 && $height == 512) {
//                $resizedImage = Image::make($file->getRealPath())->fit(512, 512);
//                $tempFile = tempnam(sys_get_temp_dir(), 'img');
//                $resizedImage->save($tempFile);
                    $s3 = AWS::createClient('s3');
                    $s3->putObject(array(
                        'Bucket' => 'prgfms',
                        'Key' => $regulation,
                        'SourceFile' => $file,
                        'ACL' => 'public-read',
                        'ContentType' => $file->getMimeType(),
                    ));
                    $spaceInfo['spcm_imge'] = $regulation;
                }
//                unlink($tempFile); // remove the temporary file
            }

            DB::connection($this->db)->beginTransaction();

            $space_id = SpaceMaintain::on($this->db)->insertGetId($spaceInfo);

//            return $request->all();

            if (isset($data['national_zone_ids']) || isset($data['area_list'])) {
                $zone_info = [];

                if ($data['is_national'] == 1) {

                    foreach ($data['national_zone_ids'] as $key => $zone_id) {
                        $zone_info[] = [
                            'spcm_id' => $space_id,
                            'is_national' => $data['is_national'],
                            'zone_id' => $zone_id,
                            'max_approve' => $data['national_zone_qtys'][$key] ?? 0,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id,
                            'var' => 1,
                            'attr1' => 1,
                            'attr2' => 1,
                        ];
                    }
                }
                elseif ($data['is_national'] == 0) {
                    foreach ($data['area_list'] as $key => $zone_id) {
                        $zone_info[] = [
                            'spcm_id' => $space_id,
                            'is_national' => $data['is_national'],
                            'zone_id' => $zone_id,
                            'max_approve' => $data['individual_zone_qtys'][$key] ?? 0,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id,
                            'var' => 1,
                            'attr1' => 1,
                            'attr2' => 1,
                        ];
                    }
                }

                SpaceZone::on($this->db)->insert($zone_info);
            }

            // store showcase items
            if (isset($data['spsb_ids'])) {
                foreach ($data['spsb_ids'] as $key => $spsb_id) {
                    $exist = SpaceMaintainShowcase::on($this->db)
                        ->where(['amim_id' => $spsb_id,
                            'spcm_id' => $space_id])->first();

                    if (!$exist) {
                        $info[] = [
                            'amim_id' => $spsb_id,
                            'min_qty' => $data['spsb_min_qtys'][$key],
                            'spcm_id' => $space_id,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id
                        ];

                    }
                }

                SpaceMaintainShowcase::insert($info);
            }

            // store free items
            if (isset($data['spft_ids'])) {
                $spft_info = [];
                foreach ($data['spft_ids'] as $key => $spft_id) {

                    if ($data['is_national'] == 1) {
                        $spft_exist = SpaceMaintainFreeItem::on($this->db)
                            ->where([
                                'spcm_id' => $space_id, 'zone_id' => 0
                            ])->first();
                        if (!$spft_exist) {
                            $spft_info[] = [
                                'amim_id' => $spft_id,
                                'min_qty' => $data['spft_min_qtys'][$key],
                                'spcm_id' => $space_id,
                                'zone_id' => 0,
                                'cont_id' => Auth::user()->employee()->cont_id,
                                'lfcl_id' => 1,
                                'aemp_iusr' => Auth::user()->employee()->id,
                                'aemp_eusr' => Auth::user()->employee()->id
                            ];
                        }

                    } else {
                        foreach ($data['area_list'] ?? [] as $k => $spft_zone_id) {

                            $spft_zone_exist = SpaceMaintainFreeItem::on($this->db)
                                ->where([
                                    'spcm_id' => $space_id, 'zone_id' => $spft_zone_id
                                ])->first();
                            if (!$spft_zone_exist) {
                                $spft_info[] = [
                                    'amim_id'   => $spft_id,
                                    'min_qty'   => $data['spft_min_qtys'][$key] ?? 0,
                                    'spcm_id'   => $space_id,
                                    'zone_id'   => $spft_zone_id,
                                    'cont_id'   => Auth::user()->employee()->cont_id,
                                    'lfcl_id'   => 1,
                                    'aemp_iusr' => Auth::user()->employee()->id,
                                    'aemp_eusr' => Auth::user()->employee()->id
                                ];
                            }
                        }
                    }
                }

                SpaceMaintainFreeItem::on($this->db)->insert($spft_info);
            }


            // store offer amount
            if (isset($data['spft_amnt']) && !is_null($data['spft_amnt'])) {
                $spam_info = [];
                if ($data['is_national'] == 1) {
                    $spam_exist = SpaceMaintainFreeAmount::on($this->db)
                        ->where([
                            'spcm_id' => $space_id, 'zone_id' => 0
                        ])->first();
                    if (!$spam_exist) {
                        $spam_info[] = [
                            'max_amnt' => $data['spft_amnt'],
                            'spcm_id' => $space_id,
                            'zone_id' => 0,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id
                        ];
                    }

                } else {
                    foreach ($data['area_list'] ?? [] as $key => $spam_zone_id) {

                        $spam_zone_exist = SpaceMaintainFreeAmount::on($this->db)
                            ->where([
                                'spcm_id' => $space_id, 'zone_id' => $spam_zone_id
                            ])->first();
                        if (!$spam_zone_exist) {
                            $spam_info[] = [
                                'max_amnt' => $data['spft_amnt'],
                                'spcm_id' => $space_id,
                                'zone_id' => $spam_zone_id,
                                'cont_id' => Auth::user()->employee()->cont_id,
                                'lfcl_id' => 1,
                                'aemp_iusr' => Auth::user()->employee()->id,
                                'aemp_eusr' => Auth::user()->employee()->id
                            ];
                        }
                    }
                }

                SpaceMaintainFreeAmount::on($this->db)->insert($spam_info);

            }


//            SpaceMaintain::where('id', $space_id)->update(['spcm_imge' => $regulation]);
            DB::connection($this->db)->commit();

            return redirect()->back()->with('success', 'Space Program Added Successfully');

        }
        catch(\Exception $exception){
            DB::connection($this->db)->rollBack();
//            dd($exception);
            return redirect()->back()->with('danger', 'Invalid Information');
        }
    }

    protected function store_old(Request $request)
    {
        $db=$this->db.'.'.'tm_spcm';
        $validatedData = $request->validate([
            'spcm_name' => 'required',
            'spcm_sdat' => 'required',
            'spcm_edat' => 'required',
        ]);
        $space=new SpaceMaintain();
        $space->setConnection($this->db);
        $space->spcm_name=$request->spcm_name;
        $space->spcm_code = 'SP'.str_pad(SpaceMaintain::on($this->db)->max('id')+1, 6, '0', STR_PAD_LEFT);
        $space->spcm_slgp=$request->slgp_id;
        $space->spcm_sdat=$request->spcm_sdat;
        $space->spcm_edat=$request->spcm_edat;
        $space->spcm_exdt=$request->spcm_edat;
        $space->spcm_qyfr=$request->spcm_qyfr ?? 0;
        $space->cont_id=Auth::user()->employee()->cont_id;
        $space->lfcl_id=1;
        $space->spcm_type=1;
        $space->aemp_iusr=Auth::user()->employee()->id;
        $space->aemp_eusr=Auth::user()->employee()->id;
        $space->save();

        return redirect()->back()->with('success', 'Space Maintain Added Successfully');
    }

    protected function show(Request $request, $id){
        if ($this->userMenu->wsmu_vsbl) {
            $space = SpaceMaintain::on($this->db)->with('saleGroup')->findOrFail($id, ['id', 'spft_id',
                'spcm_name', 'spft_amnt', 'spcm_code', 'spcm_sdat', 'spcm_exdt', 'spcm_qyfr', 'spcm_imge']);

            $showcases = [];
            if(count($space->showcases)>0) {
                $showcases = DB::connection($this->db)->select("select amim_name, amim_code, min_qty
                            from tl_spsb t1 
                            inner join tm_amim t2 on t2.id = t1.amim_id
                            where t1.spcm_id={$space->id}");
            }

            $zones = [];
            if(count($space->zones)>0) {
                $zones = DB::connection($this->db)->select("select t1.id, t1.lfcl_id, zone_name, zone_code, is_national, max_approve
                            from tl_spaz t1 
                            inner join tm_zone t2 on t2.id = t1.zone_id
                            where t1.spcm_id={$space->id}");
            }

            $free_items = [];
            if(count($space->freeItems)>0) {
                $free_items = DB::connection($this->db)->select("select distinct amim_name, amim_code, min_qty
                            from tl_spft t1 
                            inner join tm_amim t2 on t2.id = t1.amim_id
                            where t1.spcm_id={$space->id}");
            }

            $free_amounts = [];
            if(count($space->freeAmounts)>0) {
                $free_amounts = DB::connection($this->db)->select("select zone_name, zone_code, max_amnt, zone_id
                            from tl_spam t1 
                            left join tm_zone t2 on t2.id = t1.zone_id
                            where t1.spcm_id={$space->id}");
            }

            return view('SpaceManagement.maintain_space.view', [
                'permission'    => $this->userMenu,
                'space'         => $space,
                'showcases'     => count($showcases) > 0 ? collect($showcases) : [],
                'free_items'    => count($free_items) > 0 ? collect($free_items) : [],
                'free_amounts'  => count($free_amounts) > 0 ? collect($free_amounts) : [],
                'zones'         => count($zones) > 0 ? collect($zones) : []
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    protected function edit($id){
        if ($this->userMenu->wsmu_updt) {
            $space = SpaceMaintain::on($this->db)->findOrFail($id);
            return view('SpaceManagement.maintain_space.edit', ['space' => $space]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    protected function update(Request $request, $id){
        if ($this->userMenu->wsmu_updt) {
            $space = SpaceMaintain::on($this->db)->findOrFail($id);

            $data = $request->validate([
                'spcm_name' => 'nullable|string',
                'spcm_edat' => 'nullable|date_format:Y-m-d',
            ]);

            try {
                $space->update([
                    'spcm_name' => $data['spcm_name'],
                    'spcm_exdt' => $data['spcm_edat'],
                ]);

                return redirect()->back()->with('success', 'Updated Successfully');
            }catch(\Exception $exception){
                return $exception->getMessage();
                return redirect()->back()->with('danger', 'Invalid Information');
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function itemBySlgpId(Request $request)
    {
        return DB::connection($this->db)->select("select t1.amim_id, t2.amim_code, t2.amim_name from tl_sgit t1
                inner join tm_amim t2 on t1.amim_id = t2.id 
                where slgp_id = {$request->slgp_id}");
    }

    public function spaceMaintainBySlgpId(Request $request)
    {
        return DB::connection($this->db)->select("select id, spcm_code, spcm_name from tm_spcm
	                where spcm_slgp = {$request->slgp_id}");
    }

    public function getSpaceMaintainInfo(Request $request){
        return DB::connection($this->db)->select("select id, spcm_code, spcm_name, spcm_qyfr from tm_spcm
	                where id = {$request->spcm_id}");
    }

    public function updateShowcaseItems(Request $request){

        $data = $request->all();
        $space = SpaceMaintain::on($this->db)->findOrFail($data['spcm_id']);

        if(count($data)>0) {
            DB::connection($this->db)->select("Update tm_spcm SET gift_is_national={$data['spft_is_national']},
               amnt_is_national={$data['spam_is_national']}  WHERE id={$data['spcm_id']}");
        }

        $info = [];

        if (isset($data['spsb_ids'])) {
            foreach ($data['spsb_ids'] as $key => $spsb_id) {
                $exist = SpaceMaintainShowcase::on($this->db)->where(['amim_id' => $spsb_id, 'spcm_id' => $space->id])->first();

                if (!$exist) {
                    $info[] = [
                        'amim_id' => $spsb_id,
                        'spcm_id' => $space->id,
                        'cont_id' => Auth::user()->employee()->cont_id,
                        'lfcl_id' => 1,
                        'aemp_iusr' => Auth::user()->employee()->id,
                        'aemp_eusr' => Auth::user()->employee()->id
                    ];
                }
            }

            try {
                $space->showcases()->insert($info);

                return response(['success' => 'Showcase Item Stored Successfully'], 200);
            }catch (\Exception $exception)
            {
                return response(['error' => 'Showcase Item Already Exist'], 400);
            }
        }

    }

    public function updateFreeItems(Request $request){

        $data = $request->all();


            $space = SpaceMaintain::on($this->db)->findOrFail($data['spcm_id']);

            if(count($data)>0) {
                DB::connection($this->db)->select("Update tm_spcm SET gift_is_national={$data['spft_is_national']}  WHERE id={$data['spcm_id']}");
            }


            $spft_info = [];

            if (isset($data['spft_ids'])) {
                foreach ($data['spft_ids'] as $key => $spft_id) {

                    if ($data['spft_is_national'] == 1) {
                        $spft_exist = SpaceMaintainFreeItem::on($this->db)
                            ->where([
                                'spcm_id' => $space->id, 'zone_id' => 0
                            ])->first();
                        if (!$spft_exist) {
                            $spft_info[] = [
                                'amim_id' => $spft_id,
                                'spcm_id' => $space->id,
                                'zone_id' => 0,
                                'cont_id' => Auth::user()->employee()->cont_id,
                                'lfcl_id' => 1,
                                'aemp_iusr' => Auth::user()->employee()->id,
                                'aemp_eusr' => Auth::user()->employee()->id
                            ];
                        } else {
//                            $item_name = ItemMaster::on($this->db)->findOrFail($spft_id)->amim_name;

                            return response(['error' => "free item for all zones already exist"], 400);
                        }

                    } else {
                        foreach ($data['spft_zone_ids'] ?? [] as $key => $spft_zone_id) {

                            $spft_zone_exist = SpaceMaintainFreeItem::on($this->db)
                                ->where([
                                    'spcm_id' => $space->id, 'zone_id' => $spft_zone_id
                                ])->first();
                            if (!$spft_zone_exist) {
                                $spft_info[] = [
                                    'amim_id' => $spft_id,
                                    'spcm_id' => $space->id,
                                    'zone_id' => $spft_zone_id,
                                    'cont_id' => Auth::user()->employee()->cont_id,
                                    'lfcl_id' => 1,
                                    'aemp_iusr' => Auth::user()->employee()->id,
                                    'aemp_eusr' => Auth::user()->employee()->id
                                ];
                            } else {
                                $zone_name = Zone::on($this->db)->findOrFail($spft_zone_id)->zone_name;

                                return response(['error' => "free item code for {$zone_name} zone already exist"], 400);
                            }
                        }
                        $space->update(['gift_is_national' => 0]);
                    }
                }



                try {
                    SpaceMaintainFreeItem::on($this->db)->insert($spft_info);

                    return response(['success' => 'Free Item Stored Successfully'], 200);
                }catch (\Exception $exception)
                {
                    return response(['error' => 'Free Item Already Exist'], 400);
                }


            }else{
                return response(['error' => 'Free Item is Empty'], 400);
            }


    }

    public function updateFreeAmount(Request $request){

        $data = $request->all();


            $space = SpaceMaintain::on($this->db)->findOrFail($data['spcm_id']);

            if(count($data)>0) {
                DB::connection($this->db)->select("Update tm_spcm SET amnt_is_national={$data['spam_is_national']}  WHERE id={$data['spcm_id']}");
            }

            $spam_info = [];

            if (isset($data['spft_amnt']) && !is_null($data['spft_amnt'])) {
                if ($data['spam_is_national'] == 1) {
                    $spam_exist = SpaceMaintainFreeAmount::on($this->db)
                        ->where([
                            'spcm_id' => $space->id, 'zone_id' => 0
                        ])->first();
                    if (!$spam_exist) {
                        $spam_info[] = [
                            'max_amnt' => $data['spft_amnt'],
                            'spcm_id' => $space->id,
                            'zone_id' => 0,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id
                        ];
                    } else {
//                        $spam_exist->update([
//                            'max_amnt' => $data['spft_amnt'],
//                            'aemp_eusr' => Auth::user()->employee()->id
//                        ]);
//
//                        return response(['success' => "Amount for all zones updated"], 200);
                        return response(['error' => "Amount for all zones already exist"], 400);
                    }

                    $space->update(['amnt_is_national' => 1]);
                } else {
                    foreach ($data['spam_zone_ids'] ?? [] as $key => $spam_zone_id) {

                        $spam_zone_exist = SpaceMaintainFreeAmount::on($this->db)
                            ->where([
                                'spcm_id' => $space->id, 'zone_id' => $spam_zone_id
                            ])->first();
                        if (!$spam_zone_exist) {
                            $spam_info[] = [
                                'max_amnt' => $data['spft_amnt'],
                                'spcm_id' => $space->id,
                                'zone_id' => $spam_zone_id,
                                'cont_id' => Auth::user()->employee()->cont_id,
                                'lfcl_id' => 1,
                                'aemp_iusr' => Auth::user()->employee()->id,
                                'aemp_eusr' => Auth::user()->employee()->id
                            ];
                        } else {
                            $zone_name = Zone::on($this->db)->findOrFail($spam_zone_id)->zone_name;

//                            return response(['success' => "Amount updated for {$zone_name} zone"], 200);
                            return response(['error' => "Amount already exist for {$zone_name} zone"], 400);
                        }
                    }
                    $space->update(['amnt_is_national' => 0]);
                }

                try {
                    SpaceMaintainFreeAmount::on($this->db)->insert($spam_info);

                    return response(['success' => 'Free Amount Stored Successfully'], 200);
                }catch (\Exception $exception)
                {
                    return response(['error' => 'Free Amount Already Exist'], 400);
                }
            }else{
                return response(['error' => 'Free Amount is Empty'], 400);
            }


    }

    public function spaceZoneMapping(Request $request){
        $db=$this->db.'.'.'tm_spcm';
        $data = $request->validate([
            'spcm_id' => 'required|exists:'.$db.',id',
            'is_national' => 'required|in:0,1',
            'zone_ids' => 'nullable|array',
        ]);


        try {
            $space =  SpaceMaintain::on($this->db)->findOrFail($data['spcm_id']);

            if(isset($data['zone_ids']))
            {
                $info = [];

                foreach($data['zone_ids'] as $key => $zone_id){
                    $exist = SpaceZone::on($this->db)->where(['zone_id'=> $zone_id,'spcm_id'=>$space->id])->first();

                    if(!$exist) {
                        $info[] = [
                            'spcm_id' => $data['spcm_id'],
                            'is_national' => $data['is_national'],
                            'zone_id' => $zone_id,
                            'cont_id' => Auth::user()->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => Auth::user()->employee()->id,
                            'aemp_eusr' => Auth::user()->employee()->id,
                            'var' => 1,
                            'attr1' => 1,
                            'attr2' => 1,
                            'attr3' => 1,
                            'attr4' => 1,
                        ];
                    }
                }

                $space->zones()->insert($info);
                $spcm_id=$data['spcm_id'];
                DB::connection($this->db)->select("Update tm_spcm SET attr4=0 WHERE id={$spcm_id}");

            }else{
                SpaceZone::on($this->db)->insert([
                    'spcm_id' => $data['spcm_id'],
                    'is_national' => $data['is_national'],
                    'cont_id' => Auth::user()->employee()->cont_id,
                    'lfcl_id' => 1,
                    'aemp_iusr' => Auth::user()->employee()->id,
                    'aemp_eusr' => Auth::user()->employee()->id,
                    'var' => 1,
                    'attr1' => 1,
                    'attr2' => 1,
                    'attr3' => 1,
                    'attr4' => 1,
                ]);
                $spcm_id=$data['spcm_id'];
                DB::connection($this->db)->select("Update tm_spcm SET attr4=1 WHERE id={$spcm_id}");

            }

            return redirect()->back()->with('success', 'Space Zone Mapping Added Successfully');

        }catch(\Exception $exception){
            return redirect()->back()->with('danger', 'Invalid Information');
        }

    }

    public function spaceSiteMapping(Request $request){
        $spcm_table = $this->db.'.'.'tm_spcm';
        $site_table = $this->db.'.'.'tm_site';
        $data = $request->validate([
            'spcm_id' => 'required|exists:'.$spcm_table.',id',
            'site_code' => 'required|exists:'.$site_table.',site_code'
        ]);

        try {
            $space =  SpaceMaintain::on($this->db)->findOrFail($data['spcm_id']);

            $site =  Site::on($this->db)->where('site_code', $data['site_code'])->first();

            if(!$site){
                return redirect()->back()->with('danger', 'Invalid Site Code');
            }

            $exist = SpaceSite::on($this->db)->where(['site_id'=> $site->id,'spcm_id'=>$space->id])->first();


            if(!$exist) {

                $siteMapping = new SpaceSite();
                $siteMapping->setConnection($this->db);
                $siteMapping->spcm_id = $space->id ?? data['spcm_id'];
                $siteMapping->site_id = $site->id ?? data['site_id'];
                $siteMapping->cont_id = Auth::user()->employee()->cont_id;
                $siteMapping->lfcl_id = 12;
                $siteMapping->attr1 = 1;
                $siteMapping->attr2 = 1;
                $siteMapping->attr3 = 1;
                $siteMapping->attr4 = 1;
                $siteMapping->aemp_iusr = Auth::user()->employee()->id;
                $siteMapping->aemp_eusr = Auth::user()->employee()->id;
                $siteMapping->save();
            }else{
                return redirect()->back()->with('danger', 'Site Mapping with this Space already exist');
            }

            return redirect()->back()->with('success', 'Space Site Mapping Added Successfully');
        }
        catch(\Exception $exception){
            return redirect()->back()->with('danger', 'Invalid Information');
        }

    }

    public function updateSpaceZoneMapping(Request $request, $id){
        $spaceZone = SpaceZone::on($this->db)->findOrFail($id);

        try{
            if (isset($spaceZone->lfcl_id) && $spaceZone->lfcl_id == 1) {
                $spaceZone->update([
                    'lfcl_id' => 2
                ]);
            } else if (isset($spaceZone) && $spaceZone->lfcl_id == 2) {
                $spaceZone->update([
                    'lfcl_id' => 1
                ]);
            }

            return $spaceZone->lfcl_id;
        }catch(\Exception $exception){
            return 'Invalid Information';
        }
    }

    public function editSpaceSiteMapping($id){
        if ($this->userMenu->wsmu_updt) {
            $space = SpaceMaintain::on($this->db)->findOrFail($id);

            $spaceSites = $space->sitesWithStatus()->with('site:id,site_name,site_code')->paginate(30);

            return view('SpaceManagement.maintain_space.editSpace', [
                'sites' => $spaceSites,
                'space' => $space
            ]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function updateSpaceSiteMapping($id){
        $spaceSite = SpaceSite::on($this->db)->findOrFail($id);

        try{
            if (isset($spaceSite->lfcl_id) && $spaceSite->lfcl_id == 1) {
                $spaceSite->update([
                    'lfcl_id' => 2
                ]);
            } else if (isset($spaceSite) && $spaceSite->lfcl_id == 2) {
                $spaceSite->update([
                    'lfcl_id' => 1
                ]);
            }

            return $spaceSite->lfcl_id;
        }catch(\Exception $exception){
            return 'Invalid Information';
        }
    }

    public function spaceSiteMappingFormat(){
        return Excel::download(new SiteMappingWithSpace(),'site_mapping_with_space' . date("Y-m-d H:i:s") . '.xlsx' );
    }

    public function spaceSiteMappingUpdate(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('space_site_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new SiteMappingWithSpace(), $request->file('space_site_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e->getMessage());
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function excelExport()
    {
        return Excel::download(new SpaceMaintain(), "space_program_zones_".date('Y_m_d').".xlsx");
    }

    public function report()
    {
        dd('call');
    }
}
