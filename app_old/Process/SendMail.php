<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Mail;

class SendMail
{
    public function sendMailWithLink($db_name){
            $recipient_list=DB::connection($db_name->cont_conn)->select("SELECT aemp_id,aemp_usnm,aemp_name,aemp_email FROM tbl_report_request WHERE report_status=2 
                    GROUP BY aemp_id,aemp_usnm,aemp_name,aemp_email");
            for($i=0;$i<count($recipient_list);$i++){
                $id=$recipient_list[$i]->aemp_id;
                $data=DB::connection($db_name->cont_conn)->select("SELECT report_name,report_link,aemp_email,aemp_name,aemp_usnm FROM tbl_report_request 
                where aemp_id=$id AND report_status=2 ORDER BY created_at DESC");
                $recipient_info=array('email'=>$recipient_list[$i]->aemp_email,'aemp_name'=>$recipient_list[$i]->aemp_name);
                Mail::send('mail', ['data'=>$data,'info'=>$recipient_info], function($message) use ($data,$recipient_info) {
                    $message->to($recipient_info['email']);              
                    $message->subject('SPRO Requested Report');
                    $message->cc(['sa18@prangroup.com','mis42@mis.prangroup.com']);
                    $message->from('reportbi@prangroup.com');    
                });
                if (Mail::failures()) {
                    // return response showing failed emails
                    Mail::send('failure_mail', function($message) {
                        $message->to('mis42@mis.prangroup.com');              
                        $message->subject('SPRO Requested Report');
                        $message->cc(['hussainmahamud.swe@gmail.com']);
                        $message->from('mis42@mis.prangroup.com');    
                    });
                }else{
                    DB::connection($db_name->cont_conn)->select("UPDATE tbl_report_request SET report_status=3 WHERE aemp_id=$id AND report_status=2");
                    return "Mail Sent";
                }

            }

    }

}

