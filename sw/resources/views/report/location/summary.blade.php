@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>All Location History</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>

                                    <div class="input-group date-picker input-daterange">


                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Department</label>

                                    <select class="form-control" name="department_id" id="department_id">
                                        @if(sizeof($userDepartments)==0)
                                            <option value=""></option>
                                            @foreach($departments as $department)
                                                <option value='{{$department->id}}'>{{ $department->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($userDepartments as $department)
                                                <option value='{{$department->id}}'>{{ $department->name }}</option>
                                            @endforeach
                                        @endif


                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Group</label>

                                    <select class="form-control" name="sales_group_id" id="sales_group_id">
                                        @if(sizeof($userSaleGroups)==0)
                                            <option value=""></option>
                                            @foreach($saleGroups as $saleGroup)
                                                <option value='{{$saleGroup->id}}'>{{ $saleGroup->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($userSaleGroups as $userSaleGroup)
                                                <option value='{{$userSaleGroup->id}}'>{{ $userSaleGroup->name }}</option>
                                            @endforeach
                                        @endif


                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Emp</label>

                                    <select class="form-control" name="emp_id" id="emp_id">
                                        <option value=""></option>
                                        @foreach($emps as $emp)
                                            <option value='{{$emp->id}}'>{{ $emp->name."(".$emp->user()->email.")" }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>




                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>
                            <button onclick="exportTableToCSV('location_summary_report_<?php echo date('Y_m_d'); ?>.csv')"
                                    class="btn btn-success">Export CSV File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                        <div class="x_title">
                            <h1>Location History</h1>
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

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>role</th>
                                    <th>Date</th>
                                    <th>Count</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                </tr>
                                </thead>
                                <tbody id="cont">

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#emp_id").select2({width: 'resolve'});
        $("#department_id").select2({width: 'resolve'});
        $("#sales_group_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;
            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
        function filterData() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var emp_id = $("#emp_id").val();
            var department_id = $("#department_id").val();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/location/filterLocationSummary",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    emp_id: emp_id,
                    department_id: department_id,
                    sales_group_id: sales_group_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //onsole.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';

                    var count = 1;
                    for (var i = 0; i < data.length; i++) {
                        var debit = 0;
                        var credit = 0;
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].name + '</td>' +
                            '<td>' + data[i].role_name + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '<td>' + data[i].count + '</td>' +
                            '<td>' + data[i].start_time + '</td>' +
                            '<td>' + data[i].end_time + '</td>' +
                            "<td><a target='_blank' href='{{ URL::to('/')}}/location/summaryLocation/" + data[i].emp_id + "/" + data[i].date + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Employee On Map </a></td>" +

                            '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
@endsection