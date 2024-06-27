<?php

namespace App\Http\Controllers\Demo;
use App\Http\Controllers\Controller;
use App\MasterData\Egpr;
use App\MasterData\Ezpr;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    private $access_key = 'tm_dlrm';
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
        $companies=DB::connection($this->db)->select("select * from tm_acmp");
        return view('demo.company_group_mapping')->with('companies', $companies);

    }

    public function jsonLoadCompanyGroupName(Request $request){

        return $companies=DB::connection($this->db)->select("select * from tm_slgp where acmp_id='$request->company_id'");

    }


    public function loadComapnyGroup(Request $request){


         if ($request->company_id && $request->group_id){

             $results=DB::connection($this->db)->table('tm_acmp')
                ->join('tm_slgp', 'tm_slgp.acmp_id', '=', 'tm_acmp.id')
                ->join('tl_sgsm', 'tl_sgsm.slgp_id', '=', 'tm_slgp.id')
                ->join('tm_aemp', 'tm_aemp.id', '=', 'tl_sgsm.aemp_id')
                ->select('tm_aemp.id as aemp_id','tm_aemp.aemp_name','tm_aemp.aemp_usnm','tm_slgp.slgp_name','tm_acmp.acmp_name','tm_slgp.id as slgp_id','tm_acmp.id as acmp_id')
                ->where(['tm_acmp.id'=>$request->company_id,'tm_slgp.id'=>$request->group_id])
                ->get();
             // $results = DB::connection($this->db)
                // ->select("SELECT t4.id as aemp_id,t4.aemp_name,t4.aemp_usnm,t2.slgp_name,t1.acmp_name,t2.id as slgp_id,t1.id as acmp_id
         // FROM `tm_acmp`t1 JOIN tm_slgp t2 ON(t1.id=t2.acmp_id) 
         // JOIN tl_sgsm t3 ON(t2.id=t3.slgp_id)
         // JOIN tm_aemp t4 ON(t3.aemp_id=t4.id)
         // WHERE t1.id='$request->company_id' AND t2.id='$request->group_id'");


            $companies=DB::connection($this->db)->select("select * from tm_acmp");
            return view('demo.company_group_mapping')
                  ->with('results',$results)
                  ->with('companies',$companies)
                  ->with('company_id',$request->company_id)
                  ->with('group_id',$request->group_id);


         }elseif($request->company_id){

             $result2=DB::connection($this->db)->table('tm_acmp')
                 ->join('tm_slgp', 'tm_slgp.acmp_id', '=', 'tm_acmp.id')
                 ->select('tm_slgp.slgp_name','tm_acmp.acmp_name','tm_slgp.id as slgp_id','tm_acmp.id as acmp_id')
                 ->where(['tm_acmp.id'=>$request->company_id])
                 ->paginate(2000);
             $companies=DB::connection($this->db)->select("select * from tm_acmp");

             return view('demo.company_group_mapping')
                 ->with('result2',$result2)
                 ->with('companies',$companies)
                 ->with('company_id',$request->company_id)
                 ->with('group_id','');


         }



    }

    public function showGroupUser($id){

        $results=DB::connection($this->db)->table('tm_acmp')
            ->join('tm_slgp', 'tm_slgp.acmp_id', '=', 'tm_acmp.id')
            ->join('tl_sgsm', 'tl_sgsm.slgp_id', '=', 'tm_slgp.id')
            ->join('tm_aemp', 'tm_aemp.id', '=', 'tl_sgsm.aemp_id')
            ->select('tm_aemp.id as aemp_id','tm_aemp.aemp_name','tm_aemp.aemp_usnm','tm_slgp.slgp_name','tm_acmp.acmp_name','tm_slgp.id as slgp_id','tm_acmp.id as acmp_id')
            ->where(['tm_slgp.id'=>$id])
            ->paginate(2000);

        return view('demo.gorup_user_details')->with('results',$results);

    }

    public function editMapping(Request $request){

        return $zoones=DB::connection($this->db)->select("select * from tm_zone");


    }

    public function employeeGroupPermission(Request $request,$id){

        $results=DB::connection($this->db)
                 ->select("SELECT tm_slgp.id,tm_slgp.slgp_name,tm_slgp.slgp_code,t1.status FROM (SELECT tm_aemp.id,tm_aemp.aemp_name,tm_aemp.aemp_usnm,tl_egpr.status,tl_egpr.group_id
                    FROM tm_aemp
                    JOIN tl_egpr ON tl_egpr.aemp_id=tm_aemp.id
                    WHERE tl_egpr.aemp_id='$id') as t1
                    RIGHT JOIN tm_slgp ON tm_slgp.id=t1.group_id
                    ORDER BY tm_slgp.slgp_code ASC");

        $result2=DB::connection($this->db)
                ->select("SELECT tm_zone.id,tm_zone.zone_name,tm_zone.zone_code,t1.status FROM (SELECT tm_aemp.id,tm_aemp.aemp_name,tm_aemp.aemp_usnm,tl_ezpr.status,tl_ezpr.zone_id
                    FROM tm_aemp
                    JOIN tl_ezpr ON tl_ezpr.aemp_id=tm_aemp.id
                    WHERE tl_ezpr.aemp_id='$id') as t1
                    RIGHT JOIN tm_zone ON tm_zone.id=t1.zone_id
                    ORDER BY tm_zone.zone_code ASC");
        return view('demo.group_zoon_permission')
               ->with('results',$results)
               ->with('result2',$result2)
               ->with('emp_id',$id);

    }


    public function assignEmpGroupZonePermission(Request $request){

        $check_all_group=explode(",",$request->check_all_group);
        $check_all_zone=explode(",",$request->check_all_zone);
        $uncheck_all_group=explode(",",$request->uncheck_all_group);
        $uncheck_all_zone=explode(",",$request->uncheck_all_zones);
        $groupCheckLenght=sizeof($check_all_group);
        $uncheckGroupLenght=sizeof($uncheck_all_group);
        $zoneCheckLenght=sizeof($check_all_zone);
        $uncheckZoneLenght=sizeof($uncheck_all_zone);

        if($groupCheckLenght>=1 && !empty($request->check_all_group)){

            echo $this->assignEmpGroupPermission($check_all_group,$request->employee_id);
        }

        if($uncheckGroupLenght>1){

            echo $this->deleteAllUncheckGroup($request->uncheck_all_group,$request->employee_id);
        }

        if($zoneCheckLenght>=1 && !empty($request->check_all_zone)){

            echo $this->assignEmpZoonPermission($check_all_zone,$request->employee_id);

        }
        if($uncheckZoneLenght>1){

            echo $this->deleteAllUncheckZone($request->uncheck_all_zones,$request->employee_id);
        }

    }


    public function assignEmpGroupPermission($check_all_group,$employee_id){


        $lenght=sizeof($check_all_group);
        for($i=0; $i<$lenght;$i++){

            $exist=Egpr::on($this->db)
                ->where('aemp_id', '=',$employee_id)
                ->where('group_id', '=',$check_all_group[$i])
                ->get();


            if(count($exist)>0){

            }else{

                $ezpr = new Egpr();
                $ezpr->setConnection($this->db);
                $ezpr->aemp_id = $employee_id;
                $ezpr->group_id = $check_all_group[$i];
                $ezpr->save();

            }


        }

        return 100;

    }

    public function assignEmpZoonPermission($check_all_zone,$employee_id){

        $lenght=sizeof($check_all_zone);
        for($i=0; $i<$lenght;$i++){

            $exist=Ezpr::on($this->db)
                ->where('aemp_id', '=',$employee_id)
                ->where('zone_id', '=',$check_all_zone[$i])
                ->get();
            if(count($exist)>0){

            }else{

                $ezpr = new Ezpr();
                $ezpr->setConnection($this->db);
                $ezpr->aemp_id = $employee_id;
                $ezpr->zone_id = $check_all_zone[$i];
                $ezpr->save();

            }

        }

        return 200;
    }

    public function deleteAllUncheckGroup($uncheck_all_group,$employee_id){


        $result=DB::connection($this->db)->table("tl_egpr")
              ->where('aemp_id',$employee_id)
              ->whereIn('group_id',explode(",",$uncheck_all_group))->delete();
               return 300;


    }

    public function deleteAllUncheckZone($uncheck_all_zones,$employee_id){


        $result=DB::connection($this->db)->table("tl_ezpr")
              ->where('aemp_id',$employee_id)
              ->whereIn('zone_id',explode(",",$uncheck_all_zones))->delete();
              return 400;

    }
}
