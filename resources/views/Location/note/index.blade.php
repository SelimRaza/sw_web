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
                            <strong>All Note Details</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/location_note')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{--{{$search_text}}--}}">
                                <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">Go!</button>
                    </span>

                            </div>
                        </div>
                    </div>
                </form>
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

                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action=""
                                  method="get" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="form-group">

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">From Date<span
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
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Location<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="locm_id" id="locm_id" onchange="filterLocCompany(this.value)">
                                            <option value="">Select Location</option>
                                            @foreach($locm as $locmList)
                                                <option value="{{$locmList->id}}">{{$locmList->locm_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Company<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="lcmp_id" id="lcmp">
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">User<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input type="text" name="user_id" id="user" class="form-control">
                                        
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" ><span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                       <button id="send" type="button"
                                               class="btn btn-success btn-block  "
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
                                            <th>Location Master</th>
                                            <th>Company Name</th>
                                            <th>Department Name</th>
                                            <th>Section Name</th>
                                            <th>Location Code</th>
                                            <th>Location Name</th>
                                            <th>User Code</th>
                                            <th>User Name</th>
                                            <th>Note Details</th>
                                            <th>Address</th>
                                            <th>Time</th>
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
    </div>
    <script type="text/javascript">
       $("#acmp").select2();
        $('#tableDiv').hide();
        function filterLocCompany(locm_id){ 
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/location/filterCompany",
                data: {
                    id:locm_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#lcmp").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        //   console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].lcmp_name +'</option>';
                    }
                    $("#lcmp").select2();
                    $("#lcmp").append(html);
                },
                error:function(error){
                    console.log(error)
                }
            });
            
        }
        function getReport() {
            $("#cont").empty();
            var locm_id = $('#locm_id').val();
            var lcmp_id = $('#lcmp').val();
            var user_id = $('#user').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/get/location/note/filter",
                data: {
                    locm_id: locm_id,
                    lcmp_id:lcmp_id,
                    user_id:user_id,
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
                            '<td>' + data[i]['note_date'] + '</td>' +
                            '<td>' + data[i]['locm_name'] + '</td>' +
                            '<td>' + data[i]['lcmp_name'] + '</td>' +
                            '<td>' + data[i]['ldpt_name'] + '</td>' +
                            '<td>' + data[i]['lsct_name'] + '</td>' +
                            '<td>' + data[i]['site_code'] + '</td>' +
                            '<td>' + data[i]['locd_name'] + '</td>' +
                            '<td>' + data[i]['aemp_usnm'] + '</td>' +
                            '<td>' + data[i]['aemp_name'] + '</td>' +
                            '<td>' + data[i]['note_body'] + '</td>' +
                            '<td>' + data[i]['geo_addr'] + '</td>' +
                            '<td>' + data[i]['note_dtim'] + '</td>' +

                            '</tr>';
                        count++;
                    }
                    //alert(html);
                    $("#cont").append(html);

                    //$('#datatable').DataTable().draw();
                    $('#tableDiv').show();
                }

            });

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