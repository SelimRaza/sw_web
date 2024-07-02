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
                            <strong>Manual Email</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/setting_email/create')}}">Send All</a>
                            </li>
                        @endif
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
                            <h1>Manual Email</h1>
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
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Photo</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$employee->id}}</td>
                                        <td>{{$employee->name.' ('.$employee->user_name.')'}}</td>
                                        <td>{{$employee->address}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <img src="{{ URL::to('/')}}/uploads/image_icon/{{$employee->profile_icon}}"
                                                         class="avatar" alt="Avatar">
                                                </li>

                                            </ul>
                                        </td>
                                        <td>

                                            @if($permission->wsmu_updt)
                                                <a href="{{route('setting_email.edit',$employee->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Send Email
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('employee.edit',$employee->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Employee Edit
                                                </a>
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
    <script type="text/javascript">
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection