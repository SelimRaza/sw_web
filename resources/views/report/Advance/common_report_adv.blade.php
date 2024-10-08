@extends('theme.app')
@section('content')
    <div class="right_col" role="main" style="color:black !important;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>Report</strong>
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
                    {{--<div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Order Summary :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>--}}

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{url ('load/filter/common_sr_activity_filter/demo2')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <input type="hidden" name="email_address" id="email_address" value="<?php echo Auth::user()->employee()->aemp_emal; ?>">
                                {{csrf_field()}}


                                <div class="col-md-6 col-sm-6 col-xs-12" id="rp">
                                    <div class="item form-group rp_type_div" id="rpt">
                                        <div class="x_title">
                                            <div class="btnDiv col-md-12 col-sm-12 col-xs-12"
                                                 style="margin-bottom:5px;">
                                                <div id="exTab1" class="container">
                                                    <ul class="nav nav-pills" style="color:black !important; font-weight:bold;">
                                                        <li>
                                                            <a href="#1a" data-toggle="tab"
                                                               onclick="getSRReport()">SR</a>
                                                        </li>
                                                        <li><a href="#2a" data-toggle="tab" onclick="getOutletReport()">
                                                                Outlet</a>
                                                        </li>
                                                        <li><a href="#2a" data-toggle="tab" onclick="getTripReport()">
                                                                Trip</a>
                                                        </li>
                                                        <li><a href="#3a" data-toggle="tab" onclick="getOrderReport()">
                                                                Order</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab"
                                                               onclick="getDeviationReport()">
                                                                Deviation</a>
                                                        <li><a href="#4a" data-toggle="tab" onclick="getNoteReport()">
                                                                Activity</a>
                                                        </li>
                                                        
                                                        <li><a href="#4a" data-toggle="tab"
                                                               onclick="getEmpTrackingReport()">Monitoring</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab"
                                                               onclick="getAccountReport()">Accounts</a>
                                                        </li>
                                                        <!-- <li><a href="#4a" data-toggle="tab"
                                                               onclick="getTopBottomReport()">Top Bottom</a>
                                                        </li> -->
                                                    </ul>

                                                </div>

                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12"
                                             style="font-size: 11px; height: 115px; border-radius:10%;"
                                             id="rpt_selection_div">
                                            <div id="sr_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                 
                                                <!-- <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="item_summary_depo_wise"/> Item Summary[Depo]
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="order_vs_delivery_adv"/> Order Vs Deliver[Adv]
                                                    </label>
                                                </div> -->
                                                <div class="col-md-6 col-sm-6">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="zone_summary"/> Zone Summary
                                                    </label>
                                                </div>
                                                
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_productivity"/> Productivity
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_non_productivity"/> Non-Productive SR
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_summary_by_group"/> SR Summary (By Group)
                                                    </label>
                                                </div>
                                                

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_wise_item_summary_quatar"/> SR Wise Item Summary
                                                        (Order)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="attendance_report"/> Attendance Report
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_item_survey"/> SR Item Survey
                                                    </label>
                                                </div>
                                                <!-- <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_routed_outlet"/> SR Route Outlet[Req Only]
                                                    </label>
                                                </div> -->
                                                
                                            </div>
                                            <div id="outlet_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">                                                
                                                 <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="weekly_outlet_summary"/>&nbsp;Outlet Weekly Summary
                                                        </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="outlet_dues_report_adv"/>&nbsp; Outlet Dues(Cash)
                                                        </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="outlet_dues_report_credit"/>&nbsp; Outlet Dues(Credit Party)
                                                        </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="outlet_crq_history"/>&nbsp; Outlet Dues History
                                                        </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="market_outlet_sr_outlet"/> Market Outlet vs SR
                                                        Outlet
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="outlet_coverage"/>&nbsp;Outlet Coverage
                                                    </label>
                                                </div>
                                                
                                            </div>
                                            <div id="trip_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated ">                                                
                                                 <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="trip_print"/>&nbsp;Trip Print
                                                        </label>
                                                </div>
                                                
                                            </div>
                                            <div id="accounts_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated ">                                                
                                                 <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="special_budget"/>&nbsp;Special Disc Budget
                                                        </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                                value="credit_budget"/>&nbsp;Credit Budget
                                                        </label>
                                                </div>
                                                
                                            </div>
                                            <div id="emp_tracking_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat emp_tracking_input"
                                                               name="emp_tracking_reportType"
                                                               id="emp_tracking_reportType"
                                                               value="emp_tracking_sales_hierarchy"
                                                               onclick="getDiggingReport(this.value)"/>&nbsp;&nbsp;Executive
                                                        Summary(Sales Hierarchy)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat emp_tracking_input"
                                                                name="reportType"
                                                                id="reportType"
                                                                value="sh_gvt"
                                                               onclick="getEmpGvtHierarchyWindow(this.value)"/>&nbsp;&nbsp;Executive
                                                        Summary(Govt. Hierarchy)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat emp_tracking_input"
                                                               name="emp_tracking_reportType"
                                                               id="emp_tracking_reportType"
                                                               value="{{$emid}}"
                                                               onclick="getEmpAttendanceReport(this.value)"/>&nbsp;&nbsp;Employee
                                                        Attendance
                                                    </label>
                                                </div>

                                            </div>
                                            <!-- employee tracking report selection div -->
                                            <!-- tracking employee based on sales hierarchy -->
                                            <div id="tracing" class="col-md-12 col-sm-12 col-xs-12 tracing">
                                                <div class="form-group col-md-3 col-sm-3 col-xs-12 pull-right" id="sh_select_date">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="sh_slgp_id">Date<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2 " name="sh_date"
                                                                id="sh_date"
                                                                onchange="getDateWiseUserReport(this.value)">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-10 col-sm-10 col-xs-12" id="user_level"
                                                     style="font-size:14px;">
                                                    <div>
                                                        <div style="float:left;"><p
                                                                    style="font-weight:bold; margin-right:3px;"><span
                                                                        id="desig"></span></p></div>

                                                        <div class="btn-group dropright" style="float:left;">
                                                            <a href="#" class=" dropdown-toggle " data-toggle="dropdown"
                                                               aria-haspopup="true" aria-expanded="false"
                                                               style="font-size:15px;font-weight:bold;background-color:#169F85; color:white;">
                                                                ALL &nbsp;
                                                            </a>
                                                            <ul class="dropdown-menu" aria-labelledby="about-us"
                                                                style="margin-left:35px;margin-top:-20px;"
                                                                id="all_dp_content">
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div id='emid_div' style="display:none;">
                                                            <input type="hidden" value="{{$emid}}" id="emid">
                                                        </div>
                                                        <!-- <div class="btn-group dropright">
                                                                <a href="#" class=" dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-bottom:15px;">
                                                                    All &nbsp;▶
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="about-us" style="margin-left:35px;margin-top:-20px;" id="all_dp_content">
                                                                </ul>
                                                            </div> -->
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-12 float-right"
                                                     style="margin-bottom:8px;">
                                                     <input type="text" class="form-control in_tg start_date" name="start_date"
                                                               id="period" autocomplete="off" value="<?php echo date('Y-m-d'); ?>" onchange="getDateWiseUserReport(-1)" style="display:none;"/>
                                                    <input type="text" class="form-control in_tg start_date"
                                                           name="start_date"
                                                           id="dev_note_date" autocomplete="off"
                                                           value="<?php echo date('Y-m-d'); ?>"
                                                           onchange="getDateWiseDevTaskNote(this.value)"
                                                           style="display:none;"/>
                                                </div>
                                                {{--traking report --}}

                                                <div id="tableDiv_traking" class="col-md-12 col-sm-12">
                                                    <div class="x_panel">
                                                        <div class="x_content">
                                                            <div class="col-md-12 col-sm-12 col-xs-12"
                                                                 style="overflow: auto;">

                                                                <div align="right" style="margin-bottom:10px;">

                                                                    <!-- <a href="#"
                                                                       onclick="exportTableToCSV('employee_sales_traking_report_slgp_<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking')"
                                                                       class="btn btn-warning"
                                                                       id="employee_sales_traking_report_slgp">Export
                                                                        CSV File
                                                                    </a>
                                                                    <a href="#"
                                                                       onclick="exportTableToCSV('emp_task_note_report<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking')"
                                                                       class="btn btn-warning" style="display:none;"
                                                                       id="emp_task_note_report">Export
                                                                    </a> -->
                                                                </div>
                                                                <table id="datatablesa"
                                                                       class="table table-bordered table-responsive"
                                                                       data-page-length='100'>
                                                                    <thead>
                                                                    <tr class="" id="head_tracking_dev_note_task">
                                                                        <th>Sl</th>
                                                                        <th> Date</th>
                                                                        <th>Name</th>
                                                                        <th>Mobile</th>
                                                                        <th>Total SR</th>
                                                                        <th>Outlet</th>
                                                                        <th>Visited</th>
                                                                        <th>Non Visited</th>
                                                                        <th>Order</th>
                                                                        <th>Exp</th>
                                                                        <th>Exp-Tgt</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody id="cont_traking">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div id="tracing_gvt" class="col-md-12 col-sm-12 col-xs-12 tracing">
                                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                            for="sh_acmp_id">Company<span
                                                                    class="required"></span>
                                                        </label>
                                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                                            <select class="form-control commonSelect2" name="sh_acmp_id"
                                                                    id="sh_acmp_id"
                                                                    onchange="getGroup(this.value,3)">

                                                                <option value="">Select Company</option>
                                                                @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                </div>
                                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="sh_slgp_id">Group<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="sh_slgp_id"
                                                                id="sh_slgp_id"
                                                                onchange="getDiggingReport('emp_tracking_gvt_hierarchy')">

                                                            <option value="">Select Group</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="sh_slgp_id">Date<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2 " name="sh_date_gvt"
                                                                id="sh_date_gvt"
                                                                onchange="getDateWiseGvtReport(this.value)">
                                                                
                                                        </select>
                                                    </div>
                                                    
                                                </div>
                                                <div class="form-group col-md-2 col-sm-2 col-xs-12 gvt_filter" id="sh_date_gvt_single_div" style="float:right;">
                                                        <input type="text" class="form-control in_tg start_date" name="sh_date_gvt_single_date" style="display:none;"
                                                               id="sh_date_gvt_single_date" autocomplete="off" value="<?php echo date('Y-m-d',strtotime("-1 days")); ?>" onchange="getDateWiseGvtReport(-1)"/>
                                                </div>
                                                <div class="col-md-11 col-sm-11 col-xs-12" id="user_level"
                                                     style="font-size:14px;">
                                                    <div>
                                                        <div style="float:left;"><p
                                                                    style="font-weight:bold; margin-right:3px;"><span
                                                                        id="gvt_hierarchy"></span></p></div>
                                                        <div id='emid_div1' style="display:none;">

                                                        </div>
                                                        <div class="btn-group dropright" style="float:left;">
                                                            <a href="#" class=" dropdown-toggle " data-toggle="dropdown"
                                                               aria-haspopup="true" aria-expanded="false"
                                                               style="font-size:15px;font-weight:bold;background-color:#169F85; color:white;">
                                                                ALL &nbsp;
                                                            </a>
                                                            <ul class="dropdown-menu" aria-labelledby="about-us"
                                                                style="margin-left:35px;margin-top:-20px;"
                                                                id="all_dp_content1">
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1 col-xs-12 float-right deviation_date"
                                                     style="margin-bottom:8px;" id="deviation_date_div">
                                                    <input type="text" class="form-control in_tg start_date"
                                                           name="start_date"
                                                           id="deviation_date"
                                                           autocomplete="off" value="<?php echo date('Y-m-d'); ?>"
                                                           onchange="getDateWiseDeviationData(this.value,1)"/>
                                                </div>
                                                <br><br>
                                                <div id="tableDiv_traking_gvt" class="col-md-12 col-sm-12 div_hide"
                                                     style="margin-top:15px;">
                                                    <div class="x_panel">
                                                        <div class="x_content">
                                                            <div class="col-md-12 col-sm-12 col-xs-12"
                                                                 style="height:600px;overflow: auto;">

                                                                <div align="right" style="margin-bottom:10px;">
                                                                    <!-- <a href="#"
                                                                       onclick="exportDetailExecutiveGVTReport()"
                                                                       class="btn btn-primary div_hide" id="export_details"
                                                                    >Export Details
                                                                    </a>
                                                                    <a href="#"
                                                                       onclick="exportTableToExcel(this,'employee_sales_traking_report_gvt_<?php echo date('Y_m_d'); ?>.xls','tbl_traking_gvt')"
                                                                       class="btn btn-default"
                                                                       id="export_csv_tracking_gvt">Export
                                                                    </a> -->
                                                                </div>
                                                                <table id="tbl_traking_gvt" class="table table-bordered table-responsive"  border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                                                >
                                                                    <thead id="tableDiv_tracking_gvt_header1"
                                                                           style="font-size:11px;">
                                                                    <!-- <tr class="" id="tableDiv_tracking_gvt_header1">

                                                                    </tr> -->
                                                                    </thead>
                                                                    <tbody id="cont_traking_gvt"
                                                                           >
                                                                           <div id="gvt_load" style="display:none;">
                                                                                <img src="{{ asset("theme/production/images/gif-load.gif")}}" class="ajax-loader"/>
                                                                            </div>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="order_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="class_wise_order_report_amt"/> SR vs Class wise EXP/Order
                                                    </label>
                                                </div>
                                                
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="cat_wise_order_report_amt"/> SR vs Category wise EXP/Order
                                                    </label>
                                                </div>
                                                
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_wise_order_delivery"/>SR Wise Order Vs Delivery
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sku_wise_order_delivery"/> SKU Wise Order Vs
                                                        Delivery
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="zone_wise_order_delivery_summary"/> Zone Wise
                                                        Order Vs Delivery
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_wise_order_details"/> Order Details Report
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="item_coverage"/> Item Coverage
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="item_summary"/> Item Summary
                                                    </label>
                                                </div>
                                                @if(Auth::user()->country()->module_type==2)
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="item_summary_depo_wise"/> Item Summary[Depo]
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="order_vs_delivery_adv"/> Order Vs Deliver[Adv]
                                                    </label>
                                                </div>
                                                @endif
                                                <!-- <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="asset_summary"/> Asset Summary
                                                    </label>
                                                </div>
                                                
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="asset_details"/> Asset Details
                                                    </label>
                                                </div> -->

                                            </div>
                                            <div id="note_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                 <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_activity_hourly_visit"/> SR Hourly Activity
                                                        (Visit)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_activity_hourly_order"/> SR Hourly Activity
                                                        (Order)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_hourly_activity"/> SR Hourly Activity
                                            
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="note_report" /> Note Details
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="note_summary" /> Note Summary
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="sr_activity_sales_hierarchy"/> SR Activity [Gvt Hierarchy]
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="assign_task_stat"/> Assign Task 
                                                    </label>
                                                </div>
                                                <!-- <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="sr_activity_gvt_hierarchy"/> SR Activity [Gvt Hierarchy]
                                                    </label>
                                                </div> -->
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="activity_summary"/> Supervisor Activity
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="top_bottom"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="item" /> Item
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="sales_force"/> Sales Force
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="distributor"/> Distributor
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="district"/> District
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                                id="reportType"
                                                               value="thana"/> Thana
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="deviation_report"
                                                 class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

                                                <!-- <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_route_outlet" onClick="getDeviationData(this.value)"/> SR Route Outlet
                                                    </label>
                                                </div> -->
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="group_wise_route_outlet"
                                                               onClick="getDeviationData(this.value)"/> Abnormal Outlet
                                                        with Routes
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="outlet_visit"
                                                               onClick="getDeviationData(this.value)"/> Outlet Visit %
                                                    </label>
                                                </div>

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="note_task"
                                                               onClick="getDeviationData(this.value)"/> Work Note
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_movement"
                                                               onClick="getDeviationData(this.value)"/> SR
                                                        Movement(Yesterday)
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-12" style="font-size: 12px;" id="harch">

                                    <div class="item form-group rp_type_div " id="hierarchy">
                                        <div class="col-md-12 col-sm-12" style="height: 263px;">

                                            <div class="x_title" id="title_head">
                                                <div class="btnDiv col-md-12 col-sm-12 col-xs-12"
                                                     style="margin-bottom: 5px;">
                                                    <br><br>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <br><br>
                                            <div id="accounts_filter" class="form-row animate__animated animate__zoomIn">
                                                <div class="form-group col-md-12 "></div>
                                                <div class="form-group col-md-12 "></div>
                                                <div class="form-group col-md-12 "></div>
                                                <div class="form-group col-md-6 " id="year_month1_budget">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="start_date_period">Select Period<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg"
                                                            name="year_mnth1" id="year_mnth1"
                                                            value="<?php echo date('Y-m'); ?>" autocomplete="off" />

                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 ord_flag_div"  id="budget_details_sum">
                                                    <input type="radio" id="html" name="budget_type" value="1" checked>
                                                    <label for="html">Summary</label><br>
                                                    <input type="radio" id="lab1" name="budget_type" value="2" >
                                                    <label for="lab1">Details</label>
                                                </div></br>
                                                <div class="form-group col-md-6">
                                                    <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-offset-4">
                                                        <button id="trip_report_submit" type="button"
                                                                class="btn btn-success  btn-block in_tg"
                                                                onclick="getAccountsData()">Show
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                            <div id="trip_filter" class="form-row animate__animated animate__zoomIn">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="trip_date">Trip Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg trip_filter_date"
                                                               name="trip_date"
                                                               id="trip_date" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off" onchange="getAllTrip()"/>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="depo_id">Depo<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="depo_id"
                                                                id="depo_id"
                                                                onchange="getAllTrip()" >
                                                                <option value="">Select Depo</option>
                                                            @foreach($depo_list as $depo)
                                                                <option value="{{$depo->dlrm_code}}">{{$depo->dlrm_code}}
                                                                    - {{$depo->dlrm_name}}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="trip_no">Trip No<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="trip_no"
                                                                id="trip_no"
                                                               >

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="coll_date">Collection Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg trip_filter_date"
                                                               name="coll_date"
                                                               id="coll_date" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group col-md-6">
                                                    <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-offset-4">
                                                        <button id="trip_report_submit" type="button"
                                                                class="btn btn-success  btn-block in_tg"
                                                                onclick="getTripData()">Show
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                            <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                                                <div class="form-group col-md-6 filter_common">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="acmp_id">Company<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="acmp_id"
                                                                id="acmp_id"
                                                                onchange="getGroup(this.value,1)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 filter_common">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sales_group_id">Group<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="sales_group_id[]"
                                                                id="sales_group_id"
                                                                onchange="getSRForWeeklyOrder()" multiple>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6 gvt">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dist_id">District<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="dist_id"
                                                                id="dist_id"
                                                                onchange="getThanaBelogToDistrict(this.value,1)">
                                                            <option value="">Select District</option>
                                                            @foreach($dsct1 as $d)
                                                                <option value="{{$d->id}}">{{$d->dsct_name}}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 gvt">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="than_id">Thana<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="than_id"
                                                                id="than_id"
                                                                 >
                                                            <option value="">Select Thana</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6 filter_common" id="dirg_id_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dirg_id">Region/State<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="dirg_id[]"
                                                                id="dirg_id"
                                                                onchange="getZone()" multiple>
                                                            @foreach($region as $regionList)
                                                                <option value="{{$regionList->id}}">{{$regionList->dirg_code}}
                                                                    - {{$regionList->dirg_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 zone_div filter_common">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="zone_id">Zone<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="zone_id[]"
                                                                id="zone_id" onchange="getSRForWeeklyOrder()" multiple>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 ord_flag_div"  id="ord_flag_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="ord_flag">Type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="ord_flag"
                                                                id="ord_flag">
                                                                <option value="1">EXP</option>
                                                                <option value="2">Order</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 ord_flag_div"  id="dtls_sum_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dtls_sum">R Type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="dtls_sum"
                                                                id="dtls_sum">
                                                                <option value="1">Details</option>
                                                                <option value="2">Summary</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group col-md-6 ord_flag_div"  id="sr_sv_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sr_sv">U Type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="sr_sv"
                                                                id="sr_sv">
                                                                <option value="1">SR</option>
                                                                <option value="2">SV</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 ">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date">Start Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="start_date"
                                                               id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date">End Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="end_date"
                                                               id="end_date" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 attendance_type_div"  id="attendance_type_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="attendance_type">Attendance<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control cmn_select2" name="attendance_type"
                                                                id="attendance_type">
                                                                <option value="">Both</option>
                                                                <option value="=1">Present</option>
                                                                <option value="!=1">Absent</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group col-md-6 start_date_period_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_period">Select Period<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2"
                                                                name="start_date_period" id="start_date_period"
                                                                onchange="showCustomDate(this.value,1)">
                                                

                                                        </select>

                                                    </div>

                                                </div> -->
                                                <div class="form-group col-md-6 ord_flag_div"  id="sr_zone_div">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dtls_sum">SR/Zone<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="sr_zone"
                                                                id="sr_zone">
                                                                <option value="1">SR</option>
                                                                <option value="2">Zone</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @if($asset)
                                                <div class="form-group col-md-6 " id="asset_div" style="display:none;">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dirg_id">Asset type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="astm_id"
                                                                id="astm_id"
                                                                >
                                                            <option value="">Select asset</option>
                                                            @foreach($asset as $ast)
                                                                <option value="{{$ast->id}}">{{$ast->astm_name}}
                                                                    </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="form-group col-md-6 year_mnth outlet_weekly">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="start_date_period">Select Period<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg"
                                                            name="year_mnth" id="year_mnth"
                                                            value="<?php echo date('Y-m'); ?>" autocomplete="off" />

                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 sr_id outlet_weekly">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="start_date_period">Select SR<span class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="sr_id[]"
                                                            id="sr_id" multiple>
                                                        </select>

                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-offset-4">
                                                        <button id="send" type="button"
                                                                class="btn btn-success  btn-block in_tg"
                                                                onclick="getSummaryReport()">Show
                                                        </button>
                                                    </div>

                                                </div>


                                                <div class="form-group col-md-12 col-sm-12 col-xs-12"
                                                     style="margin-top: 15px;">
                                                    <!-- <div class="pull-right" style="margin-right:5%; margin-top:2%;">
                                                        <a href="#" onclick="getRequestedReportList()"
                                                           class="request_report_check">Click here to see requested
                                                            report status</a>
                                                    </div> -->


                                                </div>


                                            </div>
                                            <!-- start history -->
                                            <!-- <div id="history" class="form-row animate__animated animate__zoomIn">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="acmp_id">Company<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " name="acmp_id_h"
                                                                id="acmp_id_h"
                                                                onchange="getGroup(this.value,2)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sales_group_id">Group<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " name="sales_group_id_h"
                                                                id="sales_group_id_h"
                                                                onchange="getSR(this.value,1)">

                                                            <option value="">Select Group</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dist_id">District<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " name="dist_id_h"
                                                                id="dist_id_h"
                                                                onchange="getThanaBelogToDistrict(this.value,2)">
                                                            <option value="">Select District</option>
                                                            @foreach($dsct1 as $d)
                                                                <option value="{{$d->id}}">{{$d->dsct_name}}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="than_id">Thana<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " name="than_id_h"
                                                                id="than_id_h"
                                                                onchange="getSR(this.value,2)">
                                                            <option value="">Select Thana</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6" id="history_usr">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="usr_type">Type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " name="usr_type"
                                                                id="usr_type"
                                                               >
                                                            <option value="1">SR</option>
                                                            <option value="2">Supervisor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 ">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sr_id_h">Select Supervisor<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control " id="sr_id_h">
                                                        
                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="form-group col-md-6 ">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sr_id_h">OR <span class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg" id="sr_id_h_manual" placeholder="Staff ID">
                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 start_date_period_div_h">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_period">Select Period<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control "
                                                                name="start_date_period" id="start_date_period_h"
                                                                onchange="showCustomDate(this.value,2)">
                                                            <option value="0">Today</option>
                                                            <option value="1">Yesterday</option>
                                                            <option value="2">Last 3 days</option>
                                                            <option value="3">Last 7 days</option>
                                                            <option value="5">As Of</option>
                                                            <option value="4">Last 30 days</option>
                                                            <option value="">Custom Date?</option>

                                                        </select>

                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 ">
                                                   
                                                </div>
                                                <div class="form-group col-md-6 start_date_div_h" style="display:none;">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_h">Start Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="start_date_h"
                                                               id="start_date_h" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 start_date_div_h" style="display:none;">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_h">End Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="end_date_h"
                                                               id="end_date_h" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                            
                                                <div class="form-group col-md-6">

                                                    <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-8-offset-4">
                                                        <button id="send" type="button"
                                                                class="btn btn-success  btn-block in_tg" id="custom_history_rpt_btn"
                                                                onclick="getHReport()">Show
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12"
                                                     style="margin-top: 15px;">
                                                    <div class="pull-right" style="margin-right:5%; margin-top:2%;">
                                                        <a href="#" onclick="getRequestedReportList()"
                                                           class="request_report_check">Click here to see requested
                                                            report status</a>
                                                            
                                                    </div>


                                                </div>
                                                

                                            </div> -->
                                            <div id="history" class="form-row animate__animated animate__zoomIn">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="acmp_id">Company<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="acmp_id_h"
                                                                id="acmp_id_h"
                                                                onchange="getGroup(this.value,2)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sales_group_id">Group<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="sales_group_id_h[]"
                                                                id="sales_group_id_h"
                                                                onchange="getSR(this.value,1)" multiple>

                                                            <option value="">Select Group</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="dist_id">District<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="dist_id_h"
                                                                id="dist_id_h"
                                                                onchange="getThanaBelogToDistrict(this.value,2)">
                                                            <option value="">Select District</option>
                                                            @foreach($dsct1 as $d)
                                                                <option value="{{$d->id}}">{{$d->dsct_name}}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="than_id">Thana<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="than_id_h"
                                                                id="than_id_h"
                                                                onchange="getSR(this.value,2)">
                                                            <option value="">Select Thana</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6" id="history_usr">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="usr_type">Type<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2" name="usr_type"
                                                                id="usr_type"
                                                               >
                                                            <option value="1">SR</option>
                                                            <option value="2">Supervisor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 ">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sr_id_h" id="sr_id_h_label">Select<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control multiSelect" name="sr_id_h[]" id="sr_id_h" multiple>
                                                        
                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="form-group col-md-6 ">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="sr_id_h">OR <span class="required"></span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg" id="sr_id_h_manual" placeholder="Staff ID">
                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 start_date_period_div_h">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_period">Select Period<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <select class="form-control commonSelect2"
                                                                name="start_date_period" id="start_date_period_h"
                                                                onchange="showCustomDate(this.value,2)">
                                                            <option value="0">Today</option>
                                                            <option value="1">Yesterday</option>
                                                            <option value="2">Last 3 days</option>
                                                            <option value="3">Last 7 days</option>
                                                            <option value="5">As Of</option>
                                                            <option value="4">Last 30 days</option>
                                                            <option value="">Custom Date?</option>

                                                        </select>

                                                    </div>

                                                </div>
                                                <div class="form-group col-md-6 ">
                                                   
                                                </div>
                                                <div class="form-group col-md-6 start_date_div_h" style="display:none;">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_h">Start Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="start_date_h"
                                                               id="start_date_h" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 start_date_div_h" style="display:none;">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for="start_date_h">End Date<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <input type="text" class="form-control in_tg start_date"
                                                               name="end_date_h"
                                                               id="end_date_h" value="<?php echo date('Y-m-d'); ?>"
                                                               autocomplete="off"/>
                                                    </div>
                                                </div>
                            
                                                <div class="form-group col-md-6">

                                                    <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-8-offset-4">
                                                        <button id="send" type="button"
                                                                class="btn btn-success  btn-block in_tg" id="custom_history_rpt_btn"
                                                                onclick="getHReport()">Show
                                                        </button>
                                                    </div>
                                                </div>
                                               
                                                

                                            </div>
                                            <!-- end history -->
                                            <!-- asset filter area start -->
                                            
                                            <!-- asset filter area end -->

                                        </div>


                                    </div>
                                    <hr/>


                            </form>
                        </div>

                    </div>
                    {{--without outlet with item--}}
                    <div id="tableDiv_sr_activity_hourly_order" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow:auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_activity_hourly_order<?php echo date('Y_m_d'); ?>.xls','tbl_sr_activity_hourly_order')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sr_activity_hourly_order" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        <tr class="tbl_header" id="sr_activity_hourly_order_header">
                                            <th>Order Date</th>
                                            <th>Group</th>
                                            <th>Zone</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>09AM</th>
                                            <th>10AM</th>
                                            <th>11AM</th>
                                            <th>12PM</th>
                                            <th>01PM</th>
                                            <th>02PM</th>
                                            <th>03PM</th>
                                            <th>04PM</th>
                                            <th>05PM</th>
                                            <th>06PM</th>
                                            <th>07PM</th>
                                            <th>08PM</th>
                                            <th>09PM</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_sr_activity_hourly_order">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tableDiv_sr_activity_hourly_visit" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_activity_hourly_visit<?php echo date('Y_m_d'); ?>.xls','tbl_sr_activity_hourly_visit')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sr_activity_hourly_visit" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>

                                        <tr class="tbl_header" id="sr_activity_hourly_visit_header">
                                            <th>Order Date</th>
                                            <th>Group</th>
                                            <th>Zone</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>09AM</th>
                                            <th>10AM</th>
                                            <th>11AM</th>
                                            <th>12PM</th>
                                            <th>01PM</th>
                                            <th>02PM</th>
                                            <th>03PM</th>
                                            <th>04PM</th>
                                            <th>05PM</th>
                                            <th>06PM</th>
                                            <th>07PM</th>
                                            <th>08PM</th>
                                            <th>09PM</th>
                                        </tr>

                                        </thead>

                                        <tbody id="cont_sr_activity_hourly_visit">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="sr_hourly_report_div" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_hourly_activity<?php echo date('Y_m_d'); ?>.xls','sr_hourly_report_tb')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="sr_hourly_report_tb" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead id="sr_hourly_head" class="text-center">
                                        </thead>
                                        <tbody id="sr_hourly_cont">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--sr with class--}}
                    <div id="tableDivd_market_outlet_sr_outlet" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'market_outlet_vs_sr_outlet_<?php echo date('Y_m_d'); ?>.xls','tbl_market_outlet_sr_outlet')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_market_outlet_sr_outlet" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                            <th>SI</th>
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Available Outlet Quantity</th>
                                            <th>Route Outlets</th>


                                        </tr>
                                        </thead>
                                        <tbody id="contd_market_outlet_sr_outlet">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="div_outlet_coverage" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;max-height:700px;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'outlet_coverage<?php echo date('Y_m_d'); ?>.xls','tbl_outlet_coverage')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_outlet_coverage" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead style="position:sticky; inset-block-start:0;" class="tbl_header">
                                        <tr class="">
                                            <th>Sl</th>
                                            <th>Group</th>
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>T.Olt</th>
                                            <th>R.Olt</th>
                                            <th>C.Olt</th>
                                            <th>Exp</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_outlet_coverage">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--sr wise item summary report--}}

                    <div id="tablediv_sr_wise_item_summary_report" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_wise_item_summary_report<?php echo date('Y_m_d'); ?>.xls','tbl_sr_wise_item_summary_report')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sr_wise_item_summary_report" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        <tr id="sr_wise_item_summary_report_head">
                                        </tr>
                                        </thead>
                                        <tbody id="contd_sr_wise_item_summary_report">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--order details report for bahrain start --}}

                    <div id="tablediv_order_details_report" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'order_details_report<?php echo date('Y_m_d'); ?>.xls','order_details_report')"
                                                class="btn btn-warning">Export
                                        </a>
                                        <a onclick="exportTableToCSV('order_details_report<?php echo date('Y_m_d'); ?>.csv','tablediv_order_details_report')"
                                                class="btn btn-warning">CSV
                                        </a>
                                    </div>
                                    <table id="order_details_report" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name(CTN)</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Order Time</th>
                                        </tr>
                                        </thead>
                                        <tbody id="contd_order_details_report">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--order details report for bahrain end --}}
 

                    {{--sr with item--}}
                <!-- zone wise order delivery summary -->
                    <div id="tableDiv_zone_wise_order_delivery_summary" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:650px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'zone_wise_order_delivery_summary<?php echo date('Y_m_d'); ?>.xls','tbl_zone_wise_order_delivery_summary')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_zone_wise_order_delivery_summary" class="table table-bordered table-responsive"  border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead  style="position:sticky; inset-block-start:0;" class="tbl_header">
                                        {{--` t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`ordd_oamt`) as ordd_oamt,
                                                 sum(t1.`ordd_odat`) as ordd_Amnt `--}}
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name </th>
                                            <th>Zone Code</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_zone_wise_order_delivery_summary">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--traking report gvt --}}

                <!-- zone wise order delivery summary -->
                    <div id="tableDiv_sku_wise_order_delivery" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:650px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sku_wise_delivery<?php echo date('Y_m_d'); ?>.xls','tbl_sku_wise_order_delivery')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sku_wise_order_delivery" class="table table-striped table-bordered " border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                    >

                                        <thead  style="position:sticky; inset-block-start:0;" class="tbl_header">
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th>


                                        </tr>
                                        </thead>
                                        <tbody id="cont_sku_wise_order_delivery">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--class wise order summary amount--}}
                    <div id="tableDiv_class_wise_order_summary" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:650px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'class_wise_order_summary_<?php echo date('Y_m_d'); ?>.xls','tbl_class_wise_order_summary')"
                                                class="btn btn-warning" id="class_wise_order_exp_btn">Export
                                        </a>
                                    </div>
                                    <table id="tbl_class_wise_order_summary" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <tr id="class_wise_order_summary_amount_headings"
                                            style="position:sticky; inset-block-start:0;" class="tbl_header">

                                        </tr>

                                        <tbody id="cont_class_wise_order_summary" >

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--class wise order summary memo--}}
                    <div id="tableDiv_class_wise_order_summary_memo" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:650px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'class_wise_order_summary_<?php echo date('Y_m_d'); ?>.xls','tbl_class_wise_order_summary_memo')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_class_wise_order_summary_memo" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <tr id="class_wise_order_summary_memo_headings" class="tbl_header" style="position:sticky; inset-block-start:0;">

                                        </tr>
                                        <tbody id="cont_class_wise_order_summary_memo">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tableDiv_sr_route_outlet" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_route_outlet<?php echo date('Y_m_d'); ?>.xls','tbl_sr_route_outlet')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sr_route_outlet" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        <tr class="">
                                            <th>SI</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Zone Name</th>
                                            <th>Route Outlet</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_sr_route_outlet">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="tableDiv_group_wise_route_outlet" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'group_wise_route_outlet<?php echo date('Y_m_d'); ?>.xls','tbl_group_wise_route_outlet')"
                                                class="btn btn-warning">Export
                                        </a>
                                    </div>
                                    <table id="tbl_group_wise_route_outlet" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead>
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Group</th>
                                            <th>Total Route</th>
                                            <th>Total SR</th>
                                            <th>Below 60</th>
                                            <th>60 ~ 120</th>
                                            <th>Above 120</th>
                                            <th>Abnormal %</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_group_wise_route_outlet">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tableDiv_sr_history" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_history<?php echo date('Y_m_d'); ?>.xls','tbl_sr_history')"
                                                class="btn btn-warning" id="activity_section">Export
                                        </a>
                                    </div>
                                    <table id="tbl_sr_history" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead id="head_history" style="position:sticky; inset-block-start:0;"
                                               class="tbl_header">

                                        </thead>
                                        <tbody id="cont_history">

                                        </tbody>
                                        <tfoot id="foot_history"
                                               style="position:sticky;inset-block-end:0;">

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- modal started for visited outlet category -->
                    <!-- Note Report  Start-->
                    <div id="tableDiv_note_report" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <a onclick="exportTableToExcel(this,'sr_route_outlet<?php echo date('Y_m_d'); ?>.xls','tbl_note_report')"
                                                class="btn btn-warning" id="exp_btn">Export
                                        </a>
                                    </div>
                                    <table id="tbl_note_report" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead id="head_note_report" class="tbl_header">

                                        </thead>
                                        <tbody id="cont_note_report">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Note Report  End-->
                    <!-- asset report start -->
                    <div id="tblDiv_asset_report" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                            
                                    <!-- <div style="float:left;"><p
                                            style="font-weight:bold; margin-right:3px;"><span
                                                id="asst_click_head"></span></p>
                                    </div>

                                    <div class="btn-group dropright" style="float:left;">
                                        <a href="#" class=" dropdown-toggle " data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"
                                            style="font-size:15px;font-weight:bold;background-color:#169F85; color:white;">
                                            ALL &nbsp;
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="about-us"
                                            style="margin-left:35px;margin-top:-20px;"
                                            id="asset_dp_content">
                                        </ul>
                                    </div> -->
                              
                            
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">

                                <div align="right">
                                    <a onclick="exportTableToExcel(this,'asset-report<?php echo date('Y_m_d'); ?>.xls','tbl_asset_report')"
                                            class="btn btn-warning">Export
                                        </a>
                                </div> 
                                    <table id="tbl_asset_report" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead id="asset_report_head"  style="position:sticky; inset-block-start:0;" class="tbl_header">
                                            <tr>
                                                <th>Group</th>
                                                <th>Zone Name</th>
                                                <th>Staff Id</th>
                                                <th>SR Name</th>
                                                <th>Site Name</th>
                                                <th>T.Order</th>
                                                <th>Ast.Order</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cont_asset_report">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- asset report end -->
                    <!-- Dynamic table start -->
                    <div id="tl_dynamic" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:550px;overflow:auto;" >
                                    <div align="right" id="export_option_div">

                                    </div>
                                   
                                    <table id="masterTable" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"  data-page-length='100'>
                                        <thead id="tl_dynamic_head" class="tbl_header" style="position:sticky; inset-block-start:0;">

                                        </thead>
                                        <tbody id="tl_dynamic_cont">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Dynamic table end -->
                    <!-- requested report start -->
                    <div id="tblDiv_requested_report" class="div_hide">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <table id="datatablesa" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                           data-page-length='100'>
                                        <thead id="requested_report_head">
                                        <tr>
                                            <th>Sl</th>
                                            <th>Report Name</th>
                                            <th>Download Link</th>
                                            <th>Requested Time</th>
                                            <th>Delivery Email</th>
                                            <th>Status</th>

                                        </tr>
                                        </thead>
                                        <tbody id="tblDiv_requested_report_body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- requested report  end-->
                    <!-- trip -print-->
                    <div id="print-data" class="col-md-12 div_hide" >
                        <div class="x_panel">
                            <div class="x_title">
                                <div id="trip-summery-print-btn" class="col-md-1 col-sm-1 col-xs-12" style="float: right;display: none">
                                    <a href="#"
                                        class="btn btn-success" onclick="printTrip('print-data1')"
                                        style="float:right">Print
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content"  style="width:100%;overflow-x:auto;" style="height:600px;overflow: auto;" id="print-data1">
                                <h4 class="text_center" style="text-align:center;">Trip Details</h4>
                                <table>
                                    <thead id="print-table-head">
                                    </thead>
                                    <tbody id="cash_check" style="font-size:11px;">
                                    </tbody>
                                </table>                             
                                <table class="table table-responsive" border="1"
                                        style="overflow-x: auto; overflow-y:auto; border-collapse: collapse; font-size: 9px">
                                    <thead id="print-table-head1">
                                    </thead>
                                    <tbody id="print-table-info">
                                    </tbody>
                                </table>

                                <table class="table table-responsive" border="1"
                                        style="overflow-x: auto; overflow-y:auto; border-collapse: collapse; font-size: 9px">
                                    <thead id="other-collection-head">
                                    </thead>
                                    <tbody id="other-collection-info">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div></div>
                    <!-- requested report  end-->

                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="ac_dt" role="dialog">
        <div class="modal-dialog" style="width:80%;margin-top:-100px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center" id="modalCat">Activity Summary Details</h4>
                    <button onclick="exportTableToCSV('supervisor_activity<?php echo date('Y_m_d'); ?>.csv','ac_dt')"
                                                class="btn btn-warning" id="exp_btn">Export
                                        </button>
                </div>
                <div class="modal-body" style="max-height:550px;overflow:auto;">
                    <div class="loader" id="ac_dt_load" style="display:none; margin-left:35%;"></div>
                    <table class="table table-striped datatable">
                        <thead>
                        <tr>
                            <th>MONTH</th>
                            <th>DATE</th>
                            <th>START TIME</th>
                            <th>END TIME</th>
                            <th>THANA</th>
                            <th>DISTRICT</th>
                            <th>TOTAL NOTE</th>
                            <th>RETAIL VISIT</th>
                            <th>OTHER VISIT</th>
                        </tr>
                        </thead>
                        <tbody id="ac_dt_body">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="myModalVisit" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center" id="modalCat">Category wise Visit</h4>
                </div>
                <div class="modal-body">
                    <div class="loader" id="cat_out_load" style="display:none; margin-left:35%;"></div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Total Outlet</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="myModalVisitBody">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- category wise visited outlet -->
    <div class="modal fade" id="myModalVisitCat" role="dialog">
        <div class="modal-dialog" style="width:50%;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Category Wise Visit</h4>
                </div>
                <div class="modal-body">
                    <div class="loader" id="myModalVisitCat_load" style="display:none; margin-left:35%;"></div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Category</th>
                            <th>Visit</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody id="myModalVisitCat_body">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Visited outlet details -->
    <div class="modal fade" id="myModalVisitedOutlet" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center" id="modalOuthead">Visited Outlet Details</h4>
                </div>
                <div class="modal-body">
                    <div class="loader" id="cat_out_load_details" style="display:none; margin-left:35%;"></div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Outlet Code</th>
                            <th>Outlet Name</th>
                            <th>Outlet Mobile</th>
                            <th>Outlet Address</th>
                        </tr>
                        </thead>
                        <tbody id="myModalVisitedOutletBody">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <!-- thana market ward wise visit modal -->
    <div class="modal fade " id="myModalWardWiseVisit" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="loader" id="load_ward_visit" style="display:none; margin-left:35%;"></div>
                    <!-- <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Category</th>
                            <th>Visit</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody id="myModalWardWiseVisitBody1">

                        </tbody>
                    </table> -->
                    <h4 class="text-center">Union Wise Visit</h4>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Ward</th>
                            <th>Visit</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody id="myModalWardWiseVisitBody3">

                        </tbody>
                    </table>
                    <h4 class="text-center">Thana Wise Visit</h4>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Thana</th>
                            <th>Visit</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody id="myModalWardWiseVisitBody2">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <!-- sr ward wise visited outlet details -->
    <div class="modal fade" id="myModalVisitedOutletDetails" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Visited Outlet Details</h4>
                </div>
                <div class="modal-body">
                    <div class="loader" id="visit_out_load_details" style="display:none; margin-left:35%;"></div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Outlet Code</th>
                            <th>Outlet Name</th>
                            <th>Outlet Mobile</th>
                            <th>Outlet Address</th>
                        </tr>
                        </thead>
                        <tbody id="myModalVisitedOutletDetailsBody">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="visitMap" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%;margin-top:-220px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color:red;opacity:1;">&times;</button>
                    <h4 class="modal-title text-center">Map</h4>
                    <p class="modal-title text-center">
                        Productive Visit:<img src="{{asset('theme/image/map_icon_all/pv.png')}}">&nbsp;&nbsp;&nbsp;&nbsp;
                        ||&nbsp;&nbsp;&nbsp;&nbsp;Non.Productive Visit:<img src="{{asset('theme/image/map_icon_all/npv.png')}}">
                        Non Visited:<img src="{{asset('theme/image/map_icon_all/nv.png')}}">&nbsp;&nbsp;&nbsp;&nbsp;
                        ||&nbsp;&nbsp;&nbsp;&nbsp;SR Location: <img src="{{asset('theme/image/map_icon_all/point.png')}}">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        ||&nbsp;&nbsp;&nbsp;&nbsp;Start: <img src="{{asset('theme/image/map_icon_all/start.png')}}">
                        ||&nbsp;&nbsp;&nbsp;&nbsp;End: <img src="{{asset('theme/image/map_icon_all/end.png')}}">
                    </p>
                    
                </div>
                <div class="modal-body" id="googleMap" style="height:680px;">
                    <div class="loader" id="visit_map_loader" style="display:none; margin-left:45%;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="note_summary_map" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%;margin-top:-220px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color:red;opacity:1;">&times;</button>
                    <h4 class="modal-title text-center">Note Summary</h4>
                    <p class="modal-title text-center">
                        &nbsp;&nbsp;&nbsp;&nbsp;Start: <img src="{{asset('theme/image/map_icon_all/start.png')}}">
                        ||&nbsp;&nbsp;&nbsp;&nbsp;End: <img src="{{asset('theme/image/map_icon_all/end.png')}}">
                    </p>
                    
                </div>
                <div class="modal-body" id="note_summary_body" style="height:680px;">
                    <div class="loader" id="note_summary_loader" style="display:none; margin-left:45%;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
     <!-- note image modal start -->
    <div class="modal fade" id="note_image_modal" role="dialog" style="z-index:2;padding-left: 0">
        <div class="modal-dialog modal-lg" style="width: 60%; margin-top: -4%; height: 60%;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Note Image</h4>
                    
                </div>
                <div class="modal-body" id="note_image_modal_body">
                    <div class="loader" id="note_image_modal_load" style="display:none; margin-left:45%;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="attn_loc" role="dialog" data-backdrop="false">
        <div class="modal-dialog modal-lg" style="width:90%;margin-top:-220px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Attendance Location</h4>
                    <p class="modal-title text-center">
                        Start Location:<img src="{{asset('theme/image/map_icon/pv.png')}}">&nbsp;&nbsp;&nbsp;&nbsp;
                        ||&nbsp;&nbsp;&nbsp;&nbsp;Ending Location:<img src="{{asset('theme/image/map_icon/npv.png')}}">
                       
                    </p>
                </div>
                <div class="modal-body" id="attn_loc_body" style="height:680px;">
                    <div class="loader" id="attn_loc_load" style="display:none; margin-left:45%;"></div>
                    <div id="attn_loc_map" style="height:90%;"></div>
                    <div class="modal-footer">
                        
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="modal fade" id="asset_modal" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%;margin-top:-120px;">
        
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Asset Details</h4>
            </div>
            <div class="modal-body" id="asset_dt_table" style="max-height:550px;overflow:auto;">
                    <div class="loader" id="asset_load" style="display:none; margin-left:40%;"></div>
                    <button onclick="exportTableToCSVForCustomItem('asset_outlet_details<?php echo date('Y_m_d'); ?>.csv','asset_dt_table')"
                                                class="btn btn-warning">Export
                    </button>
                    <div class="col-md-3 col-sm-3 col-xs-3" style="float:right;">
                        <input type="text" class="form-control in_tg" id="search_asset_olt"></input>
                    </div>
                    <table class="table table-striped">
                        <thead id="asset_dt_head">
                        <tr>
                            <th>Sl</th>
                            <th>Outlet Name</th>
                            <th>Outlet Mobile</th>
                            <th>Market Name</th>
                            <th>Ward Name</th>
                            <th>Thana Name</th>
                            <th>District Name</th>
                            <th>Olt. Exp</th>
                            <th>Olt. Asset Exp</th>
                        </tr>
                        </thead>
                        <tbody id="asset_dt_cont">

                        </tbody>
                    </table>                      
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        
        </div>
    </div>


    <div class="modal fade" id="asset_olt_year" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Current Year Month Wise Statistics</h4>
            </div>
            <div class="modal-body" id="asset_olt_year_body">
                    <div class="loader" id="asset_olt_year_load" style="display:none; margin-left:40%;"></div>
                    <button onclick="exportTableToCSV('asset_olt_current_year_month_wise_summary<?php echo date('Y_m_d'); ?>.csv','asset_olt_year_body')"
                                                class="btn btn-warning btn-xs">Export
                    </button>
                    <table class="table table-striped">
                        <thead id="asset_olt_year_head">
                        <tr>
                            <th>Sl</th>
                            <th>Month Name</th>
                            <th>Outlet Name</th>
                            <th>Olt. Exp</th>
                            <th>Olt. Asset Exp</th>
                        </tr>
                        </thead>
                        <tbody id="asset_olt_year_cont">

                        </tbody>
                    </table>                      
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        
        </div>
    </div>
    <div class="modal fade" id="asset_olt_than" role="dialog">
        <div class="modal-dialog">
        
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Thana District Wise Statistics</h4>
            </div>
            <div class="modal-body" id="asset_olt_than_body">
                    <div class="loader" id="asset_olt_than_load" style="display:none; margin-left:40%;"></div>
                    <button onclick="exportTableToCSV('asset_olt_current_year_month_wise_summary<?php echo date('Y_m_d'); ?>.csv','asset_olt_than_body')"
                                                class="btn btn-warning btn-xs">Export
                    </button>
                    <table class="table table-striped">
                        <thead id="asset_olt_than_head">
                        <tr>
                            <th>Sl</th>
                            <th>Thana Name</th>
                            <th>District Name</th>
                            <th>Olt. Exp</th>
                            <th>Olt. Asset Exp</th>
                        </tr>
                        </thead>
                        <tbody id="asset_olt_than_cont">

                        </tbody>
                    </table>                      
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        
        </div>
    </div>

    <div class="modal fade" id="note_type" role="dialog">
        <div class="modal-dialog" style="width:80%; margin-top:-100px;">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">Note Details</h4>
            </div>
            <div class="modal-body  " id="note_type_body" style="max-height:550px;overflow:auto;">
                    <!-- <div class="loader" id="asset_olt_year_load" style="display:none; margin-left:40%;"></div> -->
                    <!-- <a onclick="exportTableToExcel(this,'NOTE_DETAILS<?php echo date('Y_m_d'); ?>.xls','m_note_details')"
                                                class="btn btn-warning">Export
                    </a> -->
                    <a onclick="exportTableToCSV('NOTE_DETAILS<?php echo date('Y_m_d'); ?>.csv','note_type_body')"
                                                class="btn btn-warning">Export
                    </a>
                    <div class="loader" id="note_type_load" style="display:none; margin-left:35%;"></div>
                    <div class="cursor-overlay"></div>
                    <div class="preview"></div>
                    <table class="table table-striped" id="m_note_details">
                        <thead id="note_type_head">
                        <tr>
                            <th>Sl</th>
                            <th>NOTE TIME</th>
                            <th>OUTLET NAME </th>
                            <th>NOTE TITLE</th>
                            <th>NOTE BODY</th>
                            <th>NOTE IMAG</th>
                            <th>NOTE TYPE</th>
                        </tr>
                        </thead>
                        <tbody id="note_type_cont">

                        </tbody>
                    </table>                      
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        
        </div>
    </div>

        <script type="text/javascript">
            $('.modal-backdrop').remove()
            $(document.body).removeClass("modal-open");
            $('.commonSelect2').select2();
            $('.multiSelect').multiselect({
                columns  : 1,
                search   : true,
                selectAll: true,
                texts    : {
                    placeholder: 'Select',
                    search     : 'Search'
                }
            });
            $('.commonSelect2').css('style','color:black !important');
            $SIDEBAR_MENU = $('#sidebar-menu')
            $(document).ready(function () {
                setTimeout(function () {
                    $('#menu_toggle').click();
                }, 1);
            });
            $('#rpt').height($("#hierarchy").height());
            function hide_me() {
                $('#dev_note_date').hide();
                $('#period').show();
                $('.div_hide').hide();
                $('.outlet_weekly').hide();

            }
            function printTrip(divName){
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                var newWindow = window.open();
                newWindow.document.write(printContents);
                $("head").append("<style type='text/css' media='print'>@page { size: landscape; font-size: 9px !important; margin: 0;.text_center:text-align:center;}</style>");
                newWindow.print();
               // location.reload();
            }
            function printTrip1(divName){
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                $("head").append("<style type='text/css' media='print'>@page { size: landscape; font-size: 9px !important; margin: 0;}</style>");

                window.print();

                document.body.innerHTML = originalContents;
                location.reload();
            }
            function hideReport() {
                $('#govt_heirarchy').hide();
                $('#tracing_gvt').hide();              
                $('#sales_heirarchy').hide();
                $('#accounts_filter').hide();
                $('#trip_filter').hide();
                $('#history').hide();
                $('.ord_asset').hide();
                $('#sr_report').hide();
                $('#outlet_report').hide();
                $('#trip_report').hide();
                $('#order_report').hide();
                $('#history_report').hide();
                $('#note_report').hide();
                $('#top_bottom').hide();
                $('#deviation_report').hide();
                $('#accounts_report').hide();
                $('.tracing').hide();
                $('#title_head').hide();
                $('#harch').show();
                $('.gvt').hide();
                $('#emp_tracking_report').hide();
                //$('.start_date_div').hide();
                $('#asset_div').hide();
                $('#ord_flag_div').hide();
                $('#dtls_sum_div').hide();
                $('#sr_sv_div').hide();
                $('#sr_zone_div').hide();
                $('.start_date_period_div').show();
                $("input[name='reportType']").attr('checked', false);
                $('#send').removeAttr('onclick');
                $('#send').attr('onclick', 'getSummaryReport()');
                $('.attendance_type_div').hide();              

            }
            function addClass() {
                $('#rp').removeClass('col-md-6 col-sm-6').addClass('col-md-12 col-sm-12 col-xs-12')
                $('#harch').hide();
            }
            function removeClass() {
                $('#rp').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6 col-xs-12')
                $('#harch').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6 col-xs-12')
                $('#hierarchy').removeClass('traking_div_height');
                $('#rpt').height($("#hierarchy").height());
            }
            //Report Type Selection Div Function start
            function getSRReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#sr_report').show();
                $('#sales_heirarchy').show();
                $('#dirg_id_div').show();
                $('.zone_div').show();
                //  $("input[name='reportType']:checked").checked=false;
            }
            function getOutletReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#outlet_report').show();
                $('#sales_heirarchy').show();
                $('.gvt').show();
                $('#single_start_date').hide();
                $('.start_date_period_div').hide();
                $('.zone_div').hide();
                $('#dirg_id_div').hide();
            }
            function getTripReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#trip_report').show();
                $('#trip_filter').show();
            }

            function getOrderReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#order_report').show();
                $('#sales_heirarchy').show();
                $('#dirg_id_div').show();
                $('.zone_div').show();
                // $('.ord_asset').show();
            }
            function getNoteReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#note_report').show();
                $('#sales_heirarchy').show();
                $('#dirg_id_div').show();
                $('.zone_div').show();
            }
            function getTopBottomReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#top_bottom').show();
                $('#sales_heirarchy').show();
                $('#dirg_id_div').show();
                $('.zone_div').show();
            }
            function getDeviationReport() {
                hideReport();
                hide_me();
                removeClass();
                $('#deviation_report').show();
                $('#sales_heirarchy').hide();
                $('.start_date_div').hide();
                $('.start_date_period_div').hide();
                $('#harch').hide();

            }
            function getEmpTrackingReport() {
                //$("input[name='emp_tracking_reportType']").attr('checked', false);
                hideReport();
                hide_me();
                $('#harch').hide();
                $('#emp_tracking_report').show();
            }
            function getAccountReport(){
                hideReport();
                hide_me();
                removeClass();
                $('#accounts_report').show();
                $('#accounts_filter').show();
            }
            //Report Type Selection Div End
            // custom Date field
            function hideShow(){
                $('#start_date_period_div').show();
                $('#single_start_date_div').hide();
            }
            function showCustomDate(val, place) {
                // console.log("hello selection")
                hideShow();
                if (val == '') {
                    if (place == 1) {
                        $('.start_date_div').show();
                        $('.start_date_period_div').hide();
                        $('#send').removeAttr('onclick');
                        $('#send').attr('onclick', 'reportRequestPlacementAdvance()');

                    } else {
                        $('.start_date_div_h').show();
                        $('.start_date_period_div_h').hide();

                    }

                }
                else if(val==-1){
                    $('#start_date_period_div').hide();
                    $('#single_start_date_div').show();
                }

            }
            //Helper function (Load dependent data based on filter) start from here
            function getGroup(slgp_id, place) {
                // clearDate();
                $("#sr_id").empty();
                var _token = $("#_token").val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/report/getGroup",
                    data: {
                        slgp_id: slgp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html='';
                        var gvt_slgp='<option value="">Select</option>'+
                                      '<option value="0">ALL</option>';
                        var list=[];
                        for (var i = 0; i < data.length; i++) {
                          //  console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                            gvt_slgp += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                        }
                        if (place == 1) {
                            $("#sales_group_id").empty();
                            $("#sales_group_id").append(html);
                            $("#sales_group_id").multiselect('reload'); 
                        } else if(place==2) {
                            $("#sales_group_id_h").empty();
                            $("#sales_group_id_h").append(html);
                            $("#sales_group_id_h").multiselect('reload');
                        }
                        else if(place==3){
                            $('#sh_slgp_id').empty();
                            $('#sh_slgp_id').append(gvt_slgp);
                        }
                    }
                });
            }

            function getZone() {
                //clearDate();
                var _token = $("#_token").val();
                var dirg_id = $("#dirg_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/report/getZone_adv",
                    data: {
                        dirg_id: dirg_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $("#zone_id").empty();
                        $("#zone_id1").empty();
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].zone_code + " - " + data[i].zone_name + '</option>';
                        }
                        $("#zone_id").append(html);
                        $("#zone_id").multiselect('reload');
                        $("#zone_id1").append(html);
                        getSRForWeeklyOrder();
                    }
                });
            }
            function getSRForWeeklyOrder() {
                var _token = $("#_token").val();
                var rpt_type = $("input[name='reportType']:checked").val();
                var dirg_id = $('#dirg_id').val();
                var sales_group_id = $('#sales_group_id').val();
                var zone_id = $('#zone_id').val();
                if(rpt_type=='weekly_outlet_summary'){
                   // $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "POST",
                        url: "{{ URL::to('/')}}/report/getSRListModuleTwo_adv",
                        data: {
                            slgp_id: sales_group_id,
                            dirg_id: dirg_id,
                            zone_id: zone_id,
                            _token: _token
                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            $("#sr_id").empty();
                            $('#ajax_load').css("display", "none");
                            var html ='';
                            for (var i = 0; i < data.length; i++) {
                                html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                            }
                            $("#sr_id").append(html);
                            $("#sr_id").multiselect('reload');

                        },
                        error: function(error) {
                            $('#ajax_load').css("display", "none");
                            //console.log(error)
                        }
                    });
                }
                else{
                    return false;
                }
                
            }
            function getSR(id, type) {
                var _token = $("#_token").val();
                var slgp_id=$('#sales_group_id_h').val();
                var dist_id=$('#dist_id_h').val();
                var than_id=$('#than_id_h').val();
                var usr_type=$('#usr_type').val();
                var rpt_type=$("input[name='reportType']:checked").val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/report/getSRListofActivityReport",
                    data: {
                        slgp_id: slgp_id,
                        dist_id: dist_id,
                        than_id: than_id,
                        usr_type:usr_type,
                        rpt_type:rpt_type,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        var html='';
                        var label_text='Select SR';
                        if(rpt_type=='activity_summary'){
                            label_text='Select SV';
                        }               
                        $('#ajax_load').css("display", "none");
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                        }

                        $("#sr_id_h_label").html(label_text);
                        $("#sr_id_h").html(html);
                        $("#sr_id_h").multiselect('reload'); 
                        
                    },error:function(error){
                        $('#ajax_load').css("display", "none");
                        console.log(error)
                    }
                });
            }
            function loadOutlet(market_id, place) {
                var _token = $("#_token").val();
                // var market_id = $("#market_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/json/get/market-wise/outlet",
                    data: {
                        market_id: market_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        if (place == 1) {
                            $("#outlet_id").empty();
                        } else {
                            $("#outlet_id1").empty();
                        }
                        $('#outlet_id').empty();
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select</option>';
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].site_code + " - " + data[i].site_name + '</option>';
                        }
                        if (place == 1) {
                            $("#outlet_id").append(html);
                        } else {
                            $("#outlet_id1").append(html);
                        }

                    }
                });

            }
            // LOAD TRIP
            function getAllTrip() {
                var _token = $("#_token").val();
                var trip_date = $("#trip_date").val();
                var depo_id = $("#depo_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load_trip",
                    data: {
                        trip_date: trip_date,
                        depo_id: depo_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        var html='';
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].TRIP_NO + '">' + data[i].TRIP_NO +' (' +data[i].DM_ID +')'+ '</option>';
                        }
                        $('#trip_no').html(html);

                    }
                });

            }
            function getThanaBelogToDistrict(dist_id, place) {
                getSR(4,4);
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/')}}/json/get/market_open/thana_list",
                    data: {
                        district_id: dist_id
                    },

                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select Thana</option>';
                        if (place == 1) {
                            $("#than_id").empty();
                        } else if (place == 2) {
                            $('#than_id_h').empty();
                        }
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].than_name + '</option>';
                        }
                        if (place == 1) {
                            $("#than_id").append(html);
                        } else if (place == 2) {
                            $("#than_id_h").append(html);

                        }
                    }
                });
            }
            function getWardNameBelogToThana(thana_id, place) {

                //var thana_id = $('#than_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/')}}/json/get/market_open/word_list",
                    data: {
                        thana_id: thana_id
                    },

                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select Ward</option>';
                        if (place == 1) {
                            $("#ward_id").empty();
                        } else if (place == 2) {
                            html += '<option value="all" selected>All</option>';
                            $("#ward_id1").empty();
                        }
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].ward_name + '</option>';
                        }
                        if (place == 1) {
                            $("#ward_id").append(html);
                        } else {
                            $("#ward_id1").append(html);
                        }
                    }
                });
            }
            function loadWardMarket(ward_id, place) {
                clearDate();
                //var ward_id = $('#ward_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/')}}/json/get/ward_wise/market_list",
                    data: {
                        ward_id: ward_id
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select Market</option>';
                        if (place == 1) {
                            $("#market_id").empty();
                        } else if (place == 2) {
                            html += '<option value="all" selected>All</option>';
                            $("#mktm_id1").empty();
                        }
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].market_name + '</option>';
                        }
                        if (place == 1) {
                            $("#market_id").append(html);
                        } else {
                            $("#mktm_id1").append(html);
                        }
                    }
                });
            }
            //Helper function (Load dependent data based on filter)End here
            hide_me();
            hideReport();
            /****************************Employee Tracking Report(Based on Sales Hierarchy and initial phase of gvt hierarchy) Function Start*/
            //initial phase of tracking report based on both hierarchy start
            function getEmpGvtHierarchyWindow(b){
                    hideReport();
                    hide_me();
                    addClass();
                    $('#all_dp_content1').empty();
                    $('#deviation_date_div').hide();
                    $('#tracing_gvt').show();
                    $('.gvt_filter').show();
                    $('#gvt_hierarchy').empty();
            }
            function getDiggingReport(emp_tracking_hierarchy) {
                hideReport();
                hide_me();
                addClass();
                if (emp_tracking_hierarchy == 'emp_tracking_sales_hierarchy') {
                    $('#title_head').show();
                    $('#tracing').show();
                    $('#sh_select_date').show();
                    $('#desig').empty();
                    $('#emid_div').empty();
                    var emid =<?php echo $emid; ?>;
                    var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
                    $('#emid_div').append(emid_cnt);
                    $('#employee_sales_traking_report_slgp').removeAttr('onclick');
                    $('#period').hide();
                    $('#period').removeAttr('onchange');
                    $('#employee_sales_traking_report_slgp').attr('onclick', 'exportTableToCSV("employee_sales_traking_report_slgp_<?php echo date('Y_m_d'); ?>.csv","tableDiv_traking")');
                    $('#period').attr('onchange', 'getDateWiseUserReport(-1)');
                    $.ajax({
                        type: "GET",
                        url: "{{URL::to('/')}}/getTrackingRecord",
                        cache: false,
                        dataType: "json",
                        success: function (data) {
                            $('#tableDiv_traking').show();
                            $('#head_tracking_dev_note_task').empty();
                            var head = '<th>SL</th>' +
                                        '<th>NAME</th>' +
                                        '<th>MOBILE</th>' +
                                        '<th>SR</th>' +
                                        ' <th>OUTLET</th>' +
                                        ' <th>VISITED</th>' +
                                        ' <th>VISIT/SR</th>' +
                                        '<th>NON-VISITED</th>' +
                                        ' <th>ORDER</th>' +
                                        ' <th>ORDER/SR</th>' +
                                        ' <th>S.RATE</th>' +
                                        ' <th>LPC</th>' +
                                        '<th>EXP</th>' +
                                        '<th>EXP/SR</th>' +
                                        '<th>TGT</th>';
                            $('#head_tracking_dev_note_task').append(head);
                            console.log(data);
                            var html = "";
                            var count = 1;
                            var dp_cnt = '';
                            var out_color = '';
                            var visit_color = '';
                            var t_sr = 1;
                            var data_footer='';
                            let foot_t_sr=0;
                            let foot_t_olt=0;
                            let foot_t_visit=0;
                            let foot_t_nvisit=0;
                            let foot_t_memo=0;
                            let foot_t_order_amount=0;
                            let foot_t_total_target=0;
                            let foot_dhbd_line=0;
                            for (var i = 0; i < data.length; i++) {
                                if (data[i]['role_id'] < 6) {
                                    out_color = data[i]['outlet_color'];
                                    visit_color = data[i]['visit_color'];
                                }
                                foot_t_sr+=parseInt(data[i]['totalSr']);
                                foot_t_olt+=parseInt(data[i]['t_outlet']);
                                foot_t_visit+=parseInt(data[i]['total_visited']);
                                foot_t_nvisit+=parseInt(data[i]['t_outlet'] - data[i]['total_visited']);
                                foot_t_memo+=parseInt(data[i]['memo']);
                                foot_t_order_amount+=parseFloat(data[i]['order_amount']);
                                foot_dhbd_line+=parseFloat(data[i]['dhbd_line']);
                                foot_t_total_target+=parseFloat(data[i]['total_target']/26);
                                html += '<tr>' +
                                    '<td>' + count + '</td>' +
                                    '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td>' + data[i]['totalSr'] + '</td>' +
                                    '<td style="color:' + out_color + '">' + data[i]['t_outlet'] + '</td>' +
                                    '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '</td>' +
                                    '<td>' + data[i]['vpsr'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] - data[i]['total_visited']) + '</td>' +
                                    '<td>' + data[i]['memo'] + '</td>' +
                                    '<td>' + data[i]['mpsr'] + '</td>' +
                                    '<td>' + data[i]['strikeRate'] +'%'+ '</td>' +
                                    '<td>' + data[i]['lpc'] + '</td>' +
                                    '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                                    '<td>' + data[i]['expsr'] + '</td>' +
                                    "<td>" + (data[i]['total_target'] / 26).toFixed(2) + "</td>" +
                                    '</tr>';
                                dp_cnt += '<li><a href="#" onclick="testClick(this)" emid="' + data[i]['oid'] + '" role_name="' + data[i]['aemp_name'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                count++;
                            }
                            foot_t_order_amount=foot_t_order_amount>0?foot_t_order_amount.toFixed(2):0;
                            foot_t_total_target=foot_t_total_target>0?foot_t_total_target.toFixed(2):0;
                            let expsr=foot_t_order_amount/foot_t_sr;
                            expsr=expsr>0?expsr.toFixed(2):0;
                            data_footer='<tr><td colspan="11"></td></tr><tr>'+
                                            '<td>GT</td><td></td><td></td>'+
                                            '<td>'+foot_t_sr+'</td>'+
                                            '<td>'+foot_t_olt+'</td>'+
                                            '<td>'+foot_t_visit+'</td>'+
                                            '<td>'+(foot_t_visit/foot_t_sr).toFixed()+'</td>'+
                                            '<td>'+foot_t_nvisit+'</td>'+
                                            '<td>'+foot_t_memo+'</td>'+
                                            '<td>'+(foot_t_memo/foot_t_sr).toFixed()+'</td>'+
                                            '<td>'+(foot_t_memo*100/foot_t_visit).toFixed(2)+'%'+'</td>'+
                                            '<td>'+(foot_dhbd_line/foot_t_memo).toFixed(2)+'</td>'+
                                            '<td>'+foot_t_order_amount+'</td>'+
                                            '<td>'+expsr+'</td>'+
                                            '<td>'+foot_t_total_target+'</td></tr>';
                            $('#cont_traking').empty();
                            $('#all_dp_content').empty();
                            $('#cont_traking').append(html);
                            $('#cont_traking').append(data_footer);
                            $('#all_dp_content').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking").height() + 150);
                        }, error: function (error) {
                            console.log(error);
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                        }
                    });
                } else if (emp_tracking_hierarchy == 'emp_tracking_gvt_hierarchy') {
                    $('#tracing_gvt').show();
                    $('.gvt_filter').show();
                    $('#deviation_date_div').hide();
                    $('#ajax_load').css("display", "block");
                    $('#export_csv_tracking_gvt').removeAttr('onclick');
                    $('#export_details').show();
                    $('#export_csv_tracking_gvt').attr('onclick', 'exportTableToExcel(this,"employee_sales_traking_report_gvt_<?php echo date('Y_m_d'); ?>.xls","tbl_traking_gvt")');
                    $('#emid_div1').empty();
                    var slgp_id=$('#sh_slgp_id').val();
                    var time_period=$('#sh_date_gvt').val();
                    var start_date=$('#sh_date_gvt_single_date').val();
                    var stage=-1;
                    var _token=$('#_token').val();
                    var acmp_id=$('#sh_acmp_id').val();
                    var emid_cnt = '<input type="hidden" id="gvt_hierarchy_id" stage="' + stage +'" slgp_id="'+slgp_id+'"">';
                    $('#emid_div1').append(emid_cnt);
                    $('#sh_date_gvt_single_date').hide();
                    if(time_period==-1){
                        $('#sh_date_gvt_single_date').show();
                    }
                    $.ajax({
                        type: "post",
                        url: "{{URL::to('/')}}/getGvtTrackingRecord",
                        data:{
                            slgp_id:slgp_id,
                            acmp_id:acmp_id,
                            time_period:time_period,
                            start_date:start_date,
                            stage:stage,
                            _token:_token,
                        },
                        success: function (data) {
                            console.log(data)
                            $('#ajax_load').css("display", "none");
                            $('#tableDiv_traking_gvt').show();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            var head1 = '<tr><th>Division Name</th>' +
                                '<th>Total Outlet</th>';
                            var sub_head = '<tr style="font-size:10px;">' +
                                '<th></th>' +
                                '<th></th>';
                            var html = "";
                            var solt_ref='',sv_ref='',sro_ref='',sm_ref='',so_ref='',sd_ref='';
                            var visit=1,s_rate=0,swrv=0,dp_cnt='';
                            var t_solt_ref='',t_vsit_ref='',t_rvst_ref='',t_wvst_ref='',t_memo_ref='',t_ordr_ref='',t_deli_ref='',t_srat_ref='';
                            var res=[];
                            var len=data.data.length;
                            for (var i = 0; i < data.slgp.length; i++) {
                                head1 += '<th colspan="8" style="text-align:center;">' + data.slgp[i]['slgp_name'] + '</th>';
                                sub_head += '<th>Outlet</th>'+'<th>Visit</th>'+'<th>RO-Visit</th>' +'<th>WR-Visit</th>'+ '<th>Memo</th>'+ 
                                            '<th>Exp</th>'+
                                            '<th>Delivery</th>'+ 
                                            '<th>Strike Rate(%)</th>';
                            }
                            head1 += '</tr>';
                            sub_head += '</tr>';
                            var slgp=data.slgp;
                            var data=data.data;
                            for (var i = 0; i <data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['disn_name'] + '</td>' +
                                    '<td>' + data[i]['total_outlet'] + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="0"  id="' +data[i]['disn_id'] + '">' + ' ' + data[i]['disn_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                                for(var j=0;j<slgp.length;j++){
                                        solt_ref='solt'+slgp[j].id;
                                        sv_ref='v'+slgp[j].id;
                                        sro_ref='rv'+slgp[j].id;
                                        sm_ref='m'+slgp[j].id;
                                        sd_ref='d'+slgp[j].id;
                                        so_ref='o'+slgp[j].id;
                                        swrv=data[i][sv_ref]-data[i][sro_ref];
                                        visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                        memo=parseInt(data[i][sm_ref]);
                                        s_rate=(memo*100/visit).toFixed(2);
                                        
                                        html+='<td>'+data[i][solt_ref]+'</td>'+
                                            '<td>'+data[i][sv_ref]+'</td>'+
                                            '<td>'+data[i][sro_ref]+'</td>'+
                                            '<td>'+swrv+'</td>'+
                                            '<td>'+data[i][sm_ref]+'</td>'+
                                            '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                            '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                            '<td>'+s_rate+" %"+'</td>';
                                        t_solt_ref='solt'+slgp[j].id;
                                        t_vsit_ref='vsit'+slgp[j].id;
                                        t_rvst_ref='rvst'+slgp[j].id;
                                        t_wvst_ref='wvst'+slgp[j].id;
                                        t_memo_ref='memo'+slgp[j].id;
                                        t_ordr_ref='ordr'+slgp[j].id;
                                        t_deli_ref='deli'+slgp[j].id;
                                        t_srat_ref='srat'+slgp[j].id;
                                        res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                        res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                        res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                        res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                        res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                        res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                        res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                        res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                                
                                }
                                html+='</tr>'
                            }
                            console.log(res);
                            html+='<tr style="font-weight:bold;">'+
                                    '<td>GT</td>'+
                                    '<td>'+res['tolt']+'</td>';
                            for(var j=0;j<slgp.length;j++){
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                html+='<td>'+res[t_solt_ref]+'</td>'+
                                '<td>'+res[t_vsit_ref]+'</td>'+
                                '<td>'+res[t_rvst_ref]+'</td>'+
                                '<td>'+res[t_wvst_ref]+'</td>'+
                                '<td>'+res[t_memo_ref]+'</td>'+
                                '<td>'+res[t_ordr_ref].toFixed(2)+'</td>'+
                                '<td>'+res[t_deli_ref].toFixed(2)+'</td>'+
                                '<td>'+(res[t_srat_ref]/(len)).toFixed(2)+'</td>';
                            
                            }                                    
                            html+='</tr>';
                            $('#gvt_hierarchy').empty();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            $('#tableDiv_tracking_gvt_header1').append(head1);
                            $('#tableDiv_tracking_gvt_header1').append(sub_head);
                            $('#cont_traking_gvt').empty();
                            $('#cont_traking_gvt').append(html);
                            $('#all_dp_content1').empty();
                            $('#all_dp_content1').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                        },
                        error: function (error) {
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                            console.log(error);
                        }

                    });
                }
            }
            function validateInputField(reportType, acmp_id, sales_group_id,
            ...all
            )
            {

                if (acmp_id == '') {
                    alert("Please Select Company");
                    return false;
                }
                if (sales_group_id == '') {
                    alert("Please Select Group");
                    return false;
                }
                //console.log(all[0]);
                if (reportType == 'market_outlet_sr_outlet') {
                    if (all[0] == '') {
                        alert("Please select District");
                        return false;
                    }
                    if (all[1] == '') {
                        alert("Please select Thana");
                        return false;
                    }
                }
                else {
                    return true;
                }
            }
            // function emptyContentAndAppendData(head,content){
            //     $('#tl_dynamic_head').empty();
            //     $('#tl_dynamic_cont').empty();
            //     $('#tl_dynamic_head').append(head);
            //     $('#tl_dynamic_cont').append(content);
            //     $('#tbl_dynamic').show();
            // }


            function emptyContentAndAppendData(head, content, filename = 'ExportData') {
                $('#tl_dynamic').show();
                var currentDate = '{{ date("Y_m_d") }}';
                var exportLink = '<a onclick="exportTableToExcel(this, \'' + filename + currentDate + '.xls\', \'tl_dynamic\')" class="btn btn-sm" style="background-color:green;color:white;">Excel</a>';
                $('#tl_dynamic_head').empty();
                $('#tl_dynamic_cont').empty();
                $('#export_option_div').empty();
                $('#export_option_div').append(exportLink);
                $('#tl_dynamic_head').append(head);
                $('#tl_dynamic_cont').append(content);
                $('#tl_dynamic').show();
            }

            // function emptyContentAndAppendData(head, content, filename = 'ExportData') {
            //     $('#tl_dynamic').show();
            //     var currentDate = '{{ date("Y_m_d") }}';
            //     var exportLink = '<a onclick="exportTableToExcel(this, \'' + filename + currentDate + '.xls\', \'tl_dynamic\')" class="btn btn-sm" style="background-color:green;color:white;">Excel</a>';
            //     $('#export_option_div').empty().append(exportLink);
            //     $('#tl_dynamic_head').empty().append(head);
            //     $('#tl_dynamic_cont').empty().append(content);
            //     $('#tl_dynamic').show();

            //     // Check if DataTable exists and destroy it
            //      $('#masterTable').DataTable().destroy();
               

            //     // Wait for a short period before reinitializing DataTable
            //     setTimeout(function() {
            //         $('#masterTable').DataTable({
            //                 dom: 'Bfrtip',
            //                 retrieve: false,
            //                 pageLength: 15,
            //                 buttons: [
            //                     'copy', 'csv', 'excel', 'pdf', 'print'
            //                 ]
            //         });
            //     }, 100); // Delay for 100 milliseconds
            // }


            function emptyContentAndAppendDataTrack(head,content){
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#tableDiv_tracking_gvt_header1').append(head);
                $('#cont_traking_gvt').append(content);
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_traking_gvt').excelTableFilter();
                $('.dropdown-filter-item').css('color', 'black');
                $('.dropdown-filter-dropdown').css({'margin-top': '3px', 'height': '23px', 'padding': '0px', 'gap': '1px' });
                $('.arrow-down').css('display', 'none');
                $('.dropdown-filter-icon').css('border', '1px solid white');
                $('th').css({'vertical-align': 'top', 'white-space': 'nowrap', 'text-overflow': 'ellipsis', 'width': '100% !important', 'padding': '2px 10px'});
            }
            function emptyContentAndAppendDataTrack1(head,content){
                $('#head_tracking_dev_note_task').empty();
                $('#cont_traking').empty();
                $('#head_tracking_dev_note_task').append(head);
                $('#cont_traking').append(content);
                $('#tableDiv_traking').show();
                $('#tableDiv_traking').excelTableFilter();
                $('.dropdown-filter-item').css('color', 'black');
                $('.dropdown-filter-dropdown').css({'margin-top': '3px', 'height': '23px', 'padding': '0px', 'gap': '1px' });
                $('.arrow-down').css('display', 'none');
                $('.dropdown-filter-icon').css('border', '1px solid white');
                $('th').css({'vertical-align': 'top', 'white-space': 'nowrap', 'text-overflow': 'ellipsis', 'width': '100% !important', 'padding': '2px 10px'});

            }
            function getTripData(){
                var reportType = $("input[name='reportType']:checked").val();
                var _token = $("#_token").val();
                var coll_date=$('#coll_date').val();
                var depo_id=$('#depo_id').val();
                var trip_no=$('#trip_no').val();
                $('#trip-info-panel').show()

                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/print/trip_data",
                    data: {
                        reportType: reportType,
                        depo_id: depo_id,
                        coll_date: coll_date,
                        trip_no: trip_no,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        switch(reportType){
                            case "trip_print":
                                var html='';
                                $('#trip-summary-print').prop('checked', true);
                                $('#trip-info-panel').show()
                                $('#trip-summery-print-btn').show()
                                let hasTripNumber = $('#trip_no').val();
                                let gt_other_trip=data.gt_other_trip;
                                let gt_trip=data.gt_trip;
                                if(data.trip_data.length > 0){
                                    let info = data.trip_data;
                                    let sum_cash=0;
                                    let sum_cheque=0;
                                    let sum_online=0;
                                    sum_cash=gt_trip.t_Cash+gt_other_trip.t_Cash;
                                    sum_cheque=gt_trip.t_Cheque+gt_other_trip.t_Cheque;
                                    sum_online=gt_trip.t_Online+gt_other_trip.t_Online;
                                    sum_cash=sum_cash?sum_cash.toFixed(2):0;
                                    sum_cheque=sum_cash?sum_cheque.toFixed(2):0;
                                    sum_online=sum_online?sum_online.toFixed(2):0;
                                    $('#employee_sales_traking_report_print_slgp').show()
                                    $('#print-data').show()

                                    $('#print-table-head').html(`
                                        <tr class="text-center">
                                            <td  style="text-align: center">
                                                <span><b>TRIP NO:</b></span>
                                                <span>${info[0].TRIP_NO ?? ''}</span>
                                            </td>
                                            <td >
                                                <span style="margin-left:5px;"><b>TRIP DATE:</b></span>
                                                <span>${info[0].TRIP_DATE ?? ''}</span>
                                            </td>
                                            <td >
                                                <span style="margin-left:5px;"><b>DM ID:</b></span>
                                                <span>${info[0].DM_ID ?? ''}</span>
                                            </td>
                                            <td >
                                                <span style="margin-left:5px;"><b>DM NAME:</b></span>
                                                <span>${info[0].DM_NAME ?? ''}</span>
                                            </td>
                                            <td>
                                                <span style="margin-left:5px;"><b>DEPO NAME:</b></span>
                                                <span>${info[0].DEPOT_NAME ?? ''}</span>
                                            </td>
                                            <td>
                                                <span style="margin-left:5px;"><b>STATUS:</b></span>
                                                <span>${info[0].TRIP_STAT}</span>
                                            </td>
                                        </tr>
                                       
                                    `);
                                    $('#cash_check').html(`
                                            <tr>
                                                <td></td>
                                                <td>
                                                        <span style="margin-left:5px;"><b>Trip Cash:</b></span>
                                                        <span>${gt_trip.t_Cash}</span>
                                                </td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>Trip Cheque:</b></span>
                                                        <span>${gt_trip.t_Cheque}</span>
                                                </td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>Trip Online:</b></span>
                                                        <span>${gt_trip.t_Online}</span>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>Other Cash  :</b></span>
                                                        <span>${gt_other_trip.t_Cash}</span>
                                                </td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>Other Cheque  :</b></span>
                                                        <span>${gt_other_trip.t_Cheque}</span>
                                                </td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>Other Online:</b></span>
                                                        <span>${gt_other_trip.t_Online}</span>
                                                </td>
                                                <td>
                                                    <span style="margin-left:5px;"><b>Other Collection Date:</b></span>
                                                        <span>${data.coll_date}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                        <span style="margin-left:5px;"><b>GT Cash:</b></span>
                                                        <span>${sum_cash}</span>
                                                </td>
                                                <td >
                                                        <span style="margin-left:5px;"><b>GT Cheque:</b></span>
                                                        <span>${sum_cheque}</span>
                                                </td>
                                                <td>
                                                        <span style="margin-left:5px;"><b>GT Online:</b></span>
                                                        <span>${sum_online}</span>
                                                </td>
                                            <td></td>
                                         </tr>
                                    
                                    `)


                                    let info_details = info

                                    let trip_count = 1
                                    let trip_info = ''
                                    let trip_info_head = ''
                                    $('#print-table-head1').html(`
                                        <tr class="text-center" >
                                            <th> SL</th>
                                            <th style="text-align:center;"> SR_ID</th>
                                            <th style="text-align:center;"> SR_NAME</th>
                                            <th style="text-align:center;"> CL_ID</th>
                                            <th style="width:5%; text-align:center;"> OLT_CODE</th>
                                            <th style="width:9%; text-align:center;"> OLT_NAME</th>
                                            <th style="width:6%; text-align:center;"> PARTY<br/>TYPE</th>
                                            <th style="width:9%; text-align:center;"> ORD_NO</th>
                                            <th> ORD<br/>AMNT</th>
                                            <th> CHALAN<br/>AMNT</th>
                                            <th> DELI<br/>AMNT</th>
                                            <th> COLL<br/>AMNT</th>
                                            <th> RTAN</th>
                                            <th> DISC</th>
                                            <th> CASH</th>
                                            <th> CQ</th>
                                            <th> CQ<br/>DATE</th>
                                            <th> ONLINE</th>
                                            <th> CRD<br/>AMNT</th>
                                            <th> CRD <br/>COL_DATE</th>
                                            <th> GRV_GD</th>
                                            <th> GRV_BAD</th>
                                        </tr>`);
                                     var font_type='';
                                    for(let t=0;t<info_details.length;t++){
                                        
                                        if(info_details[t].site_type=='Cr'){
                                            font_type='font-weight:bold';
                                        }else{
                                            font_type='';
                                        }
                                        let party_type = (info_details[t].apr_name) ? `${info_details[t].site_type} - ${info_details[t].apr_name}` : info_details[t].site_type

                                        trip_info +='<tr class="text-center" style="'+font_type+'">'+
                                           ` <td> ${trip_count+t} </td>
                                            <td>${info_details[t].sr_id} </td>
                                            <td>${info_details[t].sr_name ?? ''} </td>
                                            <td>${info_details[t].COLLECTOR_ID ?? ''} </td>
                                            <td>${info_details[t].site_code} </td>
                                            <td>${info_details[t].site_name ?? ''} </td>
                                            <td>${party_type} </td>
                                            <td>${info_details[t].ORDM_ORNM} </td>
                                            <td>${info_details[t].ORDD_AMNT ?? 0} </td>
                                            <td>${info_details[t].INV_AMNT ?? 0} </td>
                                            <td>${info_details[t].DELV_AMNT ?? 0} </td>
                                            <td>${info_details[t].COLLECTION_AMNT ?? 0} </td>
                                            <td>${info_details[t].RTAN_AMNT ?? 0} </td>
                                            <td>${info_details[t].DISCOUNT ?? 0} </td>
                                            <td>${info_details[t].Cash ?? 0} </td>
                                            <td>${info_details[t].Cheque ?? ''} </td>
                                            <td>${info_details[t].check_date ?? ''} </td>
                                            <td>${info_details[t].Online ?? ''} </td>
                                            <td>${info_details[t].CRED_AMNT ?? ''} </td>
                                            <td>${info_details[t].cpcr_cdat ?? ''}</td>
                                            <td>${info_details[t].GD_GRV ?? ''} </td>
                                            <td>${info_details[t].BAD_GRV ?? ''}</td>
                                        </tr>`;
                                    }
                                    trip_info +=`<tr  style="font-weight:bold;">
                                            <td colspan="8">GT</td>
                                            <td>${gt_trip.t_ORDD_AMNT ?? 0} </td>
                                            <td>${gt_trip.t_INV_AMNT ?? 0} </td>
                                            <td>${gt_trip.t_DELV_AMNT ?? 0} </td>
                                            <td>${gt_trip.t_COLLECTION_AMNT ?? 0} </td>
                                            <td>${gt_trip.t_RTAN_AMNT ?? 0} </td>
                                            <td>${gt_trip.t_DISCOUNT ?? 0} </td>
                                            <td>${gt_trip.t_Cash ?? 0} </td>
                                            <td>${gt_trip.t_Cheque ?? ''} </td>
                                            <td> </td>
                                            <td>${gt_trip.t_Online ?? ''} </td>
                                            <td>${gt_trip.t_CRED_AMNT ?? ''} </td>
                                            <td></td>
                                            <td>${gt_trip.t_GD_GRV ?? ''} </td>
                                            <td>${gt_trip.t_BAD_GRV ?? ''}</td>
                                        </tr>`;

                                    $(`#print-table-info`).html(trip_info)

                                }
                                else {
                                    $(`#print-table-info`).html('')
                                }

                                
                                if(data.other_trip_collection_data.length > 0){
                                    $('#other-collection-head').html(`
                                        <tr>
                                            <td colspan="12" style="text-align: center">
                                                <span>Previous Dues Collection</span>
                                            </td>
                                        </tr>
                                    `);


                                    let other_collections = data.other_trip_collection_data

                                    let other_collections_count = 1
                                    let other_collections_info = ''

                                    other_collections_info =`
                                        <tr >
                                            <th> SL</th>
                                            <th> SR_ID</th>
                                            <th> SR_NAME</th>
                                            <th> OLT_CODE</th>
                                            <th> OLT_NAME</th>
                                            <th> PARTY<br/>TYPE</th>
                                            <th> CHALAN<br/>NO</th>
                                            <th> CHALAN<br/>AMNT</th>
                                            <th> CRD<br/>AMNT</th>
                                            <th> CASH</th>
                                            <th> CQ</th>
                                            <th> CQ<br/>DATE</th>
                                            <th> ONLINE</th>
                                        </tr>`;
                                    for(let t=0;t<other_collections.length;t++){

                                        other_collections_info +=`<tr >
                                            <td> ${other_collections_count+t} </td>
                                            <td>${other_collections[t].SR_ID} </td>
                                            <td>${other_collections[t].SR_NAME ?? ''} </td>
                                            <td>${other_collections[t].SITE_CODE} </td>
                                            <td>${other_collections[t].SITE_NAME ?? ''} </td>
                                            <td>${other_collections[t].SITE_TYPE} </td>
                                            <td>${other_collections[t].ORDM_ORNM} </td>
                                            <td>${other_collections[t].INV_AMNT ?? 0} </td>
                                            <td>${other_collections[t].CRED_AMNT ?? 0} </td>
                                            <td>${other_collections[t].CASH ?? ''} </td>
                                            <td>${other_collections[t].CHEQUE ?? 0} </td>
                                            <td>${other_collections[t].CHECK_DATE ?? ''} </td>
                                            <td>${other_collections[t].ONLINE ?? ''} </td>
                                        </tr>`;
                                    }
                                    other_collections_info +=`<tr  style="font-weight:bold;" >
                                            <td colspan="7">GT</td>
                                            <td>${gt_other_trip.t_INV_AMNT ?? 0} </td>
                                            <td>${gt_other_trip.t_CRED_AMNT ?? ''} </td>
                                            <td>${gt_other_trip.t_Cash ?? ''} </td>
                                            <td>${gt_other_trip.t_Cheque ?? 0} </td>
                                            <td></td>
                                            <td>${gt_other_trip.t_Online ?? ''} </td>
                                        </tr>`;

                                    $(`#other-collection-info`).html(other_collections_info)

                                }
                                else {
                                    $(`#other-collection-info`).html('')
                                }
                                $("#print_cont").append(html)
                                break;
                            default :
                            
                               break;
                        }

                    },

                    error:function(error){
                        $('#ajax_load').css("display", "none");
                    }
                });
            }
            function getAccountsData(){
                hide_me();
                var _token = $("#_token").val();
                var reportType = $("input[name='reportType']:checked").val();
                var year_mnth=$('#year_mnth1').val();
                var budget_details_sum=$("input[name='budget_type']:checked").val();
                console.log(budget_details_sum)
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/getAccountsData",
                    data: {
                        _token: _token,
                        reportType: reportType,
                        year_mnth:year_mnth,
                        budget_details_sum:budget_details_sum
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var head='';
                        var html='';
                        var count=1;
                        switch(reportType){
                            case "special_budget":
                                if(budget_details_sum==1){
                                    head=`<tr><th>SL</th>
                                            <th>TIME PERIOD</th>
                                            <th>STAFF ID</th>
                                            <th>STAFF NAME</th>
                                            <th>BUDGET LIMIT</th>
                                            <th>COST</th>
                                            <th>BALANCE</th>
                                            <th>ACTION</th></tr>
                                        `;
                                    for(var i=0;i<data.length;i++){
                                        html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['budget_period'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['spbm_limt'] + '</td>' +
                                                '<td>' + data[i]['spbm_avil'] + '</td>' +
                                                '<td>' + data[i]['spbm_amnt'] + '</td>';
                                        html += "<td><a target='_blank' href='{{ URL::to('/')}}/special_budget/details/" + data[i].spbm_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Details </a></tr>";
                                        count++;
                                    }
                                }
                                else{
                                    head=`<tr><th>SL</th>
                                        <th>ORDER DATE</th>
                                        <th>ORDER NO</th>
                                        <th>ZONE NAME</th>
                                        <th>APPROVER ID</th>
                                        <th>APPROVER NAME</th>
                                        <th>SR ID</th>
                                        <th>SR NAME</th>
                                        <th>ITEM CODE</th>
                                        <th>ITEM NAME</th>
                                        <th>ORDER QTY</th>
                                        <th>ORDER AMNT</th>
                                        <th>REQ DISC</th>
                                        <th>APPROVED AMNT</th></tr>
                                        `;
                                        for(var i=0;i<data.length;i++){
                                        html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['ordm_ornm'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['apr_id'] + '</td>' +
                                                '<td>' + data[i]['apr_name'] + '</td>' +
                                                '<td>' + data[i]['sr_id'] + '</td>'+
                                                '<td>' + data[i]['sr_name'] + '</td>'+
                                                '<td>' + data[i]['amim_code'] + '</td>'+
                                                '<td>' + data[i]['amim_name'] + '</td>'+
                                                '<td>' + data[i]['ordd_inty'] + '</td>'+
                                                '<td>' + data[i]['ordd_oamt'] + '</td>'+
                                                '<td>' + data[i]['ordd_spdi'] + '</td>'+
                                                '<td>' + data[i]['ordd_spdc'] + '</td></tr>';
                                        count++;
                                    }
                                }
                                

                                break;
                            case "credit_budget":
                                if(budget_details_sum==1){
                                    head=`<tr><th>SL</th>
                                            <th>STAFF ID</th>
                                            <th>STAFF NAME</th>
                                            <th>BUDGET LIMIT</th>
                                            <th>COST</th>
                                            <th>BALANCE</th>
                                            <th>ACTION</th></tr>
                                        `;
                                    for(var i=0;i<data.length;i++){
                                        html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['spbm_limt'] + '</td>' +
                                                '<td>' + data[i]['spbm_avil'] + '</td>' +
                                                '<td>' + data[i]['spbm_amnt'] + '</td>';
                                        html += "<td><a target='_blank' href='{{ URL::to('/')}}/credit_budget/details/" + data[i].id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Details </a></tr>";
                                        count++;
                                    }
                                }
                                else{
                                    head=`<tr><th>SL</th>
                                        <th>ORDER DATE</th>
                                        <th>ORDER NO</th>
                                        <th>TRIP NO</th>
                                        <th>DM ID</th>
                                        <th>DELI DATE</th>
                                        <th>SR ID</th>
                                        <th>SR NAME</th>
                                        <th>OUTLET CODE</th>
                                        <th>OUTLET NAME</th>
                                        <th>CREDIT AMNT</th>
                                        <th>APPROVER ID</th>
                                        <th>APPROVER NAME</th>
                                        `;
                                        for(var i=0;i<data.length;i++){
                                        html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ORDM_DATE'] + '</td>' +
                                                '<td>' + data[i]['ORDM_ORNM'] + '</td>' +
                                                '<td>' + data[i]['TRIP_NO'] + '</td>' +
                                                '<td>' + data[i]['DM_CODE'] + '</td>' +
                                                '<td>' + data[i]['ORDM_DRDT'] + '</td>' +
                                                '<td>' + data[i]['SR_ID'] + '</td>'+
                                                '<td>' + data[i]['SR_NAME'] + '</td>'+
                                                '<td>' + data[i]['SITE_CODE'] + '</td>'+
                                                '<td>' + data[i]['SITE_NAME'] + '</td>'+
                                                '<td>' + data[i]['SAPR_AMNT'] + '</td>'+
                                                '<td>' + data[i]['APR_ID'] + '</td>'+
                                                '<td>' + data[i]['APR_NAME'] + '</td></tr>';
                                        count++;
                                    }
                                }
                                break;
                            
                        }
                        emptyContentAndAppendData(head,html);
                    },
                    error:function(error){
                        $('#ajax_load').css("display", "none");
                    }
                });
            }
            function getSummaryReport() {

                hide_me();
                var reportType = $("input[name='reportType']:checked").val();
                var acmp_id = $('#acmp_id').val();
                var dirg_id = $('#dirg_id').val();
                var sales_group_id = $('#sales_group_id').val();
                var zone_id = $('#zone_id').val();
                var time_period = $('#start_date_period').val()
                var start_date = $('#start_date').val()
                var end_date = $('#end_date').val()
                var dist_id = $('#dist_id').val();
                var than_id = $('#than_id').val();
                var astm_id = $('#astm_id').val();
                var _token = $("#_token").val();
                var ord_flag=$('#ord_flag').val();
                var rtype=$('#dtls_sum').val();
                var utype=$('#sr_sv').val();
                var sr_zone=$('#sr_zone').val();
                var start_date=$('#start_date').val();
                var weekly_olt_sr=$('#sr_id').val();
                var year_mnth=$('#year_mnth').val();
                var attendance_type=$('#attendance_type').val();
                var validityCheck = false;
                if (reportType === undefined) {
                    alert('Please select report');
                    return false;
                }
                else if (reportType == '') {
                    alert('Please select report');
                    return false;
                }
                if (reportType == 'market_outlet_sr_outlet') {
                    time_period = $('#start_date').val();
                    validityCheck = validateInputField(reportType, acmp_id, sales_group_id, dist_id, than_id);
                }
                
                else {
                    validityCheck = validateInputField(reportType, acmp_id, sales_group_id);
                    if(reportType=="asset_order"){
                        if(dirg_id==""||zone_id==""){
                            alert("Please select Region & Zone");
                            validityCheck=false;
                        }
                    }
                }

                if (validityCheck != false) {
                        $('#ajax_load').css("display", "block");
                        $.ajax({
                            type: "POST",
                            url: "{{ URL::to('/')}}/getSummaryData",
                            data: {
                                reportType: reportType,
                                acmp_id: acmp_id,
                                zone_id: zone_id,
                                sales_group_id: sales_group_id,
                                time_period: time_period,
                                start_date: start_date,
                                dist_id: dist_id,
                                than_id: than_id,
                                dirg_id: dirg_id,
                                astm_id:astm_id,
                                ord_flag:ord_flag,
                                start_date:start_date,
                                end_date:end_date,
                                utype:utype,
                                rtype:rtype,
                                weekly_olt_sr:weekly_olt_sr,
                                year_mnth:year_mnth,
                                sr_zone:sr_zone,
                                attendance_type:attendance_type,
                                _token: _token
                            },
                            cache: false,
                            dataType: "json",
                            success: function (data) {
                                $('#ajax_load').css("display", "none");
                                console.log(data);
                                var html = '';
                                var count = 1;
                                switch(reportType){
                                    case "zone_summary":
                                        let ft_sr=0;
                                        let ft_psr=0;
                                        let ft_attn_percent=0;
                                        let ft_lviom=0;
                                        let ft_absr=0;
                                        let ft_prodsr=0;
                                        let ft_nonprodsr=0;
                                        let ft_tolt=0;
                                        let ft_visit=0;
                                        let ft_visit_percent=0;
                                        let ft_solt=0;
                                        let ft_srate=0;
                                        let ft_lpc=0;
                                        let ft_avg_sr_k=0;
                                        let ft_olt_sr=0;
                                        let ft_visitsr=0;
                                        let ft_tamnt=0;
                                        var footer="";
                                        
                                        if(rtype==1){
                                            for (var i = 0; i < data.length; i++) {
                                                var visit_percent=(((data[i]['c_outlet']) * 100) / data[i]['t_outlet']).toFixed(2);
                                                    visit_percent=isNaN(visit_percent)?0.00:visit_percent;
                                                    visit_percent=visit_percent>100?100:visit_percent;
                                                var sr_rate=(data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2);
                                                    sr_rate=isNaN(sr_rate)?0.00:sr_rate;
                                                var avg_cont_sr=(data[i]['t_amnt'] / 1000 / data[i]['p_sr']).toFixed(2);
                                                    avg_cont_sr=isNaN(avg_cont_sr)?0.00:avg_cont_sr;
                                                var avg_olt_sr=(data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2);
                                                    avg_olt_sr=isNaN(avg_olt_sr)?0.00:avg_olt_sr;
                                                var avg_visit_sr=(data[i]['c_outlet'] / (data[i]['p_sr'])).toFixed(1);
                                               // let color=visit_percent<40?'color:red':'';
                                                avg_visit_sr=isNaN(avg_visit_sr)?0.00:avg_visit_sr;
                                                ft_sr+=parseInt(data[i]['t_sr']);
                                                ft_psr+=parseInt(data[i]['p_sr']);
                                                ft_lviom+=parseInt(data[i]['l_sr']);
                                                ft_absr+=parseInt(data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']);
                                                ft_prodsr+=parseInt(data[i]['pro_sr']);
                                                ft_tolt+=parseInt(data[i]['t_outlet']);
                                                ft_visit+=parseInt(data[i]['c_outlet']);
                                                ft_solt+=parseInt(data[i]['s_outet']);
                                                ft_lpc+=parseInt(data[i]['lpc']);
                                                ft_tamnt+=parseInt(data[i]['t_amnt'] / 1000);
                                                
                                                html += '<tr>' +
                                                    '<td>' + data[i]['date'] + '</td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                                    '<td>' + data[i]['p_sr'] + '</td>' +
                                                    '<td>' + ((data[i]['p_sr'] * 100) / data[i]['t_sr']).toFixed(2) +'%'+ '</td>' +
                                                    '<td>' + data[i]['l_sr'] + '</td>' +
                                                    '<td>' + (data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']) + '</td>' +
                                                    '<td>' + data[i]['pro_sr'] + '</td>' +
                                                    '<td>' + (data[i]['p_sr'] - data[i]['pro_sr']) + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    "<td>" + (data[i]['c_outlet']) + "</td>" +
                                                    "<td>" + visit_percent+'%'+ "</td>" +
                                                    "<td>" + data[i]['s_outet'] + "</td>" +
                                                    "<td>" + sr_rate +'%'+ "</td>" +
                                                    "<td>" + (data[i]['lpc']) + "</td>" +
                                                    "<td>" +avg_cont_sr+ "</td>" +
                                                    "<td>" + avg_olt_sr + "</td>" +
                                                    "<td>" +avg_visit_sr+ "</td>" +
                                                    "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                                    '</tr>';
                                            }
                                            ft_tamnt=ft_tamnt>0?ft_tamnt.toFixed(2):0;
                                            ft_attn_percent=((ft_psr*100)/ft_sr)>0?((ft_psr*100)/ft_sr).toFixed(2):0;
                                            ft_visit_percent=(ft_visit*100/ft_tolt)>0?(ft_visit*100/ft_tolt).toFixed(2):0;
                                            ft_avg_sr_k=(ft_tamnt/ft_psr)>0?(ft_tamnt/ft_psr).toFixed(2):0;
                                            ft_olt_sr=(ft_tolt/ft_sr)>0?(ft_tolt/ft_sr).toFixed(2):0;
                                            ft_srate=(ft_solt/ft_visit)>0?(ft_solt/ft_visit).toFixed(2):0;
                                            ft_lpc=(ft_lpc/i)>0?(ft_lpc/i).toFixed(2):0;
                                            ft_visitsr=(ft_visit*100)/ft_psr;
                                            ft_visitsr=isNaN(ft_visitsr)?0.00:ft_visitsr.toFixed(2);
                                            ft_visitsr=ft_visitsr>100?100:ft_visitsr;
                                            html+='<tr colspan="19"></tr><tr style="font-weight:bold;">'+
                                                    '<td>GT</td><td></td>'+
                                                    '<td>'+ft_sr+'</td>'+
                                                    '<td>'+ft_psr+'</td>'+
                                                    '<td>'+ft_attn_percent+'%'+'</td>'+
                                                    '<td>'+ft_lviom+'</td>'+
                                                    '<td>'+ft_absr+'</td>'+
                                                    '<td>'+ft_prodsr+'</td>'+
                                                    '<td>'+(ft_psr-ft_prodsr)+'</td>'+
                                                    '<td>'+ft_tolt+'</td>'+
                                                    '<td>'+ft_visit+'</td>'+
                                                    '<td>'+ft_visit_percent+'%'+'</td>'+
                                                    '<td>'+ft_solt+'</td>'+
                                                    '<td>'+ft_srate+'%'+'</td>'+
                                                    '<td>'+ft_lpc+'</td>'+
                                                    '<td>'+ft_avg_sr_k+'</td>'+
                                                    '<td>'+ft_olt_sr+'</td>'+
                                                    '<td>'+ft_visitsr+'</td>'+
                                                    '<td>'+ft_tamnt+'</td>';
                                            

                                        }
                                        else{
                                            console.log("OIIII")
                                            for (var i = 0; i < data.length; i++) {
                                                var visit_percent=(((data[i]['c_outlet']) * 100) / data[i]['t_outlet']).toFixed(2);
                                                    visit_percent=isNaN(visit_percent)?0.00:visit_percent;
                                                    visit_percent=visit_percent>100?100:visit_percent;
                                                var sr_rate=(data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2);
                                                    sr_rate=isNaN(sr_rate)?0.00:sr_rate;
                                                var avg_cont_sr=(data[i]['t_amnt'] / 1000 / data[i]['p_sr']).toFixed(2);
                                                    avg_cont_sr=isNaN(avg_cont_sr)?0.00:avg_cont_sr;
                                                var avg_olt_sr=(data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2);
                                                    avg_olt_sr=isNaN(avg_olt_sr)?0.00:avg_olt_sr;
                                                var avg_visit_sr=(data[i]['c_outlet'] / (data[i]['p_sr'])).toFixed(1);
                                                avg_visit_sr=isNaN(avg_visit_sr)?0.00:avg_visit_sr;
                                               // let color=visit_percent<40?'color:red':'';
                                                ft_sr+=parseInt(data[i]['t_sr']);
                                                ft_psr+=parseInt(data[i]['p_sr']);
                                                ft_lviom+=parseInt(data[i]['l_sr']);
                                                ft_absr+=parseInt(data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']);
                                                ft_prodsr+=parseInt(data[i]['pro_sr']);
                                                ft_tolt+=parseInt(data[i]['t_outlet']);
                                                ft_visit+=parseInt(data[i]['c_outlet']);
                                                ft_solt+=parseInt(data[i]['s_outet']);
                                                ft_lpc+=parseInt(data[i]['lpc']);
                                                ft_tamnt+=parseInt(data[i]['t_amnt'] / 1000);
                                                html += '<tr><td></td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                                    '<td>' + data[i]['p_sr'] + '</td>' +
                                                    '<td>' + ((data[i]['p_sr'] * 100) / data[i]['t_sr']).toFixed(2) +'%'+ '</td>' +
                                                    '<td>' + data[i]['l_sr'] + '</td>' +
                                                    '<td>' + (data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']) + '</td>' +
                                                    '<td>' + data[i]['pro_sr'] + '</td>' +
                                                    '<td>' + (data[i]['p_sr'] - data[i]['pro_sr']) + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    "<td>" + (data[i]['c_outlet']) + "</td>" +
                                                    "<td>" + visit_percent+'%'+ "</td>" +
                                                    "<td>" + data[i]['s_outet'] + "</td>" +
                                                    "<td>" + sr_rate +'%'+ "</td>" +
                                                    "<td>" + (data[i]['lpc']) + "</td>" +
                                                    "<td>" +avg_cont_sr+ "</td>" +
                                                    "<td>" + avg_olt_sr + "</td>" +
                                                    "<td>" +avg_visit_sr+ "</td>" +
                                                    "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                                    '</tr>';
                                            }
                                            ft_tamnt=ft_tamnt>0?ft_tamnt.toFixed(2):0;
                                            ft_attn_percent=((ft_psr*100)/ft_sr)>0?((ft_psr*100)/ft_sr).toFixed(2):0;
                                            ft_visit_percent=(ft_visit*100/ft_tolt)>0?(ft_visit*100/ft_tolt).toFixed(2):0;
                                            ft_avg_sr_k=(ft_tamnt/ft_psr)>0?(ft_tamnt/ft_psr).toFixed(2):0;
                                            ft_olt_sr=(ft_tolt/ft_sr)>0?(ft_tolt/ft_sr).toFixed(2):0;
                                            ft_srate=(ft_solt/ft_visit)>0?(ft_solt/ft_visit).toFixed(2):0;
                                            ft_lpc=(ft_lpc/i)>0?(ft_lpc/i).toFixed(2):0;
                                            ft_visitsr=(ft_visit*100)/ft_psr;
                                            ft_visitsr=isNaN(ft_visitsr)?0.00:ft_visitsr.toFixed(2);
                                            ft_visitsr=ft_visitsr>100?100:ft_visitsr;
                                            html+='<tr colspan="19"></tr><tr style="font-weight:bold;">'+
                                                    '<td>GT</td><td></td>'+
                                                    '<td>'+ft_sr+'</td>'+
                                                    '<td>'+ft_psr+'</td>'+
                                                    '<td>'+ft_attn_percent+'%'+'</td>'+
                                                    '<td>'+ft_lviom+'</td>'+
                                                    '<td>'+ft_absr+'</td>'+
                                                    '<td>'+ft_prodsr+'</td>'+
                                                    '<td>'+(ft_psr-ft_prodsr)+'</td>'+
                                                    '<td>'+ft_tolt+'</td>'+
                                                    '<td>'+ft_visit+'</td>'+
                                                    '<td>'+ft_visit_percent+'%'+'</td>'+
                                                    '<td>'+ft_solt+'</td>'+
                                                    '<td>'+ft_srate+'%'+'</td>'+
                                                    '<td>'+ft_lpc+'</td>'+
                                                    '<td>'+ft_avg_sr_k+'</td>'+
                                                    '<td>'+ft_olt_sr+'</td>'+
                                                    '<td>'+ft_visitsr+'</td>'+
                                                    '<td>'+ft_tamnt+'</td>';
                                        }
                                        var head=` <th>Date</th>
                                            <th>Zone Name</th>
                                            <th>T.SR</th>
                                            <th>Pr.SR</th>
                                            <th>Attn %</th>
                                            <th>LV/IOM</th>
                                            <th>Ab.SR</th>
                                            <th>P.SR</th>
                                            <th>Np.SR</th>
                                            <th>T.Olt</th>
                                            <th>Visit</th>
                                            <th>Visit %</th>
                                            <th>S.Olt</th>
                                            <th>S.Rate %</th>
                                            <th>LPC</th>
                                            <th>Avg.SR(K)</th>
                                            <th>Olt/SR</th>
                                            <th>V.Olt/SR</th>
                                            <th>Amount(K)</th>`;
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_productivity":
                                        var head="";
                                        let summary=data.summary;
                                        var data=data.data;
                                        if(rtype==3){
                                            head='<th>Zone Name</th>'+
                                                '<th>SV Name</th>'+
                                                '<th>T.SR</th>'+
                                                '<th>PRD.SR</th>'+
                                                '<th>T.Olt</th>'+
                                                '<th>Visit</th>'+
                                                '<th>Visit%</th>'+
                                                '<th>RO Visit</th>'+
                                                '<th>WR Visit</th>'+
                                                '<th>S.Olt</th>'+
                                                '<th>S.Rate%</th>'+
                                                '<th>Np.Olt</th>'+
                                                '<th>Order(K)</th>'+
                                                '<th>Avg.Olt (K)</th>'+
                                                '<th>LPC</th>';
                                            for (var i = 0; i < data.length; i++) {
                                                let t_out=1;
                                                let visit_percent=0;
                                                let c_out=1;
                                                let s_rate=0;
                                                let color="";
                                                if(data[i]['t_outlet']>0){
                                                    t_out=data[i]['t_outlet'];
                                                }
                                                if(data[i]['c_outlet']>0){
                                                    c_out=data[i]['c_outlet'];
                                                }
                                                s_rate= (data[i]['s_outet']*100)/c_out;
                                                visit_percent=(data[i]['c_outlet']*100)/t_out;
                                                s_rate=(s_rate>100?100:s_rate||0).toFixed(2);
                                                visit_percent=(visit_percent>100?100:visit_percent||0).toFixed(2);
                                            
                                                html += '<tr>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mngr'] + '</td>' +
                                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                                    '<td>' + data[i]['aemp_id'] + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    '<td>' + data[i]['c_outlet'] + '</td>' +
                                                    '<td>' +visit_percent+'%'+ '</td>' +
                                                    '<td>' +data[i]['ro_visit']+ '</td>' +
                                                    '<td>' +(data[i]['c_outlet']-data[i]['ro_visit'])+ '</td>' +
                                                    '<td>' + data[i]['s_outet'] + "</td>" +
                                                    '<td>' + s_rate+'%' + '</td>' +
                                                    '<td>' + (data[i]['c_outlet'] - data[i]['s_outet']) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['lpc']) + '</td>' +
                                                    '</tr>';
                                            }
                                            html+='<tr><tr><tr>'+
                                                '<td>GT</td><td></td>'+
                                                '<td>' + summary['total_sr'] +'</td>'+
                                                '<td>' + summary['total_psr'] +'</td>'+
                                                '<td>'+summary['total_outlet']+'</td>'+
                                                    '<td>'+summary['total_visit']+'</td>'+
                                                    '<td>'+summary['total_visit_percentage']+'%'+'</td>'+
                                                    '<td>'+summary['total_ro_visit']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_ro_visit'])+'</td>'+
                                                    '<td>'+summary['total_s_outlet']+'</td>'+
                                                    '<td>'+summary['total_strikeRate']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_s_outlet'])+'</td>'+
                                                    '<td>'+summary['total_amount']+'</td>'+
                                                    '<td>'+(summary['total_amount']/summary['total_s_outlet']).toFixed(2)+'</td>'+
                                                    '<td>'+summary['total_lpc']+'</td></tr>';
                                        }
                                        else if(rtype==1 && utype==1){
                                            head='<th>Date</th>'+
                                                '<th>Zone Name</th>'+
                                                '<th>Base Name</th>'+
                                                '<th>SV Name</th>'+
                                                '<th>SR ID</th>'+
                                                '<th>SR Name</th>'+
                                                '<th>SR Mobile</th>'+
                                                '<th>Route Name</th>'+
                                                '<th>R.Olt</th>'+
                                                '<th>Visit</th>'+
                                                '<th>Visit%</th>'+
                                                '<th>RO Visit</th>'+
                                                '<th>WR Visit</th>'+
                                                '<th>S.Olt</th>'+
                                                '<th>S.Rate%</th>'+
                                                '<th>Np.Olt</th>'+
                                                '<th>Order(K)</th>'+
                                                '<th>Avg.Olt (K)</th>'+
                                                '<th>LPC</th>'+
                                                '<th>In Time</th>'+
                                                '<th>First Visit</th>'+
                                                '<th>Last Visit</th>'+
                                                '<th>Work Time</th>';
                                            
                                            for (var i = 0; i < data.length; i++) {
                                                var c_percent= data[i]['c_percentage']>100?100: data[i]['c_percentage'];
                                                let color="";
                                                html += '<tr>' +
                                                    '<td>' + data[i]['date'] + '</td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['base_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mngr'] + '</td>' +
                                                    '<td>' + data[i]['aemp_id'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mobile'] + '</td>' +
                                                    '<td>' + data[i]['rout_name'] + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    '<td>' + data[i]['c_outlet'] + '</td>' +
                                                    '<td>' +c_percent+'%'+ '</td>' +
                                                    '<td>' +data[i]['ro_visit']+'</td>' +
                                                    '<td>' +(data[i]['c_outlet']-data[i]['ro_visit'])+'</td>' +
                                                    '<td>' + data[i]['s_outet'] + "</td>" +
                                                    '<td>' + data[i]['strikeRate']+'%' + '</td>' +
                                                    '<td>' + (data[i]['c_outlet'] - data[i]['s_outet']) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['lpc']) + '</td>' +
                                                    '<td>' + (data[i]['inTime']) + '</td>' +
                                                    '<td>' + (data[i]['firstOrTime']) + '</td>' +
                                                    '<td>' + (data[i]['lastOrTime']) + '</td>' +
                                                    '<td>' + (data[i]['workTime']) + '</td>' +
                                                    '</tr>';
                                                    count++;
                                            }
                                            html+='<tr><tr><tr>'+
                                                    '<td>GT</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>'+
                                                    '<td>'+summary['total_outlet']+'</td>'+
                                                    '<td>'+summary['total_visit']+'</td>'+
                                                    '<td>'+summary['total_visit_percentage']+'%'+'</td>'+
                                                    '<td>'+summary['total_ro_visit']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_ro_visit'])+'</td>'+
                                                    '<td>'+summary['total_s_outlet']+'</td>'+
                                                    '<td>'+summary['total_strikeRate']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_s_outlet'])+'</td>'+
                                                    '<td>'+summary['total_amount']+'</td>'+
                                                    '<td>'+(summary['total_amount']/summary['total_s_outlet']).toFixed(2)+'</td>'+
                                                    '<td>'+summary['total_lpc']+'</td></tr>';

                                        }
                                        else if(rtype==2 && utype==1){
                                            head='<th>Zone Name</th>'+
                                                '<th>SV Name</th>'+
                                                '<th>SR ID</th>'+
                                                '<th>SR Name</th>'+
                                                '<th>SR Mobile</th>'+
                                                '<th>T.Olt</th>'+
                                                '<th>Visit</th>'+
                                                '<th>Visit%</th>'+
                                                '<th>RO Visit</th>'+
                                                '<th>WR Visit</th>'+
                                                '<th>S.Olt</th>'+
                                                '<th>S.Rate%</th>'+
                                                '<th>Np.Olt</th>'+
                                                '<th>Order(K)</th>'+
                                                '<th>Avg.Olt (K)</th>'+
                                                '<th>LPC</th>';                                            
                                            for (var i = 0; i < data.length; i++) {
                                                let t_out=1;
                                                let visit_percent=0;
                                                let c_out=1;
                                                let s_rate=0;
                                                let color="";
                                                if(data[i]['t_outlet']>0){
                                                    t_out=data[i]['t_outlet'];
                                                }
                                                if(data[i]['c_outlet']>0){
                                                    c_out=data[i]['c_outlet'];
                                                }
                                                s_rate= (data[i]['s_outet']*100)/c_out;
                                                visit_percent=(data[i]['c_outlet']*100)/t_out;
                                                s_rate=(s_rate>100?100:s_rate||0).toFixed(2);
                                                visit_percent=(visit_percent>100?100:visit_percent||0).toFixed(2);
                                                // if(visit_percent<40){
                                                //     color="color:red;";
                                                // }else{
                                                //     color=" ";
                                                // }
                                                html += '<tr>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mngr'] + '</td>' +
                                                    '<td>' + data[i]['aemp_id'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mobile'] + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    '<td>' + data[i]['c_outlet'] + '</td>' +
                                                    '<td>' +visit_percent+'%'+ '</td>' +
                                                    '<td>' +data[i]['ro_visit']+ '</td>' +
                                                    '<td>' +(data[i]['c_outlet']-data[i]['ro_visit'])+ '</td>' +
                                                    '<td>' + data[i]['s_outet'] + "</td>" +
                                                    '<td>' + s_rate+'%' + '</td>' +
                                                    '<td>' + (data[i]['c_outlet'] - data[i]['s_outet']) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['lpc']) + '</td>' +
                                                    '</tr>';
                                            }
                                            html+='<tr><tr><tr>'+
                                                '<td>GT</td><td></td><td></td><td></td><td></td>'+
                                                '<td>'+summary['total_outlet']+'</td>'+
                                                    '<td>'+summary['total_visit']+'</td>'+
                                                    '<td>'+summary['total_visit_percentage']+'%'+'</td>'+
                                                    '<td>'+summary['total_ro_visit']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_ro_visit'])+'</td>'+
                                                    '<td>'+summary['total_s_outlet']+'</td>'+
                                                    '<td>'+summary['total_strikeRate']+'</td>'+
                                                    '<td>'+(summary['total_visit']-summary['total_s_outlet'])+'</td>'+
                                                    '<td>'+summary['total_amount']+'</td>'+
                                                    '<td>'+(summary['total_amount']/summary['total_s_outlet']).toFixed(2)+'</td>'+
                                                    '<td>'+summary['total_lpc']+'</td></tr>';

                                        }
                                        else if(rtype==1 && utype==2){
                                            head='<th>DATE</th>'+
                                                '<th>ZONE NAME</th>'+
                                                '<th>MANAGER ID</th>'+
                                                '<th>MANAGER NAME</th>'+
                                                '<th>DESIGNATION</th>'+
                                                '<th>VISIT</th>'+
                                                '<th>C.OLT</th>'+
                                                '<th>ORDER</th>'+
                                                '<th>EXP</th>'+
                                                '<th>EXP/OLT</th>'+
                                                '<th>LPC</th>';
                                            for (var i = 0; i < data.length; i++) {
                                                var tmemo=data[i]['memo']==0?1:data[i]['memo'];
                                                let avg_amnt=(data[i]['ordm_amnt']/tmemo)/1000;
                                                let lpc=data[i]['item_count']/tmemo;
                                                if(avg_amnt !=null && avg_amnt>0){
                                                    avg_amnt=avg_amnt.toFixed(2);
                                                }
                                                if(lpc !=null && lpc>0){
                                                    lpc=lpc.toFixed(2);
                                                }
                                                html += '<tr>' +
                                                    '<td>' + data[i]['ordm_date'] + '</td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['edsg_name'] + '</td>' +
                                                    '<td>' + data[i]['olt_visit'] + '</td>' +
                                                    '<td>' + data[i]['olt_cov'] + '</td>' +
                                                    '<td>' + data[i]['memo'] + '</td>' +
                                                    '<td>' + (data[i]['ordm_amnt']/1000).toFixed(2) + '</td>' +
                                                    '<td>' + avg_amnt + '</td>' +
                                                    '<td>' +lpc + '</td>'+
                                                    '</tr>';
                                            }
                                            
                                            html+='<tr><td colspan="9"></td><tr><tr style="color:black;font-weight:bold;">'+
                                                    '<td>GT</td><td></td><td></td><td></td><td></td>'+
                                                    '<td>'+summary['total_visit']+'</td>'+
                                                    '<td>'+summary['total_site']+'</td>'+
                                                    '<td>'+summary['total_memo']+'</td>'+
                                                    '<td>'+summary['total_amount']+'</td>'+
                                                    '<td>'+summary['avg_olt_order']+'</td>'+
                                                    '<td>'+summary['total_lpc']+'</td>';
                                                    
                                        }
                                        else if(rtype==2 && utype==2){
                                            head= '<th>ZONE NAME</th>'+
                                                '<th>MANAGER ID</th>'+
                                                '<th>MANAGER NAME</th>'+
                                                '<th>DESIGNATION</th>'+
                                                '<th>VISIT</th>'+
                                                '<th>C.OLT</th>'+
                                                '<th>ORDER</th>'+
                                                '<th>EXP</th>'+
                                                '<th>EXP/OLT</th>'+
                                                '<th>LPC</th>';
                                              
                                            for (var i = 0; i < data.length; i++) {
                                                var tmemo=data[i]['memo']==0?1:data[i]['memo'];
                                                let avg_amnt=(data[i]['ordm_amnt']/tmemo)/1000;
                                                let lpc=data[i]['item_count']/tmemo;
                                                if(avg_amnt !=null && avg_amnt>0){
                                                    avg_amnt=avg_amnt.toFixed(2);
                                                }
                                                if(lpc !=null && lpc>0){
                                                    lpc=lpc.toFixed(2);
                                                }
                                                
                                                html += '<tr>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['edsg_name'] + '</td>' +
                                                    '<td>' + data[i]['olt_visit'] + '</td>' +
                                                    '<td>' + data[i]['olt_cov'] + '</td>' +
                                                    '<td>' + data[i]['memo'] + '</td>' +
                                                    '<td>' + (data[i]['ordm_amnt']/1000).toFixed(2) + '</td>' +
                                                    '<td>' + avg_amnt + '</td>' +
                                                    '<td>' +lpc + '</td>'+
                                                    '</tr>';
                                            }
                                            html+='<tr><td colspan="9"></td><tr><tr style="color:black;font-weight:bold;">'+
                                                    '<td>GT</td><td></td><td></td><td></td>'+
                                                    '<td>'+summary['total_visit']+'</td>'+
                                                    '<td>'+summary['total_site']+'</td>'+
                                                    '<td>'+summary['total_memo']+'</td>'+
                                                    '<td>'+summary['total_amount']+'</td>'+
                                                    '<td>'+summary['avg_olt_order']+'</td>'+
                                                    '<td>'+summary['total_lpc']+'</td>';
                                                    
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_productivity_summary":
                                        var head=` <th>Zone Name</th>
                                                    <th>Base Name</th>
                                                    <th>SV Name</th>
                                                    <th>SR ID</th>
                                                    <th>SR Name</th>
                                                    <th>SR Mobile</th>
                                                    <th>T.Olt</th>
                                                    <th>Visit</th>
                                                    <th>Visit%</th>
                                                    <th>S.Olt</th>
                                                    <th>S.Rate%</th>
                                                    <th>Np.Olt</th>
                                                    <th>Order(K)</th>
                                                    <th>Avg.Olt (K)</th>
                                                    <th>LPC</th>`;
                                        var sr_rate=0;
                                            for (var i = 0; i < data.length; i++) {

                                            var c_percent=(data[i]['c_outlet']*100/data[i]['t_outlet']).toFixed(2);
                                            c_percent=c_percent>100?100:c_percent;
                                            var sr_rate= data[i]['s_outet']*100/data[i]['c_outlet'];
                                                    sr_rate=sr_rate?sr_rate.toFixed(2):0.00;
                                                html += '<tr>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['base_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mngr'] + '</td>' +
                                                    '<td>' + data[i]['aemp_id'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mobile'] + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    '<td>' + data[i]['c_outlet'] + '</td>' +
                                                    '<td>' +c_percent+ '</td>' +
                                                    '<td>' + data[i]['s_outet'] + "</td>" +
                                                    '<td>' + sr_rate + '</td>' +
                                                    '<td>' + (data[i]['c_outlet'] - data[i]['s_outet']) + '</td>' +

                                                    '<td>' + (data[i]['t_amnt'] / 1000).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['t_amnt'] / 1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                                    '<td>' + (data[i]['t_sku']/data[i]['s_outet']).toFixed(2) + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_summary_by_group":
                                        var head=`<th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>T.SR</th>
                                            <th>Pr.SR</th>
                                            <th>Attn %</th>
                                            <th>LV/IOM</th>
                                            <th>Ab SR</th>
                                            <th>P.SR</th>
                                            <th>Np.SR</th>
                                            <th>T.Olt</th>
                                            <th>Visit</th>
                                            <th>Visit %</th>
                                            <th>S.Olt</th>
                                            <th>S.Rate %</th>
                                            <th>LPC</th>
                                            <th>Avg.SR(K)</th>
                                            <th>Olt/SR</th>
                                            <th>V.Olt/SR</th>
                                            <th>Amount in (K)</th>`;
                                            for (var i = 0; i < data.length; i++) {

                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['date'] + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                                    '<td>' + data[i]['p_sr'] + '</td>' +
                                                    '<td>' + ((data[i]['p_sr'] * 100) / data[i]['t_sr']).toFixed(2) + '</td>' +
                                                    '<td>' + data[i]['l_sr'] + '</td>' +
                                                    '<td>' + (data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']) + '</td>' +
                                                    '<td>' + data[i]['pro_sr'] + '</td>' +
                                                    '<td>' + (data[i]['p_sr'] - data[i]['pro_sr']) + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    "<td>" + (data[i]['c_outlet']) + "</td>" +
                                                    "<td>" + (((data[i]['c_outlet']) * 100) / data[i]['t_outlet']).toFixed(2) + "</td>" +
                                                    "<td>" + data[i]['s_outet'] + "</td>" +
                                                    "<td>" + (data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2) + "</td>" +
                                                    "<td>" + (data[i]['lpc']) + "</td>" +
                                                    "<td>" + (data[i]['t_amnt'] / 1000 / data[i]['p_sr']).toFixed(2) + "</td>" +
                                                    "<td>" + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + "</td>" +
                                                    "<td>" + (data[i]['c_outlet'] / (data[i]['p_sr'])).toFixed(2) + "</td>" +
                                                    "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                                    '</tr>';
                                                count++;
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_non_productivity":
                                            var head=`<th>SI</th>
                                                    <th>Date</th>
                                                    <th>Group Name</th>
                                                    <th>Region Name</th>
                                                    <th>Zone Name</th>
                                                    <th>SV Name</th>
                                                    <th>SR ID</th>
                                                    <th>SR Name</th>
                                                    <th>SR Mobile</th>
                                                    <th>In Time</th>`;
                                            for (var i = 0; i < data.length; i++) {

                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['date'] + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['dirg_name'] + '</td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mngr'] + '</td>' +
                                                    '<td>' + data[i]['aemp_id'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mobile'] + '</td>' +

                                                    '<td>' + (data[i]['inTime']) + '</td>' +

                                                    '</tr>';
                                                count++;
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_wise_item_summary_quatar":
                                        var count = 1;
                                        var head='';
                                        let module=data.module;
                                        var data=data.rp_data;
                                        if(module==1){
                                            head='<th>SI</th>'+
                                                '<th>Date</th>'+
                                                '<th>Group</th>'+
                                                '<th>SR Name</th>'+
                                                '<th>Item Name</th>'+
                                                '<th>Ctn</th>'+
                                                '<th>Pics</th>'+
                                                '<th>Discount</th>'+
                                                '<th>Amount</th>';
                                            for (var i = 0; i < data.length; i++) {

                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['ordm_date'] + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['user_name'] + '</td>' +
                                                    '<td>' + data[i]['item_name'] + '</td>' +
                                                    '<td>' + data[i]['order_qnty'] + '</td>' +
                                                    '<td>' + data[i]['pics'] + '</td>' +
                                                    '<td>' + data[i]['discount'] + '</td>' +
                                                    '<td>' + data[i]['order_amnt'] + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                        }
                                        else{
                                            head='<th>SI</th>'+
                                                '<th>Date</th>'+
                                                '<th>Group</th>'+
                                                '<th>Depot</th>'+
                                                '<th>SV Name</th>'+
                                                '<th>SR Name</th>'+
                                                '<th>Item Name</th>'+
                                                '<th>Ctn</th>'+
                                                '<th>Pics</th>'+
                                                '<th>Discount</th>'+
                                                '<th>Amount</th>';
                                            for (var i = 0; i < data.length; i++) {

                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['ordm_date'] + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['dpot_name'] + '</td>' +
                                                    '<td>' + data[i]['sv_name'] + '</td>' +
                                                    '<td>' + data[i]['user_name'] + '</td>' +
                                                    '<td>' + data[i]['item_name'] + '</td>' +
                                                    '<td>' + data[i]['order_qnty'] + '</td>' +
                                                    '<td>' + data[i]['pics'] + '</td>' +
                                                    '<td>' + data[i]['discount'] + '</td>' +
                                                    '<td>' + data[i]['order_amnt'] + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_wise_order_details":
                                        var head=`<th>SI</th>
                                            <th>Date</th>
                                            <th>Zone</th>
                                            <th>SV ID</th>
                                            <th>SV NAME</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Item Subcat</th>
                                            <th>Item Factor</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Order Time</th>`;
                                            var count = 1;
                                        for (var i = 0; i < data.length; i++) {

                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['sv_id'] + '</td>' +
                                                '<td>' + data[i]['sv_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['site_code'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['amim_code'] + '</td>' +
                                                '<td>' + data[i]['amim_name'] + '</td>' +
                                                '<td>' + data[i]['itsg_name'] + '</td>' +
                                                '<td>' + data[i]['amim_duft'] + '</td>' +
                                                '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                                '<td>' + data[i]['ordd_oamt'] + '</td>' +
                                                '<td>' + data[i]['ordm_time'] + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_activity_hourly_order":
                                            $('#sr_activity_hourly_order_header').empty();
                                            if (time_period == 0) {
                                                count = 0;
                                                var dim = data[0].length;
                                                for (var i = 0; i < data.length; i++) {
                                                    if (i == 0) {
                                                        html += '<tr class="tbl_header">' + '<td>' + "Sl" + '</td>';
                                                    } else {
                                                        html += '<tr>' + '<td>' + count + '</td>';
                                                    }

                                                    for (var j = 0; j < data[0].length; j++) {
                                                        html += '<td>' + data[i][j] + '</td>';
                                                    }
                                                    //alert(data[i][0]);

                                                    html += '</tr>';
                                                    count++;
                                                }
                                            }
                                            else {
                                                var head = '<th>Date</th><th>Group</th>' +
                                                    '<th>Zone</th>' +
                                                    '<th>SR ID</th>' +
                                                    '<th>SR Name</th>' +
                                                    '<th>SR Mobile</th>' +
                                                    '<th>09AM</th>' +
                                                    '<th>10AM</th>' +
                                                    '<th>11AM</th>' +
                                                    '<th>12PM</th>' +
                                                    '<th>01PM</th>' +
                                                    '<th>02PM</th>' +
                                                    '<th>03PM</th>' +
                                                    '<th>04PM</th>' +
                                                    '<th>05PM</th>' +
                                                    '<th>06PM</th>' +
                                                    '<th>07PM</th>' +
                                                    '<th>08PM</th>' +
                                                    '<th>09PM</th>';
                                                $('#sr_activity_hourly_order_header').append(head);
                                                for (var i = 0; i < data.length; i++) {
                                                    html += '<tr>' +
                                                        '<td>' + data[i]['act_date'] + '</td>' +
                                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                        '<td>' + data[i]['9am'] + '</td>' +
                                                        '<td>' + data[i]['10am'] + '</td>' +
                                                        '<td>' + data[i]['11am'] + '</td>' +
                                                        '<td>' + data[i]['12pm'] + '</td>' +
                                                        '<td>' + data[i]['1pm'] + '</td>' +
                                                        '<td>' + data[i]['2pm'] + '</td>' +
                                                        '<td>' + data[i]['3pm'] + '</td>' +
                                                        '<td>' + data[i]['4pm'] + '</td>' +
                                                        '<td>' + data[i]['5pm'] + '</td>' +
                                                        '<td>' + data[i]['6pm'] + '</td>' +
                                                        '<td>' + data[i]['7pm'] + '</td>' +
                                                        '<td>' + data[i]['8pm'] + '</td>' +
                                                        '<td>' + data[i]['9pm'] + '</td>';
                                                }
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_activity_hourly_visit_bk":
                                            var head='';
                                            $('#sr_activity_hourly_visit_header').empty();
                                            if (time_period == 0) {
                                                count = 0;
                                                var dim = data[0].length;
                                                for (var i = 0; i < data.length; i++) {
                                                    if (i == 0) {
                                                        html += '<tr class="tbl_header">' + '<td>' + "Sl" + '</td>';
                                                    } else {
                                                        html += '<tr>' + '<td>' + count + '</td>';
                                                    }

                                                    for (var j = 0; j < data[0].length; j++) {
                                                        html += '<td>' + data[i][j] + '</td>';
                                                    }
                                                    //alert(data[i][0]);

                                                    html += '</tr>';
                                                    count++;
                                                }
                                            }
                                            else {
                                                var head = '<th>Date</th><th>Group</th>' +
                                                    '<th>Zone</th>' +
                                                    '<th>SR ID</th>' +
                                                    '<th>SR Name</th>' +
                                                    '<th>SR Mobile</th>' +
                                                    '<th>09AM</th>' +
                                                    '<th>10AM</th>' +
                                                    '<th>11AM</th>' +
                                                    '<th>12PM</th>' +
                                                    '<th>01PM</th>' +
                                                    '<th>02PM</th>' +
                                                    '<th>03PM</th>' +
                                                    '<th>04PM</th>' +
                                                    '<th>05PM</th>' +
                                                    '<th>06PM</th>' +
                                                    '<th>07PM</th>' +
                                                    '<th>08PM</th>' +
                                                    '<th>09PM</th>';
                                                for (var i = 0; i < data.length; i++) {
                                                    html += '<tr>' +
                                                        '<td>' + data[i]['act_date'] + '</td>' +
                                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                        '<td>' + data[i]['9am'] + '</td>' +
                                                        '<td>' + data[i]['10am'] + '</td>' +
                                                        '<td>' + data[i]['11am'] + '</td>' +
                                                        '<td>' + data[i]['12pm'] + '</td>' +
                                                        '<td>' + data[i]['1pm'] + '</td>' +
                                                        '<td>' + data[i]['2pm'] + '</td>' +
                                                        '<td>' + data[i]['3pm'] + '</td>' +
                                                        '<td>' + data[i]['4pm'] + '</td>' +
                                                        '<td>' + data[i]['5pm'] + '</td>' +
                                                        '<td>' + data[i]['6pm'] + '</td>' +
                                                        '<td>' + data[i]['7pm'] + '</td>' +
                                                        '<td>' + data[i]['8pm'] + '</td>' +
                                                        '<td>' + data[i]['9pm'] + '</td>';
                                                }
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                        case "sr_activity_hourly_visit":
                                    var head=`<th>Date</th>
                                        <th>Group</th>
                                        <th>Zone</th>
                                        <th>SR ID</th>
                                        <th>SR Name</th>
                                        <th>SR Mobile</th>
                                        <th>09AM</th>
                                        <th>10AM</th>
                                        <th>11AM</th>
                                        <th>12PM</th>
                                        <th>01PM</th>
                                        <th>02PM</th>
                                        <th>03PM</th>
                                        <th>04PM</th>
                                        <th>05PM</th>
                                        <th>06PM</th>
                                        <th>07PM</th>
                                        <th>08PM</th>
                                        <th>09PM</th>`;
                                        if (time_period == 0) {
                                            count = 0;
                                            var head=''
                                            var dim = data[0].length;
                                            for (var i = 0; i < data.length; i++) {
                                                if (i == 0) {
                                                    head += '<tr class="tbl_header">' + '<td>' + "Sl" + '</td>';
                                                } else {
                                                    html += '<tr>' + '<td>' + count + '</td>';
                                                }

                                                for (var j = 0; j < data[0].length; j++) {

                                                    if(i==0){
                                                        head += '<td>' + data[i][j] + '</td>';
                                                    }else{
                                                        html += '<td>' + data[i][j] + '</td>';
                                                    }
                                                }
                                                //alert(data[i][0]);

                                                html += '</tr>';

                                                count++;
                                            }
                                            head+='</td>';
                                            emptyContentAndAppendData(head,html)
                                        }
                                        else {
                                            for (var i = 0; i < data.length; i++) {
                                                html += '<tr>' +
                                                    '<td>' + data[i]['act_date'] + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                    '<td>' + data[i]['9am'] + '</td>' +
                                                    '<td>' + data[i]['10am'] + '</td>' +
                                                    '<td>' + data[i]['11am'] + '</td>' +
                                                    '<td>' + data[i]['12pm'] + '</td>' +
                                                    '<td>' + data[i]['1pm'] + '</td>' +
                                                    '<td>' + data[i]['2pm'] + '</td>' +
                                                    '<td>' + data[i]['3pm'] + '</td>' +
                                                    '<td>' + data[i]['4pm'] + '</td>' +
                                                    '<td>' + data[i]['5pm'] + '</td>' +
                                                    '<td>' + data[i]['6pm'] + '</td>' +
                                                    '<td>' + data[i]['7pm'] + '</td>' +
                                                    '<td>' + data[i]['8pm'] + '</td>' +
                                                    '<td>' + data[i]['9pm'] + '</td>';
                                            }
                                        }
                                        emptyContentAndAppendData(head,html)
                                    break;
                                    case "sr_hourly_activity":
                                        $('#sr_hourly_head').empty();
                                        $('#sr_hourly_cont').empty();
                                        var head = '<tr  style="text-align:center;"><th colspan="5"></th>'+
                                                    '<th colspan="5" class="text-center">TOTAL</th>' +
                                                    '<th colspan="3" class="text-center">~09AM</th>' +
                                                    '<th colspan="3" class="text-center">10AM</th>' +
                                                    '<th colspan="3" class="text-center">11AM</th>' +
                                                    '<th colspan="3" class="text-center">12PM</th>' +
                                                    '<th colspan="3" class="text-center">01PM</th>' +
                                                    '<th colspan="3" class="text-center">02PM</th>' +
                                                    '<th colspan="3" class="text-center">03PM</th>' +
                                                    '<th colspan="3" class="text-center">04PM</th>' +
                                                    '<th colspan="3" class="text-center">05PM</th>' +
                                                    '<th colspan="3" class="text-center">06PM</th>' +
                                                    '<th colspan="3" class="text-center">07PM</th>' +
                                                    '<th colspan="3" class="text-center">08PM~</th>' +
                                                    '</tr>';
                                        var sub_head='<tr><th>DATE</th>'+
                                                    '<th>GROUP</th>' +
                                                    '<th>ZONE</th>' +
                                                    '<th>SR ID</th>' +
                                                    '<th>SR NAME</th>' +
                                                    '<th>T.OLT</th>' +
                                                    '<th>R.OLT</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th>' +
                                                    '<th>VISIT</th>' +
                                                    '<th>ORDER</th>' +
                                                    '<th>EXP</th></tr>';
                                                for (var i = 0; i < data.length; i++) {
                                                    html += '<tr>' +
                                                        '<td>' + data[i]['act_date'] + '</td>' +
                                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                                        '<td>' + data[i]['than_olt'] + '</td>' +
                                                        '<td>' + data[i]['t_outlet'] + '</td>' +
                                                        '<td>' + data[i]['visit_olt'] + '</td>' +
                                                        '<td>' + data[i]['t_order'] + '</td>' +
                                                        '<td>' + data[i]['t_exp'] + '</td>' +
                                                        '<td>' + data[i]['9amv'] + '</td>' +
                                                        '<td>' + data[i]['9am'] + '</td>' +
                                                        '<td>' + data[i]['9exp_a'] + '</td>' +
                                                        '<td>' + data[i]['10amv'] + '</td>' +
                                                        '<td>' + data[i]['10am'] + '</td>' +
                                                        '<td>' + data[i]['10exp_a'] + '</td>' +
                                                        '<td>' + data[i]['11amv'] + '</td>' +
                                                        '<td>' + data[i]['11am'] + '</td>' +
                                                        '<td>' + data[i]['11exp_a'] + '</td>' +
                                                        '<td>' + data[i]['12pmv'] + '</td>' +
                                                        '<td>' + data[i]['12pm'] + '</td>' +
                                                        '<td>' + data[i]['12exp_p'] + '</td>' +
                                                        '<td>' + data[i]['1pmv'] + '</td>' +
                                                        '<td>' + data[i]['1pm'] + '</td>' +
                                                        '<td>' + data[i]['1exp_p'] + '</td>' +
                                                        '<td>' + data[i]['2pmv'] + '</td>' +
                                                        '<td>' + data[i]['2pm'] + '</td>' +
                                                        '<td>' + data[i]['2exp_p'] + '</td>' +
                                                        '<td>' + data[i]['3pmv'] + '</td>' +
                                                        '<td>' + data[i]['3pm'] + '</td>' +
                                                        '<td>' + data[i]['3exp_p'] + '</td>' +
                                                        '<td>' + data[i]['4pmv'] + '</td>' +
                                                        '<td>' + data[i]['4pm'] + '</td>' +
                                                        '<td>' + data[i]['4exp_p'] + '</td>' +
                                                        '<td>' + data[i]['5pmv'] + '</td>' +
                                                        '<td>' + data[i]['5pm'] + '</td>' +
                                                        '<td>' + data[i]['5exp_p'] + '</td>' +
                                                        '<td>' + data[i]['6pmv'] + '</td>' +
                                                        '<td>' + data[i]['6pm'] + '</td>' +
                                                        '<td>' + data[i]['6exp_p'] + '</td>' +
                                                        '<td>' + data[i]['7pmv'] + '</td>' +
                                                        '<td>' + data[i]['7pm'] + '</td>' +
                                                        '<td>' + data[i]['7exp_p'] + '</td>' +
                                                        '<td>' + data[i]['8pmv'] + '</td>' +
                                                        '<td>' + data[i]['8pm'] + '</td>' +
                                                        '<td>' + data[i]['8exp_p'] + '</td>' +
                                                         '</tr>';
                                                }
                                            emptyContentAndAppendData(head+sub_head,html);
                                            break;
                                    case "attendance_report":
                                        var head=`<tr class="">
                                            <th>Sl</th>
                                            <th>Date</th>
                                            <th>Group</th>
                                            <th>Region</th>
                                            <th>Zone</th>
                                            <th>Staff Id</th>
                                            <th>Emp Name</th>
                                            <th>Designation</th>
                                            <th>Mobile</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Status</th>
                                            <th>Loc</th>
                                        </tr>`;
                                        for (var i = 0; i < data.length; i++) {

                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['attn_date'] + '</td>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['dirg_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['edsg_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                '<td>' + data[i]['start_time'] + '</td>' +
                                                '<td>' + data[i]['end_time'] + '</td>' +
                                                '<td>' + data[i]['status'] + '</td>';
                                            html += "<td><a aemp_id='"+data[i].aemp_id+"' attn_date='"+data[i].attn_date+"' onclick='getAttendanceLocation(this)' class='btn btn-info btn-xs' data-toggle='modal' data-target='#attn_loc'><i class='fa fa-map-marker fa-2x' style='color:red;'></i> </a></tr>";
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sr_item_survey":
                                        var head=`<tr class="">
                                            <th>SL</th>
                                            <th>DATE</th>
                                            <th>OUTLET CODE</th>
                                            <th>OUTLET NAME</th>
                                            <th>ITEM NAME</th>
                                            <th>ITEM WEIGHT</th>
                                            <th>ITEM TPPR</th>
                                            <th>ITEM MRP</th>
                                            <th>SOURCE COUNTRY</th>
                                            <th>DISC DETAILS</th>
                                            <th>MNFC DATE</th>
                                            <th>EXPR DATE</th>
                                            <th>SR_ID</th>
                                            <th>SR_NAME</th>
                                            <th>Action</th>
                                        </tr>`;
                                        for (var i = 0; i < data.length; i++) {

                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['sv_date'] + '</td>' +
                                                '<td>' + data[i]['site_code'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['item_name'] + '</td>' +
                                                '<td>' + data[i]['item_weit'] + '</td>' +
                                                '<td>' + data[i]['item_tppr'] + '</td>' +
                                                '<td>' + data[i]['item_mrpp'] + '</td>' +
                                                '<td>' + data[i]['country_name'] + '</td>' +
                                                '<td>' + data[i]['prom_desc'] + '</td>' +
                                                '<td>' + data[i]['mnfc_date'] + '</td>' +
                                                '<td>' + data[i]['expr_date'] + '</td>'+
                                                '<td>' + data[i]['sr_id'] + '</td>'+
                                                '<td>' + data[i]['sr_name'] + '</td>';
                                                html+="<td><a id='"+data[i].id+"' href='#' class='btn btn-info btn-xs' onclick='getSurveyImage(this)'><i class='fa fa-eye'></i> View </a></td></tr>";
                                           // 
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "market_outlet_sr_outlet":  
                                            var head=`<th>SI</th>
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Available Outlet Quantity</th>
                                            <th>Route Outlets</th>`;                           
                                            for (var i = 0; i < data.length; i++) {
                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['dsct_name'] + '</td>' +
                                                    '<td>' + data[i]['than_name'] + '</td>' +
                                                    '<td>' + data[i]['ward_name'] + '</td>' +
                                                    '<td>' + data[i]['mktm_name'] + '</td>' +
                                                    '<td>' + data[i]['m_outlet_quantity'] + '</td>' +
                                                    '<td>' + data[i]['sr_outlet_quantity'] + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                            emptyContentAndAppendData(head,html);
                                        break;
                                    case "outlet_coverage":
                                        var gt_final=data.summary;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                                let ord_amnt= data[i]['ordm_amnt'];
                                                if(ord_amnt===null){
                                                    ord_amnt=0;
                                                }
                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['SALES_GROUP'] + '</td>' +
                                                    '<td>' + data[i]['DISTRICT_NAME'] + '</td>' +
                                                    '<td>' + data[i]['THANA_NAME'] + '</td>' +
                                                    '<td>' + data[i]['AVAILABLE_OUTLET'] + '</td>' +
                                                    '<td>' + data[i]['ROUTED_OUTLET'] + '</td>' +
                                                    '<td>' + data[i]['VISITED_SITES'] + '</td>' +
                                                    '<td>' + ord_amnt + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                            html+='<tr><td>GT</td><td></td><td></td><td></td>'+
                                                    '<td>'+gt_final['a_olt']+'</td>'+
                                                    '<td>'+gt_final['r_olt']+'</td>'+
                                                    '<td>'+gt_final['v_olt']+'</td>'+
                                                    '<td>'+gt_final['t_amnt']+'</td>'+
                                                     '</tr>';
                                            $("#cont_outlet_coverage").empty();
                                            $("#cont_outlet_coverage").append(html);
                                            $('#div_outlet_coverage').show();
                                        break;
                                    case "item_coverage":
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"item_coverage_<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        var head='<tr><th>Sl</th><th>Group Name</th><th>Staff Id</th><th>Emp Name</th><th>C.Item</th><th>Exp</th></tr>';
                                        var gt_final=data.summary;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['cov_item'] + '</td>' +
                                                    '<td>' + data[i]['order_amnt'] + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                            html+='<tr><td>GT</td><td></td><td></td><td></td>'+
                                                    '<td></td>'+
                                                    '<td>'+gt_final['t_ordr']+'</td>'+
                                                    '</td></tr>';
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "item_summary":
                                        // $('#export_option_btn').removeAttr('onclick');
                                        // $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"item_summary_<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        var head='<tr><th>SL</th><th>GROUP</th><th>ZONE</th><th>SR ID</th><th>SR NAME</th>'+
                                                    '<th>ITEM CODE</th>'+
                                                    '<th>ITEM NAME</th>'+
                                                    '<th>CATEGORY</th>'+
                                                    '<th>TOTAL OLT</th>'+
                                                    '<th>R.OLT</th>'+
                                                    '<th>VISIT</th>'+
                                                    '<th>TOUCH OLT</th>'+
                                                    '<th>EXP</th>'+
                                                    '</tr>';
                                        var gt_final=data.summary;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                                    '<td>' + data[i]['zone_code']+'-'+ data[i]['zone_name'] + '</td>' +
                                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                                    '<td>' + data[i]['amim_code'] + '</td>' +
                                                    '<td>' + data[i]['amim_name'] + '</td>' +
                                                    '<td>' + data[i]['itsg_name'] + '</td>' +
                                                    '<td>' + data[i]['than_olt'] + '</td>' +
                                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                                    '<td>' + data[i]['visit_olt'] + '</td>' +
                                                    '<td>' + data[i]['count_site'] + '</td>' +
                                                    '<td>' + data[i]['exp'] + '</td>' +
                                                    '</tr>';
                                                count++;
                                            }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "item_summary_depo_wise":
                                        var count=1;
                                        var head='<tr class="excel_header"><th>SL</th>'+
                                                '<th class="apply-filter">DEPO NAME</th>' +
                                                '<th class="apply-filter">CLASS NAME</th>' +
                                                '<th class="apply-filter">SUB CATEGORY</th>'+
                                                '<th class="apply-filter">ITEM CODE</th>' +
                                                '<th class="apply-filter">ITEM NAME</th>' +
                                                '<th class="apply-filter">ORDER_CTN</th>'+
                                                '<th class="apply-filter">ORDER_PICS</th>'+
                                                '<th class="apply-filter">TOTAL(PICS)</th>'+
                                                '<th class="apply-filter">FACTOR</th>'+
                                                '<th class="apply-filter">EXP</th>'+
                                                '</tr>';
                                        var html='';
                                        for(var i=0;i<data.length;i++){
                                            html+='<tr><td>'+count+'</td>'+
                                                    '<td>'+data[i].dlrm_name+'</td>'+
                                                    '<td>'+data[i].itcl_name+'</td>'+
                                                    '<td>'+data[i].itsg_name+'</td>'+
                                                    '<td>'+data[i].amim_code+'</td>'+
                                                    '<td>'+data[i].amim_name+'</td>'+
                                                    '<td>'+data[i].order_ctn+'</td>'+
                                                    '<td>'+data[i].order_pics+'</td>'+
                                                    '<td>'+data[i].total_quantity+'</td>'+
                                                    '<td>'+data[i].amim_duft+'</td>'+
                                                    '<td>'+data[i].order_amnt+'</td></tr>'+
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        break;
                                        case "weekly_outlet_summary":                                             
                                                var all_week=data.weeks;
                                                var data=data.data;
                                                var head = '<tr  style="text-align:center;"><th colspan="5"></th>'+
                                                            '<th colspan="3" class="text-center">TOTAL</th>' +
                                                            '<th colspan="3" class="text-center">1ST WEEK</th>' +
                                                            '<th colspan="3" class="text-center">2ND WEEK</th>' +
                                                            '<th colspan="3" class="text-center">3RD WEEK</th>' +
                                                            '<th colspan="3" class="text-center">4TH WEEK</th>' +
                                                            '<th colspan="3" class="text-center">5TH WEEK</th>' +
                                                            '</tr>';
                                                var sub_head='<tr class="excel_header"><th>SL</th>'+
                                                            '<th>SR_ID</th>' +
                                                            '<th class="apply-filter">SR_NAME</th>' +
                                                            '<th class="apply-filter">OUTLET_CODE</th>' +
                                                            '<th class="apply-filter">OUTLET_NAME</th>' +
                                                            '<th>VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th>'+
                                                            '<th class="apply-filter">VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th>'+
                                                            '<th class="apply-filter">VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th>'+
                                                            '<th class="apply-filter">VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th>'+
                                                            '<th class="apply-filter">VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th>'+
                                                            '<th class="apply-filter">VISIT</th>'+
                                                            '<th class="apply-filter">ORDER</th>'+
                                                            '<th class="apply-filter">EXP</th></tr>';
                                                var html='';
                                                var count=1;
                                                let v_amnt='';
                                                let v_memo='';
                                                let v_visit='';
                                                for(var i=0;i<data.length;i++){
                                                    html+='<tr><td>'+count+'</td>'+
                                                            '<td>'+data[i].aemp_usnm+'</td>'+
                                                            '<td>'+data[i].aemp_name+'</td>'+
                                                            '<td>'+data[i].site_code+'</td>'+
                                                            '<td>'+data[i].site_name+'</td>'+
                                                            '<td>'+data[i].t_visit+'</td>'+
                                                            '<td>'+data[i].t_memo+'</td>'+
                                                            '<td>'+data[i].t_amnt+'</td>';
                                                    for(var j=0;j<all_week.length;j++){
                                                        v_visit='v'+all_week[j].week_no;
                                                        v_memo='m'+all_week[j].week_no;
                                                        v_amnt='amnt'+all_week[j].week_no;
                                                        html+='<td>'+data[i][v_visit]+'</td>'+
                                                                '<td>'+data[i][v_memo]+'</td>'+
                                                                '<td>'+data[i][v_amnt]+'</td>';
                                                    }
                                                    html+='</tr>';
                                                    count++;
                                                }
                                                head=head+sub_head;
                                                $('.outlet_weekly').show();
                                                emptyContentAndAppendData(head,html);
                                                $('#export_option_btn').removeAttr('onclick');
                                                $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"OUTLET_WEEKLY_STATISTICS<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                                break;
                                    case "sr_wise_order_delivery":
                                        var head=`<th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th>`;
                                        var gt=data.gt;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {

                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                '<td>' + (data[i]['ordd_amt'].toFixed(2)) + '</td>' +
                                                '<td>' + (data[i]['deli_amt'].toFixed(2)) + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        html+='<tr><td>GT</td><td></td><td></td><td></td><td></td><td></td><td></td>'+
                                                '<td>'+gt['t_order']+'</td>'+
                                                '<td>'+gt['t_deli']+'</td></tr>';
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "order_vs_delivery_adv":
                                        var html='';
                                        var count=1;
                                        var head='<tr><th>SL</th>'+
                                                '<th class="apply-filter">REGION</th>' +
                                                '<th class="apply-filter">ZONE</th>' +
                                                '<th class="apply-filter">SR_ID</th>'+
                                                '<th class="apply-filter">SR_NAME</th>' +
                                                '<th class="apply-filter">OUTLET_CODE</th>' +
                                                '<th class="apply-filter">OUTLET_NAME</th>' +
                                                '<th>ORDER_DATE</th>'+
                                                '<th>ORDER_NO</th>'+
                                                '<th>ORDER_AMNT</th>'+
                                                '<th>CHALLAN_AMNT</th>'+
                                                '<th>DELI AMNT</th>'+
                                                '<th>DELI DATE</th>'+
                                                '<th class="apply-filter">DAY</th>'+
                                                '<th class="apply-filter">STATUS</th>'+
                                                '</tr>';
                                        for (var i = 0; i < data.length; i++) {
                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['dirg_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['site_code'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['ordm_ornm'] + '</td>' +
                                                '<td>' + data[i]['ordm_amnt'] + '</td>' +
                                                '<td>' + data[i]['challan_amnt'] + '</td>' +
                                                '<td>' + data[i]['deli_amnt'] + '</td>' +
                                                '<td>' + data[i]['ordm_drdt'] + '</td>' +
                                                '<td>' + data[i]['day_name'] + '</td>' +
                                                '<td>' + data[i]['lfcl_name'] + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"ORDER_VS_DELIVERY_ADVANCE<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;

                                    case "outlet_dues_report":
                                        var html='';
                                        var count=1;
                                        var head='<tr><th>SL</th>'+
                                                '<th class="apply-filter">GROUP</th>' +
                                                '<th class="apply-filter">ZONE</th>' +
                                                '<th class="apply-filter">DEPO</th>' +
                                                '<th class="apply-filter">SR_ID</th>'+
                                                '<th class="apply-filter">SR_NAME</th>' +
                                                '<th class="apply-filter">SV_ID</th>' +
                                                '<th class="apply-filter">SV_NAME</th>' +
                                                '<th class="apply-filter">OUTLET_CODE</th>' +
                                                '<th class="apply-filter">OUTLET_NAME</th>' +
                                                '<th class="apply-filter">ORDER_DATE</th>'+
                                                '<th class="apply-filter">ORDER_NO</th>'+
                                                '<th>ORDER_AMNT</th>'+
                                                '<th>REQ_AMNT</th>'+
                                                '<th>APR_AMNT</th>'+
                                                '<th>COLL_AMNT</th>'+
                                                '<th>COLL_DATE</th>'+
                                                '<th class="apply-filter">APR_ID</th>'+
                                                '<th class="apply-filter">APR_NAME</th>'+
                                                '</tr>';
                                        for (var i = 0; i < data.length; i++) {
                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_code']+'-'+data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['dlrm_name'] + '</td>' +
                                                '<td>' + data[i]['sr_id'] + '</td>' +
                                                '<td>' + data[i]['sr_name'] + '</td>' +
                                                '<td>' + data[i]['sv_id'] + '</td>' +
                                                '<td>' + data[i]['sv_name'] + '</td>' +
                                                '<td>' + data[i]['site_code'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['ordm_ornm'] + '</td>' +
                                                '<td>' + data[i]['ordm_amnt'] + '</td>' +
                                                '<td>' + data[i]['sreq_amnt'] + '</td>' +
                                                '<td>' + data[i]['sapr_amnt'] + '</td>' +
                                                '<td>' + data[i]['scol_amnt'] + '</td>' +
                                                '<td>' + data[i]['cpcr_cdat'] + '</td>' +
                                                '<td>' + data[i]['apr_id'] + '</td>' +
                                                '<td>' + data[i]['apr_name'] + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"ORDER_VS_DELIVERY_ADVANCE<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;
                                    case "outlet_crq_history":
                                        var head='';
                                        var html='';
                                        var count=1;
                                        head='<tr>'+
                                                '<th>SL</th>'+
                                                '<th>GROUP</th>'+
                                                '<th>REGION</th>'+
                                                '<th>ZONE</th>'+
                                                '<th>O.DATE</th>'+
                                                '<th>ORDER NO</th>'+                           
                                                '<th>TRIP NO</th>'+                           
                                                '<th>SR ID</th>'+
                                                '<th>SR NAME</th>'+
                                                '<th>OLT CODE</th>'+
                                                '<th>OLT NAME</th>'+
                                                '<th>ORD AMNT</th>'+
                                                '<th>INV AMNT</th>'+
                                                '<th>DLV AMNT</th>'+
                                                '<th>CR AMNT</th>'+
                                                '<th>APR AMNT</th>'+
                                                '<th>CC AMNT</th>'+
                                                '<th>COL AMNT</th>'+
                                                '<th>DUES AMNT</th>'+
                                                '<th>CASH</th>'+
                                                '<th>CHEQUE</th>'+
                                                '<th>CQ DATE</th>'+
                                                '<th>ONLINE</th>'+
                                                '<th>APR ID</th>'+
                                                '<th>APR NAME</th>'+
                                                '<th>DM ID</th>'+
                                                '<th>DM NAME</th>'+
                                                '<th>CRD STATUS</th>'+
                                                '<th>CRD DATE</th>'+                                                      
                                                '<th>STATUS</th></tr>';
                                            for (var i = 0; i < data.length; i++) {
                                                var color='';
                                                if(data[i].spbd_type=='Credit Req'){
                                                    color='background-color:#F8F9D7';
                                                }
                                                html += '<tr style="'+color+'">' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i].slgp_name + '</td>' +
                                                    '<td>' + data[i].dirg_name + '</td>' +
                                                    '<td>' + data[i].zone_name + '</td>' +
                                                    '<td>' + data[i].ordm_date + '</td>' +
                                                    '<td>' + data[i].ordm_ornm + '</td>' +
                                                    '<td>' + data[i].TRIP_NO + '</td>' +
                                                    '<td>' + data[i].sr_id + '</td>' +
                                                    '<td>' + data[i].sr_name + '</td>' +
                                                    '<td>' + data[i].site_code + '</td>' +
                                                    '<td>' + data[i].site_name+'</td>' +
                                                    "<td>" + data[i].ordm_amnt+ "</td>" +
                                                    "<td>" + data[i].INV_AMNT+ "</td>" +
                                                    "<td>" + data[i].DELV_AMNT+ "</td>" +
                                                    "<td>" + data[i].sreq_amnt + "</td>" +
                                                    "<td>" + data[i].sapr_amnt + "</td>" +
                                                    "<td>" + data[i].scol_amnt + "</td>" +
                                                    "<td>" + data[i].COLLECTION_AMNT + "</td>" +
                                                    "<td>" + data[i].due_amnt + "</td>" +
                                                    "<td>" + data[i].Cash + "</td>" +
                                                    "<td>" + data[i].Cheque + "</td>" +
                                                    "<td>" + data[i].check_date + "</td>" +
                                                    "<td>" + data[i].Online + "</td>" +
                                                    "<td>" + data[i].apr_id + "</td>" +
                                                    "<td>" + data[i].apr_name + "</td>" +
                                                    "<td>" + data[i].dm_id + "</td>" +
                                                    "<td>" + data[i].dm_name + "</td>" +
                                                    "<td>" + data[i].spbd_type + "</td>" +                                
                                                    "<td>" + data[i].cpcr_cdat + "</td>" +
                                                    "<td>" + data[i].c_status + "</td></tr>";
                                                count++;
                                            }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"ORDER_VS_DELIVERY_ADVANCE<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;
                                    case "outlet_dues_report_adv":
                                        var head='';
                                        var html='';
                                        var count=1;
                                        head='<tr>'+
                                                '<th>SL</th>'+
                                                '<th>GROUP</th>'+
                                                '<th>REGION</th>'+
                                                '<th>ZONE</th>'+
                                                '<th>O.DATE</th>'+
                                                '<th>ORDER NO</th>'+                           
                                                '<th>TRIP NO</th>'+                           
                                                '<th>SR ID</th>'+
                                                '<th>SR NAME</th>'+
                                                '<th>OLT CODE</th>'+
                                                '<th>OLT NAME</th>'+
                                                '<th>ORD AMNT</th>'+
                                                '<th>INV AMNT</th>'+
                                                '<th>DLV AMNT</th>'+
                                                '<th>CR AMNT</th>'+
                                                '<th>APR AMNT</th>'+
                                                '<th>CC AMNT</th>'+
                                                '<th>COL AMNT</th>'+
                                                '<th>DUES AMNT</th>'+
                                                '<th>CASH</th>'+
                                                '<th>CHEQUE</th>'+
                                                '<th>CQ DATE</th>'+
                                                '<th>ONLINE</th>'+
                                                '<th>APR ID</th>'+
                                                '<th>APR NAME</th>'+
                                                '<th>DM ID</th>'+
                                                '<th>DM NAME</th>'+
                                                '<th>CRD STATUS</th>'+
                                                '<th>CRD DATE</th>'+                                                      
                                                '<th>STATUS</th></tr>';
                                            for (var i = 0; i < data.length; i++) {
                                                var color='';
                                                if(data[i].spbd_type=='Credit Req'){
                                                    color='background-color:#F8F9D7';
                                                }
                                                html += '<tr style="'+color+'">' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i].slgp_name + '</td>' +
                                                    '<td>' + data[i].dirg_name + '</td>' +
                                                    '<td>' + data[i].zone_name + '</td>' +
                                                    '<td>' + data[i].ordm_date + '</td>' +
                                                    '<td>' + data[i].ordm_ornm + '</td>' +
                                                    '<td>' + data[i].TRIP_NO + '</td>' +
                                                    '<td>' + data[i].sr_id + '</td>' +
                                                    '<td>' + data[i].sr_name + '</td>' +
                                                    '<td>' + data[i].site_code + '</td>' +
                                                    '<td>' + data[i].site_name+'</td>' +
                                                    "<td>" + data[i].ordm_amnt+ "</td>" +
                                                    "<td>" + data[i].INV_AMNT+ "</td>" +
                                                    "<td>" + data[i].DELV_AMNT+ "</td>" +
                                                    "<td>" + data[i].sreq_amnt + "</td>" +
                                                    "<td>" + data[i].sapr_amnt + "</td>" +
                                                    "<td>" + data[i].scol_amnt + "</td>" +
                                                    "<td>" + data[i].COLLECTION_AMNT + "</td>" +
                                                    "<td>" + data[i].due_amnt + "</td>" +
                                                    "<td>" + data[i].Cash + "</td>" +
                                                    "<td>" + data[i].Cheque + "</td>" +
                                                    "<td>" + data[i].check_date + "</td>" +
                                                    "<td>" + data[i].Online + "</td>" +
                                                    "<td>" + data[i].apr_id + "</td>" +
                                                    "<td>" + data[i].apr_name + "</td>" +
                                                    "<td>" + data[i].dm_id + "</td>" +
                                                    "<td>" + data[i].dm_name + "</td>" +
                                                    "<td>" + data[i].spbd_type + "</td>" +                                
                                                    "<td>" + data[i].cpcr_cdat + "</td>" +
                                                    "<td>" + data[i].c_status + "</td></tr>";
                                                count++;
                                            }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"OUTLET_DUES_REPORT<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;
                                    case "outlet_dues_report_credit":
                                        var head='';
                                        var html='';
                                        var count=1;
                                        head='<tr>'+
                                                '<th>SL</th>'+
                                                '<th>ZONE</th>'+
                                                '<th>MANAGER_ID</th>'+
                                                '<th>MANAGER_NAME</th>'+
                                                '<th>SR_ID</th>'+
                                                '<th>SR_NAME</th>'+                           
                                                '<th>OUTLET_CODE</th>'+                           
                                                '<th>OUTLET_NAME</th>'+                           
                                                '<th>ERP_CODE</th>'+                           
                                                '<th>ERP_NAME</th>'+                           
                                                '<th>ORDER_NO</th>'+
                                                '<th>DELI_DATE</th>'+
                                                '<th>DELV_AMNT</th>'+
                                                '<th>COLL_AMNT</th>'+
                                                '<th>DUES_AMNT</th>'+
                                                '<th>DAY_LIMT</th>'+
                                                '<th>30 DAYS</th>'+
                                                '<th>45 DAYS</th>'+
                                                '<th>60 DAYS</th>'+
                                                '<th>90 DAYS</th>'+
                                                '<th>120 DAYS</th></tr>';
                                            for (var i = 0; i < data.length; i++) {
                                                html += '<tr>' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i].zone_name + '</td>' +
                                                    '<td>' + data[i].sv_id + '</td>' +
                                                    '<td>' + data[i].sv_name + '</td>' +
                                                    '<td>' + data[i].sr_id + '</td>' +
                                                    '<td>' + data[i].sr_name + '</td>' +
                                                    '<td>' + data[i].site_code + '</td>' +
                                                    '<td>' + data[i].site_name + '</td>' +
                                                    '<td>' + data[i].mother_site_code + '</td>' +
                                                    '<td>' + data[i].mother_site_name + '</td>' +
                                                    '<td>' + data[i].ORDM_ORNM + '</td>' +
                                                    '<td>' + data[i].ORDM_DRDT+'</td>' +
                                                    "<td>" + data[i].DELV_AMNT+ "</td>" +
                                                    "<td>" + data[i].COLLECTION_AMNT + "</td>" +
                                                    "<td>" + data[i].DUES_AMNT + "</td>" +
                                                    "<td>" + data[i].stcm_days + "</td>" +
                                                    "<td>" + data[i]['30_days'] + "</td>" +
                                                    "<td>" + data[i]['45_days'] + "</td>" +
                                                    "<td>" + data[i]['60_days'] + "</td>" +
                                                    "<td>" + data[i]['90_days'] + "</td>" +
                                                    "<td>" + data[i]['120_days'] + "</td></tr>";
                                                count++;
                                            }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"CREDIT_OUTLET_DUES_REPORT<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;
                                    case "outlet_dues_history":
                                        var head='';
                                        var html='';
                                        var count=1;
                                        head='<tr>'+
                                                '<th>SL</th>'+
                                                '<th>GROUP</th>'+
                                                '<th>REGION</th>'+
                                                '<th>ZONE</th>'+
                                                '<th>O.DATE</th>'+
                                                '<th>ORDER NO</th>'+                           
                                                '<th>TRIP NO</th>'+                           
                                                '<th>SR ID</th>'+
                                                '<th>SR NAME</th>'+
                                                '<th>OLT CODE</th>'+
                                                '<th>OLT NAME</th>'+
                                                '<th>ORD AMNT</th>'+
                                                '<th>INV AMNT</th>'+
                                                '<th>DLV AMNT</th>'+
                                                '<th>CR AMNT</th>'+
                                                '<th>APR AMNT</th>'+
                                                '<th>CC AMNT</th>'+
                                                '<th>COL AMNT</th>'+
                                                '<th>DUES AMNT</th>'+
                                                '<th>CASH</th>'+
                                                '<th>CHEQUE</th>'+
                                                '<th>CQ DATE</th>'+
                                                '<th>ONLINE</th>'+
                                                '<th>APR ID</th>'+
                                                '<th>APR NAME</th>'+
                                                '<th>DM ID</th>'+
                                                '<th>DM NAME</th>'+
                                                '<th>CRD STATUS</th>'+
                                                '<th>CRD DATE</th>'+                                                      
                                                '<th>STATUS</th></tr>';
                                            for (var i = 0; i < data.length; i++) {
                                                var color='';
                                                if(data[i].spbd_type=='Credit Req'){
                                                    color='background-color:#F8F9D7';
                                                }
                                                html += '<tr style="'+color+'">' +
                                                    '<td>' + count + '</td>' +
                                                    '<td>' + data[i].slgp_name + '</td>' +
                                                    '<td>' + data[i].dirg_name + '</td>' +
                                                    '<td>' + data[i].zone_name + '</td>' +
                                                    '<td>' + data[i].ordm_date + '</td>' +
                                                    '<td>' + data[i].ordm_ornm + '</td>' +
                                                    '<td>' + data[i].TRIP_NO + '</td>' +
                                                    '<td>' + data[i].sr_id + '</td>' +
                                                    '<td>' + data[i].sr_name + '</td>' +
                                                    '<td>' + data[i].site_code + '</td>' +
                                                    '<td>' + data[i].site_name+'</td>' +
                                                    "<td>" + data[i].ordm_amnt+ "</td>" +
                                                    "<td>" + data[i].INV_AMNT+ "</td>" +
                                                    "<td>" + data[i].DELV_AMNT+ "</td>" +
                                                    "<td>" + data[i].sreq_amnt + "</td>" +
                                                    "<td>" + data[i].sapr_amnt + "</td>" +
                                                    "<td>" + data[i].scol_amnt + "</td>" +
                                                    "<td>" + data[i].COLLECTION_AMNT + "</td>" +
                                                    "<td>" + data[i].due_amnt + "</td>" +
                                                    "<td>" + data[i].Cash + "</td>" +
                                                    "<td>" + data[i].Cheque + "</td>" +
                                                    "<td>" + data[i].check_date + "</td>" +
                                                    "<td>" + data[i].Online + "</td>" +
                                                    "<td>" + data[i].apr_id + "</td>" +
                                                    "<td>" + data[i].apr_name + "</td>" +
                                                    "<td>" + data[i].dm_id + "</td>" +
                                                    "<td>" + data[i].dm_name + "</td>" +
                                                    "<td>" + data[i].spbd_type + "</td>" +                                
                                                    "<td>" + data[i].cpcr_cdat + "</td>" +
                                                    "<td>" + data[i].c_status + "</td></tr>";
                                                count++;
                                            }
                                        emptyContentAndAppendData(head,html);
                                        $('#export_option_btn').removeAttr('onclick');
                                        $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"ORDER_VS_DELIVERY_ADVANCE<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                                        break;   
                                    case "zone_wise_order_delivery_summary":
                                        var gt=data.gt;
                                        var data=data.data;
                                        var head=` <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name </th>
                                            <th>Zone Code</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th>`;
                                        for (var i = 0; i < data.length; i++) {
                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['zone_code'] + '</td>' +
                                                '<td>' + (data[i]['ordd_oamt'].toFixed(2)) + '</td>' +
                                                '<td>' + (data[i]['deli_amt'].toFixed(2)) + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        html+='<tr><td>GT</td><td></td><td></td><td></td><td></td>'+
                                                '<td>'+gt['t_order']+'</td>'+
                                                '<td>'+gt['t_deli']+'</td></tr>';
                                        emptyContentAndAppendData(head,html);
                                        break;
                                    case "sku_wise_order_delivery":
                                        var count=0;
                                        var head=`<tr><th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Factor</th>
                                            <th>Order Qty</th>
                                            <th>Deli Qty</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th></tr>`;
                                        var gt=data.gt;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                '<td>' + data[i]['amim_code'] + '</td>' +
                                                '<td>' + data[i]['amim_name'] + '</td>' +
                                                '<td>' + data[i]['amim_duft'] + '</td>' +
                                                '<td>' + data[i]['order_qty'] + '</td>' +
                                                '<td>' + data[i]['deli_qty'] + '</td>' +
                                                '<td>' + data[i]['ordd_amt'] + '</td>' +
                                                '<td>' + data[i]['deli_amt'] + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        html+='<tr><td>GT</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>'+
                                                '<td>'+gt['t_order']+'</td>'+
                                                '<td>'+gt['t_deli']+'</td></tr>';
                                        emptyContentAndAppendData(head,html,'SKU_WISE_ORDER_VS_DELIVERY');
                                        break;
                                    case "class_wise_order_report_amt":
                                        if(ord_flag==1){
                                            // $('#class_wise_order_exp_btn').removeAttr('onclick');
                                            // $('#class_wise_order_exp_btn').attr('onclick', 'exportTableToExcel(this,"class_wise_order_report_amt_<?php echo date('Y_m_d'); ?>.xls","tbl_class_wise_order_summary")');
                                            let flag=rtype+sr_zone;
                                            var headings = '';
                                            switch(flag){
                                                case "11":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Date</th>' +
                                                            '<th>Staff ID</th>' +
                                                            '<th>Name</th>' +
                                                            '<th>Mobile</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                            html += '<td>' + result + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "12":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Date</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                            html += '<td>' + result + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "21":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Staff ID</th>' +
                                                            '<th>Name</th>' +
                                                            '<th>Mobile</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                            html += '<td>' + result + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "22":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                            html += '<td>' + result + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;



                                            }
                                            emptyContentAndAppendData(headings,html);
                                        }
                                        else{
                                            
                                            let flag=rtype+sr_zone;
                                            var headings = '';
                                            switch(flag){
                                                case "11":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Date</th>' +
                                                            '<th>Staff ID</th>' +
                                                            '<th>Name</th>' +
                                                            '<th>Mobile</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_memo.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['ordm_date'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_usnm'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_name'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_mob1'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            html += '<td>' + data.class_wise_ord_memo[j][cl_name] + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "12":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Date</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_memo.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['ordm_date'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            html += '<td>' + data.class_wise_ord_memo[j][cl_name] + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "21":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Staff ID</th>' +
                                                            '<th>Name</th>' +
                                                            '<th>Mobile</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_memo.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_usnm'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_name'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['aemp_mob1'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            html += '<td>' + data.class_wise_ord_memo[j][cl_name] + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;
                                                case "22":
                                                    var cl_name = '';
                                                    count = 1;
                                                    headings = '<th>Sl</th>' +
                                                            '<th>Zone Code</th>' +
                                                            '<th>Zone Name</th>';
                                                    for (var j = 0; j < data.class_wise_ord_memo.length; j++) {
                                                        html += '<tr><td>' + count + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_code'] + '</td>' +
                                                            '<td>' + data.class_wise_ord_memo[j]['zone_name'] + '</td>';

                                                        for (var i = 0; i < data.class_list.length; i++) {
                                                            cl_name = data.class_list[i]['itcl_name'];
                                                            if (j == 0) {
                                                                headings += '<th>' + data.class_list[i]['itcl_name'] + '</th>';
                                                            }
                                                            html += '<td>' + data.class_wise_ord_memo[j][cl_name] + '</td>';
                                                        }
                                                        html += '</tr>';
                                                        count++;
                                                    }
                                                    break;

                                            }
                                            emptyContentAndAppendData(headings,html);
                                        }                                        
                                        break;
                                    case "cat_wise_order_report_amt":
                                            if(ord_flag==1){
                                                let flag=rtype+sr_zone;
                                                var headings = '';
                                                switch(flag){
                                                    case "11":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Date</th>' +
                                                        '<th>Staff ID</th>' +
                                                        '<th>Name</th>' +
                                                        '<th>Mobile</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                                html += '<td>' + result+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "12":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Date</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                                html += '<td>' + result+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "21":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Staff ID</th>' +
                                                        '<th>Name</th>' +
                                                        '<th>Mobile</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                                html += '<td>' + result+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "22":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                var result = typeof data.class_wise_ord_amnt[j][cl_name] === 'number' ? (data.class_wise_ord_amnt[j][cl_name]).toFixed(2) : 0;
                                                                html += '<td>' + result+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                }
                                                emptyContentAndAppendData(headings,html);
                                            }
                                            else{
                                                let flag=rtype+sr_zone;
                                                var headings = '';
                                                switch(flag){
                                                    case "11":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Date</th>' +
                                                        '<th>Staff ID</th>' +
                                                        '<th>Name</th>' +
                                                        '<th>Mobile</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                html += '<td>' + data.class_wise_ord_amnt[j][cl_name]+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "12":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Date</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['ordm_date'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                html += '<td>' + data.class_wise_ord_amnt[j][cl_name]+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "21":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Staff ID</th>' +
                                                        '<th>Name</th>' +
                                                        '<th>Mobile</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_usnm'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_name'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['aemp_mob1'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                html += '<td>' + data.class_wise_ord_amnt[j][cl_name]+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                    case "22":
                                                        var headings = '<th>Sl</th>' +
                                                        '<th>Zone Code</th>' +
                                                        '<th>Zone Name</th>';
                                                        var cl_name = '';
                                                        count = 1;
                                                        for (var j = 0; j < data.class_wise_ord_amnt.length; j++) {
                                                            html += '<tr><td>' + count + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_code'] + '</td>' +
                                                                '<td>' + data.class_wise_ord_amnt[j]['zone_name'] + '</td>';
                                                            for (var i = 0; i < data.class_list.length; i++) {
                                                                cl_name = data.class_list[i]['itcg_name'];
                                                                if (j == 0) {
                                                                    headings += '<th>' + data.class_list[i]['itcg_name'] + '</th>';
                                                                }
                                                                html += '<td>' + data.class_wise_ord_amnt[j][cl_name]+ '</td>';
                                                            }
                                                            html += '</tr>';
                                                            count++;
                                                        }
                                                        break;
                                                }
                                                emptyContentAndAppendData(headings,html);                                              
                                            }
                                        break;
                                    case "sr_route_outlet":
                                        $('#tableDiv_sr_route_outlet').show();
                                        for (var i = 0; i < data.length; i++) {

                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td style="background-color:' + colorEffect(data[i]["t_site"], 'd1') + '">' + data[i]['t_site'] + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        $("#cont_sr_route_outlet").empty();
                                        $("#cont_sr_route_outlet").append(html);
                                        break;
                                    case "group_wise_route_outlet":
                                        var slgp_name = $("#sales_group_id option:selected").text();
                                        $('#tableDiv_group_wise_route_outlet').show();
                                        let b = 0;
                                        for (var i = 0; i < data.length; i++) {
                                            b = parseInt(data[i]['b_60']) + parseInt(data[i]['b_120']);
                                            b = (b / parseInt(data[i]['t_route']) * 100).toFixed(2)
                                            html += '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + slgp_name + '</td>' +
                                                '<td>' + data[i]['t_route'] + '</td>' +
                                                '<td>' + data[i]['t_sr'] + '</td>' +
                                                '<td>' + data[i]['b_60'] + '</td>' +
                                                '<td>' + data[i]['b_60_120'] + '</td>' +
                                                '<td>' + data[i]['b_120'] + '</td>' +
                                                '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>' +
                                                '</tr>';
                                            count++;
                                        }
                                        $("#cont_group_wise_route_outlet").empty();
                                        $("#cont_group_wise_route_outlet").append(html);
                                        break;
                                    case "note_report":
                                        var head = '<tr><th>Date</th>' +
                                            '<th>Staff id</th>' +
                                            '<th>Emp Name</th>' +
                                            '<th>Designation</th>' +
                                            '<th>Group</th>' +
                                            '<th>Zone</th>' +
                                            '<th>Note/Task Details</th>' +
                                            '<th>Note Type</th>' +
                                            '<th>Outlet</th>' +
                                            ' <th>Time</th>' +
                                            '<th>Area</th>'+
                                            '<th>Image</th></tr>';
                                        var html = '';
                                        for (var i = 0; i < data.length; i++) {
                                            html += '<tr>' +
                                                '<td>' + data[i]['note_date'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['edsg_name'] + '</td>' +
                                                '<td>' + data[i]['slgp_code'] + ' - ' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_code'] + ' - ' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['note_body'] + '</td>' +
                                                '<td>' + data[i]['note_type'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['n_time'] + '</td>' +
                                                '<td>' + data[i]['geo_addr'] + '</td>';
                                            html+="<td><a id='"+data[i].id+"' href='#' class='btn btn-info btn-xs' onclick='getNoteImage(this)'><i class='fa fa-eye'></i> View </a></td>"+
                                                '</tr>';
                                            
                                        }
                                        emptyContentAndAppendData(head,html); 
                                        break;
                                    case "note_summary":
                                        var head = '<tr><th>Sl</th>' +
                                            '<th>Staff id</th>' +
                                            '<th>Emp Name</th>' +
                                            '<th>Designation</th>' +
                                            '<th>Group</th>' +
                                            '<th>Total Note</th>' +
                                            '</tr>';
                                        var html = '';
                                        var start_date=data.start_date;
                                        var end_date=data.end_date;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                            html += `<tr>
                                                <td> ${count}</td>
                                                <td> ${data[i]['staff_id']}</td>
                                                <td> ${data[i]['staff_name']}</td>
                                                <td> ${data[i]['edsg_name']}</td>
                                                <td> ${data[i]['slgp_name']}</td>                                             
                                                <td> ${data[i]['total_note']}</td>
                                                </tr>`                                   
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html); 
                                        break;
                                    case "assign_task_stat":
                                        var head = '<tr><th>SL</th>' +
                                            '<th>NOTE DATE</th>' +
                                            '<th>NOTE BODY</th>' +
                                            '<th>NOTE TYPE</th>' +
                                            '<th>NOTE OWNER</th>' +
                                            '<th>ASSIGNED TO</th>' +
                                            '<th>ASSIGNED DATE</th>' +
                                            '<th>REMARKS</th>' +
                                            '<th>STATUS</th>' +
                                            '</tr>';
                                        var html = '';
                                        var start_date=data.start_date;
                                        var end_date=data.end_date;
                                        var data=data.data;
                                        for (var i = 0; i < data.length; i++) {
                                            html += `<tr>
                                                <td> ${count}</td>
                                                <td> ${data[i]['NOTE_DATE']}</td>
                                                <td> ${data[i]['NOTE_BODY']}</td>
                                                <td> ${data[i]['NOTE_TYPE']}</td>
                                                <td> ${data[i]['OWNER_NAME']}</td>
                                                <td> ${data[i]['ASSIGNED_NAME']}</td>                                             
                                                <td> ${data[i]['MAPPING_DATE']}</td>
                                                <td> ${data[i]['NOTE_REMARK']}</td>
                                                <td> ${data[i]['NOTE_STAT']}</td>
                                                </tr>`                                   
                                            count++;
                                        }
                                        emptyContentAndAppendData(head,html); 
                                        break;
                                    case "asset_order":
                                        var html='';
                                        var amim_list=data.amim_list;
                                        var data=data.t_data;
                                        var head='<tr>'+
                                                    '<th>Group</th>'+
                                                    '<th>Zone Name</th>'+
                                                    '<th>Staff Id</th>'+
                                                    '<th>SR Name</th>'+
                                                    '<th>Site Name</th>'+
                                                    '<th>T.Order</th>'+
                                                    '<th>Ast.Order</th>';
                                        
                                        console.log(amim_list)
                                        for(var i=0;i<data.length;i++){
                                            html+='<tr><td>'+data[i].slgp_name+'</td>'+
                                                    '<td>'+data[i].zone_name+'</td>'+
                                                    '<td>'+data[i].aemp_usnm+'</td>'+
                                                    '<td>'+data[i].aemp_name+'</td>'+
                                                    '<td>'+data[i].site_name+'</td>'+
                                                    '<td>'+data[i].site_order_amount+'</td>'+
                                                    '<td>'+data[i].ast_itm_ordr+'</td>';
                                        for(var j=0;j<amim_list.length;j++){
                                            if(i==0){
                                                var ind=amim_list[j].amim_name;
                                                head+='<th>'+ind+'</th>';
                                                html+='<td>'+data[i][ind]+'</td>';
                                            }
                                            else{
                                                var ind=amim_list[j].amim_name;
                                                html+='<td>'+data[i][ind]+'</td>';
                                            }
                                        }
                                        html+='</tr>';           
                                        }
                                        head+='</tr>';
                                        $('#asset_report_head').empty();
                                        $('#asset_report_head').append(head);
                                        $('#cont_asset_report').empty();
                                        $('#cont_asset_report').append(html);
                                        $('#tblDiv_asset_report').show();
                                        break;
                                    case "asset_details":
                                        var html='';
                                        var asset_dp_content='';
                                        var gt=[];
                                        var head='<tr>'+
                                                    '<th>Group</th>'+
                                                    '<th>Zone Name</th>'+
                                                    '<th>SR ID</th>'+
                                                    '<th>SR Name</th>'+
                                                    '<th>Site Name</th>'+
                                                    '<th>Item Name</th>'+
                                                    '<th>Olt. Asset Exp</th>';
                                        for(var i=0;i<data.length;i++){                                       
                                            html += '<tr>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                                '<td>' + data[i]['site_name'] + '</td>' +
                                                '<td>' + data[i]['item_name'] + '</td>' +
                                                '<td>' + data[i]['ast_itm_ordr'] + '</td>'+
                                                '</tr>';
                                            gt['ast_itm_ordr']=parseFloat((gt['ast_itm_ordr']||0)+parseFloat(data[i]['ast_itm_ordr']));
                                        }
                                        let gt_order=gt['ast_itm_ordr']>0?gt['ast_itm_ordr'].toFixed(2):0;
                                        html+='<tr>'+
                                                '<td>GT</td><td></td><td></td><td></td><td></td><td></td>'+
                                                '<td>'+ gt_order+'</td></tr>';
                                            
                                        $('#asset_report_head').empty();
                                        $('#asset_report_head').append(head);
                                        $('#cont_asset_report').empty();
                                        $('#cont_asset_report').append(html);
                                        $('#tblDiv_asset_report').show();
                                        break;
                                    case "asset_summary":
                                        var start_date=data.start_date;
                                        var end_date=data.end_date;
                                        var gt=data.gt;
                                        var data=data.data;
                                        var html='';
                                        var asset_dp_content='';
                                        //var gt=[];
                                        
                                        var head='<tr>'+
                                                    '<th>Group</th>'+
                                                    '<th>Zone Name</th>'+
                                                    '<th>Asset Olt</th>'+
                                                    '<th>C.Olt</th>'+
                                                    '<th>Memo</th>'+
                                                    '<th>Olt. Exp</th>'+
                                                    '<th>Olt. Asset Exp</th>'+
                                                    '<th>Action</th>';
                                        for(var i=0;i<data.length;i++){
                                            //asset_dp_content += '<li><a href="#" onclick="getAssetDeeperData(this)" s_date="'+start_date+'" e_date="'+end_date+'" slgp_id="'+sales_group_id+'" stage="0"  id="' +data[i]['zone_id'] + '">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                            
                                            html += '<tr>' +
                                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                                '<td>' + data[i]['zone_name'] + '</td>' +
                                                '<td>' + data[i]['ast_olt'] + '</td>' +
                                                '<td>' + data[i]['ast_ord_olt'] + '</td>' +
                                                '<td>' + data[i]['t_memo'] + '</td>' +
                                                '<td>' + data[i]['site_ordr'] +'</td>' +
                                                '<td>' + data[i]['ast_itm_ordr'] + '</td>'+
                                                '<td><button class="btn btn-primary btn-xs" onclick="getAssetOutletDetails(this)" astm_id="'+astm_id+'" s_date="'+start_date+'" e_date="'+end_date+'" slgp_id="'+sales_group_id+'" id="' +data[i]['zone_id'] + '">Details</button>'+
                                                '<button class="btn btn-default btn-xs" onclick="getAssetOutletDetailsThanaWise(this)" astm_id="'+astm_id+'" s_date="'+start_date+'" e_date="'+end_date+'" slgp_id="'+sales_group_id+'" id="' +data[i]['zone_id'] + '">Thana Wise</button>'
                                                '</td>'+
                                                '</tr>';
                                            // gt['ast_olt']=parseFloat((gt['ast_olt']||0)+parseFloat(data[i]['ast_olt']));
                                            // gt['ast_ord_olt']=parseFloat((gt['ast_ord_olt']||0)+parseFloat(data[i]['ast_ord_olt']));
                                            // gt['t_memo']=parseFloat((gt['t_memo']||0)+parseFloat(data[i]['t_memo']));
                                            // gt['site_ordr']=parseFloat((gt['site_ordr']||0)+parseFloat(data[i]['site_ordr']));
                                            // gt['ast_itm_ordr']=parseFloat((gt['ast_itm_ordr']||0)+parseFloat(data[i]['ast_itm_ordr']));
                                        }
                                        html+='<tr>'+
                                                '<td>GT</td><td></td><td>'+ gt['ast_olt']+'</td>'+
                                                '<td>'+ gt['ast_ord_olt']+'</td>'+
                                                '<td>'+ gt['t_memo']+'</td>'+
                                                '<td>'+ gt['site_ordr']?.toFixed(2)+'</td>'+
                                                '<td>'+ gt['ast_itm_ordr']?.toFixed(2)+'</td></tr>';
                                            
                                        $('#asset_report_head').empty();
                                        $('#asset_dp_content').empty();
                                        $('#asset_report_head').append(head);
                                        $('#cont_asset_report').empty();
                                        $('#cont_asset_report').append(html);
                                    //  $('#asset_dp_content').append(asset_dp_content);
                                        $('#tblDiv_asset_report').show();
                                        break;
                                }
                                
                                $('#ajax_load').css("display", "none");

                                // $('#tl_dynamic').excelTableFilter({
                                //     columnSelector: '.apply-filter'
                                // });
                            }, error: function (error) {
                                // console.log(time_period)
                                console.log(error);
                                // alert("Something went wrong")
                                $('#ajax_load').css("display", "none");
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                })
                            }

                        });
                }

            }
            function colorEffect(value, reportType) {
                if (reportType == 'd1') {
                    if (value < 60 || value > 120) {
                        return "#ffb5b7;color:#bb0a1e;";
                    }
                    else if (value >= 60 && value <= 120) {
                        return "#c5e1a5;color:#33691e;";
                    }
                }
                else if (reportType == 'd2') {
                    if (value <= 50) {
                        return "#c5e1a5;color:#33691e;";
                    }
                    if (value > 50) {

                        return "#ffb5b7;color:#bb0a1e;";
                    }
                }
                else if (reportType == 'd3') {
                    if (value >= 60) {
                        return "#c5e1a5;color:#33691e;";
                    }
                    if (value < 60) {
                        return "#ffb5b7;color:#bb0a1e;";
                    }
                }
                else if (reportType == 'sr_mov_sum') {
                    if (value >= 10) {
                        return "#c5e1a5;color:#33691e;";
                    }
                    if (value < 10) {
                        return "#ffb5b7;color:#bb0a1e;";
                    }
                }

            }
            //exportTableToCSVNote1,exportTableToCSVZone
            function exportTableToExcel(elem,filename, tableId){
                var BOM = "\uFEFF";
                var table=document.getElementById(tableId);
                var html = table.outerHTML;
               // var url = 'data:application/vnd.ms-excel,' + encodeURI(BOM+html); // Set your html table into url 
                var url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url 
                
                elem.setAttribute("href", url);
                $(elem).attr("download",filename);
                return false;
            }

            function exportTableToCSVNote(filename, tableId) {
                var csv = [];
                const BOM = '\uFEFF'; 
                var rows = document.querySelectorAll('#' + tableId + '  tr');
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(BOM+cols[j].innerText);
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }
            function exportTableToCSV(filename,tableId ) {
                // alert(tableId);
                var csv = []; 
                var rows = document.querySelectorAll('#' + tableId + '  tr');
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(cols[j].innerText);
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }

            function downloadCSV(csv, filename) {
                var csvFile;
                var downloadLink;
                
                csvFile = new Blob([csv], {type: "text/csv;charset=utf-8"});
                downloadLink = document.createElement("a");
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
            }
            // Declare date as datepicker
            $("#year_mnth").datepicker({
                dateFormat: 'yy-mm',
                changeMonth: true
            });
            $("#year_mnth1").datepicker({
                dateFormat: 'yy-mm',
                changeMonth: true
            });
            $(".trip_filter_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
            });
            $(document).ready(function () {
                $('.start_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '-4m',
                    maxDate: new Date(),
                    autoclose: 1,
                    showOnFocus: true
                });
            });

            $("#start_date").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: '-4m',
                maxDate: new Date(),
                changeMonth: true,
                onSelect: function (date) {
                    var slgp = $('#sales_group_id').val();
                    var zone = $('#zone_id').val();
                    var region = $('#dirg_id').val();
                    var assetType = $("input[name='outletAType']:checked").val();

                    var selectedDate = new Date(date);
                    var msecsInADay = 86400000;
                    var startDate = new Date(selectedDate.getTime());
                    var maxDate = new Date(selectedDate.getTime());
                    if (assetType != "isAll") {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
                    }
                    else if (zone != "") {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
                    }
                    else if (region != '') {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 20);
                    }
                    else if (slgp != "") {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 15);
                    }

                    $("#end_date").datepicker("option", "minDate", startDate);
                    $("#end_date").datepicker("option", "maxDate", maxDate);

                }
            });
            //Functions for the date of Distric thana wise section
            $("#start_date_d").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: '-4m',
                maxDate: new Date(),
                changeMonth: true,
                onSelect: function (date) {
                    var market_id = $('#market_id').val();
                    var ward_id = $('#ward_id').val();
                    var than_id = $('#than_id').val();
                    var assetType = $("input[name='outletAType']:checked").val();
                    var selectedDate = new Date(date);
                    var msecsInADay = 86400000;
                    var startDate = new Date(selectedDate.getTime());
                    var maxDate = new Date(selectedDate.getTime());
                    if (assetType != 'isAll') {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
                    }
                    else if (market_id != "") {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
                    }
                    else if (ward_id != '') {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 20);
                    }
                    else if (than_id != "") {
                        maxDate = new Date(selectedDate.getTime() + msecsInADay * 15);
                    }

                    $("#end_date_d").datepicker("option", "minDate", startDate);
                    $("#end_date_d").datepicker("option", "maxDate", maxDate);

                }
            });
            $(".end_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
            });
            function clearDate() {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                document.getElementById('start_date_d').value = '';
                document.getElementById('end_date_d').value = '';
            }

            $('input:radio[name="outletAType"]').change(function () {
                clearDate();
            });
            $('.target').on('click', function () {
                $('button').removeClass('selected');
                $(this).addClass('selected');
            });
            $('.target1').on('click', function () {
                $('button').removeClass('selecteded');
                $(this).addClass('selecteded');
            });

        </script>
        <style type="text/css">
            .ms-options{
                    max-height:150px !important;
                    min-height:100px !important;
            }
            .table>tbody>tr>td{
                padding:3px;
            }
            ul,li{
                list-style: none;
            }
            .request_report_check {
                text-decoration: underline;
            }

            .request_report_check:hover {
                text-decoration: underline;
                color: blue;
            }

            .target.selected {
                background-color: #d9534f;
                border-color: #d9534f;
                outline: none;
            }

            .target1.selecteded {
                background-color: #d9534f;
                border-color: #d9534f;
                outline: none;
            }

            table::-webkit-scrollbar {
                /*Your styles here*/
            }

            table::-webkit-scrollbar-thumb {
                /*Your styles here*/
            }

            table::-webkit-scrollbar-thumb:window-inactive {
                /*Your styles here*/
            }
            /* Zoom style */
            /**
 


        </style>

        {{--    style for carousel--}}
        <style>
            /* carousel */
            #quote-carousel
            {
                padding: 0 10px 30px 10px;
                margin-top: 30px;
            }

            /* Control buttons  */
            #quote-carousel .carousel-control
            {
                background: none;
                color: #222;
                font-size: 2.3em;
                text-shadow: none;
                margin-top: 25%;
            }
            /* Previous button  */
            #quote-carousel .carousel-control.left
            {
                left: -12px;
            }
            /* Next button  */
            #quote-carousel .carousel-control.right
            {
                right: -12px !important;
            }
            /* Changes the position of the indicators */
            #quote-carousel .carousel-indicators
            {
                right: 50%;
                top: auto;
                bottom: 0px;
                margin-right: -19px;
            }
            /* Changes the color of the indicators */
            #quote-carousel .carousel-indicators li
            {
                background: #c0c0c0;
            }
            #quote-carousel .carousel-indicators .active
            {
                background: #333333;
            }
            #quote-carousel img
            {
                border-radius: 5px;
                height: 45.6rem;
                width: 95%;
            }
            /* End carousel */

            .item blockquote {
                border-left: none;
                margin: 0;
            }

            .item blockquote img {
                margin-bottom: 10px;
            }

            .item blockquote p:before {
                content: "\f10d";
                font-family: 'Fontawesome';
                float: left;
                margin-right: 10px;
            }



            /**
              MEDIA QUERIES
            */

            /* Small devices (tablets, 768px and up) */
            @media (min-width: 768px) {
                #quote-carousel
                {
                    margin-bottom: 0;
                    padding: 0 40px 30px 40px;
                }

            }

            /* Small devices (tablets, up to 768px) */
            @media (max-width: 768px) {

                /* Make the indicators larger for easier clicking with fingers/thumb on mobile */

                #quote-carousel .carousel-indicators {
                    bottom: -20px !important;
                }
                #quote-carousel .carousel-indicators li {
                    display: inline-block;
                    margin: 0px 5px;
                    width: 15px;
                    height: 15px;
                }
                #quote-carousel .carousel-indicators li.active {
                    margin: 0px 5px;
                    width: 20px;
                    height: 20px;
                }
            }
        </style>


        <script id="deviation_rpt_script">
            function getDeviationData(reportType) {
                hideReport();
                hide_me();
                addClass();
                $('#export_details').hide();
                $('#gvt_hierarchy').empty();
                $('#tracing_gvt').show();
                $('#emid_div1').empty();
                $('#ajax_load').css("display", "block");
                if (reportType == 'group_wise_route_outlet') {
                    $('.gvt_filter').hide();
                    $('#export_csv_tracking_gvt').removeAttr('onclick');
                    $('#export_csv_tracking_gvt').attr('onclick', 'exportTableToCSV("abnormal_route_outlet_<?php echo date('Y_m_d'); ?>.csv","tbl_traking_gvt")');
                    $('#deviation_date').removeAttr('onchange');
                    $('#deviation_date').attr('onchange', 'getDateWiseDeviationData(this.value,1)');
                    $('#deviation_date_div').hide();
                    $.ajax({
                        type: "get",
                        url: "{{URL::to('/')}}/getDeviationData",
                        success: function (data) {
                            $('#ajax_load').css("display", "none");
                            console.log(data);
                            $('#tableDiv_traking_gvt').show();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            var html = '';
                            var dp_cnt = '';
                            var head1 = '<tr><th>Company Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th>';
                            head1 += '</tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {
                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                b=b>100?100:b;
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['acmp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td>' + b + '%' + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationDeeperData(this)" id="' + data[i]['acmp_id'] + '" stage="1">' + ' ' + data[i]['acmp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                html += '</tr>';

                            }
                            $('#tableDiv_tracking_gvt_header1').empty();
                            $('#tableDiv_tracking_gvt_header1').append(head1);
                            $('#cont_traking_gvt').empty();
                            $('#cont_traking_gvt').append(html);
                            $('#all_dp_content1').empty();
                            $('#all_dp_content1').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);
                        },
                        error: function (error) {
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                            console.log(error);
                        }

                    });
                }
                if (reportType == 'outlet_visit') {
                    $('.gvt_filter').hide();
                    $('#export_csv_tracking_gvt').removeAttr('onclick');
                    $('#export_csv_tracking_gvt').attr('onclick', 'exportTableToCSV("outlet_visit_percentage_<?php echo date('Y_m_d'); ?>.csv","tbl_traking_gvt")');
                    var stage = 0;
                    $('#deviation_date_div').show();
                    $('#deviation_date').removeAttr('onchange');
                    $('#deviation_date').attr('onchange', 'getDateWiseDeviationData(this.value,2)');
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "get",
                        url: "{{URL::to('/')}}/getDeviationData/outletVisit/" + stage,
                        success: function (data) {
                            $('#ajax_load').css("display", "none");
                            $('#tableDiv_traking_gvt').show();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            var html = '';
                            var head1 = '';
                            var dp_cnt = '';
                            head1 = '<tr><th>Company Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + percent_visit.toFixed(2) + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="1">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                            $('#tableDiv_tracking_gvt_header1').empty();
                            $('#cont_traking_gvt').empty();
                            $('#tableDiv_tracking_gvt_header1').append(head1);
                            $('#cont_traking_gvt').append(html);
                            $('#all_dp_content1').empty();
                            $('#all_dp_content1').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);
                        },
                        error: function (error) {
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                            console.log(error);
                        }

                    });
                }
                if (reportType == 'note_task') {
                    // console.log(data);
                    $('#tracing_gvt').hide();
                    $('#ajax_load').css("display", "none");
                    $('#title_head').show();
                    $('#tracing').show();
                    $('#sh_select_date').hide();
                    $('#desig').empty();
                    $('#emid_div').empty();
                    var emid =<?php echo $emid; ?>;
                    var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
                    $('#emid_div').append(emid_cnt);
                    $('#employee_sales_traking_report_slgp').hide();
                    $('#emp_task_note_report').show();
                    $('#period').hide();
                    $('#sh_date').hide();
                    $('#dev_note_date').show();
                    var date = $('#dev_note_date').val();
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "GET",
                        url: "{{URL::to('/')}}/getNoteTaskReport",
                        cache: false,
                        dataType: "json",
                        success: function (data) {
                            $('#ajax_load').css("display", "none");
                            $('#tableDiv_traking').show();
                            $('#head_tracking_dev_note_task').empty();
                            var head = '<td>Sl</td>' +
                                '<td>Name</td>' +
                                '<td>Mobile</td>' +
                                '<td>Own Task</td>' +
                                '<td>Assign Task</td>' +
                                '<td>Team Task</td>';
                            $('#head_tracking_dev_note_task').append(head);
                            //console.log(data);
                            var html = "";
                            var count = 1;
                            var dp_cnt = '';
                            var task_details = '';
                            for (var i = 0; i < data.length; i++) {
                                task_details = 'getTaskDetails/' + data[i]['id'] + '/' + date;
                                html += '<tr>' +
                                    '<td>' + count + '</td>' +
                                    '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td><a href="' + task_details + '" target="_blank">' + data[i]['t_note'] + '  ' + '<i class="fa fa-eye fa-2x" style="float:right;"></i>' + '</a></td>' +
                                    '<td>' + data[i]['assign_task'] + '</td>' +
                                    '<td>' + data[i]['tm_task'] + '</td>';
                                dp_cnt += '<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="' + data[i]['id'] + '" role_name="' + data[i]['aemp_name'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                count++;
                            }
                            $('#cont_traking').empty();
                            $('#all_dp_content').empty();
                            $('#cont_traking').append(html);
                            $('#all_dp_content').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking").height() + 150);
                        }, error: function (error) {
                            console.log(error);
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                        }
                    });
                }
                if (reportType == 'sr_movement') {
                    $('#tracing_gvt').show();
                    $('#deviation_date_div').hide();
                    $('#export_csv_tracking_gvt').removeAttr('onclick');
                    $('#export_csv_tracking_gvt').attr('onclick', 'exportTableToCSV("sr_movement_summary<?php echo date('Y_m_d'); ?>.csv","tbl_traking_gvt")');
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "get",
                        url: "{{URL::to('/')}}/getSrMovementSummary",
                        success: function (data) {
                            $('#ajax_load').css("display", "none");
                            console.log(data);
                            $('#tableDiv_traking_gvt').show();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            var head1 = '<tr><th>Company</th>' +
                                '<th>8AM ~ 01PM</th>' +
                                '<th>1PM ~ 2PM</th>' +
                                '<th>2PM ~ 7PM</th>' +
                                '<th>7PM+</th></tr>';
                            var html = "";
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['acmp_name'] + '</td>' +
                                    '<td>' + data[i]['1st_slot'] + '</td>' +
                                    '<td>' + data[i]['2nd_slot'] + '</td>' +
                                    '<td>' + data[i]['3rd_slot'] + '</td>' +
                                    '<td>' + data[i]['4th_slot'] + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="' + data[i]['acmp_id'] + '" stage="1">' + ' ' + data[i]['acmp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                            $('#gvt_hierarchy').empty();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            $('#tableDiv_tracking_gvt_header1').append(head1);
                            $('#cont_traking_gvt').empty();
                            $('#cont_traking_gvt').append(html);
                            $('#all_dp_content1').empty();
                            $('#all_dp_content1').append(dp_cnt);
                            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                        },
                        error: function (error) {
                            console.log(error);
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                        }

                    });
                }
            }
            //Abnormal Outlet with Routes start
            function getDeviationDeeperData(v) {
                //$('#deviation_date_div').show();
                var date = $('#deviation_date').val();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                v.removeAttribute('onclick');
                v.setAttribute('onclick', 'appendDeviationTopTitle(this)');
                var _token = $("#_token").val();
                $('#gvt_hierarchy').append(v);
                $('#emid_div1').empty();
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + '">';
                $('#emid_div1').append(emid_cnt);
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getDeviationDeeperData",
                    data: {
                        _token: _token,
                        id: id,
                        date: date,
                        stage: stage,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = '';
                        var dp_cnt = '';
                        var head1 = '';

                        head1 += '</tr>';
                        //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                        if (stage == 1) {
                            head1 = '<tr><th>Group Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {
                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr  style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationDeeperData(this)" id="' + data[i]['slgp_id'] + '"  stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                html += '</tr>';

                            }

                        }
                        else if (stage == 2) {
                            head1 = '<tr><th>Zone Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {
                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationDeeperData(this)" id="' + data[i]['zone_id'] + '" slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                html += '</tr>';

                            }

                        }
                        else if (stage == 3) {
                            head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>Mobile</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {

                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                // dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                                html += '</tr>';

                            }

                        }
                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#all_dp_content1').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);
                        return false;

                    }, error: function (error) {
                        $('#ajax_load').css('display', 'none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                        console.log(error);
                    }
                });

            }
            function appendDeviationTopTitle(v) {
                var date = $('#deviation_date').val();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                $(v).nextAll().remove();
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                var _token = $("#_token").val();
                $('#emid_div1').empty();
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + '" slgp_id="' + slgp_id + '">';
                $('#emid_div1').append(emid_cnt);
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getDeviationDeeperData",
                    data: {
                        _token: _token,
                        id: id,
                        date: date,
                        stage: stage,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = '';
                        var dp_cnt = '';
                        var head1 = '';

                        head1 += '</tr>';
                        //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                        if (stage == 1) {
                            head1 = '<tr><th>Group Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {
                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationDeeperData(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                html += '</tr>';
                            }

                        }
                        if (stage == 2) {
                            head1 = '<tr><th>Zone Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Route</th>' +
                                '<th>Below 60</th>' +
                                '<th>60 ~ 120</th>' +
                                '<th>Above 120</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {
                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationDeeperData(this)" id="' + data[i]['zone_id'] + '" slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                html += '</tr>';

                            }

                        }
                        else if (stage == 3) {
                            head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>Mobile</th>' +
                                '<th>Total Route</th>' +
                                '<th>Underflow</th>' +
                                '<th>Between Limit</th>' +
                                '<th>Overflow</th>' +
                                '<th>Abnormal %</th><tr>';
                            let b = 0;
                            for (var i = 0; i < data.length; i++) {

                                b = parseInt(data[i]['underflow']) + parseInt(data[i]['overflow']);
                                b = (b / parseInt(data[i]['t_rout']) * 100).toFixed(2)
                                html += '<tr style="background-color:' + colorEffect(b, 'd2') + '">' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td>' + data[i]['t_rout'] + '</td>' +
                                    '<td>' + data[i]['underflow'] + '</td>' +
                                    '<td>' + data[i]['between_limit'] + '</td>' +
                                    '<td>' + data[i]['overflow'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(b, 'd2') + '">' + b + '%' + '</td>';
                                // dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                                html += '</tr>';

                            }

                        }
                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#all_dp_content1').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);
                        return false;

                    }, error: function (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                        console.log(error);
                    }
                });
            }
            function getDateWiseDeviationData(date, rpt) {
                var id = $('#gvt_hierarchy_id').val();
                var stage = $('#gvt_hierarchy_id').attr('stage');
                var slgp_id = '';
                if (stage == '') {
                    stage = 0;
                }
                if (stage == 3) {
                    slgp_id = $('#gvt_hierarchy_id').attr('slgp_id');
                }
                var _token = $("#_token").val();
                // if(rpt==1){
                //     $('#ajax_load').show();
                //     $.ajax({
                //         type:"POST",
                //         url:"{{URL::to('/')}}/getDeviationDeeperData/"+stage,
                //         data:{
                //             _token:_token,
                //             id:id,
                //             date:date,
                //             stage:stage
                //         },
                //         dataType: "json",
                //         success: function (data){
                //             console.log(data);
                //             $('#ajax_load').hide()
                //             $('#tableDiv_traking_gvt').show();
                //             $('#tableDiv_tracking_gvt_header1').empty();
                //             var html='';
                //             var dp_cnt='';
                //             var head1='';

                //             head1+='</tr>';
                //             //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                //             if(stage==1){
                //                 head1='<tr><th>Group Name</th>'+
                //                         '<th>Total SR</th>'+
                //                         '<th>Total Route</th>'+
                //                         '<th>Underflow</th>'+
                //                         '<th>Between Limit</th>'+
                //                         '<th>Overflow</th>'+
                //                         '<th>Abnormal %</th><tr>';
                //                         let b=0;
                //                 for(var i=0;i<data.length;i++){
                //                     b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                //                     b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                //                     html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                //                             '<td>'+data[i]['slgp_name']+'</td>'+
                //                             '<td>'+data[i]['t_sr']+'</td>'+
                //                             '<td>'+data[i]['t_rout']+'</td>'+
                //                             '<td>'+data[i]['underflow']+'</td>'+
                //                             '<td>'+data[i]['between_limit']+'</td>'+
                //                             '<td>'+data[i]['overflow']+'</td>'+
                //                             '<td>' + b +'%'+ '</td>';
                //                     dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                //                     html+='</tr>';

                //                 }

                //             }
                //             else if(stage==2){
                //                 head1='<tr><th>Zone Name</th>'+
                //                         '<th>Total SR</th>'+
                //                         '<th>Total Route</th>'+
                //                         '<th>Underflow</th>'+
                //                         '<th>Between Limit</th>'+
                //                         '<th>Overflow</th>'+
                //                         '<th>Abnormal %</th><tr>';
                //                         let b=0;
                //                 for(var i=0;i<data.length;i++){
                //                     b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                //                     b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                //                     html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                //                             '<td>'+data[i]['zone_name']+'</td>'+
                //                             '<td>'+data[i]['t_sr']+'</td>'+
                //                             '<td>'+data[i]['t_rout']+'</td>'+
                //                             '<td>'+data[i]['underflow']+'</td>'+
                //                             '<td>'+data[i]['between_limit']+'</td>'+
                //                             '<td>'+data[i]['overflow']+'</td>'+
                //                             '<td>' + b +'%'+ '</td>';
                //                 // dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                //                     html+='</tr>';

                //                 }

                //             }
                //             else if(stage==3){
                //                 head1='<tr><th>Name</th>'+
                //                     '<th>Staff Id</th>'+
                //                     '<th>Mobile</th>'+
                //                     '<th>Total Route</th>'+
                //                     '<th>Underflow</th>'+
                //                     '<th>Between Limit</th>'+
                //                     '<th>Overflow</th>'+
                //                     '<th>Abnormal %</th><tr>';
                //                     let b=0;
                //                 for(var i=0;i<data.length;i++){

                //                     b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                //                     b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                //                     html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                //                             '<td>'+data[i]['aemp_name']+'</td>'+
                //                             '<td>'+data[i]['aemp_usnm']+'</td>'+
                //                             '<td>'+data[i]['aemp_mob1']+'</td>'+
                //                             '<td>'+data[i]['t_rout']+'</td>'+
                //                             '<td>'+data[i]['underflow']+'</td>'+
                //                             '<td>'+data[i]['between_limit']+'</td>'+
                //                             '<td>'+data[i]['overflow']+'</td>'+
                //                         '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                //                 }
                //             }
                //             else{
                //                 var head1='<tr><th>Company Name</th>'+
                //                             '<th>Total SR</th>'+
                //                             '<th>Total Route</th>'+
                //                             '<th>Underflow</th>'+
                //                             '<th>Between Limit</th>'+
                //                             '<th>Overflow</th>'+
                //                             '<th>Abnormal %</th>';
                //                 head1+='</tr>';
                //                 let b=0;
                //                 for(var i=0;i<data.length;i++){
                //                     b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                //                     b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                //                     html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                //                             '<td>'+data[i]['acmp_name']+'</td>'+
                //                             '<td>'+data[i]['t_sr']+'</td>'+
                //                             '<td>'+data[i]['t_rout']+'</td>'+
                //                             '<td>'+data[i]['underflow']+'</td>'+
                //                             '<td>'+data[i]['between_limit']+'</td>'+
                //                             '<td>'+data[i]['overflow']+'</td>'+
                //                             '<td>' + b +'%'+ '</td>';
                //                     dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['acmp_id']+'" stage="1">'+' '+data[i]['acmp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                //                     html+='</tr>';

                //                 }
                //             }
                //             $('#tableDiv_tracking_gvt_header1').empty();
                //             $('#cont_traking_gvt').empty();
                //             $('#all_dp_content1').empty();
                //             $('#tableDiv_tracking_gvt_header1').append(head1);
                //             $('#cont_traking_gvt').append(html);
                //             $('#all_dp_content1').append(dp_cnt);
                //             $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                //             return false;

                //         },error:function(error){
                //             Swal.fire({
                //                 icon: 'error',
                //                 title: 'Oops...',
                //                 text: 'Something went wrong!',
                //             });
                //             console.log(error);
                //         }
                //     });
                // }
                //outlet visit =2
                if (rpt == 2) {
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "POST",
                        url: "{{URL::to('/')}}/getDeviationData/outletVisit/" + stage,
                        data: {
                            _token: _token,
                            id: id,
                            date: date,
                            stage: stage,
                            slgp_id: slgp_id
                        },
                        dataType: "json",
                        success: function (data) {
                            console.log(data);
                            $('#ajax_load').css("display", "none");
                            $('#tableDiv_traking_gvt').show();
                            $('#tableDiv_tracking_gvt_header1').empty();
                            var html = '';
                            var head1 = '';
                            var dp_cnt = '';
                            if (stage == 1) {
                                head1 = '<tr><th>Group Name</th>' +
                                    '<th>Total SR</th>' +
                                    '<th>Total Outlet</th>' +
                                    '<th>Olt/SR</th>' +
                                    '<th>Visit/SR</th>' +
                                    '<th>Visit Percentage</th></tr>';
                                var percent_visit = 0;
                                for (var i = 0; i < data.length; i++) {
                                    percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                    html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['t_sr'] + '</td>' +
                                        '<td>' + data[i]['t_outlet'] + '</td>' +
                                        '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                    dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                }
                            }
                            else if (stage == 2) {
                                head1 = '<tr><th>Zone Name</th>' +
                                    '<th>Total SR</th>' +
                                    '<th>Total Outlet</th>' +
                                    '<th>Olt/SR</th>' +
                                    '<th>Visit/SR</th>' +
                                    '<th>Visit Percentage</th></tr>';
                                var percent_visit = 0;
                                for (var i = 0; i < data.length; i++) {
                                    percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                    html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                        '<td>' + data[i]['t_sr'] + '</td>' +
                                        '<td>' + data[i]['t_outlet'] + '</td>' +
                                        '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                    dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['zone_id'] + '" slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                }
                            }
                            else if (stage == 3) {
                                head1 = '<tr><th>Name</th>' +
                                    '<th>Staff Id</th>' +
                                    '<th>Mobile</th>' +
                                    '<th>Total Outlet</th>' +
                                    '<th>Visit</th>' +
                                    '<th>Visit Percentage</th></tr>';
                                var percent_visit = 0;
                                for (var i = 0; i < data.length; i++) {
                                    if (data[i]['t_outlet'] != 0) {
                                        percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet']);
                                        percent_visit = percent_visit > 100 ? 100 : percent_visit;
                                    } else {
                                        percent_visit = 0;
                                    }
                                    html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                        '<td>' + data[i]['t_outlet'] + '</td>' +
                                        '<td>' + data[i]['t_visit'] + '</td>' +
                                        '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                    // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                                }
                            }
                            else {
                                head1 = '<tr><th>Company</th>' +
                                    '<th>Total SR</th>' +
                                    '<th>Total Outlet</th>' +
                                    '<th>Olt/SR</th>' +
                                    '<th>Visit/SR</th>' +
                                    '<th>Visit Percentage</th></tr>';
                                var percent_visit = 0;
                                for (var i = 0; i < data.length; i++) {
                                    percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                    html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['t_sr'] + '</td>' +
                                        '<td>' + data[i]['t_outlet'] + '</td>' +
                                        '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                        '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                    dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                                }
                            }
                            $('#tableDiv_tracking_gvt_header1').empty();
                            $('#cont_traking_gvt').empty();
                            $('#tableDiv_tracking_gvt_header1').append(head1);
                            $('#cont_traking_gvt').append(html);
                            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                        }, error: function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            });
                            console.log(error);
                        }
                    });
                }
            }
            //Abnormal Outlet with Routes End here
            // Outlet Visit %
            function getDeviationOutletVisit(v) {
                $('#deviation_date_div').show();
                $('#deviation_date').removeAttr('onchange');
                $('#deviation_date').attr('onchange', 'getDateWiseDeviationData(this.value,2)');
                var date = $('#deviation_date').val();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                var _token = $("#_token").val();
                // $('#gvt_hierarchy').empty();
                v.removeAttribute('onclick');
                v.setAttribute('onclick', 'devOutletVisitTopClick(this)');
                $('#gvt_hierarchy').append(v);
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + '" slgp_id="' + slgp_id + '">';
                $('#emid_div1').empty();
                $('#emid_div1').append(emid_cnt);
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getDeviationData/outletVisit/" + stage,
                    data: {
                        _token: _token,
                        id: id,
                        date: date,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        //console.log(data);
                        $('#ajax_load').css("display", "none");
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = '';
                        var head1 = '';
                        var dp_cnt = '';

                        if (stage == 1) {
                            head1 = '<tr><th>Group Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 2) {
                            head1 = '<tr><th>Zone Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['zone_id'] + '"  slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 3) {
                            head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>Mobile</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Visit</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                if (data[i]['t_outlet'] != 0) {
                                    percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet']);
                                    percent_visit = percent_visit > 100 ? 100 : percent_visit;
                                } else {
                                    percent_visit = 0;
                                }
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + data[i]['t_visit'] + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                            }
                        }

                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').empty();
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                    }, error: function (error) {
                        $('#ajax_load').css("display", "none");
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                        console.log(error);
                    }
                });
            }
            function devOutletVisitTopClick(v) {
                $(v).nextAll().remove();
                var date = $('#deviation_date').val();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                var _token = $("#_token").val();
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + '" slgp_id="' + slgp_id + '">';
                $('#emid_div1').empty();
                $('#emid_div1').append(emid_cnt);
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getDeviationData/outletVisit/" + stage,
                    data: {
                        _token: _token,
                        id: id,
                        date: date,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = '';
                        var head1 = '';
                        var dp_cnt = '';
                        if (stage == 1) {
                            head1 = '<tr><th>Group Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 2) {
                            head1 = '<tr><th>Zone Name</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['zone_id'] + '" slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 3) {
                            head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>Mobile</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Visit</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                if (data[i]['t_outlet'] != 0) {
                                    percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet']);
                                } else {
                                    percent_visit = 0;
                                }
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + data[i]['t_visit'] + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                            }
                        }
                        else {
                            head1 = '<tr><th>Company</th>' +
                                '<th>Total SR</th>' +
                                '<th>Total Outlet</th>' +
                                '<th>Olt/SR</th>' +
                                '<th>Visit/SR</th>' +
                                '<th>Visit Percentage</th></tr>';
                            var percent_visit = 0;
                            for (var i = 0; i < data.length; i++) {
                                percent_visit = (100 * data[i]['t_visit']) / (data[i]['t_outlet'] < 1 ? 1 : data[i]['t_outlet']);
                                html += '<tr style="background-color:' + colorEffect(percent_visit, 'd3') + '">' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    '<td>' + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_visit'] / data[i]['t_sr']).toFixed(2) + '</td>' +
                                    '<td >' + percent_visit.toFixed(2) + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getDeviationOutletVisit(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').empty();
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                    }, error: function (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                        console.log(error);
                    }
                });
            }
            //Note Task Deeper Data
            function devNoteTaskClickAppend(v) {
                var emid = $(v).attr('emid');
                v.removeAttribute('onclick');
                v.setAttribute('onclick', 'devNoteAppendRemove(this)');
                var date = $('#dev_note_date').val();
                var _token = $("#_token").val();
                $('#desig').append(v);
                $('#emid_div').empty();
                var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
                $('#emid_div').append(emid_cnt);
                $('#employee_sales_traking_report_slgp').hide();
                $('#emp_task_note_report').show();
                $('#period').hide();
                $('#dev_note_date').show();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getUserAndDateWiseTaskNote",
                    data: {
                        _token: _token,
                        emid: emid,
                        date: date
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        $('#head_tracking_dev_note_task').empty();
                        var head = '<td>Sl</td>' +
                            '<td>Name</td>' +
                            '<td>Mobile</td>' +
                            '<td>Own Task</td>' +
                            '<td>Assign Task</td>' +
                            '<td>Team Task</td>';
                        $('#head_tracking_dev_note_task').append(head);

                        var html = "";
                        var count = 1;
                        var dp_cnt = '';
                        var task_details = '';
                        for (var i = 0; i < data.length; i++) {
                            task_details = 'getTaskDetails/' + data[i]['id'] + '/' + date;
                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td><a href="' + task_details + '" target="_blank">' + data[i]['t_note'] + '  ' + '<i class="fa fa-eye fa-2x" style="float:right;"></i>' + '</a></td>' +
                                '<td>' + data[i]['assign_task'] + '</td>' +
                                '<td>' + data[i]['tm_task'] + '</td>';
                            dp_cnt += '<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="' + data[i]['id'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            count++;
                        }
                        $('#cont_traking').empty();
                        $('#all_dp_content').empty();
                        $('#cont_traking').append(html);
                        $('#all_dp_content').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking").height() + 150);
                    }, error: function (error) {
                        $('#ajax_load').css("display", "none");
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        })
                    }
                });
            }
            function getDateWiseDevTaskNote(date) {
                var emid = $('#emid').val();
                var _token = $("#_token").val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getUserAndDateWiseTaskNote",
                    data: {
                        _token: _token,
                        emid: emid,
                        date: date
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        $('#head_tracking_dev_note_task').empty();
                        var head = '<td>Sl</td>' +
                            '<td>Name</td>' +
                            '<td>Mobile</td>' +
                            '<td>Own Task</td>' +
                            '<td>Assign Task</td>' +
                            '<td>Team Task</td>';
                        $('#head_tracking_dev_note_task').append(head);
                        var html = "";
                        var count = 1;
                        var dp_cnt = '';
                        var task_details = '';
                        for (var i = 0; i < data.length; i++) {
                            task_details = 'getTaskDetails/' + data[i]['id'] + '/' + date;
                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td><a href="' + task_details + '" target="_blank">' + data[i]['t_note'] + '  ' + '<i class="fa fa-eye fa-2x" style="float:right;"></i>' + '</a></td>' +
                                '<td>' + data[i]['assign_task'] + '</td>' +
                                '<td>' + data[i]['tm_task'] + '</td>';
                            dp_cnt += '<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="' + data[i]['id'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            count++;
                        }
                        $('#cont_traking').empty();
                        $('#all_dp_content').empty();
                        $('#cont_traking').append(html);
                        $('#all_dp_content').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking").height() + 150);
                    }, error: function (error) {
                        $('#ajax_load').css("display", "none");
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        })
                    }
                });
            }
            function devNoteAppendRemove(elem) {
                $(elem).nextAll().remove();
                var emid = $(elem).attr('emid');
                $('#emid_div').empty();
                var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
                $('#emid_div').append(emid_cnt);
                var emid = $(elem).attr('emid');
                var _token = $("#_token").val();
                var date = $('#dev_note_date').val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getUserAndDateWiseTaskNote",
                    data: {
                        _token: _token,
                        emid: emid,
                        date: date
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        $('#head_tracking_dev_note_task').empty();
                        var head = '<td>Sl</td>' +
                            '<td>Name</td>' +
                            '<td>Mobile</td>' +
                            '<td>Own Task</td>' +
                            '<td>Assign Task</td>' +
                            '<td>Team Task</td>';
                        $('#head_tracking_dev_note_task').append(head);

                        var html = "";
                        var count = 1;
                        var dp_cnt = '';
                        var task_details = '';
                        for (var i = 0; i < data.length; i++) {
                            task_details = 'getTaskDetails/' + data[i]['id'] + '/' + date;
                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td><a href="' + task_details + '" target="_blank">' + data[i]['t_note'] + '  ' + '<i class="fa fa-eye fa-2x" style="float:right;"></i>' + '</a></td>' +
                                '<td>' + data[i]['assign_task'] + '</td>' +
                                '<td>' + data[i]['tm_task'] + '</td>';
                            dp_cnt += '<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="' + data[i]['id'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            count++;
                        }
                        $('#cont_traking').empty();
                        $('#all_dp_content').empty();
                        $('#cont_traking').append(html);
                        $('#all_dp_content').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking").height() + 150);
                    }, error: function (error) {
                        $('#ajax_load').css("display", "none");
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        })
                    }
                });
            }
            function getSrMovementSummaryDeeparData(v) {
                $('#deviation_date_div').hide();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                v.removeAttribute('onclick');
                v.setAttribute('onclick', 'appendTopTitleSrMovementSummary(this)');
                var _token = $("#_token").val();
                $('#gvt_hierarchy').append(v);
                $('#emid_div1').empty();
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + ' slgp_id="' + slgp_id + '">';
                $('#emid_div1').append(emid_cnt);
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getSrMovementSummaryDeeparData",
                    data: {
                        _token: _token,
                        id: id,
                        stage: stage,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = "";
                        var dp_cnt = '';
                        var head1 = '';
                        //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                        if (stage == 1) {
                            var head1 = '<tr><th>Group</th>' +
                                '<th>8AM ~ 01PM</th>' +
                                '<th>1PM ~ 2PM</th>' +
                                '<th>2PM ~ 7PM</th>' +
                                '<th>7PM+</th></tr>';
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['1st_slot'] + '</td>' +
                                    '<td>' + data[i]['2nd_slot'] + '</td>' +
                                    '<td>' + data[i]['3rd_slot'] + '</td>' +
                                    '<td>' + data[i]['4th_slot'] + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 2) {
                            var head1 = '<tr><th>Zone</th>' +
                                '<th>8AM ~ 01PM</th>' +
                                '<th>1PM ~ 2PM</th>' +
                                '<th>2PM ~ 7PM</th>' +
                                '<th>7PM+</th></tr>';
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['1st_slot'], 'sr_mov_sum') + '">' + data[i]['1st_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['2nd_slot'], 'sr_mov_sum') + '">' + data[i]['2nd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['3rd_slot'], 'sr_mov_sum') + '">' + data[i]['3rd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['4th_slot'], 'sr_mov_sum') + '">' + data[i]['4th_slot'] + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="' + data[i]['zone_id'] + '" stage="3" slgp_id="' + data[i]['slgp_id'] + '">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 3) {
                            var head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>8AM ~ 01PM</th>' +
                                '<th>1PM ~ 2PM</th>' +
                                '<th>2PM ~ 7PM</th>' +
                                '<th>7PM+</th></tr>';
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['1st_slot'], 'sr_mov_sum') + '">' + data[i]['1st_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['2nd_slot'], 'sr_mov_sum') + '">' + data[i]['2nd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['3rd_slot'], 'sr_mov_sum') + '">' + data[i]['3rd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['4th_slot'], 'sr_mov_sum') + '">' + data[i]['4th_slot'] + '</td></tr>';
                                // dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['aemp_usnm']+'" stage="4">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                            }
                        }
                        else {
                            html = '<h2><tr>Hierarchy End!!</tr></h2>';
                        }
                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#all_dp_content1').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                    }, error: function (error) {
                        $('#ajax_load').css("display", "none");
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong !',
                        });
                    }
                });
            }
            function appendTopTitleSrMovementSummary(v) {
                $('#deviation_date_div').hide();
                var id = $(v).attr('id');
                var stage = $(v).attr('stage');
                var slgp_id = '';
                if (stage == 3) {
                    slgp_id = $(v).attr('slgp_id');
                }
                $(v).nextAll().remove();
                var _token = $("#_token").val();
                $('#emid_div1').empty();
                var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + ' slgp_id="' + slgp_id + '">';
                $('#emid_div1').append(emid_cnt);
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{URL::to('/')}}/getSrMovementSummaryDeeparData",
                    data: {
                        _token: _token,
                        id: id,
                        stage: stage,
                        slgp_id: slgp_id
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        $('#tableDiv_traking_gvt').show();
                        $('#tableDiv_tracking_gvt_header1').empty();
                        var html = "";
                        var dp_cnt = '';
                        var head1 = '';
                        //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                        if (stage == 1) {
                            var head1 = '<tr><th>Group</th>' +
                                '<th>09AM-01PM</th>' +
                                '<th>02PM-05PM</th>' +
                                '<th>06PM-09PM</th><tr>';
                            var html = "";
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['1st_slot'] + '</td>' +
                                    '<td>' + data[i]['2nd_slot'] + '</td>' +
                                    '<td>' + data[i]['3rd_slot'] + '</td>' +
                                    '<td>' + data[i]['4th_slot'] + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="' + data[i]['slgp_id'] + '" stage="2">' + ' ' + data[i]['slgp_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 2) {
                            var head1 = '<tr><th>Zone</th>' +
                                '<th>09AM-01PM</th>' +
                                '<th>02PM-05PM</th>' +
                                '<th>06PM-09PM</th><tr>';
                            var html = "";
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['1st_slot'], 'sr_mov_sum') + '">' + data[i]['1st_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['2nd_slot'], 'sr_mov_sum') + '">' + data[i]['2nd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['3rd_slot'], 'sr_mov_sum') + '">' + data[i]['3rd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['4th_slot'], 'sr_mov_sum') + '">' + data[i]['4th_slot'] + '</td></tr>';
                                dp_cnt += '<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="' + data[i]['zone_id'] + '" slgp_id="' + data[i]['slgp_id'] + '" stage="3">' + ' ' + data[i]['zone_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                            }
                        }
                        else if (stage == 3) {
                            var head1 = '<tr><th>Name</th>' +
                                '<th>Staff Id</th>' +
                                '<th>8AM ~ 01PM</th>' +
                                '<th>1PM ~ 2PM</th>' +
                                '<th>2PM ~ 7PM</th>' +
                                '<th>7PM+</th></tr>';
                            var dp_cnt = '';
                            for (var i = 0; i < data.length; i++) {
                                html += '<tr>' +
                                    '<td>' + data[i]['aemp_name'] + '</td>' +
                                    '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['1st_slot'], 'sr_mov_sum') + '">' + data[i]['1st_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['2nd_slot'], 'sr_mov_sum') + '">' + data[i]['2nd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['3rd_slot'], 'sr_mov_sum') + '">' + data[i]['3rd_slot'] + '</td>' +
                                    '<td style="background-color:' + colorEffect(data[i]['4th_slot'], 'sr_mov_sum') + '">' + data[i]['4th_slot'] + '</td></tr>';
                                // dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['aemp_usnm']+'" stage="4">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                            }
                        }
                        else {
                            var html = '<h2><tr>Hierarchy End!!</tr></h2>';
                        }
                        $('#tableDiv_tracking_gvt_header1').empty();
                        $('#cont_traking_gvt').empty();
                        $('#all_dp_content1').empty();
                        $('#tableDiv_tracking_gvt_header1').append(head1);
                        $('#cont_traking_gvt').append(html);
                        $('#all_dp_content1').append(dp_cnt);
                        $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

                    }, error: function (error) {
                        console.log(error);
                        $('#ajax_load').css("display", "none");
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                });
            }
            function showWardWiseVisitDetails(v) {
                $("#myModalWardWiseVisit").modal({backdrop: false});
                $('#myModalWardWiseVisit').modal('show');
                $('#load_ward_visit').show();
                var date = $(v).attr('date');
                var emp_id = $(v).attr('sr_id');
                $.ajax({
                    type: "get",
                    url: "{{URL::to('/')}}/getWardWiseVisitDetails/" + emp_id + "/" + date,
                    dataType: "json",
                    success: function (data) {
                        $('#load_ward_visit').hide();
                        $('#myModalWardWiseVisitBody2').empty();
                        $('#myModalWardWiseVisitBody3').empty();
                        var html = '';
                        var html1 = '';
                        var html2 = '';
                        var html3 = '';
                        var count = 1;
                        count = 1
                        for (var i = 0; i < data.data2.length; i++) {
                            html1 += '<tr><td>' + count + '</td>' +
                                '<td>' + data.data2[i]["than_name"] + '</td>' +
                                '<td>' + data.data2[i]["t_visit"] + '</td>' +
                                '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showWardWiseVisitOutletDetails(this,3,' + data.data2[i]["id"] + ')" class="fa fa-info-circle fa-2x  pull-right" sr_id="' + data.data1[i]["aemp_id"] + '" date="' + data.data1[i]["ssvh_date"] + '"></i></td>';
                            count++;

                        }
                        count = 1
                        for (var i = 0; i < data.data3.length; i++) {
                            html2 += '<tr><td>' + count + '</td>' +
                                '<td>' + data.data3[i]["ward_name"] + '</td>' +
                                '<td>' + data.data3[i]["t_visit"] + '</td>' +
                                '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showWardWiseVisitOutletDetails(this,2,' + data.data3[i]["id"] + ')" class="fa fa-info-circle fa-2x  pull-right" sr_id="' + data.data1[i]["aemp_id"] + '" date="' + data.data1[i]["ssvh_date"] + '"></i></td>';
                            count++;

                        }
                        $('#myModalWardWiseVisitBody2').append(html1);
                        $('#myModalWardWiseVisitBody3').append(html2);
                        console.log(data);
                    }, error: function (error) {
                        console.log(error);
                    }
                });
            }
            function showWardWiseVisitOutletDetails(v, type, stage) {
                var id = $(v).attr("sr_id");
                var date = $(v).attr("date");
                var _token = $("#_token").val();
                $("#myModalVisitedOutletDetails").modal({backdrop: false});
                $('#myModalVisitedOutletDetails').modal('show');
                $('#visit_out_load_details').show();
                $.ajax({
                    type: "post",
                    url: "{{URL::to('/')}}/showWardWiseVisitOutletDetails/",
                    data: {
                        id: id,
                        date: date,
                        type: type,
                        stage: stage,
                        _token: _token,
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#visit_out_load_details').hide();
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            html += '<tr><td>' + data[i]['site_code'] + '</td>' +
                                '<td>' + data[i]['site_name'] + '</td>' +
                                '<td>' + data[i]['site_mob1'] + '</td>' +
                                '<td>' + data[i]['site_adrs'] + '</td>' +
                                '</tr>';
                        }
                        $('#myModalVisitedOutletDetailsBody').empty();
                        $('#myModalVisitedOutletDetailsBody').append(html);
                    }, error: function (error) {
                        console.log(error);
                    }
                });


            }

            function showCatWiseOutlet(v, cat_id) {
                var id = $(v).attr("sr_id");
                var date = $(v).attr("date");
                var _token = $("#_token").val();
                $("#myModalVisitedOutletDetails").modal({backdrop: false});
                $('#myModalVisitedOutletDetails').modal('show');
                $('#visit_out_load_details').show();
                $.ajax({
                    type: "post",
                    url: "{{URL::to('/')}}/showCatWiseOutlet/",
                    data: {
                        id: id,
                        date: date,
                        cat_id: cat_id,
                        _token: _token,
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#visit_out_load_details').hide();
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            html += '<tr><td>' + data[i]['site_code'] + '</td>' +
                                '<td>' + data[i]['site_name'] + '</td>' +
                                '<td>' + data[i]['site_mob1'] + '</td>' +
                                '<td>' + data[i]['site_adrs'] + '</td>' +
                                '</tr>';
                        }
                        $('#myModalVisitedOutletDetailsBody').empty();
                        $('#myModalVisitedOutletDetailsBody').append(html);
                    }, error: function (error) {
                        console.log(error);
                    }
                });


            }
            function showAllVisitedOutletList(v) {
                var id = $(v).attr("sr_id");
                var date = $(v).attr("date");
                var _token = $("#_token").val();
                $("#myModalVisitCat").modal({backdrop: false});
                $('#myModalVisitCat').modal('show');
                $('#myModalVisitCat_load').show();
                $.ajax({
                    type: "post",
                    url: "{{URL::to('/')}}/showAllVisitedOutletList/",
                    data: {
                        id: id,
                        date: date,
                        _token: _token,
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#myModalVisitCat_load').hide();
                        var html = '';
                         count = 1
                        for (var i = 0; i < data.length; i++) {
                            html += '<tr><td>' + count + '</td>' +
                                '<td>' + data[i]["otcg_name"] + '</td>' +
                                '<td>' + data[i]["num"] + '</td>' +
                                '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showCatWiseOutlet(this,' + data[i]["id"] + ')" class="fa fa-info-circle fa-2x  pull-right" sr_id="' + data[i]["aemp_id"] + '" date="' + data[i]["ssvh_date"] + '"></i></td>';
                            count++;
                        }
                        $('#myModalVisitCat_body').html(html);
                    }, error: function (error) {
                        console.log(error);
                    }
                });


            }
            function reportRequestPlacement1() {
                hide_me();
                var reportType = $("input[name='reportType']:checked").val();
                var acmp_id = $('#acmp_id').val();
                var dirg_id = $('#dirg_id').val();
                var sales_group_id = $('#sales_group_id').val();
                var zone_id = $('#zone_id').val();
                var dist_id = $('#dist_id').val();
                var than_id = $('#than_id').val();
                var _token = $("#_token").val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var astm_id = $('#astm_id').val();
                var rtype=$('#dtls_sum').val();
                var utype=$('#sr_sv').val();
                var sr_zone=$('#sr_zone').val();
                var validityCheck = false;

                if (reportType === undefined) {
                    alert('Please select report');
                    return false;
                }
                else if (reportType == '') {
                    alert('Please select report');
                    return false;
                }
                if (reportType == 'market_outlet_sr_outlet') {
                    validityCheck = validateInputField(reportType, acmp_id, sales_group_id, start_date_period, dist_id, than_id);
                }
                else {
                    validityCheck = validateInputField(reportType, acmp_id, sales_group_id, start_date_period);
                }
                if (validityCheck != false) {
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type: "POST",
                        url: "{{ URL::to('/')}}/commonReportRequest",
                        data: {
                            reportType: reportType,
                            acmp_id: acmp_id,
                            zone_id: zone_id,
                            sales_group_id: sales_group_id,
                            dist_id: dist_id,
                            than_id: than_id,
                            dirg_id: dirg_id,
                            rtype: rtype,
                            utype: utype,
                            sr_zone: sr_zone,
                            start_date: start_date,
                            end_date: end_date,
                            astm_id:astm_id,
                            _token: _token
                        },
                        cache: false,
                        dataType: "json",
                        success: function (data) {
                            console.log(data);
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                title: 'Success!',
                                text: 'Thanks for your report request. You will get notified via email withing next 24 Hours!!',
                            })
                        }, error: function (error) {
                            console.log(error);
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                title: 'Please wait!',
                                text: 'Check it by clicking the  "requested report status". If you do not find it here after 5 minutes then make another request!!!',
                            })
                        }
                    });
                }
            }
            function getAttendanceLocation(v){
            var aemp_id=$(v).attr('aemp_id');
            var attn_date=$(v).attr('attn_date');
            $(v).css('style','background-color:red;')
            $.ajax({
                type:"GET",
                url:"attendance/location/"+aemp_id+"/"+attn_date,
                success:function(res){
                    $('#attn_loc_map').empty();
                    var  npv=window.location.origin+ "/theme/image/map_icon/npv.png";
                    var  pv= window.location.origin+"/theme/image/map_icon/pv.png";
                    var data=res[0];
                    console.log(data)
                    var start_loc=data.start_loc.split(',');
                    
                    var latlon=[];
                    latlon.push({ lat:start_loc[0], lng: start_loc[1] })
                    if(data.end_loc){
                        var end_loc=data.end_loc.split(',');
                        latlon.push({ lat:end_loc[0], lng: end_loc[1] });
                    }
                    console.log(latlon)
                    var map = new google.maps.Map(document.getElementById('attn_loc_map'), {
                            zoom: 15,
                            center: new google.maps.LatLng(start_loc[0], start_loc[1]),
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                    });
                    var marker;
                    for (i = 0; i < latlon.length; i++) {

                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(latlon[i]['lat'], latlon[i]['lng']),
                            map: map
                        });
                        if(i==0){
                            marker.setIcon(pv);
                        }else{
                            marker.setIcon(npv);
                        }
                    }
                    
                    
                },error:function(error){
                    console.log(error)
                }
            });
        }

            function getNoteImage(v){
                let note_id=$(v).attr('id');
                $("#note_image_modal").modal({ backdrop: false });
                $("#note_image_modal").modal('show');
                $('#note_image_modal_load').show();
                $('#note_image_modal_body').empty();
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/line/note/details/"+note_id,
                    dataType:"json",
                    success:function(data){

                        let note_image_modal_info = '';
                        let indicator = ''
                        let carousel_items = ''
                        let multiple_images = (data.length > 1) ? '' : `style="display: none"`;

                        let image_src='https://images.sihirbox.com/';

                        $('#note_image_modal_load').hide();

                        let active;

                        for(let i=0;i<data.length;i++) {

                            active = (i === 0) ? 'active' : ''

                            let img = data[i].nimg_imag;

                            indicator += `<li data-target="#quote-carousel" data-slide-to="${i}" class="${active}"></li>`

                            carousel_items += `
                                          <div class="item ${active}">
                                            <blockquote>
                                              <div class="row">
                                                <div class="text-center">
                                                  <img class="carousel_img" src="https://images.sihirbox.com/${img}">
                                                </div>
                                              </div>
                                            </blockquote>
                                          </div>`
                        }

                        note_image_modal_info=`<div class='row'>
                                <div class='col-md-12 col-sm-12'>
                                  <div class="carousel slide" data-ride="carousel" id="quote-carousel">
                                    <!-- Bottom Carousel Indicators -->

                                    <ol class="carousel-indicators" ${multiple_images}>
                                        ${indicator}
                                    </ol>

                                    <!-- Carousel Slides / Quotes -->
                                    <div class="carousel-inner">
                                      ${carousel_items}
                                    </div>

                                    <!-- Carousel Buttons Next/Prev -->
                                    <a data-slide="prev" ${multiple_images} href="#quote-carousel" class="left carousel-control"><i class="fa fa-chevron-left"></i></a>
                                    <a data-slide="next" ${multiple_images} href="#quote-carousel" class="right carousel-control"><i class="fa fa-chevron-right"></i></a>
                                  </div>
                                </div>
                              </div>`;

                        $('#note_image_modal_body').append(note_image_modal_info);
                    },
                    error:function(error){
                        console.log(error);
                    }
                });
            }


            // Survey Image
            function getSurveyImage(v){
                let note_id=$(v).attr('id');
                $("#note_image_modal").modal({ backdrop: false });
                $("#note_image_modal").modal('show');
                $('#note_image_modal_load').show();
                $('#note_image_modal_body').empty();
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/line/item_survey/details/"+note_id,
                    dataType:"json",
                    success:function(data){

                        let note_image_modal_info = '';
                        let indicator = ''
                        let carousel_items = ''
                        let multiple_images = (data.length > 1) ? '' : `style="display: none"`;

                        let image_src='https://images.sihirbox.com/';

                        $('#note_image_modal_load').hide();

                        let active;

                        for(let i=0;i<data.length;i++) {

                            active = (i === 0) ? 'active' : ''

                            let img = data[i].nimg_imag;

                            indicator += `<li data-target="#quote-carousel" data-slide-to="${i}" class="${active}"></li>`

                            carousel_items += `
                                          <div class="item ${active}">
                                            <blockquote>
                                              <div class="row">
                                                <div class="text-center">
                                                  <img class="carousel_img" src="https://images.sihirbox.com/${img}">
                                                </div>
                                              </div>
                                            </blockquote>
                                          </div>`
                        }

                        note_image_modal_info=`<div class='row'>
                                <div class='col-md-12 col-sm-12'>
                                  <div class="carousel slide" data-ride="carousel" id="quote-carousel">
                                    <!-- Bottom Carousel Indicators -->

                                    <ol class="carousel-indicators" ${multiple_images}>
                                        ${indicator}
                                    </ol>

                                    <!-- Carousel Slides / Quotes -->
                                    <div class="carousel-inner">
                                      ${carousel_items}
                                    </div>

                                    <!-- Carousel Buttons Next/Prev -->
                                    <a data-slide="prev" ${multiple_images} href="#quote-carousel" class="left carousel-control"><i class="fa fa-chevron-left"></i></a>
                                    <a data-slide="next" ${multiple_images} href="#quote-carousel" class="right carousel-control"><i class="fa fa-chevron-right"></i></a>
                                  </div>
                                </div>
                              </div>`;

                        $('#note_image_modal_body').append(note_image_modal_info);
                    },
                    error:function(error){
                        console.log(error);
                    }
                });
            }
</script>

<script src="{{asset('theme/src/js/Advance/filter_date_control.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/c_sr_attendance_rpt.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/c_sr_activity_polyline_map.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/c_sr_sales_gvt_monitoring.js')}}"></script>
<!-- <script src="{{asset('theme/src/js/Advance/date_control.js')}}"></script> -->
<script src="{{asset('theme/src/js/Advance/c_filter_division_control.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/c_sr_note_report.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/asset_report.js')}}"></script>
<script src="{{asset('theme/src/js/Advance/c_report_request.js')}}"></script>
<script>
$(document).ready(function(){
    $('.carousel_img').hover(function() {
        $(this).css("cursor", "pointer");
        $(this).toggle({
          effect: "scale",
          percent: "90%"
        },200);
    }, function() {
         $(this).toggle({
           effect: "scale",
           percent: "80%"
         },200);

    });

    //carousel options
    $('#quote-carousel').carousel({
        pause: true,
        interval: 4000,
    });
});
</script>
@endsection