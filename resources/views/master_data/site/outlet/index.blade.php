@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
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
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/create')}}">Add New</a>
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/siteUpload')}}">Upload</a>
                                <a class="btn btn-success btn-sm" href="{{ URL::to('/site/unverified')}}">Unverified List</a>
                            @endif
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
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <div class="x_content">
                                <form class="form-horizontal form-label-left">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="x_title">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                    for="dist_name" style="text-align: left">District<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="dsct_id" id="dsct_id"
                                                        onchange="getThana(this.value)"   >
                                                        <option value="">Select</option>
                                                        @foreach($dist as $dst)
                                                            <option value="{{$dst->id}}">{{$dst->dsct_code}}
                                                                - {{$dst->dsct_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Thana<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="than_id" id="than_id">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Category<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="otcg_id" id="otcg_id">
                                                        <option value="">Select</option>
                                                        @foreach($otcg as $otg)
                                                            <option value="{{$otg->id}}">{{$otg->otcg_code}}
                                                                - {{$otg->otcg_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Channel<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="chnl_id" id="chnl_id" onchange="getSubChannel(this.value)">
                                                        <option value="">Select</option>
                                                        @foreach($chnl as $cnl)
                                                            <option value="{{$cnl->id}}">{{$cnl->chnl_code}}
                                                                - {{$cnl->chnl_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Sub Channel<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="scnl_id" id="scnl_id">
                                                        <option value="">Select</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Life Cycle<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="lfcl_id" id="lfcl_id">
                                                        <option value="">All</option>
                                                        <option value="1">Active</option>
                                                        <option value="2">Inactive</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Verified/Unverified<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="site_vrfy" id="site_vrfy">
                                                        <option value="">All</option>
                                                        <option value="1">Verified</option>
                                                        <option value="0">Unverified</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Site Code<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="site_code" id="site_code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Vat-Tran No<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="site_vtrn" id="site_vtrn">
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <div class="item form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <button id="send" type="button" style="margin-left:10px;"
                                                        class="btn btn-success"
                                                        onclick="getOutletMaster()"><span class="fa fa-search"
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
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-sx-12">
                    <div class="x_panel">
                        <div class="x_content" style="overflow-x:auto;">
                            <table id="olt_table" class="table table-bordered projects table-responsive">
                                    <thead>
                                    <tr class="tbl_header">
                                        <th>SL</th>
                                        <th>Outlet Id</th>
                                        <th>Outlet Code</th>
                                        <th>Vat Trn</th>
                                        <th>Outlet Name1</th>
                                        <th>Outlet Address1</th>
                                        <th>Outlet Mobile1</th>
                                        <th>Outlet Owner</th>
                                        <th>Sub chanel</th>
                                        <th>Category</th>
                                        <th>Market</th>
                                        <th style="width: 20%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    
                                    </tbody>
                            </table>
                            @php
                                $site=(object)[];
                                function setSite($a){
                                    $site=$a;
                                }
                            @endphp
                            <p id="pagination"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('.select2').select2();
        $SIDEBAR_MENU = $('#sidebar-menu')
        $(document).ready(function () {
            setTimeout(function () {
                $('#menu_toggle').click();
            }, 1);
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
        function getSubChannel(id){
            if(id !=''){
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/getSubChannel/"+id,
                    success:function(data){
                        $('#ajax_load').hide();
                        var html='<option value="">Select</option>';
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].id+'">'+data[i].scnl_name+'</option>';
                        }
                        $('#scnl_id').empty();
                        $('#scnl_id').append(html);
                    },error:function(error){
                        console.log(error);
                    }
                });
            }else{
                $('#sub_channel_id').empty();
            }
        }
        function getThana(dist_id) {
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/')}}/json/get/market_open/thana_list",
                    data: {
                        district_id: dist_id
                    },

                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        var html = '<option value="">Select </option>';
                        $('#than_id').empty();
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id + '">' + data[i].than_name + '</option>';
                        }
                        $('#than_id').append(html);
                    }
                });
        }
        function getOutletMaster(){
            let dsct_id=$('#dsct_id').val();
            let than_id=$('#than_id').val();
            let chnl_id=$('#chnl_id').val();
            let scnl_id=$('#scnl_id').val();
            let otcg_id=$('#otcg_id').val();
            let lfcl_id=$('#lfcl_id').val();
            let site_vrfy=$('#site_vrfy').val();
            let site_code=$('#site_code').val();
            let site_vtrn=$('#site_vtrn').val();
            let _token=$('#_token').val();
            $('#ajax_load').show();
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/site/getOutletMaster",
                data:{
                    dsct_id:dsct_id,
                    than_id:than_id,
                    otcg_id:otcg_id,
                    chnl_id:chnl_id,
                    scnl_id:scnl_id,
                    lfcl_id:lfcl_id,
                    site_vrfy:site_vrfy,
                    site_code:site_code,
                    site_vtrn:site_vtrn,
                    _token:_token

                },
                dataType:"json",
                success:function(data){       
                    $('#ajax_load').hide();
                    $('#olt_table').DataTable().destroy(); 
                    let count=1;
                    let html="";
                    let site=data.sites;
                    let permission=data.permission;
                    for(let i=0;i<site.length;i++){
                        html+='<tr>'+
                                '<td>'+count+'</td>'+
                                '<td>'+site[i].site_id+'</td>'+
                                '<td>'+site[i].site_code+'</td>'+
                                '<td>'+site[i].site_vtrn+'</td>'+
                                '<td>'+site[i].site_name+'</td>'+
                                '<td>'+site[i].site_adrs+'</td>'+
                                '<td>'+site[i].site_mob1+'</td>'+
                                '<td>'+site[i].site_olnm+'</td>'+
                                '<td>'+site[i].scnl_name+'</td>'+
                                '<td>'+site[i].otcg_name+'</td>'+
                                '<td>'+site[i].mktm_name+'</td>';
                        if(permission.wsmu_vsbl){
                            html+='<td><a href="site/' + site[i].site_id + '" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View</a>&nbsp;&nbsp;';
                        }
                        if(permission.wsmu_updt){
                            html+='<a href="site/' + site[i].site_id + '/edit" class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit </a>&nbsp;&nbsp;';
                        }
                        if(permission.wsmu_delt){
                            html+= '<form style="display:inline" action="site/lfcl_change/' + site[i].site_id + '" class="pull-xs-right5 card-link" method="POST">{{csrf_field()}}{{method_field("DELETE")}}<input class="btn btn-danger btn-xs" type="submit" value="'+site[i].lfcl_name+'" onclick="return ConfirmReset()"></input></form></td></tr>';
                        }                            
                        count++;                                
                    }
                    $('#cont').empty();
                    $('#cont').append(html);
                    
                    $('#olt_table').DataTable({
                        dom: 'Bfrtip',
                        retrieve: true,
                        pageLength:5,
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ]
                    });
                },error:function(error){
                    console.log(error);
                    $('#ajax_load').hide();
                    swal.fire({
                        icon:'error',
                        text:'Something Went Wrong!!!'
                    })
                }
                
            })
        }
    </script>
@endsection