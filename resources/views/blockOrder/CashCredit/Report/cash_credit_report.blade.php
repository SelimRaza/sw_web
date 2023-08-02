@extends('theme.app')
@section('content')
    <div class="right_col" role="main">
        <div class="">
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h3 style="text-align:center;">Cash Party Budget Summary</h3>
                            <div class="col-md-1 col-sm-1 col-xs-12">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div  class="col-md-12 col-sm-12 col-xs-12 ">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12" >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Month Year<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date"
                                                name="start_date"
                                                id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="aemp_id">Supervisor ID<span class="required"></span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="aemp_id"  id="aemp_id">                                                  >                                               
                                               <option value="">Select</option>
                                                @foreach($sr_list as $sr)
                                                    <option value="{{$sr->id}}">{{$sr->aemp_usnm.'-'.$sr->aemp_name}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="sr_id">SR ID<span class="required"></span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="sr_id"  id="sr_id">                                                  >                                               
                                               <option value="">Select</option>
                                                @foreach($sales_p as $sr)
                                                    <option value="{{$sr->aemp_usnm}}">{{$sr->aemp_usnm.'-'.$sr->aemp_name}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="chnl_id">Channel<span class="required"> </span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="chnl_id"  id="chnl_id">                                                  >                                               
                                               <option value="">Select</option>
                                                @foreach($chnl as $cnl)
                                                    <option value="{{$cnl->id}}">{{$cnl->chnl_name}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </div>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="site_code">Site Code<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="site_code" placeholder="Site Code" name="site_code">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-11 col-sm-offset-11">
                                        <button class="btn btn-success" type="submit" onclick="filterData()">Show</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;"id="tbl_cash_budget">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('cash_budget<?php echo date('Y_m_d'); ?>.csv','tbl_cash_budget')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                           <thead>
                                                <tr class="">
                                                    <th>SI</th>
                                                    <th>SV ID</th>
                                                    <th>SV Name</th>
                                                    <th>Period</th>
                                                    <th>Limit</th>
                                                    <th>Cost</th>
                                                    <th>Balance</th>
                                                    <th>SR ID</th>
                                                    <th>SR Name</th>
                                                    <th>Chnl Name</th>
                                                    <th>Site Code</th>
                                                    <th>Site Name</th>
                                                    <th>Order Amnt</th>
                                                    <th>CredR Amnt</th>
                                                    <th>Approved Amnt</th>
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
    <script type="text/javascript">
         $('#start_date').datetimepicker({format: 'YYYY-MM'});
         $('.cmn_select2').select2();
        function filterData() {
            let start_date = $("#start_date").val();
            let aemp_id = $("#aemp_id").val();
            let sr_id = $("#sr_id").val();
            let site_code = $("#site_code").val();
            let chnl_id = $("#chnl_id").val();
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/cash-credit/report",
                data: {
                    start_date: start_date,
                    aemp_id: aemp_id,
                    sr_id: sr_id,
                    site_code: site_code,
                    chnl_id: chnl_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    let html="",count=1;
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].aemp_usnm + '</td>' +
                            '<td>' + data[i].aemp_name + '</td>' +
                            '<td>' + data[i].mnth_year + '</td>' +
                            '<td>' + data[i].spbm_limt + '</td>' +
                            '<td>' + data[i].spbm_avil + '</td>' +
                            '<td>' + data[i].spbm_amnt + '</td>' +
                            '<td>' + data[i].sr_id + '</td>' +
                            '<td>' + data[i].sr_name + '</td>' +
                            '<td>' + data[i].chnl_name + '</td>' +
                            '<td>'+ data[i].site_code +'</td>' +
                            '<td>' + data[i].site_name + '</td>' +
                            '<td>' + data[i].ordm_amnt + '</td>' +
                            "<td>" + data[i].sreq_amnt + "</td>"+  
                            "<td>" + data[i].sapr_amnt + "</td></tr>";  
                        count++;
                    }

                    $("#cont").append(html)


                },error:function(error){
                    $('#ajax_load').css("display", "none");
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
    </script>
@endsection