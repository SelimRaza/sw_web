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
                <div class="col-md-12">
                    <div class="x_panel" id="x_t">
                        <div class="x_title">
                            <form class="form-horizontal form-label-left" action="#"
                                   enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-1 col-sm-1 col-xs-4" for="name">Company<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-2 col-sm-2 col-xs-8">
                                        <select class="form-control in_tg" name="acmp_id" id="acmp_id"
                                                 style="max-height:18px!important;">
                                            <option value="all">All</option>
                                           @foreach($acmp as $cmp)
                                           <option value="{{$cmp->company_id}}">{{$cmp->company_id}}</option>
                                           @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-1 col-sm-1 col-xs-4" for="name">Report Type<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-2 col-sm-2 col-xs-8">
                                        <select class="form-control in_tg" name="rp_type" id="rp_type">
                                            <option value="1">Detailed</option>
                                            <option value="2">Summary</option>
                                        </select>
                                    </div>
                                    <label class="control-label  col-md-1 col-sm-3 col-xs-4" for="name">From Date<span
                                                class="required">*</span>
                                    </label>

                                    <div class="col-md-2 col-sm-2 col-xs-8">
                                        <input type="text" class="form-control in_tg" name="start_date" id="start_date"
                                               value="<?php echo date('Y/m'); ?>" autocomplete="off"/>
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-4">
                                        <button id="send" type="button"
                                                class="btn btn-primary  col-md-offset-2 col-sm-offset-2 in_tg"
                                                onclick="getReport()">Show
                                        </button>
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-4" align="right">
                                        <button onclick="exportTableToCSV('detailed_tgt_vs_achv_report_<?php echo date('Y_m_d'); ?>.csv','table_div_tgt')"
                                                class="btn btn-danger in_tg">Export
                                        </button>
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-4" align="right">
                                         <a href="#" class="pull-right">
										   <i id="show" onclick="showColum()" class="fa fa-3x fa-eye  pull-right" style="display: none;"></i>
										   <i id="hide" onclick="hideColum()" class="fa fa-3x fa-eye-slash  pull-right"></i>
										  </a>
                                    </div>

                                </div>
                            </form>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div id="tableDiv">
                        <div class="x_panel" id="x_t">

                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:760px;overflow:auto; font-size:10px;" id="table_div_tgt">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    	$('#acmp_id').select2();
        $SIDEBAR_MENU = $('#sidebar-menu')
        $(document).ready(function(){
				setTimeout(function() { 
			        $('#menu_toggle').click();
		 			console.log('clicked ');
			    }, 1);
		});
        function colorCode(p){
            if(p>=80 && p<98){
                return 'orange;color:white;"';
            }
            else if(p>=98){
                return 'green;color:white;"';
            }
            else{
                return 'white;"';
            }
        }
        function showColum(){
            $('#show').hide();
            $('#hide').show();
            $('.cl').show();
        }
        function hideColum(){
            $('#hide').hide();
            $('#show').show();
            $('.cl').hide();
        }
        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var rp_type = $('#rp_type').val();
            var start_date = $('#start_date').val();
            var _token = $("#_token").val();
            $('#table_div_tgt').empty();
            var sub_head='<tr style="font-size:10px;">'+
                        '<th>W</th>'+
                        '<th>Z</th>'+
                        '<th>X</th>'+
                       ' <th>A</th>'+
                        '<th>B</th>'+
                        '<th>C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        ' <th class="cl">C</th>'+
                        ' <th class="cl">A</th>'+
                        ' <th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                       ' <th class="cl">B</th>'+
                       ' <th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                       ' <th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                        '<th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                        '<th class="cl">C</th>'+
                       ' <th class="cl">A</th>'+
                        '<th class="cl">B</th>'+
                       ' <th class="cl">C</th>'
                       '</tr></thead>';
            if (acmp_id != "") {
               $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/get/tgt_vs_achv/report",
                    data: {
                        acmp_id: acmp_id,
                        rp_type: rp_type,
                        start_date: start_date,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<tbody>';
                        var chk ='';
                        var headings='';
                        var percent=0;
                        for (var i = 0; i < data.details.length; i++) {
                            if(chk !=data.details[i]['digr_text']){
                                headings+='<table class="table table-bordered table-responsive" id="'+data.details[i]['digr_text']+'"><thead style="font-size:11px;"><tr>'+
                                            '<th>'+data.details[i]['company_id']+'</th>'+
                                            '<th>'+start_date+'</th>'+
                                            '<th></th>'+
                                            '<th colspan="3">P</th>';
                                var cl=data.acmp_class.filter(cls =>cls.DIGR_TEXT==data.details[i]['digr_text']);
                                //console.log(cl.length);
                                var cnt=0;
                                $.each(cl, function( index, value ) {
                                        cnt++;
                                       
                                        headings += '<th colspan="3" class="cl">'+value['itcl_name']+'</th>';      
                               });
                               for(var m=0;m<9-cnt;m++){
                                   headings +='<th colspan="3" class="cl">N/S</th>';
                               }
                                headings +='<th colspan="3" class="cl">O</th>'+
                                            '</tr>'+sub_head;
                                
                                $('#table_div_tgt').append(headings);
                                headings='';
                                html +='</tbody></table>';
                                
                                    $('#'+chk).append(html);
                                    html='<tbody>';
                                
                               
                            }
                            chk=data.details[i]['digr_text'];
                            html += '<tr>' +
                                '<td>' + data.details[i]['digr_name'] + '</td>' +
                                '<td>' + data.details[i]['zone_name'] + '</td>' +
                                '<td>' + data.details[i]['zone_code'] + '</td>';
                                if(data.details[i]['total_tgt']){
                                    percent=((100*data.details[i]['total_achv'])/ data.details[i]['total_tgt']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'">' + data.details[i]['total_tgt'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'">' + data.details[i]['total_achv'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td>' + data.details[i]['total_tgt'] + '</td>' +
                                            '<td>' + data.details[i]['total_achv'] + '</td>'+
                                            '<td>0.00%</td>';
                                }

                                if(data.details[i]['plv1']){
                                    percent=((100*data.details[i]['aplv1'])/ data.details[i]['plv1']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv1'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv1'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv1'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv1'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv2']){
                                    percent=((100*data.details[i]['aplv2'])/ data.details[i]['plv2']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv2'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv2'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv2'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv2'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv3']){
                                    percent=((100*data.details[i]['aplv3'])/ data.details[i]['plv3']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv3'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv3'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv3'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv3'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv4']){
                                    percent=((100*data.details[i]['aplv4'])/ data.details[i]['plv4']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv4'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv4'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv4'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv4'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv5']){
                                    percent=((100*data.details[i]['aplv5'])/ data.details[i]['plv5']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv5'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv5'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv5'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv5'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv6']){
                                    percent=((100*data.details[i]['aplv6'])/ data.details[i]['plv6']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv6'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv6'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv6'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv6'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv7']){
                                    percent=((100*data.details[i]['aplv7'])/ data.details[i]['plv7']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv7'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv7'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv7'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv7'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv8']){
                                    percent=((100*data.details[i]['aplv8'])/ data.details[i]['plv8']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv8'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv8'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv8'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv8'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['plv9']){
                                    percent=((100*data.details[i]['aplv9'])/ data.details[i]['plv9']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['plv9'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aplv9'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['plv9'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aplv9'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                if(data.details[i]['other']){
                                    percent= ((100*data.details[i]['aother'])/ data.details[i]['other']).toFixed(2);
                                    
                                    html += '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['other'] + '</td>' +
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + data.details[i]['aother'] + '</td>'+
                                            '<td style="background-color:'+colorCode(percent)+'" class="cl">' + percent+'%' + '</td>';
                                }else{
                                    html += '<td class="cl">' + data.details[i]['other'] + '</td>' +
                                            '<td class="cl">' + data.details[i]['aother'] + '</td>'+
                                            '<td class="cl">0.00%</td>';
                                }
                                html +='</tr>';
                                if(i==data.details.length-1){
                                    html +='</tbody></table>';
                                    $('#'+chk).append(html);
                                    
                                }
                        }
                        
                    },error:function(params) {
                        console.log(params);
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
         $(function() {
            $('#start_date').datepicker( {
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy/mm',
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
            });
        });
         


    </script>

@endsection