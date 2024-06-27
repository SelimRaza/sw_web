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


                                <div class="col-md-6 col-sm-6" id="rp">
                                    <div class="item form-group rp_type_div" id="rpt">
                                        <div class="x_title">
                                            <div class="btnDiv col-md-12 col-sm-12" style="margin-bottom:5px;">
                                                <div id="exTab1" class="container">	
                                                    <ul  class="nav nav-pills">
                                                                <li>
                                                            <a  href="#1a" data-toggle="tab"  onclick="getSRReport()">SR </a>
                                                                </li>
                                                                <li><a href="#2a" data-toggle="tab"  onclick="getOutletReport()"> Outlet</a>
                                                                </li>
                                                                <li><a href="#3a" data-toggle="tab" onclick="getOrderReport()"> Order</a>
                                                                </li>
                                                                <li><a href="#4a" data-toggle="tab" onclick="getDeviaitonReport()" >
                                                                 Deviation</a>
                                                                 <li><a href="#4a" data-toggle="tab"  onclick="getNoteReport()" > Note</a>
                                                                </li>
                                                                <li><a href="#4a" data-toggle="tab"    onclick="getDiggingReport()" >Tracking</a>
                                                                </li>
                                                    </ul>

                                                </div>

                                                

                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="font-size: 11px; height: 115px;" id="rpt_selection_div">
                                            <div id="sr_report" class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-md-6 col-sm-6 ">
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
                                            <div id="outlet_report" class="col-md-12 col-sm-12 col-xs-12">
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
                                            <div id="tracing" class="col-md-12 col-sm-12 col-xs-12">
                                                
                                                <div class="col-md-10 col-sm-10" id="user_level" style="font-size:14px;">
                                                        <div >
                                                           <p style="font-weight:bold;"> {{$role_id[0]->role_name}} ><span id="desig"></span></p>
                                                            
                                                        </div>
                                                        <div >
                                                            <div id='emid_div' style="display:none;">
                                                                <input type="hidden" value="{{$emid}}" id="emid">
                                                            </div>
                                                            <div class="btn-group dropright">
                                                                <a href="#" class=" dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    All >
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="about-us" style="margin-left:45px;margin-top:-20px;" id="all_dp_content">
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                    
                                                </div>
                                                <div class="col-md-2 col-sm-2 float-right ">
                                                   <select class="form-control in_tg" id="period" onchange="getDateWiseUserReport()">
                                                       <option value="<?php echo date('Y-m-d'); ?>">Today</option>
                                                       <option value="<?php echo date('Y-m-d',strtotime("-1 days")); ?>">Yesterday</option>
                                                   </select>
                                                </div>
                                                {{--traking report --}}
                                                <br></br>
                                                <div id="tableDiv_traking" class="col-md-10 col-sm-10 col-sm-offset-1 col-md-offset-1">
                                                    <div class="x_panel">
                                                        <div class="x_content">
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                                                <div align="right">

                                                                    <a href="#" onclick="exportTableToCSV('employee_traking_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_traking')"
                                                                            class="btn btn-warning">Export CSV File
                                                                    </a>
                                                                </div>
                                                                <table id="datatablesa" class="table table-bordered table-responsive"
                                                                    data-page-length='100'>
                                                                    <thead>
                                                                    <tr class="">
                                                                        <th>Sl</th>
                                                                        <th>Name</th>
                                                                        <th>Staff ID</th>
                                                                        <th>Mobile</th>
                                                                        <th>Total Visited</th>
                                                                        <th>Memo</th>
                                                                        <th>Order Amount</th>
                                                                        <th>Target</th>
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
                                            <div id="order_report" class="col-md-12 col-sm-12 col-xs-12">

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
                                                               value="sku_wise_order_delivery"/>SKU Wise Order Vs Delivery
                                                    </label>
                                                </div>

                                            </div>
                                            <div id="note_report" class="col-md-12 col-sm-12 col-xs-12">

                                                {{--<div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="w_class" checked/> Outlet Order Summary
                                                    </label>
                                                </div>--}}

                                            </div>
                                            <div id="deviation_report" class="col-md-12 col-sm-12 col-xs-12">

                                                {{--<div class="col-md-6 col-sm-6 ">
                                                    <label>
                                                        <input type="radio" class="flat" name="reportType"
                                                               id="reportType"
                                                               value="w_class" checked/> Outlet Order Summary
                                                    </label>
                                                </div>--}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6" style="font-size: 12px;" id="harch">
                                    <div class="item form-group rp_type_div " id="hierarchy">
                                        <div class="col-md-12 col-sm-12" style="height: 263px;">

                                        <div class="x_title" id="title_head">
                                                <div class="btnDiv col-md-12 col-sm-12" style="margin-bottom: 5px;">
                                                   <br><br>
                                                </div>
                                                <div class="clearfix"></div>
                                        </div>
                                                <br><br>
                                            <div id="sales_heirarchy">
                                                
                                                <div class="item form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="acmp_id" id="acmp_id"
                                                                onchange="getGroup(this.value)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Group<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="sales_group_id"
                                                                id="sales_group_id"
                                                                onchange="clearDate()">
                                                            <option value="">Select Group</option>
                                                        </select>
                                                    </div>


                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Region
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="dirg_id" id="dirg_id"
                                                                onchange="getZone(this.value)" >
                                                            <option value="">Select Region</option>
                                                            @foreach($region as $regionList)
                                                                <option value="{{$regionList->id}}">{{$regionList->dirg_code}}
                                                                    - {{$regionList->dirg_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Zone<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="zone_id" id="zone_id"
                                                                onchange="getSR(this.value)">

                                                            <option value="">Select Zone</option>
                                                        </select>
                                                    </div>


                                                </div>


                                                <div class="form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From
                                                        Date<span class="required">*</span>
                                                    </label>

                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="form-control in_tg" name="start_date"
                                                               id="start_date"
                                                               autocomplete="off"/>
                                                    </div>

                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To
                                                        Date<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="form-control in_tg" name="end_date"
                                                               id="end_date"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12"
                                                           for="name">SR<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="sr_id" id="sr_id">
                                                            <option value="">Select SR</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-2 col-sm-offset-2">
                                                        <button id="send" type="button"
                                                                class="btn btn-success btn-block in_tg"
                                                                onclick="getSummaryReport()">Show
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="item form-group">

                                                </div>

                                            </div>
                                            <div id="govt_heirarchy">
                                            <div class="item form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="acmp_id" id="acmp_id_d"
                                                                onchange="getGroup(this.value)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                    - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Group<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="sales_group_id"
                                                                id="sales_group_id_gvt"
                                                                onchange="clearDate()">
                                                            <option value="">Select Group</option>
                                                        </select>
                                                    </div>
                                                    

                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12"
                                                           for="name">District<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="dist_id" id="dist_id"
                                                                onchange="getThanaBelogToDistrict(this.value)">
                                                            <option value="">Select District</option>
                                                            @foreach($dsct as $dsct)
                                                                <option value="{{$dsct->id}}">{{$dsct->dsct_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Thana<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="than_id" id="than_id"
                                                                onchange="getWardNameBelogToThana(this.value)">
                                                            <option value="">Select Thana</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="item form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Ward<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="ward_id" id="ward_id"
                                                                onchange="loadWardMarket()">
                                                            <option value="">Select Ward</option>

                                                        </select>
                                                    </div>

                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Market<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="market_id" id="market_id"
                                                                onchange="loadOutlet()">

                                                            <option value="">Select Market</option>
                                                        </select>
                                                    </div>


                                                </div>


                                                <div class="form-group">

                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From
                                                        Date<span
                                                                class="required">*</span>
                                                    </label>

                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="form-control in_tg" name="start_date_d"
                                                               id="start_date_d"
                                                               autocomplete="off"/>
                                                    </div>

                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To
                                                        Date<span
                                                                class="required">*</span>
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <input type="text" class="form-control in_tg" name="end_date_d"
                                                               id="end_date_d"
                                                               autocomplete="off">
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                        Outlet
                                                    </label>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <select class="form-control cmn_select2" name="outlet_id" id="outlet_id">

                                                            <option value="">Select Outlet</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-2 col-sm-offset-2">
                                                        <button id="send" type="button"
                                                                class="btn btn-success btn-block in_tg"
                                                                onclick="getSummaryReport()">Show
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="form-group">

                                                </div>
                                                {{-- <div class="item form-group">
                                                     <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Outlet<span
                                                                 class="required">*</span>
                                                     </label>
                                                     <div class="col-md-4 col-sm-4 col-xs-12">
                                                         <select class="form-control" name="outlet_id" id="outlet_id"
                                                                 onchange="getZone(this.value)">
                                                             <option value="">Select Outlet</option>
                                                             @foreach($region as $regionList)
                                                                 <option value="{{$regionList->id}}">{{$regionList->dirg_code}}
                                                                     - {{$regionList->dirg_name}}</option>
                                                             @endforeach
                                                         </select>
                                                     </div>
                                                 </div>--}}


                                            </div>

                                        </div>
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
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
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
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
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
                                            <th>Line Per Call</th>
                                            <th>Show Location</th>
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
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('sr_activity_hourly_order<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_activity_hourly_order')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        {{--<thead>

                                        <tr class="">
                                            <th>SI</th>
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Zone Name</th>

                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Date</th>
                                            <th>00:01 - 09:00</th>
                                            <th>09:01 - 10:00</th>
                                            <th>10:01 - 11:00</th>
                                            <th>11:01 - 12:00</th>
                                            <th>12:01 - 01:00</th>
                                            <th>01:01 - 02:00</th>
                                            <th>02:01 - 03:00</th>
                                            <th>03:01 - 04:00</th>
                                            <th>04:01 - 05:00</th>
                                            <th>05:01 - 06:00</th>
                                            <th>06:01 - 07:00</th>
                                            <th>07:01 - 08:00</th>
                                            <th>08:01 - 12.00</th>
                                        </tr>
                                        </thead>--}}
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

                                        <button onclick="exportTableToCSV('sr_activity_hourly_order<?php echo date('Y_m_d'); ?>.csv','tableDiv_sr_activity_hourly_visit')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>

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
                                            <th>SR Route Outlet Quantity</th>


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
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
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
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
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
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('class_wise_order_summary_<?php echo date('Y_m_d'); ?>.csv','tableDiv_class_wise_order_summary')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>

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
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('class_wise_order_summary_<?php echo date('Y_m_d'); ?>.csv','tableDiv_class_wise_order_summary_memo')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>

                                        <tbody id="cont_class_wise_order_summary_memo">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
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
            $('#tableDiv_sr_non_productivity').hide();
            $('#tableDiv_sr_summary_by_group').hide();
            $('#tableDiv_sr_activity_hourly_order').hide();
            $('#tableDiv_sr_activity_hourly_visit').hide();
            $('#tableDivd_market_outlet_sr_outlet').hide();
            $('#tableDiv_sr_wise_order_delivery').hide();
            $('#tableDiv_sku_wise_order_delivery').hide();
            $('#tableDiv_class_wise_order_summary').hide();
            $('#tableDiv_class_wise_order_summary_memo').hide();
            $('#tableDiv_traking').hide();
        }
        function testClick(v){
           var emid=$(v).attr('emid');
           var role_name=$(v).attr('role_name');
           var html=role_name+" >";
           var period=$('#period').val();
           var _token = $("#_token").val();
           $('#desig').append(html);
           $('#emid_div').empty();
           var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
           $('#emid_div').append(emid_cnt);
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
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_usnm'] + '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td>' + data[i]['total_visited'] + '</td>' +
                            '<td>' + data[i]['memo'] + '</td>' +                          
                            '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                            "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                            '</tr>';
                        dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+data[i]['aemp_name']+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
                    console.log(error);
                }
           });
        }
        function getDateWiseUserReport(){
           var emid=$('#emid').val();
           var period=$('#period').val();
           var _token = $("#_token").val();
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
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_usnm'] + '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td>' + data[i]['total_visited'] + '</td>' +
                            '<td>' + data[i]['memo'] + '</td>' +                          
                            '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                            "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                            '</tr>';
                        dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+data[i]['aemp_name']+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
                    console.log(error);
                }
           });
        }
        function hideReport(){
            $('#govt_heirarchy').hide();
            $('#sales_heirarchy').hide();
            $('#sr_report').hide();
            $('#outlet_report').hide();
            $('#order_report').hide();
            $('#note_report').hide();
            $('#order_report').hide();
            $('#tracing').hide();
            $('#title_head').hide();
            $('#harch').show();
        }
        hide_me();
        hideReport();

        function getSRReport() {
            hideReport();
            hide_me();
            removeClass();
            $('#sr_report').show();     
            $('#sales_heirarchy').show();
           
           // $('#id_govt').removeAttr('disabled');

        }
        function addClass(){
            $('#rp').removeClass('col-md-6 col-sm-6').addClass('col-md-12 col-sm-12')
            $('#harch').hide();
          //  $('#harch').removeClass('col-md-6 col-sm-6').addClass('col-md-8 col-sm-8')
           // $('#hierarchy').addClass('traking_div_height');
            //$('#rpt').height($("#hierarchy").height());
        }
        function removeClass(){
            $('#rp').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6')
            $('#harch').removeClass('col-md-4 col-sm-4').addClass('col-md-6 col-sm-6')
            $('#hierarchy').removeClass('traking_div_height');
            $('#rpt').height($("#hierarchy").height());
        }
        function getDiggingReport() {
            hideReport();
            hide_me();
            addClass();
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
                    console.log(data);
                    var html="";
                    var count=1;
                    var dp_cnt='';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['aemp_usnm'] + '</td>' +
                            '<td>' + data[i]['aemp_mob1'] + '</td>' +
                            '<td>' + data[i]['total_visited'] + '</td>' +
                            '<td>' + data[i]['memo'] + '</td>' +                          
                            '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                            "<td>" + (data[i]['total_target']/26).toFixed(2) + "</td>" +
                            '</tr>';
                        dp_cnt+='<li><a href="#" onclick="testClick(this)" emid="'+data[i]['oid']+'" role_name="'+data[i]['aemp_name']+'">'+data[i]['aemp_name']+'</a></li>';
                        count++;
                    }
                    $('#cont_traking').empty();
                    $('#all_dp_content').empty();
                    $('#cont_traking').append(html);
                    $('#all_dp_content').append(dp_cnt);
                    $('#rpt').height($("#tableDiv_traking").height()+150);
                },error:function(error){
                    console.log(error);
                }
            });
           // $('#id_govt').removeAttr('disabled');

        }
        
        // function getUserWiseReport() {
        //     var period=$('#period').val();
        //     var role_id=$('#role_id').val();
        //     var _token = $("#_token").val();
        //     //$('#ajax_load').css("display", "block");
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ URL::to('/')}}/getUserWiseReport",
        //         data: {
        //             role_id: role_id,
        //             period: period,
        //             _token: _token
        //         },
        //         cache: false,
        //         dataType: "json",
        //         success: function (data) {
        //             $('#ajax_load').css("display", "none");
        //             var html = '<option value="">Select</option>';
        //             for (var i = 0; i < data.length; i++) {
        //                 console.log(data[i]);
        //                 html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
        //             }

        //             $("#sales_group_id").append(html);

        //         }
        //     });
        // }
        

        function getOutletReport() {
            hideReport();
            hide_me();
            removeClass();
            $('#outlet_report').show();
            $('#govt_heirarchy').show();


        }

        function getOrderReport() {
            hideReport();
            hide_me();
            removeClass();
            $('#order_report').show();
            $('#sales_heirarchy').show();   

        }

        function getNoteReport() {
            hideReport();
            hide_me();
            removeClass();
            $('#note_report').show();

        }
        function getDeviaitonReport() {
            hideReport();
            hide_me();
            removeClass();
            $('#deviation_report').show();

        }

        // function getSalesHierarchy() {
        //     $('#govt_heirarchy').hide();
        //     $('#sales_heirarchy').show();
        // }

        // function getGovtHierarchy() {
        //     $('#govt_heirarchy').show();
        //     $('#sales_heirarchy').hide();
        // }


        $("input[name='reportType']").on("change", function () {
            $('#outletType').hide();
            $('#srType').hide();

            var reportType = this.value;
            //alert(reportType);
            if (reportType == "sr_activity_hourly_order") {
            //.attr('disabled','disabled');
                $('#outletType').show();
                $('#end_date').attr('disabled','disabled');
                $('#outletAssetType').show();
                $('#sales_heirarchy').show();
                $('#govt_heirarchy').hide();
            }else if ((reportType == "sr_activity_hourly_visit")){

                $('#end_date').attr('disabled','disabled');
            }else{
                $('#end_date').removeAttr('disabled');
            }

            if (reportType == "d_outlet") {
                $('#srType').show();
                $('#outletAssetType').show();

                $('#govt_heirarchy').show();
                $('#sales_heirarchy').hide();
            }

            //alert(this.value);
            //alert(this.value);
        });

        $("input[name='outletTypev']").on("change", function () {
            $('#outletAssetType').hide();
            var outletType = this.value;

            if (outletType == "olt") {
                $('#outletAssetType').show();
            }

        });

        function getGroup(slgp_id) {
            clearDate();
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

                    $("#sales_group_id").empty();
                    $('#sales_group_id_gvt').empty();

                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#sales_group_id").append(html);
                    $("#sales_group_id_gvt").append(html);

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


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Zone</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].zone_code + " - " + data[i].zone_name + '</option>';
                    }
                    $("#zone_id").append(html);

                }
            });
        }

        function getSR(zone_id) {
            clearDate();
            var sales_group_id = $('#sales_group_id').val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getSR",
                data: {
                    zone_id: zone_id,
                    sales_group_id: sales_group_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#sr_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select SR</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                    }
                    $("#sr_id").append(html);

                }
            });
        }

        function loadOutlet() {
            var _token = $("#_token").val();
            var market_id = $("#market_id").val();
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
                    console.log(data);
                    console.log(data);
                    $('#outlet_id').empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].site_code + " - " + data[i].site_name + '</option>';
                    }
                    $('#outlet_id').append(html);

                }
            });

        }

        function getThanaBelogToDistrict() {
            clearDate();
            var district_id = $('#dist_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/market_open/thana_list",
                data: {
                    district_id: district_id
                },

                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#than_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].than_name + '</option>';
                    }
                    $("#than_id").append(html);

                }

            });
        }


        function getWardNameBelogToThana() {
            clearDate();
            var thana_id = $('#than_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/market_open/word_list",
                data: {
                    thana_id: thana_id
                },

                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#ward_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].ward_name + '</option>';
                    }
                    $("#ward_id").append(html);

                }
            });
        }

        function loadWardMarket() {
            clearDate();
            var ward_id = $('#ward_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/get/ward_wise/market_list",
                data: {
                    ward_id: ward_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#market_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].market_name + '</option>';
                    }
                    $("#market_id").append(html);

                }

            });

        }


        function getSummaryReport() {
            hide_me();
            var reportType = $("input[name='reportType']:checked").val();
            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            if(sales_group_id ==''){
                sales_group_id = $('#sales_group_id_gvt').val(); 
            }
            var region_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var sr_id = $('#sr_id').val();
            var outlet_id = $('#outlet_id').val();
            var dist_id = $('#dist_id').val();
            var than_id = $('#than_id').val();
            var ward_id = $('#ward_id').val();
            var market_id = $('#market_id').val();
            var start_date_d = $('#start_date_d').val();
            var end_date_d = $('#end_date_d').val();
            var _token = $("#_token").val();
            console.log(reportType);
            if(reportType===undefined){
                return confirm('Please select report');
            }
            if(reportType=='sr_activity' || reportType=='sr_productivity' || reportType=='sr_non_productivity' || reportType=='sr_summary_by_group' || reportType=='class_wise_order_report_amt'||reportType=='class_wise_order_report_memo'||reportType=='sr_wise_order_delivery'){
                if(acmp_id==''){
                    return confirm('Please select company');
                }
                if(start_date =='' || end_date ==''){
                    return confirm('Please select date');
                }
            }
            if(reportType=='sr_activity_hourly_visit' || reportType=='sr_activity_hourly_order' || reportType=='sku_wise_order_delivery'){
                if(acmp_id==''){
                    return confirm('Please select company');
                }
                if(sales_group_id ==''){
                    return confirm('Please select group');
                }
                if(start_date ==''){
                    return confirm('Please select start date');
                }
            }
            if(reportType =='market_outlet_sr_outlet'){
                    acmp_id=$('#acmp_id_d').val();
                    if(acmp_id==''){
                        return confirm('Please select company');
                    }
                   if(sales_group_id==''){
                       return confirm('Please select group');
                   }
                   if(dist_id==''){
                       return confirm('Please select district');
                   }
                   if(than_id==''){
                       return confirm('Please select thana');
                   }
                   if(start_date_d==''){
                       return confirm('Please select start date');
                   }
                   
                }
            

            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/filter/common_sr_activity_filter/demo2",
                data: {
                    reportType: reportType,

                    acmp_id: acmp_id,
                    zone_id: zone_id,
                    region_id: region_id,
                    sales_group_id: sales_group_id,
                    start_date: start_date,
                    end_date: end_date,
                    sr_id: sr_id,
                    outlet_id: outlet_id,
                    dist_id: dist_id,
                    than_id: than_id,
                    ward_id: ward_id,
                    market_id: market_id,
                    start_date_d: start_date_d,
                    end_date_d: end_date_d,


                    _token: _token
                },

                cache: false,
                dataType: "json",

                success: function (data) {
                    //alert(data);
                    //console.log(data);
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    if (reportType == "sr_activity") {

                        for (var i = 0; i < data.length; i++) {

                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['date'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
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
                                "<td>" + (data[i]['c_outlet'] / (data[i]['p_sr'])).toFixed(2) + "</td>" +
                                "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                '</tr>';
                            count++;
                        }
                        $("#cont").empty();
                        $("#cont").append(html);
                        $('#tableDiv').show();
                    }else if (reportType == "sr_productivity") {
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
                                '<td>' + "Location" + '</td>' +
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
                       // alert("hourly order");
                        count = 0;
                        var dim =data[0].length;
                        for (var i = 0; i < data.length; i++) {
                            if (i == 0){
                                html += '<tr class="tbl_header">' + '<td>' + "###" + '</td>' ;
                            }else{
                                html += '<tr>' + '<td>' + count + '</td>' ;
                            }

                            for(var j = 0; j < data[0].length; j++){
                                html +='<td>' + data[i][j] + '</td>';
                            }
                            //alert(data[i][0]);

                            html += '</tr>';
                            count++;
                        }

                        $("#cont_sr_activity_hourly_order").empty();
                        $("#cont_sr_activity_hourly_order").append(html);
                        $('#tableDiv_sr_activity_hourly_order').show();
                    }else if (reportType == "sr_activity_hourly_visit"){

                        var dim =data[0].length;
                        for (var i = 0; i < data.length; i++) {
                            if (i == 0){
                                html += '<tr class="tbl_header">' + '<td>' + "###" + '</td>' ;
                            }else{
                                html += '<tr>' + '<td>' + count + '</td>' ;
                            }

                            for(var j = 0; j < data[0].length; j++){
                                html +='<td>' + data[i][j] + '</td>';
                            }
                            //alert(data[i][0]);

                            html += '</tr>';
                            count++;
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
                                '<td>' + data[i]['acmp_name'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['dirg_name'] + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td>' + data[i]['ordd_amt'] + '</td>' +
                                '<td>' + data[i]['deli_amt'] + '</td>' +


                                '</tr>';
                            count++;
                        }

                        $("#cont_sr_wise_order_delivery").empty();
                        $("#cont_sr_wise_order_delivery").append(html);
                        $('#tableDiv_sr_wise_order_delivery').show();
                       // alert("imu")tableDiv_sr_wise_order_delivery
                    }else if (reportType == "sku_wise_order_delivery"){
                        $('#tableDiv_sku_wise_order_delivery').show();
                        for (var i = 0; i < data.length; i++) {

                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['ordm_date'] + '</td>' +
                                '<td>' + data[i]['acmp_name'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['dirg_name'] + '</td>' +
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
                        count = 0;
                        var dim =data[0].length;
                        for (var i = 0; i < data.length; i++) {
                            if (i == 0){
                                html += '<tr class="tbl_header">' + '<td>' + "###" + '</td>' ;
                            }else{
                                html += '<tr>' + '<td>' + count + '</td>' ;
                            }

                            for(var j = 0; j < data[0].length; j++){
                                html +='<td>' + data[i][j] + '</td>';
                            }
                            //alert(data[i][0]);

                            html += '</tr>';
                            count++;
                        }

                        $("#cont_class_wise_order_summary").empty();
                        $("#cont_class_wise_order_summary").append(html);
                        $('#tableDiv_class_wise_order_summary').show();
                    }else{
                        count = 0;
                        var dim =data[0].length;
                        for (var i = 0; i < data.length; i++) {
                            if (i == 0){
                                html += '<tr class="tbl_header">' + '<td>' + "###" + '</td>' ;
                            }else{
                                html += '<tr>' + '<td>' + count + '</td>' ;
                            }

                            for(var j = 0; j < data[0].length; j++){
                                html +='<td>' + data[i][j] + '</td>';
                            }
                            //alert(data[i][0]);

                            html += '</tr>';
                            count++;
                        }
                        $("#cont_class_wise_order_summary_memo").empty();
                        $("#cont_class_wise_order_summary_memo").append(html);
                        $('#tableDiv_class_wise_order_summary_memo').show();
                    }


                    $('#ajax_load').css("display", "none");

                },error:function(error){
                    console.log(error);
                }

            });

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

        $(document).ready(function () {
            $('#start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: '-3m',
                maxDate: new Date(),
                autoclose: 1,
                showOnFocus: true
            });

            // $("select").select2({width: 'resolve'});
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

        $("#end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        //Functions for the date of Distric thana wise section
        $(document).ready(function () {
            $('#start_date_d').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: '-3m',
                maxDate: new Date(),
                autoclose: 1,
                showOnFocus: true
            });

            // $("select").select2({width: 'resolve'});
        });

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

        $("#end_date_d").datepicker({
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


        /*$("#send1").on('click', function () {
            $("#send1").addClass('selected');
        });

        $("#send2").on('click', function () {
            $("#send2").addClass('selected');
        });

        $("#send3").on('click', function () {
            $("#send3").addClass('selected');
        });

        $("#id_govt").on('click', function () {
            $("#id_govt").addClass('selected');
            $('id_sales').removeClass('selected');
        });
        $("#id_sales").on('click', function () {
            $("#id_sales").addClass('selected');
            $('id_govt').removeClass('selected');
        });*/
        //id_govt

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


    </style>

@endsection