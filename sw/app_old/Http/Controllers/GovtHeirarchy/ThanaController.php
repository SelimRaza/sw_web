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

class ThanaController extends Controller
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
                $thana = Thana::on($this->db)->where(function ($query) use ($q) {
                    $query->where('than_name', 'LIKE', '%' . $q . '%')
                        ->orWhere('than_code', 'LIKE', '%' . $q . '%');
                })->where('cont_id', $this->currentUser->country()->id)->paginate(100)->setPath('');
            } else {
                $thana = Thana::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->paginate(100);
            }
            return view('master_data.thana.index')->with('thana', $thana)->with('permission', $this->userMenu)->with('search_text', $q);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $govDistrict = District::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.thana.create')->with('govDistrict', $govDistrict)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function store(Request $request)
    {
        DB::connection($this->db)->beginTransaction();
        try {
            $thana = new Thana();
            $thana->setConnection($this->db);
            $thana->than_name = $request->than_name;
            $thana->than_code = $request->than_code;
            $thana->dsct_id = $request->dsct_id;
            $thana->cont_id = $this->currentUser->employee()->cont_id;
            $thana->lfcl_id = 1;
            $thana->aemp_iusr = $this->currentUser->employee()->id;
            $thana->aemp_eusr = $this->currentUser->employee()->id;
            $thana->save();
            DB::connection($this->db)->commit();
            return redirect()->back()->with('success', 'successfully Added');
        } catch (Exception $e) {
            DB::connection($this->db)->rollback();
            //throw $e;
            return redirect()->back()->withInput()->with('danger', 'Not Created');
        }
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $thana = Thana::on($this->db)->findorfail($id);
            return view('master_data.thana.show')->with('thana', $thana);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $govDistrict = District::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            $aThana = Thana::on($this->db)->findorfail($id);
            return view('master_data.thana.edit')->with('govDistrict', $govDistrict)->with('aThana', $aThana)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $aThana = Thana::on($this->db)->findorfail($id);
        $aThana->than_name = $request->than_name;
        $aThana->than_code = $request->than_code;
        $aThana->dsct_id = $request->dsct_id;
        $aThana->aemp_eusr = $this->currentUser->employee()->id;
        $aThana->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $aThana = Thana::on($this->db)->findorfail($id);
            $aThana->lfcl_id = $aThana->lfcl_id == 1 ? 2 : 1;
            $aThana->aemp_eusr = $this->currentUser->employee()->id;
            $aThana->save();
            return redirect('/thana');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function thanaUploadFormatGen(Request $request){
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Thana(), 'thana_format_' . date("Y-m-d H:i:s") . '.xlsx');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function thanaMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Thana(), $request->file('import_file'));
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
