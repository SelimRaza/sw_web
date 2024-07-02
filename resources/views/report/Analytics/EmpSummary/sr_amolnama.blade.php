@extends('theme.app')
@push('header')
    <script src="{{ asset("theme/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}"></script>
@endpush
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="active">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <strong>SR Amolnama</strong>
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


                        <div class="x_panel">
                            <div class="x_title card-icon" style="float: right">
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </div>

                            <div class="x_content">
                                <form class="form-horizontal form-label-left"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}

                                    {{-- Report Filter--}}
                                    <div class="x_title">
                                        <div class="item form-group">


                                            <div class="col-md-3 col-sm-3 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="start_date" style="text-align: left">Start Date<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg start_date" name="start_date"
                                                           id="start_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-6 ">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="end_date" style="text-align: left">End Date<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg end_date" name="end_date"
                                                           id="end_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>"/>                                            </div>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="name" style="text-align: left">SV ID<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" name="sv_id" class="form-control in_tg" id="sv_id">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-6 ">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                       for="name" style="text-align: left">SR ID<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" name="sr_id" class="form-control in_tg" id="sr_id">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="display: flex; justify-content: end">
                                                <button id="send" type="button" style="margin-right:10px;"
                                                        class="btn btn-success"
                                                        onclick="getSrAmolnamaData()"><span class="fa fa-search"
                                                                                            style="color: white;"></span>
                                                    <b>Search</b>
                                                </button>
                                            </div>

                                        </div>


                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>


                        </div>

                        {{-- SR Amolnama Informations --}}
                        <div class="row" id="hourly-activity-line-chart">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title" style="display: flex; justify-content: center">
                                        <h2>SR Amolnama</h2>

                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content"  style="display: flex; flex-direction: column; align-items: center; padding:0; margin: 0">

                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db;
                                                margin: 30px;
                                                padding-top: 25px;
                                                padding-right: 10px;
                                                border-radius: 15px;">

                                            <div class="col-md-12 col-sm-12 col-xs-12 margin-grid row" id="sr-info" style="padding: 0; padding-right: 10px">


                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="bu" style="text-align: left">BU
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="bu" disabled
                                                               id="bu" autocomplete="off" value="BU"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="staff-id" style="text-align: left">Staff ID
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg " name="staff_id" disabled
                                                               id="staff-id" autocomplete="off" value="Staff ID"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="group" style="text-align: left">Group
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg " name="group" disabled
                                                               id="group" value="Group" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="name" style="text-align: left">Name
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg " name="name" disabled
                                                               id="name" autocomplete="off" value="Name"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="zone" style="text-align: left">Zone
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg " name="zone" disabled
                                                               id="zone" autocomplete="off" value="Zone"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="mobile" style="text-align: left">Mobile
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg " name="mobile" disabled
                                                               id="mobile" autocomplete="off" value="Mobile"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db; margin: 30px; padding-top: 25px; padding-right: 10px; border-radius: 15px;">

                                            <div class="col-md-12 col-sm-12 col-xs-12 margin-grid row" id="sr-info" style="padding: 0; padding-right: 30px">

                                                <table class="table font_color" data-page-length="50">
                                                    <thead id="employee-wise-details">
                                                    <tr class="tbl_header_light">
                                                        <th class="cell_left_border">Day</th>
                                                        <th>Route Name</th>
                                                        <th>T.Olt</th>
    {{--                                                    <th>Day</th>--}}
    {{--                                                    <th>Route Name</th>--}}
    {{--                                                    <th>T.Olt</th>--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody id="sr-route-info">
    {{--                                                    <tr class="tbl_body_gray">--}}
    {{--                                                        <td> Sat </td>--}}
    {{--                                                        <td id="sat-rout-code"> </td>--}}
    {{--                                                        <td id="sat-olt"> </td>--}}
    {{--                                                        <td> Tus </td>--}}
    {{--                                                        <td id="tus-rout-code"> </td>--}}
    {{--                                                        <td id="tus-olt"> </td>--}}
    {{--                                                    </tr>--}}
    {{--                                                    <tr class="tbl_body_gray">--}}
    {{--                                                        <td> Sun </td>--}}
    {{--                                                        <td id="sun-rout-code"> </td>--}}
    {{--                                                        <td id="sun-olt"> </td>--}}
    {{--                                                        <td> Wed </td>--}}
    {{--                                                        <td id="wed-rout-code"> </td>--}}
    {{--                                                        <td id="wed-olt"> </td>--}}
    {{--                                                    </tr>--}}
    {{--                                                    <tr class="tbl_body_gray">--}}
    {{--                                                        <td> Mon </td>--}}
    {{--                                                        <td id="mon-rout-code"> </td>--}}
    {{--                                                        <td id="mon-olt"> </td>--}}
    {{--                                                        <td> Thu </td>--}}
    {{--                                                        <td id="thu-rout-code"> </td>--}}
    {{--                                                        <td id="thu-olt"> </td>--}}
    {{--                                                    </tr>--}}
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12 margin-grid row"  id="hierarchy-info" style="padding: 0; padding-right: 30px">

                                            <table class="table font_color" data-page-length="50">
                                                <thead id="employee-wise-details">
                                                <tr class="tbl_header_light">
                                                    <th class="cell_left_border">Particular</th>
                                                    <th>Thana</th>
                                                    <th>Union</th>
                                                    <th>Olt</th>
{{--                                                    <th>Ach%</th>--}}
                                                </tr>
                                                </thead>
                                                <tbody id="sr-hierarchy-info">
                                                <tr class="tbl_body_gray">
                                                    <td> Assign </td>
                                                    <td id="assigned-thana"> </td>
                                                    <td id="assigned-union"> </td>
                                                    <td id="assigned-olt"> </td>
{{--                                                    <td id="assigned-ach"> </td>--}}
                                                </tr>
                                                <tr class="tbl_body_gray">

                                                    <td> Visited </td>
                                                    <td id="visited-thana"> </td>
                                                    <td id="visited-union"> </td>
                                                    <td id="visited-olt"> </td>
{{--                                                    <td id="visited-ach"> </td>--}}

                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        </div>

                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db; margin: 30px; padding-top: 25px; padding-right: 10px; border-radius: 15px;">

                                            <div class="col-md-12 col-sm-12 col-xs-12 margin-grid row" id="order-info" style="margin-left: 0.5rem; padding: 0; padding-right: 10px">
                                            <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                       for="t-sku" style="text-align: left">T.Sku
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-8">
                                                    <input type="text" class="form-control in_tg " name="t_sku" disabled
                                                           id="t-sku" autocomplete="off" value="T.Sku"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                       for="category" style="text-align: left">Category
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg " name="category" disabled
                                                           id="category" autocomplete="off" value="Category"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                       for="order-sku" style="text-align: left">Order Sku
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg " name="order_sku" disabled
                                                           id="order-sku" value="Order Sku" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                       for="order-category" style="text-align: left">Order Category
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg " name="order_category" disabled
                                                           id="order-category" autocomplete="off" value="Order Category"/>
                                                </div>
                                            </div>
                                        </div>
                                        </div>


                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db; margin: 30px; padding-top: 25px; padding-right: 10px; border-radius: 15px;">
                                            <div class="col-md-12 col-sm-12 col-xs-12 margin-grid row" id="sr-info" style="margin-left: 0.5rem; padding: 0; padding-right: 10px">
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="route-olt" style="text-align: left">Route Olt
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="route_olt" disabled
                                                               id="route-olt" autocomplete="off" value="Route Olt"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4"
                                                           for="target" style="text-align: left">Target
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="target" disabled
                                                               id="target" autocomplete="off" value="Target"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="visit-olt" style="text-align: left">Visit Olt
                                                    </label>
                                                    <div  class="col-md-8 col-sm-8 col-xs-8 input-icons" style="margin-bottom: 0px;">
                                                        <i class="fa fa-eye fa-2x icon" onclick="sr_day_wise_visit_outlet()" style="float:right;"></i>
                                                        <input type="text" class="form-control in_tg input-field" name="visit_olt" disabled
                                                               id="visit-olt" value="Visit Olt" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4"
                                                           for="exp" style="text-align: left">Exp
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="exp" disabled
                                                               id="exp" autocomplete="off" value="Exp"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="s-olt" style="text-align: left">S.Olt
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="s_olt" disabled
                                                               id="s-olt" autocomplete="off" value="S.Olt"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4"
                                                           for="delivery" style="text-align: left">Delivery
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="delivery" disabled
                                                               id="delivery" autocomplete="off" value="Delivery"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="s_rate" style="text-align: left">S.Rate
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="s_rate" disabled
                                                               id="s-rate" autocomplete="off" value="S.Rate"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4"
                                                           for="p-sales" style="text-align: left">P.Sales
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="p_sales" disabled
                                                               id="p-sales" autocomplete="off" value="P.Sales"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row row">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4 text_left"
                                                           for="lpc" style="text-align: left">LPC
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                                        <input type="text" class="form-control in_tg " name="lpc" disabled
                                                               id="lpc" autocomplete="off" value="LPC"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 info-row text-right row" >
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-4"
                                                           for="old-category" style="text-align: left">Olt Category
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-8 input-icons"  style="margin-bottom: 0px;">
                                                        <i class="fa fa-eye fa-2x icon" onclick="showCategoryWiseVisit()" style="float:right;"></i>
                                                        <input type="text" class="form-control in_tg input-field" name="old_category" disabled
                                                               id="old-category" autocomplete="off" value="Olt Category"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- Hourly Activities line chart --}}
                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db; margin: 30px; padding-top: 25px; padding-right: 10px; border-radius: 15px;">
                                            <h2  style="text-align: center;" id="hourly-activities-or-note-sammary">Hourly Activities</h2>
                                            <div class="x_content">
                                                <div id="visit_vs_productive_non_productive_bar" style="height:350px;"></div>
                                            </div>
                                        </div>

                                        {{-- Attendance Summary --}}
                                        <div class="col-md-8 col-sm-8 col-xs-8" style="border: 1px solid #1291db; margin: 30px; padding-top: 25px; padding-right: 10px; border-radius: 15px;">
                                            <h2 style="text-align: center;">Attendance Summary</h2>

                                            <div class="x_content">

                                                <table id="attendance-summary-pie-chart" style="width:100%">
                                                    <tr>
                                                        <th style="width:60%;">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div id="echart_pie_t" style="height:350px;"></div>
                                                        </td>
                                                        <td>
                                                            <table class="tile_info">

                                                                <th>
                                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                        <p class="">Type</p>
                                                                    </div>
                                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                                                        <p class="">Days</p>
                                                                    </div>
                                                                </th>
                                                                <tr>
                                                                    <td>
                                                                        <p>Total Days</p>
                                                                    </td>
                                                                    <td id="total-days"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p><i class="fa fa-square" style="color:#006A4E"></i>Present</p>
                                                                    </td>
                                                                    <td id="total-present"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p><i class="fa fa-square" style="color:#34495E"></i>IOM</p>
                                                                    </td>
                                                                    <td id="total-iom"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p><i class="fa fa-square" style="color:#BDC3C7"></i>Leave</p>
                                                                    </td>
                                                                    <td id="total-leave"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p><i class="fa fa-square" style="color:#3498DB"></i>Force Leave</p>
                                                                    </td>
                                                                    <td id="total-fc_leave"></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>


                                                <table id="attendance-summary-table" class="table font_color" data-page-length="50" style="display: none">
                                                    <thead>
                                                    <tr class="tbl_header_light">
                                                        <th class="cell_left_border">Name</th>
                                                        <th>Present</th>
                                                        <th>IOM</th>
                                                        <th>Leave</th>
                                                        <th>Force Leave</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="attendance-summary-details">
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                        <div class="modal fade" id="myModalVisitedOutletDetails" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title text-center">Visited Outlet Details</h4>
                                                    </div>
                                                    <div class="modal-body"
                                                         style="overflow-y: scroll; height: 45vh;">
                                                        <div class="loader" id="visit_out_load_details" style="display:none; margin-left:35%;"></div>
                                                        <table class="table font_color" data-page-length="50">
                                                            <thead id="employee-wise-details">
                                                            <tr class="tbl_header_light">
                                                                <th class="cell_left_border">Day with Date</th>
                                                                <th>T.Olt</th>
                                                                <th>Ro.Olt</th>
                                                                <th>Wr.Olt</th>
{{--                                                                <th>Out List</th>--}}
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="visited-outlet-details-info">


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="modal fade" id="myModalCategoryWiseVisit" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title text-center">Category Wise Visit</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="loader" id="visit_out_load_details" style="display:none; margin-left:35%;"></div>
                                                        <table class="table font_color" data-page-length="50">
                                                            <thead id="employee-wise-details">
                                                            <tr class="tbl_header_light">
                                                                <th class="cell_left_border">Sl</th>
                                                                <th>Category</th>
                                                                <th>Visit</th>
                                                                <th>View</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="category-wise-visit-info">


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="modal fade" id="myModalCategoryWiseVisitView" role="dialog">
                                            <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title text-center">Category Wise Visit Details</h4>
                                                    </div>
                                                    <div class="modal-body category-wise-visit-details-modal"
                                                         style="overflow-y: scroll; height: 45vh;">
                                                        <div class="loader" style="display:none; margin-left:35%;"></div>
                                                        <table id="category-wise-visit-details" class="table font_color table-responsive" data-page-length="50">
                                                            <thead id="category-wise-visit-details-head">
                                                            <tr class="tbl_header_light">
                                                                <th class="cell_left_border">Site Code</th>
                                                                <th>Site Name</th>
                                                                <th>Site Mobile</th>
                                                                <th>Frequency</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="category-wise-visit-details-info">


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="modal fade" id="myModalOutletVisitMap" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title text-center">Category Wise Visit</h4>
                                                        <p class="modal-title text-center">
                                                            Productive Visit:<img src="{{asset('theme/image/map_icon_all/pv.png')}}">&nbsp;&nbsp;&nbsp;&nbsp;
                                                            ||&nbsp;&nbsp;&nbsp;&nbsp;Non.Productive Visit:<img src="{{asset('theme/image/map_icon_all/npv.png')}}">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            ||&nbsp;&nbsp;&nbsp;&nbsp;Start: <img src="{{asset('theme/image/map_icon_all/start.png')}}">
                                                            ||&nbsp;&nbsp;&nbsp;&nbsp;End: <img src="{{asset('theme/image/map_icon_all/end.png')}}">
                                                        </p>
                                                    </div>
                                                    <div class="modal-body" id="sr_amolnama_map" style="height:500px;overflow:hidden;">
                                                        <div class="loader" id="visit_out_load_details" style="display:none; margin-left:35%;"></div>
                                                       
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>



                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <script src="{{ asset("theme/vendors/Chart.js/dist/Chart.min.js")}}"></script>
    <script src="{{asset("theme/vendors/echarts/dist/echarts.min.js")}}"></script>

    <style type="text/css">
        @media (min-width: 768px) {
            .modal-xl {
                width: 90%;
                max-width:1200px;
            }
        }


        /*icon inside input field*/


        .fa-info-circle:hover{
            cursor: pointer;
        }


        .fa-map-marker:hover{
            cursor: pointer;
        }

        .input-icons i {
            position: absolute;
            right: 4px;
        }

        .input-icons {
            margin-bottom: 10px;
        }

        .icon {
            padding-top: 5px;
            min-width: 50px;
            text-align: center;
        }

        .icon:hover {
            cursor: pointer;
        }

        .input-field {
            padding: 10px;
        }


        .info-row {
            margin-bottom: 1rem;
            padding-left: 0;
            padding-right: 0;
        }



        .text-right{
            float: right;
        }

        .margin-grid{
            margin-left: 1.50rem;
            margin-right: 0;
            margin-bottom: 1rem;
            margin-top: 1rem;
        }


        .thumbnail {
            -webkit-box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
            box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
            transition: 0.3s;
            min-width: 40%;
            border-radius: 5px;
            flex-direction: column;
            display: flex;
            text-align: center;
        }

        .thumbnail-description {
            min-height: 40px;
        }

        .thumbnail:hover {
            cursor: pointer;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 1);
        }

        .count{
            cursor:pointer;
            font-size: 38px;
            font-weight: 600;
        }

        .count_top{
            font-size: 15px;
            font-weight: 100;
        }

        .count_bottom{
            color: #1d68a7;
            margin-top: -5px;
        }

        .summery-nav{
            margin-left: -25px !important;
        }

        #exTab1 .nav-pills > li.active>a{
            margin-left: 0px !important;
        }

        .x_title{
            border-bottom: 0 !important;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
            font-size: smaller !important;
            padding-bottom: 0 !important;
        }

        .tbl_body_gray{
            height: 1rem !important;
        }

        .fa-chevron-up:hover{
            cursor: pointer;
        }
    </style>

    <script type="text/javascript">

        let design = {
            color: [
                '#006A4E', '#34495E', '#BDC3C7', '#3498DB'
            ],

            title: {
                itemGap: 8,
                textStyle: {
                    fontWeight: 'normal',
                    color: '#408829'
                }
            },

            dataRange: {
                color: ['#1f610a', '#97b58d']
            },

            toolbox: {
                color: ['#408829', '#408829', '#408829', '#408829']
            },

            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.5)',
                axisPointer: {
                    type: 'line',
                    lineStyle: {
                        color: '#408829',
                        type: 'dashed'
                    },
                    crossStyle: {
                        color: '#408829'
                    },
                    shadowStyle: {
                        color: 'rgba(200,200,200,0.3)'
                    }
                }
            },

            dataZoom: {
                dataBackgroundColor: '#eee',
                fillerColor: 'rgba(64,136,41,0.2)',
                handleColor: '#408829'
            },
            grid: {
                borderWidth: 0
            },

            categoryAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },

            valueAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },
            timeline: {
                lineStyle: {
                    color: '#408829'
                },
                controlStyle: {
                    normal: {color: '#408829'},
                    emphasis: {color: '#408829'}
                }
            },

            k: {
                itemStyle: {
                    normal: {
                        color: '#68a54a',
                        color0: '#a9cba2',
                        lineStyle: {
                            width: 1,
                            color: '#408829',
                            color0: '#86b379'
                        }
                    }
                }
            },
            map: {
                itemStyle: {
                    normal: {
                        areaStyle: {
                            color: '#ddd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    },
                    emphasis: {
                        areaStyle: {
                            color: '#99d2dd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    }
                }
            },
            force: {
                itemStyle: {
                    normal: {
                        linkStyle: {
                            strokeColor: '#408829'
                        }
                    }
                }
            },
            chord: {
                padding: 4,
                itemStyle: {
                    normal: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    },
                    emphasis: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    }
                }
            },
            gauge: {
                startAngle: 225,
                endAngle: -45,
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: [[0.2, '#86b379'], [0.8, '#68a54a'], [1, '#408829']],
                        width: 8
                    }
                },
                axisTick: {
                    splitNumber: 10,
                    length: 12,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                axisLabel: {
                    textStyle: {
                        color: 'auto'
                    }
                },
                splitLine: {
                    length: 18,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                pointer: {
                    length: '90%',
                    color: 'auto'
                },
                title: {
                    textStyle: {
                        color: '#333'
                    }
                },
                detail: {
                    textStyle: {
                        color: 'auto'
                    }
                }
            },
            textStyle: {
                fontFamily: 'Arial, Verdana, sans-serif'
            }
        };


        let pieData = [];

        function echart(pieData=[]){
            if ($('#echart_pie_t').length ){
                var echartPieCollapse = echarts.init(document.getElementById('echart_pie_t'), design);
                echartPieCollapse.setOption({
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        x: 'center',
                        y: 'bottom',
                        data: ['Force Leave', 'Present', 'IOM', 'Leave']
                    },
                    toolbox: {
                        show: true,
                        feature: {
                            magicType: {
                                show: true,
                                type: ['pie', 'funnel']
                            },
                            restore: {
                                show: false,
                                //title: "Restore"
                            },
                            saveAsImage: {
                                show: false,
                                // title: "Save Image"
                            }
                        }
                    },
                    calculable: true,
                    series: [{
                        name: 'Area Mode',
                        type: 'pie',
                        radius: [25, 90],
                        center: ['50%', 170],
                        x: '50%',
                        max: 40,
                        sort: 'ascending',
                        data: pieData
                    }]
                });

            }
        }

        $(document).ready(function () {

            $('#datatable').DataTable({
                dom: 'Bfrtip',
                bDestroy: 'true',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
                ]

            });

            lineChart(data=[])
        });


        $('.start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: '-3m',
            maxDate: new Date(),
            autoclose: 1,
            showOnFocus: true
        });


        $(".end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });


        let isSupervisorReport;


        function getSrAmolnamaData(){
            let data;
            let sr_id=$.trim($('#sr_id').val());
            let sv_id=$.trim($('#sv_id').val());
            let _token=$('#_token').val();
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            if(sr_id=='' && sv_id ==''){
                confirm('Please provide sr/supervisor staff id');
                return false;
            }


            isSupervisorReport = false;

            data = {
                sr_id: sr_id,
                sv_id: sv_id,
                start_date: start_date,
                end_date: end_date,
                _token: _token
            };


            if(sr_id == '' && sv_id !== ''){
                isSupervisorReport = true;
            }


            if(sv_id !== '' && sr_id !== ''){
                isSupervisorReport = false;
            }

            clearInputFields();


            if(!isSupervisorReport){
                sr_information(data);
                sr_amolnama_summary(data);
                sr_emp_summary(data);
                sr_sku(data);
                sr_total_category(data);
                sr_order_category(data);
                sr_order_delivery(data);
                sr_sku_cov(data);
                sr_category_wise_visit(data);
                sr_assigned_thana_union(data);
                sr_visited_thana_union(data);
                sr_activity(data);
            }
        }


        function sr_information(filter){
            let info;

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrInformation",
                data: filter,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let sat = data[0]
                        let sun = data[1]
                        let mon = data[2]
                        let tus = data[3]
                        let wed = data[4]
                        let thu = data[5]

                        let html = '';
                        // sr-route-info
                        $('#sr-route-info').html('');
                        for (let i = 0; i < data.length; i++) {
                            html += `<tr class="tbl_body_gray">
                                    <td>${data[i]['rpln_day']} </td>
                                    <td>${data[i]['rout_name']} </td>
                                    <td>${data[i]['t_site']} </td>
                                </tr>`;
                        }

                        $('#sr-route-info').html(html);


                        // $('#sat-rout-code').text(sat.rout_name)
                        // $('#sun-rout-code').text(sun.rout_name)
                        // $('#mon-rout-code').text(mon.rout_name)
                        // $('#tus-rout-code').text(tus.rout_name)
                        // $('#wed-rout-code').text(wed.rout_name)
                        // $('#thu-rout-code').text(thu.rout_name)
                        // $('#sat-olt').text(sat.t_site)
                        // $('#sun-olt').text(sun.t_site)
                        // $('#mon-olt').text(mon.t_site)
                        // $('#tus-olt').text(tus.t_site)
                        // $('#wed-olt').text(wed.t_site)
                        // $('#thu-olt').text(thu.t_site)

                    }



                    swal.fire({
                        position: 'top-right',
                        icon: 'success',
                        title: 'Summary data is ready',
                        showConfirmButton: false,
                        timer: 1500
                    })
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_amolnama_summary(filter){
            let info;

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrAmolnamaData",
                data: filter,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        info = data[0]
                        $('#bu').val(info.acmp_name)
                        $('#staff-id').val(info.aemp_usnm)
                        $('#group').val(info.slgp_name)
                        $('#name').val(info.aemp_name)
                        $('#zone').val(info.zone_name)
                        $('#mobile').val(info.aemp_mob1)
                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_emp_summary(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrEmpSummaryData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        let v_olt = parseInt(info.np_visit)+parseInt(info.p_visit);
                        let t_visit = parseInt(info.p_visit)+parseInt(info.np_visit)
                        let s_rate = (info.t_memo/t_visit)*100

                        let pieData = [
                            {value: info.present, name: 'Present'},
                            {value: info.iom, name: 'IOM'},
                            {value: info.leave, name: 'Leave'},
                            {value: info.fc_leave, name: 'Force Leave'}
                        ];

                        echart(pieData)

                        $('#route-olt').val(info.t_outlet);
                        $('#visit-olt').val(v_olt);
                        $('#s-olt').val(info.t_memo);
                        $('#lpc').val(info.lpc);
                        $('#exp').val(info.t_amnt);
                        $('#s-rate').val(s_rate.toFixed(2));



                        $('#total-days').append(t_days);
                        $('#total-present').append(info.present);
                        $('#total-iom').append(info.iom);
                        $('#total-leave').append(info.leave);
                        $('#total-fc_leave').append(info.fc_leave);

                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_sku(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrSku",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        $('#t-sku').val(info.t_sku);
                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_total_category(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrTotalCategory",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        $('#category').val(info.total_category);
                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_order_category(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrOrderCategory",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        $('#order-category').val(info.order_category);
                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_order_delivery(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let diff = new Date(end_date) - new Date(start_date)
            let t_days = diff/1000/60/60/24;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getOrderDelivery",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        $('#delivery').val(info.deli_amnt);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function sr_sku_cov(data){

            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrOrderSku",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        $('#order-sku').val(data[0].sku_cov);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function sr_category_wise_visit(filter){
            let html;
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrCategoryWiseVisit",
                data: filter,
                cache: false,
                dataType: "json",
                success: function (data) {
                    let info = data.info
                    for (let i = 0; i < info.length; i++) {
                        html += `<tr class="tbl_body_gray">
                                <td>${i+1} </td>
                                <td>${info[i]['otcg_name']} </td>
                                <td>${info[i]['t_site']}</td>
                                <td>
                                    <i onclick="showCategoryWiseVisitDetails('${data.start_date}','${data.end_date}', ${data.employee_id}, ${info[i].id})" class="fa fa-info-circle" aria-hidden="true"></i>
                                </td>
                            </tr>`;
                    }

                    $('#category-wise-visit-info').html(html);

                    $('#old-category').val(info.length);
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function showCategoryWiseVisitDetails(start_date, end_date, employee_id, outlet_category){
            let html;
            let _token=$('#_token').val();

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrCategoryWiseVisitDetails",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    employee_id: employee_id,
                    outlet_category_id: outlet_category,
                    _token: _token,
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#myModalCategoryWiseVisitView").show();
                    $("#myModalCategoryWiseVisitView").modal({backdrop: false});
                    $('#myModalCategoryWiseVisitView').modal('show');

                    for (let i = 0; i < data.length; i++) {
                        html += `<tr class="tbl_body_gray">
                                    <td>${data[i]['site_code']} </td>
                                    <td>${data[i]['site_name']} </td>
                                    <td>${data[i]['site_mob1']} </td>
<!--                                    <td>${data[i]['site_adrs']} </td>-->
                                    <td>${data[i]['frequency']}</td>
                                </tr>`;
                    }

                    $('#category-wise-visit-details-info').html(html);
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function sr_assigned_thana_union(data){
            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrAssignedThanaUnion",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];

                        $('#assigned-thana').text(info.t_thana);
                        $('#assigned-union').text(info.t_ward);
                        $('#assigned-olt').text(info.t_site);
                        // $('#assigned-ach').text('100%');

                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_visited_thana_union(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrVisitedThanaUnion",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data[0]) {
                        let info = data[0];
                        let assigned_site = $('#assigned-olt').text();
                        let assigned_thana = $('#assigned-thana').text();
                        let assigned_union = $('#assigned-union').text();
                        let achievement;
                        let thana_achievement;
                        let union_achievement;

                        if(Math.round((info.t_site/assigned_site)*100) > 100)
                        {
                            achievement = 100
                        }else{
                            achievement = ((info.t_site/assigned_site)*100).toFixed(2)
                        }

                        if(Math.round((info.t_thana/assigned_thana)*100) > 100)
                        {
                            thana_achievement = 100
                        }else{
                            thana_achievement = ((info.t_thana/assigned_thana)*100).toFixed(2)
                        }

                        if(Math.round((info.t_ward/assigned_union)*100) > 100)
                        {
                            union_achievement = 100
                        }else{
                            union_achievement = ((info.t_ward/assigned_union)*100).toFixed(2)
                        }

                        $('#visited-thana').text(`${thana_achievement}%`);
                        $('#visited-union').text(`${union_achievement}%`);
                        $('#visited-olt').text(`${achievement}%`);

                    }
                },
                error:function(error){
                    console.log(error);
                },
                statusCode: {
                    404: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Invalid Staff ID!',
                        })
                    }
                }
            })
        }


        function sr_day_wise_visit_outlet(){
            let html;
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let sr_id=$.trim($('#sr_id').val());
            let _token=$('#_token').val();

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrDayWiseVisitOutlet",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    sr_id: sr_id,
                    _token: _token,
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#myModalVisitedOutletDetails").modal({backdrop: false});
                    $('#myModalVisitedOutletDetails').modal('show');

                    for (let i = 0; i < data.length; i++) {
                        html += `<tr class="tbl_body_gray">
                                    <td>${data[i]['date']} (${data[i]['day_name']}) </td>
                                    <td>${data[i]['t_outlet']} </td>
                                    <td>${data[i]['ro_visit']} </td>
                                    <td>${data[i]['wr_visit']} </td>
                                    <td>
                                        <i onclick="viewVisitedOutletMap('${data[i]['date']}', ${sr_id})" class="fa fa-map-marker" aria-hidden="true"></i>
                                    </td>
                                </tr>`;
                    }

                    $('#visited-outlet-details-info').html(html);
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function lineChart(data=[]){
            if ($('#visit_vs_productive_non_productive_bar').length ){
                let t_visit=[];
                let p_visit=[];
                let np_visit=[];
                let labels=[];

                labels = Object.keys(data);


                Object.values(data).forEach(([total, productive, non_productive]) => {
                    t_visit.push(total);
                    p_visit.push(productive);
                    np_visit.push(non_productive);
                });

                var echartBar = echarts.init(document.getElementById('visit_vs_productive_non_productive_bar'), design);
                echartBar.setOption({
                    title: {
                        text: '',
                        subtext: ''
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: labels
                    },
                    toolbox: {
                        show: false
                    },
                    calculable: false,
                    xAxis: [{
                        type: 'category',
                        data: labels
                    }],
                    yAxis: [{
                        type: 'value'
                    }],
                    grid: { containLabel: true },
                    series: [{
                        name: 'Total Visit',
                        type: 'bar',
                        data: t_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Productive',
                        type: 'bar',
                        data: p_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    },{
                        name: 'Non Productive',
                        type: 'bar',
                        data: np_visit,
                        markPoint: {
                            data: [{
                                type: 'max',
                                name: 'Maximum'
                            }, {
                                type: 'min',
                                name: 'Lowest'
                            }]
                        },
                        markLine: {
                            data: [{
                                type: 'average',
                                name: 'Average'
                            }]
                        }
                    }
                    ]
                });

            }
        }



        function sr_activity(data){

            $.ajax({
                type:"POST",
                url: "{{ URL::to('/')}}/getSrActivityData",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data) {
                        lineChart(data)
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }


        function showCategoryWiseVisit(){
            $("#myModalCategoryWiseVisit").modal({backdrop: false});
            $('#myModalCategoryWiseVisit').modal('show');
        }


        $('body').click(function(e) {
            let visitDetailsModal = $("#myModalCategoryWiseVisitView")

            if (visitDetailsModal.is(":visible")) {
                if(visitDetailsModal.is(e.target) && visitDetailsModal.has(e.target).length === 0) {
                    visitDetailsModal.hide();
                }
            }
        });


        function clearInputFields(){
            $('#bu').val('')
            $('#staff-id').val('')
            $('#group').val('')
            $('#name').val('')
            $('#zone').val('')
            $('#mobile').val('')
            // $('#sat-rout-code').empty()
            // $('#sun-rout-code').empty()
            // $('#mon-rout-code').empty()
            // $('#tus-rout-code').empty()
            // $('#wed-rout-code').empty()
            // $('#thu-rout-code').empty()
            // $('#sat-olt').empty()
            // $('#sun-olt').empty()
            // $('#mon-olt').empty()
            // $('#tus-olt').empty()
            // $('#wed-olt').empty()
            // $('#thu-olt').empty()
            $('#t-sku').val('')
            $('#category').val('')
            $('#order-sku').val('')
            $('#order-category').val('')
            $('#route-olt').val('')
            $('#target').val('')
            $('#visit-olt').val('')
            $('#exp').val('')
            $('#s-olt').val('')
            $('#delivery').val('')
            $('#s-rate').val('')
            $('#p-sales').val('')
            $('#lpc').val('')
            $('#sr-route-info').html('')
            $('#old-category').val('')
            $('#category-wise-visit-info').html('');
            $('#category-wise-visit-details-info').html('');


            //clear chart information
            lineChart(data=[])

            $('#total-days').empty();
            $('#total-present').empty();
            $('#total-iom').empty();
            $('#total-leave').empty();
            $('#total-fc_leave').empty();
        }
    </script>
    <script src="{{asset('theme/src/js/web_js/sr_amolnama_map.js')}}"></script>
@endsection