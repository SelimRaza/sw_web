<?php

namespace App\Http\Controllers\Report;

/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 12/24/2018
 * Time: 6:10 PM
 */
use App\BusinessObject\Department;
use App\BusinessObject\SalesGroup;
use App\MasterData\District;
use App\MasterData\Notification;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    private $access_key = 'tt_noti';
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

    public function notificationList()
    {

        if ($this->userMenu->wsmu_vsbl) {
            $empId = $this->currentUser->employee()->id;
            $country_id = $this->currentUser->employee()->cont_id;
            $notification = DB::connection($this->db)->select("SELECT `id`, `noti_head`, `noti_body`, `noti_date`, `noti_imge`, `lfcl_id` FROM `tt_noti` WHERE 1");

            return view('Notification.notification')->with('permission', $this->userMenu)
                ->with('notification', $notification);
        } else {
            return view('theme.access_limit');
        }

    }

    public function notificationListCreate()
    {

        if ($this->userMenu->wsmu_vsbl) {

            $country_id = $this->currentUser->employee()->cont_id;
            $empId = $this->currentUser->employee()->id;
            $acmp = DB::connection($this->db)->select("SELECT DISTINCT acmp_id as id, acmp_name, acmp_code FROM `user_group_permission` WHERE `aemp_id`='$empId'");
            $zone= DB::connection($this->db)->select("SELECT DISTINCT zone_id  id,zone_code,zone_name FROM `user_area_permission` WHERE `aemp_id`='$empId'");

            $role = DB::connection($this->db)->select("SELECT `id`, `role_name`, `role_code`, `cont_id`, `lfcl_id`  FROM `tm_role` WHERE lfcl_id='1'");



            $notification = DB::connection($this->db)->select("SELECT `id`, `noti_head`, `noti_body`, `noti_date`, `noti_imge`, `lfcl_id` FROM `tt_noti` WHERE 1");

            return view('Notification.create-notification')->with('permission', $this->userMenu)
                ->with('acmp', $acmp)->with('zoneList', $zone)->with('role',$role);
        } else {
            return view('theme.access_limit');
        }

    }

    public function notificationSend(Request $request){
    
        $title =$request->title;
        $bodyMessage =$request->bodyMessage;
        $image =$request->image?$request->image:'';
        $acmp_id =$request->acmp_id;
        $slgp_id =$request->sales_group_id;
        $zone_id =$request->zone_id;
        $role_id =$request->role_id;
        $user_id = $request->user_id != "" ? $request->user_id : "";
        $message=array();

        $q1 = "";
        $q2 = "";
        $q3 = "";
        $q4 = "";
        $count =1;
        $notification = new Notification();
        $notification->setConnection($this->db);
        $notification->noti_head =$title;
        $notification->noti_body =$bodyMessage;
        $notification->noti_date = date("Y-m-d");
        $notification->noti_imge =$image;
        $notification->lfcl_id = "1";
        $notification->aemp_iusr = $this->currentUser->employee()->id;
        $notification->aemp_eusr = $this->currentUser->employee()->id;
        $notification->save();
        $id =$notification->id;

        /*$insert = "INSERT ignore INTO `tt_noti_perm`(`noti_id`, `acmp_id`, `slgp_id`, `role_id`, `dirg_id`, `zone_id`, `dist_id`, `than_id`)
 VALUES ('$id', '$acmp_id', '$slgp_id', '$role_id', '0', '$zone_id', '0', '$user_id')";

        DB::connection($this->db)->select($insert);
*/

        function notify($to,$data,$count){

            //$api_key="AAAAzoiK6_8:APA91bFirlvxwWKs2ZKikSOzUdNqOCm7aGNFMgJoquyxLKMR9CQ4V7vah300i7Jr1SAXMgI_67DiimsTjn1SKMJadlmnkjOilq6s6A-ABL0LvOqG_FwvWHrzeuJWRc5oMkskkKBgpMGF";
            $api_key="AAAAzsoU3ew:APA91bH8T-JQP9unlLTWFsoHHIRaMpXyGnTQ52bUkKB_kBuaLI75WGo4BPIfyawFrzn0Tsj_o9Deg4Qu-cW4ZBIJjC3wcCRqKroGV-BS576LbvE9x1rI1vNuM-8wRNpE9IVv-rx6lLH7";
            $url="https://fcm.googleapis.com/fcm/send";
            $fields=json_encode(array('to'=>$to,'notification'=>$data));

            // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($fields));

            $headers = array();
            $headers[] = 'Authorization: key ='.$api_key;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $count = 0;
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            return $count;
            //global $message;
            //$message = "Message Send Successfully!!!";
           // echo $message;
        }
        if ($user_id!=""){

            $user_id = DB::connection($this->db)->select("SELECT t1.eftm_tokn FROM `fm_eftm` t1 
INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id WHERE t2.aemp_usnm ='$user_id'");
            foreach ($user_id as $data) {
                $fm_token= $data->eftm_tokn;

                $to = $fm_token;
                $data=array(
                    'title'=>$title,
                    'body'=>$bodyMessage,
                    'image' =>$image,
                    'picture' => ''
                );

                $send1 = notify($to,$data, $count);
                if ($send1>0){
                    $message[] = "Message Send Successfully";
                }else{
                    $message[] = "Something went wrong. Please try again!!!";
                }

            }
        }else{
            $q1 = "";
            $q2 = "";
            $q3 = "";
            $q4 = "";
            if ($slgp_id!=""){
                $q1 = " and t1.slgp_id='$slgp_id'";
            }
            if ($zone_id !=""){
                $q2 = " and t1.zone_id='$zone_id'";
            }
            if ($role_id !=""){
                $q3 = " and t1.role_id='$role_id'";
            }
            if ($acmp_id !=""){
                $q4 = " and t2.acmp_id='$acmp_id'";
            }

            $sql = "SELECT t4.eftm_tokn FROM `tm_aemp` t1 INNER JOIN tm_slgp t2 ON t1.slgp_id=t2.id 
INNER JOIN tm_acmp t3 ON t2.acmp_id=t3.id INNER JOIN fm_eftm t4 ON t1.id=t4.aemp_id 
WHERE t1.lfcl_id='1' ". $q1 . $q2 . $q3 . $q4;
            //echo $sql;
$user_id=DB::connection($this->db)->select($sql);
            foreach ($user_id as $data) {

                $fm_token= $data->eftm_tokn;

                $to = $fm_token;
                $data=array(
                    'title'=>$title,
                    'body'=>$bodyMessage,
                    'image' => '',
                    'picture' => ''
                );

                $send1 = notify($to,$data, $count);

            }
            if ($send1>0){
                $message[] = "Message Send Successfully";
            }else{
                $message[] = "Something went wrong. Please try again!!!";
            }

        }
        //echo $message;
        return $message;

        /*$role = DB::connection($this->db)->select("SELECT t1.eftm_tokn FROM `fm_eftm` t1 INNER JOIN tm_aemp t2 ON t1.aemp_id = t2.id WHERE t2.aemp_usnm IN ('165266','60919')");
        foreach ($role as $data) {
            $fm_token= $data->eftm_tokn;

            $to = $fm_token;
            $data=array(
                'title'=>'Greetings',
                'body'=>'Test Message From Kabir',
                'image' => 'https://gintonico.com/content/uploads/2015/03/fontenova.jpg',
                'picture' => 'https://gintonico.com/content/uploads/2015/03/fontenova.jpg'
            );

            notify($to,$data);

        }*/
        //$fm_token = $role[0]->eftm_tokn;
       // echo $fm_token;
        //dd($role);
//$to="dcPyTVQ1SfWVa6evusYRN6:APA91bHIX9Mi9rHax-ViVsoHg3_G-POcAkJXoXt0shA08aGveCI6Nw4CKghT4x-lON1wpurM5LnDpdNNXnA_mDEyjRlQoSUzZdp8vMU1vAeWjubgq3PyM75WDwBoNwhCe635RWz49Wq6";
        /*$to= array("faPct10YTp246oyVuRj0jK:APA91bHU9u-P93TORpkxVPJZDmnntpqseVxFDBOYg7O3BjMOXzeE-A6ZXMgRYIiFeumvsncExtRAC7dW0LgJiFPEXGZUsei2h-mTvrIn4YwOlj6ZE8_Nwr2RYyv7G2jUR0rqVwgx4HiK",
            "fatzm4d-T1u1VzG_ICxOKA:APA91bEXErsJIdnnJiINZXklk2MqUzN5pYNmyn6gxPgtTEQWWhLruGfycO7NlJ5wX883uK8eAydbcbACk0G7A1aTCFkn_fRtKly6drsifyMZ6Hyl21UHykeEiEzxp7-Wlwq-UEOw1nqK");*/


        //echo "Notification Sent";

    }

    public function getGroup(Request $request){
        $acmp_id = $request->slgp_id;
        $empId = $this->currentUser->employee()->id;
        return $companies = DB::connection($this->db)->select("SELECT DISTINCT `slgp_id` AS id,`slgp_code`,`slgp_name` FROM `user_group_permission` WHERE `aemp_id`='$empId' and acmp_id='$acmp_id'");
    }

}