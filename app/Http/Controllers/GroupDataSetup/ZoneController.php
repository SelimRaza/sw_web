<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\Company;
use App\MasterData\Country;
use App\MasterData\Division;
use App\MasterData\Region;
use App\MasterData\Zone;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;
class ZoneController extends Controller
{
    private $access_key = 'tm_zone';
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
            $country_id = $this->currentUser->country()->id;
            $zones = DB::connection($this->db)->select("SELECT t1.`zone_name`, t1.`zone_code`, t2.dirg_name, t2.dirg_code, t1.id, t1.lfcl_id 
FROM `tm_zone` t1 INNER JOIN tm_dirg t2 ON t1.`dirg_id`=t2.id WHERE t1.`cont_id`=$country_id");
           // $zones = Zone::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.zone.index')->with("zones", $zones)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }

    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $country_id = $this->currentUser->country()->id;
            $emp_id = $this->currentUser->employee()->id;

            $regions = Region::on($this->db)->where('cont_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.zone.create')->with('regions', $regions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $zone = new Zone();
        $zone->setConnection($this->db);
        $zone->zone_name = $request->name;
        $zone->zone_code = $request->code;
        $zone->dirg_id = $request->region_id;
        $zone->cont_id = $this->currentUser->country()->id;
        $zone->lfcl_id = 1;
        $zone->aemp_iusr = $this->currentUser->employee()->id;
        $zone->aemp_eusr = $this->currentUser->employee()->id;
        $zone->var = 1;
        $zone->attr1 = '';
        $zone->attr2 = '';
        $zone->attr3 = 0;
        $zone->attr4 = 0;
        $zone->save();
        return redirect()->back()->with('success', 'successfully Added');
    }

    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $zone = Zone::on($this->db)->findorfail($id);
            return view('master_data.zone.show')->with('zone', $zone);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $zone = Zone::on($this->db)->findorfail($id);
            $regions = DB::connection($this->db)->select("SELECT DISTINCT t1.dirg_name,t1.dirg_code, t1.id FROM tm_dirg t1 INNER JOIN tm_zone t2 ON t1.id=t2.dirg_id");
           // $regions = Region::where('country_id', '=', $this->currentUser->country()->id)->get();
            return view('master_data.zone.edit')->with('zone', $zone)->with('regions', $regions);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $zone = Zone::on($this->db)->findorfail($id);
        $zone->zone_name = $request->name;
        $zone->dirg_id = $request->region_id;
        $zone->zone_code = $request->code;
        $zone->aemp_eusr = $this->currentUser->employee()->id;
        $zone->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $zone = Zone::on($this->db)->findorfail($id);
            $zone->lfcl_id = $zone->lfcl_id == 1 ? 2 : 1;
            $zone->aemp_eusr = $this->currentUser->employee()->id;
            $zone->save();
            return redirect('/zone');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function filterZone(Request $request)
    {
        $divisions = DB::connection($this->db)->select("SELECT
  t1.id as id,
  t1.zone_name as name
FROM tm_zone AS t1 WHERE t1.dirg_id=$request->region_id");
        return Response::json($divisions);
    }


    public function zoneUploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            return Excel::download(new Zone(), 'zone_upload_format_' . date("Y-m-d H:i:s") . '.xlsx');

            /*$routeArray[] = ['group_id','region_id', 'zone_name', 'zone_code'];
            Excel::create('zone_upload_format_' . date("Y-m-d H:i:s"), function ($excel) use ($routeArray) {
                $excel->setTitle('Zone Upload Format');
                $excel->setCreator('FMS MDR')->setCompany('PRAN RFL Group');
                $excel->setDescription('PRAN RFL Group File System');
                $excel->sheet('Zone Upload Format', function ($sheet) use ($routeArray) {
                    $sheet->fromArray($routeArray, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function zoneMasterUploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::beginTransaction();
                try {
                    Excel::import(new Zone(), $request->file('import_file'));
                    DB::commit();
                    return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }

                /*$path = Input::file('import_file')->getRealPath();
                $data = Excel::load($path, function ($reader) {
                })->get();
                if (!empty($data) && $data->count()) {
                    DB::beginTransaction();
                    try {
                        $i = 0;
                        foreach ($data as $key => $value) {
                            $i++;
                            $zone = new Zone();
                            $zone->name = $value->zone_name;
                            $zone->code = $value->zone_code;
                            $zone->region_id = $value->region_id;
                            $zone->sales_group_id = $value->group_id;
                            $zone->status_id = 1;
                            $zone->country_id = $this->currentUser->employee()->cont_id;
                            $zone->created_by = $this->currentUser->employee()->id;
                            $zone->updated_by = $this->currentUser->employee()->id;
                            $zone->save();
                        }

                        DB::commit();
                        return redirect()->back()->with('success', 'Successfully Uploaded');
                    } catch (\Exception $e) {
                        DB::rollback();
                      //  return redirect()->back()->with('danger', ' ON Line ' . $i);
                        throw $e;
                    }
                }*/
            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }
}
