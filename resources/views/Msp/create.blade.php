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
                        <li>
                            <a href="{{ URL::to('/msp')}}">All MSP</a>
                        </li>
                        <li class="active">
                            <strong>New MSP </strong>
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
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div id="exTab1" class="container">
                                    <ul class="nav nav-pills">
                                        <li>
                                            <a href="#1a" data-toggle="tab"
                                                onclick="getDiscountArea()">MSP </a>
                                        </li>
                                        <li><a href="#2a" data-toggle="tab" onclick="getItemArea()">
                                                ITEM</a>
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
                                <form class="form-horizontal form-label-left dfdsc" action="{{route('mspm.store')}}"
                                      method="post" enctype="multipart/form-data" id="dfdsc">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                   
                                        <div class="animate__animated animate__zoomIn">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_name">MSP
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="mspm_name" name="mspm_name" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="MSP  Name" required
                                                           type="text" value="{{ old('mspm_name') }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_code">MSP
                                                    Code <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="msp_code" name="mspm_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder=" MSP Code" required="required"
                                                           type="text" value="{{ old('mspm_code') }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start_date">Start
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="start_date" name="start_date"
                                                           class="form-control col-md-7 col-xs-12 in_tg msp_date" autocomplete="off"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="end_date" name="end_date"
                                                           class="form-control col-md-7 col-xs-12 in_tg msp_date" autocomplete="off"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>
                                            </div>
                                           <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                   <button type="submit" class="btn btn-primary" style="float:right;">Save</button>
                                                </div>
                                           </div>
                                        </div>                                    
                                </form>
                                <div class="form-horizontal form-label-left dfim" id="dfim">
                                    <div class="animate__animated animate__zoomIn" >
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mspm_id"> MSP 
                                                    <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="mspm_id"
                                                                id="mspm_id"
                                                                >
                                                            <option value="">Select MSP</option>
                                                            @foreach($msps as $msp)
                                                            <option value="{{$msp->id}}">{{$msp->mspm_code."-".$msp->mspm_name}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cat_id"> Find Item 
                                                    <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-5 col-sm-5 col-xs-12">
                                                    <input id="search_text" name="search_text" class="form-control col-md-7 col-xs-12 in_tg"
                                                        placeholder="Item name or Code" 
                                                        type="text" value="{{ old('search_text') }}">
                                                </div>
                                                <div class="col-md-1 col-sm-1 col-xs-12">
                                                    <button class="btn btn-success" onclick="loadItem()" style="float:right">Load Item</button>
                                                </div>
                                            </div>


                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amim_id"> Item 
                                                    <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="amim_id"
                                                                id="amim_id"
                                                                >
                                                            <option value="">Select Item</option>
                                                            
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Quantity
                                                   <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="mspd_qnty" name="mspd_qnty" class="form-control col-md-7 col-xs-12 in_tg"
                                                        
                                                        placeholder="Quantity" required="required"
                                                        type="number" value="{{ old('mspd_qnty') }}">
                                                </div>
                                            </div>
                                        <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                <button type="submit" class="btn btn-primary" style="float:right;" onclick="itemMapping()">Save</button>
                                                </div>
                                        </div>
                                    </div> 

                                    <!-- bulk upload section -->
                                    <!-- bulk upload section end -->
                                </div>
                                <div class="form-horizontal form-label-left dfsm" id="dfsm">
                                    <div class="animate__animated animate__zoomIn" >
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site_mspm_id"> MSP 
                                                    <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="site_mspm_id"
                                                                id="site_mspm_id"
                                                                >
                                                                <option value="">Select MSP</option>
                                                            @foreach($msps as $msp)
                                                            <option value="{{$msp->id}}">{{$msp->mspm_code."-".$msp->mspm_name}}</option>
                                                            @endforeach
                                                           
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                        for="sh_acmp_id">Company<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <select class="form-control cmn_select2" name="sh_acmp_id"
                                                                id="sh_acmp_id"
                                                                onchange="getGroup(this.value,1)">

                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                            <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                    for="slgp_id">Group<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="slgp_id"
                                                            id="slgp_id">

                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site_cat"> Site Category 
                                                    <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="site_cat"
                                                                id="site_cat"
                                                                >
                                                            <option value="">Select Category</option>
                                                            @foreach($cats as $cat)
                                                                <option value="{{$cat->id}}">{{$cat->otcg_code.'-'.$cat->otcg_name}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <p style="text-align:center;">OR [Single Site]</p>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Site Code
                                                     <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="site_code" name="site_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                       
                                                        placeholder="Site Code"
                                                        type="text" value="{{ old('site_code') }}">
                                                </div>
                                            </div>
                                        <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                <button type="submit" class="btn btn-primary" style="float:right;" onclick="siteMapping()">Save</button>
                                                </div>
                                        </div>
                                    </div> 
                                </div>

                                <div class="form-horizontal form-label-left mspslgp" id="mspslgp">
                                    <div class="animate__animated animate__zoomIn" >
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_mspm_id"> MSP 
                                                    <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="slgp_mspm_id"
                                                                id="slgp_mspm_id"
                                                                >
                                                                <option value="">Select MSP</option>
                                                            @foreach($msps as $msp)
                                                            <option value="{{$msp->id}}">{{$msp->mspm_code."-".$msp->mspm_name}}</option>
                                                            @endforeach
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                        for="sh_acmp_id">Company<span
                                                                class="required"></span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <select class="form-control cmn_select2" name="sh_acmp_id"
                                                                id="sh_acmp_id"
                                                                onchange="getGroup(this.value,2)">

                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $acmpList)
                                                            <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                                - {{$acmpList->acmp_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                    for="slgp_slgp_id">Group<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="slgp_slgp_id"
                                                            id="slgp_slgp_id">

                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="zone_id"> Zone  
                                                    <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="zone_id"
                                                                id="zone_id"
                                                                >
                                                            <option value="">Select Zone</option>
                                                            @foreach($zones as $zone)
                                                                <option value="{{$zone->zone_id}}">{{$zone->zone_name}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        <div class="item form-group">
                                                <div class="col-md-9 col-sm-9">
                                                <button type="submit" class="btn btn-primary" style="float:right;" onclick="slgpZoneMapping()">Save</button>
                                                </div>
                                        </div>
                                    </div> 
                                </div>
                                <!-- check -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- bulk upload for item mapping -->
                <div class="col-md-12 col-sm-12 col-xs-12 dfim animate__animated animate__zoomIn">
                    <div class="x_panel">
                        <div class="x_title">
                            <h4 class ="text-center">Item Mapping</h4>
                            
                                <a class="btn btn-success btn-sm" href="{{url('msp/item-mapping-format')}}">Download Format </a>
                            
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

                            <form class="form-horizontal form-label-left" action="{{URL::to('msp/upload-item')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item File<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="item_file" class="form-control col-md-7 col-xs-12"
                                                name="item_file"
                                               placeholder="Item List File" type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- bulk upload for item mapping  End-->
                <!-- bulk upload for site mapping -->
                <div class="col-md-12 col-sm-12 col-xs-12 dfsm animate__animated animate__zoomIn">
                    <div class="x_panel">
                        <div class="x_title">
                            <h4 class ="text-center">Site Mapping</h4>
                            
                                <a class="btn btn-success btn-sm" href="{{url('msp/site-mapping-format')}}">Download Format </a>
                            
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

                            <form class="form-horizontal form-label-left" action="{{URL::to('msp/upload-site')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Site<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="site_file" class="form-control col-md-7 col-xs-12"
                                                name="site_file"
                                               placeholder="Site List " type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- bulk upload for site mapping  End-->
            </div>
        </div>
    </div>
    <script>
        $('#myDiv').hide();
        $("#slgp_id").select2();
        $("#buy_item").select2();
        $("#free_item").select2();
        $("#area_item").select2();
        $(".cmn_select2").select2();
        function hide(){
            $('#dfdsc').hide();
            $('.dfim').hide();
            $('.dfsm').hide();
            $('.mspslgp').hide();

        }
        $(".msp_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
            });
        hide();
        $('#dfdsc').show();
        function getDiscountArea(){
            hide();
            $('.dfdsc').show();
        }
        function getItemArea(){
            hide();
            $('.dfim').show();
        }
        function getSiteArea(){
            hide();
            $('.dfsm').show();
        }
        function getSlgpZoneArea(){
            hide();
            $('.mspslgp').show();
        }

        

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