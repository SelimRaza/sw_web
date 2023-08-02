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
                            <strong>All Promotion</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/promotion')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for......."
                                       value="">
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
                        <div class="x_title">
                            @if($permission->wsmu_crat)
                                <a href="{{ URL::to('promotion/create/new')}}" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add New
                                    Promotion SP</a>

                            @endif

                            <div class="clearfix"></div>
                        </div>
                        <form class="form-horizontal form-label-left" action=""
                              method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            {{csrf_field()}}
                            <div class="x_title">
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                               for="name" style="text-align: left">Company<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="acmp_id" id="acmp_id"
                                                    onchange="getGroup(this.value)">
                                                <option value="">Select Company</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                               style="text-align: left">Group<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="slgp_id" id="slgp_id">
                                                <option value="">Select Group</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <button id="send" type="button" style="margin-top: 22px; margin-left:10px;"
                                                class="btn btn-success"
                                                onclick="getReport()"><span class="fa fa-search"
                                                                            style="color: white;"></span>
                                            <b>Search</b>
                                        </button>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">



                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <button class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('promotion_list_<?php echo date('Y_m_d'); ?>.csv','datatabless')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>
                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Promotion Name</th>
                                    <th>Promotion Code</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Area Type</th>
                                    <th>Promotion Type</th>
                                    <th>Discount Type</th>
                                    <th>Promotion Qualifier</th>
                                    <th>Group Name</th>
                                    <th>Promotion On</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Action</th>

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

    {{--promotion data extend modal end--}}
    <script type="text/javascript">
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };


        function exportTableToCSV(filename, tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#' + tableId + '  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length - 1; j++)
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
    </script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('table.search-table').tableSearch({
                searchPlaceHolder: 'Search Text'
            });
        });

        $("#div_report").hide();
        $(document).ready(function () {
            $("select").select2({width: 'resolve'});
        });
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function getGroup(slgp_id) {
            $("#slgp_id").empty();
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

                    $("#slgp_id").append(html);

                }
            });
        }

        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var slgp_id = $('#slgp_id').val();
            var html = '';

            $("#div_report").hide();
            var _token = $("#_token").val();
            if (acmp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/promotion_sp/filter/report",
                    data: {
                        acmp_id: acmp_id,
                        slgp_id: slgp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        var count = 1;
                        var a_status = "";
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            var lfcl_id = data[i]['lfcl_id'];
                            var btn_active = "";
                            if (lfcl_id == '1') {
                                a_status = 'Active';
                                btn_active = '';

                            } else {
                                a_status = 'Inactive';
                                btn_active = '<a href="promotion_sp/p_copy/' +r_id+ '" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Copy As </a>&nbsp;&nbsp;';
                            }


                            html += '<tr class="tbl_body_gray">' +
                                '<td class="cell_left_border">' + count + '</td>' +
                                '<td>' + data[i]['prms_name'] + '</td>' +
                                '<td>' + data[i]['prms_code'] + '</td>' +
                                '<td>' + data[i]['prms_sdat'] + '</td>' +
                                '<td>' + data[i]['prms_edat'] + '</td>' +
                                '<td>' + data[i]['area_type'] + '</td>' +
                                '<td>' + data[i]['prom_type'] + '</td>' +
                                '<td>' + data[i]['discount_type'] + '</td>' +
                                '<td>' + data[i]['prom_qualifier'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['prom_on'] + '</td>' +
                                '<td>' + a_status + '</td>' +

                                '<td><a href="promotion_sp/show/' + r_id + '" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View</a>&nbsp;&nbsp;' +
                                '<a href="promotion_sp/extend_date/' + r_id + '" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit </a>&nbsp;&nbsp;' +
                                '<span>'+ btn_active +'</span>' +
                                '</td></tr>';


                            if (lfcl_id == '1') {

                                /*btn_active = "#";
                                document.getElementById(r_id).hide();
                                document.getElementById("46").style.visibility = "hidden";*/
                                $("#46").hide();
                            } else {
                                $("#46").show();
                            }
                            count++;
                        }
                        //alert(html);
                        $("#div_report").show();
                        $("#cont").append(html);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;


                        //$('#datatable').DataTable().draw();
                        $('#tableDiv').show();
                    }

                });
            } else {
                alert("Please select Group and Try again!!!");
            }

        }

    </script>
@endsection

<?php //echo $promotion->lfcl_id == 1 ? 'Active' : 'Inactive'?>