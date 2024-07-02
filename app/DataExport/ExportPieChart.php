<?php

namespace App\DataExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Maatwebsite\Excel\Concerns\WithTitle;


class ExportChart implements FromCollection, WithTitle, WithCharts
{
//
    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            ['', 2010, 2011, 2012],
            ['Q1', 12, 15, 21],
            ['Q2', 56, 73, 86],
            ['Q3', 52, 61, 69],
            ['Q4', 30, 32, 0],
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Charts';
    }

    /**
     * @return Chart|Chart[]
     */
    public function charts()
    {
        $labels     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Charts!$C$1', null)];
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Charts!$A$2:$A$5', null, 4)];
        $values     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Charts!$C$2:$C$5', null)];


        $series = new DataSeries(DataSeries::TYPE_PIECHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($values) - 1), $labels, $categories, $values);
        $plot   = new PlotArea(null, [$series]);

        $legend = new Legend();
        $chart  = new Chart('chart name', new Title('chart title'), $legend, $plot);
        $chart->setTopLeftPosition('F12');
        $chart->setBottomRightPosition('M20');

        return $chart;
    }
}
