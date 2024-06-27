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
                            <a class="label-success" href="{{ URL::to('/employee')}}">All Employee</a>
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
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{route('employee.store')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Designation <span
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
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Role <span
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
                                </div> -->
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Designation <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="master_role_id" id="master_role_id" required>
                                            <option value="">Select</option>
                                            @foreach ($masterRoles as $masterRole)
                                                <option value="{{ $masterRole->id }}">{{ ucfirst($masterRole->role_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Role <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="role_id" id="role_id" required>
                                            <option value="">Select</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ ucfirst($role->edsg_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>




                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manager ID
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="manager_id" value="{{old('manager_id')}}"
                                               placeholder="user_name" required="required" type="text">
                                       {{-- <select class="form-control" name="manager_id" id="manager_id" required>
                                            <option value="">Select</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ ucfirst($employee->aemp_usnm.' - '.$employee->aemp_name) }}</option>
                                            @endforeach
                                        </select>--}}
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manager
                                        ID
                                        <span
                                                class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="{{old('line_manager_id')}}"
                                               placeholder="user_name" required="required" type="text">
                                       {{-- <select class="form-control" name="line_manager_id" id="line_manager_id"
                                                required>
                                            <option value="">Select</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ ucfirst($employee->aemp_usnm.'-'.$employee->aemp_name) }}</option>
                                            @endforeach
                                        </select>--}}
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-6 col-xs-12 col-md-offset-1">
                                            <label class="control-label" for="name">Manager ID <span class="required">*</span></label>
                                            <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="manager_id" value="{{ old('manager_id') }}" placeholder="User Name" required="required" type="text">
                                        </div>

                                        <div class="col-md-5 col-sm-6 col-xs-12">
                                            <label class="control-label" for="name">Line Manager ID <span class="required">*</span></label>
                                            <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="{{ old('line_manager_id') }}" placeholder="User Name" required="required" type="text">
                                        </div>
                                    </div>
                                </div> -->

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Manager ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="manager_id" value="{{ old('manager_id') }}" placeholder="User Name" required="required" type="text">

                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Line Manager ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="{{ old('line_manager_id') }}" placeholder="User Name" required="required" type="text">

                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Full Name <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name" value="{{old('name')}}"
                                               placeholder="Full Name" required="required" type="text">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Last Name <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ln_name" value="{{old('ln_name')}}"
                                               placeholder="Ln Name" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">User ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="email" value="{{old('email')}}"
                                               placeholder="User Name" required="required" type="text">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Email <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="address" value="{{old('address')}}"
                                               placeholder="email@eacmple.com" type="email">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Email CC <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="email_cc"
                                               placeholder="email1@exmple.com,email2@exmple.com" type="text" value="{{old('email_cc')}}"
                                               step="any">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Mobile <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="mobile" value="{{old('mobile')}}"
                                               placeholder="Mobile" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Menu Group <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="amng_id" id="amng_id" required>
                                            <option value="">Select</option>
                                            @foreach ($appMenuGroup as $appMenuGroup1)
                                                <option value="{{ $appMenuGroup1->id }}">{{ ucfirst($appMenuGroup1->amng_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Profile Image <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="input_img" value="{{old('input_img')}}"
                                               placeholder="Image" type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Allowed Distance <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="allowed_distance" value="0"
                                               placeholder="Allowed Distance" required="required" type="number" value="{{old('allowed_distance')}}"
                                               step="any">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Outlet Code <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="outlet_code" name="outlet_code" class="form-control col-md-7 col-xs-12"
                                                data-validate-length-range="6" data-validate-words="2"  value="{{old('outlet_code')}}"
                                                placeholder="Outlet Code"  type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Credit Limit <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_crdt" value="0"
                                               placeholder="Amount" required="required" type="number" value="{{old('aemp_crdt')}}"
                                               step="any">
                                    </div>
                                    @if(Auth::user()->country()->module_type==2)
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Nationality <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="cont_id" id="cont_id" required>
                                            <option value="">Select</option>
                                            @foreach ($country as $cnt)
                                                <option value="{{ $cnt->id }}">{{ ucfirst($cnt->cont_code.'-'.$cnt->cont_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                                @if(Auth::user()->country()->module_type==2)
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Visa Number <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12" name="visa_no"
                                               placeholder="Visa Number" type="text"
                                               step="1">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Expiry Date <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                                                value="<?php echo date('Y-m-d');?>"
                                               step="1">
                                    </div>
                                </div>
                                @endif
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Auto Email <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"  {{ old('auto_email') == 'on' ? 'checked' : '' }}
                                               name="auto_email" type="checkbox"
                                        >
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Live Location <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"  {{ old('location_on') == 'on' ? 'checked' : '' }}
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="location_on" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales Person <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               {{ old('aemp_issl') == 'on' ? 'checked' : '' }} data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_issl" type="checkbox"
                                        >
                                    </div>
                                   
                                </div>

                                    
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User ID <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="email" value="{{old('email')}}"
                                               placeholder="User Name" required="required" type="text">
                                    </div>
                                </div> -->
                                

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Full Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name" value="{{old('name')}}"
                                               placeholder="Full Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ln_name" value="{{old('ln_name')}}"
                                               placeholder="Ln Name" type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="address" value="{{old('address')}}"
                                               placeholder="email@eacmple.com" type="email">
                                    </div>
                                </div> -->


                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email CC

                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="email_cc"
                                               placeholder="email1@exmple.com,email2@exmple.com" type="text" value="{{old('email_cc')}}"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Auto Email
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"  {{ old('auto_email') == 'on' ? 'checked' : '' }}
                                               name="auto_email" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Live Location
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"  {{ old('location_on') == 'on' ? 'checked' : '' }}
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="location_on" type="checkbox"
                                        >
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Is Sales Person
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               {{ old('aemp_issl') == 'on' ? 'checked' : '' }} data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_issl" type="checkbox"
                                        >
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="mobile" value="{{old('mobile')}}"
                                               placeholder="Mobile" type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Allowed Distance
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="allowed_distance" value="0"
                                               placeholder="Allowed Distance" required="required" type="number" value="{{old('allowed_distance')}}"
                                               step="any">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Customer id
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="site_id" value="0"
                                               placeholder="Site Id" required="required" type="number" value="{{old('site_id')}}"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="outlet_code" name="outlet_code" class="form-control col-md-7 col-xs-12"
                                                data-validate-length-range="6" data-validate-words="2"  value="{{old('outlet_code')}}"
                                                placeholder="Outlet Code"  type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Personal Credit Limit
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_crdt" value="0"
                                               placeholder="Amount" required="required" type="number" value="{{old('aemp_crdt')}}"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Profile
                                        Image<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="input_img" value="{{old('input_img')}}"
                                               placeholder="Image" type="file"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">App Menu Group <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="amng_id" id="amng_id" required>
                                            <option value="">Select</option>
                                            @foreach ($appMenuGroup as $appMenuGroup1)
                                                <option value="{{ $appMenuGroup1->id }}">{{ ucfirst($appMenuGroup1->amng_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> -->
                                <!-- @if(Auth::user()->country()->module_type==2) -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Nationality<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="cont_id" id="cont_id" required>
                                            <option value="">Select</option>
                                            @foreach ($country as $cnt)
                                                <option value="{{ $cnt->id }}">{{ ucfirst($cnt->cont_code.'-'.$cnt->cont_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Visa No
                                        <span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12" name="visa_no"
                                               placeholder="Visa Number" type="text"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Expiry Date
                                        <span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                                                value="<?php echo date('Y-m-d');?>"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- @endif -->
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success"> Submit</button>
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

        $(document).ready(function() {
            $("#role_id").select2({width: 'resolve'});
            $("#master_role_id").select2({width: 'resolve'});
            $("#amng_id").select2({width: 'resolve'});
            $("#cont_id").select2({width: 'resolve'});
            const role_id = '{{ old('role_id') }}';
            const master_role_id = '{{ old('master_role_id') }}';
            const amng_id = '{{ old('amng_id') }}';
            if(role_id !== '') {
                $('#role_id').val(role_id);
            }
            if(master_role_id !== '') {
                $('#master_role_id').val(master_role_id);
            }
            if(amng_id !== '') {
                $('#amng_id').val(amng_id);
            }
            $('#expr_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '0d',               
                    autoclose: 1,
                    showOnFocus: true
            });
        });


    </script>
@endsection