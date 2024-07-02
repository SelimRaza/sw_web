<?php

namespace App\Http\Controllers\GovtHeirarchy;

use App\MasterData\Base;
use App\MasterData\Channel;
use App\MasterData\District;
use App\MasterData\Division;
use App\MasterData\GovtDivision;
use App\MasterData\Market;
use App\MasterData\Outlet;
use App\MasterData\OutletCategory;
use App\MasterData\Region;
use App\MasterData\Route;
use App\MasterData\Site;
use App\MasterData\SKU;
use App\MasterData\SubCategory;
use App\MasterData\SubChannel;
use App\MasterData\Thana;
use App\MasterData\Ward;
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
use Excel;

class MarketController extends Controller
{
    private $access_key = 'tm_mktm';
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

    /*SELECT
      t1.id                                                                         AS site_id,
      t1.mktm_code,
      t1.mktm_name,
      concat(t2.ward_name, '<', t3.than_name, '<', t4.dsct_name, '<', t5.disn_name) AS mktm_name
    FROM tm_mktm AS t1
      INNER JOIN tm_ward AS t2 ON t1.ward_id = t2.id
      INNER JOIN tm_than AS t3 ON t2.than_id = t3.id
      INNER JOIN tm_dsct AS t4 ON t3.dsct_id = t4.id
      INNER JOIN tm_disn AS t5 ON t4.disn_id = t5.id*/
    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $markets = DB::connection($this->db)->table('tm_mktm AS t1')
                    ->join('tm_ward AS t2', 't1.ward_id', '=', 't2.id')
                    ->join('tm_than AS t3', 't2.than_id', '=', 't3.id')
                    ->join('tm_dsct AS t4', 't3.dsct_id', '=', 't4.id')
                    ->join('tm_disn AS t5', 't4.disn_id', '=', 't5.id')
                    ->select(
                        't1.id AS mktm_id',
                        't1.mktm_code',
                        't1.mktm_name',
                        't1.lfcl_id',
                        DB::connection($this->db)->raw('concat(t2.ward_name, "<", t3.than_name, "<", t4.dsct_name, "<", t5.disn_name) AS ward_name')
                    )->orderBy('t1.id','ASC')->where(function ($query) use ($q) {
                        $query->where('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.mktm_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.mktm_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.ward_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t3.than_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t4.dsct_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t5.disn_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(500);
            } else {
                $markets = DB::connection($this->db)->table('tm_mktm AS t1')
                    ->join('tm_ward AS t2', 't1.ward_id', '=', 't2.id')
                    ->join('tm_than AS t3', 't2.than_id', '=', 't3.id')
                    ->join('tm_dsct AS t4', 't3.dsct_id', '=', 't4.id')
                    ->join('tm_disn AS t5', 't4.disn_id', '=', 't5.id')
                    ->select(
                        't1.id AS mktm_id',
                        't1.mktm_code',
                        't1.mktm_name',
                        't1.lfcl_id',
                        DB::connection($this->db)->raw('concat(t2.ward_name, "<", t3.than_name, "<", t4.dsct_name, "<", t5.disn_name) AS ward_name')
                    )->orderBy('t1.id','ASC')->paginate(500);
            }
            return view('master_data.market.index')->with('markets', $markets)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public
    function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $govDivisions = GovtDivision::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.market.create')->with("govDivisions", $govDivisions)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public
    function store(Request $request)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $market = new Market();
            $market->setConnection($this->db);
            $market->mktm_name = $request->name;
            $market->mktm_code = $request->code;
            $market->ward_id = $request->ward_id;
            $market->cont_id = $this->currentUser->country()->id;
            $market->lfcl_id = 1;
            $market->aemp_iusr = $this->currentUser->employee()->id;
            $market->aemp_eusr = $this->currentUser->employee()->id;
            $market->save();

            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Created');
        } catch (\Exception $e) {
            DB::connection($this->db)->rollback();
            throw $e;
        }
    }


    public
    function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $market = Market::on($this->db)->findorfail($id);
            return view('master_data.market.show')->with('market', $market);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public
    function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $market = Market::on($this->db)->findorfail($id);
            $govDivisions = GovtDivision::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.market.edit')->with('market', $market)->with('govDivisions', $govDivisions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function update(Request $request, $id)
    {
        $market = Market::on($this->db)->findorfail($id);
        $market->mktm_name = $request->name;
        $market->mktm_code = $request->code;
        $market->ward_id = $request->ward_id;
        $market->aemp_eusr = $this->currentUser->employee()->id;
        $market->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public
    function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $market = Market::on($this->db)->findorfail($id);
            $market->lfcl_id = $market->lfcl_id == 1 ? 2 : 1;
            $market->aemp_eusr = $this->currentUser->employee()->id;
            $market->save();
            return redirect('/market');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function siteFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Site(), 'outlet_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function siteUpload()
    {
        if ($this->userMenu->wsmu_crat) {
            return view('master_data.site.site_upload')->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public
    function siteInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Site(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function marketUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Market(), 'market_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function marketMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Market(), $request->file('import_file'));
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
    public function getAllMarket()
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Market(), 'market_list' . date("Y-m-d") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


}
