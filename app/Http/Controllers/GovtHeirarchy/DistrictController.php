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

class DistrictController extends Controller
{
    private $access_key = 'tm_dsct';
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

    public function index(Request $request)
    {
        $q = '';
        if ($this->userMenu->wsmu_vsbl) {
            if ($request->has('search_text')) {
                $q = request('search_text');
                $districts = District::on($this->db)->where(function ($query) use ($q) {
                    $query->where('dsct_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('dsct_code', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(100)->setPath('');
            } else {
                $districts = District::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
            }
            return view('master_data.district.index')->with('districts', $districts)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $govDivisions = GovtDivision::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.district.create')->with('govDivisions', $govDivisions)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function store(Request $request)
    {
        $district = new District();
        $district->setConnection($this->db);
        $district->dsct_name = $request->dsct_name;
        $district->dsct_code = $request->dsct_code;
        $district->disn_id = $request->disn_id;
        $district->cont_id = $this->currentUser->employee()->cont_id;
        $district->lfcl_id = 1;
        $district->aemp_iusr = $this->currentUser->employee()->id;
        $district->aemp_eusr = $this->currentUser->employee()->id;
        $district->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $district = District::on($this->db)->findorfail($id);
            return view('master_data.district.show')->with('district', $district);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $govDivisions = GovtDivision::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $district = District::on($this->db)->findorfail($id);
            return view('master_data.district.edit')->with('govDivisions', $govDivisions)->with('district', $district)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $district = District::on($this->db)->findorfail($id);
        $district->dsct_name = $request->dsct_name;
        $district->dsct_code = $request->dsct_code;
        $district->disn_id = $request->disn_id;
        $district->aemp_eusr = $this->currentUser->employee()->id;
        $district->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $aDistrict= District::on($this->db)->findorfail($id);
            $aDistrict->lfcl_id = $aDistrict->lfcl_id == 1 ? 2 : 1;
            $aDistrict->aemp_eusr = $this->currentUser->employee()->id;
            $aDistrict->save();
            return redirect('/district');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function districtUploadFormatGen(Request $request){
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new District(), 'district_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function districtMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new District(), $request->file('import_file'));
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
