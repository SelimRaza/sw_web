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
                            <strong>Show Employee</strong>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <form class="form-horizontal form-label-left"
                                      action="{{route('employee.update',$employee->id)}}"
                                      method="post">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    {{method_field('PUT')}}

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Designation
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="{{$employee->role_name}}"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manger <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="{{$employee->manager_name}}"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manger
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="{{$employee->line_manager_name}}"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="{{$employee->email}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Full Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="name"
                                                   value="{{$employee->name}}"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="ln_name"
                                                   value="{{$employee->ln_name}}"
                                                   placeholder="Ln Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="address"
                                                   value="{{$employee->address}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email CC
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
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
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="<?php echo $employee->auto_email == "1" ? "Yes" : "No" ?>"
                                                   name="auto_email" type="text"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Live
                                            Location
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="<?php echo $employee->location_on == "1" ? "Yes" : "No" ?>"
                                                   name="auto_email" type="text"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="mobile"
                                                   value="{{$employee->mobile}}"
                                                   placeholder="Mobile" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Allowed
                                            Distance <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="allowed_distance" value="{{$employee->allowed_distance}}"
                                                   placeholder="Allowed Distance" required="required" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Company </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th> Company Name</th>
                                        <th> Company Code</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    @foreach ($companyMapping as $companyMapping1)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$companyMapping1->acmp_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$companyMapping1->acmp_code}}" readonly>
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Group </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Group Name</th>
                                        <th>Group code</th>
                                        <th>Price List</th>
                                        <th>Price Code</th>
                                        <th>Zone Name</th>
                                        <th>Zone Code</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    @foreach ($salesGroupMapping as $salesGroupMapping1)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->slgp_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->slgp_code}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->plmt_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->plmt_code}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->zone_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$salesGroupMapping1->zone_code}}" readonly>
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Dealer </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Depot Name</th>
                                        <th>Depot Code</th>
                                        <th>Company name</th>
                                        <th>Base name</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    @foreach ($depotMapping as $depotMapping1)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$depotMapping1->dlrm_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$depotMapping1->dlrm_code}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$depotMapping1->acmp_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$depotMapping1->base_name}}" readonly>
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Route </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Day</th>
                                        <th>Route Name</th>
                                        <th>Route code</th>
                                        <th>Base Name</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    @foreach ($routePlanMapping as $routePlanMapping1)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$routePlanMapping1->rpln_day}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$routePlanMapping1->rout_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$routePlanMapping1->rout_code}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$routePlanMapping1->base_name}}" readonly>
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Zone Group Supervisor Mapping</h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Group Name</th>
                                        <th>Group Code</th>
                                        <th>Route Name</th>
                                        <th>Route Code</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    @foreach ($zoneGroupMapping as $zoneGroupMapping1)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$zoneGroupMapping1->slgp_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$zoneGroupMapping1->slgp_code}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$zoneGroupMapping1->zone_name}}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{$zoneGroupMapping1->zone_code}}" readonly>
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
    </div>
@endsection