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
                            <strong>All Activity Report</strong>
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

                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Group</label>
                                    <select class="form-control" name="sales_group_id" id="sales_group_id">
                                        <option value=""></option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ ucfirst($group->slgp_name)."(".$group->slgp_code.")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">User name</label>
                                    <input id="user_name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="user_name"
                                           placeholder="user_name" required="required" type="text">
                                    {{--    <select class="form-control" name="emp_id" id="emp_id" required>
                                            <option value="">Select</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->aemp_name." (".$user->aemp_usnm.")" }}</option>
                                            @endforeach
                                        </select>--}}
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
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Att Count</th>
                                    <th>note Count</th>
                                    <th>visit Count</th>
                                    <th>Productive Count</th>
                                    <th>Order Amount</th>
                                    <th>Memo Line Count</th>
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
        // $("#emp_id").select2({width: 'resolve'});
        //  $("#department_id").select2({width: 'resolve'});
         $("#sales_group_id").select2({width: 'resolve'});
        var user_name = $("#user_name").val();
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
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var user_name = $("#user_name").val();
            // var department_id = $("#department_id").val();
             var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            console.log(start_date + end_date);
            /*if(dbhouse_id == ''){
             dbhouse_id = -1;
             }*/
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/activity/filterActivitySummary",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    user_name: user_name,
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
                        var note = ((data[i].note_count != null) ? data[i].note_count : 0);
                        var loc_count = ((data[i].loc_count != null) ? data[i].loc_count : 0);
                        var visit_count = ((data[i].visit_count != null) ? data[i].visit_count : 0);
                        var prod_count = ((data[i].prod_count != null) ? data[i].prod_count : 0);
                        var order_amount = ((data[i].order_amount != null) ? data[i].order_amount : 0);
                        var item_count = ((data[i].item_count != null) ? data[i].item_count : 0);
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].name + '</td>' +
                            '<td>' + data[i].role_id + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '<td>' + data[i].att_count + '</td>' +
                            '<td>' + note + '</td>' +
                            '<td>' + visit_count + '</td>' +
                            '<td>' + prod_count + '</td>' +
                            '<td>' + order_amount + '</td>' +
                            '<td>' + item_count + '</td>' +
                            '<td>' + data[i].start_time + '</td>' +
                            "<td>" + data[i].end_time + "</td>" +
                            '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
@endsection