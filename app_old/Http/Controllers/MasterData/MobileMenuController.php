<?php

namespace App\Http\Controllers\MasterData;


use App\MasterData\Employee;
use App\Menu\MobileEmpMenu;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class MobileMenuController extends Controller
{
    private $access_key = 'tbld_mobile_menu';
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


    public function create()
    {

        if ($this->userMenu->wsmu_vsbl) {
            return view('master_data.mobile_menu.create');
        } else {
            return view('theme.access_limit');
        }

    }


    public function store(Request $request)
    {
        // dd($request);
        if ($this->userMenu->wsmu_updt) {

            DB::connection($this->db)->beginTransaction();
            try {
                // dd($request->menu_id);
                DB::connection($this->db)->table('tl_aumn')
                    ->where(['aemp_id' => $request->emp_id, 'cont_id' => $this->currentUser->country()->id])
                    ->update(['aumn_vsbl' => 0]);

                if (isset($request->menu_id)) {
                    foreach ($request->menu_id as $menu_id) {
                        $user_menu = MobileEmpMenu::on($this->db)->where(['aemp_id' => $request->emp_id, 'amnu_id' => $menu_id])->first();
                        //dd($user_menu);
                        if ($user_menu != null) {
                            $user_menu->amnu_id = $menu_id;
                            $user_menu->aemp_id = $request->emp_id;
                            $user_menu->aumn_vsbl = 1;
                            $user_menu->aemp_eusr = $this->currentUser->employee()->id;
                            $user_menu->save();
                        } else {
                            $user_menu = new MobileEmpMenu();
                            $user_menu->setConnection($this->db);
                            $user_menu->amnu_id = $menu_id;
                            $user_menu->aemp_id = $request->emp_id;
                            $user_menu->aumn_vsbl = 1;
                            $user_menu->lfcl_id = 1;
                            $user_menu->cont_id = $this->currentUser->country()->id;
                            $user_menu->aemp_iusr = $this->currentUser->employee()->id;
                            $user_menu->aemp_eusr = $this->currentUser->employee()->id;
                            $user_menu->save();
                        }
                    }
                }
                DB::connection($this->db)->commit();
                return redirect()->back()->with('success', 'successfully Updated Access');
            } catch (\Exception $e) {
                DB::connection($this->db)->rollback();
                throw $e;
            }

        } else {
            return redirect()->back()->with('danger', 'Access Limited Unable to Update');
        }
    }


    public function filterMenu(Request $request)
    {
        $country_id = $this->currentUser->country()->id;
        $users = Employee::on($this->db)->where(['aemp_usnm' => $request->user_name,'cont_id' => $country_id])->first();
        if ($users != null) {
            $menuList = DB::connection($this->db)->select("SELECT
$users->id AS user_id,
t1.id,
t1.amnu_name as name,
case when t2.id>0 then '1' else '0' end visibility
FROM tm_amnu  t1 LEFT JOIN tl_aumn  t2 ON t1.id = t2.amnu_id and t2.aumn_vsbl=1 AND t2.aemp_id = $users->id WHERE t1.cont_id=$country_id");
            return Response::json($menuList);
        } else {
            return array();
        }

    }
}
