<?php

namespace App\DataExport;

use App\BusinessObject\SpaceSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SiteMappingWithSpace extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tl_spst';
    private $currentUser;
    protected $db = '';
    protected $cont_id='';
    protected $aemp_id='';


    public function __construct()
    {
        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->db = Auth::user()->country()->cont_conn;
            $this->cont_id = Auth::user()->country()->id;
            $this->aemp_id = Auth::user()->employee()->id;
        }
    }

    public function headings(): array
    {
        return [
            'space_code',
            'site_code',
            'status',
        ];
    }

    public function model(array $value)
    {
        $request=(object)$value;


        try {
            $space_id = $this->getSpaceId($request->space_code);
            $site_id = $this->getSiteId($request->site_code);
            $exist = SpaceSite::on($this->db)->where(['site_id' => $site_id,'spcm_id' => $space_id])->first();

            if (!$exist) {
                $siteMapping = new SpaceSite();
                $siteMapping->setConnection($this->db);
                $siteMapping->spcm_id = $space_id;
                $siteMapping->site_id = $site_id;
                $siteMapping->cont_id = Auth::user()->employee()->cont_id;
                $siteMapping->lfcl_id = 12;
                $siteMapping->aemp_iusr = Auth::user()->employee()->id;
                $siteMapping->aemp_eusr = Auth::user()->employee()->id;
                $siteMapping->save();
            }else{
                $exist->update([
                    'lfcl_id' => $request->status,
                    'aemp_eusr' => Auth::user()->employee()->id
                ]);
            }
        }catch(\Exception $e)
        {
            return;
        }
    }



    public function getSpaceId($space_code)
    {
        return DB::connection($this->db)->table('tm_spcm')->where('spcm_code', $space_code)->value('id');
    }

    public function getSiteId($site_code)
    {
        return DB::connection($this->db)->table('tm_site')->where('site_code', $site_code)->value('id');
    }

}
