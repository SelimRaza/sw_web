<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\MasterData\SubCategory;
use App\MasterData\ItemClass;

class UploadSKU extends Model implements WithHeadings, ToArray, WithHeadingRow
{
    protected $table = 'tm_amim';
    private $currentUser;

    protected $connection= '';
    public function __construct()
    {

        if (Auth::user()!=null){
            $this->currentUser = Auth::user();
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }

    public function headings(): array
    {
        return [
            'item_name', 'item_code', 'item_snme', 'factor', 'unit_id', 'price', 'company_id',  'sub_cat_code', 'class_code', 'status'
        ];
    }

    public function array(array $values)
    {
        $row=1;
        try {
            $infos = $values;
            $skus = [];
            for($i=0;$i<count($infos);$i++) {
                $site = SKU::on($this->connection)->where(['amim_code' => $infos[$i]['item_code']])->first();
                $row++;
                if ($site != null) {
                    
                    $site->amim_name = is_null($infos[$i]['item_name']) ? $site->amim_name : $infos[$i]['item_name'];
                    $site->amim_code = is_null($infos[$i]['item_code']) ? $site->amim_code : $infos[$i]['item_code'];
                    $site->amin_snme = is_null($infos[$i]['item_snme']) ? $site->amin_snme : $infos[$i]['item_snme'];
                    $site->amim_runt = is_null($infos[$i]['unit_id']) ? $site->amim_runt : $infos[$i]['unit_id'];
                    $site->amim_dunt = is_null($infos[$i]['unit_id']) ? $site->amim_dunt : $infos[$i]['unit_id'];
                    $site->amim_tppr = is_null($infos[$i]['price']) ? $site->amim_tppr : $infos[$i]['price'];
                    $site->amim_acmp = is_null($infos[$i]['company_id']) ? $site->amim_tppr : $infos[$i]['company_id'];
                    $site->amim_duft = is_null($infos[$i]['factor']) ? $site->amim_duft : $infos[$i]['factor'];
                    $site->amim_dppr = is_null($infos[$i]['price']) ? $site->amim_dppr : $infos[$i]['price'];
                    $site->amim_mrpp = is_null($infos[$i]['price']) ? $site->amim_mrpp : $infos[$i]['price'];
                    $site->itsg_id   = $this->getCategoryId($infos[$i]['sub_cat_code']);
                    $site->itcl_id   = $this->getClassId($infos[$i]['class_code']);

                    $site->lfcl_id   = is_null($infos[$i]['status']) ? $site->lfcl_id : $infos[$i]['status'];
                    $site->aemp_eusr = $this->currentUser->employee()->id;
                    
                    $site->save();
                    
                } else {
                    $skus[] = [
                        'amim_name' => $infos[$i]['item_name'],
                        'amim_code' => $infos[$i]['item_code'],
                        'amin_snme' => $infos[$i]['item_snme'],
                        'amim_runt' => $infos[$i]['unit_id'],
                        'amim_dunt' => $infos[$i]['unit_id'],
                        'amim_tppr' => $infos[$i]['price'],
                        'amim_duft' => $infos[$i]['factor'],
                        'amim_dppr' => $infos[$i]['price'],
                        'amim_mrpp' => $infos[$i]['price'],
                        'amim_imgl' => '',
                        'amim_imic' => '',
                        'amim_tkns' => 0,
                        'amim_colr' => '',
                        'amim_olin' => '',
                        'amim_pexc' => 0,
                        'amim_pvat' => 0,
                        'amim_cbm'  => 0,
                        'amim_issl' => 1,
                        'amim_bcod' => '',
                        'amim_acmp' => $infos[$i]['company_id'],
                        'itsg_id'   => $this->getCategoryId($infos[$i]['sub_cat_code']),
                        'itcl_id'   => $this->getClassId($infos[$i]['class_code']),
                        'lfcl_id'   => 1,
                        'cont_id'   => $this->currentUser->country()->id,
                        'aemp_iusr' => $this->currentUser->employee()->id,
                        'aemp_eusr' => $this->currentUser->employee()->id,
                    ];
                }
            }
            if (!empty($skus)) {
                
                foreach (array_chunk($skus,500) as $sku)
                {
                    DB::connection($this->connection)->table('tm_amim')->insertOrIgnore($sku);
                }
            }
        }catch(\Exception $e)
        {
            // return redirect()->back()->with('danger', $e->getMessage());
        }
    }

    public function getCategoryId($category_code){
        return SubCategory::on($this->connection)->where(['itsg_code' => $category_code])->first()->id;
        
    }

    public function getClassId($class_code){
        return ItemClass::on($this->connection)->where(['itcl_code' => $class_code])->first()->id;
        
    }

}
