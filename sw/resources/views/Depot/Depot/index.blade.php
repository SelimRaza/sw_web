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
                            <strong>All Distributor</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/depot')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{$search_text}}">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
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
                    @if($permission->wsmu_crat)
                        <a href="{{ URL::to('/depot/create')}}" class="btn btn-success btn-sm">Add New</a>
                        @if($cont_id == '2' || $cont_id == '5')
                            <a href="{{ URL::to('/depot/upload/login')}}" class="btn btn-success btn-sm">Dealer Login Create</a>
                        @endif
                    @endif
                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action="{{url('/depot/filterDepotddd')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="acmp_id" id="acmp_id"
                                                onchange="getGroup(this.value)">
                                            <option value="">Select Company</option>
                                            @foreach($acmp as $acmpList)
                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                    - {{$acmpList->acmp_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Group<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="slgp_id" id="slgp_id">
                                            <option value="">Select Group</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">District<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="dist_id" id="dist_id"
                                                onchange="getThana(this.value)">
                                            <option value="">Select District</option>
                                            @foreach($dsct as $dsctList)
                                                <option value="{{$dsctList->id}}">{{$dsctList->dsct_code}}
                                                    - {{$dsctList->dsct_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Thana
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="than_id" id="than_id">

                                            <option value="">Select Thana</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button id="send" type="button"
                                            class="btn btn-success  col-md-offset-2 col-sm-offset-2"
                                            onclick="getReport()">Submit
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>

                    <div class="x_panel" id="d_report" style="overflow: auto;">
                        <div class="x_content">
                            <button class="btn btn-danger btn-sm" onclick="exportTableToCSV('dealer_master<?php echo date('Y_m_d'); ?>.csv','datatables')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>
                            {{--{{$depots->appends(Request::only('search_text'))->links()}}--}}
                            <table id="datatables" class="table table-bordered table-responsive font_color search-table" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Owner Name</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Group</th>
                                    <th>Thana</th>
                                    <th>Base</th>
                                    <th>Zone</th>
                                    <th>Region</th>
                                    <th>Login Status</th>
                                    <th style="width: 20%">Action</th>
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

        //$("#d_report").hide();

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
        function getThana() {

            //clearDate();
            var district_id = $('#dist_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/market_open/thana_list",
                data: {
                    district_id: district_id
                },

                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#than_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Thana</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].than_code + ' - ' + data[i].than_name + '</option>';
                    }
                    $("#than_id").append(html);

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

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var slgp_id = $('#slgp_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var dist_id = $('#dist_id').val();
            var than_id = $('#than_id').val();
            $("#d_report").hide();
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (slgp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/depot/filter/DepotDetails",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        dist_id: dist_id,
                        than_id: than_id,
                        dirg_id: dirg_id,
                        slgp_id: slgp_id,
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
                        var passReset = "";
                        var contry = "";
                        for (var i = 0; i < data.length; i++) {

                            if (data[i]['dlrm_akey'] == "Y") {
                                astatus = "Disabled";
                                passReset = "";
                            } else {
                                astatus = "";
                                passReset = "Disabled";
                            }
                            if (data[i]['lfcl_id'] == "1") {
                                dstatus = "<i class='fa fa-thumbs-up'></i> Active";
                            } else {
                                dstatus = "<i class='fa fa-thumbs-down' ></i> InActive";
                            }
                            contry = data[i]['cont_id'];

                            if (contry == "2" || contry == "5") {
                                html += '<tr class="tbl_body_gray">' +
                                    '<td class="cell_left_border">' + count + '</td>' +
                                    '<td>' + data[i]['dlrm_code'] + '</td>' +
                                    '<td>' + data[i]['dlrm_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_ownm'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_adrs'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_mob1'] + '</td>' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['base_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['than_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['zone_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dirg_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_akey'] + '</td>' +
                                    '<td> <a href="depot/' + data[i]['id'] + '" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View</a>' +
                                    '<a href="depot/' + data[i]['id'] + '/edit" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit</a>' +
                                    '<a href="depot/employee/' + data[i]['id'] + '" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Employee</a>' +
                                    '<button value=" ' + data[i]['id'] + '" class="btn btn-danger btn-xs" ' + astatus + ' onclick="if(confirm(\'Do you want to create distributor credential?\'))getDealerLogin(this.value)" ><i class="fa fa-plus-circle"></i> Create Dealer Login</button>' +
                                    '<button value=" ' + data[i]['id'] + '" class="btn btn-danger btn-xs" ' + passReset + ' onclick="if(confirm(\'Do you want to reset password?\'))getDealerPassReset(this.value)" ><i class="fa fa-refresh"></i> Reset Password</button></td>' +


                                    count++;
                            }else{
                                html += '<tr class="tbl_body_gray">' +
                                    '<td class="cell_left_border">' + count + '</td>' +
                                    '<td>' + data[i]['dlrm_code'] + '</td>' +
                                    '<td>' + data[i]['dlrm_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_ownm'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_adrs'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_mob1'] + '</td>' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['base_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['than_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['zone_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dirg_name'].replace(/,/g, '-') + '</td>' +
                                    '<td>' + data[i]['dlrm_akey'] + '</td>' +
                                    '<td> <a href="depot/' + data[i]['id'] + '" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View</a>' +
                                    '<a href="depot/' + data[i]['id'] + '/edit" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit</a>' +
                                    '<a href="depot/employee/' + data[i]['id'] + '" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Employee</a>' +
                                    '<button id="btn_status" value=" ' + data[i]['id'] + '" class="btn btn-warning btn-xs" onclick="if(confirm(\'Do you want to change distributor status?\'))changeDealerStatus(this.value)" > ' + dstatus +'</button></td>' +



                                    count++;
                            }
                        }
                        $("#cont").append(html);
                        $('#tableDiv').show();

                    }
                });
            } else {
                alert("Please select Group and Try again!!!");
            }

        }

        function getDealerLogin(d_id) {
            //alert(d_id);
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            //alert(idddd);
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/depot/create/dealer/login",
                data: {
                    id: d_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    alert(data);
                    getReport();

                }

            });

        }

        function getDealerPassReset(d_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            //alert(idddd);
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/depot/create/dealer/passReset",
                data: {
                    id: d_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    alert(data);
                    getReport();

                }

            });
        }
        function changeDealerStatus(d_id) {
            //alert(d_id);
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            //alert(idddd);
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/depot/dealer/statusChange",
                data: {
                    id: d_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    alert(data);
                    //getReport();

                }

            });
        }

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });


        function exportTableToCSV(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
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


        $(document).ready(function(){
            $('table.search-table').tableSearch({
                searchPlaceHolder:'Search Text'
            });
        });
    </script>
@endsection