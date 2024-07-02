@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li >
                        <li class="label-success">
                            <a href="{{ URL::to('/market')}}">All Market</a>
                        </li>
                        <li >
                            <strong>New Market</strong>
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
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Market </h1>
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

                        <div class="x_title">

                            <button class="btn btn-success btn-sm" onclick="getAddMarket();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Market</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="add_market">
                            <strong><center>::: Add New Market :::</center></strong>
                            <div class="ln_solid"></div>
                            <form class="form-horizontal form-label-left" action="{{route('market.store')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Division
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="division_id" id="division_id"
                                                onchange="filterDistrict()"
                                                required>
                                            <option value="">Select</option>
                                            @foreach ($govDivisions as $govDivision)
                                                <option value="{{ $govDivision->id }}">{{ $govDivision->disn_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">District
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="district_id" id="district_id"
                                                onchange="filterThana()"
                                                required>
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="thana_id" id="thana_id"
                                                onchange="filterWard()"
                                                required>
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ward
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="ward_id" id="ward_id"
                                                required>
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="code" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               placeholder="Code" required="required" type="text">
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
                        <div class="x_content" id="upload_market">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/data_upload/marketMasterUpload')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="{{ URL::to('/data_upload/marketUploadFormat')}}"><span
                                                class="fa fa-cloud-download"
                                                style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                            Format</b></a>
                                </div>
                                <div class="col-md-12">
                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="import_file"
                                           placeholder="Shop List file" type="file"
                                           step="1">
                                </div>
                                <br/><br/><br/>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button id="send" type="submit" class="btn btn-primary btn-sm"
                                                style="margin-top: 10px;"><span
                                                    class="fa fa-cloud-upload"
                                                    style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                                File</b></button>
                                    </div>
                                </div>
                            </form>
                            <br>
                            <br>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#division_id").select2({width: 'resolve'});
        $("#district_id").select2({width: 'resolve'});
        $("#thana_id").select2({width: 'resolve'});
        $("#ward_id").select2({width: 'resolve'});
        $("#market_id").select2({width: 'resolve'});
        $("#channel_id").select2({width: 'resolve'});
        $("#sub_channel_id").select2({width: 'resolve'});
        $("#outlet_category_id").select2({width: 'resolve'});

        function filterDistrict() {
            var fk_id = $("#division_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterDistrict",
                data: {
                    fk_id: fk_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#district_id").empty();
                    $("#thana_id").empty();
                    $("#ward_id").empty();
                    $("#market_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#district_id").append(html);


                }
            });
        }

        function filterThana() {
            var fk_id = $("#district_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterThana",
                data: {
                    fk_id: fk_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#thana_id").empty();
                    $("#ward_id").empty();
                    $("#market_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#thana_id").append(html);


                }
            });
        }
        function filterWard() {
            var fk_id = $("#thana_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterWard",
                data: {
                    fk_id: fk_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#ward_id").empty();
                    $("#market_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#ward_id").append(html);


                }
            });
        }
        function filterMarket() {
            var fk_id = $("#ward_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterMarket",
                data: {
                    fk_id: fk_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#market_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#market_id").append(html);


                }
            });
        }
        function filterSubChannel() {
            var fk_id = $("#channel_id").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/site/filterSubChannel",
                data: {
                    fk_id: fk_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#sub_channel_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $("#sub_channel_id").append(html);


                }
            });
        }


        $('#upload_market').hide();
        function getAddMarket() {
            $('#upload_market').hide();
            $('#add_market').show();
        }

        function addUploadFile() {
            $('#upload_market').show();
            $('#add_market').hide();
        }

    </script>
@endsection