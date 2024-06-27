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
                            <strong>Employee</strong>
                        </li>

                        <li>
                            New Employee
                        </li>

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

                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action=""
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                               for="name" style="text-align: left">Company<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="acmp_id" id="acmp_id"
                                                    onchange="getGroup(this.value)">
                                                <option value="">Select Company</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Group<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="slgp_id" id="slgp_id">
                                                <option value="">Select Group</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>

                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                               for="name" style="text-align: left">SR ID<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input type="text" name="sr_id" class="form-control" id="sr_id">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <button id="send" type="button"
                                                class="btn btn-success" style="margin-left: 10px;margin-top: 25px;"
                                                onclick="getReport()"><span class="fa fa-search"></span> Search
                                        </button>
                                    </div>


                                </div>

                            </form>
                        </div>

                    </div>
                    <div class="x_panel" id="report_div">
                        <div class="x_title">


                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">

                                <form target="_blank" class="form-horizontal form-label-left"
                                      action="{{ URL::to('/')}}/employee_new"
                                      method="post">
                                    {{csrf_field()}}
                                    <button type="button" class="btn btn-danger btn-sm" onclick="exportTableToCSV('new_employee_list_<?php echo date('Y_m_d'); ?>.csv','datatables')"
                                            style="float: right"><span
                                                class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                            File</b></button>
                                    <table id="datatables" class="table table-bordered font_color search-table">
                                        <thead>
                                        <tr class="tbl_header_light">
                                            <th class="cell_left_border">Sl</th>
                                            <th style="width: 10%">User Code</th>
                                            <th>User Name</th>
                                            <th>Mobile</th>
                                            <th>Group</th>
                                            <th>Zone</th>
                                            <th>Designation</th>
                                            <th>Manager</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">

                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("select").select2({width: 'resolve'});

            $('table.search-table').tableSearch({
                searchPlaceHolder:'Search Text'
            });
        });
        $("#report_div").hide();

        function getGroup(slgp_id) {
            $("#slgp_id").empty();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getGroup",
                data: {
                    slgp_id: slgp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#sales_group_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Group</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#slgp_id").append(html);

                }
            });
        }

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var slgp_id = $('#slgp_id').val();
            var sr_id = $('#sr_id').val();
            $("#d_report").hide();
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (sr_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/newemployee/filter/empdetails",
                    data: {
                        acmp_id: acmp_id,
                        slgp_id: slgp_id,
                        sr_id: sr_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $("#d_report").show();

                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        var rr = "";
                        var astatus = "";
                        for (var i = 0; i < data.length; i++) {
                            html += '<tr class="tbl_body_gray">' +
                                '<td class="cell_left_border ">' + count + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['aemp_mob1'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['slgp_name'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['role_name'] + '</td>' +
                                '<td>' + data[i]['manager_name'].replace(/,/g, '-') + '</td>' +
                                '<td><button class="btn btn-success btn-xs" type="submit" value="' + data[i]['id'] +'" name="aemp_id_temp"><i class="fa fa-pencil"></i> Edit </button> </td></tr>'+

                                count++;
                        }
                        $("#cont").append(html);
                        $('#tableDiv').show();
                        $("#report_div").show();
                    }
                });
            }else if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/newemployee/filter/empdetails",
                    data: {
                        acmp_id: acmp_id,
                        slgp_id: slgp_id,
                        sr_id: sr_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $("#d_report").show();

                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        var rr = "";
                        var astatus = "";
                        for (var i = 0; i < data.length; i++) {
                            html += '<tr class="tbl_body_gray">' +
                                '<td class="cell_left_border ">' + count + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['aemp_mob1'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['slgp_name'].replace(/,/g, '-') + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['role_name'] + '</td>' +
                                '<td>' + data[i]['manager_name'].replace(/,/g, '-') + '</td>' +
                                '<td><button class="btn btn-success btn-xs" type="submit" value="' + data[i]['id'] +'" name="aemp_id_temp"><i class="fa fa-pencil"></i> Edit </button> </td></tr>'+

                                count++;
                        }
                        $("#cont").append(html);
                        $('#tableDiv').show();
                        $("#report_div").show();
                    }
                });
            } else {
                alert("Please select Group and Try again!!!");
            }

        }

        function exportTableToCSV(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length-1; j++)
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


        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection