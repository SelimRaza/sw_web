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

                                <form class="form-horizontal form-label-left" action="{{url('promotion_sp_2/store/new')}}"
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
                                                    <input id="name" name="promotion_name"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{$promotion->prms_name}}" required="required"
                                                           type="text">
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Code <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="Code" name="promotion_code"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{$promotion->prms_code."_".$promotion->prom_id}}" required="required"
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
                                                        <option value="foc"
                                                                @if($promotion->prmr_qfct == "foc") selected @endif >FOC
                                                        </option>
                                                        <option value="discount"
                                                                @if($promotion->prmr_qfct == "discount") selected @endif >
                                                            Discount
                                                        </option>
                                                        <option value="value"
                                                                @if($promotion->prmr_qfct == "value") selected @endif >
                                                            Value
                                                        </option>

                                                    </select>
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Discount
                                                    Type
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="discount_type" id="discount_type"
                                                            required disabled>

                                                        <option value="amount"
                                                                @if($promotion->prmr_ditp == "amount") selected @endif >
                                                            Amount
                                                        </option>
                                                        <option value="percent"
                                                                @if($promotion->prmr_ditp == "percent") selected @endif >
                                                            Percentage
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Promotion
                                                    Type {{$promotion->prmr_ctgp}}
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="promotion_type"
                                                            id="promotion_type"
                                                            required onchange="getPromotionType(this.value)">

                                                        <option value="default">Select Promotion Type</option>
                                                        <option value="single"
                                                                @if($promotion->prmr_ctgp == "single") selected @endif >
                                                            Single
                                                        </option>
                                                        <option value="multiple"
                                                                @if($promotion->prmr_ctgp == "multiple") selected @endif >
                                                            Multiple
                                                        </option>

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
                                                    <select class="form-control" name="offer_category"
                                                            id="offer_category"
                                                            required
                                                            onchange="getOrderLabelPromotionQualifier(this.value)">

                                                        <option value="">Select Offer Category</option>
                                                        <option value="Line"
                                                                @if($promotion->prmr_qfln == "Line") selected @endif >
                                                            Line
                                                        </option>
                                                        <option value="Order"
                                                                @if($promotion->prmr_qfln == "Order") selected @endif >
                                                            Order
                                                        </option>
                                                        <option value="Invoice"
                                                                @if($promotion->prmr_qfln == "Invoice") selected @endif >
                                                            Invoice
                                                        </option>

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
                                                           value="{{ $promotion->prms_sdat }}" required="required"
                                                           type="text">
                                                </div>

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">End
                                                    Date <span
                                                            class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input id="endDate" name="endDate"
                                                           class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6" data-validate-words="2"
                                                           value="{{ $promotion->prms_edat }}" required="required"
                                                           type="text">
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales
                                                    Group
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="slgp_idds" id="slgp_idds"
                                                            onchange="getCategory(this.value)"
                                                            required>

                                                        <option value="">Select Sales Group</option>
                                                        @foreach ($salesGroups as $salesGroups)
                                                            <option value="{{ $salesGroups->slgp_id }}"
                                                                    @if($promotion->prmr_qfgp == $salesGroups->slgp_id ) selected @endif >{{ $salesGroups->slgp_code.' - '.$salesGroups->slgp_name }}</option>
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
                                                    <select class="form-control" name="promotion_slab_qua"
                                                            id="promotion_slab_qua"
                                                            onchange="getPromotionSlab(this.value)"
                                                            required>
                                                        <?php foreach ($slab_sql as $slab_sqlss) {
                                                            $pro_slab_q = $slab_sqlss->pro_slab_qua;
                                                        } ?>
                                                        <option value="">Please Select Slab Type</option>
                                                        <option value="Quantity"
                                                                @if($pro_slab_q == "Quantity" ) selected @endif>Based On
                                                            Quantity
                                                        </option>
                                                        <option value="Amount"
                                                                @if($pro_slab_q == "Amount" ) selected @endif>Based On
                                                            Amount
                                                        </option>

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
                                                <button type="button" class="btn btn-warning" id="promotion_slab_btn"
                                                        onclick="addRow()" disabled>Add
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
                                                    <tbody id="slab_cont">

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
                                                        class="btn btn-danger delete-row">Delete Item
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

                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                    District
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="district" id="district"
                                                            required>

                                                        <option value="">Select District</option>
                                                        @foreach ($district as $district)
                                                            <option value="{{ $district->id }}">{{ $district->dsct_code.' - '.$district->dsct_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                    Thana
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="thana_id" id="thana_id"
                                                            required>

                                                        <option value="">Select Thana</option>
                                                        @foreach ($thana as $thana)
                                                            <option value="{{ $thana->id }}">{{ $thana->than_code.' - '.$thana->than_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                    Channel
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="channel" id="channel"
                                                            required>

                                                        <option value="">Select Channel</option>
                                                        @foreach ($channel as $channel)
                                                            <option value="{{ $channel->id }}">{{ $channel->chnl_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                    Sub Channel
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="sub_channel" id="sub_channel"
                                                            required>

                                                        <option value="">Select Sub Channel</option>
                                                        @foreach ($subchannel as $subchannel)
                                                            <option value="{{ $subchannel->id }}">{{ $subchannel->scnl_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                            </div>

                                            <div class="item form-group">
                                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Select
                                                    Category
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <select class="form-control" name="category" id="category"
                                                            required>

                                                        <option value="">Select Category</option>
                                                        @foreach ($category as $category)
                                                            <option value="{{ $category->id }}">{{ $category->otcg_name }}</option>
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
        $('#myDiv').show();
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

        var slgp_idds_id = $('#slgp_idds').val();
        var promotion_slab_quass = $('#promotion_slab_qua').val();

        $(document).ready(function () {
            var markup = "";
            var qnty = "0";
            var offer = "0";
            @foreach ($slab_sql as $slab_sql)

                qnty =
                markup += "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='min_item_qty[]' value='@if($slab_sql->slab_min_qnty!=0){{$slab_sql->slab_min_qnty}}@else{{$slab_sql->slab_min_amnt}}@endif' hidden>@if($slab_sql->slab_min_qnty!=0){{$slab_sql->slab_min_qnty}}@else{{$slab_sql->slab_min_amnt}}@endif</td><td><input type='text' name='offer_item_qty[]' value='@if($slab_sql->offer_qnty!=0){{$slab_sql->offer_qnty}}@else{{$slab_sql->offer_qnty}}@endif' hidden> {{$slab_sql->offer_amnt}}</td><td><input type='text' name='slab_text[]' value='{{$slab_sql->prsb_text}}' hidden>{{$slab_sql->prsb_text}}</td><td><input type='text' name='slab_type[]' value='{{$slab_sql->pro_slab_qua}}' hidden>{{$slab_sql->pro_slab_qua}}</td></tr>";
            // alert(markup);
            @endforeach
            $("#slab_cont").append(markup);

            $("#btn_slab_delete").click(function () {

                $("table tbody").find('input[name="record"]').each(function () {
                    if ($(this).is(":checked")) {
                        $(this).parents("tr").remove();
                    }
                });
            });

            getCategory(slgp_idds_id);
            var markup_order = "";

            @foreach ($buy_item as $buy_item)
                markup_order += "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='order_item_id[]' value='{{$buy_item->amim_id}}' hidden>{{$buy_item->issc_name}}</td><td>{{$buy_item->amim_code}}</td><td>{{$buy_item->amim_name}}</td><td><input type='hidden' name='order_type[]' value='order'>Order</td></tr>";
            //alert(markup);
            @endforeach

            $("#myTableOrder").append(markup_order);

            var markup_free = "";
            @foreach ($free_item as $free_item)
                markup_free += "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='order_item_id[]' value='{{$buy_item->amim_id}}' hidden>{{$buy_item->issc_name}}</td><td>{{$buy_item->amim_code}}</td><td>{{$buy_item->amim_name}}</td><td><input type='hidden' name='order_type[]' value='offer'>Offer</td></tr>";
            //alert(markup);
            @endforeach

            $("#myTableFree").append(markup_free);


            $("#deleteFreeItem").click(function () {
                $("table tbody").find('input[name="record"]').each(function () {
                    if ($(this).is(":checked")) {
                        $(this).parents("tr").remove();
                    }
                });
            });
            getPromotionSlab(promotion_slab_quass);
        });


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
            var fields = add_item_item_id.split(':');
            var id = fields[0];
            var category = fields[1];
            var code = fields[2];
            var name = fields[3];

            var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' name='order_item_id[]' value='" + id +
                "' hidden>" + category + "</td><td>" + code +
                "</td><td>" + name + "</td><td><input type='hidden' name='order_type[]' value='" + o_type + "'>" + o_type + "</td></tr>";
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
                        html += '<option value="' + data[i].id + ':' + data[i].issc_name + ':' + data[i].item_code + ':' + data[i].item_name + '">' + data[i].item_code + ' - ' + data[i].item_name + '</option>';
                    }
                    $("#add_item_item").append(html);

                }
            });
        }

        function showPromotionArea(type) {

            $("#area_item").select2('destroy').val("").select2();
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
                //$('#promotion_slab_qua').attr('disabled', 'disabled');
            }
        }

    </script>
@endsection