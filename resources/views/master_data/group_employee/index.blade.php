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
                            <strong>All Employee</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/groupEmployee/create')}}"> New Employee </a>
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
                            <h1>Employee</h1>
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
                                    <th>Emp Id</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>User Name</th>
                                    <th>Role</th>
                                    <th>Manager</th>
                                    <th>Line manager</th>
                                    <th>Group</th>
                                    <th>Icon</th>
                                    <th style="width: 30%"> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$employee->id}}</td>
                                        <td>{{$employee->name}}</td>
                                        <td>{{$employee->mobile}}</td>
                                        <td>{{$employee->address}}</td>
                                        <td>{{$employee->user_name}}</td>
                                        <td>{{$employee->role}}</td>
                                        <td>{{$employee->manager_name}}</td>
                                        <td>{{$employee->line_manager_name}}</td>
                                        <td>{{$employee->group_name}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <img src="{{ URL::to('/')}}/uploads/image_icon/{{$employee->profile_icon}}"
                                                         class="avatar" alt="Avatar">
                                                </li>

                                            </ul>
                                        </td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('groupEmployee.show',$employee->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('groupEmployee.edit',$employee->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('groupEmployee.destroy',$employee->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $employee->status_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="groupEmployee/{{$employee->id}}/passChange"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Password
                                                    Change
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <form style="display:inline"
                                                      action="groupEmployee/{{$employee->id}}/reset"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field("PUT")}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Pass Reset"
                                                           onclick="return ConfirmReset()">
                                                    </input>
                                                </form>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- end project list -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection