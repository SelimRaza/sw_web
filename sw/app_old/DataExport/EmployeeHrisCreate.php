<?php

namespace App\DataExport;

use App\MasterData\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeHrisCreate extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_amng';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public static function create($id)
    {
        $instance = new self();
        $instance->amng_id = $id;
        return $instance;
    }

    public function headings(): array
    {
        return ['user_name'];
    }

    public function array(array $array)
    {
        $userId=array();

        foreach ($array as $row) {
            $value = (object)$row;
            $value->user_name;
            $emp = Employee::on($this->db)->where(['aemp_usnm' => $request->aemp_usnm])->first();
            if ($emp == null) {
                $staff_detiails = "http://hris.prangroup.com:8686/api/hrisapi.svc/Staff/$request->aemp_usnm";
                $country_data = json_decode(file_get_contents($staff_detiails));
                $staffResult = json_decode($country_data->StaffResult);
                if ($staffResult != null) {
                    DB::connection($this->db)->beginTransaction();
                    try {
                        $user = User::where('email', '=', $request->aemp_usnm)->first();
                        if ($user == null) {
                            $user = User::create([
                                'name' => $staffResult[0]->NAME,
                                'email' => trim($request->aemp_usnm),
                                'password' => bcrypt(trim($request->aemp_usnm)),
                                'remember_token' => md5(uniqid(rand(), true)),
                                'lfcl_id' => 1,
                                'cont_id' => $this->currentUser->country()->id,
                            ]);
                        }
                        $emp = new Employee();
                        $emp->setConnection($this->db);
                        $emp->aemp_name = $staffResult[0]->NAME;
                        $emp->aemp_onme = $staffResult[0]->NAME;
                        $emp->aemp_stnm = $staffResult[0]->NAME;
                        $emp->aemp_mob1 = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_dtsm = $staffResult[0]->CONTACTNO != "" ? $staffResult[0]->CONTACTNO : "";
                        $emp->aemp_emal = $staffResult[0]->EMAIL != "" ? $staffResult[0]->EMAIL : "";
                        $emp->aemp_lued = $user->id;
                        $emp->edsg_id = 1;
                        $emp->role_id = 1;
                        $emp->aemp_utkn = md5(uniqid(rand(), true));
                        $emp->cont_id = $this->currentUser->country()->id;;
                        $emp->aemp_iusr = 1;
                        $emp->aemp_eusr = 1;
                        $emp->aemp_mngr = 1;
                        $emp->aemp_lmid = 1;
                        $emp->aemp_aldt = 0;
                        $emp->aemp_lcin = '50';
                        $emp->aemp_otml = 2;
                        $emp->aemp_lonl = 0;
                        $emp->aemp_usnm = trim($staffResult[0]->ID);
                        $emp->aemp_pimg = '';
                        $emp->aemp_picn = '';
                        $emp->aemp_emcc = '';
                        $emp->aemp_crdt = 0;
                        $emp->aemp_issl = 0;
                        $emp->site_id = 0;
                        $emp->lfcl_id = 1;
                        $emp->amng_id = 1;
                        $emp->save();
                        $user->cont_id = $this->currentUser->country()->id;
                        $user->save();
                        DB::connection($this->db)->commit();
                        return redirect('employee/' . $emp->id . "/edit")->with('success', 'successfully Created');
                    } catch (Exception $e) {
                        DB::connection($this->db)->rollback();
                        throw $e;
                        // return redirect()->back()->with('danger', 'Not Created');
                    }
                } else {
                    return back()->withInput()->with('danger', 'User Not found on HRIS');
                }
            } else {
                $user = User::where('email', '=', $request->aemp_usnm)->first();
                if ($user == null) {
                    $user = User::create([
                        'name' => $request->aemp_usnm,
                        'email' => trim($request->aemp_usnm),
                        'password' => bcrypt(trim($request->aemp_usnm)),
                        'remember_token' => md5(uniqid(rand(), true)),
                        'lfcl_id' => 1,
                        'cont_id' => $this->currentUser->country()->id,
                    ]);
                }
                $user->cont_id = $this->currentUser->country()->id;
                $user->save();
                return back()->withInput()->with('danger', 'Already Exist '.$this->currentUser->country()->cont_name);
            }
        }
        //DB::table('users')->whereIn('email',$userId)->update(['remember_token' => '']);
        //DB::connection($this->connection)->table('tm_aemp')->whereIn('aemp_usnm',$userId)->update(['amng_id' => $this->amng_id, 'aemp_eusr' => $this->currentUser->employee()->id]);


    }
}
