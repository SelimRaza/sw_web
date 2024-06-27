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
                            <a href="{{ URL::to('employee/employeeUpload')}}"> Employee Upload</a>
                        </li>
                        <li class="active">
                            <strong>Generate Employee Upload Format</strong>
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
                        <strong>Error! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Generate Employee Upload Format</h1>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/employee/employeeUploadFormat')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Roles <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="role_id" id="role_id" required>
                                                <option value="">Select</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ ucfirst($role->edsg_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Roles <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="master_role_id" id="master_role_id" required>
                                                <option value="">Select</option>
                                                @foreach ($masterRoles as $masterRole)
                                                    <option value="{{ $masterRole->id }}">{{ ucfirst($masterRole->role_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manager Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="manager_id" id="manager_id" required>
                                                <option value="">Select</option>
                                                @foreach ($employees as $employee)
                                                    dd($employee);
                                                    <option value="{{ $employee->id }}">{{ $employee->aemp_usnm.' - '.ucfirst($employee->aemp_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manager
                                            Name
                                            <span
                                                    class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="line_manager_id" id="line_manager_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->aemp_usnm.' - '.ucfirst($employee->aemp_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Submit</button>
                                        </div>
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
        $("#manager_id").select2({width: 'resolve'});
        $("#line_manager_id").select2({width: 'resolve'});
        $("#role_id").select2({width: 'resolve'});
        $("#master_role_id").select2({width: 'resolve'});
    </script>
@endsection