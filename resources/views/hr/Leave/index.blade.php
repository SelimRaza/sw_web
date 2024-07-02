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
                            <strong>All Leave</strong>
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
                            <h1>Leave</h1>
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

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>EmpId</th>
                                    <th>User name</th>
                                    <th>name</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Day Count</th>
                                    <th>Leave Type</th>
                                    <th>Is Half Day</th>
                                    <th>Start time</th>
                                    <th>End time</th>
                                    <th>reason</th>
                                    <th>Address</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($leaves as $index => $leave)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$leave->employee()->id}}</td>
                                        <td>{{$leave->employee()->user_name}}</td>
                                        <td>{{$leave->employee()->name}}</td>
                                        <td>{{$leave->from_date}}</td>
                                        <td>{{$leave->to_date}}</td>
                                        <td>{{$leave->day_count}}</td>
                                        <td>{{$leave->leaveType()->name}}</td>
                                        <td><?php echo $leave->is_half_day == 0 ? 'NO' : 'Yes'?></td>
                                        <td>{{$leave->start_time}}</td>
                                        <td>{{$leave->end_time}}</td>
                                        <td>{{$leave->reason}}</td>
                                        <td>{{$leave->address}}</td>
                                        <td>

                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('leave.destroy',$leave->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $leave->status_id == 1 ? 'Active' : 'Approved'?>"
                                                    >
                                                    </input>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection