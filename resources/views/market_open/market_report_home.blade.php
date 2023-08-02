@extends('theme.app')
@section('content')
    <style>
        .x_title {
            margin-bottom: 0px;
        }
        .x_panel {
            width: 100%;
            padding: 0px 8px;
            display: inline-block;
            border: 1px solid #E6E9ED;
            -webkit-column-break-inside: avoid;
            -moz-column-break-inside: avoid;
            column-break-inside: avoid;
            opacity: 1;
            transition: all .2s ease;
        }


    </style>
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
                            <center><strong> ::: Market Report :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" action="{{url('/get/market/report')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="sales_group_id" id="sales_group_id"  onchange="jsonGetEmployeeList()">
                                        <option value="">Select</option>
                                        @foreach($salesGroups as $salesGroup)
                                         <option value="{{$salesGroup->id}}">{{$salesGroup->slgp_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="employee_id" id="employee_id">

                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">District<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="district_id" id="district_id" onchange="getThanaBelogToDistrict()">
                                        <option value="">Select</option>
                                        @foreach($districts as $district)
                                            <option value="{{$district->id}}">{{$district->dsct_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="thana_id" id="thana_id" onchange="getWardNameBelogToThana()">


                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >Ward<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="ward_id" id="ward_id" onchange="loadWardMarket()">


                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Market<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="market_id" id="market_id">


                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button id="send" type="submit" class="btn btn-success">Search</button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_content">
                                <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                    <thead>
                                        <tr class="tbl_header">
                                            <th>SL</th>
                                            <th>Market</th>
                                            <th>Outlet(Count)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1?>
                                    @if(!empty($results))
                                        @foreach($results as $result)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$result->market_name}}</td>
                                                <td>{{$result->market_count}}</td>
                                                <td><a href="{{url('/ward/market/details',$result->market_id)}}"><button class="btn btn-xs btn-info">View</button></a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if(!empty($results))
                        {{$results->links()}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });
        function loadWardMarket() {

            var ward_id=$('#ward_id').val();
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
                    if(!data){
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    }else{

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.market_name));
                        });
                        $el.selectpicker('refresh');
                    }


                }
            });

        }
        function getWardNameBelogToThana() {

            var thana_id=$('#thana_id').val();
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
                    if(!data){
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    }else{

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.ward_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }
        function getThanaBelogToDistrict(){

            var district_id=$('#district_id').val();
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
                    if(!data){
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    }else{

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {

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

            var sales_group_id=$('#sales_group_id').val();
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

                        if(!data){
                            $el.html('');
                            $el.append($("<option></option>").attr("value", "").text("---"));
                            $el.selectpicker('destroy');
                        }else{

                            $el.html(' ');
                            $el.append($("<option></option>").attr("value", "").text("Select"));
                            $.each(data, function(key,value) {
                                $el.append($("<option></option>").attr("value", value.id).text(value.Name+ '-'+value.code));
                            });
                            $el.selectpicker('refresh');
                        }



                }
            });

        }

    </script>
    <script type="text/javascript">

        $(document).ready(function()
        {
            $("tr:odd").css({
                "background-color":"#e8e5e5",
                "color":"#222"});
            $("tr:even").css({
                "background-color":"#edf7f7;",
                "color":"#222"});
            $(".tbl_header").css({
                "color":"#FFFFFF"});
        });

    </script>
@endsection