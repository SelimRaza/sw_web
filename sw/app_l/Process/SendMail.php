<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Mail;
use Illuminate\Support\Facades\Storage;
class SendMail
{
    public function sendMailWithLink($db_name){
        $txt='';
        $txt=$db_name->cont_conn.'-'. date('Y-m-d h:i:s').'\n';   
        Storage::append('hello.txt',$txt);
            $recipient_list=DB::connection($db_name->cont_conn)->select("SELECT aemp_id,aemp_usnm,aemp_name,aemp_email FROM tbl_report_request 
                            WHERE report_status=2  and aemp_email !='' AND date(created_at)>=curdate()-INTERVAL 2 Day 
                            GROUP BY aemp_id,aemp_usnm,aemp_name,aemp_email");
            Storage::append('hello.txt',count($recipient_list));
            for($i=0;$i<count($recipient_list);$i++){
                try{
                    $ind_log=$recipient_list[$i]->aemp_id.'--'.$recipient_list[$i]->aemp_email.'--'.date('Y-m-d h:i:s');
                    Storage::append('hello.txt',$ind_log);
                    $id=$recipient_list[$i]->aemp_id;
                    $data=DB::connection($db_name->cont_conn)->select("SELECT report_name,report_link,aemp_email,aemp_name,aemp_usnm FROM tbl_report_request 
                    where aemp_id=$id AND report_status=2 ORDER BY created_at DESC ");
                    $recipient_info=array('email'=>$recipient_list[$i]->aemp_email,'aemp_name'=>$recipient_list[$i]->aemp_name);
                    Mail::send('mail', ['data'=>$data,'info'=>$recipient_info], function($message) use ($data,$recipient_info) {
                         $message->to($recipient_info['email']);              
                        //$message->to(['mis42@mis.prangroup.com']);              
                        $message->subject('SPRO Requested Report');
                        $message->cc(['sa18@prangroup.com']);
                        $message->from('reportbi@prangroup.com');    
                    });
                    
                    if (Mail::failures()) {
                        // Mail::send('failure_mail', function($message) {
                        //     $message->to('mis42@mis.prangroup.com');              
                        //     $message->subject('SPRO Requested Report');
                        //     $message->cc(['hussainmahamud.swe@gmail.com']);
                        //     $message->from('mis42@mis.prangroup.com');    
                        // });
                        Storage::append('hello.txt',"failed");
                    }else{
                        Storage::append('hello.txt',"sent");
                        DB::connection($db_name->cont_conn)->select("UPDATE tbl_report_request SET report_status=3 WHERE aemp_id=$id AND report_status=2");
                    }
                    sleep(12);
                }
                catch(\Exception $e){
                    Storage::append('hello.txt',$e->getMessage());
                }
                

               
            }
            $c_cmplt="Circle Completed for ".$db_name->cont_conn.'|| '.date('Y-m-d');
            Storage::append('hello.txt',$c_cmplt);

    }

    public function sendMailWithLinkTest($db_name){
        $recipient_list=DB::connection($db_name->cont_conn)->select("SELECT aemp_id,aemp_usnm,aemp_name,aemp_email FROM tbl_report_request 
                        WHERE report_status=2  and aemp_email !='' AND date(created_at)>=curdate()-INTERVAL 2 Day
                        GROUP BY aemp_id,aemp_usnm,aemp_name,aemp_email");
        for($i=0;$i<count($recipient_list);$i++){
            $id=$recipient_list[$i]->aemp_id;
            $data=DB::connection($db_name->cont_conn)->select("SELECT report_name,report_link,aemp_email,aemp_name,aemp_usnm FROM tbl_report_request 
            where aemp_id=$id AND report_status=2 ORDER BY created_at DESC ");
            $recipient_info=array('email'=>$recipient_list[$i]->aemp_email,'aemp_name'=>$recipient_list[$i]->aemp_name);
            DB::connection($db_name->cont_conn)->beginTransaction();
            try{
                Mail::send('mail', ['data'=>$data,'info'=>$recipient_info], function($message) use ($data,$recipient_info) {
                    $message->to($recipient_info['email']);              
                    $message->subject('SPRO Requested Report');
                    $message->cc(['sa18@prangroup.com','mis42@mis.prangroup.com']);
                    $message->from('reportbi@prangroup.com');    
                });
                DB::connection($db_name->cont_conn)->select("UPDATE tbl_report_request SET report_status=3 WHERE aemp_id=$id AND report_status=2");
                DB::connection($db_name->cont_conn)->commit();
            }
            catch(\Exception $e){
                Mail::send('failure_mail', function($message) {
                    $message->to('mis42@mis.prangroup.com');              
                    $message->subject('SPRO Requested Report');
                    $message->cc(['hussainmahamud.swe@gmail.com']);
                    $message->from('mis42@mis.prangroup.com');    
                });
                DB::connection($db_name->cont_conn)->rollback();
            }
        }
    }

}

