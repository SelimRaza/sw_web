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

class WardController extends Controller
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

                $wards = DB::connection($this->db)->table('tm_ward AS t1')
                    ->join('tm_than AS t2', 't1.than_id', '=', 't2.id')
                    ->select('t1.id AS id', 't1.ward_name AS ward_name', 't1.ward_code', 't1.than_id', 't2.than_name', 't1.lfcl_id')->orderBy('t1.id','ASC')
                    ->where(function ($query) use ($q) {
                        $query->Where('t1.ward_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.ward_code', 'LIKE', '%' . $q . '%')
                            ->orWhere('t1.than_id', 'LIKE', '%' . $q . '%')
                            ->orWhere('t2.than_name', 'LIKE', '%' . $q . '%');
                    })
                    ->paginate(100);

                /* $wards = Ward::on($this->db)->where(function ($query) use ($q) {
                     $query->where('ward_name', 'LIKE', '%' . $q . '%')
                         ->orWhere('ward_code', 'LIKE', '%' . $q . '%')
                         ->orWhere('than_id', 'LIKE', '%' . $q . '%');
                 })->where('cont_id', $this->currentUser->country()->id)->paginate(100)->setPath('');*/


            } else {
                // $cont_id=$this->currentUser->country()->id;
                // $wards=DB::connection($this->db)->table('tm_ward t1')
                //             ->select('t1.*','t2.*')
                //             ->join('tm_than t2','t1.than_id','=','t2.id')
                //             ->where('t1.cont_id',$cont_id)
                //             ->paginate(100);
                //$wards = Ward::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
                $wards = DB::connection($this->db)->table('tm_ward AS t1')
                    ->join('tm_than AS t2', 't1.than_id', '=', 't2.id')
                    ->select('t1.id AS id', 't1.ward_name AS ward_name', 't1.ward_code', 't1.than_id', 't2.than_name', 't1.lfcl_id')->orderBy('t1.id','ASC')
                    ->paginate(100);

            }
            return view('master_data.ward.index')->with('wards', $wards)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $govThana = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.ward.create')->with('govThana', $govThana)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $ward = new Ward();
            $ward->setConnection($this->db);
            $ward->ward_name = $request->ward_name;
            $ward->ward_code = $request->ward_code;
            $ward->than_id = $request->than_id;
            $ward->cont_id = $this->currentUser->employee()->cont_id;
            $ward->lfcl_id = 1;
            $ward->aemp_iusr = $this->currentUser->employee()->id;
            $ward->aemp_eusr = $this->currentUser->employee()->id;
            $ward->save();
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Added');
        } catch (Exception $e) {
            DB::connection($this->db)->rollback();
            //throw $e;
            return redirect()->back()->withInput()->with('danger', 'Not Created');
        }
    }

    /*ward_name
    ward_code
    than_id
    */
    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $ward = Ward::on($this->db)->findorfail($id);
            return view('master_data.ward.show')->with('ward', $ward);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $govThana = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $aWard = Ward::on($this->db)->findorfail($id);
            return view('master_data.ward.edit')->with('govThana', $govThana)->with('aWard', $aWard)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $aWard = Ward::on($this->db)->findorfail($id);
        $aWard->ward_name = $request->ward_name;
        $aWard->ward_code = $request->ward_code;
        $aWard->than_id = $request->than_id;
        $aWard->aemp_eusr = $this->currentUser->employee()->id;
        $aWard->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $aWard = Ward::on($this->db)->findorfail($id);
            $aWard->lfcl_id = $aWard->lfcl_id == 1 ? 2 : 1;
            $aWard->aemp_eusr = $this->currentUser->employee()->id;
            $aWard->save();
            return redirect('/ward');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function wardUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Ward(), 'ward_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function wardMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Ward(), $request->file('import_file'));
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
    public function getAllWard()
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Ward(), 'ward_list' . date("Y-m-d") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
