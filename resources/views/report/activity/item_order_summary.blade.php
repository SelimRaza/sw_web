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
                            <strong>Item Order Summary</strong>
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
                            <center><strong> ::: Item Order Summary :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action="{{url('/get/market/report')}}"
                                  method="get" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="form-group">

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From Date<span
                                                class="required" style="color: red;">*</span>
                                    </label>

                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" class="form-control" name="start_date" id="start_date"
                                               value="<?php echo date('Y-m-d'); ?>"/>
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To Date<span
                                                class="required" style="color: red;">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" class="form-control" name="end_date" id="end_date"
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                class="required" style="color: red;">*</span>
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
                                        <select class="form-control" name="sales_group_id" id="sales_group_id">
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
                                        <select class="form-control" name="zone_id" id="zone_id">

                                            <option value="">Select Zone</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">No. Of Item<span
                                                class="required" style="color: red;">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="no_of_item" id="no_of_item">
                                            <option value="5">Select Number of Item</option>
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="20">50</option>
                                            <option value="20">100</option>
                                            <option value="20">All</option>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Type<span
                                                class="required" style="color: red;">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="item_type" id="item_type">
                                            <option value="DESC">Top Items</option>
                                            <option value="ASC">Bottom Items</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button id="send" type="button"
                                            class="btn btn-success  col-md-offset-2 col-sm-offset-2"
                                            onclick="getReport()">Submit
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>

                    <div id="tableDiv">
                        <div class="x_panel">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                        <thead>
                                        <tr class="tbl_header">
                                            <th>##</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Category</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Factor</th>
                                            <th>Rate</th>
                                            <th>Order Quantity</th>
                                            <th>Delivery Quantity</th>
                                            <th>Order Amount(k)</th>
                                            <th>Delivery Amount(k)</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">

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

        function getGroup(slgp_id) {
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
                    var html = '<option value="">Select Group</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#sales_group_id").append(html);

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

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var sales_group_id = $('#sales_group_id').val();
            var dirg_id = $('#dirg_id').val();
            var zone_id = $('#zone_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var item_type = $('#item_type').val();
            var no_of_item = $('#no_of_item').val();
            var _token = $("#_token").val();
            /*alert(acmp_id); item_type no_of_item
            alert(sales_group_id);
            alert(dirg_id);
            alert(zone_id);*/
            if (acmp_id != "" && item_type!="" && no_of_item!="") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/filter/item_order_summary",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        dirg_id: dirg_id,
                        sales_group_id: sales_group_id,
                        start_date: start_date,
                        end_date: end_date,
                        item_type: item_type,
                        no_of_item: no_of_item,
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
                                '<td>' + start_date + " - " + end_date + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['itsg_name'] + '</td>' +
                                '<td>' + data[i]['amim_code'] + '</td>' +
                                '<td>' + data[i]['amim_name'] + '</td>' +

                                '<td>' + data[i]['ordd_unit'] + '</td>' +
                                '<td>' + data[i]['ordd_rate'] + '</td>' +
                                '<td>' + data[i]['order_qty'] + '</td>' +
                                '<td>' + data[i]['delivery_qty'] + '</td>' +
                                '<td>' + (data[i]['order_amount'] / 1000).toFixed(4) + '</td>' +

                                '<td>' + (data[i]['delivery_amount'] / 1000).toFixed(4) + '</td>' +

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

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
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


        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });


    </script>
@endsection