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
                            <strong>All IOM</strong>
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
                            <h1>IOM</h1>
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
                                    <th>Start time</th>
                                    <th>End time</th>
                                    <th>reason</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ioms as $index => $iom)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{ $iom->employee()->id }}</td>
                                        <td>{{ $iom->employee()->user_name }}</td>
                                        <td>{{ $iom->employee()->name }}</td>
                                        <td>{{$iom->from_date}}</td>
                                        <td>{{$iom->to_date}}</td>
                                        <td>{{$iom->day_count}}</td>
                                        <td>{{$iom->start_time}}</td>
                                        <td>{{$iom->end_time}}</td>
                                        <td>{{$iom->reason}}</td>
                                        <td>

                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('iom.destroy',$iom->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $iom->status_id == 1 ? 'Active' : 'Approved'?>"
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