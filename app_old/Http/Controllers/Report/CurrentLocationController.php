<?php
namespace App\Http\Controllers\Report;
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class CurrentLocationController extends Controller
{
    private $access_key = 'CurrentLocationController';
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

    public function current_map()
    {
        if ($this->userMenu->wsmu_vsbl) {

            $emp_id=$this->currentUser->employee()->id;
            $country_id=$this->currentUser->employee()->cont_id;
            $data = DB::select("SELECT
TIMESTAMPDIFF(minute,t1.times_stamp,now()) as dif,
  t1.emp_id,
  t2.name,
  t1.date,
  DATE_FORMAT(t1.times_stamp, '%l.%i%p') AS time,
  t1.lat,
  t1.lon,
  t3.email as user_name,
  t2.mobile,
  t5.name as role
FROM tblt_current_location AS t1
  INNER JOIN tbld_employee AS t2 ON t1.emp_id = t2.id
  INNER JOIN users as t3 ON t2.user_id=t3.id
  INNER JOIN tbld_role as t5 ON t2.role_id=t5.id
WHERE t1.country_id=$country_id 
ORDER BY t1.times_stamp ASC");
            return view('report.current_location.current_map')->with("locations", $data)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }

    }



}