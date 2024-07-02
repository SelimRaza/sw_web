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
                {{--<form action="{{ URL::to('/depot')}}" method="get">
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
                </form>--}}
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
                                        <select class="form-control" name="sales_group_id" id="sales_group_id">
                                            <option value="">Select Group</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Region<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="dirg_id" id="dirg_id"
                                                onchange="getZone(this.value)">
                                            <option value="">Select Region</option>
                                            @foreach($region as $regionList)
                                                <option value="{{$regionList->id}}">{{$regionList->dirg_code}}
                                                    - {{$regionList->dirg_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Zone<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="zone_id" id="zone_id">

                                            <option value="">Select Zone</option>
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

                    <div class="x_panel" id="d_report">
                        <div class="x_title">
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="overflow: auto;">

                            <table id="datatable" class="table table-bordered table-responsive" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Company</th>
                                    <th>Group</th>
                                    <th>Region</th>
                                    <th>Zone</th>
                                    <th>Base</th>

                                    <th>Dealer Code</th>
                                    <th>Dealer Name</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Owner Name</th>
                                    <th>Status</th>
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

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });

        $("#d_report").hide();

        function getGroup(slgp_id) {
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

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            $("#d_report").hide();
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (acmp_id !=""){
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/depot/filterDepotddd",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        dirg_id: dirg_id,
                        sales_group_id: sales_group_id,
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
                            if(data[i]['lfcl_id'] == '1')
                            { rr ="Active"; }
                            else {rr="Inactive"; }
                            if ( data[i]['dlrm_akey'] == "Y"){
                                astatus="Disabled";
                            }else {
                                astatus="";
                            }

                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['acmp_name'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['dirg_name'] + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['base_name'] + '</td>' +
                                '<td>' + data[i]['dlrm_code'] + '</td>' +
                                '<td>' + data[i]['dlrm_name'] + '</td>' +
                                '<td>' + data[i]['dlrm_adrs'] + '</td>' +
                                '<td>' + data[i]['dlrm_mob1'] + '</td>' +
                                '<td>' + data[i]['dlrm_ownm'] + '</td>' +
                                '<td>' + rr + '</td>' +
                                '<td>' + data[i]['dlrm_akey'] + '</td>' +
                                '<td> <a href="depot/' + data[i]['id'] + '" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View</a>' +
                                '<a href="depot/' + data[i]['id'] + '/edit" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit</a>' +
                                '<a href="depot/employee/' + data[i]['id'] + '" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Employee</a>' +
                                '<button value=" ' +data[i]['id']+ '" class="btn btn-danger btn-xs" '+ astatus +' onclick="if(confirm(\'Do you want to create distributor credential?\'))getbutton(this.value)" ><i class="fa fa-pencil"></i>Create Dealer Loginx</button></td>' +

                            count++;
                        }
                        //alert(html);
                        $("#cont").append(html);

                        //$('#datatable').DataTable().draw();
                        $('#tableDiv').show();
                    }

                });
            }else{
                alert("Please select Company and Try again!!!");
            }

        }

        function getbutton(d_id) {
            var idddd = d_id;

            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            //alert(idddd);
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/depot/create/dealer/login",
                data: {
                    id: idddd,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    alert(data);
                    getReport();

                }

            });

        }


    </script>
@endsection