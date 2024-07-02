@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">
                            <strong>  </strong>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                
            </div>
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
                        <div class="x_content">
                        <div class="col-md-12">
                            <div style="padding: 10px;">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                                    <input type="hidden" class="form-control in_tg" name="amim_id" id="amim_id" value="{{$pldt->amim_id}}">
                                    <input type="hidden" class="form-control in_tg" name="plmt_id" id="plmt_id" value="{{$pldt->plmt_id}}">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="itsg_id">Item Name
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="item_code" id="item_code" value="{{$pldt->pldt_snme}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="itsg_id">Item Code
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="item_code" id="item_code" value="{{$pldt->amim_code}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="plmt_id">Price List Code
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="price_list" id="price_list" value="{{$pldt->plmt_code}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="plmt_id">Price List Name
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="price_list" id="price_list" value="{{$plmt_name}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="tppr">Price *
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="number" value="{{$pldt->pldt_tppr*$pldt->amim_duft}}" class="form-control in_tg" name="tppr" id="tppr">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="gppr">GRV Price *
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="number" class="form-control in_tg" name="gppr" id="gppr" value="{{$pldt->pldt_tpgp*$pldt->amim_duft}}">
                                        </div>
                                    </div>
                                    
                                    <div  class="col-md-1 col-sm-1 col-xs-12" style="float:right;">
                                        <span style="float: right;"> <a class="btn btn-danger"  onclick="closeWindow()">Close<i class="fa fa-check-cross"></i> </a> </span>
                                    </div>
                                    <div  class="col-md-1 col-sm-1 col-xs-12" style="float:right;">
                                        <button type="submit" class="btn btn-success" onclick="updateItemInPriceList()" style="margin-right:10%;">Update</button>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        function closeWindow(){
            window.close();
        }
       
        function updateItemInPriceList(){
                let amim_id=$('#amim_id').val();
                let plmt_id=$('#plmt_id').val();
                let tppr=$('#tppr').val();
                let gppr=$('#gppr').val();
                let _token = $("#_token").val();
                $.ajax({
                    type:"POST",
                    url: "{{ URL::to('/')}}/update/pldt/price",
                    data:{
                        amim_id:amim_id,
                        plmt_id:plmt_id,
                        tppr:tppr,
                        gppr:gppr,
                        _token:_token

                    },
                    dataType: "json",
                    success:function(data){
                        //console.log(data);
                        swal.fire({
                            icon:"success",
                            text:"Item Price Updated Successfully",
                        })
                    },
                    error:function(error){
                        console.log(error);
                        swal.fire({
                            icon:"error",
                            text:"Something went wrong!!!",
                        })
                    }
                });
                
           
        }
    </script>
@endsection