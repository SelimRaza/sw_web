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
                            <strong>New Reports</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        

                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Company
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="acmp_id" id="acmp_id"
                                            onchange="getGroup(this.value,1)">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $companies1)
                                            <option value="{{ $companies1->id }}">{{ ucfirst($companies1->acmp_name)."(".$companies1->acmp_code.")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="slgp_id">Group
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="slgp_id" id="slgp_id"
                                            onchange="">

                                        <option value="">Select Group</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6 gvt">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="dist_id">Category<span
                                                class="required"></span>
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="otcg_id" id="otcg_id"
                                            >
                                        <option value="">Select Category</option>
                                        @foreach($otcg_list as $otcg)
                                            <option value="{{ $otcg->id }}">{{ ucfirst($otcg->otcg_name) }}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6 gvt">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="chnl_id">Channel
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="chnl_id" id="chnl_id"
                                            onchange="getSubchannel(this.value)">
                                        <option value="">Select Channel</option>
                                        @foreach($channel_list as $chnl)
                                            <option value="{{ $chnl->id }}">{{ ucfirst($chnl->chnl_code)."(".$chnl->chnl_name.")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-6 ">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="scnl_id">Subchannel<span
                                            class="required"></span>
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="scnl_id" id="scnl_id"
                                               >
                                            <option value="">Select Subchannel</option>
                                            
                                        </select>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-6 " >
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date">Site Code
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input id="site_code" class="form-control col-md-7 col-xs-12 in_tg"
                                    data-validate-length-range="6" data-validate-words="2" name="site_code"
                                    placeholder="Site Code"  type="text">
                                </div>
                            </div>
                        </div>
                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>
                            <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv')"
                                    class="btn btn-success">Export CSV File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                           {{-- @if($permission->wsmu_crat)
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/companySiteMapping/create')}}">Setup Company
                                    Site mapping</a>
                            @endif--}}
                            <h1>User wise Order Tracking Report</h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Company</th>
                                    <th>Group</th>
                                    <th>Price List</th>
                                    <th>Site Code</th>
                                    <th>Site Name</th>
                                    <th>Credit Limit</th>
                                    <th>Limit Day</th>
                                    <th>Credit Limit Type</th>
                                    <th>Payment Type</th>
                                    <th>Due Amount</th>
                                    <th>BC Amount</th>
                                    <th>Over Due Amount</th>
                                    <th>Order Amount</th>
                                    <th>Verified Amount</th>
                                    <th>Not Verified Amount</th>
                                    <th>Action</th>
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
    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        var user_name = $("#user_name").val();
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
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

        function filterData() {
            let site_code = $("#site_code").val();
            let acmp_id = $("#acmp_id").val();
            let slgp_id = $("#slgp_id").val();
            let otcg_id = $("#otcg_id").val();
            let chnl_id = $("#chnl_id").val();
            let scnl_id = $("#scnl_id").val();
            let _token = $("#_token").val();
            if(site_code=='' && slgp_id==''){
                swal.fire({
                    icon:'error',
                    text:'Please provide site code or select atleast group!!!',
                });
                return false;
            }
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/NewReport/filter3",
                data: {
                    site_code: site_code,
                    acmp_id: acmp_id,
                    slgp_id: slgp_id,
                    otcg_id: otcg_id,
                    chnl_id: chnl_id,
                    scnl_id: scnl_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    let html = '';
                    let count = 1;
                    for (let i = 0; i < data.length; i++) {

                        let bc_amount = data[i].stcm_duea-data[i].non_verified;
                        let stcm_isfx = data[i].stcm_isfx==0? 'Variable':'Fixed';
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].acmp_name + '</td>' +
                            '<td>' + data[i].slgp_name + '</td>' +
                            '<td>' + data[i].plmt_name + '</td>' +
                            '<td>' + data[i].site_code + '</td>' +
                            '<td>' + data[i].site_name + '</td>' +
                            '<td>' + data[i].stcm_limt + '</td>' +
                            '<td>' + data[i].stcm_days + '</td>' +
                            "<td>" + stcm_isfx +"</td>" +
                            '<td>' + data[i].optp_name + '</td>' +
                            '<td>' + data[i].stcm_duea + '</td>' +
                            "<td>" +  bc_amount+ "</td>" +
                            "<td>" + data[i].stcm_odue + "</td>" +
                            '<td>' + data[i].stcm_ordm + '</td>' +
                            '<td>' + data[i].pdc_amount + '</td>' +
                            '<td>' + data[i].non_verified + '</td>';
                        html += "<td><a  href='{{ URL::to('/')}}/site/credit-adjust/" + data[i].site_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Adjust</a></td></tr>";
                            
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
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
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }
                    $('#slgp_id').empty();
                    $('#slgp_id').append(html);
                    
                }
            });
        }
        function getSubchannel(chnl_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/getSubChannel/"+chnl_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">'  + data[i].scnl_name + '</option>';
                    }
                    $('#scnl_id').empty();
                    $('#scnl_id').append(html);
                    
                },
                error:function(error){
                    $('#ajax_load').css("display", "none");
                }
            });
        }
    </script>
@endsection