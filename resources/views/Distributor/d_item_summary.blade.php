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
                            <strong>Distributor</strong>
                        </li>
                        <li class="active">
                            <strong>Challan Summary</strong>
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
                            <center><strong> ::: Challan Summary Report :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="x_panel">
                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action=""
                                  method="post" enctype="multipart/form-data">
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

                                    <div class="item form-group">
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales
                                            Man<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control" name="sr_id" id="sr_id">
                                                <option value="">Select Sales Man</option>
                                                @foreach($sr as $srList)
                                                    <option value="{{$srList->sr_id}}">{{$srList->aemp_usnm}}
                                                        - {{$srList->aemp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

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
                                            <th>SI</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Rate</th>
                                            <th>Quantity</th>
                                            <th>Total Amount</th>
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

        function getReport() {
            $("#cont").empty();
            var sr_id = $('#sr_id').val();
            var start_date = $('#start_date').val();
            var _token = $("#_token").val();
            //alert(acmp_id);

            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/filter/distributorOrderItemSummary",
                data: {
                    sr_id: sr_id,
                    from_date: start_date,
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
                            '<td>' + data[i]['ordm_date'] + '</td>' +
                            '<td>' + data[i]['slgp_name'] + '</td>' +
                            '<td>' + data[i]['amim_code'] + '</td>' +
                            "<td>" + (data[i]['amim_name']) + "</td>" +
                            "<td>" + data[i]['ordd_uprc'] + "</td>" +
                            "<td>" + (data[i]['t_qnty']) + "</td>" +
                            "<td>" + (data[i]['t_amnt']) + "</td>" +
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