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
                                {{csrf_field()}}


                                <div class="col-md-6 col-sm-6 col-xs-12" id="rp">
                                    <div class="item form-group rp_type_div" id="rpt">
                                        <div class="x_title">
                                            <div class="btnDiv col-md-12 col-sm-12 col-xs-12" style="margin-bottom:5px;">
                                                <div id="exTab1" class="container">	
                                                    <ul  class="nav nav-pills">
                                                        <li>
                                                            <a  href="#1a" data-toggle="tab"  onclick="getSRReport()">SR </a>
                                                        </li>
                                                        <li><a href="#2a" data-toggle="tab"  onclick="getOutletReport()"> Outlet</a>
                                                        </li>
                                                        <li><a href="#3a" data-toggle="tab" onclick="getOrderReport()"> Order</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab" onclick="getDeviationReport()" >
                                                            Deviation</a>
                                                        <li><a href="#4a" data-toggle="tab"  onclick="getNoteReport()" > Note</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab"  onclick="getTopBottom()" >Top-Bottom</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab"    onclick="getHistoryReport()" >History</a>
                                                        </li>
                                                        <li><a href="#4a" data-toggle="tab"    onclick="getEmpTrackingReport()" >Monitoring</a>
                                                        </li>
                                                    </ul>

                                                </div> 

                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="font-size: 11px; height: 115px; border-radius:10%;" id="rpt_selection_div">
                                            <div id="sr_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                <div class="col-md-6 col-sm-6">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_activity" /> SR Activity
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_productivity" /> Productive SR
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_non_productivity" /> Non-Productive SR
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_summary_by_group" /> SR Summary (By Group)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_activity_hourly_visit" /> SR Hourly Activity (Visit)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_activity_hourly_order" /> SR Hourly Activity (Order)
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="outlet_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="market_outlet_sr_outlet" /> Market Outlet vs SR Outlet
                                                    </label>
                                                </div>
                                                {{--<div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="w_class" checked/> Outlet Order Summary
                                                    </label>
                                                </div>--}}

                                            </div>
                                            <div id="emp_tracking_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat emp_tracking_input" name="emp_tracking_reportType"
                                                               id="emp_tracking_reportType"
                                                               value="emp_tracking_sales_hierarchy" onclick="getDiggingReport(this.value)"/>&nbsp;&nbsp;Executive Summary(Sales Hierarchy)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat emp_tracking_input" name="emp_tracking_reportType"
                                                               id="emp_tracking_reportType"
                                                               value="emp_tracking_gvt_hierarchy" onclick="getDiggingReport(this.value)"/>&nbsp;&nbsp;Executive Summary(Govt. Hierarchy)
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="history_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="historyType"
                                                               id="historyType"
                                                               value="sr_history" />SR History
                                                    </label>
                                                </div>
                                                {{--<div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="w_class" checked/> Outlet Order Summary
                                                    </label>
                                                </div>--}}

                                            </div>
                                        <!-- employee tracking report selection div -->
                                            <!-- tracking employee based on sales hierarchy -->
                                            <div id="tracing" class="col-md-12 col-sm-12 col-xs-12 tracing">
                                                
                                                <div class="col-md-10 col-sm-10 col-xs-12" id="user_level" style="font-size:14px;">
                                                        <div >
                                                           <div style="float:left;"><p style="font-weight:bold; margin-right:3px;">     <span id="desig"></span> </p></div>
                                                           
                                                           <div class="btn-group dropright" style="float:left;">
                                                                <a href="#" class=" dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size:15px;font-weight:bold;background-color:#169F85; color:white;">
                                                                   ALL &nbsp;
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="about-us" style="margin-left:35px;margin-top:-20px;" id="all_dp_content">
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div >
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
                                                <div class="col-md-2 col-sm-2 col-xs-12 float-right" style="margin-bottom:8px;">
                                                    <input type="text" class="form-control in_tg start_date" name="start_date"
                                                               id="period" autocomplete="off" value="<?php echo date('Y-m-d'); ?>" onchange="getDateWiseUserReport(this.value,1)"/>
                                                    <input type="text" class="form-control in_tg start_date" name="start_date"
                                                    id="dev_note_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>" onchange="getDateWiseDevTaskNote(this.value)" style="display:none;"/>
                                                </div>
                                                {{--traking report --}}
                                                
                                                <div id="tableDiv_traking" class="col-md-12 col-sm-12">
                                                    <div class="x_panel">
                                                        <div class="x_content">
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                                            
                                                                <div align="right" style="margin-bottom:10px;">

                                                                    <a href="#" onclick="exportTableToCSV('employee_sales_traking_report_slgp_<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking')"
                                                                            class="btn btn-warning" id="employee_sales_traking_report_slgp">Export CSV File
                                                                    </a>
                                                                    <a href="#" onclick="exportTableToCSV('emp_task_note_report<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking')"
                                                                            class="btn btn-warning" style="display:none;" id="emp_task_note_report">Export CSV File
                                                                    </a>
                                                                </div>
                                                                <table id="datatablesa" class="table table-bordered table-responsive"
                                                                    data-page-length='100'>
                                                                    <thead>
                                                                    <tr class="" id="head_tracking_dev_note_task">
                                                                        <th>Sl</th>
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
                                                <div class="col-md-11 col-sm-11 col-xs-12" id="user_level" style="font-size:14px;">
                                                        <div >
                                                           <div style="float:left;"><p style="font-weight:bold; margin-right:3px;"><span id="gvt_hierarchy"></span> </p></div>
                                                           <div id='emid_div1' style="display:none;">
                                                                
                                                            </div>
                                                           <div class="btn-group dropright" style="float:left;">
                                                                <a href="#" class=" dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size:15px;font-weight:bold;background-color:#169F85; color:white;">
                                                                   ALL &nbsp;
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="about-us" style="margin-left:35px;margin-top:-20px;" id="all_dp_content1">
                                                                </ul>
                                                            </div>
                                                        </div>      
                                                </div>
                                                <div class="col-md-1 col-sm-1 col-xs-12 float-right deviation_date" style="margin-bottom:8px;" id="deviation_date_div">
                                                        <input type="text" class="form-control in_tg start_date" name="start_date"
                                                               id="deviation_date"
                                                               autocomplete="off" value="<?php echo date('Y-m-d'); ?>" onchange="getDateWiseDeviationData(this.value,1)"/>
                                                </div>
                                                <br><br>
                                                <div id="tableDiv_traking_gvt" class="col-md-12 col-sm-12" style="margin-top:15px;">
                                                    <div class="x_panel">
                                                        <div class="x_content">
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style="height:600px;overflow: auto;">
                                                            
                                                                <div align="right" style="margin-bottom:10px;">

                                                                    <a href="#" onclick="exportTableToCSV('employee_sales_traking_report_gvt_<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking_gvt')"
                                                                            class="btn btn-default" id="export_csv_tracking_gvt">Export CSV File
                                                                    </a>
                                                                </div>
                                                                <table id="datatablesa" class="table table-bordered table-responsive"
                                                                >
                                                                    <thead id="tableDiv_tracking_gvt_header1" style="font-size:11px;">
                                                                    <!-- <tr class="" id="tableDiv_tracking_gvt_header1">
                                                                        
                                                                    </tr> -->
                                                                    </thead>
                                                                    <tbody id="cont_traking_gvt" style="font-size:10px;">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="order_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="class_wise_order_report_amt" /> Class Wise Order Report (amount)
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="class_wise_order_report_memo"/> Class Wise Order Report (memo)
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
                                                               value="sku_wise_order_delivery"/> SKU Wise Order Vs Delivery
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="zone_wise_order_delivery_summary"/> Zone Wise Order Vs Delivery
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="note_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="note_report" checked/> Note Report
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="deviation_report" class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn">

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
                                                               value="group_wise_route_outlet" onClick="getDeviationData(this.value)"/> Abnormal Outlet with Routes
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="outlet_visit" onClick="getDeviationData(this.value)"/> Outlet Visit %
                                                    </label>
                                                </div>

                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="note_task" onClick="getDeviationData(this.value)"/> Work Note 
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="sr_movement" onClick="getDeviationData(this.value)"/> SR Movement(Yesterday) 
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
                                                <div class="btnDiv col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                                                   <br><br>
                                                </div>
                                                <div class="clearfix"></div>
                                        </div>
                                                <br><br>

                                    <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Company<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="acmp_id" id="acmp_id"
                                                        onchange="getGroup(this.value,1)">
                                                    <option value="">Select Company</option>
                                                    @foreach($acmp as $acmpList)
                                                        <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                            - {{$acmpList->acmp_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sales_group_id">Group<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="sales_group_id" id="sales_group_id"
                                                        onchange="">

                                                    <option value="">Select Group</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6 gvt">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                        for="dist_id">District<span
                                                            class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="dist_id" id="dist_id"
                                                        onchange="getThanaBelogToDistrict(this.value,1)">
                                                    <option value="">Select District</option>
                                                    @foreach($dsct1 as $d)
                                                        <option value="{{$d->id}}">{{$d->dsct_name}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 gvt">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="than_id">Thana<span
                                                                class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="than_id" id="than_id"
                                                        onchange="getWardNameBelogToThana(this.value,1)">
                                                    <option value="">Select Thana</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6 zone_div">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="zone_id">Zone<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="zone_id" id="zone_id">
                                                    <option value="">Select Zone</option>
                                                    @foreach($zone as $zone)
                                                        <option value="{{$zone->id}}">{{$zone->zone_code}}
                                                            - {{$zone->zone_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 start_date_div" style="display:none;">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date">Date<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <input type="text" class="form-control in_tg start_date" name="start_date"
                                                    id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                    autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 start_date_period_div">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date_period">Select Period<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <!-- <input type="text" class="form-control in_tg start_date" name="start_date_period"
                                                    id="start_date_period"
                                                    autocomplete="off"/> -->
                                                <select class="form-control cmn_select2" name="start_date_period" id="start_date_period" onchange="showCustomDate(this.value,1)">
                                                    <!-- <option value="0">Today</option>
                                                    <option value="1">Yesterday</option>
                                                    <option value="2">Last 3 days</option>
                                                    <option value="3">Last 7 days</option>
                                                    <option value="5">As Of</option>
                                                    <option value="4">Last 30 days</option>
                                                    <option value="">Custom Date?</option> -->
                                                    
                                                </select>
                                                    
                                            </div>
                                            
                                        </div>
                                       
                                        <div class="form-group col-md-6">
                                            
                                            <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-8-offset-4">
                                                <button id="send" type="button"
                                                        class="btn btn-success  btn-block in_tg"
                                                        onclick="getSummaryReport()">Show
                                                </button>
                                            </div>
                                        </div>
                                            
                                    </div>




                                    <!-- start history -->
                                    <div id="history" class="form-row animate__animated animate__zoomIn">
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Company<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="acmp_id_h" id="acmp_id_h"
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
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sales_group_id">Group<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="sales_group_id_h" id="sales_group_id_h"
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
                                                <select class="form-control cmn_select2" name="dist_id_h" id="dist_id_h"
                                                        onchange="getThanaBelogToDistrict(this.value,2)">
                                                    <option value="">Select District</option>
                                                    @foreach($dsct1 as $d)
                                                        <option value="{{$d->id}}">{{$d->dsct_name}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="than_id">Thana<span
                                                                class="required"></span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="than_id_h" id="than_id_h"
                                                        onchange="getSR(this.value,2)">
                                                    <option value="">Select Thana</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 start_date_div_h" style="display:none;">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date_h">Date<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <input type="text" class="form-control in_tg start_date" name="start_date_h"
                                                    id="start_date_h" value="<?php echo date('Y-m-d'); ?>"
                                                    autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 start_date_period_div_h">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date_period">Select Period<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <!-- <input type="text" class="form-control in_tg start_date" name="start_date_period"
                                                    id="start_date_period"
                                                    autocomplete="off"/> -->
                                                <select class="form-control cmn_select2" name="start_date_period" id="start_date_period_h" onchange="showCustomDate(this.value,2)">
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
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sr_id_h">Select SR<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" id="sr_id_h">
                                                    
                                                </select>
                                                    
                                            </div>
                                            
                                        </div>
                                       
                                        <div class="form-group col-md-6">
                                            
                                            <div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-4 col-sm-8-offset-4">
                                                <button id="send" type="button"
                                                        class="btn btn-success  btn-block in_tg"
                                                        onclick="getHReport()">Show
                                                </button>
                                            </div>
                                        </div>
                                            
                                    </div>


                                    <!-- end history -->
                                        
                                            
                                </div>
                                
                                    
                                </div>
                                <hr/>


                            </form>
                        </div>

        </div>
                    {{--outlet with class --}}
                    <div id="tableDiv">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           style="overflow-x: auto;"
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                            
                                            <th>Date</th>
                                            <th>Zone Name</th>
                                            <th>Total SR</th>
                                            <th>Present SR</th>
                                            <th>Attendance %</th>
                                            <th>Leave / IOM SR</th>
                                            <th>Absent SR</th>
                                            <th>Productive SR</th>
                                            <th>Non Productive SR</th>
                                            <th>Total Outlet</th>
                                            <th>Total Outlet Covered</th>
                                            <th>Outlet Covered %</th>
                                            <th>Total Successful Calls</th>
                                            <th>Strike Rate %</th>
                                            <th>Line Per Call (LPC)</th>
                                            <th>Average SR Contribution in (K)</th>
                                            <th>Outlet Per SR</th>
                                            <th>Visited Outlet Per SR</th>
                                            <th>Total Amount in (K)</th>
                                        </tr>

                                        </thead>
                                        <tbody id="cont">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--outlet with item --}}
                    <div id="tableDiv_sr_productivity">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_productivity')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                            <th>Date</th>
                                            <th>Zone Name</th>
                                            {{--<th>Base Name</th>--}}
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Route Name</th>
                                            <th>Total Outlet</th>
                                            <th>Outlet Covered</th>

                                            <th>Covered %</th>
                                            <th>Successful Outlet</th>
                                            <th>Strike Rate %</th>
                                            <th>Non Productive Outlet</th>
                                            <th>Total Order Amount (K)</th>
                                            <th>AVG Outlet Contribution (K)</th>
                                            <th>LPC</th>
                                            <th>In Time</th>
                                            <th>First Order Time</th>
                                            <th>Last Order Time</th>
                                            <th>Work Time</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_sr_productivity">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                   

                    {{--without outlet with class --}}
                    <div id="tableDiv_sr_non_productivity">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_non_productivity')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>In Time</th>
                                        </tr>
                                        </tr>
                                        </thead>
                                        <tbody id="cont_sr_non_productivity">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--outlet with class --}}
                    <div id="tableDiv_sr_summary_by_group">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_summary_by_group')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           style="overflow-x: auto;"
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Total SR</th>
                                            <th>Present SR</th>
                                            <th>Attendance %</th>
                                            <th>Leave / IOM SR</th>
                                            <th>Absent SR</th>
                                            <th>Productive SR</th>
                                            <th>Non Productive SR</th>
                                            <th>Total Outlet</th>
                                            <th>Total Outlet Covered</th>
                                            <th>Outlet Covered %</th>
                                            <th>Total Successful Calls</th>
                                            <th>Strike Rate %</th>
                                            <th>Line Per Call (LPC)</th>
                                            <th>Average SR Contribution in (K)</th>
                                            <th>Outlet Per SR</th>
                                            <th>Visited Outlet Per SR</th>
                                            <th>Total Amount in (K)</th>
                                        </tr>

                                        </thead>
                                        <tbody id="cont_sr_summary_by_group">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--without outlet with item--}}
                    <div id="tableDiv_sr_activity_hourly_order">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow:auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_activity_hourly_order<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_activity_hourly_order')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                            <tr class="tbl_header">
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
                    <div id="tableDiv_sr_activity_hourly_visit">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_activity_hourly_visit<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_activity_hourly_visit')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                       
                                        <tr class="tbl_header">
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

                    {{--sr with class--}}
                    <div id="tableDivd_market_outlet_sr_outlet">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('market_outlet_vs_sr_outlet_<?php echo date('Y_m_d'); ?>.csv','tableDivd_market_outlet_sr_outlet')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
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

                    {{--sr with item--}}
                    <div id="tableDiv_sr_wise_order_delivery">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_wise_order_delivery')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
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
                                            <th>Order Amount</th>
                                            <th>Delivery Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_sr_wise_order_delivery">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--sr with item--}}
                    <!-- zone wise order delivery summary -->
                    <div id="tableDiv_zone_wise_order_delivery_summary">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('zone_wise_order_delivery_summary<?php echo date('Y_m_d'); ?>.csv','tableDiv_zone_wise_order_delivery_summary')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                        {{--` t1.ordm_date, t1.`acmp_name`, t1.`slgp_name`, t1.`dirg_name`, t1.`zone_name`, sum(t1.`ordd_oamt`) as ordd_oamt,
                                                 sum(t1.`ordd_odat`) as ordd_Amnt `--}}
                                        <tr class="">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
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
                    <div id="tableDiv_sku_wise_order_delivery">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sku_wise_delivery<?php echo date('Y_m_d'); ?>.csv','tableDiv_sku_wise_order_delivery')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-striped table-bordered "
                                           >
                                        
                                        <thead>
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
                    <div id="tableDiv_class_wise_order_summary">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('class_wise_order_summary_<?php echo date('Y_m_d'); ?>.csv','tableDiv_class_wise_order_summary')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                           <tr id="class_wise_order_summary_amount_headings">

                                           </tr>

                                        <tbody id="cont_class_wise_order_summary">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--class wise order summary memo--}}
                    <div id="tableDiv_class_wise_order_summary_memo">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('class_wise_order_summary_<?php echo date('Y_m_d'); ?>.csv','tableDiv_class_wise_order_summary_memo')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                           <tr id="class_wise_order_summary_memo_headings">

                                            </tr>
                                        <tbody id="cont_class_wise_order_summary_memo">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tableDiv_sr_route_outlet">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_route_outlet<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_route_outlet')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
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


                    <div id="tableDiv_group_wise_route_outlet">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('group_wise_route_outlet<?php echo date('Y_m_d'); ?>.csv','tableDiv_group_wise_route_outlet')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
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

                    <div id="tableDiv_sr_history">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_history<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_history')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100' >
                                        <thead id="head_history" style="position:sticky; inset-block-start:0;" class="tbl_header">
                                                
                                        </thead>
                                        <tbody id="cont_history">

                                        </tbody>
                                        <tfoot id="foot_history" style="position:sticky;inset-block-end:0; background-color:gray;color:white;">
    
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- modal started for visited outlet category -->
                    <!-- Note Report  Start-->
                    <div id="tableDiv_note_report">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_route_outlet<?php echo date('Y_m_d'); ?>.csv','tableDiv_note_report')"
                                                class="btn btn-warning" id="exp_btn">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead id="head_note_report">
                                            
                                        </thead>
                                        <tbody id="cont_note_report">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Note Report  End-->
                </div>
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
          <h4 class="modal-title text-center">Category wise Visit</h4>
        </div>
        <div class="modal-body">
        <div class="loader" id="cat_out_load" style="display:none; margin-left:35%;"></div>
        <table class="table table-striped" >
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
<!-- Visited outlet details -->
<div class="modal fade" id="myModalVisitedOutlet" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Visited Outlet Details</h4>
        </div>
        <div class="modal-body">
        <div class="loader" id="cat_out_load_details" style="display:none; margin-left:35%;"></div>
        <table class="table table-striped" >
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
<div class="modal fade" id="myModalWardWiseVisit" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Market Wise Visit</h4>
        </div>
        <div class="modal-body">
        <div class="loader" id="load_ward_visit" style="display:none; margin-left:35%;"></div>
        <table class="table table-striped" >
          <thead>
            <tr>
              <th>Sl</th>
              <th>Market</th>
              <th>Visit</th>
              <th>View</th>
            </tr>
          </thead>
          <tbody id="myModalWardWiseVisitBody1">

          </tbody>
        </table>
        <h4 class="text-center">Ward Wise Visit</h4>
        <table class="table table-striped" >
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
        <table class="table table-striped" >
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
<script type="text/javascript">
    $('.modal-backdrop').remove()
    $(document.body).removeClass("modal-open");
        $('.cmn_select2').select2();
        $SIDEBAR_MENU = $('#sidebar-menu')
        $(document).ready(function(){
				setTimeout(function() { 
			        $('#menu_toggle').click();
			    }, 1);
		});
    $('#rpt').height($("#hierarchy").height());
    function hide_me() {
        $('#tableDiv').hide();
        $('#tableDiv_sr_productivity').hide();
        $('#tableDiv_sr_history').hide();
        $('#tableDiv_sr_non_productivity').hide();
        $('#tableDiv_sr_summary_by_group').hide();
        $('#tableDiv_sr_activity_hourly_order').hide();
        $('#tableDiv_sr_activity_hourly_visit').hide();
        $('#tableDivd_market_outlet_sr_outlet').hide();
        $('#tableDiv_sr_wise_order_delivery').hide();
        $('#tableDiv_sku_wise_order_delivery').hide();
        $('#tableDiv_class_wise_order_summary').hide();
        $('#tableDiv_class_wise_order_summary_memo').hide();
        $('#tableDiv_zone_wise_order_delivery_summary').hide();
        $('#tableDiv_traking').hide();
        $('#item_wise_hierarchy').hide();
        $('#tableDiv_traking_gvt').hide();
        $('#tableDiv_sr_route_outlet').hide();
        $('#tableDiv_group_wise_route_outlet').hide();
        $('#tableDiv_note_report').hide();
        $('#emp_task_note_report').hide();
        $('#sr_movement_summary').hide();
        $('#dev_note_date').hide();
        
        $('#period').show();
        $('#employee_sales_traking_report_slgp').show();
        
    }
    function hideReport(){
        $('#govt_heirarchy').hide();
        $('#sales_heirarchy').hide();
        $('#history').hide();
        $('.ord_asset').hide();
        $('#sr_report').hide();
        $('#outlet_report').hide();
        $('#order_report').hide();
        $('#history_report').hide();
        $('#note_report').hide();
        $('#deviation_report').hide();
        $('#order_report').hide();
        $('.tracing').hide();
        $('#title_head').hide();
        $('#harch').show();
        $('.gvt').hide();
        $('#emp_tracking_report').hide();
        $('.start_date_div').hide();
        $('.start_date_period_div').show();
        $("input[name='reportType']").attr('checked',false)
    }
    function addClass(){
        $('#rp').removeClass('col-md-6 col-sm-6').addClass('col-md-12 col-sm-12')
        $('#harch').hide();
    }
    function removeClass(){
        $('#rp').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6')
        $('#harch').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6')
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
        $('.start_date_div').show();
        $('.start_date_period_div').hide();
        $('.zone_div').hide();
    }

    function getOrderReport() {
        hideReport();
        hide_me();
        removeClass();
        $('#order_report').show();
        $('#sales_heirarchy').show();
        $('.zone_div').show();
        // $('.ord_asset').show();   
    }
    function getHistoryReport() {
        hideReport();
        hide_me();
        removeClass();
        $('.start_date_div_h').hide();
        $('.start_date_period_div_h').show();
        $('#history').show();
        $('#history_report').show(); 
    }
    function getNoteReport() {
        hideReport();
        hide_me();
        removeClass();
        $('#note_report').show();
        $('#sales_heirarchy').show();
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
    function getEmpTrackingReport(){
        //$("input[name='emp_tracking_reportType']").attr('checked', false);
        hideReport();
        hide_me();
        $('#harch').hide();
        $('#emp_tracking_report').show();
    }
    //Report Type Selection Div End
    // custom Date field 
    function showCustomDate(val,place){
       // console.log("hello selection")
        if(val==''){
            if(place==1){
                $('.start_date_div').show();
                $('.start_date_period_div').hide();
            }else{
                $('.start_date_div_h').show();
                $('.start_date_period_div_h').hide();
            }
            
        }
       
    }
    //Helper function (Load dependent data based on filter) start from here
    function getGroup(slgp_id,place) {
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
                var html = '<option value="">Select</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                }
                if(place==1){
                    $("#sales_group_id").empty(); 
                    $("#sales_group_id").append(html);
                }else{
                    $("#sales_group_id_h").empty(); 
                    $("#sales_group_id_h").append(html); 
                }
            }
        });
    }

    function getZone(dirg_id) {
        clearDate();
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{ URL::to('/')}}/load/report/getZone",
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
                var html = '<option value="">Select Zone</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].zone_code + " - " + data[i].zone_name + '</option>';
                }
                $("#zone_id").append(html);
                $("#zone_id1").append(html);
            }
        });
    }
    function getSR(id,type) {
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{ URL::to('/')}}/report/getSRList",
            data: {
                id: id,
                type: type,
                _token: _token
            },
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#sr_id_h").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select SR</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                }
                $("#sr_id_h").append(html);
            }
        });
    }
    function loadOutlet(market_id,place) {
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
                if(place==1){
                    $("#outlet_id").empty();
                }else{
                    $("#outlet_id1").empty();
                }
                $('#outlet_id').empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select</option>';
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id + '">' + data[i].site_code + " - " + data[i].site_name + '</option>';
                }
                if(place==1){
                    $("#outlet_id").append(html);
                }else{
                    $("#outlet_id1").append(html);
                }

            }
        });

    }
    function getThanaBelogToDistrict(dist_id,place) {
       // clearDate();
        //var district_id = $('#dist_id').val();
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
                if(place==1){
                    $("#than_id").empty();
                }else if(place==2){
                    $('#than_id_h').empty();
                }
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id + '">' + data[i].than_name + '</option>';
                }
                if(place==1){
                    $("#than_id").append(html);
                }else if(place==2){
                    $("#than_id_h").append(html);
                   
                }
            }
        });
    }
    function getWardNameBelogToThana(thana_id,place) {
       
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
                if(place==1){
                    $("#ward_id").empty();
                }else if(place==2){
                    html+='<option value="all" selected>All</option>';
                    $("#ward_id1").empty();
                }
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].ward_name + '</option>';
                }
                if(place==1){
                    $("#ward_id").append(html);
                }else{
                    $("#ward_id1").append(html);
                }         
            }
        });
    }
    function loadWardMarket(ward_id,place) {
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
                if(place==1){
                    $("#market_id").empty();
                }else if(place==2){
                    html+='<option value="all" selected>All</option>';
                    $("#mktm_id1").empty();
                }
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].market_name + '</option>';
                }
                if(place==1){
                    $("#market_id").append(html);
                }else{
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
    function getDiggingReport(emp_tracking_hierarchy) {
        hideReport();
        hide_me();
        addClass();
        if(emp_tracking_hierarchy=='emp_tracking_sales_hierarchy'){
            $('#title_head').show();
            $('#tracing').show();
            $('#desig').empty();
            $('#emid_div').empty();
            var emid=<?php echo $emid; ?>;
            var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
            $('#emid_div').append(emid_cnt);
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/getTrackingRecord",
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#tableDiv_traking').show();
                    $('#head_tracking_dev_note_task').empty();
                    var head='<th>Sl</th>'+
                            '<th>Name</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total SR</th>'+
                           ' <th>Outlet</th>'+
                           ' <th>Visited</th>'+
                            '<th>Non Visited</th>'+
                           ' <th>Order</th>'+
                            '<th>Exp</th>'+
                           ' <th>Exp-Tgt</th>';
                    $('#head_tracking_dev_note_task').append(head);
                    console.log(data);
                    var html="";
                    var count=1;
                    var dp_cnt='';
                    var out_color='';
                    var visit_color='';
                    var t_sr=1;
                    for (var i = 0; i < data.length; i++) {
                        if(data[i]['role_id']<6){
                            out_color=data[i]['outlet_color'];
                            visit_color=data[i]['visit_color'];
                        }
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td>' + data[i]['totalSr'] + '</td>' +
                            '<td style="color:'+out_color+'">' + data[i]['t_outlet'] + '</td>' +
                            '<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '</td>' +
                            '<td>' + (data[i]['t_outlet']-data[i]['total_visited']) + '</td>' +
                            '<td>' + data[i]['memo'] + '</td>' +                           
                            '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                            "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                            '</tr>';
                        dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
                    console.log(error);
                    $('#ajax_load').css("display", "none");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    })
                }
            });
        }else if(emp_tracking_hierarchy=='emp_tracking_gvt_hierarchy'){
          $('#tracing_gvt').show();
          $('#deviation_date_div').hide();
          $('#ajax_load').css("display", "block");
          $('#export_csv_tracking_gvt').removeAttr('onclick');
          $('#export_csv_tracking_gvt').attr('onclick','exportTableToCSV("employee_sales_traking_report_gvt_<?php echo date('Y_m_d'); ?>.csv","tableDiv_traking_gvt")');
          $.ajax({
            type:"get",
            url:"{{URL::to('/')}}/getGvtTrackingRecord",
            success:function(data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var head1='<tr><th>District Name</th>'+
                    '<th>Total Outlet</th>';
                var sub_head='<tr style="font-size:10px;">'+
                        '<th></th>'+
                        '<th></th>';
                var html="";
                var visit='';
                var memo='';
                var out='';
                var dp_cnt='';
                for(var i=0;i<data.slgp.length;i++){
                    head1+='<th colspan="2">'+data.slgp[i]['slgp_name']+'</th>';
                    sub_head+='<th>Visit</th>'+'<th>Memo</th>'; 
                }
                head1+='</tr>';
                sub_head+='</tr>';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                for(var i=0;i<data.data.length;i++){
                    html+='<tr>'+
                            '<td>'+data.data[i]['dsct_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                    dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['dsct_id']+'" stage="1">'+' '+data.data[i]['dsct_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                    for(var j=0;j<data.slgp.length;j++){
                        visit='slgpv'+j;
                        memo='slgpm'+j;
                        html+='<td>'+data.data[i][visit]+'</td>'+
                            '<td>'+data.data[i][memo]+'</td>';
                   
                 }
                 html+='</tr>';
                    
                }
                $('#gvt_hierarchy').empty();
               $('#tableDiv_tracking_gvt_header1').empty();
               $('#tableDiv_tracking_gvt_header1').append(head1);
               $('#tableDiv_tracking_gvt_header1').append(sub_head);
               $('#cont_traking_gvt').empty();
               $('#cont_traking_gvt').append(html);
               $('#all_dp_content1').empty();
               $('#all_dp_content1').append(dp_cnt);
               $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
            
            },
            error:function(error){
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
    /* //Deviation_Start***************/
    // Group wise Route_Outlet start
    /* //Deviation_End***************/
    //initial phase of tracking report based on both hierarchy end
    function testClick(v){
        var emid=$(v).attr('emid');
        var role_name=$(v).attr('role_name');
        v.removeAttribute('onclick');
        v.setAttribute('onclick','desigEmployeeAddRemove(this)');
        console.log(v);
        var html=role_name+" >  ";
        var period=$('#period').val();
        var _token = $("#_token").val();
        $('#desig').append(v);
        $('#emid_div').empty();
        var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
        $('#emid_div').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getUserWiseReport",
            data:{
                _token:_token,
                emid:emid,
                period:period
            },
            dataType: "json",
            success: function (data){
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking').show();
                console.log(data);
                var html="";
                var count=1;
                var dp_cnt='';
                var out_color='';
                var visit_color='';
                var t_sr=1;
                for (var i = 0; i < data.length; i++) {
                    if(data[i]['role_id']<6){
                        out_color=data[i]['outlet_color'];
                        visit_color=data[i]['visit_color'];
                    }
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td>' + data[i]['totalSr'] + '</td>' +
                        '<td style="color:'+out_color+'">' + data[i]['t_outlet'] + '</td>';
                    if(data[i]['role_id']==2){
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '<i id="show" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet('+data[i]['oid']+')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                    }else{
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '</td>';
                    }
                       html+= '<td>' + (data[i]['t_outlet']-data[i]['total_visited']) + '</td>' +
                        '<td>' + data[i]['memo'] + '</td>' +                           
                        '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                        "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                        '</tr>';
                    dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp;'+'</a></li>';
                    count++;
                }
                $('#cont_traking').empty();
                $('#all_dp_content').empty();
                $('#cont_traking').append(html);
                $('#all_dp_content').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height()+150);
            },error:function(error){
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
    function desigEmployeeAddRemove(elem){
        $(elem).nextAll().remove();
        var emid=$(elem).attr('emid');
        $('#emid_div').empty();
        var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
        $('#emid_div').append(emid_cnt);
        var emid=$(elem).attr('emid');
        var _token = $("#_token").val();
        var period=$('#period').val();
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getUserWiseReport",
            data:{
                _token:_token,
                emid:emid,
                period:period
            },
            dataType: "json",
            success: function (data){
                $('#tableDiv_traking').show();
                console.log(data);
                var html="";
                var count=1;
                var dp_cnt='';
                var out_color='';
                var visit_color='';
                var t_sr=1;
                for (var i = 0; i < data.length; i++) {
                    if(data[i]['role_id']<6){
                        out_color=data[i]['outlet_color'];
                        visit_color=data[i]['visit_color'];
                    }
                    if(data[i]['role_id']<6){
                        out_color=data[i]['outlet_color'];
                        visit_color=data[i]['visit_color'];
                    }
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td>' + data[i]['totalSr'] + '</td>' +
                        '<td style="color:'+out_color+'">' + data[i]['t_outlet'] + '</td>';
                    if(data[i]['role_id']==2){
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '<i id="show" data-toggle="modal" data-target="#myModalVisit" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet('+data[i]['oid']+')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                    }else{
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '</td>';
                    }
                    html+= '<td>' + (data[i]['t_outlet']-data[i]['total_visited']) + '</td>' +
                    '<td>' + data[i]['memo'] + '</td>' +                           
                    '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                    "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                    '</tr>';
                    dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp;'+'</a></li>';
                    count++;
                }
                $('#cont_traking').empty();
                $('#all_dp_content').empty();
                $('#cont_traking').append(html);
                $('#all_dp_content').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height()+150);
            },error:function(error){
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
    function getDateWiseUserReport(date,rpt){
        var emid=$('#emid').val();
        var period=$('#period').val();
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        if(rpt==1){
            $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getUserWiseReport",
            data:{
                _token:_token,
                emid:emid,
                period:date
            },
            dataType: "json",
            success: function (data){
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking').show();
                $('#head_tracking_dev_note_task').empty();
                var head='<th>Sl</th>'+
                        '<th>Name</th>'+
                        '<th>Mobile</th>'+
                        '<th>Total SR</th>'+
                        ' <th>Outlet</th>'+
                        ' <th>Visited</th>'+
                        '<th>Non Visited</th>'+
                        ' <th>Order</th>'+
                        '<th>Exp</th>'+
                        ' <th>Exp-Tgt</th>';
                $('#head_tracking_dev_note_task').append(head);
                console.log(data);
                var html="";
                var count=1;
                var dp_cnt='';
                var out_color='';
                var visit_color='';
                var t_sr=1;
                for (var i = 0; i < data.length; i++) {
                    if(data[i]['role_id']<6){
                        out_color=data[i]['outlet_color'];
                        visit_color=data[i]['visit_color'];
                    }
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td>' + data[i]['totalSr'] + '</td>' +
                        '<td style="color:'+out_color+'">' + data[i]['t_outlet'] + '</td>';
                    if(data[i]['role_id']==2){
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '<i id="show" data-toggle="modal" data-target="#myModalVisit" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet('+data[i]['oid']+')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                    }else{
                        html +='<td style="color:'+visit_color+'">' + data[i]['total_visited'] + '</td>';
                    }
                    html+= '<td>' + (data[i]['t_outlet']-data[i]['total_visited']) + '</td>' +
                    '<td>' + data[i]['memo'] + '</td>' +                           
                    '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                    "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                    '</tr>';
                    dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp;'+'</a></li>';
                    count++;
                }
                $('#cont_traking').empty();
                $('#all_dp_content').empty();
                $('#cont_traking').append(html);
                $('#all_dp_content').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height()+150);
            },error:function(error){
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
    //initial state of tracking report based on Govt Hierarchy
    function getGvtDeeperData(v){
        $('#deviation_date_div').hide();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        v.removeAttribute('onclick');
        v.setAttribute('onclick','appendTopTitle(this)');
        var _token = $("#_token").val();
        $('#gvt_hierarchy').append(v);
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'">';
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getGvtDeeperSalesData",
            data:{
                _token:_token,
                id:id,
                stage:stage
            },
            dataType: "json",
            success: function (data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html="";
                var visit='';
                var memo='';
                var out='';
                var dp_cnt='';
                var sub_head='<tr style="font-size:10px;">'+
                        '<th></th>'+
                        '<th></th>';
                var head1='';
                
                head1+='</tr>';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    head1+='<tr><th>Thana Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['than_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['than_id']+'" stage="2">'+' '+data.data[i]['than_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }     
                }
                else if(stage==2){
                    head1+='<tr><th>Ward Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['ward_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['ward_id']+'" stage="3">'+' '+data.data[i]['ward_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }   
                }
                else if(stage==3){
                    head1+='<tr><th>Market Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['mktm_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['mktm_id']+'" stage="4">'+' '+data.data[i]['mktm_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }  
                }
                else if(stage==4){
                    html="<h4>No Data </h4>"; 
                    head1='';
                    sub_head='';
                }
                if(stage !=4){
                    for(var i=0;i<data.slgp.length;i++){
                    head1+='<th colspan="3">'+data.slgp[i]['slgp_name']+'</th>';
                    sub_head+='<th>Outlet</th>'+'<th>Visit</th>'+'<th>Memo</th>'; 
                    }
                    head1+='</tr>';
                    sub_head+='</tr>';
                }
                
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
               
            },error:function(error){
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
    function appendTopTitle(v){
        $('#deviation_date_div').hide();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        $(v).nextAll().remove();
        var _token = $("#_token").val();
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'">';
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getGvtDeeperSalesData",
            data:{
                _token:_token,
                id:id,
                stage:stage
            },
            dataType: "json",
            success: function (data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html="";
                var visit='';
                var memo='';
                var out='';
                var dp_cnt='';
                var sub_head='<tr style="font-size:10px;">'+
                        '<th></th>'+
                        '<th></th>';
                var head1='';
                
                head1+='</tr>';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    head1+='<tr><th>Thana Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['than_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['than_id']+'" stage="2">'+' '+data.data[i]['than_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }     
                }
                else if(stage==2){
                    head1+='<tr><th>Ward Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['ward_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['ward_id']+'" stage="3">'+' '+data.data[i]['ward_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }   
                }
                else if(stage==3){
                    head1+='<tr><th>Market Name</th>'+
                            '<th>Total Outlet</th>';
                    for(var i=0;i<data.data.length;i++){
                        html+='<tr>'+
                            '<td>'+data.data[i]['mktm_name']+'</td>'+
                            '<td>'+data.data[i]['TOTL']+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getGvtDeeperData(this)" id="'+data.data[i]['mktm_id']+'" stage="4">'+' '+data.data[i]['mktm_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        for(var j=0;j<data.slgp.length;j++){
                            visit='slgpv'+j;
                            memo='slgpm'+j;
                            out='slgpo'+j;
                            html+='<td>'+data.data[i][out]+'</td>'+
                                '<td>'+data.data[i][visit]+'</td>'+
                                '<td>'+data.data[i][memo]+'</td>';
                        }
                        html+='</tr>';
                        
                    }  
                }
                else if(stage==4){
                    html="<h4>No Data </h4>"; 
                    head1='';
                    sub_head='';
                }
                if(stage !=4){
                    for(var i=0;i<data.slgp.length;i++){
                    head1+='<th colspan="3">'+data.slgp[i]['slgp_name']+'</th>';
                    sub_head+='<th>Outlet</th>'+'<th>Visit</th>'+'<th>Memo</th>'; 
                    }
                    head1+='</tr>';
                    sub_head+='</tr>';
                }
                
                
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1+sub_head);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
               
            },error:function(error){
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
   
   
    /****************************Employee Tracking Report(Based on Sales Hierarchy) Function End*/
    

    $("input[name='reportType']").on("change", function () {
            // $('#outletType').hide();
            // $('#srType').hide();

            // var reportType = this.value;
            // //alert(reportType);
            // if (reportType == "sr_activity_hourly_order") {
            // //.attr('disabled','disabled');
            //     $('#outletType').show();
            //     $('#end_date').attr('disabled','disabled');
            //     $('#outletAssetType').show();
            //     $('#sales_heirarchy').show();
            //     $('#govt_heirarchy').hide();
            // }else if ((reportType == "sr_activity_hourly_visit")){

            //     $('#end_date').attr('disabled','disabled');
            // }else{
            //     $('#end_date').removeAttr('disabled');
            // }

            // if (reportType == "d_outlet") {
            //     $('#srType').show();
            //     $('#outletAssetType').show();

            //     $('#govt_heirarchy').show();
            //     $('#sales_heirarchy').hide();
            // }
            // if (reportType == "sr_activity_hourly_order"||reportType == "sr_activity_hourly_visit") {
            //     $('#start_date_period').hide();
            //     $('#start_date').show();
            // }
        });

        // $("input[name='outletTypev']").on("change", function () {
        //     $('#outletAssetType').hide();
        //     var outletType = this.value;

        //     if (outletType == "olt") {
        //         $('#outletAssetType').show();
        //     }

        // });

function validateInputField(reportType,acmp_id,sales_group_id,start_date_period,...all){
    
    if(acmp_id==''){
        alert ("Please Select Company");
        return false;
    }
    if(sales_group_id==''){
        alert("Please Select Group");
        return false;
    }
    //console.log(all[0]);
    if(reportType=='market_outlet_sr_outlet'){
        if(all[0]==''){
            alert("Please select District");
            return false;
        }
        if(all[1]==''){
            alert("Please select Thana");
            return false;
        }
    }
    else{
        return true;
    }
}
function getSummaryReport() {
        hide_me();
        var reportType = $("input[name='reportType']:checked").val();
        var acmp_id = $('#acmp_id').val();
        var sales_group_id = $('#sales_group_id').val();
        var zone_id = $('#zone_id').val();
        var time_period =$('#start_date_period').val()
        var dist_id = $('#dist_id').val();
        var than_id = $('#than_id').val();
        var _token = $("#_token").val();
        var validityCheck=false;
        if(time_period==''){
            time_period =$('#start_date').val()
        }
        if(reportType===undefined){
            alert ('Please select report');
            return false;
        }
        else if(reportType==''){
            alert('Please select report');
            return false;
        }
        if(reportType=='market_outlet_sr_outlet'){
            time_period=$('#start_date').val();
            validityCheck=validateInputField(reportType,acmp_id,sales_group_id,start_date_period,dist_id,than_id);
        }
        else{
            validityCheck=validateInputField(reportType,acmp_id,sales_group_id,start_date_period);
        }
        if(validityCheck !=false){
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{ URL::to('/')}}/load/filter/common_sr_activity_filter/demo2",
            data: {
                reportType: reportType,
                acmp_id: acmp_id,
                zone_id: zone_id,
                sales_group_id: sales_group_id,
                time_period: time_period,
                dist_id: dist_id,
                than_id: than_id,
                _token: _token
            },
            cache: false,
            dataType: "json",
            success: function (data) {
                $('#ajax_load').css("display", "none");
                console.log(data);
                var html = '';
                var count = 1;
                if (reportType == "sr_activity") {
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['date'] + '</td>' +
                            '<td>' + data[i]['zone_name'] + '</td>' +
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
                            "<td>" + (data[i]['c_outlet'] / (data[i]['pro_sr'])).toFixed(1) + "</td>" +
                            "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                            '</tr>';
                    }
                    $("#cont").empty();
                    $("#cont").append(html);
                    $('#tableDiv').show();
                }else if (reportType == "sr_productivity") {
                    for (var i = 0; i < data.length; i++) {


                        html += '<tr>' +
                            '<td>' + data[i]['date'] + '</td>' +
                            '<td>' + data[i]['zone_name'] + '</td>' +
                            '<td>' + data[i]['aemp_id'] + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_mobile'] + '</td>' +
                            '<td>' + data[i]['rout_name'] + '</td>' +
                            '<td>' + data[i]['t_outlet'] + '</td>' +
                            '<td>' + data[i]['c_outlet'] + '</td>' +
                            '<td>' + (((data[i]['c_outlet']) * 100) / data[i]['t_outlet']).toFixed(2) + '</td>' +
                            '<td>' + data[i]['s_outet'] + "</td>" +
                            '<td>' + (data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2) + '</td>' +
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
                    $("#cont_sr_productivity").empty();
                    $("#cont_sr_productivity").append(html);
                    $('#tableDiv_sr_productivity').show();

                }else if (reportType=="sr_summary_by_group"){
                    //alert("non sr_summary_by_group");
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
                    $("#cont_sr_summary_by_group").empty();
                    $("#cont_sr_summary_by_group").append(html);
                    $('#tableDiv_sr_summary_by_group').show();
                }else if(reportType=="sr_non_productivity"){
                    //alert("non productive");
                    for (var i = 0; i < data.length; i++) {

                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['date'] + '</td>' +
                            '<td>' + data[i]['slgp_name'] + '</td>' +
                            '<td>' + data[i]['dirg_name'] + '</td>' +
                            '<td>' + data[i]['zone_name'] + '</td>' +
                            '<td>' + data[i]['aemp_id'] + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_mobile'] + '</td>' +

                            '<td>' + (data[i]['inTime']) + '</td>' +

                            '</tr>';
                        count++;
                    }
                    $("#cont_sr_non_productivity").empty();
                    $("#cont_sr_non_productivity").append(html);
                    $('#tableDiv_sr_non_productivity').show();
                }else if (reportType == "sr_activity_hourly_order"){
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                            '<td>'+data[i]['act_date']+'</td>'+
                            '<td>'+data[i]['slgp_name']+'</td>'+
                            '<td>'+data[i]['zone_name']+'</td>'+
                            '<td>'+data[i]['aemp_usnm']+'</td>'+
                            '<td>'+data[i]['aemp_name']+'</td>'+
                            '<td>'+data[i]['aemp_mob1']+'</td>'+
                            '<td>'+data[i]['9am']+'</td>'+
                            '<td>'+data[i]['10am']+'</td>'+
                            '<td>'+data[i]['11am']+'</td>'+
                            '<td>'+data[i]['12pm']+'</td>'+
                            '<td>'+data[i]['1pm']+'</td>'+
                            '<td>'+data[i]['2pm']+'</td>'+
                            '<td>'+data[i]['3pm']+'</td>'+
                            '<td>'+data[i]['4pm']+'</td>'+
                            '<td>'+data[i]['5pm']+'</td>'+
                            '<td>'+data[i]['6pm']+'</td>'+
                            '<td>'+data[i]['7pm']+'</td>'+
                            '<td>'+data[i]['8pm']+'</td>'+
                            '<td>'+data[i]['9pm']+'</td>';
                    }

                    $("#cont_sr_activity_hourly_order").empty();
                    $("#cont_sr_activity_hourly_order").append(html);
                    $('#tableDiv_sr_activity_hourly_order').show();
                }else if (reportType == "sr_activity_hourly_visit"){
                   // console.log(data);
                   
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                            '<td>'+data[i]['act_date']+'</td>'+
                            '<td>'+data[i]['slgp_name']+'</td>'+
                            '<td>'+data[i]['zone_name']+'</td>'+
                            '<td>'+data[i]['aemp_usnm']+'</td>'+
                            '<td>'+data[i]['aemp_name']+'</td>'+
                            '<td>'+data[i]['aemp_mob1']+'</td>'+
                            '<td>'+data[i]['9am']+'</td>'+
                            '<td>'+data[i]['10am']+'</td>'+
                            '<td>'+data[i]['11am']+'</td>'+
                            '<td>'+data[i]['12pm']+'</td>'+
                            '<td>'+data[i]['1pm']+'</td>'+
                            '<td>'+data[i]['2pm']+'</td>'+
                            '<td>'+data[i]['3pm']+'</td>'+
                            '<td>'+data[i]['4pm']+'</td>'+
                            '<td>'+data[i]['5pm']+'</td>'+
                            '<td>'+data[i]['6pm']+'</td>'+
                            '<td>'+data[i]['7pm']+'</td>'+
                            '<td>'+data[i]['8pm']+'</td>'+
                            '<td>'+data[i]['9pm']+'</td>';
                    }
                    $("#cont_sr_activity_hourly_visit").empty();
                    $("#cont_sr_activity_hourly_visit").append(html);
                    $('#tableDiv_sr_activity_hourly_visit').show();

                }else if (reportType == "market_outlet_sr_outlet"){
                    //alert("fee");
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
                    //alert(html);
                    $("#contd_market_outlet_sr_outlet").empty();
                    $("#contd_market_outlet_sr_outlet").append(html);
                    $('#tableDivd_market_outlet_sr_outlet').show();
                }else if (reportType == "sr_wise_order_delivery"){
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

                    $("#cont_sr_wise_order_delivery").empty();
                    $("#cont_sr_wise_order_delivery").append(html);
                    $('#tableDiv_sr_wise_order_delivery').show();
                    // alert("imu")tableDiv_sr_wise_order_delivery
                }else if (reportType == "zone_wise_order_delivery_summary"){
                    for (var i = 0; i < data.length; i++) {

                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['ordm_date'] + '</td>' +
                            '<td>' + data[i]['slgp_name'] + '</td>' +
                            '<td>' + data[i]['dirg_name'] + '</td>' +
                            '<td>' + data[i]['zone_name'] + '</td>' +
                            '<td>' + (data[i]['ordd_oamt'].toFixed(2)) + '</td>' +
                            '<td>' + (data[i]['ordd_Amnt'].toFixed(2)) + '</td>' +

                            '</tr>';
                        count++;
                    }

                    $("#cont_zone_wise_order_delivery_summary").empty();
                    $("#cont_zone_wise_order_delivery_summary").append(html);
                    $('#tableDiv_zone_wise_order_delivery_summary').show();
                    // alert("imu")tableDiv_sr_wise_order_delivery
                }
                else if (reportType == "sku_wise_order_delivery"){
                    
                    $('#tableDiv_sku_wise_order_delivery').show();
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
                            '<td>' + data[i]['ordd_amt'] + '</td>' +
                            '<td>' + data[i]['deli_amt'] + '</td>' +
                            '</tr>';
                        count++;
                    }
                    $("#cont_sku_wise_order_delivery").empty();
                    $("#cont_sku_wise_order_delivery").append(html);
                    
                    
                    // alert("imu")tableDiv_sr_wise_order_delivery
                }else if (reportType == "class_wise_order_report_amt"){
                    var headings='<th>Sl</th>'+
                                  '<th>Date</th>'+
                                  '<th>Staff ID</th>'+
                                  '<th>Name</th>'+
                                  '<th>Mobile</th>'+
                                  '<th>Zone Code</th>'+
                                  '<th>Zone Name</th>';
                                  
                    var cl_name='';
                                  
                    count = 1;
                    for(var j=0;j<data.class_wise_ord_amnt.length;j++){
                        html+='<tr><td>'+count+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['ordm_date']+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['aemp_usnm']+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['aemp_name']+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['aemp_mob1']+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['zone_code']+'</td>'+
                            '<td>'+data.class_wise_ord_amnt[j]['zone_name']+'</td>';
                        
                        for (var i = 0; i < data.class_list.length; i++) {
                            cl_name=data.class_list[i]['itcl_name'];
                            if(j==0){
                                headings+='<th>'+data.class_list[i]['itcl_name']+'</th>';
                            }
                            
                            html+='<td>'+(data.class_wise_ord_amnt[j][cl_name]).toFixed(2)+'</td>';
                        }
                        html+='</tr>';
                        count++;
                    }

                    
                    $('#class_wise_order_summary_amount_headings').empty();
                    $("#cont_class_wise_order_summary").empty();
                    $("#class_wise_order_summary_amount_headings").append(headings);
                    $("#cont_class_wise_order_summary").append(html);
                    $('#tableDiv_class_wise_order_summary').show();
                }else if(reportType == "class_wise_order_report_memo"){
                    var headings='<th>Sl</th>'+
                                  '<th>Date</th>'+
                                  '<th>Staff ID</th>'+
                                  '<th>Name</th>'+
                                  '<th>Mobile</th>'+
                                  '<th>Zone Code</th>'+
                                  '<th>Zone Name</th>';
                                  
                    var cl_name='';             
                    count = 1;
                    for(var j=0;j<data.class_wise_ord_memo.length;j++){
                        html+='<tr><td>'+count+'</td>'+
                            '<td>'+data.class_wise_ord_memo[j]['ordm_date']+'</td>'+
                            '<td>'+data.class_wise_ord_memo[j]['aemp_usnm']+'</td>'+
                            '<td>'+data.class_wise_ord_memo[j]['aemp_name']+'</td>'+
                            
                            '<td>'+data.class_wise_ord_memo[j]['aemp_mob1']+'</td>'+
                            '<td>'+data.class_wise_ord_memo[j]['zone_code']+'</td>'+
                            '<td>'+data.class_wise_ord_memo[j]['zone_name']+'</td>';
                        for (var i = 0; i < data.class_list.length; i++) {
                            cl_name=data.class_list[i]['itcl_name'];
                            if(j==0){
                                headings+='<th>'+data.class_list[i]['itcl_name']+'</th>';
                            }
                            
                            html+='<td>'+(data.class_wise_ord_memo[j][cl_name])+'</td>';
                        }
                        html+='</tr>';
                        count++;
                    }
                    $('#class_wise_order_summary_memo_headings').empty();
                    $("#cont_class_wise_order_summary_memo").empty();
                    $('#class_wise_order_summary_memo_headings').append(headings);
                    $("#cont_class_wise_order_summary_memo").append(html);
                    $('#tableDiv_class_wise_order_summary_memo').show();
                }else if(reportType == "sr_route_outlet"){
                    $('#tableDiv_sr_route_outlet').show();
                     for (var i = 0; i < data.length; i++) {

                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_usnm'] + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td>' + data[i]['zone_name'] + '</td>' +
                            '<td style="background-color:'+colorEffect( data[i]["t_site"],'d1')+'">' + data[i]['t_site'] + '</td>' +
                            '</tr>';
                        count++;
                    }
                    $("#cont_sr_route_outlet").empty();
                    $("#cont_sr_route_outlet").append(html);

                }else if(reportType == "group_wise_route_outlet"){
                    var slgp_name=$("#sales_group_id option:selected" ).text();
                    $('#tableDiv_group_wise_route_outlet').show();
                    let b=0;
                     for (var i = 0; i < data.length; i++) {
                         b=parseInt(data[i]['b_60'])+parseInt(data[i]['b_120']);
                         b=(b/parseInt(data[i]['t_route'])*100).toFixed(2)
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + slgp_name + '</td>' +
                            '<td>' + data[i]['t_route'] + '</td>' +
                            '<td>' + data[i]['t_sr'] + '</td>' +
                            '<td>' + data[i]['b_60'] + '</td>' +
                            '<td>' + data[i]['b_60_120'] + '</td>' +
                            '<td>' + data[i]['b_120'] + '</td>' +
                            '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>' +
                            '</tr>';
                        count++;
                    }
                    $("#cont_group_wise_route_outlet").empty();
                    $("#cont_group_wise_route_outlet").append(html);

                }
                else if(reportType=='note_report'){
                    $('#exp_btn').removeAttr('onclick');
                    $('#exp_btn').attr('onclick','exportTableToCSV("note_report<?php echo date('Y_m_d'); ?>.csv","tableDiv_note_report")');
                    $('#head_note_report').empty();
                    $('#cont_note_report').empty();
                    var head='<tr><th>Date</th>'+
                            '<th>Staff id</th>'+
                            '<th>Staff Name</th>'+
                            '<th>Designation</th>'+
                            '<th>Group</th>'+
                            '<th>Zone</th>'+
                            '<th>Note/Task Details</th>'+
                            '<th>Note Type</th>'+
                            '<th>Outlet</th>'+
                           ' <th>Time</th>'+
                            '<th>Area</th><tr>';
                    var html='';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>'+
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
                            '<td>' + data[i]['geo_addr'] + '</td>' +
                            '</tr>';
                        count++;
                    }
                    $('#tableDiv_note_report').show();
                    $('#head_note_report').append(head);
                    $('#cont_note_report').append(html);

                }
                $('#ajax_load').css("display", "none");

            },error:function(error){
                console.log(time_period)
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
 function colorEffect(value,reportType){
    if(reportType=='d1'){
        if(value<60 ||value>120){
            return "#ffb5b7;color:#bb0a1e;";
        }
        else if(value>=60 && value<=120){
            return "#c5e1a5;color:#33691e;";
        }
    }
    else if(reportType=='d2'){
        if(value<=50){
            return "#c5e1a5;color:#33691e;";
        }
        if(value>50){
            
            return "#ffb5b7;color:#bb0a1e;";
        }
    }
    else if(reportType=='d3'){
        if(value>=60){
            return "#c5e1a5;color:#33691e;";
        }
        if(value<60){
            return "#ffb5b7;color:#bb0a1e;";
        }
    }
    else if(reportType=='sr_mov_sum'){
        if(value>=10){
            return "#c5e1a5;color:#33691e;";
        }
        if(value<10){
            return "#ffb5b7;color:#bb0a1e;";
        }
    }
    
 }

    function exportTableToCSV(filename, tableId) {
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
        csvFile = new Blob([csv], {type: "text/csv"});
        downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }
// Declare date as datepicker
    $(document).ready(function () {
        $('.start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: '-3m',
            maxDate: new Date(),
            autoclose: 1,
            showOnFocus: true
        });
    });

    $("#start_date").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: '-3m',
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
        minDate: '-3m',
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
table::-webkit-scrollbar{
/*Your styles here*/
}
table::-webkit-scrollbar-thumb{
/*Your styles here*/
}
table::-webkit-scrollbar-thumb:window-inactive{
/*Your styles here*/
}

    </style>

<script  id="deviation_rpt_script">
    //Helper Function(Date Option changed according to selected report)
    $("input[type='radio']").click(function(){
            var reportType = $("input[name='reportType']:checked").val();
            $('.start_date_div').hide();
            $('.start_date_period_div').show();
            dateFilterOptionControl(reportType);
    });
    function dateFilterOptionControl(reportType){
        console.log(reportType);
        var html='';
        if(reportType=='sr_activity_hourly_visit'||reportType=='sr_activity_hourly_order' ){
            html='<option value="1">Yesterday</option>'+
                '<option value="2">Last 3 days</option>'+
                '<option value="3">Last 7 days</option>'+
                '<option value="5">As Of</option>'+
                '<option value="">Custom Date?</option>';
        }
        // else if(reportType=='sr_activity'|| reportType=='sr_productivity' || reportType=='sr_non_productivity'||reportType=='sr_summary_by_group'|| reportType=='zone_wise_order_delivery_summary'){
        //     html='<option value="0">Today</option>'+
        //         '<option value="1">Yesterday</option>'+
        //         '<option value="2">Last 3 days</option>'+
        //         '<option value="3">Last 7 days</option>'+
        //         '<option value="">Custom Date?</option>'+
        //         '<option value="4">Last 30 Days</option>';
        // }
        else{
            html='<option value="0">Today</option>'+
                '<option value="1">Yesterday</option>'+
                '<option value="2">Last 3 days</option>'+
                '<option value="3">Last 7 days</option>'+
                '<option value="5">As Of</option>'+
                '<option value="">Custom Date?</option>';
                
        }
        $('#start_date_period').empty();
        $('#start_date_period').append(html);
    }
    //Deviation Mother Function
    function getDeviationData(reportType){
        hideReport();
        hide_me();
        addClass();
        $('#gvt_hierarchy').empty();
        $('#tracing_gvt').show();
        $('#emid_div1').empty();
        $('#ajax_load').css("display", "block");
        if(reportType=='group_wise_route_outlet'){
            $('#export_csv_tracking_gvt').removeAttr('onclick');
            $('#export_csv_tracking_gvt').attr('onclick','exportTableToCSV("abnormal_route_outlet_<?php echo date('Y_m_d'); ?>.csv","tableDiv_traking_gvt")');
            $('#deviation_date').removeAttr('onchange');
            $('#deviation_date').attr('onchange','getDateWiseDeviationData(this.value,1)');
            $('#deviation_date_div').hide();
            $.ajax({
                type:"get",
                url:"{{URL::to('/')}}/getDeviationData",
                success:function(data){
                    $('#ajax_load').css("display", "none");
                    console.log(data);
                    $('#tableDiv_traking_gvt').show();
                    $('#tableDiv_tracking_gvt_header1').empty();
                    var html='';
                    var dp_cnt='';
                    var head1='<tr><th>Company Name</th>'+
                                '<th>Total SR</th>'+
                                '<th>Total Route</th>'+
                                '<th>Underflow</th>'+
                                '<th>Between Limit</th>'+
                                '<th>Overflow</th>'+
                                '<th>Abnormal %</th>';
                    head1+='</tr>';
                    let b=0;
                    for(var i=0;i<data.length;i++){
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['acmp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                                '<td>' + b +'%'+ '</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['acmp_id']+'" stage="1">'+' '+data[i]['acmp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').empty();
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').empty();
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                },
                error:function(error){
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
        if(reportType=='outlet_visit'){
            $('#export_csv_tracking_gvt').removeAttr('onclick');
            $('#export_csv_tracking_gvt').attr('onclick','exportTableToCSV("outlet_visit_percentage_<?php echo date('Y_m_d'); ?>.csv","tableDiv_traking_gvt")');
            var stage=0;
            $('#deviation_date_div').show();
            $('#deviation_date').removeAttr('onchange');
            $('#deviation_date').attr('onchange','getDateWiseDeviationData(this.value,2)');
            $('#ajax_load').css("display", "block");
            $.ajax({
                type:"get",
                url:"{{URL::to('/')}}/getDeviationData/outletVisit/"+stage,
                success:function(data){
                    $('#ajax_load').css("display", "none");
                    console.log(data);
                    $('#tableDiv_traking_gvt').show();
                    $('#tableDiv_tracking_gvt_header1').empty();
                    var html='';
                    var head1='';
                    var dp_cnt='';
                    head1='<tr><th>Company Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+percent_visit.toFixed(2)+'</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="1">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                    $('#tableDiv_tracking_gvt_header1').empty();
                    $('#cont_traking_gvt').empty();
                    $('#tableDiv_tracking_gvt_header1').append(head1);
                    $('#cont_traking_gvt').append(html);
                    $('#all_dp_content1').empty();
                    $('#all_dp_content1').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                },
                error:function(error){
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
        if(reportType=='note_task'){
           // console.log(data);
            $('#tracing_gvt').hide();
            $('#ajax_load').css("display", "none");
            $('#title_head').show();
            $('#tracing').show();
            $('#desig').empty();
            $('#emid_div').empty();
            var emid=<?php echo $emid; ?>;
            var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
            $('#emid_div').append(emid_cnt);
            $('#employee_sales_traking_report_slgp').hide();
            $('#emp_task_note_report').show();
            $('#period').hide();
            $('#dev_note_date').show();
            var date=$('#dev_note_date').val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/getNoteTaskReport",
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    $('#tableDiv_traking').show();
                    $('#head_tracking_dev_note_task').empty();
                    var head='<td>Sl</td>'+
                            '<td>Name</td>'+
                            '<td>Mobile</td>'+
                            '<td>Own Task</td>'+
                            '<td>Assign Task</td>'+
                            '<td>Team Task</td>';
                    $('#head_tracking_dev_note_task').append(head);
                    //console.log(data);
                    var html="";
                    var count=1;
                    var dp_cnt='';
                    var task_details='';
                    for (var i = 0; i < data.length; i++) {
                        task_details='getTaskDetails/'+data[i]['id']+'/'+date;
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>'+
                            '<td><a href="'+task_details+'" target="_blank">' + data[i]['t_note'] +'  '+'<i class="fa fa-eye fa-2x" style="float:right;"></i>'+ '</a></td>'+
                            '<td>' + data[i]['assign_task'] + '</td>'+
                            '<td>' + data[i]['tm_task'] + '</td>';
                        dp_cnt+='<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="'+data[i]['id']+'" role_name="'+data[i]['aemp_name']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
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
        if(reportType=='sr_movement'){
            $('#tracing_gvt').show();
            $('#deviation_date_div').hide();
            $('#export_csv_tracking_gvt').removeAttr('onclick');
            $('#export_csv_tracking_gvt').attr('onclick','exportTableToCSV("sr_movement_summary<?php echo date('Y_m_d'); ?>.csv","tableDiv_traking_gvt")');
            $('#ajax_load').css("display", "block");
            $.ajax({
            type:"get",
            url:"{{URL::to('/')}}/getSrMovementSummary",
            success:function(data){
                $('#ajax_load').css("display", "none");
                console.log(data);
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var head1='<tr><th>Company</th>'+
                '<th>8AM ~ 01PM</th>'+
                    '<th>1PM ~ 2PM</th>'+
                    '<th>2PM ~ 7PM</th>'+
                    '<th>7PM+</th></tr>';
                var html="";
                var dp_cnt='';
                for(var i=0;i<data.length;i++){
                    html+='<tr>'+
                            '<td>'+data[i]['acmp_name']+'</td>'+
                            '<td>'+data[i]['1st_slot']+'</td>'+
                            '<td>'+data[i]['2nd_slot']+'</td>'+
                            '<td>'+data[i]['3rd_slot']+'</td>'+
                            '<td>'+data[i]['4th_slot']+'</td></tr>';
                    dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['acmp_id']+'" stage="1">'+' '+data[i]['acmp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                }
               $('#gvt_hierarchy').empty();
               $('#tableDiv_tracking_gvt_header1').empty();
               $('#tableDiv_tracking_gvt_header1').append(head1);
               $('#cont_traking_gvt').empty();
               $('#cont_traking_gvt').append(html);
               $('#all_dp_content1').empty();
               $('#all_dp_content1').append(dp_cnt);
               $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
            
            },
            error:function(error){
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
    function getDeviationDeeperData(v){
        //$('#deviation_date_div').show();
        var date=$('#deviation_date').val();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        v.removeAttribute('onclick');
        v.setAttribute('onclick','appendDeviationTopTitle(this)');
        var _token = $("#_token").val();
        $('#gvt_hierarchy').append(v);
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'">';
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getDeviationDeeperData",
            data:{
                _token:_token,
                id:id,
                date:date,
                stage:stage,
                slgp_id:slgp_id
            },
            dataType: "json",
            success: function (data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html='';
                var dp_cnt='';
                var head1='';
                
                head1+='</tr>';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    head1='<tr><th>Group Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Route</th>'+
                            '<th>Underflow</th>'+
                            '<th>Between Limit</th>'+
                            '<th>Overflow</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr  style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                                '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['slgp_id']+'"  stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                    
                }
                else if(stage==2){
                    head1='<tr><th>Zone Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Route</th>'+
                            '<th>Underflow</th>'+
                            '<th>Between Limit</th>'+
                            '<th>Overflow</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                                '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                    
                }
                else if(stage==3){
                    head1='<tr><th>Name</th>'+
                            '<th>Staff Id</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total Route</th>'+
                            '<th>Underflow</th>'+
                            '<th>Between Limit</th>'+
                            '<th>Overflow</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                       
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td>'+data[i]['aemp_mob1']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                               '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                       // dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                    
                }             
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                return false;
               
            },error:function(error){
                $('#ajax_load').css('display','none');
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
                console.log(error);
            }
        });
        
    }
    function appendDeviationTopTitle(v){
        var date=$('#deviation_date').val();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        $(v).nextAll().remove();
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        var _token = $("#_token").val();
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'" slgp_id="'+slgp_id+'">';
        $('#emid_div1').append(emid_cnt);
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getDeviationDeeperData",
            data:{
                _token:_token,
                id:id,
                date:date,
                stage:stage,
                slgp_id:slgp_id
            },
            dataType: "json",
            success: function (data){
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html='';
                var dp_cnt='';
                var head1='';
                
                head1+='</tr>';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    head1='<tr><th>Group Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Route</th>'+
                            '<th>Underflow</th>'+
                            '<th>Between Limit</th>'+
                            '<th>Overflow</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                                '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';   
                    }
                    
                }
                if(stage==2){
                    head1='<tr><th>Zone Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Route</th>'+
                            '<th>Below 60</th>'+
                            '<th>60 ~ 120</th>'+
                            '<th>Above 120</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                                '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                    
                } 
                else if(stage==3){
                    head1='<tr><th>Name</th>'+
                            '<th>Staff Id</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total Route</th>'+
                            '<th>Underflow</th>'+
                            '<th>Between Limit</th>'+
                            '<th>Overflow</th>'+
                            '<th>Abnormal %</th><tr>';
                            let b=0;
                    for(var i=0;i<data.length;i++){
                       
                        b=parseInt(data[i]['underflow'])+parseInt(data[i]['overflow']);
                        b=(b/parseInt(data[i]['t_rout'])*100).toFixed(2)
                        html+='<tr style="background-color:'+colorEffect(b,'d2')+'">'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td>'+data[i]['aemp_mob1']+'</td>'+
                                '<td>'+data[i]['t_rout']+'</td>'+
                                '<td>'+data[i]['underflow']+'</td>'+
                                '<td>'+data[i]['between_limit']+'</td>'+
                                '<td>'+data[i]['overflow']+'</td>'+
                               '<td style="background-color:'+colorEffect(b,'d2')+'">' + b +'%'+ '</td>';
                       // dp_cnt+='<li><a href="#" onclick="getDeviationDeeperData(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        html+='</tr>';
                        
                    }
                    
                }             
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                return false;
               
            },error:function(error){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
                console.log(error);
            }
        });
    }
    function getDateWiseDeviationData(date,rpt){
        var id=$('#gvt_hierarchy_id').val();
        var stage=$('#gvt_hierarchy_id').attr('stage');
        var slgp_id='';
        if(stage==''){
            stage=0;
        }
        if(stage==3){
            slgp_id=$('#gvt_hierarchy_id').attr('slgp_id');
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
        if(rpt==2){
            $('#ajax_load').css("display", "block");    
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getDeviationData/outletVisit/"+stage,
                data:{
                    _token:_token,
                    id:id,
                    date:date,
                    stage:stage,
                    slgp_id:slgp_id
                },
                dataType: "json",
                success: function (data){
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    $('#tableDiv_traking_gvt').show();
                    $('#tableDiv_tracking_gvt_header1').empty();
                    var html='';
                    var head1='';
                    var dp_cnt='';
                    if(stage==1){
                    head1='<tr><th>Group Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==2){
                    head1='<tr><th>Zone Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==3){
                    head1='<tr><th>Name</th>'+
                            '<th>Staff Id</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Visit</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        if(data[i]['t_outlet']!=0){
                            percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']);
                            percent_visit=percent_visit>100?100:percent_visit;
                        } else{
                            percent_visit=0;
                        }
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td>'+data[i]['aemp_mob1']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+data[i]['t_visit']+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                       // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }  
                else{
                    head1='<tr><th>Company</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }   
                }   
                    $('#tableDiv_tracking_gvt_header1').empty();
                    $('#cont_traking_gvt').empty();
                    $('#tableDiv_tracking_gvt_header1').append(head1);
                    $('#cont_traking_gvt').append(html);
                    $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                
                },error:function(error){
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
    function getDeviationOutletVisit(v){
        $('#deviation_date_div').show();
        $('#deviation_date').removeAttr('onchange');
        $('#deviation_date').attr('onchange','getDateWiseDeviationData(this.value,2)');
        var date=$('#deviation_date').val();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        var _token = $("#_token").val();
       // $('#gvt_hierarchy').empty();
        v.removeAttribute('onclick');
        v.setAttribute('onclick','devOutletVisitTopClick(this)');   
        $('#gvt_hierarchy').append(v);
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'" slgp_id="'+slgp_id+'">';
        $('#emid_div1').empty();
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");    
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getDeviationData/outletVisit/"+stage,
            data:{
                _token:_token,
                id:id,
                date:date,
                slgp_id:slgp_id
            },
            dataType: "json",
            success: function (data){
                //console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html='';
                var head1='';
                var dp_cnt='';

                if(stage==1){
                    head1='<tr><th>Group Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==2){
                    head1='<tr><th>Zone Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'"  slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==3){
                    head1='<tr><th>Name</th>'+
                            '<th>Staff Id</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Visit</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        if(data[i]['t_outlet']!=0){
                            percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']);
                            percent_visit=percent_visit>100?100:percent_visit;
                        } else{
                            percent_visit=0;
                        }
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td>'+data[i]['aemp_mob1']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+data[i]['t_visit']+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                       // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                    
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').empty();
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
               
            },error:function(error){
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
    function devOutletVisitTopClick(v){
        $(v).nextAll().remove();
        var date=$('#deviation_date').val();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        var _token = $("#_token").val();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+'" slgp_id="'+slgp_id+'">';
        $('#emid_div1').empty();
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getDeviationData/outletVisit/"+stage,
                data:{
                    _token:_token,
                    id:id,
                    date:date,
                    slgp_id:slgp_id
                },
                dataType: "json",
                success: function (data){
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    $('#tableDiv_traking_gvt').show();
                    $('#tableDiv_tracking_gvt_header1').empty();
                    var html='';
                    var head1='';
                    var dp_cnt='';
                    if(stage==1){
                    head1='<tr><th>Group Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==2){
                    head1='<tr><th>Zone Name</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }
                else if(stage==3){
                    head1='<tr><th>Name</th>'+
                            '<th>Staff Id</th>'+
                            '<th>Mobile</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Visit</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        if(data[i]['t_outlet']!=0){
                            percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']);
                        } else{
                            percent_visit=0;
                        }
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td>'+data[i]['aemp_mob1']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+data[i]['t_visit']+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                       // dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['zone_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }       
                }  
                else{
                    head1='<tr><th>Company</th>'+
                            '<th>Total SR</th>'+
                            '<th>Total Outlet</th>'+
                            '<th>Olt/SR</th>'+
                            '<th>Visit/SR</th>'+
                            '<th>Visit Percentage</th></tr>';
                    var percent_visit=0;
                    for(var i=0;i<data.length;i++){
                        percent_visit=(100*data[i]['t_visit'])/(data[i]['t_outlet']<1?1:data[i]['t_outlet']);
                        html+='<tr style="background-color:'+colorEffect(percent_visit,'d3')+'">'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['t_sr']+'</td>'+
                                '<td>'+data[i]['t_outlet']+'</td>'+
                                '<td>'+(data[i]['t_outlet']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td>'+(data[i]['t_visit']/data[i]['t_sr']).toFixed(2)+'</td>'+
                                '<td >'+percent_visit.toFixed(2)+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getDeviationOutletVisit(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>'; 
                    }   
                }   
                    $('#tableDiv_tracking_gvt_header1').empty();
                    $('#cont_traking_gvt').empty();
                    $('#tableDiv_tracking_gvt_header1').append(head1);
                    $('#cont_traking_gvt').append(html);
                    $('#all_dp_content1').empty();
                    $('#all_dp_content1').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
                
                },error:function(error){
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
    function devNoteTaskClickAppend(v){
        var emid=$(v).attr('emid');
        v.removeAttribute('onclick');
        v.setAttribute('onclick','devNoteAppendRemove(this)');
        var date=$('#dev_note_date').val();
        var _token = $("#_token").val();
        $('#desig').append(v);
        $('#emid_div').empty();
        var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
        $('#emid_div').append(emid_cnt);
        $('#employee_sales_traking_report_slgp').hide();
        $('#emp_task_note_report').show();
        $('#period').hide();
        $('#dev_note_date').show();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getUserAndDateWiseTaskNote",
            data:{
                _token:_token,
                emid:emid,
                date:date
            },
            dataType: "json",
            success: function (data){
                $('#ajax_load').css("display", "none");
                $('#head_tracking_dev_note_task').empty();
                var head='<td>Sl</td>'+
                        '<td>Name</td>'+
                        '<td>Mobile</td>'+
                        '<td>Own Task</td>'+
                        '<td>Assign Task</td>'+
                        '<td>Team Task</td>';
                $('#head_tracking_dev_note_task').append(head);
                
                var html="";
                var count=1;
                var dp_cnt='';
                var task_details='';
                for (var i = 0; i < data.length; i++) {
                    task_details='getTaskDetails/'+data[i]['id']+'/'+date;
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td><a href="'+task_details+'" target="_blank">' + data[i]['t_note'] +'  '+'<i class="fa fa-eye fa-2x" style="float:right;"></i>'+ '</a></td>' +
                        '<td>' + data[i]['assign_task'] + '</td>'+        
                        '<td>' + data[i]['tm_task'] + '</td>';         
                        dp_cnt+='<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="'+data[i]['id']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                    count++;
                }
                $('#cont_traking').empty();
                $('#all_dp_content').empty();
                $('#cont_traking').append(html);
                $('#all_dp_content').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height()+150);
            },error:function(error){
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
    function getDateWiseDevTaskNote(date){
        var emid=$('#emid').val();
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getUserAndDateWiseTaskNote",
                data:{
                    _token:_token,
                    emid:emid,
                    date:date
                },
                dataType: "json",
                success: function (data){
                    $('#ajax_load').css("display", "none");
                    $('#head_tracking_dev_note_task').empty();
                    var head='<td>Sl</td>'+
                            '<td>Name</td>'+
                            '<td>Mobile</td>'+
                            '<td>Own Task</td>'+
                            '<td>Assign Task</td>'+
                            '<td>Team Task</td>';
                    $('#head_tracking_dev_note_task').append(head);
                    var html="";
                    var count=1;
                    var dp_cnt='';
                    var task_details='';
                    for (var i = 0; i < data.length; i++) {
                        task_details='getTaskDetails/'+data[i]['id']+'/'+date;
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td><a href="'+task_details+'" target="_blank">' + data[i]['t_note'] +'  '+'<i class="fa fa-eye fa-2x" style="float:right;"></i>'+ '</a></td>'+
                            '<td>' + data[i]['assign_task'] + '</td>'+        
                            '<td>' + data[i]['tm_task'] + '</td>' ;          
                            dp_cnt+='<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="'+data[i]['id']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
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
    function devNoteAppendRemove(elem){
        $(elem).nextAll().remove();
        var emid=$(elem).attr('emid');
        $('#emid_div').empty();
        var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
        $('#emid_div').append(emid_cnt);
        var emid=$(elem).attr('emid');
        var _token = $("#_token").val();
        var date=$('#dev_note_date').val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getUserAndDateWiseTaskNote",
            data:{
                _token:_token,
                emid:emid,
                date:date
            },
            dataType: "json",
            success: function (data){
                $('#ajax_load').css("display", "none");
                $('#head_tracking_dev_note_task').empty();
                var head='<td>Sl</td>'+
                        '<td>Name</td>'+
                        '<td>Mobile</td>'+
                        '<td>Own Task</td>'+
                        '<td>Assign Task</td>'+
                        '<td>Team Task</td>';
                $('#head_tracking_dev_note_task').append(head);
                
                var html="";
                var count=1;
                var dp_cnt='';
                var task_details='';
                for (var i = 0; i < data.length; i++) {
                    task_details='getTaskDetails/'+data[i]['id']+'/'+date;
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] +'-'+data[i]['aemp_usnm']+ '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td><a href="'+task_details+'" target="_blank">' + data[i]['t_note'] +'  '+'<i class="fa fa-eye fa-2x" style="float:right;"></i>'+ '</a></td>' +
                        '<td>' + data[i]['assign_task'] + '</td>'+        
                        '<td>' + data[i]['tm_task'] + '</td>';         
                        dp_cnt+='<li><a href="#" onclick="devNoteTaskClickAppend(this)" emid="'+data[i]['id']+'">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
                    count++;
                }
                $('#cont_traking').empty();
                $('#all_dp_content').empty();
                $('#cont_traking').append(html);
                $('#all_dp_content').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height()+150);
            },error:function(error){
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
    function getSrMovementSummaryDeeparData(v){
        $('#deviation_date_div').hide();
        var id=$(v).attr('id'); 
        var stage=$(v).attr('stage');
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        v.removeAttribute('onclick');
        v.setAttribute('onclick','appendTopTitleSrMovementSummary(this)');
        var _token = $("#_token").val();
        $('#gvt_hierarchy').append(v);
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+' slgp_id="'+slgp_id+'">';
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getSrMovementSummaryDeeparData",
            data:{
                _token:_token,
                id:id,
                stage:stage,
                slgp_id:slgp_id
            },
            dataType: "json",
            success: function (data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html="";
                var dp_cnt='';
                var head1='';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    var head1='<tr><th>Group</th>'+
                    '<th>8AM ~ 01PM</th>'+
                    '<th>1PM ~ 2PM</th>'+
                    '<th>2PM ~ 7PM</th>'+
                    '<th>7PM+</th></tr>';
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['1st_slot']+'</td>'+
                                '<td>'+data[i]['2nd_slot']+'</td>'+
                                '<td>'+data[i]['3rd_slot']+'</td>'+
                                '<td>'+data[i]['4th_slot']+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else if(stage==2){
                    var head1='<tr><th>Zone</th>'+
                    '<th>8AM ~ 01PM</th>'+
                    '<th>1PM ~ 2PM</th>'+
                    '<th>2PM ~ 7PM</th>'+
                    '<th>7PM+</th></tr>';
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['1st_slot'],'sr_mov_sum')+'">'+data[i]['1st_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['2nd_slot'],'sr_mov_sum')+'">'+data[i]['2nd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['3rd_slot'],'sr_mov_sum')+'">'+data[i]['3rd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['4th_slot'],'sr_mov_sum')+'">'+data[i]['4th_slot']+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['zone_id']+'" stage="3" slgp_id="'+data[i]['slgp_id']+'">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else if(stage==3){
                    var head1='<tr><th>Name</th>'+
                    '<th>Staff Id</th>'+
                    '<th>8AM ~ 01PM</th>'+
                    '<th>1PM ~ 2PM</th>'+
                    '<th>2PM ~ 7PM</th>'+
                    '<th>7PM+</th></tr>';
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['1st_slot'],'sr_mov_sum')+'">'+data[i]['1st_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['2nd_slot'],'sr_mov_sum')+'">'+data[i]['2nd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['3rd_slot'],'sr_mov_sum')+'">'+data[i]['3rd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['4th_slot'],'sr_mov_sum')+'">'+data[i]['4th_slot']+'</td></tr>';
                       // dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['aemp_usnm']+'" stage="4">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else{
                    html='<h2><tr>Hierarchy End!!</tr></h2>';
                }
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
               
            },error:function(error){
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
    function appendTopTitleSrMovementSummary(v){
        $('#deviation_date_div').hide();
        var id=$(v).attr('id');
        var stage=$(v).attr('stage');
        var slgp_id='';
        if(stage==3){
            slgp_id=$(v).attr('slgp_id');
        }
        $(v).nextAll().remove();
        var _token = $("#_token").val();
        $('#emid_div1').empty();
        var emid_cnt='<input type="hidden" value="'+id+'" id="gvt_hierarchy_id" stage="'+stage+' slgp_id="'+slgp_id+'">';
        $('#emid_div1').append(emid_cnt);
        $('#ajax_load').css("display", "block");
        $.ajax({
            type:"POST",
            url:"{{URL::to('/')}}/getSrMovementSummaryDeeparData",
            data:{
                _token:_token,
                id:id,
                stage:stage,
                slgp_id:slgp_id
            },
            dataType: "json",
            success: function (data){
                console.log(data);
                $('#ajax_load').css("display", "none");
                $('#tableDiv_traking_gvt').show();
                $('#tableDiv_tracking_gvt_header1').empty();
                var html="";
                var dp_cnt='';
                var head1='';
                //$('#tableDiv_tracking_gvt_header1').append(head1+sub_head);     
                if(stage==1){
                    var head1='<tr><th>Group</th>'+
                    '<th>09AM-01PM</th>'+
                    '<th>02PM-05PM</th>'+
                    '<th>06PM-09PM</th><tr>';
                    var html="";
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['slgp_name']+'</td>'+
                                '<td>'+data[i]['1st_slot']+'</td>'+
                                '<td>'+data[i]['2nd_slot']+'</td>'+
                                '<td>'+data[i]['3rd_slot']+'</td>'+
                                '<td>'+data[i]['4th_slot']+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['slgp_id']+'" stage="2">'+' '+data[i]['slgp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else if(stage==2){
                    var head1='<tr><th>Zone</th>'+
                    '<th>09AM-01PM</th>'+
                    '<th>02PM-05PM</th>'+
                    '<th>06PM-09PM</th><tr>';
                    var html="";
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['zone_name']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['1st_slot'],'sr_mov_sum')+'">'+data[i]['1st_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['2nd_slot'],'sr_mov_sum')+'">'+data[i]['2nd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['3rd_slot'],'sr_mov_sum')+'">'+data[i]['3rd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['4th_slot'],'sr_mov_sum')+'">'+data[i]['4th_slot']+'</td></tr>';
                        dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['zone_id']+'" slgp_id="'+data[i]['slgp_id']+'" stage="3">'+' '+data[i]['zone_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else if(stage==3){
                    var head1='<tr><th>Name</th>'+
                    '<th>Staff Id</th>'+
                    '<th>8AM ~ 01PM</th>'+
                    '<th>1PM ~ 2PM</th>'+
                    '<th>2PM ~ 7PM</th>'+
                    '<th>7PM+</th></tr>';
                    var dp_cnt='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+data[i]['aemp_name']+'</td>'+
                                '<td>'+data[i]['aemp_usnm']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['1st_slot'],'sr_mov_sum')+'">'+data[i]['1st_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['2nd_slot'],'sr_mov_sum')+'">'+data[i]['2nd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['3rd_slot'],'sr_mov_sum')+'">'+data[i]['3rd_slot']+'</td>'+
                                '<td style="background-color:'+colorEffect(data[i]['4th_slot'],'sr_mov_sum')+'">'+data[i]['4th_slot']+'</td></tr>';
                       // dp_cnt+='<li><a href="#" onclick="getSrMovementSummaryDeeparData(this)" id="'+data[i]['aemp_usnm']+'" stage="4">'+' '+data[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';   
                    }   
                }
                else{
                    var html='<h2><tr>Hierarchy End!!</tr></h2>';
                }
                $('#tableDiv_tracking_gvt_header1').empty();
                $('#cont_traking_gvt').empty();
                $('#all_dp_content1').empty();
                $('#tableDiv_tracking_gvt_header1').append(head1);
                $('#cont_traking_gvt').append(html);
                $('#all_dp_content1').append(dp_cnt);
                $('#rpt').height($("#tableDiv_traking_gvt").height()+150);
               
            },error:function(error){
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

function showCategoryWiseOutlet(emid){
    
    $("#myModalVisit").modal({backdrop:false});
    $('#myModalVisit').modal('show');
  $('#cat_out_load').show();
  var date=$('#period').val();
  $.ajax({
    type:"get",
    url:"{{URL::to('/')}}/getCatWiseOutVisit/"+emid+"/"+date,
    dataType: "json",
    success: function (data){
     
     $('#cat_out_load').hide();
     
      console.log(data);
      var html='';
      for(var i=0;i<data.length;i++){
        html+='<tr><td>'+data[i]['otcg_name']+'</td>'+
        '<td>'+data[i]['num']+'</td>'+
        '<td><i id="show" class="fa fa-info-circle fa-2x "   onclick="getVisitedOutletDetails('+emid+','+data[i]['id']+')" style="cursor:pointer;color:forestgreen;"></i></td>'+
        '</tr>';
      }
     // $('.modal-backdrop ').removeClass('modal-backdrop');
      $('#myModalVisitBody').empty();
      $('#myModalVisitBody').append(html);
    },error:function(error){
      console.log(error);
    }
  });
}
function getVisitedOutletDetails(emid,cat_id){
    var date=$('#period').val();
    $("#myModalVisitedOutlet").modal({backdrop: false});
    $('#myModalVisitedOutlet').modal('show');
    $('#cat_out_load_details').show();
    $.ajax({
        type:"get",
        url:"{{URL::to('/')}}/getVisitedOutletDetails/"+emid+"/"+date+"/"+cat_id,
        dataType: "json",
        success: function (data){
        $('#cat_out_load_details').hide();
        console.log(data);
        var html='';
        for(var i=0;i<data.length;i++){
            html+='<tr><td>'+data[i]['site_code']+'</td>'+
            '<td>'+data[i]['site_name']+'</td>'+
            '<td>'+data[i]['site_mob1']+'</td>'+
            '<td>'+data[i]['site_adrs']+'</td>'+
            '</tr>';
        }
        // $('.modal-backdrop ').removeClass('modal-backdrop');
        $('#myModalVisitedOutletBody').empty();
        $('#myModalVisitedOutletBody').append(html);
        },error:function(error){
        console.log(error);
        }
    });
}

//History Report Data Function 
function getHReport(){
    var reportType = $("input[name='historyType']:checked").val();
    var sr_id=$("#sr_id_h").val();
    if(reportType===undefined){
            alert ('Please select report');
            return false;
        }
    else if(reportType==''){
        alert('Please select report');
        return false;
    }
    if(sr_id==undefined|| sr_id==''){
        alert("Please select SR");
        return false;
    }
   // alert(sr_id)
    var time_period=$("#start_date_period_h").val();
    if(time_period==''){
            time_period =$('#start_date_h').val()
        }
    var _token = $("#_token").val();
    $('#ajax_load').css('display','block');
    $.ajax({
        type:"POST",
        url:"{{URL::to('/')}}/getHistoryReportData",
        data:{
            sr_id:sr_id,
            time_period:time_period,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            $('#ajax_load').css('display','none');
            $('#tableDiv_sr_history').show();
            $('#head_history').empty();
            $('#cont_history').empty();
            $('#foot_history').empty();
          
            var html='';
            var heading='';
            var footer='';
            var t_visit=0;
            var t_memo=0;
            var t_amnt=0.00;
            var t_olt=0;
            for(var i=0;i<data.length;i++){
                t_visit=t_visit+data[i]['t_visit'];
                t_memo=t_memo+data[i]['t_memo'];
                t_amnt=t_amnt+data[i]['t_amnt'];
                t_olt=t_olt+data[i]['rout_olt'];
                html+='<tr><td>'+data[i]['dhbd_date']+'</td>'+
                '<td>'+data[i]['aemp_usnm']+'</td>'+
                '<td>'+data[i]['aemp_name']+'</td>'+
                '<td>'+data[i]['rout_id']+'</td>'+
                '<td>'+data[i]['rout_olt']+'</td>'+
                '<td>'+data[i]['t_visit']+'</td>'+
                '<td>'+data[i]['t_memo']+'</td>'+
                '<td>'+data[i]['strikeRate']+'</td>'+
                '<td>'+data[i]['lpc']+'</td>'+
                '<td>'+data[i]['t_amnt']+'</td>'+
                '<td><button class="btn btn-success in_tg" onclick="showWardWiseVisitDetails(this)" sr_id="'+data[i]["id"]+'" date="'+data[i]["dhbd_date"]+'">View</button>'+
                '<button class="btn btn-danger in_tg" onclick="showVisitMap('+data[i]["id"]+','+data[i]["dhbd_date"]+')">Map</button></td>'+
                '</tr>';
            }
            if(reportType=="sr_history"){
                heading+='<tr><th>Date </th>'+
                        '<th>SR ID</th><th>SR Name</th>'+
                        '<th>Rout ID</th><th>Rout Outlet</th>'+
                        '<th>Visit</th><th>Order</th>'+
                        '<th>Strike Rate</th><th>LPC</th>'+
                        '<th>Exp.</th><th>Action</th><tr>';
                footer+='<tr><th colspan="4">Total</th>'+
                        '<th>'+t_olt+'</th>'+
                        '<th>'+t_visit+'</th>'+
                        '<th>'+t_memo+'</th><th></th><th></th>'+
                        '<th>'+(t_amnt).toFixed(2)+'</th><th></th></tr>';
            }
             $("#head_history").append(heading);
            $("#cont_history").append(html);
            $("#foot_history").append(footer);
            console.log(data);
            
        },error:function(error){
            $('#ajax_load').css('display','none');
            console.log(error);
        }
    });
}

function showWardWiseVisitDetails(v){
    $("#myModalWardWiseVisit").modal({backdrop: false});
    $('#myModalWardWiseVisit').modal('show');
    $('#load_ward_visit').show();
    var date=$(v).attr('date');
    var emp_id=$(v).attr('sr_id');
    //alert(emp_id);
  //  alert(date);
    $.ajax({
        type:"get",
        url:"{{URL::to('/')}}/getWardWiseVisitDetails/"+emp_id+"/"+date,
        dataType:"json",
        success:function(data){
            $('#load_ward_visit').hide();
            $('#myModalWardWiseVisitBody1').empty();
            $('#myModalWardWiseVisitBody2').empty();
            $('#myModalWardWiseVisitBody3').empty();
            var html='';
            var html1='';
            var html2='';
            var count=1;
            console.log(data);
            for(var i=0;i<data.data1.length;i++){
                html+='<tr><td>'+count+'</td>'+
                        '<td>'+data.data1[i]["mktm_name"]+'</td>'+
                        '<td>'+data.data1[i]["t_visit"]+'</td>'+
                        '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showWardWiseVisitOutletDetails(this,1)" class="fa fa-info-circle fa-2x  pull-right" sr_id="'+data.data1[i]["aemp_id"]+'" date="'+data.data1[i]["ssvh_date"]+'"></i></td>';
                        count++;
            }
            count=1
            for(var i=0;i<data.data2.length;i++){
                html1+='<tr><td>'+count+'</td>'+
                        '<td>'+data.data2[i]["than_name"]+'</td>'+
                        '<td>'+data.data2[i]["t_visit"]+'</td>'+
                        '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showWardWiseVisitOutletDetails(this,3)" class="fa fa-info-circle fa-2x  pull-right" sr_id="'+data.data1[i]["aemp_id"]+'" date="'+data.data1[i]["ssvh_date"]+'"></i></td>';
                count++;
                
            }
            count=1
            for(var i=0;i<data.data3.length;i++){
                html2+='<tr><td>'+count+'</td>'+
                        '<td>'+data.data3[i]["ward_name"]+'</td>'+
                        '<td>'+data.data3[i]["t_visit"]+'</td>'+
                        '<td><i id="show" style="color:forestgreen;cursor:pointer;" onclick="showWardWiseVisitOutletDetails(this,2)" class="fa fa-info-circle fa-2x  pull-right" sr_id="'+data.data1[i]["aemp_id"]+'" date="'+data.data1[i]["ssvh_date"]+'"></i></td>';
                count++;
                
            }
            $('#myModalWardWiseVisitBody1').append(html);
            $('#myModalWardWiseVisitBody2').append(html1);
            $('#myModalWardWiseVisitBody3').append(html2);
            console.log(data);
        },error:function(error){
            console.log(error);
        }
    });
}
function showWardWiseVisitOutletDetails(v,type){
    var id=$(v).attr("sr_id");
    var date=$(v).attr("date");
    $.ajax({
        type:"get",
        url:"{{URL::to('/')}}/getWardWiseVisitDetails/"+emp_id+"/"+date,
        dataType:"json",
        success:function(data){
            console.log(data);
        },error:function(error){
            console.log(error);
        }
    });
    

}
</script>
@endsection