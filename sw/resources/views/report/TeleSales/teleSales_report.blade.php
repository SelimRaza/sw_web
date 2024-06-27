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
                            action="{{url ('load/filter/common_sr_activity_filter/demo2')}}" method="post"
                            enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="email_address" id="email_address"
                                value="<?php echo Auth::user()->employee()->aemp_emal; ?>">
                            {{csrf_field()}}
                            <div class="col-md-6 col-sm-6 col-xs-12" id="rp">
                                <div class="item form-group rp_type_div" id="rpt">
                                    <div class="x_title">
                                        <div class="btnDiv col-md-12 col-sm-12 col-xs-12" style="margin-bottom:5px;">
                                            <div id="exTab1" class="container">
                                                <ul class="nav nav-pills"
                                                    style="color:black !important; font-weight:bold;">
                                                    <li>
                                                        <a href="#1a" data-toggle="tab" onclick="getTeleSalesReport()">TeleSales </a>
                                                    </li>
                                                    <li>
                                                        <a href="#2a" data-toggle="tab" onclick="getOutletReport()"> Outlet</a>
                                                    </li>
{{--                                                    <li><a href="#2a" data-toggle="tab" onclick="getOrderReport()">--}}
{{--                                                            Order</a>--}}
{{--                                                    </li>--}}
                                                    <!-- <li><a href="#2a" data-toggle="tab" onclick="getOutletReport()">
                                                            Outlet</a>
                                                    </li>
                                                    <li><a href="#3a" data-toggle="tab" onclick="getOrderReport()">
                                                            Order</a>
                                                    </li>
                                                    <li><a href="#4a" data-toggle="tab" onclick="getDeviationReport()">
                                                            Deviation</a>
                                                    <li><a href="#4a" data-toggle="tab" onclick="getNoteReport()">
                                                            Activity</a>
                                                    </li>

                                                    <li><a href="#4a" data-toggle="tab"
                                                            onclick="getEmpTrackingReport()">Monitoring</a>
                                                    </li> -->
                                                </ul>

                                            </div>

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12"
                                        style="font-size: 11px; height: 115px; border-radius:10%;"
                                        id="rpt_selection_div">
                                        <div id="TeleSalesReport"
                                            class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn rpt_div_hide">
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="sr_productivity" /> SR Productivity
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="sr_hourly_activity" /> SR Hourly Activity
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="Tele_NonProductive_Reason_Summary" /> NP Reason Summary
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="order_report" /> Order
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="np_reason_chart" /> NP Reason Graph
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="np_summary" /> NP Summary
                                                </label>
                                            </div>

                                            <!-- <div class="col-md-6 col-sm-6 ">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="sr_productivity" /> Productivity
                                                </label>
                                            </div> -->

                                        </div>
                                        <div id="OutletReport"
                                            class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn rpt_div_hide">
{{--                                            <div class="col-md-6 col-sm-6">--}}
{{--                                                <label>--}}
{{--                                                    <input type="radio" class="flat" name="reportType" id="reportType"--}}
{{--                                                        value="weekly_outlet_summary" /> Outlet Weekly Summary--}}
{{--                                                </label>--}}
{{--                                            </div>--}}
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                           value="non_productive_reason_outlet_summary" /> NP Reason Outlet Summary
                                                </label>
                                            </div>
                                       </div>
                                       <div id="OrdertReport"
                                            class="col-md-12 col-sm-12 col-xs-12 animate__animated animate__zoomIn rpt_div_hide">
                                            <div class="col-md-6 col-sm-6">
                                                <label>
                                                    <input type="radio" class="flat" name="reportType" id="reportType"
                                                        value="item_summary" /> Item Summary
                                                </label>
                                            </div>                                        
                                       </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6  col-xs-12" style="font-size: 12px; ">
                                <div class="item form-group rp_type_div " id="hierarchy">
                                    <div class="col-md-12 col-sm-12" style="height: 263px;">
                                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn" style="margin-top:15px;">
                                            <div class="form-group col-md-6">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="acmp_id">Company<span class="required">*</span>
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
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="sales_group_id">Group<span class="required"></span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="sales_group_id"
                                                        id="sales_group_id" onchange="getSR()">

                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 " id="dirg_id_div">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="dirg_id">Region/State<span class="required"></span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="dirg_id" id="dirg_id"
                                                        onchange="getZone(this.value)">
                                                        <option value="">Select Region</option>
                                                        @foreach($region as $regionList)
                                                        <option value="{{$regionList->id}}">{{$regionList->dirg_code}}
                                                            - {{$regionList->dirg_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 zone_div">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="zone_id">Zone<span class="required"></span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="zone_id"
                                                        id="zone_id" onchange="getSR()">
>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 start_date_div" style="display:none;"
                                                id="single_start_date_div">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date">Start Date<span class="required">*</span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg start_date"
                                                        name="start_date" id="start_date"
                                                        value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 start_date_div" style="display:none;">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date">End Date<span class="required">*</span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg start_date"
                                                        name="end_date" id="end_date"
                                                        value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 start_date_period_div">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date_period">Select Period<span class="required">*</span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="start_date_period"
                                                        id="start_date_period" onchange="showCustomDate(this.value)">


                                                    </select>

                                                </div>

                                            </div>
                                            <div class="form-group col-md-6 year_mnth">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date_period">Select Period<span class="required">*</span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg"
                                                        name="year_mnth" id="year_mnth"
                                                        value="<?php echo date('Y-m'); ?>" autocomplete="off" />

                                                </div>

                                            </div>
                                            <div class="form-group col-md-6 sr_id">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date_period">Select SR<span class="required"></span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="sr_id"
                                                        id="sr_id">
                                                    </select>

                                                </div>

                                            </div>
                                            <div class="form-group col-md-6 is_details">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for="start_date_period"><span class="required"></span>
                                                </label>
                                                <label>
                                                    <input type="radio" class="flat" name="is_details" id="reportType"
                                                        value="1" checked /> Details  
                                                </label>
                                                <label>
                                                    <input type="radio" class="flat" name="is_details" id="reportType"
                                                        value="0" />&nbsp; Summary
                                                </label>
                                            </div>
                                            <div class="form-group col-md-6">
                                                
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                    for=""><span class="required"></span>
                                                </label>
                                                    <div
                                                        class="col-md-8 col-sm-8 col-xs-12">
                                                        <button id="send" type="button"
                                                            class="btn btn-success  btn-block in_tg"
                                                            onclick="getSummaryReport()">Show
                                                        </button>
                                                    </div>
                                                <!-- <div class="form-group col-md-12 col-sm-12 col-xs-12"
                                                    style="margin-top: 15px;">
                                                    <div class="pull-right" style="margin-right:5%; margin-top:2%;">
                                                        <a href="#" onclick="getRequestedReportList()"
                                                            class="request_report_check">Click here to see requested
                                                            report status</a>
                                                    </div>
                                                </div> -->


                                            </div>

                                        </div>
                                    </div>
                                    <hr />
                                </div>

                            </div>

                            <div class="col-md-6 col-sm-6  col-xs-12" style="font-size: 12px;display: none" id="np_reason_filter_div">

                                <div class="item form-group rp_type_div">
                                    <div class="col-md-12 col-sm-12" style="height: 263px;">
                                        <div class="form-row animate__animated animate__zoomIn" style="margin-top:15px;">

                                            <div class="form-group col-md-6 " id="np-reason">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                       for="nonpro-id">NP Reason
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="nopr_id" id="nonpro-id">
                                                        <option value="">Select Productive Reason</option>
                                                        @foreach($np_reasons as $type)
                                                            <option value="{{$type->id}}">{{$type->nopr_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6"
                                                 id="np_start_date_div">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                       for="start_date">Start Date<span class="required">*</span>
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg date"
                                                           name="start_date" id="start-date"
                                                           value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" style="text-align: left; padding-left: 6px;"
                                                           for="end_date">End Date
                                                    </label>
                                                    <div class="col-md-8 col-sm-8 col-xs-12" style="padding-left: 3px">
                                                        <input type="text" class="form-control in_tg date"
                                                               name="end_date" id="end-date"
                                                               value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-4" style="float: right">
                                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                           for=""><span class="required"></span>
                                                    </label>

                                                    <button id="send" type="button"
                                                            class="btn btn-success btn-block in_tg"
                                                            onclick="getNPReasonOutletReport()">Show
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>

                </div>
                <!-- Dynamic table start -->

                <div id="tbl_dynamic" class="div_hide">
                    <div class="x_panel">

                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;">
                                <!--
                                <div align="right" id="export_option_div" style="display: none">
                                    <a onclick="exportTableToExcel(this,'sr_route_outlet<?php echo date('Y_m_d'); ?>.xls','tbl_note_report')"
                                        class="btn btn-warning" id="export_option_btn">Export
                                    </a>
                                </div>-->
                                <div align="right" id="export-image" style="display: none">
                                    <a id="export-image-btn" onclick="exportTableToExcel(this,'sr_route_outlet<?php echo date('Y_m_d'); ?>.xls','tbl_note_report')"
                                        class="btn btn-warning" id="export_option_btn">Export
                                    </a>
                                </div>
                                <table id="tl_dynamic" class="table table-bordered table-responsive" border="1"
                                    style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                    data-page-length='100'>
                                    <thead id="tl_dynamic_head" class="tbl_header" style="position:sticky; inset-block-start:0;">

                                    </thead>
                                    <tbody id="tl_dynamic_cont">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div id="line_chart" class="div_hide">
                    <div class="x_panel">

                        <div class="x_content" style="width:800px; height: 500px;">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <canvas width="800" height="400" id="np_reason_graph"></canvas>

                                
                            </div>
                            
                        </div>
                    </div>
                </div> -->
                <div id="line_chart" class="div_hide">
                    <div class="x_panel">
                        <div class="x_content" style="width: 80%; height:300px;margin-bottom:10%!important;">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <canvas width="1000" height="300" id="np_reason_graph"></canvas>
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
                                <table id="datatablesa" class="table table-bordered table-responsive" border="1"
                                    style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
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
                    <thead style="position:sticky; inset-block-start:0;" class="tbl_head" >
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
 <!-- NP Reason Details Modal -->
 <div class="modal fade" id="np_details" role="dialog">
    <div class="modal-dialog" style="width:80%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center" id="modalCat">NP Reason Details</h4>
                <button onclick="exportTableToCSV('np_reason_details<?php echo date('Y_m_d'); ?>.csv','np_details')"
                    class="btn btn-warning" id="exp_btn">Export
                </button>
            </div>
            <div class="modal-body" style="max-height:400px;overflow:auto;">
                <div class="loader" id="np_details_load" style="display:none; margin-left:35%;"></div>
                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>DATE</th>
                            <th>OUTLET CODE</th>
                            <th>OUTLET NAME</th>
                            <th>OUTLET MOBILE</th>
                            <th>ZONE CODE</th>
                            <th>ZONE NAME</th>
                            <th>OUTLET TYPE</th>
                            <th>CALL RECORD</th>
                        </tr>
                    </thead>
                    <tbody id="np_details_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
 <!-- NP Reason Details Modal -->

 </div> 

 <!-- <script src="{{ asset("theme/vendors/Chart.js/dist/Chart.min.js")}}"></script>                                 -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
 <script src="{{ asset("theme/vendors/js/html2canvas.min.js") }}"></script>
<script type="text/javascript">
$('.modal-backdrop').remove()
$(document.body).removeClass("modal-open");
$('.cmn_select2').select2();
$('.cmn_select2').css('style', 'color:black !important');
$SIDEBAR_MENU = $('#sidebar-menu')
$(document).ready(function() {
    setTimeout(function() {
        $('#menu_toggle').click();
    }, 1);
});
$('#rpt').height($("#hierarchy").height());
$('.div_hide').hide();

$('input[name="reportType"]').change(function() {
    let selectedReport = $(this).val();
    if(selectedReport === 'non_productive_reason_outlet_summary'){
        $('#hierarchy').hide()
        $('#np_reason_filter_div').show()
    }else{
        $('#hierarchy').show()
        $('#np_reason_filter_div').hide()
    }
})

function hide_me() {
    $('.div_hide').hide();
}
function reportTypeHide(){  
    $('.rpt_div_hide').hide();
    $('.sr_id').hide();
    $('.is_details').hide();
}
function hideReport() {
    $('#govt_heirarchy').hide();
    $('#tracing_gvt').hide();
    $('#sales_heirarchy').hide();
    $('#history').hide();
   
}
reportTypeHide();
function getTeleSalesReport() {
    reportTypeHide();
    customDateHide();
    $('#TeleSalesReport').show();
    $('#sales_heirarchy').show();
}
function getOutletReport() {
    reportTypeHide();
    customDateHide();
    $('#OutletReport').show();
    $('#sales_heirarchy').show();   
}
function getOrderReport() {
    reportTypeHide();
    customDateHide();
    $('#OrdertReport').show();
    $('#sales_heirarchy').show();   
}




function hideShow() {
    $('#start_date_period_div').show();
    $('#single_start_date_div').hide();
}

function showCustomDate(value) {
    hideShow();
    if(value==''){
        $('.start_date_div').show();
        $('.start_date_period_div').hide();
    }    
}

function getGroup(slgp_id, place) {
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
        success: function(data) {
            $('#ajax_load').css("display", "none");
            var html = '<option value="">Select</option>';
            var gvt_slgp = '<option value="">Select</option>' +
                '<option value="0">ALL</option>';
            for (var i = 0; i < data.length; i++) {
                console.log(data[i]);
                html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i]
                    .slgp_name + '</option>';
                gvt_slgp += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i]
                    .slgp_name + '</option>';
            }
            if (place == 1) {
                $("#sales_group_id").empty();
                $("#sales_group_id").append(html);
            } else if (place == 2) {
                $("#sales_group_id_h").empty();
                $("#sales_group_id_h").append(html);
            } else if (place == 3) {
                $('#sh_slgp_id').empty();
                $('#sh_slgp_id').append(gvt_slgp);
            }
        }
    });
}

function getZone(dirg_id) {
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
        success: function(data) {
            $("#zone_id").empty();
            $("#zone_id1").empty();
            $('#ajax_load').css("display", "none");
            var html = '<option value="">Select Zone</option>';
            for (var i = 0; i < data.length; i++) {
                html += '<option value="' + data[i].id + '">' + data[i].zone_code + " - " + data[i]
                    .zone_name + '</option>';
            }
            $("#zone_id").append(html);
            $("#zone_id1").append(html);
        }
    });
}

function getSR() {
    var _token = $("#_token").val();
    var rpt_type = $("input[name='reportType']:checked").val();
    var dirg_id = $('#dirg_id').val();
    var sales_group_id = $('#sales_group_id').val();
    var zone_id = $('#zone_id').val();
    if(rpt_type=='weekly_outlet_summary'){
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{ URL::to('/')}}/report/getSRListModuleTwo",
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
                var html = '<option value="0">ALL</option>';
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                }
                $("#sr_id").append(html);

            },
            error: function(error) {
                $('#ajax_load').css("display", "none");
                //console.log(error)
            }
        });
    }
    
}

// Append data To Table
function emptyContentAndAppendData(head,content){
    $('#tl_dynamic_head').empty();
    $('#tl_dynamic_cont').empty();
    $('#tl_dynamic_head').append(head);
    $('#tl_dynamic_cont').append(content);
    $('#tbl_dynamic').show();
    $('#tbl_dynamic').excelTableFilter();
    $('.dropdown-filter-item').css('color', 'black');
    $('.dropdown-filter-dropdown').css({'margin-top': '3px', 'height': '23px', 'padding': '0px', 'gap': '1px' });
    $('.arrow-down').css('display', 'none');
    $('.dropdown-filter-icon').css('border', '1px solid white');
    $('th').css({'vertical-align': 'top', 'white-space': 'nowrap', 'text-overflow': 'ellipsis', 'width': '100% !important', 'padding': '2px 10px'});

}
function getNPDetails(obj){
    $('#np_details').modal({backdrop: false});
    $('#np_details').modal('show');
    let aemp_id=$(obj).attr('aemp_id');
    let nopr_id=$(obj).attr('nopr_id');
    let start_date=$(obj).attr('start_date');
    let end_date=$(obj).attr('end_date');
    var _token = $("#_token").val();
    $.ajax({
        type: "POST",
        url: "{{ URL::to('/')}}/getNPDetails",
        data: {
            _token: _token,
            aemp_id:aemp_id,
            nopr_id:nopr_id,
            start_date:start_date,
            end_date:end_date
                        
        },
        cache: false,
        dataType: "json",
        success: function(data) {
            var html='';
            var count=1;
            let file='';
            for(var i=0;i<data.length;i++){
                file="https://images.sihirbox.com/"+data[i].rcds_file;
                html+='<tr><td>'+count+'</td>'+
                        '<td>'+data[i].npro_date+'</td>'+
                        '<td>'+data[i].site_code+'</td>'+
                        '<td>'+data[i].site_name+'</td>'+
                        '<td>'+data[i].site_mob1+'</td>'+
                        '<td>'+data[i].zone_code+'</td>'+
                        '<td>'+data[i].zone_name+'</td>'+
                        '<td>'+data[i].site_type+'</td>'+
                        '<td><audio controls><source src="'+file+'" type="audio/mpeg"><audio></td>'+
                        '</tr>';
                count++;
            }
            $('#np_details_body').html(html);
        },
        error:function(error){
            console.log(error);
        }
    });
}
function getRandomColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}
function getSummaryReport() {
    hide_me();
    var reportType = $("input[name='reportType']:checked").val();
    var is_details = $("input[name='is_details']:checked").val();
    var acmp_id = $('#acmp_id').val();
    var dirg_id = $('#dirg_id').val();
    var sales_group_id = $('#sales_group_id').val();
    var zone_id = $('#zone_id').val();
    var time_period = $('#start_date_period').val()
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var year_mnth = $('#year_mnth').val();
    var sr_id = $('#sr_id').val();
    if (reportType === undefined) {
        alert('Please select report');
        return false;
    }
    else if (reportType == '') {
        alert('Please select report');
        return false;
    }
    if(!acmp_id){
        alert('Please select company');
        return false;
    }
    if(!time_period && !start_date){
        alert('Please select date');
        return false;
    }
    var _token = $("#_token").val();
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "{{ URL::to('/')}}/report/tele_sales",
        data: {
            slgp_id: sales_group_id,
            acmp_id: acmp_id,
            dirg_id: dirg_id,
            zone_id: zone_id,
            time_period: time_period,
            start_date: start_date,
            end_date: end_date,
            year_mnth: year_mnth,
            is_details: is_details,
            sr_id: sr_id,
            reportType: reportType,
            _token: _token
        },
        cache: false,
        dataType: "json",
        success: function(result) {
            $('#export-image').hide();
            $('#ajax_load').css("display", "none");
            var count=1;
            var head='';
            var html='';
            switch(reportType){
                case "Tele_NonProductive_Reason_Summary":
                    var reasons='';
                    var data=result.data;
                    var nopr_list=result.nopr_list;
                    if(is_details==1){
                        head='<tr><th>SL</th><th>DATE</th><th> GROUP</th><th>SR ID</th><th>SR NAME</th>'+
                            '<th>NP VISIT</th>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr><td>'+count+'</td>'+
                                    '<td>'+data[i].npro_date+'</td>'+
                                    '<td>'+data[i].slgp_name+'</td>'+
                                    '<td>'+data[i].aemp_usnm+'</td>'+
                                    '<td>'+data[i].aemp_name+'</td>'+
                                    '<td>'+data[i].npro_olt+'</td>';
                            for(var j=0;j<nopr_list.length;j++){
                                if(i==0){
                                    head+='<th>'+nopr_list[j].nopr_name+'</th>';
                                }
                                
                                html+='<td>'+data[i][nopr_list[j].nopr_name]+'<i class="fa fa-eye" style="float:right !important; cursor:pointer" aemp_id="'+data[i].aemp_id+'" start_date="'+result.start_date+'" end_date="'+result.end_date+'" nopr_id="'+nopr_list[j].id+'" onclick="getNPDetails(this)"></i></td>';
                            }
                            html+='</tr>';
                            count++;
                                
                        }
                        head+='</tr>';
                    }
                    else{
                        head='<tr><th>SL</th><th>GROUP</th><th>SR ID</th><th>SR NAME</th>'+
                            '<th>NP VISIT</th>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr><td>'+count+'</td>'+
                                    '<td>'+data[i].slgp_name+'</td>'+
                                    '<td>'+data[i].aemp_usnm+'</td>'+
                                    '<td>'+data[i].aemp_name+'</td>'+
                                    '<td>'+data[i].npro_olt+'</td>';
                            for(var j=0;j<nopr_list.length;j++){
                                if(i==0){
                                    head+='<th>'+nopr_list[j].nopr_name+'</th>';
                                }
                                
                              //  html+='<td>'+data[i][nopr_list[j].nopr_name]+'</td>';
                              html+='<td>'+data[i][nopr_list[j].nopr_name]+'<i class="fa fa-eye" style="float:right !important; cursor:pointer" aemp_id="'+data[i].aemp_id+'" start_date="'+result.start_date+'" end_date="'+result.end_date+'" nopr_id="'+nopr_list[j].id+'" onclick="getNPDetails(this)"></i></td>';
                            }
                            html+='</tr>';
                            count++;
                                
                        }
                        head+='</tr>';
                    }
                                        
                    emptyContentAndAppendData(head,html);
                    $('#export_option_btn').removeAttr('onclick');
                    $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"NonProductive_Reason_Summary<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;
                case "np_reason_chart":
                        var myChart = Chart.getChart("np_reason_graph");
                        if (myChart) {
                            myChart.destroy();
                        }
                    // 2nd Options
                    var chartData = {
                        labels: ['9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm', '8pm', '9pm'],
                        datasets: []
                    };
                
                    for (var i = 0; i < result.data.length; i++) {
                        var noprName = result.data[i].nopr_name;
                        var chartColor = getRandomColor();
                        var chartDataPoints = [];
                
                        for (var key in result.data[i]) {
                        if (key !== 'nopr_name') {
                            chartDataPoints.push(result.data[i][key]);
                        }
                        }
                
                        chartData.datasets.push({
                        label: noprName,
                        data: chartDataPoints,
                        backgroundColor: chartColor,
                        borderColor: chartColor,
                        fill: false
                        });
                
                        // Add the legend to the right side of the chart
                        // var legendItem = '<div class="legend-item"><div class="legend-color" style="background-color: ' + chartColor + '"></div><div class="legend-label">' + noprName + '</div></div>';
                        // $('#legend').append(legendItem);
                    }
                
                    // Render the chart
                    var ctx = $('#np_reason_graph')[0].getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: chartData,
                        options: {
                        legend: {
                            display: false,
                            
                        },
                        elements: {
                            line: {
                                tension: 0.4 // adjust this value to create more or less curve between points
                            }
                        },
                        // scales: {
                        //     xAxes: [{
                        //     gridLines: {
                        //         display: false
                        //     }
                        //     }],
                        //     yAxes: [{
                        //     gridLines: {
                        //         display: true
                        //     }
                        //     }]
                        // }
                        }
                    });

                   

                $("#line_chart").show();



                    break;
                case "sr_hourly_activity":
                    var data=result;
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
                            head=head+sub_head;
                            emptyContentAndAppendData(head,html);
                            $('#export_option_btn').removeAttr('onclick');
                            $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"teleSales_sr_hourly_activity<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;               
                case "sr_productivity":
                    var data=result.data;
                    var summary=result.summary;
                    if(is_details==1){
                        head='<th>Date</th>'+
                            '<th>Zone Name</th>'+
                            '<th>Base Name</th>'+
                            '<th>SV Name</th>'+
                            '<th>SR ID</th>'+
                            '<th>SR Name</th>'+
                            '<th>SR Mobile</th>'+
                            '<th>Route Name</th>'+
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
                            '<th>LPC</th>'+
                            '<th>In Time</th>'+
                            '<th>First Visit</th>'+
                            '<th>Last Visit</th>'+
                            '<th>Work Time</th>';
                        
                        for (var i = 0; i < data.length; i++) {
                            var c_percent= data[i]['c_percentage']>100?100: data[i]['c_percentage'];
                            let color="";
                            if(c_percent<40){
                                color="color:red;";
                            }else{
                                color="";
                            }
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
                    else{
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
                            if(visit_percent<40){
                                color="color:red;";
                            }else{
                                color=" ";
                            }
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
                    emptyContentAndAppendData(head,html);
                    $('#export_option_btn').removeAttr('onclick');
                    $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"teleSales_sr_productivity<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;
                case "np_summary":
                    // $('#export-image').show();

                    data=result.data;
                    html='';
                    head='';
                    if(is_details === '1'){
                        let managers    = result.managers;
                        let users       = result.users;
                        let data        = result.data;

                        head='<tr><th>SM NAME</th>'+
                            '<th>SR NAME</th>'+
                            '<th>DETAILS STATUS</th>' +
                            '<th>QUANTITY</th>' +
                            '<th>PERCENT</th>'+
                            '</tr>';

                        let total_user_row = Object.values(data).reduce((val, index) => val+index.length+1, 0)
                        let total_users = Object.keys(data).length
                        let manager_row = total_user_row + total_users + 1
                        let user_row = total_user_row / total_users

                        for(let i=0;i<managers.length;i++){
                            html =`<tr style="font-weight: bold">
                                <td style="padding: 3px 5px !important;" rowspan="${manager_row}">${managers[i]}</td>
                            </tr>`

                            for(let j=0;j<users.length;j++){
                                html += `<tr style="font-weight: bold">
                                   <td style="padding: 3px 5px !important;" rowspan="${user_row}">${users[j].aemp_usnm} - ${users[j].aemp_name}</td>
                                </tr>`

                                let user_id = users[j].id
                                var user_info = data[user_id];

                                let total_quantity = user_info.reduce((total,user) => total+user.QUANTITY, 0 )

                                for(let k=0;k<user_info.length;k++){
                                    let amount = (k === user_info.length-1) ?  `(${Number(user_info[k].ORDER_AMNT)})` : ''
                                    let percent = ((Number(user_info[k].QUANTITY)/total_quantity)*100).toFixed(2)
                                    let percent_text = percent > 0 ? `${percent}%` : '0%'
                                    html += `<tr>
                                       <td style="padding: 3px 5px !important;">${user_info[k].VISIT_STATUS}</td>
                                       <td style="padding: 3px 5px !important;">${user_info[k].QUANTITY} ${amount}</td>
                                       <td style="padding: 3px 5px !important;">${percent_text}</td>
                                    </tr>`
                                }

                                html += `<tr style="font-weight: bold; color: red">
                                    <td style="padding: 3px 5px !important;" colspan="2">Total</td>
                                    <td style="padding: 3px 5px !important;">${total_quantity}</td>
                                    <td style="padding: 3px 5px !important;">100%</td>
                                </tr>`
                            }
                        }
                    }
                    else{
                        let count=1;
                        let total = data.reduce((total, item) => total + item.QUANTITY, 0);
                        head='<tr><th>SL</th>'+
                            '<th>VISIT STATUS</th>'+
                            '<th>QUANTITY</th>' +
                            '<th>PERCENT</th>'+
                            '</tr>';

                        for(let i=0;i<data.length;i++){
                            let percent = (data[i].QUANTITY*100/total).toFixed(2)
                            let percent_text = percent > 0 ? `${percent}%` : '0%'

                            html+=`<tr><td style="padding: 3px 5px !important;">${count}</td>
                            <td style="padding: 3px 5px !important;">${data[i].VISIT_STATUS}</td>
                            <td style="padding: 3px 5px !important;">${data[i].QUANTITY}</td>
                            <td style="padding: 3px 5px !important;">${percent_text}</td></tr>`
                            count++;
                        }
                    }

                    emptyContentAndAppendData(head,html);
                    // $('#export-image-btn').removeAttr('onclick');
                    // $('#export-image-btn').attr('onclick', 'exportTableToImage("tl_dynamic")');
                    break;
                case "weekly_outlet_summary":
                    var data=result.data;
                    var all_week=result.weeks;
                    var head = '<tr  style="text-align:center;"><th colspan="5"></th>'+
                                '<th colspan="3" class="text-center">TOTAL</th>' +
                                '<th colspan="3" class="text-center">1ST WEEK</th>' +
                                '<th colspan="3" class="text-center">2ND WEEK</th>' +
                                '<th colspan="3" class="text-center">3RD WEEK</th>' +
                                '<th colspan="3" class="text-center">4TH WEEK</th>' +
                                '<th colspan="3" class="text-center">5TH WEEK</th>' +
                                '</tr>';
                    var sub_head='<tr><th>SL</th>'+
                                '<th>SR_ID</th>' +
                                '<th>SR_NAME</th>' +
                                '<th>OUTLET_CODE</th>' +
                                '<th>OUTLET_NAME</th>' +
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th>'+
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th>'+
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th>'+
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th>'+
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th>'+
                                '<th>VISIT</th>'+
                                '<th>ORDER</th>'+
                                '<th>EXP</th></tr>';
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
                    emptyContentAndAppendData(head,html);
                    $('#export_option_btn').removeAttr('onclick');
                    $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"OUTLET_WEEKLY_STATISTICS<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;
                case "item_summary":
                    var data=result;
                    var count=1;
                    var head='<tr><th>SL</th>'+
                            '<th>DEPO NAME</th>' +
                            '<th>CLASS NAME</th>' +
                            '<th>SUB CATEGORY</th>'+
                            '<th>ITEM CODE</th>' +
                            '<th>ITEM NAME</th>' +
                            '<th>ORDER_CTN</th>'+
                            '<th>ORDER_PICS</th>'+
                            '<th>AMOUNT</th>'+
                            '</tr>';
                    var html='';
                    for(var i=0;i<data.length;i++){
                        html+='<tr><td>'+count+'</td>'+
                                '<td>'+data[i].dlrm_name+'</td>'+
                                '<td>'+data[i].itcl_name+'</td>'+
                                '<td>'+data[i].itsg_name+'</td>'+
                                '<td>'+data[i].amim_code+'</td>'+
                                '<td>'+data[i].amim_name+'</td>'+
                                '<td>'+data[i].order_pics+'</td>'+
                                '<td>'+data[i].order_ctn+'</td>'+
                                '<td>'+data[i].order_amnt+'</td></tr>'+
                        count++;
                    }
                    emptyContentAndAppendData(head,html);
                    $('#export_option_btn').removeAttr('onclick');
                    $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"ITEM_SUMMARY<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;
                case "order_report":
                    var head='';
                    var html='';
                    var data=result;
                    if(is_details==0){
                        head='<tr><th>DATE</th>'+
                                '<th>GROUP</th>' +
                                '<th>SR ID</th>' +
                                '<th>SR NAME</th>' +
                                '<th>ORDER NO</th>' +
                                '<th>T.SKU</th>' +
                                '<th>ORD_AMNT</th>' +
                                '<th>INV_DATE</th>' +
                                '<th>INV_NO</th>' +
                                '<th>INV.SKU</th>' +
                                '<th>INV_AMNT</th>' +
                                '<th>DELI_DATE</th>' +
                                '<th>DELI.SKU</th>' +
                                '<th>DELI_AMNT</th>' +
                                '</tr>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr>'+
                                    '<td>'+data[i].ordm_date+'</td>'+
                                    '<td>'+data[i].slgp_name+'</td>'+
                                    '<td>'+data[i].aemp_usnm+'</td>'+
                                    '<td>'+data[i].aemp_name+'</td>'+
                                    '<td>'+data[i].ordm_ornm+'</td>'+
                                    '<td>'+data[i].ordm_icnt+'</td>'+
                                    '<td>'+data[i].ordm_amnt+'</td>'+
                                    '<td>'+data[i].TRIP_DATE+'</td>'+
                                    '<td>'+data[i].ordm_ornm+'</td>'+
                                    '<td>'+data[i].challan_sku+'</td>'+
                                    '<td>'+data[i].INV_AMNT+'</td>'+
                                    '<td>'+data[i].delivery_date+'</td>'+
                                    '<td>'+data[i].delv_sku+'</td>'+
                                    '<td>'+data[i].DELV_AMNT+'</td></tr>';
                            count++;
                        }
                    }
                    else{
                        head='<tr><th>DATE</th>'+
                                '<th>GROUP</th>' +
                                '<th>SR ID</th>' +
                                '<th>SR NAME</th>' +
                                '<th>ORDER NO</th>' +
                                '<th>ITEM_CODE</th>' +
                                '<th>ITEM_NAME</th>' +
                                '<th>ORD_QTY</th>' +
                                '<th>ORD_AMNT</th>' +
                                '<th>INV_QNTY</th>' +
                                '<th>INV_AMNT</th>' +
                                '<th>DELI_QTY</th>' +
                                '<th>DELI_AMNT</th>' +
                                '</tr>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr>'+
                                    '<td>'+data[i].ordm_date+'</td>'+
                                    '<td>'+data[i].slgp_name+'</td>'+
                                    '<td>'+data[i].aemp_usnm+'</td>'+
                                    '<td>'+data[i].aemp_name+'</td>'+
                                    '<td>'+data[i].ordm_ornm+'</td>'+
                                    '<td>'+data[i].amim_code+'</td>'+
                                    '<td>'+data[i].amim_name+'</td>'+
                                    '<td>'+data[i].ordd_inty+'</td>'+
                                    '<td>'+data[i].ordd_oamt+'</td>'+
                                    '<td>'+data[i].INV_QNTY+'</td>'+
                                    '<td>'+data[i].INV_AMNT+'</td>'+
                                    '<td>'+data[i].ordd_dqty+'</td>'+
                                    '<td>'+data[i].ordd_odat+'</td>'+
                                    '</tr>';
                            
                        }
                    }
                    emptyContentAndAppendData(head,html);
                    $('#export_option_btn').removeAttr('onclick');
                    $('#export_option_btn').attr('onclick', 'exportTableToExcel(this,"TELE_ORDER_DATA<?php echo date('Y_m_d'); ?>.xls","tl_dynamic")');
                    break;
                default:

                break;
                
                
            }

        },
        error: function(error) {
            $('#export-image').hide();
            console.log(error)
        }
    });
    

}


function exportTableToImage(){
    if ($("#tl_dynamic_cont").length > 0) {
        html2canvas($("#tl_dynamic_cont"), {
            onrendered: function (canvas) {
                $("#download-btn").click(function () {
                    var imgData = canvas.toDataURL("image/png");
                    var link = document.createElement("a");
                    link.download = "my-image.png";
                    link.href = imgData;
                    link.click();
                });
            }
        });
    }
}


function getNPReasonOutletReport() {
    hide_me();
    var reportType = $("input[name='reportType']:checked").val();
    var nop_reason_id = $('#nonpro-id').val();
    var start_date = $('#start-date').val();
    var end_date = $('#end-date').val();
    if (reportType === undefined) {
        alert('Please select report');
        return false;
    } else if (reportType == '') {
        alert('Please select report');
        return false;
    }

    var _token = $("#_token").val();
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "{{ URL::to('/')}}/report/tele_sales",
        data: {
            _token: _token,
            start_date: start_date,
            end_date: end_date,
            reportType: reportType,
            nop_reason_id: nop_reason_id
        },
        cache: false,
        dataType: "json",
        success: function(result) {
            $('#ajax_load').css("display", "none");

            let head = '';
            let html = '';

            if(result.length > 0){
                head = `<tr>
                            <th>DATE</th>
                            <th>OLT CODE</th>
                            <th>OLT NAME</th>
                            <th>OLT ADD</th>
                            <th>OLT MOBILE</th>
                            <th>DISTRICT</th>
                            <th>THANA</th>
                            <th>WARD</th>
                            <th>MARKET</th>
                            <th>NP REASON</th>
                        </tr>`;

                for(let i=0; i<result.length; i++){
                    html += `<tr>
                                 <td> ${result[i]['npro_date']} </td>
                                 <td> ${result[i]['site_code']} </td>
                                 <td> ${result[i]['site_name']} </td>
                                 <td> ${result[i]['site_adrs']} </td>
                                 <td> ${result[i]['site_mob1']} </td>
                                 <td> ${result[i]['dsct_name']} </td>
                                 <td> ${result[i]['than_name']} </td>
                                 <td> ${result[i]['ward_name']} </td>
                                 <td> ${result[i]['mktm_name']} </td>
                                 <td> ${result[i]['nopr_name']} </td>
                             </tr>`;
                }
            }

            emptyContentAndAppendData(head,html);
        },
        error: function(error) {
            console.log(error)
        }
    });


}


//exportTableToCSVNote1,exportTableToCSVZone
function exportTableToExcel(elem, filename, tableId) {
    var BOM = "\uFEFF";
    var table = document.getElementById(tableId);
    var html = table.outerHTML;
    console.log(url);
    // var url = 'data:application/vnd.ms-excel,' + encodeURI(BOM+html); // Set your html table into url 
    var url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url 

    elem.setAttribute("href", url);
    $(elem).attr("download", filename);
    return false;
}

function exportTableToCSVNote(filename, tableId) {
    var csv = [];
    const BOM = '\uFEFF';
    var rows = document.querySelectorAll('#' + tableId + '  tr');
    for (var i = 0; i < rows.length; i++) {
        var row = [],
            cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++)
            row.push(BOM + cols[j].innerText);
        csv.push(row.join(","));
    }
    downloadCSV(csv.join("\n"), filename);
}

function exportTableToCSV(filename, tableId) {
    // alert(tableId);
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + '  tr');
    for (var i = 0; i < rows.length; i++) {
        var row = [],
            cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++)
            row.push(cols[j].innerText);
        csv.push(row.join(","));
    }
    downloadCSV(csv.join("\n"), filename);
}

function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    csvFile = new Blob([csv], {
        type: "text/csv;charset=utf-8"
    });
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
// Declare date as datepicker
$(document).ready(function() {
    $('.start_date').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: '-4m',
        maxDate: new Date(),
        autoclose: 1,
        showOnFocus: true
    });

    $('.date').datepicker({
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
    onSelect: function(date) {
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
        } else if (zone != "") {
            maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
        } else if (region != '') {
            maxDate = new Date(selectedDate.getTime() + msecsInADay * 20);
        } else if (slgp != "") {
            maxDate = new Date(selectedDate.getTime() + msecsInADay * 15);
        }

        $("#end_date").datepicker("option", "minDate", startDate);
        $("#end_date").datepicker("option", "maxDate", maxDate);

    }
});

$("#start_date_d").datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: '-4m',
    maxDate: new Date(),
    changeMonth: true,
    onSelect: function(date) {
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
        } else if (market_id != "") {
            maxDate = new Date(selectedDate.getTime() + msecsInADay * 30);
        } else if (ward_id != '') {
            maxDate = new Date(selectedDate.getTime() + msecsInADay * 20);
        } else if (than_id != "") {
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

$("#year_mnth").datepicker({
    dateFormat: 'yy-mm',
    changeMonth: true
});

function clearDate() {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('start_date_d').value = '';
    document.getElementById('end_date_d').value = '';
}

$('input:radio[name="outletAType"]').change(function() {
    clearDate();
});
$('.target').on('click', function() {
    $('button').removeClass('selected');
    $(this).addClass('selected');
});
$('.target1').on('click', function() {
    $('button').removeClass('selecteded');
    $(this).addClass('selecteded');
});
</script>

<script src="{{asset('theme/src/js/telesales_report/filter_date_control.js')}}"></script>
<script>
$(document).ready(function() {
    $('img').hover(function() {
        $(this).css("cursor", "pointer");
        $(this).toggle({
            effect: "scale",
            percent: "90%"
        }, 200);
    }, function() {
        $(this).toggle({
            effect: "scale",
            percent: "80%"
        }, 200);

    });

    //carousel options
    $('#quote-carousel').carousel({
        pause: true,
        interval: 4000,
    });
});
</script>
@endsection