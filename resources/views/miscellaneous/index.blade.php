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


        function lineChart(data=[]){
            if ($('#visit_vs_productive_non_productive_bar').length ){
                // let t_visit=[];
                // let p_visit=[];
                // let np_visit=[];
                // let labels=[];
                //
                // labels = Object.keys(data);


                // Object.values(data).forEach(([total, productive, non_productive]) => {
                //     t_visit.push(total);
                //     p_visit.push(productive);
                //     np_visit.push(non_productive);
                // });

                var echartBar = echarts.init(document.getElementById('visit_vs_productive_non_productive_bar'), design);
                echartBar.setOption({
                    xAxis: {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            data: [120, 200, 150, 80, 70, 110, 130],
                            type: 'bar',
                            showBackground: true,
                            backgroundStyle: {
                                color: 'rgba(180, 180, 180, 0.2)'
                            }
                        }
                    ]
                });

            }
        }




        $(document).ready(function () {

            lineChart();

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
@endsection