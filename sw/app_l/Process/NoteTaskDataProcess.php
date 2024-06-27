<?php

namespace App\Process;

use App\MasterData\Country;
use App\MasterData\MasterRole;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Md Mohammadunnabi
 * Date: 5/10/2020
 * Time: 11:39 AM
 */
class NoteTaskDataProcess
{

public function noteTaskDataInsertUpdateSR($db_conn){
    DB::connection($db_conn)->select("INSERT INTO tbl_note_count
    SELECT
        p.note_date,
        p.aemp_id,
        p.aemp_mngr,
        p.t_note,
        p.created_at,
        p.updated_at
    FROM
        (
        SELECT
            t1.note_date,
            t1.aemp_id,
            t2.aemp_mngr,
            COUNT(t1.id) t_note,
            CURRENT_TIMESTAMP 'created_at',
            CURRENT_TIMESTAMP 'updated_at'
        FROM
            tt_note t1
        INNER JOIN tm_aemp t2 ON
            t1.aemp_id = t2.id
        WHERE
            t1.note_date = CURDATE()
        GROUP BY
            t1.note_date, t1.aemp_id, t2.aemp_mngr) p
        ON DUPLICATE KEY
    UPDATE
        t_note = p.t_note,
        updated_at = p.updated_at;");
    DB::connection($db_conn)->select("INSERT INTO tbl_note_count
    SELECT
        p.note_date,
        p.aemp_id,
        p.aemp_mngr,
        p.t_note,
        p.created_at,
        p.updated_at
    FROM
        (
        SELECT
            t1.note_date,
            t1.aemp_id,
            t2.aemp_mngr,
            COUNT(t1.id) t_note,
            CURRENT_TIMESTAMP 'created_at',
            CURRENT_TIMESTAMP 'updated_at'
        FROM
            tt_note t1
        INNER JOIN tm_aemp t2 ON
            t1.aemp_id = t2.id
        WHERE
            t1.note_date = CURDATE()
        GROUP BY
            t1.note_date, t1.aemp_id, t2.aemp_mngr) p
        ON DUPLICATE KEY
    UPDATE
        t_note = p.t_note,
        updated_at = p.updated_at;");
}

public function noteTaskDataInsertUpdateTSM($db_conn){

}








}