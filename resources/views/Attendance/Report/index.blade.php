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
                            <h3 style="text-align:center;">Attendance Report</h3>
                            <div class="col-md-1 col-sm-1 col-xs-12">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div  class="col-md-12 col-sm-12 col-xs-12 ">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12" >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Start Date<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date"
                                                name="start_date"
                                                id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12" >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="end_date">End Date<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg end_date"
                                                name="end_date"
                                                id="end_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="report_type">Report Type<span class="required"></span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="report_type"  id="report_type">                                                  >                                               
                                               <option value="1">Details</option>
                                               <option value="2">Summary</option>
                                               
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="role_id">Role<span class="required"> </span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="role_id"  id="role_id">                                                  >                                               
                                                <option value="">All</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{$role->id}}">{{$role->role_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="aemp_usnm">Staff ID<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="aemp_usnm" placeholder="Staff Id" name="aemp_usnm">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-11 col-sm-offset-11">
                                        <button class="btn btn-success" type="submit" onclick="filterData()">Show</button>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-2 col-sm-2 col-xs-12 ">
                                        <a href="#"   onclick="getAttnDetailsRequest()" style="color:blue;">Click here to see requested reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="height:700px;overflow: auto;"id="tbl_attendance">
                                    <div align="right">

                                        <button onclick="exportTableToCSV('attendance<?php echo date('Y_m_d'); ?>.csv','tbl_attendance')"
                                                class="btn btn-warning">Export CSV File
                                        </button>
                                    </div>
                                    <table id="datatablesa" class="table table-bordered table-responsive"
                                           data-page-length='100'>
                                           <thead>
                                                <tr class="tbl_header" id="heading">
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
         $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
         $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
         $('.cmn_select2').select2();
        function filterData() {
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            let aemp_usnm = $("#aemp_usnm").val();
            let report_type=$("#report_type").val();
            let role_id=$("#role_id").val();
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/emp/attendance/report",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    report_type: report_type,
                    aemp_usnm: aemp_usnm,
                    role_id: role_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#cont").empty();
                    $("#heading").empty();
                    $('#ajax_load').css("display", "none");
                    let html="",count=1,head="";

                    if(report_type==1){
                        swal.fire({
                            icon:'success',
                            text:'Attendance Report Details Report Request Submitted Successfully !'
                        })
                        
                    }
                    else{
                        head+='<th>Sl</th>'+
                                '<th>Staff Id</th>'+
                                '<th>Employee Name</th>'+
                                '<th>Designation</th>'+
                                '<th>Mobile</th>'+
                                '<th>Group</th>'+
                                '<th>Region</th>'+
                                '<th>Zone</th>'+
                                '<th>Total Days</th>'+
                                '<th>Payable Days</th>'+
                                '<th>Present</th>'+
                                '<th>IOM</th>'+
                                '<th>Leave</th>'+
                                '<th>Force Leave</th>'+
                                '<th>Absent</th>'+
                                '<th>Gvt Holiday</th>'+
                                '<th>Off day</th>';
                        for (var i = 0; i < data.length; i++) {
                            let pay_days=parseInt(data[i].Attendance)+parseInt(data[i].IOM)+parseInt(data[i].Leave)+parseInt(data[i].Gvt_Holiday)+parseInt(data[i].Off_Day);
                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i].aemp_usnm + '</td>' +
                                '<td>' + data[i].aemp_name + '</td>' +
                                '<td>' + data[i].edsg_name + '</td>' +
                                '<td>'+ data[i].aemp_mob1 +'</td>' +
                                '<td>' + data[i].slgp_name + '</td>' +
                                '<td>' + data[i].dirg_name + '</td>' +
                                '<td>' + data[i].zone_name + '</td>' +
                                '<td>' + data[i].total_days + '</td>' +
                                '<td>' + pay_days + '</td>' +
                                '<td>' + data[i].Attendance + '</td>' +
                                '<td>' + data[i].IOM + '</td>' +
                                '<td>' + data[i].Leave + '</td>' +
                                '<td>' + data[i].Force_Leave + '</td>' +
                                '<td>' + data[i].Absent + '</td>' +
                                '<td>' + data[i].Gvt_Holiday + '</td>' +
                                '<td>' + data[i].Off_Day + '</td>';
                            count++;
                        }
                    }
                    $("#heading").append(head)
                    $("#cont").append(html)


                },error:function(error){
                    $('#ajax_load').css("display", "none");
                    console.log(error);
                }
            });
        }
        function getAttnDetailsRequest(){
            $.ajax({
                type:"Get",
                url: "{{ URL::to('/')}}/emp/attendance/details/request",
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data)
                    $('#heading').empty();
                    $('#cont').empty();
                    var head='<th>SL</th>'+
                                '<th>Report Name</th>'+
                                '<th>Report Link</th>'+
                                '<th>Created At</th>'+
                                '<th>Delivery Email</th>'+
                                '<th>Status</th>';
                    var stat='';
                    var html='';
                    let count=1;
                    for (var i = 0; i < data.length; i++) {
    
                        var color='';
                        if(data[i]['report_status']==1){
                            color="red;"
                            stat='Pending'
                        }
                        else if(data[i]['report_status']==4){
                            color="yellow;"
                            stat='Running'
                        }
                        else if(data[i]['report_status']==2){
                            color="forestgreen";
                            stat='Ready'
                        }
                        else if(data[i]['report_status']==3){
                            color="olive";
                            stat='Delivered'
                        }
                        var file="http://coreapi.sihirfms.com/reports/"+data[i]['report_link'];
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i]['report_name']+ '</td>' +
                            '<td style=""><a href="'+file+'" style="text-decoration:underline;color:blue;">'+file+ '</a></td>' +
                            '<td>' + data[i]['created_at'] + '</td>'+
                            '<td>' + data[i]['aemp_email'] + '</td>'+
                            '<td style="color:'+color+'!important;">' + stat + '</td>';
                            
                        count++;
                    }
                    $('#heading').append(head);
                    $('#cont').append(html);
                                
                },
                error:function(error){
                    console.log(error)
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