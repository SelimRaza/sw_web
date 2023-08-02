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
                            <a href="{{ URL::to('/pjp')}}">All Route Plan</a>
                        </li>
                        <li class="active">
                            <strong>New Route Plan</strong>
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
                            <h1>Route Plan </h1>
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

                            <form class="form-horizontal form-label-left" action="{{route('pjp.store')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}


                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="saturday" name="saturday"
                                                   value="Employees" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control select2" name="sales_group_id"
                                                    id="sales_group_id" onchange="filterEmployee()" required>
                                                <option value="">Select</option>
                                                @foreach ($salesGroups as $salesGroup)
                                                    <option value="{{ $salesGroup->id }}">{{ ucfirst($salesGroup->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control select2" name="emp_id" id="emp_id" required>
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table class="table table-striped table-bordered"
                                        >
                                            <thead>
                                            <tr style="background-color: #2b4570; color: white;">
                                                <th>Day</th>
                                                <th>Route</th>
                                            </tr>
                                            </thead>
                                            <tbody id="cont">
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="saturday"
                                                           name="day[]"
                                                           value="Saturday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="saturday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="sunday" name="day[]"
                                                           value="Sunday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="sunday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="monday" name="day[]"
                                                           value="Monday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="monday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="tuesday" name="day[]"
                                                           value="Tuesday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="tuesday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="wednesday" name="day[]"
                                                           value="Wednesday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="wednesday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="thursday"
                                                           name="day[]"
                                                           value="Thursday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="thursday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="friday" name="day[]"
                                                           value="Friday" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="route_id[]"
                                                            id="friday_route_id">
                                                        <option value="">Select</option>
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}">{{ $route->name.'('.$route->code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>


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
        $(".select2").select2({width: 'resolve'});
        $("table").on('click', '.removeLine', function () {
            $(this).parent('td').parent('tr').remove();
            //grand_total_amount();
        });

        //$("#sales_group_id").select2({width: 'resolve'});
        // $("#emp_id").select2({width: 'resolve'});
        function filterEmployee() {
            //filterRoute();
            $("#emp_id").empty();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            if (sales_group_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/employee/filterEmployeeByGroup",
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
    /*    function filterRoute() {
            $("#saturday_route_id").empty();
            $("#sunday_route_id").empty();
            $("#monday_route_id").empty();
            $("#tuesday_route_id").empty();
            $("#wednesday_route_id").empty();
            $("#thursday_route_id").empty();
            $("#friday_route_id").empty();
            var sales_group_id = $("#sales_group_id").val();
            var _token = $("#_token").val();
            if (sales_group_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/route/filterRoute",
                    data: {
                        sales_group_id: sales_group_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select</option>';
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                        }
                        $("#saturday_route_id").append(html);
                        $("#sunday_route_id").append(html);
                        $("#monday_route_id").append(html);
                        $("#tuesday_route_id").append(html);
                        $("#wednesday_route_id").append(html);
                        $("#thursday_route_id").append(html);
                        $("#friday_route_id").append(html);


                    }
                });
            }

        }*/
    </script>
@endsection
