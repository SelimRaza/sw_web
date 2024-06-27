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
                            <strong>All Attendance Details</strong>
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
                            <h1>Attendance Summary Details</h1>
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
                                    <th>Date</th>
                                    <th>Address</th>
                                    <th>Type</th>
                                    <th>Time</th>
                                    <th>Image</th>

                                </tr>
                                </thead>
                                <tbody id="cont">
                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{$attendance->date}}</td>
                                        <td>{{$attendance->address}}</td>
                                        <td>{{$attendance->attendance_type}}</td>
                                        <td>{{$attendance->time}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <a target="_blank" href="{{ URL::to('/')}}/uploads/{{$attendance->image}}"
                                                       class="btn btn-info btn-xs"><img src="{{ URL::to('/')}}/uploads/{{$attendance->image}}"
                                                                                                                    class="avatar" >
                                                    </a>

                                                </li>

                                            </ul>
                                        </td>
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