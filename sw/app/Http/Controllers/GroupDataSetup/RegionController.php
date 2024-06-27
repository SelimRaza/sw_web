<?php

namespace App\Http\Controllers\GroupDataSetup;


use App\MasterData\Division;
use App\MasterData\Region;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;

class RegionController extends Controller
{
    private $access_key = 'tm_dirg';
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

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $Region = Region::on($this->db)->where('cont_id', '=', $this->currentUser->employee()->cont_id)->get();;
            return view('master_data.region.index')->with("Region", $Region)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.region.create');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $region = new Region();
        $region->setConnection($this->db);
        $region->dirg_name = $request->name;
        $region->dirg_code = $request->code;
        $region->cont_id = $this->currentUser->employee()->cont_id;
        $region->lfcl_id = 1;
        $region->aemp_iusr = $this->currentUser->employee()->id;
        $region->aemp_eusr = $this->currentUser->employee()->id;
        $region->var = 1;
        $region->attr1 = '';
        $region->attr2 = '';
        $region->attr3 = 0;
        $region->attr4 = 0;

        $region->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $regions = Region::on($this->db)->findorfail($id);
            return view('master_data.region.show')->with('region', $regions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $region = Region::on($this->db)->findorfail($id);

            return view('master_data.region.edit')->with('region', $region);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $region = Region::on($this->db)->findorfail($id);
        $region->dirg_name = $request->name;
        $region->dirg_code = $request->code;
        $region->aemp_eusr = $this->currentUser->employee()->id;
        $region->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $region = Region::on($this->db)->findorfail($id);
            $region->lfcl_id = $region->lfcl_id == 1 ? 2 : 1;
            $region->aemp_eusr = $this->currentUser->employee()->id;
            $region->save();
            return redirect('/region');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function filterRegion(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.name as name
FROM tbld_region AS t1 INNER JOIN tbld_sales_group_employee AS t2 ON t1.sales_group_id = t2.sales_group_id
WHERE t2.sales_group_id=$request->sales_group_id
GROUP BY t1.id,t1.name");
        return Response::json($divisions);
    }


    public function regionFormatGen(Request $request)
    {
        //echo "afdsaf";

        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Region(), 'region_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function regionMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Region(), $request->file('import_file'));
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


}
