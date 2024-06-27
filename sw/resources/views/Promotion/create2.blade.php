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
                            <strong>New Promotion2</strong>
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

                                <form class="form-horizontal form-label-left" action="{{url('promotion/store/new')}}"
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
                                              <strong>Create Promotion Slab</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-3">
                                                    <span class="step_no">3</span>
                                                    <span class="step_descr">
                                              <strong>Assign Item</strong>
                                          </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#step-4">
                                                    <span class="step_no">4</span>
                                                    <span class="step_descr">
                                              <strong>Assign Party</strong>
                                          </span>
                                                </a>
                                            </li>

                                        </ul>
                                        <div class="x_title">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div id="step-1">
                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Name <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="name" name="promotion_name" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Promotion Name" required="required"
                                                           type="text">
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Code <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="Code" name="promotion_code" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Promotion Code" required="required"
                                                           type="text">
                                                </div>

                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Label
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="promotion_label"
                                                            id="promotion_label"
                                                            required onchange="getPromotionLabel(this.value)">
                                                        <option value="">Select Promotion Label</option>
                                                        <option value="foc">FOC</option>
                                                        <option value="discount">Discount</option>
                                                        <option value="value">Value</option>

                                                    </select>
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Discount
                                                    Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="discount_type" id="discount_type"
                                                            required disabled>

                                                        <option value="amount">Amount</option>
                                                        <option value="percent">Percentage</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name=" "
                                                            id="promotion_type"
                                                            required onchange="getPromotionType(this.value)">

                                                        <option value="default">Select Promotion Type</option>
                                                        <option value="single">Single</option>
                                                        <option value="multiple">Multiple</option>

                                                    </select>
                                                </div>
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Offer
                                                    Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="offer_type" id="offer_type"
                                                            required>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Offer
                                                    Category
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="offer_category" id="offer_category"
                                                            required onchange="getOrderLabelPromotionQualifier(this.value)">

                                                        <option value="">Select Offer Category</option>
                                                        <option value="Line">Line</option>
                                                        <option value="Order">Order</option>
                                                        <option value="Invoice">Invoice</option>

                                                    </select>
                                                </div>
                                                {{--<label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Qualifier on Order
                                                    Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="order_qualifier" id="order_qualifier"
                                                            required disabled>

                                                        <option value="">Select Order Qualifier</option>
                                                        <option value="item">Item Wise</option>
                                                        <option value="invoice">On Full Invoice</option>

                                                    </select>
                                                </div>--}}
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Start
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="startDate" name="startDate"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">End
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="endDate" name="endDate"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ date('Y-m-d')}}" required="required" type="text">
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales
                                                    Group
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="slgp_idds" id="slgp_idds" onchange="getCategory(this.value)"
                                                            required >

                                                        <option value="">Select Sales Group</option>
                                                        @foreach ($salesGroups as $salesGroups)
                                                            <option value="{{ $salesGroups->slgp_id }}">{{ $salesGroups->slgp_code.' - '.$salesGroups->slgp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="step-2" style="height: 500px;">
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                                    Promotion Slab Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="promotion_slab_qua" id="promotion_slab_qua" onchange="getPromotionSlab(this.value)"
                                                            required>

                                                        <option value="">Please Select Slab Type</option>
                                                        <option value="Quantity">Based On Quantity</option>
                                                        <option value="Amount">Based On Amount</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Minimum
                                                    Order Qty <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="min_item_qty" name="min_item_qty"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Minimum Order Quantity "
                                                           required="required"
                                                           type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Offer
                                                    Item Quantity <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="offer_item_qty" name="offer_item_qty"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Offer Item Quantity" required="required"
                                                           type="text">
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Slab
                                                    Text <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="slab_text" name="slab_text"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           placeholder="Enter Slab Text" required="required"
                                                           type="text">
                                                </div>
                                            </div>

                                            <center>
                                                <button type="button" class="btn btn-warning" id="promotion_slab_btn"  onclick="addRow()" disabled>Add
                                                    More
                                                </button>
                                            </center>

                                            <div class="item form-group">
                                                <table id="myTableSlab"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Minimum Item Quantity/Value</th>
                                                        <th>Free Item Quantity/Value</th>
                                                        <th>Slab Text</th>
                                                        <th>Slab Type</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <button type="button" id="btn_slab_delete"
                                                        class="btn btn-danger delete-row">Delete Row
                                                </button>
                                            </div>

                                        </div>

                                        <div id="step-3">
                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Add item :::</span></center>
                                            </strong>
                                            <hr/>
                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Order/Offer:
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="o_type" id="o_type"
                                                            required>

                                                        <option value="order">Order</option>
                                                        <option value="offer">Offer</option>
                                                    </select>
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Item
                                                    Category
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="add_item_category"
                                                            id="add_item_category" onchange="getItem(this.value)"
                                                            required>
                                                        <option value="">Select Category</option>
                                                    </select>
                                                </div>

                                            </div>
                                            <input type="hidden" id="item_category" value=""/>
                                            <input type="hidden" id="item_code" value=""/>
                                            <input type="hidden" id="item_name" value=""/>
                                            <div class="item form-group">


                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Item
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="add_item_item" id="add_item_item"
                                                            required>

                                                        <option value="">Select Item</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item form-group">
                                                <center>
                                                    <button type="button" class="btn btn-warning" 
                                                            onclick="addRowItemAssign()">Add
                                                        Item
                                                    </button>
                                                </center>
                                            </div>
                                            <br/>
                                            <hr/>

                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Order item :::</span>
                                                </center>
                                            </strong>
                                            <br/>
                                            <div class="item form-group">
                                                <table id="myTableOrder"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Item Category</th>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr/>
                                            <strong>
                                                <center><span style="color: #d80229;"> ::: Free item :::</span></center>
                                            </strong>
                                            <br/>

                                            <div class="item form-group">
                                                <table id="myTableFree"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Item Category</th>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <button type="button" id="deleteFreeItem"
                                                        class="btn btn-danger delete-row" disabled>Delete Item
                                                </button>
                                            </div>
                                        </div>
                                        <div id="step-4" style="height: 300px">
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
                                                           onchange="showPromotionArea(this.value);"> Nationally <br/>
                                                    <input type="radio" value="1" id="id_zonal"
                                                           name="pro_type"
                                                           onchange="showPromotionArea(this.value);"> Zonal
                                                </div>
                                            </div>

                                            <div class="item form-group" id="myDiv">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Select
                                                    Zone
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <select class="form-control" name="area_list[]" id="area_item"
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


        function addRow() {
            //alert("adf");
            var rowCoun = $('#myTableSlab >tbody >tr').length;
            //alert(rowCoun);
            var btn = document.getElementById("rowCount");
            /*if (rowCoun == 0) {
                btn.disabled = true;
            }*/
            //var min_item_qty = $("#min_item_qty").val();
            var min_item_qty = $('#min_item_qty').val();
            var offer_item_qty = $("#offer_item_qty").val();
            var slab_text = $("#slab_text").val();
            var promotion_slab_qua = $("#promotion_slab_qua").val();

            var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='min_item_qty[]' value='" + min_item_qty +
                "' hidden>" + min_item_qty + "</td><td><input type='text' name='offer_item_qty[]' value='" + offer_item_qty + "' hidden>" + offer_item_qty +
                "</td><td><input type='text' name='slab_text[]' value='" + slab_text + "' hidden>" + slab_text + "</td><td><input type='text' name='slab_type[]' value='" + promotion_slab_qua + "' hidden>" + promotion_slab_qua + "</td></tr>";
            // alert(markup);
            $("#myTableSlab").append(markup);


            // Find and remove selected table rows
            $("#btn_slab_delete").click(function () {
                $("table tbody").find('input[name="record"]').each(function () {
                    if ($(this).is(":checked")) {
                        $(this).parents("tr").remove();
                    }
                });
            });
        }

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
                "</td><td>" + name + "</td><td><input type='hidden' name='order_type[]' value='" + o_type +"'></td></tr>";
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

            // alert(Channel_ID);
            var p_type = $('#promotion_label').val();

            if (p_type == "foc") {
                $('#discount_type').attr('disabled', 'disabled');
            } else {
                $('#discount_type').removeAttr('disabled');
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
@endsection