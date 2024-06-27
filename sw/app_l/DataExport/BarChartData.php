<?php

namespace App\DataExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;


class BarChartData implements FromCollection, WithTitle
{
    private $info;

    public function __construct($info)
    {
        $this->info = $info;
    }


    public function collection()
    {
        return collect($this->info);
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data';
    }

}
