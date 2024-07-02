<?php

namespace App\Process;

use App\MasterData\MasterRole;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class NotifyUser
{
    public function notify($db_conn, $datetime, $endTime)
    {
        $query="SELECT
  t1.note_body,
  t2.eftm_tokn,
  t1.note_tokn,
  t1.id,
  group_concat('https://images.sihirbox.com/',t3.nimg_imag) AS nimg_imag
FROM tt_note AS t1
  INNER JOIN fm_eftm AS t2 ON t1.aemp_id = t2.aemp_id
  LEFT JOIN tl_nimg AS t3 ON t1.id = t3.note_id
WHERE t1.note_rtim BETWEEN '$datetime' and '$endTime' and t1.lfcl_id=1
GROUP BY t1.note_body, t2.eftm_tokn, t1.note_tokn, t1.id";
        //dd($query);
        $result = DB::connection($db_conn)->select($query);
//BETWEEN '$datetime' and '$endTime'

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . 'AAAAzsoU3ew:APA91bH8T-JQP9unlLTWFsoHHIRaMpXyGnTQ52bUkKB_kBuaLI75WGo4BPIfyawFrzn0Tsj_o9Deg4Qu-cW4ZBIJjC3wcCRqKroGV-BS576LbvE9x1rI1vNuM-8wRNpE9IVv-rx6lLH7',
            'Content-Type: application/json'
        );
       // dd($result);
       // dd(array_column($result, 'id'));
        foreach ($result as $data) {
            $fields = array(
                'to' => "$data->eftm_tokn",
                'data' => array(
                    "data" => array(
                        "code" => $data->note_tokn,
                        "body" => $data->note_body,
                        "title" => 'Note Reminder',
                        "image" => $data->nimg_imag,
                    )
                )
            );
           // dd($fields);
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
         //   echo $result;
            DB::connection($db_conn)->table('tt_note')->where('id',$data->id )->update(['lfcl_id' => 2]);
            curl_close($ch);
        }
    }


}