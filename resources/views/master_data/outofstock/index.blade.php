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
                        <li class="active">
                            <strong>Out Of Stock Item Details</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/outofstock/create')}}">Add New Item</a>
                            </li>
                        @endif
                    </ol>
                </div>
                <div class="title_right">
                    @if ($permission->wsmu_crat)
                        <a href="{{ URL::to('/bulk/outofstock')}}" style="color:darkred;font-weight:bold;" target="_blank"><i class="fa fa-upload"></i> Upload</a>
                    @endif
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <div class="x_content">
                                <form class="form-horizontal form-label-left">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="x_title">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                    for="dpot_id" style="text-align: left">Depo Name<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="dpot_id" id="dpot_id">
                                                        <option value="">Select</option>
                                                        @foreach($dpot as $dpt)
                                                            <option value="{{$dpt->id}}">{{$dpt->dpot_code}}
                                                                - {{$dpt->dpot_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itcg_id"
                                                    style="text-align: left">Category<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="itcg_id" id="itcg_id" onchange="getSubCategory(this.value)">
                                                        <option value="">Select</option>
                                                        @foreach($itcg as $cg)
                                                            <option value="{{$cg->id}}">{{$cg->itcg_code}}
                                                                - {{$cg->itcg_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itsg_id"
                                                    style="text-align: left">Subcategory<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="itsg_id" id="itsg_id">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="item_code"
                                                    style="text-align: left">Item Code<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itcl_id"
                                                    style="text-align: left">Class<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="itcl_id" id="itcl_id">
                                                        <option value="">Select</option>
                                                        @foreach($itcl as $cls)
                                                            <option value="{{$cls->id}}">{{$cls->itcl_code}}
                                                                - {{$cls->itcl_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <button id="send" type="button" style="margin-left:10px;"
                                                        class="btn btn-success"
                                                        onclick="getOutOfStockItem()"><span class="fa fa-search"
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
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <button class="btn btn-danger" id="delete-out-of-stock">Delete</button>

                            <ul class="nav navbar-right panel_toolbox">
                                <li>
                                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>  
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id=""  class="table table-bordered projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th><input type="checkbox" id="group_all">All</th>
                                    <th>SL
                                    </th>
                                    <th>DPO Name</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Class Name</th>
                                    <th>Category Name</th>
                                    <th>SubCategory Name</th>
                                    <th>Action</th>
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
    <script type="text/javascript">
        $(document).ready(function () {

            $('#group_all').on('click', function(e) {

                if($(this).is(':checked',true)){

                    $(".sub_chk").prop('checked', true);

                } else {

                    $(".sub_chk").prop('checked',false);
                }

            });
        });


        $("#delete-out-of-stock").click(function(){
            var out_stocks = [];
            var _token =$('#_token').val();
            var flag=1;
            let item_row_ids = []
            $.each($("input[name='group']:checked"), function(){
                item_row_ids.push(`out-of-stock-${$(this).val()}`)
                out_stocks.push($(this).val());
            });
            //console.log(out_stocks)


            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/bulk-delete/outofstock",
                data: {
                    _token:_token,
                    out_stocks:out_stocks,
                    
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    item_row_ids.forEach(function (id, index){
                        $(`#${id}`).remove()
                    })

                    $('.out-of-stock-count').each(function(index){
                        $(this).text(index + 1)
                    })
                    Swal.fire({
                        text: 'Out of Stock Deleted Successfully!',
                    })
                },
                error: function(data) {

                    console.log(data);

                }
            });

        });

        $('.select2').select2();
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
        function getSubCategory(itcl_id) {
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/get/sub-category/"+itcl_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    var html = '<option value="">Select </option>';
                    $('#itsg_id').empty();
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].itsg_code+'-'+ data[i].itsg_name+ '</option>';
                    }
                    $('#itsg_id').append(html);
                }
            });
        }
        function getOutOfStockItem(){
            let dpot_id=$('#dpot_id').val();
            let itcg_id=$('#itcg_id').val();
            let itsg_id=$('#itsg_id').val();
            let itcl_id=$('#itcl_id').val();
            let item_code=$('#item_code').val();
            let _token=$('#_token').val();
            $('#ajax_load').show();
            $.ajax({
                type:"POST",
                url:"{{URL::to('/')}}/get/outofStock/items",
                data:{
                    dpot_id:dpot_id,
                    itcg_id:itcg_id,
                    itsg_id:itsg_id,
                    itcl_id:itcl_id,
                    item_code:item_code,
                    _token:_token

                },
                dataType:"json",
                success:function(data){       
                    $('#ajax_load').hide();
                    $('#olt_table').DataTable().destroy(); 
                    let count=1;
                    let html="";
                    let item=data.item;
                    let permission=data.permission;
                    for(let i=0;i<item.length;i++){
                        html+='<tr id="out-of-stock-'+item[i].id+'">'+
                                '<td><input type="checkbox" class="sub_chk" name="group" value="'+item[i].id+'"></td>'+
                                '<td class="out-of-stock-count">'+count+'</td>'+
                                '<td>'+item[i].dpot_name+'</td>'+
                                '<td>'+item[i].amim_code+'</td>'+
                                '<td>'+item[i].amim_name+'</td>'+
                                '<td>'+item[i].itcl_name+'</td>'+
                                '<td>'+item[i].itcg_name+'</td>'+
                                '<td>'+item[i].itsg_name+'</td>';
                        if(permission.wsmu_vsbl){
                            html+='<td><a id="'+item[i].id+'" onclick="removeItem(this)" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i> Delete</a>&nbsp;&nbsp;';
                        }
                        
                                                   
                        count++;                                
                    }
                    $('#cont').empty();
                    $('#cont').append(html);
                },error:function(error){
                    $('#ajax_load').hide();
                    swal.fire({
                        icon:'error',
                        text:'Something Went Wrong!!!'
                    })
                }
                
            })
        }
        function removeItem(v) {
            let id=$(v).attr('id');
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/remove/outofstock/item/"+id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $(v).parent().parent().remove();
                    swal.fire({
                        icon:'success',
                        text:'Item removed  from OutofStock List',
                    });
                }
            });
        }
    </script>
@endsection