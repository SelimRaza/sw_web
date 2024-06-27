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

                        <li class="label-success">
                            <a href="{{ URL::to('/data_upload/routeSiteUpload')}}"> Route Site Upload </a>
                        </li>
                        <li class="active">
                            <strong> Generate Route Site Format </strong>
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
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Error! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Generate Route Site Format</h1>
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
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/data_upload/routeSiteFormat')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="item form-group">
                                            <label class="control-label" for="name">Group
                                                <span class="required">*</span>
                                            </label>
                                            <select class="form-control" name="sales_group_id" id="sales_group_id"
                                                    onchange="filterDivision()" required>
                                                <option value="">Select</option>
                                                @foreach ($salesGroups as $salesGroup)
                                                    <option value="{{ $salesGroup->id }}">{{ ucfirst($salesGroup->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="item form-group">
                                        <label class="control-label">Division
                                            <span class="required">*</span>
                                        </label>
                                        <select class="form-control" name="division_id"
                                                id="division_id"
                                                onchange="filterRegion()"
                                                required>
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="item form-group">
                                    <label class="control-label" for="name">Region
                                        <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="region_id" id="region_id"
                                            onchange="filterZone()"
                                            required>
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="item form-group">
                                    <label class="control-label" for="name">Zone
                                        <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="zone_id" id="zone_id"
                                            onchange="filterBase()"
                                            required>
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="item form-group">
                                    <label class="control-label" for="name">Base
                                        <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="base_id" id="base_id"
                                            onchange="filterRoute()"
                                            required>
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="item form-group">
                                    <label class="control-label" for="name"> Route
                                        <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="route_id[]" id="route_id" multiple
                                            required>
                                        <option value=""> Select </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="send" type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>

        $("#sales_group_id").select2({width: 'resolve'});
        $("#route_id").select2({width: 'resolve'});
        $("#division_id").select2({width: 'resolve'});
        $("#region_id").select2({width: 'resolve'});
        $("#zone_id").select2({width: 'resolve'});
        $("#base_id").select2({width: 'resolve'});
        $("#site_id").select2({width: 'resolve'});
        function filterDivision() {
            $("#division_id").empty();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            if (sales_group_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/division/filterDivision",
                    data: {
                        sales_group_id: sales_group_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        $('#ajax_load').css("display", "none");
                        var html = ' <option value="">Select</option>';
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                        }
                        $("#division_id").append(html);


                    }
                });
            }

        }
        function filterRegion() {
            var division_id = $("#division_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterRegion",
                data: {
                    division_id: division_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#region_id").empty();
                    $("#zone_id").empty();
                    $("#base_id").empty();
                    $("#route_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#region_id").append(html);


                }
            });
        }

        function filterZone() {
            var region_id = $("#region_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterZone",
                data: {
                    region_id: region_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#zone_id").empty();
                    $("#base_id").empty();
                    $("#route_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#zone_id").append(html);


                }
            });
        }
        function filterBase() {
            var zone_id = $("#zone_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterBase",
                data: {
                    zone_id: zone_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#base_id").empty();
                    $("#route_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#base_id").append(html);


                }
            });
        }
        function filterRoute() {
            var base_id = $("#base_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterRoute",
                data: {
                    base_id: base_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#route_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#route_id").append(html);


                }
            });
        }
    </script>
@endsection