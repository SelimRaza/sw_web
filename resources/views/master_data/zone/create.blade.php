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
                        <li>
                            <a href="{{ URL::to('/zone')}}">All Zone</a>
                        </li>
                        <li class="active">
                            <strong>New Zone</strong>
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
                            <h1>Zone </h1>
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

                            <form class="form-horizontal form-label-left" action="{{route('zone.store')}}"
                                  method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Region <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="region_id" id="region_id" required>
                                            <option value="">Select Region</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id }}">{{ ucfirst($region->dirg_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name"
                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="code"
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#region_id").select2({ width: 'resolve' });
        $("#sales_group_id").select2({width: 'resolve'});

        function filterRegion() {
            $("#region_id").empty();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            if (sales_group_id!=""){
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/region/filterRegion",
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
                        $("#region_id").append(html);


                    }
                });
            }

        }
    </script>
@endsection