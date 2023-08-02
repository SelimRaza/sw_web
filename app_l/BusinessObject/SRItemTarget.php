<?php

namespace App\BusinessObject;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SRItemTarget extends Model implements FromCollection, WithHeadings, ToArray
{
    protected $table = 'tbld_employee';
    private $currentUser;
    private $line_manager_id1;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    public static function create($line_manager_id)
    {
        $instance = new self();
        $instance->line_manager_id1 = $line_manager_id;
        return $instance;
    }

    public function collection()
    {
        return collect([
            [
                'category' => 1,
                'sub_category' => 1,
                'price_list' => 1,
                'ctn_price' => 1,
                'sku_id' => 1,
                'sku_name' => 1,
                'ctn_size' => 1,
                'primary_unit'=>1,
                'secondary_unit'=>1,
                'year'=>1,
                'month'=>1,
                'supervisor_id'=>1,
            ],

        ]);
    }

    public function headings(): array
    {
        return ['category', 'sub_category', 'price_list', 'ctn_price', 'sku_id', 'sku_name', 'ctn_size', 'primary_unit', 'secondary_unit', 'year', 'supervisor_id'];
    }


    public function employee()
    {
        return Employee::find($this->emp_id);
    }


    /**
     * @param array $array
     */
    public function array(array $array)
    {
        // TODO: Implement array() method.
    }
}
