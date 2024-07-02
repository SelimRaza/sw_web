<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class RSMP extends Model implements WithHeadings, ToArray, WithHeadingRow
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
        $i = 1;
        if($this->cont_id  == 2 || $this->cont_id == 5){
            foreach ($data as $item) {
                $site = Site::on($this->db)->where(['site_code' => $item['site_code']])->first();
                $rout = Route::on($this->db)->where(['rout_code' => $item['rout_code']])->first();
                if ($site != null) {
                    // $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $rout->id, 'site_id' => $site->id])->first();
                    // if ($routeSite1 == null) {
                        $insert[] = [
                            'site_id' => $site->id,
                            'rout_id' => $rout->id,
                            'rspm_serl' => 1,
                            'cont_id' => $this->currentUser->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => $this->currentUser->employee()->id,
                            'aemp_eusr' => $this->currentUser->employee()->id,
                            'var' => 1,
                            'attr1' => '',
                            'attr2' => '',
                            'attr3' => 0,
                            'attr4' => 0,
                        ];
                    // }
                }
                if (count($insert) >= 100) {
                    DB::connection($this->db)->table('tl_rsmp')->insertOrIgnore($insert);
                    $insert = [];
                }
            }
            if (!empty($insert)) {
                DB::connection($this->db)->table('tl_rsmp')->insertOrIgnore($insert);
            }
        }else {
             foreach ($data as $item) {
                $site = Site::on($this->db)->where(['site_code' => $item['site_code']])->first();
                $rout = Route::on($this->db)->where(['rout_code' => $item['rout_code']])->first();
                if ($site != null) {
                    $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $rout->id, 'site_id' => $site->id])->first();
                    if ($routeSite1 == null) {
                        $insert[] = [
                            'site_id' => $site->id,
                            'rout_id' => $rout->id,
                            'rspm_serl' => 1,
                            'cont_id' => $this->currentUser->employee()->cont_id,
                            'lfcl_id' => 1,
                            'aemp_iusr' => $this->currentUser->employee()->id,
                            'aemp_eusr' => $this->currentUser->employee()->id,
                            'var' => 1,
                            'attr1' => '',
                            'attr2' => '',
                            'attr3' => 0,
                            'attr4' => 0,
                        ];
                    }
                }
                
                if (count($insert) >= 100) {
                    DB::connection($this->db)->table('tl_rsmp')->insertOrIgnore($insert);
                    $insert = [];
                }
            }
            if (!empty($insert)) {
                DB::connection($this->db)->table('tl_rsmp')->insertOrIgnore($insert);
            }
        }
            
    }

    // public function model(array $value)
    // {
    //     $value = (object)$value;
    //     dd('model ', $value);
    //     $site = Site::on($this->db)->where(['site_code' => $value->site_code])->first();
    //     $rout = Route::on($this->db)->where(['rout_code' => $value->rout_code])->first();
    //     if ($site != null) {
    //         $routeSite1 = RouteSite::on($this->db)->where(['rout_id' => $rout->id, 'site_id' => $site->id])->first();
    //         if ($routeSite1 == null) {
    //             // $routeSite = new RouteSite();
    //             // $routeSite->setConnection($this->connection);
    //             // $routeSite->site_id = $site->id;
    //             // $routeSite->rout_id =$rout->id;
    //             // $routeSite->rspm_serl = 1;
    //             // $routeSite->cont_id = $this->currentUser->employee()->cont_id;
    //             // $routeSite->lfcl_id = 1;
    //             // $routeSite->aemp_iusr = $this->currentUser->employee()->id;
    //             // $routeSite->aemp_eusr = $this->currentUser->employee()->id;
    //             // $routeSite->var = 1;
    //             // $routeSite->attr1 = '';
    //             // $routeSite->attr2 = '';
    //             // $routeSite->attr3 = 0;
    //             // $routeSite->attr4 = 0;
    //             // $routeSite->save();
    //             $insert[] = [
    //                 'site_id' => $site->id,
    //                 'rout_id' =>$rout->id,
    //                 'rspm_serl' => 1,
    //                 'cont_id' =>$this->currentUser->employee()->cont_id,
    //                 'lfcl_id' =>1,
    //                 'aemp_iusr' =>$this->currentUser->employee()->id,
    //                 'aemp_eusr' =>$this->currentUser->employee()->id,
    //                 'var' => 1,
    //                 'attr1' => '',
    //                 'attr2' =>'',
    //                 'attr3' =>0,
    //                 'attr4' => 0,
    //             ];
    //         }
    //     }
    //     if (!empty($insert)) {
    //         dd($insert);
    //         foreach (array_chunk($insert,100) as $t)  
    //             {
    //                 DB::connection($this->db)->table('tl_rsmp')->insert($t);
    //             }
            
    //     }
       
       
    // }
}
