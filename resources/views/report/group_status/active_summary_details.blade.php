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
                            <strong>Active Employee Details</strong>
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
                            <h1>Active Employee Details </h1>

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
                                    <th>S/L</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Group name</th>
                                    <th>Role</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>total Day</th>
                                    <th>Attendance Day</th>
                                    <th>Total Note Count</th>
                                    <th>Total Holiday</th>
                                    <th>Absence</th>

                                </tr>
                                </thead>
                                <tbody id="cont">
                                @foreach($att_data as $index => $att_data1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$att_data1->start_date}}</td>
                                        <td>{{$att_data1->end_date}}</td>
                                        <td>{{$att_data1->name}}</td>
                                        <td>{{$att_data1->role_name}}</td>
                                        <td>{{$att_data1->user_name}}</td>
                                        <td>{{$att_data1->emp_name}}</td>
                                        <td>{{$att_data1->date_count}}</td>
                                        <td>{{$att_data1->att_day}}</td>
                                        <td>{{$att_data1->note_count}}</td>
                                        <td>{{$att_data1->holiday}}</td>
                                        <td>{{$att_data1->date_count-$att_data1->att_day-$att_data1->holiday}}</td>
                                        <td></td>

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