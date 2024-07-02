<?php
namespace App\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MasterData\Country;
use Illuminate\Support\Facades\Storage;
class ReportRequest
{
    public function updateReportStatus($db_name){
        DB::connection($db_name->cont_conn)->select("UPDATE tbl_report_request SET report_status=1 WHERE report_status=10 AND date(created_at)=curdate()");
    }

}

