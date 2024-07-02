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
                            <a>Group Permission</a>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>
            <p id="employee_id" style="display: none">{{$emp_id}}</p>
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
                            <center><strong> ::: All Groups:::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id="" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>All<br><input type="checkbox" id="group_all"></th>
                                    <th>SL</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1?>
                                @foreach($results as $result)
                                    <tr>
                                        <td>
                                            @if($result->status==1)
                                                <input type="checkbox" class="sub_chk" name="group" value="{{$result->id}}" checked>
                                            @else
                                                <input type="checkbox" class="sub_chk" name="group" value="{{$result->id}}">
                                            @endif
                                        </td>
                                        <td>{{$i++}}</td>
                                        <td>{{$result->slgp_name}}---->{{$result->slgp_code}}</td>
                                        <td><span class="badge badge-secondary">@if($result->status==1){{"Assign"}}@endif</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="">
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
                            <center><strong> ::: All Zones :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id="" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>All<br><input type="checkbox" id="zone_all"></th>
                                    <th>SL</th>
                                    <th>Zone</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1?>
                                @foreach($result2 as $result)
                                    <tr>
                                        <td>
                                            @if($result->status==1)
                                               <input type="checkbox" class="sub_chk1" name="zone" value="{{$result->id}}" checked>
                                            @else
                                                <input type="checkbox" class="sub_chk1" name="zone" value="{{$result->id}}">
                                            @endif
                                        </td>
                                        <td>{{$i++}}</td>
                                        <td>{{$result->zone_name}}---->{{$result->zone_code}}</td>
                                        <td><span class="badge badge-secondary">@if($result->status==1){{"Assign"}}@endif</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                        <button style="margin-bottom: 10px" class="btn btn-danger zone_submit_all">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });
        function ConfirmDelete()
        {
            var x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false;
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {

                $('#group_all').on('click', function(e) {

                    if($(this).is(':checked',true)){

                        $(".sub_chk").prop('checked', true);

                    } else {

                        $(".sub_chk").prop('checked',false);

                    }

                });

                $('#zone_all').on('click', function(e) {

                    if($(this).is(':checked',true)){

                        $(".sub_chk1").prop('checked', true);

                    } else {

                        $(".sub_chk1").prop('checked',false);

                    }

                });

                $(".zone_submit_all").click(function(){

                    var zones = [];
                    var groups = [];
                    var uncheck_zones =[];
                    var uncheck_groupa =[];
                    var employee_id=$("#employee_id").html();
                    $.each($("input[name='zone']:checked"), function(){

                        zones.push($(this).val());
                    });
                    $.each($("input[name='group']:checked"), function(){

                        groups.push($(this).val());
                    });

                    $.each($("input[name='zone']:not(:checked)"), function(){

                        uncheck_zones.push($(this).val());
                    });

                    $.each($("input[name='group']:not(:checked)"), function(){

                        uncheck_groupa.push($(this).val());
                    });

                    var check_all_group = groups.join(",");
                    var check_all_zone = zones.join(",");
                    var uncheck_all_group = uncheck_groupa.join(",");
                    var uncheck_all_zones = uncheck_zones.join(",");

                    $.ajax({

                        type: "GET",
                        url: "{{URL::to('/')}}/json/assign_emp/group_zoon_permission",
                        data: {

                            check_all_group: check_all_group,
                            check_all_zone: check_all_zone,
                            uncheck_all_group:uncheck_all_group,
                            uncheck_all_zones:uncheck_all_zones,
                            employee_id:employee_id

                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {

                            console.log(data);

                           alert("Successfully Done...!!");


                        },
                        error: function(data) {

                            console.log(data);

                        }
                    });

                });

        });
    </script>
@endsection