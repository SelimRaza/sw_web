<?php

namespace App\DataExport;

use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Maatwebsite\Excel\Concerns\WithTitle;


class ExportChart implements
    WithTitle, WithCharts
{
    private $info;
    private $length;
    private $legend;
    private $layout;
    private $sale_groups;
    private $bottomColumn;

    public function __construct($info)
    {
        $this->info = $info;
        $this->length = count($this->info);

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
        $this->legend = new Legend();
        $this->legend->getLayout();

        $this->layout = new Layout();
        $this->layout->setShowVal(true);

        $this->sale_groups = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$A$2:$A$'.$this->length, null, 1)];

        $chart  = $this->ExpenseChart();


        $this->bottomColumn = $this->indexToXlxsColumn($this->length);

        $expenseBottomLength = $this->length;
        $chart->setTopLeftPosition('A1');
        $chart->setBottomRightPosition("{$this->bottomColumn}{$expenseBottomLength}");




        $chart2  = $this->SalesGroupWiseOutletChart();
        [$salesGroupWiseOutletStart, $salesGroupWiseOutletEnd] = $this->ChartPostion($this->length);
        $chart2->setTopLeftPosition("A{$salesGroupWiseOutletStart}");
        $chart2->setBottomRightPosition("{$this->bottomColumn}{$salesGroupWiseOutletEnd}");





        $chart3 = $this->ItemCoverageChart();
        [$itemCoverageStart, $itemCoverageEnd] = $this->ChartPostion($salesGroupWiseOutletEnd);
        $chart3->setTopLeftPosition("A{$itemCoverageStart}");
        $chart3->setBottomRightPosition("{$this->bottomColumn}{$itemCoverageEnd}");




        $chart4 = $this->VisitsChart();
        [$visitsStart, $visitsEnd] = $this->ChartPostion($itemCoverageEnd);
        $chart4->setTopLeftPosition("A{$visitsStart}");
        $chart4->setBottomRightPosition("{$this->bottomColumn}{$visitsEnd}");




        $chart5 = $this->MemoChart();
        [$memoStart, $memoEnd] = $this->ChartPostion($visitsEnd);
        $chart5->setTopLeftPosition("A{$memoStart}");
        $chart5->setBottomRightPosition("{$this->bottomColumn}{$memoEnd}");




        $chart6 = $this->LpcChart();
        [$lpcStart, $lpcEnd] = $this->ChartPostion($memoEnd);
        $chart6->setTopLeftPosition("A{$lpcStart}");
        $chart6->setBottomRightPosition("{$this->bottomColumn}{$lpcEnd}");


        $chart7 = $this->ExpensesChart();
        [$expenseStart, $expenseEnd] = $this->ChartPostion($lpcEnd);
        $chart7->setTopLeftPosition("A{$expenseStart}");
        $chart7->setBottomRightPosition("{$this->bottomColumn}{$expenseEnd}");




        return [$chart, $chart2, $chart3, $chart4, $chart5, $chart6, $chart7];
    }

    public function ExpenseChart()
    {
        $labels1 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$B$1', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$C$1', null, 1),
        ];


        $outlets = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$'.$this->length, null),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$C$2:$C$'.$this->length, null),
        ];

        $series = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED,
            range(0, \count($outlets) - 1), $labels1, $this->sale_groups, $outlets);

        $plot = new PlotArea($this->layout, [$series]);

        return new Chart('chart name', new Title('Sales Group Wise Outlet'), $this->legend, $plot);
    }



    public function SalesGroupWiseOutletChart()
    {
        $labels2         = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$C$1', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$D$1', null, 1)
        ];
        $visit_vs_memo  = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$C$2:$C$'.$this->length, null),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$D$2:$D$'.$this->length, null),
        ];
        $series2        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED,
            range(0, \count($visit_vs_memo) - 1), $labels2, $this->sale_groups, $visit_vs_memo);

        $plot2          = new PlotArea($this->layout, [$series2]);
        return new Chart('chart name', new Title('Visit vs Memo'), $this->legend, $plot2);
    }



    public function ItemCoverageChart()
    {
        $labels3         = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$B$1', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$E$1', null, 1)
        ];
        $item_coverages = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$15', null),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$E$2:$E$15', null),
        ];
        $series3        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED,
            range(0, \count($item_coverages) - 1), $labels3, $this->sale_groups, $item_coverages);

        $plot3          = new PlotArea($this->layout, [$series3]);
        return  new Chart('chart name', new Title('Item vs Coverage Items'), $this->legend, $plot3);
    }



    public function VisitsChart()
    {
        $labels4         = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$B$1', null, 1)];
        $visits = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$15', null)];
        $series4        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($visits) - 1), $labels4, $this->sale_groups, $visits);

        $plot4          = new PlotArea($this->layout, [$series4]);
        return new Chart('chart name', new Title('Visits'), $this->legend, $plot4);
    }



    public function MemoChart()
    {
        $labels5         = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$C$1', null, 1)];
        $memos = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$C$2:$C$15', null)];
        $series5        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($memos) - 1), $labels5, $this->sale_groups, $memos);

        $plot5          = new PlotArea($this->layout, [$series5]);
        return new Chart('chart name', new Title('Memo'), $this->legend, $plot5);
    }



    public function LpcChart()
    {
        $labels6         = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$D$1', null, 1)];
        $lpc = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$D$2:$D$15', null)];
        $series6        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($lpc) - 1), $labels6, $this->sale_groups, $lpc);

        $plot6          = new PlotArea($this->layout, [$series6]);
        return new Chart('chart name', new Title('LPC'), $this->legend, $plot6);
    }



    public function ExpensesChart()
    {
        $labels7         = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$B$1', null, 1)];
        $expenses = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$15', null)];
        $series7        = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($expenses) - 1), $labels7, $this->sale_groups, $expenses);

        $plot7          = new PlotArea($this->layout, [$series7]);
        return new Chart('chart name', new Title('Expenses'), $this->legend, $plot7);
    }

    private function indexToXlxsColumn($index)
    {
        $name = '';

        while($index > 0) {
                $mod = ($index - 1) % 26;
                $name = chr(65 + $mod).$name;
                $index = (int)(($index - $mod) / 26);
        }

        return $name;
    }


    private function ChartPostion($previousEnd)
    {
        $start = $previousEnd+2;
        $end = $start+$this->length;

        return [$start, $end];
    }
}
