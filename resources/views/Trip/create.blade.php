@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <!-- <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <a href="{{ URL::to('/maintain_trip')}}">All TRIP</a>
                        </li>
                        <li class="active">
                            <strong>New TRIP </strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div> -->
            <div class="clearfix"></div>

            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
               
             @if($errors->any())
                 <div class="alert alert-danger" style="font-family:sans-serif;">
                     <p><strong>Opps Something went wrong</strong></p>
                     <ol>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error}}</li>
                     @endforeach
                     </ol>
                 </div>
             @endif
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
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
                                    <!-- <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li> -->
                                </ul>
                                <div id="exTab1" class="container">
                                    <ul class="nav nav-pills">
                                        <li>
                                            <a href="#1a" data-toggle="tab"
                                                onclick="getTripCreateArea()">TRIP </a>
                                        </li>
                                        <li><a href="#2a" data-toggle="tab" onclick="getInvoiceAssignArea()">
                                                INVOICE ASSIGN</a>
                                        </li>
                                        <li><a href="#2a" data-toggle="tab" onclick="getItemArea()">
                                                DM ASSIGN</a>
                                        </li>
                                        @if(Auth::user()->country()->module_type==2)
                                        <li><a href="#3a" data-toggle="tab" onclick="getSiteArea()">
                                                SITE</a>
                                        </li>
                                        @endif
                                        @if(Auth::user()->country()->module_type==1)
                                        <li><a href="#3a" data-toggle="tab" onclick="getSlgpZoneArea()">
                                                GROUP</a>
                                        </li>
                                        @endif
                                    </ul>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form class="form-horizontal form-label-left">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                        <div class=" " id="tp_create_div">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="acmp_id">Company<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="acmp_id"
                                                                    id="acmp_id" onchange="getDelarList(1)"
                                                                  >
                                                        <option value="">Select</option>
                                                        @foreach ($acmp_list as $acmp)
                                                            <option value="{{ $acmp->acmp_code }}">{{ $acmp->acmp_code . '-' . $acmp->acmp_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_name">Trip Type<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="tp_type"
                                                                    id="tp_type"
                                                                    >
                                                                <option value="PS">DM Trip</option>
                                                                <option value="VS">VAN Trip</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_code">Depot<span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="dlrm_id" id="dlrm_id" onchange="getDmList(1)">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_code">DM<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="dm_id" id="dm_id">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_code">Vehicle<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="vhcl_id" id="vhcl_id">
                                                        <option value="">Select</option>
                                                        @foreach ($vhcl_list as $vhcl)
                                                            <option value="{{ $vhcl->vhcl_id }}">{{ $vhcl->vhcl_id }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tp_date">Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="tp_date" name="tp_date"
                                                           class="form-control col-md-7 col-xs-12 in_tg msp_date" autocomplete="off"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                            type="text" value="<?php echo date('Y-m-d');?>">
                                                </div>
                                            </div>
                                           <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                   <button type="button" class="btn btn-primary" style="float:right;" onclick="createTrip()">Create</button>
                                                </div>
                                           </div>
                                        </div>                                    
                                </form>
                                <!-- check -->
                            </div>
                            <div class="x_content" id="invoice_assign">
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="in_acmp_id">Company<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="in_acmp_id"
                                                                id="in_acmp_id" onchange="getDelarList(2)"
                                                                >
                                                    <option value="">Select</option>
                                                    @foreach ($acmp_list as $acmp)
                                                        <option value="{{ $acmp->acmp_code }}">{{ $acmp->acmp_code . '-' . $acmp->acmp_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">Depot<span
                                                    class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="in_dlrm_id" id="in_dlrm_id" onchange="getDmList(2)">

                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">DM ID<span
                                                    class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="in_dm_id" id="in_dm_id" onchange="getTripList()">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">Trip Date<span
                                                    class="required"></span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input id="in_tp_date" name="in_tp_date"
                                                            class="form-control col-md-7 col-xs-12 in_tg msp_date" autocomplete="off"
                                                            data-validate-length-range="6" data-validate-words="2"
                                                            type="text" onchange="getTripList()">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">Trip No<span
                                                    class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control cmn_select2" name="in_trip_no" id="in_trip_no">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">Load Type<span
                                                    class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <label>
                                                        <input type="radio" class="flat" name="load_type"
                                                            id="reportType"
                                                            value="1"/>&nbsp;SR&nbsp;&nbsp;&nbsp;&nbsp;
                                                    </label>
                                                    <label>
                                                        <input type="radio" class="flat" name="load_type"
                                                            id="reportType"
                                                            value="2"/>&nbsp;DM Route
                                                    </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 div_hide in_sr_list_div">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="in_sr_list">SR List<span
                                                    class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <select class="form-control multiSelect" name="in_sr_list[]" id="in_sr_list" multiple>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sv_id">
                                            </label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <button type="button" class="btn btn-primary btn-block" onclick="getInvoiceList()"> Load Invoice </button>
                                            </div>
                                        </div>
                                        <div id="" class=" col-md-12 col-sm-12 col-xs-12 div_hide" style="margin-top:20px!important;">
                                            <div class="x_panel">

                                                <div class="x_content">
                                                    <div class="col-md-12 col-sm-12 col-xs-12" style="height:400px;overflow: auto;">
                                                        <table id="trip_details_table" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;"
                                                            data-page-length='100'>
                                                            <thead id="tl_dynamic_head" class="tbl_header" style="position:sticky; inset-block-start:0;">
                                                                <tr>
                                                                    <th>SL</th>
                                                                    <th>SR</th>
                                                                    <th>Invoice</th>
                                                                    <th>Ordr Amnt</th>
                                                                    <th>Invc Amnt</th>
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody id="trip_details_body">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                                <div class="col-md-9 col-sm-9 col-xs-12" style="">
                                    <!-- start -->
                                    <div id="tbl_dynamic" class="div_hide">
                                        <div class="x_panel">

                                            <div class="x_content">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="in_sr_list">New DM
                                                        </label>
                                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                                            <input type="text" name="new_dm" id="new_dm" class="in_tg form-control" placeholder="DM Staff ID">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 col-xs-12" style="float:right!important;">
                                                        <button type="button" class="btn btn-primary float-right" onclick="storeTripDetails()"> Save </button>
                                                    </div>
                                                       
                                                        
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="height:75vh;overflow: auto;">
                                                    <table id="tl_dynamic" class="table table-bordered table-responsive" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;height:100%"
                                                        data-page-length='100'>
                                                        <thead id="tl_dynamic_head" class="tbl_header" style="position:sticky; inset-block-start:0;">
                                                            <tr>
                                                                <th><input type="checkbox" id="group_all">All</th>
                                                                <th>Date</th>
                                                                <th>Order No</th>
                                                                <th>Outlet</th>
                                                                <th>SR Name</th>
                                                                <th>Item Name</th>
                                                                <th>Order Qty</th>                                                                
                                                                <th>Vat & Excise</th>
                                                                <th>Invoice Qty</th>
                                                                <th>Sub Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tl_dynamic_cont">
                                                         </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- end -->
                                </div>
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(".cmn_select2").select2();
        $('.multiSelect').multiselect({
            columns: 1,
            search: true,
            selectAll: true,
            texts: {
                placeholder: 'Select',
                search: 'Search'
            }
        });
        function hide(){
            $('#tp_create_div').hide();
            $('#invoice_assign').hide();
            $('.in_sr_list_div').hide();
            $('#all_invoices').hide();
        }
        $(".msp_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
            });
        hide();
        // Area Show Hide
        function getTripCreateArea(){
            hide();
            $('#tp_create_div').show();
        }
        function getInvoiceAssignArea(){
            hide();
            $('#invoice_assign').show();
        }
        // *******************Trip All Function Start ****************************************
        //==> Helper Funtion
        // Checkbox All, Single Checked
        $(document).ready(function() {
            $('#group_all').change(function() {
                var isChecked = $(this).prop('checked');
                $('.invoice_check').prop('checked', isChecked);
            });
        });
        function adjustAllSelect(){
            var totalCheckboxes = $('.invoice_check').length;
            var checkedCheckboxes = $('.invoice_check:checked').length;
            $('#group_all').prop('checked', totalCheckboxes === checkedCheckboxes);
        }

        function getDelarList(flag){
            var acmp_id='';
            var append_position='';
            if(flag==1){
                acmp_id=$('#acmp_id').val();
                append_position='#dlrm_id';
            }
            else{
                acmp_id=$('#in_acmp_id').val();
                append_position='#in_dlrm_id';
            } 
            if(acmp_id==''){
                $(append_position).empty();
                return false;
            }
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/getDelarList/"+acmp_id,
                cache:"false",
                success:function(data){
                    $('#ajax_load').css('display','none');
                    var html='<option value="">Select</option>';
                    if(data){
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].dlrm_code+'">'+data[i].dlrm_code+'-'+data[i].dlrm_name+'</option>';
                        }
                    }                   
                    $(append_position).html(html);

                },error:function(error){
                    $('#ajax_load').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Something Went Wrong !!',
                    });
                }
            });
        }
        function getDmList(flag){
            var acmp_id='';
            var dlrm_id='';
            var append_position='';
            if(flag==1){
                acmp_id=$('#acmp_id').val();
                dlrm_id=$('#dlrm_id').val();
                append_position='#dm_id';
            }
            else{
                acmp_id=$('#in_acmp_id').val();
                dlrm_id=$('#in_dlrm_id').val();
                append_position='#in_dm_id';
            } 
            if(acmp_id==''){
                $(append_position).empty();
                return false;
            }
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/getDmList/"+acmp_id+'/'+dlrm_id,
                cache:"false",
                success:function(data){
                    $('#ajax_load').css('display','none');
                    var html='<option value="">Select</option>';
                    if(data){
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].aemp_usnm+'">'+data[i].aemp_usnm+'-'+data[i].aemp_name+'</option>';
                        }
                    }                   
                    $(append_position).html(html);

                },error:function(error){
                    $('#ajax_load').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Something Went Wrong !!',
                    });
                }
            });
        }
        function getTripList(){
            var dm_id=$('#in_dm_id').val();
            var dlrm_id=$('#in_dlrm_id').val();
            var tp_date=$('#in_tp_date').val();
            var _token=$('#_token').val();
            if(dm_id =='' && dlrm_id =='' && tp_date ==''){
                $('#in_trip_no').empty();
                return false;
            }
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getTripList",
                data:{
                    _token:_token,
                    dm_id:dm_id,
                    dlrm_id:dlrm_id,
                    tp_date:tp_date,                          
                },
                cache:"false",
                success:function(data){
                    $('#ajax_load').css('display','none');
                    var html='';
                    var html1='';
                    var trip_details=data.trip_details;
                    var data=data.trip_list;
                    var count=1;
                    if(data){
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].id+'">'+data[i].TRIP_INFO+'</option>';
                        }
                    }
                    if(trip_details){
                        for(var i=0;i<trip_details.length;i++){
                            html1+=`<tr><td>${count}</td>
                                    <td>${trip_details[i].aemp_usnm}-${trip_details[i].aemp_name}</td>
                                    <td>${trip_details[i].total_invoice}</td>
                                    <td>${trip_details[i].ordm_amnt}</td>
                                    <td>${trip_details[i].invoice_amnt}</td>
                                    </tr>`;
                        }
                    }
                    
                    
                    $('#in_trip_no').html(html);
                    $('#trip_details_body').html(html1);

                },error:function(error){
                    $('#ajax_load').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Something Went Wrong !!',
                    });
                }
            });
        }
        $("input[name='load_type']").click(function() {
            var load_type = $("input[name='load_type']:checked").val();
            if(load_type==1){
                $('.in_sr_list_div').show();
                getSRList();
            }
            else{
                $('.in_sr_list_div').hide();
            }
        });
        function getSRList(){
            var acmp_id=$('#in_acmp_id').val();
            var dlrm_id=$('#in_dlrm_id').val();
            var _token=$('#_token').val();
            if(acmp_id ==null || dlrm_id ==null)
            {
                Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please select company and depot !!',
                    });
            }
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getSRList",
                data:{
                    _token:_token,
                    acmp_id:acmp_id,
                    dlrm_id:dlrm_id,                         
                },
                cache:"false",
                success:function(data){
                    $('#ajax_load').css('display','none');
                    var html='';
                    if(data){
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].aemp_id+'">'+data[i].aemp_usnm+'-'+data[i]['aemp_name']+'</option>';
                        }
                    }                                      
                    $('#in_sr_list').html(html);
                    $("#in_sr_list").multiselect('reload');

                },error:function(error){
                    $('#ajax_load').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Something Went Wrong !!',
                    });
                }
            });
        }
        function getInvoiceList(){
            var load_type = $("input[name='load_type']:checked").val();
            var sr_list=$('#in_sr_list').val();
            var acmp_id=$('#in_acmp_id').val();
            var dlrm_id=$('#in_dlrm_id').val();
            var dm_id=$('#in_dm_id').val();
            var _token=$('#_token').val();
            if(load_type==null || acmp_id ==null || dlrm_id ==null){
                Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Please select all required field'
                });
            }
            $('#ajax_load').css('display', 'block');
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/getInvoiceList",
                data:{
                    _token:_token,
                    sr_list:sr_list,
                    load_type:load_type,                         
                    acmp_id:acmp_id,                         
                    dlrm_id:dlrm_id,                         
                    dm_id:dm_id,                         
                },
                cache:"false",
                success:function(data){
                    $('#ajax_load').css('display','none');
                    var content='';
                    var count=1;
                    for(var i=0;i<data.length;i++){
                        var color='';
                        if(data[i].is_free==1){
                            color='background-color:#D1FFBD !important';
                        }
                        content+=`<tr style="${color}">
                                        <td><input type="checkbox" class="invoice_check" name="invoice_check" value="${data[i].ordm_id}" onchange="adjustAllSelect()">&nbsp;${count}</td>
                                        <td>${data[i].ordm_date}</td>
                                        <td><input type="hidden" name="ordm_list[]" value="${data[i].ordm_id}" class="ordm_list">${data[i].ordm_ornm}</td>
                                        <td>
                                            <input type="hidden" name="site_id_list[]" value="${data[i].site_id}" class="site_id_list" >
                                            <input type="hidden" name="site_code_list[]" value="${data[i].site_code}" class="site_code_list" >
                                            ${data[i].site_code}-${data[i].site_name}
                                        </td>
                                        <td>
                                            <input type="hidden" name="sr_id_list[]" value="${data[i].aemp_id}" class="sr_id_list" >
                                            <input type="hidden" name="sr_usnm_list[]" value="${data[i].aemp_usnm}" class="sr_usnm_list" >
                                            ${data[i].aemp_usnm}-${data[i].aemp_name}
                                        </td>
                                        <td>
                                            <input type="hidden" name="amim_id_list[]" value="${data[i].amim_id}" class="amim_id_list">
                                            <input type="hidden" name="amim_code_list[]" value="${data[i].amim_code}" class="amim_code_list" >
                                            ${data[i].amim_code}-${data[i].amim_name}
                                        </td>
                                        <td>
                                            <input type="hidden" name="ordd_qnty_list[]" value="${data[i].ordd_inty}" class="ordd_qnty_list">
                                            <input type="hidden" name="ordd_uprc_list[]" value="${data[i].ordd_uprc}" class="ordd_uprc_list" >
                                            ${data[i].ordd_inty}
                                        </td>
                                        <td>V:${data[i].ordd_ovat} E:${data[i].ordd_excs}</td>
                                        <td>
                                            <input type="number" name="invoice_qnty_list[]" value="0" class="invoice_qnty_list cl_${data[i].amim_id}" >
                                            <input type="hidden" name="ordd_ovat" value="${data[i].ordd_ovat}" class="ordd_ovat" > 
                                            <input type="hidden" name="ordd_excs" value="${data[i].ordd_excs}" class="ordd_excs" > 
                                            <input type="hidden" name="ordd_opds" value="${data[i].ordd_opds}" class="ordd_opds" > 
                                            <input type="hidden" name="ordd_spdc" value="${data[i].ordd_spdc}" class="ordd_spdc" > 
                                            <input type="hidden" name="ordd_dfdc" value="${data[i].ordd_spdc}" class="ordd_dfdc" > 
                                            
                                        </td>
                                        <td>
                                            <input type="number" name="invoice_details_amnt[]" value="0" class="invoice_details_amnt" >                                           
                                                                                      
                                        </td>
                                </tr>`;
                                count++;
                    }
                    $('#tl_dynamic_cont').html(content);

                },
                error:function(error){
                    $('#ajax_load').css('display','none');
                    Swal.fire({
                    title: 'Failed!',
                    text: 'Failed to fetch invoices!',
                });
                }
            })
        }
        // Calculation start
            /* 1. Invoice Qty<Order Qty 2. Stock check  3. Order Oamt generate */
            //onchange invoice qty
            function calculateOthers(v){
                var curent_dues = parseFloat($(v).closest('tr').find('.dues').val() || 0);
            }
        // Calculation end
        //Core Function
        function createTrip(){
            var _token=$('#_token').val();
            var acmp_id=$('#acmp_id').val();
            var tp_type=$('#tp_type').val();
            var dm_id=$('#dm_id').val();
            var dlrm_id=$('#dlrm_id').val();
            var tp_date=$('#tp_date').val();
            var vhcl_id=$('#vhcl_id').val();
            $('#ajax_load').css('display','block');
            if(dlrm_id !='' && tp_date !='' && tp_type !='' && acmp_id !='' && dm_id !=''){
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/create/trip",
                    data:{
                        _token:_token,
                        tp_type:tp_type,
                        dm_id:dm_id,
                        dlrm_id:dlrm_id,
                        tp_date:tp_date,
                        vhcl_id:vhcl_id,                          
                        acmp_id:acmp_id                           
                    },
                    cache:"false",
                    success:function(data){
                        $('#ajax_load').css('display','none');
                        Swal.fire({
                            icon: data.icon,
                            title: data.flag,
                            html: '<div><b style="font-size:13px!important;color:blue!important;"><i>' + data.message + '</i></b></div>',
                        });
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#ajax_load').css('display', 'none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: xhr.responseJSON.message, // Access the error message returned from Laravel
                        });
                    }
                });
            }
            else{
                $('#ajax_load').css('display','none');
                Swal.fire({
                    icon:'warning',
                    title: 'Failed',
                    text: 'Please fill all the required(*) input field!',
                });
            }
        }

        // *******************Trip All Function End ******************************************
        function loadItem(){
            var search_text=$('#search_text').val();          
            if(search_text.length>2){
                $('#ajax_load').css('display','block');
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/loadItem/"+search_text,
                    cache:"false",
                    success:function(data){
                        $('#ajax_load').css('display','none');
                        $('#amim_id').empty();
                        var html='<option value="">Select Item</option>';
                        if(data){
                            for(var i=0;i<data.length;i++){
                                html+='<option value="'+data[i].id+'">'+data[i].amim_code+'-'+data[i].amim_name+'</option>';
                            }
                        }
                        
                        
                        $('#amim_id').append(html);

                    },error:function(error){
                        $('#ajax_load').css('display','none');
                    }
                });
            }
            else{
                Swal.fire({
                    text: 'Search Text Length Should be More Than Two!',
                })
            }
        }

        function itemMapping(){
            var _token=$('#_token').val();
            var mspm_id=$('#mspm_id').val();
            var amim_id=$('#amim_id').val();
            var mspd_qnty=$('#mspd_qnty').val();
            if(mspm_id !='' && amim_id !='' && mspd_qnty !=''){
                $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/msp/itemMapping",
                        data:{
                            mspm_id:mspm_id,
                            amim_id:amim_id,
                            mspd_qnty:mspd_qnty,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Item Mapping Done!',
                            });
                        },error:function(error){
                            console.log(error)
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Item Mapping Failed!',
                            });
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Failed',
                    text: 'Please fill all the required input field!',
                });
            }
        }


        function siteMapping(){
            var _token=$('#_token').val();
            var mspm_id=$('#site_mspm_id').val();
            var site_cat=$('#site_cat').val();
            var site_code=$('#site_code').val();
            var slgp_id=$('#slgp_id').val();
            if(mspm_id !=''){
                if(site_cat =='' && site_code ==''){
                    Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select Site Category OR Provide Site Code',
                    });
                }
                else{
                    $('#ajax_load').css('display','block');
                    $.ajax({
                            type:"POST",
                            url:"{{URL::to('/')}}/msp/siteMapping",
                            data:{
                                mspm_id:mspm_id,
                                site_cat:site_cat,
                                site_code:site_code,
                                slgp_id:slgp_id,
                                _token:_token,
                            },
                            cache:"false",
                            success:function(data){
                                $('#ajax_load').css('display','none');
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Item Mapping Done!',
                                });
                            },error:function(error){
                                console.log(error);
                                $('#ajax_load').css('display','none');
                                if(error.status==500){
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text:'Duplicate Entry',
                                });
                                }else{
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Warning',
                                        text:'Some data may be failed to upload because of large volume',
                                    });
                                }
                            }
                    });
                }
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please Select MSP ',
                });
            }
        }
        
        function getGroup(slgp_id,place) {
                // clearDate();
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
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                        }
                        if(place==1){
                            $('#slgp_id').empty();
                            $('#slgp_id').append(html);
                        }else{
                            $('#slgp_slgp_id').empty();
                            $('#slgp_slgp_id').append(html);
                        }                     
                        
                        
                    }
                });
        }


        function slgpZoneMapping(){
            var _token=$('#_token').val();
            var mspm_id=$('#slgp_mspm_id').val();
            var slgp_id=$('#slgp_slgp_id').val();
            var zone_id=$('#zone_id').val();
            if(mspm_id !='' && slgp_id !='' && zone_id !=''){
                $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/msp/slgpZoneMapping",
                        data:{
                            mspm_id:mspm_id,
                            slgp_id:slgp_id,
                            zone_id:zone_id,
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'MSP Mapping Done!',
                            });
                        },error:function(error){
                            console.log(error)
                            $('#ajax_load').css('display','none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'MSP Mapping Failed!',
                            });
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Failed',
                    text: 'Please fill all the required input field!',
                });
            }
        }
    </script>
@endsection