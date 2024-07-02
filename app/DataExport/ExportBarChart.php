<?php

namespace App\DataExport;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;


class ExportBarChart implements WithTitle
//    , WithCharts
    , WithMultipleSheets
{
    private $infos;

    public function __construct()
    {
        $this->infos = [
            ['Sale Group', 'Visit', 'Memo', 'Expense', 'Item'],
            ['Q1', 12, 15, 21, 21],
            ['Q2', 12, 14, 23, 23],
            ['Q3', 23, 25, 12, 12],
            ['Q4', 30, 32, 10, 10],
            ['Q5', 30, 32, 10, 10],
            ['Q6', 30, 32, 10, 10],
            ['Q7', 30, 32, 10, 10],
            ['Q8', 30, 32, 10, 10],
            ['Q9', 30, 32, 10, 10],
            ['Q10', 30, 32, 10, 10],
            ['Q11', 30, 32, 10, 10],
            ['Q12', 30, 32, 10, 10],
            ['Q13', 30, 32, 10, 10],
            ['Q14', 30, 40, 10, 10],
            ['Q15', 30, 32, 10, 10],
            ['Q16', 30, 32, 10, 10],
            ['Q17', 30, 32, 10, 10],
            ['Q18', 30, 32, 10, 10],
            ['Q19', 30, 32, 10, 10],
            ['Q20', 30, 32, 10, 10]
        ];
    }


    public function sheets(): array
    {
        $sheets = [
            new ExportChart($this->infos),
            new BarChartData($this->infos),
        ];

        return $sheets;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Charts';
    }
}
