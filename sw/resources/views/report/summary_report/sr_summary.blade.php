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
                        <li class="active">
                            <strong>SR Summary</strong>
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
                        <div class="x_title">
                            <center><strong> ::: SR Summary :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{url ('load/filter/sr_summary/demo2')}}"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="item form-group rp_type_div">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Report Type</label>
                                    <div class="col-md-10 col-sm-10 ">


                                        <br/>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="reportType" id="reportType"
                                                   value="summary"/> Summary Report</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="reportType" id="reportType"
                                                   value="details"/> Detail Report</label>
                                        </div>

                                    </div>
                                </div>

                                <div id="detailReportType" class="item form-group rp_type_div">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12"></label>
                                    <div class="col-md-10 col-sm-10 ">
                                        <div class="col-md-4 col-sm-4 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="productive" checked/> Productive</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="non_productive"/> Non Productive</label>
                                        </div>

                                        <div class="col-md-4 col-sm-4 ">
                                        <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="both"/> Both</label>
                                        </div>

                                    </div>
                                </div>

                                <hr/>
                                <div id="sales_heirarchy">
                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="acmp_id" id="acmp_id"
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
                                            <select class="form-control" name="sales_group_id" id="sales_group_id" onchange="clearDate()">
                                                <option value="">Select Group</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Region<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="dirg_id" id="dirg_id"
                                                    onchange="getZone(this.value)">
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
                                            <select class="form-control" name="zone_id" id="zone_id"
                                                    onchange="getSR(this.value)">

                                                <option value="">Select Zone</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">SR<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="sr_id" id="sr_id">
                                                <option value="">Select SR</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group">

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From
                                            Date<span
                                                    class="required">*</span>
                                        </label>

                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To Date<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div> -->
                                    <!-- date start -->
                                    <div class="form-group ">

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="start_date">From Date<span
                                                class="required">*</span>
                                    </label>

                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input  class="form-control" name="start_date" id="start_date"
                                        autocomplete="off">
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="end_date">To End<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input  class="form-control" name="end_date" id="end_date" 
                                        autocomplete="off"  >
                                    </div>
                                </div>
                                    <!-- date end -->
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button id="send" type="button"
                                                class="btn btn-success  col-md-offset-2 col-sm-offset-2"
                                                onclick="getReport()">Submit
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                    {{-- Summary Report --}}
                    <div id="tableDiv_summary">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_summary')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>

                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="tbl_header">
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Zone Name</th>
                                            <th>Total SR</th>
                                            <th>Present SR</th>
                                            <th>Leave/IOM SR</th>
                                            <th>Absent SR</th>
                                            <th>Productive SR</th>
                                            <th>Total Outlet</th>
                                            <th>Visited Outlet</th>
                                            <th>Total memo</th>
                                            <th>Stike rate</th>
                                            <th>LPC</th>
                                            <th>EX per SR</th>
                                            <th>EX per Outlet</th>
                                            <th>Total order</th>
                                            <th>Target</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_summary">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--Details Report --}}
                    <div id="tableDiv_details">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSVProductive('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_details')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>

                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="tbl_header">
                                            <th>SL</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SD Mobile Number</th>
                                            <th>Route Name</th>
                                            <th>Total Outlet</th>
                                            <th>Visited Outlet</th>
                                            <th>Total memo</th>
                                            <th>Stike rate</th>
                                            <th>LPC</th>
                                            <th>EX per SR</th>
                                            <th>EX per Outlet</th>
                                            <th>Total order</th>
                                            <th>Target</th>
                                            <th>Start Wrok</th>
                                            <th>First Order Time</th>
                                            <th>Last Order Time</th>
                                            <th>Work Duration</th>
                                            <th>Show location</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_details">

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

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });



        $('#detailReportType').hide();
        $('#tableDiv_summary').hide();
        $('#tableDiv_details').hide();

        $('#sales_heirarchy').hide();
        //reportType detailReportType outletAType
        $("input[name='reportType']").on("change", function () {
            $('#detailReportType').hide();
            $('#sales_heirarchy').hide();

            var reportType = this.value;
            //alert(reportType);
            if (reportType == "summary") {
                $('#sales_heirarchy').show();

            } else {
                $('#detailReportType').show();
                $('#sales_heirarchy').show();
            }

            //alert(this.value);
            //alert(this.value);
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


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#sales_group_id").append(html);

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


        function getSummaryReport() {
            //reportType outletType outletAType srType classType acmp_id sales_group_id dirg_id zone_id sr_id

            var reportType = $("input[name='reportType']:checked").val();
            var outletType = $("input[name='outletTypev']:checked").val();
            var outletAType = $("input[name='outletAType']:checked").val();
            var srType = $("input[name='srTypev']:checked").val();
            var classType = $("input[name='classType']:checked").val();
            var comp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var region_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            var dist_id = $('#dist_id').val();
            var than_id = $('#than_id').val();
            var ward_id = $('#ward_id').val();
            var market_id = $('#market_id').val();
            var start_date_d = $('#start_date_d').val();
            var end_date_d = $('#end_date_d').val();

            var _token = $("#_token").val();

            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/filter/order_summary_report/filter",
                data: {
                    reportType: reportType,
                    outletType: outletType,
                    outletAType: outletAType,
                    srType: srType,
                    comp_id: comp_id,
                    classType: classType,
                    zone_id: zone_id,
                    region_id: region_id,
                    sales_group_id: sales_group_id,
                    start_date: start_date,
                    end_date: end_date,

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
                    console.log(data);


                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    if (reportType == "b_sr") {
                        if (outletType == "wOlt") {
                            if (classType == "w_class") {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['acmp_name'] + '</td>' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['dirg_name'] + '</td>' +
                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +

                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_wOlt").empty();
                                $("#cont_wOlt").append(html);

                                //$('#datatable').DataTable().draw();


                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').show();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();

                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['acmp_name'] + '</td>' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['dirg_name'] + '</td>' +
                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +

                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['amim_code'] + '</td>' +
                                        '<td>' + data[i]['amim_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_wOlt_item").empty();
                                $("#cont_wOlt_item").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').show();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();
                            }

                        }
                        if (outletType == "olt") {
                            if (classType == "w_class") {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['acmp_name'] + '</td>' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['dirg_name'] + '</td>' +
                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                    //alert(html);

                                }
                                $("#cont").empty();
                                $("#cont").append(html);

                                //$('#datatable').DataTable().draw();
                                $('#tableDiv').show();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();

                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['acmp_name'] + '</td>' +
                                        '<td>' + data[i]['slgp_name'] + '</td>' +
                                        '<td>' + data[i]['dirg_name'] + '</td>' +
                                        '<td>' + data[i]['zone_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['amim_code'] + '</td>' +
                                        '<td>' + data[i]['amim_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                    //alert(html);

                                }
                                $("#cont_with_outlet_with_item").empty();
                                $("#cont_with_outlet_with_item").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').show();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();
                            }
                        }
                    }
                    if (reportType == "d_outlet") {
                        if (srType == "wsr") {
                            if (classType == "w_class") {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +

                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['dsct_name'] + '</td>' +
                                        '<td>' + data[i]['than_name'] + '</td>' +
                                        '<td>' + data[i]['ward_name'] + '</td>' +
                                        '<td>' + data[i]['mktm_name'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                        '<td>' + data[i]['itcl_code'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_w_sr_class").empty();
                                $("#cont_w_sr_class").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').show();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();
                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +

                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['dsct_name'] + '</td>' +
                                        '<td>' + data[i]['than_name'] + '</td>' +
                                        '<td>' + data[i]['ward_name'] + '</td>' +
                                        '<td>' + data[i]['mktm_name'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +
                                        '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                        '<td>' + data[i]['aemp_name'] + '</td>' +
                                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                        '<td>' + data[i]['itcl_code'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['amim_code'] + '</td>' +
                                        '<td>' + data[i]['amim_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_w_sr_item").empty();
                                $("#cont_w_sr_item").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').show();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').hide();
                            }
                        } else {
                            if (classType == "w_class") {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +

                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['dsct_name'] + '</td>' +
                                        '<td>' + data[i]['than_name'] + '</td>' +
                                        '<td>' + data[i]['ward_name'] + '</td>' +
                                        '<td>' + data[i]['mktm_name'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +

                                        '<td>' + data[i]['itcl_code'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_wo_sr_class").empty();
                                $("#cont_wo_sr_class").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').show();
                                $('#tableDiv_wo_sr_item').hide();
                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    html += '<tr>' +

                                        '<td>' + count + '</td>' +
                                        '<td>' + data[i]['dsct_name'] + '</td>' +
                                        '<td>' + data[i]['than_name'] + '</td>' +
                                        '<td>' + data[i]['ward_name'] + '</td>' +
                                        '<td>' + data[i]['mktm_name'] + '</td>' +
                                        '<td>' + data[i]['site_code'] + '</td>' +
                                        '<td>' + data[i]['site_name'] + '</td>' +
                                        '<td>' + data[i]['site_mob1'] + '</td>' +
                                        '<td>' + data[i]['itcl_code'] + '</td>' +
                                        '<td>' + data[i]['itcl_name'] + '</td>' +
                                        '<td>' + data[i]['amim_code'] + '</td>' +
                                        '<td>' + data[i]['amim_name'] + '</td>' +
                                        '<td>' + data[i]['ordd_qnty'] + '</td>' +
                                        "<td>" + (data[i]['ordd_oamt']).toFixed(2) + "</td>" +
                                        '</tr>';
                                    count++;
                                }
                                $("#cont_wo_sr_item").empty();
                                $("#cont_wo_sr_item").append(html);

                                //$('#datatable').DataTable().draw();

                                $('#tableDiv').hide();
                                $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').hide();
                                $('#tableDiv_w_outlet_item').hide();

                                $('#tableDiv_w_sr_class').hide();
                                $('#tableDiv_w_sr_item').hide();
                                $('#tableDiv_wo_sr_class').hide();
                                $('#tableDiv_wo_sr_item').show();
                            }
                        }
                    }

                    $('#ajax_load').css("display", "none");

                }

            });

        }


        function getReport() {
            $("#cont_summary").empty();
            //reportType detailReportType outletAType
            var reportType = $("input[name='reportType']:checked").val();
            //var outletType = $("input[name='detailReportType']:checked").val();
            var outletAType = $("input[name='outletAType']:checked").val();

            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var sr_id=$('#sr_id').val();
            var _token = $("#_token").val();
            if(acmp_id ==''){
                return confirm('Please select company');
            }
            if(start_date=='' || end_date ==''){
                return confirm('Please select date');
            }
            if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/filter/sr_summary/demo2",
                    data: {
                        reportType: reportType,
                        outletAType: outletAType,
                        acmp_id: acmp_id,
                        dirg_id: dirg_id,
                        zone_id: zone_id,
                        sales_group_id: sales_group_id,
                        sr_id:sr_id,
                        start_date: start_date,
                        end_date: end_date,
                        _token: _token
                    },

                    cache: false,
                    dataType: "json",

                    success: function (data) {
                        //alert(data);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        if (reportType == "summary") {
                            for (var i = 0; i < data.length; i++) {

                                html += '<tr>' +
                                    '<td>' + count + '</td>' +
                                    '<td>' + data[i]['date'] + '</td>' +
                                    '<td>' + data[i]['slgp_name'] + '</td>' +
                                    '<td>' + data[i]['zone_name'] + '</td>' +
                                    '<td>' + data[i]['t_sr'] + '</td>' +
                                    '<td>' + data[i]['p_sr'] + '</td>' +
                                    '<td>' + data[i]['l_sr'] + '</td>' +
                                    '<td>' + (data[i]['t_sr'] - data[i]['p_sr'] - data[i]['l_sr']) + '</td>' +
                                    '<td>' + data[i]['pro_sr'] + '</td>' +
                                    '<td>' + data[i]['t_outlet'] + '</td>' +
                                    "<td>" + (data[i]['c_outlet']) + "</td>" +
                                    "<td>" + (data[i]['s_outet']) + "</td>" +
                                    "<td>" + (data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2) + "</td>" +
                                    "<td>" + (data[i]['lpc']) + "</td>" +
                                    "<td>" + (data[i]['t_amnt'] / 1000 / data[i]['p_sr']).toFixed(2) + "</td>" +
                                    "<td>" + (data[i]['t_outlet'] / data[i]['t_sr']).toFixed(2) + "</td>" +
                                    "<td>" + (data[i]['t_amnt'] / 1000).toFixed(2) + "</td>" +
                                    "<td>" + "-" + "</td>" +
                                    '</tr>';
                                count++;
                            }
                            //alert(html);
                            $("#cont_summary").append(html);

                            //$('#datatable').DataTable().draw();
                            $('#tableDiv_summary').show();
                            $('#tableDiv_details').hide();
                        }else{
                            var parameter='';
                            for (var i = 0; i < data.length; i++) {
                                parameter="sr_summary_report/map/" + data[i]['aemp_id'] +"/"+data[i]['date'];
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
                                    '<td>' + data[i]['s_outet'] + "</td>" +
                                    '<td>' + (data[i]['s_outet'] * 100 / (data[i]['c_outlet'])).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['lpc']) + '</td>' +
                                    '<td>' + (data[i]['t_amnt']/1000).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_amnt']/1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                    '<td>' + (data[i]['t_amnt']/1000).toFixed(2) + '</td>' +
                                    '<td>' + "-" + '</td>' +

                                    '<td>' + (data[i]['inTime']) + '</td>' +
                                    '<td>' + (data[i]['firstOrTime']) + '</td>' +
                                    '<td>' + (data[i]['lastOrTime']) + '</td>' +
                                    '<td>' + (data[i]['workTime']) + '</td>' +
                                    '<td><a href="'+parameter+'">' + "<i class='fa fa-map-marker fa-3x' style='color:green;text-align:center;'></i>" + '</a></td>' +
                                    '</tr>';

                                count++;
                            }
                            //alert(html);
                            $("#cont_details").append(html);

                            //$('#datatable').DataTable().draw();
                            $('#tableDiv_summary').hide();
                            $('#tableDiv_details').show();


                        }
                    }

                });
            } else {
                alert("Please select Company and Try again!!!");
            }

        }

        function exportTableToCSV(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }

        function exportTableToCSVProductive(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
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


        //$('#start_date').datetimepicker({minDate: -7, maxDate: 0, format: 'YYYY-MM-DD'});
        //$("#start_date").datetimepicker({minDate: -7, maxDate: 0, format: 'YYYY-MM-DD'}).val();

        //$("#start_date").datepicker({minDate: -61, maxDate: 0, dateFormat: 'yy-mm-dd'}).val();

        // var date = new Date();
        // date.setDate(date.getDate());

        // $('#start_date').datetimepicker({
        //     isRTL: false,
        //     format: 'dd.mm.yyyy hh:ii',
        //     autoclose: true,
        //     language: 'tr',
        //     startDate: date
        // });

        /*$('#start_date').datetimepicker({
            minDate: new Date(),
            autoclose: true
        });*/
      //  $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});

      $(document).ready(function () {
          $('#start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate:'-3m',
            maxDate:new Date(),
            autoclose: 1,
            showOnFocus:true
          });

          $("select").select2({width: 'resolve'});
        });

      $("#start_date").datepicker({ 
          dateFormat: 'yy-mm-dd',
          minDate:'-3m',
          maxDate:new Date(),
          changeMonth: true,
          onSelect: function(date){
          var slgp=$('#sales_group_id').val(); 
          var zone=$('#zone_id').val();
          var region=$('#dirg_id').val();
          var selectedDate = new Date(date);
          var msecsInADay = 86400000;
          var startDate = new Date(selectedDate.getTime());
          var maxDate = new Date(selectedDate.getTime());
          if(zone !=""){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*30);
          }
          else if(region !=''){
             maxDate = new Date(selectedDate.getTime()+msecsInADay*20);
          }
          else if(slgp !=""){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*15);
          }
          
          $("#end_date").datepicker( "option", "minDate", startDate);
          $("#end_date").datepicker( "option", "maxDate",maxDate );

            }
      });
     
      $("#end_date").datepicker({ 
          dateFormat: 'yy-mm-dd',
          changeMonth: true
      });
       function clearDate(){
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            
      }
    </script>
@endsection