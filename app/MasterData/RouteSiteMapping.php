<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class RouteSiteMapping extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tl_rsmp';
    private $currentUser;
    private $db;
    private $cont_id;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
            $this->db=Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
        }
    }

    public function headings(): array
    {
        return [
            'rout_code',
            'site_code',
        ];
    }

    public function array(array $array)
    {
        
        $data = $array;
        $insert = [];
        // dd($data);

        // Truncate the table
        DB::connection($this->db)->statement('TRUNCATE TABLE test_update_1');

        if(count($data) > 0){
            foreach ($data as $item) {
                // dd($item['rout_code']);
                $insert[] = [
                    'site_code' => $item['site_code'],
                    'route_code' => $item['rout_code'],
                ];
        
                if (count($insert) >= 100) {
                    DB::connection($this->db)->table('test_update_1')->insertOrIgnore($insert);
                    $insert = [];
                }

            }
            if (!empty($insert)) {
                DB::connection($this->db)->table('test_update_1')->insertOrIgnore($insert);
            }

            $total = DB::connection($this->db)->select("select count(id) as total_data from test_update_1");
            if (!empty($total) && $total[0]->total_data > 0) {
                $query = 'INSERT IGNORE INTO `tl_rsmp`(`rout_id`, `site_id`, `rspm_serl`, `cont_id`, `lfcl_id`, `aemp_iusr`, `aemp_eusr`) 
                    SELECT t3.id as rout_id,t2.id as site_id,1 as rspm_serl,2 as cont_id,1 as lfcl_id,1 as aemp_iusr,1 as aemp_eusr 
                    FROM `test_update_1`t1 
                    JOIN tm_site t2 ON t1.`site_code`=t2.site_code
                    JOIN tm_rout t3 ON t1.`route_code`=t3.rout_code
                    WHERE 1';

                // DB::connection($this->db)->statement($query);
                
                try {
                    DB::connection($this->db)->statement($query);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }

            }


        }

        
            
    }

    
}
