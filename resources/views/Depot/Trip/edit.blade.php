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
                            <a href="{{ URL::to('/trip')}}">All Trip</a>
                        </li>
                        <li class="active">
                            <strong>Edit Trip</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>

                                    <div class="input-group date-picker input-daterange">


                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date"
                                                   id="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date"
                                                   id="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>">

                                            <input type="hidden" class="form-control" name="dpot_id"
                                                   id="dpot_id"
                                                   value="{{ $trip->dpot_id }}">
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Emp Name
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" multiple name="aemp" id="aemp"
                                                required>
                                            <option value="">Select</option>
                                            @foreach ($aemp as $emp)
                                                <option value="{{ $emp->aemp_id }}">{{ $emp->aemp_name.'('.$emp->aemp_usnm.')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>


                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <form id="demo-form2" data-parsley-validate
                          class="form-horizontal form-label-left"
                          action="{{route('trip.update',$trip->id)}}" enctype="multipart/form-data"
                          method="post">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">

                                    <div class="x_title">
                                        <h1>Order Summary</h1>

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
                                        <div class="col-md-1 col-sm-1 col-xs-12">

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">

                                        <table class="table table-striped projects">
                                            <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select_all"/></th>
                                                <th>S/L</th>
                                                <th>Order Id</th>
                                                <th>Order Amount</th>
                                                <th>Order Date</th>
                                                <th>Order Date Time</th>
                                                <th>User Name</th>
                                                <th>Emp Name</th>
                                                <th>Outlet id</th>
                                                <th>Outlet Code</th>
                                                <th>Outlet Name</th>
                                                <th>Order Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="cont">

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>{{$trip->id}}
                                            <small>{{$trip->depot()->name}}</small>
                                        </h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
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
                                        <br/>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                   for="first-name">Trip
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="mobile_2" name="mobile_2"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       placeholder="Mobile 2" type="text" value="{{$trip->id}}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                   for="first-name">Emp Name
                                                <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="mobile_2" name="mobile_2"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       placeholder="Mobile 2" type="text"
                                                       value="{{$trip->employee()->aemp_usnm.'-'.$trip->employee()->aemp_name}}">
                                            </div>
                                        </div>
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                <button type="submit" class="btn btn-success">Next</button>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#aemp").select2({width: 'resolve'});
        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () { //".checkbox" change
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });

        function filterData() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var aemp = $("#aemp").val();
            var dpot_id = $("#dpot_id").val();
            var _token = $("#_token").val();
            console.log(start_date + end_date);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/trip/filterOrderDetails",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    aemp: aemp,
                    dpot_id: dpot_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //onsole.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;

                    for (var i = 0; i < data.length; i++) {
                        var readonly = '';
                        if (data[i].status_id != 8) {
                            readonly = 'disabled readonly'
                        }
                        html += '<tr>' +
                            " <td><input " + readonly + "  class='checkbox' type='checkbox' name='so_id[]' value='" + data[i].so_id + "'></td>" +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].order_id + '</td>' +
                            '<td>' + data[i].order_amount + '</td>' +
                            '<td>' + data[i].order_date + '</td>' +
                            '<td>' + data[i].order_date_time + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].emp_name + '</td>' +
                            "<td>" + data[i].site_id + "</td>" +
                            "<td>" + data[i].site_code + "</td>" +
                            "<td>" + data[i].site_name + "</td>" +
                            "<td>" + data[i].order_type + "</td>" +
                            "<td>" + data[i].status_name + "</td>" +
                            '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
@endsection