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
                            <strong><a href="{{url('/market_open')}}">All Market</a></strong>
                        </li>
                        <li class="active">
                            <strong>Market Report</strong>
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
                        <div class="x_title">
                            <center><strong> ::: Campaign (SMS) :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" action="{{url('/get/market/report')}}"
                              method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="sales_group_id" id="sales_group_id"
                                            onchange="jsonGetEmployeeList()">
                                        <option value="">Select Company</option>
                                        @foreach($acmp as $acmpList)
                                            <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                - {{$acmpList->acmp_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Schedule<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="sales_group_id" id="sales_group_id"
                                            onchange="jsonGetEmployeeList()">
                                        <option value="">Select Schedule</option>
                                        <option value="">One Time</option>
                                        <option value="">Schedule</option>
                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Body<span
                                            class="required">*</span>
                                </label>

                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="4" cols="50" class="form-control col-md-12 col-xs-12" name="textMessage" form="usrform">
                                            </textarea>
                                </div>
                            </div>

                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Region<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="district_id" id="district_id"
                                            onchange="getThanaBelogToDistrict()">
                                        <option value="">Select Region</option>
                                        @foreach($region as $regionList)
                                            <option value="{{$regionList->id}}">{{$regionList->dirg_code}} - {{$regionList->dirg_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="thana_id" id="thana_id"
                                            onchange="getWardNameBelogToThana()">

                                        <option value="">Select Zone</option>
                                        @foreach($zoneList as $zoneLists)
                                            <option value="{{$zoneLists->id}}">{{$zoneLists->zone_code}} - {{$zoneLists->zone_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Non Productive
                                    (7)<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="ward_id" id="ward_id"
                                            onchange="loadWardMarket()">


                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet Type<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="market_id" id="market_id">
                                        <option value="">Select Category</option>
                                        @foreach($category as $categorys)
                                            <option value="{{$categorys->id}}">{{$categorys->otcg_name}}</option>
                                        @endforeach

                                    </select>
                                </div>


                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">

                                <center>
                                    <button id="send" type="submit" class="btn btn-success">Send SMS</button>
                                </center>
                            </div>

                        </form>
                    </div>



                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });

        function loadWardMarket() {

            var ward_id = $('#ward_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/ward_wise/market_list",
                data: {
                    ward_id: ward_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#market_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.market_name));
                        });
                        $el.selectpicker('refresh');
                    }


                }
            });

        }

        function getWardNameBelogToThana() {

            var thana_id = $('#thana_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/market_open/word_list",
                data: {
                    thana_id: thana_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var $el = $('#ward_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.ward_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }

        function getThanaBelogToDistrict() {

            var district_id = $('#district_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/market_open/thana_list",
                data: {
                    district_id: district_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var $el = $('#thana_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.than_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }

        function ConfirmDelete() {

            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };

        function jsonGetEmployeeList() {

            var sales_group_id = $('#sales_group_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/jsonGetEmployeeList",
                data: {
                    sales_group_id: sales_group_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#employee_id');

                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {
                            $el.append($("<option></option>").attr("value", value.id).text(value.Name + '-' + value.code));
                        });
                        $el.selectpicker('refresh');
                    }


                }
            });

        }

    </script>
@endsection