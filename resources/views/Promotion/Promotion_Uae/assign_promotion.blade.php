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
                            <a href="{{ URL::to('/promotion_sp_2')}}">All Promotion</a>
                        </li>
                        <li class="active">
                            <strong>Assign Party</strong>
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <strong>
                                <center> ::: Assign Party :::</center>
                            </strong>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div id="exTab1" class="container">	
                            <ul  class="nav nav-pills">
                               
                                <li><a href="#2a" data-toggle="tab"  onclick="getSpecificAssignPartyWindow()">Assign Party(Multi Select)</a>
                                </li>
                                <li>
                                    <a  href="#1a" data-toggle="tab"  onclick="getAssignPartyWindow()">Assign Party( Single Select) </a>
                                </li>
                               
                            </ul>

                        </div> 
                        <div class="form-horizontal form-label-left dfsm" id="dfsm">
                            <div class="animate__animated animate__zoomIn" >
                                <div class="col-md-offset-3 col-sm-offset-3 col-md-6" style="box-shadow: 0 0 10px 5px gray; padding:10px; margin-top:10px;">
                                        <h5 class="text-center" style="color:olive;">Mandatory</h5>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="prmr_id"> Promotion 
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2" name="prmr_id"
                                                        id="prmr_id"
                                                        >
                                                        <option value="">Select Promotion</option>
                                                    @foreach($prom_list as $prom)
                                                    <option value="{{$prom->id}}">{{$prom->prms_code."-".$prom->prms_name}}</option>
                                                    @endforeach
                                                    
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    
                                    <div class="col-md-offset-3 col-sm-offset-3 col-md-6" style="box-shadow: 0 0 10px 5px gray; padding:10px; margin-top:10px;">
                                        <h5 ><b>Assign To</b></h5><h5 class="text-center" style="color:green;">Atleaset Provide One</h5>
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
                                                        @foreach($category as $cat)
                                                            <option value="{{$cat->id}}">{{$cat->otcg_code.'-'.$cat->otcg_name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
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
                                    </div>
                                    <div class="col-md-offset-3 col-sm-offset-3 col-md-6" style="box-shadow: 0 0 10px 5px gray; padding:10px; margin-top:10px;">
                                        <h5 class="text-center" style="color:red;">Optional</h5>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                for="sh_acmp_id">Channel<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="channel_id"
                                                        id="channel_id"
                                                        onchange="getSubChannel(this.value)">

                                                    <option value="">Select Channel</option>
                                                    @foreach($channel as $cnl)
                                                    <option value="{{$cnl->id}}">{{$cnl->chnl_code.'-'.$cnl->chnl_name}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                for="slgp_id">Sub Channel<span
                                                    ></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="sub_channel_id"
                                                        id="sub_channel_id">
                                                    <option value="">Select sub channel</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <div class="item form-group" >
                                        <div class="col-md-9 col-sm-9">
                                        <button type="submit" class="btn btn-primary" style="float:right; margin-top:15px;" onclick="siteMapping()">Save</button>
                                        </div>
                                </div>
                            </div> 
                        </div>
                        <div class="form-horizontal form-label-left dfsm" id="dfsm2" >
                            <div class="animate__animated animate__zoomIn col-md-6 col-md-offset-3 col-sm-offset-3" style="box-shadow: 0 0 10px 5px gray; padding:10px; margin-top:10px; border-radius:10px;">
                                <div class=" col-md-12 col-sm-12" style=" padding:10px; margin-top:10px;">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="prmr_id"> Group 
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="slgp_id"
                                                            id="slgp_id"
                                                            onchange="getSpecificGroupPromotion(this.value)">
                                                            <option value="">Select Group</option>
                                                        @foreach($slgp as $sgp)
                                                        <option value="{{$sgp->id}}">{{$sgp->slgp_code."-".$sgp->slgp_name}}</option>
                                                        @endforeach
                                                        
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <div class=" col-md-12 col-sm-12" style=" padding:10px; margin-top:10px;">
                                        <h5 class="text-center" style="color:olive;">Mandatory</h5>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="prmr_id"> Promotion 
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control cmn_select2" name="prmr_id"
                                                        id="prmr_id_1"
                                                        >
                                                    
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    
                                    <div class="col-md-12" style=" padding:10px; margin-top:10px;">
                                        <h5 ><b>Assign To</b></h5><h5 class="text-center" style="color:green;">Atleaset Provide One</h5>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site_cat"> Site Category 
                                                <span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="site_cat_1[]"
                                                            id="site_cat_1" multiple="multiple"
                                                            >
                                                        <option value="">Select Category</option>
                                                        @foreach($category as $cat)
                                                            <option value="{{$cat->id}}">{{$cat->otcg_code.'-'.$cat->otcg_name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Site Code
                                                    <span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input id="site_code_1" name="site_code" class="form-control col-md-7 col-xs-12 in_tg"
                                                    
                                                    placeholder="Site Code"
                                                    type="text" value="{{ old('site_code_1') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" col-md-12" style="padding:10px; margin-top:10px;">
                                        <h5 class="text-center" style="color:red;">Optional</h5>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                for="sh_acmp_id">Channel<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="chennel_id_1[]"
                                                        id="chennel_id_1"
                                                        onchange="getSubChannel1(this.value)" multiple="multiple">

                                                    <option value="">Select Channel</option>
                                                    @foreach($channel as $cnl)
                                                    <option value="{{$cnl->id}}">{{$cnl->chnl_code.'-'.$cnl->chnl_name}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                for="slgp_id">Sub Channel<span
                                                    ></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="form-control cmn_select2" name="sub_channel_id_1[]" multiple="multiple"
                                                        id="sub_channel_id_1">
                                                    <option value="">Select sub channel</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <div class="item form-group" >
                                        <div class="col-md-9 col-sm-9">
                                        <button type="submit" class="btn btn-primary" style="float:right; margin-top:15px;" onclick="specificGroupsiteMapping()">Save</button>
                                        </div>
                                </div>
                            </div> 
                        </div>         
                        </div>
                
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
      $('.cmn_select2').select2();
      $('#dfsm').hide();
      $('#dfsm2').hide();
      function siteMapping(){
            var _token=$('#_token').val();
            var prmr_id=$('#prmr_id').val();
            var site_cat=$('#site_cat').val();
            var site_code=$('#site_code').val();
            var channel_id=$('#channel_id').val();
            var sub_channel_id=$('#sub_channel_id').val();
            if(prmr_id !=''){
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
                            url:"{{URL::to('/')}}/prmr/siteMapping",
                            data:{
                                prmr_id:prmr_id,
                                site_cat:site_cat,
                                site_code:site_code,
                                channel_id:channel_id,
                                sub_channel_id:sub_channel_id,
                                _token:_token,
                            },
                            dataType:"json",
                            cache:"false",
                            success:function(data){
                                $('#ajax_load').css('display','none');
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Site Mapping Done!',
                                });
                            },error:function(error){
                                console.log(error);
                                $('#ajax_load').css('display','none');
                                
                                
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text:'Something Went Wrong',
                                });
                               
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

        function getSubChannel(id){
           
            if(id !=''){
                $('#ajax_load').show();
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/getSubChannel/"+id,
                    success:function(data){
                        $('#ajax_load').hide();
                        var html='<option value="">Select sub channel</option>';
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].id+'">'+data[i].scnl_name+'</option>';
                        }
                        $('#sub_channel_id').empty();
                        $('#sub_channel_id').append(html);
                    },error:function(error){
                        console.log(error);
                    }
                });
            }else{
                $('#sub_channel_id').empty();
            }
        }
        function getAssignPartyWindow(){
            $('#dfsm2').hide();
            $('#dfsm').show();           
        }
        function getSpecificAssignPartyWindow(){
            $('#dfsm').hide();
            $('#dfsm2').show();           
        }
        function getSpecificGroupPromotion(slgp_id){
            if(slgp_id !=''){
                $('#ajax_load').show();
                $.ajax({
                    type:"GET",
                    url:"{{URL::to('/')}}/getSpecificGroupPromotion/"+slgp_id,
                    success:function(data){
                        $('#ajax_load').hide();
                        var html='<option value="">Select promotion</option>';
                        for(var i=0;i<data.length;i++){
                            html+='<option value="'+data[i].id+'">'+data[i].prms_code+'-'+data[i].prms_name+'</option>';
                        }
                        $('#prmr_id_1').empty();
                        $('#prmr_id_1').append(html);
                    },error:function(error){
                        console.log(error);
                    }
                });
            }else{
                $('#prmr_id_1').empty();
            }
        }
        function getSubChannel1(id){
           
           if(id !=''){
               $('#ajax_load').show();
               $.ajax({
                   type:"GET",
                   url:"{{URL::to('/')}}/getSubChannel/"+id,
                   success:function(data){
                       $('#ajax_load').hide();
                       var html='<option value="">Select sub channel</option>';
                       for(var i=0;i<data.length;i++){
                           html+='<option value="'+data[i].id+'">'+data[i].scnl_name+'</option>';
                       }
                       $('#sub_channel_id_1').empty();
                       $('#sub_channel_id_1').append(html);
                   },error:function(error){
                       console.log(error);
                   }
               });
           }else{
               $('#sub_channel_id_1').empty();
           }
       }
       function specificGroupsiteMapping(){
            var _token=$('#_token').val();
            var prmr_id=$('#prmr_id_1').val();
            var site_cat=$('#site_cat_1').val();
            var site_code=$('#site_code_1').val();
            var channel_id=$('#channel_id_1').val();
            var sub_channel_id=$('#sub_channel_id_1').val();
            if(prmr_id !=''){
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
                            url:"{{URL::to('/')}}/prmr/specificGroupsiteMapping",
                            data:{
                                prmr_id:prmr_id,
                                site_cat:site_cat,
                                site_code:site_code,
                                channel_id:channel_id,
                                sub_channel_id:sub_channel_id,
                                _token:_token,
                            },
                            dataType:"json",
                            cache:"false",
                            success:function(data){
                                $('#ajax_load').css('display','none');
                                console.log(data)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Site Mapping Done!',
                                });
                            },error:function(error){
                                console.log(error);
                                $('#ajax_load').css('display','none');
                                
                                
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text:'Something Went Wrong',
                                });
                               
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
    </script>
@endsection