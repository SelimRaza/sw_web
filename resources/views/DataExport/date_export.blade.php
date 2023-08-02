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
                            <strong>Generate Data</strong>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>Order Details Report</h1>
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
                                      action="{{URL::to('/data_export/dataExportOrderData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        {{-- <div class="item form-group">
                                             <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                 <span
                                                         class="required">*</span>
                                             </label>
                                             <div class="col-md-6 col-sm-6 col-xs-12">
                                                 <select class="form-control" name="acmp_id" id="acmp_id"
                                                         required>
                                                     @foreach ($acmp_data as $acmp_data1)
                                                         <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                     @endforeach
                                                 </select>
                                             </div>
                                         </div>--}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id"
                                                        required>
                                                    @foreach ($slgp_data as $slgp_data1)
                                                        <option value="{{ $slgp_data1->slgp_id }}">{{ ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")" }}</option>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>Attendance Report</h1>
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
                                      action="{{URL::to('/data_export/dataExportAttendanceData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
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
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <center><strong> ::: Non-productive SR List Report ::: </strong></center>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <form class="form-horizontal form-label-left"
                                      action="{{URL::to('/data_export/dataExportNonProductiveSRListData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id"
                                                        required>
                                                    @foreach ($slgp_data as $slgp_data1)
                                                        <option value="{{ $slgp_data1->slgp_id }}">{{ ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{--<div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>--}}

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
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>OutletSummaryReport</h1>
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
                                      action="{{URL::to('/data_export/dataExportOutletSummaryReportData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        {{-- <div class="item form-group">
                                             <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                 <span
                                                         class="required">*</span>
                                             </label>
                                             <div class="col-md-6 col-sm-6 col-xs-12">
                                                 <select class="form-control" name="acmp_id" id="acmp_id"
                                                         required>
                                                     @foreach ($acmp_data as $acmp_data1)
                                                         <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                     @endforeach
                                                 </select>
                                             </div>
                                         </div>--}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id"
                                                        required>
                                                    @foreach ($slgp_data as $slgp_data1)
                                                        <option value="{{ $slgp_data1->slgp_id }}">{{ ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone

                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="zone_id" id="zone_id">
                                                    <option value="0">Select Zone</option>
                                                    @foreach ($zone_data as $zone_data1)
                                                        <option value="{{ $zone_data1->zone_id }}">{{ ucfirst($zone_data1->zone_name)."(".$zone_data1->zone_code.")" }}</option>
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
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>OutletDetailsReport</h1>
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
                                      action="{{URL::to('/data_export/dataExportOutletDetailsReportData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        {{--<div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>--}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id"
                                                        required>
                                                    @foreach ($slgp_data as $slgp_data1)
                                                        <option value="{{ $slgp_data1->slgp_id }}">{{ ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="zone_id" id="zone_id"
                                                        required>
                                                    @foreach ($zone_data as $zone_data1)
                                                        <option value="{{ $zone_data1->zone_id }}">{{ ucfirst($zone_data1->zone_name)."(".$zone_data1->zone_code.")" }}</option>
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
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>UserInfoReport</h1>
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
                                      action="{{URL::to('/data_export/dataExportUserInfoReportData')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                       value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>GroupZoneWiseOrderSummaryReport</h1>
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
                                        action="{{URL::to('/data_export/dataExportGroupZoneWiseOrderSummaryData')}}"
                                        method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                        name="start_date"
                                                        value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                        name="end_date"
                                                        value="<?php echo date('Y-m-d'); ?>"/>
                                            </div>
                                        </div>
                                        {{--<div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>--}}
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id"
                                                        required>
                                                    @foreach ($slgp_data as $slgp_data1)
                                                        <option value="{{ $slgp_data1->slgp_id }}">{{ ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="zone_id" id="zone_id"
                                                        required>
                                                    @foreach ($zone_data as $zone_data1)
                                                        <option value="{{ $zone_data1->zone_id }}">{{ ucfirst($zone_data1->zone_name)."(".$zone_data1->zone_code.")" }}</option>
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
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h1>Newly Opened Outlet</h1>
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
                                      action="{{URL::to('/data_export/newlyOpenedOutlet')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                                Date
                                                <span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="start_date"
                                                       />
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                                Date
                                                <span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                       name="end_date"
                                                      />
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        required onchange="getGroup(this.value)">
                                                        <option value="">Select..</option>
                                                    @foreach ($acmp_data as $acmp_data1)
                                                        <option value="{{ $acmp_data1->acmp_id }}">{{ ucfirst($acmp_data1->acmp_name)."(".$acmp_data1->acmp_code.")" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="usr_opn_outlt_slgp_id"
                                                        required>
                                                   
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
    </div>
    <script>
        function getGroup(acmp_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getGroup",
                data: {
                    slgp_id: acmp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#usr_opn_outlt_slgp_id").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select..</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#usr_opn_outlt_slgp_id").append(html);

                }
            });
        }
        $(document).ready(function () {
            $('.date').datetimepicker({format: 'YYYY-MM-DD'});
            $("select").select2({width: 'resolve'});
        });
    </script>
@endsection