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
                            <div>
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                                
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">Item Code *
                                                
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_id">Sales Group
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <select class="form-control cmn_select2" name="slgp_id" id="slgp_id" multiple="multiple">
                                                <option value="">Select Group</option>
                                                @foreach($slgp_list as $slgp)
                                                    <option value="{{ $slgp->slgp_id }}">{{ ucfirst($slgp->slgp_name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12  col-sm-12 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_id">
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <button type="submit" class="btn btn-success" onclick="addItem()" style="float:right;">Add Item</button>
                                        </div>
                                    </div>
                                    <div  class="col-md-1 col-sm-1 col-xs-12" style="float:right;">
                                       
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <span style="float: left;"> <button class="btn btn-danger" type="button" onclick="closeWindow()">Close Window<i class="fa fa-check-cross"></i> </button> </span>
                            <form action="{{ URL::to('non-prom/item/add')}}" method="POST">
                                {{csrf_field()}}
                                <span style="float: right;"> <button class="btn btn-success" type="submit">Save <i class="fa fa-check-circle"></i> </button> </span>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Sales Group</th>
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
                        <div class="x_content">
                            <form action="{{ URL::to('non-prom/item/bulk/add')}}" method="POST" enctype="multipart/form-data">
                                {{csrf_field()}}
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <h5 class="text-center">Non SP Item Bulk Upload</h5>
                                        <p style="color:darkred;">Type 1 For add || Type 2 For remove</p>
                                        <a href="{{URL::to('non-prom/item/bulk/format')}}">Download Format</a>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">File
                                                
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="file" class="form-control in_tg" name="npit_item" id="npit_item">
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
    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        function closeWindow(){
            window.close();
        }
        function addItem(){
            let amim_code=$('#item_code').val();
            let slgp_id=$('#slgp_id').val();
            let html='';
            if(amim_code !='' && slgp_id !=''){
                for(let i=0;i<slgp_id.length;i++){
                     html+='<tr><td><input type="hidden" name="amim_code[]" id="amim_code" value="'+amim_code+'">'+amim_code+'</td>'+
                       '<td><input type="hidden" name="sgp[]" id="sgp" value="'+slgp_id[i]+'">'+slgp_id[i]+'</td>'+
                       '<td><input type="submit" class="btn btn-danger btn-xs" value="Remove" onclick="removeItem(this)"></td></tr>';
                }
                
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
        
    </script>
@endsection