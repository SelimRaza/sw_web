<?php

namespace App\Http\Controllers\MasterData;


use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class MenuController extends Controller
{
    private $access_key = 'tblt_user_menu';
    private $currentUser;
    private $userMenu;
  //private $db;


    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key, 'cont_id' => $this->currentUser->country()->id])->first();
            if ($subMenu != null) {
                $this->userMenu = UserMenu::where(['users_id' => Auth::user()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });

    }


    public function create()
    {

        if ($this->userMenu->wsmu_vsbl) {
            $cou = $this->currentUser->country()->id;
            /*$users=  DB::connection($this->db)->select("SELECT
  t1.id,
  t1.aemp_name ,
  t1.aemp_usnm 
FROM tm_aemp  t1 WHERE t1.cont_id=$cou");*/
            return view('master_data.menu.create');
        } else {
            return view('theme.access_limit');
        }

    }

    private function getKey($array, $key)
    {
        if ($array) {
            return array_key_exists($key, $array) == 1 ? 1 : 0;
        }
        return 0;
    }

    public function store(Request $request)
    {
        if ($this->userMenu->wsmu_updt) {
            DB::beginTransaction();
            try {
                DB::table('tl_wsmu')
                    ->where(['users_id' => $request->user_id, 'cont_id' => $this->currentUser->country()->id])
                    ->update(['wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0]);
                if (sizeof($request->sub_menu_id) > 0) {
                    foreach ($request->sub_menu_id as $sub_menu_id) {
                        $user_menu = UserMenu::where(['users_id' => $request->user_id, 'wsmn_id' => $sub_menu_id])->first();
                        if ($user_menu != null) {
                            $user_menu->wsmu_vsbl = 1;
                            $user_menu->wsmu_crat = $this->getKey($request->create, $sub_menu_id);
                            $user_menu->wsmu_read = $this->getKey($request->read, $sub_menu_id);
                            $user_menu->wsmu_updt = $this->getKey($request->update, $sub_menu_id);
                            $user_menu->wsmu_delt = $this->getKey($request->delete, $sub_menu_id);
                            $user_menu->users_eusr = $this->currentUser->id;
                            $user_menu->save();
                        } else {
                            $user_menu = new UserMenu();
                            $user_menu->wsmn_id = $sub_menu_id;
                            $user_menu->users_id = $request->user_id;
                            $user_menu->wsmu_vsbl = 1;
                            $user_menu->wsmu_crat = $this->getKey($request->create, $sub_menu_id);
                            $user_menu->wsmu_read = $this->getKey($request->read, $sub_menu_id);
                            $user_menu->wsmu_updt = $this->getKey($request->update, $sub_menu_id);
                            $user_menu->wsmu_delt = $this->getKey($request->delete, $sub_menu_id);
                            $user_menu->lfcl_id = 1;
                            $user_menu->cont_id = $this->currentUser->country()->id;
                            $user_menu->users_iusr = $this->currentUser->id;
                            $user_menu->users_eusr = $this->currentUser->id;
                            $user_menu->var = 0;
                            $user_menu->attr1 = '';
                            $user_menu->attr2 = '';
                            $user_menu->attr3 = 0;
                            $user_menu->attr4 = 0;
                            $user_menu->save();
                        }
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'successfully Updated Access');
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited Unable to Update');
        }
    }


    public function filterMenu(Request $request)
    {
        $country_id = $this->currentUser->country()->id;
        $users = User::where(['email' => $request->user_name])->first();
        if ($users != null) {
            $menuList = DB::select("SELECT
   $users->id AS user_id,
  t1.id AS id,
  t1.wsmn_name AS name,
  t1.wsmn_wurl AS url,
  case when t2.id>0 then '1' else '0' end visibility,
  case when t2.`wsmu_crat`=1 then '1' else '0' end c,
  case when t2.`wsmu_read`=1 then '1' else '0' end r,
  case when t2.`wsmu_updt`=1 then '1' else '0' end u,
  case when t2.`wsmu_delt`=1 then '1' else '0' end d,
  t3.wmnu_name AS menu_name
FROM tm_wsmn  t1
 LEFT JOIN tl_wsmu  t2 ON t1.id = t2.wsmn_id and t2.wsmu_vsbl=1 AND t2.users_id = $users->id 
INNER JOIN tm_wmnu t3 ON t1.wmnu_id = t3.id
WHERE  t1.cont_id=$country_id
ORDER BY t3.id,t1.wsmn_oseq ASC ");
            return Response::json($menuList);
        } else {
            return array();
        }

    }
}
