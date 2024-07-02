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
                            <a href="{{ URL::to('/groupEmployee')}}"> All Employee </a>
                        </li>
                        <li class="active">
                            <strong>Edit Employee</strong>
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
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Employee </h1>
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

                            <form class="form-horizontal form-label-left"
                                  action="{{route('groupEmployee.update',$employee->id)}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                {{method_field('PUT')}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Roles <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="role_id" id="role_id" required>
                                            <option value="{{$employee->role()->id}}">{{$employee->role()->name}}</option>
                                            @foreach ($userRoles as $role)
                                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Level <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="master_role_id" id="master_role_id" required>
                                            <option value="{{$employee->masterRole()->id}}">{{$employee->masterRole()->name}}</option>
                                            @foreach ($masterRoles as $masterRole)
                                                @if($employee->masterRole()->id!=$masterRole->id)
                                                    <option value="{{ $masterRole->id }}">{{ ucfirst($masterRole->name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manger <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="manager_id" id="manager_id" required>
                                            <option value="{{$employee->manager()->id}}">{{$employee->manager()->user()->email.' - '.$employee->manager()->name}}</option>
                                            @foreach ($employee_all as $employee1)
                                                @if($employee->manager()->id!=$employee1->id )
                                                    <option value="{{ $employee1->id }}">{{ ucfirst($employee1->user()->email.' - '.$employee1->name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manger <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="line_manager_id" id="line_manager_id" required>
                                            <option value="{{$employee->lineManager()->id}}">{{$employee->lineManager()->user()->email.' - '.$employee->lineManager()->name}}</option>
                                            @foreach ($employee_all as $employee1)
                                                @if($employee->lineManager()->id!=$employee1->id)
                                                <option value="{{ $employee1->id }}">{{ ucfirst($employee1->user()->email.' - '.$employee1->name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="email"
                                               value="{{$employee->user()->email}}"
                                               placeholder="Code" required="required" type="text">
                                    </div>
                                </div>


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Full Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name"
                                               value="{{$employee->name}}"
                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                                name="ln_name"
                                               value="{{$employee->ln_name}}"
                                               placeholder="Ln Name"  type="text">
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                              name="address"
                                               value="{{$employee->address}}"
                                               placeholder="Email"  type="email">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email CC
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="email_cc"
                                               value="{{$employee->email_cc}}"
                                               placeholder="email1@exmple.com,email2@exmple.com" type="text"
                                               step="any">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Auto Email
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input <?php echo $employee->auto_email=="4"? "checked" :"" ?> id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="auto_email" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Live Track
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input <?php echo $employee->location_on=="4"? "checked" :"" ?> id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="location_on" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                                name="mobile"
                                               value="{{$employee->mobile}}"
                                               placeholder="Mobile"  type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Allowed Distance <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               name="allowed_distance"  value="{{$employee->allowed_distance}}"
                                               placeholder="Allowed Distance" required="required" type="number" step="any">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"> Profile Image <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="input_img"
                                               placeholder="Image" type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success"> Submit </button>
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
        $("#role_id").select2({ width: 'resolve' });
        $("#division_id").select2({ width: 'resolve' });
        $("#region_id").select2({ width: 'resolve' });
        $("#zone_id").select2({ width: 'resolve' });
        $("#company_id").select2({ width: 'resolve' });
        $("#manager_id").select2({ width: 'resolve' });
        $("#line_manager_id").select2({ width: 'resolve' });
        $("#master_role_id").select2({width: 'resolve'});

    </script>
@endsection