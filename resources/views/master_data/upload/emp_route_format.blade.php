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
                            <a href="{{ URL::to('/data_upload/empRouteUpload')}}">Employee Route Plan Upload </a>
                        </li>
                        <li class="active">
                            <strong>Generate Route Plan Format </strong>
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
                            <h1>Generate Route Plan Format</h1>
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
                                  action="{{URL::to('/data_upload/empRouteFormat')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="item form-group">
                                            <label class="control-label" for="name">Group
                                                <span class="required">*</span>
                                            </label>
                                            <select class="form-control select2" name="sales_group_id"
                                                    id="sales_group_id" onchange="filterEmployee()" required>
                                                <option value="">Select</option>
                                                @foreach ($salesGroups as $salesGroup)
                                                    <option value="{{ $salesGroup->id }}">{{ ucfirst($salesGroup->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="item form-group">
                                            <label class="control-label">Employee <span class="required">*</span>
                                            </label>
                                            <select class="form-control select2" name="emp_id[]" id="emp_id" multiple
                                                    required>
                                                <option value="">Select</option>

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

        $("#emp_id").select2({width: 'resolve'});
        $("#sales_group_id").select2({width: 'resolve'});
        function filterEmployee() {
            $("#emp_id").empty();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            if (sales_group_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/groupEmployee/filterEmployee",
                    data: {
                        sales_group_id: sales_group_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        $('#ajax_load').css("display", "none");
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                        }
                        $("#emp_id").append(html);


                    }
                });
            }

        }
    </script>
@endsection