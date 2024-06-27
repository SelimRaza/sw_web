<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\BusinessObject\SalesGroup;
use App\BusinessObject\PriceList;
use App\BusinessObject\SalesGroupEmployee;
use App\MasterData\Role;

use Illuminate\Support\Facades\DB;

class EmployeeGroupZonePricelistUpload extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_aemp';
    protected $connection = '';
    private $currentUser;

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['designation_code', 'role_code', 'manager_code', 'line_manager_code', 'user_id', 'full_name', 'short_name',
            'language_name', 'sales_person', 'hris_sync', 'mobile', 'dealer_code', 'distance', 'group_code', 'price_code', 'zone_code'];
    }

    public function user()
    {
        return User::find($this->aemp_lued);
    }

    public function role()
    {
        return Role::on($this->connection)->find($this->edsg_id);
    }

    public function masterRole()
    {
        return MasterRole::on($this->connection)->find($this->role_id);
    }

    public function manager()
    {
        return Employee::on($this->connection)->find($this->aemp_mngr);
    }

    public function lineManager()
    {
        return Employee::on($this->connection)->find($this->aemp_lmid);
    }

    public function country()
    {
        return Country::find($this->cont_id);
    }

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        //dd($array);
        //$len = sizeof($array);
        //$this->db = Auth::user()->country()->cont_conn;
        $count = 0;
        //dd($len);
        foreach ($array as $row) {
            //dd($row);
            $value = (object)$row;
            //dd($value);

            $designation = Role::on($this->connection)->where(['edsg_code' => $value->designation_code])->first();
            //dd($designation);
            $masterRole = MasterRole::on($this->connection)->where(['role_code' => $value->role_code])->first();
            //dd($masterRole);
            $ests = Employee::on($this->connection)->where(['aemp_usnm' => $value->user_id])->first();
            $manager = Employee::on($this->connection)->where(['aemp_usnm' => $value->manager_code])->first();
            $lManager = Employee::on($this->connection)->where(['aemp_usnm' => $value->line_manager_code])->first();

            $dlr = Depot:: on($this->connection)->where(['dlrm_code' => $value->dealer_code])->first();
            $slgp = SalesGroup::on($this->connection)->where(['slgp_code' => $value->group_code])->first();
            $zone = Zone::on($this->connection)->where(['zone_code' => $value->zone_code])->first();
            $plmt = PriceList::on($this->connection)->where(['plmt_code' => $value->price_code])->first();

            // dd($masterRole);
            if ($dlr != null){
                $dlrId = $dlr->id;
            }else{
                $dlrId = 0;
            }


            if ($ests != null) {
                //dd($ests);
                $emId = $ests->id;

            } else {
                $emId = 0;

            }


            if ($manager != null && $lManager != null) {
                $manag = $manager->id;
                $lmanag = $lManager->id;
            } else {
                $manag = '1';
                $lmanag = '1';

            }
            if ($emId > 0) {
                //dd($ests);
                $count++;
                $id = $ests->id;
                $emp = Employee::on($this->connection)->findorfail($id);
                $emp->aemp_name = $value->full_name == "" ? '' : $value->full_name;
                $emp->aemp_stnm = $value->short_name == "" ? '' : $value->short_name;
                $emp->aemp_onme = $value->language_name == "" ? '' : $value->language_name;
                $emp->aemp_mob1 = $value->mobile == "" ? '' : $value->mobile;

                $emp->edsg_id = $designation->id;
                $emp->role_id = $masterRole->id;

                $emp->aemp_mngr = $manag;
                $emp->aemp_lmid = $lmanag;
                $emp->aemp_aldt = $value->distance;

                $emp->aemp_issl = $value->sales_person == "1" ? 1 : 0;
                $emp->aemp_asyn = $value->hris_sync == "1" ? 'Y' : 'N';
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                //dd($emp);
                $emp->save();


            } else {
                //dd($count);
                // dd($emId);
                // echo "sdfaf";
                $user = User::create([
                    'name' => $value->full_name == "" ? '.' : $value->full_name,
                    'email' => trim($value->user_name),
                    'password' => bcrypt(trim($value->user_name)),
                    'remember_token' => md5(uniqid(rand(), true)),
                    'lfcl_id' => 1,
                    'cont_id' => $this->currentUser->cont_id,
                ]);
                //dd($designation->id);
                $emp = new Employee();
                $emp->setConnection($this->connection);
                $emp->aemp_name = $value->full_name == "" ? '' : $value->full_name;
                $emp->aemp_stnm = $value->short_name == "" ? '' : $value->short_name;
                $emp->aemp_onme = $value->language_name == "" ? '' : $value->language_name;
                $emp->aemp_mob1 = $value->mobile == "" ? '' : $value->mobile;
                $emp->aemp_dtsm = $value->mobile == "" ? '' : $value->mobile;
                $emp->aemp_asyn = $value->hris_sync == "1" ? 'Y' : 'N';
                $emp->aemp_emal = '';
                $emp->aemp_lued = $user->id;
                $emp->edsg_id = $designation->id;
                $emp->role_id = $masterRole->id;
                $emp->aemp_utkn = md5(uniqid(rand(), true));
                $emp->cont_id = $this->currentUser->cont_id;
                $emp->aemp_iusr = $this->currentUser->employee()->id;
                $emp->aemp_eusr = $this->currentUser->employee()->id;
                $emp->aemp_mngr = $manag;
                $emp->aemp_lmid = $lmanag;
                $emp->aemp_aldt = 0;
                $emp->aemp_lcin = '99';
                $emp->aemp_otml = 2;
                $emp->aemp_lonl = 2;
                $emp->aemp_usnm = trim($value->user_name);
                $emp->aemp_pimg = '';
                $emp->aemp_picn = '';
                $emp->aemp_emcc = '';
                $emp->site_id = 0;
                $emp->aemp_crdt = 0;
                $emp->aemp_issl = 1;
                $emp->lfcl_id = 1;
                $emp->save();

            }
            if ($emId > 0 && $dlrId){

                DB::connection($this->connection)->table('tl_srdi')->insertOrIgnore([
                    'aemp_id' => $ests->id,
                    'acmp_id' => 1,
                    'dlrm_id' => $dlrId,
                    'cont_id' => $this->currentUser->country()->id,
                    'lfcl_id' => 1,
                    'aemp_iusr' => $this->currentUser->employee()->id,
                    'aemp_eusr' => $this->currentUser->employee()->id,
                    'var' => 1,
                    'attr1' => '',
                    'attr2' => '',
                    'attr3' => 0,
                    'attr4' => 0,

                ]);
            }
            if ($emId >0 && $slgp != null && $plmt != null && $zone != null) {
                // $salesGroupEmp = SalesGroupEmployee::on($this->connection)->where(['slgp_id' => $slgp->id, 'aemp_id' => $employee->id,'plmt_id'=>$plmt->id])->first();
                $salesGroupEmp = SalesGroupEmployee::on($this->connection)->where(['aemp_id' => $emId])->first();
                if ($salesGroupEmp == null) {
                    $salesGroupEmp = new SalesGroupEmployee();
                    $salesGroupEmp->setConnection($this->connection);
                    $salesGroupEmp->aemp_id = $emId;
                    $salesGroupEmp->slgp_id = $slgp->id;
                    $salesGroupEmp->plmt_id = $plmt->id;
                    $salesGroupEmp->zone_id = $zone->id;
                    $salesGroupEmp->cont_id = $this->currentUser->employee()->cont_id;
                    $salesGroupEmp->lfcl_id = 1;
                    $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->save();
                }else{
                    $salesGroupEmp->setConnection($this->connection);
                    $salesGroupEmp->aemp_id = $emId;
                    $salesGroupEmp->slgp_id = $slgp->id;
                    $salesGroupEmp->plmt_id = $plmt->id;
                    $salesGroupEmp->zone_id = $zone->id;
                    $salesGroupEmp->cont_id = $this->currentUser->employee()->cont_id;
                    $salesGroupEmp->lfcl_id = 1;
                    $salesGroupEmp->aemp_iusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->aemp_eusr = $this->currentUser->employee()->id;
                    $salesGroupEmp->save();
                }
            }
        }

    }
}

/*{
    protected $table = 'tm_aemp';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return ['dealer_name', 'dealer_code'];
    }

    public function model(array $row)
    {
        $this->db = Auth::user()->country()->cont_conn;
        $value = (object)$row;
        $dlrid = $value->dealer_code;
        //$dlr = DB::connection($this->connection)->select("SELECT `id` FROM `tm_dlrm` WHERE `dlrm_code`='$dlrid'");
        //dd($dlr);
        $dlr = Depot:: on($this->db)->where(['dlrm_code' => $dlrid])->first();
        //$slgp = SalesGroup::on($this->connection)->where(['slgp_code' => $value->sales_group_code])->first();
        //$thana = Thana::on($this->connection)->where(['than_code' => $value->than_id])->first();
        //$Base = Base::on($this->connection)->where(['base_code' => $value->base_code])->first();

        if ($dlr != null) {

            $name = $dlr->dlrm_name;

            $code = $dlr->dlrm_code;
            $dlrm_adrs = $dlr->dlrm_adrs;
            $idddfdfd = $dlr->id;
            $group_id = $dlr->slgp_id;
            $cont_id = $dlr->cont_id;
            $hashed = Hash::make($code);

            if ($cont_id == '2') {
                $sql = "INSERT INTO dist.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', 'myprg_pran', '1','1', '$cont_id')";
            }
            if ($cont_id == '5') {
                $sql = "INSERT INTO dist_rfl.`users`(`name`, `email`, `dlrm_ads`, `l_id`, `password`, `cont_conn`, `printer_type`, `lfcl_id`, `cont_id`)
values ('$name', '$code', '$dlrm_adrs', '$idddfdfd', '$hashed', 'myprg_rfl', '1','1', '$cont_id')";
            }


            DB::connection($this->db)->select($sql);

            $depot = Depot::on($this->db)->findorfail($idddfdfd);
            $depot->dlrm_akey = 'Y';

            $depot->save();

        }
    }

    public function region()
    {
        return Region::on($this->connection)->find($this->dirg_id);
    }
}*/
