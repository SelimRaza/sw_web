@extends('theme.app')
@push('header')
    <script src="{{ asset("theme/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}"></script>
@endpush
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="active">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <strong>Analytical Report</strong>
                        </li>
                        {{--                        <li class="active">--}}
                        {{--                            <strong>Employee Summary</strong>--}}
                        {{--                        </li>--}}
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong></strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong></strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="x_panel">


                        <div class="x_panel">
                            <div class="x_title card-icon" style="float: right">
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </div>

                            <div class="x_content">
                                <form class="form-horizontal form-label-left" action="{{url('/depot/filterDepotddd')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}

                                    {{-- Report Filter--}}
                                    <div class="x_title">
                                        <div class="item form-group">


                                            <div class="col-md-3 col-sm-3 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="start_date" style="text-align: left">Start Date<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg start_date" name="start_date"
                                                           id="start_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-6 ">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="end_date" style="text-align: left">End Date<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg end_date" name="end_date"
                                                           id="end_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>"/>                                            </div>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="name" style="text-align: left">SV ID<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" name="sv_id" class="form-control in_tg" id="sv_id">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-6 ">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="name" style="text-align: left">SR ID<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" name="sr_id" class="form-control in_tg" id="sr_id">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="display: flex; justify-content: end">
                                                <button id="send" type="button" style="margin-right:10px;"
                                                        class="btn btn-success"
                                                        onclick="getEmpSummaryData()"><span class="fa fa-search"
                                                                                            style="color: white;"></span>
                                                    <b>Search</b>
                                                </button>
                                            </div>

                                        </div>


                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
                            {{-- card and report section--}}
                            <div>
                                <div class="row">
                                    <h3 style="display: none; text-align: center" id="staff-info"></h3>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="thumbnail" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-cart-plus"></i> Total Order</span>
                                                    <div class="count" id="ct_ord">0</div>
{{--                                                    <a href="#" class="t_memo">&nbsp;Details</a>--}}
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="thumbnail" id="items-or-notes" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-sticky-note"></i> Items</span>
                                                    <div class="count" id="t_sku">0</div>
                                                    <a href="#" class="t_sku" id="total-item-coverage">&nbsp</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="thumbnail" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-check-circle"></i> Total Visit</span>
                                                    <div class="count" id="ct_visit">0</div>
                                                    <a href="#" class="ctb_visit" id="ctb_visit">

                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="thumbnail" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-map-marker"></i> Total Outlet</span>
                                                    <div class="count" id="t_outlet">0</div>
                                                    <a href="#" id="outlet-coverage">&nbsp</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="thumbnail" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-money"></i> Expense </span>
                                                    <div class="count" id="t_amnt">0</div>
                                                    <a href="#" class="t_sku" id="deli_amnt">&nbsp</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="thumbnail" style="height: 100px">
                                                    <span class="count_top"><i class="fa fa-line-chart"></i> LPC </span>
                                                    <div class="count" id="lpc">0</div>
                                                    <a href="#" class="t_sku">&nbsp</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">&nbsp;</div>
                                    </div>

                                </div>

                            </div>


                        </div>



                        {{-- Supervisor Note Summary Table --}}
                        <div class="col-md-6 col-sm-6 col-xs-12" id="note_summary" style="padding: 0; padding-right: 10px">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Note Summary<small></small></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">Settings 1</a>
                                                <a class="dropdown-item" href="#">Settings 2</a>
                                            </div>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="x_content">

                                        <table class="table font_color" data-page-length="50">
                                            <thead>
                                            <tr class="tbl_header_light">
                                                <th class="cell_left_border">Date</th>
                                                <th>9am</th>
                                                <th>10am</th>
                                                <th>11am</th>
                                                <th>12pm</th>
                                                <th>1pm</th>
                                                <th>2pm</th>
                                                <th>3pm</th>
                                                <th>4pm</th>
                                                <th>5pm</th>
                                                <th>6pm</th>
                                            </tr>
                                            </thead>
                                            <tbody id="note-summary-info">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-sm-6 col-xs-12" id="sv-thana-coverage" style="padding: 0;">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Thana Coverage<small></small></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">Settings 1</a>
                                                <a class="dropdown-item" href="#">Settings 2</a>
                                            </div>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="x_content">

                                        <table class="table font_color" data-page-length="50">
                                            <thead>
                                            <tr class="tbl_header_light">
                                                <th class="cell_left_border">Thana</th>
                                                <th>District</th>
                                                <th>T.Olt</th>
                                            </tr>
                                            </thead>
                                            <tbody id="sv-thana-coverage-info">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hourly Activities line chart --}}
                        <div class="row" id="hourly-activity-line-chart">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2 id="hourly-activities-or-note-sammary">Hourly Activities</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Settings 1</a>
                                                    <a class="dropdown-item" href="#">Settings 2</a>
                                                </div>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div id="visit_vs_productive_non_productive_bar" style="height:350px;"></div>
                                        <div id="sv-activities-bar" style="height:350px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Doughnut Chart OR Attendance Summary Table --}}
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12  ">
                                <div class="x_panel tile overflow_hidden">
                                    <div class="x_title">
                                        <h2>Attendance Summary</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                                   aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">

                                        <table id="attendance-summary-pie-chart" style="width:100%">
                                            <tr>
                                                <th style="width:60%;">
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div id="echart_pie_t" style="height:350px;"></div>
                                                </td>
                                                <td>
                                                    <table class="tile_info">

                                                        <th>
                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                <p class="">Type</p>
                                                            </div>
                                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                                                <p class="">Days</p>
                                                            </div>
                                                        </th>
                                                        <tr>
                                                            <td>
                                                                <p>Total Days</p>
                                                            </td>
                                                            <td id="total-days"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><i class="fa fa-square" style="color:#006A4E"></i>Present</p>
                                                            </td>
                                                            <td id="total-present"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><i class="fa fa-square" style="color:#34495E"></i>IOM</p>
                                                            </td>
                                                            <td id="total-iom"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><i class="fa fa-square" style="color:#BDC3C7"></i>Leave</p>
                                                            </td>
                                                            <td id="total-leave"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p><i class="fa fa-square" style="color:#3498DB"></i>Force Leave</p>
                                                            </td>
                                                            <td id="total-fc_leave"></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>


                                        <table id="attendance-summary-table" class="table font_color" data-page-length="50" style="display: none">
                                            <thead>
                                            <tr class="tbl_header_light">
                                                <th class="cell_left_border">Name</th>
                                                <th>Present</th>
                                                <th>IOM</th>
                                                <th>Leave</th>
                                                <th>Force Leave</th>
                                            </tr>
                                            </thead>
                                            <tbody id="attendance-summary-details">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12  " style="padding: 0; padding-right: 10px">
                                <div class="x_panel">
                                    <div class="x_title">
                                            <h2 id="date-or-sr-wise-details">Date Wise Details</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Settings 1</a>
                                                    <a class="dropdown-item" href="#">Settings 2</a>
                                                </div>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="x_content">

                                        <table class="table font_color" data-page-length="50">
                                            <thead id="employee-wise-details">
                                            <tr class="tbl_header_light">
                                                <th class="cell_left_border">Date</th>
                                                <th>T.Olt</th>
                                                <th>V.Olt</th>
                                                <th>S.Olt</th>
                                                <th>Order</th>
                                                <th>Exp</th>
                                                <th>In Time</th>
                                                <th>Out Time</th>
                                                <th>W.Hour</th>
                                            </tr>
                                            </thead>
                                            <tbody id="date-wise-details">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>




                        {{-- Item Summary && Ward Coverage --}}
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12" id="item-summary">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Item Summary<small></small></h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Settings 1</a>
                                                    <a class="dropdown-item" href="#">Settings 2</a>
                                                </div>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="x_content">

                                            <table class="table font_color" data-page-length="50">
                                                <thead>
                                                <tr class="tbl_header_light">
                                                    <th class="cell_left_border">Item Code</th>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Total Amount</th>
                                                    <th>Delivery Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id="item-summery">
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12" id="sr-ward-coverage">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Ward Coverage<small></small></h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Settings 1</a>
                                                    <a class="dropdown-item" href="#">Settings 2</a>
                                                </div>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="x_content">

                                            <table class="table font_color" data-page-length="50">
                                                <thead>
                                                <tr class="tbl_header_light">
                                                    <th class="cell_left_border">Ward</th>
                                                    <th>Thana</th>
                                                    <th>District</th>
                                                    <th>T.Olt</th>
                                                </tr>
                                                </thead>
                                                <tbody id="govt-hierarchy-coverage">
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset("theme/vendors/Chart.js/dist/Chart.min.js")}}"></script>
    <script src="{{asset("theme/vendors/echarts/dist/echarts.min.js")}}"></script>

    <style type="text/css">
        .thumbnail {
            -webkit-box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
            box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
            transition: 0.3s;
            min-width: 40%;
            border-radius: 5px;
            flex-direction: column;
            display: flex;
            text-align: center;
        }

        .thumbnail-description {
            min-height: 40px;
        }

        .thumbnail:hover {
            cursor: pointer;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 1);
        }

        .count{
            cursor:pointer;
            font-size: 38px;
            font-weight: 600;
        }

        .count_top{
            font-size: 15px;
            font-weight: 100;
        }

        .count_bottom{
            color: #1d68a7;
            margin-top: -5px;
        }

        .summery-nav{
            margin-left: -25px !important;
        }

        #exTab1 .nav-pills > li.active>a{
            margin-left: 0px !important;
        }

        .x_title{
            border-bottom: 0 !important;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
            font-size: smaller !important;
            padding-bottom: 0 !important;
        }

        .tbl_body_gray{
            height: 1rem !important;
        }

        .fa-chevron-up:hover{
            cursor: pointer;
        }
    </style>

    <script type="text/javascript">

        let design = {
            color: [
                '#006A4E', '#34495E', '#BDC3C7', '#3498DB'
            ],

            title: {
                itemGap: 8,
                textStyle: {
                    fontWeight: 'normal',
                    color: '#408829'
                }
            },

            dataRange: {
                color: ['#1f610a', '#97b58d']
            },

            toolbox: {
                color: ['#408829', '#408829', '#408829', '#408829']
            },

            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.5)',
                axisPointer: {
                    type: 'line',
                    lineStyle: {
                        color: '#408829',
                        type: 'dashed'
                    },
                    crossStyle: {
                        color: '#408829'
                    },
                    shadowStyle: {
                        color: 'rgba(200,200,200,0.3)'
                    }
                }
            },

            dataZoom: {
                dataBackgroundColor: '#eee',
                fillerColor: 'rgba(64,136,41,0.2)',
                handleColor: '#408829'
            },
            grid: {
                borderWidth: 0
            },

            categoryAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },

            valueAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },
            timeline: {
                lineStyle: {
                    color: '#408829'
                },
                controlStyle: {
                    normal: {color: '#408829'},
                    emphasis: {color: '#408829'}
                }
            },

            k: {
                itemStyle: {
                    normal: {
                        color: '#68a54a',
                        color0: '#a9cba2',
                        lineStyle: {
                            width: 1,
                            color: '#408829',
                            color0: '#86b379'
                        }
                    }
                }
            },
            map: {
                itemStyle: {
                    normal: {
                        areaStyle: {
                            color: '#ddd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    },
                    emphasis: {
                        areaStyle: {
                            color: '#99d2dd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    }
                }
            },
            force: {
                itemStyle: {
                    normal: {
                        linkStyle: {
                            strokeColor: '#408829'
                        }
                    }
                }
            },
            chord: {
                padding: 4,
                itemStyle: {
                    normal: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    },
                    emphasis: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    }
                }
            },
            gauge: {
                startAngle: 225,
                endAngle: -45,
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: [[0.2, '#86b379'], [0.8, '#68a54a'], [1, '#408829']],
                        width: 8
                    }
                },
                axisTick: {
                    splitNumber: 10,
                    length: 12,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                axisLabel: {
                    textStyle: {
                        color: 'auto'
                    }
                },
                splitLine: {
                    length: 18,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                pointer: {
                    length: '90%',
                    color: 'auto'
                },
                title: {
                    textStyle: {
                        color: '#333'
                    }
                },
                detail: {
                    textStyle: {
                        color: 'auto'
                    }
                }
            },
            textStyle: {
                fontFamily: 'Arial, Verdana, sans-serif'
            }
        };

        let pieData = [];

        function echart(pieData=[]){
            if ($('#echart_pie_t').length ){
                var echartPieCollapse = echarts.init(document.getElementById('echart_pie_t'), design);
                echartPieCollapse.setOption({
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        x: 'center',
                        y: 'bottom',
                        data: ['Force Leave', 'Present', 'IOM', 'Leave']
                    },
                    toolbox: {
                        show: true,
                        feature: {
                            magicType: {
                                show: true,
                                type: ['pie', 'funnel']
                            },
                            restore: {
                                show: false,
                                //title: "Restore"
                            },
                            saveAsImage: {
                                show: false,
                                // title: "Save Image"
                            }
                        }
                    },
                    calculable: true,
                    series: [{
                        name: 'Area Mode',
                        type: 'pie',
                        radius: [25, 90],
                        center: ['50%', 170],
                        x: '50%',
                        max: 40,
                        sort: 'ascending',
                        data: pieData
                    }]
                });

            }
        }


        function lineChart(data=[]){
            if ($('#visit_vs_productive_non_productive_bar').length ){
                let t_visit=[];
                let p_visit=[];
                let np_visit=[];
                let labels=[];

                labels = Object.keys(data);


                Object.values(data).forEach(([total, productive, non_productive]) => {
                    t_visit.push(total);
                    p_visit.push(productive);
                    np_visit.push(non_productive);
                });

                var echartBar = echarts.init(document.getElementById('visit_vs_productive_non_productive_bar'), design);
                echartBar.setOption({
                    title: {
                        text: '',
                        subtext: ''
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: labels
                    },
                    toolbox: {
                        show: false
                    },
                    calculable: false,
                    xAxis: [{
                        type: 'category',
                        data: labels
                    }],
                    yAxis: [{
                        type: 'value'
                    }],
                    grid: { containLabel: true },
                    series: [{
                        name: 'Total Visit',
                        type: 'bar',
                        data: t_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Productive',
                        type: 'bar',
                        data: p_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Non Productive',
                        type: 'bar',
                        data: np_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    }
                    ]
                });

            }
        }


        function svActivitylineChart(data=[]){
            if ($('#sv-activities-bar').length ){
                let t_visit=[];
                let p_visit=[];
                let np_visit=[];
                let labels=[];

                labels = Object.keys(data);


                Object.values(data).forEach(([total, productive, non_productive]) => {
                    t_visit.push(total);
                    p_visit.push(productive);
                    np_visit.push(non_productive);
                });

                var echartBar = echarts.init(document.getElementById('sv-activities-bar'), design);
                echartBar.setOption({
                    title: {
                        text: '',
                        subtext: ''
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: labels
                    },
                    toolbox: {
                        show: false
                    },
                    calculable: false,
                    xAxis: [{
                        type: 'category',
                        data: labels
                    }],
                    yAxis: [{
                        type: 'value'
                    }],
                    grid: { containLabel: true },
                    series: [{
                        name: 'Total Visit',
                        type: 'bar',
                        data: t_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Productive',
                        type: 'bar',
                        data: p_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Non Productive',
                        type: 'bar',
                        data: np_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    }
                    ]
                });

            }
        }


        $(document).ready(function () {

            $('#note_summary').hide()
            $('#order-table').show()
            $('#delivery-table').hide()
            $('#route-table').hide()
            $('#visit-table').hide()
            $('#item-table').hide()

            $('#datatable').DataTable({
                dom: 'Bfrtip',
                bDestroy: 'true',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
                ]

            });
        });

        $('.start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: '-3m',
            maxDate: new Date(),
            autoclose: 1,
            showOnFocus: true
        });

        $(".end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });


    </script>

    <script>
        let isSupervisorReport;

        function getEmpSummaryData(){
            let data;
            let sr_id=$.trim($('#sr_id').val());
            let sv_id=$.trim($('#sv_id').val());
            let _token=$('#_token').val();
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            if(sr_id=='' && sv_id ==''){
                confirm('Please provide sr/supervisor staff id');
                return false;
            }

            isSupervisorReport = false;

            empCardClear();
            clearTablesNCharts();
            sr_report();


            data = {
                sr_id: sr_id,
                sv_id: sv_id,
                start_date: start_date,
                end_date: end_date,
                _token: _token
            };


            if(sr_id == '' && sv_id !== ''){
                isSupervisorReport = true;
                supervisor_report();
            }


            if(sv_id !== '' && sr_id !== ''){
                isSupervisorReport = false;
            }


            if(isSupervisorReport){
                sv_emp_summary(data);
                sv_work_note_data(data);
                sv_attendance_data(data);
                sv_work_note_summary(data);
                sv_thana_coverage(data);
                sv_activity(data);
            }else{
                sr_emp_summary(data);
                sr_dateWiseDetails(data);
                sr_activity(data);
                sr_GovtHierarchyCoverage(data);
                sr_order_delivery(data);
                sr_ItemSummery(data);
            }


        }


        function sr_emp_summary(data){
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrEmpSummaryData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        let t_visit = info.ro_visit;
                        let ct_visit = parseInt(info.p_visit)+parseInt(info.np_visit);
                        let t_memo = info.t_memo;
                        let t_rvisit = info.ro_visit;
                        let t_sku = info.t_sku;
                        let t_outlet = info.t_outlet;
                        let t_amnt = info.t_amnt;
                        let ctb_cont = '<span>P:' + info.p_visit + ' || NP: ' + (info.np_visit) + ' || RO:' + t_rvisit + '</span>';
                        $('#ct_visit').append(ct_visit);
                        $('#ctb_visit').append(ctb_cont);
                        $('#ct_ord').append(t_memo);
                        $('#t_outlet').append(t_outlet);
                        $('#t_amnt').append(t_amnt);
                        $('#lpc').append(info.lpc);
                        $('#staff-info').show();
                        $('#staff-info').append(`<div>Name: ${info.aemp_name}</div><div> Staff Id: ${info.aemp_usnm}</div>`);

                        let pieData = [
                            {value: info.present, name: 'Present'},
                            {value: info.iom, name: 'IOM'},
                            {value: info.leave, name: 'Leave'},
                            {value: info.fc_leave, name: 'Force Leave'}
                        ];

                        echart(pieData)

                        $('#total-days').append(t_days);
                        $('#total-present').append(info.present);
                        $('#total-iom').append(info.iom);
                        $('#total-leave').append(info.leave);
                        $('#total-fc_leave').append(info.fc_leave);

                    }



                    swal.fire({
                        position: 'top-right',
                        icon: 'success',
                        title: 'Summary data is ready',
                        showConfirmButton: false,
                        timer: 1500
                    })
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }

        function sv_emp_summary(data){
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSvEmpSummaryData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        let t_visit = info.ro_visit;
                        let ct_visit = parseInt(info.p_visit)+parseInt(info.np_visit);
                        let t_memo = info.t_memo;
                        let t_rvisit = info.ro_visit;
                        let t_sku = info.t_sku;
                        let t_outlet = info.t_outlet;
                        let t_amnt = info.t_amnt;
                        let ctb_cont = '<span>P:' + info.p_visit + ' || NP: ' + (info.np_visit) + ' || RO:' + t_rvisit + '</span>';
                        $('#ct_visit').append(ct_visit);
                        $('#ctb_visit').append(ctb_cont);
                        $('#ct_ord').append(t_memo);
                        $('#t_outlet').append(t_outlet);
                        $('#t_amnt').append(t_amnt);
                        $('#lpc').append(info.lpc);
                        $('#staff-info').show();
                        $('#staff-info').append(`<div>Name: ${info.aemp_name}</div><div> Staff Id: ${info.aemp_usnm}</div>`);

                    }



                    swal.fire({
                        position: 'top-right',
                        icon: 'success',
                        title: 'Summary data is getting ready...',
                        showConfirmButton: false,
                        timer: 3000
                    })
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }

        function sv_attendance_data(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrAttendanceData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let attendance_summary_info;
                    let employee_wise_details;

                    $('#date-or-sr-wise-details').html('SR Wise Details');

                    $('#employee-wise-details').html(`<tr class="tbl_header_light">
                                            <th class="cell_left_border">Staff ID</th>
                                            <th>Name</th>
                                            <th>T.Olt</th>
                                            <th>V.Olt</th>
                                            <th>S.Olt</th>
                                            <th>Order</th>
                                            <th>Exp</th>
                                        </tr>`);

                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            let visited_outlet = (parseInt(data[i]['np_visit']))+(parseInt(data[i]['p_visit']));
                            employee_wise_details += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['t_outlet'] + '</td>' +
                                '<td>' + visited_outlet + '</td>' +
                                '<td>' + data[i]['p_visit'] + '</td>' +
                                '<td>' + data[i]['t_memo']+'</td>' +
                                '<td>' + data[i]['t_amnt'] + '</td>';
                        }

                    if (data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            attendance_summary_info += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['present'] + '</td>' +
                                '<td>' + data[i]['iom'] + '</td>' +
                                '<td>' + data[i]['leave'] + '</td>' +
                                '<td>' + data[i]['fc_leave'] + '</td>';
                        }
                    }

                    $('#date-wise-details').html(employee_wise_details);
                    $('#attendance-summary-details').html(attendance_summary_info);
                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sr_GovtHierarchyCoverage(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getGovtHierarchyCoverage",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let html;
                    let outlet_coverage = 0;
                    if(data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            outlet_coverage +=  parseInt(data[i]['t_outlet']);
                            var r_id = data[i]['id'];
                            html += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['ward_name'] + '</td>' +
                                '<td>' + data[i]['than_name'] + '</td>' +
                                '<td>' + data[i]['dsct_name'] + '</td>' +
                                '<td>' + data[i]['t_outlet'] + '</td>';
                        }


                        $('#govt-hierarchy-coverage').html(html);
                        let outlet_cov = '<span>O.Cov:' + outlet_coverage + '</span>';

                        $('#outlet-coverage').empty();
                        $('#outlet-coverage').append(outlet_cov);
                    }


                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sr_ItemSummery(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getItemSummary",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let html;
                    if(data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            html += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['amim_code'] + '</td>' +
                                '<td>' + data[i]['amim_name'] + '</td>' +
                                '<td>' + data[i]['amim_qty'] + '</td>' +
                                '<td>' + data[i]['total_amnt'] + '</td>' +
                                '<td>' + data[i]['deli_amnt']+'</td>';
                        }

                        $('#item-summery').html(html);
                        $('#t_sku').html(data.length);
                    }else{
                        $('#t_sku').html(0);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sr_dateWiseDetails(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getDateWiseDetails",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let html;
                    $('#date-or-sr-wise-details').html('Date Wise Details');

                    $('#employee-wise-details').html(`<tr class="tbl_header_light">
                                                <th class="cell_left_border">Date</th>
                                                <th>T.Olt</th>
                                                <th>V.Olt</th>
                                                <th>S.Olt</th>
                                                <th>Order</th>
                                                <th>Exp</th>
                                                <th>In Time</th>
                                                <th>Out Time</th>
                                                <th>W.Hour</th>
                                            </tr>`);

                    if(data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            let visited_outlet = (parseInt(data[i]['v_outlet']))+(parseInt(data[i]['s_outet']));
                            html += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['date'] + '</td>' +
                                '<td>' + data[i]['t_outlet'] + '</td>' +
                                '<td>' + visited_outlet + '</td>' +
                                '<td>' + data[i]['s_outet'] + '</td>' +
                                '<td>' + data[i]['t_memo']+'</td>' +
                                '<td>' + data[i]['t_amnt'] + '</td>' +
                                '<td>' + data[i]['inTime'] + '</td>'+
                                '<td>' + data[i]['outTime'] + '</td>'+
                                '<td>' + data[i]['working_duration'] + '</td>';
                        }

                        $('#date-wise-details').html(html);
                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    400: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }

        function sr_order_delivery(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getOrderDelivery",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        let deli_amnt = '<span> D:' + info.deli_amnt+'</span>';
                        $('#deli_amnt').empty();
                        $('#deli_amnt').append(deli_amnt);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sr_activity(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrActivityData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data) {
                        lineChart(data)
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sv_activity(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSvActivityData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data) {
                        svActivitylineChart(data)
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function sv_work_note_summary(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSvWorkNoteSummary",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {

                    let note_summary_info = '';
                    if(data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            note_summary_info += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['note_date'] + '</td>' +
                                '<td>' + data[i]['9amnt'] + '</td>' +
                                '<td>' + data[i]['10amnt'] + '</td>' +
                                '<td>' + data[i]['11amnt'] + '</td>' +
                                '<td>' + data[i]['12pmnt'] + '</td>' +
                                '<td>' + data[i]['1pmnt'] + '</td>' +
                                '<td>' + data[i]['14pmnt'] + '</td>' +
                                '<td>' + data[i]['15pmnt'] + '</td>' +
                                '<td>' + data[i]['16pmnt'] + '</td>' +
                                '<td>' + data[i]['17pmnt'] + '</td>' +
                                '<td>' + data[i]['18pmnt'] + '</td>';
                        }

                        $('#note-summary-info').html(note_summary_info);
                    }
                },
                error:function(error){
                    console.log(error);
                    $('#note-summary-info').html('')
                }
            })
        }

        function sv_work_note_data(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSvWorkNoteData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0])
                    {
                        let total_notes = data[0].notes|0;
                        $('#items-or-notes').html(`<span class="count_top"><i class="fa fa-sticky-note"></i> Notes</span>
                                                    <div class="count" id="t_sku">${total_notes}</div>
                                                    <a href="#" class="t_sku" id="total-item-coverage">&nbsp</a>`);

                    }
                },
                error:function(error){
                    console.log(error);
                    $('#note-summary-info').html('')
                }
            })
        }

        function sv_thana_coverage(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSvThanaCoverage",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let thana_coverage;
                    let outlet_coverage = 0;
                    if(data[0]) {
                        for (var i = 0; i < data.length; i++) {
                            outlet_coverage +=  parseInt(data[i]['t_outlet']);
                            var r_id = data[i]['id'];
                            thana_coverage += '<tr class="tbl_body_gray">' +
                                '<td>' + data[i]['than_name'] + '</td>' +
                                '<td>' + data[i]['dsct_name'] + '</td>' +
                                '<td>' + data[i]['t_outlet'] + '</td>';
                        }


                        $('#sv-thana-coverage-info').html(thana_coverage);
                        let outlet_cov = '<span>O.Cov:' + outlet_coverage + '</span>';

                        $('#outlet-coverage').empty();
                        $('#outlet-coverage').append(outlet_cov);
                    }


                },
                error:function(error){
                    console.log(error);
                }
            })
        }

        function empCardClear(){
            $('#ct_visit').empty();
            $('#ctb_visit').empty();
            $('#ct_ord').empty();
            $('#staff-info').empty();
            $('#t_outlet').empty();
            $('#t_amnt').empty();
            $('#lpc').empty();
            $('#t_sku').empty();
            $('#outlet-coverage').empty();
            $('#deli_amnt').empty();
        }

        function clearTablesNCharts(){

            $('#total-days').empty();
            $('#total-present').empty();
            $('#total-iom').empty();
            $('#total-leave').empty();
            $('#total-fc_leave').empty();

            echart([]);
            lineChart([]);
            svActivitylineChart([]);

            $('#attendance-summary-details').html('');
            $('#date-wise-details').html('');
            $('#item-summery').html('');
            $('#govt-hierarchy-coverage').html('');
            $('#sv-thana-coverage-info').html('');

        }

        function supervisor_report(){
            $('#attendance-summary-pie-chart').hide()
            $('#visit_vs_productive_non_productive_bar').hide()
            $('#attendance-summary-table').show()
            $('#item-summary').hide()
            $('#note_summary').show()
            $('#sr-ward-coverage').hide()
            $('#sv-thana-coverage').show()
            $('#sv-activities-bar').show();
        }

        function sr_report(){
            $('#attendance-summary-pie-chart').show();
            $('#visit_vs_productive_non_productive_bar').show();
            $('#attendance-summary-table').hide();
            $('#item-summary').show();
            $('#note_summary').hide();
            $('#sr-ward-coverage').show();
            $('#sv-thana-coverage').hide();
            $('#sv-activities-bar').hide();
            $('#items-or-notes').html(`<span class="count_top"><i class="fa fa-product-hunt"></i> Item</span>
                                                    <div class="count" id="t_sku">0</div>
                                                    <a href="#" class="t_sku" id="total-item-coverage">&nbsp</a>`);
        }
    </script>
@endsection