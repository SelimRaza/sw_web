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
                            <a href="{{ URL::to('/orders')}}">All Orders</a>
                        </li>
                        <li class="active">
                            <strong>New Order</strong>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">

                            <div class="x_content">

                                <form class="form-horizontal form-label-left"
                                      action="{{url('order/store')}}"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div id="wizard" class="form_wizard wizard_horizontal">
                                        <ul class="wizard_steps">
                                            <li>
                                                <a href="#step-1">
                                                    <span class="step_no">1</span>
                                                    <span class="step_descr">
                                              <strong>Master Selection</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-2">
                                                    <span class="step_no">2</span>
                                                    <span class="step_descr">
                                              <strong>Item</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-3">
                                                    <span class="step_no">3</span>
                                                    <span class="step_descr">
                                              <strong>Place Order</strong>
                                          </span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="x_title">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div id="step-1">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="acmp_id">Company
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="acmp_id" id="acmp_id" required
                                                                onchange="getGroup(this.value)">
                                                            <option value="">Select Company</option>
                                                            @foreach($acmp as $cmp)
                                                            <option value="{{$cmp->acmp_id}}">{{$cmp->acmp_code."-".$cmp->acmp_name}}</option>
                                                            @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_id">Group
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="slgp_id" id="slgp_id" required
                                                        onchange="getSR(this.value)">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sr_id">SR
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="sr_id" id="sr_id" required onchange="getRouteList(this.value)">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dlrm_id">Dealar
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="dlrm_id" id="dlrm_id">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rout_id">Route
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="rout_id" id="rout_id" required onchange="getOutletList(this.value)">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site_id">Outlet
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control cmn_select2" name="site_id" id="site_id" required onchange="getFormat(this.value)">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="step-2" style="height: 270px;">
                                            <!-- <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amim_id">Item
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <select class="form-control cmn_select2" name="amim_id" id="amim_id" required onchange="getItemFactor(this.value)">
                                                        
                                                    </select>
                                                </div>
                                            </div> -->
                                            <!-- <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="">
                                                 
                                                </label>
                                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="item_ctn">
                                                    CTN  <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-1 col-sm-1 col-xs-12">
                                                    <input id="item_ctn" name="offer_item_qty"
                                                           class="form-control col-md-12 col-xs-12"
                                                            required="required"
                                                           type="number" step="1" min="0" value="0">
                                                </div>
                                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="item_qty">
                                                        Qty  <span
                                                                class="required"></span>
                                                </label>
                                                <div class="col-md-1 col-sm-1 col-xs-12">
                                                    
                                                    <input id="item_qty" name="item_qty"
                                                           class="form-control col-md-12 col-xs-12"
                                                            required="required"
                                                           type="number" step="1" min="0" value="1">
                                                </div>
                                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="item_factor">
                                                        Factor  <span
                                                                class="required"></span>
                                                </label>
                                                <div class="col-md-1 col-sm-1 col-xs-12">
                                                    
                                                    <input id="item_factor" name="item_factor"
                                                           class="form-control col-md-12 col-xs-12"
                                                            required="required"
                                                           type="number" step="1" min="0" value="1" disabled>
                                                </div>
                                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="item_tppr">
                                                        TPPR  <span
                                                                class="required"></span>
                                                </label>
                                                <div class="col-md-1 col-sm-1 col-xs-12">
                                                    
                                                    <input id="item_tppr" name="item_tppr"
                                                           class="form-control col-md-12 col-xs-12"
                                                            required="required"
                                                           type="number" step="1" min="0" value="1" disabled>
                                                </div>
                                                
                                            </div> -->
                                            <div class="item form-group" id="formatbtn">
                                                    <a href="{{url('order/itemFormat')}}" class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3 col-sm-offset-3">Download Format</a>
                                            </div>
                                            <div class="item form-group" style="margin-top:10px;">
                                                
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amim_file">Item File
                                                     <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                     <input id="amim_file" name="amim_file"
                                                           class="form-control col-md-12 col-xs-12"
                                                            required="required"
                                                           type="file" step="1" min="0" value="0">
                                                </div>
                                            </div>
                                            <!-- <center>
                                                <button type="button" class="btn btn-warning" id="promotion_slab_btn"
                                                        onclick="addRow()">Add
                                                    More
                                                </button>
                                            </center>

                                            <div class="item form-group">
                                                <table id="myTableSlab"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                       
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <button type="button" id="btn_slab_delete"
                                                        class="btn btn-danger delete-row">Delete Row
                                                </button>
                                            </div> -->

                                        </div>
                                        <div id="step-3" style="height: 270px;">
                                                <h3 class="text-center" style="margin-top:40px;">After finishing, Please Adjust all Item For  Getting Promotion, Discount  and Placing Order</h3>
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
        $("#slgp_idds").select2();
        $("#slgp_id").select2();
        $("#o_type").select2();
        $("#add_item_group").select2();
        $("#add_item_category").select2();
        $("#add_item_item").select2();
        $("#buy_item").select2();
        $("#free_item").select2();
        $("#area_item").select2();
        $("#promotion_slab_qua").select2();
        $("#category").select2();
        $("#sub_channel").select2();
        $("#channel").select2();

        $("#thana_id").select2();
        $("#district").select2();

        $('.cmn_select2').select2();
        // function addRow() {
        //     var rowCoun = $('#myTableSlab >tbody >tr').length;
        //     var btn = document.getElementById("rowCount");
        //     var amim_id = $('#amim_id').val();
        //     var amim_name = $("#amim_id option:selected").text();
        //     var item_qty = $("#item_qty").val();
        //     var slab_text = $("#slab_text").val();
        //     var promotion_slab_qua = $("#promotion_slab_qua").val();

        //     var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='item_list[]' value='" + amim_id +
        //         "' hidden>" + amim_name + "</td><td><input type='text' name='item_qty[]' value='" + item_qty + "' hidden>" + item_qty +
        //         "</td><td><input type='text' name='slab_text[]' value='" + slab_text + "' hidden>" + slab_text + "</td><td><input type='text' name='slab_type[]' value='" + promotion_slab_qua + "' hidden>" + promotion_slab_qua + "</td></tr>";
        //     // alert(markup);
        //     $("#myTableSlab").append(markup);


        //     // Find and remove selected table rows
        //     $("#btn_slab_delete").click(function () {
        //         $("table tbody").find('input[name="record"]').each(function () {
        //             if ($(this).is(":checked")) {
        //                 $(this).parents("tr").remove();
        //             }
        //         });
        //     });
        // }

        function addRowItemAssign() {

            var rowCoun = $('#myTableOrder >tbody >tr').length;
            if (rowCoun == 0) {
                $('#deleteFreeItem').removeAttr('Disabled');
                //  deleteOrderItem.disabled = true;
            }
            var btn = document.getElementById("rowCount");

            var o_type = $('#o_type').val();
            var item_category = $('#item_category').val();
            var item_code = $("#item_code").val();
            var item_name = $("#item_name").val();
            var add_item_item_id = $("#add_item_item").val();

            var fields = add_item_item_id.split('-');

            var id = fields[0];
            var category = fields[1];
            var code = fields[2];
            var name = fields[3];

            var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='order_item_id[]' value='" + id +
                "' hidden>" + category + "</td><td>" + code +
                "</td><td>" + name + "</td><td><input type='hidden' name='order_type[]' value='" + o_type + "'></td></tr>";
            //alert(markup);
            if (o_type === "order") {
                $("#myTableOrder").append(markup);
            } else {
                $("#myTableFree").append(markup);
            }

            $("#deleteFreeItem").click(function () {
                $("table tbody").find('input[name="record"]').each(function () {
                    if ($(this).is(":checked")) {
                        $(this).parents("tr").remove();
                    }
                });
            });
        }

        function getCategory(slgp_id) {
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/promotion/getItemCategory",
                data: {
                    slgp_id: slgp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    $("#add_item_category").empty();
                    $("#item_category").empty();

                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        //   console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].issc_name + '</option>';
                    }
                    $("#add_item_category").append(html);

                }
            });
        }


        function getItem(category_id) {
            var _token = $("#_token").val();
            var slgp_id = $("#slgp_idds").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/promotion/getCategoryItem",
                data: {
                    slgp_id: slgp_id,
                    category_id: category_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#add_item_item").empty();

                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        //   console.log(data[i]);issc_name
                        html += '<option value="' + data[i].id + '-' + data[i].issc_name + '-' + data[i].item_code + '-' + data[i].item_name + '">' + data[i].item_code + ' - ' + data[i].item_name + '</option>';
                    }
                    $("#add_item_item").append(html);

                }
            });
        }

        function showPromotionArea(type) {
            if (type == '1') {
                $('#myDiv').show();
            } else {
                $('#myDiv').hide();
            }
        }

        function getPromotionLabel(pType) {

            $("#offer_type").empty();
            $("#o_type").empty();
            $("#promotion_slab_qua").empty();

            // alert(Channel_ID);
            var p_type = $('#promotion_label').val();

            if (p_type == "foc") {
                $('#discount_type').attr('disabled', 'disabled');
                var element = '';
                element = '<option value="order">Order</option><option value="offer">Offer</option>';
                // alert(element);
                $("#o_type").append(element);

                var element = '';
                element = '<option value="Quantity">Based On Quantity</option>';
                // alert(element);
                $("#promotion_slab_qua").append(element);

            } else {
                $('#discount_type').removeAttr('disabled');
                var element = '';
                element = '<option value="order">Order</option>';
                // alert(element);
                $("#o_type").append(element);

                var element = '';
                element = '<option value="Amount">Based On Amount</option>';
                // alert(element);
                $("#promotion_slab_qua").append(element);
            }
            if (p_type == '') {
                $("#offer_type").empty();
            }
            else if (pType == 'value') {
                var element = '';
                element = '<option value="bdt">BDT</option>';
                // alert(element);
                $("#offer_type").append(element);
            } else {
                var element = '';
                element = '<option value="CRT">Crt</option><option value="QTY">Pcs</option>';
                $("#offer_type").append(element);
            }
        }

        function getPromotionType(promotionType) {


            // alert(Channel_ID);
            var promotion_type = $('#promotion_type').val();

            if (promotion_type == "single") {

                $('#add_order_item').attr('disabled', 'disabled');
            } else {
                $('#add_order_item').removeAttr('disabled');
            }

        }
        function getOrderLabelPromotionQualifier(orderTypeQualifier) {
            if (orderTypeQualifier == "Line") {
                $('#order_qualifier').attr('disabled', 'disabled');
            } else {
                $('#order_qualifier').removeAttr('disabled');
            }
        }
        function getPromotionSlab(slabValue) {
            if (slabValue == "") {
                $('#promotion_slab_btn').attr('disabled', 'disabled');
            } else {
                $('#promotion_slab_btn').removeAttr('disabled');
            }
        }

    </script>




    <script>
     function getGroup(slgp_id) {
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
                $('#slgp_id').empty();
                $('#slgp_id').append(html);
                
            }
        });
    }
    function getSR(slgp_id) {
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/')}}/order/getSRList/"+slgp_id,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#sr_id").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select SR</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].aemp_usnm + " - " + data[i].aemp_name + '</option>';
                }
                $("#sr_id").append(html);
            }
        });
     }
     function getRouteList(sr_id) {
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        getDealarList(sr_id);
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/')}}/order/getRoutOfSR/"+sr_id,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#rout_id").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select Route</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].rout_code + " - " + data[i].rout_name + '</option>';
                }
                $("#rout_id").append(html);
            }
           // getDealarList(sr_id)
        });
     }
     function getDealarList(sr_id) {
        var _token = $("#_token").val();
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/')}}/order/getDealarOfSR/"+sr_id,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#dlrm_id").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select Dealar</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].dlrm_code + " - " + data[i].dlrm_name + '</option>';
                }
                $("#dlrm_id").append(html);
            }
            
        });
     }
     function getOutletList(rout_id) {
        var _token = $("#_token").val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/')}}/order/getOutletList/"+rout_id,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#site_id").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select Outlet</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].site_code + " - " + data[i].site_name + '</option>';
                }
                $("#site_id").append(html);
            }
        });
     }

     function getItemList(outlet_id) {
        var _token = $("#_token").val();
        var slgp_id = $("#slgp_id").val();
        $('#ajax_load').css("display", "block");
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/')}}/order/getItemList/"+outlet_id+"/"+slgp_id,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#amim_id").empty();
                $('#ajax_load').css("display", "none");
                var html = '<option value="">Select Item</option>';
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i]);
                    html += '<option value="' + data[i].id + '">' + data[i].amim_code + " - " + data[i].amim_name + '</option>';
                }
                $("#amim_id").append(html);
            }
        });
     }
     function getItemFactor(amim_id) {
        var _token = $("#_token").val();
        var slgp_id = $("#slgp_id").val();
        var site_id = $("#site_id").val();
        if(amim_id !='' && slgp_id !='' && site_id !=''){
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/order/getItemFactor/"+slgp_id+"/"+site_id+"/"+amim_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#item_factor").empty();
                    $("#item_tppr").empty();
                    $('#ajax_load').css("display", "none");             
                    $("#item_factor").val(data[0].amim_duft);
                    $("#item_tppr").val(data[0].pldt_tppr);
                }
            });
        }
        else{
            $("#item_factor").val(0);
            $("#item_tppr").val(0);
            $('#ajax_load').css("display", "none"); 
            Swal.fire(
            'Bad!',
            'Please provide all required field!',
            'warning'
            )
            
        }
     }
     function getFormat(site_id){
         $('#formatbtn').empty();
         var slgp_id=$('#slgp_id').val();
        
        // var html='<a href="{{ URL::to("/")}}/order/itemFormat/'+site_id+"/"+slgp_id+'" class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3 col-sm-offset-3">Download Format</a>';
       var html = "<a  href='{{ URL::to('/')}}/order/itemFormat/" +site_id+ "/" +slgp_id+"' class='col-md-3 col-sm-3 col-xs-12 col-md-offset-3 col-sm-offset-3'>Download Format</a>";
         $('#formatbtn').append(html);
     }
    //  function addRow() {
    //         var acmp_id=$('#acmp_id').val();
    //         var slgp_id=$('#slgp_id').val();           
    //         var site_id=$('#site_id').val();
    //         var amim_id = $('#amim_id').val();
    //         var amim_name = $("#amim_id option:selected").text();
    //         var item_qty = $("#item_qty").val();
    //         var item_ctn = $("#item_ctn").val();
    //         var item_factor = $("#item_factor").val();
    //         var item_tppr = $("#item_tppr").val();
    //         var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='item_list[]' value='" + amim_id +
    //             "' hidden>" + amim_name + "</td><td><input type='text' name='item_qty[]' value='" + item_qty + "' hidden>" + item_qty +
    //             "</td><td><input type='text' name='slab_text[]' value='" + slab_text + "' hidden>" + slab_text + "</td><td><input type='text' name='slab_type[]' value='" + promotion_slab_qua + "' hidden>" + promotion_slab_qua + "</td></tr>";
    //         // alert(markup);
    //         $("#myTableSlab").append(markup);


    //         // Find and remove selected table rows
    //         $("#btn_slab_delete").click(function () {
    //             $("table tbody").find('input[name="record"]').each(function () {
    //                 if ($(this).is(":checked")) {
    //                     $(this).parents("tr").remove();
    //                 }
    //             });
    //         });
    //     }
     
    </script>
@endsection