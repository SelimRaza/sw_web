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
                            <strong> All Activity Report </strong>
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
                                    <label class="control-label">Date</label>
                                    <div class="input-group date-picker input-daterange">

                                            <input type="text" class="form-control" name="date" id="date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>

                                        <div class="col-md-4 col-sm-4 col-xs-12">

                                        </div>

                                    </div>
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
                            <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv')"
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
                            <h1>Activity Summary</h1>
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
                                    <th>emp Id</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Att Count</th>
                                    <th>Total Outlet</th>
                                    <th>Visited Outlet</th>
                                    <th>Time Spend(Min)</th>
                                    <th>Check in Count</th>
                                    <th>Productive Outlet</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>note Count</th>
                                    <th>Location Count</th>
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
        $('#date').datetimepicker({format: 'YYYY-MM-DD'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        }
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

            //  console.log(5 + 6);
            var date = $("#date").val();
            var emp_id = $("#emp_id").val();
            var _token = $("#_token").val();
            /*if(dbhouse_id == ''){
             dbhouse_id = -1;
             }*/
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/activity/filterMyActivitySummary",
                data: {
                    date: date,
                    emp_id: emp_id,
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
                        var note = ((data[i].note_count!=null) ? data[i].note_count : 0);
                        var loc_count = ((data[i].loc_count!=null) ? data[i].loc_count : 0);
                        var outlet_count = ((data[i].outlet_count!=null) ? data[i].outlet_count : 0);
                        var visited_count = ((data[i].visited_count!=null) ? data[i].visited_count : 0);
                        var time_spend = ((data[i].time_spend!=null) ? data[i].time_spend : 0);
                        var checkin_count = ((data[i].checkin_count!=null) ? data[i].checkin_count : 0);
                        var productive_count = ((data[i].productive_count!=null) ? data[i].productive_count : 0);
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].emp_id + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].name + '</td>' +
                            '<td>' + data[i].role + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '<td>' + data[i].att_count + '</td>' +
                            '<td>' + outlet_count + '</td>' +
                            '<td>' + visited_count + '</td>' +
                            '<td>' + time_spend + '</td>' +
                            '<td>' + checkin_count + '</td>' +
                            '<td>' + productive_count + '</td>' +
                            '<td>' + data[i].start_time + '</td>' +
                            "<td>" + data[i].end_time + "</td>" +
                            '<td>' + note + '</td>' +
                            '<td>' + loc_count + '</td>' +
                            "<td><a target='_blank' href='{{ URL::to('/')}}/activity/attendanceSummaryDetails/" + data[i].emp_id + "/" + data[i].date + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Attendance Detials </a></td>" +
                            "<td><a target='_blank' href='{{ URL::to('/')}}/activity/noteSummaryDetails/" + data[i].emp_id + "/" + data[i].date + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Note Detials </a></td>" +
                            "<td><a target='_blank' href='{{ URL::to('/')}}/activity/summaryLocationAll/" + data[i].emp_id + "/" + data[i].date + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Activity In Map </a></td>" +
                            "<td><a target='_blank' href='{{ URL::to('/')}}/activity/mySummaryBy/" + data[i].emp_id + "/" + data[i].date + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>  More Details </a></td>" +
                            '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
@endsection