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
                            <strong>Notification</strong>
                        </li>
                        <li class="active">
                            <strong>Create Notification</strong>
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
                    {{--<div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Create Notification :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>--}}

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action="{{url('/get/market/report')}}"
                                  method="get" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Title :
                                            </label>
                                            <input id="title" class="form-control col-md-12 col-xs-12"
                                                   name="title"
                                                   placeholder="Enter Notification title" required="required"
                                                   type="text"/>

                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Body Text :
                                            </label>
                                            <textarea rows="4" cols="50" class="form-control col-md-12 col-xs-12"
                                                      name="bodyMessage" form="usrform">
                                            </textarea>
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Image :
                                            </label>
                                            <input type="file" rows="4" cols="50"
                                                   class="form-control col-md-12 col-xs-12"
                                                   name="image" form="usrform">
                                            </input>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">Company :
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


                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">Group :
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="sales_group_id" id="sales_group_id"
                                                        multiple>
                                                    <option value="">Select Group</option>
                                                </select>
                                            </div>


                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">Zone :
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="zone_id" id="zone_id" multiple>

                                                    <option value="">Select Zone</option>
                                                    @foreach($zoneList as $zoneList)
                                                        <option value="{{$zoneList->id}}">{{$zoneList->zone_code}}
                                                            - {{$zoneList->zone_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">User Role :
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="role_id" id="role_id" multiple>
                                                    <option value="">Select Role</option>
                                                    @foreach($role as $role)
                                                        <option value="{{$role->id}}">{{$role->role_name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button id="send" type="button"
                                            class="btn btn-success" style="margin-left: 8px;"
                                            onclick="getReport()">Submit
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#tableDiv').hide();

        function getGroup(slgp_id) {
            //alert(slgp_id);
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
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#sales_group_id").append(html);

                }
            });
        }

        function getZone(dirg_id) {

            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getZone",
                data: {
                    dirg_id: dirg_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#zone_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Zone</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].zone_code + " - " + data[i].zone_name + '</option>';
                    }
                    $("#zone_id").append(html);

                }
            });
        }

        function getThan(dsct_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/json/get/market_open/thana_list",
                data: {
                    district_id: dsct_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#than_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Thana</option>';
                    for (var i = 0; i < data.length; i++) {
                        //console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].than_code + " - " + data[i].than_name + '</option>';
                    }
                    $("#than_id").append(html);

                }
            });
        }

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/filter/sr_activity_filter/demo2",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        sales_group_id: sales_group_id,
                        start_date: start_date,
                        end_date: end_date,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        //alert(data);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;

                        for (var i = 0; i < data.length; i++) {

                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['date'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['t_sr'] + '</td>' +
                                '<td>' + data[i]['p_sr'] + '</td>' +
                                '<td>' + ((data[i]['p_sr'] * 100) / data[i]['t_sr']).toFixed(2) + '</td>' +
                                '<td>' + data[i]['l_sr'] + '</td>' +
                                '<td>' + (data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']) + '</td>' +
                                '<td>' + data[i]['pro_sr'] + '</td>' +
                                '<td>' + (data[i]['p_sr'] - data[i]['pro_sr']) + '</td>' +
                                '<td>' + data[i]['t_outlet'] + '</td>' +
                                "<td>" + (data[i]['c_outlet']) + "</td>" +
                                "<td>" + (((data[i]['c_outlet']) * 100) / data[i]['t_outlet']).toFixed(2) + "</td>" +
                                "<td>" + data[i]['s_outet'] + "</td>" +
                                "<td>" + (data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2) + "</td>" +
                                "<td>" + (data[i]['lpc']) + "</td>" +
                                "<td>" + (data[i]['t_amnt'] / 1000 / data[i]['p_sr']).toFixed(2) + "</td>" +
                                "<td>" + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + "</td>" +
                                "<td>" + (data[i]['c_outlet'] / (data[i]['p_sr'])).toFixed(2) + "</td>" +
                                "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                '</tr>';
                            count++;
                        }
                        //alert(html);
                        $("#cont").append(html);

                        //$('#datatable').DataTable().draw();
                        $('#tableDiv').show();
                    }

                });
            } else {
                alert("Please select Company and Try again!!!");
            }

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


        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });


    </script>
@endsection