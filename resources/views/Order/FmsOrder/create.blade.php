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
                            <h3 style="text-align:center;">Order</h3>
                            <div class="col-md-1 col-sm-1 col-xs-12">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            
                        <form action="{{route('fmsorder.store')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                            <div  class="col-md-12 col-sm-12 col-xs-12 ">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12" >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Order Date<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date"
                                                name="start_date"
                                                id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off" readonly/>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Group <span class="required">*</span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="slgp_id"  id="slgp_id">                                                  >                                               
                                               <option value="">Select</option>
                                                @foreach($slgp_list as $slgp)
                                                    <option value="{{$slgp->slgp_id}}">{{$slgp->slgp_code.'-'.$slgp->slgp_name}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="aemp_id">SR ID <span class="required"> *</span>
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
                                            for="aemp_id">Order Depot<span class="required"> *</span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="depot_id"  id="depot_id">                                                  >                                               
                                               <option value="">Select</option>
                                                @foreach($depot_list as $depot)
                                                    <option value="{{$depot->id}}">{{$depot->id.'-'.$depot->dpot_name}}</option>
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
                                <div class="form-group col-md-6 col-sm-6 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">File <span class="required"> *</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="file" class="form-control in_tg" id="order_file" placeholder="" name="order_file">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <a href="{{URL::TO('download/sv/order/format')}}" style="color:darkred;margin-bottom:15px;">Click here to download format</a>
                                    <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-10 col-sm-offset-10">
                                        <button class="btn btn-success btn-block" type="submit">save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
        $SIDEBAR_MENU = $('#sidebar-menu')
        $(document).ready(function () {
            setTimeout(function () {
                $('#menu_toggle').click();
            }, 1);
        });
        function filterData() {
            let acmp_id = $("#acmp_id").val();
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            let emp_id = $("#staff_id").val();
            let sp_id = $("#sp_id").val();
            let ordr_id = $("#order_id").val();
            let depot_id = $("#depot_id").val();
            let dirg_id = $("#dirg_id").val();
            let scnl_id = $("#scnl_id").val();
            let site_code = $("#site_code").val();
            let lfcl_id = $("#block_type").val();
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/depo/order/details",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    acmp_id:acmp_id,
                    emp_id: emp_id,
                    sp_id:sp_id,
                    ordr_id:ordr_id,
                    depot_id:depot_id,
                    dirg_id:dirg_id,
                    scnl_id:scnl_id,
                    site_code: site_code,
                    lfcl_id: lfcl_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    for (var i = 0; i < data.length; i++) {
                        let lfcl='';
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].ordm_date + '</td>' +
                            '<td>' + data[i].acmp_name + '</td>' +
                            '<td>' + data[i].slgp_name + '</td>' +
                            '<td>' + data[i].ordm_ornm + '</td>' +
                            '<td>' + data[i].dlrm_id + '</td>' +
                            '<td>' + data[i].dlrm_name + '</td>' +
                            '<td>'+ data[i].trip_no +'</td>' +
                            '<td>' + data[i].sr_name + '</td>' +
                            '<td>' + data[i].site_name + '</td>' +
                            "<td>" + data[i].dirg_name + "</td>";
                        if(data[i].lfcl_id==1){                           
                            html+='<td style="background-color:green;color:white;"><span>'+data[i].lfcl_name+'</span></td>';
                        }else if(data[i].lfcl_id==11){
                            html+='<td ><span >'+data[i].lfcl_name+'</span></td>';
                        }
                        else if(data[i].lfcl_id==9||data[i].lfcl_id==18 ||data[i].lfcl_id==14){
                            html+='<td style="background-color:darkred;color:white;"><span>'+data[i].lfcl_name+'</span></td>';
                        }
                        else{
                            html+='<td>'+data[i].lfcl_name+'</td>';
                        }
                        html+="<td><a class='btn btn-success btn-xs' href='{{ URL::to('/')}}/single/order/details/" + data[i].id+"' ><i class='fa fa-eye'></i>View</a>";
                        if(data[i].lfcl_id==1){
                            lfcl="Actv";
                            html+='<a class="btn btn-danger btn-xs" status="'+data[i].lfcl_id+'" id="'+data[i].id+'" onclick="changeLifeCycle(this)"><i class="fa fa-remove"></i>'+lfcl+'</a>';
                        }else if(data[i].lfcl_id==21){
                            lfcl='Cncl';
                            html+='<a class="btn btn-danger btn-xs" status="'+data[i].lfcl_id+'" id="'+data[i].id+'" onclick="changeLifeCycle(this)"><i class="fa fa-remove"></i>'+lfcl+'</a>';
                        }
                        
                                
                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                },error:function(error){
                    $('#ajax_load').css("display", "none");
                    console.log(error);
                }
            });
        }

        function changeLifeCycle(v){
            let id=$(v).attr('id');
            let status=$(v).attr('status');
           // return confirm("Are you sure?")
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/order/lfcl/change/"+id+"/"+status,
                dataType:"json",
                success:function(data){
                    swal.fire({
                        icon:'success',
                        text:'Order life cycle changed successfully',
                    })
                },
                error:function(error){
                    swal.fire({
                        icon:'error',
                        text:'Something Went Wrong !!!',
                    })
                }
            })
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