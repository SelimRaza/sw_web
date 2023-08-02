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
                                
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">Item Code *
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3  col-sm-3 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tppr">Price *
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="number" class="form-control in_tg" name="tppr" id="tppr">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3  col-sm-3 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gppr">GRV Price *
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="number" class="form-control in_tg" name="gppr" id="gppr">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3  col-sm-3 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="plmt_id">Price List
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="plmt_id" id="plmt_id">
                                                <option value="">Select Price list</option>
                                                @foreach($plmt_list as $plmt)
                                                    <option value="{{ $plmt->plmt_code }}">{{ ucfirst($plmt->plmt_name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div  class="col-md-1 col-sm-1 col-xs-12" style="float:right;">
                                        <button type="submit" class="btn btn-success" onclick="addItem()" style="margin-right:10%;">Add Item</button>
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content" style="padding:2% 10% 0 10%;">
                            <span style="float: left;"> <button class="btn btn-danger" type="button" onclick="closeWindow()">Close Window<i class="fa fa-check-cross"></i> </button> </span>
                            <form action="{{ URL::to('addItemIntoPriceList')}}" method="POST">
                                {{csrf_field()}}
                                <span style="float: right;"> <button class="btn btn-success" type="submit">Save <i class="fa fa-check-circle"></i> </button> </span>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Plmt Code</th>
                                            <th>Sales Price</th>
                                            <th>Sales Grv</th>
                                            <th>Delar Price</th>
                                            <th>Delar Grv</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content" style="padding:2% 10% 0 10%;">
                            <form action="{{ URL::to('add/bulk-item/pricelist')}}" method="POST" enctype="multipart/form-data">
                                {{csrf_field()}}
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <h5 class="text-center">Price List Bulk Upload/Update</h5>
                                        <a href="{{URL::to('price-list/bulk/format')}}">Download Format</a>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">File
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="file" class="form-control in_tg" name="plmt_file" id="plmt_file">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                                        <button type="submit" class="btn btn-success">Upload</button>
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
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        function closeWindow(){
            window.close();
        }
        function addItem(){
            let amim_code=$('#item_code').val();
            let tppr=$('#tppr').val();
            let gppr=$('#gppr').val();
            let plmt_code=$('#plmt_id').val();
            if(amim_code !='' && tppr !='' && gppr !='' && plmt_code !=''){
                let html='<tr><td><input type="hidden" name="amim_code[]" id="amim_code" value="'+amim_code+'">'+amim_code+'</td>'+
                       '<td><input type="hidden" name="plmt_code[]" id="plmt_code" value="'+plmt_code+'">'+plmt_code+'</td>'+
                       '<td><input type="number" class="form-control" name="amim_tppr[]" id="amim_tppr" value="'+tppr+'"></td>'+
                       '<td><input type="number" class="form-control" name="amim_gppr[]" id="amim_gppr" value="'+gppr+'"></td>'+
                       '<td><input type="number" class="form-control" name="amim_dppr[]" id="amim_dppr" value="'+tppr+'"></td>'+
                       '<td><input type="number" class="form-control" name="amim_dgpr[]" id="amim_dgpr" value="'+gppr+'"></td>'+
                       '<td><input type="submit" class="btn btn-danger btn-xs" value="Remove" onclick="removeItem(this)"></td></tr>';
                $('#cont').append(html);
            }else{
                Swal.fire({
                    icon:'error',
                    text:'Please fill all fields',
                });
            }
        }
        function removeItem(v){
            $(v).parent().parent().remove();
        }
        function addItemInPriceList(){
            let row_count=$('#cont tr').length;
            if(row_count>=1){
                let amim_code=$('input[name="amim_code"]').length;
                let amim_tppr=$('#amim_tppr').val();
                let amim_dppr=$('#amim_dppr').val();
                let amim_gppr=$('#amim_gppr').val();
                let amim_dgpr=$('#amim_dgpr').val();
                let plmt_code=$('#plmt_id').val();
                let _token = $("#_token").val();
                console.log(amim_code)
                return false;
                $.ajax({
                    type:"POST",
                    url: "{{ URL::to('/')}}/addItemIntoPriceList/",
                    data:{
                        amim_code:amim_code,
                        amim_tppr:amim_tppr,
                        amim_gppr:amim_gppr,
                        amim_dppr:amim_dppr,
                        amim_dgpr:amim_dgpr,
                        plmt_code:plmt_code,
                        _token:_token

                    },
                    dataType: "json",
                    success:function(data){
                        //console.log(data);
                        swal.fire({
                            icon:"success",
                            text:"Item Added Successfully",
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
                
            }else{
                Swal.fire({
                    icon:'error',
                    text:'Please add some item!!!!',
                });
            }
        }
    </script>
@endsection