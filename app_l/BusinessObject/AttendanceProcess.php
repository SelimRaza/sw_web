<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AttendanceProcess extends Model
{
    protected $table = 'tblt_attendance_process';

    function processCheck($checkTime, $emp_id, $policy_id)
    {
        $status = 1;
        $hrPolicy = HRPolicy::where(['id' => $policy_id])->first();
        $hrAttendance = DB::table('tblt_attendance')
            ->select(DB::raw('min(date_time) AS start_time'), DB::raw('max(date_time) AS end_time'))
            ->where(['emp_id' => $emp_id, 'date' => date('Y-m-d', strtotime($checkTime))])
            ->groupBy('emp_id')->first();

        $now1 = date("H:i:s", strtotime($hrAttendance->start_time));
        if (date("H:i:s", strtotime($hrPolicy->start_time)) <= $now1 && $now1 <= date("H:i:s", strtotime($hrPolicy->end_time))) {
            if (((strtotime(date("H:i:s", strtotime($hrAttendance->start_time))) - strtotime($hrPolicy->start_time)) / 60) < 30) {
                $status = 3;
            }
        }
        $now2 = date("H:i:s", strtotime($hrAttendance->end_time));
        if (date("H:i:s", strtotime($hrPolicy->start_time)) <= $now2 && $now2 <= date("H:i:s", strtotime($hrPolicy->end_time))) {
            $status = 1;
        }
        if (date("H:i:s", strtotime($hrPolicy->start_time)) >= $now1 && date("H:i:s", strtotime($hrPolicy->end_time)) <= $now2) {
            $status = 2;
        }
        return $status;

    }

    function getLateMin($checkTime, $emp_id, $policy_id)
    {
        $hrPolicy = HRPolicy::where(['id' => $policy_id])->first();
        $hrAttendance = DB::table('tblt_attendance')
            ->select(DB::raw('min(date_time) AS start_time'), DB::raw('max(date_time) AS end_time'))
            ->where(['emp_id' => $emp_id, 'date' => date('Y-m-d', strtotime($checkTime))])
            ->groupBy('emp_id')->first();
        return (strtotime($hrAttendance->start_time) - strtotime($hrPolicy->start_time)) / 60;

    }
}
