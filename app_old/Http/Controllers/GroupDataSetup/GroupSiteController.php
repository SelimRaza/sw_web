<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\BusinessObject\BaseSiteMapping;
use App\BusinessObject\SalesGroup;
use App\MasterData\Base;
use App\MasterData\Division;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Site;
use App\MasterData\SKU;
use App\MasterData\SubCategory;
use App\MasterData\SubChannel;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Support\Facades\Input;

class GroupSiteController extends Controller
{
    private $access_key = 'tbld_group_site';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            if ($request->has('search_text')) {
                $q = request('search_text');
                $sites = DB::table('tbld_site')
                    ->join('tbld_base_site_mapping', 'tbld_site.id', '=', 'tbld_base_site_mapping.site_id')
                    ->join('tbld_base', 'tbld_base_site_mapping.base_id', '=', 'tbld_base.id')
                    ->join('tbld_sales_group_employee', 'tbld_base.sales_group_id', '=', 'tbld_sales_group_employee.sales_group_id')
                    ->join('tbld_sales_group', 'tbld_sales_group_employee.sales_group_id', '=', 'tbld_sales_group.id')
                    ->join('tbld_sub_channel', 'tbld_site.sub_channel_id', '=', 'tbld_sub_channel.id')
                    ->join('tbld_outlet_grade', 'tbld_site.grade_id', '=', 'tbld_outlet_grade.id')
                    ->select('tbld_site.id', 'tbld_site.name', 'tbld_site.code', 'tbld_sub_channel.name as sub_channel', 'tbld_outlet_grade.name as grade_name', 'tbld_site.site_image', 'tbld_base.name as base', 'tbld_site.status_id', 'tbld_sales_group.name as sales_group', 'tbld_base_site_mapping.sales_group_id')
                    ->where(function ($query) use($q) {
                        $query->where('tbld_site.name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.code', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.mobile_1', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.owner_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.zip_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.address', 'LIKE', '%' . $q . '%')
                            ->orWhere('tbld_site.updated_at', 'LIKE', '%' . $q . '%');
                    })->where(['tbld_sales_group_employee.emp_id' => $emp_id, 'tbld_site.country_id' => $country_id]) ->paginate(500);
            } else {
                $sites = DB::table('tbld_site')
                    ->join('tbld_base_site_mapping', 'tbld_site.id', '=', 'tbld_base_site_mapping.site_id')
                    ->join('tbld_base', 'tbld_base_site_mapping.base_id', '=', 'tbld_base.id')
                    ->join('tbld_sales_group_employee', 'tbld_base.sales_group_id', '=', 'tbld_sales_group_employee.sales_group_id')
                    ->join('tbld_sales_group', 'tbld_sales_group_employee.sales_group_id', '=', 'tbld_sales_group.id')
                    ->join('tbld_sub_channel', 'tbld_site.sub_channel_id', '=', 'tbld_sub_channel.id')
                    ->join('tbld_outlet_grade', 'tbld_site.grade_id', '=', 'tbld_outlet_grade.id')
                    ->select('tbld_site.id', 'tbld_site.name', 'tbld_site.code', 'tbld_sub_channel.name as sub_channel', 'tbld_outlet_grade.name as grade_name', 'tbld_site.site_image', 'tbld_base.name as base', 'tbld_site.status_id', 'tbld_sales_group.name as sales_group', 'tbld_base_site_mapping.sales_group_id')
                    ->where(['tbld_sales_group_employee.emp_id' => $emp_id, 'tbld_site.country_id' => $country_id]) ->paginate(500);
            }

            return view('master_data.group_site.index')->with('sites', $sites)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $divisions = Division::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $subChannels = SubChannel::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $outletGrades = OutletCategory::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_site.create')->with('subChannels', $subChannels)->with('outletGrades', $outletGrades)->with("divisions", $divisions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $outlet = new Outlet();
            $outlet->name = $request->name;
            $outlet->code = $request->code;
            $outlet->country_id = $this->currentUser->employee()->cont_id;
            $outlet->created_by = $this->currentUser->employee()->id;
            $outlet->updated_by = $this->currentUser->employee()->id;
            $outlet->save();
            $site = new Site();
            $site->name = $request->name;
            $site->code = $request->code;
            $site->ln_name = $request->ln_name;
            $site->address = $request->address;
            $site->ln_address = $request->ln_address;
            $site->owner_name = $request->owner_name;
            $site->ln_owner_name = $request->ln_owner_name;
            $site->mobile_1 = $request->mobile_1;
            $site->mobile_2 = $request->mobile_2 == null ? "" : $request->mobile_2;
            $site->email = $request->email == null ? "" : $request->email;
            $site->house_no = $request->house_no == null ? "" : $request->house_no;
            $site->vat_trn = $request->vat_trn == null ? "" : $request->vat_trn;
            $site->sub_channel_id = $request->sub_channel_id;
            $site->grade_id = $request->outlet_grade;
            $site->base_id = $request->base_id;
            $site->outlet_id = $outlet->id;
            $site->site_image = '';
            $site->owner_image = '';
            $site->lat = 0;
            $site->lon = 0;
            $site->is_verified = 0;
            $site->status_id = 1;
            $site->country_id = $this->currentUser->employee()->cont_id;
            $site->created_by = $this->currentUser->employee()->id;
            $site->updated_by = $this->currentUser->employee()->id;
            $site->save();
            DB::commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $site = Site::findorfail($id);
            return view('master_data.group_site.show')->with('site', $site);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id,$group_id)
    {
        if ($this->userMenu->wsmu_updt) {
            $site = Site::findorfail($id);
            $salesGroup = SalesGroup::findorfail($group_id);
            $baseSiteMapping = BaseSiteMapping::where(['sales_group_id' => $group_id, 'site_id' => $id])->first();
            //$base = Base::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $bases = Base::where(['cont_id' => $this->currentUser->employee()->cont_id, 'sales_group_id' => $group_id])->get();
            $subChannels = SubChannel::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $outletGrades = OutletCategory::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_site.edit')->with('subChannels', $subChannels)->with('site', $site)->with('outletGrades', $outletGrades)->with('bases', $bases)->with('salesGroup', $salesGroup)->with('baseSiteMapping', $baseSiteMapping);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function update(Request $request, $id)
    {
      //  dd($request);
        $site = Site::findorfail($id);
        $site->name = $request->name;
        $site->code = $request->code;
        $site->ln_name = $request->ln_name;
        $site->address = $request->address;
        $site->ln_address = $request->ln_address;
        $site->owner_name = $request->owner_name;
        $site->ln_owner_name = $request->ln_owner_name;
        $site->mobile_1 = $request->mobile_1;
        $site->mobile_2 = $request->mobile_2 == null ? "" : $request->mobile_2;
        $site->email = $request->email == null ? "" : $request->email;
        $site->house_no = $request->house_no == null ? "" : $request->house_no;
        $site->vat_trn = $request->vat_trn == null ? "" : $request->vat_trn;
        $site->sub_channel_id = $request->sub_channel_id;
        $site->grade_id = $request->outlet_grade;
        $site->updated_by = $this->currentUser->employee()->id;
        $site->save();
        $outlet = Outlet::findorfail($site->outlet()->id);
        $outlet->name = $request->name;
        $outlet->code = $request->code;
        $outlet->updated_by = $this->currentUser->employee()->id;
        $outlet->save();
        $baseSiteMapping = BaseSiteMapping::where(['sales_group_id' => $request->sales_group_id, 'site_id' => $id])->first();
        //if ($baseSiteMapping != null) {
            $baseSiteMapping->base_id = $request->base_id ;
            $baseSiteMapping->updated_by = $this->currentUser->employee()->id;
            $baseSiteMapping->save();
       // }
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $site = Site::findorfail($id);
            $site->status_id = $site->status_id == 1 ? 2 : 1;
            $site->updated_by = $this->currentUser->employee()->id;
            $site->save();
            return redirect('/group_site');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function filterRegion(Request $request)
    {
        $regions = Region::where('division_id', '=', $request->division_id)->get();
        return Response::json($regions);

    }

    public function filterZone(Request $request)
    {
        $zones = Zone::where('region_id', '=', $request->region_id)->get();
        return Response::json($zones);

    }

    public function filterBase(Request $request)
    {
        $zones = Base::where('zone_id', '=', $request->zone_id)->get();
        return Response::json($zones);

    }

    public function filterSite(Request $request)
    {
        $sites = DB::select("SELECT
  t1.id,
  t1.name,
  t2.route_id
FROM tbld_site AS t1 LEFT JOIN tbld_route_site_mapping AS t2 ON t1.id = t2.site_id 
WHERE t1.base_id = $request->base_id  ");
        //$sites = Site::where('base_id', '=', $request->base_id)->get();
        return Response::json($sites);

    }

    public function filterRoute(Request $request)
    {
        $zones = Route::where('base_id', '=', $request->base_id)->get();
        return Response::json($zones);

    }
}
