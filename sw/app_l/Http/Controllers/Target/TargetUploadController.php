<?php

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/23/2018
 * Time: 2:42 PM
 */

namespace App\Http\Controllers\Target;


use App\BusinessObject\Target;
use App\BusinessObject\NewTarget;
use App\BusinessObject\NewTarget2;
use App\MasterData\Employee;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Excel;

class TargetUploadController extends Controller
{
    private $access_key = 'TargetUploadController';
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

    public function target()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Target.maintain_target');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function uploadFormatGen(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            $employee = Employee::on($this->db)->where(['aemp_usnm' => $request->manager, 'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($employee != null) {
                return Excel::download(Target::create($employee, $request->trgt_date,$request->price_list), date("Y_m")."_".$request->manager.'_target_upload_format_' . $request->trgt_date . "_" . date("Y-m-d H:i:s") . '.xlsx');
            } else {
                return redirect()->back()->withInput()->with('danger', 'User name not Exists');
            }
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function uploadFormat()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $country_id = $this->currentUser->country()->id;
            $priceLists = DB::connection($this->db)->select("SELECT
                            t1.id        AS id,
                            t1.plmt_name AS name,
                            t1.plmt_code AS code
                            FROM tm_plmt AS t1
                            WHERE  t1.cont_id = $country_id");
            return view('Target.target_upload_format')->with("priceLists", $priceLists)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function upload()
    {
        if ($this->userMenu->wsmu_vsbl) {
            return view('Target.target_upload');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function uploadInsert(Request $request)
    {
        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new Target(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                   return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
               // Excel::import(new Target(), $request->file('import_file'));

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function filterTarget(Request $request)
    {
        $country_id = $this->currentUser->employee()->cont_id;
        $where = " t1.cont_id=$country_id";
        if ($request->trgt_date != "") {
            $year = date('Y', strtotime($request->trgt_date));
            $month = date('m', strtotime($request->trgt_date));
            $where .= " AND t1.trgt_year= '$year' and t1.trgt_mnth='$month'";
        }
        if ($request->manager != "") {
            $employee = Employee::on($this->db)->where('aemp_usnm', '=', $request->manager)->first();
            if ($employee != null) {
                $id=$employee->id;
                $where .= " AND t1.aemp_vusr=$id";
            }
        }

        $data = DB::connection($this->db)->select("SELECT
  t2.aemp_usnm                                              AS user_name,
  t2.aemp_name                                              AS supervisor_name,
  t1.aemp_vusr                                              AS supervisor_id,
  t1.trgt_year                                              AS year,
  t1.trgt_mnth                                              AS month_id,
  MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
  sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
  sum(t1.trgt_ramt)                                         AS initial_target_in_value
FROM tt_trgt AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_vusr = t2.id
  INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
  WHERE $where
GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_vusr, t2.aemp_usnm, t2.aemp_name;
");

        return $data;

    }

    public function bySalesMan($supervisor_id, $year, $month_id)
    {
        if ($this->userMenu->wsmu_vsbl) {

            $bySrdata = DB::connection($this->db)->select("SELECT
                        t2.id                                                     AS aemp_id,
                        t2.aemp_usnm                                              AS user_name,
                        t2.aemp_name                                              AS supervisor_name,
                        t1.aemp_susr                                              AS aemp_id,
                        t1.trgt_year                                              AS year,
                        t1.trgt_mnth                                              AS month,
                        MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
                        sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
                        sum(t1.trgt_ramt)                                         AS initial_target_in_value
                        FROM tt_trgt AS t1
                        INNER JOIN tm_aemp AS t2 ON t1.aemp_susr = t2.id
                        INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
                        WHERE t1.aemp_vusr=$supervisor_id and t1.trgt_year=$year and t1.trgt_mnth=$month_id
                        GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_susr, t2.aemp_usnm, t2.aemp_name
                        ");

            return view('Target.target_by_sr')->with("bySrdata", $bySrdata)->with('permission', $this->userMenu)->with('manager_id',$supervisor_id);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function byItem($supervisor_id, $year, $month_id)
    {
        if ($this->userMenu->wsmu_vsbl) {

            $bySrdata = DB::connection($this->db)->select("SELECT
                        t1.amim_id                                                AS amim_id,
                        t1.aemp_vusr                                              AS manager_id,
                        t2.aemp_name                                              AS supervisor_name,
                        t1.trgt_year                                              AS year,
                        t1.trgt_mnth                                              AS month,
                        MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
                        t3.amim_name                                              AS item_name,
                        sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
                        sum(t1.trgt_ramt)                                         AS initial_target_in_value
                        FROM tt_trgt AS t1
                        INNER JOIN tm_aemp AS t2 ON t1.aemp_vusr = t2.id
                        INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
                        WHERE t1.aemp_vusr = $supervisor_id AND t1.trgt_year = $year AND t1.trgt_mnth = $month_id
                        GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_vusr, t2.aemp_name, t3.amim_name,t1.amim_id,t1.aemp_vusr
                        ");

            return view('Target.target_by_item')->with("bySrdata", $bySrdata)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
    public function byCategory($supervisor_id, $year, $month_id)
    {
        if ($this->userMenu->wsmu_vsbl) {

            $bySrdata = DB::connection($this->db)->select("SELECT
  t2.aemp_name                                              AS supervisor_name,
  t1.trgt_year                                              AS year,
  t1.trgt_mnth                                              AS month,
  MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
  t3.amim_name                                              AS item_name,
  t4.itsg_name                                              AS subcategory,
  t5.itcg_name                                              AS category,
  sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
  sum(t1.trgt_ramt)                                         AS initial_target_in_value
FROM tt_trgt AS t1
  INNER JOIN tm_aemp AS t2 ON t1.aemp_vusr = t2.id
  INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
  INNER JOIN tm_itsg AS t4 ON t3.itsg_id = t4.id
  INNER JOIN tm_itcg AS t5 ON t4.itcg_id = t5.id
WHERE t1.aemp_vusr = $supervisor_id AND t1.trgt_year = $year AND t1.trgt_mnth = $month_id
GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_vusr, t2.aemp_name, t3.amim_name,t4.itsg_name,t5.itcg_name
");

            return view('Target.target_by_category')->with("bySrdata", $bySrdata)->with('permission', $this->userMenu);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
}

public function removeTarget($id,$year,$month,$manager){
    DB::connection($this->db)->select("DELETE FROM tt_trgt where aemp_vusr=$id AND trgt_year=$year AND trgt_mnth=$month");
    $country_id = $this->currentUser->employee()->cont_id;
        $where = " t1.cont_id=$country_id AND t1.trgt_year=$year AND t1.trgt_mnth=$month";
    if($manager){
        $where .=" AND t1.aemp_vusr=$manager ";
    }
    $data = DB::connection($this->db)->select("SELECT
            t2.aemp_usnm                                              AS user_name,
            t2.aemp_name                                              AS supervisor_name,
            t1.aemp_vusr                                              AS supervisor_id,
            t1.trgt_year                                              AS year,
            t1.trgt_mnth                                              AS month_id,
            MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
            sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
            sum(t1.trgt_ramt)                                         AS initial_target_in_value
            FROM tt_trgt AS t1
            INNER JOIN tm_aemp AS t2 ON t1.aemp_vusr = t2.id
            INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
            WHERE $where
            GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_vusr, t2.aemp_usnm, t2.aemp_name;
            ");
    return $data;

}
public function removeTargetBySr($id,$year,$month_id,$supervisor_id){
    DB::connection($this->db)->select("DELETE FROM tt_trgt where aemp_susr=$id AND trgt_year=$year AND trgt_mnth=$month_id");
    if ($this->userMenu->wsmu_vsbl) {
        $bySrdata = DB::connection($this->db)->select("SELECT
                    t2.id                                                     AS aemp_id,
                    t2.aemp_usnm                                              AS user_name,
                    t2.aemp_name                                              AS supervisor_name,
                    t1.aemp_susr                                              AS aemp_id,
                    t1.trgt_year                                              AS year,
                    t1.trgt_mnth                                              AS month,
                    MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
                    sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
                    sum(t1.trgt_ramt)                                         AS initial_target_in_value
                    FROM tt_trgt AS t1
                    INNER JOIN tm_aemp AS t2 ON t1.aemp_susr = t2.id
                    INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
                    WHERE t1.aemp_vusr=$supervisor_id and t1.trgt_year=$year and t1.trgt_mnth=$month_id
                    GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_susr, t2.aemp_usnm, t2.aemp_name,t2.id
                    ");

        return view('Target.target_by_sr')->with("bySrdata", $bySrdata)->with('permission', $this->userMenu)->with('manager_id',$supervisor_id);
    } else {
        return redirect()->back()->with('danger', 'Access Limited');
    }
}

public function removeTargetByItem($amim_id,$year,$month_id,$supervisor_id){
    DB::connection($this->db)->select("DELETE FROM tt_trgt where amim_id=$amim_id AND trgt_year=$year AND trgt_mnth=$month_id AND aemp_vusr=$supervisor_id");
    if ($this->userMenu->wsmu_vsbl) {
        $bySrdata = DB::connection($this->db)->select("SELECT
                    t1.amim_id                                                AS amim_id,
                    t1.aemp_vusr                                              AS manager_id,
                    t2.aemp_name                                              AS supervisor_name,
                    t1.trgt_year                                              AS year,
                    t1.trgt_mnth                                              AS month,
                    MONTHNAME(concat(t1.trgt_year, '-', t1.trgt_mnth, '-00')) AS month_name,
                    t3.amim_name                                              AS item_name,
                    sum(t1.trgt_rqty / t3.amim_duft)                          AS initial_target_in_ctn,
                    sum(t1.trgt_ramt)                                         AS initial_target_in_value
                    FROM tt_trgt AS t1
                    INNER JOIN tm_aemp AS t2 ON t1.aemp_vusr = t2.id
                    INNER JOIN tm_amim AS t3 ON t1.amim_id = t3.id
                    WHERE t1.aemp_vusr = $supervisor_id AND t1.trgt_year = $year AND t1.trgt_mnth = $month_id
                    GROUP BY t1.trgt_year, t1.trgt_mnth, t1.aemp_vusr, t2.aemp_name, t3.amim_name,t1.amim_id,t1.aemp_vusr
                    ");

        return view('Target.target_by_item')->with("bySrdata", $bySrdata)->with('permission', $this->userMenu);
    } else {
        return redirect()->back()->with('danger', 'Access Limited');
    }
}

public function newTarget(){
    if ($this->userMenu->wsmu_vsbl) {
        $country_id = $this->currentUser->country()->id;
        $priceLists = DB::connection($this->db)->select("SELECT
                        t1.id        AS id,
                        t1.plmt_name AS name,
                        t1.plmt_code AS code
                        FROM tm_plmt AS t1
                        WHERE  t1.cont_id = $country_id");
        return view('Target.NewTarget.new_target_format')->with("priceLists", $priceLists)->with('permission', $this->userMenu);
    } else {
        return redirect()->back()->with('danger', 'Access Limited');
    }
}
public function uploadFormatGenNew(Request $request)
{
    if ($this->userMenu->wsmu_crat) {
            return Excel::download(NewTarget::create( $request->trgt_date,$request->price_list), date("Y_m")."_".'_target_upload_format_' . $request->trgt_date . "_" . date("Y-m-d H:i:s") . '.xlsx');
        
    } else {
        return redirect()->back()->with('danger', 'Access Limited');
    }
}
public function uploadInsertNew(Request $request)
    {

        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new NewTarget(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                   return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
               // Excel::import(new Target(), $request->file('import_file'));

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

// Target All Upload
public function uploadFormatGenNew2(Request $request)
{
    if ($this->userMenu->wsmu_crat) {
        return Excel::download(new NewTarget2(),'target' . date("Y-m-d") . '.xlsx' );
        
    } else {
        return redirect()->back()->with('danger', 'Access Limited');
    }
}
public function uploadInsertNew2(Request $request)
    {

        if ($this->userMenu->wsmu_crat) {
            if ($request->hasFile('import_file')) {
                DB::connection($this->db)->beginTransaction();
                try {
                    Excel::import(new NewTarget2(), $request->file('import_file'));
                    DB::connection($this->db)->commit();
                   return redirect()->back()->with('success', 'Successfully Uploaded');
                } catch (\Exception $e) {
                    DB::connection($this->db)->rollback();
                    return redirect()->back()->with('danger', ' Data wrong ' . $e);
                }
               // Excel::import(new Target(), $request->file('import_file'));

            }
            return back()->with('danger', ' File Not Found');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }
}