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
                            <strong>Weekly Order Summary</strong>
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
                            <center><strong> ::: Weekly Order Summary :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{url ('load/filter/order_summary_report/filter')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                
                                <div class="item form-group rp_type_div ">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Report Type *</label>
                                    <div class="col-md-10 col-sm-10 ">


                                        <br/>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="reportType" id="reportType"
                                                   value="b_sr"/> BU-Group (SR)
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="reportType" id="reportType"
                                                   value="d_outlet"/> District-Thana (Outlet)
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div id="outletType" class="item form-group rp_type_div">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12"></label>
                                    <div class="col-md-10 col-sm-10 ">

                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletTypev" id="outletTypev"
                                                   value="olt" checked=""/> With Outlet
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletTypev" id="outletTypev"
                                                   value="wOlt"/> Without Outlet
                                            </label>
                                        </div>

                                    </div>

                                </div>
                            
                                <div id="outletAssetType" class="item form-group rp_type_div">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Asset Type *</label>
                                    <div class="col-md-10 col-sm-10 ">
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="isAll" checked/> All
                                            </label>
                                        </div>
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="issg"/> Shop Signing
                                            </label>
                                        </div>

                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="isfg"/> VC Cooler
                                            </label>
                                        </div>
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="isrfg"/>  Refrigerator
                                            </label>
                                        </div>
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="iscfm"/>  Coffee Machine
                                            </label>
                                        </div>
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="isfg"/>   Dsplay Box
                                            </label>
                                        </div>
                                        <div class="col-md-2 col-sm-2 ">
                                            <label>
                                            <input type="radio" class="flat" name="outletAType" id="outletAType"
                                                   value="isfg"/>   Glass Box
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="srType" class="item form-group rp_type_div">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12"></label>
                                    <div class="col-md-10 col-sm-10 ">
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="srTypev" id="srTypev" value="wsr"
                                                   checked="" required/> With SR
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-sm-6 ">
                                            <label>
                                            <input type="radio" class="flat" name="srTypev" id="srTypev" value="wo_sr"/>
                                                Without SR
                                            </label>
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

                                   

                                    <div class="form-group">

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">SR<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="sr_id" id="sr_id">
                                                <option value="">Select SR</option>
                                            </select>
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Month<span
                                                    class="required">*</span>
                                        </label>

                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   autocomplete="off"/>
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To Date<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="item form-group">

                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-2 col-sm-offset-2">
                                        <button id="send" type="button"
                                                class="btn btn-primary btn-block"
                                                onclick="getSummaryReport()">Submit
                                        </button>
                                    </div>
                                </div>


                                <div id="govt_heirarchy">

                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12"
                                               for="name">District<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="dist_id" id="dist_id"
                                                    onchange="getThanaBelogToDistrict(this.value)">
                                                <option value="">Select District</option>
                                                @foreach($dsct as $dsct)
                                                    <option value="{{$dsct->id}}">{{$dsct->dsct_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Thana<span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="than_id" id="than_id"
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
                                            <select class="form-control" name="ward_id" id="ward_id"
                                                    onchange="loadWardMarket()">
                                                <option value="">Select Ward</option>

                                            </select>
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Market<span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="market_id" id="market_id" onchange="loadOutlet()">

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
                                            <input type="text" class="form-control" name="start_date_d"
                                                   id="start_date_d"
                                                   autocomplete="off"/>
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To Date<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date_d" id="end_date_d"
                                                   autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select Outlet<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="outlet_id" id="outlet_id">

                                                <option value="">Select</option>
                                            </select>
                                        </div>
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

                                    <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-2 col-sm-offset-2">
                                        <button id="send" type="button"
                                                class="btn btn-dark btn-block"
                                                onclick="getSummaryReport()">Submit
                                        </button>
                                    </div>
                                </div>

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
                                           data-page-length='100'>
                                        <thead>
                                        {{--`acmp_name`, `slgp_name`, `dirg_name`, `zone_name`, `aemp_name`, `aemp_usnm`,
                                        `aemp_mob1`, `site_code`, `site_name`, `site_mob1`, `itcl_name`, `ordd_oamt`,`ordd_qnty`--}}
                                        <tr class="tbl_header">
                                            <th>SI</th>
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR Name</th>
                                            <th>SR Code</th>
                                            <th>SR Mobile</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mob1</th>
                                            <th>Class Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

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
                    <div id="tableDiv_withOutlet_with_item">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_withOutlet_with_item')"
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
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR Name</th>
                                            <th>SR Code</th>
                                            <th>SR Mobile</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mob1</th>
                                            <th>Class Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_with_outlet_with_item">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--without outlet with class --}}
                    <div id="tableDiv_w_outlet">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_w_outlet')"
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
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR Name</th>
                                            <th>SR Code</th>
                                            <th>SR Mobile</th>
                                            <th>Class Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_wOlt">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--without outlet with item--}}
                    <div id="tableDiv_w_outlet_item">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_w_outlet_item')"
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
                                            <th>Company</th>
                                            <th>Group Name</th>
                                            <th>Region Name</th>
                                            <th>Zone Name</th>
                                            <th>SR Name</th>
                                            <th>SR Code</th>
                                            <th>SR Mobile</th>
                                            <th>Class Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_wOlt_item">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--sr with class--}}
                    <div id="tableDiv_w_sr_class">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_w_sr_class')"
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
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mobile</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Class Id</th>
                                            <th>Class Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_w_sr_class">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--sr with item--}}
                    <div id="tableDiv_w_sr_item">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_w_sr_item')"
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
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mobile</th>
                                            <th>SR ID</th>
                                            <th>SR Name</th>
                                            <th>SR Mobile</th>
                                            <th>Class Id</th>
                                            <th>Class Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_w_sr_item">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--without sr with item--}}
                    <div id="tableDiv_wo_sr_class">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_wo_sr_class')"
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
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mobile</th>

                                            <th>Class Id</th>
                                            <th>Class Name</th>

                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_wo_sr_class">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--without sr with item--}}
                    <div id="tableDiv_wo_sr_item">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv_wo_sr_item')"
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
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Ward</th>
                                            <th>Market</th>
                                            <th>Outlet Code</th>
                                            <th>Outlet Name</th>
                                            <th>Outlet Mobile</th>

                                            <th>Class Id</th>
                                            <th>Class Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>

                                        </tr>
                                        </thead>
                                        <tbody id="cont_wo_sr_item">

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


        $('#tableDiv').hide();
        $('#tableDiv_withOutlet_with_item').hide();
        $('#tableDiv_w_outlet').hide();
        $('#tableDiv_w_outlet_item').hide();
        $('#tableDiv_w_sr_class').hide();
        $('#tableDiv_w_sr_item').hide();
        $('#tableDiv_wo_sr_class').hide();
        $('#tableDiv_wo_sr_item').hide();

        $('#outletType').hide();
        $('#outletAssetType').hide();
        $('#srType').hide();
        $('#govt_heirarchy').hide();
        $('#sales_heirarchy').hide();

        $("input[name='reportType']").on("change", function () {
            $('#outletType').hide();
            $('#srType').hide();

            var reportType = this.value;
            //alert(reportType);
            if (reportType == "b_sr") {
                $('#outletType').show();
                $('#outletAssetType').show();
                $('#sales_heirarchy').show();
                $('#govt_heirarchy').hide();
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
        function loadOutlet() {
            var _token = $("#_token").val();
            var  market_id= $("#market_id").val();
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
            var sr_id =$('#sr_id').val();
            var outlet_id=$('#outlet_id').val();
            var dist_id = $('#dist_id').val();
            var than_id = $('#than_id').val();
            var ward_id = $('#ward_id').val();
            var market_id = $('#market_id').val();
            var start_date_d = $('#start_date_d').val();
            var end_date_d = $('#end_date_d').val();
            var _token = $("#_token").val();
            if(reportType=="b_sr"){
                if(comp_id==""){
                    return confirm('Please select company');
                }
                if(start_date=='' || end_date==''){
                    return confirm('Please Select Date');
                   
                }
            }
            else if(reportType=="d_outlet"){
                if(dist_id ==""){
                    return confirm('Please select district');
                }
                if(start_date_d=='' || end_date_d==''){
                    return confirm('Please Select Date');
                }
            }
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
                    sr_id:sr_id,
                    outlet_id:outlet_id,
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
                    if (reportType == "b_sr"){
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


                               // $('#tableDiv').hide();
                               // $('#tableDiv_withOutlet_with_item').hide();
                                $('#tableDiv_w_outlet').show();
                                //$('#tableDiv_w_outlet_item').hide();

                                //$('#tableDiv_w_sr_class').hide();
                                //$('#tableDiv_w_sr_item').hide();
                                //$('#tableDiv_wo_sr_class').hide();
                                //$('#tableDiv_wo_sr_item').hide();

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
                    if (reportType == "d_outlet"){
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
                            }else{
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
                        }else{
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
                            }else{
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

        function getSummaryReport_doutlet() {
            //reportType outletType outletAType srType classType acmp_id sales_group_id dirg_id zone_id sr_id

            var reportType = $("input[name='reportType']:checked").val();
            var outletType = $("input[name='outletTypev']:checked").val();
            var outletAType = $("input[name='outletAType']:checked").val();
            var srTypewithsr = $("input[name='srTypev']:checked").val();
            var classType = $("input[name='classType']:checked").val();

            /* var comp_id = $('#acmp_id').val();
             var sales_group_id = $('#sales_group_id').val();
             var region_id = $('#dirg_id').val();
             var zone_id = $('#zone_id').val();
             var start_date = $('#start_date').val();
             var end_date = $('#end_date').val();*/

            var dist_id = $('#dist_id').val();
            var than_id = $('#than_id').val();
            var ward_id = $('#ward_id').val();
            var market_id = $('#market_id').val();
            var start_date_d = $('#start_date_d').val();
            var end_date_d = $('#end_date_d').val();

            var _token = $("#_token").val();
            alert(outletAType);
            alert(outletType);
            alert(reportType);
            alert(classType);
            alert(srTypewithsr);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/filter/order_summary_report/filter",
                data: {
                    reportType: reportType,
                    outletType: outletType,
                    outletAType: outletAType,
                    srTypewithsr: srType,
                    classType: classType,

                    /*comp_id: comp_id,
                    zone_id: zone_id,
                    region_id: region_id,
                    sales_group_id: sales_group_id,
                    start_date: start_date,
                    end_date: end_date,*/

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
                    alert(data);
                    console.log(data);


                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    if (srTypewithsr == "wsr") {
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
                        }
                    }
                    $('#ajax_load').css("display", "none");

                }

            });

        }

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/filter/OrderSummaryController/filter",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        sales_group_id: sales_group_id,
                        start_date: start_date,
                        end_date: end_date,
                        _token: _token
                    },

                    cache: false,
                    dataType: "json",

                    success: function (data) {
                        alert(data);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;

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
                        //alert(html);
                        $("#cont").append(html);

                        //$('#datatable').DataTable().draw();
                        $('#tableDiv').show();
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
        /*$('#start_date').datetimepicker({
            startDate: '-2m',
            endDate: '+2d',
            format: 'YYYY-MM-DD'
        });*/
        //$("#start_date").datepicker({minDate: -61, maxDate: 0, dateFormat: 'yy-mm-dd'}).val();
        // $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        // $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});

        // $('#start_date_d').datetimepicker({format: 'YYYY-MM-DD'});
        // $('#end_date_d').datetimepicker({format: 'YYYY-MM-DD'});
        // $(document).ready(function () {

        //     $("select").select2({width: 'resolve'});

        // });
//Business unit wise section Date function
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
          var assetType=$("input[name='outletAType']:checked").val();
          
          var selectedDate = new Date(date);
          var msecsInADay = 86400000;
          var startDate = new Date(selectedDate.getTime());
          var maxDate = new Date(selectedDate.getTime());
          if(assetType !="isAll"){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*30);
          }
          else if(zone !=""){
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
    //Functions for the date of Distric thana wise section
      $(document).ready(function () {
          $('#start_date_d').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate:'-3m',
            maxDate:new Date(),
            autoclose: 1,
            showOnFocus:true
          });

          $("select").select2({width: 'resolve'});
        });

      $("#start_date_d").datepicker({ 
          dateFormat: 'yy-mm-dd',
          minDate:'-3m',
          maxDate:new Date(),
          changeMonth: true,
          onSelect: function(date){
          var market_id=$('#market_id').val(); 
          var ward_id =$('#ward_id').val();
          var than_id=$('#than_id').val();
          var assetType=$("input[name='outletAType']:checked").val();
          var selectedDate = new Date(date);
          var msecsInADay = 86400000;
          var startDate = new Date(selectedDate.getTime());
          var maxDate = new Date(selectedDate.getTime());
          if(assetType !='isAll'){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*30);
          }
          else if(market_id !=""){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*30);
          }
          else if(ward_id !=''){
             maxDate = new Date(selectedDate.getTime()+msecsInADay*20);
          }
          else if(than_id !=""){
            maxDate = new Date(selectedDate.getTime()+msecsInADay*15);
          }
          
          $("#end_date_d").datepicker( "option", "minDate", startDate);
          $("#end_date_d").datepicker( "option", "maxDate",maxDate );

            }
      });
     
      $("#end_date_d").datepicker({ 
          dateFormat: 'yy-mm-dd',
          changeMonth: true
      });
       function clearDate(){
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('start_date_d').value = '';
            document.getElementById('end_date_d').value = '';
            
      }
      $('input:radio[name="outletAType"]').change(function() {
          clearDate();
      });
    </script>
@endsection