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
                            <a href="{{ URL::to('/promotion')}}">All Promotion</a>
                        </li>
                        <li class="active">
                            <strong>New Promotion</strong>
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
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <form class="form-horizontal form-label-left" action="{{route('promotion.store')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div id="wizard" class="form_wizard wizard_horizontal">
                                        <ul class="wizard_steps">
                                            <li>
                                                <a href="#step-1">
                                                    <span class="step_no">1</span>
                                                    <span class="step_descr">
                                              <strong>Create Promotion</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-2">
                                                    <span class="step_no">2</span>
                                                    <span class="step_descr">
                                              <strong>Assign Item</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-3">
                                                    <span class="step_no">3</span>
                                                    <span class="step_descr">
                                              <strong>Assign Party</strong>
                                          </span>
                                                </a>
                                            </li>

                                        </ul>
                                        <div id="step-1">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="name" name="name" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Promotion Name" required
                                                           type="text" value="{{ old('name') }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                                    Code <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="code" name="code" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Promotion Code" required="required"
                                                           type="text" value="{{ old('code') }}">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Start
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="startDate" name="startDate"
                                                           class="form-control col-md-7 col-xs-12"
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
                                                    <input id="endDate" name="endDate"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sales
                                                    Group
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="slgp_id" id="slgp_id"
                                                             onchange="filterItem(this.value)" required>

                                                        <option value="">Select Sales Group</option>
                                                        @foreach ($salesGroups as $salesGroups)
                                                            <option value="{{ $salesGroups->slgp_id }}">{{ $salesGroups->slgp_code.' - '.$salesGroups->slgp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="step-2">
                                            <strong>
                                                <center> ::: Buy item :::</center>
                                            </strong>
                                            <hr/>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Buy
                                                    Item
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="buy_item" id="buy_item"
                                                           >

                                                        <option value="">Select Buy Item</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Max.
                                                    Qty <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="max_qty" name="max_qty"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Maximum Item Quantity" required="required"
                                                           type="number" value="{{old('max_qty')}}" onkeyup="calcDisc()">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Min.
                                                    Qty <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="mi_qty" name="mi_qty"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Minimum Item Quantity" required="required"
                                                           type="number" onkeyup="calcDisc()" value="{{old('mi_qty')}}">
                                                </div>
                                            </div>
                                            
                                            <hr/>
                                            <strong>
                                                <center> ::: Free item :::</center>
                                            </strong>
                                            <hr/>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Free
                                                    Item
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="free_item" id="free_item"
                                                            required onchange="showFreeItemPrice(this.value)">

                                                        <option value="">Select Free Item</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Free
                                                    Item Qty <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="f_item_qty" name="f_item_qty" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                            placeholder="Free item Qty" 
                                                           required="required"
                                                           type="number">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Free
                                                    Item Price <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="f_item_price" name="f_item_price" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           readonly="true" 
                                                           type="number">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Discount
                                                    Percentage % <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="dis_percen" name="dis_percen" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           readonly="true"
                                                           type="number" value="{{old('dis_percen')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="step-3" style="height: 300px">
                                            <hr/>
                                            <strong>
                                                <center> ::: Assign Area :::</center>
                                            </strong>
                                            <hr/>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                                    Promotion Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input type="radio" checked="checked" value="0" id="id_national"
                                                           name="pro_type"
                                                           onchange="showChildQuestions(this.value);"> Nationally <br/>
                                                    <input type="radio" value="1" id="id_zonal"
                                                           name="pro_type"
                                                           onchange="showChildQuestions(this.value);"> Zonal
                                                </div>
                                            </div>

                                            <div class="item form-group" id="myDiv">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Select Zone
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="area_item[]" id="area_item"
                                                            multiple="multiple"
                                                            required>

                                                        <option value="">Select Zone</option>
                                                        @foreach ($zones as $zones1)
                                                            <option value="{{ $zones1->id }}">{{ $zones1->zone_code.' - '.$zones1->zone_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
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
    </div>
    <script>
        $('#myDiv').hide();
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});
        $("#slgp_id").select2();
        $("#buy_item").select2();
        $("#free_item").select2();
        $("#area_item").select2();
        

        function filterItem(slgp_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/promotion/filterItem",
                data: {
                    slgp_id: slgp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#buy_item").empty();
                    $("#free_item").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        //   console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].item_code + ' - ' + data[i].item_name + '</option>';
                    }
                    $("#buy_item").append(html);
                    $("#free_item").append(html);
                }
            });
        }
//show current free item price and discount
        function clearPrice(){
            $('#f_item_qty').val("");
            $('#f_item_price').val("");
        }

        function showFreeItemPrice(f_id) {
            clearPrice();
            var _token = $("#_token").val();
            var  slgp_id= $("#slgp_id").val();
            if(f_id){
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/promotion/filterPrice&CalcDisc",
                data: {
                    slgp_id: slgp_id,
                    item_id: f_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    $('#f_item_price').val(data[0]['pldt_tppr']);
                    
                }
            });
        }
        }
        function calcDisc(){
            var min=parseInt($('#mi_qty').val());
            var max=parseInt($('#max_qty').val());
            if(Number.isInteger(min) && Number.isInteger(max)){
                let disc=((min/max)*100).toFixed(2);
                $('#dis_percen').val(disc);
            }
            else{
               $('#dis_percen').val(0); 
            }
        }

        function showChildQuestions(type) {
            if (type == '1') {
                $('#myDiv').show();
            } else {
                $('#myDiv').hide();
            }
        }

    </script>
@endsection