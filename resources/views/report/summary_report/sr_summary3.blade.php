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
                            <strong>SR Productivity</strong>
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
                            <center><strong> ::: SR Productivity Summary :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">

                        <div class="x_content">
                            <div class="">
                                <div class="col-md-8 offset-2 rp_type_div">
                                    <div class="col-md-4 ">Select Report Type</div>
                                    <div class="col-md-2 offset-3">

                                        <label>
                                            Summary Report
                                            <input type="radio" name="type" value="1" onclick="getNextFilterField(this.value)"  id="rp_sm"/>
                                        </label>

                                    </div>
                                    <div class="col-md-2"><label>
                                            Detailed Report
                                            <input type="radio" name="type" value="2" onclick="getNextFilterField(this.value)"  id="rp_dt"/>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <form class="form-horizontal form-label-left" action="{{url('/get/market/report')}}"
                                  method="get" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}

                                <div class="form-group rp_details_radio_div">
                                    <label class="control-label col-md-4 col-sm-4 "></label>
                                    <div class="col-md-8 col-sm-8 ">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Productive<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-1 col-sm-1 col-xs-2">
                                            <input type="radio" class="form-control" name="sr_type"  value="1" onclick="checkSRTypeSelected(this.value)" />
                                        </div>
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Non Productive<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-1 col-sm-1 col-xs-2">
                                            <input type="radio" class="form-control" name="sr_type" id="sr_type" value="2" onclick="checkSRTypeSelected(this.value)" />
                                        </div>
                                        <label class="control-label col-md-1 col-sm-1 col-xs-12" for="sr_type">Both<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-1 col-sm-1 col-xs-2">
                                            <input type="radio" class="form-control" name="sr_type" id="sr_type" value="3" onclick="checkSRTypeSelected(this.value)" />
                                        </div>

                                    </div>
                                </div>
                                <div class="rp_sm">
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
                                    <div class="form-group ">

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From Date<span
                                                    class="required">*</span>
                                        </label>

                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                            >
                                        </div>

                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">To Date<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 rp_show">
                                        <button id="send" type="button"
                                                class="btn btn-success  col-md-offset-2 col-sm-offset-2"
                                                onclick="getReport()">Submit
                                        </button>
                                    </div>
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
        function testFun(){
            alert("joy Bangla");
        }
    </script>
    <script type="text/javascript">
        $('#acmp_id').select2();
        $('#sales_group_id').select2();
        $('#dirg_id').select2();
        $('#zone_id').select2();
        $('#tableDiv').hide();
        $('.rp_sm').hide();
        $('.rp_details_radio_div').hide();
        function getNextFilterField(rp_type){
            console.log(rp_type);
            if(rp_type==2){
                $('.rp_sm').hide();
                $('.rp_details_radio_div').show();
            }
            if(rp_type==1){
                $('.rp_details_radio_div').hide();
                $('.rp_sm').show();
            }

        }
        function checkSRTypeSelected(sr_type){
            console.log(sr_type);
            $('.rp_sm').show();

        }

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
                    console.log('Group Data');
                    console.log(data);
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
                    console.log('Zone Data');
                    console.log(data);
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
            var _token = $("#_token").val();
            //alert(acmp_id);
            if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/load/filter/sr_productivity/filter",
                    data: {
                        acmp_id: acmp_id,
                        zone_id: zone_id,
                        dirg_id: dirg_id,
                        sales_group_id: sales_group_id,
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

                                '<td>' + (data[i]['t_amnt']/1000).toFixed(2) + '</td>' +
                                '<td>' + (data[i]['t_amnt']/1000 / data[i]['s_outet']).toFixed(2) + '</td>' +
                                '<td>' + (data[i]['lpc']) + '</td>' +
                                '<td>' + "Location" + '</td>' +
                                '<td>' + (data[i]['inTime']) + '</td>' +
                                '<td>' + (data[i]['firstOrTime']) + '</td>' +
                                '<td>' + (data[i]['lastOrTime']) + '</td>' +
                                '<td>' + (data[i]['workTime']) + '</td>' +
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
        $(document).ready(function () {
            $('#start_date').datetimepicker({
                format: "YYYY-MM-DD"
            });
            $("select").select2({width: 'resolve'});
        });

        function dateRange(){

            var acmp=$('#acmp_id').val();
            var slgp=$('#sales_group_id').val();
            var region=$('#dirg_id').val();
            var zone=$('#zone_id').val();
            var str_date=$('#start_date').val();
            //var bd=new Date(str_date).addDays(100).format('Y-m-d');
            // console.log(bd);
            console.log(str_date);
            if(zone !=""){
                console.log('zone not null')
                $('#end_date').datetimepicker({
                    format: 'YYYY-MM-DD',
                    minDate:new Date(str_date).format('Y-m-d'),
                    maxDate:new Date(str_date).addDays(20).format('Y-m-d')});
                // $("select").select2({width: 'resolve'});
            }
            else if(slgp !=""){
                console.log('group not null')
                $('#start_date').datetimepicker({
                    format: "YYYY-MM-DD"
                });
                $('#end_date').datetimepicker({
                    minDate:new Date(str_date).format('Y-m-d'),
                    maxDate:new Date(str_date).addDays(15),
                    format: 'YYYY-MM-DD'});
                $("select").select2({width: 'resolve'});
            }
            else{
                console.log('all not null')
                $('#start_date').datetimepicker({
                    format: "YYYY-MM-DD"
                });
                $('#end_date').datetimepicker({
                    minDate:str_date,
                    maxDate:str_date,
                    format: 'YYYY-MM-DD'});
                $("select").select2({width: 'resolve'});
            }
        }
        $("#start_date").on("dp.change",function(e){ dateRange()  });
    </script>
@endsection