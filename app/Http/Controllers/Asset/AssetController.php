<?php

namespace App\Http\Controllers\Asset;

use App\DataExport\AssetMappingData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\MasterData\Asset;
use App\BusinessObject\AssetMapping;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;


class AssetController extends Controller
{
    private $access_key = 'asset-mapping';
    private $currentUser;
    private $userMenu;

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

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $assets = Asset::get();

            return view('assets.items.index', ['assets' => $assets,'permission' => $this->userMenu]);
        } else {
            return view('theme.access_limit');
        }
    }

    public function show($id, Request $request)
    {
        if ($this->userMenu->wsmu_read) {

            $mappings = $this->paginateArray(
                DB::connection($this->db)->select("select t1.id, t2.astm_name, t3.slgp_name, t3.slgp_code, t4.amim_name, t4.amim_code
                        from tl_astd t1
                        inner join tm_astm t2 on t2.id = t1.astm_id
                        inner join tm_slgp t3 on t3.id = t1.slgp_id
                        inner join tm_amim t4 on t4.id = t1.amim_id
                        where astm_id={$id}")
            );

            return view('assets.items.show', ['mappings' => $mappings,'permission' => $this->userMenu, 'id' => $id]);
        } else {
            return view('theme.access_limit');
        }
    }

    public function paginateArray($data, $perPage = 15)
    {
        $page = Paginator::resolveCurrentPage();
        $total = count($data);
        $results = array_slice($data, ($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {

            $empId = $this->currentUser->employee()->id;

            $groups = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId'");

            $assets = Asset::get();

            return view('assets.items.mapping', ['assets' => $assets,'groups' => $groups]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        $items = [];

        foreach($request->asset_ids as $key => $asset_id){
            $items[] =  [
                'astm_id' => $request->asset_ids[$key],
                'slgp_id' => $request->group_ids[$key],
                'amim_id' => $request->item_ids[$key],
                'aemp_iusr' => $this->currentUser->employee()->id,
                'aemp_eusr' => $this->currentUser->employee()->id
            ];
        }

        AssetMapping::insert($items);

        return redirect()->back()->with('success', 'Successfully Added');
    }

    public function searchProduct($like)
    {
        $items = DB::connection($this->db)->select("select amim_name, id, amim_code from tm_amim where amim_code = '{$like}'");

        if($items) {
            return $items;
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $asset = Asset::on($this->db)->findorfail($id);
            return view('assets.items.edit', ['asset' => $asset]);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::on($this->db)->findorfail($id);

        $asset->astm_name = $request->name;
        $asset->astm_type = $request->type;
        $asset->save();

        return redirect()->back()->with('success', 'successfully Updated');
    }



    public function assetMappingMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new AssetMapping(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                    //throw $e;
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function assetMappingFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new AssetMapping(), 'asset_mapping_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function delete(AssetMapping $mapping)
    {
        if($mapping)
        {
            return redirect()->back()->with('success', 'Deleted Successfully');
        }else {
            return redirect()->back()->with('danger', 'Cannot Delete');
        }

    }

    public function assetMappingExport($id)
    {
        return Excel::download(new AssetMappingData($id), "asset_mapping_".date('Y_m_d').".xlsx");
    }
}
