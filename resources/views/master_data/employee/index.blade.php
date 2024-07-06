@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>All Employee</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/employee')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{$search_text}}">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Go!</button>
                                </span>

                            </div>
                        </div>
                    </div>
                </form>
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
                            @if($permission->wsmu_crat)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/employee/create')}}">Add New</a>
                                <a class="btn btn-success btn-sm"
                                   href="{{ URL::to('employee/employeeUpload')}}">Upload</a>

                                <a class="btn btn-success btn-sm"
                                   href="{{ URL::to('get/employee/routeSearch/view')}}">Search Route</a>
                                <a class="btn btn-success btn-sm"
                                   href="{{ URL::to('employee/get/routeLike/view')}}">Route Like</a>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <div class="x_content">
                            <!-- {{$employees->appends(Request::only('search_text'))->links()}} -->

                            <table  class="table" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL


                                    </th>
                                    <th>Emp Id</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>manager</th>
                                    <th>Line manager</th>
                                    <th>Sales Group</th>
                                    <th>Company</th>
                                    <th>Icon</th>
                                    <th>App Menu</th>
                                    <th>Own Site</th>
                                    <th>IDate</th>
                                    <th>EDate</th>
                                    <th style="width: 30%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$employee->id}}</td>
                                        <td>{{$employee->aemp_usnm}}</td>
                                        <td>{{$employee->aemp_name}}</td>
                                        <td>{{$employee->aemp_mob1}}</td>
                                        <td>{{$employee->aemp_emal}}</td>
                                        <td>{{$employee->role_name}}</td>
                                        <td>{{$employee->edsg_name}}</td>
                                        <td>{{$employee->mnrg_name}}</td>
                                        <td>{{$employee->lmid_name}}</td>
                                        <td>{{$employee->slgp_name}}</td>
                                        <td>{{$employee->acmp_name}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    @if($employee->aemp_picn!='')
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/{{$employee->aemp_picn}}"
                                                             class="avatar" alt="Avatar">
                                                    @endif
                                                </li>

                                            </ul>
                                        </td>
                                        <td>{{$employee->amng_name}}</td>
                                        <td>{{$employee->site_code}}</td>
                                        <td>{{$employee->created_at}}</td>
                                        <td>{{$employee->updated_at}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('employee.show',$employee->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('employee.edit',$employee->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <!-- <form style="display:inline"
                                                      action="{{route('employee.destroy',$employee->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $employee->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form> -->
                                               
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <form style="display:inline"
                                                      action="employee/{{$employee->id}}/reset"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field("PUT")}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Pass Reset"
                                                           onclick="return ConfirmReset()">
                                                    </input>
                                                </form>
                                            @endif

                                           {{$employee->lfcl_id == 1 ? 'Active' : 'Inactive'}}

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